<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_caddie_controller.class.php,v 1.31 2019-06-07 09:53:06 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($class_path . "/caddie/caddie_root_controller.class.php");
require_once ($class_path . "/authorities_caddie.class.php");

class authorities_caddie_controller extends caddie_root_controller {

    protected static $model_class_name = 'authorities_caddie';
    protected static $procs_class_name = 'authorities_caddie_procs';

    public static function get_template_layout() {
        global $autorites_layout;
        return $autorites_layout;
    }

    public static function get_aff_paniers_from_panier($idcaddie = 0, $sub = '') {
    	global $msg;
    	
    	$idcaddie += 0;
    	static::$title = $msg['caddie_select_pointe_panier'];
    	static::$action_click = "choix_quoi";
    	static::$lien_origine = static::get_constructed_link($sub) . "&moyen=panier&idcaddie_selected=".$idcaddie;
    	$display = "<script type='text/javascript' src='./javascript/tablist.js'></script>";
    	$display .= "<hr />";
    	$display .= static::get_display_list("display");
    	$display .= "<div class='row'><hr /></div>";
    	print $display;
    }
    
    public static function get_aff_paniers($sub = '', $sub_action = '', $moyen = '') {
        global $msg;

        switch ($sub) {
            case 'action':
                switch ($sub_action) {
                    case 'edition':
                        static::$title = $msg["caddie_select_edition"];
                        static::$action_click = "choix_quoi";
                        break;
                    case 'export':
                        static::$title = $msg["caddie_select_export"];
                        static::$action_click = "choix_quoi";
                        break;
                    case 'selection':
                        static::$title = $msg["caddie_select_for_action"];
                        static::$action_click = "";
                        break;
                    case 'supprpanier':
                        static::$title = $msg["caddie_select_supprpanier"];
                        static::$action_click = "choix_quoi";
                        break;
                    case 'supprbase':
                        static::$title = $msg['caddie_select_supprbase'];
                        static::$action_click = "choix_quoi";
                        break;
                    case 'reindex':
                        static::$title = $msg['caddie_action_reindex'];
                        static::$action_click = "choix_quoi";
                        break;
                }
                static::$lien_origine = static::get_constructed_link($sub, $sub_action);
                break;
            case 'pointage':
                switch ($moyen) {
                    case 'panier':
						static::$title = $msg['caddie_select_pointe'];
                        break;
                    case 'raz':
                        static::$title = $msg['caddie_pointage_raz'];
                        break;
                    default:
                        static::$title = $msg['caddie_select_pointe'];
                        break;
                }
                static::$lien_origine = static::get_constructed_link($sub) . ($sub_action ? "&quoi=" . $sub_action : "") . ($moyen ? "&moyen=" . $moyen : "");
                static::$action_click = "";
                break;
            case 'collecte':
                static::$title = $msg["caddie_select_ajouter"];
                static::$lien_origine = static::get_constructed_link($sub) . ($sub_action ? "&quoi=" . $sub_action : "") . ($moyen ? "&moyen=" . $moyen : "");
                static::$action_click = "";
                break;
        }
        static::$object_type = "AUTHORS";

        $display = "<script type='text/javascript' src='./javascript/tablist.js'></script>";
        $display .= "<hr />";
        $display .= confirmation_delete(static::$lien_origine . "&action=del_cart&object_type=" . static::$object_type . "&item=0&idcaddie=");
        $display .= static::get_display_list("display");
        $display .= "<div class='row'><hr /></div>";
        print $display;
// 		return aff_paniers(0, "NOTI", $lien_origine, $action_click, $title, "", 0, 0, 0);
    }

