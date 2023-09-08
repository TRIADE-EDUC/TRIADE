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
<html>
<head>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/ajax_codebarre.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="https://support.triade-educ.org/support/badge/config.php"></script>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<ul>
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?><br>

<font class='T2'><b>Commande de badge</b><br>
<br>

<?php 
if ((!isset($_POST['create'])) && (!isset($_POST['create2']))) { ?>
	<form method='post' action="badge.php">
	</font>
	<font class='T2'>Indiquer la ou les classe(s) :</font><br><br>
	<?php
	checkbox_classe2(); // creation des options
	?>
	<br><br>
	<script>buttonMagicSubmit("Valider","create")</script>
	</form>

<br>
	<iframe src='https://support.triade-educ.org/support/badge/tarif.php?GRAPH=<?php print GRAPH ?>' width='400' height='300' MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=yes /></iframe>
<?php 
} 


if (isset($_POST['create'])) { 
	$idclasse=$_POST["idclasse"];
	$nbIdClasse=$_POST["nbIdClasse"];
	foreach($idclasse as $key=>$value) {
		$in.="'$value',";
	}
	$in=preg_replace('/,$/','',$in);
	$sql="SELECT libelle,elev_id,nom,prenom,code_class FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe=code_class AND  code_class IN ($in)";

	$res=execSql($sql);
	$data=chargeMat($res);
	$nbBadge=count($data);

	
?>
	<script> 
	var nbBadge='<?php print $nbBadge ?>';
	var prixBadge="";
	if (nbBadge < 6500) prixBadge=P6500;
	if (nbBadge < 2000) prixBadge=P2000;
	if (nbBadge < 1000) prixBadge=P1000;
	if (nbBadge < 750) prixBadge=P750;
	if (nbBadge < 500) prixBadge=P500;
	if (nbBadge < 300) prixBadge=P300;
	if (nbBadge < 250) prixBadge=P250;
	if (nbBadge < 200) prixBadge=P200;
	if (nbBadge < 150) prixBadge=P150;
	if (nbBadge < 100) prixBadge=P100;
	</script>
	<form method='post' onSubmit="return validateBadge();" name="formulaire"  >
	<table border='1' width='80%'>
	<tr><td align='center' bgcolor='yellow'><font class=T2>Produit</font></td>
	    <td align='center' bgcolor='yellow'><font class=T2>Quantité</font></td>
	    <td align='center' bgcolor='yellow'><font class=T2>Prix HT</font></td>
	    <td align='center' bgcolor='yellow'><font class=T2>Total HT</font></td></tr>
	<tr><td align='left' bgcolor='#FFFFFF'>&nbsp;<font class=T2> Nombre de badge </font></td>
	    <td align='center' bgcolor='#FFFFFF'><font class=T2><?php print $nbBadge ?></font></td>
		<td align='center' bgcolor='#FFFFFF'><font class=T2><script> document.write(prixBadge); </script> &euro;</font></td>
	    <td align='center' bgcolor='#FFFFFF'><font class=T2><script> var prixtotalBadge=nbBadge*prixBadge ;document.write(Math.round(prixtotalBadge * 100) / 100); </script> &euro; </font></td></tr>
	<tr><td align='left' bgcolor='#FFFFFF'>&nbsp;<font class=T2> Frais de Livraison </font></td>
	    <td align='center' bgcolor='#FFFFFF'><font class=T2>1</font></td>
    	    <td align='center' bgcolor='#FFFFFF'><font class=T2><script> document.write(PRIXCOLISSIMO); </script> &euro;</font></td>
	    <td align='center' bgcolor='#FFFFFF'><font class=T2><script> document.write(PRIXCOLISSIMO); </script> &euro;</font></td></tr>
	<tr><td align='right' colspan='3' bgcolor='#FFFFFF'><font class=T2> Prix Total HT : </font></td>
	    <td align='center' bgcolor='#FFFFFF'><font class=T2  color='blue' ><script> var prixtotal=eval(prixtotalBadge)+eval(PRIXCOLISSIMO) ;document.write(Math.round(prixtotal * 100) / 100); </script> &euro; </font></td></tr>
	</table>
	<input type='hidden' name="saisie_classe" value="<?php print $in ?>" />
	<input type='hidden' name="nbBadge" value="<?php print $nbBadge ?>" />
	<input type='hidden' name="prixbadge" id='prixbadge' value="" /><script>document.getElementById('prixbadge').value=prixBadge; </script>
	<input type='hidden' name="prixcolissimo" id='prixcolissimo' value="" /><script>document.getElementById('prixcolissimo').value=PRIXCOLISSIMO; </script>
	<br><br>
	<table border='1' width='80%'>
	<tr><td align='right'  width='40%' bgcolor='#FFFFFF'>&nbsp;<font class=T2>Contact : </font></td>
	    <td align='left' bgcolor='#FFFFFF'><input type='text' size='40' class=BUTTON name="contact" /></td>
	</tr>
	<tr><td align='right'  width='40%' bgcolor='#FFFFFF'>&nbsp;<font class=T2>Email : </font></td>
	    <td align='left' bgcolor='#FFFFFF'><input type='text' size='40' class=BUTTON name="email" /></td>
	</tr>
	<tr><td align='right'  bgcolor='#FFFFFF'>&nbsp;<font class=T2>Adresse : </font></td>
	    <td align='left' bgcolor='#FFFFFF'><input type='text' size='40' class=BUTTON name="adresse" /></td>
	</tr>
	<tr><td align='right' bgcolor='#FFFFFF'>&nbsp;<font class=T2>Code Postal : </font></td>
	    <td align='left' bgcolor='#FFFFFF'><input type='text' size='40' class=BUTTON name="ccp" /></td>
	</tr>
	<tr><td align='right'  bgcolor='#FFFFFF'>&nbsp;<font class=T2>Ville : </font></td>
	    <td align='left' bgcolor='#FFFFFF'><input type='text' size='40' class=BUTTON name="ville" /></td>
	</tr>
	</table>
	<br><br>
	<table><tr><td><script>buttonMagicSubmit("Confirmer la commande","create2")</script></td></tr></table>
	</form>
<?php 
} 

