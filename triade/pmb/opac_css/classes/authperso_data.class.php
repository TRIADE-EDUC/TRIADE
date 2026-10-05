<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_data.class.php,v 1.3.8.1 2025/01/09 15:32:15 rtigero Exp $

class authperso_data {
    
    /**
     * Antiloop empechant une boucle sur les autorités personnalisées liées
     * @var array
     */
    private static $antiloop;
    
    /**
     * Instance d'autorité personnalisée
     * @var authperso
     */
    private $authperso;
    
    /**
     * Tableau d'instances de la classe authperso_data
     * @var array
     */
    private static $authperso_data_instances;
    
    /**
     * Tableau d'instances de la classe authperso
     * @var array
     */
    private static $authperso_instances;
    
    /**
     * Script d'affichage de l'ISBD
     * @var string
     */
    private $authperso_isbd_script;
    
    /**
     * Nom de l'autorité personnalisée
     * @var string
     */
    private $authperso_name;
    
    /**
     * Numéro de l'autorité personnalisée
     * @var int
     */
    private $authperso_num;
    
    /**
     * Script d'affichage de la vue
     * @var string
     */
    private $authperso_view_script;
    
    /**
     * Instance de paramètres personnalisés
     * @var custom_parametres_perso
     */
    private $custom_pperso;
    
    /**
     * Tableau d'instances de la classe custom_parametres_perso
     * @var array
     */
    private static $custom_pperso_instances;
    
    /**
     * Flag pour savoir si l'autorité personnalisé est un évènement
     * @var int
     */
    private $event;
    
    /**
     * Identifiant de l'autorité personnalisée
     * @var int
     */
    private $id;
    
    /**
     * Tableau d'instances de la classe index_concept
     * @var array
     */
    private static $index_concept_instances;
    
    /**
     * ISBD de l'autorité personnalisée
     * @var string
     */
    private $isbd;
    
    /**
     * Permalink de l'autorité personnalisée
     * @var string
     */
    private $permalink;
    
    /**
     * Tableau d'instances de record_datas
     * @var array
     */
    private $records;
    
    /**
     * Vue de l'autorité personnalisée
     * @var string
     */
    private $view;

    /**
     * Responsabilité de l'autorité personnalisée
     * @var array
     */
    private $responsabilities;
    
    public function __construct($id = 0) {
        $this->id = (int) $id;
        $this->authperso = $this->get_authperso_instance()->info;
        $this->custom_pperso = $this->get_pperso_instance();
    }
    
    private function get_authperso_instance() {
        if (!isset(self::$authperso_instances[$this->id])) {
            self::$authperso_instances[$this->id] = new authperso($this->get_authperso_num());
        }
        return self::$authperso_instances[$this->id];
    }
    
    private function get_index_concept_instance() {
        if (!isset(self::$index_concept_instances[$this->id])) {
            self::$index_concept_instances[$this->id] = new index_concept($this->id, TYPE_AUTHPERSO);
        }
        return self::$index_concept_instances[$this->id];
    }
    
    private function get_pperso_instance() {
        if (!isset(self::$custom_pperso_instances[$this->get_authperso_num()])) {
            self::$custom_pperso_instances[$this->get_authperso_num()] = new custom_parametres_perso('authperso', 'authperso', $this->get_authperso_num(), "./autorites.php?categ=authperso&sub=update&id_authperso=$this->id");
        }
        return self::$custom_pperso_instances[$this->get_authperso_num()];
    }
    
    public static function get_instance($id) {
        if (!isset(static::$authperso_data_instances[$id])) {
            static::$authperso_data_instances[$id] = new authperso_data($id);
        }
        return static::$authperso_data_instances[$id];
    }
    
    public function get_authperso_isbd_script() {
        if (!isset($this->authperso_isbd_script)) {
            $res = pmb_mysql_query("SELECT authperso_isbd_script FROM authperso WHERE id_authperso=" . $this->get_authperso_num());
            if ($row = pmb_mysql_fetch_object($res)) {
                $this->authperso_isbd_script = '';
                if (!empty($row->authperso_isbd_script)) {
                    $this->authperso_isbd_script = $row->authperso_isbd_script;
                }
            }
        }
        return $this->authperso_isbd_script;
    }
    
    public function get_authperso_num() {
        if (!isset($this->authperso_num)) {
            $res = pmb_mysql_query("SELECT authperso_authority_authperso_num FROM authperso_authorities WHERE id_authperso_authority=$this->id");
            if ($row = pmb_mysql_fetch_object($res)) {
                $this->authperso_num = (int) $row->authperso_authority_authperso_num;
            }
        }
        return $this->authperso_num;
    }
    
