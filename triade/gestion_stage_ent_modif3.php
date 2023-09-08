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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/ajax-stage.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE59 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br><br>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();

validerequete("3");

if (isset($_POST["create"])) {
	$cr=modif_entreprise($_POST["id"],$_POST["nomentreprise"],$_POST["contact"],$_POST["adressesiege"],$_POST["codepostal"],$_POST["ville"],$_POST["activite"],$_POST["activiteprin"],$_POST["tel"],$_POST["fax"],$_POST["email"],$_POST["information"],$_POST["fonction"],$_POST["pays"],$_POST["nbchambre"],$_POST["siteweb"],$_POST["grphotelier"],$_POST["nbetoile"],$_POST["registrecommerce"],$_POST["siren"],$_POST["siret"],$_POST["formejuridique"],$_POST["secteureconomique"],$_POST["INSEE"],$_POST["NAFAPE"],$_POST["NACE"],$_POST["typeorganisation"],$_POST["qualite"]);
	if($cr == 1){
		history_cmd($_SESSION["nom"],"MODIF","ENTREPRISE STAGE");
		alertJs("Entreprise Modifiée");
	}
}


if (isset($_GET["id"])) { $id=$_GET["id"]; }
if (isset($_POST["id"])) { $id=$_POST["id"]; }


