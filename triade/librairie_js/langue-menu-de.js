/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E. 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
// Nicht mehr als 24 Zeichen // Ne pas d&eacute;passer 24 caract&egrave;res

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}


langtitre0="TRIADE-COPILOT"; // Keep as is
langtitre1="Aktuelles"; // "Actualit&eacute;" -> "Aktuelles"
langtitre2="Triade Service"; // "Service Triade" -> "Triade Service"
langtitre3="Forum"; // Keep as is
langtitre4="Suchen"; // "Rechercher" -> "Suchen"
langtitre5="Verlassen"; // "Quitter" -> "Verlassen"
langtitre6="Unterstützung"; // "Assistance" -> "Unterstützung"
langtitre7="Administrator&nbsp;Triade"; // "Administrateur&nbsp;Triade" -> "Administrator&nbsp;Triade"
//-------
langmenuparent1="Nachrichten"; // "Messagerie" -> "Nachrichten"
langmenuparent11="Schreiben"; // "Ecrire" -> "Schreiben"
langmenuparent12="Empfang"; // "R&eacute;ception" -> "Empfang"
langmenuparent13="Nachrichten des Hauptlehrers"; // "Messages du P. Principal" -> "Nachrichten des Hauptlehrers"
//-------
langmenuparent2="Schulleben"; // "Vie Scolaire" -> "Schulleben"
langmenuparent21="Noten"; // "Notes" -> "Noten"
langmenuparent22="Verspätungen"; // "Retards" -> "Verspätungen"
langmenuparent23="Abwesenheiten"; // "Absences" -> "Abwesenheiten"
langmenuparent24="Befreiungen"; // "Dispenses" -> "Befreiungen"
langmenuparent25="Disziplin"; // "Discipline" -> "Disziplin"
langmenuparent26="Hausaufgaben"; // "Devoirs maison" -> "Hausaufgaben"
langmenuparent27="Textbuch"; // "Cahier de textes" -> "Textbuch"
//-------
langmenuparent3=INTITULEDIRECTION; // Keep variable
langmenuparent31="Stundenplan"; // "Emploi du temps" -> "Stundenplan"
langmenuparent32="Verwaltungsrundschreiben"; // "Circulaires adm." -> "Verwaltungsrundschreiben"
langmenuparent33="Delegierte"; // "D&eacute;l&eacute;gu&eacute;s" -> "Delegierte"
langmenuparent34="Kalender"; // "Calendrier" -> "Kalender"
langmenuparent35="D.S.T."; // Keep as is
//------
// #########################
langmenuprof1="Nachrichten"; // "Messagerie" -> "Nachrichten"
langmenuprof11="Schreiben"; // "Ecrire" -> "Schreiben"
langmenuprof12="Empfang"; // "R&eacute;ception" -> "Empfang"
langmenuprof13="Gesendet"; // "Envoy&eacute;s" -> "Gesendet"
//------
langmenuprof2="Noten"; // "Notes" -> "Noten"
langmenuprof21="Aufgaben hinzufügen"; // "Ajouter devoirs" -> "Aufgaben hinzufügen"
langmenuprof22="Noten anzeigen"; // "Visualiser notes" -> "Noten anzeigen"
langmenuprof23="Aufgaben bearbeiten"; // "Modifier devoirs" -> "Aufgaben bearbeiten"
langmenuprof24="Aufgaben anzeigen"; // "Visualiser devoirs" -> "Aufgaben anzeigen"
langmenuprof25="Aufgaben löschen"; // "Supprimer devoirs" -> "Aufgaben löschen"
langmenuprof26="Schulaufgaben"; // "Devoirs scolaires" -> "Schulaufgaben"
langmenuprof27="Textbuch"; // "Cahier de textes" -> "Textbuch"
//------
langmenuprof3=""+INTITULEELEVE+"n"; // ""+INTITULEELEVE+"s" -> ""+INTITULEELEVE+"n" (adjust plural)
langmenuprof30="Verspätungen / Abwesenheiten "+intituleeleve+"n"; // "Rtd / Abs "+intituleeleve+"s" -> "Verspätungen / Abwesenheiten "+intituleeleve+"n"
langmenuprof32="Schülerakten "+intituleeleve+"n"; // "Fiches "+intituleeleve+"s" -> "Schülerakten "+intituleeleve+"n"
//------
langmenuprof4=INTITULEDIRECTION; // Keep variable
langmenuprof40="Raumreservierung"; // "R&eacute;servation salle" -> "Raumreservierung"
langmenuprof41="Stundenplan"; // "Emploi du Temps" -> "Stundenplan"
langmenuprof42="Verwaltungsrundschreiben"; // "Circulaires adm." -> "Verwaltungsrundschreiben"
langmenuprof43="Kalender"; // "Calendrier" -> "Kalender"
langmenuprof44="D.S.T."; // Keep as is
//------
// #########################
langmenuadmin00="Agenda"; // Keep as is
langmenuadmin0="Nachrichten"; // "Messagerie" -> "Nachrichten"
langmenuadmin01="News - Aktuelles"; // "News - Actualit&eacute;s" -> "News - Aktuelles"
langmenuadmin02="Schreiben"; // "Ecrire" -> "Schreiben"
langmenuadmin023="Papierkorb"; // "Corbeille" -> "Papierkorb"
langmenuadmin03="Empfang"; // "R&eacute;ception" -> "Empfang"
langmenuadmin04="Gesendet"; // "Envoy&eacute;s" -> "Gesendet"
langmenuadmin05="Audio News"; // "News Audio" -> "Audio News"
//------
langmenuadmin1="Verwaltung"; // "Gestion" -> "Verwaltung"
langmenuadmin11=INTITULEDIRECTION; // Keep variable
langmenuadmin12="Schulleben "; // "Vie Scolaire " -> "Schulleben "
langmenuadmin13=ucfirst(intituleenseignant)+"n"; // "ucfirst(intituleenseignant)+"s"" -> "ucfirst(intituleenseignant)+"n"" (adjust plural)
langmenuadmin14="Vertreter"; // "Suppl&eacute;ants" -> "Vertreter"
langmenuadmin15=INTITULEELEVE+"n"; // "INTITULEELEVE+"s"" -> "INTITULEELEVE+"n"" (adjust plural)
langmenuadmin16="Gruppen"; // "Groupes" -> "Gruppen"
langmenuadmin17=ucfirst(intituleclasse); // Keep as is
langmenuadmin18="Fächer"; // "Mati&egrave;res" -> "Fächer"
langmenuadmin19="Unterfächer"; // "Sous-mati&egrave;res" -> "Unterfächer"
//------
langmenuadmin2="Löschen"; // "Suppression" -> "Löschen"
//------
langmenuadmin3="Zuordnung"; // "Affectation" -> "Zuordnung"
langmenuadmin31="Stundenplan"; // "Emploi du temps" -> "Stundenplan"
langmenuadmin33="Einrichtung"; // "Mise en place" -> "Einrichtung"
langmenuadmin34="Visualisierung"; // "Visualisation" -> "Visualisierung"
langmenuadmin35="Änderung"; // "Modification" -> "Änderung"
langmenuadmin36="Löschen"; // "Suppression" -> "Löschen"
langmenuadmin37="Lehrer-Edition"; // "Edition Enseignant" -> "Lehrer-Edition"
//------
langmenuadmin4="Einrichtung"; // "Etablissement" -> "Einrichtung"
langmenuadmin40="Raumreservierung"; // "R&eacute;servation salle" -> "Raumreservierung"
langmenuadmin41="Gastronomie"; // "Restauration" -> "Gastronomie"
langmenuadmin42="Internat"; // "Internat" -> "Internat"
langmenuadmin43="C.D.I."; // Keep as is
//------
langmenuadmin5=""+INTITULEELEVE+"n"; // ""+INTITULEELEVE+"s"" -> ""+INTITULEELEVE+"n"" (adjust plural)
langmenuadmin51="Suche"; // "Recherche" -> "Suche"
langmenuadmin52="Liste"; // "Listing" -> "Liste"
langmenuadmin53="Zertifikatsverwaltung"; // "Gestion certificats" -> "Zertifikatsverwaltung"
langmenuadmin54="Gruppenverwaltung"; // "Gestion groupes" -> "Gruppenverwaltung"
langmenuadmin55="Notenbuch"; // "Carnet de notes" -> "Notenbuch"
langmenuadmin56="Krankenakte"; // "Dossier m&eacute;dical" -> "Krankenakte"
langmenuadmin57="D.S.T. Verwaltung"; // "Gestion D.S.T." -> "D.S.T. Verwaltung"
langmenuadmin58="Planungsverwaltung"; // "Gestion planning" -> "Planungsverwaltung"
langmenuadmin59="Abwesenheiten, Verspätungen des Tages"; // "Abs,retards du jour" -> "Abwesenheiten, Verspätungen des Tages"
langmenuadmin510="Abwesenheiten, Verspätungen Verwaltung"; // "Gestion abs,retards" -> "Abwesenheiten, Verspätungen Verwaltung"
langmenuadmin511="Befreiungen Verwaltung"; // "Gestion dispenses" -> "Befreiungen Verwaltung"
langmenuadmin512="Nachsitzen des Tages"; // "Retenues du jour" -> "Nachsitzen des Tages"
langmenuadmin513="Disziplinverwaltung"; // "Gestion discipline" -> "Disziplinverwaltung"
langmenuadmin514="Rundschreibenverwaltung"; // "Gestion circulaires" -> "Rundschreibenverwaltung"
langmenuadmin515="S.M.S."; // Keep as is
langmenuadmin516="W.A.P. Verwaltung"; // "Gestion W.A.P." -> "W.A.P. Verwaltung"
langmenuadmin517="Verwaltung Berufspraktikum"; // "Gestion Stage Pro" -> "Verwaltung Berufspraktikum"
langmenuadmin518="Trombinoskope"; // Keep as is
langmenuadmin519="Barcode"; // "Code barre" -> "Barcode"
// --------------
langmenuadmin6="Zeugnisse"; // "Bulletins" -> "Zeugnisse"
langmenuadmin61="Parametrierung"; // "Param&eacute;trage" -> "Parametrierung"
langmenuadmin62="Zeiträume T. definieren"; // "D&eacute;finir p&eacute;riodes T." -> "Zeiträume T. definieren"
langmenuadmin63="Zeugnisse drucken"; // "Imprimer bulletins" -> "Zeugnisse drucken"
langmenuadmin64="Zeiträume drucken"; // "Imprimer p&eacute;riodes" -> "Zeiträume drucken"
langmenuadmin65="Videoprojektor"; // "Vid&eacute;o-projecteur" -> "Videoprojektor"
langmenuadmin69="Visum Schulleben"; // "Visa vie scolaire" -> "Visum Schulleben"
// --------------
langmenuadmin7="Bannerverwaltung"; // "Gestion banni&egrave;res" -> "Bannerverwaltung"
langmenuadmin71="Banner hinzufügen"; // "Ajout banni&egrave;re" -> "Banner hinzufügen"
langmenuadmin72="Visualisierung"; // "Visualisation" -> "Visualisierung"
// --------------
langmenuadmin8="Einrichtung"; // "Etablissement" -> "Einrichtung"
langmenuadmin81="Importieren"; // "Importer" -> "Importieren"
langmenuadmin82="Aktualisierung"; // "Mise &agrave; jour" -> "Aktualisierung"
langmenuadmin83=""+INTITULEELEVE+" ohne Klasse"; // ""+INTITULEELEVE+" sans cls" -> ""+INTITULEELEVE+" ohne Klasse"
langmenuadmin84="Archivierung"; // "Archivage" -> "Archivierung"
langmenuadmin85="Überprüfung"; // "V&eacute;rification" -> "Überprüfung"
// --------------
langmenuadmin9="Neues Jahr"; // "Nouvelle ann&eacute;e" -> "Neues Jahr"
langmenuadmin91="Klassenwechsel"; // "Chgment de "+intituleclasse -> "Klassenwechsel"
langmenuadmin92="Infos bereinigen"; // "Purger infos" -> "Infos bereinigen"
// --------------
// #########################
langmenuscolaire0="Schulleben"; // "Vie Scolaire" -> "Schulleben"
langmenuscolaire01="Abwesenheiten, Verspätungen des Tages"; // "Abs,retards du jour" -> "Abwesenheiten, Verspätungen des Tages"
langmenuscolaire02="Abwesenheiten, Verspätungen Verwaltung"; // "Gestion abs,retards" -> "Abwesenheiten, Verspätungen Verwaltung"
langmenuscolaire03="Befreiungen Verwaltung"; // "Gestion dispenses" -> "Befreiungen Verwaltung"
langmenuscolaire04="Nachsitzen des Tages"; // "Retenues du jour" -> "Nachsitzen des Tages"
langmenuscolaire05="Disziplinverwaltung"; // "Gestion discipline" -> "Disziplinverwaltung"
// --------------
langmenuscolaire1=INTITULEDIRECTION; // Keep variable
langmenuscolaire11="Liste "+intituleeleve+"n"; // "Liste "+intituleeleve+"s" -> "Liste "+intituleeleve+"n" (adjust plural)
langmenuscolaire12="Stundenplan"; // "Emploi du temps" -> "Stundenplan"
langmenuscolaire13="Schülersuche "+intituleeleve+""; // "Recherche "+intituleeleve+"" -> "Schülersuche "+intituleeleve+""
langmenuscolaire14="Kalenderverwaltung"; // "Gestion calendrier" -> "Kalenderverwaltung"
langmenuscolaire15="Rundschreibenverwaltung"; // "Gestion circulaires" -> "Rundschreibenverwaltung"
langmenuscolaire16="D.S.T. Verwaltung"; // "Gestion D.S.T." -> "D.S.T. Verwaltung"
// #########################
// Fußzeile // pied de page
if (footer != "") {
	if (footerlien != "") {
		langmenupied="<a href='"+footerlien+"' target='_blank'>"+footer+"</a>";
	}else{
		langmenupied=footer;
	}
	// Translate the resolution text
	langmenupied+="<br>Um diese Seite optimal anzuzeigen: minimale Auflösung: 800x600 <br>"; // "Pour visualiser ce site de façon optimale : r&eacute;solution minimale : 800x600 <br>" -> "Um diese Seite optimal anzuzeigen: minimale Auflösung: 800x600 <br>"
}else{
	// Translate the full footer text
	langmenupied="<p> Die <b>T</b>ransparenz und die <b>R</b>aschheit der <b>I</b>nformatik im <b>D</b>ienste der <b>E</b>rziehung<br>Um diese Seite optimal anzuzeigen: minimale Auflösung: 800x600 <br> T.R.I.A.D.E. © 2026 - Alle Rechte vorbehalten"; // "La <b>T</b>ransparence et la <b>R</b>apidit&eacute; de l'<b>I</b>nformatique <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br>Pour visualiser ce site de façon optimale : r&eacute;solution minimale : 800x600 <br> T.R.I.A.D.E. © 2026 - Tous droits r&eacute;serv&eacute;s" -> "Die <b>T</b>ransparenz und die <b>R</b>aschheit der <b>I</b>nformatik im <b>D</b>ienste der <b>E</b>rziehung<br>Um diese Seite optimal anzuzeigen: minimale Auflösung: 800x600 <br> T.R.I.A.D.E. © 2026 - Alle Rechte vorbehalten"
}
// Image paths and alt texts remain the same as they are not language strings
img_logo_pied="<img src='./image/commun/triade-xhtml.jpg' alt='XHTML'>  <img src='./image/commun/triade-w3C.jpg' alt='w3C'> <img src='./image/commun/triade-css.png' alt='css' > <a href='http://www.triade-educ.com/accueil/don-triade.php' target='_blank' ><img border='0' src='./image/commun/triade_paypal.png' alt='Paypal' ></a><br /><br />";

