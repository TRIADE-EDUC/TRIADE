<?php
session_start();
error_reporting(0);
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
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
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_bascule_select.js"></script>
<script language="JavaScript" src="./librairie_js/lib_ordre_liste.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_enrGrpMat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<script language="javaScript">
var nbElems=0;
function calcul(op,i) {
	// calcul le nombre d'élèment
	nbElems = eval(nbElems + op);
	if (nbElems < 0 ) { nbElems = 0; }
	if (i == '1') { document.formulaire1.saisie_nb_recherche.value=nbElems; }
	if (i == '2') { document.formulaire2.saisie_nb_recherche.value=nbElems; }
	if (i == '3') { document.formulaire3.saisie_nb_recherche.value=nbElems; }
}

function prepEnvoi(a) {
	var tab = new Array();
	if (a == '1') { var data = document.formulaire1.saisie_recherche.options; }
	if (a == '2') { var data = document.formulaire2.saisie_recherche.options; }
	if (a == '3') { var data = document.formulaire3.saisie_recherche.options; }
	for (i=0;i<data.length;i++) { tab.push(data[i].value); }
	if (a == '1') { document.formulaire1.saisie_recherche_final.value=tab.join(","); }
	if (a == '2') { document.formulaire2.saisie_recherche_final.value=tab.join(","); }
	if (a == '3') { document.formulaire3.saisie_recherche_final.value=tab.join(","); }
}

</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"   >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Mise en place des matières par groupement" ?> </font></b></td></tr>
<tr  id='cadreCentral0' >
<td valign='top'>
<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
validerequete("menuadmin");
?>
<ul>
<br>
<?php
$j=0;
for ($i=1;$i<=$_POST["nbgroupement"]; $i++) { ?>
	<div id="grpmat<?php print $i ?>" style="position:absolute;top:30;left:30;display:none;width:800px;height:350px;padding:1px;border:1px #666 solid;background-color:#ddd;z-index:1000">
	<form method=post name="formulaire<?php print $i?>" >
	<table border=0 width=100%>
	<tr><td width=33% align=center>
	<i>Matières</i><br>
	<select size=22 name="saisie_depart"  style="width:280px">
	<?php 
	$data=affMatiere();
	for($a=0;$a<count($data);$a++)  {
		if ($data[$a][1] != "") {
			print "<option STYLE='color:#000066;background-color:#CCCCFF' value='".$data[$a][0]."' title=\"".$data[$a][1]." ".preg_replace('/0$/',"",$data[$a][2])."\" >".$data[$a][1]." ".preg_replace('/0$/',"",$data[$a][2])."</option>";
	        }
	}
	?>
	</select>
	</td>
<td width=15% align=center>
<input type="button" value="<?php print LANGCHER5?> >>>" onClick="calcul('+1','<?php print $i ?>');Deplacer(this.form.saisie_depart,this.form.saisie_recherche,'Choisissez un élèment')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
<br><br><br>
<input type="button" value="&lt;&lt;&lt; <?php print LANGCHER6 ?>" onClick="calcul('-1','<?php print $i ?>');Deplacer(this.form.saisie_recherche,this.form.saisie_depart,'Choisissez un élèment')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
</td>
<td width=33% align=center>
<i>Matières falcutatives</i><br>
<select size=22 name="saisie_recherche" style="width:280px" multiple="multiple">
<?php
$data=aff_grp_bull_leap("bulletinLeap_$i",$_POST["saisie_classe"]);
$idliste=$data[0][1];
$idliste=preg_replace('/[{}]/','',$idliste);
$tabens_1=explode(",",$idliste);
foreach ($tabens_1 as $key=>$value) {
	print "<option value='".$value."' >".chercheMatiereNom($value)."</option>";
}
if ($idliste == "") {
	print "<OPTION>-------------</OPTION>";
}
?>
</select>
<script language="javascript">
<?php if ($idliste == "") { ?>
	document.formulaire<?php print $i?>.saisie_recherche.options.length=0;
<?php } ?>
</script>
</td></tr></table>
<input type="hidden" name="saisie_recherche_final" >
<input type="hidden" name="saisie_nb_recherche" >
<br>
<center><span id='retourenr<?php print $i?>' style='color:red;'></span></center><br>
<table align="center">
<tr>
<td>
<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="new Effect.Shrink('grpmat<?php print $i ?>', 1)" />
<input type="button" name="falcutatif" value="<?php print LANGENR ?>" class="BUTTON" onclick="prepEnvoi('<?php print $i ?>');enrGrpMat('<?php print $_POST["saisie_classe"] ?>',this.form.saisie_recherche_final.value,'<?php print $_POST["label"][$j]?>','<?php print $i ?>','retourenr<?php print $i?>');" >
</td></tr></table>
</form>	
</div>
<?php if ($_POST["label"][$j] != "") { ?>
<font class='T2'> Groupement <?php print $_POST["label"][$j++] ?> [<a href='#' onclick="new Effect.Grow('grpmat<?php print $i ?>', 1); return false;">Configurer</a>]<br><br>
<?php } ?>

<?php 
} 
?>

<input type='hidden'  name="saisie_classe" value="<?php print $_POST["saisie_classe"] ?>" />
<input type='hidden'  name="nbgroupement" value="<?php print $_POST["nbgroupement"] ?>" />
<br><br>
<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="window.close()" />
</ul>
</BODY></HTML>
