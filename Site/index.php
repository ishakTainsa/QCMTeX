<?php
	include 'fichiers/entete.php';
	include('fichiers/fonctions.php');
?>
						<h2>Génération</h2>
						<p>
							Pour générer vos QCMs vous devez préalablement utiliser <a href="">le package TeX, QCMTeX </a> .
							Glisser et déposer vos fichiers TeX dans le cadre ci-dessous pour générer vos QCMs .Consultez la <a href="#documentation">documentation.</a>pour d'avantage d'informations

						</p>
							<div id="cadre">
								<?php
								$test = true;
								if(isset($_FILES['nom'])){
									$test = false;
								    $dossier = 'upload/';
								    $fichier = basename($_FILES['nom']['name']);
								    if(!move_uploaded_file($_FILES['nom']['tmp_name'], $dossier . $fichier))
											echo '<div class="alert alert-danger" role="alert"><strong>Erreur!</strong> un problème est survenue lors du téléchargement de votre fichier</div>';
								    if(isset($_POST['nbQCM'])){	
										if(isTexFile($_FILES['nom']['name']))
											$tableauQR=extractQcm($dossier.$fichier);
										if(isQcm($dossier.$fichier)){
											$nbrQcmVoulu=$_POST['nbQCM'];
											$tabQRM=genererTabQuestionReponses($tableauQR, $nbrQcmVoulu);
											if(isset($_POST['typeQ']))
												$tabRes=genererTexFileResultat($tabQRM,$_POST['typeQ']);
											echo '<a href="ddl/fichier_genere.tex">SUJETS</a>';
										}
										else{
											$test = true;
											echo '<div class="alert alert-danger" role="alert"><strong>Attention!</strong> une erreur est survenue avec votre fichier.Vérifier que l\'extension du fichier est bien <strong>\'.tex\'</strong> et que <strong>l\'environement qcm</strong> est bien présent</div>';
										}		
									}
									else
										$test = true;
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
									    	<label>Layout des QCMs :</label><br>
									    	<div class="radio">
									    		<label>
									    			<input type="radio" name="typeQ" value="liste" checked> Liste
									    		</label>
									    		<label>
													<input type="radio" name="typeQ" value="colonne"> Colonne
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