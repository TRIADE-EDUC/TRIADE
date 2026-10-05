<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PeriodicalEvent.php,v 1.5.4.1 2025/05/26 12:13:32 rtigero Exp $

namespace Pmb\DSI\Models\Event\Periodical;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\Event\RootEvent;
use Pmb\DSI\Models\Product;
use Pmb\DSI\Orm\EventDiffusionOrm;
use Pmb\DSI\Orm\EventProductOrm;

class PeriodicalEvent extends RootEvent
{
    const DAYSOFWEEK = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function trigger()
    {
        switch ($this->callerType) {
            case "diffusion":
                $eventDiffusionOrm = EventDiffusionOrm::find("num_event", $this->id)[0] ?? null;
                if (!empty($eventDiffusionOrm)) {
                    $diffusion = new Diffusion($eventDiffusionOrm->num_diffusion);
                    $lastHistory = $diffusion->getLastHistorySent();

                    if ($lastHistory) {
                        $date = new \DateTime($lastHistory->date);
                        $now = new \DateTime("now");

                        // On regarde si la diffusion a deja ete envoye aujourd'hui
                        if ($now->format("Y-m-d") === $date->format("Y-m-d")) {
                            return false;
                        }
                    }
                }
                break;
            case "product":
                $eventProductOrm = EventProductOrm::find("num_event", $this->id)[0] ?? null;
                if (!empty($eventProductOrm)) {
                    $product = new Product($eventProductOrm->num_product);
                    $lastSend = $product->getLastDiffusion();

                    if ($lastSend) {
                        $date = \DateTime::createFromFormat(GlobalContext::msg('dsi_format_date'), $lastSend);
                        if($date !== false) {
                            $now = new \DateTime("now");

                            // On regarde si la diffusion a deja ete envoye aujourd'hui
                            if ($now->format("Y-m-d") === $date->format("Y-m-d")) {
                                //return false;
                            }
                        }
                    }
                }
                break;
            default:
                break;
        }


        switch ($this->getSetting("periodical")) {
            case "daily":
                $succes = $this->dailyTrigger();
                break;

            case "weekly":
                $succes = $this->weeklyTrigger();
                break;

            case "monthly":
                $succes = $this->monthlyTrigger();
                break;

            // case "yearly":
            //     $succes = $this->yearlyTrigger();
            //     break;

            default:
                $succes = false;
                break;
        }

        return $succes;
    }

    /**
     * Periodicite journaliere
     *
     * @return boolean
     */
    protected function dailyTrigger()
    {
        $data = $this->getSetting("periodical_data", "");
        $nbDays = intval($data->nbDays);

        $start = $this->getSetting("periodical_start", null);
        $end = $this->getSetting("periodical_end", null);

        $now = new \DateTime("now");
        $now->setTime(0, 0);

        if (!$start instanceof \DateTime) {
            $start = new \DateTime($start);
        }

        if (!empty($end) && !$end instanceof \DateTime) {
            $end = new \DateTime($end);
        }

        // Verifier si la date de fin est passee, dans ce cas, pas besoin de continuer
        if (!empty($end) && $end < $now) {
            return false;
        }

        // Verifie si la date du jour est dans les dates customs
        if ($this->checkCustomAddedDates()) {
            return true;
        }

        if ($this->checkCustomRemovedDates()) {
            return false;
        }

        // Calculer le nombre de jours entre la date de debut et la date actuelle
        $interval = $start->diff($now);
        $daysBetween = $interval->days;

        // Si le nombre de jours est un multiple de $nbDays
        if ($daysBetween % $nbDays === 0) {
            return $this->checkTime();
        }

        return false;
    }

    /**
     * Periodicite hebdomadaire
     *
     * @return boolean
     */
    protected function weeklyTrigger()
    {
        $data = $this->getSetting("periodical_data", "");

        $nbWeeks = intval($data->nbWeeks);
        $dayList = array_map('intval', $data->dayList);

        $start = $this->getSetting("periodical_start", null);
        $end = $this->getSetting("periodical_end", null);

        $now = new \DateTime("now");
        $now->setTime(0, 0);

        if (!$start instanceof \DateTime) {
            $start = new \DateTime($start);
        }

        if (!empty($end) && !$end instanceof \DateTime) {
            $end = new \DateTime($end);
        }

        // Verifier si la date de fin est passee, dans ce cas, pas besoin de continuer
        if (!empty($end) && $end < $now) {
            return false;
        }

        // Verifie si la date du jour est dans les dates customs
        if ($this->checkCustomAddedDates()) {
            return true;
        }

        if ($this->checkCustomRemovedDates()) {
            return false;
        }

        // Calculer le nombre de semaines entre la date de debut et la date actuelle
        $interval = $start->diff($now);

        // Arrondir au nombre entier de semaines
        $weeksBetween = round($interval->days / 7);

        // Si le nombre de semaines est un multiple de $nbWeeks
        if ($weeksBetween % $nbWeeks === 0) {
            $dayIndex = intval($now->format('w'));

            // Si on est sur le bon jour de la semaine
            if (in_array($dayIndex, $dayList, true)) {
                return $this->checkTime();
            }
        }

        return false;
    }

