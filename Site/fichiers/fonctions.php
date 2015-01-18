<?php

/******** EXTRACTION ********/
	
	/** Extrait le qcm.
	 *  On prend la question à la position N et on lit les réponses jusqu'à la prochaine question.
	 *  Si une question n'a pas de réponse, on passe directement à la question suivante : l'array Reponses de la question sans réponse ne sera pas défini.
	 * @param File $texFile variable représentant le fichier tex uploader par le formulaire.
	 * @return array $questionsReponses 
	 */
	function extractQcm($texFile){
		$fichier = new SplFileInfo($texFile);
		$posCurseur = -1; //Position de la question dans le tableau
		if ($fichier->isReadable())
		{
			$fichier_lu = $fichier->openFile('r');
			$n=0;
			while (!$fichier_lu->eof()) //Lecture du fichier ligne par ligne
			{
				
				$ligne = $fichier_lu->current();
				if (preg_match('#\\question (.*)$#', $ligne, $tab)) 
				{	$n=0;
					$posCurseur++; 
					$questionsReponses[$posCurseur]['Question'] = $tab[1];
				}						
				if (preg_match('#\\\\(?:reponse) (.*)$#', $ligne, $tab)){
					$questionsReponses[$posCurseur]['Reponses']["$n"] = $tab[1]; $n++;
					}
					
				else if (preg_match('#\\\\(?:reponsejuste) (.*)$#', $ligne, $tab)){// C'est pour récuperer les bonnes réponses pour plus tard ...
					$questionsReponses[$posCurseur]['Reponses']["rep$n"] = $tab[1]; 
					$n++;
					}
				
				$fichier_lu->next();
			}
		}
		
		if (isset($questionsReponses))
			return $questionsReponses;
		else
			return null;
	}
	
	
	/** Compte le nombre de qcm possible, l'original inclut.
	 * @param File $texFile variable représentant le fichier tex uploader par le formulaire.
	 * @return integer $nbQcm Le nombre de qcm possible.
	 */
	function nbrQcmPossible($texFile)
	{
		$questionsReponses=extractQcm($texFile); // ca récupère le tableau de questions et réponses
		$nbQcm = null; 
		if(isset($questionsReponses)) 
		{
			$taille = count($questionsReponses); // avoir la taille du tableau 
			$factQuestion = gmp_fact($taille);// factoriel de la taille du tableau, ce qui revient à obtenir le nombre de combinaison de questions possible
			$produitFactReponse=1;
			for($i=0; $i<$taille; $i++)
			{
				$factReponses = gmp_fact( count($questionsReponses[$i]['Reponses'])); // factoriel du nombre de réponse par question
				$produitFactReponse*=$factReponses; 
			} 
			$nbQcm=$factQuestion*$produitFactReponse;
		}
		
		return $nbQcm;
	}


	function melange($questionsReponses, $num_swap) 
	{
		for($i=0;$i<count($questionsReponses);$i++){
			shuffle_assoc($questionsReponses[$i]["Reponses"]);
		}
		$questionsReponsesMelanger=$questionsReponses;
		echo "<p></p>";
		return $questionsReponsesMelanger;
	}
	
	function shuffle_assoc(&$array) {
        $keys = array_keys($array);

        shuffle($keys);

        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;

        return true;
    }
	
	/** Génère le nombre de qcm différents demandés
	 * Utilise la fonction melange($questionsReponses)
	 * Faire test si $nbrQcmVoulu < $nbrQcmPossible dans l'appelant
	 *
	 * @param Array $questionsReponses
	 * @param integer $nbrQcmVoulu Nombre de qcm que l'utilisateur veut générer.
	 * @return $tabQuestionsReponses Tableau de $questionsReponses mélangé.
	 */
	function genererTabQuestionReponses($questionsReponses, $nbrQcmVoulu){
		for ($i=0; $i<$nbrQcmVoulu; $i++) {
			$tabQuestionsReponses[$i] = melange($questionsReponses, $i);
		}
		return $tabQuestionsReponses;
	}

		function avantQcm($texFile){
		$fichier= fopen($texFile,'r');
		$debut = fgets($fichier);
		$debut.='\usepackage{tablists}
\usepackage{enumerate}'."\n";
		while($lignes = fgets($fichier) and !preg_match("#\\\begin{qcm}#", $lignes))
			$debut.=$lignes;
		fclose($fichier);
		return $debut;
	}

	function apresQcm($texFile){
		$fichier= fopen($texFile,'r');
		$fin = '';
		$end = false;
		while($lignes = fgets($fichier)){
			if($end)
				$fin .= $lignes;	
			else if(preg_match("#\\\\end{qcm}#", $lignes))
				$end = true;
		}
		fclose($fichier);
		return $fin;
	}
	
	
	/* Début d'un environnement Qcm
	 * @param integer $num_repetition Numéro du QCM
	 * @return String $str 
	 */
	function numeroQCM($num_repetition){
		$str ="QCM ".$num_repetition."\begin{enumerate}";
		return $str;
	}
	
	
	/* Permet de renvoyer une question avec ses réponses en format latex.
	 *
	 * @param Array $questionReponse Un tableau comportant une question avec ses éventuelles réponses.
	 * @param String $modeReponse Choix d'affichage des réponses, il n'y a pour l'instant que l'affichage par liste et par colonne.
	 * @param String $codage Symbole qui illustre la réponse, il n'y a que la lettre ou le chiffre qui est opérationnel.
	 * @param String $ponctuation Sysmbole qui suit le $codage.
	 * @return String $str La question et ses réponse en format Latex
	 */
	function corpsQcm($questionReponses, $modeReponse, $codage)
	{
		if ($modeReponse == 'liste') {
			$env = "enumerate";
			$item = "item";
		}
		
		else if ($modeReponse == 'colonne') {
			$env = 'tabenum';
			$item = 'tabenumitem';
		}
		
		if ($codage == 'chiffre')
			$code = '1';
		else if ($codage == 'lettre')
			$code = 'a';
			
		$code .= ')';
		
		$str = "\n\t\t\t".'\item '.$questionReponses['Question']."\n";
		if (isset($questionReponses['Reponses'])) //Si une reponse existe pour la question
		{
			$str .= "\t\t\t".'\begin{'.$env.'} ['.$code."]\n";
			foreach ($questionReponses['Reponses'] as $resp)
				$str .= "\t\t\t\t\\".$item.' '.$resp."\n";
			$str .= "\t\t\t".'\end{'.$env.'}'."\n";
		}
		$str .= "\t\t\\vspace{5mm}\n";
		return $str;
	}
	
	/* Fin d'un environnement Qcm
	 * @return String $str 
	 */
	function finQcm(){
		$str ='\end{enumerate}';
		return $str;
	}
	
	function nbQPQCM($tabQCM,$nbQuestions){
		$tabres=array();
		for($i=0;$i<count($tabQCM);$i++){
			shuffle_assoc($tabQCM[$i]);
			$output = array_slice($tabQCM[$i], 0, $nbQuestions); 
			$tabres[]=$output;
		}
		
		return $tabres;
	}


