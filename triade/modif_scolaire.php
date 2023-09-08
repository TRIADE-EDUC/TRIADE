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
       <script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMODIF13 ?> <?php print LANGASS6 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
	include_once("librairie_php/db_triade.php");
	validerequete("menuadmin");
	// connexion P
	$cnx=cnx();
	error($cnx);

	if (isset($_POST["offline"])) {
		modif_personnel_actif_desactif($_POST["id_pers"],"1"); 
		history_cmd($_SESSION["nom"],"DESACTIVE"," de $_POST[saisie_creat_nom]");
	}

	if (isset($_POST["online"])) {
		modif_personnel_actif_desactif($_POST["id_pers"],"0"); 
		history_cmd($_SESSION["nom"],"ACTIVE"," de $_POST[saisie_creat_nom]");
	}

if(isset($_POST["create"])){
	// requete ? prenom2 ?
	$cr=modif_personnel($_POST["id_pers"],$_POST["saisie_creat_nom"],$_POST["saisie_creat_prenom"],$_POST["saisie_intitule"],$_POST["saisie_creat_adr"],$_POST["saisie_creat_code"],$_POST["saisie_creat_tel"],$_POST["saisie_creat_mail"],$_POST["saisie_creat_commune"],$_POST["saisie_creat_tel_port"],'0','',$_POST["saisie_indice_salaire"]);
	if($cr == 1){
		alertJs(LANGMODIF14);
		history_cmd($_SESSION["nom"],"MODIFICATION"," de $_POST[saisie_creat_nom]");
	}
	$saisie_id=$_POST["id_pers"];
}else{
	$saisie_id=$_GET["saisie_id"];
}
	  $passage_argument="oui"; // pour le JavaScript
          // soit 0 ou 1 ou 2 PAS DE M. ni Mme ni Mme
          $data=recherche_personne_modif($saisie_id);
          // pers_id,nom,prenom,mdp,civ,email,num,adr,code_post,commune,tel
          $nom_admin=trim($data[0][1]);
          $prenom_admin=trim($data[0][2]);
          $passwd_admin=trim($data[0][3]);
          $intitule_admin=$data[0][4];
          $mail=trim($data[0][5]);
          $adr=trim($data[0][6]);
          $code_post=trim($data[0][7]);
          $commune=trim($data[0][8]);
	  $tel=trim($data[0][9]);
	  $telPort=trim($data[0][10]);
	  $offline=trim($data[0][12]);
	  $indice_salaire=trim($data[0][15]);

?>
     <!-- // fin  -->
     <blockquote><BR>
<?php if ($offline == 1) {
	print "<center><font id=color3 class=T2  ><img src='./image/commun/warning.png' align='center'/> Ce compte est actuellement inactif ! <br></font></center><br><br>";


} ?>
<fieldset><legend><?php print LANGMODIF5 ?></legend>
<table width=80% border=0 cellpadding="2" cellspacing="2" >
<tr><td align=right ><font class="T2">Civ : </font></td><td>
<select name="saisie_intitule" > 
<option id='select0' value='<?php print $intitule_admin ?>'  ><?php print civ($intitule_admin) ?></option>
<?php listingCiv() ?>
</select>
</select>
</td></tr>
<tr><td align=right width=40%><font class="T2"><?php print LANGNA1?> : </font></td><td><input type=text name="saisie_creat_nom" value="<?php print "$nom_admin" ?>" size=33 maxlength=30></td></tr>
<tr><td align=right width=40%><font class="T2"><?php print LANGNA2?> : </font></td><td><input type=text name="saisie_creat_prenom"  value="<?php print "$prenom_admin" ?>" size=33 maxlength=30></td></tr>
<tr><td align=right width=40%><font class="T2"><?php print LANGNA3?> : </font></td><td><input type=button onclick="open('modif_pers_pass.php?id=<?php print $saisie_id;?>&type=MVS','pass','width=400,height=300')" value='<?php print LANGPER30 ?>'  class="bouton2" > </td></tr>
</table>
</fieldset>
<br><br><br>
<fieldset><legend><?php print LANGMODIF6 ?></legend>
<table border=0 width='100%' ><tr><td>
<img src="image_trombi.php?idP=<?php print $saisie_id?>" border=0 >
</td><td >
[ <a href="#" STYLE="font-family: Arial;font-size:10px;color:#CC0000;font-weight:bold;" onclick="open('photoajoutpers.php?idpers=<?php print $saisie_id ?>','photo','width=450,height=280')"><?php print LANGMODIF20 ?></a> ]<br><br>
[ <a href="#" onclick="window.location.reload(true)"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;font-weight:bold;"><?php print LANGMODIF18 ?> </a> ]
</td></tr></table>
</fieldset>


<br><br><br>
<fieldset><legend><?php print LANGMODIF7 ?></legend>
<TABLE width=80% border=0 cellpadding="2" cellspacing="2">
<tr><td align=right><font class="T2"><?php print LANGMODIF8 ?> : </font></td><td><input type=text name="saisie_creat_adr" size=33 maxlength=100 value="<?php print $adr ?>"></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF9 ?> : </font></td><td><input type=text name="saisie_creat_code" size=33 maxlength=15 value="<?php print $code_post ?>"></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF10 ?> : </font></td><td><input type=text name="saisie_creat_commune" size=33 maxlength=40 value="<?php print $commune ?>"></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF11 ?> : </font></td><td><input type=text name="saisie_creat_tel" size=33 maxlength=18 value="<?php print $tel ?>"></td></tr>
<tr><td align=right><font class="T2"><?php print LANGAGENDA76 ?> : </font></td><td><input type=text name="saisie_creat_tel_port" size=33 maxlength=18 value="<?php print $telPort ?>" ></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF12 ?> : </font></td><td><input type=text name="saisie_creat_mail" size=33 maxlength=150 value="<?php print $mail ?>"></td></tr>
<tr><td align=right><font class="T2"><?php print "Indice&nbsp;salaire" ?>&nbsp;:&nbsp;</font></td><td><input type=text name="saisie_indice_salaire" size=33 maxlength=150  value="<?php print $indice_salaire ?>" ></td></tr>
</TABLE>
</fieldset>
<br><br>
<BR><BR>
<center>
	<input type=hidden name=id_pers value="<?php print $saisie_id?>" >
	<script language=JavaScript>buttonMagicSubmit("<?php print LANGMODIF13 ?>","create"); //text,nomInput</script>
	<script language=JavaScript>buttonMagic("<?php print LANGBT9?>","list_scolaire.php","_parent","","");</script>
<br><br>
<?php if ($offline == 0) { ?>
	<script language=JavaScript>buttonMagicSubmit("<?php print "Désactivé compte" ?>","offline"); //text,nomInput</script>
<?php } else {?>
	<script language=JavaScript>buttonMagicSubmit("<?php print "Activé compte" ?>","online"); //text,nomInput</script>
<?php } ?>
<BR><br>
</center>
     </blockquote>
     <!-- // fin  -->
     </td></tr></table>
     </form>
	 
     <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>

<?php 	Pgclose(); ?>


</BODY></HTML>
