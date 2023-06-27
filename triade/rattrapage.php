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
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();

if (isset($_POST["idrattrappage"])) $refRattrapage=$_POST["idrattrappage"];

if (isset($_POST["createrattrappage"])) {	
	$refRattrapage=$_POST["idrattrappage"];
	suppRattrappage($refRattrapage);
	for($i=1;$i<=5;$i++) {
		$date=$_POST["date_$i"];
		$heure=$_POST["heure_$i"];
		$duree=$_POST["duree_$i"];
		$valide=$_POST["valide_$i"];
		if (($date != "") && ($heure != "") && ($duree != "")) {
			enrRattrappage($refRattrapage,$date,$heure,$duree,$valide);
			$reponse="<center><font id='color3'><b>".LANGDONENR."</b></font></center>";
		}
	}
}

if (trim($refRattrapage) != "") {
	$data=recupRattrappage($refRattrapage); // date,heure_depart,duree,valider
}else{
	$data=array();
}

for($i=0;$i<count($data);$i++) {

	if ($i == 0) {	
		$date_1=dateForm($data[$i][0]);
		$heure_1=timeForm($data[$i][1]);
		$duree_1=timeForm($data[$i][2]);
		$valide_1=($data[$i][3] == 1) ? "<img src='image/commun/stat1.gif' title='effectuer' />" : "" ;
		$readonly_1=($data[$i][3] == 1) ? "readonly='readonly' onFocus=\"alert('Modification impossible - Rattrapage déjà effectué.');this.form.cacher.focus()\"   " : "" ;
		$valide_11=$data[$i][3];
	}

	if ($i == 1) {
		$date_2=dateForm($data[$i][0]);
		$heure_2=timeForm($data[$i][1]);
		$duree_2=timeForm($data[$i][2]);
		$valide_2=($data[$i][3] == 1) ? "<img src='image/commun/stat1.gif' title='effectuer' />" : "" ;
		$readonly_2=($data[$i][3] == 1) ? "readonly='readonly' onFocus=\"alert('Modification impossible - Rattrapage déjà effectué.');this.form.cacher.focus()\"  " : "" ;
		$valide_22=$data[$i][3];
	}

	if ($i == 2) {
		$date_3=dateForm($data[$i][0]);
		$heure_3=timeForm($data[$i][1]);
		$duree_3=timeForm($data[$i][2]);
		$valide_3=($data[$i][3] == 1) ? "<img src='image/commun/stat1.gif' title='effectuer' />" : "" ;
		$readonly_3=($data[$i][3] == 1) ? "readonly='readonly' onFocus=\"alert('Modification impossible - Rattrapage déjà effectué.');this.form.cacher.focus()\" " : "" ;
		$valide_33=$data[$i][3];
	}

	if ($i == 3) {
		$date_4=dateForm($data[$i][0]);
		$heure_4=timeForm($data[$i][1]);
		$duree_4=timeForm($data[$i][2]);
		$valide_4=($data[$i][3] == 1) ? "<img src='image/commun/stat1.gif' title='effectuer' />" : "" ;
		$readonly_4=($data[$i][3] == 1) ? "readonly='readonly' onFocus=\"alert('Modification impossible - Rattrapage déjà effectué.');this.form.cacher.focus()\"  " : "" ;
		$valide_44=$data[$i][3];
	}

	if ($i == 4) {
		$date_5=dateForm($data[$i][0]);
		$heure_5=timeForm($data[$i][1]);
		$duree_5=timeForm($data[$i][2]);
		$valide_5=($data[$i][3] == 1) ? "<img src='image/commun/stat1.gif' title='effectuer' />" : "" ;
		$readonly_5=($data[$i][3] == 1) ? "readonly='readonly' onFocus=\"alert('Modification impossible - Rattrapage déjà effectué.');this.form.cacher.focus()\"  " : "" ;
		$valide_55=$data[$i][3];
	}
}
?>
<br />
<?php print $reponse ?>
<ul>
<form method='post' >
<font class='T2'>
&nbsp;&nbsp;&nbsp;Planning des rattrapages : 
<br /><br />
&nbsp;&nbsp;&nbsp;Pour le <input  size=12 <?php print $readonly_1 ?> name="date_1" value='<?php print $date_1 ?>'  type=text title='jj/mm/aaaa' onKeyPress='onlyChar(event)' /> à <input  <?php print $readonly_1 ?>  value='<?php print $heure_1 ?>' name="heure_1" type='text' size=4  onKeyPress='onlyChar2(event)' title='hh:mm' /> durant <input  <?php print $readonly_1 ?> value='<?php print $duree_1 ?>'  name="duree_1"  type='text' size='4' onKeyPress='onlyChar2(event)' <?php print $readonly_1 ?> value='<?php print $duree_1 ?>'  title='hh:mm' />&nbsp;<?php print $valide_1 ?><input type='hidden' value='<?php print $valide_11 ?>' name='valide_1' /> 
<br />
&nbsp;&nbsp;&nbsp;Pour le <input  size=12 name="date_2" value='<?php print $date_2 ?>' type=text title='jj/mm/aaaa' onKeyPress='onlyChar(event)' <?php print $readonly_2 ?> /> à <input <?php print $readonly_2 ?> value='<?php print $heure_2 ?>'  name="heure_2" type='text' size=4  onKeyPress='onlyChar2(event)' title='hh:mm' <?php print $readonly_2 ?> /> durant <input  value='<?php print $duree_2 ?>' name="duree_2"  type='text' size='4' onKeyPress='onlyChar2(event)' title='hh:mm' <?php print $readonly_2 ?>  />&nbsp;<?php print $valide_2 ?><input type='hidden' value='<?php print $valide_22 ?>' name='valide_2' />
<br />
&nbsp;&nbsp;&nbsp;Pour le <input  size=12 name="date_3" value='<?php print $date_3 ?>' type=text title='jj/mm/aaaa' onKeyPress='onlyChar(event)' <?php print $readonly_3 ?> /> à <input  <?php print $readonly_3 ?> value='<?php print $heure_3 ?>'  name="heure_3" type='text' size=4  onKeyPress='onlyChar2(event)' title='hh:mm' /> durant <input <?php print $readonly_3 ?> value='<?php print $duree_3 ?>' name="duree_3"  type='text' size='4' onKeyPress='onlyChar2(event)' title='hh:mm' />&nbsp;<?php print $valide_3 ?><input type='hidden' value='<?php print $valide_33 ?>' name='valide_3' />
<br />
&nbsp;&nbsp;&nbsp;Pour le <input  size=12 name="date_4" value='<?php print $date_4 ?>' type=text title='jj/mm/aaaa' onKeyPress='onlyChar(event)' <?php print $readonly_4 ?> /> à <input <?php print $readonly_4 ?> value='<?php print $heure_4 ?>'   name="heure_4" type='text' size=4  onKeyPress='onlyChar2(event)' title='hh:mm' /> durant <input <?php print $readonly_4 ?> value='<?php print $duree_4 ?>' name="duree_4"  type='text' size='4' onKeyPress='onlyChar2(event)' title='hh:mm' />&nbsp;<?php print $valide_4 ?><input type='hidden' value='<?php print $valide_44 ?>' name='valide_4' />
<br />
&nbsp;&nbsp;&nbsp;Pour le <input  size=12 name="date_5" value='<?php print $date_5 ?>' type=text title='jj/mm/aaaa' onKeyPress='onlyChar(event)' <?php print $readonly_5 ?> /> à <input <?php print $readonly_5 ?> value='<?php print $heure_5 ?>'   name="heure_5" type='text' size=4  onKeyPress='onlyChar2(event)' title='hh:mm' /> durant <input  <?php print $readonly_5 ?> value='<?php print $duree_5 ?>' name="duree_5" type='text' size='4' onKeyPress='onlyChar2(event)' title='hh:mm' />&nbsp;<?php print $valide_5 ?><input type='hidden' value='<?php print $valide_55 ?>' name='valide_5' />
</font>
<br /><br />
<input type='submit' name='createrattrappage' value='Enregistrer' class='BUTTON' /> 
<input type='BUTTON' value='Fermer' name="cacher" class='BUTTON' onclick="parent.close();"  />
<input type='hidden' name='idrattrappage' value='<?php print $refRattrapage ?>'  />
</ul>
</form>
</BODY></HTML>
