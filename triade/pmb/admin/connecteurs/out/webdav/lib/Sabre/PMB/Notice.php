<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Notice.php,v 1.18.4.1 2025/03/07 15:33:03 dbellamy Exp $
namespace Sabre\PMB;

use Sabre\DAV;
use encoding_normalize;

class Notice extends Collection {
	private $notice_id;
	public $type;

	public function __construct($name,$config) {
		$this->notice_id = substr($this->get_code_from_name($name),1);
		$this->type = "notice";
		$this->config = $config;
	}


	public function getChildren() {
		$children = array();
		$query = "select explnum_id from explnum where explnum_mimetype!= 'URL' and ((explnum_notice = ".$this->notice_id." and explnum_bulletin = 0) or (explnum_notice =0 and explnum_bulletin = (select bulletin_id from bulletins join notices on notice_id = num_notice where niveau_biblio = 'b' and notice_id=".$this->notice_id.")))";
		$query = $this->filterExplnums($query);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$children[] = $this->getChild("(E".$row->explnum_id.")");
			}
		}
		return $children;
	}

	public function getName() {
		$query = "select concat(serials.tit1,' - ',notices.tit1) as title from notices join bulletins on notices.notice_id = bulletins.num_notice and notices.niveau_biblio = 'b' join notices as serials on bulletins.bulletin_notice = serials.notice_id join explnum on explnum_notice = 0 and explnum_bulletin = bulletin_id where notices.notice_id= ".$this->notice_id." and explnum_mimetype!= 'URL' union select tit1 as title from notices join explnum on explnum_bulletin = 0 and explnum_notice = notice_id where notice_id = ".$this->notice_id." and explnum_mimetype != 'URL'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$name = $row->title." (N".$this->notice_id.")";
		}
		return $this->format_name($name);
	}

    public function createFile($name, $data = null) {
    	global $charset,$base_path,$id_rep;

    	if($this->check_write_permission()){
    		$name = str_replace('\"', '', str_replace('\'', '', $name));
			if($charset !=='utf-8'){
				$name=encoding_normalize::utf8_decode($name);
			}

			$filename = realpath($base_path."/temp/")."/webdav_".md5($name.time()).".".extension_fichier($name);
			$fp = fopen($filename, "w");
			if(!$fp){
				//on a pas le droit d'écriture
				throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $filename . ')');
			}

			while ($buf = fread($data, 1024)){
				fwrite($fp, $buf);
			}
			fclose($fp);
			if(!file_exists($filename)){
				//Erreur de copie du fichier
				unlink($filename);
				throw new DAV\Exception\NotFound('Empty file (filename ' . $filename . ')');
			}
			if(!filesize($filename)){
				//Premier PUT d'un client Windows...
				unlink($filename);
				return;
			}

			$notice_id = $this->notice_id;
			$bulletin_id = 0;
			$query = "SELECT CONCAT(niveau_biblio, niveau_hierar) AS niveau FROM notices WHERE notice_id = ".$this->notice_id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				if ($row->niveau == "b2") {
					$query = "SELECT bulletin_id FROM bulletins WHERE num_notice = ".$this->notice_id;
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$notice_id = 0;
						$bulletin_id = $row->bulletin_id;
					}
				}
			}
			$explnum = new \explnum(0, $notice_id, $bulletin_id);
			$id_rep = $this->parentNode->config['upload_rep'];
			$explnum->get_file_from_temp($filename,$name,$this->parentNode->config['up_place']);
			$explnum->params['explnum_statut'] = $this->config['default_docnum_statut'];

			//Enregistrement en base - Le contenu existe déjà sous cette notice
			if(!empty($explnum->infos_docnum["contenu"])) {
			    $query = "SELECT explnum_notice,explnum_id from explnum
                        WHERE explnum_notice = ".$notice_id."
                        AND explnum_bulletin = ".$bulletin_id."
                        AND explnum_nom = '".addslashes($explnum->infos_docnum["nom"])."'
                        AND explnum_data = '".addslashes($explnum->infos_docnum["contenu"])."'";
			    $result = pmb_mysql_query($query);
			    if(pmb_mysql_num_rows($result) > 1) {
			        while ($row = pmb_mysql_fetch_object($result)) {
			            $old_docnum = new \explnum($row->explnum_id);
			            $old_docnum->delete();
			        }
			    } elseif(pmb_mysql_num_rows($result) == 1) {
			        $row = pmb_mysql_fetch_object($result);
			        $explnum->explnum_id = $row->explnum_id;
			    }
			}
			$explnum->update();
			$this->update_notice($this->notice_id);
			if(file_exists($filename)){
				unlink($filename);
			}
    	}else{
    		//on a pas le droit d'écriture
    		throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $name . ')');
    		return false;
    	}

    }
}