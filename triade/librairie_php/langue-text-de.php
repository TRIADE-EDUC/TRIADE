<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  
 *   Site                 : http://www.triade-educ.org
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

/*                                              ### À l'attention du (futur) contributeur : ###

        La traduction allemande du logiciel TRIADE est le résultat d'une traduction automatisée par IA (avec Google Gemini)
        Si vous voyez une quelconque erreur de traduction (ou un manque de clarté dans les traductions), n'hézitez pas à nous contacter 
        par mail (contact@triade-educ.org), ou via le forum (https://forum.triade-educ.org/forum/ ), 
        ou sur le serveur Discord (https://triade-educ.org/fr/discord.php )

        Tout aide est la bienvenue, nous vous remercions d'avance pour votre contribution

        -- Deutsch version --
        Die deutsche Übersetzung der TRIADE-Software ist das Ergebnis einer automatisierten KI-Übersetzung (mit Google Gemini)
        Wenn Sie irgendwelche Übersetzungsfehler (oder Unklarheiten in den Übersetzungen) sehen, zögern Sie nicht, uns zu kontaktieren 
        per E-Mail (contact@triade-educ.org), oder über das Forum (https://forum.triade-educ.org/forum/ ), 
        oder auf dem Discord-Server (https://triade-educ.org/fr/discord.php ).

        Jede Hilfe ist willkommen, wir danken Ihnen im Voraus für Ihren Beitrag.

*/

// fichier pour langue cote admin.
// POUR TOUS -------------------
// brmozilla($_SESSION[navigateur]);
//

function TextNoAccentLicence2($str){
	$str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
        $str = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
        return($str);
}

if (!defined("INTITULEDIRECTION")) { define("INTITULEDIRECTION","Leitung"); }
if (!defined("INTITULEELEVE")) { define("INTITULEELEVE","Schüler"); }
if (!defined("INTITULEELEVES")) { define("INTITULEELEVES","Schüler"); } // Plural kann gleich sein
if (!defined("INTITULECLASSE")) { define("INTITULECLASSE","Klasse"); }
if (!defined("INTITULEENSEIGNANT")) { define("INTITULEENSEIGNANT","Lehrkraft"); } // Oder Lehrer/Lehrerin

define("CLICKICI","Hier klicken");
define("VALIDER","Bestätigen");
define("LANGTP22","INFORMATION - Antrag für D.S.T. zu bestätigen !"); // D.S.T. als Akronym beibehalten
define("LANGTP33"," DST Kalender ");
define("LANGCHOIX","-- Bitte auswählen --");
define("LANGCHOIX2","keine ".INTITULECLASSE);
define("LANGCHOIX3","-- Bitte auswählen --");
define("LANGOUI","Ja");
define("LANGNON","Nein");
define("LANGFERMERFEN","Fenster schließen");
define("LANGATT","ACHTUNG !");
define("LANGDONENR","Daten gespeichert");
define("LANGPATIENT","Bitte haben Sie einen Moment Geduld");
define("LANGSTAGE1",'Verwaltung der Berufspraktika');
define("LANGINCONNU",'unbekannt'); // doit être identique que langinconnu cote javascript
define("LANGABS",'abw'); // Abkürzung für abwesend
define("LANGRTD",'versp'); // Abkürzung für verspätet
define("LANGRIEN",'nichts');
define("LANGENR",'Speichern');
define("LANGRAS1",'Heute, den ');
define("LANGDATEFORMAT",'TT/MM/JJJJ');

//------------------------------
// titre
//-------------------------------

define("LANGTITRE3","Lauftext oben auf der Seite");
define("LANGTITRE4","Lauftext im Banner");
define("LANGTITRE5","Nachricht empfangen");
define("LANGTITRE6","Erstellung eines Kontos für ".INTITULEDIRECTION);
define("LANGTITRE7","Erstellung eines Kontos für das Schulleben");
define("LANGTITRE8","Erstellung eines Kontos für ".INTITULEENSEIGNANT."");
define("LANGTITRE9","Erstellung eines Vertretungskontos");
define("LANGTITRE10","Erstellung eines Kontos für ".INTITULEELEVE);
define("LANGTITRE11","Erstellung einer Gruppe");
define("LANGTITRE12","Erstellung einer ".INTITULECLASSE);
define("LANGTITRE13","Erstellung eines Fachs");
define("LANGTITRE14","Erstellung eines Teilbereichs");
define("LANGTITRE16","Zuweisung erstellen");
define("LANGTITRE17","Zuweisung für die ".INTITULECLASSE." erstellen");
define("LANGTITRE18","Zuweisung anzeigen");
define("LANGTITRE19","Zuweisung ändern");
define("LANGTITRE20","Zuweisung für die ".INTITULECLASSE." ändern");
define("LANGTITRE21","Zuweisung löschen");
define("LANGTITRE22","Import einer ASCII-Datei (txt,csv) ");
define("LANGTITRE23","Liste der unentschuldigten Verspätungen ");
define("LANGTITRE24","Eine Befreiung hinzufügen");
define("LANGTITRE25","Befreiungen auflisten / ändern");
define("LANGTITRE26","Eine Befreiung löschen");
define("LANGTITRE27","Befreiungsverwaltung - Planung");
define("LANGTITRE28","Befreiungen anzeigen / ändern");
define("LANGTITRE29","Einsicht der ".INTITULECLASSE."en" );
define("LANGTITRE30","Suche nach einem ".INTITULEELEVE);
define("LANGTITRE31","Import der GEP-Datei");
define("LANGTITRE32","Fotoliste der ".INTITULEELEVE."");
define("LANGTITRE33","Schulbescheinigung");

//------------------------------
define("LANGTE1","Titel");
define("LANGTE2","vom");
define("LANGTE3","von");
define("LANGTE4","Anzahl der Zeichen");
define("LANGTE5","Betreff");
define("LANGTE6","An");
define("LANGTE6bis","An die Eltern von ");
define("LANGTE7","Datum");
define("LANGTE8","Nachrichten löschen");
define("LANGTE9","gelesen");
define("LANGTE10","bis zum :");
define("LANGTE11","bis ");
define("LANGTE12","am ");
define("LANGTE13","um"); // für Uhrzeit
define("LANGTE14","An die Gruppe ");

//------------------------------
define("LANGFETE","Alles Gute zum Namenstag an ");
define("LANGFEN1","Ereignis(se) des Tages");
define("LANGFEN2","D.S.T. des Tages");
//------------------------------
define("LANGLUNDI","Montag");
define("LANGMARDI","Dienstag");
define("LANGMERCREDI","Mittwoch");
define("LANGJEUDI","Donnerstag");
define("LANGVENDREDI","Freitag");
define("LANGSAMEDI","Samstag");
define("LANGDIMANCHE","Sonntag");
// ------------------------------
define("LANGMESS1","Nachricht senden - am ");
define("LANGMESS3","Nachricht an das Schulleben : ");
define("LANGMESS4","Nachricht an ".INTITULEENSEIGNANT." : ");
define("LANGMESS6","Nachricht(en) gesendet");
define("LANGMESS7","Aktualität gespeichert");
define("LANGMESS8","Nachricht(en) gesendet");
define("LANGMESS9","Auf die Nachricht antworten - am ");
define("LANGMESS10",'Die Quartalsdaten sind nicht gespeichert.');
define("LANGMESS11",'Bitte benachrichtigen Sie die '.INTITULEDIRECTION.'.');
define("LANGMESS12",'um die Quartalsdaten zu bestätigen.');
define("LANGMESS13",'Bitte klicken Sie <a href="definir_trimestre.php">hier</a>');
define("LANGMESS14",'Die Zuweisungen dieser '.INTITULECLASSE.' sind nicht gespeichert.');
define("LANGMESS15",'Bitte klicken Sie <a href="affectation_creation_key.php">hier</a>');
define("LANGMESS16",'um die Zuweisungen dieser '.INTITULECLASSE.' zu bestätigen');
define("LANGMESS17","Konfiguration");
define("LANGMESS18","W");     // erste Buchstabe des folgenden Satzes !!! (Wenn)
define("LANGMESS18bis","enn mehrere E-Mails anzugeben sind,<br> trennen Sie die E-Mails durch ein Komma.");
define("LANGMESS19","Aktiviert");
define("LANGMESS20","Konfiguration aktualisiert");
define("LANGMESS21","Benachrichtigung bei Eingang einer Nachricht in Ihrem Postfach erhalten");
define("LANGMESS22","Nachricht an eine Gruppe senden <font class=T1>(Lehrkräfte,VS,Dir,Prakt.Betreuer)</font> : "); // VS = Vie Scolaire (Schulleben), Dir = Direction (Leitung)
define("LANGMESS23","Erstellung einer E-Mail-Gruppe ");
define("LANGMESS24","Geben Sie die Personen der Gruppe an ");
define("LANGMESS25","Wählen Sie die verschiedenen Personen aus, indem Sie die Taste gedrückt halten");
define("LANGMESS26","Erstellung bestätigen");
define("LANGMESS27","E-Mail-Gruppe erstellt");
define("LANGMESS28","Liste Ihrer E-Mail-Gruppen ");
define("LANGMESS29","Gruppe ");
define("LANGMESS30","Personenliste ");
define("LANGMESS31","Nachricht von ");
define("LANGMESS32","Sie haben aktuell ");
define("LANGMESS33","Nachricht(en) in der Warteschlange ");

