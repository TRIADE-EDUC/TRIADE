<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animation_conf.class.php,v 1.3 2023/03/22 10:05:35 gneveu Exp $
use Pmb\Animations\Orm\AnimationTypesOrm;
use Pmb\Animations\Orm\AnimationOrm;

if (stristr($_SERVER['REQUEST_URI'], '.class.php')) {
    die('no access');
}

require_once ($base_path . '/plugins/animation/includes/templates/animation_conf.tpl.php');

class animation_conf
{

    public const OPTION_AUTOMATIQUE = 1;

    public const OPTION_MANUELLE = 0;

    public const OPTION_ENDDATE_ARTICLE_NO = 0;

    public const OPTION_ENDDATE_ARTICLE_YES = 1;

    public const OPTION_CALENDAR_EMPTY = "";

    private $state_anim_update = self::OPTION_MANUELLE;

    private $state_anim_create = self::OPTION_MANUELLE;

    private $id_publication_state = 3;

    private $id_section_parent = 0;

    private $ids_calendar = array();
    
    private $calendar_data = array();
    
    private $errors = array();
    
    private $types = array();

    private $state_anim_enddate_article = self::OPTION_ENDDATE_ARTICLE_NO;

    public function __construct()
    {
        $this->unserialize();
    }

    private function unserialize()
    {
        $query = 'select valeur_param from parametres where type_param = "pmb" and sstype_param = "plugin_animation"';
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $json = pmb_mysql_result($result, 0, 0);
            $params = json_decode($json, true);
            foreach ($params as $param => $value) {
                
                if ($param == "id_calendar") {
                    // Ancienne donnée, on les convertit pour qu'elle corresponde au nouveau format de donnée
                    $param = "ids_calendar";
                    $value = [AnimationOrm::DEFAULT_TYPE => $value];
                }
                
                if (isset($this->{$param})) {
                    $this->{$param} = $value;
                }
            }
        }
    }

    /**
     * @param boolean $updated
     * @return string
     */
    public function get_form($updated = false)
    {
        global $animation_conf_form;

        $form = $animation_conf_form;
        $search = [
            "!!animation_conf_calendar_by_type!!",
            "!!animation_conf_state_publication!!",
            "!!animation_conf_anim_create!!",
            "!!animation_conf_anim_update!!",
            "!!animation_conf_section_parent!!",
            "!!animation_conf_anim_init!!",
            "!!animation_conf_anim_enddate_article!!"
        ];
        $replace = [
            $this->get_conf_anim_type(),
            $this->get_conf_state_publication(),
            $this->get_conf_anim_create(),
            $this->get_conf_anim_update(),
            $this->get_conf_section_parent(),
            $this->get_conf_anim_init(),
            $this->get_conf_anim_enddate_article()
        ];
        $form = str_replace($search, $replace, $form);
        if ($updated) {
            $form .= display_notification(plugins::get_message('animation', "animation_conf_successfully_saved"));
        }
        
        return $form;
    }

    /**
     * @param string|int $id_type
     * @return string
     */
    public function get_conf_calendar_options($id_type)
    {
        global $animation_conf_calendar_options, $charset;

        if (empty($this->ids_calendar[$id_type]) || self::OPTION_CALENDAR_EMPTY == $this->ids_calendar[$id_type]) {
            $selected = "selected";
        } else {            
            $selected = "";
        }
        
        $html = $animation_conf_calendar_options;
        $search = [
            "!!animation_conf_calendar_option_value!!",
            "!!animation_conf_calendar_option_label!!",
            "!!selected!!"
        ];
        $replace = [
            self::OPTION_CALENDAR_EMPTY,
            htmlentities(plugins::get_message('animation', "animation_conf_select_calendar"), ENT_QUOTES, $charset),
            $selected
        ];
        $html = str_replace($search, $replace, $html);
        
        $query = "SELECT managed_module_box FROM cms_managed_modules WHERE managed_module_name = 'cms_module_agenda'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $data = pmb_mysql_fetch_assoc($result);
            $data = unserialize($data['managed_module_box']);
            $data = $data['module']['calendars'];
            
            $selected = "";
            foreach ($data as $id_calendar => $calendar) {
                if (!empty($this->ids_calendar[$id_type]) && $id_calendar == $this->ids_calendar[$id_type]) {
                    $selected = "selected";
                }
                $replace = [
                    $id_calendar,
                    htmlentities($calendar['name'], ENT_QUOTES, $charset),
                    $selected
                ];
                $html .= str_replace($search, $replace, $animation_conf_calendar_options);

                $selected = "";
            }
        }
        
        return $html;
    }

    public function get_conf_state_publication()
    {
        global $animation_conf_state_publication, $charset;

        $selected = "";
        $html = $animation_conf_state_publication;
        $search = [
            "!!animation_conf_section_parent_option_value!!",
            "!!animation_conf_section_parent_option_label!!",
            "!!selected!!"
        ];
        $replace = [
            0,
            htmlentities(plugins::get_message('animation', "animation_conf_select_state"), ENT_QUOTES, $charset),
            $selected
        ];
        $html = str_replace($search, $replace, $html);

        $query = "SELECT id_publication_state, editorial_publication_state_label FROM cms_editorial_publications_states";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                if ($row['id_publication_state'] == $this->id_publication_state) {
                    $selected = "selected";
                }
                $replace = [
                    $row['id_publication_state'],
                    htmlentities($row['editorial_publication_state_label'], ENT_QUOTES, $charset),
                    $selected
                ];
                $html .= str_replace($search, $replace, $animation_conf_state_publication);

                $selected = "";
            }
        }
        return $html;
    }

    public function get_conf_section_parent()
    {
        global $animation_conf_section_parent, $charset;

        $selected = "";
        $html = $animation_conf_section_parent;
        $search = [
            "!!animation_conf_section_parent_option_value!!",
            "!!animation_conf_section_parent_option_label!!",
            "!!selected!!"
        ];
        $replace = [
            0,
            htmlentities(plugins::get_message('animation', "animation_conf_select_parent"), ENT_QUOTES, $charset),
            $selected
        ];
        $html = str_replace($search, $replace, $html);

        $query = "SELECT id_section, section_title FROM cms_sections";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                if ($row['id_section'] == $this->id_section_parent) {
                    $selected = "selected";
                }
                $replace = [
                    $row['id_section'],
                    htmlentities($row['section_title'], ENT_QUOTES, $charset),
                    $selected
                ];
                $html .= str_replace($search, $replace, $animation_conf_section_parent);

                $selected = "";
            }
        }
        return $html;
    }

    public function get_conf_anim_create()
    {
        global $animation_conf_anim_create;

        if ($this->state_anim_create == self::OPTION_MANUELLE) {
            $html = str_replace('!!checked_0!!', "checked", $animation_conf_anim_create);
            $html = str_replace('!!checked_1!!', "", $html);
        } else {
            $html = str_replace('!!checked_0!!', "", $animation_conf_anim_create);
            $html = str_replace('!!checked_1!!', "checked", $html);
        }

        return $html;
    }

    public function get_conf_anim_update()
    {
        global $animation_conf_anim_update;

        if ($this->state_anim_update == self::OPTION_MANUELLE) {
            $html = str_replace('!!checked_0!!', "checked", $animation_conf_anim_update);
            $html = str_replace('!!checked_1!!', "", $html);
        } else {
            $html = str_replace('!!checked_0!!', "", $animation_conf_anim_update);
            $html = str_replace('!!checked_1!!', "checked", $html);
        }

        return $html;
    }

    public function get_conf_anim_init()
    {
        global $animation_conf_anim_init;

        return str_replace('!!animation_conf_anim_init!!', "", $animation_conf_anim_init);

    }

    public function get_conf_anim_enddate_article()
    {
        global $animation_conf_anim_enddate_article;

        if ($this->state_anim_enddate_article == self::OPTION_ENDDATE_ARTICLE_NO) {
            $html = str_replace('!!checked_0!!', "checked", $animation_conf_anim_enddate_article);
            $html = str_replace('!!checked_1!!', "", $html);
        } else {
            $html = str_replace('!!checked_0!!', "", $animation_conf_anim_enddate_article);
            $html = str_replace('!!checked_1!!', "checked", $html);
        }

        return $html;

    }

    /**
     * Methode de sauvegarde
     */
    public function save_form()
    {
        $this->set_values_from_form();
        if (pmb_mysql_num_rows(pmb_mysql_query('select 1 from parametres where type_param = "pmb" and sstype_param = "plugin_animation"')) == 0) {
            $query = 'insert into parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					values ("pmb", "plugin_animation", "' . addslashes($this->serialize()) . '", "Plugin animation", "", 1)';
        } else {
            $query = 'update parametres set valeur_param = "' . addslashes($this->serialize()) . '" where type_param = "pmb" and sstype_param = "plugin_animation"';
        }
        pmb_mysql_query($query);
    }

    private function serialize()
    {
        return json_encode([
            'state_anim_update' => $this->state_anim_update,
            'state_anim_create' => $this->state_anim_create,
            'id_publication_state' => $this->id_publication_state,
            'id_section_parent' => $this->id_section_parent,
            'ids_calendar' => $this->ids_calendar,
            'state_anim_enddate_article' => $this->state_anim_enddate_article
        ]);
    }

    private function set_values_from_form()
    {
        global $animation_state_publication;
        global $animation_section_parent;
        global $animation_conf_update;
        global $animation_conf_create;
        global $animation_anim_enddate_article;

        $this->state_anim_update = stripslashes($animation_conf_update);
        $this->state_anim_create = stripslashes($animation_conf_create);
        $this->id_publication_state = stripslashes($animation_state_publication);
        $this->id_section_parent = stripslashes($animation_section_parent);
        $this->state_anim_enddate_article = stripslashes($animation_anim_enddate_article);

        $ids_calendar = array();
        foreach ($this->get_types() as $type) {
            $var_name = "animation_choose_calendar" . $type->id_type;
            global ${$var_name};
            $ids_calendar[$type->id_type] = ${$var_name};
        }
        $this->ids_calendar = $ids_calendar;
    }
    
    
    /**
     *
     * @return string
     */
    public function get_state_anim_update()
    {
        return $this->state_anim_update;
    }

    /**
     *
     * @return string
     */
    public function get_state_anim_enddate_article()
    {
        return $this->state_anim_enddate_article;
    }

    /**
     *
     * @return string
     */
    public function get_state_anim_create()
    {
        return $this->state_anim_create;
    }
    
    /**
     * ID statut de publication
     *
     * @return string|integer
     */
    public function get_id_publication_state()
    {
        return $this->id_publication_state;
    }
    
    /**
     * ID section/rubrique parente
     *
     * @return string|integer
     */
    public function get_id_section_parent()
    {
        return $this->id_section_parent;
    }
    
    /**
     * ID des calendrier
     *
     * @return string
     */
    public function get_ids_calendar()
    {
        return $this->ids_calendar;
    }
    
    /**
     * Données du calendrier
     *
     * @param string|int $id_type
     * @return string
     */
    public function get_calendar_data($id_type)
    {
        if (!empty($this->calendar_data)) {
            return $this->calendar_data;
        }

        if (empty($this->ids_calendar[$id_type])) {
            $this->ids_calendar[$id_type] = self::OPTION_CALENDAR_EMPTY;
        }
        
        if ($this->ids_calendar[$id_type] != self::OPTION_CALENDAR_EMPTY) {            
            $query = "SELECT managed_module_box FROM cms_managed_modules WHERE managed_module_name = 'cms_module_agenda'";
            $result = pmb_mysql_query($query);
            
            if (pmb_mysql_num_rows($result)) {
                $data = pmb_mysql_fetch_assoc($result);
                $data = unserialize($data['managed_module_box']);
                $data = $data['module']['calendars'];
                foreach ($data as $id_calendar => $calendar) {
                    if ($id_calendar == $this->ids_calendar[$id_type]) {
                        $this->calendar_data = $calendar;
                        return $calendar;
                    }
                }
            }
        }
        
        return array();
    }
    
    public function check_conf()
    {
        if (empty($this->ids_calendar)) {
            $this->errors[] = plugins::get_message('animation', "animation_error_calendar");
        }
        
        if (empty($this->id_publication_state)) {
            $this->errors[] = plugins::get_message('animation', "animation_error_publication_state");
        }
        
        if (empty($this->id_section_parent)) {
            $this->errors[] = plugins::get_message('animation', "animation_error_section_parent");
        }
        
        return $this->errors;
    }
    
    public function get_types() {
        if(!empty($this->types)) {
            return $this->types;
        }
        $this->types = AnimationTypesOrm::findAll();
        return $this->types;
    }
    
    public function get_conf_anim_type() {
        global $animation_conf_calendar_type_row, $charset;
        
        $types = $this->get_types();
        $html = "";
        
        foreach ($types as $type) {
            
            if (empty($this->ids_calendar[$type->id_type])) {
                // On met aucun calendrier par défaut
                $this->ids_calendar[$type->id_type] = self::OPTION_CALENDAR_EMPTY;
            }
            
            $html .= str_replace('!!type_label!!', htmlentities($type->label, ENT_QUOTES, $charset), $animation_conf_calendar_type_row);
            $html = str_replace('!!index!!', intval($type->id_type), $html);
            $html = str_replace("!!animation_conf_calendar_options!!",$this->get_conf_calendar_options($type->id_type), $html);
        }
        
        return $html;
    }
    
    public static function animations_is_active() {
        global $animations_active;
        return $animations_active == 1;
    }
}