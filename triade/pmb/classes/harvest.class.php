<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest.class.php,v 1.9 2019-01-16 15:48:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/harvest.tpl.php");
require_once($include_path."/parser.inc.php");
   	
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/facette_search_opac.class.php");

class harvest {
	public $id=0;
	public $info=array();
	public $fields_id=array();
	public $fields=array();
	
	public function __construct($id=0) {
		$this->id=$id+0;
		$this->fetch_data();
	}
	
	public function fetch_data() {
		global $include_path;
		
		$this->info=array();
		
		$nomfichier=$include_path."/harvest/harvest_fields.xml";
		if (file_exists($nomfichier)) {
			$fp = fopen($nomfichier, "r");		
			if ($fp) {
				//un fichier est ouvert donc on le lit
				$xml = fread($fp, filesize($nomfichier));
				//on le ferme
				fclose($fp);			
				$param=_parser_text_no_function_($xml,"HARVEST");
				$this->fields=$param["FIELD"];				
			}
  		}
  		$this->fields_id=array();
  		$i=0;
  		foreach($this->fields as $key => $field){
  			$this->fields_id[$this->fields[$key]["ID"]]=$field;			
  		}
		if(!$this->id) return;
		$req="select * from harvest_profil where id_harvest_profil=". $this->id;
		
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_harvest_profil;	
			$this->info['name']= $r->harvest_profil_name;	
		}	
		$this->info['fields']=array();	
		$req="select * from harvest_field where num_harvest_profil=".$this->id." order by harvest_field_order";
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){						
				$this->info['fields'][$r->harvest_field_xml_id]['id']= $r->id_harvest_field;	
				$this->info['fields'][$r->harvest_field_xml_id]['xml']= $r->harvest_field_xml_id;	
				$this->info['fields'][$r->harvest_field_xml_id]['first_flag']= $r->harvest_field_first_flag;	
				$cpt=0;
				$this->info['fields'][$r->harvest_field_xml_id]['src']=array();	
				$req_src="select * from harvest_src where num_harvest_field=". $r->id_harvest_field." order by harvest_src_order";
				$resultat_src=pmb_mysql_query($req_src);
				while($r_src=pmb_mysql_fetch_object($resultat_src)){	
					$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['id']=$r_src->id_harvest_src;
					$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['num_field']=$r_src->num_harvest_field;
					$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['num_source']=$r_src->num_source;
					$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['unimacfield']=$r_src->harvest_src_unimacfield;
					$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['unimacsubfield']=$r_src->harvest_src_unimacsubfield;			
					$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['pmb_unimacfield']=$r_src->harvest_src_pmb_unimacfield;		
					$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['pmb_unimacsubfield']=$r_src->harvest_src_pmb_unimacsubfield;					
					$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['prec_flag']=$r_src->harvest_src_prec_flag;			
					$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['order']=$r_src->harvest_src_order;

					$this->info['source_fields'][$r_src->num_source][]=$r->harvest_field_xml_id;
					$cpt++;
				}
			}
		} 	
		$this->info['connector']=array();
		$requete="SELECT connectors_categ_sources.num_categ, connectors_sources.source_id, connectors_categ.connectors_categ_name as categ_name, connectors_sources.name, connectors_sources.comment, connectors_sources.repository, connectors_sources.opac_allowed, source_sync.cancel FROM connectors_sources LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_source = connectors_sources.source_id) LEFT JOIN connectors_categ ON (connectors_categ.connectors_categ_id = connectors_categ_sources.num_categ) LEFT JOIN source_sync ON (connectors_sources.source_id = source_sync.source_id AND connectors_sources.repository=2) ORDER BY connectors_categ_sources.num_categ DESC, connectors_sources.name";
    	$resultat=pmb_mysql_query($requete);

    	while ($source=pmb_mysql_fetch_object($resultat)) {    		
    		$this->info['connector'][$source->source_id]=$source->name;     
    	}
    	$this->info['champ_base']=array(); 		
    	$facette=new facette_search_opac();
    //	printr($facette->fields_array["FIELD"][24]);
    	if(is_array($facette->fields_array["FIELD"])) {
	    	foreach($facette->fields_array["FIELD"] as $f){    		
	    		$id_field=$f['ID']+0;
	    		if($id_field==100) continue;
	    		$this->info['champ_base'][$id_field]['libelle']=$f['NAME'];    		
	    		$this->info['champ_base'][$id_field]['id']=$id_field;    		
	    		$this->info['champ_base'][$id_field]['table']='';
	    		$this->info['champ_base'][$id_field]['tabfield']=$f['TABLE'][0]['TABLEFIELD'][0]['value'];
	    		if($f['EXTERNAL']){
	    			$this->info['champ_base'][$id_field]['table']=$f['TABLE'][0]['NAME'];    			
	    			$this->info['champ_base'][$id_field]['key']=$f['TABLE'][0]['TABLEKEY'][0]['value'];
	    			$this->info['champ_base'][$id_field]['link']=$f['TABLE'][0]['LINK'][0]['REFERENCEFIELD'][0]['value'];    			
	    			$this->info['champ_base'][$id_field]['extable']=$f['TABLE'][0]['LINK'][0]['TABLE'][0]['value'];  			
	    			$this->info['champ_base'][$id_field]['exfield']=$f['TABLE'][0]['LINK'][0]['EXTERNALFIELD'][0]['value'];
	
		    		foreach($f['TABLE'][0]['TABLEFIELD'] as $ss_f){
		    			$id_ss_field=$ss_f['ID']+0;   		
	    				$this->info['champ_base'][$id_field]['ss_field'][$id_ss_field]['id']=$id_ss_field; 
	    				$this->info['champ_base'][$id_field]['ss_field'][$id_ss_field]['id_parent']=$id_field;    	
		    			$this->info['champ_base'][$id_field]['ss_field'][$id_ss_field]['tabfield']=$ss_f['value'];
		    			$this->info['champ_base'][$id_field]['ss_field'][$id_ss_field]['libelle']=$ss_f['NAME'];
		    		}
	    		}	    		
	    	}
    	}
    	
		$requete="SELECT * from harvest_search_field where num_harvest_profil=". $this->id;
    	$resultat=pmb_mysql_query($requete);
    	while ($source=pmb_mysql_fetch_object($resultat)) {  
    		if($this->info['connector'][$source->num_source]) { 		
	    		$this->info['search_field'][$source->num_source]['field']=$source->num_field; 
	    		$this->info['search_field'][$source->num_source]['ss_field']=$source->num_ss_field;    
    		}	     
    	}
  				
	// printr($this->info['champ_base'][28]);
	}
    
    public function get_code($num_source,$notice_id){
    	$field=$this->info['search_field'][$num_source]['field'];
    	$ss_field=$this->info['search_field'][$num_source]['ss_field'];
    	$data=$this->info['champ_base'][$field];
    	
    	if($ss_field){
     		$req="select x.".$data['ss_field'][$ss_field]['tabfield']." as code from ".$data['table']." as x, ".$data['extable']." as x2 where  
     		 x2.".$data['exfield']."=  x.".$data['key']."   and $notice_id= ".$data['link']." 
     		";   		
    	} else{
    		$req="select ".$data['tabfield']." as code from notices where notice_id=$notice_id ";
    	}
    	// print $req;
  		$resultat=@pmb_mysql_query($req);
    	if ($r=@pmb_mysql_fetch_object($resultat)) {    		
    		return   $r->code;  
    	}
    	return '';
    }
    
    public function havest_notice($isbn="",$notice_id=0){
    	global $charset, $class_path,$include_path,$base_path; 
		global $dbh,$msg;
		
		global $search;
		if(empty($search)) {
			$search = array();
		}
    	$search[]="s_2";
		
		global $op_0_s_2;
		$op_0_s_2="EQ";	
			
		global $field_0_s_2;	
		foreach( $this->info['source_fields'] as $source_id => $harvest_fields){
			$field_0_s_2[]=$source_id;
		}
		
    	$search[]="f_22";
			
		global $inter_1_f_22;
		$inter_1_f_22="or";	
		
		global $op_1_f_22;
		$op_1_f_22="STARTWITH";	
				
		global $field_1_f_22;
		$field_1_f_22[]=$isbn;	
		/*
    	foreach( $this->info['source_fields'] as $source_id => $harvest_fields){			
			if($notice_id){				
				$code=$this->get_code($source_id,$notice_id);
				$field_1_f_22[]=$code;
				 print 	$code.", ";
			}else	$field_1_f_22[]=$isbn;		
		}	
	*/
		global $explicit_search;
		$explicit_search="1";	
		
		$s=new search('',"search_fields_unimarc");	
		
		$res=$s->make_search();
		$req="select * from ".$res ;
		$resultat=pmb_mysql_query($req);
		while($r=pmb_mysql_fetch_object($resultat)){				
			// printr( $r);
			$recid=$r->notice_id;
			$requete = "SELECT source_id FROM external_count WHERE rid=".$r->notice_id.";";
			$myQuery = pmb_mysql_query($requete, $dbh);
			$source_id = pmb_mysql_result($myQuery, 0, 0);
				
			$req="select * from entrepot_source_".$source_id." where recid='".$recid."' order by ufield,field_order,usubfield,subfield_order,value";
			$res_entrepot=pmb_mysql_query($req);
			while($r_ent=pmb_mysql_fetch_object($res_entrepot)){
				$this->info['notice'][$source_id][$r_ent->ufield][]=$r_ent;	
			}	
			// on fait le ménage ou pas vu les requetes
			/*$req="DELETE FROM entrepot_source_".$source_id."  where where recid='".$recid."'  ";
	    	pmb_mysql_query($req);			    		
			$req="DELETE FROM FROM external_count WHERE rid=".$r->notice_id."";
	    	pmb_mysql_query($req);*/
		}
		// printr(	$this->info['notice']);    
	    $notice_composite=array();
	    $cpt=0;
	    // $this->info['fields'][$r->harvest_field_xml_id]['xml']
	    foreach($this->info['fields'] as $xml_id=>$src_list){	    	
	    	//printr( $src_list);
	    	$first_flag=$src_list['first_flag'];  	
	    	
	    	foreach($src_list['src'] as $src){
	    		$prec_flag=$src['prec_flag'];
	    		$unimacsubfield=$src['unimacsubfield'];	// source sub_field
	    		$pmb_unimacfield=$src['pmb_unimacfield']; // destination $this->fields_id[$this->fields[$key]["ID"]]
	    		
	    		$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['pmb_unimacfield']=$r_src->harvest_src_pmb_unimacfield;		
				$this->info['fields'][$r->harvest_field_xml_id]['src'][$cpt]['pmb_unimacsubfield']=$r_src->harvest_src_pmb_unimacsubfield;	
	    		$found=0;
	    		if($this->info['notice'][ $src['num_source'] ][$src['unimacfield']]){	    			
	    			foreach($this->info['notice'][ $src['num_source'] ][$src['unimacfield']] as $notice_field){
	    				$no_memo_subfield_flag=0;
	    				if($unimacsubfield && ($notice_field->usubfield != $unimacsubfield)){
	    					$no_memo_subfield_flag=1;
	    				}
	    				//printr( $notice_ufield);
	    				if($notice_field->value && !$no_memo_subfield_flag){
	    						    					
	    					$notice_composite[$cpt]['xml_id']=$xml_id;
	    					$notice_composite[$cpt]['num_source']=$src['num_source'];
	    					$notice_composite[$cpt]['ufield']=$pmb_unimacfield;
	    					$notice_composite[$cpt]['field_ind']=$notice_field->field_ind;
	    					$notice_composite[$cpt]['usubfield']=$notice_field->usubfield;
	    					$notice_composite[$cpt]['field_order']=$notice_field->field_order;
	    					$notice_composite[$cpt]['subfield_order']=$notice_field->subfield_order;
	    					$notice_composite[$cpt]['value']=$notice_field->value;
	    					$notice_composite[$cpt]['pmb_unimacfield']=$this->fields_id[$xml_id]['UNIMARCFIELD'];
	    					$notice_composite[$cpt]['pmb_unimacsubfield']=$this->fields_id[$xml_id]['UNIMARCSUBFIELD'];
	    					$cpt++;
	    					$found=1;
	    				}
	    			}
	    		}
	    		// une valeur est trouvée , on ne s'occupe pas des sources suivantes si demandé
	    		if($first_flag && $found) {
	    			break;
	    		}
	    	}	    	
	    } 
	   // printr(	$this->info['notice']);
	    return($notice_composite);	
	    
    }
    
	public function get_form() {
		global $harvest_form_tpl, $harvest_form_elt_tpl,$msg,$charset;
		global $harvest_form_elt_ajax_tpl,$harvest_form_elt_src_tpl;
		
		$tpl=$harvest_form_tpl;
		if($this->id){
			$tpl=str_replace('!!msg_title!!',$msg['admin_harvest_build_form_edit'],$tpl);
			$tpl=str_replace('!!delete!!',"<input type='button' class='bouton' value='".$msg['admin_harvest_build_delete']."'  onclick=\"document.getElementById('action').value='delete';this.form.submit();\"  />", $tpl);
			$name=$this->info['name'];
		}else{ 
			$tpl=str_replace('!!msg_title!!',$msg['admin_harvest_build_form_add'],$tpl);
			$tpl=str_replace('!!delete!!',"",$tpl);
			$name="";
		}
		$tpl=str_replace('!!name!!',htmlentities($name, ENT_QUOTES, $charset),$tpl);
		
		$elt_list="";
		
		foreach($this->fields as $field){	// pour tout les champs unimarc à récolter	
			$elt=$harvest_form_elt_tpl;
			$nb=0;
			
			$elt=str_replace("!!pmb_field_msg!!",$msg[$field["NAME"]],$elt);
			
			if($this->id && $this->info['fields'][$field["ID"]]['src']){
				// Edition: les valeurs des champs sont issues de la base		
				$add_zone_harvest="";	
				if($this->info['fields'][$field["ID"]]['first_flag']) $first_flag=" checked='checked' ";
				else $first_flag="";
				$elt=str_replace("!!first_flagchecked!!",$first_flag,$elt);
				foreach($this->info['fields'][$field["ID"]]['src'] as $harvest_src){	
					if(!$nb){
						// Le principal
						$elt=$this->build_memo_field($elt,$field,$harvest_src,$nb);
					} else{
						// Les autres ajouts de sources
						$zone_suite=$harvest_form_elt_src_tpl;
						$zone_suite=$this->build_memo_field($zone_suite,$field,$harvest_src,$nb);
						$zone_suite=str_replace("!!nb!!",$nb,$zone_suite);	
			
						$add_zone_harvest.=$zone_suite;
					}					
					$nb++;
				}
				$elt=str_replace("!!nb!!",$nb,$elt);	
				
			} else { 
				// Création:les valeurs des champs sont issues du fichier XML
				$elt=str_replace("!!first_flagchecked!!"," checked='checked' ",$elt);
				$elt=$this->build_new_field($elt,$field,$nb);					
				$elt=str_replace("!!nb!!",0,$elt);		
				$add_zone_harvest="";	
			}			
			$elt=str_replace('!!add_zone_harvest!!',$add_zone_harvest,$elt);	
				
			// pour pouvoir ajouter une nouvelle source en js
			$add_tpl=$harvest_form_elt_ajax_tpl;
			$add_tpl=$this->build_new_field($add_tpl,$field,'!!nb!!');							
			$elt=str_replace('!!harvest_field_form_add!!',$add_tpl,$elt);	
								
			$elt=str_replace("!!id!!",$field["ID"],$elt);			
			$elt_list.=$elt;
		}
		$src_list=$this->get_src_list();
		$tpl=str_replace('!!src_list!!',$src_list,$tpl);
		$tpl=str_replace('!!elt_list!!',$elt_list,$tpl);	
		$tpl=str_replace('!!id_harvest!!',$this->id,$tpl);
		 
		return $tpl;
	}
	
	public function build_memo_field($elt,$field,$data_field,$nb){	
		// 	!!unimarcfield!! !!subfield!! !!sources!! !!pmb_unimarc_select!!		
		$elt=str_replace('!!unimarcfield!!',$this->build_unimarcfield($field["ID"],$nb,$data_field["unimacfield"]),$elt);

		if(isset($field["UNIMARCSUBFIELD"]) && $field["UNIMARCSUBFIELD"]) $elt=str_replace("!!subfield!!",$this->build_unimarcsubfield($field["ID"],$nb,$data_field["unimacsubfield"]),$elt);
		else $elt=str_replace("!!subfield!!","",$elt);
	
		$elt=str_replace('!!sources!!',$this->build_sources($field["ID"],$nb,$data_field["num_source"]),$elt);
		$elt=str_replace('!!pmb_unimarc_select!!',$this->build_pmbunimarcfield($field["ID"],$nb,$field["UNIMARCFIELD"],$data_field["pmb_unimacfield"]),$elt);	
		
		$elt=str_replace('!!onlylastempty!!',$this->build_lastempty($field["ID"],$nb,$data_field["prec_flag"]),$elt);	
		return 	$elt;	
	}
	
	public function build_new_field($elt,$field,$nb){
		// 	!!unimarcfield!! !!subfield!! !!sources!! !!pmb_unimarc_select!!
		$elt=str_replace('!!unimarcfield!!',$this->build_unimarcfield($field["ID"],$nb,$field["UNIMARCFIELD"]),$elt);

		if(isset($field["UNIMARCSUBFIELD"]) && $field["UNIMARCSUBFIELD"]) $elt=str_replace("!!subfield!!",$this->build_unimarcsubfield($field["ID"],$nb,$field["UNIMARCSUBFIELD"]),$elt);
		else $elt=str_replace("!!subfield!!","",$elt);
	
		$elt=str_replace('!!sources!!',$this->build_sources($field["ID"],$nb,''),$elt);
		$elt=str_replace('!!pmb_unimarc_select!!',$this->build_pmbunimarcfield($field["ID"],$nb,$field["UNIMARCFIELD"],''),$elt);	
		
		$elt=str_replace('!!onlylastempty!!',$this->build_lastempty($field["ID"],$nb,1),$elt);	
		return 	$elt;	
	}
	public function build_sources($id,$nb,$val){
		global $msg,$charset;
		$field=$this->get_sources_sel($id,$nb,$val);
		return $field;
	}
	public function build_unimarcfield($id,$nb,$val){
		global $msg,$charset;
		$tab=explode(',',$val);
		$field="<input type='text' size='3' name='unimarcfield_".$id."_".$nb."' value='".$tab[0]."' />";
		return $field;
	}	
	public function build_unimarcsubfield($id,$nb,$val){
		global $msg,$charset;
		$field="<input type='text' size='1' name='unimarcsubfield_".$id."_".$nb."' id='unimarcsubfield_".$id."_".$nb."' value='".$val."'/> ";
		
		return $field;
	}
	public function build_pmbunimarcfield($id,$nb,$unimacfields,$val){
		global $msg,$charset;
				
		$tab=explode(',',$unimacfields);
		if(count($tab)>1){
			$sel_unimac="<select name='pmb_unimarc_".$id."_".$nb."' >";
			$first=$val;
			foreach($tab as $uni){
				if(!$first)	$first=$uni; // premier par défaut			
				if($first==$uni){
					$selected = " selected='selected' ";
				}else $selected = "";
				$sel_unimac.="<option value='$uni' $selected>".$uni."</option>\n";
			}
			$sel_unimac.="</select>";	
		}else{
			$sel_unimac="<input type='hidden' name='pmb_unimarc_".$id."_".$nb."' value='".$tab[0]."'>";	
		}
		return $sel_unimac;
	}
	public function build_lastempty($id,$nb,$val){
		global $msg,$charset;
		if($val) $checked=" checked='checked' ";
		$field="<input type='checkbox'  name='onlylastempty_".$id."_".$nb."' id='onlylastempty_".$id."_".$nb."' $checked value='1' /> ".$msg['admin_harvest_build_form_onlylastempty']."";
		return $field;
	}

	public function save($data) {
		global $dbh;
		if(!$this->id){ // Ajout
			$req="INSERT INTO harvest_profil SET 
				harvest_profil_name='".$data['name']."'
			";	
			pmb_mysql_query($req, $dbh);
			$this->id = pmb_mysql_insert_id($dbh);
		} else {
			$req="UPDATE harvest_profil SET 
				harvest_profil_name='".$data['name']."'
				where 	id_harvest_profil=".$this->id;	
			pmb_mysql_query($req, $dbh);				
				
			foreach($this->info['fields'] as $harvest_field){				
				$req="DELETE from harvest_src WHERE num_harvest_field=".$harvest_field['id'];	
				pmb_mysql_query($req, $dbh);
			}			
			$req=" DELETE from harvest_field WHERE num_harvest_profil=".$this->id;
			pmb_mysql_query($req, $dbh);			
					
			$req=" DELETE from harvest_search_field WHERE num_harvest_profil=".$this->id;
			pmb_mysql_query($req, $dbh);					
		}
		$cpt_fields=0;
		foreach($this->fields as $field ){
			$var="firstfound_".$field["ID"];
			global ${$var};
    		$first_flag=${$var}+0;
    		
			$req="INSERT INTO harvest_field SET 
				num_harvest_profil=".$this->id.",
				harvest_field_xml_id=".$field["ID"].",
				harvest_field_first_flag=".$first_flag.",
				harvest_field_order=".$cpt_fields++."	
			";	
			pmb_mysql_query($req, $dbh);
			$harvest_field_id = pmb_mysql_insert_id($dbh);	
			
			$var="unimarcfieldnumber_".$field["ID"];
			global ${$var};
    		$nb_srce=${$var};
    		$cpt=0;
    		for($i=0;$i<=$nb_srce;$i++){    					
	    		$var="unimarcfield_".$field["ID"]."_".$i;
	    		global ${$var};
	    		$unimarcfield=${$var};
	    		if($unimarcfield){ 
	    			$var="unimarcsubfield_".$field["ID"]."_".$i;
		    		global ${$var};
		    		$unimarcsubfield=${$var};
		    		
	    			$var="source_".$field["ID"]."_".$i;
		    		global ${$var};
		    		$source=${$var}+0;				
		    			
		    		$var="pmb_unimarc_".$field["ID"]."_".$i;
		    		global ${$var};
		    		$pmb_unimarc=${$var};
		    		
		    		$var="onlylastempty_".$field["ID"]."_".$i;
		    		global ${$var};
		    		$rec_flag=${$var}+0;
		    		
		    		$req="INSERT INTO harvest_src SET 
					num_harvest_field=".$harvest_field_id.",
					num_source=".$source.",
					harvest_src_unimacfield='".$unimarcfield."',
					harvest_src_unimacsubfield='".$unimarcsubfield."',	
					harvest_src_pmb_unimacfield='".$pmb_unimarc."',		
					harvest_src_prec_flag=".$rec_flag.",	
					harvest_src_order=".$cpt."
					";	
					pmb_mysql_query($req, $dbh);
					$cpt++;
	    		}	    		
    		}
		}
		foreach($this->info['connector'] as $source=> $name){
			$var="list_crit_".$source;
			global ${$var};
    		$field=${$var}+0;
			$var="list_ss_champs_".$source;
			global ${$var};
    		$ss_field=${$var}+0;
    		if($field){
    			$req="INSERT INTO harvest_search_field SET 
					num_harvest_profil=".$this->id.",
					num_source=".$source.",
					num_field='".$field."',
					num_ss_field='".$ss_field."'
				";	
				pmb_mysql_query($req, $dbh);	
    		}
		}
		$this->fetch_data();
	}	
	
	public function delete() {
		global $dbh;
		foreach($this->info['fields'] as $harvest_field){				
			$req="DELETE from harvest_src WHERE num_harvest_field=".$harvest_field['id'];	
			pmb_mysql_query($req, $dbh);
		}			
		$req=" DELETE from harvest_field WHERE num_harvest_profil=".$this->id;
		pmb_mysql_query($req, $dbh);				
		$req=" DELETE from  harvest_profil where id_harvest_profil=". $this->id;
		pmb_mysql_query($req, $dbh);								
		$req=" DELETE from harvest_search_field WHERE num_harvest_profil=".$this->id;
		pmb_mysql_query($req, $dbh);	
		
		$this->fetch_data();	
	}	
	
    public function get_sources_sel($id_field,$nb,$value) {
    	global $msg,$charset;
    	
    	//Recherche des sources
    	$requete="SELECT connectors_categ_sources.num_categ, connectors_sources.source_id, connectors_categ.connectors_categ_name as categ_name, connectors_sources.name, connectors_sources.comment, connectors_sources.repository, connectors_sources.opac_allowed, source_sync.cancel FROM connectors_sources LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_source = connectors_sources.source_id) LEFT JOIN connectors_categ ON (connectors_categ.connectors_categ_id = connectors_categ_sources.num_categ) LEFT JOIN source_sync ON (connectors_sources.source_id = source_sync.source_id AND connectors_sources.repository=2) ORDER BY connectors_categ_sources.num_categ DESC, connectors_sources.name";
    	$resultat=pmb_mysql_query($requete);
    	$r="<select name='source_".$id_field."_".$nb."' >";
    	$current_categ=0;
    	$count = 0;
    	if(!$value)$selected=" selected='selected' ";
    	while ($source=pmb_mysql_fetch_object($resultat)) {
    		if ($current_categ !== $source->num_categ) {
    			$current_categ = $source->num_categ;
    			$source->categ_name = $source->categ_name ? $source->categ_name : $msg["source_no_category"];
    			$r .= "<optgroup label='".$source->categ_name."'>";
    			$count++;
    		}
    		if($value==$source->source_id){
    			$selected=" selected='selected' ";
    		}
    		$r.="<option $selected value='".$source->source_id."' >".htmlentities($source->name.($source->comment?" : ".$source->comment:""),ENT_QUOTES,$charset)."</option>\n";
    		$selected="";
    	}
    	$r.="</select>";
    	return $r;
    }

	public function create_list_fields($array,$source_id=0,$id_selected=0,$ss_field=0){
		global $msg;
		
		$select ="<select id='"."list_crit_".$source_id."' name='list_crit_".$source_id."' onchange=\"load_subfields('".$source_id."',0)\">";		
		$selected="";
		if(!$id_selected)$selected=" selected='selected' ";
		$select.="<option value='0' $selected>".$msg["connecteurs_no_upload_rep"]."</option>";
		foreach ($array as $id => $value) {			
			if($id==$id_selected){
				$select.="<option value=".$id." selected='selected'>".$value."</option>";
			} else {
				$select.="<option value=".$id.">".$value."</option>";
			}
		}
		$select.="</select></br>
		<div id='list_ss_crit_$source_id'>
		
		</div>";		
		if($ss_field) $select .= "<script>load_subfields($source_id, $ss_field)</script>";
		return $select;
	}
	
    public function get_src_list() {
    	global $msg,$charset;
    	
    	$r="
    		<script type='text/javascript'>
    					
				function load_subfields(source_id,id_ss_champs){
				
					var lst = document.getElementById('list_crit_'+source_id);
					var id = lst.value;
					var lst = document.getElementById('list_ss_crit_'+source_id);
					if(id=='0'){	
						lst.innerHTML =	'';
						return;		
					}
					var xhr_object=  new http_request();					
					xhr_object.request('./ajax.php?module=admin&categ=opac&section=lst_facette',1,'list_crit=' +id+ '&sub_field=' +id_ss_champs+ '&suffixe_id='+source_id );
					lst.innerHTML = xhr_object.get_text();
				}
			</script>";
    	//Recherche des sources
    	$requete="SELECT connectors_categ_sources.num_categ, connectors_sources.source_id as source_id, connectors_categ.connectors_categ_name as categ_name, connectors_sources.name, connectors_sources.comment, connectors_sources.repository, connectors_sources.opac_allowed, source_sync.cancel FROM connectors_sources LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_source = connectors_sources.source_id) LEFT JOIN connectors_categ ON (connectors_categ.connectors_categ_id = connectors_categ_sources.num_categ) LEFT JOIN source_sync ON (connectors_sources.source_id = source_sync.source_id AND connectors_sources.repository=2) ORDER BY connectors_categ_sources.num_categ DESC, connectors_sources.name";
    	$resultat=pmb_mysql_query($requete);
    	
    	$current_categ=0;
    	$count = 0;
    	
    	$facette=new facette_search_opac();
    	
    	while ($source=pmb_mysql_fetch_object($resultat)) {
    		$r.="
    			<tr>
    				<td>".
    				htmlentities($source->name.($source->comment?" : ".$source->comment:""),ENT_QUOTES,$charset)."
    				</td>
    				<td>".
    				$this->create_list_fields($facette->fields_sort(),$source->source_id,(isset($this->info['search_field']) ? $this->info['search_field'][$source->source_id]['field'] : ''), (isset($this->info['search_field']) ? $this->info['search_field'][$source->source_id]['ss_field'] : ''))."
    				</td>
    			</tr>";    			
    	}
    	
    	return $r;
    }    
} //harvest class end