// -----------------------------
// bouton
// PAS DE -->' (cote) !!!!
define("LANGBTS","Weiter >");
define("LANGBT1","Lauftext speichern");
define("LANGBT2","Information speichern");
define("LANGBT3","Beenden ohne zu senden");
define("LANGBT4","Nachricht senden");
define("LANGBT5","Bitte warten...");
define("LANGBT6","Markierte Nachrichten löschen");
define("LANGBT7","Konto speichern");
define("LANGBT11","Liste der Vertretungen");
define("LANGBT12","Liste der Gruppen");
define("LANGBT13","Die ".INTITULECLASSE."(n) bestätigen");
define("LANGBT14","Erstellung speichern");
define("LANGBT15","Liste der ".INTITULECLASSE."n");
define("LANGBT16","Liste der Fächer");
define("LANGBT17","Teilbereich speichern");
define("LANGBT18","Status speichern");
define("LANGBT19","Bestätigen");
define("LANGBT20","Beenden ohne zu speichern");
define("LANGBT21","Zuweisung speichern");
define("LANGBT22","Zuweisung löschen");
define("LANGBT23","Datei senden");
define("LANGBT24","Wiederholen");
define("LANGBT25","Seite aktualisieren");
define("LANGBT26",INTITULECLASSE." erstellen");
define("LANGBT27","Abwesenheit oder Verspätung planen");
define("LANGBT28","Einsehen");
define("LANGBT29","Abwesenheit oder Verspätung löschen");
define("LANGBT30","Aktualisierung bestätigen");
define("LANGBT31","Bestätigen");
define("LANGBT32","Befreiungen löschen");
define("LANGBT33","Befreiungen ändern");
define("LANGBT34","Befreiungen hinzufügen");
define("LANGBT35","Daten speichern von ");
define("LANGBT36","Befreiung geändert -- Das TRIADE-Team");
define("LANGBT37","Information übermitteln");
define("LANGBT38","Senden");
define("LANGBT39","Suche starten");
define("LANGBT40","Wiederherstellung");
define("LANGBT41","Abgeschlossen");
define("LANGBT42","Nicht gespeicherte ".INTITULEELEVE." bestätigen");
define("LANGBT43","Zeugnis drucken");
define("LANGBT44","Verlauf");
define("LANGBT45","Dokumentation einsehen");
define("LANGBT46","Foto speichern");
define("LANGBT47","Andere Änderung");
define("LANGBT48","Dieses Modul verlassen");
define("LANGBT49","Ganze ".INTITULECLASSE." bearbeiten");
define("LANGBT50","Löschen");
define("LANGBT51","D.S.T-Antrag bestätigen");
// -----------------------------
define("LANGCA1","N"); //
define("LANGCA1bis","achricht noch nicht gelesen"); // ohne den ersten Buchstaben
define("LANGCA2","N"); //
define("LANGCA2bis","achricht bereits gelesen"); // ohne den ersten Buchstaben
define("LANGCA3","G"); //
define("LANGCA3bis","eben Sie TT/MM/JJJJ an <BR> Im Falle eines nicht <BR>vereinbarten Datums, geben Sie den Vermerk an <br>"); // ohne den ersten Buchstaben
// -----------------------------
define("LANGNA1","Name");
define("LANGNA2","Vorname");
define("LANGNA3","Passwort");
define("LANGNA4","Neues Konto erstellt \\n\\n Das TRIADE-Team ");
define("LANGNA5","Vertretung von ");
// -----------------------------
define("LANGELE1","Informationen über den ".INTITULEELEVE);
define("LANGELE2","Name");
define("LANGELE3","Vorname");
define("LANGELE4","Klasse");
define("LANGELE5","Option");
define("LANGELE6","Status"); // Régime -> Verpflegungsstatus oder Status
define("LANGELE7","Intern");
define("LANGELE8","Halbpension");
define("LANGELE9","Extern");
define("LANGELE10","Geburtsdatum");
define("LANGELE11","Nationalität");
define("LANGELE12","Schülernummer");
// define("LANGELE12","Numéro national"); //
define("LANGELE13","Informationen zur Familie");
define("LANGELE14","Adresse 1");
define("LANGELE15","Postleitzahl");
define("LANGELE16","Ort");
define("LANGELE17","Adresse 2");
define("LANGELE18","");
define("LANGELE19","");
define("LANGELE20","Telefonnummer");
define("LANGELE21","Beruf des Vaters");
define("LANGELE22","Telefon des Vaters");
define("LANGELE23","Beruf der Mutter");
define("LANGELE24","Telefon der Mutter");
define("LANGELE25","Vorherige Schule");
define("LANGELE26","Name der Einrichtung");
define("LANGELE27","Einrichtungsnummer");
define("LANGELE28","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." erstellt -- Das TRIADE-Team");
define("LANGELE29","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." bereits vorhanden -- Das TRIADE-Team");
//------------------------------------------------------------
define("LANGGRP1","Name der Gruppe");
define("LANGGRP2","Geben Sie die ".INTITULECLASSE."n für die Gruppenerstellung an");
define("LANGGRP3","Wählen Sie die verschiedenen ".INTITULECLASSE."n aus, indem Sie die Taste gedrückt halten");
define("LANGGRP4","Strg"); // Ctrl
define("LANGGRP5","und auf die linke Maustaste klicken.");
define("LANGGRP6","Name des Bereichs");
define("LANGGRP7","Neue ".INTITULECLASSE." erstellt -- Das TRIADE-Team");
define("LANGGRP8","Neues Fach erstellt -- Das TRIADE-Team");
define("LANGGRP9","Name des Fachs");
define("LANGGRP10","Name des Teilbereichs");
//------------------------------------------------------------
//------------------------------------------------------------
define("LANGAFF1","Zuweisung für die ".INTITULECLASSE);
define("LANGAFF2","!! Die Erstellung einer Zuweisung <u>löscht</u> alle Noten der ".INTITULECLASSE." !!</u>");
define("LANGAFF3","Zuweisung der ".INTITULECLASSE."n");
//------------------------------------------------------------
define("LANGPER1","Zeitraum drucken");
define("LANGPER2","Beginn des Zeitraums");
define("LANGPER3","Ende des Zeitraums");
define("LANGPER4","Bereich");
define("LANGPER5","PDF-Datei abrufen");
define("LANGPER6",ucfirst(INTITULEENSEIGNANT)." ");
define("LANGPER8","in ".INTITULECLASSE." von ");
define("LANGPER9","Modul zur Zuweisung der ".INTITULECLASSE."n.");
define("LANGPER10","ACHTUNG, dieses Modul ist bei einer neuen Zuweisung zu verwenden,<br> es löscht alle Noten der ".INTITULEELEVE." der zugewiesenen ".INTITULECLASSE."n.");
define("LANGPER11","ACHTUNG, die Noten der ausgewählten ".INTITULECLASSE."n werden gelöscht. \\n Möchten Sie fortfahren? \\n\\n Das TRIADE-Team");
define("LANGPER12","Geben Sie den Zugangscode ein.");
define("LANGPER13","Code überprüfen");
define("LANGPER14","Anzahl der Fächer");
define("LANGPER15","Zuweisung für die ".INTITULECLASSE." erstellen");
define("LANGPER16","Anz."); // Nb -> Anzahl
define("LANGPER17","Fach");
define("LANGPER18",ucfirst(INTITULEENSEIGNANT));
define("LANGPER19","Koeff."); // Coef -> Koeffizient
define("LANGPER20","Gruppe");
define("LANGPER21","Sprache");
define("LANGPER22","Diese Seite drucken");
define("LANGPER23","Zuweisung");
define("LANGPER23bis","erfolgreich");  // Zuweisung xxxx erfolgreich
define("LANGPER24","unterbrochen"); // Zuweisung xxxx unterbrochen
define("LANGPER25","Klasse");
define("LANGPER26","Anzeige");
define("LANGPER27","Anzeigen");
define("LANGPER28","Zuweisung für die ".INTITULECLASSE." anzeigen");
define("LANGPER29","!! Die Änderung der Zuweisung <u>löscht</u> alle Noten der ".INTITULECLASSE." !!");
define("LANGPER30","Ändern");
define("LANGPER31","Zuweisung ändern");
define("LANGPER32","Änderung der Zuweisung");
define("LANGPER32bis","unterbrochen"); // Änderung der Zuweisung xxxx unterbrochen
define("LANGPER33","Löschung der Zuweisung für die ");
define("LANGPER34","!! Die Löschung der Zuweisung <u>löscht</u> alle Noten der ".INTITULECLASSE." !!</u>");
define("LANGPER35","Zuweisung der ".INTITULECLASSE);
define("LANGPER35bis","gelöscht"); // Zuweisung der Klasse xxxx gelöscht
//------------------------------------------------------------------------------
define("LANGIMP1","Import einer bestehenden Datenbank ");
define("LANGIMP2","Geben Sie den Typ der zu importierenden Datei an ");
define("LANGIMP3","ASCII-Datei ");
define("LANGIMP4","GEP-Datei ");
define("LANGIMP5","Modul zum Importieren von ASCII-Dateien.");
define("LANGIMP6","Die zu übertragende Datei <FONT color=RED><B>MUSS</B></FONT> <FONT COLOR=red><B>45</B></FONT> Felder enthalten <I>(leer oder nicht leer)</I>, die durch dasselbe Trennzeichen \"<FONT color=red><B>;</B></font>\" getrennt sind <I>(d.h. das Zeichen \"<FONT color=red><B>;</B></font>\" muss 44 Mal vorhanden sein)</I>");
define("LANGIMP7","Hier ist die Reihenfolge der anzugebenden Felder: ");
define("LANGIMP8","Name");
define("LANGIMP9","Vorname");
define("LANGIMP10",INTITULECLASSE);
define("LANGIMP11","Status"); // régime
define("LANGIMP12","Geburtsdatum");
define("LANGIMP13","Nationalität");
define("LANGIMP14","Name des Erziehungsberechtigten");
define("LANGIMP15","Vorname des Erziehungsberechtigten");

define("LANGIMP16","Adresse&nbsp;1");
define("LANGIMP18","Postleitzahl&nbsp;1");
define("LANGIMP19","Ort&nbsp;1");

define("LANGIMP17","Adresse&nbsp;2");
define("LANGIMP18_2","Postleitzahl&nbsp;2");
define("LANGIMP19_2","Ort&nbsp;2");

define("LANGIMP20","Telefon");
define("LANGIMP21","Beruf Vater");
define("LANGIMP22","Telefon Beruf Vater");
define("LANGIMP23","Beruf Mutter");
define("LANGIMP24","Telefon Beruf Mutter");
define("LANGIMP25","Einrichtungsnummer");

define("LANGIMP26","FS1"); // lv1 -> Fremdsprache 1
define("LANGIMP27","FS2"); // lv2 -> Fremdsprache 2
define("LANGIMP28","Option");
define("LANGIMP29","Nummer ".INTITULEELEVE);
define("LANGIMP30","ACHTUNG, die Datenbank wird automatisch gelöscht. \\n Möchten Sie fortfahren? \\n\\n Das TRIADE-Team");
define("LANGIMP31","ACHTUNG: Dieses Modul ist bei der ersten Verwendung zu nutzen,<br> es löscht alle Informationen der ".INTITULEELEVE." (Noten, Zeugnisse, Schulleben).<br /> * Pflichtfeld");
define("LANGIMP39","Geben Sie die zu übertragende Datei an ");
define("LANGIMP40","Datei übertragen -- Das TRIADE-Team ");
define("LANGIMP41","Die Anzahl der Felder stimmt nicht überein ");
define("LANGIMP42","Geben Sie für jede Referenz die entsprechende ".INTITULECLASSE." an ");
define("LANGIMP43","Datei nicht gespeichert ");
// ------------------------------------------------------------------------------
define("LANGABS1","Verwaltung Abwesenheiten - Verspätungen des Tages");
define("LANGABS2","Eine Abwesenheit oder Verspätung planen");
define("LANGABS3","Geben Sie den Namen des ".INTITULEELEVE." an");
define("LANGABS4","Unentschuldigte Abwesenheiten oder Verspätungen auflisten");
define("LANGABS5","Unentschuldigte Abwesenheiten");
define("LANGABS6","Unentschuldigte Verspätungen");
define("LANGABS7","Eine Abwesenheit oder Verspätung anzeigen und/oder ändern");
define("LANGABS8","Geben Sie den Namen des ".INTITULEELEVE." an");
define("LANGABS9","Eine Abwesenheit oder Verspätung anzeigen und/oder löschen");
define("LANGABS10","Kein Schüler in der Datenbank");
define("LANGABS11","Abw/Versp"); // Abs/Rtd
define("LANGABS12","Grund");
define("LANGABS13","Verspätet am");
define("LANGABS14","Versp"); // Rtd
define("LANGABS15","Abw"); // Abs
define("LANGABS16","Abbrechen");
define("LANGABS17","Abwesenheit oder Verspätung ändern");
define("LANGABS18","Abwesend&nbsp;vom&nbsp;");
define("LANGABS19","bis&nbsp;");
define("LANGABS20","Abw/Versp");
define("LANGABS21","Dauer");
define("LANGABS22","Grund");
define("LANGABS23","Uhrzeit / Datum");
define("LANGABS24","Erfassung von Abwesenheiten oder Verspätungen in Klasse ");
define("LANGABS25","Verwaltung Abwesenheit - Verspätung");
define("LANGABS26","Verwaltung Abwesenheit - Verspätung Planung");
define("LANGABS27","Daten speichern von ");
define("LANGABS28","Daten gespeichert ");
define("LANGABS29","B"); //erste Buchstabe (Befreit)
define("LANGABS29bis","efreit(e) von :"); //suite
define("LANGABS30","Befr."); // Disp -> Befreiung
define("LANGABS31",INTITULECLASSE." von ");
define("LANGABS32","V"); //erste Buchstabe (Verspätung)
define("LANGABS32bis","erspätung "); //suite
define("LANGABS33","in");
define("LANGABS34","von");
define("LANGABS35","Abwesenheit - Verspätung - Befreiung vom ");
define("LANGABS36","Aktualisierung");
define("LANGABS37","Abwesenheiten, Befreiungen, Verspätungen des Tages drucken ");
define("LANGABS38","Tel.");
define("LANGABS39","Tel. Beruf Vater ");
define("LANGABS40","Tel. Beruf Mutter");
define("LANGABS41","Tel. Privat ");
define("LANGABS42","Abwesend vom ");
define("LANGABS43","während ");
define("LANGABS44","Tag(e) ");
define("LANGABS45","Aktualisierung speichern ");
define("LANGABS46","ab dem ");

define("LANGDISP8","Befreiung löschen");
//----------------------------------------------------------------------------
define("LANGPROJ1","Wahl der ".INTITULECLASSE);
define("LANGPROJ2","Wahl des Quartals");
define("LANGPROJ3","Quartal 1");
define("LANGPROJ4","Quartal 2");
define("LANGPROJ5","Quartal 3");
define("LANGPROJ6","<font class=T2>Kein ".INTITULEELEVE." in dieser ".INTITULECLASSE."</font>");
define("LANGPROJ7","Anzahl der Verspätungen");
define("LANGPROJ8"," Gesamt");
define("LANGPROJ9","Disziplin"); // Oder Fachbereich je nach Kontext
define("LANGPROJ10","Minuten");
define("LANGPROJ11","Anz. Nachsitzen"); // Nbr de retenues
define("LANGPROJ12","zugew.&nbsp;durch&nbsp;"); // attr. par
define("LANGPROJ13","Liste");
define("LANGPROJ14","Durchschn. ".ucfirst(TextNoAccentLicence2(INTITULEELEVE)).""); // Moy -> Durchschnitt
define("LANGPROJ15","Durchschn. Klasse");
define("LANGPROJ16","Durchschnitt ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
// ----------------------------------------------------------------------------
define("LANGDISP1","<font class=T2>Kein ".INTITULEELEVE." mit diesem Namen</font>");
define("LANGDISP2","Grund");
define("LANGDISP3","Ärztliches Attest");
define("LANGDISP4","Zeitraum&nbsp;vom&nbsp;");
define("LANGDISP5","im Fach ");
define("LANGDISP6","Befreiungsstunde ");
define("LANGDISP7","<B><font color=red>G</font></B>eben Sie TT/MM/JJJJ an <BR> in beiden Feldern");
define("LANGDISP9","<b>Vollständige</b> Anzeige der Befreiungen");
define("LANGDISP10","In");
// ----------------------------------------------------------------------------
define("LANGASS1","TRIADE Assistenz");
define("LANGASS2","Bietet Ihnen einen Service, um Ihnen zu helfen, Sie bei der Nutzung von TRIADE zu unterstützen.<br /><br />Sie haben ein Problem mit einem der TRIADE-Dienste, zögern Sie nicht, uns über das folgende Formular Informationen über den betreffenden Dienst zukommen zu lassen. Unsere Ingenieure werden diesen Dienst überprüfen.");
define("LANGASS3","Betroffenes Mitglied");
define("LANGASS4","Verwaltung");
define("LANGASS5",ucfirst(INTITULEENSEIGNANT));
define("LANGASS6","Schulleben");
define("LANGASS6bis","Elternteil");
define("LANGASS7","Aktion");
define("LANGASS8","Erstellung");
define("LANGASS9","Anzeige");
define("LANGASS10","Löschung");
define("LANGASS11","Andere");
define("LANGASS12","Dienst");
define("LANGASS13","Benutzerkonto");
define("LANGASS14","Nachrichtensystem");
define("LANGASS15","Zuweisung");
define("LANGASS16","Datenbank");
define("LANGASS17","Klasse");
define("LANGASS18","Fach");
define("LANGASS19","Suche");
define("LANGASS20","D.S.T.");
define("LANGASS21","Planung");
define("LANGASS22","Befreiung");
define("LANGASS23","Disziplin");
define("LANGASS24","Rundschreiben");
define("LANGASS25","Zeugnis");
define("LANGASS26","Zeitraum");
define("LANGASS27","Kommentar");
define("LANGASS28","TRIADE Assistenz dankt Ihnen für Ihre Hilfe.");
define("LANGASS29","Das TRIADE-Team.");
define("LANGASS30","Das TRIADE-Team zu Ihren Diensten");
define("LANGASS31","TRIADE ist ein einzigartiges und neuartiges Produkt. Zögern Sie daher nicht, uns Ihre Ratschläge und Vorschläge mitzuteilen, damit die Website den tatsächlichen Erwartungen der Benutzer entspricht! Vielen Dank an Sie :-)");
define("LANGASS32","Gästebuch");
define("LANGASS33","Ihr direktes Zeugnis: Schreiben Sie Ihre Anmerkungen in unser Gästebuch.");
define("LANGASS34","Ihre Nachricht wurde an uns gesendet, wir werden Ihnen umgehend antworten.<br> <BR>Vielen Dank, dass Sie TRIADE nutzen und bis bald.<BR><BR><BR><UL><UL>Das TRIADE-Team.<BR>");
define("LANGASS35","Andere");
define("LANGASS36","SMS");
define("LANGASS37","WAP");
define("LANGASS38","Fotoliste");
define("LANGASS39","Barcode");
define("LANGASS40","Berufspraktikum");
// -----------------------------------------------------------------------------
define("LANGRECH1","<font class=T2>Kein ".INTITULEELEVE." in der ".INTITULECLASSE."</font>");
define("LANGRECH2","Suche nach ");
define("LANGRECH3","<font class=T2>Kein ".INTITULEELEVE." für diese Suche</font>");
define("LANGRECH4","Information / Änderung");
// ---------------------------------------------------------------------------------
define("LANGBASE1","ACHTUNG: Dieses Modul ist bei der ersten Verwendung zu nutzen,<br> es löscht alle Informationen der ".INTITULEELEVE." (Noten, Zeugnisse, Schulleben).");
define("LANGBASE2"," Die zu importierenden Dateien MÜSSEN im dbf-Format sein ");
define("LANGBASE3","Hier ist die Liste der Dateien ");
define("LANGBASE4","Modul zum Importieren von GEP-Dateien ");
define("LANGBASE5","Import einer GEP-Datenbank ");
define("LANGBASE6","Gesamtzahl ".INTITULEELEVE." in der DBF-Datei ");
define("LANGBASE7","Gesamtzahl ".INTITULEELEVE." in ".INTITULECLASSE);
define("LANGBASE8","Gesamtzahl ".INTITULEELEVE." ohne ".INTITULECLASSE);
define("LANGBASE9","Passwörter abrufen ");
define("LANGBASE10","Datei F_ele.dbf konnte nicht geöffnet werden");
define("LANGBASE11","Datenbank verarbeitet -- Das TRIADE-Team");
define("LANGBASE12","Die ausgewählte Datei ist ungültig!");
define("LANGBASE13","Hier ist die Liste der Passwörter");
define("LANGBASE14","Rufen Sie die Liste ab, indem Sie alle Zeilen auswählen und sie in eine \"txt\"-Datei kopieren/einfügen.");
define("LANGBASE15","Dann über Excel oder OpenOffice die \"txt\"-Datei abrufen und das Semikolon als Feldtrennzeichen angeben.");
define("LANGBASE17"," Achtung: Die Passwörter sind nur auf <br />dieser Seite zugänglich!! Denken Sie daran, die Liste abzurufen, <b>BEVOR</b> Sie auf Fertigstellen klicken ");
define("LANGBASE18","INFORMATION NICHT VERFÜGBAR");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGBULL1","Druck des Quartalszeugnisses");
define("LANGBULL2","Geben Sie die ".INTITULECLASSE." an");
define("LANGBULL3","Schuljahr");
define("LANGBULL4","<a href=\"#\" onclick=\"open('https://www.adobe.com/de/','_blank','')\"><b><FONT COLOR=red>ACHTUNG</FONT></B> Benötigt <B>Adobe Acrobat Reader</B>. Kostenlose Software und Download klicken Sie <B>HIER</B></A>");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGPARENT1","Keine Nachricht");
define("LANGPARENT2","Derzeit kein Elternvertreter zugewiesen");
define("LANGPARENT3","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."-Vertreter");
define("LANGPARENT4","Elternvertreter");
define("LANGPARENT5","Liste der Vertreter");
//----------------------------------------------------------------------//
define("LANGPUR3","ACHTUNG: Dieses Modul ist zu verwenden, <br>wenn Sie TRIADE-Daten löschen möchten.");
define("LANGPUR4","ACHTUNG, Sie betreten ein Modul, das anschließend von Ihnen ausgewählte Daten löschen wird. \\n Möchten Sie fortfahren? \\n\\n Das TRIADE-Team");
define("LANGPUR5","Die Daten werden gelöscht");
define("LANGPUR6","Information: Die Auswahl \"".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."\" beinhaltet automatisch die Löschung von Noten, Abwesenheiten, Disziplinarmaßnahmen, Befreiungen, Verspätungen, Gesprächen");
define("LANGPUR7","Geben Sie das oder die zu löschenden Element(e) an: ");
define("LANGPUR8","Behalten");
define("LANGPUR9","Löschen");
//----------------------------------------------------------------------//
define("LANGCHAN0","Modul für den ".INTITULECLASSE."swechsel eines oder mehrerer ".INTITULEELEVE."");
define("LANGCHAN1","ACHTUNG: Dieses Modul ist zu verwenden, <br>wenn Sie einen ".INTITULECLASSE."swechsel <br> für ".INTITULEELEVE." durchführen möchten");
define("LANGCHAN3","ACHTUNG, Daten des ".INTITULEELEVE." \\n oder der vom ".INTITULECLASSE."swechsel betroffenen ".INTITULEELEVE." werden gelöscht");
//----------------------------------------------------------------------//
define("LANGGEP1",'Import der GEP-Datei');
define("LANGGEP2",'Geben Sie die Datei an');
//----------------------------------------------------------------------//
define("LANGCERT1"," Dieses Zertifikat herunterladen ");
//----------------------------------------------------------------------//
define("LANGPROFR1",'Geben Sie verspätete '.INTITULEELEVE.' an');
define("LANGPROFR2",'Erfassung der Verspätungen ');
define("LANGKEY1",'<font class=T1>Kein Registrierungsschlüssel </font>');
define("LANGDISP20",'Befreiungen hinzufügen');
define("LANGPROFA",'<br><center><font size=2>Kein Registrierungsschlüssel </font><br><br>Bitte kontaktieren Sie Ihren TRIADE-Administrator, <br>um den Registrierungsantrag für TRIADE zu validieren. </center><br><br>');
define("LANGPROFB",'Hinzufügen einer Note in ');
define("LANGPROFC",'Bestätigen Sie die Speicherung der Noten ');
define("LANGPROFD",'Validieren Sie die Speicherung der Noten');
define("LANGPROFE",'&nbsp;&nbsp;<i><u>Info</u>: Mit der Enter-Taste gelangen Sie automatisch zur nächsten Note.</i>');
define("LANGPROFF",'Hinzufügen einer Note');
define("LANGPROFG",'Geben Sie die '.INTITULECLASSE.' an');
//----------------------------------------------------------------------//
define("LANGMETEO1",'TAG');
define("LANGMETEO2",'NACHT');
//----------------------------------------------------------------------//
define("LANGPROFP1","Nachricht für die ".INTITULECLASSE);
define("LANGPROFP2","Nachricht speichern");
define("LANGPROFP3","Nachricht des Klassenlehrers");
//----------------------------------------------------------------------//
// Module Stage Pro
define("LANGSTAGE1","Planung der Praktika ");
define("LANGSTAGE2","Praktikumstermine anzeigen ");
define("LANGSTAGE3","Hinzufügen ");
define("LANGSTAGE4","Zuweisen ");
define("LANGSTAGE5","Einfügen eines Praktikumstermins ");
define("LANGSTAGE6","Änderung eines Praktikumstermins ");
define("LANGSTAGE7","Einen Praktikumstermin löschen ");
define("LANGSTAGE8","Verwaltung der Unternehmen ");
define("LANGSTAGE9","Die verschiedenen Unternehmen anzeigen ");
define("LANGSTAGE10","Ein Unternehmen hinzufügen ");
define("LANGSTAGE11","Ein Unternehmen ändern ");
define("LANGSTAGE12","Ein Unternehmen löschen ");
define("LANGSTAGE13","Verwaltung der ".INTITULEELEVE." ");
define("LANGSTAGE14",INTITULEELEVE." in Unternehmen anzeigen ");
define("LANGSTAGE15",INTITULEELEVE." einem Unternehmen zuweisen ");
define("LANGSTAGE16","Merkmale eines ".INTITULEELEVE." ändern ");
define("LANGSTAGE17","Zuweisung eines ".INTITULEELEVE." löschen ");
define("LANGSTAGE18","Anzeige der Praktikumstermine");
define("LANGSTAGE19","Praktikum");
define("LANGSTAGE20","Suche nach Unternehmen");
define("LANGSTAGE21","Unternehmen nach Tätigkeit anzeigen");
define("LANGSTAGE22","Anzeige der Unternehmen");
//----------------------------------------------------------------------//
define("LANGGEN1","Verwaltung");
define("LANGGEN2","Schulleben");
define("LANGGEN3",ucfirst(INTITULEENSEIGNANT).""); // Plural von Lehrkraft ist Lehrkräfte
//----------------------------------------------------------------------//
define("LANGDST1","Antrag für D.S.T");
define("LANGDST2","Hallo, <br> <br> Ihr Antrag für eine Deutscharbeit (D.S.T.) für den ");
define("LANGDST3","<br><br><b>ist nicht möglich</b>, bitte wählen Sie ein anderes Datum oder kontaktieren Sie uns direkt. <br><br> Danke");
define("LANGDST4","<br><br><b>ist registriert</b>, für weitere Informationen kontaktieren Sie uns bitte. <br><br> Danke");
define("LANGDST5","für den ");
define("LANGDST6","Thema / Fach");
define("LANGDST7","Antrag abgelehnt");
define("LANGDST8","Antrag genehmigt");
//----------------------------------------------------------------------//
define("LANGCALEN1","Ereignis");
define("LANGCALEN2","Planung vom ");
define("LANGCALEN3","Einen Eintrag hinzufügen");
define("LANGCALEN4","Einen Eintrag löschen");
define("LANGCALEN5","Seite aktualisieren");
define("LANGCALEN6","Ereigniskalender");
define("LANGCALEN7","In ".INTITULECLASSE." von ");
define("LANGCALEN8","Klassenarbeit von "); // Devoir de
define("LANGCALEN9","Klassenarbeit(en) des Tages"); // Devoir(s) Sur Table du jour
//----------------------------------------------------------------------//
//module reservation
define("LANGRESA1","Geräteverwaltung");
define("LANGRESA2","Raumverwaltung");
define("LANGRESA3","Geräteliste");
define("LANGRESA4","Raumliste");
define("LANGRESA5","Ein Gerät hinzufügen");
define("LANGRESA6","Ein Gerät ändern");
define("LANGRESA7","Ein Gerät löschen");
define("LANGRESA8","Raum hinzufügen");
define("LANGRESA9","Raum löschen");
define("LANGRESA10","Einen Raum löschen");
define("LANGRESA11","Geräte- / Raumreservierung");
define("LANGRESA12","Gerätereservierung");
define("LANGRESA13","Raumreservierung");
define("LANGRESA14","Reservieren");
define("LANGRESA15","Erstellung eines Geräts");
define("LANGRESA16","Bezeichnung des Geräts");
define("LANGRESA17","Erstellung speichern");
define("LANGRESA18","Zusätzliche Informationen");
define("LANGRESA19","Gerät gespeichert");
define("LANGRESA20","Erstellung eines Raumes");
define("LANGRESA21","Bezeichnung des Raumes");
define("LANGRESA22","Raum gespeichert");
define("LANGRESA23","Raum löschen");
define("LANGRESA24","Raum");
define("LANGRESA25","Den Raum löschen");
define("LANGRESA26","Raum gelöscht");
define("LANGRESA27","ein Raum");
define("LANGRESA28","Dieser Raum kann nicht gelöscht werden. \\n\\n Raum ist zugewiesen.  ");
define("LANGRESA29","Gerät gelöscht");
define("LANGRESA30","Dieses Gerät kann nicht gelöscht werden. \\n\\n Gerät ist zugewiesen.  ");
define("LANGRESA31","ein Gerät");
define("LANGRESA32","Gerät löschen");
define("LANGRESA33","Gerät");
define("LANGRESA34","Ein Gerät löschen");
define("LANGRESA35","Liste der Geräte");
define("LANGRESA36","DATUM");
define("LANGRESA37","Von");
define("LANGRESA38","Bis");
define("LANGRESA39","Von wem");
define("LANGRESA40","Information");
define("LANGRESA41","Bestätigen");
define("LANGRESA42","Bestätigt");
define("LANGRESA43","Nicht&nbsp;bestätigt");
define("LANGRESA44","Geräteplanung");
define("LANGRESA45","Gerät");
define("LANGRESA46","Gerät an diesem Datum bereits reserviert");
define("LANGRESA47","Reservierungsplan dieses Geräts einsehen");
define("LANGRESA48","Reservierung ab dem ");
define("LANGRESA49","Am Datum ");
define("LANGRESA50","Gerät reserviert, wartet auf Bestätigung");
define("LANGRESA51","Raumplanung");
define("LANGRESA52","Raum");
define("LANGRESA53","Raum an diesem Datum bereits reserviert");
define("LANGRESA54","Raum reserviert, wartet auf Bestätigung");
define("LANGRESA55","Reservierungsplan für diesen Raum einsehen");
define("LANGRESA56","Reservierung bestätigen");
define("LANGRESA57","Planung");
define("LANGRESA58","Bestätigen");
//----------------------------------------------------------------------//
define("LANGTTITRE1","Mitgliederzugang");
define("LANGTTITRE2","Mitglied");
define("LANGTTITRE3","Kontoaktivierung");
define("LANGTTITRE4","Bitte haben Sie einen Moment Geduld");
//--------------
define("LANGTP1","Name");
define("LANGTP2","Vorname");
define("LANGTP3","Passwort");
define("LANGTCONNEXION","Verbindung");
define("LANGTERREURCONNECT","Verbindungsfehler");
define("LANGTCONNECCOURS","Verbindung wird hergestellt ");
define("LANGTFERMCONNEC","Klicken Sie hier, um Ihr Konto zu schließen");
define("LANGTDECONNEC","Abmeldung läuft");

define("LANGTBLAKLIST0",'<b><font color=red  class=T2>Ihr Konto ist deaktiviert!!</b><br> Um Ihr Konto wieder zu aktivieren, kontaktieren Sie Ihre Schule.</font>');

define("LANGMOIS1","Januar");
define("LANGMOIS2","Februar");
define("LANGMOIS3","März");
define("LANGMOIS4","April");
define("LANGMOIS5","Mai");
define("LANGMOIS6","Juni");
define("LANGMOIS7","Juli");
define("LANGMOIS8","August");
define("LANGMOIS9","September");
define("LANGMOIS10","Oktober");
define("LANGMOIS11","November");
define("LANGMOIS12","Dezember");

define("LANGDEPART1","des ".INTITULEELEVE."s"); // de l'élève

define("LANGVALIDE","Bestätigen");
define("LANGIMP45","Bearbeiten");

define("LANGMESS34","Nachricht nicht mehr verfügbar.");
define("LANGMESS35","Diese Gruppe öffentlich machen.");
define("LANGMESS36","Nachricht gelöscht");


define("LANGRESA59","Name des Raumes");
define("LANGRESA60","Information");

define("LANGMAINT0","Eine Wartung der Software ist geplant");
define("LANGMAINT1","Der TRIADE-Dienst ist am ");
define("LANGMAINT2","zwischen");
define("LANGMAINT3","und");

define("LANCALED1","Vorheriges Jahr");
define("LANCALED2","Nächstes Jahr");


define("LANGTTITRE5","Zugangsproblem");
define("LANGTTITRE6","Fragen");
define("LANGTPROBL1","Derzeit ist der TRIADE-Dienst in Betrieb.");
define("LANGTPROBL2","Ich habe eine Frage");
define("LANGTPROBL3","Frage speichern");
define("LANGTPROBL4","Beenden ohne zu speichern");
define("LANGTPROBL5","Erläutern Sie uns Ihr Problem");
define("LANGTPROBL6","Schuleinrichtung*: ");
define("LANGTPROBL7","E-Mail : ");
define("LANGTPROBL8","Nachricht : ");
define("LANGTPROBL9","(* Pflichtfeld)");
define("LANGTPROBL10","Problem speichern");
define("LANGTPROBL12","Wir kümmern uns schnellstmöglich um Ihr Problem. \\n\\n  Das TRIADE-Team ");

define("LANGELEV1","Schulnoten von");

define("LANGFORUM1","- Nachrichtenliste");
define("LANGFORUM2","In diesem Diskussionsforum wurde keine Nachricht veröffentlicht");
define("LANGFORUM3","Sie können ");
define("LANGFORUM3bis"," eine erste Nachricht ");
define("LANGFORUM3ter"," veröffentlichen, wenn Sie möchten ");
define("LANGFORUM4","Eine neue Nachricht veröffentlichen");
define("LANGFORUM5","Forum - Eine Nachricht veröffentlichen");
define("LANGFORUM6","Zu beachtende Charta");
define("LANGFORUM7","Fehler: Die referenzierte Nachricht existiert nicht.");
define("LANGFORUM8","Zurück zur Liste der veröffentlichten Nachrichten");
define("LANGFORUM9","--- Ursprüngliche Nachricht ---");
define("LANGFORUM10","Ihr Name ");
define("LANGFORUM11","Ihre E-Mail ");
define("LANGFORUM12","Betreff ");
define("LANGFORUM13","Senden"); // --> bouton envoyer
define("LANGFORUM14","Zurück zur Liste der veröffentlichten Nachrichten");
define("LANGFORUM15","Forum - Senden einer Nachricht");
define("LANGFORUM16","<b>Fehler</b>: Diese Seite kann nur aufgerufen werden,<br> wenn zuvor eine Nachricht ");
define("LANGFORUM16bis"," veröffentlicht "); // posté
define("LANGFORUM17","<b>Fehler</b>: Ihre Nachricht enthält keinen Text.<br>");
define("LANGFORUM18","<b>Fehler</b>: Sie haben vergessen, Ihren Namen anzugeben.<br>");
define("LANGFORUM19","Fehler! Ihre Nachricht konnte nicht veröffentlicht werden. ");
define("LANGFORUM20","<b>Fehler</b>: Die Indexdatei konnte nicht aktualisiert werden. <br>");
define("LANGFORUM21","Ihre Nachricht konnte nicht veröffentlicht werden.");
define("LANGFORUM22","Ihre Nachricht wurde korrekt veröffentlicht.<br>Vielen Dank für Ihren Beitrag.");
define("LANGFORUM23","Zurück zur Liste der veröffentlichten Nachrichten");
define("LANGFORUM24","Forum - Lesen einer Nachricht");
define("LANGFORUM25","In diesem Diskussionsforum wurde keine Nachricht veröffentlicht.");
define("LANGFORUM26","Sie können ");
define("LANGFORUM26bis","veröffentlichen");
define("LANGFORUM26ter","eine erste Nachricht, wenn Sie möchten.");
define("LANGFORUM27","Diese Nachricht existiert nicht oder wurde vom Administrator des Diskussionsforums gelöscht.<br>");
define("LANGFORUM28","Zurück zur Liste der veröffentlichten Nachrichten");
define("LANGFORUM30","Autor");
define("LANGFORUM31","Datum");
define("LANGFORUM32","Eine Antwort veröffentlichen");
define("LANGFORUM33","Vorherige Nachricht (im Diskussionsstrang)");
define("LANGFORUM34","Nächste Nachrichten (im Diskussionsstrang)");

define("LANGPROFH","Hausaufgabe zu erledigen in ");
define("LANGPROFI","Hausaufgabe speichern ");
define("LANGPROFJ","Hausaufgabe ");
define("LANGPROFK","eingegeben&nbsp;am&nbsp;");
define("LANGPROFL","Datum bestätigen");
define("LANGPROFM","Für den ");
define("LANGPROFN","Hausaufgabe vom ");
define("LANGPROFO","Hausaufgabe ");
define("LANGPROFP","Einrichtung der Klassenlehrer");
define("LANGPROFQ","Für morgen");
define("LANGPROFR","Für gestern");
define("LANGPROFS","Fach oder Thema");
define("LANGPROFT","Antrag für D.S.T bestätigen");
define("LANGPROFU","Antrag gesendet -- Das TRIADE-Team");


define("LANGPROJ17","Anzahl der Abwesenheiten");
define("LANGPROJ18","Tage");

define("LANGCALEN10","Kalender der Klassenarbeiten"); // Devoirs sur table

define("LANGPARENT6","Liste der Verspätungen");
define("LANGPARENT7","Liste der Abwesenheiten");
define("LANGPARENT8","Abwesend am ");
define("LANGPARENT9","Liste der Befreiungen");
define("LANGPARENT10","Zeitraum&nbsp;vom&nbsp;");
define("LANGPARENT11","Um"); // indique une date (heure) -> Um (Uhrzeit)
define("LANGPARENT12","Am"); // indique une date jour -> Am (Tag)
define("LANGPARENT13","Attest");
define("LANGPARENT14","Disziplinarmaßnahme");
define("LANGPARENT15","Maßnahme");
define("LANGPARENT16","Im&nbsp;Nachsitzen");
define("LANGPARENT17","um");  // indique une heure
define("LANGPARENT18","Nachsitzen durchgeführt");
define("LANGPARENT19","Liste der Verwaltungsrundschreiben");
define("LANGPARENT20","Dateizugriff");
define("LANGPARENT21","Sichtbar für ");
define("LANGPARENT22","Ereigniskalender ");
define("LANGPARENT23","Kalender der Klassenarbeiten ");
define("LANGPARENT24","Antrag für D.S.T ");


define("LANGAUDIO1","Audio-Mitteilung");
define("LANGAUDIO2","Am "); // indique une date
define("LANGAUDIO3","A"); // première lettre (Audio)
define("LANGAUDIO3bis","udio-Mitteilung im <b>mp3</b>-Format<br>Maximale Dateigröße: ");
define("LANGAUDIO4","Mitteilung speichern");
define("LANGAUDIO5","Bitte warten Sie 2 bis 3 Minuten nach dem Senden der Audiodatei.");
define("LANGAUDIO6","Audio-Mitteilung löschen");


define("LANGOK","Ok");
define("LANGCLICK","Hier klicken");
define("LANGPRECE","Zurück");
define("LANGERROR1","Daten nicht gefunden");
define("LANGERROR2","keine Daten");


define("LANGPROF1","Fach angeben");
define("LANGPROF2","Anzahl der Noten");
define("LANGPROF3","Noten anzeigen");
define("LANGPROF4","Gruppe");
define("LANGPROF5","Quartal wählen");
define("LANGPROF6","Thema "); // sujet du devoir
define("LANGPROF7","Bezeichnung des Themas "); // sujet du devoir
define("LANGPROF8","Note"); //note d'un devoir
define("LANGPROF9","Hausaufgabe"); // Devoir Scolaire à faire à la maison
define("LANGPROF10","Note ändern");
define("LANGPROF11","Klassenarbeit löschen"); // devoir --> interrogation (Klassenarbeit)
define("LANGPROF12","Klassenlehrer");
define("LANGPROF13","Datenblatt ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGPROF14","Note hinzufügen in ");
define("LANGPROF15","Note ändern in");
define("LANGPROF16","Name der Arbeit");
define("LANGPROF17","Datum&nbsp;der&nbsp;Arbeit");
define("LANGPROF18","Bitte warten");
define("LANGPROF19","Änderung der Noten bestätigen");
define("LANGPROF20","Änderung der Noten validieren");
define("LANGPROF21","Notenänderung in");
define("LANGPROF22","Notenanzeige in");
define("LANGPROF23","Löschung einer Arbeit in");
define("LANGPROF24","Arbeit vom ");
define("LANGPROF25","ist gelöscht");
define("LANGPROF26","Informationen zum ".INTITULEELEVE);
define("LANGPROF27","Administrative Informationen");
define("LANGPROF28","Informationen zum Schulleben");
define("LANGPROF29","Medizinische Informationen");
define("LANGPROF30","Information vom");
define("LANGPROF31","Von"); // indiquant une personne


define("LANGEL1","Name");
define("LANGEL2","Vorname");
define("LANGEL3","Klasse ");
define("LANGEL4","FS1"); // Lv1
define("LANGEL5","FS2"); // Lv2
define("LANGEL6","Option");
define("LANGEL7","Status"); // Régime
define("LANGEL8","Geburtsdatum");
define("LANGEL9","Nationalität");
define("LANGEL10","Passwort");
define("LANGEL11","Familienname");
define("LANGEL12","Vorname");
define("LANGEL13","Straße");
define("LANGEL14","Adresse 1");
define("LANGEL15","Postleitzahl");
define("LANGEL16","Ort");
define("LANGEL17","Straße");
define("LANGEL18","Adresse 2");
define("LANGEL19","Postleitzahl");
define("LANGEL20","Ort");
define("LANGEL21","Telefon");
define("LANGEL22","Beruf des Vaters");
define("LANGEL23","Telefon des Vaters");
define("LANGEL24","Beruf der Mutter");
define("LANGEL25","Telefon der Mutter");
define("LANGEL26","Einrichtung");
define("LANGEL27","Einrichtungscode");
define("LANGEL28","Postleitzahl");
define("LANGEL29","Ort");
define("LANGEL30","Schülernummer");
// define("LANGEL30","Numéro National");


define("LANGPROF32","Schulische Informationen");
define("LANGPROF33","Hausaufgabe");
define("LANGPROF34","Einsicht nach Woche");
define("LANGPROF35","Letzte Woche");
define("LANGPROF36","Nächste Woche");
define("LANGTP23"," INFORMATION - Reservierungsanfrage !");
define("LANGRESA61","Name des Geräts");


define("LANGIMP46","Vorname");
define("LANGIMP47","Anrede (Herr oder Frau) "); // M. ou Mme ou Mlle
define("LANGIMP48","Name");
define("LANGIMP49","* Pflichtfeld");
define("LANGIMP50","Die zu übertragende Datei <FONT color=RED><B>MUSS</B></FONT> <FONT COLOR=red><B>9</B></FONT> Felder enthalten <I>(nicht leer)</I>, getrennt durch dasselbe Trennzeichen \"<FONT color=red><B>;</B></font>\" <I>(d.h. das Zeichen \"<FONT color=red><B>;</B></font>\" muss 8 Mal vorhanden sein)</I>");
define("LANGIMP51","Passwort Elternteil");
define("LANGIMP52","Passwort ".INTITULEELEVE);


define("LANGacce_dep1","Verbindungsfehler");
define("LANGacce_dep2","Überprüfen Sie Ihre Anmeldedaten. Wenn das Problem weiterhin besteht, <br /> benachrichtigen Sie Ihren TRIADE-Administrator über den Link <br /> 'Zugangsproblem' im linken Menü");

define("LANGacce_ref1","Fehlertyp: Zugriff nicht autorisiert");
define("LANGacce_ref11","Besucht am ");
define("LANGacce_ref12","von ");
define("LANGacce_ref13","mit ");
define("LANGacce_ref2","ZUGRIFF NICHT AUTORISIERT");
define("LANGacce_ref3","Um auf Ihr Konto zuzugreifen, müssen Sie sich anmelden.");
define("LANGacce1","Der ".INTITULEELEVE." ");
define("LANGacce12","hat eine Strafarbeit zu erledigen, <br> aufgrund der Kategorie: ");
define("LANGacce13","aus dem Grund ");
define("LANGacce14","Die zu erledigende Aufgabe ist folgende: ");
define("LANGacce2","Diese Nachricht löschen: ");
define("LANGacce21","Löschen");
define("LANGacce3","Der ".INTITULEELEVE." ");
define("LANacce31","hat sich nicht</b></font> beim Schulleben (CPE) gemeldet, <b>für das Nachsitzen</b>, aufgrund der Kategorie:"); // CPE (Conseiller Principal d'Éducation) etwa: Schulsozialarbeiter/Vertrauenslehrer
define("LANacce32","aus dem Grund: ");
define("LANGacce4","Die zu erledigende Aufgabe ist folgende:");
define("LANGacce5","Löschen");
define("LANGacce6","Disziplinarverwaltung");
define("LANGaccrob11","Download der Software Adobe Acrobat Reader 8.1.0 de");
define("LANGaccrob2","23,4 MB für Windows 2000/XP/2003/Vista");
define("LANGaccrob3","Downloadzeit:");
define("LANGaccrob4","mit 56 K: 57 Min. und 3 Sek.");
define("LANGaccrob5","mit 512 K: 6 Min. und 14 Sek.");
define("LANGaccrob6","mit 5 M: 37 Sekunden");
define("LANGaccrob7","Download der Software Adobe Acrobat Reader 6.O.1 de");
define("LANGaccrob8","Größe: ");
define("LANGaccrob9","0.40916 MB für NT/95/98/2000/ME/XP");
define("LANGaccrob10","mit 56 K: 0 Min. und 58,2 Sek.");
define("LANGaccrob11bis","mit 512 K: 0 Min. und 6,6 Sek. ");
define("LANGaffec_cre21","Zuweisungserstellung für die  ".INTITULECLASSE);
define("LANGaffec_cre22","Zuweisung wird eingerichtet ");
define("LANGaffec_cre23","Die Zuweisungssoftware startet automatisch<br>Wenn die neue Seite nicht erscheint, klicken Sie ");
define("LANGaffec_cre24","TRIADE - Konto von ");
define("LANGaffec_cre31","ERSTELLUNG - ZUWEISUNG");
define("LANGaffec_cre41","Drucken");
define("LANGaffec_mod_key1","Zuweisung der ".INTITULECLASSE."n");
define("LANGaffec_mod_key2","Modul zur Änderung der Zuweisung der ".INTITULECLASSE."n.");
define("LANGaffec_mod_key3","ACHTUNG, dieses Modul ist bei Änderungen der Zuweisung zu verwenden,<br> es löscht alle Noten der ".INTITULEELEVE." der geänderten ".INTITULECLASSE."n. ");
define("LANGaffec_mod_key4","ACHTUNG, die Noten der ausgewählten ".INTITULECLASSE."n werden gelöscht. \\n Möchten Sie fortfahren? \\n\\n Das TRIADE-Team");
define("LANGattente1","Warten - TRIADE");
define("LANGattente2","Bitte warten Sie, S.V.P.");
define("LANGattente3","Das TRIADE-Team.");
define("LANGatte_mess1","TRIADE - Warten - Nachrichtensystem");
define("LANGatte_mess2","Bitte warten Sie, S.V.P.");
define("LANGatte_mess3","TRIADE-Dienst");
define("LANGbasededon20","Datei senden");
define("LANGbasededon201","nichts");
define("LANGbasededon2011","Import der GEP-Datei");
define("LANGbasededon202","Datei übertragen -- Das TRIADE-Team");
define("LANGbasededon203","Datei nicht gespeichert");
define("LANGbasededon31","Geben Sie für jede Referenz die entsprechende ".INTITULECLASSE." an");
define("LANGbasededon32","Auswahl ...");
define("LANGbasededon33","keine");
define("LANGbasededon34","Das Senden der Datei kann je nach Anzahl der ".INTITULEELEVE." <b>2 bis 4 Minuten</b> dauern.");
define("LANGbasededon35","Die Datei muss im <b>dbf</b>-Format sein und <b>F_ele.dbf</b> heißen");
define("LANGbasededon41","Fehler bei der Anzahl der ".INTITULECLASSE."n !!! - Kontaktieren Sie das TRIADE-Team <br /><br /> support@triade-educ.org</center>");
define("LANGbasededon42","Fehler bei der Eingabe der ".INTITULECLASSE."n, eine ".INTITULECLASSE." wird mehrmals wiederholt -- Das TRIADE-Team");
define("LANGbasededon43","Nachricht vom: ");
define("LANGbasededon44","Von");
define("LANGbasededon45","Mitglied:");
define("LANGbasededon46","Nachricht:");
define("LANGbasededon47","NEUE DATENBANK:");
define("LANGbasededon48","- mit GEP");
define("LANGbasededon49"," Einrichtung:");
define("LANGbasededoni11","'Achtung','./image/commun/warning.jpg','<font face=Verdana size=1><font color=red>D</font>as Modul <b>dbase</b> ist nicht <br> geladen!! <i>Notwendig zum Importieren <br> einer GEP-Datenbank.");
define("LANGbasededoni21","ACHTUNG, die alte Datenbank wird automatisch gelöscht. \\n Möchten Sie fortfahren? \\n\\n Das TRIADE-Team");
define("LANGbasededoni31","Geben Sie an, für welche Kategorie die Datei bestimmt ist ");
define("LANGbasededoni32","Der Dateiimport betrifft: ");
define("LANGbasededoni33","Import der ".INTITULEELEVE.": ");
define("LANGbasededoni34","Import der ".INTITULEENSEIGNANT.":");
define("LANGbasededoni35","Import des Schullebenspersonals: ");
define("LANGbasededoni36","Import des Verwaltungspersonals: ");
define("LANGbasededoni41","Vorherige Klasse");
define("LANGbasededoni42","Vorheriges Jahr");
define("LANGbasededoni51","Für die Bezeichnung");

define("LANGbasededoni61","Fehler");
define("LANGbasededoni71","Import der ASCII-Datei");
define("LANGbasededoni72","Nachricht vom: ");
define("LANGbasededoni721","Von");
define("LANGbasededoni722","Mitglied:");
define("LANGbasededoni723","Nachricht:");
define("LANGbasededoni724","NEUE DATENBANK:");
define("LANGbasededoni725","- mit ASCII");
define("LANGbasededoni726"," Einrichtung:");
define("LANGbasededoni73","Gesamtzahl der Einträge in der Datenbank ");
define("LANGbasededoni91","Import der ASCII-Datei");
define("LANGbasededoni92","Fehler bei der Anzahl der ".INTITULECLASSE."n !!! - Kontaktieren Sie das TRIADE-Team <br />");
define("LANGbasededoni93","Fehler bei der Eingabe der ".INTITULECLASSE."n, eine ".INTITULECLASSE." wird mehrmals wiederholt -- Das TRIADE-Team");
define("LANGbasededoni94","Daten der Datenbank verarbeitet -- Das TRIADE-Team<br />");
define("LANGbasededoni95","Gesamtzahl der ".INTITULEELEVE." in der Datenbank gespeichert: ");
define("LANGPIEDPAGE","<p> Die <b>T</b>ransparenz und <b>R</b>apidität der <b>I</b>nformatik <b>A</b>m Dienst <b>D</b>er <b>E</b>rziehung<br>Um diese Seite optimal anzuzeigen: Mindestauflösung: 800x600 <br>  © 2000 - ".date("Y")." TRIADE - Alle Rechte vorbehalten");

define("LANGAPROPOS1","Version");
define("LANGAPROPOS2","Alle Rechte vorbehalten");
define("LANGAPROPOS3","Nutzungslizenz");
define("LANGAPROPOS4","Produkt-ID");

define("LANGTELECHARGER","Herunterladen");
define("LANGAJOUT1","Für den Status: mögliche Auswahl (<b>INT</b> (Intern),<b>EXT</b> (Extern), <b>DP</b> (Halbpension)<br><br>"); // Régime
define("LANGIMP44","Die Datei ist nicht konform.");
define("LANGBASE16"," Die Spalten sind wie folgt dargestellt: <b>Loginname; Vorname Login; Passwort Elternteil; Passwort ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." im Klartext; ".INTITULECLASSE." des ".INTITULEELEVE." </b>");


define("LANGSUPP0","Löschung eines Vertretungskontos");
define("LANGSUPP1","Löschmodul");
define("LANGSUPP2","Konto löschen");
define("LANGSUPP3","Möchten Sie aus der Liste der Vertretungen löschen");
define("LANGSUPP3bis","Vertretung von");
define("LANGSUPP4","Löschung bestätigen");
define("LANGSUPP5","Dieses Konto kann nicht gelöscht werden. \\n\\n Konto einer ".INTITULECLASSE." zugewiesen.  \\n\\n  Das TRIADE-Team");
define("LANGSUPP6","Konto gelöscht - Das TRIADE-Team");
define("LANGSUPP7","Löschung einer Gruppe");
define("LANGSUPP8","Gruppe löschen");
define("LANGSUPP9","Löschung eines Kontos ");
define("LANGSUPP10","Konto löschen");
define("LANGSUPP11","ein Mitglied des Schullebens");
define("LANGSUPP12","ein Administrator");
define("LANGSUPP13","ein ".INTITULEENSEIGNANT."");
define("LANGSUPP14","Löschung eines ".INTITULEELEVE." in der ".INTITULECLASSE);
define("LANGSUPP15","Klicken Sie auf den zu löschenden ".INTITULEELEVE);
define("LANGSUPP16","Löschung eines ".INTITULEELEVE."");
define("LANGSUPP17","wird aus der Datenbank gelöscht");
define("LANGSUPP18","Alle Informationen über diesen ".INTITULEELEVE." werden gelöscht, nämlich: <br> (Noten, Abwesenheiten, Verspätungen, Befreiungen, Sanktionen, Informationen, Nachrichten, ...)");
define("LANGSUPP19","Löschung abbrechen");
define("LANGSUPP20","ist aus der Datenbank gelöscht");
define("LANGSUPP21",INTITULECLASSE." löschen");
define("LANGSUPP22","Löschung einer ".INTITULECLASSE);
define("LANGSUPP23","Löschung eines Fachs oder Teilbereichs");
define("LANGSUPP24","Fach löschen");
define("LANGSUPP25","Klasse gelöscht -- TRIADE-Dienst");
define("LANGSUPP26","Fach gelöscht -- TRIADE-Dienst");
define("LANGSUPP27","Erstellung des Fachs");
define("LANGSUPP28","Teilbereich gespeichert");

define("LANGADMIN","Verwaltung");
define("LANGPROF",ucfirst(INTITULEENSEIGNANT));
define("LANGSCOLAIRE","des Schullebens");
define("LANGCLASSE","eine ".INTITULECLASSE);


define("LANGGRP11","Name der Gruppe");
define("LANGGRP12","Betroffene Klasse(n)");
define("LANGGRP13","Liste ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGGRP14","Liste der Gruppen");
define("LANGGRP15","Erstellung einer Gruppe");
define("LANGGRP16","Geben Sie die ".INTITULEELEVE." in der Gruppe an");
define("LANGGRP17","Auswählen");
define("LANGGRP18","Gruppe speichern");
define("LANGGRP19","Gruppenerstellung durchgeführt");
define("LANGGRP20","Andere Gruppe");
define("LANGGRP21","Liste der Gruppen");
define("LANGGRP22","Bitte geben Sie eine ".INTITULECLASSE." für die Gruppenerstellung an. \\n\\n Das TRIADE-Team");
define("LANGGRP23","Liste der ".INTITULEELEVE." der Gruppe");
define("LANGGRP24","Liste der ".INTITULECLASSE."n");
define("LANGGRP25","Liste der Fächer");

//----------------//
define("LANGDONNEENR","<font class=T2>Daten gespeichert.</font>");

define("LANGABS47","Hinzufügen einer Disziplinarmaßnahme");
define("LANGABS48"," hat erreicht ");
define("LANGABS48bis","Mal die Kategorie");
define("LANGABS49","Dauer");
define("LANGABS50"," Nachsitzen vom ");
define("LANGABS51","Tel. Beruf Vater ");
define("LANGABS52","Tel. Beruf Mutter ");
define("LANGABS53","Keine Verspätung oder Abwesenheit gemeldet");

define("LANGCALRET1","Kalender &nbsp; der &nbsp; Nachsitzen");

define("LANGHISTO1","Verlauf der Operationen");

define("LANGDST9","Eintrag hinzufügen");
define("LANGDST10","Eintrag löschen");
define("LANGDST11","in ".INTITULECLASSE." von");

define("LANGDISP11","<b>Vollständige</b> Anzeige der Befreiungen");

define("LANGEN","In");

define("LANGAFF4","Bearbeitung einer ".INTITULECLASSE);
define("LANGAFF5","Alle ".INTITULECLASSE."n");
define("LANGAFF6","Diese ".INTITULECLASSE." einsehen");

define("LANGCHER1","Komplexe Suche");
define("LANGCHER2","Geben Sie das zu generierende Dateiformat an");
define("LANGCHER3","Geben Sie das Feldtrennzeichen an");
define("LANGCHER4","Suche nach einem ".INTITULEELEVE." anhand des Namens durchführen: <b>hier klicken</b>");
define("LANGCHER5","Hinzufügen");
define("LANGCHER6","Entfernen");
define("LANGCHER7","Nach oben");
define("LANGCHER8","Nach unten");
define("LANGCHER9","Weiter");
define("LANGCHER10","Gesuchtes Element");
define("LANGCHER11","Anzahl der Suchkriterien");
define("LANGCHER12","Ab");

define("LANGCHER13","mit dem Wert");
define("LANGCHER14","Ungefähre Suche");
define("LANGCHER15","Genaue Suche");
define("LANGCHER16","Suche starten");
define("LANGCHER17","Achtung: Es verbleibt ein nicht ausgewähltes Element!! -- Das TRIADE-Team ");

define("LANGCHER18","mit dem Wert");

define("LANGTITRE34","Konfiguration der Verspätungsbenachrichtigung");
define("LANGTITRE35","Konfiguration der Abwesenheitsbenachrichtigung");

define("LANGCONFIG1","Konfiguration gespeichert.");
define("LANGCONFIG2","Hier ist Ihr Text ");

define("LANGCONFIG3","Geben Sie die Liste der Eltern von ".INTITULEELEVE." an, die eine Benachrichtigung erhalten sollen");

define("LANGERROR01","Fehler beim Zugriff auf die Datenbank");
define("LANGERROR02","ACHTUNG Unmöglich <br><br>Das Problem kann von den eingegebenen Informationen herrühren <br>(Überprüfen Sie die verschiedenen Felder vor der Bestätigung).<BR>  <BR>Oder die Information ist bereits gespeichert ODER nicht zugänglich.");
define("LANGERROR03","Zugriff auf die Datenbank für diese Aktion unmöglich. <BR>");

define("LANGABS54","ist bereits als abwesend gemeldet.");
define("LANGABS55","ist bereits als verspätet gemeldet.");


define("LANGPARAM4","Das Attest ist korrekt gespeichert.");
define("LANGPARAM5","Die Schulbescheinigung der ".INTITULEELEVE." der ".INTITULECLASSE);
define("LANGPARAM5bis","ist im PDF-Format verfügbar");
define("LANGPARAM6","Parametrisierung für den Inhalt der Zeugnisse und Zeiträume");

define("LANGPARAM7","Name des Schulleiters");
define("LANGPARAM8","Name der Einrichtung");
define("LANGPARAM9","Adresse");
define("LANGPARAM10","Postleitzahl");
define("LANGPARAM11","Stadt");
define("LANGPARAM12","Telefon");
define("LANGPARAM13","E-Mail");
define("LANGPARAM14","Logo der Einrichtung");
define("LANGPARAM15","Parameter speichern");
define("LANGPARAM16","Speicherung durchgeführt. -- Das TRIADE-Team");

define("LANGCERTIF1","Die Schulbescheinigung von ");
define("LANGCERTIF1bis","ist im PDF-Format verfügbar");


define("LANGRECHE1","Informationen zum ".INTITULEELEVE."");

define("LANGBT52","Daten ändern");

define("LANGEDIT1","Daten nicht gefunden");

define("LANGMODIF1","Aktualisierung eines Kontos ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGMODIF2","Informationen zum ".INTITULEELEVE);
define("LANGMODIF3","Informationen zur Familie");

define("LANGALERT1","Daten aktualisiert -- TRIADE-Team");
define("LANGALERT2","Achtung, Dateiformat nicht konform oder Größe nicht eingehalten");
define("LANGALERT3","Achtung, Dateiformat nicht konform oder Größe nicht eingehalten");

define("LANGLOGO1","Zu übertragendes Logo");
define("LANGLOGO2","Logo speichern");
define("LANGLOGO3","Das Logo <b>muss im jpg-Format sein</b> und eine Größe von 96px mal 96px haben.");

define("LANGPARAM17","Definition der Quartals- oder Halbjahreszeiträume");
define("LANGPARAM18","Quartal oder Halbjahr");
define("LANGPARAM19","Anfangsdatum");
define("LANGPARAM20","Enddatum");
define("LANGPARAM21","Erstes");
define("LANGPARAM22","Zweites");
define("LANGPARAM23","Drittes");
define("LANGPARAM24","Quartalsdaten speichern");
define("LANGPARAM25","Daten berücksichtigt, wenn die Speicherung im Quartalsformat erfolgt");
define("LANGPARAM26","Ungültiges Datum -- TRIADE-Team");
define("LANGPARAM27","Informationen gespeichert -- TRIADE-Team");
define("LANGPARAM28","Quartal");
define("LANGPARAM29","Halbjahr");
define("LANGPARAM30","Zeugnis");


define("LANGBULL5","Zeugnisdruck");
define("LANGBULL6","Verarbeitung fortsetzen");
define("LANGBULL7","Zeitraum drucken");
define("LANGBULL8","Geben Sie den Beginn des Zeitraums an");
define("LANGBULL9","Geben Sie das Ende des Zeitraums an");
define("LANGBULL10","Geben Sie den Zeitraum an");
define("LANGBULL11","Geben Sie den Bereich an");
define("LANGBULL12","Zeitraum drucken");
define("LANGBULL13","Verlauf");
define("LANGBULL14","<FONT COLOR='red'>ACHTUNG</FONT></B> Benötigt <B>Adobe Acrobat Reader</B>. Kostenlose Software und Download ");
define("LANGBULL14bis","Herunterladen");
define("LANGBULL15","Anzeigen / Löschen");
define("LANGBULL16","Name des ".INTITULEELEVE."s");
define("LANGBULL17","Lehrkraft");
define("LANGBULL18","Notendetails");
define("LANGBULL19","Beurteilung des Klassenlehrers");
define("LANGBULL20","NOTENÜBERSICHT");
define("LANGBULL21","Zeitraum");

define("LANGBULL22","erstes Quartal");
define("LANGBULL23","zweites Quartal");
define("LANGBULL24","drittes Quartal");

define("LANGBULL25","erstes Halbjahr");
define("LANGBULL26","zweites Halbjahr");

define("LANGBULL27","Zeugnis vom ");
define("LANGBULL28","Bereich");
define("LANGBULL29","Schuljahr");

define("LANGBULL30","ZEUGNIS");

define("LANGBULL31","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGBULL32","Fächer");
define("LANGBULL33","Klasse");
define("LANGBULL34","Beurteilungen, Fortschritte, Ratschläge zur Verbesserung");

define("LANGBULL35","Koeff");
define("LANGBULL36","Durchschn."); // Moy
define("LANGBULL37","Min");
define("LANGBULL38","Max");
define("LANGBULL39","Anwesenheit und Verhalten in der Einrichtung: ");
define("LANGBULL40","Gesamtbeurteilung des pädagogischen Teams: ");
define("LANGBULL41","Zeugnis sorgfältig aufbewahren");
define("LANGBULL42","Visum des Schulleiters oder seines Vertreters");
define("LANGBULL43","SCHULJAHR");
define("LANGBULL44","Herr & Frau"); // M. & Mme
define("LANGOU","oder"); // le ou de ou bien


define("LANGPROJ19","Halbjahr 1");
define("LANGPROJ20","Halbjahr 2");

define("LANGDISC1","Nachsitzen vom ");
define("LANGDISC2","Nachsitzen des Tages drucken");


define("LANGDISC3","Tel. Privat ");
define("LANGDISC4","Tel. Beruf Vater ");
define("LANGDISC5","Tel. Beruf Mutter ");
define("LANGDISC6","Einrichtung einer Maßnahme in Klasse ");
define("LANGDISC7","Bezeichnung der Kategorie ");
define("LANGDISC8","Bezeichnung der Maßnahme ");
define("LANGDISC9","Zugewiesen von ");
define("LANGDISC10","Grund, Informationen, zu erledigende Aufgabe ");
define("LANGDISC11","Nachsitzen");
define("LANGDISC11bis","Am");  // Le pour indiquer une date
define("LANGDISC11Ter","Um");  // A pour indiquer une heure
define("LANGDISC12","Dauer");
define("LANGDISC13","<font color=red>K</font></B>reuzen Sie das Kästchen an, wenn der ".INTITULEELEVE." entweder nachsitzen muss oder eine Maßnahme erhält.");
define("LANGDISC14","Hinzufügen einer Disziplinarmaßnahme");
define("LANGDISC15","<B>*<I> P</B>: Telefon Privat, <B>BV</B>: Telefon Beruf Vater, <B>BM</B>: Telefon Beruf Mutter</I>"); // D -> P (Privat)
define("LANGDISC16","Durchführen");
define("LANGDISC17","Tel.");
define("LANGDISC18","Anzeige der Maßnahmen");
define("LANGDISC19","Anzeige der <b>5</B> letzten Maßnahmen");
define("LANGDISC20","Kategorie");
define("LANGDISC21","Vollständige Liste von ");
define("LANGDISC22","Nachsitzen anzeigen von ");
define("LANGDISC23","Anzeige der Nachsitzen");
define("LANGDISC24","<b>Vollständige</b> Anzeige der Nachsitzen");
define("LANGDISC25","Im&nbsp;Nachsitzen");
define("LANGDISC26","Nachsitzen nicht durchgeführt");
define("LANGDISC27","Maßnahmen auflisten von ");
define("LANGDISC28","Anzeige der Maßnahmen");
define("LANGDISC29","<b>Vollständige</b> Anzeige der Maßnahmen");
define("LANGDISC30","Eingabe&nbsp;am");
define("LANGDISC31","Maßnahmen auflisten von ");
define("LANGDISC32","Nachsitzen keinem Schüler zugewiesen ");
define("LANGDISC33","ACHTUNG der ".INTITULEELEVE." ");
define("LANGDISC33bis"," ist bereits für das angegebene Datum und die angegebene Uhrzeit zum Nachsitzen eingetragen. ");
define("LANGDISC34","hat erreicht");
define("LANGDISC34bis","Mal die Kategorie");
define("LANGDISC35","Maßnahme löschen");
define("LANGDISC36","Nachsitzen löschen");

define("LANGattente222","Bitte warten");

define("LANGSUPP","Lösch"); // abréviation de Supprimer -> Löschen

define("LANGCIRCU1","Verwaltung der Verwaltungsrundschreiben");
define("LANGCIRCU2","Ein Rundschreiben hinzufügen");
define("LANGCIRCU3","Rundschreiben auflisten");
define("LANGCIRCU4","Ein Rundschreiben löschen");
define("LANGCIRCU5","Hinzufügen von Verwaltungsrundschreiben");
define("LANGCIRCU6","Betreff");
define("LANGCIRCU7","Referenz");
define("LANGCIRCU8","Rundschreiben");
define("LANGCIRCU9","Korps ".ucfirst(INTITULEENSEIGNANT));
define("LANGCIRCU10","In der oder den ".INTITULECLASSE."(n)");
define("LANGCIRCU11","<font face=Verdana size=1><B><font color=red>R</font></B>undschreiben im Format: <b>doc</b>, <b>pdf</b>, <b>txt</b>, <b>Office</b>.</FONT>");
define("LANGCIRCU12","<font face=Verdana size=1><B><font color=red>R</font></B>undschreiben sichtbar für die ".INTITULEENSEIGNANT.".</FONT>");
define("LANGCIRCU13","Alle ".INTITULECLASSE."n");
define("LANGCIRCU14","Zurück zum Menü");
define("LANGCIRCU15","Rundschreiben speichern");
define("LANGCIRCU16","Rundschreiben nicht gespeichert");
define("LANGCIRCU17","Die Datei muss im Format <b>txt, doc oder pdf</b> sein und kleiner als 2MB ");
define("LANGCIRCU18","<font class=T2>Rundschreiben gespeichert</font>");
define("LANGCIRCU19","Verwaltungsrundschreiben löschen");
define("LANGCIRCU20","Dateizugriff");
define("LANGCIRCU21","<font color=red>R</b></font><font color=#000000>eferenz");

define("LANGCODEBAR1","Barcode-Verwaltung");
define("LANGCODEBAR2","Dieses Modul funktioniert nicht mit Ihrem Server. <br> Sie benötigen PHP 5 oder höher, um dieses Modul zu verwenden.");
define("LANGCODEBAR3","Hier ist die Liste der von TRIADE zugänglichen Barcodes");
define("LANGCODEBAR4","Der standardmäßig verwendete Barcode ist ");
define("LANGCODEBAR5","Liste");


define("LANGPUB1","Hinzufügen eines Werbebanners");
define("LANGPUB2","Sie möchten auf der TRIADE-Website werben");
define("LANGPUB3","Eine Werbekampagne durchführen");
define("LANGPUB4","Dafür ");
define("LANGPUB5","Sie sind bereits Werbetreibender auf TRIADE ");

define("LANGPROFB1","Beurteilung für die Quartalszeugnisse");
define("LANGPROFB2","Parametrisierung Ihrer automatisierten Kommentare");
define("LANGPROFB3","Parametrisierung");
define("LANGPROFB4","Konfiguration Kommentare Zeugnisse");
define("LANGPROFB5","Speicherung der Kommentare");
define("LANGPROFB6","Kommentar");
define("LANGPROFB7","Liste");


define("LANGPROFC1","Kalender der Geräteplanung");
define("LANGPROFC2","Kalender der Raumplanung");


define("LANGPARAM31","Anzeige im U.S.A.-Modus"); // Mode U.S.A.
define("LANGPARAM32","Anwesenheit und Verhalten in der Einrichtung: ");
define("LANGPARAM33","PDF-Datei abrufen");

define("LANGDISC37","Hinzufügen einer Disziplinarmaßnahme");

define("LANGPROFP4","<b>Klassenlehrer</b> in ");
define("LANGPROFP5","Informationen zum ".INTITULEELEVE);
define("LANGPROFP6","Informationen vom ");
define("LANGPROFP7","bis zum ");

define("LANGPROFP8","Gesamtzahl der Verspätungen");
define("LANGPROFP9","Anzahl der Verspätungen in diesem Quartal");
define("LANGPROFP10","Gesamtzahl der Abwesenheiten");
define("LANGPROFP11","Anzahl der Abwesenheiten in diesem Quartal");

define("LANGPROFP12","Verwaltung der Vertreter");
define("LANGPROFP13"," in ".INTITULECLASSE." von ");
define("LANGPROFP14","Elternvertreter");
define("LANGPROFP15","Kontaktdaten");
define("LANGPROFP16","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."-Vertreter");
define("LANGPROFP17","Elternvertreter");
define("LANGPROFP18","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."-Vertreter");
define("LANGPROFP19","Tel."); // pour téléphone
define("LANGPROFP20","Mail");
define("LANGPROFP21","Zusätzliche medizinische Informationen zum ".INTITULEELEVE);

define("LANGETUDE1","Studienzeitverwaltung"); // Gestion des études
define("LANGETUDE2","Zuweisung der ".INTITULEELEVE." zur Studienzeit");
define("LANGETUDE3","Liste der zugewiesenen Studienzeiten einsehen");
define("LANGETUDE4","Eine Studienzeit hinzufügen");
define("LANGETUDE5","Eine Studienzeit ändern");
define("LANGETUDE6","Eine Studienzeit löschen");
define("LANGETUDE7","Einsicht in eine Studienzeit");
define("LANGETUDE8",INTITULEELEVE." einer Studienzeit zuweisen");
define("LANGETUDE9",INTITULEELEVE." einer Studienzeit ändern");
define("LANGETUDE10",INTITULEELEVE." aus einer Studienzeit entfernen");
define("LANGETUDE11","Liste der Studienzeiten");

define("LANGETUDE12","Aufsichtsperson");
define("LANGETUDE13","Studienzeit");
define("LANGETUDE14","Im Raum");
define("LANGETUDE15","Woche");
define("LANGETUDE16","Am");  		// Le indique une date
define("LANGETUDE17","um");  		// à indique une heure
define("LANGETUDE18","während");  	//indique une durée
define("LANGETUDE19","Erstellung einer Studienzeit");
define("LANGETUDE20","Name der Studienzeit");
define("LANGETUDE21","Wochentag");
define("LANGETUDE22","Uhrzeit der Studienzeit");
define("LANGETUDE23","Dauer der Studienzeit");
define("LANGETUDE24","hh:mm");
define("LANGETUDE25","Studienraum");
define("LANGETUDE26","Aufsichtsperson dieser Studienzeit");
define("LANGETUDE27","Die Studienzeit ist gespeichert");
define("LANGETUDE28","Liste der Studienzeiten");
define("LANGETUDE29","Änderung einer Studienzeit");
define("LANGETUDE30","Die Studienzeit enthält ".INTITULEELEVE.". Löschen Sie die Liste der ".INTITULEELEVE." der Studienzeit, bevor Sie die Studienzeit löschen");
define("LANGETUDE31","Liste ".INTITULEELEVE);
define("LANGETUDE32","Liste der ".INTITULEELEVE."");
define("LANGETUDE33","Zuweisung eines ".INTITULEELEVE." zu einer Studienzeit");
define("LANGETUDE34","Wahl der Studienzeit");
define("LANGETUDE35","Geben Sie die ".INTITULECLASSE."n für die Zuweisung der ".INTITULEELEVE." zu dieser Studienzeit an");
define("LANGETUDE36","Bezeichnung der Studienzeit");
define("LANGETUDE37","Geben Sie die ".INTITULEELEVE." in dieser Studienzeit an");
define("LANGETUDE38","darf gehen"); // autorisé à sortir
define("LANGETUDE39","Studienzeit speichern");
define("LANGETUDE40","Andere Studienzeit");
define("LANGETUDE41","Studienzeit eines ".INTITULEELEVE." ändern");
define("LANGETUDE42","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." in Studienzeit");
define("LANGETUDE43","Änderungen speichern");
define("LANGETUDE44","Gehen erlaubt");
define("LANGETUDE45","Studienzeit eines ".INTITULEELEVE." löschen");

define("LANGLIST1","Bearbeitung einer ".INTITULECLASSE);
define("LANGLIST2","Liste der ".INTITULEENSEIGNANT." der ".INTITULECLASSE);
define("LANGLIST3","Klassenlehrer");
define("LANGLIST4","Datum");
define("LANGLIST5","Vollständige Liste im PDF-Format");
define("LANGLIST6","Klassenlehrer");


define("LANGPASS1","Neues Passwort");

define("LANGTRONBI1","Fotoliste der ".INTITULEELEVES." anzeigen");
define("LANGTRONBI2","Fotoliste der ".INTITULEELEVES." ändern");
define("LANGTRONBI3","Achtung, Dateiformat nicht konform");
define("LANGTRONBI4","Unmöglich, Foto hat nicht konforme Größe");
define("LANGTRONBI5","Name ".INTITULEELEVE);
define("LANGTRONBI6","Vorname ".INTITULEELEVE);
define("LANGTRONBI7","das Foto");
define("LANGTRONBI8","Foto hinzufügen");


define("LANGBASE19","Die ausgewählte Datei ist ungültig");
define("LANGBASE20","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." ohne ".INTITULECLASSE);
define("LANGBASE21","Anzahl der ".INTITULEELEVE." ohne ".INTITULECLASSE);
define("LANGBASE22","Anzeige der ersten 30");
define("LANGBASE23","Wechsel der ".INTITULECLASSE." für die ".INTITULEELEVE."");
define("LANGBASE24","Wechsel abgeschlossen");
define("LANGBASE25","VOR ALLEN ÄNDERUNGEN UNSERE HILFE KONSULTIEREN");
define("LANGBASE26","Wechsel der ".INTITULECLASSE." für die ".INTITULEELEVE." der ".INTITULECLASSE);
define("LANGBASE27","Information über den Wechsel der ".INTITULECLASSE." eines ".INTITULEELEVE);
define("LANGBASE28","<b>Kein Wechsel.</b> <i>(Mit der Option 'Auswahl ...')</i>");
define("LANGBASE29","Es erfolgt keine Löschung von Informationen des ".INTITULEELEVE.".");
define("LANGBASE30","<b>Der Wechsel der ".INTITULECLASSE.".</b> <i>(Mit Angabe einer ".INTITULECLASSE.")</i>");
define("LANGBASE31","Löschung Noten, Abw., Versp., Disziplin, Befreiungen des ".INTITULEELEVE.".");
define("LANGBASE32","<b>Verlässt die Schule.</b>  <i>(Mit der Option 'Verlässt die Schule')</i>");
define("LANGBASE33","Löschung des ".INTITULEELEVE." aus der Datenbank.");
define("LANGBASE34","Löschung Noten, Abw., Versp., Disziplin, Befreiungen des ".INTITULEELEVE.".");
define("LANGBASE35","Löschung interner Nachrichten der Familie.");
define("LANGBASE36","Geht in ".INTITULECLASSE." von");
define("LANGBASE37","Verlässt die Schule");
define("LANGBASE38","Den/die Wechsel bestätigen");
define("LANGBASE39","Wählen Sie ein Element");

define("LANGBASE40","Auswahl des ");

// MODULE AGENDA
define("LANGAGENDA1","Achtung!!!\nDer Termin, den Sie gerade erstellt oder geändert haben, überschneidet sich\nmit einem anderen Termin für die folgenden Benutzer");
define("LANGAGENDA2","Möchten Sie diesen Ihnen zugewiesenen Termin löschen?");
define("LANGAGENDA3","Einen Termin löschen, Erinnerung:\n\n - Alle aus diesem Termin resultierenden Vorkommen werden ebenfalls gelöscht\n - Um nur ein Vorkommen zu löschen, klicken Sie auf das entsprechende Bild rechts neben dem Termin in den Plänen\n\nMöchten Sie diesen Termin löschen?");
define("LANGAGENDA4","Ein Vorkommen löschen, Erinnerung:\n\n - Nur dieses Vorkommen wird gelöscht\n - Um einen wiederkehrenden Termin und alle seine Vorkommen zu löschen, klicken Sie auf das Kreuz rechts neben dem Termin in den Plänen oder bearbeiten Sie den Termin und klicken Sie auf die Schaltfläche [Löschen]\n\nMöchten Sie dieses Vorkommen löschen?");
define("LANGAGENDA5","Termin mit Erinnerung");
define("LANGAGENDA6","Ein Vorkommen löschen");
define("LANGAGENDA7","Einen Termin löschen");
define("LANGAGENDA8","Einen Termin übernehmen");
define("LANGAGENDA9","Details anzeigen");
define("LANGAGENDA10","Persönlicher Termin");
define("LANGAGENDA11","Zugewiesener Termin");
define("LANGAGENDA12","Aktiver Termin");
define("LANGAGENDA13","Abgeschlossener Termin");
define("LANGAGENDA14","Aktueller Tag");
define("LANGAGENDA15","Feiertag");
define("LANGAGENDA16","Einen Termin erstellen");
define("LANGAGENDA17","Klicken zum Ändern");
define("LANGAGENDA18","Einen Geburtstag speichern");
define("LANGAGENDA19","Änderung eines Geburtstags");
define("LANGAGENDA20","Bitte geben Sie den Namen der Person ein");
define("LANGAGENDA21","Bitte geben Sie das Geburtsdatum der Person ein");
define("LANGAGENDA22","Geburtstag von");
define("LANGAGENDA23","Geburtsdatum");
define("LANGAGENDA24","Format TT.MM.JJJJ");
define("LANGAGENDA25","Diesen Geburtstag löschen?");
define("LANGAGENDA26","Löschen");
define("LANGAGENDA27","Abbrechen");
define("LANGAGENDA28","Speichern");
define("LANGAGENDA29","Sind Sie sicher, dass Sie diesen Geburtstag löschen möchten?");
define("LANGAGENDA30","Ändern");
define("LANGAGENDA31","Vorheriges Jahr");
define("LANGAGENDA32","Vorheriger Monat");
define("LANGAGENDA33","Zum heutigen Datum wechseln");
define("LANGAGENDA34","Gedrückt halten für Menü");
define("LANGAGENDA35","Nächster Monat");
define("LANGAGENDA36","Nächstes Jahr");
define("LANGAGENDA37","Ein Datum auswählen");
define("LANGAGENDA38","Verschieben");
define("LANGAGENDA39","Heute");
define("LANGAGENDA40","Über den Kalender");
define("LANGAGENDA41","%s zuerst anzeigen");
define("LANGAGENDA42","Schließen");
define("LANGAGENDA43","Klicken oder ziehen, um den Wert zu ändern");
define("LANGAGENDA44","Unbekannter Benutzer");
define("LANGAGENDA45","Ihre Sitzung ist abgelaufen!");
define("LANGAGENDA46","Dieser Login ist bereits vergeben");
define("LANGAGENDA47","Altes Passwort falsch");
define("LANGAGENDA48","Bitte identifizieren Sie sich, um Phenix zu verwenden");
define("LANGAGENDA49","Die Verbindung zum SQL-Server ist fehlgeschlagen");
define("LANGAGENDA50","Profil geändert");
define("LANGAGENDA51","Termin gespeichert");
define("LANGAGENDA52","Termin aktualisiert");
define("LANGAGENDA53","Termin gelöscht");
define("LANGAGENDA54","Vorkommen des Termins gelöscht");
define("LANGAGENDA55","Geburtstag gespeichert");
define("LANGAGENDA56","Geburtstag aktualisiert");
define("LANGAGENDA57","Geburtstag gelöscht");
define("LANGAGENDA58","Konto erstellt, Sie können sich anmelden");
define("LANGAGENDA59","Die Speicherung ist fehlgeschlagen");
define("LANGAGENDA60","Alle Felder");
define("LANGAGENDA61","Firma");
define("LANGAGENDA62","Name + Vorname");
define("LANGAGENDA63","Adresse");
define("LANGAGENDA64","Telefonnummer");
define("LANGAGENDA65","E-Mail-Adresse");
define("LANGAGENDA66","Kommentare");
define("LANGAGENDA67","Suche starten");
define("LANGAGENDA68","Firma");
define("LANGAGENDA69","Name");
define("LANGAGENDA70","Vorname");
define("LANGAGENDA71","Adresse");
define("LANGAGENDA72","Stadt");
define("LANGAGENDA73","Land");
define("LANGAGENDA74","Tel. Privat");
define("LANGAGENDA75","Tel. Arbeit");
define("LANGAGENDA76","Tel.&nbsp;Mobil");
define("LANGAGENDA77","Fax");
define("LANGAGENDA78","E-Mail");
define("LANGAGENDA79","E-Mail Arbeit");
define("LANGAGENDA80","Notiz / Diverses");
define("LANGAGENDA81","Gruppe");
define("LANGAGENDA82","Teilen");
define("LANGAGENDA83","PLZ");
define("LANGAGENDA84","Geburtsdatum");
define("LANGAGENDA85","Wiederholen");
define("LANGAGENDA86","Importieren");
define("LANGAGENDA87","Import abgeschlossen");
define("LANGAGENDA88","Kontakt(e) hinzugefügt");
define("LANGAGENDA89","Kein Kontakt verfügbar!");
define("LANGAGENDA90","<LI>In Outlook, wählen Sie <I>Datei</I>-&gt;<I>Exportieren</I>-&gt;<I>Anderes Adressbuch...</I></LI>");
define("LANGAGENDA91","<LI>Wählen Sie <I>Textdatei (Kommagetrennte Werte)</I> und dann <I>Exportieren</I></LI>");
define("LANGAGENDA92","<LI>Wählen Sie den Speicherort der Datei und dann <I>Weiter</I></LI>");
define("LANGAGENDA93","<LI>In der Liste der zu exportierenden Felder, wählen Sie:<BR>");
define("LANGAGENDA94","<I>Vorname, Nachname, E-Mail-Adresse, Straße (privat), Stadt (privat), Postleitzahl (privat), Land/Region (privat), Telefon privat, Mobiltelefon, Telefon geschäftlich, Fax geschäftlich, Firma</I> und klicken Sie dann auf <I>Fertig stellen</I></LI>");
define("LANGAGENDA95","<LI>Rufen Sie die so erstellte Datei im folgenden Formular ab und klicken Sie auf <I>Importieren</I></LI>");
define("LANGAGENDA96","Bitte geben Sie eine Firma für die Suche ein");
define("LANGAGENDA97","Bitte geben Sie einen Namen oder Vornamen für die Suche ein");
define("LANGAGENDA98","Bitte geben Sie eine Adresse für die Suche ein");
define("LANGAGENDA99","Bitte geben Sie eine Telefonnummer für die Suche ein");
define("LANGAGENDA100","Bitte geben Sie eine E-Mail-Adresse für die Suche ein");
define("LANGAGENDA101","Bitte geben Sie einen Teil des Kommentars für die Suche ein");
define("LANGAGENDA102","Bitte geben Sie mindestens ein Kriterium für die Suche ein");
define("LANGAGENDA103","Sind Sie sicher, dass Sie diesen Kontakt löschen möchten?");
define("LANGAGENDA104","Jahr");
define("LANGAGENDA105","Kein Vater"); // "Pas de père" - Kontext unklar, könnte auch "Kein übergeordnetes Element" bedeuten
define("LANGAGENDA106","Liste der Personen<BR>denen Sie<BR>einen Termin zuweisen können");
define("LANGAGENDA107","Mögliche Person(en)");
define("LANGAGENDA108","Ausgewählte Person(en)");
define("LANGAGENDA109","Anzeigegenauigkeit");
define("LANGAGENDA110","30-Minuten-Block");
define("LANGAGENDA111","15-Minuten-Block");
define("LANGAGENDA112","Startzeit");
define("LANGAGENDA113","Endzeit");
define("LANGAGENDA114","Besetzt");
define("LANGAGENDA115","Teilweise");
define("LANGAGENDA116","Frei");
define("LANGAGENDA117","Einen Termin erstellen zwischen ");
define("LANGAGENDA118","Details pro Benutzer für diesen Tag");
define("LANGAGENDA119","Anzeigen");
define("LANGAGENDA120","Bitte wählen Sie eine Person aus");
define("LANGAGENDA121","Bitte wählen Sie eine Endzeit, die nach der Startzeit liegt");
define("LANGAGENDA122","Woche vom ");
define("LANGAGENDA123","bis");
define("LANGAGENDA124","Nächste Woche");
define("LANGAGENDA125","Entfernen");
define("LANGAGENDA126","Verfügbarkeiten Ihrer Kontakte für den ");
define("LANGAGENDA127","Hinzufügen");
define("LANGAGENDA128","Außerhalb des Profils");
define("LANGAGENDA129","Bitte wählen Sie eine Endzeit, die nach der Startzeit liegt");
define("LANGAGENDA130","Anzeigegenauigkeit");
define("LANGAGENDA131","Bitte geben Sie einen Namen ein");
define("LANGAGENDA132","Bitte geben Sie eine URL ein");
define("LANGAGENDA133","Einen Favoriten hinzufügen");
define("LANGAGENDA134","Druck im Querformat empfohlen");
define("LANGAGENDA135","Vorherige Woche ");
define("LANGAGENDA136","Woche ");
define("LANGAGENDA137","vom");
define("LANGAGENDA138","Geburtstag");
define("LANGAGENDA139","Standarderinnerung beim Erstellen eines Termins");
define("LANGAGENDA140","Keine Erinnerung");
define("LANGAGENDA141","Erinnerung");
define("LANGAGENDA142","Kopie per E-Mail");
define("LANGAGENDA143","Minute(n)");
define("LANGAGENDA144","Stunde(n)");
define("LANGAGENDA145","Tag(e)");
define("LANGAGENDA146","Typischer Tag");
define("LANGAGENDA147","Endet um");
define("LANGAGENDA148","Telefon VF"); // VF könnte eine Abkürzung sein, Kontext unklar
define("LANGAGENDA149","Schnittstelle");
define("LANGAGENDA150","Standardplanung");
define("LANGAGENDA151","Täglich");
define("LANGAGENDA152","Wöchentlich");
define("LANGAGENDA153","Monatlich");
define("LANGAGENDA154","30 Minuten");
define("LANGAGENDA155","15 Minuten");
define("LANGAGENDA156","45 Minuten");
define("LANGAGENDA157","1 Stunde");
define("LANGAGENDA158","Automatische Auswahl der Endzeit eines Termins");
define("LANGAGENDA159","Planung teilen<BR>zur Einsicht");
define("LANGAGENDA160","Personen, die berechtigt sind, meine Planung einzusehen");
define("LANGAGENDA161","Nicht geteilt");
define("LANGAGENDA162","Nach Wahl");
define("LANGAGENDA163","Jeder");
define("LANGAGENDA164","Planung teilen<BR>zur Bearbeitung");
define("LANGAGENDA165","Person(en), die mir einen Termin zuweisen können");
define("LANGAGENDA166","Mich per E-Mail informieren, wenn mir ein Termin zugewiesen wird");
define("LANGAGENDA167","Diesen von mir erstellten Termin löschen");
define("LANGAGENDA168","Diesen mir zugewiesenen Termin löschen");
define("LANGAGENDA169","Diesen mir zugewiesenen Termin übernehmen");
define("LANGAGENDA170","Ganztägig");
define("LANGAGENDA171","Bezeichnung wählen");
define("LANGAGENDA172","Neue Bezeichnung");
define("LANGAGENDA173","Titel");
define("LANGAGENDA174","Durchschnittliche Dauer");
define("LANGAGENDA175","Farbe");
define("LANGAGENDA176","Aussehen des Termins");
define("LANGAGENDA177","Diese Bezeichnung löschen?");
define("LANGAGENDA178","Ein Memo speichern");
define("LANGAGENDA179","Bitte geben Sie einen Titel ein");
define("LANGAGENDA180","Titel");
define("LANGAGENDA181","Inhalt");
define("LANGAGENDA182","Sind Sie sicher, dass Sie dieses Memo löschen möchten?");
define("LANGAGENDA183","Einen Termin speichern");
define("LANGAGENDA184","Der Termin, den Sie ändern möchten, gehört zu einer wiederkehrenden Serie");
define("LANGAGENDA185","Möchten Sie die gesamte Serie oder nur dieses Vorkommen ändern?");
define("LANGAGENDA186","Gesamte Serie");
define("LANGAGENDA187","Nur dieses Vorkommen");
define("LANGAGENDA188","Ganztägiger Termin");
define("LANGAGENDA189","Kalender anzeigen");
define("LANGAGENDA190","Ganztägig");
define("LANGAGENDA191","Beginnt um");
define("LANGAGENDA192","Betroffene<BR>Person");
define("LANGAGENDA193","Aussehen des Termins");
define("LANGAGENDA194","Öffentlicher Termin");
define("LANGAGENDA195","detaillierter Termin in der geteilten Planung");
define("LANGAGENDA196","Vermerk \"Besetzt\" in der geteilten Planung");
define("LANGAGENDA197","Privater Termin");
define("LANGAGENDA198","Besetzt");
define("LANGAGENDA199","als <B>nicht verfügbar</B> im Verfügbarkeitsmodul betrachten");
define("LANGAGENDA200","Frei");
define("LANGAGENDA201","als <B>frei</B> im Verfügbarkeitsmodul betrachten");
define("LANGAGENDA202","Farbe");
define("LANGAGENDA203","Teilen");
define("LANGAGENDA204","Verfügbarkeit");
define("LANGAGENDA205","Erinnerung");
define("LANGAGENDA206","Keine Erinnerung");
define("LANGAGENDA207","Kopie per E-Mail");
define("LANGAGENDA208","im Voraus");
define("LANGAGENDA209","Periodizität");
define("LANGAGENDA210","Keine");
define("LANGAGENDA211","Täglich");
define("LANGAGENDA212","Wöchentlich");
define("LANGAGENDA213","Monatlich");
define("LANGAGENDA214","Jährlich");
define("LANGAGENDA215","Alle ");
define("LANGAGENDA215bis","Tage");
define("LANGAGENDA216","Alle Werktage (Montag bis Freitag)");
define("LANGAGENDA217","Alle Tage meiner typischen Woche");
define("LANGAGENDA218","Die eingegebenen oder geänderten Informationen werden nicht gespeichert\\nMöchten Sie wirklich fortfahren?");
define("LANGAGENDA219","Profil");
define("LANGAGENDA220","Alle ");
define("LANGAGENDA221","Alle ");
define("LANGAGENDA221bis","Wochen");
define("LANGAGENDA222","jedes Monats");
define("LANGAGENDA223","erster");
define("LANGAGENDA224","zweiter");
define("LANGAGENDA225","dritter");
define("LANGAGENDA226","vierter");
define("LANGAGENDA227","letzter");
define("LANGAGENDA228","des Monats");
define("LANGAGENDA229","Der ");
define("LANGAGENDA230","Enddatum festlegen");
define("LANGAGENDA231","Endet nach");
define("LANGAGENDA232","Endet am");
define("LANGAGENDA233","Vorkommen");
define("LANGAGENDA234","Bitte geben Sie eine Bezeichnung ein");
define("LANGAGENDA235","Bitte geben Sie ein Datum ein");
define("LANGAGENDA236","Bitte wählen Sie eine Endzeit aus,\\ndie nach der Startzeit liegt");
define("LANGAGENDA237","Bitte wählen Sie eine Person aus");
define("LANGAGENDA238","Bitte geben Sie eine Anzahl von Tagen ein,\\ndie größer oder gleich 1 ist");
define("LANGAGENDA239","Bitte geben Sie eine Anzahl von Vorkommen ein,\\ndie größer oder gleich 1 ist");
define("LANGAGENDA240","Wiederholung");
define("LANGAGENDA241","Bitte geben Sie zuerst Ihren Namen und Vornamen ein");
define("LANGAGENDA242","Bitte geben Sie Ihren Vornamen ein");
define("LANGAGENDA243","Sie müssen Ihren Login eingeben");
define("LANGAGENDA244","Bitte geben Sie Ihr altes Passwort ein");
define("LANGAGENDA245","Unterschiedliche Passwörter");
define("LANGAGENDA246","Ein Passwort ist erforderlich");
define("LANGAGENDA247","Bitte wählen Sie eine Endzeit aus,\\ndie nach der Startzeit liegt");
define("LANGAGENDA248","Dieses Vorkommen löschen");
define("LANGAGENDA249","Wiederkehrender Termin");
define("LANGAGENDA250","Diesen von mir erstellten Termin löschen");
define("LANGAGENDA251","Diesen mir zugewiesenen Termin übernehmen");
define("LANGAGENDA252","Filtern");
define("LANGAGENDA253","Diese Planung drucken");
define("LANGAGENDA254","Druck im Querformat empfohlen");
define("LANGAGENDA255","Termin erstellt von ");
define("LANGAGENDA256","Status ändern");
define("LANGAGENDA257","Dieses Vorkommen löschen");
define("LANGAGENDA258","Diesen von mir erstellten Termin löschen");
define("LANGAGENDA259","Diesen mir zugewiesenen Termin löschen");
define("LANGAGENDA260","ein Termin");
define("LANGAGENDA261","ein Geburtstag");
define("LANGAGENDA262","ein Kontakt");
define("LANGAGENDA263","An den unten ausgewählten Benutzer");
define("LANGAGENDA264","Einen Termin hinzufügen");
define("LANGAGENDA265","Suche");
define("LANGAGENDA266","Verfügbarkeiten");
define("LANGAGENDA267","Kontakte");
define("LANGAGENDA268","Memo");
define("LANGAGENDA269","Bezeichnungen");
define("LANGAGENDA270","Favoriten");
define("LANGAGENDA271","Profil");
define("LANGAGENDA272","Exporterstellung fehlgeschlagen");
define("LANGAGENDA273","Agenda von ");
// FIN AGENDA

define("LANGL","M");  // L de lundi -> M für Montag
define("LANGM","D");  // M de mardi -> D für Dienstag
define("LANGME","M"); // M de mercredi -> M für Mittwoch
define("LANGJ","D");  // J de jeudi -> D für Donnerstag
define("LANGV","F");  // V de vendredi -> F für Freitag
define("LANGS","S");  // S de samedi -> S für Samstag
define("LANGD","S");  // D de dimanche -> S für Sonntag

define("LANGL1","Mo"); // Jours sur 3 lettres
define("LANGM1","Di");	// Jours sur 3 lettres
define("LANGME1","Mi"); // Jours sur 3 lettres
define("LANGJ1","Do");	// Jours sur 3 lettres
define("LANGV1","Fr");	// Jours sur 3 lettres
define("LANGS1","Sa");	// Jours sur 3 lettres
define("LANGD1","So");	// Jours sur 3 lettres

define("LANGMOIS21","Jan");			// mois abregé
define("LANGMOIS22","Feb"); 		// mois abregé
define("LANGMOIS23","Mär");			// mois abregé
define("LANGMOIS24","Apr");			// mois abregé
define("LANGMOIS25","Mai");			// mois abregé
define("LANGMOIS26","Jun");			// mois abregé
define("LANGMOIS27","Jul");			// mois abregé
define("LANGMOIS28","Aug");		// mois abregé
define("LANGMOIS29","Sep");			// mois abregé
define("LANGMOIS210","Okt");		// mois abregé
define("LANGMOIS211","Nov"); 		// mois abregé
define("LANGMOIS212","Dez"); 		// mois abregé


define("LANGPROFP22","Diese ".INTITULEENSEIGNANT." ist bereits als Klassenlehrer zugewiesen. \\n\\n Das TRIADE-Team");


define("LANGSTAGE23","Name der Tätigkeit");
define("LANGSTAGE24","Ein neues Unternehmen speichern");
define("LANGSTAGE25","Der Name dieses Unternehmens ist bereits gespeichert");
define("LANGSTAGE26","Name des Unternehmens");
define("LANGSTAGE27","Kontakt");
define("LANGSTAGE28","Adresse");
define("LANGSTAGE29","Postleitzahl");
define("LANGSTAGE30","Stadt");
define("LANGSTAGE31","Tätigkeitsbereich");
define("LANGSTAGE32","Tätigkeit hinzufügen");
define("LANGSTAGE33","Haupttätigkeit");
define("LANGSTAGE34","Telefon");
define("LANGSTAGE35","Fax");
define("LANGSTAGE36","E-Mail");
define("LANGSTAGE37","Informationen");
define("LANGSTAGE38","Unternehmen einsehen");
define("LANGSTAGE39","Firma");
define("LANGSTAGE40","Haupttätigkeit");
define("LANGSTAGE41","Andere Suche");
define("LANGSTAGE42","Tel. / Fax");
define("LANGSTAGE43","Kein Unternehmen mit diesem Namen");
define("LANGSTAGE44","Planung der Praktika");
define("LANGSTAGE45","Anfangsdatum des Praktikums");
define("LANGSTAGE46","Enddatum des Praktikums");
define("LANGSTAGE47","Praktikum speichern");
define("LANGSTAGE48","Nummer des Praktikums");
define("LANGSTAGE49","Änderung der Praktikumsdaten");
define("LANGSTAGE50","Praktikum");
define("LANGSTAGE51","Datum des Praktikums");
define("LANGSTAGE52","Eingabefehler");
define("LANGSTAGE53","Praktikum aktualisiert");
define("LANGSTAGE54","Das Praktikum vom ");
define("LANGSTAGE55","für die ".INTITULECLASSE." von");
define("LANGSTAGE56","ist gespeichert");
define("LANGSTAGE57","Praktikumsdatum gelöscht \\n\\n Das TRIADE-Team");
define("LANGSTAGE58","Unternehmen gespeichert \\n\\n Das TRIADE-Team");
define("LANGSTAGE59","Unternehmensänderung");
define("LANGSTAGE60","Unternehmen nach Tätigkeit");
define("LANGSTAGE61","Suche nach Unternehmen");
define("LANGSTAGE62","Info");
define("LANGSTAGE63","Vollständige Liste");
define("LANGSTAGE64","Anzeige der Praktikumsdaten");
define("LANGSTAGE65","Unternehmenslöschung");
define("LANGSTAGE66","Unternehmen gelöscht \\n\\n Das TRIADE-Team");
define("LANGSTAGE67","Unternehmen nach Tätigkeit einsehen");
define("LANGSTAGE68","Kein Unternehmen mit diesem Namen");
define("LANGSTAGE69","Anzeige eines ".INTITULEELEVE." bei einem Praktikum");
define("LANGSTAGE70","Praktikum Nummer drucken");
define("LANGSTAGE71","Anzeige eines ".INTITULEELEVE." bei Praktika");
define("LANGSTAGE72","&nbsp;Datum&nbsp;des&nbsp;Praktikums&nbsp;");
define("LANGSTAGE73","Zurück");
define("LANGSTAGE74","Unternehmen");
define("LANGSTAGE75","Zuweisung eines ".INTITULEELEVE." zu einem Praktikum");
define("LANGSTAGE76","Ort des Praktikums");
define("LANGSTAGE77","Verantwortlicher");
define("LANGSTAGE78",ucfirst(INTITULEENSEIGNANT)." Besucher");
define("LANGSTAGE79","Untergebracht");
define("LANGSTAGE80","Verpflegt");
define("LANGSTAGE81","Durchlauf in n Abteilungen");
define("LANGSTAGE82","Grund für Abteilungswechsel");
define("LANGSTAGE83","Zusätzliche Informationen");
define("LANGSTAGE84","Erstellung gespeichert \\n \\n Das TRIADE-Team");
define("LANGSTAGE85","Datum des Besuchs");
define("LANGSTAGE86","Änderung eines ".INTITULEELEVE." bei einem Praktikum");
define("LANGSTAGE87","Informationen gespeichert");
define("LANGSTAGE88","Löschung eines ".INTITULEELEVE." bei einem Praktikum");


define("LANGRESA62","Bezeichnung");
define("LANGRESA63","Ablehnen");
define("LANGRESA64","Eine Anfrage hinzufügen");
define("LANGRESA65","&nbsp;Von&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;bis");
define("LANGRESA66","Reserviert");
define("LANGRESA66bis","von");
define("LANGRESA67","Nicht bestätigt");
define("LANGRESA68","Bestätigt");
define("LANGRESA69","Speicherung abgeschlossen");
define("LANGRESA70","Reservierung für den ");


define("LANGNOTEUSA1","Konfiguration der Notenzuweisungen für den USA-Modus");
define("LANGNOTEUSA2","Dieses Modul ermöglicht es Ihnen, die Buchstaben entsprechend dem Prozentsatz zu positionieren, der jeder Note (Buchstabe) zugewiesen werden soll.");
define("LANGNOTEUSA3","Beispiel: von 95 bis 100 --> A+ , von 87 bis 94 --> A, etc...");
define("LANGNOTEUSA4","Von");
define("LANGNOTEUSA4bis","bis");
define("LANGNOTEUSA4ter","entspricht");
define("LANGNOTEUSA5","Zwischen der Note");
define("LANGNOTEUSA5bis","und der Note");
define("LANGNOTEUSA5ter","entspricht dies");


define("LANGABS56","Liste der unentschuldigten Abwesenheiten");
define("LANGABS57","Aktualisierung für diese Liste von ".INTITULEELEVE." durchgeführt");


define("LANGSANC1","Sanktion erstellt -- Das TRIADE-Team");
define("LANGSANC2","Kategorie nicht gelöscht. Diese Kategorie ist bereits einer Sanktion oder einem ".INTITULEELEVE." zugewiesen -- TRIADE-Team");
define("LANGSANC3","Disziplinkonfiguration");
define("LANGSANC4","Speicherung der Kategorien.");
define("LANGSANC5","Bezeichnung der Kategorie");
define("LANGSANC6","Speicherung der Namen der Sanktionen pro Kategorie.");
define("LANGSANC7","Bezeichnung der Sanktion");
define("LANGSANC8","Konfiguration Nachsitzen");
define("LANGSANC9","Benachrichtigung, wenn der ".INTITULEELEVE." die zulässige Grenze erreicht hat.");
define("LANGSANC10","Für die Kategorie");
define("LANGSANC11","Benachrichtigung nach");
define("LANGSANC12","Anzahl Male");
define("LANGSANC13","Erstellt von");
define("LANGSANC14","Eingabedatum");

// Modification de ces 2 phrases à traduire
// define("LANGPARAM1","<font class=T1>Composez votre texte pour le contenu du message de l'absence pour l'envoi du courrier aux parents d'".INTITULEELEVE.". Pour une prise en compte du nom et du prénom de l'".INTITULEELEVE." automatiquement dans chaque document, veuillez présiser la chaîne <b>NomEleve</b> et <b>PrenomEleve</b> à l'emplacement désiré. De même possibilité d'indiquer la classe avec le mot clef <b>ClasseEleve</b>, ou la date de l'absence ABSDEBUT ou ABSFIN ainsi que la durée ABSDUREE </font><br><br>");
// define("LANGPARAM2","<font class=T1>Composez votre texte pour le contenu du message de retard pour l'envoi du courrier aux parents. Pour une prise en compte du nom et du prénom de l'".INTITULEELEVE." automatiquement dans chaque document, veuillez présiser la chaîne <b>NomEleve</b> et <b>PrenomEleve</b> à l'emplacement désiré. De même possibilité d'indiquer la classe avec le mot clef <b>ClasseEleve</b>, ou la date du retard RTDDATE , l'heure RTDHEURE ainsi que le durée RTDDUREE </font><br><br>");
define("LANGPARAM1","<font class=T1>Verfassen Sie Ihren Text für den Inhalt der Abwesenheitsnachricht für den Versand an die Eltern des ".INTITULEELEVE.". Um den Namen und Vornamen des ".INTITULEELEVE." automatisch in jedes Dokument zu übernehmen, geben Sie bitte die Zeichenfolge <b>NomEleve</b> und <b>PrenomEleve</b> an der gewünschten Stelle ein. Ebenso besteht die Möglichkeit, die Klasse mit dem Schlüsselwort <b>ClasseEleve</b> anzugeben, oder das Datum der Abwesenheit ABSDEBUT oder ABSFIN sowie die Dauer ABSDUREE </font><br><br>");
define("LANGPARAM2","<font class=T1>Verfassen Sie Ihren Text für den Inhalt der Verspätungsnachricht für den Versand an die Eltern. Um den Namen und Vornamen des ".INTITULEELEVE." automatisch in jedes Dokument zu übernehmen, geben Sie bitte die Zeichenfolge <b>NomEleve</b> und <b>PrenomEleve</b> an der gewünschten Stelle ein. Ebenso besteht die Möglichkeit, die Klasse mit dem Schlüsselwort <b>ClasseEleve</b> anzugeben, oder das Datum der Verspätung RTDDATE , die Uhrzeit RTDHEURE sowie die Dauer RTDDUREE </font><br><br>");


define("LANGMODIF4","Ein Konto ändern");
define("LANGMODIF5","Anmeldeinformationen");
define("LANGMODIF6","Passfoto");
define("LANGMODIF7","Kontaktdaten des Kontos");
define("LANGMODIF8","Adresse");
define("LANGMODIF9","Postleitzahl");
define("LANGMODIF10","Gemeinde");
define("LANGMODIF11","Tel.");
define("LANGMODIF12","E-Mail");
define("LANGMODIF13","Konto ändern");
define("LANGMODIF14","Konto geändert -- TRIADE-Team");
define("LANGMODIF15","Das Passwort von ");
define("LANGMODIF15bis"," wurde geändert.");
define("LANGMODIF16","Passwortänderung");
define("LANGMODIF17","Unmöglich, Foto hat nicht konforme Größe");
define("LANGMODIF18","Dieses Foto aktualisieren");
define("LANGMODIF19","Foto hinzufügen");
define("LANGMODIF20","Foto ändern");

define("LANGGRP25bis","Gruppenverwaltung");
define("LANGGRP26","Liste der Gruppen");
define("LANGGRP27",INTITULEELEVE." einer Gruppe hinzufügen");
define("LANGGRP28",INTITULEELEVE." aus einer Gruppe entfernen");
define("LANGGRP29","Name der Gruppe");
define("LANGGRP30","Betroffene Klasse(n)");
define("LANGGRP31","Liste ändern");
define("LANGGRP32",INTITULEELEVE." der Gruppe hinzufügen");
define("LANGGRP33",INTITULEELEVE." dieser Gruppe hinzufügen");
define("LANGGRP34",ucfirst(TextNoAccentLicence2(INTITULEELEVE))." in ".INTITULECLASSE." von ");
define("LANGGRP35",ucfirst(TextNoAccentLicence2(INTITULEELEVE))." in der Gruppe");
define("LANGGRP36","Gruppe bestätigen");
define("LANGGRP37","Gruppe geändert -- TRIADE-Team ");
define("LANGGRP38","Liste der ".INTITULEELEVE." der Gruppe ");
define("LANGGRP39","Kein ".INTITULEELEVE." in dieser Gruppe");

define("LANGCARNET1","Notenheft");
define("LANGCARNET2","Klasse des ".INTITULEELEVE."s");
define("LANGCARNET3","Klicken Sie auf den <b>Namen</b> des ".INTITULEELEVE."s");

define("LANGPASSG1","Das Passwort muss mindestens <b>8 Zeichen</b> lang sein,<br /> <b>alphanumerisch</b> sein und <b>Groß- und Kleinbuchstaben</b> verwenden.");
define("LANGPASSG2","Das Passwort ist nicht korrekt. \\n Das Passwort muss enthalten: \\n\\n -> Mindestens 8 Zeichen, \\n -> alphanumerisch, \\n -> Groß- und Kleinbuchstaben \\n\\n Das TRIADE-Team");
define("LANGPASSG3","Erstellung fehlgeschlagen");


define("LANGDISC38","Sanktion hinzufügen");
define("LANGDISC39","Disziplinverwaltung");
define("LANGDISC40","Nachsitzen nicht durchgeführt.");
define("LANGDISC41","Planung Nachsitzen.");
define("LANGDISC42","Nachsitzen keinem Schüler zugewiesen.");
define("LANGDISC43","Konfiguration.");
define("LANGDISC44","Nachsitzen und Sanktionen löschen");
define("LANGDISC45","Nachsitzen und Sanktionen löschen"); // Doppelt?
define("LANGDISC46","Liste der Abwesenheiten und Verspätungen einer ".INTITULECLASSE);
define("LANGDISC47","Geben Sie den Beginn des Zeitraums an");
define("LANGDISC48","Geben Sie das Ende des Zeitraums an");
define("LANGDISC49","Geben Sie den Bereich an");
define("LANGDISC50","<br><ul>Löschung der Nachsitzen und Sanktionen <br>abhängig vom Datumsintervall.</ul>");
define("LANGDISC51","Alle ".INTITULECLASSE."n");
define("LANGDISC52","Nachsitzen und Sanktionen gelöscht");
define("LANGDISC53","Fehler! Nachsitzen und Sanktionen nicht gelöscht");

define("LANGIMP53","ASCII-Datei über SQL ");


// autre new

define("LANGSTAGE31bis","2. Tätigkeitsbereich");
define("LANGSTAGE31ter","3. Tätigkeitsbereich");
define("LANGMEDIC1","Medizinische Akte eines ".INTITULEELEVE."s");
define("LANGMEDIC2","Suche senden");
define("LANGMEDIC3","Information / Änderung");


define("LANGDISC54","Disziplinarmaßnahmen eines Schülers anzeigen");
define("LANGDISC55","Eine Sanktion löschen");
define("LANGDISC56","Sanktion löschen");

define("LANGBASE6bis","Gesamtzahl ".INTITULEELEVE." in der Datei ");

define("LANGMODIF21","Das Passwort muss haben: \\n\\n - Mindestens 8 Zeichen \\n - Alphanumerisch \\n - GROSS- und Kleinbuchstaben.\\n\\n TRIADE-Team");

define("LANGMODIF22","Passwort: 8 Zeichen - Alphanumerisch - Groß- und Kleinbuchstaben");
define("LANGPASS1bis","Passwort bestätigen");

define("LANGMODIF23","Sie können Ihr Passwort für Ihr TRIADE-Konto ändern");
define("LANGMODIF24","Das Konto ");
define("LANGMODIF24bis","wird gerade validiert..");
define("LANGMODIF24ter","ist jetzt betriebsbereit");
define("LANGMODIF25","Passwort nicht identisch. \\n\\n TRIADE-Team");

define("LANGABS58","Anzeige / Löschung Abwesenheit - Verspätung");
define("LANGABS59","Vollständige Anzeige der Verspätungen");
define("LANGABS60","Während");
define("LANGABS61","Anzeige / Änderung einer Abwesenheit - Verspätung");
define("LANGABS62","<b>Vollständige</b> Anzeige der Verspätungen und Abwesenheiten");
define("LANGABS63","Eingabe am");
define("LANGABS64","Anzeige der <b>5</b> letzten Verspätungen und Abwesenheiten");
define("LANGABS65","Vollständige Anzeige der Abwesenheiten");
define("LANGABS66","Aktualisierung für diese Liste von ".INTITULEELEVE." durchgeführt");
define("LANGABS6bis","Liste der unentschuldigten Verspätungen");
define("LANGABS4bis","Abwesenheiten oder Verspätungen auflisten");
define("LANGABS67","<font class=T2>Kein Schüler in dieser ".INTITULECLASSE."</font>");
define("LANGABS68","Abw. / Versp. einer ".INTITULECLASSE);
define("LANGABS69","Kumulierte Abw./Versp. der ".INTITULEELEVE);
define("LANGABS70","Konfiguration der Gründe");
define("LANGABS71","Anzahl Abwesenheiten / Kumuliert");
define("LANGABS72","Anzahl Verspätungen / Kumuliert");
define("LANGABS73","Abwesenheiten - Verspätungen - der ".INTITULECLASSE);
define("LANGABS74","Aktualisierung durchführen");
define("LANGABS75","Kein Abwesender oder Verspäteter");
define("LANGABS76","erfasst um ");

define("LANGDEPART3","Aufgrund eines technischen Problems,");
define("LANGDEPART4","ist der Zugriff auf den Server nicht verfügbar. Das TRIADE-Team arbeitet derzeit am Server.");

define("LANGBASE3_2","Hier ist die Liste der importierbaren Dateien.");
define("LANGbasededoni21_2","Möchten Sie fortfahren? \\n\\n Das TRIADE-Team");
define("LANGbasededon21","Das Senden der Datei kann je nach Anzahl der Elemente <b>2 bis 4 Minuten</b> dauern.");
define("LANGbasededon31_2","Geben Sie die Fächer an, die Sie importieren möchten.");
define("LANGBASE10_2","Geben Sie die hinzuzufügenden ".INTITULEENSEIGNANT." an.");

define("LANGBASE16_2"," Die Spalten sind wie folgt dargestellt: <b>Loginname; Vorname Login; Passwort im Klartext</b>");
define("LANGIMP25_2","Name der Einrichtung");
// ----------------------------- //
define("LANGABS77","Gemeldet am");
define("LANGSTAGE89","Praktikumsvertrag erstellen");
define("LANGSTAGE90","Praktikumsverträge ausgeben");
define("LANGSTAFE91","Liste der ".INTITULEELEVE.", die sich derzeit im Unternehmen befinden"); // STAFE91 -> STAGE91 ?
define("LANGSTAGE92","Liste der ".INTITULEELEVE.", die sich derzeit im Unternehmen befinden");
define("LANGPASSG4","Das Passwort muss mindestens <b>8 Zeichen</b> lang sein <br /><b>alphanumerisch</b>.");
define("LANGPASSG5","Das Passwort muss mindestens <b>4 Zeichen</b> lang sein.");
define("LANGPASSG6","Das Passwort ist nicht korrekt. \\n Das Passwort muss enthalten: \\n\\n -> Mindestens 8 Zeichen, \\n -> alphanumerisch \\n\\n Das TRIADE-Team");
define("LANGPASSG7","Das Passwort ist nicht korrekt. \\n Das Passwort muss enthalten: \\n\\n -> Mindestens 4 Zeichen. \\n\\n Das TRIADE-Team");

define("LANGMODIF22_1","Passwort: 4 Zeichen");
define("LANGMODIF22_2","Passwort: 8 Zeichen - Alphanumerisch ");
define("LANGMODIF22_3","Passwort: 8 Zeichen - Alphanumerisch - Groß- und Kleinbuchstaben");
define("LANGDEPART2","<font color=red  class=T2>ACHTUNG, um TRIADE zu verwenden, muss die PHP-Variable '<strong>register_globals</strong>' auf <u>Off</u> stehen.</font><br />");


define("LANGacce15","Aufgabe abzugeben bis zum ");
define("LANGacce16","Aufgabe heute abzugeben!");
define("LANGacce17","Hinzufügen einer Disziplinarmaßnahme");

define("LANGBASE41","Alle ".INTITULEELEVE." vor dem Import löschen");
define("LANGBASE7bis",ucfirst(TextNoAccentLicence2(INTITULEELEVE))." bereits zugewiesen");
define("LANGBASE8bis","für die ".INTITULEELEVE." <u>zugewiesen</u> und <u>ohne ".INTITULECLASSE."</u>");

define("LANGPER21bis","Sprache&nbsp;/&nbsp;Option");

define("LANGASS6ter",ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGASS41","Speicher");
define("LANGASS42","Parametrisierung");

define("LANGIMP46bis","Passwort");

define("LANGIMP54","Hausnr.");
define("LANGIMP55","Adresse");
define("LANGIMP56","Postleitzahl");
define("LANGIMP57","Telefon");
define("LANGIMP58","E-Mail");
define("LANGIMP59","Gemeinde");

define("LANGBULL1pp","Druck Quartals- oder Halbjahreszeugnis");
define("LANGBT43pp","Tabelle drucken");


define("LANGMESS38","Nachricht gelesen.");
define("LANGMESS39","Nachricht nicht gelesen.");


define("LANGDISC57","Grund&nbsp;/&nbsp;Sanktion");

define("CUMUL01","Kumulierte Abwesenheiten und Verspätungen einer ".INTITULECLASSE." pro ".INTITULEELEVE);
define("CUMUL02","Kumulierte Sanktionen einer ".INTITULECLASSE." pro ".INTITULEELEVE);
define("CUMUL03","Kumulierte Sanktionen eines ".INTITULEELEVE."s");
define("LANGPROJ18bis","Stunde(n)");
define("LANGCREAT1","Konto bereits vorhanden.");
define("ERREUR1","Internet-Netzwerk für dieses Modul nicht verfügbar.");
define("ERREUR2","Konsultieren Sie das Konfigurationsmodul, um das Netzwerk zu aktivieren.");


define("PASSG8","Passwortänderung");
define("PASSG9","Das Passwort des ".INTITULEELEVE."s ");
define("PASSG9bis"," wurde geändert.");


define("LANGPARAM34","Website der Einrichtung");
define("LANGLOGO3bis","Das Logo <b>muss im jpg-Format sein</b>");


define("LANGMAT1","Fach speichern");
define("LANGMAT2","Liste / Änderung eines Fachs");
define("LANGMAT3","Fach löschen");
define("LANGMAT4","Änderung bestätigen");
define("LANGMAT5","Fach geändert");
define("LANGMAT6","Fach bereits zugewiesen");
define("LANGCLAS1","Liste / ".INTITULECLASSE." ändern");
define("LANGCLAS2","Klasse geändert");
define("LANGCLAS3","Klasse bereits zugewiesen");

define("LANGDEVOIR1","für die Gruppe");
define("LANGDEVOIR2","für die ".INTITULECLASSE);
define("LANGDEVOIR3","Eine Hausaufgabe speichern");
define("LANGCIRCU111","<font face=Verdana size=1><B><font color=red>D</font></B>okument im Format: <b> doc</b>, <b>pdf</b>, <b>txt</b>.</FONT>");

define("LANGAFF7","Modul zur Löschung der Zuweisung von ".INTITULECLASSE."n.");
define("LANGAFF8","ACHTUNG, dieses Modul ist bei der Löschung der Zuweisung zu verwenden,<br> es löscht alle Noten der ".INTITULEELEVE." der gelöschten ".INTITULECLASSE."n.");
define("LANGAFF9","ACHTUNG, die Noten der ausgewählten ".INTITULECLASSE."n werden gelöscht. \\n Möchten Sie fortfahren? \\n\\n TRIADE-Team");
define("LANGCREAT2","Konto löschen");


define("LANGPROF37","Klassenbuch."); // Cahier de textes

// news

define("LANGPARAM35","Wahl des Zeugnisses");
define("LANGPROBLE1","Antwort per E-Mail");
define("LANGPROBLE2","Alle Felder müssen ausgefüllt sein");
define("LANGMESS37","Dieses Modul wurde vom TRIADE-Administrator nicht validiert.<br><br> Das TRIADE-Team");

define("LANGPROFP23","Schulnoten von ");
define("LANGPROFP24","vom Monat");
define("LANGPROFP25","Fotoliste");
define("LANGPROFP26","Betreuung eines ".INTITULEELEVE."s");
define("LANGPROFP27","Informationen zu den Vertretern");
define("LANGPROFP28","Nachricht für die ".INTITULECLASSE);
define("LANGPROFP29","Rundschreiben für die ".INTITULECLASSE);
define("LANGPROFP30","Verwaltung des Berufspraktikums");
define("LANGPROFP31","Tabelle der Durchschnitte der ".INTITULEELEVE);
define("LANGPROFP32","Grafische Zeugnisse der ".INTITULEELEVE);


define("LANGLETTRELUNDI","M");	  // Lundi
define("LANGLETTREMARDI","D");    // Mardi
define("LANGLETTREMERCREDI","M"); // Mercredi
define("LANGLETTREJEUDI","D");    // Jeudi
define("LANGLETTREVENDREDI","F"); // Vendredi
define("LANGLETTRESAMEDI","S");   // Samedi
define("LANGLETTREDIMANCHE","S"); // Dimanche


define("LANGRESA71","Reservierung für den");
define("LANGRESA72","von");
define("LANGRESA73","bis");
define("LANGRESA74","Zusätzliche Informationen");

define("LANGbasededoni52","akzeptierter Wert: <b>0</b> oder Herr.<br>");
define("LANGbasededoni53","akzeptierter Wert: <b>1</b> oder Frau.<br>");
define("LANGbasededoni54","akzeptierter Wert: <b>2</b> oder Frl.<br>"); // Mlle -> Fräulein (veraltet, evtl. anpassen)
define("LANGbasededoni54_2","akzeptierter Wert: <b>3</b> oder Ms <br>");
define("LANGbasededoni54_3","akzeptierter Wert: <b>4</b> oder Mr <br>");
define("LANGbasededoni54_4","akzeptierter Wert: <b>5</b> oder Mrs <br>");


define("LANGacce_dep2bis","<br><b>ACHTUNG!! Überprüfen Sie Ihren Zugangsmodus,<br> wählen Sie Ihr entsprechendes Konto.</b>");

define("LANGNA3bis","Passwort Elternteil ");
define("LANGNA3ter","Passwort ".INTITULEELEVE." ");

define("LANGELE244","E-Mail");

define("LANGTP12","Bitte bestätigen Sie Ihr Konto");

define("LANGMESS40","Sie haben <strong> ");
define("LANGMESS40bis"," </strong> RSS-Feed(s) gespeichert.");
define("LANGMESS41","Konto ");
define("LANGMESS42","Zweite Verbindung");
define("LANGMESS43","Letzte Verbindung am");

define("LANGALERT4","ACHTUNG, wählen Sie unterschiedliche Betreffnamen.");

define("LANGMODIF26","Teilbereich ändern");
define("LANGPROF38","Quartalsnoten");
define("LANGPROF39","Zusatzinformation");

define("LANGCIRCU211","Verf. für"); // Disp. pour -> Verfügbar für

define("LANGTELECHARGE","Herunterladen");

define("LANGPARENT15bis","Sanktion vom");
define("LANGDISC2bis","Sanktionen des Tages drucken");

define("LANGRECH5","Geben Sie das oder die zu suchenden Element(e) an");
define("LANGRECH6","Sortieren nach");

define("LANGPROFP33","Zeugnisse ausfüllen");
define("LANGPROFP34","Zeugnis überprüfen");
define("LANGPROFP35","Kommentare der Zeugnisse einsehen oder ändern");


define("LANGPROFP36","Kein Quartalsdatum <br /><br /> für <u>dieses Schuljahr</u> zugewiesen");
define("LANGPROFP37","Kommentare speichern");

define("LANGGRP40","Gruppe erstellt");
define("LANGGRP41","Hier ist die Liste der nicht gespeicherten ".INTITULEELEVE);
define("LANGGRP42","Diese Gruppe existiert bereits");
define("LANGGRP43","Dateifehler");
define("LANGGRP44","Eine Gruppe löschen");
define("LANGGRP45","Datei importieren");
define("LANGGRP46","Gruppenname existiert bereits -- TRIADE-Dienst");

define("LANGPARAM37","Akademie"); // Académie (Bildungsregion)
define("LANGAGENDA274","Namenstag heute ");
define("LANGPARAM38","Herzlichen Glückwunsch zum Geburtstag an ");
define("LANGEDT1","D"); // première lettre (Datei)
define("LANGEDT1bis","atei im Format <b>xml</b> oder <b>zip</b> <br>Maximale Dateigröße: ");
define("ERREUR3","Kontaktieren Sie den TRIADE-Administrator, um das Netzwerk zu aktivieren.");
define("LANGELE30","Passwort ändern");
define("LANGMESS44","Nachricht an einen ".INTITULEELEVE." in ");
define("LANGMESS5","Nachricht an ein Elternteil in : ");
define("LANGMESS45","Nachricht an eine E-Mail : ");
define("LANGMESS2","Nachricht für ".INTITULEDIRECTION." : ");
define("LANGTRONBI9","der ".INTITULEELEVE);
define("LANGTRONBI10","des Personals");
define("LANGTRONBI11","Fotoliste des Personals");

define("LANGTITRE15","Einrichtung der Klassenlehrer oder Grundschullehrer");
define("LANGPER7","zugewiesen in ".INTITULECLASSE);
define("LANGPROF40","Zusätzliche Informationen");
define("LANGPROFP38","Betreuungsheft ausfüllen oder einsehen"); // Carnet de Suivi
define("LANGEDIT2","Mobiltelefon 1");
define("LANGEDIT3","Anrede ");
define("LANGEDIT4","Name Verantw. 2");
define("LANGEDIT5","Vorname Verantw. 2");
define("LANGEDIT6","Geburtsort");
define("LANGEDIT7","Anrede ");
define("LANGEDIT8","Name Verantw. 1");
define("LANGEDIT9","Mobiltelefon 2");
define("LANGEDIT10"," Elternteil");
define("LANGEDIT11","E-Mail ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGEDIT12","Tel. ".INTITULEELEVE);
define("LANGEDIT13","E-Mail Betreuer 2");
define("LANGEDIT14","von heute");
define("LANGEDIT15","Seit 1 Tag");
define("LANGEDIT16","Seit 2 Tagen");
define("LANGEDIT17","Seit 3 Tagen");
define("LANGEDIT18","Seit 4 Tagen");
define("LANGEDIT19","Unentschuldigte Verspätung(en)");
define("LANGEDIT20","Mobiltelefon ");
define("LANGSMS1","SMS-Versand für Verspätungen seit ");
define("LANGSMS2","Nicht angegeben");
define("LANGSUPPLE","Liste der Vertretungen");
define("LANGSUPPLE1","Als Vertretung für ");
define("LANGTITRE2","Aktuelles der Einrichtung");
define("LANGTITRE1","Ereignisse");

define("LANGDISC58",INTITULEELEVE." eine Disziplin hinzufügen");
define("LANGDISC59","Eingabe im U.S.A.-Modus");
define("LANGDISC60","Prüfung ");

define("LANGBT8","Auflisten / Ändern");
define("LANGBT9","Auflisten / Ändern");
define("LANGBT10","Auflisten / Ändern");
define("LANGDIRECTION","Verwaltung");

define("LANGTITRE36","Verwaltung der Mitglieder ".INTITULEDIRECTION);
define("LANGTITRE37","Verwaltung der Mitglieder Schulleben");
define("LANGTITRE38","Verwaltung ".INTITULEENSEIGNANT);
define("LANGTITRE39","Verwaltung Vertretungen");
define("LANGTITRE40",INTITULEELEVE);
define("LANGTITRE41","Verantw."); // für die Abkürzung von "responsable" -> Verantwortlicher
define("LANGTITRE42","Betreuer"); // im familiären Rahmen
define("LANGTITRE43","Verwaltung eines ".INTITULEELEVE."s");
define("LANGTITRE44","Eine Liste von ".INTITULEELEVE." importieren");
define("LANGTITRE45","Suche ".INTITULEELEVE);
define("LANGCHERCH1","Abhängig vom Suchkriterium");
define("LANGCHERCH2","Ende der Suche");
define("LANGCHERCH3","Anzahl der gefundenen Elemente");
define("LANGPROF3bis","Hausaufgaben, Klassenarbeiten und Tests anzeigen");
define("LANGTROMBI","Listen von ".INTITULEELEVE." nach WellPhoto exportieren");
define("LANGPURG1","Löschung der Informationen");
define("LANGPUR2","Löschung der Informationen"); // Doppelt?
define("LANGPROFP39","Jahresdurchschnittstabelle:");
define("LANGBLK1","Ihr Konto ist deaktiviert.<br /><br />Sie haben versucht, auf eine nicht autorisierte Seite zuzugreifen.<br /><br />Um Ihr Konto wieder zu aktivieren, kontaktieren Sie bitte Ihre Schule.<br /><br />Das TRIADE-Team.");
define("LANGCARNET4","zugreifen");
define("LANGFORUM10bis","Ihr Vorname ");
define("LANGTPROBL11","Wir werden Ihnen schnellstmöglich antworten. \\n\\n  Das TRIADE-Team ");
define("LANGTRAD1","Liste der durchgeführten Operationen");
define("LANGPARAM39","Zertifikat gespeichert");
define("LANGPARAM40","Zertifikat nicht gespeichert");
define("LANGPARAM41","Die Datei muss im <b>rtf</b>-Format sein und kleiner als 2MB");
define("LANGBASE42","Import der Datei");
define("ACCEPTER","Akzeptieren");
define("LANGCONDITION","Ich akzeptiere die Bedingungen");
define("LANGPARAM42","Liste der Verspätungen oder Abwesenheiten");
define("LANGCARNET5","Betreuungsheft einsehen");
define("LANGCARNET6","Betreuungsheft ausfüllen");
define("LANGCARNET7","Ausfüllen");
define("LANGCARNET8","Betreuungsheft");
define("LANGCARNET9","Ein Betreuungsheft erstellen");
define("LANGCARNET10","Ein Betreuungsheft ändern");
define("LANGCARNET11","Ein Betreuungsheft löschen");
define("LANGCARNET12","Ein Betreuungsheft einsehen");
define("LANGCARNET13","Ein Betreuungsheft exportieren");
define("LANGCARNET14","Ein Betreuungsheft importieren");
define("LANGCARNET15","Importieren");
define("LANGCARNET16","Exportieren");
define("LANGCARNET17","Menü Betreuungsheft");
define("LANGCARNET18","Name des Betreuungshefts");
define("LANGCONTINUER","Weiter --->");
define("LANGCARNET19","Erstellung eines Betreuungshefts");
define("LANGCARNET20","Bewertungscodes, die von den ".INTITULEENSEIGNANT." gewählt werden können");
define("LANGCARNET21","Buchstaben");
define("LANGCARNET22","Zahlen");
define("LANGCARNET23","Farben");
define("LANGCARNET24","Noten");
define("LANGCARNET25","(0 bis 10 oder 0 bis 20)");
define("LANGCARNET26","Entsprechung");
define("LANGCARNET27","erworben");
define("LANGCARNET28","zu&nbsp;bestätigen");
define("LANGCARNET29","nicht&nbsp;erworben");
define("LANGCARNET30","wird&nbsp;gerade&nbsp;erworben");
define("LANGCARNET31","nicht&nbsp;bewertet");
define("LANGCARNET32","Grün");
define("LANGCARNET33","Blau");
define("LANGCARNET34","Orange");
define("LANGCARNET35","Rot");
define("LANGCARNET36","Zeitraum");
define("LANGCARNET37","Zeiträume");
define("LANGCARNET38","Verwaltung des Betreuungshefts");
define("LANGCARNET39","Anzahl der Zeiträume, die die Unterschrift der Eltern, des ".INTITULEENSEIGNANT."s und der Leitung erfordern ");
define("LANGCARNET40","Anzahl ");
define("LANGCARNET41","Mit diesem Betreuungsheft verbundene Bereiche");
define("LANGCARNET42","Bereiche");
define("LANGCARNET43","Maximal 4 Auswahlmöglichkeiten (die ersten 4 werden beibehalten)");
define("LANGCARNET44","Betreuungsheft erstellt. Sie können jetzt die mit diesem Heft verbundenen Kompetenzen hinzufügen.");
define("LANGCARNET45","Hinzufügen eines Kompetenzbereichs ");
define("LANGCARNET46","Bezeichnung des Kompetenzbereichs ");
define("LANGCARNET47","Entspricht diese Bezeichnung einer Kompetenzrubrik?  ");
define("LANGCARNET48","Bezeichnung");
define("LANGCARNET49","Hinzufügen einer Kompetenz ");
define("LANGCARNET50","Allgemeine Merkmale des Hefts ändern ");
define("LANGCARNET51","Einen Kompetenzbereich hinzufügen ");
define("LANGCARNET52","Einen Kompetenzbereich ändern ");
define("LANGCARNET53","Geben Sie das Betreuungsheft an");
define("LANGCARNET54","Betreuungsheft nicht vorhanden ");
define("LANGCARNET55","Einsicht in ein Betreuungsheft");
define("LANGCARNET56","Ein Betreuungsheft");
define("LANGCARNET57","Abruf des Betreuungshefts im PDF-Format");
define("LANGCARNET58","Export eines Betreuungshefts");
define("LANGCARNET59","Um dieses Betreuungsheft abzurufen");
define("LANGCARNET60","Änderung eines Betreuungshefts");
define("LANGCARNET61","Löschung eines Betreuungshefts");
define("LANGCARNET63","Import eines Betreuungshefts");
define("LANGCARNET64","Zu importierende Datei");
define("LANGCARNET65","Gesamten Stundenplan vor dem Import löschen?");
define("LANGCARNET66","Import abgebrochen. <br><br>Dieser Heftname existiert bereits! <br />Bitte löschen Sie dieses Heft, bevor Sie den Import durchführen.");
define("LANGCARNET62","ACHTUNG!!! Alle dem Betreuungsheft unterliegenden Noten werden gelöscht!");
define("LANGEDT2","Import Stundenplan Visual Timetabling");
define("LANGEDT3","Import Visual Timetabling abgeschlossen");
define("LANGEDT4","Anzeige / Verwaltung des Stundenplans");
define("LANGEDT5","Stundenplan Visual Timetabling importieren");
define("LANGEDT6","Triade nach Visual Timetabling exportieren");
define("LANGEDT7","Anzeige / Verwaltung des Stundenplans"); // Doppelt?
define("LANGEDT8","Verwalten");
define("LANGEDT9","Einrichtung des Stundenplans");
define("LANGEDT10","SQLite-Modul nicht unterstützt. Bitte validieren Sie Ihren Server für die Unterstützung von SQLite.");
define("LANGGRP47","Gruppen suchen");
define("LANGGRP48","Liste der Gruppen eines ".INTITULEELEVE."s");
define("LANGGRP49","Liste der Gruppen"); // Doppelt?
define("LANGDISP21","Konfiguration Grund Abw. / Versp.");
define("LANGDISP22","Speicherung der Gründe ");
define("LANGDISP23","Bezeichnung des Grundes ");
define("LANGDISP24","Liste der Gründe ");
define("LANGDISP25","Anzahl der aktualisierten ".INTITULEELEVE);
define("LANGDISP26","Die Datei muss im xls-Format sein");
define("LANGCARNET633","Import Betreuungsheft abgeschlossen"); // Doppelt mit LANGAGENDA63? ID-Konflikt?
define("LANGCARNET64","Liste der Sanktionen");
// News 2
define("LANGCARNET67","Hinzufügen einer Disziplinarmaßnahme");
define("LANGCARNET68","Uhrzeit");
define("LANGVIES1","Name der dem Zeugnis zugeordneten Person");
define("LANGVIES2","Koeffizient der Note Schulleben im Zeugnis");
define("LANGVIES3","Koeffizient ".ucfirst(INTITULEENSEIGNANT));
define("LANGVIES4","Koeffizient Schulleben");
define("LANGVIES5","Liste der ".ucfirst(INTITULEENSEIGNANT)."");
define("LANGVIES6","Zusätzliche schulische Informationen");


define("LANGVIES7","Noten und Kommentare speichern");
define("LANGVIES8","Druck der Abwesenheiten einer ".INTITULECLASSE);
define("LANGVIES9","Geben Sie den Monat an");
define("LANGVIES10","Geben Sie eine ".INTITULECLASSE." an");
define("LANGPDF1","Eine PDF-Datei für das Ganze");
define("LANGPDF2","Eine PDF-Datei pro ".INTITULEELEVE);
define("LANGEDIT5bis","Vorname Verantw. 1");
define("LANGGRP50","Namen einer Gruppe ändern");
define("LANGGRP51","Name der Gruppe");
define("LANGGRP52","Änderungsmodul");
define("LANGGRP53","Neuer Gruppenname");
define("LANGGRP54","oder Notenübersicht");
define("LANGGRP55","Prüfung");
define("LANG1ER","1."); // 1ère
define("LANG2EME","2."); // 2ème
define("LANG3EME","3."); // 3ème
define("LANG4EME","4."); // 4ème
define("LANG5EME","5."); // 5ème
define("LANG6EME","6."); // 6ème
define("LANG7EME","7."); // 7ème
define("LANG8EME","8."); // 8ème
define("LANG9EME","9."); // 9ème
define("LANGGRP56","Benotung auf");
define("LANGGRP57","Behalten");
define("LANGGRP58","Achtung, die Noten der zur Löschung ausgewählten ".INTITULEELEVE." <br /> werden in allen ".INTITULECLASSE."n gelöscht, die diese Gruppe verwenden!!!");
define("LANGGRP59","Den/die ".INTITULEELEVE." abwählen, der/die nicht mehr zur Gruppe gehört/gehören");
define("LANGGRP60","Liste ändern");
define("LANGPARAM3","<font class=T1>Verfassen Sie Ihren Text für den Inhalt der Schulbescheinigung. Um den Namen, Vornamen und die Adresse des ".INTITULEELEVE."s automatisch in jedes Dokument zu übernehmen, geben Sie bitte die Zeichenfolgen <b>NomEleve</b>, <b>PrenomEleve</b>, <b>AdresseEleve</b>, <b>CodePostalEleve</b> und <b>VilleEleve</b> an der gewünschten Stelle ein. Ebenso besteht die Möglichkeit, die ".INTITULECLASSE." mit dem Schlüsselwort <b>ClasseEleve</b> oder <b>ClasseEleveLong</b> anzugeben, das Geburtsdatum mit <b>DateNaissanceEleve</b>, den Geburtsort über <b>LieuDeNaissance</b>, das aktuelle Datum über <b>DateDuJour</b>, das Schuljahr über <b>AnneeScolaire</b>, die Nationalität über <b>Nationalite</b>.</font><br><br>");
define("LANGEDIT20bis","Lös");  // abréviation de Supprimer  sur 3 lettres seulement -> Löschen
define("LANGGRP61","Zurück zur Aktualisierung");
define("LANGRTDJUS","Entschuldigt"); // für einen Verzug
define("LANGABSJUS","Entschuldigt"); // für eine Abwesenheit
define("LANGPARAM2","<font class=T1>Verfassen Sie Ihren Text für den Inhalt der Verspätungsnachricht, die an die Eltern gesendet werden soll. Sie können folgende Informationen angeben: Name des ".INTITULEELEVE."s: <b>NomEleve</b> - Vorname des ".INTITULEELEVE."s: <b>PrenomEleve</b> - Adresse: <b>AdresseEleve</b> - Postleitzahl: <b>CodePostalEleve</b> - Stadt: <b>VilleEleve</b> - Klasse des ".INTITULEELEVE."s: <b>ClasseEleve</b> - Datum der Verspätung: <b>RTDDATE</b> - Uhrzeit der Verspätung: <b>RTDHEURE</b> - Dauer: <b>RTDDUREE</b>  - Kumulierte Abwesenheit: <b>CumulABS</b> </font><br><br>");
define("LANGPARAM1","<font class=T1>Verfassen Sie Ihren Text für den Inhalt der Abwesenheitsnachricht, die an die Eltern gesendet werden soll. Sie können folgende Informationen angeben: Name des ".INTITULEELEVE."s: <b>NomEleve</b> - Vorname des ".INTITULEELEVE."s: <b>PrenomEleve</b> - Adresse: <b>AdresseEleve</b> - Postleitzahl: <b>CodePostalEleve</b> - Stadt: <b>VilleEleve</b> - Klasse des ".INTITULEELEVE."s: <b>ClasseEleve</b> - Anfangsdatum der Abwesenheit:  <b>ABSDEBUT</b> - Enddatum der Abwesenheit: <b>ABSFIN</b> - Dauer: <b>ABSDUREE</b> - Name des Verantwortlichen 1: <b>NomResponsable1</b> - Adresse Verantwortlicher 1: <b>AdresseResponsable1</b> - Stadt Verantwortlicher 1: <b>VilleResponsable1</b> - Kumulierte Abwesenheit: <b>CumulABS</b> - Heutiges Datum: <b>DATEDUJOUR</b> </font><br><br>");
define("LANGGRP62","Studie"); // étude (kann auch Lernzeit bedeuten)
define("LANGGRP63","Post");
define("LANGDELEGUE1","Vertreter");
define("LANGEDT10bis","SimpleXML-Modul nicht unterstützt. Bitte validieren Sie Ihren Server für die Unterstützung der SimpleXML-Erweiterung.");
define("LANGBULL45","Eine Nachricht an alle angekreuzten ".INTITULEENSEIGNANT." senden, um sie daran zu erinnern, ihre Zeugnisse auszufüllen.");
define("LANGBULL46","Anzahl der ausgefüllten Zeugnisse in der ".INTITULECLASSE);
define("LANGMESS46","Anzeigen in");
define("LANGMESS47","Ein Nachsitzen oder eine Sanktion löschen");
define("LANGCOUR","Schriftverkehr beendet");
define("LANGCOUR1","Liste der nicht durchgeführten Nachsitzen");
define("LANGCOUR2","Konfiguration des Schriftverkehrs für Nachsitzen");
define("LANGPARAM43","<font class=T1>Verfassen Sie Ihren Text für den Inhalt der Nachsitznachricht, die an die Eltern gesendet werden soll. Sie können folgende Informationen angeben: Name des ".INTITULEELEVE."s: <b>NomEleve</b> - Vorname des ".INTITULEELEVE."s: <b>PrenomEleve</b> - Adresse: <b>AdresseEleve</b> - Postleitzahl: <b>CodePostalEleve</b> - Stadt: <b>VilleEleve</b> - Klasse des ".INTITULEELEVE."s: <b>ClasseEleve</b> - Datum des Nachsitzens: <b>DATERETENU</b> - Uhrzeit des Nachsitzens: <b>HEURERETENU</b> - Dauer: <b>RETENUDUREE</b> - Grund: <b>RETENUMOTIF</b> -  Kategorie: <b>RETENUCATEGORY</b> - Zugewiesen von: <b>ATTRIBUEPAR</b> - Zu erledigende Aufgabe: <b>DEVOIRAFAIRE</b> - Die Fakten: <b>FAITS</b> - Anrede Betreuer 1: <b>CIVILITETUTEUR1</b> - Name des Verantwortlichen 1: <b>NOMRESP1</b> Vorname des Verantwortlichen 1: <b>PRENOMRESP1</b> - Heutiges Datum: <b>DATEDUJOUR</b> </font><br><br>");
define("RESA75","Zusätzliche Informationen"); // Doppelt mit LANGAGENDA74?
define("LANGCOM","Speichern Sie alle Ihre Kommentare in Ihrer Bibliothek.");
define("LANGCOM1","Der Maximalwert muss größer als der Minimalwert sein.");
define("LANGCOM2","Alle Felder müssen korrekt ausgefüllt sein.");
define("LANGCOM3","Anzahl ".INTITULEELEVE.": ");
define("LANGSTAGE91","Name des Verantwortlichen");
define("LANGSTAGE93","Funktion des Verantw.");
define("LANGSTAGE94","des Unternehmens");
define("LANGSTAGE95","Unternehmen");
define("LANGSTAGE96","Anzahl der gefundenen Elemente");
define("LANGSTAGE97","Bitte geben Sie einen numerischen Wert ein");
define("LANGSTAGE98","Bitte geben Sie das Anfangsdatum des Praktikums an");
define("LANGSTAGE99","Bitte geben Sie das Enddatum des Praktikums an");
define("LANGPATIENTE","Bitte warten");
define("LANGSMS3","Mobiltelefonnummer");
define("LANGSMS4","Maximal 150 Zeichen");
define("LANGSMS5","Nachricht");
define("LANGSMS6","Der Versand der SMS-Nachricht wird gespeichert und ist für die ".INTITULEDIRECTION." zugänglich");
define("LANGSMS7","SMS-Nachricht senden");
define("LANGSMS8","Eine SMS-Nachricht senden");
define("LANGSMS9","Liste der Telefonnummern der Eltern <br> von ");
define("LANGSMS10","Eine SMS an eine ganze ".INTITULECLASSE." senden");
define("LANGSMS11","Eine SMS an ein Elternteil eines ".INTITULEELEVE." über seinen Namen senden");
define("LANGSMS12","Eine SMS an eine Person über ihren Namen senden");
define("LANGSMS13","Eine SMS an eine Person über ihre Nummer senden");
define("LANGSMS14","Nummer");
define("LANGbasededoni54_5","akzeptierter Wert: <b>7</b> oder P <br>"); // P. (Pater/Priester?) Kontext unklar
define("LANGbasededoni54_6","akzeptierter Wert: <b>8</b> oder Sr <br>"); // Sr. (Schwester/Nonne?) Kontext unklar
define("LANGGRP27bis",INTITULEELEVE." mehreren Gruppen hinzufügen");
define("LANGGRP28bis","Hinzufügen ".INTITULEELEVE." zu Gruppe");
define("LANGGRP29bis","Eingabe&nbsp;/&nbsp;Ändern");
define("LANGNOTEUSA6","Notenentsprechung für die Benotung im USA-Modus");
define("LANGNOTE1","Bezeichnung der Prüfung");
define("LANGPARAM44","Eine Nachricht erhalten, wenn Sie eine Information vom Typ erhalten");
define("LANGMESS17bis","Konfig.");
define("LANGNNOTE2","Sortieren nach ".INTITULECLASSE);
define("LANGNNOTE3","Sortieren nach Name");
define("LANGNNOTE4","Titel des Dokuments angeben");
define("LANGBULL47","Zeugnis ohne Teilbereiche");
define("LANGBULL48","Zeugnis mit Teilbereichen");
define("LANGBULL49","Zeugnis Probeexamen");
define("LANGMESS48","Löschbox");
define("LANGMESS49","Keinem ".INTITULEELEVE." ist ein Unternehmen zugewiesen.");
define("LANGMESS50","Plan der ".INTITULECLASSE);
define("LANGMESS51","Fakultative Fächer angeben");
define("LANGMESS52","(Noten werden im Gesamtdurchschnitt berücksichtigt, wenn sie besser als 10/20 sind)");
define("LANGMESS53","Vorherige Woche");
define("LANGMESS54","Nächste Woche");
define("LANGMESS55","Stundenplan der ".INTITULECLASSE);
define("LANGMESS56","Kein ".INTITULEELEVE);
define("LANGMESS57","Benutzername");
define("LANGMESS58","Dieses Konto hat keine Nummer.");
define("LANGMESS59","Auch entschuldigte Abw./Versp. ändern");
define("LANGMESS60","A");
define("LANGMESS60bis","bwesend");
define("LANGMESS61","der ".INTITULEENSEIGNANT);
define("LANGMESS62","Elternteil von ");
define("LANGMESS63","heute");
define("LANGBT27bis","Abw./Versp. speichern");
define("LANGDEPART3bis","Zugriff unterbrochen! ");
define("LANGDEPART4bis","Der Zugriff auf Ihr TRIADE ist derzeit unterbrochen, bitte kontaktieren Sie Ihre Schule für weitere Informationen.");
define("LANGAIDE","Online-Hilfe");
define("LANGAIDE1","Geben Sie die Entsprechungen zwischen Ihren in TRIADE gespeicherten Fächern und den für den Realschulabschluss unterrichteten Fächern an. Führen Sie dazu ein Drag & Drop (Ziehen & Ablegen) zwischen den Fächern von links nach rechts durch.");
define("LANGAIDE2","Verfassen Sie Ihren Text für den Inhalt des Praktikumsvertrags. Um Elemente wie Name, Vorname, Adresse usw. zu berücksichtigen, geben Sie bitte je nach Bedarf die folgende Zeichenfolge an:");
define("LANGBREVET1","Zugreifen");
define("LANGCONFIG4","Benachrichtigt werden, wenn");
define("LANGCONFIG5","Anz. unentschuldigter Abwesenheiten eines ".INTITULEELEVE." hat überschritten ");
define("LANGCONFIG6","Anz. unentschuldigter Verspätungen eines ".INTITULEELEVE." hat überschritten ");
define("LANGCONFIG7","Mal");
define("LANGCONFIG8","Liste der benachrichtigten Benutzer");

define("LANGMESS64","Personen, die diese Nachricht erhalten haben");
define("LANGMESS65","Liste der Hausordnungen");
define("LANGMESS66","Der Direktor");
define("LANGMESS67","Ich habe die oben genannten Dokumente zur Kenntnis genommen");
define("LANGMESS68","Ich akzeptiere die Hausordnung(en)");
define("LANGMESS69","Ich akzeptiere die allgemeinen Unterrichtsbedingungen");
define("LANGMESS70","Hausordnung zugänglich für die ".INTITULEENSEIGNANT);
define("LANGMESS71","Statusblatt der Ordnungen einsehen");
define("LANGMESS72","Statusblatt der Ordnungen drucken");
define("LANGMESS73","Liste der unbezahlten oder unvollständigen Zahlungen");
define("LANGMESS74","Statusblatt der Ordnungen");
define("LANGacce_dep2ter","<br><b>ACHTUNG! Überprüfen Sie Ihren Zugangsmodus sorgfältig und wählen Sie Ihr entsprechendes Konto.</b>");

//NEW NON CORRIGE

define("LANGMESS75","Zurück zum Hauptmenü");
define("LANGMESS76","Übereinstimmung");
define("LANGMESS77","(Hausaufgabe, Test, Prüfung)");
define("LANGMESS78","Sortieren nach ");
define("LANGMESS79","Noten sichtbar für ".INTITULEELEVE." am ");
define("LANGMESS80","Schulleben");
define("LANGMESS81","Verbindung wird hergestellt");
define("LANGMESS82","Durchschnitt");
define("LANGMESS83","Klassendurchschnitt von ".INTITULECLASSE);
define("LANGMESS84","Max");
define("LANGMESS85","Min");
define("LANGMESS86","Kein Quartalsdatum zugewiesen");
define("LANGMESS86bis","für");
define("LANGMESS86ter","dieses Schuljahr");
define("LANGMESS87","Note der Hausaufgaben von");

define("LANGMESS88","Klassenbuch gespeichert -- Triade Dienst");
define("LANGMESS89","Klassenbuch in ");
define("LANGMESS90","Denken Sie daran, Ihren Inhalt zu speichern, bevor Sie den Tab wechseln.");
define("LANGMESS91","Wochenansicht");
define("LANGMESS92","Unterrichtsinhalt");
define("LANGMESS93","Angehängte Datei");
define("LANGMESS94","Anhang");
define("LANGMESS95","Lernziel");
define("LANGMESS96","Hausaufgabe für den ");
define("LANGMESS97","nicht angegeben");
define("LANGMESS98","Hausaufgabe");
define("LANGMESS99","Notizblock");
define("LANGMESS100","Vollständige Ansicht");
define("LANGMESS101","Bestätigung");
define("LANGMESS102","Ansicht");
define("LANGMESS103","Geschätzte Zeit für diese Arbeit ");
define("LANGMESS104","Geschätzte Arbeitszeit auf ");
define("LANGMESS105","Datei ");
define("LANGMESS106","Änderung ");
define("LANGMESS107","Dieses Blatt löschen ");
define("LANGMESS108","Geschätzte Gesamtarbeitszeit ");
define("LANGMESS109","vom"); // notion de date du xxxx au xxxx
define("LANGMESS110","bis"); // notion de date du xxxx au xxxx
define("LANGMESS111","PDF-Format");
define("LANGBT288","Ansehen / Ändern");
define("LANGSITU1","Verheiratet");
define("LANGSITU2","Geschieden");
define("LANGSITU3","Witwer");
define("LANGSITU4","Witwe");
define("LANGSITU5","Lebensgefährte/in");
define("LANGSITU6","Eingetragene Lebenspartnerschaft"); // PACS
define("LANGSITU7","Ledig");
define("LANGFIN002","Zahlungsplan");
define("LANGFIN003","Zahlungsplan");
define("LANGFIN004","Kein Datum konfiguriert");
define("LANGCONFIG","Konfigurieren");

define("LANGMESS112","Zeugniskommentar Quartal/Halbjahr");
define("LANGMESS113","Wahl des Kommentars");
define("LANGMESS114","Kommentar Realschulabschluss"); // Brevet des collèges
define("LANGMESS115","Zeugnisansicht von ".INTITULECLASSE);
define("LANGMESS116","Zugreifen");
define("LANGMESS117","Serie"); // oder Zeugnisart/Staffel je nach Kontext
define("LANGMESS118","In den erweiterten Modus wechseln");
define("LANGMESS119","Beurteilungen, Ratschläge zur Verbesserung");
define("LANGMESS120","Stärken. Fortschritte. Bemühungen");
define("LANGMESS121","Abweichungen von den erwarteten Zielen");
define("LANGMESS122","Ratschläge zur Verbesserung");
define("LANGMESS123","Durchschnitt der ".INTITULECLASSE);
define("LANGMESS124","Vorheriger Kommentar");
define("LANGMESS125","Zur Liste hinzufügen");
define("LANGMESS126","Kommentar speichern");
define("LANGMESS127","Zurückkehren und klicken auf");
define("LANGMESS128","Speicherung");
define("LANGMESS129","Ansehen");
define("LANGMESS130","Vorheriger Durchschn.");
define("LANGMESS131","Kommentare speichern");
define("LANGMESS132","Bitte warten S.V.P.");
define("LANGMESS133","Leerer Kommentar");
define("LANGMESS134","Kommentar nicht gespeichert");
define("LANGMESS135","Beurteilung für das Quartalszeugnis ".INTITULECLASSE);
define("LANGMESS136","hier klicken");
define("LANGMESS137","Zusätzliche schulische Information");
define("LANGMESS138","Andere Kommentare für die Zeugnisse eingeben");

//-----------------Traduction Sam le 06/06/2014
//-----------------messagerie_brouillon.php
define("LANGMESS139","Entwürfe Nachrichten");
define("LANGMESS140","Einen Entwurf vorbereiten ");
define("LANGMESS141","Zugriff");
define("LANGMESS142","Einen Entwurf bestätigen");
define("LANGMESS143","Entwurfsnachrichten sind für alle Mitglieder der Leitung sichtbar");

//------------------param.php
define("LANGMESS144","Unterschrift des Direktors");
define("LANGMESS145","Schuljahr");
define("LANGMESS156","Land");
define("LANGMESS159","Wahl des Standorts");
define("LANGMESS160","Neuer Standort");
define("LANGMESS177","Abteilung "); // Département (kann auch Bundesland/Kanton bedeuten)
//------------------definir_trimestre.php
define("LANGMESS146","Speicherung im Halbjahresformat.");
define("LANGMESS147","Alle ".INTITULECLASSE."n");
define("LANGMESS148","Liste der Quartals- oder Halbjahreszeiträume ");
define("LANGMESS149","Ändern");
define("LANGMESS150","Löschen");
define("LANGMESS157","Quartal");
define("LANGMESS158","Klasse");
//-----------------probleme_acces_2.php
define("LANGMESS151","Identifizieren Sie Ihr Konto");
define("LANGMESS152","Bitte identifizieren Sie zuerst Ihr Konto, um Ihr Passwort zurückzusetzen.");
define("LANGMESS153","Passwortanforderung");
//-----------------geston_groupe.php
define("LANGMESS154","Gruppenerstellung");
define("LANGMESS155","Liste der Gruppen der ".INTITULEENSEIGNANT);
//-----------------gestcompte.php
define("LANGMESS161","Verwaltung Ihres Kontos");
//-----------------messagerie_reception.php
define("LANGMESS162","Verwaltung Ihres Kontos"); // Doppelt?
//------------------gestion_groupe.php
define("LANGBT53","Eingabe");
define("LANGMESS163","Überprüfung der Gruppen");
//-------------------messagerie_suppression.php
define("LANGMESS164","Papierkorb"); // Boite de suppression
define("LANGMESS165","Archivieren in");
//-------------------messagerie_reception.php
define("LANGMESS166","Posteingang");
//-------------------parametrage.php
define("LANGMESS167","Parametrierung Ihres Kontos");
define("LANGMESS168","Aktuelles");
define("LANGMESS169","Raum- / Gerätebuchung");
define("LANGMESS170","Triade Nachrichtensystem");
define("LANGMESS171","(Geben Sie Ihre E-Mail-Adresse ein)");
define("LANGMESS172","(Mobiltelefonnummer)");
//-------------------messagerie_envoi.php
define("LANGMESS173","Nachricht an eine Gruppe ");
define("LANGMESS174","Nachricht an die Vertreter:");
define("LANGMESS175","Nachricht an ein Personalmitglied: ");
define("LANGMESS176","Nachricht an einen Praktikumsbetreuer: ");
//-------------------creat_admin.php
define("LANGMESS178","Anr."); // Civ. -> Anrede
define("LANGMESS179","Gehaltsindex");
//-------------------creat_tuteur.php
define("LANGMESS180","Erstellung eines Praktikumsbetreuerkontos");
define("LANGMESS181","Liste / Änderung eines Praktikumsbetreuers");
define("LANGMESS182","Verwaltung der Praktikumsbetreuer");
define("LANGMESS183","Verbundenes Unternehmen");
define("LANGMESS184","In Eigenschaft als ");
//--------------------creat_personnel.php
define("LANGMESS185","Verwaltung der Personalmitglieder");
define("LANGMESS186","Erstellung eines Personalkontos");
//--------------------creat_eleve.php
define("LANGMESS187","Suchen");
define("LANGMESS188","Importieren");
define("LANGMESS189","Löschen");
define("LANGMESS190","FS1/Spez.:"); // Lv1/Spé
define("LANGMESS191","FS2/Spez.:"); // Lv2/Spé
define("LANGMESS192","Stipendiat");
define("LANGMESS193","Anmeldung beim BDE"); // BDE (Bureau des Étudiants) -> Studentenrat/Fachschaft
define("LANGMESS194","Anmeldung in der Bibliothek");
define("LANGMESS195","Stipendienbetrag");
define("LANGMESS196","Praktikumsvergütung");
define("LANGMESS197","Buchhaltungscode ");
define("LANGMESS198","Adresse");
define("LANGMESS199","Telefon");
define("LANGMESS200","Mobiltelefon");
define("LANGMESS201","E-Mail Student");
define("LANGMESS202","Universitäts-E-Mail");
define("LANGMESS203","Familienstand");
define("LANGMESS204","Adresse kopieren");
define("LANGMESS205","Vorherige Klasse");
//--------------------creat_class.php
define("LANGMESS206","Bezeichnung der ".INTITULECLASSE);
define("LANGMESS207","Schule");
//--------------------creat_matiere.php
define("LANGMESS208","Kurzformat");
define("LANGMESS209","Langformat");
define("LANGMESS210","Fachcode");
//--------------------reglement.php
define("LANGMESS211","Hausordnung");
define("LANGMESS212","Eine Hausordnung hinzufügen");
define("LANGMESS213","Die Hausordnung(en) auflisten");
define("LANGMESS214","Eine Hausordnung löschen");
//--------------------sms.php
define("LANGMESS215","SMS-Verwaltung");
define("LANGMESS216","Mitglied");
define("LANGMESS217",ucfirst(INTITULEDIRECTION));
define("LANGMESS218",ucfirst(INTITULEENSEIGNANT));
define("LANGMESS219","Schulleben");
define("LANGMESS220","Personal");
//--------------------Codebar0.php
define("LANGMESS221","Barcode:");
//--------------------vatel_gestion_ue.php
define("LANGMESS222","Verwaltung der Lehreinheiten");
define("LANGMESS223","Erstellung einer Lehreinheit");
define("LANGMESS224","Auflisten/Ändern");
//--------------------base_de_donne_importation.php
define("LANGMESS225","Excel-Datei");
define("LANGMESS226","XML-Datei");
define("LANGMESS227","Barcode");
//--------------------edt.php
define("LANGMESS228","Löschung eines Zeitraums ");
define("LANGMESS229","Anpassung der Uhrzeiten ");
define("LANGMESS230","Zeitraum sichtbar im Stundenplan"); // EDT (Emploi Du Temps)
define("LANGMESS231","Bild oder PDF importieren: ");
define("LANGMESS232","(Bildformat: jpg und weniger als 2MB)");
define("LANGMESS233","Stundenplan der ".INTITULECLASSE.": ");
//--------------------export.php
define("LANGMESS234","Datenexport");
define("LANGMESS235","Zu exportierende Informationen");
define("LANGMESS236","Personal");
define("LANGMESS237","Wahl der Extraktion: ");
//--------------------export.php (doublon?)
define("LANGMESS238","Name des ".INTITULEENSEIGNANT."s ");
define("LANGMESS239","Export im PDF-Format: ");
define("LANGMESS240","Exportieren");
//--------------------commaudio.php
define("LANGMESS241","Betreff: ");
define("LANGMESS242","Audiodatei: ");
//--------------------consult_classe.php
define("LANGMESS243","Drucken ");
define("LANGMESS365","&nbsp;Halbpension&nbsp;");
define("LANGMESS366","&nbsp;Intern&nbsp;");
define("LANGMESS367","&nbsp;Extern&nbsp;");
define("LANGMESS368","&nbsp;Unbekannt&nbsp;");
//--------------------resr_admin.php
define("LANGMESS244","Über Stundenplan reservieren");
//--------------------carnetnote.php
//------------modif nom de l'enseignant---LANGMESS238
//--------------------publipostage.php
define("LANGMESS245","Mitgliedstyp: ");
define("LANGMESS246","Eltern");
define("LANGMESS247","Studenten");
define("LANGMESS248","Adresstyp:");
define("LANGMESS249","Betreuer");
define("LANGMESS327","Serienbrief");
define("LANGMESS328","Anrede der Studenten anzeigen: ");
define("LANGMESS329","Matrikelnummer anzeigen: ");
define("LANGMESS330","Klasse anzeigen: ");
define("LANGMESS331","Adresse anzeigen: ");
//--------------------ficheeleve3.php
define("LANGMESS250","Klassenliste");
define("LANGMESS251","Eine SMS senden");
define("LANGMESS252","Datenblatt ändern");
define("LANGMESS253","Einem Praktikum zuweisen");
define("LANGMESS254","Dieses Konto sperren");
define("LANGMESS255","Dieses Konto entsperren");
define("LANGMESS259","Informationen");
define("LANGMESS260","Notenheft");
define("LANGMESS261","Schulleben");
define("LANGMESS262","Disziplinarmaßnahmen");
define("LANGMESS263","Durchgeführte Operationen");
define("LANGMESS264","Info Betreuer 1");
define("LANGMESS265","Info Betreuer 2");
define("LANGMESS266","Info Student");
define("LANGMESS267","Archive");
define("LANGMESS268","Medizinische Info.");
define("LANGMESS269","Zusatzinfo.");
define("LANGMESS270","Name:");
define("LANGMESS271","Vorname:");
define("LANGMESS272","Klasse:");
define("LANGMESS273","Geb.-Datum&nbsp;:");
define("LANGMESS274","Nationalität&nbsp;:");
define("LANGMESS275","Geburtsort&nbsp;:");
define("LANGMESS276","Stipendiat:");
define("LANGMESS277","Studenten-Nr.&nbsp;:");
define("LANGMESS278","FS1/Spez.:");
define("LANGMESS279","FS2/Spez.:");
define("LANGMESS280","Option:");
define("LANGMESS281","Status:"); // Régime
define("LANGMESS282","Rang-Nr.&nbsp;:");
define("LANGMESS283","Kontakt&nbsp;:");
define("LANGMESS284","Familienstand&nbsp;:");
define("LANGMESS285","Adresse&nbsp;:");
define("LANGMESS287","PLZ&nbsp;:");
define("LANGMESS288","Stadt&nbsp;:");
define("LANGMESS289","E-Mail&nbsp;:");
define("LANGMESS290","Telefon&nbsp;:");
define("LANGMESS291","Beruf&nbsp;:");
define("LANGMESS292","Tel.&nbsp;Berufl.&nbsp;:");
define("LANGMESS293","Geschlecht&nbsp;:");
define("LANGMESS294","Vorherige&nbsp;Kl.&nbsp;:");
define("LANGMESS295","Schuljahr");
define("LANGMESS296","Quart.&nbsp;/&nbsp;Halbj.");
define("LANGMESS297","Zeugnis");
define("LANGMESS298","Durchgeführt&nbsp;am");
define("LANGMESS308","Berechtigungen nicht erteilt");
define("LANGMESS309","Eine Information hinzufügen");
define("LANGMESS310","Einzelgespräch");
define("LANGMESS311","Abw./Versp. planen");
define("LANGMESS312","Abw./Versp. ändern");
define("LANGMESS313","Abw./Versp. löschen");
define("LANGMESS320","$email_eleve / $emailpro_eleve"); // Variablen beibehalten
define("LANGMESS321","$tel_eleve / $tel_fixe_eleve"); // Variablen beibehalten

//--------------------elevesansclasse.php
define("LANGMESS256","Speichern"); // Save
//--------------------consult_classe.php
define("LANGMESS257","Alle ".INTITULECLASSE."n.");
//--------------------ficheeleve.php
define("LANGMESS258","Suchen"); // Search
//--------------------newsactualite.php
define("LANGMESS299","    Titel : ");
define("LANGMESS300","Ihr TRIADE ist nicht für den Internetzugang konfiguriert, bitte konsultieren Sie Ihr Triade-Administratorkonto, um die Option für die Internetverbindung zu validieren.");
define("LANGMESS3655","Aktuelles von der Startseite"); // "Actualités de la 1er page"
//--------------------actualiteetablissement.php
//--------------------newsdefil.php
//--------------------commaudio.php // Bouton Parcourir -> Durchsuchen
//--------------------commvideo.php
define("LANGMESS301","Link des Videos : ");
define("LANGMESS302","oder Youtube-Link : ");
//--------------------emmargement.php
define("LANGMESS303","Anwesenheitslistenverwaltung ");
define("LANGMESS304","Auf Klassenebene");
define("LANGMESS305","Leere Anwesenheitsliste");
define("LANGMESS306","Leere Prüfungsanwesenheitsliste");
define("LANGMESS307","Auf Gruppenebene");
define("LANGMESS314","Anwesenheitsliste des Tages ");
define("LANGMESS315","Anwesenheitsliste&nbsp;vom&nbsp;");
define("LANGMESS316","Für die ".INTITULECLASSE." : ");
define("LANGMESS317",ucfirst(INTITULEENSEIGNANT)." : ");
define("LANGMESS318","Alle ".INTITULEENSEIGNANT." : ");
define("LANGMESS319","Höhe der Zellen der ".INTITULEELEVE);
//--------------------trombinoscope0.php
define("LANGMESS322","Im PDF-Format drucken von ".INTITULEELEVE);
define("LANGMESS323","Fotos im ZIP-Format importieren");
//--------------------chgmentclas.php
define("LANGMESS324",": Noten, Abwesenheiten, Verspätungen, Befreiungen, Sanktionen, Nachsitzen, Abschlüsse, Zeugniskommentare des ".INTITULEELEVE.", Schulgebühren, Klassenplan, Abschlüsse, Praktikumszuweisung");
//------LANGASS10-- Variable pour suppression
//--------------------certificat.php
define("LANGMESS325","Manuelle Parametrierung : ");
define("LANGMESS326","Import-Parametrierung : ");
//define("LANGMESS331","Publipostage"); // Bereits oben definiert
//--------------------visa_direction.php
define("LANGMESS332","Zeugnistyp : ");
define("LANGMESS333","Bestätigen");
define("LANGMESS334","Jährlich");
///////////////////////
//--------------------list_classe.php----- Voir comment changer le bouton Modifier -> Ändern
//--------------------list_matiere.php---- Voir comment changer le bouton Modifier -> Ändern
//--------------------listepreinscription.php
define("LANGMESS335","Liste der Voranmeldungen");
//--------------------reglement_ajout.php
define("LANGMESS336","Hausordnung");
define("LANGMESS337","Ordnung");
define("LANGMESS338","die oder die ".INTITULECLASSE."(n)");
define("LANGMESS339","die oder die ".INTITULECLASSE."(n)"); // Doppelt?
//--------------------affectation_visu.php
define("LANGMESS340","Jahr/Quartal/Halbjahr");
define("LANGMESS341","Ganzes Jahr");
define("LANGMESS342","Quartal 1 / Halbjahr 1");
define("LANGMESS343","Quartal 2 / Halbjahr 2");
define("LANGMESS344","Quartal 3");
//--------------------affectation_modif_key.php
//----Modidifier le bouton suivant par next -> Weiter
//--------------------reglement_ajout.php
//--------------------reglement_liste.php
// comment modifier le lien Reglement interieur -> Hausordnung
//----------------/reglement_supp.php
define("LANGMESS345","Anzeigen");
//-----------------vatel_list_ue.php
define("LANGMESS346","Verwaltung der Lehreinheiten");
define("LANGMESS347","Filter : ");
define("LANGMESS348","Ändern");
define("LANGMESS349","Löschen");
define("LANGMESS350","Name LE"); // UE -> Lehreinheit
define("LANGMESS351","Sem."); // Semester
define("LANGMESS352","Erstellung einer LE");

//----------------creat_groupe.php
define("LANGMESS353","Excel-Datei");
define("LANGMESS354","Inhalt der Excel-Datei");
//----------------visa_direction2.php
define("LANGMESS355","Kommentare der ".INTITULEENSEIGNANT);
define("LANGMESS356","Visum Leitung");
//----------------imprimer_tableaupp.php
define("LANGMESS357","Druck Notentabelle Quartal oder Halbjahr");
define("LANGMESS358","Ranking anzeigen ");
define("LANGMESS359","Leere Spalten anzeigen ");
define("LANGMESS360","Gruppierung nach Modul ");
define("LANGMESS361","Fächer anzeigen ");
define("LANGMESS362","Tabelle der verschiedenen Durchschnitte im Excel-Format");
define("LANGMESS374","Bis zum :");
define("LANGMESS375","Excel-Datei");
//------------------affectation_creation_key.php
//------------------affectation_visu2.php
define("LANGMESS363","Ansicht"); // Visu
define("LANGMESS364","Lehreinheit");
//------------------entretien.php
define("LANGMESS369","Protokoll der Einzelgespräche");
define("LANGMESS370","Protokoll der Gruppengespräche ");
define("LANGMESS371","Zusammenfassende Tabelle");
define("LANGMESS372","&nbsp;".ucfirst(INTITULEENSEIGNANT)."&nbsp;");
define("LANGMESS373","&nbsp;Stundenanzahl&nbsp;");
//------------------base_de_donne_key.php
define("LANGMESS376","Um Ihren Zugangscode zu ändern, konsultieren Sie bitte Ihr Konto ");
define("LANGMESS377","Triade Administrator");
define("LANGMESS378","dann das Modul \"Zugangscode\"");
//------------------chgmentClas0.php
define("LANGMESS379","kein Jahr");
define("LANGMESS380","Wahl der ".INTITULECLASSE);
//------------------chgmentClas00.php
define("LANGMESS381","Wahl der ".INTITULECLASSE." :");
define("LANGMESS383","Wechsel der ".INTITULECLASSE." für die ".INTITULEELEVE." in ");
define("LANGMESS384","Übergang für das Schuljahr");
define("LANGMESS385","Ohne ".INTITULECLASSE);
//------------------bro3uillon_reception.php // brouillon
define("LANGMESS382","Liste der Entwurfsnachrichten");
//------------------imprimer_trimestre.php
define("LANGMESS386","Personalisiertes&nbsp;Zeugnis");
define("LANGMESS387","Zeugnis definiert für die ".INTITULEENSEIGNANT." (und Eltern demnächst)");
define("LANGMESS388","Sichtbar für die ".INTITULECLASSE);
define("LANGMESS389","Zugriff auf Zeugnisse für die ".INTITULEENSEIGNANT." erlauben");

// --- NEW ERIC --- //
define("LANGMESST390","Bitte geben Sie die für Triade notwendigen Informationen für den Standort Nummer 1 ein!!<br>Bitte bestätigen Sie, indem Sie das folgende Formular validieren oder erneut validieren.");
define("LANGMESST391","Standort löschen");
define("LANGMESST392","Betreuungsheft");
define("LANGMESST393","KONTO GESPERRT");
define("LANGMESST394","KONTO IN PROBEZEIT");
define("LANGMESST395","Probezeit aufheben");
define("LANGMESST396","In Probezeit setzen");
define("LANGMESST397","Eingabe&nbsp;durch");
define("LANGMESST398","Diese Liste speichern");
define("LANGMESST399","Eine komplexe Suche durchführen");
define("LANGMESST700","Aktuelle Nachricht löschen");
define("LANGMESST701","Aktuelles von der Startseite");
define("LANGMESST702","Titel des Videos");
define("LANGMESST703","Link kopieren/einfügen ");
define("LANGMESST704","Geben Sie den Empfänger der zu übermittelnden Nachricht an.");
define("LANGMESST705","Nachricht nicht gesendet! \\n \\n Sie haben keine Berechtigung, eine Nachricht an diese Person zu senden.\\n\\n Das TRIADE-Team. ");
define("LANGTMESS400","Ihre Anfrage wurde berücksichtigt,");
define("LANGTMESS401","Bitte überprüfen Sie Ihre E-Mail-Adresse");
define("LANGTMESS402","Kein Konto für diese E-Mail!!");
define("LANGTMESS403","Bitte kontaktieren Sie Ihren Administrator, indem Sie klicken ");
define("LANGTMESS404","auf diesen Link ");
define("LANGTMESS405","TRIADE-Administrator kontaktieren ");
define("LANGTMESS406","Überprüfen");
define("LANGTMESS407","Überprüfung / Check Gruppen");
define("LANGTMESS408","Ungültige E-Mail!!");
define("LANGTMESS409","Bitte geben Sie eine gültige E-Mail-Adresse an.");
define("LANGTMESS410","<b>Hotmail</b>-E-Mails werden von unseren Servern nicht erkannt.");
define("LANGTMESS411","Bitte geben Sie eine andere E-Mail-Adresse an.");
define("LANGTMESS412","Neues Verzeichnis");
define("LANGTMESS413","Nachricht bereits gedruckt");
define("LANGTMESS414","Anhang");
define("LANGTMESS415","Archivieren in");
define("LANGTMESS416","Postfach von ");
define("LANGTMESS417","Posteingang");
define("LANGTMESS418","Klassischer Modus");
define("LANGTMESS419","Gesendete Nachrichten ");
define("LANGTMESS420","Ihre Verzeichnisse ");
define("LANGTMESS421","per E-Mail ");
define("LANGTMESS422","per SMS ");
define("LANGTMESS423","per RSS ");
define("LANGTMESS424","Modul bei Ihrer Anmeldung");
define("LANGTMESS425","Abwesenheitsmodul");
define("LANGTMESS426","Liste einer LE (Ändern / Löschen)");
define("LANGTMESS427","PDF Stundenplan Gespeichert");
define("LANGTMESS428","Das Triade-Team");
define("LANGTMESS429","Bild Stundenplan Gespeichert");
define("LANGTMESS430","Stundenplan Gelöscht");
define("LANGTMESS431","Strukturname bereits verwendet");
define("LANGTMESS432","Exportformat");
define("LANGTMESS433","&nbsp;Gesamt&nbsp;");
define("LANGTMESS434","Spalten");
define("LANGTMESS435","Praktikumsbetreuer");
define("LANGTMESS436","Adresse anzeigen");
define("LANGTMESS437","Alle Eltern");
define("LANGTMESS438","Alle ");
define("LANGTMESS439","Auflisten / Ändern");
define("LANGTMESS440","hinzufügen");
define("LANGTMESS441","Anordnung / Info.");
define("LANGTMESS442","pro Monat");
define("LANGTMESS443","Anz. Monate");
define("LANGTMESS444","Buchhaltungscode");
define("LANGTMESS445","Universitär");
define("LANGTMESS446","RIB bearbeiten"); // RIB (Relevé d'Identité Bancaire) -> Bankverbindung
define("LANGTMESS447","Daten bereits gespeichert");
define("LANGTMESS448","Zugehöriger Standort");
define("LANGTMESS449","Vollständige Definition");
define("LANGCIV0","Herr"); // M.
define("LANGCIV1","Frau"); // Mme
define("LANGCIV2","Frl."); // Mlle (Fräulein, veraltet)
define("LANGCIV3","Ms"); // (Englisch, neutral)
define("LANGCIV4","Mr"); // (Englisch)
define("LANGCIV5","Mrs"); // (Englisch)
define("LANGCIV6","Herr oder Frau");
define("LANGCIV7","Sr"); // (Schwester, Nonne?)
define("LANGCIV8","General");
define("LANGCIV9","Oberst");
define("LANGCIV10","Oberstleutnant");
define("LANGCIV11","Major"); // Commandant (kann auch Major sein)
define("LANGCIV12","Hauptmann");
define("LANGCIV13","Leutnant");
define("LANGCIV14","Unterleutnant");
define("LANGCIV15","Fähnrich"); // Aspirant
define("LANGCIV16","Major"); // Major (kann auch Stabsfeldwebel sein)
define("LANGCIV17","Hauptfeldwebel"); // Adjudant-Chef
define("LANGCIV18","Feldwebel"); // Adjudant
define("LANGCIV19","Oberfeldwebel"); // Sergent-Chef
define("LANGCIV20","Feldwebel"); // Sergent (kann auch Unteroffizier sein)
define("LANGCIV21","Hauptgefreiter"); // Caporal-Chef
define("LANGCIV22","Gefreiter"); // Caporal
define("LANGCIV23","Flieger"); // Aviateur
define("LANGCIV24","Dr.");

define("LANGMESS391","Klassischer Modus");
define("LANGMESS392","Empfängerliste");
define("LANGMESS393","Liste löschen");
define("LANGMESS394","Eine Datei auswählen");
define("LANGMESS395","Liste der Leitungsmitglieder");
define("LANGMESS396","Anzeigen / Ändern");
define("LANGMESS397","Liste des Schullebens");
define("LANGMESS398","Konto deaktivieren");
define("LANGMESS399","Konto aktivieren");
define("LANGMESS400","Berechtigung");
define("LANGMESS401","Liste der Personalkonten ");
define("LANGMESS403","Liste Praktikumsbetreuer");
define("LANGMESS404","Auflisten / Ändern");
define("LANGMESS405","Herr");
define("LANGMESS406","Frau");
//--------------------list_classe.php
//--------------------modif_classe.php
define("LANGMESS407","Änderung einer ".INTITULECLASSE);
define("LANGMESS408",INTITULECLASSE." aktivieren");
define("LANGMESS409",INTITULECLASSE." deaktivieren");
define("LANGMESS410","Vollständige Definition");
define("LANGMESS411","Zugehöriger Standort");
//--------------------affectation_creation.php
//-------------------publipostage.php
define("LANGMESS412","Etikettentyp");
define("LANGMESS413","Mitgliedstyp");
//-------------------list_matiere.php
//-------------------modif_matiere.php
define("LANGMESS414","Mitgliedstyp"); // Doppelt?
define("LANGMESS415","Fachcode");
define("LANGMESS416","Name des Teilbereichs");
define("LANGMESS417","Teilbereich löschen");
define("LANGMESS418","Fach deaktivieren");
define("LANGMESS419","Fach aktivieren");
//-------------------triadev1/circulaire_liste.php
define("LANGMESS420","Referenz");
//-------------------visu_retard_parent.php
//-------------------messagerie_envoi.php
define("LANGMESS421","Sie haben keine Berechtigung, eine Nachricht an diese Person zu senden.");
//-------------------information.php
define("LANGMESS422","Schulische Informationen");
//-------------------parametrage.php
define("LANGMESS423","Modul bei Ihrer Anmeldung ");
define("LANGMESS424","Aktuelles");
define("LANGMESS425","Abwesenheitsmodul");
//-------------------retardprof.php
define("LANGMESS426",INTITULEELEVE." als verspätet oder abwesend angeben");
//-------------------retardprof2.php
define("LANGMESS427","Uhrzeit Abw./Versp. angeben");
define("LANGMESS428","In ");
define("LANGMESS429","Uhrzeit : ");

define("LANGTMESS450","Übersetzung andere Sprache");
define("LANGTMESS451","Die importierte Datei dient derzeit als Referenz für die Erstellung des Zertifikats.");
define("LANGTMESS452","Abrufen");
define("LANGTMESS453","Zertifikat Nummer:");
define("LANGTMESS454","Eine Anmeldung hinzufügen:");
define("LANGTMESS455","Neu");
define("LANGTOUS","Alle");
define("LANGTMESS456","Ausstehend");
define("LANGTMESS457","Akzeptiert");
define("LANGTMESS458","Abgelehnt");
define("LANGTMESS459","Entscheidung");
define("LANGTMESS460","Liste in ".INTITULECLASSE." übertragen");
define("LANGTMESS461","Datenblatt(e) vernichten");
define("LANGTMESS462","Achtung!, die Hausordnung muss im PDF-Format sein und darf zwei Megabyte nicht überschreiten.");
define("LANGTMESS463","Diese Option ermöglicht es den ".INTITULEENSEIGNANT.", die Hausordnung bei ihrer ersten Anmeldung zu bestätigen.");
define("LANGTMESS464",ucfirst(INTITULEELEVE)."(s) insgesamt.");
define("LANGTMESS465","Kommentar für den");
define("LANGTMESS466","Teilbereiche anzeigen");
define("LANGTMESS467","Berücksichtigung Prüfungsnote");
define("LANGTMESS468","Berücksichtigung Koeffizient Null");
define("LANGTMESS469","Wenn der Koeffizient Null ist, werden Punkte über 10 berücksichtigt.");
define("LANGTMESS470","Spezif."); // Spezifisch
define("LANGTMESS471","Fallstudie");
define("LANGTMESS472","Ansicht: Anzeige im Zeugnis");
define("LANGTMESS473","für das Jahr:");
define("LANGTMESS474","ändern");
define("LANGTMESS475","Datei Max. Größe");
define("LANGTMESS476","Liste / Personalkonto ändern");
define("LANGTMESS477","Liste / Praktikumsbetreuer ändern");
define("LANGTMESS478","Über Barcode");
define("LANGTMESS479","Anwesende bestätigen");
define("LANGTMESS480","Visum Leitung");
define("LANGTMESS481","Kommentare für die ".INTITULEELEVES);


define("LANGTMESS482","AKTUELLES - TRIADE");
define("LANGTMESS483","nicht verfügbar");
define("LANGTMESS484","Ihre Verzeichnisse");
define("LANGTMESS485","Nachrichten an die Vertreter");
define("LANGTMESS486","Rundschreiben ändern");


define("LANGMESS430","Das ganze Jahr");
define("LANGMESS431","Mit Teilnoten Vatel "); // Vatel spezifisch?
define("LANGMESS432","Zeugnistyp");
define("LANGMESS433","Speicherung per Barcode");
define("LANGMESS434","Anwesende bestätigen");
define("LANGMESS435","Schriftverkehr");
define("LANGMESS436","Übersichten ohne Abw., noch Versp.");
define("LANGMESS437","Auflistung der Abwesenheiten");
define("LANGMESS438","Abwesenheiten pro Woche");
define("LANGMESS439","Abwesenheiten / Verspätungen drucken");
define("LANGMESS440","Liste der Anwesenden");
define("LANGMESS441","Verwaltung Abw./Versp. über Sconet"); // Sconet spezifisch?
define("LANGMESS442","Statistiken Abw. / Versp. ");
define("LANGMESS443","Verwaltung der Abwesenheiten und Verspätungen eines ".INTITULEELEVE."s");
define("LANGMESS444","Planen&nbsp;");
define("LANGMESS445","&nbsp;Ansehen&nbsp;/&nbsp;Ändern&nbsp;");
define("LANGMESS446","&nbsp;Löschen&nbsp;");
define("LANGMESS447","Zugreifen");
define("LANGMESS448","&nbsp;Abw.&nbsp;umwandeln&nbsp;");
define("LANGMESS449","Konfiguration");
define("LANGMESS450","Warnmeldungen verwalten");
define("LANGMESS451","Konfiguration Zeitfenster ");
define("LANGMESS452","Konfiguration SMS ");
define("LANGMESS453","SMS gutschreiben");

define("LANGTMESS487","Mit Noten Schulleben");
define("LANGTMESS488","Nachprüfungen nicht validiert");

define("LANGTRONBI30","Fotoliste des Personals anzeigen");
define("LANGTRONBI20","Fotoliste des Personals ändern");

define("LANGSEXEF","W"); // F -> Weiblich
define("LANGSEXEH","M"); // H -> Männlich (Homme)
define("LANGHOM","Mann");
define("LANGFEM","Frau");

define("LANGTMESS489","Stundenplan duplizieren");
define("LANGTMESS490","Stundenplan einer ".INTITULECLASSE." zu einer anderen duplizieren");
define("LANGTMESS491","Zu kopierender Zeitraum");
define("LANGTMESS492","Import des Leitungspersonals: ");
define("LANGTMESS493","Import der Personalkonten: ");
define("LANGTMESS494","Import der Unternehmen: ");
define("LANGTMESS495","Import Spezif. IPAC: "); // IPAC spezifisch?
define("LANGTMESS496","Import der Fächer: ");
define("LANGTMESS497","Dateiimportmodul: ");
define("LANGTMESS498","Excel-Dateiimportmodul ");
define("LANGTMESS499","Die zu übertragende Excel-Datei MUSS 4 Felder enthalten");
define("LANGTMESS500","Beispiel XLS-Datei");
define("LANGTMESS501","Anzahl hinzugefügter Fächer: ");
define("LANGTMESS502","Quartalsdaten");
define("LANGTMESS503","Ihr Zugang ist derzeit deaktiviert.");


define("LANGTMESS504","Passwort per E-Mail senden");
define("TITREACC1","Eltern");
define("TITREACC2",ucfirst(INTITULEENSEIGNANT));
define("TITREACC3","Schulleben");
define("TITREACC4","Praktikumsbetreuer");
define("TITREACC5","Personal");
define("LANGTMESS505","Vorherige Klassen");
define("LANGTMESS506","Spezialisierung");

define("LANGTMESS507","Ausgabe Diplomzusatz");
define("LANGTMESS508","Konfiguration Diplomzusatz");
define("LANGTMESS509","Prüfungsverwaltung");
define("LANGTMESS510","Wahl des Dokuments:");
define("LANGTMESS511","ZIP-Datei Diplomzusätze abrufen");
define("LANGTMESS512","Schulniveau");
define("LANGTMESS513","Serienbrief der Unternehmen ");
define("LANGTMESS514","Import der Unternehmen");
define("LANGTMESS515","Praktikumsvergütung");
define("LANGTMESS516","Verfolgung der Vertragsanfragen");
define("LANGTMESS517","Verwaltung Diplomzusatz");
define("LANGTMESS518","Bezeichnung:");
define("LANGTMESS519","Datei");


define("LANGTMESS520","Name des Praktikums");
define("LANGTMESS521","Im Unternehmen am: ");
define("LANGTMESS522","Land");
define("LANGTMESS523","Hotelgruppe");
define("LANGTMESS524","Anzahl Sterne");
define("LANGTMESS525","Anzahl Zimmer");
define("LANGTMESS526","Website");
define("LANGTMESS527","Zuweisung mehrerer Studenten zu einem Praktikum");
define("LANGSTAGE100","Name");
define("LANGSTAGE101","Prakt.-Nr.");
define("LANGSTAGE102","Unternehmen");
define("LANGSTAGE103","Abteilung");
define("LANGSTAGE104","Vergütung");
define("LANGSTAGE105","Untergebracht");
define("LANGSTAGE106","Verpflegt");
define("LANGSTAGE107","Bestätigen");
define("LANGSTAGE108","Personalisiertes Praktikum");
define("LANGSTAGE109","Land");
define("LANGSTAGE110","Praktikumsbetreuer");
define("LANGSTAGE111","Während des Praktikums gesprochene Sprache");
define("LANGSTAGE112","Bezeichnung der Abteilung");
define("LANGSTAGE113","Praktikumsvergütungen");
define("LANGSTAGE114","Tägliche Arbeitszeiten");
define("LANGSTAGE115","Die Praktikumsverträge");
define("LANGSTAGE116","Ausgabe der gesammelten Verträge");

define("LANGTMESS528","Sprache der ".INTITULECLASSE);
define("LANGTMESS529","Zurück ".INTITULECLASSE);
define("LANGTMESS530","Abruf der Praktikumsverträge");


define("LANGVATEL1","Abmelden");
define("LANGVATEL2","Anmelden");
define("LANGVATEL3","Passwort vergessen");
define("LANGVATEL4","Gib deine E-Mail ein");
define("LANGVATEL5","Gib dein Passwort ein");
define("LANGVATEL6","Halbjahr");
define("LANGVATEL7","Abw./Versp./Sanktion");
define("LANGVATEL8","Abwesenheiten / Verspätungen / Sanktionen");
define("LANGVATEL9","Abwesenheiten");
define("LANGVATEL10","Verspätungen");
define("LANGVATEL11","Sanktionen");
define("LANGVATEL12","Beschreibung der Fakten");
define("LANGVATEL13","<");
define("LANGVATEL14",">");
define("LANGVATEL15","Monat");
define("LANGVATEL16","Ihr Passwort zurücksetzen");
define("LANGVATEL17","Passwort vergessen?");

define("LANGVATEL18","Zugang ".ucfirst(INTITULEELEVE));
define("LANGVATEL19","Zugang ".ucfirst(INTITULEENSEIGNANT));
define("LANGVATEL20","Zugang ".ucfirst(INTITULEDIRECTION));

define("LANGVATEL21","Hinzufügen");
define("LANGVATEL22","Ändern");
define("LANGVATEL23","Löschen");
define("LANGVATEL24","Anzeigen");
define("LANGVATEL25","Was gibt's Neues?");
define("LANGVATEL26","Noten");
define("LANGVATEL27","Statistiken dieser Arbeit");
define("LANGVATEL28","UNMÖGLICH");
define("LANGVATEL29","Halbjahr bereits vergangen.");
define("LANGVATEL30",INTITULEELEVE." hinzufügen");
define("LANGVATEL31","Einem ".INTITULEELEVE." eine Note für diese Arbeit hinzufügen.");
define("LANGVATEL32","Zurück zur Aufgabenliste");
define("LANGVATEL33","Stundenplan");
define("LANGVATEL34","Abwesenheit");
define("LANGVATEL35","abwesend(e) unterzeichnet");
define("LANGVATEL36","Kalender");
define("LANGVATEL37","Speicherproblem");
define("LANGVATEL38","Datum angeben");


define("LANGVATEL39","Startseite");
define("LANGVATEL40","Wahl des ".INTITULEENSEIGNANT."s");
define("LANGVATEL41","für den ".INTITULEENSEIGNANT);
define("LANGVATEL42",ucfirst(INTITULEENSEIGNANT)." dieser Arbeit zugewiesen");
define("LANGVATEL43","Abwesenheiten oder Verspätungen in ".INTITULECLASSE." von");
define("LANGVATEL44","Andere Abwesenheiten");
define("LANGVATEL45","Andere Abwesenheiten für dieselbe ".INTITULECLASSE);
define("LANGVATEL46","Gemeldet am ");
define("LANGVATEL47","Verwaltung Abwesenheiten / Verspätungen");
define("LANGVATEL48","Per Nachricht benachrichtigen ");
define("LANGVATEL49","Tabellenaktualisierung");
define("LANGVATEL50","Diese ".INTITULECLASSE." kann nicht gelöscht werden");
define("LANGVATEL51","Klasse nicht löschbar");
define("LANGVATEL52","Klasse zugewiesen");
define("LANGVATEL53","Diese ".INTITULECLASSE." löschen");
define("LANGVATEL54","Dieses Fach löschen");
define("LANGVATEL55","Dieses Fach kann nicht gelöscht werden");
define("LANGVATEL56","Fach einer ".INTITULECLASSE." zugewiesen");
define("LANGVATEL57","Wenn kein Vorname, 'unbekannt' angeben ");
define("LANGVATEL58","Erstellung eines Verwaltungskontos");

define("LANGVATEL59","Liste der Praktikumsbetreuer");
define("LANGVATEL60","Liste der ".INTITULEENSEIGNANT);
define("LANGVATEL61","Liste des Verwaltungspersonals");
define("LANGVATEL62","Liste der Mitglieder des Schullebens");
define("LANGVATEL63","Hausordnung");
define("LANGVATEL64","Klassen");
define("LANGVATEL65","Verwaltungspersonal");
define("LANGVATEL66","Praktikumsbetreuer");
define("LANGVATEL67","Hausordnung nicht gespeichert");
define("LANGVATEL68","Die Datei muss im PDF-Format sein und kleiner als 2MB");
define("LANGVATEL69","Menü");
define("LANGVATEL70","Zugriff auf PDF der ".INTITULECLASSE);
define("LANGVATEL71","Zugriff auf PDF des Status"); // Régime
define("LANGVATEL72","Im PDF-Format drucken");
define("LANGVATEL73","Foto ändern von ");
define("LANGVATEL74","Gruppen");
define("LANGVATEL75","Erstellung einer Gruppe");
define("LANGVATEL76","Diese Liste ansehen");
define("LANGVATEL77","kein Student");
define("LANGVATEL78","Diese Gruppe ändern");
define("LANGVATEL79","Gruppenverwaltung");
define("LANGVATEL80","Gruppe NICHT gelöscht");
define("LANGVATEL81","Gruppe gelöscht");
define("LANGVATEL82","Die Gruppe ist derzeit zugewiesen.\\n\\n Kann nicht gelöscht werden.\\n\\n Ändern Sie die Zuweisung, bevor Sie diese Gruppe löschen.");
define("LANGVATEL83","Gruppe bereits erstellt.");


define("LANGVATEL84","Zeugniskonfiguration");
define("LANGVATEL85","Schulkonfiguration");
define("LANGVATEL86","Einrichtung der Zuweisungen");
define("LANGVATEL87","Änderung der Zuweisungen");
define("LANGVATEL88","Löschung der Zuweisungen");
define("LANGVATEL89","Lehreinheit");
define("LANGVATEL90","Konfiguration der Abwesenheiten");
define("LANGVATEL91","Konfiguration der Schulbescheinigungen");
define("LANGVATEL92","Konfiguration des Zusatzes");
define("LANGVATEL93","Geben Sie den Tag und Monat des Beginns Ihres Schuljahres an: ");
define("LANGVATEL94","Geben Sie den Tag und Monat des Endes Ihres Schuljahres an: ");
define("LANGVATEL95","Eingabefehler bei Ihren angegebenen Tagen oder Monaten");
define("LANGVATEL96","Schuljahr angeben");
define("LANGVATEL97","WICHTIG, DIE ERSTELLUNG EINER ZUWEISUNG LÖSCHT ALLE BEWERTUNGSINFORMATIONEN DER NEUEN BETROFFENEN KLASSE!!");
define("LANGVATEL98","Zuweisung kopieren");
define("LANGVATEL99","KOPIERFEHLER");
define("LANGVATEL100","des Schuljahres");
define("LANGVATEL101","WICHTIG, DAS KOPIEREN EINER ZUWEISUNG LÖSCHT ALLE BEWERTUNGSINFORMATIONEN DER NEUEN BETROFFENEN KLASSE!!");
define("LANGVATEL102","Zuweisung der ".INTITULECLASSE." kopieren ");
define("LANGVATEL103","Fallstudie");
define("LANGVATEL104","Schulnoten dieser ".INTITULECLASSE." löschen.");
define("LANGVATEL105","* Ansicht: Im Zeugnis anzeigen / ** Jährliche Stundenzahl / *** Ansicht: Im Zeugnis AFTEC BTS BLANC anzeigen"); // AFTEC BTS BLANC spezifisch?
define("LANGVATEL106",INTITULEENSEIGNANT." angeben");
define("LANGVATEL107","Koeffizient des Fachs angeben");
define("LANGVATEL108","Einen numerischen Wert angeben");
define("LANGVATEL109","Zeile per Drag & Drop verschieben");
define("LANGVATEL110","klicken/verschieben");
define("LANGVATEL111","auf die entsprechende Nr.");
define("LANGVATEL112","Lehreinheit kopieren");
define("LANGVATEL113","Liste der Lehreinheiten");
define("LANGVATEL114","ACHTUNG!! ZUWEISUNGEN DER KLASSE AKTUALISIEREN ");
define("LANGVATEL115"," AUF DEN DATEN LEHREINHEIT ");
define("LANGVATEL116"," im Zeugnis von null bis n ");
define("LANGVATEL117","Löschung bestätigen");
define("LANGVATEL118","Sind Sie sicher, dass Sie die folgende Lehreinheit löschen möchten?");
define("LANGVATEL119","Löschung durchgeführt");
define("LANGVATEL120","Konfig. Zeitfenster");
define("LANGVATEL121","Konfig. der Gründe");
define("LANGVATEL122","Name des Zeitfensters");
define("LANGVATEL123","Startzeit");
define("LANGVATEL124","Endzeit");
define("LANGVATEL125","Bezeichnung des Zeitfensters");
define("LANGVATEL126","Zeitfenster speichern");
define("LANGVATEL127","Standardzeitfenster");
define("LANGVATEL128","Zertifikat Nummer");
define("LANGVATEL129","Zertifikatskonfiguration");
define("LANGVATEL130","Ein Zertifikat importieren");
define("LANGVATEL131","Speicherfehler");
define("LANGVATEL132","Aktuelles Zertifikat");
define("LANGVATEL133","Konfiguration der Schlüsselwörter");

define("LANGVATEL134","Speicherfehler");
define("LANGVATEL135","Fehler: Datei nicht erkannt ");
define("LANGVATEL136","Fehler: Datei größer als 8 MB");
define("LANGVATEL137","Datei NICHT gespeichert");
define("LANGVATEL138","Ausgaben / Listen");
define("LANGVATEL139","Ausgaben pro ".INTITULECLASSE);
define("LANGVATEL140","Studentenliste");
define("LANGVATEL150","Dashboard aller ".INTITULECLASSE."n.");
define("LANGVATEL151"," ".ucfirst(INTITULEELEVE)."(s) insgesamt. Schuljahr: ");
define("LANGVATEL152"," ".ucfirst(INTITULEELEVE)."(s) insgesamt ");
define("LANGVATEL153","Anwesenheitslisten");
define("LANGVATEL154","Kein Unterricht im Stundenplan definiert.");
define("LANGVATEL155","Anfangszeit");
define("LANGVATEL156","Endzeit");
define("LANGVATEL157","Bezeichnung des Kurses");
define("LANGVATEL158","Liste der ".INTITULEENSEIGNANT);
define("LANGVATEL159","Fächerliste");
define("LANGVATEL160","Ausgabe der Schulbescheinigungen");
define("LANGVATEL161","Dokumente der Schulbescheinigungen");
define("LANGVATEL162","Abruf der Zertifikate im ZIP-Format");
define("LANGVATEL163","Gesprächsliste");
define("LANGVATEL164","Ausgabe Etiketten Studenten");
define("LANGVATEL165","Ausgabe Etiketten Eltern");
define("LANGVATEL166","Abruf des Serienbriefdokuments");
define("LANGVATEL167","Import / Export");
define("LANGVATEL168","Studenten importieren");
define("LANGVATEL169",INTITULEENSEIGNANT." importieren");
define("LANGVATEL170","Leitungspersonal importieren");
define("LANGVATEL171","Unternehmen importieren");
define("LANGVATEL172","Studenten exportieren");
define("LANGVATEL173",INTITULEENSEIGNANT." exportieren");
define("LANGVATEL174","Leitungspersonal exportieren");

define("LANGVATEL175","Adresse ".INTITULEELEVE);
define("LANGVATEL176","Gemeinde ".INTITULEELEVE);
define("LANGVATEL177","PLZ ".INTITULEELEVE); // CCP (Code Postal et Commune)
define("LANGVATEL178","Festnetz ".INTITULEELEVE);
define("LANGVATEL179","Stipendiat");
define("LANGVATEL180","Universitäts-E-Mail");
define("LANGVATEL181","Geschlecht ".INTITULEELEVE);
define("LANGVATEL182","Passwort Betreuer 2");
define("LANGVATEL183","Möglicher Status"); // Régime
define("LANGVATEL184","Mögliche Anrede");
define("LANGVATEL185","Die zu übertragende Datei MUSS 47 Felder enthalten");
define("LANGVATEL186","Beispiel XLS-Datei");
define("LANGVATEL187","Erste Zeile der Datei übernehmen ");
define("LANGVATEL188","Eine Aktualisierung durchführen ");
define("LANGVATEL189","Leere Felder der Datei berücksichtigen");
define("LANGVATEL190","Ein neues Passwort für bereits eingeschriebene ".INTITULEELEVE." zuweisen");
define("LANGVATEL191","Keine Archivierung möglich");
define("LANGVATEL192","Achtung, die Löschung der ".INTITULEELEVE." löscht alle Archive!!");
define("LANGVATEL193","Import für das folgende Schuljahr: ");
define("LANGVATEL194","FEHLER KLASSE NICHT ERSTELLT -- Triade Dienst");
define("LANGVATEL195","Die zu übertragende Datei MUSS 9 Felder enthalten");
define("LANGVATEL196","FEHLER beim Passwort der Person");
define("LANGVATEL197","Andere Spalten hinzufügen");
define("LANGVATEL198","Anz. zusätzlicher Spalte(n)");
define("LANGVATEL199","Zu exportierende Daten angeben");
define("LANGVATEL200","Struktur speichern");
define("LANGVATEL201","Wenn Sie die Exportstruktur speichern möchten, rufen Sie zuerst Ihre Excel-Datei ab und klicken Sie dann auf die Schaltfläche \"Struktur speichern\"");
define("LANGVATEL202","Name der Struktur");
define("LANGVATEL203","Abruf des Exports");
define("LANGVATEL204","Reihenfolge der Spalten in Ihrer Excel-Datei angeben");
define("LANGVATEL205","Schulzeugnis");

define("LANGVATEL206","Zeugnis / Diplomzusatz");
define("LANGVATEL207","Beurteilungen der Leitung");
define("LANGVATEL208","Beurteilungen der ".INTITULECLASSE);
define("LANGVATEL209","Notenausgaben");
define("LANGVATEL210","Ausgabe der Schulzeugnisse");
define("LANGVATEL211","Ausgabe Bachelor- / Master-Zusatz");
define("LANGVATEL212","Gespeicherte Kommentare");
define("LANGVATEL213","Überprüfen Sie Ihre Zuweisungen für diese ".INTITULECLASSE);
define("LANGVATEL214","Verwaltung der Praktikumsdaten");
define("LANGVATEL215","Unternehmensverwaltung");
define("LANGVATEL216","Zuweisung der Studenten zu Praktika");
define("LANGVATEL217","Liste der Studenten, die sich derzeit im Unternehmen befinden");
define("LANGVATEL218","Ausgabe der Verträge");
define("LANGVATEL219","Einen Zeitraum hinzufügen");
define("LANGVATEL220","Liste der Zeiträume");
define("LANGVATEL221","Das Enddatum des Praktikums darf nicht vor dem Anfangsdatum liegen");
define("LANGVATEL222","Einen Zeitraum ändern");
define("LANGVATEL223","Einen Zeitraum löschen");
define("LANGVATEL224","Löschung aller Daten, die keinem Studenten zugewiesen sind");
define("LANGVATEL225","Auflisten");
define("LANGVATEL226","Praktikumsverwaltung");
define("LANGVATEL227","Liste der Unternehmen drucken");
define("LANGVATEL228","Anz. ".INTITULEELEVE.", die ein Praktikum absolviert haben");
define("LANGVATEL229","Plan");
define("LANGVATEL230","Verlauf der ".INTITULEELEVE);
define("LANGVATEL231","Abruf der PDF-Datei");
define("LANGVATEL232","Adresse / PLZ / Stadt");
define("LANGVATEL233","Auflistung der Unternehmen vom ");
define("LANGVATEL234","Zuweisung mehrerer Studenten zu einem Praktikum");
define("LANGVATEL235","Zuweisung eines Studenten zu einem Praktikum");
define("LANGVATEL236","Beginn");
define("LANGVATEL237","Ende");
define("LANGVATEL238","Für den Zeitraum: Halbjahr / Quartal");
define("LANGVATEL239","Gewünschter Zeitraum");
define("LANGVATEL240","Nummer des Praktikums oder des personalisierten Praktikums angeben");
define("LANGVATEL241","Vollständige Liste drucken");

define("LANGVATEL242","Ausgabe der Zuweisungen");
define("LANGVATEL243","Andere ".INTITULECLASSE);
define("LANGVATEL244","Einrichtung des Stundenplans");


define("LANGVATEL245","Bitte wählen Sie den gewünschten Kontotyp");
define("LANGVATEL246","Tabellenaktualisierung");
define("LANGVATEL247","Verwaltung Abwesenheiten / Verspätungen");
define("LANGVATEL248","Eine Abwesenheit oder Verspätung hinzufügen");
define("LANGVATEL249","Für das Personal");
define("LANGVATEL250","Für das Schulleben");
define("LANGVATEL251","Für Praktikumsbetreuer");
define("LANGVATEL252","Für die Leitung");
define("LANGVATEL253","die oder die ".INTITULECLASSE."(n)");
define("LANGVATEL254","Parametrierung");
define("LANGVATEL255","Einrichtung des Stundenplans");

define("LANGVATEL256","Liste der Eltern von Studenten angeben, die eine E-Mail erhalten sollen");
define("LANGVATEL257","Keine Nummer");
define("LANGVATEL258","SMS-Versand bestätigen");
define("LANGVATEL259","Mob. ".ucfirst(INTITULEELEVE)." ");
define("LANGVATEL260","Mobil ");
define("LANGVATEL261","Tel. Berufl. Mutter ");
define("LANGVATEL262","Tel. Berufl. Vater ");
define("LANGVATEL263","Videoprojektor ");
define("LANGVATEL264","Detail ABS/Rtd ");
define("LANGVATEL265","Zeitfenster ");
define("LANGVATEL266","nicht angegeben ");
define("LANGVATEL267",INTITULEENSEIGNANT." auflisten / ändern ");
define("LANGVATEL268","Ein Konto löschen ");
define("LANGVATEL269","Abw./Versp. Student ");
define("LANGVATEL270","Abwesenheiten und Verspätungen eines Studenten");
define("LANGVATEL271","Erstellung unmöglich, Schuljahr nicht angegeben.");
define("LANGVATEL272","Neue Lehreinheit erstellt.");
define("LANGVATEL273","Fotoliste");


define("LANGVATEL274","SMS-Versand für Abwesenheiten seit ");

define("LANGVATEL275","NBETUDIANTS => Anzahl Studenten<br />
 HISTOETUDIANT => Werdegang des Studenten<br />
 NOMETUDIANT => Name des Studenten<br>
 PREETUDIANT => Vorname des Studenten<br>
 DATENAISETUDIANT => Geburtsdatum des Studenten<br>
 IDENTETUDIANT => Identifikationscode des Studenten<br>
 NOMETABLISSEMENT => Name der Einrichtung des Studenten<br>
 DATEDUJOUR => Heutiges Datum<br>
 LANGUEETUDIANT => Unterrichtssprache<br>
 NBRETUDIANTPA1 => Anzahl Studenten M4 und PREPA für Titel 1 <br>
 NBRETUDIANTPA2 => Anzahl Studenten im ersten Jahr für Titel 2 <br>
 NBRETUDIANTPREPA => Anzahl Studenten in Prepa  <br>
 NBRETUDIANTM4 => Anzahl Studenten in M4 für Titel 1 <br>
 SPECIALISATION => Spezialisierung der ".INTITULECLASSE." <br>
 NOMDIRECTEUR => Name des Direktors der Einrichtung <br>
 NOMCLASSELONG => Name der ".INTITULECLASSE." im Langformat <br>");

define("LANGVATEL276","Listen der erstellten Zeugniskommentare.");
define("LANGVATEL277","Kommentare der ".INTITULECLASSE." nach Fach einsehen.");
define("LANGVATEL278","Anzahl durchgeführt");
define("LANGVATEL279","Durchführungsrate");
define("LANGVATEL280","Gemeldet");
define("LANGVATEL281","Nachricht senden");
define("LANGVATEL282","Kommentar(e) gespeichert auf ");
define("LANGVATEL283","Alter");
define("LANGVATEL284","Jahre");
define("LANGVATEL285","Auflistung der Abwesenheiten dieses Zeitraums.");
define("LANGVATEL286","Anz.&nbsp;Abwesenheiten&nbsp;");
define("LANGVATEL287","Auflistung der Verspätungen dieses Zeitraums.");
define("LANGVATEL288","Grafiken");
define("LANGVATEL289","Kommentar der Leitung.");
define("LANGVATEL290","Kommentar des Klassenlehrers.");
define("LANGVATEL291","Die Kommentare der ".INTITULEENSEIGNANT." sind auf dem Durchschnitt des ".INTITULEELEVE."s verfügbar.");
define("LANGVATEL292","Zeugnisarchiv");

define("LANGVATEL293","Zugangskontrolle");
define("LANGVATEL294","Wechsel der ".INTITULECLASSE);
define("LANGVATEL295","Aktuelles Schuljahr der Studenten angeben");
define("LANGVATEL296","Zukünftiges Schuljahr für die Studenten angeben ");
define("LANGVATEL297","Aktuelles Schuljahr des Studenten angeben ");
define("LANGVATEL298","Neues Schuljahr angeben ");
define("LANGVATEL299","Geboren am ");
define("LANGVATEL300","Studentenblatt");
define("LANGVATEL301","Abwesenheitsalarm");
define("LANGVATEL302","SMS-Alarm");
define("LANGVATEL303","SMS-Alarm"); // Doppelt?


define("LANGNEW100","Sanktion(en)");
define("LANGNEW101","Vorausschau auf ");

define("LANGTT2","Impression du tableau de bulletin");


// --- SIECLE-BEE Export ---
define('LANG_SIECLE_EXPORT_TITRE', 'SIECLE-BEE Export (XML / ZIP)');
define('LANG_SIECLE_EXPORT_DESC', 'Erstellung des standardkonformen ZIP-Archivs für den Import in SIECLE-BEE.');
define('LANG_SIECLE_PROFIL', 'SIECLE-Schemaprofil');
define('LANG_SIECLE_PROFIL_STANDARD', 'Standard (Vertragsschulen - XSD 4.0)');
define('LANG_SIECLE_PROFIL_EPHC', 'Privatschulen ohne Vertrag (EPHC - XSD 1.1)');
define('LANG_SIECLE_UAI', 'UAI-Code (RNE) der Schule');
define('LANG_SIECLE_ANNEE', 'Schuljahr');
define('LANG_SIECLE_VALIDATION_XSD', 'XSD-Konformität vor dem Export prüfen');
define('LANG_SIECLE_PERIMETRE', 'Exportumfang');
define('LANG_SIECLE_BTN_EXPORTER', 'ZIP-Archiv generieren und herunterladen');

define('LANG_CODE_MEF', 'MEF-Code (SIECLE)');

// --- Passwortänderung (Privater Bereich) ---
define('LANG_CHG_PASS_TITLE', 'Passwort');
define('LANG_CHG_PASS_ACTUEL', 'Aktuelles Passwort');
define('LANG_CHG_PASS_NOUVEAU', 'Neues Passwort');
define('LANG_CHG_PASS_CONFIRM', 'Neues Passwort bestätigen');
define('LANG_CHG_PASS_BTN', 'Passwort ändern');
define('LANG_CHG_PASS_OK', 'Ihr Passwort wurde erfolgreich geändert.');
define('LANG_CHG_PASS_ERR_ACTUEL', 'Das aktuelle Passwort ist falsch.');
define('LANG_CHG_PASS_ERR_CONFIRM', 'Das neue Passwort und die Bestätigung stimmen nicht überein.');
define('LANG_CHG_PASS_ERR_EMPTY', 'Bitte füllen Sie alle Passwortfelder aus.');
define('LANG_CHG_PASS_ERR_SECURITY', 'Das neue Passwort entspricht nicht den Sicherheitskriterien der Einrichtung.');
define('LANG_CHG_PASS_DISABLED', 'Die Passwortänderung wurde von Ihrer Einrichtung deaktiviert.');
define('LANG_CHG_PASS_STRENGTH_LABEL', 'Passwortstärke:');
define('LANG_CHG_PASS_STRENGTH_1', 'Sehr schwach');
define('LANG_CHG_PASS_STRENGTH_2', 'Schwach');
define('LANG_CHG_PASS_STRENGTH_3', 'Mittel');
define('LANG_CHG_PASS_STRENGTH_4', 'Stark');
define('LANG_CHG_PASS_MAIL_SUBJECT', 'TRIADE: Bestätigung der Passwortänderung');
define('LANG_CHG_PASS_MAIL_BODY1', 'Guten Tag');
define('LANG_CHG_PASS_MAIL_BODY2', 'Wir bestätigen Ihnen, dass das Passwort Ihres TRIADE-Kontos erfolgreich geändert wurde am');
define('LANG_CHG_PASS_MAIL_BODY3', 'um');
define('LANG_CHG_PASS_MAIL_BODY4', 'Wenn Sie diese Änderung nicht veranlasst haben, wenden Sie sich bitte unverzüglich an die Leitung Ihrer Einrichtung.');
?>
