<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart_info.php,v 1.96 2019-04-24 14:12:27 dgoron Exp $

//Actions et affichage du résultat pour un panier de l'opac
$base_path=".";
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path."/classes/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/filter_results.class.php");

// si paramétrage authentification particulière et pour le re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

require_once($include_path."/templates/cart.tpl.php");

if($opac_search_other_function){
	require_once($include_path."/".$opac_search_other_function);
}

?>
<html>
<head>
<meta name="robots" content="noindex, nofollow">
</head>
<body id='cart_info_body' class="cart_info_body">
<span id='cart_info_iframe_content' class='basket_is_not_empty'>
<?php

function add_query($requete) {
	global $cart_;
	global $opac_max_cart_items;
	global $msg;
	global $charset;
	global $opac_simplified_cart;
	
	$resultat=pmb_mysql_query($requete);
	$nbtotal=@pmb_mysql_num_rows($resultat);
	$n=0; $na=0;
	while ($r=pmb_mysql_fetch_object($resultat)) {
		if (count($cart_)<$opac_max_cart_items) {
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
	if (count($cart_)==$opac_max_cart_items){
		if ($opac_simplified_cart) {
			$message=$msg["cart_full_simplified"];
		} else {
			$message.=", ".$msg["cart_full"];
		}
	}
	
	return $message;
}

function change_basket_image($id_notice, $action=''){
	global $header;
	
	print "<script>
			var pmb_img_basket_small_20x20 = '".get_url_icon('basket_small_20x20.png')."';
			var pmb_img_basket_exist = '".get_url_icon('basket_exist.png')."';
			var pmb_img_white_basket = '".get_url_icon('white_basket.png')."';
			var pmb_img_record_in_basket = '".get_url_icon('record_in_basket.png')."';
			changeBasketImage(".$id_notice.", '".$action."', \"".rawurlencode($header)."\")
		</script>";
}

function add_notices_to_cart($notices){
	global $cart_;
	global $opac_max_cart_items;
	global $msg;
	global $opac_simplified_cart;

	$n=0; $na=0;
	$tab_notices = explode(",",$notices);
	$nbtotal=count($tab_notices);
	for($i=0 ; $i<count($tab_notices) ; $i++){
		if (count($cart_)<$opac_max_cart_items) {
			$as=array_search($tab_notices[$i],$cart_);
			if (($as===null)||($as===false)) {
				$cart_[]=$tab_notices[$i];
				change_basket_image($tab_notices[$i]);
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

function integrate_anonymous_cart(){
	global $opac_integrate_anonymous_cart;
	global $cart_integrate_anonymous_on_confirm;
	global $cart_integrate_anonymous_auto;
	global $opac_max_cart_items;
	global $cart_script;
	global $charset;
	global $msg;
	if(isset($_SESSION['cart_anonymous']) && count($_SESSION['cart_anonymous'])){ //Un panier anonyme est présent pour ce lecteur
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
		print $cart_script;
	}
}

print "<script type='text/javascript'>
		var msg_notice_title_basket = '".addslashes($msg["notice_title_basket"])."';
		var msg_record_display_add_to_cart = '".addslashes($msg["record_display_add_to_cart"])."';
		var msg_notice_title_basket_exist = '".addslashes($msg["notice_title_basket_exist"])."';
		var msg_notice_basket_remove = '".addslashes($msg["notice_basket_remove"])."';
		</script>";
print "<script type='text/javascript' src='".$include_path."/javascript/cart.js'></script>";

$cart_css = '';
if (file_exists($base_path.'/styles/'.$opac_default_style.'/cart.css')) {
	$cart_css = '<link rel="stylesheet" type="text/css" href="'.$base_path.'/styles/'.$opac_default_style.'/cart.css" />';
}
$vide_cache=filemtime("./styles/".$css."/".$css.".css");
print "<link rel=\"stylesheet\" href=\"./styles/".$css."/".$css.".css?".$vide_cache."\" />".$cart_css."
<span class='img_basket'><img src='".get_url_icon("basket_small_20x20.png")."' style='vertical-align:center; border:0px'/></span>&nbsp;";
$cart_=(isset($_SESSION["cart"]) ? $_SESSION["cart"] : array());
if (!count($cart_)) $cart_=array();

//$id doit être addslasher car il est utilisé dans des requetes
//$id=stripslashes($id);// attention id peut etre du type es123 (recherche externe)
if(isset($location)) $location += 0;
else $location = 0;
if(!isset($id)) $id = 0; // A ne pas caster pour les notices externes
$message="";
if (($id)&&(!$lvl)) {
	if(!isset($action)) $action ='';
	switch($action) {
		case 'remove':
			$as=array_search($id,$cart_);
			if (($as!==null)&&($as!==false)) {
				unset($cart_[$as]);
				change_basket_image($id, 'remove');
			}
			break;
		default:
			if (count($cart_)<$opac_max_cart_items) {
				$as=array_search($id,$cart_);
				$notice_header=htmlentities(substr(strip_tags(stripslashes(html_entity_decode($header,ENT_QUOTES))),0,45),ENT_QUOTES,$charset);
				if ($notice_header!=$header) $notice_header.="...";
				if (($as!==null)&&($as!==false)) {
					$message=sprintf($msg["cart_notice_exists"],$notice_header);
				} else {
					$cart_[]=$id;
					$message=sprintf($msg["cart_notice_add"],$notice_header);
					change_basket_image($id);
				}
				if ($opac_simplified_cart) {
					$message="";
				}
			} else {
				$message=$msg["cart_full".($opac_simplified_cart?'_simplified':'')];
			}
			break;
	}
} else if ($lvl) {
	$notices = '';
	$message = '';
	switch ($lvl) {
		case "section_see":
			//On regarde dans quelle type de navigation on se trouve
			$requete="SELECT num_pclass FROM docsloc_section WHERE num_location='".$location."' AND num_section='".$id."' ";
			$res=pmb_mysql_query($requete);
			$type_aff_navigopac=0;
			if(pmb_mysql_num_rows($res)){
				$type_aff_navigopac=pmb_mysql_result($res,0,0);
			}

			if($type_aff_navigopac == 0 or ($type_aff_navigopac == -1 && !$plettreaut)or ($type_aff_navigopac != -1 && $type_aff_navigopac != 0 && !isset($dcote) && !isset($nc))){
				//Pas de navigation ou navigation par les auteurs mais sans choix effectué
				$requete="create temporary table temp_n_id ENGINE=MyISAM ( select distinct expl_notice as notice_id from exemplaires where expl_section='".$id."' and expl_location='".$location."' )";
				pmb_mysql_query($requete);
				//On récupère les notices de périodique avec au moins un exemplaire d'un bulletin dans la localisation et la section
				$requete="INSERT INTO temp_n_id (select distinct bulletin_notice as notice_id from bulletins join exemplaires on bulletin_id=expl_bulletin where expl_section='".$id."' and expl_location='".$location."' )";
				pmb_mysql_query($requete);
				@pmb_mysql_query("alter table temp_n_id add index(notice_id)");
				$requete = "SELECT notice_id FROM temp_n_id ";				
				
			}elseif($type_aff_navigopac == -1 ){
				
				$requete="create temporary table temp_n_id ENGINE=MyISAM ( SELECT distinct expl_notice as notice_id from exemplaires where expl_section='".$id."' and expl_location='".$location."' )";
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
				$requete="create temporary table temp_n_id ENGINE=MyISAM select distinct expl_notice as notice_id from exemplaires where expl_location=$location and expl_section='$id' " ;
				if (strlen($dcote)) {
					$requete.= " and $expl_cote_cond ";
					$level_ref=strlen($dcote)+1;
				}
				@pmb_mysql_query($requete);

				$requete2 = "insert into temp_n_id (SELECT distinct bulletin_notice as notice_id FROM bulletins join exemplaires on expl_bulletin=bulletin_id where expl_location=$location and expl_section=$id ";
				if (strlen($dcote)) {
					$requete2.= " and $expl_cote_cond ";
				}
				$requete2.= ") ";
				@pmb_mysql_query($requete2);
				@pmb_mysql_query("alter table temp_n_id add index(notice_id)");
				
				//Calcul du classement
				$rq1_index="create temporary table union1 ENGINE=MyISAM (select distinct expl_cote from exemplaires, temp_n_id where expl_location='".$location."' and expl_section='".$id."' and expl_notice=temp_n_id.notice_id) ";
				$res1_index=pmb_mysql_query($rq1_index);
				$rq2_index="create temporary table union2 ENGINE=MyISAM (select distinct expl_cote from exemplaires join (select distinct bulletin_id from bulletins join temp_n_id where bulletin_notice=notice_id) as sub on (bulletin_id=expl_bulletin) where expl_location='".$location."' and expl_section='".$id."') ";
				$res2_index=pmb_mysql_query($rq2_index);			
				$req_index="select distinct expl_cote from union1 union select distinct expl_cote from union2";
				$res_index=pmb_mysql_query($req_index);
		
				if ($level_ref==0) $level_ref=1;
				
				while (($ct=pmb_mysql_fetch_object($res_index)) && $nc) {
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
				$message = add_notices_to_cart($notices);
			}
			break;
		case "concept_see":
			require_once($class_path."/skos/skos_concept.class.php");
			
			$concept = new skos_concept($id);
			$notices = implode(",", $concept->get_indexed_notices());
			$fr = new filter_results($notices);
			$notices = $fr->get_results();		
			$message = add_notices_to_cart($notices);
			break;
		case "listlecture":
			$liste = new liste_lecture($id);
			$notices = implode(',', $liste->notices);
			if ($notices) {
				$fr = new filter_results($notices);
				$notices = $fr->get_results();
			}
			$message = add_notices_to_cart($notices);
			global $sub;
			if($sub == "consult")
				print "<script>top.document.liste_lecture.action=\"index.php?lvl=show_list&sub=consultation&id_liste=".stripslashes($id)."\";top.document.liste_lecture.target=\"\"</script>";
			else
				print "<script>top.document.liste_lecture.action=\"index.php?lvl=show_list&sub=view&id_liste=".stripslashes($id)."\";top.document.liste_lecture.target=\"\"</script>";
			break;
		default:
			// classes pour la gestion des sélecteurs
			if(!isset($autoloader) || !is_object($autoloader)){
				require_once($class_path."/autoloader.class.php");
				$autoloader = new autoloader();
			}
			$autoloader->add_register("authorities_page_class",true);
			
			require_once($class_path."/caddie/caddie_controller.class.php");
			caddie_controller::set_user_query(stripslashes($user_query));
			$message = caddie_controller::proceed($id);
			break;
	}
}else if(!$lvl && isset($notices) && $notices){
	add_notices_to_cart($notices);
}
if(!count($cart_)) echo $msg["cart_empty".($opac_simplified_cart?'_simplified':'')]; else echo $message." <a href='#' onClick=\"parent.document.location='index.php?lvl=show_cart'; return false;\">".sprintf($msg["cart_contents".($opac_simplified_cart?'_simplified':'')],count($cart_))."</a>";
$_SESSION["cart"]=$cart_;
?>
</span>
<?php
if (!count($cart_)) {
	print "<script>document.getElementById('cart_info_iframe_content').setAttribute('class', 'basket_is_empty');</script>";
}
if ($opac_accessibility && isset($_SESSION["pmbopac_fontSize"])) {
	print "
		<script type='text/javascript' src='".$include_path."/javascript/misc.js'></script>
		<script type='text/javascript'>get_ref('cart_info_body').style['fontSize'] = '".$_SESSION["pmbopac_fontSize"]."';</script>";
}
if($opac_integrate_anonymous_cart && isset($_SESSION['cart_anonymous'])){
	integrate_anonymous_cart();
}
?>
</body>
</html>
