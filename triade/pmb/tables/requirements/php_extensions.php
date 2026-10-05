<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: php_extensions.php,v 1.2.2.1 2025/01/16 11:23:18 rtigero Exp $

/**
 * *Liste des extensions PHP requises
 *
 * @var array $php_extensions
 *
 * Format :
 * "extension" => [
 *      "required"   => 0|1
 *      "version"   => version min
 * ]
 */
$php_extensions = [
    "apcu" => [
        "required" => 0,
    ],
    "bz2" => [
        "required" => 1,
    ],
    "cas" => [
        "required" => 0,
    ],
    "curl" => [
        "required" => 1,
    ],
    "dom" => [
        "required" => 1,
    ],
    "exif" => [
        "required" => 0,
    ],
    "fileinfo" => [
        "required" => 1,
    ],
    "gd" => [
        "required" => 1,
    ],
    "iconv" => [
        "required" => 1,
    ],
    "imagick" => [
        "required" => 0,
    ],
    "intl" => [
        "required" => 1,
    ],
    "json" => [
        "required" => 1,
    ],
    "ldap" => [
        "required" => 0,
    ],
    "libxml" => [
        "required" => 1,
        "version" => ">= 2.8.0",
    ],
    "mbstring" => [
        "required" => 1,
    ],
    "mysqli" => [
        "required" => 1,
    ],
    "openssl" => [
        "required" => 1,
    ],
    "session" => [
        "required" => 1,
    ],
    "soap" => [
        "required" => 1,
    ],
    "sockets" => [
        "required" => 1,
    ],
    "sqlite3" => [
        "required" => 1,
    ],
    "xdiff" => [
        "required" => 0,
    ],
    "xml" => [
        "required" => 1,
    ],
    "xsl" => [
        "required" => 1,
    ],
    "yaz" => [
        "required" => 0,
    ],
    "zip" => [
        "required" => 1,
    ]
];

