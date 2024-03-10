<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
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

include_once 'DB.php';

include_once 'conf_error.php';
include_once '../librairie_php/timezone.php';
include_once '../librairie_php/lib_prefixe.php';
include_once '../common/config2.inc.php';
include_once '../common/config.inc.php';

$gestionMDP=GESTIONMDP;
global $gestionMDP;

function cnx() {
	global $dsn;
	$cnx =& DB::connect($dsn);
        if(DB::isError($cnx))
        {
		// exit($cnx->getMessage());
        }
        else
        {
		if (!file_exists("../data/install_log/noaccess.inc")) {
			if (!get_magic_quotes_gpc()) {
				$_GET = array_map('trim', $_GET); 
				$_POST = array_map('trim', $_POST); 
				$_COOKIE = array_map('trim', $_COOKIE); 
				$_REQUEST = array_map('trim', $_REQUEST); 

				foreach($_GET as $key=>$value){ $_GET[$key] = $cnx->escapeSimple($value);  }
                                foreach($_POST as $key=>$value){ $_POST[$key] = $cnx->escapeSimple($value);  }
                                foreach($_COOKIE as $key=>$value){ $_COOKIE[$key] = $cnx->escapeSimple($value);  }
                                foreach($_REQUEST as $key=>$value){ $_REQUEST[$key] = $cnx->escapeSimple($value);  }


			/*	$_GET = @array_map('mysql_real_escape_string', $_GET); 
				$_POST = @array_map('mysql_real_escape_string', $_POST); 
				$_COOKIE = @array_map('mysql_real_escape_string', $_COOKIE); 
				$_REQUEST = @array_map('mysql_real_escape_string', $_REQUEST); */
			}
	                return $cnx;
		}
        }
}

function execSql($sql) {
        global $cnx;
	global $ERROR;

        $res =& $cnx->query($sql);
        if(DB::isError($res)) {
		if (preg_match('/restobase/i',$_SERVER['SCRIPT_NAME'])) {
			//Pgclose();
			//return ;
		}

	//	print $sql."<br>" ;
	//	print $res->getMessage()."<br><br>" ;

		if (($res->getMessage() != "DB Error: already exists") && ($res->getMessage() != "DB Error: unknown error")) {
            		$fichier="../data/erreurs.log";
	        	if (file_exists($fichier)) {
				$texte=dateDMY();
				$texte .= " <b>".$_SERVER['PHP_SELF']."</b><br />\n";
				$texte .= "Notice sur la ligne :<br>";
              	 		$texte .= "<i>$sql</i><br>";
              	 		$texte .= $res->getMessage()."<br>";
				$texte .= "<hr><br>";
              			$fichier=fopen($fichier,"a");
	        	      	fwrite($fichier,$texte);
        	      	  	fclose($fichier);
        		}
		}

		if (file_exists("../data/parametrage/analyse.triade")) {
			$fichier=fopen("../data/parametrage/analyse.log","a");
       	        	fwrite($fichier,"- ".dateDMY()." à ".dateHIS()."ERROR : ".$_SERVER['PHP_SELF']." -> ".$sql."\n");
       	        	fclose($fichier);
		}

		if ($ERROR == "true") {
                	print("<font color=\"red\"><b>$sql</b></font><br><br>");
	          	print $res->getMessage();
		}
		Pgclose();

        }else {
		if (file_exists("./data/parametrage/analyse.triade")) {
                        if (filesize("./data/parametrage/analyse.log") > 8000000) {
                                $suffixe=date("H");
                                if (filesize("./data/parametrage/analyse_${suffixe}.log")) {
                                        @unlink("./data/parametrage/analyse_${suffixe}.log");
                                }
                                copy("./data/parametrage/analyse.log","./data/parametrage/analyse_${suffixe}.log");
                                @unlink("./data/parametrage/analyse.log");
                        }
                        $fichier=fopen("./data/parametrage/analyse.log","a");
                        fwrite($fichier,"- ".dateDMY()." à ".dateHIS()." -- ".$_SERVER['PHP_SELF']." -- ".$sql."\n");
                        fclose($fichier);
                }
		
                return $res;
        }
}



function execSql2($sql) {
        global $cnx;
        $res =& $cnx->query($sql);
        if(DB::isError($res))
        {
                //print "<br>$sql</br>";
                //print $res->getMessage();
		return -1;
        }
        else {
                return $res;
        }
}


function Pgclose(){
        global $cnx;
        global $prefixe;
        $close=$cnx->disconnect();
        if(DB::isError($close))
        {
//                exit($close->getMessage());
        }
        else
        {
                return(true);
        }
}


function trunchaine($chaine,$len) {

	if (strlen(trim($chaine)) >= $len) {
     		$chaine = substr($chaine,0,$len) . "..." ;
	}
	return $chaine;
}


// function de relad de page
function reload_page($page) {
	print "<script>location.href='".$page."';</script>";
}


function MyAddSlashes($chaine) {
	if (PHPMAGICQUOTE == "auto") {      
		return( get_magic_quotes_gpc() == 1 ? $chaine : addslashes($chaine) );
	}elseif(PHPMAGICQUOTE == "off") {
		return(addslashes($chaine));
	}elseif(PHPMAGICQUOTE == "on") {
		return($chaine);
	}else{
		return($chaine);
	}
}

function MyStripSlashes($chaine) {
	if (PHPMAGICQUOTE == "auto") { 
		return( get_magic_quotes_gpc() == 1 ? stripslashes($chaine) : $chaine);
	}elseif(PHPMAGICQUOTE == "off") {
		return($chaine);
	}elseif(PHPMAGICQUOTE == "on") {
		return(stripslashes($chaine));
	}else{
		return(stripslashes($chaine));
	}
}


// chargement du résultat d'une requête SQL dans une matrice (tableau bi-dimensionnel)
function chargeMat($res) {
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
        return $mat;
}

function alertJs($txt) {
print <<<EOF
<script language=JavaScript>
alert("$txt");
</script>
EOF;
}

function error($code){
	if(!$code):
		print "<script>window.location.href='./error_base.php';</script>";
	else:
	endif;
}


// ---------------------------------------
// fonction de procedure d'installe triade
// ---------------------------------------
function delete_global() {
        global $cnx;
        global $prefixe;
	$sql="DELETE  FROM ${prefixe}personnel";
	$ins=execSql($sql);
	unset($sql);
	$sql="DELETE  FROM ${prefixe}groupes";
        $ins=execSql($sql);
	unset($sql);
	$sql="DELETE  FROM ${prefixe}types_personnel";
        $ins=execSql($sql);
	unset($sql);
	$sql="DELETE  FROM ${prefixe}retards";
	$ins=execSql($sql);
	unset($sql);
	$sql="DELETE  FROM ${prefixe}absences";
	$ins=execSql($sql);
}

