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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post name="formulaire" action="gestion_etude_eleve_ajout2.php" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGETUDE33 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<ul><BR>
<?php
// debut if de premiere procedure
if (! isset($_POST["create"])) :
?>
                 <font class="T2"><?php print LANGETUDE36 ?> :</font> <b><?php print cherche_nom_etude($_POST["saisie_etude"]) ?></b><br>
		 <input type=hidden name='saisie_etude' value="<?php print $_POST["saisie_etude"]?>">
<BR>                <BR><b><?php print LANGETUDE37 ?></b><BR><BR></UL>
<table width=100% border=0>
<TR><TD>
<?php
$i=0;
foreach($_POST["saisie_liste"] as $value) {
	$classes[$i]=$value;
	$i++;
}
$in=join(",",$classes);
if (trim($in) != "") {
	$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id,e.annee_scolaire FROM ${prefixe}classes c, ${prefixe}eleves e WHERE c.code_class = e.classe AND 	e.classe IN ($in) ORDER BY  e.nom,e.prenom";
	$res=execSql($sql);
	$data=chargeMat($res);
}
?>
<table border=1 width=100% bordercolor="#000000" style="border-collapse: collapse;" >
<TR>
<TD bgcolor="yellow" width='50%'><B><?php print LANGNA1 ?></b> <b><?php print LANGNA2 ?> </b></TD>
<TD bgcolor="yellow" width='5%'>&nbsp;<B>Année&nbsp;Scolaire</b>&nbsp;</TD>
<TD bgcolor="yellow" width=5%><b><?php print LANGASS27 ?> </b></TD>
<TD bgcolor="yellow" align=center width=5%><b>Sélectionner</b></TD>
</TR>
<script language=JavaScript>
function validecom(id) {
	if (document.getElementById(id).style.visibility == 'hidden' ) {
		document.getElementById(id).style.visibility='visible';
	}else {
		document.getElementById(id).style.visibility='hidden';
	}
}
</script>
<?php
for($i=0;$i<count($data);$i++) {
        ?>
	<TR class="tabnormal" onmouseover="this.className='tabover2'" onmouseout="this.className='tabnormal'">
	<TD>
        <b><?php print ucwords($data[$i][1])?></b>
	<?php print ucwords($data[$i][2])?>
	<br><?php print LANGCALEN7 ?> :
	<?php print $data[$i][0]?>
	</TD>
	<td align='center' ><?php print $data[$i][4] ?></td>
	<TD><div id="<?php print $data[$i][3]?>" style="visibility:hidden">
            <input type=text size=34 name="commentaire<?php print $data[$i][3]?>"><br>
	    <?php print LANGETUDE38 ?> : <input type=checkbox name="sortir<?php print $data[$i][3]?>" value="1" > (oui)
	    </div> </TD>
	<TD align=center >
	 <input type=checkbox name="saisie_choix_eleve[]" onClick="click_eleve();validecom(<?php print $data[$i][3]?>)" value='<?php print $data[$i][3]?>'> </TD>
	</TR>
	<?php
	}
?>
</TABLE>
</TD></TR></TABLE></center>
<input type=hidden name=saisie_eleve >
<BR><BR><UL>
<script language=JavaScript>buttonMagic("<?php print LANGBT20 ?>","gestion_etude_eleve_ajout.php","_parent","","");</script>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","create"); //text,nomInput</script>
</ul><br><br>
<!-- // fin  -->
</form>
<br /><br />
<?php
endif;  // fin de la premiere procedure
?>
<?php
if (isset($_POST["create"])) {
	$params=$_POST["saisie_choix_eleve"];
	$id_etude=$_POST["saisie_etude"];
	foreach($params as $value) {
		$id_eleve=$value;
		$id="commentaire$id_eleve";
		$id2="sortir$id_eleve";
		$info=$_POST[$id];
		$sortir=$_POST[$id2];
		if ($sortir != 1) { $sortir=0; }
		$cr=create_etude_eleve($id_eleve,$id_etude,$info,$sortir);
	}
?>
</UL><center><font class=T2><?php print LANGETUDE33 ?></font><BR><BR><br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGETUDE40 ?>","gestion_etude_eleve_ajout.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGETUDE11 ?>","gestion_etude_liste.php","_parent","","");</script>&nbsp;&nbsp;
</td><tr></table>
<bR><bR><br>
<?php
}
?>
</td></tr></table>
<?php
	Pgclose();
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
