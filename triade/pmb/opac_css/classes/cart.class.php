<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.class.php,v 1.2.2.2.2.7 2025/05/19 13:05:55 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cart {

    protected $session_cart;
    
    protected $actions = [];
    
    protected static $sort;
    
	public function __construct() {
	    $this->session_cart = (isset($_SESSION['cart']) ? $_SESSION['cart'] : array());
	}

	protected function get_type_action($name) {
	    global $opac_rgaa_active;
	    
	    if($opac_rgaa_active) {
	        $actions = array(
	            'show_cart_empty', 'show_cart_del_checked',
	            'show_cart_checked_all', 'show_cart_more_actions', 'show_cart_reserve', 'show_cart_print'
	        );
	        if(!in_array($name, $actions)) {
	            return 'link';
	        }
	    }
	    return 'button';
	}
	    
	protected function add_href_action($name, $label, $title, $link) {
	    $this->actions[$name] = array( 
	           'label' => $label,
	           'title' => $title,
	           'href' => $link,
	           'type' => $this->get_type_action($name)
	    );
	}
	
	protected function add_onclick_action($name, $label, $title, $event) {
	    $this->actions[$name] = array(
	        'label' => $label,
	        'title' => $title,
	        'onclick' => $event,
	        'type' => $this->get_type_action($name)
	    );
	}
	
	protected function _init_actions() {
	    global $msg;
	    global $opac_cart_more_actions_activate;
	    global $opac_allow_download_docnums;
	    global $opac_shared_lists, $allow_liste_lecture, $id_empr;
	    global $opac_show_suggest, $opac_allow_multiple_sugg, $allow_sugg;
	    global $opac_resa, $opac_resa_planning, $opac_resa_cart, $opac_resa_popup;
	    global $opac_scan_request_activate, $allow_scan_request;
	    global $opac_export_allow;
	    
	    $this->add_href_action('show_cart_empty', $msg['show_cart_empty'], $msg['show_cart_empty_title'], './index.php?lvl=show_cart&raz_cart=1');
	    $this->add_onclick_action('show_cart_del_checked', $msg['show_cart_del_checked'], $msg['show_cart_del_checked_title'], "document.cart_form.submit();");
	    $this->add_onclick_action('show_cart_print', $msg['show_cart_print'], $msg['show_cart_print_title'], "w=window.open('print.php?lvl=cart','print_window','width=500, height=750,scrollbars=yes,resizable=1'); w.focus();");
	    $this->add_onclick_action('show_cart_checked_all', $msg['show_cart_check_all'], $msg['show_cart_check_all'], "check_uncheck_all_cart();");
	    
	    if ($opac_cart_more_actions_activate) {
	        $this->add_onclick_action('show_cart_more_actions', $msg['show_cart_more_actions'], $msg['show_cart_more_actions'], "show_more_actions();");
	    }
	    if (!empty($opac_allow_download_docnums)) {
	        $this->add_onclick_action('docnum_download_caddie', $msg['docnum_download_caddie'], $msg['docnum_download_caddie'], "download_docnum();");
	        $this->add_onclick_action('docnum_download_checked', $msg['docnum_download_checked'], $msg['docnum_download_checked'], "download_docnum_notice_checked();");
	    }
	    if (!empty($opac_shared_lists) && !empty($allow_liste_lecture) && !empty($id_empr)) {
	        $this->add_href_action('list_lecture_transform_caddie', $msg['list_lecture_transform_caddie'], $msg['list_lecture_transform_caddie_title'], './index.php?lvl=show_list&sub=transform_caddie');
	        $this->add_onclick_action('list_lecture_transform_checked', $msg['list_lecture_transform_checked'], $msg['list_lecture_transform_checked_title'], "document.cart_form.action='./index.php?lvl=show_list&sub=transform_check';if(confirm_transform()) document.cart_form.submit(); else return false;");
	    }
	    if (!empty($opac_show_suggest) && !empty($opac_allow_multiple_sugg) && !empty($allow_sugg) && !empty($id_empr)) {
	        $this->add_onclick_action('transform_caddie_to_multisugg', $msg['transform_caddie_to_multisugg'], $msg['transform_caddie_to_multisugg_title'], "document.getElementById('div_src_sugg').style.display='';");
	        $this->add_onclick_action('transform_caddie_notice_to_multisugg', $msg['transform_caddie_notice_to_multisugg'], $msg['transform_caddie_to_multisugg_title'], "if(notice_checked()){ document.getElementById('div_src_sugg').style.display='';} else return false;");
	    }
	    //resas
	    if (!empty($opac_resa) && $opac_resa_planning != 1 && !empty($id_empr) && !empty($opac_resa_cart)) {
	        if (!empty($opac_resa_popup)) {
	            $this->add_onclick_action('show_cart_reserve', $msg['show_cart_reserve'], $msg['show_cart_reserve_title'], "w=window.open('./do_resa.php?lvl=resa_cart&sub=resa_cart','doresa','scrollbars=yes,width=900,height=300,menubar=0,resizable=yes'); w.focus(); return false;");
	            $this->add_onclick_action('show_cart_reserve_checked', $msg['show_cart_reserve_checked'], $msg['show_cart_reserve_checked_title'], "resa_cart_checked(true);");
	        } else {
	            $this->add_href_action('show_cart_reserve', $msg['show_cart_reserve'], $msg['show_cart_reserve_title'], "./do_resa.php?lvl=resa_cart&sub=resa_cart");
	            $this->add_onclick_action('show_cart_reserve_checked', $msg['show_cart_reserve_checked'], $msg['show_cart_reserve_checked_title'], "resa_cart_checked();");
            }
            //resas planifiees
	    } elseif(!empty($opac_resa) && $opac_resa_planning == '1' && !empty($id_empr) && !empty($opac_resa_cart)) {
	        if ($opac_resa_popup) {
	            $this->add_onclick_action('show_cart_reserve', $msg['show_cart_reserve'], $msg['show_cart_reserve_title'], "w=window.open('./do_resa.php?lvl=resa_cart&sub=resa_cart','doresa','scrollbars=yes,width=900,height=300,menubar=0,resizable=yes'); w.focus(); return false;");
	            $this->add_onclick_action('show_cart_reserve_checked', $msg['show_cart_reserve_checked'], $msg['show_cart_reserve_checked_title'], "resa_cart_checked(true, true);");
	        } else {
	            $this->add_href_action('show_cart_reserve', $msg['show_cart_reserve'], $msg['show_cart_reserve_title'], "./do_resa.php?lvl=resa_cart&sub=resa_planning_cart");
	            $this->add_onclick_action('show_cart_reserve_checked', $msg['show_cart_reserve_checked'], $msg['show_cart_reserve_checked_title'], "resa_cart_checked(false, true);");
	        }
	    }
	    // Demande de numérisation
	    if (!empty($opac_scan_request_activate) && !empty($allow_scan_request) && !empty($id_empr)) {
	        $this->add_href_action('scan_request_from_caddie', $msg['scan_request_from_caddie'], $msg['scan_request_from_caddie_title'], "./empr.php?tab=scan_requests&lvl=scan_request&sub=edit&from=caddie");
	        $this->add_onclick_action('scan_request_from_checked', $msg['scan_request_from_checked'], $msg['scan_request_from_checked_title'], "document.cart_form.action='./empr.php?tab=scan_requests&lvl=scan_request&sub=edit&from=checked';if(confirm_transform()) document.cart_form.submit(); else return false;");
	    }
	    if ($opac_export_allow == '1' || ($opac_export_allow == '2' && $_SESSION['user_code'])) {
	        $js_export_partiel = $this->get_js_export_partiel();
	        $this->add_onclick_action('show_cart_export', $msg['show_cart_export_ok'], $msg['show_cart_export_ok'], "$js_export_partiel if(getNoticeSelected()){ document.location='./export.php?action=export&typeexport='+document.export_form.typeexport.options[top.document.export_form.typeexport.selectedIndex].value+(typeof getNoticeSelected() != 'boolean' ? getNoticeSelected() : '');}}");
	    }
	}
	
	protected function get_display_multisugg_selector() {
	    global $msg, $charset;
	    
	    //Affichage du selecteur de source
	    $req = 'select * from suggestions_source order by libelle_source';
	    $res = pmb_mysql_query($req);
	    $option = '<option value="0" selected="selected">'.htmlentities($msg['empr_sugg_no_src'], ENT_QUOTES, $charset).'</option>';
	    while ($src = pmb_mysql_fetch_object($res)) {
	        $option .= "<option value='$src->id_source'>".htmlentities($src->libelle_source, ENT_QUOTES, $charset)."</option>";
	    }
	    return "<select id='sug_src' name='sug_src'>$option</select>";
	}
	
	protected function get_display_multisugg() {
	    global $msg;
	    
	    $display = '<div class="row" id="div_src_sugg" style="display:none" >';
	    $display .= '<label class="etiquette">'.$msg['empr_sugg_src'].': </label>';
	    $display .= $this->get_display_multisugg_selector();
	    $interface_node_button = new interface_node_button('transform_to_sugg_button');
	    $interface_node_button->set_value($msg[11])
	    ->set_onclick("document.cart_form.action='./empr.php?lvl=transform_to_sugg&act=transform_caddie&sug_src='+document.getElementById('sug_src').value;document.cart_form.submit();");
	    $display .= $interface_node_button->get_display();
	    $display .= '</div>';
	    return $display;
	}
	
	public function has_display_breakline($name) {
	    switch ($name) {
	        case 'docnum_download_caddie':
	        case 'list_lecture_transform_caddie':
	        case 'transform_caddie_to_multisugg':
	        case 'show_cart_reserve':
	        case 'scan_request_from_caddie':
	            return true;
	        default:
	            return false;
        }
	}
	
	protected function get_html_action($name, $action) {
	    global $charset;
	    
	    if ($action['type'] == 'link') {
	        if(!empty($action['href'])) {
	            return "<a id='".$name."' class='cart_action_link' title='".htmlentities($action['title'], ENT_QUOTES, $charset)."' href='".$action['href']."'>".htmlentities($action['label'], ENT_QUOTES, $charset)."</a>";
	        } else {
	            return "<a id='".$name."' class='cart_action_link' title='".htmlentities($action['title'], ENT_QUOTES, $charset)."' href='#' onClick=\"".$action['onclick']."\">".htmlentities($action['label'], ENT_QUOTES, $charset)."</a>";
	        }
	    } else {
	        $interface_node_button = new interface_node_button($name);
	        $interface_node_button->set_class('bouton cart_action_input')
	        ->set_value($action['label'])
	        ->set_title($action['title']);
	        if(!empty($action['href'])) {
	            $interface_node_button->set_onclick("document.location='".$action['href']."'");
	        } else {
	            $interface_node_button->set_onclick($action['onclick']);
	        }
	        return $interface_node_button->get_display();
	    }
	}
	
	protected function get_html_actions() {
	    global $msg, $charset;
	    global $opac_rgaa_active;
	    
	    $html_actions = [];
	    foreach ($this->actions as $name=>$action) {
	        $html_action = '';
			if(!$opac_rgaa_active){
				if ($this->has_display_breakline($name)) {
					$html_action .= "<br /><br />";
				} elseif (array_key_first($this->actions) != $name) {
					$html_action .= "<span class=\"espaceCartAction\">&nbsp;</span>";
				}
			}
	        switch ($name) {
	            case 'show_cart_more_actions':
					if($opac_rgaa_active){
						$html_action .= "<li id='list_action_".$name."' class='cart_action_item'>";
					}
	                $html_action .= $this->get_html_action($name, $action);
					if($opac_rgaa_active){
						$html_action .= "</li>";
						$html_action .= "<li id='show_more_actions' style='display: none;' class='cart_action_list_item'><ul id='cart_more_actions_list'>";
					}else{
						$html_action .= "<div id='show_more_actions' style='display: none;' class='cart_action_list_item'>";
					}
	                break;
	            case 'docnum_download_checked':
					if($opac_rgaa_active){
						$html_action .= "<li id='list_action_".$name."' class='cart_action_item'>";
					}
	                $html_action .= $this->get_html_action($name, $action);
	                $html_action .= "<div id='http_response'></div>";
					if($opac_rgaa_active){
						$html_action .= "</li>";
					}
	                break;
	            case 'transform_caddie_notice_to_multisugg':
					if($opac_rgaa_active){
						$html_action .= "<li id='list_action_".$name."' class='cart_action_item'>";
					}
	                $html_action .= $this->get_html_action($name, $action);
	                $html_action .= $this->get_display_multisugg();
					if($opac_rgaa_active){
						$html_action .= "</li>";
					}
	                break;
	            case 'show_cart_export':
					if($opac_rgaa_active){
						$html_action .= "<li id='list_action_".$name."' class='cart_action_item'>";
					}
	                $html_action .= "<form name='export_form'><br />";
	                
	                if($opac_rgaa_active) {
	                    $html_action .= "
                        <fieldset>
                            <legend class='visually-hidden'>".htmlentities(sprintf($msg['show_cart_export'], ''), ENT_QUOTES, $charset)."</legend>
	                           <div class='cart_selector_export'>
	                               <label for='typeexport_selector'>".sprintf($msg['show_cart_export'], '')."</label>
	                               ".$this->get_display_exports_selector()."
                               </div>";
	                } else {
	                    $html_action .= sprintf($msg['show_cart_export'], '');
	                    $html_action .= $this->get_display_exports_selector();
	                    $html_action .= "<br />";
	                }
	                $html_action .= $this->get_display_exports_radio();
	                $html_action .= "<span class=\"espaceCartAction\">&nbsp;</span>";
	                $html_action .= $this->get_html_action($name, $action);
	                if($opac_rgaa_active) {
	                    $html_action .= "
                        </fieldset>";
	                }
	                $html_action .= '</form>';
					if($opac_rgaa_active){
						$html_action .= "</li>";
					}
	                break;
	            default:
					if($opac_rgaa_active){
						$html_action .= "<li id='list_action_".$name."' class='cart_action_item'>";
					}
	                $html_action .= $this->get_html_action($name, $action);
					if($opac_rgaa_active){
						$html_action .= "</li>";
					}
	                break;
	        }
	        $html_actions[$name] = $html_action;
	    }
	    return $html_actions;
	}
	
	public function get_display_actions() {
	    global $opac_cart_more_actions_activate;
	    global $opac_rgaa_active;


		$display = "";
		if($opac_rgaa_active){
			// ouverture de la premiere liste
			$display .= "<ul id='cart_action_list'>";
		}

	    $this->_init_actions();
	    $html_actions = $this->get_html_actions();
	    if(!empty($html_actions)) {
// 	        $display .= implode("<span class=\"espaceCartAction\">&nbsp;</span>", $html_actions);
	        $display .= implode("", $html_actions);
	    }
		if($opac_rgaa_active){
			// fermeture de la premiere liste
			$display .= '</ul>';
		}
	    if ($opac_cart_more_actions_activate) {
			if($opac_rgaa_active){
				// fermeture de la seconde liste
	        	$display .= '</ul></li>';
			}else{
				$display .= '</div>';
			}
	    }
	    return $display;
	}
	
	public function get_display_title_entities() {
	    global $msg;
	    global $opac_rgaa_active;
	    
	    $nb_session_cart = count($this->session_cart);
	    if ($nb_session_cart) {
	        return common::format_title($msg['show_cart_content'].' : <b>'.sprintf($msg['show_cart_n_notices'], $nb_session_cart).'</b>', true);
	    } else {
	        if ($opac_rgaa_active) {
	            return '<h1 class="empty_cart"><span>'.$msg['show_cart_is_empty'].'</span></h1>';
	        } else {
	            return '<h3 class="empty_cart"><span>'.$msg['show_cart_is_empty'].'</span></h3>';
	        }
	    }
	}
	
	public function get_display_action_sort() {
	    global $opac_nb_max_tri;
	    global $msg;
	    
	    $display = "";
	    if (count($this->session_cart) <= $opac_nb_max_tri) {
	        $affich_tris_result_liste = sort::show_tris_selector();
	        $affich_tris_result_liste = str_replace('!!page_en_cours!!', urlencode('lvl=show_cart'), $affich_tris_result_liste);
	        $affich_tris_result_liste = str_replace('!!page_en_cours1!!', 'lvl=show_cart', $affich_tris_result_liste);
	        $display .= $affich_tris_result_liste;
	    }
	    
	    if (isset($_SESSION['last_sortnotices']) && $_SESSION['last_sortnotices'] !== "") {
	        $display .= "<span class='sort'>".$msg['tri_par'].' '.static::get_sort()->descriptionTriParId($_SESSION['last_sortnotices']).'<span class="espaceCartAction">&nbsp;</span></span>';
	    }
	    return $display;
	}
	
	public function get_display_list_entities() {
	    global $opac_search_results_per_page;
	    global $page, $cart_aff_case_traitement;
	    
	    if (!isset($page) || $page == '') {
	        $page = 1;
	    }
	    
	    $display = '<blockquote role="presentation">';
	    
	    // case à cocher de suppression transférée dans la classe notice_affichage
	    $cart_aff_case_traitement = 1 ;
	    $display .= "<form action='./index.php?lvl=show_cart&action=del&page=$page' method='post' name='cart_form'>";
	    
	    $nb_session_cart = count($this->session_cart);
	    for ($i = (($page - 1) * $opac_search_results_per_page); ($i < $nb_session_cart && ($i < ($page * $opac_search_results_per_page))); $i++) {
	        if (substr($this->session_cart[$i], 0, 2) != 'es') {
	            $display .= pmb_bidi(aff_notice($this->session_cart[$i], 1));
	        } else {
	            $display .= pmb_bidi(aff_notice_unimarc(substr($this->session_cart[$i], 2), 1));
	        }
	    }
	    $display .= "</form></blockquote>";
	    return $display;
	}
	
	public function get_display_pager() {
	    global $nb_per_page_custom;
	    global $page;
	    global $opac_search_results_per_page;
	    
	    if(!isset($nb_per_page_custom)) {
	        $nb_per_page_custom = '';
	    }
	    $nb_session_cart = count($this->session_cart);
	    return '
        <div id="cart_navbar">
            <hr />
            <div style="text-align:center">'.printnavbar($page, $nb_session_cart, $opac_search_results_per_page, './index.php?lvl=show_cart&page=!!page!!&nbr_lignes='.$nb_session_cart.($nb_per_page_custom ? "&nb_per_page_custom=".$nb_per_page_custom : '')).'
            </div>
        </div>';
	}
	
	protected function get_js_export_partiel() {
	    global $msg;
	    
	    $nb_fiche = 0;
	    $nb_fiche_total = 0;
	    if (!empty($this->session_cart) && is_countable($this->session_cart)) {
    	    foreach ($this->session_cart as $object_id) {
    	        $query = "";
    	        if (substr($object_id, 0, 2) != "es") {
    	            // Exclure de l'export (opac, panier) les fiches interdites de diffusion dans administration, Notices > Origines des notices NG72
    	            $query = "select 1 from origine_notice,notices where notice_id = '$object_id' and origine_catalogage = orinot_id and orinot_diffusion='1'";
    	        } else {
    	            $requete = "SELECT source_id FROM external_count WHERE rid=".addslashes(substr($object_id, 2));
    	            $myQuery = pmb_mysql_query($requete);
    	            if(pmb_mysql_num_rows($myQuery)) {
    	                $source_id = pmb_mysql_result($myQuery, 0, 0);
    	                $query = "select 1 from entrepot_source_$source_id where recid='".addslashes(substr($object_id, 2))."' group by ufield,usubfield,field_order,subfield_order,value";
    	            }
    	        }
    	        if($query) {
    	            $res = pmb_mysql_query($query);
    	            if (!empty(pmb_mysql_fetch_array($res))) {
    	                $nb_fiche++;
    	            }
    	        }
    	    }
    	    $nb_fiche_total = count($this->session_cart);
	    }
	    if ($nb_fiche != $nb_fiche_total) {
	        $msg_export_partiel = str_replace ('!!nb_export!!', $nb_fiche, $msg['export_partiel']);
	        $msg_export_partiel = str_replace ('!!nb_total!!', $nb_fiche_total, $msg_export_partiel);
	        return "if (confirm('".addslashes($msg_export_partiel)."')) {";
	    } else {
	        return "if (true) {";
	    }
	}
	
	protected function get_display_exports_radio() {
	    global $msg, $charset;
	    
	    return "
        <div class='cart_radio_export_all'>
		    <input type='radio' name='radio_exp' id='radio_exp_all' value='0' checked />
		    <label for='radio_exp_all'>".htmlentities($msg['export_cart_all'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='cart_radio_export_sel'>
    		<input type='radio' name='radio_exp' id='radio_exp_sel' value='1' />
    		<label for='radio_exp_sel'>".htmlentities($msg['export_cart_selected'], ENT_QUOTES, $charset)."</label>
		</div>";
	}
	
	protected function get_display_exports_selector() {
	    $exp = start_export::get_exports();
	    $selector = "<select id='typeexport_selector' name='typeexport'>" ;
	    for ($i = 0; $i < count($exp); $i++) {
	        $selector .= "<option value='".$exp[$i]['ID']."'>".$exp[$i]['NAME']."</option>";
	    }
	    $selector .= "</select>" ;
	    return $selector;
	}
	
	public function get_session_cart() {
	    return $this->session_cart;
	}
	
	public static function get_sort() {
	    if (empty(static::$sort)) {
	        static::$sort = new sort('notices', 'session');
	    }
	    return static::$sort;
	}
	
	/**
	 * Tri
	 */
	public static function sort() {
	    $cart_ = (isset($_SESSION['cart']) ? $_SESSION['cart'] : array());
	    
	    //gestion des notices externes (sauvegarde)
	    $cart_ext = array();
	    if (is_countable($cart_)) {
	        for ($i = 0; $i < sizeof($cart_); $i++){
	            if (strpos($cart_[$i], 'es') !== false) {
	                $cart_ext[] = $cart_[$i];
	            }
	        }
	    }
	    if (isset($_SESSION['last_sortnotices']) && $_SESSION['last_sortnotices'] !=='') {
	        $query = "SELECT notice_id FROM notices WHERE notice_id IN (";
	        for ($z = 0; $z < count($cart_); $z++) {
	            $query .= "'". $cart_[$z] ."',";
	        }
	        $query = substr($query, 0, strlen($query) - 1) .")";
	        $query = static::get_sort()->appliquer_tri($_SESSION['last_sortnotices'], $query, 'notice_id', 0, 0);
	    } else {
	        $query = "select notice_id from notices where notice_id in ('".implode("','", $cart_)."') order by index_serie, tnvol, index_sew";
	    }
	    $res = pmb_mysql_query($query);
	    $cart_ = array();
	    while ($r = pmb_mysql_fetch_object($res)) {
	        $cart_[] = $r->notice_id;
	    }
	    if (!empty($cart_ext)) {
	        $cart_ = array_merge($cart_, $cart_ext);
	    }
	    $_SESSION['cart'] = $cart_;
	}
	
	public static function list_delete($objects) {
	    $cart_ = (isset($_SESSION['cart']) ? $_SESSION['cart'] : array());
	    for ($i = 0; $i < count($objects); $i++) {
	        $as = array_search($objects[$i], $cart_);
	        if ($as !== null && $as !== false) {
	            //Décalage
	            for ($j = $as + 1; $j < count($cart_); $j++) {
	                $cart_[$j - 1] = $cart_[$j];
	            }
	            unset($cart_[count($cart_) - 1]);
	        }
	    }
	    $_SESSION['cart'] = $cart_;
	}
	
	public static function raz() {
	    $_SESSION['cart'] = [];
	}
	
	public static function change_basket_image($id, $action=''){
	    global $header;
	    
	    if (empty($header)) {
	        $header = '';
	    }
	    print "<script>
			var pmb_img_basket_small_20x20 = '".get_url_icon('basket_small_20x20.png')."';
			var pmb_img_basket_exist = '".get_url_icon('basket_exist.png')."';
			var pmb_img_white_basket = '".get_url_icon('white_basket.png')."';
			var pmb_img_record_in_basket = '".get_url_icon('record_in_basket.png')."';
			var pmb_img_extended_record_in_basket = '".get_url_icon('extended_record_in_basket.png')."';
			var pmb_img_extended_record_white_basket = '".get_url_icon('extended_record_white_basket.png')."';
			changeBasketImage('".$id."', '".$action."', \"".rawurlencode(stripslashes($header))."\");
		</script>";
	}
	
	public static function add($id) {
	    global $msg, $charset, $opac_max_cart_items;
	    global $header, $opac_simplified_cart;
	    global $cart_;
	    
	    $message = "";
	    if (count($cart_)<$opac_max_cart_items) {
	        $as=array_search($id,$cart_);
	        $notice_header=htmlentities(substr(strip_tags(stripslashes(html_entity_decode($header,ENT_QUOTES))),0,45),ENT_QUOTES,$charset);
	        if ($notice_header != $header) {
	            $notice_header.="...";
	        }
	        if (($as!==null)&&($as!==false)) {
	            $message = sprintf($msg["cart_notice_exists"],$notice_header);
	        } else {
	            $cart_[] = $id;
	            $message = sprintf($msg["cart_notice_add"],$notice_header);
	            static::change_basket_image($id);
	        }
	        if ($opac_simplified_cart) {
	            $message = "";
	        }
	    } else {
	        $message = $msg["cart_full".($opac_simplified_cart?'_simplified':'')];
	    }
	    return $message;
	}
	
	public static function remove($id) {
	    global $cart_;
	    $as=array_search($id,$cart_);
	    if (($as!==null)&&($as!==false)) {
	        unset($cart_[$as]);
	        static::change_basket_image($id, 'remove');
	    }
	}
	
	public static function add_entities($entities){
	    global $cart_;
	    global $opac_max_cart_items;
	    global $msg;
	    global $opac_simplified_cart;
	    
	    $n=0; $na=0;
	    $tab_entities = explode(",",$entities);
	    $nbtotal=count($tab_entities);
	    for($i=0 ; $i<$nbtotal; $i++){
	        if (count($cart_)<$opac_max_cart_items) {
	            $as=array_search($tab_entities[$i],$cart_);
	            if (($as===null)||($as===false)) {
	                $cart_[]=$tab_entities[$i];
	                static::change_basket_image($tab_entities[$i]);
	                $n++;
	            } else $na++;
	        }
	    }
	    $message = "";
	    if (count($cart_)==$opac_max_cart_items){
	        $message=$msg["cart_full".($opac_simplified_cart?'_simplified':'')];
	    }
	    return $message;
	}
	
	public static function add_from_section($id, $location, $plettreaut, $dcote, $lcote) {
	    global $default_tmp_storage_engine;
	    global $nc, $ssub;
	    
	    $lcote = intval($lcote);
	    //On regarde dans quelle type de navigation on se trouve
	    $requete="SELECT num_pclass FROM docsloc_section WHERE num_location='".$location."' AND num_section='".$id."' ";
	    $res=pmb_mysql_query($requete);
	    $type_aff_navigopac=0;
	    if(pmb_mysql_num_rows($res)){
	        $type_aff_navigopac=pmb_mysql_result($res,0,0);
	    }
	    
	    if($type_aff_navigopac == 0 or ($type_aff_navigopac == -1 && !$plettreaut)or ($type_aff_navigopac != -1 && $type_aff_navigopac != 0 && !isset($dcote) && !isset($nc))){
	        //Pas de navigation ou navigation par les auteurs mais sans choix effectué
	        $requete="create temporary table temp_n_id ENGINE={$default_tmp_storage_engine} ( select distinct expl_notice as notice_id from exemplaires where expl_section='".$id."' and expl_location='".$location."' )";
	        pmb_mysql_query($requete);
	        //On récupère les notices de périodique avec au moins un exemplaire d'un bulletin dans la localisation et la section
	        $requete="INSERT INTO temp_n_id (select distinct bulletin_notice as notice_id from bulletins join exemplaires on bulletin_id=expl_bulletin where expl_section='".$id."' and expl_location='".$location."' )";
	        pmb_mysql_query($requete);
	        pmb_mysql_query("alter table temp_n_id add index(notice_id)");
	        $requete = "SELECT notice_id FROM temp_n_id ";
	        
	    }elseif($type_aff_navigopac == -1 ){
	        
	        $requete="create temporary table temp_n_id ENGINE={$default_tmp_storage_engine} ( SELECT distinct expl_notice as notice_id from exemplaires where expl_section='".$id."' and expl_location='".$location."' )";
	        pmb_mysql_query($requete);
	        //On récupère les notices de périodique avec au moins un exemplaire d'un bulletin dans la localisation et la section
	        $requete="INSERT INTO temp_n_id (select distinct bulletin_notice as notice_id from bulletins join exemplaires on bulletin_id=expl_bulletin where expl_section='".$id."' and expl_location='".$location."' )";
	        pmb_mysql_query($requete);
	        
	        if($plettreaut == "num"){
	            $requete = "SELECT temp_n_id.notice_id FROM temp_n_id JOIN responsability ON responsability_notice=temp_n_id.notice_id JOIN authors ON author_id=responsability_author and trim(index_author) REGEXP '^[0-9]' GROUP BY temp_n_id.notice_id";
	        }elseif($plettreaut == "vide"){
	            $requete = "SELECT temp_n_id.notice_id FROM temp_n_id LEFT JOIN responsability ON responsability_notice=temp_n_id.notice_id WHERE responsability_author IS NULL GROUP BY temp_n_id.notice_id";
	        }else{
	            $requete = "SELECT temp_n_id.notice_id FROM temp_n_id JOIN responsability ON responsability_notice=temp_n_id.notice_id JOIN authors ON author_id=responsability_author and trim(index_author) REGEXP '^[".$plettreaut."]' GROUP BY temp_n_id.notice_id";
	        }
	        
	    }else{
	        
	        //Navigation par plan de classement
	        
	        //Table temporaire de tous les id
	        if ($ssub) {
	            $t_dcote=explode(",",$dcote);
	            $t_expl_cote_cond=array();
	            for ($i=0; $i<count($t_dcote); $i++) {
	                $t_expl_cote_cond[]="expl_cote regexp '(^".$t_dcote[$i]." )|(^".$t_dcote[$i]."[0-9])|(^".$t_dcote[$i]."$)|(^".$t_dcote[$i].".)'";
	            }
	            $expl_cote_cond="(".implode(" or ",$t_expl_cote_cond).")";
	        }else{
	            $expl_cote_cond= " expl_cote regexp '".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote))."' and expl_cote not regexp '(\\\\.[0-9]*".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")|([^0-9]*[0-9]+\\\\.?[0-9]*.+".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")' ";
	        }
	        $requete="create temporary table temp_n_id ENGINE={$default_tmp_storage_engine} select distinct expl_notice as notice_id from exemplaires where expl_location=$location and expl_section='$id' " ;
	        if (strlen($dcote)) {
	            $requete.= " and $expl_cote_cond ";
	            $level_ref=strlen($dcote)+1;
	        }
	        pmb_mysql_query($requete);
	        
	        $requete2 = "insert into temp_n_id (SELECT distinct bulletin_notice as notice_id FROM bulletins join exemplaires on expl_bulletin=bulletin_id where expl_location=$location and expl_section=$id ";
	        if (strlen($dcote)) {
	            $requete2.= " and $expl_cote_cond ";
	        }
	        $requete2.= ") ";
	        pmb_mysql_query($requete2);
	        pmb_mysql_query("alter table temp_n_id add index(notice_id)");
	        
	        //Calcul du classement
	        $rq1_index="create temporary table union1 ENGINE={$default_tmp_storage_engine} (select distinct expl_cote from exemplaires, temp_n_id where expl_location='".$location."' and expl_section='".$id."' and expl_notice=temp_n_id.notice_id) ";
	        pmb_mysql_query($rq1_index);
	        $rq2_index="create temporary table union2 ENGINE={$default_tmp_storage_engine} (select distinct expl_cote from exemplaires join (select distinct bulletin_id from bulletins join temp_n_id where bulletin_notice=notice_id) as sub on (bulletin_id=expl_bulletin) where expl_location='".$location."' and expl_section='".$id."') ";
	        pmb_mysql_query($rq2_index);
	        $req_index="select distinct expl_cote from union1 union select distinct expl_cote from union2";
	        $res_index=pmb_mysql_query($req_index);
	        
	        if ($level_ref==0) $level_ref=1;
	        
	        while (($ct=pmb_mysql_fetch_object($res_index)) && $nc) {
	            $c = [];
	            if (preg_match("/[0-9][0-9][0-9]/",$ct->expl_cote,$c)) {
	                $found=false;
	                $lcote=(strlen($c[0])>=3) ? 3 : strlen($c[0]);
	                $level=$level_ref;
	                while ((!$found)&&($level<=$lcote)) {
	                    $cote=substr($c[0],0,$level);
	                    $compl=str_repeat("0",$lcote-$level);
	                    $rq_index="select indexint_name,indexint_comment from indexint where indexint_name='".$cote.$compl."' and length(indexint_name)>=$lcote and num_pclass='".$type_aff_navigopac."' order by indexint_name limit 1";
	                    $res_index_1=pmb_mysql_query($rq_index);
	                    if (pmb_mysql_num_rows($res_index_1)) {
	                        $rq_del="select distinct notice_id from notices, exemplaires where expl_cote='".$ct->expl_cote."' and expl_notice=notice_id ";
	                        $rq_del.=" union select distinct notice_id from notices, exemplaires, bulletins where expl_cote='".$ct->expl_cote."' and expl_bulletin=bulletin_id and bulletin_notice=notice_id ";
	                        $res_del=pmb_mysql_query($rq_del) ;
	                        while (list($n_id)=pmb_mysql_fetch_row($res_del)) {
	                            pmb_mysql_query("delete from temp_n_id where notice_id=".$n_id);
	                        }
	                        $found=true;
	                    } else $level++;
	                }
	            }
	        }
	        $requete = "SELECT notice_id FROM temp_n_id " ;
	    }
	    
	    $r =pmb_mysql_query($requete);
	    if (pmb_mysql_num_rows($r)) {
	        $tab_notices=array();
	        while($row=pmb_mysql_fetch_object($r)) {
	            $tab_notices[]=$row->notice_id;
	        }
	        $notices=implode(',',$tab_notices);
	        $fr = new filter_results($notices);
	        $notices = $fr->get_results();
	        return static::add_entities($notices);
	    }
	    return '';
	}
	
	public static function add_from_concept($id) {
	    $concept = new skos_concept($id);
	    $notices = implode(",", $concept->get_indexed_notices());
	    $fr = new filter_results($notices);
	    $notices = $fr->get_results();
	    return static::add_entities($notices);
	}
	
	public static function add_from_liste_lecture($id) {
	    $liste = new liste_lecture($id);
	    $notices = implode(',', $liste->notices);
	    if ($notices) {
	        $fr = new filter_results($notices);
	        $notices = $fr->get_results();
	    }
	    return static::add_entities($notices);
	}
	
	public static function add_query($requete) {
	    global $cart_;
	    global $opac_max_cart_items;
	    global $msg;
	    global $opac_simplified_cart;
	    
	    $resultat = pmb_mysql_query($requete);
	    $nbtotal = pmb_mysql_num_rows($resultat);
	    $n=0; $na=0;
	    while ($r=pmb_mysql_fetch_object($resultat)) {
	        if (is_countable($cart_) && count($cart_)<$opac_max_cart_items) {
	            $as=array_search($r->notice_id,$cart_);
	            if (($as===null)||($as===false)) {
	                $cart_[]=$r->notice_id;
	                $n++;
	            } else $na++;
	        }
	    }
	    $message=sprintf($msg["cart_add_notices"],$n,$nbtotal);
	    if ($na) $message.=", ".sprintf($msg["cart_already_in"],$na);
	    if ($opac_simplified_cart) {
	        $message="";
	    }
	    if (is_countable($cart_) && count($cart_)==$opac_max_cart_items){
	        if ($opac_simplified_cart) {
	            $message=$msg["cart_full_simplified"];
	        } else {
	            $message.=", ".$msg["cart_full"];
	        }
	    }
	    return $message;
	}
	
	public static function integrate_anonymous_cart(){
	    global $cart_integrate_anonymous_on_confirm;
	    global $opac_max_cart_items;
	    global $cart_script;
	    global $msg;
	    if(isset($_SESSION['cart_anonymous']) && is_countable($_SESSION['cart_anonymous']) && count($_SESSION['cart_anonymous'])){ //Un panier anonyme est présent pour ce lecteur
	        $cart_script = $cart_integrate_anonymous_on_confirm;
	        $nb_record = count(array_unique(array_merge($_SESSION['cart_anonymous'], $_SESSION['cart'])));
	        if($nb_record > $opac_max_cart_items){
	            //Proposer de choisir un des deux paniers
	            $cart_script = str_replace('!!cart_confirm_message!!', $msg['cart_anonymous_alert_replace'], $cart_script);
	            $cart_script = str_replace('!!cart_ajax_action!!', 'keep_anonymous_cart', $cart_script);
	        }else{
	            //Proposer l'injection du panier anonyme dans le panier du lecteur
	            $cart_script = str_replace('!!cart_confirm_message!!', $msg['cart_anonymous_alert_merge'], $cart_script);
	            $cart_script = str_replace('!!cart_ajax_action!!', 'merge_cart', $cart_script);
	        }
	        return $cart_script;
	    }
	    return '';
	}
	
	public static function get_display_label() {
	    global $msg;
	    global $opac_simplified_cart;
	    global $cart_;
	    global $opac_rgaa_active;
	    
	    if(!count($cart_)) {
	        if ($opac_simplified_cart) {
	            $label = trim($msg["cart_empty_simplified"]);
	            if ($opac_rgaa_active) {
	                $cleaned_label = trim(strip_tags($label));
	                if (pmb_strlen($label) != pmb_strlen($cleaned_label) && pmb_strpos($cleaned_label, '(') === 0) {
	                    return $msg["iframe_cart_info"]." ".$label;
	                }
	                if (strpos($cleaned_label, '(') === 0) {
	                    return $msg["iframe_cart_info"]." <span class='pmb_basket_number'>".$label."</span>";
	                }
	            }
	            return $label;
	        } else {
	            return $msg["cart_empty"];
	        }
	        return $msg["cart_empty".($opac_simplified_cart?'_simplified':'')];
	    } else {
	        if ($opac_simplified_cart) {
	            $label = trim(sprintf($msg["cart_contents_simplified"],count($cart_)));
	            if ($opac_rgaa_active) {
    	            $cleaned_label = trim(strip_tags($label));
    	            if (pmb_strlen($label) != pmb_strlen($cleaned_label) && pmb_strpos($cleaned_label, '(') === 0) {
	                    return $msg["iframe_cart_info"]." ".$label;
	                }
	                if (strpos($cleaned_label, '(') === 0) {
	                    return $msg["iframe_cart_info"]." <span class='pmb_basket_number'>".$label."</span>";
	                }
	            }
	            return $label;
	        } else {
	            return sprintf($msg["cart_contents"],count($cart_));
	        }
	    }
	}
}
