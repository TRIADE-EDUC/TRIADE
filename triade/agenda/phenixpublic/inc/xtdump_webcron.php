<?php
###########################################################################
##                      -=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-               ##
##                      XT-DUMP v 0.7 :  Mysql Dump System               ##
##                      -=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-               ##
##                                                                       ##
## -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- ##
##                                                                       ##
##     Copyright (c) 2001-2003 by DreaXTeam (webmaster@dreaxteam.net)    ##
##                          http://dreaxteam.net                         ##
##                                                                       ##
## This program is free software. You can redistribute it and/or modify  ##
## it under the terms of the GNU General Public License as published by  ##
## the Free Software Foundation.                                         ##
###########################################################################

/*
  NE PLUS MODIFIER LE FICHIER EN DESSOUS DE CETTE LIGNE (Sauf si vous savez ce que vous faites ;) )
  DO NOT MODIFY THE FILE BELOW THIS LINE (unless you know what you're doing ;)).
*/

  @set_time_limit(600);

  $DB_CX->DbQuery("SELECT param, valeur FROM ${PREFIX_TABLE}configuration WHERE groupe=1");
  if ($DB_CX->DbNumRows()) {
    while ($enr = $DB_CX->DbNextRow()) {
      ${$enr['param']} = $enr['valeur'];
    }
    $tbl = explode(",",$XT_TABLE_SAV);
    if(!($XT_TAILLE_FIC+0)) {
      $XT_TAILLE_FIC = 200000;
    }
    // Recuperation de la date courante sous forme de timestamp (minuit + 1 seconde)
    $dateCrtXT=mktime(0,0,1,date("m"),date("d"),date("Y"));
    // On calcule la date de prochaine execution en fonction du type de sauvegarde choisi
    // Sauvegarde journaliere
    if ($XT_PERIODICITE==1) {
      $nextDateXT=mktime(0,0,0,date("m"),date("d")+1,date("Y"));
    }
    // Sauvegarde hebdomadaire
    elseif ($XT_PERIODICITE==2) {
      $nextDateXT=mktime(0,0,0,date("m"),date("d")+7,date("Y"));
    }
    // Sauvegarde mensuelle
    elseif ($XT_PERIODICITE==3) {
      $nextDateXT=mktime(0,0,0,date("m")+1,date("d"),date("Y"));
    }
    // Pas de sauvegarde automatique
    else {
      $dateCrtXT=$XT_NEXT_SAV;
    }
    // Si la sauvegarde est desactivee ou ne doit pas encore avoir lieu, on quitte
    if($dateCrtXT <= $XT_NEXT_SAV) {
      return;
    }
  } else {
    echo "<br><center><font color=red>".trad("XTDUMP_ER_CONFIG")."</font></center>";
    return;
  }

  // Entete Mail

  /* Gestion des Options de sauvegarde */
  if ($XT_TYPE_SAV == "1") {
    $sv_s = true;
    $sv_d = true;
  } else if ($XT_TYPE_SAV == "2") {
    $sv_s = true;
    $sv_d = false;
    $fc   = "_struct";
  } else if ($XT_TYPE_SAV == "3") {
    $sv_s = false;
    $sv_d = true;
    $fc   = "_data";
  } else {
    return;
  }

  $fext = "." . $XT_FORMAT_SAV;
  $fich = $cfgBase . $fc . $fext;

  /* Ecrazer ou non le fichier */
  $dte = "";
  if ($XT_ECRASE!="1") {
    $dte = date("dMy_Hi")."_";
  }

  $gz = "";

  if ($XT_COMPRESS_GZIP == "1") {
    $AppliMail= "application/x-zip-compressed";
    $gz .= ".gz";
  } else {
    $AppliMail= "text/plain";
  }
  $fcut = false;
  $ftbl = false;

  $f_nm = array();

  if($XT_SCINDER_FIC == "1") {
    $fcut = true;
    $fzmax = $XT_TAILLE_FIC;
    $nbf = 1;
    $f_size = 170;
  }
  if ($XT_FIC_PAR_TABLE == "1") {
    $ftbl = true;
  } else {
    if (!$fcut) {
      open_file($path."backup/dump_".$dte.$cfgBase.$fc.$fext.$gz,$XT_COMPRESS_GZIP,$cfgBase);
    } else {
      open_file($path."backup/dump_".$dte.$cfgBase.$fc."_1".$fext.$gz,$XT_COMPRESS_GZIP,$cfgBase);
    }
  }
  $nbf = 1;
  mysql_connect($cfgHote,$cfgUser,$cfgPass);
  mysql_select_db($cfgBase);

  $tblsv = do_backup($PREFIX_TABLE, $XT_DROP_TABLE, $XT_COMPRESS_GZIP, $cfgBase, $fzmax);

  @mysql_close();
  if (!$ftbl) {
    close_file($XT_COMPRESS_GZIP);
  }

  // Entete Mail
  if ($XT_ENVOI_MAIL=="1") {
    if (!$classMailerLoaded) {
      $mailer = new Mailer();
      $classMailerLoaded = true;
      if (!empty($SMTP_SERVER)) {
        $mailer->use_smtp($SMTP_SERVER, $SMTP_PORT, $SMTP_LOGIN, $SMTP_PASSWORD);
        $classSMTPLoaded = true;
      }
    } else {
      $mailer->clear_all();
    }
    $mailer->set_from($XT_MAIL, trad("XTDUMP_EXPED_MAIL"));
    $mailer->set_address($XT_MAIL);
    $mailer->set_format('html');
    $mailer->set_subject(sprintf(trad("FCT_SUJET_MAIL"),sprintf(trad("XTDUMP_SUJET_MAIL"), date(trad("XTDUMP_DATE_SUJET")))));

    $message  = "<HMTL>
  <BODY>
    <P>".sprintf(trad("XTDUMP_MAIL_FICH_SAUV"), $tblsv)."</P>
    <TABLE border='1' align='center' cellpadding='0' cellspacing='0'>
    <TR>
      <TD align=center class=texte><font size=2><b>".trad("XTDUMP_MAIL_FICH_NOM")."</b></font></TD>
      <TD align=center class=texte><font size=2><b>".trad("XTDUMP_MAIL_FICH_TAILLE")."</b></font></TD>
    </TR>";
    reset($f_nm);
    while (list($i,$val) = each($f_nm)) {
      $NomFich=$val;
      $valf = substr ($val,7);
      $mailer->attachment($NomFich, $valf, 'attachment', $AppliMail);
      $message  .= "  <TR><TD><font size=2>&nbsp;".$valf."&nbsp;</font></TD>";
      $fz_tmp = filesize($val);
      if ($fcut && ($fz_tmp > $fzmax)) {
        $message  .= "<TD>&nbsp;<font size=2 color=red>".sprintf(trad("XTDUMP_MAIL_OCTET"), $fz_tmp)."</font>&nbsp;</TD></TR>\n";
      } else {
        $message  .= "<TD>&nbsp;<font size=2>".sprintf(trad("XTDUMP_MAIL_OCTET"), $fz_tmp)."</font>&nbsp;</TD></TR>\n";
      }
    }
    $message  .= "  </TABLE><BR>
    <P align=\"center\">".sprintf(trad("XTDUMP_NEXT"), date(trad("XTDUMP_DATE_NEXT"),$nextDateXT))."</P>".signatureMail();
    $mailer->set_message($message);

    if( !$mailer->send() ) {
      echo "\n  <BR>".trad("XTDUMP_NO_MAIL");
    } else {
      echo "\n  <BR>".trad("XTDUMP_MAIL_OK");
    }
  } else {
    echo "\n  <BR>".trad("XTDUMP_SAVE_OK");
  }

  // Enregistrement de la prochaine date d'execution
  insertOrUpdate('XT_NEXT_SAV', $nextDateXT, 1);
?>
