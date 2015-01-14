<?php
	session_start();
	include 'fichiers/entete.php';
	require_once 'fichiers/Correcteur.php';
?>
						<h2>Correction</h2>
						<p>
							Pour corriger vos QCMs vous devez préalablement installer <a href="">l'OCR Tesseract et plus</a> .
							Glisser et déposer vos scans des réponses au QCMs dans le cadre ci-dessous pour corriger vos QCMs .Consultez la <a href="#documentation">documentation.</a>pour d'avantage d'informations.
						</p>
							<div id="cadre">
								<?php
									$test = true;
									if(isset($_FILES['pdf']) && isset($_FILES['corr']) && isset($_POST['points'])){
										$test = false;
										$dossier = 'upload/';
									    $fichier = basename($_FILES['pdf']['name']);
									    $fichier2 = basename($_FILES['corr']['name']);
									    if(!move_uploaded_file($_FILES['pdf']['tmp_name'], $dossier . $fichier)|| !move_uploaded_file($_FILES['corr']['tmp_name'], $dossier . $fichier2))
												echo '<div class="alert alert-danger" role="alert"><strong>Erreur!</strong> un problème est survenue lors du téléchargement de votre fichier</div>';
										$correcteur = new Correcteur($_FILES['corr']['name'],$_FILES['pdf']['name']);
					                    $exel = $correcteur->correction($_POST['points']);
					                    $denom = $correcteur->getNumQuestion();
					                    if(!empty($exel)){
						                    $_SESSION['notes'] = $exel;
						                    $_SESSION['denom'] = $denom;
						                    echo '<p><a href="fichiers/note.php">Télécharger le tableau exel</a></p>';
						                    echo'<table class="table table-bordered">
						                    	<tr><th>N°QCM</th><th>Note sur '.$denom.'(default)</th><th>Note sur 5</th><th>Note sur 10</th><th>Note sur 15</th><th>Note sur 20</th><th>Note sur 30</th><th>Note sur 40</th></tr>';
						                    foreach ($exel as $num => $notes) {
						                    	echo '<tr><th>'.($num+1).'</th>';
						                    	foreach ($notes as $note) {
						                    		echo '<td>'.$note.'</td>';
						                    	}
						                    	echo '</tr>';
						                    }
						                    echo '</table>';
					                	}
					                	else{
											echo '<div class="alert alert-danger" role="alert"><strong>Erreur!</strong> une erreur est survenue lors de la correction.Vérifier que l\'ocr est bien installer et configurer</div>';
											$test = true;
										}
									}
									if($test){
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



  ...
</table>