<?php


/*** Variables ****
	
	$texFile : variable représentant le fichier tex uploader par le formulaire
	$nbrQcmPossible : nombre de qcm qu'il est possible de générer
	$nbrQcmVoulu : nombre de qcm différents a générer demander par l'utilisateur
	$questionsReponses : tableau associatif des questions réponses (!!!A VOIR!!! le format du tableau associatif)			
	$questionsReponsesMelanger : le tableau du dessus mais mélanger
	$tabQuestionsReponses : l'ensemble des qcm !!différents!! générer stocker dans un tableau associatif (!!!A VOIR!!! le format du tableau associatif)
	$texFileResultat : variable représentant le fichier tex a retourner a l'utilisateur

*******************

	/*** PLUS SIMPLE ***/		//TEAM : Garthigan, Irvan, ??Ishak
	
	/** Vérifie l'extension d'un fichier
	 * @param File $texFile variable représentant le fichier tex uploader par le formulaire
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
	
	
	/** Extrait le qcm.
	 *  On prend la question à la position N et on lit les réponses jusqu'à la prochaine question.
	 *  Si une question n'a pas de réponse, on passe directement à la question suivante : l'array Reponses de la question sans réponse ne sera pas défini.
	 * @param File $texFile variable représentant le fichier tex uploader par le formulaire.
	 * @return array $questionsReponses de la forme :
	 *								Array (
	 * 										[0] => Array ( [Question] => "Question 1",
	 *													   [Reponses] => Array ( 
	 *																			 [0] => "Reponse1", 
	 *																			 [1] => "Reponse2" ) ),
	 *										[1] => Array ( ... ) 		);
	 */
	function extractQcm($texFile){

		$fichier = new SplFileInfo($texFile);
		$posCurseur = -1; //Position de la question dans le tableau
		if ($fichier->isReadable())
		{
			$fichier_lu = $fichier->openFile('r');
			while (!$fichier_lu->eof()) //Lecture du fichier ligne par ligne
			{
				$ligne = $fichier_lu->current();
				if (preg_match('#\\question (.*)$#', $ligne, $tab)) 
				{
					$posCurseur++; 
					$questionsReponses[$posCurseur]['Question'] = $tab[1];
				}
						
				if (preg_match('#\\\\(?:reponse|reponsejuste) (.*)$#', $ligne, $tab))
					$questionsReponses[$posCurseur]['Reponses'][] = $tab[1];
				
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
	
	
	/** Parcourir le tableau en faisant des swap de n.
	 *
	 * @param Array $tab Le tableau à parcourir.
	 * @param integer $n Intervale pour effectuer le swap.
	 * @return Array $tabSwap Le tableau swapper.
	 * Fait un bisou à khafif
	 */
	function swap($tab, $n) 
	{
		$tabSwap[] = null;
		
		if ($n > count($tab))
			$n -= count($tab);
			
		for ($i=0; $i+$n<count($tab); $i++)
		{
			if (!isset($tabSwap[$i])) 
			{
				$tabSwap[$i] = $tab[$i+$n];
				$tabSwap[$i+$n] = $tab[$i];
			}
		}
		
		for ($i=0; $i<count($tab); $i++)
		{
			if (!isset($tabSwap[$i]))
				$tabSwap[$i] = $tab[$i];
		}
		
		ksort($tabSwap);
		return $tabSwap;
	}
	

/*******************************/ //fonctions ajoutés par Adel LE 18/12/2014

	function voirLesDerniersSujets(){ //On verra plus tard 

	}
	
	
	/** Verifie si le fichier tex contient l'environnement QCM.
	 * @param File $texFile variable représentant le fichier tex uploader par le formulaire.
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

//**************************************************************************************************************************************************/\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\




	/*** PLUS COMPLEXE ***/		//TEAM : Paul, François, ??Ishak ,??Adel

	/** Mélange l'ordre des questions et des réponses de manière différente selon le nombre de questions dans un fichier.
	 * Voir shuffle($array) : http://fr.php.net/manual/fr/function.shuffle.php
		Complexité:
		test pour la taille du fichier:
				<X:		swap   (n,n)(n,n+1)
				>X:		shuffle	 ()
	 * @param Array $questionsReponses
	 * @param integer $num_swap Permet de définir l'intervalle pour effectuer le swap.
	 * @return array $questionsReponsesMelanger, null si échec
	 */
	function melange($questionsReponses, $num_swap) 
	{
		$limite = 15;
		$questionsReponsesMelanger = null;
		if (isset($questionsReponses)) 
		{
			if (count($questionsReponses) > $limite) //Cas où on a beaucoup de questions
			{
				$num_case=0; //position courante du nouveau tableau mélangé
				for ($i=0; $i<count($questionsReponses); $i++)
					$tabCle[]=$i; // Va nous servir pour le mélange des questions
			
				shuffle($tabCle);
			
				foreach ($tabCle as $posCurseur) 
				{
					$questionsReponsesMelanger[$num_case]['Question'] = $questionsReponses[$posCurseur]['Question']; //Récupération des questions mélangés
					if (isset($questionsReponses[$posCurseur]['Reponses']))
					{
						shuffle($questionsReponses[$posCurseur]['Reponses']);
						
						foreach ($questionsReponses[$posCurseur]['Reponses'] as $reponse) 
							$questionsReponsesMelanger[$num_case]['Reponses'][] = $reponse; //Récupération des réponses mélangés
					}
					$num_case++;
				}
			}
		    
			else if (count($questionsReponses) < $limite) //Cas où on a peu de questions
			{
				for ($i=0; $i<count($questionsReponses); $i++)
				{	
					$questionsMelanger = swap($questionsReponses, $num_swap);
					$questionsReponsesMelanger = $questionsMelanger;
					$questionsReponsesMelanger[$i]['Reponses'] = swap($questionsMelanger[$i]['Reponses'], $num_swap+$i);
				}
			}
		}
		return $questionsReponsesMelanger;
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

	
	/** Crée le fichier Tex avec les questions/réponses mélangés.
	 * @param Array $tabQuestionsReponses Tableau de $questionsReponses mélangé.
	 * @return File $texFileResultat Le fichier Qcm proposé à l'utilisateur.
	 */
	function genererTexFileResultat($tabQuestionsReponses){
		$texFileResultat = fopen('fichier_genere.tex', 'w');
		fputs ($texFileResultat, debutDocument(11)); // Susceptible de changer car on pourrait récuperer les packages directement à partir du fichier ..
		
		for ($i=0; $i<count($tabQuestionsReponses); $i++)
		{
			//Ajout : Mettre ici les questions en commun avant chaque QCM. Mais comment demander à l'utilisateur de le faire ???
			
			fputs($texFileResultat, numeroQCM($i+1));
			foreach ($tabQuestionsReponses[$i] as $questionReponses) {
				fputs($texFileResultat, corpsQcm($questionReponses, 'colonne', 'chiffre', ')'));
			}
			
			fputs($texFileResultat, finQcm());
			if ($i+1 != count($tabQuestionsReponses))
				fputs ($texFileResultat, "\n\t\\newpage\n");
		}
			
		fputs ($texFileResultat, finDocument());
		fclose($texFileResultat);
		
		return $texFileResultat;
	}
	

//**************************************************************************************************************************************************/\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	
	/*** Autres fonctions utiles ***/
	
	/* Entete du document proposé à l'utilisateur
	 * @param integer $taillePolice Correspond à la taille du texte
	 * @return String $str 
	 */
	function debutDocument($taillePolice)
	{
		$str = 
"\documentclass[a4paper, ".$taillePolice."pt]{article}
\usepackage[francais]{babel}
\usepackage[latin1]{inputenc}
\usepackage[T1]{fontenc}
\usepackage[top=2cm, bottom=2cm, left=2cm, right=2cm]{geometry}
\usepackage{tablists}
\usepackage{enumerate}
		
\begin{document}
	\\newenvironment{qcm} {} {}";
	
		return $str;
	}
	
	
	/* Début d'un environnement Qcm
	 * @param integer $num_repetition Numéro du QCM
	 * @return String $str 
	 */
	function numeroQCM($num_repetition)
	{
		$str =
"	
	QCM ".$num_repetition."
	
	Ecrivez votre nom et prenom :
	\begin{qcm}
		\begin{enumerate}";
	
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
	function corpsQcm($questionReponses, $modeReponse, $codage, $ponctuation)
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
			
		$code .= $ponctuation;
		
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
	function finQcm()
	{
		$str =
"
		\\end{enumerate}
	\\end{qcm}";
		
		return $str;
	}
	
	
	/* Fin du doccument proposé à l'utilisateur.
	 * @return String $str 
	 */
	function finDocument()
	{
		$str = "
\\end{document}";
	
		return $str;
	}
?>