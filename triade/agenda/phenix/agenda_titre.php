<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_RSS_Reader.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_qui_est_la.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Meteo_today_ico.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_horoscope_hebdo.txt ?>
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

  //Compatibilite du navigateur pour afficher les listes au format DHTML
  $navigateur = $_SERVER['HTTP_USER_AGENT'];
  $navigateurCompatible=!(strpos($navigateur,"MSIE 4") || strpos($navigateur,"MSIE 5") || strpos($navigateur,"MSIE 6"));
  //Fete du jour
  $DB_CX->DbQuery("SELECT fet_nom FROM ${PREFIX_TABLE}fetes WHERE fet_mois=".$moisEnCours." AND fet_jour=".intval($jourEnCours));
  $feteDuJour = $DB_CX->DbResult(0,0);
  //Titre page
  $iTitre = ($USER_SUBSTITUE!=$idUser) ? 0 : $tcType;
  switch ($iTitre) {
    case _TYPE_ANNIV : $titrePage = "&nbsp;&nbsp;".trad("TITRE_ANNIV"); break;
    case _TYPE_NOTE : $titrePage = "&nbsp;&nbsp;".trad("TITRE_NOTE"); break;
    case _TYPE_EVENEMENT : $titrePage = "&nbsp;&nbsp;".trad("TITRE_EVENEMENT"); break;
    case _TYPE_MEMO : $titrePage = "&nbsp;&nbsp;".trad("TITRE_MEMO"); break;
    case _TYPE_LIBELLE : $titrePage = "&nbsp;&nbsp;".trad("TITRE_LIBELLE"); break;
    case _TYPE_FAVORIS : $titrePage = "&nbsp;&nbsp;".trad("TITRE_FAVORIS"); break;
	// Mod RSS Reader
    case _TYPE_RSS_READER : $titrePage = "&nbsp;&nbsp;".trad("TITRE_RSS_READER"); break;	
	// Fin mod
    //Mod Emplacement Plus
    case _TYPE_EMPL : $titrePage = "&nbsp;&nbsp;".trad("TITRE_FAVORIS"); break;
    //Fin Mod Emplacement Plus
    default :
      if ($tcMenu==_MENU_PLG_MENS_GBL)
        $titrePage = "&nbsp;&nbsp;".trad("TITRE_MENSGLOB");
      elseif ($tcMenu==_MENU_PLG_HEBDO_GBL)
        $titrePage = "&nbsp;&nbsp;".trad("TITRE_HEDBGLOB");
      elseif ($tcMenu==_MENU_PLG_QUOT_GBL)
        $titrePage = "&nbsp;&nbsp;".trad("TITRE_QUOTGLOB");
      elseif ($tcMenu==_MENU_DISP_HEBDO)
        $titrePage = "&nbsp;&nbsp;".trad("TITRE_DISPO_HEBDO");
      elseif ($tcMenu==_MENU_DISP_QUOT)
        $titrePage = "&nbsp;&nbsp;".trad("TITRE_DISPO_QUOT");
      elseif ($tcMenu==_MENU_CONTACT)
        $titrePage = "&nbsp;&nbsp;".trad("TITRE_CONTACT");
      elseif ($tcMenu==_MENU_PROFIL) {
        if ($droit_PROFILS <= _DROIT_PROFIL_PARAM_PARTAGE) {
          /*Public*/$DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur  WHERE util_id=".$idUser);
        } elseif ($droit_AGENDAS < _DROIT_AGENDA_TOUS) {
          /*Users*/$DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$idUser." OR (util_partage_planning='1') OR (util_partage_planning='2' AND ppl_consultant_id=".$idUser.") ORDER BY nomUtil");
        } else  {
          /*Admin*/$DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE (LENGTH(CONCAT(util_nom, util_prenom)) > 0) ORDER BY nomUtil");
        }
        if ($DB_CX->DbNumRows()==1) {
          $genre = prefixeMot(strtolower(substr($DB_CX->DbResult(0,1),0,1)));
          $titrePage = "&nbsp;&nbsp;".sprintf(trad("TITRE_PROFIL_NOM"),$genre,$DB_CX->DbResult(0,1));
        } elseif ($navigateurCompatible) {
          // Affichage de la liste d'utilisateur au format DHTML
          $nbUtil = $DB_CX->DbNumRows();
          $hauteurDiv = ($nbUtil>20) ? "height:260px;overflow:auto;" : "";
          $titrePage = ("<DIV style=\"z-index:50;position:relative; width:100%;\">
        <DIV id=\"sel_util\" style=\"top:16px;left:0px;min-width:100%;position:absolute;background:".$ListeChoixFond.";border: 1px solid ".$AgendaBordureTableau.";display:none;".$hauteurDiv."\" onmouseover=\"javascript:clearTimeout(window.div_timer);\" onmouseout=\"javascript:retardeLancement('cacheListe()',100);\">
          <TABLE border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" class=\"paddingDG3\">\n");
          $i = 1;
          while ($enr = $DB_CX->DbNextRow()) {
            $titrePage .= ("          <TR height=\"13\"><TD nowrap align=\"left\" onclick=\"javascript: substUser('".$enr['util_id']."');\"");
            if ($enr['util_id']==$USER_SUBSTITUE) {
              if ($i>1)
                $classBord = "T";
              if ($i<$nbUtil)
                $classBord .= "B";
              $titrePage .= (" style=\"cursor:pointer;font-weight:bold;\" class=\"bord".$classBord."\" bgcolor=\"".$ListeChoixSelection."\"");
              $nomSelectionne = $enr['nomUtil'];
            } else {
              // Identification de l'utilisateur connecte
              if ($enr['util_id']==$idUser) {
                $titrePage .= (" style=\"cursor:pointer;\" bgcolor=\"".$ListeChoixDefaut."\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='".$ListeChoixDefaut."';\"");
              } else {
                $titrePage .= (" style=\"cursor:pointer;\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';\"");
              }
            }
            $titrePage .= (">".htmlspecialchars($enr['nomUtil'])."</TD></TR>\n");
            $i++;
          }
          $genre = prefixeMot(strtolower(substr($nomSelectionne,0,1)),trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE"));
          $titrePage .= ("          </TABLE>
        </DIV>
        <SPAN class=\"PageTitre\" style=\"cursor:default;\" onmouseover=\"javascript:retardeLancement('showListe(\'sel_util\',\'\')',300);\"  onmouseout=\"javascript:retardeLancement('cacheListe()',100);\">".$nomSelectionne."<IMG src=\"".((file_exists("skins/".$APPLI_STYLE."/expand_titre.gif")) ? "skins/".$APPLI_STYLE."/" : "image/")."expand_titre.gif\" width=\"7\" height=\"4\" alt=\"\" hspace=\"4\" align=\"absmiddle\" border=\"0\"></SPAN>
    </DIV>");
      $titrePage = "<TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\"><TR><TD class=\"PageTitre\" nowrap>&nbsp;&nbsp;".sprintf(trad("TITRE_PROFIL_NOM"),$genre,"</TD><TD class=\"PageTitre\" nowrap>".$titrePage)."</TD></TR></TABLE>";
        } else {
          // Affichage de la liste d'utilisateur dans un SELECT classique
          $listeUtil = "<SELECT name=\"zlSubst\" style=\"font-size:12px; font-weight:bold;\" onchange=\"javascript: substUser(this.value);\">";
          while ($enr = $DB_CX->DbNextRow()) {
            $style = ($enr['util_id']==$idUser) ? " style=\"color:".$PageNomUtilisateur.";\"" : "";
            $selected = ($enr['util_id']==$USER_SUBSTITUE) ? " selected" : "";
            $listeUtil .= "<OPTION value=\"".$enr['util_id']."\"".$selected.$style.">".htmlspecialchars($enr['nomUtil'])."</OPTION>";
          }
          $listeUtil .= "</SELECT>";
          $titrePage = "&nbsp;&nbsp;".sprintf(trad("TITRE_PROFIL_DE"),$listeUtil);
        }
      }
      elseif ($tcMenu==_MENU_NOTE_IMPORT)
        $titrePage = "&nbsp;&nbsp;".trad("TITRE_IMPORTER");
      elseif ($tcMenu==_MENU_NOTE_EXPORT)
        $titrePage = "&nbsp;&nbsp;".trad("TITRE_EXPORTER");
      elseif ($tcMenu==_MENU_ADMIN)
        $titrePage = "&nbsp;&nbsp;".sprintf(trad("TITRE_ADMIN"), $APPLI_VERSION);
      else {
        // Recherche des plannings partages auxquels l'utilisateur a acces
		// Mod qui est la ?
		$ico="";
		$ico_mail = "";
	    //sid_util_id=".$enr['util_id']."
		$DB_CX->DbQuery("SELECT DISTINCT util_id, util_email, sid_util_id FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}sid ON sid_util_id=util_id");
		while ($enr = $DB_CX->DbNextRow()) {
		if (!$enr['sid_util_id']) $ico[$enr['util_id']] = "<IMG src=\"image/user_off.gif\" BORDER=\"0\">&nbsp;&nbsp;";
		else $ico[$enr['util_id']] = "<IMG src=\"image/user_on.gif\" BORDER=\"0\">&nbsp;&nbsp;";
		if ($AFF_TITRE_MAILTO) {
		  if ($enr['util_email']) $ico_mail[$enr['util_id']] = "&nbsp;&nbsp;<A href=\"mailto:".$enr['util_email']."\" title=\"".$enr['util_email']."\"><IMG src=\"image/enveloppe_white.gif\" BORDER=\"0\" alt></A>";
		}
		}
		// Fin mod qui est là ?
        if ($droit_AGENDAS < _DROIT_AGENDA_PARTAGE) {
          /*Public*/$DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$idUser);
        } else if ($droit_AGENDAS >= _DROIT_AGENDA_TOUS) {
          /*Admin*/$DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE (LENGTH(CONCAT(util_nom, util_prenom)) > 0) ORDER BY nomUtil");
        } else {
          $DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$idUser." OR (util_partage_planning='1') OR (util_partage_planning='2' AND ppl_consultant_id=".$idUser.") ORDER BY nomUtil");
        }
        if ($DB_CX->DbNumRows()==1) {
          $genre = prefixeMot(strtolower(substr($DB_CX->DbResult(0,1),0,1)),trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE"));
          $titrePage = "&nbsp;&nbsp;".sprintf(trad("TITRE_PLANNING_NOM"),$genre,$DB_CX->DbResult(0,1));
        } elseif ($navigateurCompatible) {
          // Affichage de la liste d'utilisateur au format DHTML
          $nbUtil = $DB_CX->DbNumRows();
          $hauteurDiv = ($nbUtil>20) ? "height:260px;overflow:auto;" : "";
          $titrePage = ("<DIV style=\"z-index:50;position:relative; width:100%;\">
        <DIV id=\"sel_util\" style=\"top:16px;left:0px;min-width:100%;position:absolute;background:".$ListeChoixFond.";border: 1px solid ".$AgendaBordureTableau.";display:none;".$hauteurDiv."\" onmouseover=\"javascript:clearTimeout(window.div_timer);\" onmouseout=\"javascript:retardeLancement('cacheListe()',100);\">
          <TABLE border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" class=\"paddingDG3\">\n");
          $i = 1;
          while ($enr = $DB_CX->DbNextRow()) {
            $titrePage .= ("          <TR height=\"13\"><TD nowrap align=\"left\" onclick=\"javascript: substUser('".$enr['util_id']."');\"");
            if ($enr['util_id']==$USER_SUBSTITUE) {
              if ($i>1)
                $classBord = "T";
              if ($i<$nbUtil)
                $classBord .= "B";
              $titrePage .= (" style=\"cursor:pointer;font-weight:bold;\" class=\"bord".$classBord."\" bgcolor=\"".$ListeChoixSelection."\"");
              $nomSelectionne = $enr['nomUtil'];
            } else {
              // Identification de l'utilisateur connecte
              if ($enr['util_id']==$idUser) {
                $titrePage .= (" style=\"cursor:pointer;\" bgcolor=\"".$ListeChoixDefaut."\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='".$ListeChoixDefaut."';\"");
              } else {
                $titrePage .= (" style=\"cursor:pointer;\" onmouseover=\"javascript:this.style.backgroundColor='".$ListeChoixSurvol."';\" onmouseout=\"javascript:this.style.backgroundColor='';\"");
              }
            }
			// Mod qui est la ?
            $titrePage .= (">".$ico[$enr['util_id']]."".htmlspecialchars($enr['nomUtil'])." ".$ico_mail[$enr['util_id']]."</TD></TR>\n");
			// Fin mod qui est là ?			
            $i++;
          }
          $genre = prefixeMot(strtolower(substr($nomSelectionne,0,1)),trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE"));
          $titrePage .= ("          </TABLE>
        </DIV>
        <SPAN class=\"PageTitre\" style=\"cursor:default;\" onmouseover=\"javascript:retardeLancement('showListe(\'sel_util\',\'\')',300);\"  onmouseout=\"javascript:retardeLancement('cacheListe()',100);\">".$nomSelectionne."<IMG src=\"".((file_exists("skins/".$APPLI_STYLE."/expand_titre.gif")) ? "skins/".$APPLI_STYLE."/" : "image/")."expand_titre.gif\" width=\"7\" height=\"4\" alt=\"\" hspace=\"4\" align=\"absmiddle\" border=\"0\"></SPAN>
    </DIV>");
      $titrePage = "<TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\"><TR><TD class=\"PageTitre\" nowrap>&nbsp;&nbsp;".sprintf(trad("TITRE_PLANNING_NOM"),$genre,"</TD><TD class=\"PageTitre\" nowrap>".$titrePage)."</TD></TR></TABLE>";
        } else {
          // Affichage de la liste d'utilisateur dans un SELECT classique
          $listeUtil = "<SELECT name=\"zlSubst\" style=\"font-size:12px; font-weight:bold;\" onchange=\"javascript: substUser(this.value);\">";
          while ($enr = $DB_CX->DbNextRow()) {
            $style = ($enr['util_id']==$idUser) ? " style=\"color:".$PageNomUtilisateur.";\"" : "";
            $selected = ($enr['util_id']==$USER_SUBSTITUE) ? " selected" : "";
            $listeUtil .= "<OPTION value=\"".$enr['util_id']."\"".$selected.$style.">".htmlspecialchars($enr['nomUtil'])."</OPTION>";
          }
          $listeUtil .= "</SELECT>";
          $titrePage = "&nbsp;&nbsp;".sprintf(trad("TITRE_PLANNING_DE"),$listeUtil);
        }
      }
  }
?>

<!-- MODULE TITRE DE L'AGENDA -->
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD class="PageTitre" nowrap><?php echo $titrePage; ?></TD>
<?php message($msg); ?>
    <TD align="right" nowrap class="PageDate"><B><?php echo aff_horoscope(date("M j",$sd))."&nbsp;".aff_meteo_j(date("M j",$sd))."&nbsp;".$tabJour[date("w",$sd)]." ".$jourEnCours." ".strtolower($tabMois[date("n",$sd)])." ".$anneeEnCours; ?></B>&nbsp;&nbsp;<BR><A style="COLOR: <?php echo $PageDate; ?>;FONT-SIZE: 10px;"><?php echo sprintf(trad("TITRE_FETE_JOUR"),$feteDuJour); ?>&nbsp;&nbsp;<br><?php echo sprintf(trad("TITRE_QUANTIEME"),date("z",$sd)+1,(365+date("L",$sd))-(date("z",$sd)+1)); ?></A>&nbsp;&nbsp;</TD>
  </TR>
  </TABLE>
<!-- FIN MODULE TITRE DE L'AGENDA -->
