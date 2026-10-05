<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Aide.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_RSS_Reader.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5.5_Emplacement_Plus.txt ?>
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
?>

<!-- MODULE MENU DE L'AGENDA -->
<?php
  if (!isset($ListeMenuFond) || empty($ListeMenuFond)) {
    $ListeMenuFond = $ListeChoixFond."; BACKGROUND-IMAGE:url(./image/menu/fond_menu.gif); BACKGROUND-REPEAT:repeat-y";
  }
?>
  <FORM name="ToolBar">
  <TABLE class="menu" cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR align="center">
    <TD width="17%" height="22" class="<?php echo ($tcMenu<_MENU_DISP_HEBDO && !$tcType) ? "MenuOn" : "MenuOff"; ?>" nowrap>
      <DIV style="z-index:100;position:relative; width:100%;">
        <DIV id="sel_planning" style="top:17px;left:-1px;min-width:100%;position:absolute;background:<?php echo $ListeMenuFond; ?>;border: 1px solid <?php echo $AgendaBordureTableau; ?>;display:none;" onmouseover="javascript:clearTimeout(window.div_timer);" onmouseout="javascript:retardeLancement('cacheListe()',100);">
          <TABLE border="0" width="100%" cellspacing="0" cellpadding="0" style="padding-right:5px;">
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_PLG_QUOT; ?>');" style="cursor:pointer;<?php echo (($tcPlg==_MENU_PLG_QUOT) ? " font-weight:bold;\" class=\"bordB\" bgcolor=\"".$ListeChoixSelection."\"" : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';\""); ?>><IMG src="./image/menu/ico_quot.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_PLG_QUOTIDIEN"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_PLG_HEBDO; ?>');" style="cursor:pointer;<?php echo (($tcPlg==_MENU_PLG_HEBDO) ? " font-weight:bold;\" class=\"bordTB\" bgcolor=\"".$ListeChoixSelection."\"" : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';\""); ?>><IMG src="./image/menu/ico_hebdo.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_PLG_HEBDOMADAIRE"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_PLG_MENSUEL; ?>');" style="cursor:pointer;<?php echo (($tcPlg==_MENU_PLG_MENSUEL) ? " font-weight:bold;\" class=\"bordTB\" bgcolor=\"".$ListeChoixSelection."\"" : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';\""); ?>><IMG src="./image/menu/ico_mensuel.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_PLG_MENSUEL"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_PLG_ANNUEL; ?>');" style="cursor:pointer;<?php echo (($tcPlg==_MENU_PLG_ANNUEL) ? " font-weight:bold;\" class=\"bordT".(($droit_AGENDAS >= _DROIT_AGENDA_PARTAGE) ? "B" : "")."\" bgcolor=\"".$ListeChoixSelection."\"" : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';\""); ?>><IMG src="./image/menu/ico_annuel.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_PLG_ANNUEL"); ?></TD></TR>
<?php
  if ($droit_AGENDAS >= _DROIT_AGENDA_PARTAGE) {
?>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_PLG_QUOT_GBL; ?>');" style="cursor:pointer;<?php echo (($tcPlg==_MENU_PLG_QUOT_GBL) ? " font-weight:bold;\" class=\"bordTB\" bgcolor=\"".$ListeChoixSelection."\"" : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';\""); ?>><IMG src="./image/menu/ico_quot_g.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_PLG_QUOTGLOB"); ?>&nbsp;</TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_PLG_HEBDO_GBL; ?>');" style="cursor:pointer;<?php echo (($tcPlg==_MENU_PLG_HEBDO_GBL) ? " font-weight:bold;\" class=\"bordTB\" bgcolor=\"".$ListeChoixSelection."\"" : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';\""); ?>><IMG src="./image/menu/ico_hebdo_g.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_PLG_HEBDGLOB"); ?>&nbsp;</TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_PLG_MENS_GBL; ?>');" style="cursor:pointer;<?php echo (($tcPlg==_MENU_PLG_MENS_GBL) ? " font-weight:bold;\" class=\"bordT\" bgcolor=\"".$ListeChoixSelection."\"" : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';\""); ?>><IMG src="./image/menu/ico_mensuel_g.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_PLG_MENSGLOB"); ?>&nbsp;</TD></TR>
<?php
  }
