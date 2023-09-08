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
include("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();

if (($_SESSION["membre"] == "menuprof") || (isset($_POST["adminIdprof"]))) {

	if (isset($_POST["adminIdprof"])) {
		$idpers=$_POST["adminIdprof"];
	}else{
	        $idpers=$_SESSION['id_pers'];
	}	
        $sql="
SELECT
        a.code_classe,
        trim(c.libelle),
        a.code_matiere,
";
$sql .= " CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(langue) ) , ";
$sql .= "
        a.code_groupe,
        trim(g.libelle)
FROM
        ${prefixe}affectations a,
        ${prefixe}matieres m,
        ${prefixe}classes c,
        ${prefixe}groupes g
WHERE
        code_prof='$idpers'
AND a.code_classe = c.code_class
AND a.code_matiere = m.code_mat
AND a.code_groupe = group_id
AND a.annee_scolaire ='$anneeScolaire'
AND (a.visubull = '1' OR a.visubullbtsblanc = '1')
AND c.offline = '0'
GROUP BY a.code_matiere,a.code_classe
ORDER BY
        c.libelle,m.libelle
";

$curs=execSql($sql);
$data=chargeMat($curs);
@array_unshift($data,array()); // nécessaire pour compatibilité
// patch pour problème sous-matière à 0
for($i=0;$i<count($data);$i++){
        $tmp=explode(" 0 ",$data[$i][3]);
        $data[$i][3]=$tmp[0].' '.$tmp[1];
}
// fin patch
freeResult($curs);
unset($curs);

}
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<?php genMatJs('affectation',$data); ?>
<script type="text/javascript">
function upSelectMat(arg) {
        for(i=1;i<document.formulaire.sMat.options.length;i++){
                document.formulaire.sMat.options[i].value='';
                document.formulaire.sMat.options[i].text='';
        }
        var tmp=arg.value.split(":");
        var clas=tmp[0];
        var grp=tmp[1];
        var opt=1;
        for(i=0;i<affectation.length;i++) {
                if(affectation[i][0] == clas && affectation[i][4] == grp) {
                myOpt=new Option();
                myOpt.value = affectation[i][2];
                myOpt.text = affectation[i][3];
                myOpt.text = myOpt.text.replace(/ 0 *$/,"");   // supprime le 0 de la matiere ajout ET
                document.formulaire.sMat.options[opt]=myOpt;
                opt++;
                }
        }
        return true;
}

function getRequete2bis() {
        if (window.XMLHttpRequest) {
                result = new XMLHttpRequest();     // Firefox, Safari, ...
        }else {
              if (window.ActiveXObject)  {
              result = new ActiveXObject("Microsoft.XMLHTTP");    // Internet Explorer
              }
        }
        return result;
}



function modifText(text,rt,id,libelle) {
	var requete = getRequete2bis();
        var corps="text="+encodeURIComponent(text)+"&id="+encodeURIComponent(id)+"&libelle="+encodeURIComponent(libelle);
        if (requete != null) {
                requete.open("POST","ajaxModifSavoirEtre.php",true);
                requete.onreadystatechange = function() {
                        if(requete.readyState == 4) {
                                if(requete.status == 200) {
					document.getElementById(rt).innerHTML="&nbsp;<img src='image/commun/ok.png' >";
                                }
                        };
                }
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }

} 
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Savoir / être" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<blockquote>
<?php if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {  ?>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire" action="savoiretre.php" >
<?php 
if (isset($_POST["adminIdprof"])) {
?>
<input type='hidden' name="adminIdprof" value="<?php print $_POST["adminIdprof"] ?>" />
<?php
}
?>
<BR>
<font class="T2"><?php print LANGCARNET2 ?> : </font><select id="sClasseGrp" name="sClasseGrp">
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_classe(); // creation des options
?>
</select> 
<BR><br>
<font class="T2"><?php print LANGBULL3 ?> :</font>
                 <select name='anneeScolaire' >
                 <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,8);
                 ?>
                 </select>
<br><br>
<UL><UL><UL>
<table><tr><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult"); //text,nomInput</script></td></tr></table>

<br><br>
</UL></UL></UL>
</form>
<!--  -->

<?php 
} 

if ($_SESSION["membre"] == "menuprof") { 
?>
<br>

<form method="POST" name="formulaire0" action="savoiretre.php">
<font class="T2"><?php print LANGBULL29 ?> :</font>
                 <select name='anneeScolaire' onChange="document.formulaire0.submit()">
                 <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,2);
                 ?>
                 </select>
