<?php

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_selfservice_ui.class.php,v 1.4.4.1 2024/05/15 06:34:50 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class list_configuration_selfservice_ui extends list_configuration_ui
{
    public function __construct($filters = array(), $pager = array(), $applied_sort = array())
    {
        static::$module = 'admin';
        static::$categ = 'selfservice';
        static::$sub = str_replace(array('list_configuration_selfservice_', '_ui'), '', static::class);
        parent::__construct($filters, $pager, $applied_sort);
    }

    protected function get_form_title()
    {
        global $msg, $charset, $sub;

        return htmlentities($msg['selfservice_admin_'.$sub], ENT_QUOTES, $charset);
    }

    protected function init_default_settings()
    {
        parent::init_default_settings();
        $this->set_setting_display('grouped_objects', 'sort', false);
        $this->set_setting_column('action', 'display_mode', 'edition');
        $this->set_setting_column('message', 'display_mode', 'edition');
        $this->settings['objects']['default']['display_mode'] = 'form_table';
    }

    protected function get_main_fields_from_sub()
    {
        return array(
                'label' => 'selfservice_param_tab',
                'action' => 'selfservice_param_action',
                'message' => 'selfservice_param_message'
        );
    }

    protected function init_no_sortable_columns()
    {
        $this->no_sortable_columns = array(
                'label', 'action', 'message'
        );
    }

    public function add_selfservice($label_code, $message, $action = null)
    {
        global $msg;
        $selfservice = array(
                'label' => $msg[$label_code],
                'message' => $message,
                'action' => $action,
        );
        $this->add_object((object) $selfservice);
    }

    protected function get_cell_edition_content($object, $property)
    {
        $content = '';
        switch($property) {
            case 'action':
                if(is_object($object->action)) {
                    $content .= $this->get_cell_edition_format_content($object->action, 'valeur_param', 'select');
                }
                break;
            case 'message':
                global $charset;
                if(is_array($object->message->valeur_param)) {
                    foreach($object->message->valeur_param as $id => $message) {
                        $content .= "<br><br><b>".htmlentities($message['label'], ENT_QUOTES, $charset)."</b><br>";
                        $content .= "<input type='text' name='".$this->get_name_cell_edition($object->message, 'valeur_param')."[$id]' value='".htmlentities($message['value'], ENT_QUOTES, $charset)."' size='50' />";
                    }
                } elseif ($object->message) {
                    $content .= $this->get_cell_edition_format_content($object->message, 'valeur_param');
                }
                break;
            default:
                $content .= parent::get_cell_edition_content($object, $property);
                break;
        }
        return $content;
    }

    protected function get_button_add()
    {
        return '';
    }

    protected function save_object_property($object, $property)
    {
        switch ($property) {
            case 'action':
            case 'message':
                if(!empty($object->{$property})) {
                    $this->save_parameter($object->{$property}, 'valeur_param');
                }
                break;
        }
    }
}
