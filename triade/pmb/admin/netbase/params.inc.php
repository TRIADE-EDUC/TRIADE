<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: params.inc.php,v 1.11.2.2 2024/09/18 12:10:22 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $pmb_clean_nb_elements, $pmb_clean_nb_elements_by_field, $pmb_clean_nb_elements_by_callable;

define('NOEXPL_PAQUET_SIZE', $pmb_clean_nb_elements);
define('REINDEX_PAQUET_SIZE', $pmb_clean_nb_elements);
define('REINDEX_GLOBAL_PAQUET_SIZE', 2000);
define('REINDEX_BY_FIELDS_PAQUET_SIZE', $pmb_clean_nb_elements_by_field ?? 50000);
define('REINDEX_BY_CALLABLES_FIELDS_PAQUET_SIZE', $pmb_clean_nb_elements_by_callable ?? 5000);
define('ACQUISITION_PAQUET_SIZE', $pmb_clean_nb_elements);
define('EMPR_PAQUET_SIZE', $pmb_clean_nb_elements);
define('GAUGE_SIZE', 560);