// --------------
// #########################


langmenuadmin38="Stufenleiter"; // "Resp. Niveau" -> "Stufenleiter"
langmenuprof28="Zeugnis-Kommentare"; // "Comm. Bulletins" -> "Zeugnis-Kommentare"
langmenuadmin520="Studienverwaltung"; // "Gestion &eacute;tude" -> "Studienverwaltung"
langmenuprof45="Sanktionen "+intituleeleve+"n"; // "Sanctions "+intituleeleve+"s" -> "Sanktionen "+intituleeleve+"n" (adjust plural)
// Note: langmenuadmin38 is duplicated in the original, assuming the second one is the intended one for "Config Note USA"
langmenuadmin38="Konfiguration Note USA"; // "Config Note USA" -> "Konfiguration Note USA"
langmenuadmin44="Bestellhistorie"; // "Historique cmd" -> "Bestellhistorie"
langmenuadmin45="Diashow"; // "Diaporama" -> "Diashow"
langmenuadmin46="Parametrierung"; // "Param&eacute;trage" -> "Parametrierung"
langmenueleve1="Pädagogik"; // "P&eacute;dagogie" -> "Pädagogik"
langmenueleve2="Kurs / Lektion"; // "Cours / Leçon" -> "Kurs / Lektion"
langmenueleve3="Übungen"; // "Exercices" -> "Übungen"

