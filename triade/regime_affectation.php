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
error_reporting(0);
include("./common/config.inc.php"); // futur : auto_prepend_file
include("./librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);

// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
// données DB utiles pour cette page
$Spid=$mySession["Spid"];
$sql="
SELECT
	a.code_classe,
	trim(c.libelle),
	a.code_matiere,
";
if(DBTYPE=='pgsql')
{
	$sql .=" trim(m.libelle)||' '||trim(m.sous_matiere)||' '||trim(langue), ";
}
elseif(DBTYPE=='mysql')
{
	$sql .=" CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(langue) ), ";
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
	code_prof='$Spid'
AND a.code_classe = c.code_class
AND a.code_matiere = m.code_mat
AND a.code_groupe = group_id
ORDER BY
	c.libelle
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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGPROF13 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td>
     <!-- // fin  -->
                 <form method="POST" onsubmit="return verifAccesFiche()" name="formulaire" action="regime_affectation2.php">
                 <br />
                 <ul>
                 <font class=T2><?php print LANGPROFG ?> :</font>
				<?php if ($_SESSION["membre"] == "menuprof") { ?>
					 <select name="sClasseGrp" size="1" >
					 <option value="0" STYLE="color:#000066;background-color:#FCE4BA"> <?php print LANGCHOIX3 ?> </option>
					 <?php
					 for($i=1;$i<count($data);$i++){
					 	if( $i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp) ){
							continue;
						}else {
							// utilisation de l'opérateur ternaire expr1?expr2:expr3;
							$libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
							print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0]."\">".$libelle."</option>\n";
						}
						$gtmp=$data[$i][4];
						$ctmp=$data[$i][0];
					 }
					 unset($gtmp);
					 unset($ctmp);
					 unset($libelle);
					 ?>
					 </select>
				<?php }else{ ?>
					 <select name="sClasseGrp" size="1" >
					 <option id='select0' ><?php print LANGCHOIX?></option>
					 <?php select_classe(); // creation des options ?>
					 </select>
				<?php } ?>
				 <br /><br />
                <!--
				 Indiquer la matière :

				<select name="sMat" size="1">
                <option value="0" STYLE="color:#000066;background-color:#FCE4BA">Choix ...</option>
               	</select>
			<br><br>
                 -->
				<br>
				 <UL><UL><UL><UL>
		 <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script>
		 <script language=JavaScript>buttonMagicRetour2("cantine.php",'_parent','Retour');</script>
				                                 <br><br>

				 </UL></UL></UL></UL></UL>
                 </form>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ):
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
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
