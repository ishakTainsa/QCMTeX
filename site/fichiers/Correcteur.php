<?php
	
	class Correcteur{

		private $scanCourant;
		private $fichierOcr;
		private $fichierCorrection;
		private $grilleDuQcm;
		private $numeroDuQcm;
		private $fichierPdf;
		private $nombreDeQcms;
		private $nombreDeQuestions;
		private $numQcm;

		public function __construct ($fichierCorrection,$fichierPdf,$repertoireFichiers){
			$this->fichierCorrection = $repertoireFichiers.$fichierCorrection;
			$this->fichierPdf = $repertoireFichiers.$fichierPdf;
			$this->nombreDeQcms = exec('identify -format %n '.$this->fichierPdf.'');
		}

		public function correction($erreur){
			$retour = array();
			exec('convert -density 90x90 '.$this->fichierPdf.' upload/'.'qcm.jpg');
			for ($i=0; $i < $this->nombreDeQcms; $i++) { 
				$fichier = 'upload/qcm-'.$i.'.jpg';
				if($this->scan($fichier,$i)){
					$note = $this->note($erreur);
					$temp = array($fichier,$note,$this->convertNote($note,5),$this->convertNote($note,10),$this->convertNote($note,15),$this->convertNote($note,20),$this->convertNote($note,30),$this->convertNote($note,40));
					$retour[$this->numQcm] = $temp;
				}
				else
					$retour[-($i+1)] = array($fichier,'erreur','erreur','erreur','erreur','erreur','erreur','erreur');
			}
			return $retour;
		}

		public function scan($scan,$i){
			$this->setImage($scan);
			$this->traitementSurImage(false);
			$this->scanOcr();
			$this->fichierOcr = fopen($scan.'.txt', 'r' );
			$retour = false;
			if($this->extractNumQcm() && $this->extractGrille())
				$retour = true;
			else{
				exec('convert -density 150x150 '.$this->fichierPdf.'['.$i.'] '.'qcm-'.$i.'.jpg');
				fclose($this->fichierOcr);				
				$this->traitementSurImage(true);
				$this->scanOcr();
				$this->fichierOcr = fopen($this->image.'.txt', 'r' );
				if($this->numQcm == null)
					$this->extractNumQcm();
				if($this->extractGrille())
					$retour = true;
			}
            fclose($this->fichierOcr);
            unlink($this->image.'.txt');
            return $retour;
        }

        private function extractNumQcm(){
        	$ligne = fgets($this->fichierOcr);
        	$this->numQcm = null;
			if(preg_match('#([0-9]+)#', $ligne,$retour)){
		       		$this->numQcm = $retour[0]-1;
		       		return true;
		    }
		    else
		    	return false;
        }

        public function convertNote($note,$denominateur){
        	$numQuestion = $this->getNbrQuestions();
        	return round($note/$numQuestion*$denominateur,2);
        }

        private function note($erreur){
        	$f = fopen($this->fichierCorrection, 'r');
			$tab = unserialize(fgets($f));
			fclose($f);
			$tab = $tab[$this->numQcm];
			$numQuestion = $this->getNbrQuestions();
			$note = 0;
			if($tab == $this->grilleDuQcm)
				$note = $numQuestion;
			else{
				for ($question=0; $question < $numQuestion; $question++) { 
					if($tab[$question] == $this->grilleDuQcm[$question]) // si les réponses a la question sont juste
						$note++;
					else{
						$test = true;
						for ($reponse=0; $reponse < $this->getNbrReponses($question) ; $reponse++) { 
							if($this->grilleDuQcm[$question][$reponse] == '■')
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
			$this->grilleDuQcm = array();
			do{ // tant qu'on arrive pas au niveau de la grille on passe a la ligne suivante
			  	$ligne = fgets($this->fichierOcr);
			}while(!preg_match("#(■|□)+#",$ligne));
			for ($i=0; $i < $this->getNbrQuestions(); $i++) {
				if(preg_match_all('#■|□#', $ligne, $retour) == $this->getNbrReponses($i)){//test si il y a autant de carré que dans la correction pour savoir si l'ocr a fait une erreur/ ou autre
					$this->grilleDuQcm[] = $retour[0];
				}
				else if(trim($ligne) == '')
			    	return true;          	
				else
					return false;
				$ligne = fgets($this->fichierOcr);
			}
			return true;
		} 

		public function getNbrQcm(){
			$f = fopen($this->fichierCorrection, 'r');
			$tab = unserialize(fgets($f));
			$retour = count($tab);
			fclose($f);
			return $retour;
		}

		public function getNbrQuestions(){
			$f = fopen($this->fichierCorrection, 'r');
			$tab = unserialize(fgets($f));
			$retour = count($tab[0]);
			fclose($f);
			return $retour;
		}

		private function getNbrReponses($question){
			$f = fopen($this->fichierCorrection, 'r');
			$tab = unserialize(fgets($f));
			$retour = count($tab[$this->numQcm][$question]);
			fclose($f);
			return $retour;
		}

		private function traitementSurImage($test){
				exec('convert '.$this->image.' -threshold  70% '.$this->image);
			if($test){
				exec('convert '.$this->image.' -morphology erode square:2 '.$this->image);
				exec('convert '.$this->image.' -morphology dilate square:2 '.$this->image);
			}
		}

		private function scanOcr(){ // utilisation de l'ocr
			exec('tesseract '.$this->image.' -l qcmtex -psm 6 '.$this->image);
		}

		public function setImage($img){
			$this->image = $img;
		}

	}
?>