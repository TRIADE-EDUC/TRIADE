<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_tagcloud_datasource_tagcloud_records.class.php,v 1.10.2.2.2.1 2025/02/12 12:34:09 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once "$class_path/search.class.php";
require_once "$class_path/translation.class.php";
require_once "$class_path/XMLlist.class.php";
require_once "$include_path/misc.inc.php";

class cms_module_tagcloud_datasource_tagcloud_records extends cms_module_tagcloud_datasource_tagcloud{

	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
			return array(
			'cms_module_tagcloud_selector_tagcloud'
		);
	}

	public function get_form(){
		$form = parent::get_form();
		$form.= $this->format_text($this->msg['cms_module_tagcloud_datasource_tagcloud_records_no_parameters']);

		return $form;
	}
	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $selector_choice;

		$this->parameters= array();
		return parent::save_form();
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
// 	$selector = $this->get_selected_selector();
// 		if($selector){
// 			$article_id = $selector->get_value();
// 			$article_ids = $this->filter_datas("articles",array($selector->get_value()));
// 			if($article_ids[0]){
// 				$article = new cms_article($article_ids[0]);
// 				return $article->format_datas();
// 			}
// 		}
// 		return false;

		return array(
			array(
				'label' => "un label 1",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label ",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label 4",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label 5",
				'link' => "ici un lien a mettre",
				'weight' => 2,
				'js' => ""
			),
			array(
				'label' => "un label 6",
				'link' => "ici un lien a mettre",
				'weight' => 4,
				'js' => ""
			),
			array(
				'label' => "un label 7",
				'link' => "ici un lien a mettre",
				'weight' => 10,
				'js' => ""
			),
			array(
				'label' => "un label 8",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label 9",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array(
				'label' => "un label 10",
				'link' => "ici un lien a mettre",
				'weight' => 2,
				'js' => ""
			)
		);
	}



	public function get_manage_form(){
		global $base_path;
		//variables persos...
		global $tagcloud;
		global $tagcloud_delete;
		if(!$this->managed_datas) $this->managed_datas = array();
		if($this->managed_datas['tagclouds'][$tagcloud_delete]) unset($this->managed_datas['tagclouds'][$tagcloud_delete]);

		$form="
        <script>
            dojo.require('dijit.layout.AccordionContainer');
        </script>
		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>
			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:200px;'>
				<div dojoType= 'dijit.layout.AccordionContainer' >
					<div dojoType= 'dijit.layout.AccordionPane' title='".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_facette'])."' selected='true'>
						!!facette_list!!
						<p>
							<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&tagcloud=new&action=get_form&type=facette'/>".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_facette_add'])."</a>
						</p>

					</div>
					<div dojoType= 'dijit.layout.AccordionPane' title='".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_rmc'])."'>
						!!rmc_list!!
						<p>
							<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&tagcloud=new&action=get_form&type=rmc'/>".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_rmc_add'])."</a>
						</p>
					</div>
				</div>
			</div>
			<div dojoType='dijit.layout.ContentPane' region='center'>
			!!managed_store!!
			</div>
		</div>
		";
		$elt_tpl="
		<p>
			<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&tagcloud=!!tagcloud!!&action=get_form'>!!tagcloud_name!!</a>
			&nbsp;
			<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_store_delete=&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_common_view_django_delete_store'])."\")'>
				<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
			</a>
		</p>
		";

		$facette_list = '';
		if($this->managed_datas['tagclouds']){
			foreach($this->managed_datas['tagclouds'] as $key => $infos){
				if ($infos['type']=="facette"){
					$tpl_facette=$elt_tpl;
					$tpl_facette=str_replace('!!tagcloud!!',$key, $tpl_facette);
					$tpl_facette=str_replace('!!tagcloud_name!!',$this->format_text($infos['name']), $tpl_facette);
					$facette_list.=$tpl_facette;
				}
			}
		}
		$form= str_replace('!!facette_list!!',$facette_list, $form);

		if($tagcloud){
			$managed_store =$this->get_managed_form_start(array('tagcloud'=>$tagcloud));
			$managed_store.=$this->get_managed_store_form($tagcloud);
			$managed_store.=$this->get_managed_form_end();
		}
		$form= str_replace('!!managed_store!!',$managed_store, $form);
		return $form;
	}

	protected function get_managed_form_start($pvars=""){
		global $base_path;
		$vars ="";
		$params =array(
				'categ' => "manage"
		);
		if($this->module_class_name){
			$params['sub']= str_replace("cms_module_","",$this->module_class_name);
			$var = explode("_",$this->class_name);
			$params['quoi'] = $var[3]."s";
			$params['elem'] = $this->class_name;
		}else {
			$params['sub']= str_replace("cms_module_","",$this->class_name);
			$params['quoi'] = "module";
		}
		$params['action'] = "save_form";
		foreach($params as $key=>$val){
			if($vars!="") $vars .="&";
			$vars.=$key."=".$val;
		}
		if($pvars){
			foreach($pvars as $key=>$val){
				if($vars!="") $vars .="&";
				$vars.=$key."=".$val;
			}
		}
		return "
		<form name='".$this->class_name."_manage_form' method='POST' action='".$base_path."/cms.php?".$vars."'>
		<div class='form-contenu'>";
	}


	protected function get_managed_store_form($tagcloud){
		global $type;

		if($tagcloud != "new"){
			$infos = $this->managed_datas['tagclouds'][$tagcloud];
		}else{
			if($type=='facette'){
				$infos = array(
						'name' => "Nouvelle recherche",
						'type' => "facette",
						'criteres' =>  array(
								'critere' => 0,
								'ss_critere' => 0,
								'search_type' => 0
						)
				);
			}else{
				$infos = array(
						'name' => "Nouvelle recherche",
						'type' => "rmc",
						'rmc' => ""
				);

			}
		}

		if( $infos['type']=='facette') {

			$post_flag = true;
			$post_param = "list_crit";
			$post_param2 = "&sub_field";
			$form ="
			<script src='./javascript/http_request.js'></script>
			<script>

				function load_subfields(id_ss_champs){
					var lst = document.getElementById('list_crit');
					var id = lst.value;
					var id_subfields = id_ss_champs;
					var xhr_object=  new http_request();
					xhr_object.request('./ajax.php?module=admin&categ=opac&section=lst_facette&no_label=1',1,'list_crit='+id+'&sub_field='+id_subfields,'true',cback,0,0)
				}

				function cback(response){
					var div = document.getElementById('liste2');
					if(response=='')response = '&nbsp'
					div.innerHTML = response;
				}

				function add_facette(){

					var list_crit=document.getElementById('list_crit');
					var champ_val = list_crit.options[list_crit.selectedIndex].value;
					var champ_txt = list_crit.options[list_crit.selectedIndex].text;
					if(!champ_val)	return;

					var list_ss_champs=document.getElementById('list_ss_champs');
					if(list_ss_champs){
						var ss_champ_val = list_ss_champs.options[list_ss_champs.selectedIndex].value;
						var ss_champ_txt = list_ss_champs.options[list_ss_champs.selectedIndex].text;
					}else{
						ss_champ_val=0;
						ss_champ_txt='';
					}

					var tr = document.createElement('TR');
					tr.setAttribute('id', 'tr_'+champ_val+'_'+ss_champ_val);

					// critere principal
					var td = document.createElement('TD');
					td.appendChild(document.createTextNode(champ_txt));
					tr.appendChild(td);

					// critere 2
					var td = document.createElement('TD');
					td.appendChild(document.createTextNode(ss_champ_txt));
					tr.appendChild(td);

					// options: fields
					var td = document.createElement('TD');
					var radioInput = document.createElement('input');
			        radioInput.setAttribute('type', 'radio');
			        radioInput.setAttribute('name', 'search_type_'+champ_val+'_'+ss_champ_val);
            		radioInput.setAttribute('checked', 'checked');
            		radioInput.setAttribute('value', '0');
					td.appendChild(radioInput);
					tr.appendChild(td);

					// options: mots
					var td = document.createElement('TD');
					var radioInput = document.createElement('input');
			        radioInput.setAttribute('type', 'radio');
			        radioInput.setAttribute('name', 'search_type_'+champ_val+'_'+ss_champ_val);
            		radioInput.setAttribute('value', '1');
					td.appendChild(radioInput);
					tr.appendChild(td);

					// suppression
					var td = document.createElement('TD');
					var supr = document.createElement('input');
			        supr.setAttribute('type', 'button');
			        supr.setAttribute('value', 'X');
			        supr.setAttribute('class', 'bouton');
					supr.onclick=function(){del_facette(champ_val,ss_champ_val);};
					td.appendChild(supr);

					var list_facette = document.createElement('input');
			        list_facette.setAttribute('type', 'hidden');
			        list_facette.setAttribute('name', 'list_facette[]');
			        list_facette.setAttribute('id', 'list_facette_'+champ_val+'_'+ss_champ_val);
			        list_facette.setAttribute('value', champ_val+'_'+ss_champ_val);
					td.appendChild(list_facette);
					tr.appendChild(td);

					document.getElementById('facette_list').appendChild(tr);
				}

				function del_facette(champ_val,ss_champ_val){
					var tr = document.getElementById('tr_'+champ_val+'_'+ss_champ_val);
					document.getElementById('facette_list').removeChild(tr);
				}

			</script>
			<input id='type' type='hidden' name='type' value='".$infos['type']."'/>
			<div class='row'>
				<div class='colonne3'>
					<label for='name'>".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_facette_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input id='name' type='text' name='name' value='!!name!!'/>
				</div>
				<div class='colonne3'>
					<label for='list_crit'>".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_facette_list'])."</label>
				</div>
				<div class='colonne-suite'>
					!!liste1!!
				</div>
				<div class='colonne3'>&nbsp;</div>
				<div id='liste2' class='colonne-suite'>&nbsp;</div>
				<div class='colonne3'>&nbsp;</div>
				<div id='liste2' class='colonne-suite'><input class='bouton' type='button' value='".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_facette_add'])."' onClick=\"add_facette();return false;\"/></div>

			</div>
			<div class='row'>
				<table id='facette_list' name='facette_list'>
					<tr>
						<th>".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_critere'])."</th>
						<th>".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_ss_critere'])."</th>
						<th>".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_search_field'])."</th>
						<th>".$this->format_text($this->msg['cms_module_tagcloud_datasource_admin_search_word'])."</th>
						<th></th>
					</tr>
					!!facette_list!!
				</table>
			</div>
			";
			$form = str_replace('!!name!!',$this->format_text($infos['name']),$form);

			$this->fields_array = $this->fields_array();
			$list_champs = $this->create_list_fields();

			$form = str_replace('!!liste1!!', $list_champs, $form);

		}elseif( $infos['type']=='rmc'){

			$form=$this->add_search();
		}
		return $form;
	}

	public function save_manage_form(){
		global $tagcloud;
		global $tagcloud_delete;
		global $type;
		global $name;
		global $list_facette;

		$query = "select managed_module_box from cms_managed_modules where managed_module_name='cms_module_tagcloud' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$box = pmb_mysql_result($result,0,0);
			$infos =unserialize($box);
			$params=$infos['datasources']['cms_module_tagcloud_datasource_tagcloud_records'];
		}

		if($tagcloud_delete){
			unset($params['tagclouds'][$tagcloud_delete]);
		}else{
			if( $type=='facette') {

				$infos = array(
						'name' => $name,
						'type' => "facette"
				);
				$i=0;
				foreach ($list_facette as $facette){
					$criteres=explode('_',$facette);
					$critere=$criteres[0];
					$ss_critere=$criteres[1];
					$search_type_field="search_type_".$critere."_".$ss_critere;
					global ${$search_type_field};
					$search_type=${$search_type_field};

					$infos['criteres'][$i]['critere']=$critere;
					$infos['criteres'][$i]['ss_critere']=$ss_critere;
					$infos['criteres'][$i]['search_type']=$search_type;
					$i++;
				}
			}elseif( $infos['type']=='rmc'){


			}
			//ajout
			if($tagcloud == "new"){
				$params['tagclouds']['tagcloud'.count($params['tagclouds'])] = $infos;
			}else{
				//sinon on réécrit juste l'élément
				$params['tagclouds'][$tagcloud] = $infos;
			}
		}
		return $params;
	}

	protected function add_search(){
		global $include_path,$pmb_opac_url;
		global $lang,$msg,$base_path;

		$save_msg=$msg;
		// Recherche du fichier lang de l'opac
		$url=$pmb_opac_url."includes/messages/$lang.xml";
		$fichier_xml=$base_path."/temp/opac_lang.xml";

		curl_load_opac_file($url,$fichier_xml);
		$messages = new XMLlist("$base_path/temp/opac_lang.xml", 0);
		$messages->analyser();
		$msg = $messages->table;

		$url=$pmb_opac_url."includes/search_queries/search_fields.xml";
		$fichier_xml="$base_path/temp/search_fields_opac.xml";

		curl_load_opac_file($url,$fichier_xml);
		$my_search=new search(false,"search_fields_opac","$base_path/temp/");
		$form= $my_search->show_form("./admin.php?categ=opac&sub=search_persopac&section=liste&action=build",
				"","","./cms.php?categ=manage&sub=tagcloud&quoi=datasources&elem=cms_module_tagcloud_datasource_tagcloud_records&cms_store=new&action=get_form&type=rmc");

		$msg=$save_msg;
		return $form;
	}

	protected function get_managed_form_end(){
		return "
		</div>
		<div class='row'>
		<hr/>
		<input type='submit' class='bouton' value='".$this->format_text($this->msg['cms_manage_module_save'])."'/>
		</div>
		</form>";
	}

