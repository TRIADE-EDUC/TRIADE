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
<title>TRIADE - Messagerie</title>
</head>
<body bgcolor='#FFFFFF' >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$data=affichage_messagerie_message($_GET["saisie_id_message"]);
// $data : tab bidim - soustab 3 champs
for($i=0;$i<count($data);$i++)
{
	$number=$data[$i][10];
?>

<table width="100%" height=100% border="1" bordercolor="#000000">
  <tr valign="top" bgcolor="#FFFFFF">
    <td height="117" >
      <table width="100%" border="1" bgcolor="#CCCCCC">
        <tr>
          <td  bgcolor="#FFFFFF" height="16">
            <div align="left"><?php print ucwords(LANGTE3)?> : 
	<?php
if ((trim($data[$i][7]) == "ADM")||(trim($data[$i][7]) == "ENS")||(trim($data[$i][7]) == "MVS")||(trim($data[$i][7]) == "TUT")||(trim($data[$i][7]) == "PER")) {
        $emetteur=recherche_personne($data[$i][1]);
	$classe=$classAffiche="";
}else {
        $emetteur=recherche_eleve($data[$i][1]);
	$classe=chercheClasse_nom(chercheIdClasseDunEleve($data[$i][1]));
	$classeAffiche="<span title=\"$classe\">".trunchaine($classe,15)."</span>";
}
	print $emetteur;
	print $classeAffiche;
        ?>	
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
            <div align="center"><a href='#ancre' onclick="open('messagerie_reponse.php?saisie_id_message=<?php print $data[$i][0]?>','_parent','')" ><img src="./image/repondre.gif" align="absmiddle" alt="Répondre" border=0></A>
&nbsp;&nbsp;
<a href='#' onclick="imprimer();"><img src="./image/print.gif" align="absmiddle" alt="Imprimer" border=0></A>
            </div>
          </td>
        </tr>
      </table>
<UL>
<?php 
$message=Decrypte($data[$i][3],$number);
$message=preg_replace('/<p>\&nbsp;<\/p>/','',$message);
?>
<?php print stripslashes(Decrypte($message)) ?>
</UL>
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
</body>
</html>
