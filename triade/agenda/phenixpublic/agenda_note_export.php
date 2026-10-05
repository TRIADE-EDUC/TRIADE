<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Aide.txt ?>
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

  // On regarde si les fichiers de config sont deja charges
  if (!defined("_CONF_INC_LOADED")) {
    include("inc/param.inc.php");
    include("inc/fonctions.inc.php");
    include("inc/html.inc.php");
    include("lang/$APPLI_LANGUE.php");
  }

  // On gere un format d'export specifique selon qu'il est destine a Sunbird / MS Outlook / URL dans profil PHENIX
  // Possible icsSU (Sunbird) / icsMS ((Outlook 2000)  / icsURL (via URL dans profil) / vcs / csv
  $typeExport = strtolower(@substr($zlTypeFichier,0,3));

  // Generation du fichier d'export
  if (!empty($typeExport)) {

    // Export RSS
    if ($typeExport=="rss") {
      // Si depuis URL PHENIX
      // Date de debut et de fin en fonction des notes de l'utilisateur identifie par l'ID passe en parametre dans l'URL
      $DB_CX->DbQuery("SELECT util_id, util_format_nom,util_prenom,util_nom,util_langue,util_interface FROM ${PREFIX_TABLE}utilisateur WHERE util_url_export='".$id."'");
      $idUser = $DB_CX->DbResult(0,0) + 0;
      $FORMAT_NOM_UTIL = ($DB_CX->DbResult(0,1) == "0") ? "util_nom, ' ', util_prenom" : "util_prenom, ' ', util_nom";
      $NOM_UTIL_CREATEUR = str_replace("util_","t1.util_",$FORMAT_NOM_UTIL);
      $NOM_UTIL_MODIFICATEUR = str_replace("util_","t2.util_",$FORMAT_NOM_UTIL);
      $prenom_util = $DB_CX->DbResult(0,2);
      $nom_util = $DB_CX->DbResult(0,3);
      if ($DB_CX->DbResult(0,4)!=NULL && $DB_CX->DbResult(0,4)!="") {
        // Ecrase la selection de la langue par defaut par le choix de l'utilisateur
        @include("lang/$APPLI_LANGUE.php");
      }
      // On recupere l'interface pour l'affichage sur retour de lien
      $APPLI_STYLE = $DB_CX->DbResult(0,"util_interface");
      if (!file_exists("skins/".$APPLI_STYLE.".php")) {
        $APPLI_STYLE = "Petrole";
      }
      // Recuperation des infos de timezone de l'utilisateur
      $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, t2.util_format_heure FROM ${PREFIX_TABLE}utilisateur t1, ${PREFIX_TABLE}utilisateur t2, ${PREFIX_TABLE}timezone WHERE t1.util_id=".(($id_partage!="")?$id_partage:$idUser)." AND t2.util_id=".$idUser." AND ((tzn_zone=t1.util_timezone AND t2.util_timezone_partage='O') OR (tzn_zone=t2.util_timezone AND t2.util_timezone_partage='N'))");
      $tzGmt = $DB_CX->DbResult(0,"tzn_gmt");
      $tzDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
      $tzHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
      $tzDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
      $tzHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
      $formatHeure = $DB_CX->DbResult(0,"util_format_heure")==12 ? "h:ia" : "H:i";

      // Recuperation des bornes d'export choisis
      $a_fin = gmdate("Y") + 5;
      $mj_fin = gmdate("m-d");
      $date_fin = $a_fin."-".$mj_fin;
      // Demande affichage agenda d'un utilisateur different
      if ($id_partage!="") {
        // Recherche du nom de l'utilisateur
        $DB_CX->DbQuery("SELECT DISTINCT * FROM ${PREFIX_TABLE}planning_partage, ${PREFIX_TABLE}planning_affecte WHERE (paf_util_id=".$id_partage." AND paf_consultant_id=".$idUser.") OR (ppl_util_id=".$id_partage." AND ppl_consultant_id=".$idUser.")");
        if ($enr = $DB_CX->DbNextRow()) {
          $idUser = $id_partage;
          $DB_CX->DbQuery("SELECT DISTINCT CONCAT(".$FORMAT_NOM_UTIL.") AS nomCreateur FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$id_partage."");
          $util_desc = $DB_CX->DbResult(0,0);
        }
      }
      // Selection des dates a partir d'aujourd'hui
      $sql  = "SELECT age_id, age_libelle, age_detail, age_prive, age_aty_id, age_date, age_heure_debut, age_heure_fin, age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_couleur, age_date_creation, age_createur_id, age_date_modif, age_modificateur_id, age_plage, age_plage_duree, t1.util_semaine_type, age_lieu, age_disponibilite, age_rappel, age_rappel_coeff, age_email, age_cal_id, CONCAT(".$NOM_UTIL_CREATEUR.") AS nomCreateur, CONCAT(".$NOM_UTIL_MODIFICATEUR.") AS nomModificateur, t1.util_email ";
      $sql .= "FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur t1, ${PREFIX_TABLE}utilisateur t2 ";
      $sql .= "WHERE aco_util_id=".$idUser." AND age_id=aco_age_id AND t1.util_id=age_createur_id AND t2.util_id=age_modificateur_id";
      if ($idAge!="") {
        $sql .= " AND age_id=".$idAge;
      }
      if ($id_partage !="") {
        $sql .= " AND age_prive='0'";
      }
      if (empty($limit_rss)) {
        $limit_rss = 15;
      }
      $sql .= " AND age_date>='".gmdate("Y-m-d")."' AND age_date<='".$date_fin."' ORDER BY age_date, age_heure_debut ASC LIMIT 0,".$limit_rss;
      if ($idAge!="") {
        $DB_CX->DbQuery($sql);
        $enr = $DB_CX->DbNextRow();
        // Affichage de la note sur retour de lien
        echo "<HTML><HEAD><LINK rel=\"stylesheet\" type=\"text/css\" href=\"css/agenda_css.php?id=".$APPLI_STYLE."\"></HEAD><BODY style=\"background: url('image/trans.gif')\">";
        echo "<BR><TABLE><TR><TD>&nbsp;</TD><TD>";
        echo "<TABLE width=\"422\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" class=\"infoBulle\"><TR><TD><TABLE width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><TR valign=\"top\"><TD class=\"ibHeure\" nowrap>";
        //Decalage des notes en fonction du fuseau horaire
        list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif'],$enr['age_date']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],1,0,0);
        // On met la date de debut et de fin au bon format
        $heure_debut = afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure);
        $heure_fin = afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure);
        list($note_a,$note_m,$note_j) = explode("-",$enr['age_date']);
        echo $note_j."/".$note_m."/".$note_a."&nbsp;";
        echo "<BR>".$heure_debut."&rsaquo;".$heure_fin."&nbsp;";
        echo "</TD><TD class=\"ibTitre\" width=\"100%\">";
        echo htmlspecialchars(stripslashes($enr['age_libelle']));
        if ($enr['age_lieu']!="") {
          echo "<BR><i>(".htmlspecialchars(stripslashes($enr['age_lieu'])).")</i>";
        }
        echo "</TD></TR></TABLE></TD></TR><TR><TD class=\"ibTexte\">";
        $enr['age_detail'] = htmlspecialchars(stripslashes($enr['age_detail']));
        //Info sur la creation / modification de la note
        afficheInfoModifNote($enr, $idUser);
        if ($enr['age_detail']!="") {
          echo $enr['age_detail'];
        }
        echo "</TD></TR></TABLE>";
        echo "</TD></TR></TABLE>";
        echo "</BODY></HTML>";
      } else {
        // Creation de l'entete xml
        $contenu = "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?".">";
        $contenu .= "<rss version=\"2.0\">";
        // creation du flux RSS
        $contenu .= "<channel>";
        $contenu .= "<link>http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,-22)."phenix.php</link>";
        $contenu .= "<title>".trad("NOTEEXP_RSS_TITRE")."</title>";
        if ($id_partage!="") $contenu .= "<description>".$util_desc."</description>";
        else $contenu .= "<description>".$prenom_util." ".strtoupper($nom_util)."</description>";
        $DB_CX->DbQuery($sql);
        // Boucle sur les evenements
        while ($enr = $DB_CX->DbNextRow()) {
          // Creation des articles dans le flux
          $contenu .= "<item>";
          // On formate la date de publication
          list($note_a,$note_m,$note_j) = explode("-",$enr['age_date']);
          // On met le la date de publication au format RFC 822 : Thu, 14 Dec 2006 13:53:39 GMT
          $date_deb_fmt = date("D, d M Y H:i:s \G\M\T",mktime(floor($enr['age_heure_debut']),($enr['age_heure_debut']*60)%60,0,$note_m,$note_j,$note_a));
          //Decalage des notes en fonction du fuseau horaire
          list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],$enr['age_date']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],1,0,1);
          // On met la date de debut et de fin au bon format
          $heure_debut = afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure);
          $heure_fin = afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure);
          list($note_a,$note_m,$note_j) = explode("-",$enr['age_date']);
          $contenu .= "<title>".$note_j."/".$note_m."/".$note_a." (".$heure_debut."-".$heure_fin.") : ".htmlspecialchars(stripslashes($enr['age_libelle']))."</title>";
          if ($id_partage!="") $contenu .= "<link>http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,-22)."agenda_note_export.php?zlTypeFichier=RSS&amp;id=".$id."&amp;limit_rss=".$limit_rss."&amp;id_partage=".$id_partage."&amp;idAge=".$enr['age_id']."</link>";
          else $contenu .= "<link>http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,-22)."agenda_note_export.php?zlTypeFichier=RSS&amp;id=".$id."&amp;limit_rss=".$limit_rss."&amp;idAge=".$enr['age_id']."</link>";

          $contenu .= "<guid isPermaLink=\"false\">".md5($enr['age_id'])."</guid>";
          $contenu .= "<description>";
          if ($enr['age_detail']!="") {
            $contenu .= htmlspecialchars(stripslashes($enr['age_detail']));
          }
          if ($enr['age_lieu']!="") {
            $contenu .= "&lt;br&gt;".trad("NOTEEXP_RSS_EMPLACEMENT")." : ".htmlspecialchars(stripslashes($enr['age_lieu']));
          }
          $contenu .= "</description>";
          $contenu .= "<category>".($enr['age_prive']==1 ? trad("NOTEEXP_RSS_CAT_PRIV") : trad("NOTEEXP_RSS_CAT_PUB"))."</category>";
          $contenu .= "<pubDate>".$date_deb_fmt."</pubDate>";
          $contenu .= "<author>".$enr['nomCreateur'].(!empty($enr['util_email'])?" &lt;".$enr['util_email']."&gt;":"")."</author>";
          $contenu .= "</item>";
        }
        $contenu .= "</channel>";
        $contenu .= "</rss>";
        header("Content-Type: application/rss+xml");
        echo $contenu;
      }
      exit; // Pas besoin d'aller plus loin !
    }
    // Export ICalenda ICS-> Sunbird
    elseif ($typeExport=="ics") {
      // On gere un format d'export specifique selon qu'il est destine a Sunbird / MS Outlook / URL dans profil PHENIX
      // Possible icsSU (sunbird) / icsMS (Outlook)  / icsURL (via URL dans profil)
      $subTypeExport = strtoupper(substr($zlTypeFichier,3, 3));
      if ($subTypeExport == "URL") {
        // Si depuis URL PHENIX
        //Date de debut et de fin en fonction des notes de l'utilisateur identifie par l'ID passe en parametre dans l'URL
        $DB_CX->DbQuery("SELECT MIN(CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))), MAX(CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))), util_id, util_format_nom, util_langue FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur WHERE age_aty_id>1 AND age_mere_id=0 AND age_id=aco_age_id AND aco_util_id=util_id AND util_url_export='".$id."' GROUP BY util_id");
        if ($DB_CX->DbResult(0,0)!=NULL) {
          $date_dem_deb = $DB_CX->DbResult(0,0);
          $date_dem_fin = $DB_CX->DbResult(0,1);
        } else {
          $date_dem_deb = (gmdate("Y")-5).gmdate("-m-d")." 00.00";
          $date_dem_fin = (gmdate("Y")+5).gmdate("-m-d")." 24.00";
        }
        // Si l'idUser recupere n'est pas valide, le rejet se fera dans la recuperation du nom utilisateur (commun a tous les exports)
        $idUser = $DB_CX->DbResult(0,2) + 0;
        $FORMAT_NOM_UTIL = ($DB_CX->DbResult(0,3) == "0") ? "util_nom, ' ', util_prenom" : "util_prenom, ' ', util_nom";
        // On enregistre le fichier de langue de l'utilisateur s'il est bien renseigne en base
        if ($DB_CX->DbResult(0,4)!=NULL && $DB_CX->DbResult(0,4)!="") {
          $APPLI_LANGUE = $DB_CX->DbResult(0,4);
        }
      } else {
        // Recuperation de l'idUser par la verification de la session
        $idUser = Session_ok($sid);
      }

      // Liste des couleurs
      $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}couleurs WHERE cou_util_id=0 OR cou_util_id=".$idUser." ORDER BY cou_libelle");
      $tabCouleur = Array();
      while($enr=$DB_CX->DbNextRow()) {
        $tabCouleur[$enr['cou_libelle']] = $enr['cou_couleur'];
      }
      // Ecrase la selection de la langue par defaut par le choix de l'utilisateur
      @include("lang/$APPLI_LANGUE.php");

      // Recuperation des infos de timezone de l'utilisateur
      $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, tzn_zone FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$idUser." AND tzn_zone=util_timezone");
      $tzGmt = $DB_CX->DbResult(0,"tzn_gmt");
      $tzDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
      $tzHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
      $tzDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
      $tzHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
      $tzZone = $DB_CX->DbResult(0,"tzn_zone");
      // Calcul des bascules ete/hiver pour la date et l'heure locale
      $tzEte = calculBasculeDST($tzDateEte,gmdate("Y"),$tzHeureEte,$tzGmt,0);
      $tzHiver = calculBasculeDST($tzDateHiver,gmdate("Y"),$tzHeureHiver,$tzGmt,1);

      if ($ckExportTz!="1") {
        if ($subTypeExport!="MS") {
          $TZID = ";TZID=/mozilla.org/20071231_1/".$tzZone;
        } else {
          $TZID = ";TZID=".$tzZone;
        }
      } else {
        $TZID = "";
      }
      // Recuperation des bornes d'export choisis
      if ($subTypeExport!="URL") {
        if ($rdChExport=="1") {
          $ztDateDeb = $ztDateDebC;
          $ztDateFin = $ztDateFinC;
        }
        // Conversion de la date de debut en UTC
        list($j_deb,$m_deb,$a_deb) = explode("/",$ztDateDeb);
        $d_deb = $a_deb."-".$m_deb."-".$j_deb;
        $h_deb = "00.00";
        list($tzEteD,$tzHiverD,$hBascule,$h_deb,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_deb,$h_deb,1);
        $decalHD = calculDecalageH($tzGmt,$tzEteD,$tzHiverD,mktime(floor($h_deb-$hBascule),(($h_deb-$hBascule)*60)%60,0,$m_deb,$j_deb,$a_deb));
        $date_dem_deb = mktime(floor($h_deb-$decalHD),(($h_deb-$decalHD)*60)%60,0,$m_deb,$j_deb,$a_deb);
        // Conversion de la date de fin en UTC
        list($j_fin,$m_fin,$a_fin) = explode("/",$ztDateFin);
        $d_fin = $a_fin."-".$m_fin."-".$j_fin;
        $h_fin = "24.00";
        list($tzEteF,$tzHiverF,$hBascule,$h_fin,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_fin,$h_fin,1);
        $decalHF = calculDecalageH($tzGmt,$tzEteF,$tzHiverF,mktime(floor($h_fin-$hBascule),(($h_fin-$hBascule)*60)%60,0,$m_fin,$j_fin,$a_fin));
        $date_dem_fin = mktime(floor($h_fin-$decalHF),(($h_fin-$decalHF)*60)%60,0,$m_fin,$j_fin,$a_fin);
        if ($rdChExport!="1") {
          $date_dem_deb = date("Y-m-d H",$date_dem_deb).".".sprintf("%02d",round(date("i",$date_dem_deb)*100/60));
          $date_dem_fin = date("Y-m-d H",$date_dem_fin).".".sprintf("%02d",round(date("i",$date_dem_fin)*100/60));
        } else {
          $date_dem_deb = date("Y-m-d H:i:s",$date_dem_deb);
          $date_dem_fin = date("Y-m-d H:i:s",$date_dem_fin);
        }
      }

      // Ajustement de la date en fonction du timezone
      $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(gmdate("H"),gmdate("i"),0,gmdate("n"),gmdate("j"),gmdate("Y")));
      $localTime = mktime(gmdate("H")+floor($decalageHoraire),gmdate("i")+($decalageHoraire*60)%60,gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));

      //Nom du fichier d'export
      $fileName = "Export_agenda_iCal_".date("Ymd-His",$localTime).".ics";

      $DB_CX->DbQuery("SELECT CONCAT(".$FORMAT_NOM_UTIL.") FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
      if (!$DB_CX->DbNumRows()) {
        if ($subTypeExport == "URL") {
          $contenu = "BEGIN:VCALENDAR\r\nEND:VCALENDAR";
        } else {
          $contenu = sprintf(trad("NOTEEXP_ECHEC_CREATION"), $sql);
        }
      } else {
        $prenom_nom = $DB_CX->DbResult(0,0);

        // Tableau des jours de la semaine pour la conversion
        $jour = array("SU","MO","TU","WE","TH","FR","SA");

        // Exclusion
        $exclusion  = ($ckAffecte == 1) ? " AND age_util_id=".$idUser : ""; //  des notes affectees
        $exclusion .= ($ckFini == 1) ? " AND aco_termine=".($zlTermine+0) : ""; // des notes terminees ou actives

        if ($rdChExport!="1") {
          $sql  = "SELECT age_id, age_libelle, age_detail, age_prive, age_aty_id, age_date, age_heure_debut, age_heure_fin, age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_couleur, age_date_creation, age_date_modif, age_plage, age_plage_duree, util_semaine_type, age_lieu, age_disponibilite, age_rappel, age_rappel_coeff, age_email, age_cal_id ";
          $sql .= "FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur ";
          $sql .= "WHERE aco_util_id=".$idUser." AND age_id=aco_age_id AND age_aty_id>1 AND age_mere_id=0 AND util_id=age_createur_id";
          $sql .= " AND CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))>='".$date_dem_deb."' AND CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))<='".$date_dem_fin."'".$exclusion;
        } else {
          $sql  = "SELECT age_id, age_libelle, age_detail, age_prive, age_aty_id, age_date, age_heure_debut, age_heure_fin, age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_couleur, age_date_creation, age_date_modif, age_plage, age_plage_duree, util_semaine_type, age_lieu, age_disponibilite, age_rappel, age_rappel_coeff, age_email, age_cal_id ";
          $sql .= "FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur ";
          $sql .= "WHERE aco_util_id=".$idUser." AND age_id=aco_age_id AND age_aty_id>1 AND age_mere_id=0 AND util_id=age_createur_id";
          $sql .= " AND age_date_modif>='".$date_dem_deb."' AND age_date_modif<='".$date_dem_fin."'".$exclusion;
        }

        $DB_CX->DbQuery($sql);

        if ($DB_CX->DbNumRows()) {
          $contenu = "";
          $contenu .= "BEGIN:VCALENDAR\r\n";
          $contenu .= "PRODID:-//Phenix_MR_v1.0\r\n";
          $contenu .= "VERSION:2.0\r\n";
          $contenu .= "METHOD:REQUEST\r\n\r\n";

          // Boucle sur les evenements
          while ($enr = $DB_CX->DbNextRow()) {
            $data = "BEGIN:VEVENT\r\n";
            $data .= "UID:".$enr['age_id']."\r\n";
            $data .= "SUMMARY:".utf8_encode($enr['age_libelle'])."\r\n";
            if ($enr['age_lieu']!="") $data .= "LOCATION:".utf8_encode($enr['age_lieu'])."\r\n";
            if ($ckExportHtml!="1") {
              $dataDescript="";
              if ($enr['age_detail']!="")
                $dataDescript = preg_replace('#\r\n|\n|\r#', '\\n', strip_tags (html_entity_decode($enr['age_detail'])));
              if ($dataDescript !="")
                $data .= "DESCRIPTION:".utf8_encode($dataDescript)."\r\n";
            } else {
              if ($enr['age_detail']!="")
                $data .= "DESCRIPTION:".utf8_encode(preg_replace('#\r\n|\n|\r#', '\\n', $enr['age_detail']))."\r\n";
            }
            $data .= "ATTENDEE:".utf8_encode($prenom_nom)."\r\n";
            $data .= "CLASS:".(($enr['age_prive']==1) ? "PRIVATE" : "PUBLIC")."\r\n";
            // Ajout de champs particuliers pour PHENIX : type de note, couleur de l'agenda, rappel, disponibilite, contact associe !!
            $data .= "X-PHENIX-AGENDA-TYPE:".$enr['age_aty_id']."\r\n";
            $cat_couleur ="";
            if ($enr['age_couleur']!="") {
              $data .= "X-PHENIX-AGENDA-COLOR:".$enr['age_couleur']."\r\n";
              reset ($tabCouleur);
              while (list($key, $val) = each($tabCouleur)) {
                if ($val == $enr['age_couleur']) $cat_couleur = $key;
              }
            }
            if ($enr['age_rappel']!=0) $data .= "X-PHENIX-AGENDA-RAPPEL:".$enr['age_rappel']." ".$enr['age_rappel_coeff']." ".$enr['age_email']."\r\n";
            if ($enr['age_disponibilite']!=0) $data .= "X-PHENIX-AGENDA-DISPO:".$enr['age_disponibilite']."\r\n";
            if ($enr['age_cal_id']!=0) $data .= "X-PHENIX-AGENDA-CONTACT:".$enr['age_cal_id']."\r\n";

            // Pour etre conforme a la RFC 2445, le Z indique une date UTC
            $codeUTC = "Z";
            if ($ckExportTz!="1") {
              // Remise a l'heure du timezone
              list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif'],$enr['age_date']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],1,0,1);
              $enr['age_heure_debut']=sprintf("%01.2f",$enr['age_heure_debut']);
              $enr['age_heure_fin']=sprintf("%01.2f",$enr['age_heure_fin']);
              $codeUTC = "";
            }
            // Aucune gestion heure ete/hiver
            list($heure_deb,$min_deb) = explode(".",$enr['age_heure_debut']);
            list($heure_fin,$min_fin) = explode(".",$enr['age_heure_fin']);
            // Date de creation de la note : 2006-10-17 11:08:02 -> 20061017T110802Z
            $date_create = substr($enr['age_date_creation'],0,10);
            $heure_create = substr($enr['age_date_creation'],11,2);
            $min_create = substr($enr['age_date_creation'],14,2);
            if ($date_create =="0000-00-00") {
              $date_create = gmdate("Y-m-d");
            }
            if (($heure_create=="00") && ($min_create=="00")) {
              $heure_create = gmdate('H');
              $min_create = gmdate('i');
            }
            if (strlen($heure_create)==1) $heure_create = "0".$heure_create;
            $data .= "DTSTAMP:".str_replace("-","",$date_create)."T".$heure_create.$min_create."00Z\r\n";
             // Date de modification de la note : 2006-10-17 11:08:02 -> 20061017T110802Z
            $date_modif = substr($enr['age_date_modif'],0,10);
            $heure_modif = substr($enr['age_date_modif'],11,2);
            $min_modif = substr($enr['age_date_modif'],14,2);
            if ($date_modif =="0000-00-00") {
              $date_modif = gmdate("Y-m-d");
            }
            if (($heure_modif=="00") && ($min_modif=="00")) {
              $heure_modif = gmdate('H');
              $min_modif = gmdate('i');
            }
            if (strlen($heure_modif)==1) $heure_modif = "0".$heure_modif;
            $data .= "LAST-MODIFIED:".str_replace("-","",$date_modif)."T".$heure_modif.$min_modif."00Z\r\n";
            if (strlen($heure_deb)==1) $heure_deb = "0".$heure_deb;
            if (strlen($heure_fin)==1) $heure_fin = "0".$heure_fin;
            if ($heure_deb < 0) $heure_deb="00";
            if ($heure_fin < 0) $heure_fin="00";
            $min_deb = $min_deb * 60 / 100;
            $min_fin = $min_fin * 60 / 100;
            if (strlen($min_deb)==1) $min_deb = "0".$min_deb;
            if (strlen($min_fin)==1) $min_fin = "0".$min_fin;
            // Decalage de la date de fin si note a cheval
            list($a_deb,$m_deb,$j_deb) = explode ("-",$enr['age_date']);
            $date_fin = ($enr['age_heure_debut']>$enr['age_heure_fin']) ? date("Y-m-d",mktime(0,0,0,$m_deb,$j_deb+1,$a_deb)) : $enr['age_date'];
            // On enregistre les heures de debut et de fin de la note
            if ($enr['age_aty_id']==3) {
              $date_fin = date("Y-m-d",mktime(0,0,0,$m_deb,$j_deb+1,$a_deb));
              $data .= "DTSTART;VALUE=DATE".$TZID.":".str_replace("-","",$enr['age_date'])."\r\n";
              $data .= "DTEND;VALUE=DATE".$TZID.":".str_replace("-","",$date_fin)."\r\n";
            } else {
              $data .= "DTSTART".$TZID.":".str_replace("-","",$enr['age_date'])."T".$heure_deb.$min_deb."00".$codeUTC."\r\n";
              $data .= "DTEND".$TZID.":".str_replace("-","",$date_fin)."T".$heure_fin.$min_fin."00".$codeUTC."\r\n";
            }
            // insersion categorie
            if ($cat_couleur!="") $data .= "CATEGORIES:".utf8_encode($cat_couleur)."\r\n";
            // Si on traite une note recurrente
            if ($enr['age_ape_id']>1) {

              // On cherche le format de repetition de la note
              if ($enr['age_plage']==2) {
                //Avec une date de fin
                $repetNote = "UNTIL=".date("Ymd",$enr['age_plage_duree']).";";
              } elseif ($enr['age_plage']==1) {
                //Avec un nombre d'occurrence
                $repetNote = "COUNT=".$enr['age_plage_duree'].";";
              } else {
                //Si erreur bien que peu probable
                $repetNote = "COUNT=1;";
              }

              // PERIODICITE QUOTIDIENNE
              if ($enr['age_ape_id']==2) {
                // on gere les recurrences quotidiennes mais pas celles lundi->vendredi
                // trop d'exception sam. dim. a gerer en cas de recurrence sur du long terme
                // on cherche la derniere occurrence
                if ($enr['age_periode1']==1) {
                  // Tous les X(age_periode2) jours
                  $data .= "RRULE:FREQ=DAILY;".$repetNote."INTERVAL=".$enr['age_periode2']."\r\n";
                } elseif ($enr['age_periode1']==2) {
                  //Tous les jours ouvrables (Lundi au Vendredi) => on utilise la periodicite hebdomadaire
                  $data .= "RRULE:FREQ=WEEKLY;".$repetNote."INTERVAL=1;BYDAY=MO,TU,WE,TH,FR\r\n";
                }
              }
              //PERIODICITE HEBDOMADAIRE
              elseif ($enr['age_ape_id']==3) {
                // Toutes les X (age_periode1) semaines, chaque Y (age_periode2) jours (Lundi a Dimanche)
                //On commence par regarder si la semaine type est enregistree dans la note au format PHP, sinon on prend celle du CREATEUR de la note en la mappant au format PHP (L->D => D->S)
                $vSemaineType = ($enr['age_periode2']>0) ? $enr['age_periode2'] : substr($enr['util_semaine_type'],6).substr($enr['util_semaine_type'],0,6);
                //Si on a recupere la semaine type dans la note les premiers 0 ne sont pas recuperes a cause du type du champ (INT et pas VARCHAR) => on corrige
                for ($i=strlen($vSemaineType);$i<7;$i++) {
                  $vSemaineType = "0".$vSemaineType;
                }
                if ($vSemaineType!="0000000") {
                  $strByDay = "";
                  for ($i=0; $i<7; $i++) {
                    if (substr($vSemaineType,$i,1)==1)
                      $strByDay .= ",".$jour[$i]; //  0 -> Dimanche ... 6 -> Samedi
                  }
                  $data .= "RRULE:FREQ=WEEKLY;".$repetNote."INTERVAL=".$enr['age_periode1'].";BYDAY=".substr($strByDay,1)."\r\n";
                }
              }
              //PERIODICITE MENSUELLE
              elseif ($enr['age_ape_id']==4) {
                if ($enr['age_periode1']==1) {
                  // Tous les X (age_periode2) date du jour de chaque mois
                  $data .= "RRULE:FREQ=MONTHLY;".$repetNote."INTERVAL=".$enr['age_periode4'].";BYMONTHDAY=".$enr['age_periode2']."\r\n";
                } elseif ($enr['age_periode1']==2) {
                  // Tous les premiers...dernier X (age_periode2) nom du jour (MO,TU,WE,TH,FR,SA) de chaque mois
                  $rang = $enr['age_periode2'] + 1;
                  if ($rang==5) {
                      $rang = "-1"; // exemple le dernier mercredi du mois => BYDAY=-1WE
                  }
                  if ($subTypeExport != "MS") {
                    $data .= "RRULE:FREQ=MONTHLY;".$repetNote."INTERVAL=".$enr['age_periode4'].";BYDAY=".$rang.$jour[$enr['age_periode3']]."\r\n";
                  } else {
                    $data .= "RRULE:FREQ=MONTHLY;".$repetNote."INTERVAL=".$enr['age_periode4'].";BYDAY=".$jour[$enr['age_periode3']].";BYSETPOS=".$rang."\r\n";
                  }
                }
              }
              //PERIODICITE ANNUELLE
              elseif ($enr['age_ape_id']==5) {
                // Tous les X(age_periode2) mois
                if ($enr['age_periode1']==1) {
                  // Tous les X jour(age_periode2) du mois (age_periode3)
                  $data .= "RRULE:FREQ=YEARLY;".$repetNote."INTERVAL=1;BYMONTHDAY=".$enr['age_periode2'].";BYMONTH=".$enr['age_periode3']."\r\n";
                }
                if ($enr['age_periode1']==2) {
                  // Tous les premiers...dernier X (age_periode2) nom du jour (age_periode3) de chaque mois (age_periode4)
                  $rang = $enr['age_periode2'] + 1;
                  if ($rang==5) {
                      $rang = "-1"; // exemple le dernier mercredi du mois => BYDAY=-1WE
                  }
                  if ($subTypeExport != "MS") {
                    $data .= "RRULE:FREQ=YEARLY;".$repetNote."INTERVAL=1;BYDAY=".$rang.$jour[$enr['age_periode3']].";BYMONTH=".$enr['age_periode4']."\r\n";
                  } else {
                    $data .= "RRULE:FREQ=YEARLY;".$repetNote."INTERVAL=1;BYDAY=".$jour[$enr['age_periode3']].";BYMONTH=".$enr['age_periode4'].";BYSETPOS=".$rang."\r\n";
                  }
                }
              }
            }
            if ($enr['age_rappel']!=0) {
              $data .= "BEGIN:VALARM\r\n";
              $data .= "TRIGGER;VALUE=DURATION:";
              if ($enr['age_rappel_coeff']=="1") $data .= "-PT".$enr['age_rappel']."M\r\n";
              if ($enr['age_rappel_coeff']=="60") $data .= "-PT".$enr['age_rappel']."H\r\n";
              if ($enr['age_rappel_coeff']=="1440") $data .= "-P".$enr['age_rappel']."D\r\n";
              $date_actuelle = date("Ymd Hi",$localTime);
              $data .= "X-MOZ-LASTACK:".substr($date_actuelle,0,8)."T".substr($date_actuelle,-4)."00\r\n";
              $data .= "ACTION:DISPLAY\r\n";
              $data .= "END:VALARM\r\n";
            }
            $data .= "END:VEVENT\r\n";
            $data .= "\r\n";
            $contenu .= $data ;
          }
          $contenu .= "END:VCALENDAR";
        } else {
          if ($subTypeExport == "URL") {
            $contenu = "BEGIN:VCALENDAR\r\nEND:VCALENDAR";
          } else {
            $contenu = trad("NOTEEXP_AUCUNE_DONNEE");
          }
        }
      }
      //Sauvegarde des parametres d'exportation
      if ($subTypeExport != "URL") {
        $ckSauvSql +=0;
        if ($ckSauvSql==1) {
          $ckExportHtml +=0;
          $ckExportTz +=0;
          if ($ckFini=="1") {
            $zlTermine++;
          } else {
            $zlTermine="0";
          }
          $DB_CX->DbQuery("SELECT aex_util_id FROM ${PREFIX_TABLE}agenda_export WHERE aex_util_id=".$idUser);
          if ($DB_CX->DbNumRows()) {
            $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda_export SET aex_creation='".$rdChExport."', aex_html='".$ckExportHtml."', aex_tz='".$ckExportTz."', aex_ch_note='".$zlTermine."', aex_note_aff='".$ckAffecte."'". ($rdChExport!="0" ? ", aex_date_old='".$date_dem_fin."'" : "").", aex_type='".$zlTypeFichier."' WHERE aex_util_id='".$idUser."'");
          } else {
            $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_export (aex_util_id,aex_creation,aex_html,aex_tz,aex_ch_note,aex_note_aff". ($rdChExport!="0" ? ",aex_date_old" : "").",aex_type) VALUES (".$idUser.",'".$rdChExport."','".$ckExportHtml."','".$ckExportTz."','".$zlTermine."','".$ckAffecte."'". ($rdChExport!="0" ? ",'".$date_dem_fin."'" : "").",'".$zlTypeFichier."')");
          }
        } else {
          $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_export WHERE aex_util_id='".$idUser."'");
        }
      }
    }
    //--------------------------------------------------

    // Export VCalendar VCS -> Palm Desktop
    elseif ($typeExport=="vcs") {
      // Recuperation de l'idUser par la verification de la session
      $idUser = Session_ok($sid);
      include("lang/$APPLI_LANGUE.php");

      // Liste des couleurs
      $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}couleurs WHERE cou_util_id=0 OR cou_util_id=".$idUser." ORDER BY cou_libelle");
      $tabCouleur = Array();
      while($enr=$DB_CX->DbNextRow()) {
        $tabCouleur[$enr['cou_libelle']] = $enr['cou_couleur'];
      }
      // Recuperation des infos de timezone de l'utilisateur
      $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$idUser." AND tzn_zone=util_timezone");
      $tzGmt = $DB_CX->DbResult(0,"tzn_gmt");
      $tzDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
      $tzHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
      $tzDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
      $tzHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
      // Calcul des bascules ete/hiver pour la date et l'heure locale
      $tzEte = calculBasculeDST($tzDateEte,gmdate("Y"),$tzHeureEte,$tzGmt,0);
      $tzHiver = calculBasculeDST($tzDateHiver,gmdate("Y"),$tzHeureHiver,$tzGmt,1);

      // On gere un format d'export specifique selon qu'il est destine a PalmDesktop ou non (ne gere pas les #n)
      // Possible vcsSTD (standard) / vcsPALM (PalmDesktop)
      $subTypeExport = strtoupper(substr($zlTypeFichier,3,strlen($zlTypeFichier)));

      // Recuperation des bornes d'export choisis
      if ($rdChExport=="1") {
        $ztDateDeb = $ztDateDebC;
        $ztDateFin = $ztDateFinC;
      }
      // Conversion de la date de debut en UTC
      list($j_deb,$m_deb,$a_deb) = explode("/",$ztDateDeb);
      $d_deb = $a_deb."-".$m_deb."-".$j_deb;
      $h_deb = "00.00";
      list($tzEteD,$tzHiverD,$hBascule,$h_deb,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_deb,$h_deb,1);
      $decalHD = calculDecalageH($tzGmt,$tzEteD,$tzHiverD,mktime(floor($h_deb-$hBascule),(($h_deb-$hBascule)*60)%60,0,$m_deb,$j_deb,$a_deb));
      $date_dem_deb = mktime(floor($h_deb-$decalHD),(($h_deb-$decalHD)*60)%60,0,$m_deb,$j_deb,$a_deb);
      // Conversion de la date de fin en UTC
      list($j_fin,$m_fin,$a_fin) = explode("/",$ztDateFin);
      $d_fin = $a_fin."-".$m_fin."-".$j_fin;
      $h_fin = "24.00";
      list($tzEteF,$tzHiverF,$hBascule,$h_fin,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_fin,$h_fin,1);
      $decalHF = calculDecalageH($tzGmt,$tzEteF,$tzHiverF,mktime(floor($h_fin-$hBascule),(($h_fin-$hBascule)*60)%60,0,$m_fin,$j_fin,$a_fin));
      $date_dem_fin = mktime(floor($h_fin-$decalHF),(($h_fin-$decalHF)*60)%60,0,$m_fin,$j_fin,$a_fin);
      if ($rdChExport!="1") {
        $date_dem_deb = date("Y-m-d H",$date_dem_deb).".".sprintf("%02d",round(date("i",$date_dem_deb)*100/60));
        $date_dem_fin = date("Y-m-d H",$date_dem_fin).".".sprintf("%02d",round(date("i",$date_dem_fin)*100/60));
      } else {
        $date_dem_deb = date("Y-m-d H:i:s",$date_dem_deb);
        $date_dem_fin = date("Y-m-d H:i:s",$date_dem_fin);
      }

      // Ajustement de la date en fonction du timezone
      $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(gmdate("H"),gmdate("i"),0,gmdate("n"),gmdate("j"),gmdate("Y")));
      $localTime = mktime(gmdate("H")+floor($decalageHoraire),gmdate("i")+($decalageHoraire*60)%60,gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));

      //Nom du fichier d'export
      $fileName = "Export_agenda_vCal_".date("Ymd-His",$localTime).".vcs";

      $DB_CX->DbQuery("SELECT CONCAT(".$FORMAT_NOM_UTIL.") FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
      if (!$DB_CX->DbNumRows()) {
        $contenu = sprintf(trad("NOTEEXP_ECHEC_CREATION"), $sql);
      } else {
        $prenom_nom = $DB_CX->DbResult(0,0);

        // Tableau des jours de la semaine pour la conversion
        $jour = array("SU","MO","TU","WE","TH","FR","SA");

        // Exclusion
        $exclusion  = ($ckAffecte == 1) ? " AND age_util_id=".$idUser : ""; //  des notes affectees
        $exclusion .= ($ckFini == 1) ? " AND aco_termine=".($zlTermine+0) : ""; // des notes terminees ou actives

        if ($rdChExport!="1") {
          $sql  = "SELECT age_id, age_libelle, age_detail, age_prive, age_aty_id, age_date, age_heure_debut, age_heure_fin, age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_couleur, age_date_creation, age_date_modif, age_plage, age_plage_duree, util_semaine_type, age_lieu, age_disponibilite, age_rappel, age_rappel_coeff, age_email, age_cal_id ";
          $sql .= "FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur ";
          $sql .= "WHERE aco_util_id=".$idUser." AND age_id=aco_age_id AND age_aty_id>1 AND age_mere_id=0 AND util_id=age_createur_id";
          $sql .= " AND CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))>='".$date_dem_deb."' AND CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))<='".$date_dem_fin."'".$exclusion;
        } else {
          $sql  = "SELECT age_id, age_libelle, age_detail, age_prive, age_aty_id, age_date, age_heure_debut, age_heure_fin, age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_couleur, age_date_creation, age_date_modif, age_plage, age_plage_duree, util_semaine_type, age_lieu, age_disponibilite, age_rappel, age_rappel_coeff, age_email, age_cal_id ";
          $sql .= "FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur ";
          $sql .= "WHERE aco_util_id=".$idUser." AND age_id=aco_age_id AND age_aty_id>1 AND age_mere_id=0 AND util_id=age_createur_id";
          $sql .= " AND age_date_modif >='".$date_dem_deb."' AND age_date_modif <='".$date_dem_fin."'".$exclusion;
        }
        $DB_CX->DbQuery($sql);

        if ($DB_CX->DbNumRows()) {
          $contenu = "";
          $contenu .= "BEGIN:VCALENDAR\r\n";
          $contenu .= "PRODID:-//Phenix_MR_v1.0\r\n";
          $contenu .= "VERSION:1.0\r\n\r\n";

          // Boucle sur les evenements
          while ($enr = $DB_CX->DbNextRow()) {
            $data = "BEGIN:VEVENT\r\n";
            $data .= "UID:".$enr['age_id']."\r\n";
            $data .= "SUMMARY;ENCODING=QUOTED-PRINTABLE:".$enr['age_libelle']."\r\n";
            if ($enr['age_lieu']!="") $data .= "LOCATION;ENCODING=QUOTED-PRINTABLE:".utf8_encode($enr['age_lieu'])."\r\n";
            if ($ckExportHtml!="1") {
              $dataDescript="";
              if ($enr['age_detail']!="")
                $dataDescript = preg_replace('#\r\n|\n|\r#', '=0D=0A', strip_tags (html_entity_decode($enr['age_detail'])));
              if ($dataDescript !="")
                $data .= "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:".$dataDescript."\r\n";
            } else {
              if ($enr['age_detail']!="")
                $data .= "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:".preg_replace('#\r\n|\n|\r#', '=0D=0A', $enr['age_detail'])."\r\n";
            }
            $data .= "ATTENDEE;ROLE=OWNER;STATUS=CONFIRMED;ENCODING=QUOTED-PRINTABLE:".$prenom_nom."\r\n";
            $data .= "CLASS:".(($enr['age_prive']==1) ? "PRIVATE" : "PUBLIC")."\r\n";
            // Ajout de champs particuliers pour PHENIX : type de note, couleur de l'agenda, rappel, disponibilite, contact associe !!
            $data .= "X-PHENIX-AGENDA-TYPE:".$enr['age_aty_id']."\r\n";
            $cat_couleur ="";
            if ($enr['age_couleur']!="") {
              $data .= "X-PHENIX-AGENDA-COLOR:".$enr['age_couleur']."\r\n";
              reset ($tabCouleur);
              while (list($key, $val) = each($tabCouleur)) {
                if ($val == $enr['age_couleur']) $cat_couleur = $key;
              }
            }
            if ($enr['age_rappel']!=0) $data .= "X-PHENIX-AGENDA-RAPPEL:".$enr['age_rappel']." ".$enr['age_rappel_coeff']." ".$enr['age_email']."\r\n";
            if ($enr['age_disponibilite']!=0) $data .= "X-PHENIX-AGENDA-DISPO:".$enr['age_disponibilite']."\r\n";
            if ($enr['age_cal_id']!=0) $data .= "X-PHENIX-AGENDA-CONTACT:".$enr['age_cal_id']."\r\n";
            // Aucune gestion heure ete/hiver
            list($heure_deb,$min_deb) = explode(".",$enr['age_heure_debut']);
            list($heure_fin,$min_fin) = explode(".",$enr['age_heure_fin']);
            // Date de creation de la note : 2006-10-17 11:08:02
            $date_create = substr($enr['age_date_creation'],0,10);
            $heure_create = substr($enr['age_date_creation'],11,2);
            $min_create = substr($enr['age_date_creation'],14,2);
            if ($date_create =="0000-00-00") {
              $date_create = gmdate("Y-m-d");
            }
            if (($heure_create=="00") && ($min_create=="00")) {
              $heure_create = gmdate('H');
              $min_create = gmdate('i');
            }
            if (strlen($heure_create)==1) $heure_create = "0".$heure_create;
            $data .= "DCREATED:".str_replace("-","",$date_create)."T".$heure_create.$min_create."00Z\r\n";
             // Date de modification de la note : 2006-10-17 11:08:02 -> 20061017T110802
            $date_modif = substr($enr['age_date_modif'],0,10);
            $heure_modif = substr($enr['age_date_modif'],11,2);
            $min_modif = substr($enr['age_date_modif'],14,2);
            if ($date_modif =="0000-00-00") {
              $date_modif = gmdate("Y-m-d");
            }
            if (($heure_modif=="00") && ($min_modif=="00")) {
              $heure_modif = gmdate('H');
              $min_modif = gmdate('i');
            }
            if (strlen($heure_modif)==1) $heure_modif = "0".$heure_modif;
            $data .= "LAST-MODIFIED:".str_replace("-","",$date_modif)."T".$heure_modif.$min_modif."00Z\r\n";
            if (strlen($heure_deb)==1) $heure_deb = "0".$heure_deb;
            if (strlen($heure_fin)==1) $heure_fin = "0".$heure_fin;
            if ($heure_deb < 0) $heure_deb="00";
            if ($heure_fin < 0) $heure_fin="00";
            $min_deb = $min_deb * 60 / 100;
            $min_fin = $min_fin * 60 / 100;
            if (strlen($min_deb)==1) $min_deb = "0".$min_deb;
            if (strlen($min_fin)==1) $min_fin = "0".$min_fin;
            // Decalage de la date de fin si note a cheval
            list($a_deb,$m_deb,$j_deb) = explode ("-",$enr['age_date']);
            $date_fin = ($enr['age_heure_debut']>$enr['age_heure_fin']) ? date("Y-m-d",mktime(0,0,0,$m_deb,$j_deb+1,$a_deb)) : $enr['age_date'];
            // On enregistre les heures de debut et de fin de la note
            if ($enr['age_aty_id']==3) {
              $date_fin = date("Y-m-d",mktime(0,0,0,$m_deb,$j_deb+1,$a_deb));
              $data .= "DTSTART:".str_replace("-","",$enr['age_date'])."T000000Z\r\n";
              $data .= "DTEND:".str_replace("-","",$date_fin)."T000000Z\r\n";
            } else {
              $data .= "DTSTART:".str_replace("-","",$enr['age_date'])."T".$heure_deb.$min_deb."00Z\r\n";
              $data .= "DTEND:".str_replace("-","",$date_fin)."T".$heure_fin.$min_fin."00Z\r\n";
            }

            // insersion categorie
            if ($cat_couleur!="") $data .= "CATEGORIES:".utf8_encode($cat_couleur)."\r\n";
            // Si on traite une note recurrente
            if ($enr['age_ape_id']>1) {
              // On cherche le format de repetition de la note
              if ($enr['age_plage']==2) {
                //Avec une date de fin
                $repetNote = date("Ymd",$enr['age_plage_duree'])."T000000Z";
              } elseif (($enr['age_plage']==1) && ($subTypeExport=="STD")) {
                //Avec un nombre d'occurrence
                $repetNote = "#".$enr['age_plage_duree'];
              } else {
                // Gere le cas specifique de l'export vCal pour PalmDesktop qui ne reconnait pas le #n
                // On cherche la derniere date de repetition de la note
                $res=mysql_query("SELECT MAX(age_date) FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=age_id AND aco_util_id=".$idUser." AND age_mere_id=".$enr['age_id']);
                if (mysql_num_rows($res)) {
                  $repetNote = str_replace("-","",mysql_result($res,0,0))."T000000Z";
                } else {
                  // Si erreur bien que peu probable => on ne gere pas la repetition
                  $repetNote = "";
                }
              }

              // PERIODICITE QUOTIDIENNE
              if ($enr['age_ape_id']==2) {
                // on gere les recurrences quotidiennes mais pas celles lundi->vendredi
                // trop d'exception sam. dim. a gerer en cas de recurrence sur du long terme
                // on cherche la derniere occurrence
                if ($enr['age_periode1']==1) {
                  // Tous les X(age_periode2) jours
                  $data .= trim("RRULE:D".$enr['age_periode2']." ".$repetNote)."\r\n";
                } elseif ($enr['age_periode1']==2) {
                  //Tous les jours ouvrables (Lundi au Vendredi) => on utilise la periodicite hebdomadaire
                  $data .= trim("RRULE:W1 MO TU WE TH FR ".$repetNote)."\r\n";
                }
              }
              //PERIODICITE HEBDOMADAIRE
              elseif ($enr['age_ape_id']==3) {
                // Toutes les X (age_periode1) semaines, chaque Y (age_periode2) jours (Lundi a Dimanche)
                //On commence par regarder si la semaine type est enregistree dans la note au format PHP, sinon on prend celle du CREATEUR de la note en la mappant au format PHP (L->D => D->S)
                $vSemaineType = ($enr['age_periode2']>0) ? $enr['age_periode2'] : substr($enr['util_semaine_type'],6).substr($enr['util_semaine_type'],0,6);
                //Si on a recupere la semaine type dans la note les premiers 0 ne sont pas recuperes a cause du type du champ (INT et pas VARCHAR) => on corrige
                for ($i=strlen($vSemaineType);$i<7;$i++) {
                  $vSemaineType = "0".$vSemaineType;
                }
                if ($vSemaineType!="0000000") {
                  $strByDay = "";
                  for ($i=0; $i<7; $i++) {
                    if (substr($vSemaineType,$i,1)==1)
                      $strByDay .= " ".$jour[$i]; //  0 -> Dimanche ... 6 -> Samedi
                  }
                  $data .= trim("RRULE:W".$enr['age_periode1']." ".substr($strByDay,1)." ".$repetNote)."\r\n";
                }
              }
              //PERIODICITE MENSUELLE
              elseif ($enr['age_ape_id']==4) {
                if ($enr['age_periode1']==1) {
                  // Tous les X (age_periode2) date du jour de chaque mois
                  $data .= trim("RRULE:MD".$enr['age_periode4']." ".$enr['age_periode2']." ".$repetNote)."\r\n";
                } elseif ($enr['age_periode1']==2) {
                  // Tous les premiers X(age_periode2) nom du jour (MO,TU,WE,TH,FR,SA) de chaque mois
                  $rang = ($enr['age_periode2'] + 1)."+";
                  $data .= trim("RRULE:MP".$enr['age_periode4']." ".$rang." ".$jour[$enr['age_periode3']]." ".$repetNote)."\r\n";
                }
              }
              //PERIODICITE ANNUELLE
              elseif ($enr['age_ape_id']==5) {
                // Tous les X(age_periode2) mois
                if ($enr['age_periode1']==1) {
                  // Tous les X(age_periode3) mois
                  $data .= trim("RRULE:YM1 ".$enr['age_periode3']." ".$repetNote)."\r\n";
                }
                // ($enr['age_periode1']==2) n'a pas d'equivalence en vCal => pas de repetition
              }
            }
            if ($enr['age_rappel']!=0) {
              $date_alarm = $enr['age_date']." ".$heure_deb.$min_deb."00";
              $date_alarm = strtotime($date_alarm)-($enr['age_rappel']*$enr['age_rappel_coeff']*60);
              $data .= "DALARM:".date ("Ymd",$date_alarm)."T".date ("His",$date_alarm)."Z\r\n";
            }
            $data .= "END:VEVENT\r\n";
            $data .= "\r\n";
            $contenu .= $data ;
          }
          $contenu .= "END:VCALENDAR";
        } else {
          $contenu = trad("NOTEEXP_AUCUNE_DONNEE");
        }
      }
      //Sauvegarde des parametres d'exportation
      $ckSauvSql +=0;
      if ($ckSauvSql==1) {
        $ckExportHtml +=0;
        $ckExportTz +=0;
        if ($ckFini=="1") {
          $zlTermine++;
        } else {
          $zlTermine="0";
        }
        $DB_CX->DbQuery("SELECT aex_util_id FROM ${PREFIX_TABLE}agenda_export WHERE aex_util_id=".$idUser);
        if ($DB_CX->DbNumRows()) {
          $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda_export SET aex_creation='".$rdChExport."', aex_html='".$ckExportHtml."', aex_tz='".$ckExportTz."', aex_ch_note='".$zlTermine."', aex_note_aff='".$ckAffecte."'". ($rdChExport!="0" ? ", aex_date_old='".$date_dem_fin."'" : "").", aex_type='".$zlTypeFichier."' WHERE aex_util_id='".$idUser."'");
        } else {
          $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_export (aex_util_id,aex_creation,aex_html,aex_tz,aex_ch_note,aex_note_aff". ($rdChExport!="0" ? ",aex_date_old" : "").",aex_type) VALUES (".$idUser.",'".$rdChExport."','".$ckExportHtml."','".$ckExportTz."','".$zlTermine."','".$ckAffecte."'". ($rdChExport!="0" ? ",'".$date_dem_fin."'" : "").",'".$zlTypeFichier."')");
        }
      } else {
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_export WHERE aex_util_id='".$idUser."'");
      }
    }
    //--------------------------------------------------

    // Export CSV -> Outlook
    elseif ($typeExport=="csv") {
      // Recuperation de l'idUser par la verification de la session
      $idUser = Session_ok($sid);
      include("lang/$APPLI_LANGUE.php");

      // Recuperation des infos de timezone de l'utilisateur
      $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$idUser." AND tzn_zone=util_timezone");
      $tzGmt = $DB_CX->DbResult(0,"tzn_gmt");
      $tzDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
      $tzHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
      $tzDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
      $tzHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
      // Calcul des bascules ete/hiver pour la date et l'heure locale
      $tzEte = calculBasculeDST($tzDateEte,gmdate("Y"),$tzHeureEte,$tzGmt,0);
      $tzHiver = calculBasculeDST($tzDateHiver,gmdate("Y"),$tzHeureHiver,$tzGmt,1);

      // Recuperation des bornes d'export choisis
      if ($rdChExport=="1") {
        $ztDateDeb = $ztDateDebC;
        $ztDateFin = $ztDateFinC;
      }
      // Conversion de la date de debut en UTC
      list($j_deb,$m_deb,$a_deb) = explode("/",$ztDateDeb);
      $d_deb = $a_deb."-".$m_deb."-".$j_deb;
      $h_deb = "00.00";
      list($tzEteD,$tzHiverD,$hBascule,$h_deb,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_deb,$h_deb,1);
      $decalHD = calculDecalageH($tzGmt,$tzEteD,$tzHiverD,mktime(floor($h_deb-$hBascule),(($h_deb-$hBascule)*60)%60,0,$m_deb,$j_deb,$a_deb));
      $date_dem_deb = mktime(floor($h_deb-$decalHD),(($h_deb-$decalHD)*60)%60,0,$m_deb,$j_deb,$a_deb);
      // Conversion de la date de fin en UTC
      list($j_fin,$m_fin,$a_fin) = explode("/",$ztDateFin);
      $d_fin = $a_fin."-".$m_fin."-".$j_fin;
      $h_fin = "24.00";
      list($tzEteF,$tzHiverF,$hBascule,$h_fin,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_fin,$h_fin,1);
      $decalHF = calculDecalageH($tzGmt,$tzEteF,$tzHiverF,mktime(floor($h_fin-$hBascule),(($h_fin-$hBascule)*60)%60,0,$m_fin,$j_fin,$a_fin));
      $date_dem_fin = mktime(floor($h_fin-$decalHF),(($h_fin-$decalHF)*60)%60,0,$m_fin,$j_fin,$a_fin);
      if ($rdChExport!="1") {
        $date_dem_deb = date("Y-m-d H",$date_dem_deb).".".sprintf("%02d",round(date("i",$date_dem_deb)*100/60));
        $date_dem_fin = date("Y-m-d H",$date_dem_fin).".".sprintf("%02d",round(date("i",$date_dem_fin)*100/60));
      } else {
        $date_dem_deb = date("Y-m-d H:i:s",$date_dem_deb);
        $date_dem_fin = date("Y-m-d H:i:s",$date_dem_fin);
      }

      // Ajustement de la date en fonction du timezone
      $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(gmdate("H"),gmdate("i"),0,gmdate("n"),gmdate("j"),gmdate("Y")));
      $localTime = mktime(gmdate("H")+floor($decalageHoraire),gmdate("i")+($decalageHoraire*60)%60,gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));

      //Nom du fichier d'export
      $fileName = "Export_agenda_csv_".date("Ymd-His",$localTime).".csv";

      $DB_CX->DbQuery("SELECT CONCAT(".$FORMAT_NOM_UTIL.") FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
      if (!$DB_CX->DbNumRows()) {
        $contenu = sprintf(trad("NOTEEXP_ECHEC_CREATION"), $sql);
      } else {
        $prenom_nom = $DB_CX->DbResult(0,0);

        // Exclusion
        $exclusion  = ($ckAffecte == 1) ? " AND age_util_id=".$idUser : ""; //  des notes affectees
        $exclusion .= ($ckFini == 1) ? " AND aco_termine=".($zlTermine+0) : ""; // des notes terminees ou actives

        if ($rdChExport!="1") {
          $sql  = "SELECT age_id, age_libelle, age_detail, age_prive , age_aty_id, age_date, age_heure_debut, age_heure_fin, age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_couleur, age_date_creation ";
          $sql .= "FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne ";
          $sql .= "WHERE aco_age_id=age_id AND aco_util_id=".$idUser." AND age_aty_id>1 AND age_mere_id=0";
          $sql .= " AND CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))>='".$date_dem_deb."' AND CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))<='".$date_dem_fin."'".$exclusion;
        } else {
          $sql  = "SELECT age_id, age_libelle, age_detail, age_prive , age_aty_id, age_date, age_heure_debut, age_heure_fin, age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_couleur, age_date_creation ";
          $sql .= "FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne ";
          $sql .= "WHERE aco_age_id=age_id AND aco_util_id=".$idUser." AND age_aty_id>1 AND age_mere_id=0";
          $sql .= " AND age_date_modif >='".$date_dem_deb."' AND age_date_modif <='".$date_dem_fin."'".$exclusion;
        }
        $DB_CX->DbQuery($sql);

        if ($DB_CX->DbNumRows()) {
          $contenu .= trad("NOTEEXP_CSV_CHAMPS");
          // Boucle sur les evenements
          while ($enr = $DB_CX->DbNextRow()) {
            $Objet=$Debut_date=$Debut_heure=$Fin_date=$Fin_heure=$Journee_ent=$Rappel_act_inact=$Date_rap=$Heure_rap=$Organisateur=$Part_ob=$Part_fac=$Ressources=$Afficher_dispo=$Categories=$Critere_diff=$Description=$Emplacement=$Informations_fact=$Kilometrage=$Priorite=$Prive="";
            $Objet = str_replace('"','""',$enr['age_libelle']);
            if ($ckExportHtml!="1") {
              $dataDescript="";
              if ($enr['age_detail']!="")
                $dataDescript = str_replace('"','""', strip_tags (html_entity_decode($enr['age_detail'])));
              if ($dataDescript!="")
                $data .= $dataDescript."\r\n";
            } else {
              if ($enr['age_detail']!="")
                $Description = str_replace('"','""',$enr['age_detail'])."\r\n";
            }
            $Prive = ($enr['age_prive']==1) ? "Vrai" : "Faux";
            $Journee_ent = ($enr['age_aty_id']==3) ? "Vrai" : "Faux";
            $Organisateur=$prenom_nom;

            //Decalage des notes en fonction du fuseau horaire
            list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],$enr['age_date']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],1,0,1);
            // On met la date de debut et de fin au bon format
            $Debut_heure = afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],"H:i:s");
            $Fin_heure = afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],"H:i:s");

            list($a_deb,$m_deb,$j_deb) = explode ("-",$enr['age_date']);
            $Debut_date = $j_deb."/".$m_deb."/".$a_deb;
            $Fin_date = ($enr['age_heure_debut']>$enr['age_heure_fin']) ? date("d/m/Y",mktime(0,0,0,$m_deb,$j_deb+1,$a_deb)) : $Debut_date;

            $Rappel_act_inact="Faux";
            $contenu .= "\"".$Objet."\",\"".$Debut_date."\",\"".$Debut_heure."\",\"".$Fin_date."\",\"".$Fin_heure."\",\"".$Journee_ent."\",\"".$Rappel_act_inact."\",\"".$Date_rap."\",\"".$Heure_rap."\",\"".$Organisateur."\",\"".$Part_ob."\",\"".$Part_fac."\",\"".$Ressources."\",\"".$Afficher_dispo."\",\"".$Categories."\",\"".$Critere_diff."\",\"".$Description."\",\"".$Emplacement."\",\"".$Informations_fact."\",\"".$Kilometrage."\",\"".$Priorite."\",\"".$Prive."\"\r\n";
          }
        } else {
          $contenu = trad("NOTEEXP_AUCUNE_DONNEE");
        }
      }
      //Sauvegarde des parametres d'exportation
      $ckSauvSql +=0;
      if ($ckSauvSql==1) {
        $ckExportHtml +=0;
        $ckExportTz +=0;
        if ($ckFini=="1") {
          $zlTermine++;
        } else {
          $zlTermine="0";
        }
        $DB_CX->DbQuery("SELECT aex_util_id FROM ${PREFIX_TABLE}agenda_export WHERE aex_util_id=".$idUser);
        if ($DB_CX->DbNumRows()) {
          $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda_export SET aex_creation='".$rdChExport."', aex_html='".$ckExportHtml."', aex_tz='".$ckExportTz."', aex_ch_note='".$zlTermine."', aex_note_aff='".$ckAffecte."'". ($rdChExport!="0" ? ", aex_date_old='".$date_dem_fin."'" : "").", aex_type='".$zlTypeFichier."' WHERE aex_util_id='".$idUser."'");
        } else {
          $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_export (aex_util_id,aex_creation,aex_html,aex_tz,aex_ch_note,aex_note_aff". ($rdChExport!="0" ? ",aex_date_old" : "").",aex_type) VALUES (".$idUser.",'".$rdChExport."','".$ckExportHtml."','".$ckExportTz."','".$zlTermine."','".$ckAffecte."'". ($rdChExport!="0" ? ",'".$date_dem_fin."'" : "").",'".$zlTypeFichier."')");
        }
      } else {
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_export WHERE aex_util_id='".$idUser."'");
      }
    }
    //--------------------------------------------------

    else {
      $contenu="Format d'export non reconnu !";
    }

    // Envoi de l'entete adequate
    switch ($typeExport) {
      case "ics":
        header('Content-Type: text/calendar');
        break;
      case "vcs":
        header('Content-Type: text/x-vCalendar');
        break;
      case "csv":
        header('Content-Type: text/csv');
        break;
      default :
        header('Content-Type: text/plain');
    }
    header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
    if (preg_match('@MSIE ([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
      header('Content-Disposition: inline; filename="'.$fileName.'"');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
    } else {
      header('Content-Disposition: attachment; filename="'.$fileName.'"');
      header('Pragma: no-cache');
    }
    echo $contenu;
  }

  // Generation du module d'export dans PHENIX
  else {
    //Date de debut et de fin en fonction des notes de l'utilisateur
    $DB_CX->DbQuery("SELECT MIN(CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))), MAX(CONCAT(age_date,' ',RIGHT(CONCAT('0',age_heure_debut),5))) FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND age_aty_id>1 AND age_mere_id=0");
    if ($DB_CX->DbResult(0,0)!=NULL) {
      $ztDateDeb = $DB_CX->DbResult(0,0);
      $ztDateFin = $DB_CX->DbResult(0,1);
      // Conversion de la date de debut en Local
      $tabDate = explode(" ",$ztDateDeb);
      list($a_deb,$m_deb,$j_deb) = explode("-",$tabDate[0]);
      list($tzEte,$tzHiver,$hBascule,$tabDate[1],$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$tabDate[0],$tabDate[1],0);
      $decalHD = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(floor($tabDate[1]-$hBascule),(($tabDate[1]-$hBascule)*60)%60,0,$m_deb,$j_deb,$a_deb));
      $ztDateDeb = date("d/m/Y",mktime(floor($tabDate[1]+$decalHD),(($tabDate[1]+$decalHD)*60)%60,0,$m_deb,$j_deb,$a_deb));
      // Conversion de la date de fin en Local
      $tabDate = explode(" ",$ztDateFin);
      list($a_fin,$m_fin,$j_fin) = explode("-",$tabDate[0]);
      list($tzEte,$tzHiver,$hBascule,$tabDate[1],$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$tabDate[0],$tabDate[1],0);
      $decalHF = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(floor($tabDate[1]-$hBascule),(($tabDate[1]-$hBascule)*60)%60,0,$m_fin,$j_fin,$a_fin));
      $ztDateFin = date("d/m/Y",mktime(floor($tabDate[1]+$decalHF),(($tabDate[1]+$decalHF)*60)%60,0,$m_fin,$j_fin,$a_fin));
    } else {
      $ztDateDeb = date("d/m/",$localTime).(date("Y",$localTime)-5);
      $ztDateFin = date("d/m/",$localTime).(date("Y",$localTime)+5);
    }
    // Recuperation des parametres de sauvegarde precedents
    $ckSauvSql=0;
    $DB_CX->DbQuery("SELECT aex_html, aex_tz, aex_creation, aex_type, aex_ch_note, aex_note_aff , DATE_FORMAT(aex_date_old,'%d/%m/%Y') FROM ${PREFIX_TABLE}agenda_export WHERE aex_util_id=".$idUser);
    if ($DB_CX->DbNumRows()) {
      $ckExportHtml = $DB_CX->DbResult(0,0);
      $ckExportTz = $DB_CX->DbResult(0,1);
      $rdChExport = $DB_CX->DbResult(0,2);
      $zlType = $DB_CX->DbResult(0,3);
      $ckNote = $DB_CX->DbResult(0,4);
      $ckNoteAff = $DB_CX->DbResult(0,5);
      $ztDateDebC = $DB_CX->DbResult(0,6);
      $ckSauvSql++;
      // Conversion de la date de debut en Local
      if ($ztDateDebC!="00/00/0000") {
        $tabDate = explode(" ",$ztDateDebC);
        $tabHeure = explode(":",$tabDate[1]);
        $tabDate[1] = $tabHeure[0].".".sprintf("%02d",round($tabHeure[1]*100/60));
        list($j_deb,$m_deb,$a_deb) = explode("/",$tabDate[0]);
        list($tzEteD,$tzHiverD,$hBascule,$tabDate[1],$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$tabDate[0],$tabDate[1],0);
        $decalHD = calculDecalageH($tzGmt,$tzEteD,$tzHiverD,mktime(floor($tabDate[1]-$hBascule),(($tabDate[1]-$hBascule)*60)%60,$tabHeure[2],$m_deb,$j_deb,$a_deb));
        $ztDateDebC = date("d/m/Y",mktime(floor($tabDate[1]+$decalHD),(($tabDate[1]+$decalHD)*60)%60,$tabHeure[2],$m_deb,$j_deb,$a_deb));
      }
    }
    $ztDateFinC = date("d/m/Y",$localTime);
    if (($ztDateDebC=="00/00/0000") || ($ckSauvSql=="0")) {
      $MessAff=1;
      $ztDateDebC = $ztDateDeb;
    }