?>
          </TABLE>
        </DIV>
<?php
  switch ($tcPlg) {
    case _MENU_PLG_HEBDO : $titrePlanning = trad("MENU_PLG_HEBDOMADAIRE"); $titreIco = "./image/menu/ico_hebdo.gif"; break;
    case _MENU_PLG_MENSUEL : $titrePlanning = trad("MENU_PLG_MENSUEL"); $titreIco = "./image/menu/ico_mensuel.gif"; break;
    case _MENU_PLG_ANNUEL : $titrePlanning = trad("MENU_PLG_ANNUEL"); $titreIco = "./image/menu/ico_annuel.gif"; break;
    case _MENU_PLG_MENS_GBL : $titrePlanning = trad("MENU_PLG_MENSGLOB"); $titreIco = "./image/menu/ico_mensuel_g.gif"; break;
    case _MENU_PLG_HEBDO_GBL : $titrePlanning = trad("MENU_PLG_HEBDGLOB"); $titreIco = "./image/menu/ico_hebdo_g.gif"; break;
    case _MENU_PLG_QUOT_GBL : $titrePlanning = trad("MENU_PLG_QUOTGLOB"); $titreIco = "./image/menu/ico_quot_g.gif"; break;
    default : $titrePlanning = trad("MENU_PLG_QUOTIDIEN"); $titreIco = "./image/menu/ico_quot.gif"; break;
  }
?>
<?php
  if ($MENU_ONCLICK) {
?>
        &nbsp;<A href="javascript: retardeLancement('showListe(\'sel_planning\',\'\')',10);" class="<?php echo ($tcMenu<_MENU_DISP_HEBDO && !$tcType) ? "MenuOn" : "MenuOff"; ?>" onmouseout="javascript:retardeLancement('cacheListe()',100);"><SPAN style="font-weight: normal;"><IMG src="<?php echo $titreIco;?>" align="absmiddle" border="0"><?php echo trad("MENU_PLANNING")."&nbsp;</SPAN>".$titrePlanning; ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
  } else {
?>
        &nbsp;<A href="javascript: nvlVu('<?php echo ($tcMenu<_MENU_DISP_HEBDO) ? $tcMenu : $tcPlg; ?>');" class="<?php echo ($tcMenu<_MENU_DISP_HEBDO && !$tcType) ? "MenuOn" : "MenuOff"; ?>" onmouseover="javascript:retardeLancement('showListe(\'sel_planning\',\'\')',300);" onmouseout="javascript:retardeLancement('cacheListe()',100);"><SPAN style="font-weight: normal;"><IMG src="<?php echo $titreIco;?>" align="absmiddle" border="0"><?php echo trad("MENU_PLANNING")."&nbsp;</SPAN>".$titrePlanning; ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
  }
?>
      </DIV>
    </TD>
