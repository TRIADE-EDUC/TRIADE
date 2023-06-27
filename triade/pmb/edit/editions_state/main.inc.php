<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.11 2017-11-21 12:01:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($partial_submit)) $partial_submit = '';
if(!isset($elem)) $elem = '';

require_once($class_path."/editions_datasource.class.php");
require_once($class_path."/editions_state.class.php");

if(!$id)$id = 0;
//héhé
@ini_set("zend.ze1_compatibility_mode", "0");

$editions_state = new editions_state($id);

$data = new editions_datasource();
$data->get_datasources_list();
print "<h1>".$msg['edition_state_label']."</h1>";
switch($action){
	case "edit" :
		if($partial_submit){//Modification par l'ajax ou par le javascript
			$editions_state->get_from_form();	
		}		
		print $editions_state->get_form();
		break;
	case "save" :
		$editions_state->get_from_form();
		$editions_state->save();
		show_state_list();
		break;
	case "show" :
		print $editions_state->show($sub,$elem);
		break;
	case "delete" :
		$editions_state->delete();
	default:
		show_state_list();
		break; 
}


function show_state_list(){
	global $msg,$charset,$javascript_path;
	
	$query = "select id_editions_state, editions_state_name, editions_state_comment, libproc_classement, editions_state_num_classement from editions_states left join procs_classements on editions_state_num_classement = idproc_classement order by libproc_classement,editions_state_name asc";
	$result = pmb_mysql_query($query);
	print "
		<script type=\"text/javascript\" src=\"".$javascript_path."/tablist.js\"></script>
		<span class='item-expand'>
			<a href=\"javascript:expandAll()\"><img src='".get_url_icon('expand_all.gif')."' style='border:0px' id=\"expandall\"></a>
			<a href=\"javascript:collapseAll()\"><img src='".get_url_icon('collapse_all.gif')."' style='border:0px' id=\"collapseall\"></a>
		</span>
		";
	if(pmb_mysql_num_rows($result)){
		$class_prec=$msg['proc_clas_aucun'];
		$buf_tit=$msg['proc_clas_aucun'];
		$buf_contenu="";
		$buf_class=0;
		$parity=1;
		while ($row = pmb_mysql_fetch_object($result)){
			if (!$row->libproc_classement) $row->libproc_classement=$msg['proc_clas_aucun'];//Pour les états qui ne sont pas dans un classement
			if ($class_prec!=$row->libproc_classement) {
				if ($buf_tit) {
					$buf_contenu="<table><tr><th colspan=4>".$buf_tit."</th></tr>".$buf_contenu."</table>";
					print gen_plus("procclass".$buf_class,$buf_tit,$buf_contenu);
					$buf_contenu="";
				}
				$buf_tit=$row->libproc_classement;
				$buf_class=$row->editions_state_num_classement;
				$class_prec=$row->libproc_classement;
			}
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity++;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
			$buf_contenu.="\n<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
					<td width='10'><input type='button' class='bouton' onclick=\"document.location='./edit.php?categ=state&action=show&sub=tab&id=".$row->id_editions_state."';\" value='".$msg['708']."'/></td>
					<td onmousedown=\"document.location='./edit.php?categ=state&action=edit&id=".$row->id_editions_state."';\" ><strong>".htmlentities($row->editions_state_name,ENT_QUOTES,$charset)."</strong><br />
						<small>".htmlentities($row->editions_state_comment,ENT_QUOTES,$charset)."</small></td>
				</tr>";
		}
		$buf_contenu="<table><tr><th colspan=4>".$buf_tit."</th></tr>".$buf_contenu."</table>";
		print gen_plus("procclass".$buf_class,$buf_tit,$buf_contenu);
	}
	print "
		<div class='row'>
			<input type='button' class='bouton' value='".$msg['editions_state_add']."' onclick=\"document.location='./edit.php?categ=state&action=edit&id=0';\" />
		</div>";
}