langmenuadmin06="Speicher"; // "Stockage" -> "Speicher"
langmenuadmin07="Privater Bereich"; // "Espace Priv&eacute;" -> "Privater Bereich"


langmenuadmin66="Tabelle drucken"; // "Imprimer tableau" -> "Tabelle drucken"

// Image paths and alt texts remain the same as they are not language strings
img_logo_pied="<img src='./image/commun/triade-xhtml.jpg' alt='XHTML'>  <img src='./image/commun/triade-w3C.jpg' alt='w3C'> <img src='./image/commun/triade-css.png' alt='css' > <a href='http://www.triade-educ.com/accueil/don-triade.php' target='_blank' ><img border='0' src='./image/commun/triade_paypal.png' alt='Paypal' ></a><br /><br />";


langmenuadmin521="Meine RSS-Feeds"; // "Mes Flux RSS" -> "Meine RSS-Feeds"
langmenuadmin522="Eine SMS senden"; // "Envoyer un SMS" -> "Eine SMS senden"

langmenuadmin67="Zeugnisse überprüfen"; // "V&eacute;rifier bulletins" -> "Zeugnisse überprüfen"
langmenuadmin68="Visum Direktion"; // "Visa Direction" -> "Visum Direktion"

langmenuadmin523="Sanktionen des Tages"; // "Sanctions du jour" -> "Sanktionen des Tages"

