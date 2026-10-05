<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_bannetteslist.class.php,v 1.14 2023/06/07 10:27:46 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

class cms_module_common_view_bannetteslist extends cms_module_common_view_django{
	
	protected $render_already_generated = false;
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = 
"<div>
	{% for bannette in bannettes %}
		<h3>{{bannette.name}}</h3>
		{% for flux_rss in bannette.flux_rss %}
			<a href='{{flux_rss.link}}'>{{flux_rss.name}}</a>
		{% endfor %}
		<div>
			<blockquote>{{bannette.comment}}</blockquote>
			{% for record in bannette.records %}
				{{record.content}}
			{% endfor %}
		</div>
	{% endfor %}
</div>
";
	}
	
	protected function get_record_template_form() {
		if(!isset($this->parameters['used_template'])) $this->parameters['used_template'] = '';
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_django_template_record_content'>".$this->format_text($this->msg['cms_module_common_view_django_template_record_content'])."</label>
			</div>
			<div class='colonne-suite'>
				".notice_tpl::gen_tpl_select("cms_module_common_view_django_template_record_content",$this->parameters['used_template'])."
			</div>
		</div>";
		return $form;
	}
	
	public function get_form(){
		if(!isset($this->parameters['css'])) $this->parameters['css'] = '';
		if(!isset($this->parameters['nb_notices'])) $this->parameters['nb_notices'] = '';
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_bannetteslist_view_link'>".$this->format_text($this->msg['cms_module_common_view_bannetteslist_build_bannette_link'])."</label>
			</div>
			<div class='colonne_suite'>";
		$form.= $this->get_constructor_link_form("bannette");
		$form.="
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_bannetteslist_view_record_link'>".$this->format_text($this->msg['cms_module_common_view_bannetteslist_build_record_link'])."</label>
			</div>
			<div class='colonne_suite'>";
		$form.= $this->get_constructor_link_form("notice");
		$form.="
			</div>
		</div>".
			parent::get_form();
		$form .= $this->get_record_template_form();	
		$form .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannetteslist_view_bannetteslist_css'>".$this->format_text($this->msg['cms_module_bannetteslist_view_bannetteslist_css'])."</label>
			</div>
			<div class='colonne-suite'>
				<textarea name='cms_module_bannetteslist_view_bannetteslist_css'>".$this->format_text($this->parameters['css'])."</textarea>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_bannetteslist_view_nb_notices'>".$this->format_text($this->msg['cms_module_common_view_bannetteslist_build_bannette_nb_notices'])."</label>
			</div>
			<div class='colonne_suite'>
				<input type='number' name='cms_module_common_view_bannetteslist_nb_notices' value='".$this->parameters["nb_notices"]."'/>
			</div>
		</div>";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_common_view_bannetteslist_nb_notices;
		global $cms_module_bannetteslist_view_bannetteslist_css;
		global $cms_module_common_view_django_template_record_content;
		
		$this->save_constructor_link_form("bannette");
		$this->save_constructor_link_form("notice");
		$this->parameters['nb_notices'] = (int) $cms_module_common_view_bannetteslist_nb_notices;
		$this->parameters['css'] = stripslashes($cms_module_bannetteslist_view_bannetteslist_css);
		$this->parameters['used_template'] = $cms_module_common_view_django_template_record_content;
		return parent::save_form();
	}
		
	public function render($datas){
		global $opac_url_base;
		global $opac_show_book_pics;
		global $opac_book_pics_url;
		global $opac_notice_affichage_class;
		global $opac_bannette_notices_depliables;
		global $opac_bannette_notices_format;
		global $opac_bannette_notices_order;
		global $liens_opac;
		
		// Déjà généré dans une classe fille
		if($this->render_already_generated) {
			return parent::render($datas);
		}
		
		if(empty($opac_notice_affichage_class)){
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
		
			$resultat = pmb_mysql_query($requete);
			$cpt_record=0;
			$datas["bannettes"][$i]['records']=array();
			$thumbnailSourcesHandler = new ThumbnailSourcesHandler();
			while ($r=pmb_mysql_fetch_object($resultat)) {	
				$content="";
				$url_vign = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $r->num_notice);
				
				$notice_class = new $opac_notice_affichage_class($r->num_notice, $liens_opac);
				if (!empty($this->parameters['used_template'])) {
					$tpl = notice_tpl_gen::get_instance($this->parameters['used_template']);
					$content = $tpl->build_notice($r->num_notice);
				} else {					
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
				
				$datas["bannettes"][$i]['records'][$cpt_record] = [
				    'id' => $r->num_notice,
				    'title' => $r->title,
				    'link' => $this->get_constructed_link('notice', $r->num_notice),
				    'url_vign' => $url_vign,
				    'content' => $content,
				    'parent' => []
				];
				
				if (!empty($notice_class->parent_id)) {
				    $url_parent_vign = "";
				    $notice_parent_class = new $opac_notice_affichage_class($notice_class->parent_id);
				    
				    $parent_notice_id = $notice_parent_class->notice_id;
				    $is_parent_bulletin = false;
				    if ($notice_parent_class->notice->niveau_biblio == 'b') {
				        $parent_notice_id = $notice_parent_class->bulletin_id;
				        $is_parent_bulletin = true;
				    }
				    
				    $url_parent_vign = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $parent_notice_id);
				    
				    $datas["bannettes"][$i]['records'][$cpt_record]['parent'] = [
				        'id' => $parent_notice_id,
				        'title' => $notice_parent_class->notice->tit1,
				        'vign' => $url_parent_vign,
				        'header' => $notice_parent_class->notice_header,
				        'link' => $this->get_constructed_link('notice', $notice_parent_class->notice_id, $is_parent_bulletin)
				    ];
				}
				
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
				'desc' => $this->msg['cms_module_bannetteslist_view_bannettes_desc'],
				'children' => array(
					array(
						'var' => "bannettes[i].id",
						'desc'=> $this->msg['cms_module_bannetteslist_view_bannettes_id_desc']
					),
					array(
						'var' => "bannettes[i].name",
						'desc'=> $this->msg['cms_module_bannetteslist_view_bannettes_name_desc']
					),
					array(
						'var' => "bannettes[i].comment",
						'desc'=> $this->msg['cms_module_bannetteslist_view_bannettes_comment_desc']
					),
					array(
						'var' => "bannettes[i].record_number",
						'desc'=> $this->msg['cms_module_bannetteslist_view_bannettes_record_number_desc']
					),
					array(
						'var' => "bannettes[i].link",
						'desc'=> $this->msg['cms_module_bannetteslist_view_bannettes_link_desc']
					),
					array(
						'var' => "bannettes[i].records",		
						'desc' => $this->msg['cms_module_bannetteslist_view_records_desc'],
						'children' => array(
							array(
								'var' => "bannettes[i].records[j].id",
								'desc'=> $this->msg['cms_module_bannetteslist_view_record_id_desc']
							),
							array(
								'var' => "bannettes[i].records[j].title",
								'desc'=> $this->msg['cms_module_bannetteslist_view_record_title_desc']
							),
							array(
								'var' => "bannettes[i].records[j].link",
								'desc'=> $this->msg['cms_module_bannetteslist_view_record_link_desc']
							),
							array(
								'var' => "bannettes[i].records[j].url_vign",
								'desc'=> $this->msg['cms_module_bannetteslist_view_record_url_vign_desc']
							),
							array(
								'var' => "bannettes[i].records[j].content",
								'desc'=> $this->msg['cms_module_bannetteslist_view_notices_record_content_desc']
							),
						    array(
						        'var' => "bannettes[i].records[j].parent",
						        'desc'=> $this->msg['cms_module_bannetteslist_view_record_parent_desc'],
						        'children' => array(
						            array(
						                'var' => "bannettes[i].records[j].parent.id",
						                'desc'=> $this->msg['cms_module_bannetteslist_view_record_id_desc']
						            ),
						            array(
						                'var' => "bannettes[i].records[j].parent.title",
						                'desc'=> $this->msg['cms_module_bannetteslist_view_record_title_desc']
						            ),
						            array(
						                'var' => "bannettes[i].records[j].parent.link",
						                'desc'=> $this->msg['cms_module_bannetteslist_view_record_link_desc']
						            ),
						            array(
						                'var' => "bannettes[i].records[j].parent.url_vign",
						                'desc'=> $this->msg['cms_module_bannetteslist_view_record_url_vign_desc']
						            ),
						            array(
						                'var' => "bannettes[i].records[j].parent.content",
						                'desc'=> $this->msg['cms_module_bannetteslist_view_notices_record_content_desc']
						            )
						        )
						    )
						)									
					),
					array(
						'var' => "bannettes[i].flux_rss",
						'desc' => $this->msg['cms_module_bannetteslist_view_flux_rss_desc'],
						'children' => array(
							array(
								'var' => "bannettes[i].flux_rss[j].id",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_id_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].name",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_name_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].opac_link",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_opac_link_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].link",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_link_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].lang",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_lang_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].copy",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_copy_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].editor_mail",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_editor_mail_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].webmaster_mail",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_webmaster_mail_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].ttl",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_ttl_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].img_url",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_img_url_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].img_title",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_img_title_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].img_link",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_img_link_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].format",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_format_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].content",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_content_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].date_last",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_date_last_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].export_court",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_export_court_desc']
							),	
							array(
								'var' => "bannettes[i].flux_rss[j].template",
								'desc'=> $this->msg['cms_module_bannetteslist_view_flux_rss_template_desc']
							)															
						)
					)									
				)
			)
		),parent::get_format_data_structure());
		
		
	}
}