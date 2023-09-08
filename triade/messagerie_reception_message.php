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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./FCKeditor/editor/css/fck_editorarea.css">
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto_mail.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_imprmessage.js"></script>
<title>TRIADE - Messagerie</title>
</head>
<body bgcolor='#FFFFFF' >
<div name="a"></div>
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();
valide_message_lu($_GET["saisie_id_message"]);


$data=affichage_messagerie_message($_GET["saisie_id_message"]); 
// id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,idforward_mail,idpiecejointe,idgroupe
// $data : tab bidim - soustab 3 champs
for($i=0;$i<count($data);$i++)
{
	$number=$data[$i][10];
	$idgroupe=$data[$i][12];
	$idmessage=$data[$i][0];
?>

<table width="100%" height=100% border="0" bordercolor="#000000">
  <tr valign="top" bgcolor="#FFFFFF">
    <td height="117" >
      <table width="100%" border="1" bgcolor="#CCCCCC" style="-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; padding:5px " >
        <tr>
          <td  bgcolor="#FFFFFF" height="16"><table width=100%><tr><td align=left>
            <div align="left"><?php print ucwords(LANGTE3)?> : <b>
		<?php
		if ((trim($data[$i][7]) == "ADM")||(trim($data[$i][7]) == "ENS")||(trim($data[$i][7]) == "MVS") ||(trim($data[$i][7]) == "TUT")  ||(trim($data[$i][7]) == "PER") ) {
			$emetteur=recherche_personne($data[$i][1]);
			$classe="";$classeAffiche="";
		}else {
	        	$emetteur=recherche_eleve($data[$i][1]);
			$classe=chercheClasse_nom(chercheIdClasseDunEleve($data[$i][1]));
			$classeAffiche="</b><span title=\"$classe\"> (en classe de ".trunchaine($classe,15).")</span>";
		}
		print $emetteur;
		print $classeAffiche;
		if (($idgroupe != 0) && ($idgroupe != null) && (!cacherGrpMail($idgroupe)) ) {
			$libelleGroupe=rechercheLibelleGroupeMail($idgroupe);
		?>
		<script>
		var etat2=0;
		function bul2() {
			if (etat2 == 0) {
				AffBulle3('<?php print LANGMESS64 ?>','./image/commun/info.jpg',"");
				listingGroupeMail("<?php print $idgroupe?>");
				etat2=1;
			}else{
				HideBulle();
				etat2=0;
			}
		}
		</script>
		<?php
		print "&nbsp;[ <a href='#anc1' onclick='bul2(); return false'  >$libelleGroupe</a> ]&nbsp;&nbsp;";
		}
        	?>
		</b></td></tr></table>
	</div>
          </td>
          <td  bgcolor="#FFFFFF" height="16">
       <div align="center"><?php print dateForm($data[$i][4])?> - <?php print $data[$i][5]?></div>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="19" valign=top colspan='2'>
		<table width='100%'><tr><td><?php print LANGTE5?> : <?php print stripslashes($data[$i][8])?></td>
         
		<td><td align='right'>
	    <?php 
		if (($_SESSION["membre"] == "menuparent") && (ACCESMESSENVOIPARENT == "non"))  { $valid=1; } 
		if (($_SESSION["membre"] == "menueleve") && (ACCESMESSENVOIELEVE == "non")) { $valid=1; } 

		if ((ACCESMESSENVOIPARENT == "non") && (MESSDELEGUEELEVE == "oui") && ($_SESSION["membre"] == "menueleve")){
			$valid=(verifdelegue($_SESSION["id_pers"],$_SESSION["membre"],chercheIdClasseDunEleve($_SESSION["id_pers"]))) ? 0 : 1 ;
		}

		if ((ACCESMESSENVOIPARENT == "non") && (MESSDELEGUEPARENT == "oui") && ($_SESSION["membre"] == "menuparent")){
			$valid=(verifdelegue($_SESSION["id_pers"],$_SESSION["membre"],chercheIdClasseDunEleve($_SESSION["id_pers"]))) ? 0 : 1 ;
		}

		if ($valid != 1) {
			if (($_SESSION["navigateur"] == "IE") || ($_GET['et'] == '1')) {
				if (trim($_COOKIE["messmodelecture"]) != "classic") {
					print "<a href='#ancre' onclick=\"open('messagerie_reponse.php?et=".$_GET['et']."&saisie_id_message=".$data[$i][0]."','_top','')\" title='Répondre' >";
					print "<img src='./image/commun/email_repondre.jpg' align='absmiddle' alt='Répondre' border='0'></a>&nbsp;&nbsp;";
					print "<a href='#ancre' onclick=\"open('messagerie_envoi.php?saisie_id_message=".$data[$i][0]."&f=1','_top','')\" title='Transfert' >";
					print "<img src='./image/commun/email_forward.jpg' align='absmiddle' alt='Répondre' border='0'></a>&nbsp;&nbsp;";
				}else{
					print "<a href='#ancre' onclick=\"open('messagerie_reponse2.php?saisie_id_message=".$data[$i][0]."','_self','')\" title='Répondre' >";
					print "<img src='./image/commun/email_repondre.jpg' align='absmiddle' alt='Répondre' border='0'></a>&nbsp;&nbsp;";
					print "<a href='#ancre' onclick=\"open('messagerie_envoi.php?saisie_id_message=".$data[$i][0]."&f=1','_self','')\" title='Transfert' >";
					print "<img src='./image/commun/email_forward.jpg' align='absmiddle' alt='Répondre' border='0'></a>&nbsp;&nbsp;";
				}
			}else{
				print "<a href='#ancre' onclick=\"open('messagerie_reponse2.php?saisie_id_message=".$data[$i][0]."','_self','')\" title='Répondre' >";
				print "<img src='./image/commun/email_repondre.jpg' align='absmiddle' alt='Répondre' border='0'></A>&nbsp;&nbsp;";
				print "<a href='#ancre' onclick=\"open('messagerie_envoi.php?saisie_id_message=".$data[$i][0]."&f=1','_self','')\" title='Transfert' >";
				print "<img src='./image/commun/email_forward.jpg' align='absmiddle' alt='Répondre' border='0'></a>&nbsp;&nbsp;";
			}
		}
	?>
<?php 
$message=Decrypte($data[$i][3],$number);
$message=strip_tags($message);
$message=stripslashes($message);
$message=preg_replace('/<p>\&nbsp;<\/p>/','',$message);
$message=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$message);
$message="Message automatique : Une circulaire a été déposée à votre attention.";
?>

		<a href='#a' onclick="alerteMessage('<?php print $data[$i][0] ?>')" title='Alerte Message' ><img src="./image/commun/email_alerte.jpg" align="absmiddle" alt="Alerte Message" border='0' /></a>
