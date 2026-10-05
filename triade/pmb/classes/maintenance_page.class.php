<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: maintenance_page.class.php,v 1.15 2023/10/10 06:29:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $include_path;
global $maintenance_page_form;
global $maintenance_page_activate;
global $maintenance_page_content;
global $maintenance_page_content_title;
global $maintenance_page_content_style;
global $maintenance_page_default_content, $msg, $charset;

require_once $include_path.'/templates/maintenance_page.tpl.php';

class maintenance_page {
	
	/**
	 * Indique si la page de maintenance est active
	 * @var boolean
	 */
	protected $active;
	
	/**
	 * Contenu de la page de maintenance
	 * @var string $page_content
	 */
	protected $content;
	
	/**
	 * Chemin du fichier signalant l'activation de la page de maintenance
	 * @var string $active_filename
	 */
	protected $active_filename;
	
	/**
	 * Chemin du fichier avec le contenu de la page de maintenance
	 * @var string $content_filename
	 */
	protected $content_filename;
	
	public function __construct() {
		global $base_path;
		
		$this->active_filename = $base_path.'/opac_css/temp/.'.DATA_BASE.'_maintenance';
		$this->content_filename = $base_path.'/opac_css/temp/'.DATA_BASE.'_maintenance.html';
	}
	
	public function fetch_data() {
		$this->active = false;
		if (file_exists($this->active_filename)) {
			$this->active = true;
		}
		$this->fetch_content();
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('maintenance_page_activate', 'admin_opac_maintenance_activate')
		->add_input_node('boolean', $this->active)
		->set_class('switch');
		$interface_content_form->add_element('maintenance_page_content_title', 'admin_opac_maintenance_content_title')
		->add_input_node('text', $this->content['title']);
		$interface_content_form->add_element('maintenance_page_content', 'admin_opac_maintenance_content')
		->add_textarea_node($this->content['body'])
		->set_cols(120)
		->set_rows(40);
		$interface_content_form->add_element('maintenance_page_content_style', 'admin_opac_maintenance_content_style')
		->add_textarea_node($this->content['style'])
		->set_cols(120)
		->set_rows(20);
		return $interface_content_form->get_display();		
	}
		
	public function get_form() {
		$interface_form = new interface_form('admin_opac_maintenance_form');
		$interface_form->set_content_form($this->get_content_form());
		return $interface_form->get_display();
	}
	
	public function get_values_from_form() {
		global $maintenance_page_activate;
		global $maintenance_page_content;
		global $maintenance_page_content_title;
		global $maintenance_page_content_style;
		
		$this->active = ($maintenance_page_activate*1 ? true : false);
		$this->content['body'] = stripslashes($maintenance_page_content);
		$this->content['title'] = stripslashes($maintenance_page_content_title);
		$this->content['style'] = stripslashes($maintenance_page_content_style);
	}
	
	public function save() {
		if ($this->active && !file_exists($this->active_filename)) {
			touch($this->active_filename);
		}
		if (!$this->active && file_exists($this->active_filename)) {
			unlink($this->active_filename);
		}
		file_put_contents($this->content_filename, $this->build_page());
	}
	
	protected function fetch_content() {
	    global $maintenance_page_default_content, $msg;
	    
		$this->content = array();
		if (file_exists($this->content_filename)) {
		    $html = file_get_contents($this->content_filename);
			
		    $matches = array();
			preg_match('/<title>(.*)<\/title>/s', $html, $matches);
			if (!empty($matches[1])) {
                $this->content['title'] = $matches[1];
			} else {
			    $this->content['title'] = $msg['admin_opac_maintenance'];
			}
			preg_match('/<style>(.*)<\/style>/s', $html, $matches);
			$this->content['style'] = trim($matches[1]);
			preg_match('/<body>(.*)<\/body>/s', $html, $matches);
			if (!empty(trim($matches[1]))) {
                $this->content['body'] = trim($matches[1]);
			} else {
			    $this->content['body'] = $maintenance_page_default_content;
			}
		} else {
			// Le fichier n'existe pas encore ou a été effacé, on va chercher le contenu par défaut
			global $maintenance_page_default_content, $msg;
			$this->content['body'] = $maintenance_page_default_content;
			$this->content['title'] = $msg['admin_opac_maintenance'];
			$this->content['style'] = '';			
		}
	}
	
	protected function build_page() {
		global $charset;
		
		$html = '<!DOCTYPE html>
<html>
<head>
	<meta charset="'.$charset.'" />
	<title>'.$this->content['title'].'</title>
	<style>
		'.$this->content['style'].'
	</style>
</head>
<body>
	'.$this->content['body'].'
</body>
</html>';
		
		return $html;
	}
	
	public function activate() {
	    $this->active = true;
	    if (!file_exists($this->active_filename)) {
	        touch($this->active_filename);
	    }
	    file_put_contents($this->content_filename, $this->build_page());
	}
	
	public function disable() {
	    $this->active = false;
	    if (file_exists($this->active_filename)) {
	        unlink($this->active_filename);
	    }
	    file_put_contents($this->content_filename, $this->build_page());
	}
	
	public function set_content($content) {
	    $this->content = $content;
	}
}