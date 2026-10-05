<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_view_group.class.php,v 1.7 2023/05/04 14:15:47 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/editions_state_view.class.php");

class editions_state_view_group extends editions_state_view {
	private $sqlite_resource=null;
	private $sqlite_error="";
	private $my_function=array("sum","min","max","avg","count","group_concat","val");
	
	public function __construct($datas,$id,$param=array()){
		//on gère les propriétés communes dans la classe parente
		parent::__construct($datas,$id,$param);
		$this->sqlite_db_open();
		$this->set_param_group($param["group"] ?? array());
	}
	
	private function sqlite_db_open(){
		if(!class_exists('SQLite3')){
			$this->sqlite_error="SQLite 3 NOT supported";
			return;
		}
		try{
			$this->sqlite_resource=new SQLite3(":memory:");
			//Création de la table
			$list_champ=array();
			$query="CREATE TABLE datas(";
			foreach ( $this->datas[0] as $key => $value ) {
				$list_champ[]="champ_".$key;
				$query.="champ_".$key." text,";
			}
			$query=substr($query, 0, -1);//On enlève la dernière virgule
			$query.=");";
			$res=$this->sqlite_resource->exec($query);
			if(!$res){
				$this->sqlite_error="Unable to create table datas : ".$query;
				return;				
			}
			
			//Insertion des données
			for ($i = 1; $i < count($this->datas); $i++) {
				$query="INSERT INTO datas(".implode(", ",$list_champ).") VALUES(";
				foreach ( $this->datas[$i] as $value ) {
					$query.="'".$this->sqlite_resource->escapeString($value ?? "")."',";
				}
				$query=substr($query, 0, -1);//On enlève la dernière virgule
				$query.=");";
				$res=$this->sqlite_resource->query($query);
				if(!$res){
					$this->sqlite_error="Unable to insert in table datas : ".$query;
					return;				
				}
			}
			
		}catch(Exception $e){
			$this->sqlite_error=$e->getMessage();
		}
	}
	
	private function sqlite_calc_group(){
		
		$show_fields=$this->my_param["group"]["show_fields"];
		$group_fields=$this->my_param["group"]["group_fields"];
		
		$result=array();
		$query="SELECT ";
		
		if(count($show_fields) && $this->sqlite_resource){
			if(count($group_fields)){
				foreach ( $group_fields as $value ) {
					$query.="champ_".$value.",";
				}
			}
			
			foreach ( $show_fields as $value ) {
				$function_field = "function_field_".$value;
				$option=$this->my_param["group"]["function_fields"][$function_field];
				if($option && $option != "val"){
					if($option == "group_concat"){
						//$query.=$option."( DISTINCT champ_".$value.") AS alias_".$value.",";
						$query.=" (REPLACE(REPLACE(GROUP_CONCAT(DISTINCT REPLACE(champ_".$value.",',','{virgul}')),',',' {sep_val} '),'{virgul}',',')) AS alias_".$value.",";
					}else{
						$query.=$option."(champ_".$value.") AS alias_".$value.",";
					}
				}else{
					$query.="champ_".$value." AS alias_".$value.",";
				}
			}
			$query=substr($query, 0, -1);//On enlève la dernière virgule
			$query.=" FROM datas ";
			if(count($group_fields)){
				$query.=" GROUP BY ";
				foreach ( $group_fields as $value ) {
					$query.="champ_".$value.",";
				}
				$query=substr($query, 0, -1);//On enlève la dernière virgule
			}
			$res=$this->sqlite_resource->query($query);
			if($res){
				while ($ligne=$res->fetchArray(SQLITE3_ASSOC)) {
					$result[]=$ligne;
				}
				$res->finalize();
			}
		}
		return $result;
	}
	
	private function sqlite_db_close(){
		if($this->sqlite_resource){
			$this->sqlite_resource->close();
		}
	}
	
