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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php include_once("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSMS7 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<script>
function valide() {
	document.form.create.disabled=false;
}
</script>
<!-- // debut form  -->
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");

if (LAN == "oui") {
	
	if (file_exists("./common/config-sms.php")) {
		include_once("./common/config-sms.php");
		$idsms=SMSKEY;
		$inc=GRAPH;
		$urlsms=SMSURL;

?>
		<form method="post" name="form" action="sms-mess2.php" ><br>
		<table border=0 align=center >
		<tr><td>
		<?php 
		if (isset($_GET["eid"])) {
			$cnx=cnx();
			$filtreSMS=config_param_visu('smsfiltre');
		
			$filtreSMS=$filtreSMS[0][0];
			$tel1=cherchetelportable1($_GET["eid"]); 
			$tel2=cherchetelportable2($_GET["eid"]); 
			$tel3=cherchetelEleve($_GET["eid"]); 
			$tel=cherchetel($_GET["eid"]);

			$tel4=cherchetelpere($_GET["eid"]); 
			$tel5=cherchetelmere($_GET["eid"]); 


			$nom=recherche_eleve_nom($_GET["eid"]);
			$prenom=recherche_eleve_prenom($_GET["eid"]);
			
			$num=0;

			Pgclose();
			print "<font class='T2'>".LANGSMS9." $nom $prenom :</font> <br><br> <table border=0 width='100%' >";
				$tel1=preg_replace('/ /',"",$tel1);
				$tel1=preg_replace('/\./',"",$tel1);
				$tel1=preg_replace('/\//',"",$tel1);
				$tel1=preg_replace('/-/',"",$tel1);
				$tel1=preg_replace('/_/',"",$tel1);
				if (is_numeric($tel1)) { 
					$num=1;
					print "<tr><td align='right' width=50% >Tél. Portable 1 : </td>";
					print "<td>$tel1 <input type=radio value='$tel1' name='tel' onclick='valide()' ></tr>"; 
				}
				$tel2=preg_replace('/ /',"",$tel2);
				$tel2=preg_replace('/\./',"",$tel2);
				$tel2=preg_replace('/\//',"",$tel2);
				$tel2=preg_replace('/_/',"",$tel2);
				$tel2=preg_replace('/-/',"",$tel2);
				if (is_numeric($tel2)) { 
					$num=1;
					print "<tr><td align='right' width=50% >Tél. Portable 2 : </td>";
					print "<td>$tel2 <input type=radio value='$tel2' name='tel' onclick='valide()' ></tr>"; 
				}
				$tel=preg_replace('/ /',"",$tel);
				$tel=preg_replace('/\./',"",$tel);
				$tel=preg_replace('/\//',"",$tel);
				$tel=preg_replace('/_/',"",$tel);
				$tel=preg_replace('/-/',"",$tel);
				if (is_numeric($tel)) { 
					$num=1;
					print "<tr><td align='right' width=50% >Téléphone : </td>";
					print "<td>$tel  <input type=radio value='$tel' name='tel' onclick='valide()' ></tr>"; 
				}
				$tel3=preg_replace('/ /',"",$tel3);
				$tel3=preg_replace('/\./',"",$tel3);
				$tel3=preg_replace('/\//',"",$tel3);
				$tel3=preg_replace('/-/',"",$tel3);
				$tel3=preg_replace('/_/',"",$tel3);
				if (is_numeric($tel3)) { 
					$num=1;
					print "<tr><td align='right' width=50% >Tél. élève : </td>";
					print "<td>$tel3  <input type=radio value='$tel3' name='tel' onclick='valide()' ></tr>"; 
				}
				$tel4=preg_replace('/ /',"",$tel4);
				$tel4=preg_replace('/\./',"",$tel4);
				$tel4=preg_replace('/\//',"",$tel4);
				$tel4=preg_replace('/_/',"",$tel4);
				$tel4=preg_replace('/-/',"",$tel4);
				if (is_numeric($tel4)) { 
					$num=1;
					print "<tr><td align='right' width=50% >Tél. Prof. Père : </td>";
					print "<td>$tel4  <input type=radio value='$tel4' name='tel' onclick='valide()' ></tr>"; 
				}
				$tel5=preg_replace('/ /',"",$tel5);
				$tel5=preg_replace('/\./',"",$tel5);
				$tel5=preg_replace('/\//',"",$tel5);
				$tel5=preg_replace('/-/',"",$tel5);
				$tel5=preg_replace('/_/',"",$tel5);
				if (is_numeric($tel5)) { 
					$num=1;
					print "<tr><td align='right' width=50% >Tél. Prof. Mère : </td>";
					print "<td>$tel5  <input type=radio value='$tel5' name='tel' onclick='valide()' ></tr>"; 
				}
			print "</table><br>";

		}elseif(isset($_GET["pid"])) {
			$num=1;
		?>
		<font class=T2><?php print LANGSMS14 ?> :</font>	

<select align=top name="tel" style="width:190px" onchange="valide()" >
<?php
print "<option value='rien' id='select0' >".LANGCHOIX."</option>";
$cnx=cnx();
print "<optgroup label='".LANGGEN1."'>";
select_personne_sms('ADM');
print "<optgroup label='".LANGGEN2."'>";
select_personne_sms('MVS');
print "<optgroup label='".LANGGEN3."'>";
select_personne_sms('ENS');
Pgclose();
?>
		</select><br><br>
		<?php
		}else{
			$num=1;
		?>
			<font class=T2><?php print LANGSMS3 ?> :</font> <input type="text" value="" name="tel" onchange="valide()" /><br /><br />
		<?php 
		} 
		?>
		</td></tr>

		<?php 
		if ($num == 0) {
			print "<tr><td><font color='red' class='T2'>".LANGMESS58."<br><br></td></tr>";
		}
		?>

		<tr><td>
		<?php print LANGSMS5 ?> : (<?php print LANGSMS4 ?>) <br>
		<textarea cols=84 rows=4 name=message onkeypress="compter(this,'150', this.form.CharRestant)" ></textarea><br><input type='text' name='CharRestant' size='2' disabled='disabled'> <font size=1><i><?php print LANGSMS6 ?>.</i></font>
		<br><br><br>
<?php 
		$nb=0;
		$nb=file_get_contents($urlsms."sms-info-nb.php?idsms=$idsms");
		if ($nb > 0) {
			print "<script language=JavaScript>buttonMagicSubmit3('Envoyer','create','disabled'); //text,nomInput</script>";	
		}else{
			print "<img src='image/commun/warning2.gif' align='center'> <font class=T2><b>Crédit SMS Epuisé !!</b></font>";
		}
		?>
		</td></tr>
		</table><br>	
			

<?php			
	}else{
		print "<center><font color=red class='T2' >".LANGMESS37.".</font></center>";
	}
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR3."</i></center>";
}

?>


<!-- // fin form -->
</td></tr></table>
<br /><br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       } else {
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       }
?>
</BODY>
</HTML>
