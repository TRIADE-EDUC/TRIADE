<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_dynamic_grid.class.php,v 1.3 2023/08/17 09:47:55 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");
require_once ($include_path . "/h2o/h2o.php");

class cms_module_common_view_dynamic_grid extends cms_module_common_view_django
{

    /**
     * Valeur par défaut definis dans la Doc de Uikit
     * @see https://getuikit.com/v2/docs/grid-js.html
     * 
     * @var array
     */
    private const DEFAULT_OPTIONS = array(
        "colwidth" => "auto",
        "animation" => true,
        "duration" => 200,
        "gutter" => 0,
        "controls" => false
    );

    public function __construct($id = 0)
    {
        $this->use_jquery = true;
        parent::__construct($id);
        $this->default_template = '
<ul id="dynamic_grid_control_{{ id }}" class="uk-subnav uk-subnav-pill">
    <li data-uk-sort="filter-id:asc"><a href="#">'.$this->msg['cms_module_common_view_django_default_template_id'].'</a></li>
    <li data-uk-sort="filter-id:desc"><a href="#">'.$this->msg['cms_module_common_view_django_default_template_id_desc'].'</a></li>
    <li data-uk-sort="filter-title:asc"><a href="#">'.$this->msg['cms_module_common_view_django_default_template_title'].'</a></li>
    <li data-uk-sort="filter-title:desc"><a href="#">'.$this->msg['cms_module_common_view_django_default_template_title_desc'].'</a></li>
</ul>
                
<div id="dynamic_grid_{{ id }}" class="uk-grid" data-filter-id="dynamic_grid_control_{{ id }}">
    {% for record in records %}
        <div data-filter-id="{{ record.id }}" data-filter-title="{{ record.title }}" class="uk-width-1-3">
            <a href="{{ record.link }}" alt="{{ record.title }}">
                <div class="uk-panel-box">
                    <p>{{ record.title }}</p>
				    {% if record.vign %}<img src="{{ record.vign }}"/>{% endif %}
                </div>
			</a>
        </div>
    {% endfor %}
</div>';
    }

    public function get_form()
    {
        if (! isset($this->parameters["used_template"])) {
            $this->parameters["used_template"] = "";
        }
        if (! isset($this->parameters["colwidth"])) {
            $this->parameters["colwidth"] = 0;
        }
        if (! isset($this->parameters["animation"])) {
            $this->parameters["animation"] = self::DEFAULT_OPTIONS['animation'];
        }
        if (! isset($this->parameters["duration"])) {
            $this->parameters["duration"] = self::DEFAULT_OPTIONS['duration'];
        }
        if (! isset($this->parameters["gutter"])) {
            $this->parameters["gutter"] = self::DEFAULT_OPTIONS['gutter'];
        }

        $general_form = "
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_dynamic_grid_colwidth'>" . $this->format_text($this->msg['cms_module_common_view_dynamic_grid_colwidth']) . "</label>
				</div>
				<div class='colonne-suite'>
					<input type='number' name='cms_module_common_view_dynamic_grid_colwidth' value='" . $this->parameters['colwidth'] . "' min='0'/>&nbsp;" . $this->format_text($this->msg['cms_module_common_view_dynamic_grid_colwidth_defaut']) . "&nbsp;
				</div>
			</div>

		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_dynamic_grid_animation'>" . $this->format_text($this->msg['cms_module_common_view_dynamic_grid_animation']) . "</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_dynamic_grid_animation' value='0' " . ($this->parameters['animation'] == false ? "checked='checked'" : "") . "/>&nbsp;" . $this->format_text($this->msg['cms_module_common_view_dynamic_grid_no']) . "&nbsp;
					<input type='radio' name='cms_module_common_view_dynamic_grid_animation' value='1' " . ($this->parameters['animation'] == true ? "checked='checked'" : "") . "/>&nbsp;" . $this->format_text($this->msg['cms_module_common_view_dynamic_grid_yes']) . "&nbsp;
				</div>
			</div>

		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_dynamic_grid_duration'>" . $this->format_text($this->msg['cms_module_common_view_dynamic_grid_duration']) . "</label>
				</div>
				<div class='colonne-suite'>
					<input type='number' name='cms_module_common_view_dynamic_grid_duration' value='" . $this->parameters['duration'] . "' min='0'/>
				</div>
			</div>

		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_dynamic_grid_gutter'>" . $this->format_text($this->msg['cms_module_common_view_dynamic_grid_gutter']) . "</label>
				</div>
				<div class='colonne-suite'>
					<input type='number' name='cms_module_common_view_dynamic_grid_gutter' value='" . $this->parameters['gutter'] . "' min='0'/>
				</div>
			</div>
        ";

        $form = gen_plus("general_parameters", $this->format_text($this->msg['cms_module_common_view_dynamic_grid_general_parameters']), $general_form, true);
        $form .= parent::get_form();
        return $form;
    }

    public function save_form()
    {
        global $cms_module_common_view_dynamic_grid_colwidth;
        global $cms_module_common_view_dynamic_grid_animation;
        global $cms_module_common_view_dynamic_grid_duration;
        global $cms_module_common_view_dynamic_grid_gutter;

        $this->parameters['colwidth'] = ($cms_module_common_view_dynamic_grid_colwidth == 0 ? self::DEFAULT_OPTIONS['colwith'] : intval($cms_module_common_view_dynamic_grid_colwidth));
        $this->parameters['animation'] = ($cms_module_common_view_dynamic_grid_animation == 1 ? true : false);
        if ($this->parameters['animation']) {
            $this->parameters['duration'] = intval($cms_module_common_view_dynamic_grid_duration);
        } else {
            $this->parameters['duration'] = 0;
        }
        $this->parameters['gutter'] = intval($cms_module_common_view_dynamic_grid_gutter);

        return parent::save_form();
    }

    public function render($datas)
    {
        $html2return = "";
        $html2return = parent::render($datas);
        $html2return .= $this->get_script();
        return $html2return;
    }

    public function get_script()
    {
        return "
            <script>
                jQuery(document).ready(function() {
                    var gridNode = document.getElementById('dynamic_grid_" . $this->get_module_dom_id() . "');
                    if (gridNode && typeof UIkit.grid == 'function' ) {
                        
                        var filterId = false;
                        if (gridNode.hasAttribute('data-filter-id') && gridNode.attributes['data-filter-id'].value.length > 0) {
                            var value = gridNode.attributes['data-filter-id'].value;
                            if (value[0] != '#') {
                                value = '#'+value;
                            }
                            filterId = value;
                        }
                        
                        var grid = UIkit.grid('#dynamic_grid_" . $this->get_module_dom_id() . "', {
                            colwidth: '" . (! empty($this->parameters['colwidth']) ? $this->parameters['colwidth'] : self::DEFAULT_OPTIONS['colwidth']) . "',
                            animation: '" . (! empty($this->parameters['animation']) ? $this->parameters['animation'] : self::DEFAULT_OPTIONS['animation']) . "',
                            duration: '" . (! empty($this->parameters['duration']) ? $this->parameters['duration'] : self::DEFAULT_OPTIONS['duration']) . "',
                            gutter: '" . (! empty($this->parameters['gutter']) ? $this->parameters['gutter'] : self::DEFAULT_OPTIONS['gutter']) . "',
                            controls: filterId,
                        });
                    }
                });
            </script>
        ";
    }
}