	public function set_param_group($param=array()){
		global $save_param;
		
		if($save_param == "group"){
			global $show_fields_tabl;
			global $group_fields_tabl;
			$function_fields_tabl=array();
			if(is_array($show_fields_tabl) && count($show_fields_tabl)){
				foreach ( $show_fields_tabl as $champ ) {
       				$function_field = "function_field_".$champ;
					global ${$function_field};
					$option=${$function_field};
					if(!$option){
						$option="val";
					}
					$function_fields_tabl[$function_field]=$option;
				}
			}
		}else{
			$show_fields_tabl=$param["show_fields"];
			$group_fields_tabl=$param["group_fields"];
			$function_fields_tabl=$param["function_fields"];
		}
		
		if(!is_array($show_fields_tabl)){
			$show_fields_tabl=array();
		}
		
		if(!is_array($group_fields_tabl)){
			$group_fields_tabl=array();
		}
		if(!is_array($function_fields_tabl)){
			$function_fields_tabl=array();
		}
		
		$this->my_param["group"]=array("show_fields"=>$show_fields_tabl,"group_fields"=>$group_fields_tabl,"function_fields"=>$function_fields_tabl);
		return;
	}
	
	private function form_select_filter($champ){
		global $msg,$charset;
		$function_field = "function_field_".$champ;
		
		$html="<select name='".$function_field."' id='".$function_field."'>";
		foreach ( $this->my_function as $value ) {
       		$html.="<option value='".$value."' ".($this->my_param["group"]["function_fields"][$function_field] == $value ? " selected='selected' " : "").">".htmlentities($msg["editions_state_view_group_filter_".$value],ENT_QUOTES,$charset)."</option>";
		}
		$html.="</select>";
		return $html;
	}
	
	//
	public function show(){
		global $charset,$msg;
		global $javascript_path;
		global $show_all;
		
		$show_fields_tabl=$this->my_param["group"]["show_fields"];
		$group_fields_tabl=$this->my_param["group"]["group_fields"];
		
		$new_data=$this->sqlite_calc_group();
		$nb_lignes=count($new_data);
		$nb_colonne=count($show_fields_tabl)+count($group_fields_tabl);
		
		if($this->sqlite_error){
			$html="<div class='erreur'>".$msg["editions_state_view_group_sqlite_error"].": (SQLite 3 error: ".$this->sqlite_error.")</div>";
			return $html;
		}
		
		$html ="<script type='text/javascript' src='".$javascript_path."/sorttable.js'></script>
		<div class='row'>
			<div class='colonne4'>&nbsp;<input type='hidden' name='save_param'  id='save_param' value='group'/></div>
			<div class='colonne_scroll' style='border:0px;overflow-x:hidden;'>
					<label>".$msg['editions_state_nb_rows']."</label>
					<span>".$nb_lignes."</span>	
					<input type='button' class='bouton' value='".htmlentities($msg["actualiser"],ENT_QUOTES,$charset)."' onclick=\"test_form('group');\" />
					<input type='button' class='bouton' value='".htmlentities($msg["editions_state_view_export_excel"],ENT_QUOTES,$charset)."' onclick=\"test_form('group','edit');\" />
			</div>
		</div>
		
		<div class='row'>
		<div class='colonne4'>
			<table class='sortable'>
				<tr>
					<th>".htmlentities($msg["editions_state_view_group_par"],ENT_QUOTES,$charset)."</th>
					<th>".htmlentities($msg["editions_state_view_group_afficher"],ENT_QUOTES,$charset)."</th>
					<th>".htmlentities($msg["editions_state_view_group_champs"],ENT_QUOTES,$charset)."</th>
				</tr>";
		foreach ( $this->datas[0] as $key => $value ) {
       		$html.="
				<tr>
					<td><input type=\"checkbox\" name=\"group_fields_tabl[]\" value='".$key."' ".(in_array($key,$group_fields_tabl) ? "checked" : "")." /></td>
					<td><input type=\"checkbox\" name=\"show_fields_tabl[]\" value='".$key."' ".(in_array($key,$show_fields_tabl) ? "checked" : "")." /></td>
					<td>".htmlentities($value,ENT_QUOTES,$charset)."</td>
				</tr>";
		}

		$html.="
			</table>
		</div>
		<div class='colonne_scroll' >";
		if(count($show_fields_tabl)){
			$html.="
				<table class='sortable'>";
			//1ère ligne
			$html.="<thead>";
			$html.="<tr class='sorttop'>";
			foreach ( $group_fields_tabl as $value ) {
       			$html.="<th>".htmlentities($this->datas[0][$value],ENT_QUOTES,$charset)."</th>";
			}
			foreach ( $show_fields_tabl as $value ) {
       			$html.="<th>".htmlentities($this->datas[0][$value],ENT_QUOTES,$charset)."</th>";
			}
			$html.="</tr>";
			//2ème ligne
			$html.="<tr>";
			foreach ( $group_fields_tabl as $value ) {
       			$html.="<td>".htmlentities($msg["editions_state_view_group_distinct"],ENT_QUOTES,$charset)."</td>";
			}
			foreach ( $show_fields_tabl as $value ) {
       			$html.="<td>".$this->form_select_filter($value)."</td>";
			}
			$html.="</tr>";
			$html.="</thead>";
			//Résultat
			if(count($new_data)){
				foreach ( $new_data as $key => $ligne_result ) {
      				 $html.="<tr>";
					foreach ( $group_fields_tabl as $value ) {
		       			$html.="<td>".htmlentities($ligne_result["champ_".$value],ENT_QUOTES,$charset)."</td>";
					}
					foreach ( $show_fields_tabl as $value ) {
		       			$html.="<td>".htmlentities($ligne_result["alias_".$value],ENT_QUOTES,$charset)."</td>";
					}
					$html.="</tr>";
					if(!$show_all && ($key == 49)){
						$html.="<tr class='sortbottom' ><td colspan=\"".$nb_colonne."\"><a onclick='test_form(\"group\",\"show_all\");'><b>".$msg["editions_state_view_tab_all"]."</b></a></td></tr>";
						break;
					}
				}
			}
			
			$html.="</table>";
		}
		$html.="
		</div>
	</div>
	<div class='row'>&nbsp;</div>";
		$this->sqlite_db_close();
		return $html;
	}
	
