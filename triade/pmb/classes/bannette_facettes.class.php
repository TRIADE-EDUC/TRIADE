<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_facettes.class.php,v 1.40 2019-05-22 14:24:40 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($include_path."/templates/bannette_facettes.tpl.php");
require_once($class_path."/notice_tpl_gen.class.php");
require_once ("$class_path/mono_display.class.php") ; 
require_once ("$class_path/serial_display.class.php") ;
require_once($class_path."/notice_tpl_gen.class.php");

class bannette_facettes{
	public $id=0;// $id bannette
	public $facettes=array(); // facettes associées à la bannette
	public $environement=array(); // affichage des notices
	public $noti_tpl_document=0; // template de notice
	public $bannette_display_notice_in_every_group=0;
	public $bannette_document_group=0;
	public $sommaires=array(); // donnée du document à générer par un templatze
	
	public function __construct($id) {  // $id bannette
		$this->id=$id+0;		
		$this->fields_array = $this->fields_array();
		$this->fetch_data();
	}
	
	//recuperation de champs_base.xml
	public function fields_array(){
		global $include_path,$msg;
		global $dbh, $champ_base;
	
		if(empty($champ_base) || !is_array($champ_base)) {
			$file = $include_path."/indexation/notices/champs_base_subst.xml";
			if(!file_exists($file)){
				$file = $include_path."/indexation/notices/champs_base.xml";
			}
			$fp=fopen($file,"r");
			if ($fp) {
				$xml=fread($fp,filesize($file));
			}
			fclose($fp);
			$champ_base=_parser_text_no_function_($xml,"INDEXATION",$file);
		}
		return $champ_base;
	}
	
	public function fetch_data() {		
		global $msg,$dbh,$charset;
		$this->facettes=array();
		$req="select bannette_facettes.*,bannettes.display_notice_in_every_group,bannettes.document_group from bannette_facettes
		JOIN bannettes ON id_bannette=num_ban_facette
		where num_ban_facette=". $this->id." order by ban_facette_order";
		$res = pmb_mysql_query($req,$dbh);
		$i=0;
		if (pmb_mysql_num_rows($res)) {
			while($r=pmb_mysql_fetch_object($res)){
				$this->facettes[$i] = new stdClass();
				$this->facettes[$i]->critere=$r->ban_facette_critere;
				$this->facettes[$i]->ss_critere= $r->ban_facette_ss_critere;
				$this->facettes[$i]->order_sort= $r->ban_facette_order;
				
				if(!$this->bannette_display_notice_in_every_group){
					$this->bannette_display_notice_in_every_group=$r->display_notice_in_every_group;
				}
				
				if(!$this->bannette_document_group){
					$this->bannette_document_group=$r->document_group;
				}
				
				$i++;
			}
		}
	}	
	
	public function array_sort(){
		global $msg;
	
		$array_sort = array();
	
		$nb = count($this->fields_array['FIELD']);
		for($i=0;$i<$nb;$i++){
			if($tmp= $msg[$this->fields_array['FIELD'][$i]['NAME']]){
				$lib = $tmp;
			}else{
				$lib = $this->fields_array['FIELD'][$i]['NAME'];
			}
			$id2 = $this->fields_array['FIELD'][$i]['ID'] + 0;
			$array_sort[$id2] = $lib;
				
		}
		asort($array_sort);
		return $array_sort;
	
	}
	
	public function array_subfields($id){
		global $msg,$charset;
		
		$array = $this->fields_array;
		$array_subfields = array();
		$bool_search = 0;
		$i = 0;
	
		if($id!=100){
			$isbd = array();
			$callable = array();
			while($bool_search==0){
				if($array['FIELD'][$i]['ID']==$id){
					if(isset($array['FIELD'][$i]['ISBD'])) {
						$isbd=$array['FIELD'][$i]['ISBD'];
					}
					if(isset($array['FIELD'][$i]['CALLABLE'])) {
						$callable=$array['FIELD'][$i]['CALLABLE'];
					}
					$array = $array['FIELD'][$i]['TABLE'][0]['TABLEFIELD'];
					$bool_search = 1;
				}
				$i++;
			}
			$size = count($array);
			for($i=0;$i<$size;$i++){
				if (isset($array[$i]['NAME'])) $array_subfields[$array[$i]['ID']+0] = $msg[$array[$i]['NAME']];
			}
			if($isbd){
				$array_subfields[$isbd[0]['ID']+0]=$msg['facette_isbd'];
			}
			for($i=0;$i<count($callable);$i++){
				if (isset($callable[$i]['NAME'])) $array_subfields[$callable[$i]['ID']+0] = $msg[$callable[$i]['NAME']];
			}
		}else{
			$req= pmb_mysql_query("select idchamp,titre from notices_custom order by titre asc");
			$j=0;
			while($rslt=pmb_mysql_fetch_object($req)){
				$array_subfields[$rslt->idchamp+0] = $rslt->titre;
				$j++;
			}
		}
		return $array_subfields;
	}
	