if (isset($_POST['create2'])) {
	$idclasse=$_POST["saisie_classe"];
	$contact=$_POST["contact"];
	$email=$_POST["email"];
	$adresse=$_POST["adresse"];
	$ccp=$_POST["ccp"];
	$ville=$_POST["ville"];
	$nbBadge=$_POST["nbBadge"];
	$prixbadge=$_POST["prixbadge"];
	$prixcolissimo=$_POST["prixcolissimo"];
	$productid=PRODUCTID;
	$info="$contact ($email) \r\n$adresse \r\n$ccp \r\n$ville\r\nPrix du Badge : $prixbadge\r\nProductId:$productid";
	$datavisu=visu_param(); // nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire
	$anneescolaire=$datavisu[0][11];
	$in=stripslashes($idclasse);
	$sql="SELECT libelle,elev_id,nom,prenom,code_class FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe=code_class AND  code_class IN ($in)";
	require_once "./librairie_php/class.writeexcel_workbook.inc.php";
	require_once "./librairie_php/class.writeexcel_worksheet.inc.php";
	if (!is_dir("./data/badge")) { @mkdir("./data/badge"); htaccess("./data/badge"); } 
	if (!is_dir("./data/badge/photo")) { @mkdir("./data/badge/photo"); htaccess("./data/badge/photo"); }
	nettoyage_repertoire("./data/badge/photo");
	nettoyage_repertoire("./data/badge/");
	$fd=fopen("data/badge/info.txt","a+");
	fwrite($fd,"$info");
	fclose($fd);
	$fichier="./data/badge/listing_badge.xls";	
	$workbook = &new writeexcel_workbook($fichier);
	$worksheet1 =& $workbook->addworksheet('Listing Badge');	
	$header =& $workbook->addformat();
	$header->set_color('white');
	$header->set_align('center');
	$header->set_align('vcenter');
	$header->set_pattern();
	$header->set_fg_color('blue');
	$center =& $workbook->addformat();
	$center->set_align('left');
	$worksheet1->write('A1', "Nom Elève", $header);
	$worksheet1->write('B1', "Prénom Elève", $header);	
	$worksheet1->write('C1', "Classe", $header);
	$worksheet1->write('D1', "Année Scolaire", $header);
	$worksheet1->write('E1', "Type Code barre", $header);
	$worksheet1->write('F1', "Valeur du code barre", $header);
	$worksheet1->write('G1', "Nom de la photo", $header);
	$res=execSql($sql);
	$data=chargeMat($res);
	$ii=1;
	for($i=0;$i<count($data);$i++) {  //  libelle,elev_id,nom,prenom,code_class
		$codebarre=recupCodeBar($data[$i][1],"menueleve");
		$nom=strtoupper($data[$i][2]);
		$prenom=strtoupper($data[$i][3]);
		$idclasse=$data[$i][4];
		$nomclasse=chercheClasse_nom($idclasse);
		$ideleve=$data[$i][1];
		$classe=chercheClasse_nom($idclasse);
		$photoId=recherche_photo_eleve($ideleve);
		$nomphoto="$photoId";
		if (file_exists("./data/image_eleve/$photoId")) { 
			copy("./data/image_eleve/$photoId","./data/badge/photo/$nomphoto"); 
		}else{
			$nomphoto="";
		}
		$worksheet1->write("$ii",'0',"$nom", $center);
		$worksheet1->write("$ii",'1',"$prenom", $center);	
		$worksheet1->write("$ii",'2',"$nomclasse", $center);
		$worksheet1->write("$ii",'3',"$anneescolaire", $center);
		$worksheet1->write("$ii",'4',"code39", $center);
		$worksheet1->write("$ii",'5',"$codebarre", $center);
		$worksheet1->write("$ii",'6',"$nomphoto", $center);
		$ii++;
	}
	$workbook->close();

	// fichier Zip
	$fichierzip="./data/listing-badge.zip";
	@unlink($fichierzip);
	include_once('./librairie_php/pclzip.lib.php');
	$archive = new PclZip("$fichierzip");
	$archive->create('data/badge',PCLZIP_OPT_REMOVE_PATH,'data'); 

	$urlsite=URLSITE;
	$sujet="TRIADE-BADGE";
	$message1=$message2="
$contact ($email)
--------------------------------------------	
$adresse
$ccp
$ville
--------------------------------------------
ProductId:$productid
URL:$urlsite
nbBadge=$nbBadge
Prix du badge:$prixbadge HT
Livraison: $prixcolissimo
--------------------------------------------
";
	$to="support@triade-educ.org";
	$from=$reply=MAILREPLY;
	$nom_expediteur=MAILNOMREPLY;
	$fichierjoint=$fichierzip;

	mailTriade($sujet,$message1,$message2,$to,$from,$reply,$nom_expediteur,$fichierjoint);
?>
<font class='T2'>

	Votre commande est bien pris en compte.<br><br>

	Elle sera finalisé après réception du paiement.<br><br>

	T&eacute;l&eacute;charger la facture ProFormat <a href='https://support.triade-educ.org/support/badge/factureProFormat.php?productid=<?php print $productid ?>&email=<?php print $email ?>&nbBadge=<?php print $nbBadge ?>' target='_blank' ><font color=blue>Facture</font></a><br><br>

        <font size=1><i>Pour toutes questions, merci de contacter le support à cette adresse :<br> support@triade-educ.org</i></font>

</font>
<?php
}





Pgclose();
?>
</ul>
</body>
</html>
