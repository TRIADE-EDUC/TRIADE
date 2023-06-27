<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: affiliate_search.class.php,v 1.29 2019-02-21 11:03:40 ngantier Exp $

require_once($class_path."/connecteurs.class.php");
require_once($class_path."/marc_table.class.php");
require_once($include_path."/parser.inc.php");
require_once($class_path."/search.class.php");
require_once($class_path."/search_authorities.class.php");
require_once($include_path."/notice_affichage.inc.php");
require_once($include_path."/navbar.inc.php");
require_once($include_path."/surlignage.inc.php");
require_once($class_path."/suggest.class.php");
/*
 *  Classe pilotant les recherches affiliées dans les sources externes
 */
 
class affiliate_search {
	public $affiliate_source = array();
	public $affiliate_source_name = array();
	public $look_array = array(
		"title" => 6,
		"author" => 8,
		"publisher" => 3,
		"collection" => 4,
		"subcollection" => 5,
		"category" => 1,
		"indexint" => 2,
		"keywords" => 12,
		"abstract" => 13,
		"titre_uniforme" => 27,
		"all" => 7,
		"concept" => 28
	);
	public $authorities_extended_array = array(
		'2'  => "author",
		'3'  => "publisher",
		'4'  => "collection",
		'5'  => "subcollection",
		'11' => "category",
		'12' => "indexint",
		'51' => "titre_uniforme",
		'6'  => "serie"
	);
	public $nb_results = 0;
	public $filter = '';
	
    public function __construct($user_query="",$search_type="notices") {
    	$this->user_query = $user_query;
    	$this->search_type = $search_type;
    	$this->fetch_sources();
    	$this->generateSearch();
    }
    
    //On récupère la liste des sources affiliées
    public function fetch_sources(){
  		global $base_path;
  		
  		$connectors = new connecteurs();
  		$this->catalog = $connectors->catalog;
    	foreach ($connectors->catalog as $id=>$prop) {
			$comment=$prop['COMMENT'];
			//Recherche du nombre de sources
			$n_sources=0;
			if (is_file($base_path."/admin/connecteurs/in/".$prop['PATH']."/".$prop['NAME'].".class.php")) {
				require_once($base_path."/admin/connecteurs/in/".$prop['PATH']."/".$prop['NAME'].".class.php");
				eval("\$conn=new ".$prop['NAME']."(\"".$base_path."/admin/connecteurs/in/".$prop['PATH']."\");");
				$conn->get_sources();
				foreach($conn->sources as $source_id=>$s) {
					if($s['OPAC_AFFILIATE_SEARCH'] == 1 ){
					
						$this->affiliate_source[]= $s['SOURCE_ID'];
						$this->affiliate_source_name[]= $s['NAME'];
					}
	    		}
			}
    	}  	
    }
    
    public function generateSearch(){
 		//A surcharger
    }
    
    public function generateGlobals(){
    	global $search;
    	
    	$field_label = "f_".$this->look_array[$this->type];
		$search = array();
		$search[0] = $field_label;
		//opérateur
		$op_="BOOLEAN";
	    $op="op_0_".$field_label;
	    global ${$op};
	    ${$op}=$op_;
	    
	   	//contenu de la recherche
	    $field="field_0_".$field_label;
	    $field_=array();
	    $field_[0]=$this->user_query;
		global ${$field};
		${$field}=$field_;
	    
	    //opérateur inter-champ
	    $inter="inter_0_".$field_label;
	    global ${$inter};
	    ${$inter}="and";
	    		    		
	    //variables auxiliaires
	    $fieldvar_="fieldvar_0_".$field_label;
	    global ${$fieldvar_};
	    ${$fieldvar_}="";
	    $fieldvar=${$fieldvar_};
    }
    
    public function addSources(){
    	global $search;
    	global $op_0_s_2;
		global $field_0_s_2;
		
		$flag_found=false;
		if(empty($search)) {
			$search=array();
		}
		for ($i=0; $i<count($search); $i++) {
			if ($search[$i]=="s_2") { $flag_found=true; break; }
		}
		if (!$flag_found) {
			//Pas trouve, on decale tout !!
			for ($i=count($search)-1; $i>=0; $i--) {
				$search[$i+1]=$search[$i];
				$c_field = "field_".$i."_".$search[$i];
				$n_field = "field_".($i+1)."_".$search[$i];
				global ${$c_field}, ${$n_field};
				
				${$n_field}=${$c_field};
				${$c_field} = "";
				
				$c_op = "op_".$i."_".$search[$i];
				$n_op = "op_".($i+1)."_".$search[$i];
				global ${$c_op} ,${$n_op};
				${$n_op}=${$c_op};
				${$c_op} = "";
				
				$c_inter = "inter_".$i."_".$search[$i];
				$n_inter = "inter_".($i+1)."_".$search[$i];
				global ${$c_inter},${$n_inter};
				${$n_inter}=${$c_inter};
				${$c_inter} = "";
				
				$c_fieldvar = "fieldvar_".$i."_".$search[$i];
				$n_fieldvar = "fieldvar_".($i+1)."_".$search[$i];
				global ${$c_fieldvar},${$n_fieldvar};
				${$n_fieldvar}=${$c_fieldvar};
				${$c_fieldvar} = "";
			}
			$search[0]="s_2";
			$op_0_s_2="EQ";
			$field_0_s_2=$this->affiliate_source;
		}
    }
    
    public function makeSearch(){
 		//A surcharger
    } 
    
    public function getNbResults(){
 		//A surcharger
    }
    
    public function getTotalNbResults(){
    	$this->getNbResults();
    	if(is_array($this->getNbResults())){
			$nb_results = $this->nb_results['total'];
		}else $nb_results = $this->nb_results;
		return $nb_results;
    }
    
    public function getResults(){
    	switch($this->search_type){
    		case "authorities" :
    			return $this->getAuthoritiesResults();
    			break;
    		case "notices_authority" :
    			return $this->getNoticesAuthorityResults();
    			break;
    		case "notices" :
    		default :
    			return $this->getNoticesResults();
    			break;
    	}
    }
    
    public function getAuthoritiesResults(){
		//A surcharger
    }
    
	public function getNoticesResults() {
    	global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;						
						
    	global $count;
    	global $add_cart_link;    	
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}
    	
    	$requete = "select * from ".$this->table_tempo;
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		$resultat=pmb_mysql_query($requete,$dbh);

    	$this->results ="

		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
		";

		$this->results.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titles_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.=suggest::get_add_link();
		$this->results.="&nbsp;&nbsp;";
		
		flush();
		
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
    	while ($r=pmb_mysql_fetch_object($resultat)) {
			$this->results.= aff_notice_unimarc($r->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar();
		return $this->results;
    }
   
	public function getNoticesAuthorityResults(){
		//A surcharger
	}   
	public function make_hidden_search_form($url,$form_name="form_values",$target="",$close_form=true){
		$form = $this->external_search->make_hidden_search_form("",$form_name,"",$close_form);
		return $form;
	}
	
	public function getAuthorityLabel($id,$type){
		global $lang;
		switch ($type){
			case "author":
				$aut=new auteur($id);
				$libelle = $aut->get_isbd();
				break;
			case "category":
				$libelle = categories::getlibelle($id,$lang);
				break;
			case "publisher":
				$ed = new publisher($id);
				$libelle = $ed->get_isbd();
				break;
			case "collection" :
				$coll = new collection($id);
				$libelle = $coll->get_isbd();
				break;
			case "subcollection" :
				$coll = new subcollection($id);
				$libelle = $coll->get_isbd();
				break;
			case "serie" :
				$serie = new serie($id);
				$libelle = $serie->get_isbd();
				break;
			case "indexint" :
				$indexint = new indexint($id);
				$libelle = $indexint->get_isbd();
				break;
			case "titre_uniforme" :
				$tu = new titre_uniforme($id);
				$libelle = $tu->get_isbd();
				break;
			default :
				$libelle = "";
				break;		
		}
		return $libelle;
	}
	
	//gestion de la pagination...
	protected function getNavigationBar($nb_results=0) {
		global $page;
		global $opac_search_results_per_page;
		global $mode;
		
		if(!$page) $page = 1;
		if(!$nb_results) {
			$nb_results = $this->getTotalNbResults();
		}
		$url_page = "javascript:document.form_values.page.value=!!page!!; document.form_values.action = \"./index.php?lvl=more_results".($mode=='extended'?'&mode=extended':'')."&tab=affiliate\"; document.form_values.affiliate_page.value=document.form_values.page.value; document.form_values.submit()";
		$nb_per_page_custom_url = "javascript:document.form_values.nb_per_page_custom.value=!!nb_per_page_custom!!";
		$action = "javascript:document.form_values.page.value=document.form.page.value; document.form_values.action = \"./index.php?lvl=more_results".($mode=='extended'?'&mode=extended':'')."&tab=affiliate\"; document.form_values.affiliate_page.value=document.form_values.page.value; document.form_values.submit()";
		
		$navigationBar = "<div id='navbar'><hr />\n<div style='text-align:center'>";
		$navigationBar .= printnavbar($page, $nb_results, $opac_search_results_per_page, $url_page, $nb_per_page_custom_url, $action).
		$navigationBar .= "</div></div>";
		return $navigationBar;
	}
}

/*
 * Classe de recherche sur les notices
 */
class affiliate_search_records extends affiliate_search {