	public function delete(){
		$del = "delete from bannette_facettes where num_ban_facette = '".$this->id."'";
		pmb_mysql_query($del);
	}
	
	public function save(){
		global $max_facette;
		
		$this->delete();
		
		$order=0;
		for($i=0;$i<$max_facette;$i++){
			$critere = 'list_crit_'.$i;
			global ${$critere};
			if(${$critere} > 0){
				$ss_critere = 'list_ss_champs_'.$i;
				global ${$ss_critere};
								
				$rqt = "insert into bannette_facettes set num_ban_facette = '".$this->id."', ban_facette_critere = '".${$critere}."', ban_facette_ss_critere='".${$ss_critere}."', ban_facette_order='".$order."' ";
				pmb_mysql_query($rqt);
				$order++;				
			}			
		}		
	}
	
	public function add_ss_crit($suffixe_id,$id,$id_ss_champs=0){
		
		global $msg,$charset;		
		
		$id=intval($id);
		$id_ss_champs=intval($id_ss_champs);		
		
		$array = $this->array_subfields($id);
		$tab_ss_champs = array();
		if(isset($suffixe_id)){
			$name_ss_champs="list_ss_champs_".$suffixe_id;
		}else{
			$name_ss_champs="list_ss_champs";
		}
		if((count($array)>1)){
			$select_ss_champs="<select id='$name_ss_champs' name='$name_ss_champs'>";
			foreach($array as $j=>$val2){
				if($id_ss_champs == $j) $select_ss_champs.="<option value=".$j." selected='selected'>".htmlentities($val2,ENT_QUOTES,$charset)."</option>";
				else $select_ss_champs.="<option value=".$j.">".htmlentities($val2,ENT_QUOTES,$charset)."</option>";
			}
		
			$select_ss_champs.="</select></br>";
		}elseif(count($array)==1){
			foreach($array as $j=>$val2){
				$select_ss_champs = "<input type='hidden' name='$name_ss_champs' value='1'/>";
			}
		} else {
			$select_ss_champs = "<input type='hidden' name='$name_ss_champs' value='0'/>";
		}
		return $select_ss_champs;
	}
	
	public function add_facette($i_field){
		global $tpl_facette_elt_ajax;
		
	
		$array = $this->array_sort();
		$tpl = $tpl_facette_elt_ajax;

		$i=0;
		foreach ($array as $id => $value) {
			if(!$i){
				$select.="<option value=".$id." selected='selected'>".$value."</option>";
			} else {
				$select.="<option value=".$id.">".$value."</option>";
			}
		}
		$tpl = str_replace('!!i_field!!', $i_field, $tpl);
		$tpl = str_replace("!!liste1!!",$select,$tpl);
		$tpl = str_replace("!!id_bannette!!",$this->id,$tpl);
		return $tpl;
	}	
	
	public function gen_facette_selection(){
		global $dsi_facette_tpl;
		global $tpl_facette_elt;
	
		$array = $this->array_sort();
				
		$tpls=$dsi_facette_tpl;
		$facettes_tpl = '';
		$nb=count($this->facettes);
		if(!$nb)$nb++;
		
		for ($i=0 ; $i<$nb; $i++){
			$tpl = $tpl_facette_elt;
			
			$tpl = str_replace('!!i_field!!', $i, $tpl);
			if(isset($this->facettes[$i]->ss_critere)) {
				$tpl = str_replace('!!ss_crit!!', $this->facettes[$i]->ss_critere, $tpl);
			} else {
				$tpl = str_replace('!!ss_crit!!', '', $tpl);
			}
			$select="";								
			foreach ($array as $id => $value) {
				if(isset($this->facettes[$i]->critere) && ($id==$this->facettes[$i]->critere)){
					$select.="<option value=".$id." selected='selected'>".$value."</option>";
				} else {
					$select.="<option value=".$id.">".$value."</option>";
				}
			}				
			$tpl = str_replace("!!liste1!!",$select,$tpl);
			$facettes_tpl.=$tpl;
		}
				
		$tpls = str_replace("!!facettes!!",$facettes_tpl,$tpls);
		$tpls = str_replace("!!max_facette!!",$nb,$tpls);
		$tpls = str_replace("!!id_bannette!!",$this->id,$tpls);

		return $tpls;
	}
	
