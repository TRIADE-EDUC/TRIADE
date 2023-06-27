<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: maintenance_page.class.php,v 1.5 2018-03-16 09:14:50 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/maintenance_page.tpl.php');

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
		
		$this->active_filename = $base_path.'/opac_css/temp/.maintenance';
		$this->content_filename = $base_path.'/opac_css/temp/maintenance.html';
	}
	
	public function fetch_data() {
		$this->active = false;
		if (file_exists($this->active_filename)) {
			$this->active = true;
		}
		$this->fetch_content();
	}
	
	public function get_form() {
		global $maintenance_page_form, $base_path, $pmb_javascript_office_editor, $charset;
		
		$form = '';
		if ($pmb_javascript_office_editor) {
			$form.= $pmb_javascript_office_editor;
			$form.= "<script type='text/javascript' src='".$base_path."/javascript/tinyMCE_interface.js'></script>";
		}
		$form.= $maintenance_page_form;
		
		$checked = '';
		if ($this->active) {
			$checked = 'checked="checked"';
		}
		$form = str_replace('!!maintenance_page_activate_checked!!', $checked, $form);
		$form = str_replace('!!maintenance_page_content_title!!', $this->content['title'], $form);
		$form = str_replace('!!maintenance_page_content_body!!', $this->content['body'], $form);
		$form = str_replace('!!maintenance_page_content_style!!', $this->content['style'], $form);
		
		return $form;
	}
	
	public function get_values_from_form() {
		global $maintenance_page_activate;
		global $maintenance_page_content_body;
		global $maintenance_page_content_title;
		global $maintenance_page_content_style;
		
		$this->active = ($maintenance_page_activate*1 ? true : false);
		$this->content['body'] = stripslashes($maintenance_page_content_body);
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
		$this->content = array();
		if (file_exists($this->content_filename)) {
			$html = file_get_contents($this->content_filename);
			
			$matches = array();
			preg_match('/<title>(.*)<\/title>/s', $html, $matches);
			$this->content['title'] = $matches[1];
			preg_match('/<style>(.*)<\/style>/s', $html, $matches);
			$this->content['style'] = trim($matches[1]);
			preg_match('/<body>(.*)<\/body>/s', $html, $matches);
			$this->content['body'] = trim($matches[1]);
		} else {
			// Le fichier n'existe pas encore ou a été effacé, on va chercher le contenu par défaut
			global $maintenance_page_default_content, $msg;
			$this->content['body'] = $maintenance_page_default_content;
			$this->content['title'] = $msg['admin_opac_maintenance'];
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
}