	public function generateSearch(){
		$this->generateGlobals();
		$this->addSources();
	}
    
    public function makeSearch(){
    	global $search;
    	$this->external_search=new search("search_simple_fields_unimarc");
		$this->table_tempo = $this->external_search->make_search("f_".$this->look_array[$this->type]);
    }
    
    public function getNbResults(){
    	if(!$this->nb_results){
    		if($this->table_tempo){
    			$requete="select count(1) from ".$this->table_tempo;
    			$resultat=pmb_mysql_query($requete);
    			$this->nb_results = @pmb_mysql_result($resultat,0,0);
    			if(!$this->nb_results) $this->nb_results = 0;
    		}else{
    			global $search;
    			$this->makeSearch();
    			$this->getNbResults();
    		}
    	}
    	return $this->nb_results;
    }
}

/*
 * Classe de recherche sur les autorités
 */
class affiliate_search_authorities extends affiliate_search {
	
	public function generateSearch(){
		$this->generateGlobals();
		$this->addSources();
	}
	
	public function makeSearch(){
		global $search;
		if($this->search_type == "authorities"){
			$search_file="search_simple_fields_authorities";
		}else $search_file="search_simple_fields_unimarc";
	
		$this->external_search=new search_authorities($search_file);
		$this->table_tempo = $this->external_search->make_search("f_".$this->look_array[$this->type]);
	}
	
	public function getNbResults(){
		if(!$this->nb_results){
			if($this->table_tempo){
				if($this->search_type == "authorities"){
					$this->fetch_authorities();
				}else{
					$requete="select count(1) from ".$this->table_tempo;
					$resultat=pmb_mysql_query($requete);
					$this->nb_results = @pmb_mysql_result($resultat,0,0);
					if(!$this->nb_results) $this->nb_results = 0;
				}
			}else{
				global $search;
				$this->makeSearch();
				$this->getNbResults();
			}
		}
		return $this->nb_results;
	}
}

/*
 * Classe de recherche sur les auteurs
 */
class affiliate_search_author extends affiliate_search_authorities {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "author";
		parent::__construct($user_query,$search_type);
	}	
    
	public function getAuthoritiesResults(){
    	global $msg;
    	global $opac_search_results_per_page;
    	global $page;
    	    	
    	if($this->table_tempo){
 			if(!$this->authoritiesResult){
		    	$this->authoritiesResult ="
				<div id='resultatrech_container'>
					<div id='resultatrech_see'>";
				switch($this->filter){
					case "71":
						$result_label = $msg['collectivites_found'];
						break;
					case "72" :
						$result_label = $msg['congres_found'];
						break;
					case "70" :
					default :
						$result_label = $msg['authors_found'];
						break;															
				}
				$this->authoritiesResult.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$result_label." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
				$this->authoritiesResult.= "
					</div>
					<div id='resultatrech_liste'>
						<ul>";
				$aut = $this->fetch_auteurs();
				$authors_tab = array_merge($aut['authors'],$aut['collectivities'],$aut['congres']);
				if(!$page) $page = 1; 
	    		$start_page=$opac_search_results_per_page*($page-1);
	    		$last_item = ($start_page+$opac_search_results_per_page) <= count($authors_tab) ? ($start_page+$opac_search_results_per_page) : count($authors_tab);
	    		for($i = $start_page ; $i<$last_item ; $i++){
	    			$this->authoritiesResult.= "
					<li class='categ_colonne'><span class='notice_fort'><a href='#'  onclick='document.form_values.action=\"./index.php?lvl=external_authorities&type=author&filter=".$authors_tab[$i]['type']."&ext_value=".urlencode($authors_tab[$i]['value'])."\";document.form_values.submit();return false;'>".$authors_tab[$i]['label']."</a></span></li>";
	    		}

		    	$this->authoritiesResult.= "
						</ul>
		    		</div>
				</div>";
				//gestion de la pagination...
				$this->authoritiesResult.=  $this->getNavigationBar();
 			}
    	}else{
    		global $search;
    		$this->makeSearch();
    		$this->getAuthoritiesResults();
    	}
    	return $this->authoritiesResult;
    } 
    	
	public function getNoticesAuthorityResults(){
		global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $fonction_auteur;					
						
    	global $count;
    	global $add_cart_link;    	
    	
    	global $ext_value;
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}	
    	
    	$joins = array();
		foreach($this->affiliate_source as $aff_source){
			switch($this->filter){
				case "70" :
					$joins[] = "join entrepot_source_$aff_source on recid = notice_id and ufield like '70_' and value like '$ext_value'";
					break;	
				case "71" :
					$joins[] = "join entrepot_source_$aff_source on recid = notice_id and ufield like '71_' and field_ind not like '1%' and value like '$ext_value'";
					break;	
				case "72" :
					$joins[] = "join entrepot_source_$aff_source on recid = notice_id and ufield like '71_' and field_ind like '1%' and value like '$ext_value'";
					break;
				default :
					$joins[] = "join entrepot_source_$aff_source on recid = notice_id and value like '$ext_value'";
					break;
			}
		}		
		if(count($joins)){
			$result = array();
			$nb_results=0;
			foreach($joins as $join){
				$res =pmb_mysql_query("select distinct notice_id, pert from ".$this->table_tempo." ".$join);
				while($row = pmb_mysql_fetch_object($res)){
					$result[]=$row;
					$nb_results++;
				}
			}
			//TODO : Classsement du tableau par pert...
		}
		
		switch($this->filter){
			case "71":
				$authority_title = $msg['collectivite_see_title']; 
				break;
			case "72":
				$authority_title = $msg['congres_see_title'];
				break;
			case "70":
			default :
				$authority_title = $msg['author_see_title'];
				break;
		}

