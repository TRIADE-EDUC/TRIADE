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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<title>Triade - Compte de <?php print $_SESSION[nom]." ".$_SESSION[prenom] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE31?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
     <!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");

/*
foreach( $_POST[saisie_classe] as $clef => $valeur) {
	print $clef." ".$valeur."<br>";
}

foreach( $_POST[saisie_ref] as $clef => $valeur) {
	print $clef." ".$valeur."<br>";
}
*/
// function appele par la suite
function eclair($x , $y){
	if (!is_array($x) || !is_array($y)){
		echo "<br><br><center>".LANGbasededon41."</center>";
		print "<script>history.go(-1);</script>";
	}
	array_pad($x, count($y), "");
	array_pad($y, count($x), "");
	while(count($x) > 0){
		if (  current($x) == "choix") { array_shift($y); array_shift($x);continue; }
		$in=gep_classe(array_shift($x),array_shift($y));
		if ($in == 0) {
			alertJs(LANGbasededon42);
			print "<script>history.go(-1);</script>";
			break;
		}
	}
}

$cnx=cnx();
error($cnx);

// enregistrement dans la base de classe avec reference
// netoyage de la base gep_class;
vide_gep_classe();
eclair($_POST["saisie_classe"],$_POST["saisie_ref"]);
// fin d'enregistrement

if (file_exists("./data/fic_pass.txt")) {
	@unlink("./data/fic_pass.txt");  // destruction du fichier mot de passe
}

$nbelevedejaffecte=0;
$fic_dbf="data/fichier_gep/F_ele.dbf";
$phraseok="<br /><center>".LANGBASE11."<br /><br /></center><br />";
$fp=@dbase_open($fic_dbf, 0);
if(!$fp) {
	echo "<center><br><p>".LANGBASE10."</p>";
	echo "<input type=button Value='".LANGBT24."' onclick='javascript:history.go(-2)' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'><br /></center><br />";
} else {
	$nblignes = dbase_numrecords($fp); //nombre  de ligne
	$nbchamps = dbase_numfields($fp); //nombre de champs
	$nbeleve=0;
	$nbeleverreur=0;
	$nbeleveaffecte=0;
	$nbelevetotal=0;

	if (@dbase_get_record_with_names($fp,1)) {
		$temp = @dbase_get_record_with_names($fp,1);
	} else {
		echo "<center><p>".LANGBASE12."<br>";
		echo "<input type=button Value='".LANGBT24."' onclick='javascript:history.go(-2)' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'><br /></center><br />";
	}
	for($k = 1; ($k < $nblignes+1); $k++) {
	      $nbelevetotal=$nbelevetotal + 1;
	      $ligne = dbase_get_record($fp,$k);
	      $champs = dbase_get_record_with_names($fp,$k);
	      foreach($champs as $c => $v) {
//("ELENOM","ELEPRE","ELESEXE","ELEDATNAIS","ELEDOUBL","ELENONAT","ELEREG","DIVCOD","ETOCOD_EP", "ELEOPT1", "ELEOPT2", "ELEOPT3", "ELEOPT4", "ELEOPT5", "ELEOPT6", "ELEOPT7", "ELEOPT8", "ELEOPT9", "ELEOPT10", "ELEOPT11", "ELEOPT12");
			$v=dbase_filter(trim($v));
			//print "$c --> $v <br>";
			$v=addslashes($v);
	      		switch($c){
			 	case ELENOM: 	$nom1=$v;		break;
			 	case ELEPRE: 	$prenom1=$v;		break;
			 	case ELEDATNAIS:$date_naissance=$v;	break;
			 	case ELENONAT: 	$nonatele=$v;		break; // ajouter le numero nationnal
			 	case ELEOPT1: 	$lv1=$v;		break;
			 	case ELEOPT2: 	$lv2=$v;		break;
			 	case DIVCOD: 	$classe=$v;		break;
				case ELENAT:    $nationnalite=$v;	break; 
				case ERENO:	$numero_gep=$v;		break;
			 //	case ELEOPT3: 	$opt3=$v;		break;
			 //	case ELEOPT4: 	$opt4=$v;		break;
				}
	      	}

	      	if ($nationnalite == 100) { $nationnalite="Française";}


		$classe=recherche_gep_classe($classe);
		$passwd=passwd_random(); // creation du mot de passe
		$passwd_enr=$passwd;

		$passwd_eleve=passwd_random(); // creation du mot de passe
		$passwd_eleve_enr=$passwd_eleve;

		$numero_gep=preg_replace('/^0+/','',$numero_gep);

		if (strlen(trim($classe))) {

		

			// création du tableau de hash contenant les paramètres de la fonction create_eleve
			$params[ne]=            strtolower(trim($nom1));
			$params[pe]=            strtolower(trim($prenom1));
			$params[ce]=            $classe;
			$params[lv1]=           strtolower(trim($lv1));
			$params[lv2]=           strtolower(trim($lv2));
			$params[option]=        	"";
			// faire un module pour le regime valeur possible 0,1,2,3
			$params[regime]=        $regime;
			$params[naiss]=         $date_naissance;
			$params[nat]=           $nationnalite;
			$params[mdp]=           $passwd;
			$params[mdpeleve]=      $passwd_eleve;
			$params[nt]=            "";
			$params[pt]=		"";
			$params[nadr1]=        	"";
			$params[adr1]=        	"";
			$params[cpadr1]=      	"";
			$params[commadr1]=     	"";
			$params[nadr2]=        	"";
			$params[adr2]=         	"";
			$params[cpadr2]=       	"";
			$params[commadr2]=     	"";
			$params[tel]=          	"";
			$params[profp]=        	"";
			$params[telprofp]=     	"";
			$params[profm]=        	"";
			$params[telprofm]=     	"";
			$params[nomet]=        	"";
			$params[numet]=        	"";
			$params[cpet]=         	"";
			$params[commet]=    	"";
			$params[numero_eleve]=  $nonatele;
			$params[email]=		"";
			$params[numero_gep]=	$numero_gep;

			// nouvelle version de create_eleve()
			$ascii=0;
			$cr=@create_eleve($params,$ascii);
			if ($cr == 1) {
				$f_pass=fopen("./data/fic_pass.txt","a+");
				fwrite($f_pass,strtolower(trim($nom1)).";".strtolower(trim($prenom1)).";".$passwd_enr.";".$passwd_eleve_enr."<br />");
				fclose($f_pass);
				$nbeleveaffecte++;
			}
			if ($cr == -3) {
				$nbelevedejaffecte++;
			}
		}else{

			// divcod est null
			$nbeleverreur++;
			// création du tableau de hash contenant les paramètres de la fonction create_eleve
			$params[ne]=            strtolower(trim(addslashes($nom1)));
			$params[pe]=            strtolower(trim(addslashes($prenom1)));
			$params[lv1]=           strtolower(trim($lv1));
			$params[lv2]=           strtolower(trim($lv2));
			$params[option]=        	"";
			// faire un module pour le regime valeur possible 0,1,2,3
			$params[regime]=        $regime;
			$params[naiss]=         $date_naissance;
			$params[nat]=           $nationnalite;
			$params[mdp]=           $passwd;
			$params[mdpeleve]=      $passwd_eleve;
			$params[nt]=            "";
			$params[pt]=		"";
			$params[nadr1]=        	"";
			$params[adr1]=        	"";
			$params[cpadr1]=      	"";
			$params[commadr1]=     	"";
			$params[nadr2]=        	"";
			$params[adr2]=         	"";
			$params[cpadr2]=       	"";
			$params[commadr2]=     	"";
			$params[tel]=          	"";
			$params[profp]=        	"";
			$params[telprofp]=     	"";
			$params[profm]=        	"";
			$params[telprofm]=     	"";
			$params[nomet]=        	"";
			$params[numet]=        	"";
			$params[cpet]=         	"";
			$params[commet]=    	"";
			$params[numero_eleve]=  $nonatele;
			$params[email]=		"";
			$params[numero_gep]=	$numero_gep;

			// nouvelle create eleve sans classe
			$ascii=0;
			$cr=@create_eleve_sans_classe($params,$ascii);
			if ($cr == 1) {
				$f_pass=fopen("./data/fic_pass.txt","a+");
				fwrite($f_pass,strtolower(trim($nom1)).";".strtolower(trim($prenom1)).";".$passwd_enr.";".$passwd_eleve_enr."<br />");
				fclose($f_pass);
			}
			if ($cr == -3) {
				$nbelevedejaffecte++;
			}
		}
	}
}
@dbase_close($fp);

