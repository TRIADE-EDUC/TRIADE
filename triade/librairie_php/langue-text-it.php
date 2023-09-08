<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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

if (!defined(INTITULEDIRECTION)) { define("INTITULEDIRECTION","direction"); }
if (!defined(INTITULEELEVE)) { define("INTITULEELEVE","élève"); }
if (!defined(INTITULEELEVES)) { define("INTITULEELEVES","élèves"); }


// file per lingua lato admin.
// PER TUTTI -------------------
// brmozilla($_SESSION[navigateur]);
define("CLICKICI","Cliccate qui");
define("VALIDER","Confermare");
define("LANGTP22","INFORMAZIONE - Richiesta di C.D.G. da confermare !");
define("LANGTP3"," calendario CDG ");
define("LANGCHOIX","Scelta ...");
define("LANGCHOIX2","nessuna classe");
define("LANGCHOIX3","--- Scelta ---");
define("LANGOUI","si");
define("LANGNON","no");
define("LANGFERMERFEN","Chiudere la finestra");
define("LANGATT","ATTENZIONE !");
define("LANGDONENR","Dati registrati");
define("LANGPATIENT","Grazie per l\'attesa");
define("LANGSTAGE1",'Gestione degli stage professionali');
define("LANGINCONNU",'sconosciuto'); // doit être identique que langinconnu cote javascript
define("LANGABS",'ass');
define("LANGRTD",'rtd');
define("LANGRIEN",'niente');
define("LANGENR",'Registrare');
define("LANGRAS1",'Oggi, il ');
define("LANGDATEFORMAT",'gg/mm/aaaa');

//------------------------------
// titolo
//-------------------------------

define("LANGTITRE3","Messaggio scorrevole in alto sulla pagina");
define("LANGTITRE4","Messaggio scorrevole nello spazio logo sito web ");
define("LANGTITRE5","Ricezione messaggio");
define("LANGTITRE6","Creazone di un conto direzione");
define("LANGTITRE7","Creazione di un conto vita scolastica");
define("LANGTITRE8","Creazione di un conto docente");
define("LANGTITRE9","Creazione di un conto supplente");
define("LANGTITRE10","Creazione di un conto allievo");
define("LANGTITRE11","Creazione di un gruppo"); //
define("LANGTITRE12","Creazione di una classe"); //
define("LANGTITRE13","Creazione di una materia"); //
define("LANGTITRE14","Création di una sotto materia"); //
define("LANGTITRE16","Creazione di appartenenza");
define("LANGTITRE17","Creazione di appartenza della classe");
define("LANGTITRE18","Visualizzazione di appartenenza");
define("LANGTITRE19","Modifica di appartenenza");
define("LANGTITRE20","Modifica di appartenenza della classe");
define("LANGTITRE21","Eliminazione di appartenenza");
define("LANGTITRE22","Importazione di un file ASCII (txt,csv) ");
define("LANGTITRE23","Lista dei ritardi non giustificati ");
define("LANGTITRE24","Aggiungere una dispensa");
define("LANGTITRE25","Elenco / Modificare le dispense");
define("LANGTITRE26","Eliminare una dispensa");
define("LANGTITRE27","Gestione dispense -  Planificazione");
define("LANGTITRE28","Visualizzazione / Modifica delle dispense");
define("LANGTITRE29","Consultazione delle classi");
define("LANGTITRE30","Ricerca di un allievo");
define("LANGTITRE31","Importare un file GEP");
define("LANGTITRE32","Descrizione degli allievi");
define("LANGTITRE33","Certificato di frequenza scolastica");

//------------------------------
define("LANGTE1","Titolo");
define("LANGTE2","del");
define("LANGTE3","di");
define("LANGTE4","Numero dei caratteri");
define("LANGTE5","Oggetto");
define("LANGTE6","A");
define("LANGTE6bis","Ai genitori di ");
define("LANGTE7","Data");
define("LANGTE8","Eliminazione messaggi");
define("LANGTE9","letto");
define("LANGTE10","fino a :");
define("LANGTE11","al ");
define("LANGTE12","il ");
define("LANGTE13","a");
define("LANGTE14","Al gruppo ");

//------------------------------
define("LANGFETE","Buona Festa a ");
define("LANGFEN1","Evento(i) del giorno");
define("LANGFEN2","C.D.G. del giorno");
//------------------------------
define("LANGLUNDI","Lunedì");
define("LANGMARDI","Martedì");
define("LANGMERCREDI","Mercoledì");
define("LANGJEUDI","Giovedì");
define("LANGVENDREDI","Venerdì");
define("LANGSAMEDI","Sabato");
define("LANGDIMANCHE","Domenica");
// ------------------------------
define("LANGMESS1","Invio del messaggio - il ");
define("LANGMESS3","Invio messaggio al docente di classe : ");
define("LANGMESS4","Invio messaggio a un docente : ");
define("LANGMESS6","Messaggio inviato");
define("LANGMESS7","Attualità registrata");
define("LANGMESS8","Messaggio inviato");
define("LANGMESS9","Rispondere al messaggio - il ");
define("LANGMESS10",'Le date trimestrali non sono state registrate.');
define("LANGMESS11",'Vogliate avvisare l\'amministrazione.');
define("LANGMESS12",'per convalidare le date trimestrali.');
define("LANGMESS13",'Vogliate cliccare <a href="definir_trimestre.php">qui</a>');
define("LANGMESS14",'Le appartenenze di questa classe non sono state registrate.');
define("LANGMESS15",'Vogliate cliccare <a href="affectation_creation_key.php">qui</a>');
define("LANGMESS16",'per convalidare le appartenenze di questa classe ');
define("LANGMESS17","Configurazione");
define("LANGMESS18","D");     // prima lettera della frase che segue !!!
define("LANGMESS18bis","ei diversi email da inviare,<br> separare le email con una virgola.");
define("LANGMESS19","Attivato");
define("LANGMESS20","Configurazione aggiornata");
define("LANGMESS21","Essere avvertiti quando si riceve un messaggio nel box della posta  ");
define("LANGMESS22","Inviare un messaggio a un gruppo : ");
define("LANGMESS23","Creazione di un gruppo per invio email ");
define("LANGMESS24","Indicare le persone del gruppo ");
define("LANGMESS25","Selezionare le diverse persone tenendo il tasto premuto"); //
define("LANGMESS26","Convalidare la creazione");
define("LANGMESS27","Gruppo email creato");
define("LANGMESS28","Elenco dei vostri gruppi email ");
define("LANGMESS29","Gruppo ");
define("LANGMESS30","Elenco delle persone ");
define("LANGMESS31","Messaggio di ");
define("LANGMESS32","Attualmente avete ");
define("LANGMESS33","messaggio(i) in attesa ");

// -----------------------------
// bouton
// PAS DE -->' (cote) !!!!
define("LANGBTS","Seguente >");
define("LANGBT1","Registrare l'msg scorrevole");
define("LANGBT2","Registrare info");
define("LANGBT3","Uscire senza inviare");
define("LANGBT4","Inviare il messaggio");
define("LANGBT5","Pazientare, P.F.");
define("LANGBT6","Eliminare i messaggi evidenziati");
define("LANGBT7","Registrare il conto");
define("LANGBT11","Elenco dei supplenti");
define("LANGBT12","Elenco dei gruppi");
define("LANGBT13","Confermare la o le classi");
define("LANGBT14","Registrare la creazione");
define("LANGBT15","Elenco delle classi");
define("LANGBT16","Elenco delle materie");
define("LANGBT17","Registrare le sotto-materie");
define("LANGBT18","Registrare lo statuto"); //
define("LANGBT19","Confermare"); //
define("LANGBT20","Uscire senza registrare"); //
define("LANGBT21","Registrare assegnazione"); //
define("LANGBT22","Eliminare assegnazione"); //
define("LANGBT23","Inviare il file"); //
define("LANGBT24","Ricominciare"); //
define("LANGBT25","Reload della pagina"); //
define("LANGBT26","Creare una classe"); //
define("LANGBT27","Pianificare ass. o rit."); //
define("LANGBT28","Consultare"); //
define("LANGBT29","Eliminare ass. o rit."); //
define("LANGBT30","Confermare l'aggiornamento"); //
define("LANGBT31","Confermare");
define("LANGBT32","Eliminare dispense");
define("LANGBT33","Modificare dispense");
define("LANGBT34","Aggiungere dispense");
define("LANGBT35","Registrare il dato di ");
define("LANGBT36","Dispensa  modificata --  L'équipe TRIADE");
define("LANGBT37","Transmettere info");
define("LANGBT38","Inviare");
define("LANGBT39","Lanciare la ricerca");
define("LANGBT40","Recupero");
define("LANGBT41","Terminato");
define("LANGBT42","Confermare gli allievi non registrati");
define("LANGBT43","Stampare i giudizi");
define("LANGBT44","Storico");
define("LANGBT45","Consultare la documentazione");
define("LANGBT46","Registrare la foto");
define("LANGBT47","Altro cambiamento");
define("LANGBT48","Lasciare il  modulo");
define("LANGBT49","Inserire tutta la classe");
define("LANGBT50","Eliminare");
define("LANGBT51","Confermare domanda C.D.G");
// -----------------------------
define("LANGCA1","M"); //
define("LANGCA1bis","essaggio non ancora letto"); // senza la prima lettera
define("LANGCA2","M"); //
define("LANGCA2bis","essaggio già letto"); // senza la prima lettera
define("LANGCA3","I"); //
define("LANGCA3bis","ndicate il GG/MM/AAAA  <BR> Nel caso di una data non <BR>corretta, precisate la menzione <br>"); // senza la prima lettera
// -----------------------------
define("LANGNA1","Cognome"); //
define("LANGNA2","Nome"); //
define("LANGNA3","Password"); //
define("LANGNA4","Nuovo conto creato \\n\\n L'équipe TRIADE "); //
define("LANGNA5","Sostituzione;di&nbsp;"); //
// -----------------------------
define("LANGELE1","Indicazioni allievo"); //
define("LANGELE2","Cognome"); //
define("LANGELE3","Nome"); //
define("LANGELE4","Classe"); //
define("LANGELE5","Opzione"); //
define("LANGELE6","Regime"); //
define("LANGELE7","Interno"); //
define("LANGELE8","Semi-interno"); //
define("LANGELE9","Esterno"); //
define("LANGELE10","Data di nascita"); //
define("LANGELE11","Nazionalità"); //
define("LANGELE12","Numero studente"); //
// define("LANGELE12","Numero nazionale"); //
define("LANGELE13","Indicazioni famigliari"); //
define("LANGELE14","Indirizzo 1"); //
define("LANGELE15","Codice postale"); //
define("LANGELE16","Comune"); //
define("LANGELE17","Indirizzo 2"); //
define("LANGELE18",""); //
define("LANGELE19",""); //
define("LANGELE20","Numero di telefono"); //
define("LANGELE21","Professione del padre"); //
define("LANGELE22","Telefono del padre"); //
define("LANGELE23","Professione della madre"); //
define("LANGELE24","Telefono della madre"); //
define("LANGELE25","Scuola precedente"); //
define("LANGELE26","Nome della Scuola"); //
define("LANGELE27","Identificativo scuola"); //
define("LANGELE28","Allievo creato -- L'équipe TRIADE"); //
define("LANGELE29","Allievo già esistente  -- L'équipe TRIADE"); //
//------------------------------------------------------------
define("LANGGRP1","Nome del gruppo"); //
define("LANGGRP2","Indicate le classi per la creazione del gruppo"); //
define("LANGGRP3","Selezionate le differenti classi tenendo schiacciato il tasto"); //
define("LANGGRP4","Ctrl"); //
define("LANGGRP5","e tenendo premuto il tasto sinistro del mouse."); //
define("LANGGRP6","Nome della sezione"); //
define("LANGGRP7","Nuova classe creata -- L'équipe TRIADE"); //
define("LANGGRP8","Nuova materia creata -- L'équipe TRIADE"); //
define("LANGGRP9","Nome della materia"); //
define("LANGGRP10","Nome della sotto materia"); //
//------------------------------------------------------------
//------------------------------------------------------------
define("LANGAFF1","Assegnazione per la classe"); //
define("LANGAFF2","!! La creazione di un assegnazione <u>elimina</u> tutte le note della classe !!</u>"); //
define("LANGAFF3","Assegnazione delle classi"); //
//------------------------------------------------------------
define("LANGPER1","Stampa del periodo"); //
define("LANGPER2","Inizio dedl periodo"); //
define("LANGPER3","Fine del periodo"); //
define("LANGPER4","Sezione"); //
define("LANGPER5","Ricuperare il file PDF"); //
define("LANGPER6","docente "); //
define("LANGPER8","nella classe di "); //
define("LANGPER9","Modulo d'assegnazione delle classi."); //
define("LANGPER10","ATTENZIONE questo modulo va utilizzato nel caso di una nuova assegnazione,<br> elimina tutte le note degli allievi delle classi assegnate."); //
define("LANGPER11","ATTENZIONE, le note delle classi selezionate saranno eliminate. \\n Volete continuare ? \\n\\n Equipe TRIADE"); //
define("LANGPER12","Indicate il codice di accesso.");
define("LANGPER13","Verifica del codice");
define("LANGPER14","Numero delle materie");
define("LANGPER15","Creazione nuova assegnazione per la classe");
define("LANGPER16","No");
define("LANGPER17","Materie");
define("LANGPER18","Docente");
define("LANGPER19","Coef");
define("LANGPER20","Gruppo");
define("LANGPER21","Lingua");
define("LANGPER22","Stampare questa pagina");
define("LANGPER23","assegnazione");
define("LANGPER23bis","riuscita");  // assegnazione xxxx riuscita
define("LANGPER24","interrotta"); // assegnazione xxxx interrotta
define("LANGPER25","Classe");
define("LANGPER26","Visualizzazione");
define("LANGPER27","Visualizzare");
define("LANGPER28","Visualizzazione dell'assegnazione per la classe");
define("LANGPER29","!! La modifica dell'assegnazione <u>elimina</u> tutte le note della classe !!");
define("LANGPER30","Modificare");
define("LANGPER31","Modificare l'assegnazione");
define("LANGPER32","Modificare l'assegnazione");
define("LANGPER32bis","interrotta"); // Modifica d'assegnazione xxxx interrotta
define("LANGPER33","Eliminazione dell'assegnazione per la ");
define("LANGPER34","!! L'eliminazione dell'assegnazione <u>elimina</u> tutte le note della classe !!</u>");
define("LANGPER35","Assegnazione della classe");
define("LANGPER35bis","eliminata"); // Assegnazione della classe  xxxx eliminata
//------------------------------------------------------------------------------
define("LANGIMP1","Importare una base dati esistente ");
define("LANGIMP2","Indicare il tipo di file da importare ");
define("LANGIMP3","File ASCII ");
define("LANGIMP4","File GEP ");
define("LANGIMP5","Modulo d'importazione del file ASCII.");
define("LANGIMP6","Il file da trasmettere <FONT color=RED><B>DEVE</B></FONT> contenere <FONT COLOR=red><B>45</B></FONT> ccampi <I>(vuoti o non vuoti)</I> sseparati da un medesimo separatore cioè l \"<FONT color=red><B>;</B></font>\" <I>Ciò significa la presenza di 44 volte il carattere \"<FONT color=red><B>;</B></font>\"</I>");
define("LANGIMP7","Ecco l'ordine dei campi da indicare : ");
define("LANGIMP8","Cognome");
define("LANGIMP9","Nome");
define("LANGIMP10","classe");
define("LANGIMP11","statuto");
define("LANGIMP12","data di nascita");
define("LANGIMP13","nazionalità");
define("LANGIMP14","Cognome tutor");
define("LANGIMP15","Nome tutor");

define("LANGIMP16","indirizzo;1");
define("LANGIMP18","codice postale;1");
define("LANGIMP19","comune;1");

define("LANGIMP17","indirizzo;2");
define("LANGIMP18_2","codice postale;2");
define("LANGIMP19_2","comune;2");


define("LANGIMP20","telefono");
define("LANGIMP21","professione padre");
define("LANGIMP22","telefono professione padre");
define("LANGIMP23","professione madre");
define("LANGIMP24","telefono professione madre");
define("LANGIMP25","identificativo scuola");

define("LANGIMP26","lv1");
define("LANGIMP27","lv2");
define("LANGIMP28","opzione");
define("LANGIMP29","Numero allievi");
define("LANGIMP30","ATTENZIONE, la distruzione della base sarà automatica. \\n Volete continuare ? \\n\\n L\'Equipe TRIADE");
define("LANGIMP31","ATTENZIONE : questo modulo va utilizzato alla primo utilizzo,<br> distrugge tutte le informazioi degi allievi (note, giudizi, comportamento scolastico).<br /> * campo obbligatorio");
define("LANGIMP39","Indicare il file da trasmettere ");
define("LANGIMP40","File trasmesso -- L'équipe TRIADE ");
define("LANGIMP41","Il numero dei campi non é rispettato ");
define("LANGIMP42","Indicare per ogni referenza la classe corrispondente ");
define("LANGIMP43","File non registrato ");
// ------------------------------------------------------------------------------
define("LANGABS1","Gestione assenze - ritardi del giorno");
define("LANGABS2","Pianificare un assenza o un ritardo");
define("LANGABS3","Indicare il Cognome dell'allievo");
define("LANGABS4","Elenco delle assenze o ritardi non giustificati");
define("LANGABS5","Elenco delle assenze non giustificate");
define("LANGABS6","Elenco dei ritardi non giustificati");
define("LANGABS7","Visualizzarre e/o modificare un'assenza o ritardo");
define("LANGABS8","Indicare il Cognomee dell'allievo");
define("LANGABS9","Visualizzare e/o eliminare un'assenza o ritardo");
define("LANGABS10","nessun allievo presente nel database");
define("LANGABS11","Ass/Rit");
define("LANGABS12","Motivo");
define("LANGABS13","In ritardo il");
define("LANGABS14","Rit");
define("LANGABS15","Ass");
define("LANGABS16","Annullare");
define("LANGABS17","Modificare ass. o rit.");
define("LANGABS18","Assente dal ;");
define("LANGABS19","al&nbsp;");
define("LANGABS20","Ass/Rit");
define("LANGABS21","Durata");
define("LANGABS22","Motivo");
define("LANGABS23","Ora / Data");
define("LANGABS24","Registrazione delle assenze o ritardi nella Classe di ");
define("LANGABS25","Gestione Assenze - Ritardi");
define("LANGABS26","Gestione Assenze - Ritardi  Pianificazione");
define("LANGABS27","Registrare i dati di ");
define("LANGABS28","Dato(i) Registrato(i) ");
define("LANGABS29","D"); //prima lettera
define("LANGABS29bis","ispensato(a) da :"); //segue
define("LANGABS30","Disp");
define("LANGABS31","classe di ");
define("LANGABS32","R"); //prima lettera
define("LANGABS32bis","itardo "); //segue
define("LANGABS33","in");
define("LANGABS34","di");
define("LANGABS35","Assenza - Ritardo - dispensa  dal ");
define("LANGABS36","Aggiornamento");
define("LANGABS37","Stampare le assenze, dispense, ritardi, del giorno ");
define("LANGABS38","Tel.");
define("LANGABS39","Tel. Prof Padre ");
define("LANGABS40","Tel. Prof Madre");
define("LANGABS41","Tel. Dom ");
define("LANGABS42","Assente  dal ");
define("LANGABS43","per ");
define("LANGABS44","Giorno(i) ");
define("LANGABS45","Registrare l'aggiornamento ");
define("LANGABS46","a partire da ");

