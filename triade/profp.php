<?php
session_start();
unset($_SESSION["profpclasse"]);
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
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
error_reporting(0);
include_once("common/config.inc.php"); // futur : auto_prepend_file
include_once("librairie_php/db_triade.php");
$cnx=cnx();

// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
// données DB utiles pour cette page
// $donne=$mySession[Spid];
$donne=$_SESSION["id_suppleant"];
$sql=<<<SQL
SELECT
	p.idprof,
	p.idclasse
FROM
	${prefixe}prof_p p, ${prefixe}classes c
	
WHERE
	p.idprof='$donne' AND p.annee_scolaire='$anneeScolaire' AND c.code_class=p.idclasse 
ORDER BY c.libelle

SQL;
$curs=execSql($sql);
$data=chargeMat($curs);
// patch pour problème sous-matière à 0
for($i=0;$i<count($data);$i++){
	$nomclasse=chercheClasse($data[$i][1]);
	$nomclasse=$nomclasse[0][1];
	$option.="<option STYLE='color:#000066;background-color:#CCCCFF' value='".$data[$i][1]."'>$nomclasse</option>\n";
}
// fin patch
freeResult($curs);
unset($curs);
?>
<HTML>
<HEAD>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROF12 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td>
<br />
<ul>
	<form method="post" action="profp.php" >
        <font class="T2"><?php print LANGBULL29 ?> :</font>
        <select name='anneeScolaire' onChange="this.form.submit()"  >
        <?php
        filtreAnneeScolaireSelectNote($anneeScolaire,10);
        ?>
        </select>
        <br/>
        </form>


<form method="POST" onsubmit="return verifAccesFiche()" name="formulaire" action="profp2.php">
<font class="T2"><?php print LANGPROFG ?> :</font>
<select name="sClasseGrp" size="1" >
<option value="0" STYLE="color:#000066;background-color:#FCE4BA"> <?php print LANGCHOIX3 ?></option>
<?php
print $option;
?>
</select>
<br /><br />
<br>
<UL><UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script>
<br><br>
</UL></UL></UL></UL></UL>
</form>

<?php 

if (verifProfPUE($_SESSION["id_pers"])) {  
	$option="";
	$data=recupIdClasseUEProfp($_SESSION["id_pers"]);
	for($i=0;$i<count($data);$i++){
		$nomclasse=chercheClasse($data[$i][0]);
		$nomclasse=$nomclasse[0][1];
		$option.="<option STYLE='color:#000066;background-color:#CCCCFF' value='".$data[$i][0]."'>$nomclasse</option>\n";
	}
?>	
<hr>
<form method="POST" action="profpUE2.php">
<br />
<ul>
<font class="T2"><?php print "Unit&eacute; d'Enseignement" ?> :</font>
<select name="sClasseGrp" size="1" >
<option value="0" STYLE="color:#000066;background-color:#FCE4BA"> <?php print LANGCHOIX3 ?></option>
<?php
print $option;
?>
</select>
<br /><br />
<br>
<UL><UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script>
<br><br>
</UL></UL></UL></UL></UL>
</form>

<?php } ?>




<br>

</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION[membre] == "menuadmin") :
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
</BODY>
</HTML>
<?php @Pgclose() ?>
