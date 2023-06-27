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
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); 
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menupersonnel") {
	$cnx=cnx();
	if (!verifDroit($_SESSION["id_pers"],"ficheeleve")) {
		accesNonReserveFen();
		exit();
	}
	Pgclose();
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<?php
// affichage de l'élève (lecture seule)
$eid=$_GET["eid"];
if($eid)
// $eid provient(entre autres) de la page recherche_eleve.php
{
$sql=<<<EOF

SELECT
	elev_id,
	nom,
	prenom,
	c.libelle,
	lv1,
	lv2,
	`option`,
	regime,
	date_naissance,
	lieu_naissance,
	nationalite,
	passwd,
	passwd_eleve,
	adr_eleve,
	commune_eleve,
	ccp_eleve,
	tel_fixe_eleve,
	boursier,
	montant_bourse,
	indemnite_stage,
	emailpro_eleve,
	rangement,
	cdi,
	bde,
	situation_familiale,
	civ_1,
	nomtuteur,
	prenomtuteur,	
	adr1,
	code_post_adr1,
	commune_adr1,
	tel_port_1,
	civ_2,
	nom_resp_2,
	prenom_resp_2,
	adr2,
	code_post_adr2,
	commune_adr2,
	tel_port_2,
	telephone,
	profession_pere,
	tel_prof_pere,
	profession_mere,
	tel_prof_mere,
	nom_etablissement,
	numero_etablissement,
	code_postal_etablissement,
	commune_etablissement,
	numero_eleve,
	email,
	email_eleve,
	class_ant,
	annee_ant,
	tel_eleve,
	email_resp_2,
	sexe,
	code_compta,
	information,
	classe
FROM
	${prefixe}eleves, ${prefixe}classes c
WHERE
	elev_id='$eid'
AND	c.code_class=classe

EOF;
$res=execSql($sql);
$data=chargeMat($res);
$idClasse=$data[0][58];
?>
<table border="1" cellpadding="3" cellspacing="1" width="100%"  height="85" style="border-collapse: collapse;" >
<tr id='coulBar0' ><td height="2" colspan=2><b><font   id='menumodule1' ><?php print LANGRECHE1?></B></font></td></tr>
<?php
if( count($data) <= 0 ) {
	print("<tr><td align=center valign=center>".LANGEDIT1."</td></tr>");
} else { //debut else
?>
<tr>
<td align="center">
	<a href="#" onclick="open('photoajouteleve.php?ideleve=<?php print $eid?>','photo','width=450,height=280')"><img src="image_trombi.php?idE=<?php print $eid ?>" border=0 ></a><br />[ <a href="#" class="bouton2"  onclick="open('photoajouteleve.php?ideleve=<?php print $eid?>','photo','width=450,height=280')" >modifier</a> ]
</td>
<td valign='top'>
<table border="0" cellpadding="0" cellspacing="5" align="center">
<tr><td align="center" height='10'><br><input type=button value="<?php print LANGBT52?>" onclick="open('modif_eleve.php?eid=<?php print $data[0][0]?>','_parent','')"  class="bouton2"><br><br></td>
</tr>
<?php if ((FINANCIERVATEL == "oui") && ($_SESSION["membre"] == "menuadmin")) { ?>
<!--/****** APRES_MAJ_TRIADE_AUTO - [APRES_MAJ_TRIADE_AUTO_DATE_TRAITEMENT] - [APRES_MAJ_TRIADE_AUTO_ENTITE] : CODE AJOUTE AUTOMATIQUEMENT PAR SCRIPT 'admin_apres_maj_triade' ****** -->
    	<tr>
        	<td align="center" height='10'>
		 <input type=button onClick="open('module_financier/rib_editer.php?elev_id=<?php print $eid;?>','pass','width=550,height=400')" value='<?php print "Editer le RIB" ?>' class="bouton2" ><br><br>
        	</td>
        </tr>

    	<tr>
        	<td align="center" height='10'>
                <table border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse: collapse;" >
                	<?php
					$sql_insc ="SELECT i.inscription_id, i.annee_scolaire, c.code_class, c.libelle ";
					$sql_insc.="FROM " . PREFIXE . "fin_inscriptions i INNER JOIN " . PREFIXE . "classes c ON i.code_class = c.code_class ";
					$sql_insc.="WHERE i.elev_id = " . $eid . " ";
					$sql_insc.="ORDER BY i.annee_scolaire ASC, c.libelle ASC";
					//echo $sql_insc;
					$res_insc=execSql($sql_insc);
					

					?>
                
                	<?php
					if($res_insc->numRows() > 0) {
					?>
                    <tr>
                        <td align='center'>
                        	<select name="inscription_id_insc" id="inscription_id_insc">
                            <?php
							for($i=0; $i<$res_insc->numRows(); $i++) {
								$ligne_insc = &$res_insc->fetchRow();
								$selected = '';
								if($i == 0) {
									$selected = 'selected';
								}
							?>
                            	<option value="<?php echo $ligne_insc[0]; ?>" <?php echo $selected; ?>><?php echo $ligne_insc[1]; ?> - <?php echo $ligne_insc[3]; ?></option>
                            <?php
							}
							?>
                            </select>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td >
							<input type=button onClick="aller_a_inscription_editer()" value='<?php print LANGFIN002 ?>' class="bouton2" ><br><br>
                		</td>
                   </tr>
                	<?php
					} else {
					?>
                    <tr>
                        <td >
							<input type=button onClick="eleve_pas_inscrit()" value='<?php print LANGFIN003 ?>' class="bouton2" ><br><br>
                		</td>
                   </tr>
                	<?php
					}
					?>
                   
            	</table>
        	</td>
        </tr>
			
		<script language="javascript">
			function aller_a_inscription_editer() {
				document.for_inscription_editer.inscription_id.value = document.getElementById('inscription_id_insc').options[document.getElementById('inscription_id_insc').selectedIndex].value;
				//alert(document.getElementById('inscription_id_insc').options[document.getElementById('inscription_id_insc').selectedIndex].value);
				document.for_inscription_editer.submit();
			}
			function eleve_pas_inscrit() {
				alert("<?php echo LANGFIN004; ?>");
			}
		</script>
        <form name="for_inscription_editer" id="for_inscription_editer" method="post" action="module_financier/inscription_editer.php">
        	<input type="hidden" name="inscription_id" id="inscription_id" value="0">
         	<input type="hidden" name="elev_id" id="elev_id" value="<?php echo $eid; ?>">
        	<input type="hidden" name="appelant" id="appelant" value="edit_eleve">
       </form>

    	<tr>
        	<td align="center">
                <input type=button value="<?php print "Réservation de chambre" ?>" onClick="open('module_chambres/reservation_liste.php?eleve_id_forcer=<?php print $eid;?>&batiment_id_forcer=0&chambre_id_forcer=0&date_debut_forcer=null&date_fin_forcer=null','_parent','')"  class="bouton2"  ><br><br>
        	</td>
        </tr>   
	<!--***************************************************************************-->

	

<?php } ?>
<?php
if (isset($_GET["val"])) { inactifEleve($_GET["eid"],$_GET["val"]); }
$inactif=getInactifEleve($data[0][0]);

if ($inactif == "1") {
	$bouton="Débloquer ce compte";
	$inactifval="0";
	$img="<font id='color2'><img src='image/commun/warning2.gif' align='center' /><b>COMPTE BLOQUE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>";
}else{
	$bouton="Bloquer ce compte";
	$inactifval="1";
}

print $img;

if ($_SESSION["membre"] == "menuadmin") { ?>
	<tr><td align="center"><input type=button value="<?php print $bouton ?>" onclick="open('edit_eleve.php?eid=<?php print $data[0][0]?>&val=<?php print $inactifval?>','_parent','')"  class="bouton2"  ><br><br></td></tr>
<?php } ?>
	<tr>
        <td align="center">
		<input type=button value="<?php print "Retour Fiche de l'".INTITULEELEVE ?>" onClick="open('ficheeleve3.php?eid=<?php print $eid ?>&idclasse=<?php print $idClasse ?>','_parent','')"  class="button"  ><br><br>
        	</td>
        </tr>   
</table>
</td></tr>
<?php
	$nom_cellule=array( id, LANGELE2, LANGELE3, LANGELE4, "Lv1/Spé", "Lv2/Spé", LANGELE5, LANGELE6, LANGELE10, LANGEDIT6, LANGELE11, LANGIMP51,LANGIMP52, "adresse élève","commune élève","code postal élève","téléphone fixe élève","Boursier","Montant de la bourse","Indemnité de stage","Email Universitaire","N° Rangement / Info","Inscription à la bibliothèque","Inscription au BDE" , "Situation Familiale", LANGEDIT7, LANGEDIT8, LANGEL12,  LANGEL14, LANGEL15, LANGEL16, LANGEDIT2,  LANGEDIT3, LANGEDIT4, LANGEDIT5, LANGEL18, LANGEL19, LANGEL20, LANGEDIT9, LANGEL21, LANGEL22, LANGEL23, LANGEL24, LANGEL25, LANGEL26, LANGEL27, LANGEL28, LANGEL29, LANGEL30,  LANGELE244. LANGEDIT10, LANGEDIT11,LANGbasededoni41, LANGbasededoni42,LANGEDIT12,LANGEDIT13,"sexe","Code comptabilité","Information");
		
for($i=1;$i<count($data[0]);$i++)
{//debut for
		if ($i == 58) continue;
		if(preg_match('/[a-zA-Z0-9äâîïûüèé]{1,}/',trim($data[0][$i]))) {
			if($i==8) {$data[0][$i]=dateForm($data[0][$i]);}
			if($i==1) {$data[0][$i]=strtoupper($data[0][$i]);}
			if($i==2) {$data[0][$i]=ucwords($data[0][$i]);}
			if($i==11) {$data[0][$i]="xxxxxxxxx";}
			if($i==12) {$data[0][$i]="xxxxxxxxx";}
			if(($i==25) && (trim($data[0][$i]) != "")) { $data[0][$i]=civ($data[0][$i]); }
			if(($i==32) && (trim($data[0][$i]) != "")) { $data[0][$i]=civ($data[0][$i]); }
			if ($data[0][$i] == "") { $data[0][$i]="&nbsp;"; }
			if($nom_cellule[$i]== "Information") { $data[0][$i]=nl2br($data[0][$i]); }
			if ($i==17) { if ($data[0][$i]==1) { $data[0][$i]=LANGOUI; }else{ $data[0][$i]=LANGNON; } }
			if ($i==18) {$data[0][$i]=affichageFormatMonnaie($data[0][$i])." ".unitemonnaie();  }
			if ($i==19) {$data[0][$i]=affichageFormatMonnaie($data[0][$i])." ".unitemonnaie(); }
			if ($i==22) { if ($data[0][$i]==1) { $data[0][$i]=LANGOUI; }else{ $data[0][$i]=LANGNON; } }
			if ($i==23) { if ($data[0][$i]==1) { $data[0][$i]=LANGOUI; }else{ $data[0][$i]=LANGNON; } }

?>
		<tr><td bgcolor="#FFFFFF" width=40% align=right><B><?php print $nom_cellule[$i]?> :</B> </td>
		    <td bgcolor="#FFFFFF"><?php print $data[0][$i]?></td></tr>
		<?php
		}
		/*
		else {
		?>
		<tr><td bgcolor="#FFFFFF" width=40% align=right><B><?php print $nom_cellule[$i]?> :</B> </td>
		<td bgcolor="#FFFFFF"><font color="red"><?php print LANGERROR2?></font></td></tr>
		<?php
		} */
		}//fin for
    }//fin else
print "</table>";
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
