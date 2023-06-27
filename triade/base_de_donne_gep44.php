<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGbasededon2011?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");

$fichier=$_FILES["fichier1"]["name"];
$type=$_FILES["fichier1"]["type"];
$tmp_name=$_FILES["fichier1"]["tmp_name"];
//$size=$_FILES["fichier1"]["size"];
if ( (!empty($fichier)) && ($type == "application/octet-stream" ) && (strtoupper($fichier) == "F_WIND.DBF")  ) {
	move_uploaded_file($tmp_name,"data/fichier_gep/$fichier");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/F_wind.dbf");
	$fichier="F_wind.dbf";
	$fic_dbf="data/fichier_gep/$fichier";
	$fp=dbase_open($fic_dbf, 0);
	if(!$fp) {
	        echo "<center><br><p>".LANGBASE10_2." </p>";
	        echo "<input type=button Value='".LANGBT24."' onclick='javascript:history.go(-1)' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'><br /></center><br />";
	} else {
		$nblignes = dbase_numrecords($fp); //nombre  de ligne
	        $nbchamps = dbase_numfields($fp); //nombre de champs

	        if (@dbase_get_record_with_names($fp,1)) {
	                $temp = @dbase_get_record_with_names($fp,1);
	        } else {
	                echo "<center><p>".LANGBASE19." !<br>";
	                echo "<input type=button Value='".LANGBT24."' onclick='javascript:history.go(-1)' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'><br /></center><br />";
	        }
	        $nb=0;
	        $nbp=0;
	        for($k = 1; ($k < $nblignes+1); $k++) {
	        	  $nbp++;
	              $ligne = dbase_get_record($fp,$k);
	              $champs = dbase_get_record_with_names($fp,$k);
			unset($nom1);
			unset($prenom1);
			unset($naissance1);
			unset($civil1);
			unset($tel1);
			unset($rue1);
			unset($adres1);
			unset($ccp1);
			unset($ville1);
	              foreach($champs as $c => $v) {
			       		// $c --> le nom du champs
			       		// $v --> valeur du champs
	                       		// echo "$c : ". dbase_filter(trim($v))."<br>";
	               			$v=dbase_filter(trim($v));
					switch($c){
						case NUMIND:    $key=$v;	break;
			       			case AINOMU: 	$nom1=$v;	break;
				   		case AIPREN: 	$prenom1=$v;	break;
				   		case AINADD:	$naissance1=$v; break;
				   		case CIVILI:	$civil1=$v;     break;
				   		case AITELE:    $tel1=$v;       break;
				   		case AIADNV:	$rue1=$v;	break;
				   		case AIADDV:	$adres1=$v;	break;
				   		case AIADCP:	$ccp1=$v;	break;
						case AIADBD:	$ville1=$v;	break;
					}
					if (strlen(trim($prenom1))) {
						$tab11p["$nom1 $prenom1"]="$nom1#$prenom1#$naissance1#$civil1#$tel1#$rue1#$adres1#$ccp1#$ville1";
					}
	              }
	       }
	}
	@dbase_close($fp);
	ksort($tab11p);
	$ligne=0;
	print "<form method=post action='base_de_donne_gep444.php' name='formulaire' >";
	print "<br /><ul><font class='T1'>".LANGBASE10_2."</font></ul><br />";
	print "<table border=1 align=center ><tr bordercolor='#000000'>";
	$cnx=cnx();
	$nb=0;
	foreach ($tab11p as $clef => $b ) {
		if (strlen(trim($clef))) {

	?>
	<td class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<?php // print $b?>
	<input type=checkbox name='saisie_ref[]' value="<?php print $b ?>" >
	&nbsp;&nbsp;<font color="black"><?php print $clef?></font>&nbsp;&nbsp;</td>
    	<?php
			if ($ligne == 1) {
				print "</tr><tr bordercolor='#000000'>";
				$ligne=0;
			}else {
				$ligne++;
			}
		}
	}

	Pgclose();


	print "</table>";
	print "<br /><br /><center>";
	print LANGbasededon202;
	print "<br /><br />";
        ?>

        <SCRIPT LANGUAGE=JavaScript>
		nbcase="<?php print $nbp ?>";
		function tout() {
			for (i=0;i<=nbcase;i++) {
		                document.formulaire.elements[i].checked=true;

			}
		}
		</SCRIPT>


<table align=center><tr><td align="right" >
<input type=hidden name=nb value="<?php print $nbp ?>">
<input  type=button class='bouton2' value='<?php print "Tous les enseignants" ?>' onclick='tout()' >&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit class='BUTTON' value='<?php print LANGCHER9 ?> >' onclick='attente()' >&nbsp;&nbsp;&nbsp;
<br><br>
&nbsp;&nbsp;<?php print LANGbasededon21?>
</td></tr></table>
</form>
	<?php
?>
<br />
<br />
<?php
}else {
?>
<br />
<center> <font color=red><?php print LANGbasededon203?></font> <BR><BR>
<?php print LANGbasededon3-5 ?>
<br /><br />
<input type=button Value="<?php print LANGBT24 ?>" onclick="javascript:history.go(-1)" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br />
<br />
</center>
<?php
}
// suppression du fichier gep
@unlink("$fic_dbf");
?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
