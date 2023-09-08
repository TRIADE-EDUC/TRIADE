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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
        <?php include("./librairie_php/lib_licence.php");
        if (empty($_SESSION["adminplus"])) {
               print "<script>";
                print "location.href='./base_de_donne_key.php'";
                print "</script>";
                exit;
        }
	// connexion (après include_once lib_licence.php obligatoirement)
	include_once("librairie_php/db_triade.php");
	$cnx=cnx();
	?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
// affichage de la classe
if(isset($_POST["consult"])) {
	$saisie_classe=$_POST["saisie_classe"];
	$nbIdClasse=$_POST["nbIdClasse"];
	$annee=$_POST["annee"];
	$sqlsuite="WHERE ";
	$cl="";
	for($i=0;$i<$nbIdClasse;$i++) {
		$idclasse=$_POST["idclasse_$i"];
		if (trim($idclasse) != "") {
			$sqlsuite.=" ( e.classe='$idclasse' AND c.code_class='$idclasse' "; 
			if (trim($annee) != "") {
				$sqlsuite.=" AND ( e.annee_scolaire='$annee' OR  e.annee_scolaire IS NULL) )  OR";
			}else{
				$sqlsuite.=" ) OR";
			}
			$cl.=" - ".chercheClasse_nom($idclasse);
		}
	}
	if ($sqlsuite == "WHERE ") { $sqlsuite=""; }
	if ($sqlsuite != "") {
		$sqlsuite=preg_replace('/OR$/','',$sqlsuite);
		$sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e,${prefixe}classes c $sqlsuite AND e.annee_scolaire='$annee' GROUP BY e.nom,e.prenom  ORDER BY e.nom";
		$res=execSql($sql);
		$data=chargeMat($res);

	}else{
		$data=array();
	}

// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
?>
<form method=post action="chgmentClas11.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font id='menumodule1'><?php print LANGMESS383 ?><font id="color2"><B><?php print $cl?></font></td></tr>
<?php
if( count($data) <= 0 ) {
	print("<tr id='cadreCentral0'><td align=center valign=center>".LANGPROJ6."</td></tr></table>");
}else {
?>
<tr id='cadreCentral0'>
<td colspan=3 >
<br>
<?php print  "<font class=T2>Indiquer la nouvelle année scolaire : </font>" ?> </font> <select name="anneefutur" onChange="validSelect(this.value)" > <?php filtreAnneeScolaireSelectNote('',2) ?> </select>
<br><br>

<font class=T2><?php print LANGMESS381 ?></font>
<select id="new_classe" name="new_classe" disabled='disabled' onChange="validSelect2(this.value)" >
<option value='rien' STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX ?></option>
<option value='quit' STYLE='color:#000066;background-color:red' ><?php print LANGBASE37 ?></option>
<option value='sansclasse' STYLE='color:#000066;background-color:yellow' ><?php print LANGMESS385 ?></option>
<?php
select_classe2('20'); // creation des options
?>
	</select> &nbsp;&nbsp; <i><?php print count($data)." ".INTITULEELEVES."</i>";  ?></i>
	&nbsp;/&nbsp;<font class='T1'><?php print LANGTOUS ?> : <input type='checkbox' onclick='selectTous()' id='tous' disabled='disabled' />
<?php
	$nh=0;
	for($i=0;$i<count($data);$i++) {
		if ($data[$i][1] != "") {
			if ($nh == 0) { print "<tr>"; }
	?>
			<td class="tabnormal2" id="tr<?php print $i ?>" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
			<input type=checkbox name="idEleve_<?php print $i ?>" disabled='disabled'  id='eleve<?php print $i ?>' value="<?php print $data[$i][1]?>"  onClick="DisplayLigne('tr<?php print $i ?>');" > 
			<label for='eleve<?php print $i ?>'><?php print strtoupper(infoBulleEleveSansLoupe($data[$i][1],$data[$i][2]))?> <?php print trunchaine(trim($data[$i][3]),15)?></td>
			<?php
			$nh++;
			if ($nh == 3) {
				print "</tr>";
				$nh=0;
			}
		}
	}	
	?>

</td></tr>
</table>
<script>
function validSelect(val) {
	if (val != "") {
		document.getElementById('new_classe').disabled=false;
		document.getElementById('tous').disabled=false;
	}else{
		document.getElementById('new_classe').disabled=true;
		document.getElementById('tous').disabled=true;
		document.getElementById('new_classe').selectedIndex='0';
		for(i=0;i<<?php print count($data)?>;i++){
			document.getElementById('eleve'+i).checked=false;
			document.getElementById('eleve'+i).disabled=true;
		}
	}
}

function validSelect2(val) {
        if (val != "rien") {
                document.getElementById('tous').disabled=false;
                for(i=0;i<<?php print count($data)?>;i++){
                        document.getElementById('eleve'+i).disabled=false;
                }
        }else{
                document.getElementById('tous').disabled=true;
                document.getElementById('tous').checked=false;
                for(i=0;i<<?php print count($data)?>;i++){
                        document.getElementById('eleve'+i).checked=false;
                        document.getElementById('eleve'+i).disabled=true;
                }
        }
}

</script>
<?php
}
print "<input type=hidden name='nbEleve' value='".count($data)."'>";
print "<br><br>";
?>
<?php
	if( count($data) > 0 ) {
?>
	<ul><ul><ul><script language=JavaScript>buttonMagicSubmit("<?php print LANGBASE38 ?>","rien"); //text,nomInput</script></ul></ul></ul><br><br>
<?php
	}
}
?>
</form>
<br><br>
<script>
function selectTous() {
	if (document.getElementById('tous').checked == true) {
		for(i=0;i<=<?php print $i ?>;i++) {
			document.getElementById('eleve'+i).checked=true;
		}
	}else{
		for(i=0;i<=<?php print $i ?>;i++) {
			document.getElementById('eleve'+i).checked=false;
		}
	}

}
</script>

<form method="post" action="chgmentClas00.php" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBASE23 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<ul>
<br>
<font class=T2><?php print "Indiquer l'année scolaire en cours" ?> :</font> <select name="annee">
					<?php filtreAnneeScolaireSelectNote('',4) ?>
					<option value="" id='select1' ><?php print LANGMESS379 ?></option>
					</select>
<br><br>
<font class=T2><?php print LANGMESS381 ?></font><br><br></ul>
<ul>
<?php checkbox_classe2() ?>
</ul>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult"); //text,nomInput</script></UL></UL></UL>
<br><br><br>
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY>
</HTML>