    public static function get_aff_editable_paniers($item = 0) {
        global $msg;
        global $action;
        static::$lien_origine = "./autorites.php?categ=caddie&sub=gestion&quoi=panier";
        static::$action_click = "";
        $lien_edition_panier_cst = "<input type=button class=bouton value='$msg[caddie_editer]' onclick=\"document.location='" . static::$lien_origine . "&action=edit_cart&idcaddie=!!idcaddie!!';\" />";
        static::$object_type = "AUTHORS";

        $display = "<script type='text/javascript' src='./javascript/tablist.js'></script>";
        if ($item)
            $display .= "<form name='print_options' action='" . static::$lien_origine . "&action=" . static::$action_click . "&object_type=" . static::$object_type . "&item=$item' method='post'>";
// 		if($action!="save_cart") $display .= "<input type='checkbox' name='include_child' >&nbsp;".$msg["cart_include_child"];
        $display .= "<hr />";
        $display .= confirmation_delete(static::$lien_origine . "&action=del_cart&object_type=" . static::$object_type . "&item=$item&idcaddie=");
        $display .= static::get_display_list("editable");
        $display .= "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
        $display .= "<div class='row'><hr />";
        if ($item && $action != "save_cart") {
            $display .= "<input type='submit' value='" . $msg["print_cart_add"] . "' class='bouton'/>&nbsp;<input type='button' value='" . $msg["print_cancel"] . "' class='bouton' onClick='self.close();'/>&nbsp;";
        }
        $display .= static::get_create_button($item) . "
		</div>";
        if ($item)
            $display .="</form>";
        print $display;
    }

    public static function get_aff_paniers_in_cart($object_type = '', $item = 0) {
        global $msg;

        $display = "<form name='print_options' action='cart.php?&action=add_item&object_type=" . $object_type . "&item=$item' method='post'>";
        $display .= "<input type='hidden' id='idcaddie' name='idcaddie' >";
        $display .= "<hr />";
        $display .= "<input class='bouton' type='button' value=' " . $msg['new_cart'] . " ' onClick=\"document.location='cart.php?action=new_cart&object_type=" . $object_type . "&item=$item'\" />";
        $display .= static::get_display_list("in_cart", $object_type);
        $display .= "<input type='submit' value='" . $msg["print_cart_add"] . "' class='bouton'/>&nbsp;<input type='button' value='" . $msg["print_cancel"] . "' class='bouton' onClick='self.close();'/>&nbsp;";
        $display .= "<input class='bouton' type='button' value=' " . $msg['new_cart'] . " ' onClick=\"document.location='cart.php?action=new_cart&object_type=" . $object_type . "&item=$item'\" />";
        $display .= "<input type='hidden' name='current_print' value='" . $_SESSION['CURRENT'] . "'/>";
        $display .= "<div class='row'><hr /></div>";
        $display .= "</form>";
        print $display;
    }

    public static function get_object_instance($caddie_id = 0) {
        return new authorities_caddie($caddie_id);
    }

    public static function get_constructed_link($sub = '', $sub_categ = '', $action = '', $idcaddie = 0, $args_others = '') {
        global $base_path;

        $link = $base_path . "/autorites.php?categ=caddie&sub=" . $sub;
        if ($sub_categ) {
            switch ($sub) {
                case 'gestion':
                    $link .= "&quoi=" . $sub_categ;
                    break;
                case 'collecte':
                case 'pointage':
                    $link .= "&moyen=" . $sub_categ;
                    break;
                case 'action':
                    $link .= "&quelle=" . $sub_categ;
                    break;
            }
        }
        if ($action) {
            $link .= "&action=" . $action;
        }
        if ($args_others) {
            $link .= $args_others;
        }
        if ($idcaddie) {
            $link .= "&idcaddie=" . $idcaddie;
        }
        return $link;
    }

