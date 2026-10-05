<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_docnum_planning.class.php,v 1.1 2017/07/10 15:50:02 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");

class scan_docnum_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $dbh,$charset;
		global $deflt_upload_repertoire;
		
		//On créer le sélecteur pour choisir le repertoire d'upload 
		$query="SELECT * FROM upload_repertoire";
		$result=pmb_mysql_query($query,$dbh);
		
		$select="";
		if(pmb_mysql_num_rows($result)){
			$select.="<select name='upload_repertoire'>";
			$allready_selected=false;
			while($upload_rep=pmb_mysql_fetch_object($result)){
				if(isset($param['upload_repertoire']) && $param['upload_repertoire']==$upload_rep->repertoire_id && !$allready_selected){
					$select.="	<option selected='true' value='$upload_rep->repertoire_id'>$upload_rep->repertoire_nom</option>";
					$allready_selected=true;
				}elseif($deflt_upload_repertoire==$upload_rep->repertoire_id && !$allready_selected){
					$select.="	<option selected='true' value='$upload_rep->repertoire_id'>$upload_rep->repertoire_nom</option>";
					$allready_selected=true;
				}else{
					$select.="	<option value='$upload_rep->repertoire_id'>$upload_rep->repertoire_nom</option>";
				}
			}
			$select.="</select>";
		}else{
			$select.=$this->msg['planificateur_scan_docnum_no_upload_repertoire'];
		}
		
		$form_task = "
		<div class='row'>
			<div class='colonne3'>
				<label for='upload_folder'>".$this->msg["planificateur_scan_docnum_upload_repertoire"]."</label>
			</div>
			<div class='colonne_suite'>
				$select	
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='upload_folder'>".$this->msg["planificateur_scan_docnum_upload_folder"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' id='upload_folder' name='upload_folder' value='".(isset($param['upload_folder']) ? htmlentities($param['upload_folder'],ENT_QUOTES,$charset) : '')."'/>
			</div>
		</div>";
		
		return $form_task;
	}
	
	public function make_serialized_task_params() {
    	global $upload_folder,$upload_repertoire;

		$t = parent::make_serialized_task_params();
		
		if ($upload_folder) {
			$t["upload_folder"]=stripslashes($upload_folder);
		}
		
		if ($upload_repertoire){
			$t["upload_repertoire"]=stripslashes($upload_repertoire);
		}
		
    	return serialize($t);
	}
}