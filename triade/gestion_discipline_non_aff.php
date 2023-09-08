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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGDISC32?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<BR>
<table border=0 width=100%><tr><td colspan=2>
<form name=formulaire5 method=post >
<?php
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
include_once("./librairie_php/ajax.php");
ajax_js();

if (isset($_GET["suppid"])) {
	$supp=delete_discipline_prof($_GET["suppid"]);
	if ($supp) {
		print "<center><font class=T2>Données supprimées</font></center><br><hr>";
	}

}

if (isset($_POST["creat_config_retenue"])) {

	$id=$_POST["saisie_id"];
	$qui=$_SESSION["nom"];

	for ($i=1;$i<=$id;$i++) {

		$saisieSanction="saisie_sanction_".$i;
		$sanction=$_POST[$saisieSanction];
		$motif="Cumul de la sanction :".rechercheCategory($sanction);

		$choisi="saisie_choisi_".$i;
		$choisi=$_POST[$choisi];
		$id_eleve="saisie_eleveid_".$i;
		$id_eleve=$_POST[$id_eleve];
		$en_retenue="saisie_retenu_".$i;
		$en_retenue=$_POST[$en_retenue];
/*

		print "<Br>";print "<Br>";
		print $sanction;
		print "<Br>";
		print $motif;
		print "<Br>";
		print $id_eleve;
 */

		if ($en_retenue == "3") {

			modif_sanction_sans_retenu($id_eleve);
		}


		if ($en_retenue == "1" ) {

			$date_retenue="saisie_date_retenue_".$i;
			$date_retenue=$_POST[$date_retenue];
			$heure_retenue="saisie_heure_retenue_".$i;
			$heure_retenue=$_POST[$heure_retenue];
			$duree_retenue="saisie_duree_retenue_".$i;
			$duree_retenue=$_POST[$duree_retenue];
			$devoir="saisie_devoir_".$i;
			$devoir=$_POST[$devoir];

			$okheure=checkTime($heure_retenue);
			$okduree=checkTime($duree_retenue);
			$elements=preg_split('/\//',$date_retenue);
			if ( (checkdate($elements[1],$elements[0],$elements[2])) && ($okheure)  && ($okduree)  ) {
				$cr=create_discipline_retenue($id_eleve,dateFormBase($date_retenue),$heure_retenue,dateDMY2(),$_SESSION["nom"],$sanction,$qui,$motif,$duree_retenue,$devoir);
	       		if($cr){
	 			$cr=modif_discipline_sanction($id_eleve,$_SESSION["nom"],dateDMY2(),$sanction,$idsanction);
            	}else {
            			print "<script language=JavaScript>location.href='gestion_discipline.php?err=1&id=$id_eleve'</script>";
            	}
            }
	    }
    }
}


if (isset($_GET["err"])) {
	alertJs(LANGDISC33." ".recherche_eleve($_GET["id"])." ".LANGDISC33bis);
	print "<script language=JavaScript>location.href='gestion_discipline.php'</script>";
}

// ------------------------------------------
// sanction,nb,origin_user,date_saisie
$data=recup_sanction();
$ok=0;
$j=0;
for($i=0;$i<count($data);$i++) {
	$data2=recherche_si_retenu($data[$i][0],$data[$i][1]); 
	if (count($data2) > 0){
    		$data2=array_unique($data2);

		foreach ($data2 as $value) {
			$ok=1;
			$j++;
			$id_eleve=$value;
			$sanction=$data[$i][0];
		?>

		<table border=1 width=100% align=center >
		<tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
		<td >
		<B>
		<?php print recherche_eleve($id_eleve)?>
		<input type=hidden name="saisie_eleveid_<?php print $j?>" value="<?php print $id_eleve?>">
		<input type=hidden name="saisie_sanction_<?php print $j?>" value="<?php print $sanction?>">
		</b>
		<?php print LANGDISC34?> <B><?php print nb_fois($value,$data[$i][0])?></B> <?php print LANGDISC34bis ?> <B><?php print rechercheCategory($sanction)?></B>
		<BR><BR> En retenue :
		<select name="saisie_retenu_<?php print $j?>" onChange="Valid_retenue2('<?php print $j?>')" >
		<option value=0 STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGNON ?></option>
		<option value=1 STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGOUI ?></option>
		<option value=3 STYLE='color:#000066;background-color:yellow'><?php print LANGSUPP ?></option>
		</select>
		<?php print strtolower(LANGDISC11bis)?> <input type=text name="saisie_date_retenue_<?php print $j?>" size=13 onKeyPress="onlyChar(event)" >
		<?php
		include_once("librairie_php/calendar.php");
		calendarpopup("id1$j","document.formulaire5.saisie_date_retenue_$j",$_SESSION["langue"],"0");
		?>
		<?php print LANGTE13?> <input type=text name="saisie_heure_retenue_<?php print $j?>" size=5 onclick="this.value=''"  onKeyPress="onlyChar2(event)" >
		<?php print strtolower(LANGABS21)?> <input type=text name="saisie_duree_retenue_<?php print $j?>" size=5 onclick="this.value=''" onKeyPress="onlyChar2(event)" >
		<br>

		<textarea cols=110 rows=2 name="saisie_devoir_<?php print $j?>"><?php print LANGPROFJ ?> : </textarea>
		</td>
		</tr>
		</table>
		<?php
		}
	}
}
if ($ok == 1) {
?>
<BR>
<table align=center><tr><td>
<input type=hidden name="saisie_id" value="<?php print $j?>">
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","creat_config_retenue"); //text,nomInput</script>
</tr></td></table> <hr>
<?php } ?>

</form>

<UL>
<?php
// ------------------------------------------
$data=cherche_eleve_retenu();
//id_eleve,sanction,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait
for($i=0;$i<count($data);$i++) {
        $nom_eleve=recherche_eleve_nom($data[$i][0]);
        $prenom_eleve=recherche_eleve_prenom($data[$i][0]);
        $sanction=rechercheCategory($data[$i][1]);
        $devoir_a_faire=$data[$i][2];
        $classe=$data[$i][9];
	$nomprof=$data[$i][8];
	$description_fait=trim($data[$i][11]);
	print "L'élève <font color=red><b>$nom_eleve $prenom_eleve</b></font> ($classe) a été sanctionné d'une retenue
        suite à la sanction : <b>$sanction</b><br> ";
	print "Description des faits : $description_fait <br>";
	print "par l'enseignant(e) : ".recherche_personne($nomprof);
	print "<br><br><div align=right><input type=button value='Enregistrer la retenue' class='bouton2' onclick=\"open('gestion_discipline-retenu-ajout.php?id=".$data[$i][10]."','_parent','')\">&nbsp;&nbsp;";
	print "<input type=button value='Supprimer la retenue' class='bouton2' onclick=\"open('gestion_discipline_non_aff.php?suppid=".$data[$i][10]."','_parent','')\">&nbsp;&nbsp;</div>";
	print "<br>";
	print "<hr width=80%>";
	print "<br>";

}
?>
     </td></tr></table>

     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
     ?>
   </BODY></HTML>