	public function render_xls_file($name="state"){
		global $msg;
		
		$worksheet = new spreadsheetPMB();
		
		$show_fields_tabl=$this->my_param["group"]["show_fields"];
		$group_fields_tabl=$this->my_param["group"]["group_fields"];
	
		
		if(count($show_fields_tabl)){
			//1ère ligne
			$nb_ligne=0;
			$nb_colonne=0;
			foreach ( $group_fields_tabl as $value ) {
				$worksheet->write($nb_ligne,$nb_colonne,$this->datas[0][$value]);
				$nb_colonne++;
			}
			foreach ( $show_fields_tabl as $value ) {
       			$worksheet->write($nb_ligne,$nb_colonne,$this->datas[0][$value]);
				$nb_colonne++;
			}
			//2ème ligne
			$nb_ligne++;
			$nb_colonne=0;
			foreach ( $group_fields_tabl as $value ) {
				$worksheet->write($nb_ligne,$nb_colonne,$msg["editions_state_view_group_distinct"]);
				$nb_colonne++;
			}
			foreach ( $show_fields_tabl as $value ) {
				$worksheet->write($nb_ligne,$nb_colonne,$msg["editions_state_view_group_filter_".$this->my_param["group"]["function_fields"]["function_field_".$value]]);
				$nb_colonne++;
			}
			//Résultat
			$new_data=$this->sqlite_calc_group();
			if(count($new_data)){
				foreach ( $new_data as $ligne_result ) {
      				$nb_ligne++;
					$nb_colonne=0;
					foreach ( $group_fields_tabl as $value ) {
						$worksheet->write($nb_ligne,$nb_colonne,$ligne_result["champ_".$value]);
						$nb_colonne++;
					}
					foreach ( $show_fields_tabl as $value ) {
						$worksheet->write($nb_ligne,$nb_colonne,$ligne_result["alias_".$value]);
						$nb_colonne++;
					}
				}
			}
		}
		$worksheet->download($name.'.xls');
	}
}