    public static function proceed_selection($idcaddie = 0, $sub = '', $quelle = '', $moyen = '') {
        global $msg, $charset;
        global $action;
        global $id;
        global $elt_flag, $elt_no_flag;
        global $cart_choix_quoi_action;
		global $erreur_explain_rqt;
		
		$idcaddie = intval($idcaddie);
        $id = intval($id);
        if ($idcaddie) {
            $myCart = static::get_object_instance($idcaddie);
            print pmb_bidi($myCart->aff_cart_titre());
            if ($sub == 'action') {
                if ((($action == "form_proc") || ($action == "add_item")) && ((!$elt_flag) && (!$elt_no_flag))) {
                    error_message_history($msg["caddie_no_elements"], $msg["caddie_no_elements_for_cart"], 1);
                    exit();
                }
            }
            switch ($action) {
                case 'form_proc' :
                    $hp = new parameters($id, "authorities_caddie_procs");
                    if ($sub == 'action') {
                        $hp->gen_form(static::get_constructed_link($sub, $quelle, 'add_item', $idcaddie, "&id=$id&elt_flag=$elt_flag&elt_no_flag=$elt_no_flag"));
                    } else {
                        if ($sub == 'pointage') {
                            $action_in_form = 'pointe_item';
                        } else {
                            $action_in_form = 'add_item';
                        }
                        $hp->gen_form(static::get_constructed_link($sub, $moyen, $action_in_form, $idcaddie, "&id=$id"));
                    }
                    break;
                case 'pointe_item':
                	$model_class_name = static::get_model_class_name();
                	print $model_class_name::show_actions($idcaddie,static::$object_type);
                    if (authorities_caddie_procs::check_rights($id)) {
                        $hp = new parameters($id, "authorities_caddie_procs");
                        $hp->get_final_query();
                        echo "<hr />" . $hp->final_query . "<hr />";
                        $myCart->pointe_items_from_query($hp->final_query);
                    }
                    print pmb_bidi($myCart->aff_cart_nb_items());
                    break;
                case 'add_item':
                	$model_class_name = static::get_model_class_name();
                	print $model_class_name::show_actions($idcaddie,static::$object_type);
                    //C'est ici qu'on fait une action
                    if (authorities_caddie_procs::check_rights($id)) {
                        $hp = new parameters($id, "authorities_caddie_procs");
                        $hp->get_final_query();
                        print "<hr />" . $hp->final_query . "<hr />";
                        switch ($sub) {
                            case 'collecte':
                                print pmb_bidi($myCart->add_items_by_collecte_selection($hp->final_query));
                                break;
                            case 'action':
                                if (!explain_requete($hp->final_query))
                                    die("<br /><br />" . $hp->final_query . "<br /><br />" . $msg["proc_param_explain_failed"] . "<br /><br />" . $erreur_explain_rqt);
                                $myCart->update_items_by_action_selection($hp->final_query);
                                break;
                        }
                    }
                    print $myCart->aff_cart_nb_items();
                    if ($sub == 'action') {
                        echo "<hr /><input type='button' class='bouton' value='" . $msg["caddie_menu_action_suppr_panier"] . "' onclick='document.location=&quot;./autorites.php?categ=caddie&amp;sub=action&amp;quelle=supprpanier&amp;action=choix_quoi&amp;object_type=".static::$object_type."&amp;idcaddie=".$idcaddie."&amp;item=&amp;elt_flag=" . $elt_flag . "&amp;elt_no_flag=" . $elt_no_flag . "&quot;' />",
                        "&nbsp;<input type='button' class='bouton' value='".$msg["caddie_menu_action_edit_panier"]."' onclick=\"document.location='./autorites.php?categ=caddie&sub=gestion&quoi=panier&action=edit_cart&idcaddie=".$idcaddie."&item=0'\" />",
                        "&nbsp;<input type='button' class='bouton' value='".$msg["caddie_supprimer"]."' onclick=\"confirmation_delete(".$myCart->get_idcaddie().",'".htmlentities(addslashes($myCart->name),ENT_QUOTES, $charset)."')\" />",
                        confirmation_delete("./autorites.php?categ=caddie&sub=gestion&action=del_cart&idcaddie=");
                    }
                    break;
                default:
                    print $myCart->aff_cart_nb_items();
                    switch ($sub) {
                        case 'pointage':
                            $action_in_list = 'pointe_item';
                            break;
						case 'collecte':
							$action_in_list = 'add_item';
							break;
                        default:
                            print $cart_choix_quoi_action;
                            $action_in_list = 'add_item';
                            break;
                    }
                    if ($sub == 'action') {
                        print authorities_caddie_procs::get_display_list_from_caddie($idcaddie, 'categ=caddie&sub=' . $sub . '&quelle=' . $quelle);
                    } else {
                        print authorities_caddie_procs::get_display_list_from_caddie($idcaddie, 'categ=caddie&sub=' . $sub . '&moyen=' . $moyen, 'SELECT', $action_in_list);
                    }
                    break;
            }
        } else {
            static::get_aff_paniers($sub, $quelle, $moyen);
        }
    }

