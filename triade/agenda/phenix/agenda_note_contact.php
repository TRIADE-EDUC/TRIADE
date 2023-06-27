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
  if (isset($_GET['sid']) || isset($HTTP_GET_VARS['sid'])) {
    include("inc/param.inc.php");
    include("inc/fonctions.inc.php");
    include("inc/html.inc.php");
  } else {
    include("inc/interdit.html");
    exit;
  }

  $idUser = Session_ok($sid);

  if ($idUser == -1) {
    include("inc/interdit.html");
    exit;
  }

  include("lang/$APPLI_LANGUE.php");

  $DB_CX->DbQuery("SELECT CONCAT($FORMAT_NOM_CONTACT) AS nom,cal_adresse,cal_cp,cal_ville,cal_pays,cal_domicile,cal_travail,cal_portable,cal_fax  FROM ${PREFIX_TABLE}calepin WHERE cal_id=".$id);
  $nomCtt = $adresseCtt = $villeCtt = "";
  if ($enr = $DB_CX->DbNextRow()) {
    $nomCtt = addslashes($enr['nom']);  // Nom
    $villeCtt = addslashes($enr['cal_ville']);  // Ville
    if (!empty($enr['cal_adresse']))   // Adresse
      $adresseCtt .= str_replace(chr(13),"",str_replace(chr(10),"\\n",addslashes($enr['cal_adresse'])))."\\n\\n";
    if (!empty($enr['cal_cp']) || !empty($enr['cal_ville']))  // Code postal et Ville
      $adresseCtt .= trim($enr['cal_cp']." ".$enr['cal_ville'])."\\n";
    if (!empty($enr['cal_pays']))   // Pays
      $adresseCtt .= $enr['cal_pays']."\\n\\n";
    if (!empty($enr['cal_domicile']))   // Telephone domicile
      $adresseCtt .= trad("NOTECONT_TEL_DOMICILE")." : ".telephoneVF($enr['cal_domicile'])."\\n";
    if (!empty($enr['cal_travail']))   // Telephone professionnel
      $adresseCtt .= trad("NOTECONT_TEL_TRAVAIL")." : ".telephoneVF($enr['cal_travail'])."\\n";
    if (!empty($enr['cal_portable']))  // Portable
      $adresseCtt .= trad("NOTECONT_TEL_PORTABLE")." : ".telephoneVF($enr['cal_portable'])."\\n";
    if (!empty($enr['cal_fax']))  // FAX
      $adresseCtt .= trad("NOTECONT_FAX")." : ".telephoneVF($enr['cal_fax'])."\\n";
    // Suppression des retours chariots eventuels en fin d'adresse
    while (substr($adresseCtt,-2)=="\\n") {
      $adresseCtt = substr($adresseCtt,0,strlen($adresseCtt)-2);
    }

    if ($AUTORISE_HTML && $AUTORISE_FCKE) {
      // Si l'editeur HTML est actif, les retours a la ligne doivent etre des <br>
      $adresseCtt = str_replace("\\n","<br>", $adresseCtt);
    }
  }
  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
<!DOCTYPE html public "-//w3c//dtd html 4.0 transitional//en">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <TITLE><?php echo trad("NOTECONT_TITRE");?></TITLE>
  <LINK rel="stylesheet" type="text/css" href="css/agenda_css.php?id=<?php echo $APPLI_STYLE; ?>">
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    // Recopie du nom dans le libelle de la note
    var libelleNote = parent.window.frames['nav_<?php echo $sid; ?>'].document.Form1.ztLibelle.value;
    parent.window.frames['nav_<?php echo $sid; ?>'].document.Form1.ztLibelle.value = libelleNote.replace(/ +$/gi, "") + " <?php echo $nomCtt; ?>";
    // Recopie de l'adresse dans le detail de la note
    parent.window.frames['nav_<?php echo $sid; ?>'].document.Form1.ztDetail.value = "<?php echo $adresseCtt; ?>";
    // Recopie de la ville du contact dans l'emplacement de la note
    var lieuNote = parent.window.frames['nav_<?php echo $sid; ?>'].document.Form1.ztLieu.value;
    //parent.window.frames['nav_<?php echo $sid; ?>'].document.Form1.ztLieu.value = lieuNote.replace(/ +$/gi, "") + " <?php echo $villeCtt; ?>";
    parent.window.frames['nav_<?php echo $sid; ?>'].ToggleFck();
  //-->
  </SCRIPT>
</HEAD>
<BODY></BODY>
</HTML>
