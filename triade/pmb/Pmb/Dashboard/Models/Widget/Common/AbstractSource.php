<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AbstractSource.php,v 1.12 2024/02/26 14:28:54 dbellamy Exp $

namespace Pmb\Dashboard\Models\Widget\Common;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

abstract class AbstractSource
{
    protected static $configuration_filename = null;
    protected static $configuration = null;


    public function getConfiguration($full = false)
    {
        if (!is_null(static::$configuration)) {
            return static::$configuration['conditions'];
        }

        $configuration_file = static::$configuration_filename . ".xml";
        if (file_exists(static::$configuration_filename . "_subst.xml")) {
            $configuration_file = static::$configuration_filename . "_subst.xml";
        }

        $file = file_get_contents($configuration_file);
        // suppression commentaires et tags vides
        $file = preg_replace([ "/<!--.*?-->/s", "/<(?'pattern'[a-z|_]+)>\s*?<\/(?&pattern)>/is" ],"", $file);

        static::$configuration = json_decode(
            json_encode(simplexml_load_string($file, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT | LIBXML_NOBLANKS)),
            true
        );

        if ( isset(static::$configuration['conditions']) && count(static::$configuration['conditions']) ) {

            foreach (static::$configuration['conditions'] as $k => $param) {

                static::$configuration['conditions'][$k] = [
                    'name' => $param['name'],
                    'type' => $param['type'],
                    'datatype' => $param['datatype']
                ];


                if (!empty($param['multiple'])) {
                    static::$configuration['conditions'][$k]['multiple'] = $param['multiple'];
                }

                if (!empty($param['default'])) {
                    switch (true) {
                        case(!empty($param['default']['method'])):

                            $tmp = explode('::', $param['default']['method']);
                            $class = $tmp[0];
                            $method = $tmp[1];

                            $args = isset($param['default']['args']) ? [$param['default']['args']] : [];

                            if (class_exists($class) && method_exists($class, $method)) {
                                static::$configuration['conditions'][$k]['default'] = call_user_func_array([$class, $method], $args);
                            }
                            break;

                        case(!empty($param['default']['value'])):
                            static::$configuration['conditions'][$k]['default'] = $param['default']['value'];
                            break;
                    }
                }

                if (!empty($param['values'])) {
                    switch (true) {
                        case(!empty($param['values']['method'])):

                            $tmp = explode('::', $param['values']['method']);

                            $class = $tmp[0];
                            $method = $tmp[1];

                            $args = isset($param['values']['args']) ? [$param['values']['args']] : [];

                            if (class_exists($class) && method_exists($class, $method)) {
                                static::$configuration['conditions'][$k]['values'] = call_user_func_array([$class, $method], $args);
                            }
                            break;

                        case(!empty($param['values']['value'])):

                            static::$configuration['conditions'][$k] = $param['values']['value'];
                            break;
                    }
                }
            }
        } 

        if (true === $full) {
            return static::$configuration;
        }

        $configuration = static::$configuration;
        foreach ($configuration['methods'] as $k_method => $method) {
            if (isset($configuration['methods'][$k_method]['type'])) {
                unset($configuration['methods'][$k_method]['type']);
            }
            if (isset($configuration['methods'][$k_method]['query'])) {
                unset($configuration['methods'][$k_method]['query']);
            }
            if (isset($configuration['methods'][$k_method]['conditions'])) {
                $configuration['methods'][$k_method]['conditions'] = array_keys($configuration['methods'][$k_method]['conditions']);
            } else {
                $configuration['methods'][$k_method]['conditions'] = [];
            }
        }
        return $configuration;
    }



    public function getData($params = null)
    {
        $configuration = $this->getConfiguration(true);
        $formMethods = $params->methods;

        $data = [];

        $validMethods = [];
        $validConditions = [];

        foreach ($formMethods as $keyMethod => $formMethod) {

            // Si la methode n'est pas valide on l'ignore
            if (!isset($configuration["methods"][$formMethod->name])) {
                continue;
            }

            foreach ($formMethod->conditions as $keyCondition => $formCondition) {
                if (
                    !isset($configuration["conditions"][$keyCondition]) ||
                    !isset($configuration["methods"][$formMethod->name]["conditions"][$keyCondition]) ||
                    $this->isEmpty($formCondition)
                ) {
                    continue;
                }

                $validConditions[$keyMethod][$keyCondition] = $formCondition;
            }

            $validMethods[$keyMethod] = $formMethod;
        }

        foreach ($validMethods as $keyMethod => $validMethod) {
            $method = $configuration["methods"][$validMethod->name];

            switch ($method["type"]) {

                
                case "sql":
                    $query = $method["query"];

                    if (isset($validConditions[$keyMethod])) {

                        foreach ($validConditions[$keyMethod] as $keyCondition => $validCondition) {

                            $condition = $method["conditions"][$keyCondition];
                            $validCondition = addslashes_array($validCondition);

                            if (is_array($validCondition)) {
                                $validCondition = "'" . implode("','", $validCondition) . "'";
                            }

                            if ('period' == $keyCondition ) {

                                if( 'undefined' == $validCondition->periodSelector) {
                                    continue;
                                }
                                $dates = $this->calcPeriod($validCondition);
                                
                                $date_start = $dates[0] ?? '';
                                $date_end = $dates[1] ?? '';
                                
                                if($date_start) {
                                    $query.= str_replace (
                                        [
                                            '!!PERIOD_DATE_START!!',
                                            '!!PERIOD_DATETIME_START!!',
                                        ],
                                        [
                                            $date_start->format('Y-m-d'),
                                            $date_start->format('Y-m-d H:i:s'),
                                        ],
                                        $condition['date_start'],
                                    );
                                }
                                if($date_end) {
                                    $query.= str_replace (
                                        [
                                            '!!PERIOD_DATE_END!!',
                                            '!!PERIOD_DATETIME_END!!',
                                        ],
                                        [
                                            $date_end->format('Y-m-d'),
                                            $date_end->format('Y-m-d H:i:s'),
                                        ],
                                        $condition['date_end'],
                                    );
                                }
                            } else {
                                $pattern = "!!" . strtoupper($keyCondition) . "!!";
                                $query .= str_replace($pattern, $validCondition, $condition);
                            }
                        }
                    }
                    $r = pmb_mysql_query($query);
                    $n = pmb_mysql_num_rows($r);

                    if(!$n) {
                        break;
                    }
                    if ( 1 == $n) {
                        $data[$keyMethod] = pmb_mysql_result($r, 0, 0);
                        break;
                    }
                    $data[$keyMethod] = pmb_mysql_fetch_all($r, PMB_MYSQL_ASSOC);

                    break;


                case "method":

                    // Toutes les méthodes doivent être dans la classe qui gère la source
                    // TODO : Implémenter le système de paramètres dans les methodes
                    if (method_exists($this, $method["method"])) {
                        $data[$keyMethod] = call_user_func_array([$this, $method["method"]], []);
                    }

                    break;
            }
        }

        return $data;
    }

