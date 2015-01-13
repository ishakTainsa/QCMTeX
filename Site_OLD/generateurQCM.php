<?php
	require('fichiers_php/fonctions_generateur.php');

	$fichier = $_COOKIE['fichier_utilisateur'];
	$questions = extractQcm($fichier);
	if (isset($_POST['nbQCM']) && isset($_POST['nbQuestion']))
	{
		if (isset($_POST['aleatoire'])) {

			$questionMelange = melange($questions, 1);
			$questionGenere = genererTabQuestionReponses($questionMelange, $_POST['nbQCM']);
			$fichierFinal = genererTexFileResultat($_COOKIE['fichier_genere'],$questionGenere);
			echo "Fichier traité : <a href='".$_COOKIE['fichier_genere']."'>Télécharger</a>";
		}
		else {
			$questionGenere = genererTabQuestionReponses($questions, $_POST['nbQCM']);
			$fichierFinal = genererTexFileResultat($_COOKIE['fichier_genere'],$questionGenere);
			echo "Fichier traité : <a href='".$_COOKIE['fichier_genere']."'>Télécharger</a>";
		}
	}
?>