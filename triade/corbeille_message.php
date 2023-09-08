<?php
session_start();
error_reporting(0);
$messclassic=$_COOKIE['messmodelecture'];
if (isset($_POST['messclassic'])) {
	$messclassic=$_POST['messclassic'];
	setcookie("messmodelecture",$messclassic,time()+3600*24*90);
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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<SCRIPT LANGUAGE="JavaScript" src="./librairie_js/messagerie_fenetre.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php  print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<script language='JavaScript'>


function validecase() {
	var nb=document.form1.saisie_nb.value;
	var j=0;
	if (document.form2.tous.checked == true) {
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
</head>
<?php

include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();


//$id_pers=$_SESSION["id_suppleant"];
//if (!verif_si_compte_suppleant($id_pers)) {
$id_pers=$_SESSION["id_pers"];
//}
if (isset($_SESSION["id_suppleant"])) {
        $id_pers=$_SESSION["id_suppleant"];
}

	

$idrep="";

if (isset($_GET["idrep"])) {
	$idrep=$_GET["idrep"];
}

if (isset($_POST["outil"])) {
	if($_POST["outil"] == "-1") {
		if ($_POST["repertoire"] != "NULL") {
			creation_repertoire($_SESSION["membre"],$id_pers,$_POST["repertoire"],"");
		}
	}else{
		for($i=0;$i<$_POST["saisie_nb"];$i++) {
			$checkbox="saisie_poubelle_".$i;
			$id_supp="saisie_id_poubelle_".$i;
			$checkbox=$_POST[$checkbox];
			$id_supp=$_POST[$id_supp];
			if ($checkbox == "on") {
				$iddestinataire=$id_pers;
				$cr=messagerie_archive($id_supp,$_POST["outil"]) ;
			}
		}
		$idrep=$_POST["repertoire"];
	}
}
$deb=0;
if (isset($_POST["outil2"])) {
	$idrep=$_POST["outil2"];
	if ($idrep == 0 ) { $idrep=""; }
	if ($idrep == -2) { $idrep=""; }
	$deb=1;
}



// module destruction message
if(isset($_POST["suppmess"])) {
		for($i=0;$i<$_POST["saisie_nb"];$i++) {
			$checkbox="saisie_poubelle_".$i;
			$id_supp="saisie_id_poubelle_".$i;
			$checkbox=$_POST[$checkbox];
			$id_supp=$_POST[$id_supp];

			if ($checkbox == "on") {
				$iddestinataire=$id_pers;
				$cr=suppression_message($id_supp,$iddestinataire,'null') ;
			}
	}
}


if (isset($_POST["restaurer"])) {
	for($i=0;$i<$_POST["saisie_nb"];$i++) {
		$checkbox="saisie_poubelle_".$i;
                $id_supp="saisie_id_poubelle_".$i;
                $checkbox=$_POST[$checkbox];
                $id_supp=$_POST[$id_supp];
                if ($checkbox == "on") {
                	$iddestinataire=$id_pers;
                        $cr=restaurer_message($id_supp,$iddestinataire,'null') ;
                }
        }
}


$data_rep=select_repertoire_messagerie($id_pers,$_SESSION["membre"],"");

if ($_SESSION["membre"] == "menuadmin") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],ADM);
$type_personne="ADM";}
if ($_SESSION["membre"] == "menututeur") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],TUT);
$type_personne="TUT";}
if ($_SESSION["membre"] == "menupersonnel") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],PER);
$type_personne="PER";}
if ($_SESSION["membre"] == "menuprof") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],ENS);
$type_personne="ENS";}
if ($_SESSION["membre"] == "menuscolaire") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],MVS);
$type_personne="MVS";}
if ($_SESSION["membre"] == "menuparent") {
$destinataire=chercheIdEleve(strtolower($_SESSION["nom"]),$_SESSION["prenom"]);
$type_personne="PAR";}
if ($_SESSION["membre"] == "menueleve") {
$destinataire=chercheIdEleve(strtolower($_SESSION["nom"]),$_SESSION["prenom"]);
$type_personne="ELE";}
?>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >

