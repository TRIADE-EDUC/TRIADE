<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visits_statistics.class.php,v 1.11.2.2 2024/12/17 09:02:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path.'/templates/visits_statistics.tpl.php');

/**
 * Classe de gestion des statistiques de fréquentation
 *
 */
class visits_statistics {

	/**
	 * Localisation
	 * @var int
	 */
	protected $location;

	/**
	 * Date ou date de début
	 * @var string
	 */
	protected $date;

	/**
	 * Date de fin
	 * @var string
	 */
	protected $enddate;

	/**
	 * Tableau des statistiques
	 * @var array
	 */
	protected $statistics;

	/**
	 * Configuration des statistiques
	 * @var array
	 */
	protected static $config = null;

	/**
	 * Type de compteurs definis
	 * @var array
	 */
	protected static $counter_types = null;


	/**
	 * Constructeur
	 * @param string $date Date unique ou date de début au format SQL
	 * @param string $enddate Date de fin au format SQL
	 */
	public function __construct($location = 0, $date = '', $enddate = '') {
		global $deflt2docs_location;
		$this->location = intval($location ? $location : $deflt2docs_location);
		$this->date = ($date ? $date : date('Y-m-d'));
		$this->enddate = $enddate;
	}

	public function get_form($module) {
		global $visits_statistics_form, $visits_statistics_main_line, $visits_statistics_service_line, $visits_statistics_shortcut_button;
		global $empr_visits_statistics_active;

		if (!$empr_visits_statistics_active) {
			return '';
		}

		self::get_config();

		$html = $visits_statistics_form;
		$html = str_replace('!!module!!', $module, $html);
		$html = str_replace('!!visits_statistics_date!!', formatdate($this->date), $html);

		$visits_statistics_shortcuts_buttons = '';

		$visits_statistics_main_lines = '';
		foreach (self::$config['main'] as $type => $main) {
			$line = $visits_statistics_main_line;
			if ($main['show_shortcut']) {
				$line = str_replace('!!visits_statistics_color!!', '<div style="width:7px; height:7px; display:inline-block; background-color:'.$main['color'].';"></div>', $line);
			} else {
				$line = str_replace('!!visits_statistics_color!!', '', $line);
			}
			$line = str_replace('!!visits_statistics_main_name!!', $main['name'], $line);
			$line = str_replace('!!visits_statistics_main_counter!!', $this->get_counter($type), $line);
			$visits_statistics_main_lines.= $line;
			$button = $visits_statistics_shortcut_button;
			$button = str_replace('!!counter_type!!', $type, $button);
			$button = str_replace('!!visits_statistics_color!!', $main['color'], $button);
			$button = str_replace('!!visits_statistics_title!!', $main['name'], $button);
			if ($main['show_shortcut']) {
				$visits_statistics_shortcuts_buttons.= $button;
			}
		}

		$visits_statistics_service_lines = '';
		$current_category = 0;
		$close_row = false;
		foreach (self::$config['services'] as $type => $service) {
			$line = $visits_statistics_service_line;
			if ($service['show_shortcut']) {
				$line = str_replace('!!visits_statistics_color!!', '<div style="width:7px; height:7px; display:inline-block; background-color:'.$service['color'].';"></div>', $line);
			} else {
				$line = str_replace('!!visits_statistics_color!!', '', $line);
			}
			$line = str_replace('!!visits_statistics_service_name!!', $service['name'], $line);
			$line = str_replace('!!visits_statistics_service_counter!!', $this->get_counter($type), $line);


			if ($service['category'] && ($service['category'] != $current_category)) {
				if ($close_row) {
					$visits_statistics_service_lines.= '<td colspan="2"></td></tr>';
				}
				$close_row = false;
				$visits_statistics_service_lines.= '<td class="visits_statistics_group" colspan="4"><b>'.self::$config['categories'][$service['category']]['name'].'</b></td>';
			}
			$current_category = $service['category'];
			if (!$close_row) {
				$visits_statistics_service_lines.= '<tr class="visits_statistics_row">';
			}
			$visits_statistics_service_lines.= $line;
			if ($close_row) {
				$visits_statistics_service_lines.= '</tr>';
				$close_row = false;
			} else {
				$close_row = true;
			}

			$button = $visits_statistics_shortcut_button;
			$button = str_replace('!!counter_type!!', $type, $button);
			$button = str_replace('!!visits_statistics_color!!', $service['color'], $button);
			$button = str_replace('!!visits_statistics_title!!', $service['name'], $button);
			if ($service['show_shortcut']) {
				$visits_statistics_shortcuts_buttons.= $button;
			}
		}
		if ($close_row) {
			$visits_statistics_service_lines.= '<td colspan="2"></td></tr>';
		}

		$html = str_replace('!!visits_statistics_main_lines!!', $visits_statistics_main_lines, $html);
		$html = str_replace('!!visits_statistics_service_lines!!', $visits_statistics_service_lines, $html);
		$html = str_replace('!!visits_statistics_shortcuts_buttons!!', $visits_statistics_shortcuts_buttons, $html);
		return $html;
	}

