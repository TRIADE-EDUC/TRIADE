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
        <script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
        <script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
        <script language="JavaScript" src="./librairie_js/function.js"></script>
	<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
	<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
        <title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
        </head>
        <body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
                        <?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
if(isset($_POST["supp"])):
	$cr=@verifGroupeAffectation($_POST["saisie_grp_supp"]);
	$groupe=chercheGroupeNom($_POST["saisie_grp_supp"]);
	if (!$cr) {
        	$cr=suppression_groupe($_POST["saisie_grp_supp"]) ;
        	if($cr){
                	alertJs("Groupe supprimé --  L'Equipe Triade");
			history_cmd($_SESSION["nom"],"SUPPRESSION","Groupe $groupe supprimé");
                	reload_page('suppression_groupe.php');
        	}else{
                	alertJs("Groupe NON supprimé --  L'Equipe Triade");
		}
	}else {
		alertJs("Le groupe est actuellement affecté.\\n\\n Impossible de le supprimer.\\n\\n Modifier l\\'affectation avant de supprimer ce groupe.\\n\\n Service Triade");

	}
endif;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_supp_choix('saisie_grp_supp','un groupe')" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSUPP7?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<BR>
<blockquote>
<fieldset><legend>Module Suppression</legend>
&nbsp;&nbsp;
<font class="T2">Nom du groupe :</font>
<select name="saisie_grp_supp">
<option value=choix STYLE="color:#000066;background-color:#FCE4BA" ><?php print LANGCHOIX?></option>
<?php
$data=aff_groupe();
for($i=0;$i<count($data);$i++) {
	if ($data[$i][3] != "") {
?>
	<option value="<?php print $data[$i][0]?>" STYLE='color:#000066;background-color:#CCCCFF' ><?php print $data[$i][3]?></option>
<?php
	}
}
?>
</select><br><br><br>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGSUPP8?>","supp"); //text,nomInput</script></UL></UL></UL><br><br>
</fieldset>
</blockquote>
<?php brmozilla($_SESSION[navigateur]); ?>
<!-- // fin  -->
</td></tr></table>
</form>
     <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
   </BODY></HTML>
<?php
Pgclose();
?>
