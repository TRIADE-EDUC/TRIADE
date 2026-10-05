<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_animations_list_ui.class.php,v 1.1 2023/04/18 15:04:14 gneveu Exp $

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
        $display='';
        $query = 'select * from anim_animations where id_animation ='.$element_id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $r = pmb_mysql_fetch_object($result);
            $display='<div class="notice-parent"><span class="notice-heada"><a href="./index.php?lvl=animation_see&id='.$element_id.'">'.$r->name.'</a></span></br></div>';
        }
        return $display;
    }
}