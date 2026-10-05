<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DateHelper.php,v 1.4 2024/02/20 08:52:57 jparis Exp $

namespace Pmb\Common\Helper;

use DateInterval;

class DateHelper
{

    /**
     *
     * @param \DateTime $day
     * @return \DateTime
     */
    public static function firstDayOfMonth(\DateTime $day)
    {
        $tmp = clone $day;
        return $tmp->sub(new DateInterval("P" . ($day->format("j") - 1) . "D"));
    }

    /**
     *
     * @param \DateTime $day
     * @return \DateTime
     */
    public static function lastDayOfMonth(\DateTime $day)
    {
        $tmp = clone $day;
        return $tmp->add(new DateInterval("P" . ($day->format("t") - $day->format("j")) . "D"));
    }

    /**
     *
     * @param \DateTime $day
     * @return \DateTime
     */
    public static function lastMonday(\DateTime $day)
    {
        $tmp = clone $day;
        $nbDays = $day->format("w") - 1;
        if ($nbDays < 0) {
            $nbDays = 6;
        }
        return $tmp->sub(new DateInterval("P" . $nbDays . "D"));
    }

    /**
     * 
     * @param \DateTime $day
     * @return \DateTime
     */
    public static function nextMonday(\DateTime $day)
    {
        $tmp = clone $day;
        return $tmp->add(new DateInterval("P" . (7 - $day->format("w") + 1) . "D"));
    }

    /**
     *
     * @param \DateTime $day
     * @return \DateTime
     */
    public static function nextDay(\DateTime $day)
    {
        $tmp = clone $day;
        return $tmp->add(new DateInterval("P1D"));
    }

    /**
     *
     * @param \DateTime $day
     * @return \DateTime
     */
    public static function previousDay(\DateTime $day)
    {
        $tmp = clone $day;
        return $tmp->sub(new DateInterval("P1D"));
    }

    /**
     *
     * @param \DateTime $day
     * @return \DateTime
     */
    public static function nextMonth(\DateTime $day)
    {
        $tmp = clone $day;
        return $tmp->add(new DateInterval("P1M"));
    }

    /**
     *
     * @param \DateTime $day
     * @return \DateTime
     */
    public static function getWeekNumber(\DateTime $day)
    {
        $WeekNb = $day->format("W");
        return intval($WeekNb);
    }

    /**
     *
     * @param \DateTime $day
     * @return integer
     */
    public static function getNumberOfWeeks(\DateTime $start_day, \DateTime $end_day)
    {
        $first_week = DateHelper::getWeekNumber($start_day);
        $last_week = DateHelper::getWeekNumber($end_day);
        $nbWeeks = $last_week - $first_week;
        if ($nbWeeks <= 0) {
            $nbWeeksInPreviousYear = intval(date('W', mktime(0, 0, 0, 12, 28, $start_day->format('Y') - 1)));
            $nbWeeks = $nbWeeksInPreviousYear - $first_week + $last_week;
        }
        return $nbWeeks + 1;
    }
    
    /**
     * @param \DateTime $day
     * @return array
     */
    public static function getPrevNextYears()
    {
        $years = array();
        $date = new \DateTime();
        $date->sub(new \DateInterval("P2Y"));
        $year = $date->format('Y');
        
        for ($i = 0; $i < 10; $i++) {
            $years[] = $year + $i;
        }
        return $years;
    }


    /**
     * Retourne le nb de secondes entre deux dates
     *
     * @param \DateTime $start
     * @param \DateTime $end
     * @return number
     */
    public static function getDiffInSeconds(\DateTime $start, \DateTime $end)
    {
        return $end->format('U') - $start->format('U');
    }

    /**
     * Formate une date selon la langue de l'utilisateur.
     *
     * @param \DateTime $date La date à formater
     * @return string La date formatée
     */
    public static function formatDateByUserLang(\DateTime $date)
    {
        global $lang;

        $dateFormatter = new \IntlDateFormatter(str_replace("_", "-", $lang), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
        return $dateFormatter->format($date);
    }
}