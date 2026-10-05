<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Couleur_par_defaut.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Copie_note_par_mail.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Aide.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Menu_Note.txt ?>
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
  // Mod Aide
  // Fichier d'aide contextuel
  ?> <SCRIPT> HelpPhenixCtx="{36EC3C0B-85F2-4573-963F-4C4208A3A54C}.htm"; </SCRIPT> <?php
  // Mod Aide
?>

<!-- MODULE GESTION DES NOTES -->
<?php
  $DB_CX->DbQuery("SELECT util_debut_journee, util_duree_note, util_rappel_delai, util_rappel_type, util_rappel_email, util_fin_journee, util_couleur FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
  $debutJournee = $DB_CX->DbResult(0,0);
  $dureeNote = $DB_CX->DbResult(0,1);
  //Delai avant rappel de l'utilisateur
  if (!$id) {
    $enr['age_rappel'] = $DB_CX->DbResult(0,2);
    $enr['age_rappel_coeff'] = $DB_CX->DbResult(0,3);
    $enr['age_email'] = $DB_CX->DbResult(0,4);
  }
  $finJournee = $DB_CX->DbResult(0,5);
  // MOD Couleur par defaut
  $defCouleur = $DB_CX->DbResult(0,"util_couleur");
  // Fin MOD Couleur par defaut
  // Si on arrive des plannings globaux
  if (isset($decalH) && $tzPartage=="O") {
    $DB_CX->DbQuery("SELECT tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$decalH." AND tzn_zone=util_timezone");
    $tzLibelle = $DB_CX->DbResult(0,"tzn_libelle");
    $tzGmt = $DB_CX->DbResult(0,"tzn_gmt");
    $tzDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
    $tzHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
    $tzDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
    $tzHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
    // Calcul des bascules ete/hiver en tenant compte de l'annee de la note
    $tzEte = calculBasculeDST($tzDateEte,date("Y",$sd),$tzHeureEte,$tzGmt,0);
    $tzHiver = calculBasculeDST($tzDateHiver,date("Y",$sd),$tzHeureHiver,$tzGmt,1);
  }

  $enr['ageDate'] = date("d/m/Y",$sd);
  $enr['age_heure_debut'] = (isset($hD) && !empty($hD)) ? $hD : $debutJournee;
  $enr['age_heure_fin'] = (isset($hF) && !empty($hF)) ? $hF : $enr['age_heure_debut']+(0.25*$dureeNote);
  $enr['age_cal_id'] = (isset($cA) && !empty($cA)) ? $cA : "";
  $ztAction = "INSERT";
  $idAgeMere = 0;
  $titrePage = trad("NOTE_TITRE_ENREG");
  if ($id) {
    $testDroit = ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION) ? "" : " AND age_util_id=".$idUser;
    if (!isset($edit) && !isset($idMere)) {
      // Pas de note mere par defaut
      // On recherche si on est en train de modifier une occurrence d'une note recurrente
      // Si c'est le cas on enregistre l'id de la note Mere pour eventuellement modifier toute la serie
      $DB_CX->DbQuery("SELECT age_mere_id FROM ${PREFIX_TABLE}agenda WHERE age_id=".$id.$testDroit." AND age_aty_id!=1");
      if ($DB_CX->DbResult(0,0)) {
        $idAgeMere = $DB_CX->DbResult(0,0)+0;
        $titrePage = trad("NOTE_TITRE_MODIF_RECURRENTE");
      } else {
        // On recherche si la note en cours de modification n'est pas la note Mere d'une serie recurrente
        // pour offrir la possibilite de modifier soit cette occurrence, soit toute la serie
        $DB_CX->DbQuery("SELECT COUNT(age_id) FROM ${PREFIX_TABLE}agenda WHERE age_mere_id=".$id.$testDroit." AND age_aty_id!=1");
        if ($DB_CX->DbResult(0,0)) {
          $idAgeMere = $id;
          $titrePage = trad("NOTE_TITRE_MODIF_RECURRENTE");
        }
      }
    } else {
      $id = $idMere;
    }
    if (!$idAgeMere) {
      //Recuperation des informations de la note
      $DB_CX->DbQuery("SELECT age_id, age_date, age_heure_debut, age_heure_fin, age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_plage, age_plage_duree, age_libelle, age_detail, age_rappel, age_rappel_coeff, age_email, age_prive, age_couleur, age_aty_id, age_disponibilite, age_lieu, age_cal_id, age_email_contact, age_email_copie, age_util_id FROM ${PREFIX_TABLE}agenda WHERE age_id=".$id.$testDroit." AND age_aty_id!=1");
      if ($enr = $DB_CX->DbNextRow()) {
        $tabDate = explode("-",$enr['age_date']);
        // Decalage de la note
        list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif'],$enr['ageDate']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$enr['age_date'],$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],1);
        if ($enr['age_heure_debut'] > $enr['age_heure_fin'] && $enr['age_heure_fin'] == 0) $enr['age_heure_fin'] = "24.00";
        $tabDate = explode("-",$enr['ageDate']);
        $enr['ageDate'] = date("d/m/Y",mktime(0,0,0,$tabDate[1],$tabDate[2],$tabDate[0]));
        $ztAction = "UPDATE";
        $titrePage = trad("NOTE_TITRE_MODIF");
        $dureeNote = ($enr['age_heure_fin'] - $enr['age_heure_debut'])*4;
      }
      if (empty($enr['age_couleur']))
        $enr['age_couleur'] = $AgendaFondNotePerso;
    }
  }
  // MOD Couleur par defaut
  //else
  //  $enr['age_couleur'] = $AgendaFondNotePerso;
  else
    $enr['age_couleur'] = (empty($defCouleur)) ? $AgendaFondNotePerso : $defCouleur;
  // Fin MOD Couleur par defaut

  if (!$idAgeMere) {
    $tabTemp    = array(trad("COMMUN_COUL_DEFAUT") => $AgendaFondNotePerso);
    $tabCouleur = array_merge($tabTemp,getListeCouleur());
    $iColor = 1;
?>
<?php include("inc/checkdate.js.php"); ?>
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    var hIncrement = <?php echo ($dureeNote-1); ?>;
     //Mod Emplacement Plus
    //Remplissage du champs à partir de la selection dans la combo box
    function addEmpl(_select) {
      if (_select.selectedIndex>0) {
        document.Form1.ztLieu.value=_select[_select.selectedIndex].text+" ";
      }    
    }
    //Fin Mod Emplacement Plus
    // Saisie d'un libelle a partir de la liste de choix
    function addLib(_select) {
      if (_select.selectedIndex>0) {
        document.Form1.ztLibelle.value=_select[_select.selectedIndex].text+" ";
        var strLib = document.Form1.zlLibelle.value;
        var infoLib = strLib.split('!');
        if (infoLib[0] != "0.00") {
          document.Form1.zlHeureDuree.selectedIndex = (infoLib[0]*4)-1;
          // Note ne couvrant pas toute la journee
          document.Form1.ckTypeNote.checked = false;
          affPlageHoraire(false);
        } else {
          // Note couvrant toute la journee
          document.Form1.ckTypeNote.checked = true;
          affPlageHoraire(true);
        }
        ajustHeureFin();
        var ok = false;
        for (var i=0; i<document.Form1.zlCouleur.options.length && !ok; i++) {
          if (document.Form1.zlCouleur.options[i].value == infoLib[1]) {
            ok = true;
            document.Form1.zlCouleur.selectedIndex = i;
          }
        }
        if ((infoLib[2]) && confirm('<?php echo trad("NOTE_JS_LIBELLE_DETAIL");?>')) {
          ToggleText();
          parent.window.frames['trash_<?php echo $sid; ?>'].window.location.href = "agenda_note_libelle.php?sid=<?php echo $sid ?>&id="+infoLib[2];
        }
        document.Form1.ztLibelle.focus();
      } else {
        // On reinitialise tous les champs
        document.Form1.ckTypeNote.checked = false;
        document.Form1.zlHeureDuree.selectedIndex = <?php echo ($dureeNote-1); ?>;
        ajustHeureFin();
        affPlageHoraire(false);
        document.Form1.zlCouleur.selectedIndex = 0;
      }
      changeCouleurListe(document.Form1.zlCouleur,document.Form1.ztCouleur);
    }
    // Ajuste l'heure de fin en fonction de l'heure de debut et de la duree selectionnees
    function ajustHeureFin() {
      var idxHeureFin = document.Form1.zlHeureDebut.selectedIndex + document.Form1.zlHeureDuree.selectedIndex;
      if (idxHeureFin>=document.Form1.zlHeureFin.options.length) {
        document.Form1.zlHeureFin.selectedIndex = document.Form1.zlHeureFin.options.length - 1;
        ajustHeureDuree();
      } else {
        document.Form1.zlHeureFin.selectedIndex = idxHeureFin;
      }
    }
    // Ajuste la liste de choix de duree de la note en fonction de l'heure de fin selectionne
    function ajustHeureDuree() {
      var idxHeureDuree = document.Form1.zlHeureFin.selectedIndex - document.Form1.zlHeureDebut.selectedIndex;
      if (idxHeureDuree < 0) {
        idxHeureDuree = 0;
        document.Form1.zlHeureFin.selectedIndex = document.Form1.zlHeureDebut.selectedIndex;
      }
      document.Form1.zlHeureDuree.selectedIndex = Math.min(idxHeureDuree,document.Form1.zlHeureDuree.length-1);
    }

    // Remplace le libelle et le detail de la note par les informations du contact selectionne
    function copieInfoContact(_id) {
      if (!isNaN(_id) && _id>0 && confirm('<?php echo trad("NOTE_JS_REMPLACE_LIBELLE");?>')) {
        ToggleText();
        parent.window.frames['trash_<?php echo $sid; ?>'].window.location.href = "agenda_note_contact.php?sid=<?php echo $sid ?>&id="+_id;
      }
      return (false);
    }

<?php
    if ($ztAction == "INSERT") {
?>
    // Parametre le retour sur cette page et lance l'enregistrement d'une note
    function recommence(theForm) {
      theForm.ztRecommence.value = "OUI";
      if (!saisieOK(theForm)) {
        theForm.ztRecommence.value = "NON";
      }
    }
<?php
    }
  // Recherche si il y a des utilisateurs  modifier pour lesquels l'utilisateur courant n'a pas de droit...
  $Autoriser_Modif_Affect = True;  //Vrai si l'utilisateur courant dispose de tous les droits necessaires...
  $tmp_Participants = "";
  $str_Participants = "";          //La liste des participants pour lesquels nous n'avons pas acces...

  // Un administrateur AM_AGENDAS de niveau >= 20 a acces  tous le monde, test inutile...
  if ($droit_AGENDAS < _DROIT_AGENDA_SEUL) {
    // Si il ne s'agit pas d'une nouvelle note (cree depuis les disponibilites)
    if (empty($sChoix)) {
      // Si il ne s'agit pas d'une nouvelle note
      if ($id) {
        // Recherche et stocke les utilisateurs pour lesquels on dispose d'autorisation...
        $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_affecte ON paf_util_id=util_id LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$idUser." OR (util_autorise_affect ='1') OR (util_autorise_affect IN ('2','3') AND paf_consultant_id=".$idUser.") OR (util_partage_planning ='1') OR (util_partage_planning ='2' AND ppl_consultant_id=".$idUser.")");
        if ($DB_CX->DbNumRows()) {
          while ($tmp = $DB_CX->DbNextRow()){
            $Tab1[] = $tmp['util_id'];
          }
        }

        //Genere la liste des participants auxquels nous n'avons pas acces...
        $DB_CX->DbQuery("SELECT aco_util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur WHERE util_id=aco_util_id AND aco_age_id=".$id);
        if ($DB_CX->DbNumRows()) {
          while ($Tab2 = $DB_CX->DbNextRow()) {
            if($tmp_Participants == "") {
              $tmp_Participants = $Tab2[0];
            } else {
              $tmp_Participants .="+".$Tab2[0];
            }

            $Trouve = False;
            $i=0;
            while ($i<count($Tab1) and !$Trouve) {
              $Trouve = $Tab1[$i]==$Tab2[0];
              $i++;
            }
            if(!$Trouve) {
              $Autoriser_Modif_Affect = False;
              if($str_Participants == "") {
                $str_Participants = " - ".$Tab2[1];
              } else {
                $str_Participants .= "<br> - ".$Tab2[1];
              }
            }
          }
        }
      }
    }
  }
    if ($nbAffect>1) {
?>
    function genereListe(_liste, _tabTexte, _tabValue, _tailleTab) {
      for (var i=0; i<_tailleTab; i++)
        _liste.options[i]=new Option(_tabTexte[i], _tabValue[i]);
    }

    function bubbleSort(_tabText, _tabValue,_tailleTab) {
      var i,s;

      do {
        s=0;
        for (i=1; i<_tailleTab; i++)
          if (_tabText[i-1] > _tabText[i]) {
            y = _tabText[i-1];
            _tabText[i-1] = _tabText[i];
            _tabText[i] = y;
            y = _tabValue[i-1];
            _tabValue[i-1] = _tabValue[i];
            _tabValue[i] = y;
            s = 1;
          }
      } while (s);
    }

    function videListe(_liste) {
      var cpt = _liste.options.length;

      for(var i=0; i<cpt; i++) {
        _liste.options[0] = null;
      }
    }

    function selectUtil(_listeSource, _listeDest) {
      var i,j;
      var ok = false;
      var tabDestTexte = new Array();
      var tabDestValue = new Array();
      var tailleTabDest = 0;

      for (i=0; i<_listeDest.options.length; i++) {
        tabDestTexte[tailleTabDest]   = _listeDest.options[i].text;
        tabDestValue[tailleTabDest++] = _listeDest.options[i].value;
      }

      for (j=_listeSource.options.length-1; j>=0; j--) {
        if (_listeSource.options[j].selected) {
          ok = true;
          tabDestTexte[tailleTabDest]   = _listeSource.options[j].text;
          tabDestValue[tailleTabDest++] = _listeSource.options[j].value;
          _listeSource.options[j] = null;
        }
      }

      if (ok) {
        //Trie du tableau
        bubbleSort(tabDestTexte, tabDestValue, tailleTabDest);
        //Vide la liste destination
        videListe(_listeDest);
        //Recree la liste
        genereListe(_listeDest, tabDestTexte, tabDestValue, tailleTabDest);
      }
    }

    //Fonction pour selectionner tous les utilisateurs d'une liste source et les transferer dans une liste destination
    function selectAll(_listeSource, _listeDest) {
      for (var i=0; i<_listeSource.options.length; i++) {
        _listeSource.options[i].selected = true;
      }
      selectUtil(_listeSource, _listeDest);
    }

    function recupSelection(_liste, _champ) {
      _champ.value = "";
      for (var i=0; i<_liste.options.length; i++) {
        _champ.value += ((i) ? "+" : "") + _liste.options[i].value;
      }
    }

<?php
    }
?>
    function saisieOK(theForm) {
<?php
    if ($nbAffect>1)
      echo "      recupSelection(theForm.zlParticipant, theForm.ztParticipant);\n";
?>
      if (trim(theForm.ztLibelle.value) == "") {
        window.alert("<?php echo trad("NOTE_JS_SAISIR_LIBELLE");?>");
        theForm.ztLibelle.focus();
        return (false);
      }
      if (theForm.ztDateNote.value == "") {
        window.alert("<?php echo trad("NOTE_JS_SAISIR_DATE");?>");
        theForm.ztDateNote.focus();
        return (false);
      }
      if (!chk_date_format(theForm.ztDateNote))
        return (false);
<?php
    // Verification inutile si l'utilisateur ne peut pas affecter de notes a d'autres personnes
    if ($nbAffect>1) {
?>
      if (theForm.ztParticipant.value == "") {
        window.alert("<?php echo trad("NOTE_JS_SELECT_PERSONNE");?>");
        theForm.zlUtilisateur.focus();
        return (false);
      }
<?php
    }
    // Affichage abrege en cas de modification d'une occurrence d'une note recurrente
    // Dans ce cas l'utilisateur ne peut pas modifier les options de periodicite
    if ($edit!="occ") {
?>
      if (theForm.elements['zlPeriodicite'].value == '1' && theForm.elements['rdQ'][0].checked) {
        if (isNaN(theForm.ztQ.value) || theForm.ztQ.value<1 || theForm.ztQ.value == "") {
          window.alert("<?php echo trad("NOTE_JS_SAISIR_JOUR");?>");
          theForm.ztQ.value = "1"
          theForm.ztQ.focus();
          return (false);
        }
      }
      else if (theForm.elements['zlPeriodicite'].value == '4') {
        if (isNaN(theForm.ztM.value) || theForm.ztM.value<1 || theForm.ztM.value == "") {
          window.alert("<?php echo trad("NOTE_JS_SAISIR_MOIS");?>");
          theForm.ztM.value = "1"
          theForm.ztM.focus();
          return (false);
        }
      }
      if (theForm.elements['rdPlage'][0].checked && (isNaN(theForm.ztP.value) || theForm.ztP.value<1 || theForm.ztP.value == "")) {
        window.alert("<?php echo trad("NOTE_JS_SAISIR_OCCURENCE");?>");
        theForm.ztP.value = "10"
        theForm.ztP.focus();
        return (false);
      }
      if (theForm.elements['rdPlage'][1].checked && theForm.ztDateFin.value == "") {
        window.alert("<?php echo trad("NOTE_JS_SAISIR_PERIODE");?>");
        theForm.ztDateFin.focus();
        return (false);
      }
      if (theForm.elements['rdPlage'][1].checked && !chk_date_format(theForm.ztDateFin)) {
        return (false);
      }
<?php
    // Fin de l'affichage abrege en cas de modification d'une occurrence d'une note recurrente
    }
?>
<?php
  // MOD Copie note par mail
?>
      if (trim(theForm.ztEmailCopie.value) != "") {
        var emailRegexp = /^([a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}(\s*;\s*[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4})*)$/;
        if (emailRegexp.test(theForm.ztEmailCopie.value) == false) {
          window.alert("<?php echo trad("NOTE_JS_ERREUR_EMAIL_COPIE");?>");
          theForm.ztEmailCopie.focus();
          return (false);
        }
      }
<?php
  // Fin MOD Copie note par mail
?>
      if (theForm.zlCouleur.selectedIndex==0)
        theForm.zlCouleur.value="";

      PrepareSave();
      theForm.submit();
      return (true);
    }

    function dupNote(theForm) {
      theForm.idAge.value="";
      theForm.ztAction.value="INSERT";
      theForm.edit.value="";
      if (theForm.btSupprime!=null) {
        theForm.btSupprime.disabled=true;
      }
      theForm.btDuplique.disabled=true;
      if (ie4)
        document.all["titrePage"].innerHTML = "<?php echo trad("NOTE_TITRE_ENREG");?>";
      else if (ope)
        document.getElementById("titrePage").innerHTML = "<?php echo trad("NOTE_TITRE_ENREG");?>";
      else if (ns4) {
        var lyr = document.titrePage.document;
        lyr.write("<?php echo trad("NOTE_TITRE_ENREG");?>");
        lyr.close();
      }
    }

    // Permet de ne pas afficher l'horaire de la note lorsqu'elle couvre toute la journee
    function affPlageHoraire(_allDay) {
      var t1 = document.getElementById('plageHoraire');
      var t2 = document.getElementById('plageHoraireFull');
      if (!_allDay) {
        t2.style.display = "none";
        t1.style.display = "block";
      } else {
        t1.style.display = "none";
        t2.style.display = "block";
      }
    }
    // Permet de ne pas afficher les details de chaque choix de la periodicite
    var periodiciteVisible;
    function affPeriodicite(idDiv) {
      if (periodiciteVisible) {
        periodiciteVisible.style.display = "none";
      } else {
        document.getElementById('detailJour').style.display = "none";
        document.getElementById('detailSemaine').style.display = "none";
        document.getElementById('detailMois').style.display = "none";
        document.getElementById('detailAnnee').style.display = "none";
      }
      switch (idDiv) {
        case '2' : periodiciteVisible = document.getElementById('detailJour'); break;
        case '3' : periodiciteVisible = document.getElementById('detailSemaine'); break;
        case '4' : periodiciteVisible = document.getElementById('detailMois'); break;
        case '5' : periodiciteVisible = document.getElementById('detailAnnee'); break;
        default : idDiv = '1'; periodiciteVisible = null; break;
      }
      if (idDiv != '1') {
        periodiciteVisible.style.display = "block";
        // Affichage de la plage de periodicite uniquement lorsque la note n'est pas unique
        document.getElementById('plagePeriodicite').style.display = "block";
      } else {
        document.getElementById('plagePeriodicite').style.display = "none";
      }
    }
    //  MODS menu note
    // Gestion des onglets
    var tabOnglets = new Array("Partage","Dispo","Rappel"<?php echo (($edit != "occ") ? ",\"Period\"" : ""); ?>);
    function affOnglet(_onglet) {
      if (_onglet != "Reduit") {
        document.getElementById("tdReduit").className = "ProfilMenuInactif";
        document.getElementById("hrefReduit").className = "ProfilMenuInactif";
        document.getElementById("tdComplet").className = "ProfilMenuActif";
        document.getElementById("hrefComplet").className = "ProfilMenuActif";
        for (var i = 0;i < tabOnglets.length ; i++)
          {document.getElementById("tr"+tabOnglets[i]).style.display = window.getComputedStyle?"table-row":"block";}
      } else {
        document.getElementById("tdReduit").className = "ProfilMenuActif";
        document.getElementById("hrefReduit").className = "ProfilMenuActif";
        document.getElementById("tdComplet").className = "ProfilMenuInactif";
        document.getElementById("hrefComplet").className = "ProfilMenuInactif";
        for (var i = 0;i < tabOnglets.length;i++) 
          {document.getElementById("tr"+tabOnglets[i]).style.display = "none";}
      }
    }      
  //  MODS menu note  

    // Ajoute l'option du dernier jour du mois si "dernier" est selectionne
    function ajouteOptionMensuel(theForm) {
      if (theForm.elements['zlM2'].value == '4') {
        theForm.elements['zlM3'].options[(theForm.elements['zlM3'].options.length)] = new Option('<?php echo trad("NOTE_JOUR"); ?>','4');
      } else if (theForm.elements['zlM3'].options.length == 8) {
        theForm.elements['zlM3'].options[(theForm.elements['zlM3'].options.length - 1)] = null;
      }
    }
  //-->
  </SCRIPT>
<?php
  } else {
?>
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    function selectUtil(_listeSource, _listeDest) {
      return false;
    }
    function ajustHeureDuree() {
      return false;
    //  MODS menu note
    function affOnglet(_onglet) {
      return false;
    }
    //  MODS menu note
    }
  //-->
  </SCRIPT>
<?php
  }
