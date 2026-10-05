<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_view_carousel.class.php,v 1.3 2019/09/17 09:44:35 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_agenda_view_carousel extends cms_module_carousel_view_carousel{
	
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_agenda_view_link'>".$this->format_text($this->msg['cms_module_agenda_view_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("article");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->save_constructor_link_form("article");
		return parent::save_form();
	}
	
	public function render($datas){		
		$render_datas = array();
		$render_datas['title'] = "Liste d'évènements";
		$render_datas['events'] = array();
		foreach($datas['events'] as $event){
			$event->link = $this->get_constructed_link("article",$event->id);
			$render_datas['records'][]=$event;
		}
		return parent::render($render_datas);		
	}
	
	public function get_format_data_structure(){
		$datas = cms_article::get_format_data_structure("article",false);
		$datas[] = array(
			'var' => "link",
			'desc'=> $this->msg['cms_module_agenda_view_carousel_link_desc']
		);
		
		$format_datas = array(
			array(
				'var' => "records",
				'desc' => $this->msg['cms_module_carousel_view_carousel_records_desc'],
				'children' => $this->prefix_var_tree($datas,"records[i]")
			)
		);
		$format_datas = array_merge($format_datas,parent::get_format_data_structure());
		return $format_datas;
	}
}