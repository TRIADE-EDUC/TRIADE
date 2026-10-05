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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"visadirection")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}elseif ($_SESSION["membre"] == "menuadmin") {
	validerequete("menuadmin");
}else{
	if (PROFPACCESVISADIRECTION == "oui") {
		validerequete("menuprof");
		verif_profp_class($_SESSION["id_pers"],$_SESSION["profpclasse"]);
	}else{
		validerequete("menuadmin");
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' >
<?php print LANGTMESS480 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<form method=post onsubmit="return valideTab()" name="formulaire" action="visa_direction2.php">
<div style="max-width:600px;margin:16px auto;display:flex;flex-direction:column;gap:14px">

<div class="card">
  <div class="card-header card-header-primary">
    <i class="bi bi-funnel"></i> Paramètres
  </div>
  <div class="card-body" style="display:flex;flex-direction:column;gap:10px">

    <div class="form-row">
      <label class="cc-label"><i class="bi bi-calendar-range" style="color:#080A66"></i> <?php print LANGBULL3 ?></label>
      <select name='annee_scolaire' class="cc-select">
        <?php
        $anneeScolaire=$_COOKIE["anneeScolaire"];
        filtreAnneeScolaireSelectNote($anneeScolaire,3);
        ?>
      </select>
    </div>

    <div class="form-row">
      <label class="cc-label"><i class="bi bi-people" style="color:#080A66"></i> <?php print LANGPROFG ?></label>
      <select id="saisie_classe" name="saisie_classe" class="cc-select">
        <option id='select0'><?php print LANGCHOIX ?></option>
        <?php
        if ($_SESSION["membre"] == "menuprof") {
            print "<option id='select1' value='".$_SESSION["profpclasse"]."'>".chercheClasse_nom($_SESSION["profpclasse"])."</option>";
        }else{
            select_classe();
        }
        ?>
      </select>
    </div>

    <div class="form-row">
      <label class="cc-label"><i class="bi bi-calendar3" style="color:#080A66"></i> <?php print LANGBASE40 ?></label>
      <div style="display:flex;align-items:center;gap:8px">
      <select name="typetrisem" class="cc-select" onchange="visadirTrimes(this.value);">
        <option value="0"><?php print LANGCHOIX ?></option>
        <option value="trimestre"><?php print LANGPARAM28 ?></option>
        <option value="semestre"><?php print LANGPARAM29 ?></option>
        <option value="annuel"><?php print LANGMESS334 ?></option>
      </select>
      &nbsp;:&nbsp;
      <select name="saisie_trimestre" class="cc-select">
        <option value="0"></option>
        <option value="0"></option>
        <option value="0"></option>
      </select>
      </div>
    </div>
<script>
function visadirTrimes(val) {
    var sel = document.getElementsByName('saisie_trimestre')[0];
    if (!sel) return;
    if (val == 'trimestre') {
        sel.options[0].text='Trimestre 1'; sel.options[0].value='trimestre1';
        sel.options[1].text='Trimestre 2'; sel.options[1].value='trimestre2';
        sel.options[2].text='Trimestre 3'; sel.options[2].value='trimestre3';
    } else if (val == 'semestre') {
        sel.options[0].text='Semestre 1'; sel.options[0].value='trimestre1';
        sel.options[1].text='Semestre 2'; sel.options[1].value='trimestre2';
        sel.options[2].text='Annuel';     sel.options[2].value='annuel';
    } else if (val == 'annuel') {
        sel.options[0].text='Annuel'; sel.options[0].value='annuel';
        sel.options[1].text='';       sel.options[1].value='0';
        sel.options[2].text='';       sel.options[2].value='0';
    } else {
        sel.options[0].text=''; sel.options[0].value='0';
        sel.options[1].text=''; sel.options[1].value='0';
        sel.options[2].text=''; sel.options[2].value='0';
    }
}
</script>

    <div class="form-row">
      <label class="cc-label"><i class="bi bi-file-text" style="color:#080A66"></i> <?php print LANGMESS332 ?></label>
      <select name="type_bulletin" class="cc-select">
        <option value='default'>Standard</option>
        <?php if (VATEL != 1) { ?>
        <optgroup label="<?php print LANGBULL49 ?>">
          <option value='bacblanc'><?php print LANGPARAM30 ?> BAC Blanc</option>
          <option value='btsblanc'><?php print LANGPARAM30 ?> BTS Blanc</option>
          <option value='brevetblanc'><?php print LANGPARAM30 ?> Brevet Blanc</option>
          <option value='capblanc'><?php print LANGPARAM30 ?> CAP Blanc</option>
          <option value='bepblanc'><?php print LANGPARAM30 ?> BEP Blanc</option>
          <option value='partielblanc'><?php print LANGPARAM30 ?> Partiel Blanc</option>
        <optgroup label="Montessori">
          <option value='montessori'>Bulletin standard</option>
          <option value='montessori_spec'>Bulletin Spécif.</option>
        <optgroup label="Pigier">
          <option value='pigierparis'>Bulletin Pigier Paris</option>
          <option value='pigierparisv2'>Bulletin Pigier Paris V2</option>
        <optgroup label="La cheneraie">
          <option value='cheneraie'>Bulletin La Cheneraie</option>
        <optgroup label="Seminaire">
          <option value='seminaire'>Bulletin standard</option>
        <optgroup label="LEAP">
          <option value='leap'>Bulletin standard</option>
        <optgroup label="JTC">
          <option value='jtc'>Bulletin Immaculée Conception</option>
        <optgroup label="Unité Enseignement">
          <option value='univproafrique'>Bulletin Univ. Pro. Afrique</option>
        <?php } ?>
      </select>
    </div>

  </div>
</div>

<div class="toolbar" style="justify-content:center">
  <script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","consult");</script>
</div>

</div>
</form>
<br><br>
</td></tr>
</table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php
Pgclose();
?>
</BODY>
</HTML>
