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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGETUDE28 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
validerequete("2");
$data=liste_etude();
//id,jour_semaine,heure,salle,pion,nom_etude,duree
?>
<br><br>
<table border="1" bordercolor="#000000" align="center" width="75%">
<?php
for($i=0;$i<count($data);$i++) {
	if (($data[$i][4] == "-1") || ($data[$i][4] == NULL )) {
		$pion="???";
	}else {
		$pion=$data[$i][4];
	}
?>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'" >
        <td bordercolor=#FFFFFF>
        <font class=T1>
        <?php print LANGETUDE13 ?> : <b><?php print $data[$i][5] ?></b><br>
        <?php print LANGETUDE14 ?> : <b><?php print $data[$i][3] ?></b> / <?php print LANGETUDE12 ?> : <b><?php print $pion ?></b> <br>
        <?php print LANGETUDE15 ?> : <b></b><br>
        <?php print LANGETUDE16 ?> : <b>
        <?php
        $liste=preg_replace('/\{/','',$data[$i][1]);
        $liste=preg_replace('/\}/','',$liste);
        $tab=explode(",", $liste);
        foreach($tab as $value) {
                print jourdesemaine($value).",";
        }

        ?>
        <br>
        </b> <?php print LANGETUDE17 ?> <b><?php print timeForm($data[$i][2]) ?></b> <?php print LANGETUDE18 ?> <b>
        <?php
        if ($data[$i][6] == 0) {
                print "???";
        }else {
                print $data[$i][6];
        }
        ?>
        </b>
        <br><br>
        </font>
        </td>
	<td valign=center align=center bordercolor="#FFFFFF" >
	<form method="POST" action="gestion_etude_modif2.php">
	<input type=hidden name=id value="<?php print $data[$i][0]?>"  >
	<input type=submit name="create" value="<?php print LANGPER30 ?>" class="bouton2">
	</form>
	</td>
	</tr>


<?php
}
?>

</tr></table>
<br><br>
<!-- // fin  -->
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
