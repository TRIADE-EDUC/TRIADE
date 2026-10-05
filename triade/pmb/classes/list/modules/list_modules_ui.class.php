<?php
use Pmb\Users\Controller\RolesController;

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_modules_ui.class.php,v 1.15.2.2 2024/11/15 13:21:48 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . "/list/list_ui.class.php");
require_once ($class_path . "/modules/module_model.class.php");

class list_modules_ui extends list_ui
{

    protected static $no_check_rights;
    
    protected $rolesController;
    
    protected function has_module_permission($name, $permission=0) {
        global $PMBuserid;
        
        if(!empty(static::$no_check_rights)) {
            return true;
        }
        $data = [
            'module' => $name,
            'userId' => $PMBuserid
        ];
        $userRights = $this->rolesController->getUserRights('modules', $data);
        if (isset($userRights['visible']) && $userRights['visible'] == 0) {
            return false;
        }
        if (defined('SESSrights') && (SESSrights & $permission)) {
            return true;
        }
        if($permission == 0) {
            return true;
        }
        return false;
    }
    
    protected function _init_modules()
    {
        global $msg;
        global $dsi_active, $acquisition_active, $pmb_extension_tab, $demandes_active;
        global $fiches_active, $semantic_active, $frbr_active, $modelling_active, $animations_active;

        // Tableau de bord
        if ($this->has_module_permission('dashboard')) {
            $this->add_module('dashboard', $msg['dashboard'], $msg['dashboard'], $msg['2001'], 'icon');
        }
        // L'utilisateur fait la CIRCULATION ?
        if ($this->has_module_permission('circ', CIRCULATION_AUTH)) {
            $this->add_module('circ', $msg['5'], $msg['742'], $msg['2001']);
        }
        // L'utilisateur fait le CATALOGAGE ?
        if ($this->has_module_permission('catalog', CATALOGAGE_AUTH)) {
            $this->add_module('catalog', $msg['6'], $msg['743'], $msg['2002']);
        }
        // L'utilisateur fait les AUTORITÉS ?
        if ($this->has_module_permission('autorites', AUTORITES_AUTH)) {
            $this->add_module('autorites', $msg['132'], $msg['744'], $msg['2003']);
        }
        // L'utilisateur fait l'ÉDITIONS ?
        if ($this->has_module_permission('edit', EDIT_AUTH)) {
            $this->add_module('edit', $msg['1100'], $msg['745'], $msg['2004']);
        }

        // L'utilisateur fait la DSI ?
        if ($dsi_active && $this->has_module_permission('dsi', DSI_AUTH)) {
            $this->add_module('dsi', $msg['dsi_menu'], $msg['dsi_menu_title']);
        }

        // L'utilisateur fait l'ACQUISITION ?
        if ($acquisition_active && $this->has_module_permission('acquisition', ACQUISITION_AUTH)) {
            $this->add_module('acquisition', $msg['acquisition_menu'], $msg['acquisition_menu_title']);
        }

        // L'utilisateur accède aux extensions ?
        if ($pmb_extension_tab && $this->has_module_permission('extensions', EXTENSIONS_AUTH)) {
            $this->add_module('extensions', $msg['extensions_menu'], $msg['extensions_menu_title']);
        }

        // L'utilisateur fait les DEMANDES ?
        if ($demandes_active && $this->has_module_permission('demandes', DEMANDES_AUTH)) {
            $this->add_module('demandes', $msg['demandes_menu'], $msg['demandes_menu_title']);
        }

        // L'utilisateur fait l'onglet FICHES ?
        if ($fiches_active && $this->has_module_permission('fichier', FICHES_AUTH)) {
            $this->add_module('fichier', $msg['onglet_fichier'], $msg['onglet_fichier']);
        }

        // L'utilisateur fait l'onglet SEMANTIC ?
        if ($semantic_active == true && $this->has_module_permission('semantic', SEMANTIC_AUTH)) {
            $this->add_module('semantic', $msg['semantic_onglet_title'], $msg['semantic_onglet_title']);
        }

        // L'utilisateur fait l'onglet CMS ?
        if ($this->has_module_permission('cms', CMS_AUTH)) {
            $this->add_module('cms', $msg['cms_onglet_title'], $msg['cms_onglet_title']);
        }

        // L'utilisateur fait l'onglet FRBR ?
        if ($frbr_active == true && $this->has_module_permission('frbr', FRBR_AUTH)) {
            $this->add_module('frbr', $msg['frbr'], $msg['frbr']);
        }

        // L'utilisateur fait l'onglet modélisation ?
        if ($modelling_active == true && $this->has_module_permission('modelling', MODELLING_AUTH)) {
            $this->add_module('modelling', $msg['modelling'], $msg['modelling']);
        }
        // L'utilisateur fait l'ANIMATION ?
        if ($animations_active && $this->has_module_permission('animations', ANIMATION_AUTH)) {
            $this->add_module('animations', $msg['animation_base_title'], $msg['animation_title_css']);
        }
        // L'utilisateur fait l'ADMINISTRATION ?
        if ($this->has_module_permission('admin', ADMINISTRATION_AUTH)) {
            $this->add_module('admin', $msg['7'], $msg['746'], $msg['2005']);
        }
    }

