<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
 *   Site                 : http://www.triade-educ.com
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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(3000);
}
?>
<!-- /************************************************************
Last updated: 09.07.2002    par Taesch  Eric
*************************************************************/ -->
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGbasededoni91 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");
include_once("librairie_php/timezone.php");
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);

$cnx=cnx();

$reponse="<br /> <font class=T2><ul>Liste des élèves non trouvés <br /><br />";

if (file_exists("./data/fichier_gep/traitement.xls")) {
	$fic_xls="./data/fichier_gep/traitement.xls";
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
//	$data->setOutputEncoding('CP1251');
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);
/*
	1) N° MATRIC  
	2) GRILLE 
	3) CLASSE * 
	4) ANNÉE 
	5) NOM * 
	6) PRÉNOM * 
	7) SEXE  
	8) ADRESSE  
	9) CP  
	10) LOCALITÉ  
	11) ENT. COM.  
	12) DATE NAIS. * 
	13) LIEU NAIS.  
	14) NATIONALITE  
	15) ETABLIS_AN  
	16) ORIGINE  
	17) C. PHILOS. 
 	18) 2E L  
	19) INT /EXT  
	20) MIDI (CTD)  
	21) PERS. RESP. 
	22) PRÉNOM RESP.  
	23) D.P.  
	24) PROFESSION  
	25) CONJOINT  
	26) PROFES.  
	27) TÉL. PRIVÉ  
	28) TÉL.TRAV.P  
	29) GSM PÈRE  
	30) TÉL TRAV. M.  
	31) GSM MÈRE  
	32) RAPPORT 1È  
	33) RAPPORT 2È  
	34) INFO  
	35) DATE INSCR.  
	36) CASIER  
	37) RAPPORT 3È  
	38) PASSE  
	39) REMÉD  
	40) RAPPOT 1BI  
	41) RAPPORT 2B  
*
*/
		
	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		$classe=$data->sheets[0]['cells'][$i][3];
		$id_classe=recherche_gep_classe($classe);
		if ($id_classe != "") {	$nom_classe=chercheClasse_nom($id_classe); }


		$nom=strtolower(trim(addslashes($data->sheets[0]['cells'][$i][5])));
		$prenom=strtolower(trim(addslashes($data->sheets[0]['cells'][$i][6])));			
		$date_naissance=dateFormBase($data->sheets[0]['cells'][$i][12]);
		$cr=verifEleveExist($nom,$prenom,$date_naissance);
		if ($cr == "rien") {
			$reponse.="- <b>".strtoupper($nom)."</b> ".ucwords($prenom). "<br />";
			continue;
		}else{
			$ideleve=$cr;
		}

		
		$grille=$data->sheets[0]['cells'][$i][2];
		$sexe=$data->sheets[0]['cells'][$i][7];
		$philo=$data->sheets[0]['cells'][$i][17];
		$lv2=$data->sheets[0]['cells'][$i][18];

		if ($sexe == "M") {
			$key=$nom_classe."_garçon";
			$params[$key].=$ideleve.",";
		}
		if ($sexe == "F") {
			$key=$nom_classe."_fille";
			$params[$key].=$ideleve.",";
		}
		
		$key=$nom_classe."_".$philo;
		$params[$key].=$ideleve.",";

		$key=$nom_classe."_".$lv2;
		$params[$key].=$ideleve.",";

		$key=$nom_classe."_".$grille;
		$params[$key].=$ideleve.",";
		

	} 

	foreach($params as $key => $value)  {
		$value=preg_replace('/,$/','',$value);
		$params[comment]="";
		$params[liste_eleve]=$value;
		$params[nomgr]="$key";
		create_groupe($params,$anneeScolaire);
	//	print $key." $value<br>";
	}
 

	print $reponse;

	// suppression du fichier EXCEL
	 @unlink($fic_xls);


}else{
	print "fichier non existant";
}	




Pgclose();
?>

<br />
<ul>
<script language=JavaScript>buttonMagic("<?php print LANGBT41?>","acces2.php","_parent","","");</script><br />
<br><br />
</ul>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