/******    Generation de fichiers      ********/


	function genererTexFileGrilleDeReponse($tabQuestionsReponses,$numerotation){
		$texFileResultat = fopen('ddl/grilleRep.tex', 'w');
		$debut = '\documentclass[a4paper]{article}'."\n".'\pagestyle{empty}'."\n".'\usepackage[17pt]{extsizes}'."\n".'\usepackage[francais]{babel}'."\n".'\usepackage[latin1]{inputenc}'."\n".'\usepackage[T1]{fontenc}'."\n".'\usepackage[top=2cm, bottom=2cm, left=2cm, right=2cm]{geometry}'."\n".'\usepackage{amsmath}'."\n".'\usepackage{MnSymbol}'."\n".'\usepackage{wasysym}'."\n".'\begin{document}';
		fputs($texFileResultat, $debut);
		foreach ($tabQuestionsReponses as $numQcms => $qcm) {
			$str ="\n\t".'\begin{center}'."\n\t\t".'qcm '.($numQcms+1)."\n\t".'\end{center}'."\n";
			fputs($texFileResultat,$str);
			$contenu = '';
			$MaxNbrQ = 0;
			$numQ = 1;
			$type = 0;
			if ($numerotation == 'chiffre')
				$type = '1';
			else if ($numerotation == 'lettre')
				$type = 'a';
			foreach ($qcm as $question) {
				$nbrQ = 0;
				$contenu .="\n\t\t\t".$numQ.' . ';
				foreach ($question['Reponses'] as $reponses){
					$contenu .= '&$\square$';
				}
				$nbrQ = count($question['Reponses']);
				if($nbrQ > $MaxNbrQ)
					$MaxNbrQ = $nbrQ;
				$contenu .='\\\\';
				$numQ++;
			}
			$str = "\t".'nom :\\\\'."\n\t".'prenom :\\\\'."\n\t".'groupe :'."\n\t".'\begin{center}'."\n\t\t".'\begin{tabular}{*{'.($MaxNbrQ+1).'}{c}}';
			fputs($texFileResultat,$str);
			$str = "\n\t\t\t";
			for($i = 0;$i<$MaxNbrQ;$i++){
				$str .= '&'.$type;
				$type++;
			}
			fputs($texFileResultat,$str.'\\\\');
			fputs($texFileResultat,$contenu);
			$str = "\n\t\t".'\end{tabular}'."\n\t".'\end{center}'."\n\t".'\\newpage';
			fputs($texFileResultat,$str);
		}
		$fin ="\n".'\end{document}';
		fputs($texFileResultat,$fin);
		fclose($texFileResultat);
	}

	/** Crée le fichier Tex avec les questions/réponses mélangés.
	 * @param Array $tabQuestionsReponses Tableau de $questionsReponses mélangé.
	 * @return File $texFileResultat Le fichier Qcm proposé à l'utilisateur.
	 */
	function genererTexFileResultat($tabQuestionsReponses,$typeQ,$chiffreOuLettre,$texFile){
		$texFileResultat = fopen('ddl/fichier_genere.tex', 'w');
		fputs ($texFileResultat, avantQcm($texFile)); // Susceptible de changer car on pourrait récuperer les packages directement à partir du fichier ..
		
		for ($i=0; $i<count($tabQuestionsReponses); $i++)
		{
			//Ajout : Mettre ici les questions en commun avant chaque QCM. Mais comment demander à l'utilisateur de le faire ???
			
			fputs($texFileResultat, numeroQCM($i+1));
			foreach ($tabQuestionsReponses[$i] as $questionReponses) {
				fputs($texFileResultat, corpsQcm($questionReponses, $typeQ, $chiffreOuLettre));
			}
			
			fputs($texFileResultat, finQcm());
			if ($i+1 != count($tabQuestionsReponses))
				fputs ($texFileResultat, "\n\t\\newpage\n");
		}
			
		fputs ($texFileResultat, apresQcm($texFile));
		fclose($texFileResultat);
		
		return $texFileResultat;
	}

	/* genererCorrectionAutomatique($tabQuestionReponses)
	 *
	 * @param Array $tabQuestionReponse
	 * Genere un fichier .corr pour pouvoir effectuer une correction automatique ultérieurement
	 * @return null
	 */
	function genererCorrectionAutomatique($tabQuestionsReponses){
			/*parcourir tout le tableau
					je crée un arrayCorrige
					pour chaque QCM faire:
						je crée un arrayQCM 
						je parcour les question:
								je parcour les réponses
									je crée un array
									je lis les clés des questions
									si clés  preg "rep" 
										ajouter case noire à array
									si non
										ajouter case blanche à array
								j'ajoute l'array à l'arrayQCM	
						j'ajoue l'arrayCorrige à
			
			*/
		$tabCorrection = array();
		for($i=0;$i<count($tabQuestionsReponses);$i++){
			$qcm = array();
			foreach($tabQuestionsReponses[$i] as $tabQR){
				$q = array();
				foreach($tabQR['Reponses'] as $rep => $val){
					if(preg_match("#rep#",$rep)){
						$q[]='■';
					}
					else{
						$q[]='□';
					}
				}
				$qcm[]=$q;
			}
			$tabCorrection[]=$qcm;
		}
		$fichierRep = serialize($tabCorrection);
		$_SESSION['corrige'] = $fichierRep;
		return $tabCorrection;
	
	}

	/* genererCorrectionMain($tabQcm)
	 *
	 * @param Array $tabCorrection Un tableau comportant les carrés vide et pleins correspondant a la correction des grilles de réponse
	 * Genere un fichier .doc pour pouvoir effectuer une correction manuelle des Qcms.
	 * @return null
	 */
	function genererCorrectionMain($tabCorrection){
		$File = fopen('ddl/fichier_corr_hand.doc', 'w');
		for($i=0;$i<count($tabCorrection);$i++){
			$str="";
			$str.="QCM".($i+1)."\r\n \r\n";
			foreach($tabCorrection[$i] as $tab){
				$ligne="";
				foreach($tab as $cle => $rep){
					$ligne=$ligne."\t".$rep;
				}
				$str.=" $ligne \r\n \r\n";
			}
			fwrite($File, $str);
			}
		fclose($File);
	}
	
