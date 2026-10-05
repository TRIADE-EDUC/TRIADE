<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EmprCategModel.php,v 1.1 2023/07/05 14:24:50 dbellamy Exp $

namespace Pmb\Common\Models;

use Pmb\Common\Orm\EmprCategOrm;

class EmprCategModel extends Model
{

    protected $ormName = "\Pmb\Common\Orm\EmprCategOrm";

    public $idCategEmpr;

    public $libelle;

    public $dureeAdhesion;

    public $tarifAbt;

    public $ageMin;

    public $ageMax;


    public static function getEmprCategList()
    {
        $list = EmprCategOrm::findAll();
        return static::toArray($list);
    }
}