define("LANGDISP8","Eliminazione dispense");
//----------------------------------------------------------------------------
define("LANGPROJ1","Scelta della classe");
define("LANGPROJ2","Scelta del trimestre");
define("LANGPROJ3","Trimestre 1");
define("LANGPROJ4","Trimestre 2");
define("LANGPROJ5","Trimestre 3");
define("LANGPROJ6","<font class=T2>Nessun allievo in questa classe</font>");
define("LANGPROJ7","Numero dei ritardi");
define("LANGPROJ8"," Cumulo");
define("LANGPROJ9","Disciplina");
define("LANGPROJ10","minuti");
define("LANGPROJ11","Numero delle punizioni");
define("LANGPROJ12","attribuito da ");
define("LANGPROJ13","Elenco");
define("LANGPROJ14","Media Allievo");
define("LANGPROJ15","Media Classe");
define("LANGPROJ16","Media Allievo");
// ----------------------------------------------------------------------------
define("LANGDISP1","<font class=T2>nessun allievo con questo cogCognomee</font>");
define("LANGDISP2","Motivo");
define("LANGDISP3","Certificato medico");
define("LANGDISP4","Periodo dal;");
define("LANGDISP5","in materia ");
define("LANGDISP6","Ora di dispensa ");
define("LANGDISP7","<B><font color=red>I</font></B>ndicate il GG/MM/AAAA  <BR> nei 2 campi");
define("LANGDISP9","Visualizzazione <b>completa</B> delle dispense");
define("LANGDISP10","In");
// ----------------------------------------------------------------------------
define("LANGASS1","TRIADE assistenza");
define("LANGASS2","Vi propone un  servizio per riparare, per aiutarvi nell'utilizzo di TRIADE.<br /><br />Se avete deo problemi con un servizio di TRIADE, nnon esitate a trasmettere con il formulario che segue, le info sul servizio in questione. I nostri ing. verificheranno il servizio.");
define("LANGASS3","Membro interessato");
define("LANGASS4","Amministrazione");
define("LANGASS5","docente");
define("LANGASS6","Andamento scolastico");
define("LANGASS6bis","Genitore");
define("LANGASS7","Azione");
define("LANGASS8","Creazione");
define("LANGASS9","Visualizzazione");
define("LANGASS10","Eliminazione");
define("LANGASS11","Altro");
define("LANGASS12","Servizio");
define("LANGASS13","Conto utente");
define("LANGASS14","Messaggeria");
define("LANGASS15","Assegnazione");
define("LANGASS16","Database");
define("LANGASS17","Classe");
define("LANGASS18","Materia");
define("LANGASS19","Ricerca");
define("LANGASS20","C.D.T.");
define("LANGASS21","Planning");
define("LANGASS22","Dispensa");
define("LANGASS23","Disciplina");
define("LANGASS24","Circolare");
define("LANGASS25","Giudizio");
define("LANGASS26","Periodo");
define("LANGASS27","Commento");
define("LANGASS28","TRIADE assistenza vi ringrazia per il vostro aiuto.");
define("LANGASS29","Equipe TRIADE.");
define("LANGASS30","L'équipe TRIADE al vostro servizio");
define("LANGASS31","TRIADE é un prodotto unico e anche inedito, non esitate a trasmetterci i vostri consigli e osservazioni in modo che il sito possa rispondere alle reali attese degli utenti ! Grazie a voi :-)");
define("LANGASS32","Libro ospiti");
define("LANGASS33","Vostra testimognanza in diretta : inserite le vostre osservazioni nel nostro libro degli ospiti.");
define("LANGASS34","Il vostro msg é stato inviato, non macheremo di rispondervi.<br> <BR>Grazie per utilizzare TRIADE e a presto.<BR><BR><BR><UL><UL>L'équipe TRIADE.<BR>");
define("LANGASS35","Altro");
define("LANGASS36","SMS");
define("LANGASS37","WAP");
define("LANGASS38","descrizione");
define("LANGASS39","Codice a barre");
define("LANGASS40","Stage Pro.");
// -----------------------------------------------------------------------------
define("LANGRECH1","<font class=T2>nessun allievo nella  classe</font>");
define("LANGRECH2","Ricerca di ");
define("LANGRECH3","<font class=T2>nessun aliievo per questa ricerca</font>");
define("LANGRECH4","Informazione / Modifica");
// ---------------------------------------------------------------------------------
define("LANGBASE1","ATTENZIONE : questo modulo è da utilizzare durante la prima utilizzazione,<br> distrugge tutte le info degli allievi (note, giudizi, andamento scolastico).");
define("LANGBASE2"," I files da importare DEVONO essere in formato dbf ");
define("LANGBASE3","Ecco l'elenco dei files ");
define("LANGBASE4","Modulo d'importazionee dei files GEP ");
define("LANGBASE5","Importare una database GEP ");
define("LANGBASE6","Totale allievi nel file DBF ");
define("LANGBASE7","Totale allievi nelle classe ");
define("LANGBASE8","Totale allievi senza classe ");
define("LANGBASE9","Recupero password  ");
define("LANGBASE10","Impossibile aprire il file F_ele.dbf");
define("LANGBASE11","Database trattato da -- L'équipe TRIADE");
define("LANGBASE12","Il file selezionato non é valido !");
define("LANGBASE13","Ecco l'elenco delle password");
define("LANGBASE14","Recuperare l'elenco selezionando l'insieme delle righe ed effettuare un copia/incolla in un file \"txt\".");
define("LANGBASE15","In seguito con excel o OpenOffice, ricuperare il file \"txt\"  precisando il punto e virgola come separatore dei campi.");
define("LANGBASE17"," Attenzione : le passord non sono accessibili che su  <br />questa pagina !! Pensate a recuperare l'elenco <b>PRIMA</b> di finire ");
define("LANGBASE18","INFORMAZIONE NON DISPONIBILE");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGBULL1","Stampa giudizi trimestrali");
define("LANGBULL2","Indicate la classe");
define("LANGBULL3","Anno scolastico");
define("LANGBULL4","<a href=\"#\" onclick=\"open('https://www.adobe.com/fr/','_blank','')\"><b><FONT COLOR=red>ATTENTION</FONT></B> Utilizzare <B>Adobe Acrobat Reader</B>.  Software e download gratuito  cliccate <B>QUI</B></A>");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGPARENT1","nessun msg");
define("LANGPARENT2","Nessun delegato assegnato, per il momento");
define("LANGPARENT3","Allievo(i) delegato(i)");
define("LANGPARENT4","Genitore(i) delegato(i)");
define("LANGPARENT5","Elenco dei delegati");
//----------------------------------------------------------------------//
define("LANGPUR3","ATTENZIONE: questo modulo va utilizzato <br>quando si desidera eliminare dei dati TRIADE.");
define("LANGPUR4","ATTENZIONE, Entrate in un moduloche eliminerà i dati che avete scelto. \\n Volete continuare ? \\n\\n L\'équipe TRIADE");
define("LANGPUR5","I dati sono stati eliminati");
define("LANGPUR6","Informazione : La selezione \"Allievi\" implica l'automatica eliminazione delle note, assenze, discipline, dispense, ritardi, riunioni");
define("LANGPUR7","Indicate l'elemento o gli elementi da distruggere : ");
define("LANGPUR8","DA Conservare");
define("LANGPUR9","DA Eliminare");
//----------------------------------------------------------------------//
define("LANGCHAN0","Modulo per il cambiamento di classe di uno o più allievi");
define("LANGCHAN1","ATTENZIONE: questo modulo va utilizzato <br>se si vuole effettuare <br> un cambiamento di classe per gli allievi");
define("LANGCHAN3","ATTENZIONE, l\'insieme dei dati dell\'allievo \\n o degli allievi in relazione al cambiamento di classe sarà eliminato");
//----------------------------------------------------------------------//
define("LANGGEP1",'Importazione del file GEP');
define("LANGGEP2",'Indicate il file');
//----------------------------------------------------------------------//
define("LANGCERT1"," scaricate questo certificato ");
//----------------------------------------------------------------------//
define("LANGPROFR1",'Indicate gli allievi in ritardo');
define("LANGPROFR2",'Inserimento dei ritardi  ');
define("LANGKEY1",'<font class=T1>Nessun codice di registrazione </font>');
define("LANGDISP20",'Aggiungere dispense');
define("LANGPROFA",'<br><center><font size=2>Nessun codice di registrazione </font><br><br>Vogliate contattare la vostra Amministrazione TRIADE, <br>per convalidare la richiesta di registrazione di TRIADE. </center><br><br>');
define("LANGPROFB",'Aggiunta di una nota in ');
define("LANGPROFC",'Confermate la registrazione delle note ');
define("LANGPROFD",'Convalidate la registrazione delle note');
define("LANGPROFE",'&nbsp;&nbsp;<i><u>Info</u>: Il tasto ENTER vi permette di passare automaticamente alla nota seguente.</i>');
define("LANGPROFF",'Aggiunta di una nota');
define("LANGPROFG",'Indicare la classe');
//----------------------------------------------------------------------//
define("LANGMETEO1",'GIORNO');
define("LANGMETEO2",'NOTTE');
//----------------------------------------------------------------------//
define("LANGPROFP1","Messaggio per la classe");
define("LANGPROFP2","Registrare il messaggio");
define("LANGPROFP3","Messaggio del Docente di Classe");
//----------------------------------------------------------------------//
// Module Stage Pro
define("LANGSTAGE1","Pianificazione degli stages ");
define("LANGSTAGE2","Visualizzare le date degli stages ");
define("LANGSTAGE3","Aggiungere ");
define("LANGSTAGE4","Assegnare ");
define("LANGSTAGE5","Inserimento di una data dello stage ");
define("LANGSTAGE6","Modifica  di una data dello stage ");
define("LANGSTAGE7","Eliminare una data dello stage ");
define("LANGSTAGE8","Gestione delle aziende ");
define("LANGSTAGE9","Visualizzare le dieverse aziende ");
define("LANGSTAGE10","Aggiungere un'azienda ");
define("LANGSTAGE11","Modificare un'azienda ");
define("LANGSTAGE12","Eliminare un'azienda ");
define("LANGSTAGE13","Gestione degli allievi ");
define("LANGSTAGE14","Visualizzare gli allievi che sono in un'aziendaser ");
define("LANGSTAGE15","Assegnare un allievo a un'azienda ");
define("LANGSTAGE16","Modificare le caratteristiche di un allievo ");
define("LANGSTAGE17","Eliminare l'assegnazione di un allievo ");
define("LANGSTAGE18","Visualizzare le date dello stage");
define("LANGSTAGE19","Stage");
define("LANGSTAGE20","Ricerca di un'azienda");
define("LANGSTAGE21","Consultare le aziende per tipo di attività");
define("LANGSTAGE22","Consultazione delle aziende");
//----------------------------------------------------------------------//
define("LANGGEN1","Amministrazione");
define("LANGGEN2","Andamento scolastico");
define("LANGGEN3","Insegnanti");
//----------------------------------------------------------------------//
define("LANGCDT1","Richiesta di C.D.G");
define("LANGCDT2","Buongiorno, <br> <br> La vostra richiesta di Compito per il ");
define("LANGCDT3","<br><br><b>non è possibile</b>, vogliate scegliere un'altra data o contattarci direttamente. <br><br> Grazie");
define("LANGCDT4","<br><br><b>é registrata</b> per ogni info supplementare, contattateci. <br><br> Merci");
define("LANGCDT5","per il ");
define("LANGCDT6","Soggetto / Materia");
define("LANGCDT7","Richiesta rifiutata");
define("LANGCDT8","Richiesta accordata");
//----------------------------------------------------------------------//
define("LANGCALEN1","Evento");
define("LANGCALEN2","Planning del ");
define("LANGCALEN3","Aggiungere un evento");
define("LANGCALEN4","Elinare un evento");
define("LANGCALEN5","Reload della pagina");
define("LANGCALEN6","Calendario degli eventi");
define("LANGCALEN7","Nella classe di ");
define("LANGCALEN8","Compiti di ");
define("LANGCALEN9","Compito(i) all'ordine del giorno");
//----------------------------------------------------------------------//
//modulo riservazione
define("LANGRESA1","Gestione dell'attrezzatura");
define("LANGRESA2","Gestione delle aule");
define("LANGRESA3","Elenco dell'attrezzatura");
define("LANGRESA4","Elenco delle sale");
define("LANGRESA5","Aggiungere un'attrezzatura");
define("LANGRESA6","Modificare un'attrezzatura");
define("LANGRESA7","Eliminare un'attrezzatura");
define("LANGRESA8","Aggiungere un aula");
define("LANGRESA9","Eliminare un'aula");
define("LANGRESA10","Eliminare un'aula");
define("LANGRESA11","Riservare un'attrezzatura / aula");
define("LANGRESA12","Riservare un'attrezzatura");
define("LANGRESA13","Riservare un'aula");
define("LANGRESA14","Riservare");
define("LANGRESA15","Creare un'attrezzatura");
define("LANGRESA16","Cognomee dell'attrezzatura");
define("LANGRESA17","Registrare la crazione");
define("LANGRESA18","Informazioni complementari");
define("LANGRESA19","Attrezzatura registrata");
define("LANGRESA20","Creazione di un aula");
define("LANGRESA21","Cognomee dell'aula");
define("LANGRESA22","Aula registrata");
define("LANGRESA23","Eliminare aula");
define("LANGRESA24","Aula");
define("LANGRESA25","Eliminare l'aula");
define("LANGRESA26","Aula eliminata");
define("LANGRESA27","un'aula");
define("LANGRESA28","Impossibile eliminare auest'aula. \\n\\n Aula assegnata.  ");
define("LANGRESA29","Attrezzatura eliminata");
define("LANGRESA30","Impossibile eliminare questa attrezzatura. \\n\\n Attrezzatura assegnata.  ");
define("LANGRESA31","un'attrezzatura");
define("LANGRESA32","Eliminare attrezzatura");
define("LANGRESA33","Attrezzatura");
define("LANGRESA34","Eliminare un'attrezzatura");
define("LANGRESA35","Elenco delle attrezzaturas");
define("LANGRESA36","DATA");
define("LANGRESA37","Di");
define("LANGRESA38","A");
define("LANGRESA39","Da chi");
define("LANGRESA40","Informazione");
define("LANGRESA41","Confermare");
define("LANGRESA42","Confermato");
define("LANGRESA43","Non&nbsp;Confermato");
define("LANGRESA44","Planning Attrezzatura");
define("LANGRESA45","Attrezzatura");
define("LANGRESA46","Attrezzatura già riservata per questa data");
define("LANGRESA47","Consultare la pianificazione delle riservazioni già esistenti per questa attrezzatura");
define("LANGRESA48","Riservare a partire dal ");
define("LANGRESA49","In data del ");
define("LANGRESA50","Attrezzatura riservata in attesa di conferma");
define("LANGRESA51","Pianificazione Aula");
define("LANGRESA52","Aula");
define("LANGRESA53","Aula già riservata per questa data");
define("LANGRESA54","Aula riservata in attesa di conferma");
define("LANGRESA55","Consultare la pianificazione per le Riservazioni di quest'aula");
define("LANGRESA56","Confirmare la Riservazione");
define("LANGRESA57","Pianificazione");
define("LANGRESA58","Confermare");
//----------------------------------------------------------------------//
define("LANGTTITRE1","Accesso Membro");
define("LANGTTITRE2","Membro");
define("LANGTTITRE3","Attivazione del conto");
define("LANGTTITRE4","Grazie per voler pazientare");
//--------------
define("LANGTP1","Cognome");
define("LANGTP2","Nome");
define("LANGTP3","Password");
define("LANGTCONNEXION","Connessione");
define("LANGTERREURCONNECT","Errore di Connessione");
define("LANGTCONNECCOURS","Connessione in corso ");
define("LANGTFERMCONNEC","Cliccate qui per la chiusura del vostro conto");
define("LANGTDECONNEC","Disconnessione in corso");

define("LANGTBLAKLIST0",'<b><font color=red  class=T2>Il vostro conto é disattivato !!</b><br> Per riattivarlo, contattate l\'amministrazione del vostro istituto scolastico.</font>');

define("LANGMOIS1","Gennaio");
define("LANGMOIS2","Febbraio");
define("LANGMOIS3","Marzo");
define("LANGMOIS4","Aprile");
define("LANGMOIS5","Maggio");
define("LANGMOIS6","Giugno");
define("LANGMOIS7","Luglio");
define("LANGMOIS8","Agosto");
define("LANGMOIS9","Settembre");
define("LANGMOIS10","Ottobre");
define("LANGMOIS11","Novembre");
define("LANGMOIS12","Dicembre");

define("LANGDEPART1","dell'allievo");

define("LANGVALIDE","Confemare");
define("LANGIMP45","Scrivere");

define("LANGMESS34","Messaggi non più disponibili.");
define("LANGMESS35","Rendere pubblico questo gruppo.");
define("LANGMESS36","Messaggio eliminato");


define("LANGRESA59","Nome dell'aula");
define("LANGRESA60","Informazione");

define("LANGMAINT0","E' previsto un intervento sul programma");
define("LANGMAINT1","Il servizio TRIADE sarà inaccessibile il ");
define("LANGMAINT2","tra");
define("LANGMAINT3","e");

define("LANCALED1","Anno Precedente");
define("LANCALED2","Anno Seguente");


define("LANGTTITRE5","Problema di accesso");
define("LANGTTITRE6","Domande");
define("LANGTPROBL1","Attualmente, il servizio TRIADE  é in funzione.");
define("LANGTPROBL2","Ho una domanda");
define("LANGTPROBL3","Registrare la domanda");
define("LANGTPROBL4","Uscire senza registrare");
define("LANGTPROBL5","Spiegateci il vostro problema");
define("LANGTPROBL6","Istituto scolastico*: ");
define("LANGTPROBL7","Email : ");
define("LANGTPROBL8","Messaggio : ");
define("LANGTPROBL9","(* campo obbligatorio)");
define("LANGTPROBL10","Registrare il problema");
define("LANGTPROBL12","Ci incarichiamo di risolvere il problema nel minor tempo possibile. \\n\\n  L'Equipe TRIADE ");

define("LANGELEV1","Note scolastiche di");

define("LANGFORUM1","- Elenco dei messaggi");
define("LANGFORUM2","Nessun messaggio è stato inviato su questo forum di discussione");
define("LANGFORUM3","Voi potete ");
define("LANGFORUM3bis"," inviare ");
define("LANGFORUM3ter"," un primo messaggio se lo volete ");
define("LANGFORUM4","Inserire un nuovo messaggio");
define("LANGFORUM5","Forum - Inviare un messaggio");
define("LANGFORUM6","Regole da rispettare");
define("LANGFORUM7","Errore : il messaggio di riferimento non esiste.");
define("LANGFORUM8","Ritorno all'elenco dei messaggi inviati");
define("LANGFORUM9","--- messaggio d'origine ---");
define("LANGFORUM10","Vostro Cognome ");
define("LANGFORUM11","Vostro email ");
define("LANGFORUM12","Soggetto ");
define("LANGFORUM13","Enviare"); // --> bottone invio
define("LANGFORUM14","Ritorno all'elenco dei messaggi inviati");
define("LANGFORUM15","Forum - invio di un messaggio");
define("LANGFORUM16","<b>Errore</b> : questa pagina non può essere caricata<br> senza un messaggio che é stato precedentemente ");
define("LANGFORUM16bis"," inviato ");
define("LANGFORUM17","<b>Errore</b> : il vostro messaggio non ha nessun testo.<br>");
define("LANGFORUM18","<b>Errore</b> : avete dimenticato il vostro Cognome.<br>");
define("LANGFORUM19","Errore ! Il vostro messaggio non ha potuto essere inviato. ");
define("LANGFORUM20","<b>Errore</b> : impossibile aggiornare il file index. <br>");
define("LANGFORUM21","Il vostro messaggio non ha potuto essere inviato.");
define("LANGFORUM22","Il vostro messaggio é stato inviato correttamente.<br>Grazie del contributo.");
define("LANGFORUM23","Ritorno all'elenco dei messaggi inviati");
define("LANGFORUM24","Forum - lettura di un messaggio");
define("LANGFORUM25","Nessun messaggio è stato inviato su questo forum di discusione.");
define("LANGFORUM26","Voi potete ");
define("LANGFORUM26bis","inviare");
define("LANGFORUM26ter","un primo messaggio se lo volete.");
define("LANGFORUM27","Questo messaggio non esiste o è stato eliminato dall'amministratore del forum di discussione.<br>");
define("LANGFORUM28","Ritorno all'elenco dei messaggi inviati");
define("LANGFORUM30","Autore");
define("LANGFORUM31","Data");
define("LANGFORUM32","Inviare una risposta");
define("LANGFORUM33","messaggio precedente (nella sequenza della discussione)");
define("LANGFORUM34","messaggi seguenti (nella sequenza delle discussioni)");

define("LANGPROFH","Compito Scolastico da fare in ");
define("LANGPROFI","Registrare il compito da fare ");
define("LANGPROFJ","Campito da fare");
define("LANGPROFK","pender nota&nbsp;del&nbsp;");
define("LANGPROFL","Confermare la data");
define("LANGPROFM","Per il ");
define("LANGPROFN","Compito di ");
define("LANGPROFO","Compito scolastico ");
define("LANGPROFP","Inserimento dei docenti di classe");
define("LANGPROFQ","Per domani");
define("LANGPROFR","Per ieri");
define("LANGPROFS","Materia o soggetto");
define("LANGPROFT","Convalidare la domanda di C.D.G");
define("LANGPROFU","Domanda Inviata -- L'équipe TRIADE");


define("LANGPROJ17","Numero assenze");
define("LANGPROJ18","giorni");

define("LANGCALEN10","Calendario dei compiti");

define("LANGPARENT6","Elenco dei Ritardi");
define("LANGPARENT7","Elenco delle Assenze");
define("LANGPARENT8","Assente il ");
define("LANGPARENT9","Elenco delle dispense");
define("LANGPARENT10","Periodo&nbsp;du&nbsp;");
define("LANGPARENT11","A"); // indica una data (ora)
define("LANGPARENT12","Il"); // indica data del giorno
define("LANGPARENT13","Certificato");
define("LANGPARENT14","Sanzione disciplinare");
define("LANGPARENT15","Sanzione");
define("LANGPARENT16","In&nbsp;elaborazione");
define("LANGPARENT17","a");  // indique une heure
define("LANGPARENT18","Elaborazione effettuata");
define("LANGPARENT19","Elenco delle circolari amministrative");
define("LANGPARENT20","Accesso File");
define("LANGPARENT21","Visibile da ");
define("LANGPARENT22","Calendario degli eventi ");
define("LANGPARENT23","Calendario dei compiti del giorno ");
define("LANGPARENT24","Domanda di C.D.G ");


define("LANGAUDIO1","Comunicato Audio");
define("LANGAUDIO2","Il "); // indica una data
define("LANGAUDIO3","C"); // prima lettera
define("LANGAUDIO3bis","omunicato audio nel formato <b>mp3</b><br>Grandezza massima del file : ");
define("LANGAUDIO4","registrare il comunicato");
define("LANGAUDIO5","Vogliate pazientare da 2 a 3 minuti dopo l'invio del file audio.");
define("LANGAUDIO6","Eliminare il comunicato audio");


define("LANGOK","Ok");
define("LANGCLICK","Cliccate-qui");
define("LANGPRECE","Precedente");
define("LANGERROR1","Dati introvabili");
define("LANGERROR2","nessun dato");


define("LANGPROF1","Indicare la materia");
define("LANGPROF2","Numero delle note");
define("LANGPROF3","Visualizzazione delle note");
define("LANGPROF4","gruppo");
define("LANGPROF5","Scelta del trimestre");
define("LANGPROF6","Soggetto "); // soggetto del compito
define("LANGPROF7","Nome del soggetto "); // soggetto del compito
define("LANGPROF8","Nota"); //nota di un compito
define("LANGPROF9","Compito scolastico da fare a casa");
define("LANGPROF10","Modifica di una nota");
define("LANGPROF11","Eliminazione di un compito"); // compito --> interrogazio
define("LANGPROF12","Docente di Classe");
define("LANGPROF13","Scheda Allievo");
define("LANGPROF14","Aggiunta di una Nota in ");
define("LANGPROF15","Modificare una nota in");
define("LANGPROF16","Titolo del compito");
define("LANGPROF17","Data&nbsp;del&nbsp;compito"); // &nbsp; --> uguale un blanc
define("LANGPROF18","Pazientare");
define("LANGPROF19","Confermare la modifica delle note");
define("LANGPROF20","Convalidare la modifica delle note");
define("LANGPROF21","Modifica delle Note in");
define("LANGPROF22","Visualizzazione delle note in");
define("LANGPROF23","Eliminazione di un compito in");
define("LANGPROF24","Compito di "); // interrogazione del
define("LANGPROF25","é eliminata");
define("LANGPROF26","Informazioni sull'allievo");
define("LANGPROF27","Informazioni amministrative");
define("LANGPROF28","Informazioni sull'andamento scolastico");
define("LANGPROF29","Informazioni mediche");
define("LANGPROF30","Informazioni del");
define("LANGPROF31","Di"); // indicante una persona


