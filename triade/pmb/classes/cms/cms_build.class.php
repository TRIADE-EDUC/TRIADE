<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_build.class.php,v 1.57 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ("$include_path/cms/cms.inc.php");
require_once ("$include_path/templates/cms/cms_build.tpl.php");  
require_once($class_path."/autoloader.class.php");
require_once($class_path."/cms/cms_pages.class.php");
require_once("$class_path/param_subst.class.php");

$autoloader = new autoloader();
$autoloader->add_register("cms_modules",true);		
class cms_build{
	
	public $data = "";
	public $contener_list=array();
	public $cadre_list=array();
	public $objet_list=array();
	public $name_list=array();
	public $objets_att=array();
	public $versions=array();
	public $last_version_data=array();
	public $classement_list=array();
	public $pages_classement_list=array();
	public $pages;
	public $dom;
	public $id_version;
	public $cms_id;
	protected $nb_cadres_in_page = 0;
	protected $nb_cadres_not_in_page = 0;
	protected $nb_cadres_not_in_cms = 0;
	
	//Constructeur	 
	public function __construct($cms_id=0){
		global $include_path,$charset;
		@ini_set("zend.ze1_compatibility_mode", "0");
		$this->dom = new DomDocument();

		$this->dom->load("$include_path/cms/cms_build/cms_build_id.xml") ;
		$cms_objects=$this->dom->getElementsByTagName('cms_object');
		$this->contener_list=array();
		$this->cadre_list=array();
		$this->objet_list=array();
		$this->page_list=array();
		$this->name_list=array();
		$this->versions=array();
		$this->last_version_data=array();
		$this->cms_id=$cms_id;
		
		$rqt = "select * from cms ";			    		
		$res=pmb_mysql_query($rqt);					
    	if(!pmb_mysql_num_rows($res)){	
    		// on crée un cms par défaut
    		$this->id_version=$this->create_new_cms();
    	}
    	$this->get_versions_list();
		$this->id_version=$this->get_version_default();
		
		foreach ($cms_objects as $cms_object){    		
			$node_name=$cms_object->getAttribute('id');
    		if($cms_object->getAttribute('container')=='yes'&& $cms_object->getAttribute('receptable')=='yes'){
    			$this->contener_list[]=$node_name;    			
    		}elseif($cms_object->getAttribute('draggable')=='yes' && $cms_object->getAttribute('receptable')=='yes'){
    			// un cadre
    			$this->cadre_list[]=$node_name; 
    			if($cms_object->getAttribute('zone')=='yes') {
    				$this->zone_list[]=$node_name;    				
    			} 	
    		}elseif($cms_object->getAttribute('draggable')=='yes'){ 
    			// un objet déplacable dans un cadre ou le contener
    			$this->objet_list[]=$node_name; 	
    				
    		}	
    		$label=$cms_object->getAttribute('label');
    		if ($charset!="utf-8")  $label=utf8_decode($label);
    		$this->name_list[$cms_object->getAttribute('id')]=$label;    		
    		$this->objets_att[$node_name]['fixed']=0;    	
    		if($cms_object->getAttribute('fixed')=='yes'){
    		//	print $cms_object->getElementsByTagName('parent')->item(0);      
     			$this->objets_att[$node_name]['fixed']=1;    
	    		$rqt = "select * from cms_build where build_obj='".$node_name."' and build_version_num='".$this->id_version."' ";			    		
				$res=pmb_mysql_query($rqt);					
	    		if(!pmb_mysql_num_rows($res)){	
	    				    			
	    			$placement = array();    		
	    			if($cms_object->childNodes->length) {
			            foreach($cms_object->childNodes as $item) {
			                $placement[$item->nodeName] = $item->nodeValue;
			            }
			        } 
					$rqt_insert = "INSERT INTO cms_build SET 
						build_version_num='".$this->id_version."',
						build_obj='$node_name', 
						build_fixed='1' ,
						build_parent='".$placement['parent']."' ,
						build_child_before='".$placement['child_before']."' ,
						build_child_after='".$placement['child_after']."'
					";	
				//	print $rqt_insert."<br />";
					pmb_mysql_query($rqt_insert);
	    		}else{
	    			// cadre déjà memorisé, on conserve l'état fixed dans la base s'il a été modifié à la main 
	    			$row = pmb_mysql_fetch_object($res);
	    			$this->objets_att[$node_name]['fixed']=$row->build_fixed;
    			}		
    		}
		}
		$this->pages=new cms_pages();		
		$this->pages_classement_list=$this->pages->pages_classement_list;
		
		$this->parser= new cms_modules_parser();
		$this->modules = $this->parser->get_modules_list();
		$this->cadres = $this->parser->get_cadres_list();
		$this->cadres_classement_list=$this->parser->cadres_classement_list;
		
		//printr($this->versions);
		//print($this->id_version);
		@ini_set("zend.ze1_compatibility_mode", "1");
				
	}
	
