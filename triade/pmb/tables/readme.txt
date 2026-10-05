-- +-------------------------------------------------+
-- © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
-- +-------------------------------------------------+
-- $Id: readme.txt,v 1.11 2021/05/03 10:13:12 dbellamy Exp $

************************************************************************************************

Description des fichiers

*************************************
bibli.sql : Structure de la base de données uniquement, pas de données

Version unique
Moteur MyISAM

Création du fichier :
mysqldump -u bibli -pbibli -d --quote-names=FALSE --skip-add-drop-table bibli | sed 's/ AUTO_INCREMENT=[0-9]*\b//g' > bibli.sql

Création du fichier sans le moteur MyISAM :
mysqldump -u bibli -pbibli -d --quote-names=FALSE --skip-add-drop-table bibli | sed 's/ ENGINE=MyISAM DEFAULT AUTO_INCREMENT=[0-9]*\b//g' > bibli_no_engine.sql
		
Penser à ajouter la ligne de commentaire originale du fichier afin de conserver la version CVS du fichier
		
*************************************

minimum.sql : Données minimales 
Utilisateur admin/admin, 
Paramètres de l'application, 
Jeu de sauvegarde prêt à l'emploi, 
Jeu de paramétrage serveur Z3950.
    
Versions en français, espagnol, italien, anglais et portuguais

La liste des tables à inclure évolue à chaque version.
Faire une installation minimum et répertorier les tables non vides pour mettre à jour la liste.

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob bibli abts_periodicites abts_status anim_price_types anim_registration_origins anim_registration_status anim_status authorities authorities_statuts bannette_tpl categories classements cms_editorial_types contact_forms contribution_area_status empr_statut explnum_statut grids_generic lenders lignes_actes_statuts noeuds notice_statut origine_notice origin_authorities parametres parametres_uncached pclassement sauv_sauvegardes sauv_tables scan_request_status serialcirc_tpl suggestions_categ thesaurus users z_attr z_bib > minimum.sql

Penser à ajouter la ligne de commentaire originale du fichier afin de conserver la version CVS du fichier

*************************************

feed_essential.sql : Données nécessaires pour utiliser l'application rapidement.

Versions en francais, italien, espagnol et portuguais

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob bibli arch_emplacement arch_statut arch_type caddie caddie_procs docs_codestat docs_location docs_section docs_statut docs_type docsloc_section empr_caddie empr_caddie_procs empr_categ empr_codestat etagere etagere_caddie expl_custom expl_custom_lists facettes infopages notice_tpl notice_tplcode procs procs_classements search_perso statopac_request statopac_vues statopac_vues_col > feed_essential.sql

Penser à ajouter la ligne de commentaire originale du fichier afin de conserver la version CVS du fichier

*************************************

data_test.sql : Sélection de données afin de pouvoir tester de suite PMB.
Notices, lecteurs, prêteurs, exemplaires, périodiques...
Se base sur les données de l'application fournies dans feed_essential.sql
Doit charger le thésaurus unesco.sql

Version unique

Création du fichier :

Paramètres à modifier :
notice_enrichment, show_social_network
notices_format_onglets =5

Penser à ajouter la ligne de commentaire originale du fichier afin de conserver la version CVS du fichier

*************************************

unesco.sql 	: Thésaurus de l'UNESCO, orienté éducation.

Version unique multilingue français, anglais, espagnol

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob bibli thesaurus noeuds categories voir_aussi > unesco.sql

Penser à conserver la ligne de commentaire originale du fichier minimum.sql afin de conserver la version CVS du fichier

*************************************

agneaux.sql : Thésaurus orienté médiathèque.

Version unique en français

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob bibli thesaurus noeuds categories voir_aussi > agneaux.sql

Penser à conserver la ligne de commentaire originale du fichier minimum.sql afin de conserver la version CVS du fichier

*************************************

environnement.sql : Thésaurus pour fonds documentaire orienté environnement.

Version unique en français

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob bibli thesaurus noeuds categories voir_aussi > environnement.sql

Penser à conserver la ligne de commentaire originale du fichier minimum.sql afin de conserver la version CVS du fichier

*************************************
	
indexint_100.sql : 100 cases du savoir ou marguerite des couleurs, 
indexation décimale style Dewey simplifiée pour l'éducation

Versions en français, portuguais et anglais

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob  bibli pclassement indexint > indexint_100.sql

Penser à conserver la ligne de commentaire originale du fichier minimum.sql afin de conserver la version CVS du fichier

*************************************

indexint_dewey.sql : Indexation style Dewey

Version unique en français

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob  bibli pclassement indexint > indexint_dewey.sql

Penser à conserver la ligne de commentaire originale du fichier minimum.sql afin de conserver la version CVS du fichier

*************************************

indexint_chambery.sql : Indexation style Dewey de la BM de Chambéry, très bien conçue mais peu adaptée à des petites bibliothèques
	
Version unique en français

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob  bibli pclassement indexint > indexint_dewey.sql

Penser à conserver la ligne de commentaire originale du fichier minimum.sql afin de conserver la version CVS du fichier

*************************************

bibliportail.sql : Base complète portail Pagéo

Version unique en français

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob  bibli  > bibliportail.sql

Penser à conserver la ligne de commentaire originale du fichier minimum.sql afin de conserver la version CVS du fichier

*************************************

biblizen.sql : Base complète portail Zen

Version unique en français

Création du fichier :
mysqldump -u bibli -pbibli -t --skip-extended-insert --complete-insert --hex-blob  bibli  > biblizen.sql

Penser à conserver la ligne de commentaire originale du fichier minimum.sql afin de conserver la version CVS du fichier

*************************************

dataref.inc.php : Fichier liste des index des tables 
pour utilisation en  Administration >> Outils >> Maintenance MySQL >> Vérifier la présence des index sur les tables

Version unique 

Création du fichier :
php genere_dataref.php

*************************************


