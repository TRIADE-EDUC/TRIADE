<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UploadFolderOrm.php,v 1.1.8.1 2024/06/17 12:04:37 jparis Exp $

namespace Pmb\Common\Orm;

class UploadFolderOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "upload_repertoire";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "repertoire_id";

    /**
     *
     * @var integer
     */
    protected $repertoire_id = 0;

    /**
     *
     * @var string
     */
    protected $repertoire_nom = "";

    /**
     *
     * @var string
     */
    protected $repertoire_url = "";

    /**
     *
     * @var string
     */
    protected $repertoire_path = "";

    /**
     *
     * @var integer
     */
    protected $repertoire_navigation = 0;

    /**
     *
     * @var integer
     */
    protected $repertoire_subfolder = 0;

    /**
     *
     * @var integer
     */
    protected $repertoire_hachage = 0;

    /**
     *
     * @var integer
     */
    protected $repertoire_utf8 = 0;

    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    public function getUploadForlderList()
    {
        $return = [];
        $uploadFolderList = $this->findAll();
        foreach ($uploadFolderList as $uploadFolder) {
            $return[$uploadFolder->repertoire_id] = $uploadFolder->repertoire_nom;
        }
        return $return;
    }
}
