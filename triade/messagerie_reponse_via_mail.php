<?php
error_reporting(0);
session_start();
if (!isset($_SESSION["id_pers"]) || (trim($_SESSION["id_pers"]) == "") ) {
     header("Location: consult.php");
}
if (!isset($_GET["saisie_id_message"])) {
	print "Erreur d'accès !";
	exit;
}

include_once("./common/config.inc.php");
include_once("./librairie_php/langue.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$data=affichage_messagerie_message($_GET["saisie_id_message"]);
if (count($data) == 0) {
	print "Erreur d'accès !";
	exit;
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
 *<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>

 ***************************************************************************/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_verif_message.js"></script>
<script type="text/javascript" src="./ckeditor/ckeditor.js"></script>
<title>Triade - Messagerie</title>
</head>
<body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<!-- // texte du menu qui defile   -->
<!-- // fin du texte   -->
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="85">
<tr bgcolor="#CCCCCC">
<td >
<!-- // fin  -->
<?php
//  id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest
// $data : tab bidim - soustab 3 champs
for($i=0;$i<count($data);$i++) {
	$qui_envoi=$data[$i][7];
	$dou_envoi=$data[$i][9];
 	$number=$data[$i][10];
if ((trim($data[$i][7]) == "ADM")||(trim($data[$i][7]) == "ENS")||(trim($data[$i][7]) == "MVS")||(trim($data[$i][7]) == "TUT")) {
      $destinataire=recherche_personne($data[$i][1]);
}else {
	$destinataire=recherche_eleve($data[$i][1]);
}




?>
<form name="formulaire" method=post onsubmit='return verif_message_envoi()' action='./messagerie_enr_via_mail.php' >
<BR>
<font class='T2'>
<?php
$person_emetteur=recherche_personne($_SESSION["id_pers"]);
?>
&nbsp;&nbsp;<?php print ucwords(LANGTE3)?> : <input type=text name="saisie_emetteur" value="<?php print $person_emetteur ?>" onfocus=this.blur() maxlength='40' size=35 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" readonly="readonly" >
<input type=hidden name="idemetteur" value="<?php print $_SESSION["id_pers"] ?>">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print ucwords(LANGTE5)?> : <input type=text name="saisie_objet" size=40 maxlength=50 value="RE: <?php print stripslashes($data[$i][8])?> " >
<BR><BR>
&nbsp;&nbsp;<?php print LANGTE6?> : <input type=text name="saisie_destinataire" onfocus="this.blur()" size=30 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" value="<?php print $destinataire?>" >
<input type=hidden name="saisie_destinataire"  value="<?php print $data[$i][1]?>" >
<BR><BR><BR>
<?php
$messageencours="<br><br><hr>";
$messageencours.="> <i>".LANGMESS31.": $destinataire</i><br>";
$messageencours.="> <i>".LANGTE12." ".dateForm($data[$i][4])." ".LANGTE13." ".$data[$i][5]."</i>";
$messageencours.=Decrypte($data[$i][3],$number);
$messageencours=stripslashes($messageencours);
$messageencours=preg_replace('#\"#','',$messageencours);
$messageencours=preg_replace('/<p>\&nbsp;<\/p>/','',$messageencours);
$messageencours=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$messageencours);
$messageencours=stripslashes($messageencours);                                      
?>
<textarea id="editor" name="resultat" ><?php print $messageencours ?></textarea>
<script type="text/javascript">
var colorGRAPH='<?php print GRAPH ?>';
//<![CDATA[
CKEDITOR.replace( 'editor', {
	height: '300px'
	} );
//]]>
</script>

</font>

<input type=hidden name="saisie_type_personne_dest" value="<?php print $qui_envoi?>" >
<input type=hidden name="saisie_type_personne_emetteur" value="<?php print $dou_envoi?>" >
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit2('<?php print LANGBT4?>','rien','<?php print LANGBT5 ?>'); //text,nomInput</script>
</td></tr></table>
</form>
<!-- // fin  -->
</td></tr></table>
<?php
}
?>
</BODY></HTML>
