<?php
// +--------------------------------------------------------------------------+
// | PMB est sous licence GPL, la réutilisation du code est cadrée            |
// +--------------------------------------------------------------------------+
// $Id: print.php,v 1.71.2.4.2.2 2025/04/04 09:39:58 dgoron Exp $

//Impression

$base_path = ".";
$base_auth = "CATALOGAGE_AUTH|CIRCULATION_AUTH";
$base_title = "\$msg[print_title]";
$base_nobody=1;
$base_noheader=1;

use Pmb\Common\Library\HTML2PDF\HTML_2_PDF;
require($base_path."/includes/init.inc.php");

global $class_path, $include_path, $msg, $charset;
global $pmb_print_expl_default, $pmb_opac_url, $opac_url_base;
global $action_print, $sort_id, $pager, $permalink, $current_print;
global $short, $ex, $exnum, $output, $emailobj, $emaildest, $emaildest_id, $emailcontent;
global $notice_id, $header, $vignette, $notice_tpl, $idcaddie;
global $current_page_objects, $selected_objects;
global $nb_per_page_search, $deflt2docs_location;

if(!isset($sort_id)) $sort_id = 0;

if (($action_print=="")&&($_SESSION["PRINT"])) {
	if ($_SESSION["PRINT"]["output"]=="tt") {
	    //Le Format de cache WebP n'est pas compatible en sortie traitement de texte
	    global $pmb_img_cache_type;
        $pmb_img_cache_type = 'png';
		header("Content-Type: application/word");
		header("Content-Disposition: attachement; filename=liste.doc");
	}
}

