<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: zip.class.php,v 1.4.10.1 2025/01/16 10:24:13 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class zip {
	public $zipPath;
	public $entries=array();
	public $zip; //archive
	public $nb_pages;
	public $pagesSizes;
	public $tmp_path = array();

	public function __construct($zipPath){
		if (is_file($zipPath)){
			$this->zipPath = $zipPath;
			$this->readZip();
		} else {
			print ("Archive non trouvé : '".$zipPath."'.\n");
		}
	}

	public function readZip() {
		/* Ouverture de l'archive et lecture des entrées */
 		$this->zip = zip_open($this->zipPath);
 		if (is_resource($this->zip)) {
			while ($zip_entry = zip_read($this->zip)) {
				$zip_entry_name = zip_entry_name($zip_entry);
				if(substr($zip_entry_name,-1) === '/'){
					continue;
				}
				$this->entries[$zip_entry_name]=$zip_entry;
			}
			ksort($this->entries, SORT_NATURAL | SORT_FLAG_CASE);
 		}
	}

	public function get_file_content($file_path){
		if(!$this->tmp_path[$file_path]){
			$this->tmp_path[$file_path]=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));
			$fp = fopen($this->tmp_path[$file_path], "w+");
			$content = zip_entry_read($this->entries[$file_path],zip_entry_filesize($this->entries[$file_path]));
			fwrite($fp, $content);
			fclose($fp);
		}else{
			$content = file_get_contents($this->tmp_path[$file_path]);
		}
		return $content;
	}

	//retourne un chemin vers le fichier
	public function get_file($file_path){
		if(!$this->tmp_path[$file_path]){
			$this->get_file_content($file_path);
		}
		return $this->tmp_path[$file_path];
	}

	public function getNbPages(){
		if(!$this->nb_pages){
			//le nombre de page
			$this->nb_pages =  count($this->entries);
		}
		return $this->nb_pages;
	}

	public function get_page_content($num_page=1){
		$content = "";
		$page = 1;
		foreach ($this->entries as $entry) {
			if($page == $num_page) {
				$content = zip_entry_read($entry,zip_entry_filesize($entry));
			}
			$page++;
		}
		return $content;
	}

	public function getPagesSizes(){
		//pour chaque page
		if(!$this->pagesSizes){
			$page = 1;
			foreach ($this->entries as $entry) {
				$content = zip_entry_read($entry,zip_entry_filesize($entry));
				$src_img = imagecreatefromstring($content);
				$this->pagesSizes[$page] =array(
						'width' => imagesx($src_img),
						'height'=>  imagesy($src_img)
				);
				$page++;
			}
		}
		return $this->pagesSizes;
	}
}
?>