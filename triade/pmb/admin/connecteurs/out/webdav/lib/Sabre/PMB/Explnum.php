<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Explnum.php,v 1.9.4.1 2025/03/07 15:33:03 dbellamy Exp $
namespace Sabre\PMB;

use Sabre\DAV;
use Sabre\PMB;
use encoding_normalize;

class Explnum extends PMB\File {
	private $explnum_id;
	private $name;

	public function __construct($name) {
		$this->explnum_id = substr($this->get_code_from_name($name),1);
	}

	public function getName() {
		global $charset;
		$query = "select explnum_nom, explnum_extfichier from explnum where explnum_id = ".$this->explnum_id;
		$result = pmb_mysql_query($query);
		$name = "";
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$name = $row->explnum_nom;
			if(strpos(strtolower($row->explnum_nom),".".str_replace(".","",$row->explnum_extfichier))!==false){
				$name = substr($row->explnum_nom,0,strrpos($row->explnum_nom,"."));
			}
			$name.= " (E".$this->explnum_id.").".str_replace(".","",$row->explnum_extfichier);
		}
		if($charset != "utf-8"){
		    return encoding_normalize::utf8_normalize($name);
		}else{
			return $name;
		}
	}

	public function get() {
		$explnum = new \explnum($this->explnum_id);
		return $explnum->get_file_content();
	}

	public function getSize() {
		return strlen($this->get());
	}

	public function getContentType(){
		$mimetype= "";
		$query = "select explnum_mimetype from explnum where explnum_id = ".$this->explnum_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$mimetype = pmb_mysql_result($result,0,0);
		}
		return $mimetype;
	}

	public function getETag() {
		if(file_exists($this->explnum_id)){
			return '"' . md5_file($this->explnum_id) . '"';
		}else{
			return '"' . md5($this->explnum_id) . '"';
		}
	}

	public function put($data){
		global $base_path;
		global $id_rep;
		if($this->check_write_permission()){
			$filename = tempnam($base_path."/temp/","webdav_");
			$fp = fopen($filename, "w");
			while ($buf = fread($data, 1024)){
				fwrite($fp, $buf);
			}
			$explnum = new \explnum($this->explnum_id);
			fclose($fp);
			$id_rep = $this->config['upload_rep'];
			$explnum->get_file_from_temp($filename,$explnum->explnum_nomfichier,$this->config['up_place']);
			$explnum->update();
			unlink($filename);
		}else{
			//on a pas le droit d'écriture
			throw new DAV\Exception\Forbidden('Permission denied to modify file (filename ' . $this->getName() . ')');
		}
	}

	public function delete(){
		$explnum = new \explnum($this->explnum_id);
		$explnum->delete();
	}
}