		//construction des infos de l'autorité...
		$rqt= "select distinct source_id from ".$this->table_tempo." join  external_count on notice_id = rid";
		$resultat = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($resultat)){
			while($r = pmb_mysql_fetch_object($resultat)){
				switch($this->filter){
					case "70" :
						$where = " and ufield like '70_' and value like '$ext_value'";
						break;	
					case "71" :
						$where = " and ufield like '71_' and field_ind not like '1%' and value like '$ext_value'";
						break;	
					case "72" :
						$where = " and ufield like '71_' and field_ind like '1%' and value like '$ext_value'";
						break;
					default :
						$where = " and ufield like '7%' and value like '$ext_value'";
						break;												
				}	
				
				$rqt = "select ufield,field_order,recid from entrepot_source_".$r->source_id." join ".$this->table_tempo." where recid=notice_id $where group by ufield,field_order order by recid,field_order";
				$result_sql = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($result_sql)){
					while($col = pmb_mysql_fetch_object($result_sql)){
						$rqt = "select ufield,field_order,usubfield,value from entrepot_source_".$r->source_id." where recid='".$col->recid."' and ufield = '".$col->ufield."' and field_order = '".$col->field_order."' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
						$plop = pmb_mysql_query($rqt);
						if(pmb_mysql_num_rows($plop)){
							while($elem = pmb_mysql_fetch_object($plop)){
								switch ($elem->usubfield) {
									case "4":
										$auteur["fonction"]=$elem->value;
										$auteur["fonction_aff"]=$fonction_auteur[$elem->value];
										break;
									case "a":
										$auteur["name"]=$elem->value;
										break;
									case "b":
										$auteur["rejete"]=$elem->value;
										break;
									case "d":
										$auteur["numéro"]=$elem->value;
										break;
									case "e":
										$auteur["lieu"]=$elem->value;
										break;
									case "f":
										$auteur["date"]=$elem->value;
										break;
								}
							}
						}
					}
					$aut_titre=$auteur["rejete"].($auteur["rejete"]?" ":"").$auteur["name"];
					$auteur_cplt = ($auteur["numéro"]?" ".$auteur["numéro"]:"");
					$auteur_cplt.=($auteur_cplt ? " ; " : "").($auteur["date"]? $auteur["date"] : "");
					$auteur_cplt.=($auteur_cplt ? " ; " : "").($auteur["lieu"]? $auteur["lieu"] : "");
					$aut_titre.=($auteur_cplt ? " > ".$auteur_cplt : "");
						
					//si on a trouvé un résultat, on insiste pas...
					break;
				}
			}
		}
		
		$this->results ="
		<div id='aut_details'>
			<h3>".htmlentities($authority_title,ENT_QUOTES,$charset)."</h3>
			<div id='aut_see'>
				<div class='authorlevel2'>
					<h3>".htmlentities($aut_titre,ENT_QUOTES,$charset)."</h3>
				</div>
			</div>
			<div id='aut_details_liste'>
			<h3>".htmlentities($nb_results,ENT_QUOTES,$charset)." ".$msg['affiliate_search_external_authority_results'].implode($msg['affiliate_search_external_results_and'],$this->affiliate_source_name)."</h3>";

		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
		$last_item = ($start_page+$opac_search_results_per_page) <= count($result) ? ($start_page+$opac_search_results_per_page) : count($result);
    	for($i=$start_page ; $i<$last_item ; $i++){
			$this->results.= aff_notice_unimarc($result[$i]->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar($nb_results);
		return $this->results;	
	}
	
	public function fetch_authorities() {
		global $fonction_auteur;
		global $dbh ;
		global $opac_url_base ;

		switch($this->filter){
			case "70" :
				$rqt =  "select *,'70' as type from ".$this->table_tempo." where infos like '70%'";
				break;	
			case "71" :
				$rqt =  "select *,'71' as type from ".$this->table_tempo." where infos not like '71_|1%' and infos  like '71%'";
				break;	
			case "72" :
				$rqt =  "select *,'72' as type from ".$this->table_tempo." where infos like '71_|1%'";
				break;
			default :
				$rqt =  "select *,if(infos like '70%','70',if(infos like '71_|1%','72','71')) as type from ".$this->table_tempo;
				break;
		}

		$res_sql=pmb_mysql_query($rqt) or die (pmb_mysql_error());
		$authors = $collectivities = $congres = array();
		while($row= pmb_mysql_fetch_object($res_sql)){
			$infos = explode("|",$row->infos);
			switch($row->type){
				case "70" :
					$rqt  = "select * from entrepot_source_".$infos[7]." where recid='".$infos[6]."' and ufield = '".$infos[0]."' and field_order='".$infos[2]."'";
					$res = pmb_mysql_query($rqt);
					$rejete=$entree="";
					if(pmb_mysql_num_rows($res))
						while($r = pmb_mysql_fetch_object($res)){
							switch($r->usubfield){
								case "a" :
									$entree = $r->value;
									break;
								case "b" :
									$rejete = $r->value;
									break;
							}
						}
					$authors[]=array(
						'value' => $infos[5],
						'label' =>($rejete ? $rejete." " : "").$entree,
						'type' => "70"
					);
					break;
				case "71" :
					$rqt  = "select * from entrepot_source_".$infos[7]." where recid='".$infos[6]."' and ufield = '".$infos[0]."' and field_order='".$infos[2]."'";
					$res = pmb_mysql_query($rqt);
					$rejete=$entree="";
					if(pmb_mysql_num_rows($res))
						while($r = pmb_mysql_fetch_object($res)){
							switch($r->usubfield){
								case "a" :
									$entree = $r->value;
									break;
								case "c" :
									$rejete = $r->value;
									break;
							}
						}
					$collectivities[]=array(
						'value' => $infos[5],
						'label' =>$entree.($rejete ? ", ".$rejete : ""),
						'type' => "71"
					);
					break;
				case "72" :
					$rqt  = "select * from entrepot_source_".$infos[7]." where recid='".$infos[6]."' and ufield = '".$infos[0]."' and field_order='".$infos[2]."'";
					$res = pmb_mysql_query($rqt);
					$rejete=$entree="";
					if(pmb_mysql_num_rows($res))
						while($r = pmb_mysql_fetch_object($res)){
							switch($r->usubfield){
								case "a" :
									$entree = $r->value;
									break;
								case "c" :
									$rejete = $r->value;
									break;
							}
						}
					$congres[]=array(
						'value' => $infos[5],
						'label' =>$entree.($rejete ? ", ".$rejete : ""),
						'type' => "72"
					);
					break;
			}
		}
		$authors = array_unique($authors,SORT_REGULAR);
		$collectivities = array_unique($collectivities,SORT_REGULAR);
		$congres = array_unique($congres,SORT_REGULAR);
		
		if(!is_array($this->nb_results)) $this->nb_results = array();
		$this->nb_results['total'] = count($authors)+count($collectivities)+count($congres);
		$this->nb_results['authors'] = count($authors);
		$this->nb_results['coll'] = count($collectivities);
		$this->nb_results['congres'] = count($congres);
		return array(
			'authors' => $authors,
			'collectivities' => $collectivities,
			'congres' => $congres
		);
	} // fin fetch_auteurs	   
}

/*
 * Classe de recherche sur les auteurs
 */
class affiliate_search_collection extends affiliate_search_authorities {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "collection";
		parent::__construct($user_query,$search_type);
	} 
    
   public function getAuthoritiesResults(){
    	global $msg;
    	global $opac_search_results_per_page;
    	global $page;
    	    	
    	if($this->table_tempo){
 			if(!$this->authoritiesResult){
		    	$this->authoritiesResult ="
				<div id='resultatrech_container'>
					<div id='resultatrech_see'>";


				$this->authoritiesResult.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['collections_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
				$this->authoritiesResult.= "
					</div>
					<div id='resultatrech_liste'>
						<ul>";	
				$coll = $this->fetch_collections();

				if(!$page) $page = 1; 
	    		$start_page=$opac_search_results_per_page*($page-1);
	    		$last_item = ($start_page+$opac_search_results_per_page) <= count($coll) ? ($start_page+$opac_search_results_per_page) : count($coll);
	    		for($i = $start_page ; $i<$last_item ; $i++){
	    			$this->authoritiesResult.= "
					<li class='categ_colonne'><span class='notice_fort'><a href='#'  onclick='document.form_values.action=\"./index.php?lvl=external_authorities&type=collection&ext_value=".urlencode($coll[$i])."\";document.form_values.submit();return false;'>".$coll[$i]."</a></span></li>";
	    		}												

		    	$this->authoritiesResult.= "
						</ul>
		    		</div>
				</div>";
				//gestion de la pagination...
				$this->authoritiesResult.=  $this->getNavigationBar();
 			}
    	}else{
    		global $search;
    		$this->makeSearch();
    		$this->getAuthoritiesResults();
    	}
    	return $this->authoritiesResult;
    }   
    
 	public function getNoticesAuthorityResults(){
		global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $fonction_auteur;					
						
    	global $count;
    	global $add_cart_link;    	
    	
    	global $ext_value;
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}	
    	
    	$joins = array();
		foreach($this->affiliate_source as $aff_source){
			$joins[] = "join entrepot_source_$aff_source on recid = notice_id and value like '$ext_value'";
		}		
		if(count($joins)){
			$result = array();
			$nb_results=0;
			foreach($joins as $join){
				$res =pmb_mysql_query("select distinct notice_id, pert from ".$this->table_tempo." ".$join);
				while($row = pmb_mysql_fetch_object($res)){
					$result[]=$row;
					$nb_results++;
				}
			}
			//TODO : Classsement du tableau par pert...
		}
		
		$authority_title = $msg['detail_coll'];

		//construction des infos de l'autorité...
		$rqt= "select distinct source_id from ".$this->table_tempo." join  external_count on notice_id = rid";
		$resultat = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($resultat)){
			while($r = pmb_mysql_fetch_object($resultat)){
				$rqt = "select ufield,field_order,recid from entrepot_source_".$r->source_id." join ".$this->table_tempo." where recid=notice_id  and ufield like '225' and value like '$ext_value' group by ufield,field_order order by recid,field_order";
				$result_sql = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($result_sql)){
					while($col = pmb_mysql_fetch_object($result_sql)){
						$rqt = "select ufield,field_order,usubfield,value from entrepot_source_".$r->source_id." where recid='".$col->recid."' and ((ufield = '".$col->ufield."' and field_order = '".$col->field_order."') or ufield = '210') group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
						$plop = pmb_mysql_query($rqt);
						if(pmb_mysql_num_rows($plop)){
							while($elem = pmb_mysql_fetch_object($plop)){
								switch($elem->ufield){
									case "225" :
										switch($elem->usubfield){
											case "a" :
												$coll_name = $elem->value;
												break;
											case "x" :
												$issn = $elem->value;
												break;												
										}
									
										break;
									case "210" :
									case "219":
										switch($elem->usubfield){
											case "c" :
												$publisher = $elem->value;
												break;
										}								
										break;
								}
								
							}
						}
					}
					//si on a trouvé un résultat, on insiste pas...
					break;
				}
			}
		}
		$this->results ="
		<div id='aut_details'>
			<h3>".htmlentities($authority_title,ENT_QUOTES,$charset)."</h3>
			<div id='aut_see'>
				<div class='collectionlevel2'>
					<h3>".$msg['collection_tpl_coll']." ".htmlentities($coll_name,ENT_QUOTES,$charset)."</h3>
					<ul>
						<li>".$msg['collection_tpl_publisher']." : ".htmlentities($publisher,ENT_QUOTES,$charset)."</li>";
		if($issn) $this->results .="
						<li>".$msg['collection_tpl_issn']." : ".htmlentities($issn,ENT_QUOTES,$charset)."</li>";
		$this->results .="				
					</ul>
				</div>
			</div>
			<div id='aut_details_liste'>
			<h3>".htmlentities($nb_results,ENT_QUOTES,$charset)." ".$msg['affiliate_search_external_authority_results'].implode($msg['affiliate_search_external_results_and'],$this->affiliate_source_name)."</h3>";

		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
		$last_item = ($start_page+$opac_search_results_per_page) <= count($result) ? ($start_page+$opac_search_results_per_page) : count($result);
    	for($i=$start_page ; $i<$last_item ; $i++){
			$this->results.= aff_notice_unimarc($result[$i]->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar($nb_results);
		return $this->results;	
	}   
	
	public function getNoticesResults() {
    	global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;						
						
    	global $count;
    	global $add_cart_link;    	
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}
    	
    	$requete = "select * from ".$this->table_tempo;
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		$resultat=pmb_mysql_query($requete,$dbh);

    	$this->results ="

		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
		";

		$this->results.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titles_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
		$this->results.=suggest::get_add_link();
		flush();
		
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
    	while ($r=pmb_mysql_fetch_object($resultat)) {
			$this->results.= aff_notice_unimarc($r->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		//$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar();
		return $this->results;
    }    

	public function fetch_authorities() {
		global $fonction_auteur;
		global $dbh ;
		global $opac_url_base ;
	
		$auteurs = array() ;

		$rqt = "select * from ".$this->table_tempo." where infos like '225%' or infos like '410%'";
		
		$res_sql=pmb_mysql_query($rqt);
		$collections=array();
		while($row= pmb_mysql_fetch_object($res_sql)){
			$infos = explode("|",$row->infos);
			if(!in_array($infos[5],$collections))
				$collections[]=$infos[5];
		}
		if(!is_array($this->nb_results)) $this->nb_results = array();
		$this->nb_results['total'] = count($collections);
		return $collections;
	} // fin fetch_collections	
}


class affiliate_search_abstract extends affiliate_search_records {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "abstract";
		parent::__construct($user_query,$search_type);
	}
}

