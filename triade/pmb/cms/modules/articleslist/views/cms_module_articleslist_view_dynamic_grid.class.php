<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_articleslist_view_dynamic_grid.class.php,v 1.4.2.1 2025/01/21 15:29:47 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once ($include_path . "/h2o/h2o.php");

class cms_module_articleslist_view_dynamic_grid extends cms_module_common_view_dynamic_grid
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
    {% for article in articles %}
        <div data-filter-id="{{ article.id }}" data-filter-title="{{ article.title }}" class="uk-width-1-3">
            <a href="{{ article.link }}" alt="{{ article.title }}">
                <div class="uk-panel-box">
                    <p>{{ article.title }}</p>
				    {% if article.logo.vign %}<img src="{{ article.logo.vign }}"/>{% endif %}
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
				<label for='cms_module_recordslist_view_link'>" . $this->format_text($this->msg['cms_module_articleslist_view_link']) . "</label>
			</div>
			<div class='colonne-suite'>
                " . $this->get_constructor_link_form("article") . "
			</div>
		</div>";
        return $form;
    }

    public function save_form()
    {
        $this->save_constructor_link_form("article");
        return parent::save_form();
    }

    public function render($datas)
    {
        $articles = array();
        $links = array();
        $links["article"] = $this->get_constructed_link("article", "!!id!!");

        if (is_countable($datas['articles'])) {
	        $index = count($datas['articles']);
	        for ($i = 0; $i < $index; $i++) {
	        	$article = new cms_article($datas['articles'][$i]);
	            $articles[] = $article->format_datas($links);
	        }
        }
        return parent::render(array('articles' => $articles));
    }

    public function get_format_data_structure()
    {
        $datas = cms_article::get_format_data_structure("article", false);
        $datas[] = array(
            'var' => "link",
            'desc' => $this->msg['cms_module_articleslist_view_dynamic_grid_link_desc']
        );

        $format_datas = array(
            array(
                'var' => "articles",
                'desc' => $this->msg['cms_module_articleslist_view_dynamic_grid_articles_desc'],
                'children' => $this->prefix_var_tree($datas, "articles[i]")
            )
        );
        return array_merge($format_datas, parent::get_format_data_structure());
    }
}