    /**
     * Periodicite mensuelle
     *
     * @return boolean
     */
    protected function monthlyTrigger()
    {
        $data = $this->getSetting("periodical_data", "");

        $nbMonth = intval($data->nbMonth);
        $dayList = array_map('intval', $data->dayList);
        $frequency = intval($data->repeatDay->frequency);
        $day = intval($data->repeatDay->day);
        $monthlyCalendarSelected = $data->monthlyCalendarSelected === "no" ? false : true;

        $start = $this->getSetting("periodical_start", null);
        $end = $this->getSetting("periodical_end", null);

        $now = new \DateTime("now");
        $now->setTime(0, 0);

        if (!$start instanceof \DateTime) {
            $start = new \DateTime($start);
        }

        if (!empty($end) && !$end instanceof \DateTime) {
            $end = new \DateTime($end);
        }

        // Verifier si la date de fin est passee, dans ce cas, pas besoin de continuer
        if (!empty($end) && $end < $now) {
            return false;
        }

        // Verifie si la date du jour est dans les dates customs
        if ($this->checkCustomAddedDates()) {
            return true;
        }

        if ($this->checkCustomRemovedDates()) {
            return false;
        }

        // Calculer le nombre de mois entre la date de debut et la date actuelle
        $interval = $start->diff($now);
        $monthsBetween = $interval->y * 12 + $interval->m;

        // Si le nombre de mois est un multiple de $nbMonths, on est sur le bon mois
        if ($monthsBetween % $nbMonth === 0) {
            // On est sur le choix par jour de la semaine
            if (!$monthlyCalendarSelected) {
                // Jour de la semaine actuel (1 pour lundi, 2 pour mardi, etc.)
                $dayIndexOfWeek = intval($now->format('N'));
                // Verifier si le jour de la semaine correspond au jour choisi
                if ($dayIndexOfWeek === $day) {
                    // On est sur la frequence "chaque" jour du mois
                    if ($frequency === 10) {
                        return $this->checkTime();
                    }

                    // Extraire le jour du mois actuel
                    $dayOfMonth = intval($now->format('j'));

                    // On est sur la frequence "dernier" jour du mois
                    if ($frequency === 11) {
                        $labelDay = self::DAYSOFWEEK[$dayIndexOfWeek];

                        $year = intval($now->format('Y'));
                        $month = intval($now->format('m'));

                        // Utilisez la fonction strtotime pour obtenir le numero du dernier jour du mois
                        $lastDay = intval(date("j", strtotime("last $labelDay of $year-$month")));

                        return $lastDay === $dayOfMonth;
                    }

                    // Calcul pour savoir si le jour est bien le premier, second, etc...
                    $dayNumber = intval(floor(($dayOfMonth - 1) / 7)) + 1;

                    if ($dayNumber === $frequency) {
                        return $this->checkTime();
                    }
                }
            } else {
                // Obtient le numero de jour actuel
                $numDay = intval($now->format('d'));

                if (in_array($numDay, $dayList, true)) {
                    return $this->checkTime();
                }

                // On est sur le dernier jour du mois
                if (in_array(0, $dayList, true)) {
                    $nbDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $now->format('m'), $now->format('Y'));

                    // Verifiez si le jour est le dernier jour du mois
                    if ($nbDaysInMonth === $numDay) {
                        return $this->checkTime();
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check si l'heure du formulaire est passee ou egal a l'heure actuelle
     *
     * @return boolean
     */
    private function checkTime(): bool
    {
        $timeString = $this->getSetting("periodical_time", null);

        $time = \DateTime::createFromFormat("H:i", $timeString);
        $now = new \DateTime("now");

        if ($time <= $now) {
            return true;
        }

        return false;
    }

    /**
     * Check si ma date du jour est inclu dans les jours customs
     *
     * @return boolean
     */
    private function checkCustomAddedDates(): bool
    {
        $data = $this->getSetting("periodical_data", null);
        $addedDates = $data->custom_dates->added_dates;

        $now = new \DateTime("now");
        if (in_array($now->format("Y-m-d"), $addedDates, true)) {
            return true;
        }

        return false;
    }

    /**
     * Check si ma date du jour est inclu dans les jours customs
     *
     * @return boolean
     */
    private function checkCustomRemovedDates(): bool
    {
        $data = $this->getSetting("periodical_data", null);
        $removedDates = $data->custom_dates->removed_dates;

        $now = new \DateTime("now");
        if (in_array($now->format("Y-m-d"), $removedDates, true)) {
            return true;
        }

        return false;
    }
}
