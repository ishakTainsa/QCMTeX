<html>
    <head>
	   <meta charset="utf-8"/>
	   <title>Yolo</title>
    </head>
    <body>
        <p>
            <?php
                if(isset($_FILES['img'])){
                    require_once 'Ocr.php';
                    $ocr = new Ocr();
                    $ocr->scan($_FILES['img']['name']);
                }
                else{
                    ?>
                        <h1>Ocr QCMTeX</h1>
                        le fichier doit etre dans le repertoire de l'ocr pasque je suis une feignasse qui a pas envie de l'upload.<br><br>
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