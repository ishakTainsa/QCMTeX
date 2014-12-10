# QCMTeX Projet S3 - Iut Villetaneuse

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
## Utiliser l'ocr en PHP
##### Code de base:
**Important :** il faut avoir le dossier **ocr** disponible sur **Github** et avoir installer l'ocr [**Tesseract**](https://github.com/Tauul/QCMTeX#instalation-de-locr-tesseract)  
voir exemple dans dossier`Test ocr`

    <?php
        require_once 'ocr/Ocr.php';
        $ocr = new Ocr();
        echo $ocr->analyseScan('scan.png');
    ?>
    
##### Modifier la puissance de l'ocr :

**Important:** le temps de chargement augmantera en fonction.  

        <?php
            $ocr->setPuissance(2)// la puissance de l'ocr va de 0 a 2 ne pas metre pour laissez par default
        ?>
        
##### Modifier repertoire de sauvegarde des resultats :

        <?php
            $ocr->setRepertoireFichier("txt/");
        ?>


