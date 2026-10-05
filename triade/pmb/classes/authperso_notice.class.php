<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_notice.class.php,v 1.40.4.1 2025/03/13 16:34:28 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/authperso.class.php");

class authperso_notice {
	public $id=0; // id de la notice
	public $onglets_info=array();
	public $auth_info=array();
	public $onglets_auth_list=array();
	private static $authpersos=array();

	public function __construct($id=0) {
		$this->id=intval($id); // id de la notice
		$this->fetch_data();
	}

	public function fetch_data() {
		$this->onglets_auth_list=array();

		if(!$this->id) {
		    return;
		}

		// pour chaque autorités existantes récupérér les autorités affectés à la notice
		$req="select * from authperso, notices_authperso,authperso_authorities where id_authperso=authperso_authority_authperso_num and notice_authperso_authority_num=id_authperso_authority and notice_authperso_notice_num=".$this->id."
		order by notice_authperso_order";

		$res = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			while(($r=pmb_mysql_fetch_object($res))) {
				// get isbd ...
			    $isbd = authperso::get_isbd($r->notice_authperso_authority_num);
			    $authperso = $this->get_authperso_class($r->id_authperso);
			    $info_fields=$authperso->get_info_fields($r->notice_authperso_authority_num);

			    $this->auth_info[$r->notice_authperso_authority_num]= array(
			        'onglet_num' => $r->authperso_notice_onglet_num,
			        'authperso_name' => translation::get_translated_text($r->id_authperso, 'authperso', 'authperso_name', $r->authperso_name),
			        'infos_global' => $r->authperso_infos_global,
			        'index_infos_global' => $r->authperso_index_infos_global,
			        'isbd' => $isbd,
			        'info_fields' => $info_fields,
			        'authperso_id' => $authperso->id
			    );
				$this->onglets_auth_list[$r->authperso_notice_onglet_num][$r->id_authperso][$r->notice_authperso_authority_num]['id']=$r->notice_authperso_authority_num;
				$this->onglets_auth_list[$r->authperso_notice_onglet_num][$r->id_authperso][$r->notice_authperso_authority_num]['isbd']=$isbd;
				$this->onglets_auth_list[$r->authperso_notice_onglet_num][$r->id_authperso][$r->notice_authperso_authority_num]['authperso_name']=$r->authperso_name;
			}
			pmb_mysql_free_result($res);
		}

	}

	public function get_notice_display(){
		global $base_path;

		$aff="";
		foreach($this->onglets_auth_list as $onglet){
			$authperso_name="";
			foreach($onglet as $auth_perso){
			    foreach($auth_perso as $auth){
					if($authperso_name!=$auth['authperso_name']){
						$authperso_name=$auth['authperso_name'];
						$aff.="<br><b>".$authperso_name."</b>&nbsp;: ";
						$new=1;
					}
					if(!$new)	$aff.=", ";
					$aff.= '<a href="'.$base_path.'/autorites.php?categ=see&sub=authperso&id='.$auth['id'].'">'.$auth['isbd'].'</a>';
					$new=0;
				}
			}
		}
		return $aff;
	}

	public function get_notice_display_list(){
		$aff_list=array();
		foreach($this->onglets_auth_list as $onglet){
			foreach($onglet as $authperso_num => $auth_perso){
				$aff_list[$authperso_num]['isbd']="";
				$aff_list[$authperso_num]['name']="";
				foreach($auth_perso as $auth){
					$aff_list[$authperso_num]['name']=$auth['authperso_name'];
					if($aff_list[$authperso_num]['isbd'])$aff_list[$authperso_num]['isbd'].=", ";
					$aff_list[$authperso_num]['isbd'].=$auth['isbd'];
				}
			}
		}
		return $aff_list;
	}

	public function get_index_fields(){
		$index_fields=array();
		foreach($this->auth_info as $auth){
			foreach($auth['info_fields'] as $field){
				if(!isset($index_fields[$field['code_champ']]['ss_champ'][0])) {
				    $index_fields[$field['code_champ']]['ss_champ'][0] = array();
				}
				if(!in_array($auth['isbd'], $index_fields[$field['code_champ']]['ss_champ'][0])) {
					$index_fields[$field['code_champ']]['ss_champ'][0][] = $auth['isbd'];
				}
				if($field['search'] ){
					$index_fields[$field['code_champ']]['pond']=$field['pond'];
					if($field['all_format_values']) {
					    if(!isset($index_fields[$field['code_champ']]['ss_champ'][$field['code_ss_champ']])) {
					        $index_fields[$field['code_champ']]['ss_champ'][$field['code_ss_champ']] = array();
					    }
						$index_fields[$field['code_champ']]['ss_champ'][$field['code_ss_champ']][] = $field['all_format_values'];
					}
				}
			}
		}
		return $index_fields;
	}

	public function get_index_fields_to_delete(){
		$authpersos = new authpersos();
		return $authpersos->get_all_index_fields();
	}

	public function get_form(){
		global $msg,$charset;

		$onglet_all_tpl="
		<script>

		function authperso_highlight(obj) {
			obj.style.background='#DDD';
		}

		function authperso_downlight(obj) {
			obj.style.background='';
		}

	    function fonction_raz_authperso(authperso_id) {
	        name=this.getAttribute('id').substring(4);
	        name_id = name.substr(0,11)+'_id'+name.substr(11);
	        document.getElementById(name_id).value=0;
	        document.getElementById(name).value='';
	    }

		function add_authperso(authperso_id) {
	        var template = document.getElementById('elauthperso'+ authperso_id );
	        var authperso=document.createElement('div');
	        authperso.className='row';

			var suffixe = document.getElementById('max_authperso_'+ authperso_id).value;

	        authperso.setAttribute('id','drag_authperso_'+ authperso_id +'_' +suffixe);
	        authperso.setAttribute('order',suffixe);
	        authperso.setAttribute('highlight','authperso_highlight');
	        authperso.setAttribute('downlight','authperso_downlight');
	        authperso.setAttribute('dragicon','".get_url_icon('icone_drag_notice.png')."');
	        authperso.setAttribute('handler','handle_'+ authperso_id +'_'  +suffixe);
	        authperso.setAttribute('recepttype','authperso'+authperso_id);
	        authperso.setAttribute('recept','yes');
	        authperso.setAttribute('dragtype','authperso'+authperso_id);
	        authperso.setAttribute('draggable','yes');

	        var nom_id = 'f_authperso_'+authperso_id+'_' + suffixe
	        var f_authperso = document.createElement('input');
	        f_authperso.setAttribute('name',nom_id);
	        f_authperso.setAttribute('id',nom_id);
	        f_authperso.setAttribute('type','text');
	        f_authperso.className='saisie-80emr';
	        f_authperso.setAttribute('value','');
			f_authperso.setAttribute('completion','authperso_'+authperso_id);
	        f_authperso.setAttribute('autfield','f_authperso_id_'+ authperso_id +'_' +suffixe);

	        var del_f_authperso = document.createElement('input');
	        del_f_authperso.setAttribute('id','del_f_authperso_'+ authperso_id +'_' +suffixe);
	        del_f_authperso.onclick=fonction_raz_authperso;
	        del_f_authperso.setAttribute('type','button');
	        del_f_authperso.className='bouton';
	        del_f_authperso.setAttribute('readonly','');
	        del_f_authperso.setAttribute('value','$msg[raz]');

	        var f_authperso_id = document.createElement('input');
	        f_authperso_id.name='f_authperso_id_'+ authperso_id +'_' +suffixe;
	        f_authperso_id.setAttribute('type','hidden');
	        f_authperso_id.setAttribute('id','f_authperso_id_'+ authperso_id +'_' +suffixe);
	        f_authperso_id.setAttribute('value','');

	        var f_authperso_span_handle = document.createElement('span');
	        f_authperso_span_handle.setAttribute('id','handle_'+ authperso_id +'_' +suffixe);
	        f_authperso_span_handle.style.float='left';
	        f_authperso_span_handle.style.paddingRight='7px';

	        var f_authperso_drag_img = document.createElement('img');
	        f_authperso_drag_img.setAttribute('src','".get_url_icon('sort.png')."');
	        f_authperso_drag_img.style.width='12px';
	        f_authperso_drag_img.style.verticalAlign='middle';

	        f_authperso_span_handle.appendChild(f_authperso_drag_img);
	        f_authperso_span_handle.appendChild(f_authperso_drag_img);

	        authperso.appendChild(f_authperso_span_handle);

	        authperso.appendChild(f_authperso);
	        var space=document.createTextNode(' ');
	        authperso.appendChild(space);
	        authperso.appendChild(del_f_authperso);
	        authperso.appendChild(f_authperso_id);
			var addButton = document.getElementById('button_add_field_authperso_' + authperso_id);
			if (addButton) authperso.appendChild(addButton);

	        template.appendChild(authperso);

	        var tab_authperso_order = document.getElementById('tab_authperso_order_'+ authperso_id);
			if (tab_authperso_order.value != '') tab_authperso_order.value += ',authperso_'+ authperso_id +'_' +suffixe;

			document.getElementById('max_authperso_'+ authperso_id).value=suffixe*1+1*1 ;
	        ajax_pack_element(f_authperso);
	        init_drag();
	    }
		</script>
		";
		$authperso_notice_onglet_tpl="
		<div id='elonglet!!ongletnum!!Parent' class='parent'>
			<h3>
                ".get_expandBase_button('elonglet!!ongletnum!!', 'notice_champs_gestion')."
				!!onglet_name!!
			</h3>
		</div>
		<div id='elonglet!!ongletnum!!Child' class='child' etirable='yes' title='!!onglet_name_title!!'>
			!!authperso_list!!
		</div>
		<hr class='spacer' />
		";
		$authperso_notice_onglet_empty="
		<div id='elonglet!!ongletnum!!Parent' class='parent'>
			<h3>
                ".get_expandBase_button('elonglet!!ongletnum!!', 'notice_champs_gestion')."
				!!onglet_name!!
			</h3>
		</div>
		<div id='elonglet!!ongletnum!!Child' class='child' etirable='yes' title='!!onglet_name_title!!'>
		</div>
		<hr class='spacer' />
		";
		$authperso_notice_elt_tpl= "
		<script>
			allow_drag['authperso!!authperso_id!!']=new Array();
			allow_drag['authperso!!authperso_id!!']['authperso!!authperso_id!!']=true;

			// Fonction pour trier les authperso
			function authperso!!authperso_id!!_authperso!!authperso_id!!(dragged,target){

				var authperso=target.parentNode;
				authperso.insertBefore(dragged,target);

				authperso_downlight(target);

				recalc_recept();
				update_order!!authperso_id!!(dragged,target);
			}

			/*
			 * Mis à jour de l'ordre
			 */
			function update_order!!authperso_id!!(source,cible){
				var src_order =  source.getAttribute('order');
				var target_order = cible.getAttribute('order');
				var authperso = source.parentNode;

				var index = 0;
				var tab_authperso_order = new Array();
				for(var i=0;i<authperso.childNodes.length;i++){
					if(authperso.childNodes[i].nodeType == 1){
						if(authperso.childNodes[i].getAttribute('recepttype')=='authperso!!authperso_id!!'){
							authperso.childNodes[i].setAttribute('order',index);
							tab_authperso_order[index] = authperso.childNodes[i].getAttribute('id').substr(5);
							index++;
						}
					}
				}
				if(document.getElementById('tab_authperso_order_!!authperso_id!!')){
					document.getElementById('tab_authperso_order_!!authperso_id!!').value=tab_authperso_order.join(',');
				}

			}

			</script>
			<div id='elauthpersoChild_auth!!authperso_id!!' title='!!auth_name!!' movable='yes'>
				<div id='elauthpersoChild_auth!!authperso_id!!a' class='row'>
					<label for='f_img_load' class='etiquette'>!!auth_name!!</label>
				</div>
				<div id='elauthperso!!authperso_id!!' class='row'>

    				<input type='hidden' name='max_authperso_!!authperso_id!!'  id='max_authperso_!!authperso_id!!' value=\"!!max_authperso!!\" />

					<input type='hidden' name='tab_authperso_order_!!authperso_id!!' id='tab_authperso_order_!!authperso_id!!' value='!!tab_authperso_order!!' />
					<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=authperso&authperso_id=!!authperso_id!!&caller=notice&p1=f_authperso_id_!!authperso_id!!_&p2=f_authperso_!!authperso_id!!_&p3=!!authperso_id!!&max_field=max_authperso_!!authperso_id!!&dyn=5&parent=0&deb_rech=', 'selector')\" />
				    <input type='button' class='bouton' value='+' onClick=\"add_authperso('!!authperso_id!!');\"/>

				   !!authlist!!

				</div>
			</div>";

		$authperso_notice_elt_tpl_num="
				  	<div id='drag_authperso_!!authperso_id!!_!!iauthperso!!'  class='row' dragtype='authperso!!authperso_id!!' draggable='yes' recept='yes' recepttype='authperso!!authperso_id!!' handler='handle_!!authperso_id!!_!!iauthperso!!'
						dragicon=\"".get_url_icon('icone_drag_notice.png')."\" dragtext='!!authperso_libelle!!' downlight=\"authperso_downlight\" highlight=\"authperso_highlight\"
						order='!!iauthperso!!' style='' >
				 		<span id=\"handle_!!authperso_id!!_!!iauthperso!!\" style=\"float:left; padding-right : 7px\"><img src=\"".get_url_icon('sort.png')."\" style='width:12px; vertical-align:middle' /></span>

				        <input type='text' class='saisie-80emr' id='f_authperso_!!authperso_id!!_!!iauthperso!!' name='f_authperso_!!authperso_id!!_!!iauthperso!!' data-form-name='f_authperso_!!authperso_id!!_' completion='authperso_!!authperso_id!!' value=\"!!authperso_libelle!!\" autfield=\"f_authperso_id_!!authperso_id!!_!!iauthperso!!\" />
				        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_authperso_!!authperso_id!!_!!iauthperso!!.value=''; this.form.f_authperso_id_!!authperso_id!!_!!iauthperso!!.value='0'; \" />
				       	<input type='hidden' name='f_authperso_id_!!authperso_id!!_!!iauthperso!!' data-form-name='f_authperso_id_!!authperso_id!!_' id='f_authperso_id_!!authperso_id!!_!!iauthperso!!' value='!!auth_id!!' />
						!!button_add_field_authperso!!
					</div>
		";
		$onglet_used=array();

		// infos des autorités existantes
		$authpersos = authpersos::get_instance();
		$this->onglets_info=$authpersos->get_onglet_list();
		foreach($this->onglets_info as $onglet_num => $onglet){
			$onglet_contens="";
			foreach($onglet as $elt){
				// Pour chaque type d'autorité dans l'onglet
				//printr($elt);
				$tpl_elt=$authperso_notice_elt_tpl;
				$iauthperso=0;
				$auth_list_tpl="";
				$tab_authperso_order=array();

				if(!isset($this->onglets_auth_list[$onglet_num][$elt['id']]) || !count($this->onglets_auth_list[$onglet_num][$elt['id']])){
					// pas d'autorité
					$auth_list_tpl=$authperso_notice_elt_tpl_num;
					$auth_list_tpl=str_replace('!!authperso_libelle!!',"", $auth_list_tpl);
					$auth_list_tpl=str_replace('!!iauthperso!!',$iauthperso, $auth_list_tpl);
					$auth_list_tpl=str_replace('!!auth_id!!',"", $auth_list_tpl);
					$button_add_field_authperso = "<input id='button_add_field_authperso_".$elt['id']."' type='button' class='bouton' value='+' onClick=\"add_authperso('".$elt['id']."');\"/>";
					$auth_list_tpl=str_replace('!!button_add_field_authperso!!',$button_add_field_authperso, $auth_list_tpl);
					$max_authperso=1;
					$tab_authperso_order[]="authperso_".$elt['id']."_0";
				}else{
					foreach ($this->onglets_auth_list[$onglet_num][$elt['id']] as $auth_id =>$auth){
						// Pour chaque autorité	répétée
						$auth_tpl=$authperso_notice_elt_tpl_num;
						$auth_tpl=str_replace('!!authperso_libelle!!',strip_tags($auth['isbd']), $auth_tpl);
						$auth_tpl=str_replace('!!iauthperso!!',$iauthperso, $auth_tpl);
						$auth_tpl=str_replace('!!auth_id!!',$auth_id, $auth_tpl);
						$button_add_field_authperso = '';
						if ($auth_id == end($this->onglets_auth_list[$onglet_num][$elt['id']])['id']) {
							$button_add_field_authperso = "<input id='button_add_field_authperso_".$elt['id']."' type='button' class='bouton' value='+' onClick=\"add_authperso('".$elt['id']."');\"/>";
						}

						$auth_tpl=str_replace('!!button_add_field_authperso!!',$button_add_field_authperso, $auth_tpl);
						$auth_list_tpl.=$auth_tpl;
						$tab_authperso_order[]="authperso_".$elt['id']."_".$iauthperso;
						$iauthperso++;
						$max_authperso=$iauthperso;
					}
				}
				$tpl_elt=str_replace('!!authlist!!',$auth_list_tpl, $tpl_elt);
				$tpl_elt=str_replace('!!auth_name!!',htmlentities($elt['name'], ENT_QUOTES, $charset), $tpl_elt);
				$tpl_elt=str_replace('!!authperso_id!!',$elt['id'], $tpl_elt);
				$tpl_elt=str_replace('!!iauthperso!!',0, $tpl_elt);
				$tpl_elt=str_replace('!!max_authperso!!',$max_authperso, $tpl_elt);
				$tpl_elt=str_replace('!!tab_authperso_order!!',implode(",",$tab_authperso_order), $tpl_elt);
				$onglet_contens.=$tpl_elt;
			}
			$onglet_tpl=$authperso_notice_onglet_tpl;
			$onglet_tpl=str_replace('!!authperso_list!!',$onglet_contens, $onglet_tpl);
			if(!$elt['onglet_name']) $elt['onglet_name']=$msg['authperso_multi_search_title'];
			$onglet_tpl=str_replace('!!onglet_name!!',$elt['onglet_name'], $onglet_tpl);
			$onglet_tpl=str_replace('!!onglet_name_title!!',htmlentities($elt['onglet_name'],ENT_QUOTES, $charset), $onglet_tpl);
			$onglet_tpl=str_replace('!!ongletnum!!',$onglet_num, $onglet_tpl);
			$onglet_used[]=$onglet_num;
			$onglet_all_tpl.=$onglet_tpl;

		}
		if (count($onglet_used)) {
			$req = 'SELECT * FROM notice_onglet where id_onglet not in(' . implode(',', $onglet_used) . ') order by onglet_name';
		} else {
			$req = 'SELECT * FROM notice_onglet order by onglet_name';
		}
		$resultat = pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			while($r_onglet = pmb_mysql_fetch_object($resultat)) {
				$onglet_tpl = $authperso_notice_onglet_empty;
				$onglet_tpl = str_replace('!!onglet_name!!', $r_onglet->onglet_name, $onglet_tpl);
				$onglet_tpl = str_replace('!!onglet_name_title!!',htmlentities($r_onglet->onglet_name, ENT_QUOTES, $charset), $onglet_tpl);
				$onglet_tpl = str_replace('!!ongletnum!!', $r_onglet->id_onglet, $onglet_tpl);
				$onglet_all_tpl.= $onglet_tpl;
			}
		}
		return $onglet_all_tpl;
	}

	public function delete(){
		$req="delete from notices_authperso where notice_authperso_notice_num=".$this->id;
		pmb_mysql_query($req);
	}

	public function save_form(){
		$authpersos = authpersos::get_instance();
		$infos=$authpersos->get_data();
		if(!count($infos)) return;
		$this->delete();

		foreach ($infos as $authperso){
			$authperso_id=$authperso['id'];
			$max_authperso="max_authperso_".$authperso_id;
			global ${$max_authperso};
			$max_authperso=${$max_authperso};
			$order=0;
			$final_ordre=array();
			$tab_authperso_order="tab_authperso_order_".$authperso_id;
			global ${$tab_authperso_order};
			$tab_authperso_order=${$tab_authperso_order};
			// value="authperso_1_2,authperso_1_0,authperso_1_1" ....
			$tab_order=explode(',',$tab_authperso_order);
			foreach($tab_order as $string_order){
				$tab_string_order=explode("_",$string_order);
				$final_ordre[]=$tab_string_order[2];
			}

			if($final_ordre){
				$order=0;
				foreach($final_ordre as $old_order){

					$auth_id="f_authperso_id_".$authperso_id."_".$old_order;
					global ${$auth_id};
					$auth_id=${$auth_id};
					if($auth_id){
						$req="insert into notices_authperso set notice_authperso_notice_num=".$this->id.", notice_authperso_authority_num= $auth_id, notice_authperso_order=".$order;
						pmb_mysql_query($req);
						$order++;
					}
				}
			}else{
				$order=0;
				for($i=0;$i<$max_authperso; $i++ ){
					$auth_id="f_authperso_id_".$authperso_id."_".$i;
					global ${$auth_id};
					$auth_id=${$auth_id};

					if($auth_id){
						$req="insert into notices_authperso set notice_authperso_notice_num=".$this->id.", notice_authperso_authority_num= $auth_id, notice_authperso_order=".$order;
						pmb_mysql_query($req);
						$order++;
					}
				}
			}


		}
	}

	public function get_fields_search(){
		$mots="";
		foreach($this->auth_info as  $auth){
			if($mots)$mots.=" ";
			$mots.=$auth["infos_global"];
		}
		return $mots;
	}

	private function get_authperso_class($id_type_authperso){
		if(!isset(self::$authpersos[$id_type_authperso])){
			self::$authpersos[$id_type_authperso] = new authperso($id_type_authperso);
		}
		return self::$authpersos[$id_type_authperso];
	}
} // authperso_notice class end

