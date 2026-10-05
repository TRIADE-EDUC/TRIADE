<?php
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mysql_requirements.php,v 1.7 2023/09/01 07:58:31 dbellamy Exp $


/**
 * Liste des pre-requis MySQL / MariaDB
 *
 * @var array $mysql_requirements
 *
 * Format :
 * "mysqlVersion" => [
 *      "min"   => version mysql min
 *      "max"   => version mysql max
 * ],
 * "mariadbVersion" => [
 *      "min"   => version mariadb min
 *      "max"   => version mariadb max
 * ],
 * "variables" => [
 *      "type"              => integer|string|set
 *      "mode"              => session|global|static
 *      "suggested_value"   => Valeur suggeree
 *      "min_value"         => Valeur minimale
 *      "max_value"         => Valeur maximale
 *      "allowed_values"    => Tableau des valeurs possibles
 *      "required"          => 0|1
 *      "pmb_var"           => Variable PMB a configurer
 */

$mysql_requirements['mysqlVersion'] = [
    "min"   => "5.6",
];
$mysql_requirements['mariadbVersion'] = [
    "min"   => "10.0",
];
$mysql_requirements['variables'] = [
    "max_allowed_packet" => [
        "type" => "integer",
        "mode" => "global",
        "suggested_value" => ">= 64M",
        "min_value" => "64M",
        "pmb_var" => "SQL_MAX_ALLOWED_PACKET",
    ],
    "sql_mode" => [
        "type" => "set",
        "mode" => "session",
        "suggested_value" => "NO_AUTO_CREATE_USER",
        "allowed_values" => ["NO_AUTO_CREATE_USER", ""],
        "pmb_var" => "SQL_MODE",
    ],
    "character_set_server" => [
        "type" => "string",
        "mode" => "session",
        "suggested_value" => "utf8, utf8mb3 ou latin1",
        "allowed_values" => ["utf8", "utf8mb3", "latin1"],
        "pmb_var" => "SQL_CHARACTER_SET_SERVER",
    ],
    "collation_server" => [
        "type" => "string",
        "mode" => "session",
        "suggested_value" => "utf8_unicode_ci, utf8mb3_unicode_ci ou latin1_swedish_ci",
        "allowed_values" => ["utf8_unicode_ci", "utf8mb3_unicode_ci", "latin1_swedish_ci"],
        "pmb_var" => "SQL_COLLATION_SERVER",

    ],
    "default_storage_engine" => [
        "type" => "string",
        "mode" => "session",
        "suggested_value" => "MyISAM ou InnoDB",
        "allowed_values" => ["MyISAM", "InnoDB"],
        "pmb_var"   => "SQL_MOTOR_TYPE",
    ],
    "open_files_limit" => [
        "type" => "integer",
        "mode" => "static",
        "suggested_value" => ">= 10000",
        "min_value" => "10000",
    ],
    "key_buffer_size" => [
        "type" => "integer",
        "mode" => "global",
        "suggested_value" => ">= 1G",
        "pmb_var" => "SQL_KEY_BUFFER_SIZE",
    ],
    "join_buffer_size" => [
        "type" => "integer",
        "mode" => "session",
        "suggested_value" => ">= 4M",
        "min_value" => "4M",
        "pmb_var" => "SQL_JOIN_BUFFER_SIZE",
    ],
    "connect_timeout" => [
        "type" => "integer",
        "mode" => "global",
        "suggested_value" => "10",
    ],
    "interactive_timeout" => [
        "type" => "integer",
        "mode" => "session",
        "suggested_value" => "300",
        "max_value" => "300",
        "pmb_var" => "SQL_WAIT_TIMEOUT",
    ],
    "wait_timeout" => [
        "type" => "integer",
        "mode" => "session",
        "suggested_value" => "300",
        "max_value" => "300",
        "pmb_var" => "SQL_WAIT_TIMEOUT",
    ],
    "query_cache_limit" => [
        "type" => "integer",
        "mode" => "global",
        "suggested_value" => ">= 2M",
        "min_value" => "2M",
        "pmb_var" => "SQL_QUERY_CACHE_LIMIT",
    ],
    "query_cache_size" => [
        "type" => "integer",
        "mode" => "global",
        "suggested_value" => ">= 2M",
        "min_value" => "16M",
        "pmb_var" => "SQL_QUERY_CACHE_SIZE",
    ],
    "tmp_table_size" => [
        "type" => "integer",
        "mode" => "session",
        "suggested_value" => ">= 256M",
        "min_value" => "256M",
        "pmb_var" => "SQL_TMP_TABLE_SIZE",
    ],
];
