<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_breadcrumb.class.php,v 1.13.4.1 2025/02/25 13:40:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_breadcrumb extends cms_module_common_view_django{

	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<span class='breadcrumb'>		
	>>&nbsp;<a href='{{home.link}}'>{{home.title}}</a>&nbsp;
{% for section in sections %}
	>&nbsp;<span class='elem'><a href='{{section.link}}'>{{section.title}}</a></span>&nbsp;
{% endfor %}
</span>
";
	}
	
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label>".$this->format_text($this->msg['cms_module_common_view_breadcrumb_build_section_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("section");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->save_constructor_link_form("section");
		return parent::save_form();
	}
	
	protected static function has_bulletin_notice($id_bulletin) {
	    $id_bulletin = intval($id_bulletin);
	    $query = "select num_notice from bulletins where bulletin_id = ".$id_bulletin;
	    $result = pmb_mysql_query($query);
	    return pmb_mysql_result($result, 0, 'num_notice');
	}
	
	public function get_query(){
	    global $id;
	    global $lang;
	    
	    $post = $_POST;
	    $get = $_GET;
	    
	    if(isset($post['lvl']) && $post['lvl']){
	        $niveau = $post['lvl'];
	    } elseif (isset($get['lvl']) && $get['lvl']){
	        $niveau = $get['lvl'];
	    } else $niveau='';
	    
	    $query = "";
	    $id = intval($id);
	    if ($id) {
	        switch($niveau){
	            case 'notice_display' :
	                $query = "select notice_id as id, tit1 as title from notices where notice_id='".$id."'";
	                break;
	            case 'bulletin_display' :
	                if(static::has_bulletin_notice($id)) {
	                    $query = "select bulletin_id as id, IFNULL(NULLIF(bulletin_titre,''), CONCAT(notices_b.tit1, ', ', notices_s.tit1)) as title from bulletins join notices as notices_s on notices_s.notice_id=bulletin_notice left join notices as notices_b on notices_b.notice_id=num_notice where bulletin_id='".$id."'";
	                } else {
	                    $query = "select bulletin_id as id, IFNULL(NULLIF(bulletin_titre,''), CONCAT(IF(bulletin_numero, CONCAT(bulletin_numero, IF(mention_date, CONCAT(' - ', mention_date),'')),mention_date), ', ', notices_s.tit1)) as title from bulletins join notices as notices_s on notices_s.notice_id=bulletin_notice where bulletin_id='".$id."'";
	                }
	                break;
	            case 'author_see':
	                $query = "select author_id as id, concat(author_name,' ',author_rejete) as title from authors where author_id='".$id."'";
	                break;
	            case 'titre_uniforme_see':
	                $query = "select tu_id as id, tu_name as title from titres_uniformes where tu_id='".$id."'";
	                break;
	            case 'serie_see':
	                $query = "select serie_id as id, serie_name as title from series where serie_id='".$id."'";
	                break;
	            case 'categ_see':
	                $thes = thesaurus::getByEltId($id);
	                $query = "select noeuds.id_noeud as id, if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as title
							from noeuds left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."'
							left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."'
							where noeuds.id_noeud='".$id."'";
	                break;
	            case 'indexint_see':
	                $query = "select indexint_id as id, indexint_name as title from indexint where indexint_id='".$id."'";
	                break;
	            case 'publisher_see':
	                $query = "select ed_id as id, ed_name as title from publishers where ed_id='".$id."'";
	                break;
	            case 'coll_see':
	                $query = "select collection_id as id, collection_name as title from collections where collection_id='".$id."'";
	                break;
	            case 'subcoll_see':
	                $query = "select sub_coll_id as id, sub_coll_name as title from sub_collections where sub_coll_id='".$id."'";
	                break;
	            case 'etagere_see':
	                $query = "select idetagere as id, name as title from etagere where idetagere='".$id."'";
	                break;
	            case 'bannette_see':
	                $query = "select id_bannette as id, nom_bannette as title from bannettes where id_bannette='".$id."'";
	                break;
	            case 'rss_see':
	                $query = "select id_rss_flux as id, nom_rss_flux as title from rss_flux where id_rss_flux='".$id."'";
	                break;
	            case 'concept_see' :
	                $query ="select id_item as id, value as title from skos_fields_global_index where code_champ =1 and code_ss_champ =1 and id_item = '".$id."'";
	                break;
	            case "authperso_see":
	                $query = "select num_type from authperso_custom_values join authperso_custom on authperso_custom_champ = idchamp where authperso_custom_origine = '".$id."'";
	                $result = pmb_mysql_query($query);
	                if(pmb_mysql_num_rows($result)){
	                    $query = "select '".$id."' as id ,'".addslashes(authperso::get_isbd($id))."' as title";
	                }
	            default :
	                break;
	        }
	    }
	    return $query;
	}
	
	protected function get_page_title() {
	    return "
        <span id='breadcrumb_current_page'></span>
        <script>
            addLoadEvent(
    			function() {
    				let h1 = document.querySelector('h1');
    	           	if (h1) {
    					document.getElementById('breadcrumb_current_page').innerHTML = h1.innerText;
    				}
    			}
    		);
        </script>";
	}
	
	public function render($datas){	
		global $msg, $opac_url_base;
		
		$render_datas = array();
		$render_datas['sections'] = array();
		$render_datas['home'] = array(
			'title' => $this->msg['home'],
			'link' => $opac_url_base
		);
		//on commence par récupérer le type et le sous-type de page...
		$type_page_opac = cms_module_common_datasource_typepage_opac::get_type_page();
		$subtype_page_opac = cms_module_common_datasource_typepage_opac::get_subtype_page();
		if($type_page_opac && $subtype_page_opac){
		    $render_datas['page'] = array();
		    $query = $this->get_query();
		    if ($query) {
		        $result = pmb_mysql_query($query);
		        while ($row = pmb_mysql_fetch_object($result)) {
		            $render_datas['page']["id"] = $row->id;
		            $render_datas['page']["title"] = $row->title ?? "";
		        }
		    }
	        $render_datas['page']["generic_title"] = cms_module_common_datasource_typepage_opac::get_label($subtype_page_opac);
	        if(isset($msg['cms_page_title_'.$subtype_page_opac])) {
	            $render_datas['page']["generic_title"] = $msg['cms_page_title_'.$subtype_page_opac];
	        }
		    $render_datas['page']['type_page'] = cms_module_common_datasource_typepage_opac::get_label($type_page_opac);
		    $render_datas['page']['subtype_page'] = cms_module_common_datasource_typepage_opac::get_label($subtype_page_opac);
		}
		if (empty($render_datas['page']['title'])) {
		    $render_datas['page']['title'] = $this->get_page_title();
		}
		$links = [
		    "article" => $this->get_constructed_link("article", "!!id!!"),
		    "section" => $this->get_constructed_link("section", "!!id!!")
		];
		
		if (isset($datas['article'])) {
			$render_datas['article'] = $datas['article'];
		}

		if (isset($datas['sections']) && is_array($datas['sections']) && count($datas['sections'])) {
			foreach ($datas['sections'] as $section) {
				$cms_section = cms_provider::get_instance("section",$section);
				$infos= $cms_section->format_datas($links);
				$render_datas['sections'][]=$infos;
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){
		//dans ce cas là, c'est assez simple, c'est la vue qui va chercher les données...
		$format = array();
		$format[] =	array(
			'var' => 'home',
			'desc' => "",
			'children' => array(
				array(
					'var' => "home.title",
					'desc' => $this->msg['cms_module_common_view_home_title_desc'],
				),
				array(
					'var' => "home.link",
					'desc' => $this->msg['cms_module_common_view_home_link_desc'],
				)
			)
		); 
		$format[] = array(
		    'var' => 'page',
		    'desc' => "",
		    'children' => array(
		        array(
		            'var' => "page.title",
		            'desc' => $this->msg['cms_module_common_view_page_title_desc'],
		        ),
		        array(
		            'var' => "page.generic_title",
		            'desc' => $this->msg['cms_module_common_view_page_generic_title_desc'],
		        ),
		        array(
		            'var' => "page.type_page",
		            'desc' => $this->msg['cms_module_common_view_page_type_page_desc'],
		        ),
		        array(
		            'var' => "page.subtype_page",
		            'desc' => $this->msg['cms_module_common_view_page_subtype_page_desc'],
		        )
		    )
		);
		$sections = array(
			'var' => "sections",
			'desc' => $this->msg['cms_module_common_view_section_desc'],
			'children' => $this->prefix_var_tree(cms_section::get_format_data_structure(false,false),"sections[i]")
		);
		$sections['children'][] = array(
			'var' => "sections[i].link",
			'desc'=> $this->msg['cms_module_common_view_section_link_desc']
		);
		$format[]=$sections;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}