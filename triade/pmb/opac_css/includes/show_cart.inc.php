<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: show_cart.inc.php,v 1.78 2019-06-05 13:13:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// pour export panier
require_once($base_path.'/admin/convert/start_export.class.php');

if (isset($_GET['sort'])) {
	$_SESSION['last_sortnotices']=$_GET['sort'];
}
if (isset($count) && $count>$opac_nb_max_tri) {
	$_SESSION['last_sortnotices']='';
}

$cart_=(isset($_SESSION['cart']) ? $_SESSION['cart'] : array());

if (isset($raz_cart) && $raz_cart) {
	$cart_=array();
	$_SESSION['cart']=$cart_;
}

//Traitement des actions
if(!isset($action)) $action = '';
if ($action) {
	switch ($action) {
		case 'del':
			for ($i=0; $i<count($notice); $i++) {
				$as=array_search($notice[$i],$cart_);
				if (($as!==null)&&($as!==false)) {
					//Décalage
					for ($j=$as+1; $j<count($cart_); $j++) {
						$cart_[$j-1]=$cart_[$j];
					}
					unset($cart_[count($cart_)-1]);
				}
			}
			$_SESSION['cart']=$cart_;
			if (ceil(count($cart_)/$opac_search_results_per_page)<$page) $page=count($cart_)/$opac_search_results_per_page;
			break;
	}
}

print "<script type='text/javascript' >
var cart_all_checked = false;

function check_uncheck_all_cart() {
	if (cart_all_checked) {
		setCheckboxes('cart_form', 'notice', false);
		cart_all_checked = false;
		document.getElementById('show_cart_checked_all').value = pmbDojo.messages.getMessage('cart', 'show_cart_check_all');
		document.getElementById('show_cart_checked_all').title = pmbDojo.messages.getMessage('cart', 'show_cart_check_all');
	} else {
		setCheckboxes('cart_form', 'notice', true);
		cart_all_checked = true;
		document.getElementById('show_cart_checked_all').value = pmbDojo.messages.getMessage('cart', 'show_cart_uncheck_all');
		document.getElementById('show_cart_checked_all').title = pmbDojo.messages.getMessage('cart', 'show_cart_uncheck_all');
	}
	return false;
}

function setCheckboxes(the_form, the_objet, do_check) {
	 var elts = document.forms[the_form].elements[the_objet+'[]'] ;
	 var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
	 if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
	 		elts[i].checked = do_check;
	 	}
	 } else {
	 	elts.checked = do_check;
	 }
	 return true;
}

function confirm_transform(){
	var is_check=false;
	var elts = document.getElementsByName('notice[]') ;
	if (!elts) is_check = false ;
	var elts_cnt  = (typeof(elts.length) != 'undefined') ? elts.length : 0;
	if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
			if (elts[i].checked) {
				return true;
			}
		}
	}
	if(!is_check){
		alert(pmbDojo.messages.getMessage('opac', 'list_lecture_no_ck'));
		return false;
	}
	return is_check;
}

</script>";

print '<div id="cart_action">';

