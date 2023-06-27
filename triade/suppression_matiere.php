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
	$cr=@verif_utiliser_matiere($_POST["saisie_matiere_supp"]);
	if (!$cr) {
        	$cr=suppression_matiere($_POST["saisie_matiere_supp"]) ;
	        if($cr):
			$matierenom=chercheMatiereNom($_POST["saisie_matiere_supp"]);
			history_cmd($_SESSION["nom"],"SUPPRESSION","matière $matierenom");
        	        alertJs(LANGSUPP26);
                	reload_page('suppression_matiere.php');
	        else:
        	        error(0);
	        endif;
	}else {
		alertJs("Impossible de supprimer cette matière. \\n\\n Matière affectée à une classe.  \\n\\n  Service Triade");
	}
endif;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_supp_choix('saisie_matiere_supp','une matière')" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGSUPP23?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<blockquote><BR>
<fieldset><legend><?php print LANGSUPP1?></legend>
<br>
Liste des matières pouvant être supprimée. <br><i>Matière n'étant pas affectée à une classe de cette année ou d'une année précédente.</i><br><br>
&nbsp;&nbsp;
<font class='T2'><?php print LANGPER17?> :</font> <select name="saisie_matiere_supp">
          <option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_MatiereNonAffecter(20); // creation des options
Pgclose();
?>
</select><BR><br><br>
<script language=JavaScript>buttonMagic("<?php print LANGMAT2 ?>","list_matiere.php","_parent","","");</script><script language=JavaScript>buttonMagicSubmit("<?php print LANGSUPP24?>","supp"); //text,nomInput</script><br><br><br>
</fieldset>
<?php brmozilla($_SESSION["navigateur"]); ?>
</blockquote>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
