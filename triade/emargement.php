<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
	$anneeScolaire=$_POST["anneeScolaire"];
	setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<style>
.na-card     { background:#fff !important; border:1px solid #c5cae9 !important; border-radius:8px !important; padding:14px 16px !important; margin:10px 0 10px !important; }
.na-row      { display:flex !important; align-items:center !important; margin-bottom:8px !important; gap:8px !important; flex-wrap:wrap !important; }
.na-lbl      { font-size:12px !important; font-weight:600 !important; color:#333 !important; min-width:140px !important; flex-shrink:0 !important; }
.na-foot     { margin-top:8px !important; overflow:hidden !important; }
.na-hr       { border:none !important; border-top:1px solid #c5cae9 !important; margin:6px 0 !important; }
.na-subtitle { font-size:12px !important; font-weight:600 !important; color:#333 !important; text-align:center !important; margin:8px 0 !important; }
</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_comptaSupp.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS303 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once('librairie_php/db_triade.php');
validerequete("3");
$datap=config_param_visu("hauteuremarg");
$hauteur=$datap[0][0];
if ($hauteur != "") {
	$option="<option value='$hauteur' id='select0'>$hauteur</option>";
}else{
    $hauteur=6;
}
nettoyageEdt();
?>

<div class="na-card">
  <form method='post'>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL29 ?> :</span>
    <select name='anneeScolaire' class="cc-select" onChange="this.form.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
    </select>
  </div>
  </form>
</div>

<div class="na-card">
  <div class="na-subtitle" style="margin-bottom:12px !important;"><?php print LANGMESS304 ?></div>

  <form action='emargementvierge.php?idclasse' method='post' name='form1'>
  <input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur1' />
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS305 ?> :</span>
    <select id="saisie_classe" name="saisie_classe" class="cc-select" onChange="this.form.submit();">
      <option><?php print LANGCHOIX ?></option>
      <?php select_classe2(20); ?>
    </select>
  </div>
  </form>
  <form action='emargementviergeexamen.php' method='post' name='form2'>
  <input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur2' />
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS306 ?> :</span>
    <select id="saisie_classe" name="saisie_classe" class="cc-select" onChange="this.form.submit();">
      <option><?php print LANGCHOIX ?></option>
      <?php select_classe2(20); ?>
    </select>
  </div>
  </form>
<br><br>
  <div class="na-subtitle" style="margin-bottom:12px !important;"><?php print LANGMESS307 ?></div>

  <form action='emargementvierge.php?idgroupe' method='post' name='form3'>
  <input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur3' />
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS305 ?> :</span>
    <select id="saisie_groupe" name="saisie_groupe" class="cc-select" onChange="this.form.submit();">
      <option><?php print LANGCHOIX ?></option>
      <?php select_groupe_id(); ?>
    </select>
  </div>
  </form>

  <form action='emargementviergeexamen.php' method='post' name='form4'>
  <input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur4' />
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS306 ?> :</span>
    <select id="saisie_groupe" name="saisie_groupe" class="cc-select" onChange="this.form.submit();">
      <option><?php print LANGCHOIX ?></option>
      <?php select_groupe_id(20); ?>
    </select>
  </div>
  </form>
</div>

<hr class="na-hr">

<div class="na-card">
  <form action='emargementdujour.php' method='post' name='form_today'>
  <input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur5' />
  <input type='hidden' name='datedujour' value='<?php print date("d/m/Y") ?>' />
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS314 ?> :</span>
  </div>
</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT28?>","create","")</script>
</div>
<br>
</form>

<div class="na-card">
  <form action='emargementdujour.php' method='post' name="formulaire">
  <input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur6' />
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS315 ?> :</span>
    <input type="text" name="datedujour" value="<?php print date("d/m/Y") ?>" onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)" />
    &nbsp;<?php
    include_once("librairie_php/calendar.php");
    calendarDim("id1","document.formulaire.datedujour",$_SESSION["langue"],"0","0");
    ?>
  </div>
  <div class="na-row">
    <span class="na-lbl">au :</span>
    <input type="text" name="datedujourfin" value="" onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)" />
    &nbsp;<?php
    include_once("librairie_php/calendar.php");
    calendarDim("id2","document.formulaire.datedujourfin",$_SESSION["langue"],"0","0");
    ?>&nbsp;
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS316 ?> :</span>
    <select id="saisie_classe" name="saisie_classe" class="cc-select">
      <option value='tous'><?php print LANGAFF5 ?></option>
      <?php select_classe2(20); ?>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS317 ?> :</span>
    <select id="saisie_prof" name="saisie_prof" class="cc-select">
      <option value='tous'><?php print LANGMESS318 ?></option>
      <?php select_personne_2('ENS','25'); ?>
    </select>
  </div>
</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT28?>","create","")</script>
</div>
<br>
</form>

<div class="na-card">
  <form name="form0">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS319 ?> :</span>
    <select name="hauteur" size=1 class="cc-select" onChange='affecttaille(this.value)'>
      <?php print $option ?>
      <option value="4">04</option>
      <option value="5">05</option>
      <option value="5.5">05.5</option>
      <option value="6">06</option>
      <option value="7">07</option>
      <option value="8">08</option>
      <option value="9">09</option>
      <option value="10">10</option>
      <option value="11">11</option>
      <option value="12">12</option>
      <option value="13">13</option>
      <option value="14">14</option>
      <option value="15">15</option>
    </select>
  </div>
  </form>
</div>

<script>
function affecttaille(val) {
	document.getElementById('hauteur1').value=val;
	document.getElementById('hauteur2').value=val;
	document.getElementById('hauteur3').value=val;
	document.getElementById('hauteur4').value=val;
	document.getElementById('hauteur5').value=val;
	document.getElementById('hauteur6').value=val;
}
</script>

<br /><br />
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