define("LANGEL1","Cognome");
define("LANGEL2","Nome");
define("LANGEL3","Classe ");
define("LANGEL4","Lv1");
define("LANGEL5","Lv2");
define("LANGEL6","Opzione");
define("LANGEL7","Regime");
define("LANGEL8","Data di nascita");
define("LANGEL9","Nazionalità");
define("LANGEL10","Password");
define("LANGEL11","Cognome della Famiglia");
define("LANGEL12","Nome");
define("LANGEL13","Via");
define("LANGEL14","Indirizzo 1");
define("LANGEL15","Codice postale");
define("LANGEL16","Comune");
define("LANGEL17","via");
define("LANGEL18","Indirizzo 2");
define("LANGEL19","Codice Postale");
define("LANGEL20","Comune");
define("LANGEL21","Telefono");
define("LANGEL22","Professione del padre");
define("LANGEL23","Telefono del padre");
define("LANGEL24","Professione della madre");
define("LANGEL25","Telefono della madre");
define("LANGEL26","Scuola");
define("LANGEL27","Codice della scuola");
define("LANGEL28","Codice postale");
define("LANGEL29","Comune");
define("LANGEL30","Numero Studente");
// define("LANGEL30","Numero Nazionale");


define("LANGPROF32","Informazioni scolastiche");
define("LANGPROF33","Compito a casa");
define("LANGPROF34","Consultazione in settimana");
define("LANGPROF35","Settimana precedente");
define("LANGPROF36","Setimana prossima");
define("LANGTP23"," INFORMAZIONE - Richiesta di Riservare !");
define("LANGRESA61","Nome dell'attrezzatura");


define("LANGIMP46","Nome");
define("LANGIMP47","Titolo (Sig.o Sig.ra) ");
define("LANGIMP48","Cognome");
define("LANGIMP49","* campo obbligatorio");
define("LANGIMP50","Il file da trasmettere <FONT color=RED><B>DEVE</B></FONT> contenere <FONT COLOR=red><B>9</B></FONT> campi <I>(non vuoti)</I> separati da un medesimo separatore l \"<FONT color=red><B>;</B></font>\" <I>Sia la presenza di 8 volte il carattere \"<FONT color=red><B>;</B></font>\"</I>");
define("LANGIMP51","Password genitori");
define("LANGIMP52","Password allievo");



define("LANGacce_dep1","Errore di Connessione");
define("LANGacce_dep2","Verificare il vostro codice di Connessione, se il problema persiste, <br />  avvertite il vostro amministratore TRIADE tramite il link <br /> 'Problema d'accesso nel menu a sinistra");

define("LANGacce_ref1","Errore Tipo :Accesso non autorizzato");
define("LANGacce_ref11","Visitato il ");
define("LANGacce_ref12","da ");
define("LANGacce_ref13","con  ");
define("LANGacce_ref2","ACCESSO NON AUTORIZZATO");
define("LANGacce_ref3","Per accedere al vostro conto, dovete essere connessi.");
define("LANGacce1","L'allievo ");
define("LANGacce12","deve sottostare a una punizione, <br> a causa : ");
define("LANGacce13","per il motivo ");
define("LANGacce14","Il compito assegnato é il seguente : ");
define("LANGacce2","Eliminare questo messaggio : ");
define("LANGacce21","Eliminare");
define("LANGacce3","L'allievo ");
define("LANacce31","non si é presentato</b></font> al responsabile scolastico (CPE), <b>per la retenue</b>,  in seguito alla decisione :");
define("LANacce32","per il motivo : ");
define("LANGacce4","Il compito da eseguire é il seguente :");
define("LANGacce5","Eliminare");
define("LANGacce6","Gestione disciplinare");
define("LANGaccrob11","Download del software Adobe Acrobat Reader 8.1.0 fr");
define("LANGaccrob2","23,4 Mo  per Windows 2000/XP/2003/Vista");
define("LANGaccrob3","Tempo do download :");
define("LANGaccrob4","a 56 K : 57 min et 3 s");
define("LANGaccrob5","a 512 K : 6 min et 14 s");
define("LANGaccrob6","a 5 M : 37 secondes");
define("LANGaccrob7","Download del Software Adobe Acrobat Reader 6.O.1 fr");
define("LANGaccrob8","Grandezza : ");
define("LANGaccrob9","0.40916 Mb per NT/95/98/2000/ME/XP");
define("LANGaccrob10","a 56 K : 0 min et 58.2 s");
define("LANGaccrob11bis","a 512 K : 0 min et 6.6 s ");
define("LANGaffec_cre21","Creazione di assegnazione per la classe ");
define("LANGaffec_cre22","Assegnazione in corso ");
define("LANGaffec_cre23","L'esecuzione del programma di assegnazione é automatico<br>Se non visualizzate la nuova pagina, cliccate ");
define("LANGaffec_cre24","TRIADE - Conto di ");
define("LANGaffec_cre31","CREAZIONE - ASSEGNAZIONE");
define("LANGaffec_cre41","Stampare");
define("LANGaffec_mod_key1","Assegnazione delle classi");
define("LANGaffec_mod_key2","Modulo di modifica assegnazione classi.");
define("LANGaffec_mod_key3","ATTENZIONE questo modulo va utilizzato per le modifiche di assegnazione,<br> distrugge tutte le note degli allievi  delle classi modificate. ");
define("LANGaffec_mod_key4","ATTENZIONE, la distruzione delle note delle classi selezionate saranno cancellate. \\n Volte continuare ? \\n\\n L\'équipe TRIADE");
define("LANGattente1","Attesa - TRIADE");
define("LANGattente2","Vogliate pazientare, P.F.");
define("LANGattente3","L'Equipe TRIADE.");
define("LANGatte_mess1","TRIADE - Attesa - messaggeria");
define("LANGatte_mess2","Vogliate pazientare, P.F.");
define("LANGatte_mess3","servizio TRIADE");
define("LANGbasededon20","Inviare il file");
define("LANGbasededon201","niente");
define("LANGbasededon2011","Importare il file GEP");
define("LANGbasededon202","File inviato -- L'équipe TRIADE");
define("LANGbasededon203","File non registrato");
define("LANGbasededon31","Indicate per ogni referenza la classe corrispondente");
define("LANGbasededon32","Scelta ...");
define("LANGbasededon33","nessuna");
define("LANGbasededon34","L'invio del file può durare da <b>2 a 4 minuti</b> in funzione del numero degli allievi.");
define("LANGbasededon35","Il file deve essere nel formato <b>dbf</b> e deve essere <b>F_ele.dbf</b>");
define("LANGbasededon41","Errore inerente il numero delle classi !!! - Contattare l'équipe TRIADE <br /><br /> support@triade-educ.org</center>");
define("LANGbasededon42","Errore nella registrazione delle classi, une classe è stata inserita più volte -- L'équipe TRIADE");
define("LANGbasededon43","messaggio di : ");
define("LANGbasededon44","Da");
define("LANGbasededon45","Membro :");
define("LANGbasededon46","messaggio :");
define("LANGbasededon47","NUOVO DATABASE:");
define("LANGbasededon48","- con GEP");
define("LANGbasededon49"," Istituto :");
define("LANGbasededoni11","'Attenzione','./image/commun/warning.jpg','<font face=Verdana size=1><font color=red>I</font>l modulo <b>dbase</b> non é <br> caricato !! <i>Necessario per importare <br> una base GEP.");
define("LANGbasededoni21","ATTENZIONE, la distruzione della vecchia base dati sarà automatica. \\n Volete continuare ? \\n\\n L\'Equipe TRIADE");
define("LANGbasededoni31","Indicate a quale categoria attribuire il file ");
define("LANGbasededoni32","L'importazione del file concerne : ");
define("LANGbasededoni33","Import. degli allievi : ");
define("LANGbasededoni34","Import. degli insegnanti :");
define("LANGbasededoni35","Import. del personale che si occupa dell'andamento scolastico : ");
define("LANGbasededoni36","Import. del personale amministrativo : ");
define("LANGbasededoni41","Classe precedente");
define("LANGbasededoni42","Anno precedente");
define("LANGbasededoni51","Per il titolo");

define("LANGbasededoni61","Errore");
define("LANGbasededoni71","Import. di un file ASCII");
define("LANGbasededoni72","messaggio del : ");
define("LANGbasededoni721","Da");
define("LANGbasededoni722","Membro :");
define("LANGbasededoni723","messaggio :");
define("LANGbasededoni724","NUOVO DATABASE:");
define("LANGbasededoni725","- con ASCII");
define("LANGbasededoni726"," Istituto :");
define("LANGbasededoni73","Totale delle registrazione nel database ");
define("LANGbasededoni91","Import. del file ASCII");
define("LANGbasededoni92","Errore sul numero delle classi !!! - Contattare l'équipe TRIADE <br />");
define("LANGbasededoni93","Errore nella registrazione delle classi, una classe é inserita più volte -- L'équipe TRIADE");
define("LANGbasededoni94","Dati della base trattata -- L'équipe TRIADE<br />");
define("LANGbasededoni95","Totale allievi registrati nel database : ");
define("LANGPIEDPAGE","<p> La <b>T</b>ransparenza e la <b>R</b>apidità dell'<b>I</b>nformatica <b>A</b>l servizio <b>D</b>ell'<b>I</b>nsegnamento<br>Per visualizzare questo sito in modo ottimale :  risoluzione minima : 800x600 <br>  © 2000 - ".date("Y")." TRIADE - Tutti i diritti riservati");

define("LANGAPROPOS1","Versione");
define("LANGAPROPOS2","Tutti i diritti riservati");
define("LANGAPROPOS3","Licenza d'utilizzazione");
define("LANGAPROPOS4","Prodotto ID");

define("LANGTELECHARGER","Scaricare");
define("LANGAJOUT1","Per il Regime : scelte possibili (<b>INT</b> (Interno),<b>EXT</b> (Esterno), <b>SI</b> (Semi interno)<br><br>");
define("LANGIMP44","Il file non è conforme.");
define("LANGBASE16"," Le colonne sono rappresentate sottoforma : <b>Cognome per il login ; Nome per il login ; Password Genitori ; Password Allievo in chiaro</b>");


define("LANGSUPP0","Eliminazione di un conto Supplente");
define("LANGSUPP1","Modulo x Eliminazione");
define("LANGSUPP2","Eliminare il conto");
define("LANGSUPP3","Volete Eliminare ddei supplenti dall'elenco");
define("LANGSUPP3bis","supplente di");
define("LANGSUPP4","Confirmare l'eliminazione");
define("LANGSUPP5","Impossible Eliminare questo conto. \\n\\n Comto assegnato a una classe.  \\n\\n  L'équipe TRIADE");
define("LANGSUPP6","Conto eliminato - L'équipe TRIADE");
define("LANGSUPP7","Eliminazione di un gruppo");
define("LANGSUPP8","Eliminare il gruppo");
define("LANGSUPP9","Eliminazione di un conto ");
define("LANGSUPP10","Eliminare il conto");
define("LANGSUPP11","un membro resp. andamento scolastico");
define("LANGSUPP12","un amministratore");
define("LANGSUPP13","un docente");
define("LANGSUPP14","Eliminazione di un allievo nella classe");
define("LANGSUPP15","Cliccare sull'allievo da Eliminare");
define("LANGSUPP16","Eliminazione di un allievo");
define("LANGSUPP17","sta per essere eliminato dal database");
define("LANGSUPP18","Tutte le info per questo allievo stanno per essere eliminate, cioè : <br> (note, assenze, ritardi, dispense, sanzioni, informazioni, messaggi, ...)");
define("LANGSUPP19","Annullare l'eliminazione");
define("LANGSUPP20","é eliminato dal database");
define("LANGSUPP21","Eliminare una classe");
define("LANGSUPP22","Eliminazione di una classe");
define("LANGSUPP23","Eliminazione di una materia o sottomateria");
define("LANGSUPP24","Eliminare la materia");
define("LANGSUPP25","Classe eliminata --  Service TRIADE");
define("LANGSUPP26","Materia eliminata --  Service TRIADE");
define("LANGSUPP27","Creazione di una materia");
define("LANGSUPP28","Sotto-materia registrata");

define("LANGADMIN","Amministratore");
define("LANGPROF","docente");
define("LANGSCOLAIRE","dell'Andamento scolastico");
define("LANGCLASSE","una classe");


define("LANGGRP11","Nome del Gruppo");
define("LANGGRP12","Classe(i) interessata(e)");
define("LANGGRP13","Elenco Allievi");
define("LANGGRP14","Elenco dei gruppi");
define("LANGGRP15","Creazione di un gruppo");
define("LANGGRP16","Indicate gli allievi del gruppo");
define("LANGGRP17","Selezionare");
define("LANGGRP18","Registrare il gruppo");
define("LANGGRP19","Creazione del gruppo effettuata");
define("LANGGRP20","Altro gruppo");
define("LANGGRP21","Elenco dei gruppi");
define("LANGGRP22","Indicare una classe per la creazione del gruppo P.F. \\n\\n L'équipe TRIADE");
define("LANGGRP23","Elenco allievi del gruppo");
define("LANGGRP24","Elenco classi");
define("LANGGRP25","Elenco materie");



//----------------//
define("LANGDONNEENR","<font class=T2>Dato(i) Registrato(i).</font>");

define("LANGABS47","Aggiunta di una sanzione disciplinare");
define("LANGABS48"," é arrivato ");
define("LANGABS48bis","volte nella categoria");
define("LANGABS49","durata");
define("LANGABS50"," Ritenuta del ");
define("LANGABS51","Tel. prof. Padre ");
define("LANGABS52","Tel. prof. Madre ");
define("LANGABS53","Nessun rit. e ass. da segnalare");

define("LANGCALRET1","Calendario &nbsp; delle &nbsp; Ritenute");

define("LANGHISTO1","Storico delle operazioni");

define("LANGCDT9","Aggiungere un'inserimento");
define("LANGCDT10","Eliminare un'inserimento");
define("LANGCDT11","nella classe di");

define("LANGDISP11","Visualizzazione <b>completa</B> delle dispense");

define("LANGEN","In");

define("LANGAFF4","Registrazione di una classe");
define("LANGAFF5","Tutte le classi");
define("LANGAFF6","Consultare questa classe");

define("LANGCHER1","Ricerca Complessa");
define("LANGCHER2","Indicare il formato del file da generare");
define("LANGCHER3","Indicare il separatore dei campi");
define("LANGCHER4","Effettuare la ricerca di un allievo a partire dal Cognome : <b>cliccate qui</b>");
define("LANGCHER5","Aggiungere");
define("LANGCHER6","Cancellare");
define("LANGCHER7","Salire");
define("LANGCHER8","Scendere");
define("LANGCHER9","Seguente");
define("LANGCHER10","Elemento ricercato");
define("LANGCHER11","Numero dei criteri di ricerca");
define("LANGCHER12","A partire da");

define("LANGCHER13","con il valore");
define("LANGCHER14","Ricerca approssimativ");
define("LANGCHER15","Ricerca precisa");
define("LANGCHER16","Lanciare la ricerca");
define("LANGCHER17","Attenzione: rimane un elemento non scelto !! -- L'équipe TRIADE ");

define("LANGCHER18","come valore");

define("LANGTITRE34","Configurazione email ritardi");
define("LANGTITRE35","Configurazione email assenze");

define("LANGCONFIG1","Configurazione registrata.");
define("LANGCONFIG2","Ecco il vostro testo ");

define("LANGCONFIG3","Indicate l'elenco dei genitori degli allievi che riceveranno la posta elettronica");

define("LANGERROR01","Errore d'accesso al database");
define("LANGERROR02","ATTENZIONE Impossibile <br><br>Il problema può essere dovuto alle info acquisite <br>(Verificate i diversi campi prima di convalidarli).<BR>  <BR>Ou l'info é già registrata O non accessibile.");
define("LANGERROR03","Accesso impossibile al database per questa manipolazione . <BR>");

define("LANGABS54","é già stato registrato come assente.");
define("LANGABS55","é già stato registrato un ritardo.");


define("LANGPARAM4","Il certificato é stato registrato correttamente.");
define("LANGPARAM5","Le certificato di frequenza scolastica degli allievi della classe ");
define("LANGPARAM5bis","é disponibile, in formato PDF");
define("LANGPARAM6","Parametraggio del contenuto dei giudizi e periodi");

define("LANGPARAM7","Cognome  del direttore dell'istituto");
define("LANGPARAM8","Nome  dell'istituto o scuola");
define("LANGPARAM9","indirizzo");
define("LANGPARAM10","Codice Postale");
define("LANGPARAM11","Città");
define("LANGPARAM12","Telefono");
define("LANGPARAM13","E-mail");
define("LANGPARAM14","Logo della scuola");
define("LANGPARAM15","Registrare i parametri");
define("LANGPARAM16","Registrazione effettuta. -- L'Equipe TRIADE");

define("LANGCERTIF1","Il certificato di frequenza di ");
define("LANGCERTIF1bis","é disponibile, in formato PDF");


define("LANGRECHE1","Informazioni sull'allievo");

define("LANGBT52","Modificare i dati");

define("LANGEDIT1","Dati introvabili");

define("LANGMODIF1","Aggiornamento del conto Allievo");
define("LANGMODIF2","Annotazioni sull'allievo");
define("LANGMODIF3","Annotazioni sulla famiglia");

define("LANGALERT1","Aggiornamento dati -- Equipe TRIADE");
define("LANGALERT2","Attenzione formato del file non conforme o grandezza non rispettata");
define("LANGALERT3","Attenzione formato del file non conforme o grandezza non rispettata");

define("LANGLOGO1","Logo da trasmettere");
define("LANGLOGO2","Registrare il logo");
define("LANGLOGO3","Il logo <b>deve essere in formato jpg</b> e la grandezza 96px su 96px.");

define("LANGPARAM17","Definizione dei trimestri o semestri");
define("LANGPARAM18","Trimestre o Semestre");
define("LANGPARAM19","Data d'inizio");
define("LANGPARAM20","Date di fine");
define("LANGPARAM21","Primo");
define("LANGPARAM22","Secondo");
define("LANGPARAM23","Terzo");
define("LANGPARAM24","Registrare i dati trimestrali");
define("LANGPARAM25","Dati da ritenere, se la registrazione é trimestrale");
define("LANGPARAM26","Data non vlalida -- Equipe TRIADE");
define("LANGPARAM27","Informazioni Registrate -- Equipe TRIADE");
define("LANGPARAM28","trimestre");
define("LANGPARAM29","semestre");
define("LANGPARAM30","Giudizio");


define("LANGBULL5","Stampa del giudizio");
define("LANGBULL6","Continuare il trattamento");
define("LANGBULL7","Stampa deö periodo");
define("LANGBULL8","Indicare l'inizio del periodo");
define("LANGBULL9","Indicare la fine del periodo");
define("LANGBULL10","Indicate il periodo");
define("LANGBULL11","Indicate la sezione");
define("LANGBULL12","Stampare il periodo");
define("LANGBULL13","Storico");
define("LANGBULL14","<FONT COLOR='red'>ATTENZIONE</FONT></B> Necessita <B>Adobe Acrobat Reader</B>.  Software e download gratuito ");
define("LANGBULL14bis","Download");
define("LANGBULL15","Visualizzare / Eliminare");
define("LANGBULL16","Cognome allievo");
define("LANGBULL17","Professore");
define("LANGBULL18","Dettaglio delle note");
define("LANGBULL19","Giudizi del Docente di Classe");
define("LANGBULL20","RILEVAMENTO DELLE NOTE");
define("LANGBULL21","periodo");

define("LANGBULL22","primo trimestre");
define("LANGBULL23","secondo trimestre");
define("LANGBULL24","terzo trimestre");

define("LANGBULL25","primo semestre");
define("LANGBULL26","secondo semestre");

define("LANGBULL27","Giudizio del ");
define("LANGBULL28","Sezione");
define("LANGBULL29","Anno Scolastico");

define("LANGBULL30","GIUDIZIO");

define("LANGBULL31","Allievo");
define("LANGBULL32","Materie");
define("LANGBULL33","Classe");
define("LANGBULL34","Apprezzamenti, progressi, consigli per migliorare");

define("LANGBULL35","Coeff.");
define("LANGBULL36","Media");
define("LANGBULL37","Mini");
define("LANGBULL38","Maxi");
define("LANGBULL39","Assiduità e comportamento all'interno dell'istituto : ");
define("LANGBULL40","Apprezzamento globale del gruppo pedagogico : ");
define("LANGBULL41","Giudizio da conservare in modo particolare");
define("LANGBULL42","Visa dello chef dell'istituto o di un suo delegato");
define("LANGBULL43","ANNO SCOLASTICO");
define("LANGBULL44","S. & S.ra");
define("LANGOU","o"); // le ou de ou bien


define("LANGPROJ19","1° Semestre");
define("LANGPROJ20","2° Semestre");

define("LANGDISC1","Ritenuta del ");
define("LANGDISC2","Stampare le ritenute del giorno");


define("LANGDISC3","Tel. Dom. ");
define("LANGDISC4","Tel. Prof. Padre ");
define("LANGDISC5","Tel. Prof. Madre ");
define("LANGDISC6","Aggiornamento delle sanzioni nella Classe di ");
define("LANGDISC7","Nome della categoria ");
define("LANGDISC8","Nome della sanzione ");
define("LANGDISC9","Attribuita da ");
define("LANGDISC10","Motivo, informazioni, compiti da fare ");
define("LANGDISC11","Ritenuta");
define("LANGDISC11bis","Il");  // Le pour indiquer une date
define("LANGDISC11Ter","A");  // A pour indiquer une heure
define("LANGDISC12","durata");
define("LANGDISC13","<font color=red>S</font></B>untatet la casella se l\'allievo é sia stato ritenuto siasanzionato.");
define("LANGDISC14","Aggiunta di una sanzione disciplinare");
define("LANGDISC15","<B>*<I> D</B>: Telefono Domicilio, <B>P</B>: Telefono professionale del Padre, <B>M</B>: Telefono professionale della madre</I>");
define("LANGDISC16","Effettuare");
define("LANGDISC17","Tel.");
define("LANGDISC18","Visualizzare le Sanzioni");
define("LANGDISC19","Visualizzare le <b>5</B> ultime sanzioni");
define("LANGDISC20","Categoria");
define("LANGDISC21","Elenco completo di ");
define("LANGDISC22","Visualizzare le ritenute di ");
define("LANGDISC23","Visualizzare delle ritenute");
define("LANGDISC24","Visualizzazione  <b>completa</B> delle ritenute");
define("LANGDISC25","In ritenuta");
define("LANGDISC26","Ritenuta non effettuate");
define("LANGDISC27","Elenco delle sanzioni di ");
define("LANGDISC28","Visualizzazione delle Sanzioni");
define("LANGDISC29","Visualizzazione <b>completa</B> delle sanzioni");
define("LANGDISC30","Registrate il");
define("LANGDISC31","Elenco delle sanzioni di ");
define("LANGDISC32","Ritenuta non effettuta a un allievo ");
define("LANGDISC33","ATTENZIONE all'allievo ");
define("LANGDISC33bis"," é già in ritenuta per la data e l'ora indicata. ");
define("LANGDISC34","ha cumulato");
define("LANGDISC34bis","sia la categoria");
define("LANGDISC35","Eliminazione della Sanzione");
define("LANGDISC36","Elimnazione della Ritenuta");

