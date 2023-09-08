<?php
session_start();
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
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include("./librairie_php/lib_licence.php");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE15?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<!-- //  debut -->
<br>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("menuadmin");
if (isset($_GET["suppidprof"])) {
	@delete_profp2($_GET["suppidprof"],$_GET["idclass"],$_GET["anneeScolaire"]);
}

if (isset($_POST["create"])) {
	for($i=0;$i<$_POST["nb"];$i++) { 
		$saisie_classe=$_POST["saisie_classe_$i"];
		@create_profp($_POST["saisie_prof"],$saisie_classe,$anneeScolaire);	
        }
	alertJs(LANGDONENR);
}
?>
<ul>
<form method='post' action='profpcreat.php'  >
<font class="T2"><?php print LANGBULL3 ?> :</font>
<select name='anneeScolaire' onChange="this.form.submit()" >
<?php
filtreAnneeScolaireSelectNote($anneeScolaire,10);
?>
</select>
</form>
<br>
<form method='post' action='profpcreat.php' >
<font class="T2"><?php print LANGPER6?> :</font> <select name="saisie_prof">
<option value="rien"  STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
<?php select_personne_2("ENS",35);?>
</select>
<br><br><br>
<font class="T2"><?php print LANGPER7?> :</font>
<br/>
<table align='center'>
<tr>
<?php
$data=affClasse();
$ii=0;
for($i=0;$i<count($data);$i++) {
        $nomclasse=$data[$i][1];
	$idclasse=$data[$i][0];
	print "<td style='padding-right:5px' ><input type='checkbox' value='$idclasse' name='saisie_classe_$i'  /> $nomclasse ";
	print "</td>";
	$ii++; 
 	if ($ii == 4) { print "</tr><tr>"; $ii=0; }
}
?>
</tr></table>
<input type='hidden' name='nb' value='<?php print count($data) ?>' />
<br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT18?>","create"); //text,nomInput</script>
<br><br>
<input type='hidden' name='anneeScolaire' value="<?php print $anneeScolaire ?>" />
</form>
</ul>
<hr width=70%>
<br>
<table border=1 width=90% bgcolor="#FFFFFF" bordercolor="#CCCC00" align=center>
<?php
nettoyageProfP();
$data=aff_prof_p($anneeScolaire);
for($i=0;$i<count($data);$i++) {
?>
<tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'" >
<td id="bordure" width='50%' >
<?php $nomprenom=recherche_personne($data[$i][0]); print ucwords(strtolower($nomprenom)); ?> </td><td id="bordure"  > <?php $data2=chercheClasse($data[$i][1]);print ucwords(preg_replace('/ /',"&nbsp;",$data2[0][1]));?>
</td>
<td id="bordure" width=5 >
&nbsp;[&nbsp;<a href="profpcreat.php?suppidprof=<?php print $data[$i][0]?>&idclass=<?php print $data[$i][1]?>&anneeScolaire=<?php print $anneeScolaire ?>"><?php print LANGacce21 ?></a>&nbsp;]&nbsp;
</td>
</tr>


<?php
}
?>
</table>
<br>
<br>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
