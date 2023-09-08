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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
$action="compta_consulte_retard_sms.php";
if (LAN == "non") { $disabledSMS="disabled='disabled'"; $action=""; $valideSMS="<br />".ERREUR1; }
if (!file_exists("./common/config-sms.php")) { $disabledSMS="disabled='disabled'"; $action=""; $valideSMS="<br />".LANGMESS37; }

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><?php print LANGMESS73?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<form method=post action="compta_consulte_retard.php" >
<br><ul>
<font class='T2'> Filtre : </font>
<?php
if (isset($_POST["filtre"])) {
	$saisie_classe=$_POST["filtre"];
	if ($saisie_classe != "") {
		$option="<option value='$saisie_classe' id='select0'>".chercheClasse_nom($saisie_classe)."</option>";
	}
}
?>
<select name='filtre' onchange="this.form.submit();">	
<?php	print $option; ?>
	<option value="" id=select0>Aucun</option>
	<?php
select_classe(); // creation des options
?>
</select> 
<br><br>
<?php
if (isset($_POST["anneescolairefiltre"])) {
        $anneescolairefiltre=$_POST["anneescolairefiltre"];
}
?>
&nbsp;&nbsp;<font class=T2>Filtre : </font><select name='anneescolairefiltre' onchange="this.form.submit();" > <?php filtreAnneeScolaireSelect($anneescolairefiltre) ?> </select>
</ul>
</form>
<form name="formulaire" method="post" action="<?php print $action ?>" >
<table  style='border-collapse: collapse;' width=100% border='1' bordercolor='#000000' >
<tr>
<td bgcolor='yellow' align='center' >&nbsp;Nom&nbsp;Prénom&nbsp;</td>
<td bgcolor='yellow' align='center' width=35% >&nbsp;Versement&nbsp;</td>
<td bgcolor='yellow' align='center' width=5% >&nbsp;Montant&nbsp;</td>
<td bgcolor='yellow' align='center' width=5% >&nbsp;Echéance&nbsp;</td>
<td bgcolor='yellow' align='center' width=5% ><input type='checkbox' onclick='checktous();' name='tous' ></td>
</tr>
<?php
if (isset($_POST['filtre'])){
	if ($_POST['filtre']  == "") {
		$sqlsuite="";
	}else{
		$sqlsuite="WHERE classe='".$_POST['filtre']."'";
	}
}else{
	$sqlsuite="";
}
$sql="SELECT elev_id,nom,prenom,classe FROM ${prefixe}eleves $sqlsuite ORDER BY nom";
$res=execSql($sql);
$dataE=ChargeMat($res);
$nb=0;
$a=0;
$total=0;
for($o=0;$o<count($dataE);$o++) {
	$ideleve=$dataE[$o][0];
	$nomeleve=$dataE[$o][1];
	$prenomeleve=$dataE[$o][2];
	$idclasse=$dataE[$o][3];;
//	$classe=chercheClasse_nom($idclasse);
	$dataV=recupConfigVersement($idclasse,$anneescolairefiltre); //id,idclasse,libellevers,montantvers,datevers
	if ($dataV == "") { $dataV=array(); }
	$dataVE=recupConfigVersementEleve($ideleve,$anneescolairefiltre);
	if ($dataVE == "") { $dataVE=array(); }
	$dataV=array_merge($dataV,$dataVE);
	for($j=0;$j<count($dataV);$j++) {
		$nb++;
		$affiche=0;
		$id=$dataV[$j][0];

		if(verifcomptaExclu($id,$ideleve)) { continue; }

		$data=recupInfoVersement($ideleve,$id); // ideleve,idversement,montantvers,datevers,modepaiement
		$dateVersement=$data[0][3];
		$idvers=$data[0][1];
		if ($dateVersement != "") { $dateVersement=dateForm($dateVersement); }
		$montantVers=number_format($data[0][2],2,'.','');
		$modepaiement=nl2br($data[0][4]);
		$dateVersOr=$dataV[$j][4];
		$montantavers=$dataV[$j][3];

		$dateduJour=date("Ymd");
		$dateVersOr=preg_replace('/-/',"",$dateVersOr);
	
		if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) {
			$affiche=1;
		}

		if (($montantVers < $dataV[$j][3] ) && ($dateduJour > $dateVersOr)  && ($montantVers != "0.00") ) {
			$affiche=1;		
		}
		
		if ($affiche == 1) {
			$a++;
			print "<tr id=\"tr$a\" class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\"  >";
			print "<td valign='top'>&nbsp;".strtoupper($nomeleve)." ".ucfirst($prenomeleve)."&nbsp;</td>";
			print "<td valign='top'>&nbsp;".$dataV[$j][2]."</td>";
			print "<td valign='top' align='right'>&nbsp;<b>".preg_replace('/ /','&nbsp;',affichageFormatMonnaie($dataV[$j][3]))."</b></td>";
			print "<td valign='top' align='center'>&nbsp;".dateForm($dataV[$j][4])."</td>";
			print "<td valign='top' align='center'><input type=checkbox name='ideleve[]' value='$ideleve' onClick=\"DisplayLigne('tr$a');\" /></td>";
			print "</tr>";
			$total+=$dataV[$j][3];
		}
	}
}
print "<tr>";
print "<td colspan='2' align='right' bgcolor='#FFFFFF' >Total&nbsp;:&nbsp;</td>";
print "<td align='center' bgcolor='#FFFFFF'  >&nbsp;<b>".preg_replace('/ /','&nbsp;',affichageFormatMonnaie($total))."</b></td>";
print "<td colspan='2'  ></td>";
print "</tr>";
?>
</table>
<center><br><input type='submit' name="consult" value='Envoyer un SMS' class='BUTTON' <?php print $disabledSMS ?> /><br>
<font id=color3><?php print $valideSMS ?></font>
</center>
</form>


<script>
function checktous() {
	var nb=<?php print  $a ?>;
	for(i=1;i<=nb;i++) {
		if (document.formulaire.tous.checked == false) {
			document.formulaire.elements[i].checked=false;
			document.getElementById('tr'+i).style.backgroundColor='';
		}else{
			document.formulaire.elements[i].checked=true;
			document.getElementById('tr'+i).style.backgroundColor='#c0c0c0';
		}

	}
	if (document.formulaire.tous.checked == false) {
		document.formulaire.tous.checked=false;
	}else{
		document.formulaire.tous.checked=true;
	}

}
</script>

<br /><br />
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