class affiliate_search_title extends affiliate_search_records {
	
	public function __construct($user_query=""){
		$this->type= "title";
		parent::__construct($user_query,"notices");
	}
}

class affiliate_search_all extends affiliate_search_records {
	
	public function __construct($user_query=""){
		$this->type= "all";
		parent::__construct($user_query,"notices");
	} 
}

class affiliate_search_indexint extends affiliate_search_authorities {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "indexint";
		parent::__construct($user_query,$search_type);
	} 
    
   public function getAuthoritiesResults(){
    	global $msg;
    	global $opac_search_results_per_page;
    	global $page;
    	    	
    	if($this->table_tempo){
 			if(!$this->authoritiesResult){
		    	$this->authoritiesResult ="
				<div id='resultatrech_container'>
					<div id='resultatrech_see'>";


				$this->authoritiesResult.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['indexint_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
				$this->authoritiesResult.= "
					</div>
					<div id='resultatrech_liste'>
						<ul>";	
				$indexint = $this->fetch_indexint();

				if(!$page) $page = 1; 
	    		$start_page=$opac_search_results_per_page*($page-1);
	    		$last_item = ($start_page+$opac_search_results_per_page) <= count($indexint) ? ($start_page+$opac_search_results_per_page) : count($indexint);
	    		for($i = $start_page ; $i<$last_item ; $i++){
	    			$this->authoritiesResult.= "
					<li><a href='#' onclick='document.form_values.action=\"./index.php?lvl=external_authorities&type=".$this->type."&ext_value=".urlencode($indexint[$i])."\";document.form_values.submit();return false;'><img style='border:0px' src='".get_url_icon('folder.gif')."'> ".$indexint[$i]."</a></li>";
	    		}												

		    	$this->authoritiesResult.= "
						</ul>
		    		</div>
				</div>";
				//gestion de la pagination...
				$this->authoritiesResult.=  $this->getNavigationBar();
 			}
    	}else{
    		global $search;
    		$this->makeSearch();
    		$this->getAuthoritiesResults();
    	}
    	return $this->authoritiesResult;
    }   
    
 	public function getNoticesAuthorityResults(){
		global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $fonction_auteur;					
						
    	global $count;
    	global $add_cart_link;    	
    	
    	global $ext_value;
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}	
    	
    	$joins = array();
		foreach($this->affiliate_source as $aff_source){
			$joins[] = "join entrepot_source_$aff_source on recid = notice_id and value like '$ext_value'";
		}		
		if(count($joins)){
			$result = array();
			$nb_results=0;
			foreach($joins as $join){
				$res =pmb_mysql_query("select distinct notice_id, pert from ".$this->table_tempo." ".$join);
				while($row = pmb_mysql_fetch_object($res)){
					$result[]=$row;
					$nb_results++;
				}
			}
			//TODO : Classsement du tableau par pert...
		}
		
		$authority_title = $msg['detail_indexint'];

		//construction des infos de l'autorité...
		$rqt= "select distinct source_id from ".$this->table_tempo." join  external_count on notice_id = rid";
		$resultat = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($resultat)){
			while($r = pmb_mysql_fetch_object($resultat)){
				$rqt = "select ufield,field_order,recid from entrepot_source_".$r->source_id." join ".$this->table_tempo." where recid=notice_id  and (ufield like '67%' or ufield like '68%') and value like '$ext_value' group by ufield,field_order order by recid,field_order";
				$result_sql = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($result_sql)){
					while($col = pmb_mysql_fetch_object($result_sql)){
						$rqt = "select ufield,field_order,usubfield,value from entrepot_source_".$r->source_id." where recid='".$col->recid."' and ((ufield = '".$col->ufield."' and field_order = '".$col->field_order."')) group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
						$plop = pmb_mysql_query($rqt);
						if(pmb_mysql_num_rows($plop)){
							while($elem = pmb_mysql_fetch_object($plop)){
								switch($elem->usubfield){
									case "a" :
										$indexint_lib = $elem->value;
										break;											
								}
							}
						}
					}
					//si on a trouvé un résultat, on insiste pas...
					break;
				}
			}
		}
		$this->results ="
		<div id='aut_details'>
			<h3>".htmlentities($authority_title,ENT_QUOTES,$charset)."</h3>
			<div id='aut_see'>
				<div class='indexintlevel2'>
					<h3>".htmlentities($indexint_lib,ENT_QUOTES,$charset)."</h3>
				</div>
			</div>
			<div id='aut_details_liste'>
			<h3>".htmlentities($nb_results,ENT_QUOTES,$charset)." ".$msg['affiliate_search_external_authority_results'].implode($msg['affiliate_search_external_results_and'],$this->affiliate_source_name)."</h3>";

		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
		$last_item = ($start_page+$opac_search_results_per_page) <= count($result) ? ($start_page+$opac_search_results_per_page) : count($result);
    	for($i=$start_page ; $i<$last_item ; $i++){
			$this->results.= aff_notice_unimarc($result[$i]->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar($nb_results);
		return $this->results;	
	}   
	
	public function getNoticesResults() {
    	global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;						
						
    	global $count;
    	global $add_cart_link;    	
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}
    	
    	$requete = "select * from ".$this->table_tempo;
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		$resultat=pmb_mysql_query($requete,$dbh);

    	$this->results ="

		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
		";

		$this->results.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titles_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
		$this->results.=suggest::get_add_link();
		flush();
		
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
    	while ($r=pmb_mysql_fetch_object($resultat)) {
			$this->results.= aff_notice_unimarc($r->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		//$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar();
		return $this->results;
    }
    
    public function fetch_authorities(){
		global $dbh ;
		global $opac_url_base ;
	
		$auteurs = array() ;

		$rqt = "select * from ".$this->table_tempo." where infos like '67%' or infos like '68%'";
		
		$res_sql=pmb_mysql_query($rqt);
		$indexint=array();
		while($row= pmb_mysql_fetch_object($res_sql)){
			$infos = explode("|",$row->infos);
			if(!in_array($infos[5],$indexint))
				$indexint[]=$infos[5];
		}
		$this->nb_results['total'] = count($indexint);
		return $indexint;
    	
    }
}

class affiliate_search_publisher extends affiliate_search_authorities {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "publisher";
		parent::__construct($user_query,$search_type);
	} 
    
   public function getAuthoritiesResults(){
    	global $msg;
    	global $opac_search_results_per_page;
    	global $page;
    	    	
    	if($this->table_tempo){
 			if(!$this->authoritiesResult){
		    	$this->authoritiesResult ="
				<div id='resultatrech_container'>
					<div id='resultatrech_see'>";


				$this->authoritiesResult.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['collections_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
				$this->authoritiesResult.= "
					</div>
					<div id='resultatrech_liste'>
						<ul>";	
				$eds = $this->fetch_publishers();

				if(!$page) $page = 1; 
	    		$start_page=$opac_search_results_per_page*($page-1);
	    		$last_item = ($start_page+$opac_search_results_per_page) <= count($eds) ? ($start_page+$opac_search_results_per_page) : count($eds);
	    		for($i = $start_page ; $i<$last_item ; $i++){
	    			$this->authoritiesResult.= "
					<li class='categ_colonne'><span class='notice_fort'><a href='#'  onclick='document.form_values.action=\"./index.php?lvl=external_authorities&type=publisher&ext_value=".urlencode($eds[$i])."\";document.form_values.submit();return false;'>".$eds[$i]."</a></span></li>";
	    		}												

		    	$this->authoritiesResult.= "
						</ul>
		    		</div>
				</div>";
				//gestion de la pagination...
				$this->authoritiesResult.=  $this->getNavigationBar();
 			}
    	}else{
    		global $search;
    		$this->makeSearch();
    		$this->getAuthoritiesResults();
    	}
    	return $this->authoritiesResult;
    }   
    
 	public function getNoticesAuthorityResults(){
		global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $fonction_auteur;					
						
    	global $count;
    	global $add_cart_link;    	
    	
    	global $ext_value;
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}	
    	
    	$joins = array();
		foreach($this->affiliate_source as $aff_source){
			$joins[] = "join entrepot_source_$aff_source on recid = notice_id and value like '$ext_value'";
		}		
		if(count($joins)){
			$result = array();
			$nb_results=0;
			foreach($joins as $join){
				$res =pmb_mysql_query("select distinct notice_id, pert from ".$this->table_tempo." ".$join);
				while($row = pmb_mysql_fetch_object($res)){
					$result[]=$row;
					$nb_results++;
				}
			}
			//TODO : Classsement du tableau par pert...
		}
		
		$authority_title = $msg['publisher_see_title'];

		//construction des infos de l'autorité...
		$rqt= "select distinct source_id from ".$this->table_tempo." join  external_count on notice_id = rid";
		$resultat = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($resultat)){
			while($r = pmb_mysql_fetch_object($resultat)){
				$rqt = "select ufield,field_order,recid from entrepot_source_".$r->source_id." join ".$this->table_tempo." where recid=notice_id  and ufield like '210' and value like '$ext_value' group by ufield,field_order order by recid,field_order";
				$result_sql = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($result_sql)){
					while($col = pmb_mysql_fetch_object($result_sql)){
						$rqt = "select ufield,field_order,usubfield,value from entrepot_source_".$r->source_id." where recid='".$col->recid."' and ufield = '210' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
						$plop = pmb_mysql_query($rqt);
						if(pmb_mysql_num_rows($plop)){
							while($elem = pmb_mysql_fetch_object($plop)){
								switch($elem->usubfield){
									case "c" :
										$publisher = $elem->value;
										break;
									case "a" :
										$lieu = $elem->value;
										break;
								}								
							}
						}
					}
					//si on a trouvé un résultat, on insiste pas...
					break;
				}
			}
		}
		$this->results ="
		<div id='aut_details'>
			<h3>".htmlentities($authority_title,ENT_QUOTES,$charset)."</h3>
			<div id='aut_see'>
				<div class='collectionlevel2'>
					<h3>".sprintf($msg["publisher_details_publisher"],$publisher)."</h3>
					".($lieu ? "<p>".sprintf($msg["publisher_details_location"],$lieu)."</p>." : "")."
				</div>
			</div>
			<div id='aut_details_liste'>
			<h3>".htmlentities($nb_results,ENT_QUOTES,$charset)." ".$msg['affiliate_search_external_authority_results'].implode($msg['affiliate_search_external_results_and'],$this->affiliate_source_name)."</h3>";

		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
		$last_item = ($start_page+$opac_search_results_per_page) <= count($result) ? ($start_page+$opac_search_results_per_page) : count($result);
    	for($i=$start_page ; $i<$last_item ; $i++){
			$this->results.= aff_notice_unimarc($result[$i]->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar($nb_results);
		return $this->results;	
	}   
	
	public function getNoticesResults() {
    	global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;						
						
    	global $count;
    	global $add_cart_link;    	
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}
    	
    	$requete = "select * from ".$this->table_tempo;
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		$resultat=pmb_mysql_query($requete,$dbh);

    	$this->results ="

		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
		";

		$this->results.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titles_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
		$this->results.=suggest::get_add_link();
		flush();
		
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
    	while ($r=pmb_mysql_fetch_object($resultat)) {
			$this->results.= aff_notice_unimarc($r->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		//$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar();
		return $this->results;
    }    

	public function fetch_authorities() {
		global $dbh ;
		global $opac_url_base ;
	
		$auteurs = array() ;

		$rqt = "select * from ".$this->table_tempo." where infos like '210%'";
		
		$res_sql=pmb_mysql_query($rqt);
		$publishers=array();
		while($row= pmb_mysql_fetch_object($res_sql)){
			$infos = explode("|",$row->infos);
			if(!in_array($infos[5],$publishers))
				$publishers[]=$infos[5];
		}
		if(!is_array($this->nb_results)) $this->nb_results = array();
		$this->nb_results['total'] = count($publishers);
		return $publishers;
	} // fin fetch_publishers	
}

class affiliate_search_keywords extends affiliate_search_records {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "keywords";
		parent::__construct($user_query,$search_type);
	}  
}