</form>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire" action="savoiretre.php" >
<font class="T2"><?php print LANGCARNET2 ?> : </font><select id="sClasseGrp" name="sClasseGrp"  onChange="upSelectMat(this)" >
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
                                 for($i=1;$i<count($data);$i++){
                                        if ($i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp) ){
                                                continue;
                                        }else{
                                                // utilisation de l'opérateur ternaire expr1?expr2:expr3;
                                                $libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
						if (isset($verif[$libelle])) continue;
	                                        $verif[$libelle]=$libelle;
                                                print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0].":".$data[$i][4]."\">".$libelle."</option>\n";
                                        }
                                        $gtmp=$data[$i][4];
                                        $ctmp=$data[$i][0];
                                 }
                                 unset($gtmp);
                                 unset($ctmp);
                                 unset($libelle);
                                 unset($verif);
                                 ?>
</select><BR><br>
<font class="T2"><?php print LANGPROF1?> :</font> <select name="sMat" size="1"> <!-- saisie_matiere -->
<option value="0" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
</select>
<BR><BR>

<UL><UL><UL>
<table><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE3 ?>","create"); //text,nomInput</script></td><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult"); //text,nomInput</script></td></tr></table>
<br><br>
</UL></UL></UL>
</form>
<!--  -->

<?php } ?>


</blockquote>
</form>

<!-- // fin form -->
</td></tr></table>
<?php

if (isset($_GET["idsupp"])) { deleteSavoirEtre($_GET["idsupp"],$_GET['libelle']); }

