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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<SCRIPT LANGUAGE="JavaScript" src="./librairie_js/messagerie_fenetre.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]?> <?php print $_SESSION["prenom"]?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<script language='JavaScript'>
function archiver() {
	resultat=document.form1.outil.options[document.form1.outil.options.selectedIndex].value;
	resultat=resultat.substr(0,19);
	if (document.form1.outil.options[document.form1.outil.options.selectedIndex].value != "-1") {
		document.getElementById("creatrep").style.visibility='hidden';
		document.getElementById("repertoire").style.visibility='hidden';
		if (resultat == "") {
			resultat="null";
		}
		document.form1.repertoire.value=resultat;
		document.form1.submit();
	}else{
		document.getElementById("creatrep").style.visibility='visible';
		document.getElementById("repertoire").style.visibility='visible';
	}
}
</script>
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();

$id_pers=$_SESSION["id_suppleant"];
if (!verif_si_compte_suppleant($id_pers)) {
	$id_pers=$_SESSION["id_pers"];
}


// module destruction message
if(isset($_POST["supp_message"])) {
        for($i=0;$i<$_POST["saisie_nb"];$i++) {
                $checkbox="saisie_poubelle_".$i;
                $id_supp="saisie_id_poubelle_".$i;
                $checkbox=$_POST[$checkbox];
                $id_supp=$_POST[$id_supp];

                if ($checkbox == "on") {
                	$cr=suppression_message_envoyer($id_supp,$id_pers,'prof') ;
                }
        }
}

$idrep="";
if (isset($_GET["idrep"])) {
	$idrep=$_GET["idrep"];
}

if (isset($_POST["outil"])) {
	if($_POST["outil"] == "-1") {
		if ($_POST["repertoire"] != "NULL") {
			creation_repertoire($_SESSION["membre"],$id_pers,$_POST["repertoire"],"mess_supp");
		}
	}else{
		for($i=0;$i<$_POST["saisie_nb"];$i++) {
			$checkbox="saisie_poubelle_".$i;
			$id_supp="saisie_id_poubelle_".$i;
			$checkbox=$_POST[$checkbox];
			$id_supp=$_POST[$id_supp];
			if ($checkbox == "on") {
				$iddestinataire=$id_pers;
				$cr=messagerie_archive2($id_supp,$_POST["outil"]) ;
			}
		}
		$idrep=$_POST["repertoire"];
	}
}

$deb=0;
if (isset($_POST["outil2"])) {
	$idrep=$_POST["outil2"];
	if ($idrep == -2) { $idrep=""; }
	$deb=1;

}


if ($_SESSION[membre] == "menuadmin") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],ADM);
$type_personne="ADM";}
if ($_SESSION[membre] == "menututeur") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],TUT);
$type_personne="TUT";}
if ($_SESSION[membre] == "menupersonnel") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],PER);
$type_personne="PER";}
if ($_SESSION[membre] == "menuprof") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],ENS);
$type_personne="ENS";}
if ($_SESSION[membre] == "menuscolaire") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],MVS);
$type_personne="MVS";}
if ($_SESSION[membre] == "menuparent") {
$destinataire=chercheIdEleve(strtolower($_SESSION["nom"]),$_SESSION["prenom"]);
$type_personne="PAR";}
if ($_SESSION[membre] == "menueleve") {
$destinataire=chercheIdEleve(strtolower($_SESSION["nom"]),$_SESSION["prenom"]);
$type_personne="ELE";}


$data_rep=select_repertoire_messagerie($id_pers,$_SESSION["membre"],"mess_supp");
?>
<script>CreerFenetreBe();</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTMESS419 ?> <font id='color2' ><?php print LANGTMESS416 ?> <?php print recherche_repertoire($idrep,"suppression") ?></font></b> </font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // fin  -->
<br />
<script type="text/javascript" >
function validecase() {
	var nb=document.form1.saisie_nb.value;
	var j=1;
	if (document.form1.tous.checked == true) {
		for(i=0;i<nb;i++) {
			document.form1.elements[j].checked=true;
			DisplayLigne('tr'+i);
			j=j+2;
		}
	
	}else{
		for(i=0;i<nb;i++) {
			document.form1.elements[j].checked=false;
			DisplayLigne('tr'+i);
			j=j+2;
		}
	}

}
</script>

<form method=post name=form2>
&nbsp;&nbsp;&nbsp;&nbsp;<?php print LANGMESS46 ?> : <select name='outil2' onChange="this.form.submit()";>
<option value='0' id='select0'><?php print LANGCHOIX ?></option>
<option value='-2' id='select1'><?php print LANGMESS48 ?></option>
<?php
for($u=0;$u<count($data_rep);$u++) {
	$nb=0;
	$nb=nbmessagerepsupp($data_rep[$u][0],$id_pers,$type_personne);
	print "<option value='".$data_rep[$u][0]."' id='select1'>".trunchaine($data_rep[$u][1],25)." ($nb) </option>";
}
?>
</select>
</form>
<form method="POST" name="form1" >
<TABLE border=0 width=100% ><TR>
<TD bordercolor="#FFFFFF" width=10% align=center><U><?php print ucwords(LANGTE9)?></U>&nbsp;&nbsp;<input type="checkbox" onclick="validecase();" name="tous" value="1" id='checkbox1'  class="css-checkbox" /><label for='checkbox1' name='checkbox1_lbl' class='css-label lite-red-check'></label> </TD>
         <TD bordercolor="#FFFFFF"><U><?php print LANGTE5?></U></TD>
         <TD bordercolor="#FFFFFF" width=30%><U><?php print ucwords(LANGTE6)?></U></TD>
<TD align=center width=15% bordercolor="#FFFFFF"><U><?php print ucwords(LANGTE7)?></U></TD>
</TR>
        
         <!-- message -->
