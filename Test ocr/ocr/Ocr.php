<?php
require_once 'ocr/TesseractOCR.php';
class Ocr extends TesseractOCR{

	private $repertoireFichier;
	private $puissance;

	public function scanToFile($scan){
		$this->resize($scan);
		parent::setImage($scan);
		parent::setPagesegMode("3"); // mode d'analyse
		//parent::setWhitelist("A"); // whiteList;
		parent::setLanguage("eng");
		parent::recognizeTxt($this->repertoireFichier);
		return $this->repertoireFichier.$scan;
	}

	private function resize($scan){
		if($this->puissance == 0 or $this->puissance == 1 or $this->puissance == 2){
			$puissanceC = 0;
			if($this->puissance == 0)
				$puissanceC = 2000;
			else if($this->puissance == 1)
				$puissanceC = 3000;
			else if($this->puissance == 2)
				$puissanceC = 5000;
			exec("convert $scan -resize $puissanceC $scan");// augmentation de la résolution
		}
	}

	public function setRepertoireFichier($rep){
		$this->repertoireFichier = $rep;
	}
	public function setPuissance($puissance){
		$this->puissance = $puissance;
	}
}
?>