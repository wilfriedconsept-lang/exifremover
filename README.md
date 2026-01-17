# ExifRemover - Application Web de Nettoyage d'Images

Une application web moderne en PHP pour supprimer les métadonnées des images par lot.

## Fonctionnalités

### 🎯 Upload par Lot
- Uploadez jusqu'à 50 images simultanément
- Formats supportés : JPG, PNG, WEBP
- Interface drag & drop intuitive
- Prévisualisation des images avant traitement

### 🤖 Deux Modes de Nettoyage

#### Mode Nettoyage AI
Supprime uniquement les métadonnées liées à l'intelligence artificielle :
- Tags C2PA (Content Credentials)
- Paramètres de génération AI
- Signatures des outils AI
- **Préserve** : EXIF caméra, GPS, copyright, timestamps

#### Mode Nettoyage Complet
Supprime TOUTES les métadonnées :
- EXIF (données caméra)
- GPS (localisation)
- Timestamps (dates)
- Réglages caméra
- Copyright et auteur
- Toutes autres métadonnées

### ✨ Interface Moderne
- Design ultra moderne avec gradient animé
- Responsive (mobile, tablette, desktop)
- Galerie avec contour vert pour les images nettoyées
- Téléchargement individuel ou en lot
- Animations fluides

## Installation

### Prérequis
- PHP 7.4 ou supérieur
- Extension GD activée
- Extension EXIF activée (optionnel)
- Serveur web (Apache/Nginx)

### Configuration Apache

1. Clonez le repository :
```bash
git clone https://github.com/wilfriedconsept-lang/exifremover.git
cd exifremover
```

2. Vérifiez les permissions :
```bash
chmod 755 uploads/ cleaned/
```

3. Vérifiez que les extensions PHP sont activées :
```bash
php -m | grep -E "gd|exif"
```

4. Configurez votre virtualhost ou utilisez le serveur PHP intégré :
```bash
php -S localhost:8000
```

5. Accédez à l'application :
```
http://localhost:8000
```

### Configuration Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/exifremover;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

## Utilisation

1. **Sélectionnez un mode** :
   - Nettoyage AI : Préserve les métadonnées caméra
   - Nettoyage Complet : Supprime tout

2. **Uploadez vos images** :
   - Glissez-déposez les fichiers
   - Ou cliquez pour sélectionner

3. **Prévisualisez** :
   - Vérifiez les images sélectionnées
   - Supprimez celles que vous ne voulez pas traiter

4. **Nettoyez** :
   - Cliquez sur "Nettoyer les images"
   - Attendez le traitement

5. **Téléchargez** :
   - Téléchargez individuellement avec le bouton sur chaque image
   - Ou téléchargez tout en un clic

## Structure du Projet

```
exifremover/
├── index.php              # Page principale
├── upload.php             # Traitement de l'upload et nettoyage
├── assets/
│   ├── css/
│   │   └── style.css     # Styles modernes
│   └── js/
│       └── app.js        # Logique frontend
├── uploads/               # Images uploadées (temporaire)
├── cleaned/               # Images nettoyées
├── .htaccess             # Configuration Apache
├── .gitignore            # Fichiers ignorés par Git
└── README.md             # Documentation
```

## Sécurité

- Validation stricte des types de fichiers
- Limite de taille : 10MB par image
- Nettoyage automatique des fichiers après 1 heure
- Protection contre les injections
- Headers de sécurité configurés

## Technologies

- **Backend** : PHP 7.4+
- **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
- **Traitement d'images** : Manipulation binaire (préserve la qualité 100%)
- **Design** : CSS moderne avec animations

## Qualité des Images

L'application utilise une approche de **manipulation binaire** pour supprimer les métadonnées :
- ✅ **Aucun réencodage** de l'image
- ✅ **Qualité préservée à 100%**
- ✅ Seules les métadonnées sont modifiées
- ✅ Les données image restent intactes

### Comment ça fonctionne ?

Au lieu de décoder et réencoder l'image (ce qui cause une perte de qualité), l'application :
1. Lit le fichier au niveau binaire
2. Identifie et supprime les segments/chunks de métadonnées
3. Réécrit le fichier sans toucher aux données image

#### JPEG
- Supprime les segments APP (APP1/EXIF, APP2/ICC, APP13/IPTC, etc.)
- Préserve le segment JFIF (APP0) pour la compatibilité
- Conserve les données image compressées intactes

#### PNG
- Supprime les chunks de métadonnées (tEXt, iTXt, zTXt, eXIf, tIME, etc.)
- Préserve les chunks critiques (IHDR, PLTE, IDAT, IEND)
- Maintient la transparence et les profils colorimétriques

#### WebP
- Supprime les chunks EXIF et XMP
- Préserve les données VP8/VP8L intactes

## Limitations

- Maximum 50 images par lot
- Taille maximale : 10MB par image
- Les images sont automatiquement supprimées après 1 heure
- Le mode "Nettoyage AI" détecte et supprime les métadonnées contenant des mots-clés AI (C2PA, DALL-E, Midjourney, etc.)

## Améliorations Futures

- [ ] Support des fichiers RAW (CR2, NEF, ARW, etc.)
- [ ] Téléchargement en ZIP pour toutes les images nettoyées
- [ ] Comparaison avant/après des métadonnées avec interface visuelle
- [ ] API REST pour intégration avec d'autres services
- [ ] Authentification utilisateur et comptes
- [ ] Historique des traitements
- [ ] Support de formats additionnels (TIFF, BMP, etc.)

## Licence

MIT License - Voir le fichier LICENSE pour plus de détails

## Support

Pour toute question ou problème, ouvrez une issue sur GitHub.

## Auteur

Wilfried Concept Lang

---

Made with ❤️ for privacy
