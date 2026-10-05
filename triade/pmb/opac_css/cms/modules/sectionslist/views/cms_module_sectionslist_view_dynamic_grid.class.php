<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sectionslist_view_dynamic_grid.class.php,v 1.4.2.1 2025/01/17 10:40:42 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($include_path . "/h2o/h2o.php");

class cms_module_sectionslist_view_dynamic_grid extends cms_module_common_view_dynamic_grid
{

    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->default_template = '
<ul id="dynamic_grid_control_{{ id }}" class="uk-subnav uk-subnav-pill">
    <li data-uk-sort="filter-id:asc"><a href="#">' . $this->msg['cms_module_common_view_django_default_template_id'] . '</a></li>
    <li data-uk-sort="filter-id:desc"><a href="#">' . $this->msg['cms_module_common_view_django_default_template_id_desc'] . '</a></li>
    <li data-uk-sort="filter-title:asc"><a href="#">' . $this->msg['cms_module_common_view_django_default_template_title'] . '</a></li>
    <li data-uk-sort="filter-title:desc"><a href="#">' . $this->msg['cms_module_common_view_django_default_template_title_desc'] . '</a></li>
</ul>

<div id="dynamic_grid_{{ id }}" class="uk-grid" data-filter-id="dynamic_grid_control_{{ id }}">
    {% for section in sections %}
        <div data-filter-id="{{ section.id }}" data-filter-title="{{ section.title }}" class="uk-width-1-3">
            <a href="{{ section.link }}" alt="{{ section.title }}">
                <div class="uk-panel-box">
                    <p>{{ section.title }}</p>
				    {% if section.logo.vign %}<img src="{{ section.logo.vign }}"/>{% endif %}
                </div>
			</a>
        </div>
    {% endfor %}
</div>';
    }

    public function get_form()
    {
        $form = "";
        $form .= parent::get_form();
        $form .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_recordslist_view_link'>" . $this->format_text($this->msg['cms_module_sectionslist_view_link']) . "</label>
			</div>
			<div class='colonne-suite'>
                " . $this->get_constructor_link_form("section") . "
			</div>
		</div>";
        return $form;
    }

    public function save_form()
    {
        $this->save_constructor_link_form("section");
        return parent::save_form();
    }

    public function render($datas)
    {
        $sections = array();
        $links = [
            "article" => $this->get_constructed_link("article", "!!id!!"),
            "section" => $this->get_constructed_link("section", "!!id!!")
        ];
        $local_ids = $datas;
        if (isset($datas["sections"])) {
        	$local_ids = $datas["sections"];
        }

        if (is_countable($local_ids)) {
            $index = count($local_ids);
            for ($i = 0; $i < $index; $i++) {
                $section = new cms_section($local_ids[$i]);
                $sections[] = $section->format_datas($links);
            }
        }

        return parent::render(array('sections' => $sections));
    }

    public function get_format_data_structure()
    {
        $datas = cms_section::get_format_data_structure(false, false);
        $datas[] = array(
            'var' => "link",
            'desc' => $this->msg['cms_module_sectionslist_view_dynamic_grid_link_desc']
        );

        $format_datas = array(
            array(
                'var' => "sections",
                'desc' => $this->msg['cms_module_sectionslist_view_dynamic_grid_sections_desc'],
                'children' => $this->prefix_var_tree($datas, "sections[i]")
            )
        );
        return array_merge($format_datas, parent::get_format_data_structure());
    }
}