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
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return suite()" name="formulaire" action="./base_de_donne_key.php?base=newannee" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Nouvelle année scolaire" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<br><br>
<ul><font class='T2'><?php print "<b>Module permettant de passer à l'année suivante</b>" ?>.</font></ul><br>

<ul><img src="image/commun/warning2.gif" align='center' />&nbsp;&nbsp;&nbsp;<font class=T2 id='color3' ><b>Liste des éléments supprimés :</b> <br /></font><br>

<font class=T2>&nbsp;&nbsp;- Suppression des notes vie scolaire élèves. </font><br /> 
<font class=T2>&nbsp;&nbsp;- Suppression des études d'élèves. </font><br />
<font class=T2>&nbsp;&nbsp;- Suppression info D.S.T avant le <input type='text' name="supp_date_dst" size='12' maxlength='10' value="<?php print date("d/m/Y") ?>"  onKeyPress="onlyChar2(event)" /></font><br />
<font class=T2>&nbsp;&nbsp;- Suppr. info cal. des événements avant le <input  name="supp_date_cal" type='text' maxlength='10'  size='12' value="<?php print date("d/m/Y") ?>"  onKeyPress="onlyChar2(event)" /></font><br />
<font class=T2>&nbsp;&nbsp;- Suppr. info Emploi du temps (EDT) avant le <input type='text' name="supp_date_edt" size='12' maxlength='10'  value="<?php print date("d/m/Y") ?>"  onKeyPress="onlyChar2(event)" /></font><br />
<font class=T2>&nbsp;&nbsp;- Suppression des délégués. </font><br />
</ul>

<script language=JavaScript>
      function suite() {
	       var confirmation=confirm('<?php print LANGCHAN3?>','');
               return confirmation;
      }
      </script>
<BR><div align="center"> <input type=submit  class="BUTTON" value='<?php print LANGBTS?>' /> </div><br />
</form>
<br>
<!-- // fin form -->
 </td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
