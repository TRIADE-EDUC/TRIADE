<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_cms_editorial.class.php,v 1.2.2.2 2024/11/14 15:36:50 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path, $include_path;

class search_segment_cms_editorial
{

    /**
	 * Retourne les parametres additionnels pour le formulaire
	 *
	 * @param array $search_segments_data
	 * @return string
	 */
    public static function get_additional($search_segments_data)
    {
        $template = "";
        $template = self::get_constructor_link_form(TYPE_CMS_SECTION, $search_segments_data);
        $template .= self::get_constructor_link_form(TYPE_CMS_ARTICLE, $search_segments_data);
        return $template;
    }

    /**
     * Retourne le formulaire de construction des liens
     *
     * @param int $type (TYPE_CMS_SECTION ou TYPE_CMS_ARTICLE)
     * @param array $search_segments_data
     * @return string
     */
    public static function get_constructor_link_form($type, $search_segments_data)
    {
        global $msg, $charset;
        $env_options = "";

        $label = TYPE_CMS_ARTICLE == $type ? "cms_editorial_link_constructor_page_articles_link_select" : "cms_editorial_link_constructor_page_sections_link_select";
        $form = "
                <div class='row'>
                    <label class='etiquette'>" . $msg[$label] . "</label>
				    <select id='cms_page_selector_" . $type . "' name='cms_page_selector_" . $type . "' onChange='changeType(" . $type . ")'>
					<option value='0'>" . $msg['cms_editorial_link_constructor_page'] . "</option>";

        $query = "select id_page, page_name from cms_pages order by 2";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $form .= "
					<option value='" . $row->id_page . "' " . (isset($search_segments_data[$type]['page']) && $row->id_page == $search_segments_data[$type]['page'] ? "selected='selected'" : "") . ">" . htmlentities($row->page_name, ENT_QUOTES, $charset) . "</option>";
                $env_options .= self::get_page_env_select($row->id_page, $search_segments_data[$type]['var']);
            }
        }
        $form .= "</select>";
        $form .= "
                <label class='etiquette'>" . $msg['cms_editorial_link_constructor_page_var'] . "</label>
                <select id='cms_page_var_selector_" . $type . "' name='cms_page_var_selector_$type'>
                    <option value='0'>" . $msg['cms_page_variables'] . "</option>
                    $env_options
                </select>
            </div>
        ";
        $form .= "
            <script type='text/javascript'>
                function changeType(type) {
                    var selectPage = document.getElementById('cms_page_selector_'+type);
                    var selectVar = document.getElementById('cms_page_var_selector_'+type);
                    selectVar.value = 0;
                    for(let i = 0; i < selectVar.options.length; i++) {
                        console.log(selectPage.value)
                        console.log(selectVar[i].getAttribute('data-type-page'))
                        if(selectPage.value == selectVar[i].getAttribute('data-type-page')) {
                            selectVar[i].style.display = '';
                        } else  {
                            selectVar[i].style.display = 'none';
                        }
                    }
                }
            </script>
        ";
        return $form;
    }

    public static function get_page_env_select($page_id, $var = "")
    {
        global $charset, $msg;

        $page = new cms_page(intval($page_id));
        $form = "";
        foreach ($page->vars as $page_var) {
            $form .= "
                <option
                    value='" . htmlentities($page_var['name'], ENT_QUOTES, $charset) . "'
                    data-type-page='$page_id'
                    " . ($page_var['name'] == $var ? "selected='selected'" : "") . "
                >
                    " . htmlentities(($page_var['comment'] != "" ? $page_var['comment'] : $page_var['name']), ENT_QUOTES, $charset) . "
                </option>";
        }
        return $form;
    }

    public static function get_properties_from_form()
    {
        $values_section = static::get_value_from_form(TYPE_CMS_SECTION);
        $values_article = static::get_value_from_form(TYPE_CMS_ARTICLE);
        return [
            TYPE_CMS_SECTION => $values_section,
            TYPE_CMS_ARTICLE => $values_article
        ];
    }

    protected static function get_value_from_form($type)
    {
        $page = "cms_page_selector_{$type}";
        $var = "cms_page_var_selector_{$type}";
        global ${$page}, ${$var};
        return [
            "page" => ${$page},
            "var" => ${$var}
        ];
    }
}