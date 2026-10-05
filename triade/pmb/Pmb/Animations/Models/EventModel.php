<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EventModel.php,v 1.19 2023/05/03 13:32:27 gneveu Exp $
namespace Pmb\Animations\Models;

use Pmb\Common\Models\Model;
use Pmb\Animations\Orm\EventOrm;
use Pmb\Common\Helper\DateHelper;

class EventModel extends Model
{

    protected $ormName = "\Pmb\Animations\Orm\EventOrm";

    public $idEvent;

    public $startDate;

    public $endDate;

    public $numConfig;

    public $animations;

    public $duringDay;

    public $startHour;

    public $endHour;

    public static function getEvents()
    {
        $events = EventOrm::findAll();
        return self::toArray($events);
    }

    public static function getEvent(int $id)
    {
        $event = new EventOrm($id);
        return $event->toArray();
    }

    public static function deleteEvent(int $id)
    {
        $event = new EventOrm($id);
        $event->delete();
    }

    public static function addEvent(object $data)
    {
        $event = new EventOrm();
        if (empty($data->event->startDate) || (empty($data->event->endDate) && empty($data->event->duringDay))) {
            return false;
        }
        if (empty($data->event->startHour) || empty($data->event->endHour)) {
            $data->event->startHour = "00:00";
            $data->event->endHour = "00:00";
        }

        $endDate = null;
        if (empty($data->event->duringDay)) {
            $endDate = $data->event->endDate . " " . $data->event->endHour;
        }

        // TODO : Gérer un DateTime Helper
        $event->start_date = $data->event->startDate . " " . $data->event->startHour;
        $event->end_date = $endDate;

        if (! empty($data->num_config)) {
            $event->num_config = $data->num_config;
        }

        if (! empty($data->event->duringDay)) {
            $event->during_day = $data->event->duringDay;
        }
        $event->save();
        return $event->id_event;
    }

    public static function updateEvent(int $id, object $data)
    {
        $event = new EventOrm($id);

        if (! empty($data->event->startDate)) {
            $event->start_date = $data->event->startDate . " " . $data->event->startHour;
        }

        $event->end_date = null;
        if (empty($data->event->duringDay) && ! empty($data->event->endDate)) {
            $event->end_date = $data->event->endDate . " " . $data->event->endHour;
        }

        if (! empty($data->num_config)) {
            $event->num_config = $data->num_config;
        }

        $event->during_day = $data->event->duringDay;

        $event->save();
        return $event->id_event;
    }

    public static function getRepeatEventId($event, $year, $month, $day)
    {
        $eventORM = new EventOrm();

        // On utilise des date JS, les mois ne sont pas pareil surtout les mois
        // On fait un petit +1 car janvier correspond a 0 et decembre a 11
        $month += 1;

        $startDate = new \DateTime();
        $startDate->setDate($year, $month, $day);

        $duringDay = ! empty($event->DuringDay) ? $event->DuringDay : false;
        $eventORM->during_day = $duringDay;

        $eventORM->start_date = $startDate->format("Y-m-d") . " " . ($event->startHour ?? "00:00");

        if ($event->nbDayAnimation && 0 < $event->nbDayAnimation && ! $duringDay) {
            // On soustrait 1 exemple :
            // nbDayAnimation = 2, startDate = 18, endDate = 20... 18-19-20 cela fait bien 3 jours et non deux...
            $day += $event->nbDayAnimation - 1;
            $startDate->setDate($year, $month, $day);
            $eventORM->end_date = $startDate->format("Y-m-d") . " " . ($event->endHour ?? "00:00");
        } elseif (! $duringDay) {
            $eventORM->end_date = $startDate->format("Y-m-d") . " " . ($event->endHour ?? "00:00");
        }

        $eventORM->save();
        return $eventORM->id_event;
    }
}