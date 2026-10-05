<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: upload_folder.class.php,v 1.8 2023/08/28 14:01:11 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class upload_folder {
	
	public $repertoire_id=0;
	public $action='';
	public $nb_enregistrement=0;
	public $repertoire_nom='';
	public $repertoire_url='';
	public $repertoire_path='';
	public $repertoire_navigation=0;
	public $repertoire_hachage=0;
	public $repertoire_subfolder=20;
	public $repertoire_utf8=1;
	
	public function __construct($id=0, $action=''){
		$this->repertoire_id = intval($id);
		$this->action = $action;	
		
		if($this->repertoire_id){
			$req="select repertoire_nom, repertoire_url, repertoire_path, repertoire_navigation, repertoire_hachage, repertoire_subfolder, repertoire_utf8 from upload_repertoire where repertoire_id='".$this->repertoire_id."'";
			$res=pmb_mysql_query($req);
			if(pmb_mysql_num_rows($res)){
				$item = pmb_mysql_fetch_object($res);
				$this->repertoire_nom=$item->repertoire_nom;
				$this->repertoire_url=$item->repertoire_url;
				$this->repertoire_path=$item->repertoire_path;
				$this->repertoire_navigation=$item->repertoire_navigation;
				$this->repertoire_hachage=$item->repertoire_hachage;
				$this->repertoire_subfolder=$item->repertoire_subfolder;
				$this->repertoire_utf8=$item->repertoire_utf8;
			} 
		}
	}
	
	/**
	 * Formate le nom du chemin en utilisant le nom de rep
	 */
	public function formate_path_to_nom($chemin){			
		$chemin = str_replace($this->repertoire_path,$this->repertoire_nom."/",$chemin);
		$chemin = str_replace('//','/',$chemin);
		
		return $chemin;
	}
	
	/**
	 * Formate le nom du chemin en utilisant l'id du répertoire
	 */
	public function formate_path_to_id($chemin){			
		$chemin = str_replace($this->repertoire_path,$this->repertoire_id."/",$chemin);
		$chemin = str_replace('//','/',$chemin);
		
		return $chemin;
	}
	
	/*
	 * Retourne si le repertoire est haché
	 */
	public function isHashing(){
		return $this->repertoire_hachage;
	}
	
	/*
	 * Retourne si le repertoire est en utf8
	 */
	public function isUtf8(){
		return $this->repertoire_utf8;
	}
	
	/*
	 * Hache le nom de fichier pour le classer
	 */
	public function hachage($nom_fichier){
		$chemin= $this->repertoire_path;
		$nb_dossier = $this->repertoire_subfolder;
		$total=0;
		for($i=0;$i<strlen($nom_fichier);$i++){				
			$total += ord($nom_fichier[$i]);
		}		
		$total = $total % $nb_dossier;		
		$rep_hash = $chemin.$total."/";
		$rep_hash = str_replace("//","/",$rep_hash);
		
		return $rep_hash;
	}
	
	/*
	 * décode la chaine dans le bon charset
	 */
	public function decoder_chaine($chaine){
		global $charset;
		
		if($charset != 'utf-8' && $this->isUtf8()) {
			return encoding_normalize::utf8_decode($chaine);
		} else if($charset == 'utf-8' && !$this->isUtf8()) {
			return encoding_normalize::utf8_normalize($chaine);
		}
		return $chaine;
	}
	
	/*
 	 * encode la chaine dans le bon charset
	 */
	public function encoder_chaine($chaine){
		global $charset;
		
		if($charset != 'utf-8' && $this->isUtf8()) {
			return encoding_normalize::utf8_normalize($chaine);
		} else if($charset == 'utf-8' && !$this->isUtf8()) {
			return encoding_normalize::utf8_decode($chaine);
		}
		return $chaine;
	}
	
	public function get_path($filename){
		$path = "";
		if($this->isHashing()) $path = $this-> hachage($filename);
		else $path = $this->repertoire_path;
		return $path;
	}
	
	public static function get_upload_folders() {
		$folders = array();
		$query = "
				SELECT repertoire_id AS id, 
				repertoire_nom AS name, 
				repertoire_path AS path, 
				repertoire_navigation AS navigation,
				repertoire_subfolder AS nb_levels
				FROM upload_repertoire
		";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$folders[$row["id"]] = $row;
				$folders[$row["id"]]['formatted_path_name'] = $row['name'];
				$folders[$row["id"]]['formatted_path_id'] = $row['id'];
				if ($row["navigation"]) {
					$upload_folder = new upload_folder($row["id"]);
					$sub_folders = self::get_sub_folders($row["path"], $upload_folder, $row["nb_levels"]);
					if (count($sub_folders)) {
						$folders[$row["id"]]['sub_folders'] = $sub_folders;
					}
				}
				
			}
		}
		return $folders;
	}
	
	/**
	 * 
	 * @param string $folder_path
	 * @param upload_folder $upload_folder
	 * @param number $nb_levels
	 * @param number $occurence
	 * @return array:
	 */
	public static function get_sub_folders($folder_path, $upload_folder, $nb_levels = 20, $occurence = 1) {
		$tree = array();
		if ($occurence <= $nb_levels) {
			$occurence++;
			if ($folder_path && is_dir($folder_path)) {
				if(($files = @scandir($folder_path)) !== false) {
					for ($i=0;$i<sizeof($files);$i++) {
						if($files[$i] != '.' && $files[$i] != '..'){
							$dir_name = $files[$i];
							$path = $folder_path.$dir_name."/";
							if (is_dir($path)) {
								$tree[] = array(
										'name' => addslashes($upload_folder->decoder_chaine($dir_name)),
										'path' => addslashes($upload_folder->decoder_chaine($path)),
										'formatted_path_name' => $upload_folder->formate_path_to_nom($path),
										'formatted_path_id' => $upload_folder->formate_path_to_id($path),
										'sub_folders' => self::get_sub_folders($path, $upload_folder, $nb_levels, $occurence),
								);
							}
						}
					}
				}
			}
		}
		return $tree;
	}
	
}

