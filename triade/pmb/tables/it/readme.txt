-- +-------------------------------------------------+
-- © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
-- +-------------------------------------------------+
-- $Id: readme.txt,v 1.8 2021/05/03 10:13:12 dbellamy Exp $

------------------------------------------------------------------------------------------------------------------

Descrizione dei file
bibli.sql : struttura della base dati - nessun dato

minimum.sql : utente admin/admin, parametri dell'applicazione

feed_essential.sql : quello di cui avete bisogno per essere subito operativi:
	Dati di sistema precaricati e modificabili.
	Uno schema di backup pronto all'uso
	Uno schema di parametraggio Z3950.
	
data_test.sql : un piccolo insieme di dati bibliografici e di utenti per poter provare immediatamente PMB.

************************************************************************************************
________________________________________________________________________________________________
Attenzione, in caso di aggiornamento di una base dati esistente:
------------------------------------------------------------------------------------------------
******* Da fare immediatamente dopo l'installazione o l'aggiornamento dell'applicazione ********

Quando installate una versione nuova su una precedente
dovete obbligatoriamente, dopo la copia dei file contenuti 
in questo archivio scaricato dal web:

Verificare che i parametri contenuti in:
./includes/db_param.inc.php
./opac_css/includes/opac_db_param.inc.php

corrispondano alla vostra configurazione (salvatevela prima!)

Inoltre:
Dovete aggiornare la base dati.
Non andrà perso nulla.

Connettetevi nel solito modo a PMB, la grafica potrebbe essere 
differente.

Scegliete Amministrazione > Strumenti > Aggiorna database per aggiornare 
la struttura del database.

Una serie di messaggi indicheranno gli aggiornamenti successivi, 
continuate con gli aggiornamenti, utilizzando il link in basso, finchè non compare 
 'La vostra base dati è aggiornata alla versione ...'

A questo punto potete accedere ai vostri dati per eventuali modifiche alle preferenze,
specialmente lo stile di visualizzazione.

Non esitate a comunicarci i vostri problemi o i vostri suggerimenti
per mail : pmb@sigb.net (francia)   pmb-italia@reteisi.org (italia)

Inoltre, vi saremo grati se potessimo elencarvi tra i utenti comunicandoci qualche numero
come il numero dei lettori, delle opere, dei CD, VHS, DVD   ecc.ecc unitamente alle
coordinate della vostra installazione ci servirà a conoscervi meglio.

Maggiori informazioni nella cartella  ./doc o anche  
sul nostro sito http://www.sigb.net

Il gruppo di sviluppo.


