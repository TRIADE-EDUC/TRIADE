<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_expl_list_ui.class.php,v 1.2 2021/02/08 14:50:44 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($class_path . '/elements_list/elements_list_ui.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste d'exemplaire
 *
 * @author ngantier
 *        
 */
class elements_expl_list_ui extends elements_list_ui
{

    protected function generate_elements_list()
    {
        $elements_list = '';
        foreach ($this->contents as $element_id) {
            $elements_list .= $this->generate_element($element_id);
        }
        return $elements_list;
    }

    protected function generate_element($element_id, $recherche_ajax_mode = 0)
    {
        global $option_show_expl, $option_show_notice_fille, $base_path;
        $display = '';
        $query = 'select * from exemplaires where expl_id =' . $element_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $r = pmb_mysql_fetch_object($result);
            $link = "$base_path/catalog.php?categ=edit_expl&id=$r->expl_notice&cb=&expl_id=$r->expl_id";
            $display = new mono_display_expl('', $r->expl_id, 6, '', $option_show_expl, $link, '', '', 1, 0, 1, ! $option_show_notice_fille, "", 1);
        }
        return $display->result;
    }
}