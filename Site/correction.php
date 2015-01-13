<?php
	include 'fichiers/entete.php';
?>
						<h2>Correction</h2>
						<p>
							Pour corriger vos QCMs vous devez préalablement installer <a href="">l'OCR Tesseract et plus</a> .
							Glisser et déposer vos scans des réponses au QCMs dans le cadre ci-dessous pour corriger vos QCMs .Consultez la <a href="#documentation">documentation.</a>pour d'avantage d'informations.
						</p>
							<div id="cadre">
								<?php
									if(isset($FILES_['pdf']) && isset($FILES_['corr'])){

									}
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
									    	<input type="number" class="form-control" id="points" step="0.5" value ="1" name="points">
									</div>
									<div class="form-group">
									    <label for="note">Noter sur </label>
										    <select class="form-control" id ="note" name="note" value = "default">
										    	<option>default</option>
										    	<option>5</option>
												<option>10</option>
												<option>20</option>
												<option>40</option>
											</select>
										<p class="help-block">Par default la note est donnée sur le nombre de questions</p>
									</div>
										<input type="submit" class="btn-perso btn btn-primary" value="Générer">
									</div>
								</form>
							</div>
<?php
	include 'fichiers/pied.php';
?>