function validperson() {
        global $cnx;
        global $prefixe;
	$sql="INSERT INTO ${prefixe}types_personnel(type_pers,libelle,membre) VALUES ('ADM','administrateur','menuadmin')";
        $ins=@execSql($sql);
	$sql="INSERT INTO ${prefixe}types_personnel(type_pers,libelle,membre) VALUES ('ENS','enseignant','menuprof')";
        $ins=@execSql($sql);
	$sql="INSERT INTO ${prefixe}types_personnel(type_pers,libelle,membre) VALUES ('MVS','Vie Scolaire','menuscolaire')";
        $ins=@execSql($sql);
	$sql="INSERT INTO ${prefixe}types_personnel(type_pers,libelle,membre) VALUES ('TUT','Tuteur de stage','menututeur')";
        $ins=@execSql($sql);
	$sql="INSERT INTO ${prefixe}types_personnel(type_pers,libelle,membre) VALUES ('PER','Personnel','menupersonnel')";
        $ins=@execSql($sql);
	return $ins;
}

function validabsretard() {
        global $cnx;
        global $prefixe;
	if (DBTYPE=="mysql") {
		$sql="INSERT INTO ${prefixe}absences (elev_id,date_ab,date_saisie,duree_ab) VALUES ('-4', '0000-00-00', '0000-00-00', '0')";
        //	$ins=@execSql($sql);
		$sql="INSERT INTO ${prefixe}retards (elev_id,heure_ret,date_ret,date_saisie) VALUES ('-4', '00:00:00', '0000-00-00', '0000-00-00')";
        //	$ins=@execSql($sql);
	//	return $ins;
		return true;
	}
	if (DBTYPE=="pgsql") {
		return true;
	}
}

function delete_groupe_null() {
	global $cnx;
    global $prefixe;
	$sql="DELETE  FROM ${prefixe}groupes WHERE group_id='0' ";
    $ins=execSql($sql);
}


function delete_retard_null() {
	global $cnx;
	global $prefixe;
	$sql="DELETE  FROM ${prefixe}retards  WHERE elev_id='-4' ";
    	$ins=execSql($sql);
}


function validGroup() {
	global $cnx;
    	global $prefixe;
	$sql="INSERT INTO ${prefixe}groupes(group_id,liste_elev,commentaire,libelle) VALUES ('0',NULL,NULL,NULL)";
	$ins=@execSql($sql);
	if (DBTYPE=="mysql") {
		$sql="UPDATE ${prefixe}groupes SET group_id='0',liste_elev=NULL,commentaire=NULL,libelle=NULL WHERE liste_elev IS NULL AND libelle IS NULL";
		$ins=@execSql($sql);
	}
	return $ins;
}

function VerifierAdresseMail($adresse) {
   	$Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#';
   	if(preg_match($Syntaxe,$adresse))
      		return true;
   	else
     		return false;
}


function validtriade($nom,$pren,$mdp) {
    	global $cnx;
    	global $prefixe;
	$civ="0";
	$tp="ADM";
	$mdp=cryptage($mdp);
	$nom=MyAddSlashes($nom);
	$pren=MyAddSlashes($pren);
	$sql="INSERT INTO ${prefixe}personnel(nom,prenom,mdp,type_pers,civ) VALUES ('$nom','$pren','$mdp','$tp',$civ)";
    	$ins=@execSql($sql);
	return $ins;
}

function cryptage($mdp) {
        global $gestionMDP;
        if ($gestionMDP == "MD5") {
                $mdp=md5($mdp);
        }elseif($gestionMDP == "SHA2") {
                $mdp=hash('sha256',$mdp);
        }else{
                $mdp=crypt(md5($mdp),"T2");
        }
        return $mdp;
}

//---------------------------------------------------------------------//
// --------------------------------------------------------------------- //
// Gestion d'envoi de fichier pour document
function create_fichier($titre,$fichier,$date,$type) {
        global $cnx;
	global $prefixe;
	$titre=MyAddSlashes($titre);
	$fichier=MyAddSlashes($fichier);
        $sql="INSERT INTO ${prefixe}fichier (titre,fichier,date,type) VALUES ('$titre','$fichier','$date','$type')";
        $ins=@execSql($sql);
        if($ins) {
                return 1;
        } else {
                return 0;
        }
}

function affiche_fichier() {
        global $cnx;
        global $prefixe;
        $sql="SELECT * FROM ${prefixe}fichier ORDER BY 1 ";
        $res=execSql($sql);
        $data=ChargeMat($res);
        return $data;
}

// reçoit une date au format aaaa-jj-mm
function dateForm($date) {
	$elements=preg_split('/-/',$date);
	$rdate=$elements[2]."/".$elements[1]."/".$elements[0];
	return $rdate;
}


// gestion des bugs
function aff_bug() {
        global $cnx;
        global $prefixe;
        $sql="SELECT id, nom, prenom, date, membre, action, service, commentaire  FROM ${prefixe}bug ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function supp_bug() {
        global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}bug";
        $ins=execSql($sql);
}

function listeblacklistetotal() {
        global $cnx;
        global $prefixe;
        $sql="SELECT id,nom,prenom,date,ip,nb_tentative,cause,membre FROM ${prefixe}blacklist ORDER BY date DESC  ";
        $res=execSql($sql);
        $data=chargeMat($res);
        return $data;
}

function  blacklistsupp($supp) {
        global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}blacklist WHERE id='$supp' ";
        $ins=execSql($sql);

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

// --------------------------------------------------
function verif_table_groupe() {
        global $cnx;
        global $prefixe;

        $sql="SELECT group_id,liste_elev,libelle FROM ${prefixe}groupes WHERE group_id='0' AND liste_elev IS NULL AND libelle IS NULL";
        $res=execSql($sql);
        $data=chargeMat($res);
        if (count($data) > 0) {
                return 1 ;
        }
        $sql="SELECT group_id,liste_elev,libelle FROM ${prefixe}groupes WHERE liste_elev IS  NULL AND libelle IS NULL";
        $res=execSql($sql);
        $data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$del=MyAddSlashes($data[$i][0]);
                $sql="DELETE FROM ${prefixe}groupes WHERE group_id='$del'";
                execSql($sql);
        }
        $sql="INSERT INTO ${prefixe}groupes (group_id,liste_elev,commentaire,libelle) VALUES ('0',NULL,NULL,NULL)";
        execSql($sql);
        if (DBTYPE=="mysql") {
                $sql="UPDATE ${prefixe}groupes SET group_id='0',liste_elev=NULL,commentaire=NULL,libelle=NULL WHERE libelle IS NULL AND liste_elev IS NULL ";
                execSql($sql);
        }
	return 0 ;
}