Pgclose();
// creation ou mise a jour du fichier log  avec prise en
$today= dateDMY();
$fichier_s=fopen("./".REPADMIN."/data/fic_opinion.txt","a+");
$donnee=fwrite($fichier_s,"<BR><BR>".LANGbasededon43."<font color=red>$today</font> ".LANGbasededon44."<font color=red> ".$_SESSION["nom"]." ".$_SESSION["prenom"]."</font> <BR>".LANGbasededon45."<font color=red> ".$_SESSION["membre"]." </font><BR> <B>".LANGbasededon46."</B> <font color=red> ".LANGbasededon47." </font> ".LANGbasededon48."<BR>  ".LANGbasededon49."<font color=red>".REPECOLE."</font>");
fclose($fichier_s);
?>

<br />
<ul>
- <?php print LANGBASE6?> : <?php print $nbelevetotal?><br>
- <?php print LANGBASE7?> : <?php print $nbeleveaffecte?><br>
- <?php print LANGBASE7bis ?> : <?php print $nbelevedejaffecte?><br>
- <?php print LANGBASE8?> : <?php print $nbeleverreur?><br><br>
- <?php print LANGBASE9?> <br /> (<?php print LANGBASE8bis ?>)<br /><br />
<?php
if (file_exists("./data/fic_pass.txt")) {
	history_cmd($_SESSION["nom"],"IMPORT","GEP fichier élève");
?>
<input type=button class=BUTTON value="<?php print LANGBT40?>" onclick="open('recupepw.php','_blank','')">
<?php } ?>
<br><br>
<font color=red size=2><?php print LANGBASE17?></font>
<br /><br /><br />
 <script language=JavaScript>buttonMagic("<?php print LANGBT41?>","acces2.php","_parent","","");</script>
<?php
if ($nbeleverreur > 0 ) {
?>
	<script language=JavaScript>buttonMagic("<?php print LANGBT42?>","elevesansclasse.php","_parent","","");</script>
<?php
}

// suppression du fichier gep
@unlink("$fic_dbf");
?>

<br>
</ul>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
