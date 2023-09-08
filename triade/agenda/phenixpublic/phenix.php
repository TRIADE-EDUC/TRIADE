<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Impression_Note.txt ?>
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
  require("inc/html.inc.php");
  include("inc/param.inc.php");
  include("inc/fonctions.inc.php");
  if (!isset($sid)) {
    // Identification depuis le cookie
    if ($COOKIE_AUTH && empty($ztLogin)) {
      if (!empty($_COOKIE) && isset($_COOKIE[$COOKIE_NOM]))
        $tabLog = explode(":",$_COOKIE[$COOKIE_NOM]);
      elseif (!empty($HTTP_COOKIE_VARS) && isset($HTTP_COOKIE_VARS[$COOKIE_NOM]))
        $tabLog = explode(":",$HTTP_COOKIE_VARS[$COOKIE_NOM]);
      $ztLogin   = (get_magic_quotes_gpc()) ? stripslashes($tabLog[0]) : $tabLog[0];
      $ztPasswdMD5  = (get_magic_quotes_gpc()) ? stripslashes($tabLog[1]) : $tabLog[1];
      $hdScreen  = (get_magic_quotes_gpc()) ? stripslashes($tabLog[3]) : $tabLog[3];
      $autoLogin = $tabLog[2];
    }
    // Recherche de l'utilisateur correspondant
    $DB_CX->DbQuery("SELECT util_id, util_semaine_type FROM ${PREFIX_TABLE}utilisateur WHERE util_login = '".$ztLogin."' AND util_passwd = '".$ztPasswdMD5."'");

    if ($DB_CX->DbNumRows()) {
      // L'utilisateur existe
      $idUser = $DB_CX->DbResult(0,0);
      // On genere un nouveau sid
      mt_srand((double)microtime()*1000000);
      $hdScreen += 0;
      $sid = SessionId(8, $idUser, $DB_CX->DbResult(0,1), $hdScreen, false);
      $autoLogin += 0;
      if ($COOKIE_AUTH) // MAJ du cookie d'identification
        setcookie($COOKIE_NOM, $ztLogin.":".$ztPasswdMD5.":".$autoLogin.":".$hdScreen, time()+86400*$COOKIE_DUREE, "/", "", 0);
      if ($fromInstall=="1") // Menu Mise à jour
        $lienAdmin = "&tcMenu="._MENU_ADMIN;
      else
        $lienAdmin = "";
    }
    else {
      // L'utilisateur n'existe pas
      @session_destroy();
      // Fermeture BDD
      $DB_CX->DbDeconnect();
      Header("location: index.php?msg=1");
      exit;
    }
  }

  $idUser = Session_ok($sid);

  include("skins/$APPLI_STYLE.php");
  include("lang/$APPLI_LANGUE.php");

  // Recherche du nom de l'utilisateur
  $DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil, util_url_export FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$idUser." OR (util_partage_planning='1') OR (util_partage_planning='2' AND ppl_consultant_id=".$idUser.") ORDER BY nomUtil");

  $urlsUtils = array();
  while ($enr = $DB_CX->DbNextRow()) {
    if ($enr['util_id']==$idUser) {
      $nomUtilisateur = $enr['nomUtil'];
      $url_user = $enr['util_url_export'];
    }
    $nomsUtils[] = $enr['nomUtil'];
    $urlsUtils[] = $enr['util_url_export'];
    $idUtil[] = $enr['util_id'];
  }

  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <META http-equiv="Cache-Control" content="no-cache">
  <TITLE>Phenix <?php echo $APPLI_VERSION; ?> - Agenda de <?php echo $nomUtilisateur; ?></TITLE>
  <LINK rel="shortcut icon" type="image/x-icon" href="image/favicon.ico" />
  <LINK rel="icon" type="image/x-icon" href="image/favicon.ico" />
  <LINK rel="stylesheet" type="text/css" href="css/agenda_css.php?id=<?php echo $APPLI_STYLE; ?>">
