<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
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
<?php
$urlbrouillon="&brouillon=0";
$idbrouillon=0;
if (isset($_GET["brouillon"])) {
	$brouillon=" <font color='color3'>(Type Brouillon)</font>";
	$urlbrouillon="&brouillon=1";
	$idbrouillon=1;
}
?>
<html >
	<head>
		<?php include_once("./common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="./favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
		<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
	</head>
	<body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
		<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
		<script type="text/javascript" src="./librairie_js/verif_creat.js"></script>
		<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
		<script type="text/javascript" src="./librairie_js/function.js"></script>
		<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
		<?php include("./librairie_php/lib_licence.php"); ?>
		<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
		<?php include("./librairie_php/lib_defilement.php"); ?>
		</TD><td width="472" valign="middle" rowspan="3" align="center">
		<div align='center'><?php top_h(); ?>
		<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
		<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS1?><?php print dateDMY().$brouillon  ?></font></b></td></tr>
		<tr id='cadreCentral0'><td >
		<!-- // fin  -->
		<br />
		<table border=0 align=center>
<?php
include_once("librairie_php/db_triade.php");
// connexion P
$cnx=cnx();

if (isset($_GET["f"])) {
	$forwarding=$_GET["f"];
	$idMessage=$_GET["saisie_id_message"];
	if ($idMessage > 0) {
		print "<tr><td colspan='2' height='50' align='center'><font class='T2' id='color3' >".LANGMESST704."</font></td></tr>"; 
	}
}

if ($_GET["autorise"] == "non") {
	print "<script>alert(\"".LANGMESST705."\")</script>";
	history_cmd($_SESSION["nom"],"MESSAGERIE","Tentative d'envoi pour ".$_GET["aqui"]);
}

$valid=0;

if (($_SESSION["membre"] == "menututeur") && (ACCESMESSENVOITUTEUR == "non"))  { $valid=1; } 
if (($_SESSION["membre"] == "menuparent") && (ACCESMESSENVOIPARENT == "non"))  { $valid=1; } 
if (($_SESSION["membre"] == "menueleve") && (ACCESMESSENVOIELEVE == "non")) { $valid=1; } 
if ($brouillon == 1) { $valid=0; }

if ($valid == 1) {
	if (verifdelegue($_SESSION["id_pers"],$_SESSION["membre"],chercheIdClasseDunEleve($_SESSION["id_pers"]))) {
		if ( (MESSDELEGUEELEVE == "oui") && ($_SESSION["membre"] == "menueleve")) {   $valid=0; }
		if ( (MESSDELEGUEPARENT == "oui") && ($_SESSION["membre"] == "menuparent")) {   $valid=0; }
	}
}


if ($valid == 1) {
	print "<tr><td><center><font color=red class='T2' >".LANGMESS37.".</font></center></td></tr>";
}else{

$mailperso=mess_mail_forward($_SESSION["nom"],$_SESSION["prenom"],$_SESSION["id_pers"],$_SESSION["membre"]);
if ((MAILEXTERNE == "oui") && (ValideMail($mailperso))){
	if (  	(($_SESSION["membre"] == "menueleve") && (ELEVEENVOIEXT == "oui")) || 
		($_SESSION["membre"] == "menuadmin" ) ||
		(($_SESSION["membre"] == "menuprof" ) && (PROFENVOIEXT == "oui"))  ||
		(($_SESSION["membre"] == "menuparent" ) && (PARENTENVOIEXT == "oui"))||
		($_SESSION["membre"] == "menuscolaire" ) ||
		(($_SESSION["membre"] == "menupersonnel" ) && (PERSONNELENVOIEXT == "oui"))||
		(($_SESSION["membre"] == "menututeur" ) && (TUTEURENVOIEXT == "oui")) )
	{  	


?>
			<tr><td align=right><font class="T2"><?php print LANGMESS45 ?></font></td><td> <input type=button onclick="open('messagerie_envoi_suite.php?saisie_envoi=mailexterne<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
			<tr><td height=10></td></tr>
<?php }    

}   ?>

<?php if (  	(($_SESSION["membre"] == "menueleve") && (ELEVEENVOIDIREC == "oui")) || 
		($_SESSION["membre"] == "menuadmin" ) ||
		(($_SESSION["membre"] == "menuprof" ) && (PROFENVOIDIREC == "oui"))  ||
		(($_SESSION["membre"] == "menuparent" ) && (PARENTENVOIDIREC == "oui"))||
		($_SESSION["membre"] == "menuscolaire" ) ||
		($_SESSION["membre"] == "menupersonnel" ) ||
		(($_SESSION["membre"] == "menututeur" )  && (TUTEURENVOIDIREC == "oui"))   )

{  ?>
			<tr><td align=right><font class="T2"><?php print LANGMESS2?></font></td><td> <input type=button onclick="open('messagerie_envoi_suite.php?saisie_envoi=administrateur<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
			<tr><td height=10></td></tr>
<?php } ?>
<?php //----------------------------  ?>
<?php if (  	(($_SESSION["membre"] == "menueleve") && (ELEVEENVOISCOLAIRE == "oui")) || 
		($_SESSION["membre"] == "menuadmin" ) ||
		(($_SESSION["membre"] == "menuprof" ) && (PROFENVOISCOLAIRE == "oui")) ||
		(($_SESSION["membre"] == "menuparent" ) && (PARENTENVOISCOLAIRE == "oui")) ||
		($_SESSION["membre"] == "menuscolaire" ) ||
		($_SESSION["membre"] == "menupersonnel" ) ||
		(($_SESSION["membre"] == "menututeur" )  && (TUTEURENVOISCOLAIRE == "oui")) )

{  ?>
			<tr><td align=right><font class="T2"><?php print LANGMESS3?></font></td><td> <input type=button onclick="open('messagerie_envoi_suite.php?saisie_envoi=scolaire<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
			<tr><td height=10></td></tr>

<?php } ?>
<?php //----------------------------  ?>
<?php if (  	(($_SESSION["membre"] == "menueleve") && (ELEVEENVOIPROF == "oui")) || 
		($_SESSION["membre"] == "menuadmin" ) ||
		(($_SESSION["membre"] == "menuprof" ) &&  (PROFENVOIPROF == "oui"))  ||
		($_SESSION["membre"] == "menuscolaire" ) ||
		(($_SESSION["membre"] == "menuparent" ) &&  (PARENTENVOIPROF == "oui")) ||
		(($_SESSION["membre"] == "menupersonnel" ) &&  (PERSONNELENVOIPROF == "oui")) ||
		(($_SESSION["membre"] == "menututeur" )  &&  (TUTEURENVOIPROF == "oui")) )

{  ?>
			<tr><td align=right>
		     	<font class="T2"><?php print LANGMESS4?></font></td><td> <input type=button onclick="open('messagerie_envoi_suite.php?saisie_envoi=enseignant<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
			<tr><td height=10></td></tr>

<?php } ?>
<?php //----------------------------  ?>

<?php if (  	(($_SESSION["membre"] == "menueleve") && (ELEVEENVOIDELEGUE == "oui")) || 
		($_SESSION["membre"] == "menuadmin") ||
		(($_SESSION["membre"] == "menuprof") &&  (PROFENVOIDELEGUE == "oui"))  ||
		($_SESSION["membre"] == "menuscolaire") ||
		(($_SESSION["membre"] == "menuparent") &&  (PARENTENVOIDELEGUE == "oui")) ||
		(($_SESSION["membre"] == "menututeur") &&  (TUTEURENVOIDELEGUE == "oui"))  )

{  ?>
			<tr><td align=right>
		     	<font class="T2"><?php print LANGTMESS485." : " ?></font></td><td> <input type=button onclick="open('messagerie_envoi_suite.php?saisie_envoi=delegue<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
			<tr><td height=10></td></tr>

<?php } ?>

<?php //----------------------------  ?>

<?php if (  (	(($_SESSION["membre"] == "menueleve") && (ELEVEENVOIGRPELE == "oui")) || 
		($_SESSION["membre"] == "menuadmin") ||
		(($_SESSION["membre"] == "menuprof") &&  (PROFENVOIGRPELE == "oui"))  ||
		($_SESSION["membre"] == "menuscolaire") ||
		(($_SESSION["membre"] == "menuparent") &&  (PARENTENVOIGRPELE == "oui")) ||
		(($_SESSION["membre"] == "menupersonnel" ) &&  (PERSONNELENVOIGRPELE == "oui")) ||
		(($_SESSION["membre"] == "menututeur") &&  (TUTEURENVOIGRPELE == "oui"))  )  && (!isset($_GET["brouillon"])) )

{  ?>
			<tr><td align=right valign=top>
			<font class="T2"><?php print LANGMESS173." <font class=T1>(".INTITULEELEVES.")</font> : " ?></font></td><td> <input type=button onclick="open('messagerie_envoi_suite.php?saisie_envoi=grpmailelev<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')" value="<?php print CLICKICI?>" class='bouton2' >
			<?php 
			if ( (($_SESSION["membre"] == "menuprof") && (PROFENVOIGROUPE == "oui")) || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menuadmin") ) {
				print "&nbsp;[ <a href='messagerie_creat_grpmailele.php'>".LANGMESS17bis."</a> ]<br /><br /></tr>"; 
			}
			?>
		<tr><td height=10></td></tr>	

<?php } ?>



<?php //----------------------------  ?>
<?php if (  (($_SESSION["membre"] == "menueleve") && (ELEVEENVOITUTEUR == "oui")) || 
		($_SESSION["membre"] == "menuadmin" ) ||
		(($_SESSION["membre"] == "menuprof" ) &&  (PROFENVOITUTEUR == "oui")) ||
		($_SESSION["membre"] == "menuscolaire" ) ||
		(($_SESSION["membre"] == "menuparent" ) &&  (PARENTENVOITUTEUR == "oui")) ||
		(($_SESSION["membre"] == "menututeur" )  &&  (TUTEURENVOITUTEUR == "oui")) )

{  ?>
			<tr><td align=right><font class="T2"><?php print LANGMESS176 ?></font></td><td> <input type=button onclick="open('messagerie_envoi_suite.php?saisie_envoi=tuteur<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
			<tr><td height=10></td></tr>

<?php } ?>


<?php if (  (($_SESSION["membre"] == "menueleve") && (ELEVEENVOIPERSONNEL == "oui")) || 
		($_SESSION["membre"] == "menuadmin" ) ||
		(($_SESSION["membre"] == "menuprof" ) &&  (PROFENVOIPERSONNEL == "oui")) ||
		($_SESSION["membre"] == "menuscolaire" ) ||
		(($_SESSION["membre"] == "menuparent" ) &&  (PARENTENVOIPERSONNEL == "oui")) ||
		($_SESSION["membre"] == "menupersonnel" ))

{  ?>


			<tr><td align=right><font class="T2"><?php print LANGMESS175 ?></font></td><td> <input type=button onclick="open('messagerie_envoi_suite.php?saisie_envoi=personnel<?php print $urlbrouillon ?>&f=<?php print $forwarding ?>&saisie_id_message=<?php print $idMessage ?>','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
			<tr><td height=10></td></tr>

<?php } ?>

			<tr><td align=right valign="top">
<?php
if ( ( (($_SESSION["membre"] == "menuprof") && (PROFENVOIGROUPE == "oui")) || 
($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menuadmin") ||
(($_SESSION["membre"] == "menupersonnel" ) &&  (PERSONNELENVOIPROF == "oui")) ) && (!isset($_GET["brouillon"])) )   {  
			print "<font class='T2'>".LANGMESS22." </font><br /><br /></td><td valign='top'> <input type=button onclick=\"open('messagerie_envoi_suite.php?saisie_envoi=grpmail$urlbrouillon&f=$forwarding&saisie_id_message=$idMessage','_parent','')\" value=\"".CLICKICI."\"  class='bouton2' >&nbsp;[ <a href='messagerie_creat_grpmail.php'>".LANGMESS17bis."</a> ]<br /><br /></tr>";
		}


		if ( ($_SESSION["membre"] == "menuparent") && ( GRPMAILPARENT == "oui") )  {
			print "<font class='T2'>".LANGMESS22."</font><br /><br /></td><td valign='top'> <input type=button onclick=\"open('messagerie_envoi_suite.php?saisie_envoi=grpmail$urlbrouillon&f=<?php print $forwarding ?>&saisie_id_message=$idMessage','_parent','')\" value=\"".CLICKICI."\"  class='bouton2' >&nbsp;</tr>";
		}



if ((ACCESMESSPARENT != "non") || (MESSDELEGUEPARENT != "non")) {
	 if (   (($_SESSION["membre"] == "menueleve") && (ELEVEENVOIPARENT == "oui")) || 
		($_SESSION["membre"] == "menuadmin" ) ||
		(($_SESSION["membre"] == "menuprof" ) &&  (PROFENVOIPARENT == "oui")) ||
		($_SESSION["membre"] == "menuscolaire" ) ||
		(($_SESSION["membre"] == "menuparent" ) &&  (PARENTENVOIPARENT == "oui")) ||
		(($_SESSION["membre"] == "menupersonnel" ) &&  (PERSONNELENVOIPARENT == "oui")) ||
		(($_SESSION["membre"] == "menututeur" ) &&  (TUTEURENVOIPARENT == "oui")) ) 
	 {  

	print "<tr><td align=right valign=top>";
	print "<form name='formulaire1'  action='messagerie_envoi_suite.php' method='GET' >";
	print "<input type='hidden' name='saisie_envoi' value='parent'>";
	print "<input type='hidden' name='saisie_id_message' value='$idMessage'>";
	print "<input type='hidden' name='f' value='$forwarding'>";
	print "<input type='hidden' name='brouillon' value='$idbrouillon'>";
	print "&nbsp;<font class='T2'>".LANGMESS5."</font></td><td><select name='saisie_classe' onchange='document.formulaire1.submit()' >";
	print "<option id='select0' >".LANGCHOIX."</option>";
	print "<option id='select0'  value='touslesparentsecole'  >".LANGTMESS437."</option>";
	select_classe2(35);
	print "</select></form></td><td>";
	print "<tr><td height=10></td></tr>";
	}
}

if ((ACCESMESSELEVE != "non") || (MESSDELEGUEELEVE != "non")) {
	 if (   (($_SESSION["membre"] == "menueleve") && (ELEVEENVOIELEVE == "oui")) || 
		($_SESSION["membre"] == "menuadmin" ) ||
		(($_SESSION["membre"] == "menuprof" ) && (PROFENVOIELEVE == "oui")) ||
		($_SESSION["membre"] == "menuscolaire" ) ||
		(($_SESSION["membre"] == "menuparent" )  && (PARENTENVOIELEVE == "oui")) ||
		(($_SESSION["membre"] == "menupersonnel" )  && (PERSONNELENVOIELEVE == "oui")) ||
		(($_SESSION["membre"] == "menututeur" ) && (TUTEURENVOIELEVE == "oui")) )

	{  
	print "<tr><td align=right valign=top>";
	print "<form name='formulaire2'  action='messagerie_envoi_suite.php' method='GET' >";
	print "<input type='hidden' name='saisie_envoi' value='eleve'>";
	print "<input type='hidden' name='saisie_id_message' value='$idMessage'>";
	print "<input type='hidden' name='f' value='$forwarding'>";
	print "<input type='hidden' name='brouillon' value='$idbrouillon'>";
	print "&nbsp;". "<font class='T2'>".LANGMESS44." : </font>" ."</td><td><select name='saisie_classe' onchange='document.formulaire2.submit()' >";
	print "<option id='select0' >".LANGCHOIX."</option>";
	print "<option id='select0' value='tousleselevesecole' >".LANGTMESS438." ".INTITULEELEVES."</option>";
	select_classe2(35);
	print "</select></form></td><td>";
	print "<tr><td height=10></td></tr>";
	}
}


if (  (($_SESSION["membre"] == "menueleve") && (ELEVEENVOITUTEUR == "oui")) ||
                ($_SESSION["membre"] == "menuadmin" ) ||
                (($_SESSION["membre"] == "menuprof" ) &&  (PROFENVOITUTEUR == "oui")) ||
                ($_SESSION["membre"] == "menuscolaire" ) ||
                (($_SESSION["membre"] == "menuparent" ) &&  (PARENTENVOITUTEUR == "oui")) ||
                (($_SESSION["membre"] == "menututeur" )  &&  (TUTEURENVOITUTEUR == "oui")) )

	{	 
	print "<tr><td align=right valign=top>";
        print "<form name='formulaire4'  action='messagerie_envoi_suite.php' method='GET' >";
        print "<input type='hidden' name='saisie_envoi' value='tuteurdestage'>";
        print "<input type='hidden' name='saisie_id_message' value='$idMessage'>";
        print "<input type='hidden' name='f' value='$forwarding'>";
        print "<input type='hidden' name='brouillon' value='$idbrouillon'>";
        print "&nbsp;". "<font class='T2'> Message à un tuteur de stage en  : </font>" ."</td><td><select name='saisie_classe' onchange='document.formulaire4.submit()' >";
        print "<option id='select0' >".LANGCHOIX."</option>";
        print "<option id='select0' value='touslestuteursdestage' >".LANGTMESS438." tuteurs de stage</option>";
        select_classe2(35);
        print "</select></form></td><td>";
        print "<tr><td height=10></td></tr>";

	}	


}
print "</table></UL>";
if ($_SESSION["membre"] != "menuparent") { 
	print "</form>"; 

}

brmozilla($_SESSION["navigateur"]);
print "</td></tr></table>";
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")  ){
     	print "<SCRIPT type='text/javascript' ";
       	print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       	print "</SCRIPT>";
}else{
       	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      	print "</SCRIPT>";
      	top_d();
      	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
	print "</SCRIPT>";
}
Pgclose();
include_once("./librairie_php/finbody.php");
?>
<?php include_once("./librairie_php/finbody.php"); ?>
</BODY></HTML>
