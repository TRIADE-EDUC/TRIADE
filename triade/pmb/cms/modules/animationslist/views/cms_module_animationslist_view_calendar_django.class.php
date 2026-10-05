<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_animationslist_view_calendar_django.class.php,v 1.3 2023/08/28 14:01:14 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Animations\Models\AnimationModel;

class cms_module_animationslist_view_calendar_django extends cms_module_common_view_django {
	
    public const COLOR_STATUT = "statut";
    
    public const COLOR_CALENDAR = "calendar";
    
	public function __construct($id = 0) {
		$this->use_dojo = true;
		parent::__construct($id);
		$this->default_template = "
<div>
  {{ calendar }}

  <blockquote>
    {% for legend in legends %}
      <p style='display: inline-block;margin: 0px;'>
        <span style='float:left;width:1em;height:1em;background-color:{{ legend.color }}'></span>
        <span style='float:left;'>&nbsp;{{ legend.animation }}&nbsp;&nbsp;</span>
      </p>
    {% endfor %}
  </blockquote>

  <div>
  {% for animation in animations %}
    <h3>
    {% if animation.event.startDate %}
      {% if animation.event.endDate %}
          {% if animation.event.startDate == animation.event.endDate %}
            le {{ animation.event.startDate }} {{ animation.event.startHour }}
          {% else %}
            du {{ animation.event.startDate }} au {{ animation.event.endDate }}
          {% endif %}
      {% else %}
        le {{ animation.event.startDate }} {{ animation.event.startHour }}
      {% endif %}
    {% endif %} : {{ animation.name }}
    </h3>
  {% endfor %}
  </div>
</div>";
	}

	public function get_form() {
	    if (empty($this->parameters["calendar_color"])) {
	        $this->parameters["calendar_color"] = self::COLOR_STATUT;
	    }
	    
	    $form="
        <div class='row'>
			<div class='colonne3'>
				<label for='calendar_color_statut'>
				    ".$this->format_text($this->msg['cms_module_animationslist_view_calendar_color'])."
				</label>
			</div>
			<div class='colonne-suite'>
                <input type='radio' id='calendar_color_statut'
				     value='".self::COLOR_STATUT."'
				     name='".$this->get_form_value_name("calendar_color")."'
                    ".($this->parameters["calendar_color"] == self::COLOR_STATUT ? "checked": "")."/>
                <label for='calendar_color'>
                    ".$this->format_text($this->msg['calendar_color_statut'])."
                </label>

                <input type='radio' id='calendar_color_calendar'
				     value='".self::COLOR_CALENDAR."'
				     name='".$this->get_form_value_name("calendar_color")."'
                    ".($this->parameters["calendar_color"] == self::COLOR_CALENDAR ? "checked": "")."/>
                <label for='calendar_color'>
                    ".$this->format_text($this->msg['calendar_color_calendar'])."
                </label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for=''>
				    ".$this->format_text($this->msg['cms_module_animationslist_view_calendar_link_animation'])."
				</label>
			</div>
			<div class='colonne-suite'>
		        ". $this->get_constructor_link_form('animation') ."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for=''>
		            ".$this->format_text($this->msg['cms_module_animationslist_view_calendar_link_animationslist'])."
		        </label>
			</div>
			<div class='colonne-suite'>
				 ". $this->get_constructor_link_form('animationslist') ."
			</div>
		</div>";
	    
	    $form.= parent::get_form();
		return $form;
	}
	
	public function save_form() {
	    $this->save_constructor_link_form('animation');
	    $this->save_constructor_link_form('animationslist');
	    $this->parameters['calendar_color'] = $this->get_value_from_form("calendar_color");
		return parent::save_form();
	}
	
	public function get_headers($datas = []) {
		$headers = parent::get_headers($datas);
		$headers[] = "
		<script>
			require(['dijit/dijit']);
		</script>";
		$headers[] = "
		<script>
			require(['dijit/Calendar']);
		</script>";
		$headers[] = "<link rel='stylesheet' type='text/css' href='" . $this->get_ajax_link(array('do' => 'get_css'), 'css') . "'/>";
		return $headers;
	}
	
