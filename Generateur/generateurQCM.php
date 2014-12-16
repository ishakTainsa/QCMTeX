<?php
	$titre='QCMTex - Génération du fichier';
	$style='';
	require('entete.php');
	require('fonctions.php');

	//Teste si les valeurs entré par l'utilisateur sont correcte (provient de traitement.php
	if (isset($_POST['nbQCM']) && preg_match('#^ *[0-9]+ *$#', $_POST['nbQCM']) && isset($_POST['nbQuestion']) && isset($_POST['aleatoire']))
	{
		if (generationQCM($_COOKIE['fichier_genere'], $_POST['nbQCM'], $_POST['nbQuestion'], $_POST['aleatoire'])) //Génère le fichier
			echo "Fichier traité : <a href='".$_COOKIE['fichier_genere']."'>Télécharger</a>";
	}
	else
		echo "Echec lors de la génération de qcm.";
	
	require("fin.html");
?>