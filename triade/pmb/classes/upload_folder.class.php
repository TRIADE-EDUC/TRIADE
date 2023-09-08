<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: upload_folder.class.php,v 1.14 2019-06-07 13:21:30 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/upload_folder.tpl.php");

class upload_folder {
	
	public $repertoire_id=0;
	public $action='';
	public $nb_enregistrement=0;
	public $repertoire_nom='';
	public $repertoire_url='';
	public $repertoire_path='';
	public $repertoire_navigation=0;
	public $repertoire_hachage=0;
	public $repertoire_subfolder=0;
	public $repertoire_utf8=0;
	
	public function __construct($id=0, $action=''){
		global $dbh;
		
		$this->repertoire_id = intval($id);
		$this->action = $action;	
		
		if($this->repertoire_id){
			//Modification
			$req="select repertoire_nom, repertoire_url, repertoire_path, repertoire_navigation, repertoire_hachage, repertoire_subfolder, repertoire_utf8 from upload_repertoire where repertoire_id='".$this->repertoire_id."'";
			$res=pmb_mysql_query($req,$dbh);
			if(pmb_mysql_num_rows($res)){
				$item = pmb_mysql_fetch_object($res);
				$this->repertoire_nom=$item->repertoire_nom;
				$this->repertoire_url=$item->repertoire_url;
				$this->repertoire_path=$item->repertoire_path;
				$this->repertoire_navigation=$item->repertoire_navigation;
				$this->repertoire_hachage=$item->repertoire_hachage;
				$this->repertoire_subfolder=$item->repertoire_subfolder;
				$this->repertoire_utf8=$item->repertoire_utf8;
			} else {
				$this->repertoire_nom='';
				$this->repertoire_url='';
				$this->repertoire_path='';
				$this->repertoire_navigation=0;
				$this->repertoire_hachage=0;
				$this->repertoire_subfolder=0;
				$this->repertoire_utf8=0;
			}
		} else {
			//Création
			$this->repertoire_nom='';
			$this->repertoire_url='';
			$this->repertoire_path='';
			$this->repertoire_navigation=0;
			$this->repertoire_hachage=0;
			$this->repertoire_subfolder=20;
			$this->repertoire_utf8=0;
		}
	}
	
	/**
	 * Gestion des actions
	 */
	public function proceed(){
			
		switch($this->action){
			
			case 'add':
				$this->show_edit_form();
				break;
			case 'suppr_rep';
				$this->delete($this->repertoire_id);
				$this->show_form();
				break;
			case 'modif';
				$this->show_edit_form();
				break;
			case 'save_rep':
				$this->enregistrer($this->repertoire_id);
				$this->show_form();
				break;	
			default:
				$this->show_form();
				break;
		}
		
	}
	
	/**
	 * Formulaire qui liste les répertoires
	 */
	public function show_form(){
		global $liste_rep_form, $dbh, $charset, $msg;
		
		$rep_line = '';
		$req="select repertoire_id, repertoire_nom, repertoire_url, repertoire_path, repertoire_navigation, repertoire_hachage, repertoire_subfolder, repertoire_utf8 from upload_repertoire order by repertoire_nom";
		$res=pmb_mysql_query($req,$dbh);
		$nbr = pmb_mysql_num_rows($res);

		$parity=1;
		for($i=0;$i<$nbr;$i++) {
			$rep=pmb_mysql_fetch_object($res);
			if ($parity % 2)
				$pair_impair = "even";
			 else 
				$pair_impair = "odd";
			$parity += 1;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./admin.php?categ=docnum&sub=rep&action=modif&id=$rep->repertoire_id';\" ";
			
			$rep_line .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
						<td ><strong>".htmlentities($rep->repertoire_nom,ENT_QUOTES,$charset)."</strong></td>";
				// "<td >".htmlentities($rep->repertoire_url,ENT_QUOTES,$charset)."</td>
			$rep_line .= "<td >".htmlentities($rep->repertoire_path,ENT_QUOTES,$charset)."</td>
							<td >".htmlentities(($rep->repertoire_navigation ? $msg['upload_repertoire_yes']: $msg['upload_repertoire_no']),ENT_QUOTES,$charset)."</td>
							<td >".htmlentities(($rep->repertoire_hachage ? $msg['upload_repertoire_yes']:$msg['upload_repertoire_no'] ),ENT_QUOTES,$charset)."</td>
							<td >".htmlentities(($rep->repertoire_utf8 ? $msg['upload_repertoire_yes']:$msg['upload_repertoire_no'] ),ENT_QUOTES,$charset)."</td>
							<td >".htmlentities(($rep->repertoire_hachage ? $rep->repertoire_subfolder : ''),ENT_QUOTES,$charset)."</td>						
					</tr>";
		}
		$liste_rep_form = str_replace("!!liste_rep!!",$rep_line,$liste_rep_form);
		print $liste_rep_form;
	}
	
