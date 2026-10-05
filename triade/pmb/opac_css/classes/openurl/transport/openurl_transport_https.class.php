<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_transport_https.class.php,v 1.2.18.1 2025/03/04 16:22:49 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

require_once $class_path . "/openurl/transport/openurl_transport.class.php";

class openurl_transport_byref_https extends openurl_transport_byref
{
}

class openurl_transport_byval_https extends openurl_transport_byval
{
}

class openurl_transport_inline_https extends openurl_transport_inline
{
}