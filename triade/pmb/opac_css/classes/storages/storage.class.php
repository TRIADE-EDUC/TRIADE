<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storage.class.php,v 1.9 2021/11/24 20:00:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/file_uploader.class.php');

class storage {
	public $id = 0;
	public $class_name="";
	public $parameters = array();
	public $name ="";
	
	public function __construct($id=0){
		$this->id = intval($id);
		$this->fetch_datas();
	}
	
	protected function fetch_datas(){
		$query = "select * from storages where id_storage = ".$this->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$this->name = $row->storage_name;
			$this->class_name = $row->storage_class;
			$this->parameters = unserialize($row->storage_params);
		}		
	}
	
 	public function upload_process(){
 		global $fnc;
 		$protocol = $_SERVER["SERVER_PROTOCOL"];
 		$uploadDir = "./temp/";
 		
 		switch ($fnc){
			case 'upl':
				if (is_dir($uploadDir)) {
					if (is_writable($uploadDir)) {
						return $this->get_file();
					}else{
 						header($protocol.' 405 Method Not Allowed');
 						exit('Upload directory is not writable.');
					}
				}else{
 					header($protocol.' 404 Not Found');
 					exit('Upload directory does not exist.');
				}
				break;
			case 'del':
// 				$fileName = isset($_GET['fileName']) ? $_GET['fileName'] : null;
// 				if ($fileName) {
// 					$this->delete($fileName, $uploadDir);
// 				}else {
// 					header($protocol.' 404 Not Found');
// 					exit('No file name provided.');
// 				}
				break;
			case 'resume':
// 				$this->save($uploadDir, true);
 				break;
 			case 'getNumWrittenBytes':
// 				$fileName = isset($_GET['fileName']) ? $_GET['fileName'] : null;
// 				if($fileName){
// 					if (file_exists($uploadDir.$fileName)) {
// 						echo json_encode(array('numWritten' => filesize($uploadDir.$fileName)));
// 					}else {
// 						header($protocol.' 404 Not Found');
// 						exit('Previous upload not found. Resume not possible.');
// 					}
// 				}else{
// 					header($protocol.' 404 Not Found');
// 					exit('No file name provided.');
// 				}
 				break;
 		}	
 	}
 	
 	public function getNumWrittenBytes() {
 		return $this->numWrittenBytes;
 	}
 	
 	protected function get_file(){
 		$protocol = $_SERVER["SERVER_PROTOCOL"];
 		
 		$file = file_uploader::get_file();
 		if(is_object($file)) {
 			$this->fileName = $file->name;
 			$file->content = file_get_contents("php://input");
 		
 			// Since I don't know if the header content-length can be spoofed/is reliable, I check the file size again after it is uploaded
 			$limit = file_uploader::get_limit();
 			if (mb_strlen($file->content) > $limit) {
 				header($protocol.' 403 Forbidden');
 				return false;
 			}
 			$this->numWrittenBytes = file_put_contents("./temp/".$file->name, $file->content);
 			if ($this->numWrittenBytes !== false) {
 				header($protocol.' 201 Created');
 				$success = $this->add($file->name);
 				if(!$success){
  					unlink("./temp/".$file->name);
 				}
 				return $success;
 			}else {
 				header($protocol.' 505 Internal Server Error');
 				return false;
 			}
 		}
 	}
	
	//a surcharger
	public function get_params_form(){
		
	}
	
	//a surcharger
	public function get_params_to_save(){
	
	}	
	
	//a surcharger
	public function add($file){
	
	}
	
	//a surcharger
	public function update($new_file){
	
	}
	
	//a surcharger
	public function delete($file){
	
	}
	
	//a surcharger
	public function move($file,$dest){
	
	}
	
	//a surcharger
	public function get_uploaded_fileinfos(){
		
	}
	
	public function get_mimetype(){
		$finfo = new finfo(FILEINFO_MIME);
		//petit hack pour les formats exotiques(type BNF)
		$arrayMimetypess = array("application/bnf+zip");
		$arrayExtensions = array(".bnf");
		$original_extension = (false === $pos = strrpos($this->filepath, '.')) ? '' : substr($this->filepath, $pos);
		if(in_array($original_extension,$arrayExtensions)){
			for($i=0 ; $i<count($arrayExtensions) ; $i++){
				if($arrayExtensions[$i] == $original_extension){
					return $arrayMimetypess[$i];
				}
			}
		}else{
			$infos = $finfo->file($this->filepath);
			return substr($infos,0,strpos($infos,";"));
		}
	}
 }