<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationTypesModel.php,v 1.3.4.2 2025/04/29 08:45:45 gneveu Exp $
namespace Pmb\Animations\Models;

use Pmb\Common\Models\Model;
use Pmb\Animations\Orm\AnimationTypesOrm;
use Pmb\Animations\Orm\AnimationOrm;

class AnimationTypesModel extends Model
{

    protected $ormName = "\Pmb\Animations\Orm\AnimationTypesOrm";
    
    public const IGNORE_PROPS_TOARRAY = [
        "structure",
        "datafetch",
        "ormName",
        "animations"
    ];

    public $hasAnimations = false;

    public $idType;

    public $label;

    public $animations;

    public static function getAnimationTypesList(): array
    {
        $animationTypes = AnimationTypesOrm::findAll();
        return self::toArray($animationTypes);
    }

    public static function delete($id)
    {
        if ($id != 1) {
            $animationTypes = AnimationTypesOrm::findById($id);
            if (empty(AnimationOrm::find('num_type', $id))) {
                $animationTypes->delete();
                return true;
            }
        }
        return false;
    }

    public static function save(object $data)
    {
        if (! empty($data->id)) {
            $types = new AnimationTypesOrm($data->id);
        } else {
            $types = new AnimationTypesOrm();
        }
        if (! empty($data->label)) {
            $result = AnimationTypesOrm::find('label', $data->label);
            if ((count($result) == 1 && $types->{AnimationTypesOrm::$idTableName} === $result[0]->{AnimationTypesOrm::$idTableName}) || empty($result)) {
                $types->label = $data->label;
                $types->save();
            }
        }
    }

    public function getEditAddData()
    {
        $this->hasAnimations = $this->hasAnimations();
        return $this;
    }

    public function hasAnimations()
    {
        $query = "select 1 from anim_animations where num_type = " . $this->id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result) > 0) {
            return true;
        }
        return false;
    }

    public static function checkExistType($label)
    {
        if (! empty(AnimationTypesOrm::find('label', $label))) {
            return true;
        }
        return false;
    }
}