function verif_table() {
	// suppression des infos de l'history
	global $cnx;
	global $prefixe;
	$date=date("Y")-1;
	$date2=date("-m-d");
	$date=$date.$date2;
	$sql="DELETE FROM ${prefixe}history_cmd WHERE date_cmd < '$date'";
	execSql($sql);
	return 1;
}

function verif_matiere() {
	global $cnx;
	global $prefixe;
	if (DBTYPE=="mysql") {
		$sql="SELECT code_mat FROM ${prefixe}matieres WHERE sous_matiere IS NULL ";
		$res=execSql($sql);
	        $data=chargeMat($res);
		if (count($data) > 0) {
	                $sql="UPDATE ${prefixe}matieres SET sous_matiere=' ' WHERE sous_matiere IS NULL ";
			execSql($sql);
			return 0;
		}else{
			return 1;
		}
	}else{
		return 1 ;
	}
}


function verif_secu_rep() {
	htaccess("../data/fichier_ASCII");
	htaccess("../data/fichier_gep");
	htaccess("../data/parametrage");
	htaccess("../data/sauvegarde");
	htaccess("../data/install_log");
	htaccess("../common");
	htaccess("../data/dump");
	htaccess("../data/pdf_bull");
	htaccess("../data/pdf_certif");
	htaccess("../data/pdf_abs");
	htaccess("../data/stockage/");
	htaccess("../data/stockage/menuadmin");
	htaccess("../data/stockage/menuparent");
	htaccess("../data/stockage/menuprof");
	htaccess("../data/stockage/menuscolaire");
	htaccess("../data/stockage/menueleve");
	htaccess("../data/DevoirScolaire");
	htaccess("../data/forum");
	htaccess("../data/compteur");
	htaccess("../data/circulaire");
	htaccess("../data/archive");
	htaccess("../data/image_eleve");
	htaccess("../data/image_pers");
	htaccess("../data/recherche");
	htaccess("../data/fichiersj");
	htaccess("../data/vacation");
	htaccess("../data/comptaenseignant");
	htaccess("../data/compta");
	htaccess("./patch_ftp");
	htaccess("../data/cantine/");
	htaccess("../data/moodledata/");
	htaccess_specif("../data");
	return 1;
}


function purgeData() {
	optimise_repertoire();
	supp_rep_patch();
	verif_piece_jointe();
	verif_secu_rep();
	return 1;
}

