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

  require("inc/param.inc.php");

  // Suppression des informations selectionnees
  if (!empty($ztSuppr)) {
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}information WHERE info_id IN (".$ztSuppr.")");
  }
  // Report du rappel des informations selectionnees -> info_id|report_en_seconde[,...]
  if (!empty($ztReport)) {
    $tabReport = explode(",",$ztReport);
    for ($i=0; $i<count($tabReport); $i++) {
      list($infoId, $report) = explode("|",$tabReport[$i]);
      $tsAlert = gmmktime()+$report;
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}information SET info_heure_rappel=".$tsAlert." WHERE info_id=".$infoId);
    }
  }

  // Fermeture BDD
  $DB_CX->DbDeconnect();

  // Reprise de la surveillance
  Header("location: info_surveille.php?sid=".$sid);
  exit;
?>
