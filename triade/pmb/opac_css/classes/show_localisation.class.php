<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: show_localisation.class.php,v 1.5 2023/12/07 15:02:48 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/acces.class.php");
require_once($class_path."/translation.class.php");

class show_localisation {

	protected static $num_location;
	
	protected static $num_section;
	
	protected static $acces_j;
	
	protected static $statut_j;
	
	protected static $statut_r;
	
    public static function affiche_notice_navigopac($requete){
        global $page, $nbr_lignes, $id, $location, $dcote, $lcote, $nc, $main, $ssub,$plettreaut ;
        global $opac_nb_aut_rec_per_page,$opac_section_notices_order, $msg, $dbh, $opac_notices_depliable, $begin_result_liste, $add_cart_link_spe,$base_path;
        global $back_surloc,$back_loc,$back_section_see;
        global $opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet;
        global $facettes_tpl,$opac_facettes_ajax;
        global $opac_search_allow_refinement;
        global $nb_per_page_custom;
        
        if(!$page) $page=1;
        $debut =($page-1)*$opac_nb_aut_rec_per_page;
        //On controle paramètre de tri
        if(!trim($opac_section_notices_order)){
            $opac_section_notices_order= "index_serie, tnvol, index_sew";
        }
        if($plettreaut && $plettreaut !="vide"){
            $opac_section_notices_order= "index_author, ".$opac_section_notices_order;
        }
        $requete_initiale = $requete;
        $requete.= " ORDER BY ".$opac_section_notices_order." LIMIT $debut,$opac_nb_aut_rec_per_page";
        $res = @pmb_mysql_query($requete, $dbh);
        print $nbr_lignes." ".$msg["results"]."<br />";
        
        if ($opac_notices_depliable) print $begin_result_liste;
        if ($add_cart_link_spe)
            print pmb_bidi(str_replace("!!spe!!","&location=$location&dcote=$dcote&lcote=$lcote&ssub=$ssub&nc=$nc&plettreaut=$plettreaut",$add_cart_link_spe));
        /*//affinage
         //enregistrement de l'endroit actuel dans la session
         $_SESSION["last_module_search"]["search_mod"]="section_see";
         $_SESSION["last_module_search"]["search_id"]=$id;
         */
        
        //affinage
        if(($dcote == "") && ($plettreaut == "") && ($nc == "") && ($opac_search_allow_refinement)){
            print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_module' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
        }
        //fin affinage
        
        print "<blockquote role='presentation'>";
        print aff_notice(-1);
        while ($obj=pmb_mysql_fetch_object($res)) {
            print pmb_bidi(aff_notice($obj->notice_id));
        }
        print aff_notice(-2);
        print "</blockquote>";
        pmb_mysql_free_result($res);
        print '<div id="navbar"><hr /><div style="text-align:center">'.printnavbar($page, $nbr_lignes, $opac_nb_aut_rec_per_page, './index.php?lvl=section_see&id='.$id.'&location='.$location.(($back_surloc)?'&back_surloc='.urlencode($back_surloc):'').(($back_loc)?'&back_loc='.urlencode($back_loc):'').(($back_section_see)?'&back_section_see='.urlencode($back_section_see):'').'&page=!!page!!&nbr_lignes='.$nbr_lignes.'&dcote='.$dcote.'&lcote='.$lcote.'&nc='.$nc.'&main='.$main.'&ssub='.$ssub.'&plettreaut='.$plettreaut.($nb_per_page_custom ? "&nb_per_page_custom=".$nb_per_page_custom : '')).'</div></div>';
            
        //FACETTES
        $facettes_tpl = '';
        //comparateur de facettes : on ré-initialise
        $_SESSION['facette']=array();
        if($nbr_lignes){
            require_once($base_path.'/classes/facette_search.class.php');
            $facettes_tpl .= facettes::get_display_list_from_query($requete_initiale);
        }
    }
    
    /**
     * Liste des localisatons
     * @return string
     */
    public static function get_display_list() {
        global $opac_view_filter_class;
        
        $display = '';
        if($opac_view_filter_class){
        	if(!empty($opac_view_filter_class->params["nav_sections"])) {
	            $requete="select idlocation, location_libelle, location_pic, css_style from docs_location where location_visible_opac=1
			  and idlocation in(". implode(",",$opac_view_filter_class->params["nav_sections"]).")  order by location_libelle ";
        	} else {
        		return "";
        	}
        }
        else {
            $requete="select idlocation, location_libelle, location_pic from docs_location where location_visible_opac=1 order by location_libelle ";
        }
        $resultat=pmb_mysql_query($requete);
        if (pmb_mysql_num_rows($resultat)>1) {
            $display .= list_opac_locations_ui::get_instance()->get_display_list();
        } else {
            // zéro ou une seule localisation
            if (pmb_mysql_num_rows($resultat)) {
                $location=pmb_mysql_result($resultat,0,0);
                $display .= list_opac_sections_ui::get_instance(array('location' => intval($location)))->get_display_list();
            }
        }
        return $display;
    }
	