define("LANGattente222","Pazientare");



define("LANGSUPP","Elim"); // abbreviazione di Eliminare



define("LANGCIRCU1","Gestione delle Circolari amministrative");
define("LANGCIRCU2","Aggiungere una circolare");
define("LANGCIRCU3","Elenco delle circolari");
define("LANGCIRCU4","Eliminare una circolare");
define("LANGCIRCU5","Aggiunta di una circolare amministrativa");
define("LANGCIRCU6","Soggetto");
define("LANGCIRCU7","Referenza");
define("LANGCIRCU8","Circolare");
define("LANGCIRCU9","Corpo Insegnanti");
define("LANGCIRCU10","Nella o nelle classe (i)");
define("LANGCIRCU11","<font face=Verdana size=1><B><font color=red>C</font></B>ircolare nel formato : <b>doc</b>, <b>pdf</b>, <b>txt</b>, <b>Office</b>.</FONT>");
define("LANGCIRCU12","<font face=Verdana size=1><B><font color=red>C</font></B>ircolare visibile dagli insegnanti.</FONT>");
define("LANGCIRCU13","Tutte le classi");
define("LANGCIRCU14","Ritorno al Menu");
define("LANGCIRCU15","Registrare la circulare");
define("LANGCIRCU16","Circulare non registrata");
define("LANGCIRCU17","Il file deve essere nel formato <b>txt o doc o pdf</b> e inferiore a 2Mb ");
define("LANGCIRCU18","<font class=T2>Circolare registrata</font>");
define("LANGCIRCU19","Eliminare delle Circolari amministrative");
define("LANGCIRCU20","Accesso al File");
define("LANGCIRCU21","<font color=red>R</b></font><font color=#000000>eferenza");

define("LANGCODEBAR1","Gestione del codice a barre");
define("LANGCODEBAR2","Questo modulo non funziona con il vostro server. <br> Dovete avere PHP 5 o altro per utilizzare questo modulo.");
define("LANGCODEBAR3","Ecco un Elenco dei codici a barre accessibile da TRIADE");
define("LANGCODEBAR4","Il codice a barre utilizzato per default é il ");
define("LANGCODEBAR5","Elenco");


define("LANGPUB1","Aggiungere un banner pubblicitario");
define("LANGPUB2","Desiderate pubblicare sul sito di TRIADE");
define("LANGPUB3","Effettuare una campagna publicitaria");
define("LANGPUB4","Per questo  ");
define("LANGPUB5","Siete già stati annunciati su TRIADE ");

define("LANGPROFB1","Valutazione giudizi trimestrali");
define("LANGPROFB2","Parametraggio dei vostri commenti automatici");
define("LANGPROFB3","Parametraggio");
define("LANGPROFB4","Configurazione commenti giudizi");
define("LANGPROFB5","Registrazione dei commenti");
define("LANGPROFB6","Commenti");
define("LANGPROFB7","Elenco");


define("LANGPROFC1","Calendario pianificazione delle attrezzatura");
define("LANGPROFC2","Calendario pianificazione delle Aule");


define("LANGPARAM31","Visualizazzione in modalità U.S.A.");
define("LANGPARAM32","Assiduità e compartamento all'interno dell'istituto : ");
define("LANGPARAM33","Recuperare il file PDF");

define("LANGDISC37","Aggiunta di una sanzione disciplinare");

define("LANGPROFP4","<b>Docente di classe</b> in ");
define("LANGPROFP5","Informazioni sull'allievo");
define("LANGPROFP6","Informazioni del ");
define("LANGPROFP7","fino a ");

define("LANGPROFP8","Numero totale dei ritardi");
define("LANGPROFP9","Numero dei ritardi di questo trimestre");
define("LANGPROFP10","Numero totale delle assenze");
define("LANGPROFP11","Numero delle assenze di questo trimestre");

define("LANGPROFP12","Gestione dei delegati");
define("LANGPROFP13"," nella classe di ");
define("LANGPROFP14","Genitori delegati");
define("LANGPROFP15","Coordinate");
define("LANGPROFP16","Allievo delegato");
define("LANGPROFP17","Genitore(i) delegato(i)");
define("LANGPROFP18","Allievo(i) delegato(i)");
define("LANGPROFP19","Tel."); // per telefono
define("LANGPROFP20","Mail");
define("LANGPROFP21","Complemento info mediche sull'allievo");

define("LANGETUDE1","Gestione degli studi");
define("LANGETUDE2","Assegnazione degli allievi ai vari curriculi di studio");
define("LANGETUDE3","Consultare l'Elenco degli curricoli studi assegnati");
define("LANGETUDE4","Aggiungere un curricolo di studio");
define("LANGETUDE5","Modificare un curricolo di studio");
define("LANGETUDE6","Eliminare un curricolo di studio");
define("LANGETUDE7","Consultare un curricolo di studio");
define("LANGETUDE8","Assegnare un allievo a un curricolo di studio");
define("LANGETUDE9","Modificare il curricolo di studio di un allievo");
define("LANGETUDE10","Eliminare un allievo da un curricolo di studio");
define("LANGETUDE11","Elenco dei curricoli di studio");

define("LANGETUDE12","Sorvegliante");
define("LANGETUDE13","Studio");
define("LANGETUDE14","In Aula");
define("LANGETUDE15","Settimana");
define("LANGETUDE16","Il");  		// Il indica una data
define("LANGETUDE17","a");  		// a indica un ora
define("LANGETUDE18","durante");  	//indica una durata
define("LANGETUDE19","Creazione di un'ora studio");
define("LANGETUDE20","Nome dell'ora di studio");
define("LANGETUDE21","Giorno della settimana");
define("LANGETUDE22","L'ora di studio");
define("LANGETUDE23","Durata dello studio");
define("LANGETUDE24","hh:mm");
define("LANGETUDE25","Aula di studio");
define("LANGETUDE26","Sorvegliante dell'ora di studio");
define("LANGETUDE27","L'ora di studio é registrata");
define("LANGETUDE28","Elenco delle ore di studio");
define("LANGETUDE29","Modifica dell'ora di studio");
define("LANGETUDE30","L'ora di studio contiene un elenco di allievi. Eliminare dall'elenco prima gli allievi e poi l'ora di studio");
define("LANGETUDE31","Elenco allievi");
define("LANGETUDE32","Elenco degli allievi");
define("LANGETUDE33","Assegnazione di un allievo a un'ora di studio");
define("LANGETUDE34","Scelta dell'ora di studio");
define("LANGETUDE35","Indicare la classe per l'assegnazione degli allievi a questa ora di studio");
define("LANGETUDE36","Nome dell'ora di studio");
define("LANGETUDE37","Indicate gli allievi assegnati a questa ora di studio");
define("LANGETUDE38","autorizzato ad uscire");
define("LANGETUDE39","Registrare l'ora di studio");
define("LANGETUDE40","Altra ora di studio");
define("LANGETUDE41","Modificare l'ora di studio di un allievo");
define("LANGETUDE42","Allievo presente all'ora di studio");
define("LANGETUDE43","Registrare le modifiche");
define("LANGETUDE44","Uscita autorizzata");
define("LANGETUDE45","Eliminare l'ora di studio di un allievo");

define("LANGLIST1","Creazione di una classe");
define("LANGLIST2","Elenco degli insegnanti di una classe");
define("LANGLIST3","Docente di Classe");
define("LANGLIST4","Data");
define("LANGLIST5","Elenco completo in formato PDF");
define("LANGLIST6","Docente di Classe");


define("LANGPASS1","Nuova Password");

define("LANGTRONBI1","Visualizzazione descrizione");
define("LANGTRONBI2","Modificare descrizione");
define("LANGTRONBI3","Attenzione al formato del file non conforme");
define("LANGTRONBI4","Impossibile caricare foro di grandezza non conforme");
define("LANGTRONBI5","Cognome allievo");
define("LANGTRONBI6","Nome allievo");
define("LANGTRONBI7","la foto");
define("LANGTRONBI8","Aggiungere foto");


define("LANGBASE19","Il file selezionato non é valido");
define("LANGBASE20","Allievo senza classe");
define("LANGBASE21","Numero di allievi senza classe");
define("LANGBASE22","Visualizzazione dei primi 30");
define("LANGBASE23","Cambiamento di classe per gli allievi");
define("LANGBASE24","Cambiamento Terminato");
define("LANGBASE25","PRIMA DELLE MODIFICHE CONSULTARE LA NOSTRA PAGINA DI AIUTO");
define("LANGBASE26","Cambiamento di classe per gli allievi della classe");
define("LANGBASE27","Informazioni di cambiamento di classe di un allievo");
define("LANGBASE28","<b>Nessun cambiamento.</b> <i>(Con l'opzione 'scelta ...')</i>");
define("LANGBASE29","Nessuna eliminazione d'info dell'allievo é stata effettuata.");
define("LANGBASE30","<b>Il cambiamento di classe.</b> <i>(Con indicazione di una classe)</i>");
define("LANGBASE31","Eliminazione note, ass, ritardi, discipline, dispense  dell'allievo.");
define("LANGBASE32","<b>Lascia la scuola.</b>  <i>(Con l'opzione 'Lascia la scuola')</i>");
define("LANGBASE33","Eliminazione dell'allievo nel database.");
define("LANGBASE34","Eliminazione note, ass, ritardi, discipline, dispense dell'allievo.");
define("LANGBASE35","Eliminazione messaggi interni della famiglia.");
define("LANGBASE36","Va nella classe di");
define("LANGBASE37","Lascia la scuola");
define("LANGBASE38","Convalida del(i) cambiamento(i)");
define("LANGBASE39","Scegliete un elemento");


define("LANGBASE40","Scelta del ");


// MODULO AGENDA 
define("LANGAGENDA1","Attenzione!!!\nLa nota che state per creare o modificare si sovrappone\ncon un'altra nota per gli utenti che seguiranno");
define("LANGAGENDA2","Volete eliminare la nota che é stata inserita ?");
define("LANGAGENDA3","Eliminazione di una nota, richiamo :\\n\\n - Tutte le indicazioni relative a questa nota saranno cancellate\\n - Per eliminare una sola ricorrenza, cliccate sull'immagine corrispondente a destra della nota nella pianificzione\\n\\nVolete eliminare questa nota ?");
define("LANGAGENDA4","Eliminazione di una ricorrenza, richiamo :\\n\\n - Solamanete questa ricorrenza sarà eliminata\\n - Per eliminare una nota ricorrente e tutte le sue ricorrenze, cliccate sula croce a destra della nota nella pianificazione o editate la nota e cliccate sul bottone [Eliminare]\\n\\nVolete eliminare questa ricorrenza ?");
define("LANGAGENDA5","nota con richiamo");
define("LANGAGENDA6","Eliminare una ricorrenza");
define("LANGAGENDA7","Eliminare unanota");
define("LANGAGENDA8","Appropriarsi di una nota");
define("LANGAGENDA9","Visualizzare il dettaglio");
define("LANGAGENDA10","nota personale");
define("LANGAGENDA11","nota assegnata");
define("LANGAGENDA12","nota attiva");
define("LANGAGENDA13","nota definitiva");
define("LANGAGENDA14","Oggi");
define("LANGAGENDA15","Giorno feriale");
define("LANGAGENDA16","Creare una nota");
define("LANGAGENDA17","cliccare su cambiare");
define("LANGAGENDA18","registrare una data d'anniversario");
define("LANGAGENDA19","Modificare una data d'anniversario");
define("LANGAGENDA20","Vogliate registrare il Cognome della persona");
define("LANGAGENDA21","Vogliate registrare la data di nascita della persona");
define("LANGAGENDA22","Anniversario di");
define("LANGAGENDA23","Data di nascita");
define("LANGAGENDA24","Formato gg/mm/aaaa");
define("LANGAGENDA25","Eliminare questo anniversario ?");
define("LANGAGENDA26","Eliminare");
define("LANGAGENDA27","Annullare");
define("LANGAGENDA28","registrare");
define("LANGAGENDA29","Siete sicuri di voler cancellare questo anniversario ?");
define("LANGAGENDA30","Modificare");
define("LANGAGENDA31","Anno prec.");
define("LANGAGENDA32","Mese prec.");
define("LANGAGENDA33","Andare alla data odierna");
define("LANGAGENDA34","mantenere come menu");
define("LANGAGENDA35","Mese seg.");
define("LANGAGENDA36","Anno seg.");
define("LANGAGENDA37","Selezionare una data");
define("LANGAGENDA38","Spostare");
define("LANGAGENDA39","Oggi");
define("LANGAGENDA40","A proposito del calendario");
define("LANGAGENDA41","Visualizzare per primo");
define("LANGAGENDA42","Chiudere");
define("LANGAGENDA43","Cliccare per modificare il valore");
define("LANGAGENDA44","Utente sconosciuto");
define("LANGAGENDA45","La vostra sessione é terminata !");
define("LANGAGENDA46","Questo login é già attivo;");
define("LANGAGENDA47","Vecchia Password sbagliata;");
define("LANGAGENDA48","Vogliate identificarvi per utilizzare Phenix");
define("LANGAGENDA49","La Connessione al server SQL non é andata a buon fine;");
define("LANGAGENDA50","Profilo modificato;");
define("LANGAGENDA51","nota registrata");
define("LANGAGENDA52","nota aggiornata");
define("LANGAGENDA53","nota eliminata");
define("LANGAGENDA54","Ricorrenza della nota eliminata");
define("LANGAGENDA55","Anniversario registrato;");
define("LANGAGENDA56","Anniversario aggiornato");
define("LANGAGENDA57","Anniversario eliminato");
define("LANGAGENDA58","Conto creato, potete connettervi");
define("LANGAGENDA59","La registrazione non é andata a buon fine");
define("LANGAGENDA60","Tutti i campi");
define("LANGAGENDA61","Società");
define("LANGAGENDA62","Cognome + Nome;Cognome");
define("LANGAGENDA63","Indirizzo");
define("LANGAGENDA64","Numero di telefono");
define("LANGAGENDA65","Indirizzo Email");
define("LANGAGENDA66","Commenti");
define("LANGAGENDA67","Eseguire la ricerca");
define("LANGAGENDA68","Società");
define("LANGAGENDA69","Cognome");
define("LANGAGENDA70","Nome;Cognome");
define("LANGAGENDA71","Indirizzo");
define("LANGAGENDA72","Città");
define("LANGAGENDA73","Paese");
define("LANGAGENDA74","Tel. Domicilio");
define("LANGAGENDA75","Tel. Lavoro");
define("LANGAGENDA76","Tel. Portatile");
define("LANGAGENDA77","Fax");
define("LANGAGENDA78","Email");
define("LANGAGENDA79","Email Pro");
define("LANGAGENDA80","Note / Diversi");
define("LANGAGENDA81","Gruppo");
define("LANGAGENDA82","Ripartizione");
define("LANGAGENDA83","CP");
define("LANGAGENDA84","Data di nascita");
define("LANGAGENDA85","Ricominciare");
define("LANGAGENDA86","Importare");
define("LANGAGENDA87","Impor. terminato");
define("LANGAGENDA88","contatto(i) aggiunto(i)");
define("LANGAGENDA89","Nessun contatto disponibile !");
define("LANGAGENDA90","<LI>In Outlook, procedura: <I>File</I>-&gt;<I>Esportare</I>-<I>Altri indirizzi...</I></LI>");
define("LANGAGENDA91","<LI>Scegliere <I>File testo (valori separati da virgole)</I> poi <I>Esportare</I></LI>");
define("LANGAGENDA92","<LI>Scegliere l'indirizzo dove il file sarà salvato; poi <I>Seguente</I></LI>");
define("LANGAGENDA93","<LI>Nell'elenco dei campi da esportare, selezionare :<BR>");
define("LANGAGENDA94","<I>Nome, Cognome, Indirizzo mail, Via (domicilio), Città (domicilio), Codice Postale (domicilio), Paese/regione (domicilio), Telefono personale, Telefono mobile, Telefono professionale, Fax professionale, Società </I> in seguito cliccare su <I>Terminare</I></LI>");
define("LANGAGENDA95","<LI>Recuperare il file creato tramite il formulario qui sotto e cliccare su <I>Importare</I></LI>");
define("LANGAGENDA96","Vogliate digitare una società  per la ricerca");
define("LANGAGENDA97","Vogliate digitare un Cognome o un Nome per la ricerca");
define("LANGAGENDA98","Vogliate digitare un indirizzo per la ricerca");
define("LANGAGENDA99","Vogliate digitare un numero di telefono per la ricerca");
define("LANGAGENDA100","Vogliate digitare un indirizzo Email per la ricerca");
define("LANGAGENDA101","Vogliate digitare un piccolo commento per la ricerca");
define("LANGAGENDA102","Vogliate digitare almeno un criterio per la ricerca");
define("LANGAGENDA103","Siete sicuri di voler cancellare questo contatto ?");
define("LANGAGENDA104","Anno");
define("LANGAGENDA105","Nessun padre");
define("LANGAGENDA106","Elenco delle persone<BR> ai quali potete<BR>inviare una nota");
define("LANGAGENDA107","Persona(e) possibile(i)");
define("LANGAGENDA108","Personna(e) selezionata(e)");
define("LANGAGENDA109","Precisione ddi visualizzazione");
define("LANGAGENDA110","Lasso di tempo di 30min");
define("LANGAGENDA111","Lasso di tempo di 15min");
define("LANGAGENDA112","Ora d'inizio");
define("LANGAGENDA113","Ora di fine");
define("LANGAGENDA114","Occupata;");
define("LANGAGENDA115","Parziale");
define("LANGAGENDA116","Libera");
define("LANGAGENDA117","Creare una nota iniziale ");
define("LANGAGENDA118","Dettaglio di ogni utente per la giornata odierna");
define("LANGAGENDA119","Visualizzare");
define("LANGAGENDA120","Vogliate selezionare una persona");
define("LANGAGENDA121","Vogliate selezionare un'ora di fine che si situi dopo l'ora iniziale");
define("LANGAGENDA122","Sttimana dal ");
define("LANGAGENDA123","al");
define("LANGAGENDA124","Settimana seguente");
define("LANGAGENDA125","Togliere");
define("LANGAGENDA126","Disponibilità inerente i vostri contatti per il ");
define("LANGAGENDA127","Aggiungere");
define("LANGAGENDA128","Fuori Profilo");
define("LANGAGENDA129","Vogliate selezionare un'ora finale che si situi dopo l'ora iniziale");
define("LANGAGENDA130","Precisione nella visualizzazione");
define("LANGAGENDA131","Vogliate digitare un Cognome");
define("LANGAGENDA132","Vogliate digitare un URL");
define("LANGAGENDA133","Aggiungere un favorito");
define("LANGAGENDA134","Stampa in modo Landscape consigliato");
define("LANGAGENDA135","Settimana precedente ");
define("LANGAGENDA136","Settimana");
define("LANGAGENDA137","dal");
define("LANGAGENDA138","Anniversario");
define("LANGAGENDA139","Richiamo per defaut alla creazione di una nota");
define("LANGAGENDA140","Nessun richiamo");
define("LANGAGENDA141","Richiamo");
define("LANGAGENDA142","copia via mail");
define("LANGAGENDA143","minuto(i)");
define("LANGAGENDA144","ora(e)");
define("LANGAGENDA145","giorno(i)");
define("LANGAGENDA146","Giornata tipo");
define("LANGAGENDA147","Terminato");
define("LANGAGENDA148","Telefono VF");
define("LANGAGENDA149","Interfaccia");
define("LANGAGENDA150","Planning per defaut");
define("LANGAGENDA151","Quotidiano");
define("LANGAGENDA152","Settimanale");
define("LANGAGENDA153","Mensile");
define("LANGAGENDA154","30 minuti");
define("LANGAGENDA155","15 minuti");
define("LANGAGENDA156","45 minuti");
define("LANGAGENDA157","1 ora");
define("LANGAGENDA158","Selezione automatica dell'ora di scadenza di una nota");
define("LANGAGENDA159","Condivisione del planning<BR>in consultazione");
define("LANGAGENDA160","Persone autorizzate a consultare il mio planning");
define("LANGAGENDA161","Non condiviso;");
define("LANGAGENDA162","A scelta");
define("LANGAGENDA163","Tutti");
define("LANGAGENDA164","Condivisione del planning<BR> da modificare");
define("LANGAGENDA165","Persona(e) che possono segnalarmi una nota");
define("LANGAGENDA166","Informarmi via mail quando una nota non é stata segnalata");
define("LANGAGENDA167","Eliminare la nota creata");
define("LANGAGENDA168","Eliminare questa nota che mi hanno segnalato");
define("LANGAGENDA169","Appropriarmi di questa nota che mi é stata segnalata");
define("LANGAGENDA170","Tutta la giornata");
define("LANGAGENDA171","Scelta di una nota");
define("LANGAGENDA172","Nuova nota");
define("LANGAGENDA173","é");
define("LANGAGENDA174","Durata media");
define("LANGAGENDA175","Colore");
define("LANGAGENDA176","Apparenza della nota");
define("LANGAGENDA177","Eliminare questa nota ?");
define("LANGAGENDA178","Registrare un memo");
define("LANGAGENDA179","Vogliate digitare un titolo");
define("LANGAGENDA180","Titolo");
define("LANGAGENDA181","Contenuto");
define("LANGAGENDA182","Siete sicuri di voler cancellare questo memo ?");
define("LANGAGENDA183","Registrare una nota");
define("LANGAGENDA184","La nota che intendete Modificare appartiene a una serie ricorrente");
define("LANGAGENDA185","Volete Modificare tutta la serie o solo questa nota ?");
define("LANGAGENDA186","Tutta la serie");
define("LANGAGENDA187","Unicamente e solo in questa occasione");
define("LANGAGENDA188","Nota di tutta la giornata");
define("LANGAGENDA189","Visualizzare il calendario");
define("LANGAGENDA190","Tutta la giornata");
define("LANGAGENDA191","Inizio a");  // Début à
define("LANGAGENDA192","Persona<BR>interessata");
define("LANGAGENDA193","Apparenza della nota");
define("LANGAGENDA194","Nota pubblica");
define("LANGAGENDA195","nota dettagliata condivisa a livello di planning");
define("LANGAGENDA196","menzione \"Occupata\" nella condivisione del planning");
define("LANGAGENDA197","nota privata");
define("LANGAGENDA198","Occupato(a)");
define("LANGAGENDA199","considerare come <B>non disponibile</B> nel modulo delle disponibilità");
define("LANGAGENDA200","Libero");
define("LANGAGENDA201","considerare come <B>libero</B> nel modulo delle disponibilità");
define("LANGAGENDA202","Colore");
define("LANGAGENDA203","Divisione");
define("LANGAGENDA204","Disponibilità");
define("LANGAGENDA205","Richiamo");
define("LANGAGENDA206","Nessun richiamo");
define("LANGAGENDA207","copia via mail");
define("LANGAGENDA208","a priori");  // à l'avance
define("LANGAGENDA209","Periodicità");
define("LANGAGENDA210","Nessuna");
define("LANGAGENDA211","Quotidiana");
define("LANGAGENDA212","settimanale");
define("LANGAGENDA213","Mensile");
define("LANGAGENDA214","Annuale");
define("LANGAGENDA215","Tutti i ");
define("LANGAGENDA215bis","giorni");
define("LANGAGENDA216","Tutti i giorni feriali (Lunedì al Venerdì)");
define("LANGAGENDA217","Tutti i giorni della mia settimana tipo");
define("LANGAGENDA218","Le info digitate o modificate non saranno registrate\\nSiete sicuri di voler continuare ?");
define("LANGAGENDA219","profilo");
define("LANGAGENDA220","Tutte le ");
define("LANGAGENDA221","Tutte le ");
define("LANGAGENDA221bis","settimane");
define("LANGAGENDA222","di ogni mese");
define("LANGAGENDA223","primo");
define("LANGAGENDA224","secondo");
define("LANGAGENDA225","terzo");
define("LANGAGENDA226","quarto");
define("LANGAGENDA227","ultimo");
define("LANGAGENDA228","del mese");
define("LANGAGENDA229","Il ");
define("LANGAGENDA230","Definire la data di fine");
define("LANGAGENDA231","Fine dopo"); // Fin après
define("LANGAGENDA232","Fine il");
define("LANGAGENDA233","ricorrenza(e)");
define("LANGAGENDA234","Vogliate digitare un giudizio");
define("LANGAGENDA235","Vogliate digitare una data");
define("LANGAGENDA236","Vogliate digitare un ora di fine\\na posteriori dell'ora d'inizio");  // \\n signifie un retour chariot
define("LANGAGENDA237","Vogliate selezionare una persona");
define("LANGAGENDA238","Vogliate digitare il numero di giorni\\nsuperiore o uguale a 1");
define("LANGAGENDA239","Vogliate digitare un numero ri ricorrenze\\nsuperiore o uguale a 1");
define("LANGAGENDA240","Ripetizione"); // répétition
define("LANGAGENDA241","Vogliate digitare il vostro Cognome e il vostro Nome prima di tutto");
define("LANGAGENDA242","Vogliate digitare il vostro Nome");
define("LANGAGENDA243","Dovete digitare il vostro login");
define("LANGAGENDA244","Vogliate digitare la vostra vecchia password");
define("LANGAGENDA245","Password differenti");
define("LANGAGENDA246","Una Password é obbligatoria");
define("LANGAGENDA247","Vogliate selezionare un'ora di fine\\nsuperiore all'ora d'inizio");
define("LANGAGENDA248","Eliminare questo caso");
define("LANGAGENDA249","Nota ricorrente");
define("LANGAGENDA250","Eliminare questa nota che ho creato");
define("LANGAGENDA251","Apopropriarmi di questa nota che mi é stata segnalata");
define("LANGAGENDA252","Filtrare");
define("LANGAGENDA253","Stampare questo planning");
define("LANGAGENDA254","Stampa in formato Landscape consigliato");
define("LANGAGENDA255","Nota creata da ");
define("LANGAGENDA256","Cambiare lo statuto");
define("LANGAGENDA257","Eliminare questa ricorrenza");
define("LANGAGENDA258","Eliminare questa nota che ho creato");
define("LANGAGENDA259","Eliminare questa nota che mi hanno segnalato");
define("LANGAGENDA260","una nota");
define("LANGAGENDA261","un anniversario");
define("LANGAGENDA262","un contatto");
define("LANGAGENDA263","All'utilizatore selezionato qui sotto");
define("LANGAGENDA264","Aggiungere una nota");
define("LANGAGENDA265","Ricerca");
define("LANGAGENDA266","Disponibilità");
define("LANGAGENDA267","Contatti");
define("LANGAGENDA268","Memo");
define("LANGAGENDA269","Giuidzio");
define("LANGAGENDA270","Favoriti");
define("LANGAGENDA271","Profilo");
define("LANGAGENDA272","Creazione per esport. non riuscita");
define("LANGAGENDA273","Agenda di ");
// FIN AGENDA