if (VATEL == "1") {
	langmenuadmin70="Zusatz zum Diplom"; // "Suppl. au diplôme" -> "Zusatz zum Diplom"
}else{
	langmenuadmin70="Prüfungen / Brevets"; // "Examens / Brevets" -> "Prüfungen / Brevets"
}

langtitre2bis="Neues&nbsp;Fenster"; // "Nouvelle&nbsp;fen&ecirc;tre" -> "Neues&nbsp;Fenster"
langmenuprof29="Informationen"; // "Informations" -> "Informationen"
langmenuadmin73="Note Schulleben"; // "Note Vie Scolaire" -> "Note Schulleben"

langmenuadmin47="Zusatzmodule"; // "Modules annexes" -> "Zusatzmodule"
langmenuadmin48="Verlaufsheft"; // "Carnet de Suivi" -> "Verlaufsheft"
langmenuprof31="Hauptlehrer / Klassenlehrer"; // "Prof P. / Instituteur" -> "Hauptlehrer / Klassenlehrer"
langmenuadmin32="Hauptlehrer / Klassenlehrer"; // "Prof P. / Instituteur" -> "Hauptlehrer / Klassenlehrer"
langmenuadmin20="Praktikumsbetreuer"; // "Tuteurs de stage" -> "Praktikumsbetreuer"