$data=recherche_entreprise_id2($id); //id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,contact_fonction,pays_ent,nbchambre,siteweb,grphotelier,nbetoile,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation,qualite
for($i=0;$i<count($data);$i++) {
?>

<form method=post name="formulaire">
<table width=100% border=0 align=center >

<tr>
<td align="right" ><font class=T2><?php print LANGSTAGE26 ?> :</font></td>
<td><input type=text name="nomentreprise" maxlength="30" size=30 value="<?php print quote_input($data[$i][1]) ?>"></td>
</tr>


<tr>
<td align="right" ><font class="T2"><?php print "Registre du commerce" ?> :</font></td><td><input type="text" name="registrecommerce" size=30 maxlength="100" value="<?php print quote_input($data[$i][18]) ?>" ></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "SIREN" ?> :</font></td><td><input type="text" name="siren" size=30 maxlength="100" value="<?php print quote_input($data[$i][19]) ?>" ></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "SIRET" ?> :</font></td><td><input type="text" name="siret" size=30 maxlength="100" value="<?php print quote_input($data[$i][20]) ?>" ></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Forme juridique" ?> :</font></td><td>	<select name="formejuridique">
<?php if ($data[$i][21] != "") 	print "<option value='".$data[$i][21]."' id='select1'  >".$data[$i][21]."</option>"; ?>
											<option value='' id='select0' ></option>
											<option value='SA' id='select1'>SA</option>
											<option value='SARL' id='select1'>SARL</option>
											<option value='EURL' id='select1'>EURL</option>
											<option value='SNC' id='select1'>SNC</option>
											<option value='SAS' id='select1'>SAS</option>
											<option value='SASU' id='select1'>SASU</option>
											<option value='EI' id='select1'>EI</option>
											<option value='Auto-Entreprise' id='select1'>Auto-Entreprise</option>
											</select></td>
</tr>

<td align="right" ><font class="T2"><?php print "Secteur économique" ?> :</font></td><td> <select name="secteureconomique">
<?php if ($data[$i][22] != "") 	print "<option value='".$data[$i][22]."' id='select1' >".$data[$i][22]."</option>"; ?>
											<option value='' id='select0' ></option>
											<option value='Primaire' id='select1'>Primaire</option>
											<option value='Secondaire' id='select1'>Secondaire</option>
											<option value='Tertiaire' id='select1'>Tertiaire</option>
											</select></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Secteur INSEE" ?> :</font></td><td>
<select name="INSEE">
<option value="<?php print $data[$i][23]?>" id='select1' ><?php print $data[$i][23]?></option>
<option value="EA Agriculture, sylviculture, pêche" id='select1' >EA Agriculture, sylviculture, pêche</option>
<option value="EB Industries agricoles et alimentaires" id='select1' >EB Industries agricoles et alimentaires</option>
<option value="EC Industrie des biens de consommation" id='select1' >EC Industrie des biens de consommation</option>
<option value="ED Industrie automobile" id='select1' >ED Industrie automobile</option>
<option value="EE Industries des biens d'équipement" id='select1' >EE Industries des biens d'équipement</option>
<option value="EF Industries des biens intermédiaires" id='select1' >EF Industries des biens intermédiaires</option>
<option value="EG Energie" id='select1' >EG Energie</option>
<option value="EH Construction" id='select1' >EH Construction</option>
<option value="EJ Commerce" id='select1' >EJ Commerce</option>
<option value="EK Transports" id='select1' >EK Transports</option>
<option value="EL Activités financières" id='select1' >EL Activités financières</option>
<option value="EM Activités immobilières" id='select1' >EM Activités immobilières</option>
<option value="EN Services aux entreprises" id='select1' >EN Services aux entreprises</option>
<option value="EP Services aux particuliers" id='select1' >EP Services aux particuliers</option>
<option value="EQ Éducation, santé, action sociale" id='select1' >EQ Éducation, santé, action sociale</option>
<option value="ER Administration" id='select1' >ER Administration</option>
</select>
</td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Code NAF/APE" ?> :</font></td><td><input type="text" name="NAFAPE" size=30 maxlength="100" value="<?php print quote_input($data[$i][24]) ?>"  ></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Branche d'activité (NACE)" ?> :</font></td><td><input type="text" name="NACE" size=30 maxlength="100" value="<?php print quote_input($data[$i][25]) ?>" ></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Type d'organisation" ?> :</font></td><td><select name="typeorganisation">
<?php if ($data[$i][26] != "") 	print "<option value='".$data[$i][26]."' id='select1'  >".$data[$i][26]."</option>"; ?>
											<option value='' id='select0' ></option>
											<option value='Administration' id='select1'>Administration</option>
											<option value='Association' id='select1'>Association</option>
											<option value='Entreprise' id='select1'>Entreprise</option>
											</select></td>
</tr>






<tr>
<td align="right" ><font class=T2><?php print LANGSTAGE91 ?> :</font></td>
<td><input type=text name="contact" maxlength="50" size=30 value="<?php print quote_input($data[$i][2]) ?>"></td>
</tr>
<tr>
<td align="right" ><font class=T2><?php print LANGSTAGE93 ?> :</font></td>
<td><input type=text name="fonction" maxlength="30" size=30 value="<?php print quote_input($data[$i][12]) ?>"></td>
</tr>
<tr>
<td align="right" ><font class=T2><?php print LANGSTAGE28." ".LANGSTAGE94 ?> :</font></td>
<td><input type=text name="adressesiege" maxlength="50" size=30 value="<?php print quote_input($data[$i][3]) ?>"></td>
</tr>
<tr>
<td align="right" ><font class=T2><?php print LANGSTAGE29." ".LANGSTAGE95 ?> :</font></td>
<td><input type=text name="codepostal" maxlength="10" size=30 value="<?php print $data[$i][4] ?>"></td>
</tr>
<tr>
<td align="right" ><font class=T2><?php print LANGSTAGE30." ".LANGSTAGE94 ?> :</font></td>
<td><input type="text" name="ville" maxlength="30" size=30 value="<?php print quote_input($data[$i][5]) ?>"></td>
</tr>
<tr>
<td align="right" ><font class=T2><?php print "Pays ".LANGSTAGE94 ?> :</font></td>
<td><input type="text" name="pays" maxlength="50" size=30 value="<?php print quote_input($data[$i][13]) ?>"></td>
</tr>
<tr>
<td align="right" ><font class=T2><?php print LANGSTAGE31 ?> :</font></td>
<td>
<select name=activite>
<option value='<?php print $data[$i][6]?>'><?php print strtolower($data[$i][6])?></option>
<?php
$data2=activite_liste();
for($a=0;$a<count($data2);$a++) {
	print "<option value='".$data2[$a][0]."' title=\"".$data2[$a][0]."\" >".trunchaine(strtolower($data2[$a][0]),22)."</option>";
}
?>
</select>
 [ <a href="#" onclick="open('gestion_stage_activite_aj.php','stageact','width=400,height=100');"><?php print LANGSTAGE32 ?></a> ]
</td>
</tr>
<tr>
<td align=right ><font class=T2><?php print LANGSTAGE33 ?> :</font></td>
<td><input type=text name="activiteprin" size=30 value="<?php print quote_input($data[$i][7]) ?>"></td>
</tr>

<tr>
<td align=right ><font class=T2><?php print "Groupe hôtelier" ?> :</font></td>
<td><input type=text name="grphotelier" size=30 value="<?php print quote_input($data[$i][16]) ?>" ></td>
</tr>
<tr>
<td align=right ><font class=T2><?php print "Nombre d'étoiles" ?> :</font></td>
<td><input type=text name="nbetoile" size=30 value="<?php print $data[$i][17]?>" ></td>
</tr>
<tr>
<td align=right ><font class=T2><?php print "Nombre de chambres" ?> :</font></td>
<td><input type=text name="nbchambre" size=30 value="<?php print $data[$i][14]?>" ></td>
</tr>

<tr>
<td align=right ><font class=T2><?php print LANGSTAGE34 ?> :</font></td>
<td><input type=text name="tel" size=30 value="<?php print $data[$i][8]?>"></td>
</tr>
<tr>
<td align=right ><font class=T2><?php print LANGSTAGE35 ?> :</font></td>
<td><input type=text name="fax" size=30 value="<?php print $data[$i][9]?>"></td>
</tr>
<tr>
<td align=right ><font class=T2><?php print LANGSTAGE36 ?> :</font></td>
<td><input type=text name="email" size=30 value="<?php print $data[$i][10]?>"></td>
</tr>

<tr>
<td align=right ><font class=T2><?php print "Qualit&eacute;" ?> :</font></td>
<td><input type=text name="qualite" size=30 value="<?php print quote_input($data[$i][27]) ?>" ></td>
</tr>

<tr>
<td align=right ><font class=T2><?php print "Site web" ?> :</font></td>
<td><input type=text name="siteweb" size=30 value="<?php print $data[$i][15]?>" ></td>
</tr>

<tr>
<td align=right ><font class=T2><?php print LANGSTAGE37 ?>  :</font></td>
<td><input type=text name="information" size=30 value="<?php print $data[$i][11]?>">
</td>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan=2 align=center><table><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30 ?>","create"); //text,nomInput</script>
<?php 
if ($_SESSION["membre"] == "menuprof") {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>&nbsp;&nbsp;";
}elseif(($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menuparent")) {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_el.php','_parent')</script>&nbsp;&nbsp;";
}else{
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>&nbsp;&nbsp;";	
}
?>
</td></tr></table>
</td></tr>
</table>
<input type=hidden name=id value='<?php print $id?>'>
</form>
<?php
	} // fin du for
?>
<BR><br>
</ul>
<!-- // fin  -->
</td></tr></table>




<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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
</BODY></HTML>
