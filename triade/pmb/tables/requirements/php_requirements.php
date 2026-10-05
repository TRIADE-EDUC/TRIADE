<?php
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: php_requirements.php,v 1.4.2.1 2024/05/23 12:35:57 dbellamy Exp $


/**
 * Liste des pre-requis PHP
 *
 * @var array $php_requirements
 *
 * Format :
 * "version" => [
 *      "min"   => version php min
 *      "max"   => version php max
 * ],
 * "options" => [
 *      "type"               => integer|string|set|boolean
 *      "mode"               => php.ini|PHP_INI_USER|PHP_INI_PERDIR|PHP_INI_SYSTEM|PHP_INI_ALL (inutilisé actuellement)
 *      "suggested_value"    => Valeur suggeree
 *      "min_value"          => Valeur minimale
  *     "max_value"          => Valeur maximale
 *      "allowed_values"     => Tableau des valeurs possibles
 *      "required"           => 0|1
 *      "extension"          => Extension associee
 * ]
 */

$php_requirements['version'] = [
    'min' => "7.3",
    'max' => "8.3",
];

$php_requirements['options'] = [
    "date.timezone" => [
        "type" => "string",
        "mode" => "PHP_INI_ALL",
        "suggested_value" => "Europe/Paris",
        "required" => 1,
    ],
    "display_errors" => [
        "type" => "boolean",
        "mode" => "PHP_INI_ALL",
        "suggested_value" => "Off",
    ],
    "expose_php" => [
        "type" => "boolean",
        "mode" => "php.ini",
        "suggested_value" => "Off",
    ],
    "max_execution_time" => [
        "type" => "integer",
        "mode" => "PHP_INI_ALL",
        "suggested_value" => ">= 300",
        "min_value" => "300",
    ],
    "max_input_vars" => [
        "type" => "integer",
        "mode" => "PHP_INI_PERDIR",
        "suggested_value" => ">= 50000",
        "min_value" => "50000",
    ],
    "memory_limit" => [
        "type" => "integer",
        "mode" => "PHP_INI_ALL",
        "suggested_value" => ">= 256M",
        "min_value" => "256M",
    ],
    "post_max_size" => [
        "type" => "integer",
        "mode" => "PHP_INI_PERDIR",
        "suggested_value" => ">= 64M",
        "min_value" => "64M"
    ],
    "upload_max_filesize" => [
        "type" => "integer",
        "mode" => "PHP_INI_PERDIR",
        "suggested_value" => ">= 64M",
        "min_value" => "64M"
    ]
];