<?php
  if ((($USER_SUBSTITUE==$idUser) and ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) or ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION)) {
?>
    <TD width="17%" class="<?php echo ($tcType && $tcMenu!=_MENU_CONTACT) ? "MenuOn" : "MenuOff"; ?>" nowrap>
      <DIV style="z-index:100;position:relative; width:100%;">
        <DIV id="sel_ajouter" style="top:17px;left:-1px;min-width:100%;position:absolute;background:<?php echo $ListeMenuFond; ?>;border: 1px solid <?php echo $AgendaBordureTableau; ?>;display:none;" onmouseover="javascript:clearTimeout(window.div_timer);" onmouseout="javascript:retardeLancement('cacheListe()',100);">
          <TABLE border="0" width="100%" cellspacing="0" cellpadding="0" style="padding-right:5px;">
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_NOTE; ?>');" style="cursor:pointer;<?php echo (($tcType==_TYPE_NOTE || (!$tcType && $tcMenu!=_MENU_CONTACT)) ? " font-weight:bold;\" class=\"bordB\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_note.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_AJT_NOTE"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_ANNIV; ?>');" style="cursor:pointer;<?php echo (($tcType==_TYPE_ANNIV) ? " font-weight:bold;\" class=\"bordTB\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_anniversaire.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_AJT_ANNIV"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_EVENEMENT; ?>');" style="cursor:pointer;<?php echo (($tcType==_TYPE_EVENEMENT) ? " font-weight:bold;\" class=\"bordTB\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_evenement.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_AJT_EVENEMENT"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_MEMO; ?>');" style="cursor:pointer;<?php echo (($tcType==_TYPE_MEMO) ? " font-weight:bold;\" class=\"bordTB\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_memo.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_AJT_MEMO"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_LIBELLE; ?>');" style="cursor:pointer;<?php echo (($tcType==_TYPE_LIBELLE) ? " font-weight:bold;\" class=\"bordTB\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_libelle.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_AJT_LIBELLE"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_FAVORIS; ?>');" style="cursor:pointer;<?php echo (($tcType==_TYPE_FAVORIS) ? " font-weight:bold;\" class=\"bordT\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_favoris.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_AJT_FAVORI"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_RSS_READER; ?>');" style="cursor:pointer;<?php echo (($tcType==_TYPE_RSS_READER) ? " font-weight:bold;\" class=\"bordT\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_rss_reader.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_AJT_RSS_READER"); ?></TD></TR>			
            <!-- Mod Emplacement Plus -->
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_EMPL; ?>');" style="cursor:pointer;<?php echo (($tcType==_TYPE_EMPL) ? " font-weight:bold;\" class=\"bordT\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><img src="./image/menu/ico_empl.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_AJT_EMPL"); ?></TD></TR>
            <!-- Fin Mod Emplacement Plus -->
          </TABLE>
        </DIV>
<?php
  switch ($tcType) {
    case _TYPE_ANNIV :
      $titreAjout = "<SPAN style=\"font-weight: normal;\">".trad("MENU_GESTION")."&nbsp;</SPAN>".trad("MENU_AJT_ANNIV");
      $lienNouveau = _TYPE_ANNIV;
	    $titreAjoutIco = "./image/menu/ico_anniversaire.gif";
      break;
    case _TYPE_EVENEMENT :
      $titreAjout = "<SPAN style=\"font-weight: normal;\">".trad("MENU_GESTION")."&nbsp;</SPAN>".trad("MENU_AJT_EVENEMENT");
      $lienNouveau = _TYPE_EVENEMENT;
	    $titreAjoutIco = "./image/menu/ico_evenement.gif";
      break;
    case _TYPE_MEMO :
      $titreAjout = "<SPAN style=\"font-weight: normal;\">".trad("MENU_GESTION")."&nbsp;</SPAN>".trad("MENU_AJT_MEMO");
      $lienNouveau = _TYPE_MEMO;
	    $titreAjoutIco = "./image/menu/ico_memo.gif";
      break;
    case _TYPE_LIBELLE :
      $titreAjout = "<SPAN style=\"font-weight: normal;\">".trad("MENU_GESTION")."&nbsp;</SPAN>".trad("MENU_AJT_LIBELLE");
      $lienNouveau = _TYPE_LIBELLE;
	    $titreAjoutIco = "./image/menu/ico_libelle.gif";
      break;
    case _TYPE_FAVORIS :
      $titreAjout = "<SPAN style=\"font-weight: normal;\">".trad("MENU_GESTION")."&nbsp;</SPAN>".trad("MENU_AJT_FAVORI");
      $lienNouveau = _TYPE_FAVORIS;
	    $titreAjoutIco = "./image/menu/ico_favoris.gif";
      break;
   // MOD RSS Reader
    case _TYPE_RSS_READER :
      $titreAjout = "<SPAN style=\"font-weight: normal;\">".trad("MENU_GESTION")."&nbsp;</SPAN>".trad("MENU_AJT_RSS_READER");
      $lienNouveau = _TYPE_RSS_READER;
	    $titreAjoutIco = "./image/menu/ico_rss_reader.gif";
      break;
    // Fin mod   	
    //Mod Emplacement Plus
    case _TYPE_EMPL :
      $titreAjout = "<SPAN style=\"font-weight: normal;\">".trad("MENU_GESTION")."&nbsp;</SPAN>".trad("MENU_AJT_EMPL");
      $lienNouveau = _TYPE_EMPL;
      $titreAjoutIco = "./image/menu/ico_empl.gif";
      break;
    //Fin Mod Emplacement Plus
    default :
        $titreAjout = "<SPAN style=\"font-weight: normal;\">".trad("MENU_GESTION")."&nbsp;</SPAN>".trad("MENU_AJT_NOTE");
        $lienNouveau = _TYPE_NOTE;
		    $titreAjoutIco = "./image/menu/ico_note.gif";
      break;
  }
?>
<?php
    if ($MENU_ONCLICK) {
?>
        <A href="javascript: retardeLancement('showListe(\'sel_ajouter\',\'\')',10);" class="<?php echo ($tcType && $tcMenu!=_MENU_CONTACT) ? "MenuOn" : "MenuOff"; ?>" onmouseout="javascript:retardeLancement('cacheListe()',100);"><IMG src="<?php echo $titreAjoutIco;?>" align="absmiddle" border="0"><?php echo $titreAjout; ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
    } else {
?>
        <A href="javascript: nvType('<?php echo $lienNouveau; ?>');" class="<?php echo ($tcType && $tcMenu!=_MENU_CONTACT) ? "MenuOn" : "MenuOff"; ?>" onmouseover="javascript:retardeLancement('showListe(\'sel_ajouter\',\'\')',300);"  onmouseout="javascript:retardeLancement('cacheListe()',100);"><IMG src="<?php echo $titreAjoutIco;?>" align="absmiddle" border="0"><?php echo $titreAjout; ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
    }
?>
      </DIV>
    </TD>
<?php
  } elseif (($AFFECTE_NOTE) and ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) {
    echo "    <TD width=\"17%\" class=\"".(($tcType) ? "MenuOn" : "MenuOff")."\" nowrap><IMG src=\"./image/menu/ico_note.gif\" align=\"absmiddle\"><A href=\"javascript: nvType('"._TYPE_NOTE."');\" class=\"".(($tcType) ? "MenuOn" : "MenuOff")."\" title=\"".trad("MENU_UTIL_SELECTIONNE")."\">".trad("MENU_AJOUTER_NOTE")."</A>&nbsp;</TD>\n";
  }

  if ($droit_NOTES >= _DROIT_NOTE_CONSULT_RECHERCHE) {
?>
    <TD width="16%" class="<?php echo ($tcMenu==_MENU_RECHERCHE && !$tcType) ? "MenuOn" : "MenuOff"; ?>"><IMG src="./image/menu/ico_recherche.gif" align="absmiddle"><A href="javascript: nvlVu('<?php echo _MENU_RECHERCHE; ?>');" class="<?php echo ($tcMenu==_MENU_RECHERCHE && !$tcType) ? "MenuOn" : "MenuOff"; ?>"><?php echo trad("MENU_RECHERCHE"); ?></A></TD>
<?php
  }

  if ((($USER_SUBSTITUE==$idUser) and ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) or ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION)) {
    if ($droit_AGENDAS >= _DROIT_AGENDA_PARTAGE) {
      if ($tcMenu!=_MENU_DISP_HEBDO && $tcMenu!=_MENU_DISP_QUOT) {
        $choixDispo = $MENU_DISPO;
      } else {
        $choixDispo = $tcMenu;
      }
?>
    <TD width="17%" class="<?php echo (($tcMenu==_MENU_DISP_HEBDO || $tcMenu==_MENU_DISP_QUOT) && !$tcType) ? "MenuOn" : "MenuOff"; ?>" nowrap>
      <DIV style="z-index:100;position:relative; width:100%;">
        <DIV id="sel_dispo" style="top:17px;left:-1px;min-width:100%;position:absolute;background:<?php echo $ListeMenuFond; ?>;border: 1px solid <?php echo $AgendaBordureTableau; ?>;display:none;" onmouseover="javascript:clearTimeout(window.div_timer);" onmouseout="javascript:retardeLancement('cacheListe()',100);">
          <TABLE border="0" width="100%" cellspacing="0" cellpadding="0" style="padding-right:5px;">
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_DISP_QUOT; ?>');" style="cursor:pointer;<?php echo (($choixDispo==_MENU_DISP_QUOT) ? " font-weight:bold;\" class=\"bordB\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_dispo_quot.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_DISPO_QUOT"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_DISP_HEBDO; ?>');" style="cursor:pointer;<?php echo (($choixDispo==_MENU_DISP_HEBDO) ? " font-weight:bold;\" class=\"bordT\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_dispo_hebdo.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_DISPO_HEBDO"); ?></TD></TR>
          </TABLE>
        </DIV>
<?php
      if ($MENU_ONCLICK) {
?>
        <A href="javascript: retardeLancement('showListe(\'sel_dispo\',\'\')',10);" class="<?php echo (($tcMenu==_MENU_DISP_HEBDO || $tcMenu==_MENU_DISP_QUOT) && !$tcType) ? "MenuOn" : "MenuOff"; ?>" onmouseout="javascript:retardeLancement('cacheListe()',100);"><IMG src="./image/menu/ico_dispo.gif" align="absmiddle" border="0"><?php echo trad("MENU_DISPONIBILITE"); ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
      } else {
?>
        <A href="javascript: nvlVu('<?php echo $choixDispo; ?>');" class="<?php echo (($tcMenu==_MENU_DISP_HEBDO || $tcMenu==_MENU_DISP_QUOT) && !$tcType) ? "MenuOn" : "MenuOff"; ?>" onmouseover="javascript:retardeLancement('showListe(\'sel_dispo\',\'\')',300);"  onmouseout="javascript:retardeLancement('cacheListe()',100);"><IMG src="./image/menu/ico_dispo.gif" align="absmiddle" border="0"><?php echo trad("MENU_DISPONIBILITE"); ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
      }
?>
      </DIV>
    </TD>
<?php
    }