if (!isset($page) || $page=='') $page=1;
if (count($cart_)) {

	//gestion des notices externes (sauvegarde)
	$cart_ext = array();
	for($i=0;$i<sizeof($cart_);$i++){
		if(strpos($cart_[$i],'es') !== false){
			$cart_ext[] = $cart_[$i];
		}
	}

	print
	"<input type='button' id='show_cart_empty' class='bouton' value=\"".$msg['show_cart_empty']."\" title=\"".$msg['show_cart_empty_title']."\" onClick=\"document.location='./index.php?lvl=show_cart&raz_cart=1'\" />
	<span class=\"espaceCartAction\">&nbsp;</span>
	<input type='button' id='show_cart_del_checked' class='bouton' value=\"".$msg['show_cart_del_checked']."\" title=\"".$msg['show_cart_del_checked_title']."\" onClick=\"document.cart_form.submit();\" />
	<span class=\"espaceCartAction\">&nbsp;</span>
	<input type='button' id='show_cart_print' class='bouton' value=\"".$msg['show_cart_print']."\" title=\"".$msg['show_cart_print_title']."\" onClick=\"w=window.open('print.php?lvl=cart','print_window','width=500, height=750,scrollbars=yes,resizable=1'); w.focus();\" />
	<span class=\"espaceCartAction\">&nbsp;</span>
	<input type='button' id='show_cart_checked_all' class='bouton' value=\"".$msg['show_cart_check_all']."\" title=\"".$msg['show_cart_check_all']."\" onClick=\"check_uncheck_all_cart();\" />";

	if($opac_allow_download_docnums) {
		print "
		 <script type='text/javascript' >
			function download_docnum() {
				var url='./ajax.php?module=ajax&categ=download_docnum&sub=gen_list';
				window.open(url);

			}
			function download_docnum_notice_checked() {
				var is_check=false;
				var elts = document.getElementsByName('notice[]') ;
				var elts_chk = '';
				var elts_cnt  = (typeof(elts.length) != 'undefined')
		                  ? elts.length
		                  : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						if (elts[i].checked) {
							if (elts_chk == '') {
								elts_chk += elts[i].value;
							} else {
								elts_chk += ','+elts[i].value;
							}
						}
					}
				}
				if(elts_chk != '') {
					is_check=true;
				}
				if(!is_check){
					alert('".$msg['docnum_download_no_ck']."');
					return false;
				}
				var url='./ajax.php?module=ajax&categ=download_docnum&sub=gen_list';
				window.open(url+'&select_noti='+elts_chk);
			}
		</script>";
		print 
		"<br /><br />
			<input type='button' id='docnum_download_caddie' class='bouton' value=\"".$msg['docnum_download_caddie']."\" title=\"".$msg['docnum_download_caddie']."\" onClick=\"download_docnum();\" />
			<span class=\"espaceCartAction\">&nbsp;</span>
			<input type='button' id='docnum_download_checked' class='bouton' value=\"".$msg['docnum_download_checked']."\" title=\"".$msg['docnum_download_checked']."\" onClick=\"download_docnum_notice_checked();\" />
			<div id='http_response'></div>";
	}
	if($opac_shared_lists && $allow_liste_lecture && $id_empr){
		print 
		"<br /><br />
			<input type='button' id='list_lecture_transform_caddie' class='bouton' value=\"".$msg['list_lecture_transform_caddie']."\" title=\"".$msg['list_lecture_transform_caddie_title']."\" onClick=\"document.location='./index.php?lvl=show_list&sub=transform_caddie'\" />
			<span class=\"espaceCartAction\">&nbsp;</span>
			<input type='button' id='list_lecture_transform_checked' class='bouton' value=\"".$msg['list_lecture_transform_checked']."\" title=\"".$msg['list_lecture_transform_checked_title']."\" onClick=\"document.cart_form.action='./index.php?lvl=show_list&sub=transform_check';if(confirm_transform()) document.cart_form.submit(); else return false;\" />";
	}
	if ($opac_show_suggest && $opac_allow_multiple_sugg && $allow_sugg && $id_empr) {
		print "
		 <script type='text/javascript' >
		 function notice_checked(){
			var is_check=false;
			var elts = document.getElementsByName('notice[]') ;
			if (!elts) is_check = false ;
			var elts_cnt  = (typeof(elts.length) != 'undefined')
	                  ? elts.length
	                  : 0;
			if (elts_cnt) {
				for (var i = 0; i < elts_cnt; i++) {
					if (elts[i].checked) {
						return true;
					}
				}
			}
			if(!is_check){
				alert(pmbDojo.messages.getMessage('opac', 'list_lecture_no_ck'));
				return false;
			}

			return is_check;
		}
		</script>
		";
		print '<br /><br />';
		print "<input type='button' id='transform_caddie_to_multisugg' class='bouton' value=\"".$msg['transform_caddie_to_multisugg']."\" title=\"".$msg['transform_caddie_to_multisugg_title']."\" onClick=\"document.getElementById('div_src_sugg').style.display='';\" />";
		print "<span class=\"espaceCartAction\">&nbsp;</span>
		<input type='button' id='transform_caddie_notice_to_multisugg' class='bouton' value=\"".$msg['transform_caddie_notice_to_multisugg']."\" title=\"".$msg['transform_caddie_notice_to_multisugg']."\" onClick=\"if(notice_checked()){ document.getElementById('div_src_sugg').style.display='';} else return false; \" />";
		print '<div class="row" id="div_src_sugg" style="display:none" >';
		print '<label class="etiquette">'.$msg['empr_sugg_src'].': </label>';
		//Affichage du selecteur de source
		$req = 'select * from suggestions_source order by libelle_source';
		$res= pmb_mysql_query($req,$dbh);
		$option = '<option value="0" selected="selected">'.htmlentities($msg['empr_sugg_no_src'],ENT_QUOTES,$charset).'</option>';
		while(($src=pmb_mysql_fetch_object($res))){
			$option .= '<option value="'.$src->id_source.'" >'.htmlentities($src->libelle_source,ENT_QUOTES,$charset).'</option>';
		}
		$selecteur = '<select id="sug_src" name="sug_src">'.$option.'</select>';
		print $selecteur;
		print "<input type='button' class='bouton' value=\"".$msg[11]."\" onClick=\"document.cart_form.action='./empr.php?lvl=transform_to_sugg&act=transform_caddie&sug_src='+document.getElementById('sug_src').value;document.cart_form.submit();\" />";
		print '</div>';
	}

	//resas
	if (!isset($id_empr)) {
	    $id_empr = '';
	}
	if($opac_resa && $opac_resa_planning!=1 && $id_empr && $opac_resa_cart) {

		print '<br /><br />';
		if($opac_resa_popup){
			print "<input type='button' id='show_cart_reserve' class='bouton' value=\"".$msg['show_cart_reserve']."\" title=\"".$msg['show_cart_reserve_title']."\"
					onClick=\"
						w=window.open('./do_resa.php?lvl=resa_cart&sub=resa_cart','doresa','scrollbars=yes,width=900,height=300,menubar=0,resizable=yes'); w.focus(); return false;
					\" /><span class=\"espaceCartAction\">&nbsp;</span>

				   <input type='button' id='show_cart_reserve_checked' class='bouton' value=\"".$msg['show_cart_reserve_checked']."\" title=\"".$msg['show_cart_reserve_checked_title']."\"
					onClick=\"
						var notice='';
						var data=document.forms['cart_form'].elements['notice[]'];

						if(typeof(data.length) != 'undefined'){
				   			for (var key = 0; key < data.length; key++) {
								if(data[key].checked && data[key].value){
									notice+='&notice[]='+data[key].value;
								}
							}
						}else{
							if(data.checked && data.value){
								notice+='&notice[]='+data.value;
							}
						}

						if(notice!=''){
							w=window.open('./do_resa.php?lvl=resa_cart&sub=resa_cart_checked'+notice,'doresa','scrollbars=yes,width=900,height=300,menubar=0,resizable=yes');
							w.focus();
							return false;
						}else{
							alert('".$msg['resa_no_doc_selected']."')
							return false;
						}
					\" />";
		}else{
			print "<input type='button' id='show_cart_reserve' class='bouton' value=\"".$msg['show_cart_reserve']."\" title=\"".$msg['show_cart_reserve_title']."\" onClick=\"
						document.location='./do_resa.php?lvl=resa_cart&sub=resa_cart';
					\" /><span class=\"espaceCartAction\">&nbsp;</span>
				   <input type='button' id='show_cart_reserve_checked' class='bouton' value=\"".$msg['show_cart_reserve_checked']."\" title=\"".$msg['show_cart_reserve_checked_title']."\" onClick=\"
						var notice='';
						var data=document.forms['cart_form'].elements['notice[]'];

						if(typeof(data.length) != 'undefined'){
							for (var key = 0; key < data.length; key++) {
								if(data[key].checked && data[key].value){
									notice+='&notice[]='+data[key].value;
								}
							}
						}else{
							if(data.checked && data.value){
								notice+='&notice[]='+data.value;
							}
						}

						if(notice!=''){
							document.location='./do_resa.php?lvl=resa_cart&sub=resa_cart_checked'+notice;
						}else{
							alert('".$msg['resa_no_doc_selected']."')
							return false;
						}

					\" />";
		}

	//resas planifiees
	} elseif($opac_resa && $opac_resa_planning=='1' && $id_empr && $opac_resa_cart) {

		print '<br /><br />';
		if($opac_resa_popup){

			print "<input type='button' id='show_cart_reserve' class='bouton' value=\"".$msg['show_cart_reserve']."\" title=\"".$msg['show_cart_reserve_title']."\"
					onClick=\"
						w=window.open('./do_resa.php?lvl=resa_cart&sub=resa_cart','doresa','scrollbars=yes,width=900,height=300,menubar=0,resizable=yes'); w.focus(); return false;
					\" /><span class=\"espaceCartAction\">&nbsp;</span>

				   <input type='button' id='show_cart_reserve_checked' class='bouton' value=\"".$msg['show_cart_reserve_checked']."\" title=\"".$msg['show_cart_reserve_checked_title']."\"
					onClick=\"
						var notice='';
						var data=document.forms['cart_form'].elements['notice[]'];

						if(typeof(data.length) != 'undefined'){
				   			for (var key = 0; key < data.length; key++) {
								if(data[key].checked && data[key].value){
									notice+='&notice[]='+data[key].value;
								}
							}
						}else{
							if(data.checked && data.value){
								notice+='&notice[]='+data.value;
							}
						}

						if(notice!=''){
							w=window.open('./do_resa.php?lvl=resa_cart&sub=resa_planning_cart_checked'+notice,'doresa','scrollbars=yes,width=900,height=300,menubar=0,resizable=yes');
							w.focus();
							return false;
						}else{
							alert('".$msg['resa_no_doc_selected']."')
							return false;
						}
					\" />";

		} else {

			print "<input type='button' id='show_cart_reserve' class='bouton' value=\"".$msg['show_cart_reserve']."\" title=\"".$msg['show_cart_reserve_title']."\" onClick=\"
						document.location='./do_resa.php?lvl=resa_cart&sub=resa_planning_cart';
					\" /><span class=\"espaceCartAction\">&nbsp;</span>
				   <input type='button' id='show_cart_reserve_checked' class='bouton' value=\"".$msg['show_cart_reserve_checked']."\" title=\"".$msg['show_cart_reserve_checked_title']."\" onClick=\"
						var notice='';
						var data=document.forms['cart_form'].elements['notice[]'];

						if(typeof(data.length) != 'undefined'){
							for (var key = 0; key < data.length; key++) {
								if(data[key].checked && data[key].value){
									notice+='&notice[]='+data[key].value;
								}
							}
						}else{
							if(data.checked && data.value){
								notice+='&notice[]='+data.value;
							}
						}

						if(notice!=''){
							document.location='./do_resa.php?lvl=resa_cart&sub=resa_planning_cart_checked'+notice;
						}else{
							alert('".$msg['resa_no_doc_selected']."')
							return false;
						}

					\" />";
		}
	}

	// Demande de numérisation
	if ($opac_scan_request_activate && $allow_scan_request && $id_empr) {
		print "<br /><br /><input type='button' id='scan_request_from_caddie' class='bouton' value=\"".$msg["scan_request_from_caddie"]."\" title=\"".$msg["scan_request_from_caddie_title"]."\" onClick=\"document.location='./empr.php?tab=scan_requests&lvl=scan_request&sub=edit&from=caddie'\" /><span class=\"espaceCartAction\">&nbsp;</span>
		<input type='button' id='scan_request_from_checked' class='bouton' value=\"".$msg["scan_request_from_checked"]."\" title=\"".$msg["scan_request_from_checked_title"]."\" onClick=\"document.cart_form.action='./empr.php?tab=scan_requests&lvl=scan_request&sub=edit&from=checked';if(confirm_transform()) document.cart_form.submit(); else return false;\" />";
	}
	
	//Tri
	if (isset($_SESSION['last_sortnotices']) && $_SESSION['last_sortnotices'] != '') {
		$sort=new sort('notices','session');
		$sql = "SELECT notice_id FROM notices WHERE notice_id IN (";
		for ($z=0; $z<count($cart_); $z++) {
			$sql.="'". $cart_[$z]."',";
		}
		$sql = substr($sql, 0, strlen($sql) - 1) .")";

		$sql=$sort->appliquer_tri($_SESSION['last_sortnotices'],$sql,'notice_id',0,0);
	} else {
		$sql="select notice_id from notices where notice_id in ('".implode("','",$cart_)."') order by tit1";
	}

	$res=pmb_mysql_query($sql,$dbh);
	$cart_=array();
	while ($r=pmb_mysql_fetch_object($res)) {
		$cart_[]=$r->notice_id;
	}
	if($cart_ext) $cart_ = array_merge($cart_,$cart_ext);
	$_SESSION['cart']=$cart_;

	if (($opac_export_allow=='1') || (($opac_export_allow=='2') && ($_SESSION['user_code']))) {
		$nb_fiche=0;
		$nb_fiche_total=count($cart_);

		for ($z=0; $z<$nb_fiche_total; $z++) {
			if (substr($cart_[$z],0,2)!="es"){
				// Exclure de l'export (opac, panier) les fiches interdites de diffusion dans administration, Notices > Origines des notices NG72
				$sql="select 1 from origine_notice,notices where notice_id = '$cart_[$z]' and origine_catalogage = orinot_id and orinot_diffusion='1' ";
			} else {
				$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes(substr($cart_[$z],2));
				$myQuery = pmb_mysql_query($requete, $dbh);
				$source_id = pmb_mysql_result($myQuery, 0, 0);
				$sql="select 1 from entrepot_source_$source_id where recid='".addslashes(substr($cart_[$z],2))."' group by ufield,usubfield,field_order,subfield_order,value";
			}
			$res=pmb_mysql_query($sql,$dbh);
			if ($ligne=pmb_mysql_fetch_array($res))
				$nb_fiche++;
		}
		if ($nb_fiche!=$nb_fiche_total) {
			$msg_export_partiel = str_replace ('!!nb_export!!',$nb_fiche, $msg['export_partiel']);
			$msg_export_partiel = str_replace ('!!nb_total!!',$nb_fiche_total, $msg_export_partiel);
			$js_export_partiel = "if (confirm('".addslashes($msg_export_partiel)."')) {";
		} else $js_export_partiel = "if (true) {";

		print "<form name='export_form'><br />";
		$radio = "<br />
			<input type='radio' name='radio_exp' id='radio_exp_all' value='0' checked /><label for='radio_exp_all'>".htmlentities($msg['export_cart_all'],ENT_QUOTES,$charset)."</label>
			<input type='radio' name='radio_exp' id='radio_exp_sel' value='1' /><label for='radio_exp_sel'>".htmlentities($msg['export_cart_selected'],ENT_QUOTES,$charset)."</label>
		";

		$exp = start_export::get_exports();
		$selector_exp = "<select name='typeexport'>" ;
		for ($i=0;$i<count($exp);$i++) {
			$selector_exp .= "<option value='".$exp[$i]['ID']."'>".$exp[$i]['NAME']."</option>";
		}
		$selector_exp .= "</select>" ;
		print sprintf($msg['show_cart_export']."<span class=\"espaceCartAction\">&nbsp;</span>",$selector_exp.$radio);
		print "<script type='text/javascript' >
			function getNoticeSelected(){
				if(document.getElementById('radio_exp_sel').checked){
					var items = '&select_item=';
					var notices = document.forms['cart_form'].elements;
					var hasSelected = false;
					for (var i = 0; i < notices.length; i++) {
					 	if(notices[i].checked) {
					 		items += notices[i].value+',';
							hasSelected = true;
						}
					}
					if(!hasSelected) {
						alert(pmbDojo.messages.getMessage('opac', 'list_lecture_no_ck'));
						return false;
					} else return items;
				}
				return true;
			}
		</script>";
		print "<span class=\"espaceCartAction\">&nbsp;</span><input type='button' class='bouton' value=\"".$msg['show_cart_export_ok']."\" onClick=\"$js_export_partiel if(getNoticeSelected()){ document.location='./export.php?action=export&typeexport='+document.export_form.typeexport.options[top.document.export_form.typeexport.selectedIndex].value+getNoticeSelected();}}\" />";
		print '</form>';
		}
	}