class affiliate_search_category extends affiliate_search_authorities {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "category";
		parent::__construct($user_query,$search_type);
	} 
    
   public function getAuthoritiesResults(){
    	global $msg;
    	global $opac_search_results_per_page;
    	global $page;
    	    	
    	if($this->table_tempo){
 			if(!$this->authoritiesResult){
		    	$this->authoritiesResult ="
				<div id='resultatrech_container'>
					<div id='resultatrech_see'>";


				$this->authoritiesResult.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['categs_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
				$this->authoritiesResult.= "
					</div>
					<div id='resultatrech_liste'>
						<ul>";	
				$categ = $this->fetch_categories();

				if(!$page) $page = 1; 
	    		$start_page=$opac_search_results_per_page*($page-1);
	    		$last_item = ($start_page+$opac_search_results_per_page) <= count($categ) ? ($start_page+$opac_search_results_per_page) : count($categ);
	    		for($i = $start_page ; $i<$last_item ; $i++){
	    			$this->authoritiesResult.= "
					<li class='categ_colonne'><span class='notice_fort'><a href='#'  onclick='document.form_values.action=\"./index.php?lvl=external_authorities&type=".$this->type."&ext_value=".urlencode($categ[$i])."\";document.form_values.submit();return false;'>".$categ[$i]."</a></span></li>";
	    		}												

		    	$this->authoritiesResult.= "
						</ul>
		    		</div>
				</div>";
				//gestion de la pagination...
				$this->authoritiesResult.=  $this->getNavigationBar();
 			}
    	}else{
    		global $search;
    		$this->makeSearch();
    		$this->getAuthoritiesResults();
    	}
    	return $this->authoritiesResult;
    }   
    
 	public function getNoticesAuthorityResults(){
		global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $fonction_auteur;					
						
    	global $count;
    	global $add_cart_link;    	
    	
    	global $ext_value;
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}	
    	
    	$joins = array();
		foreach($this->affiliate_source as $aff_source){
			$joins[] = "join entrepot_source_$aff_source on recid = notice_id and value like '$ext_value'";
		}		
		if(count($joins)){
			$result = array();
			$nb_results=0;
			foreach($joins as $join){
				$res =pmb_mysql_query("select distinct notice_id, pert from ".$this->table_tempo." ".$join);
				while($row = pmb_mysql_fetch_object($res)){
					$result[]=$row;
					$nb_results++;
				}
			}
			//TODO : Classsement du tableau par pert...
		}
		
		$authority_title = $msg['categories'];

		//construction des infos de l'autorité...
		$rqt= "select distinct source_id from ".$this->table_tempo." join  external_count on notice_id = rid";
		$resultat = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($resultat)){
			while($r = pmb_mysql_fetch_object($resultat)){
				$rqt = "select ufield,field_order,recid from entrepot_source_".$r->source_id." join ".$this->table_tempo." where recid=notice_id  and ufield like '606' and value like '$ext_value' group by ufield,field_order order by recid,field_order";
				$result_sql = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($result_sql)){
					while($col = pmb_mysql_fetch_object($result_sql)){
						$rqt = "select ufield,field_order,usubfield,value from entrepot_source_".$r->source_id." where recid='".$col->recid."' and ((ufield = '".$col->ufield."' and field_order = '".$col->field_order."')) group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
						$plop = pmb_mysql_query($rqt);
						if(pmb_mysql_num_rows($plop)){
							while($elem = pmb_mysql_fetch_object($plop)){
								switch($elem->usubfield){
									case "a" :
										$categ_lib = $elem->value;
										
										break;
								}								
							}
						}
					}
					//si on a trouvé un résultat, on insiste pas...
					break;
				}
			}
		}
		$this->results ="
		<div id='aut_details'>
			<h3>".htmlentities($authority_title,ENT_QUOTES,$charset)."</h3>
			<div id='aut_see'>
				<div class='categorylevel2'>
					<h3>".$msg['category']." ".htmlentities($categ_lib,ENT_QUOTES,$charset)."</h3>
				</div>
			</div>
			<div id='aut_details_liste'>
			<h3>".htmlentities($nb_results,ENT_QUOTES,$charset)." ".$msg['affiliate_search_external_authority_results'].implode($msg['affiliate_search_external_results_and'],$this->affiliate_source_name)."</h3>";

		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
		$last_item = ($start_page+$opac_search_results_per_page) <= count($result) ? ($start_page+$opac_search_results_per_page) : count($result);
    	for($i=$start_page ; $i<$last_item ; $i++){
			$this->results.= aff_notice_unimarc($result[$i]->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.= $this->getNavigationBar($nb_results);
		return $this->results;	
	}   
	
	public function getNoticesResults() {
    	global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;						
						
    	global $count;
    	global $add_cart_link;    	
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}
    	
    	$requete = "select * from ".$this->table_tempo;
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		$resultat=pmb_mysql_query($requete,$dbh);

    	$this->results ="

		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
		";

		$this->results.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titles_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
		$this->results.=suggest::get_add_link();
		flush();
		
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
    	while ($r=pmb_mysql_fetch_object($resultat)) {
			$this->results.= aff_notice_unimarc($r->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		//$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar();
		return $this->results;
    }    

	public function fetch_authorities() {
		global $dbh ;
		global $opac_url_base ;
	
		$auteurs = array() ;

		$rqt = "select * from ".$this->table_tempo." where infos like '606%'";
		
		$res_sql=pmb_mysql_query($rqt);
		$categ=array();
		while($row= pmb_mysql_fetch_object($res_sql)){
			$infos = explode("|",$row->infos);
			if(!in_array($infos[5],$categ))
				$categ[]=$infos[5];
		}
		if(!is_array($this->nb_results)) $this->nb_results = array();
		$this->nb_results['total'] = count($categ);
		return $categ;
	} // fin fetch_categories	
}

class affiliate_search_subcollection extends affiliate_search_authorities {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "subcollection";
		parent::__construct($user_query,$search_type);
	} 
    
   public function getAuthoritiesResults(){
    	global $msg;
    	global $opac_search_results_per_page;
    	global $page;
    	    	
    	if($this->table_tempo){
 			if(!$this->authoritiesResult){
		    	$this->authoritiesResult ="
				<div id='resultatrech_container'>
					<div id='resultatrech_see'>";


				$this->authoritiesResult.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['subcolls_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
				$this->authoritiesResult.= "
					</div>
					<div id='resultatrech_liste'>
						<ul>";	
				$subcoll = $this->fetch_subcollections();

				if(!$page) $page = 1; 
	    		$start_page=$opac_search_results_per_page*($page-1);
	    		$last_item = ($start_page+$opac_search_results_per_page) <= count($subcoll) ? ($start_page+$opac_search_results_per_page) : count($subcoll);
	    		for($i = $start_page ; $i<$last_item ; $i++){
	    			$this->authoritiesResult.= "
					<li class='categ_colonne'><span class='notice_fort'><a href='#'  onclick='document.form_values.action=\"./index.php?lvl=external_authorities&type=subcollection&ext_value=".urlencode($subcoll[$i])."\";document.form_values.submit();return false;'>".$subcoll[$i]."</a></span></li>";
	    		}												

		    	$this->authoritiesResult.= "
						</ul>
		    		</div>
				</div>";
				//gestion de la pagination...
				$this->authoritiesResult.=  $this->getNavigationBar();
 			}
    	}else{
    		global $search;
    		$this->makeSearch();
    		$this->getAuthoritiesResults();
    	}
    	return $this->authoritiesResult;
    }   
    
 	public function getNoticesAuthorityResults(){
		global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $fonction_auteur;					
						
    	global $count;
    	global $add_cart_link;    	
    	
    	global $ext_value;
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}	
    	
    	$joins = array();
		foreach($this->affiliate_source as $aff_source){
			$joins[] = "join entrepot_source_$aff_source on recid = notice_id and value like '$ext_value'";
		}		
		if(count($joins)){
			$result = array();
			$nb_results=0;
			foreach($joins as $join){
				$res =pmb_mysql_query("select distinct notice_id, pert from ".$this->table_tempo." ".$join);
				while($row = pmb_mysql_fetch_object($res)){
					$result[]=$row;
					$nb_results++;
				}
			}
			//TODO : Classsement du tableau par pert...
		}
		
		$authority_title = $msg['detail_coll'];

		//construction des infos de l'autorité...
		$rqt= "select distinct source_id from ".$this->table_tempo." join  external_count on notice_id = rid";
		$resultat = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($resultat)){
			while($r = pmb_mysql_fetch_object($resultat)){
				$rqt = "select ufield,field_order,recid from entrepot_source_".$r->source_id." join ".$this->table_tempo." where recid=notice_id  and ufield like '225' and value like '$ext_value' group by ufield,field_order order by recid,field_order";
				$result_sql = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($result_sql)){
					while($col = pmb_mysql_fetch_object($result_sql)){
						$rqt = "select ufield,field_order,usubfield,value from entrepot_source_".$r->source_id." where recid='".$col->recid."' and ((ufield = '".$col->ufield."' and field_order = '".$col->field_order."') or ufield = '210' or ufield = '410' or ufield = '411') group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
						$plop = pmb_mysql_query($rqt);
						if(pmb_mysql_num_rows($plop)){
							while($elem = pmb_mysql_fetch_object($plop)){
								switch($elem->ufield){
									case "410" :
										$coll_name = $elem->value;
										break;
									case "411" :
										$subcoll_name = $elem->value;
										break;
									case "225" :
										switch($elem->usubfield){
											case "a" :
												$coll_name = $elem->value;
												break;
											case "i" :
												$subcoll_name = $elem->value;
												break;
											case "x" :
												$issn = $elem->value;
												break;												
										}
										break;
									case "210" :
										switch($elem->usubfield){
											case "c" :
												$publisher = $elem->value;
												break;
										}								
										break;
								}
								
							}
						}
					}
					//si on a trouvé un résultat, on insiste pas...
					break;
				}
			}
		}
		$this->results ="
		<div id='aut_details'>
			<h3>".htmlentities($authority_title,ENT_QUOTES,$charset)."</h3>
			<div id='aut_see'>
				<h3>".sprintf($msg["subcollection_details_subcollection"],$subcoll_name)."</h3>
				<ul>
				  <li>".sprintf($msg["subcollection_details_author"],$publisher)."</li>
				  <li>".sprintf($msg["subcollection_details_collection"],$coll_name)."</li>";
		if($issn) $this->results .="
						<li>".sprintf($msg["subcollection_details_issn"],$issn)."</li>";
		$this->results .="				
				</ul>
			</div>
			<div id='aut_details_liste'>
			<h3>".htmlentities($nb_results,ENT_QUOTES,$charset)." ".$msg['affiliate_search_external_authority_results'].implode($msg['affiliate_search_external_results_and'],$this->affiliate_source_name)."</h3>";

		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
		$last_item = ($start_page+$opac_search_results_per_page) <= count($result) ? ($start_page+$opac_search_results_per_page) : count($result);
    	for($i=$start_page ; $i<$last_item ; $i++){
			$this->results.= aff_notice_unimarc($result[$i]->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.= $this->getNavigationBar($nb_results);
		return $this->results;	
	}   
	
	public function getNoticesResults() {
    	global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;						
						
    	global $count;
    	global $add_cart_link;    	
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}
    	
    	$requete = "select * from ".$this->table_tempo;
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		$resultat=pmb_mysql_query($requete,$dbh);

    	$this->results ="

		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
		";

		$this->results.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titles_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
		$this->results.=suggest::get_add_link();
		flush();
		
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
    	while ($r=pmb_mysql_fetch_object($resultat)) {
			$this->results.= aff_notice_unimarc($r->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		//$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar();
		return $this->results;
    }    

	public function fetch_authorities() {
		global $dbh ;
	
		$auteurs = array() ;

		$rqt = "select * from ".$this->table_tempo." where infos like '225|%|%|i%' or infos like '411|%|%|t%'";
		$res_sql=pmb_mysql_query($rqt);
		$subcollections=array();
		while($row= pmb_mysql_fetch_object($res_sql)){
			$infos = explode("|",$row->infos);
			if(!in_array($infos[5],$subcollections))
				$subcollections[]=$infos[5];
		}
		if(!is_array($this->nb_results)) $this->nb_results = array();
		$this->nb_results['total'] = count($subcollections);
		return $subcollections;
	} // fin fetch_subcollections	
}
class affiliate_search_titre_uniforme extends affiliate_search_authorities {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "titre_uniforme";
		parent::__construct($user_query,$search_type);
	} 
    
   public function getAuthoritiesResults(){
    	global $msg;
    	global $opac_search_results_per_page;
    	global $page;
    	    	
    	if($this->table_tempo){
 			if(!$this->authoritiesResult){
		    	$this->authoritiesResult ="
				<div id='resultatrech_container'>
					<div id='resultatrech_see'>";


				$this->authoritiesResult.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titres_uniformes_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
				$this->authoritiesResult.= "
					</div>
					<div id='resultatrech_liste'>
						<ul>";	
				$tus = $this->fetch_tu();

				if(!$page) $page = 1; 
	    		$start_page=$opac_search_results_per_page*($page-1);
	    		$last_item = ($start_page+$opac_search_results_per_page) <= count($tus) ? ($start_page+$opac_search_results_per_page) : count($tus);
	    		for($i = $start_page ; $i<$last_item ; $i++){
	    			$this->authoritiesResult.= "
					<li class='categ_colonne'><span class='notice_fort'><a href='#'  onclick='document.form_values.action=\"./index.php?lvl=external_authorities&type=titre_uniforme&ext_value=".urlencode($tus[$i])."\";document.form_values.submit();return false;'>".$tus[$i]."</a></span></li>";
	    		}												

		    	$this->authoritiesResult.= "
						</ul>
		    		</div>
				</div>";
				//gestion de la pagination...
				$this->authoritiesResult.= $this->getNavigationBar();
 			}
    	}else{
    		global $search;
    		$this->makeSearch();
    		$this->getAuthoritiesResults();
    	}
    	return $this->authoritiesResult;
    }   
    
 	public function getNoticesAuthorityResults(){
		global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $fonction_auteur;					
						
    	global $count;
    	global $add_cart_link;    	
    	
    	global $ext_value;
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}	
    	
    	$joins = array();
		foreach($this->affiliate_source as $aff_source){
			$joins[] = "join entrepot_source_$aff_source on recid = notice_id and value like '$ext_value'";
		}		
		if(count($joins)){
			$result = array();
			$nb_results=0;
			foreach($joins as $join){
				$res =pmb_mysql_query("select distinct notice_id, pert from ".$this->table_tempo." ".$join);
				while($row = pmb_mysql_fetch_object($res)){
					$result[]=$row;
					$nb_results++;
				}
			}
			//TODO : Classsement du tableau par pert...
		}
		
		$authority_title = $msg['titre_uniforme_see_title'];

		//construction des infos de l'autorité...
		$rqt= "select distinct source_id from ".$this->table_tempo." join  external_count on notice_id = rid";
		$resultat = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($resultat)){
			while($r = pmb_mysql_fetch_object($resultat)){
				$rqt = "select ufield,field_order,recid from entrepot_source_".$r->source_id." join ".$this->table_tempo." where recid=notice_id  and ufield like '500' and value like '$ext_value' group by ufield,field_order order by recid,field_order";
				$result_sql = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($result_sql)){
					while($col = pmb_mysql_fetch_object($result_sql)){
						$rqt = "select ufield,field_order,usubfield,value from entrepot_source_".$r->source_id." where recid='".$col->recid."' and ufield = '500' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
						$plop = pmb_mysql_query($rqt);
						if(pmb_mysql_num_rows($plop)){
							while($elem = pmb_mysql_fetch_object($plop)){
								switch($elem->usubfield){
									case "a" :
										$tu = $elem->value;
										break;
								}								
							}
						}
					}
					//si on a trouvé un résultat, on insiste pas...
					break;
				}
			}
		}
		$this->results ="
		<div id='aut_details'>
			<h3>".htmlentities($authority_title,ENT_QUOTES,$charset)."</h3>
			<div id='aut_see'>
				<div class='collectionlevel2'>
					<h3>".sprintf($msg["titre_uniforme_detail"],$tu)."</h3>
				</div>
			</div>
			<div id='aut_details_liste'>
			<h3>".htmlentities($nb_results,ENT_QUOTES,$charset)." ".$msg['affiliate_search_external_authority_results'].implode($msg['affiliate_search_external_results_and'],$this->affiliate_source_name)."</h3>";

		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
		$last_item = ($start_page+$opac_search_results_per_page) <= count($result) ? ($start_page+$opac_search_results_per_page) : count($result);
    	for($i=$start_page ; $i<$last_item ; $i++){
			$this->results.= aff_notice_unimarc($result[$i]->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar($nb_results);
		return $this->results;	
	}   
	
	public function getNoticesResults() {
    	global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;						
						
    	global $count;
    	global $add_cart_link;    	
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}
    	
    	$requete = "select * from ".$this->table_tempo;
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		$resultat=pmb_mysql_query($requete,$dbh);

    	$this->results ="

		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
		";

		$this->results.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titles_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
		$this->results.=suggest::get_add_link();
		flush();
		
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
    	while ($r=pmb_mysql_fetch_object($resultat)) {
			$this->results.= aff_notice_unimarc($r->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		//$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar();
		return $this->results;
    }    

	public function fetch_authorities() {
		global $dbh ;
		global $opac_url_base ;
	
		$auteurs = array() ;

		$rqt = "select * from ".$this->table_tempo." where infos like '500%'";
		
		$res_sql=pmb_mysql_query($rqt);
		$tu=array();
		while($row= pmb_mysql_fetch_object($res_sql)){
			$infos = explode("|",$row->infos);
			if(!in_array($infos[5],$tu))
				$tu[]=$infos[5];
		}
		if(!is_array($this->nb_results)) $this->nb_results = array();
		$this->nb_results['total'] = count($tu);
		return $tu;
	} // fin fetch_tu
}

class affiliate_search_extended extends affiliate_search {
//
	
	public function __construct($user_query=""){
		$tempsearch = unserialize(stripslashes($user_query));
		for($i = 0 ; $i<count($tempsearch['SEARCH']) ; $i++){
			if($tempsearch[$i]['OP'] == "AUTHORITY"){
				$tempsearch[$i]['OP'] = "BOOLEAN";
				$s = explode("_",$tempsearch[$i]['SEARCH']);
				$tempsearch[$i]['FIELD'][0] = $this->getAuthorityLabel($tempsearch[$i]['FIELD'][0],$this->authorities_extended_array[$s[1]]);
			}
		}
		$this->serialized = serialize($tempsearch);
		$this->search_type = "notices";
    	$this->fetch_sources();
    	$this->generateSearch();
    	
	} 
		
	public function generateSearch(){
		$this->external_search=new search("search_fields_unimarc");
		$this->external_search->unserialize_search($this->serialized);
		$this->addSources();
		global $search;
    }	

    public function makeSearch(){
    	global $search;	
		$this->external_search->remove_forbidden_fields();    	
		$this->table_tempo = $this->external_search->make_search();
    }
    
	public function getNoticesResults() {
    	global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;						
						
    	global $count;
    	global $add_cart_link;    	
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}
    	
    	$requete = "select * from ".$this->table_tempo;
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		$resultat=pmb_mysql_query($requete,$dbh);

    	$this->results ="

		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
		";

		$this->results.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titles_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.=suggest::get_add_link();
		$this->results.="&nbsp;&nbsp;";
		
		flush();
		
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		if($res && pmb_mysql_num_rows($res)){
			while ($row = pmb_mysql_fetch_array($res)) {
				$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
			}
		}	
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
		if($resultat && pmb_mysql_num_rows($resultat)){
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$this->results.= aff_notice_unimarc($r->notice_id, 0, $entrepots_localisations);
			}
		}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar();
		return $this->results;
    }
    
	public function getNbResults(){
    	if(!$this->nb_results){
    		if($this->table_tempo){
   				$requete="select count(1) from ".$this->table_tempo;
				$resultat=pmb_mysql_query($requete);
				$this->nb_results = @pmb_mysql_result($resultat,0,0); 
				if(!$this->nb_results) $this->nb_results = 0;
    		}else{
    			global $search;
    			$this->makeSearch();
    			$this->getNbResults();
    		}
    	}
    	return $this->nb_results;
    } 
}

