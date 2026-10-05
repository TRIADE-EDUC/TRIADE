<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<script>
window.alert = function(msg) { alertify.error(msg); };
function confirmerSuppr(url) {
    alertify.confirm('Supprimer ?', 'Confirmer la suppression de cet établissement ?',
        function() { location.href = url; },
        function() {}
    );
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPARAM6?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
     <!-- // fin  -->
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();


if (isset($_GET["suppsite"])) {
	suppr_param($_GET["suppsite"]);
}

if (isset($_POST["create"])){
	$cr=create_param(stripslashes($_POST["saisie_nom"]),stripslashes($_POST["saisie_adresse"]),$_POST["saisie_postal"],stripslashes($_POST["saisie_ville"]),$_POST["saisie_tel"],$_POST["saisie_mail"],stripslashes($_POST["saisie_directeur"]),$_POST["saisie_urlsite"],stripslashes($_POST["saisie_accademie"]),stripslashes($_POST["saisie_pays"]),$_POST["saisie_departement"],$_POST["anneeScolaire"],$_POST["idsite"]);
	if($cr == 1){
		print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGPARAM16)."'); });</script>";
		history_cmd($_SESSION["nom"],"MODIFICATION","Adresse etablissement ".$_POST["saisie_nom"]);
	}
}

$id=0;
if (isset($_POST["idsite"])) $id=$_POST["idsite"];


if (verifSiInfoParamSaisie() == 0) {
	print "<br><center><font id='color3' class='T2'>".LANGMESST390."</font></center><br>";
	$id='1';
}

$data=visu_param_id($id);
// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,$anneeScolaire
for($i=0;$i<countTriade($data);$i++) {
	$nom_etablissement=trim($data[$i][0]);
	$adresse=trim($data[$i][1]);
	$postal=trim($data[$i][2]);
	$ville=trim($data[$i][3]);
	$tel=trim($data[$i][4]);
	$mail=trim($data[$i][5]);
	$directeur_etablissement=trim($data[$i][6]);
	$urlsite=trim($data[$i][7]);
	$accademie=trim($data[$i][8]);
	$pays=trim($data[$i][9]);
	$departement=trim($data[$i][10]);
	$anneeScolaire=trim($data[$i][11]);
	$id=$data[$i][12];
}

$disabled="disabled='disabled'";
if ($id != 0) {
	$disabled="";
}
?>
<div class="param-wrap">

<!-- ── Sélection site ── -->
<form method='post'>
<div class="param-site-row">
    <img src="image/commun/etablissement.png" style="height:28px;vertical-align:middle">
    <span class="param-site-label"><?php print LANGMESS159 ?> :</span>
    <select name='idsite' class="param-select" onChange='this.form.submit()'>
        <option value='0' id='select0'><?php print LANGMESS160 ?></option>
        <?php select_site($id) ?>
    </select>
</div>
</form>

