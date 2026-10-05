<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk_custom_fields.class.php,v 1.3 2021/03/17 13:32:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/chklnk/chklnk.class.php");

class chklnk_custom_fields extends chklnk {
	    
	protected $sub_type;
	
	protected $parametres_perso;
	
    protected function get_title() {
    	global $msg;
    	
    	$title = '';
    	switch ($this->sub_type) {
			case 'collstate':
				$title .= $msg['chklnk_verifcp_etatcoll'];
				break;
			case 'cms_editorial':
				$title .= $msg['chklnk_verifeditorialcontentcp'];
				break;
			case 'notices':
			default:
				$title .= $msg['chklnk_verifcp'];
				break;
		}
		return $title;
    }
    
    protected function get_query() {
    	$query = '';
    	switch ($this->sub_type) {
    		case 'collstate':
    			$query .= implode(" union ", static::$queries['cp_etatcoll']);
    			break;
    		case 'cms_editorial':
    			$query .= "select distinct id_article as id, article_title as title from cms_articles join cms_editorial_custom_values on id_article = cms_editorial_custom_origine join cms_editorial_custom on idchamp = cms_editorial_custom_champ where type in ('url','resolve')";
    			$query .= " union ";
    			$query .= "select distinct id_section as id, section_title as title from cms_sections join cms_editorial_custom_values on id_section = cms_editorial_custom_origine join cms_editorial_custom on idchamp = cms_editorial_custom_champ where type in ('url','resolve')";
    			break;
    		case 'notices':
    		default:
    			$query .= implode(" union ", static::$queries['cp']);
    			break;
    	}
    	return $query;
    }
    
    protected function get_label_progress_bar() {
    	global $msg;
    	
    	$label = '';
    	switch ($this->sub_type) {
    		case 'collstate':
    			$label .= $msg['chklnk_verifcp_etatcoll'];
    			break;
    		case 'cms_editorial':
    			$label .= $msg['chklnk_verifurl_editorial_content_cp'];
    			break;
    		case 'notices':
    		default:
    			$label .= $msg['chklnk_verif_cp'];
    			break;
    	}
    	return $label;
    }
    
    protected function get_element_label($element) {
    	switch ($this->sub_type) {
    		case 'collstate':
    			return $element->tit1;
//     			$o->tit1
    			break;
    		case 'cms_editorial':
    			return $element->title;
    			break;
    		case 'notices':
    		default:
    			return notice::get_notice_title($element->id);
    			break;
    	}
    }
    
    protected function get_element_edit_link($element) {
    	switch ($this->sub_type) {
    		case 'collstate':
    			return notice::get_permalink($element->notice_id);
    			break;
    		case 'cms_editorial':
    			return "./cms.php?categ=editorial&sub=list";
    			break;
    		case 'notices':
    		default:
    			return notice::get_permalink($element->id);
    			break;
    	}
    }
    
    protected function process_element($element) {
    	global $pmb_url_base;
    	
    	$display = '';
    	$pp = $this->get_instance_parametres_perso();
    	switch ($this->sub_type) {
    		case 'collstate':
    			$pp->get_values($element->collstate_id);
    			break;
    		default:
    			$pp->get_values($element->id);
    			break;
    	}
		foreach($pp->values as $id_cp => $values){
			if($pp->t_fields[$id_cp]['TYPE'] == "url"){
				foreach($values as $value){
					$link = "";
					if(strpos($value,"|")!== false){
						$link = substr($value,0,strpos($value,"|"));
					}else $link = $value;
					$element->link = $link;
					$display .= $this->check_link($element);
				}
			}else if ($pp->t_fields[$id_cp]['TYPE'] == "resolve"){
				$options=$pp->t_fields[$id_cp]['OPTIONS'][0];
				foreach($values as $value){
					$link = "";
					$val = explode("|",$value);
					if(count($val)>1){
						$id =$val[0];
						foreach ($options['RESOLVE'] as $res){
							if($res['ID'] == $val[1]){
								$label = $res['LABEL'];
								$url= $res['value'];
								break;
							}
						}
						$link = str_replace("!!id!!",$id,$url);
						$link = str_replace('./', '', $link);
						if(preg_match('`^[a-zA-Z0-9_]+\.php`',$link)){
							$link=$pmb_url_base.$link;
						}
						$element->link = $link;
						$display .= $this->check_link($element);
					}						
				}
			}
		}
		return $display;
    }
    
    protected function get_instance_parametres_perso() {
    	if(!isset($this->parametres_perso[$this->sub_type])) {
    		$this->parametres_perso[$this->sub_type] = new parametres_perso($this->sub_type);
    	}
    	return $this->parametres_perso[$this->sub_type];
    }
    
    public function set_sub_type($sub_type) {
    	$this->sub_type = $sub_type;
    }
}
?>