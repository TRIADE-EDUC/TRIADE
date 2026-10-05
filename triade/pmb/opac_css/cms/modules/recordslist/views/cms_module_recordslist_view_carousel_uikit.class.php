<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_view_carousel_uikit.class.php,v 1.4 2023/06/07 10:27:46 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

class cms_module_recordslist_view_carousel_uikit extends cms_module_common_view_carousel_uikit {
	
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_recordslist_view_link'>".$this->format_text($this->msg['cms_module_recordslist_view_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("notice");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->save_constructor_link_form("notice");
		return parent::save_form();
	}
	
	public function render($datas){
		global $opac_show_book_pics;
		global $opac_book_pics_url;
		global $opac_notice_affichage_class;
		
		$render_datas = array();
		if(empty($opac_notice_affichage_class)){
			$opac_notice_affichage_class ="notice_affichage";
		}
		
		if (empty($datas['records'])) {
		    $datas['records'] = array();
		}
		
		$add_to_cart_link = '';
		
		$query = "select notice_id,tit1,thumbnail_url,code from notices where notice_id in ('".implode("','",$datas['records'])."') order by field( notice_id, '".implode("','",$datas['records'])."')";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
		    $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
			while($row = pmb_mysql_fetch_object($result)){
			    $url_vign = "";
			    if ($opac_show_book_pics=='1') {
			        $url_vign = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $row->notice_id);
			    }
				$notice_class = new $opac_notice_affichage_class($row->notice_id);
				$notice_class->do_header();
				if($this->parameters['used_template']){
					$tpl = notice_tpl_gen::get_instance($this->parameters['used_template']);
					$content = $tpl->build_notice($row->notice_id);
				}else{
					$notice_class->do_isbd();
					$content = $notice_class->notice_isbd;
				}
				
				$render_datas[] = array(
					'id' => $row->notice_id,
					'title' => $row->tit1,
					'link' => $this->get_constructed_link("notice",$row->notice_id),
					'vign' => $url_vign,
					'header' => $notice_class->notice_header,
					'content' => $content
				);
			}
			
			$add_to_cart_link = '<span class="addCart">
							<a title="'.$this->msg['cms_module_recordslist_view_add_cart_link'].'" target="cart_info" href="cart_info.php?notices='.implode(",",$datas['records']).'">'.$this->msg['cms_module_recordslist_view_add_cart_link'].'</a>
						  </span>';
		}
		$render_datas = array(
			'title' => $datas['title'] ?? "",
			'records' => $render_datas,
			'add_to_cart_link' => $add_to_cart_link
		);
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){
		$datas = new cms_module_carousel_datasource_notices();
		$format_datas = $datas->get_format_data_structure();
		$format_datas[0]['children'][] = array(
				'var' => "records[i].header",
				'desc'=> $this->msg['cms_module_common_view_record_header_desc']
		);
		$format_datas[0]['children'][] = array(
				'var' => "records[i].content",
				'desc' => $this->msg['cms_module_common_view_carousel_uikit_record_content_desc']
		);
		$format_datas[] = array(
				'var' => "add_to_cart_link",
				'desc' => $this->msg['cms_module_recordslist_view_add_cart_link_desc']
		);
 		$format_datas = array_merge($format_datas,parent::get_format_data_structure());
		return $format_datas;
	}
}