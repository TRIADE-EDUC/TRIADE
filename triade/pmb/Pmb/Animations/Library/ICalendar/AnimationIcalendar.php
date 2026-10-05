<?php

namespace Pmb\Animations\Library\ICalendar;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Animations\Models\AnimationModel;
use Pmb\Animations\Models\RegistrationModel;
use Pmb\Common\Library\ICalendar\ICalendar;
use Pmb\Common\Library\ICalendar\ICalendarException;

class AnimationIcalendar extends ICalendar
{

    /**
     * Create a new iCalendar object
     *
     * @param AnimationModel $animation
     * @return AnimationIcalendar
     */
    public static function getInstance(AnimationModel $animation)
    {
        $iCalendar = new AnimationIcalendar($animation->name, $animation->description);
        $iCalendar->uid = 'animation-' . $animation->id;

        $event = $animation->fetchEvent();
        $iCalendar->setEvent(
            new \DateTime($event->startDate),
            new \DateTime($event->duringDay ? $event->startDate : $event->endDate),
        );

        $locations = $animation->fetchLocation(true);
        if (! empty($locations)) {
            $locations_libelle = [];
            foreach ($locations as $location) {
                $locations_libelle[] = $location['locationLibelle'];
            }
            $iCalendar->setLocation(implode(', ', $locations_libelle));
        }

        return $iCalendar;
    }

    /**
     * Set the registration
     *
     * @param RegistrationModel $registration
     * @return void
     */
    public function setRegistration(RegistrationModel $registration, bool $isCancelled = false)
    {
        $this->uid .= '-' . $registration->id;

        if ($isCancelled) {
            $this->setStatus(ICalendar::STATUS_CANCELLED);
        } else {
            switch ($registration->numRegistrationStatus) {
                case RegistrationModel::VALIDATED:
                    $this->setStatus(ICalendar::STATUS_CONFIRMED);
                    break;

                case RegistrationModel::PENDING_VALIDATION:
                case RegistrationModel::WAITING_LIST:
                    $this->setStatus(ICalendar::STATUS_TENTATIVE);
                    break;

                default:
                    throw new ICalendarException("Unknown registration status : " . $registration->numRegistrationStatus);
            }
        }
    }

}
