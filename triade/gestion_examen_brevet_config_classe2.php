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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/style.css">
<script language="JavaScript" src="./librairie_js/brevet.js"></script>
<script >var panelWidth = 40;</script>
<script language="JavaScript" src="./librairie_js/lib_aide.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<style type="text/css">
#dhtmlgoodies_leftPanel{		/* Styling the help panel */	
	background-color:#CCCCCC;	/* Blue background color */
	color:#000000;			/* White text color */
					/* You shouldn't change these 5 options unless you need to */		
	height:100%;		
	left:0px;
	z-index:10;
	position:absolute;
	display:none;
	padding:0px;
}
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

if (isset($_POST["saisie_classe"])) { $idclasse=$_POST["saisie_classe"]; }
if (isset($_GET["saisie_classe"])) { $idclasse=$_GET["saisie_classe"]; }

if (isset($_POST["donnee"])) {
	$donnee=$_POST["donnee"];
	$tab=explode(";",$donnee);
	enrConfigBrevet($tab,$_POST["idclasse"]);
	alertJs(LANGDONENR);
	$idclasse=$_POST["idclasse"];
}

?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='cadreCentral0'>
<td >
<!-- This is the content -->
<div id="dhtmlgoodies_leftPanel">
<img src="image/commun/info2.gif" align=left>
<font class=T1><b><?php print LANGAIDE1 ?></b><br><br>
</font>
</div>
<!-- End content -->
<!-- End code for the left panel -->
<form method='post' name='formulaire0' >
<input type="hidden" name="saisie_classe" value="<?php print $idclasse ?>" />
<script language=JavaScript>buttonMagicRetour2('gestion_examen_brevet_config_classe2.php?saisie_classe=<?php print $idclasse ?>','_self','<?php print LANGBT25 ?>')</script>
<script language=JavaScript>buttonMagic3("<?php print LANGAIDE ?>","initSlideLeftPanel();return false");</script>
<input type="checkbox" name='affMatiereTous' value='1' <?php if ($_POST["affMatiereTous"] == 1) print "checked=checked" ?> onclick="document.formulaire0.submit()" ><font class='T2'>Afficher toutes les matières. (Colonne de gauche)</font>
</form>

<font class=T1>
- 1* La langue LV1 doit être précisée sur la fiche élève. / 2* La langue LV2 doit être précisée sur la fiche élève. / 3* L'option doit être précisée sur la fiche élève.<br>
<br>
- Modifier le coef. de la matière en cliquant sur le titre du cadre.
</font>
<br><br>