/******    Autres  Fonctions    ********/
	
	/* isPdfFile($pdfFile)
	 *
	 * @param File $pdfFile variable représentant le fichier pdf uploader par le formulaire
	 * Vérifie si l'extension du fichier est bien ".pdf"
	 * @return boolean true si l'extension est de type .pdf, false sinon
	 */
	function isPdfFile($pdfFile){
		$fichier= new SplFileInfo($pdfFile);
		$extension = pathinfo($fichier->getFilename(), PATHINFO_EXTENSION);
			
		if($extension != 'pdf')
			return false;
		else
			return true;
	}

	/* isCorrFile($corrFile)
	 *
	 * @param File $corrFile variable représentant le fichier corr uploader par le formulaire
	 * Vérifie si l'extension du fichier est bien ".corr"
	 * @return boolean true si l'extension est de type .corr, false sinon
	 */
	function isCorrFile($corrFile){
		$fichier= new SplFileInfo($corrFile);
		$extension = pathinfo($fichier->getFilename(), PATHINFO_EXTENSION);
			
		if($extension != 'corr')
			return false;
		else
			return true;
	}

	/* isTexFile($texFile)
	 *
	 * @param File $texFile variable représentant le fichier tex uploader par le formulaire
	 * Vérifie si l'extension du fichier est bien ".tex"
	 * @return boolean true si l'extension est de type .tex, false sinon
	 */
	function isTexFile($texFile){
		$fichier= new SplFileInfo($texFile);
		$extension = pathinfo($fichier->getFilename(), PATHINFO_EXTENSION);
		
		if($extension != 'tex')
			return false;
		else
			return true;
	}

	/* isQcm($texFile)
	 *
	 * @param File $texFile variable représentant le fichier tex uploader par le formulaire.
	 * Verifie si le fichier tex contient l'environnement QCM.
	 * @return boolean true si le fichier contient un environnement qcm, false sinon.
	 */
	
	function isQcm($texFile){
		$fichier = new SplFileInfo($texFile);
		$debutQcm = false;
		
		if ($fichier->isReadable())
		{
			$fichier_lu = $fichier->openFile('r');
			while (!$fichier_lu->eof()) //Lecture du fichier ligne par ligne
			{
				$ligne = $fichier_lu->current();
				if (!$debutQcm && preg_match('#\\\\begin{qcm}#', $ligne))
					$debutQcm = true;
						
				if ( $debutQcm && preg_match('#\\\\end{qcm}#', $ligne))
					return true;
				
				$fichier_lu->next();
			}
		}
		return false;
 	}

	/* deleteAllOldFiles()
	 *
	 * @param null 
	 *	Supprime tous les fichiers uploader quand une nouvelle session est crée
	 * @return null
	 */

	function deleteAllOldFiles(){
		echo "delete all";// vider le contenu du fichier upload
	}

	/************ Fin du fichier ***********/
?>