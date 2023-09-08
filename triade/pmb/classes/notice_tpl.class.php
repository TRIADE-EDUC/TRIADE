<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_tpl.class.php,v 1.15 2018-12-20 11:00:19 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$include_path/templates/notice_tpl.tpl.php");
require_once("$class_path/notice_tpl_gen.class.php");
require_once("$class_path/marc_table.class.php");

class notice_tpl {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------	
	public $id;		// MySQL id in table 'notice_tpl'
	public $name;		// nom du template
	public $comment;	// description du template
	public $code ; // Code du template
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {			
		$this->id = intval($id);
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos 
	// ---------------------------------------------------------------
	public function getData() {
		global $dbh,$msg;

		$this->name = '';			
		$this->comment = '';
		$this->id_test = '';
		$this->code=array();
		$this->code[0]=array();
		
		$req_loc="select idlocation,location_libelle from docs_location";
		$res_loc=pmb_mysql_query($req_loc);
		
		$this->location_label[0]=$msg["all_location"];
		if (pmb_mysql_num_rows($res_loc)) {	
			while (($r=pmb_mysql_fetch_object($res_loc))) {
				$this->code[$r->idlocation]=array();
				$this->location_label[$r->idlocation]=$r->location_libelle;
			}
		}	
		$source = marc_list_collection::get_instance("doctype");
		$source_tab = $source->table;
		$type_doc[0]="";
		$this->type_doc_label[0]=$msg["tous_types_docs"];
		foreach($source_tab as $key=>$libelle) {
			$type_doc[$key]="";
			$this->type_doc_label[$key]=$libelle;
		}
		foreach($this->code as $key =>$val) {
			$this->code[$key]["0"]=$type_doc;
			$this->code[$key]["m"]=$type_doc;
			$this->code[$key]["a"]=$type_doc;
			$this->code[$key]["s"]=$type_doc;
			$this->code[$key]["b"]=$type_doc;
		}
		$this->type_notice["0"]=$msg["notice_tpl_notice_all"];
		$this->type_notice["m"]=$msg["notice_tpl_notice_monographie"];
		$this->type_notice["a"]=$msg["notice_tpl_notice_article"];
		$this->type_notice["s"]=$msg["notice_tpl_notice_periodique"];
		$this->type_notice["b"]=$msg["notice_tpl_notice_bulletin"];
	
		if($this->id) {
			$requete = "SELECT * FROM notice_tpl WHERE notpl_id='".$this->id."' LIMIT 1 ";
			$result = @pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);				
				$this->name	= $temp->notpl_name;
				$this->comment	= $temp->notpl_comment;
				$this->show_opac	= $temp->notpl_show_opac;					
				$this->id_test	= $temp->notpl_id_test;			
				$requete = "SELECT * FROM notice_tplcode  WHERE num_notpl='".$this->id."' ";
				$result_code = @pmb_mysql_query($requete, $dbh);
				if(pmb_mysql_num_rows($result_code)) {
					while(($temp_code= pmb_mysql_fetch_object($result_code))) {
						$this->code[$temp_code->notplcode_localisation][$temp_code->notplcode_niveau_biblio] [$temp_code->notplcode_typdoc]=$temp_code->nottplcode_code;	
					}
				}
			} else {
				// pas trouvé avec cette clé
				$this->id = 0;								
			}
		}
	}
	
	// ---------------------------------------------------------------
	//		show_list : affichage de la liste des éléments
	// ---------------------------------------------------------------	
	public function show_list($link="./edit.php") {	
		global $dbh, $charset,$msg;
		global $notice_tpl_liste, $notice_tpl_liste_ligne;
		
		$tableau = '';
		$requete = "SELECT * FROM notice_tpl ORDER BY notpl_name ";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			$pair="odd";
			while(($temp = pmb_mysql_fetch_object($result))){	
				$id = $temp->notpl_id;			
				$name = $temp->notpl_name;
				$comment = $temp->notpl_comment;
				if($temp->notpl_show_opac)	$show_opac=$msg["notice_tpl_show_opac_yes"];
				else $show_opac=$msg["notice_tpl_show_opac_no"];
					
				
				if($pair=="even") $pair ="odd";	else $pair ="even";
				// contruction de la ligne
				$ligne=$notice_tpl_liste_ligne;
				
				$ligne = str_replace("!!name!!",	htmlentities($name,ENT_QUOTES, $charset), $ligne);
				$ligne = str_replace("!!comment!!",	htmlentities($comment,ENT_QUOTES, $charset), $ligne);
				$ligne = str_replace("!!show_opac!!",	$show_opac, $ligne);	
				$ligne = str_replace("!!pair!!",	$pair, $ligne);					
				$ligne = str_replace("!!link_edit!!",	$link."?categ=tpl&sub=notice&action=edit&id=$id", $ligne);	
				$ligne = str_replace("!!link_eval!!",	$link."?categ=tpl&sub=notice&action=eval&id=$id&id_test=".$this->id_test, $ligne);	
				$ligne = str_replace("!!link_export!!",	"./export.php?quoi=notice_tpl&id=".$id, $ligne);
				$ligne = str_replace("!!id!!",		$id, $ligne);	
				$tableau.=$ligne;			
			}				
		}
		$liste = str_replace("!!notice_tpl_liste!!",$tableau, $notice_tpl_liste);	
		$liste = str_replace("!!link_ajouter!!",	$link."?categ=tpl&sub=notice&action=edit", $liste);	
		$liste = str_replace("!!link_import!!",	$link."?categ=tpl&sub=notice&action=import", $liste);	
		return $liste;
	}	
	
	// ----------------------------------------------------------------------------------
	//		get_is_truncated_form : affichage du formulaire avec les localisations ou non
	// ----------------------------------------------------------------------------------
	public function get_is_truncated_form() {
		
		$is_truncated_form = true;
		
		if($this->id) {
			//En modif, on vérifie si un template dans une loc
			if (count($this->code)>1) {
				foreach ($this->code as $id_location =>$tab_typenotice) {
					//on passe la loc "0" (toutes les locs)
					if (!$id_location) {
						continue;
					}
					foreach($tab_typenotice as $typenotice =>$tab_typedoc) {
						foreach($tab_typedoc as  $typedoc=>$code) {
							if ($code) {
								$is_truncated_form = false;
								return $is_truncated_form;
							}
						}
					}
				}
			}
		}
		
		return $is_truncated_form;
	}
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form($link="./edit.php") {
	
		global $msg;
		global $notice_tpl_form, $notice_tpl_show_loc_btn;
		global $charset;

		$form=$notice_tpl_form;		
		$action = $link."?categ=tpl&sub=notice&action=update&id=!!id!!";
		
		if($this->id) {
			$libelle = $msg["notice_tpl_modifier"];			
			$button_delete = "<input type='button' class='bouton' value='".$msg['63']."' onClick=\"confirm_delete();\">";
			$action_delete = $link."?categ=tpl&sub=notice&action=delete&id=!!id!!";
			$button_duplicate = "<input type='button' class='bouton' value='".$msg["edit_tpl_duplicate_button"]."' onClick=\"document.location='./edit.php?categ=tpl&sub=notice&action=duplicate&id=".$this->id."';\" />";
			if($this->show_opac) $show_opac=" checked='checked' "; else $show_opac="";			
		} else {			
			$libelle = $msg["notice_tpl_ajouter"];
			$button_delete = "";
			$button_duplicate = "";
			$action_delete= "";
			$show_opac="";
		}
		
		//on n'affiche les localisations que si un template existe dans une des locs
		$is_truncated_form = $this->get_is_truncated_form();
		$form_code = '';
		if ($is_truncated_form) {
			$form_typenotice_all = $this->get_form_typenotice_all(0, $this->code[0]);
			$form_code.=gen_plus("plus_location0",$this->location_label[0],$form_typenotice_all);
		} else {
			foreach($this->code as $id_location =>$tab_typenotice) {
				$form_typenotice_all = $this->get_form_typenotice_all($id_location, $tab_typenotice);
				$form_code.=gen_plus("plus_location".$id_location,$this->location_label[$id_location],$form_typenotice_all);
			}
		}

		$form = str_replace("!!libelle!!",	$libelle, $form);
		$form = str_replace("!!name!!",		htmlentities($this->name,ENT_QUOTES, $charset), $form);
		$form = str_replace("!!comment!!",	htmlentities($this->comment,ENT_QUOTES, $charset), $form);
		$form = str_replace("!!id_test!!",	htmlentities($this->id_test,ENT_QUOTES, $charset), $form);
		$form = str_replace("!!show_opac!!",$show_opac, $form);
		$form = str_replace("!!code_part!!", $form_code, $form);
		if ($is_truncated_form) {
			$form = str_replace("!!show_loc!!", $notice_tpl_show_loc_btn, $form);
		} else {
			$form = str_replace("!!show_loc!!", "", $form);
		}
	
		$form = str_replace("!!action!!",	$action, $form);
		$form = str_replace("!!duplicate!!", $button_duplicate, $form);		
		$form = str_replace("!!delete!!",	$button_delete,	$form);
		$form = str_replace("!!action_delete!!",$action_delete,	$form);
		$form = str_replace("!!id!!",		$this->id, $form);

		return $form;
	}
	
	// ---------------------------------------------------------------------
	//		get_form_typenotice_all : récupère l'affichage des localisations
	// ---------------------------------------------------------------------
	public function get_form_typenotice_all_loc() {
		
		$form_typenotice_all_loc = '';
		
		foreach($this->code as $id_location =>$tab_typenotice) {
			if (!$id_location) {
				continue;
			}
			$line = $this->get_form_typenotice_all($id_location, $tab_typenotice);
			$form_typenotice_all_loc.=gen_plus("plus_location".$id_location,$this->location_label[$id_location],$line);
		}
		
		return $form_typenotice_all_loc;
	}
	
	// ----------------------------------------------------------------------
	//		get_form_typenotice_all : récupère l'affichage d'une localisation
	// ----------------------------------------------------------------------
	public function get_form_typenotice_all($id_location, $tab_typenotice) {
		global $charset;
		global $notice_tpl_form_code;
		
		$form_typenotice_all='';
		
		foreach($tab_typenotice as $typenotice =>$tab_typedoc) {
			$form_code_typedoc='';
			foreach($tab_typedoc as  $typedoc=>$code) {
				$form_code_temp = str_replace("!!loc!!", $id_location, $notice_tpl_form_code);
				$form_code_temp = str_replace("!!typenotice!!",	$typenotice, $form_code_temp);
				$form_code_temp = str_replace("!!typedoc!!", $typedoc, $form_code_temp);
				$form_code_temp = str_replace("!!code!!", htmlentities($code,ENT_QUOTES, $charset), $form_code_temp);
				$form_code_typedoc.= gen_plus("plus_typedoc".$id_location."_".$typenotice."_".$typedoc,$this->type_doc_label["$typedoc"],$form_code_temp);
				if ($code != "") {
					$form_code_typedoc.= "<script type='text/javascript'>
							document.getElementById('plus_typedoc".$id_location."_".$typenotice."_".$typedoc."Child').setAttribute('style','margin-bottom:6px; display:block;width:94%');
							document.getElementById('plus_typedoc".$id_location."_".$typenotice."_".$typedoc."Img').src = imgOpened.src;
							if (document.getElementById('plus_typedoc".$id_location."_".$typenotice."_".$typedoc."Child').parentNode) {
								document.getElementById('plus_typedoc".$id_location."_".$typenotice."_".$typedoc."Child').parentNode.setAttribute('style','margin-bottom:6px; display:block;width:94%');
								if (document.getElementById('plus_typenotice".$id_location."_".$typenotice."_Img')) {
									document.getElementById('plus_typenotice".$id_location."_".$typenotice."_Img').src = imgOpened.src;
								}
								if (document.getElementById('plus_location".$id_location."Img')) {
									document.getElementById('plus_location".$id_location."Img').src = imgOpened.src;
								}
							}
						</script>";
				}
			}
			$form_typenotice_all.= gen_plus("plus_typenotice".$id_location."_".$typenotice."_",$this->type_notice["$typenotice"],$form_code_typedoc);
		}
		
		return $form_typenotice_all;
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression 
	// ---------------------------------------------------------------
	public function delete() {
		global $dbh;
		global $msg;
		
		if(!$this->id)	return $msg[403]; 

		// effacement dans la table
		$requete = "DELETE FROM notice_tpl WHERE notpl_id='".$this->id."' ";
		pmb_mysql_query($requete, $dbh);
		$requete = "DELETE FROM  notice_tplcode  WHERE num_notpl='".$this->id."' ";
		pmb_mysql_query($requete, $dbh);
		
		return false;
	}
	
	
	
	// ---------------------------------------------------------------
	//		update($value) : mise à jour 
	// ---------------------------------------------------------------
	public function update($value) {
	
		global $dbh;
		global $msg;
		global $include_path;
			
		// nettoyage des chaînes en entrée		
		$value['name'] = addslashes(clean_string($value['name']));
		$value['comment'] = addslashes($value['comment']);		
		$value['id_test'] = addslashes($value['id_test']);		
		$value['show_opac'] = addslashes($value['show_opac']);
		
		if(!$value['name'])	return false;
		
		$requete  = "SET  ";
		$requete .= "notpl_name='".$value["name"]."', ";	
		$requete .= "notpl_id_test='".$value["id_test"]."', ";			
		$requete .= "notpl_comment='".$value["comment"]."', ";		
		$requete .= "notpl_show_opac='".$value["show_opac"]."' ";		
		 
		if($this->id) {
			// update
			$requete = "UPDATE notice_tpl $requete WHERE notpl_id=".$this->id." ";
			if(!pmb_mysql_query($requete, $dbh)) {		
				require_once("$include_path/user_error.inc.php"); 
				warning($msg["notice_tpl_modifier"], $msg["notice_tpl_modifier_erreur"]);
				return false;
			}
		} else {
			// creation
			$requete = "INSERT INTO notice_tpl ".$requete;
			if(pmb_mysql_query($requete, $dbh)) {
				$this->id=pmb_mysql_insert_id();				
			} else {
				require_once("$include_path/user_error.inc.php"); 
				warning($msg["notice_tpl_ajouter"], $msg["notice_tpl_ajouter_erreur"]);
				return false;
			}
		}
		
		// insertion du code 
		$requete = "DELETE FROM  notice_tplcode  WHERE num_notpl='".$this->id."' ";
		pmb_mysql_query($requete, $dbh);
		
		if($value['code'])
		foreach($value['code'] as $id_location =>$tab_typenotice) {				
			foreach($tab_typenotice as $typenotice =>$tab_typedoc) {					
				foreach($tab_typedoc as  $typedoc=>$code) {	
					$requete = "INSERT INTO notice_tplcode SET 
						num_notpl='".$this->id."',
						notplcode_localisation='$id_location', 
						notplcode_typdoc='$typedoc',
						notplcode_niveau_biblio='$typenotice', 
						nottplcode_code='". addslashes($code)."' ";	
					if(!pmb_mysql_query($requete, $dbh)) {
						require_once("$include_path/user_error.inc.php"); 
						warning($msg["notice_tpl_ajouter"], $msg["notice_tpl_ajouter_erreur"]);
						return false;
					}						
				}	
							
			}			
		}	
		return true;
	}
		
	public function update_from_form() {
		global $name, $code_list, $comment,$id_test,$show_opac;
		
		$value['name']=stripslashes($name);
		$value['comment']=stripslashes($comment);
		$value['id_test']=stripslashes($id_test);
		$value['show_opac']=stripslashes($show_opac);

		foreach($code_list as $input_code)	{
			$code="";
			eval("global \$".$input_code.";\$code= $".$input_code.";");
			if($code) {
				list($label,$location,$type_notice,$type_doc)=explode("_",$input_code);				
				$value["code"]["$location"]["$type_notice"]["$type_doc"]=stripslashes($code);
			}
		}
		$this->update($value); 		
	}
	
	static public function gen_tpl_select($select_name="notice_tpl", $selected_id=0) {		
		global $msg;
		
		$requete = "SELECT notpl_id, concat(notpl_name,'. ',notpl_comment) as nom  FROM notice_tpl ORDER BY notpl_name ";
		$onchange="";
		return gen_liste ($requete, "notpl_id", "nom", $select_name, $onchange, $selected_id, 0, $msg["notice_tpl_list_default"], 0,$msg["notice_tpl_list_default"], 0) ;
	}
		
	public function show_eval($notice_id=0) {
		global $notice_tpl_eval;
		global $deflt2docs_location;
		
		if(!$notice_id)$notice_id=$this->id_test;
		$notice_tpl_gen = notice_tpl_gen::get_instance($this->id); 
		$tpl= $notice_tpl_gen->build_notice($notice_id,$deflt2docs_location); 
		$form = str_replace("!!tpl!!",	$tpl, $notice_tpl_eval);		
		
		return $form;
	}	
	
	public function show_import_form($link="./edit.php") {
		global $msg;
		global $notice_tpl_form_import;
		global $charset;

		$form=$notice_tpl_form_import;		
		$action = $link."?categ=tpl&sub=notice&action=import_suite";
		
		$form = str_replace("!!action!!",	$action, $form);
					
		return $form;
	}
	
	public function do_import(){
		global $dbh, $msg, $charset;

		$erreur=0;
		$userfile_name = $_FILES['f_fichier']['name'];
		$userfile_temp = $_FILES['f_fichier']['tmp_name'];
		$userfile_moved = basename($userfile_temp);
				
		$userfile_name = preg_replace("/ |'|\\|\"|\//m", "_", $userfile_name);
				
		// création
		if (move_uploaded_file($userfile_temp,'./temp/'.$userfile_moved)) {
			$fic=1;
		}				
		if (!$fic) {
			$erreur=$erreur+10;
		}else{
			$fp = fopen('./temp/'.$userfile_moved , "r" );
			$contenu = fread ($fp, filesize('./temp/'.$userfile_moved));
			if (!$fp || $contenu=="") $erreur=$erreur+100; ;
			fclose ($fp) ;
		}
				
		//récupération et affectation des lignes
		$input_main_tmp='';
		$input_sub_tmp='';
		$input_locations_tmp='';
		$input_charset_tmp='';
		$input_cours='';
		$tmpLignes=explode("\n",$contenu);		
		foreach ($tmpLignes as $ligne){
			if (preg_match('`^\#main\#=(.+)`',$ligne,$out)) {
				$cours='input_main';
				$input_main_tmp=$out[1];
			} elseif (preg_match('`^\#sub\#=(.+)`',$ligne,$out)) {
				$cours='input_sub';
				$input_sub_tmp=$out[1];
			} elseif (preg_match('`^\#locations\#=(.+)`',$ligne,$out)) {
				$cours='input_locations';
				$input_locations_tmp=$out[1];
			} elseif (preg_match('`^\#charset\#=(.+)`',$ligne,$out)) {
				$cours='input_charset';
				$input_charset_tmp=$out[1];
			} else {
				switch ($cours) {
					case 'input_main' :
						$input_main_tmp.="\n".$ligne;
						break;
					case 'input_sub' :
						$input_sub_tmp.="\n".$ligne;
						break;
					case 'input_locations' :
						$input_locations_tmp.="\n".$ligne;
						break;
					case 'input_charset' :
						$input_charset_tmp.="\n".$ligne;
						break;
					default :
						$erreur=5;
						break;
				}
			}
		}
		
		//on recrée les données
		$input_main=unserialize($input_main_tmp);
		$input_sub=unserialize($input_sub_tmp);
		$input_locations_tmp=unserialize($input_locations_tmp);
		$input_locations=array();
		if(is_array($input_locations_tmp) && count($input_locations_tmp)){
			foreach($input_locations_tmp as $value){
				$input_locations[$value["id_location"]]=$value["lib_location"];
			}
		}
		$input_charset=$input_charset_tmp;
		
		//on vérifie
		if(!count($input_main)||!count($input_sub)||!trim($input_charset)){
			$erreur=5;
		}
				
		if(!$erreur){
			
			//valeurs à connaitre
			$doctype = marc_list_collection::get_instance('doctype');
			$locations = array();
			$res=pmb_mysql_query("SELECT idlocation, location_libelle FROM docs_location ORDER BY 1");
			while($row=pmb_mysql_fetch_object($res)){
				$locations[$row->idlocation]=$row->location_libelle;
			}
			
			//gestion de l'encodage fichier/PMB
			$fonction_convert="";
			if($input_charset=='iso-8859-1' && $charset=='utf-8'){
				$input_main=pmb_utf8_encode($input_main);
				$input_sub=pmb_utf8_encode($input_sub);
				$input_locations=pmb_utf8_encode($input_locations);
			}elseif($input_charset=='utf-8' && $charset=='iso-8859-1'){
				$input_main=pmb_utf8_decode($input_main);
				$input_sub=pmb_utf8_decode($input_sub);
				$input_locations=pmb_utf8_decode($input_locations);
			}
			
			//Ajout dans notice_tpl
			$requete="INSERT INTO notice_tpl SET ";
			foreach($input_main as $key=>$value){
				if($key){
					$requete.=", ";
				}
				$requete.=$value["field"]."='".addslashes($value["value"])."'";
			}
			pmb_mysql_query($requete);
			$id_tpl = pmb_mysql_insert_id();
			if ($id_tpl) {
				
				//Ajouts dans notice_tpl_code
				$array_errors=array();
				foreach($input_sub as $sub){
					$ok_import=true;
					$array_error_row=array();
					if(isset($id_loc_cours)){
						unset($id_loc_cours);
					}
					if(isset($typdoc_cours)){
						unset($typdoc_cours);
					}
					
					//création requête
					$requete="INSERT INTO notice_tplcode SET num_notpl=".$id_tpl;
					foreach($sub as $value){
						$requete.=", ".$value["field"]."='".addslashes($value["value"])."'";
						if($value["field"]=="notplcode_localisation"){
							$id_loc_cours=$value["value"];
						}
						if($value["field"]=="notplcode_typdoc"){
							$typdoc_cours=$value["value"];
						}
						if($value["field"]=="nottplcode_code"){
							$array_error_row["tpl_code"]=$value["value"];
						}
					}
					$array_error_row["typdoc"]=$typdoc_cours;
					$array_error_row["location"]=$input_locations[$id_loc_cours]." (".$id_loc_cours.")";
					
					//vérification localisation et type de document
					if(!isset($id_loc_cours)||!isset($typdoc_cours)){
						$ok_import=false;
						$array_error_row["error"]=$msg["notice_tpl_import_error_missing_info"];
					}elseif(($id_loc_cours) || ($typdoc_cours)){
						if($id_loc_cours){
							if(!isset($locations[$id_loc_cours])||($locations[$id_loc_cours]!=$input_locations[$id_loc_cours])){
								$array_error_row["error"]=$msg["notice_tpl_import_error_missing_location"];
								$ok_import=false;
							}
						}
						if($typdoc_cours){
							if(!isset($doctype->table[$typdoc_cours])){
								$array_error_row["error"]=$msg["notice_tpl_import_error_missing_typdoc"];
								$ok_import=false;
							}
						}
					}
					
					//ajout requête
					if($ok_import){
						pmb_mysql_query($requete);
					}else{
						$array_errors[]=$array_error_row;
					}
				}
				
				//Affichage des templates en erreur
				if(count($array_errors)){
					print "<h1>".$msg['notice_tpl_import_invalide']."</h1>";
					foreach($array_errors as $error){
						echo "<b>".$msg["notice_tpl_import_error_error"]."</b> : ".$error["error"]."<br>";
						echo "<b>".$msg["notice_tpl_import_error_typdoc"]."</b> : ".$error["typdoc"]."<br>";
						echo "<b>".$msg["notice_tpl_import_error_location"]."</b> : ".$error["location"]."<br>";
						echo "<b>".$msg["notice_tpl_import_error_tplcode"]."</b> : <br>".nl2br(htmlentities($error["tpl_code"]))."<br>";
						echo "<hr><br>";
					}
				}
				print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"./edit.php?categ=tpl&sub=notice\" >
				<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\" />
				</form>";
				if(!count($array_errors)){
					print "<script type=\"text/javascript\">document.dummy.submit();</script>";
				}
			} else {
				print "<h1>".$msg['notice_tpl_import_invalide']."</h1>
				Error code = 7";
				print $this->show_import_form();
			}
		} else {
			print "<h1>".$msg['notice_tpl_import_invalide']."</h1>
			Error code = $erreur";
			print $this->show_import_form();
		}
		print "</div>";
				
		//On efface le fichier temporaire
		if ($userfile_name) {
			unlink('./temp/'.$userfile_moved);
		}	
	}
	
	/**
	 * Retourne tous les répertoires de templates de notices
	 * @param string $selected
	 * @return string
	 */
	public static function get_directories_options($selected = '') {
		global $msg,$opac_notices_format_django_directory;
	
		if (!$selected) {
			$selected = $opac_notices_format_django_directory;
		}
		if (!$selected) {
			$selected = 'common';
		}
		$dirs = array_filter(glob('./opac_css/includes/templates/record/*'), 'is_dir');
		$tpl = "";
		foreach($dirs as $dir){
			if(basename($dir) != "CVS"){
				$tpl.= "<option ".(basename($dir) == basename($selected) ? "selected='selected'" : "")." value='".basename($dir)."'>
				".(basename($dir) == "common" ? basename($dir)." (".$msg['proc_options_default_value'].")" : basename($dir))."</option>";
			}
		}
		return $tpl;
	}

} // fin class 