if ((isset($_POST["consult"])) || (isset($_GET["idsupp"])))  {

	if (isset($_GET["idclasse"])) $saisie_classe=$_GET["idclasse"];
	if (isset($_POST["sClasseGrp"])) $saisie_classe=$_POST["sClasseGrp"];
	if ($anneeScolaire == "") $anneeScolaire=anneeScolaireViaIdClasse($saisie_classe);

//	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire') UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  ORDER BY e.nom)";

	$sql=" SELECT s.* FROM ( SELECT libelle,elev_id,nom,prenom,date_naissance,regime,numero_eleve,code_compta,nomtuteur,prenomtuteur,civ_1,telephone,email FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' UNION ALL SELECT c.libelle,e.elev_id,e.nom,e.prenom,e.date_naissance,e.regime,e.numero_eleve,e.code_compta,e.nomtuteur,e.prenomtuteur,e.civ_1,e.telephone,e.email FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY s.nom";

	//$sql="SELECT libelle,elev_id,nom,prenom,code_class FROM ${prefixe}eleves,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
	$cl=$data[0][0];
	?>
	<BR><BR><BR>
	<form method='post' action='savoiretre2.php' >
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
	<tr id='coulBar0' ><td height="2" colspan='5' ><b><font   id='menumodule1' >
	<?php print LANGELE4 ?> : <font id="color2" ><B><?php print $cl?></font> / </b> <?php print LANGCOM3 ?> <font id="color2"><b><?php print count($data) ?></b></font> / Année Scolaire <font id="color2"><b><?php print $anneeScolaire ?></b></font></font>
	</font></td>
	</tr>
	<?php
	if (count($data) <= 0 ) {
		print("<tr id='cadreCentral0'><td  align=center valign=center><font class=T2>".LANGRECH1."</font></td></tr>");
	}else{
		for($i=0;$i<count($data);$i++) { 
			//$nomeleve=infoBulleEleveSansLoupe($data[$i][1],strtoupper($data[$i][2]));
			$nomeleve=strtoupper($data[$i][2]);
			print "<tr>";
			print "<td bgcolor='yellow' colspan='5' >$nomeleve ".ucwords($data[$i][3])."</td>";
			$dataInfo=recupSavoirEtre($data[$i][1],$saisie_classe,$anneeScolaire);
			// ponctualite,motivation,dynamisme,id,date,idpers,idmatiere
			for($j=0;$j<count($dataInfo);$j++) { 
				$ponct=stripslashes($dataInfo[$j][0]);
				$motiv=stripslashes($dataInfo[$j][1]);
				$dynam=stripslashes($dataInfo[$j][2]);
				$nommatiere=chercheMatiereNom($dataInfo[$j][6]);
				$id=$dataInfo[$j][3];
				$date=dateForm($dataInfo[$j][4]);
				$idpers=$dataInfo[$j][5];
				$personne=preg_replace('/ /','&nbsp;',recherche_personne2($idpers));				
				$motiv=preg_replace('/"/',"&quot;",$motiv);
				$dynam=preg_replace('/"/',"&quot;",$dynam);
				$ponct=preg_replace('/"/',"&quot;",$ponct); 
				$motiv=preg_replace('/\'/',"&acute;",$motiv);
				$dynam=preg_replace('/\'/',"&acute;",$dynam);
				$ponct=preg_replace('/\'/',"&acute;",$ponct); 
				if (trim($ponct) != "") {
					$input="<textarea  cols=35 style=\'width:100%\' rows=3 size=55 onBlur=modifText(this.value,\'rt_ponct_$i$j\',\'$id\',\'ponct\') >$ponct</textarea><span id=\'rt_ponct_$i$j\'></span>";
					print "<tr bgcolor='#FFFFFF' ><td width='5%'>Aptitude à manifester de l'intérêt pour son travail&nbsp;:&nbsp;</td><td id='ponct_$i$j' >$ponct</td><td width='5%'>par&nbsp;$personne&nbsp;le&nbsp;$date<br>Matière&nbsp;:&nbsp;$nommatiere</td><td width='55' align='center' >&nbsp;<a href='savoiretre.php?idsupp=$id&idclasse=$saisie_classe&libelle=ponct' ><img src='image/commun/trash.png' align='center' title='Supprimer' border='0' /></a>&nbsp;&nbsp;<a href='#' onClick=\"document.getElementById('ponct_$i$j').innerHTML='$input'; return false;\" ><img src='image/commun/editer.gif' align='center' title='Editer'  border='0' /></a>&nbsp;</td></tr>";
				}
				if (trim($motiv) != "") {
					$input="<textarea cols=35 rows=3 style=\'width:100%\' onBlur=modifText(this.value,\'rt_motiv_$i$j\',\'$id\',\'motiv\') >$motiv</textarea><span id=\'rt_motiv_$i$j\'></span>";
					print "<tr bgcolor='#FFFFFF' ><td>Aptitude à la méthode et au soin&nbsp;:&nbsp;</td><td id='motiv_$i$j' >$motiv</td><td>par&nbsp;$personne&nbsp;le&nbsp;$date<br>Matière&nbsp;:&nbsp;$nommatiere</td><td width='55' align='center' >&nbsp;<a href='savoiretre.php?idsupp=$id&idclasse=$saisie_classe&libelle=motiv' ><img src='image/commun/trash.png' align='center' title='Supprimer' border='0' /></a>&nbsp;&nbsp;<a href='#' onClick=\"document.getElementById('motiv_$i$j').innerHTML='$input'; return false;\" ><img src='image/commun/editer.gif' align='center' title='Editer'  border='0' /></a>&nbsp;</td></tr>"; 
				}
				if (trim($dynam) != "") {
					$input="<textarea cols=35 rows=3 style=\'width:100%\' onBlur=modifText(this.value,\'rt_dyna_$i$j\',\'$id\',\'dyna\') >$dynam</textarea><span id=\'rt_dyna_$i$j\'></span>";
					print "<tr bgcolor='#FFFFFF' ><td>Aptitude à écouter&nbsp;:&nbsp;</td><td id='dyna_$i$j' >$dynam</td><td>par&nbsp;$personne&nbsp;le&nbsp;$date<br>Matière&nbsp;:&nbsp;$nommatiere</td><td width='55' align='center' >&nbsp;<a href='savoiretre.php?idsupp=$id&idclasse=$saisie_classe&libelle=dyna' ><img src='image/commun/trash.png' align='center'  title='Supprimer' border='0' /></a>&nbsp;&nbsp;<a href='#' onClick=\"document.getElementById('dyna_$i$j').innerHTML='$input'; return false;\"  ><img src='image/commun/editer.gif' align='center' title='Editer'  border='0'  /></a>&nbsp;</td></tr>";
				}
			}		
		}
	}
	print "</td></tr></table>";
}


