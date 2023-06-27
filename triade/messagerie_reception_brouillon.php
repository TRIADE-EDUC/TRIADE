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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./FCKeditor/editor/css/fck_editorarea.css">
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto_mail.js"></script>
<title>TRIADE - Messagerie</title>
</head>
<body id='bodyfond2'>
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$data=affichage_messagerie_message($_GET["saisie_id_message"]); 
// id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,idforward_mail,idpiecejointe,idgroupe
// $data : tab bidim - soustab 3 champs
for($i=0;$i<count($data);$i++)
{
	$number=$data[$i][10];
	$idgroupe=$data[$i][12];
?>

<table width="100%" height=100% border="0" bordercolor="#000000">
  <tr valign="top" bgcolor="#FFFFFF">
    <td height="117" >
      <table width="100%" border="1" bgcolor="#CCCCCC">
        <tr>
          <td  bgcolor="#FFFFFF" height="16"><table width=100%><tr><td align=left>
            <div align="left"><?php print ucwords(LANGTE3)?> : <b>
		<?php
		if ((trim($data[$i][7]) == "ADM")||(trim($data[$i][7]) == "ENS")||(trim($data[$i][7]) == "MVS") ||(trim($data[$i][7]) == "TUT")  ||(trim($data[$i][7]) == "PER") ) {
	       		$emetteur=recherche_personne($data[$i][1]);
		}else {
	        	$emetteur=recherche_eleve($data[$i][1]);
		}
		print $emetteur;
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
		</b></td><td align=right width=40%> 
		<?php  
			$ficJ=fichierJointExiste($data[0][11]);
			if ($ficJ != "" ) { 
		?>
	
			<i>Pièce Jointe :</i> <a href="accessfichier.php?id=<?php print $ficJ ?>" ><img src="image/commun/download.png"  border='0' /></a>
		<?php } ?>
		</td></tr></table>
	</div>
          </td>
          <td  bgcolor="#FFFFFF" height="16">
       <div align="center"><?php print dateForm($data[$i][4])?> - <?php print $data[$i][5]?></div>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="19" valign=top>
            <div align="left" ><?php print LANGTE5?> : <?php print $data[$i][8]?></div>
          </td>
	  <td height="19" width="18%">
		<div align="center">
	    <?php 
		if (($_SESSION["membre"] == "menuparent") && (ACCESMESSENVOIPARENT == "non"))  { $valid=1; } 
		if (($_SESSION["membre"] == "menueleve") && (ACCESMESSENVOIELEVE == "non")) { $valid=1; } 

		if ((MESSDELEGUEELEVE == "oui") && ($_SESSION["membre"] == "menueleve")){
			$valid=(verifdelegue($_SESSION["id_pers"],$_SESSION["membre"],chercheIdClasseDunEleve($_SESSION["id_pers"]))) ? 0 : 1 ;
		}

		if ((MESSDELEGUEPARENT == "oui") && ($_SESSION["membre"] == "menuparent")){
			$valid=(verifdelegue($_SESSION["id_pers"],$_SESSION["membre"],chercheIdClasseDunEleve($_SESSION["id_pers"]))) ? 0 : 1 ;
		}

	
		if ($_SESSION["navigateur"] == "IE") {
			print "[ <a href='#ancre' onclick=\"open('messagerie_reponse_brouillon.php?saisie_id_message=".$data[$i][0]."','_top','')\" >";
			print "<b>Valider</b></A> ]&nbsp;&nbsp;";
		}else{
			print "[ <a href='#ancre' onclick=\"open('messagerie_reponse_brouillon2.php?saisie_id_message=".$data[$i][0]."','_self','')\" >";
			print "<b>Valider</b></A> ]&nbsp;&nbsp;";
		}
	    ?>
            </div>
          </td>
        </tr>
      </table>
<br>
<?php 
$message=Decrypte($data[$i][3],$number);
$message=stripslashes($message);
$message=preg_replace('/<p>\&nbsp;<\/p>/','',$message);
?>
<table  align=center width=97% border=0><tr><td><div style=''><?php print stripslashes($message) ?></div>
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
</body>
</html>
