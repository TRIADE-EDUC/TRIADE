<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesScanDocnum.class.php,v 1.8 2023/09/22 15:04:56 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");
require_once($class_path."/explnum.class.php");

class pmbesScanDocnum extends external_services_api_class {
	
	public function get_doc_num($explnum, $upload_folder) {
		global $pmb_set_time_limit, $base_path;		
		$report=array();
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			@set_time_limit($pmb_set_time_limit);
			
			if(is_array($explnum) && $upload_folder) {
				
				$idLien='';
				if($explnum['explnum_bulletin']){
					$query="SELECT 1 FROM bulletins WHERE bulletin_id=".$explnum['explnum_bulletin'];
					$result=pmb_mysql_query($query);
					
					if(pmb_mysql_num_rows($result)){
						$idLien='explnum_bulletin='.$explnum['explnum_bulletin'];
					}
				}elseif($explnum['explnum_notice']){
					$query="SELECT 1 FROM notices WHERE notice_id=".$explnum['explnum_notice'];
					$result=pmb_mysql_query($query);
						
					if(pmb_mysql_num_rows($result)){
						$idLien='explnum_notice='.$explnum['explnum_notice'];
					}
				}

				if($idLien){
					if(file_exists($upload_folder.$explnum['explnum_nomfichier'])){
					
						if(!$explnum['explnum_nom']){
							$explnum['explnum_nom']=$explnum['explnum_nomfichier'];
						}
						
						if(!$explnum['explnum_repertoire']){
							$explnum['explnum_repertoire']='1';
						}
						
						if(!$explnum['explnum_statut']){
							$explnum['explnum_statut']='0';
						}
						
						if(!$explnum['explnum_path']){
							$explnum['explnum_path']='/';
						}
						
						if(!$explnum['explnum_extfichier']){
							$match=array();
							if(preg_match('/.+\.(.+)$/', $explnum['explnum_nomfichier'], $match)){
								$explnum['explnum_extfichier']=$match[1];
							}
						}
						
						if(!$explnum['explnum_mimetype']){
							$explnum['explnum_mimetype']=trouve_mimetype($upload_folder.$explnum['explnum_nomfichier'],$explnum['explnum_extfichier']);
						}
						
						if(!$explnum['explnum_vignette']){
							$nom_temp = session_id().microtime();
							$nom_temp = str_replace(' ','_',$nom_temp);
							$nom_temp = str_replace('.','_',$nom_temp);
							$fichier_tmp = $base_path."/temp/".$nom_temp;
							copy($upload_folder.$explnum['explnum_nomfichier'],$fichier_tmp);
							$explnum['explnum_vignette']=reduire_image($fichier_tmp);
							unlink($fichier_tmp);
						}
						
						$query='INSERT INTO explnum SET '.$idLien.',
								explnum_nom="'.addslashes($explnum['explnum_nom']).'",
								explnum_mimetype="'.addslashes($explnum['explnum_mimetype']).'",
								explnum_extfichier="'.addslashes($explnum['explnum_extfichier']).'",
								explnum_nomfichier="'.addslashes($explnum['explnum_nomfichier']).'",
								explnum_vignette="'.addslashes($explnum['explnum_vignette']).'",	
								explnum_path="'.addslashes($explnum['explnum_path']).'",
								explnum_repertoire="'.addslashes($explnum['explnum_repertoire']).'",
								explnum_statut="'.addslashes($explnum['explnum_statut']).'"';
						
						pmb_mysql_query($query);
						$explnum['explnum_id']=pmb_mysql_insert_id();
						
						if($explnum['explnum_id']){
							
							//on récup le répertoire dans lequel envoyer le fichier
							$upload_repertoire=new upload_folder($explnum['explnum_repertoire']);
							
							if(!is_dir($upload_repertoire->decoder_chaine($upload_repertoire->repertoire_path))){
								$report['error'][]=$this->msg['get_doc_num_upload_repertoire_do_not_exist'];
								//On efface l'entrée
								$query='DELETE FROM explnum WHERE explnum_id='.$explnum['explnum_id'];
								pmb_mysql_query($query);
							
							}
							if(!copy($upload_folder.$explnum['explnum_nomfichier'],$upload_repertoire->decoder_chaine($upload_repertoire->repertoire_path).$explnum['explnum_nomfichier'])){
								$report['error'][]=$this->msg['get_doc_num_rename_error'];
								//On efface l'entrée
								$query='DELETE FROM explnum WHERE explnum_id='.$explnum['explnum_id'];
								pmb_mysql_query($query);
							}else{
							    unlink($upload_folder.$explnum['explnum_nomfichier']);
								//Réussi ici, on réindex et on incrémente le résultat
								$index = new indexation_docnum($explnum['explnum_id']);
								$index->indexer();
								
								$report['info']=1;
							}
							
						}else{
							//erreur de déplacement ou de création
							$report['error'][]=$this->msg['get_doc_num_cant_create_explnum'];
						}
						
					}else{
						$report['error'][]=$this->msg['get_doc_num_file_not_found'];
						//On efface l'entrée
						$query='DELETE FROM explnum WHERE explnum_id='.$explnum['explnum_id'];
						pmb_mysql_query($query);
					}
				}else{
					//doc num sans id notice ou bulletin
					$report['error'][]=$this->msg['get_doc_num_missing_link'];
				}
			}else{
				//il manque un param
				$report['error'][]=$this->msg['get_doc_num_missing_param'];
			}
		}
		return $report;
	}
}