print '</div>';

if (count($cart_)) {
	print '<h3 class="title_basket">
			<span>'.$msg['show_cart_content'].'</span>
			: <b>'.sprintf($msg['show_cart_n_notices'],count($cart_)).'</b>
			</h3>';

	print '<div class="search_result">';
	if ($opac_notices_depliable) print $begin_result_liste;

	if (count($cart_)<=$pmb_nb_max_tri) {
		$affich_tris_result_liste = sort::show_tris_selector();
		$affich_tris_result_liste = str_replace('!!page_en_cours!!','lvl=show_cart',$affich_tris_result_liste);
		$affich_tris_result_liste = str_replace('!!page_en_cours1!!','lvl=show_cart',$affich_tris_result_liste);
		print $affich_tris_result_liste;
	}
	
	if (isset($_SESSION['last_sortnotices']) && $_SESSION['last_sortnotices'] != '')
		print "<span class='sort'>".$msg['tri_par'].' '.$sort->descriptionTriParId($_SESSION['last_sortnotices']).'<span class="espaceCartAction">&nbsp;</span></span>';

	print '<blockquote>';

	// case à cocher de suppression transférée dans la classe notice_affichage
	$cart_aff_case_traitement = 1 ;
	print '<form action="./index.php?lvl=show_cart&action=del&page='.$page.'" method="post" name="cart_form">';
	for ($i=(($page-1)*$opac_search_results_per_page); (($i<count($cart_))&&($i<($page*$opac_search_results_per_page))); $i++) {
		if (substr($cart_[$i],0,2)!='es') {
			print pmb_bidi(aff_notice($cart_[$i],1));
		} else {
			print pmb_bidi(aff_notice_unimarc(substr($cart_[$i],2),1));
		}
	}
	print '</form></blockquote></div>';
	if(!isset($nb_per_page_custom)) $nb_per_page_custom = '';
	print '<div id="cart_navbar"><hr /><div style="text-align:center">'.printnavbar($page, count($cart_), $opac_search_results_per_page, './index.php?lvl=show_cart&page=!!page!!&nbr_lignes='.count($cart_).($nb_per_page_custom ? "&nb_per_page_custom=".$nb_per_page_custom : '')).'</div></div>';
} else {
	print '<h3><span>'.$msg['show_cart_is_empty'].'</span></h3>';
}
?>