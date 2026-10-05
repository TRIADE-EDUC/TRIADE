<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_cart.inc.php,v 1.48.2.1 2024/05/03 07:40:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// ********************************************************************************
// affichage des paniers existants
function aff_paniers_empr($item=0, $lien_origine="./circ.php?", $action_click = "add_item", $titre="", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1,$post_param_serialized="") {
	global $msg;
	global $sub,$quoi;
	global $action;
	global $list_ui_objects_type;
	
	if($sub!='gestion' && $sub!='action') {
		print "<form name='print_options' action='$lien_origine&action=$action_click".($list_ui_objects_type ? "&list_ui_objects_type=".$list_ui_objects_type : "")."&item=$item' method='post'>";
	}
	print "<script type='text/javascript' src='./javascript/tablist.js'></script>";
	print "<script type='text/javascript' src='./javascript/ajax.js'></script>";
	print "<hr />";
	$boutons_select = '';
	if ($lien_creation) {
		print "<div class='row'>";
		if($sub!='gestion')  print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"this.form.action='$lien_origine&action=new_cart".($list_ui_objects_type ? "&list_ui_objects_type=".$list_ui_objects_type : "")."&item=$item'; this.form.submit();\" />";
		else print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart".($list_ui_objects_type ? "&list_ui_objects_type=".$list_ui_objects_type : "")."&item=$item'\" />";
		print "</div><br>";
	}
	
	list_empr_caddies_ui::set_lien_creation($lien_creation);
	list_empr_caddies_ui::set_lien_edition($lien_edition);
	$list_empr_caddies_ui = new list_empr_caddies_ui(array('type' => $restriction_panier));
	$list_empr_caddies_ui->set_item($item);
	$list_empr_caddies_ui->set_lien_origine($lien_origine);
	$list_empr_caddies_ui->set_action_click($action_click);
	$list_empr_caddies_ui->set_expandable_title($titre);
	print confirmation_delete("$lien_origine&action=del_cart".($list_ui_objects_type ? "&list_ui_objects_type=".$list_ui_objects_type : "")."&item=$item&idemprcaddie=");
	print "<script type='text/javascript'>
			function add_to_cart(form) {
        		var inputs = form.getElementsByTagName('input');
        		var count=0;
        		for(i=0;i<inputs.length;i++){
					if(inputs[i].type=='checkbox' && inputs[i].checked==true)
        				count ++;
				}
				if(count == 0){
					alert(\"$msg[no_emprcart_selected]\");
					return false;
				}
				return true;
   			}
   		</script>";
	print $list_empr_caddies_ui->get_display_list();
	$script_submit = $list_empr_caddies_ui->get_script_submit();
	if($sub=="gestion" && $quoi=="panier"){
		print "<script type='text/javascript'>
            pmb_include('./javascript/classementGen.js');
        </script>";
	}
	
	if($sub!='gestion' && $sub!='action'&& $action != "del_cart") {
		$boutons_select="<input type='submit' value='".$msg["print_cart_add"]."' class='bouton' onclick=\"return add_to_cart(this.form);\"/>&nbsp;<input type='button' value='".$msg["print_cancel"]."' class='bouton' onClick='self.close();'/>&nbsp;";
	}	
	if ($lien_creation) {
		print "<div class='row'><hr />";
			if($sub!='gestion')  print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"this.form.action='$lien_origine&action=new_cart".($list_ui_objects_type ? "&list_ui_objects_type=".$list_ui_objects_type : "")."&item=$item'; this.form.submit();\" />";
			else print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart".($list_ui_objects_type ? "&list_ui_objects_type=".$list_ui_objects_type : "")."&item=$item'\" />";
		print "</div>"; 
	} else {
		print "<div class='row'><hr />
			$boutons_select
			</div>"; 		
	}
	if ($post_param_serialized != "") {
		print unserialize($post_param_serialized);
	}			
	 if($sub!='gestion')  print"</form>";
	 print $script_submit;

}
