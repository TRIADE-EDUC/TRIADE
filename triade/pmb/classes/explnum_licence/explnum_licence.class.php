<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence.class.php,v 1.22 2023/06/23 14:47:47 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

use Spipu\Html2Pdf\Html2Pdf;

global $class_path, $include_path;
require_once ($include_path . '/templates/explnum_licence/explnum_licence.tpl.php');
require_once ($class_path . '/explnum_licence/explnum_licence_profile.class.php');
require_once ($class_path . '/explnum_licence/explnum_licence_right.class.php');
require_once ($class_path . '/translation.class.php');

/**
 * Classe de gestion des régimes de licence
 *
 * @author apetithomme, vtouchard
 *        
 */
class explnum_licence {
    /**
     * Identifiant
     *
     * @var int
     */
    protected $id;

    /**
     * Libellé du régime de licence
     *
     * @var string
     */
    protected $label;

    /**
     * URI
     *
     * @var string
     */
    protected $uri;

    /**
     * Profils associés
     *
     * @var explnum_licence_profile
     */
    protected $profiles;

    /**
     * Droits associés
     *
     * @var explnum_licence_right
     */
    protected $rights;

    public static $script_included = false;

	public function __construct($id = 0) {
        $this->id = intval($id);
        $this->fetch_data();
    }

    public function get_content_form() {
    	$interface_content_form = new interface_content_form(static::class);
    	$interface_content_form->add_element('explnum_licence_label', 'docnum_statut_libelle')
    	->add_input_node('text', $this->get_label())
    	->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_label'));
    	$interface_content_form->add_element('explnum_licence_uri', 'explnum_licence_uri')
    	->add_input_node('text', $this->get_uri())
    	->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_uri'));
    	return $interface_content_form->get_display();
    }
    
    public function get_form() {
        global $msg;
        global $base_path;

        $interface_form = new interface_form('explnumlicenceform');
        if (! $this->id) {
            $interface_form->set_label($msg['explnum_licence_new']);
        } else {
            $interface_form->set_label($msg['explnum_licence_edit']);
        }
        $interface_form->set_object_id($this->id)
            ->set_url_base($base_path . "/admin.php?categ=docnum&sub=licence")
            ->set_confirm_delete_msg($msg['explnum_licence_confirm_delete'])
            ->set_content_form($this->get_content_form())
            ->set_table_name('explnum_licence');
        return $interface_form->get_display();
    }

    public function set_properties_from_form(){
        global $explnum_licence_uri, $explnum_licence_label;

        $this->uri = stripslashes($explnum_licence_uri);
        $this->label = stripslashes($explnum_licence_label);
    }

	public function save(){
        $query = '';
        $clause = '';
        if ($this->id) {
            $query .= 'update ';
            $clause = ' where id_explnum_licence = ' . $this->id;
        } else {
            $query .= 'insert into ';
        }

        $query .= 'explnum_licence set
				explnum_licence_label = "' . addslashes($this->label) . '",
				explnum_licence_uri = "' . addslashes($this->uri) . '"';
        $query .= $clause;

        pmb_mysql_query($query);

        if (! $this->id) {
            $this->id = pmb_mysql_insert_id();
        }

        $translation = new translation($this->id, 'explnum_licence');
        $translation->update_small_text('explnum_licence_label');
        $translation->update_small_text('explnum_licence_uri');
    }

	public function fetch_data() {
        if (! $this->id) {
            return false;
        }
        $query = 'select explnum_licence_label, explnum_licence_uri from explnum_licence where id_explnum_licence = ' . $this->id;
        $result = pmb_mysql_query($query);
        $row = pmb_mysql_fetch_assoc($result);
        if (count($row)) {
            $this->label = $row['explnum_licence_label'];
            $this->uri = $row['explnum_licence_uri'];
        }
    }

	public function delete($force = false) {
        if (! $this->id) {
            return false;
        }
        $this->get_profiles();
        $this->get_rights();

        if ($force || ! $this->is_used()) {
            foreach ($this->rights as $right) {
                $right->delete($force);
            }
            foreach ($this->profiles as $profile) {
                $profile->delete($force);
            }
            pmb_mysql_query('delete from explnum_licence where id_explnum_licence = ' . $this->id);

            translation::delete($this->id, 'explnum_licence');
            return true;
        }
        return false;
    }

	public function is_used() {
        $this->get_profiles();
        foreach ($this->profiles as $profile) {
            if ($profile->is_used()) {
                return true;
            }
        }
        return false;
    }