	public function create_new_cms($name=''){
		global $dbh,$msg,$PMBuserid;
		
		if(!$name){ // c'est le tout premier
			$selected=1;	
			$name=$msg["cms_build_version_cms_default_name"];
		}
		$rqt_insert = "INSERT INTO cms SET 
			cms_name='$name'
		";			
		pmb_mysql_query($rqt_insert);
		$id_cms = pmb_mysql_insert_id();

		$rqt_insert = "INSERT INTO cms_version SET 
			version_cms_num = '$id_cms',
			version_date = now(),
			version_comment = '".$msg["cms_build_version_cms_default_tag_name"]."',
			version_user = $PMBuserid
		";		
		pmb_mysql_query($rqt_insert);
		$id_version = pmb_mysql_insert_id();
		return 	$id_version;				
	}
	
	public function save_version_form($id_cms=0){
		global $name, $comment,$opac_default,$opac_view_num;	
		global $dbh,$msg,$PMBuserid;		
		global $pmb_opac_view_activate;
		
		if($id_cms){
			$rqt="Update cms SET cms_name='$name',cms_comment='$comment',cms_opac_default='$opac_default', cms_opac_view_num='$opac_view_num' where id_cms=$id_cms ";
			pmb_mysql_query($rqt);
		}else{
			$rqt.="	INSERT cms SET 
				cms_name='$name',
				cms_comment='$comment'	,
				cms_opac_default='$opac_default', 
				cms_opac_view_num='$opac_view_num' ";
			pmb_mysql_query($rqt);
			$id_cms = pmb_mysql_insert_id();
			
			$rqt_insert = "INSERT INTO cms_version SET 
				version_cms_num = '$id_cms',
				version_date = now(),
				version_comment = '".$msg["cms_build_version_cms_default_tag_name"]."',
				version_user = $PMBuserid
			";
			pmb_mysql_query($rqt_insert);
			$id_version = pmb_mysql_insert_id();
		}
		if($opac_default){
			// on nettoie les autres opac_default
			$rqt="Update cms SET cms_opac_default=0 where id_cms!=$id_cms ";
			pmb_mysql_query($rqt);			
			// Update du paramètre opac_cms permettant de le sélectionner par défaut				
			$req="update parametres set valeur_param='".$id_cms."' where type_param = 'opac' and sstype_param='cms' ";
			pmb_mysql_query($req, $dbh);
		}
		
		if($pmb_opac_view_activate){
			// surcharge du param opac_cms
			$param_subst=new param_subst("opac","opac_view",$opac_view_num);
			$param_subst->delete_param_value("cms",$id_cms);
			if($opac_view_num){
				$param_subst->save_param("cms",$id_cms,"id du CMS utilisé en OPAC");
			}
		}
		
		$rqt="select count(1) from cms where cms_opac_default!=0 ";
		$res = pmb_mysql_query($rqt, $dbh);				
		if(!pmb_mysql_result($res, 0, 0)){
			$req="update parametres set valeur_param=0 where type_param = 'opac' and sstype_param='cms' ";
			pmb_mysql_query($req, $dbh);
		}
		$this->get_versions_list();
	}
	