	public function render($datas) {
	    $renderDatas = [
	        "legends" => [],
	        "animations" => [],
	        "calendar" => ""
	    ];
	    
	    $renderDatas['calendar'] = "<div id='cms_module_calendar_{$this->id}'></div>";
	    
	    $styles = [];
	    if (!empty($datas["animations"])) {
	        foreach ($datas["animations"] as $idAnimation) {
	            $animation = new AnimationModel($idAnimation);
	            $cmsData = $animation->getCmsData();
	            if (empty($animation->event->startDate) || in_array($idAnimation, $renderDatas["legends"])) {
	                continue;
	            }
	            
	            // On choisi la couleur du calendrier
	            if (self::COLOR_CALENDAR == $this->parameters["calendar_color"]) {
	                $animation->fetchCalendar();
	                $color = $animation->calendar->color;
	                $colorKey = $animation->numCalendar;
	            } else {
	                $animation->fetchStatus();
	                $color = $animation->status->color;
	                $colorKey = $animation->numStatus;
	            }
	            
	            $styles[$colorKey] = $color;
	            $renderDatas["legends"][$idAnimation] = [
	                "color" => $color,
	                "animation" => $this->format_text($animation->name)
	            ];
	            
	            $cmsData['color_key'] = $colorKey;
	            $cmsData['link '] = $this->get_constructed_link("animation", $idAnimation);
	            
	            $startTime = new DateTime($animation->event->startDate);
	            $endTime = new DateTime($animation->event->endDate);
	            $cmsData["event"]["startDateTime"] = $startTime->getTimestamp();
	            $cmsData["event"]["endDateTime"] = $endTime->getTimestamp();
	            
	            $renderDatas['animations'][] = $cmsData;
	        }
	    }
	    
        $renderDatas['calendar'] .= "<style>";
	    foreach ($styles as $colorKey => $color) {
	        $renderDatas['calendar'] .= "
				#{$this->get_module_dom_id()} td.cms_module_animationslist_animation_{$colorKey} {
					background : $color;
				}
				#{$this->get_module_dom_id()} .cms_module_animationslist_view_calendar_animationslist .cms_module_animationslist_animation_{$colorKey} {
					color : $color;
				}
	        ";
	    }
        $renderDatas['calendar'] .= "</style>";
        
        $animations = encoding_normalize::json_encode(encoding_normalize::utf8_normalize($renderDatas['animations']));
        if (empty($animations)) {
            $animations = "{}";
        }
	    
	    $singleAnimationLink = $this->get_constructed_link('animation', '!!id!!');
	    if (empty($singleAnimationLink)) {
	        $singleAnimationLink = "";
	    }
	    
	    $animationsLink = $this->get_constructed_link('animationslist', '!!date!!');
	    if (empty($animationsLink)) {
	        $animationsLink = "";
	    }
	    
	    $renderDatas['calendar'] .= "
		<script type='text/javascript'>
            require([
                'apps/pmb/cms/CmsAnimationsCalendar',
                'dojo/ready',
                'dojo/domReady!'
            ], function(Calendar, ready){
                ready(function() {
                    var calendar = new Calendar({
                        animations: {$animations},
                        singleAnimationLink: '{$singleAnimationLink}',
                        animationsLink: '{$animationsLink}'
                    }, 'cms_module_calendar_{$this->id}')
                    calendar.startup();
                });
            });
		</script>";

	    return parent::render($renderDatas);
	}
	
	public function get_format_data_structure(){
	    $formatData = (new AnimationModel())->getCmsStructure("animations[i]");
	    $formatData[0]['children'][] = array(
	        'var' => "animations[i].link",
	        'desc'=> $this->msg['cms_module_animationslist_view_calendar_link']
	    );
	    $formatData[] = array(
	        'var' => "calendar",
	        'desc'=> $this->msg['cms_module_animationslist_view_calendar_calendar_desc']
	    );
	    $formatData[] = array(
	        'var' => "legends",
	        'desc'=> $this->msg['cms_module_animationslist_view_calendar_legends_desc'],
	        'children' => array(
	            array(
	                'var' => "legends[i].animation",
	                'desc'=> $this->msg['cms_module_animationslist_view_calendar_legend_calendar_desc']
	            ),
	            array(
	                'var' => "legends[i].color",
	                'desc'=> $this->msg['cms_module_animationslist_view_calendar_legend_color_desc']
	            )
	        )
	    );
	    return array_merge($formatData, parent::get_format_data_structure());
	}
	
	public function execute_ajax() {
		global $do;
		
		$response = array();
		switch ($do) {
			case 'get_css':
				$response['content-type'] = 'text/css';
				$response['content'] = "
                    #".$this->get_module_dom_id()." td.cms_module_animationslist_animation_day {
                    	background : green;
                    }
                    #".$this->get_module_dom_id()." ul.cms_module_animationslist_view_calendar_animationslist li {
                    	display : block;
                    }
                    
                    #".$this->get_module_dom_id()." ul.cms_module_animationslist_view_calendar_animationslist li a {
                    	display : inline;
                    	background : none;
                    	border : none;
                    	color : inherit !important;
                    }";
				break;
			case 'get_js':
				$response['content-type'] = 'application/javascript';
				$response['content'] = '';
				break;
		}
		
		return $response;
	}
}
