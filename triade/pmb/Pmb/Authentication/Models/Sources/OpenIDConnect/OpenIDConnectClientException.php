<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OpenIDConnectClientException.php,v 1.2 2024/02/14 10:45:42 tsamson Exp $

namespace Pmb\Authentication\Models\Sources\OpenIDConnect;

use Exception;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

/**
 * OpenIDConnect Exception Class
 */
class OpenIDConnectClientException extends \Exception
{
}