function verif_piece_jointe() {
        global $cnx;
        global $prefixe;

        $sql="DELETE FROM ${prefixe}piecejointe WHERE md5='' OR idpiecejointe=''";
        execSql($sql);

        $sql="SELECT idpiecejointe FROM ${prefixe}piecejointe";
        $res=execSql($sql);
        $data=chargeMat($res);
        for($i=0;$i<count($data);$i++) {
                $idpiecejointe=$data[$i][0];
                $sql="SELECT idpiecejointe FROM ${prefixe}messageries WHERE idpiecejointe='$idpiecejointe'";
                $res2=execSql($sql);
                $data2=chargeMat($res2);
                if (count($data2) == 0) {
                        $sql="DELETE FROM ${prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe'";
                        execSql($sql);
                }
        }


        $path="../data/fichiersj";
        $O = dir($path);
        if(!is_object($O))
        return false;
        while($file = $O -> read()) {
            if($file != '.' && $file != '..') {
                if(is_file($path.'/'.$file)) {
                        $sql="SELECT * FROM ${prefixe}piecejointe WHERE md5='$file'";
                        $res=execSql($sql);
                        $data=chargeMat($res);
                        if (count($data) == 0) {
                                unlink("../data/fichiersj/$file");
                        }
                }
            }
        }
        // !!!! il faut bien appeler 2 fois la méthode close() !!!
        $O -> close();
        if (SERVEURTYPE != "SERVEURFREE") { $O -> close(); }
        return true;
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


function htaccessRacine() {
	include_once("../common/config.inc.php");
	
	$text="";

	if (SERVEURTYPE == "SERVEUROVH") {
		//ajouter dans le fichier .htaccess 
		$text.="SetEnv REGISTER_GLOBALS 0\n";
		$text.="SetEnv ZEND_OPTIMIZER 1\n";
		$text.="SetEnv MAGIC_QUOTES 1\n";
		$text.="SetEnv PHP_VER 5\n";
		$text.="SetEnv SESSION_USE_TRANS_SID 0\n";
		$text.="ErrorDocument 404 /".ECOLE."/err404.php\n";
		$text.="ErrorDocument 403 /".ECOLE."/err403.php\n";
	}
	
	if (SERVEURTYPE == "SERVEURFREE") {
		$text.="#HTACCESS\n";
		$text.="php 1\n";
		$text.="ErrorDocument 404 /".ECOLE."/err404.php\n";
		$text.="ErrorDocument 403 /".ECOLE."/err403.php\n";
	}


	if (SERVEURTYPE == "SERVEURONLINE") {
		$text.="ErrorDocument 404 /".ECOLE."/err404.php\n";
		$text.="ErrorDocument 403 /".ECOLE."/err403.php\n";
		$text.="AddType application/x-httpd-php5 .php\n";
	}

	if (SERVEURTYPE == "SERVEURAUTRENET") {
		$text.="ErrorDocument 404 /".ECOLE."/err404.php\n";
		$text.="ErrorDocument 403 /".ECOLE."/err403.php\n";
		$text.="php_flag register_globals off\n";
		$text.="php_value upload_max_filesize 8000000\n";
		$text.="php_value post_max_size 10000000\n";
	}

	if (SERVEURTYPE == "SERVEURKWARTZ") {
		$text.="ErrorDocument 404 /".ECOLE."/err404.php\n";
		$text.="ErrorDocument 403 /".ECOLE."/err403.php\n";
		$text.="php_flag register_globals off\n";
		$text.="php_value upload_max_filesize 8000000\n";
	}

	if (SERVEURTYPE == "APACHE2TRIAD") {
		$text.="ErrorDocument 404 /".ECOLE."/err404.php\n";
		$text.="ErrorDocument 403 /".ECOLE."/err403.php\n";
		$text.="php_flag register_globals off\n";
		$text.='php_value upload_max_filesize 8000000'."\n";
		$text.='php_value post_max_size 10000000'."\n";
	}

	if (SERVEURTYPE == "SERVEUROXITO") {
		$text.="ErrorDocument 404 /".ECOLE."/err404.php\n";
		$text.="ErrorDocument 403 /".ECOLE."/err403.php\n";
	}

	if (SERVEURTYPE == "SERVEUR1AND1") {
		$text.="ErrorDocument 404 /".ECOLE."/err404.php\n";
		$text.="ErrorDocument 403 /".ECOLE."/err403.php\n";
		$text.="AddType x-mapp-php5 .php\n";
		$text2="register_globals = off\n";
		$text2.="allow_url_fopen = on\n";
		@unlink("../../php.ini");
		$fp = fopen("../../php.ini", "w");
		fwrite($fp,$text2);
		fclose($fp);

		@unlink("../php.ini");
		$fp = fopen("../php.ini", "w");
		fwrite($fp,$text2);
		fclose($fp);

		@unlink("../../agenda/phenix/php.ini");
		$fp = fopen("../../agenda/phenix/php.ini", "w");
		fwrite($fp,$text2);
		fclose($fp);

		@unlink("../../forum/php.ini");
		$fp = fopen("../../forum/php.ini", "w");
		fwrite($fp,$text2);
		fclose($fp);

		@unlink("../../messenger/gestion/php.ini");
		$fp = fopen("../../messenger/gestion/php.ini", "w");
		fwrite($fp,$text2);
		fclose($fp);
	}



	@unlink("../../.htaccess");
	$fp = fopen("../../.htaccess", "w");
	fwrite($fp,$text);
	fclose($fp);
	
	return true;
}


function htaccess_specif($rep) {
	$text="<Files \"fic_news_defil_menuscolaire.txt\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>\n";
	$text.="\n";
	$text.="<Files \"fic_news_defil_menuadmin.txt\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>\n";
	$text.="\n";
	$text.="<Files \"error.log\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>\n";
	$text.="\n";
	$text.="<Files \"fic_probleme.txt\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>\n";
	$text.="\n";
	$text.="<Files \"fic_question_faq.txt\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>\n";
	$text.="\n";
	$text.="<Files \"fic_opinion.txt\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>\n";
	$text.="\n";
	$text.="<Files \"erreurs.log\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>\n";
	$text.="\n";	
	$text.="<Files \"bug_report.txt\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>\n";

	@unlink("$rep/.htaccess");
	$fp = fopen("$rep/.htaccess", "w");
	fwrite($fp,$text);
	fclose($fp);
	return true;
}

// -------------------------------------------------------------------
function ajout_patch($idpatch,$info) {
	global $cnx;
        global $prefixe;
	$date=dateDMY2();
	$time=dateHIS();
	$info=MyAddSlashes($info);
	$idpatch=MyAddSlashes($idpatch);
 	$sql="INSERT INTO ${prefixe}patch (idpatch,date,heure,info) VALUES ('$idpatch','$date','$time','$info')";
    	execSql($sql);
}

function list_patch() {
	global $cnx;
        global $prefixe;
 	$sql="SELECT idpatch,date,heure,info FROM ${prefixe}patch ORDER BY idpatch DESC ";
    	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data ;
}

function info_patch($idpatch) {
	global $cnx;
	global $prefixe;
	$idpatch=MyAddSlashes($idpatch);
 	$sql="SELECT idpatch,info FROM ${prefixe}patch WHERE idpatch='$idpatch' ";
    	$res=execSql($sql);
    	$data=chargeMat($res);
    	return $data[0][1] ;
}

function verifpatchinstall($idpatch) {
	global $cnx;
	global $prefixe;
	$idpatch=MyAddSlashes($idpatch);
	$idpatch=preg_replace('/.zip/i','',$idpatch);
 	$sql="SELECT idpatch,info FROM ${prefixe}patch WHERE idpatch='$idpatch' ";
    	$res=execSql($sql);
	$data=chargeMat($res);
    	unset($sql);
    	if (count($data) > 0) {
    		return true;
    	}else{
    		return false;
    	}
}

function verif_affectation() {

// 0 corrigé
// 1 c'est ok
// 2 erreur
// 3 erreur avec ?

	global $cnx;
        global $prefixe;
	$sql="SELECT ordre_affichage,code_matiere,code_prof,code_classe,coef,code_groupe,langue,avec_sous_matiere FROM ${prefixe}affectations WHERE langue IS NULL ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		for($i=0;$i<count($data);$i++) {
			$sql="UPDATE ${prefixe}affectations SET langue=' ' WHERE ordre_affichage='".$data[$i][0]."'  AND code_matiere='".$data[$i][1]."' AND code_prof='".$data[$i][2]."' AND code_classe='".$data[$i][3]."' AND coef='".$data[$i][4]."' AND code_groupe='".$data[$i][5]."' AND avec_sous_matiere='".$data[$i][7]."'";
			$cr=execSql($sql);
			if (!$cr) {
				return 2;
			}
		}
	}else{
		return 1;
	}
	return 0;
}

function supprimer_patch($idpatch) {
    	global $cnx;
	global $prefixe;
	$idpatch=MyAddSlashes($idpatch);
	$sql="DELETE FROM ${prefixe}patch WHERE  idpatch='$idpatch' ";
    	$ins=execSql($sql);
    	unset($sql);
}

function supprimerTousLesPatchs() {
    	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}patch ";
    	$ins=execSql($sql);
    	unset($sql);
}

function updatesql($sql) {
	global $cnx;
        global $prefixe;
	return(execSql($sql));
}


