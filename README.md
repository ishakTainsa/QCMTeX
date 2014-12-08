# QCMTeX Projet S3 - IUT Villetaneuse Paris XIII

  Un générateur de qcm en LaTeX avec correcteur automatique.

## Instalation de l'OCR Tesseract

### Pour Windows
##### ImageMagick
    
Installer [ImageMagick](http://www.imagemagick.org/download/binaries/ImageMagick-6.9.0-0-Q16-x64-dll.exe).
    
Pour tester l'installation, faire dans la cmd

        convert --version
        Version: ImageMagick 6.9.0-0 Q16 ect..

##### Tesseract
Installer [Tesseract](https://tesseract-ocr.googlecode.com/files/tesseract-ocr-setup-3.02.02.exe)
    
Pour tester l'installation, faire dans la cmd(relancé la console si ça marche pas)

        tesseract -v
        tesseract 3.02
### Pour Linux
a venir ..
## Tesseract en PHP
#### Code de base:

    <?php
        require_once 'ocr/TesseractOCR.php';
        $ocr = new TesseractOCR('image.png');
        $txt = $ocr->recognize();
        echo "Texte reconnu : " . $txt;
    ?>
#### Juste certains caractères:

    <?php
        $ocr->setWhiteList("ABCDEFGH...ect"); //uniquement les majuscules seront reconnus
    ?>
#### changer de langue(nécessite téléchargement des langues):

    <?php
        $ocr->setLanguage("fre"); //pour le français
    ?>
#### Modes de lecture:
tapez `tesseract` dans le cmd pour voir les options :


    <?php
        $ocr->setPagesegMode("0");// de 0 jusqu'a 10
    ?>

