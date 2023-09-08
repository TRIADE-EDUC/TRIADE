<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_task_docnum.class.php,v 1.2 2017-07-12 15:15:02 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/explnum.inc.php');

class scheduler_task_docnum{
	
	public $id_tache_docnum = 0;
	public $tache_docnum_nomfichier = '';
	public $tache_docnum_mimetype = '';
	public $tache_docnum_data = '';
	public $tache_docnum_extfichier = '';
	public $tache_docnum_repertoire;
	public $tache_docnum_path ='';
	public $num_tache ='';
	public $tache_docnum_file=array();
	
	/*
	 * Constructeur
	 */
	public function __construct($id_docnum=0){
		$this->id_tache_docnum = $id_docnum+0;
		$this->tache_docnum_nomfichier = '';
		$this->tache_docnum_data = '';
		$this->tache_docnum_mimetype = '';
		$this->tache_docnum_extfichier = '';
		$this->tache_docnum_repertoire = '';
		$this->tache_docnum_path = '';
		$this->num_tache = '';
		if($this->id_tache_docnum){
			$query = "select * from taches_docnum where id_tache_docnum='".$this->id_tache_docnum."'";
			$res=pmb_mysql_query($query);
			if(pmb_mysql_num_rows($res)){
				$tdn = pmb_mysql_fetch_object($res);
				$this->tache_docnum_nomfichier = $tdn->tache_docnum_nomfichier;
	 			$this->tache_docnum_data = $tdn->tache_docnum_data;
	 			$this->tache_docnum_mimetype = $tdn->tache_docnum_mimetype;
				$this->tache_docnum_extfichier = $tdn->tache_docnum_extfichier;
				$this->tache_docnum_repertoire = $tdn->tache_docnum_repertoire;
				$this->tache_docnum_path = $tdn->tache_docnum_path;
				$this->num_tache = $tdn->num_tache;
			}
		}
	}
	
	/*
	 * Suppression
	 */
	public function delete(){
		$query = "delete from taches_docnum where id_tache_docnum='".$this->id_tache_docnum."'";
		pmb_mysql_query($query);
	}
	
	/*
	 * Enregistrement
	 */
	public function save(){
		if(!$this->id_tache_docnum){
			//Création
			$query = "insert into taches_docnum set 
					 tache_docnum_nomfichier='".addslashes($this->tache_docnum_nomfichier)."',
					 tache_docnum_mimetype='".addslashes($this->tache_docnum_mimetype)."',
					 tache_docnum_extfichier='".addslashes($this->tache_docnum_extfichier)."',
					 tache_docnum_data='".addslashes($this->tache_docnum_data)."',
					 tache_docnum_repertoire='".addslashes($this->tache_docnum_repertoire)."',
					 tache_docnum_path='".addslashes($this->tache_docnum_path)."',
					 num_tache='".addslashes($this->num_tache)."'
					 ";
			pmb_mysql_query(${$query});
			$this->id_tache_docnum = pmb_mysql_insert_id();
		} else{
			//Modification
			$query = "update taches_docnum set  
					 tache_docnum_nomfichier='".addslashes($this->tache_docnum_nomfichier)."',
					 tache_docnum_mimetype='".addslashes($this->tache_docnum_mimetype)."',
					 tache_docnum_extfichier='".addslashes($this->tache_docnum_extfichier)."',
					 tache_docnum_data='".addslashes($this->tache_docnum_data)."',
					 tache_docnum_repertoire='".addslashes($this->tache_docnum_repertoire)."',
					 tache_docnum_path='".addslashes($this->tache_docnum_path)."',
					 num_tache='".addslashes($this->num_tache)."'
					 where id_tache_docnum='".$this->id_tache_docnum."'";
			pmb_mysql_query($query);
		}
	}
	
	/*
	 * Charge le fichier
	 */
	public function load_file($file_info=array()){
		if($file_info){
			$this->tache_docnum_file = $file_info;
		}
	}	
	
	/*
	 * Analyse du fichier pour en récupérer le contenu et les infos
	 */
	
	public function analyse_file(){
		
		if($this->tache_docnum_file){
			
			create_tableau_mimetype();
			$userfile_name = $this->tache_docnum_file['name'] ;
			$userfile_temp = $this->tache_docnum_file['tmp_name'] ;
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
			
			$this->tache_docnum_mimetype = $mime;
			$this->tache_docnum_nomfichier = $userfile_name;
			$this->tache_docnum_extfichier = $userfile_ext;
			$this->tache_docnum_data = $contenu;
			
			unlink($file_name);
		}
	}
	
	/*
	 * Affecte un nom de fichier si il a été défini
	 */
	public function setName($nom=''){
		if($nom)
			$this->tache_docnum_nomfichier = stripslashes($nom);
	}
	
	/*
	 * Affiche les documents numériques dans un tableau
	 */
	public function show_docnum_table($docnum_tab=array()){
		global $charset;
		
		create_tableau_mimetype();
		$display = "";
		if($docnum_tab){
			$nb_doc = 0;

			for($i=0;$i<count($docnum_tab);$i++){
				$nb_doc++;
				
				if($nb_doc == 1) $display .= "<tr >";
				$alt = htmlentities($docnum_tab[$i]['tache_docnum_nomfichier'],ENT_QUOTES,$charset).' - '.htmlentities($docnum_tab[$i]['tache_docnum_mimetype'],ENT_QUOTES,$charset);
				$display .= "<td class='docnum' style='width:25%;border:1px solid #CCCCCC;padding : 5px 5px'>
						<a target='_blank' alt='$alt' title='$alt' href=\"./tache_docnum.php?tache_docnum_id=".$docnum_tab[$i]['id_tache_docnum']."\">
							<img src='./images/mimetype/".icone_mimetype($docnum_tab[$i]['tache_docnum_mimetype'],$docnum_tab[$i]['tache_docnum_extfichier'])."' alt='$alt' title='$alt' >
						</a>
						<br />
						<a target='_blank' href='./tache_docnum.php?tache_docnum_id=".$docnum_tab[$i]['id_tache_docnum']."'>".htmlentities($docnum_tab[$i]['tache_docnum_nomfichier'],ENT_QUOTES,$charset)."
						</a>
						<div class='explnum_type'>".$docnum_tab[$i]['tache_docnum_mimetype']."</div>
						</td>
					";
				if($nb_doc == 4) {
					$display .= "</tr>";
					$nb_doc=0;
				}
			}
		}
		return $display;
	}
}
?>