// affichage de la classe
if ((isset($_POST["create"])) || (isset($_GET["idmodif"])))  {
	if (isset($_GET["idmodif"])) $idmodif=$_GET["idmodif"];
	if (isset($_GET["idclasse"])) $saisie_classe=$_GET["idclasse"];
	if (isset($_POST["sClasseGrp"])) $saisie_classe=$_POST["sClasseGrp"];
	$anneeScolaire=anneeScolaireViaIdClasse($saisie_classe);
	 $sql=" SELECT s.* FROM ( SELECT libelle,elev_id,nom,prenom,date_naissance,regime,numero_eleve,code_compta,nomtuteur,prenomtuteur,civ_1,telephone,email FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' UNION ALL SELECT c.libelle,e.elev_id,e.nom,e.prenom,e.date_naissance,e.regime,e.numero_eleve,e.code_compta,e.nomtuteur,e.prenomtuteur,e.civ_1,e.telephone,e.email FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY s.nom";

	//$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire') UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  ORDER BY e.nom)";
	// $sql="SELECT libelle,elev_id,nom,prenom,code_class FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
	$cl=$data[0][0];
?>
<BR><BR><BR>
<form method='post' action='savoiretre2.php' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >

<tr id='coulBar0' ><td height="2" colspan='5' ><b><font   id='menumodule1' >
<?php print LANGELE4 ?> : <font id="color2" ><B><?php print $cl?></font> / </b> <?php print LANGCOM3 ?> <font id="color2"><b><?php print count($data) ?></b></font>  / Année Scolaire <font id="color2"><b><?php print $anneeScolaire ?></b></font></font></font></td>
<tr>
<td colspan='5' bgcolor="white" align='center'  >

<br>Matière : <font id="color2" ><B><?php print  chercheMatiereNom($_POST['sMat']) ?></b></font>
<br>
<br><i>Vous pouvez saisir jusqu'à 250 caractères par champs.</i>
</td></tr>
</tr>
<?php
if (count($data) <= 0 ) {
	print("<tr id='cadreCentral0'><td  align=center valign=center><font class=T2>".LANGRECH1."</font></td></tr>");
}else{ ?>
	<tr >
	<td bgcolor="yellow"><B><?php print LANGTP1 ?></B></td>
	<td bgcolor="yellow"><B><?php print LANGTP2 ?></B></td>
	<td bgcolor="yellow"><B><?php print "Aptitude à manifester de l'intérêt pour son travail" ?></B></td>
	<td bgcolor="yellow"><B><?php print "Aptitude à la méthode et au soin" ?></B></td>
	<td bgcolor="yellow"><B><?php print "Aptitude à écouter" ?></B></td>
	</tr>
	<?php 
	for($i=0;$i<count($data);$i++) { 
		$motiv="";
		$dynam="";
		$ponct="";
		if ($idmodif > 0 ) {
			$info=recupSavoirEtre2($idmodif); //ponctualite,motivation,dynamisme,id,date,ideleve
			$ideleveInfo=$info[0][5];
			if ($ideleveInfo == $data[$i][1]) { 
				$ponct=stripslashes($info[0][0]);
				$motiv=stripslashes($info[0][1]);
				$dynam=stripslashes($info[0][2]);
				$motiv=preg_replace('/"/',"&quot;",$motiv);
				$dynam=preg_replace('/"/',"&quot;",$dynam);
				$ponct=preg_replace('/"/',"&quot;",$ponct); 
		}
	}
?>
	<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<td ><?php print infoBulleEleveSansLoupe($data[$i][1],strtoupper($data[$i][2]))?><input type='hidden' name='ideleve_<?php print $i ?>' value='<?php print $data[$i][1] ?>' /></td>
	<td ><?php print ucwords($data[$i][3])?></td>
	<td ><textarea name='ponct_<?php print $i?>' rows='3' cols='25' maxlength='250' style='width:100%' ><?php print $ponct ?></textarea></td>
	<td ><textarea name='motiv_<?php print $i?>' rows='3' cols='25' maxlength='250' style='width:100%' ><?php print $motiv ?></textarea></td>
	<td ><textarea name='dynam_<?php print $i?>' rows='3' cols='25' maxlength='250' style='width:100%' ><?php print $dynam ?></textarea></td>
	</tr>
	<?php
	}
	print "<tr><td colspan='5' height='40' bgcolor='#FFFFFF' align='center'><table align='center'><tr><td><script language=JavaScript>buttonMagicSubmit('".VALIDER."','create');</script></td></tr></table></td></tr>";
}
print "</table>
	<input type='hidden' name='idclasse' value='$saisie_classe' />
	<input type='hidden' name='nb' value='".count($data)."' />
	<input type='hidden' name='idmatiere' value='".$_POST['sMat']."' />";
	if ($_SESSION["membre"] == "menuadmin") {
		print "<input type='hidden' name='adminIdprof' value='".$_POST['adminIdprof']."' />";
	}
print	"</form>";
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY>
</HTML>
