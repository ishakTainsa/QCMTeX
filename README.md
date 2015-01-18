# QCMTeX Projet S3 - Iut Villetaneuse

  Un générateur de qcm en LaTeX avec correcteur automatique.

## Installation du Correcteur sur le serveur

### Pour Windows
##### ImageMagick
    
Installer [ImageMagick](http://www.imagemagick.org/download/binaries/).  
Télécharger "ImageMagik-X.X.X-X-XXX-x64-ddl.exe"
    
Pour tester l'installation, faire dans la cmd

        convert --version
        Version: ImageMagick X.X.X-X XXX ect..

##### Tesseract
Installer [Tesseract](https://tesseract-ocr.googlecode.com/files/tesseract-ocr-setup-3.02.02.exe)
    
Pour tester l'installation, faire dans la cmd(relancé la console si ça marche pas)

        tesseract -v
        tesseract X.XX

###### !IMPORTANT! Language QCMTeX pour Tesseract
Pour pouvoir utiliser l'ocr pour les grilles de réponses il faut avoir le fichier suivant :  [*](http://www.christopherbleschet.com/ddl/qcmtex.traineddata)  
  
fichier a mettre dans le repertoire suivant : 
    
    C:\Program Files (x86)\Tesseract-OCR\tessdata

### Pour Linux
##### ImageMagick  
Installer ImageMagick :

        sudo apt-get install imagemagick
    
Pour tester l'installation

        convert --version
        Version: ImageMagick X.X.X-X XXX ect..

##### Tesseract
Installer Tesseract :  
        
        sudo apt-get install tesseract-ocr
Pour tester l'installation, faire dans la cmd(relancé la console si ça marche pas)

        tesseract -v
        tesseract X.XX

###### !IMPORTANT! Language QCMTeX pour Tesseract
Pour pouvoir utiliser l'ocr pour les grilles de réponses il faut avoir le fichier suivant :  [*](http://www.christopherbleschet.com/ddl/qcmtex.traineddata)  
  
fichier a mettre dans le repertoire suivant : 
    
    /usr/share/tesseract-ocr/tessdata