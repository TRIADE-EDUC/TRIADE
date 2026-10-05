<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_task_docnum.class.php,v 1.4 2023/03/28 13:02:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path.'/explnum.inc.php');

class scheduler_task_docnum{
	
	public $id = 0;
	public $nomfichier = '';
	public $mimetype = '';
	public $data = '';
	public $extfichier = '';
	public $repertoire = '';
	public $path = '';
	public $num_tache = 0;
	public $file=array();
	
	/*
	 * Constructeur
	 */
	public function __construct($id=0){
		$this->id = intval($id);
		if($this->id) {
			$this->fetch_data();
		}
	}
	
	protected function fetch_data() {
		$query = "select * from taches_docnum where id_tache_docnum='".$this->id."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$this->nomfichier = $row->tache_docnum_nomfichier;
			$this->data = $row->tache_docnum_data;
			$this->mimetype = $row->tache_docnum_mimetype;
			$this->extfichier = $row->tache_docnum_extfichier;
			$this->repertoire = $row->tache_docnum_repertoire;
			$this->path = $row->tache_docnum_path;
			$this->num_tache = $row->num_tache;
		}
	}
	
	/*
	 * Suppression
	 */
	public function delete(){
		$query = "delete from taches_docnum where id_tache_docnum='".$this->id."'";
		pmb_mysql_query($query);
	}
	
	/*
	 * Enregistrement
	 */
	public function save(){
		if(!$this->id){
			//Création
			$query = "insert into taches_docnum set 
					 tache_docnum_nomfichier='".addslashes($this->nomfichier)."',
					 tache_docnum_mimetype='".addslashes($this->mimetype)."',
					 tache_docnum_extfichier='".addslashes($this->extfichier)."',
					 tache_docnum_data='".addslashes($this->data)."',
					 tache_docnum_repertoire='".addslashes($this->repertoire)."',
					 tache_docnum_path='".addslashes($this->path)."',
					 num_tache='".addslashes($this->num_tache)."'
					 ";
			pmb_mysql_query(${$query});
			$this->id = pmb_mysql_insert_id();
		} else{
			//Modification
			$query = "update taches_docnum set  
					 tache_docnum_nomfichier='".addslashes($this->nomfichier)."',
					 tache_docnum_mimetype='".addslashes($this->mimetype)."',
					 tache_docnum_extfichier='".addslashes($this->extfichier)."',
					 tache_docnum_data='".addslashes($this->data)."',
					 tache_docnum_repertoire='".addslashes($this->repertoire)."',
					 tache_docnum_path='".addslashes($this->path)."',
					 num_tache='".addslashes($this->num_tache)."'
					 where id_tache_docnum='".$this->id."'";
			pmb_mysql_query($query);
		}
	}
	
	/*
	 * Charge le fichier
	 */
	public function load_file($file_info=array()){
		if($file_info){
			$this->file = $file_info;
		}
	}	
	
	/*
	 * Analyse du fichier pour en récupérer le contenu et les infos
	 */
	
	public function analyse_file(){
		
		if($this->file){
			
			create_tableau_mimetype();
			$userfile_name = $this->file['name'] ;
			$userfile_temp = $this->file['tmp_name'] ;
			$userfile_moved = basename($userfile_temp);
			$userfile_name = preg_replace("/ |'|\\|\"|\//m", "_", $userfile_name);
			$userfile_ext = '';
			if ($userfile_name) {
				$userfile_ext = extension_fichier($userfile_name);
			}		
			move_uploaded_file($userfile_temp,"./temp/".$userfile_moved);
			$file_name = "./temp/".$userfile_moved;
			$fp = fopen($file_name , "r" ) ;
			$contenu = fread ($fp, filesize($file_name));
			fclose ($fp) ;
			$mime = trouve_mimetype($userfile_moved,$userfile_ext) ;
			if (!$mime) $mime="application/data";
			
			$this->mimetype = $mime;
			$this->nomfichier = $userfile_name;
			$this->extfichier = $userfile_ext;
			$this->data = $contenu;
			
			unlink($file_name);
		}
	}
}
?>