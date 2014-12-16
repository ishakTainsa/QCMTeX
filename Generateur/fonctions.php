<?php 

	function testQCM($nom_fichier)
	{
		$etape=0;
		$fichier_lu = fopen($nom_fichier, 'r');
		while ($ligne = fgets($fichier_lu)) //Lecture du fichier crée
		{
			if ($etape==0 && preg_match('#\\\\begin{qcm}#', $ligne)) //On a detecté un environnement qcm
				$etape=1;
				
			elseif ($etape==1 && preg_match('#\\question (.*)$#', $ligne)) //Verifie si elle comporte au moins une question
				$etape=2;
				
			elseif ($etape==2 && preg_match('#\\\\end{qcm}#', $ligne)) //Il y a une fermeture de l'environnement qcm
				return true;
		}
		echo "Ceci n'est pas un fichier QCM !";
		return false;
	}

	
	function lectureQCM($nom_fichier)
	{
		if (testQCM($nom_fichier))
		{
			$fichier_lu = fopen($nom_fichier, 'r');
			$compteur = -1;
			while ($ligne = fgets($fichier_lu)) //Lecture du fichier crée
			{
				if (preg_match('#\\question (.*)$#', $ligne, $tab)) 
				{
					$compteur++;
					$question[$compteur] = $tab[1]; //On utilise un tableau qui va stocker les questions
				}
						
				if (preg_match('#\\\\(reponse|reponsejuste) (.*)$#', $ligne, $tab))
				{
					$reponse[$compteur][] = $tab[2]; //On utilise un autre tableau qui stocke un tableau de reponse pour chaque question
					if ($tab[1] == 'reponsejuste')
						$reponsejuste[$compteur][] = $tab[2]; //On utilise un troisieme tableau qui stocke les bonnes reponses
				}
			}
			//On transfert les 3 tableaux dans des cookies
			setcookie('question', serialize($question), time()+3600*24, NULL, NULL, FALSE, TRUE);
			if (isset($reponse))
			{
				setcookie('reponse', serialize($reponse), time()+3600*24, NULL, NULL, FALSE, TRUE);
				if (isset($reponsejuste))
					setcookie('reponsejuste', serialize($reponsejuste), time()+3600*24, NULL, NULL, FALSE, TRUE);
			}
			fclose($fichier_lu);
			//unlink ($nom_fichier); //Le fichier devient inutile lorsque qu'on a recuperé les questions et les réponses, il est donc supprimé, sinon il emcombre le serveur !
		
			return true;
		}
		else
			return false;
	}
	
	
	function generationQCM($nom_fichier, $nbQCM, $nbQuestion, $aleatoire)
	{
		if (!empty($_COOKIE['question']))
		{
			$question = unserialize($_COOKIE['question']);
			if (!empty($_COOKIE['reponse']))
				$reponse = unserialize($_COOKIE['reponse']);
			$fichier_ecrit = fopen($nom_fichier, 'w');
			
			fputs ($fichier_ecrit, debutDocument()); //Partie comportant les packages
			
			for($i=1; $i<=$nbQCM; $i++) //Création de X qcm.
			{
				fputs($fichier_ecrit, numeroQCM($i));
				fputs($fichier_ecrit, environnementQCM($nbQuestion, $aleatoire));
				fputs ($fichier_ecrit, feuilleReponse());
				
				if ($i!=$nbQCM)
					fputs ($fichier_ecrit, "\n\t\\newpage\n");
			}
			
			fputs ($fichier_ecrit, finDocument());
			fclose($fichier_ecrit);
			
			return true;
		}
		else
		{
			echo "Erreur lors de la génération du qcm.";
			return false;
		}
	}
	
	function debutDocument()
	{
		//La liste exhaustive des packages permet de ne pas d'avoir d'erreur lorsque l'utilisateur nous transmet un document
		//On a peu de chance qu'il y ait des packages manquants dans ce cas
		$str = 
"\documentclass[a4paper, 11pt]{article}
\usepackage[francais]{babel}
\usepackage[latin1]{inputenc}
\usepackage[T1]{fontenc}
\usepackage[top=2cm, bottom=2cm, left=2cm, right=2cm]{geometry}
\usepackage{amsfonts, amssymb}
\usepackage{tablists}
\usepackage{layout}
\usepackage{setspace}
\usepackage{soul}
\usepackage{ulem}
\usepackage{eurosym}
\usepackage{bookman}
\usepackage{charter}
\usepackage{newcent}
\usepackage{lmodern}
\usepackage{mathpazo}
\usepackage{mathptmx}
\usepackage{url}
\usepackage{verbatim}
\usepackage{moreverb}
\usepackage{listings}
\usepackage{fancyhdr}
\usepackage{wrapfig}
\usepackage{color}
\usepackage{colortbl}
\usepackage{amsmath}
\usepackage{mathrsfs}
\usepackage{makeidx}
\usepackage{dsfont}
\usepackage{pifont}
		
\begin{document}
	\\newenvironment{qcm} {} {}";
	
		return $str;
	}
	
	function numeroQCM($num_repetition)
	{
		$str=
"	
	QCM ".$num_repetition."
	
	Ecrivez votre nom et prenom :
	\begin{qcm}
		\begin{enumerate}";
	
		return $str;
	}

	
	function environnementQCM($nbQuestion, $aleatoire)
	{
		$str="";
		$count=1;
		if (!empty($_COOKIE['question']))
		{
			$question = unserialize($_COOKIE['question']); //Chargement des questions et des réponses /!\ On peut changer en session ...
			if (!empty($_COOKIE['reponse']))
				$reponse = unserialize($_COOKIE['reponse']);
			
			for ($i=0; $i<count($question); $i++)
				$tabCle[]=$i; // Va nous servir pour le choix des questions
			
			if($aleatoire=="oui")
				shuffle($tabCle); //Mélange les éléments du tableau
			
			foreach ($tabCle as $cle)
			{
				$str.= "\n\t\t\t".'\item '.$question[$cle]."\n";
				if (isset($reponse[$cle])) //Si une reponse existe pour la question
				{
					$str.="\t\t\t".'\begin{tabenum}[1)]'."\n";
					
					if ($aleatoire=="oui")
						shuffle ($reponse[$cle]); //Mélange les éléments du tableau
					
					foreach ($reponse[$cle] as $resp)
						$str.="\t\t\t\t".'\tabenumitem '.$resp."\n";
					$str.="\t\t\t".'\end{tabenum}'."\n";
				}
				else //Si la question ne comporte pas de reponse, on saute des lignes
					$str.="\n\n";
				
				if ($count==$nbQuestion)
					return $str;
				else
					$count++;
			}
		}
	}

	
	function feuilleReponse()
	{
		$str = "
		\\end{enumerate}
	
		R\'{e}pondre en noircissant la casse correspondante \`{a} la bonne r\'{e}ponse.

		\begin{center}
			\begin{tabular}{cccccc}

				Questions & 1 & 2 & 3 & 4 & 5\\\

				1 & \ding{109} & \ding{109} & \ding{109} & \ding{109} & \ding{109} \\\

				2 & \ding{109} & \ding{109} & \ding{109} & \ding{109} & \ding{109} \\\

			\\end{tabular}
		\\end{center}
		Vous avez 5 minutes !
	\\end{qcm}";
	
		return $str;
	}
	
	function finDocument()
	{
		$str = "
\\end{document}";
	
		return $str;
	}