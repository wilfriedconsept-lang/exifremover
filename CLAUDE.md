# CLAUDE.md - AI Assistant Guide for ExifRemover

## Project Overview

**ExifRemover** is a tool designed to remove EXIF (Exchangeable Image File Format) metadata from image files. EXIF data can contain sensitive information such as GPS coordinates, camera details, timestamps, and other metadata that users may want to strip before sharing images publicly.

### Purpose
- Remove EXIF metadata from images (JPEG, PNG, TIFF, etc.)
- Provide both CLI and library interfaces
- Preserve image quality while removing metadata
- Support batch processing of multiple files
- Optionally preview metadata before removal

## Current Repository State

**Status**: New repository - codebase to be implemented

This repository is currently in its initial state. When implementing features, follow the structure and conventions outlined in this document.

## Recommended Project Structure

```
exifremover/
├── src/                    # Source code
│   ├── cli/               # Command-line interface
│   │   ├── main.py/ts/go  # CLI entry point
│   │   └── args.py/ts/go  # Argument parsing
│   ├── core/              # Core functionality
│   │   ├── remover.py/ts/go    # Main EXIF removal logic
│   │   ├── reader.py/ts/go     # EXIF metadata reading
│   │   └── utils.py/ts/go      # Utility functions
│   └── lib/               # Library interface (if applicable)
├── tests/                 # Test files
│   ├── unit/             # Unit tests
│   ├── integration/      # Integration tests
│   └── fixtures/         # Test images with/without EXIF
├── docs/                 # Documentation
│   ├── API.md           # API documentation
│   ├── USAGE.md         # Usage examples
│   └── CONTRIBUTING.md  # Contribution guidelines
├── examples/            # Example scripts and usage
├── .github/             # GitHub workflows and templates
│   └── workflows/       # CI/CD pipelines
├── CLAUDE.md           # This file - AI assistant guide
├── README.md           # Project overview and quick start
├── LICENSE             # License file
└── setup.py/           # Package configuration
    package.json/
    go.mod/
    Cargo.toml
```

## Technology Stack Considerations

When implementing this project, consider these language options:

### Python
- **Pros**: Rich ecosystem (Pillow, piexif, ExifRead), easy CLI with argparse/click
- **Libraries**: Pillow, piexif, ExifRead
- **Package**: setuptools, poetry, or hatch

### Go
- **Pros**: Fast, single binary distribution, good concurrency for batch processing
- **Libraries**: github.com/rwcarlsen/goexif, github.com/dsoprea/go-exif
- **Build**: Standard go modules

### TypeScript/Node.js
- **Pros**: Cross-platform, npm ecosystem
- **Libraries**: exif-parser, piexifjs, sharp
- **Package**: npm/yarn with package.json

### Rust
- **Pros**: Performance, memory safety, single binary
- **Libraries**: kamadak-exif, image crate
- **Build**: Cargo

**Recommendation**: Choose based on target users and deployment needs. Python for ease of use, Go/Rust for performance and distribution.

## Core Functionality Requirements

### Essential Features
1. **EXIF Removal**: Strip all EXIF metadata from supported image formats
2. **Metadata Reading**: Display EXIF data before removal (optional preview)
3. **Format Support**: JPEG (primary), PNG, TIFF, WebP
4. **Batch Processing**: Process multiple files/directories
5. **Preservation Options**: Optionally keep specific metadata fields
6. **Output Management**: In-place editing or save to new location

### CLI Interface
```bash
# Basic usage
exifremover image.jpg

# Batch processing
exifremover *.jpg
exifremover -r ./photos/

# Preview metadata
exifremover --show image.jpg

# Keep orientation data
exifremover --keep-orientation image.jpg

# Output to different location
exifremover image.jpg -o cleaned/
```

## Development Workflow

### Branch Strategy
- **Main branch**: Stable, production-ready code
- **Development branches**: Feature branches prefixed with `claude/` for AI-assisted development
- Always develop on the designated branch specified in task context

### Commit Guidelines
- Use clear, descriptive commit messages
- Follow conventional commits format:
  - `feat:` - New features
  - `fix:` - Bug fixes
  - `docs:` - Documentation changes
  - `test:` - Test additions/modifications
  - `refactor:` - Code refactoring
  - `perf:` - Performance improvements

### Testing Requirements
- Unit tests for all core functions
- Integration tests for CLI interface
- Test fixtures with sample images (with and without EXIF)
- Aim for >80% code coverage
- Test edge cases: corrupted files, non-image files, already clean images

### Code Quality
- Use linters appropriate to the language (pylint/black, golangci-lint, eslint, clippy)
- Type hints/annotations where applicable
- Document all public functions and classes
- Keep functions focused and small (<50 lines typically)

## AI Assistant Conventions

### When Working on This Project

1. **Read Before Writing**
   - Always read existing files before modifying them
   - Understand the current implementation before suggesting changes

2. **Security Considerations**
   - Validate all file inputs (check file types, sizes)
   - Handle malformed/malicious image files gracefully
   - Avoid path traversal vulnerabilities in file operations
   - Don't expose sensitive error details to end users

