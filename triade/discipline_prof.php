<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}

error_reporting(0);
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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
include_once("./common/config2.inc.php");

$cnx=cnx();
error($cnx);

// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

$idSpid=$mySession[Spid];

// données DB utiles pour cette page
$sql="
SELECT
	a.code_classe,
	trim(c.libelle),
	a.code_matiere,
";
if(DBTYPE=='pgsql')
{
	$sql .= " trim(m.libelle)||' '||trim(m.sous_matiere)||' '||trim(langue), ";
}
elseif(DBTYPE=='mysql')
{
	$sql .= " CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(IFNULL(langue,''))), ";
}
$sql .= "
	a.code_groupe,
	trim(g.libelle)
FROM
	${prefixe}affectations a,
	${prefixe}matieres m,
	${prefixe}classes c,
	${prefixe}groupes g
WHERE
	code_prof='$idSpid'
AND a.code_classe = c.code_class
AND a.code_matiere = m.code_mat
AND a.code_groupe = group_id
AND a.annee_scolaire = '$anneeScolaire'
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
genMatJs('affectation',$data);
freeResult($curs);
unset($curs);
//htmlTableMat($data);
?>
<HTML>
<HEAD>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/jquery-min.js" ></script>
<script type="text/javascript">
<?php
$choixmatiere='1';
if (defined("CHOIXMATIEREPROF")) {
	$choixmatiere=CHOIXMATIEREPROF;
}
if (trim($choixmatiere) == "") { $choixmatiere='1'; }
?>
function upSelectMat(arg) {
	for(i=1;i<document.formulaire.sMat.options.length;i++){
		document.formulaire.sMat.options[i].value='';
		document.formulaire.sMat.options[i].text='';
	}
	var tmp=arg.value.split(":");
	var clas=tmp[0];
	var grp=tmp[1];
	var opt='<?php print $choixmatiere ?>';
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
</script>

<style>
ul { style-type:none;list-style: none; cursor:pointer;margin-left: 3px;padding-left: 0; }
li { padding:7x; }
</style>

</head>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_note.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Ajout d'une sanction disciplinaire" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td>
<!-- // fin  -->

<?php
//vsuite()
if (0) {
	?>
	<br>
	<center><?php print LANGKEY1 ?></center>
	<?php

}else {
	if ($choixmatiere == 0) {
		$onsubmit="onsubmit=\"return verifAccesNotebis()\"";
	}else{
		$onsubmit="onsubmit=\"return verifAccesNote()\"";
	}

?>
        <br />
        <blockquote>
	<form method="post" action="discipline_prof.php" >
        <font class="T2"><?php print LANGBULL29 ?> :</font>
        <select name='anneeScolaire' onChange="this.form.submit()"  >
        <?php
        filtreAnneeScolaireSelectNote($anneeScolaire,3);
        ?>
        </select>
        <br/>
        </form>

	<form method="POST" <?php print $onsubmit ?> name="formulaire" action="discipline_prof2.php" >
                 <font class="T2"><?php print LANGPROFG ?>  :</font>

 <select name="sClasseGrp" size="1" onChange="upSelectMat(this)">
 <option value="0" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX3 ?></option>
		 <?php
			 for($i=1;$i<count($data);$i++){
				 	if( $i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp) ){
						continue;
						}
					else {
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
				 </select>
				 <br /><br />

				 <font class="T2"><?php print LANGPROF1?> :</font>

				<select name="sMat" size="1"> <!-- saisie_matiere -->
                <option value="0" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
                <!--
				<option></option>
				<option></option>
				<option></option>
				<option></option>
				-->
				</select>
                 <BR><BR><br>
		 <UL><UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script><br><br>
		 </UL></UL></UL></UL></UL>
                 </form>
<?php
}
?>
</td></tr></table>
<br /><br />

<?php//-----------------------------------------------------------------------?>

<?php

include_once("./librairie_php/ajax.php");
ajax_js();
?>
<form method=post onsubmit="return valide_recherche_eleve_2()" action="gestion_discipline_modif.php" name="formulaire_2">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGDISC54 ?> </font></b></td>
     </tr>
     <tr  id='cadreCentral0' >
     <td >
<blockquote><BR>
<table border=0 cellspacing=0>
<tr><td style="padding-top:0px;" nowrap><font class="T2"><?php print LANGABS3?> : </font></td><td><input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" style="width:15em;"  /></td></tr>

<tr><td></td><td style="padding-top:0px;"><div id="userList" style="width:13.5em;border-style:none; background-color:#EEEEEE;"></div></td></tr>

</table>
<?php brmozilla($_SESSION["navigateur"]); ?>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rien"); //text,nomInput</script>
</UL></UL></UL>
 </blockquote>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
    </td></TR>
</TABLE>
</form>
<br />
<?php//-----------------------------------------------------------------------?>

     <!-- // fin  -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Liste de vos 10 dernières sanctions et retenues disciplinaires" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td>
<?php
if (isset($_GET["suppdevoir"])) {
//	 supp_discipline_prof($_GET["suppdevoir"]);
}
// sanction devoir à faire
$data=cherche_discipline_prof_devoir($_SESSION["id_pers"]);
//id_eleve,id_category,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait
for($i=0;$i<count($data);$i++) {
	$nom_eleve=recherche_eleve_nom($data[$i][0]);
	$prenom_eleve=recherche_eleve_prenom($data[$i][0]);
	$sanction=rechercheCategory($data[$i][1]);
	$devoir_a_faire=$data[$i][2];
	$motif=$data[$i][7];
	$classe=$data[$i][9];
	$lesfaits=$data[$i][11];
	print "&nbsp;".LANGacce1." <b>$nom_eleve $prenom_eleve</b> ($classe) ".LANGacce12." <font id=color2 >$sanction</font> ".LANGacce13." : <b>$motif</b>";
	print "<br>&nbsp;<u>Description des faits</u> : $lesfaits ";
	print "<br />&nbsp;<u>".LANGacce14."</u> <i>$devoir_a_faire</i>";
	print "<br />&nbsp;<u>".LANGacce15."</u>".dateForm($data[$i][3]);
	// print "<div align=right>[<a href='discipline_prof.php?suppdevoir=".$data[$i][10]."' >".LANGacce21."</a>]&nbsp;&nbsp;&nbsp;</div>";
	print "<br /><br />";
}
print "<br>";
if (count($data)) print "<hr><br />";

$data=cherche_discipline_prof_devoir_retenu($_SESSION["id_pers"]);
//id_eleve,sanction,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof FROM discipline_prof
for($i=0;$i<count($data);$i++) {
        $nom_eleve=recherche_eleve_nom($data[$i][0]);
        $prenom_eleve=recherche_eleve_prenom($data[$i][0]);
        $sanction=rechercheCategory($data[$i][1]);
        $devoir_a_faire=stripslashes($data[$i][2]);
        $motif=$data[$i][7];
        $classe=$data[$i][9];
        print "&nbsp;".LANGacce3."<b>$nom_eleve $prenom_eleve</b> ($classe) <font color=red><b>".LANacce31." <u>$sanction</u> ".LANacce32."<b>$motif</b>";
        print "<br />&nbsp;".LANGacce4." <i>$devoir_a_faire</i><br /><br /> ";
       //  print "<div align=right>[<a href='discipline_prof.php?suppdevoir=".$data[$i][10]."' >".LANGacce5."</a>]&nbsp;&nbsp;&nbsp;</div>";
       // print "<br />";
}
?>


     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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

<script>
    $(document).ready(function(){
        $('#search').keyup(function(){
            var query = $(this).val();
            if(query != '')
            {
                $.ajax({
                    url:"librairie_php/search_eleve.php",
                    method:"POST",
                    data:{query:query}, success:function(data)
                    {                        $('#userList').fadeIn();
                        $('#userList').html(data);
                    }
                });
            }
        });
        $(document).on('click', 'li', function(){
            $('#search').val($(this).text());
            $('#userList').fadeOut();
        });
    });
</script>

   </BODY>
   </HTML>
   <?php @Pgclose() ?>
