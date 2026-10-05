<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: params.interface.php,v 1.15.4.1 2025/01/30 09:08:07 tsamson Exp $
 
 //on défini les méthodes à implémenter pour une classe de paramétrage...

interface params{
 	//renvoi un paramètre
 	public function getParam($parameter);
 	//renvoi le nombre de documents
 	public function getNbDocs();
 	//renvoi le document courant
 	public function getCurrentDoc();
 	//renvoi le suivant
 	public function getDoc($numDoc);
}

class base_params implements params {
	public $listeDocs = array();		//tableau de documents
	public $listeMimetypes = array();	//tableau listant les différents mimetypes des documents
	public $current = 0;				//position courante dans le tableau
	public $currentDoc = array();			//Document courant
	public $currentMimetype = "";		//mimetype courant
	public $params;					//tableau de paramètres utiles pour la recontructions des requetes...et même voir plus
	public $position = 0;				//
	public $listeBulls = array();
	public $listeNotices = array();
	public $driver_name="";
	
	public function getParam($parameter){
		return $this->params[$parameter];
	}
	
	public function getNbDocs(){
		return is_countable($this->listeDocs) ? sizeof($this->listeDocs) : 0;
	}
	
	public function getCurrentDoc(){
		return $this->currentDoc;
	}

	//renvoi un document précis sinon renvoi faux
 	public function getDoc($numDoc){
 		if($numDoc >= 0 && $numDoc <= $this->getNbDocs()-1){
 			$this->current = $numDoc;
 			return $this->getCurrentDoc();
 		}else return false;
 	}
	
 	public function isInCache($id){
 		global $visionneuse_path;
 		return file_exists($visionneuse_path."/temp/".$this->driver_name."_".$id);
  	}
 	
 	public function setInCache($id,$data){
 		global $visionneuse_path;
 		$fdest = fopen($visionneuse_path."/temp/".$this->driver_name."_".$id,"w+");
 		fwrite($fdest,$data);
 		fclose($fdest);
 	}
 	
 	public function readInCache($id){
 		global $visionneuse_path;
  		$data = "";
  		$data = file_get_contents($visionneuse_path."/temp/".$this->driver_name."_".$id);	
 		return $data;	
 	}
 	
 	public function get_cached_filename($id){
 		global $visionneuse_path;
 		return realpath($visionneuse_path)."/temp/".$this->driver_name."_".$id;
 	}
 	
 	public function get_cached_url_filename($id){
 	    return $this->getUrlBase()."visionneuse/temp/".$this->driver_name."_".$id;
 	}
 	
 	public function cleanCache(){
 		global $visionneuse_path;

	    $dh = opendir($visionneuse_path."/temp/");
	    if (!$dh) return;
	    $files = array();
	    $totalSize = 0;
	
	    while (($file = readdir($dh)) !== false){
	        if ($file != "." && $file != ".." && $file != "dummy.txt" && $file != "CVS") {
		    	$stat = stat($visionneuse_path."/temp/".$file);
	        	$files[$file] = array("mtime"=>$stat['mtime']);
	        	$totalSize += $stat['size'];
	        }
	    }
 		closedir($dh);
		$deleteList = array();
		foreach ($files as $file => $stat) {
			//si le dernier accès au fichier est de plus de 3h, on vide...
			if( (time() - $stat["mtime"] > (3600*3)) ){
				if(is_dir($visionneuse_path."/temp/".$file)){
					$this->rrmdir($visionneuse_path."/temp/".$file);
				}else{
					unlink($visionneuse_path."/temp/".$file);
				}
			}	
		}
 	}
 	
 	public function rrmdir($dir){
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
    
	public function is_allowed($explnum_id){	
		$docnum_visible = true;
		return $docnum_visible;
	}
    
	
	public function is_downloadable($explnum_id){
		return true;
	}
	
	public function getMimetypeConf(){
		global $opac_visionneuse_params;
		return unserialize(htmlspecialchars_decode($opac_visionneuse_params));
	}
	
	public function getUrlImage($img){
		global $opac_url_base;
	
		if($img !== "")
			$img = $opac_url_base."images/".$img;
			
		return $img;
	}
	
	public function getUrlBase(){
		global $opac_url_base;
		return $opac_url_base;
	}
	
	public function getClassParam($class){
		$params = serialize(array());
		if($class != ""){
			$req="SELECT visionneuse_params_parameters FROM visionneuse_params WHERE visionneuse_params_class LIKE '$class'";
			if($res=pmb_mysql_query($req)){
				if(pmb_mysql_num_rows($res)){
					$result = pmb_mysql_fetch_object($res);
					$params = htmlspecialchars_decode($result->visionneuse_params_parameters);
				}
			}
		}
		return $params;
	}
	
	public function copyCurrentDocInCache(){
		copy($this->currentDoc['path'],$this->get_cached_filename($this->currentDoc['id']));
	}
	
}
?>