	public function get_settings_menu() {
        global $admin_explnum_licence_settings_menu;

        return str_replace('!!id!!', $this->id, $admin_explnum_licence_settings_menu);
    }

    public function get_profiles_list() {
        return list_configuration_explnum_licence_profiles_ui::get_instance(array(
            'licence' => $this->id
        ))->get_display_list();
    }

    public function get_rights_list() {
        return list_configuration_explnum_licence_rights_ui::get_instance(array(
            'licence' => $this->id
        ))->get_display_list();
    }

    public function get_rights() {
        if (isset($this->rights)) {
            return $this->rights;
        }
        $this->rights = array();
        $query = 'select id_explnum_licence_right from explnum_licence_rights where explnum_licence_right_explnum_licence_num = "' . $this->id . '" order by explnum_licence_right_label';
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $this->rights[$row['id_explnum_licence_right']] = new explnum_licence_right($row['id_explnum_licence_right']);
            }
        }
        return $this->rights;
    }

    public static function get_licence_selector($selected = array()) {
        global $explnum_licence_selector, $explnum_licence_selector_script;
        $query = 'select id_explnum_licence, explnum_licence_label from explnum_licence order by explnum_licence_label';
        $result = pmb_mysql_query($query);
        $explnum_licence_list = array();
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $explnum_licence_list[] = $row;
            }
        }
        if (! count($explnum_licence_list)) {
            return '';
        }
        if (! count($selected)) {
            $selected = array(
                [
                    0
                ]
            );
        }
        $licence_ids = array_keys($selected);

        $final_template = '';
        $html = '';

        $cpt = 0;
        $final_template .= '<input class="bouton" value="+" id="add_licence_selector" type="button"><br/>';
        if (is_array($licence_ids) && count($licence_ids)) {
            for ($i = 0; $i < count($licence_ids); $i ++) {
                $explnum_licence = new explnum_licence($licence_ids[$i]);
                for ($j = 0; $j < count($selected[$licence_ids[$i]]); $j ++) {

                    $html = $explnum_licence_selector;
                    $options = '';
                    foreach ($explnum_licence_list as $row) {
                        $options .= '<option ' . ($licence_ids[$i] == $row['id_explnum_licence'] ? ' selected="selected" ' : "") . ' value="' . $row['id_explnum_licence'] . '" >' . $row['explnum_licence_label'] . '</option>';
                    }
                    $html = str_replace('!!explnum_licence_selector_options!!', $options, $html);
                    $profile_form_list = $explnum_licence->get_profiles_form_list(array(
                        $selected[$licence_ids[$i]][$j]
                    ), $cpt);
                    $html = str_replace('!!selector_index!!', $cpt, $html);
                    $html = str_replace('!!explnum_licence_profiles!!', $profile_form_list, $html);
                    $final_template .= $html;
                    $cpt ++;
                }
            }
        }

        $final_template .= $explnum_licence_selector_script;
        return $final_template;
    }

    public function get_profiles_form_list($selected = array(), $selector_index = 0) {
        global $explnum_licence_profiles_form_list_item, $charset;

        $selector_index = intval($selector_index);
        $html = '';
        $this->get_profiles();
        if (count($this->profiles)) {
            foreach ($this->profiles as $profile) {
                $current_profile = $explnum_licence_profiles_form_list_item;
                $current_profile = str_replace('!!explnum_licence_profile_id!!', htmlentities($profile->get_id(), ENT_QUOTES, $charset), $current_profile);
                $current_profile = str_replace('!!explnum_licence_profile_logo_url!!', htmlentities($profile->get_logo_url(), ENT_QUOTES, $charset), $current_profile);
                $current_profile = str_replace('!!explnum_licence_profile_label!!', htmlentities($profile->get_label(), ENT_QUOTES, $charset), $current_profile);
                $is_checked = (in_array($profile->get_id(), $selected) || (empty($selected) && empty($html)));
                $current_profile = str_replace('!!explnum_licence_profile_selected!!', ($is_checked ? ' checked="checked" ' : ''), $current_profile);
                $current_profile = str_replace('!!explnum_licence_profile_selector_index!!', $selector_index, $current_profile);

                $html .= $current_profile;
            }
        }
        return $html;
    }

    public static function save_explnum_licence_profiles($explnum_id, $explnum_licence_profiles_parameters = array()) {
        $explnum_id = intval($explnum_id);
        if (! $explnum_id) {
            return;
        }
        global $explnum_licence_profiles;
        if (! count($explnum_licence_profiles_parameters) && isset($explnum_licence_profiles)) {
            $explnum_licence_profiles_parameters = $explnum_licence_profiles;
        }
        $explnum_licence_profiles_parameters = array_unique($explnum_licence_profiles_parameters);
        pmb_mysql_query('delete from explnum_licence_profile_explnums where explnum_licence_profile_explnums_explnum_num = ' . $explnum_id);
        $query = '';
        foreach ($explnum_licence_profiles_parameters as $profile_id) {
            if ($query) {
                $query .= ',';
            }
            $query .= ' (' . $explnum_id . ', ' . $profile_id . ') ';
        }

        if ($query) {
            $query = 'insert into explnum_licence_profile_explnums (explnum_licence_profile_explnums_explnum_num, explnum_licence_profile_explnums_profile_num) values ' . $query;
            pmb_mysql_query($query);
        }
    }

    public static function get_explnum_licence_profiles($explnum_id) {
        $explnum_id = intval($explnum_id);
        $ids = array();

        if (! $explnum_id) {
            return $ids;
        }
        $result = pmb_mysql_query('
			select explnum_licence_profile_explnums.explnum_licence_profile_explnums_profile_num, explnum_licence_profiles.explnum_licence_profile_explnum_licence_num
			from explnum_licence_profile_explnums join explnum_licence_profiles
			on explnum_licence_profiles.id_explnum_licence_profile = explnum_licence_profile_explnums.explnum_licence_profile_explnums_profile_num
			where explnum_licence_profile_explnums.explnum_licence_profile_explnums_explnum_num = ' . $explnum_id);
        $ids = array();
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                if (! isset($ids[$row['explnum_licence_profile_explnum_licence_num']])) {
                    $ids[$row['explnum_licence_profile_explnum_licence_num']] = array();
                }
                $ids[$row['explnum_licence_profile_explnum_licence_num']][] = $row['explnum_licence_profile_explnums_profile_num'];
            }
        }
        return $ids;
    }

    public static function delete_explnum_licence_profiles($explnum_id) {
        $explnum_id = intval($explnum_id);
        if ($explnum_id) {
            pmb_mysql_query('delete from explnum_licence_profile_explnums where explnum_licence_profile_explnums_explnum_num = ' . $explnum_id);
        }
    }

    public function get_profiles() {
        if (isset($this->profiles)) {
            return $this->profiles;
        }
        $this->profiles = array();
        $query = 'select id_explnum_licence_profile from explnum_licence_profiles where explnum_licence_profile_explnum_licence_num = "' . $this->id . '" order by explnum_licence_profile_label';
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $this->profiles[$row['id_explnum_licence_profile']] = new explnum_licence_profile($row['id_explnum_licence_profile']);
            }
        }
        return $this->profiles;
    }

    public function get_label() {
        if (! isset($this->label)) {
            $this->fetch_data();
        }
        return $this->label ?? '';
    }

    public function get_uri() {
        if (! isset($this->uri)) {
            $this->fetch_data();
        }
        return $this->uri ?? '';
    }

    public static function get_explnum_licence_picto($explnum_id) {
        if (! $explnum_id) {
            return '';
        }
        global $explnum_licence_info_picto, $explnum_licence_script_dialog;
        $html = '';
        $profiles = self::get_explnum_licence_profiles($explnum_id);
        if (! count($profiles)) {
            return $html;
        }
        if (! self::$script_included) {
            $html = $explnum_licence_script_dialog;
            self::$script_included = true;
        }
        $html .= str_replace('!!explnum_id!!', $explnum_id, $explnum_licence_info_picto);
        return $html;
    }

    public static function get_explnum_licence_details($explnum_id) {
        if (! $explnum_id) {
            return '';
        }
        global $msg;
        global $charset;
        global $explnum_licence_profile_details, $explnum_licence_right_details;
        $html = '';
        $profiles = self::get_explnum_licence_profiles($explnum_id);
        if (! count($profiles)) {
            return $html;
        }
        foreach ($profiles as $licence_id => $profiles_id) {
            $explnum_licence = new explnum_licence($licence_id);
            foreach ($profiles_id as $profile_id) {
                $profile_detail = str_replace('!!explnum_licence_label!!', htmlentities($explnum_licence->get_label(), ENT_QUOTES, $charset), $explnum_licence_profile_details);
                $profile_detail = str_replace('!!explnum_licence_uri!!', htmlentities($explnum_licence->get_uri(), ENT_QUOTES, $charset), $profile_detail);

                $profile = new explnum_licence_profile($profile_id);
                $profile_detail = str_replace('!!explnum_licence_profile_label!!', htmlentities($profile->get_label(), ENT_QUOTES, $charset), $profile_detail);
                $profile_detail = str_replace('!!explnum_licence_profile_uri!!', htmlentities($profile->get_uri(), ENT_QUOTES, $charset), $profile_detail);
                $profile_detail = str_replace('!!explnum_licence_profile_logo_url!!', htmlentities($profile->get_logo_url(), ENT_QUOTES, $charset), $profile_detail);

                $profile_detail = str_replace('!!explnum_licence_profile_image!!', $profile->get_logo_url() ? "<img style='height:30px;' src='" . $profile->get_logo_url() . "' alt='" . htmlentities($profile->get_label(), ENT_QUOTES, $charset) . "'/>" : '', $profile_detail);
                $profile_detail = str_replace('!!explnum_licence_profile_explanation!!', nl2br(htmlentities($profile->get_explanation(), ENT_QUOTES, $charset)), $profile_detail);
                $prohibitions = $authorizations = '';
                foreach ($profile->get_rights() as $right) {
                    $right_detail = str_replace('!!explnum_licence_right_image!!', $right->get_logo_url() ? "<img style='height:30px;' src='" . $right->get_logo_url() . "' alt='" . htmlentities($right->get_label(), ENT_QUOTES, $charset) . "' />" : '', $explnum_licence_right_details);
                    $right_detail = str_replace('!!explnum_licence_right_label!!', htmlentities($right->get_label(), ENT_QUOTES, $charset), $right_detail);
                    $right_detail = str_replace('!!explnum_licence_right_explanation!!', htmlentities($right->get_explanation(), ENT_QUOTES, $charset), $right_detail);
                    if ($right->get_type()) {
                        $authorizations .= $right_detail;
                    } else {
                        $prohibitions .= $right_detail;
                    }
                }
                $explnum_licence_rights_details = '';
                if ($prohibitions) {
                    $explnum_licence_rights_details .= '<h4>' . $msg['explnum_licence_right_prohibitions'] . '</h4>';
                    $explnum_licence_rights_details .= $prohibitions;
                }
                if ($authorizations) {
                    $explnum_licence_rights_details .= '<h4>' . $msg['explnum_licence_right_authorisations'] . '</h4>';
                    $explnum_licence_rights_details .= $authorizations;
                }
                $profile_detail = str_replace('!!explnum_licence_rights_details!!', $explnum_licence_rights_details, $profile_detail);
                $html .= $profile_detail;
            }
        }
        return $html;
    }

    public static function get_explnum_licence_as_pdf($explnum_id)
    {
        if (! $explnum_id) {
            return '';
        }
        global $class_path;
        global $explnum_licence_pdf_container_template;

        $template = str_replace('!!explnum_licence_profiles_details!!', self::get_explnum_licence_details($explnum_id), $explnum_licence_pdf_container_template);

        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html2pdf->writeHTML($template);
        $html2pdf->output('licence_' . $explnum_id . '.pdf');
    }

    public static function get_explnum_licence_tooltip($explnum_id) {
        if (! $explnum_id) {
            return '';
        }
        $html = '';
        $profiles = self::get_explnum_licence_profiles($explnum_id);
        if (! count($profiles)) {
            return $html;
        }
        foreach ($profiles as $licence_id => $profiles_id) {
            $explnum_licence = new explnum_licence($licence_id);
            $html .= $explnum_licence->get_label() . '<br/>';
            foreach ($profiles_id as $profile_id) {
                $profile = new explnum_licence_profile($profile_id);
                $html .= ($profile->get_logo_url() ? '<img src="' . $profile->get_logo_url() . '" height="30px;"/><br/>' : '') . $profile->get_label() . '<br/>';
            }
        }
        return $html;
    }

    public static function get_explnum_licence_quotation($explnum_id) {
        global $explnum_licence_profile_quotation;

        if (! $explnum_id) {
            return '';
        }
        $html = '';
        $profiles = self::get_explnum_licence_profiles($explnum_id);
        if (! count($profiles)) {
            return $html;
        }
        foreach ($profiles as $profiles_id) {
            foreach ($profiles_id as $profile_id) {
                $profile = new explnum_licence_profile($profile_id);
                $html .= str_replace('!!profile_quotation!!', $profile->get_quotation_rights_for_explnum($explnum_id), $explnum_licence_profile_quotation);
            }
        }
        return $html;
    }
}