<?php
session_start();
	$title = 'QCMTeX  - Génération';
	include('fichiers/fonctions.php');
	include 'fichiers/entete.php';
?>
						<h2>Génération</h2>
						<p>
							Pour générer vos QCMs vous devez préalablement utiliser <a href="">le package TeX, QCMTeX </a> .
							Glisser et déposer vos fichiers TeX dans le cadre ci-dessous pour générer vos QCMs .Consultez la <a href="#documentation">documentation.</a>pour d'avantage d'informations

						</p>
							<div id="cadre">
								<?php
								$test = true;
								if(isset($_FILES['nom']) && $_POST['nbQ'] > 0 && $_POST['nbQCM'] > 0){
									$test = false;
								    $dossier = 'upload/';
								    $fichier = basename($_FILES['nom']['name']);
								    if(!move_uploaded_file($_FILES['nom']['tmp_name'], $dossier . $fichier)){
										echo '<div class="alert alert-danger" role="alert"><strong>Erreur!</strong> un problème est survenue lors du téléchargement de votre fichier</div>';
								    	$test = true;
								    }
								    else{
										if(isTexFile($dossier.$fichier)){
											$tableauQR = extractQcm($dossier.$fichier);
											if(isQcm($dossier.$fichier)){
												$nbrQcmVoulu = $_POST['nbQCM'];
												$tabQRM = genererTabQuestionReponses($tableauQR,$nbrQcmVoulu);
												$tab = nbQPQCM($tabQRM,$_POST["nbQ"]);
												genererCorrectionMain(genererCorrectionAutomatique($tab));
												genererTexFileGrilleDeReponse($tab,$_POST['typeR']);
												genererTexFileResultat($tab,$_POST['typeQ'],$_POST['typeR'],$dossier.$fichier);
												echo '<p><a href="ddl/fichier_genere.tex">Sujets en LaTeX</a></p>';
												echo '<p><a href="ddl/grilleRep.tex">Grilles de réponse en LaTeX</a></p>';
												echo '<p><a href="fichiers/genereFichierCorr.php">Fichier de correction automatique</a></p>';
												echo '<p><a href="ddl/fichier_corr_hand.doc">Fichier de correction manuelle</a></p>';
											}
											else{
												$test = true;
												echo '<div class="alert alert-danger" role="alert"><strong>Attention!</strong> une erreur est survenue avec votre fichier.Vérifier que  et que <strong>l\'environement qcm</strong> est bien présent</div>';
											}
										}
										else{
											$test = true;
											echo '<div class="alert alert-danger" role="alert"><strong>Attention!</strong> une erreur est survenue avec votre fichier.Vérifier que l\'extension du fichier est bien <strong>\'.tex\'</strong></div>';
										}
									}
								}
								if($test){
									?>
									<form method="post" class="form-horizontal" action="#" enctype="multipart/form-data">
										<div class="form-group">
											<label>Fichier Tex</label>
											<input type="file" name="nom" />
										</div>
										<div class="form-group">
									        <label>Nombre de QCMs à générer</label>
									        <input type="number" value="1" class="form-control" name="nbQCM"></p>
									    </div>
									    <div class="form-group">
									        <label>Nombre de questions par QCM</label>
									        <input type="number" value="1" class="form-control" name="nbQ"></p>
									    </div>
									    <div class="form-group">
									    	<label>Alignement des réponses </label><br>
									    	<div class="radio">
									    		<label>
									    			<input type="radio" name="typeQ" value="liste" checked> Vertical
									    		</label>
									    		<label>
													<input type="radio" name="typeQ" value="colonne"> Horizontal
												</label>
											</div>
										</div>
										<div class="form-group">
									    	<label>Numérotation des réponses </label><br>
									    	<div class="radio">
									    		<label>
									    			<input type="radio" name="typeR" value="lettre" checked> Par lettre
									    		</label>
									    		<label>
													<input type="radio" name="typeR" value="chiffre"> Par nombre
												</label>
											</div>
										</div>
										<input type="submit" class="btn-perso btn btn-primary" value="Générer"/>
									</form>
									<?php 
								}
								?>
							</div>
<?php
	include 'fichiers/pied.php';
?>