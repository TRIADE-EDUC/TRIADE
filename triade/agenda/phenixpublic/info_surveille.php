<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Meteo_today_ico.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_horoscope_hebdo.txt ?>
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

  require("inc/nocache.inc.php");
  include("inc/param.inc.php");
  include("inc/html.inc.php");
  include("lang/$APPLI_LANGUE.php");
  include("inc/fonctions.inc.php");
  include('inc/class.mailer.php');
  if (isset($_GET['sid'])) {
    $idUser = Session_ok($sid);
    $refreshPage = "<META http-equiv=\"REFRESH\" content=\"60; url=info_surveille.php?sid=".$sid."\">\n  ";
  } else {
    // Version "standalone" du fichier qui permet de l'appeler via la crontab Linux ou des sites tels que Webcron ou CronJobs
    $idUser = 0;
    $APPLI_STYLE = "Petrole";
    $refreshPage = "";
  }

  // Initialisations
  $nbRappel  = $nbMail = 0;
  $classMailerLoaded = $classSMTPLoaded = false;

  // Creation d'une nouvelle instance pour l'execution de requetes en boucle
  $DB = new Db($DB_CX->ConnexionID);
  // MOD meteo
  $delai_maj = 4;
  $url_ok=0;
  $go_maj = 0;
  $v=0;
  $datej = date("m/j/y");
  if ($idUser == 0) {
    // on met à jour toutes les villes de la base meteo
    $DB_CX->DbQuery("SELECT DISTINCT LEFT(util_meteo_code,8) FROM ${PREFIX_TABLE}utilisateur WHERE util_meteo_code!='' group by util_meteo_code");
    while ($enr = $DB_CX->DbNextRow()) {
    $villes[$v] = $enr[0];
    $v++;
    }
    $DB_CX->DbQuery("SELECT valeur FROM ${PREFIX_TABLE}configuration where param='MET_DATE_MAJ'");
    $met_date_maj = $DB_CX->DbResult(0,0);  
  }
  else {
    $v=1; // on ne met à jour que la ville de l'utilisateur
    $DB_CX->DbQuery("SELECT util_meteo_code FROM ${PREFIX_TABLE}utilisateur where util_id='".$idUser."'");
    list($villes[0]) = explode(";",$DB_CX->DbResult(0,0));
    $DB_CX->DbQuery("SELECT  met_date_maj FROM ${PREFIX_TABLE}meteo where  met_code_ville='".$villes[0]."'");
    $met_date_maj = $DB_CX->DbResult(0,0);    
  }  
  if ($met_date_maj != "") {
    list($met_date_maj,$met_heure_maj) = explode(" ",$met_date_maj);
    if ((date(G)-$met_heure_maj)>=$delai_maj) $go_maj = 1;
  elseif ($met_date_maj!=date("m/j/y")) $go_maj = 1; 
    else $go_maj = 0; 
  }
  if (($go_maj) || ($met_date_maj == "")) {
    if ($idUser == 0) $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}meteo");
  else $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}meteo WHERE met_code_ville='".$villes[0]."'");
    for ($i=0;$i<$v;$i++) {  
    $weather = @file ('http://xml.weather.com/weather/local/'.$villes[$i].'?cc=*&unit=s&dayf=7'); 
    if ($weather) {
      echo "Ajout ou MAJ météo pour ".$villes[$i]."<br>";
    $meteo_j = array();
    $dayf_ok = 0;
    $meteo_jid = -1;
    foreach ($weather as $line_num => $line) {
      $line = explode(">",$line);
      $line[0] = substr(trim($line[0]),1);
      $line[1] = substr(trim($line[1]),0,strlen(trim($line[1]))-strlen($line[0])-2);
      if (strpos($line[0], "obst") === 0) {
      $ville = htmlspecialchars($line[1]);
      }
      if (strpos($line[0], "dayf") === 0) {
        $dayf_ok = 1;
      }
      if ((strpos($line[0], "lsup") === 0) && ($dayf_ok==1)) {
        $date_meteo_lib = trim(htmlspecialchars($line[1]));
        $date_meteo = substr(trim(htmlspecialchars($line[1])),0,8);
      }
      if (strpos($line[0],'day d=') === 0) {
        $meteo_jid++;  
        $hi_ok = 0;
        $low_ok = 0;
        $part_d_ok = 0;
        $part_n_ok = 0;   
      $sunr_ok = 0;
      $suns_ok = 0;
      $wind_ok=0;
      list(,$meteo_j[$meteo_jid.'_day']) = explode('dt="',$line[0]);
      $meteo_j[$meteo_jid.'_day'] = substr($meteo_j[$meteo_jid.'_day'],0,strlen($meteo_j[$meteo_jid.'_day'])-1);
      }
      if (strpos($line[0], 'part p="n"') === 0) {
        $part_n_ok = 1;
      $part_d_ok = 0;
      }    
      if (strpos($line[0], 'part p="d"') === 0) {
        $part_n_ok = 0;
      $part_d_ok = 1;
      }        
      if ((strpos($line[0], "hi") === 0) && ($hi_ok==0)) {
        $meteo_j[$meteo_jid.'_soir'] = $line[1];
        $hi_ok = 1;
      }  
      if ((strpos($line[0], "low") === 0) && ($low_ok==0)) {
        $meteo_j[$meteo_jid.'_matin'] = $line[1];
        $low_ok = 1;
      }    
      if ((strpos($line[0], 'icon') === 0) && ($part_d_ok==1)) {
        $meteo_j[$meteo_jid.'_matin_icone'] = $line[1];
      }    
      if ((strpos($line[0], 'icon') === 0) && ($part_n_ok==1)) {
        $meteo_j[$meteo_jid.'_soir_icone'] = $line[1];
      }  
      if ((strpos($line[0], 'sunr') === 0) && ($sunr_ok==0)) {
        $meteo_j[$meteo_jid.'_sunr'] = trim(substr($line[1],0,5));
      $sunr_ok=1;
      }        
      if ((strpos($line[0], 'suns') === 0) && ($suns_ok==0)) {
        list($suns_h,$suns_m) = explode(':',substr($line[1],0,5));
        $meteo_j[$meteo_jid.'_suns'] = ($suns_h+12).":".trim($suns_m); // on le met au format 24h
      $suns_ok=1;
      }  
      if ((strpos($line[0], 'ppcp') === 0) && ($part_d_ok==1)) {
        $meteo_j[$meteo_jid.'_matin_p'] = $line[1];
      }  
      if ((strpos($line[0], 'ppcp') === 0) && ($part_n_ok==1)) {
        $meteo_j[$meteo_jid.'_soir_p'] = $line[1];
      }  
      if ((strpos($line[0], 'hmid') === 0) && ($part_d_ok==1)) {
        $meteo_j[$meteo_jid.'_matin_h'] = $line[1];
      }  
      if ((strpos($line[0], 'hmid') === 0) && ($part_n_ok==1)) {
        $meteo_j[$meteo_jid.'_soir_h'] = $line[1];
      }    
      if ((strpos($line[0], 'wind') === 0) && ($part_d_ok==1)) {
        $wind_ok=1;
      }      
      if ((strpos($line[0], 's') === 0) && ($wind_ok==1) && ($part_d_ok==1)) {
        $meteo_j[$meteo_jid.'_matin_vent'] = $line[1];
        $wind_ok=0;
      }  
      if ((strpos($line[0], 'wind') === 0) && ($part_n_ok==1)) {
        $wind_ok=1;
      }      
      if ((strpos($line[0], 's') === 0) && ($wind_ok==1) && ($part_n_ok==1)) {
        $meteo_j[$meteo_jid.'_soir_vent'] = $line[1];
        $wind_ok=0;
      }        
    }  
    for ($j=0;$j<7;$j++) {
      $met_inf_j[$j] = $meteo_j[$j.'_day'].";".$meteo_j[$j.'_matin'].";".$meteo_j[$j.'_soir'].";".$meteo_j[$j.'_matin_icone'].";".$meteo_j[$j.'_soir_icone'].";".$meteo_j[$j.'_sunr'].";".$meteo_j[$j.'_suns'].";".$meteo_j[$j.'_matin_p'].";".$meteo_j[$j.'_soir_p'].";".$meteo_j[$j.'_matin_h'].";".$meteo_j[$j.'_soir_h'].";".$meteo_j[$j.'_matin_vent'].";".$meteo_j[$j.'_soir_vent'];
    }
    echo $met_inf_j[0]."<BR>";
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}meteo VALUES ('".$villes[$i]."','".$ville."','".$date_meteo_lib."','".date("m/j/y")." ".date(G)."','".$met_inf_j[0]."','".$met_inf_j[1]."','".$met_inf_j[2]."','".$met_inf_j[3]."','".$met_inf_j[4]."','".$met_inf_j[5]."','".$met_inf_j[6]."')");
    }
      else echo "Erreur de connexion sur le serveur météo pour la ville : ".$villes[$i]."<br>";
  }
  $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}configuration SET valeur='".date("m/j/y")." ".date(G)."'WHERE param='MET_DATE_MAJ'");
  }
  echo "<BR>";  
  // fin mod meteo
  // MOD horoscope v1.1
  function unhtmlspecialchars( $string )
  {
    $string = str_replace ( '&amp;', '&', $string );
    $string = str_replace ( '&#039;', '\'', $string );
    $string = str_replace ( '&quot;', '"', $string );
    $string = str_replace ( '&lt;', '<', $string );
    $string = str_replace ( '&gt;', '>', $string );
    return $string;
  }  
  $url_ok=0;
  $go_maj = 0;
  $detail = "";
  $s=0;
  $datej = date("m/j/y");
  if ($idUser == 0) {
    // on met à jour toutes les infos horoscope
    $DB_CX->DbQuery("SELECT DISTINCT util_horo FROM ${PREFIX_TABLE}utilisateur WHERE util_horo!='' group by util_horo");
    while ($enr = $DB_CX->DbNextRow()) {
    $signe[$s] = $enr[0];
    $s++;
    }
    $DB_CX->DbQuery("SELECT valeur FROM ${PREFIX_TABLE}configuration where param='HORO_DATE_MAJ'");
    $horo_date_maj = $DB_CX->DbResult(0,0);  
  }
  else {
    $s=1; // on ne met à jour que la ville de l'utilisateur
    $DB_CX->DbQuery("SELECT util_horo FROM ${PREFIX_TABLE}utilisateur where util_id='".$idUser."'");
    $signe[0] = $DB_CX->DbResult(0,0);
    $DB_CX->DbQuery("SELECT  horo_date_maj FROM ${PREFIX_TABLE}horoscope where horo_signe='".$signe[0]."'");
    $horo_date_maj = $DB_CX->DbResult(0,0);    
  }  
  if ($horo_date_maj != $datej)
    $go_maj = 1; 
  else $go_maj = 0; 
  if (($go_maj) || ($horo_date_maj == "")) {
    if ($idUser == 0) $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}horoscope");
  else $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}horoscope WHERE horo_signe='".$signe[0]."'");
    for ($i=0;$i<$s;$i++) {  
    $horoscope = @file ('http://www.asiaflash.com/horoscope/rss_horojour_'.$signe[$i].'.xml'); 
    if ($horoscope) {
    $desc = 0;
    $center = 0;
    foreach ($horoscope as $line_num => $line) {
      $line = unhtmlspecialchars($line);    
      if (strpos($line,'http://www.asiaflash.com/horoscope-') != false)
      $desc = 0;        
          if (strpos($line,"<description") != false) {
       $desc++;
       }
       if ($desc=="2") {
        if (strpos($line,"http://www.asiaflash.com/anh") != false) {
        $line = str_replace("http://www.asiaflash.com/anh","image/horoscope",$line);
        $line = str_replace("<br/>","",$line);
      }  
      if ($line == "<br>") $line = "";
      $line = str_replace("<br><br>","<br>",$line);
      $line = str_replace("\r\n"," ",$line);
        $detail[$i] .= rtrim($line);    
       }
    }  
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}horoscope VALUES ('".$signe[$i]."','".addslashes($detail[$i])."','".date("m/j/y")."')");
    }
      else echo "Erreur de connexion sur le serveur horoscope pour le signe : ".$signe[$i]."<br>";
  }
  $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}configuration SET valeur='".date("m/j/y")."'WHERE param='HORO_DATE_MAJ'");
  }
  echo "<BR>";
  // fin mod horoscope

  function gereRappel($typeRappel) {
    global $nbRappel, $nbMail, $PREFIX_TABLE, $DB_CX, $DB;

    $noteID = 0;
    $sujetMail = $corpsMail = "";
    $destMail = array();
    // Requete a executer en fonction du type de rappel a notifier
    if ($typeRappel==_RAPPEL_NOTE) {
      $sql=("SELECT age_id AS id, age_util_id AS idEmetteur, DATE_FORMAT(age_date,'%d/%m/%Y') AS dateEvent, age_heure_debut, age_libelle, age_detail, age_lieu, age_rappel, age_rappel_coeff, age_email AS envoiMail, aco_util_id AS idDestinataire, dest.util_email AS destEmail, CONCAT(exp.util_prenom,' ',exp.util_nom) AS expNom, exp.util_email AS expEmail, age_email_contact, cal_email, cal_emailpro, CONCAT(cal_prenom,' ',cal_nom) AS nomContact, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur dest, ${PREFIX_TABLE}utilisateur exp, ${PREFIX_TABLE}timezone WHERE aco_rappel_ok=0 AND aco_termine=0 AND age_id=aco_age_id AND (age_aty_id=2 OR age_aty_id=3) AND age_rappel>0 AND TO_DAYS(age_date)-TO_DAYS('".gmdate("Y-m-d H:i:s", time())."') < 60 AND dest.util_id=aco_util_id AND exp.util_id=age_util_id AND tzn_zone=dest.util_timezone ORDER BY age_date, age_heure_debut");
    } elseif ($typeRappel==_RAPPEL_ANNIV) {
      $sql=("SELECT age_id AS id, age_util_id AS idEmetteur, DATE_FORMAT(age_date,'%d/%m/".date("Y")."') AS dateEvent, age_libelle AS nomAnniv, age_date AS dateNaissance, aco_util_id AS idDestinataire, util_email AS destEmail, CONCAT(util_prenom,' ',util_nom) AS expNom, util_email AS expEmail, util_rappel_anniv, util_rappel_anniv_coeff, util_rappel_anniv_email AS envoiMail FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur WHERE aco_rappel_ok<".date("Y")." AND age_id=aco_age_id AND age_aty_id=1 AND util_id=aco_util_id AND util_rappel_anniv>0 AND TO_DAYS(DATE_FORMAT(age_date,'".date("Y")."-%m-%d 00:00:00'))-TO_DAYS('".date("Y-m-d H:i:s", time())."') < 60 ORDER BY dateEvent DESC");
    } elseif ($typeRappel==_RAPPEL_ANNIV_CONTACT) {
      $sql=("SELECT cal_id AS id, cal_util_id AS idEmetteur, DATE_FORMAT(cal_date_naissance,'%d/%m/".date("Y")."') AS dateEvent, CONCAT(cal_prenom,' ',cal_nom) AS nomAnniv, cal_date_naissance AS dateNaissance, cal_util_id AS idDestinataire, util_email AS destEmail, CONCAT(util_prenom,' ',util_nom) AS expNom, util_email AS expEmail, util_rappel_anniv, util_rappel_anniv_coeff, util_rappel_anniv_email AS envoiMail FROM ${PREFIX_TABLE}calepin, ${PREFIX_TABLE}utilisateur WHERE cal_rappel_ok<".date("Y")." AND util_id=cal_util_id AND util_rappel_anniv>0 AND TO_DAYS(DATE_FORMAT(cal_date_naissance,'".date("Y")."-%m-%d 00:00:00'))-TO_DAYS('".date("Y-m-d H:i:s", time())."') < 60 ORDER BY dateEvent DESC");
    }
    $DB_CX->DbQuery($sql);
    while ($enr = $DB_CX->DbNextRow()) {
      $tabDate = explode("/",$enr['dateEvent']);
      // Recuperation des infos de timezone de l'utilisateur
      if (!empty($enr['tzn_gmt'])) {
        $tzGmt = $enr['tzn_gmt'];
        $tzEte = calculBasculeDST($enr['tzn_date_ete'],gmdate("Y"),$enr['tzn_heure_ete'],$tzGmt,0);
        $tzHiver = calculBasculeDST($enr['tzn_date_hiver'],gmdate("Y"),$enr['tzn_heure_hiver'],$tzGmt,1);
        $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(gmdate("H"),gmdate("i"),0,gmdate("n"),gmdate("j"),gmdate("Y")));
      }
      if ($typeRappel==_RAPPEL_NOTE) {
        $tsEvent  = mktime($enr['age_heure_debut']+floor($decalageHoraire),(($enr['age_heure_debut']+$decalageHoraire)*60)%60,0,$tabDate[1],$tabDate[0],$tabDate[2]);
        $tsNow    = mktime(gmdate("H")+floor($decalageHoraire),gmdate("i")+($decalageHoraire*60)%60+($enr['age_rappel']*$enr['age_rappel_coeff']),0,gmdate("n"),gmdate("j"),gmdate("Y"));
        $libEvent = $enr['age_libelle'];
      } else {
        $tsEvent  = mktime(0,0,0,$tabDate[1],$tabDate[0],$tabDate[2]);
        $tsNow    = mktime(date("H"),date("i")+($enr['util_rappel_anniv']*$enr['util_rappel_anniv_coeff']),0,date("n"),date("j"),date("Y"));
        $libEvent = sprintf(trad("INFO_ANNIVERSAIRE_DE"),prefixeMot(strtolower(substr($enr['nomAnniv'],0,1)),trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE")).$enr['nomAnniv']);
      }
      if ($tsEvent<=$tsNow) {
        $nbRappel++;
        $libEvent = str_replace("&nbsp;", " ", $libEvent);
        $DB->DbQuery("INSERT INTO ${PREFIX_TABLE}information (info_emetteur_id, info_destinataire_id, info_age_id, info_date, info_commentaire, info_heure_rappel) VALUES (".$enr['idEmetteur'].",".$enr['idDestinataire'].",".(($typeRappel!=_RAPPEL_ANNIV_CONTACT) ? $enr['id'] : -1).",'".date("Y-m-d H:i",$tsEvent)."', '".$tsEvent."@".addslashes($libEvent)."', ".gmmktime().")");
        if ($typeRappel==_RAPPEL_NOTE) {
          $DB->DbQuery("UPDATE ${PREFIX_TABLE}agenda_concerne SET aco_rappel_ok=1 WHERE aco_age_id=".$enr['id']." AND aco_util_id=".$enr['idDestinataire']);
        } elseif ($typeRappel==_RAPPEL_ANNIV) {
          $DB->DbQuery("UPDATE ${PREFIX_TABLE}agenda_concerne SET aco_rappel_ok=".date("Y")." WHERE aco_age_id=".$enr['id']." AND aco_util_id=".$enr['idDestinataire']);
        } elseif ($typeRappel==_RAPPEL_ANNIV_CONTACT) {
          $DB->DbQuery("UPDATE ${PREFIX_TABLE}calepin SET cal_rappel_ok=".date("Y")." WHERE cal_id=".$enr['id']." AND cal_util_id=".$enr['idDestinataire']);
        }
        if ($enr['id']!=$noteID) {
          if (count($destMail)>0) {
            $nbMail += (envoiMail($nomEmetteur,$mailEmetteur,$destMail,$sujetMail,$corpsMail)) ? 1 : 0;
          }
          $noteID = $enr['id'];
          $destMail  = array();
          $mailEmetteur = $enr['expEmail'];
          $nomEmetteur = $enr['expNom'];
          if ($typeRappel==_RAPPEL_NOTE) {
            // Info sur le mail pour les destinataires "Phenix" de la note
            $sujetMail = date("[d/m/y - H\hi]",$tsEvent)." ".$libEvent;
            $corpsMail = nl2br("<HTML><BODY>".sprintf(trad("INFO_NOTE_DEBUTE"), date("d/m/Y",$tsEvent), date("H\hi",$tsEvent))."\n\n<U>".trad("INFO_LIBELLE")."</U>:&nbsp;".$libEvent."\n".((!empty($enr['age_lieu'])) ? "<U>".trad("INFO_EMPLACEMENT")."</U>:&nbsp;".$enr['age_lieu']."\n" : "").((!empty($enr['age_detail'])) ? "<U>".trad("INFO_DETAIL")."</U>:&nbsp;".$enr['age_detail']."\n" : "").((!empty($enr['nomContact'])) ? "<U>".trad("INFO_CONTACT")."</U>:&nbsp;".$enr['nomContact'] : "").signatureMail());
            // A la premiere lecture de la note, si age_email_contact=1 ET (cal_email OU cal_emailpro non vide)
            // -> Envoi du mail au contact associe puis desactivation du rappel au contact associe pour la note
            if ($enr['age_email_contact']==1 && (!empty($enr['cal_email']) || !empty($enr['cal_emailpro']))) {
              $corpsMailContact = nl2br("<HTML><BODY>".sprintf(trad("INFO_NOTE_CONTACT_DEBUTE"), date("d/m/Y",$tsEvent), date("H\hi",$tsEvent), $enr['expNom'])."\n\n<U>".trad("INFO_LIBELLE")."</U>:&nbsp;".$libEvent."\n".((!empty($enr['age_lieu'])) ? "<U>".trad("INFO_EMPLACEMENT")."</U>:&nbsp;".$enr['age_lieu']."\n" : "").((!empty($enr['age_detail'])) ? "<U>".trad("INFO_DETAIL")."</U>:&nbsp;".$enr['age_detail'] : "").signatureMail());
              // Envoi en priorite a l'adresse email "personnelle" du contact, si non renseignee, envoi sur l'adresse professionnelle
              $destMailContact = (!empty($enr['cal_email'])) ? array($enr['cal_email']) : array($enr['cal_emailpro']);
              $nbMail += (envoiMail($nomEmetteur,$mailEmetteur,$destMailContact,$sujetMail,$corpsMailContact)) ? 1 : 0;
              // Pour le rappel au contact associe a une note, lorsque le rappel a ete traite (c'est le cas ici),
              // -> on passe la valeur a 2 (1 signifiant -> A traiter et 0 -> Pas de rappel)
              $DB->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_email_contact=2 WHERE age_id=".$enr['id']);
            }
          } else {
            $sujetMail = "[".$enr['dateEvent']."] ".$libEvent;
            $tabDate = explode("-",$enr['dateNaissance']);
            $age = calculAge($tabDate,$tsEvent);
            $corpsMail = nl2br("<HTML><BODY>".sprintf(trad("INFO_DETAIL_AGE"), $enr['nomAnniv'], $age, $enr['dateEvent']).signatureMail());
          }
        }
        if ($enr['envoiMail']==1 && !empty($enr['destEmail'])) {
          $destMail[] = $enr['destEmail'];
        }
      }
    }
    if (count($destMail)>0) {
      $nbMail += (envoiMail($nomEmetteur,$mailEmetteur,$destMail,$sujetMail,$corpsMail)) ? 1 : 0;
    }
  }

  // Recherche des rappels de note a notifier pour tous les utilisateurs et aux contacts associes
  gereRappel(_RAPPEL_NOTE);
  // Recherche des anniversaires a notifier pour tous les utilisateurs
  gereRappel(_RAPPEL_ANNIV);
  // Recherche des anniversaires des contacts a notifier pour tous les utilisateurs
  gereRappel(_RAPPEL_ANNIV_CONTACT);

  // CORPS DE LA PAGE
  echo ("<!doctype html public \"-//w3c//dtd html 4.0 transitional//en\">
<HTML>
<HEAD>
  ".$refreshPage."<META http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
  <LINK rel=\"stylesheet\" type=\"text/css\" href=\"css/agenda_css.php?id=".$APPLI_STYLE."\">
  <TITLE>".trad("INFO_TITRE_PAGE")."</TITLE>\n");

  $onLoad = "";
  if ($idUser>0) {
    // Recherche des rappels a notifier pour l'utilisateur connecte
    $DB_CX->DbQuery("SELECT info_id FROM ${PREFIX_TABLE}information WHERE info_destinataire_id=".$idUser." AND info_heure_rappel<=".gmmktime());
    if ($DB_CX->DbNumRows()) {
      $onLoad = " onLoad=\"javascript: alerte();\"";
      echo ("  <SCRIPT language=\"JavaScript\">
  <!--
    var infoWin;
    function alerte() {
      if (window.showModalDialog) {
        var _options = 'dialogWidth:400px;dialogHeight:".min(95+50*$DB_CX->DbNumRows(),270)."px;center:1;scroll:1;help:0;status:0;';
        infoWin = window.showModalDialog(\"info_popup.php?sid=".$sid."\",\"EventWin_".$sid."\",_options);
      } else {
        var _width = 400, _height = ".min(100+60*$DB_CX->DbNumRows(),290).";
        var posX = (Math.max(screen.width,_width)-_width)/2;
        var posY = (Math.max(screen.height,_height)-_height)/2;
        var nVer = navigator.appVersion.split(';');
        var _position = (!(nVer[1].match('MSIE'))) ? ',top=' + posY + ',left=' + posX : ',screenY=' + posY + ',screenX=' + posX;
        infoWin = window.open('info_popup.php?sid=".$sid."','EventWin_".$sid."','dependent=1,menubar=0,toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,width=' + _width + ',height=' + _height + _position);
      }
    }
  //-->
  </SCRIPT>\n");
    }
  }
  echo ("</HEAD>

<BODY".$onLoad.">
  ".sprintf(trad("INFO_RECAPITULATIF"), $nbRappel, $nbMail));

  // Appel au fichier de gestion des sauvegardes automatiques de la base de donnees
  $path =  dirname(__FILE__)."/";
  include("inc/xtdump.inc.php");
  include ("inc/xtdump_webcron.php");

  echo ("\n</BODY>
</HTML>");

  // Fermeture SMTP
  if ($classSMTPLoaded)
    $mailer->smtp->quit();

  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
