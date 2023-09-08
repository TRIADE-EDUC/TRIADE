<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannetteslistabon_view_bannetteslistabon.class.php,v 1.2 2019-06-13 14:00:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_bannetteslistabon_view_bannetteslistabon extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
	    global $opac_url_base;
		
		parent::__construct($id);
		$this->default_template = 
"<script type='text/javascript'>
function show_hide_records(bannette_id){
	var content = document.getElementById('itemRecords'+bannette_id);
	if (content){
		var content_display = content.style.display;
		if(content_display == 'none'){
			content.style.display = 'block';
		}else{
			content.style.display = 'none';
		}
	}
}

function show_connect_subscribe(choice_connect){
	var div_connect = document.getElementById('div_connect');
	var div_subscribe = document.getElementById('div_subscribe');

	if (choice_connect){
		div_connect.style.display= 'block';
		div_subscribe.style.display= 'none';
	} else {
		div_connect.style.display= 'none';
		div_subscribe.style.display= 'block';
	}
}
				
function notValidEmail(email) {
	var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
	if (reg.test(email)){
		return false;
	} else {
		return true;
	}
} 

function valid_form(choice){
	var form=document.forms['bannette_subscription'];
	if(choice=='abonn'){
		document.bannette_subscription.action='empr.php';
		document.bannette_subscription.submit();
	} else if(choice=='connect'){
		if ((form.connect_login.value.length==0) || (form.connect_password.value.length==0)){
			alert('".$this->msg['cms_module_bannetteslistabon_view_incomplete_filling_js']."');
		} else {
			var req = new XMLHttpRequest();
			var params = 'login=' + form.connect_login.value + '&password=' + form.connect_password.value;
			req.open('POST', '{{ajax_link_connect}}', true);
			req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			
			req.onreadystatechange = function (aEvt) {
			  if (req.readyState == 4) {
			  	if(req.status == 200){
					if (req.responseText=='ok_connect') {
						document.bannette_subscription.action='empr.php';
						document.bannette_subscription.submit();
					} else if (req.responseText=='error_connect_1') {
						alert('".$this->msg['cms_module_bannetteslistabon_view_error_connect_1']."');
					}
				}
			  }
			};
			req.send(params);
		}
	} else if(choice=='subscribe'){
		if ((form.subscribe_nom.value.length==0) || (form.subscribe_prenom.value.length==0) || (form.subscribe_email.value.length==0) || (form.subscribe_login.value.length==0) || (form.subscribe_password.value.length==0) || (form.subscribe_passwordv.value.length==0) || (form.subscribe_verifcode.value.length<5)) {
			alert('".$this->msg['cms_module_bannetteslistabon_view_incomplete_filling_js']."');
		} else if (notValidEmail(form.subscribe_email.value)) {
			alert('".$this->msg['cms_module_bannetteslistabon_view_incorrect_mail_filling_js']."');
		} else if (form.subscribe_password.value!=form.subscribe_passwordv.value) {
			alert('".$this->msg['cms_module_bannetteslistabon_view_no_concording_pwd']."');
		} else {
			var req = new XMLHttpRequest();
			var params = 'f_nom=' + form.subscribe_nom.value + '&f_prenom=' + form.subscribe_prenom.value + '&f_email=' + form.subscribe_email.value + '&f_login=' + form.subscribe_login.value + '&f_password=' + form.subscribe_password.value + '&f_passwordv=' + form.subscribe_passwordv.value + '&f_verifcode=' + form.subscribe_verifcode.value;
			if(form.subscribe_consent_message.checked) {
                params = params + '&f_consent_message=1';
            }
			params = params + '&enregistrer=' + form.enregistrer.value + '&lvl=' + form.lvl.value + '&new_connexion=' + form.new_connexion.value + '&tab=' + form.tab.value;
			for (i=0, n=form.elements.length; i<n; i++) {
				if (form.elements[i].name.indexOf('bannette_abon') != -1) {
					if (form.elements[i].checked) {
						params = params + '&' + form.elements[i].name + '=1'
					}
				}
			}
			req.open('POST', '{{ajax_link_subscribe}}', true);
			req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			
			req.onreadystatechange = function (aEvt) {
			  if (req.readyState == 4) {
			  	if(req.status == 200){
					if (req.responseText=='ok_subscribe') {
						document.location = document.location + '&ok_subscribe=1';
					} else if (req.responseText=='error_subscribe_1') {
						alert('".$this->msg['cms_module_bannetteslistabon_view_error_subscribe_1']."');
					} else if (req.responseText=='error_subscribe_2') {
						alert('".$this->msg['cms_module_bannetteslistabon_view_error_subscribe_2']."');
					} else if (req.responseText=='error_subscribe_3') {
						alert('".$this->msg['cms_module_bannetteslistabon_view_error_subscribe_3']."');
					} else if (req.responseText=='error_subscribe_8') {
						alert('".$this->msg['cms_module_bannetteslistabon_view_error_subscribe_8']."');
					} else if (req.responseText=='error_subscribe_9') {
						alert('".$this->msg['cms_module_bannetteslistabon_view_error_subscribe_9']."');
					}
				}
			  }
			};
			req.send(params);
		}
	}
}
</script>
{% if get_vars.ok_subscribe != '1' %}
<form action='' method='post' name='bannette_subscription'>
	<input type='hidden' value='PUB' name='enregistrer'/>
	<input type='hidden' value='bannette_gerer' name='lvl'/>
	<input type='hidden' value='1' name='new_connexion'/>
	<input type='hidden' value='dsi' name='tab'/>
	<input type='hidden' value='1' name='ok_abonn'/>
	<div id='alertes'>
		<div id='abonnRss'>
			<span id='lienAbnn'>".$this->msg['cms_module_bannetteslistabon_view_manage_abo']."</span>
		</div>
	</div>
	<br>
	{% for bannette in bannettes %}
	<div class='itemAlerte'>
		{% sqlvalue test %}select num_bannette from bannette_abon where num_bannette = {{bannette.id}} and num_empr = {{session_vars.id_empr}}
		{% endsqlvalue %}
		{% if test %} 	
		<input type='checkbox' name='already_abon' value='1' checked='checked' disabled='true' title='".$this->msg['cms_module_bannetteslistabon_view_receive_by_mail']." {% if bannette.comment %}{{bannette.comment}}{% else %}{{bannette.name}}{% endif %}' alt='".$this->msg['cms_module_bannetteslistabon_view_receive_by_mail']." {% if bannette.comment %}{{bannette.comment}}{% else %}{{bannette.name}}{% endif %}'/>		
		{% else %}
		<input type='checkbox' name='bannette_abon[{{bannette.id}}]' value='1' title='".$this->msg['cms_module_bannetteslistabon_view_receive_by_mail']." {% if bannette.comment %}{{bannette.comment}}{% else %}{{bannette.name}}{% endif %}' alt='".$this->msg['cms_module_bannetteslistabon_view_receive_by_mail']." {% if bannette.comment %}{{bannette.comment}}{% else %}{{bannette.name}}{% endif %}'/>
		{% endif %}
		<a href='#' onclick='show_hide_records({{bannette.id}});return false;'>
			<span class='libelleItem'>{% if bannette.comment %}{{bannette.comment}}{% else %}{{bannette.name}}{% endif %}</span>
		</a>
		{% if bannette.flux_rss.0.id %}
		<a href='{{bannette.flux_rss.0.opac_link}}' target='_blank'>
			<img class='imgItem' src='".$opac_url_base."cms/modules/bannetteslistabon/images/rss_feed.png' title=\"".$this->msg['cms_module_bannetteslistabon_view_rss_abo']." '{% if bannette.comment %}{{bannette.comment}}{% else %}{{bannette.name}}{% endif %}'\" alt=\"".$this->msg['cms_module_bannetteslistabon_view_rss_abo']." '{% if bannette.comment %}{{bannette.comment}}{% else %}{{bannette.name}}{% endif %}'\" style='height: 15px;'/>
		</a>
		{% endif %}		
	</div>
	<div id='itemRecords{{bannette.id}}' class='itemRecords' style='display:none;'>
		{% for record in bannette.records%}
			{{record.content}}
		{% endfor %}
		<div class='seeAll'><a href='./index.php?lvl=bannette_see&id_bannette={{bannette.id}}'>".$this->msg['cms_module_bannetteslistabon_view_see_all_item']."</a></div>
	</div> 
	{% endfor %}
	{% if session_vars.id_empr != 0 %}
	<br>
	<input class='bouton' type='button' onclick=\"valid_form('abonn')\" value=\"".$this->msg['cms_module_bannetteslistabon_view_item_abo']."\">
	{% else %}
	<br>
	<input type='radio' name='subscribe' value='1' checked onclick='javascript:show_connect_subscribe(false)'> ".$this->msg['cms_module_bannetteslistabon_view_radio_subscribe']." &nbsp;&nbsp;<input type='radio' name='subscribe' value='0' onclick='javascript:show_connect_subscribe(true)'> ".$this->msg['cms_module_bannetteslistabon_view_radio_connect']."
	<div id='div_connect' style='display:none;'>
		<table>
			<tbody>
				<tr>
					<td width='180'>
						<h4><span>".$this->msg['cms_module_bannetteslistabon_view_connect_login']." </span></h4>
					</td>
					<td>
						<input type='text' value='' name='connect_login'>
					</td>
				</tr>
				<tr>
					<td>
						<h4><span>".$this->msg['cms_module_bannetteslistabon_view_connect_pwd']." </span></h4>
					</td>
					<td>
						<input class='password' type='password' value='' name='connect_password'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<a href='./askmdp.php'>".$this->msg['cms_module_bannetteslistabon_view_connect_forgotten_pwd']."</a>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input class='bouton' type='button' value=\"".$this->msg['cms_module_bannetteslistabon_view_connect_btn']."\" name='ok' onclick=\"valid_form('connect')\">
					</td>
				</tr>
				</tbody>
		</table>
	</div>
	<div id='div_subscribe' style='display:block;'>
		<table>
			<tbody>
				<tr>
					<td width='180'>
						<h4><span>".$this->msg['cms_module_bannetteslistabon_view_subscribe_name']." </span></h4>
					</td>
					<td>
						<input class='subsform' type='text' value='' tabindex='1' name='subscribe_nom'>
					</td>
				</tr>
				<tr>
					<td>
						<h4><span>".$this->msg['cms_module_bannetteslistabon_view_subscribe_first_name']." </span></h4>
					</td>
					<td>
						<input class='subsform' type='text' value='' tabindex='2' name='subscribe_prenom'>
					</td>
				</tr>
				<tr>
					<td>
						<h4><span>".$this->msg['cms_module_bannetteslistabon_view_subscribe_mail']." </span></h4>
					</td>
					<td>
						<input class='subsform' type='text' value='' tabindex='3' name='subscribe_email'>
					</td>
				</tr>
				<tr>
					<td>
						<h4><span>".$this->msg['cms_module_bannetteslistabon_view_subscribe_login']." </span></h4>
					</td>
					<td>
						<input class='subsform' type='text' value='' tabindex='4' name='subscribe_login'>
					</td>
				</tr>
				<tr>
					<td>
						<h4><span>".$this->msg['cms_module_bannetteslistabon_view_subscribe_pwd']." </span></h4>
					</td>
					<td>
						<input class='subsform' type='password' value='' tabindex='5' name='subscribe_password'>
					</td>
				</tr>
				<tr>
					<td>
						<h4><span>".$this->msg['cms_module_bannetteslistabon_view_subscribe_pwd_verif']." </span></h4>
					</td>
					<td>
						<input class='subsform' type='password' value='' tabindex='6' name='subscribe_passwordv'>
					</td>
				</tr>
				<tr>
					<td>
						".$this->msg['cms_module_bannetteslistabon_view_subscribe_captcha_msg']."
					</td>
					<td>
						<img src='./includes/imageverifcode.inc.php'>
					</td>
				</tr>
				<tr>
					<td>
						<h4><span>".$this->msg['cms_module_bannetteslistabon_view_subscribe_captcha']." </span></h4>
					</td>
					<td>
						<input class='subsform' type='text' value='' tabindex='7' name='subscribe_verifcode'><br />
                        <input type='checkbox' name='subscribe_consent_message' value='1' /> 
				        <span class='websubscribe_consent_message'> ".$this->msg['cms_module_bannetteslistabon_view_subscribe_consent_message']."</span>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input class='bouton' type='button' onclick=\"valid_form('subscribe')\" value=\"".$this->msg['cms_module_bannetteslistabon_view_subscribe_btn']."\">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	{% endif %}
	<br>