<?php
//---------
//
$fichier="messagerie_suppression.php#idrep=$idrep";
$table="messagerie_envoyer";
if ($idrep == "") {
	$req2="AND (repertoire IS NULL OR repertoire = '0')";
}else{
	$req2="AND repertoire='$idrep'";
}
$requete="WHERE emetteur='$destinataire' $req2 ";
$nbaff=20;
if ((isset($_GET["nba"])) && ($deb != 1)){
	$depart=$_GET["limit"];
}else {
	$depart=0;
}

//$data=affichage_messagerie_envoyer_pour_suppression_limit($id_pers,$depart,$nbaff);
$data=affichage_messagerie_envoyer_limit($type_personne,$id_pers,$depart,$nbaff,$idrep);

//$data=affichage_messagerie_pour_suppression($destinataire);
//// id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, lu_par_utilisateur
// $data : tab bidim - soustab 3 champs
for($i=0;$i<count($data);$i++) {
		
	$lu="<img src='./image/lu.png'>";
	if (DBTYPE == "pgsql") {
		if ($data[$i][10] == "f") { $lu="<img src='./image/nonlu.png'>"; }
	}
	if (DBTYPE == "mysql") {
		if ($data[$i][10] == 0) { $lu="<img src='./image/nonlu.png'>"; }
	}
	 
	if ($data[$i][9] == "PAR") {
			$titre="<i>".LANGMESS62."</i> <br>";
			$emetteur=recherche_eleve($data[$i][2]);
	}
	if ($data[$i][9] == "ELE") {
			$titre="<i>".INTITULEELEVE."</i> <br>";
			$emetteur=recherche_eleve($data[$i][2]);
	}

	if (($data[$i][9] != "ELE") &&  ($data[$i][9] != "PAR")) {
			$titre="";
			$emetteur=recherche_personne($data[$i][2]);	
	}

$reponse_checkbox="<input type=checkbox name=saisie_poubelle_".$i." onClick=\"DisplayLigne('tr".$i."')\" >";
$hidden="<input type=hidden name=saisie_id_poubelle_".$i." value=".$data[$i][0]." >";
$hidden_nb="<input type=hidden name=saisie_nb value=".count($data).">";

?>
<TR id="tr<?php print $i ?>" class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
<TD align=center ><?php print $lu?>&nbsp;<?php print $reponse_checkbox;print $hidden?></TD>
<TD > <A href='#'  onclick="return apercu('./messagerie_envoyer_reception_message_verif.php?saisie_id_message=<?php print $data[$i][0]?>')"><img src='./image/lettre1.gif' align=center border=0 alt='Message'><?php print  trunchaine(stripslashes($data[$i][8]),'35')?></A></TD>
<TD>
<?php
print $titre;
print trunchaine($emetteur,'25');
?>
</TD>
<TD align=center><?php print dateForm($data[$i][4])?> <BR> <?php print $data[$i][5]?></TD>
</TR>
<?php
 }
?>
<tr><TD height=10 colspan=4></TD></TR>
<?php
$mess="<font face=Verdana size=1><img src=\'./image/nonlu.png\'>".LANGMESS39." &nbsp; &nbsp; &nbsp; <img src=\'./image/lu.png\'>". LANGMESS38." </font>";
$information="Information";
if ((LAN == "oui") && (AGENTWEB == "oui")) {
	if (file_exists("./common/config8.inc.php")) include_once("./common/config8.inc.php");
	$vocal="M1";
	$information="Agent Web ".AGENTWEBPRENOM;
	$mess="<iframe width=100 height=100 src=\'./agentweb/agentmel.php?inc=5&mess=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe><br><font face=Verdana size=1><img src=\'./image/nonlu.png\'>".LANGMESS39." <br><br> <img src=\'./image/lu.png\'>". LANGMESS38." </font>" ;
}
$reponse_poubelle="<TR><td  colspan='4' >
	<A href='#' onMouseOver=\"AffBulle3('$information','./image/commun/info.jpg','$mess'); window.status=''; return true;\" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>&nbsp;&nbsp;
	<input type=submit name='supp_message' value='".LANGBT50."' class='button' > ";
$reponse_poubelle.="&nbsp;&nbsp;&nbsp; ";
$reponse_poubelle.=LANGTMESS415." : <select name='outil' onChange='archiver()' >";
$reponse_poubelle.="<option value='0' id='select0'>".LANGCHOIX."</option>";
$reponse_poubelle.="<option value='-1' id='select1'>".LANGTMESS412."</option>";
$reponse_poubelle.="<optgroup label=\"".LANGTMESS420."\" >";
for($u=0;$u<count($data_rep);$u++) {
	$reponse_poubelle.="<option value='".$data_rep[$u][0]."' id='select1'>".trunchaine($data_rep[$u][1],10)."</option>";
}
$reponse_poubelle.="</select>";
$reponse_poubelle.=" <input type=text name='repertoire' id='repertoire' style='visibility:hidden' /> <input type=submit value='ok' name='creatrep' id='creatrep' style='visibility:hidden'  />";

$reponse_poubelle.="</TD></TR>";

print $reponse_poubelle;
?>

</table>


<?php print $hidden_nb?>
</form>
<table width=100% border=0 >
<tr><td align=left width=33%><br>&nbsp;<?php precedent0($fichier,$table,$depart,$nbaff,$requete); ?><br><br></td>
<td align=right width=33%><br><?php suivant0($fichier,$table,$depart,$nbaff,$requete); ?>&nbsp;<br><br></td>
</tr></table>
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
     ?>

  <SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
<?php include_once("./librairie_php/finbody.php"); ?>
     </BODY></HTML>
