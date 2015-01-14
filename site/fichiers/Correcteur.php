<?php
	
	class Correcteur{

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
			$this->nombreDeQcm = $this->getNbrQcm();//exec('identify -format %n upload/'.$this->filePdf.'');//bug chelou
		}

		public function correction($erreur){
			$retour = array();
			exec('convert -density 250x250 upload/'.$this->filePdf.' '.'upload/qcm.jpg');
			for ($i=0; $i < $this->nombreDeQcm; $i++) { 
				$fichier = '';
				if($this->nombreDeQcm <= 1)
					$fichier = 'qcm.jpg';
				else
					$fichier = 'qcm-'.$i.'.jpg';
				if($this->scan($fichier)){
					$note = $this->note($erreur);
					$temp = array($note,$this->convertNote($note,5),$this->convertNote($note,10),$this->convertNote($note,15),$this->convertNote($note,20),$this->convertNote($note,30),$this->convertNote($note,40));
					$retour[$this->numQcm+$i] = $temp;
				}
				else
					$retour[$this->numQcm+$i] = array("erreur","erreur","erreur","erreur","erreur","erreur","erreur");
			}
			return $retour;
		}

		public function scan($scan){
			$this->setImage($scan);
			$this->traitementSurImage(false); // metre true si le qcm n'a pas été reconnu a la premiere passe
			$this->scanOcr(); // apres les traitement scan de l'ocr
			$this->fileOcr = fopen("upload/".$scan.'.txt', 'r' );
			$retour = false;
			$this->extractNumQcm();//  a enlever quand le probleme du dessous sera resolu 
			if(/*$this->extractNumQcm() &&*/ $this->extractGrille())
				$retour = true;
			else{
				fclose($this->fileOcr);
				$this->traitementSurImage(true);
				$this->scanOcr();
				$this->fileOcr = fopen("upload/".$this->image.'.txt', 'r' );
				$this->extractNumQcm(); //  a enlever quand le probleme du dessous sera resolu 
				if(/*$this->extractNumQcm() &&*/ $this->extractGrille())
					$retour = true;
			}
            fclose($this->fileOcr);
            unlink("upload/".$this->image.'.txt');
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
		       		$this->numQcm = $retour[0]-1;
		       		return true;
		    }
		    else
		    	return false;
        }

        public function convertNote($note,$denominateur){
        	$numQuestion = $this->getNumQuestion();
        	return round($note/$numQuestion*$denominateur,2);
        }

        public function note($erreur){
        	$f = fopen("upload/".$this->fileCor, 'r');
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
			return $note;
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

		public function getNbrQcm(){
			$f = fopen("upload/".$this->fileCor, 'r');
			$tab = unserialize(fgets($f));
			$retour = count($tab);
			fclose($f);
			return $retour;
		}

		public function getNumQuestion(){
			$f = fopen("upload/".$this->fileCor, 'r');
			$tab = unserialize(fgets($f));
			$retour = count($tab[0]);
			fclose($f);
			return $retour;
		}

		private function getNumReponses($question){
			$f = fopen("upload/".$this->fileCor, 'r');
			$tab = unserialize(fgets($f));
			$retour = count($tab[$this->numQcm][$question]);
			fclose($f);
			return $retour;
		}

		private function traitementSurImage($test){
			if(!$test)
				exec('convert upload/'.$this->image.' -threshold  70% upload/'.$this->image);
			else{// traitement d'image a faire seulement dans le cas ou l'image ne serait pas reconnu au premier abord
				exec('convert upload/'.$this->image.' -morphology smooth square upload/'.$this->image);
				exec('convert upload/'.$this->image.' -morphology erode square:3 upload/'.$this->image);
				exec('convert upload/'.$this->image.' -morphology dilate square:3 upload/'.$this->image);
			}
		}

		private function scanOcr(){ // utilisation de l'ocr
			exec('tesseract upload/'.$this->image.' -l qcmtex -psm 6 upload/'.$this->image);
		}

		public function setImage($img){
			$this->image = $img;
		}

	}
?>