	public function get_versions_list(){
		global $dbh;
		$this->versions=array();
		
		$requete = "select * from cms order by cms_name ";
		$res = pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){				
				$this->versions[$row->id_cms]['id']=$row->id_cms;				
				$this->versions[$row->id_cms]['name']=$row->cms_name;
				$this->versions[$row->id_cms]['comment']=$row->cms_comment;
				//$this->versions[$row->id_cms]['selected']=$row->cms_selected;
				$this->versions[$row->id_cms]['opac_default']=$row->cms_opac_default;
				$this->versions[$row->id_cms]['opac_view_num']=$row->cms_opac_view_num;
				$this->versions[$row->id_cms]['versions']=array();
				$requete = "select * from cms_version where version_cms_num='".$row->id_cms."' order by version_date desc ";
				$res_version = pmb_mysql_query($requete, $dbh);				
				while($row_version = pmb_mysql_fetch_object($res_version)){			
					$this->versions[$row->id_cms]['versions'][$row_version->id_version]['id']=$row_version->id_version;		
					$this->versions[$row->id_cms]['versions'][$row_version->id_version]['date']=$row_version->version_date;	
					$this->versions[$row->id_cms]['versions'][$row_version->id_version]['comment']=$row_version->version_comment;			
					$this->versions[$row->id_cms]['versions'][$row_version->id_version]['user']=$row_version->version_user;
					$this->versions[$row->id_cms]['versions'][$row_version->id_version]['selected']=$row_version->version_public;
					
					$this->versions_info[$row_version->id_version]['id_cms']=$row->id_cms;				
				}		
			}
		}		
	}
	
	public function get_version_default(){
		global $dbh;
		if ($this->cms_id)  $sel= " and id_cms='".$this->cms_id."' ";
		else if(isset($_SESSION["cms_in_use"]) && $_SESSION["cms_in_use"]) $sel= " and id_cms='".$_SESSION["cms_in_use"]."' ";
		else $sel = '';
		// on prend la derniere version du cms utilisé 
		$requete = "select * from cms_version, cms where 
			id_cms=version_cms_num 
			$sel
			order by version_date desc 
		";		
		$res = pmb_mysql_query($requete, $dbh);				
		if($row = pmb_mysql_fetch_object($res)){	
			$_SESSION["cms_in_use"]=$row->id_cms;
			$_SESSION["cms_version"]=$row->id_version;
			return $row->id_version;
		}		
		// si pas trouvé on prend le premier venu
		$requete = "select * from cms_version, cms where 
			id_cms=version_cms_num 
			order by version_date desc 
		";
		$res = pmb_mysql_query($requete, $dbh);				
		if($row = pmb_mysql_fetch_object($res)){	
			$_SESSION["cms_in_use"]=$row->id_cms;
			$_SESSION["cms_version"]=$row->id_version;
			return $row->id_version;
		}		
	}
	
	public function version_delete($id_version){
		global $dbh;
		$req = "delete from cms_version where id_version= $id_version ";		
		$res = pmb_mysql_query($req, $dbh);	
		$req = "delete from cms_build where build_version_num= $id_version ";			
		$res = pmb_mysql_query($req, $dbh);	
		$this->get_versions_list();
	}
	
	public function cms_delete($id_cms){
		global $dbh;		
		
		$requete = "select * from cms_version where version_cms_num='".$id_cms."' ";
		$res_version = pmb_mysql_query($requete, $dbh);				
		while($row_version = pmb_mysql_fetch_object($res_version)){		
			$req = "delete from cms_build where build_version_num='". $row_version->id_version."'  ";			
			pmb_mysql_query($req, $dbh);	
		}
		$req = "delete from cms_version where version_cms_num='$id_cms'  ";		
		$res = pmb_mysql_query($req, $dbh);	
		$req = "delete from cms where id_cms= $id_cms ";			
		$res = pmb_mysql_query($req, $dbh);	
		$this->get_versions_list();
	}
	
	public function get_form_block(){
		global $cms_build_block_tpl;
		global $cms_build_pages_tpl;
		global $cms_build_pages_tpl_item;
		global $cms_build_modules_tpl,$cms_build_modules_tpl_item;
		global $cms_build_cadre_tpl_filter;
		global $build_id_version;
		global $dbh;
		global $opac_cms;
		global $pmb_opac_url,$cms_url_base_cms_build;
		
		if($cms_url_base_cms_build){
			$build_url=$cms_url_base_cms_build;
		} else $build_url=$pmb_opac_url;
		
		if($build_id_version){
			$requete = "select * from cms_version where id_version =$build_id_version ";
			$res = pmb_mysql_query($requete, $dbh);				
			if($row = pmb_mysql_fetch_object($res)){	
				$_SESSION["cms_in_use"]=$row->version_cms_num;
				$_SESSION["cms_version"]=$row->id_version;					
			}
		}
		$tpl=$cms_build_block_tpl;
		$javascript="";
		if(count($this->contener_list))$javascript.="var cms_contener_list=new Array('".implode("','",$this->contener_list)."');";
		if(count($this->cadre_list))$javascript.="var cms_zone_list=new Array('".implode("','",$this->cadre_list)."');";
		if(count($this->zone_list))$javascript.="var cms_zone_list_dragable=new Array('".implode("','",$this->zone_list)."');";
		if(count($this->objet_list))$javascript.="var cms_objet_list=new Array('".implode("','",$this->objet_list)."');";
		$javascript.="var cms_name_list=new Array(); ";
		foreach($this->name_list as $id_objet =>$name){
			$javascript.="cms_name_list['$id_objet']='".addslashes($name)."'; \n";
		}
		
		$tpl=str_replace("!!cms_objet_list_declaration!!",$javascript,$tpl);		
		$tpl=str_replace("!!opac_url!!",$build_url,$tpl);
		$pages_tpl=$this->pages->get_list($cms_build_pages_tpl,$cms_build_pages_tpl_item);		
		$tpl=str_replace("!!cms_objet_pages!!",$pages_tpl,$tpl);
		
		$tpl=str_replace("!!cms_objet_modules!!", $this->build_modules_list(),$tpl);
		$tpl=str_replace("!!id_version!!", $_SESSION["cms_version"],$tpl);
		if(!($opac_view_num=$this->versions[$_SESSION["cms_in_use"]]['opac_view_num']))$opac_view_num=-1;
		$tpl=str_replace("!!opac_view_id!!",$opac_view_num,$tpl);

		$tpl=str_replace("!!cadre_filter!!", $cms_build_cadre_tpl_filter,$tpl);
		$tpl=str_replace("!!cadre_list_in_page!!", $this->build_cadres_list_in_page(),$tpl);
		$tpl=str_replace("!!cadre_list_in_page_nb!!", $this->nb_cadres_in_page, $tpl);
		$tpl=str_replace("!!cadre_list_not_in_page!!", $this->build_cadres_list_not_in_page(),$tpl);
		$tpl=str_replace("!!cadre_list_not_in_page_nb!!", $this->nb_cadres_not_in_page, $tpl);
		$tpl=str_replace("!!cadre_list_not_in_cms!!", $this->build_cadres_list_not_in_cms(),$tpl);
		$tpl=str_replace("!!cadre_list_not_in_cms_nb!!", $this->nb_cadres_not_in_cms, $tpl);
		$tpl=str_replace("!!cms_objet_versions!!", $this->build_versions_list(),$tpl);
		
		$tpl=str_replace("!!cms_clean_cache!!", $this->get_clean_cache_button(),$tpl);
		$tpl=str_replace("!!cms_reset_all_css!!", $this->get_reset_all_css_button(),$tpl);
		$tpl=str_replace("!!cms_clean_cache_img!!", $this->get_clean_cache_img(),$tpl);
		
		return $tpl;
	}
	

	public function build_versions_list(){
		global $msg,$cms_build_versions_tpl;
		global $cms_build_versions_tpl_item;
		
		$tpl=$cms_build_versions_tpl;
		$cadre_portail_list=array();
		$items="";	
		$pair='even';		
		foreach($this->versions as $version => $infos){
			if($pair=='odd')$pair="even"; else $pair="odd";
			$item=$cms_build_versions_tpl_item;
			
			$item=str_replace("!!id!!", $infos['id'],$item);
			$item=str_replace("!!name!!", $infos['name'],$item);
			$item=str_replace("!!opac_view_id!!", $infos['opac_view_num'],$item);			
			if($infos['opac_default'])	$item=str_replace("!!opac_default!!", $msg["cms_build_cms_opac_default_info"],$item);
			else $item=str_replace("!!opac_default!!", "",$item);
			$item=str_replace("!!odd_even!!", $pair,$item);
			if($_SESSION["cms_in_use"]==$infos['id'])
				$item=str_replace("!!cms_in_use!!", "*",$item);
			else
				$item=str_replace("!!cms_in_use!!", "",$item);
	        $items.=$item;
	    }	
		$tpl=str_replace("!!items!!", $items,$tpl);
			
		return $tpl;
	}	
	
	public function build_versions_list_ajax(){
		global $msg;
		global $cms_build_versions_tpl_item;
		$items="";	
		$pair='even';		
		foreach($this->versions as $version => $infos){
			if($pair=='odd')$pair="even"; else $pair="odd";
			$item=$cms_build_versions_tpl_item;
			
			$item=str_replace("!!id!!", $infos['id'],$item);
			$item=str_replace("!!name!!", $infos['name'],$item);
			$item=str_replace("!!opac_view_id!!", $infos['opac_view_num'],$item); 			
			if($infos['opac_default'])	$item=str_replace("!!opac_default!!", $msg["cms_build_cms_opac_default_info"],$item);
			else $item=str_replace("!!opac_default!!", "",$item);
			$item=str_replace("!!odd_even!!", $pair,$item);
			if($_SESSION["cms_in_use"]==$infos['id'])
				$item=str_replace("!!cms_in_use!!", "*",$item);
			else
				$item=str_replace("!!cms_in_use!!", "",$item);
	        $items.=$item;
	    }
		return $items;
	}	
	
	public function get_version_form($id_cms,$ajax=0){
		global $msg;
		global $charset;
		global $cms_build_version_form_tpl,$cms_build_version_form_ajax_tpl,$cms_build_version_del_button_tpl;
		global $cms_build_version_tags_item; 
		global $pmb_opac_view_activate; 
		
		if($ajax)$tpl= $cms_build_version_form_ajax_tpl;	
		else $tpl=$cms_build_version_form_tpl;	
		
		$tpl = str_replace("!!name!!",htmlentities($this->versions[$id_cms]['name'] ,ENT_QUOTES, $charset),$tpl);
		$tpl = str_replace("!!comment!!",htmlentities($this->versions[$id_cms]['comment'] ,ENT_QUOTES, $charset),$tpl);
		
		if($pmb_opac_view_activate){			
			$list=gen_liste ("SELECT opac_view_id,opac_view_name FROM opac_views order by opac_view_name ", 
					"opac_view_id", 
					"opac_view_name", 
					"opac_view_num", "",$this->versions[$id_cms]['opac_view_num'],
					0, $msg["cms_build_cms_opac_view_empty"],
					0, $msg["cms_build_cms_opac_view_select"],0,'');		
		} else {
			$list="";
		}
		$tpl = str_replace("!!opac_view!!",$list,$tpl);
		
		$selected = 0;
		$items="";
		if($id_cms){
			$tpl = str_replace("!!form_title!!",htmlentities($msg["cms_build_version_edit_bt"] ,ENT_QUOTES, $charset),$tpl);				
			$tpl = str_replace("!!form_suppr!!",$cms_build_version_del_button_tpl,$tpl);
			$pair='even';		
			foreach($this->versions[$id_cms]['versions'] as $version){
				$item=$cms_build_version_tags_item;
							
				$item = str_replace("!!version_date!!",$version['date'],$item);
				
				$item = str_replace("!!id_version!!",$version['id'],$item);
				if($version['selected']){
					$selected=1;
					$item = str_replace("!!checked!!","checked='checked'",$item);
				}
				else $item = str_replace("!!checked!!","",$item);
				
				$requete = "select username from users where userid =".$version['user'];
				$res = pmb_mysql_query($requete);
				if($row = pmb_mysql_fetch_object($res)){
					$item = str_replace("!!user!!",$row->username,$item);
				} else {
					$item = str_replace("!!user!!","",$item);
				}
				
				if($pair=='odd')$pair="even"; else $pair="odd";
				$item=str_replace("!!odd_even!!", $pair,$item);
				$items.=$item;
		
			}
		}else{
			$tpl = str_replace("!!form_title!!",htmlentities($msg["cms_build_version_add_bt"] ,ENT_QUOTES, $charset),$tpl);
			$tpl = str_replace("!!form_suppr!!","",$tpl);
		}
		//print_r($this->versions[$id_cms]);
		if($this->versions[$id_cms]["opac_default"]==1) $tpl = str_replace("!!opac_default_checked!!","checked='checked'",$tpl);	
		else $tpl = str_replace("!!opac_default_checked!!","",$tpl);

		$tpl = str_replace("!!version_list!!",$items,$tpl);
		if($selected) $tpl = str_replace("!!version_checked!!","",$tpl);
		else $tpl = str_replace("!!version_checked!!","checked='checked'",$tpl);	
		$tpl = str_replace("!!id!!",$id_cms,$tpl);	

		
		return $tpl;
	}
	
	public function build_modules_list(){
		global $msg,$cms_build_modules_tpl;
		$tpl=$cms_build_modules_tpl;
		$module_list="";
		foreach($this->modules as $module => $infos){
	        $module_list.= "<p><a href='#' onclick='cms_build_load_module(\"".$module."\",\"get_form\",0);return false;'>".$infos['name']."</a><p>";
	    }
		$tpl=str_replace("!!items!!", $module_list,$tpl);
		return $tpl;
	}	
		
	public function save_cadre_classement($id_cadre,$classement){
		
		$id_cadre+=0;
		$query = "update cms_cadres set cadre_classement='$classement' where id_cadre = ".$id_cadre;
		pmb_mysql_query($query);
	}

	public function unchain_cadre($id_cadre){
	
		$id_cadre+=0;
		foreach($this->cadres as $key => $cadre){				
			if($cadre->id_cadre == $id_cadre) {
				$query = "DELETE from cms_build where build_obj='".$cadre->cadre_object."_".$cadre->id_cadre."' and build_version_num= '".$this->id_version."' ";
				pmb_mysql_query($query);
			}
		}	
	}
		
	public function get_classement_list($classement_selected=""){
		global $charset,$msg;
		$tpl="";
		if(!$classement_selected)	$tpl.="<option value='' selected='selected'></option>";
		else $tpl.="<option value=''></option>";
		foreach($this->cadres_classement_list as $classement=> $val){
			if($classement_selected==$classement)$selected=" selected='selected' "; else $selected="";
			$tpl.="<option value='".htmlentities($classement ,ENT_QUOTES, $charset)."' $selected>".htmlentities($classement ,ENT_QUOTES, $charset)."</option>";			
		}
		return $tpl;
	}
			
	public function build_cadres_list_in_page($in_page=""){
		global $msg,$cms_build_cadres_in_page_tpl;
		global $cms_build_cadre_tpl_item;
		
		$tpl=$cms_build_cadres_in_page_tpl;
		$cadre_portail_list=array();
		$items="";	
		
		$pair='even';	
		if(!$in_page)$in_page=array();
		$classement="";	

		$this->nb_cadres_in_page = 0;
		foreach($this->cadres as $cadre => $infos){
			$item=$cms_build_cadre_tpl_item;
			if(in_array($infos->cadre_object."_".$infos->id_cadre,$in_page)){
				if($classement!=$infos->cadre_classement){
					$item="
					<tr><td><h3>".$infos->cadre_classement."</h3></td></tr>".$item;
					$classement=$infos->cadre_classement;
				}
				if($pair=='odd')$pair="even"; else $pair="odd";	
				$classement_list= $this->get_classement_list($infos->cadre_classement);
				
				$item=str_replace("!!cadre_object!!", $infos->cadre_object,$item);
				$item=str_replace("!!id_cadre!!", $infos->id_cadre,$item);
				$item=str_replace("!!cadre_name!!", $infos->cadre_name,$item);				
				$item=str_replace("!!classement_list!!", $classement_list,$item);
				$item=str_replace("!!odd_even!!", $pair,$item);
		        $items.=$item;
		        $this->nb_cadres_in_page++;
			} 
	        $cadre_portail_list[]= $infos->cadre_object."_".$infos->id_cadre;
	    }	
		$tpl=str_replace("!!items!!", $items,$tpl);
		if(count($cadre_portail_list))$javascript="var cms_cadre_portail_list=new Array('".implode("','",$cadre_portail_list)."');";
		else $javascript="";
		$tpl=str_replace("!!cms_cadre_portail_list!!", $javascript,$tpl);		
		return $tpl;
	}	
	
	public function build_cadres_list_not_in_page($in_page=""){
		global $cms_build_cadres_not_in_page_tpl;
		global $cms_build_cadre_tpl_not_in_page_item;
		global $pmb_opac_url;
		
		$tpl=$cms_build_cadres_not_in_page_tpl;
		$items="";	
		$items_not_in_page="";
		$pair='even';	
		if(!$in_page)$in_page=array();	
		$classement="";	
		$this->nb_cadres_not_in_page = 0;
		foreach($this->cadres as $cadre => $infos){
			if(!$this->cadre_is_in_cms($infos)) continue;
			if(!in_array($infos->cadre_object."_".$infos->id_cadre,$in_page)){				
				$item=$cms_build_cadre_tpl_not_in_page_item;
				if($classement!=$infos->cadre_classement){
					$item="
					<tr><td><h3>".$infos->cadre_classement."</h3></td></tr>".$item;
					$classement=$infos->cadre_classement;
				}
				$classement_list= $this->get_classement_list($infos->cadre_classement);
				if($pair=='odd')$pair="even"; else $pair="odd";
				
				if($infos->cadre_url){		
					$item=str_replace("!!cadre_link!!","<a onclick=\"!!load_page_opac!!\" href='#' >!!cadre_name!!</a>",$item);			
					$item=str_replace("!!load_page_opac!!", "cms_load_opac_page('!!cadre_object!!_!!id_cadre!!','".$pmb_opac_url.$infos->cadre_url."');",$item);
				}else{ 
					$item=str_replace("!!cadre_link!!","!!cadre_name!!",$item);	
					$item=str_replace("!!load_page_opac!!","",$item);
				}
				$item=str_replace("!!cadre_object!!", $infos->cadre_object,$item);
				$item=str_replace("!!id_cadre!!", $infos->id_cadre,$item);
				$item=str_replace("!!cadre_name!!", $infos->cadre_name,$item);		
				
				$item=str_replace("!!classement_list!!", $classement_list,$item);
				$item=str_replace("!!odd_even!!", $pair,$item);
		        $items_not_in_page.=$item;
		        $this->nb_cadres_not_in_page++;
			}  
	    }	
		$tpl=str_replace("!!items!!", $items_not_in_page, $tpl);
		return $tpl;
	}		
	
	public function cadre_is_in_cms($cadre){
		$query = "select * from cms_build where build_obj='".$cadre->cadre_object."_".$cadre->id_cadre."' and build_version_num= '".$this->id_version."' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			if($row = pmb_mysql_fetch_object($result)){
				return true;
			}
		}	
		return false;
	}
	
	
	public function build_cadres_list_not_in_cms($in_page=""){
		global $cms_build_cadres_not_in_cms_tpl;
		global $cms_build_cadre_tpl_not_in_cms_item;
		global $pmb_opac_url;

		$tpl=$cms_build_cadres_not_in_cms_tpl;
		$items="";
		$items_not_in_cms="";
		$pair='even';
		if(!$in_page)$in_page=array();
		$classement="";
		$this->nb_cadres_not_in_cms = 0;
		foreach($this->cadres as $cadre => $infos){
			
			if($this->cadre_is_in_cms($infos)) continue;
			if(in_array($infos->cadre_object."_".$infos->id_cadre,$in_page)) continue;
			$item=$cms_build_cadre_tpl_not_in_cms_item;
			if($classement!=$infos->cadre_classement){
				$item="
				<tr><td><h3>".$infos->cadre_classement."</h3></td></tr>".$item;
				$classement=$infos->cadre_classement;
			}
			$classement_list= $this->get_classement_list($infos->cadre_classement);
			if($pair=='odd')$pair="even"; else $pair="odd";

			if($infos->cadre_url){
				$item=str_replace("!!cadre_link!!","<a onclick=\"!!load_page_opac!!\" href='#' >!!cadre_name!!</a>",$item);
				$item=str_replace("!!load_page_opac!!", "cms_load_opac_page('!!cadre_object!!_!!id_cadre!!','".$pmb_opac_url.$infos->cadre_url."');",$item);
			}else{
				$item=str_replace("!!cadre_link!!","!!cadre_name!!",$item);
				$item=str_replace("!!load_page_opac!!","",$item);
			}
			$item=str_replace("!!cadre_object!!", $infos->cadre_object,$item);
			$item=str_replace("!!id_cadre!!", $infos->id_cadre,$item);
			$item=str_replace("!!cadre_name!!", $infos->cadre_name,$item);

			$item=str_replace("!!classement_list!!", $classement_list,$item);
			$item=str_replace("!!odd_even!!", $pair,$item);
			$items_not_in_cms.=$item;
			$this->nb_cadres_not_in_cms++;
		}
		$tpl=str_replace("!!items!!", $items_not_in_cms, $tpl);
		return $tpl;
	}	
	
	public function is_fixed($cadre){
		if($this->objets_att[$cadre['name']]){
			return $this->objets_att[$cadre['name']]['fixed'];
		}
		return $cadre['fixed'];    		
	}
	
	public function get_fixed_after($zone,$cadre_name){			
		$flag_start=0;
		foreach($zone['childs'] as $cadre){	
/*			if(!in_array($cadre['name'],$this->cadre_list) && strstr($cadre['name'],"cms_module_")){
				continue;
			}
*/			
			if(strstr($cadre['name'],"add_div_"))continue;		
			if($cadre['name']==$cadre_name){
				$flag_start=1; 
				continue;
			}
			if($flag_start && $this->is_fixed($cadre)){
				return $cadre['name'];
			}
		}		
		return "";
	}	
	
	public function get_after($zone,$cadre_name){			
		$flag_start=0;
		foreach($zone['childs'] as $cadre){				
/*			if(!in_array($cadre['name'],$this->cadre_list) && strstr($cadre['name'],"cms_module_")){
				continue;
			}
*/			if(strstr($cadre['name'],"add_div_"))continue;	
			if($cadre['name']==$cadre_name){
				$flag_start=1; 
				continue;
			}
			if($flag_start){
				return $cadre['name'];
			}
		}		
		return "";
	}
			
	public function version_create_new(){	
		global $PMBuserid;	

		$rqt_insert = "INSERT INTO cms_version SET 			
			version_cms_num = '".$_SESSION["cms_in_use"]."',		
			version_date = now(),
			version_user = $PMBuserid
		";		
		pmb_mysql_query($rqt_insert);
		$id_version = pmb_mysql_insert_id();
		
		$_SESSION["cms_version"]=$id_version;
		return($id_version);
	}
	public function get_last_version_data(){	
		global $PMBuserid;	
		$id_version=$this->get_version_default();
		$this->last_version_data=array();
		if(!$id_version) return;
		$query_css_zones = "select * from cms_build where  build_version_num= '".$id_version."' ";
		$res = pmb_mysql_query($query_css_zones);
		if(pmb_mysql_num_rows($res)){
			while($r = pmb_mysql_fetch_object($res)){
				$this->last_version_data[$r->build_obj]=$r;
			}
		}	
	}	
	
	public function save_opac($build_info,$data){			
		$this->get_last_version_data();
		$id_version=$this->version_create_new();
		$cadre_list=array();
		$cadre_list=array();
		$zone_before="";
		$zone_after="";
		foreach($data['cms_nodes'][0] as $key => $zone){				
			$zone_name=	$zone['name'];	
			$zone_style=$zone['style'];
			$zone_parent=$zone['parent'];				
			$build_div=$zone['build_div'];		
			if($data['cms_nodes'][0][$key+1])$zone_after=$data['cms_nodes'][0][$key+1]['name'];
			else $zone_after="";

			$rqt = "insert into cms_build SET 	
				build_version_num= '$id_version',
				build_type	='zone',
				build_fixed	='1',
				build_obj='$zone_name'	,			
				build_parent='".$zone_parent."' ,
				build_child_before='".$zone_before."', 
				build_child_after='".$zone_after."', 
				build_css='".$zone_style."', 
				build_div='".$build_div."' 		 						
			";		
			pmb_mysql_query($rqt);	
			$zone_before=$zone_name;
			$cadre_list[]=$zone_name;
			$cadre_before="";
			$cadre_fixed_before="";
			foreach($zone['childs'] as $cadre){	
				$cadre_name= $cadre['name'];		
/*				if(!in_array($cadre_name,$this->cadre_list) && strstr($cadre_name,"cms_module_")){
					continue;
				}
*/
				if(strstr($cadre_name,"add_div_"))continue;
				$cadre_after=$this->get_after($zone, $cadre_name);					
				$cadre_fixed_after=$this->get_fixed_after($zone, $cadre_name);				
				$style=$cadre['style'];				
				$build_div=$cadre['build_div'];			
				if($this->is_fixed($cadre)){		// Le cadre est fixe
					$rqt = "insert into cms_build SET 	
						build_version_num= '$id_version',
						build_type	='cadre',
						build_fixed	='1',
						build_obj='$cadre_name'	,			
						build_parent='".$zone_name."' ,
						build_child_before='".$cadre_fixed_before."', 
						build_child_after='".$cadre_fixed_after."', 
						build_css='".$style."', 
						build_div='".$build_div."' 							
					";		
					pmb_mysql_query($rqt);
				}else{		
					$rqt = "insert into cms_build SET 	
						build_version_num= '$id_version',
						build_type	='cadre',
						build_fixed	='0',
						build_obj='$cadre_name'	,	
						build_parent='".$zone_name."' ,
						build_child_before='".$cadre_before."', 
						build_child_after='".$cadre_after."', 
						build_css='".$style."' , 
						build_div='".$build_div."' 							
					";				
					pmb_mysql_query($rqt);
				}	
				if($this->is_fixed($cadre)) $cadre_fixed_before=$cadre_name; 
				$cadre_before=$cadre_name;
				
				$cadre_list[]=$cadre_name;
			}
			$this->clean_link($zone_name,$id_version);	
		}
		//retauration des cadres non présents dans la page
		foreach($this->last_version_data as $cadre_name =>$cadre){	
			if(!in_array($cadre_name,$cadre_list)){
				if(strstr($cadre_name,"add_div_"))continue;
				$rqt = "insert into cms_build SET 	
					build_version_num= '$id_version',
					build_type	='".$this->last_version_data[$cadre_name]->build_type."',
					build_fixed	='".$this->last_version_data[$cadre_name]->build_fixed."',
					build_obj='$cadre_name'	,	
					build_parent='".$this->last_version_data[$cadre_name]->build_parent."' ,
					build_child_before='".$this->last_version_data[$cadre_name]->build_child_before."', 
					build_child_after='".$this->last_version_data[$cadre_name]->build_child_after."', 
					build_css='".$this->last_version_data[$cadre_name]->build_css."', 
					build_div='".$this->last_version_data[$cadre_name]->build_div."'									
				";				
				pmb_mysql_query($rqt);	
			}
		}	
		return $id_version;

	}
	
	public function clean_link($zone_name,$id_version){
		$rqt = "select * from cms_build where build_parent='".$zone_name."' and  build_version_num= '$id_version' ";			    		
		$res=pmb_mysql_query($rqt);					
    	while($r=pmb_mysql_fetch_object($res)){
    		$cadre_name=$r->build_obj;
    		$cadre_before=$r->build_child_before;
    		$cadre_after=$r->build_child_after;
    		if($cadre_before){
    			$rqt = "select * from cms_build where build_version_num= '$id_version' and build_parent='".$zone_name."' and build_obj='$cadre_before'";	
				$res_link=pmb_mysql_query($rqt);					
	    		if(!pmb_mysql_num_rows($res_link)){		
	    			$rqt = "update cms_build SET 	
						build_child_before=''
						WHERE build_obj='$cadre_name' and  build_version_num= '$id_version' 
					";	
					pmb_mysql_query($rqt);	
	    		}
    		}
    		if($cadre_after){
    			$rqt = "select * from cms_build where  build_version_num= '$id_version' and build_parent='".$zone_name."' and build_obj='$cadre_after'";	
				$res_link=pmb_mysql_query($rqt);					
	    		if(!pmb_mysql_num_rows($res_link)){		
	    			$rqt = "update cms_build SET 	
						build_child_after=''
						WHERE build_obj='$cadre_name' and  build_version_num= '$id_version' 
					";	
					pmb_mysql_query($rqt);	
	    		}
    		}
    	}
	}
	
	public function get_clean_cache_button(){
		global $msg,$base_path;
		$clean_cache_button ='<div data-dojo-type=\'dijit/form/Button\' data-dojo-props=\'id:"clean_cache_button",title:"'.cms_cache::get_cache_formatted_last_date().'",onclick:"if(confirm(\"'.$msg['cms_clean_cache_confirm'].'\")){document.location=\"'.$base_path.'/cms.php?categ=build&sub=block&action=clean_cache\";}"\'>'.$msg['cms_clean_cache'].'</div>';
		return $clean_cache_button;
	}
	
	public function get_reset_all_css_button(){
		global $msg;
	
		$reset_all_css_button='';
		$current_version = (int) $_SESSION["cms_version"];
		
		$query = "select max(id_version) as max_num_version from cms_version where version_cms_num='".$_SESSION["cms_in_use"]."'";
		$result_version = pmb_mysql_query($query);
		if($result_version) {
			$max_num_version = pmb_mysql_result($result_version, 0, 'max_num_version');
		} else {
			$max_num_version = 0;
		}
		if($current_version == $max_num_version) {
			$rqt = "select * from cms_build where build_css <> '' and build_version_num= '".$current_version."' ";
			$res=pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)){
				$reset_all_css_button ='
					<div class="row" id="cms_edit_reset_all_css_obj">
						<div data-dojo-type=\'dijit/form/Button\' data-dojo-props=\'id:"reset_all_css_button",onclick:"if(confirm(\"'.$msg['cms_build_reset_all_css_bt_confirm'].'\")){cms_reset_all_css_opac();}"\'>'.$msg['cms_build_reset_all_css_bt'].'</div>
					</div>';
			}
		}
		return $reset_all_css_button;		
	}
	
	public static function reset_all_css($id_version){
		global $dbh;
	
		$id_version += 0;
		if($id_version) {
			pmb_mysql_query("UPDATE cms_build SET build_css='' where build_version_num=".$id_version);
		}
	}

	public function get_clean_cache_img(){
		global $msg,$base_path,$cms_active_image_cache;
		$clean_cache_img="";
		if ($cms_active_image_cache){
			$clean_cache_img ='<div data-dojo-type=\'dijit/form/Button\' data-dojo-props=\'id:"clean_cache_button_img",onclick:"if(confirm(\"'.$msg['cms_clean_cache_confirm_img'].'\")){document.location=\"'.$base_path.'/cms.php?categ=build&sub=block&action=clean_cache_img\";}"\'>'.$msg['cms_clean_cache_img'].'</div>';
		}
		return $clean_cache_img;
	}
}