function restodump($sql) {
	global $cnx;
    	global $prefixe;
	$cr=execSql($sql);
	if ((!$cr) && (DEV == 1)) {
		print "<strong>Erreur :</strong> $sql";		
		print "<br />";
	}
	unset($sql);
	return $cr;
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


function delfile($str)  { 
    foreach(glob($str) as $fn) { 
	    unlink($fn); 
    } 
} 



function optimize_mysql() {
	global $cnx;
	global $prefixe;
	$sql = "OPTIMIZE TABLE 
		${prefixe}absences, 
		${prefixe}abs_rtd_aucun, 
		${prefixe}affectations, 
		${prefixe}alerteabsrtd, 
		${prefixe}avertissement, 
		${prefixe}b2ia2notation, 
		${prefixe}blacklist, 
		${prefixe}brevetcoef, 
		${prefixe}brevetconfig,
		${prefixe}brevetnote,
		${prefixe}bug, 
		${prefixe}bulletin_direction_com, 
		${prefixe}bulletin_profp_com, 
		${prefixe}bulletin_prof_com, 
		${prefixe}bulletin_prof_param, 
		${prefixe}bulletin_scolaire_com, 
		${prefixe}calendrier_dst,
		${prefixe}calend_evenement, 
		${prefixe}carnet_competence, 
		${prefixe}carnet_descriptif, 
		${prefixe}carnet_evaluation,
		${prefixe}carnet_section,
		${prefixe}carnet_suivi,
		${prefixe}checksum, 
		${prefixe}circulaire, 
		${prefixe}classes, 
		${prefixe}code_postal, 
		${prefixe}comptaconfig,
		${prefixe}comptaconfigmodele, 
		${prefixe}comptaversement, 
		${prefixe}config_creneau, 
		${prefixe}config_note_usa,
		${prefixe}config_rtd_abs, 
		${prefixe}date_trimestrielle, 
		${prefixe}delegue, 
		${prefixe}demande_dst,
		${prefixe}devoir_scolaire, 
		${prefixe}diaporama, 
		${prefixe}discipline_prof, 
		${prefixe}discipline_retenue,
		${prefixe}discipline_sanction,
		${prefixe}dispenses, 
		${prefixe}edt_enseignement,
		${prefixe}edt_seances,
		${prefixe}eleves,
		${prefixe}elevessansclasse, 
		${prefixe}emploi, 
		${prefixe}entretieneleve, 
		${prefixe}etude_affect, 
		${prefixe}etude_param,
		${prefixe}ficheliaison,
		${prefixe}fiche_info,
		${prefixe}fiche_med,
		${prefixe}fichier, 
		${prefixe}gep_classe,
		${prefixe}groupes,
		${prefixe}history_bulletin,
		${prefixe}history_cmd,
		${prefixe}history_periode,
		${prefixe}info_ecole,
		${prefixe}ip_timeout,
		${prefixe}mail_grp,
		${prefixe}matieres, 
		${prefixe}messageries, 
		${prefixe}messagerie_envoyer, 
		${prefixe}messagerie_repertoire, 
		${prefixe}news_admin, 
		${prefixe}news_prof_p, 
		${prefixe}notes,
		${prefixe}notes_scolaire,
		${prefixe}notes_scolaire_param,
		${prefixe}parametrage,
		${prefixe}patch, 
		${prefixe}personnel, 
		${prefixe}piecejointe,
		${prefixe}planclasse, 
		${prefixe}preinscription_eleves, 
		${prefixe}present,
		${prefixe}prof_p, 
		${prefixe}px_admin, 
		${prefixe}px_agenda, 
		${prefixe}px_agenda_concerne, 
		${prefixe}px_agenda_export, 
		${prefixe}px_calepin, 
		${prefixe}px_calepin_appartient, 
		${prefixe}px_calepin_groupe,
		${prefixe}px_configuration,
		${prefixe}px_couleurs, 
		${prefixe}px_droit,
		${prefixe}px_emplacement,
		${prefixe}px_evenement,
		${prefixe}px_favoris, 
		${prefixe}px_favoris_groupe, 
		${prefixe}px_fetes,
		${prefixe}px_global_groupe,
		${prefixe}px_groupe_util, 
		${prefixe}px_horoscope, 
		${prefixe}px_information,
		${prefixe}px_libelle,
		${prefixe}px_memo,
		${prefixe}px_meteo, 
		${prefixe}px_mods,
		${prefixe}px_planning_affecte,
		${prefixe}px_planning_affichage,
		${prefixe}px_planning_partage,
		${prefixe}px_rss_reader, 
		${prefixe}px_sid,
		${prefixe}px_timezone,
		${prefixe}px_tria2phenix,
		${prefixe}px_utilisateur,
		${prefixe}reglement, 
		${prefixe}resa_liste, 
		${prefixe}resa_matos,
		${prefixe}retards,
		${prefixe}rss, 
		${prefixe}rssgen, 
		${prefixe}sanctions,
		${prefixe}stage_activite,
		${prefixe}stage_date,
		${prefixe}stage_eleve,
		${prefixe}stage_entreprise,
		${prefixe}statconxparheure, 
		${prefixe}statdebit,
		${prefixe}statexecution, 
		${prefixe}statnavigateur,
		${prefixe}statscreen, 
		${prefixe}statutilisateur,
		${prefixe}stat_trace,
		${prefixe}types_personnel,
		${prefixe}type_category, 
		${prefixe}type_nb_sanction, 
		${prefixe}type_sanction, 
		${prefixe}vacataires,
		${prefixe}vacation_config";

	$res=execSql($sql);
	if ($res) {
		return 1;
	}else{
		return 3;
	}
}
function optimize_pgsql() {
	$sql="VACUUM";
	execSql($sql);
	$sql="VACUUM ANALYSE";
	execSql($sql);
	return 1;
}

function analyse_page() {
	global $cnx;
    	global $prefixe;
	$sql="SELECT file,time_max,time_min FROM ${prefixe}statexecution ORDER BY time_max DESC";
	$res=execSql($sql);
    	$data=ChargeMat($res);
	return $data;
}

function requetesql($sql) {
        global $cnx;
        global $prefixe;
        $res=execSql(trim($sql));
	if ($res) {
		print "<b>Requête réussie</b>";
		if (preg_match('/select/i',$sql)) {
			$data=ChargeMat($res);
			print_r($data);
		}
	}else {
		print "<b>Erreur sur votre requête SQL</b>";
	}
        unset($sql);
}


function verif_html_rep($rep) {
	if (is_dir($rep)) {
		$text="<html><body OnLoad=\"location.href='../../index.html';\" ></body></html>";
		@unlink("$rep/index.html");
		$fp = fopen("$rep/index.html","w");
		fwrite($fp,$text);
		fclose($fp);
	}
}

function verif_php_rep($rep) {
	if (is_dir($rep)) {
		$text="<html><body OnLoad=\"location.href='../index1.php';\" ></body></html>";
		@unlink("$rep/index.php");
		@unlink("$rep/index.html");
		$fp = fopen("$rep/index.php","w");
		fwrite($fp,$text);
		fclose($fp);
	}
}

function verif_repertoire() {
	if (is_dir('../data/patch/000-MD5')) { recursive_delete('../data/patch/000-MD5'); }
	if (is_dir('../data/patch/000-SMS')) { recursive_delete('../data/patch/000-SMS'); }
	if (is_dir('../data/patch/000-PRODUCTID')) { recursive_delete('../data/patch/000-PRODUCTID'); }
	if (!is_dir('../data/image_banniere')) { $cr=mkdir('../data/image_banniere'); if ($cr != 1) { return 3;  } }

	verif_php_rep("../data");
	verif_php_rep("../messenger");
	verif_html_rep("../data/image_banniere");
	verif_html_rep("../data/image_diapo");
	verif_html_rep("../data/image_pers");
	verif_html_rep("../data/image_eleve");
	verif_html_rep("../data/rss");
	verif_html_rep("../data/recherche");
	verif_html_rep("../data/patch");
	verif_html_rep("../data/menuscolaire");
	verif_html_rep("../data/menuadmin");
	verif_html_rep("../data/menuprof");
	verif_html_rep("../data/menueleve");
	verif_html_rep("../data/menuparent");
	verif_html_rep("../data/audio");
	verif_html_rep("../audio");
	robottxt(); // verif robot.txt
	@unlink("../data/fic_pass.txt");
	@unlink("../data/fic_pass2.txt");
	@unlink("./patch.zip");
	delfile("./*.tmp");
	delfile("../*.tmp");

	return 1;
}

function optimise_repertoire() {
	recursive_delete('../data/pdf_abs');
	mkdir('../data/pdf_abs');
	htaccess("../data/pdf_abs");
	recursive_delete('../data/pdf_certif');
	mkdir('../data/pdf_certif');
	htaccess("../data/pdf_certif");
	recursive_delete('../data/tmp');
	mkdir('../data/tmp');
	recursive_delete('../data/tmp/photos');
	mkdir('../data/tmp/photos');
	recursive_delete('../data/sauvegarde');
	mkdir('../data/sauvegarde');	
	htaccess("../data/sauvegarde");
	recursive_delete('../data/bull_xls');
	mkdir('../data/bull_xls');
	htaccess('../data/bull_xls');
	recursive_delete('../data/DevoirScolaire');
	mkdir('../data/DevoirScolaire');
	htaccess('../data/DevoirScolaire');
	recursive_delete('../data/edt');
	mkdir('../data/edt');
	htaccess('../data/edt');
	recursive_delete('../data/fichier_ASCII');
	mkdir('../data/fichier_ASCII');
	htaccess('../data/fichier_ASCII');
	recursive_delete('../data/patch');
	mkdir('../data/patch');
	htaccess('../data/patch');
	recursive_delete('../data/pdf_abs');
	mkdir('../data/pdf_abs');
	htaccess('../data/pdf_abs');
	recursive_delete('../data/pdf_bull');
	mkdir('../data/pdf_bull');
	htaccess('../data/pdf_bull');
	recursive_delete('../data/pdf_quantification');
	mkdir('../data/pdf_quantification');
	recursive_delete('../data/recherche');
	mkdir('../data/recherche');
	htaccess('../data/recherche');
	recursive_delete('../data/pdf_depot');
	mkdir('../data/pdf_depot');
	htaccess('../data/pdf_depot');
	
	return 1;
}


function updateMd5($md5,$fichier) {
	global $cnx;
	global $prefixe;
	$fichier=MyAddSlashes($fichier);
	$sql="SELECT fichier FROM ${prefixe}checksum WHERE fichier='$fichier' ";
	$res=execSql($sql);
    	$data=ChargeMat($res);
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}checksum SET sum='$md5', etat='0' WHERE fichier='$fichier'";
		execSql($sql);
	}else{
		$sql="INSERT INTO ${prefixe}checksum (sum,fichier,etat) VALUES ('$md5','$fichier','0')";
    		execSql($sql);
	}

}
function history_cmd($user_cmd,$cmd,$com){
	global $cnx;
	global $prefixe;
	$time_cmd=dateHIS();
	$date_cmd=dateDMY2();
	$com=MyAddSlashes($com);
	$user_cmd=MyAddSlashes($user_cmd);
	$cmd=MyAddSlashes($cmd);
	$sql="INSERT INTO ${prefixe}history_cmd (time_cmd,date_cmd,user_cmd,cmd,commentaire) VALUES ('$time_cmd','$date_cmd','$user_cmd','$cmd','$com')";
	$com=strip_tags($com);
	$com=preg_replace('/\'/',"\'",$com);
	$info=$user_cmd."##".$cmd."##".$com;
	acceslog($info);
	return(execSql($sql));
}

