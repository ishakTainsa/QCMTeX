<?php
	$titre='QCMTex - Chargement de fichier';
	$style='';
	require('entete.php');
	require('fonctions.php');
?>

	<h1> QCMTex : éditeur de QCM </h1>
	
											<!--FORMULAIRE DEMANDANT A L'UTILISATEUR SON FICHIER .TEX-->
	<form action="traitement.php" method="post" enctype="multipart/form-data">
		<!-- Taille de fichier 100ko maximum -->
		<input type="hidden" name="MAX_FILE_SIZE" value="100000"/> 
		<p> Charger un document de format .tex (document_utilisateur.tex dans le dossier) : <input type="file" name="qcm"/> </p>
		<p> <input type="submit" value="Envoyer le fichier"/> </p>
	</form>
															<!--FIN FORMULAIRE-->

<?php
	require("fin.html");
?>