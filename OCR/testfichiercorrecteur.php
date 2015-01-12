<html>
    <head>
	   <meta charset="utf-8"/>
	   <title>Yolo</title>
    </head>
    <body>
        <p>
        	<?php
				$reponses1 = array('■','□','□','□','■');
				$reponses2 = array('□','■','□','■','□');
				$reponses3 = array('□','□','■','□','□');
				$reponses4 = array('□','□','□','■','□');
				$reponses5 = array('■','□','□','□','■');
				$reponses6 = array('□','■','□','■','□');
				$reponses7 = array('□','□','■','□','□');;
				$reponses8 = array('□','■','□','■','□');
				$reponses9 = array('■','□','□','□','■');
				$reponses10 = array('□','□','□','□','■');
				$reponses11 = array('□','□','■','□','□');
				$questions = array($reponses1,$reponses2,$reponses3,$reponses4,$reponses5,$reponses6,$reponses7,$reponses8,$reponses9,$reponses10,$reponses11);
				$qcm = array($questions);
				$fichierRep = serialize($qcm);
				$f = fopen("fichierRep.corr", "a+");
				fwrite($f, $fichierRep);
				fclose($f);
				$f = fopen("fichierRep.corr", "r");
				$tabRes = unserialize(fgets($f));
				fclose($f);
				print_r($tabRes);
			?>
        </p>
    </body>
</html>