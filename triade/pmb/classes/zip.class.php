<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: zip.class.php,v 1.3 2017-06-30 14:08:17 dgoron Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class zip {
	public $zipPath;
	public $entries=array();
	public $zip; //archive
	
	public function __construct($zipPath){
		$this->zipPath = $zipPath;
	}
	
	public function readZip() {
		/* Ouverture de l'archive et lecture des entrées */
 		$this->zip = zip_open($this->zipPath);
 		if (is_resource($this->zip)) {
			while ($zip_entry = zip_read($this->zip)) {
 				if(substr(zip_entry_name($zip_entry),strlen(zip_entry_name($zip_entry))-1) != "/"){
					if(strrpos(zip_entry_name($zip_entry),"/")!=0) $start = strrpos(zip_entry_name($zip_entry),"/")+1;
					else $start = 0;
					$fileName = substr(zip_entry_name($zip_entry),$start);
					$t['fileName'] = $fileName;
					$t['zipEntry']= $zip_entry; 
					$t['zipEntryName'] = zip_entry_name($zip_entry);		
 					$this->entries[$t['zipEntry']]=$t;
 				}
			}
 		}
	}
	
	public function getFileContent($fileName){
		if(!$this->zip) $this->readZip();
		$content = "";
		foreach($this->entries as $file){
			if($file['fileName'] == $fileName)
				 $content = zip_entry_read($file['zipEntry'],zip_entry_filesize($file['zipEntry']));
		}
		return $content;
	}
}
?>