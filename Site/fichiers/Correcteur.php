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
		private $repertoireFichiers;

		/* Constructeur du Correcteur
		 *
		 * @param File $fichierCorrection,$fichierPdf,$repertoireFichiers
		 */
		public function __construct ($fichierCorrection,$fichierPdf,$repertoireFichiers){
			$this->fichierCorrection = $repertoireFichiers.$fichierCorrection;
			$this->fichierPdf = $repertoireFichiers.$fichierPdf;
			$this->nombreDeQcms = exec('identify -format %n '.$this->fichierPdf.'');
			$this->repertoireFichiers = $repertoireFichiers;
		}

		/* correction($pointsAEnlever)
		 * @param int $pointsAEnlever  points a enlever en cas d'erreur a une question
		 * On extrait chaque page du pdf en image, on fait une boucle en fonction du nombre de page, si la correction est bien effectuer on stocke les notes sinon affiche qu'il y a eu une erreur
		 * @return Array $retour tableau contenant les notes des qcms 
		 */

		public function correction($pointsAEnlever){
			$retour = array();
			exec('convert -density 90x90 '.$this->fichierPdf.' '.$this->repertoireFichiers.'qcm.jpg');
			for ($i=0; $i < $this->nombreDeQcms; $i++) { 
				$fichier = $this->repertoireFichiers.'qcm-'.$i.'.jpg';
				if($this->scan($fichier,$i)){
					$note = $this->note($pointsAEnlever);
					$temp = array($fichier,$note,$this->convertNote($note,5),$this->convertNote($note,10),$this->convertNote($note,15),$this->convertNote($note,20),$this->convertNote($note,30),$this->convertNote($note,40));
					$retour[$this->numQcm] = $temp;
				}
				else
					$retour[-($i+1)] = array($fichier,'erreur','erreur','erreur','erreur','erreur','erreur','erreur');
			}
			return $retour;
		}

		 /* scan($scan,$i)
		 * 
		 * @param File $scan
		 * @param int $i
		 * Effectue un premier test sur l'image avec un traitement d'image leger, si ça échoue on réextrait l'image dans une résolution supérieure et on éffectue des traitements d'images plus poussé
		 * @return boolean $retour false si l'ocr a échoué a analayser , true sinon
		 */

		public function scan($scan,$i){
			$this->scanCourant = $scan;
			$this->traitementSurImage(false);
			$this->scanOcr(); 
			$this->fichierOcr = fopen($scan.'.txt', 'r' ); // ouverture du fichier donner par l'ocr
			$retour = false;
			if($this->extractNumQcm() && $this->extractGrille()) // test si le numéro du qcm et la grille sont extraite , si oui on sort de la fonction
				$retour = true;
			else{ // si l'extraction échoue
				exec('convert -density 150x150 '.$this->fichierPdf.'['.$i.'] '.$this->repertoireFichiers.'qcm-'.$i.'.jpg'); // on extrait de nouveau l'image dans une résolution supérieure
				fclose($this->fichierOcr);	// on ferme le fichier de l'ocr pour en générer un nouveau			
				$this->traitementSurImage(false);
				$this->scanOcr(); // nouveau scan
				$this->fichierOcr = fopen($this->scanCourant.'.txt', 'r' );
				if($this->numQcm == null) // si le numéro du qcm n'as pas été trouver à la premiere passe on tente de l'extraire de nouveau
					$this->extractNumQcm();  
				if($this->extractGrille() && $this->numQcm != null) // on retente d'extraire la grille si on échoute la fonction retourne false
					$retour = true;
				else{ // si la resolution supérieure ne suffit pas on effectue des traitements d'image plus pousser
					fclose($this->fichierOcr);	// on ferme le fichier de l'ocr pour en générer un nouveau			
					$this->traitementSurImage(true);
					$this->scanOcr(); // nouveau scan
					$this->fichierOcr = fopen($this->scanCourant.'.txt', 'r' );
					if($this->numQcm == null) // si le numéro du qcm n'as pas été trouver à la premiere passe on tente de l'extraire de nouveau
						$this->extractNumQcm();  
					if($this->extractGrille() && $this->numQcm != null) // on retente d'extraire la grille si on échoute la fonction retourne false
						$retour = true;
				}
			}
            fclose($this->fichierOcr);
            unlink($this->scanCourant.'.txt'); // on supprime le fichier txt génére par l'ocr
            return $retour;
        }

        /* convertNote($note,$denominateur)
         * @param float $note  note d'un qcm
         * @param int $denominateur
         * @return float ,La note ramener sur le denominateur voulu
         */
        public function convertNote($note,$denominateur){
        	$numQuestion = $this->getNbrQuestions();
        	return round($note/$numQuestion*$denominateur,2);
        }

        /* note($pointsAEnlever)
         * @param float $pointsAEnlever points a retirer en cas d'erreur de l'éleve
         * compare les réponses de l'éleve au fichier de correction pour noter l'eleve
         * @return float $note note de l'éleve au qcm
         */
        private function note($pointsAEnlever){
        	$f = fopen($this->fichierCorrection, 'r');
			$tab = unserialize(fgets($f));
			fclose($f);
			$tab = $tab[$this->numQcm];
			$numQuestion = $this->getNbrQuestions();
			$note = 0;
			if($tab == $this->grilleDuQcm) // si l'eleve a tout bon
				$note = $numQuestion;
			else{
				for ($question=0; $question < $numQuestion; $question++) { 
					if($tab[$question] == $this->grilleDuQcm[$question]) // si la réponse à la question est juste
						$note++;
					else{
						$test = true;
						for ($reponse=0; $reponse < $this->getNbrReponses($question) ; $reponse++) { 
							if($this->grilleDuQcm[$question][$reponse] == '■') // test si l'éleve a remplit une case de la question
								$test = false;
						}
						if(!$test) // si une case est remplie alors l'éleve c'est tromper
							$note-=$pointsAEnlever;
					}
				}
			}
			return $note;
        }

        /* extractNumQcm()
         * @param  null (utilise le fichier générer par l'ocr accesible via this->fichierOcr)
         * @return boolean  si il arrive a extraire le numéro du qcm true, sinon false
         */
        private function extractNumQcm(){
        	$ligne = fgets($this->fichierOcr);
        	$this->numQcm = null; // pour remetre à null pour ne pas confondre avec le numéro du Qcm précedent si le numéro n'est pas récupérer en desous
			if(preg_match('#([0-9]+)#', $ligne,$retour)){
		       		$this->numQcm = $retour[0]-1;
		       		return true;
		    }
		    else
		    	return false;
        }

        /* extractGrille()
         * extrait la grille de réponse de l'éleve dans le fichier générer par l'ocr
         * @return boolean si on a reussit a extraire la grille du qcm true sinon false
         */

        private function extractGrille(){
			$this->grilleDuQcm = array(); // on met la grille a null pour ne pas récupérer la grille du Qcm précedent

			do{
			  	$ligne = fgets($this->fichierOcr);
			}while(!preg_match("#(■|□)+#",$ligne)); // tant qu'on arrive pas au niveau de la grille on passe a la ligne suivante

			for ($i=0; $i < $this->getNbrQuestions(); $i++) {
				if(preg_match_all('#■|□#', $ligne, $retour) == $this->getNbrReponses($i)){//test si il y a autant de carré que dans la correction pour savoir si l'ocr a fait une erreur
					$this->grilleDuQcm[] = $retour[0];
				}        	
				else
					return false;
				$ligne = fgets($this->fichierOcr);
			}
			return true;
		}  

		/* getNbrQuestions()
		 * ouvre le fichier de correction et compte le nombre de question par qcm
		 * @return int $retour le nombre de questions
		 */
		public function getNbrQuestions(){
			$f = fopen($this->fichierCorrection, 'r');
			$tab = unserialize(fgets($f));
			$retour = count($tab[0]);
			fclose($f);
			return $retour;
		}


		/* getNbrQuestions()
		 * @param int $question le numéro de la question analyser
		 * ouvre le fichier de correction et compte le nombre de "carré" à la question analysé du Qcm analysé
		 * @return int $retour le nombre de questions
		 */
		private function getNbrReponses($numQuestion){
			$f = fopen($this->fichierCorrection, 'r');
			$tab = unserialize(fgets($f)); // déserialise le fichier de correction afin de récupérer l'array contenant la correction 
			$retour = count($tab[$this->numQcm][$numQuestion]); // compte le nombre de "carré" à remplir pour la question du qcm analyser
			fclose($f);
			return $retour;
		}

		/* traitementSurImage($test)
		 * @param boolean $test pour déterminer si on n'éffectue que le premier traitement ou tout les traitements
		 * effectue des traitement sur l'image courante
		 */

		private function traitementSurImage($test){
				exec('convert '.$this->scanCourant.' -threshold  70% '.$this->scanCourant); // filtre l'image pour limiter au couleurs noir et blanc
			if($test){
				exec('convert '.$this->scanCourant.' -morphology erode square:2 '.$this->scanCourant); // les deux traitements combinés remplissent les carrés noirs si il ne sont pas bien remplis
				exec('convert '.$this->scanCourant.' -morphology dilate square:2 '.$this->scanCourant); //
			}
		}

		/* scanOcr()
		 * Analyse avec l'ocr l'image courrante et génére un fichier de correction de la forme "image.extension.txt"
		 */
		private function scanOcr(){ // utilisation de l'ocr
			exec('tesseract '.$this->scanCourant.' -l qcmtex -psm 6 '.$this->scanCourant); //-l qcmtex le language dans lequel l'ocr analyse l'image et -psm 6 le mode de ségmentation.Voir documentation Tesseract
 		}
	}
?>