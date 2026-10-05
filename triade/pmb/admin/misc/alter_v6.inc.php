<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alter_v6.inc.php,v 1.95.2.8.2.1 2025/03/20 10:30:28 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $action;
global $msg;
global $pmb_version_brut, $pmb_version_patch, $pmb_bdd_version;

settype ($action,"string");

pmb_mysql_query("set names latin1 ");

$db_update_log_version = "PMB : " . $pmb_version_brut . "." . $pmb_version_patch . "; BDD : " . $pmb_bdd_version;

switch ($action) {
    case "lancement":
        switch ($pmb_bdd_version) {
            case "v5.36":
                $maj_a_faire = "v6.00";
                echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
                echo form_relance ($maj_a_faire);
                break;
            case "v6.00":
                $maj_a_faire = "v6.01";
                echo "<strong><font color='#FF0000'>".$msg[1804]."$maj_a_faire !</font></strong><br />";
                echo form_relance ($maj_a_faire);
                break;
            case "v6.01":
                echo "<strong><font color='#FF0000'>".$msg[1805].$pmb_bdd_version." !</font></strong><br />";
                break;
            default:
                echo "<strong><font color='#FF0000'>".$msg[1806].$pmb_bdd_version." !</font></strong><br />";
                break;
        }
        break;

    case "v6.00":
        echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
        // Equipe DEV Refonte gestion des vignettes
        $rqt = "CREATE TABLE IF NOT EXISTS thumbnail_sources (
        			id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        			class varchar(255) NOT NULL DEFAULT '',
        			settings mediumblob NOT NULL,
        			active tinyint(1) NOT NULL DEFAULT 0
        		)";
        echo traite_rqt($rqt,"CREATE TABLE thumbnail_sources");

        $query = "SELECT 1 FROM thumbnail_sources WHERE id = 1";
        $result = pmb_mysql_query($query);
        if (!pmb_mysql_num_rows($result)) {
            $rqt = "INSERT INTO thumbnail_sources (class, settings, active) VALUES
                ('Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Noimage\\\\NoImageThumbnailSource', '[{\"typedoc\":\"\",\"nivbiblio\":\"\",\"value\":\"no_image.png\"}]', 1);";
            echo traite_rqt($rqt,"INSERT NoImageThumbnailSource INTO thumbnail_sources ");
        }

        $rqt = "CREATE TABLE IF NOT EXISTS thumbnail_sources_entities (
        			id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        			source_class varchar(255) NOT NULL DEFAULT '',
        			pivot_class varchar(255) NOT NULL DEFAULT '',
        			type int(11) NOT NULL DEFAULT 0,
        			pivot LONGTEXT,
        			ranking int(10) NOT NULL DEFAULT 0
        		)";
        echo traite_rqt($rqt,"CREATE TABLE thumbnail_sources_entities");

        $rqt = "ALTER TABLE thumbnail_sources_entities CHANGE pivot pivot LONGTEXT;";
        echo traite_rqt($rqt,"pivot CHANGE in thumbnail_sources_entities");

        $query = "SELECT 1 FROM thumbnail_sources_entities WHERE
    		        source_class in ('Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Url\\\\RecordUrlThumbnailSource',
    		                          'Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Amazon\\\\RecordAmazonThumbnailSource',
    		                          'Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Docnum\\\\RecordDocnumThumbnailSource',
    		                          'Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Noimage\\\\NoImageThumbnailSource')
    		        AND pivot_class = 'Pmb\\\\Thumbnail\\\\Models\\\\Pivots\\\\Entities\\\\Record\\\\RecordBasicPivot\\\\RecordBasicPivot' LIMIT 1";
        $result = pmb_mysql_query($query);
        if (!pmb_mysql_num_rows($result)) {
            $rqt = "INSERT INTO thumbnail_sources_entities (source_class, pivot_class, type, pivot, ranking) VALUES
                ('Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Url\\\\RecordUrlThumbnailSource', 'Pmb\\\\Thumbnail\\\\Models\\\\Pivots\\\\Entities\\\\Record\\\\RecordBasicPivot\\\\RecordBasicPivot', 1, '{\"typedoc\":\"\",\"nivbiblio\":\"\"}', 0),
                ('Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Amazon\\\\RecordAmazonThumbnailSource', 'Pmb\\\\Thumbnail\\\\Models\\\\Pivots\\\\Entities\\\\Record\\\\RecordBasicPivot\\\\RecordBasicPivot', 1, '{\"typedoc\":\"\",\"nivbiblio\":\"\"}', 1),
                ('Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Docnum\\\\RecordDocnumThumbnailSource', 'Pmb\\\\Thumbnail\\\\Models\\\\Pivots\\\\Entities\\\\Record\\\\RecordBasicPivot\\\\RecordBasicPivot', 1, '{\"typedoc\":\"\",\"nivbiblio\":\"\"}', 2),
                ('Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Noimage\\\\NoImageThumbnailSource', 'Pmb\\\\Thumbnail\\\\Models\\\\Pivots\\\\Entities\\\\Record\\\\RecordBasicPivot\\\\RecordBasicPivot', 1, '{\"typedoc\":\"\",\"nivbiblio\":\"\"}', 3);";
            echo traite_rqt($rqt,"INSERT RecordUrlThumbnailSource, RecordAmazonThumbnailSource, RecordDocnumThumbnailSource AND NoImageThumbnailSource INTO thumbnail_sources_entities ");
        }

        // GN - Ajout d'une colonne "logo" pour une animation
        $rqt = "ALTER TABLE anim_animations ADD logo blob default NULL";
        echo traite_rqt($rqt,"alter table anim_animations add logo");

        // GN - Ajout d'une colonne "anim_events" pour un event
        $rqt = "ALTER TABLE anim_events ADD during_day integer default 0";
        echo traite_rqt($rqt,"alter table anim_events add during_day");

        // DG - Ajout du paramétrage lié au type d'authentification
        $rqt = "ALTER TABLE mails_configuration ADD mail_configuration_authentification_type_settings mediumtext AFTER mail_configuration_authentification_type" ;
        echo traite_rqt($rqt,"ALTER TABLE mails_configuration ADD mail_configuration_authentification_type_settings");

        // DG - Configuration des mails - configuration validée ?
        $rqt = "ALTER TABLE mails_configuration ADD mail_configuration_validated INT(1) NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"ALTER TABLE mails_configuration add mail_configuration_validated");

        // DG - Configuration des mails - informations sur la configuration
        $rqt = "ALTER TABLE mails_configuration ADD mail_configuration_informations text NOT NULL";
        echo traite_rqt($rqt,"ALTER TABLE mails_configuration add mail_configuration_informations");

        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'selfservice' and sstype_param='resa_ici_todo_valid'")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
        			VALUES (0, 'selfservice', 'resa_ici_todo_valid', '0', '1', 'Permet d\'ignorer la validité de la réservation') ";
            echo traite_rqt($rqt, "INSERT selfservice_resa_ici_todo_valid='0' INTO parametres") ;
        }

        // GN - Ajout d'une colonne dans "anim_animation" pour enregistrer qu'une personne a la fois a une animation
        $rqt = "ALTER TABLE anim_animations ADD unique_registration tinyint default 0";
        echo traite_rqt($rqt,"alter table anim_animations add unique_registration");

        // GN - Ajout d'un paramètre utilisateur pour les animations (Autoriser l'inscription en liste d'attente)
        $rqt = "ALTER TABLE users ADD deflt_animation_waiting_list TINYINT UNSIGNED DEFAULT 0 NOT NULL ";
        echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_animation_waiting_list");

        // GN - Ajout d'un paramètre utilisateur pour les animations (Valider l'inscription automatiquement à l'OPAC)
        $rqt = "ALTER TABLE users ADD deflt_animation_automatic_registration TINYINT UNSIGNED DEFAULT 0 NOT NULL ";
        echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_animation_automatic_registration");

        // GN - Ajout d'un paramètre utilisateur pour les animations (Type de communication)
        $rqt = "ALTER TABLE users ADD deflt_animation_communication_type TINYINT UNSIGNED DEFAULT 1 NOT NULL ";
        echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_animation_communication_type");

        // GN - Ajout d'un paramètre utilisateur pour les animations (Inscription unique a une animation)
        $rqt = "ALTER TABLE users ADD deflt_animation_unique_registration TINYINT UNSIGNED DEFAULT 0 NOT NULL ";
        echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_animation_unique_registration");

        // Equipe DEV refonte D.S.I
        $rqt = "CREATE TABLE IF NOT EXISTS dsi_channel (
				id_channel int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				settings mediumblob NOT NULL,
				type int(11) NOT NULL DEFAULT 0,
				model tinyint(1) NOT NULL DEFAULT 0,
				num_model int(10) UNSIGNED DEFAULT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_channel");


        $rqt = "CREATE TABLE IF NOT EXISTS dsi_content_history (
				id_content_history int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				type int(11) NOT NULL DEFAULT 0,
				content longblob NOT NULL,
				num_diffusion_history int(10) UNSIGNED NOT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_content_history");


        $rqt = "CREATE TABLE IF NOT EXISTS dsi_diffusion (
				id_diffusion int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				settings mediumblob NOT NULL,
				num_status int(10) UNSIGNED NOT NULL DEFAULT 1,
				num_subscriber_list int(10) UNSIGNED DEFAULT NULL,
				num_item int(10) UNSIGNED NOT NULL,
				num_view int(10) UNSIGNED NOT NULL,
				num_channel int(10) UNSIGNED NOT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_diffusion");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_diffusion_history (
				id_diffusion_history int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				total_recipients int(10) UNSIGNED NOT NULL DEFAULT 0,
				num_diffusion int(10) UNSIGNED NOT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_diffusion_history");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_diffusion_product (
				num_diffusion int(10) UNSIGNED NOT NULL,
				num_product int(10) UNSIGNED NOT NULL,
				active tinyint(1) NOT NULL DEFAULT 0,
				last_diffusion datetime DEFAULT NULL,
				PRIMARY KEY (num_diffusion, num_product)
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_diffusion_product");


        $rqt = "CREATE TABLE IF NOT EXISTS dsi_diffusion_status (
				id_diffusion_status int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				active tinyint(1) NOT NULL DEFAULT 0
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_diffusion_status");

        $rqt = "REPLACE INTO dsi_diffusion_status (id_diffusion_status, name, active) VALUES (1, 'Statut par défaut', '1')";
        echo traite_rqt($rqt,"INSERT default status into dsi_diffusion_status ");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_event (
				id_event int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				model tinyint(1) NOT NULL DEFAULT 0,
				settings mediumblob NOT NULL,
				type int(11) NOT NULL DEFAULT 0,
				num_model int(10) UNSIGNED DEFAULT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_event");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_event_diffusion (
				num_event int(10) UNSIGNED NOT NULL,
				num_diffusion int(10) UNSIGNED NOT NULL,
				PRIMARY KEY (num_event, num_diffusion)
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_event_diffusion");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_event_product (
				num_event int(10) UNSIGNED NOT NULL,
				num_product int(10) UNSIGNED NOT NULL,
				PRIMARY KEY (num_event, num_product)
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_event_diffusion");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_item (
				id_item int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				model tinyint(1) NOT NULL DEFAULT 0,
				settings mediumblob NOT NULL,
				type int(11) NOT NULL DEFAULT 0,
				num_model int(10) UNSIGNED DEFAULT NULL,
				num_parent int(10) UNSIGNED DEFAULT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_item");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_product (
				id_product int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				settings mediumblob NOT NULL,
				num_subscriber_list int(10) UNSIGNED DEFAULT NULL,
				num_status int(10) UNSIGNED NOT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_product");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_product_status (
				id_product_status int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				active tinyint(1) NOT NULL DEFAULT 0
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_product_status");

        $rqt = "REPLACE INTO dsi_product_status (id_product_status, name, active) VALUES (1, 'Statut par défaut', '1')";
        echo traite_rqt($rqt,"INSERT default status into dsi_product_status");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_subscribers (
				id_subscriber int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL,
				settings mediumblob NOT NULL,
				type int(11) NOT NULL DEFAULT 0,
				update_type int(11) NOT NULL DEFAULT 0
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_subscribers");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_subscribers_diffusion (
				id_subscriber_diffusion int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				settings mediumblob NOT NULL,
				type int(11) NOT NULL DEFAULT 0,
				update_type int(11) NOT NULL DEFAULT 0,
				num_diffusion int(10) UNSIGNED NOT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_subscribers_diffusion");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_subscribers_product (
				id_subscriber_product int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				settings mediumblob NOT NULL,
				type int(11) NOT NULL DEFAULT 0,
				update_type int(11) NOT NULL DEFAULT 0,
				num_product int(10) UNSIGNED NOT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_subscribers_product");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_subscriber_list (
				id_subscriber_list int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				model tinyint(1) NOT NULL DEFAULT 0,
				settings mediumblob NOT NULL,
				num_parent int(10) UNSIGNED DEFAULT NULL,
				num_model int(10) UNSIGNED DEFAULT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_subscriber_list");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_subscriber_list_content (
				num_subscriber int(10) UNSIGNED NOT NULL,
				num_subscriber_list int(10) UNSIGNED NOT NULL,
				PRIMARY KEY (num_subscriber, num_subscriber_list)
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_subscriber_list_content");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_tag (
				id_tag int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT ''
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_tag");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_view (
				id_view int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name varchar(255) NOT NULL DEFAULT '',
				model tinyint(1) NOT NULL DEFAULT 0,
				settings mediumblob NOT NULL,
				type int(11) NOT NULL DEFAULT 0,
				num_model int(10) UNSIGNED DEFAULT NULL,
				num_parent int(10) UNSIGNED DEFAULT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_view");

        $rqt = "CREATE TABLE IF NOT EXISTS dsi_entities_tags (
  				num_tag int(11) UNSIGNED NOT NULL,
  				num_entity int(11) UNSIGNED NOT NULL,
  				type int(11) UNSIGNED NOT NULL,
  				PRIMARY KEY (num_tag, num_entity, type)
			)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_entities_tags");


        // Equipe DEV - Ajout d'un paramètre pour le RGAA
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'opac' and sstype_param='rgaa_active'")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
        			VALUES (0, 'opac', 'rgaa_active', '0', '0', 'Activer la transformation HTML pour compatibilité RGAA\n0 : non\n1 : oui') ";
            echo traite_rqt($rqt, "INSERT opac_rgaa_active='0' INTO parametres") ;
        }

        // +-------------------------------------------------+
        echo "</table>";
        $rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
        $res = pmb_mysql_query($rqt) ;
        echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
        echo form_relance ("v6.01");
        break;

    case "v6.01":
        echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
        // +-------------------------------------------------+

        // DG - Ajout d'une classification sur les listes
        $rqt = "ALTER TABLE lists ADD list_num_ranking int not null default 0 AFTER list_default_selected" ;
        echo traite_rqt($rqt,"ALTER TABLE lists ADD list_num_ranking");

        // DG - Ajout dans les bannettes la possibilité d'historiser les diffusions
        $rqt = "ALTER TABLE bannettes ADD bannette_diffusions_history INT(1) UNSIGNED NOT NULL default 0";
        echo traite_rqt($rqt,"ALTER TABLE bannettes ADD bannette_diffusions_history");

        // DG - Log des diffusions de bannettes
        $rqt = "CREATE TABLE IF NOT EXISTS bannettes_diffusions (
					id_diffusion int unsigned not null auto_increment primary key,
        			diffusion_num_bannette int(9) unsigned not null default 0,
        			diffusion_mail_object text,
					diffusion_mail_content mediumtext,
					diffusion_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					diffusion_records text,
					diffusion_deleted_records text,
					diffusion_recipients text,
					diffusion_failed_recipients text
        		)";
        echo traite_rqt($rqt,"create table bannettes_diffusions");

        // TS-RT-JP - Ajout de la table dsi_content_buffer
        $rqt = "CREATE TABLE IF NOT EXISTS dsi_content_buffer (
		  id_content_buffer int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  type int(11) NOT NULL DEFAULT 0,
		  content longblob NOT NULL,
		  num_diffusion_history int(10) UNSIGNED NOT NULL DEFAULT 0
		)";
        echo traite_rqt($rqt,"CREATE TABLE dsi_content_buffer");

        // TS-RT-JP - Ajout du champ automatic sur une diffusion
        $rqt = "ALTER TABLE dsi_diffusion ADD automatic tinyint(1) NOT NULL DEFAULT 0 AFTER settings" ;
        echo traite_rqt($rqt,"ALTER dsi_diffusion ADD automatic");

        // TS-RT-JP - Ajout d'un état sur l'historique de diffusion
        $rqt = "ALTER TABLE dsi_diffusion_history ADD state tinyint(1) NOT NULL DEFAULT 0 AFTER total_recipients" ;
        echo traite_rqt($rqt,"ALTER dsi_diffusion_history ADD state");

        //DG - Tâches : changement du champ msg_statut en texte
        $rqt = "ALTER TABLE taches MODIFY msg_statut TEXT";
        echo traite_rqt($rqt,"ALTER TABLE taches MODIFY msg_statut IN TEXT");

        //DG - Tâches : changement du champ indicat_progress en nombre flotant
        $rqt = "ALTER TABLE taches MODIFY indicat_progress FLOAT(5,2) NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"ALTER TABLE taches MODIFY indicat_progress IN FLOAT");

        //DG - Ajout d'un paramètre caché permettant de définir si une indexation via le gestionnaire de tâches est en cours
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='scheduler_indexation_in_progress' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (NULL, 'pmb', 'scheduler_indexation_in_progress', '0', 'Paramètre caché permettant de définir si une indexation via le gestionnaire de tâches est en cours', '', '1')" ;
            echo traite_rqt($rqt,"insert hidden pmb_scheduler_indexation_in_progress=0 into parametres") ;
        }

        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='gestion_animation' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'gestion_animation', '0', 'Utiliser la gestion des animations des lecteurs ? \n 0 : Non\n 1 : Oui, gestion simple, \n 2 : Oui, gestion avancée') " ;
            echo traite_rqt($rqt,"insert pmb_gestion_animation = 0 into parametres");
        }

        // GN - Ajout d'un paramètre utilisateur (import Z3950 en catalogue automatique/manuel)
        $rqt = "ALTER TABLE users ADD deflt_notice_catalog_categories_auto INT(1) UNSIGNED DEFAULT 1 NOT NULL ";
        echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_notice_catalog_categories_auto");

        // Equipe DEV Plugins
        $rqt = "CREATE TABLE IF NOT EXISTS plugins (
        			id_plugin int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        			plugin_name varchar(255) NOT NULL DEFAULT '',
        			plugin_settings text NOT NULL
			)";
        echo traite_rqt($rqt,"CREATE TABLE plugins");

        // DB - Info de modification des fichiers db_param
        $rqt = " select 1" ;
        echo traite_rqt($rqt, encoding_normalize::charset_normalize("<b class='erreur'>
            LES FICHIERS DE CONNEXION A LA BASE DE DONNEES ( pmb/includes/db_param.inc.php et pmb/opac_css/includes/opac_db_param.inc.php ONT ETE MODIFIES.<br />
            Un mod&egrave;le de r&eacute;f&eacute;rence est d&eacute;fini dans le r&eacute;pertoire pmb/tables pour chacun de ces fichiers.<br />
            VERIFIEZ CES FICHIERS SI VOUS VENEZ DE FAIRE UNE MISE A JOUR DE VOTRE INSTALLATION.
            </b>", 'iso-8859-1'));

        // TS - mise à jour du paramètre pmb_book_pics_url
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE sstype_param='book_pics_url' AND comment_param LIKE '%Ce paramètre n\'est plus utilisé%'"))==0){
            $rqt = "UPDATE parametres SET comment_param = CONCAT(comment_param,'\n Ce paramètre n\'est plus utilisé. Merci de reporter les valeurs personnalisées dans le paramétrage des vignettes (admin/vignettes/sources/liens externes).') WHERE sstype_param = 'book_pics_url'" ;
            echo traite_rqt($rqt, encoding_normalize::charset_normalize("<b class='erreur'>
                Les param&egrave;tres book_pics_url ne sont plus utilis&eacute;s. Merci de reporter les valeurs personnalis&eacute;es dans le param&eacute;trage de vignettes (admin/vignettes/sources/liens externes.
                </b>", 'iso-8859-1'));
        }

        // TS - modification du nom de la source de vignettes
        $rqt = "UPDATE thumbnail_sources_entities SET source_class = 'Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Externallinks\\\\RecordExternallinksThumbnailSource' WHERE source_class = 'Pmb\\\\Thumbnail\\\\Models\\\\Sources\\\\Entities\\\\Record\\\\Amazon\\\\RecordAmazonThumbnailSource'";
        echo traite_rqt($rqt, "UPDATE thumbnail_sources_entitie WHERE source_class = 'Pmb\\Thumbnail\\Models\\Sources\\Entities\\Record\\Amazon\\RecordAmazonThumbnailSource'");

        //TS - changement du champ search_universe_description en text
        $rqt = "ALTER TABLE search_universes MODIFY search_universe_description TEXT";
        echo traite_rqt($rqt,"ALTER TABLE search_universes MODIFY search_universe_description IN TEXT");

        //TS - changement du champ search_segment_description en text
        $rqt = "ALTER TABLE search_segments MODIFY search_segment_description TEXT";
        echo traite_rqt($rqt,"ALTER TABLE search_segments MODIFY search_segment_description IN TEXT");

        //GN - Ajout d'un champ search_segment_data pour stocker des données
        $rqt = "ALTER TABLE search_segments ADD search_segment_data varchar(255)";
        echo traite_rqt($rqt,"ALTER TABLE search_segments ADD search_segment_data");

        //DG - Modification de la taille du champ watch_boolean_expression en text
        $rqt = "ALTER TABLE docwatch_watches MODIFY watch_boolean_expression TEXT";
        echo traite_rqt($rqt,"ALTER TABLE docwatch_watches MODIFY watch_boolean_expression IN TEXT");

        //DG - Modification de la taille du champ datasource_boolean_expression en text
        $rqt = "ALTER TABLE docwatch_datasources MODIFY datasource_boolean_expression TEXT";
        echo traite_rqt($rqt,"ALTER TABLE docwatch_datasources MODIFY datasource_boolean_expression IN TEXT");

        //QV - Refonte DSI ajout des descripteurs
        $rqt = "CREATE TABLE IF NOT EXISTS dsi_diffusion_descriptors (
            num_diffusion int(11) NOT NULL DEFAULT 0,
            num_noeud int(11) NOT NULL DEFAULT 0,
            diffusion_descriptor_order int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (num_diffusion, num_noeud)
        )";
        echo traite_rqt($rqt, "CREATE TABLE dsi_diffusion_descriptors");

        //QV - Refonte DSI correction du commentaire dsi_active
        $rqt = "UPDATE parametres SET comment_param = 'D.S.I activée ? \r\n 0: Non \r\n 1: Oui \r\n 2: Oui (refonte)' WHERE type_param = 'dsi' AND sstype_param = 'active';";
        echo traite_rqt($rqt, "UPDATE parametres SET comment_param for dsi_active");

        //QV - Refonte Portail correction du commentaire cms_active
        $rqt = "UPDATE parametres SET comment_param = 'Module \'Portail\' activé.\r\n 0 : Non.\r\n 1 : Oui.\r\n 2 : Oui (refonte).' WHERE type_param = 'cms' AND sstype_param = 'active';";
        echo traite_rqt($rqt, "UPDATE parametres SET comment_param for cms_active");

        // DG - Table de cache des ISBD d'entités
        $rqt = "CREATE TABLE IF NOT EXISTS entities (
				num_entity int(10) UNSIGNED NOT NULL DEFAULT 0,
				type_entity int(3) UNSIGNED NOT NULL DEFAULT 0,
				entity_isbd text NOT NULL,
				PRIMARY KEY(num_entity, type_entity)
			)";
        echo traite_rqt($rqt,"CREATE TABLE entities");

        // DG / JP - Paramètre d'activation du module MFA
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'security' and sstype_param='mfa_active'")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
        			VALUES (0, 'security', 'mfa_active', '0', '0', 'Double authentification activée.\r\n 0 : Non.\r\n 1 : Oui.')";
            echo traite_rqt($rqt, "INSERT security_mfa_active INTO parametres") ;
        }

        //RT - Modification commentaire accessibility
        $rqt = "UPDATE parametres SET comment_param = 'Accessibilité activée.\n0 : Non.\n1 : Oui.\n2 : Oui + compatibilité REM (unité CSS)' WHERE type_param = 'opac' AND sstype_param = 'accessibility'";
        echo traite_rqt($rqt,"UPDATE parametres SET comment_param for accessibility");

        //RT - TS Ajout paramètre d'activation de l'autocomplétion en recherche simple
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'opac' and sstype_param='search_autocomplete'")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
        			VALUES (0, 'opac', 'search_autocomplete', '0', '0', 'Autocomplétion en recherche simple activée.\r\n 0 : Non.\r\n 1 : Oui.', 'c_recherche')";
            echo traite_rqt($rqt, "INSERT opac_search_autocomplete INTO parametres") ;
        }

        // DG - Log des diffusions de bannettes - détails des équations exécutées au remplissage
        $rqt = "ALTER TABLE bannettes_diffusions ADD diffusion_equations text";
        echo traite_rqt($rqt,"ALTER TABLE bannettes_diffusions ADD diffusion_equations");

        // JP - Ajout du champ modified sur un content_buffer
        $rqt = "ALTER TABLE dsi_content_buffer ADD modified tinyint(1) NOT NULL DEFAULT 0 AFTER content" ;
        echo traite_rqt($rqt,"ALTER dsi_content_buffer ADD modified");

        // DB / QV : Compatibilité MySQL 8
        // Utilisation des back quotes (`) pour Mysql 8. NE PAS LES SUPPRIMER
        if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM thumbnail_sources_entities LIKE 'rank' ")) != 0 ) {
            $rqt = "ALTER TABLE thumbnail_sources_entities CHANGE `rank` ranking int(10) NOT NULL DEFAULT 0";
            echo traite_rqt($rqt,"ALTER TABLE thumbnail_sources_entities CHANGE rank ranking");
        }
        if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM notices_relations LIKE 'rank' ")) != 0 ) {
            $rqt = "ALTER TABLE notices_relations CHANGE `rank` ranking int(11)  NOT NULL DEFAULT 0";
            echo traite_rqt($rqt,"ALTER TABLE notices_relations CHANGE rank ranking");
        }

        // DG - Modification de la date de création en datetime
        $rqt = "ALTER TABLE cms_documents MODIFY document_create_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
        echo traite_rqt($rqt,"ALTER TABLE cms_documents MODIFY document_create_date DATETIME");

        // DG - Ajout du champ cache_cadre_header sur la table cms_cache_cadres
        $rqt = "ALTER TABLE cms_cache_cadres ADD cache_cadre_header MEDIUMTEXT NOT NULL" ;
        echo traite_rqt($rqt,"ALTER cms_cache_cadres ADD cache_cadre_header");

        //GN - Alerter l'utilisateur par mail des nouvelles inscriptions a une animation proposées ?
        $rqt = "ALTER TABLE users ADD user_alert_animation_mail INT(1) UNSIGNED NOT NULL DEFAULT 0 after deflt_animation_unique_registration";
        echo traite_rqt($rqt,"ALTER TABLE users add user_alert_animation_mail default 0");

        //GN - Ajout d'un email pour recevoir des mails, car l'autre email sert a envoyer des emails, et des fois on ne peut pas la consulter
        $rqt = "ALTER TABLE users ADD user_email_recipient VARCHAR(255) default '' after user_alert_animation_mail";
        echo traite_rqt($rqt,"ALTER TABLE users add user_email_recipient default ''");

        //GN - Ajout d'une table pour enregistrer les transactions de paiement
        $rqt = "CREATE TABLE IF NOT EXISTS transaction_payments (
            id INT(11) unsigned auto_increment,
            order_number INT NOT NULL,
            payment_date DATETIME NOT NULL,
            payment_status INT(1) NOT NULL,
            payment_organization_status VARCHAR(10) NULL,
            num_user INT NOT NULL,
            num_organization INT(1)NOT NULL,
            PRIMARY KEY (id),
            UNIQUE order_number (order_number)
            ) ";
        echo traite_rqt($rqt,"create table transaction_payments");

        //GN - Ajout d'une table pour enregistrer les organismes de paiement
        $rqt = "CREATE TABLE IF NOT EXISTS payment_organization (
            id INT(11) unsigned auto_increment,
            name VARCHAR(255) NOT NULL,
            data mediumblob NULL,
            PRIMARY KEY (id)
            ) ";
        echo traite_rqt($rqt,"create table payment_organization");

        //GN - Ajout d'une table d'une table de liaison entre les payments et les comptes
        $rqt = "CREATE TABLE IF NOT EXISTS transaction_compte_payments (
            id INT(11) unsigned auto_increment,
            transaction_num INT NOT NULL,
            compte_num INT NOT NULL,
            amount INT NOT NULL,
            PRIMARY KEY (id)
            )";
        echo traite_rqt($rqt,"create table transaction_compte_payments");

        //RT - Ajout paramètres utilisateur pour les statuts dans la D.S.I.
        $rqt = "ALTER TABLE users ADD deflt_dsi_diffusion_default_status TINYINT UNSIGNED DEFAULT 1 NOT NULL";
        echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_dsi_diffusion_default_status TINYINT UNSIGNED DEFAULT 1 NOT NULL");
        $rqt = "ALTER TABLE users ADD deflt_dsi_product_default_status TINYINT UNSIGNED DEFAULT 1 NOT NULL";
        echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_dsi_product_default_status TINYINT UNSIGNED DEFAULT 1 NOT NULL");

        // QV - Parametre Content Security Policy (CSP)
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='content_security_policy' "))==0){
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion, section_param)
				VALUES (NULL, 'opac', 'content_security_policy', '', 'Permet de définir la valeur pour le content security policy (CSP) ou stratégie de sécurité du contenu afin de renforcer la sécurité de votre OPAC.\n\nLaisser la valeur à vide pour ne spécifier aucune directive de sécurité de contenu.', 0, 'a_general')";
            echo traite_rqt($rqt,"insert opac_content_security_policy = '' into parametres");
        }

        // DG / JP - Double authentification - Services
        $rqt = "CREATE TABLE IF NOT EXISTS mfa_services (
        		id int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				context VARCHAR(255) NOT NULL DEFAULT '',
				application tinyint(1) NOT NULL DEFAULT 0,
				mail tinyint(1) NOT NULL DEFAULT 0,
				sms tinyint(1) NOT NULL DEFAULT 0,
				required tinyint(1) NOT NULL DEFAULT 0,
				suggest_message MEDIUMTEXT,
				UNIQUE(context)
			)";
        echo traite_rqt($rqt,"CREATE TABLE mfa_services");

        // DG / JP - Double authentification - Configuration mail
        $rqt = "CREATE TABLE IF NOT EXISTS mfa_mail (
        		id int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				context VARCHAR(255) NOT NULL DEFAULT '',
				object VARCHAR(255) NOT NULL DEFAULT '',
				content MEDIUMTEXT,
				UNIQUE(context)
			)";
        echo traite_rqt($rqt,"CREATE TABLE mfa_mail");

        // DG / JP - Double authentification - Configuration SMS
        $rqt = "CREATE TABLE IF NOT EXISTS mfa_sms (
        		id int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				context VARCHAR(255) NOT NULL DEFAULT '',
				content MEDIUMTEXT,
				UNIQUE(context)
			)";
        echo traite_rqt($rqt,"CREATE TABLE mfa_sms");

        // DG / JP - Double authentification - Configuration OTP
        $rqt = "CREATE TABLE IF NOT EXISTS mfa_otp (
        		id int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				context VARCHAR(255) NOT NULL DEFAULT '',
				hash_method VARCHAR(255) NOT NULL DEFAULT '',
				lifetime int(11) NOT NULL DEFAULT 0,
				length_code int(2) NOT NULL DEFAULT 0,
				UNIQUE(context)
			)";
        echo traite_rqt($rqt,"CREATE TABLE mfa_otp");

        // DG / JP - Double authentification - Utilisateurs
        $rqt = "ALTER TABLE users ADD mfa_secret_code varchar(255)";
        echo traite_rqt($rqt,"ALTER TABLE users ADD mfa_secret_code");
        $rqt = "ALTER TABLE users ADD mfa_favorite varchar(255)";
        echo traite_rqt($rqt,"ALTER TABLE users ADD mfa_favorite");

        // DG / JP - Double authentification - Lecteurs
        $rqt = "ALTER TABLE empr ADD mfa_secret_code varchar(255)";
        echo traite_rqt($rqt,"ALTER TABLE empr ADD mfa_secret_code");
        $rqt = "ALTER TABLE empr ADD mfa_favorite varchar(255)";
        echo traite_rqt($rqt,"ALTER TABLE empr ADD mfa_favorite");

        //DB / GN - Table de stockage des modeles d'authentification externe
        $rqt = "CREATE TABLE IF NOT EXISTS authentication_models (
        			id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        			source_name varchar(255) NOT NULL DEFAULT '',
        			name varchar(255) NOT NULL DEFAULT '',
        			settings mediumblob NOT NULL,
        			context tinyint(1) NOT NULL DEFAULT 0
        		)";
        echo traite_rqt($rqt,"CREATE TABLE authentication_models");

        //DB / GN - Table de stockage des configurations d'authentification externe
        $rqt = "CREATE TABLE IF NOT EXISTS authentication_configs (
        			id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        			source_name varchar(255) NOT NULL DEFAULT '',
        			name varchar(255) NOT NULL DEFAULT '',
        			settings mediumblob NOT NULL,
        			context tinyint(1) NOT NULL DEFAULT 0,
        			ranking tinyint(1) NOT NULL DEFAULT 0
        		)";
        echo traite_rqt($rqt,"CREATE TABLE authentication_configs");

        // DB / GN - Paramètre d'activation connexion OPAC
        if ( pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'security' and sstype_param='allow_internal_opac_authentication'")) == 0 ) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
        			VALUES (0, 'security', 'allow_internal_opac_authentication', '0', '1', 'Autorise l\'authentification interne en OPAC.')";
            echo traite_rqt($rqt, "INSERT allow_internal_opac_authentication INTO parametres") ;
        }

        // DB / GN - Paramètre d'activation connexion GESTION
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'security' and sstype_param='allow_internal_gestion_authentication'")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
        			VALUES (0, 'security', 'allow_internal_gestion_authentication', '0', '1', 'Autorise l\'authentification interne en Gestion.')";
            echo traite_rqt($rqt, "INSERT allow_internal_gestion_authentication INTO parametres") ;
        }
        // DB / QV : Compatibilité MySQL 8
        // Utilisation des back quotes (`) pour Mysql 8. NE PAS LES SUPPRIMER
        // rank est un mot reserve, d'ou l'utilisation de back quotes
        if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM authentication_configs LIKE 'rank' ")) != 0 ) {
            $rqt = "ALTER TABLE authentication_configs CHANGE `rank` ranking tinyint(1)  NOT NULL DEFAULT 0";
            echo traite_rqt($rqt,"ALTER TABLE authentication_configs CHANGE rank ranking");
        }
        // DG - TS - Parametre pour définir la taille maximale du cache des images en gestion
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_cache_size' "))==0){
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion)
					VALUES (NULL, 'pmb', 'img_cache_size', '100', 'Taille maximale du cache des images en Mo. Paramètre modifiable uniquement via l\'application.', 1)";
            echo traite_rqt($rqt,"insert pmb_img_cache_size = '100' into parametres ");
        }
        // DG - TS - Parametre pour définir la taille maximale du cache des images en opac
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='img_cache_size' "))==0){
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion, section_param)
					VALUES (NULL, 'opac', 'img_cache_size', '100', 'Taille maximale du cache des images en Mo. Paramètre modifiable uniquement via l\'application.', 1, 'a_general')";
            echo traite_rqt($rqt,"insert opac_img_cache_size = '100' into parametres ");
        }
        // DG - TS - Parametre pour définir la volumétrie d'images à supprimer lors de la saturation du cache en gestion
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_cache_clean_size' "))==0){
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion)
					VALUES (NULL, 'pmb', 'img_cache_clean_size', '20', 'Pourcentage du nombre d\'images à supprimer lors de la saturation du cache.  Paramètre modifiable uniquement via l\'application.', 1)";
            echo traite_rqt($rqt,"insert pmb_img_cache_clean_size = '20' into parametres ");
        }
        // DG - TS - Parametre pour définir la volumétrie d'images à supprimer lors de la saturation du cache en opac
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='img_cache_clean_size' "))==0){
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion, section_param)
					VALUES (NULL, 'opac', 'img_cache_clean_size', '20', 'Pourcentage du nombre d\'images à supprimer lors de la saturation du cache.  Paramètre modifiable uniquement via l\'application.', 1, 'a_general')";
            echo traite_rqt($rqt,"insert opac_img_cache_clean_size = '20' into parametres ");
        }
        // DG - TS - Parametre pour définir le type des images stockees dans le cache opac
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='img_cache_type' "))==0){
            $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion, section_param)
					VALUES (NULL, 'opac', 'img_cache_type', 'webp', 'Type d\'image à stocker dans le cache. Paramètre modifiable uniquement via l\'application.', 1, 'a_general')";
            echo traite_rqt($rqt,"insert opac_img_cache_type = 'webp' into parametres ");
        }

        // DB - Modification des tables récolteur
        $rqt = "ALTER TABLE harvest_field ADD harvest_field_ufield varchar(100) DEFAULT NULL AFTER harvest_field_xml_id";
        echo traite_rqt($rqt,"ALTER TABLE harvest_field ADD harvest_field_ufields");

        $rqt = "ALTER TABLE harvest_search_field CHANGE num_field num_field VARCHAR(25) NOT NULL DEFAULT '' ";
        echo traite_rqt($rqt,"ALTER TABLE harvest_search_field CHANGE num_field VARCHAR(25)");

        $rqt = "ALTER TABLE harvest_src DROP harvest_src_pmb_unimacfield, DROP harvest_src_pmb_unimacsubfield, DROP harvest_src_unimacsubfield";
        echo traite_rqt($rqt,"ALTER TABLE harvest_src DROP harvest_src_pmb_unimacfield, harvest_src_pmb_unimacsubfield, harvest_src_unimacsubfield");

        if ( pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM harvest_src LIKE 'harvest_src_unimacfield' ")) != 0 ) {
            $rqt = "ALTER TABLE harvest_src CHANGE harvest_src_unimacfield harvest_src_ufield VARCHAR(255) NOT NULL DEFAULT '' ";
            echo traite_rqt($rqt,"ALTER TABLE harvest_src CHANGE harvest_src_unimacfield harvest_src_ufield ");
        }

        //DG - Ajout d'une clé primaire aux listes associées aux champs personnalisés
        $rqt = "ALTER TABLE anim_animation_custom_lists ADD id_anim_animation_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE anim_animation_custom_lists ADD id_anim_animation_custom_list ");

        $rqt = "ALTER TABLE anim_price_type_custom_lists ADD id_anim_price_type_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE anim_price_type_custom_lists ADD id_anim_price_type_custom_list ");

        $rqt = "ALTER TABLE author_custom_lists ADD id_author_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE author_custom_lists ADD id_author_custom_list ");

        $rqt = "ALTER TABLE authperso_custom_lists ADD id_authperso_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE authperso_custom_lists ADD id_authperso_custom_list ");

        $rqt = "ALTER TABLE categ_custom_lists ADD id_categ_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE categ_custom_lists ADD id_categ_custom_list ");

        $rqt = "ALTER TABLE cms_editorial_custom_lists ADD id_cms_editorial_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_lists ADD id_cms_editorial_custom_list ");

        $rqt = "ALTER TABLE collection_custom_lists ADD id_collection_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE collection_custom_lists ADD id_collection_custom_list ");

        $rqt = "ALTER TABLE collstate_custom_lists ADD id_collstate_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE collstate_custom_lists ADD id_collstate_custom_list ");

        $rqt = "ALTER TABLE demandes_custom_lists ADD id_demandes_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE demandes_custom_lists ADD id_demandes_custom_list ");

        $rqt = "ALTER TABLE empr_custom_lists ADD id_empr_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE empr_custom_lists ADD id_empr_custom_list ");

        $rqt = "ALTER TABLE explnum_custom_lists ADD id_explnum_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE explnum_custom_lists ADD id_explnum_custom_list ");

        $rqt = "ALTER TABLE expl_custom_lists ADD id_expl_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE expl_custom_lists ADD id_expl_custom_list ");

        $rqt = "ALTER TABLE notices_custom_lists ADD id_notices_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE notices_custom_lists ADD id_notices_custom_list ");

        $rqt = "ALTER TABLE gestfic0_custom_lists ADD id_gestfic0_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_lists ADD id_gestfic0_custom_list ");

        $rqt = "ALTER TABLE indexint_custom_lists ADD id_indexint_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE indexint_custom_lists ADD id_indexint_custom_list ");

        $rqt = "ALTER TABLE pret_custom_lists ADD id_pret_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE pret_custom_lists ADD id_pret_custom_list ");

        $rqt = "ALTER TABLE publisher_custom_lists ADD id_publisher_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE publisher_custom_lists ADD id_publisher_custom_list ");

        $rqt = "ALTER TABLE serie_custom_lists ADD id_serie_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE serie_custom_lists ADD id_serie_custom_list ");

        $rqt = "ALTER TABLE skos_custom_lists ADD id_skos_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE skos_custom_lists ADD id_skos_custom_list ");

        $rqt = "ALTER TABLE subcollection_custom_lists ADD id_subcollection_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_lists ADD id_subcollection_custom_list ");

        $rqt = "ALTER TABLE tu_custom_lists ADD id_tu_custom_list INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
        echo traite_rqt($rqt,"ALTER TABLE tu_custom_lists ADD id_tu_custom_list ");

        // GN - Ajout d'une table pour stocker les paramètres de l'IA
        $rqt = "CREATE TABLE IF NOT EXISTS ai_settings (
            id_ai_setting int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            settings_ai_settings mediumblob NOT NULL,
            active_ai_settings tinyint(1) NOT NULL DEFAULT 0
        )";
        echo traite_rqt($rqt,"CREATE TABLE ai_settings");

        // QV & GN : Ajout d'un paramètre pour activer le module IA
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='ai' AND sstype_param='active'")) == 0) {
            $rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
            VALUES ('ai', 'active', '0', 'Activé le module d\'intelligence artificielle.\n 0 : Non.\n 1 : Oui.', '', 0)";
            echo traite_rqt($rqt, 'INSERT INTO parametres artificial_intelligence');
        }

        // QV & GN : Création de la table pour les sessions avec l'IA
        $rqt = "CREATE TABLE IF NOT EXISTS ai_session_semantique (
            id_ai_session_semantique int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            ai_session_semantique_name varchar(255) NOT NULL,
            ai_session_semantique_questions mediumblob NOT NULL,
            ai_session_semantique_reponses mediumblob NOT NULL,
            ai_session_semantique_num_objects mediumblob NOT NULL,
            ai_session_semantique_anonyme_sessid varchar(12) NOT NULL DEFAULT '',
            INDEX ai_session_semantique_anonyme_sessid (ai_session_semantique_anonyme_sessid)
        )";
        echo traite_rqt($rqt,"CREATE TABLE ai_session_semantique");


        // DB - PM - JP - Ajout des tables du dashboard
        $rqt = "CREATE TABLE IF NOT EXISTS dashboard (
            id_dashboard int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            dashboard_name varchar(255) NOT NULL DEFAULT '',
            dashboard_editable tinyint(1) NOT NULL DEFAULT 0,
            num_user int(5) UNSIGNED NOT NULL DEFAULT 0
        )";
        echo traite_rqt($rqt,"CREATE TABLE dashboard");

        $rqt = "CREATE TABLE IF NOT EXISTS widget (
            id_widget int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            widget_name varchar(255) NOT NULL DEFAULT '',
            widget_editable tinyint(1) NOT NULL DEFAULT 0,
            widget_type varchar(255) NOT NULL DEFAULT '',
            num_user int(5) UNSIGNED NOT NULL DEFAULT 0,
            widget_shareable tinyint(1) NOT NULL DEFAULT 0,
            widget_settings mediumblob NOT NULL
        )";
        echo traite_rqt($rqt,"CREATE TABLE widget");

        $rqt = "CREATE TABLE IF NOT EXISTS dashboard_users_groups (
            num_dashboard int(10) UNSIGNED NOT NULL DEFAULT 0,
            num_users_groups int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (num_dashboard, num_users_groups)
        )";
        echo traite_rqt($rqt,"CREATE TABLE dashboard_users_groups");

        $rqt = "CREATE TABLE IF NOT EXISTS dashboard_widget (
            num_dashboard int(10) UNSIGNED NOT NULL DEFAULT 0,
            num_widget int(10) UNSIGNED NOT NULL DEFAULT 0,
            dashboard_widget_settings mediumblob NOT NULL,
            PRIMARY KEY (num_dashboard, num_widget)
        )";
        echo traite_rqt($rqt,"CREATE TABLE dashboard_widget");

        // TS & DG : Création de la table des jeux de facettes
        $rqt = "CREATE TABLE IF NOT EXISTS facettes_sets (
				id_set int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                type varchar(255) NOT NULL DEFAULT '',
				name varchar(255) NOT NULL DEFAULT '',
                num_user int(5) UNSIGNED NOT NULL,
				users_groups mediumblob NOT NULL,
                ranking int(10) NOT NULL DEFAULT 0
			)";
        echo traite_rqt($rqt,"CREATE TABLE facettes_sets");

        // TS & DG - Ajout de l'identifiant du jeu de facettes associé
        $rqt = "ALTER TABLE facettes add num_facettes_set int(10) not null default 0";
        echo traite_rqt($rqt,"ALTER TABLE facettes add num_facettes_set ");
        $rqt = "ALTER TABLE facettes ADD INDEX i_num_facettes_set(num_facettes_set)";
        echo traite_rqt($rqt,"ALTER TABLE facettes ADD index i_num_facettes_set");

        // TS & DG - Ajout de l'identifiant du jeu de facettes externe associé
        $rqt = "ALTER TABLE facettes_external add num_facettes_set int(10) not null default 0";
        echo traite_rqt($rqt,"ALTER TABLE facettes_external add num_facettes_set ");
        $rqt = "ALTER TABLE facettes_external ADD INDEX i_num_facettes_set(num_facettes_set)";
        echo traite_rqt($rqt,"ALTER TABLE facettes_external ADD index i_num_facettes_set");

        //DB - Nettoyage table translation
        $rqt = "delete from translation where trans_table='parametres' and trans_num not in (select id_param from parametres where concat(type_param,'_',sstype_param) in ('".implode("','", parameter::TRANSLATED_PARAMETERS)."'))";
        echo traite_rqt($rqt,"CLEAN TABLE translation");

        //DG - Paramètre pour afficher ou non le bandeau d'acceptation des cookies sur le thème DSFR
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cookies_consent_dsfr' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'cookies_consent_dsfr', '0', 'Utiliser le thème DSFR (Système de Design de l\'État) pour le bandeau d\'acceptation des cookies et des traceurs ? \n0 : Non 1 : Oui','a_general',0)";
            echo traite_rqt($rqt,"insert opac_cookies_consent_dsfr into parametres");
        }

        // TS & DG : Création de la table de liaison des jeux de facettes avec les utilisateurs
        $rqt = "CREATE TABLE IF NOT EXISTS facettes_sets_users (
				num_set int(10) UNSIGNED NOT NULL,
                num_user int(5) UNSIGNED NOT NULL,
                visible int(1) UNSIGNED NOT NULL,
                ranking int(10) NOT NULL DEFAULT 0,
                primary key (num_set, num_user)
			)";
        echo traite_rqt($rqt,"CREATE TABLE facettes_sets_users");

        // TS & DG - Suppression du rang pour une gestion par utilisateur
        $rqt = "ALTER TABLE facettes_sets DROP ranking";
        echo traite_rqt($rqt,"ALTER TABLE facettes_sets DROP ranking");

        // DB - Ajout index sur explnum_mimetype
        $add_index = true;
        $req = "SHOW INDEX FROM explnum WHERE Key_name='i_explnum_mimetype' ";
        $res = pmb_mysql_query($req);
        if($res && pmb_mysql_num_rows($res)){
            $add_index=false;
        }
        if($add_index){
            @set_time_limit(0);
            pmb_mysql_query("set wait_timeout=28800");
            $rqt = "ALTER TABLE explnum ADD INDEX i_explnum_mimetype(explnum_mimetype)";
            echo traite_rqt($rqt,"alter table explnum add index i_explnum_mimetype");
        }

        //DG - Date de diffusion sur les décomptes
        $rqt = "ALTER TABLE rent_accounts ADD account_diffusion_date datetime";
        echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_diffusion_date");

        //DG - Date de fin de droits sur les décomptes
        $rqt = "ALTER TABLE rent_accounts ADD account_rights_date datetime";
        echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_rights_date");

        //DG - Droits illimités sur les décomptes
        $rqt = "ALTER TABLE rent_accounts ADD account_unlimited_rights int(1) unsigned not null default 0";
        echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_unlimited_rights");

        // QV - Ajout d'un paramètre pour la recherche des synonymes (Gestion et OPAC)
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' AND sstype_param='synonym_search' "))==0){
            $rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES('opac', 'synonym_search', '1', 'Activer la recherche des synonymes d\'un mot\n0 : non\n1 : oui', 'c_recherche', 0)" ;
            echo traite_rqt($rqt,"INSERT opac_synonym_search INTO parametres") ;
        }
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' AND sstype_param='synonym_search' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES(0, 'pmb', 'synonym_search', '1', 'Activer la recherche des synonymes d\'un mot\n0 : non\n1 : oui', 'c_recherche', 0)" ;
            echo traite_rqt($rqt,"INSERT pmb_synonym_search INTO parametres") ;
        }

        // JP - Ajout d'un paramètre pour la connexion à l'API sphinx pour le multi-bases
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'sphinx' AND sstype_param='api_connect' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
                VALUES (NULL, 'sphinx', 'api_connect', '127.0.0.1:9312', 'Paramètre de connexion à l\'API du serveur sphinx :\n hote:port', '')";
            echo traite_rqt($rqt,"INSERT sphinx_api_connect = '127.0.0.1:9312' INTO parametres ");
        }

        // QV - Ajout des options sur les statuts d'autorités pour la recherche en catalogage
        $rqt = "ALTER TABLE authorities_statuts ADD authorities_statuts_autocomplete int(1) unsigned not null default 1";
        echo traite_rqt($rqt,"ALTER TABLE authorities_statuts ADD authorities_statuts_autocomplete");

        $rqt = "ALTER TABLE authorities_statuts ADD authorities_statuts_searcher_autority int(1) unsigned not null default 1";
        echo traite_rqt($rqt,"ALTER TABLE authorities_statuts ADD authorities_statuts_searcher_autority");

        // DB - Ajout d'un champ host_name et d'un champ alive_at dans la table taches
        $rqt = "ALTER TABLE taches ADD host_name varchar(255) NOT NULL DEFAULT '' AFTER id_process,  ADD alive_at TIMESTAMP NULL DEFAULT NULL AFTER host_name ";
        echo traite_rqt($rqt,"ALTER TABLE taches ADD host_name, alive_at ");

        // JP - Ajout d'un champ pour le label des boutons dans les recherches prédéfinies opac (RGAA)
        $rqt = "ALTER TABLE search_persopac ADD search_button_label VARCHAR(255) NOT NULL DEFAULT '' AFTER search_shortname";
        echo traite_rqt($rqt, "ALTER TABLE search_persopac ADD search_button_label");

        // +-------------------------------------------------+
        echo "</table>";
        $rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' ";
        $res = pmb_mysql_query($rqt) ;
        $rqt = "update parametres set valeur_param='0' where type_param='pmb' and sstype_param='bdd_subversion' ";
        $res = pmb_mysql_query($rqt) ;
        echo "<strong><font color='#FF0000'>".$msg[1807].$action." !</font></strong><br />";
        break;

    default:
        include("$include_path/messages/help/$lang/alter.txt");
        break;
}