define("LANGL","L");  // L de lundi
define("LANGM","M");  // M de mardi
define("LANGME","M");  // M de mercredi
define("LANGJ","G");  // J de jeudi
define("LANGV","V");  // V de vendredi
define("LANGS","S");  // S de samedi
define("LANGD","D");  // D de dimanche

define("LANGL1","Lun"); // Jours sur 3 lettres
define("LANGM1","Mar");	// Jours sur 3 lettres
define("LANGME1","Mer"); // Jours sur 3 lettres
define("LANGJ1","Gio");	// Jours sur 3 lettres
define("LANGV1","Ven");	// Jours sur 3 lettres
define("LANGS1","Sab");	// Jours sur 3 lettres
define("LANGD1","Dom");	// Jours sur 3 lettres

define("LANGMOIS21","Gen");			// mois abregé
define("LANGMOIS22","Feb"); 		// mois abregé
define("LANGMOIS23","Mar");			// mois abregé
define("LANGMOIS24","Apr");				// mois abregé
define("LANGMOIS25","Mag");				// mois abregé
define("LANGMOIS26","Giu");			// mois abregé
define("LANGMOIS27","Lug");			// mois abregé
define("LANGMOIS28","Ago");		// mois abregé
define("LANGMOIS29","Set");			// mois abregé
define("LANGMOIS210","Ott");			// mois abregé
define("LANGMOIS211","Nov"); 			// mois abregé
define("LANGMOIS212","Dic"); 	// mois abregé



define("LANGPROFP22","Questo docente é già assegnato come docente di classe. \\n\\n L'Equipe TRIADE");



define("LANGSTAGE23","Nome dell'attività");
define("LANGSTAGE24","Registrare una nuova azienda");
define("LANGSTAGE25","Il Nome di questa azienda é già registrato");
define("LANGSTAGE26","Nome dell'azienda");
define("LANGSTAGE27","Contatto");
define("LANGSTAGE28","Indirizzo");
define("LANGSTAGE29","Codice postale");
define("LANGSTAGE30","Città");
define("LANGSTAGE31","Settore di attività");
define("LANGSTAGE32","Aggiungere un'attività");
define("LANGSTAGE33","Attività principale");
define("LANGSTAGE34","Telefono");
define("LANGSTAGE35","Fax");
define("LANGSTAGE36","Email");
define("LANGSTAGE37","Info");
define("LANGSTAGE38","Consultazione delle imprese");
define("LANGSTAGE39","Società");
define("LANGSTAGE40","Attività principale");
define("LANGSTAGE41","Altra ricerca");
define("LANGSTAGE42","Tel. / Fax");
define("LANGSTAGE43","Nessuna azienda con questo Nome");
define("LANGSTAGE44","Pinaificazione degli stages");
define("LANGSTAGE45","Data inizio stage");
define("LANGSTAGE46","Data fine stage");
define("LANGSTAGE47","Registrare stage");
define("LANGSTAGE48","Identificativo stage");
define("LANGSTAGE49","Modifica date di stage");
define("LANGSTAGE50","Stage");
define("LANGSTAGE51","Data stage");
define("LANGSTAGE52","Errore di registrazione");
define("LANGSTAGE53","Stage aggiornato");
define("LANGSTAGE54","Stage del ");
define("LANGSTAGE55","per la classe di");
define("LANGSTAGE56","é registrato");
define("LANGSTAGE57","Data di stage, eliminato \\n\\n L'Equipe TRIADE");
define("LANGSTAGE58","Azienda registrata \\n\\n L'Equipe TRIADE");
define("LANGSTAGE59","Modifica dell'azienda");
define("LANGSTAGE60","Consultare le aziende per attività");
define("LANGSTAGE61","Ricerca dell'azienda");
define("LANGSTAGE62","Info");
define("LANGSTAGE63","Elenco completo");
define("LANGSTAGE64","Visualizzazione delle date dello stage");
define("LANGSTAGE65","Eliminazione dell'azienda");
define("LANGSTAGE66","Azienda eliminata \\n\\n L'Equipe TRIADE");
define("LANGSTAGE67","Consultare le aziende per attività");
define("LANGSTAGE68","Nessuna azienda con questo Nome");
define("LANGSTAGE69","Visualizzazione di un allievo stagiaire");
define("LANGSTAGE70","Stampare identificativo stage");
define("LANGSTAGE71","Visualizzazione di un allievo agli stages");
define("LANGSTAGE72","&nbsp;Data&nbsp;dello&nbsp;Stage&nbsp;"); // respecter les &nbsp;
define("LANGSTAGE73","Ritorno");
define("LANGSTAGE74","Azienda");
define("LANGSTAGE75","Assegnazione di un allievo a uno stage");
define("LANGSTAGE76","Luogo stage");
define("LANGSTAGE77","Responsabile");
define("LANGSTAGE78","docente in visita");
define("LANGSTAGE79","Alloggiato");
define("LANGSTAGE80","Pensione");
define("LANGSTAGE81","Passaggio in n servizi");
define("LANGSTAGE82","Motivo del cambiamento di servizio");
define("LANGSTAGE83","Info. complementari");
define("LANGSTAGE84","Creazione registrata \\n \\n L'Equipe TRIADE");
define("LANGSTAGE85","Data della visita");
define("LANGSTAGE86","Modifica dello stage di un allievo");
define("LANGSTAGE87","Info registrate");
define("LANGSTAGE88","Eliminazione dello stage di un allievo");


define("LANGRESA62","Argomento");
define("LANGRESA63","Rifiutato");
define("LANGRESA64","Aggiungere una domanda");
define("LANGRESA65","&nbsp;Di&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;à");
define("LANGRESA66","Riservato");
define("LANGRESA66bis","da");  // suite réservé par
define("LANGRESA67","Non confermato");
define("LANGRESA68","Confermato");
define("LANGRESA69","Registrazione terminata");
define("LANGRESA70","Riservare per il ");






define("LANGNOTEUSA1","Configurazione delle attribuzioni delle note con modalità USA");
define("LANGNOTEUSA2","Questo modulo vi permette di posizionare le lettere in funzione della percentuale da attribuire a ogni nota (lettera).");
define("LANGNOTEUSA3","Esempio : da 95 a 100 --> A+ , da 87 a 94  --> A, ecc...");
define("LANGNOTEUSA4","Da");
define("LANGNOTEUSA4bis","a");
define("LANGNOTEUSA4ter","equivalente a");   //  es : Da  10 a 20 equivale a B
define("LANGNOTEUSA5","Tra la nota");
define("LANGNOTEUSA5bis","e la nota");
define("LANGNOTEUSA5ter","questo equivale a");



define("LANGABS56","Elenco delle assenze non giustificate");
define("LANGABS57","Aggiornamento per questo elenco di allievi");




define("LANGSANC1","Sanzione creata -- L'Equipe TRIADE");
define("LANGSANC2","Categoria non eliminata. Questa categoria é già stata assegnata a una sanzione o a un allievo -- Equipe TRIADE");
define("LANGSANC3","Configurazione disciplina");
define("LANGSANC4","registrazione delle categorie.");
define("LANGSANC5","Argomento della categoria");
define("LANGSANC6","registrazione dei Nomi delle sanzioni per categoria.");
define("LANGSANC7","Argomento della sanzione");
define("LANGSANC8","Configurazione inserita");
define("LANGSANC9","Avvertimento con messaggio nel caso in cui un allievo ha raggiunto il limite autorizzato.");
define("LANGSANC10","Per la categoria");
define("LANGSANC11","Avvertimento di un messaggio dopo");
define("LANGSANC12","Nb volte");
define("LANGSANC13","Creato da");
define("LANGSANC14","Data di registrazione");

// Modification de ces 2 phrases à traduire
// define("LANGPARAM1","<font class=T1>Composez votre texte pour le contenu du messaggio de l'absence pour l'envoi du courrier aux parents d'allievo. Pour une prise en compte du Cognome et du Nome de l'allievo automatiquement dans chaque document, veuillez présiser la chaîne <b>CognomeEleve</b> et <b>PreCognomeEleve</b> à l'emplacement désiré. De même possibilité d'indiquer la classe avec le mot clef <b>ClasseEleve</b>, ou la date de l'absence ABSDEBUT ou ABSFIN ainsi que la durée ABSDUREE </font><br><br>");
// define("LANGPARAM2","<font class=T1>Composez votre texte pour le contenu du messaggio de retard pour l'envoi du courrier aux parents. Pour une prise en compte du Cognome et du Nome de l'allievo automatiquement dans chaque document, veuillez présiser la chaîne <b>CognomeEleve</b> et <b>PreCognomeEleve</b> à l'emplacement désiré. De même possibilité d'indiquer la classe avec le mot clef <b>ClasseEleve</b>, ou la date du retard RTDDATE , l'heure RTDHEURE ainsi que le durée RTDDUREE </font><br><br>");


define("LANGMODIF4","Modifica di un conto");
define("LANGMODIF5","Info di Connessione");
define("LANGMODIF6","Foto d'identità");
define("LANGMODIF7","Coodinate del conto");
define("LANGMODIF8","Indirizzo");
define("LANGMODIF9","Codice postale");
define("LANGMODIF10","Comune");
define("LANGMODIF11","Tel.");
define("LANGMODIF12","Email");
define("LANGMODIF13","Modificare il conto");
define("LANGMODIF14","Conto modificato -- Equipe TRIADE");
define("LANGMODIF15","La Password di ");
define("LANGMODIF15bis"," é stata modificata.");
define("LANGMODIF16","Modifica della Password");
define("LANGMODIF17","Impossibile foto di grandezza non conforme");
define("LANGMODIF18","Riattualizzare questa foto");
define("LANGMODIF19","Aggiungere la foto");
define("LANGMODIF20","Modificare la foto");

define("LANGGRP25bis","Gestione dei gruppi");
define("LANGGRP26","Elenco dei gruppi");
define("LANGGRP27","Aggiungere un allievo a un gruppo");
define("LANGGRP28","Eliminare un allievo da un gruppo");
define("LANGGRP29","Nome del gruppo");
define("LANGGRP30","Concerne Classe(i)");
define("LANGGRP31","Modificare Elenco");
define("LANGGRP32","Aggiungere degli allievi al gruppo");
define("LANGGRP33","Aggiungere un allievo in questo gruppo");
define("LANGGRP34","Allievo nella classe di ");
define("LANGGRP35","Allievo nel gruppo");
define("LANGGRP36","Convalidare il gruppo");
define("LANGGRP37","Gruppo modificato -- Equipe TRIADE ");
define("LANGGRP38","Elenco degli allievi del gruppo ");
define("LANGGRP39","Nessun allievo in questo gruppo");

define("LANGCARNET1","Dossier delle note");
define("LANGCARNET2","Classe dell'allievo");
define("LANGCARNET3","Cliccate su <b>Cognome</b> dell'allievo");

define("LANGPASSG1","La Password deve essere di <b>8 caratteri</b> minimo,<br /> <b>alfanumerica</b> e utilizzando <b>maiuscole e minuscole</b>.");
define("LANGPASSG2","La Password non é corretta. \\n La Password deve comportare : \\n\\n -> 8 caratteri minimo, \\n -> alfanumerico, \\n -> maiuscolo e minuscolo \\n\\n L\\'Equipe TRIADE");
define("LANGPASSG3","Creazione non riuscita");



define("LANGDISC38","Aggiungere Sanzione");
define("LANGDISC39","Gestione delle discipline");
define("LANGDISC40","Punizione scolastica non effettuata.");
define("LANGDISC41","Planning delle punizioni.");
define("LANGDISC42","Punizione non assegnata ad un allievo.");
define("LANGDISC43","Configurazione.");
define("LANGDISC44","Eliminare punizioni e sanzioni");
define("LANGDISC45","Eliminare punizioni e sanzioni");
define("LANGDISC46","Elenco delle assenze e dei ritardi di una classe");
define("LANGDISC47","Indicate l'inizio del periodo");
define("LANGDISC48","Indicate la fine del periodo");
define("LANGDISC49","Indicate la sezione");
define("LANGDISC50","<br><ul>Eliminazione delle punizioni e delle sanzioni in <br>funzione dell'intervallo temporale.</ul>");
define("LANGDISC51","Tutte le classi");
define("LANGDISC52","Punizioni e sanzioni eliminate");
define("LANGDISC53","Errore ! Punizioni e sanzioni non eliminate");

define("LANGIMP53","File ASCII via SQL ");


// altra new

define("LANGSTAGE31bis","2do Settore d'Attività");
define("LANGSTAGE31ter","3zo Settore d'Attività");
define("LANGMEDIC1","Dossier medico di un allievo");
define("LANGMEDIC2","Iniziare la ricerca");
define("LANGMEDIC3","Info / Modifica");


define("LANGDISC54","Visualizzare le materie di un allievo");
define("LANGDISC55","Eliminare una Sanzione");
define("LANGDISC56","Eliminare Sanzione");

define("LANGBASE6bis","Totale  allievi nel file ");

define("LANGMODIF21","Le Password deve avere : \\n\\n - 8 caratteri minimo \\n - Alfanumerici \\n - MAIUSCOLI e minuscoli.\\n\\n Equipe TRIADE");

define("LANGMODIF22","Password : 8 caratteri - Alfanumerici - Maiusoli e minuscoli");
define("LANGPASS1bis","Confermare Password");

define("LANGMODIF23","Potete cambiare la vostra Password tramite il vostro conto TRIADE");
define("LANGMODIF24","Il conto ");
define("LANGMODIF24bis","é di in corso di convalida..");
define("LANGMODIF24ter","ora é operativo");
define("LANGMODIF25","Password non identica. \\n\\n Equipe TRIADE");

define("LANGABS58","Visualizzazione / Eliminazione Assenza - Ritardo");
define("LANGABS59","Visualizzazione completa dei ritardi");
define("LANGABS60","Per");  	// unaa durata per diverso tempo
define("LANGABS61","Visualizzazione / Modifica di una  Assenza - Ritardo");
define("LANGABS62","Visualizzazione <b>completa</B> dei rtdi e ass");
define("LANGABS63","registrati il");
define("LANGABS64","Visualizzazione dei <b>5</B> ultimi rit.e ass.");
define("LANGABS65","Visualizzazione completa delle assenze");
define("LANGABS66","Aggiornamento effettuato per questo elenco di Allievi");
define("LANGABS6bis","Elenco dei ritardi non giustificati");
define("LANGABS4bis","Elenco delle assenze o ritardi");
define("LANGABS67","<font class=T2>Nessun allievo di questa classe</font>");
define("LANGABS68","Elenco delle ass./rit. di una classe");
define("LANGABS69","Cumulo delle ass./rit. degli allievi");
define("LANGABS70","Configurazione dei motivi");
define("LANGABS71","Numero delle assenze / Cumulo");
define("LANGABS72","Numero dei ritardi / Cumulo");
define("LANGABS73","Assenze - ritardi -  della classe ");
define("LANGABS74","Effettuare l'aggiornamento");
define("LANGABS75","Nessuna ass. o rit.");
define("LANGABS76","rilevato il ");

define("LANGDEPART3","A seguito di un problema tecnico,");
define("LANGDEPART4","l'accesso al server non é disponibile. L'équipe TRIADE sta intervenendo sul server.");

define("LANGBASE3_2","Ecco l'elenco dei file che possono essere importati.");
define("LANGbasededoni21_2","Volete continuare ? \\n\\n L\'Equipe TRIADE");
define("LANGbasededon21","L'invio dei files può durare da <b>2 a 4 min</b> in funzione del numero di elementi.");
define("LANGbasededon31_2","Indicate gli argomenti che volete importare.");
define("LANGBASE10_2","Indicate gli insegnanti da aggiungere.");

define("LANGBASE16_2"," Le colonne sono rappresentate sottoforma : <b>Cognome del login ; Nome del login ; Password in chiaror</b>");
define("LANGIMP25_2","Nome dell'istituto");
// ----------------------------- //
define("LANGABS77","Segnalato il");
define("LANGSTAGE89","Sottoscrivere la convenzione dello stage");
define("LANGSTAGE90","Disdire le convenzioni dello stage");
define("LANGSTAFE91","Elenco degli allievi in azienda");
define("LANGSTAGE92","Elenco degli allievi in azienda");
define("LANGPASSG4","La Password deve essere di <b>8 caratteri</b> minimo <br /><b>alfanumerica</b>.");
define("LANGPASSG5","La Password deve essere di <b>4 caratteri</b> minimo.");
define("LANGPASSG6","La Password non é corretta. \\n La Password deve contenere : \\n\\n -> 8 caratteri minimo, \\n -> alfanumerici \\n\\n L\\'Equipe TRIADE");
define("LANGPASSG7","La Password non é corretta. \\n La Password deve contenere : \\n\\n -> 4 caratteri minimo. \\n\\n L\\'Equipe TRIADE");

