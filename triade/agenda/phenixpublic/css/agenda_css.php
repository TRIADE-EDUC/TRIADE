<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_MemoProgress_plus.txt ?>
<?php
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  header("Content-Type: text/css");

  if (!isset($_GET) && isset($HTTP_GET_VARS)) {
    $_GET    = $HTTP_GET_VARS;
    $_SERVER = $HTTP_SERVER_VARS;
  }

  if (!empty($_GET) && isset($_GET['id'])) {
    $skin = $_GET['id'];
    $vu   = (isset($_GET['vu'])) ? $_GET['vu'] : 0;
    $IP   = (isset($_GET['IP'])) ? $_GET['IP'] : "0";
    $navigateur = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : "";
  }
  if (!isset($skin) || empty($skin)) {
    $skin = "Petrole";
  }
  include("../skins/$skin.php");
  if ($IP=="0") {
    if (isset($AppliFondEcranIndex)) $AppliFondEcran=$AppliFondEcranIndex;
    if (isset($AppliFondImageIndex)) $AppliFondImage=$AppliFondImageIndex;
    if (isset($MenuCopyrightIndex)) $MenuCopyright=$MenuCopyrightIndex;
    if (isset($AgendaProfilActifFondIndex)) $AgendaProfilActifFond=$AgendaProfilActifFondIndex;
    if (isset($AgendaProfilActifColorIndex)) $AgendaProfilActifColor=$AgendaProfilActifColorIndex;
    if (isset($bgColorIndex)) $bgColor[1]=$bgColorIndex; 
    if (isset($AgendaBordureTableauIndex)) $AgendaBordureTableau=$AgendaBordureTableauIndex;
    if (isset($AgendaTexteIndex)) $AgendaTexte=$AgendaTexteIndex; 
    if (isset($FormulaireFondInputIndex)) $FormulaireFondInput=$FormulaireFondInputIndex;
    if (isset($FormulaireBordureInputIndex)) $FormulaireBordureInput=$FormulaireBordureInputIndex; 
    if (isset($FormulaireFondBoutonIndex)) $FormulaireFondBouton=$FormulaireFondBoutonIndex; 
    if (isset($FormulaireTexteBoutonIndex)) $FormulaireTexteBouton=$FormulaireTexteBoutonIndex;
    if (isset($FormulaireBordureBoutonIndex)) $FormulaireBordureBouton=$FormulaireBordureBoutonIndex;
    if (isset($FormulaireImageBoutonIndex)) $FormulaireImageBouton=$FormulaireImageBoutonIndex;
    if (isset($FormulaireTexteInputIndex)) $FormulaireTexteInput=$FormulaireTexteInputIndex;
  } if ($IP=="1") {
    if (isset($AppliFondEcranIndex)) $AppliFondEcran=$AppliFondEcranIndex;
    if (isset($AppliFondImageIndex)) $AppliFondImage=$AppliFondImageIndex;
  }
?>
BODY {
  FONT-FAMILY: Verdana, Arial, Tahoma;
  FONT-SIZE: 10px;
  FONT-WEIGHT: normal;
  COLOR: <?php echo $AgendaTexte; ?>;
  BACKGROUND-COLOR: <?php echo $AppliFondEcran; ?>;
  BACKGROUND-IMAGE: <?php echo $AppliFondImage; ?>;
  MARGIN: 0px;
}

A, A:link, A:visited  {
  COLOR: <?php echo $AgendaLien; ?>;
  TEXT-DECORATION: none;
}
A:hover  {
  COLOR: <?php echo $AgendaLienHover; ?>;
  TEXT-DECORATION: none;
}

TABLE {
  FONT-FAMILY: Verdana, Arial, Tahoma;
  FONT-SIZE: 10px;
  COLOR: <?php echo $AgendaTexte; ?>;
  BORDER-COLLAPSE: collapse;
}

FORM {
  PADDING: 0px;
  MARGIN: 0px;
}

