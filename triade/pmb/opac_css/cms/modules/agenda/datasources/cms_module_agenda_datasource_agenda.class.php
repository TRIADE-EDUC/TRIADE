<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_datasource_agenda.class.php,v 1.28.2.3.2.1 2025/01/30 11:42:35 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_agenda_datasource_agenda extends cms_module_common_datasource_list
{

    /**
     * Trier par defaut qui utilise la fonction sort_event
     *
     * @var string
     */
    public const DEFAULT_SORT = "event_date";

    /**
     * Active les tris
     *
     * @var boolean
     */
    protected $sortable = true;

    /**
     * Active la possibilite de limiter le nombre d'articles
     *
     * @var boolean
     */
    protected $limitable = true;

    public function __construct($id = 0)
    {
        parent::__construct($id);
        if (empty($this->parameters["sort_by"])) {
            $this->parameters["sort_by"] = static::DEFAULT_SORT;
        }
    }

    /**
     * On défini les critères de tri utilisable pour cette source de donnée
     *
     * @return array
     */
    protected function get_sort_criterias()
    {
        return array(
            static::DEFAULT_SORT,
            "publication_date",
            "id_article",
            "article_title",
            "article_order",
            "rand()"
        );
    }

    /**
     * On défini les sélecteurs utilisable pour cette source de donnée
     *
     * @return array
     */
    public function get_available_selectors()
    {
        return array(
            "cms_module_common_selector_env_var",
            "cms_module_agenda_selector_calendars_date",
            "cms_module_agenda_selector_calendars"
        );
    }

    /**
     * Récupération des données de la source...
     *
     * @return array
     */
    public function get_datas()
    {
        $selector = $this->get_selected_selector();
        if (!$selector) {
            return array('events'=> []);
        }

        $links = ["article" => $this->get_constructed_link("article", "!!id!!")];
        switch($this->parameters['selector']) {
            case "cms_module_agenda_selector_calendars":
                $events = $this->fetchEventByCalendarSelector($selector, $links);
                break;
            case "cms_module_common_selector_env_var":
                $events = $this->fetchEventByEnvVarSelector($selector, $links);
                break;
            case "cms_module_agenda_selector_calendars_date":
                $events = $this->fetchEventByCalendarsDateSelector($selector, $links);
                break;
            default:
                $events = [];
                break;
        }

        if (!empty($this->parameters["sort_by"]) && $this->parameters["sort_by"] == static::DEFAULT_SORT) {
            usort($events, array($this, "sort_event"));
        }

        $nbMaxElements = intval($this->parameters["nb_max_elements"]) ?? 0;
        if ($nbMaxElements && $nbMaxElements > 0) {
            $events = array_slice($events, 0, $nbMaxElements);
        }

        return array('events'=> $events);
    }

    /**
     * Fonction de tri par date
     *
     * @param cms_editorial_data $a
     * @param cms_editorial_data $b
     * @return int
     */
    public function sort_event($a, $b)
    {
        $start_time_a = isset($a->event_start) ? $a->event_start['time'] : 0;
        $start_time_b = isset($b->event_start) ? $b->event_start['time'] : 0;

        $end_time_a = isset($a->event_end) ? $a->event_end['time'] : 0;
        $end_time_b = isset($b->event_end) ? $b->event_end['time'] : 0;

        $positive = $this->parameters["sort_order"] == "desc" ? -1 : 1;
        $negative = $this->parameters["sort_order"] == "desc" ? 1 : -1;

        if ($start_time_a > $start_time_b) {
            return $positive;
        } elseif ($start_time_a == $start_time_b) {
            if ($end_time_a > $end_time_b) {
                return $positive;
            } else {
                return $negative;
            }
        } else {
            return $negative;
        }
    }

    public function get_format_data_structure($type='event')
    {
        $format_datas = array();
        switch($type) {
            //event
            case "event":
                $format_datas = cms_article::get_format_data_structure("article");
                $format_datas[] = array(
                    'var' => "event_start",
                    'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_start_desc'],
                    'children' => array(
                        array(
                            'var' => "event_start.format_value",
                            'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_start_format_value_desc'],
                        ),
                        array(
                            'var' => "event_start.value",
                            'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_start_value_desc'],
                        ),
                        array(
                            'var' => "event_start.time",
                            'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_start_time_desc'],
                        )
                    )
                );
                $format_datas[] = array(
                    'var' => "event_end",
                    'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_end_desc'],
                    'children' => array(
                        array(
                            'var' => "event_end.format_value",
                            'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_end_format_value_desc'],
                        ),
                        array(
                            'var' => "event_end.value",
                            'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_end_value_desc'],
                        ),
                        array(
                            'var' => "event_end.time",
                            'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_end_time_desc'],
                        )
                    )
                );
                $format_datas[] = array(
                    'var' => "id_type",
                    'desc' => $this->msg['cms_module_agenda_datasource_agenda_id_type_desc']
                );
                $format_datas[] = array(
                    'var' => "color",
                    'desc' => $this->msg['cms_module_agenda_datasource_agenda_color_desc']
                );
                $format_datas[] = array(
                    'var' => "calendar",
                    'desc' => $this->msg['cms_module_agenda_datasource_agenda_calendar_desc']
                );
                break;
            case "eventslist":
                $format_event = $this->get_format_data_structure("event");
                $format_datas[] = array(
                    'var' => "events",
                    'desc'=> $this->msg['cms_module_agenda_datasource_agenda_events_desc'],
                    'children' => $this->prefix_var_tree($format_event, "events[i]")
                );
                break;
        }
        return $format_datas;
    }

    /**
     * Recuperation des infos du calendrier associe a cet evenement
     *
     * @return array|false
     */
    protected function fetchManagedModuleBox()
    {
        $query = "SELECT managed_module_box FROM cms_managed_modules
            JOIN cms_cadres ON id_cadre = '".$this->cadre_parent."' AND cadre_object = managed_module_name";
        $result = pmb_mysql_query($query);

        if (pmb_mysql_num_rows($result)) {
            $box = pmb_mysql_result($result, 0, 0);
            return unserialize($box);
        }
        return false;
    }

    /**
     * Récupération des articles correspondant à un type
     * puis on les tris et on filtre les données
     *
     * @param integer $numType
     * @return array
     */
    protected function fetchArticleByNumType(int $numType)
    {
        $articles = array();

        $query = "SELECT id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date FROM cms_articles WHERE article_num_type = '".$numType."'";
        if (!empty($this->parameters["sort_by"]) && $this->parameters["sort_by"] != static::DEFAULT_SORT) {
            $query .= " ORDER BY " . addslashes($this->parameters["sort_by"]);
            if ($this->parameters["sort_order"] != "") {
                $query .= " " . addslashes($this->parameters["sort_order"]);
            }
        }

        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $articles[] = $row->id_article;
            }

            $articles = $this->filter_datas("articles", $articles);
        }

        return $articles;
    }

    protected function fetchEventByCalendarSelector($selector, array $links)
    {
        $today = time();
        $dateTime = date('Y-m-d', $today);

        $calendars = array();
        $old_events = array();
        $current_events = array();
        $futur_events = array();

        $events = array();
        $infos = $this->fetchManagedModuleBox();

        if ($infos) {
            //On test s'il s'agit du nouveau format de calendrier comportant les paramètres old_event ou futur_event
            $module_parameters = $selector->get_value();

            $old_event = false;
            $futur_event = false;

            // Traite le cas pour pour les cadres mal enregistrer sur les portails
            if (!isset($module_parameters['old_event'])) {
                $old_event = false;
                $futur_event = true;
            }

            if (!empty($module_parameters['old_event'])) {
                $old_event = true;
            }
            if (!empty($module_parameters['futur_event'])) {
                $futur_event = true;
            }

            $calendars = isset($module_parameters['calendars']) ? $module_parameters['calendars'] : $module_parameters;

            foreach ($calendars as $calendar) {
                if (!isset($infos['module']['calendars'][$calendar])) {
                    continue;
                }

                $elem = $infos['module']['calendars'][$calendar];
                $articles = $this->fetchArticleByNumType(intval($elem['type']));
                foreach ($articles as $article) {
                    $event = new cms_editorial_data($article, "article", $links);

                    foreach ($event->fields_type as $field) {
                        if ($field['id'] == $elem['start_date']) {
                            $event->event_start = $field['values'][0];
                            $event->event_start['time'] = mktime(
                                0,
                                0,
                                0,
                                intval(substr($field['values'][0]['value'], 5, 2)),
                                intval(substr($field['values'][0]['value'], 8, 2)),
                                intval(substr($field['values'][0]['value'], 0, 4))
                            );
                        }
                        if ($field['id'] == $elem['end_date']) {
                            $event->event_end = $field['values'][0];
                            $event->event_end['time'] = mktime(
                                0,
                                0,
                                0,
                                intval(substr($field['values'][0]['value'], 5, 2)),
                                intval(substr($field['values'][0]['value'], 8, 2)),
                                intval(substr($field['values'][0]['value'], 0, 4))
                            );
                        }
                    }

                    $event->id_type = $elem['type'];
                    $event->color = $elem['color'];
                    $event->calendar = $elem['name'];

                    //Evenement sur une période
                    if (!empty($event->event_start) && !empty($event->event_end)) {
                        if ($event->event_start['value']<= $dateTime && $event->event_end['value'] >= $dateTime) {
                            $current_events[] = $event;
                        } elseif ($old_event && $event->event_start['value'] < $dateTime && $event->event_end['value'] < $dateTime) {
                            $old_events[] = $event;
                        } elseif ($futur_event && $event->event_start['value'] > $dateTime && $event->event_end['value'] > $dateTime) {
                            $futur_events[] = $event;
                        }
                    //Evenement ponctuel
                    } elseif (!empty($event->event_start)) {
                        if ($event->event_start['value'] == $dateTime) {
                            $current_events[] = $event;
                        } elseif ($old_event && $event->event_start['value'] < $dateTime) {
                            $old_events[] = $event;
                        } elseif ($futur_event && $event->event_start['value'] > $dateTime) {
                            $futur_events[] = $event;
                        }
                    }

                    $event = null;
                }
            }
        }

        //On conditionne l'ajout des évènements en fonction des paramêtres
        if ($old_event && !empty($old_events)) {
            $events = array_merge($events, $old_events);
        }

        if (!empty($current_events)) {
            $events = array_merge($events, $current_events);
        }

        if ($futur_event && !empty($futur_events)) {
            $events = array_merge($events, $futur_events);
        }
        return $events;
    }

    protected function fetchEventByEnvVarSelector($selector, array $links)
    {
        $event = new cms_editorial_data($selector->get_value(), "article", $links);

        //allons chercher les infos du calendrier associé à cet évènement
        $infos = $this->fetchManagedModuleBox();
        if ($infos) {
            foreach ($infos['module']['calendars'] as $calendar) {
                if ($calendar['type'] == $event->num_type) {
                    foreach ($event->fields_type as $field) {
                        if ($field['id'] == $calendar['start_date']) {
                            $event->event_start = $field['values'][0];
                            $event->event_start['time'] = mktime(
                                0,
                                0,
                                0,
                                intval(substr($field['values'][0]['value'], 5, 2)),
                                intval(substr($field['values'][0]['value'], 8, 2)),
                                intval(substr($field['values'][0]['value'], 0, 4))
                            );
                        }

                        if ($field['id'] == $calendar['end_date']) {
                            $event->event_end = $field['values'][0];
                            $event->event_end['time'] = mktime(
                                0,
                                0,
                                0,
                                intval(substr($field['values'][0]['value'], 5, 2)),
                                intval(substr($field['values'][0]['value'], 8, 2)),
                                intval(substr($field['values'][0]['value'], 0, 4))
                            );
                        }
                    }

                    $event->id_type = $calendar['type'];
                    $event->color = $calendar['color'];
                    $event->calendar = $calendar['name'];
                    break;
                }
            }
        }

        return [$event];
    }


    protected function fetchEventByCalendarsDateSelector($selector, array $links)
    {
        $today = time();
        $events = array();
        $old_events = array();
        $current_events = array();
        $futur_events = array();

        $infos = $this->fetchManagedModuleBox();
        if ($infos) {
            //On test s'il s'agit du nouveau format de calendrier comportant les paramètres old_event ou futur_event
            $module_parameters = $selector->get_value();

            $old_event = false;
            $futur_event = false;

            $params = isset($module_parameters['calendars']) ? $module_parameters['calendars'] : $module_parameters;

            // Traite le cas pour pour les cadres enregistrer sur le portail avec l'ancien comportement
            if (!isset($module_parameters['calendars']['old_event'])) {
                $old_event = false;
                $futur_event = true;
            }

            if (!empty($params['old_event'])) {
                $old_event = true;
            }

            if (!empty($params['futur_event'])) {
                $futur_event = true;
            }

            $datas = $module_parameters;
            if (isset($module_parameters['calendars']['old_event'])) {
                $datas = $module_parameters['calendars'];
            }

            $datas['date'] = isset($module_parameters['calendars']) ? $module_parameters['date'] : "";
            $time = $today;

            $selected_date = false;

            if (!empty($datas['date']) && preg_match("/^[0-9]{4}.[0-9]{2}.[0-9]{2}$/", $datas['date'])) {
                // Le format de la date attendu est AAAA-MM-JJ ou AAAA/MM/JJ etc...
                $time = mktime(
                    0,
                    0,
                    0,
                    intval(substr($datas['date'], 5, 2)),
                    intval(substr($datas['date'], 8, 2)),
                    intval(substr($datas['date'], 0, 4))
                );
                $selected_date = true;
            }

            $dateTime = date('Y-m-d', $time);
            foreach ($datas['calendars'] as $calendar) {
                $elem = $infos['module']['calendars'][$calendar];

                $articles = $this->fetchArticleByNumType(intval($elem['type']));
                foreach ($articles as $article) {
                    $event = new cms_editorial_data($article, "article", $links);

                    foreach ($event->fields_type as $field) {
                        if ($field['id'] == $elem['start_date']) {
                            $event->event_start = $field['values'][0];
                            $event->event_start['time'] = mktime(
                                0,
                                0,
                                0,
                                intval(substr($field['values'][0]['value'], 5, 2)),
                                intval(substr($field['values'][0]['value'], 8, 2)),
                                intval(substr($field['values'][0]['value'], 0, 4))
                            );
                        }

                        if ($field['id'] == $elem['end_date']) {
                            $event->event_end = $field['values'][0];
                            $event->event_end['time'] = mktime(
                                0,
                                0,
                                0,
                                intval(substr($field['values'][0]['value'], 5, 2)),
                                intval(substr($field['values'][0]['value'], 8, 2)),
                                intval(substr($field['values'][0]['value'], 0, 4))
                            );
                        }
                    }

                    $event->id_type = $elem['type'];
                    $event->color = $elem['color'];
                    $event->calendar = $elem['name'];

                    //Evenement sur une période
                    if (!empty($event->event_start) && !empty($event->event_end)) {
                        if ($event->event_start['value'] <= $dateTime && $event->event_end['value'] >= $dateTime) {
                            $current_events[] = $event;
                        } elseif ($old_event && $event->event_start['value'] < $dateTime && $event->event_end['value'] < $dateTime) {
                            $old_events[] = $event;
                        } elseif ($futur_event && $event->event_start['value'] > $dateTime && $event->event_end['value'] > $dateTime) {
                            $futur_events[] = $event;
                        }
                    //Evenement ponctuel
                    } elseif (!empty($event->event_start)) {
                        if ($event->event_start['value'] == $dateTime) {
                            $current_events[] = $event;
                        } elseif ($old_event && $event->event_start['value'] < $dateTime) {
                            $old_events[] = $event;
                        } elseif ($futur_event && $event->event_start['value'] > $dateTime) {
                            $futur_events[] = $event;
                        }
                    }
                }
            }
        }
        //On modifie l'état du flag old_event si une date est passée en paramètres Get
        if ($selected_date) {
            $old_event = false;
        }
        //On conditionne l'ajout des évènements en fonction des paramêtres
        if ($old_event && !empty($old_events)) {
            $events = array_merge($events, $old_events);
        }
        if (!empty($current_events)) {
            $events = array_merge($events, $current_events);
        }
        if ($futur_event && !empty($futur_events)) {
            $events = array_merge($events, $futur_events);
        }
        return $events;
    }
}
