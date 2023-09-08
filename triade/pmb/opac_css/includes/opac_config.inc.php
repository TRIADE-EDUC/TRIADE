<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_config.inc.php,v 1.16 2017-06-15 12:25:23 mbertin Exp $

// fichier de configuration de l'OPAC PMB
// the lang is set in start.inc.php why this piece of code?

// Character set = encodage des données. Attention ne pas modifier en cours d'utilisation, votre base de données serait pleine de caracteres bizarre !!!
$charset = "iso-8859-1";

// définition des types d'audit
define('AUDIT_NOTICE'	,    1);
define('AUDIT_EXPL'		,    2);
define('AUDIT_BULLETIN'	,    3);
define('AUDIT_ACQUIS'	,    4);
define('AUDIT_PRET'		,    5);
define('AUDIT_AUTHOR'	,    6);
define('AUDIT_COLLECTION',   7);
define('AUDIT_SUB_COLLECTION',8);
define('AUDIT_INDEXINT'	,    9);
define('AUDIT_PUBLISHER',    10);
define('AUDIT_SERIE'	,    11);
define('AUDIT_CATEG'	,    12);
define('AUDIT_TITRE_UNIFORME',13);
define('AUDIT_DEMANDE'	,    14);
define('AUDIT_ACTION'	,    15);
define('AUDIT_NOTE',16);
define('AUDIT_EDITORIAL_ARTICLE',20);
define('AUDIT_EDITORIAL_SECTION',21);
define('AUDIT_EXPLNUM',22);
define('AUDIT_CONCEPT', 23);

$CACHE_ENGINE = 'apcu';//Type de moteur de cache php utilisé
$CACHE_MAXTIME = 86400;//Duree de mise en cache
$KEY_CACHE_FILE_XML = 'key_cache_file_xml';//Prefix pour la cle des variables en cache pour les fichiers XML
		
@include_once("includes/opac_config_local.inc.php") ;