class affiliate_search_concept extends affiliate_search_authorities {
	
	public function __construct($user_query="",$search_type="notices"){
		$this->type= "concept";
		parent::__construct($user_query,$search_type);
	} 
    
   public function getAuthoritiesResults(){
    	global $msg;
    	global $opac_search_results_per_page;
    	global $page;
    	    	
    	if($this->table_tempo){
 			if(!$this->authoritiesResult){
		    	$this->authoritiesResult ="
				<div id='resultatrech_container'>
					<div id='resultatrech_see'>";


				$this->authoritiesResult.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['concepts_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
				$this->authoritiesResult.= "
					</div>
					<div id='resultatrech_liste'>
						<ul>";	
				$concept = $this->fetch_concepts();

				if(!$page) $page = 1; 
	    		$start_page=$opac_search_results_per_page*($page-1);
	    		$last_item = ($start_page+$opac_search_results_per_page) <= count($concept) ? ($start_page+$opac_search_results_per_page) : count($concept);
	    		for($i = $start_page ; $i<$last_item ; $i++){
	    			$this->authoritiesResult.= "
					<li class='concept_colonne'><span class='notice_fort'><a href='#'  onclick='document.form_values.action=\"./index.php?lvl=external_authorities&type=".$this->type."&ext_value=".urlencode($concept[$i])."\";document.form_values.submit();return false;'>".$concept[$i]."</a></span></li>";
	    		}												

		    	$this->authoritiesResult.= "
						</ul>
		    		</div>
				</div>";
				//gestion de la pagination...
				$this->authoritiesResult.=  $this->getNavigationBar();
 			}
    	}else{
    		global $search;
    		$this->makeSearch();
    		$this->getAuthoritiesResults();
    	}
    	return $this->authoritiesResult;
    }   
    
 	public function getNoticesAuthorityResults(){
		global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $fonction_auteur;					
						
    	global $count;
    	global $add_cart_link;    	
    	
    	global $ext_value;
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}	
    	
    	$joins = array();
		foreach($this->affiliate_source as $aff_source){
			$joins[] = "join entrepot_source_$aff_source on recid = notice_id and value like '$ext_value'";
		}		
		if(count($joins)){
			$result = array();
			$nb_results=0;
			foreach($joins as $join){
				$res =pmb_mysql_query("select distinct notice_id, pert from ".$this->table_tempo." ".$join);
				while($row = pmb_mysql_fetch_object($res)){
					$result[]=$row;
					$nb_results++;
				}
			}
			//TODO : Classsement du tableau par pert...
		}
		
		$authority_title = $msg['skos_view_concepts_concepts'];

		//construction des infos de l'autorité...
		$rqt= "select distinct source_id from ".$this->table_tempo." join  external_count on notice_id = rid";
		$resultat = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($resultat)){
			while($r = pmb_mysql_fetch_object($resultat)){
				$rqt = "select ufield,field_order,recid from entrepot_source_".$r->source_id." join ".$this->table_tempo." where recid=notice_id  and ufield like '606' and value like '$ext_value' group by ufield,field_order order by recid,field_order";
				$result_sql = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($result_sql)){
					while($col = pmb_mysql_fetch_object($result_sql)){
						$rqt = "select ufield,field_order,usubfield,value from entrepot_source_".$r->source_id." where recid='".$col->recid."' and ((ufield = '".$col->ufield."' and field_order = '".$col->field_order."')) group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
						$plop = pmb_mysql_query($rqt);
						if(pmb_mysql_num_rows($plop)){
							while($elem = pmb_mysql_fetch_object($plop)){
								switch($elem->usubfield){
									case "a" :
										$concept_lib = $elem->value;
										
										break;
								}								
							}
						}
					}
					//si on a trouvé un résultat, on insiste pas...
					break;
				}
			}
		}
		$this->results ="
		<div id='aut_details'>
			<h3>".htmlentities($authority_title,ENT_QUOTES,$charset)."</h3>
			<div id='aut_see'>
				<div class='categorylevel2'>
					<h3>".$msg['skos_concept']." ".htmlentities($concept_lib,ENT_QUOTES,$charset)."</h3>
				</div>
			</div>
			<div id='aut_details_liste'>
			<h3>".htmlentities($nb_results,ENT_QUOTES,$charset)." ".$msg['affiliate_search_external_authority_results'].implode($msg['affiliate_search_external_results_and'],$this->affiliate_source_name)."</h3>";

		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
		$last_item = ($start_page+$opac_search_results_per_page) <= count($result) ? ($start_page+$opac_search_results_per_page) : count($result);
    	for($i=$start_page ; $i<$last_item ; $i++){
			$this->results.= aff_notice_unimarc($result[$i]->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar($nb_results);
		return $this->results;	
	}   
	
	public function getNoticesResults() {
    	global $dbh;
    	global $begin_result_liste;
    	global $opac_notices_depliable;
    	global $opac_search_results_per_page;
    	$nb_per_page_search = $opac_search_results_per_page;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;						
						
    	global $count;
    	global $add_cart_link;    	
    	if(!$page) $page = 1; 
    	$start_page=$nb_per_page_search*($page-1);
    	
    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		return;
    	}

    	if(!$this->table_tempo){
    		global $search;
    		$this->makeSearch();
    	}
    	
    	$requete = "select * from ".$this->table_tempo;
		$requete .= " limit ".$start_page.",".$nb_per_page_search;
		$resultat=pmb_mysql_query($requete,$dbh);

    	$this->results ="

		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">
		";

		$this->results.= pmb_bidi("<h3>".$this->getTotalNbResults()." ".$msg['titles_found']." ".$this->external_search->make_human_query().activation_surlignage()."</h3>");
		$this->results.=suggest::get_add_link();
		flush();
		
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]); 
		}	
		
		if ($opac_notices_depliable) $this->results.= $begin_result_liste;
		
		$this->results.= $add_cart_link;
		
		$this->results.= "	</div>\n
		<div id=\"resultatrech_liste\">";
		$this->results.= "<blockquote>";
    	while ($r=pmb_mysql_fetch_object($resultat)) {
			$this->results.= aff_notice_unimarc($r->notice_id, 0, $entrepots_localisations);
    	}
    	$this->results.= "</blockquote>";   
    	$this->results.= "</div>
		</div>";
		//on a besoin d'un formulaire pour reposter la recherche
		//$this->results.= $this->make_hidden_search_form();
		//gestion de la pagination...
		$this->results.=  $this->getNavigationBar();
		return $this->results;
    }    

	public function fetch_authorities() {
		global $dbh ;
		global $opac_url_base ;
	
		$rqt = "select * from ".$this->table_tempo." where infos like '606%'";
		
		$res_sql=pmb_mysql_query($rqt);
		$concept=array();
		while($row= pmb_mysql_fetch_object($res_sql)){
			$infos = explode("|",$row->infos);
			if(!in_array($infos[5],$concept))
				$concept[]=$infos[5];
		}
		if(!is_array($this->nb_results)) $this->nb_results = array();		
		$this->nb_results['total'] = count($concept);
		return $concept;
	} // fin fetch_concepts
}
?>