<?php
$data=affMatiereAffectation($idclasse); //code_mat,libelle,sous_matiere
?>
<div id="dhtmlgoodies_dragDropContainer" style="width:100%">
	<div id="dhtmlgoodies_listOfItems" >
		<div>
			<p>&nbsp;Vos&nbsp;matières&nbsp;TRIADE&nbsp;&nbsp;</p>		
		<ul id="allItems">
			<?php 
			for($i=0;$i<count($data);$i++) {
				if ($_POST["affMatiereTous"] != "1") {
					if (verifMatiereBrevetLI($data[$i][0],$idclasse)) { continue; }	
				}
				if ($data[$i][2] == "0") { $data[$i][2]=""; }
				print "<li id='".$data[$i][0]."'>".$data[$i][1]." ".$data[$i][2]."</li>";
			}
			?>
		</ul>
		</div>
	</div>	
	<div id="dhtmlgoodies_mainContainer"  >
		<!-- ONE <UL> for each "room" -->
		<div>
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Français&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Français</p></a>
			<ul id="Français"><?php listMatiereBrevetLI("Français",$idclasse) ?></ul>
		</div>
		<div>		
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Mathématiques&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Mathématiques</p></a>
			<ul id="Mathématiques"><?php listMatiereBrevetLI("Mathématiques",$idclasse) ?></ul>
		</div>
		<div>
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Langue vivante 1&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Lv1&nbsp;<font size=1>(1*)</font></p></a>
			<ul id="Langue vivante 1"><?php listMatiereBrevetLI("Langue vivante 1",$idclasse) ?></ul>
		</div>
		<div>
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=SVT&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>SVT</p></a>
			<ul id="SVT"><?php listMatiereBrevetLI("SVT",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Sciences Physiques&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Sciences&nbsp;Physiques</p></a>
			<ul id="Sciences Physiques"><?php listMatiereBrevetLI("Sciences Physiques",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Education Socioculturelle&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Education&nbsp;socioculturelle</p></a>
			<ul id="Education Socioculturelle"><?php listMatiereBrevetLI("Education Socioculturelle",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Prevention Sante Environnement&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Prévention Santé Env.</p></a>
			<ul id="Prevention Sante Environnement"><?php listMatiereBrevetLI("Prevention Sante Environnement",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Sciences Biologiques&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Sciences&nbsp;Biologiques</p></a>
			<ul id="Sciences Biologiques"><?php listMatiereBrevetLI("Sciences Biologiques",$idclasse) ?></ul>
		</div>

		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Physique - Chimie&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Physique&nbsp;-&nbsp;Chimie&nbsp;</p></a>
			<ul id="Physique - Chimie"><?php listMatiereBrevetLI("Physique - Chimie",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelleEducation physique et sportive=&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Educ.&nbsp;physique&nbsp;et sportive&nbsp;</p></a>
			<ul id="Education physique et sportive"><?php listMatiereBrevetLI("Education physique et sportive",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Arts plastiques&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Arts&nbsp;plastiques&nbsp;</p></a>
			<ul id="Arts plastiques"><?php listMatiereBrevetLI("Arts plastiques",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Education musicale&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Education&nbsp;musicale&nbsp;</p></a>
			<ul id="Education musicale"><?php listMatiereBrevetLI("Education musicale",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Techno Secteur Agricoles&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Techno.&nbsp;Secteur&nbsp;Agricoles</p></a>
			<ul id="Techno Secteur Agricoles"><?php listMatiereBrevetLI("Techno Secteur Agricoles",$idclasse) ?></ul>
		</div>

		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Technologique&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Technologique&nbsp;</p></a>
			<ul id="Technologique"><?php listMatiereBrevetLI("Technologique",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=langue vivante 2&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>lv2&nbsp;<font size=1>(2*)</font></p></a>
			<ul id="langue vivante 2"><?php listMatiereBrevetLI("langue vivante 2",$idclasse) ?></ul>
		</div>
		
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Education civique&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Education&nbsp;civique&nbsp;</p></a>
			<ul id="Education civique"><?php listMatiereBrevetLI("Education civique",$idclasse) ?></ul>
		</div>
	
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=histoire des arts&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>histoire&nbsp;des&nbsp;arts</p></a>
			<ul id="histoire des arts"><?php listMatiereBrevetLI("histoire des arts",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Découverte professionnelle 6h&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Découverte&nbsp;professionnelle&nbsp;6h&nbsp;</p></a>
			<ul id="Découverte professionnelle 6h"><?php listMatiereBrevetLI("Découverte professionnelle 6h",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Latin ou grec ou Découverte professionnelle 3h (option facultative)&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Latin,&nbsp;grec,&nbsp;Découverte&nbsp;prof.&nbsp;3h&nbsp;(option&nbsp;facultative)&nbsp;<font size=1>(3*)</font></p></a>
			<ul id="Latin ou grec ou Découverte professionnelle 3h (option facultative)"><?php listMatiereBrevetLI("Latin ou grec ou Découverte professionnelle 3h (option facultative)",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Latin ou grec ou langue vivante 2 (option facultative)&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Latin,&nbsp;grec,&nbsp;lv2&nbsp;(option&nbsp;facultative)&nbsp;<font size=1>(3*)</font></p></a>
			<ul id="Latin ou grec ou langue vivante 2 (option facultative)"><?php listMatiereBrevetLI("Latin ou grec ou langue vivante 2 (option facultative)",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Histoire - Géographie&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Histoire&nbsp;-&nbsp;Géographie&nbsp;</p></a>
			<ul id="Histoire - Géographie"><?php listMatiereBrevetLI("Histoire - Géographie",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Histoire - Géographie - Civique&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Hist.&nbsp;Géo.&nbsp;Educ.&nbsp;Civique&nbsp;</p></a>
			<ul id="Histoire - Géographie - Civique"><?php listMatiereBrevetLI("Histoire - Géographie - Civique",$idclasse) ?></ul>
		</div>
		<div>	
			<a href="#" title="Config. Coef." onclick="PopupCentrer('modifcoefbrevet.php?libelle=Prévention Santé&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Prévention&nbsp;Santé&nbsp;Env.&nbsp;</p></a>
			<ul id="Prévention Santé"><?php listMatiereBrevetLI("Prévention Santé",$idclasse) ?></ul>
		</div>
	

	</div>
</div>
<div id="footer">
<form method="post" name="formulaire" >
<input type="hidden" name="affMatiereTous" value="<?php print $_POST["affMatiereTous"] ?>" />
<input type="hidden" name="idclasse" value="<?php print $idclasse ?>" />
<input type="button" onclick="saveDragDropNodes()" value="<?php print LANGENR ?>" class=BUTTON >
</div>
<ul id="dragContent"></ul>
<div id="dragDropIndicator"><img src="./image/commun/insert.gif"></div>
<input type=text name="donnee" size="90" style="visibility:hidden" >
</form>
<!-- // fin form -->
</td></tr></table>
<?php Pgclose(); ?>
</BODY>
</HTML>