	public function build_document($notice_ids,$notice_tpl="",$gen_summary=0,$gen_document=0){
		
		if($notice_tpl){
			$this->noti_tpl_document= notice_tpl_gen::get_instance($notice_tpl);
		} else $this->noti_tpl_document="";
		
		$facettes_list=$this->facettes;
		$this->gen_summary=$gen_summary;
		$this->summary="";
		$this->index=0;
		
		$res_notice_ids=$this->filter_facettes_search($facettes_list,$notice_ids);
		$resultat_aff=$this->filter_facettes_print($res_notice_ids,1,array(),$gen_document);
		
		if($this->gen_summary) $resultat_aff="<A NAME='SUMMARY'></A><div class='summary'><br />".$this->summary."</div>".$resultat_aff;
		
		return $resultat_aff;		
	}
	
	public function build_notice($notice_id, $id_bannette = 0){
		global $deflt2docs_location,$opac_url_base;
		
		global $use_opac_url_base; $use_opac_url_base=1;
		global $use_dsi_diff_mode;
		
		$url_base_opac = $opac_url_base."index.php?database=".DATA_BASE."&lvl=notice_display&id=";
		
		// paramétrage :
		$this->environement["short"] = 6 ;
		$this->environement["ex"] = 0 ;
		$this->environement["exnum"] = 1 ;
		
		if($this->noti_tpl_document) {
			$tpl_document=$this->noti_tpl_document->build_notice($notice_id, $deflt2docs_location, false, $id_bannette);
		} else {
			$tpl_document='';
		}
		if(!$tpl_document) {
			$n=pmb_mysql_fetch_object(@pmb_mysql_query("select * from notices where notice_id=".$notice_id));
			if ($n->niveau_biblio == 'm'|| $n->niveau_biblio == 'b') {
				$mono=new mono_display($n,$this->environement["short"],"",$this->environement["ex"],"","","",0,1,$this->environement["exnum"],0,"",0,true,false,0,0,1,$this->id);
				$tpl_document.= "<a href='".$url_base_opac.$n->notice_id.bannette::get_url_connexion_auto()."'><b>".$mono->header."</b></a><br /><br />\r\n";
				$tpl_document.= $mono->isbd;
			} elseif ($n->niveau_biblio == 's' || $n->niveau_biblio == 'a') {
				$serial = new serial_display($n, 6, "", "", "", "", "", 0,1,$this->environement["exnum"],0, false,0,0,'',false,1,$this->id);
				$tpl_document.= "<a href='".$url_base_opac.$n->notice_id.bannette::get_url_connexion_auto()."'><b>".$serial->header."</b></a><br /><br />\r\n";
				$tpl_document.= $serial->isbd;
			}
			$tpl_document=str_replace('<!-- !!avis_notice!! -->', "", $tpl_document);
			global $notice_separator;
			if($notice_separator)$tpl_document.=$notice_separator;
			else $tpl_document.="<div class='hr'><hr /></div>";
		}
		 return $tpl_document."\r\n";
	}
		