	protected function get_sql_clause() {
		$clause = ' visits_statistics_location = "'.$this->location.'"';
		if ($this->enddate) {
			return $clause.' and DATE(visits_statistics_date) <= "'.$this->date.'" and DATE(visits_statistics_date) >= "'.$this->enddate.'" ';
		}
		return $clause.' and DATE(visits_statistics_date) = "'.$this->date.'" ';
	}

	public function get_statistics() {
		if (isset($this->statistics)) {
			return $this->statistics;
		}
		$this->statistics = array();
		$query = 'select DATE(visits_statistics_date) as date, visits_statistics_type as type, count(*) as value from visits_statistics where '.$this->get_sql_clause().' GROUP BY DATE(visits_statistics_date), visits_statistics_type';
		$result = pmb_mysql_query($query);

		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if (!isset($this->statistics[$row->date])) {
					$this->statistics[$row->date] = array();
				}
				$this->statistics[$row->date][$row->type] = $row->value;
			}
		}

		return $this->statistics;
	}

	protected function get_counter($type) {
		global $visits_statistics_form_counter;

		$this->get_statistics();
		$count = 0;
		if (isset($this->statistics[$this->date][$type])) {
			$count = $this->statistics[$this->date][$type];
		}

		$html = $visits_statistics_form_counter;
		$html = str_replace('!!counter_type!!', $type, $html);
		$html = str_replace('!!count!!', $count, $html);

		return $html;
	}


    /**
     * Ajoute une visite au compteur du type specifie
     *
     * @param string $type = type de compteur
     * @return boolean
     */
    public function add_visit($type)
    {
        static::getCounterTypes();
        if (!in_array($type, static::$counter_types)) {
            return false;
        }

        $query = 'INSERT INTO visits_statistics (visits_statistics_date, visits_statistics_location, visits_statistics_type) VALUES (NOW(), "' . $this->location . '", "' . $type . '")';
        pmb_mysql_query($query);
    }

	/**
	 * Ajoute une quantité de visite au compteur du type specifie
	 *
	 * @param string $type = type de compteur
	 * @param integer $number = nombre de visites
	 * @return boolean
	 */
	public function add_visits($type, $number=0)
	{
	    static::getCounterTypes();
	    if (!in_array($type, static::$counter_types)) {
	        return false;
	    }
	    $number = intval($number);
	    if ($number) {
	        $values = [];
	        for ($i=0; $i<$number; $i++) {
	            $values [] = '(NOW(), "'.$this->location.'", "'.$type.'")';
	        }
	        $query = 'INSERT INTO visits_statistics (visits_statistics_date, visits_statistics_location, visits_statistics_type) VALUES '.implode(', ', $values);
	        pmb_mysql_query($query);
	    }
	}

    /**
     * Retire une visite au compteur du type specifie
     *
     * @param string $type : type de compteur
     * @return boolean
     */
    public function remove_visit($type)
    {
        static::getCounterTypes();
        if (!in_array($type, static::$counter_types)) {
            return false;
        }

        $query = 'DELETE FROM visits_statistics WHERE visits_statistics_id in (SELECT * FROM (SELECT MAX(visits_statistics_id) FROM visits_statistics WHERE DATE(visits_statistics_date) = "' .
            $this->date . '" AND visits_statistics_location = "' . $this->location . '" AND visits_statistics_type = "' . $type . '") AS vs)';
        pmb_mysql_query($query);
    }

	/**
	 * Retire une quantité de visite au compteur du type specifie
	 *
	 * @param string $type : type de compteur
	 * @param integer $number = nombre de visites
	 * @return boolean
	 */
	public function remove_visits($type, $number=0)
	{
	    static::getCounterTypes();
	    if (!in_array($type, static::$counter_types)) {
	        return false;
	    }
	    $number = intval($number);
	    if ($number) {
	        $ids = [];
	        $query = 'SELECT visits_statistics_id FROM visits_statistics 
                    WHERE DATE(visits_statistics_date) = "' . $this->date . '" 
                    AND visits_statistics_location = "' . $this->location . '" 
                    AND visits_statistics_type = "' . $type . '" 
                    ORDER BY visits_statistics_id DESC LIMIT 0, '.$number;
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            while ($row = pmb_mysql_fetch_object($result)) {
	                $ids[] = $row->visits_statistics_id;
	            }
	            $query = 'DELETE FROM visits_statistics WHERE visits_statistics_id in (
                    '.implode(',', $ids).')';
                pmb_mysql_query($query);
	        }
	    }
	    
	}

    /**
     * Met a jour le nombre de visites du compteur de type specifie
     *
     * @param string $type : type de compteur
     * @param int $value : valeur a affecter
     * @return boolean
     */
    public function update_visits($type, $value)
    {
        static::getCounterTypes();
        if (!in_array($type, static::$counter_types)) {
            return false;
        }
        $value = intval($value);
        if ($value > 9999) {
            //La saisie est limitee a 4 chiffres, limitons une action malveillante
            return false;
        }
        if ($value < 0) {
            $value = 0;
        }

        $this->get_statistics();
        if (!isset($this->statistics[$this->date][$type])) {
            $this->statistics[$this->date][$type] = 0;
        }

        // La valeur est superieure a ce qui est en base, on ajoute des visites
        if ($this->statistics[$this->date][$type] < $value) {
		    $number = ($value - $this->statistics[$this->date][$type]);
		    $this->add_visits($type, $number);
        }
        // La valeur est inferieure a ce qui est en base, on supprime des visites
        if ($this->statistics[$this->date][$type] > $value) {
		    $number = ($this->statistics[$this->date][$type] - $value);
		    $this->remove_visits($type, $number);
        }
    }


	public static function get_config(){
		global $include_path, $base_path, $charset;

		if (isset(self::$config)) {
			return self::$config;
		}

		if(file_exists($include_path.'/visits_statistics/config_subst.xml')){
			$xmlFile = $include_path.'/visits_statistics/config_subst.xml';
		}elseif(file_exists($include_path.'/visits_statistics/config.xml')){
			$xmlFile = $include_path.'/visits_statistics/config.xml';
		}else{
			//pas de fichier à analyser
			return false;
		}

		$fileInfo = pathinfo($xmlFile);
		$tempFile = $base_path."/temp/XML".preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset).".tmp";

		if (!file_exists($tempFile) || (filemtime($xmlFile) > filemtime($tempFile))) {
			//Le fichier XML original a-t-il été modifié ultérieurement ?
			//on va re-générer le pseudo-cache
			if(file_exists($tempFile)){
				unlink($tempFile);
			}
			//Parse le fichier dans un tableau
			$fp=fopen($xmlFile,"r") or die("Can't find XML file ".$xmlFile);
			$xml=fread($fp, filesize($xmlFile));
			fclose($fp);
			$xml_2_analyze=_parser_text_no_function_($xml, 'STATS');

			self::$config = array(
					'main' => array(),
					'services' => array(),
					'categories' => array()
			);
			if (isset($xml_2_analyze['MAIN'])) {
				foreach ($xml_2_analyze['MAIN'] as $main) {
					self::$config['main'][$main['TYPE']] = array(
							'name' => get_msg_to_display($main['NAME']),
							'color' => $main['COLOR'],
							'shortcut' => (isset($main['SHORTCUT']) ? $main['SHORTCUT'] : ''),
							'show_shortcut' => ((isset($main['SHOW_SHORTCUT']) && ($main['SHOW_SHORTCUT'] == 'yes')) ? true : false)
					);
				}
			}
			if (isset($xml_2_analyze['SERVICES'][0]['SERVICE'])) {
				foreach ($xml_2_analyze['SERVICES'][0]['SERVICE'] as $service) {
					self::$config['services'][$service['TYPE']] = array(
							'name' => get_msg_to_display($service['NAME']),
							'color' => $service['COLOR'],
							'shortcut' => (isset($service['SHORTCUT']) ? $service['SHORTCUT'] : ''),
							'show_shortcut' => ((isset($service['SHOW_SHORTCUT']) && ($service['SHOW_SHORTCUT'] == 'yes')) ? true : false),
							'category' => (isset($service['CATEGORY']) ? $service['CATEGORY'] : 0)
					);
				}
			}
			if (isset($xml_2_analyze['CATEGORIES']) && isset($xml_2_analyze['CATEGORIES'][0]['CATEGORY'])) {
				foreach ($xml_2_analyze['CATEGORIES'][0]['CATEGORY'] as $category) {
					self::$config['categories'][$category['ID']] = array(
							'name' => get_msg_to_display($category['NAME'])
					);
				}
			}

			$tmp = fopen($tempFile, "wb");
			fwrite($tmp, serialize(self::$config));
			fclose($tmp);
		} else if (file_exists($tempFile)){
			$tmp = fopen($tempFile, "r");
			self::$config = unserialize(fread($tmp, filesize($tempFile)));
			fclose($tmp);
		}

		return self::$config;
	}

	/**
	 * Retourne la liste des compteurs definis
	 *
	 * @return array
	 */
	public function getCounterTypes()
	{
	    self::get_config();
	    if(!is_null(static::$counter_types)) {
	        return static::$counter_types;
	    }
	    $counter_types = [];
	    if(!empty(self::$config['main'])) {
	        $counter_types = array_keys(self::$config['main']);
	    }
	    if(!empty(self::$config['services'])) {
	        $counter_types = array_merge($counter_types, array_keys(self::$config['services']));
	    }
	    if(!empty(self::$config['categories'])) {
	        $counter_types = array_merge($counter_types, array_keys(self::$config['categories']));
	    }
	    static::$counter_types = $counter_types;
	    return static::$counter_types;
	}


	public static function get_label_from_type($type) {
		self::get_config();
		foreach (self::$config['main'] as $main_type => $main) {
			if($main_type == $type) {
				return $main['name'];
			}
		}
		foreach (self::$config['services'] as $service_type => $service) {
			if($service_type == $type) {
				return $service['name'];
			}
		}
		return '';
	}

	public function get_json_statistics() {
		$this->get_statistics();
		return encoding_normalize::json_encode($this->statistics[$this->date]);
	}
}