?>
    <TD width="16%" class="<?php echo ($tcMenu==_MENU_CONTACT) ? "MenuOn" : "MenuOff"; ?>" nowrap>
      <DIV style="z-index:100;position:relative; width:100%;">
        <DIV id="sel_contact" style="top:17px;left:-1px;min-width:100%;position:absolute;background:<?php echo $ListeMenuFond; ?>;border: 1px solid <?php echo $AgendaBordureTableau; ?>;display:none;" onmouseover="javascript:clearTimeout(window.div_timer);" onmouseout="javascript:retardeLancement('cacheListe()',100);">
          <TABLE border="0" width="100%" cellspacing="0" cellpadding="0" style="padding-right:5px;">
<?php
    if ($MENU_ONCLICK) {
?>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_CONTACT; ?>');" style="cursor:pointer;<?php echo (($tcMenu==_MENU_CONTACT && $type!="Importer" && $tcType!=_TYPE_CONTACT) ? " font-weight:bold;\" class=\"bordB\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_contact.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_CONTACTS"); ?></TD></TR>
<?php
    }
?>
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_CONTACT; ?>');" style="cursor:pointer;<?php echo (($tcType==_TYPE_CONTACT) ? " font-weight:bold;\" class=\"bord".(($MENU_ONCLICK) ? "T" : "")."B\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_contact.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_AJT_CONTACT"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvType('<?php echo _TYPE_IMPORT_CONTACT; ?>');" style="cursor:pointer;<?php echo (($tcMenu==_MENU_CONTACT && $type=="Importer") ? " font-weight:bold;\" class=\"bordT\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_import_contact.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_IMPORT_CONTACT"); ?></TD></TR>
          </TABLE>
        </DIV>
