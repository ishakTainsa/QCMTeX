<?php

	if (isset($_FILES['file']) && isset($_FILES['file']['name']) && trim($_FILES['file']['name'])!='') // teste si le formulaire a été soumis
	{
		$nom_fichier = basename($_FILES['file']['name']);
		$taille_max = 100000;
		
		if (!preg_match('#^.*\.(tex)$#', (string)$_FILES['file']['name']))
			$erreur = 'Vous devez uploader un fichier de type .tex !';
		
		if (filesize($_FILES['file']['tmp_name']) > $taille_max)
			$erreur = 'Le fichier est trop gros !';
		
		if (!isset($erreur)) // S'il n'y a pas d'erreur, on lit le fichier
		{
			if (move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/'.$nom_fichier)) //Création du fichier
			{
				$fichier_lu = fopen('uploads/'.$nom_fichier, 'r');
				$compteur = -1;
				while ($ligne = fgets($fichier_lu)) //Lecture du fichier crée
				{
					if (preg_match('#\\question (.*)$#', $ligne, $tab)) 
					{
						$compteur++;
						$question[$compteur] = $tab[1]; //On utilise un tableau qui va stocker les questions
					}
					
					if (preg_match('#(reponse|reponsejuste) (.*)$#', $ligne, $tab)) //Le double slash ne marche pas pour reponse, bizarre ...
					{
						$reponse[$compteur][] = $tab[2]; //On utilise un autre tableau qui stocke un tableau de reponse pour chaque question
						if ($tab[1] == 'reponsejuste')
							$reponsejuste[$compteur] = $tab[2]; //On utilise un troisieme tableau qui stocke les bonnes reponses
					}
				}
				fclose($fichier_lu);

				$fichier_ecrit = fopen('generer/'.$nom_fichier.'_generer.tex', 'w'); //Generation du file
				fputs ($fichier_ecrit, '\documentclass[a4paper, 11pt]{article}'."\n"); //Entete du document
				fputs ($fichier_ecrit, '\usepackage[francais]{babel}'."\n");
				fputs ($fichier_ecrit, '\usepackage[latin1]{inputenc}'."\n");
				fputs ($fichier_ecrit, '\usepackage[T1]{fontenc}'."\n\n");
				fputs ($fichier_ecrit, '\begin{document}'."\n");
				fputs ($fichier_ecrit, "\t".'Ecrivez votre nom et prenom :'."\n\n"); //Accent non reconnu par le compilateur ...
				fputs ($fichier_ecrit, "\t".'\newenvironment{qcm} {} {}'."\n\n");
				fputs ($fichier_ecrit, "\t".'\begin{qcm}'."\n\n");
				fputs ($fichier_ecrit, "\t\t".'\begin{enumerate}'."\n");
				
				foreach ($question as $cle=>$quest) //Enumere toutes les questions
				{
					fputs ($fichier_ecrit, "\t\t\t".'\item '.$quest."\n");
					if (isset($reponse[$cle])) //Si une reponse existe pour la question
					{
						fputs ($fichier_ecrit, "\t\t\t".'\begin{enumerate}'."\n");
						foreach ($reponse[$cle] as $resp)
							fputs ($fichier_ecrit, "\t\t\t\t".'\item '.$resp."\n");
						fputs ($fichier_ecrit, "\t\t\t".'\end{enumerate}'."\n");
					}
					else //Si la question ne comporte pas de reponse, on saute des lignes
						fputs ($fichier_ecrit, "\n\n");
					fputs ($fichier_ecrit, "\n");
				}
				
				fputs ($fichier_ecrit, "\t\t".'\end{enumerate}'."\n"); //Bas du document
				fputs ($fichier_ecrit, "\t\t".'Vous avez 5 minutes !'."\n");
				
				fputs ($fichier_ecrit, "\t".'\end{qcm}'."\n");
				fputs ($fichier_ecrit, '\end{document}'."\n");
				
				fclose($fichier_ecrit);
			}
		}
	}
?>