    private function isEmpty($value)
    {
        if (empty($value)) {
            // Si la valeur est vide, vérifier si elle est égale à 0, "0", true ou false
            if ($value === 0 || $value === "0" || $value === false) {
                return false;
            }
            return true;
        }
        return false;
    }

    protected function calcPeriod($validCondition)
    {
        $start_date = null;
        $end_date = null;

        switch ($validCondition->periodSelector) {
            default:
                $dates = $this->calcDates($validCondition->periodSelector);

                $start_date = $dates[0];
                $end_date = $dates[1];
                break;

            case 'dates':
                $start_date = new \DateTime($validCondition->datesSince);
                $end_date = new \DateTime($validCondition->datesTo);
                break;

            case 'since':
                if('aDate' == $validCondition->sinceSelector) {
                    $start_date = new \DateTime($validCondition->sinceStartDate);
                } else {
                    $start_date = $this->calcDates($validCondition->sinceSelector)[0];
                }

                if ($validCondition->sinceDuration && $validCondition->sinceUnit) {
                    $end_date = $this->calcDateWithDuration($start_date, $validCondition->sinceDuration, $validCondition->sinceUnit, 'since');
                }
                break;

            case 'to':
                if('aDate' == $validCondition->toSelector) {
                    $end_date = new \DateTime($validCondition->toEndDate);
                } else {
                    $end_date = $this->calcDates($validCondition->toSelector)[0];
                }

                if ($validCondition->toDuration && $validCondition->toUnit) {
                    $start_date = $this->calcDateWithDuration($end_date, $validCondition->toDuration, $validCondition->toUnit, 'to');
                    $end_date->sub(new \DateInterval('P1D'));
                }
                break;
        }

        if(!is_null($start_date)) {
            $start_date->setTime(0, 0, 0);
        }

        if(!is_null($end_date)) {
            $end_date->setTime(23, 59, 59);
        }

        return [$start_date, $end_date];
    }


    protected function calcDates($period = 'today')
    {
        $start_date = null;
        $end_date = null;

        switch ($period) {
            default:
            case 'today':
                $start_date = new \DateTime('today');
                $end_date = new \DateTime('today');
                break;

            case 'thisWeek':
                $start_date = new \DateTime('this week');
                $end_date = new \DateTime('this week +6 days');
                break;

            case 'lastWeek':
                $start_date = new \DateTime('last week');
                $end_date = new \DateTime('last week +6 days');
                break;

            case 'thisMonth':
                $start_date = new \DateTime('first day of this month');
                $end_date = new \DateTime('last day of this month');
                break;

            case 'lastMonth':
                $start_date = new \DateTime('first day of last month');
                $end_date = new \DateTime('last day of last month');
                break;

            case 'thisYear':
                $start_date = new \DateTime('first day of january');
                $end_date = new \DateTime('last day of december');
                break;

            case 'lastYear':
                $start_date = new \DateTime('first day of january');
                $start_date->sub(new \DateInterval('P1Y'));

                $end_date = new \DateTime('last day of december');
                $end_date->sub(new \DateInterval('P1Y'));
                break;
        }

        return [$start_date, $end_date];
    }

    protected function calcDateWithDuration(\DateTime $date, int $duration, string $unit, string $direction)
    {
        if(!in_array($unit,  ['days', 'weeks', 'months', 'years']) ) {
            return null;
        }
        
        $next_date = clone($date);
        $unit = strtoupper($unit[0]);
        $interval = new \DateInterval('P'. $duration . $unit);

        if('since' == $direction ) {
            $next_date->add($interval);
        } else {
            $next_date->sub($interval);
        }

        return $next_date;
    }
}