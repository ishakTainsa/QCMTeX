<html>
    <head>
	   <meta charset="utf-8"/>
	   <title>Yolo</title>
    </head>
    <body>
        <p>
            <?php
                if(isset($_FILES['img'])){
                    require_once 'ocr/Ocr.php';
                    $ocr = new Ocr();
                    $ocr->setRepertoireFichier("txt/");
                    $ocr->setPuissance("2");
                    $adrFichier = $ocr->scanToFile($_FILES['img']['name']);
                    $monfichier = fopen(''.$adrFichier.'.txt', 'r');
                    while($ligne = fgets($monfichier)){
                        echo $ligne.'<br>';
                    }
                    fclose($monfichier);
                }
                else{
                    ?>
                        <h1>Ocr QCMTeX</h1>
                        le fichier doit etre dans le repertoire de l'ocr.
                        <form enctype="multipart/form-data" action="index.php" method="post">
                            Image : <input name="img" type="file"><br><br>
                                    <input type="submit" value="Analyser">
                        </form>
                    <?php
                }
            ?>
        </p>
    </body>
</html>