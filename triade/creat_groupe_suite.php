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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

if (!isset($_POST["aucun_eleve"])) {
	if (count($_POST["saisie_liste"]) <= 0 ) {
?>
		<script language=JavaScript>
		alert("<?php print LANGGRP22?>");
		location.href="creat_groupe.php";
		</script>

<?php
	}
}else{

	if ((count($_POST["saisie_liste"]) <= 0 ) && ($_POST["aucun_eleve"] != "1") ) {
	?>
		<script language=JavaScript>
		alert("<?php print LANGGRP22?>");
		location.href="creat_groupe.php";
		</script>

<?php
	}
}



if (GROUPEGESTIONPROF == "oui") {
	$JS="validecreatgroupe2bis()";
}else{
	$JS="validecreatgroupe2()";
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return <?php print $JS ?>" name="formulaire" action='./creat_groupe_suite.php' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGRP15?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<ul><BR>
<?php
// debut if de premiere procedure
  if (! isset($_POST["create"])) :
?>

<font class=T2><?php print "Année Scolaire" ?> : <?php print $_POST["annee_scolaire"] ?><br><br>

<font class=T2><?php print LANGGRP1?> </font> : <input type=text name='saisie_intitule' size=25 onfocus="this.blur()" value="<?php print stripslashes($_POST["saisie_intitule"]) ?>"><BR>
<BR>                <BR><font class=T2><?php print LANGGRP16?></font><BR><BR></UL>
<UL><?php print LANGASS27?> : <BR>
<textarea name="saisie_commentaire" cols=65 rows=2></textarea></UL>
<center>
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

$sql=<<<EOF

SELECT
	c.libelle,
	e.nom,
	e.prenom,
	e.elev_id,
	e.lv1,
	e.lv2
FROM
	${prefixe}classes c,
	${prefixe}eleves e
WHERE
	c.code_class = e.classe
AND 	e.classe IN ($in)
ORDER BY
	e.nom,
	e.prenom

EOF;

$res=execSql($sql);
$data=chargeMat($res);

}

?>
<table border=1 width=100% bordercolor="#000000" style="border-collapse: collapse;" >
<TR>
<TD bgcolor="yellow" width=20%><B><?php print LANGEL1?></b> </TD>
<TD bgcolor="yellow" width=20%><b><?php print LANGEL2?> </b></TD>
<TD bgcolor="yellow" width=10%><b><?php print LANGEL3?> </b></TD>
<TD bgcolor="yellow" width=15%><b><?php print LANGEL4?></b></TD>
<TD bgcolor="yellow" width=15%><b><?php print LANGEL5?></b></TD>
<TD bgcolor="yellow" align=center width=20%><b><?php print LANGGRP17?> </b></TD>
</TR>
<?php
for($i=0;$i<count($data);$i++)
        {
        ?>
	<TR id="tr<?php print $i ?>" class="tabnormal" onmouseover="this.className='tabover2'" onmouseout="this.className='tabnormal'">
	<TD>
        <?php print ucwords($data[$i][1])?>
	</TD>
	<TD><?php print ucwords($data[$i][2])?>
	</TD>
	<TD><?php print $data[$i][0]?>
	</TD>
	<TD><?php print $data[$i][4]?></TD>
	<TD><?php print $data[$i][5]?></TD>
	<TD align=center >
	 <input type=checkbox name="saisie_choix_eleve[]" onClick="click_eleve();DisplayLigne('tr<?php print $i?>')" value='<?php print $data[$i][3]?>'> </TD>
	</TR>
	<?php
	}
?>
</TABLE>
</TD></TR></TABLE></center>
<input type=hidden name=saisie_eleve >
<input type=hidden name=saisie_liste value='1'>
<input type=hidden name=annee_scolaire value='<?php print $_POST["annee_scolaire"] ?>'>
<?php if (GROUPEGESTIONPROF == "oui") { ?>
<br>&nbsp;&nbsp;&nbsp;<input type=checkbox name=aucun_eleve value='1' /> Création du groupe sans élève. 
<?php } ?>
<BR><BR><UL>
<script language=JavaScript>buttonMagic("<?php print LANGBT20?>","creat_groupe.php","_parent","","");</script>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGGRP18?>","create"); //text,nomInput</script>
</ul><br><br>
<!-- // fin  -->
</form>
<br /><br />
<?php
endif;  // fin de la premiere procedure

if (isset($_POST["create"])) {

$anneeScolaire=$_POST["annee_scolaire"];
$params[liste_eleve]=join(",",$_POST["saisie_choix_eleve"]);
$params[comment]=$_POST["saisie_commentaire"];
$params[nomgr]=trim($_POST["saisie_intitule"]);

if(create_groupe($params,$anneeScolaire)):
	alertJs("Groupe créé \n\n Service Triade ");
	history_cmd($_SESSION["nom"],"CREATION","groupe ".$_POST["saisie_intitule"]." ");
else:
	error(0);
endif;
?>
</UL><center><font class=T2><?php print LANGGRP19?></font><BR><BR><br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGGRP20?>","creat_groupe.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGGRP21?>","liste_groupe.php","_parent","","");</script>&nbsp;&nbsp;
</td><tr></table>
<bR><bR><br>
<?php
}
?>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
Pgclose();
?>
</BODY></HTML>
