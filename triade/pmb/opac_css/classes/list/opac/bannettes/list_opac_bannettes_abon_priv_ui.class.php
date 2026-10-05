<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_bannettes_abon_priv_ui.class.php,v 1.3 2024/04/23 12:24:01 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_bannettes_abon_priv_ui extends list_opac_bannettes_abon_ui {
	
// 	protected function get_title() {
// 		global $msg;
		
// 		return "<h3><span>".$msg['dsi_bannette_gerer_priv']."</span></h3>\n";
// 	}
	
    /**
     * Initialisation des colonnes disponibles
     */
    protected function init_available_columns() {
        parent::init_available_columns();
        $this->available_columns['main_fields']['actions'] = 'dsi_bannette_gerer_actions';
    }
    
    protected function init_default_columns() {
        parent::init_default_columns();
        $this->add_column('actions');
    }
    
    protected function init_default_settings() {
        parent::init_default_settings();
        $this->set_setting_column('actions', 'exportable', false);
    }
    
    protected function get_cell_content($object, $property) {
        global $msg, $charset, $base_path;
        global $opac_allow_resiliation;
        
        $content = '';
        switch($property) {
            case 'subscribed':
                if (!$opac_allow_resiliation && count($object->categorie_lecteurs)) {
                    $content .= "\n<input type='checkbox' name='dummy[]' value='' disabled />";
                    $content .= "<input type='hidden' name='bannette_abon[".$object->id_bannette."]' value='1' style='display:none'/>";
                } else {
                    $content .= "
                    <label for='bannette_abon_".$object->id_bannette."' class='visually-hidden'>".htmlentities($msg['list_ui_selection_checkbox_bannette'],ENT_QUOTES,$charset) . $object->comment_public."</label>
                    <input type='checkbox' id='bannette_abon_".$object->id_bannette."' name='bannette_abon[".$object->id_bannette."]' value='1' title='".htmlentities($msg['list_ui_selection_checkbox'],ENT_QUOTES,$charset)."' />";
                }
                break;
            case 'actions':
                $content .= "<a href='".$base_path."/empr.php?tab=dsi&lvl=bannette_edit&id_bannette=".$object->id_bannette."' style='cursor : pointer'>
                    <img src='".get_url_icon('tag.png')."' alt='' title='".htmlentities($msg['edit'],ENT_QUOTES,$charset)."' />
                    <span class='visually-hidden'> ".htmlentities($msg['list_ui_bannette_edit_btn'],ENT_QUOTES,$charset) . $object->comment_public."</span>
                </a>";
                break;
            default :
                $content .= parent::get_cell_content($object, $property);
                break;
        }
        return $content;
    }
}