    public static function get_sections() {
    	$sections = array();
    	$requete="select idsection, section_libelle, section_libelle_opac, section_pic from docs_section, exemplaires where expl_location=".static::$num_location." and section_visible_opac=1 and expl_section=idsection group by idsection order by section_libelle_opac, section_libelle ";
    	$resultat=pmb_mysql_query($requete);
    	while ($r=pmb_mysql_fetch_object($resultat)) {
    		if ($r->section_libelle_opac) {
    			$section_label = translation::get_translated_text($r->idsection, 'docs_section', 'section_libelle_opac', $r->section_libelle_opac);
    		} else {
    			$section_label = translation::get_translated_text($r->idsection, 'docs_section', 'section_libelle', $r->section_libelle);
    		}
    		if ($r->section_pic) {
    			$pic = $r->section_pic ;
    		} else {
				$pic = get_url_icon("rayonnage-small.png") ;
    		}
    		$sections[$section_label] = array(
    				'id' => $r->idsection,
    				'label' => $section_label,
    				'pic' => $pic
    		);
    	}
    	//Tri alphanumérique sur le libellé
    	ksort($sections);
    	return $sections;
    }
    
    /**
     * Liste des sections
     */
    public static function get_display_sections_list() {
        global $msg;
        
        $display = "<b>".sprintf($msg["l_title_search"],"<a href='index.php?'>","</a>")."</b><br /><br />";
        $display .= list_opac_sections_ui::get_instance(array('location' => static::$num_location))->get_display_list();
        return $display;
    }
	
    public static function init_query_restricts() {
        global $gestion_acces_active, $gestion_acces_empr_notice;
        
        static::$acces_j = '';
        if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
            $ac= new acces();
            $dom_2= $ac->setDomain(2);
            static::$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
        }
        
        if(static::$acces_j) {
            static::$statut_j = '';
            static::$statut_r = '';
        } else {
            static::$statut_j = ',notice_statut';
            static::$statut_r = "and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
        }
        if(isset($_SESSION["opac_view"]) && $_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
            $opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
            static::$statut_r .= " and ".$opac_view_restrict;
        }
    }
    
    public static function get_query_records_items($select='', $clause='', $group_by='') {
        $query = "
            SELECT ".$select."
            FROM notices ".static::$acces_j." 
            JOIN exemplaires ON expl_notice=notice_id AND expl_section='".static::$num_section."' AND expl_location='".static::$num_location."'
			JOIN docs_location ON docs_location.idlocation = exemplaires.expl_location and location_visible_opac = 1
				JOIN docs_section ON docs_section.idsection = exemplaires.expl_section and section_visible_opac = 1
				JOIN docs_statut ON docs_statut.idstatut = exemplaires.expl_statut and statut_visible_opac = 1 
            ".static::$statut_j." 
            WHERE 1";
        if($clause) {
            $query .= " AND ".$clause;
        }
        $query .= " ".static::$statut_r;
        if($group_by) {
            $query .= " GROUP BY ".$group_by;
        }
        return $query;
    }
    
    public static function get_query_serials_items($select='', $clause='', $group_by='') {
        $query = "
            SELECT ".$select." 
            FROM exemplaires
			JOIN docs_location ON docs_location.idlocation = exemplaires.expl_location and location_visible_opac = 1
				JOIN docs_section ON docs_section.idsection = exemplaires.expl_section and section_visible_opac = 1
				JOIN docs_statut ON docs_statut.idstatut = exemplaires.expl_statut and statut_visible_opac = 1 
            JOIN bulletins ON expl_bulletin=bulletin_id AND expl_section='".static::$num_section."' AND expl_location='".static::$num_location."' 
            JOIN notices ON notice_id=bulletin_notice ".static::$acces_j." ".static::$statut_j." 
            WHERE 1";
        if($clause) {
            $query .= " AND ".$clause;
        }
        $query .= " ".static::$statut_r;
        if($group_by) {
            $query .= " GROUP BY ".$group_by;
        }
        return $query;
    }
    
    public static function set_num_location($num_location) {
        static::$num_location = $num_location;
    }
    
    public static function set_num_section($num_section) {
        static::$num_section = $num_section;
    }
    
}