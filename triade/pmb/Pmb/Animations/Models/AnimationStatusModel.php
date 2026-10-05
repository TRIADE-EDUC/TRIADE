<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationStatusModel.php,v 1.11.4.1 2025/04/29 08:45:45 gneveu Exp $
namespace Pmb\Animations\Models;

use Pmb\Common\Models\Model;
use Pmb\Animations\Orm\AnimationStatusOrm;
use Pmb\Animations\Orm\AnimationOrm;

class AnimationStatusModel extends Model
{

    protected $ormName = "\Pmb\Animations\Orm\AnimationStatusOrm";

    public $hasAnimations = false;

    public $idStatus;

    public $label;

    public $color;

    public $animations;

    public static function getAnimationStatusList(): array
    {
        $animationStatus = AnimationStatusOrm::findAll();
        return self::toArray($animationStatus);
    }

    public static function delete($id)
    {
        if ($id != 1) {
            $animationStatus = AnimationStatusOrm::findById($id);
            if (empty(AnimationOrm::find('num_status', $id))) {
                $animationStatus->delete();
                return true;
            }
        }
        return false;
    }

    public static function save(object $data)
    {
        if (! empty($data->id)) {
            $status = new AnimationStatusOrm($data->id);
        } else {
            $status = new AnimationStatusOrm();
        }
        if (! empty($data->label)) {
            $result = AnimationStatusOrm::find('label', $data->label);
            if ((count($result) == 1 && $status->{AnimationStatusOrm::$idTableName} === $result[0]->{AnimationStatusOrm::$idTableName}) || empty($result)) {
                $status->label = $data->label;
                $status->color = $data->color ?? '';
                $status->save();
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
        $query = "select 1 from anim_animations where num_status = " . $this->id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result) > 0) {
            return true;
        }
        return false;
    }

    public static function checkExistStatus($label)
    {
        if (! empty(AnimationStatusOrm::find('label', $label))) {
            return true;
        }
        return false;
    }
}