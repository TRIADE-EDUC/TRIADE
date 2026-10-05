<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_import_ch_coul.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Couleur_par_defaut.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Export_recherche.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Liste_des_libelles.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Scission_de_note_recurente.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Recherche_agendas_partages.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Copie_note_par_mail.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Aide.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Rappel_Sonore.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_RSS_Reader.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_qui_est_la.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Meteo_today_ico.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Menu_Note.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_MemoProgress_plus.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Impression_Note.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_import_util.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Groupes_de_contacts_multiples.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_fcke_aff_outils.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_DD.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_calcul_temps.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5.5_Options_en_masse.txt ?>
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

  $Fich_mod_lng[] = "_mod-xxxxxxxxx.php";
  // MOD Import choix couleur
  $Fich_mod_lng[] = "_mod-import_ch_coul.php";
  // MOD Couleur par defaut
  $Fich_mod_lng[] = "_mod-couleur.php";
  // MOD Statut termine auto
  $Fich_mod_lng[] = "_mod-export_recherche.php";
  // MOD Liste des libelles
  $Fich_mod_lng[] = "_mod-liste_libelles.php";
  // MOD Scission de note
  $Fich_mod_lng[] = "_mod-scission.php";
  // MOD Recherche etendue
  $Fich_mod_lng[] = "_mod-recherche.php";
  // MOD Copie note par mail
  $Fich_mod_lng[] = "_mod-copie_note.php";
  // MOD Aide
  $Fich_mod_lng[] = "_mod-aide.php";
  // MOD Son
  $Fich_mod_lng[] = "_mod-rappel-sonore.php";
  // MOD RSS Reader
  $Fich_mod_lng[] = "_mod-rss_reader.php";  
  // MOD D&D
  $Fich_mod_lng[] = "_mod-quiestla.php";
  //- MOD meteo -//
  $Fich_mod_lng[] = "_mod-meteo.php";
  //  MODS menu note
  $Fich_mod_lng[] = "_mod-menu-note.php";
  // MOD Restaure
  $Fich_mod_lng[] = "_mod-memo-progress.php";
	// Mod Ajout Impression Note
	$Fich_mod_lng[] = "_mod-Impression_Note.php";
	// Fin Mod Ajout Impression Note
  // Mod Import
  $Fich_mod_lng[] = "_mod-import.php";
  // MOD Groupes de contacts multiples
  $Fich_mod_lng[] = "_mod-groupes_multiples.php";
  // MOD Aide
  $Fich_mod_lng[] = "_mod-fcke-aff.php";
  // MOD D&D
  $Fich_mod_lng[] = "_mod-dd.php";
  //  MODS menu note
  $Fich_mod_lng[] = "_mod-calcul_temps.php";
  // MOD Options en masse
  $Fich_mod_lng[] = "_mod-options_en_masse.php";
  //Mod Emplacement Plus
  $Fich_mod_lng[] = "_mod-Emplacement_Plus.php";
  //Fin Mod Emplacement Plus

  for ($i = 0; $i < count($Fich_mod_lng); $i++) {
    if (file_exists("lang/mods/".$APPLI_LANGUE.$Fich_mod_lng[$i])) {
      include $APPLI_LANGUE.$Fich_mod_lng[$i];
    } elseif (file_exists("lang/mods/fr".$Fich_mod_lng[$i])) {
        include "fr".$Fich_mod_lng[$i];
    }
  }
?>
