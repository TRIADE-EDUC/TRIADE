<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_menu_view_menu.class.php,v 1.3.16.1 2025/01/17 10:40:46 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_menu_view_menu extends cms_module_common_view{


	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_form(){
		$form=$this->format_text($this->msg['cms_module_menu_view_menu_no_parameters']);
		return $form;
	}

	public function render($datas){
		$html_to_display = "";
		if(isset($datas['items'])){
			$html_to_display.= $this->build_items($datas['items']);
		}
		return $html_to_display;
	}

	protected function build_items($datas,$lvl=0){
		$display = "";
		if (is_countable($datas) && count($datas)) {
			$display.= "
			<ul class='cms_menu cms_menu_deep".$lvl."'>";
			foreach($datas as $item){
				$display.= "
				<li".($item['link'] == $_SERVER['HTTP_REFERER'] ? " class='cms_menu_current'":"").">";
				if($item['link']){
					$display.= "
					<a href='".$item['link']."' alt='".$this->format_text($item['title'])."' title='".$this->format_text($item['title'])."'>";
				}
				$display.=$item['title'];
				if($item['link']){
					$display.= "
					</a>";
				}
				if(isset($item['children'])) {
					$display.=$this->build_items($item['children'],$lvl+1);
				}
				$display.="
				</li>";
			}
			$display.= "
			</ul>";
		}
		return $display;
	}
}