<?php
    if ($MENU_ONCLICK) {
?>
        <A href="javascript: retardeLancement('showListe(\'sel_contact\',\'\')',10);" class="<?php echo ($tcMenu==_MENU_CONTACT) ? "MenuOn" : "MenuOff"; ?>" onmouseout="javascript:retardeLancement('cacheListe()',100);"><IMG src="./image/menu/ico_contact.gif" align="absmiddle" border="0"><?php echo trad("MENU_CONTACTS"); ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
    } else {
?>
        <A href="javascript: nvlVu('<?php echo _MENU_CONTACT; ?>');" class="<?php echo ($tcMenu==_MENU_CONTACT) ? "MenuOn" : "MenuOff"; ?>" onmouseover="javascript:retardeLancement('showListe(\'sel_contact\',\'\')',300);"  onmouseout="javascript:retardeLancement('cacheListe()',100);"><IMG src="./image/menu/ico_contact.gif" align="absmiddle" border="0"><?php echo trad("MENU_CONTACTS"); ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
    }
?>
      </DIV>
    </TD>


<?php
  }

  // Recherche des droits de l'utilisateur pour constituer le menu
  $menuProfil = (($USER_SUBSTITUE==$idUser and $droit_PROFILS >= _DROIT_PROFIL_PARAM_BASE) or $droit_PROFILS >= _DROIT_PROFIL_AUTRE_PARAM_BASE or $idAdmin!=0);
  $menuImport = (($USER_SUBSTITUE==$idUser and $droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) or $droit_NOTES >= _DROIT_NOTE_MODIF_CREATION);
  $menuAdmin = ($droit_ADMIN=="O");

  if ($menuProfil || $menuImport || $menuAdmin) {
?>
    <TD width="17%" class="<?php echo ((($tcMenu==_MENU_PROFIL || $tcMenu==_MENU_NOTE_IMPORT || $tcMenu==_MENU_NOTE_EXPORT || $tcMenu==_MENU_ADMIN) and !$tcType)) ? "MenuOn" : "MenuOff"; ?>" nowrap>
      <DIV style="z-index:100;position:relative; width:100%;">
        <DIV id="sel_outils" style="top:17px;right:0px;min-width:100%;position:absolute;background:<?php echo $ListeMenuFond; ?>;border: 1px solid <?php echo $AgendaBordureTableau; ?>;display:none;" onmouseover="javascript:clearTimeout(window.div_timer);" onmouseout="javascript:retardeLancement('cacheListe()',100);">
          <TABLE border="0" width="100%" cellspacing="0" cellpadding="0" style="padding-right:5px;">
<?php
    if ($menuProfil) {
?>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_PROFIL; ?>');" style="cursor:pointer;<?php echo (($tcMenu==_MENU_PROFIL) ? " font-weight:bold;\"".(($menuImport || $menuAdmin) ? " class=\"bordB\"" : "")." bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_profil.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_PROFIL"); ?></TD></TR>
<?php
    }

    if ($menuImport) {
?>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_NOTE_IMPORT; ?>');" style="cursor:pointer;<?php echo (($tcMenu==_MENU_NOTE_IMPORT) ? " font-weight:bold;\" class=\"bord".(($menuAdmin) ? "T" : "")."B\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_import_note.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_IMPORT_NOTE"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_NOTE_EXPORT; ?>');" style="cursor:pointer;<?php echo (($tcMenu==_MENU_NOTE_EXPORT) ? " font-weight:bold;\" class=\"bordT".(($menuAdmin) ? "B" : "")."\" bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_export_note.gif" border="0" align="absmiddle">&nbsp;<?php echo trad("MENU_EXPORT_NOTE"); ?></TD></TR>
<?php
    }
    if ($menuAdmin) {
?>
            <TR><TD nowrap align="left" onclick="javascript: nvlVu('<?php echo _MENU_ADMIN; ?>');" style="cursor:pointer;<?php echo (($tcMenu==_MENU_ADMIN) ? " font-weight:bold;\"".(($menuProfil || $menuImport) ? " class=\"bordT\"" : "")." bgcolor=\"".$ListeChoixSelection : "\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';"); ?>"><IMG src="./image/menu/ico_admin.gif" border="0" align="absmiddle">&nbsp;<?php
      if (!$idAdmin && !empty($ztLoginAdm) && !empty($ztPasswdMD5Adm)) {
        // Recherche d'une connexion administrateur
        $DB_CX->DbQuery("SELECT admin_id FROM ${PREFIX_TABLE}admin WHERE admin_login = '".$ztLoginAdm."' AND admin_passwd = '".$ztPasswdMD5Adm."'");
        if ($DB_CX->DbNumRows()) {
          $idAdmin = $DB_CX->DbResult(0,0);
          $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}sid SET sid_admin_id=".$idAdmin." WHERE sid_id='".$sid."'");
        }
      }
      if (!$idAdmin)
        echo trad('CALENDRIER_ADMIN');
      else
        echo "<FONT color=\"#FF0000\">".trad('CALENDRIER_ADMIN')."</FONT>";
      echo "</TD></TR>\n";
    }
