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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>

<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL1?></font></b></td></tr>
<tr  id='cadreCentral0' >
<td >
<!-- // fin  -->

<?php

if (isset($_GET["err"])) {
	print "<br><center><font id='color3'><b>Merci d'indiquer le choix du bulletin !!</b></font></center>";

}

include_once('librairie_php/db_triade.php');
include_once("librairie_php/lib_bulletin.php");
$cnx=cnx();

if (!is_dir("./data/archive/bulletin")) mkdir("./data/archive/bulletin"); 

if (isset($_GET["sClasseGrp"])) { 
	$data=aff_enr_parametrage("autorisebulletinprof"); 
	if ($data[0][1] == "oui") {
		if ($_SESSION["membre"] == "menuprof") {
			$tabClasse=affClasseAffectationProf($_SESSION["id_pers"]); //  libelle,code_classe
		}
	}else{
		$idclasse=$_GET["sClasseGrp"];
		verif_profp_class($_SESSION["id_pers"],$idclasse);
	}
}else{ 
	if ($_SESSION["membre"] == "menuprof") {
		$tabClasse=affClasseAffectationProf($_SESSION["id_pers"]); //  libelle,code_classe
	}
}

if (isset($_POST["saisie_classe"])) { $idclasse=$_POST["saisie_classe"]; } 
if ($_SESSION["membre"] == "menueleve") { validerequete("menuadmin"); }
if ($_SESSION["membre"] == "menuparent") { validerequete("menuadmin"); }
if ($_SESSION["membre"] == "menupersonnel") { 
	if (!verifDroit($_SESSION["id_pers"],"imprbulletin")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}
if ($_SESSION["membre"] == "menututeur") { validerequete("menuadmin"); }
if ($_SESSION["membre"] == "menuadmin") {
	if (isset($_POST["param"]))  {	
		enrbulletinclasse($_POST["saisie_classe"],$_POST["enrbull"]);
		enr_parametrage("autorisebulletinprof",$_POST["autorisebulletinprof"]); 
	}
}

$idsite=chercheIdSite($idclasse);

$erreurdeja=0;
$valeur=aff_Trimestre();
if (count($valeur)) {
?>
     <form method="post" action="imprimer_trimestre.php" >
     <table width="100%" border="0" align="center" >
     <tr><td width=100%>
     <table width="90%" border="0" align="center" height=150>
     <tr>
     <td width="50%" align="right" valign=top ><font class="T2"><?php print LANGBULL2?> : </font></td>
     <td  valign=top>
     <?php 
     if ( (isset($idclasse)) && ($_SESSION["membre"] != "menuadmin") )  { ?>
	<b><?php print chercheClasse_nom($idclasse) ?></b>
	<input type="hidden" name="saisie_classe" value="<?php print $idclasse ?>" />
<?php }else{ ?>
     <select name="saisie_classe" onChange="this.form.submit()" >
	 <?php 	if (isset($idclasse)) { ?>
     		<option value='<?php print $idclasse ?>'  id='select0' ><?php print chercheClasse_nom($idclasse) ?></option>
	 <?php } ?>
     		<option value='0' id='select0' ><?php print LANGCHOIX?></option>
<?php
		if (isset($_GET["sClasseGrp"])) {
			$deja=0;
			for($i=0;$i<count($tabClasse);$i++) {
				if ($tabClasse[$i][1] == $_GET["sClasseGrp"]) {
					$deja=1;
					break;
				}
			}
			if ($deja == 0) print "<option  value='".$_GET["sClasseGrp"]."' id='select1' >".chercheClasse_nom($_GET["sClasseGrp"])."</option>";
		}
		if (count($tabClasse)  > 0) {
			for($i=0;$i<count($tabClasse);$i++) {
				print "<option  value='".$tabClasse[$i][1]."' id='select1' >".$tabClasse[$i][0]."</option>";
			}
		}else{
			select_classe(); // creation des options
		}
		print "</select>";
	} 
?>
     </td>
     </tr>
     </form>
     <form name="formulaire" method="post" action="./bulletin_construction0.php"  onsubmit="return verifimpbull();">
     <input type='hidden' name='saisie_classe' value='<?php print $idclasse ?>' >
     <tr><br><td align="right"  valign=top ><font class="T2"><?php print LANGBASE40 ?></font> <select name="typetrisem" onchange="trimes();" >
     <option value=0  id='select0' ><?php print LANGCHOIX?></option>
     <option value="trimestre" id='select1'><?php print LANGPARAM28?></option>
     <option value="semestre"  id='select1'><?php print LANGPARAM29?></option>
     <option value="cycle"  id='select1'><?php print "Cycle" ?></option>
     </select> <font class="T2"> : </font></TD>
     <TD  valign=top><select name="saisie_trimestre">
                     <option id='select1'>        </option>
                     <option id='select1'>        </option>
                     <option id='select1'>        </option>
                     <option id='select1'>        </option>
              </Select>
         </td>
     </tr>
     <tr><td align=right  valign=top ><font class="T2"><?php print LANGBULL3?> :</font> </td>
     <td valign=top> 
        <select name='anneeScolaire' >
        <?php
        $anneeScolaire=$_COOKIE["anneeScolaire"];
        filtreAnneeScolaireSelectNote($anneeScolaire,7);
        ?>
        </select>
     	</td></tr>
     
     <tr><td align=right  valign=top><font class="T2"><?php print LANGPARAM35 ?> :</font> </td>
     <td  valign=top>
		<select name="typebull" >
		<option value=0  id='select0' ><?php print LANGCHOIX?></option>
	<?php
	if (isset($_COOKIE["bulletinannee"])) {
	  print "<option id='select1' value='".$_COOKIE["bulletinselection"]."' selected='selected' >".RecupBulletin(trim($_COOKIE["bulletinselection"]))."</option>";
	}

	$tab=array();
	if (file_exists("./common/config.bulletin.php")) {
		include_once("./common/config.bulletin.php");
		$liste=LISTEBULLETIN;
		$tab=explode(",",$liste);
	}
	if (count($tab) >= 1) {
		foreach($tab as $key=>$value) {
			$libelle=RecupBulletin(trim($value));
			print "<option id='select1' value='$value'>$libelle</option>";
		}
	}else{
      
		for($i=0;$i<count($tabClasse);$i++) {
			$data=recupBulletinClasse($tabClasse[$i][1]); 
			if (count($data) > 0) {
				$libel=RecupBulletin($data[0][0]);	
				$tablibel[$libel]=$data[0][0];		
			}
		}
		foreach($tablibel as $key=>$value) {
			print "<option value='$value' id='select1' >$key</option>";
		}

		if ($_SESSION["membre"] != "menuprof") {
			listingBulletin();
		}
		listBulletinBlanc();
	}
	?>
     	 </select>
      </td></tr>
</table>
<BR>
<div id='bullperso' align="center"></div>
<center>
<?php if ($_SESSION["membre"] == "menuadmin") { ?>
<table width="100%" border="0" align="center">
<?php }else{ ?>
<table width="50%" border="0" align="center">
<?php } ?>
<tr>
	<?php if ($_SESSION["membre"] == "menuadmin") { ?>
	<td><script language=JavaScript>buttonMagic("<?php print "Autorisation d'accès aux bulletins" ?>","bulletin_param.php",'_self','','') //text,nomInput,action</script></td>
	<?php } ?>
	<td><script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT43?>","rien",""); //text,nomInput,action</script></td>
<?php if (isset($_SESSION["profpclasse"])) { print "<td><script>buttonMagicRetour('profp2.php','_self')</script></td>"; } ?>
<?php if ($_SESSION["membre"] == "menuadmin") { ?>
	<td><script language=JavaScript>buttonMagic("<?php print LANGMESS386 ?>","https://support.triade-educ.org/support/bulletinPerso.php?type=bull",'_blank','','')</script></td>
<?php } ?>
	
</tr></table>
</form>
<hr>

<?php 
if ($_SESSION["membre"] == "menuadmin") { ?>
	<form method="post" action="imprimer_trimestre.php" >
	<font class="T2"><?php print LANGASS25 ?> : <select name="enrbull" >
	<?php 
	$checked="";
	$data=aff_enr_parametrage("autorisebulletinprof"); 
	if ((count($data) > 0) && ($data[0][1] != "" )) { $checked="checked='checked'"; }
	print "<option value='0'   id='select0' >".LANGCHOIX."</option>";
	if (file_exists("./common/config.bulletin.php")) {
			include_once("./common/config.bulletin.php");
			$liste=LISTEBULLETIN;
			$tab=explode(",",$liste);
			foreach($tab as $key=>$value) {
				$libelle=RecupBulletin(trim($value));
				print "<option id='select1' value='$value'>$libelle</option>";
			}
	}else{
		listingBulletin();
		listBulletinBlanc(); 
	}
	?>
	</select><br>
	<font class='T1'><i><?php print LANGMESS387 ?></i></font>
	<br /><br />

	<?php print LANGMESS388 ?> : 
	<select name="saisie_classe">
        <option selected value=0 id='select0' ><?php print LANGAFF5 ?></option>
	<?php select_classe(); ?>
	</select>
	<br /><br />
	<font class="T2"><?php print LANGMESS389 ?> : </font> <input type='checkbox' name="autorisebulletinprof" <?php print $checked ?> value='oui'  /> 
	<font class='T1'>(<?php print LANGOUI ?>)</font>
	<br /><br />
	<table align='center'><tr><td><script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR ?>","param","");</script></td></tr></table>
	</form>
	<br>
	<table width=100% bgcolor="#FFFFFF" >
	<tr><td bgcolor='yellow'><?php print LANGASS17 ?></td><td bgcolor='yellow'><?php print LANGASS25 ?></td><td bgcolor='yellow' width=5% >&nbsp;<?php print LANGASS7 ?>&nbsp;</td></tr>
<?php
	suppBulletinClasse($_GET["idsupp"]);
	$dataliste=listeBulletinClasse(); // idclasse,bulletin 
	for ($i=0;$i<count($dataliste);$i++) {
		$idclasse=$dataliste[$i][0];
		$idbull=$dataliste[$i][1];
		$libel=RecupBulletin($idbull);
		print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">";
		print "<td>&nbsp;".chercheClasse_nom($idclasse)."</td><td>&nbsp;$libel</td>";
		print "<td>&nbsp;<a href='imprimer_trimestre.php?idsupp=$idclasse'><img src='image/commun/trash.png' border='0' /></a></td>";
		print "</tr>";
	}
	print "</table>";
} 
?>
<br /><br />
</td></tr></table>
<?php
}else {

if ($erreurdeja != 1) {
?>
<br />
<center>
<?php print LANGMESS10?> <br>
<?php if ($_SESSION["membre"] == "menuadmin") { ?>
<br>
<br>
<font size=3><?php print LANGMESS13?><br>
<br>
<?php print LANGMESS12?><br>
</center>
<?php } ?>
<?php } } ?>


<!-- // fin  -->
</td></tr></table>
</form>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif ;
// deconnexion en fin de fichier


@nettoyage_repertoire("./data/pdf_bull");
Pgclose();
?>
</BODY></HTML>
