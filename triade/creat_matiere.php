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
<form method=post onsubmit="return verifcreatmatiere()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE13?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td ><BR>
<!-- // fin  -->
&nbsp;&nbsp;
<font class=T2><?php print LANGGRP9?> :</font> <input type=text name="saisie_creat_matiere" size=20 maxlength='200' > <i><?php print LANGMESS208 ?></i><BR>
<BR><bR>
&nbsp;&nbsp;
<font class=T2><?php print LANGGRP9?> :</font> <input type=text name="saisie_creat_matiere_en" size=20 maxlength='200' > <i><?php print LANGTMESS450 ?></i><BR>
<BR><bR>
&nbsp;&nbsp;
<font class=T2><?php print LANGGRP9?> :</font> <input type=text name="saisie_creat_matiere_long" size=40 maxlength='250' > <i><?php print LANGMESS209 ?>.</i><BR>
<BR><bR>
&nbsp;&nbsp;
<font class=T2><?php print LANGMESS210 ?> :</font> <input type=text name="saisie_code_matiere" size=20 maxlength='20' ><BR>
<BR><bR>

<script language=JavaScript>buttonMagicSubmit("<?php print LANGMAT1 ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("<?php print LANGMAT2 ?>","list_matiere.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGAGENDA86 ?>","base_de_donne_importation23.php?id=matierexls","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGMAT3 ?>","suppression_matiere.php","_parent","","");</script>
<br><br>
<br>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
if(isset($_POST["create"])):
        include_once("librairie_php/db_triade.php");
	validerequete("menuadmin");
        // connexion P
        $cnx=cnx();
        // creation
        $cr=create_matiere_2($_POST["saisie_creat_matiere"],$_POST["saisie_creat_matiere_long"],$_POST["saisie_code_matiere"],$_POST["saisie_creat_matiere_en"]);
        if($cr):
	       alertJs(LANGGRP8);
	       $matiere=$_POST["saisie_creat_matiere"];
               history_cmd($_SESSION["nom"],"CREATION","matiere $matiere");
        else:
               error(0);
        endif;
        Pgclose();
endif;
?>
   </BODY></HTML>

