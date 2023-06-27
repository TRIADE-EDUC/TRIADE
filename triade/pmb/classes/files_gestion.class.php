<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: files_gestion.class.php,v 1.7 2018-10-03 10:47:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/files_gestion.tpl.php");

class files_gestion {	
	public $path="";
	public $url="";
	public $info=array();
	public $error="";
	
	public function __construct($path,$url,$create_if_not_exist=1) {
		global $msg;
		$this->error="";
		$this->path=$path;		
		$this->url=$url;			
		// path exist?
		if(!is_dir($this->path)){		
			if($create_if_not_exist){
				if(!@mkdir($this->path)){						
					$this->error=$msg["admin_files_gestion_error_create_folder"].$this->path."<br />".$msg["admin_files_gestion_error_param_img_folder"];
					$this->path="";
					return;
				}
				chmod($this->path, 0777);
			} else{	
				$this->path=""; 
				$this->error=$msg["admin_files_gestion_error_no_path"];
				return;
			}	
		}	
		$this->fetch_data();
	}
	
	public function fetch_data() {
		global $msg;
		global $PMBuserid;
		$this->error="";
		$this->info=array();
		$i=0;
		if(!is_dir($this->path)){
			$this->error=$msg["admin_files_gestion_error_is_no_path"].$this->path;
			$this->path="";
			return;
		}
		if(($objects = @scandir($this->path)) !== false) {
			foreach ($objects as $object) {
				if($object != '.' && $object != '..') {
					if (filetype($this->path."/".$object) != "dir") {
						$this->info[$i]['name']=$object;
						$this->info[$i]['strtolower_name']=strtolower($object);
						$this->info[$i]['path']=$this->path;
						$this->info[$i]['type']=filetype($this->path . $object);
						$i++;
					}
 				}
			}
		}
		if(count($this->info)){
			usort($this->info, array($this,'triArrayInfo'));
		}
	}
	
	public function triArrayInfo($a, $b){
		return $a['strtolower_name'] > $b['strtolower_name'];
	}
	
	public function get_error() {
		return $this->error;
	}	
		
	public function get_count_file() {
		return count($this->info);
	}	
	
	public function upload($from='bottom', $MAX_FILESIZE=0x500000) {
		global $msg;
		
		$statut=false;
		$input_name = 'select_file_'.$from;
		if (! is_uploaded_file($_FILES[$input_name]['tmp_name'])){
			$this->error=$msg["admin_files_gestion_error_not_write"].$_FILES[$input_name]['name'];
			return $statut;				
		}
		
		if ($_FILES[$input_name]['size'] >= $MAX_FILESIZE){ 
			$this->error=$msg["admin_files_gestion_error_to_big"].$_FILES[$input_name]['name'];
			return $statut;
		}
		//		"/^\.(jpg|jpeg|gif|png|doc|docx|txt|rtf|pdf|xls|xlsx|ppt|pptx){1}$/i"; 
		$no_valid_extension="/^\.(php|PHP){1}$/i";
		if(preg_match($no_valid_extension, strrchr($_FILES[$input_name]['name'], '.'))){
			$this->error=$msg["admin_files_gestion_error_not_valid"].$_FILES[$input_name]['name'];
			return $statut;			
		}
		// tout semble ok on le déplace au bon endroit
		$statut=move_uploaded_file($_FILES[$input_name]["tmp_name"],$this->path.$_FILES[$input_name]['name']);
		if($statut==false) $this->error=$msg["admin_files_gestion_error_not_loaded"].$_FILES[$input_name]['name'];
	
		chmod($this->path.$_FILES[$input_name]['name'], 0777);
		$this->fetch_data();
		return $statut;
	}	
		
	public function delete($filename) {
		global $msg;
		$statut=false;
		foreach($this->info as $elt){
			if($filename==$elt['name']){
				$file_to_delete=$elt['path'].$filename;
				if(file_exists($file_to_delete)){
					$statut=unlink($file_to_delete);
					if($statut==false) $this->error=$msg["admin_files_gestion_error_not_delete"].$file_to_delete;
				}else{
					$this->error=$msg["admin_files_gestion_error_is_not_file"].$file_to_delete;
				}	
				break;
			}
		}
		$this->fetch_data();
		return($statut);
	}	
	
	protected function get_file_template($from='bottom') {
		global $files_gestion_list_add_file;
		
		$tpl = $files_gestion_list_add_file;
		$tpl = str_replace('!!from!!',$from, $tpl);
		return $tpl;
	}
	
	public function get_list($post_url="admin.php?categ=mailtpl&sub=img") {
		global $msg,$charset;
		global $files_gestion_list_tpl,$files_gestion_list_line_tpl;
		
		$tpl=$files_gestion_list_tpl;
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info as $elt){
			$tpl_elt=$files_gestion_list_line_tpl;
			if($odd_even=='odd')$odd_even="even";
			else $odd_even="odd";
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);
			$tpl_elt=str_replace('!!name!!',$elt['name'], $tpl_elt);
			$tpl_elt=str_replace('!!path!!',$elt['path'], $tpl_elt);
			$tpl_elt=str_replace('!!type!!',$elt['type'], $tpl_elt);
			$tpl_elt=str_replace('!!vignette!!',"<img height='15' width='15' src=\"".$this->url.urlencode($elt['name'])."\" alt=\"\" onmouseover=\"show_div_img(event,'".$this->url.urlencode($elt['name'])."')\" onmouseout=\"hide_div_img()\" />", $tpl_elt);
			$tpl_elt=str_replace('!!urlencode_name!!',urlencode($elt['name']), $tpl_elt);
			$tpl_list.=$tpl_elt;
		}
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		$tpl=str_replace('!!post_url!!',$post_url, $tpl);
		if(count($this->info) > 20) {
			$tpl=str_replace('!!add_file_top!!',$this->get_file_template('top'), $tpl);
		} else {
			$tpl=str_replace('!!add_file_top!!','', $tpl);
		}
		$tpl=str_replace('!!add_file_bottom!!',$this->get_file_template('bottom'), $tpl);
		return $tpl;
	}	
	
	public function get_sel($sel_name='select_file',$value_tpl="!!path!!!!name!!",$label_tpl="!!name!!") {
		global $msg, $pmb_mail_html_format; 
		$tpl="<select name='$sel_name' id='$sel_name'>";				
		foreach($this->info as $elt){
			$value=$value_tpl;
			$value=str_replace('!!name!!',$elt['name'], $value);
//			if ($pmb_mail_html_format==2)$url_file=$elt['path'];
//			else $url_file=$this->url;
			
			$value=str_replace('!!path!!',$this->url, $value);
			$value=str_replace('!!type!!',$elt['type'], $value);
			$label=$label_tpl;
			$label=str_replace('!!name!!',$elt['name'], $label);
			$label=str_replace('!!path!!',$elt['path'], $label);
			$label=str_replace('!!type!!',$elt['type'], $label);
			$tpl.="<option value=".$value.">".$label."</option>";
		}
		$tpl.="</select>";
		return $tpl;
	}		
} // files_gestion class end
	
