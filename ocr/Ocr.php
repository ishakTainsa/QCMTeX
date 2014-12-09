<?php
require_once 'TesseractOCR.php';
class Ocr extends TesseractOCR{

	public function analyseQcm($scan){
		parent::setImage($scan);
		//parent::setWhiteList("A");
		//parent::setLanguage("fre");     OPTIONS A VENIR POUR UNE MEILLEURE RECONNAISANCE
		//parent::setPagesegMode("0");
		$retour = parent::recognize();
		if(trim($retour) == "")
			$retour = "Aucun texte reconnu";
		return $retour;
	}
}
?>