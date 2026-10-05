<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannetteslistabon_view_bannetteslistabon.class.php,v 1.9 2024/03/06 14:19:43 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_bannetteslistabon_view_bannetteslistabon extends cms_module_common_view_django
{

    public function __construct($id=0)
    {
        parent::__construct($id);
        $this->default_template = "{% include './includes/templates/cms/modules/bannetteslistabon/cms_module_bannetteslistabon_view.tpl.html' %}";
    }


    public function get_form()
    {
        $form = "
        <div class='row'>
            <div class='colonne3'>
                <label for='cms_module_common_bannetteslistabon_view_link'>" . $this->format_text($this->msg['cms_module_common_view_bannetteslistabon_build_bannette_link']) . "</label>
            </div>
            <div class='colonne_suite'>";
        $form .= $this->get_constructor_link_form("bannette");
        $form .= "
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='cms_module_common_bannetteslistabon_view_record_link'>" . $this->format_text($this->msg['cms_module_common_view_bannetteslistabon_build_record_link']) . "</label>
            </div>
            <div class='colonne_suite'>";
        $form .= $this->get_constructor_link_form("notice");
        $form .= "
            </div>
        </div>" . parent::get_form() . "
        <div class='row'>
            <div class='colonne3'>
                <label for='cms_module_common_view_django_template_record_content'>" . $this->format_text($this->msg['cms_module_common_view_django_template_record_content']) . "</label>
            </div>
            <div class='colonne-suite'> " . notice_tpl::gen_tpl_select("cms_module_common_view_django_template_record_content", ($this->parameters['used_template'] ?? '')) . "</div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='cms_module_bannetteslistabon_view_bannetteslistabon_css'>" . $this->format_text($this->msg['cms_module_bannetteslistabon_view_bannetteslistabon_css']) . "</label>
            </div>
            <div class='colonne-suite'>
                <textarea name='cms_module_bannetteslistabon_view_bannetteslistabon_css'>" . $this->format_text(($this->parameters['css'] ?? '')) . "</textarea>
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='cms_module_common_bannetteslistabon_view_nb_notices'>" . $this->format_text($this->msg['cms_module_common_view_bannetteslistabon_build_bannette_nb_notices']) . "</label>
            </div>
            <div class='colonne_suite'>
                <input type='number' name='cms_module_common_view_bannetteslistabon_nb_notices' value='" . ($this->parameters["nb_notices"] ?? '') . "'/>
            </div>
        </div>";
        return $form;
    }


    public function save_form()
    {
        global $cms_module_common_view_bannetteslistabon_nb_notices;
        global $cms_module_bannetteslistabon_view_bannetteslistabon_css;
        global $cms_module_common_view_django_template_record_content;
        
        $this->save_constructor_link_form("bannette");
        $this->save_constructor_link_form("notice");
        $this->parameters['nb_notices'] = (int) $cms_module_common_view_bannetteslistabon_nb_notices;
        $this->parameters['css'] = stripslashes($cms_module_bannetteslistabon_view_bannetteslistabon_css);
        $this->parameters['used_template'] = $cms_module_common_view_django_template_record_content;
        return parent::save_form();
    }


    public function render($data)
    {
        global $opac_url_base;
        global $opac_show_book_pics;
        global $opac_book_pics_url;
        global $opac_notice_affichage_class;
        global $opac_bannette_notices_depliables;
        global $opac_bannette_notices_format;
        global $opac_bannette_notices_order;
        global $liens_opac;
        
        if(empty($opac_notice_affichage_class)){
            $opac_notice_affichage_class ="notice_affichage";
        }
        
        //on gere l'affichage des banettes
        foreach($data["bannettes"] as $i => $bannette) {
            $data['bannettes'][$i]['link'] = $this->get_constructed_link('bannette',$data['bannettes'][$i]['id']);
            
            if($this->parameters['nb_notices']) {
                $limitation = " LIMIT ". $this->parameters['nb_notices'];
            }
            $requete = "select * from bannette_contenu, notices where num_bannette='".$data['bannettes'][$i]['id']."' and notice_id=num_notice";
            if($opac_bannette_notices_order){
                $requete.= " order by ".$opac_bannette_notices_order;
            }
            $requete.= " ".$limitation;
            
            $resultat = pmb_mysql_query($requete);
            $cpt_record=0;
            $data["bannettes"][$i]['records']=array();
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
                $data["bannettes"][$i]['records'][$cpt_record]['id']=$r->num_notice;
                $data["bannettes"][$i]['records'][$cpt_record]['title']=$r->title;
                $data["bannettes"][$i]['records'][$cpt_record]['link']=$this->get_constructed_link("notice",$r->num_notice);
                $data["bannettes"][$i]['records'][$cpt_record]['url_vign']=$url_vign;
                $data["bannettes"][$i]['records'][$cpt_record]['content']=$content;
                $cpt_record++;
            }
        }
        
        //captcha
        $data['empr']['captcha'] = emprunteur_display::get_captcha();
        //regles mots de passe
        $data['empr']['password_rules'] = emprunteur::get_json_enabled_password_rules(0);
        //messages du module
        $data['module']['msg'] = $this->msg;
        $rendered = parent::render($data);
        
        return $rendered;
    }


    /**
     * Permet d'ajouter des meta dans la page en OPAC
     *
     * @param array $data
     * @return array
     */
    public function get_headers($data = [])
    {
        global $opac_url_base;

        return [
            "add" => [
                '<script src="'. $opac_url_base .'includes/javascript/misc.js"></script>',
                '<script src="'. $opac_url_base .'includes/javascript/ajax.js"></script>',
            ],
        ];
    }

    protected function get_format_data_bannette_structure($prefix) {
		return array(
				array(
						'var' => $prefix.".id",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_id_desc']
				),
				array(
						'var' => $prefix.".name",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_name_desc']
				),
				array(
						'var' => $prefix.".comment",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_comment_desc']
				),
				array(
						'var' => $prefix.".record_number",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_record_number_desc']
				),
				array(
						'var' => $prefix.".link",
						'desc'=> $this->msg['cms_module_bannetteslistabon_view_bannettes_link_desc']
				),
				array(
						'var' => $prefix.".records",
						'desc' => $this->msg['cms_module_bannetteslistabon_view_records_desc'],
						'children' => array(
								array(
										'var' => $prefix.".records[j].id",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_record_id_desc']
								),
								array(
										'var' => $prefix.".records[j].title",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_record_title_desc']
								),
								array(
										'var' => $prefix.".records[j].link",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_record_link_desc']
								),
								array(
										'var' => $prefix.".records[j].url_vign",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_record_url_vign_desc']
								),
								array(
										'var' => $prefix.".records[j].content",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_notices_record_content_desc']
								)
						)
				),
				array(
						'var' => $prefix.".flux_rss",
						'desc' => $this->msg['cms_module_bannetteslistabon_view_flux_rss_desc'],
						'children' => array(
								array(
										'var' => $prefix.".flux_rss[j].id",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_id_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].name",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_name_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].opac_link",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_opac_link_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].link",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_link_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].lang",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_lang_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].copy",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_copy_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].editor_mail",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_editor_mail_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].webmaster_mail",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_webmaster_mail_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].ttl",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_ttl_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].img_url",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_img_url_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].img_title",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_img_title_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].img_link",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_img_link_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].format",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_format_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].content",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_content_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].date_last",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_date_last_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].export_court",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_export_court_desc']
								),
								array(
										'var' => $prefix.".flux_rss[j].template",
										'desc'=> $this->msg['cms_module_bannetteslistabon_view_flux_rss_template_desc']
								)
						)
				)
		);
	}

	public function get_format_data_structure(){
		return array_merge(array(
				array(
						'var' => "bannettes",
						'desc' => $this->msg['cms_module_bannetteslistabon_view_bannettes_desc'],
						'children' => $this->get_format_data_bannette_structure("bannettes[i]")
				),
				array(
						'var' => "categories",
						'desc' => $this->msg['cms_module_bannetteslistabon_view_categories_desc'],
						'children' => array(
								'var' => "categories[h].bannettes",
								'desc' => $this->msg['cms_module_bannetteslistabon_view_bannettes_desc'],
								'children' => $this->get_format_data_bannette_structure("categories[h].bannettes[i]")
						)
				)
		), parent::get_format_data_structure());
	}    
}