?>
          </TABLE>
        </DIV>
<?php
    if ($MENU_ONCLICK) {
?>
        &nbsp;<A href="javascript: retardeLancement('showListe(\'sel_outils\',\'\')',10);" class="<?php echo ((($tcMenu==_MENU_PROFIL || $tcMenu==_MENU_NOTE_IMPORT || $tcMenu==_MENU_NOTE_EXPORT || $tcMenu==_MENU_ADMIN) and !$tcType)) ? "MenuOn" : "MenuOff"; ?>" onmouseout="javascript:retardeLancement('cacheListe()',100);"><IMG src="./image/menu/ico_outils.gif" align="absmiddle" border="0"><?php echo trad("MENU_OUTILS"); ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
    } else {
?>
        &nbsp;<A href="javascript:;" class="<?php echo ((($tcMenu==_MENU_PROFIL || $tcMenu==_MENU_NOTE_IMPORT || $tcMenu==_MENU_NOTE_EXPORT || $tcMenu==_MENU_ADMIN) and !$tcType)) ? "MenuOn" : "MenuOff"; ?>" onmouseover="javascript:retardeLancement('showListe(\'sel_outils\',\'\')',300);"  onmouseout="javascript:retardeLancement('cacheListe()',100);"><IMG src="./image/menu/ico_outils.gif" align="absmiddle" border="0"><?php echo trad("MENU_OUTILS"); ?><IMG src="<?php echo (file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/"); ?>expand_menu.gif" width="7" height="4" alt="" hspace="4" align="absmiddle" border="0"></A>
