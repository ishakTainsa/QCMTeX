<?php
session_start();
header('Content-Type : text/csv');
header('Content-Disposition : attachement; filename="Notes.csv"');
$notes = $_SESSION['notes'];
$denom = $_SESSION['denom'];
echo '"N° du QCM";"Note sur '.$denom.'";"Note sur 5";"Note sur 10";"Note sur 15";"Note sur 20";"Note sur 30";"Note sur 40"';
foreach ($notes as $key => $value) {
	echo "\n".'"'.($key+1).'"';
	foreach ($value as $value2) {
		echo ';"'.$value2.'"';
	}
}
?>