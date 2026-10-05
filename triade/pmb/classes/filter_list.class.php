<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: filter_list.class.php,v 1.53 2023/08/30 14:32:32 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/parser.inc.php");

class filter_list {
	public $filter_name; //nom du filtre (fichier)
	public $filter_source; //source xml (texte xml ou fichier)
	public $params; //tableau xml
	public $fixedfields; //tableau des champs fixes du xml
	public $specialfields; //tableau des champs spéciaux du xml
	public $fixedcolumns; //colonnes fixes (ids séparés par des virgules)
	public $sortablecolumns; //liste des champs dans l'ordre où s'effectuera le tri 
	public $filtercolumns; //liste des champs sur lesquels filtrer
	public $displaycolumns; //liste des champs à afficher
	public $specialcolumns; //liste des champs spéciaux à afficher (ids séparés par des virgules)
	public $multiple; //option multiple de la liste des filtres
	public $error; //booléen d'erreur
	public $error_message; //message de l'erreur
	public $css = array(); //style d'affichage
	public $scripts = array(); //scripts sur les lignes de résultat
	public $page; //page en cours
	public $nb_per_page; //nombre d'enregistrements par page
	public $query; //texte de la requête finale
	public $t_query; //ressource id de la requête finale
	public $no_filter; //booléen de filtrage ou non
	public $original_query; //requête d'origine
	public $select_original=""; //select d'origine
	public $where_original=""; //where d'origine
	public $from_original=""; //from d'origine
	public $filtered_query=""; //résultat de filters_query
	
    public function __construct($filter_name,$filter_source="",$display="",$filter="",$sort="") {
    	$this->filter_name=$filter_name;
    	$this->filter_source=$filter_source;
    	$this->parse();	
    	$this->fixedcolumns=$this->params["REFERENCE"][0]["FIXED"];
    	$this->displaycolumns=$display;
    	if (!$this->displaycolumns) $this->displaycolumns=$this->fixedcolumns;
    	$this->sortablecolumns=$sort;
    	if (!$this->sortablecolumns) $this->sortablecolumns=$this->fixedcolumns;
    	$this->filtercolumns=$filter;
    	if (!$this->filtercolumns) $this->filtercolumns=$this->fixedcolumns;
    }
    
    //fonction d'activation du filtre
    public function activate_filters() {
    	global $msg, $charset;
    	global $default_tmp_storage_engine;

    	$requete=$this->display_query();
    	if (!$this->no_filter) {
    		if ($this->original_query) {
    			$temp=substr($this->original_query,0,strpos(strtolower($this->original_query),"from"));
    			$pos=strpos($temp,$this->params["REFERENCEKEY"][0]['value']);
    			if ($pos===FALSE) {
    				$this->error=true;
    				$this->error_message=htmlentities(str_replace("%s",$this->params["REFERENCEKEY"][0]['value'],$msg["filters_original_query_no_key"]),ENT_QUOTES,$charset);
    			} else {
    				if ($this->original_query) $requete.=",table_filter_tempo";
    			}
    		}
    		if (!$this->filtered_query){
    			$where=$this->filters_query();
    		}else{
    			$where=$this->filtered_query;
    		}	
    				
    		if ($where){
    			$requete.=$where;
    		}else{
    			$this->no_filter=1;
    			$requete.=" where 1 ";
    		}
    	}
    	if (($this->original_query)&&(!$this->error)) {
    		//création d'une table temporaire
    		pmb_mysql_query("drop table if exists table_filter_tempo");
    		$creer_table_tempo="CREATE TEMPORARY TABLE table_filter_tempo ENGINE={$default_tmp_storage_engine} (".$this->original_query.")";
    		pmb_mysql_query($creer_table_tempo);
    		$modif_primaire="ALTER TABLE table_filter_tempo add PRIMARY KEY (".$this->params["REFERENCEKEY"][0]['value'].")";
    		pmb_mysql_query($modif_primaire);  
    		$requete.=" and ".$this->params["REFERENCE"][0]['value'].".".$this->params["REFERENCEKEY"][0]['value']."=table_filter_tempo.".$this->params["REFERENCEKEY"][0]['value'];
    		$requete.=" group by ".$this->params["REFERENCE"][0]['value'].".".$this->params["REFERENCEKEY"][0]['value'];
    	}
    	$requete.=$this->sort_query();
    	if (!$this->no_filter) {
    		$this->t_query=pmb_mysql_query($requete);
    	}
    	$requete.=$this->pager_query();
    	$this->query=$requete;
    	if (pmb_mysql_error()) $erreur=pmb_mysql_error();
    	else $erreur='';
    	if ($erreur) {
    		$this->error=true;
    		$this->error_message=$erreur;
    	} 	
    }
    