function filesize_format($bytes) {
  $bytes=(float)$bytes;
  if ($bytes<1024){
  $numero=number_format($bytes, 0, ',', '.')." Byte";
  return $numero;
  }
  if ($bytes<1048576){
     $numero=number_format($bytes/1024, 2, ',', '.')." KByte";
  return $numero;
  }
  if ($bytes>=1048576){
     $numero=number_format($bytes/1048576, 2, ',', '.')." MByte";
  return $numero;
  }
}

function human_readable( $size )
{
   $count = 0;
   $format = array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
   while(($size/1024)>1 && $count<8)
   {
       $size=$size/1024;
       $count++;
   }
   if( $size < 10 ) $decimals = 1;
   else $decimals = 0;
   $return = number_format($size,$decimals,'.',' ')." ".$format[$count];
   return $return;
}


function verif_fichier($repadmin) {
	global $cnx;
	global $prefixe;

	if (file_exists("./data.triade")) {
		$fic=fopen("./data.triade","r");
		$lines=file ("./data.triade");
		foreach ($lines as $line_num => $line) {
			if(preg_match('/:/',$line)){
				list($fichier,$null)= split (":", $line, 2);
				if (file_exists($fichier))  { unlink($fichier); }
			}	
		}
		fclose($fic);
		unlink("./data.triade");
	}


	$sql="SELECT sum,etat,fichier FROM ${prefixe}checksum";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		for($i=0;$i<count($data);$i++) {
			$sum=trim($data[$i][0]);
			$fichierorig=$data[$i][2];
			$fichier=trim($data[$i][2]);
			$fichier=preg_replace('/\.\/admin/',"./$repadmin",$fichier);
			$fichier=preg_replace('/\.\//',"../",$fichier);
			if (file_exists($fichier)) {
				$val=md5_file($fichier);
			}else{
				$val=null;
			}
			$fichierorig=MyAddSlashes($fichierorig);
			if ($val == $sum) {
				$sql="UPDATE ${prefixe}checksum SET etat='0' WHERE fichier='$fichierorig'";
				execSql($sql);
			}else{
				// voir aussi recup_liste_check2.php, db_triade-admin.php - serveur
				if ( (preg_match('/\.log$/i',$fichier)) || 
					(preg_match('/css$/i',$fichier)) ||  
					(preg_match('/^.*\/common\/.*$/i',$fichier)) ||  
					(preg_match('/htaccess$/i',$fichier))  ||
					(preg_match('/htpasswd$/i',$fichier))  ||
					(preg_match('/intra-msn-triade.zip$/i',$value))  ||
					(preg_match('/^.*\/installation\/.*$/i',$fichier))  ||
					(preg_match('/^.*\/data\/.*$/i',$fichier)) ||
					(preg_match('/^.*\/moodle\/.*$/i',$fichier)) ||
					(preg_match('/^.*\/dokeos\/.*$/i',$fichier)) ||
					(preg_match('/^.*\/pmb\/.*$/i',$fichier)) ||
					(preg_match('/^.*\/cache\/.*$/i',$fichier)) ||
					(preg_match('/^.*\/librairie_js\/menudepart.js$/i',$fichier)) ||
					(preg_match('/^.*\/librairie_js\/menuadmin.js$/i',$fichier)) ||
					(preg_match('/^.*\/librairie_js\/menueleve.js$/i',$fichier)) ||
					(preg_match('/^.*\/librairie_js\/menuparent.js$/i',$fichier)) ||
					(preg_match('/^.*\/librairie_js\/menuprof.js$/i',$fichier)) ||
					(preg_match('/^.*\/librairie_js\/menuscolaire.js$/i',$fichier)) ||
					(preg_match('/^.*\/dump-dist.php$/i',$fichier)) ||
					(preg_match('/^.*\/robots.txt$/i',$fichier)) ||
					(preg_match('/^.*\/alert.triade$/i',$fichier)) ||
					(preg_match('/^.*\/triade.alert$/i',$fichier)) ||
					(preg_match('/^.*\/lib_error.php$/i',$fichier)) ||
					(preg_match('/^.*\/conf_error.php$/i',$fichier)) ||
					(preg_match('/^.*\/log/.*$/i',$fichier)) ||
					(preg_match('/^.*\/messenger\/IntraMessengerClient\/options.ini$/i',$fichier)) ||
					(preg_match('/^.*\/messenger\/public\/intra-msn-triade.zip$/i',$fichier)) ||
					(preg_match('/^.*\/livreor\/identif\/logins.php$/i',$fichier))
				) {
					$sql="DELETE FROM ${prefixe}checksum WHERE fichier='$fichierorig'";
					execSql($sql);
				}else{
					$sql="UPDATE ${prefixe}checksum SET etat='1' WHERE fichier='$fichierorig'";
					execSql($sql);
				}
			}
		}
		$sql="SELECT etat FROM ${prefixe}checksum WHERE etat='1' LIMIT 1 ";
		$res=execSql($sql);
		$data=ChargeMat($res);
		if (count($data) > 0) {
		    return 3;
		}else{
		    return 1;
		}
	}else{
		return 1;
	}

}

