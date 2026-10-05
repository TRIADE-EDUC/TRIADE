<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationCalendarModel.php,v 1.3 2023/05/03 13:15:26 gneveu Exp $
namespace Pmb\Animations\Models;

use Pmb\Common\Models\Model;
use Pmb\Animations\Orm\AnimationCalendarOrm;

class AnimationCalendarModel extends Model
{

    protected $ormName = "\Pmb\Animations\Orm\AnimationCalendarOrm";

    public $idCalendar;

    public $name;

    public $color;

    public $calendar;

    public static function getAnimationCalendarList()
    {
        $animationCalendar = AnimationCalendarOrm::findAll();
        return self::toArray($animationCalendar);
    }

    public function getEditAddData()
    {
        return $this;
    }

    public static function save(object $data)
    {
        if (! empty($data->id)) {
            $calendar = new AnimationCalendarOrm($data->id);
        } else {
            $calendar = new AnimationCalendarOrm();
        }
        if (! empty($data->name) && ! empty($data->color)) {
            $result = AnimationCalendarOrm::find('id', $data->id);
            if ((count($result) == 1 && $calendar->{AnimationCalendarOrm::$idTableName} === $result[0]->{AnimationCalendarOrm::$idTableName}) || empty($result)) {
                $calendar->name = $data->name;
                $calendar->color = $data->color;
                $calendar->save();
            }
        }
    }

    public static function checkExistCalendar($name)
    {
        if (! empty(AnimationCalendarOrm::find('name', $name))) {
            return true;
        }
        return false;
    }

    public static function delete($id)
    {
        if ($id != 1) {
            $calendarORM = AnimationCalendarOrm::findById($id);
            if (isset($calendarORM)) {
                $calendarORM->delete();
                return true;
            }
        }
        return false;
    }
}