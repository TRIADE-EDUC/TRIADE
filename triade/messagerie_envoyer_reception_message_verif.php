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
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<title>TRIADE - Messagerie</title>
</head>
<body bgcolor='#FFFFFF' >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$data=affichage_messagerie_envoyer_message($_GET["saisie_id_message"]);
//  id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,idforward_mail,idpiecejointe
// $data : tab bidim - soustab 3 champs
for($i=0;$i<count($data);$i++)
{
	$number=$data[$i][10];
	$idDest=$data[$i][2];
	$typeDest=$data[$i][9];
?>

<table width="100%" height=100% border="0" bordercolor="#000000">
  <tr valign="top" bgcolor="#FFFFFF">
    <td height="117" id=bordure>
      <table width="100%" border="1" bgcolor="#CCCCCC" style="-webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; padding:5px " >
        <tr>
          <td  bgcolor="#FFFFFF" height="16"><table width=100% border='0' ><tr><td align=left>
            <div align="left"><?php print ucwords(LANGTE3)?> : 
	<?php
if ((trim($data[$i][7]) == "ADM")||(trim($data[$i][7]) == "ENS")||(trim($data[$i][7]) == "MVS") ||(trim($data[$i][7]) == "TUT") ||(trim($data[$i][7]) == "PER")  ) {
        $emetteur=recherche_personne($data[$i][1]);
}else {
        $emetteur=recherche_eleve($data[$i][1]);
}

print $emetteur;

if ((trim($typeDest) == "ADM")||(trim($typeDest) == "ENS")||(trim($typeDest) == "MVS") ||(trim($typeDest) == "TUT") ||(trim($typeDest) == "PER") ) {
	$destinataire=recherche_personne($idDest);
	$classe=$classeAffiche="";
}else {
        $destinataire=recherche_eleve($idDest);
	$classe=chercheClasse_nom(chercheIdClasseDunEleve($idDest));
	$classeAffiche="<span title=\"$classe\">(".trunchaine($classe,15).")</span>";
}

print " à ";
print "<b>".$destinataire."</b>";
print " ".$classeAffiche;



        ?>	
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
&nbsp;&nbsp;
&nbsp;&nbsp;
&nbsp;&nbsp;
<a href='#' onclick="imprimer();"><img src="./image/print.gif" align="absmiddle" alt="Imprimer" border=0></A>
            </div>
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
	<i>Pièce(s) Jointe(s) :</i> <a href="#" onclick="AffBulleAvecQuit('Liste des fichiers disponibles','image/commun/info.jpg','<?php print $listingdll ?>'); return false;" ><img src="image/commun/download.png"  border='0' /></a>
<?php } ?>
</td></tr></table>
<?php 
$message=Decrypte($data[$i][3],$number);
$message=stripslashes($message);
$message=preg_replace('/<p>\&nbsp;<\/p>/','',$message);
$message=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$message);

?>
<table  align=center width=97% border=0><tr><td><div style=''><?php print stripslashes($message); ?></div>
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
