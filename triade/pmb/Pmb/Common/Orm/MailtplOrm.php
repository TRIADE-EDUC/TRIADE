<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailtplOrm.php,v 1.1 2021/03/01 15:54:17 qvarin Exp $

namespace Pmb\Common\Orm;

class MailtplOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "mailtpl";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_mailtpl";

    /**
     *
     * @var integer
     */
    protected $id_mailtpl = 0;

    /**
     *
     * @var string
     */
    protected $mailtpl_name = "";

    /**
     *
     * @var string
     */
    protected $mailtpl_objet = "";

    /**
     *
     * @var string
     */
    protected $mailtpl_tpl = "";

    /**
     *
     * @var string
     */
    protected $mailtpl_users = "";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}