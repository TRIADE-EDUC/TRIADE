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
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$Seid=$_SESSION["id_pers"];

if ($_SESSION["membre"] == "menututeur") { $Seid=""; }

if (isset($_SESSION["idelevetuteur"])) {
	$Seid=$_SESSION["idelevetuteur"];	
}

if (isset($_POST["idelevetuteur"])) {
	$Seid=$_POST["idelevetuteur"];
	$_SESSION["idelevetuteur"]=$Seid;
	$Scid=chercheClasseEleve($Seid);
	$_SESSION["idClasse"]=$Scid;
}

if ((trim($Seid) == "") && ($_SESSION["membre"] == "menututeur")) {
         $list=listEleveTuteur2($_SESSION["id_pers"]);
         if (count($list) == 1) {
                $Seid=$list[0][0];
                $Scid=chercheClasseEleve($Seid);
                $idClasse=$Scid;
        }
}


?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<form method="post" action="visu_absence_parent.php" >
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPARENT7 ?> </font></b>
<?php
if ($_SESSION["membre"] == "menututeur") {
?>
	&nbsp;&nbsp;
	<select name='idelevetuteur' onchange="this.form.submit()" >
		<?php 
		if ($Seid != "") {
			$nom=recherche_eleve_nom($Seid);
			$prenom=recherche_eleve_prenom($Seid);
	        	print "<option id='select1' value='$Seid' title=\"".strtoupper($nom)." $prenom\" >".trunchaine(strtoupper($nom)." ".$prenom,40)."</option>\n";
		}else{
			print "<option id='select0' >".LANGCHOIX."</option>";
		}
		listEleveTuteur($_SESSION["id_pers"],40)
		?>
	</select>
<?php
}
?>
</td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<br />
<font class="T2"><?php print LANGBULL29 ?> :</font>
<select name='anneeScolaire' onchange="this.form.submit()" >
<?php
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select><br /><br />
</form>
<!-- // fin  -->
<table border="1" bordercolor="#000000" width="100%" style='border-collapse: collapse;'  >
<TR>
<TD bgcolor='yellow' align=center width=5%><?php print preg_replace('/ /','&nbsp;',LANGPARENT8) ?> </td>
<TD bgcolor='yellow' align=center width=15%><?php print ucwords(LANGABS43) ?> </td>
<TD bgcolor='yellow' align=center width=5%><?php print ucwords(LANGPROFK) ?> </td>
<TD bgcolor='yellow' align=center><?php print ucwords(LANGDISP2) ?> </td>
</TR>
<?php
$data_2=affAbsence($Seid,$anneeScolaire);
//  elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time, justifier, heure_saisie, heuredabsence, creneaux, smsenvoye, idrattrapage
for($j=0;$j<count($data_2);$j++) {
	$motif=$data_2[$j][6];
	$dateRattrapage="";
	$heureRattrapage="";
	$valideRattrapage="";
	$idrattrapage=$data_2[$j][15];
//	$SMSEnvoye=$data_2[$j][14];
	if ($idrattrapage > 0) {
		$dataRattrapage=recupRattrappage($idrattrapage); //date,heure_depart,duree,valider
		$dateRattrapage=$dataRattrapage[0][0];
		$heureRattrapage=$dataRattrapage[0][1];
		$valideRattrapage=$dataRattrapage[0][3];
	}
	if ($data_2[$j][6] == "inconnu") { $motif=LANGINCONNU; }
       	if (trim($data_2[$j][6]) == "0") { $motif=LANGINCONNU; }
?>
        <tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
        <TD align=center valign=top><?php print dateForm($data_2[$j][1])?></td>
	<TD align=center valign=top><?php 
	if ($data_2[$j][4] > 0) {
		print $data_2[$j][4]." Jour(s)";
	}
	if ($data_2[$j][4] == -1) {
		print $data_2[$j][7]." Heure(s)";
	}
	?> </td>
        <TD align=center valign=top><?php print dateForm($data_2[$j][2])?></td>
	<TD valign=top>&nbsp; <?php print ucwords($motif)?>
	<?php 
	if ($dateRattrapage != "") {
		$dateRattrapage=dateForm($dateRattrapage);
		$heureRattrapage=timeForm($heureRattrapage);
		echo "<br> <font color=blue>- Rattrapé le $dateRattrapage à $heureRattrapage </font>";
	} 
	?>
	</td>
        </td>
        </tr>

<?php
      }
?>
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
