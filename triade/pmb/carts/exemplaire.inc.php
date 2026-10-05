<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: exemplaire.inc.php,v 1.19 2021/06/16 14:46:41 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $items, $item, $msg, $aff, $action, $idcaddie, $include_child, $current_print;

$items = [];
if (!empty($item)) {
    $items = explode(',', $item);
}

$nb_expl = 0;
foreach ($items as $expl_id) {
    if ($expl_id) {
        $nb_expl++;
    	$requete = "SELECT expl_notice, expl_bulletin FROM exemplaires WHERE expl_id='$expl_id' ";
    	$result = @pmb_mysql_query($requete);
    	if(pmb_mysql_num_rows($result)) {
    		$temp = pmb_mysql_fetch_object($result);
    		$expl = get_expl_info($expl_id,0);
    		$aff_reduit = $msg[376]."&nbsp;".$expl->expl_cb." ".$expl->aff_reduit ;
    		if  ($temp->expl_notice) {
    			$notice = new mono_display($temp->expl_notice, 1, '', 0);
    			$aff .= $notice->isbd;
    			} else {
    				$bl = new bulletinage_display($temp->expl_bulletin);
    				$aff .= $bl->display;
    				}
    		} else {
    			$aff .= $msg["info_ex_introuvables"];
    			$aff_reduit = $msg["info_ex_introuvables"];
    		}
    		$expl = get_expl_info($expl_id);
    	// informations de localisation
    	$aff.= "<div class=\"row\">";
    	$aff.= "<u>".$msg[298]."</u>&nbsp;:&nbsp;".$expl->location_libelle.'<br />';
    	$aff.= "<u>".$msg[295]."</u>&nbsp;:&nbsp;".$expl->section_libelle.'<br />';
    	$aff.= "<u>".$msg[296]."</u>&nbsp;:&nbsp;".$expl->expl_cote.'<br />';
    	$aff.= "<u>".$msg[297]."</u>&nbsp;:&nbsp;".$expl->statut_libelle;
    	$aff.= "</div>";
        
    	if ($nb_expl == 21) {
    	    print '<strong class="see_all"><a href="#" onclick="hide_elements(\'see_all\'); show_elements(\'show_more\');">'.$msg["selector_author_type_all"].'</a></strong>';
            print '<div class="show_more" style="display : none;">';
    	}
    	print '<strong>'.pmb_bidi($aff_reduit).'</strong><br />';
    }
}
if ($nb_expl > 20) {
    print '</div>';
}

switch($action) {
    case 'add_item':
        if ($idcaddie) {
            $caddie[0] = $idcaddie;
        }
        foreach ($caddie as $idcaddie) {
            $myCart = new caddie($idcaddie);
            foreach ($items as $item_content) {
                if ($include_child) {
                    $tab_list_child = notice::get_list_child($item_content);
                    if (count($tab_list_child)) {
                        foreach ($tab_list_child as $notice_id) {
                            $myCart->add_item($notice_id, "EXPL");
                        }
                    }
                } else {
                    $myCart->add_item($item_content, "EXPL");
                }
            }
            $myCart->compte_items();
        }
        print "<script type='text/javascript'>window.close();</script>";
        break;
    case 'new_cart':
        break;
    case 'del_cart':
    case 'valid_new_cart':
    default:
        if(isset($current_print) && $current_print) {
            $action="print_prepare";
            require_once("./print_cart.php");
        } else {
            aff_paniers($item, "EXPL", "./cart.php?", "add_item", $msg["caddie_add_EXPL"], "", 0, 1, 1);
        }
        break;
}