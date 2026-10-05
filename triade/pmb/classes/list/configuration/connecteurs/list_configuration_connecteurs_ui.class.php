<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_connecteurs_ui.class.php,v 1.4 2021/10/22 08:29:05 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_connecteurs_ui extends list_configuration_ui {
		
	protected $connector_out_set_types;
	protected $connector_out_set_types_msgs;
	protected $connector_out_set_types_classes;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		static::$module = 'admin';
		static::$categ = 'connecteurs';
		static::$sub = str_replace(array('list_configuration_connecteurs_', '_ui'), '', static::class);
		
		$this->connector_out_set_types = array(
				1, //Paniers de notices
				2,  //Recherche multi-critères de notices
				3,  //Paniers d'exemplaires
				4  //Paniers de lecteurs
		);
		
		$this->connector_out_set_types_msgs = array(
				1 => "connector_out_set_types_msg_1",
				2 => "connector_out_set_types_msg_2",
				3 => "connector_out_set_types_msg_3",
				4 => "connector_out_set_types_msg_4"
		);
		
		$this->connector_out_set_types_classes = array(
				1 => "connector_out_set_noticecaddie",
				2 => "connector_out_set_noticemulticritere",
				3 => "connector_out_set_explcaddie",
				4 => "connector_out_set_emprcaddie"
		);
		
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function add_column_expand() {
		$this->columns[] = array(
				'property' => 'expand',
				'label' => '',
				'html' => "<img src='".get_url_icon('plus.gif')."' class='img_plus' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation(); show_sources(\"".addslashes('!!node_name!!')."\", e); ' style='cursor:pointer;'/>",
				'exportable' => false
		);
	}
	
	public function get_display_list() {
	    $display = "
		<script type='text/javascript' >
			function show_sources(id, event) {
				if (document.getElementById(id).style.display == 'none') {
					document.getElementById(id).style.display = '';
                    event.target.src = event.target.src.replace('plus', 'minus');
				} else {
					document.getElementById(id).style.display = 'none';
                    event.target.src = event.target.src.replace('minus', 'plus');
				}
			}
		</script>";
	    $display .= parent::get_display_list();
	    
	    return $display;
	}
}