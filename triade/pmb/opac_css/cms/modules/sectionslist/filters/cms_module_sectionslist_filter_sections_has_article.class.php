<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sectionslist_filter_sections_has_article.class.php,v 1.3.2.1 2025/01/17 10:40:47 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_sectionslist_filter_sections_has_article extends cms_module_common_filter
{

    private const SECTION_CONTAINS_AN_ARTICLE = 1;

    private const TREE_CONTAINS_AN_ARTICLE = 0;

    public function get_filter_from_selectors()
    {
        return array();
    }

    public function get_filter_by_selectors()
    {
        return array();
    }

    /*
     * Récupération des informations en base
     */
    protected function fetch_datas()
    {
        if ($this->id) {
            $query = "SELECT id_cadre_content, cadre_content_hash, cadre_content_num_cadre, cadre_content_data FROM cms_cadre_content WHERE id_cadre_content = '" . intval($this->id) . "'";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_object($result);
                $this->id = (int) $row->id_cadre_content;
                $this->hash = $row->cadre_content_hash;
                $this->cadre_parent = (int) $row->cadre_content_num_cadre;
                $this->unserialize($row->cadre_content_data);
            }
        }
    }

    public function get_form()
    {
        $form = "";
        $form = $this->get_hash_form();
        $form .= "<input type='hidden' name='cms_module_common_module_filters[]' value='$this->class_name'/>";

        $name = $this->get_form_value_name("filters_choice");
        $value = 0;
        if (isset($this->parameters['filters_choice'])) {
            $value = intval($this->parameters['filters_choice']);
        }

        $form .= "
		<div class='row'>
            <div class='colonne3'>
                <label>" . $this->format_text($this->msg['cms_module_sectionslist_filter_sections_has_article_label']) . "</label>
            </div>
            <div class='colonne3'>&nbsp;</div>
            <div class='colonne-suite'>
                <input type='button' class='bouton' value='X' onclick=\"destroy_filter(this, " . $this->id . ", '" . $this->class_name . "');\"/>
            </div>
            <script type='text/javascript'>
    			if (typeof destroy_filter != 'function') {
    				function destroy_filter(node, id, class_name) {
    					dojo.xhrGet({
    						url : './ajax.php?module=cms&categ=module&elem=' + class_name + '&action=delete&id=' + id
    					});
    					var content = dijit.byId(node.parentNode.parentNode.parentNode.id);
    					if (content) {
    						content.destroyRecursive(false);
    					}
    				}
    			}
		    </script>
		</div>
		<div class='row'>
            <div class='colonne3'>&nbsp;</div>
            <div class='colonne-suite'>
		        <div class='row'>
                    <input id='" . $this->get_form_value_name("has_article_value" . self::SECTION_CONTAINS_AN_ARTICLE) . "' name='" . $name . "' type='radio' value='" . self::SECTION_CONTAINS_AN_ARTICLE . "' " . ($value == self::SECTION_CONTAINS_AN_ARTICLE ? 'checked=""' : '') . ">
                    <label for='" . $this->get_form_value_name("has_article_value" . self::SECTION_CONTAINS_AN_ARTICLE) . "' label>" . $this->format_text($this->msg['cms_module_sectionslist_filter_sections_has_article_value1']) . "</label>
                </div>
		        <div class='row'>
                    <input id='" . $this->get_form_value_name("has_article_value" . self::TREE_CONTAINS_AN_ARTICLE) . "' name='" . $name . "' type='radio' value='" . self::TREE_CONTAINS_AN_ARTICLE . "' " . ($value == self::TREE_CONTAINS_AN_ARTICLE ? 'checked=""' : '') . ">
                    <label for='" . $this->get_form_value_name("has_article_value" . self::TREE_CONTAINS_AN_ARTICLE) . "' label>" . $this->format_text($this->msg['cms_module_sectionslist_filter_sections_has_article_value0']) . "</label>
                </div>
            </div>
		</div>";

        return $form;
    }

    public function filter($datas)
    {
        if (empty($datas)) {
            return array();
        }

        switch ($this->parameters['filters_choice']) {

            case self::SECTION_CONTAINS_AN_ARTICLE:
                return $this->filtred_sections($datas);

            case self::TREE_CONTAINS_AN_ARTICLE:
                return $this->filtred_sections_in_tree($datas);

            default:
                return $datas;
        }
    }

    /**
     * On retourne les rubriques qui contient au moins un article
     *
     * @param array $ids_section
     * @return number[]
     */
    private function filtred_sections(array $ids_section)
    {
        $filtered_datas = array();

        $index = is_countable($ids_section) ? count($ids_section) : 0;
        for ($i = 0; $i < $index; $i ++) {
            $is_section = intval($ids_section[$i]);
            $cms_editorial_data = new cms_editorial_data($is_section, "section");
            $articles = $cms_editorial_data->get_articles();
            if (! empty($articles)) {
                $filtered_datas[] = $is_section;
            }
        }

        return $filtered_datas;
    }

    /**
     * On retourne les rubriques qui possèdent un article sur elles-mêmes ou dans les sous-rubriques
     *
     * @param array $section
     * @return number[]
     */
    private function filtred_sections_in_tree(array $ids_section)
    {
        $filtered_datas = array();

        $index = is_countable($ids_section) ? count($ids_section) : 0;
        for ($i = 0; $i < $index; $i ++) {

            $id_section = intval($ids_section[$i]);
            $cms_editorial_data = new cms_editorial_data($id_section, "section");

            $has_article = false;
            if (! empty($cms_editorial_data->get_articles())) {
                $has_article = true;
            } else {
                $children = $cms_editorial_data->get_children();
                if (!empty($children)) {
                    $has_article = $this->children_has_article($children);
                }
            }
            if ($has_article) {
                $filtered_datas[] = $id_section;
            }
        }

        return $filtered_datas;
    }

    /**
     * Les sous-rubriques contiennent au moins un article
     *
     * @param array $children
     * @return boolean
     */
    private function children_has_article(array $children)
    {
        $index = is_countable($children) ? count($children) : 0;
        for ($i = 0; $i < $index; $i++) {
            /**
             * @var cms_editorial_data $cms_editorial_data
             */
            $cms_editorial_data = $children[$i];
            if (! empty($cms_editorial_data->get_articles())) {
                return true;
            } else {
                $has_article = $this->children_has_article($cms_editorial_data->get_children());
                if ($has_article) {
                    return $has_article;
                }
            }
        }
        return false;
    }

    public function save_form()
    {
        $this->parameters['filters_choice'] = $this->get_value_from_form("filters_choice");
        $this->get_hash();

        if ($this->id) {
            $query = "UPDATE cms_cadre_content SET";
            $clause = " WHERE id_cadre_content=" . $this->id;
        } else {
            $query = "INSERT INTO cms_cadre_content SET";
            $clause = "";
        }

        $query .= "
			cadre_content_hash = '" . $this->hash . "',
			cadre_content_type = 'filter',
			cadre_content_object = '" . $this->class_name . "'," . ($this->cadre_parent ? "cadre_content_num_cadre = '" . $this->cadre_parent . "'," : "") . "
			cadre_content_data = '" . addslashes($this->serialize()) . "'
			" . $clause;
        $result = pmb_mysql_query($query);

        if ($result) {
            if (! $this->id) {
                $this->id = pmb_mysql_insert_id();
            }
            return true;
        } else {
            $this->delete_hash();
            return false;
        }
    }
}