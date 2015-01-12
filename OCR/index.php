<html>
    <head>
	   <meta charset="utf-8"/>
	   <title>Yolo</title>
    </head>
    <body>
        <p>
            <?php
                $timestart=microtime(true);
                if(isset($_FILES['img'])){
                    require_once 'Correcteur.php';
                    $correcteur = new Correcteur('fichierRep.corr');
                    $correcteur->scan($_FILES['img']['name']);
                    //echo $correcteur->getNumQcm()."<br>";
                    foreach ($correcteur->getGrille() as $key => $value) {
                        foreach($value as $key2 =>$value2)
                            echo $value2;
                        echo "<br>";
                    }
                    echo '<br>note : '.$correcteur->note();
                    $timeend=microtime(true);
                    $time=$timeend-$timestart;
                    $page_load_time = number_format($time, 3);
                    echo "<br>Debut du script: ".date("H:i:s", $timestart);
                    echo "<br>Fin du script: ".date("H:i:s", $timeend);
                    echo "<br>Script execute en " . $page_load_time . " sec";
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