define("LANGMODIF22_1","Password : 4 caratteri");
define("LANGMODIF22_2","Password : 8 caratteri - Alfanumerici ");
define("LANGMODIF22_3","Password : 8 caratteri - Alfanumerici - Maiuscolo e minuscolo");
define("LANGDEPART2","<font color=red  class=T2>ATTENZIONE, per utilizzare TRIADE, la variabile php '<strong>register_globals</strong>' deve essere su <u>Off</u>.</font><br />");


define("LANGacce15","Compito da consegnare il ");
define("LANGacce16","Compito da consegnare oggi !");
define("LANGacce17","Aggiunta di una sanzione disciplinare");

define("LANGBASE41","Eliminare tutti gli allievi prima dell'importazione");
define("LANGBASE7bis","Allievo già assegnato");
define("LANGBASE8bis","per gli allievi <u>assegnati</u> e <u>senza classe</u>");

define("LANGPER21bis","Lingua&nbsp;/&nbsp;opzione");

define("LANGASS6ter","Allievo");
define("LANGASS41","Stoccaggio");
define("LANGASS42","Parametraggio");

define("LANGIMP46bis","Password");

define("LANGIMP54","N° via");
define("LANGIMP55","indirizzo");
define("LANGIMP56","codice postale");
define("LANGIMP57","telefono");
define("LANGIMP58","email");
define("LANGIMP59","comune");

define("LANGBULL1pp","Stampa giudizi trimestrali o semestrali");
define("LANGBT43pp","Stampare Tabellone");


define("LANGMESS38","messaggio letto.");
define("LANGMESS39","messaggio non letto.");


define("LANGDISC57","Motivo&nbsp;/&nbsp;Sanzione");

define("CUMUL01","Cumulo delle assenze e ritardi di una classe per allievo");
define("CUMUL02","Cumulo delle sanzioni di una classe per allievo");
define("CUMUL03","Cumulo delle sanzioni di un allievo");
define("LANGPROJ18bis","ora(e)");
define("LANGCREAT1","Conto già esistente.");
define("Errore1","Rete Internet non disponibile per questo modulo.");
define("Errore2","Consultare il modulo di Configurazione per attivare la rete.");


define("PASSG8","Modifica Password");
define("PASSG9","La Password dell'allievo ");
define("PASSG9bis"," é stata modificata.");


define("LANGPARAM34","Site Web dell'istituto");
define("LANGLOGO3bis","Il logo <b>deve essere in formato jpg</b>");


define("LANGMAT1","Registrare materia");
define("LANGMAT2","Elenco / Modifica di una materia");
define("LANGMAT3","Eliminare materia");
define("LANGMAT4","Convalidare la modifica");
define("LANGMAT5","Materia modificata");
define("LANGMAT6","Materia già assegnata");
define("LANGCLAS1","Elenco / Modificare classe");
define("LANGCLAS2","Classe modificata");
define("LANGCLAS3","Classe già assegnata");

define("LANGDEVOIR1","per il gruppo");
define("LANGDEVOIR2","per la classe");
define("LANGDEVOIR3","Registrare un compito scolastico");
define("LANGCIRCU111","<font face=Verdana size=1><B><font color=red>D</font></B>ocumento nel formato : <b> doc</b>, <b>pdf</b>, <b>txt</b>.</FONT>");

define("LANGAFF7","Modulo per eliminare l'assegnazioni delle classi.");
define("LANGAFF8","ATTENZIONE questo modulo va utilizzato nel caso si voglia l'eliminazione dell'assegnazione,<br> distrugge tutte le note degli allievi delle classi eliminate.");
define("LANGAFF9","ATTENZIONE, le note delle classi selezionate saranno eliminate. \\n Volete continuare ? \\n\\n Equipe TRIADE");
define("LANGCREAT2","Eliminare un conto");


define("LANGPROF37","Diario.");

// news

define("LANGPARAM35","Scelta del comunicato");
define("LANGPROBLE1","risposta per email");
define("LANGPROBLE2","Tutti i campi devono essere riempiti");
define("LANGMESS37","Questo modulo non é stato convalidato dall'amministratore TRIADE.<br><br> L'Equipe TRIADE");

define("LANGPROFP23","Note scolastiche di ");
define("LANGPROFP24","del mese di");
define("LANGPROFP25","descrizione");
define("LANGPROFP26","Responsabile allievo");
define("LANGPROFP27","Info sui delegati");
define("LANGPROFP28","messaggio per la classe");
define("LANGPROFP29","Circolare per la classe");
define("LANGPROFP30","Gestione dello stage professionnale");
define("LANGPROFP31","Tabella delle medie degli allievi");
define("LANGPROFP32","Giudizi grafici degli allievi");


define("LANGLETTRELUNDI","L");	  // Lunedi
define("LANGLETTREMARDI","M");    // Martedi
define("LANGLETTREMERCREDI","M"); // Mercoledi
define("LANGLETTREJEUDI","G");    // Giovedi
define("LANGLETTREVENDREDI","V"); // Venerdi
define("LANGLETTRESAMEDI","S");   // Sabato
define("LANGLETTREDIMANCHE","D"); // Domenica



define("LANGRESA71","Riservare per il");
define("LANGRESA72","da");
define("LANGRESA73","a");
define("LANGRESA74","Info complementari");

define("LANGbasededoni52","valore accettato : <b>0</b> o Sig.<br>");
define("LANGbasededoni53","valore accettato : <b>1</b> o Sig.ra<br>");
define("LANGbasededoni54","valore accettato : <b>2</b> o Sig.ina<br>");
define("LANGbasededoni54_2","valore accettato : <b>3</b> o Ms <br>");
define("LANGbasededoni54_3","valore accettato : <b>4</b> o Mr <br>");
define("LANGbasededoni54_4","valore accettato : <b>5</b> o Mrs <br>");


define("LANGacce_dep2bis","<br><b>ATTENZIONE !!  Vérificate bene il vostro sistema d'accesso,<br> scegliete un conto corrispondente.</b>");

define("LANGNA3bis","Password genitore "); //
define("LANGNA3ter","Password allievo "); //

define("LANGELE244","Email");

define("LANGTP12","Vogliate convalidare il conto");

define("LANGMESS40","Voi avete <strong> ");
define("LANGMESS40bis"," </strong> indirizzo(i) RSS registrato(i).");  // Aggiungere "\" devant les quotes
define("LANGMESS41","Conto ");  // Compte comme "compte utilisateur".
define("LANGMESS42","Seconda connessione");
define("LANGMESS43","Ultima connessione il");

define("LANGALERT4","ATTENZIONE, Scegliete dei Cognomes di soggetti diversi.");

define("LANGMODIF26","Modificare le sotto-materie");
define("LANGPROF38","Note Trimestrali");
define("LANGPROF39","Complemento d'info");

define("LANGCIRCU21","Disp. per"); // abrev. per "Disponibile per" 

define("LANGTELECHARGE","Scaricare"); //  download

define("LANGPARENT15bis","Sanzione del");
define("LANGDISC2bis","Stampare la sanzione del giorno");

define("LANGRECH5","Indicare l'elemento o gli elementi da ricercare");
define("LANGRECH6","Suddividere nell'ordine");

define("LANGPROFP33","Completare i giudizi");
define("LANGPROFP34","Verificare i giudizi");
define("LANGPROFP35","Consultare o Modificare i commenti dei giudizi");


define("LANGPROFP36","Nessuna data trimestrale <br /><br /> assegnata per <u>questo anno scolastico</u>");
define("LANGPROFP37","Registrare i commenti");

define("LANGGRP40","Gruppo creato");
define("LANGGRP41","Ecco l'elenco degli allievi non registrati");
define("LANGGRP42","Questo gruppo esiste già");
define("LANGGRP43","Errore del file");
define("LANGGRP44","Eliminare un gruppo");
define("LANGGRP45","Importare un file");
define("LANGGRP46","Nome del gruppo esistente -- Service TRIADE");

define("LANGPARAM37","Accademia");
define("LANGAGENDA274","Festa del giorno ");
define("LANGPARAM38","Buon anniversario a ");
define("LANGEDT1","F"); // prima lettera
define("LANGEDT1bis","ile nel formato <b>xml</b> o <b>zip</b> <br>Grandezza max. del file : ");
define("Errore3","Contattare l'amministratore TRIADE per attivare la rete.");
define("LANGELE30","Cambiare la Password");
define("LANGMESS44","Inviare un msg a un allievo in: ");
define("LANGMESS5","Inviare un msg a un genitore in : ");
define("LANGMESS45","Inviare un msg a un indirizzo email : ");
define("LANGMESS2","Inviare un msg alla direzione : ");
define("LANGTRONBI9","degli allievi");
define("LANGTRONBI10","del personale");
define("LANGTRONBI11","descrizione del personale");
define("LANGTITRE15","Aggiornamento dei docenti di classe e dei docenti");
define("LANGPER7","assegnato alla classe "); //
define("LANGPROF40","Info complementari");
define("LANGPROFP38","Riempire o consultare il quaderno di classe");
define("LANGEDIT2","Tel. Portatile 1");
define("LANGEDIT3","Stato civile ");
define("LANGEDIT4","Cognome Resp. 2");
define("LANGEDIT5","Nome Resp. 2");
define("LANGEDIT6","Luogo di nascita");
define("LANGEDIT7","Stato civile ");
define("LANGEDIT8","Cognome Resp. 1");
define("LANGEDIT9","Tel. Portatile 2");
define("LANGEDIT10"," Genitore");
define("LANGEDIT11","Email Allievo");
define("LANGEDIT12","Tel. allievo");
define("LANGEDIT13","Email Tutor 2");
define("LANGEDIT14","odierno");
define("LANGEDIT15","Da 1 giorno");
define("LANGEDIT16","Da 2 giorni");
define("LANGEDIT17","Da 3 giorni");
define("LANGEDIT18","Da 4 giorni");
define("LANGEDIT19","Ritardo(i) non giustificato(i)");
define("LANGEDIT20","Tel. Portatile ");
define("LANGSMS1","Invio SMS per i ritardi da ");
define("LANGSMS2","Non indicato");
define("LANGSUPPLE","Elenco dei supplenti");
define("LANGSUPPLE1","Supplente di ");
define("LANGTITRE2","Attualità dell'istituto");
define("LANGTITRE1","Eventi");

define("LANGDISC58","Aggiungere une materia a un allievo");
define("LANGDISC59","Registrazione in modalità U.S.A.");
define("LANGDISC60","Esame ");

define("LANGBT8","Elenco / Modifica dei Direttori");
define("LANGBT9","Elenco / Modifica dell'andamento scolastico");
define("LANGBT10","Elenco / Modifica degli insegnanti");
define("LANGDIRECTION","Direzione");

define("LANGTITRE36","Gestione dei membri della Direzione");
define("LANGTITRE37","Gestione dei membri del consiglio di classe");
define("LANGTITRE38","Gestione dei docenti");
define("LANGTITRE39","Gestione dei supplenti");
define("LANGTITRE40","allievo");
define("LANGTITRE41","resp."); // per l'abbreviazione di "responsabile"
define("LANGTITRE42","tutore"); // nel quadro famigliare
define("LANGTITRE43","Gestione di un allievo");
define("LANGTITRE44","Importare un Elenco di allievi");
define("LANGTITRE45","Ricerca di un allievo");
define("LANGCHERCH1","In funzione del criterio di ricerca");
define("LANGCHERCH2","Fine della ricerca");
define("LANGCHERCH3","Numero degli elementi trovati");
define("LANGPROF3bis","Visualizzare i compiti, interrogazioni e controlli");
define("LANGTROMBI","Esportare gli elenchi degli allievi verso WellPhoto");
define("LANGPURG1","Eliminazione delle info");
define("LANGPUR2","Eliminazione delle info");
define("LANGPROFP39","Tabella delle medie annuali :");
define("LANGBLK1","Il vostro conto é disattivato.<br /><br />Avete tentato un accesso su una pagina non autorizzata.<br /><br />Per riattivare il vostro conto vogliate contattare il vostro istituto scolastico.<br /><br />L'Equipe TRIADE.");
define("LANGCARNET4","accedere");
define("LANGFORUM10bis","Vostro Nome ");
define("LANGTPROBL11","Noi ci incarichiamo di rispondervi nel più breve tempo possibile. \\n\\n  L'Equipe TRIADE ");
define("LANGTRAD1","Elenco delle operazioni effettuate");
define("LANGPARAM39","Certificato registrato");
define("LANGPARAM40","Certificato non registrato");
define("LANGPARAM41","Il file deve essere in formato <b>rtf</b> e inferiore a 2Mb");
define("LANGBASE42","Importazione del file");
define("ACCEPTER","Accettare");
define("LANGCONDITION","Accetto le Condizioni");
define("LANGPARAM42","Elenco dei ritardi o assenze");
define("LANGCARNET5","Consultare il dossier andamento scolastico");
define("LANGCARNET6","Riempire il dossier frequenza ");
define("LANGCARNET7","Riempire");
define("LANGCARNET8","Quaderno frequenza");
define("LANGCARNET9","Creare una tabella di frequenza");
define("LANGCARNET10","Modificare una tabella di frequenza");
define("LANGCARNET11","Eliminare una tabella di frequenza");
define("LANGCARNET12","Consultare una tabella di frequenza");
define("LANGCARNET13","Esportare una tabella di frequenza");
define("LANGCARNET14","Importare una tabella di frequenza");
define("LANGCARNET15","Importare");
define("LANGCARNET16","Esportare");
define("LANGCARNET17","Menu quaderno di frequenza");
define("LANGCARNET18","Nome del quaderno di frequenza");
define("LANGCONTINUER","Continuare --->");
define("LANGCARNET19","Creazione di una tabella di frequenza");
define("LANGCARNET20","Codici di valutazione che possono essere scelti dai docenti");
define("LANGCARNET21","Lettere");
define("LANGCARNET22","Ciffre");
define("LANGCARNET23","Colori");
define("LANGCARNET24","Note");
define("LANGCARNET25","(0 a 10 o 0 a 20)");
define("LANGCARNET26","Corrispondenza");
define("LANGCARNET27","acquisito");
define("LANGCARNET28","da&nbsp;confermare");
define("LANGCARNET29","non&nbsp;acquisito");
define("LANGCARNET30","in&nbsp;corso&nbsp;d'acquisizione");
define("LANGCARNET31","non&nbsp;valutato");
define("LANGCARNET32","Verde");
define("LANGCARNET33","Blu");
define("LANGCARNET34","Arancio");
define("LANGCARNET35","Rosso");
define("LANGCARNET36","periodo");
define("LANGCARNET37","periodi");
define("LANGCARNET38","Gestione del quaderno di frequenza");
define("LANGCARNET39","Numero(i) di periodo(i) che impongono la firma dei genitori, del docente e della Direzione ");
define("LANGCARNET40","Numero(i) ");
define("LANGCARNET41","Sezioni associate a questo quaderno delle frequenze");
define("LANGCARNET42","Sezioni");
define("LANGCARNET43","Massimo 4 scelte possibili (le prime 4 saranno conservate)");
define("LANGCARNET44","Quaderno di frequenza creato. Ora potete aggiungere le competenze associate a questo quaderno.");
define("LANGCARNET45","Aggiunta di un sfera di competenza ");
define("LANGCARNET46","Nome della sfera di competenza ");
define("LANGCARNET47","Questa sfera corrisponde a una rubrica delle competenze ?  ");
define("LANGCARNET48","Nome");
define("LANGCARNET49","Aggiunta di una competenza ");
define("LANGCARNET50","Modificare le caratteristiche generale del quaderno di frequenza ");
define("LANGCARNET51","Aggiungere una sfera di competenza ");
define("LANGCARNET52","Modificare una sfera di competenza ");
define("LANGCARNET53","Indicate la tabella di frequenza");
define("LANGCARNET54","Tabella di frequenza non esistente ");
define("LANGCARNET55","Consultazione di una tabella di frequenza");
define("LANGCARNET56","una tabella di frequenza");
define("LANGCARNET57","Recupero del quaderno di frequenza in formato PDF");
define("LANGCARNET58","Esportazione di una tabella di frequenza");
define("LANGCARNET59","Per recuperare una tabella di frequenza");
define("LANGCARNET60","Modifica di una tabella di frequenza");
define("LANGCARNET61","Eliminare una tabella di frequenza");
define("LANGCARNET63","Importazione di una tabella di frequenza");
define("LANGCARNET64","File da importare");
define("LANGCARNET65","Eliminare tutti gli orari prima dell'importazione ?");
define("LANGCARNET66","Importazione annullata. <br><br>Ce Nome della tabella di frequenza ! <br />Vogliate eliminare questa tabella prima di continuare l'importazione.");
define("LANGCARNET62","ATTENZIONE !!! Tutte le note della tabella di frequenza saranno cancellate!");
define("LANGEDT2","Importazione degli orari Timetabling");
define("LANGEDT3","Importazione Visual Timetabling terminata");
define("LANGEDT4","Visualizzazione / Gestione degli orari");
define("LANGEDT5","Importare gli orari Visual Timetabling");
define("LANGEDT6","Esportare Triade verso Visual Timetabling");
define("LANGEDT7","Visualizzazione / Gestione Orari");
define("LANGEDT8","Amministrare");
define("LANGEDT9","Aggiornameno degli orari");
define("LANGEDT10","Module SQLite non sopportato. Vogliate convalidare il vostro server per il funzionamento del supporto SQLite.");
define("LANGGRP47","Ricercare i gruppi");
define("LANGGRP48","Elenco dei gruppi di un allievo");
define("LANGGRP49","Elenco dei gruppi");
define("LANGDISP21","Configurazione Motivi ass./rit");
define("LANGDISP22","Registrazone dei motivi ");
define("LANGDISP23","Indicazione del motivo ");
define("LANGDISP24","Elenco dei motivi ");
define("LANGDISP25","Numero degli allievi aggiornati");
define("LANGDISP26","Il file deve avere il formato xls");
define("LANGCARNET63","Importazione Tabelle di frequenza terminato");
define("LANGCARNET64","Elenco delle sanzioni");
// News 2
define("LANGCARNET67","Aggiunta di una sanzione disciplinare");
define("LANGCARNET68","Orario");
define("LANGVIES1","Cognome della persona annessa al giudizio");
define("LANGVIES2","Coeff. della note di frequenza scolastica sul giudizio");
define("LANGVIES3","Coeff. Docente");
define("LANGVIES4","Coeff. andamento scolastico");
define("LANGVIES5","Elenco dei docenti");
define("LANGVIES6","Info Scolastiche complementari");


