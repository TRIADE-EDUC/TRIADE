<?php

/*****************************************
*	Configuration
*****************************************/

define ("FLAG_TAMPON", 0);
// Flag pour l'utilisation du tampon, 1 Tampon en service, 0 désactivé
// La désactivation du tampon est possible pour la compatibilité avec php sous win32,
// où les commandes unlink et rename peuvent poser problème.
// Sur un système de type unix l'utilisation du tampon est recommandé

define ("REP_TEMP_PHP2RTF", "./temp_php2rtf/");
// path relatif du repertoire tampon pour l'écriture du doc .rtf
// Si le tampon est désactivé le path est totalement superflu... ;)


/****************************************
*	Fin de la configuration
****************************************/




/*
*	Classes php2rtf version 0.1.4
*	Permet de générer des fichier .rtf via php, sans maitriser forcemment la syntaxe rtf.
*	Licence GNU-GPL
*	Merci d'avance pour vos correctifs, suggestions et remarques : Olivier pnine@free.fr
*/




class php2rtf {


var $tampon_memoire = "";			// tampon mémoire vive, si FLAG_TAMPON est à 0
var $nom_tampon;				// Nom du fichier tampon pour l'écriture du doc .rtf
var $rtf_ratio_lg = 56.7;			// Ratio de conversion entre les twips et l'unité de mesure du document
var $header_charset = "{\\rtf\ansi";		// chaine charset
var $header_fonttbl = "\deff";			// chaine correspondant à la table de font
var $header_colortbl = "";			// chaine correspondant à la table de couleurs
var $pictures;					// chaine correspondant aux entêtes et valeurs hexa d'une image
var $para = "";					// chaine entête d'un paragraphe
var $para_texte = "";				// chaine de texte formaté
var $para_format = "";				// chaine de formatage d'un paragraphe
var $cell_deff = "";				// chaine formatage d'une cellule
var $cell_temp = "";				// chaine de contenu des cellules d'une lignes
var $table_deff = "";				// chaine de formatage d'une ligne de tableau




/*
*	Méthodes générales pour la classe
*/

// Gère l'écriture d'une ligne supplémentaire sur le fichier de tampon
// Renvoie 1 si réussite, 0 en cas d'échec
function rtf_ajout_ligne($ligne){


	if(FLAG_TAMPON){

		$fpw = @fopen(REP_TEMP_PHP2RTF.$this->nom_tampon."_temp", "w"); 
		if (!$fpw) return(0);

		$fpr = @fopen(REP_TEMP_PHP2RTF.$this->nom_tampon, "r");			
		if (!$fpr) return(0);

		while($ligne_r = fgets($fpr, 6138)){
			$test = @fputs($fpw, $ligne_r, 6138);
			if(!$test) 
				return(0);
			}

		$test = @fputs($fpw, $ligne, 6138);
			if(!$test) 
				return(0);	

		if(!@fclose($fpr))
			return(0); 
		if (!@fclose($fpw))
			return(0);

		if (!@unlink(REP_TEMP_PHP2RTF.$this->nom_tampon)) 			
			return(0); 
		if (!@rename(REP_TEMP_PHP2RTF.$this->nom_tampon."_temp", REP_TEMP_PHP2RTF.$this->nom_tampon)) 
			return(0); 
		
		}

		
	else $this->tampon_memoire .= $ligne;

	return(1);
	
	}



// Crée le fichier de tampon pour l'écriture ligne à ligne du texte en rtf
// Renvoie 0 en cas d'erreur de création du fichier
function php2rtf(){

	if (FLAG_TAMPON){	
		$this->nom_tampon = "temp_rtf".time();
	
		$fp = fopen(REP_TEMP_PHP2RTF.$this->nom_tampon, "w");
		if (!$fp)
			return(0);
		else 
			return(1);
		}

	else
		return(1);
	
	}
	

// Retourne le fichier rtf directement vers le nav en ajoutant un content type
// Renvoie 1 en cas de réussite, 0 en cas d'échec
function rtf_get_doc_nav(){

	
	if (!$this->rtf_ajout_ligne("}"))
		return(0);
	
	if(FLAG_TAMPON){
		$fp = fopen(REP_TEMP_PHP2RTF.$this->nom_tampon, "r");
		if (!$fp)
			return(0);

		header("Content-Type: application/msword");
		fpassthru($fp);

		if(!$this->rtf_destructeur_tampon())	
			return(0);
	
		return(1);
	
		}
	
	else{
		header("Content-Type: application/msword");
		
		echo $this->tampon_memoire;
		
		$this->rtf_destructeur_tampon();
		return(1);
		}
		
	}


// Détruit le fichier de tampon, renvoie 1 en cas de réussite, 0 en cas d'échec
function rtf_destructeur_tampon(){
	
	if(FLAG_TAMPON){
		if(!@unlink(REP_TEMP_PHP2RTF.$this->nom_tampon))
			return(0);
		}
	
	else
		$this->tampon_memoire = "";
		
	return(1);
	}



// Gestion des unités de longueur (par défaut le mm)
function rtf_set_unite($unite = 0){

	if(!preg_match('/^[0-3]$/', $unite))
		return(0);

	switch($unite){
	
		// millimètre
		case 0:
			$this->rtf_ratio_lg = 56.7;
			break;
		
		// centimètre
		case 1:
			$this->rtf_ratio_lg = 567;
			break;
		
		// inchs
		case 2:
			$this->rtf_ratio_lg = 1440; 
			break;
		
		// twips
		case 3:
			$this->rtf_ratio_lg = 1;
			break;
		}
	
	return(1);	

	}





/*
*	Méthodes pour gérer les tableaux
*
*
*/

// Ajoute une nouvelle cellule de tableau dans la ligne en cours
function rtf_get_cell_tab(){

	if(empty($this->table_deff) || empty($this->para))
		return(0);
	
	$this->cell_temp .= $this->cell_deff.$this->para.$this->para_format."\intbl".$this->para_texte."\par\cell";
		
	$this->para = "";
	$this->para_texte = "";
	$this->para_format = "";
	$this->cell_deff = "";
		
	return(1);
	}


// Ajoute une nouvelle ligne de tableau dans le tampon
function rtf_get_ligne_tab(){

	if(empty($this->table_deff) || empty($this->cell_temp))
		return(0);
	
	if(!$this->rtf_ajout_ligne("{".$this->table_deff.$this->cell_temp."\\row}"))
		return(0);
		
	$this->table_deff = "";
	$this->cell_temp = "";
		
	return(1);
	
	}


// Formatage d'une ligne d'un tableau
function rtf_set_ligne_tab($align="l", $border=0, $marges=0, $hauteur_ligne=0, $code=0){

	if(!preg_match('/^(l|r|c)$/', $align))
		return(0);
	if(!preg_match('/^[0-9]{1,4}([.][0-9]{1,2})?$/', $border))
		return(0);
	if(!preg_match('/^[-]?[0-9]{1,8}([.][0-9]{1,2})?$/', $hauteur_ligne))
		return(0);
	if(!preg_match('/^[0-2]$/', $code))
		return(0);
	if(!preg_match('/^[0-9]{1,8}([.][0-9]{1,2})?$/', $marges))
		return(0);
	
	$this->table_deff = "\\trowd";
	$this->table_deff .= "\\trgaph".round($marges * $this->rtf_ratio_lg);
	$this->table_deff .= "\\trq".$align;
	if(!empty($border)){
		$border = round($border * $this->rtf_ratio_lg);
		$this->table_deff .= "\\trbrdrt\brdrw".$border."\\trbrdrb\brdrw".$border."\\trbrdrl\\brdrw".$border."\\trbrdrr\brdrw".$border;
		}
		
	$this->table_deff .= "\\trrh".round($hauteur_ligne * $this->rtf_ratio_lg);
	
	if($code == 1)
		$this->table_deff .= "\\trkeep";
	if($code == 2)
		$this->table_deff .= "\\trkeepfollow";
	
	
	return(1);	
	}



// Formatte la cellule en cours
function rtf_set_cell_tab($largeur, $valign="t", $border=0, $ombrage=0, $color=-1){

	if(!preg_match('/^[0-9]{1,8}([.][0-9]{1,2})?$/', $largeur))
		return(0);
	if(!preg_match('/^(t|b|c)$/', $valign))
		return(0);
	if(!preg_match('/^[0-9]{1,5}([.][0-9]{1,2})?$/', $border))
		return(0);
	if(!preg_match('/^[0-9]{1,3}$/', $ombrage))
		return(0);
	if(!preg_match('/^(-1|[0-9]{1,4})$/', $color))
		return(0);
	
	$this->cell_deff .= "\clvertal".$valign;
	
	if(!empty($border)){
		$border = round($border * $this->rtf_ratio_lg);
		$this->cell_deff .= "\clbrdrt\brdrw".$border."\clbrdrb\brdrw".$border."\clbrdrl\brdrw".$border."\clbrdrr\brdrw".$border."\clbrdrl\brdrw".$border;
		}
	if($color != -1)
		$this->cell_deff .= "\clcbpat".$color;
	
	$this->cell_deff .= "\clshdng".($ombrage * 100);
	
	$this->cell_deff .= "\cellx".round($largeur * $this->rtf_ratio_lg);
	
	return(1);

	}




/*
*	Gère les paragraphes et le formattage des caractères
*
*	Pris en charge : 
*		Couleurs des fontes (ref header_colortbl)
*		Polices des fontes (ref header_fonttbl)
*		Espacement avant, et après le paragraphe
*		Saut de page avant le paragraphe
*		Formattage du texte (Bold, Italic, Underline)
*		Taille de la police	
*		Retrait à droite, à gauche et de la première ligne
*		Espacement interligne
*		Alignement centré, à droite, à gauche
*	
*	Possibilité d'appeler directement la méthode rtf_para() 
*	, dans ce cas pas de possibilité de formattage des caractères (police, taille, couleur...),
*	 mais possibilité néanmoins de formatter le paragraphe (alignement, retrait...)
*	
*	Ou d'appeler rtf_set_para_format() , puis rtf_set_para_texte() , puis rtf_get_para()
*	Dans ce cas, plusieurs appels successifs à rtf_set_para_texte() sont posssible.
*/


function rtf_get_para(){

	if(empty($this->para))
		return(0);
	
	if(!$this->rtf_ajout_ligne($this->para.$this->para_format.$this->para_texte."\par"))
			return(0);
		
		$this->para = "";
		$this->para_texte = "";
		$this->para_format = "";
		
		return(1);

	}


// Permet le formattage du texte d'un paragraphe
function rtf_set_para_texte($texte, $font_style=-1, $font_size=0, $font_number=-1, $format=0, $f_color=-1){

	if(!preg_match('/^([0-9]{1,3}|-1)$/', $font_style))
		return(0);
	if(!preg_match('/^[0-9]{1,4}$/', $font_size))
		return(0);
	if(!preg_match('/^([0-9]{1,3}|-1)$/', $font_number))
		return(0);
	if(!preg_match('/^([biu]+|0)$/', $format))
		return(0);
	if(!preg_match('/^([0-9]{1,3}|-1)$/', $f_color))
		return(0);

	if(!empty($font_size))
		$this->para_texte .= "\fs".$font_size;
	
	// Pas encore implémenté --> stylesheet
	// if($font_style != -1)
	//	$this->para_texte .= "\cs".$font_style
	
	
	if($font_number != -1)
		$this->para_texte .= "\f".$font_number;
	
	if(!empty($format)){
		
		$temp = "";
		if(preg_match('/[b]/', $format))
			$temp .= "\b";
		if(preg_match('/[u]/', $format))
			$temp .= "\uldash";
		if(preg_match('/[i]/', $format))
			$temp .= "\i";
		
		$this->para_texte .= $temp;
		}
		
	
	if($f_color != -1)
		$this->para_texte .= "\cf".$f_color;
	
	$texte = str_replace("\n", "", $texte);
	$texte = str_replace("\r", "", $texte);

	if (strlen($texte) > 4096)
		return(0);
		
	$this->para_texte .= " ".$texte."\plain ";
	
	return(1);	
	}


// Gestion rapide des paragraphes sans mise en forme des textes
function rtf_para($texte, $code=0, $style=-1, $align="l", $indent=0, $space=0){

	
	// style non encore implementé (stylesheet dans les headers....)
	
	
	if (is_array($indent) && isset($indent[0]))
		$l_indent = $indent[0];
	else
		$l_indent = 0;
	
	if (is_array($indent) && isset($indent[1]))
		$r_indent = $indent[1];
	else
		$r_indent = 0;
	if (is_array($indent) && isset($indent[2]))
		$fl_indent = $indent[2];
	else
		$fl_indent = 0;
		
	if (is_array($space) && isset($space[0]))
		$space_b = $space[0];
	else
		$space_b = 0;
		
	if (is_array($space) && isset($space[1]))
		$space_a = $space[1];
	else
		$space_a = 0;
		
	if (is_array($space) && isset($space[2]))
		$space_l = $space[2];
	else
		$space_l = 0;
		
	if(!$this->rtf_set_para_format($code, $style, $align, $l_indent, $r_indent, $fl_indent, $space_b, $space_a, $space_l))
		return(0);

	if(!$this->rtf_set_para_texte($texte))
		return(0);
		
	$temp = $this->para.$this->para_format.$this->para_texte."\par";
	if(!$this->rtf_ajout_ligne($temp))
		return(0);
		
	$this->para = "";
	$this->para_texte = "";
	$this->para_format = "";
		
	return(1);
		
	}
	
// Formattage du paragraphes de textes	
function rtf_set_para_format($code=0, $style=0, $align="l", $l_indent=0, $r_indent=0, $fl_indent=0, $space_b=0, $space_a=0, $space_l=0){
	
	if(!preg_match('/^(l|c|j|r|d)$/', $align))
		return(0);
	if(!preg_match('/^[0-9]+([.][0-9]{1,2})?$/', $fl_indent))
		return(0);
	if(!preg_match('/^[0-9]+([.][0-9]{1,2})?$/', $l_indent))
		return(0);
	if(!preg_match('/^[0-9]+([.][0-9]{1,2})?$/', $r_indent))
		return(0);
	if(!preg_match('/^[0-9]+([.][0-9]{1,2})?$/', $space_b)) 
		return(0);
	if(!preg_match('/^[0-9]+([.][0-9]{1,2})?$/', $space_a)) 
		return(0);
	if(!preg_match('/^[-]?[0-9]+([.][0-9]{1,2})?$/', $space_l)) 
		return(0);     

	$this->para_format = "\q".$align;
              
	switch($code){
		case 0: $this->para = "\par\plain";
			break;

		case 1: $this->para = "\pard\plain";
			break;

		case 2: $this->para = "\par\pagebb\plain";
			break;
			
		case 3: $this->para = "\pard\pagebb\plain";
			break;
		
		default: $this->para = "\par\plain"; 
		}

	// style pas encore implémenter...

	if($fl_indent)                                 	
		$this->para_format .= "\fi".round($fl_indent * $this->rtf_ratio_lg);
	if($l_indent)
		$this->para_format .= "\li".round($l_indent * $this->rtf_ratio_lg);
	if($r_indent)
		$this->para_format .= "\\ri".round($r_indent * $this->rtf_ratio_lg);
	if($space_b)
		$this->para_format .= "\sb".round($space_b * $this->rtf_ration_lg);
	if($space_a)
		$this->para_format .= "\sa".round($space_a * $this->rtf_ratio_lg);
	if($space_l)
		$this->para_format .= "\sl".round($space_l * $this->rtf_ratio_lg);
		
	return(1);
	}






/*
*	Seules les images au format .jpeg sont pour l'instant supportées 
*	Par défaut l'image est affichée en taille réelle
*	possibilité de spécifier les dimensions picscalex et picscaley
*
*/

function rtf_pictures($path, $pictx=0, $picty=0){

	if(!is_file($path))
		return(0);
	
	if(!preg_match('/^[0-9]{1,10}([.][0-9]{1,2})?$/', $pictx))
		return(0);
	if(!preg_match('/^[0-9]{1,10}([.][0-9]{1,2})?$/', $picty))
		return(0);
		  	
	$this->pictures = "{\pict";
	
	$tab_img = @getimagesize($path);
	if(!is_array($tab_img) || $tab_img[2] == 1 || $tab_img[2] == 3)
		return(0);
	if ($tab_img[2] == 2)
		$this->pictures .= "\jpegblip";

	/*
	Pas encore implémenté, support des images en .png
	if ($tab_img[2] == 3)
	*/

	srand(time());
	$temp = rand(100000000, 999999999);
	$this->pictures .= "\bliptag-".$temp;
	
	if(!empty($pictx))
		$this->pictures .= "\picscalex".round($pictx * $this->rtf_ratio_lg);
	if(!empty($picty))
		$this->pictures .= "\picscaley".round($picty * $this->rtf_ratio_lg);
		
	
	$fp = @fopen($path, "r");
	if(!$fp)
		return(0);
	while($ligne = @fread($fp, 4096)){
		if(!$ligne)
			return(0);
		$this->pictures .= bin2hex($ligne); 
		}
	
	$this->pictures .= "}";
	
	if (!$this->rtf_ajout_ligne($this->pictures))
		return(0);
	
	$this->pictures = "";
	return(1);
	
	}




/*
*	La partie Headers, sont gérées les points suivants :
*	Charset (0 à 3)
*	fonttbl (fontfamilly et fontname)
*	colortbl (tab multidiemensionnel RVB, une couleur par ligne)
*/

function rtf_set_header($charset=0, $fonttbl=0, $colortbl=0){

	if(!$this->rtf_set_header_charset($charset))
		return(0);
	if(!$this->rtf_set_header_fonttbl($fonttbl))
		return(0);
	if(!$this->rtf_set_header_colortbl($colortbl))
		return(0);
		
	}



function rtf_get_header(){

	if(!$this->rtf_ajout_ligne($this->header_charset.$this->header_fonttbl.$this->header_colortbl))
		return(0);
		
	$this->header_charset = "";
	$this->header_fonttbl = "";
	$this->header_colortbl = "";	
	
	return(1);
	}


// Définition des couleurs disponibles dans le document pour les polices de caractères
function rtf_set_header_colortbl($colortbl){

	if(is_array($colortbl)){
		
		$this->header_colortbl = "{\colortbl;";
		
		// A modifier pour travailler sur des tableaux non 'constant'
		
		$fin = count($colortbl);
		for($a=0 ; $a < $fin ; $a++){
			if(!isset($colortbl[$a][0]) || !isset($colortbl[$a][1]) || !isset($colortbl[$a][2]) || $colortbl[$a][0]>255 
				|| $colortbl[$a][1]>255 || $colortbl[$a][2]>255 ||$colortbl[$a][0]<0 || $colortbl[$a][1]<0 || $colortbl[$a][2]<0)
				return(0);
		
			$this->header_colortbl .= "\\red".$colortbl[$a][0]."\green".$colortbl[$a][1]."\blue".$colortbl[$a][2].";";
			}
			
		$this->header_colortbl .= "}";
	
		}
	
	}


// Définition des familles et polices de caractères
function rtf_set_header_fonttbl($fonttbl=0){

	if(is_array($fonttbl)){
		$this->header_fonttbl = "{\fonttbl";
		 
		 // A modifier pour travailler sur des tableaux non 'constant'
		 
		$fin = count($fonttbl);
		for($a=0 ; $a < $fin ; $a++){
			if (!isset($fonttbl[$a][0]))
				return(0);
			$this->header_fonttbl .= "{\f".$a."\f".$fonttbl[$a][0]." ".$fonttbl[$a][1].";}";
			}
		$this->header_fonttbl .= "}";		
			
		}
	
	return(1);
	}


// Définition du charset
function rtf_set_header_charset($charset=0){

	switch($charset){
		case 0:
			$this->header_charset = "{\\rtf\ansi";
			break;
		case 1:
			$this->header_charset = "{\\rtf\mac";
			break;
		case 2:
			$this->header_charset = "{\\rtf\pc";
			break;
		case 3:
			$this->header_charset = "\\rtf\pca";
			break;
		default:
			$this->header_charset = "{\\rtf\ansi";
		}

	}

}
