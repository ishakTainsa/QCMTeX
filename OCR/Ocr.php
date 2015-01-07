<?php
	
	class Ocr{

		private $repertoire;
		private $image;
		private $scanMode;
		private $langue;

		public function scan($scan){
			$this->setRepertoire("");
			$this->setImage($scan);
			$this->setScanMode("6");
			$this->setLangue("qcmtex");
			$this->traitementSurImage(); // options d'amelioration
			$this->scanOcr();
			$f = fopen($this->repertoire.$this->image.'.txt', 'r' );
			while($ligne = fgets($f)){
                        echo $ligne . '<br>';     
            }
            fclose($f);
            unlink($this->repertoire.$this->image.'.txt');
		}

		private function traitementSurImage(){
			exec('convert '.$this->image.' -threshold  70% Imagetraitee_'.$this->image);
		}

		private function scanOcr(){
			exec('tesseract Imagetraitee_'.$this->image.' -l '.$this->langue.' -psm '.$this->scanMode.' '.$this->image);
		}

		public function setRepertoire($rep){
			$this->repertoire = $rep;
		}

		public function setLangue($langue){
			$this->langue = $langue;
		}

		public function setScanMode($mode){
			$this->scanMode = $mode;
		}

		public function setImage($img){
			$this->image = $img;
		}

	}
?>