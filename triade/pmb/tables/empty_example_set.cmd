@echo off
rem mysql.exe doit figurer dans le path, à défaut, préciser le chemin complet :
rem pour easyphp, mysql.exe peut être là: c:\easyphp\mysql\bin\mysql.exe
rem
rem -u bibli = utilisateur ayant les droits sur la base bibli
rem -pbibli = mot de passe de l'utilisateur
rem -h localhost = machine hébergeant le serveur mysql
rem bibli : la base de données sélectionnée
rem
rem < empty_example_set.sql : le script exécuté : va vider les tables :
rem             abo_liste_lecture
rem             abts_abts
rem             abts_abts_modeles
rem             abts_grille_abt
rem             abts_grille_modele
rem             abts_modeles
rem             acces_profiles
rem             acces_rights
rem             actes
rem             admin_session
rem             analysis
rem             audit
rem             aut_link
rem             authorities_sources
rem             authors
rem             avis
rem             bannette_abon
rem             bannette_contenu
rem             bannette_equation
rem             bannette_exports
rem             bannettes
rem             budgets
rem             bulletins
rem             cache_amendes
rem             caddie_content
rem             cms
rem             cms_articles
rem             cms_articles_descriptors
rem             cms_build
rem             cms_cadre_content
rem             cms_cadres
rem             cms_editorial_custom
rem             cms_editorial_custom_lists
rem             cms_editorial_custom_values
rem             cms_editorial_fields_global_index
rem             cms_editorial_publications_states
rem             cms_editorial_types
rem             cms_editorial_words_global_index
rem             cms_hash
rem             cms_managed_modules
rem             cms_pages
rem             cms_pages_env
rem             cms_sections
rem             cms_sections_descriptors
rem             cms_vars
rem             cms_version
rem             collections
rem             collections_state
rem             collstate_custom
rem             collstate_custom_lists
rem             collstate_custom_values
rem             comptes
rem             connectors
rem             connectors_categ
rem             connectors_categ_sources
rem             connectors_out
rem             connectors_out_oai_tokens
rem             connectors_out_setcache_values
rem             connectors_out_setcaches
rem             connectors_out_setcateg_sets
rem             connectors_out_setcategs
rem             connectors_out_sets
rem             connectors_out_sources
rem             connectors_out_sources_esgroups
rem             connectors_sources
rem             coordonnees
rem             demandes
rem             demandes_actions
rem             demandes_notes
rem             demandes_theme
rem             demandes_type
rem             demandes_users
rem             dsi_archive
rem             editions_states
rem             empr
rem             empr_caddie_content
rem             empr_custom
rem             empr_custom_lists
rem             empr_custom_values
rem             empr_grilles
rem             empr_groupe
rem             empty_words_calculs
rem             entites
rem             entrepot_source_2
rem             entrepot_source_4
rem             entrepot_source_5
rem             entrepots_localisations
rem             equations
rem             error_log
rem             es_cache
rem             es_cache_blob
rem             es_cache_int
rem             es_converted_cache
rem             es_esgroup_esusers
rem             es_esgroups
rem             es_esusers
rem             es_methods
rem             es_methods_users
rem             es_searchcache
rem             es_searchsessions
rem             exemplaires
rem             exemplaires_temp
rem             exercices
rem             expl_custom_values
rem             explnum
rem             explnum_doc
rem             explnum_doc_actions
rem             explnum_doc_sugg
rem             explnum_location
rem             external_count
rem             fiche
rem             frais
rem             gestfic0_custom
rem             gestfic0_custom_lists
rem             gestfic0_custom_values
rem             grilles
rem             groupe
rem             groupexpl
rem             groupexpl_expl
rem             harvest_field
rem             harvest_profil
rem             harvest_profil_import
rem             harvest_profil_import_field
rem             harvest_search_field
rem             harvest_src
rem             import_marc
rem             liens_actes
rem             lignes_actes
rem             lignes_actes_relances
rem             linked_mots
rem             log_expl_retard
rem             log_retard
rem             logopac
rem             mailtpl
rem             mots
rem             notices
rem             notices_authorities_sources
rem             notices_categories
rem             notices_custom
rem             notices_custom_lists
rem             notices_custom_values
rem             notices_externes
rem             notices_fields_global_index
rem             notices_global_index
rem             notices_langues
rem             notices_mots_global_index
rem             notices_relations
rem             notices_titres_uniformes
rem             offres_remises
rem             opac_filters
rem             opac_liste_lecture
rem             opac_sessions
rem             opac_views
rem             opac_views_empr
rem             ouvertures
rem             paiements
rem             param_subst
rem             perio_relance
rem             planificateur
rem             pret
rem             pret_archive
rem             publishers
rem             quotas
rem             quotas_finance
rem             quotas_opac_views
rem             rapport_demandes
rem             recouvrements
rem             resa
rem             resa_archive
rem             resa_loc
rem             resa_planning
rem             resa_ranger
rem             responsability
rem             rss_content
rem             rss_flux
rem             rss_flux_content
rem             rubriques
rem             sauv_lieux
rem             sauv_log
rem             search_cache
rem             search_persopac
rem             search_persopac_empr_categ
rem             serialcirc
rem             serialcirc_ask
rem             serialcirc_circ
rem             serialcirc_copy
rem             serialcirc_diff
rem             serialcirc_expl
rem             serialcirc_group
rem             series
rem             sessions
rem             source_sync
rem             sources_enrichment
rem             statopac
rem             sub_collections
rem             suggestions
rem             suggestions_origine
rem             suggestions_source
rem             sur_location
rem             taches
rem             taches_docnum
rem             taches_type
rem             tags
rem             titres_uniformes
rem             transactions
rem             transferts
rem             transferts_demande
rem             translation
rem             tris
rem             tu_distrib
rem             tu_ref
rem             tu_subdiv
rem             tva_achats
rem             type_abts
rem             type_comptes
rem             types_produits
rem             upload_repertoire
rem             users_groups
rem             visionneuse_params
rem             words
rem             z_notices
rem             z_query
rem ce script doit recevoir qques parametres :
rem %1 le nom de la base de donnees
rem %2 le nom de la machine hote du serveur mysql
rem %3 le user de la base de donnees de PMB
rem %4 le mot de passe du user (qui peut etre vide)
if "%1"=="" goto syntaxe
if "%2"=="" goto syntaxe
if "%3"=="" goto syntaxe
goto suite
:syntaxe
echo syntaxe d'appel de ce script :
echo empty_example_set.cmd param1 param2 param3 param4
echo     ou :
echo        param1 = base de donnees de PMB, "bibli" a l'installation
echo        param2 = nom de la machine hote du serveur MySQL, "localhost" par defaut
echo        param3 = user de la base de donnees de PMB, "bibli" a l'installation
echo        param4 = mot de passe du user de la base, "bibli" a l'installation
goto fin
:suite
echo Si vous avez charge les donnees de test de PMB (data_test.sql),
echo vous disposez d'un jeu de notices et d'exemplaires pour tester PMB.
echo Ce script vous propose de vider ces tables d'exemple de votre application
echo afin de repartir d'une base PMB vierge :
echo ------------------------------------------
echo     table des emprunteurs              
echo     table des groupes d'emprunteurs
echo     table des prets          
echo     table des notices        
echo     table des exemplaires     
echo     table des bulletins de periodiques
echo     table de  depouillement des periodiques
echo     table des series          
echo     table des collections       
echo     table des sous-collections  
echo     table des auteurs           
echo     table des editeurs
echo     ...
echo ------------------------------------------
:start
echo e) VIDER LES TABLES D'EXEMPLES
echo q) Quitter

set /p userinp=Taper e ou q :
set userinp=%userinp:~0,1%
if "%userinp%"=="e" goto empty
if "%userinp%"=="q" goto fin
echo Choix invalide
goto start

:empty
echo commande executee : mysql -u %3 -p%4 -h %2 %1  empty_example_set.sql
echo .
mysql -u %3 -p%4 -h %2 %1 < empty_example_set.sql
echo .
echo ------------------------------------------
echo les tables ont ete videes
echo ------------------------------------------
goto sortie
:fin
echo ------------------------------------------
echo operation abandonnee
echo ------------------------------------------
:sortie
