<?php
session_start();
header('Content-Type : text');
header('Content-Disposition : attachement; filename="fichierCorr.corr"');
$fichier = $_SESSION['corrige'];
echo $fichier;
?>