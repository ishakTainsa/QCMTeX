<?php
	session_start();
	$title = 'QCMTeX  - Correction';
	require_once 'fichiers/Correcteur.php';
	include 'fichiers/fonctions.php';
	include 'fichiers/entete.php';
?>
						<h2>Correction</h2>
						<p>
							Pour corriger vos QCMs vous devez préalablement configurer votre serveur <a href="">Voir ici</a> .
							Pour pouvoir corriger vos qcms vous devez disposez des scans des qcms remplis dans un fichier pdf et également du fichier de correction ".corr" associé, si toutefois la correction echoue veuillez vous reportez a la correction manuelle qui vous a été fournis lors de la génération.
						</p>
							<div id="cadre">
								<?php
									$test = true; // test pour affichier le formulaire ou effectuer la correction
									if(isset($_FILES['pdf']) && isset($_FILES['corr']) && isset($_POST['points'])){// teste si le formulaire a été rempli
										$test = false;
										$dossier = 'upload/';
									    $fichier = basename($_FILES['pdf']['name']);
									    $fichier2 = basename($_FILES['corr']['name']);
									    if(isPdfFile($_FILES['pdf']['name']) && isCorrFile($_FILES['corr']['name']) && $_POST['points']>=0){ // test si le formulaire a bien été rempli
										    if(!move_uploaded_file($_FILES['pdf']['tmp_name'], $dossier . $fichier)|| !move_uploaded_file($_FILES['corr']['tmp_name'], $dossier . $fichier2)){// test si l'upload des fichier c'est bien effectué
												echo '<div class="alert alert-danger" role="alert"><strong>Erreur!</strong> Une erreur est survenue lors de l\'upload de vos fichiers</div>';
												$test = true;
											}
											else{ // si l'upload des fichiers c'est bien passer
												set_time_limit (60*60*24);// augmente la limite du temps d'execution d'un fichier php au cas ou elle depasserait 30s
												$timeStart=microtime(true);
												$correcteur = new Correcteur($_FILES['corr']['name'],$_FILES['pdf']['name'],$dossier); //instantiation d'un objet Correcteur avec les fichiers nécessaire + le repertoire des fichiers
							                    $exel = $correcteur->correction($_POST['points']); // méthode qui effectue la correction en foncton du nombre de points à retirer en cas d'erreur
							                    $timeEnd=microtime(true); //
							                    $time=$timeEnd-$timeStart; // Pour calculer le temps d'execution du correcteur à titre indicatif
							                    $page_load_time = number_format($time, 3); // 
							                    echo '<div class="alert alert-success" role="alert"><strong>Succès!</strong> Correction execute en ' . $page_load_time . ' sec</div>'; 
							                    $denom = $correcteur->getNbrQuestions(); // utiliser plus bas dans la session
							                    if(!empty($exel)){ // vérifie si le correcteur a bien fonctionner
								                    $_SESSION['notes'] = $exel; // variable à reutiliser pour la génération du fichiers Exel
								                    $_SESSION['denom'] = $denom; // variable à reutiliser pour la génération du fichiers Exel
								                    echo '<p><a href="fichiers/note.php">Télécharger le tableau exel</a></p>';
								                    echo'<table class="table table-bordered">
								                    	<tr><th>N°QCM</th><th>Scan</th><th>Note sur '.$denom.'(default)</th><th>Note sur 5</th><th>Note sur 10</th><th>Note sur 15</th><th>Note sur 20</th><th>Note sur 30</th><th>Note sur 40</th></tr>';
								                    foreach ($exel as $num => $notes) { // affiche le tableau des résultats de la correction
								                    	echo '<tr><th>'.($num+1).'</th>';
								                    	foreach ($notes as $note) {
															if(is_float($note) ||is_int($note))
								                    			echo '<td>'.$note.'</td>';
								                    		else
								                    			echo '<td><a href="'.$note.'" target="_blank">Image</a>';
								                    	}
								                    	echo '</tr>';
								                    }
								                    echo '</table>';
						                		}					                		
							                	else{ // si il y a eu un probleme a l'execution de $correcteur->correction($points)
													echo '<div class="alert alert-danger" role="alert"><strong>Erreur!</strong> une erreur est survenue lors de la correction.Vérifier que l\'ocr est bien installer et configurer</div>';
													$test = true;
												}
						                	}
					                	}else{ // si les champs du formulaire sont conformes aux informations, fichiers demandés
					                		echo '<div class="alert alert-danger" role="alert"><strong>Erreur!</strong> Vérifier que l\'extension des fichiers est respecter et que les points a retirer sont supérieur ou égal à 0</div>';
					                		$test = true;
					                	}

									}
									if($test){ // si le formulaire n'as pas été envoyer ou si on a rencontrer un probleme on affiche le formulaire
								?>
										<form action ="correction.php" class="form-horizontal" method="post" enctype="multipart/form-data">
											<div class="form-group">
												<label for="pdf">Fichier Pdf</label>
											    	<input type="file" id="pdf" name="pdf">
											</div>
											<div class="form-group">
											    <label for="correction">Fichier Correction</label>
											    	<input type="file" id="correction" name="corr">
											</div>
											<div class="form-group">
											    <label for="points">Points a retirer en cas d'erreur</label>
											    	<input type="number" class="form-control" id="points" step="0.25" value ="0" name="points">
											</div>
												<input type="submit" class="btn-perso btn btn-primary" value="Corriger">
											</div>
										</form>
								<?php 
									}
									?>
							</div>

<?php
	include 'fichiers/pied.php';
?>