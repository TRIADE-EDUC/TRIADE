<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SignatureOrm.php,v 1.2 2022/05/02 07:40:42 gneveu Exp $

namespace Pmb\Digitalsignature\Orm;

use Pmb\Common\Orm\Orm;

class SignatureOrm extends Orm
{

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "digital_signature";

    /**
     * Primary Key
     *
     * @var integer
     */
    public static $idTableName = "id";

    /**
     *
     * @var int
     */
    protected $id = 0;

    /**
     *
     * @var int
     */
    protected $type = 0;

    /**
     *
     * @var string
     */
    protected $fields = "";

    /**
     *
     * @var string
     */
    protected $name = "";

    /**
     *
     * @var int
     */
    protected $upload_folder = 0;

    /**

     * @Relation 0n
     * @Orm Pmb\Digitalsignature\Orm\CertificateOrm
     * @RelatedKey id
     */
    protected $num_cert = 0;
    
    /**
     * @Relation 0n
     * @Orm Pmb\Digitalsignature\Orm\CertificateOrm
     * @Table certificates
     * @RelatedKey num_cert
     */
    protected $certificate = null;
    
}