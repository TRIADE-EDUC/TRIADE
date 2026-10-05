<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationConfigsOrm.php,v 1.2 2023/08/29 15:31:35 dbellamy Exp $
namespace Pmb\Authentication\Orm;


if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AuthenticationConfigsOrm extends AuthenticationModelsOrm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "authentication_configs";

    /**
     *
     * @var integer
     */
    protected $ranking = 0;
}



