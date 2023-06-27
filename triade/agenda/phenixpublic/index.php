<?php
error_reporting(0);
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

include_once("../../common/config2.inc.php");
if (AGENDADIRECT != "oui") { exit; }


  if (file_exists("inc/conf.inc.php")) {
    include("inc/param.inc.php");
  }
  if (!isset($INDEX_STYLE) || !file_exists("skins/".$INDEX_STYLE.".php")) {
    $APPLI_STYLE = "Petrole";
  } else {
    $APPLI_STYLE = $INDEX_STYLE;
  }
  require("inc/nocache.inc.php");
  $nc += 0;
  $PageIndex = ($nc == 1) ? "1" : "0";
  require("inc/html.inc.php");
  include("lang/$APPLI_LANGUE.php");



  if ($PUBLIC && $ztFrom == "profil" && $ztAction == "INSERT") {
    include("inc/fonctions.inc.php");
    // Recuperation des Saisies
    $rsProfil['util_nom'] = ($AUTO_UPPERCASE == true) ? strtoupper($ztNom) : ucfirst(strtolower($ztNom));
    $rsProfil['util_prenom'] = ucfirst($ztPrenom);
    $rsProfil['util_login'] = $ztLogin;
    $rsProfil['util_interface'] = $zlInterface;
    $rsProfil['util_debut_journee'] = $zlHeureDebut;
    $rsProfil['util_fin_journee'] = $zlHeureFin;
    if ($rdTelephone != "N")
      $rdTelephone = "O";
    $rsProfil['util_telephone_vf'] = $rdTelephone;
    $rsProfil['util_planning'] = $zlPlanning+0;
    if ($rdPartage=="2" && empty($ztPartage))
      $rdPartage = "0";
    $rsProfil['util_partage_planning'] = $rdPartage+0;
    $rsProfil['util_email'] = $ztEmail;
    if (($zlAffectation=="2" && empty($ztPartage)) || ($zlAffectation=="3" && empty($ztAffecte)))
      $zlAffectation = "0";
    $rsProfil['util_autorise_affect'] = $zlAffectation+0;
    if ($ckAlertEmail!="O")
      $ckAlertEmail = "N";
    elseif (empty($rsProfil['util_email']) || !$rsProfil['util_autorise_affect'])
      $ckAlertEmail="N";
    $rsProfil['util_alert_affect'] = $ckAlertEmail;
    if ($zlPrecision!="2")
      $zlPrecision = "1";
    $rsProfil['util_precision_planning'] = $zlPrecision;
    $rsProfil['util_semaine_type'] = "";
    for ($i=1; $i<8; $i++)
      $rsProfil['util_semaine_type'] .= ${"bt".$i} + 0;
    $rsProfil['util_duree_note'] = $zlDureeNote;
    if ($rdRappel != 2) {
      $rsProfil['util_rappel_delai'] = 0;
      $rsProfil['util_rappel_type'] = 1;
      $rsProfil['util_rappel_email'] = 0;
    } else {
      $rsProfil['util_rappel_delai'] = $zlRappelDelai;
      $rsProfil['util_rappel_type'] = $zlRappelType;
      $rsProfil['util_rappel_email'] = $ckRappelEmail;
    }
    if ($rsProfil['util_rappel_email'] != 1)
      $rsProfil['util_rappel_email'] = 0;
    if ($zlFormatNom!="1")
      $zlFormatNom = "0";
    $rsProfil['util_format_nom'] = $zlFormatNom;
    if ($zlMenuDispo!="9")
      $zlMenuDispo = "8";
    $rsProfil['util_menu_dispo'] = $zlMenuDispo;
    $rsProfil['util_url_export'] = $ztCodeURL;
    if ($rdBarree != "N")
      $rdBarree = "O";
    $rsProfil['util_note_barree'] = $rdBarree;
    if ($rdRappelAnniv != 2) {
      $rsProfil['util_rappel_anniv'] = 0;
      $rsProfil['util_rappel_anniv_coeff'] = 1;
      $rsProfil['util_rappel_anniv_email'] = 0;
    } else {
      $rsProfil['util_rappel_anniv'] = $zlRappelAnniv;
      $rsProfil['util_rappel_anniv_coeff'] = $zlRappelAnnivCoeff;
      $rsProfil['util_rappel_anniv_email'] = $ckAnnivEmail;
    }
    if ($rsProfil['util_rappel_anniv_email'] != 1)
      $rsProfil['util_rappel_anniv_email'] = 0;
    $rsProfil['util_langue'] = $zlLangue;
    $rsProfil['util_timezone'] = $zlFuseauHoraire;
    if ($ckFuseauPartage!="O")
      $ckFuseauPartage = "N";
    $rsProfil['util_timezone_partage'] = $ckFuseauPartage;
    $rsProfil['util_format_heure'] = $zlFormatHeure;
    // Format d'affichage des noms d'utilisateurs dans les listes en cas de retour sur la page de creation de compte suite a une erreur
    $FORMAT_NOM_UTIL = ($rsProfil['util_format_nom'] == "0") ? "util_nom, ' ', util_prenom" : "util_prenom, ' ', util_nom";

    // Verifie si le login choisi n'est pas deja utilise
    $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur WHERE util_login='".$rsProfil['util_login']."'");
    if (!$DB_CX->DbNumRows()) {
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}utilisateur (util_nom, util_prenom, util_login, util_passwd, util_interface, util_debut_journee, util_fin_journee, util_telephone_vf, util_planning, util_partage_planning, util_email, util_autorise_affect, util_alert_affect, util_precision_planning, util_semaine_type, util_duree_note, util_rappel_delai, util_rappel_type, util_rappel_email, util_format_nom, util_menu_dispo, util_url_export, util_note_barree, util_rappel_anniv, util_rappel_anniv_coeff, util_rappel_anniv_email, util_langue, util_timezone, util_timezone_partage, util_format_heure) VALUES ('".$rsProfil['util_nom']."', '".$rsProfil['util_prenom']."', '".$rsProfil['util_login']."', '".$ztPasswdMD5."','".$rsProfil['util_interface']."',".$rsProfil['util_debut_journee'].",".$rsProfil['util_fin_journee'].",'".$rsProfil['util_telephone_vf']."',".$rsProfil['util_planning'].",'".$rsProfil['util_partage_planning']."','".$rsProfil['util_email']."','".$rsProfil['util_autorise_affect']."','".$rsProfil['util_alert_affect']."','".$rsProfil['util_precision_planning']."','".$rsProfil['util_semaine_type']."','".$rsProfil['util_duree_note']."',".$rsProfil['util_rappel_delai'].",".$rsProfil['util_rappel_type'].",".$rsProfil['util_rappel_email'].",'".$rsProfil['util_format_nom']."','".$rsProfil['util_menu_dispo']."','".$rsProfil['util_url_export']."','".$rsProfil['util_note_barree']."',".$rsProfil['util_rappel_anniv'].",".$rsProfil['util_rappel_anniv_coeff'].",".$rsProfil['util_rappel_anniv_email'].",'".$rsProfil['util_langue']."','".$rsProfil['util_timezone']."','".$rsProfil['util_timezone_partage']."','".$rsProfil['util_format_heure']."')");
      if ($DB_CX->DbAffectedRows()>0) {
        $idUser = $DB_CX->DbInsertID();
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_groupe (cgr_util_id, cgr_nom) VALUES (".$idUser.", '".trad("COMMUN_NON_CLASSE")."')");
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}favoris_groupe (fgr_util_id, fgr_nom) VALUES (".$idUser.", '".trad("COMMUN_NON_CLASSE")."')");
        // Partage du planning en consultation : si partage selectif uniquement
        if ($rsProfil['util_partage_planning']==2) {
          $tabPartage = explode("+", $ztPartage);
          for ($i=0;$i<count($tabPartage);$i++)
            $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_partage VALUES (".$idUser.",".$tabPartage[$i].")");
        }
        // Partage du planning en modification
        if ($rsProfil['util_autorise_affect']==3) {// Si affectation selective uniquement
          $tabAffecte = explode("+", $ztAffecte);
          for ($i=0;$i<count($tabAffecte);$i++)
            $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_affecte VALUES (".$idUser.",".$tabAffecte[$i].")");
        }
        elseif ($rsProfil['util_autorise_affect']==2) {// Si consultation basee sur la liste du partage
          if ($rsProfil['util_partage_planning']!=2)
            $rsProfil['util_autorise_affect']=$rsProfil['util_partage_planning'];
          else {
            for ($i=0;$i<count($tabPartage);$i++)
              $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_affecte VALUES (".$idUser.",".$tabPartage[$i].")");
          }
        }
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}droit (droit_util_id, droit_admin) VALUES (".$idUser.", 'N')");
        if ($COOKIE_AUTH) { // Envoi d'un cookie temporaire + connexion automatique
          setcookie($COOKIE_NOM, $rsProfil['util_login'].":".$ztPasswdMD5.":0:0", time()+86400*$COOKIE_DUREE, "/", "", 0);
          Header("location: phenix.php");
          exit;
        }
        else
          $msg = 15;
      }
      else {
        $nc = 1;
        $msg = 16;
      }
    }
    else {
      $nc = 1;
      $msg = 3;
    }
  }
  elseif (!isset($msg) && !$nc) {
    // Identification depuis le cookie
    if ($COOKIE_AUTH && (!empty($_COOKIE[$COOKIE_NOM]) || !empty($HTTP_COOKIE_VARS[$COOKIE_NOM]))) {
      if (!empty($_COOKIE) && isset($_COOKIE[$COOKIE_NOM]))
        $tabLog = explode(":",$_COOKIE[$COOKIE_NOM]);
      elseif (!empty($HTTP_COOKIE_VARS) && isset($HTTP_COOKIE_VARS[$COOKIE_NOM]))
        $tabLog = explode(":",$HTTP_COOKIE_VARS[$COOKIE_NOM]);
      // Connexion automatique choisie par l'utilisateur
      if ($tabLog[2]==1) {
        Header("location: phenix.php");
        exit;
      }
    }
  }

  include("skins/$APPLI_STYLE.php"); // Style par defaut
  entete_page();

  if ($nc != 1) { // On n'est pas dans le cas d'une creation de compte
    if (isset($bgColorIndex))
      $bgColor[1] = $bgColorIndex; 
?>
  <LINK rel="shortcut icon" type="image/x-icon" href="image/favicon.ico" />
  <LINK rel="icon" type="image/x-icon" href="image/favicon.ico" />
  <SCRIPT language="JavaScript" src="inc/MD5.js" type="text/javascript"></SCRIPT>
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    // Fonction trim javascript (suppression d'espaces avant et apres une chaine)
    function trim(chaine) {
      return chaine.replace(/^\s+/, "").replace(/\s+$/, "");
    }

    function saisieOK(theForm) {
      if (trim(theForm.ztLogin.value) == "") {
        window.alert("<?php echo trad("INDEX_ERREUR_LOGIN"); ?>");
        theForm.ztLogin.focus();
        return (false);
      }

      if (trim(theForm.ztPasswd.value) == "") {
        window.alert("<?php echo trad("INDEX_ERREUR_PASSWORD"); ?>");
        theForm.ztPasswd.focus();
        return (false);
      }

      //Cryptage MD5 du mot de passe avant submit (s'il a ete renseigne)
      theForm.ztPasswdMD5.value = MD5(theForm.ztPasswd.value);
      //Mot de passe en clair supprime
      theForm.ztPasswd.value = "";
      theForm.hdScreen.value = (window.innerWidth) ? window.innerWidth : window.document.body.clientWidth;
      return (true);
    }
<?php if ($PUBLIC) { ?>

    function compteUtil() {
      window.location.href = "index.php?nc=1";
    }
<?php } ?>
  //-->
  </SCRIPT>
</HEAD>

<BODY leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" onLoad="javascript: window.focus(); document.formLogUtil.ztLogin.focus();">
  <BR><BR><TABLE border=0 cellpadding=0 cellspacing=0 width="100%">
  <TR height=24>
<?php message($msg); ?>
  </TR>
  <TR>
    <TD valign="top" align="center" width="100%"><FORM action="phenix.php" method="post" name="formLogUtil" id="formLogUtil" target="_top" onsubmit="javascript: return saisieOK(this);">
      <TABLE border="0" cellspacing="0" cellpadding="0" align="center">
      <TR>
        <TD height="100" nowrap>&nbsp;</TD>
      </TR>
      <TR>
        <TD valign="top" align="center"><TABLE border="0" cellspacing="0" cellpadding="0" style="border-collapse:separate;">
          <TR>
            <TD colspan="2" class="ProfilMenuActif" align="center" valign="middle" height="18" style="font-size:12px;"><B>Phenix <?php echo $APPLI_VERSION; ?></B></TD>
            <TD><img src="<?php echo (file_exists("skins/".$APPLI_STYLE."/ombre_1.png") ? "skins/".$APPLI_STYLE."/ombre_1.png" : (file_exists("skins/".$APPLI_STYLE."/ombre_1.gif") ? "skins/".$APPLI_STYLE."/ombre_1.gif" : "image/index/ombre_1b.gif")); ?>" alt="" width="6" height="18" border="0"></TD>
          </TR>
          <TR>
            <TD colspan="2" align="center" class="bordLRB" bgcolor="<?php echo $bgColor[1]; ?>"><TABLE border="0" cellspacing="0" cellpadding="2" style="font-size:11px;">
              <TR>
                <TD height="18" colspan=3>&nbsp;</TD>
              </TR>
              <TR>
                <TD height="22"><?php echo trad("INDEX_UTILISATEUR"); ?>&nbsp;</TD>
                <TD><INPUT type="text" class="Texte" name="ztLogin" size="15" maxlength="12" value="<?php echo $ztLogin; ?>"></TD>
                <TD rowspan=3 align="right" width="80"><img src="<?php echo (file_exists("skins/".$APPLI_STYLE."/login.png") ? "skins/".$APPLI_STYLE."/login.png" : (file_exists("skins/".$APPLI_STYLE."/login.gif") ? "skins/".$APPLI_STYLE."/login.gif" : "image/index/login.gif")); ?>" alt="" width="64" height="64" border="0"></TD>
              </TR>
              <TR>
                <TD height="22" nowrap><?php echo trad("INDEX_PASSWORD"); ?>&nbsp;&nbsp;</TD>
                <TD><INPUT type="password" class="Texte" name="ztPasswd" size="15" maxlength="12" value="<?php echo $ztPasswd; ?>"><INPUT type="hidden" name="ztPasswdMD5"></TD>
              </TR>
              <TR>
                <TD height="22" colspan="2" nowrap><?php if ($COOKIE_AUTH) { ?><?php echo trad("INDEX_COOKIE"); ?>&nbsp;&nbsp;<INPUT type="checkbox" class="case" name="autoLogin" value="1"><?php } ?>&nbsp;</TD>
              </TR>
              <TR>
                <TD height="50" colspan="3" align="center" valign="middle"><INPUT type="submit" class="Bouton" value="<?php echo trad("INDEX_CONNECTER"); ?>" name="btSubmit"><?php if ($PUBLIC) { ?>&nbsp;&nbsp;&nbsp;<INPUT type="button" class="Bouton" value="<?php echo trad("INDEX_NOUVEAU"); ?>" name="btCreer" onclick="javascript: compteUtil();"><?php } ?></TD>
              </TR>
            </TABLE></TD>
            <TD><img src="<?php echo (file_exists("skins/".$APPLI_STYLE."/ombre_2.png") ? "skins/".$APPLI_STYLE."/ombre_2.png" : (file_exists("skins/".$APPLI_STYLE."/ombre_2.gif") ? "skins/".$APPLI_STYLE."/ombre_2.gif" : "image/index/ombre_2b.gif")); ?>" alt="" width="6" height="139" border="0"></TD>
          </TR>
          <TR>
            <TD><img src="<?php echo (file_exists("skins/".$APPLI_STYLE."/ombre_3.png") ? "skins/".$APPLI_STYLE."/ombre_3.png" : (file_exists("skins/".$APPLI_STYLE."/ombre_3.gif") ? "skins/".$APPLI_STYLE."/ombre_3.gif" : "image/index/ombre_3b.gif")); ?>" alt="" width="8" height="6" border="0"></TD>
            <TD><img src="<?php echo (file_exists("skins/".$APPLI_STYLE."/ombre_4.png") ? "skins/".$APPLI_STYLE."/ombre_4.png" : (file_exists("skins/".$APPLI_STYLE."/ombre_4.gif") ? "skins/".$APPLI_STYLE."/ombre_4.gif" : "image/index/ombre_4b.gif")); ?>" alt="" width="292" height="6" border="0"></TD>
            <TD><img src="<?php echo (file_exists("skins/".$APPLI_STYLE."/ombre_5.png") ? "skins/".$APPLI_STYLE."/ombre_5.png" : (file_exists("skins/".$APPLI_STYLE."/ombre_5.gif") ? "skins/".$APPLI_STYLE."/ombre_5.gif" : "image/index/ombre_5b.gif")); ?>" alt="" width="6" height="6" border="0"></TD>
          </TR>
        </TABLE></TD>
      </TR>
      </TABLE>
      <INPUT type="hidden" name="hdScreen"></TD>
    </FORM></TD>
  </TR>
  </TABLE>
<?php
    // Recherche de nouvelle version
    
  }
  elseif ($PUBLIC) {
    //Demande de creation d'un nouveau compte
    echo ("  <SCRIPT language=\"JavaScript\" type=\"text/javascript\">
  <!--
    // Fonction trim javascript (suppression d'espaces avant et apres une chaine)
    function trim(chaine) {
      return chaine.replace(/^\s+/, \"\").replace(/\s+$/, \"\");
    }
  //-->
  </SCRIPT>
</HEAD>

<BODY onLoad=\"javascript: window.focus(); selectUtil(document.frmProfil.zlUtilisateur, document.frmProfil.zlPartage); selectUtil(document.frmProfil.zlUtilisateur2, document.frmProfil.zlAffecte); document.frmProfil.ztNom.focus();\" topmargin=0 leftmargin=0 rightmargin=0 bottommargin=0 marginwidth=0 marginheight=0>
  <TABLE border=0 cellpadding=0 cellspacing=0 width=\"100%\">
  <TR height=24>\n");
    message($msg);
    echo ("  </TR>
  <TR>
    <TD valign=\"top\" align=\"center\" width=\"100%\">\n");
    if (!$msg) {
      $DB_CX->DbQuery("SHOW FIELDS FROM ".$PREFIX_TABLE."utilisateur");
      while ($champ = $DB_CX->DbNextRow()) {
        $rsProfil[$champ[Field]]=$champ['Default'];
      }
      $rsProfil['util_id']     = "";
      $rsProfil['util_nom']    = "";
      $rsProfil['util_prenom'] = "";
      $rsProfil['util_login']  = "";
      $rsProfil['util_passwd'] = "";
      $formatHeure = $rsProfil['util_format_heure']==12 ? "h:ia" : "H:i";
      // Format d'affichage des noms d'utilisateurs dans les listes en fonction du choix defini ci-dessus
      $FORMAT_NOM_UTIL = ($rsProfil['util_format_nom'] == "0") ? "util_nom, ' ', util_prenom" : "util_prenom, ' ', util_nom";
    }
    include("agenda_profil.php");
    echo ("    <BR>&nbsp;</TD>
  </TR>
  </TABLE>\n");
  }
?>
</BODY>
</HTML>