?>
<!-- DEBUT MODULE EXPORT NOTES -->
<!--  Mod Aide  -->
<!-- Fichier d'aide contextuel  -->
  <SCRIPT> HelpPhenixCtx="{4B71BC68-9EE3-426C-AD3B-9CD2F3323B78}.htm"; </SCRIPT>
<!-- Mod Aide  -->
  <STYLE type="text/css">@import url(css/calendar_css.php?id=<?php echo $APPLI_STYLE; ?>);</STYLE>
  <SCRIPT type="text/javascript" src="inc/calendar.js"></SCRIPT>
  <SCRIPT type="text/javascript">
    // Permet de d'afficher les details de chaque choix de l'export
    function affTypeExport(_Ch) {
      var t1 = document.getElementById('ExportDate');
      var t2 = document.getElementById('ExportModif');
      if (_Ch == '0') {
        t2.style.display = "none";
        t1.style.display = "block";
      } else {
        t1.style.display = "none";
        t2.style.display = "block";
      }
    }
  //-->
</SCRIPT>
<?php
  include("inc/calendar-setup.js.php");
  include("inc/checkdate.js.php");
?>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD height="28" class="sousMenu"><?php echo trad("NOTEEXP_TITRE_EXPORT");?></TD>
  </TR>
  </TABLE><BR>

  <FORM name="FormExport" method="POST" action="agenda_note_export.php">
    <INPUT type="hidden" name="sid" value="<?php echo $sid; ?>">
    <INPUT type="hidden" name="tcMenu" value="<?php echo $tcPlg; ?>">
    <INPUT type="hidden" name="tcType" value="<?php echo $tcType; ?>">
    <INPUT type="hidden" name="sd" value="<?php echo $sd; ?>">
    <TABLE width="600" border="0" cellpadding="0" cellspacing="0">
    <TR>
      <TD align="center"><TABLE cellspacing="0" cellpadding="0" border="0" width="100%">
        <TR bgcolor="<?php echo $CalepinFondMessage; ?>">
          <TD align="left" colspan="2" class="bordTLRB" style="padding-left:3px;padding-right:3px;"><?php echo trad("NOTEEXP_ACCUEIL_TEXTE");?>
          </TD>
        </TR>
        <TR bgcolor="<?php echo $CalepinFondMessage; ?>">
          <TD align="left" colspan="2" class="bordLRB" style="padding-left:3px;padding-right:3px;">
            <BR><?php echo trad("NOTEEXP_ACCUEIL_CHOIX_DATE");?>&nbsp;&nbsp;
            <LABEL for="ChExportD"><INPUT type="radio" name="rdChExport" id="ChExportD" value="0" class="Case" onClick="affTypeExport(this.value)" <?php echo ($rdChExport!="1" ? "checked >&nbsp;" : ">&nbsp;").trad("NOTEEXP_ACCUEIL_DATE");?></LABEL>&nbsp;&nbsp;
            <LABEL for="ChExportC"><INPUT type="radio" name="rdChExport" id="ChExportC" value="1" class="Case" onClick="affTypeExport(this.value)"<?php echo ($rdChExport=="1" ? "checked >&nbsp;" : ">&nbsp;").trad("NOTEEXP_ACCUEIL_MODIF");?></LABEL><BR><BR>
            <LABEL for="HtmlExport"><INPUT type="checkbox" name="ckExportHtml" value="1" <?php if ($ckExportHtml==1) echo " checked"; ?> class="case" id="HtmlExport">&nbsp;<?php echo trad("NOTEEXP_ACCUEIL_HTML");?></LABEL><BR><BR>
            <LABEL for="TzExport"><INPUT type="checkbox" name="ckExportTz" value="1"<?php if ($ckExportTz==1) echo " checked"; ?> class="case" id="TzExport">&nbsp;<?php echo trad("NOTEEXP_ACCUEIL_TZ");?></LABEL><BR><BR>
            <U><?php echo trad("NOTEEXP_ACCUEIL_PERIODE");?></U> :<BR><BR>
            <DIV id="ExportDate" style="display:<?php echo ($rdChExport!="1" ? "block" : "none"); ?>">
            <TABLE width="100%">
            <TR>
              <TD nowrap><B><?php echo trad("NOTEEXP_ACCUEIL_DATE_DEB");?> :</B>&nbsp;</TD>
              <TD><INPUT type="text" class="Texte" name="ztDateDeb" id="ztDateDeb" size=12 maxlength=10 value="<?php echo $ztDateDeb; ?>" title="<?php echo trad("NOTEEXP_ACCUEIL_DATE_FORMAT");?>" onKeyPress="return onlyChar(event);">&nbsp;<INPUT type="button" id="btCal" value="..." class="Picklist" style="height:16px" title="<?php echo trad("NOTEEXP_ACCUEIL_AFFICHE_CALENDRIER");?>"></TD>
              <TD rowspan="2" align="center"><?php echo trad("NOTEEXP_ACCUEIL_INFO");?></TD>
            </TR>
            <TR>
              <TD><B><?php echo trad("NOTEEXP_ACCUEIL_DATE_FIN");?> :</B></TD>
              <TD><INPUT type="text" class="Texte" name="ztDateFin" id="ztDateFin" size=12 maxlength=10 value="<?php echo $ztDateFin; ?>" title="<?php echo trad("NOTEEXP_ACCUEIL_DATE_FORMAT");?>" onKeyPress="return onlyChar(event);">&nbsp;<INPUT type="button" id="btCal2" value="..." class="Picklist" style="height:16px" title="<?php echo trad("NOTEEXP_ACCUEIL_AFFICHE_CALENDRIER");?>"></TD>
            </TR>
            </TABLE><BR></DIV>
            <DIV id="ExportModif" style="display:<?php echo ($rdChExport!="1" ? "none" : "block"); ?>">
            <TABLE width="100%">
            <TR>
              <TD nowrap><B><?php echo trad("NOTEEXP_ACCUEIL_DATE_DEB");?> :</B>&nbsp;</TD>
              <TD><INPUT type="text" class="Texte" name="ztDateDebC" id="ztDateDebC" size=12 maxlength=10 value="<?php echo $ztDateDebC; ?>" title="<?php echo trad("NOTEEXP_ACCUEIL_DATE_FORMAT");?>" onKeyPress="return onlyCharC(event);">&nbsp;<INPUT type="button" id="btCal4" value="..." class="Picklist" style="height:16px" title="<?php echo trad("NOTEEXP_ACCUEIL_AFFICHE_CALENDRIER");?>"></TD>
              <TD rowspan="2" align="center"><?php echo (($MessAff != 1) ? trad("NOTEEXP_ACCUEIL_INFO_CREATE") : trad("NOTEEXP_ACCUEIL_INFO")); ?></TD>
            </TR>
            <TR>
              <TD><B><?php echo trad("NOTEEXP_ACCUEIL_DATE_FIN");?> :</B></TD>
              <TD><INPUT type="text" class="Texte" name="ztDateFinC" id="ztDateFinC" size=12 maxlength=10 value="<?php echo $ztDateFinC; ?>" title="<?php echo trad("NOTEEXP_ACCUEIL_DATE_FORMAT");?>" onKeyPress="return onlyCharC(event);">&nbsp;<INPUT type="button" id="btCal6" value="..." class="Picklist" style="height:16px" title="<?php echo trad("NOTEEXP_ACCUEIL_AFFICHE_CALENDRIER");?>"></TD>
            </TR>
            </TABLE><BR></DIV>
            <TABLE width="100%">
            <TR>
              <TD><B><?php echo trad("NOTEEXP_ACCUEIL_EXCLURE");?> :</B></TD>
              <TD colspan="2"><LABEL for="saufFini"><INPUT type="checkbox" name="ckFini" id="saufFini" value="1" class="Case"<?php echo ($ckNote!=0 ? " checked>&nbsp;" : ">&nbsp;").trad("NOTEEXP_ACCUEIL_NOTES");?> </LABEL><SELECT name="zlTermine" size="1" onFocus="document.FormExport.ckFini.checked='true';"><OPTION value="0"<?php echo ($ckNote==1 ? " selected >" : ">").trad("NOTEEXP_ACCUEIL_TERMINEES");?></OPTION><OPTION value="1"<?php echo ($ckNote==2 ? " selected >" : ">").trad("NOTEEXP_ACCUEIL_ACTIVES");?></OPTION></SELECT>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<LABEL for="saufAffecte"><INPUT type="checkbox" name="ckAffecte" id="saufAffecte" value="1" class="Case"<?php echo ($ckNoteAff=="1" ? " checked >&nbsp;" : ">&nbsp;").trad("NOTEEXP_ACCUEIL_NOTES_AFFECTEES");?></LABEL></TD>
            </TR>
            </TABLE><BR>
          </TD>
        </TR>
        <TR bgcolor="<?php echo $bgColor[1]; ?>">
          <TD width="125" class="tabIntitule">&nbsp;<B><?php echo trad("NOTEEXP_ACCUEIL_TYPE_TITRE");?> : </B></TD>
          <TD width="475" class="tabInput"><SELECT name="zlTypeFichier" size="1">
            <OPTION value="init"<?php echo ($zlType=="init" ? " selected>" : ">").trad("NOTEEXP_ACCUEIL_TYPE_TITRE2");?></OPTION>
            <OPTION value="icsSUN"<?php echo ($zlType=="icsSUN" ? " selected>" : ">").trad("NOTEEXP_ACCUEIL_TYPE_ICAL1");?></OPTION>
            <OPTION value="icsMS"<?php echo ($zlType=="icsMS" ? " selected>" : ">").trad("NOTEEXP_ACCUEIL_TYPE_ICAL2");?></OPTION>
            <OPTION value="vcsSTD"<?php echo ($zlType=="vcsSTD" ? " selected>" : ">").trad("NOTEEXP_ACCUEIL_TYPE_VCAL1");?></OPTION>
            <OPTION value="vcsPALM"<?php echo ($zlType=="vcsPALM" ? " selected>" : ">").trad("NOTEEXP_ACCUEIL_TYPE_VCAL2");?></OPTION>
            <OPTION value="csv"<?php echo ($zlType=="csv" ? " selected>" : ">").trad("NOTEEXP_ACCUEIL_TYPE_CSV");?></OPTION>
          </SELECT></TD>
        </TR>
      </TABLE></TD>
    </TR>
    <TR>
      <TD colspan="2" align="center"><BR><INPUT type="button" class="bouton" name="btExporter" value="<?php echo trad("NOTEEXP_ACCUEIL_BOUTON_EXPORTER");?>" title="<?php echo trad("NOTEEXP_ACCUEIL_BOUTON_EXPORTER");?>" style="width:65px;" onclick="javascript: if (document.forms.FormExport.zlTypeFichier.value=='init') alert('<?php echo trad("NOTEEXP_ACCUEIL_JS_CHOIX_TYPE");?>'); else document.forms.FormExport.submit();">
        &nbsp;&nbsp;&nbsp;&nbsp;<LABEL for="SauvSql"><INPUT type="checkbox" name="ckSauvSql" value="1" class="Case" id="SauvSql"<?php if ($ckSauvSql==1) echo " checked"; ?>><FONT color="<?php echo $AgendaLegende; ?>">&nbsp;<?php echo trad('NOTEEXP_ACCUEIL_SAUV_SQL'); ?></FONT></LABEL>
      </TD>
    </TR>
    </TABLE>
  </FORM>

  <SCRIPT type="text/javascript">
  <!--
    Calendar.setup( {
      inputField : "ztDateDeb",    // ID of the input field
      ifFormat   : "%d/%m/%Y",     // the date format
      button     : "btCal"         // ID of the button
    } );

    Calendar.setup( {
      inputField : "ztDateFin",    // ID of the input field
      ifFormat   : "%d/%m/%Y",     // the date format
      button     : "btCal2"        // ID of the button
    } );
    Calendar.setup( {
      inputField : "ztDateDebC",   // ID of the input field
      ifFormat   : "%d/%m/%Y",     // the date format
      button     : "btCal4"        // ID of the button
    } );

    Calendar.setup( {
      inputField : "ztDateFinC",   // ID of the input field
      ifFormat   : "%d/%m/%Y",     // the date format
      button     : "btCal6"        // ID of the button
    } );

    //N'autorise que [0-9] / et : comme saisie
    function onlyCharC(ev) {
      ev || (ev=window.event);
      if ((ev.keyCode < 47) || (ev.keyCode > 58)) {
        ev.returnValue=false;
      }
      if ((ev.which < 47) || (ev.which > 58)) {
        return (false);
      }
      return (true);
    }
  //-->
  </SCRIPT>
<!-- FIN MODULE EXPORT NOTES -->
<?php
  }
?>
