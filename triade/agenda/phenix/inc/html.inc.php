<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_fcke_aff_outils.txt ?>
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

// ----------------------------------------------------------------------------
function nlTObr($str = "", $extra = "") {
  return str_replace("  ","&nbsp;&nbsp;",str_replace(chr(13),"",str_replace(chr(10),$extra."<BR>",$str)));
}


// ----------------------------------------------------------------------------
function entete_page() {
  global $APPLI_STYLE, $RELOAD_PLANNING, $sid, $tcMenu, $tcType, $sd, $APPLI_LANGUE, $PageIndex;
  echo ("<!--
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
 -->
<!DOCTYPE html public \"-//w3c//dtd html 4.0 transitional//en\">
<HTML>
<HEAD>
  <META http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
  <META http-equiv=\"Cache-Control\" content=\"no-cache\">
  <META name=\"Author\" content=\"Stephane TEIL (phenix-agenda@laposte.net)\">
  <META name=\"robots\" content=\"noindex\">
  <NOSCRIPT><META http-equiv=\"refresh\" content=\"0; url=inc/noscript.php?lg=$APPLI_LANGUE\"></NOSCRIPT>\n");
  if ($tcMenu<_MENU_PLG_ANNUEL && !$tcType && $RELOAD_PLANNING && $sid)
    echo "  <META http-equiv=\"REFRESH\" content=\"${RELOAD_PLANNING}; url=agenda.php?sid=$sid&tcMenu=$tcMenu&sd=$sd\">\n";
  echo ("  <TITLE>Phenix</TITLE>
  <LINK rel=\"stylesheet\" type=\"text/css\" href=\"css/agenda_css.php?id=".$APPLI_STYLE."&vu=".$tcMenu."&IP=".$PageIndex."\">\n");
}


// ----------------------------------------------------------------------------
function message($msg) {
  echo "    <TD align=\"center\" valign=\"middle\" width=\"100%\" nowrap>";
  switch ($msg) {
    case 1  : echo erreur(trad("MSG_UTIL_INCONNU")); break;
    case 2  : echo erreur(trad("MSG_SESSION_EXPIRE")); break;
    case 3  : echo erreur(trad("MSG_LOGIN_EXISTANT")); break;
    case 4  : echo erreur(trad("MSG_PASSWORD_ERRONE")); break;
    case 5  : echo confirme(trad("MSG_IDENTIFICATION")); break;
    case 6  : echo erreur(trad("MSG_ERREUR_BDD")); break;
    case 7  : echo confirme(trad("MSG_PROFIL_MODIFE")); break;
    case 8  : echo confirme(trad("MSG_NOTE_CREEE")); break;
    case 9  : echo confirme(trad("MSG_NOTE_MAJ")); break;
    case 10 : echo confirme(trad("MSG_NOTE_SUPPRIMEE")); break;
    case 11 : echo confirme(trad("MSG_OCCURENCE_SUPPRIMEE")); break;
    case 12 : echo confirme(trad("MSG_ANNIV_CREEE")); break;
    case 13 : echo confirme(trad("MSG_ANNIV_MAJ")); break;
    case 14 : echo confirme(trad("MSG_ANNIV_SUPPRIMEE")); break;
    case 15 : echo confirme(trad("MSG_COMPTE_CREE")); break;
    case 16 : echo erreur(trad("MSG_ERREUR_ENREGISTREMENT")); break;
    case 17 : echo erreur(trad("MSG_CODE_INVALIDE")); break;
    case 18 : echo confirme(trad("MSG_EVT_CREE")); break;
    case 19 : echo confirme(trad("MSG_EVT_MAJ")); break;
    case 20 : echo confirme(trad("MSG_EVT_SUPPRIME")); break;
    default : echo "&nbsp;";
  }
  echo "</TD>\n";
}


// ----------------------------------------------------------------------------
function confirme($msg = "") {
  return "<A class=\"confirm\">&nbsp;&nbsp;".$msg."&nbsp;&nbsp;</A>";
}


// ----------------------------------------------------------------------------
function erreur($msg = "") {
  return "<A class=\"erreur\">&nbsp;&nbsp;".$msg."&nbsp;&nbsp;</A>";
}


