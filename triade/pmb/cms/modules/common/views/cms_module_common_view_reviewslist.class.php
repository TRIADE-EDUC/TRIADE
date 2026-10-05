<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_reviewslist.class.php,v 1.2 2022/08/04 14:13:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/review_data.class.php") ;

class cms_module_common_view_reviewslist extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
    <h2>Les derniers avis</h2>
{% for review in reviews %}
<h3>Note : {{review.note}}</h3>
<blockquote>{{review.commentaire}}</blockquote>

<!-- exemple d'avis sur notice -->
<blockquote>
    {{review.object.tit1}} <a href='{{review.object.permalink}}' target='_blank'>-></a>
    <img src='{{review.object.picture_url}}'/>
</blockquote>

<!-- emprunteur -->
<blockquote>{{ review.empr.emprunteur.empr_nom }} {{ review.empr.emprunteur.empr_prenom }}</blockquote>
<hr/>
{% endfor %}
</div>";
	}
	
	public function render($datas){
	    $render_datas = $this->get_render_datas($datas);
	    //on rappelle le tout...
	    return parent::render($render_datas);
	}
	
	protected function get_render_datas($datas) {
	    //on rajoute nos éléments...
	    $render_datas = [
	        'title' => "Liste d'avis",
	        'reviews' => [],
        ];
	    if(is_array($datas)){
	        foreach($datas as $id_avis){
	            $review_data = new review_data($id_avis);
	            $render_datas['reviews'][]=$review_data;
	        }
	    }
	    return $render_datas;
	}
	
	public function get_format_data_structure(){
	    $format = array();
	    $format[] = array(
	        'var' => "title",
	        'desc' => $this->msg['cms_module_common_view_title']
	    );
	    $format[] =	array(
	        'var' => "reviews",
	        'desc' => $this->msg['cms_module_commom_view_reviews_desc'],
	        'children' => array(
	            array(
	                'var' => "reviews[i].id",
	                'desc'=> $this->msg['cms_module_common_view_review_id_desc']
	            ),
	            array(
	                'var' => "reviews[i].note",
	                'desc'=> $this->msg['cms_module_common_view_review_note_desc']
	            ),
	            array(
	                'var' => "reviews[i].sujet",
	                'desc'=> $this->msg['cms_module_common_view_review_subject_desc']
	            ),
	            array(
	                'var' => "reviews[i].commentaire",
	                'desc'=> $this->msg['cms_module_common_view_review_comment_desc']
	            ),
	            array(
	                'var' => "reviews[i].create_date",
	                'desc'=> $this->msg['cms_module_common_view_review_create_date_desc']
	            ),
	            array(
	                'var' => "reviews[i].valide",
	                'desc'=> $this->msg['cms_module_common_view_review_valide_desc']
	            ),
	            array(
	                'var' => "reviews[i].avis_rank",
	                'desc'=> $this->msg['cms_module_common_view_review_rank_desc']
	            ),
	            array(
	                'var' => "reviews[i].private",
	                'desc'=> $this->msg['cms_module_common_view_review_private_desc']
	            ),
	            array(
	                'var' => "reviews[i].empr",
	                'desc'=> $this->msg['cms_module_common_view_review_empr'],
	                'children' => array(
	                    array(
	                        'var' => "reviews[i].empr.emprunteur",
	                        'desc'=> $this->msg['cms_module_common_view_review_empr'],
	                        'children' => array( 
                                array(
                                    'var' => "reviews[i].empr.emprunteur.empr_nom",
                                    'desc'=> $this->msg['cms_module_common_view_review_empr_nom']
                                ),
                                array(
                                    'var' => "reviews[i].empr.emprunteur.empr_prenom",
                                    'desc'=> $this->msg['cms_module_common_view_review_empr_prenom']
                                ),
                            ),
                        ),
                    ),
	            ),
	            array(
	                'var' => "reviews[i].object",
	                'desc'=> $this->msg['cms_module_common_view_review_object'],
	                'children' => array(),
	            ),
	            array(
	                'var' => "reviews[i].reading_list",
	                'desc'=> $this->msg['cms_module_common_view_review_reading_list'],
	                'children' => array(),
	            ),
	        )
	    );
	    
	    $format = array_merge($format,parent::get_format_data_structure());
	    return $format;
	}
}