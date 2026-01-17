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
- **Traitement d'images** : GD Library
- **Design** : CSS moderne avec animations

## Limitations

- Maximum 50 images par lot
- Taille maximale : 10MB par image
- Les images sont automatiquement supprimées après 1 heure
- Le mode "Nettoyage AI" utilise actuellement le même algorithme que le mode complet (nécessite exiftool pour une implémentation complète)

## Améliorations Futures

- [ ] Intégration d'exiftool pour un nettoyage AI précis
- [ ] Support des fichiers RAW
- [ ] Téléchargement en ZIP
- [ ] Comparaison avant/après des métadonnées
- [ ] API REST
- [ ] Authentification utilisateur
- [ ] Historique des traitements

## Licence

MIT License - Voir le fichier LICENSE pour plus de détails

## Support

Pour toute question ou problème, ouvrez une issue sur GitHub.

## Auteur

Wilfried Concept Lang

---

Made with ❤️ for privacy