// unkorrigiert // non corrig&eacute;

langmenuadmin40bis="Ausrüstungsreservierung"; // "R&eacute;servation &eacute;quip." -> "Ausrüstungsreservierung"
langmenuadmin41bis="Delegierte"; // "D&eacute;l&eacute;gu&eacute;s" -> "Delegierte"
langmenuparent36="Delegierte"; // "D&eacute;l&eacute;gu&eacute;s" -> "Delegierte"
langmenuprof46="Klassenplan "+intituleclasse; // "Plan de "+intituleclasse -> "Klassenplan "+intituleclasse
langmenuadmin524="Notanet"; // Keep as is
langmenuadmin39="Einzelgespräch"; // "Entretien individuel" -> "Einzelgespräch"
langmenuadmin525="Lehrer-Vergütung"; // "Vacation Ens." -> "Lehrer-Vergütung"

// --------------
langmenuadmin102="Voranmeldung"; // "Pr&eacute;-inscriptions" -> "Voranmeldung"
//-------


langmenuadmin101="Hausordnung"; // "R&eacute;glement interne" -> "Hausordnung"
langmenuprof47="Berufspraktikum."; // "Stage Pro." -> "Berufspraktikum."
langmenuadmin90="Buchhaltung"; // "Comptabilit&eacute;" -> "Buchhaltung"
langmenuadmin91bis="Statusblatt"; // "Fiche d'&eacute;tat" -> "Statusblatt"
langmenuadmin92bis="Inkasso"; // "Encaissement" -> "Inkasso"
langmenuadmin93="Mahnung"; // "Rappel" -> "Mahnung"
langmenuadmin94="Konfiguration Zeitplan"; // "Config. Ech&eacute;ancier" -> "Konfiguration Zeitplan"
langmenuadmin95="Quantifizierung"; // "Quantification" -> "Quantifizierung"
langmenuadmin96="Exportieren"; // "Exporter" -> "Exportieren"
langmenuadmin97="Unterschriftenliste"; // "Emargement" -> "Unterschriftenliste"
langmenuadmin98="Unterrichtseinheiten"; // "Unit&eacute;s enseignmts" -> "Unterrichtseinheiten"
langmenuadmin99="Neues Jahr"; // "Nouvelle ann&eacute;e" -> "Neues Jahr"
langmenuadmin100="Triade-MSN"; // Keep as is
langmenuadmin103="E-Learning"; // Keep as is
langmenuadmin525bis="Status Zahlung"; // "Etat Versement" -> "Status Zahlung"
langmenuadmin104="Personal"; // "Personnels" -> "Personal"
langmenueleve517="Berufspraktikum."; // "Stage Pro." -> "Berufspraktikum."
langmenueleve518="Serienbrief"; // "Publipostage" -> "Serienbrief"

