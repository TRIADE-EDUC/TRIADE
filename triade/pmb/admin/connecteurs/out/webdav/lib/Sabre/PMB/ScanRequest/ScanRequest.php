<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ScanRequest.php,v 1.20.4.2 2025/03/14 08:07:34 qvarin Exp $
namespace Sabre\PMB\ScanRequest;

use Sabre\DAV;
use encoding_normalize;

class ScanRequest extends Collection {
	protected $scan_request;
	protected $record;

	public function __construct($name,$config) {
		parent::__construct($config);

		$id = substr($this->get_code_from_name($name),1);
		$this->scan_request = new \scan_request($id);
		$this->type = "scan_request";
	}

	public function getName() {
		return $this->format_name($this->scan_request->get_title()." (R".$this->scan_request->get_id().")");
	}

	public function getChildren() {
		$children = array();
		$query = "select scan_request_linked_record_num_notice as notice_id
				from scan_request_linked_records
				where scan_request_linked_record_num_bulletin = 0
				and scan_request_linked_record_num_request = ".$this->scan_request->get_id();
		$query = $this->getQueryFilterNotices($query);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$query = 'select id_notice_nomenclature from nomenclature_notices_nomenclatures where notice_nomenclature_num_notice= '.$row->notice_id.' limit 1';
				if(pmb_mysql_result(pmb_mysql_query($query), 0,0)){
					$children[] = $this->getChild("(M".$row->notice_id.")");
				}else{
					$query = 'select child_record_num_record from nomenclature_children_records where child_record_num_record= '.$row->notice_id.' limit 1';
					if(pmb_mysql_result(pmb_mysql_query($query), 0,0)){
						$children[] = $this->getChild("(I".$row->notice_id.")");
					}else{
						$children[] = $this->getChild("(N".$row->notice_id.")");
					}
				}
			}
		}
		$query = "select scan_request_linked_record_num_bulletin as bulletin_id, if(bulletins.num_notice,bulletins.num_notice,bulletins.bulletin_notice) as bulletin_num_notice
				from scan_request_linked_records
				join bulletins on bulletins.bulletin_id = scan_request_linked_records.scan_request_linked_record_num_bulletin and scan_request_linked_record_num_notice = 0
				left join notices as notices_b on bulletins.num_notice = notices_b.notice_id and bulletins.num_notice <> 0
    			left join notices as notices_s on bulletins.bulletin_notice = notices_s.notice_id and bulletins.bulletin_notice <> 0
				where scan_request_linked_record_num_notice = 0
				and scan_request_linked_record_num_request = ".$this->scan_request->get_id();
		$query = $this->getQueryFilterBulletins($query);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$children[] = $this->getChild("(B".$row->bulletin_id.")");
			}
		}
		usort($children,"sortChildren");
		return $children;
	}

	public function get_scan_request() {
		return $this->scan_request;
	}

	public function add_explnum($notice_id, $bulletin_id, $explnum_id) {
		$query = 'insert into scan_request_explnum (scan_request_explnum_num_request, scan_request_explnum_num_notice, scan_request_explnum_num_bulletin, scan_request_explnum_num_explnum) values ('.$this->scan_request->get_id().', '.$notice_id.', '.$bulletin_id.', '.$explnum_id.')';
		pmb_mysql_query($query);
	}

	public function getLastModified() {
	    $last = new \Datetime($this->scan_request->get_update_date());
	    return $last->format("U");
	}

	public function create_scan_request_file($notice_id, $bulletin_id, $name, $data = null,$from_music="") {
	    global $charset,$base_path,$id_rep, $matches;
		global $pmb_nomenclature_record_children_link;

		if($this->check_write_permission()){
			$name = str_replace('\"', '', str_replace('\'', '', $name));
			if($charset !=='utf-8'){
				$name=encoding_normalize::utf8_decode($name);
			}
			// On préfixe le nom avec l'identifiant de la demande pour éviter les doublons sur le serveur
			$name = $this->scan_request->get_id().'_'.$name;
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

			$metas = array();
			//récupération de la table de métas
			\create_tableau_mimetype();
			$mimetype = \trouve_mimetype($filename,extension_fichier($name));
			//on commence avec la gymnatisque des métas...
			if($mimetype == "application/epub+zip"){
				//pour les ebook, on gère ca directement ici !
				$epub = new \epubData(realpath($filename));
				$metas=$epub->metas;
				$img = imagecreatefromstring($epub->getCoverContent());
				$file=tempnam(sys_get_temp_dir(),"vign");
				imagepng($img,$file);
				$metas['thumbnail_content'] = file_get_contents($file);
				unlink($file);
			}else{
				$metas = \extract_metas(realpath($filename),$mimetype);
			}

			if($from_music != ""){
				switch($from_music){
					case "submanif" :
						//on retrouve la notice de la nomenclature générale associé
						$notice_relations = new \notice_relations($notice_id);
						$parents = $notice_relations->get_parents();
						if(isset($parents[$pmb_nomenclature_record_children_link][0])) {
								$manif_id = $parents[$pmb_nomenclature_record_children_link][0]->get_linked_notice();
						}
						break;
				}
				$query = 'select scan_request_as_folder, scan_request_folder_num_notice from scan_requests where id_scan_request = '.$this->scan_request->get_id();
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					// il y a un dossier demandé
					$row = pmb_mysql_fetch_object($result);
					if ($row->scan_request_as_folder) {
						if (!$row->scan_request_folder_num_notice) {
							$this->scan_request->create_folder_record();
							$notice_id = $this->scan_request->get_folder_num_notice();
						} else {
							$notice_id = $row->scan_request_folder_num_notice;
						}
						$bulletin_id = 0;
					}else if ($metas['Description'] != ''){
						//ya des métas qui vont bien pour les nomemclatures
						if(preg_match_all("/[Ii](\d+)/", $metas['Description'],$matches)){
							// on vient de récupérer la liste des IDs de notices
							$notices = $matches[1];
							// La notice courante est dedans, on poursuit le traitement
							if(in_array($notice_id,$notices)){
								//on s'assure que tous les IDs cités sont dedans (enfin on ne traite que ceux la)
								$notice_relations = new \notice_relations($this->record->id);
								$childs = $notice_relations->get_childs();
								if(isset($childs[$pmb_nomenclature_record_children_link])) {
									$child_exists = false;
									foreach ($childs[$pmb_nomenclature_record_children_link] as $child) {
										if(in_array($child->get_linked_notice(), $notices)) {
											// on ajoute le docnum sur la sous-manif...
											$this->save_explnum($filename, $name, $child->get_linked_notice(), $bulletin_id,$manif_id,true);
											$child_exists = true;
										}
									}
									if($child_exists) {
										// on a conservé le document pour pouvoir le dupliquer autant de fois que de sous-manif... on peut faire le tri
										unlink($filename);
										return true;
									}
								}
							}
						}
					}
				}
				//Document sans métas déposé sur un dossier de la musique
				$this->save_explnum($filename, $name, $notice_id, $bulletin_id,$manif_id);
				return true;
			}
			//Si on n'est pas déjà sorti, c'est un cas classique, on ajoute juste le document
			$this->save_explnum($filename, $name, $notice_id, $bulletin_id);

			return true;
		}else{
			//on a pas le droit d'écriture
			throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $name . ')');
			return false;
		}
	}

	protected function save_explnum($filename,$name,$notice_id,$bulletin_id,$manifestation_nomenclature=0,$keep_file=false){
		global $id_rep;
		$explnum = new \explnum(0, $notice_id, $bulletin_id);
		$id_rep = $this->config['upload_rep'];
		if($keep_file){
			copy($filename, $filename.'.bak');
		}
		$explnum->get_file_from_temp($filename,$name,$this->config['up_place']);
		$explnum->params['explnum_statut'] = $this->config['default_docnum_statut'];
		$explnum->update();
		if($this->scan_request->get_concept_uri()){
			$concept = new \concept(0,$this->scan_request->get_concept_uri());
			$index_concept = new \index_concept($explnum->explnum_id, TYPE_EXPLNUM);
			$index_concept->add_concept($concept);
			$index_concept->save(false);
		}
		if ($notice_id) {
			$this->update_notice($notice_id);
		}
		$manifestation_nomenclature = intval($manifestation_nomenclature);
		$this->add_explnum($manifestation_nomenclature ? $manifestation_nomenclature : $notice_id, $bulletin_id, $explnum->explnum_id);
		if(!$keep_file && file_exists($filename)){
			unlink($filename);
		}else{
			copy($filename.'.bak', $filename);
		}
		return true;
	}
}