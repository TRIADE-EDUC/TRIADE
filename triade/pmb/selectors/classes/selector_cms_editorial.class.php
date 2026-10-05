<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_cms_editorial.class.php,v 1.1.4.1 2025/01/16 10:24:12 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $base_path, $class_path;

class selector_cms_editorial extends selector
{

    public function __construct($user_input = '')
    {
        parent::__construct($user_input);
        $this->objects_type = TYPE_CMS_EDITORIAL;
    }

    public function proceed()
    {
        global $action;

        $entity_form = '';

        switch ($action) {
            case 'simple_search':
                $entity_form = $this->get_simple_search_form();
                break;
            case 'advanced_search':
                $entity_form = $this->get_advanced_search_form();
                break;
            default:
                print $this->get_sel_header_template();
                print $this->get_js_script();
                print $this->get_sel_footer_template();
                print $this->get_sub_tabs();
                break;
        }
        if ($entity_form) {
            header("Content-Type: text/html; charset=UTF-8");
            print encoding_normalize::utf8_normalize($entity_form);
        }
    }

    protected function get_searcher_tabs_instance()
    {
        if (! isset($this->searcher_tabs_instance)) {
            $this->searcher_tabs_instance = new searcher_selectors_tabs('cms_editorial');
        }
        return $this->searcher_tabs_instance;
    }

    protected function get_search_fields_filtered_objects_types()
    {
        return array();
    }

    protected function get_search_instance()
    {
        $search = new search(false,"search_fields_cms_editorial");
        $search->add_context_parameter('in_selector', true);
        return $search;
    }

    protected function get_search_perso_instance($id = 0)
    {
        return new search_perso($id);
    }

    public function get_segment_type()
    {
        if (! empty($this->segment_type)) {
            return $this->segment_type;
        }

        $this->segment_type = TYPE_CMS_ARTICLE;
        if ($this->num_segment) {
            $query = "
			    SELECT search_segment_type
			    FROM search_segments
			    WHERE id_search_segment = '" . $this->num_segment . "'
			";
            $result = pmb_mysql_query($query);

            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_assoc($result);
                $this->segment_type = $row['search_segment_type'];
            }
        }
        return $this->segment_type;
    }
}
