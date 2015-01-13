<?php
	
	class Correcteur{

		private $repertoire;
		private $image;
		private $fileOcr;
		private $fileCor;
		private $grille;
		private $numQcm;
		private $filePdf;
		private $nombreDeQcm;

		public function __construct ($fileCor,$filePdf){
			$this->fileCor = $fileCor;
			$this->filePdf = $filePdf;
			$this->nombreDeQcm = exec('identify -format %n '.$this->filePdf);
		}

		public function isPdf($file){
			$fichier = new SplFileInfo($file);
			$extension = pathinfo($fichier->getFilename(), PATHINFO_EXTENSION);
			if($extension != 'pdf')
				return false;
			else
				return true;
		}

		public function correction(){
			exec('convert -density 250x250 '.$this->filePdf.' qcm.jpg');
			for ($i=0; $i < $this->nombreDeQcm; $i++) { 
				$fichier = '';
				if($this->nombreDeQcm <= 1)
					$fichier = 'qcm.jpg';
				else
					$fichier = 'qcm-'.$i.'.jpg';
				if($this->scan($fichier))
					$retour[$this->numQcm+$i] = $this->note(null,1);
				else
					$retour[$this->numQcm+$i] = 'erreur';
			}
			return $retour;
		}

		public function scan($scan){
			$this->setRepertoire('');
			$this->setImage($scan);
			$this->traitementSurImage(false); // metre true si le qcm n'a pas été reconnu a la premiere passe
			$this->scanOcr(); // apres les traitement scan de l'ocr
			$this->fileOcr = fopen($this->repertoire.$this->image.'.txt', 'r' );
			$retour = false;
			$this->extractNumQcm();//  a enlever quand le probleme du dessous sera resolu 
			if(/*$this->extractNumQcm() &&*/ $this->extractGrille())
				$retour = true;
			else{
				fclose($this->fileOcr);
				$this->traitementSurImage(true);
				$this->scanOcr();
				$this->fileOcr = fopen($this->repertoire.$this->image.'.txt', 'r' );
				$this->extractNumQcm(); //  a enlever quand le probleme du dessous sera resolu 
				if(/*$this->extractNumQcm() &&*/$this->extractGrille())
					$retour = true;
			}
            fclose($this->fileOcr);
            unlink($this->repertoire.$this->image.'.txt');
            return $retour;
        }

        public function getNumQcm(){
        	return $this->numQcm;
        }

        public function getGrille(){// juste pour les test a retirer plus tard
        	return $this->grille;
        }

        private function extractNumQcm(){
        	$ligne = fgets($this->fileOcr);
			$this->numQcm = 0;
			if(preg_match('#([0-9]+)#', $ligne,$retour)){
		       		//$this->numQcm = $retour[0]-1;
		       		return true;
		    }
		    else
		    	return false;
        }

        public function note($denominateur,$erreur){
        	$f = fopen($this->fileCor, 'r');
			$tab = unserialize(fgets($f));
			fclose($f);
			$tab = $tab[$this->numQcm];
			$numQuestion = $this->getNumQuestion();
			$note = 0;
			if($tab == $this->grille)
				$note = $numQuestion;
			else{
				for ($question=0; $question < $numQuestion; $question++) { 
					if($tab[$question] == $this->grille[$question]) // si les réponses a la question sont juste
						$note++;
					else{
						$test = true;
						for ($reponse=0; $reponse < $this->getNumReponses($question) ; $reponse++) { 
							if($this->grille[$question][$reponse] == '■')
								$test = false;
						}
						if(!$test)// si les cases ne sont pas laisser vide
							$note-=$erreur;
					}
				}
			}
			if($denominateur == null)
				$denominateur = $numQuestion;
			$note = round($note/$numQuestion*$denominateur,2);
			return $note.'/'.$denominateur;
        }

        private function extractGrille(){
			$this->grille = array();
			for ($i=0 ; $i < 4  ; $i++ )
			  	$ligne = fgets($this->fileOcr);
			for ($i=0; $i < $this->getNumQuestion(); $i++) { 
				$ligne = fgets($this->fileOcr);
				if(preg_match_all('#■|□#', $ligne, $retour) == $this->getNumReponses($i)){//test si il y a autant de carré que dans la correction pour savoir si l'ocr a fait une erreur/ ou autre
					$this->grille[] = $retour[0];
				}
				else if(trim($ligne) == '')
			    	return true;          	
				else
					return false;
			}
			return true;
		} 

		private function getNumQuestion(){
			$f = fopen($this->fileCor, 'r');
			$tab = unserialize(fgets($f));
			$retour = count($tab[$this->numQcm]);
			fclose($f);
			return $retour;
		}

		private function getNumReponses($question){
			$f = fopen($this->fileCor, 'r');
			$tab = unserialize(fgets($f));
			$retour = count($tab[$this->numQcm][$question]);
			fclose($f);
			return $retour;
		}

		private function traitementSurImage($test){
			if(!$test)
				exec('convert '.$this->image.' -threshold  70% '.$this->image);
			else{// traitement d'image a faire seulement dans le cas ou l'image ne serait pas reconnu au premier abord
				exec('convert '.$this->image.' -morphology smooth square '.$this->image);
				exec('convert '.$this->image.' -morphology erode square:3 '.$this->image);
				exec('convert '.$this->image.' -morphology dilate square:3 '.$this->image);
			}
		}

		private function scanOcr(){ // utilisation de l'ocr
			exec('tesseract '.$this->image.' -l qcmtex -psm 6 '.$this->image);
		}

		public function setRepertoire($rep){
			$this->repertoire = $rep;
		}

		public function setImage($img){
			$this->image = $img;
		}

	}
?>