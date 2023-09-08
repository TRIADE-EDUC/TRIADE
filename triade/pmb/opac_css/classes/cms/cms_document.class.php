<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_document.class.php,v 1.8 2018-08-24 08:44:59 plmrozowski Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/explnum.inc.php");
require_once($class_path."/cms/cms_collections.class.php");
create_tableau_mimetype();

class cms_document {
	public $id=0;
	public $title="";
	public $description="";
	public $filename="";
	public $mimetype="";
	public $filesize="";
	public $vignette="";
	public $url="";
	public $path ="";
	public $create_date="";
	public $num_storage=0;
	public $type_object="";
	public $num_object=0;
	protected $human_size = 0;
	protected $storage;
	
	public function __construct($id=0){
		$this->id = $id*1;
		$this->fetch_datas_cache();
	}
	
	protected function fetch_datas_cache(){
		if($tmp=cms_cache::get_at_cms_cache($this)){
			$this->restore($tmp);
		}else{
			$this->fetch_datas();
			cms_cache::set_at_cms_cache($this);
		}
	}
	
	protected function restore($cms_object){
		foreach(get_object_vars($cms_object) as $propertieName=>$propertieValue){
			$this->{$propertieName}=$propertieValue;
		}
	}
	
	protected function fetch_datas(){
		if($this->id){
			$query = "select document_title,document_description,document_filename,document_mimetype,document_filesize,document_vignette,document_url,document_path,document_create_date,document_num_storage,document_type_object,document_num_object from cms_documents where id_document = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->title = $row->document_title;
				$this->description = $row->document_description;
				$this->filename = $row->document_filename;
				$this->mimetype = $row->document_mimetype;
				$this->filesize = $row->document_filesize;
				$this->vignette = $row->document_vignette;
				$this->url = $row->document_url;
				$this->path = $row->document_path;
				$this->create_date = $row->document_create_date;
				$this->num_storage = $row->document_num_storage;
				$this->type_object = $row->document_type_object;
				$this->num_object = $row->document_num_object;
			}
			if($this->num_storage){
				$this->storage = storages::get_storage_class($this->num_storage);
			}
		}
	}
	
	public function get_item_render($edit_js_function="openEditDialog"){
		global $msg,$charset;
		$item = "
		<div class='document_item' id='document_".$this->id."'>
			<div class='document_item_content'>
			<img src='".$this->get_vignette_url()."' alt='".$msg["opac_notice_vignette_alt"]."'/>
			<br/>
			<p> <a href='#' onclick='".$edit_js_function."(".$this->id.");return false;' title='".htmlentities($msg['cms_document_edit_link'])."'>".htmlentities(($this->title ? $this->title : $this->filename),ENT_QUOTES,$charset)."</a><br />
			<span style='font-size:.8em;'>".htmlentities($this->mimetype,ENT_QUOTES,$charset).($this->filesize ? " - (".$this->get_human_size().")" : "")."</span></p>
			</div>
		</div>";
		return $item;
	}
	
	public function get_item_form($selected = false){
		global $msg,$charset;
		$item = "
		<div class='document_item".($selected? " document_item_selected" : "")."' id='document_".$this->id."'>
			<div class='document_checkbox'>
				<input name='cms_documents_linked[]' onchange='document_change_background(".$this->id.");' type='checkbox'".($selected ? "checked='checked'" : "")." value='".htmlentities($this->id,ENT_QUOTES,$charset)."'/>
			</div>
			<div class='document_item_content'>
				<img src='".$this->get_vignette_url()."' alt='".$msg["opac_notice_vignette_alt"]."'/>
				<br/>
				<p>".htmlentities(($this->title ? $this->title : $this->filename),ENT_QUOTES,$charset)."<br />
				<span style='font-size:.8em;'>".htmlentities($this->mimetype,ENT_QUOTES,$charset).($this->filesize ? " - (".$this->get_human_size().")" : "")."</span></p>
			</div>
		</div>";
		return $item;
	}
	
	public function get_vignette_url(){
		global $opac_url_base;
		return "./ajax.php?module=cms&categ=document&action=thumbnail&id=".$this->id;
	}
		
	public function get_document_url(){
		global $opac_url_base;
		return "./ajax.php?module=cms&categ=document&action=render&id=".$this->id;
	}
			
	public function get_human_size(){
		$units = array("o","Ko","Mo","Go");
		$i=0;
		do{
			if(!$this->human_size)$this->human_size = $this->filesize;
			$this->human_size = $this->human_size/1024;	
			$i++;
		}while($this->human_size >= 1024);
		return round($this->human_size,1)." ".$units[$i];
	}
	
	public function get_form($action="./ajax.php?module=cms&categ=documents&action=save_form&id="){
		global $msg,$charset;
		$form = "
		<form name='cms_document_form' id='cms_document_form' method='POST' action='".$action.$this->id."' style='width:500px;'>
			<div class='form-contenu'>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_title'>".htmlentities($msg['cms_document_title'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<input type='text' name='cms_document_title' value='".htmlentities($this->title,ENT_QUOTES,$charset)."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_description'>".htmlentities($msg['cms_document_description'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<textarea name='cms_document_description' >".htmlentities($this->description,ENT_QUOTES,$charset)."</textarea>
					</div>
				</div>";
		if($this->url){
			$form.= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_url'>".htmlentities($msg['cms_document_url'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<input type='text' name='cms_document_url' value='".htmlentities($this->url,ENT_QUOTES,$charset)."'/>
					</div>
				</div>";
		}
		if($this->id){	
			$form.= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_vign'>".htmlentities($msg['cms_document_vign'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<input type='checkbox' name='cms_document_vign' value='1'/>
					</div>
				</div>";
		}
		$form.="
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<div class='colonne3'>
						<label>".htmlentities($msg['cms_document_filename'],ENT_QUOTES,$charset)."</label>
						<br />
						<label>".htmlentities($msg['cms_document_mimetype'],ENT_QUOTES,$charset)."</label>
						<br />
						<label>".htmlentities($msg['cms_document_filesize'],ENT_QUOTES,$charset)."</label>
						<br />
						<label>".htmlentities($msg['cms_document_date'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<span>".htmlentities($this->filename,ENT_QUOTES,$charset)."</span>
						<br />
						<span>".htmlentities($this->mimetype,ENT_QUOTES,$charset)."</span>
						<br />
						<span>".htmlentities($this->get_human_size(),ENT_QUOTES,$charset)."</span>
						<br />
						<span>".htmlentities(format_date($this->create_date),ENT_QUOTES,$charset)."</span>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label>".htmlentities($msg['cms_document_storage'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						".$this->storage->get_storage_infos()."
					</div>
				</div>
				<div class='row'>&nbsp;</div>
				<hr />
				<div class='row'>
					<div class='left'>
						<input type='submit' class='bouton'  value='".htmlentities($msg['cms_document_save'],ENT_QUOTES,$charset)."'/>
					</div>
					<div class='right'>
						<input type='button' class='bouton' id='doc_del_button' value='".htmlentities($msg['cms_document_delete'],ENT_QUOTES,$charset)."'/>
					</div>
				</div>
				<div class='row'></div>
			</div>
		</form>
		<script>
			require(['dojo/dom-construct'],function(domConstruct){
				var form = dojo.byId('cms_document_form');
				dojo.connect(form, 'onsubmit', function(event){
					dojo.stopEvent(event);
					var xhrArgs = {
						form: dojo.byId('cms_document_form'),
						handleAs: 'text',
						load: function(data){
							domConstruct.place(data,'document_".$this->id."','replace');
							dijit.byId('dialog_document').hide();
						}
					};
					var deferred = dojo.xhrPost(xhrArgs);
				});	
				dojo.connect(dojo.byId('doc_del_button'),'onclick',function(event){
					if(confirm('".addslashes($msg['cms_document_confirm_delete'])."')){
						var xhrArgs = {
							url : '".str_replace("action=save_form","action=delete",$action).$this->id."',
							handleAs: 'text',
							load: function(data){
								if(data == 1){
									dojo.byId('document_".$this->id."').parentNode.removeChild(dojo.byId('document_".$this->id."'));
								}else{
									alert(data);
								}
								dijit.byId('dialog_document').hide();
							}
						};
						dojo.xhrGet(xhrArgs);
					}
				});
			});
		</script>";
		return $form;
	}
	
	function save_form(){
		global $msg,$charset;
		global $cms_document_title,$cms_document_description,$cms_document_url,$cms_document_vign;
		
		$this->title = $cms_document_title;
		$this->description = $cms_document_description;
		$this->url = $cms_document_url;
		
		if($cms_document_vign){
			$this->calculate_vignette();
		}
		
		
		if($this->id){
			$query = "update cms_documents set ";
			$clause = " where id_document = '".$this->id."'";
		}else{
			$query = "insert into cms_documents set ";
			$clause="";
		}
		
		$query.= "
			document_title = '".addslashes($this->title)."',
			document_description = '".addslashes($this->description)."',
			document_url = '".addslashes($this->url)."'";
		if($cms_document_vign){
			$query.= ",
			document_vignette = '".addslashes($this->vignette)."'";	
		}
		if(pmb_mysql_query($query.$clause)){
			return $this->get_item_render("openEditDialog");
		}
	}
	
	function delete(){
		//TODO vérification avant suppression dans le contenu éditorial
		
		//suppression physique
		if($this->storage->delete($this->path.$this->filename)){
			//il ne reste plus que la base
			if(pmb_mysql_query("delete from cms_documents where id_document = '".$this->id."'")){
				return true;
			}
		}else{
			return $msg['cms_document_delete_physical_error'];
		}
		return false;
	}
	
	function calculate_vignette(){
		error_reporting(null);
		global $base_path,$include_path,$class_path;
		$path = $this->get_document_in_tmp();
		if($path){
			switch($this->mimetype){
				case "application/epub+zip" :
					require_once($class_path."/epubData.class.php");
					$doc = new epubData($path);
					file_put_contents($path, $doc->getCoverContent());
				default :
					$this->vignette = construire_vignette($path);
					break;
			}
			unlink($path);
		}
	}
	
	function get_document_in_tmp(){
		$this->clean_tmp();
		global $base_path;
		$path = $base_path."/temp/cms_document_".$this->id;
 		if(file_exists($path)){
 			return $path;
 		}else if($this->storage->duplicate($this->path.$this->filename,$path)){
 			return $path;
 		}
		return false;
	}
	
	protected function clean_tmp(){
		global $base_path;
		$dh = opendir($base_path."/temp/");
		if (!$dh) return;
		$files = array();
		while (($file = readdir($dh)) !== false){
			if ($file != "." && $file != ".." && substr($file,0,strlen("cms_document_")) == "cms_document_") {
				$stat = stat($base_path."/temp/".$file);
				$files[$file] = array("mtime"=>$stat['mtime']);
			}
		}
		closedir($dh);
		$deleteList = array();
		foreach ($files as $file => $stat) {
			//si le dernier accès au fichier est de plus de 3h, on vide...
			if(time() - $stat["mtime"] > (3600*3)){
				if(is_dir($base_path."/temp/".$file)){
					$this->rrmdir($base_path."/temp/".$file);
				}else{
					unlink($base_path."/temp/".$file);
				}
			}
		}
	}
	
	function rrmdir($dir){
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir"){
						$this->rrmdir($dir."/".$object);
					}else{
						unlink($dir."/".$object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	
	public function format_datas(){
		$collection = new cms_collection($this->num_object);
		
		$datas = array(
			'id' => $this->id,
			'name' => $this->title,
			'description' => $this->description,
			'filename' => $this->filename,
			'mimetype' => $this->mimetype,
			'filesize' => array(
				'human' => $this->get_human_size(),
				'value' => $this->filesize
			),
			'url' => $this->get_document_url(),
			'create_date' => $this->create_date,
			'thumbnails_url' => $this->get_vignette_url()
		);
		$datas['collection'] = $collection->get_infos();
		return $datas;
	}
	
	public static function get_format_data_structure(){
		global $msg;
		$format_datas = array();
		$format_datas[] = array(
			'var' => "id",
			'desc'=> $msg['cms_document_format_data_id']
		);
		$format_datas[] = array(
			'var' => "name",
			'desc'=> $msg['cms_document_format_data_name']
		);	
		$format_datas[] = array(
			'var' => "description",
			'desc'=> $msg['cms_document_format_data_description']
		);
		$format_datas[] = array(
			'var' => "filename",
			'desc'=> $msg['cms_document_format_data_filename']
		);		
		$format_datas[] = array(
			'var' => "mimetype",
			'desc'=> $msg['cms_document_format_data_mimetype']
		);
		$format_datas[] = array(
			'var' => "filesize",
			'desc'=> $msg['cms_document_format_data_filesize'],
			'children' => array(
				array(
					'var' => "filesize.human",
					'desc'=> $msg['cms_document_format_data_filesize_human']
				),
				array(
					'var' => "filesize.value",
					'desc'=> $msg['cms_document_format_data_filesize_value']
				)
			)
		);	
		$format_datas[] = array(
				'var' => "url",
				'desc'=> $msg['cms_document_format_data_url']
		);
		$format_datas[] = array(
				'var' => "create_date",
				'desc'=> $msg['cms_document_format_data_create_date']
		);
		$format_datas[] = array(
				'var' => "thumbnails_url",
				'desc'=> $msg['cms_document_format_data_thumbnails_url']
		);	
		$format_datas[] = array(
			'var' => "collection",
			'desc'=> $msg['cms_document_format_data_collection'],
			'children' => array(
				array(
					'var' => "collection.id",
					'desc'=> $msg['cms_document_format_data_collection_id']
				),
				array(
					'var' => "collection.name",
					'desc'=> $msg['cms_document_format_data_collection_name']
				),
				array(
					'var' => "collection.description",
					'desc'=> $msg['cms_document_format_data_collection_description']
				)
			)
		);
		return $format_datas;
	}
	
	public function render_thumbnail(){
		header('Content-Type: image/png');
		if($this->vignette){
 			print $this->vignette;	
		}else{
			global $prefix_url_image ;
			if ($prefix_url_image) $tmpprefix_url_image = $prefix_url_image;
			else $tmpprefix_url_image = "./" ;
			print file_get_contents($tmpprefix_url_image."images/mimetype/".icone_mimetype($this->mimetype,substr($this->filename,strrpos($this->filename,".")+1)));
		}
	}
	
	public function render_doc(){
		$content = $this->storage->get_content($this->path.$this->filename);
		if($content){
			header('Content-Type: '.$this->mimetype);
			header('Content-Disposition: inline; filename="'.$this->filename.'"');
			if($this->filesize) header("Content-Length: ".$this->filesize);
			print $content;
		}
	}
}