?>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD height="28" class="sousMenu"><DIV id="titrePage"><?php echo $titrePage; ?></DIV></TD>
  </TR>
  </TABLE>
<?php
  // Si on a detecte que la note en cours de modification appartient a une serie recurrente
  // on propose au choix de modifer toute la serie (en se basant sur les informations de la note mere)
  // ou uniquement l'occurrence choisie (dans ce cas le formulaire ne contiendra pas les informations de periodicite)
  if ($idAgeMere) {
    $URL = "?sid=$sid&tcType=$tcType&tcMenu=$tcMenu&tcPlg=$tcPlg&sd=$sd&id=$id".(isset($decalH) ? "&decalH=$decalH" :"");
    if (!empty($ggr)) {  // si on est en edition de note depuis les plannings globaux
      $URL .= "&ggr=".$ggr."&ztActionGrp=".$ztActionGrp;
    }
?>
  <BR>
  <TABLE border="0" cellspacing="0" cellpadding="8">
  <TR>
    <TD bgcolor="<?php echo $bgColor[0]; ?>" align="center" class="bordTLRB"><?php echo trad("NOTE_LIB_MODIF_SERIE");?></TD>
  </TR>
  </TABLE>
  <BR><FORM action="agenda.php" method="post" name="Form1" onsubmit="javascript: return (false);">
    <INPUT type="button" name="btAll" class="Bouton" value="<?php echo trad("NOTE_BT_SERIE");?>" tabindex="1" onclick="javascript: document.location.href='<?php echo $URL; ?>&edit=all&idMere=<?php echo $idAgeMere; ?>'">&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btOcc" class="Bouton" value="<?php echo trad("NOTE_BT_OCCURENCE");?>" tabindex="2" onclick="javascript: document.location.href='<?php echo $URL; ?>&edit=occ&idMere=<?php echo $id; ?>'">
  </FORM>
<?php
  } else {
?>
  <BR>
  <FORM action="agenda_traitement.php" method="post" name="Form1">
    <INPUT type="hidden" name="sid" value="<?php echo $sid; ?>">
    <INPUT type="hidden" name="idAge" value="<?php echo $enr['age_id']; ?>">
    <INPUT type="hidden" name="ztAction" value="<?php echo $ztAction; ?>">
    <INPUT type="hidden" name="edit" value="<?php echo $edit; ?>">
    <INPUT type="hidden" name="ztFrom" value="note">
    <INPUT type="hidden" name="tcMenu" value="<?php echo $tcPlg; ?>">
    <INPUT type="hidden" name="sd" value="<?php echo date("Y-n-j", $sd); ?>">
    <INPUT type="hidden" name="ztRecommence" value="NON">
    <INPUT type="hidden" name="tzGmt" value="<?php echo $tzGmt; ?>">
    <INPUT type="hidden" name="tzDateEte" value="<?php echo $tzDateEte; ?>">
    <INPUT type="hidden" name="tzHeureEte" value="<?php echo $tzHeureEte; ?>">
    <INPUT type="hidden" name="tzDateHiver" value="<?php echo $tzDateHiver; ?>">
    <INPUT type="hidden" name="tzHeureHiver" value="<?php echo $tzHeureHiver; ?>">
    <INPUT type="hidden" name="ggr" value="<?php echo $ggr; ?>">
    <INPUT type="hidden" name="ztActionGrp" value="<?php echo $ztActionGrp; ?>">
<?php
    if ($nbAffect==1)
      echo "    <INPUT type=\"hidden\" name=\"ztParticipant\" value=\"".$idUser."\">\n";
?>
    <TABLE cellspacing="0" cellpadding="0" width="550" border="0">
<?php      
  //  MODS menu note
?>  
    <TR bgcolor="<?php echo $bgColor[$iColor%2]; ?>">
      <TD colspan="2" class="ProfilMenuActif">
        <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
          <TR align="center">
            <TD width="50%" id="tdComplet" height="22" class="ProfilMenuActif"><A href="javascript: affOnglet('Complet');" id="hrefComplet" class="ProfilMenuActif"><?php echo trad("NOTE_MENU_COMPLET"); ?></A></TD>
            <TD width="50%" id="tdReduit" class="ProfilMenuInactif"><A href="javascript: affOnglet('Reduit');" id="hrefReduit" class="ProfilMenuInactif"><?php echo trad("NOTE_MENU_REDUIT"); ?></A></TD>
        </TR>
      </TABLE>
      </TD>
    </TR>
<?php      
  //  MODS menu note
?>  
    <TR bgcolor="<?php echo $bgColor[$iColor%2]; ?>">
      <TD class="tabIntitule"><?php echo trad("NOTE_LIB_LIBELLE"); ?></TD>
      <TD class="tabInput"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
<?php
    //Liste des libelles personnalises de l'utilisateur connecte
    $DB_CX->DbQuery("SELECT lib_id, lib_nom, lib_duree, lib_couleur, lib_detail FROM ${PREFIX_TABLE}libelle WHERE lib_util_id=".$idUser." OR (lib_util_id!=".$idUser." AND lib_partage='O') ORDER BY lib_nom");
    if ($DB_CX->DbNumRows()) {
      echo ("      <TR>
        <TD nowrap><SELECT name=\"zlLibelle\" onchange=\"javascript: addLib(this);\">
          <OPTION value=\"0\">-- ".trad("NOTE_LIBELLES_PERSO")." --</OPTION>\n");
      while ($lib = $DB_CX->DbNextRow()) {
        if (empty($lib['lib_couleur']))
          $lib['lib_couleur'] = $AgendaFondNotePerso;
        if (!empty($lib['lib_detail']))
          $lib['lib_detail']=$lib['lib_id'];
        echo "          <OPTION value=\"".$lib['lib_duree']."!".$lib['lib_couleur']."!".$lib['lib_detail']."\">".htmlspecialchars($lib['lib_nom'])."</OPTION>\n";
      }
      echo ("        </SELECT></TD>
      </TR>\n");
    }
?>
      <TR>
        <TD nowrap width="470" height="18"><INPUT type="text" class="Texte" name="ztLibelle" value="<?php echo htmlspecialchars($enr['age_libelle']); ?>" style="width:469px" maxlength="230"></TD>
      </TR>
    </TABLE></TD>
  </TR>
  <!-- Mod Emplacement Plus -->
  <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>" height="21">
    <TD class="tabIntitule"><?php echo trad("NOTE_LIB_EMPLACEMENT");?></TD>
    <TD class="tabInput"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
    <?php
      $DB_CX->DbQuery("SELECT empl_id, empl_nom FROM ${PREFIX_TABLE}emplacement WHERE empl_util_id=".$idUser.(($MODIF_PARTAGE) ? " OR (empl_util_id!=".$idUser." AND empl_partage='O')" : "")." ORDER BY empl_nom");
    if ($DB_CX->DbNumRows()) {
      echo ("      <TR>
    <TD nowrap><SELECT name=\"zlLieu\" onchange=\"javascript: addEmpl(this);\">
    <OPTION value=\"0\">-- ".trad("EMPL_NOTE_PERSO")." --</OPTION>");
      while ($listEmpl = $DB_CX->DbNextRow()) {
        $selected = ($id == $listEmpl['empl_id']) ? " selected" : "";
        echo "      <OPTION value=\"".$listEmpl['empl_id']."\"".$selected.">".htmlspecialchars($listEmpl['empl_nom'])."</OPTION>\n";
      }
      echo ("        </SELECT></TD>
      </TR>\n");
    }
    ?>
  <TR>
    <TD nowrap width="470" height="18"><INPUT type="text" class="Texte" name="ztLieu" value="<?php echo htmlspecialchars($enr['age_lieu']); ?>" style="width:469px" maxlength="230"></TD>
  </TR>
  </TABLE></TD>
  <!-- Fin Mod Emplacement Plus -->
  <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
    <TD class="tabIntitule"><?php echo trad("NOTE_LIB_DETAIL");?></TD>
    <TD class="tabInput" nowrap><?php genereTextArea("ztDetail",$enr['age_detail'],469,7); ?></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>" height="21">
    <TD class="tabIntitule"><?php echo trad("NOTE_LIB_DATE");?></TD>
    <TD class="tabInput" nowrap><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
      <TR>
        <TD><INPUT type="text" class="Texte" name="ztDateNote" id="ztDateNote" size=12 maxlength=10 value="<?php echo $enr['ageDate']; ?>" title="<?php echo trad("NOTE_FORMAT_DATE");?>" onKeyPress="return onlyChar(event);">&nbsp;<INPUT type="button" id="btCalNote" value="..." class="Picklist" style="height:16px" title="<?php echo trad("NOTE_AFFICHE_CALENDRIER");?>">&nbsp;&nbsp;<I>(<?php echo trad("NOTE_FORMAT_DATE");?>)</I></TD>
        <TD nowrap><LABEL for="allDay"><INPUT type="checkbox" class="case" name="ckTypeNote" id="allDay" value="3" onClick="affPlageHoraire(this.checked)"<?php if ($enr['age_aty_id']==3) echo " checked"; ?>>&nbsp;<?php echo trad("LIBELLE_JOURNEE_ENTIERE");?></LABEL></TD>
      </TR>
    </TABLE></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>" height="21">
    <TD class="tabIntitule"><?php echo trad("NOTE_LIB_HORAIRES");?>
      <BR><SPAN style="font-weight:normal;font-size:0.9em;"><?php echo sprintf(trad("NOTE_FUSEAU_ACTUEL"), (($tzGmt<0) ? "-" : "+").afficheHeure(floor(abs($tzGmt)),abs($tzGmt))); ?></SPAN></TD>
    <TD class="bordTLRB" nowrap><DIV id="plageHoraireFull" style="display:<?php echo ($enr['age_aty_id']==3) ? "block" : "none"; ?>;padding:2px;"><?php echo trad("COMMUN_JOURNEE_ENTIERE");?></DIV>
      <DIV id="plageHoraire" style="display:<?php echo ($enr['age_aty_id']==3) ? "none" : "block"; ?>;padding-left:2px;padding-top:1px;"><TABLE cellspacing="0" cellpadding="0" border="0" width="98%">
      <TR>
        <TD width="33%" nowrap><?php echo trad("NOTE_HEURE_DEBUT");?>&nbsp;<SELECT name="zlHeureDebut" size="1" onchange="javascript: ajustHeureFin();">
<?php
    for ($i=0; $i<24;$i=$i+0.25) {
      $selected = ($i == $enr['age_heure_debut']) ? " selected" : "";
      echo "          <OPTION value=\"".$i."\"".$selected.">".afficheHeure($i,$i,$formatHeure)."</OPTION>\n";
    }
?>
        </SELECT></TD>
        <TD width="33%"><?php echo trad("NOTE_DUREE");?>&nbsp;<SELECT name="zlHeureDuree" size="1" onchange="javascript: ajustHeureFin();">
<?php
    for ($i=0.25; $i<=24;$i=$i+0.25) {
      $selected = ($i == ($dureeNote/4)) ? " selected" : "";
      $val = afficheHeure($i,$i);
      echo "          <OPTION value=\"".$i."\"".$selected.">".($val=="00:00" ? "24:00" : $val)."</OPTION>\n";
    }
?>
        </SELECT></TD>
        <TD width="33%"><?php echo trad("NOTE_HEURE_FIN");?>&nbsp;<SELECT name="zlHeureFin" size="1" onchange="javascript: ajustHeureDuree();">
<?php
    for ($i=0.25; $i<=24;$i=$i+0.25) {
      $selected = ($i == $enr['age_heure_fin']) ? " selected" : "";
      echo "          <OPTION value=\"".$i."\"".$selected.">".afficheHeure($i,$i,$formatHeure)."</OPTION>\n";
    }
?>
        </SELECT></TD>
      </TR>
    </TABLE></DIV></TD>
  </TR>
<?php
    if ($nbAffect>1) {
      // Liste des personnes selectionnees via le module des disponibilites
      if (!empty($sChoix))
        $tabConcerne = explode(",", $sChoix);
      // Selection automatique de l'utilisateur pour la creation d'une note
      elseif (!$id)
        $tabConcerne[] = $USER_SUBSTITUE;
      // Sinon recupere la liste des personnes concernees par la note
      else {
        $DB_CX->DbQuery("SELECT aco_util_id FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$id);
        while ($rsParticipe = $DB_CX->DbNextRow())
          $tabConcerne[] = $rsParticipe['aco_util_id'];
      }
?>
  <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
    <TD class="tabIntitule" nowrap><?php echo trad("NOTE_LIB_PERSONNE_CONCERNEE");?></TD>
    <TD class="tabInput"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
      <TR>
        <TH><?php echo trad("NOTE_PERSONNES_POSSIBLES");?></TH>
        <TH>&nbsp;</TH>
        <TH><?php echo trad("NOTE_PERSONNES_SELECTION");?></TH>
      </TR>
      <TR>
        <TD><SELECT name="zlUtilisateur" id="zlUtilisateur" size="8" multiple style="width:210px;">
<?php
      while ($rsUtil = $DB->DbNextRow()) {
        $selected = "";
        for ($i=0; $i<count($tabConcerne) && empty($selected); $i++) {
          if ($tabConcerne[$i] == $rsUtil['util_id'])
            $selected = " selected";
        }
        echo "          <OPTION value=\"".$rsUtil['util_id']."\"".$selected.">".$rsUtil['nomUtil']."</OPTION>\n";
      }
?>
        </SELECT></TD>
        <TD align="center" valign="middle" width="48"><TABLE border=0 cellpadding=0 cellspacing=0>
          <TR>
            <TD>&nbsp;<INPUT type="button" class="PickList" name="btSelect" id="btSelect" value="&#155;" title="<?php echo trad("NOTE_BT_AJOUT_SELECTION");?>" onClick="javascript: selectUtil(document.Form1.zlUtilisateur, document.Form1.zlParticipant);">&nbsp;</TD>
          </TR>
          <TR>
            <TD height="7"></TD>
          </TR>
          <TR>
            <TD>&nbsp;<INPUT type="button" class="PickList" name="btSelect" id="btSelect" value="&#187;" title="<?php echo trad("NOTE_BT_AJOUT_TOUS");?>" onClick="javascript: selectAll(document.Form1.zlUtilisateur, document.Form1.zlParticipant);">&nbsp;</TD>
          </TR>
          <TR>
            <TD height="7"></TD>
          </TR>
          <TR>
            <TD nowrap>&nbsp;<INPUT type="button" class="PickList" name="btDeselect" id="btDeselect" value="&#139;" title="<?php echo trad("NOTE_BT_ENLEVE_SELECTION");?>" onClick="javascript: selectUtil(document.Form1.zlParticipant, document.Form1.zlUtilisateur);">&nbsp;</TD>
          </TR>
          <TR>
            <TD height="7"></TD>
          </TR>
          <TR>
            <TD nowrap>&nbsp;<INPUT type="button" class="PickList" name="btDeselect" id="btDeselect" value="&#171;" title="<?php echo trad("NOTE_BT_ENLEVE_TOUS");?>" onClick="javascript: selectAll(document.Form1.zlParticipant, document.Form1.zlUtilisateur);">&nbsp;</TD>
          </TR>
        </TABLE></TD>
        <TD><SELECT name="zlParticipant" id="zlParticipant" size="8" multiple style="width:210px;"></SELECT></TD>
      </TR>
    </TABLE><INPUT type="hidden" name="ztParticipant" value=""></TD>
  </TR>
<?php
    }

    // Recuperation des contacts de l'utilisateur et ceux qui sont partages
    $DB_CX->DbQuery("SELECT DISTINCT cal_id, LTRIM(CONCAT(cal_nom,' ',cal_prenom)) AS nomContact FROM ${PREFIX_TABLE}calepin WHERE cal_util_id=".$idUser." OR cal_partage='O' ORDER BY nomContact");
    // Le choix du contact n'est pas affiche si le calepin est vide
    if ($DB_CX->DbNumRows()) {
?>
  <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>" height="21">
    <TD class="tabIntitule" nowrap><?php echo trad("NOTE_LIB_CONTACT_ASSOCIE");?></TD>
    <TD class="tabInput"><SELECT name="zlContactAssocie" size="1">
      <OPTION value="0"><?php echo trad("NOTE_LIB_CONTACT_AUCUN");?></OPTION>
<?php
      $lettreCrt = "";
      while ($cal = $DB_CX->DbNextRow()) {
        // Premiere lettre
        if ($lettreCrt!=substr($cal['nomContact'],0,1)) {
          if ($lettreCrt!="") {
            echo "      </OPTGROUP>\n";
          }
          $lettreCrt = substr($cal['nomContact'],0,1);
          echo "      <OPTGROUP label=\"".htmlspecialchars($lettreCrt)."\">\n";
        }
        $selected = ($cal['cal_id']==$enr['age_cal_id']) ? " selected" : "";
        echo "        <OPTION value=\"".$cal['cal_id']."\"".$selected.">".htmlspecialchars($cal['nomContact'])."</OPTION>\n";
      }
      echo "      </OPTGROUP>\n";
?>
    </SELECT>&nbsp;<INPUT type="button" value="<?php echo trad("NOTE_BT_COPIE_CONTACT");?>" class="Bouton" onclick="javascript: copieInfoContact(document.Form1.zlContactAssocie.value);"></TD>
  </TR>
<?php
    }
?>
  <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>" height="21">
    <TD class="tabIntitule"><?php echo trad("NOTE_LIB_COULEUR");?></TD>
    <TD class="tabInput"><SELECT name="zlCouleur" style="background-color:<?php echo $enr['age_couleur'];?>;" onchange="javascript: changeCouleurListe(this,document.Form1.ztCouleur);">
<?php
    reset($tabCouleur);
    while (list($key, $val) = each($tabCouleur)) {
      $selected = ($val==$enr['age_couleur']) ? " selected" : "";
      echo "      <OPTION style=\"background-color:".$val.";\" value=\"".$val."\"".$selected.">".$key."</OPTION>\n";
    }
?>
    </SELECT>&nbsp;&nbsp;&nbsp;<INPUT type="text" name="ztCouleur" class="Texte" value="<?php echo trad("NOTE_APPARENCE_NOTE");?>" style="background:<?php echo $enr['age_couleur']; ?>; text-align:center; font-weight:bold; height:17px;" size=25 readonly tabindex="1000"></TD>
  </TR>
<?php
    if ($enr['age_prive']==1) {
      $rdPr1 = " checked";
      $rdPr2 = "";
    }
    else {
      $rdPr1 = "";
      $rdPr2 = " checked";
    }
?>
<?php      
  //  MODS menu note
?>  
    <TR id="trPartage" bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
<?php      
  //  MODS menu note
?>  
      <TD class="tabIntitule"><?php echo trad("NOTE_LIB_PARTAGE");?></TD>
    <TD class="tabInput" style="padding-left:0px;"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
      <TR>
        <TD height="20" nowrap><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"><LABEL for="priveKO"><INPUT type="radio" name="rdPrive" id="priveKO" value="0" class="Case"<?php echo $rdPr2; ?>>&nbsp;<?php echo trad("NOTE_PUBLIQUE");?></LABEL></TD>
      </TR>
      <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
        <TD height="20"><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"><LABEL for="priveOK"><INPUT type="radio" name="rdPrive" id="priveOK" value="1" class="Case"<?php echo $rdPr1; ?>>&nbsp;<?php echo trad("NOTE_PRIVEE");?></LABEL></TD>
      </TR>
    </TABLE></TD>
  </TR>
<?php
    if ($enr['age_disponibilite']==1) {
      $rdDp1 = "";
      $rdDp2 = " checked";
    }
    else {
      $rdDp1 = " checked";
      $rdDp2 = "";
    }
?>
<?php      
  //  MODS menu note
?>  
    <TR id="trDispo" bgcolor="<?php echo $bgColor[$iColor%2]; ?>">
<?php      
  //  MODS menu note
?>  
      <TD class="tabIntitule"><?php echo trad("NOTE_LIB_DISPO");?></TD>
    <TD class="tabInput" style="padding:0px;"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
      <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
        <TD height="20" nowrap><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"><LABEL for="dispoKO"><INPUT type="radio" name="rdDispo" id="dispoKO" value="0" class="Case"<?php echo $rdDp1; ?>>&nbsp;<?php echo trad("NOTE_OCCUPE");?></LABEL></TD>
      </TR>
      <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
        <TD height="20" nowrap><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"><LABEL for="dispoOK"><INPUT type="radio" name="rdDispo" id="dispoOK" value="1" class="Case"<?php echo $rdDp2; ?>>&nbsp;<?php echo trad("NOTE_LIBRE");?></LABEL></TD>
      </TR>
    </TABLE></TD>
  </TR>
<?php
    if ($enr['age_rappel']) {
      $rdR1 = "";
      $rdR2 = " checked";
    }
    else {
      $rdR1 = " checked";
      $rdR2 = "";
    }
?>
<?php      
  //  MODS menu note
?>  
    <TR id="trRappel" bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
<?php      
  //  MODS menu note
?>  
      <TD class="tabIntitule"><?php echo trad("NOTE_LIB_RAPPEL");?></TD>
    <TD class="tabInput" style="padding-left:0px;"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
      <TR>
        <TD height="20" nowrap><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"><LABEL for="rappelKO"><INPUT type="radio" name="rdRappel" id="rappelKO" value="1" class="Case"<?php echo $rdR1; ?>>&nbsp;<?php echo trad("NOTE_AUCUN_RAPPEL");?></LABEL></TD>
      </TR>
      <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
        <TD height="20"><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"><LABEL for="rappelOK"><INPUT type="radio" name="rdRappel" id="rappelOK" value="2" class="Case"<?php echo $rdR2; ?>>&nbsp;<?php echo trad("COMMUN_LIB_RAPPEL");?></LABEL>&nbsp;<SELECT name="zlR1" onFocus="document.Form1.rdRappel[1].checked='true';">
<?php
    if (!$enr['age_rappel'])
      $enr['age_rappel']=5;
    for ($i=1;$i<60;$i++) {
      $selected = ($enr['age_rappel']==$i) ? " selected" : "";
      echo "          <OPTION value=\"".$i."\"".$selected.">".$i."</OPTION>\n";
    }
?>
        </SELECT>
        <SELECT name="zlR2" onFocus="document.Form1.rdRappel[1].checked='true';">
          <OPTION value="1"<?php if ($enr['age_rappel_coeff']==1) echo " selected"; ?>><?php echo trad("COMMUN_MINUTE");?></OPTION>
          <OPTION value="60"<?php if ($enr['age_rappel_coeff']==60) echo " selected"; ?>><?php echo trad("COMMUN_HEURE");?></OPTION>
          <OPTION value="1440"<?php if ($enr['age_rappel_coeff']==1440) echo " selected"; ?>><?php echo trad("COMMUN_JOUR");?></OPTION>
        </SELECT> <?php echo trad("COMMUN_AVANCE");?><?php if (function_exists("mail")) { ?>
        <BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo trad("NOTE_EMAIL_RAPPEL");?>
        &nbsp;&nbsp;&nbsp;<LABEL for="email"><INPUT type="checkbox" name="ckEmail" value="1" class="Case" id="email"<?php if ($enr['age_email']==1) echo " checked"; ?>>&nbsp;<?php echo trad("NOTE_EMAIL_RAPPEL_PHENIX");?></LABEL>
        &nbsp;&nbsp;&nbsp;<LABEL for="emailContact"><INPUT type="checkbox" name="ckEmailContact" value="1" class="Case" id="emailContact"<?php if ($enr['age_email_contact']!=0) echo " checked"; ?>>&nbsp;<?php echo trad("NOTE_EMAIL_RAPPEL_CONTACT");?></LABEL><?php } ?>
        </TD>
      </TR>
    </TABLE></TD>
  </TR>
<?php
    // Affichage abrege en cas de modification d'une occurrence d'une note recurrente
    // Dans ce cas l'utilisateur ne peut pas modifier les options de periodicite
    if ($edit!="occ") {
?>
<?php      
  //  MODS menu note
?>  
    <TR id="trPeriod" >
<?php      
  //  MODS menu note
?>  
      <TD class="tabIntitule" bgcolor="<?php echo $bgColor[$iColor%2]; ?>"><?php echo trad("NOTE_LIB_PERIODICITE");?></TD>
<?php
      $periodicite = $enr['age_ape_id'] + 0;
      if (!$periodicite)
        $periodicite = 1;

      $d = date("j",$sd);
      $j = date("w",$sd);
      $m = date("n",$sd);
      $rdQ1 = " checked"; $rdQ2 = ""; $ztQ = "1";
      $ztH = "1";
      $rdM1 = " checked"; $rdM2 = ""; $zlM1 = $d; $zlM2 = "0"; $zlM3 = $j; $ztM = "1";
      $rdA1 = " checked"; $rdA2 = ""; $zlA1 = $d; $zlA2 = $m; $zlA3 = "0"; $zlA4 = $j; $zlA5 = $m;
      $rdP1 = " checked"; $rdP2 = ""; $ztP = "10"; $ztDateFin = date("d/m/Y",mktime(0,0,0,$m+1,$d,date("Y",$sd)));
      // Recuperation de la semaine type de l'utilisateur SUBSTITUE
      $DB_CX->DbQuery("SELECT util_semaine_type FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$USER_SUBSTITUE);
      $vSemaineType = $DB_CX->DbResult(0,0);
      if ($periodicite > 1) {
        switch ($periodicite) {
          case 2 :
            if ($enr['age_periode1'] == 1) { $ztQ = $enr['age_periode2']; }
            else { $rdQ1 = ""; $rdQ2 = " checked"; }
            break;
          case 3 : $ztH = ($enr['age_periode1']<1) ? 1 : $enr['age_periode1'];
            $vSemaineType = $enr['age_periode2'];
            //Quand on recupere la semaine type dans la note les premiers 0 ne sont pas recuperes a cause du type du champ (INT et pas VARCHAR) => on corrige
            for ($i=strlen($vSemaineType);$i<7;$i++) {
              $vSemaineType = "0".$vSemaineType;
            }
            // On transforme ensuite le format PHP (Dim a Sam) en format Phenix (Lun a Dim)
            $vSemaineType = substr($vSemaineType,1).substr($vSemaineType,0,1);
            break;
          case 4 :
            if ($enr['age_periode1'] == 1) { $zlM1 = $enr['age_periode2']; }
            else { $rdM1 = ""; $rdM2 = " checked"; $zlM2 = $enr['age_periode2']; $zlM3 = $enr['age_periode3']; }
            $ztM = $enr['age_periode4'];
            break;
          case 5 :
            if ($enr['age_periode1'] == 1) { $zlA1 = $enr['age_periode2']; $zlA2 = $enr['age_periode3']; }
            else { $rdA1 = ""; $rdA2 = " checked"; $zlA3 = $enr['age_periode2']; $zlA4 = $enr['age_periode3']; $zlA5 = $enr['age_periode4']; }
            break;
        }
        if ($enr['age_plage']==1)
          $ztP = $enr['age_plage_duree'];
        elseif ($enr['age_plage']==2) {
          $rdP1 = "";
          $rdP2 = " checked";
          $ztDateFin = date("d/m/Y",$enr['age_plage_duree']);
        }
      }
      // Generation des variables pour les jours a cocher dans la periodicite quotidienne
      for ($i=1; $i<8; $i++) {
        ${"bt".$i} = substr($vSemaineType,$i-1,1);
      }
?>

    <TD class="tabInput" style="padding:0px;" nowrap><TABLE cellspacing="0" cellpadding="0" width="100%" border="0" bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
      <TR height="20">
        <TD width="2" nowrap><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"></TD>
        <TD colspan="5" width="100%" nowrap><?php echo trad("NOTE_REPETITION");?> : <SELECT name="zlPeriodicite" id="zlPeriodicite" onChange="javascript: affPeriodicite(this.value);">
          <OPTION value="1"<?php if ($periodicite==1) echo " selected"; ?>><?php echo trad("NOTE_REPETITION_AUCUNE");?></OPTION>
          <OPTION value="2"<?php if ($periodicite==2) echo " selected"; ?>><?php echo trad("NOTE_REPETITION_QUOTIDIENNE");?></OPTION>
          <OPTION value="3"<?php if ($periodicite==3) echo " selected"; ?>><?php echo trad("NOTE_REPETITION_HEBDOMADAIRE");?></OPTION>
          <OPTION value="4"<?php if ($periodicite==4) echo " selected"; ?>><?php echo trad("NOTE_REPETITION_MENSUELLE");?></OPTION>
          <OPTION value="5"<?php if ($periodicite==5) echo " selected"; ?>><?php echo trad("NOTE_REPETITION_ANNUELLE");?></OPTION>
        </SELECT></TD>
      </TR>
      </TABLE>
      <DIV id="detailJour" style="display:<?php echo ($periodicite==2) ? "block" : "none"; ?>"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
        <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>" height="40">
          <TD width="2" nowrap><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"></TD>
          <TD width="100%"><LABEL for="quot1"><INPUT type="radio" name="rdQ" id="quot1" value="1" class="Case"<?php echo $rdQ1; ?>> <?php echo trad("NOTE_TOUS_LES");?></LABEL> <INPUT type="text" class="Texte" name="ztQ" value="<?php echo $ztQ; ?>" size="2" maxlength="2" onKeyPress="return onlyChar(event);" onFocus="document.Form1.rdQ[0].checked='true';">&nbsp;<?php echo trad("NOTE_JOURS");?><BR>
            <LABEL for="quot2"><INPUT type="radio" name="rdQ" id="quot2" value="2" class="Case"<?php echo $rdQ2; ?>> <?php echo trad("NOTE_JOURS_OUVRABLES");?></LABEL></TD>
        </TR>
      </TABLE></DIV>
      <DIV id="detailSemaine" style="display:<?php echo ($periodicite==3) ? "block" : "none"; ?>"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
        <TR bgcolor="<?php echo $bgColor[$iColor%2]; ?>" height="20">
          <TD width="3" nowrap><IMG src="image/trans.gif" alt="" width="3" height="1" border="0"></TD>
          <TD width="100%"><?php echo trad("NOTE_TOUTES_LES");?> <INPUT type="text" class="Texte" name="ztH" value="<?php echo $ztH; ?>" size="2" maxlength="2" onKeyPress="return onlyChar(event);">&nbsp;<?php echo trad("NOTE_SEMAINES_LE");?><BR>
          <LABEL for="lundi"><INPUT type="checkbox" name="bt1" value="1" tabindex="<?php echo $tabIndex++; ?>"<?php if ($bt1==1) echo " checked"; ?> class="case" id="lundi">&nbsp;<?php echo trad("NOTE_LUN");?></LABEL>&nbsp;&nbsp;
            <LABEL for="mardi"><INPUT type="checkbox" name="bt2" value="1" tabindex="<?php echo $tabIndex++; ?>"<?php if ($bt2==1) echo " checked"; ?> class="case" id="mardi">&nbsp;<?php echo trad("NOTE_MAR");?></LABEL>&nbsp;&nbsp;
            <LABEL for="mercredi"><INPUT type="checkbox" name="bt3" value="1" tabindex="<?php echo $tabIndex++; ?>"<?php if ($bt3==1) echo " checked"; ?> class="case" id="mercredi">&nbsp;<?php echo trad("NOTE_MER");?></LABEL>&nbsp;&nbsp;
            <LABEL for="jeudi"><INPUT type="checkbox" name="bt4" value="1" tabindex="<?php echo $tabIndex++; ?>"<?php if ($bt4==1) echo " checked"; ?> class="case" id="jeudi">&nbsp;<?php echo trad("NOTE_JEU");?></LABEL>&nbsp;&nbsp;
            <LABEL for="vendredi"><INPUT type="checkbox" name="bt5" value="1" tabindex="<?php echo $tabIndex++; ?>"<?php if ($bt5==1) echo " checked"; ?> class="case" id="vendredi">&nbsp;<?php echo trad("NOTE_VEN");?></LABEL>&nbsp;&nbsp;
            <LABEL for="samedi"><INPUT type="checkbox" name="bt6" value="1" tabindex="<?php echo $tabIndex++; ?>"<?php if ($bt6==1) echo " checked"; ?> class="case" id="samedi">&nbsp;<?php echo trad("NOTE_SAM");?></LABEL>&nbsp;&nbsp;
          <LABEL for="dimanche"><INPUT type="checkbox" name="bt7" value="1" tabindex="<?php echo $tabIndex++; ?>"<?php if ($bt7==1) echo " checked"; ?> class="case" id="dimanche">&nbsp;<?php echo trad("NOTE_DIM");?></LABEL></TD>
        </TR>
      </TABLE></DIV>
      <DIV id="detailMois" style="display:<?php echo ($periodicite==4) ? "block" : "none"; ?>"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
        <TR bgcolor="<?php echo $bgColor[$iColor%2]; ?>" height="20">
          <TD width="2" nowrap><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"></TD>
          <TD width="100%"><?php echo trad("NOTE_TOUS_LES");?> <INPUT type="text" class="Texte" name="ztM" value="<?php echo $ztM; ?>" size="2" maxlength="2" onKeyPress="return onlyChar(event);">&nbsp;<?php echo trad("NOTE_MOIS");?><BR>
          <LABEL for="mois1"><INPUT type="radio" name="rdM" id="mois1" value="1" class="Case"<?php echo $rdM1; ?>> <?php echo trad("NOTE_LE");?></LABEL> <SELECT name="zlM1" onFocus="document.Form1.rdM[0].checked='true';">
<?php
      for ($i=1;$i<32;$i++) {
        $selected = ($zlM1==$i) ? " selected" : "";
        echo "            <OPTION value=\"".$i."\"".$selected.">".$i."</OPTION>\n";
      }
?>
          </SELECT> <?php echo trad("NOTE_CHAQUE_MOIS");?><BR>
          <LABEL for="mois2"><INPUT type="radio" name="rdM" id="mois2" value="2" class="Case"<?php echo $rdM2; ?>> <?php echo trad("NOTE_LE");?></LABEL> <SELECT name="zlM2" onFocus="document.Form1.rdM[1].checked='true';" onchange="javascript: ajouteOptionMensuel(document.Form1);">
            <OPTION value="0"<?php if ($zlM2==0) echo " selected"; ?>><?php echo trad("NOTE_PREMIER");?></OPTION>
            <OPTION value="1"<?php if ($zlM2==1) echo " selected"; ?>><?php echo trad("NOTE_DEUXIEME");?></OPTION>
            <OPTION value="2"<?php if ($zlM2==2) echo " selected"; ?>><?php echo trad("NOTE_TROISIEME");?></OPTION>
            <OPTION value="3"<?php if ($zlM2==3) echo " selected"; ?>><?php echo trad("NOTE_QUATRIEME");?></OPTION>
            <OPTION value="4"<?php if ($zlM2==4) echo " selected"; ?>><?php echo trad("NOTE_DERNIER");?></OPTION>
          </SELECT>&nbsp;<SELECT name="zlM3" onFocus="document.Form1.rdM[1].checked='true';">
<?php
      for ($i=0;$i<7;$i++) {
        $selected = ($zlM3==$i) ? " selected" : "";
        echo "            <OPTION value=\"".$i."\"".$selected.">".$tabJour[$i]."</OPTION>\n";
      }
      if ($zlM2==4) {
        $selected = ($zlM3==9) ? " selected" : "";
        echo "            <OPTION value=\"9\"".$selected.">".trad("NOTE_JOUR")."</OPTION>\n";
      }
?>
          </SELECT> <?php echo trad("NOTE_DU_MOIS");?></TD>
        </TR>
      </TABLE></DIV>
      <DIV id="detailAnnee" style="display:<?php echo ($periodicite==5) ? "block" : "none"; ?>"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
        <TR bgcolor="<?php echo $bgColor[$iColor%2]; ?>" height="20">
          <TD width="2" nowrap><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"></TD>
          <TD width="100%"><LABEL for="annee1"><INPUT type="radio" name="rdA" id="annee1" value="1" class="Case"<?php echo $rdA1; ?>> <?php echo trad("NOTE_TOUS_LES");?></LABEL> <SELECT name="zlA1" onFocus="document.Form1.rdA[0].checked='true';">
<?php
      for ($i=1;$i<32;$i++) {
        $selected = ($zlA1==$i) ? " selected" : "";
        echo "            <OPTION value=\"".$i."\"".$selected.">".$i."</OPTION>\n";
      }
?>
          </SELECT>&nbsp;<SELECT name="zlA2" onFocus="document.Form1.rdA[0].checked='true';">
<?php
      for ($i=1;$i<13;$i++) {
        $selected = ($zlA2==$i) ? " selected" : "";
        echo "            <OPTION value=\"".$i."\"".$selected.">".$tabMois[$i]."</OPTION>\n";
      }
?>
          </SELECT><BR>
          <LABEL for="annee2"><INPUT type="radio" name="rdA" id="annee2" value="2" class="Case"<?php echo $rdA2; ?>> <?php echo trad("NOTE_LE");?></LABEL> <SELECT name="zlA3" onFocus="document.Form1.rdA[1].checked='true';">
            <OPTION value="0"<?php if ($zlA3==0) echo " selected"; ?>><?php echo trad("NOTE_PREMIER");?></OPTION>
            <OPTION value="1"<?php if ($zlA3==1) echo " selected"; ?>><?php echo trad("NOTE_DEUXIEME");?></OPTION>
            <OPTION value="2"<?php if ($zlA3==2) echo " selected"; ?>><?php echo trad("NOTE_TROISIEME");?></OPTION>
            <OPTION value="3"<?php if ($zlA3==3) echo " selected"; ?>><?php echo trad("NOTE_QUATRIEME");?></OPTION>
            <OPTION value="4"<?php if ($zlA3==4) echo " selected"; ?>><?php echo trad("NOTE_DERNIER");?></OPTION>
          </SELECT>&nbsp;<SELECT name="zlA4" onFocus="document.Form1.rdA[1].checked='true';">
<?php
      for ($i=0;$i<7;$i++) {
        $selected = ($zlA4==$i) ? " selected" : "";
        echo "            <OPTION value=\"".$i."\"".$selected.">".$tabJour[$i]."</OPTION>\n";
      }
?>
          </SELECT> de <SELECT name="zlA5" onFocus="document.Form1.rdA[1].checked='true';">
<?php
      for ($i=1;$i<13;$i++) {
        $selected = ($zlA5==$i) ? " selected" : "";
        echo "            <OPTION value=\"".$i."\"".$selected.">".$tabMois[$i]."</OPTION>\n";
      }
?>
          </SELECT></TD>
        </TR>
      </TABLE></DIV>
      <DIV id="plagePeriodicite" style="display:<?php echo ($periodicite!=1) ? "block" : "none"; ?>"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
        <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
          <TD height="20"><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"><?php echo trad("NOTE_DATE_FIN");?> :</TD>
        </TR>
        <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>">
          <TD height="20"><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"><INPUT type="radio" name="rdPlage" id="finApres" value="1" class="Case"<?php echo $rdP1; ?>><LABEL for="finApres">&nbsp;<?php echo trad("NOTE_FIN_APRES");?>&nbsp;</LABEL><INPUT type="text" class="Texte" name="ztP" value="<?php echo $ztP; ?>" size="2" maxlength="2" onKeyPress="return onlyChar(event);" onFocus="document.Form1.rdPlage[0].checked='true';">&nbsp;<?php echo trad("NOTE_OCCURENCES");?></TD>
        </TR>
        <TR bgcolor="<?php echo $bgColor[$iColor%2]; ?>">
          <TD><IMG src="image/trans.gif" alt="" width="2" height="1" border="0"><INPUT type="radio" name="rdPlage" id="finLe" value="2" class="Case"<?php echo $rdP2; ?>><LABEL for="finLe">&nbsp;<?php echo trad("NOTE_FIN_LE");?>&nbsp;
          <INPUT type="text" class="Texte" name="ztDateFin" id="ztDateFin" size=12 maxlength=10 value="<?php echo $ztDateFin; ?>" title="<?php echo trad("NOTE_FORMAT_DATE");?>" onKeyPress="return onlyChar(event);" onFocus="document.Form1.rdPlage[1].checked='true';">&nbsp;<INPUT type="button" id="btCalFin" value="..." class="Picklist" style="height:16px" title="<?php echo trad("NOTE_AFFICHE_CALENDRIER");?>" onFocus="document.Form1.rdPlage[1].checked='true';">&nbsp;&nbsp;<I>(<?php echo trad("NOTE_FORMAT_DATE");?>)</I></TD>
        </TR>
      </TABLE></DIV>
    </TD>
  </TR>
<?php
    // Fin de l'affichage abrege en cas de modification d'une occurrence d'une note recurrente
    }
?>
<?php
  // MOD Copie note par mail
?>
  <TR bgcolor="<?php echo $bgColor[++$iColor%2]; ?>" height="21">
    <TD class="tabIntitule"><?php echo trad("NOTE_LIB_EMAIL_COPIE");?></TD>
    <TD class="tabInput" nowrap><INPUT type="text" class="Texte" name="ztEmailCopie" value="<?php echo htmlspecialchars($enr['age_email_copie']); ?>" style="width:469px" maxlength="230"></TD>
  </TR>
<?php
  // Fin MOD Copie note par mail
?>
  </TABLE>
  <BR><CENTER>
    <INPUT type="button" name="btEnregistre" value="<?php echo trad("NOTE_BT_ENREGISTRER");?>" onClick="javascript: return saisieOK(document.Form1);" class="Bouton">&nbsp;&nbsp;&nbsp;<?php if ($ztAction == "INSERT") { ?>
    <INPUT type="button" name="btRecommence" value="<?php echo trad("NOTE_BT_RECOMMENCER");?>" onClick="javascript: recommence(document.Form1);" class="Bouton">&nbsp;&nbsp;&nbsp;<?php } ?>
    <INPUT type="button" name="btAnnule" value="<?php echo trad("NOTE_BT_ANNULER");?>" onclick="javascript: btAnnul();" class="Bouton">&nbsp;&nbsp;&nbsp;
    <?php if (($ztAction == "UPDATE") and (($enr['age_util_id'] == $idUser) or ($droit_NOTES >= _DROIT_NOTE_COMPLET))) { ?><INPUT type="button" name="btSupprime" value="<?php echo trad("NOTE_BT_SUPPRIMER");?>" onclick="javascript: supprOcc('<?php echo $id; ?>','<?php echo (($edit == "occ") ? "0" : "1"); ?>');" class="Bouton">&nbsp;&nbsp;&nbsp;<?php } ?>
    <?php if ($Autoriser_Modif_Affect && $ztAction=="UPDATE" && $edit!="occ") { ?><INPUT type="button" class="Bouton" name="btDuplique" value="<?php echo trad("NOTE_BT_DUPLIQUER");?>" onclick="javascript: dupNote(document.Form1);"><?php } ?>
    </CENTER>
  </FORM>
  <SCRIPT type="text/javascript">
  <!--
    Calendar.setup( {
      inputField : "ztDateNote",    // ID of the input field
      ifFormat   : "%d/%m/%Y",  // the date format
      button     : "btCalNote"      // ID of the button
    } );
<?php
    if ($edit!="occ") {
?>

    Calendar.setup( {
      inputField : "ztDateFin",    // ID of the input field
      ifFormat   : "%d/%m/%Y",  // the date format
      button     : "btCalFin"      // ID of the button
    } );
<?php
    }
?>
    document.Form1.ztLibelle.focus();
  //-->
  </SCRIPT>
<?php
  }
?>
<!-- FIN MODULE GESTION DES NOTES -->