	/**
	 * Formulaire de création/édition d'un répertoire
	 */
	public function show_edit_form(){
		
		global $rep_edit_form, $msg, $charset;
		
		if(!$this->repertoire_id)
			$champ_sub = "<input type='text' class='saisie-5em' name='rep_sub' id='rep_sub' value='!!rep_sub!!'/>";
		else $champ_sub = "<label id='rep_sub'>!!rep_sub!!</label>";
		$rep_edit_form = str_replace("!!rep_nom!!",htmlentities($this->repertoire_nom, ENT_QUOTES,$charset),$rep_edit_form);
		$rep_edit_form = str_replace("!!rep_url!!",htmlentities($this->repertoire_url, ENT_QUOTES,$charset),$rep_edit_form);
		$rep_edit_form = str_replace("!!rep_path!!",htmlentities($this->repertoire_path, ENT_QUOTES,$charset),$rep_edit_form);
		if($this->repertoire_navigation){
			$rep_edit_form = str_replace("!!select_nav_yes!!",'selected',$rep_edit_form);
			$rep_edit_form = str_replace("!!select_nav_no!!",'',$rep_edit_form);
		} else {
			$rep_edit_form = str_replace("!!select_nav_yes!!",'',$rep_edit_form);
			$rep_edit_form = str_replace("!!select_nav_no!!",'selected',$rep_edit_form);
		}
		if($this->repertoire_hachage){
			$rep_edit_form = str_replace("!!select_hash_yes!!",'selected',$rep_edit_form);
			$rep_edit_form = str_replace("!!select_hash_no!!",'',$rep_edit_form);
		} else {
			$rep_edit_form = str_replace("!!select_hash_yes!!",'',$rep_edit_form);
			$rep_edit_form = str_replace("!!select_hash_no!!",'selected',$rep_edit_form);
		}
		if($this->repertoire_utf8){
			$rep_edit_form = str_replace("!!select_utf8_yes!!",'selected',$rep_edit_form);
			$rep_edit_form = str_replace("!!select_utf8_no!!",'',$rep_edit_form);
		} else {
			$rep_edit_form = str_replace("!!select_utf8_yes!!",'',$rep_edit_form);
			$rep_edit_form = str_replace("!!select_utf8_no!!",'selected',$rep_edit_form);
		}
		$rep_edit_form = str_replace("!!champ_sub!!",$champ_sub,$rep_edit_form);
		$rep_edit_form = str_replace("!!id!!",htmlentities($this->repertoire_id, ENT_QUOTES,$charset),$rep_edit_form);
		$rep_edit_form = str_replace("!!rep_sub!!",htmlentities($this->repertoire_subfolder, ENT_QUOTES,$charset),$rep_edit_form);
	
		$btn_suppr = "<input type='submit' class='bouton' value='$msg[63]' onclick='this.form.action.value=\"suppr_rep\"'/>";
		$rep_edit_form = str_replace("!!btn_suppr!!",$btn_suppr,$rep_edit_form);
			
		print $rep_edit_form;
		
	}
	
