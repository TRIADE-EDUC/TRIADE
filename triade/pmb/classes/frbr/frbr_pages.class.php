<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_pages.class.php,v 1.9 2019-06-06 11:51:49 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/frbr/frbr_page.class.php");
require_once($class_path."/frbr/frbr_entities_parser.class.php");

class frbr_pages {
	
	/**
	 * Liste des pages
	 */
	protected $pages;
	
	/**
	 * Constructeur
	 */
	public function __construct() {
		$this->fetch_data();
	}
	
	/**
	 * Données
	 */
	protected function fetch_data() {
		
		$this->pages = array();
		$query = 'select id_page, page_entity from frbr_pages order by page_entity, page_order, page_name';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->pages[$row->page_entity][] = new frbr_page($row->id_page);
			}
		}
	}
	
	/**
	 * Liste des pages
	 */
	public function get_display_content_list($entity_name='') {
		global $msg, $base_path;
		
		$display = '';
		$parity=1;
		foreach($this->pages[$entity_name] as $page) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity++;
			$td_css_style = "style='cursor: pointer;'";
			$td_javascript = " onmouseover=\"this.className='surbrillance center'\" onmouseout=\"this.className='$pair_impair center'\" ";
			$td_javascript .= " onmousedown=\"document.location='cms.php?categ=frbr_pages&sub=edit&id=".$page->get_id()."';\" ";
			
			$display .= "<tr class='$pair_impair'>";
			$display .= "<td>
							<input type='button' class='bouton_small' value='-' onClick=\"document.location='./cms.php?categ=frbr_pages&sub=list&action=down&id=".$page->get_id()."'\"/>
							<input type='button' class='bouton_small' value='+' onClick=\"document.location='./cms.php?categ=frbr_pages&sub=list&action=up&id=".$page->get_id()."'\"/>
						</td>";
			$display .= '<td '.$td_javascript.' '.$td_css_style.' class="center">'.$page->get_name().'</td>';
			$display .= '<td '.$td_javascript.' '.$td_css_style.' class="center">'.($page->get_parameter_value('records_list') ? 'X' : '').'</td>';
			$display .= '<td '.$td_javascript.' '.$td_css_style.' class="center">'.($page->get_parameter_value('facettes_list') ? 'X' : '').'</td>';
			$display .= '<td '.$td_javascript.' '.$td_css_style.' class="center">'.($page->get_parameter_value('isbd') ? 'X' : '').'</td>';
			$display .= '<td '.$td_javascript.' '.$td_css_style.' class="center">'.$page->get_parameter_value('template_directory').'</td>';
			$display .= '<td '.$td_javascript.' '.$td_css_style.' class="center">'.$page->get_parameter_value('record_template_directory').'</td>';
			$display .= '<td class="center"><input type="button" class="bouton" value="'.$msg['frbr_page_tree_build'].'" onclick=\'document.location="'.$base_path.'/cms.php?categ=frbr_pages&sub=build&num_page='.$page->get_id().'&num_parent=0"\' /></td>';
			$display .= '</tr>';
		}
		return $display;
	}
	
	/**
	 * Header de la liste
	 */
	public function get_display_header_list($entity_name='') {
		global $msg, $charset;
		
		$display = "
		<tr>
			<th>".htmlentities($msg['frbr_page_order'],ENT_QUOTES,$charset)."</th>
			<th width='20%'>".htmlentities($msg['frbr_page_name'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['frbr_page_parameter_records_list'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['frbr_page_parameter_facettes_list'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['frbr_page_parameter_isbd'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['frbr_page_parameter_template_directory'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['frbr_page_parameter_record_template_directory'],ENT_QUOTES,$charset)."</th>
			<th></th>
		</tr>
		";
		return $display;
	}
	
	/**
	 * Affiche la liste des objets
	 */
	public function get_display_list() {
		global $msg, $charset;
		
		$display = "";
		$entities_parser = new frbr_entities_parser();
		$managed_entities = $entities_parser->get_managed_entities();
		foreach($this->pages as $entity_name=>$entity_page) {
			$display .= "<h3>".$managed_entities[$entity_name]['name']."</h3>";
			
			$display .= "<table id='pages_list'>";
			$display .= $this->get_display_header_list($entity_name);
			if(count($this->pages)) {
				$display .= $this->get_display_content_list($entity_name);
			}
			$display .= "</table>";
		}
		$display .= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
				<input type='button' class='bouton' name='frbr_page_add' value='".htmlentities($msg["frbr_page_add"], ENT_QUOTES, $charset)."' 
					onclick=\"document.location='./cms.php?categ=frbr_pages&sub=edit'\" />
			</div>
			<div class='right'>
			</div>
		</div>";
		return $display;
	}
	
	public function get_pages() {
		return $this->pages;
	}
}