3. **Error Handling**
   - Provide clear, actionable error messages
   - Distinguish between user errors and system errors
   - Log detailed errors for debugging, show simplified messages to users
   - Always handle file I/O errors gracefully

4. **Performance**
   - Optimize for batch processing of many files
   - Use streaming for large files when possible
   - Consider memory usage for large images
   - Implement progress indicators for long operations

5. **User Experience**
   - Make CLI intuitive with clear help text
   - Provide sensible defaults
   - Show progress for batch operations
   - Confirm destructive operations (unless --force flag used)

6. **Documentation**
   - Update README.md when adding features
   - Add inline comments for complex logic only
   - Keep API documentation in sync with code
   - Include usage examples for new features

### Code Style Principles

- **Simplicity**: Favor simple, readable code over clever solutions
- **Consistency**: Follow established patterns in the codebase
- **Minimalism**: Only add what's necessary for the current task
- **Clarity**: Clear variable names, logical function organization
- **Testability**: Write code that's easy to test

### Common Pitfalls to Avoid

1. **Metadata Loss**: Ensure image quality is preserved when removing EXIF
2. **Format Confusion**: Different handling for JPEG vs PNG metadata
3. **Incomplete Removal**: Some formats store metadata in multiple locations
4. **Orientation Issues**: Removing orientation EXIF may cause rotated images
5. **Dependency Bloat**: Keep dependencies minimal and justified

## File Format Specifics

### JPEG
- EXIF stored in APP1 marker
- May also have IPTC, XMP metadata
- Orientation tag affects display rotation

### PNG
- Metadata in text chunks (tEXt, iTXt, zTXt)
- No standard EXIF location (sometimes in eXIf chunk)

### TIFF
- EXIF data in IFD (Image File Directory) tags
- More complex structure than JPEG

### WebP
- EXIF in EXIF chunk
- May contain XMP, ICCP chunks

## External Dependencies Guidance

### Image Processing Libraries
Choose libraries that:
- Support multiple image formats
- Allow metadata access and removal
- Preserve image quality
- Have active maintenance
- Are well-documented

### CLI Framework (if applicable)
- Use standard library parsers when sufficient
- For complex CLIs, consider: argparse (Python), cobra (Go), commander (Node.js), clap (Rust)

## Testing Strategy

### Unit Tests
- Test metadata reading for each format
- Test removal for each format
- Test preservation of specific fields
- Test error handling for invalid inputs

### Integration Tests
- End-to-end CLI testing
- Batch processing scenarios
- File permission scenarios
- Cross-platform path handling

### Test Data
- Create fixtures with known EXIF data
- Include edge cases: minimal EXIF, maximal EXIF, corrupted EXIF
- Test with real-world sample images

## Deployment and Distribution

### For Python
```bash
pip install exifremover
# or
pipx install exifremover
```

### For Go
```bash
go install github.com/wilfriedconsept-lang/exifremover@latest
```

### For npm
```bash
npm install -g exifremover
```

## Privacy and Security Considerations

- **Purpose**: This tool enhances privacy by removing potentially sensitive metadata
- **Responsibility**: Clearly document what metadata is removed
- **Verification**: Provide way for users to verify metadata is actually removed
- **Backups**: Recommend users backup originals before in-place editing

## Future Enhancement Ideas

When these are requested, consider:
- GUI application (desktop or web)
- Selective metadata preservation
- Metadata viewing/editing (not just removal)
- Support for video file metadata
- Integration with photo management tools
- Cloud storage integration
- Recursive directory processing with filters

## Resources and References

### EXIF Specification
- EXIF 2.32 specification
- TIFF 6.0 specification
- JPEG File Interchange Format

### Related Tools
- ExifTool (Perl) - comprehensive metadata tool
- mat2 (Python) - metadata removal for multiple formats
- ImageMagick - image manipulation with metadata support

## Questions to Ask Users

When implementing features, clarify:
1. **Target platform**: Desktop, server, cloud?
2. **Primary use case**: CLI, library, or both?
3. **Performance priority**: Fast batch processing or feature-rich?
4. **Safety**: In-place editing or always create copies?
5. **Metadata scope**: Only EXIF or also IPTC, XMP, ICC profiles?

## Maintenance Notes

- Keep dependency versions updated for security
- Test with new image format versions
- Monitor for changes in metadata standards
- Consider backwards compatibility for CLI interface
- Document breaking changes clearly

---

## For AI Assistants: Quick Start Checklist

When beginning work on this project:

- [ ] Determine if codebase exists or needs initialization
- [ ] Identify programming language choice (or ask user)
- [ ] Check for existing tests and maintain coverage
- [ ] Read existing code before making changes
- [ ] Follow security best practices for file handling
- [ ] Update README.md with any new features
- [ ] Run tests before committing
- [ ] Use clear commit messages
- [ ] Push to designated claude/ branch

## Summary

This project aims to provide a simple, effective tool for removing EXIF metadata from images. Focus on:
- **Reliability**: Don't corrupt images
- **Completeness**: Remove all requested metadata
- **Usability**: Make it easy to use
- **Performance**: Handle batch operations efficiently
- **Security**: Validate inputs, handle errors gracefully

Keep the implementation focused, well-tested, and documented.