    public static function print_prepare($idcaddie_new=0) {
        global $msg, $base_path;
        global $object_type, $item, $current_print, $aff_lien, $boutons_select;
        global $selected_objects, $pager;
		
        if (!$object_type) {
        	$object_type = "MIXED";
        }
        
        print "<script type='text/javascript' src='./javascript/tablist.js'></script>";
        print "<h3>".$msg["print_cart_title"]."</h3>\n";
        print "<form name='print_options' action='print_cart.php?action=print&current_print=".$current_print."&object_type=".$object_type."&authorities_caddie=1' method='post'>";
        //Affichage de la sélection des paniers
        $requete = "SELECT authorities_caddie.*, COUNT(object_id) AS nb_objects, COUNT(flag=1) AS nb_flags 
        			FROM authorities_caddie 
        			LEFT JOIN authorities_caddie_content ON caddie_id = idcaddie ";
        if($object_type != "MIXED") {
        	$requete .= " WHERE type = '".$object_type."' ";
        }
        $requete .= " GROUP BY idcaddie ORDER BY type, name, comment";
        $resultat = pmb_mysql_query($requete);
        $ctype = "";
        $parity = 0;
        $script_submit = '';
        while ($ca = pmb_mysql_fetch_object($resultat)) {
            if (!empty($idcaddie_new) && ($idcaddie_new != $ca->idcaddie)) continue;
            if (!empty($idcaddie_new) && ($idcaddie_new == $ca->idcaddie)) {
                $script_submit =  "<script>document.getElementById('id_" . $ca->idcaddie . "').checked=true;document.forms['print_options'].submit()</script>";
            }
            $ca_auth = explode(" ", $ca->autorisations);
            $as = in_array(SESSuserid, $ca_auth);
            if (($as !== false) && ($as !== null)) {
                if ($ca->type != $ctype) {
                    $ctype = $ca->type;
                    $print_cart[$ctype]["titre"] = "<b>".$msg["caddie_de_".$ca->type]."</b><br/>";
                }
                if (!trim($ca->caddie_classement)) {
                    $ca->caddie_classement = classementGen::getDefaultLibelle();
                }
                $print_cart[$ctype]["classement_list"][$ca->caddie_classement]["title"] = stripslashes($ca->caddie_classement);
                if (($parity = 1 - $parity)) {
                    $pair_impair = "even";
                } else {
                    $pair_impair = "odd";
                }
                if(!isset($print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"])){
                	$print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"] = "";
                }
                $tr_javascript = " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
                $print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"].= pmb_bidi("
                		<tr class='".$pair_impair."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" >
                			<td class='classement60'>
                				<input type='checkbox' id='id_".$ca->idcaddie."' name='caddie[".$ca->idcaddie."]' value='".$ca->idcaddie."' />
                				&nbsp;
                				<a href='javascript:document.getElementById(\"id_".$ca->idcaddie."\").checked=true;document.forms[\"print_options\"].submit();' />
                				<strong>".$ca->name."</strong>");
                if ($ca->comment) {
                    $print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"].= pmb_bidi("
                    			<br/>
                    			<small>(".$ca->comment.")</small>");
                }
                $print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"].= pmb_bidi("
                			</td>
                			<td>
                				<b>".$ca->nb_flags."</b>".$msg['caddie_contient_pointes']." / <b>$ca->nb_objects</b> 
                			</td>
							<td>".$aff_lien."</td>
						</tr>");
            }
        }
        
        if (!isset($pager) && !$selected_objects) $pager = 0;
        elseif (!isset($pager)) $pager = 2;
        if (!isset($selected_objects)) $selected_objects = '';
        print "<script>
            function get_params_url() {
                var pager = document.querySelector('input[name=\"pager\"]:checked').value;               
                       //'./cart.php?action=new_cart&object_type=".$object_type."&item=".$item."&current_print=".$current_print."&authorities_caddie=1&pager=$pager&&selected_objects=$selected_objects'
                return './cart.php?action=new_cart&object_type=" . $object_type . "&item=$item&current_print=$current_print&authorities_caddie=1&selected_objects=$selected_objects&pager=' +  pager;
            }
        </script>";
        print "		<input type='radio' id='pager_2' name='pager' value='2' " . ($pager == 2 ? "checked='checked'" : "") . "/>&nbsp;<label for='pager_2'>" . $msg["print_size_selected_elements_authorities"] . "</label><br />
        			<input type='radio' id='pager_1' name='pager' value='1' " . ($pager == 1 ? "checked='checked'" : "") . "/>&nbsp;<label for='pager_1'>".$msg["print_size_current_page_authorities"]."</label><br/>
                    <input type='radio' id='pager_0' name='pager' value='0' " . (!$pager ? "checked='checked'" : "") . "/>&nbsp;<label for='pager_0'>".$msg["print_size_all_authorities"]."</label><br/>
					<div class='row'>
						<hr/>
						".$boutons_select."&nbsp;
						<input class='bouton' type='button' value='".$msg['new_cart']."' onClick=\"document.location=get_params_url();\" />
					</div>
					<hr/>";

        print pmb_bidi("
        			<div class='row'>
        				<a href='javascript:expandAll()'>
        					<img src='".get_url_icon('expand_all.gif')."' id='expandall' style='border:0px'>
        				</a>
                        <a href='javascript:collapseAll()'>
        					<img src='".get_url_icon('collapse_all.gif')."' id='collapseall' style='border:0px'>
        				</a>".$msg['caddie_add_search']."
        			</div>");

        if (!empty($print_cart)) {
            foreach ($print_cart as $key => $cart_type) {
                ksort($print_cart[$key]["classement_list"]);
            }
            foreach ($print_cart as $key => $cart_type) {
                //on remplace les clés à cause des accents
                $cart_type["classement_list"] = array_values($cart_type["classement_list"]);
                $contenu = "";
                foreach ($cart_type["classement_list"] as $keyBis => $cart_typeBis) {
                    $contenu.=gen_plus($key . $keyBis, $cart_typeBis["title"], "<table border='0' cellspacing='0' style='width:100%' class='classementGen_tableau'>".$cart_typeBis["cart_list"]."</table>", 1);
                }
                print gen_plus($key, $cart_type["titre"], $contenu, 1);
            }
        }
        print "			<input type='hidden' name='current_print' value='".$current_print."'/>";
        if($selected_objects) {
        	print "<input type='hidden' name='selected_objects' value='$selected_objects'/>";
        }
        $boutons_select = '';
        if (!empty($print_cart)) {
            $boutons_select = "<input type='submit' value='".$msg['print_cart_add']."' class='bouton' />";
        }
        $boutons_select.= "&nbsp;<input type='button' value='".$msg['print_cancel']."' class='bouton' onClick='self.close();' />";
        print "		<div class='row'>
        				<hr />
	        			".$boutons_select."&nbsp;
	        			<input class='bouton' type='button' value='".$msg['new_cart']."' onClick=\"document.location=get_params_url();\" />
	        		</div>";
        print "	</form>
		<script type='text/javascript' src='".$base_path."/javascript/popup.js'></script>
        ";
        print $script_submit;
    }

    public static function print_cart() {
        global $msg;
        global $nb_per_page_search, $page, $search, $message;
        global $object_type, $idcaddie;
        
        $environement = $_SESSION["PRINT_CART"];
        if (!empty($environement["TEXT_QUERY"])) {
            $requete = $environement["TEXT_QUERY"];
			if (count($environement["TEXT_LIST_QUERY"])) {
				foreach($environement["TEXT_LIST_QUERY"] as $query) {			
					 @pmb_mysql_query($query);					
				}
			}          
            if (!$environement["pager"]) {
                $p = stripos($requete, "limit");
                if ($p) {
                    $requete = substr($requete, 0, $p);
                }
            }
        } else if (!empty($environement['SEARCH_TYPE'])){ 
            switch ($environement["SEARCH_TYPE"]) {
            	case "simple":			
					$sat = new searcher_authorities_tab($environement["FORM_VALUES"]);
                    break;
            	case "extended":			
					$sat = new search_authorities(true, 'search_fields_authorities');
					$sat->reduct_search();
					$table = $sat->make_search();
                    $requete = "select " . $table . ".* from $table";

                    if ($environement["pager"]) {
                        $requete.=" limit " . $nb_per_page_search * $page . ",$nb_per_page_search";
                    } else {
                      	$p = stripos($requete, "limit");
                       	if ($p) {
                       		$requete = substr($requete, 0, $p);
                       	}
                    }
                    break;
            	case "classic":
            		global $user_input;
            		$sat = searcher_factory::get_searcher(strtolower($environement["SEARCH_OBJECTS_TYPE"]), '', $user_input);
            		break;
                case "cart":
                    $requete = "select object_id as id_authority from authorities_caddie_content";
                    $requete.=" where caddie_id=" . $idcaddie;
                    if (!$environement["pager"]) {
                        $p = stripos($requete, "limit");
                        if ($p) {
                            $requete = substr($requete, 0, $p);
                        }
                    }else{
                        $requete.=$orderby . " limit " . ($nb_per_page_search * ($page - 1)) . ",$nb_per_page_search";
                    }
                    break;
            }
        }
        
        if ($environement["caddie"]) {
            foreach ($environement["caddie"] as $environement_caddie) {
                $c = static::get_object_instance($environement_caddie);
                $nb_items_before = $c->nb_item;
                if (isset($requete) && $requete) {
	                $resultat = @pmb_mysql_query($requete);               
	                print pmb_mysql_error();
	                while (($r = pmb_mysql_fetch_object($resultat))) {
	                	$c->add_item($r->id_authority, $object_type);
	                }
                } else { 
                	if($environement["pager"]){
                		$simple_search_results = $sat->get_sorted_result("default",($nb_per_page_search * $page), $nb_per_page_search);
                	} else {
                		$simple_search_results = explode(',',$sat->get_result());
                	}
                	foreach($simple_search_results as $id) {
                	    if (!$environement["pager"] && $environement['SEARCH_OBJECTS_TYPE'] == 'CONCEPTS' && $environement["SEARCH_TYPE"] == 'classic') {
                	       $query = "SELECT id_authority FROM authorities WHERE num_object=" . $id . " and type_object=" . AUT_TABLE_CONCEPT;
                	       $res = @pmb_mysql_query($query);         
                	       if (($r = pmb_mysql_fetch_object($res))) {
                	           $id = $r->id_authority;
                	       } else {
                	           continue;
                	       }
                        }
                		if ($environement["pager"] != 2 || in_array($id, $environement['selected_objects'])) {
                			$c->add_item($id, $object_type);
                		}
                	}
                }
                $c->compte_items();
                $message.=sprintf($msg["print_cart_n_added"] . "\\n", ($c->nb_item - $nb_items_before), $c->name);
            }
            print "<script>alert(\"".$message."\"); self.close();</script>";
        } else {
            print "<script>alert(\"" . $msg["print_cart_no_cart_selected"] . "\"); history.go(-1);</script>";
        }
        $_SESSION["PRINT_CART"] = false;
    }
    
    public static function set_session() {
    	global $current_print, $caddie, $pager, $include_child, $msg, $object_type;
    	global $selected_objects;
    	if ($_SESSION["session_history"][$current_print]) {
    		if($_SESSION["session_history"][$current_print]["AUT"]){
    			$_SESSION["PRINT_CART"]=$_SESSION["session_history"][$current_print]["AUT"];
    		}
    		$_SESSION["PRINT_CART"]["caddie"]=$caddie;
    		$_SESSION["PRINT_CART"]["pager"]=$pager;
    		$_SESSION["PRINT_CART"]["include_child"]=$include_child;
    		if($selected_objects) {
    			$_SESSION["PRINT_CART"]["selected_objects"]=explode(',', $selected_objects);
    		}
    		echo "<script>document.location='./print_cart.php?object_type=".$object_type."&authorities_caddie=1'</script>";
    	} else {
    		echo "<script>alert(\"".$msg["print_no_search"]."\"); self.close();</script>";
    	}
    }
}

// fin de déclaration de la classe authorities_caddie_controller
