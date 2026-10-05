<?php
use Pmb\Animations\Orm\AnimationOrm;
use Pmb\Animations\Opac\Models\AnimationModel;
use Pmb\Animations\Opac\Models\RegistrationModel;
use Pmb\Animations\Opac\Controller\AnimationsController;

// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_animations_list_ui.class.php,v 1.1.6.1 2025/04/07 15:18:01 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_list_ui.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste rubrique du contenu éditorial
 * @author ngantier
 *
 */
class elements_animations_list_ui extends elements_list_ui {

    protected function generate_elements_list(){
        $elements_list = '';
        foreach($this->contents as $element_id){
            $elements_list.= $this->generate_element($element_id);
        }
        return $elements_list;
    }

    protected function generate_element($element_id, $recherche_ajax_mode=0){
        $empr_id = intval($_SESSION['id_empr_session']);
        $animation_controller = new AnimationsController();
        $context = $animation_controller->getAnimationRenderContext($element_id, $empr_id);
        return static::render($this->get_template_path(),$context);
    }
    
    private function get_template_path() {
        global $opac_authorities_templates_folder, $include_path;
        
        $template_directory = $opac_authorities_templates_folder ?? "common";
        switch (true) {
            case file_exists($include_path.'/templates/animations/'.$template_directory.'/animation_in_result_display_subst.tpl.html'):
                return $include_path.'/templates/animations/'.$template_directory.'/animation_in_result_display_subst.tpl.html';
            case file_exists($include_path.'/templates/animations/'.$template_directory.'/animation_in_result_display.tpl.html'):
                return $include_path.'/templates/animations/'.$template_directory.'/animation_in_result_display.tpl.html';
            case file_exists($include_path.'/templates/animations/common/animation_in_result_display_subst.tpl.html'):
                return $include_path.'/templates/animations/common/animation_in_result_display_subst.tpl.html';
            case file_exists($include_path.'/templates/animations/common/animation_in_result_display.tpl.html'):
                return $include_path.'/templates/animations/common/animation_in_result_display.tpl.html';
        }
        return "";
    }
}