<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UploadFolderModel.php,v 1.1 2022/04/29 15:17:09 gneveu Exp $

namespace Pmb\Common\Models;

use Pmb\Common\Orm\UploadFolderOrm;

class UploadFolderModel extends Model
{
    protected $ormName = "\Pmb\Common\Orm\UploadFolderOrm";
    
    public static function getUploadForlderList()
    {
        $list = UploadFolderOrm::findAll();
        return self::toArray($list);
    }
}