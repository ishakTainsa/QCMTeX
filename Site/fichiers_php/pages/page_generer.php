						<h2>Génération</h2>
						<p>
							Pour générer vos QCMs vous devez préalablement utiliser <a href="">le package TeX, QCMTeX </a> .
							Glisser et déposer vos fichiers TeX dans le cadre ci-dessous pour générer vos QCMs .Consultez la <a href="#documentation">documentation.</a>pour d'avantage d'informations

						</p>
						<!--
						<form action="yolo/traitement.php" class="dropzone" id="my-awesome-dropzone">
						</form>
      					-->
						<div id="dragndrop">
							<form action ="generateurQCM.php" method="post" role="form">
								<div class="form-group">
									<label>Nombre de QCM à générer</label>
									<input type="number" value="1" class="form-control" id="exampleInputNumber1">
								</div>
								<div class="form-group">
									<label>Nombre de questions par Qcm</label>
										<input type="number" value="1" class="form-control" id="exampleInputNumber1">
								</div>
								<label class="checkbox-inline">
									<input type="checkbox" value="">Option 1
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
									<input type="submit" class="btn-perso btn btn-primary" value="Générer">
								</div>
							</form>
						</div>
