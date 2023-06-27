-- +-------------------------------------------------+
-- © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
-- +-------------------------------------------------+
-- $Id: readme.txt,v 1.7 2016-03-30 09:04:51 mbertin Exp $

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


///////////////////// Liste des tables remplies par fichiers /////////////////

\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            minimum.sql
# Contenu de la table abts_periodicites
# Contenu de la table bannette_tpl
# Contenu de la table categories
# Contenu de la table classements
# Contenu de la table cms_editorial_types
# Contenu de la table empr_statut
# Contenu de la table explnum_statut
# Contenu de la table lenders
# Contenu de la table lignes_actes_statuts
# Contenu de la table noeuds
# Contenu de la table notice_statut
# Contenu de la table origin_authorities
# Contenu de la table origine_notice
# Contenu de la table parametres
# Contenu de la table pclassement
# Contenu de la table sauv_sauvegardes
# Contenu de la table sauv_tables
# Contenu de la table serialcirc_tpl
# Contenu de la table suggestions_categ
# Contenu de la table thesaurus
# Contenu de la table users
	utente admin/admin
# Contenu de la table z_attr
# Contenu de la table z_bib

\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            feed_essential.sql
# Contenu de la table arch_emplacement
# Contenu de la table arch_statut
# Contenu de la table arch_type
# Contenu de la table caddie
# Contenu de la table caddie_procs
# Contenu de la table docs_codestat
# Contenu de la table docs_location
# Contenu de la table docs_section
# Contenu de la table docs_statut
# Contenu de la table docs_type
# Contenu de la table docsloc_section
# Contenu de la table empr_caddie
# Contenu de la table empr_caddie_procs
# Contenu de la table empr_categ
# Contenu de la table empr_codestat
# Contenu de la table etagere
# Contenu de la table etagere_caddie
# Contenu de la table expl_custom
# Contenu de la table expl_custom_lists
# Contenu de la table facettes
# Contenu de la table infopages
# Contenu de la table notice_tpl
# Contenu de la table notice_tplcode
# Contenu de la table procs
# Contenu de la table procs_classements
# Contenu de la table search_perso
# Contenu de la table statopac_request
# Contenu de la table statopac_vue_1
# Contenu de la table statopac_vue_2
# Contenu de la table statopac_vue_3
# Contenu de la table statopac_vue_4
# Contenu de la table statopac_vue_5
# Contenu de la table statopac_vues
# Contenu de la table statopac_vues_col

\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            bibliportail.sql
# Base complète avec un portail pagéo de démo

\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            biblizen.sql
# Base complète avec un portail zen de démo

\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            data_test.sql
# Contenu de la table analysis
# Contenu de la table authors
# Contenu de la table bannette_abon
# Contenu de la table bannette_contenu
# Contenu de la table bannette_equation
# Contenu de la table bannettes
# Contenu de la table bulletins
# Contenu de la table caddie_content
# Contenu de la table collections
# Contenu de la table connectors_categ
# Contenu de la table connectors_categ_sources
# Contenu de la table connectors_sources
# Contenu de la table empr
# Contenu de la table entrepot_source_2
# Contenu de la table entrepot_source_4
# Contenu de la table entrepot_source_5
# Contenu de la table equations
# Contenu de la table exemplaires
# Contenu de la table explnum
# Contenu de la table external_count
# Contenu de la table notices
# Contenu de la table notices_categories
# Contenu de la table notices_fields_global_index
# Contenu de la table notices_global_index
# Contenu de la table notices_langues
# Contenu de la table notices_mots_global_index
# Contenu de la table notices_relations
# Contenu de la table publishers
# Contenu de la table responsability
# Contenu de la table series
# Contenu de la table sources_enrichment
# Contenu de la table words


\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            agneaux.sql
\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            unesco.sql
\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            environnement.sql
# Contenu de la table voir_aussi
# Contenu de la table categories
# Contenu de la table noeuds
# Contenu de la table thesaurus

\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            indexint_chambery.sql
\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            indexint_100.sql
\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            indexint_dewey.sql
\_/-\_/-\_/-\_/-\_/-\_/-\_/-\            indexint_small_en.sql
# Contenu de la table indexint
# Contenu de la table pclassement