// ----------------------------------------------------------------------------
function genereTextArea($name,$value,$width,$height) {
  global $AUTORISE_HTML,$AUTORISE_FCKE,$FCKE_TOOLBAR,$FCKE_BROWSE,$FCKE_UPLOAD,$FCKE_BASE,$FCKE_SKIN,$APPLI_LANGUE;
  // Mod fcke_aff_toolbar  
  global $FCKE_AFF_TOOLBAR;
  // Mod fcke_aff_toolbar  
  if ($AUTORISE_HTML && $AUTORISE_FCKE) {
    // Editor Start
    switch ($FCKE_TOOLBAR) {
      case  "Full" : $height+=5; break;
      case  "Extend" : $height+=3; break;
      case  "Intermed" : $height+=2; break;
      case  "Basic" : $height++; break;
    }
    // Si le skin n'existe pas, on prend celui par defaut
    if ($FCKE_SKIN!="") {
      if (!is_dir("FCKeditor/editor/skins/".$FCKE_SKIN)) {
        $FCKE_SKIN = "default";
      }
    } else {
      $FCKE_SKIN = "default";
    }

    echo ("<SCRIPT type=\"text/javascript\" src=\"FCKeditor/fckeditor.js\"></SCRIPT>
  <DIV id=\"_Textarea\" style=\"display:none\">
    <INPUT type=\"button\" class=\"Bouton\" value=\"Retour en HTML\" onclick=\"javascript: ToggleFck()\" /><BR />
    <TEXTAREA id=\"".$name."\" name=\"".$name."\".cols=\"52\" rows=\"".$height."\" wrap=\"soft\" style=\"width:".$width."px;\"></TEXTAREA>
  </DIV>
  <DIV id=\"_FCKeditor\">
    <TEXTAREA id=\"".$name."1\" name=\"".$name."1\" cols=\"52\" rows=\"".$height."\" wrap=\"soft\" style=\"width:".$width."px;\">".htmlspecialchars(stripslashes($value))."</TEXTAREA>
  </DIV>
  <SCRIPT type=\"text/javascript\">
    var BasePath = document.location.pathname.substring( 0, document.location.pathname.lastIndexOf( '/' ) + 1) ;
    var oFCKeditor = new FCKeditor('".$name."1');
    oFCKeditor.BasePath = 'FCKeditor/';
    oFCKeditor.ToolbarSet = '".$FCKE_TOOLBAR."';
    oFCKeditor.Height = '".($height*20)."';
    oFCKeditor.Width = '".$width."';
    oFCKeditor.Config['AutoDetectLanguage'] = false;
    oFCKeditor.Config['DefaultLanguage'] = '".$APPLI_LANGUE."';
    oFCKeditor.Config['UserFilesPath'] = '".$FCKE_BASE."';
    ".(($FCKE_SKIN!="") ? "oFCKeditor.Config['SkinPath'] = BasePath + oFCKeditor.BasePath + 'editor/skins/".$FCKE_SKIN."/';" : "").";
    var BaseSkin = ".(($FCKE_SKIN!="") ? "'?Skin=' + BasePath + oFCKeditor.BasePath + 'editor/skins/".$FCKE_SKIN."/&';" : "?").";
    oFCKeditor.Config['ImageBrowser'] = ".(($FCKE_BROWSE) ? 1 : 0).";
    ".(($FCKE_BROWSE) ? "oFCKeditor.Config['ImageBrowserURL'] = BasePath + oFCKeditor.BasePath + 'editor/filemanager/browser.html' + BaseSkin + 'Lang=' + BasePath + oFCKeditor.BasePath + 'editor/lang/".$APPLI_LANGUE."&Type=Image&Connector=connector.php';" : "").";
    oFCKeditor.Config['ImageUpload'] = ".(($FCKE_UPLOAD) ? 1 : 0).";
    oFCKeditor.Config['LinkBrowser'] = ".(($FCKE_BROWSE) ? 1 : 0).";
    ".(($FCKE_BROWSE) ? "oFCKeditor.Config['LinkBrowserURL'] = BasePath + oFCKeditor.BasePath + 'editor/filemanager/browser.html' + BaseSkin + 'Lang=' + BasePath + oFCKeditor.BasePath + 'editor/lang/".$APPLI_LANGUE."&Connector=connector.php';" : "").";
    oFCKeditor.Config['LinkUpload'] = ".(($FCKE_UPLOAD) ? 1 : 0).";
    oFCKeditor.Config['FlashBrowser'] = ".(($FCKE_BROWSE) ? 1 : 0).";
    ".(($FCKE_BROWSE) ? "oFCKeditor.Config['FlashBrowserURL'] = BasePath + oFCKeditor.BasePath + 'editor/filemanager/browser.html' + BaseSkin + 'Lang=' + BasePath + oFCKeditor.BasePath + 'editor/lang/".$APPLI_LANGUE."&Type=Flash&Connector=connector.php';" : "").";
    oFCKeditor.Config['FlashUpload'] = ".(($FCKE_UPLOAD) ? 1 : 0).";

    //  Mod fcke_aff_toolbar 
    oFCKeditor.Config['ToolbarStartExpanded'] = '".$FCKE_AFF_TOOLBAR."';
    //  Mod fcke_aff_toolbar
    oFCKeditor.ReplaceTextarea();

    function ToggleText() {
      var oEditor ;
      var eTextareaDiv= document.getElementById('_Textarea');
      var eFCKeditorDiv= document.getElementById('_FCKeditor');
      oEditor = FCKeditorAPI.GetInstance('".$name."1') ;
      document.getElementById('$name').value = oEditor.GetXHTML();
      eTextareaDiv.style.display = '';
      eFCKeditorDiv.style.display = 'none';
    }
    function ToggleFck() {
      var oEditor ;
      var eTextareaDiv= document.getElementById('_Textarea');
      var eFCKeditorDiv= document.getElementById('_FCKeditor');
      oEditor = FCKeditorAPI.GetInstance('".$name."1');
      oEditor.SetHTML(document.getElementById('".$name."').value);
      eTextareaDiv.style.display = 'none';
      eFCKeditorDiv.style.display = '';
      if ( oEditor && !document.all ) {
        if ( oEditor.EditMode == FCK_EDITMODE_WYSIWYG )
          oEditor.MakeEditable();
      }
    }
    function PrepareSave() {
      if ( document.getElementById('_Textarea').style.display == 'none' ) {
        var oEditor;
        var eTextareaDiv= document.getElementById('_Textarea');
        var eFCKeditorDiv= document.getElementById('_FCKeditor');
        oEditor = FCKeditorAPI.GetInstance('".$name."1');
        document.getElementById('".$name."').value = oEditor.GetXHTML();
        eTextareaDiv.style.display = '';
        eFCKeditorDiv.style.display = 'none';
      }
    }
  </SCRIPT>");
  } else {
    echo ("<TEXTAREA name=\"".$name."\" cols=\"52\" rows=\"".$height."\" wrap=\"soft\" style=\"width:".$width."px;\">".htmlspecialchars(stripslashes($value))."</TEXTAREA>
  <SCRIPT type=\"text/javascript\">
    function ToggleText() { return true; }
    function ToggleFck() { return true; }
    function PrepareSave() { return true; }
  </SCRIPT>");
  }
}


// ----------------------------------------------------------------------------
function genereListeCouleur($avecToutesNotes=true, $avecCouleurDefaut=true, $avecBouton=true) {
  global $AgendaFondNotePerso, $FILTRE_COULEUR, $FormulaireFondInput;
  //Recuperation des couleurs/categories de notes
  $tabTemp    = array(trad("COMMUN_TOUTES_NOTES") => "ALL", trad("COMMUN_COUL_DEFAUT") => $AgendaFondNotePerso);
  $tabCouleur = array_merge($tabTemp,getListeCouleur());

  //Construction de la liste des couleurs/categories de notes
  reset($tabCouleur);
  $liste = "<SELECT name=\"zlFiltreCouleur\" style=\"background-color:".(($FILTRE_COULEUR!="ALL") ? $FILTRE_COULEUR : $FormulaireFondInput).";\" onchange=\"javascript: changeCouleurListe(this,false);\">\n";
  while (list($key, $val) = each($tabCouleur)) {
    $selected = ($val==$FILTRE_COULEUR) ? " selected" : "";
    $liste .= "        <OPTION value=\"".$val."\" style=\"background-color:".(($val!="ALL") ? $val : $FormulaireFondInput).";\"".$selected.">".$key."</OPTION>\n";
  }
  $liste .= "      </SELECT>\n";
  if ($avecBouton) {
    $liste .= "      <INPUT type=\"submit\" class=\"bouton\" value=\"".trad("COMMUN_FILTRER")."\" name=\"btFiltreAffiche\">";
  }
  echo $liste;
}


// ----------------------------------------------------------------------------
/* Traduction d'un libelle */
function trad($libelle) {
  global $LG;
  if ($LG[$libelle]!="") return $LG[$libelle];
  else return "undefined";
}


// ----------------------------------------------------------------------------
// FORMATAGE DES HEURES POUR L'AFFICHAGE
// ----------------------------------------------------------------------------
function afficheHeure($heure,$minute,$format="H:i") {
  return date($format,mktime($heure,($minute*60)%60,0,1,1,2000));
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// AFFICHE LE CONTENU D'UN TEXTE DANS UNE POPUP JS PLUTOT QU'UN TITLE HTML
// TITRE DU POPUP FACULTATIF
// ----------------------------------------------------------------------------
  function infoPopup($text, $titre="") {
    return " onmouseover=\"javascript: atc('".addslashes($titre)."','".addslashes($text)."'); return false;\" onmouseout=\"javascript: nd(); return true;\"";
  }
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// INFOS DETAILLEES ET ACCES DIRECT A LA FICHE DU CONTACT ASSOCIE
// SELON LES DROITS SUR LE CONTACT
// ----------------------------------------------------------------------------
  function getInfoContactAssocie($enr,$droit_NOTES) {
    global $idUser, $MODIF_PARTAGE;
    $infoContact = "";
    // Infos detaillees et acces direct a la fiche du contact associe selon les droits sur le contact
    if (!empty($enr['nomContact']) && ($enr['cal_util_id']==$idUser || $enr['cal_partage']=='O')) {
      $infoContact = "<B>".$enr['nomContact']."</B><BR>";  // Nom
      if (!empty($enr['cal_societe']))   // Societe
        $infoContact .= "<I>".$enr['cal_societe']."</I><BR>";
      if (!empty($enr['cal_adresse']))   // Adresse
        $infoContact .= str_replace(chr(13),"",str_replace(chr(10),"<BR>",$enr['cal_adresse']))."<BR><BR>";
      if (!empty($enr['cal_cp']) || !empty($enr['cal_ville']))  // Code postal et Ville
        $infoContact .= trim($enr['cal_cp']." ".$enr['cal_ville'])."<BR>";
      if (!empty($enr['cal_pays']))   // Pays
        $infoContact .= $enr['cal_pays']."<BR><BR>";
      if (!empty($enr['cal_domicile']))   // Telephone domicile
        $infoContact .= "<IMG src='image/calepin/telephone.gif' border=0 width=18 height=14 vspace=1 title='Domicile' align='absmiddle'> ".telephoneVF($enr['cal_domicile'])."<BR>";
      if (!empty($enr['cal_travail']))   // Telephone professionnel
        $infoContact .= "<IMG src='image/calepin/telephone2.gif' border=0 width=18 height=14 vspace=1 title='Travail' align='absmiddle'> ".telephoneVF($enr['cal_travail'])."<BR>";
      if (!empty($enr['cal_portable']))  // Portable
        $infoContact .= "<IMG src='image/calepin/portable.gif' border=0 width=18 height=16 vspace=1 title='Portable' align='absmiddle'> ".telephoneVF($enr['cal_portable'])."<BR>";
      if (!empty($enr['cal_fax']))  // FAX
        $infoContact .= "<IMG src='image/calepin/fax.gif' border=0 width=16 height=15 vspace=1 hspace=1 title='Fax' align='absmiddle'> ".telephoneVF($enr['cal_fax'])."<BR>";
      if (!empty($enr['cal_email']))  // Email
        $infoContact .= "<IMG src='image/calepin/email.gif' border=0 width=16 height=15 vspace=1 hspace=1 title='Email' align='absmiddle'> ".$enr['cal_email']."<BR>";
      if (!empty($enr['cal_emailpro']))  // Email Pro
        $infoContact .= "<IMG src='image/calepin/email.gif' border=0 width=16 height=15 vspace=1 hspace=1 title='Email Pro' align='absmiddle'> ".$enr['cal_emailpro']."<BR>";
      // Suppression des retours chariots eventuels en fin d'adresse
      while (substr($infoContact,-4)=="<BR>") {
        $infoContact = substr($infoContact,0,strlen($infoContact)-4);
      }
      // Acces aux informations detaillees du contact
      if ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) {
        $infoContact = "&nbsp;<A href=\"javascript: affContact('".$enr['cal_id']."');\"><IMG src=\"image/contact.gif\" width=\"10\" height=\"11\" border=\"0\" align=\"absmiddle\"".infoPopup($infoContact,trad("FCT_CONTACT_ASSOCIE"))."></A>";
      } else {
        $infoContact = "&nbsp;<IMG src=\"image/contact.gif\" width=\"10\" height=\"11\" border=\"0\" align=\"absmiddle\"".infoPopup($infoContact,trad("FCT_CONTACT_ASSOCIE")).">";
      }
    }
    return $infoContact;
  }
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// AFFICHAGE DES INFORMATIONS DE CREATION / MODIFICATION D'UNE NOTE
// idUtil EST L'IDENTIFIANT DE L'UTILISATEUR DONT ON CONSULTE LE PLANNING
// ----------------------------------------------------------------------------
function afficheInfoModifNote(&$enr, $idUtil) {
  //Info sur la creation de la note
  $enr['age_detail'] .= "<P class=\"infoDate\">".sprintf(trad("COMMUN_CREATION_DATE"), $enr['dateCreation']).(($enr['age_createur_id']!=$idUtil) ? " ".sprintf(trad("COMMUN_CREATION_PAR"), $enr['nomCreateur']) : "");
  //Info sur la modification de la note
  if ($enr['dateModif']!=$enr['dateCreation']) {
    $enr['age_detail'] .= "<BR>".sprintf(trad("COMMUN_DATE_DERNIERE_MODIF"), $enr['dateModif']).(($enr['age_modificateur_id']!=$idUtil) ? " ".sprintf(trad("COMMUN_MODIFICATION_PAR"), $enr['nomModificateur']) : "");
  }
  $enr['age_detail'] .= "</P>";
}
// ----------------------------------------------------------------------------
?>
