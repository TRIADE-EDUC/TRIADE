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
<?php  include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_cantine.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Gestionnaire de cantine"?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>
<?php
if ( (verifDroit($_SESSION["id_pers"],"cantine")) || ($_SESSION["membre"] == "menuadmin" )) { 
	if (isset($_GET["eid"])) { 
		$idpers=$_GET["eid"];
		$membre="menueleve";
		$nom=recherche_eleve_nom($idpers);
		$prenom=recherche_eleve_prenom($idpers); 
		$nomprenom="$nom $prenom";
	}
	if (isset($_GET["idpers"])) { 
		$idpers=$_GET["idpers"];
		$membre=$_GET["membre"];
		if ($membre == "menueleve") {
			$nom=recherche_eleve_nom($idpers);
			$prenom=recherche_eleve_prenom($idpers); 
			$nomprenom="$nom $prenom";
		}else{
			$membre=renvoiTypePersonneMembre(recherche_type_personne($idpers)); 
			$nomprenom=recherche_personne2($idpers);
		}
	}
	if (isset($_POST["saisie_pers"])) { 
		$idpers=$_POST["saisie_pers"];
		$membre=renvoiTypePersonneMembre(recherche_type_personne($idpers)); 
		$nomprenom=recherche_personne2($idpers);
	}
	if (isset($_GET["idsupp"])) {
		$idpers=$_GET["idpers2"];
		$membre=$_GET["membre"];
		if ($membre == "menueleve") {
			$nom=recherche_eleve_nom($idpers);
			$prenom=recherche_eleve_prenom($idpers); 
			$nomprenom="$nom $prenom";
		}else{
			$membre=renvoiTypePersonneMembre(recherche_type_personne($idpers)); 
			$nomprenom=recherche_personne2($idpers);
		}
		$cr=suppOperationCantine($_GET["idsupp"]);
		if ($cr) { history_cmd($_SESSION["nom"],"CANTINE","Suppression Opération sur ($nomprenom) ");}

	}


?>

<div id="viscredit" style="position:absolute;top:140;left:330;display:none;width:450px;height:290px;padding:1px;border:1px #666 solid;background-color:#ddd;z-index:1000">
		<?php   ?>
		<form><br /><br />
		&nbsp;&nbsp;&nbsp;<font class=T2><b>Cr&eacute;diter le compte de <?php print $nomprenom ?></b></font>
		<br /><br />
		<ul><table border='0' style='border-collapse:collapse;' >	
		<tr><td align='right'><font class='T2'>&nbsp;Date :</font></td><td><input type='text' name='date' size='10' value='<?php print dateDMY() ?>' /></td></tr>
		<tr><td height=20></td></tr>
		<tr><td align='right'><font class='T2'>&nbsp;Cr&eacute;dit :</font></td><td><input type='text' name='credit' size='10' value='0' onclick="this.value=''" /></td></tr>
		<tr><td height=20></td></tr>	
		<tr><td align='right'><font class='T2'>&nbsp;D&eacute;tail :</font></td><td><input type='text' name='detail' size='50' maxlength='250' /></td></tr>
		<tr><td height=20></td></tr>
		<tr><td align='right' colspan='2' ><input type='button' onclick="enrCreditCantine('<?php print $idpers ?>','<?php print $membre ?>',this.form.date.value,this.form.credit.value,this.form.detail.value,'retourenr0')" value="<?php print LANGENR ?>" class='bouton2' />
		
		&nbsp;&nbsp;<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="new Effect.Shrink('viscredit', 1)" /><br><br>
		<span id='retourenr0' style='color:red; '></span>		
		</table></ul>
		</form>
	</div>
<br><ul>
	<script language=JavaScript>buttonMagic3("<?php print "Cr&eacute;diter le compte" ?>","new Effect.Grow('viscredit', 1); return false;");</script>
	<script language=JavaScript>buttonMagic3("<?php print "R&eacute;ctualiser la page" ?>","open('cantine_compta_2.php?idpers=<?php print $idpers ?>&membre=<?php print $membre ?>','_parent','');");</script>
	<script language=JavaScript>buttonMagicRetour('cantine_compta.php','_self')</script>
</ul><br><br><br>


<table border='1' width=100% bgcolor='#FFFFFF' style='border-collapse: collapse;' >
<tr>
<td bgcolor='yellow' width=5%><font class='T2'>&nbsp;Date&nbsp;</font></td>
<td bgcolor='yellow'><font class='T2'>&nbsp;D&eacute;tail&nbsp;</font></td>
<td bgcolor='yellow' width=15% align='right' colspan=2 ><font class='T2'>&nbsp;Montant&nbsp;<?php print unitemonnaie() ?>&nbsp;</font></td>
</tr>

<?php 
$data=recupComptaPers($idpers,$membre); //date,prix,plateau
for($i=0;$i<count($data);$i++) {
	if ($i < 50) {
	print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">";
		print "<td ><font class='T2'>&nbsp;".dateForm($data[$i][0])."&nbsp;</font></td>";
		print "<td ><font class='T2'>&nbsp;".urldecode($data[$i][2])."</font></td>";
		print "<td align='right' ><font class='T2'>".affichageFormatMonnaie($data[$i][1])."</font></td>";
		print "<td align='right' ><a href='cantine_compta_2.php?idsupp=".$data[$i][3]."&idpers2=$idpers&membre=$membre'><img src='image/commun/b_drop.png' border='0'/></a></td>";
		print "</tr>";
	}
	$total+=$data[$i][1];
}
	print "<tr>";
	print "<td colspan=2 align='right'><font class='T2'>Totaux : </font></td>";
	print "<td align='right' ><font class='T2'>".affichageFormatMonnaie($total)."</font></td>";
	
	print "</tr>";
?>

</table>
<?php }else{ ?>
<br><font class="T2" id="color3"><center><img src="image/commun/img_ssl.gif" align='center' /> Accès réservé</center></font>
<br><br>
<?php } ?>
</td></tr></table>
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
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
</BODY>
</HTML>
