<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationModel.php,v 1.1.8.1 2025/04/23 09:50:36 qvarin Exp $

namespace Pmb\Animations\Opac\Models;

use Pmb\Animations\Library\ICalendar\AnimationIcalendar;
use Pmb\Animations\Models\AnimationModel as AnimationModelGestion;
use Pmb\Common\Models\CustomFieldModel;

class AnimationModel extends AnimationModelGestion
{
    public function fetchCustomFields()
    {
        if (! empty($this->customFields)) {
            return $this->customFields;
        }
        $this->customFields = CustomFieldModel::getAllCustomFields('anim_animation', $this->id, true);
        $this->gotCustomFieldsValues = false;
        foreach ($this->customFields as $field) {
            if (! empty($field['customField']['values'])) {
                $this->gotCustomFieldsValues = true;
            }
        }
    }

    /**
     * Exporte les animations au format iCal
     *
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @return void
     */
    public static function exportIcal($startDate = null, $endDate = null)
    {
        $animationIcalendars = [];
        $result = pmb_mysql_query(self::buildQueryExport($startDate, $endDate));
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $animation = new AnimationModel($row->id_animation);

                $animationIcalendar = AnimationIcalendar::getInstance($animation);
                $animationIcalendar->setStatus(AnimationIcalendar::STATUS_CONFIRMED);
                $animationIcalendars[] = $animationIcalendar;
            }
        }

        AnimationIcalendar::outputEvents($animationIcalendars, AnimationIcalendar::OUTPUT_DEST_I);
    }

    /**
     * Exporte les animations au format JSON
     *
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @return void
     */
    public static function exportJson($startDate = null, $endDate = null)
    {
        global $opac_url_base;

        $data = [];
        $result = pmb_mysql_query(self::buildQueryExport($startDate, $endDate));
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $animation = new AnimationModel($row->id_animation);

                $locationsData = [];
                $locations = $animation->fetchLocation(true);
                if (! empty($locations)) {
                    foreach ($locations as $location) {
                        $locationsData[] = [
                            'name' => $location['locationLibelle'],
                            'address' => $location['adr1'],
                            'additionnalAddress' => $location['adr2'],
                            'zipcode' => $location['cp'],
                            'town' => $location['town'],
                            'country' => $location['country'],
                            'contact' => [
                                'phone' => $location['phone'],
                                'email' => $location['email'],
                            ]
                        ];
                    }
                }

                $event = $animation->fetchEvent();
                $data[] = [
                    'title' => $animation->name,
                    'summary' => $animation->description,
                    'link' => $opac_url_base . 'index.php?lvl=animation_see&id=' . $animation->id,
                    'logo' => $opac_url_base . 'animations_vign.php?animationId=' . $animation->id,
                    'event' => [
                        'timezone' => date_default_timezone_get(),
                        'start' => (new \DateTime($event->startDate))->format('Ymd\THis'),
                        'end' => (new \DateTime($event->duringDay ? $event->startDate : $event->endDate))->format('Ymd\THis'),
                    ],
                    'locations' => $locationsData,
                ];
            }
        }

        header('Content-type: application/json');
        echo json_encode($data);
    }

    /**
     * Construit la requête d'export
     *
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @return string
     */
    protected static function buildQueryExport($startDate = null, $endDate = null)
    {
        $query = 'SELECT id_animation FROM anim_animations';
        if (isset($startDate) || isset($endDate)) {
            $query .= ' JOIN anim_events ON num_event = id_event WHERE';
            if (isset($startDate)) {
                $query .= ' start_date >= "' . $startDate->format('Y-m-d H:i:s') . '"';
            }
            if (isset($endDate)) {
                if (isset($startDate)) {
                    $query .= ' AND';
                }
                $query .= ' end_date <= "' . $endDate->format('Y-m-d H:i:s') . '"';
            }
        }

        return $query;
    }
}