	/**
	 * Suppression d'un répertoire
	 */
	public function delete($id){
		global $msg,$dbh;
		
		$req="select explnum_id from explnum where explnum_repertoire='".$id."'";
		$res = pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($res)){
			error_form_message($msg["upload_repertoire_no_del"]);
		} else{		
			$req = "delete from upload_repertoire where repertoire_id='".$id."'";
			pmb_mysql_query($req,$dbh);
		}
	}
	
	/**
	 * Enregistrement d'un répertoire
	 */
	public function enregistrer($id=0){
		
		global $rep_nom, $rep_url, $rep_path, $rep_hash, $rep_navig, $rep_sub, $dbh, $rep_utf8, $msg; 
		
		if(substr($rep_path,strlen($rep_path)-1) !== '/') $rep_path=$rep_path."/";
				 
		if($id) {
			$req = "update upload_repertoire set repertoire_nom='".$rep_nom."', repertoire_url='".$rep_url."', repertoire_path='".$rep_path."', repertoire_navigation='".$rep_navig."', repertoire_hachage='".$rep_hash."', repertoire_utf8='".$rep_utf8."' where repertoire_id='".$id."'";
			pmb_mysql_query($req,$dbh);
		} else{			
			$req = "select repertoire_id from upload_repertoire where repertoire_nom='".$rep_nom."'";
			$res = pmb_mysql_query($req,$dbh);
			if(pmb_mysql_num_rows($res)){
				error_form_message($msg["upload_repertoire_name_exists"]);
			} else {		
				$req="insert into upload_repertoire (repertoire_nom, repertoire_url, repertoire_path, repertoire_navigation, repertoire_hachage, repertoire_subfolder,repertoire_utf8) values ('".$rep_nom."', '".$rep_url."', '".$rep_path."', '".$rep_navig."', '".$rep_hash."', '".$rep_sub."', '".$rep_utf8."')";
				pmb_mysql_query($req,$dbh);
			}
		}
		
	}
	
	/**
	 * Compte le nombre d'enregistrement
	 */	
	public function compte_repertoire(){
		global $dbh;
		
		$req = "select count(repertoire_id) from upload_repertoire";
		$res = pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($res)){
			$this->nb_enregistrement =  pmb_mysql_result($res,0,0);
		} else 	$this->nb_enregistrement = 0;
		
	}
	
	/**
	 * Construit l'arbre des répertoires
	 */
	public function make_tree(){
		global $msg, $charset, $dbh;
		
		print "<script type='text/javascript' src='../../javascript/dtree.js'></script>";
		print "<script type='text/javascript' src='../../javascript/upload.js'></script>";
		
		$dtree = "<script type='text/javascript'>\n";
		$dtree .= "var tab_libelle = new Array();";
		
		$dtree.= "_dt_fiel_ = new dTree('_dt_fiel_');\n";
				
		//Creation racine liens depuis les champs de la table de reference
		$dtree.="_dt_fiel_.add('Rep_0',-1,'&nbsp;&nbsp;".addslashes($msg["upload_repertoire_my_folder"])."');\n";
			
		$req = "select * from upload_repertoire order by repertoire_nom";
		$res=pmb_mysql_query($req,$dbh);
		while(($rep=pmb_mysql_fetch_object($res))){	
			$up = new upload_folder($rep->repertoire_id);
			$dtree .= "tab_libelle[\"Rep_".$rep->repertoire_id."\"] = \"".addslashes($up->formate_path_to_nom($rep->repertoire_path)). "\";";  		
			$dtree.="_dt_fiel_.add('Rep_".$rep->repertoire_id."','Rep_0','".addslashes($rep->repertoire_nom)."','','javascript:copy_to_div(\'Rep_".$rep->repertoire_id."\', \'".$rep->repertoire_id."\');');\n";
			if($rep->repertoire_navigation && !$rep->repertoire_hachage){
				$this->getNodes($rep->repertoire_path, "Rep_".$rep->repertoire_id, $dtree);
			}			
		}
		
		$dtree.= "_dt_fiel_.icon.root='../../images/req_fiel.gif';";	
		$dtree.= "_dt_fiel_.icon.node='../../images/dtree/folder.gif';";				
		$dtree.= "document.getElementById('up_fiel_tree').innerHTML = _dt_fiel_;\n";
		$dtree.= "</script>\n";
		return $dtree;
	}
	
	/**
	 * Construit les noeuds de l'arborescence
	 */
	public function getNodes($chemin='', $id, &$tree){		

		if($chemin && is_dir($chemin)){			
			if(($files = @scandir($chemin)) !== false){
				for($i=0;$i<sizeof($files);$i++){
					if($files[$i] != '.' && $files[$i] != '..'){
						$id_noeud = $id."_".$i;
						$id_parent = $id;
						$dir_name = $files[$i];
						$path = $chemin.$dir_name."/"; 
						if(is_dir($path)){
							$id_copy = explode("_",$id_parent);
							$up = new upload_folder($id_copy[1]);
							//$tree .= "tab_libelle[\"$id_noeud\"] = \"".$up->decoder_chaine(addslashes($up->formate_path_to_nom($path))). "\";";
							$tree .= "tab_libelle[\"$id_noeud\"] = \"".addslashes($up->formate_path_to_nom($chemin).$up->decoder_chaine($dir_name)."/"). "\";";   
							$tree .="_dt_fiel_.add('$id_noeud','$id_parent','".addslashes($up->decoder_chaine($dir_name))."','','javascript:copy_to_div(\'".$id_noeud."\',\'".$up->repertoire_id."\');');\n";	
							$this->getNodes($path,$id_noeud, $tree);
						}
					}
				}
			}
		}
		return $tree;
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
	
	/**
	 * Formate le nom du chemin en utilisant le nom de rep
	 */
	public function formate_nom_to_path($chemin){	
		$chemin = str_replace($this->repertoire_nom,$this->repertoire_path,$chemin);
		$chemin = str_replace('//','/',$chemin);
		
		return $chemin;
	}
	
	/**
	 * Formate le chemin pour la sauvegarde dans les exemplaires numériques
	 */
	public function formate_path_to_save($chemin){
		$chemin = str_replace($this->repertoire_nom,'/',$chemin);
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
			return utf8_decode($chaine);
		} else if($charset == 'utf-8' && !$this->isUtf8()) {
			return utf8_encode($chaine);
		}
		return $chaine;
	}
	
	/*
 	 * encode la chaine dans le bon charset
	 */
	public function encoder_chaine($chaine){
		global $charset;
		
		if($charset != 'utf-8' && $this->isUtf8()) {
			return utf8_encode($chaine);
		} else if($charset == 'utf-8' && !$this->isUtf8()) {
			return utf8_decode($chaine);
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
?>