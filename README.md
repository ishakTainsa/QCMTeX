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

###### !IMPORTANT! Language QCMTeX pour Tesseract
Pour pouvoir utiliser l'ocr pour les grilles de réponses il faut avoir le fichier suivant :  [*](http://www.christopherbleschet.com/ddl/qcmtex.traineddata)  
  
fichier a mettre dans le repertoire suivant : 
    
    C:\Program Files (x86)\Tesseract-OCR\tessdata

### Pour Linux
a venir ..


