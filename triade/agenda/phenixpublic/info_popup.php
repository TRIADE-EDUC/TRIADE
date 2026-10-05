<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Rappel_Sonore.txt ?>
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
  if (isset($_GET['sid']) || isset($HTTP_GET_VARS['sid'])) {
    include("inc/param.inc.php");
    include("inc/fonctions.inc.php");
  } else {
    include("inc/interdit.html");
    exit;
  }

  $idUser = Session_ok($sid);

  if ($idUser == -1) {
    include("inc/interdit.html");
    exit;
  }

  include("skins/$APPLI_STYLE.php");
  include("lang/$APPLI_LANGUE.php");

  // On recherche si on a des infos a afficher
  $DB_CX->DbQuery("SELECT info_id, info_date, info_commentaire, info_heure_rappel FROM ${PREFIX_TABLE}information WHERE info_destinataire_id=".$idUser." AND info_heure_rappel<=".gmmktime()." ORDER BY info_id");
  // Parcours et affichage des resultats
  $nbInfoTrouvees = $DB_CX->DbNumRows();
  if ($nbInfoTrouvees>0) {
    $infoUser  = "<TABLE width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" bgcolor=\"".$AgendaPopUpBords."\" style=\"border-collapse:separate;\">\n";
    $infoUser .= "        <TR><TD bgcolor=\"".$AgendaFavorisFond."\" align=\"center\" width=\"100%\"><A class=\"MemoFavorisTitre\">&nbsp;<B>".trad("POPUP_RAPPEL")."</B></A></TD></TR>\n";
    $index = 1;
    $cpt = 0;
    while ($enr = $DB_CX->DbNextRow()) {
      $index = 1 - $index;
      $infoUser .= "        <TR bgcolor=\"".$bgColor[$index]."\">";
      $pos = strpos($enr['info_commentaire'],"@");
      $timeStamp = substr($enr['info_commentaire'],0,$pos);
      $infoUser .= "<TD><A href=\"agenda.php?sd=".$timeStamp."&sid=".$sid."\" target=\"nav_".$sid."\">".sprintf(trad("POPUP_INFO_RAPPEL"),substr($enr['info_commentaire'],$pos+1),$tabJour[date("w",$timeStamp)]." ".strftime(trad("COMMUN_FORMAT_DATE_CREATION"),strtotime($enr['info_date'])))."</A><BR>";

      $infoUser .= "\n          &nbsp;&nbsp;&nbsp;<LABEL for=\"ckSuppr".$cpt."\"><INPUT type=\"checkbox\" class=\"Case\" id=\"ckSuppr".$cpt."\" value=\"".$enr['info_id']."\" onClick=\"javascript: changeCoche('ckReport".$cpt."',false);\">&nbsp;".trad("POPUP_SUPPR")."</LABEL><BR>";

      $infoUser .= "\n          &nbsp;&nbsp;&nbsp;<LABEL for=\"ckReport".$cpt."\"><INPUT type=\"checkbox\" class=\"Case\" id=\"ckReport".$cpt."\" value=\"".$enr['info_id']."\" onClick=\"javascript: changeCoche('ckSuppr".$cpt."',false);\">&nbsp;".trad("POPUP_REPORT")."&nbsp;</LABEL>
            <SELECT id=\"zlQ".$cpt."\" onFocus=\"changeCoche('ckSuppr".$cpt."',false); changeCoche('ckReport".$cpt."',true);\">";
      for ($i=1;$i<60;$i++) {
        $selected = ($i==5) ? " selected" : "";
        $infoUser .= "<OPTION value=\"".$i."\"".$selected.">".$i."</OPTION>";
      }

      $infoUser .= ("</SELECT>
            <SELECT id=\"zlC".$cpt."\" onFocus=\"changeCoche('ckSuppr".$cpt."',false); changeCoche('ckReport".$cpt."',true);\">
              <OPTION value=\"60\">".trad("COMMUN_MINUTE")."</OPTION>
              <OPTION value=\"3600\">".trad("COMMUN_HEURE")."</OPTION>
              <OPTION value=\"86400\">".trad("COMMUN_JOUR")."</OPTION>
        </SELECT>");

      $infoUser .= "</TD></TR>\n";
      $cpt++;
    }
    $infoUser .= "      </TABLE></TD>\n    </TR>\n    <TR>\n      <TD align=\"center\" class=\"legendeBis\"><A onClick=\"javascript:CheckAll('ckReport',false);CheckAll('ckSuppr',true);\" style=\"cursor:pointer;color:".$AgendaLegende.";\">".trad("POPUP_SUPPR_TOUS")."</A> - <A onClick=\"javascript:CheckAll('ckSuppr',false);CheckAll('ckReport',true);\" style=\"cursor:pointer;color:".$AgendaLegende.";\">".trad("POPUP_REPORT_TOUS")."</A><BR>&nbsp;";
  }
  else {
    include("blank.html");
    exit;
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <LINK rel="stylesheet" type="text/css" href="css/agenda_css.php?id=<?php echo $APPLI_STYLE; ?>">
  <TITLE>Phenix <?php echo $APPLI_VERSION; ?></TITLE>
  <SCRIPT language=JavaScript>
  <!--
  //Fermeture automatique de la popup apres 65s
  function timeOut() {
    this.todo='self.close()';
    timerId=setTimeout(this.todo,65000);
  }

  function OnLoad_Event() {
    var monTimeout;
    monTimeout=new timeOut();
  }

  //Verifie si une information a ete selectionnee
  function saisieOK(theForm) {
    var i;
    var statutOK = false;

    theForm.ztSuppr.value = "";
    theForm.ztReport.value = "";

    for (i=0; i<<?php echo $nbInfoTrouvees; ?>; i++) {
      if (document.getElementById('ckSuppr'+i).checked) {
        theForm.ztSuppr.value += ((theForm.ztSuppr.value=="") ? "" : ",") + document.getElementById('ckSuppr'+i).value;
        statutOK = true;
      } else if (document.getElementById('ckReport'+i).checked) {
        theForm.ztReport.value += ((theForm.ztReport.value=="") ? "" : ",") + document.getElementById('ckReport'+i).value + "|" + (document.getElementById('zlQ'+i).value * document.getElementById('zlC'+i).value);
        statutOK = true;
      }
    }
    if (!statutOK) {
      window.alert("<?php echo trad("POPUP_SELECTION"); ?>");
      return (false);
    }
    theForm.submit();
    self.close();
    return (true);
  }

  // Change l'etat coche / decoche (_statut) d'une case a cocher (_checkbox)
  function CheckAll(_checkbox,_statut) {
    for (var i=0; i<<?php echo $nbInfoTrouvees; ?>; i++) {
      changeCoche(_checkbox + i,_statut);
    }
  }
  function changeCoche(_checkbox, _statut) {
    document.getElementById(_checkbox).checked = _statut;
  }
  //-->
  </SCRIPT>
</HEAD>
<BODY onLoad="javascript: window.focus(); OnLoad_Event();" style='<?php echo $AgendaPopUpFondImage; ?>'>
<?php  // Mod Son 
  $DB_CX->DbQuery("SELECT util_rappel_son, util_choix_son FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
  if ($DB_CX->DbNumRows() && $enr = $DB_CX->DbNextRow()) {
    if ($enr['util_rappel_son'] == 'O') 
    { 
      echo ("<EMBED SRC=\"son/".$enr['util_choix_son']."\" LOOP=FALSE HIDDEN=TRUE AUTOSTART=TRUE>");
    }
  }
  // Mod Son ?>
  <FORM action="info_valider.php?sid=<?php echo $sid; ?>" method="post" target="surv_<?php echo $sid; ?>" name="Form1">
    <INPUT type="hidden" name="ztSuppr" value="">
    <INPUT type="hidden" name="ztReport" value="">
    <TABLE width="100%" border="0" cellspacing="0" cellpadding="0">
    <TR>
      <TD valign="top"><?php echo $infoUser; ?></TD>
    </TR>
    <TR>
      <TD align="center"><BR><INPUT type="button" class="Bouton" name="btSubmit" value="<?php echo trad("POPUP_VALIDER"); ?>" onclick="javascript:return saisieOK(document.Form1);">&nbsp;&nbsp;&nbsp;
        <INPUT type="button" class="Bouton" value="<?php echo trad("POPUP_FERMER"); ?>" onclick="javascript:return window.close();"><BR>&nbsp;
      </TD>
    </TR>
    </TABLE>
  </FORM>
</BODY>
</HTML>
<?php
  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