//recuperation de champs_base.xml
	public function fields_array(){
		global $include_path,$msg;
		global $dbh, $champ_base;

		if (!is_countable($champ_base) || !count($champ_base)) {
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

	public function array_sort(){
		global $msg;

		$array_sort = array();
		$nb = 0;
		if (is_countable($this->fields_array['FIELD'])) {
    		$nb = count($this->fields_array['FIELD']);
		}
		for($i=0;$i<$nb;$i++){
			if($tmp= $msg[$this->fields_array['FIELD'][$i]['NAME']]){
				$lib = $tmp;
			}else{
				$lib = $this->fields_array['FIELD'][$i]['NAME'];
			}
			$id2 = intval($this->fields_array['FIELD'][$i]['ID']);
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
			while($bool_search==0){
				if($array['FIELD'][$i]['ID']==$id){
					$isbd=$array['FIELD'][$i]['ISBD'];
					$array = $array['FIELD'][$i]['TABLE'][0]['TABLEFIELD'];
					$bool_search = 1;
				}
				$i++;
			}
			$size = 0;
			if (is_countable($array)) {
    			$size = count($array);
			}
			for($i=0;$i<$size;$i++){
				if ($array[$i]['NAME']) $array_subfields[$array[$i]['ID']+0] = $msg[$array[$i]['NAME']];
			}
			if($isbd){
				$array_subfields[$isbd[0]['ID']+0]=$msg['facette_isbd'];
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

//creation de la liste des criteres principaux
	public function create_list_fields(){
		global $msg;
		//recuperation du fichier xml de configuration
		$array = $this->array_sort();

		$select ="<select id='list_crit' name='list_crit' onchange='load_subfields(0)'>";
		foreach ($array as $id => $value) {
			if($id==$this->crit){
				$select.="<option value=".$id." selected='selected'>".$value."</option>";
			} else {
				$select.="<option value=".$id.">".$value."</option>";
			}
		}
		$select.="</select></br>";
		if($this->crit!=null) $select .= "<script>load_subfields(".$this->ss_crit.")</script>";
		return $select;
	}

//liste liee => sous champs
	public function create_list_subfields($id,$id_ss_champs=0,$suffixe_id=0){
		global $msg,$charset;
		$array = $this->array_subfields($id);
		$tab_ss_champs = array();
		$select_ss_champs="<label>".$msg["facette_filtre_secondaire"]."</label></br>";
		if($suffixe_id){
			$name_ss_champs="list_ss_champs_".$suffixe_id;
		}else{
			$name_ss_champs="list_ss_champs";
		}
		$select_ss_champs.="<select id='$name_ss_champs' name='$name_ss_champs'>";

		if(is_countable($array) && count($array)>1){
			foreach($array as $j=>$val2){
				if($id_ss_champs == $j) $select_ss_champs.="<option value=".$j." selected='selected'>".htmlentities($val2,ENT_QUOTES,$charset)."</option>";
				else $select_ss_champs.="<option value=".$j.">".htmlentities($val2,ENT_QUOTES,$charset)."</option>";
			}

			$select_ss_champs.="</select></br>";
			return $select_ss_champs;
		}elseif(is_countable($array) && count($array)==1){
			foreach($array as $j=>$val2){
				$select_ss_champs = "<input type='hidden' name='$name_ss_champs' value='1'/>";
			}
			return $select_ss_champs;
		}
	}
}