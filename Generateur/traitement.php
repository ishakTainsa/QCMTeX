<?php
	$titre='QCMTex - Traitement de fichier';
	$style='';
	require('entete.php');
	require('fonctions.php');

													    //TEST DU FICHIER ENVOYÉ PAR l'UTILISATEUR
	if (isset($_FILES['qcm']) && isset($_FILES['qcm']['name']) && trim($_FILES['qcm']['name'])!='')
	{
		$fichier_utilisateur = basename($_FILES['qcm']['name']);
		$fichier_genere = "generation.tex";
		$fichier_reponse = "fichier_reponse.tex";
		$taille_max = 100000;
		
		setcookie('fichier_genere', $fichier_genere, time()+3600*24, NULL, NULL, FALSE, TRUE);
		setcookie('fichier_reponse', $fichier_reponse, time()+3600*24, NULL, NULL, FALSE, TRUE);
		
		if (!preg_match('#^.*\.(tex)$#', (string)$_FILES['qcm']['name']))
			$erreur = 'Vous devez uploader un fichier de type .tex !';
		
		if (filesize($_FILES['qcm']['tmp_name']) > $taille_max)
			$erreur = 'Le fichier est trop gros !';
		
		if (!isset($erreur))
		{
			if (move_uploaded_file($_FILES['qcm']['tmp_name'], $fichier_utilisateur)) //Création d'un fichier temporaire
			{		
				if (lectureQCM($fichier_utilisateur))
				{
?>														<!--FORMULAIRE POUR LES OPTIONS DU QCM-->
					<form action ="generateurQCM.php" method="post" >
						<p> Nombre de QCM à générer : <input type="text" name="nbQCM"/> </p>
						<p> Nombre de questions par QCM : 
							<select name="nbQuestion">
							<?php
								for($i=1; $i<=count(unserialize($_COOKIE['question'])); $i++)
									echo '<option value ="'.$i.'">'.$i.'</option>';
							?>
							</select>
						</p>
						<p> Mode aléatoire : 
							<input type="radio" name="aleatoire" value="oui" >Oui</input>
							<input type="radio" name="aleatoire" value="non" >Non</input>
						</p>
						<p> <input type="submit" value="Soumettre"/> </p>
					</form>
															<!--FIN FORMULAIRE OPTIONS QCM-->
<?php
				}
			}
			else
				echo 'Échec lors du chargement du QCM.';			
		}		
		else //Le fichier de l'utilisateur ne marche pas
			echo $erreur;
	}
													//FIN TEST DU FICHIER ENVOYÉ PAR l'UTILISATEUR
	
	require("fin.html");
?>