require_once($class_path."/mono_display.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($class_path."/author.class.php");
require_once($class_path."/editor.class.php");
require_once($include_path."/isbn.inc.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/serie.class.php");
require_once($include_path."/explnum.inc.php");
require_once($class_path."/category.class.php");
require_once($class_path."/indexint.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/serial_display.class.php");
require_once($include_path."/mail.inc.php");
require_once($class_path."/notice_tpl_gen.class.php");

if (file_exists($include_path.'/print/print_options_subst.xml')){
	$xml_print = new XMLlist($include_path.'/print/print_options_subst.xml');
} else {
	$xml_print = new XMLlist($include_path.'/print/print_options.xml');
}
$xml_print->analyser();
$print_options = $xml_print->table;

header ("Content-Type: text/html; charset=$charset");
if ($action_print=="print_prepare") {
	print $std_header;
	print "<h3>".$msg["print_options"]."</h3>\n";
	print "<script type='text/javascript' src='./javascript/ajax.js'></script>";
	print "
	<div id='att'></div>
	<script type='text/javascript'>
		function sel_part_gestion(){
			if(document.getElementById('outp').checked){
				document.getElementById('mail_part').style.display='none';
			}
			if(document.getElementById('outt').checked){
				document.getElementById('mail_part').style.display='none';
			}
			if(document.getElementById('oute').checked){
				document.getElementById('mail_part').style.display='block';
				ajax_resize_elements();
			}
			if(document.getElementById('outpdf').checked){
				document.getElementById('mail_part').style.display='none';
				ajax_resize_elements();
			}
		}
	</script>";
	print "<form name='print_options' action='print.php?action_print=print' method='post'>
	<b>".$msg["print_size"]."</b>";
	if(!isset($notice_id) || !$notice_id)
	print"
	<blockquote>
		<input type='radio' name='pager' id='selected_elements' value='2' ".($print_options['selected_elements'] ? ' checked ' : '')."/><label for='selected_elements'>&nbsp;".$msg["print_size_selected_elements"]."</label><br />
		<input type='radio' name='pager' id='current_page' value='1' ".($print_options['current_page'] ? ' checked ' : '')."/><label for='current_page'>&nbsp;".$msg["print_size_current_page"]."</label><br />
		<input type='radio' name='pager' id='all' value='0' ".($print_options['all'] ? ' checked ' : '')."/><label for='all'>&nbsp;".$msg["print_size_all"]."</label>
	</blockquote>";

	$sort_info = $sort_id ? '<input type="hidden" name="sort_id" value="'.$sort_id.'">' : '';

	$onchange="
		var div_sel=document.getElementById('sel_notice_tpl');
		var div_sel2=document.getElementById('sel_notice_tpl2');
		var notice_tpl=document.getElementById('notice_tpl');
		var sel=notice_tpl.options[notice_tpl.selectedIndex].value;
	    if(sel>0){
	    	div_sel.style.display='none';
	    	div_sel2.style.display='none';
	    }else {
	    	div_sel.style.display='block';
	    	div_sel2.style.display='block';
	    }
	";
	$sel_notice_tpl=notice_tpl_gen::gen_tpl_select("notice_tpl",0,$onchange);

	print"
	<label for='notice_tpl'><b>".$msg["print_format_title"]."</b></label>
	<blockquote>
		$sel_notice_tpl
		<div id='sel_notice_tpl'>
			<input type='radio' name='short' id='s1' value='1' ".($print_options['s1'] ? ' checked ' : '')."/><label for='s1'>&nbsp;".$msg["print_short_format"]."</label><br />
			<input type='radio' name='short' id='s0' value='6' ".($print_options['s0'] ? ' checked ' : '')."/><label for='s0'>&nbsp;".$msg["print_long_format"]."</label><br />
			<input type='checkbox' name='header' id='header' value='1' ".($print_options['header'] ? ' checked ' : '')."/><label for='header'>&nbsp;".$msg["print_header"]."</label><br/>
			<input type='checkbox' name='permalink' id='permalink' value='1' ".($print_options['permalink'] ? ' checked ' : '')."/><label for='permalink'>&nbsp;".$msg["print_permalink"]."</label><br />
			<input type='checkbox' name='vignette' id='vignette' value='1' ".($print_options['vignette'] ? ' checked ' : '')."/><label for='vignette'>&nbsp;".$msg["print_vignette"]."</label><br />
		</div>
	</blockquote>
	<div id='sel_notice_tpl2'>
	<b>".$msg["print_ex_title"]."</b>
	<blockquote>";
	if ($pmb_print_expl_default) {
		$checkprintexpl="checked";
		$checknoprintexpl="";
	} else {
		$checkprintexpl="";
		$checknoprintexpl="checked";
	}
	print "
		<input type='radio' name='ex' id='ex1' value='1' $checkprintexpl /><label for='ex1'>&nbsp;".$msg["print_ex"]."</label><br />
		<input type='radio' name='ex' id='ex0' value='0' $checknoprintexpl /><label for='ex0'>&nbsp;".$msg["print_no_ex"]."</label>
	</blockquote>
	<b>".$msg["print_numeric_ex_title"]."</b>
		<blockquote>
			<input type='radio' name='exnum' id='exnum1' value='1' ".($print_options['exnum'] ? ' checked=\'checked\' ' : '')."/><label for='exnum1'>&nbsp;".$msg["print_numeric_ex"]."</label><br />
			<input type='radio' name='exnum' id='exnum0' value='0' ".($print_options['exnum'] ? '' : ' checked=\'checked\' ')."/><label for='exnum0'>&nbsp;".$msg["print_no_numeric_ex"]."</label>
		</blockquote>
	</div>
	<b>".$msg["print_output_title"]."</b>
	<blockquote>
		<input type='radio' name='output' id='outp' onClick =\"sel_part_gestion();\" value='printer' ".($print_options['outp'] ? ' checked ' : '')."/><label for='outp'>&nbsp;".$msg["print_output_printer"]."</label><br />
		<input type='radio' name='output' id='outt' onClick =\"sel_part_gestion();\" value='tt' ".($print_options['outt'] ? ' checked ' : '')."/><label for='outt'>&nbsp;".$msg["print_output_writer"]."</label><br />
		<input type='radio' name='output' id='oute' onClick =\"sel_part_gestion();\" value='email' ".($print_options['oute'] ? ' checked ' : '')."/><label for='oute'>&nbsp;".$msg["print_output_email"]."</label><br />
		<input type='radio' name='output' id='outpdf' onClick =\"sel_part_gestion();\" value='pdf' ".($print_options['outpdf'] ? ' checked ' : '')."/><label for='outpdf'>&nbsp;".$msg["print_output_outpdf"]."</label><br />
	</blockquote>
	<div id='mail_part'>
		<div class='row'>
			<div><label for='emaildest_0'>".$msg["print_emaildest"]."</label></div>
			<input type='text' id='emaildest_0' class='saisie-20emr' completion='empr_mail' name='emaildest[]' autfield='emaildest_id_0' value='' autocomplete='off'/>
			<input type='button' class='bouton' value='X' onclick=\"document.getElementById('emaildest_0').value=''; document.getElementById('emaildest_id_0').value='';\">
			<input class='bouton' value='+' onclick='add_dest_field(this);' counter='0' type='button'>
			<input type='hidden' name='emaildest_id[]' id='emaildest_id_0'/>
		</div>
		<div id='emailObj' class='row'>
			<div><label for='emailobj'>".$msg["print_emailobj_label"]."</label></div>
			<input type='text' size='40' id='emailobj' name='emailobj' value='".htmlentities(trim($msg["print_emailobj"]." ".$opac_biblio_name." - ".formatdate(today())), ENT_QUOTES, $charset)."' />
		</div>
		<div id='emailContent' class='row'>
			<div><label for='emailcontent'>".$msg["523"]."</label></div>
            <textarea rows='4' cols='45' id='emailcontent' name='emailcontent' value=''></textarea>
		</div>

	</div>
	<input type='hidden' name='current_print' value='$current_print'/>
	<input type='hidden' name='current_page_objects' value='$current_page_objects'/>
    <input type='hidden' name='selected_objects' value='$selected_objects'/>
	<input type='hidden' name='notice_id' value='".(isset($notice_id) ? $notice_id : '')."'/>".$sort_info."
	<span style='text-align:center'><input type='submit' value='".$msg["print_print"]."' class='bouton'/>&nbsp;<input type='button' value='".$msg["print_cancel"]."' class='bouton' onClick='self.close();'/></span>";
	print "</form>
	<script type='text/javascript' src='".$base_path."/javascript/popup.js'></script>
	<script type='text/javascript'>
		function add_dest_field(buttonClicked){
			var currentCounter = buttonClicked.getAttribute('counter');
			currentCounter++;

			var newLine = document.createElement('div');
			newLine.setAttribute('class', 'row');

			var newInput = document.createElement('input');
			newInput.setAttribute('class','saisie-20emr');
			newInput.setAttribute('id', 'emaildest_'+currentCounter);
			newInput.setAttribute('completion','empr_mail');
			newInput.setAttribute('name','emaildest[]');
			newInput.setAttribute('autfield', 'emaildest_id_'+currentCounter);
			newInput.setAttribute('value', '');
			newInput.setAttribute('autocomplete', 'off');
			newInput.setAttribute('type', 'text');

			var newInputId = document.createElement('input');
			newInputId.setAttribute('id','emaildest_id_'+currentCounter);
			newInputId.setAttribute('type','hidden');
			newInputId.setAttribute('name','emaildest_id[]');


			var newPurge = document.createElement('input');
			newPurge.setAttribute('value','X');
			newPurge.setAttribute('type','button');
			newPurge.setAttribute('class','bouton');
			newPurge.addEventListener('click', function(){
				newInput.value='';
				newInputId.value='';
			});

			newLine.appendChild(newInput);
			newLine.appendChild(newInputId);
			newLine.appendChild(newPurge);

			buttonClicked.setAttribute('counter', currentCounter);
			buttonClicked.parentElement.parentElement.insertBefore(newLine, document.getElementById('emailObj'));
			ajax_pack_element(newInput);
		}

		sel_part_gestion();
		if(getSelectedObjects('opener')) {
			document.getElementById('selected_elements').checked = 'checked';
		}
		ajax_parse_dom();
	</script></body></html>";
}

if ($action_print=="print") {
	if (!empty($_SESSION["session_history"][$current_print])) {
		$_SESSION["PRINT"]=$_SESSION["session_history"][$current_print]["NOTI"];
		$_SESSION["PRINT"]["short"]=$short;
		$_SESSION["PRINT"]["ex"]=$ex;
		$_SESSION["PRINT"]["exnum"]=$exnum;
		$_SESSION["PRINT"]["output"]=$output;
		$_SESSION["PRINT"]["emailobj"]=$emailobj;
		$_SESSION["PRINT"]["emaildest"]=$emaildest;
		$_SESSION["PRINT"]["emaildest_id"]=$emaildest_id;
		$_SESSION["PRINT"]["emailcontent"]=$emailcontent;
		$_SESSION["PRINT"]["pager"]=$pager;
		$_SESSION["PRINT"]["current_page_objects"]=(!empty($current_page_objects) ? explode(',', $current_page_objects) : array());
		$_SESSION["PRINT"]["selected_objects"]=(!empty($selected_objects) ? explode(',', $selected_objects) : array());
		$_SESSION["PRINT"]["notice_id"]=$notice_id;
		$_SESSION["PRINT"]["permalink"]=$permalink;
		$_SESSION["PRINT"]["vignette"]=$vignette;
		$_SESSION["PRINT"]["header"]=$header;
		$_SESSION["PRINT"]["notice_tpl"]=$notice_tpl;
		if ($sort_id) $_SESSION["PRINT"]["sort_id"]=$sort_id;
		else $_SESSION["PRINT"]["sort_id"]=$_SESSION['tri'];
		echo "<script>document.location='./print.php'</script>";
	} elseif ($notice_id) {
		$_SESSION["PRINT"]["short"]=$short;
		$_SESSION["PRINT"]["ex"]=$ex;
		$_SESSION["PRINT"]["exnum"]=$exnum;
		$_SESSION["PRINT"]["output"]=$output;
		$_SESSION["PRINT"]["emailobj"]=$emailobj;
		$_SESSION["PRINT"]["emaildest"]=$emaildest;
		$_SESSION["PRINT"]["emaildest_id"]=$emaildest_id;
		$_SESSION["PRINT"]["emailcontent"]=$emailcontent;
		$_SESSION["PRINT"]["pager"]=$pager;
		$_SESSION["PRINT"]["current_page_objects"]=(!empty($current_page_objects) ? explode(',', $current_page_objects) : array());
		$_SESSION["PRINT"]["selected_objects"]=(!empty($selected_objects) ? explode(',', $selected_objects) : array());
		$_SESSION["PRINT"]["notice_id"]=$notice_id;
		$_SESSION["PRINT"]["permalink"]=$permalink;
		$_SESSION["PRINT"]["vignette"]=$vignette;
		$_SESSION["PRINT"]["header"]=$header;
		$_SESSION["PRINT"]["notice_tpl"]=$notice_tpl;
		echo "<script>document.location='./print.php'</script>";
	} else {
		echo "<script>alert(\"".$msg["print_no_search"]."\"); self.close();</script>";
	}
}
$use_opac_url_base=1;
$prefix_url_image=$opac_url_base;
$no_aff_doc_num_image=1;

if (($action_print=="")&&($_SESSION["PRINT"])) {
	$environement=$_SESSION["PRINT"];
	switch($environement['output']) {
		case 'email': // on n'utilise pas le cache des images dans le contenu de l'email
			$no_use_img_cache = 1;
			break;
	}
	$limit='';
	$already_sorted = false;
	if (!empty($environement["pager"]) && $environement["pager"] == 1 && !empty($environement['current_page_objects']) && is_array($environement['current_page_objects'])) {
	    array_walk($environement['current_page_objects'], "intval");
	    $requete = "select notice_id from notices where notice_id IN (".implode(',', $environement['current_page_objects']).")";
	    $already_sorted = true;
	} elseif($environement["notice_id"]){
		$requete="select notice_id from notices where notice_id=".$environement["notice_id"];
		$already_sorted = true;
	} elseif (!empty($environement["TEXT_QUERY"])) {
		if (is_array($environement["TEXT_LIST_QUERY"]) && count($environement["TEXT_LIST_QUERY"])) {
			foreach($environement["TEXT_LIST_QUERY"] as $query) {
				pmb_mysql_query($query);
			}
		}
		$requete=preg_replace('/limit\s+[0-9]\s*,*\s*[0-9]*\s*$/','',$environement["TEXT_QUERY"],1);
	} else {
		switch ($environement["SEARCH_TYPE"]) {
			case "extended":
				$sh=new search();
				$table=$sh->make_search();
				$requete = "select notice_id from $table";
				break;
			case "cart":
			    $idcaddie = intval($idcaddie);
				$requete = "select object_id as notice_id from caddie_content join notices where caddie_id=".$idcaddie." and object_id=notice_id";
				$requete .= " order by index_sew";
				break;
		}
	}
	if (!$already_sorted) {
	    if (!empty($environement["pager"])) {
	        $nb_per_page_search = intval($nb_per_page_search);
	        $start= $nb_per_page_search*($environement["PAGE"]-1);
	        $nbLimit = $nb_per_page_search;
	        $limit="limit ".$start.",$nb_per_page_search";
	    }else{
	        $start = 0;
	        $nbLimit = -1;
	    }
    	if (!empty($environement["sort_id"])) {
    		$sort = new sort('notices','base');
    		$requete = $sort->appliquer_tri($environement["sort_id"] , $requete, "notice_id", $start, $nbLimit);
    	}else{
    		$requete.=" $limit";
    	}
	}
	$resultat = pmb_mysql_query($requete);

	if (!$environement["vignette"]) {
		$pmb_book_pics_show = 0;
	}
	$pheader = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /><title>".$msg['print_title']."</title></head><body>";
	if($environement['output']=='email') {
		$environement['emailcontent'] = trim(stripslashes($environement['emailcontent']));
		if ($environement['emailcontent']) {
			$pheader.= $msg['523'].$environement['emailcontent'].'<br />';
		}
	}

	if($_SESSION["PRINT"]["notice_tpl"])	$noti_tpl=new notice_tpl_gen($_SESSION["PRINT"]["notice_tpl"]);
	else $noti_tpl=0;
	//Probleme d'inclusion de polices avec Html2pdf donc on enleve
	$pheader.= "<style type=\"text/css\">
	body {
		font-size: 10pt;
		".($environement['output'] != "pdf" ? "font-family: verdana, geneva, helvetica, arial;" : "").
		"color:#000000;
		background:#FFFFFF;
	}
	td {
		font-size: 10pt;
		".($environement['output'] != "pdf" ? "font-family: verdana, geneva, helvetica, arial;" : "").
		"color:#000000;
	}
	th {
		font-size: 10pt;
		".($environement['output'] != "pdf" ? "font-family: verdana, geneva, helvetica, arial;" : "").
		"color:#000000;
		font-weight:bold;
		color:#000000;
		background:#DDDDDD;
		text-align:left;
	}
	hr {
		border:none;
		border-bottom:1px solid #000000;
	}
	h3 {
		font-size: 12pt;
		color:#000000;
	}
	.vignetteimg {
		max-width: 140px;
		max-height: 200px;
		-moz-box-shadow: 1px 1px 5px #666666;
		-webkit-box-shadow: 1px 1px 5px #666666;
		box-shadow: 1px 1px 5px #666666;
	}
	.img_notice {
		max-width: 140px;
		max-height: 200px;
	}
	</style>";

	if($noti_tpl) {
		$pheader.=$noti_tpl->get_print_css_style();
	}
	$output_final = $pheader;

	$date_today = formatdate(today()) ;
	if (pmb_mysql_num_rows($resultat) != 1) {
		$output_final.= '<h3>'.$date_today.'&nbsp;'.sprintf($msg["print_n_notices"],pmb_mysql_num_rows($resultat)).'</h3>';
	}
	$output_final.= '<br />';

	while (($r=pmb_mysql_fetch_object($resultat))) {
		if ($environement["pager"] != 2 || in_array($r->notice_id, $environement['selected_objects'])) {
			if($noti_tpl) {
				$output_final.=$noti_tpl->build_notice($r->notice_id,$deflt2docs_location);
				$output_final.="<br />";
			} else{
				$n=pmb_mysql_fetch_object(pmb_mysql_query("select * from notices where notice_id=".$r->notice_id));
				if($n->niveau_biblio != 's' && $n->niveau_biblio != 'a') {
					if($environement['output']=='email'||$environement['output']=='tt'){
						$mono=new mono_display($n,$environement["short"],"",$environement["ex"],"","","",0,4,$environement["exnum"]);
					}else{
						$mono=new mono_display($n,$environement["short"],"",$environement["ex"],"","","",0,1,$environement["exnum"]);
					}
					if ($environement['header']) {
						$output_final.= '<b>'.$mono->header.'</b><br /><br />';
					}
					$output_final.= $mono->isbd;
				} else {
					if($environement['output']=='email'||$environement['output']=='tt'){
						$serial = new serial_display($n, $environement["short"], "", "", "", "", "", 0, 4, $environement["exnum"], 0, true, 0, 0, array(), false, 1, 0, 0, $environement["ex"]);
					}else{
						$serial = new serial_display($n, $environement["short"], "", "", "", "", "", 0, 1, $environement["exnum"], 0, true, 0, 0, array(), false, 1, 0, 0, $environement["ex"]);
					}
					if ($environement['header']) {
						$output_final.= '<b>'.$serial->header.'</b><br /><br />';
					}
					$output_final.= $serial->isbd;
				}
				if($environement['permalink']) {
					$output_final .= "<br /><a href='".$pmb_opac_url."index.php?lvl=notice_display&id=".$r->notice_id."'>".substr($pmb_opac_url."index.php?lvl=notice_display&id=".$r->notice_id,0,80)."</a><br />";
				}
				$output_final.= "<hr />";
			}
		}
	}
	if ($charset!='utf-8') {
		$output_final=cp1252Toiso88591($output_final);
	}
	switch($environement['output']) {

		case 'email':
			$mail_addresses = array();
			foreach($environement['emaildest'] as $i => $email){
				if(isset($environement['emaildest_id'][$i]) && $environement['emaildest_id'][$i]){
					$environement['emaildest_id'][$i] = intval($environement['emaildest_id'][$i]);
					$query = "select empr_mail from empr where id_empr = ".$environement['emaildest_id'][$i];
					$result = pmb_mysql_result(pmb_mysql_query($query), 0,0);
					$mail_addresses[] = $result;
				}else{
					if($email){
						$mail_addresses[] = $email;
					}
				}
			}
			$f_message_to_send = $output_final.'<br /><br />'.mail_bloc_adresse().'</body></html>';

			$mail_print = new mail_print();
			$mail_print->set_mail_to_mail(implode(';',$mail_addresses))
					->set_mail_content($f_message_to_send);
			$res_envoi = $mail_print->send_mail();
			if ($res_envoi) {
				print $pheader."<br /><br /><h3>".sprintf($msg["print_emailsucceed"],implode(', ',$mail_addresses))."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["print_emailclose"]."</a></body></html>" ;
			} else {
				print $pheader."<br /><br /><h3>".sprintf($msg["print_emailfailed"],implode(', ',$mail_addresses))."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["print_emailclose"]."</a></body></html>" ;
			}
			break;
		case 'printer':
			$output_final.= '<script type="text/javascript">self.print();</script>';
			$output_final.= '</body></html>';
			print pmb_bidi($output_final);
			break;
		case 'tt':
			$output_final.= '</body></html>';
			print pmb_bidi($output_final);
			break;
		case 'pdf':
			//Petite modification du style pour les vignettes sinon elles apparaissent coupees
			//Pb connu de Html2pdf avec les tables
			//TODO vérifier dans les templates sans tables
			$output_final.= "<style>td {width:350px;}</style>";
			$output_final.= "</body></html>";
			$html2pdf = new HTML_2_PDF();
			$html2pdf->setTestTdInOnePage(false);
			$html2pdf->writeHTML(pmb_bidi($output_final));
			$html2pdf->output('impression.pdf', 'I');
			break;
	}
	$_SESSION["PRINT"]=false;
}