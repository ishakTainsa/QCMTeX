						<h2>Génération</h2>
						<p>
							Pour générer vos QCMs vous devez préalablement utiliser <a href="">le package TeX, QCMTeX </a> .
							Glisser et déposer vos fichiers TeX dans le cadre ci-dessous pour générer vos QCMs .Consultez la <a href="#documentation">documentation.</a>pour d'avantage d'informations

						</p>
      					
						<div id="dragndrop">
							<form action ="traitementQCM.php" method="post" enctype="multipart/form-data">
								<div class="form-group">
									<input type="file" name="qcm"/>
									<input type="submit" class="btn-perso btn btn-primary" value="Générer">
								</div>
							</form>
						</div>