<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl.class.php,v 1.2.18.1 2025/03/04 15:50:02 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class openurl_root
{

    public static $uri = "info:ofi";

    public static $serialize = "";

    public $type;
}