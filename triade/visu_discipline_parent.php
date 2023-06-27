<?php 
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
        $anneeScolaire=$_POST["anneeScolaire"];
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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('./librairie_php/db_triade.php');
$cnx=cnx();
$Seid=$_SESSION["id_pers"];
if ($_SESSION["membre"] == "menututeur") { $Seid=""; }
if (isset($_POST["idelevetuteur"])) {
	$Seid=$_POST["idelevetuteur"];
	$_SESSION["idelevetuteur"]=$Seid;
	$Scid=chercheClasseEleve($Seid);
	$_SESSION["idClasse"]=$Scid;
}
if (isset($_SESSION["idelevetuteur"])) {
	$Seid=$_SESSION["idelevetuteur"];	
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method="post" action="visu_discipline_parent.php" >
     <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
     <tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPARENT14 ?></font></b>

<?php
if ($_SESSION["membre"] == "menututeur") {
?>
	&nbsp;&nbsp;
	<select name='idelevetuteur' onchange="this.form.submit()" >
		<?php 
		if ($Seid != "") {
			$nom=recherche_eleve_nom($Seid);
			$prenom=recherche_eleve_prenom($Seid);
	        	print "<option id='select1' value='$Seid' title=\"".strtoupper($nom)." $prenom\" >".trunchaine(strtoupper($nom)." ".$prenom,30)."</option>\n";
		}else{
			print "<option id='select0' >".LANGCHOIX."</option>";
		}
		listEleveTuteur($_SESSION["id_pers"],30)
		?>
	</select>
<?php
}
?>
</td>
     </tr>
</form>
     <tr id='cadreCentral0' >
     <td valign=top>
<br />
<font class="T2"><?php print LANGBULL29 ?> :</font>
<select name='anneeScolaire' onchange="this.form.submit()" >
<?php
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select><br /><br />
<!-- // fin  -->
<table border="1" bordercolor="#000000" width="100%" style='border-collapse: collapse;' >
<TR><td colspan=4 bgcolor=#FFFFFF align=center> <?php print LANGPARENT15 ?>  </td></tr>
<TR>

<TD bgcolor='yellow' align=center width=5><?php print ucwords(LANGPROFK) ?></td>
<TD bgcolor='yellow' align=center ><?php print LANGDISC57?> </td>

</TR>
<?php

if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "menueleve")) {
	$data_2=affSanction_par_eleve($Seid);
}

if (($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	$data_2=affSanction_par_eleve($_GET["eid"]);
}


// id,id_eleve,motif,id_category,date_saisie,origin_saisie,signature_parent,attribuer_par,devoir_a_faire,description_fait
// $data : tab bidim - soustab 3 champs

for($j=0;$j<count($data_2);$j++)
        {
		$raison=$data_2[$j][8];
		$raison=preg_replace('/\r\n/',"<br />",$raison);
		$raison=preg_replace('/\n/',"<br />",$raison);

?>
	<TR  class="tabnormal2" onMouseOver="this.className='tabover'" onMouseOut="this.className='tabnormal2'">
	
	<TD align=center valign="top" width=10% ><?php print dateForm($data_2[$j][4])?></td>
	<TD valign=top>
	&nbsp;<?php print ucwords(LANGDISC20) ?>: <font color=red><b><?php print rechercheCategory($data_2[$j][3])?></b></font> <br />
	&nbsp;<?php print ucwords(LANGPARENT15) ?>: <b><?php print $data_2[$j][2]?></b><br />
	&nbsp;Attribué par : <?php print trim($data_2[$j][7]) ?><br>
	&nbsp;Description des faits : <?php print $data_2[$j][9]?><br>
	&nbsp;Devoir à faire : <?php print $data_2[$j][8]?>
	</td>
	
	</TR>

<?php
	
        }
?>
</table>
<br /><br />
<table border="1" bordercolor="#000000" width="100%" style='border-collapse: collapse;' >
<TR><td colspan=3 bgcolor=#FFFFFF align=center> <?php print LANGPARENT16 ?></td></tr>
<TR>
<TD bgcolor='yellow' align=center width=10%><?php print LANGPARENT16 ?></td>
<TD bgcolor='yellow' align=center ><?php print LANGDISP2 ?></td>
</TR>
<?php
if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menututeur") ) {
	$data_2= affRetenuTotal_par_eleve($_SESSION["id_pers"]);
}

if (($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	$data_2= affRetenuTotal_par_eleve($_GET["eid"]);
}


// id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire,description_fait
// $data : tab bidim - soustab 3 champs
for($j=0;$j<count($data_2);$j++)
        {
?>
        <TR  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
        <form method=POST>
	<TD align=center valign=top><?php print dateForm($data_2[$j][1])?><br><?php print LANGPARENT17 ?><br><?php print $data_2[$j][2]?>
	<br> (<?php print timeForm($data_2[$j][10]) ?>) </td>
	<TD valign=top>
	&nbsp;<?php print ucwords(LANGDISC20) ?>: <font color=red><b><?php print rechercheCategory($data_2[$j][5])?></b></font> <br />
	&nbsp;<?php print ucwords(LANGPARENT15) ?>: <b><?php print $data_2[$j][7]?></b><br />
	&nbsp;<?php print LANGPARENT18 ?> :
		<?php
		if ($data_2[$j][6] != 1 ) {
			print "<b><font color=red>".ucwords(LANGNON)."</font></b>";
		}else {
			print ucwords(LANGOUI);
		}
		?>
	<br />&nbsp;Attribué par : <?php print ucwords($data_2[$j][8])?> - le <?php print dateForm($data_2[$j][3]) ?>
	<br />&nbsp;Description des faits : <?php print $data_2[$j][12]?>
	<br />&nbsp;Devoir à faire : <?php print $data_2[$j][11]?>
	</td>
        </form>
        </TR>
<?php } ?>

</table>
<br><br>
     <!-- // fin  -->
     </td></tr></table>
     <?php
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
     Pgclose();
     ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
   </BODY></HTML>