<?php
  $limit_rss_def = "15";
  // On cree le flux pour l'utilisateur
  foreach ($urlsUtils as $key=>$urlUtil) {
    if (($urlUtil!="") && ($idUtil[$key]==$idUser)) {
      $limit_rss = array("5","10","20","50","100");
      for ($i=0;$i<5;$i=$i+1)  {
        echo "  <LINK rel=\"alternate\" type=\"application/rss+xml\" title=\"".sprintf(trad("PHENIX_RSS_TITRE1"), $limit_rss[$i])."\" href=\"agenda_note_export.php?zlTypeFichier=RSS&amp;id=".$url_user."&amp;limit_rss=".$limit_rss[$i]."\" />\n";
      }
    }
  }
  // On cree le flux pour des utilisateurs nous partageant leur agenda en modif/consult
  foreach ($urlsUtils as $key=>$urlUtil) {
    if (($urlUtil!="") && ($idUtil[$key]!=$idUser)) {
      echo "  <LINK rel=\"alternate\" type=\"application/rss+xml\" title=\"".sprintf(trad("PHENIX_RSS_TITRE2"), $nomsUtils[$key])."\" href=\"agenda_note_export.php?zlTypeFichier=RSS&amp;id=".$url_user."&amp;limit_rss=".$limit_rss_def."&amp;id_partage=".$idUtil[$key]."\" />\n";
    }
  }
