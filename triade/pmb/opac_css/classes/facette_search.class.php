<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette_search.class.php,v 1.100 2019-05-16 12:54:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/includes/notice_affichage.inc.php");
require_once($class_path."/acces.class.php");
require_once($class_path."/suggest.class.php");
require_once($class_path."/facettes_root.class.php");
require_once($class_path."/notice.class.php");

class facettes extends facettes_root {
	
	/**
	 * Nom de la table bdd
	 * @var string
	 */
	public static $table_name = 'facettes';
	
	/**
	 * Mode d'affichage (extended/external)
	 * @var string
	 */
	public $mode = 'extended';
	
	/**
	 * Nom de la classe de comparaison
	 */
	protected static $compare_class_name = 'facette_search_compare';
	
	public function __construct($objects_ids = ''){
		parent::__construct($objects_ids);
	}
		
	protected function get_query_by_facette($id_critere, $id_ss_critere, $type = "notices") {
		global $lang;
	
		if($type == 'notices'){
			$plural_prefix = 'notices';
			$prefix = 'notice';
		}else{
			$plural_prefix = 'authorities';
			$prefix = 'authority';
		}
		
		$query = 'select value ,count(distinct id_'.$prefix.') as nb_result from (SELECT value,id_'.$prefix.' FROM '.$plural_prefix.'_fields_global_index'.
					gen_where_in($plural_prefix.'_fields_global_index.id_'.$prefix, $this->objects_ids).'
					AND code_champ = '.($id_critere+0).'
					AND code_ss_champ = '.($id_ss_critere+0).'
					AND lang in ("","'.$lang.'","'.substr($lang,0,2).'")) as sub
					GROUP BY value
					ORDER BY ';
		return $query;
	}
		
	public static function do_level1() {
		global $msg,$mode,$autolevel1,$opac_autolevel2,$tab,$charset;
		global $lvl, $id;
		$table="";
		if (($_SESSION["level1"])&&(!$autolevel1)&&($tab!="affiliate")) {
			$table.="<h3>".htmlentities($msg['autolevel1_search'],ENT_QUOTES,$charset)."</h3>\n<table id='lvl1_list'>";
			$n=0;
			foreach($_SESSION["level1"] as $mod_search=>$level) {
				$current=false;
				switch ($mod_search) {
					case "abstract":
						$form_name="search_abstract";
						$lvl_msg=$msg["abstract"];
						if ($mode=="abstract") $current=true;
						break;
					case "author":
						$form_name="search_authors";
						$lvl_msg=$msg["authors"];
						if ($mode=="auteur") $current=true;
						break;
					case "category":
						$form_name="search_categorie";
						$lvl_msg=$msg["categories"];
						if ($mode=="categorie") $current=true;
						break;
					case "collection":
						$form_name="search_collection";
						$lvl_msg=$msg["collections"];
						if ($mode=="collection") $current=true;
						break;
					case "docnum":
						$form_name="search_docnum";
						$lvl_msg=$msg["docnum"];
						if ($mode=="docnum") $current=true;
						break;
					case "indexint":
						$form_name="search_indexint";
						$lvl_msg=$msg["indexint"];
						if ($mode=="indexint") $current=true;
						break;
					case "keywords":
						$form_name="search_keywords";
						$lvl_msg=$msg["keywords"];
						if ($mode=="keyword") $current=true;
						break;
					case "publisher":
						$form_name="search_publishers";
						$lvl_msg=$msg["publishers"];
						if ($mode=="editeur") $current=true;
						break;
					case "subcollection":
						$form_name="search_sub_collection";
						$lvl_msg=$msg["subcollections"];
						if ($mode=="souscollection") $current=true;
						break;
					case "title":
						$form_name="search_objects";
						$lvl_msg=$msg["titles"];
						if (($mode=="titre")||($mode=="title")) $current=true;
						break;
					case "titre_uniforme":
						$form_name="search_titres_uniformes";
						$lvl_msg=$msg["titres_uniformes"];
						if ($mode=="titre_uniforme") $current=true;
						break;
					case "tous":
						$form_name="search_tous";
						$lvl_msg=$msg["tous"];
						if ($mode=="tous") $current=true;
						break;
					case "concept":
						$form_name="search_concepts";
						$lvl_msg=$msg["concepts_search"];
						if ($mode=="concept") $current=true;
						break;
					default:
						if(substr($mod_search, 0,10) == "authperso_"){
							$form_name="search_".$mod_search;
							$lvl_msg=$level['name'];
							if ($mode==$mod_search) $current=true;
						}
						break;
				}
				if ($n % 2) $pair_impair = "odd"; else $pair_impair = "even";
				$td_javascript=" ";
		        $tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
				$table.="<tr class='$pair_impair' $tr_surbrillance>
							<td>".$level["form"].($current?"<span class='current'>":"<a href='javascript:document.forms[\"$form_name\"].submit()'>")."$lvl_msg (".$level["count"].")".($current?"</span>":"</a>")."</td>
						</tr>";
				$n++;
			} 
			$table.="</table>";
		} else {
			if (($opac_autolevel2)&&($autolevel1)&&($tab!="affiliate")) {
				//Génération du post et du get... 
				//Attention tous ce qui passe par ajax.php doit être en utf-8
				$table="<script>";
				$to_submit="";
				foreach ($_POST as $key=>$val) {//Attention si on a un tableau de tableau c'est mort
					if (!is_array($val)) {
						$to_submit.=($to_submit?"&":"").rawurlencode(($charset == "utf-8")?$key:utf8_encode($key))."=".rawurlencode(($charset == "utf-8")?$val:utf8_encode($val));
					} else {
						foreach($val as $subkey=>$subval) {
							$to_submit.=($to_submit?"&":"").rawurlencode((($charset == "utf-8")?$key:utf8_encode($key))."[".addslashes(($charset == "utf-8")?$subkey:utf8_encode($subkey))."]")."=".rawurlencode(($charset == "utf-8")?$subval:utf8_encode($subval));
						}
					}
				}
				foreach ($_GET as $key=>$val) {//Attention si on a un tableau de tableau c'est mort
					if (!is_array($val)) {
						$to_submit.=($to_submit?"&":"").rawurlencode(($charset == "utf-8")?$key:utf8_encode($key))."=".rawurlencode(($charset == "utf-8")?$val:utf8_encode($val));
					} else {
						foreach($val as $subkey=>$subval) {
							$to_submit.=($to_submit?"&":"").rawurlencode((($charset == "utf-8")?$key:utf8_encode($key))."[".addslashes(($charset == "utf-8")?$subkey:utf8_encode($subkey))."]")."=".rawurlencode(($charset == "utf-8")?$subval:utf8_encode($subval));
						}
					}
				}
				$table.="var tosubmit=\"".$to_submit."\";
						var cms_build_activate=\"".($_SESSION["cms_build_activate"] ? $_SESSION["cms_build_activate"] : 0)."\";" .
						"function updateLevel1(result) {
							if(result != '') {
								document.getElementById('lvl1').innerHTML=result;
							} else {
								if(!cms_build_activate) {
									require(['dojo/ready', 'dojo/dom-construct'], function(ready, domConstruct){
										ready(function(){
											domConstruct.destroy('lvl1');
										});
									});
								}
							}
						}
						getlevel2=new http_request();
						getlevel2.request('./ajax.php?module=ajax&categ=level1".($lvl == 'search_segment' ? '&segment_id='.$id : '')."',true,tosubmit,true,updateLevel1);";
				$table.="</script>";
				$table.="<h3>".htmlentities($msg['autolevel1_search'],ENT_QUOTES,$charset)."</h3>\n" .
						"<img src='".get_url_icon('patience.gif')."' id='wait_level1'/>";
			}
		}
		return $table;
	}
	
	public static function get_facette_wrapper(){
		$script = parent::get_facette_wrapper();
		$script .= "
		<script type='text/javascript'>
			function valid_facettes_multi(){
				//on bloque si aucune case cochée
				var form = document.facettes_multi;
				for (i=0, n=form.elements.length; i<n; i++){
					if ((form.elements[i].checked == true)) {
						if(document.getElementById('filtre_compare_facette')) {
							document.getElementById('filtre_compare_facette').value='filter';
						}
						if(document.getElementById('filtre_compare_form_values')) {
							document.getElementById('filtre_compare_form_values').value='filter';
						}
						form.submit();
						return true;
					}
				}
				return false;
			}
		</script>";
		return $script;
	}
	
	public static function make_facette_search_env() {
		global $search;
		global $op_0_s_1;
		global $field_0_s_1;
		
		//historique des recherches
		if(empty($search)) {
			$search = array();
		}
		$search[] = "s_1";
		$op_0_s_1 = "EQ";
		$field_0_s_1[] = $_SESSION['last_query']+0; 
		
		//creation des globales => parametres de recherche
		if ($_SESSION['facette']) {
			for ($i=0;$i<count($_SESSION['facette']);$i++) {
				$search[] = "s_3";
		    	$field = "field_".($i+1)."_s_3";
		    	$field_=array();
    			$field_ = $_SESSION['facette'][$i];
    			global ${$field};
    			${$field} = $field_;
    			
		    	$op = "op_".($i+1)."_s_3";
		    	$op_ = "EQ";
    			global ${$op};
    			${$op}=$op_;
    		    
    			$inter = "inter_".($i+1)."_s_3";
    			$inter_ = "and";
    			global ${$inter};
    			${$inter} = $inter_;
			}
		}
	}
	
	protected static function get_link_delete_clicked($id, $facettes_nb_applied) {
		if ($facettes_nb_applied==1) {
			$link = "document.location=\"".static::format_url('lvl=more_results&get_last_query=1&reinit_facette=1')."\";";
		} else {
			$link = "document.location=\"".static::format_url('lvl=more_results&mode=extended&facette_test=1&param_delete_facette='.$id)."\";";
		}
		return $link;
	}
	
	protected static function get_link_not_clicked($name, $label, $code_champ, $code_ss_champ, $id, $nb_result) {
		$link =  "document.location=\"".static::format_url("lvl=more_results&mode=extended&facette_test=1");
		$link .= "&name=".rawurlencode($name)."&value=".rawurlencode($label)."&champ=".$code_champ."&ss_champ=".$code_ss_champ."\";";
		return $link;
	}
	
	protected static function get_link_reinit_facettes() {
		$link =  "document.location=\"".static::format_url("lvl=more_results&get_last_query=1&reinit_facette=1")."\";";
		return $link;
	}
	
	protected static function get_link_back($reinit_compare=false) {
		$link =  "document.location.href=\"".static::format_url("lvl=more_results&get_last_query=1".($reinit_compare ? "&reinit_compare=1" : ""))."\"";
		return $link;
	}
	
	public static function get_session_values() {
		if(!isset($_SESSION['facette'])) $_SESSION['facette'] = array();
		return $_SESSION['facette'];
	}
	
	public static function set_session_values($session_values) {
		$_SESSION['facette'] = $session_values;
	}
	
	public static function delete_session_value($param_delete_facette) {
		if(isset($_SESSION['facette'][$param_delete_facette])){
			unset($_SESSION['facette'][$param_delete_facette]);
			$_SESSION['facette'] = array_values($_SESSION['facette']);
		}
	}
	
	public static function make_facette_suggest($id_notice_array){
		global $opac_modules_search_title,$opac_modules_search_author,$opac_modules_search_publisher,$opac_modules_search_titre_uniforme;
		global $opac_modules_search_collection,$opac_modules_search_subcollection,$opac_modules_search_category,$opac_modules_search_indexint;
		global $opac_modules_search_keywords,$opac_modules_search_abstract,$opac_modules_search_concept,$opac_modules_search_docnum;
		global $msg,$user_query,$opac_autolevel2;
		
		$suggestion = new suggest($user_query);
		
		if ($opac_autolevel2==2) {
			$action = static::format_url("lvl=more_results&autolevel1=1");
		} else {
			$action = static::format_url("lvl=search_result&search_type_asked=simple_search");
		}
		if ($opac_modules_search_title==2) $look["look_TITLE"]=1;
		if ($opac_modules_search_author==2) $look["look_AUTHOR"]=1 ;
		if ($opac_modules_search_publisher==2) $look["look_PUBLISHER"] = 1 ; 
		if ($opac_modules_search_titre_uniforme==2) $look["look_TITRE_UNIFORME"] = 1 ; 
		if ($opac_modules_search_collection==2) $look["look_COLLECTION"] = 1 ;	
		if ($opac_modules_search_subcollection==2) $look["look_SUBCOLLECTION"] = 1 ;
		if ($opac_modules_search_category==2) $look["look_CATEGORY"] = 1 ;
		if ($opac_modules_search_indexint==2) $look["look_INDEXINT"] = 1 ;
		if ($opac_modules_search_keywords==2) $look["look_KEYWORDS"] = 1 ;
		if ($opac_modules_search_abstract==2) $look["look_ABSTRACT"] = 1 ;
		if ($opac_modules_search_concept==2) $look["look_CONCEPT"] = 1;
		$look["look_ALL"] = 1 ;
		if ($opac_modules_search_docnum==2) $look["look_DOCNUM"] = 1;
		foreach($look as $looktype=>$lookflag) {
			 $action.="&".$looktype."=1"; 
		}
		$table_facette_suggest ="<table class='facette_suggest'><tbody>";
		
		//on recrée un tableau pour regrouper les éventuels doublons
		$tmpArray = array();
		$tmpArray = $suggestion->listUniqueSimilars();
		
		if (count($tmpArray)) {
			foreach($tmpArray as $word){
				$table_facette_suggest.="<tr>
					<td>
						<a href='".$action."&user_query=".rawurlencode($word)."'>
							<span class='facette_libelle'>".$word."</span>
						</a>
					</td>
				</tr>";
			}
		}
		$table_facette_suggest.="</tbody></table>";
		
		if (count($tmpArray)) {
			$table = "<div id='facette_suggest'><h3>".$msg['facette_suggest']."</h3>".$table_facette_suggest."</div>";
		} else {
			$table = "";
		}
		
		return $table;
	}
	
	public static function expl_voisin($id_notice=0){
		global $charset,$msg;
		$data=array();
		$notices_list = facettes::get_expl_voisin($id_notice);
		$display=static::aff_notices_list($notices_list);
		$data['aff']="";
		if($display)$data['aff']= "<h3 class='avis_detail'>".$msg['expl_voisin_search']."</h3>".$display;
		if ($charset!="utf-8") $data['aff']= utf8_encode($data['aff']);
		$data['id']=$id_notice;
		return $data;
	}	
		
	public static function get_expl_voisin($id_notice=0){
		global $dbh;
		global $opac_nb_notices_similaires;
		
		$id_notice+=0;
		$notice_list=array();	
		$req = "select expl_cote from exemplaires where expl_notice=$id_notice";
		$res = @pmb_mysql_query($req,$dbh);
		
		$nb_result = $opac_nb_notices_similaires;
		if($nb_result>6 || $nb_result<0 || !(isset($opac_nb_notices_similaires))){
			$nb_result=6;
		}
		$nb_asc="";
		$nb_desc="";
		if(($nb_result%2)==0){
			$nb_asc = $nb_result/2;
			$nb_desc = $nb_asc;
		} else {
			$nb_desc = $nb_result%2;
			$nb_asc = $nb_result-$nb_desc;
		}		
		
		if($res && pmb_mysql_num_rows($res)){
			$r=pmb_mysql_fetch_object($res);
			$cote=$r->expl_cote;			
			$query = "
			(select distinct expl_notice from exemplaires where expl_notice!=0 and expl_cote >= '".$cote."' and expl_notice!=$id_notice order by expl_cote asc limit ".$nb_asc.")
				union 
			(select distinct expl_notice from exemplaires where expl_notice!=0 and expl_cote < '".$cote."' and expl_notice!=$id_notice  order by expl_cote desc limit ".$nb_desc.")" ;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result) > 0){				
				while($row = pmb_mysql_fetch_object($result)){
					$notice_list[] = $row->expl_notice;
				}
			}			
		}	
		return $notice_list;
	}	
			
	
	public static function similitude($id_notice=0){
		global $charset,$msg;
		$data=array();
		$notices_list = facettes::get_similitude_notice($id_notice);
		$display= static::aff_notices_list($notices_list);		
		$data['aff']="";
		if($display)$data['aff']= "<h3 class='avis_detail'>".$msg['simili_search']."</h3>".$display;
		if ($charset!="utf-8") $data['aff']= utf8_encode($data['aff']);
		$data['id']=$id_notice;
		return $data;
	}
	
	public static function get_similitude_notice($id_notice=0){
		global $dbh;
		global $opac_nb_notices_similaires;
		global $gestion_acces_active,$gestion_acces_empr_notice;
		
		$id_notice+=0;		
		$req="select distinct code_champ, code_ss_champ, num_word from notices_mots_global_index 
				".gen_where_in('code_champ', '1,17,19,20,25')."
						and	id_notice=".$id_notice;
				
		$res=pmb_mysql_query($req,$dbh);
		$where_mots="";
		$notice_list=array();
		if($res && pmb_mysql_num_rows($res)){
			while($r=pmb_mysql_fetch_object($res)){
				if($where_mots)$where_mots.=" or ";
				$where_mots.="(code_champ =".$r->code_champ." AND code_ss_champ =".$r->code_ss_champ." AND num_word =".$r->num_word." and id_notice != ".$id_notice.")";
			}
		}
		if($where_mots){
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
			}
			$nb_result = $opac_nb_notices_similaires;
			if($nb_result>6 || $nb_result<0 || !(isset($opac_nb_notices_similaires))){
				$nb_result=6;
			}
			$req = "select id_notice, sum(pond) as s from notices_mots_global_index where $where_mots group by id_notice order by s desc limit ".$nb_result;
			$res = @pmb_mysql_query($req,$dbh);		
			if($res && pmb_mysql_num_rows($res)){
				while($r=pmb_mysql_fetch_object($res)){
					if($r->s >80){
						$acces_v=TRUE;
						if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
							$acces_v = $dom_2->getRights($_SESSION['id_empr_session'],$r->id_notice,4);
						} else {
							$requete = "SELECT notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$r->id_notice."' and id_notice_statut=statut ";
							$myQuery = pmb_mysql_query($requete, $dbh);
							if($myQuery && pmb_mysql_num_rows($myQuery)) {
								$statut_temp = pmb_mysql_fetch_object($myQuery);
								if(!$statut_temp->notice_visible_opac)	$acces_v=FALSE;
								if($statut_temp->notice_visible_opac_abon && !$_SESSION['id_empr_session'])	$acces_v=FALSE;
							} else 	$acces_v=FALSE;
						}
						if($acces_v){
							$notice_list[] = $r->id_notice;
						}
					}
				}
			}
		}
		return $notice_list;
	}
	
	protected static function aff_notices_list($notices_list){
		global $dbh,$charset;
		global $opac_show_book_pics,$opac_book_pics_url,$opac_book_pics_msg,$opac_url_base;
		global $opac_notice_affichage_class,$gestion_acces_active,$gestion_acces_empr_notice;
		global $opac_notice_reduit_format_similaire ;
		
		$img_list = "";
		$title_list = "";
		
		$tabNotice = array();
		
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {				
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
		}
		$i = 0;
		foreach($notices_list as $notice_id){		
			$acces_v=TRUE;	
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {	
				$acces_v = $dom_2->getRights($_SESSION['id_empr_session'],$notice_id,4);
			} else {
				$requete = "SELECT notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$notice_id."' and id_notice_statut=statut ";
				$myQuery = pmb_mysql_query($requete, $dbh);
				if($myQuery && pmb_mysql_num_rows($myQuery)) {
					$statut_temp = pmb_mysql_fetch_object($myQuery);
					if(!$statut_temp->notice_visible_opac)	$acces_v=FALSE;
					if($statut_temp->notice_visible_opac_abon && !$_SESSION['id_empr_session'])	$acces_v=FALSE;
				} else 	$acces_v=FALSE;
			}
			if(!$acces_v) continue;
			
			$req = "select * from notices where notice_id=$notice_id";			
			$res = @pmb_mysql_query($req,$dbh);
			$image = "";
			if($r=pmb_mysql_fetch_object($res)){
				if (substr($opac_notice_reduit_format_similaire,0,1)!="H" && $opac_show_book_pics=='1') {
					if (($r->code && $opac_book_pics_url) || $r->thumbnail_url) {
						$url_image_ok = getimage_url($r->code, $r->thumbnail_url);
						$title_image_ok = "";
						if(!$r->thumbnail_url) {
							$title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
						}
						if(!trim($title_image_ok)){
							$title_image_ok = htmlentities($r->tit1, ENT_QUOTES, $charset);
						}
						$image = "<a href='".$opac_url_base."index.php?lvl=notice_display&id=".$notice_id."'>"."<img class='vignetteimg_simili' src='".$url_image_ok."' title=\"".$title_image_ok."\" hspace='4' vspace='2'>"."</a>";
					} else {
						$image = "<a href='".$opac_url_base."index.php?lvl=notice_display&id=".$notice_id."'>"."<img class='vignetteimg_simili' src='".notice::get_picture_url_no_image($r->niveau_biblio, $r->typdoc)."' hspace='4' vspace='2'></a>";
					}
				}		
				$notice = new $opac_notice_affichage_class($notice_id, "", 0,0,1);	
				$notice->do_header_similaire();				
				$notice_header= "<a href='".$opac_url_base."index.php?lvl=notice_display&id=".$notice_id."'>".$notice->notice_header."</a>";		
				$i++;
			}			
			
			// affichage du titre et de l'image dans la même cellule
			if($image!=""){
				$img_list.="<td class='center'>".$image."<br />".$notice_header."</td>";
			} else {
				$img_list.="<td class='center'>".$notice_header."</td>";
			}		
			
		}
		if(!$i)return"";		
		$display="<table style='width:100%;table-layout:fixed;'><tr>".$img_list."</tr></table>";		
		
		return $display;
	}
	
	/**
	 * Retourne le template de facettes
	 * @param string $query
	 */
	public static function get_display_list_from_query($query) {
		global $opac_facettes_ajax;
		
		$display = '';
		$objects = '';
		$result = pmb_mysql_query($query);
		if($result) {
			while($row = pmb_mysql_fetch_object($result)){
				if($objects){
					$objects.=",";
				}
				$objects.= $row->notice_id;
			}
		}
		if(!$opac_facettes_ajax){
			$display .= facettes::make_facette($objects);
		}else{
			$_SESSION['tab_result']=$objects;
			$display .= static::call_ajax_facettes();
		}
		//Formulaire "FACTICE" pour l'application du comparateur et du filtre multiple...
		if($display) {
			$display.= '
			<form name="form_values" style="display:none;" method="post" action="'.static::format_url('lvl=more_results&mode=extended').'">
				<input type="hidden" name="from_see" value="1" />
				'.facette_search_compare::form_write_facette_compare().'
			</form>';
		}
		return $display;
	}
	
	public static function get_formatted_value($id_critere, $id_ss_critere, $value) {
		//Aucun formatage nécessaire pour les facettes PMB (non externes).
		return get_msg_to_display($value);
	}
	
	public function get_query_expl($notices_ids) {
		global $opac_view_filter_class;
	
		$opac_view_filter_where = '';
		if($opac_view_filter_class){
			if(sizeof($opac_view_filter_class->params["nav_sections"])){
				$opac_view_filter_where=" AND idlocation in (". implode(",",$opac_view_filter_class->params["nav_sections"]).")";
			}else{
				return "";
			}
		}
		$commons_select = "
			SELECT exemplaires.expl_location AS id_location, notices.notice_id AS id_notice
			FROM exemplaires ";
	
		$commons_join = "
			JOIN docs_section ON exemplaires.expl_section=docs_section.idsection AND docs_section.section_visible_opac=1
			JOIN docs_statut ON exemplaires.expl_statut=docs_statut.idstatut AND statut_visible_opac=1
			JOIN docs_location ON exemplaires.expl_location=docs_location.idlocation AND docs_location.location_visible_opac=1
			JOIN notice_statut on notice_statut.id_notice_statut=notices.statut and notice_statut.expl_visible_opac=1
			".(!$_SESSION["user_code"] ? " and notice_statut.expl_visible_opac_abon=0 " : "")." ";
			
		$query =
		$commons_select."
			JOIN notices ON exemplaires.expl_notice = notices.notice_id and exemplaires.expl_bulletin= 0
			".$commons_join."
			".gen_where_in('notices.notice_id', $notices_ids). $opac_view_filter_where."
			UNION
			".$commons_select."
			JOIN bulletins ON exemplaires.expl_bulletin = bulletins.bulletin_id	and exemplaires.expl_notice= 0					
			JOIN notices ON bulletins.bulletin_notice = notices.notice_id
			".$commons_join."
			".gen_where_in('notices.notice_id', $notices_ids). $opac_view_filter_where."
			UNION
			".$commons_select."
			JOIN bulletins ON exemplaires.expl_bulletin = bulletins.bulletin_id and exemplaires.expl_notice= 0
			JOIN notices ON bulletins.num_notice = notices.notice_id
			".$commons_join."
			".gen_where_in('notices.notice_id', $notices_ids). $opac_view_filter_where;
	
		return $query;
	}
	
	public function get_query_explnum($notices_ids) {
		global $gestion_acces_active,$gestion_acces_empr_docnum;
	
		if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
			$ac= new acces();
			$dom_3= $ac->setDomain(3);
			$acces_j = $dom_3->getJoin($_SESSION['id_empr_session'],16,'explnum_id');
		} else {
			$acces_j = "
				JOIN explnum_statut ON explnum_docnum_statut=id_explnum_statut
				AND (
					(explnum_statut.explnum_visible_opac=1	AND explnum_statut.explnum_visible_opac_abon=0)"
					.($_SESSION["user_code"]?" or (explnum_statut.explnum_visible_opac_abon=1 and explnum_statut.explnum_visible_opac=1)":"")
					.")";
		}
		$query = "
			SELECT explnum_location.num_location AS id_location, explnum.explnum_notice AS id_notice
			FROM explnum_location
			JOIN explnum ON explnum_location.num_explnum = explnum.explnum_id AND explnum.explnum_bulletin = 0
			JOIN docs_location ON explnum_location.num_location = docs_location.idlocation AND docs_location.location_visible_opac=1
			" . $acces_j .
			gen_where_in('explnum.explnum_notice', $notices_ids)."
			UNION
			SELECT explnum_location.num_location AS id_location, bulletins.bulletin_notice AS id_notice
			FROM explnum_location
			JOIN explnum ON explnum_location.num_explnum = explnum.explnum_id AND explnum.explnum_notice = 0
			JOIN bulletins ON explnum.explnum_bulletin = bulletins.bulletin_id
			JOIN docs_location ON explnum_location.num_location = docs_location.idlocation AND docs_location.location_visible_opac=1
			" . $acces_j .
			gen_where_in('bulletins.bulletin_notice', $notices_ids)."
			UNION
			SELECT explnum_location.num_location AS id_location, bulletins.num_notice AS id_notice
			FROM explnum_location
			JOIN explnum ON explnum_location.num_explnum = explnum.explnum_id AND explnum.explnum_notice = 0
			JOIN bulletins ON explnum.explnum_bulletin = bulletins.bulletin_id
			JOIN docs_location ON explnum_location.num_location = docs_location.idlocation AND docs_location.location_visible_opac=1
			" . $acces_j .
			gen_where_in('bulletins.num_notice', $notices_ids);
		
		return $query;
	}
}// end class
