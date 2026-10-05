<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_shelveslist_view_django.class.php,v 1.2.10.1 2025/04/29 10:51:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_shelveslist_view_django extends cms_module_common_view_django {
	
	
	public function __construct($id=0){
		parent::__construct($id);
		
		$this->default_template = "<div>
	{% for shelve in shelves %}
		<h3>{{shelve.name}}</h3>
		{% if shelve.link_rss %}
			<a href='{{shelve.link_rss}}'>Flux RSS</a>
		{% endif %}
		<div>
			<blockquote>{{shelve.comment}}</blockquote>
			{{shelve.records}}
		</div>
	{% endfor %}
</div>";
	}
	
	public function get_form(){
		if(!isset($this->parameters['used_template'])) $this->parameters['used_template'] = '';
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_shelveslist_view_link'>".$this->format_text($this->msg['cms_module_shelveslist_view_link'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("notice");
		$form.="
				</div>
			</div>";
		$form.= parent::get_form();
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_shelveslist_view_django_used_template'>".$this->format_text($this->msg['cms_module_shelveslist_view_django_used_template'])."</label>
				</div>
				<div class='colonne-suite'>";
		
		$form.= notice_tpl::gen_tpl_select("cms_module_shelveslist_view_django_used_template",$this->parameters['used_template']);
		$form.="				
				</div>
			</div>
		";
		return $form;
	}
	
	public function save_form(){
	    global $cms_module_shelveslist_view_django_used_template;
		
		$this->save_constructor_link_form("shelve");
		$this->parameters['used_template'] = $cms_module_shelveslist_view_django_used_template;
		return parent::save_form();
	}
	
	public function render($datas){
	    global $opac_etagere_notices_format;
	    global $opac_notice_affichage_class;
	    
	    if(!$opac_notice_affichage_class){
	        $opac_notice_affichage_class ="notice_affichage";
	    }
	    
	    if(!$this->parameters["nb_notices"]){
	        $this->parameters["nb_notices"] = 0;
	    }
	    
	    //on gère l'affichage des notices
	    if (!empty($datas["shelves"]) && is_countable($datas["shelves"])) {
    	    foreach($datas["shelves"] as $i => $shelve) {
    	        $notices = get_etagere_notices($shelve['id'], $this->parameters["nb_notices"]);
    	        $content = "";
    	        foreach ($notices as $idnotice => $niveau_biblio) {
    	            if($this->parameters['used_template']){
    	                $tpl = notice_tpl_gen::get_instance($this->parameters['used_template']);
    	                $content .= $tpl->build_notice($idnotice);
    	            } else {
    	                $content .= aff_notice($idnotice, 0, 1, 0, $opac_etagere_notices_format, AFF_ETA_NOTICES_DEPLIABLES_OUI, 0, 1, 0, 1, $this->parameters['django_directory']);
    	            }
    	        }
    	        if (!is_array($datas['shelves'][$i])) {
    	            $datas['shelves'][$i] = array();
    	        }
    	        $datas['shelves'][$i]['records'] = $content;
    	        $datas['shelves'][$i]['cart_link'] = $this->get_constructed_link('shelve_to_cart', $shelve['id']);
    	    }
	    }
	    
	    //on rappelle le tout...
	    return parent::render($datas);
	}
	
	public function get_format_data_structure(){
	    $format_datas= array(
	        array(
	            'var' => "shelves",
	            'desc' => $this->msg['cms_module_shelveslist_view_desc'],
	            'children' => array(
	                array(
	                    'var' => "shelves[i].id",
	                    'desc'=> $this->msg['cms_module_shelveslist_view_id_desc']
	                ),
	                array(
	                    'var' => 'shelves[i].cart_link',
	                    'desc' => $this->msg['cms_module_shelveslist_view_name_desc'],
	                ),
	                array(
	                    'var' => "shelves[i].name",
	                    'desc'=> $this->msg['cms_module_shelveslist_view_link_desc']
	                ),
	                array(
	                    'var' => "shelves[i].link",
	                    'desc'=> $this->msg['cms_module_shelveslist_view_link_rss_desc']
	                ),
	                array(
	                    'var' => "shelves[i].link_rss",
	                    'desc'=> $this->msg['cms_module_shelveslist_view_link_rss_desc']
	                ),
	                array(
	                    'var' => "shelves[i].comment",
	                    'desc'=> $this->msg['cms_module_shelveslist_view_comment_desc']
	                ),
	                array(
	                    'var' => "shelves[i].records",
	                    'desc'=> $this->msg['cms_module_shelveslist_view_records_desc']
	                )
	            )
	        )
	    );
	    $format_datas = array_merge($format_datas,parent::get_format_data_structure());
	    return $format_datas;
	}
}