    public function get_authperso_view_script() {
        if (!isset($this->authperso_view_script)) {
            $res = pmb_mysql_query("SELECT authperso_view_script FROM authperso WHERE id_authperso=" . $this->get_authperso_num());
            $this->authperso_view_script = '';
            if ($row = pmb_mysql_fetch_object($res)) {
                $this->authperso_view_script = $row->authperso_view_script;
            }
        }
        return $this->authperso_view_script;
    }
    
    
    public function get_id() {
        return $this->id;
    }
    
    public function get_isbd() {
        global $base_path;
        
        if (!isset($this->isbd)) {
            $this->custom_pperso->get_out_values($this->id);
            $authperso_fields = $this->custom_pperso->values;
            $authperso_isbd_script = $this->get_authperso_isbd_script();
            if (!empty($authperso_isbd_script)) {
                $index_concept = $this->get_index_concept_instance();
                $authperso_fields['index_concepts'] = $index_concept->get_data();
                
                $template_path = "$base_path/temp/" . LOCATION . "_authperso_isbd_$this->authperso_num";
                if (!file_exists($template_path) || (md5($authperso_isbd_script) != md5_file($template_path))) {
                    file_put_contents($template_path, $authperso_isbd_script);
                }
                $h2o = H2o_collection::get_instance($template_path);
                $this->isbd = $h2o->render($authperso_fields);
            } else {
                $this->isbd = '';
                foreach ($authperso_fields as $field) {
                    $this->isbd .= (!empty($field['values'][0]['format_value']) ? $field['values'][0]['format_value'] . '.  ' : '');
                }
            }
        }
        return $this->isbd;
    }
    
    public function get_name() {
        return $this->authperso['name'];
    }
    
    public function get_permalink() {
        global $liens_opac;
        if (!isset($this->permalink)) {
            $this->permalink = str_replace('!!id!!', $this->id, $liens_opac['lien_rech_authperso']);
        }
        return $this->permalink;
    }
    
    public function get_records() {
        if (!isset($this->records)) {
            $res = pmb_mysql_query("SELECT notice_authperso_notice_num AS id FROM notices_authperso WHERE notice_authperso_authority_num = $this->id ORDER BY notice_authperso_order");
            $this->records = [];
            if (pmb_mysql_num_rows($res)) {
                while ($row = pmb_mysql_fetch_assoc($res)) {
                    $this->records[] = record_datas::get_instance($row['id']);
                }
            }
        }
        return $this->records;
    }
    
    public function get_view() {
        global $base_path;
        
        if (!isset($this->view)) {
            $this->custom_pperso->get_out_values($this->id);
            $authperso_fields = $this->custom_pperso->values;
            if (empty(static::$antiloop[$this->id])) {
                static::$antiloop[$this->id] = true;
                $aut_link = new aut_link($this->authperso_num + 1000, $this->id);
                $authperso_fields['authorities_link'] = $aut_link->get_data();
            }
            $authperso_view_script = $this->get_authperso_view_script();
            if (!empty($authperso_view_script)) {
                $template_path = "$base_path/temp/" . LOCATION . "_authperso_isbd_$this->authperso_num";
                if (!file_exists($template_path)  || (md5($authperso_view_script) != md5_file($template_path))) {
                    file_put_contents($template_path, $authperso_view_script);
                }
                $h2o = H2o_collection::get_instance($template_path);
                $this->view = $h2o->render($authperso_fields);
            } else {
                $this->view = '';
                foreach ($authperso_fields as $field) {
                    $this->view .= (!empty($field['values'][0]['format_value']) ? $field['values'][0]['format_value'] . '.  ' : '');
                }
            }
        }
        return $this->view;
    }
    
    public function is_event() {
        if (!isset($this->event)) {
            $this->event = $this->authperso['event'];
        }
        return $this->event;
    }
    
    public function get_p_perso() {
        return $this->get_pperso_instance();
    }
    
    public function get_info() {
        // BT - Pour maintenir la compatibilité avec les écritures {{ auth.info.view }} ou {{ auth.info.isbd }} par exemple
        return $this;
    }

    public function get_responsabilities()
	{
		if(! is_null($this->responsabilities)) {
			return $this->responsabilities;
		}

		$this->responsabilities = array();

		//On verifie que les responsabilites sont dispos sur cette autorite
		if($this->id && isset($this->authperso) && $this->authperso["responsability_authperso"]) {
			$responsabilities = new responsabilities();
			$authperso_responsabilities = $responsabilities->get_responsabilities_authperso($this->id);
			//On nettoie le vedette_author qui est un element de formulaire
			$this->responsabilities = array_map(function($elem) {
				unset($elem['vedette_author']);
				return $elem;
			}, $authperso_responsabilities);
		}

		return $this->responsabilities;
	}
}