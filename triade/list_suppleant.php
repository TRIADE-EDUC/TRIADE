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
        <script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
        <script language="JavaScript" src="./librairie_js/function.js"></script>
	<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
        <title>Liste des suppléants</title>
        </head>
        <body id='bodyfond2' marginheight="0" marginwidtd="0" leftmargin="0" topmargin="0">
        <?php include("./librairie_php/lib_licence.php"); ?>
<BR><center><font size=3><U><?php print LANGSUPPLE ?></U></font></center>
	<BR><BR>
<form method=post name="formulaire" >
<table border="1" align=center width=90% bordercolor="#000000" style='border-collapse: collapse;' >
<tr><td align=center bgcolor="yellow"><?php print LANGNA1 ?> <?php print LANGNA2 ?> </td></td><td align=center bgcolor="yellow"><?php print LANGSUPPLE1 ?></td><td align=center bgcolor="yellow"><?php print LANGPARAM19 ?></td><td align=center bgcolor="yellow"><?php print LANGPARAM20 ?></td></tr>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();

if (isset($_POST["modif"])) {
	for($i=0;$i<$_POST["nb"];$i++) {
		$info="saisie_date_$i";
		$pers="saisie_pers_id_$i";
		$idprof="saisie_prof_id_$i";
		$cr=modifDateSuppleant($_POST[$info],$_POST[$pers],$_POST[$idprof]);
	}
	alertJs(LANGDONENR);
}

$sql="SELECT p.civ,p.nom,p.prenom,p1.prenom,p1.nom,v.date_ent,v.date_sort,v.pers_id,v.rpers_id FROM ${prefixe}personnel p, ${prefixe}vacataires v,${prefixe}personnel p1 WHERE p.pers_id=v.pers_id AND v.rpers_id=p1.pers_id ORDER BY 2";
$res=execSql($sql);
$data=chargeMat($res);
// $data : tab bidim
for($i=0;$i<count($data);$i++) {
?>
<tr>
	<td bgcolor="#FFFFFF" align=left><?php print civ($data[$i][0])." ".ucwords($data[$i][1])?> <?php print ucwords($data[$i][2])?>  </td>
	<td bgcolor="#FFFFFF" align=left>
		<select name='saisie_prof_id_<?php print $i ?>' >
		<option value='<?php print $data[$i][8] ?>' id='select0' ><?php $nomprenom=ucwords($data[$i][3])." ".ucwords($data[$i][4]); print trunchaine($nomprenom,30)?></option>
		<?php
		select_personne_2('ENS',30); // creation des options
		?>
		</select>
	</td>
	<td bgcolor="#FFFFFF" align=center><?php print dateForm($data[$i][5])?></td>
	<td bgcolor="#FFFFFF" align=center>
	<?php 
	if (strlen($data[$i][6]) > 0) { 
		include_once("librairie_php/calendar.php");
		calendarpopup("id1","document.formulaire.saisie_date_$i",$_SESSION["langue"],"1");
	}else{ 
		print "Inconnu"; 
	}?>
	<input type=text name="saisie_date_<?php print $i ?>" value="<?php print dateForm($data[$i][6])?>"  onclick="this.value=''" size=12 class=bouton2>
	<input type=hidden name="saisie_pers_id_<?php print $i ?>" value="<?php print $data[$i][7] ?>" >
	</td>
	</tr>
<?php
	}
?>
</table>
<BR><BR>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30 ?>","modif"); //text,nomInput</script>
<script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script>&nbsp;&nbsp;</td></tr></table><br>
<input type=hidden name="nb" value="<?php print count($data) ?>" >
</form>
</BODY></HTML>