<!-- ── Formulaire établissement ── -->
<form method='post' onsubmit="return verifadresse()" name="formulaire">
<div class="param-card">

    <div class="param-field">
        <span class="param-label"><?php print preg_replace('/ /','&nbsp;',LANGPARAM7) ?> :</span>
        <input type="text" class="param-input" name="saisie_directeur" value="<?php print $directeur_etablissement ?>" maxlength="30">
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGMESS144 ?> :</span>
        <input type="button" onclick="open('signaturedirecteur.php?id=<?php print $id ?>','logo','width=400,height=200')" value="<?php print CLICKICI ?>" class="param-btn-popup" <?php print $disabled ?>>
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGPARAM8 ?> <span class="param-required">*</span> :</span>
        <input type="text" class="param-input" name="saisie_nom" value="<?php print $nom_etablissement ?>" maxlength="50">
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGPARAM37 ?> :</span>
        <input type="text" class="param-input" name="saisie_accademie" value="<?php print $accademie ?>" maxlength="50">
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGMESS145 ?> <span class="param-required">*</span> :</span>
        <select name='anneeScolaire' class="param-select">
        <?php
        include_once("librairie_php/timezone.php");
        $annee=dateY();
        $anneemoins=$annee - 1;
        $anneeplus=$annee + 1;
        if ($anneeScolaire != "") {
            print "<option value='$anneeScolaire' id='select1'>$anneeScolaire</option>";
        } else {
            print "<option value='' id='select0'>".LANGCHOIX."</option>";
        }
        $anneemoins2=$anneemoins-1;
        $anneemoins1=$anneemoins;
        ?>
        <option id='select1' value='<?php print "$anneemoins2 - $anneemoins1" ?>'><?php print "$anneemoins2 - $anneemoins" ?></option>
        <option id='select1' value='<?php print "$anneemoins - $annee" ?>'><?php print "$anneemoins - $annee" ?></option>
        <option id='select1' value='<?php print "$annee - $anneeplus" ?>'><?php print "$annee - $anneeplus" ?></option>
        </select>
    </div>

    <div class="param-field" style="align-items:flex-start">
        <span class="param-label" style="padding-top:4px"><?php print LANGPARAM9 ?> <span class="param-required">*</span> :</span>
        <textarea name="saisie_adresse" class="param-input" rows="2" style="height:auto"><?php print $adresse ?></textarea>
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGPARAM10 ?> <span class="param-required">*</span> :</span>
        <input type="text" class="param-input" name="saisie_postal" value="<?php print $postal ?>" maxlength="7" style="max-width:100px">
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGPARAM11 ?> <span class="param-required">*</span> :</span>
        <input type="text" class="param-input" name="saisie_ville" value="<?php print $ville ?>" maxlength="30">
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGMESS177 ?> <span class="param-required">*</span> :</span>
        <input type="text" class="param-input" name="saisie_departement" value="<?php print $departement ?>" maxlength="50">
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGMESS156 ?> <span class="param-required">*</span> :</span>
        <input type="text" class="param-input" name="saisie_pays" value="<?php print $pays ?>" maxlength="30">
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGPARAM12 ?> :</span>
        <input type="text" class="param-input" name="saisie_tel" value="<?php print $tel ?>" maxlength="30">
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGPARAM13 ?> :</span>
        <input type="text" class="param-input" name="saisie_mail" value="<?php print $mail ?>" maxlength="50">
    </div>

    <div class="param-field">
        <span class="param-label"><?php print LANGPARAM34 ?> :</span>
        <input type="text" class="param-input" name="saisie_urlsite" value="<?php print $urlsite ?>">
    </div>

    <div class="param-field" style="border-bottom:none">
        <span class="param-label"><?php print LANGPARAM14 ?> :</span>
        <input type="button" onclick="open('logoetablissement.php?id=<?php print $id ?>','logo','width=400,height=200')" value="<?php print CLICKICI ?>" class="param-btn-popup" <?php print $disabled ?>>
    </div>

</div><!-- /.param-card -->

<div class="param-submit-row">
    <table align="center"><tr><td>
    <script language="JavaScript">buttonMagicSubmitIdDiv("<?php print LANGPARAM15 ?>",'create','ok','bt1','0')</script>
    <?php if (($id != 1) && ($id != 0)) {
        print "<input type='button' value='".LANGMESST391."' class='btn-action btn-danger' onclick=\"confirmerSuppr('param.php?suppsite=$id')\" style='margin-left:6px'>";
    } ?>
    </td></tr></table>
</div>

</div><!-- /.param-wrap -->
<?php 

if ((LAN == "oui") && ($ville != "") && ($adresse != "") && ($pays != "")){
	$adresse=preg_replace('/\r\n/'," ",$adresse);
?>
		<br /><center>
		<iframe src="https://support.triade-educ.org/support/google-map-V3-triade.php?etablissement=<?php print  urlencode($nom_etablissement)?>&adresse=<?php print urlencode($adresse) ?>&ville=<?php print urlencode($ville) ?>&pays=<?php print urlencode($pays)?>&web=<?php print urlencode($urlsite) ?>" width=400 height=300 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no style='box-shadow: 1px 1px 12px #555;' ></iframe >
		</center>
<?php 

} 

Pgclose();

?>

<BR><br>
<!-- // fin  -->
</td></tr></table>
<input type='hidden' name='idsite' value='<?php print $id ?>'  >
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