class harvests {	
	public $info=array();
	
	public function __construct() {
		$this->fetch_data();
	}
	
	public function fetch_data() {
		$this->info=array();
		$i=0;
		$req="select * from harvest_profil ";
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info[$i]= $harvest=new harvest($r->id_harvest_profil);	
				
				$i++;
			}
		}	
	}
		
	public function get_list() {
		global $harvest_list_tpl,$harvest_list_line_tpl,$msg;
		
		$tpl=$harvest_list_tpl;
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info as $elt){
			$tpl_elt=$harvest_list_line_tpl;
			if($odd_even=='odd')$odd_even="even";
			else $odd_even="odd";
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);	
			$tpl_elt=str_replace('!!name!!',$elt->info['name'], $tpl_elt);	
			$tpl_elt=str_replace('!!id!!',$elt->info['id'], $tpl_elt);	
			$tpl_list.=$tpl_elt;	
		}
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		return $tpl;
	}	
	
	public function get_sel($sel_name,$sel_id=0) {
		global $harvest_list_tpl,$harvest_list_line_tpl,$msg;
		$tpl="<select name='$sel_name' >";
				
		foreach($this->info as $elt){
			if($elt->info['id']==$sel_id){
				$tpl.="<option value=".$elt->info['id']." selected='selected'>".$elt->info['name']."</option>";
			} else {
				$tpl.="<option value=".$elt->info['id'].">".$elt->info['name']."</option>";
			}
		}
		$tpl.="</select>";
		return $tpl;
	}		
} //harvests class end
	