    protected function fetch_data()
    {
        $this->objects = array();
        $this->rolesController = new RolesController();
        $this->_init_modules();
        $this->pager['nb_results'] = count($this->objects);
        $this->messages = "";
    }

    public function add_module($name, $label, $title = '', $accesskey = '', $display_mode = '')
    {
        $module_model = new module_model($name);
        $module_model->set_label($label)
            ->set_title($title)
            ->set_accesskey($accesskey)
            ->set_display_mode($display_mode);
        $this->add_object($module_model);
    }

    public function get_module_destination_link($name)
    {
        global $base_path;
        global $cms_active;

        $module_model = new module_model($name);
        if ($module_model->get_destination_link()) {
            return $module_model->get_destination_link();
        } else {
            $link = $base_path . "/" . $name . ".php";
            switch ($name) {
                case 'autorites':
                    $link .= "?categ=search";
                    break;
                case 'edit':
                    $link .= "?categ=procs";
                    break;
                case 'cms':
                    $link .= ($cms_active ? "?categ=editorial&sub=list" : "?categ=frbr_pages&sub=list");
                    break;
            }
            return $link;
        }
    }

    public function get_display_module($name, $label, $title = '', $accesskey = '', $display_mode = '')
    {
        global $current, $charset;

        $display = "<li id='navbar-" . $name . "' ";
        if ($current == $name . ".php") {
            $display .= " class='current'><a class='current' ";
        } else {
            $display .= "><a ";
        }
        $display .= "title='" . htmlentities($label, ENT_QUOTES, $charset) . " : " . htmlentities($title, ENT_QUOTES, $charset) . "' href='./" . $this->get_module_destination_link($name) . "' accesskey='" . htmlentities($accesskey, ENT_QUOTES, $charset) . "'>";
        if ($display_mode == 'icon') {
            $display .= "<img title='" . htmlentities($title, ENT_QUOTES, $charset) . "' alt='" . htmlentities($title, ENT_QUOTES, $charset) . "' src='" . get_url_icon($name . '.png') . "'/>";
        } else {
            $display .= htmlentities($label, ENT_QUOTES, $charset);
        }
        $display .= "</a></li>";
        return $display;
    }

    protected function get_display_notification_zone()
    {
        global $msg;
        global $current, $class_path;
        global $styles_path, $stylesheet;
        global $pmb_dashboard_quick_params_activate;

        $notification_zone = "
		<div id='notification_zone'>
			<div class='row ui-flex ui-flex-between '>
				<div class='ui-flex-grow'>
					!!visits_statistics!!
					<div class='row' id='plugins'>!!plugins!!</div>
					<div class='row' id='quick_actions'>!!quick_actions!!</div>
					<div class='row' id='indexation_infos'></div>
				</div>
				<div class='ui-flex-shrink' id='alert_zone'></div>
			</div>
			<div class='row' id='notifications'></div>
		</div>";

        // chargement du tableau de board du module...
        $dashboard_module_name = substr($current, 0, strpos($current, "."));
        $dashboard_class_name = '';
        if (file_exists($class_path . "/dashboard/dashboard_module_" . $dashboard_module_name . ".class.php")) {
            // on récupère la classe;
            require_once ($class_path . "/dashboard/dashboard_module_" . $dashboard_module_name . ".class.php");
            $dashboard_class_name = "dashboard_module_" . $dashboard_module_name;
            $dash = new $dashboard_class_name();
            // Dans certains cas, l'affichage change...
            switch ($dashboard_module_name) {
                case "dashboard":
                    // dans le tableau de bord, on n'affiche rien en notification...
                    return '';
                default:
                    if (file_exists($styles_path . "/" . $stylesheet . "/images/notification_new.png")) {
                        $notif_icon_path = $styles_path . "/" . $stylesheet . "/images";
                    } else {
                        $notif_icon_path = "./images";
                    }
                    $notification_zone .= "
			<script type='text/javascript'>var notif = new notification('" . $dashboard_module_name . "','" . addslashes($msg['empty_notification']) . "','" . addslashes($msg['new_notification']) . "','" . $notif_icon_path . "/notification_new.png','" . $notif_icon_path . "/notification_empty.png')</script>";

                    $notification_zone = str_replace("!!visits_statistics!!", $dash->get_visits_statistics_form(), $notification_zone);
                    $notification_zone = str_replace("!!plugins!!", $dash->get_plugins_form(), $notification_zone);
                    $quick_params = ($pmb_dashboard_quick_params_activate ? $dash->get_quick_params_form() : '');
                    $notification_zone = str_replace("!!quick_actions!!", $quick_params ?? "", $notification_zone);
                    return $notification_zone;
            }
        } else {
            return '';
        }
    }

