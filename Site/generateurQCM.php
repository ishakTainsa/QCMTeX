<?php
	require('fichiers_php/fonctions_generateur.php');


	$fichier = $_COOKIE['fichier_utilisateur'];
	$questions = extractQcm($fichier);
	if (isset($_POST['nbQCM']) && isset($_POST['nbQuestion']))
	{
		if (isset($_POST['aleatoire'])) {

			$questionMelange = melange($questions, 4);
			$questionGenere = genererTabQuestionReponses($questionMelange, $_POST['nbQCM']);
			$fichier = genererTexFileResultat($questionGenere);
			echo "Fichier traité : <a href='".$fichier."'>Télécharger</a>";
		}
		else {
			$questionGenere = genererTabQuestionReponses($questions, $_POST['nbQCM']);
			$fichier_Final = genererTexFileResultat($questionGenere);
			echo "Fichier traité : <a href='".$fichier."'>Télécharger</a>";
		}
	}
//	print_r($_COOKIE);
?>