	public function filter_facettes_search($facettes_list,$notice_ids){
		global $dbh;
		global $lang;
		global $msg;
		global $dsi_bannette_notices_order ;

		$notices=implode(",",$notice_ids);
		$res_notice_ids=array();
		$res_notice_ids["values"]=array();
		$res_notice_ids["notfound"]=array();
			
		$critere= $facettes_list[0]->critere;
		$ss_critere= $facettes_list[0]->ss_critere;
	
		if ($dsi_bannette_notices_order) {
			$req = "SELECT * FROM notices_fields_global_index LEFT JOIN notices on (id_notice=notice_id)
			WHERE id_notice IN (".$notices.")
			AND code_champ = ".$critere."	AND code_ss_champ = ".$ss_critere." AND lang in ('','".$lang."') order by value,".$dsi_bannette_notices_order;
		} else {
			$req = "SELECT * FROM notices_fields_global_index
			WHERE id_notice IN (".$notices.")
			AND code_champ = ".$critere."	AND code_ss_champ = ".$ss_critere." AND lang in ('','".$lang."') order by value ";
		}	
		
		//		print $req."<br>";
		$res = pmb_mysql_query($req,$dbh);
		if (pmb_mysql_num_rows($res)) {
			while($r=pmb_mysql_fetch_object($res)){
				$res_notice_ids["folder"][$r->value]["values"][]= $r->id_notice;
				$res_notice_ids["memo"][]= $r->id_notice;
			}
			foreach($notice_ids as $id_notice ){
				if(!in_array($id_notice,$res_notice_ids["memo"]))	$res_notice_ids["notfound"][]=$id_notice;
			}
			// Si encore une facette d'affinage, on fait du récursif	
			if(count($facettes_list)>1){	
				array_splice($facettes_list, 0,1);
				foreach($res_notice_ids["folder"] as $folder => $contens){
					//printr($contens["values"]);
					$res_notice_ids["folder"][$folder]= $this->filter_facettes_search($facettes_list, $contens["values"]);
					//printr($res_notice_ids["folder"][$folder]);
						
					$res_notice_ids["folder"][$folder]["notfound_cumul"]=array();
					foreach($res_notice_ids["folder"][$folder]["values"] as $value){
						if(is_array($value["notfound"]))
							$res_notice_ids["folder"][$folder]["notfound_cumul"]=array_merge($res_notice_ids["folder"][$folder]["notfound_cumul"],$value["notfound"]);
					}
				}
			}
		}else{				
			$res_notice_ids["notfound"]=$notice_ids;
		}	
		return $res_notice_ids;
	}
	
	public function filter_facettes_print($res_notice_ids, $rang=1,$notfound=array(),$gen_document=0,&$already_printed=array()){
		global $dbh, $msg, $charset;
		global $lang;
		
		$tpl = "";
		if(count($res_notice_ids["notfound"])){
			$tpl.="<p$rang class='dsi_notices_no_class_rang_$rang'>";
			foreach($res_notice_ids["notfound"] as $notice_id){
				if( !in_array($notice_id, $notfound) )
				$tpl.="".$this->build_notice($notice_id)."<br />" ;
				$notfound[]=$notice_id;
			}
			$tpl.="</p$rang>";
		}	
		
		if(is_array($res_notice_ids["folder"])){
			
			foreach($res_notice_ids["folder"] as $folder => $contens){
				
				if((!$gen_document && $this->bannette_display_notice_in_every_group) || ($gen_document && $this->bannette_display_notice_in_every_group  && $this->bannette_document_group)){
					//on vide $already_printed pour afficher systèmatiquement la notice dans chaque groupe
					$already_printed=array();
				}
				
				if(!sizeof($already_printed) || sizeof(array_diff($contens["values"],$already_printed))){

					if($this->gen_summary && $rang==1){
						$this->index++;
						$this->summary.="<a href='#[".$this->index."]' class='summary_elt'>".htmlentities($this->index." - ".$folder,ENT_QUOTES,$charset)."</a><br />";
							
						if(!$gen_document || ($gen_document && $this->bannette_document_group)){
							$tpl.="<a name='[".$this->index."]'></a><h$rang class='dsi_rang_$rang'>".htmlentities($folder,ENT_QUOTES,$charset)."</h$rang>";
						}
					}else{
						if(!$gen_document || ($gen_document && $this->bannette_document_group)){
							$tpl.="<h$rang class='dsi_rang_$rang'>".htmlentities($folder,ENT_QUOTES,$charset)."</h$rang>";
						}
					}
					
					$tpl.="<p$rang class='dsi_notices_rang_$rang'>";
					
					foreach($contens["values"] as $notice_id){
						if(!in_array($notice_id,$already_printed)){
							$tpl.=$this->build_notice($notice_id)."<br />" ;
							if($gen_document && !$this->bannette_document_group){
								$tpl.="<div class='hr'><hr /></div>\r\n";
							}
							$already_printed[]=$notice_id;
						}
					}
					if(isset($contens["notfound"]) && count($contens["notfound"])){
						foreach($contens["notfound"] as $notice_id){
							if( !in_array($notice_id, $notfound) )
								$tpl.=$this->build_notice($notice_id)."<br />" ;						
								$notfound[]=$notice_id;
						}
					}
					
					$tpl.="</p$rang>";
					
					//printr($contens["folder"]);
					if(isset($contens["folder"]) && count($contens["folder"])){
						$rang++;
						// c'est une arborescence. Construction du titre
						$tpl.=$this->filter_facettes_print($contens,$rang,$notfound,$gen_document,$already_printed);
						$rang--;
					}	
				}elseif(isset($contens["folder"]) && count($contens["folder"])){
					
					foreach($contens['folder'] as $folder2=>$values2){
						if(!sizeof($already_printed) || sizeof(array_diff($values2["values"],$already_printed))){
							if($this->gen_summary && $rang==1){
								$this->index++;
								$this->summary.="<a href='#[".$this->index."]' class='summary_elt'>".htmlentities($this->index." - ".$folder,ENT_QUOTES,$charset)."</a><br />";
									
								if(!$gen_document || ($gen_document && $this->bannette_document_group)){
									$tpl.="<a name='[".$this->index."]'></a><h$rang class='dsi_rang_$rang'>".htmlentities($folder,ENT_QUOTES,$charset)."</h$rang>";
								}
							}else{
								if(!$gen_document || ($gen_document && $this->bannette_document_group)){
									$tpl.="<h$rang class='dsi_rang_$rang'>".htmlentities($folder,ENT_QUOTES,$charset)."</h$rang>";
								}
							}
							break;
						}
					}
					
					$rang++;
					// c'est une arborescence. Construction du titre
					$tpl.=$this->filter_facettes_print($contens,$rang,$notfound,$gen_document,$already_printed);
					$rang--;

				}	
			}	
		}
		return $tpl;
	}
	
