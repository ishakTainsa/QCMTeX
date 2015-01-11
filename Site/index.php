<!doctype html>
<html>
	<head>
		<title>QCMTeX</title>
		<meta charset="utf8">
		<link href='http://fonts.googleapis.com/css?family=Actor' rel='stylesheet' type='text/css'>
		<link href='dist/css/bootstrap.css' rel='stylesheet' type='text/css'>
		<link href='dist/css/style.css' rel='stylesheet' type='text/css'>
		<link href="dist/css/dropzone.css" type="text/css" rel="stylesheet">
		<script src="dist/js/jquery.min.js"></script>
		<script src="dist/js/bootstrap.min.js"></script>
		<script src="dist/js/dropzone.min.js"></script>
		<script type="text/javascript">
			$(function() {
				var myDropzone = new Dropzone("#my-awesome-dropzone");
				myDropzone.on("addedfile", function(file) {
				alert("succes");
			});

			})
		</script>
		<?php
			require('fichiers_php/fonctions_generateur.php');
		?>
	</head>
	<body>
		<header>
			<h1>QCMTeX</h1>
		</header>
		<section>
			<div role="tabpanel">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<li role="presentation" class="active"><a href="#generer"  aria-controls="generer" data-toggle="tab">Générer</a></li>
					<li role="presentation"><a href="#corriger"  aria-controls="corriger" data-toggle="tab">Corriger</a></li>
					<li role="presentation" class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
							Documentation <span class="caret"></span>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li role="presentation"><a href="#documentation"> Liens 1</a></li>
						<li role="presentation"><a href="#documentation"> Liens 2</a></li>
						<li role="presentation"><a href="#documentation"> Liens 3</a></li>
					</ul>
					</li>
				</ul>
				<div class="tab-content">	
					<div role="tabpanel" class="content tab-pane fade in active" id="generer">
						<?php include"fichiers_php/pages/Page_generer.php" ?>
					</div>
					<div role="tabpanel" class="content tab-pane fade" id="corriger">
						<?php include"fichiers_php/pages/page_corriger.php" ?>
					</div>
					<div role="tabpanel" class="content tab-pane fade" id="documentation">
						<?php include"fichiers_php/pages/page_documentation.php" ?>
					</div>
				</div>
			</div>	
		</section>
		<footer>
			<p><span id="pied_page">QCMTeX - Projet de S3 du groupe E17. <a target="_blank" href="https://github.com/Tauul/QCMTeX">Voir GitHub</a></span></p>
		</footer>
		<script type="text/javascript">
			$(document).ready(function(){ 
				$("#myTab a").click(function(e){
					e.preventDefault();
					$(this).tab('show');
				});
			});
		</script>
	</body>
</html>