function listeFichierMd5() {
	global $cnx;
	global $prefixe;
	$sql="SELECT sum,etat,fichier FROM ${prefixe}checksum WHERE etat='1' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	return $data;
}

function acceslog($message) {
	$fichier="../data/install_log/access.log";
	if (filesize($fichier) >= 1999999) {
		@unlink("$fichier.old");
		rename($fichier,"$fichier.old");
	}
	$date=dateDMY();
	$heure=dateHIS();

	$ret="\n";
	if (PHP_OS == "WINNT") {  $ret="\r\n"; }

    	$texte="$date|$heure|Administrateur|$message$ret";
    	$fic=fopen($fichier,"a+");
    	fwrite($fic,$texte);
    	fclose($fic);
}

function vider_checksum() {
	global $cnx;
        global $prefixe;
	$sql="TRUNCATE TABLE ${prefixe}checksum";
	execSql($sql);
}

function robottxt(){
	include_once('../common/lib_ecole.php');
	$repecole=REPECOLE;
	$text = 'User-agent: Mediapartners-Google'."\n";
	$text.= 'Disallow: /'.$repecole.'/admin/'."\n";
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
	@unlink("../robots.txt");
	@unlink("../../robots.txt");
	$fp=fopen("../../robots.txt","w");
	fwrite($fp,$text);
	fclose($fp);

}

function verif_config() {
	$text="<?php\n";
	$text.="error_reporting(0);\n";
	$text.="?>\n";
	@unlink("librairie_php/lib_error.php");
	$fp = fopen("librairie_php/lib_error.php", "w");
	fwrite($fp,$text);
	fclose($fp);
	@unlink("../librairie_php/lib_error.php");
	$fp = fopen("../librairie_php/lib_error.php", "w");
	fwrite($fp,$text);
	fclose($fp);
	$text="<?php\n";
	$text.="\$ERROR=\"false\";\n";
	$text.="global \$ERROR;\n";
	$text.="?>\n";
	@unlink("../librairie_php/conf_error.php");
	$fp = fopen("../librairie_php/conf_error.php", "w");
	fwrite($fp,$text);
	fclose($fp);
	@unlink("./librairie_php/conf_error.php");
	$fp = fopen("./librairie_php/conf_error.php", "w");
	fwrite($fp,$text);
	fclose($fp);

	return 1;

}


function ValideMail($email) {
    if ($email == "") { return 0; }
	$mail_valide =  preg_match('/([A-Za-z0-9]|-|_|\.)*@([A-Za-z0-9]|-|_|\.)*\.([A-Za-z0-9]|-|_|\.)*/',$email);
    if ($mail_valide) {
	    return 1;
    }else{
	    alertJs("ATTENTION, votre email : $email n'est pas valide. \\n \\n Equipe Triade.");
	    return 0; 
    }
}  


function supp_rep_patch() {
	recursive_delete('../data/patch');
	mkdir("../data/patch");
}

function couleurFont($graphe) {
	if ($graphe == 1) { return "#CCCCCC"; }

}


function verifDbb() {
	global $cnx;
	global $prefixe;

	@unlink("../data/dump/structure.sql");

	$host=HOST;
	$base=DB;
	$login=USER;
	$password=PWD;

	$bdd=$base;	
	@mysqli_connect($host, $login, $password);
	
	$sql="SHOW TABLES FROM `$base`";
	$result=execSql($sql);

	/* Tant qu'il y a des tables */
	while ($row = mysqli_fetch_row($result))
	{
		//$lignesql.="\n#\n# Table `".$row[0]."`\n#\n";
		//$lignesql.= "DROP TABLE IF EXISTS `$row[0]`;\n";
		/* Se connecte à la base à sauvegarder */
		mysqli_select_db($bdd);
		/* Enregistre sa structure */
		$req = mysqli_query("SHOW CREATE TABLE ".$row[0]);
		if (preg_match('/statutilisateur/i',$row[0])) {	continue; }
		$res = mysqli_fetch_array($req);
		$partern="/$prefixe/";
		$res[1]=preg_replace($partern,'',$res[1]);
		$res[1]=preg_replace('/ENGINE=.*$/','',$res[1]);
		$res[1]=preg_replace('/collate [A-Za-z0-9_]+/',' ',$res[1]);
		$res[1]=preg_replace('/[ \t]/','',$res[1]);
		$res[1]=strtolower($res[1]);
		$lignesql.= $res[1].";\n\n";
		/* Compteur du nombre de tables */
		$NbTables ++;
	}		
	@mysqli_close();
	
	$fp = fopen("../data/dump/structure.sql", "w");
	fwrite($fp,$lignesql);
	fclose($fp);

	return md5_file("../data/dump/structure.sql");

}

