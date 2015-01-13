<?php
	require('fichiers_php/fonctions_generateur.php');

													    //TEST DU FICHIER ENVOYÉ PAR l'UTILISATEUR
	if (isset($_FILES['qcm']) && isset($_FILES['qcm']['name']) && trim($_FILES['qcm']['name'])!='')
	{
		$fichier_utilisateur = basename($_FILES['qcm']['name']);
		$fichier_genere = "fichier_genere.tex";
		
		setcookie('fichier_utilisateur', $fichier_utilisateur, time()+3600*24, NULL, NULL, FALSE, TRUE);
		setcookie('fichier_genere', $fichier_genere, time()+3600*24, NULL, NULL, FALSE, TRUE);

		if(isTexFile($fichier_utilisateur)==false) {
			$erreur = 'Vous devez uploader un fichier de type .tex !';
		}		

		if(isQcm($fichier_utilisateur)==false) {
			$erreur = 'Vous devez uploader un fichier avec un environnement QCM !';
		}

		if (!isset($erreur))
		{
			if (move_uploaded_file($_FILES['qcm']['tmp_name'], $fichier_utilisateur)) //Création d'un fichier temporaire
			{		
				$questions = extractQcm($_FILES['qcm']['name']);
				if (isset($questions)) 
				{
?>														<!--FORMULAIRE POUR LES OPTIONS DU QCM-->
					<div id="dragndrop">
							<form action ="generateurQCM.php" method="post" role="form">
								<div class="form-group">
									<label>Nombre de QCM à générer</label>
									<input type="number" value="1" class="form-control" name="nbQCM">
								</div>
								<div class="form-group">
									<label>Nombre de questions par Qcm</label>
									<select name="nbQuestion">
									<?php
										for($i=1; $i<=count($questions); $i++)
											echo '<option value ="'.$i.'">'.$i.'</option>';
									?>
									</select>
								</div>
								<label class="checkbox-inline">
									<input type="checkbox" name="aleatoire" value="">Mode aléatoire des questions.
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" value="">Option 2
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" value="">Option 3
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" value="">Option 4
								</label>
								<div class="form-group">
									<input type="hidden" name="qcm" value=<?php $questions ?>/>
									<input type="submit" class="btn-perso btn btn-primary" value="Générer">
								</div>
							</form>
						</div>
					
															<!--FIN FORMULAIRE OPTIONS QCM-->
<?php
				}
			}
			else
				echo 'Échec lors du chargement du QCM.';			
		}		
		else {//Le fichier de l'utilisateur ne marche pas
			echo $erreur;

		}
	}
	else {//Le fichier de l'utilisateur ne marche pas
		echo 'rien';
	}
													//FIN TEST DU FICHIER ENVOYÉ PAR l'UTILISATEUR

?>