	public function build_document_data($notice_ids,$notice_tpl=""){
		$this->sommaires=array();
		if($notice_tpl){
			$this->noti_tpl_document = notice_tpl_gen::get_instance($notice_tpl);
		} else $this->noti_tpl_document="";
	
		$facettes_list=$this->facettes;
		$this->index=0;
	
		$res_notice_ids=$this->filter_facettes_search($facettes_list,$notice_ids);
		$resultat_aff=$this->filter_facettes_data($res_notice_ids,1,array());
		return $this->sommaires;
	}
	
	public function filter_facettes_data($res_notice_ids, $rang=1,$notfound=array(),$gen_document=0,&$already_printed=array()){
		global $dbh, $msg, $charset;
		global $lang;
	
		if(count($res_notice_ids["notfound"])){
			//$this->sommaires[$this->index]['level']=$rang;
			foreach($res_notice_ids["notfound"] as $notice_id){
				if( !in_array($notice_id, $notfound) )					
					$this->sommaires[$this->index]['records'][]['render']=$this->build_notice($notice_id);				
				$notfound[]=$notice_id;
			}
		}	
		if(is_array($res_notice_ids["folder"])){				
			foreach($res_notice_ids["folder"] as $folder => $contens){
	
				if((!$gen_document && $this->bannette_display_notice_in_every_group) || ($gen_document && $this->bannette_display_notice_in_every_group  && $this->bannette_document_group)){
					//on vide $already_printed pour afficher systèmatiquement la notice dans chaque groupe
					$already_printed=array();
				}	
				if(!sizeof($already_printed) || sizeof(array_diff($contens["values"],$already_printed))){					
					$this->index++;
					$this->sommaires[$this->index]['title']=$folder;
					$this->sommaires[$this->index]['level']=$rang;												
					foreach($contens["values"] as $notice_id){
						if(!in_array($notice_id,$already_printed)){
							$this->sommaires[$this->index]['records'][]['render']=$this->build_notice($notice_id);
							$already_printed[]=$notice_id;
						}
					}
					if(isset($contens["notfound"]) && count($contens["notfound"])){
						foreach($contens["notfound"] as $notice_id){
							if( !in_array($notice_id, $notfound) )
							$this->sommaires[$this->index]['records'][]['render']=$this->build_notice($notice_id);
							$notfound[]=$notice_id;
						}
					}											
					//printr($contens["folder"]);
					if(isset($contens["folder"]) && count($contens["folder"])){
						$rang++;
						// c'est une arborescence. Construction du titre
						$this->filter_facettes_data($contens,$rang,$notfound,$gen_document,$already_printed);
						$rang--;
					}
				}elseif(isset($contens["folder"]) && count($contens["folder"])){
						
					foreach($contens['folder'] as $folder2=>$values2){
						if(!sizeof($already_printed) || sizeof(array_diff($values2["values"],$already_printed)) || !empty($values2['folder'])){
							$this->index++;
							$this->sommaires[$this->index]['title']=$folder;
							$this->sommaires[$this->index]['level']=$rang;						
							break;
						}
					}						
					$rang++;
					// c'est une arborescence. Construction du titre
					$this->filter_facettes_data($contens,$rang,$notfound,$gen_document,$already_printed);
					$rang--;
	
				}
			}
		}
		return 0;
	}	
		
}// end class
