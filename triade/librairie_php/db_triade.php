<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.org
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
//
// fonctions métiers de Triade
//


include_once 'lib_param.php';
include_once 'DB.php';
include_once 'timezone.php';
include_once 'conf_error.php';
include_once 'lib_prefixe.php';


global $prefixe;
global $cnx;
global $dsn;
global $ERROR;
global $gestionMDP;

function cnx2() {
	global $dsn;
	global $prefixe;
	$cnx = DB::connect($dsn);
	if(DB::isError($cnx)){
		//exit($cnx->getMessage());
	}else{
		if ((! @file_exists("../data/install_log/noaccess.inc")) && (! @file_exists("../../../../common/stop.triade"))) {
			if (!get_magic_quotes_gpc()) {
				if (preg_match('/demdst2\.php/',$_SERVER['REQUEST_URI'])) { 
					foreach($_POST['saisie_acc'] as &$check){ $check = mysql_real_escape_string($check);  }	
				}elseif (preg_match('/noteajout3\.php/',$_SERVER['REQUEST_URI'])) { 
					foreach($_POST['elev_id'] as &$check){ $check = mysql_real_escape_string($check);  }
					foreach($_POST['elev_nom'] as &$check){ $check = mysql_real_escape_string($check);  }
				}elseif (preg_match('/ajoutnotes3\.php/',$_SERVER['REQUEST_URI'])) { 
					foreach($_POST['elev_id'] as &$check){ $check = mysql_real_escape_string($check);  }
					foreach($_POST['elev_nom'] as &$check){ $check = mysql_real_escape_string($check);  }
				}elseif (preg_match('/notemodif4\.php/',$_SERVER['REQUEST_URI'])) { 
					foreach($_POST['note_id'] as &$check){ $check = mysql_real_escape_string($check);  }
					foreach($_POST['elev_id'] as &$check){ $check = mysql_real_escape_string($check);  }
					foreach($_POST['elev_nom'] as &$check){ $check = mysql_real_escape_string($check);  }
					foreach($_POST['iNotes'] as &$check){ $check = mysql_real_escape_string($check);  }
					$_POST["sMat"]=mysql_real_escape_string($_POST["sMat"]);
				}elseif (preg_match('/modifiernotes4\.php/',$_SERVER['REQUEST_URI'])) {
			                foreach($_POST['note_id'] as &$check){ $check = mysql_real_escape_string($check);  }
               				foreach($_POST['elev_id'] as &$check){ $check = mysql_real_escape_string($check);  }
                    			foreach($_POST['elev_nom'] as &$check){ $check = mysql_real_escape_string($check);  }
                    			foreach($_POST['iNotes'] as &$check){ $check = mysql_real_escape_string($check);  }
                    			$_POST["sMat"]=mysql_real_escape_string($_POST["sMat"]);		
				}elseif (preg_match('/notevisu3\.php/',$_SERVER['REQUEST_URI'])) {
					$_GET["libel"]=mysql_real_escape_string($_GET["libel"]);
					$_GET["sujet"]=mysql_real_escape_string($_GET["sujet"]);
					foreach($_GET['args'] as &$check){ $check = mysql_real_escape_string($check);  }
				}elseif (preg_match('/notesupp3\.php/',$_SERVER['REQUEST_URI'])) {
					$_GET["libel"]=mysql_real_escape_string($_GET["libel"]);
					$_GET["sujet"]=mysql_real_escape_string($_GET["sujet"]);
					foreach($_GET['args'] as &$check){ $check = mysql_real_escape_string($check);  }
				}elseif (preg_match('/supprimernotes3\.php/',$_SERVER['REQUEST_URI'])) {
					$_GET["libel"]=mysql_real_escape_string($_GET["libel"]);
					$_GET["sujet"]=mysql_real_escape_string($_GET["sujet"]);
					foreach($_GET['args'] as &$check){ $check = mysql_real_escape_string($check);  }
				}elseif (preg_match('/gestion_abs_retard_du_jour_misaj2\.php/',$_SERVER['REQUEST_URI'])) {
					// rien
				}elseif (preg_match('/creat_groupe_suite\.php/',$_SERVER['REQUEST_URI'])) {
					// rien
				}elseif (preg_match('/profpcombulletin3\.php/',$_SERVER['REQUEST_URI'])) {			
					$_GET["saisie_trimestre"]=mysql_real_escape_string($_GET["saisie_trimestre"]);
					$_GET["saisie_classe"]=mysql_real_escape_string($_GET["saisie_classe"]);
					$_GET["saisie_nb"]=mysql_real_escape_string($_GET["saisie_nb"]);
					$_GET["type_bulletin"]=mysql_real_escape_string($_GET["type_bulletin"]);
					foreach($_GET['args'] as &$check){ $check = mysql_real_escape_string($check);  }
			 	}elseif (preg_match('/export_eleve_2\.php/',$_SERVER['REQUEST_URI'])) {
                                        // rien
                                }elseif (preg_match('/export_eleve_3\.php/',$_SERVER['REQUEST_URI'])) {
                                        // rien
				}elseif (preg_match('/base_de_donne_importation31\.php/',$_SERVER['REQUEST_URI'])) {

				}elseif (preg_match('/base_de_donne_importation32\.php/',$_SERVER['REQUEST_URI'])) {

				}elseif (preg_match('/sms-mess-classe2\.php/',$_SERVER['REQUEST_URI'])) {
					$_POST["message"]=mysql_real_escape_string($_POST["message"]);
				}elseif (preg_match('/edit_event\.php/',$_SERVER['REQUEST_URI'])) {
                                                $_POST["eventDescription"]=mysqli_real_escape_string($_POST["eventDescription"]);
                                                foreach($_POST['jours'] as &$check){ $check = mysql_real_escape_string($check);  }
				}else{
					$_GET = @array_map('trim', $_GET);
                                        $_POST = @array_map('trim', $_POST);
                                        $_COOKIE = @array_map('trim', $_COOKIE);
                                        $_REQUEST = @array_map('trim', $_REQUEST);
                                        foreach($_GET as $key=>$value){ $_GET[$key] = $cnx->escapeSimple($value);  }
                                        foreach($_POST as $key=>$value){ $_POST[$key] = $cnx->escapeSimple($value);  }
                                        foreach($_COOKIE as $key=>$value){ $_COOKIE[$key] = $cnx->escapeSimple($value);  }
                                        foreach($_REQUEST as $key=>$value){ $_REQUEST[$key] = $cnx->escapeSimple($value);  }

				}
			}
			return $cnx;
		}
	}
}


function cnx() {
	global $dsn;
	global $prefixe;
	
	if (!$cnx) { 
		$cnx = DB::connect($dsn);
		if(DB::isError($cnx)){
			//exit($cnx->getMessage());
		}else{
			if ((! @file_exists("./data/install_log/noaccess.inc")) && (! @file_exists("../../../common/stop.triade"))) {
				if (!get_magic_quotes_gpc()) {
					if (preg_match('/demdst2\.php/',$_SERVER['REQUEST_URI'])) { 
						foreach($_POST['saisie_acc'] as &$check){ $check = $cnx->escapeSimple($check);  }	
					}elseif (preg_match('/noteajout3\.php/',$_SERVER['REQUEST_URI'])) { 
						foreach($_POST['elev_id'] as &$check){ $check = $cnx->escapeSimple($check);  }
						foreach($_POST['elev_nom'] as &$check){ $check = $cnx->escapeSimple($check);  }
					}elseif (preg_match('/ajoutnotes3\.php/',$_SERVER['REQUEST_URI'])) { 
						foreach($_POST['elev_id'] as &$check){ $check = $cnx->escapeSimple($check);  }
						foreach($_POST['elev_nom'] as &$check){ $check = $cnx->escapeSimple($check);  }
					}elseif (preg_match('/notemodif4\.php/',$_SERVER['REQUEST_URI'])) { 
						foreach($_POST['note_id'] as &$check){ $check = $cnx->escapeSimple($check);  }
						foreach($_POST['elev_id'] as &$check){ $check = $cnx->escapeSimple($check);  }
						foreach($_POST['elev_nom'] as &$check){ $check = $cnx->escapeSimple($check);  }
						foreach($_POST['iNotes'] as &$check){ $check = $cnx->escapeSimple($check);  }
						$_POST["sMat"]=$cnx->escapeSimple($_POST["sMat"]);
					}elseif (preg_match('/modifiernotes4\.php/',$_SERVER['REQUEST_URI'])) {
	                               	         foreach($_POST['note_id'] as &$check){ $check = $cnx->escapeSimple($check);  }
        	                       	         foreach($_POST['elev_id'] as &$check){ $check = $cnx->escapeSimple($check);  }
                	               	         foreach($_POST['elev_nom'] as &$check){ $check = $cnx->escapeSimple($check);  }
                        	       	         foreach($_POST['iNotes'] as &$check){ $check = $cnx->escapeSimple($check);  }
                               		         $_POST["sMat"]=mysql_real_escape_string($_POST["sMat"]);
					}elseif (preg_match('/modifiernotes3\.php/',$_SERVER['REQUEST_URI'])) {
						$_GET["libel"]=$cnx->escapeSimple($_GET["libel"]);
						$_GET["sujet"]=$cnx->escapeSimple($_GET["sujet"]);
						foreach($_GET['args'] as &$check){ $check = $cnx->escapeSimple($check);  }
					}elseif (preg_match('/notesupp3\.php/',$_SERVER['REQUEST_URI'])) {
						$_GET["libel"]=$cnx->escapeSimple($_GET["libel"]);
						$_GET["sujet"]=$cnx->escapeSimple($_GET["sujet"]);
						foreach($_GET['args'] as &$check){ $check = $cnx->escapeSimple($check);  }
					}elseif (preg_match('/supprimernotes3\.php/',$_SERVER['REQUEST_URI'])) {
						$_GET["libel"]=$cnx->escapeSimple($_GET["libel"]);
						$_GET["sujet"]=$cnx->escapeSimple($_GET["sujet"]);
						foreach($_GET['args'] as &$check){ $check = $cnx->escapeSimple($check);  }
					}elseif (preg_match('/profpcombulletin3\.php/',$_SERVER['REQUEST_URI'])) {
						$_GET["saisie_trimestre"]=$cnx->escapeSimple($_GET["saisie_trimestre"]);
							$_GET["saisie_classe"]=$cnx->escapeSimple($_GET["saisie_classe"]);
						$_GET["saisie_nb"]=$cnx->escapeSimple($_GET["saisie_nb"]);
						$_GET["type_bulletin"]=$cnx->escapeSimple($_GET["type_bulletin"]);
						foreach($_GET['args'] as &$check){ $check = $cnx->escapeSimple($check);  }
 					}elseif	 (preg_match('/listepreinscription\.php/',$_SERVER['REQUEST_URI'])) {
						foreach($_POST['listing'] as &$check){ $check = $cnx->escapeSimple($check);  }
						
 					}elseif	 (preg_match('/export_eleve_2\.php/',$_SERVER['REQUEST_URI'])) {
                        	               	 // rien
                			}elseif (preg_match('/export_eleve_3\.php/',$_SERVER['REQUEST_URI'])) {
                               		         // rien
					}elseif (preg_match('/compta_fiche3\.php/',$_SERVER['REQUEST_URI'])) {
					}elseif (preg_match('/cantine_passage\.php/',$_SERVER['REQUEST_URI'])) {		
					}elseif (preg_match('/sms-mess-classe2\.php/',$_SERVER['REQUEST_URI'])) {
						$_POST["message"]=$cnx->escapeSimple($_POST["message"]);
					}elseif (preg_match('/notevisu3\.php/',$_SERVER['REQUEST_URI'])) {
						$_GET["sujet"]=$cnx->escapeSimple($_GET["sujet"]);
						$_GET["libel"]=$cnx->escapeSimple($_GET["libel"]);
					}elseif (preg_match('/base_de_donne_importation31\.php/',$_SERVER['REQUEST_URI'])) {			
					}elseif (preg_match('/base_de_donne_importation32\.php/',$_SERVER['REQUEST_URI'])) {	
					}elseif (preg_match('/liste_abs_impr2\.php/',$_SERVER['REQUEST_URI'])) {
					}elseif (preg_match('/gestion_abs_config_alerte\.php/',$_SERVER['REQUEST_URI'])) {
					}elseif (preg_match('/edit_event\.php/',$_SERVER['REQUEST_URI'])) {
                                                $_POST["eventDescription"]=$cnx->escapeSimple($_POST["eventDescription"]);
                                                foreach($_POST['jours'] as &$check){ $check = $cnx->escapeSimple($check);  }
					}elseif (preg_match('/news_actualite\.php/',$_SERVER['REQUEST_URI'])) {
						
					}elseif (preg_match('/gestion_delegue_impr\.php/',$_SERVER['REQUEST_URI'])) {
					}elseif (preg_match('/reglement_ajout2\.php/',$_SERVER['REQUEST_URI'])) {
						$_POST["saisie_titre"]=$cnx->escapeSimple($_POSt["saisie_titre"]);
						$_POST["saisie_ref"]=$cnx->escapeSimple($_POSt["saisie_ref"]);
					}elseif (preg_match('/regime_affectation2\.php/',$_SERVER['REQUEST_URI'])) {
					}elseif (preg_match('/sms-envoi\.php/',$_SERVER['REQUEST_URI'])) {
					}elseif (preg_match('/creat_groupe_suite\.php/',$_SERVER['REQUEST_URI'])) {
					}elseif (preg_match('/stockage-partage-fichier\.php/',$_SERVER['REQUEST_URI'])) {
					}elseif (preg_match('/export_personnel_3\.php/',$_SERVER['REQUEST_URI'])) {
					}elseif (preg_match('/export_personnel_2\.php/',$_SERVER['REQUEST_URI'])) {
					}else{
						$_GET = @array_map('trim', $_GET);
                                        	$_POST = @array_map('trim', $_POST);
	                                        $_COOKIE = @array_map('trim', $_COOKIE);
	                                        $_REQUEST = @array_map('trim', $_REQUEST);
	                                        foreach($_GET as $key=>$value){ $_GET[$key] = $cnx->escapeSimple($value);  }
	                                        foreach($_POST as $key=>$value){ $_POST[$key] = $cnx->escapeSimple($value);  }
	                                        foreach($_COOKIE as $key=>$value){ $_COOKIE[$key] = $cnx->escapeSimple($value);  }
	                                        foreach($_REQUEST as $key=>$value){ $_REQUEST[$key] = $cnx->escapeSimple($value);  }
					}
				}
				return $cnx;
			}
		}
	}else{
		return $cnx;
	}
}

function execSql($sql) {
	global $cnx;
	global $ERROR;
	global $prefixe;
	if (trim($sql) == "") { return; } 
	$res = $cnx->query($sql);
	
	if(DB::isError($res)) {
		if (($res->getMessage() != "DB Error: already exists") && 
			($res->getMessage() != "DB Error: unknown error") && 
			($res->getMessage() != "DB Error: no database selected") 
		//	($res->getMessage() != "DB Error: no such table") 
		) {

			$fichier="./data/erreurs.log";
			if (file_exists("./data/erreurs.log")) { $fichier="./data/erreurs.log"; }
			if (file_exists("../data/erreurs.log")) { $fichier="../data/erreurs.log"; }
			if (file_exists("../../data/erreurs.log")) { $fichier="../../data/erreurs.log"; }
       	    $texte  = dateDMY()." à ".dateHIS();
			$texte .= "<br>Base de type : " . DBTYPE ;
       	    $texte .= "<br>Fichier : <b>".$_SERVER['PHP_SELF']."</b><br />\n";
       	    $texte .= "Notice sur la ligne :<br>";
       	    $texte .= "<i>$sql</i><br>";
			$texte .= $res->getMessage()."<br>";
			$texte .= "<hr><br>";
       	    $fichier=fopen($fichier,"a");
       	    fwrite($fichier,$texte);
			fclose($fichier);
		}
		if (file_exists("./data/parametrage/analyse.triade")) {  // activation du l'analyse de triade 
			turnOverLog("./data/parametrage/analyse.log",100000000); // 100Mo
			$fichier=fopen("./data/parametrage/analyse.log","a");
       	    fwrite($fichier,"- ".dateDMY()." à ".dateHIS()."ERROR : ".$_SERVER['PHP_SELF']." -> ".$sql."\n");
       	    fclose($fichier);
		}
		if (file_exists("../data/parametrage/analyse.triade")) {  // activation du l'analyse de triade 
			turnOverLog("../data/parametrage/analyse.log",100000000); // 100Mo
			$fichier=fopen("../data/parametrage/analyse.log","a");
       	    fwrite($fichier,"- ".dateDMY()." à ".dateHIS()."ERROR : ".$_SERVER['PHP_SELF']." -> ".$sql."\n");
           	fclose($fichier);
		}
		if ($ERROR == "true")  {
          	print("<font color='red'><b>$sql</b></font><br><br>");
	    	print $res->getMessage();
	    }
		Pgclose();
	}else {
		if (file_exists("./data/parametrage/analyse.triade")) { // activation du l'analyse de triade 
			turnOverLog("./data/parametrage/analyse.log",100000000); // 100Mo
                        $fichier=fopen("./data/parametrage/analyse.log","a");
                        fwrite($fichier,"- ".dateDMY()." à ".dateHIS()." -- ".$_SERVER['PHP_SELF']." -- ".$sql."\n");
                        fclose($fichier);
                }
		if (file_exists("../data/parametrage/analyse.triade")) { // activation du l'analyse de triade 
			turnOverLog("../data/parametrage/analyse.log",100000000); // 100Mo
                        $fichier=fopen("../data/parametrage/analyse.log","a");
                        fwrite($fichier,"- ".dateDMY()." à ".dateHIS()." -- ".$_SERVER['PHP_SELF']." -- ".$sql."\n");
                        fclose($fichier);
        }
		return $res;
	}
}

function Pgclose(){
	global $cnx;
	global $prefixe;
	$close=$cnx->disconnect();
	if(DB::isError($close))
	{
//		exit($close->getMessage());
	}
	else
	{
		return(true);
	}
}


/**
* Libérer un résultat sql
*
* @param object Objet de type ResultSet
* @return bool
*/
function freeResult($resultSet) {
	return($resultSet->free());
}
#----------------------------------------------------------------

if ($_SESSION["langue"] == "fr" ) {
$MOIS=array(
	'',
	'Janvier',
	'Février',
	'Mars',
	'Avril',
	'Mai',
	'Juin',
	'Juillet',
	'Août',
	'Septembre',
	'Octobre',
	'Novembre',
	'Décembre'
);
}

if ($_SESSION["langue"] == "en" ) {
$MOIS=array(
	'',
	'January',
	'February',
	'March',
	'April',
	'May',
	'June',
	'July',
	'August',
	'September',
	'October',
	'November',
	'December'
);
}

if ($_SESSION["langue"] == "es" ) {
$MOIS=array(
	'',
	'Enero',
	'Febrero',
	'Marzo',
	'Abril',
	'Mayo',
	'Junio',
	'Julio',
	'Agosto',
	'Septiembre',
	'Octubre',
	'Noviembre',
	'Diciembre'
);
}

if ($_SESSION["langue"] == "it" ) {
$MOIS=array(
	'',
	'Gennaio',
	'Febbraio',
	'Marzo',
	'Aprile',
	'Maggio',
	'Giugno',
	'Luglio',
	'Agosto',
	'Settembre',
	'Ottobre',
	'Novembre',
	'Dicembre'
);
}

if ($_SESSION["langue"] == "arabe" ) {
$MOIS=array(
	'',
	'Ø¬Ø§Ù†ÙÙŠ',
	'ÙÙŠÙØ±ÙŠ',
	'Ù…Ø§Ø±Ø³',
	'Ø£ÙØ±ÙŠÙ„',
	'Ù…Ø§ÙŠ',
	'Ø¬ÙˆØ§Ù†',
	'Ø¬ÙˆÙŠÙ„ÙŠØ©',
	'Ø£ÙˆØª',
	'Ø³Ø¨ØªÙ…Ø¨Ø±',
	'Ø£ÙƒØªÙˆØ¨Ø±',
	'Ù†ÙˆÙÙ…Ø¨Ø±',
	'Ø¯ÙŠØ³Ù…Ø¨Ø±'
);
}

if ($_SESSION["langue"] == "bret" ) {
$MOIS=array(
	'',
	'Genver',
	'C&#146;hwevrer',
	'Meurzh',
	'Ebrel',
	'Mae',
	'Even',
	'Gouhere',
	'Eost',
	'Gwengolo',
	'Here',
	'Du',
	'Kerzu'
);
}

/*-------------------------------------------------------------------------------------*/
/* 
 * Coder un texte pour le WAP
 * Posté par Jerome Neuveglise
 * Cette fonction permet de coder un texte afin que les caractères spéciaux soient
 * lisibles sur un écran de portable WAP. Par exemple, un é doit se voit coder en
 * &#233;
*/

function spec_chars($chaine) {
  	for ($i = 161; $i < 255; $i++)
	$item=chr($i);
  	$chaine = preg_replace("/$item/", "&#$i;", $chaine);
  	return $chaine;
}

/*-------------------------------------------------------------------------------------*/
/*
 * Recherche approximative
 * Posté par Ben
 * Si vous voulez donner un peu plus de fonctionnalités à votre moteur de
 * recherche, proposez aux utilisateurs la méthode approximative.
 * Il suffit de transformer chaque mot à chercher de la manière suivante :
 * "phpinfo" devient "p%h%p%i%n%f%o".
 * Donc votre requête devient :
 * SELECT * FROM liens WHERE url LIKE '%p%h%p%i%n%f%o%'
 * Explications : si la colonne url de la table liens contient la valeur
 * "http://www.phpinfo.net", une recherche de "pnfo" trouvera cette valeur, etc...
 * Une fonction pour transformer chaque 'token' :
 /*--------------------------------------------------------------------------------------*/

function approx_sql($token) {
	for ($cpt = 0; $cpt < strlen($token); $cpt++)
    	$token_tabl[]=$token[$cpt];
  	return implode("%", $token_tabl);
}
 /*--------------------------------------------------------------------------------------*/

function accent_import($chaine)	{
	$chaine=str_replace("oe","[#339]",$chaine);
	$chaine=str_replace("à","[agrave]",$chaine);
	$chaine=str_replace("é","[eacute]",$chaine);
	$chaine=str_replace("è","[egrave]",$chaine);
	$chaine=str_replace("ù","[ugrave]",$chaine);
	$chaine=str_replace("â","[acirc]",$chaine);
	$chaine=str_replace("ê","[ecirc]",$chaine);
	$chaine=str_replace("î","[icirc]",$chaine);
	$chaine=str_replace("ô","[ocirc]",$chaine);
	$chaine=str_replace("û","[ucirc]",$chaine);
	$chaine=str_replace("ä","[auml]",$chaine);
	$chaine=str_replace("ë","[euml]",$chaine);
	$chaine=str_replace("ï","[iuml]",$chaine);
	$chaine=str_replace("ö","[ouml]",$chaine);
	$chaine=str_replace("ü","[uuml]",$chaine);
	$chaine=str_replace("È","[Egrave]",$chaine);
	$chaine=str_replace("É","[Eacute]",$chaine);
	$chaine=str_replace("Ê","[Ecirc]",$chaine);
	$chaine=str_replace("Ç","[Ccedil]",$chaine);
	$chaine=str_replace("'","[Quote]",$chaine);
	$chaine=str_replace("`","[ByQuote]",$chaine);
	$chaine=str_replace('"',"[DblQuote]",$chaine);
	$chaine=str_replace('’',"[Quote2]",$chaine);
	$chaine=str_replace('ç',"[ccdile]",$chaine);

	
	$chaine=str_replace('ó',"[ocute]",$chaine);
	$chaine=str_replace('á',"[acute]",$chaine);
	$chaine=str_replace('ñ',"[ntild]",$chaine);
	$chaine=str_replace('í',"[icute]",$chaine);
	$chaine=str_replace('ú',"[ucute]",$chaine);
	$chaine=str_replace('Ñ',"[Ntild]",$chaine);
	$chaine=str_replace('<',"[stdin]",$chaine);
	$chaine=str_replace('>',"[stdout]",$chaine);

	return $chaine;
}

function accent_export($chaine)	{
	$chaine=str_replace("[#339]","oe",$chaine);
	$chaine=str_replace("[agrave]","à",$chaine);
	$chaine=str_replace("[eacute]","é",$chaine);
	$chaine=str_replace("[egrave]","è",$chaine);
	$chaine=str_replace("[ugrave]","ù",$chaine);
	$chaine=str_replace("[acirc]","â",$chaine);
	$chaine=str_replace("[ecirc]","ê",$chaine);
	$chaine=str_replace("[icirc]","î",$chaine);
	$chaine=str_replace("[ocirc]","ô",$chaine);
	$chaine=str_replace("[ucirc]","û",$chaine);
	$chaine=str_replace("[auml]","ä",$chaine);
	$chaine=str_replace("[euml]","ë",$chaine);
	$chaine=str_replace("[iuml]","ï",$chaine);
	$chaine=str_replace("[ouml]","ö",$chaine);
	$chaine=str_replace("[uuml]","ü",$chaine);
	$chaine=str_replace("[Egrave]","È",$chaine);
	$chaine=str_replace("[Eacute]","É",$chaine);
	$chaine=str_replace("[Ecirc]","Ê",$chaine);
	$chaine=str_replace("[Ccedil]","Ç",$chaine);
	$chaine=str_replace("[Quote]","'",$chaine);
	$chaine=str_replace("[ByQuote]","`",$chaine);
	$chaine=str_replace("[DblQuote]",'"',$chaine);
	$chaine=str_replace("[Quote2]",'’',$chaine);
	$chaine=str_replace("[ccdile]",'ç',$chaine);
	$chaine=str_replace("[ocute]",'ó',$chaine);
	$chaine=str_replace("[acute]",'á',$chaine);
	$chaine=str_replace("[ntild]",'ñ',$chaine);
	$chaine=str_replace("[icute]",'í',$chaine);
	$chaine=str_replace("[ucute]",'ú',$chaine);
	$chaine=str_replace("[Ntild]",'Ñ',$chaine);
	$chaine=str_replace("[stdin]",'<',$chaine);
	$chaine=str_replace("[stdout]",'>',$chaine);

	return $chaine;
}


function txt_vers_html($chaine)	{
	$chaine=str_replace("'","&#8216;",$chaine);
	$chaine=str_replace("oe","&#339;",$chaine);
	$chaine=str_replace("'","&#8217;",$chaine);
	$chaine=str_replace("...","&#8230;",$chaine);
	$chaine=str_replace("&","&amp;",$chaine);
	$chaine=str_replace("<","&lt;",$chaine);
	$chaine=str_replace(">","&gt;",$chaine);
	$chaine=str_replace("\"","&quot;",$chaine);
	$chaine=str_replace("à","&agrave;",$chaine);
	$chaine=str_replace("é","&eacute;",$chaine);
	$chaine=str_replace("è","&egrave;",$chaine);
	$chaine=str_replace("ù","&ugrave;",$chaine);
	$chaine=str_replace("â","&acirc;",$chaine);
	$chaine=str_replace("ê","&ecirc;",$chaine);
	$chaine=str_replace("î","&icirc;",$chaine);
	$chaine=str_replace("ô","&ocirc;",$chaine);
	$chaine=str_replace("û","&ucirc;",$chaine);
	$chaine=str_replace("ä","&auml;",$chaine);
	$chaine=str_replace("ë","&euml;",$chaine);
	$chaine=str_replace("ï","&iuml;",$chaine);
	$chaine=str_replace("ö","&ouml;",$chaine);
	$chaine=str_replace("ü","&uuml;",$chaine);
	$chaine=str_replace("È","&Egrave;",$chaine);
	$chaine=str_replace("É","&Eacute;",$chaine);
	$chaine=str_replace("Ê","&Ecirc;",$chaine);
	$chaine=str_replace("Ç","&Ccedil;",$chaine);
	$chaine=str_replace("ç","&ccedil;",$chaine);
	$chaine=str_replace(" ","&nbsp;",$chaine);
	return $chaine;
}


function txt_vers_html_sans_quote($chaine) {
        $chaine=str_replace("&","&amp;",$chaine);
        $chaine=str_replace("<","&lt;",$chaine);
        $chaine=str_replace(">","&gt;",$chaine);
        $chaine=str_replace("\"","&quot;",$chaine);
        $chaine=str_replace("à","&agrave;",$chaine);
        $chaine=str_replace("é","&eacute;",$chaine);
        $chaine=str_replace("è","&egrave;",$chaine);
        $chaine=str_replace("ù","&ugrave;",$chaine);
        $chaine=str_replace("â","&acirc;",$chaine);
        $chaine=str_replace("ê","&ecirc;",$chaine);
        $chaine=str_replace("î","&icirc;",$chaine);
        $chaine=str_replace("ô","&ocirc;",$chaine);
        $chaine=str_replace("û","&ucirc;",$chaine);
        $chaine=str_replace("ä","&auml;",$chaine);
        $chaine=str_replace("ë","&euml;",$chaine);
        $chaine=str_replace("ï","&iuml;",$chaine);
        $chaine=str_replace("ö","&ouml;",$chaine);
        $chaine=str_replace("ü","&uuml;",$chaine);
        $chaine=str_replace("È","&Egrave;",$chaine);
        $chaine=str_replace("É","&Eacute;",$chaine);
        $chaine=str_replace("Ê","&Ecirc;",$chaine);
        $chaine=str_replace("Ç","&Ccedil;",$chaine);
        $chaine=str_replace("ç","&ccedil;",$chaine);
        return $chaine;
}


function html_quotes($chaine) {
	$chaine=str_replace('"',"&quot;",$chaine);
	$chaine=str_replace("'","&#8217;",$chaine);
        $chaine=preg_replace('/\r\n/',"<br />",$chaine);
	return $chaine;
}

function quotes_spec($chaine) {
        $chaine=str_replace("&#8217;","&quot;",$chaine);
        return $chaine;
}


function quote_input($chaine) {
	$chaine=str_replace('"','&quot;',$chaine);
	$chaine=str_replace("'","&#8217;",$chaine);
	return $chaine;
}


function html_vers_text($chaine)	{
	$chaine=str_replace("&#8216;","'",$chaine);
	$chaine=str_replace("&#8221;",'"',$chaine);
	$chaine=str_replace("&#339;","oe",$chaine);
	$chaine=str_replace("&#8217;","'",$chaine);
	$chaine=str_replace("&#8230;","...",$chaine);
	$chaine=str_replace("&amp;","&",$chaine);
	$chaine=str_replace("&lt;","<",$chaine);
	$chaine=str_replace("&gt;",">",$chaine);
	$chaine=str_replace("&quot;","\"",$chaine);
	$chaine=str_replace("&agrave;","à",$chaine);
	$chaine=str_replace("&eacute;","é",$chaine);
	$chaine=str_replace("&egrave;","è",$chaine);
	$chaine=str_replace("&ugrave;","ù",$chaine);
	$chaine=str_replace("&acirc;","â",$chaine);
	$chaine=str_replace("&ecirc;","ê",$chaine);
	$chaine=str_replace("&icirc;","î",$chaine);
	$chaine=str_replace("&ocirc;","ô",$chaine);
	$chaine=str_replace("&ucirc;","û",$chaine);
	$chaine=str_replace("&auml;","ä",$chaine);
	$chaine=str_replace("&euml;","ë",$chaine);
	$chaine=str_replace("&iuml;","ï",$chaine);
	$chaine=str_replace("&ouml;","ö",$chaine);
	$chaine=str_replace("&uuml;","ü",$chaine);
	$chaine=str_replace("&nbsp;"," ",$chaine);
	$chaine=str_replace("&ccedil;","ç",$chaine); 
	$chaine=str_replace("&deg;","°",$chaine); 
	$chaine=str_replace("&#65533;",'"',$chaine);
    $chaine=str_replace("&laquo;",'"',$chaine);
    $chaine=str_replace("&raquo;",'"',$chaine);
    $chaine=str_replace("&hellip;",'',$chaine);
    $chaine=str_replace("&ndash;",'-',$chaine);
    $chaine=str_replace("&Egrave;",'E',$chaine);
    $chaine=str_replace("&Eacute;",'E',$chaine);
	$chaine=str_replace("&sup2;",'²',$chaine);

	return $chaine;
}

function ValideMail($email) {
    if ($email == "") { return 0; }
         // $mail_valide =  preg_match('/([A-Za-z0-9]|-|_|\.)*@([A-Za-z0-9]|-|_|\.)*\.([A-Za-z0-9]|-|_|\.)*/',$email);
    if (preg_match('#^[\w.-]+@[\w.-]+\.[a-z]{2,6}$#i', $email)) {
	    return 1;
    }else{
	 // alertJs("ATTENTION, votre email : $email n'est pas valide. \\n \\n Equipe Triade.");
	    return 0; 
    }
}  

// function de relad de page
function reload_page($page) {
	print "<script>location.href='".$page."';</script>";
}

function jourdesemaine($id) {
switch($id) {
        case 1 :
        return LANGLUNDI;
        break;
        case 2:
        return LANGMARDI;
        break;
        case 3:
        return LANGMERCREDI;
        break;
        case 4:
        return LANGJEUDI;
        break;
        case 5:
        return LANGVENDREDI;
        break;
        case 6:
        return LANGSAMEDI;
        break;
        case 7:
        return LANGDIMANCHE;
        break;
        }
}


// chargement du résultat d une requête SQL dans une matrice (tableau bi-dimensionnel)
function chargeMat($res) {
	$mat=null;
	$c = $res->numCols();
	$l = $res->numRows();
	for($i=0;$i<$l;$i++)
	{
		$ligne = & $res->fetchRow();
		for($j=0;$j<$c;$j++)
		{
			$ligne[$j]=MyStripSlashes($ligne[$j]);
			$mat[$i][$j] = $ligne[$j];
		}
	}
    	freeResult($res);
	return $mat;
}


function MyStripSlashes($chaine) {
        return( get_magic_quotes_gpc() == 1 ? stripslashes($chaine) : $chaine);
}


// chargement du résultat d une requête SQL dans une matrice (tableau)
function chargeMat2($res) {
	$c = $res->numCols();
	$l = $res->numRows();
	for($i=0;$i<$l;$i++)
	{
		$ligne = & $res->fetchRow();
		$mat[$i] = $ligne[0];
	}
	freeResult($res);
	return $mat;
}


function create_personnel_via_admin($nom,$pren,$mdp,$tp,$civ,$pren2='') {
	global $cnx;
	global $prefixe;

	include_once("./common/config2.inc.php");
	$sql="SELECT nom,prenom FROM ${prefixe}personnel WHERE nom='$nom' AND prenom='$pren' AND  type_pers='ADM' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return "-1";
	}

	if ((empty($nom)) || (empty($pren)) || (empty($mdp))) {
		return false ;
	}

	if (SECURITE == 3) {
		if ( (strlen($mdp) < 8) || (!preg_match('/[a-z]/',$mdp)) || (!preg_match('/[A-Z]/',$mdp)) || (!preg_match('/[0-9]/',$mdp)) ) {
			return false ;
		}
	}

	if (SECURITE == 2) {
		if ( (strlen($mdp) < 8) || (!preg_match('/[a-z]/',$mdp)) || (!preg_match('/[0-9]/',$mdp)) ) {
			return false ;
		}
	}

	if (SECURITE == 1) {
		if (strlen($mdp) < 4) {
			return false ;
		}
	}

	$mdp=cryptage($mdp);
	$sql="INSERT INTO ${prefixe}personnel(nom,prenom,prenom2,mdp,type_pers,civ) VALUES ('$nom','$pren','$pren2','$mdp','$tp','$civ')";
	return(execSql($sql));
}

//-------------------------------------------------------------------------//
function create_personnel($nom,$pren,$mdp,$tp,$civ,$pren2='',$adr,$codepostal,$tel,$mail,$commune,$telportable="",$idsociete=0,$pays="",$indicesalaire="",$qualite="") {
	global $cnx;
	global $prefixe;
	include_once("./common/config2.inc.php");
	$sql="SELECT nom,prenom FROM ${prefixe}personnel WHERE nom='$nom' AND prenom='$pren' AND type_pers= '$tp'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return -1;
	}
	if (VERIFPASS == "oui") {
		if (SECURITE == 3) {
			if ( (strlen($mdp) < 8) || (!preg_match('/[a-z]/',$mdp)) || (!preg_match('/[A-Z]/',$mdp)) || (!preg_match('/[0-9]/',$mdp)) ) {
				return -3 ;
			}
		}
		if (SECURITE == 2) {
			if ( (strlen($mdp) < 8) || (!preg_match('/[a-z]/',$mdp)) || (!preg_match('/[0-9]/',$mdp)) ) {
				return -3 ;
			}
		}
		if (SECURITE == 1) {
			if (strlen($mdp) < 4) {
				return -3 ;
			}
		}
	}
	$mdp=cryptage($mdp);
	if ((MAILMESS == "oui") && (trim($mail) != "")){
		if (DBTYPE == "pgsql") {
			$valid="TRUE";
		}
		if (DBTYPE == "mysql")  {
			$valid=1;
		}
	}else {
		if (DBTYPE == "pgsql") {
			$valid="FALSE";
		}
		if (DBTYPE == "mysql")  {
			$valid=0;
		}
	}
	if (empty($indicesalaire)) $indicesalaire=0;
	$sql="INSERT INTO ${prefixe}personnel(nom,prenom,prenom2,mdp,type_pers,civ,adr,code_post,commune,tel,email,valid_forward_mail,tel_port,id_societe_tuteur,pays,indice_salaire,qualite) VALUES ('$nom','$pren','$pren2','$mdp','$tp','$civ','$adr','$codepostal','$commune','$tel','$mail','$valid','$telportable','$idsociete','$pays','$indicesalaire','$qualite')";
	return(execSql($sql));
}


function cryptage($mdp) {
	global $gestionMDP;
	if ($gestionMDP == "MD5") { 
		$mdp=md5($mdp); 
	}else{
		$mdp=crypt(md5($mdp),"T2");
	}
	return $mdp;
}


function create_personnel_prof($nom,$pren,$mdp,$tp,$civ,$pren2='',$adr,$codepostal,$tel,$mail,$commune,$telportable,$identifiant,$indicesalaire) {
	global $cnx;
	global $prefixe;
	include_once("./common/config2.inc.php");

	$sql="SELECT nom,prenom FROM ${prefixe}personnel WHERE nom='$nom' AND prenom='$pren' AND type_pers= '$tp'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return -1;
	}

	if (VERIFPASS == "oui") {

		if (SECURITE == 3) {
			if ( (strlen($mdp) < 8) || (!preg_match('/[a-z]/',$mdp)) || (!preg_match('/[A-Z]/',$mdp)) || (!preg_match('/[0-9]/',$mdp)) ) {
				return -3 ;
			}
		}

		if (SECURITE == 2) {
			if ( (strlen($mdp) < 8) || (!preg_match('/[a-z]/',$mdp)) || (!preg_match('/[0-9]/',$mdp)) ) {
				return -3 ;
			}
		}

		if (SECURITE == 1) {
			if (strlen($mdp) < 4) {
				return -3 ;
			}
		}
	}

	$mdp=cryptage($mdp);

	if ((MAILMESS == "oui") && (trim($mail) != "")){
		if (DBTYPE == "pgsql") {
			$valid="TRUE";
		}
		if (DBTYPE == "mysql")  {
			$valid=1;
		}
	}else {
		if (DBTYPE == "pgsql") {
			$valid="FALSE";
		}
		if (DBTYPE == "mysql")  {
			$valid=0;
		}
	}

	$sql="INSERT INTO ${prefixe}personnel(nom,prenom,prenom2,mdp,type_pers,civ,adr,code_post,commune,tel,email,valid_forward_mail,tel_port,identifiant,indice_salaire) VALUES ('$nom','$pren','$pren2','$mdp','$tp','$civ','$adr','$codepostal','$commune','$tel','$mail','$valid','$telportable','$identifiant','$indicesalaire')";
	return(execSql($sql));
}


//-------------------------------------------------------------------------//
function trunchaine($chaine,$len) {

	if (strlen(trim($chaine)) >= $len) {
     		$chaine = substr($chaine,0,$len) . "..." ;
	}
	return $chaine;
}


function trunchaine2($chaine,$len) {

	if (strlen(trim($chaine)) >= $len) {
     		$chaine = substr($chaine,0,$len) . "" ;
	}
	return $chaine;
}

//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
//

function validerequete2($ses) {
	if ($ses != "suppreme") {
		blacklist();
	}
}


function blacklist() {
	print "<script language='javascript'>";
	$METHOD=$_SERVER["REQUEST_METHOD"];
	$URI=$_SERVER["REQUEST_URI"];
	$REFERER=$_SERVER["HTTP_REFERER"];
	$info="$URI / via $REFERER en mode $METHOD";
	if (DEV == 1) {
	        print "alert('Permission Denied - Code ERROR : 0A03 $info')";
	}elseif (VATEL == 1){
		blacklistVatel();
	}else{
		print "location.href='./blacklist.php?fichier=$info'";
	}
        print "</script>";
        exit;
}

function blacklistVatel() {
	print "<script language='javascript'>";
	if (DEV == 1) {
	        print "alert('Permission Denied - Code ERROR : 0A03 $info')";
	}else{
		print "location.href='./accesdenied.php";
	}
    print "</script>";
    exit;
}

function validerequete($membre) {
	// choix possible du membre : menuadmin menuparent menuprof menuscolaire
	//validerequete("menuadmin");
	//validerequete("menuparent");
	//validerequete("menuprof");
	//validerequete("menuscolaire");
	//validerequete("menututeur");
	//validerequete("2");
	//history_cmd($_SESSION[nom],"","");
	//validerequete("3");
	//validerequete("4"); // menueleve
	//validerequete("profadmin");
	//validerequete("5"); // menueleve et menuparent
	//validerequete("6"); // menueleve et menuparent et menututeur
	//validerequete("7"); // menuadmin menuprof menuscolaire menupersonnel
	//validerequete("8"); // menuadmin menupersonnel
	
	if ($membre == "2") {
		if ((MEMBRE != "menuadmin") && (MEMBRE != "menuscolaire") && (MEMBRE != "menupersonnel")) {
			blacklist();
		}
	}elseif ($membre == "3") {
		if ((MEMBRE != "menuadmin") && (MEMBRE != "menuscolaire") && (MEMBRE != "menuprof")) {
			blacklist();
		}
	}elseif ($membre == "4") {
		if (MEMBRE != "menueleve") {
			blacklist();
		}
	}elseif ($membre == "5") {
		if ((MEMBRE != "menueleve") && (MEMBRE != "menuparent")) {
			blacklist();
		}
	}elseif ($membre == "6") {
		if ((MEMBRE != "menueleve") && (MEMBRE != "menuparent") && (MEMBRE != "menututeur")) {
			blacklist();
		}
	}elseif ($membre == "7") {
		if ((MEMBRE != "menuadmin") && (MEMBRE != "menuscolaire") && (MEMBRE != "menuprof") &&  (MEMBRE != "menupersonnel")) {
			blacklist();
		}
	}elseif ($membre == "8") {
		if ((MEMBRE != "menuadmin") &&  (MEMBRE != "menupersonnel")) {
			blacklist();
		}
	}elseif ($membre == "9") {
		if ((MEMBRE != "menuadmin") &&  (MEMBRE != "menuprof") && (MEMBRE != "menueleve") && (MEMBRE != "menuparent")  && (MEMBRE != "menuscolaire") ) {
			blacklist();
		}
	}elseif ($membre == "profadmin") {
		if ((MEMBRE != "menuprof") &&  (MEMBRE != "menuadmin")){
			blacklist();
		}
	}elseif ($membre == "menututeur") {
		if (MEMBRE != "menututeur") {
			blacklist();
		}
	}elseif ($membre == "menupersonnel") {
		if (MEMBRE != "menupersonnel") {
			blacklist();
		}
	}else {
		if (MEMBRE != $membre) {
			blacklist();
		}
	}
}





//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
function create_sanction($libelle,$category) {
        global $cnx;
	global $prefixe;
        $sql="INSERT INTO ${prefixe}type_sanction(libelle,id_category) VALUES ('$libelle','$category')";
 	return(execSql($sql));
}
function create_motif($libelle) {
        global $cnx;
	global $prefixe;
        $sql="INSERT INTO ${prefixe}config_rtd_abs(libelle) VALUES ('$libelle')";
 	return(execSql($sql));
}
//-------------------------------------------------------------------------//

function create_creneau($libelle,$dep_h,$fin_h) {
        global $cnx;
	global $prefixe;
	$libelle=preg_replace('/"/',"'",$libelle);
	$sql="SELECT * FROM ${prefixe}config_creneau WHERE libelle='$libelle'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return false;
	}else{
		$sql="INSERT INTO ${prefixe}config_creneau(libelle,dep_h,fin_h) VALUES ('$libelle','$dep_h','$fin_h')";
 		return(execSql($sql));
	}
}

//-------------------------------------------------------------------------//
function create_devoirscolaire($idclsorgrp,$idmatiere,$date,$pourle,$travail,$clsorgrp,$number,$idprof,$tempsestime,$idgroupe=0) {
	global $cnx;
	global $prefixe;
	$heure=dateHIS();
	$date=dateFormBase($date);
	$datecontenu=dateFormBase($datecontenu);
	$pourle=dateFormBase($pourle);

	$listingClasse='{}';
	if ($clsorgrp > 0) {
		$sql="SELECT liste_elev FROM ${prefixe}groupes WHERE group_id='$idgroupe'";
		$res=execSql($sql);
		$data2=chargeMat($res);
		$liste_eleves=preg_replace('/\{/',"",$data2[0][0]);
                $liste_eleves=preg_replace('/\}/',"",$liste_eleves);
                $listeEleve=preg_split ("/,/", $liste_eleves);

		if (is_array($listeEleve)) {
			foreach ($listeEleve as $valeur) {
				$listingEleve[$valeur]="$valeur";	
			}
		}

		if (is_array($listingEleve)) {
			foreach($listingEleve as $idEleve) {
				$sql="SELECT classe FROM ${prefixe}eleves WHERE elev_id='$idEleve'";
				$res=execSql($sql);
				$data2=chargeMat($res);
				$idclasse=$data2[0][0];
				$listeclasse[$idclasse]=$idclasse;
			}
		}

		if (is_array($listeclasse)) {	
			foreach($listeclasse as $valeur) {
				$listingClasseT.="$valeur,";
			}
		}

		if ($listingClasseT != "") {
			$listingClasseT=preg_replace('/,$/','',$listingClasseT);
			$listingClasse="{".$listingClasseT."}";
		}
	}	

	$sql="SELECT * FROM ${prefixe}devoir_scolaire WHERE id_class_or_grp='$idclsorgrp' AND date_devoir='$pourle' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}devoir_scolaire SET liste_id_classe='$listingClasse',  date_saisie='$date',  heure_saisie='$heure', texte='$travail', tempsestimedevoir='$tempsestime' , number='$number' WHERE id_class_or_grp='$idclsorgrp' AND date_devoir='$pourle' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof'";	
		return(execSql($sql));		
	}else{
		delete_devoirscolaire($pourle,$clsorgrp,$idmatiere,$idclsorgrp,$idprof);
		$date=dateDMY2();
		$sql="INSERT INTO ${prefixe}devoir_scolaire(id_class_or_grp,matiere_id,date_saisie,heure_saisie,date_devoir,texte,classorgrp,number,idprof,tempsestimedevoir,liste_id_classe)VALUES ('$idclsorgrp','$idmatiere','$date','$heure','$pourle','$travail','$clsorgrp','$number','$idprof','$tempsestime','$listingClasse')";
	        return(execSql($sql));
	}
}

function create_cahiertexteContenu($idclsorgrp,$idmatiere,$date,$datecontenu,$clsorgrp,$number,$idprof,$contenu,$idgroupe=0) {
    	global $cnx;
	global $prefixe;
	$heure=dateHIS();
	$date=dateDMY2();
	$datecontenu=dateFormBase($datecontenu);
	$sql="SELECT * FROM ${prefixe}cahiertexte WHERE id_class_or_grp='$idclsorgrp' AND date_contenu='$datecontenu' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof'";
	$res=execSql($sql);
	$data=chargeMat($res);

	$listingClasse='{}';
	if ($clsorgrp > 0) {
		$sql="SELECT liste_elev FROM ${prefixe}groupes WHERE group_id='$idgroupe'";
		$res=execSql($sql);
		$data2=chargeMat($res);
		$liste_eleves=preg_replace('/\{/',"",$data2[0][0]);
                $liste_eleves=preg_replace('/\}/',"",$liste_eleves);
                $listeEleve=preg_split("/,/", $liste_eleves);

		if (is_array($listeEleve)) {
			foreach ($listeEleve as $valeur) {
				$listingEleve[$valeur]="$valeur";	
			}
		}

		if (is_array($listingEleve)) {
			foreach($listingEleve as $idEleve) {
				$sql="SELECT classe FROM ${prefixe}eleves WHERE elev_id='$idEleve'";
				$res=execSql($sql);
				$data2=chargeMat($res);
				$idclasse=$data2[0][0];
				$listeclasse[$idclasse]=$idclasse;
			}
		}

		if (is_array($listeclasse)) {	
			foreach($listeclasse as $valeur) {
				$listingClasseT.="$valeur,";
			}
		}

		if ($listingClasseT != "") {
			$listingClasseT=preg_replace('/,$/','',$listingClasseT);
			$listingClasse="{".$listingClasseT."}";
		}

	}

	fclose($fic);

	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}cahiertexte SET liste_id_classe='$listingClasse' , date_saisie='$date',  heure_saisie='$heure', contenu='$contenu' ,number='$number' WHERE id_class_or_grp='$idclsorgrp' AND date_contenu='$datecontenu' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof'";
		return(execSql($sql));	
	}else{
		delete_cahiertexte($datecontenu,$clsorgrp,$idmatiere,$idclsorgrp,$idprof);
		$date=dateDMY2();
		$sql="INSERT INTO ${prefixe}cahiertexte(id_class_or_grp,matiere_id,date_saisie,heure_saisie,date_contenu,classorgrp,number,idprof,contenu,liste_id_classe) VALUES ('$idclsorgrp','$idmatiere','$date','$heure','$datecontenu','$clsorgrp','$number','$idprof','$contenu','$listingClasse')";
	        return(execSql($sql));
	}
}



function create_cahiertexteBlocNote($idclsorgrp,$idmatiere,$datecontenu,$clsorgrp,$idprof,$contenu) {
    	global $cnx;
	global $prefixe;
	$heure=dateHIS();
	$datecontenu=dateFormBase($datecontenu);
	$sql="SELECT * FROM ${prefixe}cahiertexte WHERE id_class_or_grp='$idclsorgrp' AND date_contenu='$datecontenu' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}cahiertexte SET blocnote='$contenu'  WHERE id_class_or_grp='$idclsorgrp' AND date_contenu='$datecontenu' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof'";
		return(execSql($sql));	
	}else{
		delete_cahiertexte($pourle,$clsorgrp,$idmatiere,$idclsorgrp,$idprof);
		$date=dateDMY2();
		$sql="INSERT INTO ${prefixe}cahiertexte(id_class_or_grp,matiere_id,date_saisie,heure_saisie,date_contenu,classorgrp,idprof,blocnote) VALUES ('$idclsorgrp','$idmatiere','$date','$heure','$datecontenu','$clsorgrp','$idprof','$contenu')";
	        return(execSql($sql));
	}

}

function create_cahiertexteObjectif($idclsorgrp,$idmatiere,$date,$datecontenu,$clsorgrp,$number,$idprof,$objectif,$idgroupe=0) {
    	global $cnx;
	global $prefixe;
	$heure=dateHIS();
	$date=dateDMY2();
	$datecontenu=dateFormBase($datecontenu);
	$sql="SELECT * FROM ${prefixe}cahiertexte WHERE id_class_or_grp='$idclsorgrp' AND date_contenu='$datecontenu' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof'";
	$res=execSql($sql);
	$data=chargeMat($res);

	$listingClasse='{}';
	if ($clsorgrp > 0) {
		$sql="SELECT liste_elev FROM ${prefixe}groupes WHERE group_id='$idgroupe'";
		$res=execSql($sql);
		$data2=chargeMat($res);
		$liste_eleves=preg_replace('/\{/',"",$data2[0][0]);
                $liste_eleves=preg_replace('/\}/',"",$liste_eleves);
                $listeEleve=preg_split ("/,/", $liste_eleves);

		if (is_array($listeEleve)) {
			foreach ($listeEleve as $valeur) {
				$listingEleve[$valeur]="$valeur";	
			}
		}

		if (is_array($listingEleve)) {
			foreach($listingEleve as $idEleve) {
				$sql="SELECT classe FROM ${prefixe}eleves WHERE elev_id='$idEleve'";
				$res=execSql($sql);
				$data2=chargeMat($res);
				$idclasse=$data2[0][0];
				$listeclasse[$idclasse]=$idclasse;
			}
		}

		if (is_array($listeclasse)) {	
			foreach($listeclasse as $valeur) {
				$listingClasseT.="$valeur,";
			}
		}

		if ($listingClasseT != "") {
			$listingClasseT=preg_replace('/,$/','',$listingClasseT);
			$listingClasse="{".$listingClasseT."}";
		}

	}

	if ($number != "") { $sqlSuite=", number_obj='$number' "; }
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}cahiertexte SET liste_id_classe='$listingClasse' , date_saisie='$date',  heure_saisie='$heure', objectif='$objectif' $sqlSuite WHERE id_class_or_grp='$idclsorgrp' AND date_contenu='$datecontenu' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof'";
		return(execSql($sql));	
	}else{
		delete_cahiertexte($datecontenu,$clsorgrp,$idmatiere,$idclsorgrp,$idprof);
		$sql="INSERT INTO ${prefixe}cahiertexte(id_class_or_grp,matiere_id,date_saisie,heure_saisie,date_contenu,classorgrp,number_obj,idprof,objectif,liste_id_classe) VALUES ('$idclsorgrp','$idmatiere','$date','$heure','$datecontenu','$clsorgrp','$number','$idprof','$objectif','$listingClasse')";
	        return(execSql($sql));
	}
}

function modif_devoirscolaire($iddevoir,$idclsorgrp,$idmatiere,$date,$pourle,$texte,$clsorgrp,$number,$fichier) {
	global $cnx;
	global $prefixe;
	$heure=dateHIS();
	$date=dateFormBase($date);
	$pourle=dateFormBase($pourle);
	if (trim($fichier) == "") {
		$sql="UPDATE ${prefixe}devoir_scolaire SET texte='$texte',date_saisie='$date',heure_saisie='$heure' WHERE id='$iddevoir'";
	}else{
		$sql="UPDATE ${prefixe}devoir_scolaire SET texte='$texte',date_saisie='$date',heure_saisie='$heure',number='$number' WHERE id='$iddevoir'";
	}
        return(execSql($sql));
}

function delete_cahiertexte($date_contenu,$clsorgrp,$idmatiere,$idclsorgrp,$idprof) {
	global $cnx;
	global $prefixe;
	$date_contenu=dateFormBase($date_contenu);
	$sql="DELETE FROM ${prefixe}cahiertexte WHERE id_class_or_grp='$idclsorgrp' AND date_contenu='$date_contenu' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof' ";
	execSql($sql);
}

function delete_devoirscolaire($date_contenu,$clsorgrp,$idmatiere,$idclsorgrp,$idprof) {
	global $cnx;
	global $prefixe;
	$date_contenu=dateFormBase($date_contenu);
	$sql="DELETE FROM ${prefixe}devoir_scolaire WHERE id_class_or_grp='$idclsorgrp' AND date_devoir='$date_contenu' AND matiere_id='$idmatiere' AND classorgrp='$clsorgrp' AND idprof='$idprof' ";
	execSql($sql);
}

function recherche_devoir_scolaire_2($date_contenu,$clsorgrp,$idmatiere,$idclsorgrp,$idprof) {
	global $cnx;
	global $prefixe;
	$date_contenu=dateFormBase($date_contenu);

	$sql="SELECT liste_id_classe,id FROM ${prefixe}devoir_scolaire WHERE classorgrp='1' AND  date_devoir='$date_contenu' AND matiere_id='$idmatiere' AND idprof='$idprof'  ";
	
	$res=execSql($sql);
	$data_3=chargeMat($res);
	for($i=0;$i<count($data_3);$i++) {
		$liste_id_classe=$data_3[$i][0];
		$id=$data_3[$i][1];
		$liste_id_classe=preg_replace('/\{/','',$liste_id_classe);
		$liste_id_classe=preg_replace('/\}/','',$liste_id_classe);
		$tab=preg_split('/,/',$liste_id_classe);
		foreach($tab as $key=>$value) {
			if ($value == $idclsorgrp) {
				$liste_id.="'$id',";
				break;
			}
		}		
	}


	if ($liste_id != "") {
		$liste_id=preg_replace('/,$/','',$liste_id);
		$sqlsuite=" OR id IN ($liste_id)";
	}

 
	if ($clsorgrp > 0 ) { $clsorgrp=1 ; }else{ $clsorgrp=0 ; }
	$sql="SELECT id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,date_devoir,texte,classorgrp,number,idprof,tempsestimedevoir  FROM ${prefixe}devoir_scolaire WHERE id_class_or_grp='$idclsorgrp' AND matiere_id='$idmatiere' AND date_devoir='$date_contenu' AND classorgrp='$clsorgrp' AND idprof='$idprof' $sqlsuite ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function recherche_devoir_scolaire_2viaclasse($date_contenu,$clsorgrp,$idclsorgrp) {
	global $cnx;
	global $prefixe;
	$date_contenu=dateFormBase($date_contenu);
	if ($clsorgrp > 0 ) { $clsorgrp=1 ; }else{ $clsorgrp=0 ; }
	$sql="SELECT id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,date_devoir,texte,classorgrp,number,idprof,tempsestimedevoir  FROM ${prefixe}devoir_scolaire WHERE id_class_or_grp='$idclsorgrp' AND date_devoir='$date_contenu' AND classorgrp='$clsorgrp' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function recherche_contenu_scolaire_($date_contenu,$clsorgrp,$idmatiere,$idclsorgrp,$idprof) {
	global $cnx;
	global $prefixe;
	$date_contenu=dateFormBase($date_contenu);
	$sql="SELECT liste_id_classe,id FROM ${prefixe}cahiertexte WHERE classorgrp='1' AND  date_contenu='$date_contenu' AND matiere_id='$idmatiere' AND idprof='$idprof'  ";
	$res=execSql($sql);
	$data_3=chargeMat($res);
	for($i=0;$i<count($data_3);$i++) {
		$liste_id_classe=$data_3[$i][0];
		$id=$data_3[$i][1];
		$liste_id_classe=preg_replace('/\{/','',$liste_id_classe);
		$liste_id_classe=preg_replace('/\}/','',$liste_id_classe);
		$tab=preg_split('/,/',$liste_id_classe);
		foreach($tab as $key=>$value) {
			if ($value == $idclsorgrp) {
				$liste_id.="'$id',";
				break;
			}
		}		
	}


	if ($liste_id != "") {
		$liste_id=preg_replace('/,$/','',$liste_id);
		$sqlsuite=" OR id IN ($liste_id)";
	}
	$sql="SELECT id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,classorgrp,number,contenu,objectif,date_contenu,idprof,number_obj,blocnote FROM ${prefixe}cahiertexte WHERE id_class_or_grp='$idclsorgrp' AND matiere_id='$idmatiere' AND date_contenu='$date_contenu'  AND classorgrp='$clsorgrp' AND idprof='$idprof' $sqlsuite ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function recherche_contenu_scolaire_viaclasse($date_contenu,$clsorgrp,$idclsorgrp) {
	global $cnx;
	global $prefixe;
	$date_contenu=dateFormBase($date_contenu);
	$sql="SELECT id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,classorgrp,number,contenu,objectif,date_contenu,idprof,number_obj,blocnote FROM ${prefixe}cahiertexte WHERE id_class_or_grp='$idclsorgrp'  AND date_contenu='$date_contenu'  AND classorgrp='$clsorgrp' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function recherche_devoir_scolaire($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,date_devoir,texte,classorgrp,number FROM ${prefixe}devoir_scolaire WHERE id='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function devoirsupp($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT number FROM ${prefixe}devoir_scolaire WHERE id='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$number=$data[0][0];
	}
	if (file_exists("./data/DevoirScolaire/$number")) {
		unlink("./data/DevoirScolaire/$number");
	}

	$sql="DELETE FROM ${prefixe}devoir_scolaire  WHERE id='$id'";
        $ins=execSql($sql);
        unset($sql);
}

//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
// configuration du nombre de sanction pour la mise en place d'une retenue
function creat_config_retenue($sanction,$nb,$user,$date) {
        global $cnx;
        global $prefixe;
        $sql="INSERT INTO ${prefixe}type_nb_sanction(sanction,nb,origin_user,date_saisie) VALUES ('$sanction','$nb','$user','$date')";
       	return(execSql($sql));
}

//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
function create_discipline_retenue($id_eleve,$date_retenue,$heure_retenue,$date,$user,$sanction,$qui,$motif,$duree_retenue,$devoir=NULL,$description_fait,$courrierparent=NULL,$daterepport=NULL) {
        global $cnx;
	global $prefixe;

	$idclasse=chercheIdClasseDunEleve($id_eleve);
	$sql="SELECT idprof  FROM ${prefixe}prof_p WHERE idclasse='$idclasse' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	
	$date=date("Y-m-d");
	$heure=date("H:s:i");
	$type_personne=renvoiTypePersonne($_SESSION['membre']);
	$number=md5(uniqid(rand()));
	$objet="Retenue pour $qui";
	$message="Retenue pour $qui <br> Motif : $motif <br> Description : $description_fait <br> Sanction : $sanction <br> Retenue le ".dateForm($date_retenue)." à ".timeForm($heure_retenue);
	for($i=0;$i<count($data);$i++) {
		$destinataire=$data[$i][0];
		envoi_messagerie($_SESSION["id_pers"],$destinataire,$objet,Crypte($message,$number),$date,$heure,$type_personne,'ENS',$number,'',0);
	}
	
	envoi_messagerie($_SESSION["id_pers"],$id_eleve,"Message personnel Triade",Crypte($message,$number),$date,$heure,$type_personne,'PAR',$number,'',0);

        //$sql="INSERT INTO ${prefixe}type_nb_sanction(sanction,nb,origin_user,date_saisie) VALUES ('$sanction','$nb','$user','$date')";
	$motif=strip_tags($motif);
	$motif=addslashes($motif);
	$sanction=addslashes($sanction);
	if ($courrierparent == null) $courrierparent=0;
	if ($daterepport == null) $daterepport="0000-00-00";
        $sql="INSERT INTO ${prefixe}discipline_retenue(id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire,description_fait,courrier_env,repport_du) VALUES ($id_eleve,'$date_retenue','$heure_retenue','$date','$user',$sanction,'false','$motif','$qui','false','$duree_retenue','$devoir','$description_fait','$courrierparent','$daterepport')";
	return(execSql($sql));

	
}




//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
function create_discipline_prof($id_eleve,$sanction,$motif,$qui,$devoir,$etat,$date_devoir,$idclasse,$description_fait,$idsanction) {
	global $cnx;
        global $prefixe;
	if ($date_devoir == "") {
		$date_devoir="0000-00-00";
	}else {
		$date_devoir=dateFormBase($date_devoir);
	}
	if ($etat == "devoir") {
		if (DBTYPE == "pgsql") { $retenu='FALSE'; }
	        if (DBTYPE == "mysql") { $retenu='0'; }
		$sql="INSERT INTO ${prefixe}discipline_prof (id_eleve,id_category,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,description_fait,idsanction) VALUES ('$id_eleve','$sanction','$devoir','$date_devoir','$retenu','$retenu','','$motif','$qui','$idclasse','$description_fait','$idsanction')";
		execSql($sql);
		$date=dateDMY2();
		$user=recherche_personne($qui);
		create_discipline_sanction($id_eleve,$motif,$sanction,$date,$user,$user,$devoir,$description_fait);
	}
	if ($etat == "retenu") {
		if (DBTYPE == "pgsql") { $retenu='TRUE'; $retenu_enrg='FALSE'; }
	        if (DBTYPE == "mysql") { $retenu='1'; $retenu_enrg='0'; }
		$sql="INSERT INTO ${prefixe}discipline_prof (id_eleve,id_category,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,description_fait,idsanction) VALUES ('$id_eleve','$sanction','$devoir','$date_devoir','$retenu','$retenu_enrg','','$motif','$qui','$idclasse','$description_fait','$idsanction')";
		execSql($sql);

	}
}

function cherche_eleve_retenu() {
	global $cnx;
    	global $prefixe;
	if (DBTYPE == "pgsql") { $retenuenr='FALSE'; }
        if (DBTYPE == "mysql") { $retenuenr='0'; }
        if (DBTYPE == "pgsql") { $retenu='TRUE'; }
        if (DBTYPE == "mysql") { $retenu='1'; }
	$sql="SELECT id_eleve,id_category,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait  FROM ${prefixe}discipline_prof WHERE  demande_retenu='$retenu' AND retenu_enrg='$retenuenr'  ";
	$res=execSql($sql);
        $data=chargeMat($res);
        unset($sql);
        return $data;
}

function recherche_retenue_du_jour_2($date,$dateFin) {
	global $cnx;
    	global $prefixe;
	$dateDebut=dateFormBase($date);
	$dateFin=dateFormBase($dateFin);
    	$sql="SELECT  id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire,description_fait FROM ${prefixe}discipline_retenue WHERE   date_de_la_retenue >='$dateDebut' AND date_de_la_retenue <= '$dateFin'";
	$res=execSql($sql);
	$data=chargeMat($res);
	unset($sql);
    	return $data;
}


function recherche_retenue_du_jour_2bis($date,$dateFin,$tri) {
	global $cnx;
    	global $prefixe;
	$dateDebut=dateFormBase($date);
	$dateFin=dateFormBase($dateFin);
	if ($tri == "nom") {
		$suitesql="AND d.id_elev=e.elev_id";
		$tri="e.nom";
	}
	if ($tri == "classe") {
		$suitesql="AND d.id_elev=e.elev_id AND e.classe=c.code_class";
		$tri="c.libelle, e.nom";
	}
    	$sql="SELECT  d.id_elev,d.date_de_la_retenue,d.heure_de_la_retenue,d.date_de_saisie,d.origi_saisie,d.id_category,d.retenue_effectuer,d.motif,d.attribuer_par,d.signature_parent,d.duree_retenu,d.devoir_a_faire,d.description_fait FROM ${prefixe}discipline_retenue d, ${prefixe}eleves e, ${prefixe}classes c  WHERE   d.date_de_la_retenue >='$dateDebut' AND d.date_de_la_retenue <= '$dateFin' $suitesql GROUP BY d.id_elev,d.date_de_la_retenue,d.heure_de_la_retenue ORDER BY $tri  ";
	$res=execSql($sql);
	$data=chargeMat($res);
	unset($sql);
    	return $data;
}

function recherche_retenue_du_jour() {
	global $cnx;
    	global $prefixe;
    	$date_du_jour=dateDMY2();
    	$sql="SELECT  id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu FROM ${prefixe}discipline_retenue WHERE  date_de_la_retenue='$date_du_jour' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	unset($sql);
    	return $data;
}

function recherche_sanction_du_jour() {
	global $cnx;
    	global $prefixe;
    	$date_du_jour=dateDMY2();
    	$sql="SELECT  id,id_eleve,motif,id_category,date_saisie,origin_saisie,enr_en_retenue,signature_parent,attribuer_par,devoir_a_faire,description_fait FROM ${prefixe}discipline_sanction  WHERE  date_saisie='$date_du_jour' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	unset($sql);
    	return $data;
}


function recherche_sanction_du_jour_2bis($date,$dateFin,$tri) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($date);
	$dateFin=dateFormBase($dateFin);
	if ($tri == "nom") {
		$suitesql="AND d.id_eleve=e.elev_id";
		$tri="e.nom";
	}
	if ($tri == "classe") {
		$suitesql="AND d.id_eleve=e.elev_id AND e.classe=c.code_class";
		$tri="c.libelle, e.nom";
	}
    	$sql="SELECT  d.id,d.id_eleve,d.motif,d.id_category,d.date_saisie,d.origin_saisie,d.enr_en_retenue,d.signature_parent,d.attribuer_par,d.devoir_a_faire,d.description_fait FROM ${prefixe}discipline_sanction d , ${prefixe}eleves e, ${prefixe}classes c WHERE  d.date_saisie >='$dateDebut' AND d.date_saisie <= '$dateFin' $suitesql GROUP BY d.id,d.id_eleve,d.date_saisie ORDER BY $tri ";
	$res=execSql($sql);
	$data=chargeMat($res);
	unset($sql);
    	return $data;
}

function supp_discipline_prof($id) {
	global $cnx;
    	global $prefixe;
	if (trim($id) != "") {
		$sql="DELETE FROM ${prefixe}discipline_prof WHERE id='$id' ";
	    	execSql($sql);
	}
}


function modif_discipline_prof($id) {
	global $cnx;
        global $prefixe;
	if (DBTYPE == "pgsql") { $retenuenr='FALSE'; }
        if (DBTYPE == "mysql") { $retenuenr='0'; }
        if (DBTYPE == "pgsql") { $retenu='TRUE'; }
        if (DBTYPE == "mysql") { $retenu='1'; }
	$sql="UPDATE ${prefixe}discipline_prof SET retenu_enrg='$retenu' WHERE id='$id'";
        $ins=execSql($sql);
}

function cherche_discipline_prof_devoir_retenu($idprof) {
	global $cnx;
        global $prefixe;
	$date=dateDMY2();
	if (DBTYPE == "pgsql") { $retenuenr='FALSE'; }
        if (DBTYPE == "mysql") { $retenuenr='0'; }
	if (DBTYPE == "pgsql") { $retenu='TRUE'; }
        if (DBTYPE == "mysql") { $retenu='1'; }
	$sql="SELECT id_eleve,id_category,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait  FROM ${prefixe}discipline_prof WHERE idprof='$idprof' AND demande_retenu='$retenu' AND retenu_enrg='$retenuenr'";
	$res=execSql($sql);
        $data=chargeMat($res);
        unset($sql);
        return $data;
}

function cherche_eleve_retenu_id($id) {
	global $cnx;
        global $prefixe;
        $date=dateDMY2();
        if (DBTYPE == "pgsql") { $retenuenr='FALSE'; }
        if (DBTYPE == "mysql") { $retenuenr='0'; }
        if (DBTYPE == "pgsql") { $retenu='TRUE'; }
        if (DBTYPE == "mysql") { $retenu='1'; }
        $sql="SELECT id_eleve,id_category,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait,idsanction  FROM ${prefixe}discipline_prof WHERE id='$id'  ";
        $res=execSql($sql);
        $data=chargeMat($res);
        unset($sql);
        return $data;
}

function cherche_discipline_prof_devoir($idprof) {
	global $cnx;
        global $prefixe;
	$date=dateDMY2();
	$sql="SELECT id_eleve,id_category,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait  FROM ${prefixe}discipline_prof WHERE idprof='$idprof' AND devoir_pour_le='$date' ";
	$res=execSql($sql);
        $data=chargeMat($res);
        unset($sql);
        return $data;
}


function cherche_discipline_prof_devoir2($idprof) {
	global $cnx;
        global $prefixe;
	$date=dateDMY2();
	$sql="SELECT id_eleve,id_category,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait  FROM ${prefixe}discipline_prof WHERE idprof='$idprof' AND devoir_pour_le != '0000-00-00' ";
	$res=execSql($sql);
        $data=chargeMat($res);
        unset($sql);
        return $data;
}

//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
function create_discipline_sanction($id_eleve,$motif,$sanction,$date,$user,$qui,$devoir,$description_fait) {
        global $cnx;
        global $prefixe;
	$motif=strip_tags($motif);

	$idclasse=chercheIdClasseDunEleve($id_eleve);
	$nomeleve=recherche_eleve_nom($id_eleve);
        $prenomeleve=recherche_eleve_prenom($id_eleve);
	
	$anneeScolaire=$_COOKIE["anneeScolaire"];

	$sql="SELECT idprof  FROM ${prefixe}prof_p WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);

	$sql="SELECT libelle FROM ${prefixe}type_category WHERE id='$sanction'";
	$res=execSql($sql);
        $data2=chargeMat($res);
	$libellesanction=$data2[0][0];

	$date=date("Y-m-d");
	$heure=date("H:s:i");
	$type_personne=renvoiTypePersonne($_SESSION['membre']);
	$number=md5(uniqid(rand()));
	$objet="Sanction : $nomeleve $prenomeleve";
	if ($qui == "") $attribue="non indiqué"; 
	$message="Sanction pour l'élève : $nomeleve $prenomeleve <br>Sanction : $motif<br>Attribué par : $qui<br>Description : $description_fait<br>Catégorie : $libellesanction <br>Devoir à faire : $devoir";
	for($i=0;$i<count($data);$i++) {
		$destinataire=$data[$i][0];
		envoi_messagerie($_SESSION["id_pers"],$destinataire,$objet,Crypte($message,$number),$date,$heure,$type_personne,'ENS',$number,'',0);
	}
	
	envoi_messagerie($_SESSION["id_pers"],$id_eleve,"Message personnel Triade",Crypte($message,$number),$date,$heure,$type_personne,'PAR',$number,'',0);


        $sql="INSERT INTO ${prefixe}discipline_sanction(id_eleve,motif,id_category,date_saisie,attribuer_par,origin_saisie,enr_en_retenue,signature_parent,devoir_a_faire,description_fait) VALUES ($id_eleve,'$motif',$sanction,'$date','$qui','$user','false','false','$devoir','$description_fait')";
	return(execSql($sql));
}

//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
// creation eleve
function create_eleve($params,$ascii) {
	global $cnx;
	global $prefixe;

	if (trim($params[mdp]) != "") { $params[mdp]=cryptage($params[mdp]); }
	if (isset($params[mdp2])) {
		if (trim($params[mdp2]) != "") { $params[mdp2]=cryptage($params[mdp2]); }
	}else{
		$params[mdp2]=null;
	}
	if (trim($params[mdpeleve]) != "") { 
		$params[mdpeleve]=cryptage($params[mdpeleve]); 
	}

	if ($ascii) { $params[naiss]=dateFormBase($params[naiss]); }

	$sql="SELECT nom FROM ${prefixe}eleves  WHERE nom='$params[ne]' AND prenom='$params[pe]' AND  date_naissance='$params[naiss]' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) { return -3; }

	$sql="SELECT nom FROM ${prefixe}eleves  WHERE nom='$params[ne]' AND prenom='$params[pe]' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$nbp=count($data)+1;
	    $params[pe]=$params[pe]." (".$nbp.")";
	}

	if ((MAILMESS == "oui") && (trim($params[email]) != "")){
		if (DBTYPE == "pgsql") {
			$valid="TRUE";
		}
		if (DBTYPE == "mysql")  {
			$valid=1;
		}
	}else {
		if (DBTYPE == "pgsql") {
			$valid="FALSE";
		}
		if (DBTYPE == "mysql")  {
			$valid=0;
		}
	}

	if (trim($params[civ_1]) == "") { $params[civ_1]="null"; }
	if (trim($params[civ_2]) == "") { $params[civ_2]="null"; }
	if (trim($params[numero_gep]) == "") {$params[numero_gep]="null"; } 

	$params[boursier_montant]=preg_replace('/,/','\.',$params[boursier_montant]);
	$params[indemnite_stage]=preg_replace('/,/','\.',$params[indemnite_stage]);

        $boursier=$params[boursier];
        if ($boursier == "oui") $boursier='1';
        if ($boursier == "non") $boursier='0';
	
	if (($params[ce] == '-1') || ($params[ce] <= 0)) { return(false); }

	$annee_scolaire=$params[annee_scolaire];
	if (trim($annee_scolaire) == "") $annee_scolaire=anneeScolaireViaIdClasse($params['ce']);

$sql=<<<EOF

	INSERT INTO ${prefixe}eleves(
		nom,
		prenom,
		classe,
		lv1,
		lv2,
		`option`,
		regime,
		date_naissance,
		nationalite,
		passwd,
		passwd_eleve,
		nomtuteur,
		prenomtuteur,
		adr1,
		code_post_adr1,
		commune_adr1,
		adr2,
		code_post_adr2,
		commune_adr2,
		telephone,
		profession_pere,
		tel_prof_pere,
		profession_mere,
		tel_prof_mere,
		nom_etablissement,
		numero_etablissement,
		code_postal_etablissement,
		commune_etablissement,
		numero_eleve,
		email,
		class_ant,
		annee_ant,
		numero_gep,
		valid_forward_mail_parent,
		civ_1,
		civ_2,
		tel_eleve,
		email_eleve,
		nom_resp_2,
		prenom_resp_2,
		lieu_naissance,
		tel_port_1,
		tel_port_2,
		valid_forward_mail_eleve,
		email_resp_2,
		code_compta,
		sexe,
		passwd_parent_2,
		information,
		adr_eleve,
		commune_eleve,
		ccp_eleve,
		tel_fixe_eleve,
		pays_eleve,
		boursier,
		montant_bourse,
		indemnite_stage,
		nbmoisindemnite,
		emailpro_eleve,
		rangement,
		cdi,
		bde,
		situation_familiale,
		annee_scolaire,
		serie_bac,
		annee_bac,
		departement_bac,
		departementnais
	)VALUES (
		'$params[ne]',
		'$params[pe]',
		'$params[ce]',
		'$params[lv1]',
		'$params[lv2]',
		'$params[option]',
		'$params[regime]',
		'$params[naiss]',
		'$params[nat]',
		'$params[mdp]',
		'$params[mdpeleve]',
		'$params[nt]',
		'$params[pt]',
		'$params[adr1]',
		'$params[cpadr1]',
		'$params[commadr1]',
		'$params[adr2]',
		'$params[cpadr2]',
		'$params[commadr2]',
		'$params[tel]',
		'$params[profp]',
		'$params[telprofp]',
		'$params[profm]',
		'$params[telprofm]',
		'$params[nomet]',
		'$params[numet]',
		'$params[cpet]',
		'$params[commet]',
		'$params[numero_eleve]',
		'$params[email]',
		'$params[classe_ant]',
		'$params[annee_ant]',
		 $params[numero_gep],
		'$valid',
		 $params[civ_1],
		 $params[civ_2],
		'$params[tel_eleve]',
		'$params[mail_eleve]',
		'$params[nom_resp2]',
		'$params[prenom_resp2]',
		'$params[lieunais]',
		'$params[tel_port_1]',
		'$params[tel_port_2]',
		'$valid',
		'$params[email_2]',
		'$params[codecompta]',
		'$params[sexe]',
		'$params[mdp2]',
		'$params[information]',
		'$params[adr_eleve]',
		'$params[commune_eleve]',
		'$params[ccp_eleve]',
		'$params[tel_fixe_eleve]',
		'$params[pays_eleve]',
		'$boursier',
		'$params[boursier_montant]',
		'$params[indemnite_stage]',
		'$params[nbmoisindemnite_stage]',
		'$params[mailpro_eleve]',
		'$params[rangement]',
		'$params[cdi]',
		'$params[bde]',
		'$params[situation_familiale]',
		'$annee_scolaire',
		'$params[saisie_serie_bac]',
		'$params[saisie_annee_bac]',
		'$params[saisie_departement_bac]',
		'$params[saisie_departementnais]'
		)
EOF;
		$rc=execSql($sql);
		$UID=mysqli_insert_id();

		if (AUTOINE == "oui") {
			$INE=$annee_scolaire."-".$UID;
			$sql="UPDATE ${prefixe}eleves SET numero_eleve='$INE' WHERE elev_id='$UID'";
			execSql($sql);
		}	
		return($rc);
}


function updateINE($INE,$nomEleve,$prenomEleve) {
	global $prefixe;
	global $cnx;
	$sql="UPDATE ${prefixe}eleves SET numero_eleve='$INE' WHERE nom='$nomEleve' AND prenom='$prenomEleve' AND numero_eleve=''";
	execSql($sql);
}




function updateGepEleve($numero_gep,$nomparent,$prenomparent,$adresse,$adresse2,$codepostal,$ville,$tel,$telpereprof,$telperepers,$telmerepers,$telmereprof) {
	global $prefixe;
	global $cnx;
	
	$adresse=trim($adresse." ".$adresse2);
	

	$sql="SELECT nom FROM ${prefixe}eleves WHERE numero_gep='$numero_gep'  ";
 	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}eleves SET nomtuteur='$nomparent', prenomtuteur='$prenomparent', adr1='$adresse', code_post_adr1='$codepostal', commune_adr1='$ville', telephone='$tel', tel_prof_pere='$telpereprof' , tel_prof_mere='$telmereprof' WHERE numero_gep='$numero_gep' ";
		return(execSql($sql));
	}


	$sql="SELECT nom FROM ${prefixe}elevessansclasse WHERE numero_gep='$numero_gep'  ";
 	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}elevessansclasse SET nomtuteur='$nomparent', prenomtuteur='$prenomparent', adr1='$adresse', code_post_adr1='$codepostal', commune_adr1='$ville', telephone='$tel', tel_prof_pere='$telpereprof' , tel_prof_mere='$telmereprof' WHERE numero_gep='$numero_gep' ";
		return(execSql($sql));
	}

	return false;
}


function chercheLvo($ideleve) {
        global $prefixe;
	global $cnx;
	$sql="SELECT lv1,lv2,`option` FROM ${prefixe}eleves WHERE elev_id='$ideleve' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

// function pour le passage de la table elevessansclasse à eleves
function trans($nomE,$prenomE){
        global $prefixe;
	global $cnx;
	$sql="SELECT 
		lv1,
		lv2,
		`option`,
		regime,
		date_naissance,
		nationalite,
		passwd,
		nomtuteur,
		prenomtuteur,
		adr1,
		code_post_adr1,
		commune_adr1,
		adr2,
		code_post_adr2,
		commune_adr2,
		telephone,
		profession_pere,
		tel_prof_pere,
		profession_mere,
		tel_prof_mere,
		nom_etablissement,
		numero_etablissement,
		code_postal_etablissement,
		commune_etablissement,
		numero_eleve,
		photo,
		email,
		class_ant,
		annee_ant,
		numero_gep,
		passwd_eleve,
		civ_1,
		civ_2,
		tel_eleve,
		email_eleve,
		nom_resp_2,
		prenom_resp_2,
		lieu_naissance,
		tel_port_1,
		tel_port_2,
		email_resp_2,
		valid_forward_mail_eleve,
		valid_forward_mail_parent,
		code_compta,
		sexe,
		passwd_parent_2,
		information,
		adr_eleve,
		commune_eleve,
		ccp_eleve,
		tel_fixe_eleve,
		pays_eleve
		FROM ${prefixe}elevessansclasse WHERE nom='$nomE' AND prenom='$prenomE'";
        $res=execSql($sql);
        $data=chargeMat($res);
	unset($sql);
        return $data;
}


function create_eleve2($nomE,$prenomE,$classe,$lv1,$lv2) {
        global $prefixe;
	global $cnx;

	$sql="SELECT elev_id FROM ${prefixe}eleves WHERE  nom='$nomE'  AND prenom='$prenomE' AND   classe='$classe' AND    lv1='$lv1' AND   lv2='$lv2' ";
        $res=execSql($sql);
	$data=chargeMat($res);

	if (count($data) <= 0) {
		$sql="INSERT INTO ${prefixe}eleves (nom,prenom,classe,lv1,lv2) VALUES ('$nomE','$prenomE','$classe','$lv1','$lv2')";
		$ins=execSql($sql);
		unset($sql);
		$resul=trans($nomE,$prenomE);
 		for($i=0;$i<count($resul);$i++) {
			$lv1=trim(addslashes($resul[$i][0]));
			$lv2=trim(addslashes($resul[$i][1]));
			$option=trim(addslashes($resul[$i][2]));
			$regime=trim(addslashes($resul[$i][3]));
			$date_naissance=$resul[$i][4];
			$nationalite=trim(addslashes($resul[$i][5]));
			$passwd=trim($resul[$i][6]);
			$nomtuteur=trim(addslashes($resul[$i][7]));
			$prenomtuteur=trim(addslashes($resul[$i][8]));
			$adr1=trim(addslashes($resul[$i][9]));
			$code_post_adr1=trim(addslashes($resul[$i][10]));
			$commune_adr1=trim(addslashes($resul[$i][11]));
			$adr2=trim(addslashes($resul[$i][12]));
			$code_post_adr2=trim(addslashes($resul[$i][13]));
			$commune_adr2=trim(addslashes($resul[$i][14]));
			$telephone=trim($resul[$i][15]);
			$profession_pere=trim(addslashes($resul[$i][16]));
			$tel_prof_pere=trim($resul[$i][17]);
			$profession_mere=trim(addslashes($resul[$i][18]));
			$tel_prof_mere=trim($resul[$i][19]);
			$nom_etablissement=trim(addslashes($resul[$i][20]));
			$numero_etablissement=trim($resul[$i][21]);
			$code_postal_etablissement=trim($resul[$i][22]);
			$commune_etablissement=trim(addslashes($resul[$i][23]));
			$numero_eleve=trim($resul[$i][24]);
			$photo=trim($resul[$i][25]);
			$email=trim($resul[$i][26]);
		    	$class_ant=trim($resul[$i][27]);
			$annee_ant=trim($resul[$i][28]);
			$numero_gep=trim($resul[$i][29]);
			$passwd_eleve=trim($resul[$i][30]);
			$valid_forward_mail_parent=NULL;
			$civ_1=trim($resul[$i][31]);
			$civ_2=trim($resul[$i][32]);
			$tel_eleve=trim($resul[$i][33]);
			$email_eleve=trim($resul[$i][34]);
			$nom_resp_2=trim(addslashes($resul[$i][35]));
			$prenom_resp_2=trim(addslashes($resul[$i][36]));
			$lieu_naissance=trim(addslashes($resul[$i][37]));
			$tel_port_1=trim($resul[$i][38]);
			$tel_port_2=trim($resul[$i][39]);
			$email_resp_2=trim($resul[$i][40]);
			$valid_forward_mail_eleve=trim($resul[$i][41]);
			$valid_forward_mail_parent=trim($resul[$i][42]);
			$code_compta=trim($resul[$i][43]);
			$sexe=trim($resul[$i][44]);
			$passwd_parent_2=trim($resul[$i][45]);
			$information=trim($resul[$i][46]);
			$adr_eleve=trim($resul[$i][47]);
			$commune_eleve=trim($resul[$i][48]);
			$ccp_eleve=trim($resul[$i][49]);
			$tel_fixe_eleve=trim($resul[$i][50]);
			$pays_eleve=trim($resul[$i][51]);

		}

	$sql="UPDATE ${prefixe}eleves SET lv1='$lv1',lv2='$lv2', `option`='$option',regime='$regime',date_naissance='$date_naissance',nationalite='$nationalite',passwd='$passwd',nomtuteur='$nomtuteur',prenomtuteur='$prenomtuteur',adr1='$adr1',code_post_adr1='$code_post_adr1',commune_adr1='$commune_adr1',adr2='$adr2',code_post_adr2='$code_post_adr2',commune_adr2='$commune_adr2',telephone='$telephone',profession_pere='$profession_pere',tel_prof_pere='$tel_prof_pere',profession_mere='$profession_mere',tel_prof_mere='$tel_prof_mere',nom_etablissement='$nom_etablissement',numero_etablissement='$numero_etablissement',code_postal_etablissement='$code_postal_etablissement',commune_etablissement='$commune_etablissement',numero_eleve='$numero_eleve',photo='$photo',email='$email',class_ant='$class_ant',annee_ant='$annee_ant',numero_gep='$numero_gep',passwd_eleve='$passwd_eleve',valid_forward_mail_parent='$valid_forward_mail_parent',civ_1='$civ_1',civ_2='$civ_2',tel_eleve='$tel_eleve',email_eleve='$email_eleve',nom_resp_2='$nom_resp_2',prenom_resp_2='$prenom_resp_2',lieu_naissance='$lieu_naissance',tel_port_1='$tel_port_1',tel_port_2='$tel_port_2', valid_forward_mail_eleve='$valid_forward_mail_eleve', email_resp_2='$email_resp_2', valid_forward_mail_parent='$valid_forward_mail_parent', code_compta='$code_compta', sexe='$sexe' , passwd_parent_2='$passwd_parent_2', information='$information', adr_eleve='$adr_eleve' , ccp_eleve='$commune_eleve',  commune_eleve='$ccp_eleve',  tel_fixe_eleve='$tel_fixe_eleve', pays_eleve='$pays_eleve' WHERE nom='$nomE' AND prenom='$prenomE'";
	$ins=execSql($sql);
	}

	// suppression dans la base elevessansclasse
	if($ins) {
       		$sql="DELETE FROM ${prefixe}elevessansclasse WHERE nom='$nomE' AND prenom='$prenomE' ";
        	$ins=execSql($sql);
		unset($sql);
	}


}

function create_update_eleve_scolnet($nomE,$prenomE,$datenaissance,$params,$ascii,$champsvide,$affectpasswd) {
        global $prefixe;
	global $cnx;

	if (preg_match('/\//',$datenaissance)) { $datenaissance=dateFormBase($datenaissance); }


	$sql="SELECT elev_id,classe,annee_scolaire FROM ${prefixe}eleves WHERE  lower(nom)='$nomE'  AND lower(prenom)='$prenomE' AND   date_naissance='$datenaissance'";
        $res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) >  0) {
		
		if ($data[0][2] != "") enrEtudiantHistory($data[0][0],$data[0][2],$data[0][1]);

		$classe=$params[ce];
		$lv1=$params[lv1];
		$lv2=$params[lv2];
		$option=$params[option];
		$regime=$params[regime];
		$nationnalite=$params[nat];
		$passwd=$params[mdp];
		$passwd_eleve=$params[mdpeleve];
		$nomtuteur=$params[nt];
		$prenomtuteur=$params[pt];
		$adr1=$params[adr1];
		$code_post_adr1=$params[cpadr1];
		$commune_adr1=$params[commadr1];
		$adr2=$params[adr2];
		$code_post_adr2=$params[cpadr2];
		$commune_adr2=$params[commadr2];
		$telephone=$params[tel];
	//	$profession_pere=$params[profp];
	//	$tel_prof_pere=$params[telprofp];
	//	$profession_mere=$params[profm];
	//	$tel_prof_mere=$params[telprofm];
	//	$nom_etablissement=$params[nomet];
	//	$numero_etablissement=$params[numet];
	//	$code_postale_etablissement=$params[cpet];
	//	$commune_etablissement=$params[commet];
		$numero_eleve=$params[numero_eleve];
		$email=$params[email];
	//	$class_ant=$params[classe_ant];
	//	$annee_ant=$params[annee_ant];
		$numero_gep=$params[numero_gep];
		$civ1=$params[civ_1];
		$civ2=$params[civ_2];
	//	$tel_eleve=$params[tel_eleve];
	//	$email_eleve=$params[mail_eleve];
		$nom_resp_2=$params[nom_resp2];
		$prenom_resp_2=$params[prenom_resp2];
		$lieu_naissance=$params[lieunais];
		$tel_port_1=$params[tel_port_1];
		$tel_port_2=$params[tel_port_2];
		$anneeScolaire=$params[annee_scolaire];

	//	$email_resp_2=$params[email_2];
	//	$codecompta=$params[codecompta];
		$sexe=$params[sexe];
	//	$boursier=$params[boursier];
		$idClasse=$classe;
	
	//	$boursier=$params['boursier'];
	//	if ($boursier == "oui") $boursier='1';
	//	if ($boursier == "non") $boursier='0';


		if ($affectpasswd != 1) { $sqlsuite="passwd='$passwd' , passwd_eleve='$passwd_eleve' , "; }

		if ($champsvide == 1) {

			if (trim($lv1) != "") { $sql2.="lv1='$lv1',"; }
			if (trim($lv2) != "") { $sql2.="lv2='$lv2',"; }
			if ($affectpasswd != 1) { $sql2.="passwd='$passwd',passwd_eleve='$passwd_eleve',"; }
			if (trim($idClasse) != "") { $sql2.="classe='$idClasse',"; }
			if (trim($option) != "") { $sql2.="`option`='$option',"; }
			if (trim($regime) != "") { $sql2.="regime='$regime',"; }
			if (trim($nationnalite) != "") { $sql2.="nationalite='$nationnalite',"; }
			if (trim($nomtuteur) != "") { $sql2.="nomtuteur='$nomtuteur',"; }
			if (trim($prenomtuteur) != "") { $sql2.="prenomtuteur='$prenomtuteur',"; }
			if (trim($adr1) != "") { $sql2.="adr1='$adr1',"; }
			if (trim($code_post_adr1) != "") { $sql2.="code_post_adr1='$code_post_adr1',"; }
			if (trim($commune_adr1) != "") { $sql2.="commune_adr1='$commune_adr1',"; }
			if (trim($adr2) != "") { $sql2.="adr2='$adr2',"; }
			if (trim($code_post_adr2) != "") { $sql2.="code_post_adr2='$code_post_adr2',"; }
			if (trim($commune_adr2) != "") { $sql2.="commune_adr2='$commune_adr2',"; }
			if (trim($telephone) != "") { $sql2.="telephone='$telephone',"; }
			if (trim($numero_eleve) != "") { $sql2.="numero_eleve='$numero_eleve',"; }
			if (trim($email) != "") { $sql2.="email='$email',"; }
			if (trim($civ1) != "") { $sql2.="civ_1='$civ1',"; }
			if (trim($civ2) != "") { $sql2.="civ_2='$civ2',"; }
			if (trim($nom_resp_2) != "") { $sql2.="nom_resp_2='$nom_resp_2',"; }
			if (trim($prenom_resp_2) != "") { $sql2.="prenom_resp_2='$prenom_resp_2',"; }
			if (trim($lieu_naissance) != "") { $sql2.="lieu_naissance='$lieu_naissance',"; }
			if (trim($tel_port_1) != "") { $sql2.="tel_port_1='$tel_port_1',"; }
			if (trim($tel_port_2) != "") { $sql2.="tel_port_2='$tel_port_2',"; }
	//		if (trim($boursier) != "") { $sql2.="boursier='$boursier',"; }			
			if (trim($sexe) != "") { $sql2.="sexe='$sexe',"; }
			if (trim($sql2) != "") { 
				$sql2=preg_replace('/,$/',"",$sql2);
				$sql="UPDATE ${prefixe}eleves SET $sql2 WHERE nom='$nomE'  AND prenom='$prenomE' AND   date_naissance='$datenaissance'"; 
			}
		}else {

			$sql="UPDATE ${prefixe}eleves 
			SET 
			lv1='$lv1',
			lv2='$lv2',
			classe='$idClasse',
			`option`='$option',
			regime='$regime',
			nationalite='$nationnalite',
			nomtuteur='$nomtuteur',
			prenomtuteur='$prenomtuteur',
			adr1='$adr1',
			code_post_adr1='$code_post_adr1',
			commune_adr1='$commune_adr1',
			adr2='$adr2',
			code_post_adr2='$code_post_adr2',
			commune_adr2='$commune_adr2',
			telephone='$telephone',
			numero_eleve='$numero_eleve',
			email='$email', 
			$sqlsuite 
			civ_1='$civ1',
			civ_2='$civ2',
			nom_resp_2='$nom_resp_2',
			prenom_resp_2='$prenom_resp_2',
			lieu_naissance='$lieu_naissance',
			tel_port_1='$tel_port_1',
			tel_port_2='$tel_port_2',
		      	sexe='$sexe',
			annee_scolaire='$anneeScolaire'
			WHERE nom='$nomE'  AND prenom='$prenomE' AND   date_naissance='$datenaissance' ";
		}
		$ins=execSql($sql);
		if ($ins) {
			if ($affectpasswd == 1) {
				return(-2); // Pas de mot de passe
			}else{
				return($ins);
			}
		}else{
			return($ins);
		}
	}else{
		$cr=@create_eleve($params,$ascii);
		return($cr);	
	}
}




function create_update_eleve($nomE,$prenomE,$datenaissance,$params,$ascii,$champsvide,$affectpasswd,$noncontroledatenaissance) {
    	global $prefixe;
	global $cnx;


	
	
	if (preg_match('/\//',$datenaissance)) { $datenaissance=dateFormBase($datenaissance); }

	if ($noncontroledatenaissance == "oui") {
		$sql="SELECT elev_id,classe,annee_scolaire FROM ${prefixe}eleves WHERE  lower(nom)='$nomE'  AND lower(prenom)='$prenomE' "; 
	}else{
		$sql="SELECT elev_id,classe,annee_scolaire FROM ${prefixe}eleves WHERE  lower(nom)='$nomE'  AND lower(prenom)='$prenomE'  AND   date_naissance='$datenaissance'";
	}
        $res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) >  0) {

		if ($data[0][2] != "") enrEtudiantHistory($data[0][0],$data[0][2],$data[0][1]);

		if (trim($params[mdp]) != "") { $params[mdp]=cryptage($params[mdp]); }
		if (isset($params[mdp2])) {
			if (trim($params[mdp2]) != "") { $params[mdp2]=cryptage($params[mdp2]); }
		}else{
			$params[mdp2]=null;
		}
		if (trim($params[mdpeleve]) != "") { $params[mdpeleve]=cryptage($params[mdpeleve]); }

		$classe=$params[ce];
		$lv1=$params[lv1];
		$lv2=$params[lv2];
		$option=$params[option];
		$regime=$params[regime];
		$nationnalite=$params[nat];
		$passwd=$params[mdp];
		$passwd2=$params[mdp2];
		$passwd_eleve=$params[mdpeleve];
		$nomtuteur=$params[nt];
		$prenomtuteur=$params[pt];
		$adr1=$params[adr1];
		$code_post_adr1=$params[cpadr1];
		$commune_adr1=$params[commadr1];
		$adr2=$params[adr2];
		$code_post_adr2=$params[cpadr2];
		$commune_adr2=$params[commadr2];
		$telephone=$params[tel];
		$profession_pere=$params[profp];
		$tel_prof_pere=$params[telprofp];
		$profession_mere=$params[profm];
		$tel_prof_mere=$params[telprofm];
		$nom_etablissement=$params[nomet];
		$numero_etablissement=$params[numet];
		$code_postale_etablissement=$params[cpet];
		$commune_etablissement=$params[commet];
		$numero_eleve=$params[numero_eleve];
		$email=$params[email];
		$class_ant=$params[classe_ant];
		$annee_ant=$params[annee_ant];
		$numero_gep=$params[numero_gep];
		$civ1=$params[civ_1];
		$civ2=$params[civ_2];
		$tel_eleve=$params[tel_eleve];
		$email_eleve=$params[mail_eleve];
		$nom_resp_2=$params[nom_resp2];
		$prenom_resp_2=$params[prenom_resp2];
		$lieu_naissance=$params[lieunais];
		$tel_port_1=$params[tel_port_1];
		$tel_port_2=$params[tel_port_2];
		$email_resp_2=$params[email_2];
		$boursier=$params[boursier];
		$anneeScolaire=$params[annee_scolaire];
	//	$codecompta=$params[codecompta];
	//	$sexe=$params[sexe];

		$idClasse=$classe;
	
		$boursier=$params['boursier'];
		if ($boursier == "oui") $boursier='1';
		if ($boursier == "non") $boursier='0';

		

		if ($affectpasswd != 1) { $sqlsuite="passwd='$passwd' , passwd_eleve='$passwd_eleve' ,"; }

		

		if ($champsvide == 1) {
			if (trim($lv1) != "") { $sql2="lv1='$lv1',"; }
			if (trim($lv2) != "") { $sql2.="lv2='$lv2',"; }
			if ($affectpasswd != 1) { $sql2.="passwd='$passwd', passwd_eleve='$passwd_eleve', passwd_parent_2='$passwd2', "; }
			if (trim($idClasse) != "") { $sql2.="classe='$idClasse',"; }
			if (trim($option) != "") { $sql2.="`option`='$option',"; }
			if (trim($regime) != "") { $sql2.="regime='$regime',"; }
			if (trim($nationnalite) != "") { $sql2.="nationalite='$nationnalite',"; }
			if (trim($nomtuteur) != "") { $sql2.="nomtuteur='$nomtuteur',"; }
			if (trim($prenomtuteur) != "") { $sql2.="prenomtuteur='$prenomtuteur',"; }
			if (trim($adr1) != "") { $sql2.="adr1='$adr1',"; }
			if (trim($code_post_adr1) != "") { $sql2.="code_post_adr1='$code_post_adr1',"; }
			if (trim($commune_adr1) != "") { $sql2.="commune_adr1='$commune_adr1',"; }
			if (trim($adr2) != "") { $sql2.="adr2='$adr2',"; }
			if (trim($code_post_adr2) != "") { $sql2.="code_post_adr2='$code_post_adr2',"; }
			if (trim($commune_adr2) != "") { $sql2.="commune_adr2='$commune_adr2',"; }
			if (trim($telephone) != "") { $sql2.="telephone='$telephone',"; }
			if (trim($numero_eleve) != "") { $sql2.="numero_eleve='$numero_eleve',"; }
			if (trim($email) != "") { $sql2.="email='$email',"; }
			if (trim($civ1) != "") { $sql2.="civ_1='$civ1',"; }
			if (trim($civ2) != "") { $sql2.="civ_2='$civ2',"; }
			if (trim($nom_resp_2) != "") { $sql2.="nom_resp_2='$nom_resp_2',"; }
			if (trim($prenom_resp_2) != "") { $sql2.="prenom_resp_2='$prenom_resp_2',"; }
			if (trim($lieu_naissance) != "") { $sql2.="lieu_naissance='$lieu_naissance',"; }
			if (trim($tel_port_1) != "") { $sql2.="tel_port_1='$tel_port_1',"; }
			if (trim($tel_port_2) != "") { $sql2.="tel_port_2='$tel_port_2',"; }
			if (trim($profession_pere) != "") { $sql2.="profession_pere='$profession_pere',"; }
			if (trim($tel_prof_pere) != "") { $sql2.="tel_prof_pere='$tel_prof_pere',"; }
			if (trim($profession_mere) != "") { $sql2.="profession_mere='$profession_mere',"; }
			if (trim($tel_prof_mere) != "") { $sql2.="tel_prof_mere='$tel_prof_mere',"; }
			if (trim($nom_etablissement) != "") { $sql2.="nom_etablissement='$nom_etablissement',"; }
			if (trim($numero_etablissement) != "") { $sql2.="numero_etablissement='$numero_etablissement',"; }
			if (trim($tel_eleve) != "") { $sql2.="tel_eleve='$tel_eleve',"; }
			if (trim($email_eleve) != "") { $sql2.="email_eleve='$email_eleve',"; }
			if (trim($class_ant) != "") { $sql2.="class_ant='$class_ant',"; }
			if (trim($annee_ant) != "") { $sql2.="annee_ant='$annee_ant',"; }
			if (trim($numero_gep) != "") { $sql2.="numero_gep='$numero_gep',"; }
			if (trim($lieu_naissance) != "") { $sql2.="lieu_naissance='$lieu_naissance',"; }
			if (trim($boursier) != "") { $sql2.="boursier='$boursier',"; }
			if (trim($datenaissance) != "") { $sql2.="date_naissance='$datenaissance',"; }
			if (trim($anneeScolaire) != "") { $sql2.="annee_scolaire='$anneeScolaire',"; }
			
			
			if (trim($sql2) != "") { 
				$sql2=preg_replace('/,$/',"",$sql2);
				if ($noncontroledatenaissance == "oui") { 
					$sql="UPDATE ${prefixe}eleves SET $sql2 WHERE nom='$nomE'  AND prenom='$prenomE'";  
				}else{
					$sql="UPDATE ${prefixe}eleves SET $sql2 WHERE nom='$nomE'  AND prenom='$prenomE' AND   date_naissance='$datenaissance'"; 
				}
			}
		}else{
			if ($noncontroledatenaissance == "oui") {
				$sqlsuite2="";
			}else{
				$sqlsuite2=" AND  date_naissance='$datenaissance'";
			}
			$sql="UPDATE ${prefixe}eleves 
				SET 
				lv1='$lv1',
				lv2='$lv2',
				classe='$idClasse',
				`option`='$option',
				date_naissance='$datenaissance',
				regime='$regime',
				nationalite='$nationnalite',
				nomtuteur='$nomtuteur',
				prenomtuteur='$prenomtuteur',
				adr1='$adr1',
				code_post_adr1='$code_post_adr1',
				commune_adr1='$commune_adr1',
				adr2='$adr2',
				code_post_adr2='$code_post_adr2',
				commune_adr2='$commune_adr2',
				$sqlsuite
				telephone='$telephone',
				profession_pere='$profession_pere',
				tel_prof_pere='$tel_prof_pere',
				profession_mere='$profession_mere',
				tel_prof_mere='$tel_prof_mere',
				nom_etablissement='$nom_etablissement',
				numero_etablissement='$numero_etablissement',
				";
		
				if (AUTOINE != "oui") $sql.="numero_eleve='$numero_eleve', ";

				$sql.=" email='$email',
				class_ant='$class_ant',
				annee_ant='$annee_ant',
				numero_gep='$numero_gep',
				civ_1='$civ1',
				civ_2='$civ2',
				tel_eleve='$tel_eleve',
				email_eleve='$email_eleve',
				nom_resp_2='$nom_resp_2',
				prenom_resp_2='$prenom_resp_2',
				lieu_naissance='$lieu_naissance',
				tel_port_1='$tel_port_1',
				tel_port_2='$tel_port_2',   
				boursier='$boursier',
				annee_scolaire='$anneeScolaire'
				WHERE nom='$nomE' AND prenom='$prenomE'  $sqlsuite2 "; 
		}
		$ins=execSql($sql);
		if ($ins) {
			if ($affectpasswd == 1) {
				return("-2");
			}else{
				return($ins);
			}
		}else{
			return($ins);
		}
	}else{
		$cr=@create_eleve($params,$ascii);
		return($cr);	
	}
}

function modif_photo_pers($nomphoto,$idpers) {
	global $cnx;
        global $prefixe;
	$sql="UPDATE ${prefixe}personnel SET photo='$nomphoto' WHERE pers_id='$idpers'";
	return($ins=execSql($sql));
}

function modif_photo($nomphoto,$ideleve) {
	global $cnx;
        global $prefixe;
	$sql="UPDATE ${prefixe}eleves SET photo='$nomphoto' WHERE elev_id='$ideleve'";
	return($ins=execSql($sql));
}

function purge_messagerie($qui) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}messageries WHERE type_personne_dest='$qui'";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}messagerie_envoyer WHERE type_personne='$qui'";
	execSql($sql);
}

function supprMessageTous($idpers,$date) {
	global $cnx;
	global $prefixe;
	if ($idpers > 0) {
		$sql="DELETE FROM ${prefixe}messageries WHERE destinataire='$idpers' AND date <= '$date'";
		execSql($sql);
	}
}

function purgeDateStage() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}stage_date"; 
	execSql($sql);
}


function purgeAffectationStage() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}stage_eleve"; 
	execSql($sql);
}


function purge_responsable_info_eleve() {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}eleves SET 
		`regime` = '',
		`civ_1` = '',
		`nomtuteur` = '',
		`prenomtuteur` = '',
		`adr1` = '',
		`code_post_adr1` = '',
		`commune_adr1` = '',
		`tel_port_1` = '',
		`civ_2` = '',
		`nom_resp_2` = '',
		`prenom_resp_2` = '',
		`adr2` = '',
		`code_post_adr2` = '',
		`commune_adr2` = '',
		`tel_port_2` = '',
		`telephone` = '',
		`profession_pere` = '',
		`tel_prof_pere` = '',
		`profession_mere` = '',
		`tel_prof_mere`='', 
		`email_eleve` = '',
		`sexe` = '',
		`email`= '',
		`email_resp_2`='',
		`tel_eleve`=''
	";
	execSql($sql);
}

function purge_avertissement($qui) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}avertissement WHERE type_pers='$qui'";
	execSql($sql);
}


function delete_compte_eleve($nom,$prenom,$annenaissance) {
	global $cnx;
	global $prefixe;
	$annenaissance=dateFormBase($annenaissance);
	$sql="DELETE FROM ${prefixe}eleves WHERE nom='$nom' AND prenom='$prenom' AND date_naissance='$annenaissance'";
	execSql($sql);
}

function purge_present() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}present"; 
	execSql($sql);	
}

function purge_edt() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}edt_seances"; 
	execSql($sql);	
}

function purge_abs_sconet() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}absences_sconet"; 
	execSql($sql);
}


function purge_element_eleve() {
		history_cmd("Direction","PURGE","Eleves");
		vide_notes();
		vide_notes_scolaire();
		vide_eleves_sans_classe();
		vide_absences();
		vide_devoir_scolaire();
		vide_discipline_retenue();
		vide_discipline_sanction();
		vide_dispenses();
		vide_retards();
		vide_message_prof_p();
		purge_discipline_prof();
		vide_eleves_agenda();
		purge_groupe();validGroup();
		purgeEleveEtude();
		vide_commentaire_bulletin();
		purge_messagerie("ELE");
		purge_messagerie("PAR");
		purge_avertissement("menuparent");
		purge_avertissement("menueleve");
		vide_eleves();
		purge_rep_membre("./data/rss/menueleve");
		purge_rep_membre("./data/rss/menuparent");
		purge_rep_membre("./data/stockage/menueleve");
		purge_rep_membre("./data/stockage/menuparent");
		purge_carnet_evaluation();
		purge_photographe_de_france();
		purgeEntretien();
		purge_present();
		purge_Versement();
		purge_abs_sconet();
		purge_droitScolarite_eleve();
		if (is_dir("./data/archive")) { nettoyage_repertoire("./data/archive"); }
		purgeCantineEleve();
		purgeEleveHisto();
}


function purgeEleveHisto() {
	global $cnx;
        global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}eleves_histo";
	return(execSql($sql));
}


function htaccess($rep) {
	if (is_dir($rep)) {
		$text="<Files \"*\">\n";
		$text.="Order Deny,Allow\n";
		$text.="Deny from all\n";
		$text.="</Files>";
		@unlink("$rep/.htaccess");
		$fp = fopen("$rep/.htaccess", "w");
		fwrite($fp,$text);
		fclose($fp);
	}
	return true;
}

function purge_photographe_de_france() {
	if (file_exists("./common/config-pdf.php")) {
		include_once("./common/config-pdf.php");
		$idetablissement=IDETABLISSEMENT;
		if (is_dir("./data/$idetablissement/image")) { nettoyage_repertoire("./data/$idetablissement/image"); }	
		@unlink("./data/$idetablissement/image/.htaccess");
		rmdir("./data/$idetablissement/image");
		if (is_dir("./data/$idetablissement")) { nettoyage_repertoire("./data/$idetablissement"); }
		@unlink("./data/$idetablissement/.htaccess");
		rmdir("./data/$idetablissement");
		unlink("./common/config-pdf.php");
	}
}

function purge_carnet_evaluation() {
	global $cnx;
        global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}carnet_evaluation";
	return(execSql($sql));
}

function purge_affectation() {
	global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}affectations";
	return(execSql($sql));
}

function vide_commentaire_bulletin() {
	global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_profp_com";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_prof_com";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_direction_com";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_profp_ue";
	execSql($sql);
}

function purge_discipline_prof() {
	global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}discipline_prof";
	return(execSql($sql));
}

function purgeetudeaffect() {
	global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}etude_param";
	return(execSql($sql));
}

function purgerepertoirerecherche() {
	global $cnx;
        global $prefixe;
	$dir=opendir("./data/recherche/");
	while ($file = readdir($dir)) {
			$file=trim($file);
			if (($file != ".triade") && ($file != ".htaccess" )  && ($file != "." ) && ($file != ".." ) ) {
				$fic="./data/recherche/$file";
				@unlink("$fic");
			}
	}
	closedir($dir);
}


function purge_rep_membre($path) {
	$O = dir($path);
    	if(!is_object($O))
    	return false;
    	while($file = $O -> read()) {
	    if($file != '.' && $file != '..') {
            	if(is_file($path.'/'.$file)) {
		    	$cr=unlink($path.'/'.$file);
		}else{
                	if(is_dir($path.'/'.$file)) {
				purge_rep_membre($path.'/'.$file);
			}
		}
            }
        }
    	// !!!! il faut bien appeler 2 fois la méthode close() !!!
    	$O -> close();
    	$O -> close();
	rmdir($path);
}




function purgeetudeeleve() {
	global $cnx;
    	global $prefixe;
	$sql="DELETE FROM ${prefixe}etude_affect";
	return(execSql($sql));
}

function create_eleve_sans_classe($params,$ascii){
	global $cnx;
    	global $prefixe;

	if ($ascii) { $params[naiss]=dateFormBase($params[naiss]); }

	$sql="SELECT nom FROM ${prefixe}eleves  WHERE nom='$params[ne]' AND prenom='$params[pe]' AND  date_naissance='$params[naiss]' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) { 
		return -3;
	}


	$params[mdp]=cryptage($params[mdp]);
	$params[mdpeleve]=cryptage($params[mdpeleve]);

	$boursier=$params['boursier'];
	if ($boursier == "oui") $boursier='1';
	if ($boursier == "non") $boursier='0';

$sql=<<<EOF
	INSERT INTO ${prefixe}elevessansclasse(
		nom,
		prenom,
		lv1,
		lv2,
		`option`,
		regime,
		date_naissance,
		nationalite,
		passwd,
		passwd_eleve,
		nomtuteur,
		prenomtuteur,
		adr1,
		code_post_adr1,
		commune_adr1,
		adr2,
		code_post_adr2,
		commune_adr2,
		telephone,
		profession_pere,
		tel_prof_pere,
		profession_mere,
		tel_prof_mere,
		nom_etablissement,
		numero_etablissement,
		code_postal_etablissement,
		commune_etablissement,
		numero_eleve,
		email,
		class_ant,
		annee_ant,
		numero_gep,
		civ_1,
		civ_2,
		nom_resp_2,
		prenom_resp_2,
		lieu_naissance,
		tel_port_1,
		tel_port_2,
		valid_forward_mail_eleve,
		valid_forward_mail_parent,
		code_compta,
		sexe,
		email_eleve,
		email_resp_2,
		photo,
		tel_eleve,
		boursier
		)
	VALUES (
		'$params[ne]',
		'$params[pe]',
		'$params[lv1]',
		'$params[lv2]',
		'$params[option]',
		'$params[regime]',
		'$params[naiss]',
		'$params[nat]',
		'$params[mdp]',
		'$params[mdpeleve]',
		'$params[nt]',
		'$params[pt]',
		'$params[adr1]',
		'$params[cpadr1]',
		'$params[commadr1]',
		'$params[adr2]',
		'$params[cpadr2]',
		'$params[commadr2]',
		'$params[tel]',
		'$params[profp]',
		'$params[telprofp]',
		'$params[profm]',
		'$params[telprofm]',
		'$params[nomet]',
		'$params[numet]',
		'$params[cpet]',
		'$params[commet]',
		'$params[numero_eleve]',
		'$params[email]',
		'$params[classe_ant]',
		'$params[annee_ant]',
		'$params[numero_gep]',
		'$params[civ_1]',
		'$params[civ_2]',
		'$params[nom_resp2]',
		'$params[prenom_resp2]',
		'$params[lieunais]',
		'$params[tel_port_1]',
		'$params[tel_port_2]',
		'$params[valid_forward_mail_eleve]',
		'$params[valid_forward_mail_parent]',
		'$params[code_compta]',
		'$params[sexe]',
		'$params[email_eleve]',
		'$params[email_resp_2]',
		'$params[photo]',
		'$params[tel_eleve]',
		'$boursier'
	)

EOF;
       return(execSql($sql));
}


// creation du suppleant
function create_suppleant($nom,$pren,$mdp,$remp,$date_entree,$date_sortie,$civ,$pren2='') {
        global $prefixe;
	// cette fonction nécessite une transaction
	// pour être fiable
	// ex : mauvais pers_id pour vacataire
	// print("ds create_supp<br>");
	global $cnx;
	$date_entree=dateFormBase($date_entree);

	include_once("./common/config2.inc.php");

	if (VERIFPASS == "oui") {

		if (SECURITE == 3) {
			if ( (strlen($mdp) < 8) || (!preg_match('/[a-z]/',$mdp)) || (!preg_match('/[A-Z]/',$mdp)) || (!preg_match('/[0-9]/',$mdp)) ) {
				return -3 ;
			}
		}

		if (SECURITE == 2) {
			if ( (strlen($mdp) < 8) || (!preg_match('/[a-z]/',$mdp)) || (!preg_match('/[0-9]/',$mdp)) ) {
				return -3 ;
			}
		}

		if (SECURITE == 1) {
			if (strlen($mdp) < 4) {
				return -3 ;
			}
		}
	}

	$mdp=cryptage($mdp);
	if($date_sortie == LANGINCONNU ) {
		$date_sortie='NULL';
	} else {
		$date_sortie="'".dateFormBase($date_sortie)."'";
	}

	$sql_ins1="INSERT INTO ${prefixe}personnel(nom,prenom,prenom2,mdp,type_pers,civ) VALUES ('$nom','$pren','$pren2','$mdp','ENS',$civ)";
	execSql($sql_ins1);
	$sql_ins2="SELECT pers_id FROM ${prefixe}personnel WHERE nom='$nom' AND prenom='$pren' AND prenom2='$pren2'";

	if($res=chargeMat(execSql($sql_ins2)))
	{
		$pers_id = $res[0][0];
	}
	else
	{
		return 0;
	}
	if ($pers_id) {
		$sql_ins3="INSERT INTO ${prefixe}vacataires VALUES($pers_id,'$date_entree',$remp,$date_sortie)";
		if(execSql($sql_ins3))
		{
			return(true);
		}
		else
		{
			return(false);
		}
	}

}

function verif_si_suppleant($id_pers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id,rpers_id FROM ${prefixe}vacataires WHERE pers_id='$id_pers' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$rpers_id=$data[0][1];
	if (count($data) > 0) {
		// verif compte encore valide
		$dateaujourdui=dateDMY2();
		$sql="SELECT pers_id,date_sort FROM ${prefixe}vacataires WHERE pers_id='$id_pers' ";
		$res=execSql($sql);
		$data2=chargeMat($res);
		if ((count($data2) > 0) && (($data2[0][1] == "0000-00-00" ) || (trim($data2[0][1]) == "" ) || ($data2[0][1] == null ) )) {
			return $rpers_id;
		}
		if ((count($data2) > 0) && ($data2[0][1] >= $dateaujourdui)) {
			return $rpers_id;
		}
		return("compteexpire");
	}else {
		return $id_pers;
	}
}


function recupIdSuppleant($id_pers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id,rpers_id FROM ${prefixe}vacataires WHERE rpers_id='$id_pers' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$rpers_id=$data[0][1];
	if (count($data) > 0) {
		$dateaujourdui=dateDMY2();
		$sql="SELECT pers_id,date_sort FROM ${prefixe}vacataires WHERE rpers_id='$id_pers' ";
		$res=execSql($sql);
		$data2=chargeMat($res);
		if ((count($data2) > 0) && (($data2[0][1] == "0000-00-00" ) || (trim($data2[0][1]) == "" ) || ($data2[0][1] == null ) )) {	
			return $data2[0][0];
		}
		if ((count($data2) > 0) && ($data2[0][1] >= $dateaujourdui)) {
			return $data2[0][0];
		}
	}
	return $id_pers;
}


function modifDateSuppleant($info,$pers,$idprof) {
	global $cnx;
	global $prefixe;
	$info=dateFormBase($info);
	$sql="UPDATE ${prefixe}vacataires SET date_sort='$info',  rpers_id='$idprof' WHERE pers_id='$pers'";
	execSql($sql);
}

function alertJs($txt) {
print <<<EOF
<script type='text/javaScript'>
alert("$txt");
</script>
EOF;
}

function affPers($type){
	global $cnx;
        global $prefixe;
	$sql="SELECT pers_id, civ, nom, prenom, identifiant, offline, email FROM ${prefixe}personnel WHERE type_pers='$type' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function affPersActif($type){
	global $cnx;
    global $prefixe;
	$sql="SELECT pers_id, civ, nom, prenom, identifiant, offline, email FROM ${prefixe}personnel WHERE type_pers='$type' AND offline='0' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function verifSiProfActif($idprof) {
	global $cnx;
        global $prefixe;
	$sql="SELECT  offline FROM ${prefixe}personnel WHERE type_pers='ENS' AND pers_id=$idprof";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);  // à 1 si inactif
}

function affPersDebutFin($type,$deb,$fin){
	global $cnx;
        global $prefixe;
	$sql="SELECT pers_id, civ, nom, prenom, identifiant FROM ${prefixe}personnel WHERE type_pers='$type' ORDER BY nom LIMIT $deb,$fin";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function affEleve($tri='ele'){
	global $cnx;
	global $prefixe;
	$sqlsuite="";
	if ($tri == "ele") { $sqlsuite="ORDER BY nom"; }
	if ($tri == "cls") { $sqlsuite="ORDER BY classe,nom"; }
	$sql="SELECT elev_id, nom, prenom, classe FROM ${prefixe}eleves  $sqlsuite ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function select_eleve($tri) {
	global $cnx;
	global $prefixe;
	$data=affEleve($tri); //elev_id, nom, prenom, classe
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
	        print "<option id='select1' value='".$data[$i][0]."' title=\"".strtoupper($data[$i][1])." ".$data[$i][2]." (".chercheClasse_nom($data[$i][3]).")\"  >".strtoupper($data[$i][1])." ".$data[$i][2]." (".chercheClasse_nom($data[$i][3]).")</option>\n";
	}
}

function select_eleve_grpmail($tri,$tabliste) {
	global $cnx;
	global $prefixe;
	$data=affEleve($tri); //elev_id, nom, prenom, classe
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
		$selected="";
		foreach($tabliste as $key=>$value) {
			if ($value == $data[$i][0]) {
				$selected="selected='selected'";
				break;
			}
		}
	        print "<option id='select1' $selected  value='".$data[$i][0]."' title=\"".strtoupper($data[$i][1])." ".$data[$i][2]." (".chercheClasse_nom($data[$i][3]).")\"  >".strtoupper($data[$i][1])." ".$data[$i][2]." (".chercheClasse_nom($data[$i][3]).")</option>\n";
	}
}

function affPers_nom($qui){
	global $cnx;
        global $prefixe;
	$sql="SELECT pers_id, civ, nom, prenom FROM ${prefixe}personnel WHERE pers_id='$qui'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}
//-------------------------------------------------------------------------//

function nb_element($table){
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}$table";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0 ) {
		return false;
	}else{
		return true;
	}
}

function affRetard($type,$anneeScolaire=''){
        global $cnx;
        global $prefixe;
        if (nb_element("retards")) { return ;}
        if ($anneeScolaire == "") {
                $dateDebut=recupDateDebutAnnee($_COOKIE["anneeScolaire"]);
                $dateFin=recupDateFinAnnee($_COOKIE["anneeScolaire"]);
        }else{
                $dateDebut=recupDateDebutAnnee($anneeScolaire);
                $dateFin=recupDateFinAnnee($anneeScolaire);
        }
        $sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux , idrattrapage FROM ${prefixe}retards WHERE elev_id='$type' AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ORDER BY date_ret DESC ,heure_ret DESC  ";
        $res=execSql($sql);
        $data_22=chargeMat($res);
        return $data_22;
}


function affRetarddujour($type,$date){
	global $cnx;
	global $prefixe;
	if (nb_element("retards")) { return ;}
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere FROM ${prefixe}retards WHERE elev_id='$type' AND date_ret='$date' ORDER BY date_ret DESC ,heure_ret DESC ";
	$res=execSql($sql);
	$data_22=chargeMat($res);
	return $data_22;
}

function affRetarddujour2($date) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere,justifier, heure_saisie,idprof,idrattrapage,smsenvoye FROM ${prefixe}retards WHERE date_ret='$date' AND (motif = 'inconnu'  OR  trim(duree_ret) = '0' OR justifier!=1 ) ORDER BY elev_id, date_ret DESC ,heure_ret DESC ";
      $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function affRetarddujour3($date) {
        global $cnx;
	global $prefixe;
	if (preg_match('\//',$date)) { $date=dateFormBase($date); }
        $sql="SELECT r.elev_id, r.heure_ret, r.date_ret, r.date_saisie, r.origin_saisie, r.duree_ret, r.motif, r.idmatiere, r.justifier, r.heure_saisie,  r.creneaux , r.smsenvoye FROM ${prefixe}retards r, ${prefixe}eleves e, ${prefixe}classes c WHERE r.elev_id=e.elev_id AND  c.code_class=e.classe AND r.date_ret='$date' ORDER BY c.libelle,e.nom, r.date_ret DESC , r.heure_ret DESC ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}



// nombre de retard
function nombre_retard($type,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	 $sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif FROM ${prefixe}retards WHERE elev_id='$type' AND date_ret >='$dateDebut' AND date_ret <= '$dateFin' ";
	 $res=execSql($sql);
	 $data_2=chargeMat($res);
	 return $data_2;
}



function nombre_abs_matiere($id_eleve,$dateDebut,$dateFin,$idmatiere) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND id_matiere='$idmatiere' ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function nombre_abs_devoir_matiere($idEleve,$dateDebutP1,$dateFinP1,$idmatiere,$idprof,$idgroupe) {
	global $cnx;
	global $prefixe;
	$dateDebutP1=dateFormBase($dateDebutP1);
	$dateFinP1=dateFormBase($dateFinP1);
	$sql="SELECT * FROM ${prefixe}notes WHERE 
		elev_id='$idEleve' 
		AND code_mat='$idmatiere'  
		AND date >= '$dateDebutP1'
		AND date <= '$dateFinP1'
		AND prof_id = '$idprof'
		AND note = '-1' 
		AND ((id_groupe = '$idgroupe') OR (id_groupe = '0')) ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function nombre_retardNonJustifie($type,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	 $sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif FROM ${prefixe}retards WHERE elev_id='$type' AND date_ret >='$dateDebut' AND date_ret <= '$dateFin' AND justifier != '1' ";
	 $res=execSql($sql);
	 $data_2=chargeMat($res);
	 return count($data_2);
}


function nombre_retardJustifie($type,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif FROM ${prefixe}retards WHERE elev_id='$type' AND date_ret >='$dateDebut' AND date_ret <= '$dateFin'  AND justifier = '1' ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return count($data_2);
}



function affRetardNonJustifie($type){
	global $cnx;
	global $prefixe;
	if (nb_element("retards")) { return ;}
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif FROM ${prefixe}retards WHERE elev_id='$type'  AND (duree_ret='0' OR duree_ret='???' OR motif='inconnu' ) ORDER BY date_ret DESC,heure_ret DESC";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function affRetardNonJustifieSms($date){
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, smsenvoye, heure_saisie FROM ${prefixe}retards WHERE date_ret>='$date'  AND (duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR  justifier = '0' ) ORDER BY date_ret DESC,heure_ret DESC";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function affRetardNonJustifie2bis($trie,$idclasse="tous"){
	global $cnx;
	global $prefixe;
	if ($trie == "nom") {
		$trie='e.nom';
	}elseif ($trie == "date") {
		$trie='date_ret DESC,heure_ret DESC';
	}elseif ($trie == "classe") {
		$trie='e.classe';
	}else{
		$trie='e.nom';
	}
	if (($idclasse != 'tous') &&  ($idclasse != '')) {
		$suiteSql="AND e.classe='$idclasse'";
	}
	$sql="SELECT a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, e.nom, e.elev_id, e.classe,a.courrierenvoyer,e.email,e.email_resp_2  FROM ${prefixe}retards  a , ${prefixe}eleves e WHERE (a.duree_ret='0' OR a.duree_ret='???' OR a.motif='inconnu' OR a.justifier!='1')  AND a.elev_id = e.elev_id $suiteSql AND a.courrierenvoyer='0' ORDER BY $trie";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function affRetardNonJustifie2(){
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie FROM ${prefixe}retards WHERE duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR justifier!='1' ORDER BY elev_id, date_ret DESC,heure_ret DESC";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function affRetardNonJustifie22Limit($idClasse,$offset,$limit,$dateDebut,$dateFin){
	global $cnx;
	global $prefixe;
	if ((trim($dateDebut) == "") && (trim($dateFin) == ""))  {
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux FROM ${prefixe}retards WHERE duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR justifier!='1' ORDER BY elev_id, date_ret DESC,heure_ret DESC  LIMIT $limit OFFSET $offset";
		}else{
			$sql="SELECT a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, a.justifier, a.heure_saisie, creneaux FROM ${prefixe}retards a, ${prefixe}eleves e  WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND (a.duree_ret='0' OR a.duree_ret='???' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ret DESC, a.heure_ret DESC  LIMIT $limit OFFSET $offset ";
		}
	}else{
		$dateDebut=dateFormBase($dateDebut);
		$dateFin=dateFormBase($dateFin);
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux FROM ${prefixe}retards WHERE (duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR justifier!='1')  AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin'  ORDER BY elev_id, date_ret DESC,heure_ret DESC  LIMIT $limit OFFSET $offset";
		}else{
			$sql="SELECT a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, a.justifier, a.heure_saisie, creneaux FROM ${prefixe}retards a, ${prefixe}eleves e  WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND (a.duree_ret='0' OR a.duree_ret='???' OR a.motif='inconnu' OR a.justifier!='1' ) AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ORDER BY a.elev_id, a.date_ret DESC, a.heure_ret DESC  LIMIT $limit OFFSET $offset ";
		}
	}
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function nbRtdNonJustifier($idClasse,$dateDebut,$dateFin){
	global $cnx;
	global $prefixe;
	if ((trim($dateDebut) == "") && (trim($dateFin) == ""))  {
		if ($idClasse == "tous") {
			$sql="SELECT * FROM ${prefixe}retards WHERE duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR justifier!='1'";
		}else{
			$sql="SELECT a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, a.justifier, a.heure_saisie FROM ${prefixe}retards a, ${prefixe}eleves e  WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND (a.duree_ret='0' OR a.duree_ret='???' OR a.motif='inconnu' OR a.justifier!='1' )";
		}
	}else{
		$dateDebut=dateFormBase($dateDebut);
		$dateFin=dateFormBase($dateFin);
		if ($idClasse == "tous") {
			$sql="SELECT * FROM ${prefixe}retards WHERE (duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR justifier!='1') AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ";
		}else{
			$sql="SELECT a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, a.justifier, a.heure_saisie FROM ${prefixe}retards a, ${prefixe}eleves e  WHERE  date_ret >= '$dateDebut' AND date_ret <= '$dateFin' AND (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND (a.duree_ret='0' OR a.duree_ret='???' OR a.motif='inconnu' OR a.justifier!='1' )";
		}
	}
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return count($data_2);
}


//-------------------------------------------------------------------------//
// afficher devoir scolaires profs
function affdevoirScolaire($clsorgrp,$sMat,$idclsorgrp) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id FROM ${prefixe}devoir_scolaire WHERE classorgrp='$clsorgrp' AND id_class_or_grp='$idclsorgrp' AND matiere_id='$sMat' ORDER BY date_devoir,heure_saisie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}
function affdevoirScolaireTotal($clsorgrp,$idclsorgrp,$date) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number FROM ${prefixe}devoir_scolaire WHERE id_class_or_grp='$idclsorgrp' AND date_devoir='$date'  ORDER BY date_devoir,heure_saisie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function vide_devoir_classe($id) {
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}devoir_scolaire WHERE id_class_or_grp='$id'";
        return(execSql($sql));
}

//-------------------------------------------------------------------------//
// afficher devoir scolaires parent
function affdevoirScolaireParent($idClasse,$date,$ref) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);

	$sql="SELECT liste_id_classe,id FROM ${prefixe}devoir_scolaire WHERE classorgrp='1' AND $ref='$date' ";
	$res=execSql($sql);
	$data_3=chargeMat($res);
	for($i=0;$i<count($data_3);$i++) {
		$liste_id_classe=$data_3[$i][0];
		$id=$data_3[$i][1];
		$liste_id_classe=preg_replace('/\{/','',$liste_id_classe);
		$liste_id_classe=preg_replace('/\}/','',$liste_id_classe);
		$tab=preg_split('/,/',$liste_id_classe);
		foreach($tab as $key=>$value) {
			if ($value == $idClasse) {
				$liste_id.="'$id',";
				break;
			}
		}		
	}

	if ($liste_id != "") {
		$liste_id=preg_replace('/,$/','',$liste_id);
		$sqlsuite=" OR id IN ($liste_id)";
	}

	$sql="SELECT id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number, idprof,tempsestimedevoir FROM ${prefixe}devoir_scolaire WHERE id_class_or_grp='$idClasse' AND $ref='$date' $sqlsuite  ORDER BY date_devoir,heure_saisie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function affdevoirScolaireProf($idProf,$date,$ref,$classorgrp) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT liste_id_classe,id FROM ${prefixe}devoir_scolaire WHERE classorgrp='1' AND $ref='$date' ";
	$res=execSql($sql);
	$data_3=chargeMat($res);
	for($i=0;$i<count($data_3);$i++) {
		$liste_id_classe=$data_3[$i][0];
		$id=$data_3[$i][1];
		$liste_id_classe=preg_replace('/\{/','',$liste_id_classe);
		$liste_id_classe=preg_replace('/\}/','',$liste_id_classe);
		$tab=preg_split('/,/',$liste_id_classe);
		foreach($tab as $key=>$value) {
			if ($value == $idClasse) {
				$liste_id.="'$id',";
				break;
			}
		}		
	}

	if ($liste_id != "") {
		$liste_id=preg_replace('/,$/','',$liste_id);
		$sqlsuite=" OR id IN ($liste_id)";
	}
	$sql="SELECT id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number,idprof,tempsestimedevoir FROM ${prefixe}devoir_scolaire WHERE idprof='$idProf' AND $ref='$date' AND classorgrp='$classorgrp' $sqlsuite ORDER BY date_devoir,heure_saisie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function affcontenuScolaireProf($idProf,$date,$ref,$classorgrp) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT liste_id_classe,id FROM ${prefixe}cahiertexte WHERE classorgrp='1' AND $ref='$date' ORDER BY date_contenu,heure_saisie";
	$res=execSql($sql);
	$data_3=chargeMat($res);
	for($i=0;$i<count($data_3);$i++) {
		$liste_id_classe=$data_3[$i][0];
		$id=$data_3[$i][1];
		$liste_id_classe=preg_replace('/\{/','',$liste_id_classe);
		$liste_id_classe=preg_replace('/\}/','',$liste_id_classe);
		$tab=preg_split('/,/',$liste_id_classe);
		foreach($tab as $key=>$value) {
			if ($value == $idClasse) {
				$liste_id.="'$id',";
				break;
			}
		}		
	}

	if ($liste_id != "") {
		$liste_id=preg_replace('/,$/','',$liste_id);
		$sqlsuite=" OR id IN ($liste_id)";
	}
	$sql="SELECT id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_contenu, contenu, classorgrp, id, number, idprof FROM ${prefixe}cahiertexte WHERE idprof='$idProf' AND $ref='$date'  AND classorgrp='$classorgrp' $sqlsuite ORDER BY date_contenu,heure_saisie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function affobjectifScolaireProf($idProf,$date,$ref,$classorgrp) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT liste_id_classe,id FROM ${prefixe}cahiertexte WHERE classorgrp='1' AND $ref='$date' ORDER BY date_contenu,heure_saisie";
	$res=execSql($sql);
	$data_3=chargeMat($res);
	for($i=0;$i<count($data_3);$i++) {
		$liste_id_classe=$data_3[$i][0];
		$id=$data_3[$i][1];
		$liste_id_classe=preg_replace('/\{/','',$liste_id_classe);
		$liste_id_classe=preg_replace('/\}/','',$liste_id_classe);
		$tab=preg_split('/,/',$liste_id_classe);
		foreach($tab as $key=>$value) {
			if ($value == $idClasse) {
				$liste_id.="'$id',";
				break;
			}
		}		
	}

	if ($liste_id != "") {
		$liste_id=preg_replace('/,$/','',$liste_id);
		$sqlsuite=" OR id IN ($liste_id)";
	}
	$sql="SELECT id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_contenu, objectif, classorgrp, id, number, idprof FROM ${prefixe}cahiertexte WHERE idprof='$idProf' AND $ref='$date'  AND classorgrp='$classorgrp' $sqlsuite  ORDER BY date_contenu,heure_saisie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function affcontenuScolaireParent($idClasse,$date,$ref) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);

	$sql="SELECT liste_id_classe,id FROM ${prefixe}cahiertexte WHERE classorgrp='1' AND $ref='$date' ORDER BY date_contenu,heure_saisie";
	$res=execSql($sql);
	$data_3=chargeMat($res);
	for($i=0;$i<count($data_3);$i++) {
		$liste_id_classe=$data_3[$i][0];
		$id=$data_3[$i][1];
		$liste_id_classe=preg_replace('/\{/','',$liste_id_classe);
		$liste_id_classe=preg_replace('/\}/','',$liste_id_classe);
		$tab=preg_split('/,/',$liste_id_classe);
		foreach($tab as $key=>$value) {
			if ($value == $idClasse) {
				$liste_id.="'$id',";
				break;
			}
		}		
	}

	if ($liste_id != "") {
		$liste_id=preg_replace('/,$/','',$liste_id);
		$sqlsuite=" OR id IN ($liste_id)";
	}

	$sql="SELECT id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_contenu, contenu, classorgrp, id, number, idprof FROM ${prefixe}cahiertexte WHERE id_class_or_grp='$idClasse' AND $ref='$date' $sqlsuite ORDER BY date_contenu,heure_saisie";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}





function exportPDF_contobj_cahiertext($dateDebut,$dateFin,$ref,$idprof,$idmatiere,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT date_saisie, heure_saisie, date_contenu, contenu, objectif  FROM ${prefixe}cahiertexte WHERE $ref>='$dateDebut' AND $ref<='$dateFin' AND matiere_id='$idmatiere' AND idprof='$idprof' AND id_class_or_grp='$idclasse' ORDER BY date_contenu,heure_saisie";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function exportPDF_devoir_cahiertext($dateDebut,$dateFin,$ref,$idprof,$idmatiere,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT date_saisie, heure_saisie, date_devoir, texte FROM ${prefixe}devoir_scolaire WHERE $ref>='$dateDebut' AND $ref<='$dateFin' AND idprof='$idprof' AND id_class_or_grp='$idclasse'    AND matiere_id='$idmatiere'  ORDER BY date_devoir,heure_saisie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function RechercheCommentaireCahiertexte($id,$type) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT contenu,objectif FROM ${prefixe}cahiertexte WHERE id='$id'";
	if ($type == 0) {
		$res=execSql($sql);
		$data_2=chargeMat($res);
		return $data_2[0][0];
	}
	if ($type == 2) {
		$res=execSql($sql);
		$data_2=chargeMat($res);
		return $data_2[0][1];
	}
	if ($type == 1) {
		$sql="SELECT texte FROM ${prefixe}devoir_scolaire WHERE id='$id'";
		$res=execSql($sql);
		$data_2=chargeMat($res);
		return $data_2[0][0];
	}
}

function enrVisaCahierdeText($idclasse,$idmatiere,$datedebut,$datefin,$classorgrp,$idprof) {
	global $cnx;
	global $prefixe;
	$datedebut=dateFormBase($datedebut);
	$datefin=dateFormBase($datefin);
	$sql="UPDATE ${prefixe}cahiertexte SET visadirecteur='1'  WHERE idprof='$idprof' AND  id_class_or_grp='$idclasse' AND classorgrp='$classorgrp' AND matiere_id='$idmatiere' AND  date_contenu <='$datedebut' AND date_contenu >='$datefin' ";
	execSql($sql);
	$sql="UPDATE ${prefixe}devoir_scolaire SET visadirecteur='1'  WHERE idprof='$idprof' AND id_class_or_grp='$idclasse' AND classorgrp='$classorgrp' AND matiere_id='$idmatiere' AND  date_devoir <='$datedebut' AND date_devoir >='$datefin' ";
	execSql($sql);
	return(true);
}

function SaveDevoir($idDevoir,$commentaire,$type) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$time=dateHIS();
	if ($type == 0) { $type="contenu"; }
	if ($type == 2) { $type="objectif"; }
	if (($type == 0) || ($type == 2)) {
		$sql="UPDATE ${prefixe}cahiertexte SET $type='$commentaire', date_saisie='$date', heure_saisie='$time' WHERE id='$idDevoir'";
		return(execSql($sql));
	}
	if ($type == 1) {
		$sql="UPDATE ${prefixe}devoir_scolaire SET texte='$commentaire', date_saisie='$date', heure_saisie='$time' WHERE id='$idDevoir'";
		return(execSql($sql));
	}
}

function SuppDevoir($idDevoir,$type) {
	global $cnx;
	global $prefixe;
	if ($type == 0) {
		$sql="UPDATE  ${prefixe}cahiertexte SET contenu='' WHERE id='$idDevoir'";
		return(execSql($sql));
	}
	if ($type == 2) {
		$sql="UPDATE  ${prefixe}cahiertexte SET objectif='' WHERE id='$idDevoir'";
		return(execSql($sql));
	}
	if ($type == 1) {
		$sql="SELECT number  FROM ${prefixe}devoir_scolaire WHERE id='$idDevoir'";
		$res=execSql($sql);
		$data=chargeMat($res);
		if ($data[0][0] != "") { @unlink("./data/DevoirScolaire/".$data[0][0]); }
		$sql="DELETE FROM ${prefixe}devoir_scolaire WHERE id='$idDevoir'";
		return(execSql($sql));
	}
}


function affobjectifScolaireParent($idClasse,$date,$ref) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);

	$sql="SELECT liste_id_classe,id FROM ${prefixe}cahiertexte WHERE classorgrp='1' AND $ref='$date' ORDER BY date_contenu,heure_saisie";
	$res=execSql($sql);
	$data_3=chargeMat($res);
	for($i=0;$i<count($data_3);$i++) {
		$liste_id_classe=$data_3[$i][0];
		$id=$data_3[$i][1];
		$liste_id_classe=preg_replace('/\{/','',$liste_id_classe);
		$liste_id_classe=preg_replace('/\}/','',$liste_id_classe);
		$tab=preg_split('/,/',$liste_id_classe);
		foreach($tab as $key=>$value) {
			if ($value == $idClasse) {
				$liste_id.="'$id',";
				break;
			}
		}		
	}

	if ($liste_id != "") {
		$liste_id=preg_replace('/,$/','',$liste_id);
		$sqlsuite=" OR id IN ($liste_id)";
	}

	$sql="SELECT id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_contenu, objectif, classorgrp, id, number_obj, idprof FROM ${prefixe}cahiertexte WHERE id_class_or_grp='$idClasse' AND $ref='$date' $sqlsuite  ORDER BY date_contenu,heure_saisie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function affdevoirScolaireParent_7j($idClasse,$date,$date2) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number  FROM ${prefixe}devoir_scolaire WHERE id_class_or_grp='$idClasse' AND date_devoir >= '$date' AND date_devoir<='$date2' ORDER BY date_devoir,heure_saisie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}
//-------------------------------------------------------------------------//
// afficher les dispenses
function affDispence($type){
        global $cnx;
        global $prefixe;
        $dateDebut=recupDateDebutAnnee($_COOKIE["anneeScolaire"]);
        $dateFin=recupDateFinAnnee($_COOKIE["anneeScolaire"]);
        $sql="SELECT elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3 FROM ${prefixe}dispenses WHERE elev_id='$type' AND date_debut >= '$dateDebut' AND date_fin <= '$dateFin'  ORDER BY date_debut DESC";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        return $data_2;
}

function affDispence2($id_eleve) {
	global $cnx;
	global $prefixe;
	$datej=dateDMY2();
	$day=date("D");
	//Tuesday Wednesday Thursday Friday
	if ($day == "Mon") { $jours="Lundi"; }
	if ($day == "Tue") { $jours="Mardi"; }
	if ($day == "Wed") { $jours="Mercredi"; }
	if ($day == "Thu") { $jours="Jeudi"; }
	if ($day == "Fri") { $jours="Vendredi"; }
	if ($day == "Sat") { $jours="Samedi"; }
	$sql="SELECT elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3 FROM ${prefixe}dispenses WHERE elev_id='$id_eleve' AND date_debut<='$datej' AND date_fin>='$datej' AND ( jour1='$jours' OR jour2='$jours' OR jour3='$jours' )  ORDER BY date_debut DESC";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function suppCaracFichier($chaine) {
	$chaine=str_replace(":","_",$chaine);
	$chaine=str_replace("@","_",$chaine);
	$chaine=str_replace("/","_",$chaine);
	$chaine=str_replace('\\',"_",$chaine);
	$chaine=str_replace(";","_",$chaine);
	$chaine=str_replace(".","_",$chaine);
	$chaine=str_replace("!","_",$chaine);
	$chaine=str_replace("(","_",$chaine);
	$chaine=str_replace(")","_",$chaine);
	$chaine=str_replace("&","_",$chaine);
	$chaine=str_replace(":","_",$chaine);
	$chaine=str_replace(" ","_",$chaine);
	$chaine=str_replace("?","_",$chaine);
	return $chaine;
}


	
	
function affDispence3($date) {
        global $cnx;
	global $prefixe;
        $datej=dateFormBase($date);
        $day=date("D");
        //Tuesday Wednesday Thursday Friday
        if ($day == "Mon") { $jours="Lundi"; }
        if ($day == "Tue") { $jours="Mardi"; }
        if ($day == "Wed") { $jours="Mercredi"; }
        if ($day == "Thu") { $jours="Jeudi"; }
        if ($day == "Fri") { $jours="Vendredi"; }
        if ($day == "Sat") { $jours="Samedi"; }
        if ($day == "Sun") { $jours="Dimanche"; }
        $sql="SELECT r.elev_id, r.code_mat, r.date_debut, r.date_fin, r.date_saisie, r.origin_saisie, r.certificat, r.motif, r.heure1, r.jour1, r.heure2, r.jour2, r.heure3, r.jour3 FROM ${prefixe}dispenses r, ${prefixe}eleves e, ${prefixe}classes c WHERE  r.elev_id=e.elev_id AND  c.code_class=e.classe AND r.date_debut<='$datej' AND r.date_fin>='$datej' AND ( r.jour1='$jours' OR r.jour2='$jours' OR r.jour3='$jours' )  ORDER BY c.libelle, e.nom, r.date_debut DESC";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        return $data_2;
}

function dejadisp($id_eleve) {
	global $cnx;
	global $prefixe;
	$datej=dateDMY2();
	$day=date("D");
	//Tuesday Wednesday Thursday Friday
	if ($day == "Mon") { $jours="Lundi"; }
	if ($day == "Tue") { $jours="Mardi"; }
	if ($day == "Wed") { $jours="Mercredi"; }
	if ($day == "Thu") { $jours="Jeudi"; }
	if ($day == "Fri") { $jours="Vendredi"; }
	if ($day == "Sat") { $jours="Samedi"; }
	$sql="SELECT elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3 FROM ${prefixe}dispenses WHERE elev_id='$id_eleve' AND date_debut<='$datej' AND date_fin>='$datej' AND ( jour1='$jours' OR jour2='$jours' OR jour3='$jours' )  ORDER BY date_debut DESC";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function dejadispViaDate($id_eleve,$date) {
	global $cnx;
	global $prefixe;
	$datej=dateFormBase($date);
        $elements=preg_split('/\//',$date);
        $annee=$elements[2];
        $mois=$elements[1];
        $jour=$elements[0];
        $resultat=mktime(0,0,0,$mois,$jour,$annee);
        $day=strftime("%a",$resultat);
	//Tuesday Wednesday Thursday Friday
	if ($day == "Mon") { $jours="Lundi"; }
	if ($day == "Tue") { $jours="Mardi"; }
	if ($day == "Wed") { $jours="Mercredi"; }
	if ($day == "Thu") { $jours="Jeudi"; }
	if ($day == "Fri") { $jours="Vendredi"; }
	if ($day == "Sat") { $jours="Samedi"; }
	$sql="SELECT elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3 FROM ${prefixe}dispenses WHERE elev_id='$id_eleve' AND date_debut<='$datej' AND date_fin>='$datej' AND ( jour1='$jours' OR jour2='$jours' OR jour3='$jours' )  ORDER BY date_debut DESC";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}





//-------------------------------------------------------------------------//
// afficher les  absences
function affAbsence($type,$anneeScolaire=''){
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == "") {
                $dateDebut=recupDateDebutAnnee($_COOKIE["anneeScolaire"]);
                $dateFin=recupDateFinAnnee($_COOKIE["anneeScolaire"]);
        }else{
                $dateDebut=recupDateDebutAnnee($anneeScolaire);
                $dateFin=recupDateFinAnnee($anneeScolaire);
        }
        $sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time, justifier, heure_saisie, heuredabsence, creneaux, smsenvoye, idrattrapage FROM ${prefixe}absences WHERE elev_id='$type' AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin'  limit 1";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 0) {
                $sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time, justifier,  heure_saisie, heuredabsence, creneaux, smsenvoye , idrattrapage FROM ${prefixe}absences WHERE elev_id='$type' AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' ORDER BY date_ab DESC , heuredabsence DESC";
                $res=execSql($sql);
                $data_2=chargeMat($res);
                return $data_2;
        }
}


// recherche le nombre d'absence
function nombre_abs($id_eleve,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin'";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

// recherche le nombre d'absence non justifié
function nombre_absNonJustifie($id_eleve,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND justifier != '1' ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	for($i=0;$i<count($data_2);$i++) {
		if ($data_2[$i][4] > 0) {
			$nb=$nb+$data_2[$i][4];
		}
	}
	return $nb*2;
}

function nombre_absJustifieTypeAbs($id_eleve,$dateDebut,$dateFin,$typeAbs) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND justifier = '1' AND motif='$typeAbs' ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	for($i=0;$i<count($data_2);$i++) {
		if ($data_2[$i][4] > 0) {
			$nb=$nb+$data_2[$i][4];
		}
	}
	return $nb*2;
}


function absEleve($dateDebut,$jour,$heure,$idEleve,$pdf) {
	global $cnx;
	global $prefixe;
	$pdf->SetFillColor(255);
	$dateDebut=dateplusn(dateForm($dateDebut),$jour) ;
	$sql="SELECT justifier,duree_heure FROM ${prefixe}absences WHERE elev_id='$idEleve' AND date_ab ='$dateDebut' AND duree_ab='-1' AND heuredabsence LIKE '$heure%'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$pdf->SetFillColor(255,204,0);
	}

	$sql="SELECT justifier FROM ${prefixe}absences WHERE elev_id='$idEleve' AND date_ab <= '$dateDebut' AND date_fin >= '$dateDebut' AND duree_ab != '-1'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$pdf->SetFillColor(255,204,0);
	}

	$sql="SELECT justifier FROM ${prefixe}retards WHERE elev_id='$idEleve' AND date_ret ='$dateDebut' AND heure_ret LIKE '$heure%' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$pdf->SetFillColor(79,176,145);
	}
}

function absInfoEleve($dateDebut,$jour,$heure,$idEleve) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateplusn(dateForm($dateDebut),$jour) ;
	$sql="SELECT justifier FROM ${prefixe}absences WHERE elev_id='$idEleve' AND date_ab ='$dateDebut' AND heuredabsence LIKE '$heure%' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		if ($data[0][0] == 0) {
			return "?";
		}else{
			return "A";
		}
	}

	$sql="SELECT justifier FROM ${prefixe}absences WHERE elev_id='$idEleve' AND date_ab <= '$dateDebut' AND date_fin >= '$dateDebut' AND duree_ab != '-1'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		if ($data[0][0] == 0) {
			return "?";
		}else{
			return "A";
		}
	}

	$sql="SELECT justifier FROM ${prefixe}retards WHERE elev_id='$idEleve' AND date_ret ='$dateDebut' AND heure_ret LIKE '$heure%' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		if ($data[0][0] == 1) {
			return "R";
		}else{
			return "?";
		}
	}
	
}



function nbrEleveAbsCantineAujourdhui($tabetudiant) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$nb=0;
	foreach($tabetudiant as $key=>$idEleve) {
		$sql="SELECT COUNT(a.date_ab) FROM ${prefixe}absences a, ${prefixe}eleves e WHERE a.date_ab >= '$date' AND a.date_fin <='$date' AND a.elev_id=e.elev_id  AND duree_ab != '-1' AND a.elev_id='$idEleve' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		if ($data[0][0] > 0) {
			$nb++;
		}
	}
	return($nb);

}



function affAbsNonJustif($type){
	global $cnx;
	global $prefixe;
	if (nb_element("absences")) { return ;}
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie FROM ${prefixe}absences WHERE elev_id='$type' AND (duree_ab='0' OR motif='inconnu') ORDER BY date_ab DESC ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function affAbsNonJustifSms($date){
	global $cnx;
	global $prefixe;
	if (nb_element("absences")) { return ;}
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, smsenvoye FROM ${prefixe}absences WHERE date_ab>='$date' AND (duree_ab='0' OR motif='inconnu' OR justifier = '0' ) ORDER BY elev_id, date_ab DESC";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function affAbsNonJustif2(){
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie FROM ${prefixe}absences WHERE duree_ab='0' OR motif='inconnu' OR justifier!='1' ORDER BY elev_id, date_ab DESC ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function affAbsNonJustif22($idClasse){
	global $cnx;
	global $prefixe;
	if ($idClasse == "tous") {
		$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie , creneaux FROM ${prefixe}absences WHERE duree_ab='0' OR motif='inconnu' OR justifier!='1' ORDER BY elev_id, date_ab DESC ";
		$res=execSql($sql);
		$data_2=chargeMat($res);
		return $data_2;
	}else{
		$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, a.justifier, a.heure_saisie, a.creneaux FROM ${prefixe}absences a, ${prefixe}eleves e WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND ( a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ab DESC ";
		$res=execSql($sql);
		$data_2=chargeMat($res);
		return $data_2;
	}
}

function affAbsNonJustif22Limit($idClasse,$offset,$limit,$dateDebut,$dateFin){
	global $cnx;
	global $prefixe;
	if ((trim($dateDebut) == "") && (trim($dateFin) == ""))  {
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie , creneaux,smsenvoye FROM ${prefixe}absences WHERE duree_ab='0' OR motif='inconnu' OR justifier!='1' ORDER BY elev_id, date_ab DESC LIMIT $limit OFFSET $offset";
		}else{
			$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, a.justifier, a.heure_saisie, a.creneaux,a.smsenvoye FROM ${prefixe}absences a, ${prefixe}eleves e WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND ( a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ab DESC LIMIT $limit OFFSET $offset ";
		}		
		$res=execSql($sql);
		$data_2=chargeMat($res);
		return $data_2;
	}else{
		$dateDebut=dateFormBase($dateDebut);
		$dateFin=dateFormBase($dateFin);
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie , creneaux,smsenvoye FROM ${prefixe}absences WHERE (duree_ab='0' OR motif='inconnu' OR justifier!='1') AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' ORDER BY elev_id, date_ab DESC LIMIT $limit OFFSET $offset";
		}else{
			$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, a.justifier, a.heure_saisie, a.creneaux,a.smsenvoye FROM ${prefixe}absences a, ${prefixe}eleves e WHERE  a.date_ab >= '$dateDebut' AND a.date_ab <= '$dateFin' AND (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND ( a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ab DESC LIMIT $limit OFFSET $offset ";
		}		
		$res=execSql($sql);
		$data_2=chargeMat($res);
		return $data_2;
	}
}
 

function nbAbsNonJustifier($idClasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	if ((trim($dateDebut) == "") && (trim($dateFin) == ""))  {
		if ($idClasse == "tous") {
			$sql="SELECT * FROM ${prefixe}absences WHERE duree_ab='0' OR motif='inconnu' OR justifier!='1' ";
		}else{
			$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, a.justifier, a.heure_saisie, a.creneaux FROM ${prefixe}absences a, ${prefixe}eleves e WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND ( a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1' )  ";
		}		
	}else{
		$dateDebut=dateFormBase($dateDebut);
		$dateFin=dateFormBase($dateFin);
		if ($idClasse == "tous") {
			$sql="SELECT * FROM ${prefixe}absences WHERE (duree_ab='0' OR motif='inconnu' OR justifier!='1') AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' ";
		}else{
			$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, a.justifier, a.heure_saisie, a.creneaux FROM ${prefixe}absences a, ${prefixe}eleves e WHERE  a.date_ab >= '$dateDebut' AND a.date_ab <= '$dateFin' AND (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND ( a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1' )  ";
		}	

	}
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return count($data_2);
}


function affAbsNonJustif2bis($trie,$idclasse="tous"){
	global $cnx;
	global $prefixe;
	if ($trie == "nom") {
		$trie='e.nom';
	}elseif ($trie == "date") {
		$trie='a.date_ab DESC';
	}elseif ($trie == "classe") {
		$trie='e.classe ';
	}else{
		$trie='e.nom';
	}
	if (($idclasse != 'tous') &&  ($idclasse != '')) {
		$suiteSql="AND e.classe='$idclasse'";
	}
	$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, e.nom, e.elev_id, e.classe, a.courrierenvoyer,e.email,e.email_resp_2,e.email_eleve FROM ${prefixe}absences a , ${prefixe}eleves e WHERE (a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1') AND a.elev_id = e.elev_id $suiteSql AND a.courrierenvoyer = '0' ORDER BY $trie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function affAbsJustif2bis($trie,$idclasse="tous"){
	global $cnx;
	global $prefixe;
	if ($trie == "nom") {
		$trie='e.nom';
	}elseif ($trie == "date") {
		$trie='a.date_ab DESC';
	}elseif ($trie == "classe") {
		$trie='e.classe ';
	}else{
		$trie='e.nom';
	}
	if (($idclasse != 'tous') &&  ($idclasse != '')) {
		$suiteSql="AND e.classe='$idclasse'";
	}
	$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, e.nom, e.elev_id, e.classe, a.courrierenvoyer,e.email,e.email_resp_2, e.email_eleve  FROM ${prefixe}absences a , ${prefixe}eleves e WHERE (a.duree_ab='0' OR a.motif='inconnu' OR a.justifier='1') AND a.elev_id = e.elev_id $suiteSql AND a.courrierenvoyer = '0' ORDER BY $trie ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

//-------------------------------------------------------------------------//
// afficher les  absences
function affAbsence2($id_eleve,$datej){
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time,  heuredabsence, justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab<='$datej' AND date_fin>='$datej' ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function affAbsence3($datej){
        global $cnx;
	global $prefixe;
        $sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie, idprof, heuredabsence, idrattrapage, smsenvoye FROM ${prefixe}absences WHERE date_ab<='$datej' AND date_fin>='$datej' AND (motif = 'inconnu' OR duree_ab='0' OR justifier!=1 )";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        return $data_2;
}


function affAbsence4($datej){
        global $cnx;
	global $prefixe;
	if (preg_match('/\//',$datej)) { $datej=dateFormBase($datej); }
	$sql="SELECT r.elev_id, r.date_ab, r.date_saisie, r.origin_saisie, r.duree_ab ,r.date_fin, r.motif, r.duree_heure, r.id_matiere, r.heure_saisie, r.justifier, r.heuredabsence, r.creneaux, r.smsenvoye  FROM ${prefixe}absences r, ${prefixe}eleves e, ${prefixe}classes c WHERE  r.elev_id=e.elev_id AND  c.code_class=e.classe AND r.date_ab<='$datej' AND r.date_fin>='$datej' ORDER BY c.libelle, e.nom ";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        return $data_2;
}



//-------------------------------------------------------------------------//
function dejaabs($id_eleve) {
	global $cnx;
	global $prefixe;
	$datej=dateDMY2();
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab<='$datej' AND date_fin>='$datej' AND  (duree_ab != '-1' AND duree_ab != '0')";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function dejaabsviaDate($id_eleve,$date) {
	global $cnx;
	global $prefixe;
	$datej=dateFormBase($date);
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab<='$datej' AND date_fin>='$datej' AND  (duree_ab != '-1' AND duree_ab != '0')";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}



function cherchemail($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT email FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data[0][0];
}

function cherchemailparent($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT email FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data[0][0];
}

function recup_date_naissance_eleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT date_naissance FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return dateForm($data[0][0]);
}


function update_mail($email,$membre,$id_pers) {
	global $cnx;
	global $prefixe;

	if ($membre == "menueleve") {
		if (VATEL == 1) {
			$sql="UPDATE ${prefixe}eleves SET emailpro_eleve='$email' WHERE elev_id='$id_pers' ";
		}else{
			$sql="UPDATE ${prefixe}eleves SET email_eleve='$email' WHERE elev_id='$id_pers' ";
		}
	}
	if ($membre == "menuparent") {
		$sql="UPDATE ${prefixe}eleves SET email='$email' WHERE elev_id='$id_pers' ";
	}
	if (($membre == "menuprof") || ($membre == "menuscolaire") || ($membre == "menuadmin") || ($membre == "menututeur") || ($membre == "menupersonnel") ) {
		$sql="UPDATE ${prefixe}personnel SET email='$email' WHERE pers_id='$id_pers' ";
	}
	if ($sql != "") {
		return(execSql($sql));
	}
}

function cherchemaileleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT email_eleve FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data[0][0];
}

function cherchemailelevePro($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT emailpro_eleve FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data[0][0];
}

function cherchemailpersonnel($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT email FROM ${prefixe}personnel WHERE pers_id='$idpers' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data[0][0];
}


function chercheAdressepersonnel($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT adr FROM ${prefixe}personnel WHERE pers_id='$idpers' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	$data[0][0];
}

function chercheCCPpersonnel($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_post FROM ${prefixe}personnel WHERE pers_id='$idpers' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	$data[0][0];
}

function chercheVillepersonnel($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT commune FROM ${prefixe}personnel WHERE pers_id='$idpers' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	$data[0][0];
}

function chercheTelpersonnel($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT tel FROM ${prefixe}personnel WHERE pers_id='$idpers' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	$data[0][0];
}


function chercheadresse($id_eleve) {
        global $cnx;
        global $prefixe;
        $sql="SELECT elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance,email_eleve,adr_eleve,ccp_eleve,commune_eleve  FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
} 

function cherchetelportable1($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  tel_port_1 FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return trim($data[0][0]);
	}
}


function chercheNumeroEleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  numero_eleve FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return trim($data[0][0]);
	}
}


function chercheIdEleveViaNumeroEleve($numEleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  elev_id FROM ${prefixe}eleves WHERE numero_eleve='$numEleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return trim($data[0][0]);
	}
}

function chercheIdEleveViaNomPrenom($nom,$prenom) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  elev_id FROM ${prefixe}eleves WHERE lower(nom)='$nom' AND lower(prenom) LIKE '$prenom%' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return trim($data[0][0]);
	}
}


function cherchetelportable2($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  tel_port_2 FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return trim($data[0][0]);
	}
}

function cherchetel($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom,prenom,classe,elev_id,telephone,tel_prof_pere,tel_prof_mere FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return trim($data[0][4]);
	}
}

function cherchetelEleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom,prenom,classe,elev_id,tel_eleve FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return trim($data[0][4]);
	}
}

function cherchetelpere($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom,prenom,classe,elev_id,telephone,tel_prof_pere,tel_prof_mere FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return trim($data[0][5]);
	}
}
function cherchetelmere($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom,prenom,classe,elev_id,telephone,tel_prof_pere,tel_prof_mere FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return trim($data[0][6]);
	}
}

//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
// afficher le nombre de sanction pour les retenues
function affSanction_nb_retenue(){
	global $cnx;
	global $prefixe;
	$sql="SELECT sanction,nb,origin_user,date_saisie FROM ${prefixe}type_nb_sanction ORDER BY date_saisie DESC";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}
//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
// afficher de sanction pour un eleve
function affSanction_par_eleve_trimestre($id_eleve,$dateDebut,$dateFin){
	global $cnx;
	global $prefixe;
	$sql="SELECT id,id_eleve,motif,id_category,date_saisie,origin_saisie,signature_parent,attribuer_par,devoir_a_faire,description_fait FROM ${prefixe}discipline_sanction WHERE id_eleve='$id_eleve' AND date_saisie >='$dateDebut' AND date_saisie <= '$dateFin' ORDER BY date_saisie DESC";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}
//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
// afficher de sanction pour un eleve
function affSanction_par_eleve($id_eleve){
        global $cnx;
        global $prefixe;
        $dateDebut=recupDateDebutAnnee($_COOKIE["anneeScolaire"]);
        $dateFin=recupDateFinAnnee($_COOKIE["anneeScolaire"]);
        $sql="SELECT id,id_eleve,motif,id_category,date_saisie,origin_saisie,signature_parent,attribuer_par,devoir_a_faire,description_fait FROM ${prefixe}discipline_sanction WHERE id_eleve='$id_eleve' AND date_saisie >= '$dateDebut' AND date_saisie <= '$dateFin'  ORDER BY date_saisie DESC";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function modifSanction($id,$sanction,$motif,$datesaisie){
	global $cnx;
	global $prefixe;
	$datesaisie=dateFormBase($datesaisie);
	$sql="UPDATE ${prefixe}discipline_sanction SET id_category='$sanction', motif='$motif' ,   date_saisie='$datesaisie' WHERE id='$id'";
	return(execSql($sql));
}


function cherche_sanction_day($ideleve) {
	global $cnx;
	global $prefixe;
	$date=date("Y-m-d");
	$sql="SELECT id,id_eleve,motif,id_category,date_saisie,origin_saisie,signature_parent,attribuer_par FROM ${prefixe}discipline_sanction WHERE id_eleve='$ideleve' AND date_saisie='$date' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;

}
//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
// afficher de retenue  pour un eleve
function affRetenu_par_eleve($id_eleve){
	global $cnx;
	global $prefixe;
	$sql="
	SELECT
		id_elev,
		date_de_la_retenue,
		heure_de_la_retenue,
		date_de_saisie,
		origi_saisie,
		id_category,
		retenue_effectuer,
		motif,
		attribuer_par,
		signature_parent,
		duree_retenu
	FROM
		${prefixe}discipline_retenue
	WHERE
		id_elev='$id_eleve'
	AND 	
		retenue_effectuer=0

	ORDER BY
		date_de_la_retenue,heure_de_la_retenue DESC";
	$res=execSql($sql);
	//$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function affRetenu_eleve_non_effectue($tri) {
	global $cnx;
	global $prefixe;
	$sql="
	SELECT
		d.id_elev,
		d.date_de_la_retenue,
		d.heure_de_la_retenue,
		d.date_de_saisie,
		d.origi_saisie,
		d.id_category,
		d.retenue_effectuer,
		d.motif,
		d.attribuer_par,
		d.signature_parent,
		d.duree_retenu,
		e.nom,
		e.prenom,
		e.classe,
		d.repport_du

	FROM
		${prefixe}discipline_retenue d, ${prefixe}eleves e
	WHERE
		d.retenue_effectuer='0' 
	AND
		d.id_elev = e.elev_id

	ORDER BY
	$tri ";
	$res=execSql($sql);
	//$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function affRetenuNonEffectue(){
	global $cnx;
	global $prefixe;
	$sql="
	SELECT
		id_elev,
		date_de_la_retenue,
		heure_de_la_retenue,
		date_de_saisie,
		origi_saisie,
		id_category,
		retenue_effectuer,
		motif,
		attribuer_par,
		signature_parent,
		duree_retenu,
		devoir_a_faire,
		description_fait
	FROM
		${prefixe}discipline_retenue
	WHERE
		";
	if (DBTYPE == "pgsql") {
		$sql.="retenue_effectuer=FALSE";
	}
	if (DBTYPE == "mysql")  {
		$sql.="retenue_effectuer=0";
	}
	$sql.="
	ORDER BY
		date_de_la_retenue,heure_de_la_retenue DESC";
	$res=execSql($sql);
	//$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function affRetenuNonEffectuebis($trie){
	global $cnx;
	global $prefixe;
	$sql="
	SELECT
		r.id_elev,
		r.date_de_la_retenue,
		r.heure_de_la_retenue,
		r.date_de_saisie,
		r.origi_saisie,
		r.id_category,
		r.retenue_effectuer,
		r.motif,
		r.attribuer_par,
		r.signature_parent,
		r.duree_retenu,
		r.devoir_a_faire,
		r.description_fait,
		e.nom,
		r.courrier_env

	FROM
		${prefixe}discipline_retenue  r, ${prefixe}eleves  e
	WHERE
		";
	if (DBTYPE == "pgsql") { $sql.="r.retenue_effectuer=FALSE "; }
	if (DBTYPE == "mysql") { $sql.="r.retenue_effectuer=0 "; }

	$sql.=" AND r.id_elev = e.elev_id ";

	if ($trie == "date") {
		$sql.=" ORDER BY r.date_de_la_retenue,r.heure_de_la_retenue DESC";
	}elseif($trie == "nom") {
		$sql.="ORDER BY e.nom ";
	}else{
		$sql.=" ORDER BY r.date_de_la_retenue,r.heure_de_la_retenue DESC";
	}	
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function affRetenuTotal_par_eleve($id_eleve){
        global $cnx;
        global $prefixe;
        $dateDebut=recupDateDebutAnnee($_COOKIE["anneeScolaire"]);
        $dateFin=recupDateFinAnnee($_COOKIE["anneeScolaire"]);
        $sql="SELECT * FROM ${prefixe}discipline_retenue WHERE id_elev='$id_eleve' AND date_de_la_retenue >= '$dateDebut' AND date_de_la_retenue <= '$dateFin' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 0 ) {
                $sql="SELECT id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire,description_fait  FROM ${prefixe}discipline_retenue WHERE id_elev='$id_eleve' AND date_de_la_retenue >= '$dateDebut' AND date_de_la_retenue <= '$dateFin'  ORDER BY date_de_la_retenue DESC";
                $res=execSql($sql);
                $data=chargeMat($res);
                return $data;
        }else {
                return  ;
        }
}


function rechercheDevoirRetenu($ideleve,$date_de_la_retenue,$heure_de_la_retenue) {
	global $cnx;
	global $prefixe;
	$sql="SELECT devoir_a_faire FROM ${prefixe}discipline_retenue WHERE id_elev='$ideleve' AND date_de_la_retenue='$date_de_la_retenue' AND heure_de_la_retenue='$heure_de_la_retenue'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}

function rechercheDescriptionFaitRetenu($ideleve,$date_de_la_retenue,$heure_de_la_retenue) {
	global $cnx;
	global $prefixe;
	$sql="SELECT description_fait  FROM ${prefixe}discipline_retenue WHERE id_elev='$ideleve' AND date_de_la_retenue='$date_de_la_retenue' AND heure_de_la_retenue='$heure_de_la_retenue'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}

function verifSiReportRetenu($ideleve,$date_de_la_retenue,$heure_de_la_retenue) {
	global $cnx;
	global $prefixe;
	$sql="SELECT repport_du  FROM ${prefixe}discipline_retenue WHERE id_elev='$ideleve' AND date_de_la_retenue='$date_de_la_retenue' AND heure_de_la_retenue='$heure_de_la_retenue'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);

}


function modifRetenue($ideleve,$date_de_la_retenue,$heure_de_la_retenue,$saisie_sanction,$saisie_motif) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}discipline_retenue SET id_category='$saisie_sanction' , motif='$saisie_motif' WHERE id_elev='$ideleve' AND date_de_la_retenue='$date_de_la_retenue' AND heure_de_la_retenue='$heure_de_la_retenue'";
	return(execSql($sql));
}


function valideEnvoiCourrierDiscipline($idEleve,$dateretenue,$heureretenue,$duree,$idcategory,$motif,$attribuerpar,$devoirafaire,$faits) {
	global $cnx;
	global $prefixe;
	if (preg_match('/\//',$dateretenue)) { $dateretenue=dateFormBase($dateretenue); }
	$sql="UPDATE ${prefixe}discipline_retenue SET courrier_env='1' WHERE id_elev='$idEleve' AND date_de_la_retenue='$dateretenue' AND heure_de_la_retenue='$heureretenue' AND id_category='$idcategory' AND motif='$motif' AND attribuer_par='$attribuerpar' AND devoir_a_faire='$devoirafaire' AND description_fait='$faits' ";
	return(execSql($sql));
}


function valideEnvoiCourrier($idEleve,$datedebut,$datefin,$time) {
	global $cnx;
	global $prefixe;
	$datedebut=dateFormBase($datedebut);
	$datefin=dateFormBase($datefin);
	$sql="UPDATE ${prefixe}absences SET courrierenvoyer='1' WHERE elev_id='$idEleve' AND  date_ab='$datedebut' AND date_fin='$datefin' AND time='$time' ";
	return(execSql($sql));
}

function valideEnvoiCourrierRetard($idEleve,$datedebut,$duree,$time) {
	global $cnx;
	global $prefixe;
	$datedebut=dateFormBase($datedebut);
	$datefin=dateFormBase($datefin);
	$sql="UPDATE ${prefixe}retards SET courrierenvoyer='1' WHERE elev_id='$idEleve' AND  date_ret='$datedebut' AND duree_ret='$duree' AND heure_ret='$time' ";
	return(execSql($sql));
}


function verifListeRegime() {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}regime WHERE libelle='demi-pension'";
	$res=execSql($sql);
	$data=chargeMat($res); 
	if (count($data) == 0) {
		$sql="INSERT INTO ${prefixe}regime (libelle,lundi_m,lundi_s,mardi_m,mardi_s,mercredi_m,mercredi_s,jeudi_m,jeudi_s,vendredi_m,vendredi_s,samedi_m,samedi_s,dimanche_m,dimanche_s) VALUES ('demi-pension', '1', '0', '1', '0', '1', '0', '1', '0', '1', '0', '0', '0', '0', '0');";
		execSql($sql);
	}
	$sql="SELECT * FROM ${prefixe}regime WHERE libelle='externe'";
	$res=execSql($sql);
	$data=chargeMat($res); 
	if (count($data) == 0) {
		$sql="INSERT INTO ${prefixe}regime (libelle,lundi_m,lundi_s,mardi_m,mardi_s,mercredi_m,mercredi_s,jeudi_m,jeudi_s,vendredi_m,vendredi_s,samedi_m,samedi_s,dimanche_m,dimanche_s) VALUES ('externe', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0');";
		execSql($sql);
	}
}



function recupListEleveRegime() {
	global $cnx;
	global $prefixe;
	verifListeRegime();
	$jour=dateJourSemaine();
	if ($jour == 0) { $champs="dimanche_"; }
	if ($jour == 1) { $champs="lundi_"; }
	if ($jour == 2) { $champs="mardi_"; }
	if ($jour == 3) { $champs="mercredi_"; }
	if ($jour == 4) { $champs="jeudi_"; }
	if ($jour == 5) { $champs="vendredi_"; }
	if ($jour == 6) { $champs="samedi_"; }
	if (dateH() >= 12) { $champs.="s"; }
	if (dateH() < 12) { $champs.="m"; } 
	if ($champs == "") return ; 
	$sql="SELECT libelle FROM ${prefixe}regime WHERE ${champs}='1'";
	$res=execSql($sql);
	$data=chargeMat($res); 
	for($i=0;$i<count($data);$i++) {
		$regime=trim($data[$i][0]);
		$regime=preg_replace("/'/","&#8216;",$regime);
		$regime=addslashes(trim($regime));
		$regime=strtolower($regime);
		$sql="SELECT elev_id FROM ${prefixe}eleves WHERE lower(trim(regime))='$regime'";
		$res=execSql($sql);
		$data2=chargeMat($res); 
		for ($j=0;$j<count($data2);$j++) {
			$ideleve=$data2[$j][0];
			$tab[$ideleve]=$ideleve;
		}
	}
	return($tab);
}

function affRetenuTotal_par_eleve_trimestre($id_eleve,$dateDebut,$dateFin){
	global $cnx;
	global $prefixe;
	$sql="SELECT id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu FROM ${prefixe}discipline_retenue WHERE id_elev='$id_eleve' AND date_de_la_retenue >='$dateDebut' AND date_de_la_retenue <= '$dateFin' ORDER BY date_de_la_retenue DESC";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}



function recupabsDuJour($o,$mois,$annee,$idEleve) {
	global $cnx;
	global $prefixe;
	if ($o < 10) { $o="0".$o; }
	$jour="${annee}-${mois}-${o}";
	$sql="SELECT  elev_id,date_ab,date_saisie,duree_ab,origin_saisie,date_fin,motif,duree_heure,id_matiere,time,justifier FROM ${prefixe}absences WHERE date_ab<='$jour' AND date_fin >='$jour' AND elev_id='$idEleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
	

}
//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
// afficher de retenue
function affRetenue(){
	global $cnx;
	global $prefixe;
	$sql="SELECT id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu FROM ${prefixe}discipline_retenue ORDER BY 2";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}
//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
// afficher de retenue  pour un eleve
function affUneRetenu_dun_eleve($date,$heure,$id_eleve){
	global $cnx;
	global $prefixe;
	$sql="SELECT id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent FROM ${prefixe}discipline_retenue WHERE id_elev='$id_eleve' AND date_de_la_retenue='$date' AND heure_de_la_retenue='$heure' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}
//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//

// affichage dst
function affDst(){
	global $cnx;
	global $prefixe;
        $sql="SELECT id_dst,date,matiere,code_classe,heure,duree,idsalle FROM ${prefixe}calendrier_dst ORDER BY date";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affDst2($idclasse){
	global $cnx;
	global $prefixe;
	$classe=txt_vers_html(chercheClasse_nom($idclasse));
	$classe=preg_replace('/&nbsp;/',' ',$classe);
	$sql="SELECT id_dst,date,matiere,code_classe,heure,duree,idsalle FROM ${prefixe}calendrier_dst WHERE code_classe='$classe' ORDER BY date";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function attenteValidDST($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  id_dem,id_pers,date_dem,classe,mat_text,heure,duree FROM ${prefixe}demande_dst WHERE id_pers='$idpers' ORDER BY date_dem";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

// verifie la presence d'une entree dans la table
// retourne 1 si au moins une valeur sinon retourne 0
function verif_contenu($table){
	global $cnx;
	global $prefixe;
    $sql="SELECT * FROM ${prefixe}$table";
    $res=execSql($sql);
    $data=chargeMat($res);
	$result=count($data);
	if ($result > 0) {
        	return 1;  // au moins une entree
	}else {
		return 0;  // pas d'entree
	}
}

// affichage calendrier evenement
function affEvenement(){
	global $cnx;
	global $prefixe;
        $sql="SELECT * FROM ${prefixe}calend_evenement ORDER BY date";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affEvenementjour($date){
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
        $sql="SELECT id_evenement,date,evenement  FROM ${prefixe}calend_evenement WHERE date='$date'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affEvenementjour_7j($date,$date2) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT id_evenement,date,evenement  FROM ${prefixe}calend_evenement WHERE date >='$date' AND date <= '$date2' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


// affichage matiere
function affMatiere(){
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}matieres WHERE offline='0' ORDER BY libelle";
	//  code_mat,libelle,sous_matiere,offline
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

// affichage matiere
function affToutesLesMatieres(){
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}matieres ORDER BY libelle";
	//  code_mat,libelle,sous_matiere,offline,couleur, libelle_long, code_matiere
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function etatOfflineMatiere($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT offline FROM ${prefixe}matieres WHERE code_mat='$id'";
	//  code_mat,libelle,sous_matiere,offline
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}

function affMatiereLimit($deb,$fin){
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}matieres ORDER BY libelle LIMIT $deb,$fin";
	//  code_mat,libelle,sous_matiere
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function affMatiereAffectation($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT m.code_mat,m.libelle,m.sous_matiere FROM ${prefixe}matieres m, ${prefixe}affectations a  WHERE m.code_mat=a.code_matiere AND a.code_classe='$idclasse' AND a.annee_scolaire='$anneeScolaire' ORDER BY m.libelle";
	//  code_mat,libelle,sous_matiere
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affClasseAffectationProf($idprof) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT m.libelle,a.code_classe FROM ${prefixe}classes m, ${prefixe}affectations a  WHERE m.code_class=a.code_classe AND a.code_prof='$idprof' AND a.annee_scolaire='$anneeScolaire'  GROUP BY m.libelle ORDER BY m.libelle  ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function affClasseAffectationNonProf() {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle, code_class FROM ${prefixe}classes  ORDER BY libelle  ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

// affichage classe
function affClasse(){
	global $cnx;
	global $prefixe;
        $sql="SELECT code_class,libelle,desclong,offline FROM ${prefixe}classes WHERE offline='0' ORDER BY libelle ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affClasseSansOffline() {
	global $cnx;
	global $prefixe;
        $sql="SELECT code_class,libelle,desclong,offline,idsite,niveau FROM ${prefixe}classes  ORDER BY libelle ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function recupSite($id) {
        global $cnx;
        global $prefixe;
        $sql="SELECT * FROM ${prefixe}info_ecole";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) < 1) {
                return "";
        }else{
                $sql="SELECT  nom_ecole FROM ${prefixe}info_ecole WHERE id='$id'";
                $res=execSql($sql);
                $data=chargeMat($res);
                return $data[0][0];
        }
}

function recupDateTrimByIdclasse($trim,$idclasse,$anneeScolaire='') {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == '') $anneeScolaire=$_COOKIE["anneeScolaire"];

        if (trim($anneeScolaire) != "") {
                $sqlsuite=" AND annee_scolaire = '$anneeScolaire' ";
        }

        if (($trim == "annee") || ($trim == "annuel" )) {
                $sql="SELECT date_debut FROM  ${prefixe}date_trimestrielle WHERE trim_choix='trimestre1' AND (idclasse='$idclasse' OR idclasse='0')  $sqlsuite ";
                $res=execSql($sql);
                $data=chargeMat($res);
                $dateDebut1=$data[0][0];
                $sql="SELECT date_fin FROM  ${prefixe}date_trimestrielle WHERE trim_choix='trimestre2' AND (idclasse='$idclasse' OR idclasse='0')  $sqlsuite ";
                $res=execSql($sql);
                $data=chargeMat($res);
                $dateFin2=$data[0][0];
                $sql="SELECT date_fin FROM  ${prefixe}date_trimestrielle WHERE trim_choix='trimestre3' AND (idclasse='$idclasse' OR idclasse='0') $sqlsuite ";
                $res=execSql($sql);
                $data=chargeMat($res);
                $dateFin3=$data[0][0];
                if ($dateFin3 != "") { $dateFin2=$dateFin3; }
                $tab[0][0]=$dateDebut1;
                $tab[0][1]=$dateFin2;
                return($tab);
        }else{
                // Vérification si dans la table il y a une idclasse en 0 ==> donc toutes les classes
                $sql="SELECT date_debut,date_fin,trim_choix FROM  ${prefixe}date_trimestrielle WHERE trim_choix='$trim' AND (idclasse='$idclasse' OR idclasse='0') $sqlsuite  ";
                $res=execSql($sql);
                $data=chargeMat($res);
                if (count($data) > 0) {
                        return $data;
                }else{
                        $sql="SELECT date_debut,date_fin,trim_choix FROM  ${prefixe}date_trimestrielle WHERE trim_choix='$trim' AND idclasse='0' $sqlsuite ";
                        return(chargeMat(execSql($sql)));
                }
        }
}


function visu_paramViaIdSite($idsite){
        if (($idsite == "") || ($idsite == 0)) { $idsite=1; }
        global $cnx;
        global $prefixe;
        $sql="SELECT  nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire FROM ${prefixe}info_ecole WHERE id='$idsite' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function anneeScolaireViaIdClasse($idClasse="") {
        global $cnx;
        global $prefixe;
        if ($idClasse != "") {
                $idSite=chercheIdSite($idClasse);
                $sql="SELECT annee_scolaire FROM ${prefixe}info_ecole WHERE id='$idSite' ";
                $res=execSql($sql);
                $data=chargeMat($res);
                return $data[0][0];
        }else{
                $sql="SELECT annee_scolaire FROM ${prefixe}info_ecole WHERE id='1' ";
                $res=execSql($sql);
                $data=chargeMat($res);
                return $data[0][0];
        }
}



function chercheIdSite($id_classe) {
        global $cnx;
        global $prefixe;
        if ($id_classe != "") {
                $sql="SELECT idsite FROM ${prefixe}classes WHERE code_class='$id_classe'";
                $res=execSql($sql);
                $data=chargeMat($res);
                return $data[0][0];
        }
        return "1";
}






function affClasseLimit($deb,$fin){
	global $cnx;
	global $prefixe;
        $sql="SELECT code_class,libelle FROM ${prefixe}classes WHERE offline='0'  ORDER BY libelle LIMIT $deb,$fin";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affGroupe() {
	global $cnx;
	global $prefixe;
	$sql="SELECT group_id,libelle FROM ${prefixe}groupes ORDER BY libelle ";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affEtude() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,nom_etude FROM ${prefixe}etude_param ORDER BY nom_etude ";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function rechercheEtude($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,nom_etude FROM ${prefixe}etude_param WHERE id='$id' ";
	$res=execSql($sql);
    $data=chargeMat($res);
    return $data[0][1];
}

// affichage sanction
function affSanction(){
	global $cnx;
	global $prefixe;
        $sql="SELECT * FROM ${prefixe}type_sanction ORDER BY libelle";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affMotif(){
	global $cnx;
	global $prefixe;
        $sql="SELECT id,libelle FROM ${prefixe}config_rtd_abs ORDER BY libelle";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affCreneaux(){
	global $cnx;
	global $prefixe;
        $sql="SELECT libelle, dep_h,fin_h FROM ${prefixe}config_creneau ORDER BY libelle";
        $res=execSql($sql);
	$dataP=chargeMat($res);
        return $dataP;
}


function recupInfoCreneau($libelle) {
	global $cnx;
	global $prefixe;
        $sql="SELECT libelle,dep_h,fin_h FROM ${prefixe}config_creneau WHERE  libelle='$libelle' ";
        $res=execSql($sql);
	$dataP=chargeMat($res);
        return $dataP;

}


function error($code){
	if($code == 0) {
		print "<script>window.location.href='./error_base.php';</script>";
	}
}


// error pour la gestion de la
// suppression d'une classe
function error1($code){
	if(!$code):
	print "<script>window.location.href='./error_supp_classe.php';</script>";
	else:
	endif;
}


function civ($civ)	{
	if (trim($civ) == "") {
		return LANGCIV6;
	}
	switch(trim($civ)) {
		case -1:
		return '';
		break;
        	case 0 :
	        return LANGCIV0 ;
       	 	break;
	        case 1:
        	return LANGCIV1 ;
	        break;
        	case 2:
	        return LANGCIV2 ;
        	break;
	        case 3:
        	return LANGCIV3 ;
	        break;
        	case 4:
	        return LANGCIV4 ;
        	break;
	        case 5:
        	return LANGCIV5 ;
	        break;
		case 6:
        	return LANGCIV6 ;
	        break;
	        case 7:
        	return 'P.';
	        break;
	        case 8:
        	return LANGCIV7 ;
	        break;
	        case 9:
        	return LANGCIV8;
	        break;
	        case 10:
        	return LANGCIV9;
	        break;
	        case 11:
        	return LANGCIV10;
	        break;
	        case 12:
        	return LANGCIV11;
	        break;
	        case 13:
        	return LANGCIV12;
	        break;
	        case 14:
        	return LANGCIV13;
	        break;
	        case 15:
        	return LANGCIV14;
	        break;
	        case 16:
        	return LANGCIV15;
	        break;
	        case 17:
        	return LANGCIV16;
	        break;
	        case 18:
        	return LANGCIV17;
	        break;
	        case 19:
        	return LANGCIV18;
	        break;
	        case 20:
        	return LANGCIV19;
	        break;
	        case 21:
        	return LANGCIV20;
	        break;
	        case 22:
        	return LANGCIV21;
	        break;
	        case 23:
        	return LANGCIV22;
	        break;
	        case 24:
        	return LANGCIV23;
	        break;
		case 25:
        	return 'Ø§Ù„Ø¢Ù†Ø³Ø©';
	        break;
		case 26:
        	return 'Ø§Ù„Ø³ÙŠØ¯';
	        break;
		case 27:
        	return 'Ø§Ù„Ø³ÙŠØ¯Ø©';
	        break;
		case 28:
        	return LANGCIV24 ;
	        break;
        	default:
	        return LANGCIV6 ;
        	break;
        }
}


function civ2($civ) {
//	print $civ."<br>";
	if ($civ == "") { return -1; }
	if (strtoupper(trim($civ)) == strtoupper(LANGCIV0))  { return 0; }
	if (strtoupper(trim($civ)) == "MONSIEUR")  { return 0; }
	if (strtoupper(trim($civ)) == strtoupper(LANGCIV1)) { return 1; }
	if (strtoupper(trim($civ)) == "MADAME") { return 1; }
	if (strtoupper(trim($civ)) == strtoupper(LANGCIV2)) { return 2; }
	if (strtoupper(trim($civ)) == strtoupper(LANGCIV3)) { return 3; }
	if (strtoupper(trim($civ)) == strtoupper(LANGCIV4)) { return 4; }
	if (strtoupper(trim($civ)) == strtoupper(LANGCIV5)) { return 5; }
	if (strtoupper(trim($civ)) == strtoupper(LANGCIV6) ) { return 6; }
	if (strtoupper(trim($civ)) == "MADAME OU MONSIEUR") { return 6; }
	if (strtoupper(trim($civ)) == "P.") { return 7; }
	if (strtoupper(trim($civ)) == strtoupper(LANGCIV7)) { return 8; }
	if (trim($civ) == LANGCIV8) { return 9; }
	if (trim($civ) == LANGCIV9) { return 10; }
	if (trim($civ) == LANGCIV10) { return 11; }
	if (trim($civ) == LANGCIV11) { return 12; }
	if (trim($civ) == LANGCIV12) { return 13; }
	if (trim($civ) == LANGCIV13) { return 14; }
	if (trim($civ) == LANGCIV14) { return 15; }
	if (trim($civ) == LANGCIV15) { return 16; }
	if (trim($civ) == LANGCIV16) { return 17; }
	if (trim($civ) == LANGCIV17) { return 18; }
	if (trim($civ) == LANGCIV18) { return 19; }
	if (trim($civ) == LANGCIV19) { return 20; }
	if (trim($civ) == LANGCIV20) { return 21; }
	if (trim($civ) == LANGCIV21) { return 22; }
	if (trim($civ) == LANGCIV22) { return 23; }
	if (trim($civ) == LANGCIV23) { return 24; }
	if (trim($civ) == "Ø§Ù„Ø¢Ù†Ø³Ø©") { return 25; }
	if (trim($civ) == "Ø§Ù„Ø³ÙŠØ¯") { return 26; }
	if (trim($civ) == "Ø§Ù„Ø³ÙŠØ¯Ø©") { return 27; }
	if (trim($civ) == LANGCIV24 ) { return 28; }
}


function civ3($civ) {
	if ($civ == "0") { return 0; }
	if ($civ == "1") { return 1; }
	if ($civ == "2") { return 2; }
	if ($civ == "3") { return 3; }
	if ($civ == "4") { return 4; }
	if ($civ == "5") { return 5; }
	if ($civ == "6") { return 6; }
	if ($civ == "7") { return 7; }
	if ($civ == "8") { return 8; }
	if ($civ == "9") { return 9; }
	if ($civ == "10") { return 10; }
	if ($civ == "11") { return 11; }
	if ($civ == "12") { return 12; }
	if ($civ == "13") { return 13; }
	if ($civ == "14") { return 14; }
	if ($civ == "15") { return 15; }
	if ($civ == "16") { return 16; }
	if ($civ == "17") { return 17; }
	if ($civ == "18") { return 18; }
	if ($civ == "19") { return 19; }
	if ($civ == "20") { return 20; }
	if ($civ == "21") { return 21; }
	if ($civ == "22") { return 22; }
	if ($civ == "23") { return 23; }
	if ($civ == "24") { return 24; }
	if ($civ == "28") { return 28; }
	if (trim($civ) == "") { return -1; }
	$civ=strtoupper($civ);

	if ($civ == strtoupper(LANGCIV0) )  { return 0; }
	if ($civ == strtoupper(LANGCIV1) ) { return 1; }
	if ($civ == strtoupper(LANGCIV2) ){ return 2; }
	if ($civ == strtoupper(LANGCIV3) )  { return 3; }
	if ($civ == strtoupper(LANGCIV4) )  { return 4; }
	if ($civ == strtoupper(LANGCIV5) ) { return 5; }
	if ($civ == strtoupper(LANGCIV6) ) { return 6; }
	if ($civ == "P.") { return 7; }
	if ($civ == strtoupper(LANGCIV7) ) { return 8; }
	if ($civ == LANGCIV8 ) { return 9; }
	if ($civ == LANGCIV9) { return 10; }
	if ($civ == LANGCIV10) { return 11; }
	if ($civ == LANGCIV11) { return 12; }
	if ($civ == LANGCIV12) { return 13; }
	if ($civ == LANGCIV13) { return 14; }
	if ($civ == LANGCIV14) { return 15; }
	if ($civ == LANGCIV15) { return 16; }
	if ($civ == LANGCIV16) { return 17; }
	if ($civ == LANGCIV17) { return 18; }
	if ($civ == LANGCIV18) { return 19; }
	if ($civ == LANGCIV19) { return 20; }
	if ($civ == LANGCIV20) { return 21; }
	if ($civ == LANGCIV21) { return 22; }
	if ($civ == LANGCIV22) { return 23; }
	if ($civ == LANGCIV23) { return 24; }
	if ($civ == LANGCIV24 ) { return 28; }
}


function listingCiv() {
        print "<option value='-1' id='select1'></option>";
        print "<option value='6' id='select1'>".LANGCIV6."</option>";
        print "<option value='0' id='select1'>".LANGCIV0."</option>";
        print "<option value='1' id='select1'>".LANGCIV1."</option>";
        print "<option value='2' id='select1'>".LANGCIV2."</option>";
        print "<option value='3' id='select1'>".LANGCIV3."</option>";
        print "<option value='4' id='select1'>".LANGCIV4."</option>";
        print "<option value='5' id='select1'>".LANGCIV5."</option>";
        print "<option value='7' id='select1'>P.</option>";
        print "<option value='8' id='select1'>".LANGCIV7."</option>";
        print "<option value='28' id='select1'>".LANGCIV24."</option>";
        if (CIVARMEE == "oui") {
                print "<optgroup label='Grades'>";
                print "<option value='9' id='select1' >".LANGCIV8."</option>"; //  GAL
                print "<option value='10' id='select1' >".LANGCIV9."</option>"; // COL
                print "<option value='11' id='select1' >".LANGCIV10."</option>"; //LCL
                print "<option value='12' id='select1' >".LANGCIV11."</option>"; //  CDT
                print "<option value='13' id='select1' >".LANGCIV12."</option>"; //   CNE
                print "<option value='14' id='select1' >".LANGCIV13."</option>"; //  LTT
                print "<option value='15' id='select1' >".LANGCIV14."</option>"; //SLT
                print "<option value='16' id='select1' >".LANGCIV15."</option>"; //      ASP
                print "<option value='17' id='select1' >".LANGCIV16."</option>"; //         MAJ
                print "<option value='18' id='select1' >".LANGCIV17."</option>"; // ADC
                print "<option value='19' id='select1' >".LANGCIV18."</option>"; //      ADJ
                print "<option value='20' id='select1' >".LANGCIV19."</option>"; // SGC
                print "<option value='21' id='select1' >".LANGCIV20."</option>"; //  SGT
                print "<option value='22' id='select1' >".LANGCIV21."</option>"; // CLC
                print "<option value='23' id='select1' >".LANGCIV22."</option>"; //   CAL
                print "<option value='24' id='select1' >".LANGCIV23."</option>"; //  AVT
        }
}


// function creation de la classe
function create_classe($nom_classe) {
        global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}classes WHERE libelle='$nom_classe' ";
	$res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0) {
		return -1;
	}else{
	        $sql="INSERT INTO ${prefixe}classes(libelle) VALUES ('$nom_classe')";
		return(execSql($sql));
	}
}

function create_classe2($nom_classe,$description_long) {
        global $cnx;
	global $prefixe;
        $sql="INSERT INTO ${prefixe}classes(libelle,desclong,idsite) VALUES ('$nom_classe','$description_long','1')";
	return(execSql($sql));
}

function create_classe22($nom_classe,$description_long,$idsite,$langue,$niveau,$specification) {
        global $cnx;
        global $prefixe;
        $sql="INSERT INTO ${prefixe}classes(libelle,desclong,idsite,langueclasse,niveau,specification) VALUES ('$nom_classe','$description_long','$idsite','$langue','$niveau','$specification')";
        return(execSql($sql));
} 

function modif_classe($nom,$id) {
        global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}classes WHERE libelle='$nom' ";
	$res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0) {
		return 0;
	}else{
		$sql="UPDATE ${prefixe}classes SET libelle='$nom' WHERE code_class='$id' ";
		return(execSql($sql));
	}
}


function modif_classe_desact2($idclasse) {
        global $cnx;
	global $prefixe;
	$sql="SELECT offline FROM ${prefixe}classes WHERE code_class='$idclasse' ";
	$res=execSql($sql);
        $data=chargeMat($res);
	if ($data[0][0] == 0) {
		$sql="UPDATE ${prefixe}classes SET offline='1' WHERE code_class='$idclasse' ";;
	}else{
		$sql="UPDATE ${prefixe}classes SET offline='0' WHERE code_class='$idclasse' ";	
	}
	execSql($sql);
}

function modif_classe22($nom,$id,$description_long,$idsite,$langue,$niveau,$specification) {
        global $cnx;
        global $prefixe;
        $nom=trim($nom);
        $sql="UPDATE ${prefixe}classes SET libelle='$nom', desclong='$description_long' , idsite='$idsite' , langueclasse='$langue' , niveau='$niveau' , specification='$specification' WHERE code_class='$id' ";
        return(execSql($sql));
} 


function modif_classe2($nom,$id,$description_long) {
        global $cnx;
	global $prefixe;
	$nom=trim($nom);
	$sql="UPDATE ${prefixe}classes SET libelle='$nom', desclong='$description_long' WHERE code_class='$id' ";
	return(execSql($sql));
}

// function creation de la matiere
function create_matiere($nom_matiere) {
        global $cnx;
	global $prefixe;
	$mat=trim($nom_matiere);
	$matTmp=explode(" ",$mat);
	$mat='';// espace indispensable pour la base de données et l'affichage
	foreach($matTmp as $tmp) {
		trim($tmp);
		$mat .= $tmp." ";
	}
	$mat=trim($mat);
	$sql="INSERT INTO ${prefixe}matieres(libelle,sous_matiere) VALUES ('$mat','0')";
 	return(execSql($sql));
}

function create_matiere_2($nom_matiere,$nom_matiere_long,$code_matiere="",$libelle_en) {
        global $cnx;
	global $prefixe;
	$mat=trim($nom_matiere);
	$matTmp=explode(" ",$mat);
	$mat='';// espace indispensable pour la base de données et l'affichage
	foreach($matTmp as $tmp) {
		trim($tmp);
		$mat .= $tmp." ";
	}
	$mat=trim($mat);
	$sql="SELECT * FROM ${prefixe}matieres WHERE libelle='$nom_matiere'";
	$res=execSql($sql);
	$data=chargeMat($res);
	freeResult($res);
	if(count($data) > 0 ) return(false); 
	$sql="INSERT INTO ${prefixe}matieres(libelle,sous_matiere,libelle_long,code_matiere,libelle_en) VALUES ('$mat','0','$nom_matiere_long','$code_matiere','$libelle_en')";
 	return(execSql($sql));
}



function mise_ajoutCouleurMatiere($id,$couleur) {
        global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}matieres SET couleur='$couleur' WHERE code_mat='$id' ";
	execSql($sql);
}

function create_matiereEDT($nom_matiere,$couleur) {
        global $cnx;
	global $prefixe;
	$mat=trim($nom_matiere);
	$matTmp=explode(" ",$mat);
	$mat='';// espace indispensable pour la base de données et l'affichage
	foreach($matTmp as $tmp) {
		trim($tmp);
		$mat .= $tmp." ";
	}
	$mat=trim($mat);
	$sql="INSERT INTO ${prefixe}matieres(libelle,sous_matiere,couleur) VALUES ('$mat','0','$couleur')";
	return(execSql($sql));
}

function modif_matiere($nom,$id,$sous_matiere,$libelleLong,$code_matiere,$libelle_en) {
	global $cnx;
	global $prefixe;
	if (trim($sous_matiere) == "") { $sous_matiere=0; }
	$sql="UPDATE ${prefixe}matieres SET libelle='$nom',  libelle_en='$libelle_en', sous_matiere='$sous_matiere' ,  libelle_long='$libelleLong' , code_matiere='$code_matiere' WHERE code_mat='$id' ";
	return(execSql($sql));
}




// function creation de la matiere
function create_matiere_sousmatiere($nom_matiere,$sousmatiere) {
        global $cnx;
	global $prefixe;
	$mat=trim($sousmatiere);
	$nom_matiere=trim($nom_matiere);
	$matTmp=explode(" ",$mat);
	$mat=' ';// espace indispensable pour la base de données et l'affichage'
	foreach($matTmp as $tmp) {
		trim($tmp);
		$mat .= $tmp." ";
	}
	$mat=trim($mat);
	$sql="SELECT trim(libelle) FROM ${prefixe}matieres WHERE code_mat='$nom_matiere' ORDER BY libelle";
	$res=execSql($sql);
	$data=chargeMat($res);
	freeResult($res);
	if(count($data) > 0 ) { $nom_matiere=addslashes($data[0][0]); }
	$sql="INSERT INTO ${prefixe}matieres(libelle,sous_matiere) VALUES ('$nom_matiere','$mat')";
	return(execSql($sql));
}

//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
// function config planning DST
function calend_dst($date,$classe,$matiere,$heure,$duree,$idsalle) {
    	global $cnx;
	global $prefixe;
	$matiere=htmlentities($matiere,ENT_COMPAT);
    	$sql="INSERT INTO ${prefixe}calendrier_dst(date,code_classe,matiere,heure,duree,idsalle) VALUES ('$date','$matiere','$classe','$heure','$duree','$idsalle')";
	return(execSql($sql));
}

//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
// function config date trimestrielle
function def_trimestre($trim_choix,$date_debut,$date_fin,$idclasse,$anneeScolaire) {
        global $cnx;
	global $prefixe;
	if ($idclasse != 0) {
		$sql="DELETE FROM ${prefixe}date_trimestrielle WHERE idclasse='0' AND annee_scolaire='$anneeScolaire' ";
		execSql($sql);
	}
	$sql="INSERT INTO ${prefixe}date_trimestrielle(date_debut,date_fin,trim_choix,idclasse,annee_scolaire) VALUES ('$date_debut','$date_fin','$trim_choix','$idclasse','$anneeScolaire')";
	return(execSql($sql));
}
// recupe date en fonction du trimestre
function recupDateTrim($trim) {
        global $cnx;
	global $prefixe;
        $sql="SELECT date_debut,date_fin,trim_choix FROM  ${prefixe}date_trimestrielle WHERE trim_choix='$trim'";
        return(chargeMat(execSql($sql)));
}


function recherche_entr_info_entreprise_via_id($recherche) {
        global $cnx;
        global $prefixe;
        $recherche=trim($recherche);
        $sql="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus FROM ${prefixe}stage_entreprise WHERE id_serial='$recherche' ";
        $res=@execSql($sql);
      $data=chargeMat($res);
      return($data);
}


function recupTrimestreNote($idclasse,$date) {
        global $cnx;
	global $prefixe;
	$dateB=dateFormBase($date);
	$sql="SELECT trim_choix FROM  ${prefixe}date_trimestrielle WHERE (idclasse='$idclasse' OR idclasse='0' )AND  date_debut <= '$dateB' AND date_fin >= '$dateB' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		if ($data[0][0] == "trimestre1") { return(" Notation pour le Trimestre/Semestre 1 pour le devoir du $date");}
		if ($data[0][0] == "trimestre2") { return(" Notation pour le Trimestre/Semestre 2 pour le devoir du $date");}
		if ($data[0][0] == "trimestre3") { return(" Notation pour le Trimestre 3 pour le devoir du $date");}
	}else{
		return("ATTENTION !!!! Pas de trimestre/semestre pour cette date $date !!!!");
	}
}



function recupTrimestreDevoir($idclasse,$date) {
        global $cnx;
	global $prefixe;
	$dateB=dateFormBase($date);
	$sql="SELECT trim_choix FROM  ${prefixe}date_trimestrielle WHERE (idclasse='$idclasse' OR idclasse='0' )AND  date_debut <= '$dateB' AND date_fin >= '$dateB' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		if ($data[0][0] == "trimestre1") { return("1");}
		if ($data[0][0] == "trimestre2") { return("2");}
		if ($data[0][0] == "trimestre3") { return("3");}
	}else{
		return("0");
	}
}


function recupDateDebutAnnee($anneeScolaire='') {
        global $cnx;
        global $prefixe;
        if (trim($anneeScolaire) == '') $anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($anneeScolaire == "") $anneeScolaire=anneeScolaireViaIdClasse();
        if ($anneeScolaire != "") {
                list($anneeDebut,$anneeFin)=preg_split('/ - /',$anneeScolaire);
                $sql="SELECT text FROM  ${prefixe}parametrage WHERE libelle='anneescolaire_dj'";
                $res=execSql($sql);
                $data=chargeMat($res);
                $jourdebut=$data[0][0];
                $sql="SELECT text FROM  ${prefixe}parametrage WHERE libelle='anneescolaire_dm'";
                $res=execSql($sql);
                $data=chargeMat($res);
                $moisdebut=$data[0][0];
                return("$anneeDebut-$moisdebut-$jourdebut");
        }
}


function recupDateFinAnnee($anneeScolaire='') {
        global $cnx;
        global $prefixe;
        if (trim($anneeScolaire) == '') $anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($anneeScolaire == "") $anneeScolaire=anneeScolaireViaIdClasse();
        if ($anneeScolaire != "") {
                list($anneeDebut,$anneeFin)=preg_split('/ - /',$anneeScolaire);
                $sql="SELECT text FROM  ${prefixe}parametrage WHERE libelle='anneescolaire_fj'";
                $res=execSql($sql);
                $data=chargeMat($res);
                $jourfin=$data[0][0];
                $sql="SELECT text FROM  ${prefixe}parametrage WHERE libelle='anneescolaire_fm'";
                $res=execSql($sql);
                $data=chargeMat($res);
                $moisfin=$data[0][0];
                return("$anneeFin-$moisfin-$jourfin");
        }
}


//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function modif_personnel($id_pers,$nom,$prenom,$civ,$adr,$code,$tel,$mail,$commune,$telport,$idsociete=0,$pays,$indice_salaire) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}personnel SET nom='$nom',prenom='$prenom',civ=$civ,email='$mail',commune='$commune',tel='$tel',adr='$adr',code_post='$code',tel_port='$telport',id_societe_tuteur='$idsociete',pays='$pays',indice_salaire='$indice_salaire' WHERE pers_id='$id_pers' ";
	if(execSql($sql))
	{
		$nom=strtoupper($nom);
		$prenom=ucfirst($prenom);
		$sql="SELECT type_pers FROM ${prefixe}personnel WHERE pers_id='$id_pers'";
		$res=execSql($sql);
		$data=chargeMat($res);
		$membre=$data[0][0];	
		if ($membre == "ADM") { $membre="menuadmin"; } 
		if ($membre == "MVS") { $membre="menuscolaire"; } 
		if ($membre == "ENS") { $membre="menuprof"; } 
		if ($membre == "PAR") { $membre="menuparent"; } 
		if ($membre == "ELE") { $membre="menueleve"; } 
		if ($membre == "TUT") { $membre="menututeur"; } 
		if ($membre == "PER") { $membre="menupersonnel"; } 
		$sql="UPDATE ${prefixe}px_utilisateur  SET util_nom='$nom', util_prenom='$prenom' WHERE util_login='$membre$id_pers' ";
		execSql($sql);


        return(true);
    } else {
        return(false);
    }
}

function modif_personnel_prof($id_pers,$nom,$prenom,$civ,$adr,$code,$tel,$mail,$commune,$telport,$identifiant,$indice_salaire) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}personnel SET nom='$nom',prenom='$prenom',civ=$civ,email='$mail',commune='$commune',tel='$tel',adr='$adr',code_post='$code',tel_port='$telport',identifiant='$identifiant',indice_salaire='$indice_salaire' WHERE pers_id='$id_pers' ";
	if(execSql($sql))
	{
        return(true);
    } else {
        return(false);
    }
}

function modif_personnel_actif_desactif($id_pers,$etat) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}personnel SET offline='$etat' WHERE pers_id='$id_pers'";
	execSql($sql);
}

function modif_matiere_actif_desactif($id,$etat) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}matieres SET offline='$etat' WHERE code_mat='$id'";
	execSql($sql);
}

//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
// function config retard mise à jour
function modif_retard($elev_id,$heure_ret,$date_ret,$duree,$motif,$date_de_saisie,$user,$justifier,$heuredoriginret,$heuredoriginsaisie,$refRattrapage) {
        global $cnx;
	global $prefixe;
	$motif=strip_tags($motif);
	$heureS=dateHIS();
	if ($motif == LANGINCONNU) { $motif="inconnu"; }
	if ($motif == "") { $motif="inconnu"; }
	if (strlen($heure_ret) == 5) { $heure_ret.=":00"; }
        $sql="UPDATE ${prefixe}retards SET   idrattrapage='$refRattrapage', date_saisie='$date_de_saisie',origin_saisie='$user',duree_ret='$duree',motif='$motif',justifier='$justifier', heure_saisie='$heureS',heure_ret='$heure_ret' WHERE date_ret='$date_ret' AND elev_id='$elev_id' AND heure_ret='$heuredoriginret' AND heure_saisie='$heuredoriginsaisie' ";
	return(execSql($sql));
}

function modif_retard2($elev_id,$heure_ret,$date_ret,$duree,$motif,$date_de_saisie,$user,$justifier,$heuredoriginsaisie,$date_ret_origine) {
        global $cnx;
	global $prefixe;
	$motif=strip_tags($motif);
	$heureS=dateHIS();
	if (preg_match('/\//',$date_ret_origine)) { $date_ret_origine=dateFormBase($date_ret_origine); }
	if (preg_match('/\//',$date_ret)) { $date_ret=dateFormBase($date_ret); }
	if ($date_ret != "") { $suiteSQL=" , date_ret='$date_ret'"; }
	if ($date_ret != "") { $suiteSQL1=" AND date_ret='$date_ret'"; }
	if ($motif == LANGINCONNU) { $motif="inconnu"; }
	if ($motif == "") { $motif="inconnu"; }
	if (strlen($heure_ret) == 5) { $heure_ret.=":00"; }
	$sql="SELECT * FROM ${prefixe}retards WHERE date_saisie='$date_de_saisie' AND origin_saisie='$user' AND duree_ret='$duree'  AND motif='$motif'  AND justifier='$justifier'  AND heure_saisie='$heureS'  AND heure_ret='$heure_ret'  $suiteSQL1" ;
	$res=execSql($sql);
	$data=chargeMat($res);	
	if (count($data) > 0) {
		return -1; // info existant déjà
	}else{
	        $sql="UPDATE ${prefixe}retards SET date_saisie='$date_de_saisie',origin_saisie='$user',duree_ret='$duree',motif='$motif',justifier='$justifier', heure_saisie='$heureS',heure_ret='$heure_ret'  $suiteSQL WHERE heure_ret='$heure_ret' AND date_ret='$date_ret_origine' AND elev_id='$elev_id'  AND heure_saisie='$heuredoriginsaisie' ";
		execSql($sql);
	}
}

//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//
function suppretardbyabs($elev_id,$date_ab,$date_fin) {
	global $cnx;
	global $prefixe;
	if (($date_ab != "") && ($date_fin)) {
		$sql="DELETE FROM ${prefixe}retards WHERE elev_id='$elev_id' AND  date_ret>='$date_ab' AND date_ret<='$date_fin'";
		return(execSql($sql));
	}
}

function suppabsbyabs($elev_id,$date_ab,$date_fin,$time) {
	global $cnx;
	global $prefixe;
	if ($time == "") {
		if (($elev_id != "") && ($date_ab != "") && ($date_fin != "")){
			$sql="DELETE FROM ${prefixe}absences WHERE elev_id='$elev_id' AND   date_ab>='$date_ab' AND  date_fin<='$date_fin'";
			return(execSql($sql));
		}
	}else{
		if (($date_ab != "") && ($date_fin != "") && ($time != ""))
		$sql="DELETE FROM ${prefixe}absences WHERE elev_id='$elev_id' AND   date_ab>='$date_ab' AND  date_fin<='$date_fin' AND  time != '$time' ";
		return(execSql($sql));
	}
}

function suppabsbyabsHeure($elev_id,$date_ab,$heure,$heureD) {
	global $cnx;
	global $prefixe;
	if (($heure > 0 ) && ($elev_id != "") && ($date_ab != "")) {
		$sql="DELETE FROM ${prefixe}absences WHERE elev_id='$elev_id' AND   date_ab='$date_ab' AND heuredabsence > '$heureD' AND heuredabsence <= '$heure' ";
		return(execSql($sql));
	}
}


//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function modif_absence($elev_id,$date_ab,$date_de_saisie,$user,$motif,$duree,$time,$idmatiere,$justifier,$heuredoriginsaisie,$saisie_date_ret_origine,$heuredabsence,$refRattrapage) {
    	global $cnx;
	global $prefixe;
	$motif=strip_tags($motif);
	if ($motif == LANGINCONNU) { $motif="inconnu"; }
    	if ($motif == "") { $motif="inconnu"; }
	$heureS=dateHIS();
	$date_ab_sup=$saisie_date_ret_origine;
	if (preg_match('/\//',$date_ab)) { $date_ab=dateFormBase($date_ab); }
	if ($date_ab != "") { $suiteSQL=", date_ab='$date_ab'"; $date_ab_sup=$date_ab; }
	if (preg_match('/H/i',$duree)) {
		$dureeH=preg_replace('/H/',".",$duree);
		$duree="-1";
		$date_fin=$saisie_date_ret_origine;
    		$sql="UPDATE ${prefixe}absences SET idrattrapage='$refRattrapage', duree_heure='$dureeH',date_saisie='$date_de_saisie',origin_saisie='$user',duree_ab='$duree',motif='$motif',date_fin='$date_fin', justifier='$justifier' , heure_saisie='$heureS'  $suiteSQL  WHERE date_ab='$saisie_date_ret_origine' AND elev_id='$elev_id' AND  time='$time' AND id_matiere='$idmatiere' ";
		$cr=execSql($sql);
		if ($cr) {  
			$heurefin=dateplusnh(dateForm($date_ab_sup),$heuredabsence,$dureeH);
			suppabsbyabsHeure($elev_id,$date_ab_sup,$heurefin,$heuredabsence);  
		}
		return $cr;
	}else{
		$duree=preg_replace('/ J/',"",$duree);
	   	$date_fin=calculDateFin($date_ab_sup,$duree);
		if ($duree == "???") { $duree=0; }
		$sql="UPDATE ${prefixe}absences SET idrattrapage='$refRattrapage', date_saisie='$date_de_saisie', duree_heure='0', origin_saisie='$user',duree_ab='$duree',motif='$motif',date_fin='$date_fin',  justifier='$justifier', heure_saisie='$heureS' $suiteSQL WHERE date_ab='$saisie_date_ret_origine' AND date_saisie='$date_de_saisie' AND  heure_saisie='$heuredoriginsaisie' AND elev_id='$elev_id' AND  time='$time' AND id_matiere='$idmatiere' ";
		$cr=execSql($sql);
		if (($cr) && ($duree != "???" )) { suppretardbyabs($elev_id,$date_ab_sup,$date_fin); suppabsbyabs($elev_id,$date_ab_sup,$date_fin,$time); }
		return $cr;
	}
}



//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function modif_dispence($id_elev,$matiere,$date_debut,$date_fin,$date_saisie,$user,$certif,$motif,$heure0,$jour0,$heure1,$jour1,$heure2,$jour2) {
        global $cnx;
	global $prefixe;
	$motif=strip_tags($motif);
	if ($certif == "on") {
		if(DBTYPE=='pgsql') {
			$certif=true;
       		} elseif(DBTYPE=='mysql') {
			$certif=1;
       		}
	}else {
		if(DBTYPE=='pgsql') {
			$certif=false;
       		} elseif(DBTYPE=='mysql') {
			$certif=0;
       		}
	}
        $sql="UPDATE ${prefixe}dispenses SET date_saisie='$date_saisie',origin_saisie='$user',motif='$motif',date_fin='$date_fin',code_mat='$matiere',certificat='$certif',heure1='$heure0',jour1='$jour0',heure2='$heure1',jour2='$jour1',heure3='$heure2',jour3='$jour2' WHERE date_debut='$date_debut' AND elev_id='$id_elev'";
	 	return(execSql($sql));
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function modif_retenue($date,$heure,$id_eleve) {
        global $cnx;
	global $prefixe;
	if(DBTYPE=='pgsql') { $vrai='true'; }
	if(DBTYPE=='mysql') { $vrai=1; }
        $sql="UPDATE ${prefixe}discipline_retenue SET retenue_effectuer=$vrai WHERE date_de_la_retenue='$date' AND id_elev='$id_eleve' AND heure_de_la_retenue='$heure'";
 	 	return(execSql($sql));
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function modif_retenue_non($date,$heure,$id_eleve) {
        global $cnx;
	global $prefixe;
        $sql="UPDATE ${prefixe}discipline_retenue SET retenue_effectuer='false' WHERE date_de_la_retenue='$date' AND id_elev='$id_eleve' AND heure_de_la_retenue='$heure'";
 	 	return(execSql($sql));
}

//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//

// affichage date trimestrielle mise à jour
function aff_Trimestre() {
	global $cnx;
	global $prefixe;
	$sql="SELECT date_debut,date_fin,trim_choix FROM ${prefixe}date_trimestrielle";
        return(chargeMat(execSql($sql)));
}

// affichage date trimestrielle  en fonction du trimestre
function affDateTrim($trim) {
	global $cnx;
	global $prefixe;
	$sql="SELECT date_debut,date_fin FROM ${prefixe}date_trimestrielle WHERE trim_choix='$trim'";
    return(chargeMat(execSql($sql)));
}

function affDateTrimByIdclasse($trim,$idclasse,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	// Vérification si dans la table il y a une idclasse en 0 ==> donc toutes les classes
	if (trim($trim) != "") $sqlsuite=" trim_choix='$trim' AND ";
	$sql="SELECT date_debut,date_fin,trim_choix FROM  ${prefixe}date_trimestrielle WHERE  $sqlsuite  (idclasse='$idclasse' OR idclasse='0') AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return($data);
	}else{
		$sql="SELECT date_debut,date_fin,trim_choix FROM  ${prefixe}date_trimestrielle WHERE trim_choix='$trim' AND idclasse='0' AND annee_scolaire='$anneeScolaire' ";
       		return(chargeMat(execSql($sql)));
	}
}

function recupDateTrimIdclasse($idclasse,$anneeScolaire='') {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT date_debut,date_fin,trim_choix,idclasse FROM ${prefixe}date_trimestrielle WHERE (idclasse='$idclasse' OR idclasse='0') AND annee_scolaire='$anneeScolaire' ORDER BY trim_choix ";
        return(chargeMat(execSql($sql)));
}


//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
// function config planning evenement
function calend_evenement($date,$evenement) {
	global $cnx;
	global $prefixe;
	$evenement=strip_tags($evenement);
	$evenement=htmlentities($evenement,ENT_COMPAT);
        $sql="INSERT INTO ${prefixe}calend_evenement(date,evenement) VALUES ('$date','$evenement')";
	return(execSql($sql));
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
// function de creation d'un select automatique
function select_personne($qui) {
	global $cnx;
	global $prefixe;
	$data=affPersActif($qui);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
	        print "<option id='select1' value='".$data[$i][0]."' >".strtoupper($data[$i][2])." ".$data[$i][3]."</option>\n";
        }
}


function select_personne_edt($qui,$heureDebut,$date) {
	global $cnx;
        global $prefixe;
        $data=affPersActif($qui);
	$date=dateFormBase($date);
	for($i=0;$i<count($data);$i++) {
		$idprof=$data[$i][0];
		$sql="SELECT * FROM ${prefixe}edt_seances WHERE idprof='$idprof' AND heure='$heureDebut' AND date='$date'";
		$res=execSql($sql);
        	$data2=chargeMat($res);
		if (count($data2)) continue;
		print "<option id='select1' value='".$data[$i][0]."' >".strtoupper($data[$i][2])." ".$data[$i][3]."</option>\n";
	}
}


function select_personne_qui($qui,$id) {
        global $cnx;
        global $prefixe;
        $data=affPersActif($qui);
        // $datA : tab bidim - soustab 3 champs
        for($i=0;$i<count($data);$i++) {
		if ($id == $data[$i][0]) {  
			$selected="selected='selected'"; 
		}else{
			$selected="";
		}
                print "<option id='select1' value='".$data[$i][0]."'  $selected >".strtoupper($data[$i][2])." ".$data[$i][3]."</option>\n";
        }
}


function select_personne_grpmail($qui,$tabliste) {
	global $cnx;
	global $prefixe;
	$data=affPersActif($qui);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
		$selected="";
		foreach($tabliste as $key=>$value) {
			if ($value == $data[$i][0]) {
				$selected="selected='selected'";
				break;
			}
		}
	        print "<option id='select1' value='".$data[$i][0]."' $selected >".strtoupper($data[$i][2])." ".$data[$i][3]."</option>\n";
        }
}

function select_personne_tuteur($type,$idsociete) {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id, civ, nom, prenom, identifiant, offline FROM ${prefixe}personnel WHERE type_pers='$type' AND id_societe_tuteur='$idsociete' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	// pers_id, civ, nom, prenom, identifiant, offline
	print "<optgroup label='Listing' />";
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][5] == 1) {
			print "<optgroup label=\"".civ($data[$i][1])." ".strtoupper($data[$i][2])." ".$data[$i][3]."  (Inactif)\" />\n";
		}else{
		        print "<option id='select1' value='".$data[$i][0]."' >".strtoupper($data[$i][2])." ".$data[$i][3]."</option>\n";
		}
        }
}

function select_personne_messagerie($qui) {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id, civ, nom, prenom, identifiant FROM ${prefixe}personnel WHERE type_pers='$qui' AND offline='0'  ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
	        print "<option id='select1' value='".$data[$i][0]."' >".strtoupper($data[$i][2])." ".$data[$i][3]."</option>\n";
        }
}

function select_personne_sms($qui) {
	global $cnx;
	global $prefixe;
	$filtreSMS=config_param_visu('smsfiltre');
	$filtreSMS=$filtreSMS[0][0];
	$data=affPers($qui); //pers_id, civ, nom, prenom
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
		$tel=cherchetelPortPersonnel($data[$i][0]);
		if (preg_match("/^$filtreSMS/",$tel)) {
			$tel=preg_replace('/ /',"",$tel);
			$tel=preg_replace('/\./',"",$tel);
			if (is_numeric($tel)) { 
				print "<option id='select1' value='".$tel."'>".strtoupper($data[$i][2])." (".$tel.")</option>\n";
			}
		}
        }
}


function cherchetelPortPersonnel($idPers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  tel_port FROM ${prefixe}personnel WHERE pers_id='$idPers' ";
	$res=execSql($sql);
	$data=chargeMat($res);	
	return $data[0][0];
}

function select_personne_2($qui,$nb) {
	global $cnx;
	global $prefixe;
	$data=affPersActif($qui);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
	        print "<option id='select1' value='".$data[$i][0]."' title=\"".civ($data[$i][1])." ".strtoupper($data[$i][2])." ".$data[$i][3]."\" >".trunchaine(strtoupper($data[$i][2])." ".$data[$i][3],$nb)."</option>\n";
        }
}


function select_personne_2_selected($qui,$nb,$idpers) {
	global $cnx;
	global $prefixe;
	$data=affPersActif($qui);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
			if ($idpers == $data[$i][0]) {
					$selected="selected='selected'";
			}else{
					$selected="";
			}
	        print "<option id='select1' value='".$data[$i][0]."' title=\"".civ($data[$i][1])." ".strtoupper($data[$i][2])." ".$data[$i][3]."\"  $selected >".trunchaine(strtoupper($data[$i][2])." ".$data[$i][3],$nb)."</option>\n";
        }
}



function select_personne_ens_classe($nb,$idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT  p.pers_id,p.civ,p.nom,p.prenom FROM ${prefixe}affectations a , ${prefixe}personnel p  WHERE a.code_classe='$idclasse' AND a.annee_scolaire='$anneeScolaire' AND a.code_prof=p.pers_id GROUP BY p.pers_id "; 
	$res=execSql($sql);
	$data=chargeMat($res);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
	        print "<option id='select1' value='".$data[$i][0]."' title=\"".civ($data[$i][1])." ".strtoupper($data[$i][2])." ".$data[$i][3]."\" >".trunchaine(strtoupper($data[$i][2])." ".$data[$i][3],$nb)."</option>\n";
        }
}



function select_personne_uniq($qui) {
	global $cnx;
	global $prefixe;
	$data=affPers_nom($qui);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
	        print "<option id='select1' value='".$data[$i][0]."'>".strtoupper($data[$i][2])." ".$data[$i][3]."</option>\n";
        }
}


//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
// function de creation d'un select automatique
function select_personne_nom($qui) {
	global $cnx;
	global $prefixe;
	$data=affPersActif($qui);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++)
        {
	$value=trim(strtoupper($data[$i][2]))." ".trim($data[$i][3]);
       print "<option value=\"$value\" id='select1' title=\"".$value."\" >".trunchaine($value,"15")."</option>";
        }
}


function select_personne_nom_len($qui,$len) {
	global $cnx;
	global $prefixe;
	$data=affPersActif($qui);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
	$value=trim(strtoupper($data[$i][2]))." ".trim($data[$i][3]);
       print "<option value=\"$value\" id='select1' title=\"".$value."\" >".trunchaine($value,$len)."</option>";
        }
}


function select_personne_nom_len_id($qui,$len) {
	global $cnx;
	global $prefixe;
	$data=affPersActif($qui);
	// $datA : tab bidim - soustab 3 champs
	for($i=0;$i<count($data);$i++) {
	$value=trim(strtoupper($data[$i][2]))." ".trim($data[$i][3]);
       print "<option value='".$data[$i][0]."' id='select1' title=\"".$value."\" >".trunchaine($value,$len)."</option>";
        }
}

// suppression de compte ENS, MVS, ADM, TUT
function suppression_personnel($pers) {
	global $cnx;
	global $prefixe;
	$typePersonne=recherche_type_personne($pers);
	if ($typePersonne == "ADM") { $membre="menuadmin"; }
        if ($typePersonne == "ENS") { $membre="menuprof"; }
        if ($typePersonne == "MVS") { $membre="menuscolaire"; }
        if ($typePersonne == "TUT") { $membre="menututeur"; }
        if ($typePersonne == "PER") { $membre="menupersonnel"; }
	supp_info_util_agenda($membre,$pers);
	purgeCantinePers($pers,$membre);
	supp_info_entretienEleve($pers);
	supp_info_entretienProf($pers);
	suppression_messagerie_repertoire($pers);
	$sql="DELETE FROM ${prefixe}personnel WHERE pers_id='$pers'";
	return(execSql($sql));
}
// suppression de DST
function suppression_dst($id) {
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}calendrier_dst WHERE id_dst='$id'";
	 	return(execSql($sql));
}
// suppression de devoir scolaire à la maison
function suppression_devoir_scolaire($id) {
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}devoir_scolaire WHERE id='$id'";
 	return(execSql($sql));
}


// suppression de retard
function suppression_retard($saisie_eleve_id,$saisie_heure_ret,$saisie_date_ret) {
	global $cnx;
	global $prefixe;
	if (preg_match('/\//',$saisie_date_ret)) { $saisie_date_ret=dateFormBase($saisie_date_ret); }
        $sql="DELETE FROM ${prefixe}retards WHERE elev_id='$saisie_eleve_id' AND heure_ret='$saisie_heure_ret' AND date_ret='$saisie_date_ret'";
 	return(execSql($sql));
}

// suppression de retenue
function suppression_retenue($eleve_id,$date,$heure) {
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}discipline_retenue WHERE id_elev='$eleve_id' AND date_de_la_retenue='$date' AND heure_de_la_retenue='$heure'";
	return(execSql($sql));
}

// suppression de Sanction
function suppression_discipline($id) {
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}discipline_sanction WHERE id='$id'";
	return(execSql($sql));
}

// suppression de absence
function suppression_absence($saisie_eleve_id,$saisie_date_ret,$time,$idmatiere) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}absences WHERE elev_id='$saisie_eleve_id' AND date_ab='$saisie_date_ret' AND time='$time' AND id_matiere='$idmatiere' ";
 	return(execSql($sql));
}

// suppression de dispence
function suppression_dispence($saisie_eleve_id,$saisie_date_debut) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}dispenses WHERE elev_id='$saisie_eleve_id' AND date_debut='$saisie_date_debut'";
	return(execSql($sql));
}


// suppression de evenement
function purge_evenement() {
	global $cnx;
	global $prefixe;
    	$sql="TRUNCATE TABLE ${prefixe}calend_evenement ";
	return(execSql($sql));
}
function suppression_evenement($id) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}calend_evenement WHERE id_evenement='$id'";
	return(execSql($sql));
}

function purgepersonnel($qui) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}personnel WHERE type_pers='$qui' ";
	if ($qui == "MVS") { purge_rep_membre("./data/rss/menuscolaire");	purge_rep_membre("./data/stockage/menuscolaire");  }
	if ($qui == "ENS") { purge_rep_membre("./data/rss/menuprof");		purge_rep_membre("./data/stockage/menuprof"); }
	if ($qui == "ADM") { purge_rep_membre("./data/rss/menuadmin");		purge_rep_membre("./data/stockage/menuadmin"); }
	if ($qui == "TUT") { purge_rep_membre("./data/rss/menututeur");		purge_rep_membre("./data/stockage/menututeur"); }
	if ($qui == "PER") { purge_rep_membre("./data/rss/menupersonnel");	purge_rep_membre("./data/stockage/menupersonnel"); }
	return(execSql($sql));
}


// function de creation d'un select automatique
// passe du nom de la classe dans le value
function select_classe_nom() {
	global $cnx;
	global $prefixe;
        $data=affClasse();
        for($i=0;$i<count($data);$i++)
        {
	$nomclasse=$data[$i][1];
        print "<option id='select1' value='".$nomclasse."'>".$data[$i][1]."</option>\n";
        }
}

function select_classe_nom_2() {
	global $cnx;
	global $prefixe;
        $data=affClasse();
        for($i=0;$i<count($data);$i++)
        {
	$nomclasse=$data[$i][1];
        print "<option id='select1' value='".$nomclasse."' title=\"".$data[$i][1]."\" >".trunchaine($data[$i][1],20)."</option>\n";
        }
}

function select_groupe() {
	global $cnx;
	global $prefixe;
	$data=affGroupe();
        for($i=0;$i<count($data);$i++) {
	   if (trim($data[$i][1]) != "") {
	        $nomclasse=$data[$i][1];
        	print "<option id='select1' value='".$nomclasse."'>".$data[$i][1]."</option>\n";
	    }
        }
}

function select_groupe_id() {
	global $cnx;
	global $prefixe;
	$data=affGroupe();
        for($i=0;$i<count($data);$i++) {
	   if (trim($data[$i][1]) != "") {
	        $nomclasse=$data[$i][0];
        	print "<option id='select1' value='".$nomclasse."'>".$data[$i][1]."</option>\n";
	    }
        }
}


function select_etude() {
	global $cnx;
	global $prefixe;
     	$data=affEtude();
        for($i=0;$i<count($data);$i++) {
	    	if (trim($data[$i][1]) != "") {
	   		     $id=$data[$i][0];
       		 	 print "<option id='select1' value='".$id."'>".trim($data[$i][1])."</option>\n";
	   		 }
        }
}

//------------------------------------------------------------------------//
//------------------------------------------------------------------------//
// function de creation d'un select automatique
// passe du nom de la sanction dans le value
function select_sanction() {
	global $cnx;
	global $prefixe;
        $data=affSanction();
        for($i=0;$i<count($data);$i++)
        {
        print "<option  id='select1' value='".trim($data[$i][0])."'>".trim($data[$i][1])."</option>\n";
        }
}

function select_motif() {
	global $cnx;
	global $prefixe;
        $data=affMotif();
        for($i=0;$i<count($data);$i++)
        {
        print "<option  id='select1' value='".trim($data[$i][0])."'>".trim($data[$i][1])."</option>\n";
        }
}


function select_creneaux2() {
	global $cnx;
	global $prefixe;
        $data=affCreneaux();
        for($i=0;$i<count($data);$i++){
    	    print "<option  id='select1' value=\"".trim($data[$i][0])."#".$data[$i][1]."#".$data[$i][2]."\"  title=\"".trim($data[$i][0])."\" >".trunchaine(trim($data[$i][0]),15)." : ".timeForm($data[$i][1])." - ".timeForm($data[$i][2])."</option>\n";
        }
}

function select_creneaux() {
	global $cnx;
	global $prefixe;
        $data=affCreneaux();
        for($i=0;$i<count($data);$i++){
    	    print "<option  id='select1' value=\"".trim($data[$i][0])."\" title=\"".trim($data[$i][0])."\" >".trunchaine(trim($data[$i][0]),15)."</option>\n";
        }
}

//------------------------------------------------------------------------//
//------------------------------------------------------------------------//
// passe l'id dans le value
function select_classe($idclasse='') {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_class,libelle FROM ${prefixe}classes WHERE offline='0' ORDER BY libelle";
	$data=ChargeMat(execSql($sql));
    for($i=0;$i<count($data);$i++)
    {
	$selected="";
	if ($idclasse == $data[$i][0]) $selected="selected='selected'"; 
        print "<option id='select1' value='".$data[$i][0]."' $selected >".$data[$i][1]."</option>\n";
    }
}

function select_classe2($nb) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_class,libelle FROM ${prefixe}classes  WHERE offline='0' ORDER BY libelle";
	$data=ChargeMat(execSql($sql));
    for($i=0;$i<count($data);$i++)
    {
        print "<option id='select1' value='".$data[$i][0]."' title=\"".$data[$i][1]."\" >".trunchaine($data[$i][1],$nb)."</option>\n";
    }
}

function select_classe_search($idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_class,libelle FROM ${prefixe}classes WHERE code_class='$idclasse' ";
	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
        	print "<option id='select1' value='".$data[$i][0]."' selected='selected' >".$data[$i][1]."</option>\n";
    	}
}

function select_groupe_search($idgroupe) {
	global $cnx;
	global $prefixe;
	$sql="SELECT group_id,libelle FROM ${prefixe}groupes WHERE group_id='$idgroupe' ";
	$data=ChargeMat(execSql($sql));
	if ($idgroupe > 0) {
		for($i=0;$i<count($data);$i++) {
        		print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][1]."</option>\n";
    		}
	}
}

function select_ressource_search($idRessource) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle FROM ${prefixe}resa_matos WHERE id='$idRessource' ";
	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
        	print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][1]."</option>\n";
    	}
}


function recherche_ressource($idRessource) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle FROM ${prefixe}resa_matos WHERE id='$idRessource' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][1];
}

function select_matiere_search($idmatiere,$len) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE code_mat='$idmatiere' ";
	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
		print "<option id='select1' value='".$data[$i][0]."' title=\"".trim($data[$i][1])." ".trim($data[$i][2])."\" >".trunchaine(trim($data[$i][1]),$len);
   		if(trim($data[$i][2]) != "0") {
			print trunchaine(" ".trim($data[$i][2]),$len);
		}
		print "</option>\n";
    	}
}


// passe l'id dans le value
// function pour l'import de fichier gep
function select_classe_gep() {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_class,libelle FROM ${prefixe}classes ORDER BY libelle";
    	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++)
    	{
    		print "<option id='select1' value='".$data[$i][0]."' title=\"".$data[$i][1]."\" >".trunchaine($data[$i][1],15)."</option>\n";
	}
}

// function de creation d'un select automatique
// passe de l'id dans le value
function select_matiere() {
	global $cnx;
	global $prefixe;
    $data=affMatiere();
    // $datA : tab bidim - soustab 3 champs
    for($i=0;$i<count($data);$i++)
    {
    	print "<option id='select1' value='".$data[$i][0]."'>".trim($data[$i][1]);
   		if(trim($data[$i][2]) != "0") {
			print " ".trim($data[$i][2]);
		}
		print "</option>\n";
    }
}

// function de creation d'un select automatique
// passe de l'id dans le value
function select_matiere3($len) {
	global $cnx;
	global $prefixe;
    $data=affMatiere(); // code_mat,libelle,sous_matiere
    // $datA : tab bidim - soustab 3 champs
    for($i=0;$i<count($data);$i++)
    {
        if ($data[$i][2] == "0") { $data[$i][2]=""; }
        print "<option id='select1' value='".$data[$i][0]."' title=\"".trim($data[$i][1])." ".trim($data[$i][2])."\" >".trunchaine(trim($data[$i][1]),$len);
   	if(trim($data[$i][2]) != "0") {
		print trunchaine(" ".trim($data[$i][2]),$len);
	}
	print "</option>\n";
    }
}

// function de creation d'un select automatique
// passe de l'id dans le value
function select_ToutesLesMatieres3($len) {
	global $cnx;
	global $prefixe;
    $data=affToutesLesMatieres(); // code_mat,libelle,sous_matiere
    // $datA : tab bidim - soustab 3 champs
    for($i=0;$i<count($data);$i++)
    {
    	print "<option id='select1' value='".$data[$i][0]."' title=\"".trim($data[$i][1])." ".trim($data[$i][2])."\" >".trunchaine(trim($data[$i][1]),$len);
   		if(trim($data[$i][2]) != "0") {
			print trunchaine(" ".trim($data[$i][2]),$len);
		}
		print "</option>\n";
    }
}

// function de creation d'un select automatique
// passe de le nom dans le value
function select_matiere4($len) {
	global $cnx;
	global $prefixe;
    $data=affMatiere();
    // $datA : tab bidim - soustab 3 champs
    for($i=0;$i<count($data);$i++)
    {
    	print "<option id='select1' value='".$data[$i][1]."' title=\"".trim($data[$i][1])."\" >".trunchaine(trim($data[$i][1]),$len);
   		if(trim($data[$i][2]) != "0") {
			print trunchaine(" ".trim($data[$i][2]),$len);
		}
		print "</option>\n";
    }
}


// function de creation d'un select automatique
// avec passage du nom de la matiere dans le value de l option
function select_matiere2() {
	global $cnx;
	global $prefixe;
        $data=affMatiere();
        // $datA : tab bidim - soustab 3 champs
        for($i=0;$i<count($data);$i++)
        {
		$sousmat=trim($data[$i][2]);
		$sousmat=preg_replace('/^0$/',"",$sousmat);	
        print "<option id='select1' value=\"".trim($data[$i][1])."\" title=\"".trim($data[$i][1])." ".$sousmat."\" >".trunchaine(trim($data[$i][1]),15)." ".trunchaine(zero2blanc($data[$i][2]),15)."</option>\n";
        }
}


function select_matiere_pour_lvo() {  // pour lv1,lv2,option
	global $cnx;
	global $prefixe;
        $data=affMatiere();
        // $datA : tab bidim - soustab 3 champs
        for($i=0;$i<count($data);$i++)
        {
		$sousmat=trim($data[$i][2]);
		$sousmat=preg_replace('/^0$/',"",$sousmat);	
        	print "<option id='select1' value=\"".trim($data[$i][1])." ".$sousmat."\" title=\"".trim($data[$i][1])." ".$sousmat."\" >".trunchaine(trim($data[$i][1]),15)." ".trunchaine(zero2blanc($data[$i][2]),15)."</option>\n";
        }
}


function select_matiere_pour_lvo_sans_sousmatiere() {  // pour lv1,lv2,option
	global $cnx;
	global $prefixe;
        $data=affMatiere();
        // $datA : tab bidim - soustab 3 champs
        for($i=0;$i<count($data);$i++) {
		if ($data[$i][2] != "0") { continue; } 
		$sousmat=trim($data[$i][2]);
		$sousmat=preg_replace('/^0$/',"",$sousmat);	
        	print "<option id='select1' value=\"".trim($data[$i][1])." ".$sousmat."\" title=\"".trim($data[$i][1])." ".$sousmat."\" >".trunchaine(trim($data[$i][1]),15)." ".trunchaine(zero2blanc($data[$i][2]),15)."</option>\n";
        }
}


// function de remplacement
// du zero par un blanc
function zero2blanc($chaine) {
	return(strtr($chaine, "0", " "));
}

// suppression d'un groupe
function purge_groupe() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}groupes ";
	execSql($sql);
	$sql="UPDATE ${prefixe}affectations SET code_groupe = '0'";
	return(execSql($sql));
}
function suppression_groupe($id_grp) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}groupes WHERE group_id='$id_grp'";
	return(execSql($sql));
}

function modifier_nom_groupe($idgrp,$newgroupe) {
	global $cnx;
	global $prefixe;
	$newgroupe=addslashes($newgroupe);
	$sql="UPDATE ${prefixe}groupes set  libelle='$newgroupe' WHERE  group_id='$idgrp' ";
	return(execSql($sql));

}

// function de creation d'un select automatique via du JavaScript
function select_matiere_script() {
	global $cnx;
	global $prefixe;
    $data=affMatiere();
    //$datA : tab bidim - soustab 3 champs
    for($i=0;$i<count($data);$i++)
    {
    	print "<option id='select1' value='".$data[$i][1]."'>".$data[$i][1]." ".$data[$i][2]."</option>\n";
    }
}

// function creation automatique de checkbox pour les classe
// avec comme value = nom de la classe
function checkbox_classe() {
	global $cnx;
	global $prefixe;
    $data=affClasse();
    $nb=0;
    // $datA : tab bidim - soustab 3 champs
    for($i=0;$i<count($data);$i++)
    {
    $nb++;
print "<label for='caseClass$i' >".$data[$i][1]."<input type=checkbox value='".$data[$i][1]."' id='caseClass$i'  style='background-color:#CCCCCC'> \n";
	if ( $nb == 5 ) { print "<BR>";$nb=0; }
    }
}


// function creation automatique de checkbox pour les classe
// avec comme value = idclasse
function checkbox_classe2() {
	global $cnx;
	global $prefixe;
	$data=affClasse();
    	$nb=0;
	// $datA : tab bidim - soustab 3 champs
	print "<table border='1' style='-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; padding:5px'  ><tr>";
    	for($i=0;$i<count($data);$i++) {
	    	$nb++;
		print "<td align='right' id='bordure' ><label for='caseClass2$i' >&nbsp;".$data[$i][1]."&nbsp;</label></td><td id='bordure'><input type=checkbox id='caseClass2$i' value='".$data[$i][0]."' style='background-color:#CCCCCC' name='idclasse_$i' ></td> \n";
		if ( $nb == 4 ) { print "</tr><tr>";$nb=0; }
    	}
	print "</tr></table>";
    print "<input type=hidden name='nbIdClasse' value='".count($data)."' />";
}

function change_date($date){
	$tab_date=preg_split('/\//',$date);
	$tab_date=array_reverse($tab_date);
	return (join("-",$tab_date));
}

function codeJS($code){
print <<<EOF
<script language="javascript">
$code
</script>
EOF;
}

// suppression de Matiere
function suppression_matiere($matiere) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}matieres WHERE code_mat='$matiere'";
	return(execSql($sql));
}

// suppression sanction
function supp_sanction($sanction) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}type_sanction WHERE id_sanc='$sanction'";
	return(execSql($sql));
}
function supp_motif($sanction) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}config_rtd_abs WHERE id='$sanction'";
	return(execSql($sql));
}

function supp_creneau($libelle) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}config_creneau WHERE libelle='$libelle'";
	return(execSql($sql));
}
//------------------------------------------------------------------//
//------------------------------------------------------------------//
// suppression sanction
function supp_config_nb_sanction($sanction) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}type_nb_sanction WHERE sanction='$sanction'";
	return(execSql($sql));
}
//------------------------------------------------------------------//
//------------------------------------------------------------------//

// suppression de classe
function suppression_classe($classe) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}classes WHERE code_class='$classe'";
	return(execSql($sql));
}
//------------------------------------------------------------//
//------------------------------------------------------------//
// reçoit une date au format aaaa-jj-mm
function dateForm($date){
	if (preg_match('/-/',$date)) {
		$elements=preg_split('/-/',$date);
		$rdate=$elements[2]."/".$elements[1]."/".$elements[0];
		return $rdate;
	}else{
		return $date;
	}
}

function dateJJMM($date) {
	$elements=preg_split('/-/',$date);
	$rdate=$elements[2]."/".$elements[1];
	return $rdate;

}

function dateNonForm($date){
	$elements=preg_split('/-/',$date);
	$rdate=$elements[0].$elements[1].$elements[2];
	return $rdate;
}

function dateFormBase2($date){  // attends jj-mm-aaaa -> aaaa-mm-jj
	$elements=preg_split('/-/',$date);
	$rdate=$elements[2]."-".$elements[1]."-".$elements[0];
	return $rdate;
}

// reçoit une heure au format hh:mm:ss
// retourne au format hh:mm
function timeForm($time){
	$elements=preg_split('/:/',$time);
	$rdate=$elements[0].":".$elements[1];
	return $rdate;
}

function calculAge($dateNaissance) {
	$elements=preg_split('/\//',$dateNaissance);
	$anneeEnCours=date("Y");
	$age = $anneeEnCours - $elements[2];
	return $age;

}


// reçoit une date au format jj/mm/aaaa
// renvoi une date au format aaaa-mm-jj
function dateFormBase($date){
	if (trim($date) != "") {
		$elements=preg_split('/\//',$date);
		if ((trim($elements[1]) != "") && (trim($elements[2]) != "") && (trim($elements[0]) != "")) {
			if (checkdate($elements[1],$elements[0],$elements[2])) {
				$rdate=$elements[2]."-".$elements[1]."-".$elements[0];
				return $rdate;
			}
		}
	}
	return "";
}


// reçoit une heure de type hh:mm
// verifie si vrai
function checkTime($heure) {
	$elements1=preg_split('/:/',$heure);
	if (($elements1[0] > 0) && ($elements1[0] < 24) && ($elements1[1] >= 0) && ($elements1[1] < 60)) {
		return true;
	}else{
		return false;
	}
}


// reçoit une date au format aaaa-mm-jj
// renvoi une date au format aaaammjj
function dateFormSimple($date) {
	$elements=preg_split('/\//',$date);
	$rdate=$elements[2].$elements[1].$elements[0];
	return $rdate;
}

// renvoie l'annee
// reçoit une date au format aaaa-jj-mm
function dateAnnee($date) {
	$element=preg_split('/-/',$date);
	$rdate=$element[0];
	return $rdate;
}

// renvoie le mois et annee MM/AAAA
// reçoit une date au format aaaa-jj-mm
function dateMoisAnnee($date) {
	$element=preg_split('/-/',$date);
	$rdate=$element[1]."/".$element[0];
	return $rdate;
}


// renvoie le jour et le mois JJ/MM
// reçoit une date au format aaaa-jj-mm
function dateJourMois($date) {
	$element=preg_split('/-/',$date);
	$rdate=$element[2]."/".$element[1];
	return $rdate;
}
//-------------------------------------------------------------------//
//-------------------------------------------------------------------//
function modif_pers_passe($id,$pass,$type,$envoiMail="non",$email="") {
        global $cnx;
        global $prefixe;
        include_once("./common/config2.inc.php");

        if (empty($pass)) {
                        return 0;
        }



        if (SECURITE == 3) {
                if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[A-Z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
                        return 0 ;
                }
        }

        if (SECURITE == 2) {
                if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
                        return 0 ;
                }
        }

        if (SECURITE == 1) {
                if (strlen($pass) < 4) {
                        return 0 ;
                }
        }


        $mdp=cryptage($pass);
        $sql="UPDATE ${prefixe}personnel set mdp='$mdp' WHERE pers_id='$id' AND type_pers='$type' ";
        if(execSql($sql)) {
                if ($type == "ADM") { $type="menuadmin"; }
                if ($type == "ENS") { $type="menuprof"; }
                if ($type == "MVS") { $type="menuscolaire"; }
                if ($type == "TUT") { $type="menututeur"; }
                if ($type == "PER") { $type="menupersonnel"; }
                $type_membre=$type;
                $nom=recherche_personne_nom($id,$type);
                $prenom=recherche_personne_prenom($id,$type);
                $nom=addslashes($nom);
                $prenom=addslashes($prenom);
                delete_inscription($nom,$prenom,$type);
                if ($envoiMail == "oui") {
                        if ((ValideMail($email)) && (trim($pass) != "")) { envoiMotDePasse($email,stripslashes($nom),stripslashes($prenom),$pass,$type_membre); }
                }
                return 1;
        }else{
                return 0;
        }
} 

function updateFichePersonnel($idpers,$PE,$lieuenseignant,$type) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}personnel set lieudenseigement='$lieuenseignant'  WHERE pers_id='$idpers' AND type_pers='$type'";
	return(execSql($sql));
}

function rechercheInfoPerso($idpers,$element) {
	global $cnx;
	global $prefixe;
	$sql="SELECT lieudenseigement FROM ${prefixe}personnel WHERE pers_id='$idpers'";
	$data=ChargeMat(execSql($sql));
	return($data[0][0]);
}

function modif_eleve_passe($eid,$pass,$parent,$envoiMail="non",$email="") {
	global $cnx;
	global $prefixe;

	if (empty($pass)) {
		return 0;
	}
	include_once("./common/config2.inc.php");

	if (SECURITE == 3) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[A-Z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}

	if (SECURITE == 2) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}

	if (SECURITE == 1) {
		if (strlen($pass) < 4) {
			return 0 ;
		}
	}


	$mdp=cryptage($pass);
	$sql="UPDATE ${prefixe}eleves set passwd='$mdp' WHERE elev_id='$eid' "; 
	if(execSql($sql)) {
		$nom=recherche_eleve_nom($eid);
		$prenom=recherche_eleve_prenom($eid);
		$nom=addslashes($nom);
		$prenom=addslashes($prenom);
		delete_inscription($nom,$prenom,"menuparent");
		if ($envoiMail == "oui") {
			if ((ValideMail($email)) && (trim($pass) != "")) { envoiMotDePasse($email,stripslashes($nom),stripslashes($prenom),$pass,"menuparent"); }
		}
		return 1;
	}else{
		return 0;
	}
}


function modif_eleve_passe_parent2($eid,$pass,$parent,$envoiMail="non",$email="") {
	global $cnx;
	global $prefixe;

	if (empty($pass)) {
		return 0;
	}
	include_once("./common/config2.inc.php");

	if (SECURITE == 3) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[A-Z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}

	if (SECURITE == 2) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}

	if (SECURITE == 1) {
		if (strlen($pass) < 4) {
			return 0 ;
		}
	}


	$mdp=cryptage($pass);
	$sql="UPDATE ${prefixe}eleves set passwd_parent_2='$mdp' WHERE elev_id='$eid' "; 
	if(execSql($sql)) {
		$nom=recherche_eleve_nom($eid);
		$prenom=recherche_eleve_prenom($eid);
		$nom=addslashes($nom);
		$prenom=addslashes($prenom);
		delete_inscription($nom,$prenom,"menuparent");
		if ($envoiMail == "oui") {
			if ((ValideMail($email)) && (trim($pass) != "")) { envoiMotDePasse($email,stripslashes($nom),stripslashes($prenom),$pass,"menuparent"); }
		}
		return 1;
	}else{
		return 0;
	}
}

function modif_eleve_passe_2($eid,$pass,$envoiMail="non",$email="") {
	global $cnx;
	global $prefixe;
	include_once("./common/config2.inc.php");

	if (SECURITE == 3) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[A-Z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}

	if (SECURITE == 2) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}

	if (SECURITE == 1) {
		if (strlen($pass) < 4) {
			return 0 ;
		}
	}

	$mdp=cryptage($pass);
	$sql="UPDATE ${prefixe}eleves set passwd_eleve='$mdp' WHERE elev_id='$eid' ";
	if(execSql($sql)) {
		$nom=recherche_eleve_nom($eid);
		$prenom=recherche_eleve_prenom($eid);
		$nom=addslashes($nom);
		$prenom=addslashes($prenom);
		delete_inscription($nom,$prenom,"menueleve");
		if ($envoiMail == "oui") {
			if ((ValideMail($email)) && (trim($pass) != "")) { envoiMotDePasse($email,stripslashes($nom),stripslashes($prenom),$pass,"menueleve"); }
		}
		return 1;
	}else{
		return 0;
	}
}

function modif_eleve_sconet($params){
	global $cnx;
	global $prefixe;
	$numEleve=$params["numEleve"];
	$responsable=$params["responsable"];
	if (trim($numEleve) == "") { return 0; }
	$sql="SELECT elev_id FROM ${prefixe}eleves WHERE numero_eleve='$numEleve'";
	$res=execSql($sql);
	$liste=chargeMat($res);
	$sqlsuite="";
	if (count($liste) > 0) {
		if ($responsable == "1") {
			if (trim($params[adresse1]) != "") 	{ $params[adresse1]=preg_replace('/, ,/','',$params[adresse1]);
								  $sqlsuite="adr1='".$params[adresse1].".',"; }
			if (trim($params[codepostal1]) != "") 	{ $sqlsuite.="code_post_adr1='".$params[codepostal1]."',"; }
			if (trim($params[ville1]) != "") 	{ $sqlsuite.="commune_adr1='".$params[ville1]."',"; }
			if (trim($params[teltuteur1]) != "") 	{ $sqlsuite.="telephone='".$params[teltuteur1]."',"; }
			if (trim($params[email1]) != "") 	{ $sqlsuite.="email='".$params[email1]."',"; }
			if (trim($params[telportable1]) != "") 	{ $sqlsuite.="tel_port_1='".$params[telportable1]."',"; }
			if (trim($params[sexe]) != "") 		{ $sqlsuite.="sexe='".strtolower($params[sexe])."',"; }
			if (trim($params[maileleve]) != "") 	{ $sqlsuite.="email_eleve='".$params[maileleve]."',"; }
			if (trim($params[nomparent]) != "")	{ $sqlsuite.="nomtuteur='".$params[nomparent]."',"; }
			if (trim($params[prenomparent]) != "")	{ $sqlsuite.="prenomtuteur='".$params[prenomparent]."',"; }
			if (trim($params[regime]) != "") 	{ 
				if ($params[regime] == "DP DAN") {
					$sqlsuite.="regime='Demi Pension',"; 
				}elseif($params[regime] == "EXTERN") {
					$sqlsuite.="regime='Interne',";
				}else{
					$sqlsuite.="regime='Externe',";
				}
			}
			if ((trim($params[parente]) == "PERE") || (trim($params[parente]) == "PERE-M" ) || (trim($params[parente]) == "AUTRE" ) ) {
				if (trim($params[telprofession1]) != ""){ $sqlsuite.="tel_prof_pere='".$params[telprofession1]."',"; }
				if (trim($params[emploi]) != "")	{ $sqlsuite.="profession_pere='".$params[emploi]."',"; }
				if ((trim($params[parente]) == "PERE") || (trim($params[parente]) == "PERE-M" )) {
					$sqlsuite.="civ_1='0',";
				}
			}
			if (trim($params[parente]) == "MERE") {
				if (trim($params[telprofession1]) != ""){ $sqlsuite.="tel_prof_mere='".$params[telprofession1]."',"; }
				if (trim($params[emploi]) != ""){ $sqlsuite.="profession_mere='".$params[emploi]."',"; }
				$sqlsuite.="civ_1='1',";
			}

			if ($sqlsuite != "") {
				$sqlsuite=preg_replace('/,$/','',$sqlsuite);
				$sql="UPDATE ${prefixe}eleves SET $sqlsuite WHERE numero_eleve='$numEleve'";
				return(execSql($sql));
			}
		}
		if ($responsable == "2") {
			if (trim($params[adresse1]) != "") 	{ $params[adresse1]=preg_replace('/, ,/','',$params[adresse1]);
								  $sqlsuite="adr2='".$params[adresse1].".',"; }
			if (trim($params[codepostal1]) != "") 	{ $sqlsuite.="code_post_adr2='".$params[codepostal1]."',"; }
			if (trim($params[ville1]) != "") 	{ $sqlsuite.="commune_adr2='".$params[ville1]."',"; }
			if (trim($params[email1]) != "") 	{ $sqlsuite.="email_resp_2='".$params[email1]."',"; }
			if (trim($params[telportable1]) != "") 	{ $sqlsuite.="tel_port_2='".$params[telportable1]."',"; }
			if (trim($params[maileleve]) != "") 	{ $sqlsuite.="email_eleve='".$params[maileleve]."',"; }
			if (trim($params[nomparent]) != "")	{ $sqlsuite.="nom_resp_2='".$params[nomparent]."',"; }
			if (trim($params[prenomparent]) != "")	{ $sqlsuite.="prenom_resp_2='".$params[prenomparent]."',"; }
			
			if (trim($params[parente]) == "PERE") {
				if (trim($params[telprofession1]) != ""){ $sqlsuite.="tel_prof_pere='".$params[telprofession1]."',"; }
				if (trim($params[emploi]) != ""){ $sqlsuite.="profession_pere='".$params[emploi]."',"; }
				$sqlsuite.="civ_2='0',";
			}
			if (trim($params[parente]) == "MERE") {
				if (trim($params[telprofession1]) != ""){ $sqlsuite.="tel_prof_mere='".$params[telprofession1]."',"; }
				if (trim($params[emploi]) != ""){ $sqlsuite.="profession_mere='".$params[emploi]."',"; }
				$sqlsuite.="civ_2='1',";
			}

			if ($sqlsuite != "") {
				$sqlsuite=preg_replace('/,$/','',$sqlsuite);
				$sql="UPDATE ${prefixe}eleves SET $sqlsuite WHERE numero_eleve='$numEleve'";
				return(execSql($sql));
			}
		}


		return 0;
	}else{
		return 0;
	}
}

function modif_eleve($eid,$params){
	global $cnx;
	global $prefixe;
	$params[mdp]=cryptage($params[mdp]);
	$params[naiss]=dateFormBase($params[naiss]);

	if (trim($params[civ_1]) == "") { $params[civ_1]=NULL; }
	if (trim($params[civ_2]) == "") { $params[civ_2]=NULL; }

	$params[boursier_montant]=preg_replace('/,/','\.',$params[boursier_montant]);
	$params[indemnite_stage]=preg_replace('/,/','\.',$params[indemnite_stage]);


$sql=<<<EOF


UPDATE ${prefixe}eleves

SET
      nom=			'$params[ne]'
,     prenom=			'$params[pe]'
,     classe=			'$params[ce]'
,     lv1=			'$params[lv1]'
,     lv2=			'$params[lv2]'
,     `option`=			'$params[option]'
,     regime=			'$params[regime]'
,     date_naissance=		'$params[naiss]'
,     nationalite=		'$params[nat]'
,     nomtuteur=		'$params[nt]'
,     prenomtuteur=		'$params[pt]'
,     adr1=			'$params[adr1]'
,     code_post_adr1=		'$params[cpadr1]'
,     commune_adr1=		'$params[commadr1]'
,     adr2=			'$params[adr2]'
,     code_post_adr2=		'$params[cpadr2]'
,     commune_adr2=		'$params[commadr2]'
,     telephone=		'$params[tel]'
,     profession_pere=		'$params[profp]'
,     tel_prof_pere=		'$params[telprofp]'
,     profession_mere=		'$params[profm]'
,     tel_prof_mere=		'$params[telprofm]'
,     nom_etablissement=	'$params[nomet]'
,     numero_etablissement=	'$params[numet]'
,     code_postal_etablissement='$params[cpet]'
,     commune_etablissement=	'$params[commet]'
,     numero_eleve=		'$params[numero_eleve]'
,     email=			'$params[email]'
,     class_ant=		'$params[classe_ant]'
,     annee_ant=		'$params[annee_ant]'
,	civ_1=			'$params[civ1]'
,	civ_2=			'$params[civ2]'
,	tel_eleve=		'$params[tel_eleve]'
,	email_eleve=		'$params[mail_eleve]'
,	nom_resp_2=		'$params[nom_resp2]'
,	prenom_resp_2=		'$params[prenom_resp2]'
,	lieu_naissance=		'$params[lieunais]'
,	tel_port_1=		'$params[tel_port_1]'
,	tel_port_2=		'$params[tel_port_2]'
,	email_resp_2=		'$params[email_2]'
,	sexe=			'$params[sexe]'
,	code_compta=		'$params[codecompta]'
,	information=		'$params[information]'
,	adr_eleve=		'$params[adr_eleve]'
,	commune_eleve=		'$params[commune_eleve]'
,	ccp_eleve=		'$params[ccp_eleve]'
,	tel_fixe_eleve=		'$params[tel_fixe_eleve]'
,	pays_eleve=		'$params[pays_eleve]'
,	boursier=		'$params[boursier]'
,	montant_bourse=		'$params[boursier_montant]'
,	indemnite_stage=	'$params[indemnite_stage]'
,	nbmoisindemnite=	'$params[nbmoisindemnite_stage]'
,	emailpro_eleve=		'$params[mailpro_eleve]'
,	rangement=		'$params[rangement]'
,	bde=			'$params[bde]'
,	cdi=			'$params[cdi]'
,	situation_familiale=	'$params[situation_familiale]'
,	serie_bac=		'$params[saisie_serie_bac]'
,	annee_bac=		'$params[saisie_annee_bac]'
,	departement_bac=	'$params[saisie_departement_bac]'
,	departementnais=	'$params[saisie_departementnais]'
WHERE elev_id='$eid'

EOF;
if(execSql($sql)) {
	return 1;
}else{
	return 0;
	}
}


function etatBoursier($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT boursier FROM ${prefixe}eleves WHERE elev_id='$ideleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return(($data[0][0] == 0) ? "non" : "oui");

}

function nbmoisindemnite($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nbmoisindemnite FROM ${prefixe}eleves WHERE elev_id='$ideleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}

function montantBourse($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT montant_bourse FROM ${prefixe}eleves WHERE elev_id='$ideleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return(affichageFormatMonnaie($data[0][0])." ".unitemonnaie());
}

function montantBoursePdf($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT montant_bourse FROM ${prefixe}eleves WHERE elev_id='$ideleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return(affichageFormatMonnaie($data[0][0])." ".unitemonnaiePdf());
}

function montantIndemniteStagePdf($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT indemnite_stage FROM ${prefixe}eleves WHERE elev_id='$ideleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return(affichageFormatMonnaie($data[0][0])." ".unitemonnaiePdf());
}



function montantIndemniteStage($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT indemnite_stage FROM ${prefixe}eleves WHERE elev_id='$ideleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return(affichageFormatMonnaie($data[0][0])." ".unitemonnaie());
}




function select_suppleant() {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id FROM ${prefixe}vacataires";
	$res=execSql($sql);
	$liste=chargeMat($res);
	if (count($liste) > 0) {
		for($j=0;$j<count($liste);$j++) {
		   $sql="SELECT pers_id,nom,prenom FROM ${prefixe}personnel  WHERE pers_id='".$liste[$j][0]."' ";
		   $res=execSql($sql);
		   $data=chargeMat($res);
		   $id=$data[0][0];
	   	   $nom=strtoupper(trim($data[0][1]));
		   $prenom=ucwords(trim($data[0][2]));
		   print "<option id='select1' value='$id'>$nom $prenom</option>";
		}
	}
}

function supp_suppleant($pid) {
	global $cnx;
	global $prefixe;
	//nécessite une transaction
	$sqla="DELETE FROM ${prefixe}vacataires WHERE pers_id='$pid'";
	$sqlb="DELETE FROM ${prefixe}personnel WHERE pers_id='$pid'";
	if( execSql($sqla) && execSql($sqlb) ) {
		return 1;
	}else {
		return 0;
	}
}

// pour cle des champs de $params -> create_groupe_suite.php
function create_groupe($params,$anneeScolaire) {

	if (trim($anneeScolaire) == "") {
               $anneeScolaire=anneeScolaire();
      	}
	
	global $cnx;
	global $prefixe;
$sql=<<<EOF
INSERT INTO ${prefixe}groupes(liste_elev,commentaire,libelle,annee_scolaire) VALUES (
        '\{$params[liste_eleve]}',
        '$params[comment]',
	'$params[nomgr]',
	'$anneeScolaire'
)
EOF;
return(execSql($sql));
}

function enr_bull_bonifacio($nomgrp,$idliste) {
 	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='$nomgrp'";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage(libelle,text) VALUES ('$nomgrp','\{$idliste}')";
	execSql($sql);
}


function enrGrpMat($idclasse,$idliste,$nomgrp,$leap) {
 	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='bulletinLeap_$leap' AND idclasse='$idclasse' ";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage(libelle,text,idclasse,info) VALUES ('bulletinLeap_$leap','\{$idliste}','$idclasse','$nomgrp')";
	return(execSql($sql));
}


function delGrpMat($idclasse) {
 	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE idclasse='$idclasse' AND libelle='bulletinLeap_1' ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}parametrage WHERE idclasse='$idclasse' AND libelle='bulletinLeap_2' ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}parametrage WHERE idclasse='$idclasse' AND libelle='bulletinLeap_3' ";
	execSql($sql);
}

function enr_parametrage($libelle,$valeur,$info='') {
 	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='$libelle'";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage(libelle,text,info) VALUES ('$libelle','$valeur','$info')";
	execSql($sql);
}


function supp_parametrage($libelle) {
 	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='$libelle'";
	execSql($sql);
}


function supp_parametrage_supplementtitre($text) {
 	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE text='$text' AND info='supplementautitre'";
	execSql($sql);
}

function enr_bull_matFalcutative($nomgrp,$idliste) {
 	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='$nomgrp'";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage(libelle,text) VALUES ('$nomgrp','\{$idliste}')";
	execSql($sql);
}

function aff_structure($libelle) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle,text FROM ${prefixe}parametrage WHERE libelle like '$libelle%' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function recupListeSupplementAuTitre() {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle,text FROM ${prefixe}parametrage WHERE info = 'supplementautitre' ORDER BY libelle ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}



function aff_enr_parametrage($libelle) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle,text FROM ${prefixe}parametrage WHERE libelle='$libelle' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function aff_valeur_parametrage($libelle) {
	global $cnx;
	global $prefixe;
	$sql="SELECT text FROM ${prefixe}parametrage WHERE libelle='$libelle' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][0];
}


function aff_grp_bull_bonifacio($libelle) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle,text FROM ${prefixe}parametrage WHERE libelle='$libelle' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function aff_grp_bull_leap($libelle,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle,text,info FROM ${prefixe}parametrage WHERE libelle='$libelle' AND idclasse='$idclasse' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function recupClassSMSnonConfig() {
	global $cnx;
	global $prefixe;
	$sql="SELECT text FROM ${prefixe}parametrage WHERE libelle='smsconfignonclasse' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$idliste=preg_replace('/\{/','',$data[0][0]);
	$idliste=preg_replace('/\}/','',$idliste);
	$tab=explode(',',$idliste);
	return($tab);

}

function aff_bull_matfacultative($libelle) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle,text FROM ${prefixe}parametrage WHERE libelle='$libelle' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

// pour cle des champs de $params -> modif_groupe.php
function modif_group($params) {
	global $cnx;
	global $prefixe;
	$nomgroupe=$params['nomgr'];
	$sql="UPDATE ${prefixe}groupes SET liste_elev='\{$params[liste_eleve]}' WHERE libelle='$nomgroupe'";
	return(execSql($sql));
}


function modif_perm_cdt($params) {
	global $cnx;
	global $prefixe;
	$nomgroupe=$params['nomgrp'];
	$nomgroupe=addslashes($nomgroupe);
	$sql="SELECT libelle,text,idclasse,info FROM ${prefixe}parametrage WHERE libelle='$nomgroupe'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}parametrage SET text='\{$params[liste_prof]}' WHERE libelle='$nomgroupe'";
		return(execSql($sql));
	}else{
		$sql="INSERT INTO ${prefixe}parametrage (text,libelle) VALUE ('\{$params[liste_prof]}','$nomgroupe')";
		return(execSql($sql));
	}
}


// select
function aff_groupe() {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}groupes ORDER BY 3";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function verifnomgrp($intitule) {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}groupes WHERE libelle='$intitule' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if(count($data) > 0) {
		return false;
	}else{
		return true;
	}

}

function genMatJs($nom,$data)
{
print <<<EOF
<script language="JavaScript">
var $nom=new Array();\n
EOF;
for($i=0;$i<count($data);$i++)
	{
	print($nom."[".$i."]=new Array();\n");
		for($j=0;$j<count($data[$i]);$j++)
		{
		$data[$i][$j]=preg_replace('/\'/',"\'",$data[$i][$j]);
		print($nom."[".$i."][".$j."]='".$data[$i][$j]."' ;\n");
		}
	}
print("</script>\n");
}

//---------------------------------------------------------------------------//
function verif_retard($heure,$duree,$date,$saisie_pers,$date_saisie,$user,$motif,$idmatiere,$justifier,$idprof) {
	global $cnx;
	global $prefixe;
	if (preg_match('/\//',$date)) { $date=dateFormBase($date); }
	$sql="SELECT * FROM ${prefixe}retards WHERE elev_id='$saisie_pers' AND date_ret='$date' AND heure_ret='$heure' ";
	$cr=execSql($sql);
	$data=chargeMat($cr);
	if (count($data) > 0) {
		return true;
	}else{
		return false;
	}
}
//---------------------------------------------------------------------------//
function create_retard($heure,$duree,$date,$saisie_pers,$date_saisie,$user,$motif,$idmatiere,$justifier,$idprof,$creneaux) {
	global $cnx;
	global $prefixe;
	$motif=strip_tags($motif);
	if (preg_match('/\//',$date)) { $date=dateFormBase($date); }
	$time=dateHIS();
	if (trim($heure) == "") { $heure=dateHIS(); }
        if ($motif == LANGINCONNU) { $motif="inconnu"; }
        if ($motif == "") { $motif="inconnu"; }
	if (($duree == "????") || ($duree == "???"))  { $duree=0; }
	$sql="INSERT INTO ${prefixe}retards(elev_id,heure_ret,date_ret,date_saisie,origin_saisie,duree_ret,motif,idmatiere,justifier,heure_saisie,idprof,creneaux) VALUES ('$saisie_pers','$heure','$date','$date_saisie','$user','$duree','$motif','$idmatiere','$justifier','$time','$idprof','$creneaux')";
	$cr=execSql($sql);
	if (($cr) && (!$justifier)) { enrAlertAbsRtd($saisie_pers,'alertNbRtd'); }
	return $cr;
}
//-----------------------
			
function create_retard2($heuresaisie,$duree,$heure,$saisie_pers,$date_saisie,$user,$motif,$idmatiere,$justifier,$date,$refRattrapage) {
	global $cnx;
	global $prefixe;
	$motif=strip_tags($motif);
        if ($motif == LANGINCONNU) { $motif="inconnu"; }
        if ($motif == "") { $motif="inconnu"; }
	if (($duree == "????") || ($duree == "???"))  { $duree=0; }
	$sql="INSERT INTO ${prefixe}retards(elev_id,heure_ret,date_ret,date_saisie,origin_saisie,duree_ret,motif,idmatiere,justifier,heure_saisie,idrattrapage) VALUES ('$saisie_pers','$heuresaisie','$date','$date_saisie','$user','$duree','$motif','$idmatiere','$justifier','$heure','$refRattrapage')";
	$cr=execSql($sql);
	if (($cr) && (!$justifier)) { enrAlertAbsRtd($saisie_pers,'alertNbRtd'); }
	return $cr;
}
//----------------------------------------------------//
//---------------------------------------------------------------------------//
function create_absent($duree,$date,$saisie_pers,$date_saisie,$user,$motif,$idmatiere,$justifier,$heuredabsence,$idprof,$creneaux,$refRattrapage) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$motif=strip_tags($motif);
	$time=dateHIS();
    if ($motif == LANGINCONNU) { $motif="inconnu"; }
	if ($motif == "") { $motif="inconnu"; }
	if ($duree == "????") { $duree=0; }
	if ((preg_match('/H/',$duree))  || ($duree == "heure")) {
		if ($duree == "heure") {
			$dureeH=$heuredabsence;
			
			list($horaireLibelle,$heure,$horaireFin)=preg_split('/#/',$creneaux);
			$hdeb=conv_en_seconde($heure);
			$hfin=conv_en_seconde($horaireFin);
			$h=$hfin-$hdeb;
			$dureeH=calcul_hours($h);
			$dureeH=timeForm($dureeH);
			$dureeH=preg_replace('/:/','.',$dureeH);
	
		}else{
			$dureeH=preg_replace('/H/',".",$duree);
		}
		$duree="-1";
		$datefin=$date;
		$sql="INSERT INTO ${prefixe}absences(elev_id,date_ab,date_saisie,origin_saisie,duree_ab,date_fin,motif,duree_heure,id_matiere,time,justifier,heure_saisie,heuredabsence,idprof,creneaux,idrattrapage) VALUES 
		                                    ($saisie_pers,'$date','$date_saisie','$user','$duree','$datefin','$motif','$dureeH','$idmatiere','$time','$justifier','$time','$heuredabsence','$idprof','$creneaux','$refRattrapage')";
		$cr=execSql($sql);
		if (($cr) && (!$justifier)) { enrAlertAbsRtd($saisie_pers,'alertNbAbs');smsauto($saisie_pers); }
		return $cr;
	}else{
		$dureeH=0;
		$duree=preg_replace('/ J/',"",$duree);
		$datefin=calculDateFin($date,$duree) ;
		$sql="SELECT elev_id,date_ab,date_saisie,origin_saisie,duree_ab,date_fin,motif FROM ${prefixe}absences WHERE elev_id='$saisie_pers' AND date_ab >= '$date' AND date_fin <= '$datefin' AND ( duree_ab != '-1' AND duree_ab != '0' )";
		$cr=execSql($sql);
		$data=chargeMat($cr);
		if (count($data) > 0) {
			if ($duree >= 1) { suppretardbyabs($saisie_pers,$date,$datefin); }
		}else {
			if ($cr) {
				suppabsbyabs($elev_id,$date_ab,$date_fin,'');
		    	$sql="INSERT INTO ${prefixe}absences(elev_id,date_ab,date_saisie,origin_saisie,duree_ab,date_fin,motif,duree_heure,id_matiere,time,justifier,heure_saisie,heuredabsence,idprof,creneaux,idrattrapage) VALUES ('$saisie_pers','$date','$date_saisie','$user','$duree','$datefin','$motif','$dureeH','$idmatiere','$time','$justifier','$time','$heuredabsence','$idprof','$creneaux','$refRattrapage')";
				$cr=execSql($sql);
				if (($cr) && (!$justifier)) { enrAlertAbsRtd($saisie_pers,'alertNbAbs');smsauto($saisie_pers); }
				if (($cr) && ($duree >= 1)) { suppretardbyabs($saisie_pers,$date,$datefin); }
				return $cr;
			}
		}
	}

}




function smsauto($ideleve) {
	global $prefixe;
	global $cnx;
	if (!file_exists("./common/config-sms.php")) {return;}

	$idclasse=chercheIdClasseDunEleve($ideleve); 
	$sql="SELECT text FROM ${prefixe}parametrage WHERE libelle='smsconfignonclasse'";
	$res=execSql($sql);
	$data=chargeMat($res);	
	$listeClasse=preg_replace('/\{/',"",$data[0][0]);
	$listeClasse=preg_replace('/\}/',"",$listeClasse);		
	$data=explode(",", $listeClasse);
	foreach($data as $key=>$value) {
		if ($value == $idclasse) return;
	}


	$a=config_param_visu('SMSAUTO');
	$b=config_param_visu('SMSNBABS');
	$c=config_param_visu('SMSAUTOJUSTIFIER');
	$SMSAUTO=$a[0][0];
	$nbAbsSMS=$b[0][0];
	$SMSAUTOJUSTIFIER=$c[0][0];
	if ($SMSAUTO != '1') { return; }
	$date=dateDMY2(); 
	$sql="SELECT justifier,time,date_ab FROM ${prefixe}absences WHERE date_ab='$date' AND elev_id='$ideleve' AND (duree_ab='-1' OR duree_ab='0') ORDER BY time desc LIMIT $nbAbsSMS";
	$cr=execSql($sql);
	$data=chargeMat($cr);
	$dataABS=$data;
	$type="absent(e)";
	$justifier=0;
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][0] == '0') {
			$justifier++;
		}
	}


	if ($justifier == $nbAbsSMS) {
		$recup="";
		include_once("./common/config-sms.php");
		$idsms=SMSKEY;
		$urlsms=SMSURL;
		$sql="SELECT nom,prenom,telephone,tel_port_1,tel_port_2,tel_eleve,tel_fixe_eleve FROM ${prefixe}eleves WHERE elev_id='$ideleve'";
		$cr=execSql($sql);
		$data=chargeMat($cr);
		$prenomEleve=$data[0][0];
		$nomEleve=$data[0][1];
		$telephone=$data[0][2];
		$tel_port_1=$data[0][3];
		$tel_port_2=$data[0][4];
		$tel_eleve=$data[0][5];
		$tel_fixe_eleve=$data[0][6];
	
		$c=config_param_visu('SMSAUTOTEL');
		if (($c[0][0] == 1) && ($telephone != "")) { $recup.="$prenomEleve $nomEleve&$telephone#"; }
		$c=config_param_visu('SMSAUTOTELPORT1');
		if (($c[0][0] == 1) && ($tel_port_1 != "")) { $recup.="$prenomEleve $nomEleve&$tel_port_1#"; }
		$c=config_param_visu('SMSAUTOTELPORT2');
		if (($c[0][0] == 1) && ($tel_port_2 != "")) { $recup.="$prenomEleve $nomEleve&$tel_port_2#"; }
		$c=config_param_visu('SMSAUTOTELELEVEPORT');
		if (($c[0][0] == 1) && ($tel_eleve != "")) { $recup.="$prenomEleve $nomEleve&$tel_eleve#"; }
		$c=config_param_visu('SMSAUTOTELELEVEFIXE');
		if (($c[0][0] == 1) && ($tel_fixe_eleve != "")) { $recup.="$prenomEleve $nomEleve&$tel_fixe_eleve#"; }

		if ($recup != "") {		

			$message=config_param_visu("sms-message");

			$message=$message[0][0];
			if (trim($message) == "") { $message="Nous vous signalons que votre enfant ELEVE est absent(e) aujourd'hui (DATE) "; }
			$message=preg_replace('/"/',"'",$message);

			$date=dateDMY();
			$message=preg_replace('/DATE/',"$date",$message);
			$classe=chercheClasse_nom(chercheClasseEleve($ideleve));
			$message=preg_replace('/CLASSE/',"$classe",$message);
			$message=preg_replace('/TYPE/',"$type",$message);


			print "<iframe width='10' height='10' name='TRIADE-SMS' src='../vide.html' style='visibility:hidden' ></iframe>";
			print "<form method='post' action='${urlsms}sms-envoi.php' target='TRIADE-SMS' name='formulaire'>";
			print "<input type='hidden' name='sms-envoi' value=\"$recup\">";
			print "<input type='hidden' name='sms-message' value=\"".stripslashes($message)."\">";
			print "<input type='hidden' name='sms-id' value='$idsms'>";
			print "<input type='hidden' name='sms-info' value=\"$_SESSION[nom] $_SESSION[prenom]\" >";
			print "</form>";
			print "<script>document.formulaire.submit()</script>";
			enrHistoEleve($ideleve,$date,"Envoi SMS abs/rtd","");

			//  justifier,time,date_ab
			$justifier=$dataABS[0][0];
			$time=$dataABS[0][1];
			$date_ab=$dataABS[0][2];
			$sqlsuite="";
			if ($SMSAUTOJUSTIFIER == '1') { $sqlsuite=" , justifier='1' "; }
			$sql="UPDATE ${prefixe}absences SET smsenvoye='1' $sqlsuite WHERE elev_id='$ideleve' AND date_ab='$date_ab' AND time='$time'";
			execSql($sql);

		}
	}

}

function signeEnvoiSmsAbsRtd($type,$ideleve,$date_ab,$time) {
	global $cnx;
	global $prefixe;
	if ($type == "absent(e)") {
		$sql="UPDATE ${prefixe}absences SET smsenvoye='1' WHERE elev_id='$ideleve' AND date_ab='$date_ab' AND time='$time'";
		execSql($sql);
	}else{
		$sql="UPDATE ${prefixe}retards SET smsenvoye='1' WHERE elev_id='$ideleve' AND date_ret='$date_ab' AND heure_saisie='$time'";
		execSql($sql);
	}
}

function enrAlertAbsRtd($idEleve,$type) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,type,nb,signaler FROM ${prefixe}alerteabsrtd WHERE ideleve='$idEleve' AND type='$type' ";
	$data=chargeMat(execSql($sql));
	if (count($data) > 0) {
		$nb=$data[0][2] + 1;
		$val=aff_enr_parametrage($type); 
		$nbmax=$val[0][1];
		$signaler=$data[0][3];
		if (($nb >= $nbmax) && ($signaler == '0') && (trim($nbmax) != "") ) {
			$number=md5(uniqid(rand()));
			$val=aff_enr_parametrage("alertAbsMail"); 
			$dataLi=liste_idpers_grp_mail($val[0][1]);
			$nomPrenomEleve=rechercheEleveNomPrenom($idEleve);
			$objet="Alerte : abs/rtd ".addslashes($nomPrenomEleve);
			$date=dateDMY2();
			$heure=dateHIS();
			if ( $type=="alertNbAbs" ) { $type2="d'absence autorisée"; }
			if ( $type=="alertNbRtd" ) { $type2="de retard autorisé"; }
			$message="L'élève <b>$nomPrenomEleve</b> vient de dépasser le nombre  $type2 <br> <br> <i>Message automatique</i><br><br>";
			for($j=0;$j<count($dataLi);$j++) {
				$idpers=$dataLi[$j];
				$type_personne_dest=$type_personne=recherche_type_personne($idpers);
				$emetteur=$destinataire=$idpers;
				envoi_messagerie($emetteur,$destinataire,$objet,Crypte($message,$number),$date,$heure,$type_personne,$type_personne_dest,$number,'',0);
			}
			$sql="UPDATE ${prefixe}alerteabsrtd SET signaler='1' WHERE ideleve='$idEleve' AND type='$type' AND signaler='0' ";
			execSql($sql);
		}
		$sql="UPDATE ${prefixe}alerteabsrtd SET nb='$nb' WHERE ideleve='$idEleve' AND type='$type' AND nb='".$data[0][2]."' ";
	}else{
		$sql="INSERT INTO ${prefixe}alerteabsrtd (ideleve,type,nb,signaler) VALUES ('$idEleve','$type','1','0')";
	}
	execSql($sql);
}

function enrAlertAbsRtdBis($idEleve,$type,$matabs) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,type,matiereabs,nb,signaler FROM ${prefixe}alerteabsrtd WHERE ideleve='$idEleve' AND type='$type' AND matiereabs='$matabs'";
	$dataa=chargeMat(execSql($sql));
	if (count($dataa) > 0) {
		$nb=$dataa[0][3] + 1;
                $nbmax=25;
		$signaler=$dataa[0][4];
		if (($nb >= $nbmax) && ($signaler == '0') && (trim($nbmax) != "") ) {
			$number=md5(uniqid(rand()));
			$val=aff_enr_parametrage("alertAbsMail"); 
			$dataLi=liste_idpers_grp_mail($val[0][1]);
			$nomPrenomEleve=rechercheEleveNomPrenom($idEleve);
			$objet="Alerte : abs/rtd ".addslashes($nomPrenomEleve);
			$date=dateDMY2();
			$heure=dateHIS();
			if ( $type=="alertNbAbs" ) { $type2="d'absence autorisée"; }
			if ( $type=="alertNbRtd" ) { $type2="de retard autorisé"; }
				$message="L'élève <b>$nomPrenomEleve</b> vient de dépasser le nombre   $type2 pour la matiére $matabs, nombre Abs : $nb / $nbmax<br> <br> <i>Message automatique</i><br><br>";
			for($j=0;$j<count($dataLi);$j++) {
				$idpers=$dataLi[$j];
				$type_personne_dest=$type_personne=recherche_type_personne($idpers);
				$emetteur=$destinataire=$idpers;
				envoi_messagerie($emetteur,$destinataire,$objet,Crypte($message,$number),$date,$heure,$type_personne,$type_personne_dest,$number,'',0);
			}
			$sql="UPDATE ${prefixe}alerteabsrtd SET signaler='1' WHERE ideleve='$idEleve' AND type='$type' AND signaler='0' AND matiereabs='$matabs' ";
			execSql($sql);
		}
		$sql="UPDATE ${prefixe}alerteabsrtd SET nb='$nb' WHERE ideleve='$idEleve' AND type='$type' AND matiereabs='$matabs' AND nb='".$dataa[0][3]."' ";
	}else{
		$sql="INSERT INTO ${prefixe}alerteabsrtd (ideleve,type,matiereabs,nb,signaler) VALUES ('$idEleve','$type','$matabs','1','0')";
	}
	execSql($sql);
}


function recupEntervaleAbs($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT date_ab,date_fin FROM ${prefixe}absences WHERE elev_id='$idEleve'";
	$cr=execSql($sql);
	$data=chargeMat($cr);
	if (count($data) > 0) {
		for($i=0;$i<count($data);$i++) {
			$dateDebut=$data[$i][0];
			$dateFin=$data[$i][1];
			if ($dateFin == "0000-00-00") { $dateFin=$dateDebut; }
			$resultat[$dateDebut]=$dateFin;
		}
		return $resultat;
	}
	return;
}


function createAffectationCopy($idclasseSource,$anneeScolaireSource,$idclasseDestination,$anneeScolaireDest) {
	global $cnx;
	global $prefixe;

	
	$sql="DELETE FROM ${prefixe}affectations WHERE code_classe='$idclasseDestination' AND  annee_scolaire='$anneeScolaireDest'";
	execSql($sql);


	$sql="SELECT ordre_affichage , code_matiere  ,code_prof  , code_classe  , coef  , code_groupe  , langue  , avec_sous_matiere , visubull  , nb_heure  , trim  , ects  , id_ue_detail  , specif_etat, visubullbtsblanc  FROM ${prefixe}affectations WHERE code_classe='$idclasseSource' AND  annee_scolaire='$anneeScolaireSource' ";
	$cr=execSql($sql);
	$data=chargeMat($cr);
	for($i=0;$i<count($data);$i++) {
		$ordre_affichage=$data[$i][0];
		$code_matiere=$data[$i][1];
		$code_prof=$data[$i][2];
		$code_classe=$idclasseDestination;
		$coef=$data[$i][4];
		$langue=$data[$i][6];
		if ($langue == '') $langue=null ; 
		$avec_sous_matiere=$data[$i][7];
		if ($avec_sous_matiere == 'false') { $avec_sous_matiere=0; }
		if ($avec_sous_matiere == 'true')  { $avec_sous_matiere=1; }
		$visubull=$data[$i][8];
		if ($visubull == '') $visubull=0 ;
		$nb_heure=$data[$i][9];
		$trim=$data[$i][10];
		$ects=$data[$i][11];
		$id_ue_detail='';
		$specif_etat='';
		$code_groupe='';
		$annee_scolaire=$anneeScolaireDest;
		$visubullbtsblanc=$data[$i][14];
		$sql="INSERT INTO  ${prefixe}affectations (ordre_affichage,code_matiere,code_prof,code_classe,coef,code_groupe,langue,avec_sous_matiere,visubull,nb_heure,trim,ects,id_ue_detail,specif_etat,annee_scolaire,visubullbtsblanc) VALUES ('$ordre_affichage','$code_matiere','$code_prof','$code_classe','$coef','$code_groupe','$langue','$avec_sous_matiere','$visubull','$nb_heure','$trim','$ects','$id_ue_detail','$specif_etat','$annee_scolaire','$visubullbtsblanc')";
		execSql($sql);
	}
}



function createAffectation($cdata){
	global $cnx;
	global $prefixe;
	global $_POST;
	$cid=$cdata[0][0];
	$cnom=trim($cdata[0][1]);
	$rows=$_POST["saisie_nb_matiere"];
	$tri=trim($_POST['saisie_tri']);
	$anneeScolaire=trim($_POST["anneeScolaire"]);

	if ($tri == '') $tri="tous";


for ($i=0;$i<=$rows;$i++) {
	$matTmp=explode(":",$_POST['saisie_matiere_'.$i]);
	$varSql[$i][mat]=$matTmp[0];
	$varSql[$i][prof]=$_POST['saisie_prof_'.$i];
	$varSql[$i][cid]=$cid;
	$varSql[$i][coef]=$_POST['saisie_coef_'.$i];
	$varSql[$i][grp]=$_POST['saisie_groupe_'.$i];
	$varSql[$i][lang]=trim($_POST['saisie_langue_'.$i]);
	$varSql[$i][visubull]=$_POST['saisie_visubull_'.$i];
	$varSql[$i][visubullBTSblanc]=$_POST['saisie_visubull_btsblanc_'.$i];
	$varSql[$i][nbheure]=$_POST['saisie_nbheure_'.$i];
	$varSql[$i][tri]=$tri;
	$varSql[$i][ects]=$_POST['saisie_ects_'.$i];
	$varSql[$i][ue]=$_POST['ue_'.$i];
	$varSql[$i][specif_etat]=$_POST['specif_'.$i];
	$varSql[$i][anneeScolaire]=$anneeScolaire;
	$varSql[$i][info_semestre]=$_POST['info_semestre_'.$i];
	$varSql[$i][coef_certif]=$_POST['saisie_coef_certif_'.$i];
	$varSql[$i][note_planche]=$_POST['saisie_note_planche_'.$i];
		// inutile ??? versus requête sur matieres champ ''
	if($matTmp[1]):
		$varSql[$i][smat]='true';
	else:
		$varSql[$i][smat]='false';
	endif;

	if($varSql[$i][lang] == '' ) {
		$varSql[$i][lang]=null;
	}

	if(trim($varSql[$i][visubull]) == '') {
		$varSql[$i][visubull]=0;
	}
	
	if ($varSql[$i][smat] == 'false') { $varSql[$i][smat]=0; }
	if ($varSql[$i][smat] == 'true') { $varSql[$i][smat]=1; }
}



for($i=0;$i<count($varSql);$i++){
	for($j=0;$j<count($varSql[$i]);$j++){
		if ($varSql[$i][ue] != "") {
			$sql="DELETE FROM ${prefixe}ue_detail WHERE code_ue='".$varSql[$i][ue]."'";
			execSql($sql);
		}
	}
}

// création du tableau des requêtes insert
for($i=0;$i<count($varSql);$i++){

	if ($varSql[$i][ue] > 0) {
		$sql="LOCK TABLES ${prefixe}ue_detail WRITE";
		execSql($sql);
		$sql="SET AUTOCOMMIT = 0";  
		execSql($sql);
		$sql="INSERT INTO ${prefixe}ue_detail  (code_ue,code_matiere,code_enseignant,code_idgroupe) VALUES ('".$varSql[$i][ue]."','".$varSql[$i][mat]."','".$varSql[$i][prof]."','".$varSql[$i][grp]."');";
		execSql($sql);
		$sql="SELECT LAST_INSERT_ID()";  
		$res=execSql($sql);
		$dataO=chargeMat($res);
		$idcode_ue=$dataO[0][0];
		$sql="COMMIT";  
		execSql($sql);
		$sql="UNLOCK TABLES"; 
		execSql($sql);
	}else{
		$idcode_ue='0';
	}
		
	
	for($j=0;$j<count($varSql[$i]);$j++){


$listSql[$i]=<<<SQL
INSERT INTO
	${prefixe}affectations
VALUES(
SQL;
$listSql[$i] .= $i.",";
$listSql[$i] .= "'".$varSql[$i][mat]."',";
$listSql[$i] .= "'".$varSql[$i][prof]."',";
$listSql[$i] .= "'".$varSql[$i][cid]."',";
$listSql[$i] .= "'".$varSql[$i][coef]."',";
$listSql[$i] .= "'".$varSql[$i][grp]."',";
$listSql[$i] .= "'".$varSql[$i][lang]."',";
$listSql[$i] .= "'".$varSql[$i][smat]."',";
$listSql[$i] .= "'".$varSql[$i][visubull]."',";
$listSql[$i] .= "'".$varSql[$i][nbheure]."',";
$listSql[$i] .= "'".$varSql[$i][tri]."',";
$listSql[$i] .= "'".$varSql[$i][ects]."',";
$listSql[$i] .= "'".$idcode_ue."',";
$listSql[$i] .= "'".$varSql[$i][specif_etat]."',";
$listSql[$i] .= "'".$varSql[$i][anneeScolaire]."',";
$listSql[$i] .= "'".$varSql[$i][visubullBTSblanc]."',";
$listSql[$i] .= "'".$varSql[$i][info_semestre]."',";
$listSql[$i] .= "'".$varSql[$i][coef_certif]."',";
$listSql[$i] .= "'".$varSql[$i][note_planche]."'";
$listSql[$i] .= ')';
	}


}

// insertion finale
// transaction pour la destruction des lignes
//

if(!execSql('BEGIN')):
	$flag=false;
endif;

if(!execSql("DELETE FROM ${prefixe}affectations WHERE code_classe='$cid' AND trim='$tri' AND annee_scolaire='$anneeScolaire'  ;")):
	$flag=false;
endif;

for($i=0;$i<count($listSql);$i++){
	if(!execSql($listSql[$i])):
		$flag=false;
		break;
	else:
		$flag=true;
		continue;
	endif;
}

execSql("DELETE FROM ${prefixe}affectations WHERE code_matiere='-1'");

if($flag){
	execSql('COMMIT');
	return true;

} else {
	execSql('ROLLBACK');
	return false;
}
// fin fonction createAffectation
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function create_dispence($id_eleve,$matiere,$date_debut,$date_fin,$date_saisie,$user,$certif,$motif,$heure1,$jour1,$heure2,$jour2,$heure3,$jour3) {
	global $cnx;
	global $prefixe;
	$motif=strip_tags($motif);
	if(DBTYPE=='pgsql')
	{
		//on ne modifie rien
	}
	elseif(DBTYPE=='mysql')
	{
		if($certif=='true')
		{
			$certif=1;
		}
		elseif($certif=='false')
		{
			$certif=0;
		}
		else
		{
			//erreur
		}
	}
    $sql="INSERT INTO ${prefixe}dispenses(elev_id,code_mat,date_debut,date_fin,date_saisie,certificat,motif,heure1,jour1,heure2,jour2,heure3,jour3,origin_saisie) VALUES ($id_eleve,$matiere,'$date_debut','$date_fin','$date_saisie',$certif,'$motif','$heure1','$jour1','$heure2','$jour2','$heure3','$jour3','$user')";
	return(execSql($sql));
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function calculDateFin($date,$duree) {
	// duree exprimer en jour
	// $date en format 2002-11-31
	global $cnx;
	global $prefixe;
	$dur=$duree;
	$elements=preg_split('/-/',$date);
	//$rdate=$elements[2]."/".$elements[1]."/".$elements[0];
	//mktime (0,0,0,mois,jour,annee);
	$rdate=mktime (0,0,0,$elements[1],$elements[2],$elements[0]);
	$duree = $duree * 86500 ;
	if ($dur >= 1) {
		$duree = $duree - 86500 ; // on enleve 1 journée
	}
	$rdate=$rdate + $duree;
	$rdate=strftime("%Y-%m-%d", $rdate);
	return $rdate;
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function dateSecond($date) {
	$elements=preg_split('/\//',$date);
	//mktime (0,0,0,mois,jour,annee);
	$rdate=mktime (0,0,0,$elements[1],$elements[0],$elements[2]);
	return $rdate;
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function chercheClasse($id_classe) {
	global $cnx;
	global $prefixe;
	if ($id_classe != '') {
$sql=<<<EOF
SELECT code_class,trim(libelle),trim(desclong)
FROM ${prefixe}classes
WHERE code_class='$id_classe'
ORDER BY libelle
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
	}
}
//---------------------------------------------------------------------------//
function chercheIdClasse($nomClasse) {
	global $cnx;
	global $prefixe;
	$nomClasse=$nomClasse;
	if ($nomClasse != '') {
		$sql="SELECT code_class FROM ${prefixe}classes WHERE libelle='$nomClasse' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		return $data[0][0];
	}
	return "";
}
//---------------------------------------------------------------------------//
function chercheClasse_nom($id_classe) {
	global $cnx;
	global $prefixe;
	if ($id_classe != "") {
		$sql="SELECT code_class,trim(libelle) FROM ${prefixe}classes WHERE code_class='$id_classe'";
		$res=execSql($sql);
		$data=chargeMat($res);
		return $data[0][1];
	}
	return "";
}


function recupCoursDuJourViaClasse($date,$idClasse) {
        global $cnx;
        global $prefixe;
        $datedujour=dateFormBase($date);
        $sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule FROM ${prefixe}edt_seances WHERE (coursannule != '1' OR coursannule IS NULL)  AND date='$datedujour' AND idclasse='$idClasse' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
} 

function chercheClasse_nomLong($id_classe) {
	global $cnx;
	global $prefixe;
	if ($id_classe != "") {
		$sql="SELECT code_class,trim(libelle),trim(desclong) FROM ${prefixe}classes WHERE code_class='$id_classe'";
		$res=execSql($sql);
		$data=chargeMat($res);
		if ($data[0][2] != "") {
			return stripslashes($data[0][2]);
		}else{
			return stripslashes($data[0][1]);
		}
	}
	return "";
}

function chercheClasse_description($id_classe) {
	global $cnx;
	global $prefixe;
	if ($id_classe != "") {
		$sql="SELECT code_class,trim(desclong) FROM ${prefixe}classes WHERE code_class='$id_classe'";
		$res=execSql($sql);
		$data=chargeMat($res);
		return stripslashes($data[0][1]);
	}
	return "";
}

function ClasseIsOffline($id_classe) {
	global $cnx;
	global $prefixe;
	$sql="SELECT offline FROM ${prefixe}classes WHERE code_class='$id_classe' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}

//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function chercheGroupeId($nom_groupe) {
	global $cnx;
	global $prefixe;
	$nom_groupe=addslashes($nom_groupe);
$sql=<<<EOF
SELECT group_id,trim(libelle)
FROM ${prefixe}groupes
WHERE libelle='$nom_groupe'
ORDER BY libelle
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][0];
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function chercheGroupeNom($id_groupe) {
	global $cnx;
	global $prefixe;
$sql=<<<EOF
SELECT group_id,trim(libelle)
FROM ${prefixe}groupes
WHERE group_id='$id_groupe'
ORDER BY libelle
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	if($data[0][1]=='néant'):
		return '';
	else:
		return $data[0][1];
	endif;
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function chercheIdMatiere($nom_matiere) {
	global $cnx;
	global $prefixe;
	$nom_matiere=preg_replace('/\'/',"\'",$nom_matiere);
	$sql="SELECT code_mat,trim(libelle) FROM ${prefixe}matieres WHERE   libelle='$nom_matiere'  OR ";
	$sql.=" CONCAT(trim(libelle),' ',trim(sous_matiere)) = '$nom_matiere' ";
	$sql.=" ORDER BY libelle";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][0];
}
//---------------------------------------------------------------------------//
function chercheIdMatiere2($nom_matiere) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_mat FROM ${prefixe}matieres WHERE libelle='$nom_matiere'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][0];
}
//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function chercheMatiereNom($id_matiere) {
	global $cnx;
	global $prefixe;
	$id_matiere=trim($id_matiere);
	if ($id_matiere > 0) {
		$sql="SELECT code_mat,trim(libelle),trim(sous_matiere) FROM ${prefixe}matieres WHERE code_mat='$id_matiere' ORDER BY libelle";
		$res=execSql($sql);
		$data=chargeMat($res);
		freeResult($res);
		unset($res);
		if(!$data[0][2]){
			$data[0][2]='';
		}
		if ($data[0][2] == "0" )$data[0][2]="";	
		return $data[0][1]." ".$data[0][2];
	}
	
	if ($id_matiere < 0) {
		$id_matiere=preg_replace('/-/','',$id_matiere);
		$sql="SELECT id,nom_etude FROM ${prefixe}etude_param WHERE id='$id_matiere'";
		$res=execSql($sql);
		$data=chargeMat($res);
		freeResult($res);
		unset($res);
		return $data[0][1];
	}

}


function chercheMatiereNomBrevet($id_matiere) {
	global $cnx;
	global $prefixe;
	$id_matiere=trim($id_matiere);
	$sql="SELECT code_mat,trim(libelle) FROM ${prefixe}matieres WHERE code_mat='$id_matiere'";
	$res=execSql($sql);
	$data=chargeMat($res);
	freeResult($res);
	unset($res);
	return $data[0][1];
}

function verifMatiereSuivanteCommeSousmatiere($id_matiere) {
	global $cnx;
	global $prefixe;
	$id_matiere=trim($id_matiere);
	if ($id_matiere > 0) {
		$sql="SELECT code_mat,trim(libelle),trim(sous_matiere) FROM ${prefixe}matieres WHERE code_mat='$id_matiere' ORDER BY libelle";
		$res=execSql($sql);
		$data=chargeMat($res);
		freeResult($res);
		unset($res);
		if (($data[0][2] != "") &&  ($data[0][2] != "0")){
			return true;
		}
		return false;
	}
	return false;
}



function chercheMatiereNom2($id_matiere) {
	global $cnx;
	global $prefixe;
	$id_matiere=trim($id_matiere);
	if ($id_matiere > 0) {
		$sql="SELECT code_mat,trim(libelle),trim(sous_matiere) FROM ${prefixe}matieres WHERE code_mat='$id_matiere' ORDER BY libelle";
		$res=execSql($sql);
		$data=chargeMat($res);
		freeResult($res);
		unset($res);
		if(!$data[0][2]){
			$data[0][2]='';
		}
		return $data[0][1];
	}
}


function chercheMatiereLong($id_matiere) {
	global $cnx;
	global $prefixe;
	$id_matiere=trim($id_matiere);
	if ($id_matiere > 0) {
		$sql="SELECT trim(libelle_long),libelle FROM ${prefixe}matieres WHERE code_mat='$id_matiere'";
		$res=execSql($sql);
		$data=chargeMat($res);
		freeResult($res);
		unset($res);
		if ($data[0][0] != "") return $data[0][0];
		return $data[0][1];
	}
}

function chercheCodeMatiere($id_matiere) {
	global $cnx;
	global $prefixe;
	$id_matiere=trim($id_matiere);
	if ($id_matiere > 0) {
		$sql="SELECT trim(code_matiere) FROM ${prefixe}matieres WHERE code_mat='$id_matiere'";
		$res=execSql($sql);
		$data=chargeMat($res);
		freeResult($res);
		unset($res);
		return $data[0][0];
	}
}



function chercheMatiereNom3($id_matiere) {
	global $cnx;
	global $prefixe;
	$id_matiere=trim($id_matiere);
	if ($id_matiere > 0) {
		$sql="SELECT code_mat,trim(libelle),trim(sous_matiere) FROM ${prefixe}matieres WHERE code_mat='$id_matiere' ORDER BY libelle";
		$res=execSql($sql);
		$data=chargeMat($res);
		freeResult($res);
		unset($res);
		if(count($data) > 0){
			return	$data[0][1];
		}
	}
	return "";
}

function listingQuantite($datedebut,$datefin,$prestation,$annule) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($datedebut);
	$dateFin=dateFormBase($datefin);
	$sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule FROM ${prefixe}edt_seances WHERE date >= '$dateDebut' AND date <= '$dateFin' ";
	if ($annule == 1) {
		$sql.="	AND (coursannule != 1 OR coursannule IS NULL)";
	}
	if ($prestation == 1) {
		$sql.=" AND (prestation != 0 AND prestation IS NOT NULL)";
	}
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function listingQuantiteClasseEnseignant($datedebut,$datefin,$idPers,$idClasse,$prestation,$annule) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($datedebut);
	$dateFin=dateFormBase($datefin);
	$sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule FROM ${prefixe}edt_seances WHERE date >= '$dateDebut' AND date <= '$dateFin'";
	if ($annule == 1) {
		$sql.="	AND (coursannule != 1 OR coursannule IS NULL)";
	}
	if ($prestation == 1) {
		$sql.=" AND (prestation != 0 AND prestation IS NOT NULL)";
	}       
	$sql.=" AND idclasse='$idClasse' AND idprof='$idPers' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function listingQuantiteMatiereClasse($datedebut,$datefin,$idMatiere,$idClasse,$prestation,$annule) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($datedebut);
	$dateFin=dateFormBase($datefin);
	$sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule FROM ${prefixe}edt_seances WHERE date >= '$dateDebut' AND date <= '$dateFin'";
	if ($annule == 1) {
		$sql.="	AND (coursannule != 1 OR coursannule IS NULL)";
	}
	if ($prestation == 1) {
		$sql.=" AND (prestation != 0 AND prestation IS NOT NULL)";
	}       
	$sql.=" AND idclasse='$idClasse' AND idmatiere='$idMatiere' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function listingQuantiteMatiereEnseignant($datedebut,$datefin,$idMatiere,$idPers,$prestation,$annule) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($datedebut);
	$dateFin=dateFormBase($datefin);
	$sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule FROM ${prefixe}edt_seances WHERE date >= '$dateDebut' AND date <= '$dateFin'";
	if ($annule == 1) {
		$sql.="	AND (coursannule != 1 OR coursannule IS NULL)";
	}
	if ($prestation == 1) {
		$sql.=" AND (prestation != 0 AND prestation IS NOT NULL)";
	}       
	$sql.="	AND idprof='$idPers' AND idmatiere='$idMatiere' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}



function recupCoursAnnule() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule FROM ${prefixe}edt_seances WHERE coursannule='1' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;

}


function recupCoursduJour($date) {
	global $cnx;
	global $prefixe;
	$datedujour=dateFormBase($date);
	$sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule FROM ${prefixe}edt_seances WHERE (coursannule != '1' OR coursannule IS NULL)  AND date='$datedujour' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function chercheSousMatiereNom($id_matiere) {
	global $cnx;
	global $prefixe;
	$id_matiere=trim($id_matiere);
	if ($id_matiere > 0) {
		$sql="SELECT code_mat,trim(libelle),trim(sous_matiere) FROM ${prefixe}matieres WHERE code_mat='$id_matiere' ORDER BY libelle ";
		$res=execSql($sql);
		$data=chargeMat($res);
		freeResult($res);
		unset($res);
		if(!$data[0][2]){
			$data[0][2]='';
		}
		return $data[0][2];	
	}
}


//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//
function en_attente() {
	$resultat="<img src='/image/cubemv2.gif' align=center>";
	$resultat.="<img src='/image/cubemv1.gif' align=center>";
	$resultat.="<img src='/image/cubemv.gif' align=center>";
	print $resultat;
}
//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//
function chercheSanction($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_sanc,libelle FROM ${prefixe}type_sanction WHERE id_sanc='$id'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function chercheIdSanction($libelle){
	global $cnx;
	global $prefixe;
	$sql="SELECT id_sanc,libelle FROM ${prefixe}type_sanction WHERE libelle='$libelle'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}

function rechercheSanction($id) {
	global $cnx;
	global $prefixe;
$sql=<<<EOF
SELECT id_sanc,libelle
FROM ${prefixe}type_sanction
WHERE id_sanc='$id'
EOF;
        $res=execSql($sql);
        $data=chargeMat($res);
        return ucwords($data[0][1]);
}

//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//
function nb_sanction($sanction) {
	global $cnx;
	global $prefixe;
$sql=<<<EOF
SELECT sanction,nb,origin_user,date_saisie
FROM ${prefixe}type_nb_sanction
WHERE sanction='$sanction'
EOF;
   $res=execSql($sql);
   $data=chargeMat($res);
   return $data;
}


function Recherche_nb_sanction_eleve($sanction,$id_eleve) {
	global $cnx;
	global $prefixe;
$sql=<<<EOF
SELECT id_category,id_eleve,enr_en_retenue
FROM ${prefixe}discipline_sanction
WHERE id_category='$sanction' AND id_eleve='$id_eleve' AND enr_en_retenue='false'
EOF;
   $res=execSql($sql);
   $data=chargeMat($res);
   return $data;
}

function recherche_eleve_prenom($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id,prenom FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$valeur=ucwords(strtolower(trim($data[0][1])));
	return $valeur;
}

function recherche_eleve_nom($id_eleve) {
	global $cnx;
	global $prefixe;
	if ($id_eleve > 0) {
		$sql="SELECT elev_id,nom FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
		$res=execSql($sql);
		$data=chargeMat($res);
		$valeur=strtoupper(trim($data[0][1]));
		return $valeur;
	}
}

function recherche_eleve_prenom_parent($id_eleve,$parent) {
	global $cnx;
	global $prefixe;
	if ($parent == 1) { $sql="SELECT elev_id, prenomtuteur FROM  ${prefixe}eleves WHERE elev_id='$id_eleve'"; 	}
	if ($parent == 2) { $sql="SELECT elev_id, prenom_resp_2 FROM ${prefixe}eleves WHERE elev_id='$id_eleve'"; 	}
	$res=execSql($sql);
   	$data=chargeMat($res);
   	$valeur=ucwords(strtolower(trim($data[0][1])));
   	return $valeur;
}


function recherche_eleve_civ_parent($id_eleve,$parent) {
	global $cnx;
	global $prefixe;
	if ($parent == 1) { $sql="SELECT elev_id, civ_1 FROM ${prefixe}eleves WHERE elev_id='$id_eleve'"; }
	if ($parent == 2) { $sql="SELECT elev_id, civ_1 FROM ${prefixe}eleves WHERE elev_id='$id_eleve'"; }
	$res=execSql($sql);
   	$data=chargeMat($res);
   	$valeur=civ($data[0][1]);
   	return $valeur;
}

function recherche_eleve_nom_parent($id_eleve,$parent) {
	global $cnx;
	global $prefixe;
	if ($parent == 1) { $sql="SELECT elev_id, nomtuteur FROM ${prefixe}eleves WHERE elev_id='$id_eleve'"; }
	if ($parent == 2) { $sql="SELECT elev_id, nom_resp_2 FROM ${prefixe}eleves WHERE elev_id='$id_eleve'"; }
   	$res=execSql($sql);
   	$data=chargeMat($res);
   	$valeur=strtoupper(trim($data[0][1]));
   	return $valeur;
}


function recherche_eleve($id_eleve) {
	global $cnx;
	global $prefixe;
$sql=<<<EOF
SELECT elev_id,nom,prenom
FROM ${prefixe}eleves
WHERE elev_id='$id_eleve'
EOF;

   $res=execSql($sql);
   $data=chargeMat($res);
   $valeur=strtoupper(trim($data[0][1]))." ".ucwords(trim($data[0][2]));
   return $valeur;
}

function chercheIdEleve($nom,$prenom) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id,nom,prenom FROM ${prefixe}eleves WHERE trim(lower(nom))='$nom' AND trim(lower(prenom))='$prenom' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$valeur=$data[0][0];
	return $valeur;
}

function recherche_photo_pers($idpers) {
	global $cnx;
	global $prefixe;

$sql=<<<EOF
SELECT pers_id,photo
FROM ${prefixe}personnel
WHERE pers_id='$idpers'
EOF;

   $res=execSql($sql);
   $data=chargeMat($res);
   $valeur=$data[0][1];
   return $valeur;

}


function recherche_photo_eleve($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id,photo FROM ${prefixe}eleves WHERE elev_id='$ideleve'";
	$res=execSql($sql);
   	$data=chargeMat($res);
   	$valeur=$data[0][1];
   	return $valeur;
}

function chercheIdClasseDunEleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id,classe FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
   	$data=chargeMat($res);
   	freeResult($res);
   	$valeur=$data[0][1];
   	return $valeur;
}


function chercheDateNaissance($id_eleve) {
	global $cnx;
	global $prefixe;
$sql=<<<EOF
SELECT elev_id,date_naissance
FROM ${prefixe}eleves
WHERE elev_id='$id_eleve'
EOF;

   $res=execSql($sql);
   $data=chargeMat($res);
   freeResult($res);
   $valeur=$data[0][1];
   return $valeur;
}

function recherche_personne2($id) {
        global $cnx;
        global $prefixe;
if ($id != "") {
$sql=<<<EOF
SELECT pers_id,nom,prenom,civ
FROM ${prefixe}personnel
WHERE pers_id='$id'
EOF;

   $res=execSql($sql);
   $data=chargeMat($res);
   $valeur=civ($data[0][3])." ".TextNoAccent(strtoupper(trim($data[0][1])));
   return $valeur;
   }
}





function recherche_personne($id) {
	global $cnx;
	global $prefixe;
	if ($id > 0) {
		$sql="SELECT pers_id,nom,prenom,civ FROM ${prefixe}personnel WHERE pers_id='$id'";
		$res=execSql($sql);
   		$data=chargeMat($res);
   		if ($data[0][1] != "") {
	   		$valeur=civ($data[0][3])." ".strtoupper(trim($data[0][1]))." ".ucwords(trim($data[0][2]));
   		}else{
	   		$valeur="";
   		}
   		return $valeur;
   	}else{
		if ($id == "-1") return "Message Automatique";
	}
}

function recherche_personne_prenom($id,$type_pers) {
	global $cnx;
	global $prefixe;
	if (($type_pers == "PAR") || ($type_pers == "ELE")){
		$sql="SELECT elev_id,prenom FROM ${prefixe}eleves WHERE elev_id='$id' ";
   		$res=execSql($sql);
		$data=chargeMat($res);
		$val=trim($data[0][1]);
	}else{
		$sql="SELECT pers_id,prenom FROM ${prefixe}personnel WHERE pers_id='$id'";
		$res=execSql($sql);
		$data=chargeMat($res);
		$val=trim($data[0][1]);
	}
	return $val;
}

function recherche_personne_nom($id,$type_pers) {
	global $cnx;
	global $prefixe;
	if (($type_pers == "PAR") || ($type_pers == "ELE")){
		$sql="SELECT elev_id,nom FROM ${prefixe}eleves WHERE elev_id='$id' ";
   		$res=execSql($sql);
		$data=chargeMat($res);
		$val=trim($data[0][1]);
	}else{
		$sql="SELECT pers_id,nom,prenom,civ FROM ${prefixe}personnel WHERE pers_id='$id'";
		$res=execSql($sql);
		$data=chargeMat($res);
		$val=trim($data[0][1]);
	}
	return $val;
}

function recherche_personne_modif($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id,nom,prenom,mdp,civ,email,adr,code_post,commune,tel,tel_port,identifiant,offline,id_societe_tuteur,pays,indice_salaire,qualite FROM ${prefixe}personnel WHERE pers_id='$id'";
	$res=execSql($sql);
   	$data=chargeMat($res);
   	return $data;
}

//--------------------------------------------------------------------//

function chercheIdPersonne($nom,$prenom,$civ) {
	global $cnx;
	global $prefixe;
$sql=<<<EOF
SELECT pers_id,nom,prenom,type_pers
FROM ${prefixe}personnel
WHERE nom='$nom' AND prenom='$prenom' AND type_pers='$civ'
EOF;
   $res=execSql($sql);
   $data=chargeMat($res);
   $valeur=$data[0][0];
   return $valeur;
}

function chercheIdPersonne2($nom,$prenom) {
	global $cnx;
	global $prefixe;
	$nom=strtolower($nom);
	$prenom=strtolower($prenom);
$sql=<<<EOF
SELECT pers_id,nom,prenom,type_pers
FROM ${prefixe}personnel
WHERE nom='$nom' AND prenom='$prenom'
EOF;
   $res=execSql($sql);
   $data=chargeMat($res);
   $valeur=$data[0][0];
   return $valeur;
}

function chercheIdPersonneEDT($nom,$prenom,$membre) {
	global $cnx;
	global $prefixe;
	$nom=strtolower($nom);
	$prenom=strtolower($prenom);
$sql=<<<EOF
SELECT pers_id,nom,prenom,type_pers
FROM ${prefixe}personnel
WHERE nom='$nom' AND prenom='$prenom' AND type_pers='$membre'
EOF;
   $res=execSql($sql);
   $data=chargeMat($res);
   $valeur=$data[0][0];
   return $valeur;
}


function cherchePersonneExist($nom,$prenom,$membre) {
	global $cnx;
	global $prefixe;
	$nom=strtolower($nom);
	$prenom=strtolower($prenom);
$sql=<<<EOF
SELECT pers_id,nom,prenom
FROM ${prefixe}personnel
WHERE lower(nom)='$nom' AND lower(prenom)='$prenom' AND type_pers='$membre'
EOF;
   $res=execSql($sql);
   $data=chargeMat($res);
   if (count($data) > 0) {
   		return true;
   } else {
   		return false;
   }
}

function cherchePersonnelPhotoId() {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id,nom,prenom FROM ${prefixe}personnel ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function chercheElevePhotoId() {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id,nom,prenom FROM ${prefixe}eleves ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function recherche_type_personne($id) {
	global $cnx;
	global $prefixe;
$sql=<<<EOF
SELECT *
FROM ${prefixe}personnel
WHERE pers_id='$id'
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	$valeur=$data[0][5];
	return $valeur;
}

//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//
// modif sanction
function modif_discipline_sanction($id_eleve,$user,$date_saisie,$sanction) {
	global $cnx;
	global $prefixe;
	if(DBTYPE=='pgsql') { $vrai='true'; }
    if(DBTYPE=='mysql') { $vrai=1; }
	$sql="UPDATE ${prefixe}discipline_sanction SET date_saisie='$date_saisie',origin_saisie='$user',enr_en_retenue=$vrai WHERE id_eleve='$id_eleve' AND id_category='$sanction'";
	return(execSql($sql));
}


function modif_discipline_sanction_2($id_eleve,$user,$date_saisie,$sanction) {
	global $cnx;
	global $prefixe;
	if(DBTYPE=='pgsql') { $vrai='true'; }
    if(DBTYPE=='mysql') { $vrai=1; }
	$sql="UPDATE ${prefixe}discipline_sanction SET date_saisie='$date_saisie',origin_saisie='$user',enr_en_retenue=$vrai WHERE id_eleve='$id_eleve' AND id_category='$sanction'";
	return(execSql($sql));
}

//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//
function hashPostVar($noms)
{
global $_POST;
for($i=0;$i<count($noms);$i++){
	$clepost=$noms[$i];
	$cletab=$noms[++$i];
	$tab[$cletab]=trim($_POST[$clepost]);
	}
return $tab;
}

function hashGetVar($noms)
{
global $_GET;
for($i=0;$i<count($noms);$i++){
	$clepost=$noms[$i];
	$cletab=$noms[++$i];
	$tab[$cletab]=strtolower(trim($_GET[$clepost]));
	}
return $tab;
}

function acces($data) {
	global $cnx;
	global $prefixe;
	$im=$data['membre'];
	switch($im) {
		case 'administrateur' :
			$im='adm';
			break;
		case 'vie scolaire':
			$im='mvs';
			break;
		case 'enseignant':
			$im='ens';
			break;
		case 'parent':
			$im='parent';
			break;
		case 'eleve' :
			$im='eleve';
			break;
		case 'tuteurstage' :
			$im='tut';
			break;
		case 'personnel' :
			$im='per';
			break;
		default:
			error(0);
			break;
	}
	$in=$data['nom'];
	$ip=$data['prenom'];
	$ipwd=cryptage($data[pwd]);
	$in=preg_replace('/\(/','',$in);
	$ip=preg_replace('/\(/','',$ip);
	$in=preg_replace('/;/','',$in);
	$ip=preg_replace('/;/','',$ip);

	if($im == 'adm' || $im == 'mvs' || $im == 'ens'  || $im == 'tut' || $im == 'per' ) {
$sql=<<<EOF
SELECT COUNT(*) FROM ${prefixe}personnel
WHERE
	lower(trim(nom))='$in'
AND	lower(trim(prenom))='$ip'
AND	mdp='$ipwd'
AND	lower(trim(type_pers))='$im'
AND	offline = '0'
EOF;
	}else if($im == 'parent') {
$sql=<<<EOF
SELECT COUNT(*) FROM ${prefixe}eleves
WHERE
	lower(trim(nom))='$in' AND lower(trim(prenom))='$ip' AND ( passwd='$ipwd' OR passwd_parent_2='$ipwd' )
EOF;
	}else if($im == 'eleve') {
$sql=<<<EOF
SELECT COUNT(*) FROM ${prefixe}eleves
WHERE
	lower(trim(nom))='$in'
AND	lower(trim(prenom))='$ip'
AND     passwd_eleve='$ipwd'
AND	compte_inactif='0'
EOF;
	}else{
	error(0);
	}
	$exec=execSql($sql);
$res=chargeMat($exec);
return $res[0][0];
}


function accesViaEmail($email,$password,$membre) {
	global $cnx;
	global $prefixe;	
	$ipwd=cryptage($password);
	$email=preg_replace('/\(/','',$email);
	$password=preg_replace('/\(/','',$password);
	$email=preg_replace('/;/','',$email);
	$password=preg_replace('/;/','',$password);
	if ($membre == "menueleve") { 
		$sql="SELECT COUNT(*) FROM ${prefixe}eleves WHERE passwd_eleve='$ipwd' AND compte_inactif = '0' AND (email_eleve='$email'  OR emailpro_eleve='$email')";
		$exec=execSql($sql);
		$res=chargeMat($exec);
		return $res[0][0];
	}
	
	if ($membre == "menuprof") { 
		$sql="SELECT COUNT(*) FROM ${prefixe}personnel WHERE mdp='$ipwd' AND offline = '0' AND email='$email' AND type_pers='ENS' ";
		$exec=execSql($sql);
		$res=chargeMat($exec);
		return $res[0][0];
	}
	
	if ($membre == "menuadmin") { 
		$sql="SELECT COUNT(*) FROM ${prefixe}personnel WHERE mdp='$ipwd' AND offline = '0' AND email='$email' AND type_pers='ADM' ";
		$exec=execSql($sql);
		$res=chargeMat($exec);
		return $res[0][0];
	}
	
	return(0);
}

function accesWap($email,$pass,$membre) {
	global $cnx;
	global $prefixe;
	if ($membre == "menuparent") {
		$sql="SELECT id_pers,nom,prenom FROM ${prefixe}eleves WHERE (lower(trim(email))='$email' AND passwd='$pass') OR (lower(trim(email_resp_2))='$email' AND passwd_parent_2='$pass')";
		$exec=execSql($sql);
		$res=chargeMat($exec);
		return($res);
	}
}



function rechercheParent($ideleve,$password) {
	global $cnx;
	global $prefixe;
	$ipwd=cryptage($password);
	$sql="SELECT * FROM ${prefixe}eleves WHERE  elev_id='$ideleve' AND  passwd='$ipwd'";
       	$exec=execSql($sql);
	$data=chargeMat($exec);
	if (count($data) > 0 ) {
		return '1';
	}
	$sql="SELECT * FROM ${prefixe}eleves WHERE  elev_id='$ideleve' AND  passwd_parent_2='$ipwd' ";
       	$exec=execSql($sql);
	$data=chargeMat($exec);
	if (count($data) > 0 ) {
		return '2';
	}
}


//---------------------------------------------------------------------//
// mise en place d'affectation


function visu_affectation($anneeScolaire='') {
        global $cnx;
	global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($anneeScolaire == "") $anneeScolaire=anneeScolaireViaIdClasse();
        $sql="SELECT distinct code_classe,f.libelle FROM ${prefixe}affectations e, ${prefixe}classes f WHERE e.annee_scolaire='$anneeScolaire' AND e.code_classe=f.code_class GROUP BY e.code_classe ORDER BY f.libelle ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}



function visu_affectation_2($tri,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$sql="SELECT distinct code_classe,f.libelle FROM ${prefixe}affectations e, ${prefixe}classes f WHERE e.code_classe=f.code_class AND trim='$tri' AND annee_scolaire='$anneeScolaire'  ORDER BY f.libelle ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function visu_affectation_2_prof($tri,$idclasse,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$sql="SELECT distinct code_classe,f.libelle FROM ${prefixe}affectations e, ${prefixe}classes f WHERE e.code_classe=f.code_class AND trim='$tri' AND f.code_class='$idclasse' AND annee_scolaire='$anneeScolaire' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function tauxBoursier($idclasse,$nbeleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}eleves WHERE classe='$idclasse' AND boursier='1'";
        $res=execSql($sql);
        $data=chargeMat($res);
	$nbboursier=count($data);
	if ($nbeleve != 0) {
		return(($nbboursier/$nbeleve)*100);
	}else{
		return(0);
	}
}


function nbBoursier($idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT count(*) FROM ${prefixe}eleves WHERE classe='$idclasse' AND boursier='1'";
        $res=execSql($sql);
        $data=chargeMat($res);
	return($data[0][0]);
}

function moyenneIndemnite($idclasse,$nbeleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT indemnite_stage FROM ${prefixe}eleves WHERE classe='$idclasse' AND indemnite_stage != ''";
        $res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$indemnite_stage+=$data[$i][0];
		$nb++;
	}
	if ($nb != 0) {
		return($indemnite_stage/$nb);
	}else{
		return(0);
	}
}


function visu_affectation_detail_cahier_texte($id_classe,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
$sql="
SELECT
	ordre_affichage,
	code_matiere,
	code_prof,
	code_classe,
	coef,
	g.libelle,
	a.langue,
	a.avec_sous_matiere,
	a.visubull,
	a.nb_heure
FROM
	${prefixe}affectations a,
	${prefixe}groupes g
WHERE
	code_classe='$id_classe'
	AND   a.code_groupe = g.group_id
	AND a.annee_scolaire='$anneeScolaire'


ORDER BY 1
";

// GROUP BY code_prof

        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function visu_affectation_detail_cahier_texte_ens($id_classe,$anneeScolaire,$idpers) {
        global $cnx;
        global $prefixe;
$sql="
SELECT
        ordre_affichage,
        code_matiere,
        code_prof,
        code_classe,
        coef,
        g.libelle,
        a.langue,
        a.avec_sous_matiere,
        a.visubull,
        a.nb_heure
FROM
        ${prefixe}affectations a,
        ${prefixe}groupes g
WHERE
        code_classe='$id_classe'
        AND   a.code_groupe = g.group_id
        AND a.annee_scolaire='$anneeScolaire'
        AND a.visubull = '1'
        AND a.code_prof='$idpers'

ORDER BY 1
";

        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;


}









function visu_affectation_detail_ens($idpers,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == '') $anneeScolaire=$_COOKIE["anneeScolaire"];
$sql="
SELECT
	ordre_affichage,
	code_matiere,
	code_prof,
	code_classe,
	coef,
	g.libelle,
	a.langue,
	a.avec_sous_matiere,
	a.visubull,
	a.nb_heure
FROM
	${prefixe}affectations a,
	${prefixe}groupes g
WHERE
	code_prof='$idpers'
AND   a.annee_scolaire = '$anneeScolaire'
GROUP BY code_classe,code_matiere
ORDER BY code_classe

";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}







function visu_affectation_detail($id_classe,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == '') $anneeScolaire=$_COOKIE["anneeScolaire"];
$sql="
SELECT
	ordre_affichage,
	code_matiere,
	code_prof,
	code_classe,
	coef,
	g.libelle,
	a.langue,
	a.avec_sous_matiere,
	a.visubull,
	a.nb_heure
FROM
	${prefixe}affectations a,
	${prefixe}groupes g
WHERE
	code_classe='$id_classe'
AND   a.code_groupe = g.group_id
AND   a.annee_scolaire = '$anneeScolaire'
ORDER BY 1
";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}



function visu_affectation_detail_2($id_classe,$tri,$anneeScolaire) {
        global $cnx;
        global $prefixe;
$sql="
SELECT
        ordre_affichage,
        code_matiere,
        code_prof,
        code_classe,
        coef,
        g.libelle,
        a.langue,
        a.avec_sous_matiere,
        a.visubull,
        a.nb_heure,
        a.ects,
        a.id_ue_detail,
        a.specif_etat,
        a.annee_scolaire,
        a.visubullbtsblanc,
	a.num_semestre_info,
	a.trim,
	a.coef_certif,
	a.note_planche
FROM
        ${prefixe}affectations a,
        ${prefixe}groupes g
WHERE
        code_classe='$id_classe'
AND     trim='$tri'
AND     a.annee_scolaire='$anneeScolaire'
AND     a.code_groupe = g.group_id
ORDER BY 1
";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function liste_eleve_etude($id_etude) {
        global $cnx;
        global $prefixe;
        $sql="SELECT a.id_eleve,a.id_etude,a.information,a.`auto_exit` FROM ${prefixe}etude_affect a , ${prefixe}eleves e WHERE  a.`id_etude`='$id_etude' AND a.id_eleve=e.elev_id ORDER BY e.nom";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
} 


function visu_affectation_detail_bulletin($id_classe) {
	global $cnx;
	global $prefixe;  
	$anneeScolaire=$_COOKIE["anneeScolaire"];
$sql="
SELECT
	ordre_affichage,
	code_matiere,
	code_prof,
	code_classe,
	coef,
	g.libelle,
	a.langue,
	a.avec_sous_matiere,
	a.visubull,
	a.ects
FROM
	${prefixe}affectations a,
	${prefixe}groupes g
WHERE
	code_classe='$id_classe'
AND   	a.code_groupe = g.group_id
AND	a.visubull = 1
AND 	a.annee_scolaire='$anneeScolaire'
ORDER BY 1
";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

// verification si le prof est affecte à une classe
function verif_utiliser($idProf) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_prof FROM ${prefixe}affectations WHERE code_prof='$idProf' ";
        $res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0 ) {
        	return 0 ;
	}else {
	        return 1 ;
	}
}

function listingMatiereProf($idProf,$idclasse) {
        global $cnx;
        global $prefixe;
        if ($idclasse == "tous") {
                $sql="SELECT  code_matiere,code_classe  FROM ${prefixe}affectations WHERE code_prof='$idProf' ";
        }else{
                $sql="SELECT  code_matiere,code_classe  FROM ${prefixe}affectations WHERE code_prof='$idProf' AND code_classe='$idclasse'";
        }
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}


function verifProfDansGroupe($idProf,$idgroup) {
	global $cnx;
	global $prefixe;
	// PAS DE VERIF ANNEE SCOLAIRE
	$sql="SELECT  code_prof FROM ${prefixe}affectations WHERE code_prof='$idProf' AND code_groupe='$idgroup' ";
        $res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0 ) {
        	return 0 ;
	}else {
	        return 1 ;
	}
}


function verifProfDansClasse($idProf,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  code_prof FROM ${prefixe}affectations WHERE code_prof='$idProf' AND code_classe='$idclasse' ";
        $res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0 ) {
        	return 0 ;
	}else {
	        return 1 ;
	}
}

//--------------------------------------------------------------------//
function verifGroupeAffectation($id_grp) {
	global $cnx;
	global $prefixe;
	// PAS DE VERIF ANNEE SCOLAIRE
	$sql="SELECT code_groupe FROM ${prefixe}affectations WHERE code_groupe='$id_grp'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data > 0) {
		return 1;
	}else{
		return 0;
	}
}

function verif_utiliser_matiere($idmatiere) {
	global $cnx;
	global $prefixe;
	// PAS DE VERIF ANNEE SCOLAIRE
	$sql="SELECT code_matiere FROM ${prefixe}affectations WHERE code_matiere='$idmatiere'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data > 0) {
		return 1;
	}else{
		return 0;
	}
}

function verif_eleve_classe($idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}eleves  WHERE classe='$idclasse'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data > 0) {
		return 1;
	}else{
		return 0;
	}
}

function nbEleve($idclasse,$anneeScolaire="") {
        global $cnx;
        global $prefixe;
        if (trim($anneeScolaire) == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        if (trim($anneeScolaire) == "") $anneeScolaire=anneeScolaireViaIdClasse();


	$sql="(SELECT elev_id,nom,prenom FROM ${prefixe}eleves , ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e , ${prefixe}classes c , ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.elev_id)";
	$res=execSql($sql);
        $data=chargeMat($res);
/*
        $sql="SELECT * FROM ${prefixe}eleves  WHERE classe='$idclasse' AND annee_scolaire='$anneeScolaire' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) == 0) {
                $sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  ORDER BY e.nom";
                $res=execSql($sql);
                $data=chargeMat($res);
	}
*/
        if ($data > 0) {
                return count($data);
        }else{
                return 0;
        }
}

function nbEleveTotal() {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="(SELECT elev_id,nom,prenom FROM ${prefixe}eleves WHERE annee_scolaire='$anneeScolaire') UNION (SELECT e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  GROUP BY e.elev_id )";
	$res=execSql($sql);
	$data=chargeMat($res);

/*	$sql="SELECT * FROM ${prefixe}eleves WHERE annee_scolaire='$anneeScolaire'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) == 0) {
                $sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'";
		$res=execSql($sql);
		$data=chargeMat($res);
	} */

	if ($data > 0) {
		return count($data);
	}else{
		return 0;
	}
}


function nbEleveInterne($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];

	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire'  AND lower(regime)='interne' ) UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  AND lower(e.regime)='interne'  GROUP BY e.elev_id)";
	$res=execSql($sql);
	$data=chargeMat($res);
	
/*
	$sql="SELECT * FROM ${prefixe}eleves  WHERE classe='$idclasse' AND lower(regime)='interne'  AND annee_scolaire='$anneeScolaire'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) == 0) {
		$sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  AND lower(regime)='interne' ";
		$res=execSql($sql);
		$data=chargeMat($res);
	}
 */
	if ($data > 0) {
		return count($data);
	}else{
		return 0;
	}
}




function nbEleveDemiPension($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];

	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire' AND ( lower(regime)='demi pension' OR lower(regime)='demi-pension') ) UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' AND ( lower(e.regime)='demi pension' OR lower(e.regime)='demi-pension') GROUP BY e.elev_id )";
	$res=execSql($sql);
	$data=chargeMat($res);

/*	$sql="SELECT * FROM ${prefixe}eleves  WHERE classe='$idclasse' AND ( lower(regime)='demi pension' OR lower(regime)='demi-pension') AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) == 0) {
		$sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' AND ( lower(regime)='demi pension' OR lower(regime)='demi-pension') ";
		$res=execSql($sql);
		$data=chargeMat($res);
	}
*/
	if ($data > 0) {
		return count($data);
	}else{
		return 0;
	}
}

function nombre_absJustifie($id_eleve,$dateDebut,$dateFin) {
        global $cnx;
        global $prefixe;
        $sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND justifier = '1' AND  duree_ab >= '0.5' ";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        for($i=0;$i<count($data_2);$i++) {
                if ($data_2[$i][4] > 0) {
                        $nb=$nb+$data_2[$i][4];
                }
        }
        return $nb*2;
}


function nombre_absJustifieMaladie($id_eleve,$dateDebut,$dateFin) {
        global $cnx;
        global $prefixe;
        $sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND justifier = '1' AND lower(motif) = 'certificat médical' AND duree_ab >= '0.5' ";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        $nb=0;
        for($i=0;$i<count($data_2);$i++) {
                if ($data_2[$i][4] > 0) {
                        $nb=$nb+$data_2[$i][4];
                }
        }
        return $nb*2;
}



function nbEleveExterne($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];

	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire' AND lower(regime)='externe' ) UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' AND lower(regime)='externe' GROUP BY e.elev_id  )";
	$res=execSql($sql);
	$data=chargeMat($res);
/*
	$sql="SELECT * FROM ${prefixe}eleves  WHERE classe='$idclasse' AND lower(regime)='externe' AND annee_scolaire='$anneeScolaire'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) == 0) {
                $sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' AND lower(regime)='externe'";
        	$res=execSql($sql);
		$data=chargeMat($res);
	}
*/	
	if ($data > 0) {
		return count($data);
	}else{
		return 0;
	}
}

function nbEleveRegimeInconnu($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];

	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire' AND (regime IS NULL  OR regime='') ) UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' AND (regime IS NULL  OR regime='') GROUP BY e.elev_id )";
	$res=execSql($sql);
	$data=chargeMat($res);
/*
	$sql="SELECT * FROM ${prefixe}eleves  WHERE classe='$idclasse' AND (regime IS NULL  OR regime='') AND annee_scolaire='$anneeScolaire'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) == 0) {
		$sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' AND (regime IS NULL  OR regime='')";
		$res=execSql($sql);
		$data=chargeMat($res);
	}
 */	
	if ($data > 0) {
		return count($data);
	}else{
		return 0;
	}
}



function verif_utiliser_classe($idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_classe FROM ${prefixe}affectations WHERE code_classe='$idclasse'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data > 0) {
		return 1;
	}else{
		return 0;
	}
}


function verifPresenceId($idpers,$typemembre) {
	global $cnx;
	global $prefixe;
	if ($typemembre == "NOPERS") {
		$sql="SELECT elev_id FROM ${prefixe}eleves WHERE elev_id='$idpers'";
	}else{
		$sql="SELECT pers_id FROM ${prefixe}personnel WHERE pers_id='$idpers'";
	}
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data > 0) {
		return 1;
	}else{
		return 0;
	}
}

//---------------------------------------------------------------------//
//---------------------------------------------------------------------//
// Mise en place d'envoi de message
function envoi_messagerie($emetteur,$destinataire,$objet,$text,$date,$heure,$type_personne,$type_personne_dest,$number,$idpiecejointe,$brouillon=0) {
	global $cnx;
	global $prefixe;
	if (!get_magic_quotes_gpc()) {
	    $objet = addslashes($objet);
	    $text = addslashes($text);
	}

	$nbenvoi=0;
	if (FORWARDMAIL == "oui") {
        	if ($type_personne_dest == "ADM") { $membre="menuadmin"; }
        	if ($type_personne_dest == "PAR") { $membre="menuparent"; }
        	if ($type_personne_dest == "ENS") { $membre="menuprof"; }
        	if ($type_personne_dest == "MVS") { $membre="menuscolaire"; }
        	if ($type_personne_dest == "TUT") { $membre="menututeur"; }
        	if ($type_personne_dest == "PER") { $membre="menupersonnel"; }

            $nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
            $prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
            if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre)) {
                  $email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre);
                  $http=protohttps(); // return http:// ou https://
		  $servername=$_SERVER["SERVER_NAME"];
                  $lien="$http".$servername."/";
                  envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,recherche_personne($emetteur),$number,$objet,$destinataire) ;
            }
        }

	
	if ($type_personne_dest == "GRPMAIL") {
		$idgroupe=$destinataire;
		$data=liste_idpers_mail($destinataire);
		$listeid=liste_idpers_grp_mail($data);
		foreach($listeid as $idunique) {
			$type_personne_dest=recherche_type_personne($idunique);
			if (verifPresenceId($idunique,'PERS')) {
				$idmess_envoyer=md5(uniqid(rand()));
				$sql="INSERT INTO ${prefixe}messageries (emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,lu_par_utilisateur,idforward_mail,idmess_envoyer,idpiecejointe,idgroupe,brouillon) VALUES ('$emetteur','$idunique','$text','$date','$heure','false','$type_personne','$objet','$type_personne_dest','false','$number','$idmess_envoyer','$idpiecejointe','$idgroupe','$brouillon')";
				$rt=execSql($sql);
				if ($rt) $nbenvoi++;
				if ($brouillon == 0) {
					$sql="INSERT INTO ${prefixe}messagerie_envoyer (emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,lu_par_utilisateur,idforward_mail,idmess_envoyer,idpiecejointe) VALUES ('$emetteur','$idunique','$text','$date','$heure','false','$type_personne','$objet','$type_personne_dest','false','$number','$idmess_envoyer','$idpiecejointe')";
					execSql($sql);
					$loginMSN=recherche_personne_nom($idunique,$type_personne_dest).".".recherche_personne_prenom($idunique,$type_personne_dest);
					envoiMessageIntraMSN("Vous avez reçu un message \"$objet\"  sur votre compte Triade",$loginMSN);
				}

			}

		}
		return($nbenvoi);

	}elseif ($type_personne_dest == "GRPMAILELEV") {
		$idgroupe=$destinataire;
		$data=liste_idpers_mail($destinataire);
		$listeid=liste_idpers_grp_mail($data);
		foreach($listeid as $idunique) {
			$type_personne_dest="ELE";
			$idmess_envoyer=md5(uniqid(rand()));
			if (verifPresenceId($idunique,'NOPERS')) {
				$sql="INSERT INTO ${prefixe}messageries (emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,lu_par_utilisateur,idforward_mail,idmess_envoyer,idpiecejointe,idgroupe,brouillon) VALUES ('$emetteur','$idunique','$text','$date','$heure','false','$type_personne','$objet','$type_personne_dest','false','$number','$idmess_envoyer','$idpiecejointe','$idgroupe','$brouillon')";
				$rt=execSql($sql);
				if ($rt) $nbenvoi++;

				if ($brouillon == 0) {
					$sql="INSERT INTO ${prefixe}messagerie_envoyer (emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,lu_par_utilisateur,idforward_mail,idmess_envoyer,idpiecejointe,idgroupe) VALUES ('$emetteur','$idunique','$text','$date','$heure','false','$type_personne','$objet','$type_personne_dest','false','$number','$idmess_envoyer','$idpiecejointe','$idgroupe')";
					execSql($sql);
			
					$loginMSN=recherche_personne_nom($idunique,$type_personne_dest).".".recherche_personne_prenom($idunique,$type_personne_dest);
					envoiMessageIntraMSN("Vous avez reçu un message \"$objet\"  sur votre compte Triade",$loginMSN);
				}
			}

		}
		return($nbenvoi);

	}else{
	    $idgroupe="0";
	    $idmess_envoyer=md5(uniqid(rand()));
	    $sql="INSERT INTO ${prefixe}messageries (emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,lu_par_utilisateur,idforward_mail,idmess_envoyer,idpiecejointe,idgroupe,brouillon) VALUES ('$emetteur','$destinataire','$text','$date','$heure','false','$type_personne','$objet','$type_personne_dest','false','$number','$idmess_envoyer','$idpiecejointe','$idgroupe','$brouillon')";
	    $rt=execSql($sql);
	    if ($rt) $nbenvoi++;
	    if ($brouillon == 0) {
		    $sql="INSERT INTO ${prefixe}messagerie_envoyer (emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,lu_par_utilisateur,idforward_mail,idmess_envoyer,idpiecejointe) VALUES ('$emetteur','$destinataire','$text','$date','$heure','false','$type_personne','$objet','$type_personne_dest','false','$number','$idmess_envoyer','$idpiecejointe')";
		    $loginMSN=recherche_personne_nom($destinataire,$type_personne_dest).".".recherche_personne_prenom($destinataire,$type_personne_dest);
		    envoiMessageIntraMSN("Vous avez reçu un message \"$objet\"  sur votre compte Triade",$loginMSN);
		    $rt=execSql($sql);
	    }
	    return($nbenvoi);
	}
}


function affichage_messagerie_envoyer_limit($type_personne,$destinataire,$offset,$limit,$idrep) {
	global $cnx;
	global $prefixe;
	if ($idrep == "") {
		$idrep="(repertoire IS NULL OR repertoire = '0')";
	}else{
		$idrep="repertoire='$idrep'";
	}

	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, lu_par_utilisateur, idpiecejointe FROM ${prefixe}messagerie_envoyer WHERE type_personne='$type_personne' AND emetteur='$destinataire'  AND $idrep ORDER BY date DESC, heure DESC LIMIT $offset,$limit";
	

        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function affichage_messagerie_limit($type_personne,$destinataire,$offset,$limit,$idrep,$order="") {
	global $cnx;
	global $prefixe;

	if ($idrep == "") {
		$idrep="( repertoire IS NULL OR repertoire = '0') ";
	}else{
		$idrep="repertoire='$idrep'";
	}

	if ($order == "") $order="date DESC";
	if ($order == "date") $order="date DESC";
	if ($order == "date2") $order="date";
	if ($order == "objet") $order="objet , date DESC";
	if ($order == "objet2") $order="objet DESC, date DESC";
	if ($order == "de") $order="emetteur , date DESC";
	if ($order == "de2") $order="emetteur DESC, date DESC";


	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, lu_par_utilisateur, idpiecejointe,impression,alerte FROM ${prefixe}messageries WHERE brouillon='0' AND  type_personne_dest='$type_personne' AND destinataire='$destinataire' AND $idrep ORDER BY $order , heure DESC LIMIT $offset,$limit";
	

        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affichage_messagerie_brouillon_limit($type_personne,$destinataire,$offset,$limit) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, lu_par_utilisateur, idpiecejointe FROM ${prefixe}messageries WHERE brouillon='1'  ORDER BY date DESC, heure DESC LIMIT $offset,$limit";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affichage_messagerie($type_personne,$destinataire,$idrep) {
	global $cnx;
	global $prefixe;

	if ($idrep == "") {
			$idrep="(repertoire IS NULL OR repertoire = '0')";
	}else{
			$idrep="repertoire='$idrep'";
	}


	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, lu_par_utilisateur FROM ${prefixe}messageries WHERE  brouillon='0' AND type_personne_dest='$type_personne' AND destinataire='$destinataire' AND $idrep ORDER BY date DESC, heure DESC LIMIT 30";
    $res=execSql($sql);
    $data=chargeMat($res);
    return $data;
}

function affichage_messagerie_envoyer_pour_suppression_limit($destinataire,$offset,$limit) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,lu_par_utilisateur  FROM ${prefixe}messagerie_envoyer WHERE emetteur='$destinataire'   ORDER BY date DESC, heure DESC LIMIT 30";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affichage_messagerie_pour_suppression_limit($destinataire,$offset,$limit) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,lu_par_utilisateur  FROM ${prefixe}messageries WHERE emetteur='$destinataire'   ORDER BY date DESC, heure DESC LIMIT 30";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function affichage_messagerie_pour_suppression($destinataire) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest FROM ${prefixe}messageries WHERE emetteur='$destinataire'   ORDER BY date DESC, heure DESC LIMIT 30 ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;

}
function affichage_messagerie_envoyer_message($id_message) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,idforward_mail,idpiecejointe  FROM ${prefixe}messagerie_envoyer  WHERE id_message='$id_message'";
    	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}
function affichage_messagerie_message($id_message) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,idforward_mail,idpiecejointe,idgroupe  FROM ${prefixe}messageries WHERE id_message='$id_message'";
    $res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function affichage_messagerie_message_via_mail($idforward) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,idforward_mail,repertoire,idmess_envoyer,idpiecejointe,idgroupe   FROM ${prefixe}messageries WHERE idforward_mail='$idforward'";
    	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function imprMessage($id) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}messageries set impression='1' WHERE id_message='$id'";
	execSql($sql);
}

function suppression_message_envoyer($id,$idpers,$qui) {
        global $cnx;
        global $prefixe;
        $sql="DELETE FROM ${prefixe}messagerie_envoyer WHERE id_message='$id' AND emetteur='$idpers'";
        suppPiecejointe3($id);
        return(execSql($sql));
}


function suppPiecejointe3($id) {
        global $cnx;
        global $prefixe;
        $sql="SELECT idpiecejointe FROM ${prefixe}messagerie_envoyer WHERE id_message='$id'";
        $res=execSql($sql);
        $data=chargeMat($res);
        $idpiecejointe=$data[0][0];
        if ($idpiecejointe == "") return;

        $sql="SELECT idpiecejointe FROM ${prefixe}messageries WHERE idpiecejointe='$idpiecejointe'";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 0) return ;

        $sql="SELECT idpiecejointe FROM ${prefixe}messagerie_envoyer WHERE id_message='$id'";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 1) return ;

        if (count($data) == 1) {
                $sql="SELECT md5 FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe'";
                $res=execSql($sql);
                $data=chargeMat($res);
                for($i=0;$i<count($data);$i++) {
                        $filemd5=$data[$i][0];
                        @unlink("./data/fichiersj/$filemd5");
                }
                $sql="DELETE FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe'";
                execSql($sql);
        } 
}

function suppression_message($id,$idpers,$qui) {
	global $cnx;
	global $prefixe;
	if ($qui == "prof") {
		$sql="DELETE FROM ${prefixe}messageries WHERE id_message='$id' AND emetteur='$idpers'";
	}else{
    		$sql="DELETE FROM ${prefixe}messageries WHERE id_message='$id' AND destinataire='$idpers'";
	}
	suppPiecejointe2($id);
	return(execSql($sql));
}


function suppPiecejointe2($id) {
        global $cnx;
        global $prefixe;
        $sql="SELECT idpiecejointe FROM ${prefixe}messageries WHERE id_message='$id'";
        $res=execSql($sql);
        $data=chargeMat($res);
        $idpiecejointe=$data[0][0];
        if ($idpiecejointe == "") return;

        $sql="SELECT idpiecejointe FROM ${prefixe}messagerie_envoyer WHERE idpiecejointe='$idpiecejointe'";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 0) return ;

        $sql="SELECT idpiecejointe FROM ${prefixe}messageries WHERE id_message='$id'";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 1) return ;

        if (count($data) == 1) {
                $sql="SELECT md5 FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe'";
                $res=execSql($sql);
                $data=chargeMat($res);
                for($i=0;$i<count($data);$i++) {
                        $filemd5=$data[$i][0];
                        @unlink("./data/fichiersj/$filemd5");
                }
                $sql="DELETE FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe'";
                execSql($sql);
        }
}



function suppression_message_brouillon($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}messageries WHERE id_message='$id' AND brouillon='1'";
	return(execSql($sql));
}

function lecture_message($id) {
	global $cnx;
	global $prefixe;
    	if(DBTYPE=='pgsql')
	{
		$sql="UPDATE ${prefixe}messageries SET lu='true' WHERE id_message='$id'";
	}
	elseif(DBTYPE=='mysql')
	{
		$sql="UPDATE ${prefixe}messageries SET lu=1 WHERE id_message='$id'";
	}
	$cr=execSql($sql);
	if ($cr) {
		valide_message_lu_envoyer($id);
		return true;
	}
}

function valide_message_lu($id) {
	global $cnx;
	global $prefixe;
	if(DBTYPE=='pgsql')
	{
        	$sql="UPDATE ${prefixe}messageries SET lu_par_utilisateur='true' WHERE id_message='$id'";
 
	}
	elseif(DBTYPE=='mysql')
	{
        	$sql="UPDATE ${prefixe}messageries SET lu_par_utilisateur=1 WHERE id_message='$id'";
	}
	$cr=execSql($sql);
	if ($cr) {
		valide_message_lu_envoyer($id);
		return true;
	}
}

function valide_message_lu_envoyer($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_message,idmess_envoyer FROM ${prefixe}messageries  WHERE id_message='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$id2=$data[0][1];
		if(DBTYPE=='pgsql') {
	        	$sql="UPDATE ${prefixe}messagerie_envoyer SET lu_par_utilisateur='true' WHERE idmess_envoyer ='$id2'";
	       		execSql($sql);	
 
		} elseif(DBTYPE=='mysql') {
	        	$sql="UPDATE ${prefixe}messagerie_envoyer SET lu_par_utilisateur=1 WHERE idmess_envoyer ='$id2'";
	       		execSql($sql);	
		}
	}
}

function valide_message_lu_number($number,$idpers) {
	global $cnx;
	global $prefixe;
	if(DBTYPE=='pgsql') {
                $sql="UPDATE ${prefixe}messageries SET lu_par_utilisateur=true WHERE idforward_mail='$number'  AND destinataire='$idpers'  ";
        }
        elseif(DBTYPE=='mysql') {
                $sql="UPDATE ${prefixe}messageries SET lu_par_utilisateur=1 WHERE idforward_mail='$number'  AND destinataire='$idpers'  ";
        }
	$cr=execSql($sql);
	if ($cr) {
		$sql="SELECT id_message,idmess_envoyer FROM ${prefixe}messageries  WHERE  idforward_mail='$number' AND destinataire='$idpers'  ";
		$res=execSql($sql);
		$data=chargeMat($res);
		if (count($data) > 0) {
			$id2=$data[0][1];
			if(DBTYPE=='pgsql') {
	        		$sql="UPDATE ${prefixe}messagerie_envoyer SET lu_par_utilisateur='true' WHERE idmess_envoyer ='$id2'";
	       			execSql($sql);	
 
			} elseif(DBTYPE=='mysql') {
		        	$sql="UPDATE ${prefixe}messagerie_envoyer SET lu_par_utilisateur=1 WHERE idmess_envoyer ='$id2'";
	       			execSql($sql);	
			}
		}
		return true;
	}
}

//-----------------------------------------------------------------------
//----------------------------------------------------------------------
function create_circulaire($titre,$ref,$fichier,$date,$prof,$Classe,$idpers,$pers,$mvs,$dir,$tut,$categorie) {
	global $cnx;
	global $prefixe;
	if ($Classe == "NULL") { $Classe="NULL"; }else { $Classe="'".$Classe."'"; }
	if ($pers != 1) { $pers=0; }
	if ($mvs != 1) { $mvs=0; }
	if ($dir != 1) { $dir=0; }
	if ($tut != 1) { $tut=0; }
	if ($prof != 1) { $prof=0; }
    	$sql="INSERT INTO ${prefixe}circulaire (sujet,refence,file,date,enseignant,classe,idprofp,comptepersonnel,compteviescolaire,comptedirection,comptetuteurdestage,categorie) VALUES ('$titre','$ref','$fichier','$date','$prof',$Classe,'$idpers','$pers','$mvs','$dir','$tut','$categorie')";
	return(execSql($sql));
}

function modif_circulaire($titre,$ref,$date,$prof,$Classe,$idprofp,$pers,$mvs,$dir,$tut,$id_circulaire,$categorie) {
	global $cnx;
	global $prefixe;
	if ($Classe == "NULL") { $Classe="NULL"; }else { $Classe="'".$Classe."'"; }
	if ($pers != 1) { $pers=0; }
	if ($mvs != 1) { $mvs=0; }
	if ($dir != 1) { $dir=0; }
	if ($tut != 1) { $tut=0; }
	if ($prof != 1) { $prof=0; }
	$sql="UPDATE ${prefixe}circulaire SET sujet='$titre', refence='$ref',date='$date',enseignant='$prof',classe=$Classe,idprofp='$idprofp',comptepersonnel='$pers',compteviescolaire='$mvs',comptedirection='$dir',comptetuteurdestage='$tut', categorie='$categorie' WHERE id_circulaire='$id_circulaire'";
 	return(execSql($sql));
}

function listeCatCirculaire() {
	global $cnx;
	global $prefixe;
	$sql="SELECT categorie FROM ${prefixe}circulaire $sqlsuite GROUP BY categorie ORDER BY categorie ";
	$res=execSql($sql);
        $data=chargeMat($res);
	return($data);
}



function create_reglement($titre,$ref,$fichier,$date,$prof,$Classe) {
	global $cnx;
	global $prefixe;
	if ($prof == 1) { 
		$sql="DELETE FROM ${prefixe}statUtilisateur WHERE type_membre='menuprof' ";
		execSql($sql);
	}
	if ($Classe == "NULL") { 
		$Classe="NULL"; 
	}else { 
		$Classe="'".$Classe."'";  
		$sql="DELETE FROM ${prefixe}statUtilisateur WHERE type_membre='menueleve'";
		execSql($sql);
		$sql="DELETE FROM ${prefixe}statUtilisateur WHERE type_membre='menuparent'";
		execSql($sql);
	}
    	$sql="INSERT INTO ${prefixe}reglement (sujet,refence,file,date,enseignant,classe) VALUES ('$titre','$ref','$fichier','$date','$prof',$Classe)";
	return(execSql($sql));
}

function circulaireAffProf($okprof,$tri,$filtre) {
	global $cnx;
	global $prefixe;
	if ($tri == "date") $tri="date DESC";
	if ($filtre != "") $sqlsuite=" AND categorie='$filtre'";
	$sql="SELECT id_circulaire, sujet, refence, file, date, enseignant, classe,categorie  FROM	${prefixe}circulaire WHERE enseignant='1'  $sqlsuite ORDER BY $tri ";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function circulaireAffPersonnel($tri,$filtre) {
	global $cnx;
	global $prefixe;
	if ($tri == "date") $tri="date DESC";
	if ($filtre != "") $sqlsuite=" AND categorie='$filtre'";
	$sql="SELECT id_circulaire, sujet, refence, file, date, enseignant, classe, categorie  FROM	${prefixe}circulaire WHERE comptepersonnel='1' $sqlsuite ORDER BY $tri ";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function circulaireAffVieScolaire($tri,$filtre) {
	global $cnx;
	global $prefixe;
	if ($tri == "date") $tri="date DESC";
	if ($filtre != "") $sqlsuite=" AND categorie='$filtre'";
	$sql="SELECT id_circulaire, sujet, refence, file, date, enseignant, classe, categorie  FROM	${prefixe}circulaire WHERE compteviescolaire='1' $sqlsuite ORDER BY $tri ";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function circulaireAffTuteurdeStage($tri,$filtre) {
         global $cnx;
         global $prefixe;
         $tri="date DESC";
         if ($tri == "date") $tri="date DESC";
         if ($filtre != "") $sqlsuite=" AND categorie='$filtre'";
         $sql="SELECT id_circulaire, sujet, refence, file, date, enseignant, classe,categorie  FROM ${prefixe}circulaire WHERE comptetuteurdestage='1' $sqlsuite ORDER BY $tri ";
         $res=execSql($sql);
         $data=chargeMat($res);
         return $data;
}





function circulaireAffProfP($idprofp,$tri) {
	global $cnx;
	global $prefixe;
	if ($tri == "date") $tri="date DESC";
	if (trim($tri) == "") $tri="date DESC";
	$sql="SELECT id_circulaire, sujet, refence, file, date, enseignant, classe FROM	${prefixe}circulaire WHERE idprofp='$idprofp'  ORDER BY $tri ";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function reglementAffProf($okprof) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id, sujet, refence, file, date, enseignant, classe FROM	${prefixe}reglement WHERE enseignant='1' ORDER BY date DESC";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function circulaireAffAdmin($tri,$filtre) {
         global $cnx;
         global $prefixe;
         if ($tri == "date") $tri="date DESC";
         if (($tri != "date") && ($tri != "refence")&&($tri!="sujet")) $tri="";
         if (trim($tri) != "") $ORDERBY=" ORDER BY ";
         if ($filtre != "") $sqlsuite=" WHERE categorie='$filtre' ";
         if ($ORDERBY == "") { $ORDERBY=" ORDER BY date DESC"; }
         $sql="SELECT id_circulaire,sujet,refence,file,date,enseignant,classe,categorie FROM  ${prefixe}circulaire $sqlsuite $ORDERBY $tri ";
         $res=execSql($sql);
         $data=chargeMat($res);
         return $data;
 }




function circulaireAffAdmin2() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_circulaire,sujet,refence,file,date,enseignant,classe FROM ${prefixe}circulaire  ORDER BY date DESC";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function reglementAffAdmin() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,sujet,refence,file,date,enseignant,classe FROM ${prefixe}reglement ORDER BY date DESC";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}



function chercheReglement($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,sujet,refence,file,date,enseignant,classe FROM ${prefixe}reglement WHERE id='$id'" ;
	$res=execSql($sql);
    $data=chargeMat($res);
    return $data;	
}

function chercheCirculaire($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_circulaire,sujet,refence,file,date,enseignant,classe,idprofp,comptepersonnel,compteviescolaire,comptedirection,comptetuteurdestage,categorie FROM ${prefixe}circulaire WHERE id_circulaire='$id'";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function purge_circulaire() {
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}circulaire";
	DirPurge("./data/circulaire");
	return(execSql($sql));
}

function circulaireSup($id) {
	global $cnx;
	global $prefixe;
    $sql="DELETE FROM ${prefixe}circulaire WHERE id_circulaire='$id'";
	return(execSql($sql));
}

function reglementSup($id) {
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}reglement WHERE id='$id'";
	return(execSql($sql));
}

function circulaireAffParent($idClasse,$tri,$filtre) {
	global $cnx;
	global $prefixe;
	if ($filtre != "") $sqlsuite="WHERE categorie='$filtre'";
	$sql="SELECT  id_circulaire, sujet, refence, file, date, enseignant, classe, categorie  FROM ${prefixe}circulaire  $sqlsuite ORDER BY $tri DESC";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function reglementAffParent(){
	global $cnx;
	global $prefixe;
	$sql="SELECT  id, sujet, refence, file, date, enseignant, classe FROM ${prefixe}reglement ORDER BY date DESC";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}



function affSelecMotif() {
	global $cnx;
	global $prefixe;
	$sql="SELECT  id, libelle FROM ${prefixe}config_rtd_abs ORDER BY libelle";
	$res=execSql($sql);
	$data=chargeMat($res);
	$nb=15;
	if ($_SESSION['widthfen'] >= 1020) $nb=30;
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][1] != "") print "<option value=\"".$data[$i][1]."\" id='select1'  title=\"".$data[$i][1]."\" >".trunchaine($data[$i][1],$nb)."</option>";
	}
}
//----------------------------------------------------------------------
//----------------------------------------------------------------------
// select pour la modif
function selectHtml2($name,$size,$multiple,$options,$defaultName,$defaultId,$validsupp,$lenght,$disabledENS='') {
if($multiple)
        $mulitple='mulitple="multiple"';
else
        $multiple='';
$selectHead="<select name='$name' size='$size' $multiple  >";
$defaultNameTitle=$defaultName;
if ($lenght > 0) { $defaultName=trunchaine($defaultName,$lenght); }
$selectHead.="<option value=\"$defaultId\" title=\"$defaultNameTitle\" STYLE=\"color:#000066;background-color:#FCE4BA\">$defaultName</option>";
if ($validsupp == "1") {
        $selectHead.="<option value='-1'  STYLE=\"color:red;background-color:white\" $disabledENS  >Supprimer</option>";
}
if ($validsupp == "2") {
        $selectHead.="<option value='0' $disabledENS  >Choix</option>";
}
$selectFoot= '</select>';
$selectOptions='';
for($i=0;$i<count($options);$i++) {
$val=$options[$i][0];
$lib=$options[$i][1];
$lib=preg_replace('/0/', " ", $lib); // j'ai rajouter mais si la matiere contient un zero il sera remplace par un blanc
$libTitle=$lib;
if ($lenght > 0) { $lib=trunchaine($lib,$lenght); }
$selectOptions .= <<<HTML
<option value="$val" id='select1' title="$libTitle" $disabledENS >$lib</option>
HTML;
$selectOptions .= "\n";
}
$selectHtml = $selectHead;
$selectHtml .= $selectOptions;
$selectHtml .= $selectFoot ;
return $selectHtml ;
}

// ------------------------------------------------------------//
// $options -> matrice de listes à deux éléments (0:value du option ,1:libelle du option)
function selectHtml($name,$size,$multiple,$options)
{
if($multiple)
	$mulitple='mulitple="multiple"';
else
	$multiple='';
$selectHead="<select name=\"$name\" size=\"$size\" $multiple><option value=\"0\" STYLE=\"color:#000066;background-color:#FCE4BA\">".LANGCHOIX."</option>";
$selectFoot= '</select>';
$selectOptions='';
for($i=0;$i<count($options);$i++) {
$val=$options[$i][0];
$lib=$options[$i][1];
$lib=preg_replace('/0/', " ", $lib); // j'ai rajouter mais si la matiere contient un zero il sera remplace par un blanc
$libtitle=$lib;
$lib=trunchaine($lib,40);
$selectOptions .= <<<HTML
<option value="$val" id='select1' title="$libtitle"  >$lib</option>
HTML;
$selectOptions .= "\n";
}
$selectHtml = $selectHead;
$selectHtml .= $selectOptions;
$selectHtml .= $selectFoot ;
return $selectHtml ;
}

function matGroup($nomClasse){
	global $cnx;
	global $prefixe;
$sql=<<<SQL
SELECT
	group_id,
	libelle,
	liste_elev
FROM
	${prefixe}groupes
ORDER BY
	libelle
SQL;
$res=execSql($sql);
$groupes=chargeMat($res);
$cpt2=0;
$explod=array();
for($cpt=0;$cpt<count($groupes);$cpt++){
	$liste_eleves = substr($groupes[$cpt][2],1);
	$liste_eleves = substr($liste_eleves,0,strlen($liste_eleves)-1);
	trim($liste_eleves);
	if( empty ($liste_eleves)  )
	{
		continue;
	}
	$liste_eleves=preg_replace('/,,/',',',$liste_eleves);
	$liste_eleves=preg_replace('/\{,/',"{",$liste_eleves);
	$liste_eleves=preg_replace('/,\}/',"}",$liste_eleves);
	$sql = "SELECT libelle FROM ${prefixe}classes, ${prefixe}eleves WHERE elev_id IN ($liste_eleves) AND classe = code_class";
	$res = execSql($sql);
	$data = chargeMat($res);

	for($cpt3=0;$cpt3<count($data);$cpt3++) {
		array_push($explod,$data[$cpt3][0]);
	}
	$explod=array_unique($explod);

	$sl = $groupes[$cpt][1];

	foreach($explod as $tmp){
		if( strtolower(trim($tmp)) == strtolower(trim($nomClasse)) ) {
			$mat[$cpt2][0]=$groupes[$cpt][0];
			$mat[$cpt2][1]=$sl;
			$cpt2++;
			}
	}
	unset($explod);
	$explod=array();
}
return $mat;
}

function matGroup2($nomClasse){
        global $cnx;
        global $prefixe;
$sql=<<<SQL
SELECT
        group_id,
        libelle,
        liste_elev,
        annee_scolaire
FROM
        ${prefixe}groupes
ORDER BY
        annee_scolaire,libelle
SQL;
$res=execSql($sql);
$groupes=chargeMat($res);
$cpt2=0;
$explod=array();
for($cpt=0;$cpt<count($groupes);$cpt++){
        $anneeScolaire=$groupes[$cpt][3];
        $liste_eleves = substr($groupes[$cpt][2],1);
        $liste_eleves = substr($liste_eleves,0,strlen($liste_eleves)-1);
        trim($liste_eleves);
        if( empty ($liste_eleves)  )
        {
                continue;
        }
        $liste_eleves=preg_replace('/,,/',',',$liste_eleves);
        $liste_eleves=preg_replace('/\{,/',"{",$liste_eleves);
        $liste_eleves=preg_replace('/,\}/',"}",$liste_eleves);
        $sql = "SELECT libelle FROM ${prefixe}classes, ${prefixe}eleves WHERE elev_id IN ($liste_eleves) AND classe = code_class";
        $res = execSql($sql);
        $data = chargeMat($res);


        for($cpt3=0;$cpt3<count($data);$cpt3++)
        {
                array_push($explod,$data[$cpt3][0]);
        }
        $explod=array_unique($explod);

        $sl = $groupes[$cpt][1];

        foreach($explod as $tmp){
                if( strtolower(trim($tmp)) == strtolower(trim($nomClasse)) ) {
                        $mat[$cpt2][0]=$groupes[$cpt][0];
                        $mat[$cpt2][1]=$sl." => ".$anneeScolaire;
                        $cpt2++;
                        }
        }
        unset($explod);
        $explod=array();
}
return $mat;
}

function recupInfoGroupe($idgroupe) {
        global $cnx;
        global $prefixe;
        $sql="SELECT group_id,libelle,annee_scolaire FROM ${prefixe}groupes WHERE group_id='$idgroupe' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function modifAnneeScolaireGroupe($idgroupe,$anneeScolaire){
        global $cnx;
        global $prefixe;
        $sql="UPDATE ${prefixe}groupes SET annee_scolaire='$anneeScolaire' WHERE group_id='$idgroupe' ";
        execSql($sql);
}


function htmlTrMatAffec($mat){
        global $cnx;
        global $prefixe;
        for($i=0;$i<count($mat);$i++){
                print("<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">\n");
                for($j=0;$j<count($mat[$i]);$j++){
                        $matiere=$mat[$i][$j];
                        if ($j == 5) {
                                $matiere="non";
                                if ($mat[$i][5] == "1") { $matiere="oui"; }
                        }elseif ($j == 6) {
                                        $matiere="non";
                                        if ($mat[$i][6] == "1") { $matiere="oui"; }
                        }elseif ($j == 12) {
				// rien
                        }else{
                                if (!preg_match('/[0-9]0$/',trim($matiere))) {
                                        $matiere=preg_replace('/^0$/',"",trim($matiere));  // supprime le ZERO des sous matieres
                                        $matiere=preg_replace('/0$/',"",trim($matiere));  // supprime le ZERO des sous matieres
                                }

                        }
                        if ($j == 9) {
                                $sql="SELECT c.nom_ue  FROM ${prefixe}ue_detail u, ${prefixe}ue c WHERE u.code_ue=c.code_ue AND code_ue_detail='$matiere' ";
                                $res=execSql($sql);
                                $data=chargeMat($res);
                                $matiere=$data[0][0];
                        }
                        print "\t<td>&nbsp;".$matiere."</td>\n" ;
                }
                print("</tr>\n");
        }
}

function htmlTrMat($mat){
                for($i=0;$i<count($mat);$i++){
                        print("<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\">\n");
                        for($j=0;$j<count($mat[$i]);$j++){
                                $matiere=trim($mat[$i][$j]);
                                if ($j == 5) {
                                        $matiere="non";
                                        if ($mat[$i][5] == "1") { $matiere="oui"; }
                                }elseif ($j == 6) {
                                        $matiere="non";
                                        if ($mat[$i][6] == "1") { $matiere="oui"; }
                                }elseif ($j == 10) {
					// rien
                                }else{
                                        if (!preg_match('/[0-9]0$/',trim($matiere))) {
                                                $matiere=preg_replace('/^0$/',"",trim($matiere));  // supprime le ZERO des sous matieres
                                                $matiere=preg_replace('/0$/',"",trim($matiere));  // supprime le ZERO des sous matieres
                                        }
                                }
                                print "\t<td valign=top ><font class=T2>&nbsp;".$matiere."</font></td>\n" ;
                        }
                        print("</tr>\n");
                }
// fin fonction htmlTrMat
}

function htmlTrMatCopy($mat){
                for($i=0;$i<count($mat);$i++){
                        print("<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\">\n");
                        for($j=0;$j<count($mat[$i]);$j++){
                                $matiere=trim($mat[$i][$j]);
                                if ($j == 6) {
                                        $matiere="non";
                                        if ($mat[$i][6] == "1") { $matiere="oui"; }
                                }elseif ($j == 6) {
                                        $matiere="non";
                                        if ($mat[$i][6] == "1") { $matiere="oui"; }
                                }else{
                                        if (!preg_match('/[0-9]0$/',trim($matiere))) {
                                                $matiere=preg_replace('/^0$/',"",trim($matiere));  // supprime le ZERO des sous matieres
                                                $matiere=preg_replace('/0$/',"",trim($matiere));  // supprime le ZERO des sous matieres
                                        }
                                }
                                if ($j == 4) $matiere='<font color="green"><i>non&nbsp;récupéré</i></font>';
                                if ($j == 5) $matiere='<font color="green"><i>non&nbsp;récupéré</i></font>';
                                print "\t<td valign=top ><font class=T2>&nbsp;".$matiere."</font></td>\n" ;
                        }
                        print("</tr>\n");
                }
// fin fonction htmlTrMat
}

function hashSessionVar($ident){
global $_SESSION;
for($i=0;$i<count($ident);$i++) {
	$Scle=$ident[$i];
	$i++;
	$cle=$ident[$i];
	$hash[$cle]=$_SESSION[$Scle];
}
return $hash;
}

function htmlTableMat($mat,$fic) {
// fonction de dev-débogue principalement
// affichant une matrice dans un tableau html
echo "<table border=\"1\" width=\"100%\" bgcolor=\"#CCCCCC\" style=\"border-collapse: collapse;\" >\n";
if ($fic == "modifnote2") {
echo "<tr>";
echo "<td bgcolor='yellow' align='center'>".LANGPROF16."</td>";
echo "<td bgcolor='yellow' align='center' width='20%'>".LANGPROF17."</td>";
echo "<td bgcolor='yellow' align='center'  width='5%'>".LANGPER19.".</td>";
echo "<td bgcolor='yellow'  align='center' width='5%'>".LANGPER30."</td>";
echo "</tr>";
}
if ($fic == "suppnote2") {
echo "<tr>";
echo "<td bgcolor='yellow' align='center'>".LANGPROF16."</td>";
echo "<td bgcolor='yellow' align='center' width='20%'>".LANGPROF17."</td>";
echo "<td bgcolor='yellow' align='center'  width='5%'>".LANGPER19.".</td>";
echo "<td bgcolor='yellow'  align='center' width='5%'>".LANGBT50."</td>";
echo "</tr>";
}
if ($fic == "notevisu2") {
echo "<tr>";
echo "<td bgcolor='yellow' align='center'>".LANGPROF16."</td>";
echo "<td bgcolor='yellow' align='center' width='20%'>".LANGPROF17."</td>";
echo "<td bgcolor='yellow' align='center'  width='5%'>".LANGPER19.".</td>";
echo "<td bgcolor='yellow'  align='center' width='5%'>".LANGPER27."</td>";
echo "</tr>";
}
htmlTrMat($mat);
echo "</table>\n";
}

function htmlFormText($name,$value,$size,$maxlength){
$string = <<<HTML
<input type="text" name="$name" value="$value" size="$size" maxlength="$maxlength" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" autocomplete="off" />
HTML;
return $string."\n";
}

function htmlFormTextVatel($name,$value,$size,$maxlength,$option){
$string = <<<HTML
<input type="text" name="$name" value="$value" size="$size" maxlength="$maxlength" class="form-control" autocomplete="off"  $option />
HTML;
return $string."\n";
}


function htmlFormTextDateNoteAjout($name,$value,$size,$maxlength,$i,$idclasse,$idgroupe){
	$string="<input type='text' name='$name' value='$value' size='$size' maxlength='$maxlength' STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\" readonly='readonly' autocomplete='off' $affTrim onBlur='AfficheTrimestre();' />";
	return $string."\n";
}

function htmlFormTextDateNoteAjoutVatel($name,$value,$size,$maxlength,$i,$idclasse,$idgroupe){
	$string="<input type='text' name='$name' value='$value' size='$size' maxlength='$maxlength' class='form-control' readonly='readonly' autocomplete='off' $affTrim onBlur='AfficheTrimestre();' />";
	return $string."\n";
}

function htmlFormText2($name,$value,$size,$maxlength){
$string = <<<HTML
<input type="text" name="$name" value="$value" size="$size" maxlength="$maxlength" autocomplete="off"  />
HTML;
return $string."\n";
}

function htmlFormTextVatel2($name,$value,$size,$maxlength){
	
	$string = <<<HTML
<input type="text" name="$name" value="$value" size="$size" maxlength="$maxlength" autocomplete="off"  class="form-control" />
HTML;
	return $string."\n";
}

function htmlFormTextNoteAjout($name,$value,$size,$maxlength,$i,$idclasse,$idgroupe,$idmatiere,$verif){
if ($verif == "oui") {
$string = <<<HTML
<input type="text" name="$name" value="$value" size="$size" maxlength="$maxlength"  onChange="verifSujet(this.value,document.form11.iDate$i.value,$idclasse,$idgroupe,$idmatiere);" autocomplete="off"  />
HTML;
}else{
$string = <<<HTML
<input type="text" name="$name" value="$value" size="$size" maxlength="$maxlength" autocomplete="off" />
HTML;
}
return $string."\n";
}

function htmlFormTextNoteAjoutVatel($name,$value,$size,$maxlength,$i,$idclasse,$idgroupe,$idmatiere,$verif){
if ($verif == "oui") {
$string = <<<HTML
<input type="text" name="$name" value="$value" size="$size" maxlength="$maxlength" class="form-control"   onChange="verifSujet(this.value,document.form11.iDate$i.value,$idclasse,$idgroupe,$idmatiere);" autocomplete="off"  />
HTML;
}else{
$string = <<<HTML
<input type="text" name="$name" value="$value" size="$size" maxlength="$maxlength" class="form-control"  autocomplete="off" />
HTML;
}
return $string."\n";
}


function htmlFormTextarea($name,$value,$rows,$cols){
$string = <<<HTML
<textarea name="$name" rows="$rows" cols="$cols">
	$value
</textarea>
HTML;
return $string."\n";
}

function htmlFormHidden($name,$value){
$string = <<<HTML
<input type="hidden" name="$name" value="$value" />
HTML;
return $string."\n";
}


// -----------------------------------
// functions php pour les statistiques
// -----------------------------------

function statNavigateur($navigateur,$version,$os,$langue) {
	global $cnx;
	global $prefixe;
	$ok="rien";
	$sql="SELECT *  FROM  ${prefixe}statnavigateur WHERE navigateur='$navigateur' AND version='$version' AND os='$os' AND langue='$langue' ";
        $res=execSql($sql);
	$data=chargeMat($res);
	$ok=$data[0][0];
	if ($ok == "") {
		$nb=1;
        	$sql="INSERT INTO ${prefixe}statnavigateur (navigateur,version,nb_fois,os,langue) VALUES ('$navigateur','$version',$nb,'$os','$langue')";

	}else{
        	$sql="UPDATE ${prefixe}statnavigateur SET nb_fois=nb_fois+1  WHERE navigateur='$navigateur' AND version='$version' AND os='$os' AND langue='$langue' ";
	}
	return(execSql($sql));
}

function statScreen($taille) {
	global $cnx;
	global $prefixe;
	if ($taille != ""){
		$ok="rien";
		$sql="SELECT taille,nb_fois  FROM  ${prefixe}statscreen  WHERE  taille='$taille'";
		$res=execSql($sql);
		$data=chargeMat($res);
		$ok=$data[0][0];
		if ($ok == "") {
			$nb=1;
       		 	$sql="INSERT INTO ${prefixe}statscreen (taille,nb_fois) VALUES ('$taille',$nb)";
		}else{
        		$sql="UPDATE ${prefixe}statscreen SET nb_fois=nb_fois+1  WHERE taille='$taille' ";
		}
		return(execSql($sql));
	}
}

// verification du compte pour l'inscription
function verif_compte($nom,$prenom,$idpers,$membre) {
	global $cnx;
	global $prefixe;
	$nom=trim(strtolower($nom));
	$prenom=trim(strtolower($prenom));
	$sql="SELECT *  FROM  ${prefixe}statUtilisateur  WHERE  nom='$nom' AND prenom='$prenom' AND idpers='$idpers' AND type_membre='$membre' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$ok=$data[0][0];
	if (trim($ok) == "") {
		return true;
	}else{
		return 0;
	}
}

function delete_inscription($nom,$prenom,$type) {
	global $cnx;
	global $prefixe;
	$nom=trim(strtolower($nom));
	$prenom=trim(strtolower($prenom));
	$sql="DELETE FROM  ${prefixe}statUtilisateur  WHERE  nom='$nom' AND prenom='$prenom' AND type_membre='$type' ";
	return(execSql($sql));
}

function delete_inscription_total() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM  ${prefixe}statUtilisateur";
	return(execSql($sql));
}


function statUtilisateur($nom,$prenom,$idpers,$membre) {
	global $cnx;
	global $prefixe;
	$nom=trim(strtolower(trunchaine($nom,30)));
	$prenom=trim(strtolower(trunchaine($prenom,30)));
	$sql="SELECT *  FROM  ${prefixe}statUtilisateur  WHERE  nom='$nom' AND prenom='$prenom' AND idpers='$idpers' AND type_membre='$membre'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$ok=$data[0][0];
	if ($ok == "") {
		$date=dateDMY2();
      		$sql="INSERT INTO ${prefixe}statUtilisateur (date_entree,nom,prenom,idpers,type_membre) VALUES ('$date','$nom','$prenom','$idpers','$membre')";
		return(execSql($sql));
	}
}


function statConecParHeure($heure) {
	global $cnx;
	global $prefixe;
	$ok="rien";
	$sql="SELECT *  FROM  ${prefixe}statconxparheure WHERE heure='$heure'";
        $res=execSql($sql);
	$data=chargeMat($res);
	$ok=$data[0][0];
	if ($ok == "") {
		$nb=1;
        	$sql="INSERT INTO ${prefixe}statconxparheure (heure,nb_fois) VALUES ('$heure',$nb)";
	}else{
        	$sql="UPDATE ${prefixe}statconxparheure SET nb_fois=nb_fois+1  WHERE heure='$heure' ";
	}
	return(execSql($sql));
}


function statDebit($debit) {
	global $cnx;
	global $prefixe;
	if ($debit != "") {
		$ok="rien";
		$sql="SELECT *  FROM  ${prefixe}statdebit WHERE debit='$debit'";
 	        $res=execSql($sql);
		$data=chargeMat($res);
		$ok=$data[0][0];
		if ($ok == "") {
			$nb=1;
        		$sql="INSERT INTO ${prefixe}statdebit (debit,nb_fois) VALUES ('$debit',$nb)";
		}else{
       		 	$sql="UPDATE ${prefixe}statdebit SET nb_fois=nb_fois+1  WHERE debit='$debit' ";
		}
		return(execSql($sql));
	}
}

function affStatNavigateur() {
	global $cnx;
	global $prefixe;
$sql="SELECT navigateur, version, nb_fois, os, langue FROM ${prefixe}statnavigateur  ORDER BY nb_fois DESC";
$res=execSql($sql);
$data=chargeMat($res);
return $data;
}

function affStatConxParHeure() {
	global $cnx;
	global $prefixe;
	$sql="SELECT heure, nb_fois FROM ${prefixe}statconxparheure  ORDER BY heure  DESC";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function affStatDebit() {
	global $cnx;
	global $prefixe;
$sql="SELECT debit, nb_fois FROM ${prefixe}statdebit  ORDER BY nb_fois  DESC";
$res=execSql($sql);
$data=chargeMat($res);
return $data;
}

function affStatEcran() {
	global $cnx;
	global $prefixe;
$sql="SELECT taille, nb_fois FROM ${prefixe}statscreen  ORDER BY nb_fois  DESC";
$res=execSql($sql);
$data=chargeMat($res);
return $data;
}

function affStatUtilisateur2($limit) {
	global $cnx;
	global $prefixe;
$sql="SELECT nom, prenom, date_entree, type_membre ,nb_conx, der_conx FROM ${prefixe}statUtilisateur  ORDER BY date_entree  DESC LIMIT $limit ";
$res=execSql($sql);
$data=chargeMat($res);
return $data;
}


function affStatUtilisateur() {
	global $cnx;
	global $prefixe;
$sql="SELECT nom, prenom, date_entree FROM ${prefixe}statUtilisateur  ORDER BY date_entree  DESC";
$res=execSql($sql);
$data=chargeMat($res);
return $data;
}
// ---------- fin fonction stat //

// mise à jour du mot de passe
function update_passwd($pass,$membre,$idpers) {
	global $cnx;
	global $prefixe;

	if (empty($pass)) {
		return 0;
	}

	
	include_once("./common/config2.inc.php");

	if (SECURITE == 3) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[A-Z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}

	if (SECURITE == 2) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}

	if (SECURITE == 1) {
		if (strlen($pass) < 4) {
			return 0 ;
		}
	}


	$confirm_passwd=cryptage($pass);
	if (($membre == "menuadmin" ) || ($membre == "menuprof") || ($membre == "menuscolaire") || ($membre == "menututeur") || ($membre == "menupersonnel")) {
		$sql="UPDATE ${prefixe}personnel SET mdp='$confirm_passwd'  WHERE  pers_id='$idpers' ";
		return(execSql($sql));
	}
	if ($membre == "menuparent" ) {
		$sql="UPDATE ${prefixe}eleves SET passwd='$confirm_passwd'  WHERE  elev_id='$idpers'";
		return(execSql($sql));
	}
	if ($membre == "menueleve" ) {
		$sql="UPDATE ${prefixe}eleves SET passwd_eleve='$confirm_passwd'  WHERE  elev_id='$idpers' ";
		return(execSql($sql));
	}
}

// mise à jour du mot de passe
function update_passwd_parent2($pass,$membre,$idpers) {
	global $cnx;
	global $prefixe;
	if (empty($pass)) { return 0; }
	include_once("./common/config2.inc.php");
	if (SECURITE == 3) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[A-Z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}
	if (SECURITE == 2) {
		if ( (strlen($pass) < 8) || (!preg_match('/[a-z]/',$pass)) || (!preg_match('/[0-9]/',$pass)) ) {
			return 0 ;
		}
	}
	if (SECURITE == 1) {
		if (strlen($pass) < 4) {
			return 0 ;
		}
	}
	$confirm_passwd=cryptage($pass);
	if ($membre == "menuparent" ) {
		$sql="UPDATE ${prefixe}eleves SET passwd_parent_2='$confirm_passwd'  WHERE  elev_id='$idpers'";
		return(execSql($sql));
	}
}

// mise à jour du mail personnel
function valide_email($mail,$idpers,$membre) {
	global $cnx;
	global $prefixe;
    $sql="UPDATE ${prefixe}personnel SET email='$mail'  WHERE idpers='$idpers'";
	return(execSql($sql));
}
// ---------------------------
// creation banniere
function create_banniere($nom_banniere,$date_debut,$date_fin,$frequence,$lien,$fichier) {
	global $cnx;
	global $prefixe;
	$date_debut=dateFormBase($date_debut);
	$date_fin=dateFormBase($date_fin);
	if ($frequence == "peu") { $freq=0; }  // peu
	if ($frequence == "normal") { $freq=1; }  // normal
	if ($frequence == "souvent") { $freq=2; }  // souvent
    $sql="INSERT INTO ${prefixe}banniere (nom,date_debut,date_fin,lien,frequence,image) VALUES ('$nom_banniere','$date_debut','$date_fin','$lien',$freq,'$fichier')";
	return(execSql($sql));
}

function visu_banniere() {
	global $cnx;
	global $prefixe;
$sql="SELECT nom,date_debut,date_fin,lien,frequence,image,code_banniere  FROM  ${prefixe}banniere  ORDER BY nom ";
$res=execSql($sql);
$data=chargeMat($res);
return $data;
}

function suppression_publicite($id) {
	global $cnx;
	global $prefixe;
    $sql="DELETE FROM  ${prefixe}banniere  WHERE code_banniere='$id'";
	return(execSql($sql));
}
// ---------------------------------------------------------

function create_assistance($membre,$action,$service,$commentaire,$nom,$prenom) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$commentaire=strip_tags($commentaire);
    $sql="INSERT INTO ${prefixe}bug (nom,prenom,date,membre,action,service,commentaire) VALUES ('$nom','$prenom','$date','$membre','$action','$service','$commentaire')";
	return(execSql($sql));
}

//-----------------------------
function dbase_filter($s){
  for($i = 0; $i < strlen($s); $i++){
      $code = ord($s[$i]);
          switch($code){
	      case 129:	$s[$i] = "ü"; break;
              case 130: $s[$i] = "é"; break;
	      case 131:	$s[$i] = "â"; break;
              case 132:	$s[$i] = "ä"; break;
	      case 133:	$s[$i] = "à"; break;
              case 135:	$s[$i] = "ç"; break;
	      case 136:	$s[$i] = "ê"; break;
              case 137:	$s[$i] = "ë"; break;
	      case 138:	$s[$i] = "è"; break;
              case 139:	$s[$i] = "ï"; break;
	      case 140:	$s[$i] = "î"; break;
              case 147:	$s[$i] = "ô"; break;
	      case 148:	$s[$i] = "ö"; break;
              case 150:	$s[$i] = "û"; break;
              case 151:	$s[$i] = "ù"; break;
          }
    }
return $s;
}

// --------------------------------------------
// Gestion garde info sur reference gep classe
function gep_classe($id_classe,$ref) {
	global $cnx;
	global $prefixe;
	if ($id_classe == "-1")  { return 1 ; }
	if ($id_classe == "")  { return 1 ; }
	if ($ref == "")  { return 1 ; }
	if ((trim($ref)  != "") && (trim($id_classe) != "")) {
		$sql="INSERT INTO ${prefixe}gep_classe (reference,id_classe) VALUES ('$ref','$id_classe')";
		return(execSql($sql));
	}
}

// recherche l'id classe en fonction de la reference GEP
function recherche_gep_classe($classeref) {
	global $cnx;
	global $prefixe;
	$classeref=trim(addslashes($classeref));
	$sql="SELECT reference,id_classe FROM ${prefixe}gep_classe WHERE reference='$classeref'";
    	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data[0][1];
}




function suppression_classe_gep($classe_supp) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}gep_classe WHERE id_classe='$classe_supp'";
	return(execSql($sql));
}

// ----------------------------------------
// Module de vidage dans la base
// ----------------------------------------
function vide_notes(){
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}notes";
	return(execSql($sql));
}

function vide_notes_scolaire(){
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}notes_scolaire";
	return(execSql($sql));
}

function vide_notes_classe($idClasse,$anneeScolaire){
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, classe FROM ${prefixe}eleves WHERE classe='$idClasse' ";
	$datedebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
	$datefin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
	$res=execSql($sql);
	$data=chargeMat($res);
	for ($int=0;$int<count($data);$int++) {
		$idEleve=$data[$int][0];
        	$sql="DELETE FROM ${prefixe}notes WHERE elev_id='$idEleve' AND date >= '$datedebut' AND date <= '$datefin' ";
        	execSql($sql);
	}
}

function recupJourMoisScolaire($etat,$anneeScolaire) {
	global $cnx;
        global $prefixe;
	list($anneeDebut,$anneeFin)=preg_split('/-/',"$anneeScolaire");
	$anneeDebut=trim($anneeDebut);
	$anneeFin=trim($anneeFin);
	if ($etat == "deb") {
		$anneescolaire_dj=aff_valeur_parametrage("anneescolaire_dj");
		$anneescolaire_dm=aff_valeur_parametrage("anneescolaire_dm");
		return("$anneescolaire_dj/$anneescolaire_dm/$anneeDebut");
		
	}elseif ($etat == "fin") {
		$anneescolaire_fj=aff_valeur_parametrage("anneescolaire_fj");
		$anneescolaire_fm=aff_valeur_parametrage("anneescolaire_fm");
		return("$anneescolaire_fj/$anneescolaire_fm/$anneeFin");
	}else{
		$retour="";
	}
	return($retour);

}

function vide_eleves_sans_classe(){
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}elevessansclasse";
	return(execSql($sql));
}

function vide_eleves(){
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}eleves";
	return(execSql($sql));
}

function vide_absences(){
	global $cnx;
	global $prefixe;
	puge_abs_rtd_aucun();
        $sql="DELETE FROM ${prefixe}absences";
	return(execSql($sql));
}

function vide_devoir_scolaire(){
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}devoir_scolaire";
	nettoyage_repertoire("./data/DevoirScolaire");
	return(execSql($sql));
}

function purgeimport() {
	DirPurge("./data/fichier_ASCII");
	DirPurge("./data/fichier_gep");
}

function vide_discipline_retenue(){
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}discipline_retenue";
	return(execSql($sql));
}

function vide_discipline_sanction(){
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}discipline_sanction";
	return(execSql($sql));
}

function vide_dispenses(){
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}dispenses";
	return(execSql($sql));
}

function vide_retards(){
	global $cnx;
	global $prefixe;
	puge_abs_rtd_aucun();
        $sql="DELETE FROM ${prefixe}retards";
	return(execSql($sql));
}

// supprime toutes les entrées de GEP_classe
function vide_gep_classe(){
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}gep_classe";
	return(execSql($sql));
}

function vide_message_prof_p() {
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}news_prof_p";
	return(execSql($sql));
}

//---------------------------------------------
function create_param($nom,$adresse,$postal,$ville,$tel,$mail,$directeur,$urlsite,$academie,$pays,$departement,$anneeScolaire,$idsite) {
	global $cnx;
	global $prefixe;
	if ($idsite == 0) {
		$sql="INSERT INTO ${prefixe}info_ecole (nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire,map) VALUES ('$nom','$adresse','$postal','$ville','$tel','$mail','$directeur','$urlsite','$academie','$pays','$departement','$anneeScolaire','1')";
		execSql($sql);
	}else{
		$sql="UPDATE ${prefixe}info_ecole SET 	
			nom_ecole='$nom', 
			adresse='$adresse', 
			postal='$postal', 
			ville='$ville', 
			tel='$tel', 
			email='$mail', 
			directeur='$directeur', 
			urlsite='$urlsite', 
			academie='$academie', 
			pays='$pays', 
			departement='$departement',
			annee_scolaire='$anneeScolaire',
			map='1'
			WHERE id='$idsite'";
		execSql($sql);
	}
	if ($idsite == 1) {
		$sql="UPDATE `settings_current` SET `selected_value`='$nom' WHERE `id`='3'";
		execSql($sql);
	}
}


function paramnouvelleannee() {
	global $cnx;
	global $prefixe;
	$anneepre=date("Y")-1;
	$anneescolaire=$anneepre." - ".date("Y");
	list($a,$b)=preg_split('/ - /',$anneescolaire);
	$c=$b+1;
	$anneescolairenew="$b - $c";
	$sql="UPDATE ${prefixe}info_ecole SET annee_scolaire='$anneescolairenew'";
	execSql($sql);
	return($anneescolairenew);
}

function visu_param(){
	global $cnx;
	global $prefixe;
	$sql="SELECT  nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire FROM ${prefixe}info_ecole WHERE id='1'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function suppr_param($idsite) {
	global $cnx;
	global $prefixe;
	if ($idsite != 1) {
	        $sql="DELETE FROM ${prefixe}info_ecole WHERE id='$idsite'";
		execSql($sql);
	}
}

function chercherIdSiteClasse($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idsite FROM ${prefixe}classes WHERE code_class='$id'";
	$res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}


function visu_param_id($id){
	global $cnx;
	global $prefixe;
	$sql="SELECT  nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire,id FROM ${prefixe}info_ecole WHERE id='$id' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function select_site($id) {
        global $cnx;
        global $prefixe;
        $sql="SELECT  nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire,id FROM ${prefixe}info_ecole";
        $res=execSql($sql);
        $data=chargeMat($res);
        for($i=0;$i<count($data);$i++) {
                if ($id == $data[$i][12]) {
                        print "<option id='select1' value='".$data[$i][12]."' selected='selected' >".$data[$i][12].") ".$data[$i][0]."</option>";
                }else{
                        print "<option id='select1' value='".$data[$i][12]."' >".$data[$i][12].") ".$data[$i][0]."</option>";
                }
        }
}


function chercheClasseEleve($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT classe  FROM ${prefixe}eleves WHERE elev_id='$idEleve'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}

//--------------------------------------------
function verifMatiereAvecGroupeCarnetDeNote($idMatiere,$idEleve,$idClasse,$anneeScolaire='') {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == '') $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT code_matiere,code_groupe FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND annee_scolaire='$anneeScolaire' ";
        $curs=execSql($sql);
        $resultat=chargeMat($curs);
        unset($curs);
        unset($sql);
        if ($resultat[0][1] == 0) {
                       return 0;
        }else {
                       $tab_idGr=$resultat;
        }
        for($i=0;$i<count($tab_idGr);$i++) {
                $sql="SELECT group_id,liste_elev FROM ${prefixe}groupes WHERE group_id='".$tab_idGr[$i][1]."'";
                $res=execSql($sql);
                $data=chargeMat($res);
                $liste_eleves=preg_replace('/\{/',"",$data[0][1]);
                $liste_eleves=preg_replace('/\}/',"",$liste_eleves);
                $listeEleve=preg_split("/,/", $liste_eleves);
                foreach ($listeEleve as $valeur) {
                        if ($valeur == $idEleve) {
                                return 0;
                        }
                }
        }
        return 1;
}


function verifMatiereAvecGroupeCarnetDeNote2($idMatiere,$idEleve,$idClasse,$ordre,$anneeScolaire='') {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == '') $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT code_matiere,code_groupe FROM ${prefixe}affectations WHERE  ordre_affichage='$ordre' AND code_matiere='$idMatiere' AND code_classe='$idClasse' AND annee_scolaire='$anneeScolaire' ";
        $curs=execSql($sql);
        $resultat=chargeMat($curs);
        unset($curs);
        unset($sql);
        if ($resultat[0][1] == 0) {
                       return 0;
        }else {
                       $tab_idGr=$resultat;
        }
        for($i=0;$i<count($tab_idGr);$i++) {
                $sql="SELECT group_id,liste_elev FROM ${prefixe}groupes WHERE group_id='".$tab_idGr[$i][1]."'";
                $res=execSql($sql);
                $data=chargeMat($res);
                $liste_eleves=preg_replace('/\{/',"",$data[0][1]);
                $liste_eleves=preg_replace('/\}/',"",$liste_eleves);
                $listeEleve=preg_split ("/,/", $liste_eleves);
                foreach ($listeEleve as $valeur) {
                        if ($valeur == $idEleve) {
                                return 0;
                        }
                }
        }
        return 1;
}


// gestion history periode
// mise à jour des logs history
function historyPeriode($fichier,$classe_nom,$nom_periode,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}history_periode(fichier,classe,periode,datedebut,datefin) VALUES ('$fichier','$classe_nom','$nom_periode','$dateDebut','$dateFin')";
	//return(execSql($sql));
	return 1;
}

function destructionPeriode($fichier,$classe_nom,$periode,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
        $sql="SELECT * FROM history_periode WHERE fichier='$fichier' AND classe='$classe_nom' AND periode='$periode' AND datedebut='$dateDebut' AND datefin='$dateFin' ";
        $res=execSql($sql);
        $data=chargeMat($res);
	return $data;
}

// affichage des history
function historyPeriodeAff() {
	global $cnx;
	global $prefixe;
        $sql="SELECT fichier,classe,periode,datedebut,datefin,idhistory  FROM  ${prefixe}history_periode ORDER BY classe, datefin DESC";
        $res=execSql($sql);
        $data=chargeMat($res);
	return $data;
}
// suppression history periode
function supp_history_periode($id) {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}history_periode WHERE idhistory='$id'";
	return(execSql($sql));
}

function purge_history_periode() {
	global $cnx;
	global $prefixe;
    	$sql="DELETE FROM ${prefixe}history_periode";
	$rep="./data/pdf_bull/";
	$Array = array(); $dir = opendir($rep);
  	$i=0;
  	while ($File = readdir($dir)){
		if($File != "." && $File != ".." && $File != ".htaccess") {
			if (preg_match('/^tableaupp/',$File)) {
				$fichier="$rep/$File";
      				@unlink("$fichier");
			}
			if (preg_match('/^periode/',$File)) {
				$fichier="$rep/$File";
      				@unlink("$fichier");
			}
			if (preg_match('/jpg$/',$File)) {
				$fichier="$rep/$File";
      				@unlink("$fichier");
			}
			if (preg_match('/^edition/',$File)) {
				$fichier="$rep/$File";
      				@unlink("$fichier");
			}
  		}
    		$i++;
  	}
	closedir($dir);
	return(execSql($sql));
}

// gestion history bulletin
// mise à jour des logs history
function historyBulletin($fichier,$classe,$trimestre,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="INSERT INTO ${prefixe}history_bulletin(fichier,classe,trimestre,datedebut,datefin) VALUES ('$fichier','$classe','$trimestre','$dateDebut','$dateFin')";
	//return(execSql($sql));
	return 1;
}

function destruction_bulletin($fichier,$classe_nom,$trimestre,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
    $sql="DELETE FROM ${prefixe}history_bulletin WHERE fichier='$fichier' AND  classe='$classe_nom'  AND trimestre='$trimestre' AND datedebut='$dateDebut' AND datefin='$dateFin' ";
	return(execSql($sql));
}

function purge_bulletin() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}history_bulletin";
	$rep="./data/pdf_bull/";
	$Array = array(); $dir = opendir($rep);
  	$i=0;
  	while ($File = readdir($dir)){
		if($File != "." && $File != ".." && $File != ".htaccess") {
			if (preg_match('/^bulletin/',$File)) {
				$fichier="$rep/$File";
      				@unlink("$fichier");
			}
		}
    		$i++;
  	}
	closedir($dir);
	return(execSql($sql));
}

function purge_trimestre() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}date_trimestrielle";
	return(execSql($sql));
}

// affichage des history
function historyBulletinAff() {
	global $cnx;
	global $prefixe;
    $sql="SELECT fichier,classe,trimestre,datedebut,datefin,idhistory  FROM  ${prefixe}history_bulletin ORDER BY classe, datedebut DESC";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


// recherche le trimestre en cours
function recherche_trimestre_en_cours() {
	global $cnx;
	global $prefixe;
	$date_aujoudhui=dateDMY2();
	$sql="SELECT date_debut,date_fin,trim_choix  FROM  ${prefixe}date_trimestrielle WHERE date_debut <='$date_aujoudhui' AND date_fin >= '$date_aujoudhui'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][2];  // return trim_choix
}

function recherche_trimestre_en_cours_via_classe($idclasse,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	$date_aujoudhui=dateDMY2();
	if ($anneeScolaire != "") $sqlsuite=" AND annee_scolaire = '$anneeScolaire' ";
	$sql="SELECT date_debut,date_fin,trim_choix  FROM  ${prefixe}date_trimestrielle WHERE date_debut <='$date_aujoudhui' AND date_fin >= '$date_aujoudhui' AND ( idclasse='$idclasse' OR idclasse='0') $sqlsuite ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][2];  // return trim_choix
}




function recherche_trimestre_classe_anneescolaire($anneeScolaire,$idclasse) {
        global $cnx;
        global $prefixe;
        $sql="SELECT date_debut,date_fin,trim_choix  FROM  ${prefixe}date_trimestrielle WHERE  ( idclasse='$idclasse' OR idclasse='0' )  AND  annee_scolaire='$anneeScolaire' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);  // return trim_choix
}


function recherche_intervalle_trimestre($choix_tri) {
	global $cnx;
	global $prefixe;
	$sql="SELECT date_debut,date_fin,trim_choix  FROM  ${prefixe}date_trimestrielle WHERE trim_choix='$choix_tri' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function recherche_intervalle_trimestre_via_classe($choix_tri,$idclasse,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$sql="SELECT date_debut,date_fin,trim_choix  FROM  ${prefixe}date_trimestrielle WHERE trim_choix='$choix_tri' AND ( idclasse='$idclasse' OR idclasse='0') AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


// suppression history periode
function supp_history_bulletin($id) {
	global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}history_bulletin WHERE idhistory='$id'";
	return(execSql($sql));
}


// eleve sans classe
function affElevesansclasse() {
	global $cnx;
	global $prefixe;
        $sql="SELECT *  FROM  ${prefixe}elevessansclasse  ORDER BY nom LIMIT 30";
        $res=execSql($sql);
        $data=chargeMat($res);
	return $data;
}


function affElevesansclasseAutre($nb) {
	global $cnx;
	global $prefixe;
	$nb = 30 - $nb;
        $sql="SELECT elev_id,nom,prenom,lv1,lv2,classe  FROM  ${prefixe}eleves WHERE classe='-1' ORDER BY nom LIMIT $nb";
        $res=execSql($sql);
        $data=chargeMat($res);
	return $data;
}


function affElevesansclasseAutreTotal() {
	global $cnx;
	global $prefixe;
        $sql="SELECT elev_id,nom,prenom,lv1,lv2,classe  FROM  ${prefixe}eleves WHERE classe='-1' ";
        $res=execSql($sql);
        $data=chargeMat($res);
	return $data;
}


function affElevesansclasseTotal() {
	global $cnx;
	global $prefixe;
        $sql="SELECT *  FROM  ${prefixe}elevessansclasse";
        $res=execSql($sql);
        $data=chargeMat($res);
	return $data;
}

// demande de DST
function demande_dst($date,$classe,$text,$id_pers,$heure,$duree) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="INSERT INTO ${prefixe}demande_dst(id_pers,date_dem,classe,mat_text,heure,duree) VALUES ('$id_pers','$date','$classe','$text','$heure','$duree')";
	return(execSql($sql));
}

function datedemandedst($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM ${prefixe}demande_dst WHERE id_dem='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][2];
}

function chercheval($id){
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM ${prefixe}demande_dst WHERE id_dem='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][4];
}

function chercheClasseDemandeDst($id){
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM ${prefixe}demande_dst WHERE id_dem='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][3];
}

function iddemandedst($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM ${prefixe}demande_dst WHERE id_dem='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][1];
}

// visualisation des demandes de DST
function consult_demande_dst() {
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM ${prefixe}demande_dst ORDER BY date_dem ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function consult_demande_dst_acces2() {
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM ${prefixe}demande_dst ORDER BY date_dem LIMIT 1";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function purge_dst2($datedujour) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM  ${prefixe}calendrier_dst WHERE date <= '$datedujour' ";
	return(execSql($sql));
}

function purge_evenement2($datedujour) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM  ${prefixe}calend_evenement WHERE date <= '$datedujour' ";
	return(execSql($sql));
}

function purgeEdtSeance2($datedujour) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}edt_seances WHERE date <= '$datedujour' ";
	return(execSql($sql));

}

function purge_dst() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}demande_dst ";
	return(execSql($sql));
}
function supp_dem_dst($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}demande_dst WHERE id_dem='$id'";
	return(execSql($sql));
}

function supp_dem_dst_by_prof($id,$idpers){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}demande_dst WHERE id_dem='$id' AND id_pers='$idpers' ";
	return(execSql($sql));
}


// recherche note
function recherche_note($id_eleve,$sql2) {
	global $cnx;
	global $prefixe;
	if(DBTYPE=='pgsql')
	{
		$sql="
		SELECT
			note_id,
			elev_id,
			prof_id,
			code_mat,
			coef,
			date,
			sujet,
			TRUNC(note,2)
		FROM
			${prefixe}notes
		WHERE
			elev_id='$id_eleve'
		AND ".$sql2."
		ORDER BY
			date DESC";
	}
	elseif(DBTYPE=='mysql')
	{
		$sql="
		SELECT
			note_id,
			elev_id,
			prof_id,
			code_mat,
			coef,
			date,
			sujet,
			TRUNCATE(note,2)
		FROM
			${prefixe}notes
		WHERE
			elev_id='$id_eleve'
		AND ".$sql2."
		ORDER BY
			date DESC";
	}
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


// recherche note pour visu prof
function recherche_note_pour_prof($id_eleve,$sql2,$idmatiere,$idpers) {
	global $cnx;
	global $prefixe;
	if (trim($sql2) != "") {
		$f_trunc = DBTYPE=='pgsql'?'trunc':'truncate';
		$sql="SELECT note_id,elev_id,prof_id,code_mat,coef,date,sujet,$f_trunc(note,2),typenote,noteexam,notationsur  FROM ${prefixe}notes WHERE elev_id='$id_eleve' AND ".$sql2." AND code_mat='$idmatiere' AND prof_id='$idpers' ORDER BY date ASC,sujet";
		$res=execSql($sql);
		$data=chargeMat($res);
		return $data;
	}else{
		return "";
	}
}

function recherche_note_pour_admin($id_eleve,$sql2,$idmatiere,$idpers) {
	global $cnx;
	global $prefixe;
	if ($_SESSION["membre"] != "menuadmin" ) {
		if (!verifDroit($_SESSION["id_pers"],"carnetnotes")) {
			accesNonReserveFen();
		}
	}else{
		validerequete("menuadmin");
	}
	if (trim($sql2) != "") {
		$f_trunc = DBTYPE=='pgsql'?'trunc':'truncate';
		$sql="SELECT note_id,elev_id,prof_id,code_mat,coef,date,sujet,$f_trunc(note,2),typenote,noteexam,notationsur  FROM ${prefixe}notes WHERE elev_id='$id_eleve' AND ".$sql2." AND code_mat='$idmatiere' ORDER BY date ASC,sujet";
		$res=execSql($sql);
		$data=chargeMat($res);
		return $data;
	}else{
                return array();
	}
}

function recherche_note_pour_scolaire($id_eleve,$sql2,$idmatiere,$idpers) {
	global $cnx;
        global $prefixe;
        if ($_SESSION["membre"] == "menuprof") {
                verif_profp_ens($_SESSION["id_pers"]);
        }else{
                validerequete("2");
        }
        if (trim($sql2) != "") {
                $f_trunc = DBTYPE=='pgsql'?'trunc':'truncate';
                $sql="SELECT note_id,elev_id,prof_id,code_mat,coef,date,sujet,$f_trunc(note,2),typenote,noteexam,notationsur  FROM ${prefixe}notes WHERE elev_id='$id_eleve' AND ".$sql2." AND code_mat='$idmatiere' ORDER BY date ASC,sujet";
                $res=execSql($sql);
                $data=chargeMat($res);
                return $data;
        }else{
                return array();
        } 
	
}


// history des opérations éffectué
function history_cmd($user_cmd,$cmd,$com){
	global $cnx;
	global $prefixe;
	if (trim($user_cmd) != "") {
		$time_cmd=dateHIS();
		$date_cmd=dateDMY2();
		$com=addslashes(utf8_decode($com));
		$user_cmd=addslashes(utf8_decode($user_cmd));
		$sql="INSERT INTO ${prefixe}history_cmd (time_cmd,date_cmd,user_cmd,cmd,commentaire) VALUES ('$time_cmd','$date_cmd','$user_cmd','$cmd','$com')";
		execSql($sql);
	}
	$com=strip_tags($com);
	$com=preg_replace('/\'/',"\'",$com);
	$info=$user_cmd."##".$cmd."##".$com;
	acceslog($info);
}


function history_cmdAdmin($user_cmd,$cmd,$com){
	global $cnx;
	global $prefixe;
	if (trim($user_cmd) != "") {
		$time_cmd=dateHIS();
		$date_cmd=dateDMY2();
		$com=addslashes($com);
		$user_cmd=addslashes($user_cmd);
		$sql="INSERT INTO ${prefixe}history_cmd (time_cmd,date_cmd,user_cmd,cmd,commentaire) VALUES ('$time_cmd','$date_cmd','$user_cmd','$cmd','$com')";
		execSql($sql);
	}
	$com=strip_tags($com);
	$com=preg_replace('/\'/',"\'",$com);
	$info=$user_cmd."##".$cmd."##".$com;
	acceslogAdmin($info);
}

function affHistoryCmd(){
	global $cnx;
	global $prefixe;
	$sql="SELECT time_cmd,date_cmd,user_cmd,cmd,commentaire,id  FROM ${prefixe}history_cmd ORDER by date_cmd DESC,time_cmd DESC LIMIT 400";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

// pour supprimer tous les fichiers
// d'un repertoire
function nettoyage_repertoire($repdir) {
	$h=opendir($repdir);
	while($file=readdir($h)){
		if($file!="." && $file!=".." && $file !=".htaccess"){
			unlink("$repdir/$file");
		}
	}
	closedir($h);
}


// supprimer_rep (array ('test2'));
function supprimer_rep ($tableau) { // fonction pour supprimer un ou plusieurs repertoires et tout ce qu'il y a dedans
	foreach ($tableau as $dir) {
	if (file_exists ($dir)) {
    	$dh = opendir ($dir);
    	while (($file = readdir ($dh)) !== false ) { 
    		if ($file !== '.' && $file !== '..') {
    			if (is_dir ($dir.'/'.$file)) {
    				$tab = array ($dir.'/'.$file);
    			  supprimer_rep ($tab); // si on trouve un repertoire, on fait un appel recursif pour fouiller ce repertoire
    			}
    			else {
    				if (file_exists ($dir.'/'.$file)) {
    					unlink ($dir.'/'.$file); // si on trouve un fichier, on le supprime
    				}
    			}
    		}
    	}
    	closedir ($dh);
    	if (is_dir ($dir)) {
    		rmdir ($dir); // on supprime le repertoire courant
    	}
    return true;
    }
  }
}



// Module de sauvegarde complete
//------------------------------
function sauvegarde_total($user,$mdp) {
	nettoyage_repertoire("./data/sauvegarde");
	$fichier=fopen("./data/tempo","w");
	$donnee=fwrite($fichier,"$mdp");
	fclose($fichier);
	$val=rand(1000,9999);
	$fic_sauve="dbname_".$val.".sql";
	exec('pg_dump -h localhost -U '.$user.' campus > ./data/sauvegarde/'.$fic_sauve.' < ./data/tempo');
	unlink("./data/tempo");
	// sauve en tar
	exec('tar cvf ./data/sauvegarde/'.$fic_sauve.'.tar .');
	// return
	$ficok="./data/sauvegarde/$fic_sauve";
    return $ficok;
}

// Module de restauration complete
//--------------------------------
function restauration_total($user,$mdp,$ficsql) {
	$fichier=fopen("./data/tempo","w");
	$donnee=fwrite($fichier,"$mdp");
	fclose($fichier);
	exec('psql  -d campus -U '.$user.' -f ./data/sauvegarde/'.$ficsql.' < ./data/tempo');
	unlink("./data/tempo");
	// return
	return 1;
}
// -----



// suppression d'un eleve dans la base
function suppEleveInfo($id_eleve){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	return(execSql($sql));
}
function suppElevenote($id_eleve){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}notes WHERE elev_id='$id_eleve'";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_scolaire_com WHERE ideleve='$id_eleve'";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}notes_scolaire WHERE ideleve='$id_eleve'";
	return(execSql($sql));
}

function suppEleveCompta($id_eleve) {
	global $cnx;
	global $prefixe;
	if (trim($id_eleve) != '') {
		$sql="DELETE FROM ${prefixe}comptaconfig WHERE ideleve='$id_eleve'";
		execSql($sql);
		$sql="DELETE FROM ${prefixe}comptaexclu WHERE ideleve='$id_eleve'";
		execSql($sql);
	}
}

function suppEleveabs($id_eleve){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}absences WHERE elev_id='$id_eleve'";
	return(execSql($sql));
}
function suppEleveretard($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}retards WHERE elev_id='$id_eleve'";
	return(execSql($sql));
}
function suppElevediscipline($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}discipline_retenue WHERE id_elev='$id_eleve'";
	return(execSql($sql));
}
function suppElevesanction($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}discipline_sanction WHERE id_eleve='$id_eleve'";
	return(execSql($sql));
}
function suppEleveSansClasse($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}elevessansclasse WHERE elev_id='$id_eleve'";
	return(execSql($sql));
}
function suppElevedisp($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}dispenses WHERE elev_id='$id_eleve'";
	return(execSql($sql));
}
function suppEleveMessagerieDest($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}messageries WHERE destinataire='$id_eleve' AND (type_personne_dest='PAR' OR type_personne_dest='ELE') ";
	return(execSql($sql));
}
function suppEleveMessagerieEnvoi($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}messageries WHERE emetteur='$id_eleve' AND (type_personne_dest='PAR' OR type_personne_dest='ELE') ";
	return(execSql($sql));
}
function suppEleveMessagerieRepertoire($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}messagerie_repertoire WHERE id_pers='$id_eleve' AND (membre ='menueleve' OR membre ='menuparent') ";
	return(execSql($sql));
}
function suppEleveCarnetEvaluation($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}carnet_evaluation WHERE  ideleve='$id_eleve' ";
	return(execSql($sql));
}

function suppElevePlanClasse($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}planclasse WHERE  ideleve='$id_eleve' ";
	return(execSql($sql));
}

function suppElevegroupe($id_eleve) {
	global $cnx;
	global $prefixe;
	if (trim($id_eleve) != "") {
		$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes ORDER BY libelle ";
		$res=execSql($sql);
		$data=chargeMat($res);
		if (count($data) > 0) {
			for($i=0;$i<count($data);$i++) {
				$idgrp=trim($data[$i][0]);
				$liste_eleves="";
				if ($idgrp != "") {
					$liste_eleves=preg_replace('/\{/',"",$data[$i][1]);
					$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
					$tab_eleve=explode(",",$liste_eleves);
					unset($tabGrp);
					foreach($tab_eleve as $key) {
						if ($key != $id_eleve) {
							$tabGrp[$key]=$key;
						}
					}	
					$liste_eleves=implode(",", $tabGrp);
					$liste_eleves="{".$liste_eleves."}";
					$liste_eleves=preg_replace('/\{,/',"{",$liste_eleves);
					$liste_eleves=preg_replace('/,\}/',"}",$liste_eleves);
					$idgrp=addslashes($idgrp);
					$sql="UPDATE ${prefixe}groupes SET liste_elev='$liste_eleves' WHERE libelle='$idgrp'";
					execSql($sql);
				}
			}
		}
	}
}


function suppEleveStage($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}stage_eleve WHERE id_eleve='$id_eleve' ";
	return(execSql($sql));
}

function suppEleveCommentaireBulletin($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_direction_com WHERE ideleve='$id_eleve' ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_profp_com WHERE ideleve='$id_eleve' ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_profp_ue WHERE ideleve='$id_eleve' ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_prof_com WHERE ideleve='$id_eleve' ";
	execSql($sql);

}
/////----------------------------------------------------------------------------------------------------

function listeEleveDansGroupe($idgroupe) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=anneeScolaireViaIdClasse();
	$sql="SELECT group_id,liste_elev FROM ${prefixe}groupes WHERE  group_id='$idgroupe' AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
	$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
	unset($sql);
	$tab_eleve=explode(",",$liste_eleves);
	return $tab_eleve;  //
}



function rechercheEleveDansGroupe($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT group_id,liste_elev,libelle FROM ${prefixe}groupes order by  libelle ";
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$idgrp=$data[$i][0];
		$liste_eleves=preg_replace('/\{/',"",$data[$i][1]);
		$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
		$tab_eleve=explode(",",$liste_eleves);
		foreach($tab_eleve as $key) {
			if ($key == $ideleve) {
				$tabGrp[$idgrp]=$idgrp;
				break;
			}
		}
	}
	return $tabGrp;  //
}


// changement de classe
function changementClasseEleve($id_eleve,$newsClasse,$anneefuture) {
	global $cnx;
	global $prefixe;
	@saveInfoStage($id_eleve);
	$sql="SELECT classe,annee_scolaire FROM  ${prefixe}eleves WHERE elev_id='$id_eleve' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$annee_scolaire=$anneefuture;
	$anneeScolairePasse=$data[0][1];
	$idclassold=$data[0][0];
	$oldClasse=chercheClasse_nom($data[0][0]);
	$sql="UPDATE ${prefixe}eleves SET classe='$newsClasse',class_ant='$oldClasse',annee_scolaire='$annee_scolaire' WHERE elev_id='$id_eleve'";
	$cr=execSql($sql);
	if ($cr) {
		if (($anneeScolairePasse != $annee_scolaire) && ($id_eleve != 0) && (trim($anneeScolairePasse) !=  "")) { 
			$sql="INSERT INTO ${prefixe}eleves_histo (ideleve,idclasse,annee_scolaire) VALUES ('$id_eleve','$idclassold','$anneeScolairePasse')";
			execSql($sql);	
		}
	}
	return($cr);
}
// fin changement




// module de news
function create_news($titre,$news,$nom,$prenom,$id) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$heure=dateHIS();

	$titre=$cnx->escapeSimple($titre);
	$news=$cnx->escapeSimple($news);
	$nom=$cnx->escapeSimple($nom);
	$prenom=$cnx->escapeSimple($prenom);

	if (trim($id) != "") {
		$sql="DELETE FROM ${prefixe}news_admin WHERE idnews='$id'";
		execSql($sql);
	}
	$sql="INSERT INTO ${prefixe}news_admin(nom,prenom,date,heure,titre,texte) VALUES ('$nom','$prenom','$date','$heure','$titre','$news')";
	return(execSql($sql));
}


function create_news_video($titre,$news,$nom,$prenom,$type,$configflv) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$heure=dateHIS();
	$sql="INSERT INTO ${prefixe}news_admin(nom,prenom,date,heure,titre,texte,type,config_video) VALUES ('$nom','$prenom','$date','$heure','$titre','$news','$type','$configflv')";
	return(execSql($sql));
}


function create_news_page_1($titre,$news) {
	$fichier="./data/fic_news_page_contenu.txt";
	@unlink($fichier);
	$fic=fopen($fichier,"a+");
    	fwrite($fic,$news);
	fclose($fic);

	$fichier="./data/fic_news_page_titre.txt";
	@unlink($fichier);
	$fic=fopen($fichier,"a+");
    	fwrite($fic,$titre);
	fclose($fic);

	$fichier="./data/fic_news_page_date.txt";
	@unlink($fichier);
	$fic=fopen($fichier,"a+");
	$date=dateDMY();
    	fwrite($fic,$date);
	fclose($fic);
}



function consultMessAdmin() {
	global $cnx;
	global $prefixe;
	$sql="SELECT idnews,nom,prenom,date,heure,titre,texte,type FROM  ${prefixe}news_admin  ORDER BY date DESC, heure DESC LIMIT 30";
	$res=execSql($sql);
	$data=chargeMat($res);
	unset($sql);
	return $data;
}


function consultMessAdminId($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idnews,nom,prenom,date,heure,titre,texte,type FROM  ${prefixe}news_admin WHERE idnews='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}



function purge_news() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}news_admin  ";
        return(execSql($sql));
}
// fin du module de news

// enleve les accents de la chaine
function TextNoAccent($str){
       $str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
       $str = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $str);
       $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
       $str = preg_replace('#&[^;]+;#', '', $str);
       return($str);
}



function TextNoCarac($text) {
        $text=preg_replace('/°/',"_",$text);
        $text=preg_replace('/\+/',"_",$text);
        $text=preg_replace('/\&/',"_",$text);
        $text=preg_replace('/ /',"_",$text);
        $text=preg_replace('/\//',"_",$text);
        $text=preg_replace('/\'/',"_",$text);
        $text=preg_replace('/"/',"_",$text);
        return($text);
}

// ----------------------------------------- //
// Module de gestion précédent et suivant //
// fichier_destination,table_SQL,poiint_de_depart_liste,nombre_element_affichage
 //
 //
function suivant0($fichier,$table,$limit,$nbaff,$requete) {
	global $cnx;
	global $prefixe;
        $sqlS="SELECT *  FROM ${prefixe}$table $requete ";
        $resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);
	$limit=$limit + $nbaff;
	$get="";
	if (preg_match('/#/',$fichier)) {
		list($fichier,$get)=preg_split('/#/',$fichier);
		$get="&".$get;
	}

	if ($limit < $nbElement) {
           $url="$fichier?limit=$limit&nba=${nbaff}$get&id=$id";
           print "<input type=button class=BUTTON value='Suivant -->' onclick=\"open('".$url."','_parent','')\"> ";
	}
}

function precedent0($fichier,$table,$limit,$nbaff,$requete) {
	global $cnx;
	global $prefixe;
        $sqlS="SELECT *  FROM ${prefixe}$table $requete ";
        $resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);

        $limit=$limit - $nbaff;
	$get="";
	if (preg_match('/#/',$fichier)) {
		list($fichier,$get)=preg_split('/#/',$fichier);
		$get="&".$get;
	}
       	if ($limit >= 0) {
             $url="$fichier?limit=$limit&nba=${nbaff}$get&id=$id";
             print "<input type=button class=BUTTON value='<-- Précédent' onclick=\"open('".$url."','_parent','')\"> ";
       	}
}

function suivant($fichier,$table,$limit,$nbaff,$champs,$id) {
	global $cnx;
	global $prefixe;
        $sqlS="SELECT *  FROM ${prefixe}$table WHERE $champs = '$id' ";
        $resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);
        $limit=$limit + $nbaff;
	if ($limit < $nbElement) {
           $url="$fichier?limit=$limit&nba=$nbaff&id=$id";
           print "<input type=button class=BUTTON value='Suivant -->' onclick=\"open('".$url."','_parent','')\"> ";
	}
}


function suivant4($fichier,$table,$limit,$nbaff,$idClasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	if ( (trim($dateDebut) == "") && (trim($dateFin) == "")) {
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie , creneaux FROM ${prefixe}absences WHERE duree_ab='0' OR motif='inconnu' OR justifier!='1' ORDER BY elev_id, date_ab DESC";
		}else{
			$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, a.justifier, a.heure_saisie, a.creneaux FROM ${prefixe}absences a, ${prefixe}eleves e WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND ( a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ab DESC";
		}		
	}else{
		$dateDebut=dateFormBase($dateDebut);
		$dateFin=dateFormBase($dateFin);
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie , creneaux FROM ${prefixe}absences WHERE (duree_ab='0' OR motif='inconnu' OR justifier!='1') AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' ORDER BY elev_id, date_ab DESC";
		}else{
			$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, a.justifier, a.heure_saisie, a.creneaux FROM ${prefixe}absences a, ${prefixe}eleves e WHERE  date_ab >= '$dateDebut' AND date_ab <= '$dateFin' AND (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND ( a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ab DESC";
		}	
	}
	$res=execSql($sql);
	$dataS=chargeMat($res);
	$nbElement=count($dataS);
	$limit=$limit + $nbaff;
	if ($limit < $nbElement) {
           $url="$fichier?limit=$limit&nba=$nbaff&filtre=$idClasse&dateDebut=$dateDebut&dateFin=$dateFin";
           print "<input type=button class=BUTTON value='Suivant -->' onclick=\"open('".$url."','_parent','')\"> ";
	}
}


function suivant2($fichier,$table,$limit,$nbaff) {
	global $cnx;
	global $prefixe;
        $sqlS="SELECT *  FROM ${prefixe}$table ";
        $resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);
        $limit=$limit + $nbaff;
	if ($limit < $nbElement) {
           $url="$fichier?limit=$limit&nba=$nbaff&id=$id";
           print "<input type=button class=BUTTON value='".LANGBTS."' onclick=\"open('".$url."','_parent','')\"> ";
	}
}

function suivant2VATEL($fichier,$table,$limit,$nbaff) {
	global $cnx;
	global $prefixe;
        $sqlS="SELECT *  FROM ${prefixe}$table ";
        $resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);
        $limit=$limit + $nbaff;
	if ($limit < $nbElement) {
           $url="$fichier?limit=$limit&nba=$nbaff&id=$id";
           print "<input type=button class='btn btn-primary btn-sm  vat-btn-footer'   value='".LANGBTS."' onclick=\"open('".$url."','_parent','')\"> ";
	}
}

function suivant_entr($fichier,$table,$limit,$nbaff,$champs,$id,$departement,$activite=-1,$ville,$secteureconomique,$siren,$formejuridique,$typeorganisation,$NAFAPE,$NACE) {
	global $cnx;
	global $prefixe;

		if ($departement != "") { 
		if (preg_match('/%/', $departement)) {
			$sqlsuite.="AND code_p LIKE '$departement' ";
		}else{
			$sqlsuite.="AND code_p='$departement' ";
		}  
	}
	if ($ville != "") { 
		if (preg_match('/%/', $ville)) {
			$sqlsuite.="AND ville LIKE '$ville' ";
		}else{
			$sqlsuite.="AND ville = '$ville' ";
		}  
	}
	if (($activite != "-1") && ($activite != "inconnu")) { 
		if (preg_match('/%/', $activite)) {
			$sqlsuite.="AND (secteur_ac LIKE '$activite'  OR secteur_ac2 LIKE '$activite' OR secteur_ac3 LIKE '$activite') ";
		}else{
			$sqlsuite.="AND (secteur_ac = '$activite' OR secteur_ac2='$activite' OR secteur_ac3='$activite') ";
		}  
	}
	if ($secteureconomique != "") { 
		if (preg_match('/%/', $secteureconomique)) {
			$sqlsuite.="AND secteureconomique LIKE '$secteureconomique' ";
		}else{
			$sqlsuite.="AND secteureconomique = '$secteureconomique' ";
		}  
	}
	if ($siren != "") { 
		if (preg_match('/%/', $siren)) {
			$sqlsuite.="AND siren LIKE '$siren' ";
		}else{
			$sqlsuite.="AND siren = '$siren' ";
		}  
	}
	if ($formejuridique != "") { 
		if (preg_match('/%/', $formejuridique)) {
			$sqlsuite.="AND formejuridique LIKE '$formejuridique' ";
		}else{
			$sqlsuite.="AND formejuridique = '$formejuridique' ";
		}  
	}
	if ($typeorganisation != "") { 
		if (preg_match('/%/', $typeorganisation)) {
			$sqlsuite.="AND typeorganisation LIKE '$typeorganisation' ";
		}else{
			$sqlsuite.="AND typeorganisation = '$typeorganisation' ";
		}  
	}
	if ($NAFAPE != "") { 
		if (preg_match('/%/', $NAFAPE)) {
			$sqlsuite.="AND NAFAPE LIKE '$NAFAPE' ";
		}else{
			$sqlsuite.="AND NAFAPE = '$NAFAPE' ";
		}  
	}
	if ($NACE != "") { 
		if (preg_match('/%/', $NACE)) {
			$sqlsuite.="AND NACE LIKE '$NACE' ";
		}else{
			$sqlsuite.="AND NACE = '$NACE' ";
		}  
	}


	if ($sqlsuite != "") { 
		$sqlsuite="WHERE$sqlsuite "; 
		$sqlsuite=preg_replace('/WHEREAND/',' WHERE ',$sqlsuite);
	}

	$sqlS="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation FROM ${prefixe}stage_entreprise   $sqlsuite ORDER BY 2 ";


       	$resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);
        $limit=$limit + $nbaff;
	if ($limit < $nbElement) {
           $url="$fichier?limit=$limit&nba=$nbaff&id=$id&departement=$departement&ville=$ville&&secteureconomique=$secteureconomique&siren=$siren&formejuridique=$formejuridique&typeorganisation=$typeorganisation&NAFAPE=$NAFAPE&NACE=$NACE";
           print "<input type=button class=BUTTON value='Suivant -->' onclick=\"open('".$url."','_parent','')\"> ";
	}
}

function precedent($fichier,$table,$limit,$nbaff,$champs,$id) {
	global $cnx;
	global $prefixe;
        $sqlS="SELECT *  FROM ${prefixe}$table WHERE $champs = '$id' ";
        $resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);

        $limit=$limit - $nbaff;

       	if ($limit >= 0) {
             $url="$fichier?limit=$limit&nba=$nbaff&id=$id";
             print "<input type=button class=BUTTON value='<-- Précédent' onclick=\"open('".$url."','_parent','')\"> ";
       	}
}



function precedent4($fichier,$table,$limit,$nbaff,$idClasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	if ( (trim($dateDebut) == "") && (trim($dateFin) == "")) {
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie , creneaux FROM ${prefixe}absences WHERE duree_ab='0' OR motif='inconnu' OR justifier!='1' ORDER BY elev_id, date_ab DESC";
		}else{
			$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, a.justifier, a.heure_saisie, a.creneaux FROM ${prefixe}absences a, ${prefixe}eleves e WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND ( a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ab DESC";
		}		
	}else{
		$dateDebut=dateFormBase($dateDebut);
		$dateFin=dateFormBase($dateFin);
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie , creneaux FROM ${prefixe}absences WHERE (duree_ab='0' OR motif='inconnu' OR justifier!='1') AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' ORDER BY elev_id, date_ab DESC";
		}else{
			$sql="SELECT a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, a.justifier, a.heure_saisie, a.creneaux FROM ${prefixe}absences a, ${prefixe}eleves e WHERE date_ab >= '$dateDebut' AND date_ab <= '$dateFin' AND (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND ( a.duree_ab='0' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ab DESC";
		}		
	}

	$res=execSql($sql);
	$dataS=chargeMat($res);
	$nbElement=count($dataS);
	$limit=$limit - $nbaff;
	if ($limit >= 0) {
             $url="$fichier?limit=$limit&nba=$nbaff&filtre=$idClasse&dateDebut=$dateDebut&dateFin=$dateFin";
             print "<input type=button class=BUTTON value='<-- Précédent' onclick=\"open('".$url."','_parent','')\"> ";
       	}
}


function suivant5($fichier,$table,$limit,$nbaff,$idClasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	if ( (trim($dateDebut) == "") && (trim($dateFin) == "")) {
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie FROM ${prefixe}retards WHERE duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR justifier!='1' ORDER BY elev_id, date_ret DESC,heure_ret DESC ";
		}else{
			$sql="SELECT a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, a.justifier, a.heure_saisie FROM ${prefixe}retards a, ${prefixe}eleves e  WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND (a.duree_ret='0' OR a.duree_ret='???' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ret DESC, a.heure_ret DESC   ";
		}
	}else{
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie FROM ${prefixe}retards WHERE (duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR justifier!='1') AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ORDER BY elev_id, date_ret DESC,heure_ret DESC ";
		}else{
			$sql="SELECT a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, a.justifier, a.heure_saisie FROM ${prefixe}retards a, ${prefixe}eleves e  WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND (a.duree_ret='0' OR a.duree_ret='???' OR a.motif='inconnu' OR a.justifier!='1' ) AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ORDER BY a.elev_id, a.date_ret DESC, a.heure_ret DESC   ";
		}
	}
	$res=execSql($sql);
	$dataS=chargeMat($res);
	$nbElement=count($dataS);
	$limit=$limit + $nbaff;
	if ($limit < $nbElement) {
           $url="$fichier?limit=$limit&nba=$nbaff&filtre=$idClasse&dateDebut=$dateDebut&dateFin=$dateFin";
           print "<input type=button class=BUTTON value='Suivant -->' onclick=\"open('".$url."','_parent','')\"> ";
	}
}



function precedent5($fichier,$table,$limit,$nbaff,$idClasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	if ( (trim($dateDebut) == "") && (trim($dateFin) == "")) {
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie FROM ${prefixe}retards WHERE duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR justifier!='1' ORDER BY elev_id, date_ret DESC,heure_ret DESC  ";
		}else{
			$sql="SELECT a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, a.justifier, a.heure_saisie FROM ${prefixe}retards a, ${prefixe}eleves e  WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND (a.duree_ret='0' OR a.duree_ret='???' OR a.motif='inconnu' OR a.justifier!='1' ) ORDER BY a.elev_id, a.date_ret DESC, a.heure_ret DESC  ";
		}		
	}else{
		if ($idClasse == "tous") {
			$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie FROM ${prefixe}retards WHERE date_ret >= '$dateDebut' AND date_ret <= '$dateFin' AND (duree_ret='0' OR duree_ret='???' OR motif='inconnu' OR justifier!='1') ORDER BY elev_id, date_ret DESC,heure_ret DESC  ";
		}else{
			$sql="SELECT a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, a.justifier, a.heure_saisie FROM ${prefixe}retards a, ${prefixe}eleves e  WHERE (e.elev_id = a.elev_id AND e.classe='$idClasse' ) AND (a.duree_ret='0' OR a.duree_ret='???' OR a.motif='inconnu' OR a.justifier!='1' ) AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ORDER BY a.elev_id, a.date_ret DESC, a.heure_ret DESC  ";
		}		}
	$res=execSql($sql);
	$dataS=chargeMat($res);
	$nbElement=count($dataS);
	$limit=$limit - $nbaff;
	if ($limit >= 0) {
             $url="$fichier?limit=$limit&nba=$nbaff&filtre=$idClasse&dateDebut=$dateDebut&dateFin=$dateFin";
             print "<input type=button class=BUTTON value='<-- Précédent' onclick=\"open('".$url."','_parent','')\"> ";
       	}
}

function precedent2($fichier,$table,$limit,$nbaff) {
	global $cnx;
	global $prefixe;
        $sqlS="SELECT *  FROM ${prefixe}$table";
        $resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);

        $limit=$limit - $nbaff;

       	if ($limit >= 0) {
             $url="$fichier?limit=$limit&nba=$nbaff&id=$id";
             print "<input type=button class=BUTTON value='<-- Précédent' onclick=\"open('".$url."','_parent','')\"> ";
       	}
}

function precedent2VATEL($fichier,$table,$limit,$nbaff) {
	global $cnx;
	global $prefixe;
        $sqlS="SELECT *  FROM ${prefixe}$table";
        $resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);

        $limit=$limit - $nbaff;

       	if ($limit >= 0) {
             $url="$fichier?limit=$limit&nba=$nbaff&id=$id";
             print "<input type=button class='btn btn-primary btn-sm  vat-btn-footer'  value='< ".LANGPRECE."' onclick=\"open('".$url."','_parent','')\"> ";
       	}
}

function precedent_entr($fichier,$table,$limit,$nbaff,$champs,$id,$departement,$activite=1,$ville,$secteureconomique,$siren,$formejuridique,$typeorganisation,$NAFAPE,$NACE) {
	global $cnx;
	global $prefixe;
	if ($departement != "") { 
		if (preg_match('/%/', $departement)) {
			$sqlsuite.="AND code_p LIKE '$departement' ";
		}else{
			$sqlsuite.="AND code_p='$departement' ";
		}  
	}
	if ($ville != "") { 
		if (preg_match('/%/', $ville)) {
			$sqlsuite.="AND ville LIKE '$ville' ";
		}else{
			$sqlsuite.="AND ville = '$ville' ";
		}  
	}
	if (($activite != "-1") && ($activite != "inconnu")) { 
		if (preg_match('/%/', $activite)) {
			$sqlsuite.="AND (secteur_ac LIKE '$activite'  OR secteur_ac2 LIKE '$activite' OR secteur_ac3 LIKE '$activite') ";
		}else{
			$sqlsuite.="AND (secteur_ac = '$activite' OR secteur_ac2='$activite' OR secteur_ac3='$activite') ";
		}  
	}
	if ($secteureconomique != "") { 
		if (preg_match('/%/', $secteureconomique)) {
			$sqlsuite.="AND secteureconomique LIKE '$secteureconomique' ";
		}else{
			$sqlsuite.="AND secteureconomique = '$secteureconomique' ";
		}  
	}
	if ($siren != "") { 
		if (preg_match('/%/', $siren)) {
			$sqlsuite.="AND siren LIKE '$siren' ";
		}else{
			$sqlsuite.="AND siren = '$siren' ";
		}  
	}
	if ($formejuridique != "") { 
		if (preg_match('/%/', $formejuridique)) {
			$sqlsuite.="AND formejuridique LIKE '$formejuridique' ";
		}else{
			$sqlsuite.="AND formejuridique = '$formejuridique' ";
		}  
	}
	if ($typeorganisation != "") { 
		if (preg_match('/%/', $typeorganisation)) {
			$sqlsuite.="AND typeorganisation LIKE '$typeorganisation' ";
		}else{
			$sqlsuite.="AND typeorganisation = '$typeorganisation' ";
		}  
	}
	if ($NAFAPE != "") { 
		if (preg_match('/%/', $NAFAPE)) {
			$sqlsuite.="AND NAFAPE LIKE '$NAFAPE' ";
		}else{
			$sqlsuite.="AND NAFAPE = '$NAFAPE' ";
		}  
	}
	if ($NACE != "") { 
		if (preg_match('/%/', $NACE)) {
			$sqlsuite.="AND NACE LIKE '$NACE' ";
		}else{
			$sqlsuite.="AND NACE = '$NACE' ";
		}  
	}


	if ($sqlsuite != "") { 
		$sqlsuite="WHERE$sqlsuite "; 
		$sqlsuite=preg_replace('/WHEREAND/',' WHERE ',$sqlsuite);
	}

	$sqlS="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation FROM ${prefixe}stage_entreprise   $sqlsuite ORDER BY 2 ";

        $resS=execSql($sqlS);
        $dataS=chargeMat($resS);
        $nbElement=count($dataS);

        $limit=$limit - $nbaff;

       	if ($limit >= 0) {
             $url="$fichier?limit=$limit&nba=$nbaff&id=$id&departement=$departement&ville=$ville&secteureconomique=$secteureconomique&siren=$siren&formejuridique=$formejuridique&typeorganisation=$typeorganisation&NAFAPE=$NAFAPE&NACE=$NACE";
             print "<input type=button class=BUTTON value='<-- Précédent' onclick=\"open('".$url."','_parent','')\"> ";
       	}
}
// ----------------------------------------- //
// ----------------------------------------- //

function  profPmed($dateDebut,$commentaire,$nomProf,$idEleve) {
	global $cnx;
	global $prefixe;
        $commentaire=nl2br($commentaire);
        $commentaire=strip_tags($commentaire,"<BR>");
	$dateDebut=dateFormBase($dateDebut);
	$sql="INSERT INTO ${prefixe}fiche_med(date,ideleve,commentaire,nomProf) VALUES ('$dateDebut','$idEleve','$commentaire','$nomProf')";
	return(execSql($sql));
}
function profPmedAff($idEleve) {
	global $cnx;
	global $prefixe;
	if (strtolower($idEleve) == "all") { 
		$sql="SELECT id,date,ideleve,nomProf,commentaire  FROM ${prefixe}fiche_med ORDER BY ideleve";
	}else{
		$sql="SELECT id,date,ideleve,nomProf,commentaire  FROM ${prefixe}fiche_med WHERE ideleve='$idEleve' ";	
	}
	$res=execSql($sql);
	$data=chargeMat($res);
        unset($sql);
	return $data;
}
function profPmedsupp($idsupp){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}fiche_med  WHERE id='$idsupp' ";
	return(execSql($sql));
}
function purgeprofPmedsupp(){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}fiche_med   ";
	return(execSql($sql));
}

function suppInfoMedic($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}fiche_med  WHERE ideleve='$idEleve' ";
	return(execSql($sql));
}

function purge_brevetCollege(){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}brevetnote   ";
	return(execSql($sql));
}
//---------------------------------------------- //
// ----------------------------------------- //
function  profPinfo($dateDebut,$dateFin,$commentaire,$nomProf,$idEleve) {
	global $cnx;
	global $prefixe;
        $commentaire=nl2br($commentaire);
        $commentaire=strip_tags($commentaire,"<BR>");
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="INSERT INTO ${prefixe}fiche_info(dateDebut,dateFin,idEleve,commentaire,nomProf) VALUES ('$dateDebut','$dateFin','$idEleve','$commentaire','$nomProf')";
	return(execSql($sql));
}
function profPinfoAff($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM ${prefixe}fiche_info WHERE idEleve='$idEleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
        unset($sql);
	return $data;
}


function profPsupp($idsupp){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}fiche_info  WHERE id='$idsupp' ";
	return(execSql($sql));
}


function suppInfo($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}fiche_info  WHERE idEleve='$idEleve' ";
	return(execSql($sql));
}

function purgeprofPsupp(){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}fiche_info  ";
	return(execSql($sql));
}
//---------------------------------------------- //
function couperchaine($chaine,$long) {
	if (strlen($chaine) > $long) {
		$chaine=substr($chaine,0,$long);
		return $chaine."...";
	}else{
		return $chaine;
	}
}
//-----------------------------------------------//

function delete_delegue($idClasse) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}delegue  WHERE idclasse='$idClasse' ";
	return(execSql($sql));
}
function purge_delete_delegue() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}delegue";
	return(execSql($sql));
}

function purgeComBulletin() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_direction_com";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_prof_com";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_profp_com";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}bulletin_profp_ue";
	execSql($sql);
}

function delete_prof_com($idpers) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_prof_param WHERE  idprof='$idpers'  ";
	execSql($sql);
}

function purge_prof_com_bull() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_prof_param";
	execSql($sql);
}

//$idparent1,$idparent2,$ideleve1,$ideleve2,$idClasse
function create_delegue($idparent1,$idparent2,$ideleve1,$ideleve2,$idClasse) {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}delegue(idclasse,nomparent1,nomparent2,eleve1,eleve2) VALUES ('$idClasse','$idparent1','$idparent2','$ideleve1','$ideleve2')";
	return(execSql($sql));

}

function aff_delegue($idClasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,nomparent1,nomparent2,eleve1,eleve2  FROM  ${prefixe}delegue WHERE idclasse='$idClasse'";
	$res=execSql($sql);
	$data=chargeMat($res);
        unset($sql);
	return $data;
}

function aff_delegueTous() {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,nomparent1,nomparent2,eleve1,eleve2  FROM  ${prefixe}delegue ORDER BY idclasse ";
	$res=execSql($sql);
	$data=chargeMat($res);
        unset($sql);
	return $data;
}

function delegue($ideleve,$idclasse,$typedelegue) {
	global $cnx;
	global $prefixe;
	if ($typedelegue == "eleve") {
		$sql="SELECT idclasse,nomparent1,nomparent2,eleve1,eleve2  FROM  ${prefixe}delegue WHERE idclasse='$idclasse' AND ( eleve1='$ideleve' OR eleve2='$ideleve')";
	}else{
		$sql="SELECT idclasse,nomparent1,nomparent2,eleve1,eleve2  FROM  ${prefixe}delegue WHERE idclasse='$idclasse' AND ( nomparent1='$ideleve' OR nomparent2='$ideleve')";
	}
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return "(".LANGDELEGUE1.") ";
	}

}

//---------------------------------------------//
function news_prof_p($commentaire,$idclasse,$idpers) {
	global $cnx;
	global $prefixe;
 	$date=dateDMY2();
	$sql="INSERT INTO ${prefixe}news_prof_p(idclasse,commentaire,date_saisie,idprof) VALUES ('$idclasse','$commentaire','$date','$idpers')";
	return(execSql($sql));
}

function aff_news_prof_p($idclasse,$idprof) {
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM ${prefixe}news_prof_p WHERE idclasse='$idclasse' AND idprof='$idprof' ORDER BY date_saisie DESC";
	$res=execSql($sql);
	$data=chargeMat($res);
	unset($sql);
	return $data;
}

function delete_news_prof_p($idClasse) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}news_prof_p  WHERE id='$idClasse' ";
	return(execSql($sql));
}

function  delete_profp($idprof,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == "ALL"){
		$sql="DELETE FROM ${prefixe}prof_p  WHERE idprof='$idprof'";
	}else{
		$sql="DELETE FROM ${prefixe}prof_p  WHERE idprof='$idprof' AND annee_scolaire='$anneeScolaire' ";
	}
	return(execSql($sql));
}

function delete_profp2($idprof,$idclass,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}prof_p  WHERE idprof='$idprof' AND idclasse='$idclass' AND annee_scolaire='$anneeScolaire' ";
	return(execSql($sql));
}

function  purge_delete_profp() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}prof_p  ";
	return(execSql($sql));
}

function create_profp($idprof,$idclasse,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  idprof,idclasse FROM ${prefixe}prof_p WHERE idprof='$idprof' AND idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return 2;
	}
	$sql="INSERT INTO ${prefixe}prof_p(idprof,idclasse,annee_scolaire) VALUES ('$idprof','$idclasse','$anneeScolaire')";
	return(execSql($sql));
}

function aff_prof_p($anneeScolaire='') {
	global $cnx;
	global $prefixe;
	$sql="SELECT e.idprof,e.idclasse,f.code_class  FROM ${prefixe}prof_p e, ${prefixe}classes f WHERE e.idclasse=f.code_class AND annee_scolaire='$anneeScolaire' ORDER BY f.libelle ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function rechercheprofp($idclasse,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire=='') $anneeScolaire=$_COOKIE["anneeScolaire"]; 
	$sql="SELECT idprof,idclasse   FROM ${prefixe}prof_p WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][0];
}


function rechercheprofpMulti($idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,idclasse   FROM ${prefixe}prof_p WHERE idclasse='$idclasse' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function aff_prof_p_classe($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=anneeScolaire();
	if ($idclasse != '') {
		$sql="SELECT idprof  FROM ${prefixe}prof_p WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		return $data[0][0];
	}
}



function chercheIdClasseDunProfP($idprof) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$idprof' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][1];
}


function verif_compte_cree($nomeleve,$prenomeleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom,prenom FROM ${prefixe}eleves WHERE nom='$nomeleve' AND prenom='$prenomeleve'  ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return 0;
	}else {
		return 1;
	}

}

// destruction de la classe //
function delAffectation($cid,$anneeScolaire){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}affectations WHERE code_classe = '$cid' AND annee_scolaire = '$anneeScolaire' ";
	return(execSql($sql));
}


function validGroup() {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}groupes(group_id,liste_elev,commentaire,libelle) VALUES ('0',NULL,NULL,NULL)";
	$ins=@execSql($sql);
	if (DBTYPE=="mysql") {
		$sql="UPDATE ${prefixe}groupes SET group_id='0', liste_elev=NULL ,commentaire=NULL,libelle=NULL WHERE group_id='1'";
		$ins=@execSql($sql);
	}
	return $ins;
}

function blacklisteenr($nomuser,$prenomuser,$date,$ip,$navig_user,$membre,$fichier) {
	global $cnx;
	global $prefixe;
	$nomuser=trim(strtolower($nomuser));
	$prenomuser=trim(strtolower($prenomuser));
	$sql="SELECT nom,prenom,nb_tentative FROM ${prefixe}blacklist WHERE nom='$nomuser' AND prenom='$prenomuser' AND membre='$membre' ";
	$res=@execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
	     $nbnew=$data[0][2]+1;
	  	 $sql="UPDATE ${prefixe}blacklist SET date='$date', nb_tentative='$nbnew' , ip='$ip', navigateur='$navig_user' WHERE nom='$nomuser' AND prenom='$prenomuser' AND membre='$membre' ";
	     	acceslog("BlackList $nomuser,$prenomuser,$date,$ip,$navig_user,$membre,$fichier");
	}else{
		 $sql="INSERT INTO ${prefixe}blacklist(nom,prenom,date,nb_tentative,ip,navigateur,membre,cause) VALUES ('$nomuser','$prenomuser','$date','1','$ip','$navig_user','$membre','$fichier')";
		acceslog("BlackList $nomuser,$prenomuser,$date,$ip,$navig_user,$membre,$fichier");
	}
	$ins=execSql($sql);
	if ($ins) {
		if (MAILBLACKLIST == "oui") { mailAdmin("Un utilisateur est BlackListé"); }
	}
	return $ins;
 }

function verifblacklist($nomuser,$prenomuser,$membre){
	global $cnx;
	global $prefixe;
	if (strtolower($membre) == "administrateur") { 	$membre="menuadmin"; } 
	if (strtolower($membre) == "parent") { 		$membre="menuparent"; } 
	if (strtolower($membre) == "eleve") { 		$membre="menueleve"; } 
	if (strtolower($membre) == "vie scolaire") { 	$membre="menuscolaire"; } 
	if (strtolower($membre) == "enseignant") { 	$membre="menuprof"; } 
	if (strtolower($membre) == "tuteur stage") { 	$membre="menututeur"; } 
	if (strtolower($membre) == "personnel") { 	$membre="menupersonnel"; } 
	$sql="SELECT nom,prenom,membre FROM ${prefixe}blacklist WHERE nom='$nomuser' AND prenom='$prenomuser' AND membre='$membre' ";
	$res=@execSql($sql);
	$data=chargeMat($res);
	return $data;
}
//-------------------------------------------------------------------------------------------------------------------//
function stage_ajout($num,$debutdate,$findate,$idclasse,$nomstage,$jourdesemaine) {
	global $cnx;
	global $prefixe;
	$debutdate=dateFormBase($debutdate);
	$findate=dateFormBase($findate);
	if ($jourdesemaine != "") { 
		foreach($jourdesemaine as $key=>$value){
			$listejour.=$value.",";
		}
	}
	$listejour=preg_replace('/,$/','',$listejour);
	$sql="INSERT INTO ${prefixe}stage_date(idclasse,datedebut,datefin,numstage,nom_stage,jourdesemaine) VALUES ('$idclasse','$debutdate','$findate','$num','$nomstage','$listejour')";
	return(execSql($sql));
}


function activite_ajout($activite) {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}stage_activite(libelle) VALUES ('$activite')";
	return(execSql($sql));
}

function activite_liste() {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle FROM ${prefixe}stage_activite ORDER BY 1";
	$res=@execSql($sql);
	$data=chargeMat($res);
    	return $data;
}



function stage_modif($id,$num,$debutdate,$findate,$idclasse,$nomStage,$jourdesemaine){
	global $cnx;
	global $prefixe;
	$debutdate=dateFormBase($debutdate);
	$findate=dateFormBase($findate);
	if ($jourdesemaine != "") { 
		foreach($jourdesemaine as $key=>$value){
			$listejour.=$value.",";
		}
	}
	$listejour=preg_replace('/,$/','',$listejour);
	$sql="UPDATE ${prefixe}stage_date SET idclasse='$idclasse', jourdesemaine='$listejour' , datedebut='$debutdate', datefin='$findate' , numstage='$num', nom_stage='$nomStage' WHERE id='$id'";
	return(execSql($sql));
}

function listestagenum() {
	global $cnx;
	global $prefixe;
	$sql="SELECT numstage,nom_stage FROM ${prefixe}stage_date ORDER BY numstage ";
	$res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function select_stage_nom($idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,nom_stage,idclasse,datedebut,datefin  FROM ${prefixe}stage_date  WHERE idclasse = '$idclasse' ORDER BY nom_stage ";
    	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++)
	{
		$dateDebut=dateForm($data[$i][3]);
		$dateFin=dateForm($data[$i][4]);
    		print "<option id='select1' value='".$data[$i][0]."' >".ucwords($data[$i][1])." ($dateDebut - $dateFin)  </option>\n";
	}
}

function enregistrement_demande_stage($idstage,$societe,$message,$idpers) {
	global $cnx;
	global $prefixe;
	$dateenvoi=dateDMY2();
	$sql="INSERT INTO ${prefixe}stage_convention(date_demande,date_retour,idpers,idstage,message,societe,etat,date_envoi) VALUES ('$dateenvoi','0000-00-00','$idpers','$idstage','$message','$societe','0','0000-00-00')";
	return(execSql($sql));

}

function listingDemandeStage($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,date_envoi,date_demande,idstage,message,societe,etat,date_retour FROM ${prefixe}stage_convention  WHERE idpers = '$idpers' ORDER BY date_envoi DESC ";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function listingDemandeStageDir($filtre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,date_demande,date_retour,idstage,message,societe,etat,idpers,date_envoi FROM ${prefixe}stage_convention WHERE etat='$filtre' ORDER BY date_envoi DESC ";
	$data=ChargeMat(execSql($sql));
	return($data);

}

function updateDemandeStageDir($id,$etat) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$sql="UPDATE ${prefixe}stage_convention SET etat='$etat',date_envoi='$date' WHERE id='$id'";
	return(execSql($sql));
}

function listestageclasse() {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse FROM ${prefixe}stage_date  ORDER BY idclasse ";
	$res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function listestageclassenum($num,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,datedebut,datefin,numstage FROM ${prefixe}stage_date  WHERE numstage='$num' AND idclasse='$idclasse'";
	$res=@execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0) {
		$data=dateForm($data[0][1])." <br>au<br> ".dateForm($data[0][2]);
	}else {
		$data="";
	}
        return $data;


}

function select_stage($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,numstage,idclasse  FROM ${prefixe}stage_date  WHERE idclasse = '$id' ORDER BY numstage ";
    	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++)
    	{
    		print "<option id='select1' value='".$data[$i][0]."' >".ucwords($data[$i][1])."</option>\n";
	}
}


function checkbox_stage($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,numstage,idclasse,nom_stage,datedebut,datefin  FROM ${prefixe}stage_date  WHERE idclasse = '$id' ORDER BY numstage ";
    	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++)
	{
		$datedebut=dateForm($data[$i][4]);
		$datefin=dateForm($data[$i][5]);
		list($nom_stage,$null)=preg_split('/\|/',$data[$i][3]);
		$date=listestageclassenum($data[$i][1],$id);
		$date=preg_replace("/<br>/"," ",$date);
		print "<input type='checkbox' onclick=\"document.formulaire.create.disabled=false;document.getElementById('alerte').style.display='none';document.getElementById('date1').value='$datedebut';document.getElementById('date2').value='$datefin';\" name=idstage[] value='".$data[$i][0]."' id='idstage$i' > N° ".ucwords($data[$i][1]);
		print " ".$nom_stage."<br />&nbsp;&nbsp;&nbsp;<font class='T1'><i>($date)</i></font>";
		print "<br>";
	}
	print "<input type='hidden' name='nbidstage' id='nbstage' value='".count($data)."'>";
}

function checkbox_stage_multi($id,$j) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,numstage,idclasse,nom_stage  FROM ${prefixe}stage_date  WHERE idclasse = '$id' ORDER BY numstage ";
    	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
		list($nom_stage,$null)=preg_split('/\|/',$data[$i][3]);
		$date=listestageclassenum($data[$i][1],$id);
		$date=preg_replace('/<br>/'," ",$date);
		print "<input type='checkbox' name=idstage_${i}[] value='".$data[$i][0]."' > N° ".ucwords($data[$i][1]);
		print " ".$nom_stage."<br />&nbsp;&nbsp;&nbsp;<font class='T1'><i>($date)</i></font>";
		print "<br>";
	}
	print "<input type='hidden' name='nbidstage' id='nbstage' value='".count($data)."' />";
}

function select_stage_multi2($id,$j,$num_stage) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,numstage,idclasse,nom_stage  FROM ${prefixe}stage_date  WHERE idclasse = '$id' ORDER BY numstage ";
	$data=ChargeMat(execSql($sql));
	print "<select  name='idstage_$j' id='idstage_$j' >";
	print "<option  value='' id='select0' >".LANGCHOIX."</option>";
	for($i=0;$i<count($data);$i++) {
		list($nom_stage,$null)=preg_split('/\|/',$data[$i][3]);
		$date=listestageclassenum($data[$i][1],$id);
		$date=preg_replace("/\<br\>/"," ",$date);
		$selected="";
		if ($num_stage == $data[$i][0]) $selected="selected='selected'";
		print "<option  value='".$data[$i][0]."' id='select1' $selected > N° ".ucwords($data[$i][1]);
		print " ".$nom_stage."($date)</option>";
		print "<br>";
	}
	print "</select>";
}

function select_stage_multi2VATEL($id,$j,$num_stage) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,numstage,idclasse,nom_stage  FROM ${prefixe}stage_date  WHERE idclasse = '$id' ORDER BY numstage ";
	$data=ChargeMat(execSql($sql));
	print "<select  name='idstage_$j' id='idstage_$j'  style=\"padding:0px;height:initial;\"  >";
	print "<option  value='' id='select0' >".LANGCHOIX."</option>";
	for($i=0;$i<count($data);$i++) {
		list($nom_stage,$null)=preg_split('/\|/',$data[$i][3]);
		$date=listestageclassenum($data[$i][1],$id);
		$date=preg_replace("/\<br\>/"," ",$date);
		$selected="";
		if ($num_stage == $data[$i][0]) $selected="selected='selected'";
		print "<option  value='".$data[$i][0]."' id='select1' $selected > N° ".ucwords($data[$i][1]);
		print " ".$nom_stage."($date)</option>";
		print "<br>";
	}
	print "</select>";
}


function radiobox_stage($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,numstage,idclasse  FROM ${prefixe}stage_date  WHERE idclasse = '$id' ORDER BY numstage ";
    	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++)
	{
		$date=listestageclassenum($data[$i][1],$id);
		$date=preg_replace("/\<br\>/"," ",$date);
		print "<input type='radio' name='idstage' value='".$data[$i][0]."' id='idstage$i' > N° ".ucwords($data[$i][1]);
		print " <i>($date)</i>";
		print "<br>";
	}
}

function listestage() {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,datedebut,datefin,numstage,id FROM ${prefixe}stage_date  ORDER BY idclasse,numstage ";
	$res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function listestageTri($tri) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,datedebut,datefin,numstage,id FROM ${prefixe}stage_date  ORDER BY $tri ";
	$res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function recherchedatestage($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,datedebut,datefin,numstage,id,nom_stage,jourdesemaine FROM ${prefixe}stage_date  WHERE id='$id' ";
	$res=@execSql($sql);
      	$data=chargeMat($res);
      	return $data;
}


function rechercheiddatestagebyAll($idclasse,$datedebut,$datefin,$numstage,$nom_stage) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id FROM ${prefixe}stage_date  WHERE idclasse='$idclasse' AND datedebut='$datedebut' AND datefin='$datefin' AND numstage='$numstage' AND nom_stage='$nom_stage'";
	$res=@execSql($sql);
      	$data=chargeMat($res);
      	return($data[0][0]);
}

function verifSiAjoutStage($idclasse,$datedebut,$datefin,$numstage,$nom_stage) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id FROM ${prefixe}stage_date  WHERE idclasse='$idclasse' AND datedebut='$datedebut' AND datefin='$datefin' AND numstage='$numstage' AND nom_stage='$nom_stage'";
	$res=@execSql($sql);
      	$data=chargeMat($res);
      	return(count($data));
}

function enrContreRendu($identreprise,$ideleve,$idstage,$contrerendu,$heurevisite,$datevisite,$nom,$prenom,$ficmd5,$fichier,$idprof) {
	global $cnx;
	global $prefixe;
	$datevisite=dateFormBase($datevisite);
	$date=dateDMY2();
	$sql="INSERT INTO ${prefixe}stage_contrerendu(ideleve,idstage,dateVisite,heureVisite,identreprise,contrerendu,visiteur,datesaisie,fichier_md5,fichier_name,id_prof_visite) VALUES ('$ideleve','$idstage','$datevisite','$heurevisite','$identreprise','$contrerendu','$nom $prenom','$date','$ficmd5','$fichier','$idprof')";
	return(execSql($sql));

}

function modifContreRendu($identreprise,$ideleve,$idstage,$contrerendu,$heurevisite,$datevisite,$nom,$prenom,$idcontrerendu,$ficmd5,$fichier,$idprof) {
	global $cnx;
	global $prefixe;
	$datevisite=dateFormBase($datevisite);
	$date=dateDMY2();
	$sql="UPDATE ${prefixe}stage_contrerendu SET contrerendu='$contrerendu', datesaisie='$date', dateVisite='$datevisite', heureVisite='$heurevisite', visiteur='$nom $prenom', fichier_md5='$ficmd5' ,fichier_name='$fichier' , id_prof_visite='$idprof' WHERE id='$idcontrerendu' AND ideleve='$ideleve' AND identreprise='$identreprise' AND idstage='$idstage'";
	return(execSql($sql));
}

function SuppFichierContreRenduStage($fichiermd5) {
	global $cnx;
	global $prefixe;
	$datevisite=dateFormBase($datevisite);
	$date=dateDMY2();
	$sql="UPDATE ${prefixe}stage_contrerendu SET  fichier_md5='', fichier_name='' WHERE fichier_md5='$fichiermd5'";
	return(execSql($sql));
}

function listingContreRenduStage($ideleve,$identreprise) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idstage,dateVisite,heureVisite,identreprise,contrerendu,visiteur,datesaisie FROM ${prefixe}stage_contrerendu  WHERE ideleve='$ideleve' AND identreprise='$identreprise' ";
	$res=@execSql($sql);
     	$data=chargeMat($res);
      	return $data;
}


function InfoContreRenduStage($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idstage,dateVisite,heureVisite,identreprise,contrerendu,visiteur,datesaisie,fichier_md5,fichier_name,id_prof_visite FROM ${prefixe}stage_contrerendu  WHERE id='$id'";
	$res=@execSql($sql);
     	$data=chargeMat($res);
      	return $data;
}

function recherchedatestage3($num,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,datedebut,datefin,numstage,id,nom_stage FROM ${prefixe}stage_date  WHERE id='$num' AND idclasse='$idclasse' ";
	$res=@execSql($sql);
      	$data=chargeMat($res);
	return $data;
}

function recherchedatestage2($num,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,datedebut,datefin,numstage,id,nom_stage FROM ${prefixe}stage_date  WHERE id='$num' AND idclasse='$idclasse' ";
	$res=@execSql($sql);
      	$data=chargeMat($res);
	$text="&nbsp;".dateForm($data[0][1])."&nbsp;au&nbsp;".dateForm($data[0][2]);
	return $text;
}

function rechercheNumStage($num) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,datedebut,datefin,numstage,id,nom_stage FROM ${prefixe}stage_date  WHERE id='$num' ";
	$res=@execSql($sql);
      $data=chargeMat($res);
	return $data[0][3];
}


function chercheNomStage($num_stage) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom_stage FROM ${prefixe}stage_date  WHERE numstage='$num_stage' ";
	$res=@execSql($sql);
	$data=chargeMat($res);
	if (($data[0][0] != "") || ($data[0][0] != NULL)) {
		return $data[0][0];	
	}else{
		return "";
	}
	
}


function chercheNomStageviaId($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom_stage FROM ${prefixe}stage_date  WHERE id='$id' ";
	$res=@execSql($sql);
	$data=chargeMat($res);
	if (($data[0][0] != "") || ($data[0][0] != NULL)) {
		return $data[0][0];	
	}else{
		return "";
	}
	
}

function stage_date_supp($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_eleve,num_stage FROM ${prefixe}stage_eleve WHERE num_stage='$id' ";
	$res=@execSql($sql);
	$data=chargeMat($res);

     	if (count($data) > 0) {
		return -1;
	}else{
		$sql="DELETE FROM ${prefixe}stage_date  WHERE id='$id'";
		return(execSql($sql));
	}
}


function stage_date_supp_tous() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id FROM ${prefixe}stage_date";
	$res=@execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$id=$data[$i][0];
		$sql="SELECT id_eleve,num_stage FROM ${prefixe}stage_eleve WHERE num_stage='$id'";
		$res=@execSql($sql);
		$data2=chargeMat($res);
	     	if (count($data2) > 0) { continue; }
		$sql="DELETE FROM ${prefixe}stage_date  WHERE id='$id'";
		execSql($sql);	
	}
}




function create_entreprise($nomentreprise,$contact,$adressesiege,$codepostal,$ville,$activite,$activiteprin,$tel,$fax,$email,$information,$activite2,$activite3,$fonction,$nbchambre,$siteweb,$grphotelier,$nbetoile,$pays,$registrecommerce,$siren,$siret,$formejuridique,$secteureconomique,$INSEE,$NAFAPE,$NACE,$typeorganisation,$qualite) {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}stage_entreprise(nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,secteur_ac2,secteur_ac3,contact_fonction,nbchambre,siteweb,grphotelier,nbetoile,pays_ent,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation,qualite) VALUES ('$nomentreprise','$contact','$adressesiege','$codepostal','$ville','$activite','$activiteprin','$tel','$fax','$email','$information','$activite2','$activite3','$fonction','$nbchambre','$siteweb','$grphotelier','$nbetoile','$pays','$registrecommerce','$siren','$siret','$formejuridique','$secteureconomique','$INSEE','$NAFAPE','$NACE','$typeorganisation','$qualite')";
	return(execSql($sql));
}

function create_entreprise_via_cs($nomentreprise,$contact,$adressesiege,$codepostal,$ville,$activite,$activiteprin,$tel,$fax,$email,$information,$activite2,$activite3,$fonction,$nbchambre,$siteweb,$grphotelier,$nbetoile,$pays,$registrecommerce,$siren,$siret,$formejuridique,$secteureconomique,$INSEE,$NAFAPE,$NACE,$typeorganisation,$idcs) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idcs FROM ${prefixe}stage_entreprise WHERE idcs='$idcs'";
		history_cmd($_SESSION["nom"],"SQL","$sql");
	$res=@execSql($sql);
	$data2=chargeMat($res);
	if (count($data2)) {
		// rien
	}else{
		$formejuridique=preg_replace('/Choix.../i','',$formejuridique);
		$secteureconomique=preg_replace('/Choix.../i','',$secteureconomique);
		$INSEE=preg_replace('/Choix.../i','',$INSEE);
		$sql="INSERT INTO ${prefixe}stage_entreprise(nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,secteur_ac2,secteur_ac3,contact_fonction,nbchambre,siteweb,grphotelier,nbetoile,pays_ent,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation,idcs) VALUES ('$nomentreprise','$contact','$adressesiege','$codepostal','$ville','$activite','$activiteprin','$tel','$fax','$email','$information','$activite2','$activite3','$fonction','$nbchambre','$siteweb','$grphotelier','$nbetoile','$pays','$registrecommerce','$siren','$siret','$formejuridique','$secteureconomique','$INSEE','$NAFAPE','$NACE','$typeorganisation','$idcs')";
		execSql($sql);
	}
	$sql="SELECT id_serial FROM ${prefixe}stage_entreprise WHERE idcs='$idcs'";
	$res=@execSql($sql);
	$data2=chargeMat($res);
	return($data2[0][0]);
}

function modif_entreprise($id,$nomentreprise,$contact,$adressesiege,$codepostal,$ville,$activite,$activiteprin,$tel,$fax,$email,$information,$fonction,$pays,$nbchambre,$siteweb,$grphotelier,$nbetoile,$registrecommerce,$siren,$siret,$formejuridique,$secteureconomique,$INSEE,$NAFAPE,$NACE,$typeorganisation,$qualite) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}stage_entreprise SET nom='$nomentreprise', contact='$contact',adresse='$adressesiege',code_p='$codepostal',ville='$ville',secteur_ac='$activite',activite_prin='$activiteprin',tel='$tel',fax='$fax',email='$email',info_plus='$information',contact_fonction='$fonction',pays_ent='$pays',nbchambre='$nbchambre' ,siteweb='$siteweb' ,grphotelier='$grphotelier' , nbetoile='$nbetoile',registrecommerce='$registrecommerce',siren='$siren',siret='$siret',formejuridique='$formejuridique',secteureconomique='$secteureconomique',INSEE='$INSEE',NAFAPE='$NAFAPE',NACE='$NACE',typeorganisation='$typeorganisation' , qualite='$qualite' WHERE id_serial='$id'";
	return(execSql($sql));
}

function recherche_activite_limit($activite,$offset,$limit,$departement,$ville,$secteureconomique,$siren,$formejuridique,$typeorganisation,$NAFAPE,$NACE) {
	global $cnx;
	global $prefixe;

	// registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation
	if (($departement != "") && ($departement != 'tous')){ 
		if (preg_match('/%/', $departement)) {
			$sqlsuite.="AND code_p LIKE '$departement' ";
		}else{
			$sqlsuite.="AND code_p='$departement' ";
		}  
	}
	if ($ville != "") { 
		if (preg_match('/%/', $ville)) {
			$sqlsuite.="AND ville LIKE '$ville' ";
		}else{
			$sqlsuite.="AND ville = '$ville' ";
		}  
	}
	if (($activite != "-1") && ($activite != "inconnu")) { 
		if (preg_match('/%/', $activite)) {
			$sqlsuite.="AND (secteur_ac LIKE '$activite'  OR secteur_ac2 LIKE '$activite' OR secteur_ac3 LIKE '$activite') ";
		}else{
			$sqlsuite.="AND (secteur_ac = '$activite' OR secteur_ac2='$activite' OR secteur_ac3='$activite') ";
		}  
	}
	if ($secteureconomique != "") { 
		if (preg_match('/%/', $secteureconomique)) {
			$sqlsuite.="AND secteureconomique LIKE '$secteureconomique' ";
		}else{
			$sqlsuite.="AND secteureconomique = '$secteureconomique' ";
		}  
	}
	if ($siren != "") { 
		if (preg_match('/%/', $siren)) {
			$sqlsuite.="AND siren LIKE '$siren' ";
		}else{
			$sqlsuite.="AND siren = '$siren' ";
		}  
	}
	if ($formejuridique != "") { 
		if (preg_match('/%/', $formejuridique)) {
			$sqlsuite.="AND formejuridique LIKE '$formejuridique' ";
		}else{
			$sqlsuite.="AND formejuridique = '$formejuridique' ";
		}  
	}
	if ($typeorganisation != "") { 
		if (preg_match('/%/', $typeorganisation)) {
			$sqlsuite.="AND typeorganisation LIKE '$typeorganisation' ";
		}else{
			$sqlsuite.="AND typeorganisation = '$typeorganisation' ";
		}  
	}
	if ($NAFAPE != "") { 
		if (preg_match('/%/', $NAFAPE)) {
			$sqlsuite.="AND NAFAPE LIKE '$NAFAPE' ";
		}else{
			$sqlsuite.="AND NAFAPE = '$NAFAPE' ";
		}  
	}
	if ($NACE != "") { 
		if (preg_match('/%/', $NACE)) {
			$sqlsuite.="AND NACE LIKE '$NACE' ";
		}else{
			$sqlsuite.="AND NACE = '$NACE' ";
		}  
	}


	if ($sqlsuite != "") { 
		$sqlsuite="WHERE$sqlsuite "; 
		$sqlsuite=preg_replace('/WHEREAND/',' WHERE ',$sqlsuite);
	}
	$sql="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation FROM ${prefixe}stage_entreprise   $sqlsuite ORDER BY 2 LIMIT $offset,$limit ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function recherche_activite_nb($activite,$offset,$limit,$departement,$ville,$secteureconomique,$siren,$formejuridique,$typeorganisation,$NAFAPE,$NACE) {
	global $cnx;
	global $prefixe;
	if (trim($departement) != "") { 
		if (preg_match('/%/', $departement)) {
			$sqlsuite.="AND code_p LIKE '$departement' ";
		}else{
			$sqlsuite.="AND code_p='$departement' ";
		}  
	}
	if ($ville != "") { 
		if (preg_match('/%/', $ville)) {
			$sqlsuite.="AND ville LIKE '$ville' ";
		}else{
			$sqlsuite.="AND ville = '$ville' ";
		}  
	}
	if (($activite != "-1") && ($activite != "inconnu")) { 
		if (preg_match('/%/', $activite)) {
			$sqlsuite.="AND (secteur_ac LIKE '$activite'  OR secteur_ac2 LIKE '$activite' OR secteur_ac3 LIKE '$activite') ";
		}else{
			$sqlsuite.="AND (secteur_ac = '$activite' OR secteur_ac2='$activite' OR secteur_ac3='$activite') ";
		}  
	}
	if ($secteureconomique != "") { 
		if (preg_match('/%/', $secteureconomique)) {
			$sqlsuite.="AND secteureconomique LIKE '$secteureconomique' ";
		}else{
			$sqlsuite.="AND secteureconomique = '$secteureconomique' ";
		}  
	}
	if ($siren != "") { 
		if (preg_match('/%/', $siren)) {
			$sqlsuite.="AND siren LIKE '$siren' ";
		}else{
			$sqlsuite.="AND siren = '$siren' ";
		}  
	}
	if ($formejuridique != "") { 
		if (preg_match('/%/', $formejuridique)) {
			$sqlsuite.="AND formejuridique LIKE '$formejuridique' ";
		}else{
			$sqlsuite.="AND formejuridique = '$formejuridique' ";
		}  
	}
	if ($typeorganisation != "") { 
		if (preg_match('/%/', $typeorganisation)) {
			$sqlsuite.="AND typeorganisation LIKE '$typeorganisation' ";
		}else{
			$sqlsuite.="AND typeorganisation = '$typeorganisation' ";
		}  
	}
	if ($NAFAPE != "") { 
		if (preg_match('/%/', $NAFAPE)) {
			$sqlsuite.="AND NAFAPE LIKE '$NAFAPE' ";
		}else{
			$sqlsuite.="AND NAFAPE = '$NAFAPE' ";
		}  
	}
	if ($NACE != "") { 
		if (preg_match('/%/', $NACE)) {
			$sqlsuite.="AND NACE LIKE '$NACE' ";
		}else{
			$sqlsuite.="AND NACE = '$NACE' ";
		}  
	}


	if ($sqlsuite != "") { 
		$sqlsuite="WHERE$sqlsuite "; 
		$sqlsuite=preg_replace('/WHEREAND/',' WHERE ',$sqlsuite);
	} 
		
	$sql="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus FROM ${prefixe}stage_entreprise $sqlsuite";
	
        $res=execSql($sql);
        $data=chargeMat($res);
        return count($data);
}

function recherche_activite($activite) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus FROM ${prefixe}stage_entreprise WHERE secteur_ac='$activite' OR secteur_ac2='$activite' OR secteur_ac3='$activite' ORDER BY 2";
	$res=@execSql($sql);
      $data=chargeMat($res);
      return $data;
}

function recherche_activite_id($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,nbchambre,siteweb,grphotelier,nbetoile,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation  FROM ${prefixe}stage_entreprise WHERE id_serial='$id' ";
	$res=@execSql($sql);
      $data=chargeMat($res);
      return $data;
}

function recherche_entreprise_nom($recherche) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,contact_fonction,pays_ent,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation,qualite FROM ${prefixe}stage_entreprise WHERE nom='$recherche' ";
	$res=@execSql($sql);
    $data=chargeMat($res);
    return $data;
}

function suppContreRenduViaId($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT fichier_md5 FROM ${prefixe}stage_contrerendu WHERE id='$id'";
	$res=@execSql($sql);
	$data=chargeMat($res);
	@unlink("./data/pdf_stage/".$data[0][0]);
	$sql="DELETE FROM ${prefixe}stage_contrerendu WHERE id='$id'";
	return(@execSql($sql));
}

function suppContreRenduViaIdeleve($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}stage_contrerendu WHERE ideleve='$ideleve'";
	@execSql($sql);
}

function suppContreRenduViaIdStage($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}stage_contrerendu WHERE idstage='$id'";
	@execSql($sql);
}

function suppContreRenduViaIdEntreprise($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}stage_contrerendu WHERE identreprise='$id'";
	@execSql($sql);
}

function recupInfoEntreprise($recherche) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,contact_fonction,pays_ent,
		secteur_ac2 ,secteur_ac3 ,contact_fonction ,nbchambre ,siteweb ,grphotelier ,nbetoile ,registrecommerce ,siren ,siret ,formejuridique ,secteureconomique ,
		INSEE ,NAFAPE ,NACE ,typeorganisation 
	
		FROM ${prefixe}stage_entreprise WHERE id_serial='$recherche' ";
	$res=@execSql($sql);
    $data=chargeMat($res);
    return $data;
}


function listingEntreprise() {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,contact_fonction,pays_ent FROM ${prefixe}stage_entreprise ORDER BY nom ";
	$res=@execSql($sql);
    	$data=chargeMat($res);
    	return $data;
}

function verifEntreprise($nomEntreprise) {
	global $cnx;
	global $prefixe;
	$nomEntreprise=strtolower($nomEntreprise);
	$sql="SELECT nom,code_p,ville FROM ${prefixe}stage_entreprise WHERE lower(nom) LIKE '$nomEntreprise%' ";
	$res=@execSql($sql);
    	$data=chargeMat($res);
    	return $data;

}

function recherche_entr_nom_via_id($recherche) {
	global $cnx;
	global $prefixe;
	$recherche=trim($recherche);
	$sql="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus FROM ${prefixe}stage_entreprise WHERE id_serial='$recherche' ";
	$res=@execSql($sql);
      $data=chargeMat($res);
      return $data[0][1];
}

function select_entreprise() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_serial,nom FROM ${prefixe}stage_entreprise ORDER BY nom ";
    	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
    		print "<option id='select1' value='".$data[$i][0]."'>".ucwords($data[$i][1])."</option>\n";
	}
}

function select_entreprise_limit($nb) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_serial,nom FROM ${prefixe}stage_entreprise ORDER BY nom ";
    	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++)
    	{
    		print "<option id='select1' value='".$data[$i][0]."' title=\"".ucwords($data[$i][1])."\" >".ucwords(trunchaine($data[$i][1],$nb))."</option>\n";
	}
}

function select_recherche_entreprise($nb,$idsociete) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_serial,nom FROM ${prefixe}stage_entreprise WHERE id_serial='$idsociete' ";
    	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++)
    	{
    		print "<option id='select0' value='".$data[$i][0]."' title=\"".ucwords($data[$i][1])."\"  >".ucwords(trunchaine($data[$i][1],$nb))."</option>\n";
	}
}


function create_eleve_stage($ideleve,$iddent,$lieu,$ville,$idprof,$date,$loge,$nourri,$xservice,$raison,$info,$iddatestage,$codepostal,$tuteur,$tel,$alternance,$dateDebutAlternance,$dateFinAlternance,$jourstage,$compte_tuteur_stage,$horairedebutjournalier,$horairefinjournalier,$date2,$idprof2,$service,$indemnitestage,$pays,$fax,$responsable2,$langue,$trim,$idtuteur2=0) {
	global $cnx;
	global $prefixe;
	if ($horairedebutjournalier == "hh:mm") { $horairedebutjournalier=""; }
	if ($horairefinjournalier == "hh:mm") { $horairedebutjournalier=""; }
	if ($idprof == "Choix ...") { $idprof="NULL" ; }
	if ($compte_tuteur_stage == "") { $compte_tuteur_stage=0; }
	if ($alternance != "1") { $alternance=0; }
	if ($dateDebutAlternance != "") { $dateDebutAlternance=dateFormBase($dateDebutAlternance); }
	if ($dateFinAlternance != "") { $dateFinAlternance=dateFormBase($dateFinAlternance); }
	if ($date2 != "") { $date2=dateFormBase($date2); }
	if ($date != "") { $date=dateFormBase($date); }
	if ($jourstage != "") { 
		foreach($jourstage as $key=>$value){
			$jourstages.=$value.",";
		}
	}
	$jourstages=preg_replace('/,$/','',$jourstages);

	$sql="INSERT INTO ${prefixe}stage_eleve(id_eleve,id_entreprise,lieu_stage,ville_stage,id_prof_visite,date_visite_prof,loger,nourri,passage_x_service,raison,info_plus,num_stage,code_p,tuteur_stage,tel,compte_tuteur_stage,alternance,jour_alternance,dateDebutAlternance,dateFinAlternance,horairedebutjournalier,horairefinjournalier,date_visite_prof2,id_prof_visite2,service,indemnitestage,pays_stage,fax,autre_responsable,langue,trimestre,compte_tuteur_stage_2) VALUES ('$ideleve','$iddent','$lieu','$ville','$idprof','$date','$loge','$nourri','$xservice','$raison','$info','$iddatestage','$codepostal','$tuteur','$tel','$compte_tuteur_stage','$alternance','$jourstages','$dateDebutAlternance','$dateFinAlternance','$horairedebutjournalier','$horairefinjournalier','$date2','$idprof2','$service','$indemnitestage','$pays_stage','$fax','$responsable2','$langue','$trim','$idtuteur2')";
	execSql($sql);
	
	$sql="SELECT * FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve' AND id_entreprise='$iddent' AND lieu_stage='$lieu' AND ville_stage='$ville' AND num_stage='$iddatestage'";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) {
		return 1;
	}else{
		return 0;
	}

}

function recherche_stage_eleve($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_eleve,id_entreprise,lieu_stage,ville_stage,id_prof_visite,date_visite_prof,loger,nourri,passage_x_service,raison,info_plus,num_stage,code_p,id,tuteur_stage,tel,compte_tuteur_stage,alternance,jour_alternance,dateDebutAlternance,dateFinAlternance FROM ${prefixe}stage_eleve WHERE id_eleve='$id' ORDER BY num_stage";
	$res=@execSql($sql);
      $data=chargeMat($res);
      return $data;
}


function recherche_stage_eleve_entreprise($id_eleve) {
	global $cnx;
	global $prefixe;

	$sql="SELECT e.id_eleve ,e.id_entreprise,f.id_serial,f.nom,f.contact,f.adresse,f.code_p,f.ville,f.secteur_ac,f.activite_prin,f.tel,f.fax,f.email,f.contact_fonction,f.pays_ent,e.num_stage, e.compte_tuteur_stage, e.id_prof_visite, e.date_visite_prof2, e.id_prof_visite2, e.date_visite_prof,f.nbchambre,f.siteweb,f.grphotelier,f.nbetoile,e.loger,e.service,e.indemnitestage, e.pays_stage,e.alternance,e.dateDebutAlternance,e.dateFinAlternance,e.autre_responsable,g.datedebut,g.datefin  FROM ${prefixe}stage_eleve e
		LEFT JOIN ${prefixe}stage_date g  ON g.numstage = e.num_stage 
		LEFT JOIN ${prefixe}stage_entreprise f ON e.id_entreprise = f.id_serial 
		WHERE e.id_eleve='$id_eleve'  ORDER BY f.nom";
	$res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function recherche_stage_eleve_entreprise11($id_eleve,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT e.id_eleve ,e.id_entreprise,f.id_serial,f.nom,f.contact,f.adresse,f.code_p,f.ville,f.secteur_ac,f.activite_prin,f.tel,f.fax,f.email,f.contact_fonction,f.pays_ent,e.num_stage, e.compte_tuteur_stage, e.id_prof_visite, e.date_visite_prof2, e.id_prof_visite2, e.date_visite_prof,f.nbchambre,f.siteweb,f.grphotelier,f.nbetoile,e.loger,e.service,e.indemnitestage,e.pays_stage,e.alternance,e.dateDebutAlternance,e.dateFinAlternance,e.autre_responsable,g.datedebut,g.datefin  
		FROM ${prefixe}stage_eleve e 
		LEFT JOIN ${prefixe}stage_entreprise f 	ON g.numstage = e.num_stage
		LEFT JOIN ${prefixe}stage_date g  	ON  e.id_entreprise = f.id_serial AND g.idclasse='$idclasse'
		WHERE e.id_eleve='$id_eleve' ORDER BY f.nom";
	$res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function recherche_stage_eleve_entreprise2($id_eleve,$idEntreprise) {
	global $cnx;
	global $prefixe;
	$sql="SELECT e.id_eleve ,e.id_entreprise,f.id_serial,f.nom,f.contact,f.adresse,f.code_p,f.ville,f.secteur_ac,f.activite_prin,f.tel,f.fax,f.email,f.contact_fonction,f.pays_ent,e.num_stage, e.tuteur_stage, e.id_prof_visite,g.datedebut,g.datefin,e.alternance,e.dateDebutAlternance,e.dateFinAlternance,e.autre_responsable  
		FROM ${prefixe}stage_eleve e  
		LEFT JOIN ${prefixe}stage_entreprise f  ON e.id_entreprise = f.id_serial AND e.id_entreprise='$idEntreprise'
		LEFT JOIN ${prefixe}stage_date g 	ON g.numstage = e.num_stage 
		WHERE e.id_eleve='$id_eleve' ORDER BY f.nom";
	$res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}



function recherche_stage_eleve_entreprise3($id_eleve,$idEntreprise,$idclasse,$num_stage) {
        global $cnx;
	global $prefixe;
	$sql="SELECT e.id_eleve ,e.id_entreprise,f.id_serial,f.nom,f.contact,f.adresse,f.code_p,f.ville,f.secteur_ac,f.activite_prin,f.tel,f.fax,f.email,f.contact_fonction,f.pays_ent,e.num_stage, e.compte_tuteur_stage, e.id_prof_visite,g.datedebut,g.datefin, g.numstage, e.date_visite_prof2, e.id_prof_visite2, e.date_visite_prof,f.nbchambre,f.siteweb,f.grphotelier,f.nbetoile,e.loger,e.service,e.indemnitestage,e.pays_stage,e.alternance,e.dateDebutAlternance,e.dateFinAlternance,e.autre_responsable,g.datedebut,g.datefin   
		FROM ${prefixe}stage_eleve e 
		LEFT JOIN ${prefixe}stage_entreprise f 	ON e.id_entreprise = f.id_serial AND e.id_entreprise='$idEntreprise'
		LEFT JOIN ${prefixe}stage_date g 	ON g.id = e.num_stage AND g.idclasse='$idclasse'
		WHERE e.id_eleve='$id_eleve' AND e.num_stage='$num_stage' ORDER BY f.nom";
        $res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function update_stage_eleve($id,$idstage,$ident,$lieu,$ville,$postal,$responsable,$idprof,$date,$loge,$nourri,$xservice,$raison,$info,$ideleve,$tel,$idtuteur,$horairedebutjournalier,$horairefinjournalier,$date2,$idprof2,$pays,$service,$indemnitestage) {
	global $cnx;
	global $prefixe;
        if ($idprof == "Choix ...") {
               $idprof="NULL" ;
        }
	if ($date == "") {
               $date="NULL" ;
        }else{
               $date=dateFormBase($date);
	}
	if ($date2 == "") {
               $date2="NULL" ;
        }else{
               $date2=dateFormBase($date2);
	}
	$sql="UPDATE ${prefixe}stage_eleve SET id_eleve='$ideleve', id_entreprise='$ident', lieu_stage='$lieu', ville_stage='$ville', id_prof_visite='$idprof', code_p='$postal', tuteur_stage='$responsable', date_visite_prof='$date', loger='$loge', nourri='$nourri', passage_x_service='$xservice', raison='$raison', info_plus='$info',  num_stage='$idstage', tel='$tel' , compte_tuteur_stage='$idtuteur', horairedebutjournalier='$horairedebutjournalier' , horairefinjournalier='$horairefinjournalier', date_visite_prof2='$date2', id_prof_visite2='$idprof2' , pays_stage='$pays',  service='$service', indemnitestage='$indemnitestage' WHERE id='$id' ";
	$ins=execSql($sql);
}

function recherche_stage_eleve_par_id($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_eleve,id_entreprise,lieu_stage,ville_stage,id_prof_visite,date_visite_prof,loger,nourri,passage_x_service,raison,info_plus,num_stage,code_p,id,tuteur_stage,tel,compte_tuteur_stage,alternance,jour_alternance,dateDebutAlternance,dateFinAlternance,horairedebutjournalier,horairefinjournalier,date_visite_prof2,id_prof_visite2,service,indemnitestage,pays_stage   FROM ${prefixe}stage_eleve WHERE id='$id'";
	$res=@execSql($sql);
      $data=chargeMat($res);
      return $data;
}



function rechercheEntreStageEle($ideleve,$numstage) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_eleve,id_entreprise,num_stage FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve' AND num_stage='$numstage' ";
	$res=@execSql($sql);
      $data=chargeMat($res);
      return $data[0][1] ;
}



function verifProfVisiteur($id_pers,$ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve' AND (id_prof_visite='$id_pers' OR  id_prof_visite2='$id_pers') ";
	$res=@execSql($sql);
      	$data=chargeMat($res);
      	return count($data) ;
}

function supp_stage_eleve($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM  ${prefixe}stage_eleve   WHERE id='$id'";
	return(execSql($sql));
}

function recherche_entreprise_id($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_eleve,id_entreprise,lieu_stage,ville_stage,id_prof_visite,date_visite_prof,loger,nourri,passage_x_service,raison,info_plus,num_stage,code_p,id,tuteur_stage FROM ${prefixe}stage_eleve WHERE id='$id'";
	$res=@execSql($sql);
      $data=chargeMat($res);
      return $data;
}


function recherche_entreprise_id2($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,contact_fonction,pays_ent,nbchambre,siteweb,grphotelier,nbetoile,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation,qualite  FROM ${prefixe}stage_entreprise WHERE id_serial='$id'";
	$res=@execSql($sql);
      	$data=chargeMat($res);
      	return $data;
}

function entreprise_supp($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_eleve,id_entreprise FROM ${prefixe}stage_eleve WHERE id_entreprise='$id' ";
	$res=@execSql($sql);
	$data=chargeMat($res);
     	if (count($data) > 0) {
		return -1;
	}else{
		$sql="DELETE FROM ${prefixe}stage_entreprise WHERE id_serial='$id'";
		$cr=execSql($sql);
		if ($cr) {
			$sql="DELETE FROM ${prefixe}stage_history WHERE identreprise='$id'";
			execSql($sql);
		}
		return($cr);
	}
}


function recherche_stage_historique($ideleve) {
	global $cnx;
	global $prefixe;	 
	$sql="SELECT e.nom,s.nomprenomeleve,s.classeeleve,s.periodestage,s.trimestre,s.langue,s.service,s.identreprise FROM ${prefixe}stage_history s, ${prefixe}stage_entreprise e WHERE e.id_serial=s.identreprise AND s.ideleve='$ideleve' GROUP BY s.periodestage ORDER BY s.classeeleve ";
	$res=@execSql($sql);
	$data=chargeMat($res);
	return($data);
}


function DirPurge($rep) {
  $Array = array(); $dir = opendir($rep);
  $i=0;
  while ($File = readdir($dir)){
  	if($File != "." && $File != ".." && $File != ".triade" && $File != ".htaccess") {
		$fichier="$rep/$File";
      	@unlink("$fichier");
  	}
    	$i++;
  }
  closedir($dir);
}

function DirPurgeForum() {
  $rep="./data/forum";
  $Array = array(); $dir = opendir($rep);
  $i=0;
  while ($File = readdir($dir)){
	$ok=preg_match('/dat$/',$File);
	if ($ok) {
		$fichier="$rep/$File";
      	@unlink("$fichier");
  	}
    	$i++;
  }
  closedir($dir);
}

function DirPurgeLivreor() {
  $rep="./data/livreor";
  $Array = array(); $dir = opendir($rep);
  $i=0;
  while ($File = readdir($dir)){
	$ok=preg_match('/ebs$/',$File);
	if ($ok) {
		$fichier="$rep/$File";
      	@unlink("$fichier");
  	}
    	$i++;
  }
  closedir($dir);
}

function verifkey($code1,$code2,$code3) {
        $code=$code1-$code2-$code3;
        $date=dateYMD();
        $etat=$date - $code;
        print "<br>";
        if ($etat < 9800 ) {
                return "1";
        }else {
                return "0";
        }
}

// module equipement
function create_equip($libelle,$info) {
      global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}resa_matos(libelle,info,type) VALUES ('$libelle','$info','equip')";
	return(execSql($sql));
}
function create_salle($libelle,$info) {
      global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}resa_matos(libelle,info,type) VALUES ('$libelle','$info','salle')";
	return(execSql($sql));
}

function modif_salle($libelle,$info,$id) {
        global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}resa_matos SET libelle='$libelle' , info='$info'  WHERE id='$id' ";
	return(execSql($sql));
}

function modif_equip($libelle,$info,$id) {
        global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}resa_matos SET libelle='$libelle' , info='$info'  WHERE id='$id' ";
	return(execSql($sql));
}

function purgeequip() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}resa_matos ";
	return(execSql($sql));
}

function purgecontrerendustage() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}stage_contrerendu";
	return(execSql($sql));
}

function  purgeresa() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}resa_liste ";
	return(execSql($sql));
}

function affPlanEquip($koi,$date) {
	global $cnx;
	global $prefixe;
	if ($date == "rien") {
		$sql="SELECT  n.id,n.idmatos,n.idqui,n.quand,n.heure_depart,n.heure_fin,n.info,n.valider,m.type,m.id,n.refcommun  FROM  ${prefixe}resa_matos m, ${prefixe}resa_liste n WHERE n.idmatos = m.id AND  m.type='$koi' ORDER BY n.quand, n.heure_depart  ";
	}else{
		$sql="SELECT  n.id,n.idmatos,n.idqui,n.quand,n.heure_depart,n.heure_fin,n.info,n.valider,m.type,m.id,n.refcommun  FROM  ${prefixe}resa_matos m, ${prefixe}resa_liste n WHERE n.idmatos = m.id AND  m.type='$koi' AND n.quand = '$date' ORDER BY n.quand, n.heure_depart ";
	}
	$res=execSql($sql);
    $data=chargeMat($res);
    return $data;
}

function affPlanEquipDetail($koi,$date,$idequip) {
	global $cnx;
	global $prefixe;
	if (($idequip != 'tous') || (empty($idequip)) ) {
		$suitesql=" AND n.idmatos='$idequip' ";
	}
	if ($date == "rien") {
		$sql="SELECT  n.id,n.idmatos,n.idqui,n.quand,n.heure_depart,n.heure_fin,n.info,n.valider,m.type,m.id,n.refcommun  FROM  ${prefixe}resa_matos m, ${prefixe}resa_liste n WHERE n.idmatos = m.id AND  m.type='$koi' $suitesql ORDER BY n.quand, n.heure_depart  ";
	}else{
		$sql="SELECT  n.id,n.idmatos,n.idqui,n.quand,n.heure_depart,n.heure_fin,n.info,n.valider,m.type,m.id,n.refcommun  FROM  ${prefixe}resa_matos m, ${prefixe}resa_liste n WHERE n.idmatos = m.id AND  m.type='$koi' $suitesql AND n.quand = '$date' ORDER BY n.quand, n.heure_depart ";
	}
	$res=execSql($sql);
    $data=chargeMat($res);
    return $data;
}


function affResaEquip($koi,$date1,$date2) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  n.id,n.idmatos,n.idqui,n.quand,n.heure_depart,n.heure_fin,n.info,n.valider,m.type,m.id  FROM  ${prefixe}resa_matos m, ${prefixe}resa_liste n WHERE n.idmatos = m.id AND  m.type='$koi' AND n.quand >= '$date1' AND n.quand <= '$date2' ORDER BY n.quand, n.heure_depart ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data;
}


function valide_equip($valide,$id,$idpers,$servername) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  id,idmatos,idqui,quand,heure_depart,heure_fin,info,valider  FROM  ${prefixe}resa_liste WHERE id='$id'";
    	$res=execSql($sql);
    	$data=chargeMat($res);
	$equip=recherche_equip($data[0][1]);
	$date=dateForm($data[0][3]);
	$heure_depart=timeForm($data[0][4]);
	$heure_fin=timeForm($data[0][5]);
	$info=$data[0][6];
	$destinataire=$data[0][2];
	if ($valide == 1) {
		$text="Bonjour, <br /><br /> Votre réservation de : $equip est accordée.<br><br> ";
		$text.="<i>Informations : le $date du $heure_depart au $heure_fin <br> $info </i>";
		$text.="<br><br>Pour tout renseignement nous contacter. <br><br>";
		$text.="Triadement Votre, ";
            if (DBTYPE == "pgsql") { $valid="valider=TRUE"; }
            if (DBTYPE == "mysql")  { $valid="valider='1'"; }
        	$sql="UPDATE ${prefixe}resa_liste  SET $valid WHERE id='$id' ";
	        execSql($sql);
        }
	if ($valide == 2) {
        $text="Bonjour, <br /><br /> Votre réservation de : $equip n\'est pas accordée.<br><br> ";
		$text.="<i>Informations : le $date du $heure_depart au $heure_fin <br> $info </i>";
		$text.="<br><br>Pour tout renseignement nous contacter. <br><br>";
		$text.="Triadement Votre, ";
		$sql="DELETE FROM ${prefixe}resa_liste WHERE id='$id'";
	    execSql($sql);
	}
	if ((trim($destinataire) != "") && ($valide > 0)){
        $objet="Demande de réservation";
        $date=dateDMY2();
        $heure=dateHIS();
        $type_personne=recherche_type_personne($idpers);
        $type_personne_dest=recherche_type_personne($destinataire);
	if ($type_personne_dest == "ADM") { $membre="menuadmin"; }
        if ($type_personne_dest == "PAR") { $membre="menuparent"; }
        if ($type_personne_dest == "ENS") { $membre="menuprof"; }
        if ($type_personne_dest == "MVS") { $membre="menuscolaire"; }
        if ($type_personne_dest == "TUT") { $membre="menututeur"; }
        if ($type_personne_dest == "PER") { $membre="menupersonnel"; }

	$emetteur=$idpers;
	$number=md5(uniqid(rand()));
	
        envoi_messagerie($idpers,$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number,'',0);
	if (FORWARDMAIL == "oui") {
            $nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
            $prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
            if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre)) {
                  $email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre);
		  $http=protohttps(); // return http:// ou https://
		  $lien="$http".$servername."/";
                  envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,recherche_personne($emetteur),$number,$objet,$destinataire) ;
            }
       }
    }

}


function supp_resa($id,$recursive) {
	global $cnx;
	global $prefixe;
	if ($recursive == "oui") {
		$data=rechercheRefcommun($id);
		$idpers=$data[0][1];
		$refCommun=$data[0][0];
		if (trim($refCommun) != "") {
			$sql="SELECT id FROM ${prefixe}resa_liste WHERE refcommun='$refCommun'";
			$res=execSql($sql);
			$data=chargeMat($res);
			for($i=0;$i<count($data);$i++) {
				$idresaliste=$data[$i][0];
				$sql="DELETE FROM ${prefixe}edt_seances WHERE id_resa_liste='$idresaliste'";
      				execSql($sql);	
			}
			$sql="DELETE FROM ${prefixe}resa_liste WHERE refcommun='$refCommun'";
        		execSql($sql);	
		}
	}
	$sql="DELETE FROM ${prefixe}edt_seances WHERE id_resa_liste='$id'";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}resa_liste WHERE id='$id'";
	execSql($sql);

}

function rechercheRefcommun($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT refcommun,idqui FROM  ${prefixe}resa_liste WHERE id='$id' ";
	$res=execSql($sql);
	$data=chargeMat($res);
    	return $data;
}

function consult_resa() {
	global $cnx;
	global $prefixe;
	if (DBTYPE == "pgsql") {
               $valid=FALSE;
	       $valid="valider=FALSE";
        }
        if (DBTYPE == "mysql")  {
               $valid=0;
        }
	$sql="SELECT valider FROM  ${prefixe}resa_liste WHERE valider='$valid' ";
	$res=execSql($sql);
	$data=chargeMat($res);
    return $data;
}


function consult_resa2($koi) { // $koi = 1 pour salle et 2 pour equip
	global $cnx;
	global $prefixe;
	$koi=($koi == 1) ? "salle" : "equip" ;
	$sql="SELECT r.valider FROM  ${prefixe}resa_liste r, ${prefixe}resa_matos e WHERE r.valider='0' AND e.type='$koi' AND e.id=r.idmatos";
	$res=execSql($sql);
	$data=chargeMat($res);
    	return $data;
}

function verif_utiliser_salle($idsalle) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  idmatos FROM  ${prefixe}resa_liste WHERE idmatos='$idsalle'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data > 0) {
		return 1;
	}else{
		return 0;
	}

}


function affSalle($koi) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  id,libelle,info,type FROM  ${prefixe}resa_matos WHERE type='$koi' ORDER BY libelle";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function rechercheInfoSalleEquipement($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  id,libelle,info,type FROM  ${prefixe}resa_matos WHERE id='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function list_equip_valide($equip) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  a.id,a.idmatos,a.idqui,a.quand,a.heure_depart,a.heure_fin,a.info,a.valider,b.id,b.type FROM  ${prefixe}resa_liste a, ${prefixe}resa_matos b WHERE a.idmatos = b.id AND b.type='$equip' AND  ";
	if (DBTYPE == "pgsql") {
                $sql.="a.valider=FALSE";
        }
        if (DBTYPE == "mysql")  {
                $sql.="a.valider=0";
        }
	$sql.= " ORDER BY a.quand LIMIT 100";
	$res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function recherche_equip($koi) {
	global $cnx;
	global $prefixe;
	if ($koi != "") {
		$sql="SELECT  id,libelle,info,type FROM  ${prefixe}resa_matos WHERE id='$koi'";
		$res=execSql($sql);
		$data=chargeMat($res);
		return $data[0][1];
	}else{
		return "";
	}
}

function suppression_salle($idclasse)  {
	global $cnx;
	global $prefixe;
      $sql="DELETE FROM ${prefixe}resa_matos WHERE id='$idclasse'";
	return(execSql($sql));
}

function select_salle($id='') {
        $data=affSalle('salle');
        for($i=0;$i<count($data);$i++) {
		$idsalle=$data[$i][0];
		$checked="";
		if ($id == $idsalle) $checked="selected='selected'";
        	print "<option $checked id='select1' value='".$idsalle."'>".$data[$i][1]."</option>\n";
        }
}


function select_equip($id='') {
        $data=affSalle('equip');
        for($i=0;$i<count($data);$i++) {
		$idequip=$data[$i][0];
		$checked="";
		if ($idequip == $id) $checked="selected='selected'";
        	print "<option  $checked  id='select1' value='".$idequip."'>".$data[$i][1]."</option>\n";
        }
}



function create_resa($equipement,$date,$idpers,$heure1,$heure2,$info,$confirm=0,$jusquau,$tabJours) {
	global $cnx;
	global $prefixe;
	
	if ($confirm == 1) {
		$valid=1;
	}else{
		$valid=0;
	}
	$duree=dureeEntreDeuxHeure($heure1,$heure2);
	if (trim($jusquau) == "") {
		$date=dateFormBase($date);
		$sql="INSERT INTO ${prefixe}resa_liste(idmatos,idqui,quand,heure_depart,heure_fin,info,valider) VALUES ('$equipement','$idpers','$date','$heure1','$heure2','$info','$valid')";
		$cr=execSql($sql);
		$idResaListe=mysqli_insert_id();
		if ($cr == 1) {
			$CODE=md5(date("H:m:sd/m/Y").rand(1000,9999));
			
			$sql="INSERT INTO ${prefixe}edt_seances (code,enseignement,date,heure,duree,bgcolor) VALUES ('$CODE','$info','$date','$heure1','$duree','#FFFFFF')";
			execSql($sql);
			$sql="SELECT id FROM ${prefixe}edt_seances WHERE code='$CODE'";
			$res=execSql($sql);
			$data=chargeMat($res);
			for($h=0;$h<count($data);$h++){
				$id=$data[$h][0];
				miseAJourEdt($id,$info,'#FFFFFF','0',$idpers,'','0','','','0','0','0','0000-00-00','hh:mm',$valid,'0',$equipement,$duree,$heure1,'','','','');
			}
			$sql="UPDATE ${prefixe}edt_seances SET id_resa_liste='$idResaListe' WHERE code='$CODE'";
			execSql($sql);
		}
		return($cr);
	}else{
		$DateDepart=conv_datetimestamp($date); //dd/mm/YYYY exclus
		$DateFin=conv_datetimestamp($jusquau);	
		$refcommun=md5("$DateDepart$DateFin$idpers$equipement");
		$tabsql="";
		$CODE=md5(date("H:m:sd/m/Y").rand(1000,9999));
		while($DateDepart <= $DateFin) {
			$date=strftime("%Y-%m-%d",$DateDepart);
			$dateB=strftime("%d/%m/%Y",$DateDepart);
			foreach($tabJours as $key=>$value) {
				$jour=date_jour2(strftime("%d/%m/%Y",$DateDepart));
				switch($jour) {
					case "di" :  $jour="0" ; break;
					case "lu" :  $jour="1" ; break;
					case "ma" :  $jour="2" ; break;
					case "me" :  $jour="3" ; break;
					case "je" :  $jour="4" ; break;
					case "ve" :  $jour="5" ; break;
					case "sa" :  $jour="6" ; break;
				}
	
				if ($jour == $value) {
					$cr=verif_si_resa_possible($equipement,$dateB,$heure1,$heure2);
					if ($cr == 0) {
						$tabsql[]=" INSERT INTO ${prefixe}resa_liste (idmatos,idqui,quand,heure_depart,heure_fin,info,valider,refcommun) VALUES ('$equipement','$idpers','$date','$heure1','$heure2','$info','$valid','$refcommun');";
						
						$tabsql2[]=" INSERT INTO ${prefixe}edt_seances (code,enseignement,date,heure,duree,bgcolor) VALUES ('$CODE','$info','$date','$heure1','$duree','#FFFFFF')";
					}else{
						break 2;
					}
				}
			}
			$DateDepart+=60 * 60 * 24 * 1 ;
		}
		if ($cr == 0) {
			foreach($tabsql as $key=>$sql) {
				$cr=execSql($sql);
				$idResaListe=mysqli_insert_id();
				if ($cr == 1) {
					$sql=$tabsql2[$key];
					execSql($sql);
					$sql="SELECT id FROM ${prefixe}edt_seances WHERE code='$CODE'";
					$res=execSql($sql);
					$data=chargeMat($res);
					for($h=0;$h<count($data);$h++){
						$id=$data[$h][0];
						miseAJourEdt($id,$info,'#FFFFFF','0',$idpers,'','0','','','0','0','0','0000-00-00','hh:mm',$valid,'0',$equipement,$duree,$heure1,'','','','');
					}
					$sql="UPDATE ${prefixe}edt_seances SET id_resa_liste='$idResaListe' WHERE code='$CODE'";
					execSql($sql);
				}
			}
			return 1;
		}else{
			return 0;
		}
	}
}


function recupInfoSeance($date,$heure,$idprof) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,idmatiere,heure,duree,idgroupe FROM ${prefixe}edt_seances WHERE date='$date' AND idprof='$idprof' AND heure<='$heure'  ORDER BY heure DESC  limit 1 ";

	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}

function recupInfoSeance2($date,$heure,$idprof,$idmatiere,$idclasse,$idgroupe) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,idmatiere,heure,duree,idgroupe FROM ${prefixe}edt_seances WHERE (idclasse='$idclasse' OR idgroupe='$idgroupe') and idmatiere='$idmatiere' and date='$date' AND idprof='$idprof' AND heure<='$heure'  ORDER BY heure DESC  limit 1 ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}


function create_resa2($equipement,$date,$idpers,$heure1,$heure2,$info,$confirm=0,$jusquau,$tabJours,$id_edt_seances) {
	global $cnx;
	global $prefixe;
	
	if ($confirm == 1) {
		$valid=1;
	}else{
		$valid=0;
	}

	$sql="SELECT * FROM ${prefixe}resa_liste WHERE id_edt_seance='$id_edt_seances'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$update=1;
	}else{
		$update=0;
	}

	$elements=preg_split('/\//',$date);
        $annee=$elements[2];
        $mois=$elements[1];
	$jour=$elements[0];


	list($h,$m,$s)=preg_split('/:/',$heure1);
	list($h2,$m2,$s2)=preg_split('/:/',$heure2);	
	$resultat=mktime($h,$m,$s,$mois,$jour,$annee);
	$resultat=$resultat + ($h2 * 60 * 60) + ($m2  * 60) + $s2 ;
	$heure2=strftime("%H:%M:%S",$resultat);

	$sql="SELECT code FROM ${prefixe}edt_seances WHERE id='$id_edt_seances' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$CODE=$data[0][0];
	$sql="SELECT date FROM ${prefixe}edt_seances WHERE code='$CODE' ORDER BY date LIMIT 1";
	$res=execSql($sql);
	$data=chargeMat($res);
	$dateRecup=$data[0][0];
	
	if (dateFormBase($date) == $dateRecup) { $jusquau=""; }
	if (trim($jusquau) == "") {
		$date=dateFormBase($date);
		if ($update == 1) {
			$sql="UPDATE ${prefixe}resa_liste SET idmatos='$equipement',idqui='$idpers',heure_depart='$heure1',heure_fin='$heure2',info='$info',valider='$valid' WHERE id_edt_seance='$id_edt_seances'";
			$cr=execSql($sql);
			$sql="SELECT id FROM ${prefixe}resa_liste WHERE id_edt_seance='$id_edt_seances'";
			$res=execSql($sql);
			$data=chargeMat($res);
			$idResaListe=$data[0][0];
		}else{
			$sql="INSERT INTO ${prefixe}resa_liste(idmatos,idqui,quand,heure_depart,heure_fin,info,valider,id_edt_seance) VALUES ('$equipement','$idpers','$date','$heure1','$heure2','$info','$valid','$id_edt_seances')";
			$cr=execSql($sql);
			$idResaListe=mysqli_insert_id();
		}
	}else{

		$jusquau=dateForm($dateRecup);	
		$DateDepart=conv_datetimestamp($jusquau); //dd/mm/YYYY exclus
		$DateFin=conv_datetimestamp($date);	
		$refcommun=md5("$DateDepart$DateFin$idpers$equipement");
		$tabsql="";		
		
		while($DateDepart <= $DateFin) {
			$date=strftime("%Y-%m-%d",$DateDepart);
			$dateB=strftime("%d/%m/%Y",$DateDepart);
			foreach($tabJours as $key=>$value) {
				$jour=date_jour2(strftime("%d/%m/%Y",$DateDepart));
				switch($jour) {
					case "di" :  $jour="0" ; break;
					case "lu" :  $jour="1" ; break;
					case "ma" :  $jour="2" ; break;
					case "me" :  $jour="3" ; break;
					case "je" :  $jour="4" ; break;
					case "ve" :  $jour="5" ; break;
					case "sa" :  $jour="6" ; break;
				}
			
				if ($jour == $value) {
					$cr=verif_si_resa_possible($equipement,$dateB,$heure1,$heure2);
					if ($cr == 0) {
						if ($update == 0) {
							$tabsql[]=" INSERT INTO ${prefixe}resa_liste (idmatos,idqui,quand,heure_depart,heure_fin,info,valider,refcommun,id_edt_seance) VALUES ('$equipement','$idpers','$date','$heure1','$heure2','$info','$valid','$refcommun','$id_edt_seances');";
						}else{
							$tabsql[]="UPDATE ${prefixe}resa_liste SET idmatos='$equipement',idqui='$idpers',quand='$date',heure_depart='$heure1',heure_fin='$heure2',info='$info',valider='$valid', refcommun='$refcommun' WHERE id_edt_seance='$id_edt_seances'";
						}
					}else{
						break 2;
					}
				}
			}
			$DateDepart+=60 * 60 * 24 * 1 ;
		}
		if ($cr == 0) {
			foreach($tabsql as $key=>$sql) {
				if ($update == 0) {
					$cr=execSql($sql);
					$idResaListe=mysqli_insert_id();
				}else{
					execSql($sql);
					$sql="SELECT id FROM ${prefixe}resa_liste WHERE id_edt_seance='$id_edt_seances'";
					$res=execSql($sql);
					$data=chargeMat($res);
					$idResaListe=$data[0][0];
				}
			}
			$sql="SELECT code FROM ${prefixe}edt_seances WHERE id='$id_edt_seances'";
			$res=execSql($sql);
			$data=chargeMat($res);
			$CODE=$data[0][0];
			$sql="UPDATE ${prefixe}edt_seances SET id_resa_liste='$idResaListe' WHERE code='$CODE'";
			execSql($sql);
			return 1;
		}else{
			return 0;
		}
	}
	$sql="SELECT code FROM ${prefixe}edt_seances WHERE id='$id_edt_seances'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$CODE=$data[0][0];
	$sql="UPDATE ${prefixe}edt_seances SET id_resa_liste='$idResaListe' WHERE code='$CODE'";
	execSql($sql);
}

function verif_si_resa_possible($equipement,$date,$heure1,$heure2) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT idmatos,idqui,quand,heure_depart,heure_fin,info,valider FROM  ${prefixe}resa_liste WHERE idmatos='$equipement' AND quand='$date' AND ((heure_depart <= '$heure1' AND heure_fin >= '$heure1') OR (heure_depart <= '$heure2' AND heure_fin >= '$heure2') OR (heure_depart >= '$heure1' AND heure_fin <= '$heure2')  OR (heure_depart <= '$heure1' AND heure_fin >= '$heure2') ) ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) != 0) {
		return $data;
	}else{
		return 0;
	}
}

function planning_equipement($equipement,$datedepart) {
	global $cnx;
	global $prefixe;
	$datedepart=dateFormBase($datedepart);
	$sql="SELECT  idmatos,idqui,quand,heure_depart,heure_fin,info,valider FROM  ${prefixe}resa_liste WHERE idmatos='$equipement' AND quand = '$datedepart' ORDER BY quand DESC  , heure_depart ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function list_equip() {
        $data=affSalle('equip');
	  return $data;
}

function list_salle() {
        $data=affSalle('salle');
	  return $data;
}

function renvoiTypePersonneMembre($membre) {
	if ($membre == "ADM") { $membre="menuadmin"; } 
	if ($membre == "MVS") { $membre="menuscolaire"; } 
	if ($membre == "ENS") { $membre="menuprof"; } 
	if ($membre == "PAR") { $membre="menuparent"; } 
	if ($membre == "ELE") { $membre="menueleve"; } 
	if ($membre == "TUT") { $membre="menututeur"; } 
	if ($membre == "PER") { $membre="menupersonnel"; } 
	return($membre);
}

function renvoiTypePersonne($membre) {
	if ($membre == "menuadmin") { $membre="ADM"; } 
	if ($membre == "menuscolaire") { $membre="MVS"; } 
	if ($membre == "menuprof") { $membre="ENS"; } 
	if ($membre == "menuparent") { $membre="PAR"; } 
	if ($membre == "menueleve") { $membre="ELE"; } 
	if ($membre == "menututeur") { $membre="TUT"; } 
	if ($membre == "menupersonnel") { $membre="PER"; } 
	return($membre);
}


function renvoiMembreFormatePersonne($membre) {
	if ($membre == "menuadmin") { $membre="Direction"; } 
	if ($membre == "menuscolaire") { $membre="Vie Scolaire"; } 
	if ($membre == "menuprof") { $membre="Enseignant"; } 
	if ($membre == "menuparent") { $membre="Parent"; } 
	if ($membre == "menueleve") { $membre="Elève"; } 
	if ($membre == "menututeur") { $membre="Tuteur de stage"; } 
	if ($membre == "menupersonnel") { $membre="Personnel"; } 
	return($membre);
}


function mess_forward($email,$valid,$nom,$prenom,$idpers,$membre) {
	global $cnx;
	global $prefixe;

	if ($membre == "ADM") { $membre="menuadmin"; } 
	if ($membre == "MVS") { $membre="menuscolaire"; } 
	if ($membre == "ENS") { $membre="menuprof"; } 
	if ($membre == "PAR") { $membre="menuparent"; } 
	if ($membre == "ELE") { $membre="menueleve"; } 
	if ($membre == "TUT") { $membre="menututeur"; } 
	if ($membre == "PER") { $membre="menupersonnel"; } 
	

	$nom=strtolower($nom);
	$email=trim($email);
	if ($valid) {
		if (DBTYPE == "pgsql") {
			$valid="TRUE";
		}
		if (DBTYPE == "mysql")  {
			$valid=1;
		}
	}else {
		if (DBTYPE == "pgsql") {
			$valid="FALSE";
		}
		if (DBTYPE == "mysql")  {
			$valid=0;
		}

	}
	if (($membre == "menuadmin") || ($membre == "menuscolaire") || ($membre == "menuprof") || ($membre == "menututeur") || ($membre == "menupersonnel")){
		$sql="UPDATE ${prefixe}personnel  SET email='$email', valid_forward_mail='$valid' WHERE pers_id='$idpers' ";
	}
	
	if ($membre == "menuparent") {
		$sql="UPDATE ${prefixe}eleves  SET email='$email', valid_forward_mail_parent='$valid' WHERE elev_id='$idpers' ";
	}

	if ($membre == "menueleve") {
		$sql="UPDATE ${prefixe}eleves  SET email_eleve='$email', valid_forward_mail_eleve='$valid' WHERE elev_id='$idpers' ";
	}
	

	return(execSql($sql));
}

function mess_mail_forward_parent($idpers,$idparent) {
	global $cnx;
        global $prefixe;
	if ($idparent == '1') {
		$sql="SELECT email FROM ${prefixe}eleves  WHERE elev_id='$idpers' ";
		$res=execSql($sql);
                $data=chargeMat($res);
                return($data[0][0]);
	}

	if ($idparent == '2') {
                $sql="SELECT email_resp_2 FROM ${prefixe}eleves  WHERE elev_id='$idpers' ";
                $res=execSql($sql);
                $data=chargeMat($res);
                return($data[0][0]);
        }
}

function mess_mail_forward($nom,$prenom,$idpers,$membre) {
	global $cnx;
	global $prefixe;
	$nom=strtolower($nom);

        if ($membre == "ADM") { $membre="menuadmin"; }
        if ($membre == "MVS") { $membre="menuscolaire"; }
        if ($membre == "ENS") { $membre="menuprof"; }
        if ($membre == "PAR") { $membre="menuparent"; }
        if ($membre == "ELE") { $membre="menueleve"; }
	if ($membre == "TUT") { $membre="menututeur"; }
	if ($membre == "PER") { $membre="menupersonnel"; }
	

	if (($membre == "menuadmin") || ($membre == "menuprof") || ($membre == "menuscolaire") || ($membre == "menututeur") || ($membre == "menupersonnel")) {
		$sql="SELECT nom,prenom,valid_forward_mail,email,pers_id FROM ${prefixe}personnel  WHERE pers_id='$idpers' ";	
		$res=execSql($sql);
		$data=chargeMat($res);
		return $data[0][3];
	}

	if ($membre == "menueleve") {
		$sql="SELECT nom,prenom,valid_forward_mail_eleve,email_eleve,elev_id,emailpro_eleve FROM ${prefixe}eleves  WHERE elev_id='$idpers' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		$email=trim($data[0][3]);
		if (trim($data[0][5]) != "") {
			if ($email != "") {
				$email.=",".$data[0][5];
			}else{
				$email=$data[0][5];
			}
		}
		return($email);
	}


	if ($membre == "menuparent") {
		$sql="SELECT nom,prenom,valid_forward_mail_parent,email,elev_id,email_resp_2 FROM ${prefixe}eleves  WHERE elev_id='$idpers' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		$email=trim($data[0][3]);
		if (trim($data[0][5]) != "") {
			if ($email != "") {
				$email.=",".$data[0][5];
			}else{
				$email=$data[0][5];
			}
		}
		return($email);
	}

}

function check_mail_forward_parent($nom,$prenom,$idpers,$idparent) {
	global $cnx;
        global $prefixe;
	$sql="SELECT valid_forward_mail_parent FROM ${prefixe}eleves  WHERE elev_id='$idpers'";
	$res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}


function check_mail_forward($nom,$prenom,$idpers,$membre) {
	global $cnx;
	global $prefixe;

	if ($membre == "ADM") { $membre="menuadmin"; } 
	if ($membre == "MVS") { $membre="menuscolaire"; } 
	if ($membre == "ENS") { $membre="menuprof"; } 
	if ($membre == "PAR") { $membre="menuparent"; } 
	if ($membre == "ELE") { $membre="menueleve"; } 
	if ($membre == "TUT") { $membre="menututeur"; }
	if ($membre == "PER") { $membre="menupersonnel"; }

	$nom=strtolower($nom);
	if (($membre == "menuadmin") || ($membre == "menuprof") || ($membre == "menuscolaire") || ($membre == "menututeur") || ($membre == "menupersonnel")  ) {
		$sql="SELECT  nom,prenom,email,valid_forward_mail,pers_id FROM ${prefixe}personnel  WHERE pers_id='$idpers' AND ";
	}
	if ($membre == "menueleve") {
		$sql="SELECT nom,prenom,email_eleve,valid_forward_mail_eleve,elev_id FROM ${prefixe}eleves  WHERE elev_id='$idpers' AND ";
	}
	if ($membre == "menuparent") {
		$sql="SELECT nom,prenom,email,valid_forward_mail_parent,elev_id FROM ${prefixe}eleves  WHERE elev_id='$idpers' AND ";
	}
	
	if ($membre == "menuparent") { $sql.="valid_forward_mail_parent=1"; }
	if ($membre == "menueleve") { $sql.="valid_forward_mail_eleve=1"; }
	if (($membre == "menuadmin")||($membre == "menuprof")||($membre == "menuscolaire")||($membre == "menututeur")||($membre == "menupersonnel")) { $sql.="valid_forward_mail=1"; }
	if ($sql != "") {
		$res=execSql($sql);
		$data=chargeMat($res);
		if (count($data) > 0) {
			return 1;
		}else {
			return 0;
		}
	}else{
		return 0;
	}
}

function check_mail_conf($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom,prenom,pers_id FROM ${prefixe}personnel  WHERE pers_id='$idpers'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return 1;
	}else {
		return 0;
	}

}



function envoi_mail_forward($nom,$prenom,$texte,$email,$lien,$emetteur,$number,$objet,$idpers) {

  // recuperation des coordonnées de l'etablissement
  $data=visu_param();
  for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
  }

  $message ="
<b>Messagerie TRIADE</b><br>
<br>
	Bonjour,<br>
<br>
<font color=red>Vous avez un message sur votre messagerie $nom_etablissement / Triade :</font><br>
Message de : $emetteur<br>
<br>
Pour consulter ce message veuillez utiliser la messagerie Triade<br>
via ce lien :  <a href='$lien".REPECOLE."/consult.php?id=$number&idp=$idpers' target='_blank' >$lien".REPECOLE."/consult.php?id=$number&idp=$idpers</a><br>
<br>
NE PAS FAIRE REPONDRE VIA VOTRE MESSAGERIE<br>
<br>
$nom_etablissement<br>
$adresse<br>
$ville - $postal<br>
$tel - $mail<br>
<hr>
	Hello,<br>
<br>
<font color=red>You have received a message on your $nom_etablissement TRIADE account</font><br>
Message of : $emetteur<br>
<br>
To view this message you must use your TRIADE account,<br>
click on the link below: <a href='$lien".REPECOLE."/consult.php?id=$number&idp=$idpers' target='_blank' >$lien".REPECOLE."/consult.php?id=$number&idp=$idpers</a><br>
<br>
DO NOT REPLYTO THIS MESSAGE USING YOUR EMAIL ACCOUNT.<br>
<br>
$nom_etablissement<br>
$adresse<br>
$ville - $postal<br>
$tel - $mail<br>
<br>
";
  $to = trim($email);
  $objet=TextNoAccent($objet) ;
  $objet=stripslashes($objet);
  $sujet = "Triade : $objet";
  $nom_expediteur=expediteur_triade();
  $email_expediteur=MAILREPLY;
//  $message=TextNoAccent($message);
			
  $ret="\n";
  if (PHP_OS == "WINNT") {  $ret="\r\n"; }

  $from = 'From: "'.$nom_expediteur.'" <'.$email_expediteur.'>'."$ret";
  $headers=$from;
//  $message=TextNoAccent($message);
  $email_expediteur=trim($email_expediteur);
  //print "to : $to, Sujet : $sujet , From:$from <br>  <hr>";
  if (preg_match('/,/',$to)) {
	  if (ValideMail($email_expediteur)) {
		mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
	  }
  }else{
	  if (ValideMail($to) && (ValideMail($email_expediteur)) ) {
		//mail($to, $sujet, $message,$headers);
		mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
	  }
   }

}


function envoi_mail_central($nom,$email,$password,$emetteur,$objet) {

  // recuperation des coordonnées de l'etablissement
  $data=visu_param();
  for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
  }

  $message ="
<b>Message Centrale des stages</b><br>
<br>
    Bonjour ".trim($nom).",<br>
<br>
<u>Votre demande d'affiliation vient d'etre active :</u><br>
Message de : $emetteur<br>
<br>
Votre mot de passe est : $password<br>
<br>
L'activation s'effectue via votre compte direction puis le module 'Centrale Stages' <br>
puis 'Valider votre accréditation à une centrale de stage'<br>
<br>
Une fois le mot de passe enregistré vous pourrez acceder a la central des stages.<br>
<br>
<hr>
<font color=red><b>NE PAS FAIRE REPONDRE VIA VOTRE MESSAGERIE</b></font><br>
<br>
$nom_etablissement<br>
$adresse<br>
$ville - $postal<br>
$tel - $mail<br>
";
  $to = trim($email);
  $objet=TextNoAccent($objet) ;
  $objet=stripslashes($objet);
  $sujet = "Triade : $objet";
  $nom_expediteur=expediteur_triade();
  $email_expediteur=MAILREPLY;
//  $message=TextNoAccent($message);
			
  $ret="\n";
  if (PHP_OS == "WINNT") {  $ret="\r\n"; }

  $from = 'From: "'.$nom_expediteur.'" <'.$email_expediteur.'>'."$ret";
  $headers=$from;
//  $message=TextNoAccent($message);
  $email_expediteur=trim($email_expediteur);
  //print "to : $to, Sujet : $sujet , From:$from <br>  <hr>";
  if (ValideMail($to) && (ValideMail($email_expediteur)) ) {
  	//mail($to, $sujet, $message,$headers);
	mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
  }

}



//---------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------

function create_groupe_mail($libelle,$idpers,$liste,$public,$cacher) {
        global $cnx;
	global $prefixe;
	if ($public == "on") {
                $public=1;
        }else {
                $public=0;
	}
	if ($cacher == "on") {
                $cacher=1;
        }else {
                $cacher=0;
        }
	$idliste=join(",",$liste);
	$sql="INSERT INTO ${prefixe}mail_grp(libelle,idpers,liste_id,public,cacher,grpelev) VALUES ('$libelle','$idpers','\{$idliste}',$public,$cacher,'0')";
	execSql($sql);
}

function modif_groupe_mail($libelle,$idpers,$liste,$public,$cacher,$id) {
        global $cnx;
	global $prefixe;
	if ($public == "on") {
                $public=1;
        }else {
                $public=0;
	}
	if ($cacher == "on") {
                $cacher=1;
        }else {
                $cacher=0;
        }
	$idliste=join(",",$liste);
	$sql="UPDATE ${prefixe}mail_grp SET libelle='$libelle' , liste_id='\{$idliste}', public='$public',  cacher='$cacher' WHERE idpers='$idpers' AND id='$id' ";
	return(execSql($sql));
}


function cacherGrpMail($id) {
        global $cnx;
	global $prefixe;
	$sql="SELECT libelle FROM ${prefixe}mail_grp  WHERE id='$id' AND cacher='1' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return true;
	}else{
		return false;
	}
}

function create_groupe_mailEle($libelle,$idpers,$liste,$public,$cacher) {
        global $cnx;
	global $prefixe;
	if ($public == "on") {
                $public=1;
        }else {
                $public=0;
	}
	if ($cacher == "on") {
                $cacher=1;
        }else {
                $cacher=0;
        }
	$idliste=join(",",$liste);
	$sql="INSERT INTO ${prefixe}mail_grp(libelle,idpers,liste_id,public,grpelev,cacher) VALUES ('$libelle','$idpers','\{$idliste}','$public','1','$cacher')";
	execSql($sql);	
}

function modif_groupe_mailEle($libelle,$idpers,$liste,$public,$cacher,$id) {
        global $cnx;
	global $prefixe;
	if ($public == "on") {
                $public=1;
        }else {
                $public=0;
	}
	if ($cacher == "on") {
                $cacher=1;
        }else {
                $cacher=0;
        }
	$idliste=join(",",$liste);
	$sql="UPDATE ${prefixe}mail_grp SET libelle='$libelle' , liste_id='\{$idliste}', public='$public',  cacher='$cacher' WHERE idpers='$idpers' AND id='$id' AND grpelev='1' ";
	return(execSql($sql));
}

function rechercheLibelleGroupeMail($id) {
        global $cnx;
	global $prefixe;
	$sql="SELECT libelle FROM ${prefixe}mail_grp  WHERE id='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][0];
}



function liste_grp_mail($id_pers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,liste_id,libelle,cacher FROM ${prefixe}mail_grp  WHERE idpers='$id_pers' AND grpelev != '1'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function verifGroupMail($id,$idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,liste_id,libelle,cacher,public FROM ${prefixe}mail_grp  WHERE id='$id' AND idpers='$idpers' AND grpelev != '1' LIMIT 1";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return $data;
	}
	return(false);
}

function verifGroupMailEleve($id,$idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,liste_id,libelle,cacher,public FROM ${prefixe}mail_grp  WHERE id='$id' AND idpers='$idpers' AND grpelev = '1' LIMIT 1";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return $data;
	}
	return(false);
}

function liste_grp_maileleve($id_pers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,liste_id,libelle,cacher FROM ${prefixe}mail_grp  WHERE idpers='$id_pers' AND grpelev='1' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function liste_idpers_mail($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,liste_id,libelle FROM ${prefixe}mail_grp  WHERE id='$id' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][2];
}


function liste_idpers_grp_mail($liste_id) {
	global $cnx;
	global $prefixe;
	$liste_id=preg_replace("/\{/","",$liste_id);
	$liste_id=preg_replace("/\}/","",$liste_id);
	$data=explode(",",$liste_id);
	return $data;
}

function mail_grp_supp($id,$idpers) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}mail_grp WHERE id='$id' AND idpers=$idpers";
	return(execSql($sql));
}

function mail_grp_suppeleve($id,$idpers) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}mail_grp WHERE id='$id' AND idpers=$idpers AND grpelev='1' ";
	return(execSql($sql));
}

function listingGroupeMail($idgroupe) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,liste_id,libelle,public FROM ${prefixe}mail_grp WHERE id='$idgroupe'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$liste_id=$data[0][2];
		$data=liste_idpers_grp_mail($liste_id);
	}else{
		$data=array();
	}
	return $data;
}

function nomDuGroupeMail($idgroupe) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,liste_id,libelle,public FROM ${prefixe}mail_grp WHERE id='$idgroupe'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return $data[0][3];
	}else{
		return "";
	}
}

function select_grp_mail($id_pers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,liste_id,libelle,public FROM ${prefixe}mail_grp WHERE idpers='$id_pers' OR  ";
        $sql.="public=1";
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][3]."</option>";
	}
}


function select_grp_mailelev($id_pers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,liste_id,libelle,public FROM ${prefixe}mail_grp WHERE (idpers='$id_pers' OR  public='1' ) AND grpelev='1'";
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][3]."</option>";
	}
}

function verifsiretardAvecDate($eleveid,$date) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, creneaux FROM ${prefixe}retards WHERE elev_id='$eleveid' AND date_ret='$date' ORDER BY heure_ret DESC ";
	$res=execSql($sql);
	$data22=chargeMat($res);
	return $data22;
}


function verifsiretard($eleveid) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, creneaux FROM ${prefixe}retards WHERE elev_id='$eleveid' AND date_ret='$date' ORDER BY heure_ret DESC ";
	$res=execSql($sql);
	$data22=chargeMat($res);
	return $data22;
}

function verifsiabsAvecDate($eleveid,$date) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT elev_id, duree_heure, date_ab, date_saisie, origin_saisie, duree_ab , motif, id_matiere, creneaux, heuredabsence  FROM ${prefixe}absences WHERE elev_id='$eleveid' AND date_ab='$date' AND date_fin='$date' AND  (duree_ab = '-1' OR duree_ab = '0') ORDER BY heuredabsence DESC ";
	$res=execSql($sql);
	$data23=chargeMat($res);
	return $data23;

}


function verifsiabs($eleveid) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$sql="SELECT elev_id, duree_heure, date_ab, date_saisie, origin_saisie, duree_ab , motif, id_matiere, creneaux, heuredabsence  FROM ${prefixe}absences WHERE elev_id='$eleveid' AND date_ab='$date' AND date_fin='$date' AND  (duree_ab = '-1' OR duree_ab = '0') ORDER BY heuredabsence DESC ";
	$res=execSql($sql);
	$data23=chargeMat($res);
	return $data23;

}

function supp_actu($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT config_video FROM ${prefixe}news_admin  WHERE idnews='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$config_video=$data[0][0];
	$sql="DELETE FROM ${prefixe}news_admin  WHERE idnews='$id'";
	$cr=execSql($sql);
	if ($cr) {
		if (file_exists("./flvplayer/${config_video}txt")) {
			unlink("./flvplayer/${config_video}txt");
		}
	}
}

function verifGroupBis($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT liste_elev FROM ${prefixe}groupes WHERE  libelle LIKE '%BIS%'  ";
	$res=execSql($sql);
	$data=chargeMat($res);
	for ($i=0;$i<count($data);$i++) {
		$liste_eleves=preg_replace("/\{/","",$data[$i][0]);
       		$liste_eleves=preg_replace("/\}/","",$liste_eleves);
		$listeEleve=preg_split ("/,/", $liste_eleves);
		foreach ($listeEleve as $valeur) {
			if ($idEleve == $valeur) {
				return true;
			}
		}
	}
	return false;
}



function verif_table_classe() {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_classe FROM ${prefixe}affectations GROUP BY code_classe";
	$res=execSql($sql);
	$data=chargeMat($res);
	for ($i=0;$i<count($data);$i++) {
		$code_classe=$data[$i][0];
		if (trim($code_classe) == "") continue;
		$sql="SELECT * FROM ${prefixe}classes WHERE code_class='$code_classe'";
		$data2=chargeMat(execSql($sql));
		if (count($data2)) {
			continue;
		}else{
			$sql="DELETE FROM ${prefixe}affectations WHERE code_classe='$code_classe'";
			execSql($sql);
		}
	}
}


function verif_table_groupe() {
	global $cnx;
	global $prefixe;
	$sql="SELECT group_id,liste_elev,libelle FROM ${prefixe}groupes WHERE group_id='0' AND liste_elev IS NULL AND libelle IS NULL";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return;
	}
	$sql="SELECT group_id,liste_elev,libelle FROM ${prefixe}groupes WHERE liste_elev IS  NULL AND libelle IS NULL";
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$sql="DELETE FROM ${prefixe}groupes WHERE group_id='".$data[$i][0]."'";
		execSql($sql);
	}
        $sql="INSERT INTO ${prefixe}groupes (group_id,liste_elev,commentaire,libelle) VALUES ('0',NULL,NULL,NULL)";
        execSql($sql);
        if (DBTYPE=="mysql") {
                $sql="UPDATE ${prefixe}groupes SET group_id='0', liste_elev=NULL ,commentaire=NULL,libelle=NULL WHERE libelle IS NULL AND liste_elev IS NULL ";
                execSql($sql);
        }
}

function etude_ajout($nometude,$jour,$heure_etude,$duree_etude,$pers_surv,$salleetude) {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}etude_param (jour_semaine,heure,salle,pion,nom_etude,duree) VALUES ('\{$jour\}','$heure_etude','$salleetude','$pers_surv','$nometude','$duree_etude')";
	return(execSql($sql));

}

function etude_modif($nometude,$jour,$heure_etude,$duree_etude,$pers_surv,$salleetude,$id) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}etude_param SET jour_semaine='\{$jour\}', heure='$heure_etude', salle='$salleetude', pion='$pers_surv', nom_etude='$nometude', duree='$duree_etude' WHERE id='$id' ";
	return(execSql($sql));

}

function liste_etude() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,jour_semaine,heure,salle,pion,nom_etude,duree FROM ${prefixe}etude_param";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function liste_etude_2($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,jour_semaine,heure,salle,pion,nom_etude,duree FROM ${prefixe}etude_param WHERE id='$id' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function etude_supp($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}etude_param  WHERE id='$id'";
        return(execSql($sql));
}

function cherche_nom_etude($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,nom_etude FROM ${prefixe}etude_param WHERE id='$id' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][1];
}

function liste_etude_option() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,nom_etude FROM ${prefixe}etude_param ";
	$res=execSql($sql);
        $data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
	print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][1]."</option>\n";
	}
}

function create_etude_eleve($id_eleve,$id_etude,$info,$sortir) {
        global $cnx;
	global $prefixe;
        if ($sortir == 1) {
                if (DBTYPE == "pgsql") {
                        $sorti="TRUE";
                }
                if (DBTYPE == "mysql")  {
                         $sorti=1;
                }

        }else {
                if (DBTYPE == "pgsql") {
                        $sorti="FALSE";
                }
                if (DBTYPE == "mysql")  {
                        $sorti=0;
                }
        }
        $sql="INSERT INTO ${prefixe}etude_affect (id_eleve,id_etude,information,auto_exit) VALUES ('$id_eleve','$id_etude','$info','$sorti')";
        return(execSql($sql));
}

function modif_eleve_etude($id_eleve,$id_etude,$info,$sortir) {
	global $cnx;
	global $prefixe;
	if ($sortir == 1) {
                if (DBTYPE == "pgsql") {
                        $sorti="TRUE";
                }
                if (DBTYPE == "mysql")  {
                         $sorti=1;
                }

        }else {
                if (DBTYPE == "pgsql") {
                        $sorti="FALSE";
                }
                if (DBTYPE == "mysql")  {
                        $sorti=0;
                }
        }
	$sql="UPDATE ${prefixe}etude_affect SET information='$info', auto_exit='$sorti' WHERE id_eleve='$id_eleve' AND id_etude='$id_etude'  ";
        return(execSql($sql));
}

function verifchecketude($id,$id_etude) {
        global $cnx;
	global $prefixe;
        $sql="SELECT id_eleve,id_etude,information,auto_exit FROM ${prefixe}etude_affect WHERE id_eleve='$id' AND id_etude='$id_etude' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (($data[0][3] == 1) || ($data[0][3] == "t")) {
                return "checked='checked'";
        }else {
                return;
        }
}

function supp_etude_eleve($id_eleve,$id_etude) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}etude_affect WHERE id_eleve='$id_eleve' AND id_etude='$id_etude'  ";
	return(execSql($sql));
}



function liste_eleve_etude_trombi($id_etude) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_eleve,id_etude,information,`auto_exit` FROM ${prefixe}etude_affect WHERE  `id_etude`='$id_etude' limit 50 ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}

function verif_eleve_etude($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}etude_affect WHERE id_etude='$id' ";
	$res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0) {
		return true;
	}else{
		return false;
	}
}

function suppEleveEtude($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}etude_affect WHERE id_eleve='$id_eleve'  ";
	return(execSql($sql));
}

function purgeParamEtude() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}etude_affect";
	return(execSql($sql));
}

function purgeEleveEtude() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}etude_param ";
	return(execSql($sql));
}


function supp_absretard($type,$id,$heure,$date,$time,$idmatiere) {
	global $cnx;
	global $prefixe;
	if ($type == "absent") {
		$sql="DELETE FROM ${prefixe}absences WHERE elev_id='$id' AND date_ab='$date' AND time='$time' AND id_matiere='$idmatiere' ";
		return(execSql($sql));
	}

	if ($type == "retard") {
		$sql="DELETE FROM ${prefixe}retards WHERE elev_id='$id' AND heure_ret='$heure' AND date_ret='$date' ";
		return(execSql($sql));
	}
}


function create_com_bulletin($com,$idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof FROM ${prefixe}bulletin_prof_param WHERE idprof='$idpers' AND com='$com'";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) {
		return 1;
	}else {
		$sql="INSERT INTO ${prefixe}bulletin_prof_param (idprof,com) VALUES ('$idpers','$com')";
	
	        return(execSql($sql));
	}
}


function select_com_bulletin($idpers,$nbcarac) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,com,idprof FROM ${prefixe}bulletin_prof_param WHERE idprof='$idpers' ORDER BY com";
   	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++)
    	{
    		print "<option id='select1' value='".$data[$i][0]."' title=\"".$data[$i][1]."\" >".trunchaine($data[$i][1],$nbcarac)."</option>\n";
	}
}

function supp_com_bulletin($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_prof_param WHERE id='$id' ";
	return(execSql($sql));
}

function supp_com_bulletin2($idpers) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_prof_param WHERE idprof='$idpers' ";
	return(execSql($sql));
}


function liste_com_bulletin($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,com,idprof FROM ${prefixe}bulletin_prof_param WHERE idprof='$idpers' ORDER BY com";
   	$data=ChargeMat(execSql($sql));
	return $data;
}



function enregistrement_com_bulletin($idmatiere,$idclasse,$tri,$idEleve,$commentaire,$idprof,$idgroupe,$typecom,$anneeScolaire) {
        global $cnx;
        global $prefixe;
        if ($idgroupe > 0)  { $idclasse="-1"; }
	if ($anneeScolaire != "") { 
	        $sql="SELECT idmatiere,idclasse,trimestre,ideleve FROM ${prefixe}bulletin_prof_com WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND idgroupe='$idgroupe' AND idprof='$idprof' AND typecom='$typecom' AND annee_scolaire='$anneeScolaire' ";
	        $data=ChargeMat(execSql($sql));
	        if (count($data) > 0) {
	                $sql="UPDATE ${prefixe}bulletin_prof_com SET com='$commentaire' WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND idgroupe='$idgroupe' AND  idprof='$idprof' AND typecom='$typecom' AND annee_scolaire='$anneeScolaire'";
	        }else {
	                $sql="INSERT INTO ${prefixe}bulletin_prof_com (idmatiere,idclasse,trimestre,ideleve,com,idprof,idgroupe,typecom,annee_scolaire) VALUES ('$idmatiere','$idclasse','$tri','$idEleve','$commentaire','$idprof','$idgroupe','$typecom','$anneeScolaire')";
	    	}
		return(execSql($sql));
	}
}

function enregistrement_note_scolaire($idmatiere,$idclasse,$tri,$idEleve,$note,$idprof,$idgroupe,$commentaire,$anneeScolaire) {
        global $cnx;
	global $prefixe;
	if (trim($note) == "") {  $note="-1"; }
        if ($idgroupe > 0)  { $idclasse="-1"; }
        $sql="SELECT idmatiere,idclasse,trimestre,ideleve FROM ${prefixe}notes_scolaire WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND idgroupe='$idgroupe' AND idprof='$idprof' AND  annee_scolaire='$anneeScolaire' ";
        $data=ChargeMat(execSql($sql));
        if (count($data) > 0) {
                $sql="UPDATE ${prefixe}notes_scolaire SET note='$note',commentaire='$commentaire' WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND idgroupe='$idgroupe' AND annee_scolaire='$anneeScolaire' ";
        }else {
                $sql="INSERT INTO ${prefixe}notes_scolaire (idmatiere,idclasse,trimestre,ideleve,note,idprof,idgroupe,commentaire,annee_scolaire) VALUES ('$idmatiere','$idclasse','$tri','$idEleve','$note','$idprof','$idgroupe','$commentaire','$anneeScolaire')";
    }

    return(execSql($sql));
}



function cherche_com_eleve($idEleve,$idmatiere,$idclasse,$tri,$idprof,$idgroupe,$anneeScolaire='') {
        global $cnx;
        global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($idgroupe > 0) {
                $sql="SELECT com,idprof FROM ${prefixe}bulletin_prof_com WHERE idgroupe='$idgroupe' AND idmatiere='$idmatiere' AND trimestre='$tri' AND ideleve='$idEleve' AND idprof='$idprof' AND typecom='0'  AND annee_scolaire='$anneeScolaire' ";
        }else{
                $sql="SELECT com,idprof FROM ${prefixe}bulletin_prof_com WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND idprof='$idprof' AND typecom='0'  AND annee_scolaire='$anneeScolaire' ";
        }
        $data=ChargeMat(execSql($sql));
        return html_vers_text($data[0][0]);
}

function cherche_com_eleve2($idEleve,$idmatiere,$idclasse,$tri,$idprof,$idgroupe,$typecom,$anneeScolaire='') {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($idgroupe > 0) {
                $sql="SELECT com,idprof FROM ${prefixe}bulletin_prof_com WHERE idgroupe='$idgroupe' AND idmatiere='$idmatiere' AND trimestre='$tri' AND ideleve='$idEleve' AND idprof='$idprof' AND typecom='$typecom' AND annee_scolaire='$anneeScolaire' ";
        }else{
                $sql="SELECT com,idprof FROM ${prefixe}bulletin_prof_com WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND idprof='$idprof' AND typecom='$typecom'  AND annee_scolaire='$anneeScolaire' ";
        }
        $data=ChargeMat(execSql($sql));
        return html_vers_text($data[0][0]);
}



function cherche_com_eleve_examen($idEleve,$idmatiere,$idclasse,$tri,$idprof,$idgroupe,$examen,$anneeScolaire='') {
        global $cnx;
	global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($idgroupe > 0) {
                $sql="SELECT com,idprof  FROM ${prefixe}bulletin_prof_com WHERE idgroupe='$idgroupe' AND idmatiere='$idmatiere' AND trimestre='$tri' AND ideleve='$idEleve' AND idprof='$idprof' AND annee_scolaire='$anneeScolaire' ";
        }else{
                $sql="SELECT com,idprof  FROM ${prefixe}bulletin_prof_com WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND idprof='$idprof' AND annee_scolaire='$anneeScolaire' ";
        }

        $data=ChargeMat(execSql($sql));
        return html_vers_text($data[0][0]);
}

function cherche_note_scolaire_eleve($idEleve,$idmatiere,$idclasse,$tri,$idprof,$idgroupe,$examen=0,$anneeScolaire='') {
        global $cnx;
        global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	if ($idgroupe > 0) {
                $sql="SELECT note,idprof  FROM ${prefixe}notes_scolaire WHERE idgroupe='$idgroupe' AND idmatiere='$idmatiere' AND trimestre='$tri' AND ideleve='$idEleve' AND idprof='$idprof' AND examen='$examen' AND annee_scolaire='$anneeScolaire' ";
        }else{
                $sql="SELECT note,idprof  FROM ${prefixe}notes_scolaire WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND idprof='$idprof' AND examen='$examen'  AND annee_scolaire='$anneeScolaire' ";
        }

	$data=ChargeMat(execSql($sql));
	if ($data[0][0] == -1) {
		return "";
	}else{
	        return $data[0][0];
	}
}




function cherche_note_scolaire_eleve_cpe($idEleve,$idmatiere,$idclasse,$tri,$idgroupe,$examen=0) {
        global $cnx;
        global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($idgroupe > 0) {
                $sql="SELECT note,idprof  FROM ${prefixe}notes_scolaire WHERE idgroupe='$idgroupe' AND idmatiere='$idmatiere' AND trimestre='$tri' AND ideleve='$idEleve' AND examen='$examen' AND annee_scolaire='$anneeScolaire' ";
        }else{
                $sql="SELECT note,idprof  FROM ${prefixe}notes_scolaire WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND examen='$examen' AND annee_scolaire='$anneeScolaire'  ";
        }

	$data=ChargeMat(execSql($sql));
	if ($data[0][0] == -1) {
		return "";
	}else{
		if ($data[0][0] != "") {
		        return number_format($data[0][0],2,'.','');
		}else{
			return "";
		}
	}
}

function cherche_com_scolaire_eleve_cpe($idEleve,$idmatiere,$idclasse,$tri,$idgroupe,$examen=0) {
        global $cnx;
        global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($idgroupe > 0) {
                $sql="SELECT commentaire,idprof  FROM ${prefixe}notes_scolaire WHERE idgroupe='$idgroupe' AND idmatiere='$idmatiere' AND trimestre='$tri' AND ideleve='$idEleve' AND examen='$examen' AND annee_scolaire='$anneeScolaire' ";
        }else{
                $sql="SELECT commentaire,idprof  FROM ${prefixe}notes_scolaire WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND examen='$examen'AND annee_scolaire='$anneeScolaire'  ";
        }

	$data=ChargeMat(execSql($sql));
	if ($data[0][0] == -1) {
		return "";
	}else{
	        return html_vers_text($data[0][0]);
	}
}


// -----------------------------------------------------------------------------------------------------------//
/*
$debut=deb_prog();
fin_prog($debut);
*/
function deb_prog() {
	$time = microtime();
	$tableau = explode(" ",$time);
	return ($tableau[1] + $tableau[0]);
}

function fin_prog($debut) {
	global $cnx;
    	global $prefixe;
	// calcul du temps d'éxécution et affichage
	$fin=deb_prog();
	$total = $fin - $debut;
	$total = substr($total,0,8);
	$file=$_SERVER["SCRIPT_NAME"];
	$file2=trunchaine($file,25);
	$sql="SELECT file,time_max,time_min FROM ${prefixe}statexecution WHERE file='$file2'";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) {
		if ($data[0][1] < $total) {
			$sql="UPDATE ${prefixe}statexecution  SET time_max='$total' WHERE file='$file2'";
			execSql($sql);
		}
		if ($data[0][2] > $total) {
			$sql="UPDATE ${prefixe}statexecution  SET time_min='$total' WHERE file='$file2'";
			 execSql($sql);
		}
			return ;
	}else{
		$sql="INSERT INTO ${prefixe}statexecution (file,time_max,time_min) VALUES ('$file2','$total','$total')";
		execSql($sql);
	}
}

// -----------------------------------------------------------------------------------------------------------//

function enr_config_note_usa($libelle,$min,$max) {
    global $cnx;
    global $prefixe;
    $libelle=strtoupper($libelle);
    if (strlen($max) == 2) { $max=$max+0.99; }
    
    $sql="SELECT libelle,min,max FROM ${prefixe}config_note_usa WHERE libelle='$libelle'";
    $data=ChargeMat(execSql($sql));
    if (count($data) > 0) {
    	print "<ul><font color=red>Cette note est déjà affectée.</font></ul>";
    	return;
    }
	$sql="SELECT libelle,min,max FROM ${prefixe}config_note_usa WHERE (min <= '$min' AND max >= '$min') OR (max >= '$max' AND min <= '$max' )";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) {
	    	print "<ul><font color=red>Cette intervalle fait déjà partie de la note <b>".$data[0][0]."</b>.</font></ul>";
	    	return;
    }
	
	$sql="INSERT INTO ${prefixe}config_note_usa (libelle,min,max) VALUES ('$libelle','$min','$max')";
	execSql($sql);
	return true;
}


function aff_config_note_usa() {
	global $cnx;
    global $prefixe;
    $sql="SELECT id,libelle,min,max FROM ${prefixe}config_note_usa";
    $data=ChargeMat(execSql($sql));
	return $data;
}

function supp_config_note_usa($idsupp) {
	global $cnx;
    global $prefixe;
	$sql="DELETE FROM ${prefixe}config_note_usa WHERE id='$idsupp' ";
	return(execSql($sql));
}

function recherche_note_en($not) {
	global $cnx;
    	global $prefixe;
	if (trim($not) != "") {
		$not=preg_replace("/,/",".",$not);
		$sql="SELECT  libelle,min,max FROM ${prefixe}config_note_usa WHERE  min <= '$not' AND  max >= '$not' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		if (count($data) > 0) {
				$not=$data[0][0];
		}else {
				$not="?" ;
		}
	}else {
		$not="";
	}
	return $not;
}


function  enr_trace($nav,$os,$ip,$nom,$prenom,$membre) {
	global $cnx;
    	global $prefixe;
    	$nom="$nom $prenom";
    	$date=dateDMY2();
    	$heure=dateHIS();
    	$sql="INSERT INTO ${prefixe}stat_trace (nom,ip,date,heure,os,navigateur,membre) VALUES ('$nom','$ip','$date','$heure','$os','$nav','$membre')";
	execSql($sql);
	$message="$nav#$os#$ip#$nom";
	acceslog($message);
}


function purge_stat_trace() {
	// durée d'archivage UNE année.
	global $cnx;
    	global $prefixe;
	$date=dateDMY();
        $date=datemoinsn($date,365) ;
	$sql="DELETE FROM ${prefixe}stat_trace WHERE date <= '$date' ";
	execSql($sql);
}

function trace_aff($limit) {
	global $cnx;
	global $prefixe;

	purge_stat_trace();

	if ($limit == 0) {
		$sql="SELECT  nom,ip,date,heure,os,navigateur,membre FROM ${prefixe}stat_trace ORDER BY date  DESC, heure DESC";
	}else{
		$sql="SELECT  nom,ip,date,heure,os,navigateur,membre FROM ${prefixe}stat_trace ORDER BY date  DESC, heure DESC LIMIT $limit";
	}
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}
function nombre_rtdnj($id_eleve,$dateDebut,$dateFin) {
    	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere FROM ${prefixe}retards WHERE elev_id='$id_eleve' AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' AND (motif='inconnu' OR motif='Inconnu') ";
	$res=execSql($sql);
	$data_22=chargeMat($res);
	return $data_22;
}
//--------------------------------------------------------------------------------------------------------//
// Lisaa
// recherche le nombre d'absence nonjustif
function nombre_absnj($id_eleve,$dateDebut,$dateFin) {
    global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND (motif='inconnu' OR motif='Inconnu') ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}
//FM recherche le nombre de sanction pour un eleve
function nombre_Sanc($id_eleve,$dateDebut,$dateFin){
    global $cnx;
	global $prefixe;
	$sql="SELECT id,id_eleve,motif,id_category,date_saisie,origin_saisie FROM ${prefixe}discipline_sanction WHERE id_eleve='$id_eleve' AND id_category=true AND date_saisie >='$dateDebut' AND date_saisie <= '$dateFin' ORDER BY date_saisie DESC";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}
//AP recherche le nombre d'exclusions
function nombre_Exclu($id_eleve,$dateDebut,$dateFin){
    global $cnx;
	global $prefixe;
	$sql="SELECT id,id_eleve,motif,id_category,date_saisie,enr_en_retenue, origin_saisie FROM ${prefixe}discipline_sanction WHERE id_eleve='$id_eleve' AND enr_en_retenue=true AND date_saisie >='$dateDebut' AND date_saisie <= '$dateFin' ORDER BY date_saisie DESC";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}

function cherche_com_gen($idEleve,$idclasse,$tri) {
    	global $cnx;
	global $prefixe;
	$gen="37";
	$sql="SELECT com,idprof  FROM ${prefixe}bulletin_prof_com WHERE idmatiere='$gen' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];
}
//--------------------------------------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
function create_category($libelle) {
        global $cnx;
		global $prefixe;
        $sql="INSERT INTO ${prefixe}type_category(libelle) VALUES ('$libelle')";
 		return(execSql($sql));
}

function select_category() {
	global $cnx;
	global $prefixe;
        $data=affCategory();
        for($i=0;$i<count($data);$i++)
        {
        print "<option  id='select1' value='".trim($data[$i][0])."'>".ucwords(trim($data[$i][1]))."</option>\n";
        }
}

// affichage sanction
function affCategory(){
	global $cnx;
	global $prefixe;
        $sql="SELECT * FROM ${prefixe}type_category ORDER BY libelle";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function supp_category($sanction) {
	global $cnx;
	global $prefixe;
    $sql="DELETE FROM ${prefixe}type_category WHERE id='$sanction'";
	$cr=execSql($sql);
	if ($cr) {
		$sql="DELETE FROM ${prefixe}type_sanction WHERE id_category='$sanction'";
		execSql($sql);
	}
	return $cr;
}

function chercheCategory($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle FROM ${prefixe}type_category WHERE id='$id' ";
    $res=execSql($sql);
    $data=chargeMat($res);
    return $data;
}


// affichage sanction
function affSanction2($id){
	global $cnx;
	global $prefixe;
        $sql="SELECT * FROM ${prefixe}type_sanction WHERE id_category='$id' ORDER BY libelle ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function select_sanction2() {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}type_category ORDER BY libelle";
	$res=execSql($sql);
    $data1=chargeMat($res);
    for ($j=0;$j<count($data1);$j++) {
    	$data=affSanction2($data1[$j][0]);
    			print "<optgroup label=\"".$data1[$j][1]."\"></optgroup>";
		for($i=0;$i<count($data);$i++) {
		        print "<option  id='select1' value='".trim($data[$i][0])."'>".ucwords(trim($data[$i][1]))."</option>\n";
        }


    }

}

function rechercheCategory($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle FROM ${prefixe}type_category WHERE id='$id'";
	$res=execSql($sql);
    $data1=chargeMat($res);
    return $data1[0][0];

}


function recup_sanction() {
	global $cnx;
	global $prefixe;
	$sql="SELECT sanction,nb FROM ${prefixe}type_nb_sanction";
	$res=execSql($sql);
    	$data1=chargeMat($res);
    	return $data1 ;
}

function recherche_si_retenu($sanction,$nb) {
	global $cnx;
	global $prefixe;

	if (DBTYPE == "pgsql") { $aa=FALSE; }
	if (DBTYPE == "mysql")  { $aa=0; }


	$sql="SELECT e.id_eleve FROM ${prefixe}discipline_sanction e WHERE e.enr_en_retenue='$aa' AND  e.id_category='$sanction' AND $nb <= (SELECT count(id_eleve) FROM ${prefixe}discipline_sanction WHERE enr_en_retenue='$aa' AND  id_category='$sanction' AND  e.id_eleve=id_eleve ) ";
	$res=execSql($sql);
    	$data1=chargeMat2($res);
    	return $data1;
}


function nb_fois($id_eleve,$sanction) {
	global $cnx;
	global $prefixe;

	if (DBTYPE == "pgsql") { $aa=FALSE; }
	if (DBTYPE == "mysql")  { $aa=0; }

	$sql="SELECT id_eleve,id_category FROM ${prefixe}discipline_sanction WHERE id_eleve='$id_eleve' AND  id_category='$sanction' AND  enr_en_retenue='$aa' ";
	$res=execSql($sql);
    $data1=chargeMat($res);
    return count($data1);
}

// create_sanction();



// ---------------------------------------------------------------------//
// ---------------------------------------------------------------------//

function GenerationCle($Texte,$CleDEncryptage) {
  $CleDEncryptage = md5($CleDEncryptage);
  $Compteur=0;
  $VariableTemp = "";
  for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++)
    {
    if ($Compteur==strlen($CleDEncryptage))
      $Compteur=0;
    $VariableTemp.= substr($Texte,$Ctr,1) ^ substr($CleDEncryptage,$Compteur,1);
    $Compteur++;
    }
  return $VariableTemp;
}



function Crypte($Texte,$Cle)  {
  srand((double)microtime()*1000000);
  $CleDEncryptage = md5(rand(0,32000) );
  $Compteur=0;
  $VariableTemp = "";
  for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++)
    {
    if ($Compteur==strlen($CleDEncryptage))
      $Compteur=0;
    $VariableTemp.= substr($CleDEncryptage,$Compteur,1).(substr($Texte,$Ctr,1) ^ substr($CleDEncryptage,$Compteur,1) );
    $Compteur++;
    }
  return base64_encode(GenerationCle($VariableTemp,$Cle) );
}



function Decrypte($Texte,$Cle) {
  $Texte = GenerationCle(base64_decode($Texte),$Cle);
  $VariableTemp = "";
  for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++)
    {
    $md5 = substr($Texte,$Ctr,1);
    $Ctr++;
    $VariableTemp.= (substr($Texte,$Ctr,1) ^ $md5);
    }
  return $VariableTemp;
}



// ---------------------------------------------------------------------//
// ---------------------------------------------------------------------//

function affRetard_via_date($type,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere,justifier,heure_saisie,creneaux, idrattrapage FROM ${prefixe}retards WHERE elev_id='$type' AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ORDER BY date_ret DESC ,heure_ret DESC ";
	$res=execSql($sql);
	$data_22=chargeMat($res);
	return $data_22;

}

function affDiscipline_via_date($type,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT id,id_eleve,motif,id_category,date_saisie,origin_saisie,enr_en_retenue,signature_parent,attribuer_par,devoir_a_faire   FROM ${prefixe}discipline_sanction WHERE id_eleve='$type' AND date_saisie >= '$dateDebut' AND date_saisie <= '$dateFin' ORDER BY date_saisie DESC ";
	$res=execSql($sql);
	$data_22=chargeMat($res);
	return $data_22;

}


function affRetenue_via_date($type,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire    FROM ${prefixe}discipline_retenue  WHERE id_elev='$type' AND date_de_la_retenue >= '$dateDebut' AND date_de_la_retenue <= '$dateFin' ORDER BY date_de_la_retenue DESC ";
	$res=execSql($sql);
	$data_22=chargeMat($res);
	return $data_22;

}

function affAbsence2_via_date($type,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier,creneaux,idrattrapage FROM ${prefixe}absences WHERE elev_id='$type' AND date_ab >='$dateDebut' AND date_ab <='$dateFin' ";
	$res=execSql($sql);
	$data_2=chargeMat($res);
	return $data_2;
}


function verif_retenu($id_eleve,$date_retenue,$heure_retenue) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_elev,date_de_la_retenue,heure_de_la_retenue  FROM ${prefixe}discipline_retenue WHERE id_elev='$id_eleve' AND  date_de_la_retenue='$date_retenue'  AND heure_de_la_retenue='$heure_retenue' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
			return true ;
	}else {
			return false ;
	}
}



function modif_sanction_sans_retenu($id_eleve) {
	global $cnx;
	global $prefixe;

	if (DBTYPE == "pgsql") { $aa=TRUE; }
	if (DBTYPE == "mysql")  { $aa=1; }
	if (DBTYPE == "pgsql") { $aaa=FALSE; }
	if (DBTYPE == "mysql")  { $aaa=0; }

	$sql="UPDATE ${prefixe}discipline_sanction SET enr_en_retenue='$aa' WHERE id_eleve='$id_eleve' AND enr_en_retenue='$aaa' ";
	execSql($sql);
}


function delete_discipline_prof($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}discipline_prof WHERE id='$id'";
	return(execSql($sql));
}

function rechercheCategory2($idsanction) {
        global $cnx;
        global $prefixe;
        $sql="SELECT e.libelle FROM ${prefixe}type_saction e, ${prefixe}type_category d   WHERE d.id_sanc='$idsanction' AND d.id_category=e.id ";
        $res=execSql($sql);
        $data1=chargeMat($res);
        return $data1[0][0];
}

function verif_si_affecte($id) {
        global $cnx;
        global $prefixe;
        $sql="SELECT e.id_category  FROM ${prefixe}type_sanction e, ${prefixe}discipline_retenue f, ${prefixe}discipline_retenue g  WHERE e.id_category='$id' OR  f.id_category='$id' OR g.id_category='$id'   LIMIT 1 ";
        $res=execSql($sql);
        $data1=chargeMat($res);
        return count($data1);
}

function supp_sanction_2($id_sanc) {
        global $cnx;
        global $prefixe;
        $sql="DELETE  FROM ${prefixe}type_sanction  WHERE id_sanc='$id_sanc' ";
        return(execSql($sql));
}

/*************************************/
/*  Module de suppression d'un ELEVE */
function suppression_eleve($id_eleve) {
	if (trim($id_eleve) != '' ) {
		$nomeleve=recherche_eleve_nom($id_eleve);
		$prenomeleve=recherche_eleve_prenom($id_eleve);
		@saveInfoStage($id_eleve);
		@suppEleveInfo($id_eleve);
		@suppElevenote($id_eleve);
		@suppEleveabs($id_eleve);
		@suppEleveretard($id_eleve);
		@suppElevedisp($id_eleve);
		@suppElevegroupe($id_eleve);
		@suppEleveMessagerieDest($id_eleve);
		@suppEleveMessagerieEnvoi($id_eleve);
		@suppEleveMessagerieRepertoire($id_eleve);
		@suppElevesanction($id_eleve);
		@suppElevediscipline($id_eleve);
		@suppEleveSansClasse($id_eleve);
		@suppEleveEtude($id_eleve);
		@suppEleveStage($id_eleve);
		@supp_info_util_agenda("menueleve",$id_eleve);
		@supp_info_util_agenda("menuparent",$id_eleve);
		@suppEleveCommentaireBulletin($id_eleve);
		@suppEleveCarnetEvaluation($id_eleve);
		@suppElevePlanClasse($id_eleve);
		@DeleteBrevetviaIdEleve($id_eleve);
		@suppInfoMedic($id_eleve);
		@suppInfo($id_eleve);
		@suppFicheLiaison($id_eleve);
		@purgeEntretienEleve($id_eleve);
		@suppEleveComptaVersement($id_eleve);
		@suppEleveCompta($id_eleve);
		@suppContreRenduViaIdeleve($id_eleve);
		@purgeCantinePers($id_eleve,"menueleve");
		@suppEleveAbsSconet($id_eleve);
		@suppEleveHisto($id_eleve);
	}else{
		// rien;	
	}


}


function suppEleveHisto($ideleve) {
	global $cnx;
    	global $prefixe;
	$sql="DELETE FROM ${prefixe}eleves_histo WHERE ideleve='$ideleve'";
	execSql($sql);
}


function SuppressionInfoEleveSuiteChangement($id_eleve) {
	// supprimer les infos de l'eleves
	// note, abs, retards, sanctions, dispenses, brevet 
	if (trim($id_eleve) != '') {
//		@suppElevenote($id_eleve);
//		@suppEleveabs($id_eleve);
//		@suppEleveretard($id_eleve);
//		@suppElevedisp($id_eleve);
//		@suppElevesanction($id_eleve);
//		@suppElevediscipline($id_eleve);
		@DeleteBrevetviaIdEleve($id_eleve);
//		@suppEleveCommentaireBulletin($id_eleve);
		@suppElevePlanClasse($id_eleve);
		@suppEleveAbsSconet($id_eleve);
	}
}





function suppEleveAbsSconet($ideleve) {
	global $cnx;
    	global $prefixe;
	$sql="DELETE FROM ${prefixe}absences_sconet  WHERE ideleve='$ideleve'";
	execSql($sql);
}

/*********************************/

function config_param_ajout($text,$libelle) {
	global $cnx;
    	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='$libelle'";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage (text,libelle) VALUES ('$text','$libelle')";
	return(execSql($sql));
}

function config_param_ajout_classe($text,$libelle,$idclasse) {
	global $cnx;
    	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='$libelle' AND idclasse='$idclasse' ";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage (text,libelle,idclasse) VALUES ('$text','$libelle','$idclasse')";
	return(execSql($sql));
}

function config_param_visu($libelle) {
	global $cnx;
    	global $prefixe;
    	$sql="SELECT  text,libelle FROM ${prefixe}parametrage  WHERE libelle='$libelle' LIMIT 1 ";
    	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data;
}

function verifImageJpg($type) {
	if ((preg_match("/$type/i", 'jpg')) || (preg_match("/$type/i", 'pjpeg')) || (preg_match("/$type/i", 'jpeg')) )  {
		return true;
	}else{
		return false;
	}
}


function verifPdf($type) {
	if ((preg_match("/$type/i", 'pdf')) || (preg_match("/$type/i", 'PDF')) )  {
		return true;
	}else{
		return false;
	}
}

function mailAdmin($type_erreur) {

	$ok=0;
	if ((defined("MAILADMIN")) && (defined("MAILREPLY"))) {
		if ((MAILADMIN != "") && (MAILREPLY != "")) {
			$ok=1;
			$to1=MAILADMIN;
		}
	}

	if ((defined("MAILADMIN2")) && (defined("MAILREPLY"))) {
		if ((MAILADMIN2 != "") && (MAILREPLY != "")) {
			$ok=1;
			$to2=MAILADMIN2;
		}
	}


	if ($ok==1) {
		include_once("./common/lib_phpMyadmin.php");
		$http=protohttps(); // return http:// ou https://
		$lien="$http".SERVER_NAME."/";
		$message ="

<b>ALERTE ADMINISTRATEUR TRIADE</b><br>
<br>
<br>
    Bonjour Administrateur,<br>
<br>
<u>Vous avez un message sur votre compte administrateur Triade :</u><br>
<br>
Message de Type : $type_erreur<br>
<br>
Pour consulter ce message veuillez utiliser le compte administrateur Triade<br>
via ce lien: <a href='$lien".REPECOLE."/".REPADMIN."/' target='_blank' >$lien".REPECOLE."/".REPADMIN."/</a><br>
<br>
NE PAS FAIRE REPONDRE VIA VOTRE MESSAGERIE<br>
<br>
<hr>
Hello Administrator,<br>
<br>
<u>You have received a message on your TRIADE account :</u><br>
<br>
Message : $type_erreur<br>
<br>
<br>
To view this message you must use your TRIADE account,<br>
click on the link below: <a href='$lien".REPECOLE."/".REPADMIN."/' target='_blank' >$lien".REPECOLE."/".REPADMIN."/</a><br>
<br>
DO NOT REPLYTO THIS MESSAGE USING YOUR EMAIL ACCOUNT.<br>

";


  		$sujet = "Triade : Support ALERTE ";
  		$nom_expediteur=expediteur_triade();
		$email_expediteur=MAILREPLY;
//		$message=TextNoAccent($message);
				
	 	$ret="\n";
  		if (PHP_OS == "WINNT") {  $ret="\r\n"; }

	  	$from = 'From: "'.$nom_expediteur.'" <'.$email_expediteur.'>'."$ret";
  		$headers=$from;
//  		$message=TextNoAccent($message);
		if (($to1 != "") && (ValideMail($to1))) { 	
			mailTriade($sujet,$message,$message,$to1,$email_expediteur,$email_expediteur,$nom_expediteur,"");
			//mail($to1, $sujet, $message,$headers);  
		}
		if (($to2 != "") && (ValideMail($to2))) { 
			mailTriade($sujet,$message,$message,$to2,$email_expediteur,$email_expediteur,$nom_expediteur,"");
			//mail($to2, $sujet, $message,$headers);  
		}
	}
}



function acceslog($message) {

	if (file_exists("./data/install_log/access.log"))  $fichier="./data/install_log/access.log";
	if (file_exists("../data/install_log/access.log"))  $fichier="../data/install_log/access.log";	
	
	$date=dateDMY();
	$heure=dateHIS();
	$ret="\n";
	if (PHP_OS == "WINNT") {  $ret="\r\n"; }
	$texte="$date|$heure|$message$ret";
    $fic=fopen($fichier,"a+");
    fwrite($fic,$texte);
    fclose($fic);
}

function acceslogAdmin($message) {
	$fichier="../data/install_log/access.log";
	$date=dateDMY();
	$heure=dateHIS();
	$ret="\n";
	if (PHP_OS == "WINNT") {  $ret="\r\n"; }
	$texte="$date|$heure|$message$ret";
    	$fic=fopen($fichier,"a+");
    	fwrite($fic,$texte);
    	fclose($fic);
}

function passwd_random() {
	include_once("./common/config2.inc.php");
	while(1) {
		$cara="AZERTYUIOPqsdfghjklmWXCVBNazertyuiopQSDFGHJKLMwxcvbn1234567890";
		if (SECURITE == 3) {
			$cara="AZERTYUIOPqsdfghjklmWXCVBNazertyuiopQSDFGHJKLMwxcvbn1234567890";
		}
		if (SECURITE == 2) {
			$cara="qsdfghjklmazertyuiopwxcvbn1234567890";
		}
		if (SECURITE == 1) {
			$cara="qsdfghjklmazertyuiopwxcvbn";
		}
		$nbrcara=7;
		$mdp="";
		srand((double)microtime()*1000000);
		for($i=0;$i<=$nbrcara;$i++) {
			$mdp.=$cara[rand()%strlen($cara)];
		}
		if ((SECURITE == 3 ) && (preg_match('/[a-z]/',$mdp)) && (preg_match('/[A-Z]/',$mdp))  && (preg_match('/[0-9]/',$mdp)) ) { return $mdp; }
		if ((SECURITE == 2 ) && (preg_match('/[a-z]/',$mdp)) && (preg_match('/[0-9]/',$mdp)) ) { return $mdp; }
		if (SECURITE == 1) { return $mdp; }
	}
	
}



function passwd_random2() {
	include_once("../common/config2.inc.php");
	while(1) {
		$cara="AZERTYUIOPqsdfghjklmWXCVBNazertyuiopQSDFGHJKLMwxcvbn1234567890";
		if (SECURITE == 3) {
			$cara="AZERTYUIOPqsdfghjklmWXCVBNazertyuiopQSDFGHJKLMwxcvbn1234567890";
		}
		if (SECURITE == 2) {
			$cara="qsdfghjklmazertyuiopwxcvbn1234567890";
		}
		if (SECURITE == 1) {
			$cara="qsdfghjklmazertyuiopwxcvbn";
		}
		$nbrcara=7;
		$mdp="";
		srand((double)microtime()*1000000);
		for($i=0;$i<=$nbrcara;$i++) {
			$mdp.=$cara[rand()%strlen($cara)];
		}
		if ((SECURITE == 3 ) && (preg_match('/[a-z]/',$mdp)) && (preg_match('/[A-Z]/',$mdp))  && (preg_match('/[0-9]/',$mdp))) { return $mdp; }
		if ((SECURITE == 2 ) && (preg_match('/[a-z]/',$mdp)) && (preg_match('/[0-9]/',$mdp)) ) { return $mdp; }
		if (SECURITE == 1) { return $mdp; }
	}
}



function supprimer_retenu_sanction_intervalle($dateDebut,$dateFin) {
	global $cnx;
    global $prefixe;
    $dateDebut=dateFormBase($dateDebut);
    $dateFin=dateFormBase($dateFin);
    $sql="DELETE  FROM ${prefixe}discipline_sanction   WHERE date_saisie >='$dateDebut' AND date_saisie <= '$dateFin' ";
    execSql($sql);
    $sql="DELETE  FROM ${prefixe}discipline_retenue   WHERE date_de_la_retenue >='$dateDebut' AND date_de_la_retenue <= '$dateFin' ";
    execSql($sql);
    return 1;
}


function sansaccent($chaine) {
   return strtr($chaine,'àâäåãáÂÄÀÅÃÁæÆçÇéèêëÉÊËÈïîìíÏÎÌÍñÑöôóòõÓÔÖÒÕùûüúÜÛÙÚÿ','aaaaaaaaaaaaaacceeeeeeeeiiiiiiiinnoooooooooouuuuuuuuy');
}


function sansaccentmajuscule($chaine) {
   return strtr($chaine,'ÂÄÀÅÃÁÇÉÊËÈÏÎÌÍÑÓÔÖÒÕÜÛÙÚ','AAAAAACEEEEIIIINOOOOOUUUU');
}


function ip_timeout($ip) {
		global $cnx;
    		global $prefixe;
		$sql="SELECT ip,timeout FROM ${prefixe}ip_timeout   WHERE ip='$ip' ";
		$res=execSql($sql);
        	$data=chargeMat($res);
        if (count($data) > 0) {
        	$timeout=$data[0][1];
        	$timeout=$timeout * 2;
        	$sql="UPDATE ${prefixe}ip_timeout SET timeout='$timeout' WHERE ip='$ip'";
        	$ins=execSql($sql);
        }else{
        	$sql="INSERT INTO ${prefixe}ip_timeout (ip,timeout) VALUES ('$ip','2')";
		execSql($sql);
        }
		sleep($timeout);
}

function ip_timeout_clear($ip) {
	global $cnx;
    	global $prefixe;
	$sql="DELETE  FROM ${prefixe}ip_timeout  WHERE ip='$ip'  ";
    	execSql($sql);
}

function enr_photo_bulletin($photo,$idsite) {
	global $cnx;
    	global $prefixe;
	$sql="DELETE  FROM ${prefixe}parametrage  WHERE libelle='param_logo_bull$idsite'  ";
    	@execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage (text,libelle) VALUES ('$photo','param_logo_bulletin$idsite')";
	execSql($sql);
}

function enr_photo_signature($photo,$id) {
	global $cnx;
    	global $prefixe;
	$sql="DELETE  FROM ${prefixe}parametrage  WHERE libelle='param_logo_sign$id'  ";
    	@execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage (text,libelle) VALUES ('$photo','param_logo_sign$id')";
	execSql($sql);
}

function recup_photo_bulletin() {
	global $cnx;
	global $prefixe;
	$sql="SELECT text,libelle FROM ${prefixe}parametrage WHERE libelle='param_logo_bulletin' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data;
}

function recup_photo_bulletin_idsite($idsite) {
	global $cnx;
	global $prefixe;
	if ($idsite == 1) $idsite="";
	if ($idsite == 0) $idsite="";
	$sql="SELECT text,libelle FROM ${prefixe}parametrage WHERE libelle='param_logo_bulletin$idsite' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data;
}

function recup_photo_signature() {
	global $cnx;
	global $prefixe;
	$sql="SELECT text,libelle FROM ${prefixe}parametrage WHERE libelle='param_logo_sign' ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data;
}

function recup_photo_signature_idsite($idsite) {
	global $cnx;
	global $prefixe;
	$sqlsuite="";	
	if ($idsite == 1) $sqlsuite=" OR libelle='param_logo_sign' ";
	$sql="SELECT text,libelle FROM ${prefixe}parametrage WHERE libelle='param_logo_sign$idsite' $sqlsuite ";
	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data;
}

function supp_photo_bulletin($idsite) {
	global $cnx;
    	global $prefixe;
	if ($idsite == 1) $idsite="";
	$sql="DELETE  FROM ${prefixe}parametrage  WHERE libelle='param_logo_bulletin$idsite'  ";
    	@execSql($sql);
}


function supp_param_creneaux() {
	global $cnx;
    	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='creneau'";
	@execSql($sql);
}


function supp_photo_signature($idsite) {
	global $cnx;
    	global $prefixe;
	$sql="DELETE  FROM ${prefixe}parametrage  WHERE libelle='param_logo_sign$idsite'  ";
    	@execSql($sql);
}

function aucun_retard($classe,$matiere,$nom,$prenom,$date) {
	global $cnx;
	global $prefixe;
	$date2=datemoinsn($date,365);
	$date=dateFormBase($date);
	$sql="DELETE FROM ${prefixe}abs_rtd_aucun WHERE date <= '$date2'";
	execSql($sql);
	$heure=dateHIS();
	$nom=ucwords($nom);
	$prenom=ucwords($prenom);
	$nomprenom=trunchaine($nom." ".$prenom,75);
	$sql="INSERT INTO ${prefixe}abs_rtd_aucun (classe,date,heure,matiere,enseignant) VALUES ('$classe','$date','$heure','$matiere','$nomprenom')";
	execSql($sql);
}

function enrAbsrtdHisto($classe,$matiere,$nom,$prenom,$date,$nbabs,$nbrtd) {
	global $cnx;
    	global $prefixe;
	$date2=datemoinsn($date,365);
	$date=dateFormBase($date);
	$sql="DELETE FROM ${prefixe}abs_rtd_info WHERE date <= '$date2'";
	execSql($sql);
	$heure=dateHIS();
	$nom=ucwords($nom);
	$prenom=ucwords($prenom);
	$nomprenom=trunchaine($nom." ".$prenom,75);
	$sql="INSERT INTO ${prefixe}abs_rtd_info (classe,date,heure,matiere,enseignant,nbabs,nbrtd) VALUES ('$classe','$date','$heure','$matiere','$nomprenom','$nbabs','$nbrtd')";
	execSql($sql);
}


function recup_abs_rtd_aucun($date) {
	global $cnx;
    	global $prefixe;
    	$date=dateFormBase($date);
	$sql="(SELECT  
		CAST(id AS BINARY) , 
		CAST(classe AS BINARY) , 
		CAST(date AS BINARY) , 
		CAST(heure AS BINARY) , 
		CAST(matiere AS BINARY) , 
		CAST(NULL AS BINARY) , 
		CAST(NULL AS BINARY)
		FROM ${prefixe}abs_rtd_aucun WHERE date='$date')
		UNION  
		(SELECT 
		id,classe,date,heure,matiere,nbabs,nbrtd  
		FROM ${prefixe}abs_rtd_info WHERE date='$date') ORDER BY 4" ;
	$res=execSql($sql);
	$data=chargeMat($res);
    	return $data;
}


function puge_abs_rtd_aucun() {
	global $cnx;
    	global $prefixe;
   	$sql="TRUNCATE TABLE ${prefixe}abs_rtd_aucun";
   	@execSql($sql);
   	$sql="TRUNCATE TABLE ${prefixe}abs_rtd_info";
   	@execSql($sql);
}

function recup_abs_rtd_aucun2($dateDebut,$dateFin) {
	global $cnx;
    	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT  id,classe,date,heure,matiere,enseignant  FROM ${prefixe}abs_rtd_aucun WHERE date>='$dateDebut' AND date<='$dateFin' ORDER BY date " ;
	$res=execSql($sql);
	$data=chargeMat($res);
    	return $data;
}


function creation_repertoire($membre,$idpers,$repertoire,$category) {
	global $cnx;
	global $prefixe;
    	$repertoire=ucwords($repertoire);
    	$sql="SELECT  id  FROM ${prefixe}messagerie_repertoire  WHERE id_pers='$idpers' AND membre='$membre' AND libelle='$repertoire' AND category='$category' " ;
    	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
			return ;
	}else{
			if (strtolower($repertoire) != "null") {
				$sql="INSERT INTO ${prefixe}messagerie_repertoire (id_pers,membre,libelle,category) VALUES ('$idpers','$membre','$repertoire','$category')";
				execSql($sql);
			}
	}

}

function suppression_messagerie_repertoire($idpers) {
	global $cnx;
    	global $prefixe;
	$sql="DELETE FROM ${prefixe}messagerie_repertoire WHERE id_pers='$idpers'";
	execSql($sql);
}


function select_repertoire_messagerie($idpers,$membre,$category) {
	global $cnx;
    	global $prefixe;
    	$sql="SELECT  id,libelle  FROM ${prefixe}messagerie_repertoire  WHERE id_pers='$idpers' AND membre='$membre' AND  category='$category' ORDER BY libelle " ;
    	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function messagerie_archive($id,$idrep) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}messageries  SET repertoire ='$idrep' WHERE id_message='$id'";
	execSql($sql);
}

function messagerie_archive2($id,$idrep) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}messagerie_envoyer  SET repertoire ='$idrep' WHERE id_message='$id'";
	execSql($sql);
}

function nbmessagerep($idrep,$idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  *  FROM ${prefixe}messageries  WHERE destinataire='$idpers' AND repertoire='$idrep' AND type_personne_dest='$membre' " ;
	$res=execSql($sql);
	$data=chargeMat($res);
	return count($data);
}


function nbmessagerepsupp($idrep,$idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  *  FROM ${prefixe}messagerie_envoyer  WHERE emetteur='$idpers' AND repertoire='$idrep' AND type_personne='$membre' " ;
	$res=execSql($sql);
	$data=chargeMat($res);
	return count($data);
}

function recherche_repertoire($id,$source) {
	global $cnx;
	global $prefixe;
	if ($source == "suppression") {
		$retour="Suppression";
		$sql="SELECT  libelle  FROM ${prefixe}messagerie_repertoire  WHERE id='$id' AND category='mess_supp' " ;
	}
	if ($source == "reception") {
		$retour="Réception";
		$sql="SELECT  libelle  FROM ${prefixe}messagerie_repertoire  WHERE id='$id' " ;
	}

	if (($id == "") || ($id == "null")) {
		return $retour;
	}else{
		global $cnx;
		global $prefixe;
		$res=execSql($sql);
		$data=chargeMat($res);
		return($data[0][0]);
	}
}


function bonux_entreprise($ident) {
		global $cnx;
		global $prefixe;
		$sql="SELECT bonus FROM ${prefixe}stage_entreprise  WHERE id_serial='$ident'";
		$res=execSql($sql);
		$data=chargeMat($res);
		$bonus=$data[0][0] + 1;
		$sql="UPDATE ${prefixe}stage_entreprise  SET bonus='$bonus'  WHERE id_serial='$ident'";
		execSql($sql);
}

function history_entreprise($identreprise,$ideleve,$periode,$langue,$trim,$service) {
        global $cnx;
        global $prefixe;
        $sql="SELECT nom,prenom,classe FROM ${prefixe}eleves WHERE elev_id='$ideleve' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 0) {
                $nomprenomeleve=$data[0][0]." ".$data[0][1];
                $nomprenomeleve=addslashes($nomprenomeleve);
                $classNom=chercheClasse_nom($data[0][2]);
                $classNom=addslashes($classNom);
                $sql="INSERT INTO ${prefixe}stage_history (identreprise,nomprenomeleve,classeeleve,periodestage,ideleve,langue,trimestre,service) VALUES ('$identreprise','$nomprenomeleve','$classNom','$periode','$ideleve','$langue','$trim','$service')";
                execSql($sql);
        }
}



function recupNbrEleveDemipension() {
	global $cnx;
	global $prefixe;
	$sql="SELECT COUNT(regime) FROM ${prefixe}eleves WHERE regime != 'externe'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}


function recupNbrEleveRegime() {
	global $cnx;
	global $prefixe;
	$joursemaine=dateJourSemaine();
	$heure=dateH();
	if ($heure < 12) { $suffixe="_m"; }else{ $suffixe="_s"; }
	// De 0 (pour Dimanche) à 6 (pour Samedi)
	if ($joursemaine == 0) { $jour="dimanche"; }
	if ($joursemaine == 1) { $jour="lundi"; }
	if ($joursemaine == 2) { $jour="mardi"; }
	if ($joursemaine == 3) { $jour="mercredi"; }
	if ($joursemaine == 4) { $jour="jeudi"; }
	if ($joursemaine == 5) { $jour="vendredi"; }
	if ($joursemaine == 6) { $jour="samedi"; }

	$nb=0;
	$jour=$jour.$suffixe;
	$sql="SELECT libelle FROM ${prefixe}regime WHERE $jour='1'";
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$sql="SELECT COUNT(*) FROM ${prefixe}eleves WHERE regime='".$data[$i][0]."'";
		$res=execSql($sql);
		$data2=chargeMat($res);	
		$nb+=$data2[0][0];
	}
	return($nb);
}



function saveInfoStage($ideleve) {
	global $cnx;
	global $prefixe;
	if ($ideleve == "") return; 
	$idclasse=chercheIdClasseDunEleve($ideleve);
	$sql="SELECT id_entreprise,num_stage FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$identreprise=$data[$i][0];
		$idnumstage=$data[$i][1]; 
		$sql="SELECT datedebut,datefin FROM ${prefixe}stage_date WHERE idclasse='$idclasse' AND id='$idnumstage'";
		$res=execSql($sql);
		$data2=chargeMat($res);
		for($j=0;$j<count($data2);$j++) {
			$periode=dateForm($data2[$j][0])." au ".dateForm($data2[$j][1]);
			if (trim($periode) != "au") {
				history_entreprise($identreprise,$ideleve,$periode);
			}
		}
	}	
	suppEleveStage($ideleve);
}


function listingHistorique($identreprise) {
	global $cnx;
	global $prefixe;
	$sql="SELECT identreprise,nomprenomeleve,classeeleve,periodestage FROM ${prefixe}stage_history WHERE identreprise='$identreprise'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}

function insert_matiere_gep($matiere) {
		global $cnx;
		global $prefixe;
		$sql="SELECT * FROM ${prefixe}matieres WHERE  libelle='$matiere' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		if (count($data) <= 0) {
			$sql="INSERT INTO ${prefixe}matieres (libelle) VALUES ('$matiere')";
			return(execSql($sql));
		}

}


function import_classe_ipac($classe) {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}classes (libelle,offline,idsite) VALUES ('$classe','0','1')";
	execSql($sql);
}

function chercheMatiereEn($idMatiere) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle_en FROM ${prefixe}matieres WHERE  code_mat='$idMatiere' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}

function import_matiere_ipac($matiere_fr,$matiere_en,$code_matiere) {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}matieres (libelle,libelle_en,sous_matiere,code_matiere) VALUES ('$matiere_fr','$matiere_en','0','$code_matiere')";
	execSql($sql);
}

function update_matiere_ipac($idmatiere,$matiere_fr,$matiere_en,$code_matiere) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}matieres SET libelle='$matiere_fr' , libelle_en='$matiere_en' , code_matiere='$code_matiere' WHERE  code_mat='$idmatiere' ";
	execSql($sql);
}

function ajout_prof_gep($nom1,$prenom1,$naissance1,$civil1,$tel1,$rue1,$adres1,$ccp1,$ville1,$mdp1) {
		global $cnx;
		global $prefixe;
		$sql="SELECT * FROM ${prefixe}personnel WHERE  nom='$nom1' AND prenom='$prenom1' AND type_pers='ENS' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		if (count($data) <= 0) {
			$mdp1=cryptage($mdp1);
			$sql="INSERT INTO ${prefixe}personnel (nom,prenom,mdp,type_pers,civ,adr,code_post,commune,tel) VALUES ('$nom1','$prenom1','$mdp1','ENS','$civil1','$rue1 $adres1','$ccp1','$ville1','$tel1')";
			return(execSql($sql));
		}
}

function recherche_codeP_ent() {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_p FROM ${prefixe}stage_entreprise ORDER BY code_p ";
	$res=@execSql($sql);
    	$data=chargeMat2($res);
	$data=array_unique($data);	
	for ($i=0;$i<count($data);$i++) {
		if (trim($data[$i]) != "") {
			print "<option value=\"".$data[$i]."\" id='select1' >".$data[$i]."</option>";
		}
	}
}

function liste_eleve_entreprise($idclasse='') {
        global $cnx;
        global $prefixe;
        $datedujour=dateDMY2();
        if ($idclasse > 0) {
                $sql="SELECT s.id,s.id_eleve,s.id_entreprise,s.id_prof_visite,s.lieu_stage,s.visite_effectuer,s.ville_stage,s.code_p,s.tuteur_stage,s.jour_repos,s.info_plus,s.loger,s.nourri,s.passage_x_service,s.raison,s.date_visite_prof,s.num_stage FROM  ${prefixe}stage_eleve s, ${prefixe}eleves e WHERE e.classe='$idclasse' AND s.id_eleve = e.elev_id ORDER BY e.nom";
        }else{
                $sql="SELECT id,id_eleve,id_entreprise,id_prof_visite,lieu_stage,visite_effectuer,ville_stage,code_p,tuteur_stage,jour_repos,info_plus,loger,nourri,passage_x_service,raison,date_visite_prof,num_stage FROM  ${prefixe}stage_eleve ORDER BY id_entreprise";
        }
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function liste_eleve_entreprise_limit($offset,$limit) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,id_eleve,id_entreprise,id_prof_visite,lieu_stage,visite_effectuer,ville_stage,code_p,tuteur_stage,jour_repos,info_plus,loger,nourri,passage_x_service,raison,date_visite_prof,num_stage,alternance,jour_alternance,dateDebutAlternance,dateFinAlternance  FROM ${prefixe}stage_eleve ORDER BY id_entreprise LIMIT $offset,$limit ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;

}

function iso2ascii($s) {
	$iso="áèïéìíåµòóø¹»úùý¾äëöüÁÈÏÉÌÍÅ¥ÒÓØ©«ÚÙÝ®ÄËÖÜ";
	$asc="acdeeillnorstuuyzaeouACDEEILLNORSTUUYZAEOU";
	return strtr($s,$iso,$asc);
}


function affichageMessageSecurite() {
	include_once("./common/config2.inc.php");
	if (SECURITE == 3) { return LANGPASSG1; }
	if (SECURITE == 2) { return LANGPASSG4; }
	if (SECURITE == 1) { return LANGPASSG5; }	
}

function affichageMessageSecurite2() {
	include_once("./common/config2.inc.php");
	if (SECURITE == 3) { return LANGPASSG2; }
	if (SECURITE == 2) { return LANGPASSG6; }
	if (SECURITE == 1) { return LANGPASSG7; }	
}

function affichageMessageSecurite3() {
	include_once("./common/config2.inc.php");
	if (SECURITE == 3) { return LANGMODIF22_3; }
	if (SECURITE == 2) { return LANGMODIF22_2; }
	if (SECURITE == 1) { return LANGMODIF22_1; }	
}

function affichageMessageSecuriteAdmin1() {
	include_once("../common/config2.inc.php");
	if (SECURITE == 3) { return LANGPASSG2; }
	if (SECURITE == 2) { return LANGPASSG6; }
	if (SECURITE == 1) { return LANGPASSG7; }	
}

function verifpasswd($passwd,$idmess,$idpers) {
	global $cnx;
	global $prefixe;
	if ($idpers != "") {
		$sql2="AND destinataire='$idpers' ";
	}else{
		$sql2="";
	}
	$sql="SELECT destinataire,type_personne_dest,idforward_mail FROM ${prefixe}messageries WHERE  idforward_mail='$idmess'  $sql2 ";	
	$res=execSql($sql);
	$data=chargeMat($res);
	if (($data[0][1] == "ADM") || ($data[0][1] == "MVS") || ($data[0][1] == "ENS") || ($data[0][1] == "TUT") || ($data[0][1] == "PER")  ) {
		$sql="SELECT pers_id,mdp FROM  ${prefixe}personnel  WHERE pers_id='".$data[0][0]."' ";	
	}elseif ($data[0][1] == "ELE") {
		$sql="SELECT elev_id,passwd_eleve FROM  ${prefixe}eleves WHERE elev_id='".$data[0][0]."' ";
	}elseif ($data[0][1] == "PAR") {
		$sql="SELECT elev_id,passwd  FROM  ${prefixe}eleves WHERE elev_id='".$data[0][0]."' ";
	}
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data[0][1];
}

function verifmessage($idmess,$idpers) {
	global $cnx;
	global $prefixe;
	if ($idpers != "") {
		$sql2="AND destinataire='$idpers' ";
	}else{
		$sql2="";
	}
	$sql="SELECT idforward_mail FROM ${prefixe}messageries WHERE  idforward_mail='$idmess' $sql2 ";
	$res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0) {
		return 1;
	}else{
		return 0;
	}


}

function enreg_param($mail,$sms,$rss,$id_pers,$membre,$info,$numero) {
	global $cnx;
	global $prefixe;
	if ($info != "rien") {
		$sql="DELETE FROM ${prefixe}avertissement WHERE id_pers='$id_pers' AND type_pers='$membre' AND parametrage='$info' ";
		@execSql($sql);
		if ($mail != "") {
			$sql="INSERT INTO ${prefixe}avertissement (id_pers,type_pers,parametrage,valeur) VALUE ('$id_pers','$membre','$info','$mail')";
			execSql($sql);
		}
		if ($sms != "") {
			$sql="INSERT INTO ${prefixe}avertissement (id_pers,type_pers,parametrage,valeur) VALUE ('$id_pers','$membre','$info','$sms/$numero')";
			execSql($sql);
		}
		if ($rss != "") {
			$sql="INSERT INTO ${prefixe}avertissement (id_pers,type_pers,parametrage,valeur) VALUE ('$id_pers','$membre','$info','$rss')";
			execSql($sql);
		}
		return 1;
	}
}


function recherche_param($id_pers,$info,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT parametrage,valeur FROM ${prefixe}avertissement WHERE id_pers='$id_pers' AND parametrage='$info' AND type_pers='$membre'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function create_rss($koi,$nom,$prenom,$descriptionItem) {
	
	//create_rss("reservation",$_SESSION["nom"],$_SESSION["prenom"],$_SESSION["membre"],$_SESSION["id_pers"],$descriptionItem);

	global $cnx;
	global $prefixe;
	include_once("./librairie_php/feedcreator.class.php");
	include_once("./librairie_php/lib_rss.php");

	$description="RSS TRIADE";
	$http=protohttps(); // return http:// ou https://
	$link="$http".$_SERVER["SERVER_NAME"]."/".ECOLE."/";
	$auteur="$nom $prenom";

	$descriptionItem=$descriptionItem."<br /><br />";

	if ($koi == "resa") { $titre="Reservation de salle / equipement"; }
	if ($koi == "actu") { $titre="Etablissement - Actualité - News"; }

	$rss = new UniversalFeedCreator(); 
	$rss->useCached(); // use cached version if age<1 hour
	$rss->title = "$titre"; 
	$rss->description = "$description"; 

	//optional
	$rss->descriptionTruncSize = 500;
	$rss->descriptionHtmlSyndicated = true;

	$rss->link = $link; 
	$rss->syndicationURL = "$http".$_SERVER["SERVER_NAME"]."/".$_SERVER["PHP_SELF"]; 

	$image = new FeedImage(); 
	$image->title = "TRIADE"; 
	$image->url = "$http".$_SERVER["SERVER_NAME"]."/".ECOLE."/image/logo_triade_licence.gif"; 
	$image->link = "http://www.triade-educ.org"; 
	$image->description = "Triade"; 

	//optional
	$image->descriptionTruncSize = 500;
	$image->descriptionHtmlSyndicated = true;

	$rss->image = $image; 

	// get your news items from somewhere, e.g. your database: 
    	$item = new FeedItem(); 
	$item->title = "$titre"; 
	$item->link = "$link"; 
	$item->description = "$descriptionItem"; 

    	//optional
    	$item->descriptionTruncSize = 500;
    	$item->descriptionHtmlSyndicated = true;

    	$item->date = $data->newsdate; 
    	$item->source = "$http".$_SERVER["server_name"]; 
    	$item->author = $auteur; 
     
    	$rss->addItem($item); 


	// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1 (deprecated),
	// MBOX, OPML, ATOM, ATOM0.3, HTML, JS

	$data=recherche_rss($koi);
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][3] == "rss") { 
			$type_membre=$data[$i][1];	
			$id_pers=$data[$i][0];	
			@mkdir("./data/rss/$type_membre");
			@mkdir("./data/rss/$type_membre/$id_pers");
			// mettre à jour
			if ($koi == "resa") {	$fichier="./data/rss/$type_membre/$id_pers/reservation.xml"; }
			if ($koi == "actu") {	$fichier="./data/rss/actualite.xml"; }
			$rss->saveFeed("RSS2.0", "$fichier");
		}
	}
}

function expediteur_triade() {
	if (defined("MAILNOMREPLY")) {
		if (trim(MAILNOMREPLY) == "") {
			return "Information Scolaire";
		}
		return MAILNOMREPLY ;
	}else{
		return "Information Scolaire";
	}
}

function create_mail($koi,$nomE,$prenomE,$descriptionItem) {
	global $cnx;
	global $prefixe;
	include_once("./common/config2.inc.php");
	if ((defined("MAILREPLY")) && (MAILREPLY != "")) {

		$data=recherche_rss($koi);
		//  id_pers,type_pers,parametrage,valeur

		for($i=0;$i<count($data);$i++) {
			if (preg_match('/\@/',$data[$i][3])) { 
				$http=protohttps(); // return http:// ou https://
				$lien="$http".$_SERVER["SERVER_NAME"]."/".ECOLE."/";
				$nomD=recherche_personne_nom($data[$i][0],$data[$i][1]);
				$prenomD=recherche_personne_prenom($data[$i][0],$data[$i][1]);
$message="
<br /><br />
 ACTUALITE / NEWS / INFO <br />
========================== <br />
<br />

    Bonjour $prenomD $nomD ,


Description : <br />

$descriptionItem

<br /><br />NE PAS FAIRE REPONDRE VIA VOTRE MESSAGERIE<br /><br />
____________________________________________________________<br />
<br />
<br >

        Hello $prenomD $nomD,<br />
<br />

Information : <br />

$descriptionItem


<br /><br />DO NOT REPLYTO THIS MESSAGE USING YOUR EMAIL ACCOUNT.<br />
";
				$messageTEXT=preg_replace("/\&nbsp;/"," ",$message);
                                $to = $data[$i][3] ;
                                $sujet = "Actualite / news (Mail Auto-Triade)";
                                $nom_expediteur=expediteur_triade();
                                $email_expediteur=MAILREPLY;
//                                $message=TextNoAccent($message);

                                $ret="\n";
                                if (PHP_OS == "WINNT") {  $ret="\r\n"; }

                                $from = 'From: "'.$nom_expediteur.'" <'.$email_expediteur.'>'."$ret";
                                $headers=$from;
                                if (ValideMail($to)) {
                                        $reply=$email_expediteur;
                                        $from=$email_expediteur;
                                        $fichierjoint="";
                                        mailTriade($sujet,$messageTEXT,$message,$to,$from,$reply,$nom_expediteur,$fichierjoint);
				}
			}
		}
	}
}



function recherche_rss($koi) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_pers,type_pers,parametrage,valeur  FROM ${prefixe}avertissement WHERE parametrage='$koi'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function prevenir($koi,$nom,$prenom,$descriptionItem) {
	/**
	 * $koi valeur possible : actu , resa
	 * prevenir("actu",$_SESSION["nom"],$_SESSION["prenom"],stripSlashes($_POST["resultat"]));
	 */
	create_rss($koi,$nom,$prenom,$descriptionItem);	
	create_mail($koi,$nom,$prenom,$descriptionItem);
}


function supp_info_util_agenda($membre,$idpers) {
	global $cnx;
	global $prefixe;
	$utillogin="$membre$idpers"; // menueleve663
	$sql="SELECT util_id FROM  ${prefixe}px_utilisateur WHERE util_login='$utillogin' "; 
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$util_id=$data[0][0];
	}else{
		return 1;
	}


	$sql="DELETE FROM  ${prefixe}px_favoris WHERE fav_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_agenda  WHERE age_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_agenda_concerne   WHERE aco_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_calepin   WHERE cal_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_calepin_groupe WHERE cgr_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_libelle WHERE lib_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_memo WHERE mem_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_planning_affecte WHERE paf_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_planning_partage WHERE ppl_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_sid  WHERE sid_util_id='$util_id'";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_utilisateur  WHERE util_id='$util_id'";
	@execSql($sql);
}

function purgeagenda() {
	global $cnx;
	global $prefixe;

	$sql="DELETE FROM  ${prefixe}px_favoris ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_agenda ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_agenda_concerne  ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_calepin   ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_calepin_groupe  ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_information ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_libelle ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_memo ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_planning_affecte ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_planning_partage ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_sid ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_utilisateur ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_calepin_appartient  ";
	@execSql($sql);
	$sql="DELETE FROM  ${prefixe}px_information   ";
	@execSql($sql);
}

function vide_eleves_agenda() {
	global $cnx;
	global $prefixe;
	$sql="SELECT util_id FROM  ${prefixe}px_utilisateur   WHERE  util_nom LIKE '%)' "; 
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		for ($i=0;$i<count($data);$i++) {
			$util_id=$data[$i][0];
			$sql="DELETE FROM  ${prefixe}px_favoris WHERE fav_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_agenda  WHERE age_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_agenda_concerne   WHERE aco_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_calepin   WHERE cal_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_calepin_groupe  WHERE cgr_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_libelle WHERE lib_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_memo WHERE mem_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_planning_affecte WHERE paf_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_planning_partage WHERE ppl_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_sid  WHERE sid_util_id='$util_id'";
			@execSql($sql);
			$sql="DELETE FROM  ${prefixe}px_utilisateur  WHERE util_id='$util_id'";
			@execSql($sql);
		}
	}
}



function verif_table_matiere() {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_mat  FROM ${prefixe}matieres WHERE sous_matiere IS NULL";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		for($i=0;$i<count($data);$i++) {
			$code=$data[$i][0];
			$sql="UPDATE ${prefixe}matieres SET sous_matiere=' ' WHERE code_mat='$code'";
		        $ins=execSql($sql);
		}
	}
}

function supp_eleve_sansclass($ideleve) {
	global $cnx;
	global $prefixe;
	@suppression_eleve($ideleve);
	$sql="DELETE FROM ${prefixe}elevessansclasse WHERE elev_id='$ideleve' ";
	@execSql($sql);
}

function enr_statUtilisateur($nom,$prenom,$id_pers,$membre,$id_session) {
	global $cnx;
	global $prefixe;
	$nom=strtolower($nom);
	$sql="SELECT date_entree,nom,prenom,idpers,type_membre,id_session,nb_conx,der_conx   FROM  ${prefixe}statUtilisateur  WHERE  nom='$nom' AND prenom='$prenom' AND idpers='$id_pers' AND type_membre='$membre' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data > 0) {
		$nbconx=$data[0][6]+1;
		$sql="UPDATE ${prefixe}statUtilisateur SET nb_conx='$nbconx',id_session='$id_session' WHERE nom='$nom' AND prenom='$prenom' AND idpers='$id_pers' AND type_membre='$membre' ";
		execSql($sql);
	}
}

function mise_statUtilisateur($nom,$prenom,$id_pers,$membre,$id_session) {
	global $cnx;
	global $prefixe;
	$time=date('d/m/Y H:i:s');
	$sql="UPDATE ${prefixe}statUtilisateur SET der_conx='$time' WHERE id_session='$id_session' AND nom='$nom' AND prenom='$prenom' AND idpers='$id_pers' AND type_membre='$membre' ";
	execSql($sql);
}

function verif_profp_eleve($eid,$idprof,$membre) {
	global $cnx;
	global $prefixe;
	if ($membre == "menuprof") {
		$sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$idprof' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		$ok=0;
		if (count($data) > 0) {
			for ($i=0;$i<count($data);$i++) {
				$idclasse=$data[$i][1];
				$sql="SELECT elev_id FROM ${prefixe}eleves WHERE classe='$idclasse' AND elev_id='$eid' ";
			       	$res=execSql($sql);
				$data2=chargeMat($res);
				if (count($data2) > 0 ) {
					$ok=1;
					break;
				}	
			}
		}
	}elseif($membre == "menuadmin") {
		$ok=1;
	}elseif($membre == "menuscolaire") {
		$ok=0;
	}else{	
		$ok=0;
	}
	if ($ok == 0) {
		blacklist();
	}
}

function verif_profp_eleve2($eid,$idprof) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$idprof' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		for ($i=0;$i<count($data);$i++) {
			$idclasse=$data[$i][1];
			$sql="SELECT elev_id FROM ${prefixe}eleves WHERE classe='$idclasse' AND elev_id='$eid' ";
		       	$res=execSql($sql);
			$data2=chargeMat($res);
			if (count($data2) > 0 ) {
				return true;
			}	
		}
		return false;
	}else{
		return false;
	}
}

function verif_profp_class($eid,$idclasse,$anneeScolaire="") {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$eid' AND idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 0) {
                return "";
        }else{
                $id_pers=verif_si_suppleant($_SESSION["id_suppleant"]);
                if ($id_pers == "compteexpire") blacklist();
                $sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$_SESSION[id_suppleant]' AND idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
                $res=execSql($sql);
                $data=chargeMat($res);
                if (count($data) > 0) {
                        return "";
                }
                blacklist();
        }
}


function verif_profp_ens($eid) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$eid' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return "";
	}else{
		$id_pers=verif_si_suppleant($_SESSION["id_suppleant"]);
		if ($id_pers == "compteexpire") blacklist();
		$sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$_SESSION[id_suppleant]'";
		$res=execSql($sql);
		$data=chargeMat($res);
		if (count($data) > 0) {
			return "";
		}	
		blacklist();
	}
}




function verif_profp_class_sans_blacklist($eid,$idclasse,$anneeScolaire="") {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$eid' AND idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return "ok";
	}else{
		$id_pers=verif_si_suppleant($_SESSION["id_suppleant"]);
		if ($id_pers == "compteexpire") blacklist();
		$sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$_SESSION[id_suppleant]' AND idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		if (count($data) > 0) {
			return "ok";
		}	
		return "nook";
	}
}

function supp_parametrage_bulletin($libelle,$idclasse) {
        global $cnx;
        global $prefixe;
        $sql="DELETE FROM ${prefixe}parametrage WHERE libelle='$libelle' AND idclasse='$idclasse'";
        execSql($sql);
}


function verifSiEleveEnStage($ideleve,$date) {
	global $cnx;
	global $prefixe;
	$elements=preg_split('/\//',$date);
	$date=dateFormBase($date);
        $annee=$elements[2];
	$mois=$elements[1];
	$jour=$elements[0];
	$resultat=mktime(0,0,0,$mois,$jour,$annee);
	$jourdelasemaine=strftime("%w",$resultat);

	$sql="SELECT num_stage,alternance,id FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) == 0) {
		return(0);
	}else{
		for($i=0;$i<count($data);$i++) {
			$alternance=$data[$i][1];
			$num_stage=$data[$i][0];
			if ($alternance == 1) {
				$id=$data[$i][2];		
				$sql="SELECT jour_alternance FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve' AND dateDebutAlternance <= '$date'  AND dateFinAlternance >= '$date' AND  jour_alternance LIKE '%$jourdelasemaine%'";
				$res=execSql($sql);
				$data2=chargeMat($res);		
				return($data2[0][0]);
			}else{
				$sql="SELECT count(d.id) FROM ${prefixe}stage_date d, ${prefixe}stage_eleve s WHERE d.id='$num_stage' AND d.datedebut  <= '$date'  AND d.datefin >= '$date' AND id_eleve='$ideleve' AND s.num_stage=d.id";
				$res=execSql($sql);
				$data2=chargeMat($res);	
				return($data2[0][0]);
			}
		}
	}
}

function nbrEleveEnStageAujourdhui($tabeleve) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$jourdelasemaine=dateJourSemaine();
	
	$total=0;
	foreach($tabeleve as $key=>$ideleve) {
		$sql="SELECT num_stage,alternance,id FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve'";
		$res=execSql($sql);
		$data2=chargeMat($res);
		if (count($data2) == 0) {
			continue;
		}else{
			for($ii=0;$ii<count($data2);$ii++) {
				$alternance=$data2[$ii][1];
				$num_stage=$data2[$ii][0];
				//$ideleve=$data2[$ii][3];
				if ($alternance == 1) {
					$id=$data[$ii][2];		
					$sql="SELECT jour_alternance FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve' AND dateDebutAlternance <= '$date'  AND dateFinAlternance >= '$date' AND  jour_alternance LIKE '%$jourdelasemaine%'";
					$res=execSql($sql);
					$data2=chargeMat($res);		
					if (count($data2)) { $total+=1; break; }
				}else{
					$sql="SELECT d.id FROM ${prefixe}stage_date d, ${prefixe}stage_eleve s WHERE d.id='$num_stage' AND d.datedebut  <= '$date'  AND d.datefin >= '$date' AND id_eleve='$ideleve' AND s.num_stage=d.id";
					$res=execSql($sql);
					$data2=chargeMat($res);	
					if (count($data2)) { $total+=1; break; }
				}
			}
		}
	}
	return($total);
}

 
function verif_profp_class2($eid,$idclasse,$anneeScolaire="") {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT idprof,idclasse  FROM ${prefixe}prof_p WHERE idprof='$eid' AND idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return true;
	}else{
	 	return false;
	}
}

function envoi_message_par_mail($sujet,$message,$to,$from,$nom_expediteur) {
	$messageT=html_vers_text($message);	
//	$messageT=TextNoAccent($messageT);
	$sujet=TextNoAccent($sujet);
	mailTriade($sujet,$messageT,$message,$to,$from,$from,$nom_expediteur,"");
}

function mailTriade($sujet,$message1,$message2,$to,$from,$reply,$nom_expediteur,$fichierjoint) {


	$nom_expediteur=MAILNOMREPLY;
	//---------------------
	//DECLARE LES VARIABLES
	//---------------------
	$sujet=stripslashes($sujet);
	$email_expediteur=MAILREPLY;
	$email_reply=$from;
	$destinataire=$to;

        $message1=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$message1);
        $message1=preg_replace('/\\\\\\\/','',$message1);
        $message2=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$message2);
        $message2=preg_replace('/\\\\\\\/','',$message2);


	$message_texte=$message1;
	$message_html=$message2;

	$ret="\n";
	if (PHP_OS == "WINNT") {  $ret="\r\n"; }

	$piedmessagetexte="

------------------------------------------------------------------------------
  Conformement aux dispositions de la loi Informatiques et Libertes du 6
Janvier 1978, vous disposez d'un droit d'acces, de rectification, de
modification et de suppression des donnees vous concernant.
Pour toute question relative aux donnees personnelles ou pour exercer
vos droits au titre de la loi Informatiques et Libertes, vous pouvez
contacter l'administrateur Triade de votre etablissement scolaire.
-------------------------------------------------------------------------------
";
	$piedmessagehtml="<br><br>
<hr>
<i>Conformement aux dispositions de la loi Informatiques et Libertes du 6
Janvier 1978, vous disposez d'un droit d'acces, de rectification, de
modification et de suppression des donnees vous concernant.
Pour toute question relative aux donnees personnelles ou pour exercer
vos droits au titre de la loi Informatiques et Libertes, vous pouvez
contacter l'administrateur Triade de votre etablissement scolaire.</i>
<hr><br>
<br>
T.R.I.A.D.E.  http://www.triade-educ.org / Gestion Scolaire via Internet<br>
<br>
";



	$piedmessage2="
_________________________________________________________________________________________________________
---> T.R.I.A.D.E.  http://www.triade-educ.org / Gestion Scolaire via Internet <----
";

        //-----------------------------------------------
        //GENERE LA FRONTIERE DU MAIL ENTRE TEXTE ET HTML
        //-----------------------------------------------

        //Test Thomas Trachet
        //Ligne suivante
        //$frontiere = '-----=' . md5(uniqid(mt_rand()));
        //Remplacee par :
        $frontiere = md5(uniqid(mt_rand()));

        //-----------------------------------------------
        //HEADERS DU MAIL
        //-----------------------------------------------
	if (trim($nom_expediteur) == "") { $nom_expediteur=$email_expediteur; }

        $headers = 'From: "'.$nom_expediteur.'" <'.$email_expediteur.'>'."$ret";
        $headers .= 'Return-Path: <'.$email_reply.'>'."$ret";
        $headers .= 'MIME-Version: 1.0'."$ret";
        $headers .= 'Content-Type: multipart/mixed; boundary="'.$frontiere.'"';

        //-----------------------------------------------
        //MESSAGE TEXTE
        //-----------------------------------------------
        $message_texte.=$piedmessagetexte;
        $message_texte.=$piedmessage2;
        $message_texte=stripslashes($message_texte);
        $message_texte=preg_replace('/&nbsp;/',' ',$message_texte);
        $message_texte=preg_replace("/<[^>]*>/", "", $message_texte);


        //Test Thomas Trachet
        //Ligne suivante
        //$message = 'This is a multi-part message in MIME format.'."$ret$ret";
        //Remplacee par :
        $message = 'This is a multi-part message in MIME format.'."$ret";
        //$message .= '--'.$frontiere."$ret";
        //$message .= 'Content-Type: text/plain; charset="iso-8859-1"'."$ret";
        //$message .= 'Content-Transfer-Encoding: 8bit'."$ret$ret";
       // $message .= TextNoAccent($message_texte)."$ret$ret";

        //-----------------------------------------------
        //MESSAGE HTML
        //-----------------------------------------------
        $message_html.="$piedmessagehtml";
        $message_html=stripslashes($message_html);
        $http=protohttps(); // return http:// ou https://
        $serveur="src=\"$http".$_SERVER["SERVER_NAME"];
//        $message_html=preg_replace('/src=/',$serveur,$message_html);
//        $message_html=preg_replace('/"\//','/',$message_html);

        $message .= '--'.$frontiere."$ret";
        $message .= 'Content-Type: text/html; charset="iso-8859-1"'."$ret";
        $message .= 'Content-Transfer-Encoding: 8bit'."$ret$ret";
        $message .= $message_html."$ret$ret";

        //-----------------------------------------------
        //PIECE JOINTE
        //-----------------------------------------------
        if ($fichierjoint != "") {
		if (preg_match('/,/',$fichierjoint)) {
			$tabfichierjoint=explode(",",$fichierjoint);
			foreach ($tabfichierjoint as $key=>$fichier) {
		                $message .= '--'.$frontiere."$ret";
				$file_type = filetype($fichier);
				$fichierName=basename($fichier);
		                $message .= 'Content-Type: '.$file_type.'; name="'.$fichierName.'"'."$ret";
		                $message .= 'Content-Transfer-Encoding: base64'."$ret";
		                $message .= 'Content-Disposition:attachement; filename="'.$fichierName.'"'."$ret$ret";
		                $message .= chunk_split(base64_encode(file_get_contents($fichier)))."$ret";
			}
		}else{
			$fichier=$fichierjoint;
			$message .= '--'.$frontiere."$ret";
                        $file_type = filetype($fichier);
			$fichierName=basename($fichier);
                        $message .= 'Content-Type: '.$file_type.'; name="'.$fichierName.'"'."$ret";
                        $message .= 'Content-Transfer-Encoding: base64'."$ret";
                        $message .= 'Content-Disposition:attachement; filename="'.$fichierName.'"'."$ret$ret";
                        $message .= chunk_split(base64_encode(file_get_contents($fichier)))."$ret";
		}
        }

        //Test Thomas Trachet
        //Ligne ajoutee :
        $message .= '--'.$frontiere.'--'."$ret";
	if (preg_match('/,/',$destinataire)) {
		mail($destinataire,$sujet,$message,$headers); 
		history_cmd("MAILING","MAILING","Email envoyé à $destinataire");
	}else{
		if (ValideMail($destinataire)) { 
			mail($destinataire,$sujet,$message,$headers); 
	//	print "mail($destinataire,$sujet,$message,$headers)";
			history_cmd("MAILING","MAILING","Email envoyé à $destinataire");
		}
	}
}

function arrondi($note) {
	if (preg_match("/50$/",$note)) { return $note; }	
	if (preg_match("/00$/",$note)) { return	number_format($note,0,'',''); }	
	list($dix,$cent)=preg_split('/\./',$note);
	if (($cent >= 01) && ($cent <= 25)) { return  floor(number_format($note,0,'','')); } // xx.01 xx.25
	if (($cent >= 26) && ($cent < 50))  { return  preg_replace("/(.*\.)[0-9][0-9]/","\\150",$note); } // xx.26 xx.49
	if (($cent > 50) && ($cent <= 75))  { return  preg_replace("/(.*\.)[0-9][0-9]/","\\150",$note); } // xx.51 xx.75
	if (($cent <= 99) && ($cent >= 76))   { return  ceil(number_format($note,0,'','')); }  // xx.76 xx.99
	return $note;
}

function arrondiaudixieme($valeur) {
	if (preg_match('/,/',$valeur)) { $valeur=preg_replace('/,/','.',$valeur); }
	$valeur=round($valeur,1);
	$valeur=sprintf("%01.1f",$valeur);
	list($unite,$dixaine)=preg_split('/\./',$valeur);
/*	if ($dixaine == "00") {
		$dixaine=0;
	}elseif ($unite == 20) {
		$dixaine=0;
		$unite=20;
	}elseif ($dixaine > 50) { 
		$dixaine=0;  
	}else { 
		$dixaine=5; 
	}
*/
	if ($unite < 10) { $unite="0".$unite; } 
	$valeur=$unite.".".$dixaine;
	return $valeur;
}


/* ------------------------------- */

/**
 * @author olive
 * @copyright 2008
 */

function arrondiOlive($note) {
    $n=floor($note);
    if ($n-0.25<=$note or $note<$n+0.25) {
        return $n;
    }    
    else {
        return    $n+0.5;
    }    
}

/* ------------------------------- */

function verifNomSujet($nom,$date,$idclasse,$idgroupe,$idmatiere) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$nom=addslashes($nom);
	$sql="SELECT * FROM ${prefixe}notes WHERE sujet='$nom' AND date='$date' AND ((id_classe='$idclasse' AND id_groupe='$idgroupe') OR (id_classe='-1' AND id_groupe='$idgroupe'))  AND  code_mat='$idmatiere' ";
	$res=execSql($sql);
	$data=chargeMat($res);	
	if (count($data)) {
		return true;
	}else{
		return false;
	}

}


function telechargerFichier($chemin){
 if ((!file_exists($chemin)) && (!@fclose(@fopen($chemin, "r")))) die('Erreur:fichier incorrect');
 $filename = stripslashes(basename($chemin));
 $user_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
 header("Content-type: application/force-download");
 header(((is_integer(strpos($user_agent,"msie")))&&(is_integer(strpos($user_agent, "win"))))?"Content-Disposition:filename=\"$filename\"":"Content-Disposition: attachment; filename=\"$filename\"");
 header("Content-Description: Telechargement de Fichier");
 @readfile($chemin);
 die();
}

function tailleFichier($fichier){
	$size_unit="o";
	$taille=filesize($fichier);
	if ($taille >= 1073741824) {$taille = round($taille / 1073741824 * 100) / 100 . " G".$size_unit;}
	elseif ($taille >= 1048576) {$taille = round($taille / 1048576 * 100) / 100 . " M".$size_unit;}
	elseif ($taille >= 1024) {$taille = round($taille / 1024 * 100) / 100 . " K".$size_unit;}
	else {$taille = $taille . " ".$size_unit;} 
	if($taille==0) {$taille="-";}
	return $taille;
}

function select_classe_profp($idprof) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,idclasse FROM ${prefixe}prof_p WHERE idprof='$idprof' ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	// patch pour problème sous-matière à 0
	for($i=0;$i<count($data);$i++){
		$nomclasse=chercheClasse($data[$i][1]);
		$nomclasse=$nomclasse[0][1];
		$option.="<option STYLE='color:#000066;background-color:#CCCCFF' value='".$data[$i][1]."'>$nomclasse</option>\n";
	}
	// fin patch
	freeResult($curs);
	unset($curs);
	print $option;
}


function affichage_derniereconx($type_personne,$idpers) {
	global $cnx;
	global $prefixe;
	if ($type_personne == "ADM") {$type_personne="menuadmin";}
	if ($type_personne == "ENS") {$type_personne="menuprof";}
	if ($type_personne == "MVS") {$type_personne="menuscolaire";}
	if ($type_personne == "PAR") {$type_personne="menuparent";}
	if ($type_personne == "ELE") {$type_personne="menueleve";}
	if ($type_personne == "TUT") {$type_personne="menututeur";}
	if ($type_personne == "PER") {$type_personne="menupersonnel";}
	$sql="SELECT nb_conx,der_conx  FROM ${prefixe}statUtilisateur WHERE idpers='$idpers' AND type_membre='$type_personne'   ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	if (count($data) > 0) {
		if ($data[0][1] == NULL) {
			return "- ".LANGMESS42;
		}else{
			return "- ".LANGMESS43." ".timeForm($data[0][1]);
		}
	}
}


function create_comm_direc_bull($eleveid,$tri,$com,$montessori,$type_bulletin,$leap_felicitation,$leap_encouragement,$leap_megcomp,$leap_megtrav,$jtc_promu,$jtc_reprendre,$jtc_orientation,$pp_av_trav,$pp_av_comp,$pp_enc,$pp_feli,$ppv2_av,$ppv2_faible,$ppv2_passable,$ppv2_enc,$ppv2_feli,$anneeScolaire) {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	if (trim($tri) == "") return ;
        if ($anneeScolaire != "") {
                $sql="DELETE FROM ${prefixe}bulletin_direction_com WHERE ideleve='$eleveid' AND trimestre='$tri' AND type_bulletin='$type_bulletin' AND annee_scolaire='$anneeScolaire' ";
                execSql($sql);
                $sql="INSERT INTO ${prefixe}bulletin_direction_com (ideleve,trimestre,commentaire,montessori,type_bulletin,leap_encouragement,leap_felicitation,leap_meg_comp,leap_meg_trav,jtc_promu,jtc_reprendre,jtc_orientation,pp_av_trav,pp_av_comp,pp_enc,pp_feli,ppv2_av,ppv2_faible,ppv2_passable,ppv2_enc,ppv2_feli,annee_scolaire) VALUES ('$eleveid','$tri','$com','$montessori','$type_bulletin','$leap_encouragement','$leap_felicitation','$leap_megcomp','$leap_megtrav','$jtc_promu','$jtc_reprendre','$jtc_orientation','$pp_av_trav','$pp_av_comp','$pp_enc','$pp_feli','$ppv2_av','$ppv2_faible','$ppv2_passable','$ppv2_enc','$ppv2_feli','$anneeScolaire')";
                return(execSql($sql));
        }
}

function modifLeap($eleveid,$tri,$com,$montessori,$type_bulletin,$leap_felicitation,$leap_encouragement,$leap_megcomp,$leap_megtrav,$anneeScolaire="") {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT * FROM ${prefixe}bulletin_direction_com WHERE ideleve='$eleveid' AND trimestre='$tri' AND type_bulletin='$type_bulletin' AND annee_scolaire='$anneeScolaire' ";
        $curs=execSql($sql);
        $data=chargeMat($curs);
        if (count($data)) {
                if ($anneeScolaire == "2014 - 2015") {
                        $sql="UPDATE ${prefixe}bulletin_direction_com SET leap_encouragement='$leap_encouragement' , leap_felicitation='$leap_felicitation', leap_meg_comp='$leap_megcomp' , leap_meg_trav='$leap_megtrav' WHERE ideleve='$eleveid' AND trimestre='$tri' AND type_bulletin='$type_bulletin' AND (annee_scolaire='$anneeScolaire' OR annee_scolaire='')";
                }else{
                        $sql="UPDATE ${prefixe}bulletin_direction_com SET leap_encouragement='$leap_encouragement' , leap_felicitation='$leap_felicitation', leap_meg_comp='$leap_megcomp' , leap_meg_trav='$leap_megtrav' WHERE ideleve='$eleveid' AND trimestre='$tri' AND type_bulletin='$type_bulletin' AND annee_scolaire='$anneeScolaire'";
                }
                execSql($sql);
        }else{
                $sql="INSERT INTO ${prefixe}bulletin_direction_com (ideleve,trimestre,type_bulletin,leap_encouragement,leap_felicitation,leap_meg_comp,leap_meg_trav,annee_scolaire) VALUES ('$eleveid','$tri','$type_bulletin','$leap_encouragement','$leap_felicitation','$leap_megcomp','$leap_megtrav','$anneeScolaire')";
                execSql($sql);
        }
}


function rechercheleap($ideleve,$type_bulletin,$trimestre,$anneeScolaire="") {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT leap_encouragement,leap_felicitation,leap_meg_comp,leap_meg_trav,pp_av_trav,pp_av_comp,pp_enc,pp_feli,ppv2_av,ppv2_faible,ppv2_passable,ppv2_enc,ppv2_feli FROM ${prefixe}bulletin_direction_com WHERE ideleve='$ideleve' AND trimestre='$trimestre' AND type_bulletin='$type_bulletin' AND annee_scolaire='$anneeScolaire' ";
        $curs=execSql($sql);
        $data=chargeMat($curs);
        return($data);
} 

function recherchepigierparis($ideleve,$type_bulletin,$trimestre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT pp_av_trav,pp_av_comp,pp_enc,pp_feli FROM ${prefixe}bulletin_direction_com WHERE ideleve='$ideleve' AND trimestre='$trimestre' AND type_bulletin='$type_bulletin' ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	return($data);
}

function recherchepigierparisv2($ideleve,$type_bulletin,$trimestre,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT ppv2_av,ppv2_faible,ppv2_passable,ppv2_enc,ppv2_feli FROM ${prefixe}bulletin_direction_com WHERE ideleve='$ideleve' AND trimestre='$trimestre' AND type_bulletin='$type_bulletin' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	return($data);
}

function recherchejtc($ideleve,$type_bulletin,$trimestre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT jtc_promu,jtc_reprendre,jtc_orientation FROM ${prefixe}bulletin_direction_com WHERE ideleve='$ideleve' AND trimestre='$trimestre' AND type_bulletin='$type_bulletin' ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	return($data);
}

function supphistoStage($identreprise,$ideleve,$classeeleve,$periode) {
	global $cnx;
	global $prefixe;	 
	$sql="DELETE FROM  ${prefixe}stage_history WHERE identreprise=$identreprise AND ideleve='$ideleve' AND classeeleve='$classeeleve' AND periodestage='$periode' ";
	@execSql($sql);
}

function recherchemontessori($ideleve,$type_bulletin,$trimestre,$anneeScolaire="") {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT montessori FROM ${prefixe}bulletin_direction_com WHERE ideleve='$ideleve' AND trimestre='$trimestre' AND type_bulletin='$type_bulletin' AND annee_scolaire='$anneeScolaire'  ";
        $curs=execSql($sql);
        $data=chargeMat($curs);
        return($data);
}
					


function create_comm_scolaire_bull($eleveid,$tri,$com,$anneeScolaire) {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire != "") {
                $sql="DELETE FROM ${prefixe}bulletin_scolaire_com WHERE ideleve='$eleveid' AND trimestre='$tri' AND annee_scolaire='$anneeScolaire'";
                execSql($sql);
                $sql="INSERT INTO ${prefixe}bulletin_scolaire_com (ideleve,trimestre,commentaire,annee_scolaire) VALUES ('$eleveid','$tri','$com','$anneeScolaire')";
                return(execSql($sql));
        }
}


function create_comm_profp_bull($eleveid,$tri,$com,$anneeScolaire,$type_com) {
        global $cnx;
        global $prefixe;
        if ($type_com == "") $type_com="default";
        if ($type_com == "leap") $type_com="default";
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($anneeScolaire != "") {
                $sql="DELETE FROM ${prefixe}bulletin_profp_com WHERE ideleve='$eleveid' AND trimestre='$tri' AND annee_scolaire='$anneeScolaire' AND type_com='$type_com' ";
                execSql($sql);
                $sql="INSERT INTO ${prefixe}bulletin_profp_com (ideleve,trimestre,commentaire,annee_scolaire,type_com) VALUES ('$eleveid','$tri','$com','$anneeScolaire','$type_com')";
                return(execSql($sql));
        }
}


function create_comm_profp_ue($eleveid,$tri,$com,$idclasse,$id_ue){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_profp_ue WHERE ideleve='$eleveid' AND tri='$tri'  AND id_ue='$id_ue'  AND idclasse='$idclasse' ";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}bulletin_profp_ue (ideleve,tri,com,id_ue,idclasse) VALUES ('$eleveid','$tri','$com','$id_ue','$idclasse')";
	return(execSql($sql));
}


function recherche_com($ideleve,$tri,$type_bulletin,$anneeScolaire="") {
        global $cnx;
	global $prefixe;
	$triO=$tri;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($tri == 'trimestre2') $triautre="Semestre 2";
        if ($tri == 'trimestre1') $triautre="Semestre 1";
        $tri=strtolower(preg_replace('/ /','',$tri));
        if ($tri == "trimestre3") $sqlsuite=" OR lower(trimestre) = 'trimestre 3'";
	$sql="SELECT commentaire FROM  ${prefixe}bulletin_direction_com WHERE ideleve='$ideleve' AND (lower(trimestre)='$tri' OR  trimestre='$triO' OR trimestre='$triautre' $sqlsuite ) AND type_bulletin='$type_bulletin' AND annee_scolaire='$anneeScolaire' ";
        $curs=execSql($sql);
        $data=chargeMat($curs);
        if (count($data) > 0) {
                return html_vers_text($data[0][0]);
        }else {
                return "";
        }
}




function recherche_com_scolaire($ideleve,$tri,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT commentaire FROM  ${prefixe}bulletin_scolaire_com WHERE ideleve='$ideleve' AND trimestre='$tri' AND annee_scolaire='$anneeScolaire'";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	if (count($data) > 0) {
		return html_vers_text($data[0][0]);
	}else {
		return "";
	}
}

function recherche_com_profP($ideleve,$tri,$anneeScolaire='',$type_com='default') {
        global $cnx;
        global $prefixe;
        if ($type_com == 'leap') $type_com='default';
        if (trim($anneeScolaire) == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT commentaire FROM  ${prefixe}bulletin_profp_com WHERE ideleve='$ideleve' AND trimestre='$tri' AND annee_scolaire='$anneeScolaire' AND type_com='$type_com' ";
        $curs=execSql($sql);
        $data=chargeMat($curs);
        if (count($data) > 0) {
                return html_vers_text($data[0][0]);
        }else{   
                return "";
        }
}



function recherche_com_profp_ue($ideleve,$tri,$code_eu,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT com FROM  ${prefixe}bulletin_profp_ue WHERE ideleve='$ideleve' AND tri='$tri' AND idclasse='$idclasse' AND id_ue='$code_eu'  ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	if (count($data) > 0) {
		return html_vers_text($data[0][0]);
	}else {
		return "";
	}
}


function nb_de_commentaire($idMatiere,$idClasse,$tri,$idprof,$idgroupe,$anneeScolaire='') {
        global $cnx;
        global $prefixe;
        if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT count(idmatiere) FROM ${prefixe}bulletin_prof_com WHERE  idmatiere='$idMatiere' AND  ( (idclasse='$idClasse' AND idgroupe='$idgroupe') OR  (idclasse='-1' AND idgroupe='$idgroupe') ) AND  trimestre='$tri' AND com != '' AND idprof='$idprof' AND annee_scolaire='$anneeScolaire' ";
        $curs=execSql($sql);
        $data=chargeMat($curs);
        return $data[0][0];
}



function nb_eleve($idClasse,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT  a.elev_id FROM ${prefixe}eleves a, ${prefixe}classes c WHERE c.code_class='$idClasse' AND a.classe='$idClasse'  AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return count($data);
}

function nb_eleve_groupe($idgroupe,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT group_id,liste_elev,commentaire,libelle FROM ${prefixe}groupes WHERE group_id='$idgroupe' AND annee_scolaire='$anneeScolaire'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$liste_eleves=preg_replace("/\{/","",$data[0][1]);
        $liste_eleves=preg_replace("/\}/","",$liste_eleves);
	$listeEleve=preg_split("/,/", $liste_eleves);
	$nb=0;
        foreach ($listeEleve as $valeur) {
        	$nb++;
        }
	return $nb;

}

function recupDateTriman($trim) {
        global $cnx;
  	global $prefixe;
        $sql="SELECT date_debut,date_fin,trim_choix FROM  ${prefixe}date_trimestrielle WHERE trim_choix<='$trim'";
        return(chargeMat(execSql($sql)));
} 


function verifNbMatiere($idClasse) {
        global $cnx;
  	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
$sql=<<<SQL
SELECT
	a.code_matiere,a.code_prof,a.ordre_affichage,
	-- libelle||' '||sous_matiere,b.code_classe
	case
		when sous_matiere = '0' then lower(trim(libelle))
		else lower(trim(libelle||' '||sous_matiere))
	end
FROM
	${prefixe}affectations a, ${prefixe}matieres m
WHERE
	a.code_classe = '$idClasse'
AND a.code_matiere = m.code_mat
AND a.annee_scolaire='$anneeScolaire'
ORDER BY
	a.ordre_affichage
SQL;
	$curs=execSql($sql);
	$ordre=chargeMat($curs);
	unset($curs);
	return count($ordre);
}

function nbMatiere2($idClasse,$anneeScolaire="") {
        global $cnx;
  	global $prefixe;
	if (trim($anneeScolaire) == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT code_classe FROM ${prefixe}affectations WHERE code_classe = '$idClasse' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$ordre=chargeMat($curs);
	return count($ordre);
}


function verifEleveExist($nom,$prenom,$naissance) {
        global $cnx;
  	global $prefixe;
	$nom=addslashes($nom);
	$prenom=addslashes($prenom);
	$sql="SELECT elev_id FROM ${prefixe}eleves WHERE nom='$nom' AND prenom='$prenom' AND date_naissance='$naissance' ";
        $curs=execSql($sql);
	$data=chargeMat($curs);
	if (count($data) > 0) {
		return $data[0][0];
	}else{
		return "rien";
	}
}

function verifEleveExistViaId($idEleve) {
        global $cnx;
  	global $prefixe;
	$nom=addslashes($prenom);
	$prenom=addslashes($nom);
	$sql="SELECT elev_id FROM ${prefixe}eleves WHERE elev_id='$idEleve'  ";
        $curs=execSql($sql);
	$data=chargeMat($curs);
	if (count($data) > 0) {
		return true;
	}else{
		return false;
	}
}

function sonore_action($titreson) {
	print "<object id='mp3player' type='application/x-shockwave-flash' data='./librairie_php/player.swf' width='0' height='0'>\n";
	print "<param name='type' value='application/x-shockwave-flash' />\n";
	print "<param name='codebase' value='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0' />\n";
	print "<param name='movie' value='./librairie_php/player.swf' />\n";
	print "<param name='FlashVars' value='my_BackgroundColor=0xdddddd&amp;my_loop=false&amp;file=./audio/".$titreson."&amp;autolaunch=true' />\n";
	print "</object>\n";
}


function enr_param_fiche_brevet_college($classe1,$classe2) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='brevet_college_c1' OR libelle='brevet_college_c2'";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage(libelle,text) VALUES ('brevet_college_c1','$classe1')";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage(libelle,text) VALUES ('brevet_college_c2','$classe2')";
	execSql($sql);
}

function enr_comport_namur($periode_1_namur,$periode_2_namur,$periode_3_namur) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='perio_1_namur' ; ";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage(libelle,text) VALUES ('perio_1_namur','$periode_1_namur')";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='perio_2_namur' ; ";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage(libelle,text) VALUES ('perio_2_namur','$periode_2_namur')";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='perio_3_namur' ; ";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage(libelle,text) VALUES ('perio_3_namur','$periode_3_namur')";
	execSql($sql);
	return 1;
}

function recup_comport_namur($libelle) {
	global $cnx;
	global $prefixe;
 	$sql="SELECT libelle,text FROM  ${prefixe}parametrage WHERE libelle='$libelle' ";
        $curs=execSql($sql);
	$data=chargeMat($curs);
	return $data[0][1];
}

function recupCreneauDefault($libelle) {
	global $cnx;
	global $prefixe;
 	$sql="SELECT libelle,text FROM  ${prefixe}parametrage WHERE libelle='$libelle' ";
        $curs=execSql($sql);
	$data=chargeMat($curs);
	return $data;
}


function chercheAnniversaire() {
	global $cnx;
	global $prefixe;
	$dateYM=dateMY2();
	$sql="SELECT elev_id,nom,prenom,classe  FROM  ${prefixe}eleves WHERE date_naissance LIKE '%$dateYM' ";
        $curs=execSql($sql);
	$data=chargeMat($curs);
	return $data;

}

function confPolice($commentaireeleve) {
	$commentaireeleve=trim($commentaireeleve);
	$len=strlen($commentaireeleve);
	if (($len > 0) && ($len < 100)) {
		$tab[0]=8; // taille de la police
		$tab[1]=3; // taille du cadre d'ecriture
        }elseif (($len >= 100) && ($len < 200)) {
                $tab[0]=7.5; // taille de la police
                $tab[1]=2.5; // taille du cadre d'ecriture		
	}elseif (($len >= 200) && ($len < 230)) {
		$tab[0]=6; // taille de la police
		$tab[1]=2.5; // taille du cadre d'ecriture
	}elseif (($len >= 230) && ($len < 300)) {
		$tab[0]=5.5; // taille de la police
		$tab[1]=2.5; // taille du cadre d'ecriture
	}elseif (($len >= 300) && ($len < 400)) {
		$tab[0]=5; // taille de la police
		$tab[1]=2.5; // taille du cadre d'ecriture
	}elseif (($len >= 400) && ($len < 500)) {
		$tab[0]=4; // taille de la police
		$tab[1]=2.5; // taille du cadre d'ecriture
	}else{
		$tab[0]=7;
		$tab[1]=2.5;
	}
	return $tab;
}


function confPoliceSeminaire($commentaireeleve) {
	$commentaireeleve=trim($commentaireeleve);
	$len=strlen($commentaireeleve);
	if (($len > 0) && ($len < 50)) {
		$tab[0]=8; // taille de la police
		$tab[1]=4; // taille du cadre d'ecriture
	}elseif (($len >= 50) && ($len < 100)) {
		$tab[0]=5; // taille de la police
		$tab[1]=2.5; // taille du cadre d'ecriture
	}elseif (($len >= 100) && ($len < 200)) {
		$tab[0]=4; // taille de la police
		$tab[1]=2.5; // taille du cadre d'ecriture
	}else{
		$tab[0]=3; // taille de la police
		$tab[1]=2.5; // taille du cadre d'ecriture
	}
	return $tab;
}

function confPolice2($commentaire) {
	$commentaire=trim($commentaire);
	$len=strlen($commentaire);
	if (($len > 0) && ($len < 261)) {
		$tab[0]=8; // taille de la police
		$tab[1]=3; // taille du cadre d'ecriture
	}elseif (($len >= 260) && ($len < 350)) {
		$tab[0]=7; 
		$tab[1]=3; 
	}elseif ($len >= 350)  {
		$tab[0]=6.5; 
		$tab[1]=3;
	}else{
		$tab[0]=7;
		$tab[1]=2.5;
	}
	return $tab;
}

function confPoliceChicago($commentaireeleve) {
	$commentaireeleve=trim($commentaireeleve);
	$len=strlen($commentaireeleve);
	if (($len > 0) && ($len <= 200)) {
		$tab[0]=10; // taille de la police
		$tab[1]=3; // taille du cadre d'ecriture
	}elseif (($len > 201) && ($len <= 300)){
		$tab[0]=8.5; 
		$tab[1]=3; 
	}elseif (($len >= 301) && ($len <= 350)) {  
		$tab[0]=8;
		$tab[1]=3;	
	}elseif ($len >= 351) {
		$tab[0]=7;
		$tab[1]=3;
	}
	return $tab;
}

function SQLite_cahierDeTextes() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,classorgrp,number,fichier,idprof,objectif,contenu,date_contenu,number_obj,fichier_obj,blocnote,visadirecteur FROM ${prefixe}cahiertexte";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	return $data;
}


function SQLite_devoirScolaire() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,id_class_or_grp,  matiere_id,  date_saisie,  heure_saisie,  date_devoir,  texte,  classorgrp,  number,  idprof , tempsestimedevoir,  visadirecteur  FROM ${prefixe}devoir_scolaire";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	return $data;
}

function SQLite_affectation() {
	global $cnx;
	global $prefixe;
	$sql="SELECT ordre_affichage, code_matiere, code_prof, code_classe, coef, code_groupe, langue, avec_sous_matiere, visubull, nb_heure, trim, ects FROM ${prefixe}affectations ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	return $data;
}


function SQLite_notes() {
	global $cnx;
	global $prefixe;
	$sql="SELECT  note_id,elev_id,prof_id,code_mat,coef,date,sujet,TRUNCATE(note,2),id_classe,id_groupe,typenote,noteexam,notationsur,notevisiblele  FROM  ${prefixe}notes";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	return $data;
}

function SQLite_entretienEleve() {
	global $cnx;
	global $prefixe;
	$sql="SELECT  ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation  FROM  ${prefixe}entretieneleve";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	return $data;
}

function SQLite_abs() {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time, justifier, heure_saisie,heuredabsence,idprof,creneaux,smsenvoye,courrierenvoyer  FROM ${prefixe}absences ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function SQLite_rtd() {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier , heure_saisie, idprof, creneaux,smsenvoye,courrierenvoyer FROM ${prefixe}retards ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function SQLite_sanction(){
	global $cnx;
	global $prefixe;
	$sql="SELECT id,id_eleve,motif,id_category,date_saisie,origin_saisie,enr_en_retenue,signature_parent,attribuer_par,devoir_a_faire,description_fait FROM ${prefixe}discipline_sanction ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;

}


function SQLite_retenu(){
	global $cnx;
	global $prefixe;
	$sql="SELECT id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire,description_fait FROM ${prefixe}discipline_retenue";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function select_classe_emploi() {
	global $cnx;
 	global $prefixe;
	$sql="SELECT code_class,libelle FROM ${prefixe}classes ORDER BY libelle";
	$res=execSql($sql);
	$data=chargeMat($res);
    	for($i=0;$i<count($data);$i++) {
		print "<option id='select1' value='".$data[$i][1]."'>".$data[$i][1]."</option>\n";
    	}
}


// ---------------------------------------------------------------------------------------------------//
function import_edt_seance($CODE,$ENSEIGNEMENT,$DATE,$HEURE,$DUREE,$idclasse,$idprof,$idmatiere,$idSalle) {
	global $cnx;
	global $prefixe;
//	$sql="DELETE FROM ${prefixe}edt_seances  WHERE code='$CODE' ";
//	execSql($sql);
	if (!preg_match("/-/",$DATE)) {
		$DATE=dateFormBase($DATE);
	}
	$HEURE=$HEURE."00";
	$DUREE=$DUREE."00";
	$ENSEIGNEMENT=htmlspecialchars($ENSEIGNEMENT);
	$data=rechercheCodeEnseignement($ENSEIGNEMENT); //  code,nom,code_matiere,duree_total,duree_chaque_seance
	$ENSEIGNEMENT=" ".addslashes($data[0][1]);
	$code_matiere=$data[0][2];
	$dataMatiere=chercheMatiereNom($code_matiere); // nom,type,code,alias
	$ENSEIGNEMENT.=" ".$dataMatiere;
	$sql="SELECT couleur FROM ${prefixe}matieres WHERE code_mat='$idmatiere'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$bgcolor=$data[0][0];
	if ($bgcolor == "") $bgcolor="#FFFFFF";
	$sql="INSERT INTO ${prefixe}edt_seances (code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,idmatiere,idressource) VALUES ('$CODE','$ENSEIGNEMENT','$DATE','$HEURE','$DUREE','$bgcolor','$idclasse','$idprof','$idmatiere','$idSalle')";
	execSql($sql);
}


function rechercheIdEdtEnseignement($code_enseignement) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_matiere FROM ${prefixe}edt_enseignement WHERE code='$code_enseignement' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}


function chercheIdEleveEdt($nom,$prenom,$naissance) {
        global $cnx;
        global $prefixe;
        $sql="SELECT elev_id FROM ${prefixe}eleves WHERE trim(lower(nom))='$nom' AND trim(lower(prenom))='$prenom' AND date_naissance='$naissance'";
        $res=execSql($sql);
        $data=chargeMat($res);
        $valeur=$data[0][0];
        return $valeur;
}


function listEdt($startOfWeek,$endOfWeek,$idclasse,$idprof,$idRessource,$accesEDT){
	global $cnx;
	global $prefixe;
	list($startOfWeek,$null)=preg_split('/ /',$startOfWeek);
	list($endOfWeek,$null)=preg_split('/ /',$endOfWeek);

        if ($idclasse == "tous") { $sqlsuite=""; }else{ $sqlsuite="AND idclasse='$idclasse'"; }
        if ($idRessource == "tous") { $sqlsuite2=""; }else{ $sqlsuite2="AND idressource='$idRessource'"; }
        if ($idRessource == "") $sqlsuite2="";

	$datefinEDT=aff_enr_parametrage("datefinEDT");
	$datedebutEDT=aff_enr_parametrage("datedebutEDT");
	$datefinEDT=preg_replace("/-/","",dateFormBase($datefinEDT[0][1]));
	$datedebutEDT=preg_replace("/-/","",dateFormBase($datedebutEDT[0][1]));

	$dateDeb=preg_replace("/-/","",$startOfWeek);
	$dateFi=preg_replace("/-/","",$endOfWeek);

	if( $accesEDT == "menuadmin") { 
                  $datedebutEDT="";
                  $datefinEDT="";
       	}

	if (trim($datedebutEDT) == "") { $datedebutEDT="00000000"; }
	if (trim($datefinEDT) == "") { $datefinEDT="99999999"; }

	if ( ($dateDeb >= $datedebutEDT) &&  ( $dateDeb <= $datefinEDT) ) {

        if  (($idclasse != "") && ($idprof != "") && ($idRessource != "")) {
                $sql="SELECT id,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,idressource,id_resa_liste,idgroupe FROM ${prefixe}edt_seances WHERE  date>='$startOfWeek' and date<='$endOfWeek' $sqlsuite $sqlsuite2 AND idprof='$idprof'  ";
        }elseif (($idclasse != "") && ($idRessource != "")) {
                $sql="SELECT id,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,idressource,id_resa_liste,idgroupe FROM ${prefixe}edt_seances WHERE  date>='$startOfWeek' and date<='$endOfWeek' $sqlsuite $sqlsuite2  ";
        }elseif (($idclasse == "") && ($idRessource == "tous")) {
                $sql="SELECT id,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,idressource,id_resa_liste,idgroupe FROM ${prefixe}edt_seances WHERE  date>='$startOfWeek' and date<='$endOfWeek' AND idressource > 0  ";
        }else{
                if ($idclasse != "") {
                        $sql="SELECT id,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,idressource,id_resa_liste,idgroupe FROM ${prefixe}edt_seances WHERE  date>='$startOfWeek' and date<='$endOfWeek' $sqlsuite $sqlsuite2 ";
                }elseif ($idprof != "") {
                        $sql="SELECT id,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,idressource,id_resa_liste,idgroupe FROM ${prefixe}edt_seances WHERE  date>='$startOfWeek' and date<='$endOfWeek' AND idprof='$idprof' ";
                }elseif ($idRessource != "") {
                        $sql="SELECT id,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,idressource,id_resa_liste,idgroupe FROM ${prefixe}edt_seances WHERE  date>='$startOfWeek' and date<='$endOfWeek' AND idressource='$idRessource' ";
                }else{
                        $sql="SELECT id,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,idressource,id_resa_liste,idgroupe FROM ${prefixe}edt_seances WHERE  date>='$startOfWeek' and date<='$endOfWeek' AND idprof IS NULL AND idclasse IS NULL AND idressource IS NULL ";
                }
        }

	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;

	}
}


function ajoutEdt($CODE,$ENSEIGNEMENT,$dateDebut,$dateFin,$bgcolor) {
	global $cnx;
	global $prefixe;

	list($DATE,$HEURE)=preg_split('/ /',$dateDebut);
	list($null,$heure2)=preg_split('/ /',$dateFin);


	$elements=preg_split('/-/',$DATE);
        $annee=$elements[0];
        $mois=$elements[1];
	$jour=$elements[2];

	$elements=preg_split('/:/',$HEURE);
	$heure=$elements[0];
	$minute=$elements[1];
	if ($minute == 29) { $minute=30; }
	$seconde=$elements[2];
	$resultat1=mktime($heure,$minute,$seconde,$mois,$jour,$annee);

	$elements=preg_split('/:/',$heure2);
	$heure=$elements[0];
	$minute=$elements[1];
	if ($minute == 29) { $minute=30; }
	$seconde=$elements[2];
	$resultat2=mktime($heure,$minute,$seconde,$mois,$jour,$annee);

	$result=$resultat2-$resultat1;
	
	$ENSEIGNEMENT=htmlspecialchars($ENSEIGNEMENT);
	$DUREE=calcul_hours($result);

	$CODE=md5(date("H:m:sd/m/Y").rand(1000,9999));


	if (trim($DATE) != "") {
		$sql="INSERT INTO ${prefixe}edt_seances (code,enseignement,date,heure,duree,bgcolor) VALUES ('$CODE','$ENSEIGNEMENT','$DATE','$HEURE','$DUREE','$bgcolor')";
		$rc=execSql($sql);
		if ($rc) {
			$sql="SELECT id FROM ${prefixe}edt_seances WHERE  date='$DATE' AND heure='$HEURE' AND code='$CODE' AND duree='$DUREE' AND  enseignement='$ENSEIGNEMENT' ";
			$res=execSql($sql);
			$data=chargeMat($res);
		
			return $data[0][0];
		}
	}
	
}

function recupIdEdt($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,code FROM ${prefixe}edt_seances WHERE  id='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) { 
		return $data[0][1];
	}else{
		return "";
	}	
}

function miseEdt($code,$description,$dateDebut,$dateFin,$bgcolor) {
	global $cnx;
	global $prefixe;
	list($DATE,$HEURE)=preg_split('/ /',$dateDebut);
	list($null,$heure2)=preg_split('/ /',$dateFin);

	$elements=preg_split('/-/',$DATE);
        $annee=$elements[0];
        $mois=$elements[1];
	$jour=$elements[2];

	$elements=preg_split('/:/',$HEURE);
	$heure=$elements[0];
	$minute=$elements[1];
	if ($minute == 29) { $minute=30; }
	$seconde=$elements[2];
	$HEURE="$heure:$minute:$seconde";
	$resultat1=mktime($heure,$minute,$seconde,$mois,$jour,$annee);

	$elements=preg_split('/:/',$heure2);
	$heure=$elements[0];
	$minute=$elements[1];
	if ($minute == 29) { $minute=30; }
	$seconde=$elements[2];
	$resultat2=mktime($heure,$minute,$seconde,$mois,$jour,$annee);

	$result=$resultat2-$resultat1;
	
	$DUREE=calcul_hours($result);
	$sql="UPDATE ${prefixe}edt_seances SET enseignement='$description',date='$DATE',heure='$HEURE',duree='$DUREE',bgcolor='$bgcolor' WHERE id='$code'";
	execSql($sql);

}

function changementIdProfEdt($dateDebut,$dateFin,$idpersold,$idpersnew,$idclasse) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="UPDATE ${prefixe}edt_seances SET idprof='$idpersnew'  WHERE idprof='$idpersold'  AND date >= '$dateDebut' AND date <= '$dateFin' AND idclasse='$idclasse' ";
	return(execSql($sql));
}

function edtUpdateHoraire($id,$heure,$duree) {
	global $cnx;
	global $prefixe;
	if (trim($heure) != "") { $setHeure="heure='$heure'"; }
	if (trim($duree) != "") { $setDuree="duree='$duree'"; }
	$sql="UPDATE ${prefixe}edt_seances SET $setHeure  $setDuree WHERE id='$id'";
	execSql($sql);
}

function edtSuppHoraire($id) {
	global $cnx;
	global $prefixe;
	if ($id > 0) {
		$sql="DELETE FROM ${prefixe}edt_seances WHERE id='$id'";
		return(execSql($sql));
	}else{
		return(false);
	}
}


function dureeEntreDeuxHeure($dateDebut,$dateFin) {

	$elements=preg_split('/:/',$dateDebut);
	$heure=$elements[0];
	$minute=$elements[1];
	if ($minute == 29) { $minute=30; }
	$seconde=$elements[2];
	$resultat1=mktime($heure,$minute,$seconde,$mois,$jour,$annee);

	$elements=preg_split('/:/',$dateFin);
	$heure=$elements[0];
	$minute=$elements[1];
	if ($minute == 29) { $minute=30; }
	$seconde=$elements[2];
	$resultat2=mktime($heure,$minute,$seconde,$mois,$jour,$annee);

	$result=$resultat2-$resultat1;
	
	$DUREE=calcul_hours($result);
	return $DUREE;
}


function miseEdt2($id,$dateDebut,$dateFin,$bgcolor) {
	global $cnx;
	global $prefixe;
	list($DATE,$HEURE)=preg_split('/ /',$dateDebut);
	list($null,$heure2)=preg_split('/ /',$dateFin);

	$elements=preg_split('/-/',$DATE);
        $annee=$elements[0];
        $mois=$elements[1];
	$jour=$elements[2];

	$elements=preg_split('/:/',$HEURE);
	$heure=$elements[0];
	$minute=$elements[1];
	if ($minute == 29) { $minute=30; }
	$seconde=$elements[2];
	$resultat1=mktime($heure,$minute,$seconde,$mois,$jour,$annee);

	$elements=preg_split('/:/',$heure2);
	$heure=$elements[0];
	$minute=$elements[1];
	if ($minute == 29) { $minute=30; }
	$seconde=$elements[2];
	$resultat2=mktime($heure,$minute,$seconde,$mois,$jour,$annee);

	$result=$resultat2-$resultat1;
	
	$DUREE=calcul_hours($result);
	$description=htmlspecialchars($description);


	$sql="UPDATE ${prefixe}edt_seances SET date='$DATE',heure='$HEURE',duree='$DUREE' WHERE id='$id' ";
	execSql($sql);	
}



function delete_edt($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}edt_seances WHERE id='$id'";
	execSql($sql);
}

function deleteEdtId($id,$recursive) {
	global $cnx;
	global $prefixe;
	if ($recursive == "oui") {
		$info=rechercheDescriptionEdt($id);
		$CODE=$info[0][0];
		$sql="SELECT id_resa_liste FROM  ${prefixe}edt_seances WHERE code='$CODE'";
		$res=execSql($sql);
		$data=chargeMat($res);
		for($i=0;$i<count($data);$i++) {
			$iddata=$data[$i][0];
			$sql="DELETE FROM ${prefixe}resa_liste WHERE id='$iddata'";
			execSql($sql);
		}
		$sql="DELETE FROM ${prefixe}edt_seances WHERE code='$CODE'";
		execSql($sql);
	}
	$sql="SELECT id_resa_liste FROM  ${prefixe}edt_seances WHERE id='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$iddata=$data[0][0];
	$sql="DELETE FROM ${prefixe}resa_liste WHERE id='$iddata'";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}edt_seances WHERE id='$id'";
	execSql($sql);
}


function nettoyageEdt() {
	global $cnx;
	global $prefixe;
	$date=date("Y-m-d");
	$sql="DELETE FROM ${prefixe}edt_seances WHERE idclasse IS NULL AND idprof IS NULL ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}edt_seances  WHERE heure='00:00:00'";
	execSql($sql);
}


function rechercheDescriptionEdt($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,emargement,idressource,emargementeval,emargementpedago,idgroupe FROM ${prefixe}edt_seances WHERE id='$id' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}

function rechercheFinRecurrence($code) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code,enseignement,date,heure,duree,bgcolor,idclasse,idprof FROM ${prefixe}edt_seances WHERE  code='$code' ORDER BY id DESC";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data > 0)) {
		return dateForm($data[0][2]);
	}else{
		return "";
	}
}

function miseAJourEdt($id,$eventDescription,$color,$idclasse,$idprof,$jusquau,$prestation,$tabJours,$recursive,$idmatiere,$coursannule,$docdst,$reportle,$reporta,$validecreation,$emargement,$idressource,$dureehoraire,$debuthoraire,$emargementeval,$emargementpedago,$idgroupe,$semainesurdeux) {
	global $cnx;
	global $prefixe;
	//$eventDescription=htmlspecialchars($eventDescription);
	$eventDescription=preg_replace("/\n/"," ",$eventDescription);
	$eventDescription=preg_replace("/\r/"," ",$eventDescription);


	$sec=conv_en_seconde($debuthoraire)-(TIMEZONE*3600);
	$debuthoraire=calcul_hours($sec);
	
	if ($reportle == "jj/mm/aaaa") { $reportle=""; }
	if ($coursannule != 1) { $coursannule=0; }
//	if ($idressource > 0) { $emargement=0; }
	if (trim($color) == "") { $color="#FFFFFF"; } 

	$reportle=dateFormBase($reportle);

	// verif si enseignant deja affecté pour la meme heure meme jour 
	$info=rechercheDescriptionEdt($id);
	$DateDepart=conv_datetimestamp(dateForm($info[0][2]));
	$DATE=strftime("%Y-%m-%d",$DateDepart);	
	$sql="SELECT * FROM ${prefixe}edt_seances WHERE idprof='$idprof' AND heure='$debuthoraire' AND date='$DATE' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 1) {
		alertJs("Cet enseignant est déjà affecté pour cette plage horaire.");
		return ; 
	}


	if (($idclasse == "rien") && ($idressource == "rien"))  { 
		$sql="DELETE FROM ${prefixe}edt_seances  WHERE id='$id'";
	}else{
		if ($coursannule != 1) { $reportle="0000-00-00";$reporta="hh:mm"; }
		$sql="UPDATE ${prefixe}edt_seances SET enseignement='$eventDescription',bgcolor='$color',idclasse='$idclasse',idprof='$idprof',prestation='$prestation',idmatiere='$idmatiere', coursannule='$coursannule' , docdst='$docdst' , reportle='$reportle', reporta='$reporta', emargement='$emargement', idressource='$idressource', heure='$debuthoraire' , duree='$dureehoraire', emargementeval='$emargementeval', emargementpedago='$emargementpedago', idgroupe='$idgroupe' WHERE id='$id'";
		
		execSql($sql);
		if (($coursannule == 1) && ($reportle != "0000-00-00") && ($reporta != "hh:mm") && ($validecreation == 1)) {
			$info3=rechercheDescriptionEdt($id);
			$CODE=$info3[0][0];
			$DUREE=$info3[0][4];

			$sql="INSERT INTO ${prefixe}edt_seances (code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,emargement,idressource,emargementeval,emargementpedago,idgroupe) VALUES ('$CODE','$eventDescription','$reportle','$reporta','$DUREE','$color','$idclasse','$idprof','$prestation','$idmatiere','0','$docdst','0000-00-00','hh:mm','$emargement','$idressource','$emargementeval', '$emargementpedago','$idgroupe')";
			execSql($sql);
		}
		if (($recursive == "oui") && (!empty($recursive))) {
			$info=rechercheDescriptionEdt($id);
			$CODE=$info[0][0];
			$sql="UPDATE ${prefixe}edt_seances SET enseignement='$eventDescription',bgcolor='$color',idclasse='$idclasse',idprof='$idprof',prestation='$prestation',idmatiere='$idmatiere', coursannule='$coursannule' , docdst='$docdst', emargement='$emargement', idressource='$idressource', heure='$debuthoraire' , duree='$dureehoraire', emargementeval='$emargementeval', emargementpedago='$emargementpedago', idgroupe='$idgroupe' WHERE code='$CODE'";
			execSql($sql);
		}else{
			if (trim($jusquau) != "") {
				$info=rechercheDescriptionEdt($id);
				$CODE=$info[0][0];
				$ENSEIGNEMENT=$info[0][1];
				$HEURE=$info[0][3];
				$DUREE=$info[0][4];
				$DateDepart=conv_datetimestamp(dateForm($info[0][2])); //yyyy-mm-jj exclus
				$DateFin=conv_datetimestamp($jusquau);	
	//			$DateDepart+=60 * 60 * 24 * 1 ;  // +1 jours
				$cpj=1;
				if ($semainesurdeux == "oui") $DateFin+=60 * 60 * 24 * 7; 

				$DATEORIGIN=strftime("%Y-%m-%d",$DateDepart);

				while($DateDepart <= $DateFin) {
					
					$DATE=strftime("%Y-%m-%d",$DateDepart);
					if (($semainesurdeux == "oui") && ($cpj == 7)){
						$DateDepart+=60 * 60 * 24 * 7 ; $cpj=0;
						$DATE2=strftime("%Y-%m-%d",$DateDepart);
						continue;
					}
					foreach($tabJours as $key=>$value) {
						$jour=date_jour2(strftime("%d/%m/%Y",$DateDepart));
						switch($jour) {
							case "di" :  $jour="0" ; break;
							case "lu" :  $jour="1" ; break;
							case "ma" :  $jour="2" ; break;
							case "me" :  $jour="3" ; break;
							case "je" :  $jour="4" ; break;
							case "ve" :  $jour="5" ; break;
							case "sa" :  $jour="6" ; break;
						}

						if ($jour == $value) {
							if ($DATE != $DATEORIGIN) { 
								$sql="INSERT INTO ${prefixe}edt_seances (code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,emargement,idressource,emargementeval,emargementpedago,idgroupe) VALUES ('$CODE','$ENSEIGNEMENT','$DATE','$HEURE','$DUREE','$color','$idclasse','$idprof','$prestation','$idmatiere','$coursannule','$docdst','$reportle','$reporta','$emargement','$idressource','$emargementeval', '$emargementpedago','$idgroupe')";
								execSql($sql);	
								$texte.="$sql\n";
							}
						}
					}

					$DateDepart+=60 * 60 * 24 * 1 ;
					$cpj++;
				}
			}
		}
	}
	
}





function CreateEdt($eventDescription,$color,$idclasse,$idprof,$jusquau,$prestation,$tabJours,$recursive,$idmatiere,$coursannule,$docdst,$emargement,$idressource,$dureehoraire,$reporta,$reportle,$affichehoraire) {
	global $cnx;
	global $prefixe;
	//$eventDescription=htmlspecialchars($eventDescription);
	$eventDescription=preg_replace("/\n/"," ",$eventDescription);
	$eventDescription=preg_replace("/\r/"," ",$eventDescription);
	$idressource=0;
	if ($reportle == "jj/mm/aaaa") { $reportle=""; }
	if ($coursannule != 1) { $coursannule=0; }
	if (($idclasse == "rien") && ($idressource == "rien"))  { 
		// rien ;
	}else{
		$tabIdClasse=array();
		if ($idclasse == "tous") {
			$tabclasse=affClasse(); //code_class,libelle,desclong
			for($i=0;$i<count($tabclasse);$i++) {
				$tabIdClasse[]=$tabclasse[$i][0];
			}
		}else{
			$tabIdClasse[]=$idclasse;
		}
		
		$reportle=dateFormBase($reportle);

		for($ii=0;$ii<count($tabIdClasse);$ii++) {
			$idclasse=trim($tabIdClasse[$ii]);

			$CODE=md5(date("H:m:sd/m/Y").rand(1000,9999));
			$DUREE=$dureehoraire;
			$sql="INSERT INTO ${prefixe}edt_seances (code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,emargement,idressource,affichehoraire) VALUES ('$CODE','$eventDescription','$reportle','$reporta','$DUREE','$color','$idclasse','$idprof','$prestation','$idmatiere','0','$docdst','0000-00-00','hh:mm','$emargement','$idressource','$affichehoraire')";
			execSql($sql);

			if (trim($jusquau) != "") {
				$ENSEIGNEMENT=$eventDescription;
				$HEURE=$reporta;
	
				$DateDepart=conv_datetimestamp(dateForm($reportle)); //yyyy-mm-jj exclus
				$DateFin=conv_datetimestamp($jusquau);
			
				$DateDepart+=60 * 60 * 24 * 1 ;  // +1 jours
				while($DateDepart <= $DateFin) {
					$DATE=strftime("%Y-%m-%d",$DateDepart);
					foreach($tabJours as $key=>$value) {
						$jour=date_jour2(strftime("%d/%m/%Y",$DateDepart));
						switch($jour) {
							case "di" :  $jour="7" ; break;
							case "lu" :  $jour="1" ; break;
							case "ma" :  $jour="2" ; break;
							case "me" :  $jour="3" ; break;
							case "je" :  $jour="4" ; break;
							case "ve" :  $jour="5" ; break;
							case "sa" :  $jour="6" ; break;
						}
						if ($jour == $value) {
							$sql="INSERT INTO ${prefixe}edt_seances (code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,emargement,idressource,affichehoraire) VALUES ('$CODE','$ENSEIGNEMENT','$DATE','$HEURE','$DUREE','$color','$idclasse','$idprof','$prestation','$idmatiere','$coursannule','$docdst','$reportle','$reporta','$emargement','$idressource','$affichehoraire')";
							execSql($sql);	
						}
					}
					$DateDepart+=60 * 60 * 24 * 1 ;
				}
			}
		}
	}
}

function import_edt_enseignement($CODE,$NOM,$CODE_MATIERE,$DUREE_TOTALE,$DUREE_CHAQUE_SEANCE) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}edt_enseignement  WHERE code='$CODE' ";
	execSql($sql);
//	$DUREE_TOTALE=$DUREE_TOTALE."00";
	$DUREE=$DUREE."00";
	$sql="INSERT INTO ${prefixe}edt_enseignement (code,nom,code_matiere,duree_total,duree_chaque_seance) VALUES ('$CODE','$NOM','$CODE_MATIERE','$DUREE_TOTALE','$DUREE_CHAQUE_SEANCE')";
	execSql($sql);

}

function rechercheCodeEnseignement($ENSEIGNEMENT) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code,nom,code_matiere,duree_total,duree_chaque_seance FROM ${prefixe}edt_enseignement WHERE  code='$ENSEIGNEMENT' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;
}


function purgeEdtEnseignement() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}edt_enseignement";
	execSql($sql);
}

function purgeEdtSeance() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}edt_seances";
	execSql($sql);
}

function create_carnet($nom_carnet,$code_lettre,$code_chiffre,$code_couleur,$code_note,$section,$nbPeriode,$code_julesverne,$code_commentaire) {
	global $cnx;
	global $prefixe;
	$nom_carnet=trunchaine($nom_carnet,40);
	$liste_section="{";
	for($i=0;$i<4;$i++) {
		if (trim($section[$i]) != "") {
			$liste_section.=$section[$i].",";
		}
	}
	$liste_section.="}";
	$liste_section=preg_replace("/,\}/","}",$liste_section);
	if ($liste_section == "{}") { $liste_section=""; }
	$sql="SELECT id FROM ${prefixe}carnet_suivi WHERE nom_carnet='$nom_carnet' ";
	$data=ChargeMat(execSql($sql));
	if ($data > 0) { return -1 ; }

	if ($nom_carnet != "") {
		$sql="INSERT INTO ${prefixe}carnet_suivi (nom_carnet,code_lettre,code_chiffre,code_couleur,code_note,section,nb_periode,code_julesverne,code_commentaire) VALUES ('$nom_carnet','$code_lettre','$code_chiffre','$code_couleur','$code_note','$liste_section','$nbPeriode','$code_julesverne','$code_commentaire')";
		execSql($sql);
	}

}


function modif_carnet($idcarnet,$nom_carnet,$code_lettre,$code_chiffre,$code_couleur,$code_note,$section,$nb_periode,$code_julesverne,$code_commentaire) {
	global $cnx;
	global $prefixe;
	$nom_carnet=trunchaine($nom_carnet,40);
	$liste_section="{";
	for($i=0;$i<4;$i++) {
		if (trim($section[$i]) != "") {
			$liste_section.=$section[$i].",";
		}
	}
	$liste_section.="}";
	$liste_section=preg_replace("/,\}/","}",$liste_section);
	if ($nom_carnet != "") {
		$sql="UPDATE ${prefixe}carnet_suivi SET nom_carnet='$nom_carnet',code_lettre='$code_lettre',code_chiffre='$code_chiffre',code_couleur='$code_couleur',code_note='$code_note',section='$liste_section',nb_periode='$nb_periode' , code_julesverne = '$code_julesverne' , code_commentaire = '$code_commentaire' WHERE id='$idcarnet'";
		execSql($sql);
	}
}

function select_carnet() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,nom_carnet FROM ${prefixe}carnet_suivi ORDER BY nom_carnet";
	$data=ChargeMat(execSql($sql));
    	for($i=0;$i<count($data);$i++) {
	        print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][1]."</option>\n";
    	}
}

function select_carnet_idclasse($idclasse,$idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,nom_carnet,section FROM ${prefixe}carnet_suivi ORDER BY nom_carnet ";
	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
		$section=$data[$i][2];
		$section=preg_replace("/\{/",'',$section);
		$section=preg_replace("/\}/",'',$section);
		$tab=preg_split('/,/',$section);
		foreach($tab as $key=>$value) {
			$value=addslashes($value);
			$sql="SELECT id,listeidclasse FROM ${prefixe}carnet_section WHERE id='$value' ";
			$data2=ChargeMat(execSql($sql));
			for($ii=0;$ii<count($data2);$ii++) {
				$section=$data2[$ii][1];
				$section=preg_replace("/\{/",'',$section);
				$section=preg_replace("/\}/",'',$section);
				$tab2=preg_split('/,/',$section);
				foreach($tab2 as $key2=>$value2) {
					if ($value2 == $idclasse) {
						print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][1]."</option>\n";
						break;
					}
				}
			}
		}
	}
}


function affCarnet($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom_carnet,code_lettre,code_chiffre,code_couleur,code_note,section,nb_periode FROM ${prefixe}carnet_suivi WHERE id='$idcarnet' ";
	$data=ChargeMat(execSql($sql));
    	return $data;
}

function chercheIdCarnet($nom_carnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id FROM ${prefixe}carnet_suivi  WHERE nom_carnet='$nom_carnet'";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];
}

function chercheNomCarnet($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,nom_carnet FROM ${prefixe}carnet_suivi where id='$idcarnet' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][1];
}

function enr_competence($idcarnet,$competence) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id FROM ${prefixe}carnet_competence WHERE idcarnet='$idcarnet' AND libelle='$competence' ";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0 ) {
		return $data[0][0];
	}else{
		$sql="SELECT ordre FROM ${prefixe}carnet_competence WHERE idcarnet='$idcarnet' ORDER BY ordre DESC";
		$data=ChargeMat(execSql($sql));
		$ordre=$data[0][0] + 1;
		$sql="INSERT INTO ${prefixe}carnet_competence (idcarnet,libelle,ordre) VALUES ('$idcarnet','$competence','$ordre')";
		execSql($sql);
		$sql="SELECT id FROM ${prefixe}carnet_competence WHERE idcarnet='$idcarnet' AND libelle='$competence' AND ordre='$ordre' ";
		$data=ChargeMat(execSql($sql));
		return $data[0][0];
	}
}


function enr_competence_import($idcarnet,$competence,$ordre) {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}carnet_competence (idcarnet,libelle,ordre) VALUES ('$idcarnet','$competence','$ordre')";
	execSql($sql);
	$sql="SELECT id FROM ${prefixe}carnet_competence WHERE idcarnet='$idcarnet' AND libelle='$competence' AND ordre='$ordre' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];

}

function affCompetence($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,ordre FROM ${prefixe}carnet_competence WHERE idcarnet='$idcarnet'  ";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function enr_descriptif($idcarnet,$idcompetence,$bold,$descriptif) {
	global $cnx;
	global $prefixe;
	$descriptif=addslashes($descriptif);
	$sql="SELECT id FROM ${prefixe}carnet_descriptif WHERE idcarnet='$idcarnet' AND idcompetence='$idcompetence' AND  libelle='$descriptif'";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0 ) {
		return -1;
	}else{
		$sql="SELECT ordre FROM ${prefixe}carnet_descriptif WHERE idcarnet='$idcarnet' AND idcompetence='$idcompetence' ORDER BY ordre DESC";
		$data=ChargeMat(execSql($sql));
		$ordre=$data[0][0] + 1;
		$sql="INSERT INTO ${prefixe}carnet_descriptif (idcarnet,idcompetence,bold,libelle,ordre) VALUES ('$idcarnet','$idcompetence','$bold','$descriptif','$ordre')";
		execSql($sql);
	}
}

function enr_descriptif_import($idcarnet,$idcompetence,$bold,$descriptif,$ordre) {
	global $cnx;
	global $prefixe;
	$descriptif=addslashes($descriptif);
	$sql="SELECT id FROM ${prefixe}carnet_descriptif WHERE idcarnet='$idcarnet' AND idcompetence='$idcompetence' AND  libelle='$descriptif'";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0 ) {
		return -1;
	}else{
		$sql="INSERT INTO ${prefixe}carnet_descriptif (idcarnet,idcompetence,bold,libelle,ordre) VALUES ('$idcarnet','$idcompetence','$bold','$descriptif','$ordre')";
		execSql($sql);
	}
}

function affDescriptif($idcompetence) {
	global $cnx;
	global $prefixe;
	$sql="SELECT bold,libelle,ordre FROM ${prefixe}carnet_descriptif WHERE  idcompetence='$idcompetence' ORDER BY ordre DESC ";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function modif_descriptif($iddescriptif,$bold,$descriptif) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}carnet_descriptif SET libelle='$descriptif',bold='$bold' WHERE id='$iddescriptif'";
	execSql($sql);
}

function chercheCompetence($idcompetence) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle FROM ${prefixe}carnet_competence WHERE id='$idcompetence' ";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0 ) {
		return $data[0][0];
	}
}

function rechercheDescriptif($idcompetence,$idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,bold,ordre FROM ${prefixe}carnet_descriptif WHERE idcarnet='$idcarnet' AND idcompetence='$idcompetence' ORDER BY ordre";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function chercheInfoDescriptif($iddescriptif) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idcarnet,idcompetence,libelle,bold,ordre FROM ${prefixe}carnet_descriptif WHERE id='$iddescriptif'";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function supp_descriptif($iddescsupp) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}carnet_descriptif  WHERE id='$iddescsupp' ";
	execSql($sql);
}

function listeCompetence($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idcarnet,libelle,ordre FROM ${prefixe}carnet_competence WHERE idcarnet='$idcarnet' ORDER BY ordre";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function modifOrdreCompetence($idcarnet,$liste) {
	global $cnx;
	global $prefixe;
	$tabList=preg_split("/,/",$liste);
	$nbList=count($tabList);
	for($i=0;$i<$nbList;$i++) {
		$value=$tabList[$i];
		$sql="UPDATE ${prefixe}carnet_competence SET ordre='$i' WHERE id='$value' ";
		execSql($sql);
	}

}

function chercheTypeNotation($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_lettre,code_chiffre,code_couleur,code_note,code_julesverne,code_commentaire FROM ${prefixe}carnet_suivi  WHERE id='$idcarnet' ";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function selectTypeNotation($idcarnet){
	global $cnx;
	global $prefixe;
	$sql="SELECT code_lettre,code_chiffre,code_couleur,code_note,code_julesverne,code_commentaire FROM ${prefixe}carnet_suivi  WHERE id='$idcarnet' ";
	$data=ChargeMat(execSql($sql));
	if ($data[0][0] == 1) { print "<option id='select1' value='lettre'>en Lettre (A à D)</option>\n"; }
	if ($data[0][1] == 1) { print "<option id='select1' value='chiffre'>en Chiffre (1 à 4)</option>\n"; }
	if ($data[0][2] == 1) { print "<option id='select1' value='couleur'>en Couleur (Vert, Bleu ,Orange, Rouge)</option>\n"; }
	if ($data[0][3] == 1) { print "<option id='select1' value='note'>en Note (0 à 10 ou 0 à 20)</option>\n"; }
	if ($data[0][5] == 1) { print "<option id='select1' value='commentaire'>en Commentaire</option>\n"; }
	if ($data[0][4] == 1) { print "<option id='select1' value='julesverne'>en Notation Jules Verne</option>\n"; }
}

function nb_periode($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nb_periode FROM ${prefixe}carnet_suivi  WHERE id='$idcarnet' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];
}

function selectPeriodeCarnet($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nb_periode FROM ${prefixe}carnet_suivi  WHERE id='$idcarnet' ";
	$data=ChargeMat(execSql($sql));
	$nb=$data[0][0];
	for($i=1;$i<=$nb;$i++) {
		print "<option id='select1' value='$i'>Période $i</option>\n";
	}

}

function enr_section($listeIdClasse,$section) {
	global $cnx;
	global $prefixe;
	if (trim($section) == "") {
		return 0;
	}
	$sql="SELECT id FROM ${prefixe}carnet_section WHERE libelle='$section' ";
	$data=ChargeMat(execSql($sql));
	if ((count($data) <= 0))  {
		$liste="{";
		$liste.="$listeIdClasse";
		$liste.="}";
		$liste=preg_replace("/,\}/","}",$liste);
		$sql="INSERT INTO ${prefixe}carnet_section (libelle,listeidclasse) VALUES ('$section','$liste')";
		$cr=execSql($sql);
	}else{
		$cr=0;
	}
	return $cr;
}


function modif_section($listeIdClasse,$idsection) {
	global $cnx;
	global $prefixe;
	if (trim($idsection) == "") {
		return 0;
	}
	$liste="{";
	$liste.="$listeIdClasse";
	$liste.="}";
	$liste=preg_replace("/,\}/","}",$liste);
	$sql="UPDATE  ${prefixe}carnet_section  SET listeidclasse='$liste'  WHERE id='$idsection' ";
	return(execSql($sql));
}

function listeSection() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,listeidclasse FROM ${prefixe}carnet_section ORDER BY libelle ";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function select_section($liste) {
	global $cnx;
	global $prefixe;
	if ($liste == "tous") {
		$data=listeSection(); //id,libelle,listeidclasse
	}elseif($liste > 0) { 
		print "<option id='select1' value='".$liste."'>".chercheNomSection($liste)."</option>\n";
	}else{
		return;
	}
	for($i=0;$i<count($data);$i++) {
		$libelle=$data[$i][1];
		$id=$data[$i][0];
        	print "<option id='select1' value='".$id."'>".$libelle."</option>\n";
		
	}
}

function select_classeSection($idsection) {
	global $cnx;
	global $prefixe;
	if ($idsection > 0) {
		$tab=chercheSectionClasse($idsection);
		foreach($tab as $key=>$value) {
			print "<option id='select0' value='$value' >".chercheClasse_nom($value)."</option>\n";
		}

	}else{
		return ;
	}

}

function chercheSectionClasse($idsection) {
	global $cnx;
	global $prefixe;
	$sql="SELECT listeidclasse FROM ${prefixe}carnet_section WHERE id='$idsection' ";
	$data=ChargeMat(execSql($sql));
	$data=preg_replace("/\{/","",$data[0][0]);
	$data=preg_replace("/\}/","",$data);
	$tab_section=preg_split("/,/",$data);
	return $tab_section;
}

function listeCarnet() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id FROM ${prefixe}carnet_suivi  ";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function chercheNomSection($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle FROM ${prefixe}carnet_section WHERE id='$id' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];
}



function supp_section($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}carnet_section WHERE id='$id' ";
	execSql($sql);
}





function verifSectionCarnet($idcarnet,$idsection) {
	global $cnx;
	global $prefixe;
	$sql="SELECT section FROM ${prefixe}carnet_suivi WHERE id='$idcarnet' ";
	$data=ChargeMat(execSql($sql));
	$data=preg_replace('/\{/',"",$data[0][0]);
	$data=preg_replace('/\}/',"",$data);
	$tab_section=preg_split('/,/',$data);
	foreach($tab_section as $key=>$value) {
		if ($value == $idsection) {
			return true;
		}
	}
	return false;
}

function chercheSectionCarnet($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT section FROM ${prefixe}carnet_suivi WHERE id='$idcarnet' ";
	$data=ChargeMat(execSql($sql));
	$data=preg_replace('/\{/',"",$data[0][0]);
	$data=preg_replace('/\}/',"",$data);
	$tab_section=preg_split('/,/',$data);
	return $tab_section;
}

function image_bulletin($idEleve) {
	global $cnx;
	global $prefixe;
	$photoLocal=recherche_photo_eleve($idEleve);
	$fic="./data/image_eleve/$photoLocal";
	if ((file_exists($fic)) && (trim($photoLocal) !=  "")) {
		return $fic;
	}elseif(file_exists("./common/config-pdf.php")) {
		include_once("./common/config-pdf.php");
		$idetablissement=IDETABLISSEMENT;
		if (file_exists("./data/${idetablissement}/image/E_${idEleve}.jpg")) {
			return "./data/${idetablissement}/image/E_${idEleve}.jpg";
		}
	}
	return ;
}

function enr_evaluation_carnet($idEleve,$idcarnet,$idcompetence,$notation,$periode,$idclasse,$note,$idDescriptif) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id FROM ${prefixe}carnet_evaluation  WHERE idcarnet='$idcarnet' AND 
		idcompetence='$idcompetence' AND
		iddescriptif='$idDescriptif' AND
		periode='$periode' AND
		ideleve='$idEleve' AND
		idclasse='$idclasse' ";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}carnet_evaluation SET note='$note', type_notation='$notation' WHERE idcarnet='$idcarnet' AND 
		idcompetence='$idcompetence' AND
		iddescriptif='$idDescriptif' AND
		periode='$periode' AND
		ideleve='$idEleve' AND
		idclasse='$idclasse' ";
		execSql($sql);
	}else{
		$sql="INSERT INTO ${prefixe}carnet_evaluation (`idcarnet`,`idcompetence`,`iddescriptif`,`note`,`periode`,`ideleve`,`idclasse`,`type_notation`) VALUES ('$idcarnet','$idcompetence','$idDescriptif','$note','$periode','$idEleve','$idclasse','$notation')";
		execSql($sql);
	}
}

function rechercheEvalutionEleve($idEleve,$idcarnet,$iddescriptif,$idcompetence,$notation,$periode,$idsection) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,note FROM ${prefixe}carnet_evaluation  WHERE idcarnet='$idcarnet' AND 
		idcompetence='$idcompetence' AND
		iddescriptif='$iddescriptif' AND
		periode='$periode' AND
		ideleve='$idEleve' AND
		idclasse='$idsection' AND
		type_notation='$notation' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][1];

}

function chercheListeIdClasseSection($nomsection) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  listeidclasse FROM ${prefixe}carnet_section  WHERE libelle='$nomsection'";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];

}

function supprimer_carnet($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}carnet_competence  WHERE  idcarnet='$idcarnet' ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}carnet_descriptif  WHERE  idcarnet='$idcarnet' ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}carnet_descriptif  WHERE  idcarnet='$idcarnet' ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}carnet_evaluation  WHERE  idcarnet='$idcarnet' ";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}carnet_suivi  WHERE  id='$idcarnet' ";
	execSql($sql);
}

function rechercheEvalutionEleveBultin($idEleve,$idcarnet,$iddescriptif,$idcompetence,$periode,$listeidclasse) {
	global $cnx;
	global $prefixe;
	$listeidclasse=preg_replace("/\{/","",$listeidclasse);
	$listeidclasse=preg_replace("/\}/","",$listeidclasse);

	if ($listeidclasse != "") { $sqlsuite=" AND idclasse IN ($listeidclasse) "; }

	$sql="SELECT id,note,type_notation FROM ${prefixe}carnet_evaluation  WHERE 
		idcarnet='$idcarnet' AND 
		idcompetence='$idcompetence' AND
		iddescriptif='$iddescriptif' AND
		periode='$periode' AND
		ideleve='$idEleve' 
		$sqlsuite ";

	$data=ChargeMat(execSql($sql));
	return $data;

}


function Lire_La_Ligne_n($fichier, $ligne) {
    if (file_exists("$fichier")) {
        if($id = fopen("$fichier", "r+")){
               while(!feof($id)){
                        $result[]= fgets($id,1000000);
               }
               fclose($id);
               $tab=$result;
               $result=$tab[$ligne-1];
               return $result;
          }else{
                return pb_ouv;
          }
    }else{
       return no_file;
   }
}

function initialisePasswordEleveParent($eleve,$parent,$envoiMail=0,$idclasse='tous',$anneeScolaire) {
	global $cnx;
	global $prefixe;
	if (($idclasse != "tous") && ($idclasse > 0)) {	
		$sqlSuite=" WHERE classe='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	}else{
		$sqlSuite=" WHERE annee_scolaire='$anneeScolaire' ";
	}
	$sql="SELECT  elev_id,nom,prenom,classe,email,email_eleve FROM ${prefixe}eleves $sqlSuite ORDER BY nom";
	$data=ChargeMat(execSql($sql));
	@unlink("../data/fic_pass.txt");
	for($i=0;$i<count($data);$i++) {
		$ideleve=$data[$i][0];
		$nomEleve=strtolower(trim($data[$i][1]));
		$prenomEleve=strtolower(trim($data[$i][2]));
		$emaileleve=$data[$i][5];
		$emailparent=$data[$i][4];
		$passwd=passwd_random2(); // creation du mot de passe
		$passwd_enr=$passwd;
		$passwd_eleve=passwd_random2();
		$passwd_eleve_enr=$passwd_eleve;
		$nomclasse=chercheClasse_nom($data[$i][3]);
		if ($eleve != 1) {$passwd_eleve_enr="pas de changement";}
		if ($parent != 1) {$passwd_enr="pas de changement";}
				
		$ligne="$nomEleve;$prenomEleve;$passwd_enr;$passwd_eleve_enr;$nomclasse<br />";
		$passwd=cryptage($passwd_enr);
		$passwd_eleve=cryptage($passwd_eleve_enr);
		if ($eleve == 1) {
			$sqlsuite=" passwd_eleve='$passwd_eleve' ";
		}
		if ($parent == 1) {
			if ($sqlsuite != "") $sqlsuite.=" , ";
			$sqlsuite.=" passwd='$passwd' ";
		}
		if ($sqlsuite != "") { 
			$sql="UPDATE ${prefixe}eleves SET $sqlsuite  WHERE elev_id='$ideleve' ";
			$ins=execSql($sql);
		}
		$f_pass=fopen("../data/fic_pass.txt","a+");
		fwrite($f_pass,$ligne);
		fclose($f_pass);
	
		if ($envoiMail == 1) {
			if (($eleve == 1) && (ValideMail($emaileleve)) && (trim($passwd_eleve_enr) != "")) { envoiMotDePasse($emaileleve,$nomEleve,$prenomEleve,$passwd_eleve_enr,"menueleve"); }
			if (($parent == 1) && (ValideMail($emailparent)) && (trim($passwd_enr) != "")) { envoiMotDePasse($emailparent,$nomEleve,$prenomEleve,$passwd_enr,"menuparent"); }
		}	

	}
	$sql="DELETE FROM  ${prefixe}statUtilisateur WHERE  type_membre='menueleve' OR type_membre='menuparent' ";
	execSql($sql);
	return(count($data)); 
}


function envoiMotDePasse($email,$nom,$prenom,$motdepasse,$membre) {
	global $cnx;
	global $prefixe;

	if (file_exists("./langue-text-fr.php")) include_once("./langue-text-fr.php");
	if (file_exists("./lbbrairie_php/langue-text-fr.php")) include_once("./librairie_php/langue-text-fr.php");
	if (file_exists("../librairie_php/langue-text-fr.php")) include_once("../librairie_php/langue-text-fr.php");

	if ($membre == "menuparent") 	$membre=" ".TITREACC1." ";
	if ($membre == "menueleve")  	$membre=" ".INTITULEELEVES." ";
	if ($membre == "menuprof")   	$membre=" ".TITREACC2." ";
	if ($membre == "menuadmin")  	$membre=" ".INTITULEDIRECTION." ";
	if ($membre == "menuscolaire")  $membre=" ".TITREACC3." ";
	if ($membre == "menututeur")  	$membre=" ".TITREACC4." ";
	if ($membre == "menupersonnel") $membre=" ".TITREACC5." ";


	$url=preg_replace('/http:\/\//','',URLSITE);

	$message="
Bonjour,<br>
<br>
Votre compte TRIADE est actif sur http://$url<br>
<br>
Pour vous connecter, utilisez les identifiants ci-dessous :<br>
<br>
         Nom  : $nom<br>
Pr&eacute;nom : $prenom<br>
 Mot de passe : $motdepasse<br>
<br>
Vous devez utiliser l'acc&egrave;s :  $membre (voir menu gauche)<br>
<br>
<br>
--<br>
Triadement Votre,<br>
<br>
L'Equipe TRIADE.<br>
<br>
";

	$to = trim($email);
	$sujet="TRIADE : Activation de votre compte";
	$nom_expediteur=expediteur_triade();
	$email_expediteur=MAILREPLY;
//	$message=TextNoAccent($message);		
	$ret="\n";
	if (PHP_OS == "WINNT") {  $ret="\r\n"; }	
	$from = 'From: "'.$nom_expediteur.'" <'.$email_expediteur.'>'."$ret";
	$headers=$from;
	$email_expediteur=trim($email_expediteur);

//	print "$sujet,$message,$message,"$to",$email_expediteur,$email_expediteur,$nom_expediteur<hr>"; exit;

	mailTriade($sujet,$message,$message,"$to",$email_expediteur,$email_expediteur,$nom_expediteur,"");

}


function envoiMailPassElPa($nomP,$prenomP,$dateNaissanceP,$passwdParent,$passwdEleve) {
	global $cnx;
	global $prefixe;
	if (preg_match('/\//',$dateNaissanceP)) {
		$dateNaissanceP=dateFormBase($dateNaissanceP);
	}
	$nomP=strtolower(trim($nomP));
	$prenomP=strtolower(trim($prenomP));
	$sql="SELECT email,email_eleve FROM ${prefixe}eleves WHERE lower(trim(nom))='$nomP' AND lower(trim(prenom))='$prenomP' AND date_naissance='$dateNaissanceP'";
	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
		$emaileleve=$data[$i][1];
		$emailtuteur1=$data[$i][0];
		if ((ValideMail($emaileleve)) && (trim($passwdEleve) != "")) { envoiMotDePasse($emaileleve,$nomP,$prenomP,$passwdEleve,"menueleve"); }
		if ((ValideMail($emailtuteur1)) && (trim($passwdParent) != "")) { envoiMotDePasse($emailtuteur1,$nomP,$prenomP,$passwdParent,"menuparent"); }
	}
}

function modifPassword($nomP,$prenomP,$dateNaissanceP,$passwdParent,$passwdEleve) {
        global $cnx;
        global $prefixe;
        if ($dateNaissanceP != "X") {
                if (preg_match('/\//',$dateNaissanceP)) {
			$dateNaissanceP=dateFormBase($dateNaissanceP);
                }
        }
        $nomP=strtolower(trim($nomP));
        $prenomP=strtolower(trim($prenomP));
        if ($dateNaissanceP != "X") {
                $sql="SELECT * FROM ${prefixe}eleves WHERE lower(trim(nom))='$nomP' AND lower(trim(prenom))='$prenomP' AND date_naissance='$dateNaissanceP'";
        }else{
                $sql="SELECT * FROM ${prefixe}eleves WHERE lower(trim(nom))='$nomP' AND lower(trim(prenom))='$prenomP'";
        }
        $data=ChargeMat(execSql($sql));
        if (count($data) > 0) {
                if (trim($passwdParent) != "") {
                        $passwdParent=cryptage($passwdParent);
                        $sql="UPDATE ${prefixe}eleves SET passwd='$passwdParent' WHERE lower(trim(nom))='$nomP' AND lower(trim(prenom))='$prenomP' AND date_naissance='$dateNaissanceP'";
                        execSql($sql);
                }
                if (trim($passwdEleve) != "")  {
                        $passwdEleve=cryptage($passwdEleve);
                        $sql="UPDATE ${prefixe}eleves SET passwd_eleve='$passwdEleve' WHERE lower(trim(nom))='$nomP' AND lower(trim(prenom))='$prenomP' AND date_naissance='$dateNaissanceP'";
                        execSql($sql);
                }
        }else{
                return -1;
        }
} 


function initialisePasswordDefinieEnseignant($password,$emailenvoie) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  pers_id,nom,prenom,email FROM ${prefixe}personnel WHERE type_pers='ENS' ORDER BY nom";
	$data=ChargeMat(execSql($sql));
	@unlink("../data/fic_pass.txt");
	for($i=0;$i<count($data);$i++) {
		$idpers=$data[$i][0];
		$nomEns=strtolower(trim($data[$i][1]));
		$prenomEns=strtolower(trim($data[$i][2]));
		$emailEns=$data[$i][3];
		$passwd=$password; 
		$ligne="$nomEns;$prenomEns;$password<br />";
		$passwd=cryptage($passwd);
		$sql="UPDATE ${prefixe}personnel SET mdp='$passwd'  WHERE pers_id='$idpers' ";
		$ins=execSql($sql);
		$f_pass=fopen("../data/fic_pass.txt","a+");
		fwrite($f_pass,$ligne);
		fclose($f_pass);
		if ($emailenvoie == 1) {
			if ((ValideMail($emailEns)) && (trim($password) != "")) { envoiMotDePasse($emailEns,$nomEns,$prenomEns,$password,"menuprof"); }
		}	
	}
	$sql="DELETE FROM  ${prefixe}statUtilisateur WHERE  type_membre='menuprof' ";
	execSql($sql);	
}

function initialisePasswordEnseignant($emailenvoie) {
	global $cnx;
	global $prefixe;	
	$sql="SELECT  pers_id,nom,prenom,email FROM ${prefixe}personnel WHERE type_pers='ENS' ORDER BY nom";
	$data=ChargeMat(execSql($sql));
	@unlink("../data/fic_pass.txt");
	for($i=0;$i<count($data);$i++) {
		$idpers=$data[$i][0];
		$nomEns=strtolower(trim($data[$i][1]));
		$prenomEns=strtolower(trim($data[$i][2]));
		$emailEns=$data[$i][3];
		$passwd=passwd_random2(); // creation du mot de passe
		$passwd_enr=$passwd;
		$nomclasse=chercheClasse_nom($data[$i][3]);
		$ligne="$nomEns;$prenomEns;$passwd_enr<br />";
		$passwd=cryptage($passwd_enr);
		$sql="UPDATE ${prefixe}personnel SET mdp='$passwd'  WHERE pers_id='$idpers' ";
		$ins=execSql($sql);
		$f_pass=fopen("../data/fic_pass.txt","a+");
		fwrite($f_pass,$ligne);
		fclose($f_pass);
		if ($emailenvoie == 1) {
			if ((ValideMail($emailEns)) && (trim($passwd_enr) != "")) { envoiMotDePasse($emailEns,$nomEns,$prenomEns,$passwd_enr,"menuprof"); }
		}	

	}
	$sql="DELETE FROM  ${prefixe}statUtilisateur WHERE  type_membre='menuprof' ";
	execSql($sql);
}

function initialisePasswordDefinieEleveParent($passel,$passpar,$envoiMail=0,$idclasse='tous',$anneeScolaire) {
	global $cnx;
	global $prefixe;
	if (($idclasse != "tous") && ($idclasse > 0)) {
		$sqlSuite=" WHERE classe='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	}else{
		$sqlSuite=" WHERE annee_scolaire='$anneeScolaire' ";
	}
	$sql="SELECT  elev_id,nom,prenom,classe,email,email_eleve FROM ${prefixe}eleves $sqlSuite ORDER BY nom";
	$data=ChargeMat(execSql($sql));
	@unlink("../data/fic_pass.txt");
	for($i=0;$i<count($data);$i++) {
		$ideleve=$data[$i][0];
		$nomEleve=strtolower(trim($data[$i][1]));
		$prenomEleve=strtolower(trim($data[$i][2]));
		$emaileleve=$data[$i][5];
		$emailparent=$data[$i][4];
		$passwd=$passpar; // creation du mot de passe
		$passwd_eleve=$passel;
		$nomclasse=chercheClasse_nom($data[$i][3]);
		if ($passwd_eleve == "") {$passel="pas de changement";}
		if ($passwd == "") {$passpar="pas de changement";}

		$ligne="$nomEleve;$prenomEleve;$passpar;$passel;$nomclasse<br />";
		$passwd=cryptage($passwd);
		$passwd_eleve=cryptage($passwd_eleve);
		if (trim($passwd_eleve) != "") {
			$sqlsuite=" passwd_eleve='$passwd_eleve' ";
		}
		if (trim($passwd) != "") {
			if ($sqlsuite != "") $sqlsuite.=" , ";
			$sqlsuite.=" passwd='$passwd' ";
		}
		if ($sqlsuite != "") { 
			$sql="UPDATE ${prefixe}eleves SET $sqlsuite WHERE elev_id='$ideleve' ";
			$ins=execSql($sql);
		}
		$f_pass=fopen("../data/fic_pass.txt","a+");
		fwrite($f_pass,$ligne);
		fclose($f_pass);
		
		if ($envoiMail == 1) {
			if ((ValideMail($emaileleve)) && (trim($passwd_eleve) != "")) {  envoiMotDePasse($emaileleve,$nomEleve,$prenomEleve,$passel,"menueleve"); }
			if ((ValideMail($emailparent)) && (trim($passwd) != "")) { envoiMotDePasse($emailparent,$nomEleve,$prenomEleve,$passpar,"menuparent"); }
		}
	}
	$sql="DELETE FROM  ${prefixe}statUtilisateur WHERE  type_membre='menueleve' OR type_membre='menuparent' ";
	execSql($sql);
	return(count($data));
}

function ajoutModifCaracVieScolaire($bullpers,$coef_bulletin,$coefProf,$coefScolaire,$idclasse,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse FROM ${prefixe}notes_scolaire_param WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire'";
	$data=ChargeMat(execSql($sql));
	$coef_bulletin=preg_replace('/,/','.',$coef_bulletin);
	$coefProf=preg_replace('/,/','.',$coefProf);
	$coefScolaire=preg_replace('/,/','.',$coefScolaire);
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}notes_scolaire_param SET coefbull='$coef_bulletin', coefprof='$coefProf', coefviescolaire='$coefScolaire', personnebulletin='$bullpers'  WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	}else{
		$sql="INSERT INTO ${prefixe}notes_scolaire_param (idclasse,coefbull,coefprof,coefviescolaire,personnebulletin,annee_scolaire) VALUES ('$idclasse','$coef_bulletin','$coefProf','$coefScolaire','$bullpers','$anneeScolaire')";
	}
	execSql($sql);
}


function recupCaractVieScolaire($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT idclasse,coefbull,coefprof,coefviescolaire,personnebulletin FROM ${prefixe}notes_scolaire_param WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$data=ChargeMat(execSql($sql));
	return $data;

}

function supprime_note_groupe($params,$idgrp) {
	global $cnx;
	global $prefixe;
	if ($params["liste_eleve_supp"] != "") {
		$liste_eleves="(".$params["liste_eleve_supp"];
		$liste_eleves=$liste_eleves.")";
		$sql="DELETE FROM ${prefixe}notes WHERE elev_id IN $liste_eleves  AND id_groupe='$idgrp' ";
		execSql($sql);
	}
}


function verifEleveDansGroupe($ideleve,$idgrp) {
	global $cnx;
	global $prefixe;
	$sql="SELECT liste_elev FROM ${prefixe}groupes WHERE group_id='$idgrp' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$liste_eleves=$data[0][0];
	$liste_eleves=preg_replace("/\{/","",$liste_eleves);
	$liste_eleves=preg_replace("/\}/","",$liste_eleves);
	$tab=explode(",",$liste_eleves);
	foreach($tab as $key => $value) {
		if ($value == $ideleve) {
			return false;
		}
	}
	return true;
}

function rechercheAdresseEleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT adr1 FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][0] != "") { 
		$valeur=trim($data[0][0]); 
	}else{
		$valeur="";
	}
   	return $valeur;
}

function rechercheCodePostalEleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_post_adr1 FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][0] != "") { 
		$valeur=trim($data[0][0]); 
	}else{
		$valeur="";
	}
   	return $valeur;
}

function rechercheVilleEleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT commune_adr1 FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][0] != "") { 
		$valeur=trim($data[0][0]); 
	}else{
		$valeur="";
	}
   	return $valeur;
}

function rechercheSexeEleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT sexe FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][0] != "") { 
		$valeur=trim($data[0][0]); 
	}else{
		$valeur="";
	}
   	return $valeur;
}

function rechercheVilleReponsable1($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_post_adr1,commune_adr1 FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) { 
		$valeur=trim($data[0][0]." ".$data[0][1]);
	}else{
		$valeur=" ";
	}

   	return $valeur;
}

function rechercheAdresseReponsable1($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT adr1 FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][0] != "") { 
		$valeur=trim($data[0][0]); 
	}else{
		$valeur="";
	}
   	return $valeur;
}


function rechercheNomReponsable1($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nomtuteur FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][0] != "") { 
		$valeur=trim($data[0][0]); 
	}else{
		$valeur="";
	}
   	return $valeur;
}



function rechercheLieuNaissanceEleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT lieu_naissance FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][0] != "") { 
		$valeur=trim($data[0][0]); 
	}else{
		$valeur="";
	}
   	return $valeur;
}


function rechercheNationaliteEleve($id_eleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nationalite FROM ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][0] != "") { 
		$valeur=trim(ucfirst($data[0][0])); 
	}else{
		$valeur="";
	}
   	return $valeur;
}



function couleurFont($graphe) {
	if ($graphe == 1) { return "#CCCCCC"; }
}



function verifdelegue($idpers,$membre,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT  idclasse,nomparent1,nomparent2,eleve1,eleve2 FROM ${prefixe}delegue WHERE idclasse='$idclasse' AND ";
	if ($membre == "menuparent") {
		$sql.="( nomparent1 = '$idpers' OR nomparent2 = '$idpers' ) "; 
	}elseif($membre == "menueleve") {
		$sql.="( eleve1 = '$idpers' OR eleve2 = '$idpers' )";	
	}else {
		return 1;	
	}
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) { 
		return 1; 
	}else{
		return 0;
	}
}

function rechercheEleveNomPrenom($idEleve) {
	if (($idEleve == 0) || (trim($idEleve) == "") || ($idEleve == NULL)) {
		return "???";
	}else{
		global $cnx;
		global $prefixe;
		$sql="SELECT nom,prenom FROM ${prefixe}eleves WHERE elev_id='$idEleve'";
		$res=execSql($sql);
		$data=chargeMat($res);
		if ($data[0][0] != "") { 
			$valeur=trim(strtoupper($data[0][0]))." ".trim(ucwords($data[0][1])); 
		}else{
			$valeur="";
		}
   		return $valeur;
	}

}


function eregist_planclasse($idclasse,$newcoord) {
	global $cnx;
	global $prefixe;
	//print $newcoord; //E1632(516;249),E1633(346;123), 
	$newcoord=preg_replace('/,$/',"","$newcoord");
	$tab=explode(",",$newcoord);
	$sql="DELETE FROM ${prefixe}planclasse  WHERE  idclasse='$idclasse' ";
	execSql($sql);
	foreach($tab as $key=>$value) {
		if (preg_match('/E/',$value)) {
			list($idEleve,$coor)=preg_split('/\(/',$value,2);
			$idEleve=preg_replace('/E/',"",$idEleve);
			$coor=preg_replace('/\)/',"",$coor);
			list($x,$y)=preg_split('/\;/',$coor,2);
		}
		if (preg_match('/B/',$value)) {
			list($idEleve,$coor)=preg_split('/\(/',$value,2);
			$idEleve=preg_replace('/B/',"",$idEleve);
			$idEleve="-".$idEleve;
			$coor=preg_replace('/\)/',"",$coor);
			list($x,$y)=preg_split('/\;/',$coor,2);
		}
		$sql="INSERT INTO ${prefixe}planclasse (ideleve,idclasse,posx,posy) VALUES ('$idEleve','$idclasse','$x','$y')";
		execSql($sql);
	}
}

function cherchePlanX($idEleve,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT posx FROM ${prefixe}planclasse  WHERE  ideleve='$idEleve' AND  idclasse='$idclasse' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return $data[0][0];
	}else{
		return -1;
	}
}

function cherchePlanY($idEleve,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT posy FROM ${prefixe}planclasse  WHERE  ideleve='$idEleve' AND  idclasse='$idclasse' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return $data[0][0];
	}else{
		return -1;
	}
}

function ListeCompletDiscipline($idcarnet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,bold,ordre,idcompetence FROM ${prefixe}carnet_descriptif WHERE idcarnet='$idcarnet'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return $data;

}

function insertFicheLiaison($idclasse,$trimes,$evalprogress,$evaldiff,$ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve FROM ${prefixe}ficheliaison WHERE ideleve='$ideleve' AND trimestre='$trimes' AND idclasse='$idclasse'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}ficheliaison SET dom_progress='$evalprogress', dom_difficulte='$evaldiff' WHERE ideleve='$ideleve' AND trimestre='$trimes' AND idclasse='$idclasse' ";
	}else{
	$sql="INSERT INTO ${prefixe}ficheliaison (ideleve,trimestre,dom_progress,dom_difficulte,com_suj_aide,eleve_viescolaire,eleve_travscolaire,conclusion_prof,conclusion_dir,idclasse) VALUES ('$ideleve', '$trimes', '$evalprogress' , '$evaldiff' , '' , '' , '' , '' , '','$idclasse');";
	}
	execSql($sql);
}

function consultFicheLiaisonDomain($idEleve,$idclasse,$tri) {
	global $cnx;
	global $prefixe;
	$sql="SELECT dom_progress,dom_difficulte,com_suj_aide,eleve_viescolaire,eleve_travscolaire,conclusion_prof,conclusion_dir FROM ${prefixe}ficheliaison WHERE ideleve='$idEleve' AND trimestre='$tri' AND idclasse='$idclasse'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}

function suppSousMatiere($id) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}matieres SET sous_matiere='0' WHERE code_mat='$id'";
	execSql($sql);
}

function suppFicheLiaison($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}ficheliaison  WHERE  ideleve='$idEleve' ";
	execSql($sql);
}

function protohttps() {
	if (HTTPS == "oui") {
		return "https://";
	}else{
		return "http://";
	}
}

function create_piecejointe($fichier,$fichierMD5,$idpiecejointe,$etat) {
	global $cnx;
	global $prefixe;
	// Etat=1 c'est ok , sinon à détruire
//	$sql="SELECT md5,nom,etat FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe' ";
//	$res=execSql($sql);
//	$data=chargeMat($res);
//	if (count($data) > 0) {
//		$sql="UPDATE ${prefixe}piecejointe SET nom='$fichier', md5='$fichierMD5', etat='$etat' WHERE idpiecejointe='$idpiecejointe'  ";
//		execSql($sql);
//	}else{
		$sql="INSERT INTO ${prefixe}piecejointe (nom,md5,idpiecejointe,etat) VALUES ('$fichier','$fichierMD5','$idpiecejointe','$etat')";
		execSql($sql);
//	}
}	

function verifpiecejointe($idpiecejointe) {
	global $cnx;
	global $prefixe;
	$sql="SELECT md5,nom,etat FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		if (trim($data[0][2]) == 1) {
			if (file_exists("./data/fichiersj/".trim($data[0][0]))) {
				return $data[0][1]."/".$idpiecejointe;
			}else{
				return "-1/".$idpiecejointe;
			}
		}elseif($data[0][2] == 2) {
			return "-1/".$idpiecejointe;
		}else{
			return "-1/".$idpiecejointe;
		}
	}else{
		return "-2/".$idpiecejointe;
	}
}


function infoPieceJointe($idpiecejointe) {
        global $cnx;
        global $prefixe;
        $sql="SELECT md5,nom FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe' ";
        $res=execSql($sql);
        $data=chargeMat($res);
	return($data);
}

function recupPieceJointe($idpiecejointe) {
	global $cnx;
	global $prefixe;
	/*
	$sql="SELECT md5,nom,etat,idpiecejointe FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$md5=$data[$i][0];
		if ( (!file_exists("./data/DevoirScolaire/$md5")) && (!is_dir("./data/DevoirScolaire/$md5")))  {
			$sql="DELETE FROM ${prefixe}piecejointe WHERE md5='$md5'";
			execSql($sql);
		}	
	}*/
	$sql="SELECT md5,nom,etat,idpiecejointe FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}



function deleteRefPieceJointe($idpiecejointe) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}piecejointe  WHERE  idpiecejointe='$idpiecejointe' ";
	execSql($sql);
}

function nomFichierJoint($md5) {
	global $cnx;
	global $prefixe;
	$sql="SELECT md5,nom FROM ${prefixe}piecejointe WHERE md5='$md5' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return $data[0][1];
	}else{
		return "";
	}
}


function fichierJointExiste($idpiecejointe) {
        global $cnx;
        global $prefixe;
        if ($idpiecejointe != "") {
                $sql="SELECT md5,nom FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe'";
                $res=execSql($sql);
                $data=chargeMat($res);
                if (count($data) > 0) {
                        return $data;
                }
        }
}

function suppPieceJointe($ficmd5) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}piecejointe WHERE md5='$ficmd5' ";
	execSql($sql);
}


function selectStage($eid) {
	global $cnx;
        global $prefixe;
        $sql="SELECT id_eleve,
                id_entreprise,
                lieu_stage,
                ville_stage,
                id_prof_visite,
                date_visite_prof,
               	loger,
                nourri,
             	passage_x_service,
              	raison,
              	info_plus,
             	num_stage,
             	code_p,
             	id,
	    	tuteur_stage FROM ${prefixe}stage_eleve WHERE id_eleve='$eid' ORDER BY num_stage";
	$res=@execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$identr=$data[$i][1];
	        $num=$data[$i][11];
	        $identnum="$identr#$num";
	        $tabEntr[$identnum]=recherche_entr_nom_via_id($identr);
	}
	foreach($tabEntr as $key=>$value) {
	        list($id,$idnum)=preg_split('/#/',$key);
	        $num=rechercheNumStage($idnum);
	       	if (trim($num) != "") $num="$num)";
	        print "<option id='select1' value='$id#$idnum' title=\"$value\" > $num ".stripslashes(trunchaine($value,30))."</option>";
	}
}		
 




function selectStage2($eid) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_eleve,
		id_entreprise,
		lieu_stage,
		ville_stage,
		id_prof_visite,
		date_visite_prof,
		loger,
		nourri,
		passage_x_service,
		raison,
		info_plus,
		num_stage,
		code_p,
		id,
		tuteur_stage FROM ${prefixe}stage_eleve WHERE id_eleve='$eid' ORDER BY num_stage";
	$res=@execSql($sql);
      	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$identr=$data[$i][1];
		$num=$data[$i][11];
		$identnum="$identr";
		$tabEntr[$identnum]=recherche_entr_nom_via_id($identr);
	}
	foreach($tabEntr as $key=>$value) {
		$num=rechercheNumStage($key);
		print "<option id='select1' value='$id' title=\"$value\" >".trunchaine($value,30)."</option>";
	}
}


function recupcomptegoogleanalytic() {
	global $cnx;
        global $prefixe;
	$sql="SELECT text FROM ${prefixe}parametrage WHERE libelle='googleanalytic' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		return $data[0][0];
	}else{
		return "";
	}
}

function arrondiAuDemi2($nombre) {
	/* 
	- 1.21 x 2 = 2.42 -> round(2.42) = 2 -> 2 / 2 = 1 
	- 1.28 x 2 = 2.56 -> round(2.56) = 3 -> 3 / 2 = 1.5 
	- 1.69 x 2 = 3.38 -> round(3.38) = 3 -> 3 / 2 = 1.5 
	- 1.78 x 2 = 3.56 -> round(3.56) = 4 -> 4 / 2 = 2 
	*/
	$arrondi = round($nombre * 2) / 2;
	return $arrondi;
}


function arrondiAuDemi($valeur) {  // notation sur 20
	if (preg_match('/,/',$valeur)) { $valeur=preg_replace('/,/','.',$valeur); }
	$valeur=round($valeur,2);
	$valeur=sprintf("%01.2f",$valeur);
	list($unite,$dixaine)=preg_split('/\./',$valeur);
	if ($dixaine == "00") {
		$dixaine=0;
	}elseif ($unite == 20) {
		$dixaine=0;
		$unite=20;
	}elseif ($dixaine > 50) { 
		$dixaine=0; 
		$unite++; 
	}else { 
		$dixaine=5; 
	}
	if ($unite < 10) { $unite="0".$unite; } 
	$valeur=$unite.".".$dixaine;
	return $valeur;
}

function verifChamps($table,$champs,$valeur) {
	global $cnx;
        global $prefixe;
	$sql="SELECT * FROM ${prefixe}${table} WHERE $champs='$valeur' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		return true;
	}else{
		return false;
	}
}


function listingGroupeProf($idprof) {
	global $cnx;
        global $prefixe;
	$sql="SELECT  libelle  FROM ${prefixe}affectations e, ${prefixe}groupes  f WHERE  e.code_prof='$idprof' AND   f.group_id=e.code_groupe ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	$liste="";
	if (count($data) > 0) {
		for($i=0;$i<count($data);$i++) {
			if (trim($data[$i][0]) != "") { $liste.=$data[$i][0].", "; }
		}
		if (trim($liste) != "") {
			return $liste;
		}else{
			return LANGbasededon33;
		}
	}else{
		return LANGbasededon33;
	}
}


function verifResaEnCours($idpers) {
	global $cnx;
        global $prefixe;
	$sql="SELECT * FROM ${prefixe}resa_liste WHERE  idqui='$idpers' AND valider = '0' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		return true;
	}else{
		return false;
	}
}


function verifResaValider($id) {
	global $cnx;
        global $prefixe;
	$sql="SELECT valider FROM ${prefixe}resa_liste WHERE  id='$id'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		return $data[0][0];
	}else{
		return 0;
	}
}

function chercheNomMatos($idmatos) {
	global $cnx;
        global $prefixe;
	$sql="SELECT libelle FROM ${prefixe}resa_matos WHERE id='$idmatos' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data[0][0];
}

function suppResa($idpers,$id) {
	global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}resa_liste  WHERE  id='$id' AND idqui='$idpers' ";
	return(execSql($sql));
}

function ListeResa($idpers) {
	global $cnx;
        global $prefixe;
	$sql="SELECT  id,idmatos,idqui,quand,heure_depart,heure_fin,info,valider,refcommun FROM ${prefixe}resa_liste WHERE  idqui='$idpers' ORDER BY quand DESC,heure_depart DESC";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}


function verifResaList() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}resa_liste  WHERE  idmatos='0' ";
	return(execSql($sql));

}


function enrConfigBrevet($tab,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}brevetconfig WHERE idclasse='$idclasse' ";
	execSql($sql);
	foreach($tab as $key => $value) {
		list($libelle,$idmatiere)=preg_split('/\|/',$value);
		if ($libelle != "") {
			$sql="INSERT INTO ${prefixe}brevetconfig (libelle,idmatiere,idclasse) VALUES ('$libelle','$idmatiere','$idclasse');";
			execSql($sql);
		}
	}
}

function listMatiereBrevetLI($libelle,$idclasse) {
        global $cnx;
        global $prefixe;
        $sql="SELECT libelle,idmatiere,coefbrevet FROM ${prefixe}brevetconfig WHERE libelle='$libelle'  AND idclasse='$idclasse' ";
        $res=execSql($sql);
        $data=ChargeMat($res);
        for($i=0;$i<count($data);$i++) {
                $coef=$data[$i][2];
                if ($coef == "") $coef="1";
                print "<li id='".$data[$i][1]."' >".chercheMatiereNom($data[$i][1]);
                print "&nbsp;&nbsp;<font size=1>($coef)</font> </li>\n";
        }
}


function rechercheMatiereBrevet($matiere,$idclasse) {
	global $cnx;
	global $prefixe;
	$matiere=strtolower($matiere);
	$sql="SELECT idmatiere,libelle FROM ${prefixe}brevetconfig WHERE lower(libelle)='$matiere' AND idclasse='$idclasse'  ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function verifMatiereBrevetLI($idmatiere,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT libelle,idmatiere FROM ${prefixe}brevetconfig WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) { 
		return true;
	}else{
		return false;
	}
}



function create_noteB2IA2($eleveid,$idclasse,$type_notation,$note) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT note FROM ${prefixe}b2iA2notation WHERE ideleve='$eleveid' AND idclasse='$idclasse' AND type_notation='$type_notation' AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) { 
		$sql="UPDATE ${prefixe}b2iA2notation SET note='$note' WHERE ideleve='$eleveid' AND idclasse='$idclasse' AND type_notation='$type_notation' AND annee_scolaire='$anneeScolaire' ";
		return(execSql($sql));		
	}else{
		$sql="INSERT INTO ${prefixe}b2iA2notation (ideleve,idclasse,type_notation,note,annee_scolaire) VALUES ('$eleveid','$idclasse','$type_notation','$note','$anneeScolaire');";
		return(execSql($sql));
	}
}

function rechercheB2IEleve($ideleve,$idclasse,$type_notation) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT note FROM ${prefixe}b2iA2notation WHERE ideleve='$ideleve' AND idclasse='$idclasse' AND type_notation='$type_notation' AND annee_scolaire='$anneeScolaire'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) { 
		return $data[0][0];
	}else{
		return "";
	}
}


function enregCoefBrevet($params,$typeBrevet) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}brevetcoef  WHERE  type_brevet='$typeBrevet'";
	execSql($sql);
	foreach($params as $key=>$value) {
		$sql="INSERT INTO ${prefixe}brevetcoef (matiere,coef,type_brevet) VALUES ('$key','$value','$typeBrevet')";
		execSql($sql);
	}
	return true;
}

function rechercheCoefBrevet($matiere,$typeBrevet) {
	global $cnx;
	global $prefixe;
	$sql="SELECT coef FROM ${prefixe}brevetcoef WHERE type_brevet='$typeBrevet' AND matiere='$matiere'  ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) { 
		return $data[0][0];
	}else{
		return "";
	}
}

function enrgBrevet($INE,$code,$note,$typeBrevet,$idEleve) {
	global $cnx;
	global $prefixe;
	if (trim($code) != "") {
		$sql="INSERT INTO ${prefixe}brevetnote (ine,matiere,note,type_brevet,ideleve) VALUES ('$INE','$code','$note','$typeBrevet','$idEleve')";
		execSql($sql);
	}
}

function DeleteBrevetviaIdEleve($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}brevetnote  WHERE ideleve='$idEleve'";
	execSql($sql);
}

function DeleteBrevet($INE,$typeBrevet) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}brevetnote  WHERE  ine='$INE' AND  type_brevet='$typeBrevet' ";
	execSql($sql);
}


function RecupNoteBrevet($INE,$matiere,$typeBrevet,$idEleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT note FROM ${prefixe}brevetnote WHERE type_brevet='$typeBrevet' AND matiere='$matiere' AND ideleve='$idEleve' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) { 
		return $data[0][0];
	}else{
		return "";
	}

}

function modifOrdreaffectation($idclasse,$tablist,$tri,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}affectations WHERE code_classe='$idclasse' AND trim='$tri' AND annee_scolaire='$anneeScolaire' ";
	execSql($sql);
	foreach($tablist as $ordreNew => $value) {
		list($ordre,$idMatiere,$idProf,$coef,$idGrp,$idLang,$avecSousMatiere,$visubull,$ects,$ue,$specif_etat,$visubullbtsblanc,$info_semestre,$nbheure,$id_ue_detail)=preg_split('/:/',$value);
		$sql="INSERT INTO ${prefixe}affectations (ordre_affichage,code_matiere,code_prof,code_classe,coef,code_groupe,langue,avec_sous_matiere,visubull,trim,ects,id_ue_detail,annee_scolaire,specif_etat,visubullbtsblanc,num_semestre_info,nb_heure) VALUES ('$ordreNew','$idMatiere','$idProf','$idclasse','$coef','$idGrp','$idLang','$avecSousMatiere','$visubull','$tri','$ects','$id_ue_detail','$anneeScolaire','$specif_etat','$visubullbtsblanc','$info_semestre','$nbheure')";
		$cr=execSql($sql);
	}
	return 1;
	
	
}

function enrg_entretien($ideleve,$saisiedate,$heuredebut,$heurefin,$objet,$nomclasse,$nom,$prenom,$preparation,$id_entretienpedagogue) {
	global $cnx;
	global $prefixe;
	$saisiedate=dateFormBase($saisiedate);
	if ($preparation != 1) { $preparation=0; }
	$sql="INSERT INTO ${prefixe}entretieneleve (ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,preparation) VALUES ('$ideleve','$saisiedate','$heuredebut','$heurefin','$nomclasse','$objet','$nom $prenom','$preparation' )";
	execSql($sql);
	$sql="SELECT id FROM ${prefixe}entretieneleve WHERE ideleve='$ideleve' AND date='$saisiedate' AND  heuredebut='$heuredebut' AND  heurefin='$heurefin' AND  nomclasse='$nomclasse' AND  objet='$objet' AND  recupar='$nom $prenom' AND preparation='$preparation'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	$id_entretieneleve=$data[0][0];
	$tab=explode(";",$id_entretienpedagogue);
	foreach($tab as $key=>$value) {
		if ($value > 0) {
			$sql="SELECT * FROM ${prefixe}entretienpedagogue WHERE id_entretieneleve='$id_entretieneleve'  AND  id_entretienpedagogue='$value'";
			$res=execSql($sql);
			$data=ChargeMat($res);
			if (count($data) == 0) {
				$sql="INSERT INTO ${prefixe}entretienpedagogue (id_entretieneleve,id_entretienpedagogue) VALUES ('$id_entretieneleve','$value')";
				execSql($sql);
			}
		}
	}
}


function recupListNomPrenomPedago($identretien) {
	global $cnx;
	global $prefixe; 
	$sql="SELECT p.nom,p.prenom,p.civ FROM ${prefixe}personnel p, ${prefixe}entretienpedagogue e, ${prefixe}entretieneleve t  WHERE e.id_entretieneleve=t.id AND e.id_entretienpedagogue=p.pers_id AND e.id_entretieneleve='$identretien' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);
}


function supp_info_entretienEleve($identretienpedago) {
	global $cnx;
	global $prefixe; 
	$sql="DELETE FROM ${prefixe}entretienpedagogue WHERE id_entretienpedagogue='$identretienpedago'";
	execSql($sql);
}

function supp_info_entretienProf($idpers) {
	global $cnx;
	global $prefixe; 
	$sql="DELETE FROM ${prefixe}entretienprof WHERE idpers='$idpers'";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}entretiendureeprof WHERE idprof='$idpers'";
	execSql($sql);
}

function recupListPedago() {
	global $cnx;
	global $prefixe; 
	$sql="SELECT p.nom,p.prenom,p.civ,t.heuredebut,t.heurefin,t.nomclasse,p.pers_id FROM ${prefixe}personnel p, ${prefixe}entretienpedagogue e, ${prefixe}entretieneleve t  WHERE e.id_entretieneleve=t.id AND e.id_entretienpedagogue=p.pers_id  group by p.pers_id";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);
}


function enrg_entretienProf($idpers,$saisiedate,$heuredebut,$heurefin,$objet,$nomclasse,$nom,$prenom,$preparation) {
	global $cnx;
	global $prefixe;
	$saisiedate=dateFormBase($saisiedate);
	if ($preparation != 1) { $preparation=0; }
	$sql="INSERT INTO ${prefixe}entretienprof (idpers,date,heuredebut,heurefin,nomclasse,objet,recupar,preparation) VALUES ('$idpers','$saisiedate','$heuredebut','$heurefin','$nomclasse','$objet','$nom $prenom','$preparation' )";
	execSql($sql);
}


function modif_entretienProf($idpers,$saisiedate,$heuredebut,$heurefin,$objet,$nomclasse,$nom,$prenom,$identretien,$preparation) {
	global $cnx;
	global $prefixe;
	$saisiedate=dateFormBase($saisiedate);
	$sql="UPDATE ${prefixe}entretienprof SET date='$saisiedate', preparation='$preparation',  heuredebut='$heuredebut', heurefin='$heurefin', nomclasse='$nomclasse', objet='$objet', recupar='$nom $prenom' WHERE id='$identretien' ";
	execSql($sql);
}


function modif_entretien($ideleve,$saisiedate,$heuredebut,$heurefin,$objet,$nomclasse,$nom,$prenom,$identretien,$preparation) {
	global $cnx;
	global $prefixe;
	$saisiedate=dateFormBase($saisiedate);
	$sql="UPDATE ${prefixe}entretieneleve SET date='$saisiedate', preparation='$preparation',  heuredebut='$heuredebut', heurefin='$heurefin', nomclasse='$nomclasse', objet='$objet', recupar='$nom $prenom' WHERE id='$identretien' ";
	execSql($sql);
}

function recupEntretiens($idEleve,$idEntretien) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation FROM ${prefixe}entretieneleve WHERE ideleve='$idEleve' AND id='$idEntretien'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function recupEntretiensProf($idPers,$idEntretien) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idpers,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation FROM ${prefixe}entretienprof WHERE idpers='$idPers' AND id='$idEntretien'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function cumulEntretien($eid,$nomclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,date,heuredebut,heurefin,nomclasse FROM ${prefixe}entretieneleve WHERE ideleve='$eid'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	for($i=0;$i<count($data);$i++) {
		list($heure,$minute,$seconde)=preg_split('/:/',$data[$i][2]);
		$secondeH1=($heure*60*60) + $seconde + ($minute*60);
		list($heure2,$minute2,$seconde2)=preg_split('/:/',$data[$i][3]);
		$secondeH2=($heure2*60*60) + $seconde2 + ($minute2*60);
		$temps=$secondeH2-$secondeH1;
		$temps1+=$temps;
	}
	return(timeForm(calcul_hours($temps1)));
}


function cumulEntretien2($eid) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,date,heuredebut,heurefin,nomclasse FROM ${prefixe}entretieneleve WHERE ideleve='$eid'  ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	for($i=0;$i<count($data);$i++) {
		list($heure,$minute,$seconde)=preg_split('/:/',$data[$i][2]);
		$secondeH1=($heure*60*60) + $seconde + ($minute*60);
		list($heure2,$minute2,$seconde2)=preg_split('/:/',$data[$i][3]);
		$secondeH2=($heure2*60*60) + $seconde2 + ($minute2*60);
		$temps=$secondeH2-$secondeH1;
		$classe=$data[$i][4];
		$tab[$classe]+=$temps;
	}
	foreach($tab as $classe => $value) {
		$duree=timeForm(calcul_hours($value));
		$retour.="$classe&nbsp;($duree)&nbsp;-&nbsp;";
	}
	return($retour);
}

function cumulEntretienPedago($pid) {
	global $cnx;
	global $prefixe;
	$sql="SELECT e.ideleve,e.date,e.heuredebut,e.heurefin,e.nomclasse FROM ${prefixe}entretieneleve e, ${prefixe}entretienpedagogue p WHERE p.id_entretieneleve=e.id AND p.id_entretienpedagogue='$pid' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	for($i=0;$i<count($data);$i++) {
		list($heure,$minute,$seconde)=preg_split('/:/',$data[$i][2]);
		$secondeH1=($heure*60*60) + $seconde + ($minute*60);
		list($heure2,$minute2,$seconde2)=preg_split('/:/',$data[$i][3]);
		$secondeH2=($heure2*60*60) + $seconde2 + ($minute2*60);
		$temps=$secondeH2-$secondeH1;
		$classe=$data[$i][4];
		$tab[$classe]+=$temps;
		$dureetotal+=$temps;
	}

	foreach($tab as $classe => $value) {
		$duree=timeForm(calcul_hours($value));
		$retour.="&nbsp;$classe&nbsp;($duree)&nbsp;<br />";
	}
	$dureetotal=timeForm(calcul_hours($dureetotal));
	$retour.="&nbsp;<b>Total : $dureetotal</b>&nbsp;<br />";
	return($retour);
}

function cumulEntretien22($eid,$nomclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,date,heuredebut,heurefin,nomclasse FROM ${prefixe}entretieneleve WHERE ideleve='$eid' AND nomclasse='$nomclasse' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	for($i=0;$i<count($data);$i++) {
		list($heure,$minute,$seconde)=preg_split('/:/',$data[$i][2]);
		$secondeH1=($heure*60*60) + $seconde + ($minute*60);
		list($heure2,$minute2,$seconde2)=preg_split('/:/',$data[$i][3]);
		$secondeH2=($heure2*60*60) + $seconde2 + ($minute2*60);
		$temps=$secondeH2-$secondeH1;
		$temps1+=$temps;
	}
	return($temps1);
}

function cumulEntretienProf($eid,$nomclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idpers,date,heuredebut,heurefin,nomclasse FROM ${prefixe}entretienprof WHERE idpers='$eid'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	for($i=0;$i<count($data);$i++) {
		list($heure,$minute,$seconde)=preg_split('/:/',$data[$i][2]);
		$secondeH1=($heure*60*60) + $seconde + ($minute*60);
		list($heure2,$minute2,$seconde2)=preg_split('/:/',$data[$i][3]);
		$secondeH2=($heure2*60*60) + $seconde2 + ($minute2*60);
		$temps=$secondeH2-$secondeH1;
		$temps1+=$temps;
	}
	return(timeForm(calcul_hours($temps1)));
}

function listeEntretien($eid) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation FROM ${prefixe}entretieneleve WHERE ideleve='$eid' ORDER BY date DESC, heuredebut ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;	
}


function listeEntretienProf($eid) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idpers,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation FROM ${prefixe}entretienprof WHERE idpers='$eid' ORDER BY date DESC, heuredebut ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;	
}

function suppEntretien($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}entretieneleve  WHERE  id='$id' ";
	execSql($sql);
}


function suppEntretienProf($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}entretienprof  WHERE  id='$id' ";
	execSql($sql);
}

function purgeEntretienEleve($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}entretieneleve  WHERE  ideleve='$idEleve' ";
	execSql($sql);
}


function purgeEntretien() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}entretieneleve";
	execSql($sql);
}

function purgeCahierTexte() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}cahiertexte";
	execSql($sql);
	$sql="TRUNCATE TABLE ${prefixe}devoir_scolaire";
	execSql($sql);
}

function calculduree($h1,$h2) {
	list($heure,$minute,$seconde)=preg_split('/:/',$h1);
	$secondeH1=($heure*60*60) + $seconde + ($minute*60);
	list($heure2,$minute2,$seconde2)=preg_split('/:/',$h2);
	$secondeH2=($heure2*60*60) + $seconde2 + ($minute2*60);
	$temps=$secondeH2-$secondeH1;
	return calcul_hours($temps);
}

function enrEvalHoraire($evaluation,$basehoraire,$prestation) {
	global $cnx;
	global $prefixe;
	$sql="INSERT INTO ${prefixe}vacation_config (libelle,taux,type_prestation) VALUES ('$evaluation','$basehoraire','$prestation')";
	execSql($sql);
}

function affEvalHoraire() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,taux FROM ${prefixe}vacation_config ORDER BY libelle";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function getInactifEleve($eid) {
	global $cnx;
	global $prefixe;
	$sql="SELECT compte_inactif FROM ${prefixe}eleves WHERE elev_id='$eid'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data[0][0];
}

function inactifEleve($eid,$inactif) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}eleves SET compte_inactif='$inactif'  WHERE elev_id='$eid'";
	return(execSql($sql));
}

function nbHeureVacation($idpers,$idprestation,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	if ($dateDebut != "") { 
		$dateDebut=dateFormBase($dateDebut); 
		$dateFin=dateFormBase($dateFin); 
		$sqlsuite="AND s.date >= '$dateDebut' AND s.date <= '$dateFin'";
	}
	$sql="SELECT s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.type_prestation,s.idmatiere,s.coursannule FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v WHERE v.id = s.prestation AND s.idprof='$idpers' AND s.prestation='$idprestation' $sqlsuite ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function nbHeureVacationMatiereParDate($idpers,$idmatiere,$dateDebut,$dateFin,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT duree FROM ${prefixe}edt_seances WHERE idprof='$idpers' AND idmatiere='$idmatiere' AND date >= '$dateDebut' AND date <= '$dateFin' AND coursannule = '0' AND idclasse='$idclasse' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function nbHeureVacationParDate($idpers,$idprestation,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.type_prestation,s.idmatiere,s.coursannule FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v WHERE v.id = s.prestation AND s.idprof='$idpers' AND s.prestation='$idprestation' AND s.date >= '$dateDebut' AND s.date <= '$dateFin'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}


function recupNbHeureCommandeViaIdMatiereAndTypePrestation($type_prestation,$idmatiere) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nbheure FROM ${prefixe}vacation_commande WHERE idmatiere='$idmatiere' AND type_prestation='$type_prestation'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data[0][0];
}

function recupNbHeureCommandeViaIdClasseAndTypePrestation($type_prestation,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nbheure FROM ${prefixe}vacation_commande WHERE idclasse='$idclasse' AND type_prestation='$type_prestation'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data[0][0];
}

function recupNbHeureCommandeViaIdPersAndTypePrestation($type_prestation,$idPers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nbheure FROM ${prefixe}vacation_commande WHERE id_pers='$idPers' AND type_prestation='$type_prestation'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data[0][0];
}

function recupNbHeureEffectueViaIdMatiere($idmatiere,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe; 
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT duree,prestation FROM ${prefixe}edt_seances WHERE idmatiere='$idmatiere' AND date >= '$dateDebut' AND date <= '$dateFin' AND coursannule != '1'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function recupNbHeureEffectueViaIdPers($idPers,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe; 
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT duree,prestation FROM ${prefixe}edt_seances WHERE idprof='$idPers' AND date >= '$dateDebut' AND date <= '$dateFin' AND coursannule != '1'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}



function recupNbHeureEffectueViaIdClasse($idclasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe; 
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT duree,prestation FROM ${prefixe}edt_seances WHERE idclasse='$idclasse' AND date >= '$dateDebut' AND date <= '$dateFin' AND coursannule != '1'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}


function nbHeureVacationParIdMatiere($idMatiere,$dateDebut,$dateFin) {
        global $cnx;
        global $prefixe;
        $dateDebut=dateFormBase($dateDebut);
        $dateFin=dateFormBase($dateFin);
        $sql="SELECT s.duree FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v WHERE v.id = s.prestation AND s.idmatiere='$idMatiere'   AND s.date >= '$dateDebut' AND s.date <= '$dateFin' AND s.coursannule != '1' ";
        $res=execSql($sql);
        $data=ChargeMat($res);
        for($i=0;$i<count($data);$i++) {
                $sec+=conv_en_seconde($data[$i][0]);
        }
        $cr=calcul_hours($sec);
        return($cr);
}


function nbHeureVacationParIdClasse($idClasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT SEC_TO_TIME(SUM(s.duree)) FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v WHERE v.id = s.prestation AND s.idclasse='$idClasse' 	AND s.date >= '$dateDebut' AND s.date <= '$dateFin' AND s.coursannule != '1' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data[0][0]);
}

function nbHeureVacationParIdPers($idPers,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT SEC_TO_TIME(SUM(s.duree)) FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v WHERE v.id = s.prestation AND s.idprof='$idPers' 	AND s.date >= '$dateDebut' AND s.date <= '$dateFin' AND s.coursannule != '1' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data[0][0]);
}

function listePrestaHoraire() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation FROM ${prefixe}edt_seances 
		WHERE 	
		( 
		heure NOT like '%:00:%' AND
		heure NOT like '%:05:%' AND 
		heure NOT like '%:10:%' AND 
		heure NOT like '%:15:%' AND 
		heure NOT like '%:20:%' AND 
		heure NOT like '%:25:%' AND 
		heure NOT like '%:30:%' AND 
		heure NOT like '%:35:%' AND 
		heure NOT like '%:40:%' AND 
		heure NOT like '%:45:%' AND 
		heure NOT like '%:50:%' AND 
		heure NOT like '%:55:%'
		)
	       OR 	
	       ( 
		duree NOT like '%:00:%' AND
		duree NOT like '%:05:%' AND 
		duree NOT like '%:10:%' AND 
		duree NOT like '%:15:%' AND 
		duree NOT like '%:20:%' AND 
		duree NOT like '%:25:%' AND 
		duree NOT like '%:30:%' AND 
		duree NOT like '%:35:%' AND 
		duree NOT like '%:40:%' AND 
		duree NOT like '%:45:%' AND 
		duree NOT like '%:50:%' AND 
		duree NOT like '%:55:%'  
		) ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}



function verifHoraire($id,$heure,$duree) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation FROM ${prefixe}edt_seances WHERE 	";
	if (trim($heure) != "") {
		$sql.="
		heure NOT like '%:00:%' AND
		heure NOT like '%:05:%' AND 
		heure NOT like '%:10:%' AND 
		heure NOT like '%:15:%' AND 
		heure NOT like '%:20:%' AND 
		heure NOT like '%:25:%' AND 
		heure NOT like '%:30:%' AND 
		heure NOT like '%:35:%' AND 
		heure NOT like '%:40:%' AND 
		heure NOT like '%:45:%' AND 
		heure NOT like '%:50:%' AND 
		heure NOT like '%:55:%' 
		AND  id='$id' ";
	}
	if (trim($duree) != "") {
		$sql.="
		duree NOT like '%:00:%' AND
		duree NOT like '%:05:%' AND 
		duree NOT like '%:10:%' AND 
		duree NOT like '%:15:%' AND 
		duree NOT like '%:20:%' AND 
		duree NOT like '%:25:%' AND 
		duree NOT like '%:30:%' AND 
		duree NOT like '%:35:%' AND 
		duree NOT like '%:40:%' AND 
		duree NOT like '%:45:%' AND 
		duree NOT like '%:50:%' AND 
		duree NOT like '%:55:%'
		AND id='$id' ";
	}
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		return 0;
	}else{
		return 1;
	}

}


function heureEnseignant($idpers,$idclasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	if ($idpers != "") {
		if ($idclasse != "tous") {
			$suitesql="AND s.idclasse='$idclasse'";
		}
		$dateDebut=dateFormBase($dateDebut);
		$dateFin=dateFormBase($dateFin);
		$sql="SELECT s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.taux,s.coursannule FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v  WHERE v.id = s.prestation  AND s.idprof='$idpers' $suitesql AND v.type_prestation='cours'  AND s.date >= '$dateDebut' AND s.date <= '$dateFin'   ORDER BY date, heure DESC";
		$res=execSql($sql);
		$data=ChargeMat($res);
		if (count($data) > 0) {
			return $data;
		}
	}
	return $data;
}




function heureEnseignantparDate($idpers,$idclasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if ($idpers != "") {
		if ($idclasse != "tous") {
			$suitesql="AND s.idclasse='$idclasse'";
		}
		$sql="SELECT s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.taux,s.coursannule FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v  WHERE v.id = s.prestation  AND s.idprof='$idpers' $suitesql AND v.type_prestation='cours'  AND s.date >= '$dateDebut' AND s.date <= '$dateFin' ORDER BY date, heure DESC";
		$res=execSql($sql);
		$data=ChargeMat($res);
		if (count($data) > 0) {
			return $data;
		}
	}
	return $data;
}

function heureEnseignantparDate2($idpers,$idclasse,$dateDebut,$dateFin,$typeprestation,$annule,$idmatiere='',$idprestation='') {
        global $cnx;
        global $prefixe;
        $dateDebut=dateFormBase($dateDebut);
        $dateFin=dateFormBase($dateFin);
        if ($idpers != "") {
                if ($idclasse != "tous") { $suitesql="AND s.idclasse='$idclasse'"; }
                if ($annule == "1") { $annule="AND s.coursannule != 1"; }else{ $annule=''; }
                if ($idmatiere != "")  $suitesql2=" AND  s.idmatiere='$idmatiere' ";
                if ($idprestation > 0) $suitesql3=" AND s.prestation='$idprestation' ";
                $sql="SELECT s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.taux,s.coursannule,s.reportle,s.reporta FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v  WHERE v.id = s.prestation  $suitesql2 $suitesql3  AND s.idprof='$idpers' $suitesql AND v.type_prestation='$typeprestation' $annule AND s.date >= '$dateDebut' AND s.date <= '$dateFin' ORDER BY date, heure DESC";
                $res=execSql($sql);
                $data=ChargeMat($res);
                if (count($data) > 0) {
                        return $data;
                }
        }
        return $data;
}

function dateEnseignantEval($idpers,$idclasse,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if ($idpers != "") {
		if ($idclasse != "tous") {
			$suitesql="AND s.idclasse='$idclasse'";
		}
		$sql="SELECT s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.taux,s.coursannule FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v WHERE v.id = s.prestation  AND  s.idprof='$idpers' $suitesql AND v.type_prestation='eval' AND s.date >= '$dateDebut' AND s.date <= '$dateFin'  ORDER BY date , heure DESC";
		$res=execSql($sql);
		$data=ChargeMat($res);
		if (count($data) > 0) {
			return $data;
		}
	}
	return $data;

}

function dateEnseignantEvalparDate($idpers,$idclasse,$datedebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$datedebut=dateFormBase($datedebut);
	$dateFin=dateFormBase($dateFin);
	if ($idpers != "") {
		if ($idclasse != "tous") {
			$suitesql="AND s.idclasse='$idclasse'";
		}
		$sql="SELECT s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.taux,s.coursannule FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v WHERE v.id = s.prestation  AND  s.idprof='$idpers' $suitesql AND v.type_prestation='eval'  AND s.date >= '$datedebut' AND s.date <= '$dateFin'  ORDER BY date , heure ";
		$res=execSql($sql);
		$data=ChargeMat($res);
		if (count($data) > 0) {
			return $data;
		}
	}
	return $data;

}

function heureEnseignant2($idpers,$anneemois,$idclasse) {
	global $cnx;
	global $prefixe;
	$liste="";
	if ($idpers != "") {
		if ($idclasse != "tous") {
			$suitesql="AND s.idclasse='$idclasse'";
		}
		$sql="SELECT s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,s.idmatiere,s.coursannule FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v WHERE v.id = s.prestation  AND  s.idprof='$idpers' $suitesql  AND v.type_prestation='cours' AND s.date LIKE '$anneemois%' ORDER BY date , heure ";
		$res=execSql($sql);
		$data=ChargeMat($res);
		if (count($data) > 0) {
			for($i=0;$i<count($data);$i++) {
				$secondeT=conv_en_seconde($data[$i][4]);
				$secondeT+=conv_en_seconde($data[$i][5]);
				$heureFin=calcul_hours($secondeT);
				if ($data[$i][11] == 1) {
					$liste.=dateForm($data[$i][3])." (".timeForm($data[$i][4]). "-".timeForm($heureFin).") --> ANNULE \n";
				}else{
					$liste.=dateForm($data[$i][3])." (".timeForm($data[$i][4]). "-".timeForm($heureFin).")  \n";
				}
			}
		}
	}
	return $liste;
}

function nbHeureEnseignant2($idpers,$anneemois,$idclasse) {
	global $cnx;
	global $prefixe;
	$liste="";
	$nb=0;
	if ($idpers != "") {
		if ($idclasse != "tous") {
			$suitesql="AND s.idclasse='$idclasse'";
		}
		$sql="SELECT s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,s.idmatiere,s.coursannule FROM ${prefixe}edt_seances s, ${prefixe}vacation_config v WHERE v.id = s.prestation  AND  s.idprof='$idpers' $suitesql  AND v.type_prestation='cours' AND s.date LIKE '$anneemois%' ORDER BY date , heure DESC";
		$res=execSql($sql);
		$data=ChargeMat($res);
		if (count($data) > 0) {
			for($i=0;$i<count($data);$i++) {
				$nb++;		
			}
		}
	}
	return $nb;
}


function affEvalHoraireMotif($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,taux,type_prestation FROM ${prefixe}vacation_config WHERE id='$id' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}


function recupTypePrestationViaIdMatiere($idMatiere) {
	global $cnx;
	global $prefixe; 
	$sql="SELECT type_prestation FROM ${prefixe}vacation_commande WHERE idmatiere='$idMatiere' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function recupTypePrestationViaIdClasse($idClasse) {
	global $cnx;
	global $prefixe; 
	$sql="SELECT type_prestation FROM ${prefixe}vacation_commande WHERE idclasse='$idClasse' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function recupTypePrestationViaIdPers($idPers) {
	global $cnx;
	global $prefixe; 
	$sql="SELECT type_prestation FROM ${prefixe}vacation_commande WHERE id_pers='$idPers' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}


function affTauxViaId($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT taux FROM ${prefixe}vacation_config WHERE id='$id' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data[0][0];
}


function modifEvalHoraire($idEval,$saisie_evaluation,$saisie_basehoraire,$prestation) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}vacation_config SET libelle='$saisie_evaluation' , taux='$saisie_basehoraire',type_prestation='$prestation'  WHERE id='$idEval' ";
	return(execSql($sql));	
}

function select_EvalHoraire2() {
	global $cnx;
	global $prefixe;
        $data=affEvalHoraire();
        for($i=0;$i<count($data);$i++) {
		$libelle=$data[$i][1];
		$id=$data[$i][0];
        	print "<option id='select1' value='".$id."'>".$libelle."</option>\n";
        }
}


function recherchePrestatNom($idprestat) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,taux FROM ${prefixe}vacation_config WHERE id='$idprestat'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data[0][1];
}

function rechercheBasePrestat($idprestat) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,taux FROM ${prefixe}vacation_config WHERE id='$idprestat'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data[0][2];
}

function unitemonnaie() {
	if (MONNAIE == "euro") return "&euro;";
	if (MONNAIE == "dollar") return "&#36;";
	if (MONNAIE == "dinars") return "DT";
	if (MONNAIE == "livre") return "&pound;";
	if (MONNAIE == "yen") return "&yen;";
	if (MONNAIE == "dirham") return "Dh";
	if (MONNAIE == "FCFA") return "FCFA";
	if (MONNAIE == "LAK") return "LAK";
	if (MONNAIE == "CFP") return "CFP";
	if (MONNAIE == "HTG") return "HTG";
	if (MONNAIE == "CHF") return "CHF";
	if (MONNAIE == "FC") return "FC";
	if (MONNAIE == "dollarCAD") return "&#36;";
	return "&euro;";
}

function  unitemonnaiePdf() {
	if (MONNAIE == "euro") return "Euros";
	if (MONNAIE == "dollar") return "Dollars";
	if (MONNAIE == "dollarCAD") return "Dollars CAD";
	if (MONNAIE == "dinars") return "Dinars";
	if (MONNAIE == "livre") return "Livres";
	if (MONNAIE == "yen") return "Yens";
	if (MONNAIE == "dirham") return "Dh";
	if (MONNAIE == "FCFA") return "Franc CFA";
	if (MONNAIE == "LAK") return "Kip Laotien";
	if (MONNAIE == "CFP") return "Francs Pacifiques";
	if (MONNAIE == "HTG") return "Gourdes";
	if (MONNAIE == "CHF") return "Franc suisse";
	if (MONNAIE == "FC") return "Franc Congolais";
	return "&euro;";
}

function select_EvalHoraire() {
	global $cnx;
	global $prefixe;
        $data=affEvalHoraire();
	$unite=unitemonnaie();
        for($i=0;$i<count($data);$i++) {
		$libelle=$data[$i][1];
		$id=$data[$i][0];
		$taux=$data[$i][2];
		if ($libelle != "Annulé") {
			print "<option id='select1' value='".$id."'>".$libelle." (".affichageFormatMonnaie($taux)." $unite) </option>\n";
		}
        }
}

function suppEvalHoraire($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}edt_seances WHERE prestation='$id'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data)) {
		alertJs("Suppression impossible, cette vacation est en cours d'utilisation dans l\'emploi du temps.");
	}else{
		$sql="DELETE FROM ${prefixe}vacation_config  WHERE  id='$id'  ";
		execSql($sql);
	}
}


function select_EvalHoraire_search($id) {
	global $cnx;
	global $prefixe;
	$unite=unitemonnaie();
        $data=affEvalHoraireMotif($id); // id,libelle,taux,type_prestation
       	for($i=0;$i<count($data);$i++) {
		$libelle=$data[$i][1];
		$id=$data[$i][0];
		$taux=$data[$i][2];
		print "<option id='select1' value='".$id."'>".$libelle." (".affichageFormatMonnaie($taux)." $unite) </option>\n";
        }
}


function chercheSigneAstro($jour,$mois) {
	/*
	Horoscope des Béliers BELIER du 21 mars au 20 avril  
   	Horoscope des Taureaux TAUREAU du 21 avril au 20 mai 
   	Horoscope des Gémeaux GEMEAUX du 21 mai au 20 juin 
   	Horoscope des Cancers CANCER du 21 juin au 23 juillet 
   	Horoscope des Lions LION du 23 juillet au 23 aout 
   	Horoscope des Vierges VIERGE du 23 aout au 23 septembre 
   	Horoscope des Balances BALANCE du 23 septembre au 23 octobre 
   	Horoscope des Scorpions SCORPION du 23 octobre au 22 novembre 
   	Horoscope des Sagittaires SAGITTAIRE du 22 novembre au 21 decembre 
   	Horoscope des Capricornes CAPRICORNE du 21 decembre au 20 janvier 
   	Horoscope des Verseaux VERSEAU du 20 janvier au 19 fevrier 
	Horoscope des Poissons POISSONS du 19 fevrier au 21 mars 	 
 	*/
	if ((($jour >= 21) && ($mois == 3)) || (($jour <= 20) && ($mois == 4))) { return "belier"; }
	if ((($jour >= 21) && ($mois == 4)) || (($jour <= 20) && ($mois == 4))) { return "taureau"; }
	if ((($jour >= 21) && ($mois == 5)) || (($jour <= 20) && ($mois == 6))) { return "gemeaux"; }
	if ((($jour >= 21) && ($mois == 6)) || (($jour <= 22) && ($mois == 7))) { return "cancer"; }

}

function enrgPresent($saisie_heure,$datedepart,$idmatiere,$idprof,$ideleve) {
	global $cnx;
	global $prefixe;
	$datedepart=dateFormBase($datedepart);
	list($libelle,$heureD,$heureF)=preg_split('/#/',$saisie_heure);
	$sql="INSERT INTO ${prefixe}present (ideleve,idpers,idmatiere,date,horaire) VALUES ('$ideleve','$idprof','$idmatiere','$datedepart','$libelle')";
	execSql($sql);
}

function listingPresent($dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT p.id,p.ideleve,p.idpers,p.idmatiere,p.date,p.horaire,e.nom,e.prenom FROM ${prefixe}present p, ${prefixe}eleves e WHERE p.ideleve=e.elev_id  AND p.date >= '$dateDebut' AND p.date <= '$dateFin' ORDER BY p.date DESC,e.nom";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);
}

function enrConfigVersement($idclasse,$nameversement,$montantversement,$dateversement,$anneescolaire) {
	global $cnx;
	global $prefixe;
	$dateversement=dateFormBase($dateversement);
	$montantversement=preg_replace('/,/','.',$montantversement);
	$sql="INSERT INTO ${prefixe}comptaconfig (idclasse,libellevers,montantvers,datevers,ideleve,anneescolaire) VALUES ('$idclasse','$nameversement','$montantversement','$dateversement','0','$anneescolaire')";
	execSql($sql);
}


function suppConfigVersement($idclasse) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}comptaconfig WHERE idclasse='$idclasse'";
	execSql($sql);
}



function enrConfigVersementEleve($idclasse,$nameversement,$montantversement,$dateversement,$ideleve,$modedepaiement,$anneescolaire) {
	global $cnx;
	global $prefixe;
	$dateversement=dateFormBase($dateversement);
	$montantversement=preg_replace('/,/','.',$montantversement);
	$sql="INSERT INTO ${prefixe}comptaconfig (idclasse,libellevers,montantvers,datevers,ideleve,modedepaiement,anneescolaire) VALUES ('$idclasse','$nameversement','$montantversement','$dateversement','$ideleve','$modedepaiement','$anneescolaire')";
	execSql($sql);

}


function supprConfigCompta($eid) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}comptaconfig WHERE ideleve='$eid'";
	execSql($sql);
}

function recupConfigVersement($idclasse,$anneescolaire=' ') {
	global $cnx;
	global $prefixe;
	if ($idclasse == "tous") {
		$sql="SELECT id,idclasse,libellevers,montantvers,datevers,modedepaiement FROM ${prefixe}comptaconfig WHERE anneescolaire='$anneescolaire' ORDER BY datevers ";
	}else{
		$sql="SELECT id,idclasse,libellevers,montantvers,datevers,modedepaiement FROM ${prefixe}comptaconfig WHERE anneescolaire='$anneescolaire' AND idclasse='$idclasse' AND ideleve='0' ORDER BY datevers ";
	}
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);
}

function recupConfigVersementEleve($ideleve,$anneescolaire='') {
	global $cnx;
	global $prefixe;
	if ($anneescolaire == "") $anneescolaire=anneeScolaire(); 
	if ($ideleve == "tous") {
		$sql="SELECT id,idclasse,libellevers,montantvers,datevers,modedepaiement FROM ${prefixe}comptaconfig WHERE anneescolaire='$anneescolaire'  ORDER BY datevers ";
		$res=execSql($sql);
		$data=ChargeMat($res);
		return($data);
	}else{
		if (trim($ideleve) != "") {
			$sql="SELECT id,idclasse,libellevers,montantvers,datevers,modedepaiement FROM ${prefixe}comptaconfig WHERE anneescolaire='$anneescolaire' AND ideleve='$ideleve'  ORDER BY datevers ";
			$res=execSql($sql);
			$data=ChargeMat($res);
			return($data);
		}
	}
	return null;

}


function recupConfigVersementEleveEtClasse($ideleve,$anneescolaire) {
	global $cnx;
	global $prefixe;
	$idclasse=recupIdClasseEleve($ideleve,$anneescolaire);	
	$sql="SELECT id,idclasse,libellevers,montantvers,datevers,modedepaiement FROM ${prefixe}comptaconfig WHERE anneescolaire='$anneescolaire' AND (ideleve='$ideleve' OR idclasse='$idclasse') ORDER BY datevers ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);
}

function chercheNomVersement($idversement) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libellevers FROM ${prefixe}comptaconfig WHERE id='$idversement' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data[0][1]);
}

function affichageFormatMonnaie($valeur) {
	$nbdecimal=NBDECIMALMONNAIE;
	if (MONNAIE == "euro") {
		$valeur=number_format($valeur,$nbdecimal,',','');
	}elseif(MONNAIE == "livre") {
		$valeur=number_format($valeur, $nbdecimal, ',', '');
	}elseif(MONNAIE == "livre") {
		$valeur=number_format($valeur, $nbdecimal, ',', '');
	}else{
		$valeur=number_format($valeur,$nbdecimal,',','');
	}
	return $valeur;
}



function selectVersement($idclasse) {
	$data=recupConfigVersement($idclasse); //id,idclasse,libellevers,montantvers,datevers
	// $datA : tab bidim - soustab 3 champs
	$unite=unitemonnaie();
	for($i=0;$i<count($data);$i++) {
	        print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][2]." (".affichageFormatMonnaie($data[$i][3])." $unite )</option>\n";
        }	
}

function selectVersementEleve($ideleve) {
	$data=recupConfigVersementEleve($ideleve); //id,idclasse,libellevers,montantvers,datevers
	// $datA : tab bidim - soustab 3 champs
	$unite=unitemonnaie();
	for($i=0;$i<count($data);$i++) {
	        print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][2]." (".affichageFormatMonnaie($data[$i][3])." $unite)</option>\n";
        }	
}

function  selectVersementAjout($idclasse,$ideleve,$anneeScolaire) {
	$data=recupConfigVersement($idclasse,$anneeScolaire); //id,idclasse,libellevers,montantvers,datevers
	// $datA : tab bidim - soustab 3 champs
	$unite=unitemonnaie();
	for($i=0;$i<count($data);$i++) {
		if (verifcomptaExclu($data[$i][0],$ideleve)) {
			$option="optgroup title=\"".$data[$i][2]."\" label=\"".trunchaine($data[$i][2],20)." (".affichageFormatMonnaie($data[$i][3])." $unite) exonéré.\"";
		}elseif (verifVersement($data[$i][0],$ideleve)) {
			$option="optgroup title=\"".$data[$i][2]."\" label=\"".trunchaine($data[$i][2],20)." (".affichageFormatMonnaie($data[$i][3])." $unite) déjà versé.\"";
		}else{
			$option="option id='select1' ";
		}
	        print "<$option  value='".$data[$i][0]."' title=\"".$data[$i][2]."\" >".trunchaine($data[$i][2],20)." (".affichageFormatMonnaie($data[$i][3])." $unite)</option>\n";
        }

	$data2=recupConfigVersementEleve($ideleve,$anneeScolaire); //id,idclasse,libellevers,montantvers,datevers
	for($i=0;$i<count($data2);$i++) {
		if (verifVersement($data2[$i][0],$ideleve)) {
			$option="optgroup title=\"".$data2[$i][2]."\"  label=\"".trunchaine($data2[$i][2],20)." (".affichageFormatMonnaie($data2[$i][3])." $unite) déjà versé.\"";
		}else{
			$option="option id='select1' ";
		}
	        print "<$option  value='".$data2[$i][0]."'  title=\"".$data2[$i][2]."\" >".trunchaine($data2[$i][2],20)." (".affichageFormatMonnaie($data2[$i][3])." $unite)</option>\n";
        }
}

function verifVersement($id,$ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}comptaversement WHERE ideleve='$ideleve' AND  idversement='$id' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		return true;
	}else{
		return false;
	}
}


function suppEleveComptaVersement($id_eleve) {
	global $cnx;
	global $prefixe;
	if (trim($id_eleve) != "") {
		$sql="DELETE FROM ${prefixe}comptaversement WHERE ideleve='$id_eleve'";
		execSql($sql);
		$sql="DELETE FROM ${prefixe}comptaconfig WHERE ideleve='$id_eleve'";
		execSql($sql);
	}
}


function purge_Versement() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}comptaversement";
	execSql($sql);
}

function purge_droitScolarite_eleve() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}comptaconfig WHERE idclasse='0'";
	execSql($sql);
}

function purgeComptabilite() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}comptaversement";
	execSql($sql);
	$sql="TRUNCATE TABLE ${prefixe}comptaconfig";
	execSql($sql);
	$sql="TRUNCATE TABLE ${prefixe}comptaconfigmodele";
	execSql($sql);
}


function suppression_encaissement()  {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}comptaversement";
	execSql($sql);
}


function purgegrpmail()   {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}mail_grp";
	execSql($sql);
}

function purgecantine()   {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}cantine_compte ";
	execSql($sql);
	$sql="TRUNCATE TABLE ${prefixe}cantine_menu ";
	execSql($sql);
}


function purgeCantinePers($id,$membre) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}cantine_compte WHERE idpers='$id' AND membre='$membre' ";
	execSql($sql);
}


function purgeCantineEleve() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}cantine_compte WHERE membre='menueleve' ";
	execSql($sql);
}

function modifCompta($id,$libelle,$montant,$date) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	if (trim($id) != "") {
		$sql="UPDATE ${prefixe}comptaconfig SET libellevers='$libelle',montantvers='$montant',datevers='$date' WHERE id='$id'  ";
		execSql($sql);
	}
}

function suppCompta($id) {
	global $cnx;
	global $prefixe;
	if (trim($id) != "") {
		$sql="DELETE FROM ${prefixe}comptaconfig  WHERE  id='$id'  ";
		execSql($sql);	
	}
}

function enrVersementEleve($typeversement,$montant,$modepaiement,$dateversement,$ideleve,$anneescolaire,$numcheque,$banque) {
	global $cnx;
	global $prefixe;
	$dateversement=dateFormBase($dateversement);
	$montant=preg_replace('/,/','.',$montant);
	$sql="SELECT * FROM ${prefixe}comptaversement WHERE ideleve='$ideleve' AND  idversement='$typeversement' AND datevers='$dateversement' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		return false;
	}
	$sql="INSERT INTO ${prefixe}comptaversement (ideleve,idversement,montantvers,datevers,modepaiement,anneescolaire,num_cheque,etablissement_bancaire) VALUES ('$ideleve','$typeversement','$montant','$dateversement','$modepaiement','$anneescolaire','$numcheque','$banque')";
	return(execSql($sql));
}

function modifVersementEleve($typeversement,$montant,$modepaiement,$dateversement,$ideleve,$oldidvers,$oldiddate,$numcheque,$banque) {
	global $cnx;
	global $prefixe;
	$montant=preg_replace('/,/','.',$montant);
	$dateversement=dateFormBase($dateversement);
	$sql="UPDATE ${prefixe}comptaversement SET idversement='$typeversement' , montantvers='$montant', datevers='$dateversement', modepaiement='$modepaiement',num_cheque='$numcheque', etablissement_bancaire='$banque' WHERE ideleve='$ideleve' AND datevers='$oldiddate'  AND idversement='$oldidvers' ";
	return(execSql($sql));	
}


function listVersement($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,idversement,montantvers,datevers,modepaiement FROM ${prefixe}comptaversement WHERE ideleve='$ideleve' ORDER BY datevers DESC ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);
}

function verifEncaissement($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}comptaversement WHERE idversement='$id'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		return true;
	}else{
		return false;
	}

}


function verifVersementSurConfig($id){
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM ${prefixe}comptaversement WHERE idversement='$id' LIMIT 1";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return(count($data));
}

function recupInfoVersement($ideleve,$id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,idversement,montantvers,datevers,modepaiement,anneescolaire,num_cheque,etablissement_bancaire FROM ${prefixe}comptaversement WHERE ideleve='$ideleve' AND idversement='$id' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);

}

function chercheMontantVersement($ideleve,$dateversement,$idvers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,montantvers FROM ${prefixe}comptaversement WHERE ideleve='$ideleve' AND datevers='$dateversement'  AND idversement='$idvers' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data[0][1]);
}


function chercheVersement($dateversement) {
	global $cnx;
	global $prefixe;
	$dateversement=dateFormBase($dateversement);
	$sql="SELECT ideleve,montantvers,num_cheque,modepaiement,idversement,etablissement_bancaire  FROM ${prefixe}comptaversement WHERE datevers='$dateversement' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);
}

function affichage_ElevesansclasseTotal_limit($depart,$nbaff) {
        global $cnx;
        global $prefixe;
        $sql="SELECT  elev_id,nom,prenom,lv1,lv2,classe  FROM  ${prefixe}eleves WHERE classe='-1'  LIMIT $depart,$nbaff";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}




function chercheVersementPeriode($dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT ideleve,montantvers,num_cheque,modepaiement,idversement,etablissement_bancaire  FROM ${prefixe}comptaversement WHERE datevers >= '$dateDebut' AND  datevers <= '$dateFin'";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);
}

function chercheModePaiement($ideleve,$datevers,$idversement) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,modepaiement FROM ${prefixe}comptaversement WHERE ideleve='$ideleve' AND datevers='$datevers'  AND idversement='$idversement' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data[0][1]);
}

function chercheNumCheque($ideleve,$datevers,$idversement) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,num_cheque FROM ${prefixe}comptaversement WHERE ideleve='$ideleve' AND datevers='$datevers'  AND idversement='$idversement' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data[0][1]);
}

function chercheEtabBancaire($ideleve,$datevers,$idversement) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ideleve,etablissement_bancaire FROM ${prefixe}comptaversement WHERE ideleve='$ideleve' AND datevers='$datevers'  AND idversement='$idversement' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data[0][1]);
}

function suppVersementEleve($typeversement,$montant,$dateversement,$ideleve) {
	global $cnx;
	global $prefixe;
	$dateversement=dateFormBase($dateversement);
	$sql="DELETE FROM ${prefixe}comptaversement  WHERE  ideleve='$ideleve' AND datevers='$dateversement'  AND idversement='$typeversement' AND montantvers='$montant'  ";
	return(execSql($sql));	

}

function anneeScolaire(){
	global $cnx;
	global $prefixe;
	$sql="SELECT annee_scolaire FROM ${prefixe}info_ecole";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}




function stripHTMLtags($texte){
	//On retire le code HTML
	$mots = explode("<",$texte);
	$texte = "";
	$nbmots = count($mots);
	
	for ($m = 0; $m < $nbmots; $m++)
		{
		$mot = $mots[$m];
		$finbalise = strpos($mot,">",0);
		if ($finbalise > 0) { $mot = substr($mot,$finbalise+1); }
		$texte .= "$mot";
		}
		
	return $texte;
}

function listingEleve() {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id,
		nom,
		prenom,
		classe,
		lv1,
		lv2,
		`option`,
		regime,
		date_naissance,
		lieu_naissance,
		nationalite,
		passwd,
		passwd_eleve,
		civ_1,
		nomtuteur,
		prenomtuteur,
		adr1,
		code_post_adr1,
		commune_adr1,
		tel_port_1,
		civ_2,
		nom_resp_2,
		prenom_resp_2,
		adr2,
		code_post_adr2,
		commune_adr2,
		tel_port_2,
		telephone,
		profession_pere,
		tel_prof_pere,
		profession_mere,
		tel_prof_mere,
		nom_etablissement,
		numero_etablissement,
		code_postal_etablissement,
		commune_etablissement,
		numero_eleve,
		photo,
		email,
		email_eleve,
		email_resp_2,
		class_ant,
		annee_ant,
		numero_gep,
		valid_forward_mail_eleve,
		valid_forward_mail_parent,
		tel_eleve,
		code_compta,
		sexe,
		email_eleve,
		adr_eleve,
		ccp_eleve,
		commune_eleve,
		pays_eleve,
		emailpro_eleve,
		annee_scolaire,
		information,
		tel_fixe_eleve
		FROM ${prefixe}eleves ORDER BY nom";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;

}


function listingPersonnel($membre) {
	global $cnx;
	global $prefixe;
	if ($membre != "0") {
		$sqlsuite=" WHERE type_pers='$membre' ";
	}
	$sql="SELECT pers_id,
		nom,
		prenom,
		prenom2,  
		mdp,  
		type_pers, 
		civ,
		photo,
		email,
		valid_forward_mail,
		adr,
		code_post,
		commune, 
		tel, 
		tel_port,
		identifiant, 
		lieudenseigement,
		offline,
		id_societe_tuteur,
		pays,
		indice_salaire,
		qualite
		FROM ${prefixe}personnel
		$sqlsuite
		ORDER BY nom
		";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;

}

function nettoyage_EDT() {
	global $cnx;
	global $prefixe;
	$sql="DELETE from ${prefixe}edt_seances WHERE heure='00:00:00' AND duree='00:00:00' ";
	execSql($sql);
	$sql="DELETE from ${prefixe}edt_seances WHERE duree='00:00:00' ";
	execSql($sql);
	$sql="DELETE from ${prefixe}edt_seances WHERE date='1970-01-01' ";
	execSql($sql);
}


function listingPreinscription($idclasse,$decision,$annee_scolaire) {
	global $cnx;
	global $prefixe;
	if (trim($idclasse) == "") { return ; }
	if ($idclasse != "Tous") { $sqlsuite="classe='$idclasse'"; }
	if ($decision != "Tous") { $sqlsuite.="AND decision='$decision'"; }
	if (trim($annee_scolaire) != "") { $sqlsuite.="AND annee_scolaire='$annee_scolaire'"; }
	$sqlsuite=preg_replace('/^AND/','',$sqlsuite);
	if ($sqlsuite != "") { $sqlsuite="WHERE $sqlsuite"; }
	$sql="SELECT nom,prenom,classe,decision,date_demande,elev_id,annee_scolaire FROM ${prefixe}preinscription_eleves  $sqlsuite ORDER BY date_demande";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function modifPreinscription($ideleve,$status) {
	global $cnx;
	global $prefixe;
	$date=date("Y-m-d");
	$sql="UPDATE ${prefixe}preinscription_eleves SET decision='$status',datedecision='$date' WHERE elev_id='$ideleve'  ";
	execSql($sql);
}

function infoPreinscription($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id,nom,prenom,classe,lv1,lv2,option2,regime,date_naissance,lieu_naissance,nationalite,passwd,passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,photo,email,email_eleve,email_resp_2,class_ant,annee_ant,numero_gep,valid_forward_mail_eleve,valid_forward_mail_parent,tel_eleve,code_compta,sexe,decision,date_demande,datedecision,annee_scolaire,information,adr_eleve,ccp_eleve,commune_eleve,tel_fixe_eleve,pays_eleve,boursier,boursier_montant   FROM ${prefixe}preinscription_eleves WHERE elev_id='$ideleve'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function transferePreinscription($idelevepre) {
	global $cnx;
	global $prefixe;
	$data=infoPreinscription($idelevepre);
	$params[ne]=addslashes($data[0][1]);
	$params[pe]=addslashes($data[0][2]);
	$params[ce]=addslashes($data[0][3]);
	$params[lv1]=addslashes($data[0][4]);
	$params[lv2]=addslashes($data[0][5]);
	$params[option]=addslashes($data[0][6]);
	$params[regime]=addslashes($data[0][7]);
	$params[naiss]=addslashes($data[0][8]);
	$params[lieunais]=addslashes($data[0][9]);
	$params[nat]=addslashes($data[0][10]);
	$params[mdp]=addslashes($data[0][11]);
	$params[mdpeleve]=addslashes($data[0][12]);
	$params[civ_1]=addslashes($data[0][13]);
	$params[nt]=addslashes($data[0][14]);
	$params[pt]=addslashes($data[0][15]);
	$params[adr1]=addslashes($data[0][16]);
	$params[cpadr1]=addslashes($data[0][17]);
	$params[commadr1]=addslashes($data[0][18]);
	$params[tel_port_1]=addslashes($data[0][19]);
	$params[civ_2]=addslashes($data[0][20]);
	$params[nom_resp2]=addslashes($data[0][21]);
	$params[prenom_resp2]=addslashes($data[0][22]);
	$params[adr2]=addslashes($data[0][23]);
	$params[cpadr2]=addslashes($data[0][24]);
	$params[commadr2]=addslashes($data[0][25]);
	$params[tel_port_2]=addslashes($data[0][26]);
	$params[tel]=addslashes($data[0][27]);
	$params[profp]=addslashes($data[0][28]);
	$params[telprofp]=addslashes($data[0][29]);
	$params[profm]=addslashes($data[0][30]);
	$params[telprofm]=addslashes($data[0][31]);
	$params[nomet]=addslashes($data[0][32]);
	$params[numet]=addslashes($data[0][33]);
	$params[cpet]=addslashes($data[0][34]);
	$params[commet]=addslashes($data[0][35]);
	$params[numero_eleve]=addslashes($data[0][36]);
	$photo=$data[0][37];
	$params[email]=addslashes($data[0][38]);
	$params[mail_eleve]=addslashes($data[0][39]);
	$params[email_2]=addslashes($data[0][40]);
	$params[classe_ant]=addslashes($data[0][41]);
	$params[annee_ant]=addslashes($data[0][42]);
	$params[numero_gep]=addslashes($data[0][43]);
	$valid_forward_mail_eleve=$data[0][44];
	$valid_forward_mail_parent=$data[0][45];
	$params[tel_eleve]=addslashes($data[0][46]);
	$params[codecompta]=addslashes($data[0][47]);
	$params[sexe]=addslashes($data[0][48]);
	$params[information]=addslashes($data[0][53]);
	$params[adr_eleve]=addslashes($data[0][54]);
	$params[ccp_eleve]=addslashes($data[0][55]);
	$params[commune_eleve]=addslashes($data[0][56]);
	$params[tel_fixe_eleve]=addslashes($data[0][57]);
	$params[pays_eleve]=addslashes($data[0][58]);
	$params[boursier]=addslashes($data[0][59]);
	$params[boursier_montant]=addslashes($data[0][60]);

	$decision=$data[0][49];
	$date_demande=$data[0][50];
	$date_decision=$data[0][51];
	$cr=create_eleve($params,'');
	if ($cr) {  
		suppfichepreinscription($idelevepre); 
	} 


}

function suppfichepreinscription($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}preinscription_eleves  WHERE  elev_id='$ideleve'  ";
	execSql($sql);	
}

function enrConfigVersementModele($idmodele,$nameversement,$montantversement,$dateversement,$nommodele) {
	global $cnx;
	global $prefixe;
	$dateversement=dateFormBase($dateversement);
	if ($idmodele == "-1") { 
		$nommodele2=$nommodele;
		$idmodele=md5(date("YmdHms").rand(1000,9999));
		$sql="INSERT INTO ${prefixe}comptaconfigmodele (refmodele,nommodele,libellevers,montantvers,datevers) VALUES ('$idmodele','$nommodele2','$nameversement','$montantversement','$dateversement')";
	}else{
		$nommodele2=chercheNomModeleVers($idmodele);
		$sql="INSERT INTO ${prefixe}comptaconfigmodele (refmodele,nommodele,libellevers,montantvers,datevers) VALUES ('$idmodele','$nommodele2','$nameversement','$montantversement','$dateversement')";
		
	}
	return(execSql($sql));
}

function chercheNomModeleVers($idmodele) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nommodele FROM ${prefixe}comptaconfigmodele WHERE refmodele='$idmodele' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}

function chercheIdConfigVersModele($nommodele) {
	global $cnx;
	global $prefixe;
	$sql="SELECT refmodele FROM ${prefixe}comptaconfigmodele WHERE nommodele='$nommodele' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}

function recupConfigVersementModele($refmodele) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,refmodele,nommodele,libellevers,montantvers,datevers FROM ${prefixe}comptaconfigmodele WHERE refmodele='$refmodele' ORDER BY datevers ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return($data);
}

function affModele() {
	global $cnx;
	global $prefixe;
	$sql="SELECT refmodele,nommodele FROM ${prefixe}comptaconfigmodele";
	$res=execSql($sql);
	$data=ChargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$refmodele=$data[$i][0];
		$nommodele=$data[$i][1];
		$tab[$refmodele]=$nommodele;
	}
	return($tab);
}

function suppComptaModele($id) {
	global $cnx;
	global $prefixe;
	if (trim($id) != "") {
		$sql="DELETE FROM ${prefixe}comptaconfigmodele  WHERE  id='$id'  ";
		execSql($sql);	
	}	
}


function modifComptaModele($id,$libelle,$montant,$date) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="UPDATE ${prefixe}comptaconfigmodele SET libellevers='$libelle',montantvers='$montant',datevers='$date' WHERE id='$id'  ";
	execSql($sql);
}


function listingModele() {
	global $cnx;
	global $prefixe;
	$data=affModele();
	foreach($data as $key=>$value) {
		print "<option value='$key' id='select0' >".$value."</option>";
	}
}

function enrConfigVersementEleveViaModele($refModele,$ideleve,$idclasse,$modedepaiement,$anneescolaire) {
	global $cnx;
	global $prefixe;
	$data=recupConfigVersementModele($refModele); //id,refmodele,nommodele,libellevers,montantvers,datevers
	for($i=0;$i<count($data);$i++) {
		$nameversement=addslashes($data[$i][3]);
		$montantversement=$data[$i][4];
		$dateversement=$data[$i][5];
		$sql="INSERT INTO ${prefixe}comptaconfig (idclasse,libellevers,montantvers,datevers,ideleve,modedepaiement,anneescolaire) VALUES ('$idclasse','$nameversement','$montantversement','$dateversement','$ideleve','$modedepaiement','$anneescolaire')";
		execSql($sql);
	}

}


function nettoyageProfP() {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,idclasse FROM ${prefixe}prof_p";
	$res=execSql($sql);
	$data=ChargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$idprof=$data[$i][0];
		$idclasse=$data[$i][1];
		$sql="SELECT pers_id FROM ${prefixe}personnel WHERE pers_id='$idprof' ";
		$res=execSql($sql);
		$data2=ChargeMat($res);
		if (!count($data2)) {
			$sql="DELETE FROM ${prefixe}prof_p  WHERE  idprof='$idprof'  ";
			execSql($sql);
		}
		$sql="SELECT code_class FROM ${prefixe}classes WHERE code_class='$idclasse' ";
		$res=execSql($sql);
		$data2=ChargeMat($res);
		if (!count($data2)) {
			$sql="DELETE FROM ${prefixe}prof_p  WHERE  idclasse='$idclasse'  ";
			execSql($sql);
		}
	}
}

function nbpers() {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id FROM ${prefixe}personnel  ";
	$res=execSql($sql);
	$data2=ChargeMat($res);	
	$nbpers=count($data2);

	$sql="SELECT elev_id FROM ${prefixe}eleves  ";
	$res=execSql($sql);
	$data2=ChargeMat($res);	
	$nbeleve=count($data2);

	return($_SERVER["SERVER_NAME"].":$nbeleve:$nbpers");
}

function enregistrement_note_scolaire_cpe($idmatiere,$idclasse,$tri,$idEleve,$note,$idprof,$idgroupe,$commentaire,$examen=0,$anneeScolaire) {
        global $cnx;
        global $prefixe;
        if (trim($note) == "") {  $note="-1"; }
        $sql="DELETE FROM ${prefixe}notes_scolaire WHERE idmatiere='-10' AND idclasse='$idclasse' AND trimestre='$tri' AND ideleve='$idEleve' AND examen='$examen' AND annee_scolaire='$anneeScolaire'  ";
        execSql($sql);
        $sql="INSERT INTO ${prefixe}notes_scolaire (idmatiere,idclasse,trimestre,ideleve,note,idprof,idgroupe,commentaire,examen,annee_scolaire) VALUES ('-10','$idclasse','$tri','$idEleve','$note','$idprof','0','$commentaire','$examen','$anneeScolaire')";
    	return(execSql($sql));
}


function paiementVacation($idprof,$dateDebut,$dateFin,$infopaiement,$montantHT,$montantTTC,$tva,$idpiecejointe) {
        global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$datetransaction=dateDMY2();
	$sql="INSERT INTO ${prefixe}vacation_paiement (id_prof,datedebut,datefin,montant_ht,montant_tc,montant_tva,info,datetransaction,idpiecejointe)  VALUES ('$idprof', '$dateDebut', '$dateFin', '$montantHT', '$montantTTC', '$tva','$infopaiement','$datetransaction','$idpiecejointe');";
	execSql($sql);
}

function listingPaiementVacation($idprof) {
        global $cnx;
	global $prefixe;
	$sql="SELECT id_prof,datedebut,datefin,montant_ht,montant_tc,montant_tva,info,datetransaction,idpiecejointe,id FROM ${prefixe}vacation_paiement WHERE id_prof='$idprof' ORDER BY datetransaction DESC";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function delete_comptaVacation($idprof) {
        global $cnx;
	global $prefixe;
	$data=listingPaiementVacation($idprof);
	for($i=0;$i<count($data);$i++) { deletePaimentVacation($data[$i][9]); }
}

function deletePaimentVacation($id) {
        global $cnx;
	global $prefixe;
	$sql="SELECT idpiecejointe FROM ${prefixe}vacation_paiement WHERE id='$id'";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) {
		$idpiecejointe=$data[0][0];
		@unlink("./data/comptaenseignant/$idpiecejointe.pdf");
		$sql="DELETE FROM ${prefixe}vacation_paiement WHERE id='$id'";
		execSql($sql);
	}
}

function paiementEffectue($idprof) {
        global $cnx;
	global $prefixe;
	$sql="SELECT montant_ht,montant_tc,montant_tva FROM ${prefixe}vacation_paiement WHERE id_prof='$idprof'";
	$data=ChargeMat(execSql($sql));
	return($data);
}


function delete_coef_bulletin($typebull,$idclasse) {
        global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_blanc_coef WHERE idclasse='$idclasse' AND type_bull='$typebull'";
	execSql($sql);
}

function enr_coef_bulletin($typebull,$idclasse,$idmatiere,$coef,$k) {
        global $cnx;
	global $prefixe;
	$coef=number_format($coef,2,'.','');
	$sql="INSERT INTO ${prefixe}bulletin_blanc_coef (idclasse,idmatiere,coef,type_bull,ordre) VALUES ('$idclasse','$idmatiere','$coef','$typebull',$k);";
	execSql($sql);
}


function recup_coef_bulletin($typebull,$idclasse,$idmatiere,$k) {
        global $cnx;
	global $prefixe;
	$sql="SELECT coef FROM ${prefixe}bulletin_blanc_coef WHERE idclasse='$idclasse' AND idmatiere='$idmatiere' AND type_bull='$typebull' AND ordre='$k'";
	$data=ChargeMat(execSql($sql));
	return($data[0][0]);
}

function envoiMessageIntraMSN($message,$loginMSN) { // loginMSN au format nom.prénom
        global $cnx;
	global $prefixe;
	$loginMSN=addslashes(strtolower($loginMSN));
	if (file_exists("./common/config-messenger.php")) {
		$sql="SELECT ID_USER FROM ${prefixe}im_USR_USER WHERE  USR_USERNAME='$loginMSN'";
		$data=ChargeMat(execSql($sql));
		$time=dateHIS();
		if (count($data) > 0) {
			$idMSN=$data[0][0];
			$sql="INSERT INTO ${prefixe}im_MSG_MESSAGE (ID_USER_AUT,ID_USER_DEST,MSG_TEXT,MSG_CR,MSG_ETAT,MSG_TIME,MSG_DATE,ID_CONFERENCE) VALUES ('-99','$idMSN','$message','','0','$time','0000-00-00', '0')";
			execSql($sql);
		}
	}
}

function turnOverLog($fichier,$size) {
	if (!file_exists($fichier)) { return; }
	if (!is_numeric($size)) { return ; }
	if (filesize($fichier) >= $size) {
		@copy($fichier,"${fichier}.old");
		@unlink($fichier);
		@touch($fichier);
	}
}

function lireFichierBasEnhaut($fichier) {
	$array_file_content = array();
	$file_content = '';

	// le fichier 
	$handle = @fopen("$fichier", "r" );

	// lecture & copie dans une variable 
	if ($handle) {
	   while (!feof($handle)) {
	      $file_content .= fgets($handle, 4096);
  	 }
 	  fclose($handle);
	}

	// explosion en tableau 
	$array_file_content = explode("\n",$file_content);

	// lecture du tableau à l'envers 
	$nb_lines = count($array_file_content); // nombre de lignes 

	for ($ptr = $nb_lines; $ptr >=0; $ptr--)
	{
	  echo $array_file_content[$ptr];
	}
}

function verifPdfProfVersement($fic,$id_pers) {
	global $cnx;
	global $prefixe;
	$fic=preg_replace('/\.\/data\/comptaenseignant\//',"",$fic);
	$fic=preg_replace('/\.pdf/',"",$fic);
	$sql="SELECT * FROM  ${prefixe}vacation_paiement  WHERE idpiecejointe='$fic' AND id_prof='$id_pers' ";
	$data=ChargeMat(execSql($sql));
	return(count($data));
	
}

function enrHistoEleve($ideleve,$date,$action,$info) {
        global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="INSERT INTO ${prefixe}history_eleve (ideleve,date,action,info) VALUES ('$ideleve','$date','$action','$info');";
	execSql($sql);
}

function listingHistoEleve($idEleve) {
        global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="SELECT date,action,info FROM  ${prefixe}history_eleve  WHERE ideleve='$idEleve' ORDER BY date DESC LIMIT 500 ";
	$data=ChargeMat(execSql($sql));
	return($data);

}



function infoBulleEleve($idEleve) {
        global $cnx;
	global $prefixe;
	$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id,e.tel_port_1,e.tel_port_2,e.telephone,e.tel_eleve,email,email_resp_2,probatoire
	      FROM ${prefixe}eleves e, ${prefixe}classes c WHERE e.elev_id='$idEleve' AND e.classe=c.code_class ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][10] == 1) $infoprobatoire="<img src=image/commun/important.png /><b>En pr&eacute;riode probatoire.</b><br>";
	$information=strtoupper($data[0][1])." ".ucwords($data[0][2]);
	$mess="<table width=100%>";
	$mess.="<tr><td valign=top width=95%>";
	$mess.=$infoprobatoire;
	$mess.="Classe : <font color=blue>".html_quotes($data[0][0])."</font>";
	$mess.="<br /> Tél resp 1 : <font color=blue>".$data[0][4]." / ".$data[0][6]."</font>" ; 
	$mess.="<br /> Tél resp 2 : <font color=blue>".$data[0][5]."</font>" ; 
	$mess.="<br /> Tél Elève : <font color=blue>".$data[0][7]."</font>"; 
	$mess.="<br>Email resp 1 : <font color=blue>".$data[0][8]."</font>" ; 
	$mess.="<br>Email resp 2 : <font color=blue>".$data[0][9]."</font>" ; 
	$mess.="</td>";
	$mess.="<td valign=top>";
	$mess.="<img src=\'image_trombi.php?idE=".$data[0][3]."\' border=0 >";
	$mess.="</td>";
	$mess.="</tr></table>";
	$information=preg_replace("/'/","\'",$information);
	print "&nbsp;<a href='#'  onMouseOver=\"AffBulle3('$information','./image/commun/info.jpg','$mess'); return false;\"  onMouseOut=\"HideBulle()\"; ><img src='./image/commun/affichage.gif' border='0'/></a>";

	//<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
	//<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
}

function infoBulleEleveSansLoupe($idEleve,$lien) {
        global $cnx;
	global $prefixe;
	$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id,e.tel_port_1,e.tel_port_2,e.telephone,e.tel_eleve,email,email_resp_2,probatoire
	      FROM ${prefixe}eleves e, ${prefixe}classes c WHERE e.elev_id='$idEleve' AND e.classe=c.code_class ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][10] == 1) $infoprobatoire="<img src=image/commun/important.png /><b>En pr&eacute;riode probatoire.</b><br>";
	$information=strtoupper($data[0][1])." ".ucwords($data[0][2]);
	$mess="<table width=100%>";
	$mess.="<tr><td valign=top width=95%>";
	$mess.=$infoprobatoire;
	$mess.="Classe : <font color=blue>".html_quotes($data[0][0])."</font>";
	$mess.="<br /> Tél resp 1 : <font color=blue>".$data[0][4]." / ".$data[0][6]."</font>" ; 
	$mess.="<br /> Tél resp 2 : <font color=blue>".$data[0][5]."</font>" ; 
	$mess.="<br /> Tél Elève : <font color=blue>".$data[0][7]."</font>"; 
	$mess.="<br>Email resp 1 : <font color=blue>".$data[0][8]."</font>" ; 
	$mess.="<br>Email resp 2 : <font color=blue>".$data[0][9]."</font>" ; 
	$mess.="</td>";
	$mess.="<td valign=top>";
	$mess.="<img src=\'image_trombi.php?idE=".$data[0][3]."\' border=0 >";
	$mess.="</td>";
	$mess.="</tr></table>";
	print "&nbsp;<a href='#'  onMouseOver=\"AffBulle3('$information','./image/commun/info.jpg','$mess'); return false;\"  onMouseOut=\"HideBulle()\"; >".$lien."</a>";

	//<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
	//<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
}

function infoBulleEleveSansLoupeAvecLien($idEleve,$Nomlien,$lien) {
        global $cnx;
        global $prefixe;
        $sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id,e.tel_port_1,e.tel_port_2,e.telephone,e.tel_eleve,email,email_resp_2
              FROM ${prefixe}eleves e, ${prefixe}classes c WHERE e.elev_id='$idEleve' AND e.classe=c.code_class ";
        $res=execSql($sql);
        $data=chargeMat($res);
        $information=strtoupper($data[0][1])." ".ucwords($data[0][2]);
        $mess="<table width=100%>";
        $mess.="<tr><td valign=top width=95%>";
        $mess.="Classe : <font color=blue>".html_quotes($data[0][0])."</font>";
        $mess.="<br /> Tél resp 1 : <font color=blue>".$data[0][4]." / ".$data[0][6]."</font>" ;
        $mess.="<br /> Tél resp 2 : <font color=blue>".$data[0][5]."</font>" ;
        $mess.="<br /> Tél Elève : <font color=blue>".$data[0][7]."</font>";
        $mess.="<br>Email resp 1 : <font color=blue>".$data[0][8]."</font>" ;
        $mess.="<br>Email resp 2 : <font color=blue>".$data[0][9]."</font>" ;
        $mess.="</td>";
        $mess.="<td valign=top>";
        $mess.="<img src=\'image_trombi.php?idE=".$data[0][3]."\' border=0 >";
        $mess.="</td>";
        $mess.="</tr></table>";
        return "&nbsp;<a href='$lien'  onMouseOver=\"AffBulle3('$information','./image/commun/info.jpg','$mess'); return false;\"  onMouseOut=\"HideBulle()\"; >".$Nomlien."</a>";
        //<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
        //<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
}



function infoBulleEleveSansLoupe2($idEleve,$lien) {
        global $cnx;
	global $prefixe;
	$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id,e.tel_port_1,e.tel_port_2,e.telephone,e.tel_eleve,email,email_resp_2
	      FROM ${prefixe}eleves e, ${prefixe}classes c WHERE e.elev_id='$idEleve' AND e.classe=c.code_class ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$information=strtoupper($data[0][1])." ".ucwords($data[0][2]);
	$mess="<table width=100%>";
	$mess.="<tr><td valign=top width=95%>";
	$mess.="Classe : <font color=blue>".html_quotes($data[0][0])."</font>";
	$mess.="<br /> Tél resp 1 : <font color=blue>".$data[0][4]." / ".$data[0][6]."</font>" ; 
	$mess.="<br /> Tél resp 2 : <font color=blue>".$data[0][5]."</font>" ; 
	$mess.="<br /> Tél Elève : <font color=blue>".$data[0][7]."</font>"; 
	$mess.="<br>Email resp 1 : <font color=blue>".$data[0][8]."</font>" ; 
	$mess.="<br>Email resp 2 : <font color=blue>".$data[0][9]."</font>" ; 
	$mess.="</td>";
	$mess.="<td valign=top>";
	$mess.="<img src=\'image_trombi.php?idE=".$data[0][3]."\' border=0 >";
	$mess.="</td>";
	$mess.="</tr></table>";
	return "&nbsp;<a href='#'  onMouseOver=\"AffBulle3('$information','./image/commun/info.jpg','$mess'); return false;\"  onMouseOut=\"HideBulle()\"; >".$lien."</a>";

	//<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
	//<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
}

function verifcomptaExclu($idcomptaclasse,$eid) {
        global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM  ${prefixe}comptaexclu  WHERE ideleve='$eid' AND idcomptaclasse='$idcomptaclasse' ";
	$data=ChargeMat(execSql($sql));
	if (count($data)) {
		return true;
	}else{
		return false;
	}
}


function comptaValideClasse($idcomptaclasse,$eid) {
        global $cnx;
	global $prefixe;
	$sql="DELETE  FROM  ${prefixe}comptaexclu  WHERE ideleve='$eid' AND idcomptaclasse='$idcomptaclasse' ";
	execSql($sql);
}

function comptaExcluClasse($idcomptaclasse,$eid) {
        global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM  ${prefixe}comptaexclu  WHERE ideleve='$eid' AND idcomptaclasse='$idcomptaclasse' ";
	$data=ChargeMat(execSql($sql));
	if (count($data)) {
		return true;
	}else{
		$sql="INSERT INTO ${prefixe}comptaexclu (ideleve,idcomptaclasse) VALUES ('$eid','$idcomptaclasse');";
		execSql($sql);
	}
}

function listEleveTuteur($id,$nb) {
 	global $cnx;
	global $prefixe;
	$sql="SELECT id_eleve FROM  ${prefixe}stage_eleve  WHERE compte_tuteur_stage='$id' GROUP BY id_eleve";
	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
		$eid=$data[$i][0];
		$nom=recherche_eleve_nom($eid);
		$prenom=recherche_eleve_prenom($eid);
	        print "<option id='select1' value='$eid' title=\"".strtoupper($nom)." $prenom\" >".trunchaine(strtoupper($nom)." ".$prenom,$nb)."</option>\n";
        }
}


function listEleveTuteur2($id) {
	global $cnx;
        global $prefixe;
        $sql="SELECT id_eleve FROM  ${prefixe}stage_eleve  WHERE compte_tuteur_stage='$id' GROUP BY id_eleve";
        $data=ChargeMat(execSql($sql));
	return($data);
}



function recupAdrEleve($idClasse,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	if (trim($anneeScolaire) == '') $_COOKIE["anneeScolaire"];
	$sql="SELECT nom,prenom,adr_eleve,ccp_eleve,commune_eleve,sexe,pays_eleve,numero_eleve FROM ${prefixe}eleves WHERE classe='$idClasse' AND compte_inactif != 1 AND annee_scolaire='$anneeScolaire' ORDER BY nom,prenom";
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	return $liste;
}


function recupAdrParent1($idClasse,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	if (trim($anneeScolaire) == '') $_COOKIE["anneeScolaire"];
	$sql="SELECT nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,civ_1,pays_eleve,numero_eleve FROM ${prefixe}eleves WHERE classe='$idClasse' AND compte_inactif != 1 AND annee_scolaire='$anneeScolaire' ORDER BY nom,prenom";
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	return $liste;
}


function recupAdrParent2($idClasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nomtuteur,prenomtuteur,adr2,code_post_adr2,commune_adr2,civ_2,pays_eleve,numero_eleve FROM ${prefixe}eleves WHERE classe='$idClasse' AND compte_inactif != 1 ORDER BY nom,prenom";
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	return $liste;
}


function recupAdrMembre($membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom,prenom,adr,code_post,commune,civ,pays FROM ${prefixe}personnel WHERE type_pers='$membre' ORDER BY nom,prenom";
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	return $liste;
}

function verifEditeDevoir($iddevoir,$idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM  ${prefixe}devoir_scolaire  WHERE idprof ='$idpers' AND id='$iddevoir' ";
	$data=ChargeMat(execSql($sql));
	if (count($data)) {
		return true;
	}else{
		return false;
	}
}

function verifEditeObjectif($iddevoir,$idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM  ${prefixe}cahiertexte  WHERE idprof ='$idpers' AND id='$iddevoir' ";
	$data=ChargeMat(execSql($sql));
	if (count($data)) {
		return true;
	}else{
		return false;
	}
}

function verifEditeContenu($iddevoir,$idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT *  FROM  ${prefixe}cahiertexte  WHERE idprof ='$idpers' AND id='$iddevoir' ";
	$data=ChargeMat(execSql($sql));
	if (count($data)) {
		return true;
	}else{
		return false;
	}
}

function recupIdCodeBar($idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,id_pers,membre  FROM  ${prefixe}codebar  WHERE id_pers ='$idpers' AND membre='$membre' ";
	$data=ChargeMat(execSql($sql));
	if (count($data)) {
		return $data[0][0];
	}else{
		while(true) {
			$id=rand(1000,9999);
			$sql="SELECT * FROM  ${prefixe}codebar  WHERE id='$id'";
			$data=ChargeMat(execSql($sql));
			if (count($data) > 0) {
				continue;
			}
			break;
		}
		$sql="INSERT INTO ${prefixe}codebar (id,id_pers,membre) VALUES ('$id','$idpers','$membre');";
		execSql($sql);
		return $id;
	}
}


function recupCodeBar($idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id FROM  ${prefixe}codebar WHERE id_pers='$idpers' AND membre='$membre' ";
	$data=ChargeMat(execSql($sql));
	return($data[0][0]);
}


function verifCodebarre($idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,id_pers,membre  FROM  ${prefixe}codebar  WHERE id_pers ='$idpers' AND membre='$membre' AND valide='1' ";
	$data=ChargeMat(execSql($sql));
	if (count($data)) {
		return true;
	}else{
		return false;
	}
	
}

function suppIdCodeBar($idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="DELETE  FROM  ${prefixe}codebar   WHERE id_pers ='$idpers' AND membre='$membre'";
	execSql($sql);
}

function valideIdCodeBar($idpers,$membre,$valide) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}codebar SET valide='$valide' WHERE id_pers ='$idpers' AND membre='$membre'";
	execSql($sql);
}

function ModifCodeBar($idpers,$membre,$idcode) {
	global $cnx;
	global $prefixe;
	$sql="UPDATE ${prefixe}codebar SET id='$idcode' WHERE id_pers ='$idpers' AND membre='$membre'";
	execSql($sql);
}

function rechercheIdEleveViaCodeBarre($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_pers,valide,membre FROM  ${prefixe}codebar  WHERE id='$id' AND membre='menueleve'";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function rechercheIdPersViaCodeBarre($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id_pers,valide,membre FROM  ${prefixe}codebar  WHERE id='$id'";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function accesNonReserve() {
	print "<br><font class='T2' id='color3'><center><img src='image/commun/img_ssl.gif' align='center' /> Accès non autorisé.</center></font>";
}


function accesNonReserveFen() {
	print "<script>location.href='noaccess.php';</script>";
}


function droitModule($idpers,$params) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}droitmodule WHERE idpers='$idpers'";
	execSql($sql);
	foreach($params as $key => $value) {
		$sql="INSERT INTO ${prefixe}droitmodule (idpers,module,permission) VALUE ('$idpers','$key','$value')";
		execSql($sql);
	}
}

function verifDroit($idpers,$module) {
	global $cnx;
	global $prefixe;
	$sql="SELECT * FROM  ${prefixe}droitmodule  WHERE idpers='$idpers' AND module='$module' AND permission='1'";
	$data=ChargeMat(execSql($sql));
	return count($data);
}

function ajoutPlateau($plat,$prix,$attribue,$indiceSalaire,$platdefault) {
	global $cnx;
	global $prefixe;
	$prix=preg_replace('/,/','.',$prix);
	$sql="INSERT INTO ${prefixe}cantine_menu (libelle,prix,attribue,indice_salaire,platdefault) VALUE ('$plat','$prix','$attribue','$indiceSalaire','$platdefault')";
	execSql($sql);
}

function modifPlateau($plat,$prix,$attribue,$indiceSalaire,$id,$platdefault) {
	global $cnx;
	global $prefixe;
	$prix=preg_replace('/,/','.',$prix);
	$sql="UPDATE ${prefixe}cantine_menu SET libelle='$plat', prix='$prix', attribue='$attribue', indice_salaire='$indiceSalaire', platdefault='$platdefault' WHERE id='$id'";
	execSql($sql);
}

function cherchePlateauCantine($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,prix,attribue,platdefault FROM  ${prefixe}cantine_menu  WHERE id='$id'";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function recupConfigCantine() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,prix,attribue,indice_salaire,platdefault FROM  ${prefixe}cantine_menu ORDER BY libelle";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function suppPlateau($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}cantine_menu WHERE id='$id'";
	execSql($sql);
}

function recupIndiceSalairePers($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT indice_salaire FROM  ${prefixe}personnel WHERE pers_id='$idpers'";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];
}


function recupListIndiceSalaire() {
	global $cnx;
	global $prefixe;
	$sql="SELECT indice_salaire FROM  ${prefixe}personnel GROUP BY indice_salaire ORDER BY indice_salaire";
	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][0] != 0) {
			print "<option value=\"".$data[$i][0]."\" id='select1'  >".$data[$i][0]."</option>";
		}
	}
}


function recupListPlateau() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,prix,attribue,indice_salaire,platdefault FROM  ${prefixe}cantine_menu ORDER BY 2 ";
	$data=ChargeMat(execSql($sql));
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][0] != 0) {
			print "<option value=\"".$data[$i][0]."\" id='select1'  >".$data[$i][1]."</option>";
		}
	}

}

function creditCantine($idpers,$membre,$libelle,$date,$prix) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$prix=preg_replace('/,/','.',$prix);
	$sql="INSERT INTO ${prefixe}cantine_compte (idpers,membre,date,prix,plateau) VALUE ('$idpers','$membre','$date','$prix','$libelle')";
	return(execSql($sql));
}

function enrPlateauCompte($idpers,$plat,$membre) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	foreach($plat as $key=>$value) {
		list($prix,$libelle)=preg_split('/\#\|\|\#/',$value);
		$sql="INSERT INTO ${prefixe}cantine_compte (idpers,membre,date,prix,plateau) VALUE ('$idpers','$membre','$date','-$prix','$libelle')";
		execSql($sql);
	}
}

function verifCompteDejaPasse($idpers,$membre) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$sql="SELECT * FROM  ${prefixe}cantine_compte WHERE idpers='$idpers' AND date='$date' AND membre='$membre' AND prix LIKE '-%' ";
	$data=ChargeMat(execSql($sql));
	return count($data);
}

function nbpassagecantine() {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$sql="SELECT * FROM  ${prefixe}cantine_compte WHERE date='$date' AND prix LIKE '-%' GROUP BY idpers ";
	$data=ChargeMat(execSql($sql));
	return count($data);
}

function recupComptaPers($idpers,$membre) {
	global $cnx;
	global $prefixe;
	if ($membre == "menuparent") $membre="menueleve";
	$sql="SELECT date,prix,plateau,id FROM  ${prefixe}cantine_compte WHERE idpers='$idpers' AND membre='$membre' ORDER BY date DESC ";
	$data=ChargeMat(execSql($sql));
	return($data);
}


function listingCantine($dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT sum(prix),plateau,count(plateau),prix FROM  ${prefixe}cantine_compte WHERE date >='$dateDebut' AND date <= '$dateFin' AND prix LIKE '-%' GROUP BY 2";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function suppOperationCantine($id){
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}cantine_compte WHERE id='$id'";
	return(execSql($sql));
}


function recupConfig($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,libelle,prix,attribue,indice_salaire,platdefault FROM  ${prefixe}cantine_menu WHERE id='$id' ";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function sommeComptaPers($idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT date,prix,plateau FROM  ${prefixe}cantine_compte WHERE idpers='$idpers' AND membre='$membre'";
	$data=ChargeMat(execSql($sql));
	$total=0;
	for($i=0;$i<count($data);$i++) {
		$total+=$data[$i][1]; 
	}
	return affichageFormatMonnaie($total);
}


function valideProductId() {
	global $cnx;
	global $prefixe;
	$idProduct=PRODUCTID;
	if (trim($idProduct) != "000") { return; }
	$val=$_SERVER["HTTP_REFERER"].time();
	$idProduct=md5($val);
	$texte="<?php\n";
	$texte.="define(\"PRODUCTID\",\"$idProduct\");\n";
	$texte.="?>\n";
	$fichier="./common/productId.php";
	$fichier=fopen($fichier,"w");
        fwrite($fichier,$texte);
	fclose($fichier);
}


function enrAbsSconet($numEleve,$nbAbs,$nbNonJustif,$nbRet,$tri,$nom,$prenom) {
	global $cnx;
	global $prefixe;
	$idEleve=chercheIdEleveViaNumeroEleve($numEleve);
	if (trim($idEleve) == "") {
		$nom=addslashes($nom);
		$prenom=addslashes($prenom);
		$idEleve=chercheIdEleveViaNomPrenom($nom,$prenom);
	}
	if ($idEleve > 0) {
		$sql="DELETE FROM ${prefixe}absences_sconet WHERE ideleve='$idEleve' AND trimestre='$tri'";
		execSql($sql);
		$sql="INSERT INTO ${prefixe}absences_sconet (ideleve,nb_abs,nb_abs_no_just,nb_rtd,trimestre) VALUE ('$idEleve','$nbAbs','$nbNonJustif','$nbRet','$tri')";
		return(execSql($sql));
	}
}

function recupAbsRtdSconet($ideleve,$tri) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nb_abs,nb_abs_no_just,nb_rtd FROM ${prefixe}absences_sconet WHERE ideleve='$ideleve' AND trimestre='$tri' ";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function MiseAbsRtdSconet($ideleve,$nbrabs,$nbrabsnonjust,$nbrrtd,$tri) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}absences_sconet WHERE ideleve='$ideleve' AND trimestre='$tri' ";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}absences_sconet (ideleve,nb_abs,nb_abs_no_just,nb_rtd,trimestre) VALUE ('$ideleve','$nbrabs','$nbrabsnonjust','$nbrrtd','$tri')";
	execSql($sql);
}

function nombre_retard_sconet($ideleve,$tri) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nb_rtd FROM ${prefixe}absences_sconet WHERE ideleve='$ideleve' AND trimestre='$tri' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];
}

function nombre_abs_sconet($ideleve,$tri) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nb_abs FROM ${prefixe}absences_sconet WHERE ideleve='$ideleve' AND trimestre='$tri' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];
}

function nombre_abs_nonjustifie_sconet($ideleve,$tri) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nb_abs_no_just FROM ${prefixe}absences_sconet WHERE ideleve='$ideleve' AND trimestre='$tri' ";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];
}

function creationStageCentralSouhait($datedemande,$identreprise,$sexe,$service,$observation,$idperiode,$nbdemande,$salaire,$logement,$service2='',$sexe2='',$service3='',$sexe3='',$nbdemande2='',$nbdemande3='',$indemnitestage='',$repas='',$typerepas=''){
        global $cnx;
        global $prefixe;
        $datedemande=dateFormBase($datedemande);
        $sql="INSERT INTO ${prefixe}centralstagesouhait  (datedemande,identreprise,sexe,service,observation,idperiode,nbdemande,salaire,logement,indemnitestage,repas,typerepas)VALUE ('$datedemande','$identreprise','$sexe','$service','$observation','$idperiode','$nbdemande','$salaire','$logement','$indemnitestage','$repas','$typerepas')";
        $cr=execSql($sql);

        if (trim($service2) != "") {
                $sql="INSERT INTO ${prefixe}centralstagesouhait  (datedemande,identreprise,sexe,service,observation,idperiode,nbdemande,salaire,logement,indemnitestage,repas,typerepas)VALUE ('$datedemande','$identreprise','$sexe2','$service2','$observation','$idperiode','$nbdemande2','$salaire','$logement','$indemnitestage','$repas','$typerepas')";
                execSql($sql);
        }

        if (trim($service3) != "") {
                $sql="INSERT INTO ${prefixe}centralstagesouhait  (datedemande,identreprise,sexe,service,observation,idperiode,nbdemande,salaire,logement,indemnitestage,repas,typerepas)VALUE ('$datedemande','$identreprise','$sexe3','$service3','$observation','$idperiode','$nbdemande3','$salaire','$logement','$indemnitestage','$repas','$typerepas')";
                execSql($sql);
        }
        return($cr);
}


function periodeStageCentralSouhait() {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$sql="SELECT debut_periode,fin_periode,id FROM ${prefixe}centralstagesouhait WHERE fin_periode >= '$date' GROUP BY debut_periode,fin_periode ORDER BY debut_periode ";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function recupInfoCentralStage($id) {
        global $cnx;
        global $prefixe;
        $date=dateDMY2();
        $sql="SELECT datedemande,identreprise,sexe,service,observation,nbdemande,idperiode,null,salaire,logement,indemnitestage,repas FROM ${prefixe}centralstagesouhait WHERE id='$id'";
        $data=ChargeMat(execSql($sql));
        return $data;
}

function suppressionInfoCentralStage($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}centralstagesouhait WHERE id='$id'";
	return(execSql($sql));	
}

function modifInfoCentralStage($id,$identreprise,$sexe,$service,$observation,$nbdemande,$idperiode,$salaire,$date,$logement) {
	global $cnx;
	global $prefixe;
	$date=dateFormBase($date);
	$sql="UPDATE ${prefixe}centralstagesouhait SET 
		datedemande='$date',
		identreprise='$identreprise',
		sexe='$sexe',
		service='$service',
		observation='$observation',
		nbdemande='$nbdemande',
		idperiode='$idperiode',
		logement='$logement',
		salaire='$salaire'  WHERE id='$id' ";
	return(execSql($sql));	
}

function recupPeriodeStageCentralSouhait($id) {
	global $cnx;
	global $prefixe;
	// id,datedebut,datefin,nomstage FROM ${prefixe}centralstagedate
	$sql="SELECT datedebut,datefin,nomstage FROM ${prefixe}centralstagedate WHERE id = '$id'";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function rechercheStageCentralSouhait($periode1,$periode2,$idsouhait) {
	global $cnx;
	global $prefixe;
	$periode1=dateFormBase($periode1);
	$periode2=dateFormBase($periode2);
	$sql="SELECT c.id,c.datedemande,c.identreprise,c.sexe,c.service,c.observation,c.nbdemande,s.nom,s.adresse,s.ville,s.code_p,s.contact,s.tel,s.fax,s.email,s.info_plus,null,null,c.salaire,c.logement,s.pays_ent,s.contact_fonction,s.siteweb,s.grphotelier,s.nbetoile,s.nbchambre,s.qualite FROM ${prefixe}centralstagesouhait c, ${prefixe}stage_entreprise s , ${prefixe}centralstagedate d WHERE d.datedebut = '$periode1' AND d.datefin = '$periode2' AND d.id ='$idsouhait' AND c.identreprise = s.id_serial AND d.id=c.idperiode ORDER BY s.pays_ent,s.ville,s.code_p,s.nom";
	$data=ChargeMat(execSql($sql));
	return $data;
}


function rechercheStageCentralSouhait2($idsouhait) {
	global $cnx;
	global $prefixe;
	$periode1=dateFormBase($periode1);
	$periode2=dateFormBase($periode2);
	$sql="SELECT c.id,c.datedemande,c.identreprise,c.sexe,c.service,c.observation,c.nbdemande,s.nom,s.adresse,s.ville,s.code_p,s.contact,s.tel,s.fax,s.email,s.info_plus,null,null,c.salaire,c.logement,s.qualite FROM ${prefixe}centralstagesouhait c, ${prefixe}stage_entreprise s , ${prefixe}centralstagedate d WHERE d.id ='$idsouhait' AND c.identreprise = s.id_serial AND d.id=c.idperiode ORDER BY s.nom";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function rechercheDateStageCentralSouhait2($idsouhait) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nomstage,datedebut,datefin,id FROM  ${prefixe}centralstagedate WHERE id='$idsouhait' ";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function enrcentralSouhait($id,$attribution,$nb,$productid,$modifviacentral) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}centralstageattribution WHERE id='$nb' AND idcentralstage='$id' AND productid='$productid'";
	execSql($sql);
	if (trim($attribution) != "") {
		$sql="SELECT * FROM ${prefixe}centralstageattribution WHERE id='$nb' AND idcentralstage='$id'";
		$data=ChargeMat(execSql($sql));
		if (count($data) == 0) {
			$sql="INSERT INTO ${prefixe}centralstageattribution (id,idcentralstage,attribution,productid,vialacentral) VALUES ('$nb','$id','$attribution','$productid','$modifviacentral')";
			execSql($sql);
		}
	}
	if ($modifviacentral) {
		$sql="UPDATE ${prefixe}centralstageattribution SET vialacentral='1' , attribution='$attribution' WHERE id='$nb' AND idcentralstage='$id'";
		execSql($sql);
	}
}




function rechercheAttribution($id,$idcentralstage) {
	global $cnx;
	global $prefixe;
	$sql="SELECT attribution,productid,id,confirmer,emailenvoye,idcentralstage,vialacentral FROM ${prefixe}centralstageattribution WHERE id='$id' AND idcentralstage='$idcentralstage'";
	$data=ChargeMat(execSql($sql));
	return $data;
}

function enrDemandeAffiliation($contact,$email,$etablissement,$ville,$pays,$productidclient) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$sql="SELECT * FROM ${prefixe}centralstageaffiliation WHERE productid='$productidclient'";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) { return; }
	$sql="INSERT INTO ${prefixe}centralstageaffiliation  (datedemande,nom,email,etablissement,ville,pays,productid) VALUE ('$date','$contact','$email','$etablissement','$ville','$pays','$productidclient')";
	execSql($sql);
}

function listeDemandeAffiliation() {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$sql="SELECT datedemande,nom,email,etablissement,ville,pays,productid,autorise,password FROM ${prefixe}centralstageaffiliation";
	$data=ChargeMat(execSql($sql));
	return($data);
}


function infoDemandeAffiliation($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT datedemande,nom,email,etablissement,ville,pays,productid,autorise,password FROM ${prefixe}centralstageaffiliation WHERE productid='$id'";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function verifAutoriseAffiliation($productid) {
	global $cnx;
	global $prefixe;
	$date=dateDMY2();
	$sql="SELECT autorise FROM ${prefixe}centralstageaffiliation WHERE productid='$productid'";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function actionDemandeValidation($productid,$action,$passwd) {
	global $cnx;
	global $prefixe;
	if ($action == "ok") { $sql="UPDATE ${prefixe}centralstageaffiliation SET autorise='1', password='$passwd' WHERE productid='$productid'"; }
	if ($action == "pasok") { $sql="UPDATE ${prefixe}centralstageaffiliation SET autorise='0' WHERE productid='$productid'"; }
	if ($action == "supp") { $sql="DELETE FROM ${prefixe}centralstageaffiliation WHERE productid='$productid'"; }
	execSql($sql);	
}

function suppAffiliationCentralStage() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}centralstageaffiliation";
	execSql($sql);
}


function creationDateStageCentral($periode1,$periode2,$nomdustage) {
	global $cnx;
	global $prefixe;
	$periode1=dateFormBase($periode1);
	$periode2=dateFormBase($periode2);
	$sql="INSERT INTO ${prefixe}centralstagedate  (datedebut,datefin,nomstage) VALUE ('$periode1','$periode2','$nomdustage')";
	return(execSql($sql));
}


function listeDateStageCentral() {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,datedebut,datefin,nomstage FROM ${prefixe}centralstagedate";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function infoDateStageCentral($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,datedebut,datefin,nomstage FROM ${prefixe}centralstagedate WHERE id='$id' ";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function suppDateStageCentral($id) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}centralstagedate WHERE id='$id'";
	execSql($sql);

}


function modifDateStageCentral($periode1,$periode2,$nomdustage,$id) {
	global $cnx;
	global $prefixe;
	$periode1=dateFormBase($periode1);
	$periode2=dateFormBase($periode2);
	$sql="UPDATE ${prefixe}centralstagedate SET datedebut='$periode1' ,  datefin='$periode2' , nomstage='$nomdustage' WHERE id='$id'";
	return(execSql($sql));
}

function periodeStageCentralDate() {
	global $cnx;
	global $prefixe;
	$datedujour=dateDMY2();
	$sql="SELECT datedebut,datefin,id,nomstage FROM ${prefixe}centralstagedate WHERE datefin >= '$datedujour'";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function periodeStageCentralDateSelect($idperiode) {
	global $cnx;
	global $prefixe;
	$datedujour=dateDMY2();
	$sql="SELECT datedebut,datefin,id,nomstage FROM ${prefixe}centralstagedate WHERE id = '$idperiode'";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function verifAccesCentrale($productid,$p) {
	global $cnx;
	global $prefixe;
	include_once("./common/productid.php");
	if ($productid == PRODUCTID) { return(1); }
	$sql="SELECT * FROM ${prefixe}centralstageaffiliation WHERE productid='$productid' AND autorise='1' AND password='$p'";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) {
		return(1);
	}else{
		print "<br><br><center><img src='image/commun/warning.png' align='center' /> <font class=T2 id='color2'>ACCES INTERDIT !</font>";
		print "<br><br><font class='T2'>Merci de contacter votre centrale de stage <br> afin de valider votre établissement.</font>";
		print "</center>";
		exit;	
	}
}

function rechercheIdProfViaEmail($email) {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id FROM ${prefixe}personnel WHERE email='$email' AND type_pers='ENS' ";
	$data=ChargeMat(execSql($sql));
	if ($data[0][0] > 0) return $data[0][0];
	return(0);
}

function rechercheIdAdminViaEmail($email) {
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id FROM ${prefixe}personnel WHERE email='$email' AND type_pers='ADM' ";
	$data=ChargeMat(execSql($sql));
	if ($data[0][0] > 0) return $data[0][0];
	return(0);
}


function rechercheIdEleveViaEmail($email) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id FROM ${prefixe}eleves WHERE email_eleve='$email'";
	$data=ChargeMat(execSql($sql));
	if ($data[0][0] > 0) return $data[0][0];
	
	$sql="SELECT elev_id FROM ${prefixe}eleves WHERE emailpro_eleve='$email'";
	$data=ChargeMat(execSql($sql));
	if ($data[0][0] > 0) return $data[0][0];
	
	return(0);
}

function rechercheIdEleveViaNumEtudiant($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT elev_id FROM ${prefixe}eleves WHERE numero_eleve='$id'";
	$data=ChargeMat(execSql($sql));
	return $data[0][0];
}

function updateCodeBarre($params) {
	global $cnx;
	global $prefixe;
	$codebarre=$params[num_code];
	$numeleve=$params[num_eleve];
	$sql="SELECT elev_id FROM ${prefixe}eleves WHERE numero_eleve='$numeleve'";
	$data=ChargeMat(execSql($sql));
	if (count($data) == 1) {
		$idpers=$data[0][0];
		$sql="UPDATE ${prefixe}codebar SET id='$codebarre' WHERE id_pers='$idpers' AND membre='menueleve'";
		return(execSql($sql));
	}
}


function enrbulletinclasse($idclasse,$enrbull) {
	global $cnx;
	global $prefixe;

	if ($idclasse == 0) {
		$sql="DELETE FROM ${prefixe}bulletin_visible";
		execSql($sql);
		$sql="SELECT code_class FROM ${prefixe}classes";
		$data=ChargeMat(execSql($sql));
		for($i=0;$i<count($data);$i++) {
			$sql="INSERT INTO ${prefixe}bulletin_visible (idclasse,bulletin) VALUE ('".$data[$i][0]."','$enrbull')";
			execSql($sql);
		}

	}else{
		$sql="DELETE FROM ${prefixe}bulletin_visible WHERE idclasse='$idclasse'";
		execSql($sql);
		$sql="INSERT INTO ${prefixe}bulletin_visible (idclasse,bulletin) VALUE ('$idclasse','$enrbull')";
		execSql($sql);
	}
}

function listeBulletinClasse() {
	global $cnx;
	global $prefixe;
	$sql="SELECT idclasse,bulletin FROM ${prefixe}bulletin_visible ORDER BY idclasse";
	$data=ChargeMat(execSql($sql));
	return($data);
}
function suppBulletinClasse($idsupp) {
	global $cnx;
	global $prefixe;
	if ($idsupp > 0) {
		$sql="DELETE FROM ${prefixe}bulletin_visible WHERE idclasse='$idsupp' ";
		execSql($sql);
	}
}

function recupBulletinClasse($idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT bulletin FROM ${prefixe}bulletin_visible WHERE idclasse='$idclasse' ";
	$data=ChargeMat(execSql($sql));
	return($data);
}

function deleteListeEleveEntr($id) {
        global $cnx;
        global $prefixe;
        if ($id > 0) {
                $sql="DELETE FROM  ${prefixe}stage_eleve WHERE id='$id'  ";
                execSql($sql);
        }
}

function  verif_si_compte_suppleant($id_pers) {
        global $cnx;
        global $prefixe;
        $sql="SELECT pers_id,rpers_id FROM ${prefixe}vacataires WHERE pers_id='$id_pers' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 0) {
                return(1);
        }else{
                return(0);
        }
}


function libelleTrimestre($saisie_tri) {
	$libelle="Toute l'année";
	if ($saisie_tri == "tous" ) { $libelle="Toute l'année"; }
	if ($saisie_tri == "trimestre1" ) { $libelle="Trimestre 1 / Semestre 1"; }
	if ($saisie_tri == "trimestre2" ) { $libelle="Trimestre 2 / Semestre 2"; }
	if ($saisie_tri == "trimestre3" ) { $libelle="Trimestre 3"; } 
	return($libelle);
}


function enrCommande($idpers,$prestation,$idclasse,$nbheure,$idmatiere,$nbforfait,$totalEnNet) {
	global $cnx;
	global $prefixe;
	if (!is_numeric($nbforfait)) $nbforfait=0;
	if (!is_numeric($totalEnNet)) $totalEnNet=0;
	$sql="INSERT INTO ${prefixe}vacation_commande  (id_pers,nbheure,idmatiere,type_prestation,idclasse,nbforfait,enNet) VALUE ('$idpers','$nbheure','$idmatiere','$prestation','$idclasse','$nbforfait','$totalEnNet')";
	return(execSql($sql));
}

function recupCommandeVacation($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nbheure,idmatiere,type_prestation,idclasse,id,nbforfait,enNet FROM ${prefixe}vacation_commande WHERE id_pers='$idpers'";
	$res=execSql($sql);
        $data=chargeMat($res);
	return($data);
}

function suppCommandeVocation($id) {
	global $cnx;
	global $prefixe;
  	$sql="DELETE FROM  ${prefixe}vacation_commande WHERE id='$id'  ";
        execSql($sql);
}

function enrgCommBrevet($commentaire,$code,$idEleve,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$year=date("Y");
 	$sql="DELETE FROM  ${prefixe}brevetcom WHERE ideleve='$idEleve'  AND annee='$year' AND codematiere='$code' and annee_scolaire='$anneeScolaire' ";
        execSql($sql); 
	$sql="INSERT INTO ${prefixe}brevetcom  (ideleve,annee,codematiere,commentaire,annee_scolaire) VALUE ('$idEleve','$year','$code','$commentaire','$anneeScolaire')";
	execSql($sql);
}

function recupCommBrevet($code,$idEleve) {
	global $cnx;
	global $prefixe;
	$year=date("Y");
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT commentaire FROM ${prefixe}brevetcom WHERE ideleve='$idEleve'  AND annee='$year' AND codematiere='$code' and annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
        $data=chargeMat($res);
	return(stripslashes($data[0][0]));
}

function ajoutEntretienEnseignant($id_liste_eleve,$idprof,$duree) {
	global $cnx;
	global $prefixe;
	$date=dateYMD();
	$tab=explode(',',$id_liste_eleve);
	$reference=md5(rand(1000,9999).$date);
	foreach($tab as $key=>$ideleve) {
		if ($ideleve == 0) continue;
		$idclasse=chercheIdClasseDunEleve($ideleve);
		$sql="INSERT INTO ${prefixe}entretiendureeprof (idprof,duree,idclasse,date_saisie,reference,ideleve) VALUE ('$idprof','$duree','$idclasse','$date','$reference','$ideleve')";
		execSql($sql);
	}
}

function listingEntretienEnseignantParReference() {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,duree,idclasse,date_saisie,reference,ideleve FROM ${prefixe}entretiendureeprof  group by reference ORDER BY date_saisie";
	$res=execSql($sql);
        $data=chargeMat($res);
	return($data);
}

function listingEntretienEnseignantParReferenceViaIdprof($idprof) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,duree,idclasse,date_saisie,reference,ideleve FROM ${prefixe}entretiendureeprof WHERE idprof='$idprof' GROUP BY reference ORDER BY date_saisie";
	$res=execSql($sql);
        $data=chargeMat($res);
	return($data);
}

function listingEntretienEnseignantViaReference($reference) {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,duree,idclasse,date_saisie,ideleve FROM ${prefixe}entretiendureeprof  where reference='$reference'";
	$res=execSql($sql);
        $data=chargeMat($res);
	return($data);
}


function listingEntretienEnseignant() {
	global $cnx;
	global $prefixe;
	$sql="SELECT idprof,duree,idclasse,date_saisie,reference,ideleve FROM ${prefixe}entretiendureeprof";
	$res=execSql($sql);
        $data=chargeMat($res);
	return($data);
}

function purgeEntreprise() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}stage_entreprise";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}personnel WHERE type_pers='TUT' ";
	execSql($sql);
}

function purgeEntretienEnseignentPourEtudiant() {
	global $cnx;
	global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}entretiendureeprof";
	return(execSql($sql));
}

function suppEntretienEnseignentPourEtudiant($idprof) {
       	global $cnx;
        global $prefixe;
        $sql="DELETE FROM  ${prefixe}entretiendureeprof WHERE idprof='$idprof'  ";
        execSql($sql);
}


function nbHeureProgrammeParMatiere($idmatiere) {
       	global $cnx;
	global $prefixe; 
	$sql="SELECT nbheure FROM ${prefixe}vacation_commande WHERE idmatiere='$idmatiere'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$nb=0;
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][0] > 0) {
			$nb+=$data[$i][0];
		}
	}
	return($nb);
}

function nbHeureProgrammeParClasse($idClasse) {
       	global $cnx;
	global $prefixe; 
	$sql="SELECT nbheure FROM ${prefixe}vacation_commande WHERE idclasse='$idClasse'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$nb=0;
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][0] > 0) {
			$nb+=$data[$i][0];
		}
	}
	return($nb);
}

function nbHeureProgrammeParIdPers($idPers) {
       	global $cnx;
	global $prefixe; 
	$sql="SELECT nbheure FROM ${prefixe}vacation_commande WHERE id_pers='$idPers'";
	$res=execSql($sql);
        $data=chargeMat($res);
	$nb=0;
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][0] > 0) {
			$nb+=$data[$i][0];
		}
	}
	return($nb);
}

function updatePwdMoodle($idpers,$passwd) {
       	global $cnx;
	global $prefixe; 
	$passwd=md5($passwd);
	$sql="UPDATE ${prefixe}eleves SET mdp_moodle='$passwd' WHERE elev_id='$idpers'";
	execSql($sql);
}



function enrRegime($libelle,$lundimidi,$lundisoir,$mardimidi,$mardisoir,$mercredimidi,$mercredisoir,$jeudimidi,$jeudisoir,$vendredimidi,$vendredisoir,$samedimidi,$samedisoir,$dimanchemidi,$dimanchesoir) {
       	global $cnx;
	global $prefixe;  
	$sql="SELECT * FROM ${prefixe}regime WHERE libelle='$libelle'";
        $res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0) {
		return("2");
	}
	$sql="INSERT INTO ${prefixe}regime  (`libelle` , `lundi_m` , `lundi_s` , `mardi_m` , `mardi_s` , `mercredi_m` , `mercredi_s` , `jeudi_m` , `jeudi_s` , `vendredi_m` , `vendredi_s` , `samedi_m` , `samedi_s` , `dimanche_m` , `dimanche_s` ) VALUES ('$libelle','$lundimidi','$lundisoir','$mardimidi','$mardisoir','$mercredimidi','$mercredisoir','$jeudimidi','$jeudisoir','$vendredimidi','$vendredisoir','$samedimidi','$samedisoir','$dimanchemidi','$dimanchesoir')";
	execSql($sql);
	return(1);
}


function updateRegime($libelle,$lundimidi,$lundisoir,$mardimidi,$mardisoir,$mercredimidi,$mercredisoir,$jeudimidi,$jeudisoir,$vendredimidi,$vendredisoir,$samedimidi,$samedisoir,$dimanchemidi,$dimanchesoir,$id) {
       	global $cnx;
	global $prefixe;  
	$sql="UPDATE ${prefixe}regime SET 
		`libelle`='$libelle' , 
		`lundi_m`='$lundimidi' , 
		`lundi_s`='$lundisoir' , 
		`mardi_m`='$mardimidi' , 
		`mardi_s`='$mardisoir' , 
		`mercredi_m`='$mercredimidi' , 
		`mercredi_s`='$mercredisoir' , 
		`jeudi_m`='$jeudimidi' , 
		`jeudi_s`='$jeudisoir' , 
		`vendredi_m`='$vendredimidi' , 
		`vendredi_s`='$vendredisoir' , 
		`samedi_m`='$samedimidi' , 
		`samedi_s`='$samedisoir' , 
		`dimanche_m`='$dimanchemidi' , 
		`dimanche_s`='$dimanchesoir' 
		WHERE id='$id'";
	execSql($sql);
}


function selectRegime() {
       	global $cnx;
	global $prefixe;  
	$data=listingRegime();
	for($i=0;$i<count($data);$i++) {
		print "<option value=\"".txt_vers_html($data[$i][0])."\" id='select1'>".$data[$i][0]."</option>\n";
	}
}

function listingRegime() {
       	global $cnx;
	global $prefixe;  
	$sql="SELECT `libelle` , `lundi_m` , `lundi_s` , `mardi_m` , `mardi_s` , `mercredi_m` , `mercredi_s` , `jeudi_m` , `jeudi_s` , `vendredi_m` , `vendredi_s` , `samedi_m` , `samedi_s` , `dimanche_m` , `dimanche_s`,id FROM ${prefixe}regime ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}

function InfoRegime($id) {
       	global $cnx;
	global $prefixe;  
	$sql="SELECT `libelle` , `lundi_m` , `lundi_s` , `mardi_m` , `mardi_s` , `mercredi_m` , `mercredi_s` , `jeudi_m` , `jeudi_s` , `vendredi_m` , `vendredi_s` , `samedi_m` , `samedi_s` , `dimanche_m` , `dimanche_s` FROM ${prefixe}regime WHERE id='$id' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}

function select_regime() {
        global $cnx;
        global $prefixe;
        $sql="SELECT `libelle` , id FROM ${prefixe}regime ORDER BY libelle ";
        $res=execSql($sql);
        $data=chargeMat($res);
        print "<option value='externe' id='select1' >Externe</option>";
        print "<option value='demi-pension' id='select1' >Demi-pension</option>";
        print "<option value='interne' id='select1' >Interne</option>";
        for($i=0;$i<count($data);$i++) {
                print "<option value='".$data[$i][1]."' id='select1' >".$data[$i][0]."</option>";
        }
} 



function select_regime2() {
       	global $cnx;
	global $prefixe;  
	$sql="SELECT `libelle` , id FROM ${prefixe}regime ORDER BY libelle ";
        $res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print "<option value='".$data[$i][0]."' id='select1' >".$data[$i][0]."</option>";
	}
}

function miseAjourRegime($ideleve,$regime) {
       	global $cnx;
	global $prefixe;  
	$sql="UPDATE ${prefixe}eleves SET regime='$regime' WHERE elev_id='$ideleve'";
	execSql($sql);
}


function enrIdClasseSMS($idclasse) {
       	global $cnx;
	global $prefixe;
	$sql="SELECT  text  FROM ${prefixe}parametrage WHERE libelle='smsconfignonclasse' ";
        $res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		$idliste=preg_replace('/\{/','',$data[0][0]);
		$idliste=preg_replace('/\}/','',$idliste);
		$idliste.=",".$idclasse;
	}else{
		$idliste=$idclasse;
	}
	$data=explode(',',$idliste);
	$data=array_unique($data);
	$idliste=implode(',',$data);
	enr_bull_bonifacio("smsconfignonclasse",$idliste);
}


function suppIdClasseSMS() {
       	global $cnx;
	global $prefixe;  
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='smsconfignonclasse'";
	execSql($sql);
}

function rechercheNomRegime($id) {
     	global $cnx;
	global $prefixe;  
	$sql="SELECT `libelle` FROM ${prefixe}regime WHERE id='$id' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);

}

function recupInfoIdEdt($id) {
        global $cnx;
        global $prefixe;
	$id=preg_replace('/EDT/','',$id);
        $sql="SELECT date,heure,duree,idclasse,idprof,idmatiere   FROM ${prefixe}edt_seances WHERE  id='$id'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}


function suppRegime($id) {
        global $cnx;
        global $prefixe;
	if ($id > 0) {
		$sql="DELETE FROM ${prefixe}regime WHERE id='$id'";
		execSql($sql);
	}	
}

function supprimerCompetence($idcarnet,$idcompetence) {
        global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}carnet_descriptif WHERE  idcompetence='$idcompetence' AND idcarnet='$idcarnet'";
	execSql($sql);
	$sql="DELETE FROM ${prefixe}carnet_competence WHERE idcarnet='$idcarnet' AND id='$idcompetence'";
	execSql($sql);
}

function confPoliceViaHauteur($commentaireeleve,$hauteur) {
        if ($hauteur <= "8") { return(confPolice($commentaireeleve)); }
	if ($hauteur >= "10") {
                $commentaireeleve=trim($commentaireeleve);
                $len=strlen($commentaireeleve);
                if (($len > 0) && ($len < 100)) {
                        $tab[0]=8; // taille de la police
                        $tab[1]=3; // taille du cadre d'ecriture
                }elseif (($len >= 100) && ($len < 230)) {
                        $tab[0]=8; // taille de la police
                        $tab[1]=2.5; // taille du cadre d'ecriture
                }elseif (($len >= 230) && ($len < 300)) {
                        $tab[0]=8; // taille de la police
                        $tab[1]=2.3; // taille du cadre d'ecriture
                }elseif (($len >= 300) && ($len < 400)) {
                        $tab[0]=8; // taille de la police
                        $tab[1]=2.3; // taille du cadre d'ecriture
                }elseif (($len >= 400) && ($len < 500)) {
                        $tab[0]=8; // taille de la police
                        $tab[1]=2.3; // taille du cadre d'ecriture
                }else{
                        $tab[0]=8;
                        $tab[1]=2.5;
                }
                return $tab;
        }
        if ($hauteur > "8") {
                $commentaireeleve=trim($commentaireeleve);
                $len=strlen($commentaireeleve);
                if (($len > 0) && ($len < 100)) {
                        $tab[0]=10; // taille de la police
                        $tab[1]=3; // taille du cadre d'ecriture
                }elseif (($len >= 100) && ($len < 230)) {
                        $tab[0]=8; // taille de la police
                        $tab[1]=2.5; // taille du cadre d'ecriture
                }elseif (($len >= 230) && ($len < 300)) {
                        $tab[0]=7; // taille de la police
                        $tab[1]=2.3; // taille du cadre d'ecriture
                }elseif (($len >= 300) && ($len < 400)) {
                        $tab[0]=6; // taille de la police
                        $tab[1]=2.3; // taille du cadre d'ecriture
                }elseif (($len >= 400) && ($len < 500)) {
                        $tab[0]=5; // taille de la police
                        $tab[1]=2.3; // taille du cadre d'ecriture
                }else{
                        $tab[0]=9;
                        $tab[1]=2.5;
                }
                return $tab;
        }
}

function recherche_contact_entreprise($id_entreprise) {
        global $cnx;
        global $prefixe;
        $sql="SELECT contact FROM ${prefixe}stage_entreprise WHERE id_serial='$id_entreprise'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}

function recupAdrSociete($siege,$type_societe,$idclasse) {
        global $cnx;
        global $prefixe;
        if ($type_societe == 2) {
                $sql="SELECT e.nom,e.adresse,e.ville,e.code_p,e.pays_ent,null,e.id_serial  FROM  ${prefixe}stage_entreprise e ORDER BY e.nom";
        }else{
                if ($idclasse != 0) {
                        if ($siege == "siege") {
                                $sql="SELECT e.nom,e.adresse,e.ville,e.code_p,e.pays_ent,se.tuteur_stage,e.id_serial  FROM  ${prefixe}stage_eleve se, ${prefixe}stage_entreprise e, ${prefixe}eleves el  WHERE se.id_entreprise=e.id_serial AND el.classe='$idclasse' AND el.elev_id=se.id_eleve  ORDER BY e.nom";
                        }else{
                                $sql="SELECT e.nom,se.lieu_stage,se.ville_stage,se.code_p,e.pays_ent,se.tuteur_stage,e.id_serial FROM  ${prefixe}stage_eleve se, ${prefixe}stage_entreprise e, ${prefixe}eleves el WHERE se.id_entreprise=e.id_serial AND el.classe='$idclasse' AND el.elev_id=se.id_eleve ORDER BY e.nom";
                        }
                }else{
                        if ($siege == "siege") {
                                $sql="SELECT e.nom,e.adresse,e.ville,e.code_p,e.pays_ent,se.tuteur_stage,e.id_serial  FROM  ${prefixe}stage_eleve se, ${prefixe}stage_entreprise e WHERE se.id_entreprise=e.id_serial ORDER BY e.nom";
                        }else{
                                $sql="SELECT e.nom,se.lieu_stage,se.ville_stage,se.code_p,e.pays_ent,se.tuteur_stage,e.id_serial FROM  ${prefixe}stage_eleve se, ${prefixe}stage_entreprise e WHERE se.id_entreprise=e.id_serial ORDER BY e.nom";
                        }

                }
        }
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}


function nombre_absNonJustifieIsmapp($id_eleve,$dateDebut,$dateFin) {
        global $cnx;
        global $prefixe;
        $sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' AND justifier != '1'";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        for($i=0;$i<count($data_2);$i++) {
                if ( ($data_2[$i][4] > 0) || ($data_2[$i][4] == '-1'))  {
                        $nb++;
                }
        }
        return $nb;
}

function topabs($nblimit) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
	$data=array();
	if ($dateDebut != "") {
		$sql="SELECT elev_id FROM ${prefixe}absences WHERE date_ab >= '$dateDebut' AND date_ab <= '$dateFin' GROUP BY elev_id ORDER BY count(*) DESC LIMIT $nblimit";
		$res=execSql($sql);
	        $data=chargeMat($res);
	}	
        return($data);
}

function nbabstotal($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT count(*) FROM ${prefixe}absences where elev_id='$ideleve'";
	$res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}

function nbabstotalClasse($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
        $data=array();
        if ($dateDebut != "") {
		$sql="SELECT count(*) FROM ${prefixe}absences a, ${prefixe}eleves e where e.elev_id=a.elev_id AND  e.classe='$idclasse' AND a.date_ab >= '$dateDebut' AND a.date_ab <= '$dateFin' ";
		$res=execSql($sql);
	        $data=chargeMat($res);
	}
        return($data[0][0]);
}



function topabsclasse() {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
        $data=array();
	if ($dateDebut != "") {
	 	$sql="SELECT e.classe FROM ${prefixe}absences a, ${prefixe}eleves e WHERE e.elev_id=a.elev_id  AND a.date_ab >= '$dateDebut' AND a.date_ab <= '$dateFin'  GROUP BY e.classe ORDER BY count(*) DESC ";
		$res=execSql($sql);
        	$data=chargeMat($res);
	}
        return($data);
}


function nbretardtotalClasse($idclasse) {
	global $cnx;
	global $prefixe; 
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
        $data[0][0]="";
        if ($dateDebut != "") {
		$sql="SELECT count(*)  FROM ${prefixe}retards a, ${prefixe}eleves e where e.elev_id=a.elev_id AND  e.classe='$idclasse' AND a.date_ret >= '$dateDebut' AND a.date_ret <= '$dateFin' ";
		$res=execSql($sql);
        	$data=chargeMat($res);
	}
	return($data[0][0]);
}

function nbabstotalMatiere($idmatiere) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
        $data[0][0]="";
	if ($dateDebut != "") {
		if ($idmatiere == 0) {
		 	$sql="SELECT count(*) FROM ${prefixe}absences WHERE (id_matiere='0' OR id_matiere='-1') AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' ";
		}else{
		 	$sql="SELECT count(*) FROM ${prefixe}absences WHERE id_matiere='$idmatiere' AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' ";
		}
		$res=execSql($sql);
	        $data=chargeMat($res);
	}
        return($data[0][0]);

}

function nbretardtotalMatiere($idmatiere) {
	global $cnx;
	global $prefixe; 
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
        $data[0][0]="";
        if ($dateDebut != "") {
		if ($idmatiere == 0) {
			$sql="SELECT count(*) FROM ${prefixe}retards WHERE (idmatiere='0' OR idmatiere='-1') AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ";
		}else{
			$sql="SELECT count(*) FROM ${prefixe}retards WHERE idmatiere='$idmatiere' AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ";
		}
		$res=execSql($sql);
	        $data=chargeMat($res);
        }
	return($data[0][0]);	
}

function nbabstotalEnseignant($idprof) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
        $data[0][0]="";
        if ($dateDebut != "") {
		if ($idprof == 0) {
		 	$sql="SELECT count(*) FROM ${prefixe}absences WHERE (idprof ='0' OR  idprof='-1') AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' ";
		}else{
		 	$sql="SELECT count(*) FROM ${prefixe}absences WHERE idprof='$idprof'  AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' ";
		}
		$res=execSql($sql);
        	$data=chargeMat($res);
	}
        return($data[0][0]);

}

function nbretardtotalEnseignant($idprof) {
	global $cnx;
	global $prefixe; 
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
        $data[0][0]="";
	if ($dateDebut != "") {
		if ($idprof == 0) {
			$sql="SELECT count(*) FROM ${prefixe}retards WHERE (idprof='0' OR idprof='-1') AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ";
		}else{
			$sql="SELECT count(*) FROM ${prefixe}retards WHERE idprof='$idprof' AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' ";
		}
		$res=execSql($sql);
	        $data=chargeMat($res);
	}		
        return($data[0][0]);	
}

function nbabstotalTaux($creneaux) {
	global $cnx;
	global $prefixe;
	$creneaux=addslashes($creneaux);
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
        $data[0][0]="";
        if ($dateDebut != "") {
		$sql="SELECT count(*) FROM ${prefixe}absences WHERE creneaux='$creneaux' AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin'  ";
		$res=execSql($sql);
	        $data=chargeMat($res);
	}
        return($data[0][0]);

}

function nbretardtotalTaux($creneaux) {
	global $cnx;
	global $prefixe; 
	$creneaux=addslashes($creneaux);
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $dateDebut=dateFormBase(recupJourMoisScolaire("deb",$anneeScolaire));
        $dateFin=dateFormBase(recupJourMoisScolaire("fin",$anneeScolaire));
        $data[0][0]="";
        if ($dateDebut != "") {
		$sql="SELECT count(*)  FROM ${prefixe}retards WHERE creneaux='$creneaux' AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin'";
		$res=execSql($sql);
	        $data=chargeMat($res);
	}
        return($data[0][0]);	
}

function nombre_absNonJustifie2($id_eleve,$dateDebut,$dateFin) {
        global $cnx;
        global $prefixe;
        $sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND justifier != '1' ";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        return $data_2;
}


function nombre_absHeureTotal($id_eleve,$dateDebut,$dateFin) {
	global $cnx;
        global $prefixe;
        $sql="SELECT duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND duree_ab = '-1' ";
        $res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$duree=$data[$i][0];	// 1.00 ou 1.30
		list($heure,$minute)=preg_split('/\./',$duree);
		$temps+=($minute*60)+($heure*3600);
	}
	
	
	$hours = floor($temps / 3600); 

	$min = floor(($temps - ($hours * 3600)) / 60); 
  	if ($min < 10) $min = "0".$min; 
	
	return("$hours:$min");
}

function nombre_absHeureTotalNonJustifie($id_eleve,$dateDebut,$dateFin) {
	global $cnx;
        global $prefixe;
        $sql="SELECT duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND duree_ab = '-1' AND justifier != '1' ";
        $res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$duree=$data[$i][0];	// 1.00 ou 1.30
		list($heure,$minute)=preg_split('/\./',$duree);
		$temps+=($minute*60)+($heure*3600);
	}
	
	
	$hours = floor($temps / 3600); 

	$min = floor(($temps - ($hours * 3600)) / 60); 
  	if ($min < 10) $min = "0".$min; 
	
	return("$hours:$min");
}

function nombre_absHeureTotalJustifie($id_eleve,$dateDebut,$dateFin) {
	global $cnx;
        global $prefixe;
        $sql="SELECT duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND duree_ab = '-1' AND justifier = '1' ";
        $res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$duree=$data[$i][0];	// 1.00 ou 1.30
		list($heure,$minute)=preg_split('/\./',$duree);
		$temps+=($minute*60)+($heure*3600);
	}
	
	
	$hours = floor($temps / 3600); 

	$min = floor(($temps - ($hours * 3600)) / 60); 
  	if ($min < 10) $min = "0".$min; 
	
	return("$hours:$min");
}




function nombre_absJustifie2($id_eleve,$dateDebut,$dateFin) {
        global $cnx;
        global $prefixe;
        $sql="SELECT elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >='$dateDebut' AND date_ab <= '$dateFin' AND justifier = '1' ";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        return $data_2;
}


function recupEleveViaIdEleve($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone,numero_eleve FROM ${prefixe}eleves WHERE elev_id='$idEleve' ORDER BY nom,prenom";
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	return $liste;
}

function verifMatiereLangue($idEleve,$idMatiere,$langue,$idClasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_mat,trim(libelle),trim(sous_matiere) FROM ${prefixe}matieres WHERE code_mat='$idMatiere'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$nomMatiere=strtolower(trim($data[0][1]));
	if ($nomMatiere != "") {
		if ($langue == 'LV1') { $sqlsuite="lower(lv1) = '$nomMatiere'"; } 
		if ($langue == 'LV2') { $sqlsuite="lower(lv2) = '$nomMatiere'"; } 
		if ($langue == 'OPT') { $sqlsuite="lower(`option`) = '$nomMatiere'"; } 
		if ($sqlsuite == "") { return(false); }
		$sql="SELECT nom FROM ${prefixe}eleves WHERE elev_id='$idEleve' AND $sqlsuite and  classe='$idClasse'";
		$curs=execSql($sql);
		$liste=chargeMat($curs);
		if (count($liste)) return(true);
		return(false);
	}else{
		return(false);
	}
}


function recupCodeMatiere($idMatiere) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_matiere FROM ${prefixe}matieres WHERE code_mat='$idMatiere'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}

function recupMatiereEn($idmatiere) {
        global $cnx;
        global $prefixe;
        $sql="SELECT libelle_en FROM ${prefixe}matieres WHERE code_mat='$idmatiere'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}

function filtreCopierColler($message) {
        $message=preg_replace('/\<!--\[if !supportLists\]--\>/','',$message);
        $message=preg_replace('/\<!--\[endif\]--\>/','',$message);
	$message=preg_replace('/\<script/i','',$message);
	$message=preg_replace('/\<\?/i','',$message);
        return($message);
}

function chercheIdMatos($matos) {
        global $cnx;
        global $prefixe;
        $sql="SELECT id FROM ${prefixe}resa_matos WHERE libelle='$matos' ";
        $res=execSql($sql);
        $data=ChargeMat($res);
        return $data[0][0];
}


function listMatiereBrevet2($libelle,$idclasse) {
        global $cnx;
        global $prefixe;
        $sql="SELECT libelle,idmatiere,coefbrevet FROM ${prefixe}brevetconfig WHERE libelle='$libelle'  AND idclasse='$idclasse' ";
        $res=execSql($sql);
        $data=ChargeMat($res);
        print "<ul><table border='0'>";
        for($i=0;$i<count($data);$i++) {
                $coef=$data[$i][2];
                if ($coef == "") $coef="1";
                print "<tr><td align='right'><font class='T2'>";
                print chercheMatiereNom($data[$i][1]);
                print " : <input type='hidden' value='".$data[$i][1]."' name='idmatiere_$i' /> ";
                print "</td><td><input type='text' value='$coef' size='2' name='coef_$i' /></td></tr>";
        }
        print "</table></ul>";
        print "<input type='hidden' name='nb' value='".count($data)."' />";
}

function miseAjourCoefBrevet($idclasse,$libelle,$idmatiere,$coef) {
        global $cnx;
        global $prefixe;
	$coef=preg_replace('/,/','.',$coef);
        $sql="UPDATE ${prefixe}brevetconfig SET coefbrevet='$coef' WHERE idmatiere='$idmatiere' AND idclasse='$idclasse' AND libelle='$libelle'";
        execSql($sql);
}

function recupCoefBrevet($idclasse,$libelle,$idmatiere) {
        global $cnx;
        global $prefixe;
        $sql="SELECT coefbrevet FROM ${prefixe}brevetconfig WHERE libelle='$libelle'  AND idclasse='$idclasse' AND idmatiere='$idmatiere'";
        $res=execSql($sql);
        $data=ChargeMat($res);
        return($data[0][0]);
}


function adsens() {
	$text="google.com, pub-5077438242993464, DIRECT, f08c47fec0942fa0";
        @unlink("./ads.txt");
        @unlink("../ads.txt");
        $fp=fopen("../ads.txt","w");
        fwrite($fp,$text);
        fclose($fp);
}


function robottxt(){
	include_once('../common/lib_ecole.php');
	$repecole=REPECOLE;
	$text = 'User-agent: Mediapartners-Google'."\n";
	$text.= 'Allow: /'.$repecole."\n";
	$text.= 'Disallow: /'.$repecole.'/agenda/'."\n";
	$text.= 'Disallow: /'.$repecole.'/common/'."\n";
	$text.= 'Disallow: /'.$repecole.'/data/'."\n";
	$text.= 'Disallow: /'.$repecole.'/forum/'."\n";
	$text.= 'Disallow: /'.$repecole.'/gedt/'."\n";
	$text.= 'Disallow: /'.$repecole.'/image/'."\n";
	$text.= 'Disallow: /'.$repecole.'/jpgraph/'."\n";
	$text.= 'Disallow: /'.$repecole.'/librairie_css/'."\n";
	$text.= 'Disallow: /'.$repecole.'/librairie_js/'."\n";
	$text.= 'Disallow: /'.$repecole.'/librairie_pdf/'."\n";
	$text.= 'Disallow: /'.$repecole.'/librairie_php/'."\n";
	$text.= 'Disallow: /'.$repecole.'/livreor/'."\n";
	$text.= 'Disallow: /'.$repecole.'/messagerie/'."\n";
	$text.= 'Disallow: /'.$repecole.'/wap/'."\n";
	$text.= 'Disallow: /'.$repecole.'/meteo/'."\n";
	$text.= 'Disallow: /'.$repecole.'/dokeos/'."\n";
	$text.= 'Disallow: /'.$repecole.'/moodle/'."\n";
	$text.= 'Disallow: /'.$repecole.'/installation/'."\n";
	$text.= 'Disallow: /'.$repecole.'/cache/'."\n";
	$text.= 'Disallow: /'.$repecole.'/audio/'."\n";
	$text.= 'Disallow: /'.$repecole.'/module_chambres/'."\n";
	$text.= 'Disallow: /'.$repecole.'/module_financier/'."\n";
	$text.= 'Disallow: /'.$repecole.'/include/'."\n";
	$text.= 'User-agent: *'."\n";
	$text.= 'Disallow: /'.$repecole.'/'."\n";
	$text.= 'User-agent: Googlebot-Image'."\n";
	$text.= 'Disallow: /'.$repecole.'/'."\n";
	@unlink("./robots.txt");
	@unlink("../robots.txt");
	$fp=fopen("../robots.txt","w");
	fwrite($fp,$text);
	fclose($fp);
}


function setCookies($key,$value) {
        setcookie($key,$value,time()+3600*24*2);
}

function getCookies($key) {
	if (isset($_COOKIE[$key])) {
		return($_COOKIE[$key]);
	}else{
		return;
	}
}


function recupDateDebutAnneeByClasse($idclasse) {
        global $cnx;
        global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT date_debut,date_fin,trim_choix FROM  ${prefixe}date_trimestrielle WHERE trim_choix='trimestre1' AND (idclasse='$idclasse' OR idclasse='0' ) AND annee_scolaire='$anneeScolaire' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        $dateDebut=$data[0][0];
        return $dateDebut;
}


function recupDateFinAnneeByClasse($idclasse) {
        global $cnx;
        global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT date_debut,date_fin,trim_choix FROM  ${prefixe}date_trimestrielle WHERE trim_choix='trimestre3' AND (idclasse='$idclasse'OR idclasse='0' ) AND annee_scolaire='$anneeScolaire' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 0) {
                $dateFin=$data[0][1];
                return $dateFin;
        }else{
                $sql="SELECT date_debut,date_fin,trim_choix FROM  ${prefixe}date_trimestrielle WHERE trim_choix='trimestre2' AND (idclasse='$idclasse'OR idclasse='0' ) AND annee_scolaire='$anneeScolaire' ";
                $res=execSql($sql);
                $data=chargeMat($res);
                $dateFin=$data[0][1];
                return $dateFin;
        }
}

function creation_eleve_via_edt($nom,$prenom,$naissance,$idnationnal,$boursier,$email) {
        $params[ne]=addslashes($nom);
        $params[pe]=addslashes($prenom);
        $params[ce]=-3;
        $params[lv1]="";
        $params[lv2]="";
        $params[option]="";
        $params[regime]="";
        $params[naiss]="$naissance";
        $params[nat]="";
        $params[mdp]="";
        $params[mdpeleve]="";
        $params[nt]="";
        $params[pt]="";
        $params[adr1]="";
        $params[cpadr1]="";
        $params[commadr1]="";
        $params[adr2]="";
        $params[cpadr2]="";
        $params[commadr2]="";
        $params[tel]="";
        $params[profp]="";
        $params[telprofp]="";
        $params[profm]="";
        $params[telprofm]="";
        $params[nomet]="";
        $params[numet]="";
        $params[cpet]="";
        $params[commet]="";
        $params[numero_eleve]="$idnationnal";
        $params[email]="";
        $params[classe_ant]="";
        $params[annee_ant]="";
        $params[numero_gep]="";
        $params[civ_1]="";
        $params[civ_2]="";
        $params[tel_eleve]="";
        $params[mail_eleve]=strtolower($email);
        $params[nom_resp2]="";
        $params[prenom_resp2]="";
        $params[lieunais]="";
        $params[tel_port_1]="";
        $params[tel_port_2]="";
        $params[email_2]="";
        $params[codecompta]="";
        $params[sexe]="";
        $params[mdp2]="";
        $params[information]="";
        $params[adr_eleve]="";
        $params[commune_eleve]="";
        $params[ccp_eleve]="";
        $params[tel_fixe_eleve]="";
        $params[pays_eleve]="";
        $params[boursier]="$bousier";
        $params[boursier_montant]="";
        $params[indemnite_stage]="";
        $params[nbmoisindemnite_stage]="";
        $params[mailpro_eleve]="";
        create_eleve($params,0);
}

function modifClasseEleveEdt($idEleve,$idclasse){
        global $cnx;
        global $prefixe;
        $sql="UPDATE ${prefixe}eleves SET classe='$idclasse' WHERE elev_id='$idEleve'";
        execSql($sql);
}

function deleteEleveViaEdt() {
        global $cnx;
        global $prefixe;
        $sql="DELETE FROM ${prefixe}eleves WHERE elev_id='-3'";
        execSql($sql);
}

function recupMatiereUECoefCertifPositif($code_ue,$idclasse) {
        global $cnx;
        global $prefixe;
        $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull , a.langue , a.specif_etat, NULL, NULL, a.num_semestre_info
                        FROM  ${prefixe}ue_detail u, ${prefixe}matieres m , ${prefixe}affectations a
                        WHERE code_ue='$code_ue'
                        AND m.code_mat = u.code_matiere
                        AND a.id_ue_detail = u.code_ue_detail
                        AND a.visubull = '1'
                        AND a.annee_scolaire='$anneeScolaire'
                        AND coef_certif > '0'
                        ORDER BY a.ordre_affichage";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}


function recupUE($idClasse,$sem) {
        global $cnx;
        global $prefixe;
        $anneeScolaire=$_COOKIE["anneeScolaire"];
        if ($sem == 1 || $sem == 2 || $sem == 3) {
                $sql="SELECT code_ue,nom_ue,coef_ue,ects_ue,nom_ue_en,semestre FROM ${prefixe}ue WHERE code_classe='$idClasse' AND (semestre='$sem' OR  semestre='0') AND annee_scolaire='$anneeScolaire' ORDER BY num_ue";
                $res=execSql($sql);
                $data=chargeMat($res);
                return($data);
        }else{
                $sql="SELECT code_ue,nom_ue,coef_ue,ects_ue,nom_ue_en,semestre FROM ${prefixe}ue WHERE code_classe='$idClasse' AND annee_scolaire='$anneeScolaire'  ORDER BY num_ue";
                $res=execSql($sql);
                $data=chargeMat($res);
                return($data);
        }
}


function verifUEenCours($idClasse,$semestre,$ordre,$libelle_fr,$libelle_en,$anneeScolaire='') {
  	global $cnx;
        global $prefixe;
	if (trim($anneeScolaire) == '') $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT code_ue FROM  ${prefixe}ue WHERE code_classe='$idClasse' AND semestre='$semestre' AND nom_ue='$libelle_fr' AND nom_ue_en='$libelle_en' AND annee_scolaire='$anneeScolaire' ";
        $res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}


function import_UE_IPAC($idClasse,$semestre,$ordre,$libelle_fr,$libelle_en,$anneeScolaire) {
  	global $cnx;
        global $prefixe;
	if (trim($anneeScolaire) != '') { 
		$sql="INSERT INTO ${prefixe}ue (code_classe,semestre,nom_ue,nom_ue_en,coef_ue,num_ue,annee_scolaire) VALUE ('$idClasse','$semestre','$libelle_fr','$libelle_en','1','$ordre','$anneeScolaire')";
		execSql($sql);
	}
}

function import_Detail_UE_IPAC($idMatiere,$idUE) {
  	global $cnx;
        global $prefixe;
	$sql="INSERT INTO ${prefixe}ue_detail (code_ue,code_matiere) VALUE ('$idUE','$idMatiere')";
	execSql($sql);
}
  

function verifSiAffectationEnCoursSansUE($idMatiere,$idClasse,$coef,$semestre,$ects,$id_ue_detail,$anneeScolaire) {
  	global $cnx;
        global $prefixe;
	$sql="SELECT * FROM  ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND trim='$semestre' AND annee_scolaire='$anneeScolaire' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return(count($data));
}


function verifSiAffectationEnCoursAvecUE($idMatiere,$idClasse,$coef,$semestre,$ects,$id_ue_detail,$anneeScolaire) {
        global $cnx;
        global $prefixe;
        $sql="SELECT * FROM  ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND coef='$coef' AND trim='$semestre' AND ects='$ects' AND id_ue_detail='$id_ue_detail' AND annee_scolaire='$anneeScolaire' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return(count($data));
}

function import_affectation_IPAC($idMatiere,$idClasse,$coef,$numero_semestre,$ects,$idDetailUE,$ordre,$specif,$anneeScolaire="",$info_semestre="",$coef_certif="",$note_planche="") {
        global $cnx;
        global $prefixe;
        if ($specif == "oui") {
                $specif="etudedecasipac";
        }
        if (trim($anneeScolaire) == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="INSERT INTO ${prefixe}affectations (code_matiere,code_classe,coef,trim,ects,id_ue_detail,specif_etat,ordre_affichage,langue,nb_heure,code_groupe,annee_scolaire,num_semestre_info,coef_certif,note_planche) VALUE ('$idMatiere','$idClasse','$coef','$numero_semestre','$ects','$idDetailUE','$specif','$ordre','','','0','$anneeScolaire','$info_semestre','$coef_certif','$note_planche')";
        execSql($sql);
}

function import_affectation_IPAC_UPDATE($idMatiere,$idClasse,$coef,$numero_semestre,$ects,$idDetailUE,$ordre,$specif,$anneeScolaire="",$info_semestre="",$coef_certif="",$note_planche="") {
        global $cnx;
        global $prefixe;
        if ($specif == "oui") {
                $specif="etudedecasipac";
        }
        if (trim($anneeScolaire) == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="UPDATE  ${prefixe}affectations SET id_ue_detail='$idDetailUE' , specif_etat='$specif' ,   ects='$ects' , coef='$coef' , num_semestre_info='$info_semestre' , coef_certif='$coef_certif' , note_planche='$note_planche'
            WHERE annee_scolaire='$anneeScolaire' AND code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordre' AND trim='$numero_semestre' ";
        execSql($sql);

}

function verifDetailUEenCours($idMatiere,$idUE) {
	global $cnx;
        global $prefixe;
        $sql="SELECT code_ue_detail FROM  ${prefixe}ue_detail WHERE code_ue='$idUE' AND code_matiere='$idMatiere'";
        $res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);

}

function recupMatiereUESpecif($idclasse,$option) {
  	global $cnx;
        global $prefixe;  
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT a.code_matiere,m.libelle,a.code_prof,a.ordre_affichage, a.langue
			FROM  ${prefixe}affectations a , ${prefixe}matieres m
			WHERE code_classe='$idclasse'
			AND a.code_matiere = m.code_mat
			AND a.langue='$option'
			AND a.annee_scolaire='$anneeScolaire'
			ORDER BY a.ordre_affichage";
        $res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}

function recupMatiereUE($code_ue,$idclasse) {
        global $cnx;
        global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull , a.langue , a.specif_etat, NULL, NULL, a.num_semestre_info  
                        FROM  ${prefixe}ue_detail u, ${prefixe}matieres m , ${prefixe}affectations a
                        WHERE code_ue='$code_ue'
                        AND m.code_mat = u.code_matiere
                        AND a.id_ue_detail = u.code_ue_detail
			AND a.visubull = '1'
			AND a.annee_scolaire='$anneeScolaire'
                        ORDER BY a.ordre_affichage";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}


function recupCoursduJour2($dateDebut,$dateFin,$idprof,$idclasse) {
        global $cnx;
        global $prefixe;
        $dateDebut=dateFormBase($dateDebut);
        $sqlsuite="";
        if (($idprof != '') && ($idprof != 'tous')) {
                $sqlsuite.=" AND idprof='$idprof' ";
        }
        if (($idclasse != '') && ($idclasse != 'tous')) {
                $sqlsuite.=" AND idclasse='$idclasse' ";
        }
        if (trim($dateFin) != "") {
                $dateFin=dateFormBase($dateFin);
                $sqlsuite.=" AND date >= '$dateDebut' AND date <= '$dateFin' AND  (emargement='1' OR emargementeval='1') ORDER BY idclasse,date,heure ";
        }else{
                $sqlsuite.=" AND date='$dateDebut' AND (emargement='1' OR emargementeval='1') ORDER BY idclasse,date,heure ";
        }
        $sql="SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,emargement,emargementeval,emargementpedago,idgroupe FROM ${prefixe}edt_seances WHERE (coursannule != '1' OR coursannule IS NULL)  $sqlsuite  ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}



function rechercheCompteEmailMdp($email) {
        global $cnx;
	global $prefixe;
	$sql="SELECT elev_id FROM  ${prefixe}eleves WHERE email_eleve='$email'";
    $res=execSql($sql);
	$data=chargeMat($res);
	if (count($data)) { return("eleve:".$data[0][0]); }

	$sql="SELECT elev_id FROM  ${prefixe}eleves WHERE emailpro_eleve='$email'";
    $res=execSql($sql);
	$data=chargeMat($res);
	if (count($data)) { return("eleve:".$data[0][0]); }

	$sql="SELECT elev_id FROM  ${prefixe}eleves WHERE email_resp_2='$email'";
    $res=execSql($sql);
	$data=chargeMat($res);
	if (count($data)) { return("tuteur2:".$data[0][0]); }

	$sql="SELECT elev_id FROM  ${prefixe}eleves WHERE email='$email'";
    $res=execSql($sql);
	$data=chargeMat($res);
	if (count($data)) { return("tuteur1:".$data[0][0]); }
	
	return("");
}



function rechercheCompteEmailMdpPersonnel($email,$membre='') {
        global $cnx;
	global $prefixe;
	if ($membre != "") $sqlSuite=" AND type_pers='$membre' "; 
	$sql="SELECT pers_id FROM  ${prefixe}personnel WHERE email='$email' $sqlSuite ";
        $res=execSql($sql);
	$data=chargeMat($res);
	if (count($data)) { return("pers:".$data[0][0]); }
	return("");
}





function modifPassOublie($mdp0,$idpers,$membre,$email) {
        global $cnx;
	global $prefixe;
	if ((trim($idpers) != '') && (trim($email) != '')) {
		$error=0;
		$mdp=cryptage($mdp0);
		if ($membre == "pers") {
			$sql="UPDATE ${prefixe}personnel SET mdp='$mdp' WHERE pers_id='$idpers'";
			execSql($sql);
		}elseif ($membre == "tuteur1") {
			$sql="UPDATE ${prefixe}eleves SET passwd='$mdp' WHERE elev_id='$idpers'";
	        	execSql($sql);
		}elseif ($membre == "tuteur2") {
			$sql="UPDATE ${prefixe}eleves SET passwd_parent_2='$mdp' WHERE elev_id='$idpers'";
	       	 	execSql($sql);
		}elseif ($membre == "eleve") {
			$sql="UPDATE ${prefixe}eleves SET passwd_eleve='$mdp' WHERE elev_id='$idpers'";
			execSql($sql);
		}else {
			$error=1;
		}
		if ($error != 1) {
			$date=dateDMY();
			history_cmd("ASSISTANT","CHANGEMENT","Mot de passe pour $email");
		
			if (VATEL == "1") {

				$message="
Madame, Monsieur<br>
<br>
Vous avez contacté le service ENT Vatel parce que vous avez égaré votre mot de passe.<br>
Merci de noter votre mot de passe : $mdp0<br>
<br>
Le service ENT Vatel<br>
";
			}else{

				$message="
	Bonjour,<br>
<br>
	Suite a votre demande de changement de mot de passe du $date<br>
	<br>
	voici votre nouveau mot de passe : $mdp0<br>
<br>
	L'Equipe TRIADE	<br>
<br>
";
			}

			$to = trim($email);
			$objet="TRIADE : Changement de mot de passe";
			$sujet = "$objet";
  			$nom_expediteur=expediteur_triade();
  			$email_expediteur=MAILREPLY;
//	  		$message=TextNoAccent($message);
			
  			$ret="\n";
	  		if (PHP_OS == "WINNT") {  $ret="\r\n"; }	

  			$from = 'From: "'.$nom_expediteur.'" <'.$email_expediteur.'>'."$ret";
  			$headers=$from;
  			$email_expediteur=trim($email_expediteur);
  			//print "to : $to, Sujet : $sujet , From:$from <br>  <hr>";
  			if (ValideMail($to) && (ValideMail($email_expediteur)) ) {
				//mail($to, $sujet, $message,$headers);
				mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
			}
		}
	}
}

function recupRegime($id_eleve) {
        global $cnx;
        global $prefixe;
        $sql="SELECT regime FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}

function recupEmail($membre,$idpers,$tuteur) {
        global $cnx;
	global $prefixe;
	if ( ($membre == "menuadmin") || ($membre == "menuprof") || ($membre == "menuscolaire") || ($membre == "menupersonnel") || ($membre == "menututeur") ) {
		$sql="SELECT email FROM ${prefixe}personnel WHERE pers_id='$idpers' ";
	}else{
		if ($membre == "menueleve") $champs="email_eleve";
		if (($membre == "menuparent") && ($tuteur == "1")) $champs="email";
		if (($membre == "menuparent") && ($tuteur == "2")) $champs="email_resp_2";
		if (trim($champs) != "") { $sql="SELECT $champs FROM ${prefixe}eleves WHERE elev_id='$idpers' "; }
	}
	if (trim($sql) != "") {
	        $res=execSql($sql);
	        $data=chargeMat($res);
	        return $data[0][0];
	}
}


function recupEmailVatel($membre,$idpers,$tuteur) {
        global $cnx;
	global $prefixe;
	if ( ($membre == "menuadmin") || ($membre == "menuprof") || ($membre == "menuscolaire") || ($membre == "menupersonnel") || ($membre == "menututeur") ) {
		$sql="SELECT email FROM ${prefixe}personnel WHERE pers_id='$idpers' ";
	}else{
		if ($membre == "menueleve") $champs="emailpro_eleve";
		if (($membre == "menuparent") && ($tuteur == "1")) $champs="email";
		if (($membre == "menuparent") && ($tuteur == "2")) $champs="email_resp_2";
		if (trim($champs) != "") { $sql="SELECT $champs FROM ${prefixe}eleves WHERE elev_id='$idpers' "; }
	}
	if (trim($sql) != "") {
	        $res=execSql($sql);
	        $data=chargeMat($res);
	        return $data[0][0];
	}
}

function modifEmail($membre,$idpers,$tuteur,$email) {
        global $cnx;
	global $prefixe;
	if ( ($membre == "menuadmin") || ($membre == "menuprof") || ($membre == "menuscolaire") || ($membre == "menupersonnel") || ($membre == "menututeur") ) {
		$sql="UPDATE ${prefixe}personnel SET email='$email' WHERE pers_id='$idpers' ";
	}else{
		if ($membre == "menueleve") {
			if (VATEL == 1) {
				$champs="emailpro_eleve";
			}else{	
				$champs="email_eleve";
			}
		}
		if (($membre == "menuparent") && ($tuteur == "1")) $champs="email";
		if (($membre == "menuparent") && ($tuteur == "2")) $champs="email_resp_2";
		if (trim($champs) != "") { $sql="UPDATE ${prefixe}eleves SET $champs='$email' WHERE elev_id='$idpers' "; }
	}
	if (trim($sql) != "") { execSql($sql); } 
}


function verifEmailEnregistrer($idpers,$membre,$tuteur) {
        global $cnx;
	global $prefixe;
	if ( ($membre == "menuadmin") || ($membre == "menuprof") || ($membre == "menuscolaire") || ($membre == "menupersonnel") || ($membre == "menututeur") ) {
		$sql="SELECT email FROM ${prefixe}personnel WHERE pers_id='$idpers' AND email != '' ";
	}else{
		if ($membre == "menueleve") {
			if (VATEL == 1) {
				$champs="emailpro_eleve";
			}else{	
				$champs="email_eleve";
			}
		}
		if (($membre == "menuparent") && ($tuteur == "1")) $champs="email ";
		if (($membre == "menuparent") && ($tuteur == "2")) $champs="email_resp_2 ";
		if (trim($champs) != "") { $sql="SELECT $champs FROM ${prefixe}eleves WHERE elev_id='$idpers' AND $champs != ''  "; }
	}
	if (trim($sql) != "") {
	        $res=execSql($sql);
	        $data=chargeMat($res);
		$mail_valide =  preg_match('/([A-Za-z0-9]|-|_|\.)*@([A-Za-z0-9]|-|_|\.)*\.([A-Za-z0-9]|-|_|\.)*/',$data[0][0]);
		if (($data[0][0] == "") || ($mail_valide != 1)) {
			return(0);
		}else{
			return(1);
		}
	}
	return(0);
}

function enrRattrappage($refRattrapage,$date,$heure,$duree,$valide) {
        global $cnx;
	global $prefixe;
	if ((trim($refRattrapage) != "") && (trim($date) != '') && ($data != '0000-00-00') && (trim($duree) != '') && (trim($heure) != '') ) {
		$date=dateFormBase($date);
		$sql="INSERT INTO ${prefixe}absrtdrattrapage (date,heure_depart,duree,ref_id_absrtd,valider) VALUE ('$date','$heure','$duree','$refRattrapage','$valide')";
		execSql($sql);
	}
}


function suppRattrappage($refRattrapage) {
        global $cnx;
	global $prefixe;
	if (($refRattrapage != "") && ($refRattrapage != '0')) {
		$sql="DELETE FROM ${prefixe}absrtdrattrapage WHERE ref_id_absrtd='$refRattrapage'";
		execSql($sql);
	}
}

function recupRattrappage($refRattrapage) {
        global $cnx;
        global $prefixe;
	if (trim($refRattrapage) != '') {
	        $sql="SELECT date,heure_depart,duree,valider FROM ${prefixe}absrtdrattrapage WHERE ref_id_absrtd='$refRattrapage' ";
	        $res=execSql($sql);
	        $data=chargeMat($res);
        	return($data);
	}
}



function verifRattrapageRetards($elev_id, $heure_ret, $date_ret, $date_saisie, $duree_ret, $idmatiere, $justifier, $heure_saisie, $creneaux) {
        global $cnx;
        global $prefixe;
	$sql="SELECT idrattrapage FROM ${prefixe}retards  WHERE  elev_id='$elev_id' AND heure_ret='$heure_ret' AND date_ret='$date_ret' AND date_saisie='$date_saisie' AND duree_ret='$duree_ret' AND idmatiere='$idmatiere' AND justifier='$justifier' AND heure_saisie='$heure_saisie' AND creneaux='$creneaux'  ";
	$res=execSql($sql);
	$data_22=chargeMat($res);
	if (($data_22[0][0] != "") && ($data_22[0][0] != "0"))  {
		return($data_22[0][0]);
	}else{
		$idrattrapage="$elev_id#ref#".rand(0000,9999);
		$sql="UPDATE ${prefixe}retards SET idrattrapage='$idrattrapage' WHERE elev_id='$elev_id' AND heure_ret='$heure_ret' AND date_ret='$date_ret' AND date_saisie='$date_saisie' AND duree_ret='$duree_ret' AND idmatiere='$idmatiere' AND justifier='$justifier' AND heure_saisie='$heure_saisie' AND creneaux='$creneaux' ";
		execSql($sql);
	}
	return($idrattrapage);
}


function verifRattrapageAbsences($elev_id, $date_ab, $date_saisie, $duree_ab, $date_fin,  $idmatiere, $time, $heure_saisie, $creneaux) {
        global $cnx;
        global $prefixe;
	$sql="SELECT idrattrapage FROM ${prefixe}absences  WHERE  elev_id='$elev_id' AND date_ab='$date_ab' AND date_saisie='$date_saisie' AND duree_ab='$duree_ab' AND id_matiere='$idmatiere' AND time='$time' AND heure_saisie='$heure_saisie' AND creneaux='$creneaux' "; 
	$res=execSql($sql);
	$data_22=chargeMat($res);
	if ((trim($data_22[0][0]) != "") && ($data_22[0][0] != "0"))  {
		return($data_22[0][0]);
	}else{
		$idrattrapage="$elev_id#ref#".rand(0000,9999);
		$sql="UPDATE ${prefixe}absences SET idrattrapage='$idrattrapage' WHERE elev_id='$elev_id' AND date_ab='$date_ab' AND date_saisie='$date_saisie' AND id_matiere='$idmatiere' AND duree_ab='$duree_ab' AND time='$time' AND heure_saisie='$heure_saisie' AND creneaux='$creneaux' ";
		execSql($sql);
	}
	return($idrattrapage);
 
}


function codeCouleurEDTVT($code) {
/*	Argenté 12632256
	blanc 16777215
	Bleu = 255
	Bleu foncé 5242880
	Bleu-Vert 5263360
	Bordeaux 128
	Cyan 17776960
	Fuchsia 16711935
	Gris 5263440
	Jaune 65535
	Jaune clair 8454143
	Kaki 32896
	Lilas 16711808
	Marine 8388608
	Marron clair 32896
	Noir 0
	Orange 33023
	Pétrole 16744448
	Prune 8388736
	Rose 12615935
	Rouge = 16711680
	Rouge foncé 128
	Rose foncé 8388863
	Saumon 8421631
	Turquoise 8453888
	Vert 32768
	Vert clair 65280
	Vert eau 16776960
	Violet 8388736*/
	//$colors = array("#CCCCCC","#FFFFFF","#E2EBED","#FF0000","#00CC00","#9999FF","#FF6600","#330066","#FF00FF","#CCFF00","#33FF00","#993300","#333300","#6699FF","#9900FF",'#00FF00','#CC99FF','#808080','#008000','#BFDAA3');

	if ($code == "126322560" ) 	return "#CCCCCC";
	if ($code == "16777215" ) 	return "#FFFFFF";
	if ($code == "255" ) 		return "#E2EBED";
	if ($code == "5242880" ) 	return "#FF0000";
	if ($code == "5263360" ) 	return "#00CC00";
	if ($code == "128" ) 		return "#9999FF";
	if ($code == "17776960" ) 	return "#FF6600";
	if ($code == "16711935" ) 	return "#330066";
	if ($code == "5263440" ) 	return "#FF00FF";
	if ($code == "65535" ) 		return "#CCFF00";
	if ($code == "8454143" ) 	return "#33FF00";
	if ($code == "32896" ) 		return "#993300";
	if ($code == "16711808" ) 	return "#333300";
	if ($code == "8388608" ) 	return "#6699FF";
	if ($code == "32896" ) 		return "#9900FF";
	if ($code == "33023" ) 		return "#00FF00";
	if ($code == "8388736" ) 	return "#CC99FF";
	if ($code == "16744448" ) 	return "#808080";
	if ($code == "8388863" ) 	return "#008000";
	if ($code == "8421631" ) 	return "#BFDAA3";
	if ($code == "8453888" ) 	return "#CCCCCC";
	if ($code == "32768" ) 		return "#E2EBED";
	if ($code == "65280" ) 		return "#FF0000";
	if ($code == "16776960" ) 	return "#9999FF";
	if ($code == "8388736" ) 	return "#330066";
	return("#FFFFFF");
}

function listingRattrapageNonValider($dateDebut,$dateFin) {
        global $cnx;
	global $prefixe;
	$sqlSuite="";
	if ($dateDebut != "") {
		$dateDebut=dateFormBase($dateDebut);
		$dateFin=dateFormBase($dateFin);
		$sqlSuite=" AND r.date >= '$dateDebut' AND r.date <= '$dateFin' ";
	}
	$sql="(SELECT r.date,r.heure_depart,r.duree,r.ref_id_absrtd,r.id,r.valider,nom 
		FROM ${prefixe}absrtdrattrapage r , ${prefixe}eleves e , ${prefixe}absences a
		WHERE r.valider='0' AND a.idrattrapage = r.ref_id_absrtd AND a.elev_id = e.elev_id $sqlSuite )
		UNION
	        (SELECT r.date,r.heure_depart,r.duree,r.ref_id_absrtd,r.id,r.valider,nom 
		FROM ${prefixe}absrtdrattrapage r , ${prefixe}eleves e , ${prefixe}retards t
		WHERE r.valider='0' AND t.idrattrapage = r.ref_id_absrtd AND t.elev_id = e.elev_id  $sqlSuite ) ORDER BY nom
		"; 
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}



function rechercheEleveViaRef_id_absrtd($idrattrapage) {
        global $cnx;
        global $prefixe;
	if ($idrattrapage != '') {
		$sql="SELECT elev_id FROM ${prefixe}absences WHERE idrattrapage='$idrattrapage'"; 
		$res=execSql($sql);
		$data=chargeMat($res);
		if (count($data)) {
			$nomeleve=rechercheEleveNomPrenom($data[0][0]);
			return("$nomeleve#".$data[0][0]);
		}else{
			$sql="SELECT elev_id FROM ${prefixe}retards WHERE idrattrapage='$idrattrapage'"; 
			$res=execSql($sql);
			$data=chargeMat($res);
			if (count($data)) {
				$nomeleve=rechercheEleveNomPrenom($data[0][0]);
				return("$nomeleve#".$data[0][0]);
			}	
		}
	}
	return("Inconnu#0");
}

function valideRattrappage($id) {
        global $cnx;
        global $prefixe;
	$sql="UPDATE ${prefixe}absrtdrattrapage SET valider='1' WHERE id='$id'";
	execSql($sql);
}

function suppressionRattrapage($idsupp,$nompers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT ref_id_absrtd FROM ${prefixe}absrtdrattrapage  WHERE id='$idsupp'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$ref_id_absrtd=$data[0][0];
	if ($ref_id_absrtd != "") {
		list($nomeleve,$ideleve)=preg_split('/#/',rechercheEleveViaRef_id_absrtd($ref_id_absrtd));
		$sql="DELETE FROM ${prefixe}absrtdrattrapage  WHERE id='$idsupp' AND  ref_id_absrtd='$ref_id_absrtd' ";
		execSql($sql);
		history_cmd($nompers,"SUPPRESSION","rattrapage de Etudiant : $nomeleve");
	}
}


function recupRattrapage($dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;	
	$sql="SELECT  id,date,heure_depart,duree,ref_id_absrtd,valider FROM ${prefixe}absrtdrattrapage  WHERE date >= '$dateDebut'   AND  date <= '$dateFin' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}

function recupInfoRattrapageAbs($ref_id_absrtd) {
	global $cnx;
	global $prefixe;	
	$sql="SELECT elev_id, date_ab, duree_ab, motif, id_matiere, justifier, creneaux, idrattrapage, duree_heure  FROM ${prefixe}absences  WHERE idrattrapage='$ref_id_absrtd'";
	$res=execSql($sql);
	$data=chargeMat($res);		
	if (count($data)) { return($data); }

	$sql="SELECT elev_id, date_ret, duree_ret, motif, idmatiere, justifier, creneaux, idrattrapage FROM ${prefixe}retards  WHERE idrattrapage='$ref_id_absrtd'";
	$res=execSql($sql);
	$data=chargeMat($res);		
	if (count($data)) { return($data); }
}



function couleurDeFond3($inc) {
        if ($inc == 0) {
                $couleur="FFFFFF";
        }elseif ($inc == 2) {
                $couleur="FFFFFF";
        }elseif ($inc == 1) {
                $couleur="cccccc";
        }elseif ($inc == 3) {
                $couleur="FFFFFF";
        }elseif ($inc == 4) {
                $couleur="FFE664";
        }elseif ($inc == 5) {
                $couleur="FFFFFF";
        }elseif ($inc == 6) {
                $couleur="FFFFFF";
        }elseif ($inc == 7) {
                $couleur="FFFFFF";
        }elseif ($inc == 8) {
		$couleur="cba3c6";
	}elseif ($inc == 11) {
                $couleur="FFFFFF";
        }else{
                $couleur="FFFFFF";
        }
        return $couleur;
}

function couleurDeFond4($inc) {
        if ($inc == 0) {
                $couleur="B2CADE";
        }elseif ($inc == 2) {
                $couleur="CC9999";
        }elseif ($inc == 1) {
                $couleur="FCE4BA";
        }elseif ($inc == 3) {
                $couleur="99FF66";
        }elseif ($inc == 4) {
                $couleur="FFE664";
        }elseif ($inc == 5) {
                $couleur="B2CADE";
        }elseif ($inc == 6) {
                $couleur="FFFFFF";
        }elseif ($inc == 7) {
                $couleur="FFFFFF";
        }elseif ($inc == 8) {
                $couleur="cba3c6";
	}elseif ($inc == 10) {
                $couleur="94C11F";
	}elseif ($inc == 11) {
                $couleur="FFFFFF";
        }else{
                $couleur="B2CADE";
        }
        return $couleur;
}



/*
 * CRYPT_CKEY et CRYPT_CIV sont à changer pour votre projet
 */


function encrypt($text) {
     $text_num = str_split($text, CRYPT_CBIT_CHECK);
     $text_num = CRYPT_CBIT_CHECK - strlen($text_num[count($text_num)-1]);
 
    for ($i=0;$i<$text_num; $i++)
         $text = $text . chr($text_num);
 
    $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'cbc', '');
     mcrypt_generic_init($cipher, CRYPT_CKEY, CRYPT_CIV);
     
    $decrypted = mcrypt_generic($cipher, $text);
     mcrypt_generic_deinit($cipher);
 
    return base64_encode($decrypted);
}


function decrypt($encrypted_text) {
     $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'cbc', '');
     mcrypt_generic_init($cipher, CRYPT_CKEY, CRYPT_CIV);
     
    $decrypted = mdecrypt_generic($cipher, base64_decode($encrypted_text));
     mcrypt_generic_deinit($cipher);
     
    $last_char = substr($decrypted,-1);
 
    for($i=0; $i<(CRYPT_CBIT_CHECK-1); $i++) {
         if(chr($i) == $last_char)
         {
             $decrypted = substr($decrypted, 0, strlen($decrypted)-$i);
             break;
         }
    }
    return $decrypted;
}

function natureImage($fichier) {
        $array= getImageSize($fichier);
        $type= $array[2];
        switch($type){
          case 1 : $type= "GIF"; break;
          case 2 : $type= "JPG"; break;
          case 3 : $type= "PNG"; break;
          case 4 : $type= "SWF"; break;
          case 5 : $type= "PSD"; break;
          case 6 : $type= "BMP"; break;
          case 7 : $type= "TIFF Intel"; break;
          case 8 : $type= "TIFF Motorola"; break;
          case 9 : $type= "JPC"; break;
          case 10 : $type= "JP2"; break;
          case 11 : $type= "JPX"; break;
          default : $type= "inconnu"; break;
        }
        return($type);
}

function vatel_liste_ueT($semestre,$idclasse,$anneeScolaire) {  // fonction utilisée pour afficher la liste de toute des UE
   	global $cnx;
	global $prefixe;
	if ($semestre == 'tous') { 
		$sqlsuite= "(semestre='0' OR semestre='1' OR semestre='2')";
	}else{ 
		$sqlsuite= "semestre='$semestre'"; 
	} 
	$sql="SELECT code_ue,nom_ue,semestre FROM ${prefixe}ue WHERE $sqlsuite AND code_classe='$idclasse' AND annee_scolaire='$anneeScolaire' ORDER BY nom_ue ";
	return (chargeMat(execSql($sql)));
}

function recupNomUE($ue) {
   	global $cnx;
	global $prefixe;
	$sql="SELECT code_ue FROM  ${prefixe}ue_detail WHERE  code_ue_detail='$ue'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$ue=$data[0][0];
	$sql="SELECT nom_ue,code_ue,semestre FROM ${prefixe}ue WHERE code_ue='$ue'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data);
}


function recupInfoM($id_pers,$idparent,$membre) {
   	global $cnx;
	global $prefixe;
	if ($membre == "menuparent") {
		if ($idparent == 2) $suite="_resp_2";
		$sql="SELECT email$suite,civ_$idparent  FROM ${prefixe}eleves WHERE elev_id='$id_pers' AND `mailing_tu$idparent`='0'";
	}elseif($membre == "menueleve") {
		$sql="SELECT email_eleve,sexe FROM ${prefixe}eleves WHERE elev_id='$id_pers' AND mailing_el='0'";
	}else{
		$sql="SELECT email,civ FROM ${prefixe}personnel WHERE pers_id='$id_pers' AND mailing_pers='0'";
	}
	$res=execSql($sql);
	$data=chargeMat($res);
	return ($data);
}


function modifEtat($id_pers,$idparent,$membre) {
   	global $cnx;
	global $prefixe;
	if ($membre == "menuparent") {
		$sql="UPDATE ${prefixe}eleves SET `mailing_tu$idparent`='1' WHERE elev_id='$id_pers'";
	}elseif($membre == "menueleve") {
		$sql="UPDATE ${prefixe}eleves SET mailing_el='1' WHERE elev_id='$id_pers'";
	}else{
		$sql="UPDATE ${prefixe}personnel SET mailing_pers='1' WHERE pers_id='$id_pers'";
	}
	execSql($sql);
}

function nb2space($chaine) {
	$chaine=preg_replace('/ /','&nbsp;',$chaine);
	return($chaine);
}


function check_rib($cbanque, $cguichet, $nocompte, $clerib) {
        $tabcompte = "";
        $len = strlen($nocompte);
        if ($len != 11) {
                return false;
        }
        for ($i = 0; $i < $len; $i++) {
                $car = substr($nocompte, $i, 1);
                if (!is_numeric($car)) {
                        $c = ord($car) - (ord('A') - 1);
                        $b = (($c + pow ( 2, ($c - 10) / 9 )) % 10) + (($c > 18 && $c < 25) ? 1 : 0);
                        $tabcompte .= $b;
                }
                else {
                        $tabcompte .= $car;
                }
        }
        $int = $cbanque . $cguichet . $tabcompte . $clerib;
        return (strlen($int) >= 21 && bcmod($int, 97) == 0);
}


function Rib2Iban($codebanque,$codeguichet,$numerocompte,$cle){
        $charConversion = array("A" => "10","B" => "11","C" => "12","D" => "13","E" => "14","F" => "15","G" => "16","H" => "17",
        "I" => "18","J" => "19","K" => "20","L" => "21","M" => "22","N" => "23","O" => "24","P" => "25","Q" => "26",
        "R" => "27","S" => "28","T" => "29","U" => "30","V" => "31","W" => "32","X" => "33","Y" => "34","Z" => "35");
        $tmpiban = strtr(strtoupper($codebanque.$codeguichet.$numerocompte.$cle)."FR00",$charConversion);
        // Soustraction du modulo 97 de l'IBAN temporaire à 98
        $cleiban = strval(98 - intval(bcmod($tmpiban,"97")));
        if (strlen($cleiban) == 1)
                $cleiban = "0".$cleiban;
        return "FR".$cleiban.$codebanque.$codeguichet.$numerocompte.$cle;
}


function anneeScolaireSelect($anneeselected=""){
         $annee=dateY();
         $anneemoins=$annee - 1 ;
         $anneeplus=$annee + 1 ;
         $anneeplusdeux=$anneeplus + 1;
         $anneeScolaire=anneeScolaire();
	 list($anneeDebut,$anneeFin)=preg_split('/-/',"$anneeScolaire");
	 $anneeDebut=trim($anneeDebut);
	 $anneeFin=trim($anneeFin);

	
         if ($anneeScolaire != "") {
		 $anneeplus=$anneeFin + 1;
		 $anneeplusdeux=$anneeplus + 1;
         	 $anneemoins=$anneeDebut - 1 ;
	 	 $choix2="selected='selected'";
		 if ($anneeselected == "$anneeFin - $anneeplus"){ $choix1="selected='selected'"; $choix2=""; }
		 if ($anneeselected == "$anneeScolaire") $choix2="selected='selected'";
		 if ($anneeselected == "$anneemoins - $anneeDebut") { $choix3="selected='selected'"; $choix2=""; }
                 print "<option id='select1' value='$anneeFin - $anneeplus' $choix1 >$anneeFin - $anneeplus</option>";
                 print "<option value='$anneeScolaire'  id='select1' $choix2 >$anneeScolaire</option>";
                 print "<option id='select1' value='$anneemoins - $anneeDebut' $choix3 >$anneemoins - $anneeDebut</option>";
         }else{
                 print "<option value=''id='select0' >".LANGCHOIX."</option>";
                 print "<option id='select1' value='$anneeplusdeux - $anneeplus' >$anneeplusdeux - $anneeplus</option>";
                 print "<option id='select1' value='$anneeplus - $annee' >$anneeplus - $annee</option>";
                 print "<option id='select1' value='$annee - $anneemoins' >$annee - $anneemoins</option>";
         }
}

function filtreAnneeScolaireSelectNote($choix,$nb=10){
        $annee=dateY();
        print "<option id='select0' value='' >".LANGCHOIX3."</option>";
        for($i=1;$i<=$nb;$i++) {
                $annee2=$annee + 1 ;
                $selected="";
                if ($choix == "$annee - $annee2") $selected="selected='selected'";
                print "<option id='select1' value='$annee - $annee2' $selected >$annee - $annee2</option>";
                $annee--;
        }
}

         
function filtreAnneeScolaireSelectFutur(){
	$annee=dateY();
	$nb=10;
	$anneeP=$annee-1;
        print "<option id='select0' value='' >".LANGCHOIX3."</option>";
        print "<option id='select1' value='$anneeP - $annee' >$anneeP - $annee</option>";
        for($i=1;$i<=$nb;$i++) {
                $annee2=$annee + 1 ;
                $selected="";
                print "<option id='select1' value='$annee - $annee2' >$annee - $annee2</option>";
                $annee++;
        }
}

function filtreAnneeScolaireSelectAnterieur($choix,$nb=10){
        $annee=dateY();
        print "<option id='select0' value='' >".LANGCHOIX3."</option>";
        for($i=1;$i<$nb;$i++) {
                $annee2=$annee + 1 ;
                $selected="";
                if ($choix == "$annee - $annee2") $selected="selected='selected'";
                print "<option id='select1' value='$annee - $annee2' $selected >$annee - $annee2</option>";
                $annee--;
        }
}




function filtreAnneeScolaireSelect($choix){
	$annee=dateY();
	for($i=1;$i<=10;$i++) {
		$annee2=$annee + 1 ;
		$selected="";
		if ($choix == "$annee - $annee2") $selected="selected='selected'";
		print "<option id='select1' value='$annee - $annee2' $selected >$annee - $annee2</option>";
		$annee--;
	}
}


function verifierIBAN($iban){
 
        $charConversion = array("A" => "10","B" => "11","C" => "12","D" => "13","E" => "14","F" => "15","G" => "16","H" => "17",
"I" => "18","J" => "19","K" => "20","L" => "21","M" => "22","N" => "23","O" => "24","P" => "25","Q" => "26","R" => "27",
"S" => "28","T" => "29","U" => "30","V" => "31","W" => "32","X" => "33","Y" => "34","Z" => "35");
 
        // Déplacement des 4 premiers caractères vers la droite et conversion des caractères
        $tmpiban = strtr(substr($iban,4,strlen($iban)-4).substr($iban,0,4),$charConversion);
 
        // Calcul du Modulo 97 par la fonction bcmod et comparaison du reste à 1
        return (intval(bcmod($tmpiban,"97")) == 1);
}

function isValideIBAN ($s_IBAN) {
        // Vérification que le numéro IBAN est bien défini
        if(empty($s_IBAN)) return false;
 
        // Nettoyage des caractères de formatage et mise en Capital
        $s_IBAN = strtoupper(trim($s_IBAN));
        /* Vérification de l'IBAN par rapport au modèle :
                - Ne comporte pas ' espace , . / - ? : ( ) , " +
                - Suppression des caractères IBAN en début de phrase si présent
                - Déplacement des 4 premiers caractères (2 lettres et 2 chiffres) à la fin de la chaîne
                - Remplacement des caractères alphabétiques comme suit : A->10, B->11 C->12... Z->35
                - Vérifie que le modulo 97 donne 1
        */
        $s_modele = array('/[\'\s\/\-\?:\(\)\.,"\+]/', '/^IBAN(.+)/', '/([[:alpha:]]{2}[[:digit:]]{2})([[:alnum:]]+)/', "/([A-Z])/e");
        $s_retour = array('', '\1', '\2\1', "ord('\\1')-55");
        $i_IBAN = preg_replace($s_modele, $s_retour, $s_IBAN);
 
        return (bcmod($i_IBAN, 97) == 1) ;
}



function suppPeriodeEdt($dateDebut,$dateFin) {
	global $cnx;
        global $prefixe;
        $sql="DELETE FROM ${prefixe}edt_seances WHERE date >='$dateDebut' AND date <= '$dateFin'";
        execSql($sql);
}

function saveBulletinArchiv($idEleve,$annescolaire,$trimestre,$classe,$fichier){
        global $cnx;
        global $prefixe;
        $sql="DELETE FROM ${prefixe}bulletin_archivage WHERE ideleve='$idEleve' AND anneescolaire='$annescolaire' AND trimestre='$trimestre' ";
	execSql($sql);
	$date=date("Y-m-d");
        $classe=addslashes($classe);
        $fichier=addslashes($fichier);	
	$trimestre=addslashes($trimestre);
	$sql="INSERT INTO ${prefixe}bulletin_archivage (ideleve,anneescolaire,trimestre,date,classe,fichier) VALUES ('$idEleve','$annescolaire','$trimestre','$date','$classe','$fichier')";
	execSql($sql);
}

function recupArchiveBulletin($idEleve) {
	global $cnx;
        global $prefixe;
	$sql="SELECT ideleve,anneescolaire,trimestre,date,classe,fichier FROM ${prefixe}bulletin_archivage WHERE  ideleve='$idEleve' ORDER BY anneescolaire,trimestre ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return ($data);
}

function suppArchiveBulletinEleve($fichier) {
	global $cnx;
        global $prefixe;
	$sql="DELETE FROM  ${prefixe}bulletin_archivage WHERE  fichier='$fichier' ";
	execSql($sql);
}

function bulletin_archivage($trimestre,$anneeScolaire,$fichiersource,$idEleve,$classe_nom,$nomEleve,$prenomEleve) {
        if (trim($anneeScolaire) == "") {
                $annescolaire=anneeScolaire();
        }else{
                $annescolaire=$anneeScolaire;
        }
        $annescolaire=preg_replace('/ /','',$annescolaire);
        $classe_nom=TextNoAccent($classe_nom);
        $classe_nom=TextNoCarac($classe_nom);
        $classe_nom=preg_replace('/\//',"_",$classe_nom);
        if (!is_dir("./data/archive")) { mkdir("./data/archive"); }
        if (!is_dir("./data/archive/bulletin")) { mkdir("./data/archive/bulletin"); }
        if (!is_dir("./data/archive/bulletin/$annescolaire")) { mkdir("./data/archive/bulletin/$annescolaire"); }
        if (!is_dir("./data/archive/bulletin/$annescolaire/_$idEleve")) { mkdir("./data/archive/bulletin/$annescolaire/_$idEleve"); }
        $fichierdest="./data/archive/bulletin/$annescolaire/_$idEleve/${classe_nom}_${nomEleve}_${prenomEleve}_${annescolaire}_".$trimestre.".pdf";
        @unlink($fichierdest); // destruction avant creation
	@copy($fichiersource,$fichierdest);
        saveBulletinArchiv($idEleve,$annescolaire,$trimestre,$classe_nom,$fichierdest);
        history_cmd($_SESSION['nom'],"Archivage","Bulletin $nomEleve $prenomEleve $trimestre");
        $date=date("d/m/Y");
        enrHistoEleve($idEleve,$date,"CREATION","Bulletin du $trimestre en ".preg_replace('/_/',' ',$classe_nom));
}


function nbpersreeel() {
         global $cnx;
         global $prefixe;
         $sql="SELECT pers_id FROM ${prefixe}personnel  ";
         $res=execSql($sql);
         $data2=ChargeMat($res);
         $nbpers=count($data2);

         $data=visu_affectation();
         for($i=0;$i<count($data);$i++) {
                 $nbeleve=nbEleve($data[$i][0]);
                 $nbeleveTotal+=$nbeleve;
         }
         $nbeleve=$nbeleveTotal;
         return($_SERVER["SERVER_NAME"].":$nbeleve:$nbpers");
}


function listeMatiereProf($idprof,$idclasse,$idmatiere) {
         global $cnx;
         global $prefixe;
	 $anneeScolaire=$_COOKIE["anneeScolaire"];
         $sql="SELECT code_matiere FROM ${prefixe}affectations WHERE  code_prof='$idprof' AND code_matiere != '$idmatiere' AND code_classe='$idclasse' AND annee_scolaire='$anneeScolaire' ";
         $res=execSql($sql);
         $data2=ChargeMat($res);
	 return($data2);
}


function import_entreprise($params) {
	global $cnx;
        global $prefixe;

	$nom=$params['nom_entreprise'];
	if (trim($nom) == "") return(false); 
	
	$sql="SELECT * FROM  ${prefixe}stage_entreprise WHERE nom='$nom'";
	$res=execSql($sql);
	$data2=ChargeMat($res);

	if (count($data2)) return(false); 

	$sql="INSERT INTO ${prefixe}stage_entreprise (
		`secteur_ac` ,
		`activite_prin` ,
		`nom` ,
		`adresse` ,
		`ville` ,
		`code_p` ,
		`contact` ,
		`tel` ,
		`fax` ,
		`email` ,
		`info_plus` ,	
		`secteur_ac2` ,
		`secteur_ac3` ,
		`pays_ent` ,
		`contact_fonction` ,
		`siteweb` ,
		`registrecommerce` ,
		`siren` ,
		`siret` ,
		`formejuridique` ,
		`secteureconomique` ,
		`INSEE` ,
		`NAFAPE` ,
		`NACE` ,
		`typeorganisation` 
		)VALUES (
		'$params[secteur_activite]',
		'$params[activite_principale]',
		'$params[nom_entreprise]',
		'$params[adresse]',
		'$params[ville_entreprise]',
		'$params[code_postal]',
		'$params[nom_responsable]',
		'$params[telephone]',
		'$params[fax]',
		'$params[email]',
		'$params[information]',
		'$params[secteur_activite_2]',
		'$params[secteur_activite_3]',
		'$params[pays_entreprise]',
		'$params[fonction_responsable]',
		'$params[web]',
		'$params[registre_commerce]',
		'$params[siren]',
		'$params[siret]',
		'$params[forme_juridique]',
		'$params[secteur_economique]',
		'$params[insee]',
		'$params[naf_ape]',
		'$params[nace]',
		'$params[organisation]'
		)";
		return(execSql($sql));	
}



function import_entreprise_pigier($params) {
	global $cnx;
        global $prefixe;

	$nom_entreprise=$params["nom_societe"];
	if (trim($nom_entreprise) == "") return(false); 

	$email_etudiant=$params["email-etudiant"];
	if (trim($email_etudiant) == "") return(false); 

	$sql="SELECT * FROM  ${prefixe}stage_entreprise WHERE nom='$nom_entreprise'";
	$res=execSql($sql);
	$data2=ChargeMat($res);

	$adresse=$params["adresse_societe"];
	$ccp=$params["ccp"];
	$ville=$params["ville"];
	$civ=civ2($params["civilite"]);
	$nom_tuteur=$params["nom_tuteur"];
	$prenom_tuteur=$params["prenom_tuteur"];
	$tel_societe=$params["tel_societe"];
	if (!preg_match('/^0/',$tel_societe)) { $tel_societe="0".$tel_societe; }
	$email_tuteur=$params["email-tuteur"];
	$mdp=$params["mot_de_passe_tuteur"];

	if ($mdp == "") { 
		$mdp=passwd_random(); 
	}

	if (count($data2)) {
		// UPDATE
		$sql="UPDATE ${prefixe}stage_entreprise SET adresse='$adresse' , ville='$ville' , code_p='$ccp'  WHERE nom='$nom_entreprise' (nom,adresse,ville,code_p)";
	}else{
		$sql="INSERT INTO ${prefixe}stage_entreprise (nom,adresse,ville,code_p) VALUES ('$nom_entreprise','$adresse','$ville','$ccp')";
		execSql($sql);
	}

	$data=recherche_entreprise_nom($nom_entreprise);
	$idsociete=$data[0][0];
	if ($idsociete > 0) create_personnel($nom_tuteur,$prenom_tuteur,$mdp,'TUT',$civ,'',$adresse,$ccp,$tel_societe,$email_tuteur,$ville,$tel_societe,$idsociete,'','','');

	$sql="SELECT pers_id FROM  ${prefixe}personnel WHERE type_pers='TUT' AND email='$email_tuteur'";
	$res=execSql($sql);
        $data=ChargeMat($res);
	$idtuteurStage=$data[0][0];

	$num_stage=$params["num_stage"];
	$email_etudiant=$params["email-etudiant"];

	$sql="SELECT elev_id,classe FROM ${prefixe}eleves WHERE email_eleve='$email_etudiant'";
	$res=execSql($sql);
        $data=ChargeMat($res);
	$ideleve=$data[0][0];
	$idclasse=$data[0][1];
	if ($ideleve > 0) {
		list($deb,$fin)=preg_split('/-/',$num_stage);
		for($i=$deb;$i<=$fin;$i++){
			$sql="SELECT id FROM ${prefixe}stage_date WHERE numstage='$i' AND idclasse='$idclasse' ";
			$res=execSql($sql);
		        $data33=ChargeMat($res);
			$idstage=$data33[0][0];
			$sql="SELECT * FROM  ${prefixe}stage_eleve WHERE id_eleve='$ideleve' AND num_stage='$idstage'";
			$res=execSql($sql);
		        $data=ChargeMat($res);
			if (count($data) == 0) {
				$sql="INSERT INTO ${prefixe}stage_eleve(id_eleve,id_entreprise,lieu_stage,ville_stage,num_stage,code_p,compte_tuteur_stage,tel) VALUES ('$ideleve','$idsociete','$adresse','$ville','$idstage','$ccp','$idtuteurStage','$tel_societe')";
				execSql($sql);
			}
		}
	}
	return(true);

}


function nbsp($chaine) { 
	$chaine=preg_replace('/ /','&nbsp;',$chaine);
	return($chaine); 
}


function confirmEleveStageCentral($productid,$id,$idcentralestage,$value) {
         global $cnx;
         global $prefixe;
         $sql="UPDATE ${prefixe}centralstageattribution SET confirmer='$value' WHERE productid='$productid' AND id='$id' AND idcentralstage='$idcentralestage'";
         execSql($sql);
}


function  ajaxEnvoiMailCentral($productid,$id,$idcentralestage,$email,$nomprenometudiant,$infocontenu,$numbermail) {
         global $cnx;
         global $prefixe;
         include_once("./common/config2.inc.php");
        
	 $nomprenometudiant=utf8_decode($nomprenometudiant);
 	 $nomprenometudiant=stripslashes($nomprenometudiant);
	 $infocontenu=utf8_decode($infocontenu); 
 	 $infocontenu=stripslashes($infocontenu);

	 list($nom_ent,$contact,$nometablissement,$villeetablissement,$paysetablissement,$periode,$departement)  = preg_split('/\|\|/',$infocontenu);
         $objet=aff_valeur_parametrage("${numbermail}mailentrobj");
         $message=aff_valeur_parametrage("${numbermail}mailentrmess");
         $ccmail=aff_valeur_parametrage("${numbermail}mailentrcc");

         $message.="

 ----------------------------------------------------------------------------------------------------------
 Conformement aux dispositions de la loi Informatiques et Libertes du 6 Janvier 1978, vous 
 disposez d'un droit d'acces, de rectification, de modification et de suppression des donnees vous 
 concernant.
 Pour toute question relative aux donnees personnelles ou pour exercer vos droits au titre 
 de la loi Informatiques et Libertes, vous pouvez contacter l'administrateur Triade de votre 
 etablissement scolaire.
 ----------------------------------------------------------------------------------------------------------
 ---> T.R.I.A.D.E.  http://www.triade-educ.org / Gestion Scolaire via Internet <----

 ";

         $message=preg_replace('/NOMPRENOMTUDIANT/',"$nomprenometudiant",$message);
         $message=preg_replace('/NOMENTREPRISE/',"$nom_ent",$message);
         $message=preg_replace('/NOMCONTACT/',"$contact",$message);
         $message=preg_replace('/NOMETABLISSEMENTSCOLAIRE/',"$nometablissement",$message);
         $message=preg_replace('/VILLETABLISSEMENTSCOLAIRE/',"$villeetablissement",$message);
         $message=preg_replace('/PAYSETABLISSEMENTSCOLAIRE/',"$paysetablissement",$message);
	 $message=preg_replace('/DEPARTEMENT/',"$departement",$message);
	 

         $to = trim($email);
         $objet=TextNoAccent($objet) ;
         $objet=stripslashes($objet);
         $sujet = "$objet";
         $nom_expediteur=expediteur_triade();
         $email_expediteur=MAILREPLY;
         //$message=TextNoAccent($message);
         $ret="\n";
         if (PHP_OS == "WINNT") {  $ret="\r\n"; }
         $from = 'From: "'.$nom_expediteur.'" <'.$email_expediteur.'>'."$ret";
         $headers=$from;
         //$message=TextNoAccent($message);
         $email_expediteur=trim($email_expediteur);
	 if (ValideMail($to) && (ValideMail($email_expediteur)) ) {
  		mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
                $sql="UPDATE ${prefixe}centralstageattribution SET emailenvoye='1' WHERE productid='$productid' AND id='$id' AND idcentralstage='$idcentralestage'";
                execSql($sql);
                if (ValideMail($ccmail)) mailTriade($sujet,$message,$message,$ccmail,$email_expediteur,$email_expediteur,$nom_expediteur,"");
                return("ok");
         }else{
                return("nonok");
         }
} 


function ajoutPartage($chemin,$fichier,$idPersSource,$idPersDest,$idclasse,$membrerep,$idpersrep) {
        global $cnx;
	global $prefixe;
	$sql="INSERT INTO  ${prefixe}stockage_partage (fichier,chemin,membreIdProprio,membreIdAutorise,idclasse,membresource,idsource) VALUES ('$fichier','$chemin','$idPersSource','$idPersDest','$idclasse','$membrerep','$idpersrep')";
	execSql($sql);	
}


function suppPartage($chemin,$fichier,$idPersSource,$idPersDest,$idclasse) {
        global $cnx;
	global $prefixe;
        $sql="DELETE FROM ${prefixe}stockage_partage WHERE fichier='$fichier' AND chemin='$chemin' AND membreIdProprio='$idPersSource' AND idclasse='$idclasse'";
	execSql($sql);
}

function verifSIPartage($chemin,$fichier,$idPersSource,$idPersDest) {
	global $cnx;
	global $prefixe;
	$sql="SELECT count(*) FROM ${prefixe}stockage_partage WHERE fichier='$fichier' AND chemin='$chemin' AND membreIdProprio='$idPersSource' AND membreIdAutorise='$idPersDest'";
        $res=execSql($sql);
        $data=ChargeMat($res);
	return($data[0][0]);
}


function recupListFichierPartager($idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT fichier,chemin,membreIdProprio,membreIdAutorise,idclasse,membresource,idsource,id FROM ${prefixe}stockage_partage 
		     WHERE membreIdAutorise='$membre$idpers' ";
        $res=execSql($sql);
        $data=ChargeMat($res);
	return($data);
}

function recupListFichierPartagerViaId($id) {
	global $cnx;
	global $prefixe;
	$sql="SELECT fichier,chemin,membreIdProprio,membreIdAutorise,idclasse,membresource,idsource,id FROM ${prefixe}stockage_partage 
		     WHERE id='$id' ";
	
        $res=execSql($sql);
        $data=ChargeMat($res);
	return($data);
}


function verifProfPUE($idpers) {
	global $cnx;
	global $prefixe;
	if ($idpers > 0) {
		$sql="SELECT * FROM ${prefixe}ue WHERE idpers_profp='$idpers' ";
	        $res=execSql($sql);
	        $data=ChargeMat($res);
		return(count($data));
	}else{
		return(false);
	}
}

function recupIdClasseUEProfp($idpers) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_classe FROM ${prefixe}ue WHERE idpers_profp='$idpers' GROUP BY code_classe";
        $res=execSql($sql);
        $data=ChargeMat($res);
	return($data);
}


function recupUECode_UE($idpers,$idclasse) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_ue,nom_ue FROM ${prefixe}ue WHERE idpers_profp='$idpers' AND code_classe='$idclasse' ";
        $res=execSql($sql);
        $data=ChargeMat($res);
	return($data);
}


function enr_commentaire_classe($commentaire,$idmatiere,$tri,$idclasse,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_com_classe WHERE idclasse='$idclasse' AND idmatiere='$idmatiere' AND trimestre='$tri' AND annee_scolaire='$anneeScolaire' ";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}bulletin_com_classe (idclasse,commentaire,idmatiere,trimestre,annee_scolaire) VALUES ('$idclasse','$commentaire','$idmatiere','$tri','$anneeScolaire')";
	execSql($sql);
}


function cherche_com_classe_matiere($idmatiere,$tri,$idclasse,$anneeScolaire='') {
	global $cnx;
	global $prefixe;
	$sql="SELECT commentaire FROM ${prefixe}bulletin_com_classe WHERE idclasse='$idclasse' AND idmatiere='$idmatiere' AND trimestre='$tri' AND annee_scolaire='$anneeScolaire' ";
        $res=execSql($sql);
        $data=ChargeMat($res);
	return($data[0][0]);
}


function affiche_stage_multiple($ideleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT num_stage,id_entreprise,service,loger,nourri,indemnitestage,compte_tuteur_stage,compte_tuteur_stage_2 FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve'";
//	id_eleve,id_entreprise,lieu_stage,ville_stage,id_prof_visite,date_visite_prof,loger,nourri,passage_x_service,raison,info_plus,num_stage,code_p,tuteur_stage,tel,compte_tuteur_stage,alternance,jour_alternance,dateDebutAlternance,dateFinAlternance,horairedebutjournalier,horairefinjournalier,date_visite_prof2,id_prof_visite2,service,indemnitestage,pays_stage,fax,autre_responsable
	
	$res=execSql($sql);
    $data=ChargeMat($res);
    return($data);
}


function recupCoefPartielApplicationPratique($idClasse,$tri,$idmatiere) {
        global $cnx;
        global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT coef FROM ${prefixe}affectations WHERE code_classe='$idClasse' AND (trim='$tri' OR trim='tous') AND code_matiere='$idmatiere' AND annee_scolaire='$anneeScolaire'";
        $res=execSql($sql);
        $data=ChargeMat($res);
        return($data[0][0]);
} 

function recupPieceJointeMessagerie($idpiecejointe) {
        global $cnx;
        global $prefixe;
        $sql="SELECT md5,nom,etat,idpiecejointe FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        for($i=0;$i<count($data);$i++) {
                $md5=$data[$i][0];
                if ( (!file_exists("./data/fichiersj/$md5")) && (!is_dir("./data/fichiersj/$md5")))  {
                        $sql="DELETE FROM ${prefixe}piecejointe WHERE md5='$md5'";
                        execSql($sql);
                }
        }
        $sql="SELECT md5,nom,etat,idpiecejointe FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}


function alerteMessage($idmessage) {
	global $cnx;
        global $prefixe;
	$sql="SELECT alerte FROM ${prefixe}messageries WHERE id_message='$idmessage'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if ($data[0][0] == "1") {
		$alerte="0";
	}else{
		$alerte="1";
	}
	$sql="UPDATE ${prefixe}messageries SET alerte='$alerte' WHERE id_message='$idmessage'";
	execSql($sql);
	return($alerte);
}


function miseAjourBase() {
	global $cnx;
	global $prefixe;
	$date=date("Y-m-d");
	$heure=date("H:m:s");
	// ----------------------------------------------------------------------------------------------------------------------------------
	$sql="TRUNCATE TABLE ${prefixe}news_admin";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}news_admin (nom,prenom,date,heure,titre,texte,type,config_video) VALUES ('Triade','Support','$date','$heure','Essai Vidéo','<table align=center style=\'box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);\' ><tr><td><object width=420 height=280><param name=movie value=http://www.youtube.com/v/_R5IQoIYvTM?fs=1&amp;hl=fr_FR&amp;rel=0></param><param name=allowFullScreen value=false ></param><param name=allowscriptaccess value=always ></param><embed src=http://www.youtube.com/v/_R5IQoIYvTM?fs=1&amp;hl=fr_FR&amp;rel=0 type=application/x-shockwave-flash allowscriptaccess=always allowfullscreen=false width=420 height=280></embed></object></td></tr></table><br /><br />','video','')";
	$cr=execSql($sql);
	// ------------------------------------------------------------------------------------------------------------------------------------
}

function recupClasseProf($idpers,$anneeScolaire) {
        global $cnx;
        global $prefixe;
        $sql="SELECT a.code_classe FROM ${prefixe}affectations a, ${prefixe}classes c  WHERE a.code_prof='$idpers' AND a.code_classe=c.code_class AND a.annee_scolaire='$anneeScolaire' GROUP BY a.code_classe ORDER BY c.libelle";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function recupInfoTuteurStage($ideleve) {
	global $cnx;
        global $prefixe;
	$sql="SELECT compte_tuteur_stage FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve'";
	$res=execSql($sql);
        $data=chargeMat($res);
	$idtuteur=$data[0][0];
	
	$sql="SELECT nom,prenom,civ,email,adr,code_post,commune,tel,tel_port,id_societe_tuteur FROM ${prefixe}personnel WHERE pers_id='$idtuteur' AND type_pers='TUT' ";
	$res=execSql($sql);
        $data=chargeMat($res);
	return($data);
}

function ProbatoireEleve($id_eleve,$probaval) {
	global $cnx;
        global $prefixe;
	$sql="UPDATE ${prefixe}eleves SET probatoire='$probaval' WHERE elev_id='$id_eleve'";
	execSql($sql);
}

function getProbaEleve($id_eleve) {
	global $cnx;
        global $prefixe;
	$sql="SELECT probatoire FROM  ${prefixe}eleves WHERE elev_id='$id_eleve'";
	$res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}


function MyAddSlashes($val) {
	return $val;
}


function verifSiInfoParamSaisie() {
	global $cnx;
        global $prefixe;
	$sql="SELECT * FROM  ${prefixe}info_ecole WHERE id='1' AND map='0'";
	$res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) == 1) {
		return(0);
	}else{
		return(1);
	}
}


function nbEleveSexeFemme($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];


	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves , ${prefixe}classes  WHERE  sexe='f' AND  classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e , ${prefixe}classes c , ${prefixe}eleves_histo h WHERE  e.sexe='f' AND  h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.elev_id )";
	$res=execSql($sql);
        $data=chargeMat($res);

/*
        if (anneeScolaireViaIdClasse($idclasse) == $_COOKIE["anneeScolaire"]) {
		$sql="SELECT sexe FROM ${prefixe}eleves WHERE sexe='f' AND classe='$idclasse' ";
        }else{
                $sql="SELECT e.sexe FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' AND e.sexe='f' ";
        }
	$res=execSql($sql);
	$data=chargeMat($res);
 */
	return(count($data));
}


function nbEleveSexeHomme($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];

	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves , ${prefixe}classes  WHERE  sexe='m' AND  classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e , ${prefixe}classes c , ${prefixe}eleves_histo h WHERE  e.sexe='m' AND  h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.elev_id )";
	$res=execSql($sql);
	$data=chargeMat($res);

/*
        if (anneeScolaireViaIdClasse($idclasse) == $_COOKIE["anneeScolaire"]) {
		$sql="SELECT sexe FROM ${prefixe}eleves WHERE sexe='m' AND classe='$idclasse' ";
	}else{
                $sql="SELECT e.sexe FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' AND e.sexe='m' ";
        }
	$res=execSql($sql);
	$data=chargeMat($res);
 */
	return(count($data));
}


function coefcent($valeur,$coef,$verif) {
	if ($valeur == "") return $valeur;
        if ($verif == "oui") {
                $multiplicateur=100*$coef;
                $multiplicateur=$multiplicateur/20;
                $val=$valeur*$multiplicateur;
                $val=number_format("$val",'0','','');
                return($val);
        }else{
                return($valeur);
        }
}


function duplicateEDT($idclasseSource,$idclasseDestination,$date_debut,$date_fin) {
        global $cnx;
        global $prefixe;
        $date_debut=dateFormBase($date_debut);
        $date_fin=dateFormBase($date_fin);
        $sql="SELECT code,enseignement,`date`,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,emargement,emargementeval,emargementpedago,idressource,idgroupe,id_resa_liste,affichehoraire FROM ${prefixe}edt_seances WHERE idclasse='$idclasseSource' AND `date` >= '$date_debut' AND `date` <='$date_fin' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        for($i=0;$i<count($data);$i++) {
                $code=$data[$i][0]."_$idclasseDestination";
                $enseignement=$data[$i][1];
                $date=$data[$i][2];
                $heure=$data[$i][3];
                $duree=$data[$i][4];
                $bgcolor=$data[$i][5];
                $idprof=$data[$i][7];
                $prestation=$data[$i][8];
                $idmatiere=$data[$i][9];
                $coursannule=$data[$i][10];
                $docdst=$data[$i][11];
                $reportle=$data[$i][12];
                $reporta=$data[$i][13];
                $emargement=$data[$i][14];
                $emargementeval=$data[$i][15];
                $emargementpedago=$data[$i][16];
                $idressource=$data[$i][17];
                $idgroupe=$data[$i][18];
                $id_resa_liste=$data[$i][19];
                $affichehoraire=$data[$i][20];
                $sql2="SELECT * FROM ${prefixe}edt_seances WHERE idclasse='$idclasseDestination' AND code='$code' AND enseignement='$enseignement' AND `date`='$date'
                                                                AND heure='$heure' AND duree='$duree' AND bgcolor='$bgcolor' AND idprof='$idprof'
                                                                AND prestation='$prestation' AND idmatiere='$idmatiere' AND coursannule='$coursannule'
                                                                AND docdst='$docdst' AND reportle='$reportle' AND reporta='$reporta' AND emargement='$emargement' AND emargementeval='$emargementeval'
                                                                AND emargementpedago='$emargementpedago' AND idressource='$idressource' AND idgroupe='$idgroupe'
                                                                AND id_resa_liste='$id_resa_liste' AND affichehoraire='$affichehoraire'";
                $res2=execSql($sql2);
                $data2=chargeMat($res2);
                if (count($data2)) {
                        continue;
                }else{
                        $sql3="INSERT INTO ${prefixe}edt_seances (code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,emargement,emargementeval,emargementpedago,idressource,idgroupe,id_resa_liste,affichehoraire) VALUES ('$code','$enseignement','$date','$heure','$duree','$bgcolor','$idclasseDestination','$idprof','$prestation','$idmatiere','$coursannule','$docdst','$reportle','$reporta','$emargement','$emargementeval','$emargementpedago','$idressource','$idgroupe','$id_resa_liste','$affichehoraire')";
                        execSql($sql3);
                }

        }

} 

function human_filesize($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
  	$factor = floor((strlen($bytes) - 1) / 3);
  	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}


function nbRattrapageAbsRtd($id_eleve,$dateDebut,$dateFin) {
        global $cnx;
        global $prefixe;
        $sql="SELECT idrattrapage FROM ${prefixe}absences WHERE elev_id='$id_eleve' AND date_ab >= '$dateDebut' AND date_ab <= '$dateFin' AND idrattrapage != ''";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        $nb=count($data_2);
        $sql="SELECT idrattrapage FROM ${prefixe}retards WHERE elev_id='$id_eleve' AND date_ret >= '$dateDebut' AND date_ret <= '$dateFin' AND idrattrapage != ''";
        $res=execSql($sql);
        $data_2=chargeMat($res);
        $nb+=count($data_2);
        return($nb);
}

function archiveCumulAbsRtd() {
        global $cnx;
	global $prefixe;
	$sql="SELECT annee_scolaire FROM ${prefixe}info_ecole WHERE id='1'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$anneeScolaire=$data[0][0];

	$sql="SELECT elev_id,classe FROM ${prefixe}eleves";
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$ideleve=$data[$i][0];
		$idclasse=$data[$i][1];
		$sql="SELECT duree_ab, duree_heure FROM ${prefixe}absences WHERE elev_id='$ideleve'";
		$res=execSql($sql);
		$data2=chargeMat($res);	
		for($j=0;$j<count($data2);$j++) {
			if ($data2[$j][0] == "-1") {
				list($heure,$minute)=preg_split('/\./',$data2[$j][1]);
				$nbheureabs+=$minute*60;
				$nbheureabs+=$heure*3600;
			}else{
				$nbdemijourabs+=$data2[$j][0];
			}
		}

		$sql="SELECT duree_ret  FROM ${prefixe}retards WHERE elev_id='$ideleve'";
		$res=execSql($sql);
		$data2=chargeMat($res);	
		for($j=0;$j<count($data2);$j++) {
			if (preg_match('/mn$/',$data2[$j][0])) {
				$nbretards+=(preg_replace('/mn/','',$data2[$j][0]) * 60);
			}else{
				$nbretards+=(preg_replace('/h/','',$data2[$j][0]) * 3600);
			}
			
		}
 
		
		$sql="DELETE FROM ${prefixe}cumul_abstrd  WHERE ideleve='$ideleve' AND  anneescolaire='$anneeScolaire' ";
		execSql($sql);
		$sql="INSERT INTO ${prefixe}cumul_abstrd (ideleve,idclasse,nbheureabs,nbdemijourabs,nbretards,anneescolaire) VALUES ('$ideleve','$idclasse','$nbheureabs','$nbdemijourabs','$nbretards','$anneeScolaire')";
		execSql($sql);
		// nbheureabs, nbretard en secondes
		$nbheureabs=0;
		$nbdemijourabs=0;
		$nbretards=0;

	}
}


function verifSiEtudeDeCas($code_ue,$idClasse) {
        global $cnx;
        global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage,a.visubull,a.langue,a.specif_etat
                        FROM  ${prefixe}ue_detail u, ${prefixe}matieres m , ${prefixe}affectations a
                        WHERE code_ue='$code_ue'
                        AND m.code_mat = u.code_matiere
                        AND a.id_ue_detail = u.code_ue_detail
                        AND a.code_classe = '$idClasse'
			AND a.annee_scolaire='$anneeScolaire'
                        AND a.specif_etat='etudedecasipac'";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data)) return(true);
        return(false);
}


function verifSiEtudeDeCas2($nom_ue,$idClasse) {
        global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT code_ue FROM ${prefixe}ue WHERE nom_ue='$nom_ue' AND code_classe='$idClasse'";
        $res=execSql($sql);
        $data=chargeMat($res);
        for($i=0;$i<count($data);$i++) {
                $code_ue=$data[$i][0];
                $sql="SELECT u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage,a.visubull,a.langue,a.specif_etat
                        FROM ${prefixe}ue_detail u, ${prefixe}matieres m , ${prefixe}affectations a
                        WHERE code_ue='$code_ue'
                        AND m.code_mat = u.code_matiere
                        AND a.id_ue_detail = u.code_ue_detail
			AND a.code_classe = '$idClasse'
			AND a.annee_scolaire='$anneeScolaire'
                        AND a.specif_etat='etudedecasipac'";
                $res=execSql($sql);
                $data2=chargeMat($res);
                if (count($data2)) return(true);
        }
        return(false);
}


function cherchemailparent2($id_eleve) {
        global $cnx;
        global $prefixe;
        $sql="SELECT email_resp_2 FROM ${prefixe}eleves WHERE elev_id='$id_eleve' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}


function saveMoyenneAnnuel($idEleve,$idmatiere,$idClasse,$code_ue,$moyen,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=preg_replace('/ /','',$anneeScolaire);
	$sql="DELETE FROM ${prefixe}save_moy_annuel WHERE ideleve='$idEleve' AND idclasse='$idClasse' AND idmatiere='$idmatiere' AND code_ue='$code_ue' AND anne_scolaire='$anneeScolaire' "; 
	execSql($sql);
	$sql="INSERT INTO ${prefixe}save_moy_annuel (ideleve,idclasse,idmatiere,code_ue,moyenne,anne_scolaire) VALUES ('$idEleve','$idClasse','$idmatiere','$code_ue','$moyen','$anneeScolaire')";
	execSql($sql);
}



function recursive_delete($path) {
    $O = dir($path);
    if(!is_object($O))
    return false;
    while($file = $O -> read()) {
            if($file != '.' && $file != '..') {
                if(is_file($path.'/'.$file)) {
                        $cr=unlink($path.'/'.$file);
                }else{
                        if(is_dir($path.'/'.$file)) {
                                recursive_delete($path.'/'.$file);
                        }
                }
            }
        }
    // !!!! il faut bien appeler 2 fois la méthode close() !!!
    $O -> close();
    if (SERVEURTYPE != "SERVEURFREE") { $O -> close(); }
    rmdir($path);
    return true;
}


function sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location) {

/* Utilisation
$from_name = "webmaster";
$from_address = "";
$to_name = "";
$to_address = "";
$startTime = "12/03/2015 09:00:00";
$endTime = "12/03/2015 18:00:00";
$subject = "";
$description = "";
$location = "";
sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location);
*/


    $domain = 'exchangecore.com';

    //Create Email Headers
    $mime_boundary = "----Meeting Booking----".MD5(TIME());

    $headers = "From: ".$from_name." <".$from_address.">\n";
    $headers .= "Reply-To: ".$from_name." <".$from_address.">\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
    $headers .= "Content-class: urn:content-classes:calendarmessage\n";
    
    //Create Email Body (HTML)
    $message = "--$mime_boundary\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= "<html>\n";
    $message .= "<body>\n";
    $message .= '<p>Dear '.$to_name.',</p>';
    $message .= '<p>'.$description.'</p>';
    $message .= "</body>\n";
    $message .= "</html>\n";
    $message .= "--$mime_boundary\r\n";

    $ical = 'BEGIN:VCALENDAR' . "\r\n" .
    'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
    'VERSION:2.0' . "\r\n" .
    'METHOD:REQUEST' . "\r\n" .
    'BEGIN:VTIMEZONE' . "\r\n" .
    'TZID:Eastern Time' . "\r\n" .
    'BEGIN:STANDARD' . "\r\n" .
    'DTSTART:20091101T020000' . "\r\n" .
    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
    'TZOFFSETFROM:-0400' . "\r\n" .
    'TZOFFSETTO:-0500' . "\r\n" .
    'TZNAME:EST' . "\r\n" .
    'END:STANDARD' . "\r\n" .
    'BEGIN:DAYLIGHT' . "\r\n" .
    'DTSTART:20090301T020000' . "\r\n" .
    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
    'TZOFFSETFROM:-0500' . "\r\n" .
    'TZOFFSETTO:-0400' . "\r\n" .
    'TZNAME:EDST' . "\r\n" .
    'END:DAYLIGHT' . "\r\n" .
    'END:VTIMEZONE' . "\r\n" .	
    'BEGIN:VEVENT' . "\r\n" .
    'ORGANIZER;CN="'.$from_name.'":MAILTO:'.$from_address. "\r\n" .
    'ATTENDEE;CN="'.$to_name.'";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:'.$to_address. "\r\n" .
    'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
    'UID:'.date("Ymd\TGis", strtotime($startTime)).rand()."@".$domain."\r\n" .
    'DTSTAMP:'.date("Ymd\TGis"). "\r\n" .
    'DTSTART;TZID="Eastern Time":'.date("Ymd\THis", strtotime($startTime)). "\r\n" .
    'DTEND;TZID="Eastern Time":'.date("Ymd\THis", strtotime($endTime)). "\r\n" .
    'TRANSP:OPAQUE'. "\r\n" .
    'SEQUENCE:1'. "\r\n" .
    'SUMMARY:' . $subject . "\r\n" .
    'LOCATION:' . $location . "\r\n" .
    'CLASS:PUBLIC'. "\r\n" .
    'PRIORITY:5'. "\r\n" .
    'BEGIN:VALARM' . "\r\n" .
    'TRIGGER:-PT15M' . "\r\n" .
    'ACTION:DISPLAY' . "\r\n" .
    'DESCRIPTION:Reminder' . "\r\n" .
    'END:VALARM' . "\r\n" .
    'END:VEVENT'. "\r\n" .
    'END:VCALENDAR'. "\r\n";
    $message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST\n';
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= $ical;

    $mailsent = mail($to_address, $subject, $message, $headers);

    return ($mailsent)?(true):(false);
}


function chercherLangueClasse($id) {
        global $cnx;
        global $prefixe;
        $sql="SELECT langueclasse FROM ${prefixe}classes WHERE code_class='$id'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}

function chercherNiveauClasse($id) {
        global $cnx;
        global $prefixe;
        $sql="SELECT niveau FROM ${prefixe}classes WHERE code_class='$id'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
} 

function chercherSpecificationClasse($id) {
        global $cnx;
        global $prefixe;
        $sql="SELECT specification FROM ${prefixe}classes WHERE code_class='$id'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return stripslashes($data[0][0]);
} 


function recupLangueClasse($idClasse) {
        global $cnx;
        global $prefixe;
        $sql="SELECT langueclasse FROM ${prefixe}classes WHERE code_class='$idClasse'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}

function nbEtudiantNiveau($niveau) {
        global $cnx;
        global $prefixe;
        $sql="SELECT count(*) FROM ${prefixe}classes c, ${prefixe}eleves e WHERE c.niveau='$niveau' AND c.code_class=e.classe";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}

function recupUETRIADE($idClasse,$sem) {
        global $cnx;
	global $prefixe;
        $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT code_ue,nom_ue,coef_ue,ects_ue,nom_ue_en FROM  ${prefixe}ue WHERE code_classe='$idClasse' AND (semestre='1' OR semestre='2' OR semestre='0') AND annee_scolaire='$anneeScolaire' GROUP BY nom_ue  ORDER BY num_ue";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}

function recupMatiereUE2($nom_ue,$idclasse) {
        global $cnx;
        global $prefixe;
        $nom_ue=addslashes($nom_ue);
        $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull , a.langue , a.specif_etat , e.semestre, a.ects, a.num_semestre_info
                        FROM  ${prefixe}ue_detail u, ${prefixe}matieres m , ${prefixe}affectations a, ${prefixe}ue e
                        WHERE u.code_ue=e.code_ue
                        AND e.nom_ue='$nom_ue'
                        AND m.code_mat = u.code_matiere
                        AND a.id_ue_detail = u.code_ue_detail
                        AND a.visubull = '1'
                        AND a.code_classe='$idclasse'
			AND a.annee_scolaire='$anneeScolaire'
                        ORDER BY a.ordre_affichage";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}


function enrEtudiantHistory($idEleve,$anneeScolaire,$idclasse) {
	global $cnx;
	global $prefixe;
	if ($idEleve != 0) {
		$sql="DELETE FROM ${prefixe}eleves_histo WHERE ideleve='$idEleve' AND annee_scolaire='$anneeScolaire'";
		execSql($sql);
		$sql="INSERT INTO ${prefixe}eleves_histo (ideleve,idclasse,annee_scolaire) VALUES ('$idEleve','$idclasse','$anneeScolaire') ";
		execSql($sql);
	}

}

function listingHistoClasseEleve($idEleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT e.annee_scolaire,c.libelle FROM ${prefixe}eleves_histo e, ${prefixe}classes c WHERE e.ideleve='$idEleve' AND e.idclasse=c.code_class ORDER BY e.annee_scolaire DESC ";
	$res=execSql($sql);
        $data=chargeMat($res);
        return($data);		
}

function select_MatiereNonAffecter($len) {
        global $cnx;
        global $prefixe;
        $data=affToutesLesMatieres(); // code_mat,libelle,sous_matiere
        // $datA : tab bidim - soustab 3 champs
        for($i=0;$i<count($data);$i++) {
                if (verif_utiliser_matiere($data[$i][0])) continue;
                print "<option id='select1' value='".$data[$i][0]."' title=\"".trim($data[$i][1])." ".trim($data[$i][2])."\" > $ok ".trunchaine(trim(stripslashes($data[$i][1])),$len);
                if(trim($data[$i][2]) != "0") {
                        print trunchaine(" ".trim($data[$i][2]),$len);
                }
                print "</option>\n";
        }
}

function verifAnneeScolaireFuture($anneeScolaire,$idClasse) {
        global $cnx;
        global $prefixe;
        $anneeScolaireEnCours=anneeScolaireViaIdClasse($idClasse);
        list($annee,$anneep)=preg_split('/ - /',$anneeScolaireEnCours);
        list($anneef,$anneeff)=preg_split('/ - /',$anneeScolaire);
        if ($anneep == $anneef) return(true);
        return(false);

}


function saveIpacBulletin($idEleve,$admis,$anneeScolaire,$idclasse,$bulletinProvisoire) {
        global $cnx;
	global $prefixe;
	if ($idEleve > 0) {
		$sql="DELETE FROM ${prefixe}bulletin_ipac  WHERE ideleve='$idEleve' AND idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' AND bulletinprovisoire='$bulletinProvisoire' ";
		execSql($sql);
		$sql="INSERT INTO ${prefixe}bulletin_ipac (ideleve,idclasse,annee_scolaire,bulletinprovisoire,admis) VALUES ('$idEleve','$idclasse','$anneeScolaire','$bulletinProvisoire','$admis') ";
		execSql($sql);
	}
}

function recupIpacBulletin($idEleve,$anneeScolaire,$idclasse,$bulletinProvisoire) {
        global $cnx;
	global $prefixe;
	$sql="SELECT admis FROM ${prefixe}bulletin_ipac WHERE ideleve='$idEleve' AND idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' AND bulletinprovisoire='$bulletinProvisoire' ";
	$res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);	
}


function saveSavoirEtre($idEleve,$idclasse,$anneeScolaire,$ponct,$motiv,$dynam,$idpers,$idmatiere) {
        global $cnx;
	global $prefixe;
	$date=dateYMD();
	$sql="INSERT INTO ${prefixe}savoiretre (ideleve,idclasse,annee_scolaire,ponctualite,motivation,dynamisme,idpers,date,idmatiere) VALUES ('$idEleve','$idclasse','$anneeScolaire','$ponct','$motiv','$dynam','$idpers','$date','$idmatiere') ";
	execSql($sql);

}

function recupSavoirEtre($idEleve,$idclasse,$anneeScolaire) {
        global $cnx;
        global $prefixe;
	$sql="SELECT ponctualite,motivation,dynamisme,id,date,idpers,idmatiere FROM ${prefixe}savoiretre WHERE ideleve='$idEleve' AND idclasse='$idclasse'  AND annee_scolaire='$anneeScolaire'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}


function recupSavoirEtre2($id) {
        global $cnx;
	global $prefixe;
	$sql="SELECT ponctualite,motivation,dynamisme,id,date,ideleve FROM ${prefixe}savoiretre WHERE id='$id' ";
	$res=execSql($sql);
        $data=chargeMat($res);
        return($data);

}

function deleteSavoirEtre($id,$libelle) {
        global $cnx;
	global $prefixe;
	if ($id > 0) {
		if ($libelle == "ponct")  $libelle2="ponctualite";
		if ($libelle == "dyna")  $libelle2="dynamisme";
		if ($libelle == "motiv")  $libelle2="motivation";
		if ($libelle2 == "") return ;
		$sql="UPDATE ${prefixe}savoiretre SET $libelle2='' WHERE id='$id'";	
		//$sql="DELETE FROM ${prefixe}savoiretre WHERE id='$id' ";
		execSql($sql);
	}
}

function copyUniteEnseignement($saisie_classe_source,$anneeScolaireSource,$saisie_classe_destination,$anneeScolaireDest) {
        global $cnx;
	global $prefixe; 
	$sql="SELECT semestre,num_ue,nom_ue,coef_ue,ects_ue,idpers_profp,nom_ue_en,annee_scolaire,code_ue FROM ${prefixe}ue WHERE code_classe='$saisie_classe_source' AND annee_scolaire='$anneeScolaireSource' ";
	$res=execSql($sql);
        $data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$semestre=$data[$i][0];
		$num_ue=$data[$i][1];
		$nom_ue=addslashes($data[$i][2]);
		$coef_ue=$data[$i][3];
		$ects_ue=$data[$i][4];
		$idpers_profp=$data[$i][5];
		$nom_ue_en=addslashes($data[$i][6]);
		$code_ue=$data[$i][8];
		$sql="INSERT INTO ${prefixe}ue (code_classe,semestre,num_ue,nom_ue,coef_ue,ects_ue,idpers_profp,nom_ue_en,annee_scolaire) VALUES ('$saisie_classe_destination','$semestre','$num_ue','$nom_ue','$coef_ue','$ects_ue','$idpers_profp','$nom_ue_en','$anneeScolaireDest') ";
		execSql($sql);
	}

	for($i=0;$i<count($data);$i++) {
		$semestre=$data[$i][0];
		$num_ue=$data[$i][1];
		$nom_ue=$data[$i][2];
		$coef_ue=$data[$i][3];
		$ects_ue=$data[$i][4];
		$idpers_profp=$data[$i][5];
		$nom_ue_en=$data[$i][6];
		$code_ue=$data[$i][8];
		suiteCopyUniteEnseignement($saisie_classe_destination,$anneeScolaireDest,$code_ue,$num_ue,$semestre);
	}
}


function suiteCopyUniteEnseignement($saisie_classe,$anneeScolaire,$code_ue_ancien,$num_ue,$semestre) {
        global $cnx;
	global $prefixe;
	$sql="SELECT code_ue FROM ${prefixe}ue  WHERE code_classe='$saisie_classe' AND annee_scolaire='$anneeScolaire' AND semestre='$semestre' AND num_ue='$num_ue'";
	$res=execSql($sql);
        $data=chargeMat($res);
	$code_ue_new=$data[0][0];
	if (($code_ue_new > 0) && ($code_ue_ancien > 0)) { 
		$sql="SELECT code_matiere,code_enseignant,code_idgroupe FROM ${prefixe}ue_detail WHERE code_ue='$code_ue_ancien'";
		$res=execSql($sql);
		$data=chargeMat($res);
		for($i=0;$i<count($data);$i++) {
			$code_matiere=$data[$i][0];
			$code_enseignant=$data[$i][1];
			$code_idgroupe=$data[$i][2];
			if ($code_matiere > 0) { 
				$sql="INSERT INTO ${prefixe}ue_detail (code_ue,code_matiere,code_enseignant,code_idgroupe) VALUES ('$code_ue_new','$code_matiere','$code_enseignant','$code_idgroupe') ";
				execSql($sql);	
			}
		}
	}
 }


function accesBulletinElPar($tri,$anneeScolaire,$idclasse) {
 	global $cnx;
	global $prefixe; 
	$sql="INSERT INTO ${prefixe}bulletin_visu_parele (idclasse,tri,annee_scolaire) VALUES ('$idclasse','$tri','$anneeScolaire') ";
	execSql($sql);
}

function affichAccesBulletinElPar($anneeScolaire) {
	global $cnx;
	global $prefixe; 
	$sql="SELECT id,idclasse,tri FROM ${prefixe}bulletin_visu_parele WHERE annee_scolaire='$anneeScolaire'";
	$res=execSql($sql);
        $data=chargeMat($res);
	return($data);
}


function suppBulletinElPar($id) {
 	global $cnx;
	global $prefixe; 
	if ($id > 0) {
		$sql="DELETE FROM ${prefixe}bulletin_visu_parele WHERE id='$id'";
		execSql($sql);
	}
}

function recupAutorisationBulletinElPar($idClasse,$anneeScolaire) {
	global $cnx;
	global $prefixe; 
	$sql="SELECT tri,id FROM ${prefixe}bulletin_visu_parele WHERE annee_scolaire='$anneeScolaire' AND idclasse='$idClasse' ORDER BY annee_scolaire,tri ";
	$res=execSql($sql);
        $data=chargeMat($res);
	return($data);

}


function recupInfoBulletinElPar($id) {
		global $cnx;
		global $prefixe; 
		$sql="SELECT idclasse,tri,annee_scolaire FROM ${prefixe}bulletin_visu_parele WHERE id='$id' ";
		$res=execSql($sql);
        $data=chargeMat($res);
		return($data);
}

function rechercheSeanceEdt($datedepart) {
		global $cnx;
		global $prefixe; 
		$datedepart=dateFormBase($datedepart);
		$sql="SELECT idmatiere,idclasse,idprof,heure,duree FROM ${prefixe}edt_seances WHERE date='$datedepart' ";
		$res=execSql($sql);
        $data=chargeMat($res);
		return($data);
}


function recupIdClasseEleve($ideleve,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	$sql="SELECT classe FROM ${prefixe}eleves WHERE elev_id='$ideleve'";
	$res=execSql($sql);
        $data=chargeMat($res);
        $anneeScolaireEnCours=anneeScolaireViaIdClasse($idClasse);
	if ($anneeScolaireEnCours == $anneeScolaire) {
		$idClasse=$data[0][0];
	}else{
		$idClasse=recupHistoClasseEleve($ideleve,$anneeScolaire);
	}
	return $idClasse;
}

function recupHistoClasseEleve($Seid,$annee_scolaire) {
		global $cnx;
        global $prefixe;
		$sql="SELECT idclasse FROM ${prefixe}eleves_histo WHERE ideleve='$Seid' AND annee_scolaire='$annee_scolaire' ";
		$res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}

function modifSavoirEtre($text,$id,$libelle) {
	global $cnx;
        global $prefixe;
        if ($id > 0) {
                if ($libelle == "ponct")  $libelle2="ponctualite";
                if ($libelle == "dyna")  $libelle2="dynamisme";
                if ($libelle == "motiv")  $libelle2="motivation";
		if ($libelle2 == "") return ;
                $sql="UPDATE ${prefixe}savoiretre SET $libelle2='$text' WHERE id='$id'";
                execSql($sql);
        }
}

function deleteSavoirEtre2($id) {
	global $cnx;
    global $prefixe;
	if ($id > 0) {
		$sql="DELETE FROM ${prefixe}savoiretre WHERE id='$id'";
		execSql($sql);
    }
}

function aff_TuteurStage($idClasse) {
	global $cnx;
    global $prefixe;
	$anneeScolaire=anneeScolaireViaIdClasse($idClasse);
	$sql="SELECT p.pers_id,p.nom,p.prenom FROM ${prefixe}personnel p , ${prefixe}stage_eleve s , ${prefixe}eleves e , ${prefixe}stage_entreprise t
		WHERE  
		p.type_pers='TUT' AND 
		s.id_eleve=e.elev_id AND  
		e.classe = '$idClasse' AND
		e.annee_scolaire ='$anneeScolaire' AND
		s.id_entreprise = t.id_serial AND 
		p.id_societe_tuteur = t.id_serial
		GROUP BY p.pers_id
		ORDER BY p.nom
		";
	$res=execSql($sql);
    $data=chargeMat($res);
    return($data);
}


function verifEmailExistEleve($email,$idpers) {
		global $cnx;
		global $prefixe;
		if (trim($idpers) != "") { $sqlsuite=" AND elev_id!='$idpers' "; }
		$sql="SELECT count(*) FROM ${prefixe}eleves WHERE (email='$email' OR email_eleve='$email'  OR  emailpro_eleve='$email' OR email_resp_2='$email') $sqlsuite ";
		$res=execSql($sql);
		$data=chargeMat($res);
		return($data[0][0]);	
}

function savequestion($question) {
		global $cnx;
		global $prefixe;
		if (trim($question) != "") {
			$question=stripslashes($question);
			$sql="INSERT INTO ${prefixe}askevalens (question) VALUES ('$question') ";
			execSql($sql);
		}
}

function listQuestion() {
		global $cnx;
		global $prefixe;
		$sql="SELECT id,question FROM ${prefixe}askevalens";
		$res=execSql($sql);
        $data=chargeMat($res);
        return($data);
	
}

function suppQuestionEvalEns($id) {
		global $cnx;
		global $prefixe;
		if (is_numeric($id)) {
			$sql="DELETE FROM ${prefixe}askevalens WHERE id='$id'";
			execSql($sql);
		}
	
}

function verifSiDernierCours($idprof,$date) {
        global $cnx;
        global $prefixe;
        $date=dateFormBase($date);
        $sql="SELECT count(*) FROM ${prefixe}edt_seances WHERE idprof='$idprof' AND date > '$date'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}


function verifSiAutreGroupeEDT($eventEndDate,$idclasse) {
	global $cnx;
        global $prefixe;
	if ($idclasse > 0) {
		list($date,$heure)=preg_split('/ /',$eventEndDate);
		$sql="SELECT count(*) FROM ${prefixe}edt_seances WHERE idclasse='$idclasse' AND date='$date' AND heure='$heure'";
		$res=execSql($sql);
	        $data=chargeMat($res);
        	return($data[0][0]);
	}
}

function recupNetBrut($idpers)  {
        global $cnx;
        global $prefixe;
        $sql="SELECT enNet FROM ${prefixe}vacation_commande WHERE id_pers='$idpers'";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}


function create_com_livret_EPI_AP($idprofp,$thematique,$commentaire,$EPI,$tri,$saisie_classe,$annee_scolaire,$type_rubrique,$num,$ideleve) {
        global $cnx;
        global $prefixe;
        $sql="DELETE FROM ${prefixe}bulletin_livret_classe WHERE idprof='$idprofp' AND annee_scolaire='$annee_scolaire' AND type_rubrique='$type_rubrique' AND idclasse='$saisie_classe' AND trim='$tri' AND num='$num' AND ideleve='$ideleve' ";
        execSql($sql);
        $sql="INSERT INTO ${prefixe}bulletin_livret_classe  (intitule,thematique,idprof,commentaire,type_rubrique,annee_scolaire,trim,idclasse,num,ideleve) VALUES
                ('$EPI','$thematique','$idprofp','$commentaire','$type_rubrique','$annee_scolaire','$tri','$saisie_classe','$num','$ideleve') ";
        return(execSql($sql));
}


function recupComLivretEPIAP($type_rubrique,$anneeScolaire,$idclasse,$idprofP,$trim,$num) {
        global $cnx;
        global $prefixe;
        $sql="SELECT intitule,thematique,idprof,commentaire,type_rubrique,annee_scolaire,trim,idclasse,num FROM ${prefixe}bulletin_livret_classe
                WHERE idprof='$idprofP' AND annee_scolaire='$anneeScolaire' AND type_rubrique='$type_rubrique' AND idclasse='$idclasse' AND trim='$trim' AND num='$num' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}

function recupCommentaireLivretEPIAPIdeleve($type_rubrique,$anneeScolaire,$idclasse,$idprofP,$trim,$num,$ideleve) {
        global $cnx;
        global $prefixe;
        $sql="SELECT commentaire FROM ${prefixe}bulletin_livret_classe WHERE idprof='$idprofP' AND annee_scolaire='$anneeScolaire' AND type_rubrique='$type_rubrique' AND idclasse='$idclasse' AND trim='$trim' AND num='$num' AND ideleve='$ideleve' ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data[0][0]);
}


function rechercheEntreStageElecomplet($ideleve,$numstage) {
        global $cnx;
        global $prefixe;
        if (strtolower($numstage) == "alternance") {
                $sql="SELECT id_eleve,id_entreprise,num_stage,lieu_stage,ville_stage,tuteur_stage,id_prof_visite,date_visite_prof,tel,date_visite_prof2,id_prof_visite2,compte_tuteur_stage,info_plus,dateDebutAlternance,dateFinAlternance FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve' AND alternance='1' ";
        }else{
                $sql="SELECT id_eleve,id_entreprise,num_stage,lieu_stage,ville_stage,tuteur_stage,id_prof_visite,date_visite_prof,tel,date_visite_prof2,id_prof_visite2,compte_tuteur_stage,info_plus FROM ${prefixe}stage_eleve WHERE id_eleve='$ideleve' AND num_stage='$numstage' ";
        }
        $res=@execSql($sql);
        $data=chargeMat($res);
        return $data ;
}

function enrComBulletinCycle($ideleve,$cycle,$q1,$q2,$q3,$q4,$q5,$q6,$q7,$idprofp,$commentaire,$q4bis) {
        global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_cycle  WHERE ideleve='$ideleve' AND cycle='$cycle'";
	execSql($sql);

	$commentaire=preg_replace('/\\n/'," ",trim($commentaire));
	$sql="INSERT INTO ${prefixe}bulletin_cycle (ideleve,cycle,q1,q2,q3,q4,q5,q6,q7,commentaire,idprofp,q4bis) VALUES ('$ideleve','$cycle','$q1','$q2','$q3','$q4','$q5','$q6','$q7','$commentaire','$idprofp','$q4bis')";
	execSql($sql);
}


function recupInfoCyclePropP($ideleve,$cycle) {
        global $cnx;
	global $prefixe;
	$cycle=preg_replace('/cycle/','',$cycle);
	$sql="SELECT ideleve,cycle,q1,q2,q3,q4,q5,q6,q7,commentaire,idprofp,q4bis FROM ${prefixe}bulletin_cycle WHERE ideleve='$ideleve' AND cycle='$cycle' ";
	$res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function enrPtAbsBulletin($ideleve,$pt,$trim,$anneeScolaire,$idclasse) {
	global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}bulletin_ptabs WHERE ideleve='$ideleve' AND  annee_scolaire='$anneeScolaire' AND idclasse='$idclasse' AND trim='$trim'";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}bulletin_ptabs (ideleve,annee_scolaire,idclasse,trim,point) VALUES ('$ideleve','$anneeScolaire','$idclasse','$trim','$pt')";
	execSql($sql);
}

function recupPtAbsBulletin($ideleve,$trim,$anneeScolaire,$idclasse) {
	global $cnx;
        global $prefixe;
	$sql="SELECT point FROM ${prefixe}bulletin_ptabs WHERE ideleve='$ideleve' AND  annee_scolaire='$anneeScolaire' AND idclasse='$idclasse' AND trim='$trim'";
	$res=@execSql($sql);
        $data=chargeMat($res);
        return $data[0][0];
}

function ajaxFlagCentralStage($value,$flag,$attribution,$productid,$id,$idcentralestage) {
        global $cnx;
        global $prefixe;
        $sql="UPDATE ${prefixe}centralstageattribution  SET $flag='$value' WHERE id='$id' AND idcentralstage='$idcentralestage' AND attribution='$attribution' AND productid='$productid' ";
        return(execSql($sql));
}

function recupFlageCentral($productid,$flag,$attribution,$id,$idcentralestage) {
        global $cnx;
        global $prefixe;
        if ($flag == "flagcause") {
                $sql="SELECT flagcause FROM ${prefixe}centralstageattribution WHERE id='$id' AND idcentralstage='$idcentralestage' AND attribution='$attribution' AND productid='$productid' ";
                $res=@execSql($sql);
                $data=chargeMat($res);
                return $data[0][0];
        }else{
                $sql="SELECT $flag FROM ${prefixe}centralstageattribution WHERE id='$id' AND idcentralstage='$idcentralestage' AND attribution='$attribution' AND productid='$productid' ";
                $res=@execSql($sql);
                $data=chargeMat($res);
                if ($data[0][0] == 'true') {
                        return("checked='checked'");
                }else{
                        return("");
                }
        }
}



function createExamenConfig($examen,$coefexamen) {
        global $cnx;
        global $prefixe;
        $sql="INSERT INTO ${prefixe}config_examen (libelle,coef) VALUES ('$examen','$coefexamen') ";
        execSql($sql);
}


function recupExamenConfig() {
        global $cnx;
        global $prefixe;
        $sql="SELECT id, libelle , coef FROM ${prefixe}config_examen ORDER BY libelle";
        $res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function rechercheExamenConfig($id) {
        global $cnx;
        global $prefixe;
        $sql="SELECT id, libelle , coef FROM ${prefixe}config_examen WHERE id='$id' ";
        $res=@execSql($sql);
        $data=chargeMat($res);
        return $data;
}


function suppExamenConfig($id) {
        global $cnx;
        global $prefixe;
        $sql="DELETE FROM ${prefixe}config_examen WHERE id='$id' ";
        execSql($sql);
}


function modifExamenConfig($libelle,$coef,$id) {
        global $cnx;
        global $prefixe;
        $sql="UPDATE ${prefixe}config_examen SET libelle='$libelle' , coef='$coef' WHERE id='$id'";
        execSql($sql);
}

function recupMatiereUE2certifpositif($nom_ue,$idclasse) {
        global $cnx;
        global $prefixe;
        $nom_ue=addslashes($nom_ue);
        $anneeScolaire=$_COOKIE["anneeScolaire"];
        $sql="SELECT u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull , a.langue , a.specif_etat , e.semestre, a.ects, a.num_semestre_info
                        FROM  ${prefixe}ue_detail u, ${prefixe}matieres m , ${prefixe}affectations a, ${prefixe}ue e
                        WHERE u.code_ue=e.code_ue
                        AND e.nom_ue='$nom_ue'
                        AND m.code_mat = u.code_matiere
                        AND a.id_ue_detail = u.code_ue_detail
                        AND a.visubull = '1'
                        AND a.code_classe='$idclasse'
                        AND a.annee_scolaire='$anneeScolaire'
                        AND a.coef_certif > '0'
                        ORDER BY a.ordre_affichage";
        $res=execSql($sql);
        $data=chargeMat($res);
        return($data);
}

function recupNotePlanche($idmatiere,$idClasse,$ordre,$anneeScolaire) {
	global $cnx;
	global $prefixe;
       	$sql="SELECT note_planche FROM ${prefixe}affectations WHERE ordre_affichage='$ordre' AND code_matiere='$idmatiere' AND code_classe='$idClasse' AND annee_scolaire='$anneeScolaire'";
       	$res=@execSql($sql);
       	$data=chargeMat($res);
       	return($data[0][0]);
}



?>