define("LANGVIES7","Registrare le note e commenti");
define("LANGVIES8","Stampa delle assenze di una classe");
define("LANGVIES9","Indicate il mese");
define("LANGVIES10","Indicate la classe ");
define("LANGPDF1","Un file PDF per il tutto");
define("LANGPDF2","Un file PDF per allievo");
define("LANGEDIT5bis","Nome Resp. 1");
define("LANGGRP50","Modificare il Cognome di un gruppo");
define("LANGGRP51","Nome di un gruppo");
define("LANGGRP52","Modulo Modifica");
define("LANGGRP53","Nuovo Nome di un gruppo");
define("LANGGRP54","o rilevamento delle note");
define("LANGGRP55","esame");
define("LANG1ER","1°");
define("LANG2EME","2°");
define("LANG3EME","3°");
define("LANG4EME","4°");
define("LANG5EME","5°");
define("LANG6EME","6°");
define("LANG7EME","7°");
define("LANG8EME","8°");
define("LANG9EME","9°");
define("LANGGRP56","Annotazione su");
define("LANGGRP57","Salvare");
define("LANGGRP58","Attenzione, le note degli allievi selezionati per l'eliminazione <br /> saranno eliminati in tutte le classi che appartengono a questo gruppo !!!");
define("LANGGRP59","Spuntare l(gli) allievo(i) non appartenenti più a questo gruppo ");
define("LANGGRP60","Modificare Elenco");
define("LANGPARAM3","<font class=T1>Componete il vostro testo per il certificato di frequenza scolastica. Per il rilevamento automatico del Cognome, del Nome e dell'indirizzo dell'allievo in ogni documento, vogliate precisare la catena <b>CognomeAllievo</b>, <b>Nome Allievo</b>, <b>IndirizzoAllievo</b>, <b>CodicePostaleAllievo</b> et <b>CittàAllievo</b> al giusto posto. Inoltre, avete la possibilità di indicare la classe con la parola chiave <b>ClasseAllievo</b>, la data di nascita con <b>DataNascitaAllievo</b>, luogo di nascita <b>LuogpDiNascita</b> come la dta del giorno<b>DataDelGiorno</b>. </font><br><br>");
define("LANGEDIT20bis","Elim");  // abbrev. di Eliminare  su solo 3 lettere
define("LANGGRP61","Ritorno all'aggiornamento");
define("LANGRTDJUS","Giustificato"); // per un ritardo
define("LANGABSJUS","Giustificata"); // per un'assenza
define("LANGPARAM2","<font class=T1>Componete il testo del messaggio dei ritardi da inviare ai genitori. Potete precisare le info seguenti : Cognome dell'allievo : <b>CognomeAllievo</b> - Nome dell'allievo : <b>NomeAllievo</b> - Indirizzo : <b>IndirizzoAllievo</b> - Codice postale : <b>CodicePostaleAllievo</b> - Città : <b>CittàAllievoe</b> - Classe dell'allievo : <b>ClasseAllievo</b> - Data del ritardo : <b>RTDDATE</b> - Ora del ritardo : <b>RTDORA</b> - Durata : <b>RTDDURATA</b> - Date du jour : <b>DATEDUJOUR</b>. </font><br><br>");
define("LANGPARAM1","<font class=T1>Componete il testo del messaggio per l'assenza da inviare ai genitori. Potete precisare le info seguenti : Cognome dell'allievo : <b>CognomeAllievo</b> - Nome dell'allievo : <b>NomeAllievo</b> - Indirizzo : <b>IndirizzoAllievo</b> - Codice postale : <b>CodicePostaleAllievo</b> - Città : <b>CittàAllievo</b> - Classe dell'allievo : <b>ClasseAllievo</b> - Data inizio assenza :  <b>ABSINIZIO</b> - Data di fine assenza : <b>ABSFINE</b> - Durata : <b>ABSDURATA</b>. </font><br><br>");
define("LANGGRP62","studio");
define("LANGGRP63","Mail");
define("LANGDELEGUE1","delegato");
define("LANGEDT10bis","Modulo SempliceXML non sopportato. Vogliate convalidare il vostro server per farein modo che interpreti correttamente l'esetnsione SempliceXML.");
define("LANGBULL45","Inviare un messaggio a tutti i docenti selezionati per avvisarli di riempire i giusdizi.");
define("LANGBULL46","Numero dei giudizi da completare nella classe");
define("LANGMESS46","Visualizzare in");
define("LANGMESS47","Eliminare un punizione o sanzione");
define("LANGCOUR","Mail terminato");
define("LANGCOUR1","Elenco delle punizioni non eseguite");
define("LANGCOUR2","Configurazione del dossier delle punizioni");
define("LANGPARAM43","<font class=T1>Componete il testo inerente il messaggio della punizione da inviare ai genitori. Potete precisare le seguenti info : Cognome de l'allievo : <b>CognomeEleve</b> - Nome de l'allievo : <b>PreCognomeEleve</b> - Adresse : <b>AdresseEleve</b> - Code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'allievo : <b>ClasseEleve</b> - Date de la retenue : <b>DATERETENU</b> - Heure de la retenue : <b>HEURERETENU</b> - Durée : <b>RETENUDUREE</b> - Motif : <b>RETENUMOTIF</b> -  Catégorie : <b>RETENUCATEGORY</b> - Attribuée par : <b>ATTRIBUEPAR</b> - Devoir à faire : <b>DEVOIRAFAIRE</b> - Les faits : <b>FAITS</b> - Civilité tuteur 1 : <b>CIVILITETUTEUR1</b> - Nom du responsable 1 : <b>NOMRESP1</b> Prénom du responsable 1 : <b>PRENOMRESP1</b> - Date du jour : <b>DATEDUJOUR</b> </font><br><br>");
define("RESA75","Informazioni complementari");
define("LANGCOM","Registrare tutti i vostri commenti nella vostra cartoteca.");
define("LANGCOM1","Il valore max deve essere più grande del valore min.");
define("LANGCOM2","Tutti i campi devono essere riempiti correttamente.");
define("LANGCOM3","Numero di allievi : ");
define("LANGSTAGE91","Cognome del responsabile");
define("LANGSTAGE93","Funzione del resp.");
define("LANGSTAGE94","dell'impresa");
define("LANGSTAGE95","Impresa");
define("LANGSTAGE96","Numero di elementi trovati");
define("LANGSTAGE97","Indicare un valore numerico, pf.");
define("LANGSTAGE98","Indicare la data di inizio stage, pf");
define("LANGSTAGE99","Indicare la data di fine stage, pf");
define("LANGPATIENTE","Vogliate pazientare");
define("LANGSMS3","Numero telefono del portatile");
define("LANGSMS4","150 caratteri maximum");
define("LANGSMS5","messaggio");
define("LANGSMS6","L'invio del messaggio SMS é memorizzato e accessibile dalla Direzione");
define("LANGSMS7","Invio messaggio SMS");
define("LANGSMS8","Inviare un messaggio SMS");
define("LANGSMS9","Elenco dei numri di telefono dei genitori <br> di ");
define("LANGSMS10","Inviare un sms a tutta la classe");
define("LANGSMS11","Inviare un sms a un genitore di un allievo utilizzando il suo Cognome");
define("LANGSMS12","Inviare un sms a una persona utilizzando il suo Cognome");
define("LANGSMS13","Inviare un sms a una persona utilizzando il suo numero");
define("LANGSMS14","Numero");
define("LANGbasededoni54_5","valore accettato : <b>7</b> o P <br>");
define("LANGbasededoni54_6","valore accettato : <b>8</b> o Sr <br>");
define("LANGGRP27bis","Aggiungere un allievo a più gruppi");
define("LANGGRP28bis","Aggiunta allievo a un gruppo");
define("LANGGRP29bis","Registrazione&nbsp;/&nbsp;Modifica");
define("LANGNOTEUSA6","Corrispondenza delle note per utilizzo modulo USA");
define("LANGNOTE1","Tipo d'esame");
define("LANGPARAM44","Ricevere un messaggio quando voi ricevete una info del tipo");
define("LANGMESS17bis","Config.");
define("LANGNNOTE2","Scegliere per classe");
define("LANGNNOTE3","Scegliere per Cognome");
define("LANGNNOTE4","Indicare il titolo del documento");
define("LANGBULL47","Giudizio senza sotto-materie");
define("LANGBULL48","Giudizio con sotto-materie");
define("LANGBULL49","Giudizio esame vuoto");
define("LANGMESS48","Cestino");
define("LANGMESS49","Nessun allievo assegnato a una ditta.");
define("LANGMESS50","Piano della classe");
define("LANGMESS51","Indicare le materie facoltative");
define("LANGMESS52","(Note contabilizzate nella media generale, se sono superiori a 10/20)");
define("LANGMESS53","Settimana precedente");
define("LANGMESS54","Settimana seguente");
define("LANGMESS55","Orario della classe ");
define("LANGMESS56","Nessun allievo");
define("LANGMESS57","Identificativo");
define("LANGMESS58","Questo conto non possiede nessun numero.");
define("LANGMESS59","Modificare anche le ass/rtd giustificati");
define("LANGMESS60","A");
define("LANGMESS60bis","ssente");
define("LANGMESS61","dei docenti");
define("LANGMESS62","Genitore di ");
define("LANGMESS63","oggi");  //  
define("LANGBT27bis","Registrare ass/rtd"); //
define("LANGDEPART3bis","Accesso interroto ! ");
define("LANGDEPART4bis","L'accesso al vostro TRIADE é attualmente interrotto, grazie per contattare il vostro istituto scolastico per maggiori info.");
define("LANGAIDE","Aiuto in linea");
define("LANGAIDE1","Indicate le corrispondenze tra le vostre materie di TRIADE e le materie insegnate per il brevetto dei collegi. Per effettuare questa operazione un drag&drop tra le materie da sinistra a destra.");
define("LANGAIDE2","Digitate il vostro testo per il contenuto della convenzione di stage. Per inserire elementi quali il Cognome, Nome, indirizzo, ecc..., vogliate precisare la concatenazione seguente in funzione dei vostri bisogni :");
define("LANGBREVET1","Accedere");
define("LANGCONFIG4","Essere avvertito da un messaggio quando");
define("LANGCONFIG5","Il no d'assenze non giustificate di allievo ha superato il limite ");
define("LANGCONFIG6","Il no dei ritardi non giustificati di un allievo ha superato i limiti ");
define("LANGCONFIG7","volte");
define("LANGCONFIG8","Elenco degli utenti avvertiti");

define("LANGMESS64","Persone che hanno ricevuto questo mesaggio");
define("LANGMESS65","Elenco dei regolamenti inetrni");
define("LANGMESS66","Il Direttore");
define("LANGMESS67","Ho preso conoscenza dei diversi documenti qui sotto");
define("LANGMESS68","Accetto il o i regolamento(i) interno(i)");
define("LANGMESS69","Accetto le condizioni generali d'insegnamento");
define("LANGMESS70","Regolamento accessibile ai docenti");
define("LANGMESS71","Consultare lo statuto dei regolamento");
define("LANGMESS72","Stampare la scheda dei regolamenti");
define("LANGMESS73","Elenco delle fatture non pagate o pagamento(i) incompleto(i)");
define("LANGMESS74","Scheda dei regolamenti");
define("LANGacce_dep2ter","<br><b>ATTENZIONE !  Verificate il vostro modo d'acceso, scegliete il vostro conto corrispondente.</b>");
//NEW NON CORRIGE


define("LANGMESS75","Retour menu principal");
define("LANGMESS76","Correspondance");
define("LANGMESS77","(devoir, contrôle, examen)");
define("LANGMESS78","Trier par ");
define("LANGMESS79","Notes visibles pour les élèves le ");
define("LANGMESS80","vie scolaire");
define("LANGMESS81","Connexion en cours");
define("LANGMESS82","Moyenne");
define("LANGMESS83","Moyenne de classe");
define("LANGMESS84","Max");
define("LANGMESS85","Min");
define("LANGMESS86","Aucune date trimestrielle affectée");
define("LANGMESS86bis","pour");
define("LANGMESS86ter","cette année scolaire");
define("LANGMESS87","Note des devoirs de");

define("LANGMESS88","Cahier de texte enregistré  -- Service Triade");
define("LANGMESS89","Cahier de texte en ");
define("LANGMESS90","Penser à enregistrer votre contenu avant de changer d'onglet.");
define("LANGMESS91","Consultation de la semaine");
define("LANGMESS92","Contenu du cours");
define("LANGMESS93","Fichier joint");
define("LANGMESS94","Piece Jointe");
define("LANGMESS95","Objectif du cours");
define("LANGMESS96","Devoir à faire pour le ");
define("LANGMESS97","non indiqué");
define("LANGMESS98","Devoir à faire");
define("LANGMESS99","Bloc-Notes");
define("LANGMESS100","Consultation compléte");
define("LANGMESS101","Validation");
define("LANGMESS102","Consultation");
define("LANGMESS103","Temps estimé pour ce travail ");
define("LANGMESS104","Temps de travail estimé à ");
define("LANGMESS105","Fichier ");
define("LANGMESS106","Modification ");
define("LANGMESS107","Supprimer cette fiche ");
define("LANGMESS108","Temps de travail total estimé ");
define("LANGMESS109","du"); // notion de date du xxxx au xxxx
define("LANGMESS110","au"); // notion de date du xxxx au xxxx
define("LANGMESS111","Format PDF"); 
define("LANGBT288","Consulter / Modifier");
define("LANGSITU1","Marié(e)"); //
define("LANGSITU2","Divorcé(e)"); //
define("LANGSITU3","Veuf"); //
define("LANGSITU4","Veuve"); //
define("LANGSITU5","Concubin"); //
define("LANGSITU6","PACS"); //
define("LANGSITU7","Célibataire");
define("LANGFIN002","Echéancier");//
define("LANGFIN003","Echéancier");//
define("LANGFIN004","Aucune date de configurée");//
define("LANGCONFIG","Configurer");//

define("LANGMESS112","Commentaire bulletin trimestre/semestre");
define("LANGMESS113","Choix du commentaire");
define("LANGMESS114","Commentaire brevet des collèges");
define("LANGMESS115","Visualisation du bulletin de classe");
define("LANGMESS116","Accèder");
define("LANGMESS117","Série");
define("LANGMESS118","Passer en mode étendu");
define("LANGMESS119","Appréciations, Conseils pour progresser");
define("LANGMESS120","Points d'appui. Progrès. Efforts");
define("LANGMESS121","Ecarts par rapport aux objectifs attendu");
define("LANGMESS122","Conseils pour progresser");
define("LANGMESS123","Moyenne de la classe");
define("LANGMESS124","Commentaire précédent");
define("LANGMESS125","Ajout dans liste"); // vérif. pas de quote (') 
define("LANGMESS126","Enregistrer le commentaire"); // vérif. pas de quote (') 
define("LANGMESS127","Revenir et cliquer sur"); // vérif. pas de quote (') 
define("LANGMESS128","Enregistrement");  // vérif. pas de quote (') 
define("LANGMESS129","Consulter");
define("LANGMESS130","Moy. Précédente");
define("LANGMESS131","Enregistrer les commentaires");
define("LANGMESS132","Patientez S.V.P.");
define("LANGMESS133","Commentaire vide");
define("LANGMESS134","commentaire non enregistré");
define("LANGMESS135","Appréciation pour le bulletin trimestriel classe");
define("LANGMESS136","cliquez-ici");
define("LANGMESS137","Information Scolaire Complémentaire");
define("LANGMESS138","Saisir autres commentaires pour les bulletins");

//-----------------Traduction Sam le 06/06/2014
//-----------------messagerie_brouillon.php
define("LANGMESS139","Messagerie brouillon");
define("LANGMESS140","Préparer un brouillon ");
define("LANGMESS141","Accès");
define("LANGMESS142","Valider un brouillon");
define("LANGMESS143","Les messages brouillons sont visibles par tous les membres de la direction");

//------------------param.php
define("LANGMESS144","Signature du directeur");
define("LANGMESS145","Année scolaire");
define("LANGMESS156","Pays");
define("LANGMESS159","Choix du site");
define("LANGMESS160","Nouveau site");
define("LANGMESS177","Département ");
//------------------definir_trimestre.php
define("LANGMESS146","Enregistrement au format semestriel.");
define("LANGMESS147","Toutes les classes");
define("LANGMESS148","Liste des périodes trimestrielles ou semestrielles ");
define("LANGMESS149","Modifier");
define("LANGMESS150","Supprimer");
define("LANGMESS157","Trimestre");
define("LANGMESS158","Classe");
//-----------------probleme_acces_2.php
define("LANGMESS151","Identifiez votre compte");
define("LANGMESS152","Veuillez d'abord identifier votre compte pour réinitialiser votre mot de passe.");
define("LANGMESS153","Demande de mot de passe");
//-----------------geston_groupe.php
define("LANGMESS154","Création de groupe");
define("LANGMESS155","Liste des groupes des enseignants");
//-----------------gestcompte.php
define("LANGMESS161","Gestion de votre compte");
//-----------------messagerie_reception.php
define("LANGMESS162","Gestion de votre compte");
//------------------gestion_groupe.php
define("LANGBT53","Entrée"); // traduit par sam le 09/06/2014
define("LANGMESS163","Vérification des groupes");
//-------------------messagerie_suppression.php
define("LANGMESS164","Boite de suppression");
define("LANGMESS165","Archiver dans");
//-------------------messagerie_reception.php
define("LANGMESS166","Boite de reception");
//-------------------parametrage.php
define("LANGMESS167","Paramétrage de votre compte");
define("LANGMESS168","Actualités");
define("LANGMESS169","Réservation Salle / Equipement");
define("LANGMESS170","Messagerie Triade");
define("LANGMESS171","(Indiquer votre  email)");
define("LANGMESS172","(Numéro de portable)");
// TTTTTTTT
//-------------------messagerie_envoi.php
define("LANGMESS173","Message à un groupe ");
define("LANGMESS174","Message aux délégués :");
define("LANGMESS175","Message à un membre du personnel : ");
define("LANGMESS176","Message à un tuteur de stage : ");
//-------------------creat_admin.php
define("LANGMESS178","Civ.");
define("LANGMESS179","Indice&nbsp;salaire");
//-------------------creat_tuteur.php
define("LANGMESS180","Création d'un compte tuteur de stage");
define("LANGMESS181","Liste / Modification d'un tuteur de stage");
define("LANGMESS182","Gestion des membres Tuteur de stage");
define("LANGMESS183","Entreprise liée");
define("LANGMESS184","En qualité de ");
//--------------------creat_personnel.php
define("LANGMESS185","Gestion des membres du Personnel");
define("LANGMESS186","Création d'un compte personnel"); // "Cr&eacute;ation d'un compte personnel"
//--------------------creat_eleve.php
define("LANGMESS187","Rechercher");
define("LANGMESS188","Importer");
define("LANGMESS189","Supprimer");
define("LANGMESS190","Lv1/Spé :");
define("LANGMESS191","Lv2/Spé :");
define("LANGMESS192","Boursier");
define("LANGMESS193","Inscription au BDE");
define("LANGMESS194","Inscription à la bibliothèque");
define("LANGMESS195","Montant Bourse");
define("LANGMESS196","Indemnité Stage");
define("LANGMESS197","Code comptabilité ");
define("LANGMESS198","Adresse");
define("LANGMESS199","Téléphone");
define("LANGMESS200","Tél. Portable");
define("LANGMESS201","E-mail Etudiant");
define("LANGMESS202","E-mail universitaire");
define("LANGMESS203","Situation Familiale");
define("LANGMESS204","Copier adresse");
define("LANGMESS205","Classe antérieure");
//--------------------creat_class.php
define("LANGMESS206","Intitulé de la classe");
define("LANGMESS207","Ecole");
//--------------------creat_matiere.php
define("LANGMESS208","Format court");
define("LANGMESS209","Format long");
define("LANGMESS210","Code matière");
//--------------------reglement.php
define("LANGMESS211","Réglement intérieur");
define("LANGMESS212","Ajouter un règlement");
define("LANGMESS213","lister le/les règlements");
define("LANGMESS214","Supprimer un règlement");
//--------------------sms.php
define("LANGMESS215","Gestion des SMS");
define("LANGMESS216","Membre");
define("LANGMESS217","Direction");
define("LANGMESS218","Enseignant");
define("LANGMESS219","Vie Scolaire");
define("LANGMESS220","Personnel");
//--------------------Codebar0.php
define("LANGMESS221","Code barre :");


//--------------------vatel_gestion_ue.php
define("LANGMESS222","Gestion des Unités d'enseignements");
define("LANGMESS223","Création d'une unité d'enseignement");
define("LANGMESS224","Lister/Modifier");
//--------------------base_de_donne_importation.php
define("LANGMESS225","Fichier Excel");
define("LANGMESS226","Fichier XML");
define("LANGMESS227","Code barre");
//--------------------edt.php
define("LANGMESS228","Suppression d'une période ");
define("LANGMESS229","Ajustement des horaires ");
define("LANGMESS230","Période visible sur l'EDT");
define("LANGMESS231","Importer image ou pdf : ");
define("LANGMESS232","(format  de l'image : jpg et moins de 2Mo )");
define("LANGMESS233","EDT de la classe : ");
//--------------------export.php
define("LANGMESS234","Exportation des données");
define("LANGMESS235","Informations à exporter");
define("LANGMESS236","Personnel");
define("LANGMESS237","Choix de l'extraction : ");
//--------------------export.php
define("LANGMESS238","Nom de l'enseignant ");
define("LANGMESS239","Exportation au format PDF : ");
define("LANGMESS240","Exporter");
//--------------------commaudio.php
define("LANGMESS241","Sujet : ");
define("LANGMESS242","Fichier audio : ");
//--------------------consult_classe.php
define("LANGMESS243","Impression ");
define("LANGMESS365","&nbsp;Demi&nbsp;Pension&nbsp;");
define("LANGMESS366","&nbsp;Interne&nbsp;");
define("LANGMESS367","&nbsp;Externe&nbsp;");
define("LANGMESS368","&nbsp;Inconnu&nbsp;");
//--------------------resr_admin.php
define("LANGMESS244","Réserver via E.D.T.");
//--------------------carnetnote.php
//------------modif nom de l'enseignant---LANGMESS238
//--------------------publipostage.php
define("LANGMESS245","Type membre : ");
define("LANGMESS246","Parents");
define("LANGMESS247","Etudiants");
define("LANGMESS248","Type adresse :");
define("LANGMESS249","Tuteur");
define("LANGMESS327","Publipostage");
define("LANGMESS328","Afficher la civilit&eacute; des &eacute;tudiants : ");
define("LANGMESS329","Afficher matricule : ");
define("LANGMESS330","Afficher Classe : ");
define("LANGMESS331","Afficher Adresse : ");


////////////////////////////////////////////////////////////////////// A revoir
//--------------------ficheeleve3.php
define("LANGMESS250","Listing Classe");
define("LANGMESS251","Envoyer un SMS");
define("LANGMESS252","Modifier Fiche");
define("LANGMESS253","Affecter &agrave; un stage");
define("LANGMESS254","Bloquer ce compte");
define("LANGMESS255","Débloquer ce compte");
define("LANGMESS259","Renseignements");
define("LANGMESS260","Carnet de notes");
define("LANGMESS261","Vie Scolaire");
define("LANGMESS262","Disciplines");
define("LANGMESS263","Opérations effectuées");
define("LANGMESS264","Info. Tuteur 1");
define("LANGMESS265","Info. Tuteur 2");
define("LANGMESS266","Info. Etudiant");
define("LANGMESS267","Archives");
define("LANGMESS268","Info. médicales");
define("LANGMESS269","info. compl.");
define("LANGMESS270","Nom :");
define("LANGMESS271","Prénom :");
define("LANGMESS272","Classe :");
define("LANGMESS273","Date&nbsp;de&nbsp;nais.&nbsp;:");
define("LANGMESS274","Nationalité&nbsp;:");
define("LANGMESS275","Lieu&nbsp;naissance&nbsp;:");
define("LANGMESS276","Boursier :");
define("LANGMESS277","Numéro&nbsp;Etudiant&nbsp;:");
define("LANGMESS278","Lv1/Spé :");
define("LANGMESS279","Lv2/Spé :");
define("LANGMESS280","Option :");
define("LANGMESS281","Régime :");
define("LANGMESS282","N°&nbsp;Rangement&nbsp;:");
define("LANGMESS283","Contact&nbsp;:");
define("LANGMESS284","Situation&nbsp;familiale&nbsp;:");
define("LANGMESS285","Adresse&nbsp;:");
define("LANGMESS287","Code&nbsp;Postal&nbsp;:");
define("LANGMESS288","Ville&nbsp;:");
define("LANGMESS289","Email&nbsp;:");
define("LANGMESS290","Téléphone&nbsp;:");
define("LANGMESS291","Profession&nbsp;:");
define("LANGMESS292","Tél.&nbsp;Prof.&nbsp;:");
define("LANGMESS293","Sexe&nbsp;:");
define("LANGMESS294","Classe&nbsp;ant.&nbsp;:");
define("LANGMESS295","Année&nbsp;Scolaire");
define("LANGMESS296","Trim&nbsp;/&nbsp;Sem");
define("LANGMESS297","Bulletin");
define("LANGMESS298","Effectué&nbsp;le");
define("LANGMESS308","Permission non accordées");
define("LANGMESS309","Ajouter une information");
define("LANGMESS310","Entretien individuel");
define("LANGMESS311","Planifier abs/rtd");
define("LANGMESS312","Modifier abs/rtd");
define("LANGMESS313","Supprimer abs/rtd");
define("LANGMESS320","$email_eleve / $emailpro_eleve");
define("LANGMESS321","$tel_eleve / $tel_fixe_eleve");

