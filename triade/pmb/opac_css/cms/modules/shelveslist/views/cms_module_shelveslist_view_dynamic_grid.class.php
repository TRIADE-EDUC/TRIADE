<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_shelveslist_view_dynamic_grid.class.php,v 1.3 2023/04/21 06:42:48 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($include_path . "/h2o/h2o.php");

class cms_module_shelveslist_view_dynamic_grid extends cms_module_common_view_dynamic_grid
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
    {% for shelve in shelves %}
        <div data-filter-id="{{ shelve.id }}" data-filter-title="{{ shelve.name }}" class="uk-width-1-3">
                <div class="uk-panel-box">
                    <p>{{ shelve.comment }}</p>
                    {% if shelve.link_rss %}
                        <a href="{{ shelve.link_rss }}" alt="{{ shelve.name }}">Flux RSS</a>
                    {% endif %}
                </div>
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
                <label for='cms_module_common_view_django_template_record_content'>" . $this->format_text($this->msg['cms_module_common_view_django_template_record_content']) . "</label>
            </div>
            <div class='colonne-suite'>
                " . notice_tpl::gen_tpl_select("cms_module_shelveslist_view_django_used_template", $this->parameters['used_template']) . "
            </div>
            </div>
        </div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_recordslist_view_link'>" . $this->format_text($this->msg['cms_module_selveslist_view_link']) . "</label>
			</div>
			<div class='colonne-suite'>
                " . $this->get_constructor_link_form("notice") . "
			</div>
		</div>";
        return $form;
    }

    public function save_form()
    {
        global $cms_module_shelveslist_view_django_used_template;
        $this->parameters['used_template'] = $cms_module_shelveslist_view_django_used_template;
        $this->save_constructor_link_form("notice");
        return parent::save_form();
    }

    public function render($datas)
    {
        global $opac_etagere_notices_format;
        global $opac_notice_affichage_class;
        
        $shelves = array();

        if (! $opac_notice_affichage_class) {
            $opac_notice_affichage_class = "notice_affichage";
        }

        if (! $this->parameters["nb_notices"]) {
            $this->parameters["nb_notices"] = 0;
        }

        foreach ($datas["shelves"] as $shelve) {
            
            $content = "";
            $notices = get_etagere_notices($shelve['id'], $this->parameters["nb_notices"]);
            foreach ($notices as $idnotice => $niveau_biblio) {
                if ($this->parameters['used_template']) {
                    $tpl = notice_tpl_gen::get_instance($this->parameters['used_template']);
                    $content .= $tpl->build_notice($idnotice);
                } else {
                    $content .= aff_notice($idnotice, 0, 1, 0, $opac_etagere_notices_format, AFF_ETA_NOTICES_DEPLIABLES_OUI, 0, 1, 0, 1, $this->parameters['django_directory']);
                }
            }
            
            $shelve['records'] = $content;
            $shelve['cart_link'] = $this->get_constructed_link('shelve_to_cart', $shelve['id']);
            $shelves[] = $shelve;
        }

        return parent::render(array('shelves' => $shelves));
    }

    public function get_format_data_structure()
    {
        $format_datas = array(
            array(
                'var' => "shelves",
                'desc' => $this->msg['cms_module_shelveslist_view_desc'],
                'children' => array(
                    array(
                        'var' => "shelves[i].id",
                        'desc' => $this->msg['cms_module_shelveslist_view_id_desc']
                    ),
                    array(
                        'var' => 'shelves[i].cart_link',
                        'desc' => $this->msg['cms_module_shelveslist_view_name_desc']
                    ),
                    array(
                        'var' => "shelves[i].name",
                        'desc' => $this->msg['cms_module_shelveslist_view_link_desc']
                    ),
                    array(
                        'var' => "shelves[i].link",
                        'desc' => $this->msg['cms_module_shelveslist_view_link_rss_desc']
                    ),
                    array(
                        'var' => "shelves[i].link_rss",
                        'desc' => $this->msg['cms_module_shelveslist_view_link_rss_desc']
                    ),
                    array(
                        'var' => "shelves[i].comment",
                        'desc' => $this->msg['cms_module_shelveslist_view_comment_desc']
                    ),
                    array(
                        'var' => "shelves[i].records",
                        'desc' => $this->msg['cms_module_shelveslist_view_records_desc']
                    )
                )
            )
        );
        $format_datas = array_merge($format_datas, parent::get_format_data_structure());
        return $format_datas;
    }
}