langmenuadmin01A="News 1. Seite"; // "News 1er Page" -> "News 1. Seite"
langmenuadmin01B="News Aktuelles"; // "News actualit&eacute;" -> "News Aktuelles"
langmenuadmin01C="Laufende News"; // "News d&eacute;filant" -> "Laufende News"


langmenupersonnel1="Zugang Module"; // "Acc&egrave;s Modules" -> "Zugang Module"
langmenupersonnel2="Kantine"; // "Cantine" -> "Kantine"
langmenuadmin399="Lehrer-Gespräch"; // "Entretien Ens." -> "Lehrer-Gespräch"
langmenuadmin531="Praktikumszentrale"; // "Centrale Stages" -> "Praktikumszentrale"

langmenuadmin9000="Finanzmodul"; // "Module financier" -> "Finanzmodul"
langmenuadmin9001="Anmeldung"; // "Inscription" -> "Anmeldung"
langmenuadmin9002="Parametrierung"; // "Param&eacute;trage" -> "Parametrierung"
langmenuadmin9003="Zahlungen"; // "Paiements" -> "Zahlungen"
langmenuadmin9004="Editionen"; // "Editions" -> "Editionen"

langmenuadmin9100="Zimmermodul"; // "Module chambres" -> "Zimmermodul"
langmenuadmin9101="Planung"; // "Planning" -> "Planung"
langmenuadmin9102="Reservierungen"; // "Reservations" -> "Reservierungen"
langmenuadmin9103="Parametrierung"; // "Param&eacute;trage" -> "Parametrierung"

langmenuadmin022="Entwurf"; // "Brouillon" -> "Entwurf"
langmenuadmin055="Video News"; // "News Vid&eacute;o" -> "Video News"
langmenuadmin1011="Stipendiat"; // "Boursier" -> "Stipendiat"
langmenuadmin526="Schulmaterial"; // "Fourniture scolaire" -> "Schulmaterial"
langmenuadmin527="Ressourcenverwaltung"; // "Gestion ressources" -> "Ressourcenverwaltung"

langmenugeneral01="Ihr Konto"; // "Votre compte" -> "Ihr Konto"
langmenuparent34bis="Planung"; // "Planning" -> "Planung"
langmenuadmin528="Organigramm"; // "Organigramme" -> "Organigramm"
langmenuadmin529="Schuljahr"; // "Année Scolaire" -> "Schuljahr"
langmenuadmin911=intituleclasse+" vorherig"; // intituleclasse+" antérieure" -> intituleclasse+" vorherig"

langmenuadmin912="Sozialkompetenz"; // "Savoir-être" -> "Sozialkompetenz"
langmenuadmin913="Lehrer-Bewertung"; // "Eval. Enseignant" -> "Lehrer-Bewertung"

langmenugeneral01a="Memo"; // "M&eacute;mo" -> "Memo"