?>
  <SCRIPT language="JavaScript">
  <!--
    /* Compatibilite du navigateur */
    var NS4 = (document.layers) ? 1 : 0;
    var OPE = (document.getElementById) ? 1 : 0;
    var IE4 = (document.all && parseInt(navigator.appVersion)>=4) ? 1 : 0;

    //Changement de statut de la note
    function termineNote(_note,_barree) {
      var oNoteA = oTemoinA = oNoteB = oTemoinB = null;
      var oNote = oTemoin = null;
      var dNote = "";
      dNote = _note.charAt(_note.length-1);
      if (dNote=="a" || dNote=="b")
        _note = _note.substr(0,_note.length-1);
      else
        dNote = "";
      if (NS4) {
        if (dNote) {
          if (eval("window.frames['nav_<?php echo $sid; ?>'].window.document.t"+_note+"a")) {
            oNoteA   = eval("window.frames['nav_<?php echo $sid; ?>'].window.document.n"+_note+"a");
            oTemoinA = eval("window.frames['nav_<?php echo $sid; ?>'].window.document.t"+_note+"a");
          }
          if (eval("window.frames['nav_<?php echo $sid; ?>'].window.document.t"+_note+"b")) {
            oNoteB   = eval("window.frames['nav_<?php echo $sid; ?>'].window.document.n"+_note+"b");
            oTemoinB = eval("window.frames['nav_<?php echo $sid; ?>'].window.document.t"+_note+"b");
          }
        } else {
          oNote   = eval("window.frames['nav_<?php echo $sid; ?>'].window.document.n"+_note);
          oTemoin = eval("window.frames['nav_<?php echo $sid; ?>'].window.document.t"+_note);
        }
      }
      else if (OPE || IE4) {
        if (dNote) {
          if (window.frames['nav_<?php echo $sid; ?>'].window.document.getElementById("n"+_note+"a")) {
            oNoteA   = window.frames['nav_<?php echo $sid; ?>'].window.document.getElementById("n"+_note+"a").style;
            oTemoinA = window.frames['nav_<?php echo $sid; ?>'].window.document.getElementById("t"+_note+"a");
          }
          if (window.frames['nav_<?php echo $sid; ?>'].window.document.getElementById("n"+_note+"b")) {
            oNoteB   = window.frames['nav_<?php echo $sid; ?>'].window.document.getElementById("n"+_note+"b").style;
            oTemoinB = window.frames['nav_<?php echo $sid; ?>'].window.document.getElementById("t"+_note+"b");
          }
        } else {
          oNote   = window.frames['nav_<?php echo $sid; ?>'].window.document.getElementById("n"+_note).style;
          oTemoin = window.frames['nav_<?php echo $sid; ?>'].window.document.getElementById("t"+_note);
        }
      }
      if (NS4 || OPE || IE4) {
        if (oNoteA) {
          var oImgTemoinA = oTemoinA.src;
          oImgTemoinA = oImgTemoinA.substr(Math.max(oImgTemoinA.length,11)-11,Math.min(oImgTemoinA.length,11));
          if (oImgTemoinA == "puce_ko.gif") {
            oNoteA.textDecoration = (_barree) ? "line-through" : "none";
            oTemoinA.src = "image/puce_ok.gif";
          } else {
            oNoteA.textDecoration = "none";
            oTemoinA.src = "image/puce_ko.gif";
          }
        }
        if (oNoteB) {
          var oImgTemoinB = oTemoinB.src;
          oImgTemoinB = oImgTemoinB.substr(Math.max(oImgTemoinB.length,11)-11,Math.min(oImgTemoinB.length,11));
          if (oImgTemoinB == "puce_ko.gif") {
            oNoteB.textDecoration = (_barree) ? "line-through" : "none";
            oTemoinB.src = "image/puce_ok.gif";
          } else {
            oNoteB.textDecoration = "none";
            oTemoinB.src = "image/puce_ko.gif";
          }
        }
        if (oTemoin) {
          var oImgTemoin = oTemoin.src;
          oImgTemoin = oImgTemoin.substr(Math.max(oImgTemoin.length,11)-11,Math.min(oImgTemoin.length,11));
          if (oImgTemoin == "puce_ko.gif") {
            oNote.textDecoration = (_barree) ? "line-through" : "none";
            oTemoin.src = "image/puce_ok.gif";
          } else {

            oNote.textDecoration = "none";
            oTemoin.src = "image/puce_ko.gif";
          }
        }
        window.frames['trash_<?php echo $sid; ?>'].window.location.href = "agenda_traitement.php?sid=<?php echo $sid ?>&comp=1&ztAction=TERMINE&idAge="+_note;
      }
      else
        window.frames['nav_<?php echo $sid; ?>'].window.location.href = "agenda_traitement.php?sid=<?php echo $sid ?>&comp=0&ztAction=TERMINE&idAge="+_note;
    }

    //Impression de planning
    function imprime(_vu,_sd,_extra) {
      window.open("agenda_impression.php?sid=<?php echo $sid ?>&vu="+_vu+"&sd="+_sd+"&extra="+_extra,"pxImpression");
    }
   	//Mod Ajout Impression Note
   	//Impression d'une note
		function imprNote(_note) {
			window.open("agenda_imprim_rdv.php?sid=<?php echo $sid ?>&idAge="+_note,"pxImpression");
		}
  	//Fin Mod Ajout Impression Note
  //-->
  </SCRIPT>
</HEAD>
<!-- frames -->
  <FRAMESET rows="*,0,0" border="0">
    <FRAME name="nav_<?php echo $sid; ?>" src="agenda.php?sid=<?php echo $sid.$lienAdmin."&hdScreen=".$hdScreen; ?>" target="nav_<?php echo $sid; ?>" marginwidth="3" marginheight="3" scrolling="auto" frameborder="0" noresize>
    <FRAME name="surv_<?php echo $sid; ?>" src="info_surveille.php?sid=<?php echo $sid; ?>" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" noresize>
    <FRAME name="trash_<?php echo $sid; ?>" src="blank.html" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" noresize>
  </FRAMESET>
  <NOFRAMES>
    <BODY>
      <CENTER><FONT color="red"><B><?php echo trad("PHENIX_ERREUR_FRAMES"); ?></B></FONT></CENTER>
    </BODY>
  </NOFRAMES>
</HTML>