<script>CreerFenetreBe();</script>
<SCRIPT language="JavaScript" <?php  print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php  include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php  top_h(); ?>
<a name=ancre>
<SCRIPT language="JavaScript" <?php  print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE5?> :  <font id='color2' ><?php print "Corbeille"  ?> </font></b></font>
<?php
if (($_SESSION["membre"] == "menuparent") && (ACCESMESSPARENT == "non"))  { $valid=1; } 
if (($_SESSION["membre"] == "menututeur") && (ACCESMESSTUTEUR == "non"))  { $valid=1; } 
if (($_SESSION["membre"] == "menueleve") && (ACCESMESSELEVE == "non")) { $valid=1; } 
if ($valid == 1) {
	if (verifdelegue($id_pers,$_SESSION["membre"],chercheIdClasseDunEleve($id_pers))) {
		if ( (MESSDELEGUEELEVE == "oui") && ($_SESSION["membre"] == "menueleve")) {   $valid=0; }
		if ( (MESSDELEGUEPARENT == "oui") && ($_SESSION["membre"] == "menuparent")) {   $valid=0; }
	}
}

if ($valid == 1) {
	print "</td></tr><tr id='cadreCentral0'><td>";
	print "<br /><center><font color=red class='T2' >".LANGMESS37.".</font></center><br /><br />";
}else{

if (FORWARDMAIL == "oui") { ?>
&nbsp;&nbsp;&nbsp; - &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="messagerie_foward.php"><font color=yellow><b><?php print LANGMESS17?> / <?php print LANGASS10 ?></b></font></a>
<?php } ?>
</td></tr>
<tr id='cadreCentral0'>
<td>


     <!-- // fin  -->
<?php
$objetF="objet";
$deF="de";
$dateF="date";
$imgFDate="&nbsp;&nbsp;<img src='image/commun/za2.png' border='0' />";
$imgFObjet="";
$imgFDe="";
if ($_GET['tri'] == "objet") { $objetF="objet2"; $imgFObjet="&nbsp;&nbsp;<img src='image/commun/za2.png' border='0' />" ; $imgFDate=""; }
if ($_GET['tri'] == "de") { $deF="de2"; $imgFDe="&nbsp;&nbsp;<img src='image/commun/za2.png' border='0' />" ; $imgFDate="";}
if ($_GET['tri'] == "date") { $dateF="date2";$imgFDate="&nbsp;&nbsp;<img src='image/commun/za2.png' border='0' />" ; }
if ($_GET['tri'] == "objet2") { $objetF="objet"; $imgFObjet="&nbsp;&nbsp;<img src='image/commun/za.png' border='0' />" ; $imgFDate="";}
if ($_GET['tri'] == "de2") { $deF="de";$imgFDe="&nbsp;&nbsp;<img src='image/commun/za.png' border='0' />" ; $imgFDate=""; }
if ($_GET['tri'] == "date2") { $dateF="date";$imgFDate="&nbsp;&nbsp;<img src='image/commun/za.png' border='0' />" ; }


?>
<form method="post" name="form2" action="messagerie_reception.php" >
<TABLE border=0 width=100% >
<TR>
<TD >&nbsp;<input type="checkbox" onclick="validecase();" name="tous" value="1" id='checkbox1'  class="css-checkbox" /><label for='checkbox1' name='checkbox1_lbl' class='css-label lite-red-check'></label></TD>
<TD ><a href='messagerie_reception.php?tri=<?php print $objetF ?>&idrep=<?php print $idrep ?>' ><font class=T2><?php print LANGTE5?></font><?php print $imgFObjet ?></a></TD>
<TD  width=30%><a href='messagerie_reception.php?tri=<?php print $deF ?>&idrep=<?php print $idrep ?>' ><font class=T2><?php print ucwords(LANGTE3)?></font><?php print $imgFDe ?></a></TD>
<TD align=center width=15% ><a href='messagerie_reception.php?tri=<?php print $dateF ?>&idrep=<?php print $idrep ?>' ><font class=T2><?php print LANGTE7?></font><?php print $imgFDate ?></a></TD>
</TR>
<br>

&nbsp;&nbsp;&nbsp;&nbsp;<?php print LANGMESS46 ?> : <select name='outil2' onChange="this.form.submit()";>
<option value='0' id='select0'><?php print LANGCHOIX ?></option>
<option value='-2' id='select1'><?php print LANGTMESS417 ?></option>
<?php

for($u=0;$u<count($data_rep);$u++) {
	$nb=0;
	$nb=nbmessagerep($data_rep[$u][0],$id_pers,$type_personne);
	$selected="";
	if ($idrep == $data_rep[$u][0]) $selected="selected='selected'"; 
	print "<option value='".$data_rep[$u][0]."' id='select1' $selected  >".trunchaine($data_rep[$u][1],15)." ($nb) </option>";
}
?>
<?php
$checkedmessclassic="";
if ($messclassic == 'classic') { $checkedmessclassic="selected='selected'"; } 

?>
	</select>&nbsp;&nbsp;&nbsp; <select  name='messclassic' onChange="this.form.submit()">
					<option value='' id='select1' ><?php print LANGNON ?></option>
					<option value='classic' <?php print $checkedmessclassic ?> id='select1' ><?php print LANGOUI ?></option>
				    </select>&nbsp;&nbsp;&nbsp;<?php print LANGTMESS418 ?>


</form>
<br><br>



<form method="POST" name="form1" >
<!-- message -->
<?php
//---------
$fichier="messagerie_reception.php#idrep=$idrep&tri=".$_GET['tri'];
$table="messageries";
if ($idrep == "") {
	$req2="AND ( repertoire IS NULL OR repertoire='0') ";
}else{
	$req2="AND repertoire='$idrep'";
}
$requete="WHERE destinataire='$destinataire' $req2 ";

if ($_GET['tri'] == "objet") 	{ $orderby="objet"; $requete.=" ORDER BY objet, date DESC, heure DESC";  }
if ($_GET['tri'] == "objet2") 	{ $orderby="objet2"; $requete.=" ORDER BY objet DESC, date DESC, heure DESC";  }
if ($_GET['tri'] == "de") 	{ $orderby="de"; $requete.=" ORDER BY emetteur, date DESC, heure DESC";   }
if ($_GET['tri'] == "de2") 	{ $orderby="de2"; $requete.=" ORDER BY emetteur DESC, date DESC, heure DESC";  }
if ($_GET['tri'] == "date")	{ $orderby="date";  $requete.=" ORDER BY date, heure DESC"; }
if ($_GET['tri'] == "date2") 	{ $orderby="date2"; $requete.=" ORDER BY date DESC, heure DESC"; } 



$nbaff=20;
if ((isset($_GET["nba"])) && ($deb != 1)){
	$depart=$_GET["limit"];
}else {
	$depart=0;
}
$data=affichage_messagerie_limit($type_personne,$destinataire,$depart,$nbaff,$idrep,$orderby,'1');
// id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, lu_par_utilisateur, idpiecejointe, impression, alerte, corbeille
for($i=0;$i<count($data);$i++) {
	$impression=$data[$i][12];
	$alerte=$data[$i][13];
	if ( ($_SESSION["membre"] == 'menuadmin') || ($_SESSION["membre"] == 'menuscolaire') || ($_SESSION["membre"] == 'menuprof') || ($_SESSION["membre"] == 'menuparent') || ($_SESSION["membre"] == 'menueleve')  || ($_SESSION["membre"] == 'menututeur') || ($_SESSION["membre"] == 'menupersonnel') ) :

		if (fichierJointExiste($data[$i][11])) { 
			$imgpiecejointe="<img src='image/attach.gif' align='center' border='0' title=\"".LANGTMESS414."\" />"; 
		}else{ 
			$imgpiecejointe="";  
		}

		if ($impression == 1) { 
			$imgimpression="<img src='image/commun/valid.gif' align='center' border='0' title=\"".LANGTMESS413."\" />"; 
		}else{ 
			$imgimpression="";  
		}


$reponse_poubelle="<TR><td  bordercolor='#FFFFFF' colspan='4' ><input type=submit name='suppmess' value='".LANGBT50."' class='button' > <input type='submit' value='Restaurer' name='restaurer' class='button' /> ";
$reponse_poubelle.="&nbsp;&nbsp;&nbsp; ";
$reponse_poubelle.=" <input type=submit value='ok' name='creatrep' id='creatrep' style='visibility:hidden'  />";

$reponse_poubelle.="</TD></TR>";
$reponse_checkbox="<input type=checkbox name=saisie_poubelle_".$i." onClick=\"DisplayLigne('tr".$i."')\"  >";
$hidden="<input type=hidden name=saisie_id_poubelle_".$i." value=".$data[$i][0]." >";
$hidden_nb="<input type=hidden name=saisie_nb value=".count($data).">";
         endif ;

	 $bold="<b>";
	 $finbold="</b>";
	 $imgc="lettre.gif";
	 if (DBTYPE == "mysql" ) {
		if ($data[$i][10] == "1") { $bold=""; $finbold=""; $imgc="lettrelu.gif"; }
	 }
	 if (DBTYPE == "pgsql") {
		if ($data[$i][10] == "t") {  $bold=""; $finbold=""; $imgc="lettrelu.gif"; }
	 }

	 if ($alerte ==  1) {
		 $alerte="<img src='image/commun/alerte.png' align='bottom' title='Alerte Message' />";
		 $alerteC="<font color='red'>";
		 $alerteCC="</font>";
	 }else{
		 $alerte="";
		 $alerteC="";
		 $alerteCC="";
	 }
?>
<TR id="tr<?php print $i ?>" class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
<TD width=5% ><?php  print "$reponse_checkbox"; print "$hidden";?></TD>
<TD ><?php print $imgimpression ?><?php print $alerte ?><?php print $bold?>
<?php  if ($messclassic == "classic") { ?>
	<A href='#' title="<?php print stripslashes(trim($data[$i][8])) ?>" onClick="open('./messagerie_reception_message.php?saisie_id_message=<?php print $data[$i][0]?>','messagerie','width=740,height=600,menubar=no,resizable=no,scrollbars=YES,status=no,toolbar=no'); return true;"><?php print $alerteC ?><img src='./image/commun/<?php print $imgc?>' align=center border=0 alt='Message'> <?php print $imgpiecejointe?> <?php print  trunchaine(stripslashes(trim($data[$i][8])),'35')?><?php print $alerteCC ?></A><?php print $finbold?>
<?php }else{ ?>
	<A href='#' title="<?php print stripslashes(trim($data[$i][8])) ?>"  onClick="return apercu('./messagerie_reception_message.php?saisie_id_message=<?php print $data[$i][0]?>&et=1')"><?php print $alerteC ?><img src='./image/commun/<?php print $imgc?>' align=center border=0 alt='Message'> <?php print $imgpiecejointe?> <?php print  trunchaine(stripslashes(trim($data[$i][8])),'35')?><?php print $alerteCC ?></A><?php print $finbold?>
<?php } ?>
</TD>

<TD>
<?php print $bold?>
<?php
$titre="";
if ((trim($data[$i][7]) == "ADM")||(trim($data[$i][7]) == "ENS")||(trim($data[$i][7]) == "MVS")||(trim($data[$i][7]) == "TUT")||(trim($data[$i][7]) == "PER")) {
	$emetteur=recherche_personne($data[$i][1]);
}else {
	if ($data[$i][7] == "ELE") {
		$titre="<i>".INTITULEELEVE." : </i><br>";
	}
	if ($data[$i][7] == "PAR") {
			$titre="<i>".LANGMESS62." : </i><br>";
	}
	$emetteur=recherche_eleve($data[$i][1]);
}
print $titre.$emetteur;
?>
<?php print $finbold?>
</TD>
<TD align=center width=20%><?php print $bold?><?php print dateForm($data[$i][4])?> <BR> <?php print $data[$i][5]?><?php print $finbold ?></TD>
</TR>
<?php
}
?>

<!-- fin message -->
<tr><TD height=10  colspan=4></TD></TR>
<?php  print "$reponse_poubelle"; print $hidden_nb; ?>
</table>
<table width=100% border=0 >
<tr><td align=left width=33%><br>&nbsp;<?php precedent0($fichier,$table,$depart,$nbaff,$requete); ?><br><br></td>
<td align=right width=33%><br><?php suivant0($fichier,$table,$depart,$nbaff,$requete); ?>&nbsp;<br><br></td>
</tr></table>
<?php } ?>

<!-- // fin  -->
</td></tr></table>
</form>
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
<?php include_once("./librairie_php/finbody.php"); ?>

     </BODY></HTML>