function verifMessagerie() {
	global $cnx;
	global $prefixe;
	$sql="DELETE FROM ${prefixe}piecejointe WHERE nom='application/octet-stream'";
	execSql($sql);
	return 1;
}	
	
	


function verifAgenda() {
	global $cnx;
	global $prefixe;
	$sql="SELECT idtriade,idphenix,membre  FROM  ${prefixe}px_tria2phenix";
	$res=execSql($sql);
	$data=ChargeMat($res);
	for($i=0;$i<count($data);$i++) {
		$idtriade=$data[$i][0];
		$idphenix=$data[$i][1];
		$membre=$data[$i][2];
		$utillogin="$membre$idtriade";
		$sql="SELECT util_id FROM  ${prefixe}px_utilisateur WHERE util_login='$utillogin' AND util_id='$idphenix' ";
		$res=execSql($sql);
		$data2=ChargeMat($res);
		if (count($data2) > 0) {
			continue;
		}else{
			$sql="DELETE FROM  ${prefixe}px_tria2phenix WHERE idtriade='$idtriade'";
			execSql($sql);
		}
	}
	return 1;
}



function protohttps() {
	if (HTTPS == "oui") {
		return 'https://';
	}else{
		return 'http://';
	}
}

function enr_googleAnalytics($idref) {
	global $cnx;
        global $prefixe;
	$sql="DELETE FROM ${prefixe}parametrage WHERE libelle='googleanalytic'";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}parametrage (libelle,text) VALUES ('googleanalytic','$idref')";
	execSql($sql);
}

function verifcomptegoogleanalytic() {
	global $cnx;
        global $prefixe;
	$sql="SELECT text FROM ${prefixe}parametrage WHERE libelle='googleanalytic' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 0) {
		return true;
	}else{
		return false;
	}
}

function modifpassedokeos($mp) {
	global $cnx;
	global $prefixe;
	$mp=md5($mp);
	$sql="UPDATE `user` SET password='$mp' WHERE user_id='1'";
	execSql($sql);
}

function modifpassemoodle($mp) {
	global $cnx;
	global $prefixe;
	$mp=md5($mp);
	$sql="UPDATE `mdl_user` SET password='$mp' WHERE id='2'";
	execSql($sql);
}

function intraMSN(){
	$file_reg="../messenger/im_setup.reg";
	@unlink("$file_reg");
	$url = "http://".$_SERVER['SERVER_NAME']."/".ECOLE."/messenger/";
	// http://localhost/triadev17/messenger/
	$reg_lang="FR";
	$data  = "Windows Registry Editor Version 5.00\n\n";
	$data .= "[HKEY_CURRENT_USER\Software\THe UDS\IM]\n";
	$data .= '"url"="' . $url . '"';
	$data .= "\n";
	$data .= '"lang"="' . $reg_lang . '"';
	$data .= "\n";
	file_put_contents($file_reg, $data);
	return 1;
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


function verifMoodle($host,$user,$pass,$db,$type) {
        global $cnx;
	global $prefixe;
	$sql="CREATE TABLE IF NOT EXISTS `mdl_config_plugins` (`id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,`plugin` varchar(100) NOT NULL DEFAULT 'core',`name` varchar(100) NOT NULL DEFAULT '',`value` text NOT NULL,PRIMARY KEY (`id`),UNIQUE KEY `mdl_confplug_plunam_uix` (`plugin`,`name`)) ENGINE=$type";
	execSql($sql);
	$sql="SELECT * FROM mdl_config_plugins WHERE plugin='auth/db' ";
        $res=execSql($sql);
        $data=ChargeMat($res);
	if (count($data) == 0) {
		$sql="INSERT INTO `mdl_config_plugins` (`plugin`, `name`, `value`) VALUES
			('auth/db', 'host', '$host'),
			('auth/db', 'type', 'mysql'),
			('auth/db', 'sybasequoting', '0'),
			('auth/db', 'name', '$db'),
			('auth/db', 'user', '$user'),
			('auth/db', 'pass', '$pass'),
			('auth/db', 'table', '${prefixe}eleves'),
			('auth/db', 'fielduser', 'nom'),
			('auth/db', 'fieldpass', 'mdp_moodle'),
			('auth/db', 'passtype', 'md5')";
		execSql($sql);
	}
}


function chiffrer_pass($pass, $salt)  {
  	$pass_cr = sha1(sha1($pass . md5($salt)));
  	return $pass_cr;
}

function random($nb_char) {
	$ret = "";
  	$user_ramdom_key = "(aLABbC0cEd1[eDf2FghR3ij4kYXQl5Um-OPn6pVq7rJs8*tuW9I+vGw@xHTy&#)K]Z%§!M_S";
  	srand((double)microtime()*time());
  	for($i = 0; $i < $nb_char; $i++) {
    		$ret .= $user_ramdom_key[rand()%strlen($user_ramdom_key)];
  	}
  	return $ret;
} 

function modifPasseAdminIntraMSN($pwd) {
	global $cnx;
	global $prefixe;
	$id_admin='1';
	$adm_salt = random(20);
  	$pwd = chiffrer_pass($pwd, $adm_salt);
  	//
  	$requete  = " UPDATE " . $prefixe . "im_ADM_ADMINACP ";
  	$requete .= " SET ADM_PASSWORD = '" . $pwd . "', ";
  	$requete .= " ADM_SALT = '" . $adm_salt . "', ";
  	$requete .= " ADM_DATE_PASSWORD = CURDATE() ";
  	$requete .= " WHERE ID_ADMIN = " . $id_admin . " ";
	$requete .= " limit 1 ";
	execSql($requete);
}

function modifPasseAdminMoodle($pwd) {
	global $cnx;
        global $prefixe;
	$sql="UPDATE mdl_user SET password=MD5($pwd) WHERE username='administrateur'";
	execSql($requete);
}


?>
