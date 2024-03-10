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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Importation d'un fichier" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");

$fichier=$_FILES["fichier1"]["name"];
$type=$_FILES["fichier1"]["type"];
$tmp_name=$_FILES["fichier1"]["tmp_name"];

//$size=$_FILES["fichier1"]["size"];
if ( (!empty($fichier)) && (($type == "application/octet-stream" ) || ($type == "application/vnd.ms-excel" ))) {
	move_uploaded_file($tmp_name,"data/fichier_gep/$fichier");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/traitement.xls");
	@unlink("data/fichier_gep/$fichier");
	$fic_xls="data/fichier_gep/traitement.xls";
	$typefichier="excel";
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
//	$data->setOutputEncoding('CP1251');
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);

	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
		//	print $data->sheets[0]['cellsInfo'][$i][$j];
		//	print $data->sheets[0]['cellsInfo'][$i][$j]['raw'];
		//	print $data->sheets[0]['cellsInfo'][$i][$j]['colspan'];
		//	print $data->sheets[0]['cellsInfo'][$i][$j]['rowspan'];
		// 	print $data->sheets[0]['cellsInfo'][$i][$j]['type'];
			if ($i == 1) {	continue; }
			$donnee=$data->sheets[0]['cells'][$i][3];
			$tab11[$donnee]=$donnee;
		}
	}

	
		 if ($_POST["vide_eleve"] == "oui") { 
			 $cnx=cnx();
			 purge_element_eleve(); 
			 if (is_dir("./data/archive")) {
				 nettoyage_repertoire("./data/archive");
			 }
			 Pgclose();
		 }


	 	@ksort($tab11);
	 	$ligne=0;
		print "<br><form method=post action='base_de_donne_importation413.php'>";
		print "<input type=hidden name='fichier' value=\"$fic_xls\">";
		print "<input type=hidden name='typefichier' value=\"$typefichier\">";
		print "<ul>".LANGIMP42."</ul>";
		print "<table border=1 align=center bgcolor='#FFFFFF'><tr bordercolor='#000000'>";
		$cnx=cnx();
		foreach ($tab11 as $clef => $b ) {
			if (strlen(trim($clef))) {
		    	?>
				<td><input type=text name='saisie_ref[]' value='<?php print $clef?>' size=10 onfocus='this.blur()'></td>
				<td><select name="saisie_classe[]">
			<?php
				print "<option value='-1' STYLE='color:#000066;background-color:#FCE4BA'>".LANGCHOIX."</option>";
				$id_classe=recherche_gep_classe($clef);
		    	if ($id_classe != "") {
			    	  $nom_classe=chercheClasse_nom($id_classe);
			    	  if ($nom_classe != "") {
	        		  		print "<option selected value='$id_classe'>$nom_classe</option>";
	        		  }
		    	}
			    	select_classe_gep(); // creation des options
			    ?>
				</select>
				</td>
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
	

	
?>
<br><br>
<table align=center><tr><td>
<script language=JavaScript> buttonMagicReactualise()</script>
<script language=JavaScript> buttonMagic("<?php print LANGBT26 ?>","creat_classe_gep.php","ajclass","width=450,height=150","")</script>
<input type=submit class='BUTTON' value='<?php print LANGCHER9 ?> >' onclick='attente()' ></center>
<br>
&nbsp;&nbsp;<?php print LANGbasededon21?>
</td></tr></table>
</form>
<br />
<br />
<?php
}else {
?>
<br />
<center> <font color=red><?php print LANGbasededon203?></font> <BR><BR>
<?php print "Le fichier doit être au format xls"?>
<br /><br />
<input type=button Value="<?php print LANGBT24 ?>" onclick="javascript:history.go(-1)" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br />
<br />
</center>
<?php
}
?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