//--------------------elevesansclasse.php
define("LANGMESS256","Save");
//--------------------consult_classe.php
define("LANGMESS257","All classes.");
//--------------------ficheeleve.php
define("LANGMESS258","Search");
//--------------------newsactualite.php
define("LANGMESS299","    Titre : ");
define("LANGMESS300","Votre TRIADE n'est pas configuré en accès Internet, veuillez consulter votre compte administrateur Triade pour valider l'option de la connexion Internet.");
define("LANGMESS365","Actualités  de la 1er page");
//--------------------actualiteetablissement.php
//--------------------newsdefil.php
//--------------------commaudio.php // Bouton Parcourir
//--------------------commvideo.php
define("LANGMESS301","Lien de la video : ");
define("LANGMESS302","ou Lien Youtube : ");
//--------------------emmargement.php
// ICIIIIIIIICICICICICIC
define("LANGMESS303","Gestion des émargements ");
define("LANGMESS304","Au niveau de la classe");
define("LANGMESS305","Emargement vierge");
define("LANGMESS306","Emargement vierge d'examen");
define("LANGMESS306","Emargement vierge d'examen");
define("LANGMESS307","Au niveau du groupe");
define("LANGMESS314","Emargement du jour ");
define("LANGMESS315","Emargement&nbsp;du&nbsp;");
define("LANGMESS316","Pour la classe : ");
define("LANGMESS317","Enseignant : ");
define("LANGMESS318","Tous les enseignants : ");
define("LANGMESS319","Hauteur des cellules des élèves");
//--------------------trombinoscope0.php
define("LANGMESS322","Imprimer au format PDF");
define("LANGMESS323","Importer les photos au format ZIP");
//--------------------chgmentclas.php
define("LANGMESS324",": notes, absences, retards, dispences, sanctions, retenues, Brevets, Commentaires bulletin de l'élève, droits de scolarité, plan de classe, Brevets, Affectation stage");
//------LANGASS10-- Variable pour suppression
//--------------------certificat.php
define("LANGMESS325","Paramétrage  manuel : ");
define("LANGMESS326","Paramétrage  import : ");
//define("LANGMESS331","Publipostage");
//--------------------visa_direction.php
define("LANGMESS332","Type du bulletin : ");
// VALIDER CHANGER PAR ENTER-->LANGMESS116
define("LANGMESS333","Valider");
define("LANGMESS334","Annuel"); /// voir si posible de mettre une variable
///////////////////////
//--------------------list_classe.php----- Voir comment changer le bouton Modifier
//--------------------list_matiere.php---- Voir comment changer le bouton Modifier
//--------------------listepreinscription.php
define("LANGMESS335","Liste des pré-inscriptions");
//--------------------reglement_ajout.php
define("LANGMESS336","Règlement intérieur");
define("LANGMESS337","règlement");
define("LANGMESS338","la ou les classe(s)");
define("LANGMESS339","la ou les classe(s)");
//--------------------affectation_visu.php
define("LANGMESS340","Année/Trimestre/Semestre");
define("LANGMESS341","Toute l'année");
define("LANGMESS342","Trimestre 1 / Semestre 1");
define("LANGMESS343","Trimestre 2 / Semestre 2");
define("LANGMESS344","Trimestre 3");
//--------------------affectation_modif_key.php
//----Modidifier le bouton suivant par next
//--------------------reglement_ajout.php
//--------------------reglement_liste.php
// comment modifier le lien Reglement interieur
//----------------/reglement_supp.php
define("LANGMESS345","Visualiser");
//-----------------vatel_list_ue.php
define("LANGMESS346","Gestion des Unités d'Enseignements");
define("LANGMESS347","Filtre : ");
define("LANGMESS348","Modifier");
define("LANGMESS349","Supprimer");
define("LANGMESS350","Nom UE");
define("LANGMESS351","Sem.");
define("LANGMESS352","Création d'une UE");

//----------------creat_groupe.php
define("LANGMESS353","Fichier excel");
define("LANGMESS354","Contenu du fichier excel");
//----------------visa_direction2.php
define("LANGMESS355","Commentaire des enseignants");
define("LANGMESS356","Visa direction");
//----------------imprimer_tableaupp.php
define("LANGMESS357","Impression tableau de notes trimestriel ou semestriel");
define("LANGMESS358","Afficher le classement ");
define("LANGMESS359","Afficher les colonnes vides ");
define("LANGMESS360","Regroupement par module ");
define("LANGMESS361","Afficher les matières ");
define("LANGMESS362","Tableau des différentes moyennes au format excel");
define("LANGMESS374","Jusqu'au :");
define("LANGMESS375","Fichier Excel");
//------------------affectation_creation_key.php
//------------------affectation_visu2.php
define("LANGMESS363","Visu<i>*</i>");
define("LANGMESS364","Unité Ens.");
//------------------entretien.php
define("LANGMESS369","Journal d'entretiens individuels");
define("LANGMESS370","Journal d'entretiens groupés ");
define("LANGMESS371","Tableau récapitulatif");
define("LANGMESS372","&nbsp;Enseignants&nbsp;");
define("LANGMESS373","&nbsp;Nombre&nbsp;d'heures&nbsp;");
//------------------base_de_donne_key.php
define("LANGMESS376","Pour modifier / changer votre code d'accès, merci de consulter votre compte ");
define("LANGMESS377","administrateur Triade");
define("LANGMESS378","puis le module 'code d'accès'");
//------------------chgmentClas0.php
// année = Year
define("LANGMESS379","pas d'année");
define("LANGMESS380","Choix de la classe");
//------------------chgmentClas00.php
// année et pas d'année 
define("LANGMESS381","Choix des classes :");
define("LANGMESS383","Changement de classe pour les élèves en ");
define("LANGMESS384","Passage pour l'année scolaire");
define("LANGMESS385","Sans classe");
//------------------brouillon_reception.php
define("LANGMESS382","Liste des messages brouillons");
//------------------imprimer_trimestre.php
define("LANGMESS386","Bulletin&nbsp;personnalisé");
define("LANGMESS387","Bulletin définit pour les enseignants (et parents  prochainement)");
define("LANGMESS388","Visible pour la classe");
define("LANGMESS389","Autoriser l'accès aux bulletins pour les enseignants");
//LANGMESS389

define("LANGMESST390","Merci de renseigner les informations nécessaires à Triade pour le site numéro 1 !!<br>Merci de confirmer en validant ou revalidant le formulaire suivant.");
define("LANGMESST391","Supprimer site");
define("LANGMESST392","Carnet de suivi");
define("LANGMESST393","COMPTE BLOQUE");
define("LANGMESST394","COMPTE EN PERIODE PROBATOIRE");
define("LANGMESST395","Supprimer la période probatoire");
define("LANGMESST396","Mise en période probatoire");
define("LANGMESST397","Saisie&nbsp;par");
define("LANGMESST398","Enregistrer cette liste");
define("LANGMESST399","Effectuer une recherche complexe");
define("LANGMESST700","Supprimer message en cours");
define("LANGMESST701","Actualités  de la 1er page");
define("LANGMESST702","Titre de la vidéo");
define("LANGMESST703","Copier/coller le lien ");
define("LANGMESST704","Indiquer le destinateur du message à transmettre.");
define("LANGMESST705","Message non envoyé ! \\n \\n Vous n'avez pas l'autorisation d'envoyer un message à cette personne.\\n\\n L'Equipe TRIADE. ");
define("LANGTMESS400","Votre demande a bien été pris en compte,");
define("LANGTMESS401","Veuillez consulter votre adresse email");
define("LANGTMESS402","Aucun compte pour cet email !!");
define("LANGTMESS403","merci de contacter votre administrateur en cliquant ");
define("LANGTMESS404","sur ce lien ");
define("LANGTMESS405","Contacter l'administrateur TRIADE ");
define("LANGTMESS406","Vérifier");
define("LANGTMESS407","Vérification / Check groupes");
define("LANGTMESS408","Email non valide !!");
define("LANGTMESS409","Merci d'indiquer un email valide.");
define("LANGTMESS410","Les emails <b>hotmail</b> ne sont pas reconnues par nos serveurs.");
define("LANGTMESS411","Merci d'indiquer une autre adresse email.");
define("LANGTMESS412","Nouveau Répertoire");
define("LANGTMESS413","Message déjà imprimé");
define("LANGTMESS414","Pièce jointe");
define("LANGTMESS415","Archiver dans");
define("LANGTMESS416","Boite de ");
define("LANGTMESS417","Boite de Réception");
define("LANGTMESS418","Mode Classique");
define("LANGTMESS419","Messages envoyées ");
define("LANGTMESS420","Vos répertoires ");
define("LANGTMESS421","via le mail ");
define("LANGTMESS422","via SMS ");
define("LANGTMESS423","via RSS ");
define("LANGTMESS424","Module lors de votre connexion");
define("LANGTMESS425","Module d'absenteisme");
define("LANGTMESS426","Liste d'une UE ( Modif / Suppr )");
define("LANGTMESS427","PDF EDT Enregistré");
define("LANGTMESS428","L'Equipe Triade");
define("LANGTMESS429","Image EDT Enregistrée");
define("LANGTMESS430","EDT Supprimé");
define("LANGTMESS431","Nom de structure déjà utilisé");
define("LANGTMESS432","Exportation format");
define("LANGTMESS433","&nbsp;Total&nbsp;");
define("LANGTMESS434","colonnes");
define("LANGTMESS435","Tuteur de stage");
define("LANGTMESS436","Afficher Adresse");
define("LANGTMESS437","Tous les parents");
define("LANGTMESS438","Tous les ");
define("LANGTMESS439","Lister / Modification");
define("LANGTMESS440","ajouter");
define("LANGTMESS441","Rangement / Info.");
define("LANGTMESS442","par mois");
define("LANGTMESS443","Nb mois");
define("LANGTMESS444","Code comptabilité");
define("LANGTMESS445","Universitaire");
define("LANGTMESS446","Editer le RIB");
define("LANGTMESS447","Donnée déjà enregistrée");
define("LANGTMESS448","Site rattaché");
define("LANGTMESS449","Définition compléte");

define("LANGCIV0","M.");
define("LANGCIV1","Mme");
define("LANGCIV2","Mlle");
define("LANGCIV3","Ms");
define("LANGCIV4","Mr");
define("LANGCIV5","Mrs");
define("LANGCIV6","M. ou Mme");
define("LANGCIV7","Sr");
define("LANGCIV8","Général");
define("LANGCIV9","Colonel");
define("LANGCIV10","Lieutenant-Colonel");
define("LANGCIV11","Commandant");
define("LANGCIV12","Capitaine");
define("LANGCIV13","Lieutenant");
define("LANGCIV14","Sous-Lieutenant");
define("LANGCIV15","Aspirant");
define("LANGCIV16","Major");
define("LANGCIV17","Adjudant-Chef");
define("LANGCIV18","Adjudant");
define("LANGCIV19","Sergent-Chef");
define("LANGCIV20","Sergent");
define("LANGCIV21","Caporal-Chef");
define("LANGCIV22","Caporal");
define("LANGCIV23","Aviateur");
define("LANGCIV24","Dr");

define("LANGMESS391","Mode Classique");
define("LANGMESS392","Liste des destinataires");
define("LANGMESS393","Effacer liste"); // lg 262
define("LANGMESS394","Sélectionnez un fichier");
define("LANGMESS395","Liste des membres de la direction");
define("LANGMESS396","Visualiser / Modifier");
define("LANGMESS397","Liste de la Vie Scolaire");
define("LANGMESS398","Désactiver compte");
define("LANGMESS399","Activer compte");
define("LANGMESS400","Permission");
define("LANGMESS401","Liste des comptes personnels ");
define("LANGMESS403","Liste Tuteur de stage");
define("LANGMESS404","Liste / Modifier");
define("LANGMESS405","M.");
define("LANGMESS406","Mme");
define("LANGTMESS450","Traduction autre langue");
define("LANGTMESS451","Actuellement le fichier import sert de référence à la création du certificat.");
define("LANGTMESS452","Récupérer");
define("LANGTMESS453","Certificat numéro :");
define("LANGTMESS454","Ajouter une inscription :");
define("LANGTMESS455","Nouveau");
define("LANGTOUS","Tous");
define("LANGTMESS456","En attente");
define("LANGTMESS457","Accepté");
define("LANGTMESS458","Réfusé");
define("LANGTMESS459","Décision");
define("LANGTMESS460","Transferer liste en classe");
define("LANGTMESS461","Destruction fiche(s)");
define("LANGTMESS462","Attention !, le règlement doit être au format pdf et ne pas dépasser deux méga octé.");
define("LANGTMESS463","Cette option permet aux enseignants, de valider le réglement au moment de leur premiere connexion.");
define("LANGTMESS464","Elève(s) au total.");
define("LANGTMESS465","Commentaire pour le");
define("LANGTMESS466","Afficher les sous-matières");
define("LANGTMESS467","Prise en compte note examen");
define("LANGTMESS468","Prise en compte coef à zéro");
define("LANGTMESS469","Si le coefficient est à zéro, les points supérieurs à 10 seront pris en compte.");
define("LANGTMESS470","Spécif");
define("LANGTMESS471","Etude de cas");
define("LANGTMESS472","Visu : Visualisation dans le bulletin");
define("LANGTMESS473","pour l'année :");
define("LANGTMESS474","changer");
define("LANGTMESS475","Fichier Taille Max");
define("LANGTMESS476","Liste / Modifier un compte personnel");
define("LANGTMESS477","Liste / Modifier un tuteur de stage");

//--------------------list_classe.php
//--------------------modif_classe.php
define("LANGMESS407","Modification d'une classe");
define("LANGMESS408","Activer la classe");
define("LANGMESS409","Désactiver la classe");
define("LANGMESS410","Définition complète");
define("LANGMESS411","Site rattaché");
//--------------------affectation_creation.php
//-------------------publipostage.php
define("LANGMESS412","Type de vignette");
define("LANGMESS413","Type de membre");
//-------------------list_matiere.php
//-------------------modif_matiere.php
define("LANGMESS414","Type de membre");
define("LANGMESS415","Code matière");
define("LANGMESS416","Nom de la sous-matière");
define("LANGMESS417","Supprimer sous matière");
define("LANGMESS418","Désactiver matière");
define("LANGMESS419","Activer matière");
//-------------------triadev1/circulaire_liste.php
define("LANGMESS420","Référence");
//-------------------visu_retard_parent.php
//-------------------messagerie_envoi.php
define("LANGMESS421","Vous n'avez pas l'autorisation d'envoyer un message à cette personne.");
//-------------------information.php
define("LANGMESS422","Informations scolaires");
//-------------------parametrage.php
define("LANGMESS423","Module lors de votre connexion ");
define("LANGMESS424","Actualités");
define("LANGMESS425","Module d'absenteisme");
//-------------------retardprof.php
define("LANGMESS426","Indiquez des élèves en retard ou absent");
//-------------------retardprof2.php
define("LANGMESS427","Indiquer heure d'abs/rtd");
define("LANGMESS428","En ");
define("LANGMESS429","Horaire : ");

define("LANGTMESS478","Via code barre");
define("LANGTMESS479","Valider les présents");
define("LANGTMESS480","Visa direction");
define("LANGTMESS481","Commentaires pour les ".INTITULEELEVES);


define("LANGTMESS482","ACTUALITES - TRIADE");
define("LANGTMESS483","non disponible");
define("LANGTMESS484","Vos répertoires");
define("LANGTMESS485","Messages aux délégués");
define("LANGTMESS486","Modifier des circulaires");
define("LANGMESS430","L'année complète");
define("LANGMESS431","Avec notes partiel Vatel ");
define("LANGMESS432","Type du bulletin");
define("LANGMESS433","Enregistrement par code barre");
define("LANGMESS434","Valider les présents");
define("LANGMESS435","Courrier");
define("LANGMESS436","Relevés sans abs, ni rtd");
define("LANGMESS437","Listing des absences");
define("LANGMESS438","Absences par semaine");
define("LANGMESS439","Imprimer absences / retards");
define("LANGMESS440","Liste des présents");
define("LANGMESS441","Gestion abs/rtd via sconet");
define("LANGMESS442","Statistiques Abs / Rtd ");
define("LANGMESS443","Gestion des absences et retards d'un ".INTITULEELEVE);
define("LANGMESS444","Planifier&nbsp;");
define("LANGMESS445","&nbsp;Consulter&nbsp;/&nbsp;Modifier&nbsp;");
define("LANGMESS446","&nbsp;Supprimer&nbsp;");
define("LANGMESS447","Accéder");
define("LANGMESS448","&nbsp;Convertir&nbsp;abs.&nbsp;");
define("LANGMESS449","Configuration");
define("LANGMESS450","Gestion alertes");
define("LANGMESS451","Configuration créneau horaire ");
define("LANGMESS452","Configuration  SMS ");
define("LANGMESS453","Créditer des SMS");

define("LANGTMESS487","Avec notes vie scolaire");
define("LANGTMESS488","Rattrapage non validés");

define("LANGTRONBI30","Visualisation Trombinoscope du personnel");
define("LANGTRONBI20","Modifier Trombinoscope du personnel");

define("LANGSEXEF","F");
define("LANGSEXEH","H");
define("LANGHOM","Homme");
define("LANGFEM","Femme");


define("LANGTMESS489","Dupliquer l'EDT");
define("LANGTMESS490","Dupliquer l'EDT d'une classe vers une autre");
define("LANGTMESS491","Période à copier");
define("LANGTMESS492","Import du personnel de direction : ");
define("LANGTMESS493","Import des comptes du personnel : ");
define("LANGTMESS494","Import des entreprises : ");
define("LANGTMESS495","Import Spécif. IPAC : ");
define("LANGTMESS496","Import des matières : ");
define("LANGTMESS497","Module d'importation de fichier : ");
define("LANGTMESS498","Module d'importation de fichier Excel ");
define("LANGTMESS499","Le fichier excel à transmettre DOIT contenir 4 champs");
define("LANGTMESS500","Exemple fichier xls");
define("LANGTMESS501","Nombre de matière ajoutée : ");
define("LANGTMESS502","Dates Trimestrielles");
define("LANGTMESS503","Votre accès est actuellement désactivé.");
define("LANGTMESS504","Envoyer mot de passe par mail");
define("TITREACC1","parents");      // Info au niveau de la page d'accueil "Accès Parents"  
define("TITREACC2","Enseignants");  // Info au niveau de la page d'accueil "Accès Enseignants"  
define("TITREACC3","Vie scolaire"); // Info au niveau de la page d'accueil "Accès Vie scolaire"  
define("TITREACC4","Tuteur Stage"); // Info au niveau de la page d'accueil "Accès Tuteur Stage"  
define("TITREACC5","Personnels");   // Info au niveau de la page d'accueil "Accès Personnels"  
define("LANGTMESS505","Classe antérieures");
define("LANGTMESS506","Spécialisation");
define("LANGTMESS507","Sortie supplément au titre");
define("LANGTMESS508","Configuration supplément au titre");
define("LANGTMESS509","Gestion d'examen");
define("LANGTMESS510","Choix du document :");
define("LANGTMESS511","Récupérer le fichier ZIP Suppléments Titre");
define("LANGTMESS512","Niveau scolaire");
define("LANGTMESS513","Publipostage des sociétés ");
define("LANGTMESS514","Import des entreprises");
define("LANGTMESS515","Indemnité de stage");
define("LANGTMESS516","Suivi des demandes de convention");
define("LANGTMESS517","Gestion supplément au titre");
define("LANGTMESS518","Libellé :");
define("LANGTMESS519","Fichier");

define("LANGTMESS520","Nom du stage");
define("LANGTMESS521","En Entreprise le : ");
define("LANGTMESS522","Pays");
define("LANGTMESS523","Groupe hôtelier");
define("LANGTMESS524","Nombre d'étoiles");
define("LANGTMESS525","Nombre de chambres");
define("LANGTMESS526","Site web");
define("LANGTMESS527","Affectation de plusieurs étudiants à un stage");
define("LANGSTAGE100","Nom");
define("LANGSTAGE101","N° Stage");
define("LANGSTAGE102","Entreprise");
define("LANGSTAGE103","Service");
define("LANGSTAGE104","Indemnité");
define("LANGSTAGE105","Logé");
define("LANGSTAGE106","Nourri");
define("LANGSTAGE107","Valider");
define("LANGSTAGE108","Stage personnalisé");
define("LANGSTAGE109","Pays");
define("LANGSTAGE110","Tuteur de stage");
define("LANGSTAGE111","Langue parlé durant le stage");
define("LANGSTAGE112","Intitulé du service");
define("LANGSTAGE113","Indemnités de stage");
define("LANGSTAGE114","Horaires journaliers");
define("LANGSTAGE115","Les conventions de stage");
define("LANGSTAGE116","Sortie des conventions groupées");

define("LANGTMESS528","Langue de la classe");
define("LANGTMESS529","Retour classe");
define("LANGTMESS530","Récuperation des conventions de stage");


define("LANGVATEL1","D&eacute;connexion");
define("LANGVATEL2","Me connecter");
define("LANGVATEL3","Mot de passe oubli&eacute;");
define("LANGVATEL4","Ecris ton email");
define("LANGVATEL5","Ecris ton mot de passe");
define("LANGVATEL6","Semestre");
define("LANGVATEL7","Abs/Rtd/Sanction");
define("LANGVATEL8","Absences / Retards / Sanctions");
define("LANGVATEL9","Absences");
define("LANGVATEL10","Retards");
define("LANGVATEL11","Sanctions");
define("LANGVATEL12","Description des faits");
define("LANGVATEL13","<");
define("LANGVATEL14",">");
define("LANGVATEL15","Mois");
define("LANGVATEL16","R&eacute;initialiser votre mot de passe");
define("LANGVATEL17","Mot de passe oubli&eacute; ?");

define("LANGVATEL18","Acc&egrave;s Etudiant");
define("LANGVATEL19","Acc&egrave;s Enseignant");
define("LANGVATEL20","Acc&egrave;s Personnel");

define("LANGVATEL21","Ajouter");
define("LANGVATEL22","Modifier");
define("LANGVATEL23","Supprimer");
define("LANGVATEL24","Visualiser");
define("LANGVATEL25","Quoi de neuf ?");
define("LANGVATEL26","Notes");
define("LANGVATEL27","Statistiques de ce devoir");
define("LANGVATEL28","IMPOSSIBLE");
define("LANGVATEL29","Semestre déjà passé.");
define("LANGVATEL30","Ajouter élève");
define("LANGVATEL31","Ajouter une note à un élève pour ce devoir.");
define("LANGVATEL32","Retour sur la liste des devoirs");
define("LANGVATEL33","Emploi du temps");
define("LANGVATEL34","Absentéisme");
define("LANGVATEL35","absent(s) signé(s)");
define("LANGVATEL36","Calendrier");
define("LANGVATEL37","Problème d'enregistrement");
define("LANGVATEL38","Indiquer la date");


define("LANGMESSE01","Accès à votre compte SMS");
define("LANGMESSE02","Gestion des SMS"); 

define("LANGNEW100","Sanction(s)");
define("LANGNEW101","Prévision sur ");


?>