<?php
    }
?>
      </DIV>
    </TD>
<?php
  }
  // Mod Aide  
    if ($AIDE_AFF != "0") {
?>
    <TD width="100%" class="MenuOff"></TD>
    <TD width="100" class="MenuOff" nowrap>
      <DIV style="z-index:100;position:relative; width:100%;">
        <DIV id="sel_aide" style="top:17px;right:0px;min-width:100%;position:absolute;background:<?php echo $ListeMenuFond; ?>;border: 1px solid <?php echo $AgendaBordureTableau; ?>;display:none;" onmouseover="javascript:clearTimeout(window.div_timer);" onmouseout="javascript:retardeLancement('cacheListe()',100);">
          <TABLE border="0" width="100%" cellspacing="0" cellpadding="0">
<?php 
      if ($AIDE_AFF >= "3") {
?>
            <TR><TD nowrap align="left" onclick="javascript: appelhelp();" style="cursor:pointer;" onmouseover="javascript:this.style.backgroundColor='<?php echo $ListeChoixSurvol; ?>';" onmouseout="javascript:this.style.backgroundColor='';"><IMG src="./image/menu/ico_aide_gen.gif"  border="0" align="absmiddle">&nbsp;<?php echo trad("MODAIDE_GENE"); ?></TD></TR>
            <TR><TD nowrap align="left" onclick="javascript: appelhelpcontext();" style="cursor:pointer;" onmouseover="javascript:this.style.backgroundColor='<?php echo $ListeChoixSurvol; ?>';" onmouseout="javascript:this.style.backgroundColor='';"><IMG src="./image/menu/ico_aide_ctx.gif"  border="0" align="absmiddle">&nbsp;<?php echo trad("MODAIDE_CONTEXT"); ?></TD></TR>
<?php 
      if ($AIDE_AFF == "4") {
?>
            <TR><TD nowrap align="left" onclick="javascript: appelhelppdf();" style="cursor:pointer;" onmouseover="javascript:this.style.backgroundColor='<?php echo $ListeChoixSurvol; ?>';" onmouseout="javascript:this.style.backgroundColor='';"><IMG src="./image/menu/ico_aide_pdf.gif"  border="0" align="absmiddle">&nbsp;<?php echo trad("MODAIDE_PDF"); ?></TD></TR>
<?php 
        }
?>
<?php 
        }
?>
            <TR><TD nowrap align="left" onclick="javascript: affAbout('visible');" style="cursor:pointer;" onmouseover="javascript:this.style.backgroundColor='<?php echo $ListeChoixSurvol; ?>';" onmouseout="javascript:this.style.backgroundColor='';"><IMG src="./image/menu/ico_a_propos.gif"  border="0" align="absmiddle">&nbsp;<?php echo trad("MODAIDE_APP"); ?></TD></TR>
          </TABLE>
        </DIV>
<?php
  if ($MENU_ONCLICK)
  {
?>
        &nbsp;<A href="javascript: retardeLancement('showListe(\'sel_aide\',\'\')',10);" class="MenuOff" onmouseout="javascript:retardeLancement('cacheListe()',100);" title="<?php echo trad("MODAIDE_TCH"); ?>"><IMG src="image/menu/ico_aide.gif"  alt="" align="absmiddle" border="0"><?php echo trad("MODAIDE_AIDE"); ?></A><?php echo (($AIDE_AFF!="3" && $AIDE_AFF!="4") ? "" : "<IMG onclick=\"javascript:retardeLancement('showListe(\'sel_aide\',\'\')',10);\" style=\"cursor:N-resize;\" src=\"".(file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/")."expand_menu.gif\" width=\"7\" height=\"4\" alt=\"\" hspace=\"4\" align=\"absmiddle\" border=\"0\">"); ?>
<?php
  } else {
?>
        &nbsp;<A href="javascript:<?php echo (($AIDE_AFF=="1") ? "appelhelp()" : (($AIDE_AFF=="4" || $AIDE_AFF=="3" || $AIDE_AFF=="2")? "appelhelpcontext()" :"")); ?>;" class="MenuOff" onmouseover="javascript:retardeLancement('showListe(\'sel_aide\',\'\')',300);"  onmouseout="javascript:retardeLancement('cacheListe()',100);" title="<?php echo trad("MODAIDE_TCH"); ?>"><IMG src="image/menu/ico_aide.gif"  alt="" align="absmiddle" border="0"><?php echo trad("MODAIDE_AIDE").(($AIDE_AFF!="3" && $AIDE_AFF!="4") ? "" : "<IMG src=\"".(file_exists("skins/".$APPLI_STYLE."/expand_menu.gif") ? "skins/".$APPLI_STYLE."/" : "image/")."expand_menu.gif\" width=\"7\" height=\"4\" alt=\"\" hspace=\"4\" align=\"absmiddle\" border=\"0\">"); ?></A>
<?php
  }
?>
      </DIV>
    </TD>
<?php
      }
  // Mod Aide  
?>
  </TR>
  </TABLE>
  </FORM>
<!-- FIN MODULE MENU DE L'AGENDA -->