<!--       		<a href='#a' onClick="ecoutermessage('<?php print $message ?>');" title='Ecouter' ><img src="./image/commun/email_son.jpg" align="absmiddle" alt="Ecouter" border='0' /></a>  -->
       		<a href='#a' onclick="imprimerMessage();" title='Imprimer' ><img src="./image/commun/email_imprimer.jpg" align="absmiddle" alt="Imprimer" border='0' /></a>
            </td></tr></table>
          </td>
        </tr>
      </table>
<table  width="100%"  border="0" bordercolor="#000000" ><tr><td> 
<?php    
$tabficJ=fichierJointExiste($data[0][11]); // md5,nom
if (count($tabficJ) > 0) {  
	$listingdll="<font class=T1>";
	for($j=0;$j<count($tabficJ);$j++) {
		$nom=$tabficJ[$j][1];
		$md5=$tabficJ[$j][0];
		$listingdll.=" - <a href=\'accessfichier.php?id=$md5\' target=\'_blank\'>".$nom."</a><br />";
	}
	$listingdll.="</font>";
?>
	<i>Pi&egrave;ce(s) Jointe(s) :</i> <a href="#" onclick="AffBulleAvecQuit('Liste des fichiers disponibles','image/commun/info.jpg','<?php print $listingdll ?>'); return false;" ><img src="image/commun/download.png"  border='0' /></a>
<?php } ?>
</td></tr></table>
<br>
<?php 
$message=Decrypte($data[$i][3],$number);
$message=stripslashes($message);
$message=preg_replace('/<p>\&nbsp;<\/p>/','',$message);
$message=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$message);
?>
<table align=center width=97% border=0><tr><td><div id='editor' ><?php print stripslashes($message) ?></div>
<br><br><br>
<hr>
<div align='center'><?php top_p();?></div></td></tr></table>
</td>
</tr>
</table>
<?php
	$destinataire=chercheIdEleve(strtolower($_SESSION["nom"]),$_SESSION["prenom"]);
	if ($destinataire == $data[$i][2]) {
		lecture_message($data[$i][0]);
	}
}
Pgclose();
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<script>
function imprimerMessage() {
	var ok=confirm(langfunc3);
        if (ok) {
		window.print();
		flagImpMessage('<?php print $idmessage ?>');
        }
	
}

function ecoutermessage(message) {
	var language="fr-fr";	
	var url="https://ia.triade-educ.net/apitext-to-speech.php?productId=<?php print $productId ?>&message="+encodeURIComponent(message)+"&lang="+language;
	open(url,'son','width=30,height=30');
}


alerteMessage = function (id) {
	var myAjax = new Ajax.Request(
		"ajaxAlerteMessage.php",
		{	method: "post",
			parameters : "id="+id,
			asynchronous: true,
			timeout: 5000,
			onComplete: infoText
		}
	)
}

infoText = function (request) { alert(request.responseText); }


</script>
</body>
</html>
