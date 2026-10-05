<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CertificateOrm.php,v 1.1 2022/04/29 15:17:09 gneveu Exp $

namespace Pmb\Digitalsignature\Orm;

use Pmb\Common\Orm\Orm;

class CertificateOrm extends Orm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "certificates";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id";


    /**
     *
     * @var int
     */
    protected $id = 0;
    
    /**
     *
     * @var string
     */
    protected $name = "";

    /**
     *
     * @var string
     */
    protected $private_key = "";

    /**
     *
     * @var string
     */
    protected $cert = "";

}