    public function get_display()
    {
        global $msg;
        // ----------------------------------
        // $menu_bar : template menu bar
        // Générer le $menu_bar selon les droits...
        // Par défaut : la page d'accueil.
        $display = '<!--	Menu bar	-->
			' . $this->get_display_notification_zone() . '
			<div id="navbar">
				<h3><span>' . $msg['1913'] . '</span></h3>
				<ul>';
        foreach ($this->objects as $object) {
            $display .= $this->get_display_module($object->get_name(), $object->get_label(), $object->get_title(), $object->get_accesskey(), $object->get_display_mode());
        }
        $display .= '
				</ul>
			</div>';
        return $display;
    }

    /**
     * Initialisation des filtres disponibles
     */
    protected function init_available_filters()
    {
        $this->available_filters['main_fields'] = array();
        $this->available_filters['custom_fields'] = array();
    }

    /**
     * Initialisation des colonnes disponibles
     */
    protected function init_available_columns()
    {
        $this->available_columns = array(
            'main_fields' => array(
                'label' => '103',
                'destination_link' => 'admin_URL',
                'initialization' => 'initialization'
            )
        );
        $this->available_columns['custom_fields'] = array();
    }

    protected function init_no_sortable_columns()
    {
        $this->no_sortable_columns = array(
            'label',
            'destination_link',
            'initialization'
        );
    }

    protected function init_default_pager()
    {
        parent::init_default_pager();
        $this->pager['all_on_page'] = true;
    }

    protected function init_default_columns()
    {
        $this->add_column_selection();
        $this->add_column('label');
        $this->add_column('destination_link');
        $this->add_column('initialization');
    }

    protected function init_default_settings()
    {
        parent::init_default_settings();
        $this->set_setting_display('search_form', 'visible', false);
        $this->set_setting_display('search_form', 'export_icons', false);
        $this->set_setting_display('query', 'human', false);
        $this->set_setting_column('default', 'align', 'left');
    }

    protected function _get_object_property_destination_link($object)
    {
        $list_ui_class_name = 'list_tabs_' . $object->get_name() . '_ui';
        if (class_exists($list_ui_class_name)) {
            $list_ui_class_name::set_module_name($object->get_name());
            $list_ui_instance = new $list_ui_class_name();
            $module_tabs_objects = $list_ui_instance->get_objects();
            if (count($module_tabs_objects)) {
                foreach ($module_tabs_objects as $tab) {
                    if ($object->get_destination_link() == $tab->get_destination_link()) {
                        return $tab->get_label();
                    }
                }
                return $module_tabs_objects[0]->get_label();
            }
        }
        return $object->get_destination_link();
    }

    protected function _get_object_property_initialization($object)
    {
        if ($object->is_substituted()) {
            return 1;
        }
        return 0;
    }

    protected function get_cell_content($object, $property)
    {
        $content = '';
        switch ($property) {
            case 'initialization':
                if ($object->is_substituted()) {
                    $link = static::get_controller_url_base() . "&action=delete&name=" . $object->get_name();
                    $content .= $this->get_img_cell_content('initialization.png', 'initialize', $link, 'initialization_confirm');
                }
                break;
            default:
                $content .= parent::get_cell_content($object, $property);
                break;
        }
        return $content;
    }

    protected function get_default_attributes_format_cell($object, $property)
    {
        $attributes = array();
        switch ($property) {
            case 'initialization':
                break;
            default:
                $attributes['onclick'] = "window.location=\"" . static::get_controller_url_base() . "&action=edit&name=" . $object->get_name() . "\"";
                break;
        }
        return $attributes;
    }

    protected function init_default_selection_actions()
    {
        global $msg;

        parent::init_default_selection_actions();
        $initialize_link = array(
            'href' => static::get_controller_url_base() . "&action=list_delete",
            'confirm' => $msg['initialization_confirm']
        );
        $this->add_selection_action('delete', $msg['initialize'], '', $initialize_link);
    }

    public function get_objects_data() {
        if (empty($this->selected_columns['name'])) {
            $this->add_column('name');
        }
        return parent::get_objects_data();
    }
    
    public static function delete_object($name)
    {
        module_model::delete($name);
    }
    
    public static function set_no_check_rights($no_check_rights) {
        static::$no_check_rights = intval($no_check_rights);
    }
}