</form>
{% else %}
<span id='ok_subscribe'>".$this->msg['cms_module_bannetteslistabon_view_email_sent']."</span>
{% endif %}
";
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_bannetteslistabon_view_link'>".$this->format_text($this->msg['cms_module_common_view_bannetteslistabon_build_bannette_link'])."</label>
			</div>
			<div class='colonne_suite'>";
		$form.= $this->get_constructor_link_form("bannette");
		$form.="
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_bannetteslistabon_view_record_link'>".$this->format_text($this->msg['cms_module_common_view_bannetteslistabon_build_record_link'])."</label>
			</div>
			<div class='colonne_suite'>";
		$form.= $this->get_constructor_link_form("notice");
		$form.="
			</div>
		</div>".
			parent::get_form()
				."
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_django_template_record_content'>".$this->format_text($this->msg['cms_module_common_view_django_template_record_content'])."</label>
			</div>
			<div class='colonne-suite'>
				".notice_tpl::gen_tpl_select("cms_module_common_view_django_template_record_content",$this->parameters['used_template'])."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannetteslistabon_view_bannetteslistabon_css'>".$this->format_text($this->msg['cms_module_bannetteslistabon_view_bannetteslistabon_css'])."</label>
			</div>
			<div class='colonne-suite'>
				<textarea name='cms_module_bannetteslistabon_view_bannetteslistabon_css'>".$this->format_text($this->parameters['css'])."</textarea>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_bannetteslistabon_view_nb_notices'>".$this->format_text($this->msg['cms_module_common_view_bannetteslistabon_build_bannette_nb_notices'])."</label>
			</div>
			<div class='colonne_suite'>
				<input type='number' name='cms_module_common_view_bannetteslistabon_nb_notices' value='".$this->parameters["nb_notices"]."'/>
			</div>
		</div>";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_common_view_bannetteslistabon_nb_notices;
		global $cms_module_bannetteslistabon_view_bannetteslistabon_css;		
		global $cms_module_common_view_django_template_record_content;
		
		$this->save_constructor_link_form("bannette");
		$this->save_constructor_link_form("notice");
		$this->parameters['nb_notices'] = $cms_module_common_view_bannetteslistabon_nb_notices+0;
		$this->parameters['css'] = stripslashes($cms_module_bannetteslistabon_view_bannetteslistabon_css);
		$this->parameters['used_template'] = $cms_module_common_view_django_template_record_content;
		return parent::save_form();
	}
		
	
	public function render($datas){
		global $dbh;			
		global $opac_url_base;
		global $opac_show_book_pics;
		global $opac_book_pics_url;
		global $opac_notice_affichage_class;
		global $opac_bannette_notices_depliables;
		global $opac_bannette_notices_format;
		global $opac_bannette_notices_order;
		global $liens_opac;
		
		if(!$opac_notice_affichage_class){
			$opac_notice_affichage_class ="notice_affichage";
		}
	
		//on gère l'affichage des banettes				
		foreach($datas["bannettes"] as $i => $bannette) {
			$datas['bannettes'][$i]['link'] = $this->get_constructed_link('bannette',$datas['bannettes'][$i]['id']);
			
			if($this->parameters['nb_notices']) $limitation = " LIMIT ". $this->parameters['nb_notices'];
			$requete = "select * from bannette_contenu, notices where num_bannette='".$datas['bannettes'][$i]['id']."' 
			and notice_id=num_notice";
			if($opac_bannette_notices_order){
				$requete.= " order by ".$opac_bannette_notices_order;
			}
			$requete.= " ".$limitation;
		
			$resultat = pmb_mysql_query($requete, $dbh);
			$cpt_record=0;
			$datas["bannettes"][$i]['records']=array();
			while ($r=pmb_mysql_fetch_object($resultat)) {	
				$content="";
				if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $r->thumbnail_url)) {
					$code_chiffre = pmb_preg_replace('/-|\.| /', '', $r->code);
					$url_image = $opac_book_pics_url ;
					$url_image = $opac_url_base."getimage.php?url_image=".urlencode($url_image)."&noticecode=!!noticecode!!&vigurl=".urlencode($r->thumbnail_url) ;
					if ($r->thumbnail_url){
					$url_vign=$r->thumbnail_url;	
					}else if($code_chiffre){
						$url_vign = str_replace("!!noticecode!!", $code_chiffre, $url_image) ;
					}else {
						$url_vign = $opac_url_base."images/vide.png";			
					}
				}
				if($this->parameters['used_template']){
					$tpl = new notice_tpl_gen($this->parameters['used_template']);
					$content= $tpl->build_notice($r->num_notice);
				}else{					
					$notice_class = new $opac_notice_affichage_class($r->num_notice,$liens_opac);
					$notice_class->do_header();
					switch ($opac_bannette_notices_format) {
						case AFF_BAN_NOTICES_REDUIT :
							$content .= "<div class='etagere-titre-reduit'>".$notice_class->notice_header_with_link."</div>" ;
							break;
						case AFF_BAN_NOTICES_ISBD :
							$notice_class->do_isbd();
							$notice_class->genere_simple($opac_bannette_notices_depliables, 'ISBD') ;
							$content .= $notice_class->result ;
							break;
						case AFF_BAN_NOTICES_PUBLIC :
							$notice_class->do_public();
							$notice_class->genere_simple($opac_bannette_notices_depliables, 'PUBLIC') ;
							$content .= $notice_class->result ;
							break;
						case AFF_BAN_NOTICES_BOTH :
							$notice_class->do_isbd();
							$notice_class->do_public();
							$notice_class->genere_double($opac_bannette_notices_depliables, 'PUBLIC') ;
							$content .= $notice_class->result ;
							break ;
						default:
							$notice_class->do_isbd();
							$notice_class->do_public();
							$notice_class->genere_double($opac_bannette_notices_depliables, 'autre') ;
							$content .= $notice_class->result ;
							break ;
					}
				}
				$datas["bannettes"][$i]['records'][$cpt_record]['id']=$r->num_notice;
				$datas["bannettes"][$i]['records'][$cpt_record]['title']=$r->title;
				$datas["bannettes"][$i]['records'][$cpt_record]['link']=$this->get_constructed_link("notice",$r->num_notice);
				$datas["bannettes"][$i]['records'][$cpt_record]['url_vign']=$url_vign;
				$datas["bannettes"][$i]['records'][$cpt_record]['content']=$content;
				$cpt_record++;
			}		
		}
		//on rappelle le tout...
		return parent::render($datas);
	}
	
	
	
	public function get_format_data_structure(){
		return array_merge(array(
			array(
				'var' => "bannettes",
				'desc' => $this->msg['cms_module_bannetteslistabon_view_bannettes_desc'],
				'children' => array(
					array(
						'var' => "bannettes[i].id",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_id_desc']
					),
					array(
						'var' => "bannettes[i].name",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_name_desc']
					),
					array(
						'var' => "bannettes[i].comment",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_comment_desc']
					),
					array(
						'var' => "bannettes[i].record_number",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_record_number_desc']
					),
					array(
						'var' => "bannettes[i].link",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_link_desc']
					),
					array(
						'var' => "bannettes[i].records",		
						'desc' => $this->msg['cms_module_bannetteslistabon_view_records_desc'],
						'children' => array(
							array(
								'var' => "bannettes[i].records[j].id",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_record_id_desc']
							),
							array(
								'var' => "bannettes[i].records[j].title",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_record_title_desc']
							),
							array(
								'var' => "bannettes[i].records[j].link",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_record_link_desc']
							),
							array(
								'var' => "bannettes[i].records[j].url_vign",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_record_url_vign_desc']
							),
							array(
								'var' => "bannettes[i].records[j].content",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_notices_record_content_desc']
							)
						)									
					),
					array(
						'var' => "bannettes[i].flux_rss",
						'desc' => $this->msg['cms_module_bannetteslistabon_view_flux_rss_desc'],
						'children' => array(
							array(
								'var' => "bannettes[i].flux_rss[j].id",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_id_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].name",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_name_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].opac_link",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_opac_link_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].link",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_link_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].lang",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_lang_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].copy",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_copy_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].editor_mail",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_editor_mail_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].webmaster_mail",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_webmaster_mail_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].ttl",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_ttl_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].img_url",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_img_url_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].img_title",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_img_title_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].img_link",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_img_link_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].format",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_format_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].content",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_content_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].date_last",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_date_last_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].export_court",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_export_court_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].template",
								'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_template_desc']
							)															
						)
					)									
				)
			)
		),parent::get_format_data_structure());
		
		
	}
}