/*STYLE DU MENU*/
TABLE.menu {
  BORDER-COLLAPSE: <?php echo (isset($MenuBorderCollapse) && !empty($MenuBorderCollapse)) ? $MenuBorderCollapse : "collapse"; ?>;
}
A.MenuOn, A.MenuOn:link, A.MenuOn:visited {
  COLOR: <?php echo $MenuOnLien; ?>;
  FONT-WEIGHT: bold;
}
A.MenuOn:hover  {
  COLOR: <?php echo $MenuOnLienHover; ?>;
}
A.MenuOff, A.MenuOff:link, A.MenuOff:visited {
  FONT-WEIGHT: normal;
  COLOR: <?php echo $MenuOffLien; ?>;
}
A.MenuOff:hover  {
  COLOR: <?php echo $MenuOffLienHover; ?>;
}
TD.MenuOn {
  COLOR: <?php echo $MenuOnLien; ?>;
  FONT-SIZE: 11px;
  BACKGROUND-COLOR: <?php echo $MenuOnFond; ?>;
}
TD.MenuOff {
  COLOR: <?php echo $MenuOffLien; ?>;
  FONT-SIZE: 11px;
  BACKGROUND-COLOR: <?php echo $MenuFond; ?>;
}
A.MenuLienAppli, A.MenuLienAppli:link, A.MenuLienAppli:visited {
  COLOR: <?php echo $MenuLienAppli; ?>;
  FONT-WEIGHT: normal;
  FONT-SIZE: 12px;
}
A.MenuLienAppli:hover  {
  COLOR: <?php echo $MenuLienAppliHover; ?>;
}
A.MenuLienForum, A.MenuLienForum:link, A.MenuLienForum:visited {
  COLOR: <?php echo $MenuLienForum; ?>;
  FONT-WEIGHT: normal;
}
A.MenuLienForum:hover  {
  COLOR: <?php echo $MenuLienForumHover; ?>;
}
A.Aujourdhui, A.Aujourdhui:link, A.Aujourdhui:visited {
  FONT-SIZE: 11px;
  COLOR: <?php echo $AujourdhuiLien; ?>;
}
A.Aujourdhui:hover {
  COLOR: <?php echo $AujourdhuiLienHover; ?>;
}
A.Copyright {
  FONT-FAMILY: Arial;
  FONT-SIZE: 9px;
  COLOR: <?php echo $MenuCopyright; ?>;
}
.PageTitre {
  FONT-SIZE: 16px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo $PageTitre; ?>;
}
TD.PageDate {
  FONT-SIZE: 11px;
  COLOR: <?php echo $PageDate; ?>;
}
/*FIN STYLE DU MENU*/
/*STYLE DU CALENDRIER ET SOUS MENU*/
A.sousMenu, A.sousMenu:link, A.sousMenu:visited {
  COLOR: <?php echo $CalNavigationLien; ?>;
}
A.sousMenu:hover  {
  COLOR: <?php echo $CalNavigationLienHover; ?>;
}
TD.sousMenu {
  BACKGROUND-COLOR: <?php echo $CalNavigationFond; ?>;
  COLOR: <?php echo $CalNavigationTexte; ?>;
  FONT-WEIGHT: bold;
  FONT-SIZE: 11px;
  TEXT-ALIGN: center;
}
A.ProfilMenuActif, A.ProfilMenuActif:link, A.ProfilMenuActif:visited {
  COLOR: <?php echo $AgendaProfilActifLien; ?>;
}
A.ProfilMenuActif:hover  {
  COLOR: <?php echo $AgendaProfilActifLienHover; ?>;
}
TD.ProfilMenuActif {
  BACKGROUND-COLOR: <?php echo $AgendaProfilActifFond; ?>;
  COLOR: <?php echo $AgendaProfilActifColor; ?>;
  FONT-WEIGHT: bold;
  FONT-SIZE: 11px;
  TEXT-ALIGN: center;
}
A.ProfilMenuInactif, A.ProfilMenuInactif:link, A.ProfilMenuInactif:visited {
  COLOR: <?php echo $AgendaProfilInactifLien; ?>;
}
A.ProfilMenuInactif:hover  {
  COLOR: <?php echo $AgendaProfilInactifLienHover; ?>;
}
TD.ProfilMenuInactif {
  BACKGROUND-COLOR: <?php echo $AgendaProfilInactifFond; ?>;
  COLOR: <?php echo $AgendaProfilInactifColor; ?>;
  FONT-SIZE: 11px;
  FONT-WEIGHT: normal;
}
A.MemoFavorisTitre, A.MemoFavorisTitre:link, A.MemoFavorisTitre:visited {
  COLOR: <?php echo $CalMemoFavorisTitre; ?>;
}
A.MemoFavorisTitre:hover  {
  COLOR: <?php echo $CalMemoFavorisTitreHover; ?>;
}
TD.legende {
  BACKGROUND-COLOR: <?php echo $AgendaLegendeActionFond; ?>;
  COLOR: <?php echo $AgendaLegendeActionTexte; ?>;
  FONT-SIZE: 10px;
  TEXT-ALIGN: center;
}
TD.legendeBis {
  COLOR: <?php echo $AgendaLegende; ?>;
}
TD.bordLegende {
  BORDER: solid 1px <?php echo $AgendaBordLegende; ?>;
}
A.jMoisCrt, A.jMoisCrt:link, A.jMoisCrt.visited {
  COLOR: <?php echo $CalJour; ?>;
}
A.jMoisCrt:hover {
  COLOR: <?php echo $CalJourHover; ?>;
}
A.jMoisCrtWE, A.jMoisCrtWE:link, A.jMoisCrtWE.visited {
  COLOR: <?php echo $CalJourWE; ?>;
}
A.jMoisCrtWE:hover {
  COLOR: <?php echo $CalJourWEHover; ?>;
}
A.jMoisPrec, A.jMoisPrec:link, A.jMoisPrec.visited {
  COLOR: <?php echo $CalJourMoisPrec; ?>;
  FONT-STYLE: italic;
  FONT-SIZE: 8px;
}
A.jMoisPrec:hover {
  COLOR: <?php echo $CalJourMoisPrecHover; ?>;
  FONT-STYLE: italic;
  FONT-SIZE: 8px;
}
A.jMoisPrecWE, A.jMoisPrecWE:link, A.jMoisPrecWE.visited {
  COLOR: <?php echo $CalJourMoisPrecWE; ?>;
  FONT-STYLE: italic;
  FONT-SIZE: 8px;
}
A.jMoisPrecWE:hover {
  COLOR: <?php echo $CalJourMoisPrecWEHover; ?>;
  FONT-STYLE: italic;
  FONT-SIZE: 8px;
}
INPUT.CalTexte  {
  FONT-FAMILY: Verdana, Arial, Helvetica;
  FONT-SIZE: 9px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo $CalMoisNavigationTexte; ?>;
  BACKGROUND-COLOR: <?php echo $CalMoisNavigationFond; ?>;
  BORDER: <?php echo $CalMoisNavigationFond; ?> solid 0px;
  TEXT-ALIGN: center;
}
A.calFlecheAnnee, A.calFlecheAnnee:link, A.calFlecheAnnee:visited {
  FONT-FAMILY: Verdana, Arial, Helvetica;
  FONT-SIZE: 11px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo $CalMoisNavigationTexte; ?>;
  PADDING-LEFT: 2px;
  PADDING-RIGHT: 2px;
}
A.calFlecheAnnee:hover {
  COLOR: <?php echo $CalMoisNavigationTexteHover; ?>;
}
A.calAllerDate, A.calAllerDate:link, A.calAllerDate:visited {
  FONT-FAMILY: Verdana, Arial, Helvetica;
  FONT-SIZE: 9px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo $CalMoisNavigationTexte; ?>;
  TEXT-DECORATION: overline underline;
  PADDING-LEFT: 1px;
  PADDING-RIGHT: 1px;
}
A.calAllerDate:hover {
  COLOR: <?php echo $CalMoisNavigationTexteHover; ?>;
}
A.btnQuitter, A.btnQuitter:link, A.btnQuitter:visited {
  COLOR: <?php echo $CalQuitterLien; ?>;
}
A.btnQuitter:hover  {
  COLOR: <?php echo $CalQuitterLienHover; ?>;
}
TD.enteteTableau {
  BACKGROUND-COLOR: <?php echo $AgendaFondEnteteTableau; ?>;
  COLOR: <?php echo $AgendaTexteEnteteTableau; ?>;
  FONT-WEIGHT: bold;
  FONT-SIZE: 11px;
  TEXT-ALIGN: center;
}
TD.CalFondDebutSemaine {
  BORDER-WIDTH: 1px 0px 1px 1px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondMilieuSemaine {
  BORDER-WIDTH: 1px 0px 1px 0px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondFinSemaine {
  BORDER-WIDTH: 1px 1px 1px 0px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>; 
}
TD.CalFondJour {
  BORDER-WIDTH: 1px 1px 1px 1px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondDebutMoisDimanche {
  BORDER-WIDTH: 1px 1px 0px 1px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondDebutmois {
  BORDER-WIDTH: 1px 0px 0px 1px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondFinMoisLundi {
  BORDER-WIDTH: 0px 1px 1px 1px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondFinMois {
  BORDER-WIDTH: 0px 1px 1px 0px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondFinPremiereLigneMois {
  BORDER-WIDTH: 1px 1px 0px 0px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondHautMois {
  BORDER-WIDTH: 1px 0px 0px 0px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondDebutDernierLigneMois {
  BORDER-WIDTH: 0px 0px 1px 1px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondFinMois {
  BORDER-WIDTH: 0px 1px 1px 0px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}  
TD.CalFondDebutMois {
  BORDER-WIDTH: 1px 0px 0px 1px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondBasMois {
  BORDER-WIDTH: 0px 0px 1px 0px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondDebutLigneMois {
  BORDER-WIDTH: 0px 0px 0px 1px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>;
}
TD.CalFondFinLigneMois {
  BORDER-WIDTH: 0px 1px 0px 0px;
  BORDER-STYLE: solid;
  BORDER-COLOR: <?php echo $CalFondBordures; ?>; 
}
.CalTitreSemaines {
  COLOR: <?php echo $CalTitreSemainesColor; ?>;
}
A.MemoFavorisTexte, A.MemoFavorisTexte:link, A.MemoFavorisTexte:visited {
  COLOR: <?php echo $CalMemoFavorisTexte; ?>;
}
A.MemoFavorisTexte:hover  {
  COLOR: <?php echo $CalMemoFavorisTexteHover; ?>;
}
.CalFavorisGroupe {
  COLOR: <?php echo $CalFavorisGroupeColor; ?>;
}
A.AgendaTitreJours, A.AgendaTitreJours:link, A.AgendaTitreJours:visited {
  COLOR: <?php echo $AgendaTitreJoursColor; ?>;
}
A.AgendaTitreJours:hover  {
  COLOR: <?php echo $AgendaTitreJoursColorHover; ?>;
}
A.AgendaFleche, A.AgendaFleche:link, A.AgendaFleche:visited {
  COLOR: <?php echo $AgendaFlecheColor; ?>;
}
A.AgendaFleche:hover  {
  COLOR: <?php echo $AgendaFlecheColorHover; ?>;
}
/*FIN STYLE DU CALENDRIER ET SOUS MENU*/
/*STYLE DES TABLEAUX DE FORMULAIRE*/
TD.tabIntitule {
  FONT-WEIGHT: bold;
  PADDING-LEFT: 2px;
  PADDING-RIGHT: 2px;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.tabInput {
  PADDING-LEFT: 2px;
  PADDING-TOP: 1px;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordT {
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordL {
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordR {
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordB {
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordTL {
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordTR {
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordTB {
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordTLR {
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordLRB {
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordTLRB {
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
/* STYLE MOD MemoProgress*/
TD.bordLB {
	BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
	BORDER-LEFT: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordTLB {
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordTRB {
	BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
	BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
	BORDER-RIGHT: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
/*FIN STYLE MOD MemoProgress*/
<?php if ($vu==20) { ?>
TD.bordBas {
  PADDING-LEFT: 2px;
  PADDING-TOP: 1px;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.bordRBas {
  FONT-WEIGHT: bold;
  PADDING-LEFT: 2px;
  PADDING-RIGHT: 2px;
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
<?php } ?>
INPUT.Texte, TEXTAREA, SELECT<?php echo (ereg("MSIE", $navigateur)) ? ", .Saisie" : ""; ?> {
  FONT-FAMILY: Verdana, Arial, Helvetica;
  FONT-SIZE: 10px;
  BACKGROUND-COLOR: <?php echo $FormulaireFondInput; ?>;
  COLOR: <?php echo $FormulaireTexteInput; ?>;
  BORDER: <?php echo $FormulaireBordureInput; ?>;
}
INPUT.Texte:hover, TEXTAREA:hover, SELECT:hover<?php echo (ereg("MSIE", $navigateur)) ? ", .SaisieHover" : ""; ?> {
  FONT-FAMILY: Verdana, Arial, Helvetica;
  FONT-SIZE: 10px;
  BACKGROUND-COLOR: <?php echo (isset($FormulaireFondInputHover) && !empty($FormulaireFondInputHover)) ? $FormulaireFondInputHover : $FormulaireFondInput; ?>;
  COLOR: <?php echo (isset($FormulaireTexteInputHover) && !empty($FormulaireTexteInputHover)) ? $FormulaireTexteInputHover : $FormulaireTexteInput; ?>;
  BORDER: <?php echo (isset($FormulaireBordureInputHover) && !empty($FormulaireBordureInputHover)) ? $FormulaireBordureInputHover : $FormulaireBordureInput; ?>;
}
INPUT.Case  {
  WIDTH: 14px;
  HEIGHT: 14px;
  VERTICAL-ALIGN: <?php echo (ereg("MSIE", $navigateur)) ? "baseline" : "middle"; ?>; /* IE ou Firefox */
}
INPUT.Bouton  {
  FONT-FAMILY: Verdana, Arial, Helvetica;
  FONT-SIZE: 10px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo $FormulaireTexteBouton; ?>;
  BORDER: <?php echo $FormulaireBordureBouton; ?>;
  BACKGROUND-COLOR: <?php echo $FormulaireFondBouton; ?>;
  BACKGROUND-IMAGE: <?php echo $FormulaireImageBouton; ?>;
  CURSOR: pointer;
}
INPUT.Bouton:hover<?php echo (ereg("MSIE", $navigateur)) ? ", INPUT.BoutonHover" : ""; ?> {
  FONT-FAMILY: Verdana, Arial, Helvetica;
  FONT-SIZE: 10px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo (isset($FormulaireTexteBoutonHover) && !empty($FormulaireTexteBoutonHover)) ? $FormulaireTexteBoutonHover : $FormulaireTexteBouton; ?>;
  BORDER: <?php echo (isset($FormulaireBordureBoutonHover) && !empty($FormulaireBordureBoutonHover)) ? $FormulaireBordureBoutonHover : $FormulaireBordureBouton; ?>;
  BACKGROUND-COLOR: <?php echo (isset($FormulaireFondBoutonHover) && !empty($FormulaireFondBoutonHover)) ? $FormulaireFondBoutonHover : $FormulaireFondBouton; ?>;
  BACKGROUND-IMAGE: <?php echo (isset($FormulaireImageBoutonHover) && !empty($FormulaireImageBoutonHover)) ? $FormulaireImageBoutonHover : $FormulaireImageBouton; ?>;
  CURSOR: pointer;
}
INPUT.PickList  {
  FONT-FAMILY: Verdana, Arial, Helvetica;
  FONT-SIZE: 9px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo $FormulaireTexteBouton; ?>;
  BORDER: <?php echo $FormulaireBordureBouton; ?>;
  BACKGROUND-COLOR: <?php echo $FormulaireFondBouton; ?>;
  BACKGROUND-IMAGE: <?php echo $FormulaireImageBouton; ?>;
  WIDTH: 24px;
  CURSOR: pointer;
}
INPUT.PickList:hover<?php echo (ereg("MSIE", $navigateur)) ? ", INPUT.PickListHover" : ""; ?> {
  FONT-FAMILY: Verdana, Arial, Helvetica;
  FONT-SIZE: 9px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo (isset($FormulaireTexteBoutonHover) && !empty($FormulaireTexteBoutonHover)) ? $FormulaireTexteBoutonHover : $FormulaireTexteBouton; ?>;
  BORDER: <?php echo (isset($FormulaireBordureBoutonHover) && !empty($FormulaireBordureBoutonHover)) ? $FormulaireBordureBoutonHover : $FormulaireBordureBouton; ?>;
  BACKGROUND-COLOR: <?php echo (isset($FormulaireFondBoutonHover) && !empty($FormulaireFondBoutonHover)) ? $FormulaireFondBoutonHover : $FormulaireFondBouton; ?>;
  BACKGROUND-IMAGE: <?php echo (isset($FormulaireImageBoutonHover) && !empty($FormulaireImageBoutonHover)) ? $FormulaireImageBoutonHover : $FormulaireImageBouton; ?>;
  WIDTH: 24px;
  CURSOR: pointer;
}
/*FIN STYLE DES TABLEAUX DE FORMULAIRE*/
<?php if ($vu==1) { ?>
/*STYLE PLANNING HEBDOMADAIRE*/
TD.dayWeek {
  FONT-WEIGHT: bold;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalTitreFond; ?>;
  TEXT-ALIGN: center;
}
TD.dayWeekCrt {
  FONT-WEIGHT: bold;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalJourSelection; ?>;
  TEXT-ALIGN: center;
}
TD.dayFerie {
  FONT-WEIGHT: bold;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalJourFerie; ?>;
  TEXT-ALIGN: center;
}
TD.dayEvenement  {
  FONT-WEIGHT: bold;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalJourEvenement; ?>;
  TEXT-ALIGN: center;
}
TD.heure {
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
  FONT-SIZE: 11px;
}
TD.minute {
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
  FONT-SIZE: 9px;
}
/*FIN STYLE PLANNING HEBDOMADAIRE*/
<?php
 } elseif ($vu==2 || $vu==4 || $vu==5 || $vu==6) {
?>
/*STYLE PLANNING MENSUEL ET GLOBAUX*/
TR.PopUpGbl  {
  COLOR: <?php echo $AgendaTextePopup; ?>;
  VERTICAL-ALIGN: top; 
}
TD.jourPlanningGbl  {
  BACKGROUND-COLOR: <?php echo $PlanningJour; ?>;
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.DjourPlanningGbl  {
  BACKGROUND-COLOR: <?php echo $PlanningJour; ?>;
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.FjourPlanningGbl  {
  BACKGROUND-COLOR: <?php echo $PlanningJour; ?>;
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-TOP: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.jourPlanning  {
  BACKGROUND-COLOR: <?php echo $PlanningJour; ?>;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.nomUtil {
  BACKGROUND-COLOR: <?php echo $PlanningMois; ?>;
  FONT-WEIGHT: bold;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.numWeek {
  FONT-WEIGHT: bold;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalTitreFond; ?>;
  PADDING: 2px;
  TEXT-ALIGN: center;
}
TD.numWeekCrt {
  FONT-WEIGHT: bold;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalJourSelection; ?>;
  PADDING: 2px;
  TEXT-ALIGN: center;
}
TD.mensNote {
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $bgColor[0]; ?>;
}
TD.mensJour {
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalJourSelection; ?>;
}
TD.mensFerie {
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalJourFerie; ?>;
}
TD.mensEvenement  {
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalJourEvenement; ?>;
}
TD.mensPrec {
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  BACKGROUND-COLOR: <?php echo $CalFond; ?>;
}
/*FIN STYLE PLANNING MENSUEL ET GLOBAUX*/
<?php } elseif ($vu==0) { ?>
/*STYLE PLANNING QUOTIDIEN*/
TD.borderTop {
  COLOR: <?php echo $AgendaTexte; ?>;
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureNote; ?>;
  BORDER-TOP: solid 1px <?php echo $AgendaBordureNote; ?>;
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureNote; ?>;
  TEXT-ALIGN: center;
}
TD.borderMiddle {
  COLOR: <?php echo $AgendaTexte; ?>;
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureNote; ?>;
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureNote; ?>;
  TEXT-ALIGN: center;
}
TD.borderBottom {
  COLOR: <?php echo $AgendaTexte; ?>;
  BORDER-LEFT: solid 1px <?php echo $AgendaBordureNote; ?>;
  BORDER-BOTTOM: solid 1px <?php echo $AgendaBordureNote; ?>;
  BORDER-RIGHT: solid 1px <?php echo $AgendaBordureNote; ?>;
  TEXT-ALIGN: center;
}
TD.borderAll {
  COLOR: <?php echo $AgendaTexte; ?>;
  BORDER: solid 1px <?php echo $AgendaBordureNote; ?>;
  TEXT-ALIGN: center;
}
TD.borderNone {
  COLOR: <?php echo $AgendaTexte; ?>;
  TEXT-ALIGN: center;
}
TD.borderNote {
  COLOR: <?php echo $AgendaTexte; ?>;
  BORDER: dashed 1px <?php echo $AgendaBordureNote; ?>;
  PADDING: 2px;
}
TD.borderNotePerso {
  COLOR: <?php echo $AgendaTexte; ?>;
  BORDER: solid 1px <?php echo $AgendaBordureNote; ?>;
  PADDING: 2px;
}
/*FIN STYLE PLANNING QUOTIDIEN*/
<?php } elseif ($vu==3 || $vu==8 || $vu==9) { ?>
/*STYLE CASE PLANNING ANNUEL ET DISPONIBILITES*/
TD.jourPlanning  {
  BACKGROUND-COLOR: <?php echo $PlanningJour; ?>;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
TD.nomUtil {
  HEIGHT: 14px;
  BACKGROUND-COLOR: <?php echo $PlanningMois; ?>;
  FONT-WEIGHT: bold;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
.note {
  BACKGROUND-COLOR: <?php echo $PlanningNotePrivee; ?>;
  CURSOR: pointer;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
.partiel {
  BACKGROUND-COLOR: <?php echo $PlanningPartiel; ?>;
  CURSOR: pointer;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
.libre {
  BACKGROUND-COLOR: <?php echo $PlanningLibre; ?>;
  CURSOR: pointer;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
.libreCons {
  BACKGROUND-COLOR: <?php echo $PlanningLibre; ?>;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
.invalide {
  BACKGROUND-COLOR: <?php echo $AgendaBordureTableau; ?>;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
  COLOR: <?php echo $PlanningInvalideTexte; ?>;
}
/*FIN STYLE CASE PLANNING ANNUEL ET DISPONIBILITES*/
<?php } //elseif ($zlVu==11) { ?>
/*STYLE DES LETTRES DE L'ALPHABET DU CALEPIN*/
A.alphabet,A.alphabet:link,A.alphabet:visited {
  FONT-SIZE: 10px;
}
A.alphabet:hover {
  FONT-SIZE: 13px;
}
/*FIN STYLE CASE PLANNING ANNUEL ET DISPONIBILITES*/
<?php //} ?>
/*STYLE INFO BULLE*/
TD.ibHeure {
  FONT-WEIGHT: bold;
  COLOR: <?php echo $AgendaTexteTitrePopup; ?>;
  BACKGROUND-COLOR: <?php echo $AgendaFondTitrePopup; ?>;
  PADDING: 1px;
}
TD.ibTitre {
  COLOR: <?php echo $AgendaTexteTitrePopup; ?>;
  BACKGROUND-COLOR: <?php echo $AgendaFondTitrePopup; ?>;
  PADDING: 1px;
}
TD.ibTexte {
  COLOR: <?php echo $AgendaTextePopup; ?>;
  BACKGROUND-COLOR: <?php echo $AgendaFondPopup; ?>;
  PADDING: 2px;
}
P.infoDate {
  FONT-SIZE: 9px;
  COLOR: <?php echo $AgendaDateCreationNote; ?>;
  TEXT-ALIGN: right;
}
DIV.infoDate {
  FONT-SIZE: 9px;
  COLOR: <?php echo $AgendaDateCreationNote; ?>;
  TEXT-ALIGN: right;
}
TABLE.infoBulle {
  BORDER-COLLAPSE: separate;
  BACKGROUND-COLOR: <?php echo $AgendaPopupBordure; ?>;
}
/*FIN STYLE INFO BULLE*/
/*STYLE MESSAGES INFORMATIONS*/
.erreur  {
  FONT-FAMILY: Arial;
  FONT-SIZE: 12px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo $MessageErreurTexte; ?>;
  BACKGROUND-COLOR: <?php echo $MessageErreurFond; ?>;
}
.confirm  {
  FONT-FAMILY: Arial;
  FONT-SIZE: 12px;
  FONT-WEIGHT: bold;
  COLOR: <?php echo $MessageConfirmeTexte; ?>;
  BACKGROUND-COLOR: <?php echo $MessageConfirmeFond; ?>;
}
.rouge  {
  COLOR: <?php echo $MessageErreurFond; ?>;
  TEXT-ALIGN: center;
}
.vert  {
  COLOR: <?php echo $MessageConfirmeFond; ?>;
  TEXT-ALIGN: center;
}
/*FIN STYLE MESSAGES INFORMATIONS*/

.displayBlock {
  DISPLAY: block;
}
.displayNone {
  DISPLAY: none;
}
.paddingDG3 TD {
  PADDING-LEFT: 3px;
  PADDING-RIGHT: 3px;
}
.generation {
  BACKGROUND-COLOR: #CCCCCC;
  HEIGHT: 15px;
  FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif
}
DIV.timezone {
  COLOR: <?php echo $AgendaTexteInfoTimezone; ?>;
  FONT-SIZE: 9px;
  WIDTH: 99%;
  TEXT-ALIGN: right;
}
.code {
	BACKGROUND-COLOR: <?php echo $bgColor[0]; ?>;
  BORDER: solid 1px <?php echo $AgendaBordureTableau; ?>;
}
A.about, A.about:link, A.about:visited {
	COLOR:#336699;
  TEXT-DECORATION:none;
  FONT-WEIGHT:bold;
}
A.about:hover {
	COLOR:#1F3D59;
  TEXT-DECORATION:none;
  FONT-WEIGHT:bold;
}
TD.about {
	COLOR:<?php echo $AgendaTextePopup; ?>;
  FONT-SIZE:11px;
  FONT-FAMILY:Verdana, Tahoma, Arial, sans-serif;
}