    //fonction de traduction des filtres en langage naturel
    public function make_human_filters() {
    	global $class_path;
    	global $msg;
    	global $charset;
    	    	    	
    	$ret="";
    	
    	$human_filters=htmlentities($msg["filters_label"],ENT_QUOTES,$charset)." ";
    	$s=explode(",",$this->filtercolumns);	
    	//parcours des champs 
    	for ($i=0;$i<count($s);$i++) {
    		if(substr($s[$i],0,2) == "#p") $cp_ref = 1;
    		else $cp_ref = 0;
    		if ((substr($s[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    			//champs personnalisés
    			require_once($class_path."/parametres_perso.class.php");
    			$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    			if (!$cp->no_special_fields) {
    				$id=substr($s[$i],2);
    				$valeurs_post="f".$cp->t_fields[$id]["NAME"];
    				global ${$valeurs_post};
    				$v=array();
    				if (${$valeurs_post}) $v=${$valeurs_post};
    				if(count($v) > 1 || (is_array($v) && isset($v[0]) && $v[0] != "-1" && $v[0] != "")){
    					//Récupération du champ
	    				$field=array();
						$field['ID']=$id;
						$field['NAME']=$cp->t_fields[$id]['NAME'];
						$field['MANDATORY']=$cp->t_fields[$id]['MANDATORY'];
						$field['ALIAS']=$cp->t_fields[$id]['TITRE'];
						$field['DATATYPE']=$cp->t_fields[$id]['DATATYPE'];
						$field['OPTIONS']=$cp->t_fields[$id]['OPTIONS'];
						$field['VALUES']=$v;
						$field['PREFIX']=$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"];
						if (($cp->t_fields[$id]['TYPE']!="list")&&($cp->t_fields[$id]['TYPE']!="query_list")) {
							$field['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE']="-1";
							$field['OPTIONS'][0]['UNSELECT_ITEM'][0]['value']=$msg["empr_perso_all_values"];
						}
						$human_filters.=strtolower($cp->t_fields[$id]["TITRE"])." \"";
						$temp=array();
						foreach($v as $dummykey) {
							if ($dummykey!=$field['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE']) {
	    						if (($field['DATATYPE']=="text")||($field['DATATYPE']=="comment")) $temp[]=$dummykey;
	    							else $temp[]=$cp->get_formatted_output(array($dummykey),$id);
							}
						}
						if (count($temp)) {
							$human_filters.=implode(",",$temp);
						}
						$human_filters.="\" ".htmlentities($msg["filters_sort_next"],ENT_QUOTES,$charset)." ";	
    				}	
    			}
    		} elseif (array_key_exists($s[$i],$this->fixedfields)) {
    	 		//champs fixes
    			//est-ce que le champ est filtrable	
    	 		if ($this->fixedfields[$s[$i]]["FILTERABLE"]=="yes") {
    	 			$nom_valeur_post="f".$this->fixedfields[$s[$i]]["ID"];
    				$valeur_post=array();
    				global ${$nom_valeur_post};
    				$valeur_post=${$nom_valeur_post};
    				if (is_array($valeur_post)) {
    					$t=array();
    					$t[0]=-1;
    					$v=array_diff($valeur_post,$t);
    					if (count($v)) {
    						if (substr($this->fixedfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->fixedfields[$s[$i]]["NAME"],4,strlen($this->fixedfields[$s[$i]]["NAME"])-4)];
    							else $nom=$this->fixedfields[$s[$i]]["NAME"];
    						$human_filters.=strtolower($nom)." \"";
    						if ($this->fixedfields[$s[$i]]["TABLE"] && isset($this->fixedfields[$s[$i]]["TABLEKEY"])) {
    							$temp=array();
    							foreach($v as $dummykey) {
    	 							$requete="select ".$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value']." from ";
    	 							$requete.=$this->fixedfields[$s[$i]]["TABLE"][0]['value'];
    	 							$requete.=" where ".$this->fixedfields[$s[$i]]["TABLEKEY"][0]['value']."='".$dummykey."'";
    	 							$execute_query=pmb_mysql_query($requete);
    	 							$resultat_query=pmb_mysql_fetch_row($execute_query);
    	 							$temp[]=$resultat_query[0];	
    	 						}
    	 						$human_filters.=implode(",",$temp);
    						} else {
    							$human_filters.=htmlentities(stripslashes(implode(",",$v)),ENT_QUOTES,$charset);
    						}
    						$human_filters.="\" ".htmlentities($msg["filters_sort_next"],ENT_QUOTES,$charset)." ";
    					}
    				}
    	 		}	 
    	 	} else {
    	 		$nom_valeur_post="f".$this->specialfields[$s[$i]]["ID"];
    			$valeur_post=array();
    			global ${$nom_valeur_post};
    			$valeur_post=${$nom_valeur_post};
    			if (is_array($valeur_post)) {
    				$t[0]=-1;
    				$v=array_diff($valeur_post,$t);
    				if (count($v)) {
    					$temp=array();
    					if (substr($this->specialfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->specialfields[$s[$i]]["NAME"],4,strlen($this->specialfields[$s[$i]]["NAME"])-4)];
    						else $nom=$this->specialfields[$s[$i]]["NAME"];
    					$human_filters.=strtolower($nom)." \"";
    					foreach($v as $dummykey) {
    						$temp[]=$dummykey;
    					}
    					$human_filters.=htmlentities(stripslashes(implode(",",$temp)),ENT_QUOTES,$charset);
    					$human_filters.="\" ".htmlentities($msg["filters_sort_next"],ENT_QUOTES,$charset)." ";
    				}
    			}
    		}    					
    	}
    	if ($human_filters!=htmlentities($msg["filters_label"],ENT_QUOTES,$charset)." ") $human_filters=substr($human_filters,0,strlen($human_filters)-(strlen($msg["filters_sort_next"])+2));	
    		else $human_filters="";
    	    	    	
    	//champs triables
    	$human_sort=htmlentities($msg["sort_label"],ENT_QUOTES,$charset)." ";
    	$s=explode(",",$this->sortablecolumns);	
    	//parcours des champs 
    	for ($j=0;$j<count($s);$j++) {
    		$sort_list="sort_list_".$j;
    		global ${$sort_list};
    		if (!${$sort_list}) ${$sort_list}=$s[$j];
    		if (${$sort_list}!="-1") {
    			for ($i=0;$i<count($s);$i++) {
    				if(substr($s[$i],0,2) == "#p") $cp_ref = 1;
    				else $cp_ref = 0;
    				if (${$sort_list}==$s[$i]) {
    					if ((substr($s[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    						//champs personnalisés
    						require_once($class_path."/parametres_perso.class.php");
    						$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    						if (!$cp->no_special_fields) {
    							$id=substr($s[$i],2,strlen($s[$i])-1);
    							$human_sort.=strtolower($cp->t_fields[$id]["TITRE"])." ".htmlentities($msg["filters_sort_next"],ENT_QUOTES,$charset)." ";
    						}
    					} elseif (array_key_exists($s[$i],$this->fixedfields)) {
    	 					//champs fixes
    						//est-ce que le champ est filtrable	
    	 					if ($this->fixedfields[$s[$i]]["SORTABLE"]=="yes") {
    							if (substr($this->fixedfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->fixedfields[$s[$i]]["NAME"],4,strlen($this->fixedfields[$s[$i]]["NAME"])-4)];
    								else $nom=$this->fixedfields[$s[$i]]["NAME"];
    							$human_sort.=strtolower($nom)." ".htmlentities($msg["filters_sort_next"],ENT_QUOTES,$charset)." ";
    						}	 
    					} else {
    						if (substr($this->specialfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->specialfields[$s[$i]]["NAME"],4,strlen($this->specialfields[$s[$i]]["NAME"])-4)];
    							else $nom=$this->specialfields[$s[$i]]["NAME"];
    						$human_sort.=strtolower($nom)." ".htmlentities($msg["filters_sort_next"],ENT_QUOTES,$charset)." ";
    					}
    				}    					
    			}
    		}
    	}
    	if ($human_sort!=htmlentities($msg["sort_label"],ENT_QUOTES,$charset)." ") $human_sort=substr($human_sort,0,strlen($human_sort)-(strlen($msg["filters_sort_next"])+2));	
    			else $human_sort="";
    	
    	if (($human_sort)||($human_filters)) $ret="<div class='row'>".$human_filters."</div><div class='row'>".$human_sort."</div>";	
    	return $ret;
    }
    
    //fonction de manipulation de données pour le contenu du select et du from dans la requête
    public function display_query() {
    	global $class_path;
    	global $msg, $charset;
    	$froms=array();
    	$dependant_left_joins=array();
    	$ret="";
    	$select=$this->params["REFERENCE"][0]['value'].".".$this->params["REFERENCEKEY"][0]['value'].",";
    	$bool_perso_exist=false;
    	$bool_perso_loan_exist=false;
    	$bool_ref_exist=false;

    	$affiche=explode(",",$this->displaycolumns);
    	$filter=explode(",",$this->filtercolumns);
    	$sort=explode(",",$this->sortablecolumns);
    	$tmp=array_merge($affiche,array_diff($filter,$affiche));
    	$total=array_merge($tmp,array_diff($sort,$tmp));
    	for ($i=0;$i<count($total);$i++) {
    		if(substr($total[$i],0,2) == "#p") $cp_ref = 1;
    		else $cp_ref = 0;
    		if ((substr($total[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    			//champs personnalisés
    			require_once($class_path."/parametres_perso.class.php");
    			$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    			if (!$cp->no_special_fields) {
    				$id=substr($total[$i],2,strlen($total[$i])-1);
    				if(($cp_ref > 0) && !$bool_perso_loan_exist) {
    					$afrom = " left join ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]." on (".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."".".".$this->params["REFERENCE"][$cp_ref]["TABLEKEY"]." = ".$this->params["REFERENCE"][0]['value'].".".$this->params["REFERENCEKEY"][0]['value'].")";
    					$dependant_left_joins[] = $afrom;
    					$bool_perso_loan_exist = true;
    				}
    				$afrom = " left join ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_values ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_values".$id." on (".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_values".$id."".".".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_origine"." = ".$this->params["REFERENCE"][$cp_ref]['value'].".".$this->params["REFERENCEKEY"][$cp_ref]['value']." AND ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_values".$id.".".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_champ = ".$id.")";
    				$dependant_left_joins[] = $afrom;
    				$select.=$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_values".$id.".".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_".$cp->t_fields[$id]["DATATYPE"]." AS ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_".$cp->t_fields[$id]["DATATYPE"].$id.",";
    				if ($cp->t_fields[$id]['TYPE']=="list") {
    					$dependant_left_joins[] ="left join ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_lists ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_lists".$id." on (".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_values".$id.".".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_".$cp->t_fields[$id]["DATATYPE"]."=".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_lists".$id.".".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_list_value)";
    				}
    				$bool_perso_exist=true;
    			}
    		} elseif (array_key_exists($total[$i],$this->fixedfields)) {
    			//champs fixes
    			//est-ce que le champ est affichable
    			if ($this->fixedfields[$total[$i]]["DISPLAYABLE"]=="yes") {
    				if ($this->fixedfields[$total[$i]]["TABLE"][0]['value']) {
    	 				if ($this->fixedfields[$total[$i]]["LINK"]) {
    	 					for ($x=0;$x<count($this->fixedfields[$total[$i]]["LINK"]);$x++) {
    	 						//de quelle type est la relation
    	 						if ($this->fixedfields[$total[$i]]["LINK"][$x]["TYPE"]=="nn") {
    	 							$afrom="left join ".$this->fixedfields[$total[$i]]["LINK"][$x]["TABLE"][0]['value']." on (";
    	 							$afrom.=$this->params["REFERENCE"][0]['value'].".".$this->params["REFERENCEKEY"][0]['value']."=";
    	 							$afrom.=$this->fixedfields[$total[$i]]["LINK"][$x]["TABLE"][0]['value'].".".$this->fixedfields[$total[$i]]["LINK"][$x]["REFERENCEFIELD"][0]['value'].")";
    	 							$afrom.=" left join ".$this->fixedfields[$total[$i]]["TABLE"][0]['value']." on (".$this->fixedfields[$total[$i]]["TABLE"][0]['value'].".".$this->fixedfields[$total[$i]]["TABLEKEY"][0]['value']."=";
    	 							$afrom.=$this->fixedfields[$total[$i]]["LINK"][$x]["TABLE"][0]['value'].".".$this->fixedfields[$total[$i]]["LINK"][$x]["EXTERNALFIELD"][0]['value'].")";
    	 							$dependant_left_joins[] = $afrom;
    	 							//$bool_ref_exist=true;
    	 							$select.="GROUP_CONCAT(distinct ".$this->fixedfields[$total[$i]]["TABLEFIELD"][0]['value'].") as ".$this->fixedfields[$total[$i]]["TABLEFIELD"][0]["NAME"].",";
    	 						} elseif ($this->fixedfields[$total[$i]]["LINK"][$x]["TYPE"]=="0n") {
    	 							$afrom=" left join ".$this->fixedfields[$total[$i]]["TABLE"][0]['value']." on (".$this->fixedfields[$total[$i]]["TABLE"][0]['value'].".".$this->fixedfields[$total[$i]]["TABLEKEY"][0]['value']."=";
    	 							$afrom.=$this->fixedfields[$total[$i]]["LINK"][$x]["TABLE"][0]['value'].".".$this->fixedfields[$total[$i]]["LINK"][$x]["REFERENCEFIELD"][0]['value'].")";
    	 							$dependant_left_joins[] = $afrom;
    	 							$select.=$this->fixedfields[$total[$i]]["TABLE"][0]['value'].".";
    	 							$select.=$this->fixedfields[$total[$i]]["TABLEFIELD"][0]['value'];
    	 							if ($this->fixedfields[$total[$i]]["TABLEALIAS"][0]['value'])
    	 								$select.=" as ".$this->fixedfields[$total[$i]]["TABLEALIAS"][0]['value'].",";
    	 							else
    	 								$select.=",";
    	 						} else {
    	 							$select.=$this->fixedfields[$total[$i]]["TABLE"][0]['value'].".";
    	 							$select.=$this->fixedfields[$total[$i]]["TABLEFIELD"][0]['value'];
    	 							if ($this->fixedfields[$total[$i]]["TABLEALIAS"][0]['value'])
    	 								$select.=" as ".$this->fixedfields[$total[$i]]["TABLEALIAS"][0]['value'].",";
    	 							else
    	 								$select.=",";
    	 							
    	 							$froms[]=$this->fixedfields[$total[$i]]["TABLE"][0]['value'];
    	 						}
    	 					}	
    	 				} else $from.=$this->fixedfields[$total[$i]]["TABLE"][0]['value'].",";
    	 			} else {
    	 				$fields=explode(",",$this->fixedfields[$total[$i]]["TABLEFIELD"][0]['value']);
    	 				for ($g=0;$g<count($fields);$g++) {
    	 					$select.=$this->params["REFERENCE"][0]['value'].".".$fields[$g].",";
    	 				}
    	 			}
    	 		}		 
    	 	}   					
    	}
     	if ($bool_ref_exist==false) {
     	 	$froms[]=$this->params["REFERENCE"][0]['value'];
     	 	$bool_ref_exist=true;
   		}
    	if ($this->select_original) $select.=$this->select_original.",";
    	if ($this->from_original) $from.=$this->from_original.",";
    	$select=substr($select,0,strlen($select)-1);
		$from = implode(",",$froms);
 		$from.= " ".implode(" ", $dependant_left_joins);
    	$ret="select ".$select." from ".$from;
    	return $ret; 	
    }
    
    //fonction de manipulation de données pour le contenu de l'order by dans la requête
    public function sort_query() {
    	 global $class_path;
    	 global $msg, $charset;
    	 
    	 $ret="";
    	 $orderby="";
    	    	 
    	$s=explode(",",$this->sortablecolumns);	
    	//parcours des champs 
    	for ($j=0;$j<count($s);$j++) {
    		$sort_list="sort_list_".$j;
    		global ${$sort_list};
    		if (!${$sort_list}) ${$sort_list}=$s[$j];
    		if (${$sort_list}!="-1") {
    			for ($i=0;$i<count($s);$i++) {
    				if (${$sort_list}==$s[$i]) {
    					if(substr($s[$i],0,2) == "#p") $cp_ref = 1;
    					else $cp_ref = 0;
    					if ((substr($s[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    						//champs personnalisés
    						require_once($class_path."/parametres_perso.class.php");
    						$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    						if (!$cp->no_special_fields) {
    							$id=substr($s[$i],2,strlen($s[$i])-1);
    							if ($cp->t_fields[$id]['TYPE']=="list") {
    								$orderby.=$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_lists".$id.".".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_list_lib,";
    							} else {
    								$orderby.=$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_values".$id.".".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_".$cp->t_fields[$id]['DATATYPE'].",";
    							}
    						}
    	 				} elseif (array_key_exists($s[$i],$this->fixedfields)) {
    	 					//champs fixes
    						//est-ce que le champ est triable	
    	 					if ($this->fixedfields[$s[$i]]["SORTABLE"]=="yes") {
    	 						if ($this->fixedfields[$s[$i]]["TABLE"][0]['value']) {
    	 							for ($x=0;$x<count($this->fixedfields[$s[$i]]["LINK"]);$x++) {
    	 								//de quelle type est la relation
    	 								if ($this->fixedfields[$s[$i]]["LINK"][$x]["TYPE"]=="nn") {
    	 									$orderby.=$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'].",";
    	 								} elseif ($this->fixedfields[$s[$i]]["LINK"][$x]["TYPE"]=="0n") {
    	 									$orderby.=$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'].",";
    	 								} else {
    	 									$orderby.=$this->fixedfields[$s[$i]]["TABLE"][0]['value'].".";
    	 									$orderby.=$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'].",";
    	 								}
    	 							}		
    	 						} else {
    	 							$orderby.=$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'].",";
    	 						}
    	 					} 
    	 				} else {
    	 					$name_function=$this->specialfields[$s[$i]]["FUNCTION"];
							$r="";
							$v=array();
							$t=array();	
							$param=$this->specialfields[$s[$i]]["PARAM"][0]['value'];
							$execute_query=pmb_mysql_query("select ".$this->params["REFERENCEKEY"][0]['value']." from ".$this->params["REFERENCE"][0]['value']);
							while ($row=pmb_mysql_fetch_array($execute_query)) {
								$t[]=$row[$this->params["REFERENCEKEY"][0]['value']];
							}
							pmb_mysql_free_result($execute_query);
							$bool=true;
							eval("\$r=".$name_function."(\$t,\$param,\$v,\$bool);");
    	 				}    					
    				}
    	 		}
    		}
    	}	   	 	
    	 
    	if ($orderby) {
    		$orderby=substr($orderby,0,strlen($orderby)-1);
    		$ret=" order by ".$orderby;
    	}
    	return $ret; 	
    }
    
    //fonction de manipulation de données pour le contenu du where dans la requête
    public function filters_query() {
    	global $class_path;
    	global $msg, $charset;
    	$ret="";
    	$where="";
    	if ($this->where_original){
    		$where.=$this->where_original." and ";
    	}
    	
    	$affiche=explode(",",$this->displaycolumns);
    	$filter=explode(",",$this->filtercolumns);
    	$sort=explode(",",$this->sortablecolumns);
    	$tmp=array_merge($affiche,array_diff($filter,$affiche));
    	$total=array_merge($tmp,array_diff($sort,$tmp));
    	
    	for ($i=0;$i<count($total);$i++) {
    		if(substr($total[$i],0,2) == "#p") $cp_ref = 1;
    		else $cp_ref = 0;
    		if ((substr($total[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    			//champs personnalisés
    			require_once($class_path."/parametres_perso.class.php");
    			$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    			if (!$cp->no_special_fields) {
    				$id=substr($total[$i],2,strlen($total[$i])-1);
    				$valeurs_post="f".$cp->t_fields[$id]["NAME"];
    				$v=array();
    				global ${$valeurs_post};
    				if (${$valeurs_post}) $v=${$valeurs_post};
    				//Récupération du champ
    				$field=array();
					$field['ID']=$id;
					$field['NAME']=$cp->t_fields[$id]['NAME'];
					$field['MANDATORY']=$cp->t_fields[$id]['MANDATORY'];
					$field['ALIAS']=$cp->t_fields[$id]['TITRE'];
					$field['DATATYPE']=$cp->t_fields[$id]['DATATYPE'];
					$field['OPTIONS']=$cp->t_fields[$id]['OPTIONS'];
					$field['VALUES']=$v;
					$field['PREFIX']=$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"];
					if (isset($cp->t_fields[$id]['TYPE'])&&($cp->t_fields[$id]['TYPE']!="list")&&($cp->t_fields[$id]['TYPE']!="query_list")) {
						$field['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE']="-1";
						$field['OPTIONS'][0]['UNSELECT_ITEM'][0]['value']=$msg["empr_perso_all_values"];
						$field['OPTIONS'][0]['DEFAULT_VALUE'][0]['value']="-1";
					}
					$bool=false;
					$t[0]=$field['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'];
    				$w=array_diff($v,$t);
    				if (count($w)) {
    					$where.=$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_values".$id.".".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_".$field['DATATYPE']." in ('".implode("','",$v)."') and ";
					}
				}
    		} elseif (array_key_exists($total[$i],$this->fixedfields)) {
    			//champs fixes
    			//est-ce que le champ est filtrable	
    			if ($this->fixedfields[$total[$i]]["FILTERABLE"]=="yes") {
    				$nom_valeurs_post="f".$this->fixedfields[$total[$i]]["ID"];
    				$valeurs_post=array();
    				global ${$nom_valeurs_post};
    				$valeurs_post=${$nom_valeurs_post};
    				if (is_array($valeurs_post)) {
    					$t[0]=-1;
    					$v=array_diff($valeurs_post,$t);
    					if (count($v)) {
    						if ($this->fixedfields[$total[$i]]["TABLE"][0]['value']) $where.=$this->fixedfields[$total[$i]]["TABLE"][0]['value'].".".$this->fixedfields[$total[$i]]["TABLEKEY"][0]['value']." in (".implode(",",$v).") and ";
    							else $where.=$this->params["REFERENCE"][0]['value'].".".$this->fixedfields[$total[$i]]["TABLEFIELD"][0]['value']." in ('".implode("','",$v)."') and ";
    					}
    				} 	
    				if ($this->fixedfields[$total[$i]]["TABLE"][0]['value']) {	
    					for ($x=0;$x<count($this->fixedfields[$total[$i]]["LINK"]);$x++) {
    						//de quelle type est la relation
    						if ($this->fixedfields[$total[$i]]["LINK"][$x]["TYPE"]=="1n") {
    							$where.=$this->params["REFERENCE"][0]['value'].".".$this->fixedfields[$total[$i]]["LINK"][$x]["REFERENCEFIELD"][0]['value']."=";
    							$where.=$this->fixedfields[$total[$i]]["TABLE"][0]['value'].".".$this->fixedfields[$total[$i]]["TABLEKEY"][0]['value']." and ";
    						}
    					}
    				}
    			} 
    		}    					
    	}  	 	
    	
    	if ($where) $where=substr($where,0,strlen($where)-4);
    	if ($where) $ret=" where ".$where;

    	$this->filtered_query = $ret;
    	return $ret;	
    }
    
    //fonction de manipulation de données pour la limitation des résultats
    public function pager_query() {
    	$ret="";
    	if (($this->page)&&($this->nb_per_page)) {
    		$limit=($this->page-1)*$this->nb_per_page.",".$this->nb_per_page;		
    		$ret=" limit ".$limit;
    	}
    	return $ret;	
    }
    
    //fonction de création du lien de la page
    public function display_pager() {
    	global $class_path;
    	global $msg;
    	
    	$ret="";
    	//On calcul les page en fonction du résultat de la requte total et pas de la recherche de départ
    	$requete=preg_replace("/limit [0-9]*,[0-9]*/i","",$this->query);
    	$res=pmb_mysql_query($requete);
    	$nb_lignes=pmb_mysql_num_rows($res);
    	if ($nb_lignes!=0) {
    		$ret = "<div class='center'>";
    		$nbepages = ceil($nb_lignes/$this->nb_per_page);
			$suivante = $this->page+1;
			$precedente = $this->page-1;
			if ($nbepages!=1 && $this->page != 1) {
				$ret .= "<a id='premiere' href='#' onClick='document.forms[\"form_filters\"].page.value=1;document.forms[\"form_filters\"].submit();'><img src='".get_url_icon('first.gif')."' style='border:0px; margin:6px 6px' alt='".$msg['first_page']."' class='align_middle' title='".$msg['first_page']."' /></a>";
				$ret .= "<a id='precedente' href='#' onClick='document.forms[\"form_filters\"].page.value=".$precedente.";document.forms[\"form_filters\"].submit();'><img src='".get_url_icon('left.gif')."' style='border:0px; margin:6px 6px' alt='".$msg[48]."' class='align_middle' title='".$msg[48]."' /></a>";
			}
			$deb = $this->page-10;
			if ($deb<1) $deb=1;
			$fin = $this->page+10;
			if($fin>$nbepages)$fin=$nbepages; 
			for ($i = $deb; ($i <= $nbepages) && ($i<=$fin) ; $i++) {
				if($i==$this->page) {
					$ret .= "<strong>".$i."</strong>";
				} else {
					$ret .= "<a href='#' onClick='document.forms[\"form_filters\"].page.value=".$i.";document.forms[\"form_filters\"].submit();'>".$i."</a>";
				}
				if ($i<$nbepages) $ret.=" ";
			}
			if ($suivante<=$nbepages) {
				$ret .= "<a href='#' onClick='document.forms[\"form_filters\"].page.value=".$suivante.";document.forms[\"form_filters\"].submit();'><img src='".get_url_icon('right.gif')."' style='border:0px; margin:6px 6px' alt='".$msg[49]."' class='align_middle' title='".$msg[49]."' /></a>";
			}

			if(($this->page)<$nbepages)  {
				$ret .= "<a id='derniere' href='#' onClick='document.forms[\"form_filters\"].page.value=".$nbepages.";document.forms[\"form_filters\"].submit();'><img src='".get_url_icon('last.gif')."' style='border:0px; margin:6px 6px' alt='".$msg['last_page']."' class='align_middle' title='".$msg['last_page']."' /></a>";
			}
			$ret.="</div>";
    	}
    	return $ret;	
    }
    
    //fonction de récupération du nombre de lignes de la requête
    public function nb_lines_query() {
    	if (!$this->no_filter) {
    		if ($this->t_query) {
    			$compt=pmb_mysql_num_rows($this->t_query);
    			return $compt;	
    		}
    	} else {
    		if ($this->original_query) {
    			$resultat=pmb_mysql_query($this->original_query);
    			$compt=pmb_mysql_num_rows($resultat);
    			return $compt;	
    		}
    	}
    }
    
    //fonction de récupération d'une ligne de la requête
    public function extract_line_query($n_line) {
    	if (!$this->no_filter) {
    		if ($this->t_query) {
    			$i=1;
    			while ($row=pmb_mysql_fetch_object($this->t_query)) {
    				if ($i==$n_line) {
    					return $row;
    					exit();	
    				}
    				$i++;	
    			}	
    		}
    	} else {
    		if ($this->original_query) {
    			$i=1;
    			$resultat=pmb_mysql_query($this->original_query);
    			while ($row=pmb_mysql_fetch_object($resultat)) {
    				if ($i==$n_line) {
    					return $row;
    					exit();	
    				}
    				$i++;	
    			}	
    		}
    	}	
    } 
    
    //fonction d'affichage des entêtes de colonnes de la requête
    public function display_columns() {
    	global $class_path;
    	global $msg;
    	global $charset;
    	$aff="";
    	
    	$s=explode(",",$this->displaycolumns);
    	//parcours des champs 
    	for ($i=0;$i<count($s);$i++) {
    		if(substr($s[$i],0,2) == "#p") $cp_ref = 1;
    		else $cp_ref = 0;
    		//détermination d'un champ personnalisé
    		if ((substr($s[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    			//champs personnalisés
    			require_once($class_path."/parametres_perso.class.php");
    			$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    			if (!$cp->no_special_fields) {
    				$id=substr($s[$i],2);
    				$aff.="<th>".htmlentities($cp->t_fields[$id]['TITRE'],ENT_QUOTES,$charset)."</th>";
    			}
    		} elseif (array_key_exists($s[$i],$this->fixedfields)) {
    			//champs fixes
    			//est-ce que le champ est affichable
    			if ($this->fixedfields[$s[$i]]["DISPLAYABLE"]=="yes") {
    				if (substr($this->fixedfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->fixedfields[$s[$i]]["NAME"],4,strlen($this->fixedfields[$s[$i]]["NAME"])-4)];
						else $nom=$this->fixedfields[$s[$i]]["NAME"];
					$aff.="<th>".htmlentities($nom,ENT_QUOTES,$charset)."</th>";
    			}
    		} else {
    			if (substr($this->specialfields[$s[$i]]["NAME"],0,4)=="msg:") {
    				$nom=$msg[substr($this->specialfields[$s[$i]]["NAME"],4,strlen($this->specialfields[$s[$i]]["NAME"])-4)];	
    			} else {
    				$nom=$this->specialfields[$s[$i]]["NAME"];
    			}
    			$aff.="<th>".htmlentities($nom,ENT_QUOTES,$charset)."</th>";
    		}
    	}
    	return $aff;    				
    }
    
    //fonction d'affichage du résultat
    public function display_result() {
    	global $class_path, $charset, $msg;
    	$aff="";
    	if ($this->query) {
    		if(!isset($this->css["table"]["class"])) $this->css["table"]["class"] = '';
    		if(!isset($this->css["table"]["style"])) $this->css["table"]["style"] = '';
    		if(!isset($this->scripts["row"]["onmouseout"])) $this->scripts["row"]["onmouseout"] = '';
    		if(!isset($this->scripts["row"]["onmouseover"])) $this->scripts["row"]["onmouseover"] = '';
    		if(!isset($this->scripts["row"]["onmousedown"])) $this->scripts["row"]["onmousedown"] = '';
    		if(!isset($this->css["row_even"]["class"])) $this->css["row_even"]["class"] = '';
    		if(!isset($this->css["row_odd"]["class"])) $this->css["row_odd"]["class"] = '';
    		$execute_query=pmb_mysql_query($this->query);
    		$aff.="<table class='".$this->css["table"]["class"]."' style='".$this->css["table"]["style"]."'>";
    		$parity = 0;
			$header ="";
			$s=explode(",",$this->displaycolumns);
			for($n=0; $n < count($s); $n++) {
    			if(substr($s[$n],0,2) == "#p") $cp_ref = 1;
    			else $cp_ref = 0;
				if ((substr($s[$n],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    				//champs perso
    				require_once($class_path."/parametres_perso.class.php");
    				$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    				if (!$cp->no_special_fields) {
    					$id=substr($s[$n],2);
    					$header.="<th>".htmlentities($cp->t_fields[$id]['TITRE'],ENT_QUOTES,$charset)."</th>";
    				}
				} elseif (array_key_exists($s[$n],$this->fixedfields)) {
    				//champs fixes
    				$header.="<th>".$msg[str_replace("msg:","",$this->fixedfields[$s[$n]]["NAME"])]."</th>";
				} elseif (array_key_exists($s[$n],$this->specialfields)) {
					//champs spéciaux
					$header.="<th>".$msg[str_replace("msg:","",$this->specialfields[$s[$n]]["NAME"])]."</th>";
				} else {
					$header.="<th>&nbsp;</th>";
				}
			}
			$aff .= $header;
    		while (($result=pmb_mysql_fetch_array($execute_query))) {
    			$onmouseout=$this->scripts["row"]["onmouseout"];
    			$onmouseover=$this->scripts["row"]["onmouseover"];
    			$onmousedown=$this->scripts["row"]["onmousedown"];
    			if ($parity % 2) {
    				$pair_impair = $this->css["row_even"]["class"];
    				$pair_impair_style = $this->css["row_even"]["style"];
    				$onmouseout=str_replace('!!parity!!',$this->css["row_even"]["class"],$onmouseout);
    				$onmouseover=str_replace('!!parity!!',$this->css["row_even"]["class"],$onmouseover);
    				$onmouseout=str_replace('!!parity!!',$this->css["row_even"]["class"],$onmouseout);
    			} else { 
    				$pair_impair = $this->css["row_odd"]["class"];
    				$pair_impair_style = $this->css["row_odd"]["style"];
    				$onmouseout=str_replace('!!parity!!',$this->css["row_odd"]["class"],$onmouseout);
    				$onmouseover=str_replace('!!parity!!',$this->css["row_odd"]["class"],$onmouseover);
    				$onmousedown=str_replace('!!parity!!',$this->css["row_odd"]["class"],$onmousedown);
    			}
    			$ligne="";
    			
    			$s=explode(",",$this->displaycolumns);
    	    	//parcours des champs 
    			for ($i=0;$i<count($s);$i++) {
    				if(substr($s[$i],0,2) == "#p") $cp_ref = 1;
    				else $cp_ref = 0;
    				//détermination de la valeur
    				if ((substr($s[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    					//champs perso
    					require_once($class_path."/parametres_perso.class.php");
    					$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
   						$id=substr($s[$i],2);
    					$cp->get_values($result[$this->params["REFERENCEKEY"][$cp_ref]["value"]]);
    					if (!$cp->no_special_fields) {
//    						$temp=$result[$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_".$cp->t_fields[$id]["DATATYPE"].$id];
    						$onmouseout=str_replace("!!".$s[$i]."!!",rawurlencode($temp),$onmouseout);
    						$onmouseover=str_replace("!!".$s[$i]."!!",rawurlencode($temp),$onmouseover);
    						$onmousedown=str_replace("!!".$s[$i]."!!",rawurlencode($temp),$onmousedown);
    						$temp=$cp->get_formatted_output((isset($cp->values[$id]) ? $cp->values[$id] : array()), $id);
    						if (!$temp) $temp="&nbsp;";
    						if(!isset($this->css["cols"][$i]["class"])) $this->css["cols"][$i]["class"] = '';
    						if(!isset($this->css["cols"][$i]["style"])) $this->css["cols"][$i]["style"] = '';
    						$ligne.="<td class='".$this->css["cols"][$i]["class"]."' style='".$this->css["cols"][$i]["style"]."'>".$temp."</td>";
    					}
    				} elseif (array_key_exists($s[$i],$this->fixedfields)) {
    					//champs fixes
    					$f=array();
    					if ($this->fixedfields[$s[$i]]["LINK"]) {
    						for ($x=0;$x<count($this->fixedfields[$s[$i]]["LINK"]);$x++) {
    							if ($this->fixedfields[$s[$i]]["LINK"][$x]["TYPE"]=="nn") {
    								if ($this->fixedfields[$s[$i]]["TABLEALIAS"][0]["NAME"])
    									$f[0]=$this->fixedfields[$s[$i]]["TABLEALIAS"][0]["NAME"];
    								else
										$f[0]=$this->fixedfields[$s[$i]]["TABLEFIELD"][0]["NAME"];    									
    							} else {
    								if ($this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value'])
    									$f[0]=$this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value'];
    								else
    									$f[0]=$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'];
    							}
    						}
    					} else {
    						if ($this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value'])
    							$f=explode(",",$this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value']);
    						else
								$f=explode(",",$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value']);    							
    					}
    					$b=0;
    					if(!isset($this->css["cols"][$i]["class"])) $this->css["cols"][$i]["class"] = '';
    					if(!isset($this->css["cols"][$i]["style"])) $this->css["cols"][$i]["style"] = '';
    					$ligne.="<td class='".$this->css["cols"][$i]["class"]."' style='".$this->css["cols"][$i]["style"]."'>";
    					while ($b<count($f)) {
    						$temp=$result[$f[$b]];
    						if ($temp == '') $temp="&nbsp;";
    						$ligne .= $temp."&nbsp;";	
    						$b++; 
    					}
    					$ligne .= "</td>";
    					$onmouseout=str_replace("!!".$s[$i]."!!",rawurlencode($temp),$onmouseout);
    					$onmouseover=str_replace("!!".$s[$i]."!!",rawurlencode($temp),$onmouseover);
    					$onmousedown=str_replace("!!".$s[$i]."!!",rawurlencode($temp),$onmousedown);
    				} else {
    					$name_function=$this->specialfields[$s[$i]]["FUNCTION"];
						$r="";
						$key=$result[$this->params["REFERENCEKEY"][0]['value']];
						eval("\$r=".$name_function."(\$key);");
						$ligne.=$r;
    				}
    			}

    			if (($this->filter_name == "empr") && (!array_search("b", $s))) {
					require_once($class_path."/emprunteur.class.php");
					$code_b = emprunteur::get_cb_empr($result[$this->params["REFERENCEKEY"][0]['value']]);
					if ($code_b) {
    					$onmouseout=str_replace("!!b!!",$code_b,$onmouseout);
    					$onmouseover=str_replace("!!b!!",$code_b,$onmouseover);
    					$onmousedown=str_replace("!!b!!",$code_b,$onmousedown);
					}
    			}
    			$onmouseout=str_replace("!!".$this->params["REFERENCEKEY"][0]['value']."!!",$result[$this->params["REFERENCEKEY"][0]['value']],$onmouseout);
    			$onmouseover=str_replace("!!".$this->params["REFERENCEKEY"][0]['value']."!!",$result[$this->params["REFERENCEKEY"][0]['value']],$onmouseover);
    			$onmousedown=str_replace("!!".$this->params["REFERENCEKEY"][0]['value']."!!",$result[$this->params["REFERENCEKEY"][0]['value']],$onmousedown);
    			$aff.="<tr class='".$pair_impair."' style='$pair_impair_style' onmouseover=\"this.className='surbrillance';".$onmouseover."\" onmouseout=\"".$onmouseout."\" onmousedown='".$onmousedown."'>";					
				$aff.=$ligne;
				$aff.="</tr>";
				$parity += 1;
    		}
    		$aff.="</table>";		
    	}
    	return $aff;	
    }
                
    //fonction d'affichage des filtres applicables sur la liste des emprunteurs
    public function display_filters() {
    	global $msg;
    	global $charset;
    	global $class_path;
    	global $aff_filter_list_empr;
    	
    	$aff="";
    	$s=explode(",",$this->filtercolumns);
    		
    	//parcours des champs 
    	for ($i=0;$i<count($s);$i++) {
    		if(substr($s[$i],0,2) == "#p") $cp_ref = 1;
    		else $cp_ref = 0;
    		//détermination d'un champ personnalisé
    		if ((substr($s[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    			//champs personnalisés
    			require_once($class_path."/parametres_perso.class.php");
    			$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    			if (!$cp->no_special_fields) {
    				$id=substr($s[$i],2,strlen($s[$i])-1);
    				$valeurs_post="f".$cp->t_fields[$id]["NAME"];
    				$v=array();
    				global ${$valeurs_post};
    				if (${$valeurs_post}) $v=${$valeurs_post};
    				$aff.="<div class='left'><div style='vertical-align: middle'>".htmlentities($cp->t_fields[$id]['TITRE'],ENT_QUOTES,$charset)."</div>&nbsp;&nbsp;";
					//Récupération du champ
    				$field=array();
					$field['ID']=$id;
					$field['NAME']=$cp->t_fields[$id]['NAME'];
					$field['MANDATORY']=$cp->t_fields[$id]['MANDATORY'];
					$field['ALIAS']=$cp->t_fields[$id]['TITRE'];
					$field['DATATYPE']=$cp->t_fields[$id]['DATATYPE'];
					$field['OPTIONS']=$cp->t_fields[$id]['OPTIONS'];
					if (($cp->t_fields[$id]['TYPE']!="list")&&($cp->t_fields[$id]['TYPE']!="query_list")) {
						$field['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE']="-1";
						$field['OPTIONS'][0]['UNSELECT_ITEM'][0]['value']=$msg["empr_perso_all_values"];
						$field['OPTIONS'][0]['QUERY'][0]['value']="select ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_".$cp->t_fields[$id]['DATATYPE']." from ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_values where ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_champ=".$id." group by ".$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]."_custom_".$cp->t_fields[$id]['DATATYPE'];
						if ((!$v)||($v[0]=="")||($v[0]=="-1")) {
							$field['OPTIONS'][0]['DEFAULT_VALUE'][0]['value']="-1";
						}
					} 
					$field['VALUES']=$v;
					$field['PREFIX']=$this->params["REFERENCE"][$cp_ref]["PREFIXNAME"];
					$name="f".$cp->t_fields[$id]['NAME'];
					$multiple=$this->multiple;
					$r="";
					eval("\$r=".$aff_filter_list_empr[$cp->t_fields[$id]['TYPE']]."(\$field,\$name,\$multiple);");
					$aff.=$r."</div>";
    			}	
    		} elseif (array_key_exists($s[$i],$this->fixedfields)) {
    			//champs fixes
    			//est-ce que le champ est filtrable
    			if ($this->fixedfields[$s[$i]]["FILTERABLE"]=="yes") {
    				if (substr($this->fixedfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->fixedfields[$s[$i]]["NAME"],4,strlen($this->fixedfields[$s[$i]]["NAME"])-4)];
    					else $nom=$this->fixedfields[$s[$i]]["NAME"];
    				
    				$aff.="<div class='left'><div style='vertical-align: middle'>".htmlentities($nom,ENT_QUOTES,$charset)."</div>&nbsp;&nbsp;";
    				$requete="select ";
    				//détermination d'une table étrangère
    				if ($this->fixedfields[$s[$i]]["TABLE"][0]['value']) {
    					$requete.=$this->fixedfields[$s[$i]]["TABLEKEY"][0]['value'].",";
    					$from=$this->fixedfields[$s[$i]]["TABLE"][0]['value'];
    					$where="";
    				} else {
    					$from=$this->params["REFERENCE"][0]['value'];
    					if ($this->fixedfields[$s[$i]]["TABLE"][0]['value']) {
    						$where_field = $this->fixedfields[$s[$i]]["TABLE"][0]['value'].".".$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'];
    					} else {
    						$where_field = $this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'];
    					}
    					$where = " where ".$where_field." is not null and ".$where_field."<>''";
    					
    					if ($this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value']) {
    						$where.=" group by ".$this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value'];
    					} else {
    						$where.=" group by ".$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'];
    					}
    				}	
    				if ($this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value']) {
    					$alias = " as ".$this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value'];
    					$order = $this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value'];
    				} else {
    					$alias = "";
    					$order = $this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'];
    				}		
    				$requete.=$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value'].$alias;
    				$requete.=" from ".$from."".$where." order by ".$order;
    				$execute_query=pmb_mysql_query($requete);
    				if ($execute_query) {
    					$valeurs_post="f".$this->fixedfields[$s[$i]]["ID"];
    					global ${$valeurs_post};
    					if ($this->multiple) $multiple="size=5 multiple";
    					$aff.="<select name='f".$this->fixedfields[$s[$i]]["ID"]."[]' $multiple>\n";
    					$aff.="<option value='-1'";
    					if (!${$valeurs_post}) ${$valeurs_post}=-1;
    					if (is_array(${$valeurs_post})) {
    						$as=array_search("-1",${$valeurs_post});
							if (($as!==FALSE)&&($as!==NULL)) $aff.=" selected";
    					} else {
    						if (${$valeurs_post}==-1) $aff.=" selected";	
    					}
    					if (substr($this->fixedfields[$s[$i]]["DEFAULTVALUE"],0,4)=="msg:") $nom=$msg[substr($this->fixedfields[$s[$i]]["DEFAULTVALUE"],4,strlen($this->fixedfields[$s[$i]]["DEFAULTVALUE"])-4)];
    						else $nom=$this->fixedfields[$s[$i]]["DEFAULTVALUE"];
    					$aff.=">".htmlentities($nom,ENT_QUOTES,$charset)."</option>\n"; 	
    					
    					while($resultat=pmb_mysql_fetch_array($execute_query)) {
    						if ($this->fixedfields[$s[$i]]["TABLE"][0]['value']) {
    							$valeur_temp=$resultat[$this->fixedfields[$s[$i]]["TABLEKEY"][0]['value']];
    						} else {
    							if ($this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value'])
    								$valeur_temp=htmlentities($resultat[$this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value']],ENT_QUOTES,$charset);
    							else
    								$valeur_temp=htmlentities($resultat[$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value']],ENT_QUOTES,$charset);
    						}
    						$aff.="<option value='".$valeur_temp."'";
    						if (is_array(${$valeurs_post})) {
    							$as=array_search($valeur_temp,${$valeurs_post});
    							if (($as!==FALSE)&&($as!==NULL)) $aff.=" selected";	
    						} else {
    							if ($valeur_temp==${$valeurs_post}) $aff.=" selected";	
    						}
    						$aff.=">";
    						if ($this->fixedfields[$s[$i]]["TYPE"]=="date") {
    							if ($this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value'])
    								$valeur=formatdate($resultat[$this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value']]);
    							else 
    								$valeur=formatdate($resultat[$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value']]);
    						} else {
    							if ($this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value'])
    								$valeur=$resultat[$this->fixedfields[$s[$i]]["TABLEALIAS"][0]['value']];
    							else 
    								$valeur=$resultat[$this->fixedfields[$s[$i]]["TABLEFIELD"][0]['value']];
    						}
    						$aff.=htmlentities($valeur,ENT_QUOTES,$charset)."</option>\n";	
    					}
    					$aff.="</select></div>\n";
    				}
    			}
    		} else {
    			$name_function=$this->specialfields[$s[$i]]["FUNCTION"];
				$r="";
				$t=array();
				if (substr($this->specialfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->specialfields[$s[$i]]["NAME"],4,strlen($this->specialfields[$s[$i]]["NAME"])-4)];
					else $nom=$this->specialfields[$s[$i]]["NAME"];
				$aff.="<div class='left'><div style='vertical-align: middle'>".htmlentities($nom,ENT_QUOTES,$charset)."</div>&nbsp;&nbsp;";
				if ($this->multiple) $multiple="size=5 multiple";
				$aff.="<select name='f".$this->specialfields[$s[$i]]["ID"]."[]' $multiple>\n";
    			$aff.="<option value='-1'";
    			$valeurs_post="f".$this->specialfields[$s[$i]]["ID"];
    			global ${$valeurs_post};
    			$v=array();
    			$v=${$valeurs_post};
    			if ((!${$valeurs_post})&&(!is_array(${$valeurs_post}))) $aff.=" selected";
    			else {	
    				if (is_array(${$valeurs_post})) {
    					$as=array_search("-1",${$valeurs_post});
						if (($as!==FALSE)&&($as!==NULL)) $aff.=" selected";
    				} else $aff.=" selected";
				}
				if (substr($this->specialfields[$s[$i]]["DEFAULTVALUE"],0,4)=="msg:") $nom=$msg[substr($this->specialfields[$s[$i]]["DEFAULTVALUE"],4,strlen($this->specialfields[$s[$i]]["DEFAULTVALUE"])-4)];
					else $nom=$this->specialfields[$s[$i]]["DEFAULTVALUE"];
    			$aff.=">".htmlentities($nom,ENT_QUOTES,$charset)."</option>\n"; 	
				$param=$this->specialfields[$s[$i]]["PARAM"][0]['value'];
				$execute_query=pmb_mysql_query("select ".$this->params["REFERENCEKEY"][0]['value']." from ".$this->params["REFERENCE"][0]['value']);
				while ($row=pmb_mysql_fetch_array($execute_query)) {
					$t[]=$row[$this->params["REFERENCEKEY"][0]['value']];
				}
				pmb_mysql_free_result($execute_query);
				eval("\$r=".$name_function."(\$t,\$param,\$v);");
				$aff.=$r;
				$aff.="</select></div>\n";
    		}	
    	}
    	return $aff;
    }
    
    //fonction d'affichage des tris applicables sur la liste des emprunteurs
    public function display_sort() {
    	global $msg;
    	global $charset;
    	global $class_path;
    	    	    	
    	$aff=$msg["sort_label"]."&nbsp;&nbsp;";
    	$s=explode(",",$this->sortablecolumns);
    	//parcours des champs 
    	for ($j=0;$j<=count($s)-2;$j++) {
    		$liste="&nbsp;&nbsp;<select name='sort_list_".$j."'><option value='-1'";
    		$sort_list="sort_list_".$j;
    		global ${$sort_list};
    		if (!${$sort_list}) ${$sort_list}=$s[$j];
    		if (${$sort_list}==-1) $liste.=" selected";
    		$liste.=">".$msg["tri_inactif"]."</option>";
    		for ($i=0;$i<count($s);$i++) {
    			if(substr($s[$i],0,2) == "#p") $cp_ref = 1;
    			else $cp_ref = 0;
    			//détermination d'un champ personnalisé
    			if ((substr($s[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    				//champs personnalisés
    				require_once($class_path."/parametres_perso.class.php");
    				$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    				if (!$cp->no_special_fields) {
    					$id=substr($s[$i],2,strlen($s[$i])-1);
    					$liste.="<option value='".$s[$i]."'";
						if (${$sort_list}) {
							if (${$sort_list}==$s[$i]) $liste.=" selected";	
						} else { 
							$liste.=" selected";
						}
						$liste.=">".htmlentities($cp->t_fields[$id]['TITRE'],ENT_QUOTES,$charset)."</option>\n";
    				}	
    			} elseif (array_key_exists($s[$i],$this->fixedfields)) {
    				//champs fixes
    				//est-ce que le champ est triable
    				if ($this->fixedfields[$s[$i]]["SORTABLE"]=="yes") {
    					$liste.="<option value='".$s[$i]."'";
    					if (${$sort_list}) {
							if (${$sort_list}==$s[$i]) $liste.=" selected";	
						} else { 
							$liste.=" selected";
						}
						if (substr($this->fixedfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->fixedfields[$s[$i]]["NAME"],4,strlen($this->fixedfields[$s[$i]]["NAME"])-4)];
    						else $nom=$this->fixedfields[$s[$i]]["NAME"];
    					$liste.=">".htmlentities($nom,ENT_QUOTES,$charset)."</option>\n";		
    				}
    			} else {
    				$liste.="<option value='".$s[$i]."'";
    				if (${$sort_list}) {
						if (${$sort_list}==$s[$i]) $liste.=" selected";	
					} else { 
						$liste.=" selected";
					}
					if (substr($this->fixedfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->fixedfields[$s[$i]]["NAME"],4,strlen($this->fixedfields[$s[$i]]["NAME"])-4)];
    						else $nom=$this->fixedfields[$s[$i]]["NAME"];
    				$liste.=">".htmlentities($nom,ENT_QUOTES,$charset)."</option>\n";			
    			}
    		}
    		$liste.="</select>&nbsp;&nbsp;".$msg["filters_sort_next"]."&nbsp;&nbsp;";	
    		$aff.=$liste;
    	}
    	$liste="<select name='sort_list_".$j."'><option value='-1'";
    	$sort_list="sort_list_".$j;
    	global ${$sort_list};
    	if (!${$sort_list}) ${$sort_list}=$s[$j];
    	if (${$sort_list}==-1) $liste.=" selected";
    	$liste.=">".$msg["tri_inactif"]."</option>";
    	for ($i=0;$i<=count($s)-1;$i++) {
    		if(substr($s[$i],0,2) == "#p") $cp_ref = 1;
    		else $cp_ref = 0;
    		//détermination d'un champ personnalisé
    		if ((substr($s[$i],0,1)=="#")&&($this->params["REFERENCE"][$cp_ref]["DYNAMICFIELDS"]=="yes")) {
    			//champs personnalisés
    			require_once($class_path."/parametres_perso.class.php");
    			$cp=new parametres_perso($this->params["REFERENCE"][$cp_ref]["PREFIXNAME"]);
    			if (!$cp->no_special_fields) {
    				$id=substr($s[$i],2,strlen($s[$i])-1);
    				$liste.="<option value='".$s[$i]."'";
					if (${$sort_list}) {
						if (${$sort_list}==$s[$i]) $liste.=" selected";	
					} else $liste.=" selected";	
					$liste.=">".htmlentities($cp->t_fields[$id]['TITRE'],ENT_QUOTES,$charset)."</option>\n";
    			}	
    		} elseif (array_key_exists($s[$i],$this->fixedfields)) {
    			//champs fixes
    			//est-ce que le champ est triable
    			if ($this->fixedfields[$s[$i]]["SORTABLE"]=="yes") {
    				$liste.="<option value='".$s[$i]."'";
					if (${$sort_list}) {
						if (${$sort_list}==$s[$i]) $liste.=" selected";	
					} else $liste.=" selected";
					if (substr($this->fixedfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->fixedfields[$s[$i]]["NAME"],4,strlen($this->fixedfields[$s[$i]]["NAME"])-4)];
    					else $nom=$this->fixedfields[$s[$i]]["NAME"];	
    				$liste.=">".htmlentities($nom,ENT_QUOTES,$charset)."</option>\n";		
    			}
    		} else {
    			$liste.="<option value='".$s[$i]."'";
    			if (${$sort_list}) {
					if (${$sort_list}==$s[$i]) $liste.=" selected";	
				} else { 
					$liste.=" selected";
				}
				if (substr($this->specialfields[$s[$i]]["NAME"],0,4)=="msg:") $nom=$msg[substr($this->specialfields[$s[$i]]["NAME"],4,strlen($this->fixedfields[$s[$i]]["NAME"])-4)];
    				else $nom=$this->specialfields[$s[$i]]["NAME"];
    			$liste.=">".htmlentities($nom,ENT_QUOTES,$charset)."</option>\n";			
    		}
    	}
    	$liste.="</select>\n";	
    	$aff.=$liste;
    	
    	return $aff;
    }
    
    //fonction de génération du xml par rapport à une requête
    //Cette fonction n'est jamais utilisée Matthieu 17/04/20013
    //Si ça devait être le cas voir la gestion de l'encodage
    public function gen_xml($requete,$champ_pivot) {
    	global $msg;
    	$execute_query=pmb_mysql_query($requete);
    	
    	$ret="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
    	$ret.="<fields>";
    	
    	$table = pmb_mysql_field_table($execute_query, $champ_pivot);
    	
    	$header="<reference dynamicfields=\"\" prefixname=\"\">".$table."</reference>";
    	$header.="<referencekey>".$champ_pivot."</referencekey>";
    	
    	$i=0;
    	while ($i < pmb_mysql_num_fields($execute_query)) {
    		$meta = pmb_mysql_fetch_field($execute_query);
    		if ($meta) {
    			$fields.="<field name=\"".$meta->name."\" type=\"".$meta->type."\" value=\"\" id=\"".$i."\" filterable=\"yes\" sortable=\"yes\" displayable=\"yes\" defaultvalue=\"\">";	
    			$fields.="<tablefield>".$meta->name."</tablefield>";
    		} else {
    			$fields.="<field>";
    			$this->error=true;
    			$this->error_message=str_replace('%s',$i,$msg["erreur_detail_champ"]);
    			$ret.=str_replace('%s',$i,$msg["erreur_detail_champ"]);
    		}
    		$fields.="</field>";	
    	}
    	$fields.="<specialfields></specialfields>";
    	$ret.=$header.$fields."</fields>";
    	$ret=htmlspecialchars($ret);
    	return $ret; 	
    }
    
    public function parse() {
    	global $include_path;
    	    	
    	if (!$this->filter_name) {
    		$xml=$this->filter_source;
    		$params=_parser_text_no_function_($xml,"FIELDS");
    		$this->params=$params;
    	} else {
    		$file_size="";
    		if (file_exists($include_path."/filters_list/".$this->filter_name."/".$this->filter_source."_subst.xml")) {
    			$fp=fopen($include_path."/filters_list/".$this->filter_name."/".$this->filter_source."_subst.xml","r");	
    			$file_size=filesize($include_path."/filters_list/".$this->filter_name."/".$this->filter_source."_subst.xml");
    		} else {
    			$fp=fopen($include_path."/filters_list/".$this->filter_name."/".$this->filter_source.".xml","r");
    			$file_size=filesize($include_path."/filters_list/".$this->filter_name."/".$this->filter_source.".xml");
    		}
    		if ($fp) {
    			$xml=fread($fp,$file_size);
    			fclose($fp);
    			$params=_parser_text_no_function_($xml,"FIELDS");
    			$this->params=$params;
    		} else {
    			$this->error=true;
    			$this->error_message="Can't open definition file";
    		}
    	}
    	//lecture des champs fixes
    	for ($i=0;$i<count($params["FIXEDFIELDS"][0]["FIELD"]);$i++) {
    		$this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]=$params["FIXEDFIELDS"][0]["FIELD"][$i];
    		if(!isset($this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["TABLEALIAS"][0]['value'])) {
    			$this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["TABLEALIAS"][0]['value'] = '';
    		}
    		if(!isset($this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["TABLEALIAS"][0]['NAME'])) {
    			$this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["TABLEALIAS"][0]['NAME'] = '';
    		}
    		if(!isset($this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["TABLE"][0]['value'])) {
    			$this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["TABLE"][0]['value'] = '';
    		}
    		if(!isset($this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["PARAM"][0]['value'])) {
    			$this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["PARAM"][0]['value'] = '';
    		}
    		if(!isset($this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["LINK"])) {
    			$this->fixedfields[$params["FIXEDFIELDS"][0]["FIELD"][$i]["VALUE"]]["LINK"] = '';
    		}
    	}
    	//lecture des champs spéciaux
    	for ($i=0;$i<count($params["SPECIALFIELDS"][0]["FIELD"]);$i++) {
    		$this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]=$params["SPECIALFIELDS"][0]["FIELD"][$i];
    		if(!isset($this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["TABLEALIAS"][0]['value'])) {
    			$this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["TABLEALIAS"][0]['value'] = '';
    		}
    		if(!isset($this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["TABLEALIAS"][0]['NAME'])) {
    			$this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["TABLEALIAS"][0]['NAME'] = '';
    		}
    		if(!isset($this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["TABLE"][0]['value'])) {
    			$this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["TABLE"][0]['value'] = '';
    		}
    		if(!isset($this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["PARAM"][0]['value'])) {
    			$this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["PARAM"][0]['value'] = '';
    		}
    		if(!isset($this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["LINK"])) {
    			$this->specialfields[$params["SPECIALFIELDS"][0]["FIELD"][$i]["ID"]]["LINK"] = '';
    		}
    	}
    }
}
?>