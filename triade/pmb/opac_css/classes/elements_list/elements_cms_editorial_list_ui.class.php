<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_cms_editorial_list_ui.class.php,v 1.1.4.2 2024/11/14 15:36:50 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($class_path . '/elements_list/elements_list_ui.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste d'article du contenu éditorial
 *
 * @author ngantier
 *
 */
class elements_cms_editorial_list_ui extends elements_list_ui
{

    public $link = [];

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
        $id = explode("_", $element_id);

        if (strpos($element_id, "article")) {
            $type = entities::get_entity_name_from_type(TYPE_CMS_ARTICLE);
            $template_path = $this->get_template_path(TYPE_CMS_ARTICLE);
            $param_link = TYPE_CMS_ARTICLE;
        } else {
            $type = entities::get_entity_name_from_type(TYPE_CMS_SECTION);
            $template_path = $this->get_template_path(TYPE_CMS_SECTION);
            $param_link = TYPE_CMS_SECTION;
        }

        $page_id = $this->link[$param_link]['page'] ?? 0;
        $var = $this->link[$param_link]['var'] ?? "";

        $cms = new cms_editorial_data($id[0], $type, [
            $type => "./index.php?lvl=cmspage&pageid=$page_id&$var=!!id!!"
        ]);

        return static::render($template_path, [
            $type => $cms
        ]);
    }

    /**
     * Permet de definir les liens par type de contenu editorial
     *
     * @param array $param
     * @return void
     */
    public function set_link($param)
    {
        $this->link = $param;
    }

    private function get_template_path($type) {
        global $include_path;

        $template_directory = 'common';

        switch ($type) {
            case TYPE_CMS_SECTION:
                $type_name = "section";
                break;
            case TYPE_CMS_ARTICLE:
                $type_name = "article";
                break;
            default:
                return "";
        }

        switch (true) {
            case file_exists($include_path.'/templates/cms_editorial/'.$type_name.'/'.$template_directory.'/'.$type_name.'_in_result_display_subst.tpl.html'):
                return $include_path.'/templates/cms_editorial/'.$type_name.'/'.$template_directory.'/'.$type_name.'_in_result_display_subst.tpl.html';
            case file_exists($include_path.'/templates/cms_editorial/'.$type_name.'/'.$template_directory.'/'.$type_name.'_in_result_display.tpl.html'):
                return $include_path.'/templates/cms_editorial/'.$type_name.'/'.$template_directory.'/'.$type_name.'_in_result_display.tpl.html';
        }
        return "";
    }
}