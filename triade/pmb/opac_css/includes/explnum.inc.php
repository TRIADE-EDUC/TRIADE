<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.inc.php,v 1.63 2019-05-29 09:03:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/auth_popup.class.php");
require_once($class_path."/explnum_licence/explnum_licence.class.php");
//require_once($class_path."/access.class.php");

// charge le tableau des extensions/mimetypes, on en a besoin en maj comme en affichage
function create_tableau_mimetype() {
	
	global $lang;
	global $charset;
	global $base_path;
	global $include_path;
	
	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	
	if (!empty($_mimetypes_bymimetype_) && sizeof($_mimetypes_bymimetype_)) return;
	
	$_mimetypes_bymimetype_ = array();
	$_mimetypes_byext_ = array();

	require_once ($include_path.'/parser.inc.php') ;
	
	$fonction = array ("MIMETYPE" => "__mimetype__");
	
	if (file_exists($include_path."/mime_types/".$lang."_subst.xml"))
		$fic_mime_types = $include_path."/mime_types/".$lang."_subst.xml";
	else
		$fic_mime_types = $include_path."/mime_types/".$lang.".xml";	

	$fonction = array ("MIMETYPE" => "__mimetype__");
	_parser_($fic_mime_types, $fonction, "MIMETYPELIST" ) ;
	
}

function extension_fichier($fichier) {
	
	$f = strrev($fichier);
	$ext = substr($f, 0, strpos($f,"."));
	return strtolower(strrev($ext));
}

function trouve_mimetype ($fichier, $ext='') {
	
	global $_mimetypes_byext_ ;
	if ($ext!='') {
		// chercher le mimetype associe a l'extension : si trouvee nickel, sinon : ""
		if ($_mimetypes_byext_[$ext]["mimetype"]) return $_mimetypes_byext_[$ext]["mimetype"] ;
	}
	if (extension_loaded('mime_magic') || extension_loaded('fileinfo')) {
		$mime_type = mime_content_type($fichier) ;
		if ($mime_type) return $mime_type ;
	}
	return '';
}
	
function __mimetype__($param) {
	
	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	
	$mimetype_rec = array() ;
	$mimetype_rec["plugin"] = $param["PLUGIN"] ;
	$mimetype_rec["icon"] = $param["ICON"] ;
	$mimetype_rec["label"] = (isset($param["LABEL"]) ? $param["LABEL"] : '');
	$mimetype_rec["embeded"] = $param["EMBEDED"] ;
	
	$_mimetypes_bymimetype_[$param["NAME"]] = $mimetype_rec ;
	
	for ($i=0; $i<count($param["EXTENSION"]) ; $i++  ) {
		$mimetypeext_rec = array() ;
		$mimetypeext_rec = $mimetype_rec ;
		$mimetypeext_rec["mimetype"] = $param["NAME"] ;
		if (isset($param["EXTENSION"][$i]["LABEL"])) {
			$mimetypeext_rec["label"] =  $param["EXTENSION"][$i]["LABEL"] ;
		}
		$_mimetypes_byext_[$param["EXTENSION"][$i]["value"]] = $mimetypeext_rec ;
	}
}


function icone_mimetype ($mimetype, $ext) {
	
	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	// trouve l'icone associée au mimetype
	// sinon trouve l'icone associée à l'extension
	/*
	echo "<pre>" ;
	print_r ($_mimetypes_bymimetype_) ;
	print_r ( $_mimetypes_byext_ ) ;
	echo "</pre>" ;
	echo "<br />-- $mimetype<br />-- $ext";
	*/
	if ($_mimetypes_bymimetype_[$mimetype]["icon"]) return $_mimetypes_bymimetype_[$mimetype]["icon"] ;
	if ($_mimetypes_byext_[$ext]["icon"]) return $_mimetypes_byext_[$ext]["icon"] ;
	return "unknown.gif" ;
} // fin icone_mimetype


// fonction retournant les infos d'exemplaires numériques pour une notice ou un bulletin donné
function show_explnum_per_notice($no_notice, $no_bulletin, $link_expl='') {
	
	// params :
	// $link_expl= lien associé à l'exemplaire avec !!explnum_id!! à mettre à jour
	global $dbh;
	global $charset;
	global $opac_url_base ;
	global $opac_visionneuse_allow;
	global $opac_photo_filtre_mimetype;
	global $opac_explnum_order;
	global $opac_show_links_invisible_docnums;
	global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
	global $memo_expl;
	global $nb_explnum_visible;
	
	$nb_explnum_visible = 0; // pour l'affichage en template de notice
	if (!$no_notice && !$no_bulletin) return "";
	
	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	create_tableau_mimetype() ;
	
	// récupération du nombre d'exemplaires
	$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut FROM explnum WHERE ";
	if ($no_notice && !$no_bulletin) $requete .= "explnum_notice='$no_notice' ";
	elseif (!$no_notice && $no_bulletin) $requete .= "explnum_bulletin='$no_bulletin' ";
	elseif ($no_notice && $no_bulletin) $requete .= "explnum_bulletin='$no_bulletin' or explnum_notice='$no_notice' ";
	if($no_notice)
		$requete .= "union SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
			FROM explnum, bulletins
			WHERE bulletin_id = explnum_bulletin
			AND bulletins.num_notice='".$no_notice."'";
	if ($opac_explnum_order) $requete .= " order by ".$opac_explnum_order;
	else $requete .= " order by explnum_mimetype, explnum_nom, explnum_id ";
	$res = pmb_mysql_query($requete, $dbh);
	$nb_ex = pmb_mysql_num_rows($res);
	
	$docnum_visible = true;
	$id_for_right = $no_notice;
	if($no_bulletin){
		$query = "select num_notice,bulletin_notice from bulletins where bulletin_id = ".$no_bulletin;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$infos = pmb_mysql_fetch_object($result);
			if($infos->num_notice){
				$id_for_right = $infos->num_notice;
			}else{
				$id_for_right = $infos->bulletin_notice;	
			}
		}
	}
	if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
		$ac= new acces();
		$dom_2= $ac->setDomain(2);
		$docnum_visible = $dom_2->getRights($_SESSION['id_empr_session'],$id_for_right,16);
	} else {
		$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$id_for_right."' and id_notice_statut=statut ";
		$myQuery = pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($myQuery)) {
			$statut_temp = pmb_mysql_fetch_object($myQuery);
			if(!$statut_temp->explnum_visible_opac)	$docnum_visible=false;
			if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$docnum_visible=false;
		} else $docnum_visible=false;
	}

	if ($nb_ex && ($docnum_visible || $opac_show_links_invisible_docnums)) {
		// on récupère les données des exemplaires
		$i = 1 ;
		$ligne_finale = '';
		$ligne = '';
		global $search_terms;
		$docnums_exists_flag = false;
		while (($expl = pmb_mysql_fetch_object($res))) {
			
			// couleur de l'img en fonction du statut
			if ($expl->explnum_docnum_statut) {
				$rqt_st = "SELECT * FROM explnum_statut WHERE  id_explnum_statut='".$expl->explnum_docnum_statut."' ";
				$Query_statut = pmb_mysql_query($rqt_st, $dbh)or die ($rqt_st." ".pmb_mysql_error()) ;
				$r_statut = pmb_mysql_fetch_object($Query_statut);
				$class_img = " class='docnum_".$r_statut->class_html."' ";
				if ($expl->explnum_docnum_statut>1) {
					$txt = $r_statut->opac_libelle;
				}else $txt="";
				$statut_libelle_div="
					<div id='zoom_statut_docnum".$expl->explnum_id."' style='border: 2px solid rgb(85, 85, 85); background-color: rgb(255, 255, 255); position: absolute; z-index: 2000; display: none;'>
						<b>$txt</b>
					</div>
				";			
			} else {
				$class_img = " class='docnum_statutnot1' " ;
				$txt = "" ;
			}

			$explnum_docnum_visible = true;
			$explnum_docnum_consult = true;
			if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
				$ac= new acces();
				$dom_3= $ac->setDomain(3);
				$explnum_docnum_visible = $dom_3->getRights($_SESSION['id_empr_session'],$expl->explnum_id,16);
				$explnum_docnum_consult = $dom_3->getRights($_SESSION['id_empr_session'],$expl->explnum_id,4);
			} else {
				$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon, explnum_consult_opac, explnum_consult_opac_abon FROM explnum, explnum_statut WHERE explnum_id ='".$expl->explnum_id."' and id_explnum_statut=explnum_docnum_statut ";
				$myQuery = pmb_mysql_query($requete, $dbh);
				if(pmb_mysql_num_rows($myQuery)) {
					$statut_temp = pmb_mysql_fetch_object($myQuery);
					if(!$statut_temp->explnum_visible_opac)	{
						$explnum_docnum_visible=false;
					}
					if(!$statut_temp->explnum_consult_opac)	{
						$explnum_docnum_consult=false;
					}
					if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session']) $explnum_docnum_visible = false;
					if($statut_temp->explnum_consult_opac_abon && !$_SESSION['id_empr_session'])	$explnum_docnum_consult=false;
				} else {
					$explnum_docnum_visible=false;
				}
			}
			if ($explnum_docnum_visible ||  $opac_show_links_invisible_docnums) {
				$docnums_exists_flag = true;
				if ($i==1) $ligne="<tr><td !!td_id_1!! class='docnum center' style='width:33%'>!!1!!</td><td  !!td_id_2!! class='docnum center' style='width:33%'>!!2!!</td><td !!td_id_3!! class='docnum center' style='width:33%'>!!3!!</td></tr>" ;
				$tlink = '';
				if ($link_expl) {
					$tlink = str_replace("!!explnum_id!!", $expl->explnum_id, $link_expl);
					$tlink = str_replace("!!notice_id!!", $expl->explnum_notice, $tlink);					
					$tlink = str_replace("!!bulletin_id!!", $expl->explnum_bulletin, $tlink);					
				} 
				$alt = htmlentities($expl->explnum_nom." - ".$expl->explnum_mimetype,ENT_QUOTES, $charset) ;
				
				if ($expl->explnum_vignette) $obj="<img src='".$opac_url_base."vig_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' border='0'>";
					else // trouver l'icone correspondant au mime_type
						$obj="<img src='".get_url_icon('mimetype/'.icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier), 1)."' alt='$alt' title='$alt' border='0'>";		
				$expl_liste_obj = "";
				
				$obj_suite="$statut_libelle_div
				<a  href='#' onmouseout=\"z=document.getElementById('zoom_statut_docnum".$expl->explnum_id."'); z.style.display='none'; \" onmouseover=\"z=document.getElementById('zoom_statut_docnum".$expl->explnum_id."'); z.style.display=''; \">
					<div class='vignette_doc_num' ><img $class_img width='10' height='10' src='".get_url_icon('spacer.gif')."'></div>
				</a>
				";
				$obj_suite.= explnum_licence::get_explnum_licence_picto($expl->explnum_id);
				
				$words_to_find="";
				if (($expl->explnum_mimetype=='application/pdf') ||($expl->explnum_mimetype=='URL' && (strpos($expl->explnum_nom,'.pdf')!==false))){
					if (is_array($search_terms)) {
						$words_to_find = "#search=\"".trim(str_replace('*','',implode(' ',$search_terms)))."\"";
					} 
				}
				//si l'affichage du lien vers les documents numériques est forcé et qu'on est pas connecté, on propose l'invite de connexion!
				if(!$explnum_docnum_visible && $opac_show_links_invisible_docnums && !$_SESSION['id_empr_session']){
					if ($opac_visionneuse_allow)
						$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
					if ($explnum_docnum_consult && $allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
						$link="
							<script type='text/javascript'>
								if(typeof(sendToVisionneuse) == 'undefined'){
									var sendToVisionneuse = function (explnum_id){
										document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
									}
								}
								function sendToVisionneuse_".$expl->explnum_id."(){
									open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");
								}
							</script>
							<a href='#' onclick=\"auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=sendToVisionneuse_".$expl->explnum_id."');\" title='$alt'>".$obj."</a>$obj_suite<br />";
						$expl_liste_obj .=$link;
					}else{
						$link="
							<a href='#' onclick=\"auth_popup('./ajax.php?module=ajax&categ=auth&new_tab=1&callback_url=".rawurlencode($opac_url_base."doc_num.php?explnum_id=".$expl->explnum_id)."')\" title='$alt'>".$obj."</a>$obj_suite<br />";
						$expl_liste_obj .=$link;
					}
				}else{
					if ($opac_visionneuse_allow)
						$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
					if ($explnum_docnum_consult && $allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
						$link="
							<script type='text/javascript'>
								if(typeof(sendToVisionneuse) == 'undefined'){
									var sendToVisionneuse = function (explnum_id){
										document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
									}
								}
							</script>
							<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");return false;\" title='$alt'>".$obj."</a>$obj_suite<br />";
						$expl_liste_obj .=$link;
					} else {
						$suite_url_explnum ="doc_num.php?explnum_id=$expl->explnum_id";
						$expl_liste_obj .= "<a href='".$opac_url_base.$suite_url_explnum."' title='$alt' target='_blank'>".$obj."</a>$obj_suite<br />" ;
					}
				}
	
				if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
				elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
				else $explmime_nom = $expl->explnum_mimetype ;
				
				
				if ($tlink) {
					$expl_liste_obj .= "<a href='$tlink'>";
					$expl_liste_obj .= "<span class='title_docnum'>".htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."</span></a><div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
				} else {
					$expl_liste_obj .= "<span class='title_docnum'>".htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."</span><div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
				}
				
				// mémorisation des exemplaires numériques et de leurs localisations
				$ids_loc = array();
				$requete_loc = "SELECT num_location	FROM explnum_location  WHERE num_explnum=" . $expl->explnum_id;				
				$result_loc = pmb_mysql_query($requete_loc, $dbh);
				if (pmb_mysql_num_rows($result_loc)) {
					while($loc = pmb_mysql_fetch_object($result_loc)) {
						$ids_loc[] = $loc->num_location;
					}
				}	
				
				$memo_expl['explnum'][]=array(
						'expl_id' => $expl->explnum_id,
						'expl_location'	=> $ids_loc,
						'id_notice' => $no_notice,
						'id_bulletin' => $no_bulletin
				);	

				$ligne = str_replace("!!td_id_" . $i . "!!", " id = 'explnum_" . $expl->explnum_id . "' ", $ligne);
				$ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
				$i++;
				if ($i==4) {
					$ligne_finale .= $ligne ;
					$i=1;
				}
				$nb_explnum_visible++; // pour l'affichage en template de notice
			}
		}
		if (!$ligne_finale) $ligne_finale = $ligne ;
		elseif ($i!=1) $ligne_finale .= $ligne ;
		$ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
		$ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
		$ligne_finale = str_replace('!!td_id_2!!', '', $ligne_finale);
		$ligne_finale = str_replace('!!td_id_3!!', '', $ligne_finale);
		
	} else return "";
	$entry = '';
	if($docnums_exists_flag){
		$entry .= "<table class='docnum'>$ligne_finale</table>";
	}
	return $entry;

}


/**
 * Fonction retournant les infos d'exemplaires numériques pour une notice ou un bulletin donné
 * @param int $explnum_id Identifiant du document numérique
 * @return string
 */
function show_explnum_per_id($explnum_id, $link_explnum = "") {
	
	global $dbh;
	global $charset;
	global $opac_url_base ;
	global $opac_visionneuse_allow;
	global $opac_photo_filtre_mimetype;
	global $opac_explnum_order;
	global $opac_show_links_invisible_docnums;
	global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
	global $search_terms;
	
	if (!$explnum_id) return "";
	
	global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
	create_tableau_mimetype() ;
	
	// récupération des infos du document
	$query = "select explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier , explnum_docnum_statut FROM explnum WHERE explnum_id = ".$explnum_id;
	$result = pmb_mysql_query($query, $dbh);
	if ($result && pmb_mysql_num_rows($result)) {
		if ($explnum = pmb_mysql_fetch_object($result)) {
			$docnum_visible = true;
			$id_for_right = $explnum->explnum_notice;
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
				$docnum_visible = $dom_2->getRights($_SESSION['id_empr_session'],$id_for_right,16);
			} else {
				$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$id_for_right."' and id_notice_statut=statut ";
				$myQuery = pmb_mysql_query($requete, $dbh);
				if(pmb_mysql_num_rows($myQuery)) {
					$statut_temp = pmb_mysql_fetch_object($myQuery);
					if(!$statut_temp->explnum_visible_opac)	$docnum_visible=false;
					if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$docnum_visible=false;
				} else 	$docnum_visible=false;
			}
			if ($docnum_visible) {
				if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
					$ac= new acces();
					$dom_3= $ac->setDomain(3);
					$docnum_visible = $dom_3->getRights($_SESSION['id_empr_session'],$explnum->explnum_id,16);
				} else {
					$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM explnum, explnum_statut WHERE explnum_id ='".$explnum->explnum_id."' and id_explnum_statut=explnum_docnum_statut ";
					$myQuery = pmb_mysql_query($requete, $dbh);
					if(pmb_mysql_num_rows($myQuery)) {
						$statut_temp = pmb_mysql_fetch_object($myQuery);
						if(!$statut_temp->explnum_visible_opac)	$docnum_visible=false;
						if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$docnum_visible=false;
					} else $docnum_visible=false;
				}
			}
			$tlink = '';
			if ($link_explnum) {
				$tlink = str_replace("!!explnum_id!!", $explnum->explnum_id, $link_explnum);
				$tlink = str_replace("!!notice_id!!", $explnum->explnum_notice, $tlink);					
				$tlink = str_replace("!!bulletin_id!!", $explnum->explnum_bulletin, $tlink);					
			}
			
			$alt = htmlentities($explnum->explnum_nom." - ".$explnum->explnum_mimetype,ENT_QUOTES, $charset) ;
			
			// couleur de l'img en fonction du statut
			if ($expl->explnum_docnum_statut) {
				$rqt_st = "SELECT * FROM explnum_statut WHERE  id_explnum_statut='".$expl->explnum_docnum_statut."' ";
				$Query_statut = pmb_mysql_query($rqt_st, $dbh)or die ($rqt_st. " ".pmb_mysql_error()) ;
				$r_statut = pmb_mysql_fetch_object($Query_statut);
				$class_img = " class='docnum_".$r_statut->class_html."' ";
				if ($expl->explnum_docnum_statut>1) {
					$txt = $r_statut->opac_libelle;
				}else $txt="";			
				$statut_libelle_div="
					<div id='zoom_statut_docnum".$expl->explnum_id."' style='border: 2px solid rgb(85, 85, 85); background-color: rgb(255, 255, 255); position: absolute; z-index: 2000; display: none;'>
						<b>$txt</b>
					</div>
				";			
			} else {
				$class_img = " class='docnum_statutnot1' " ;
				$txt = "" ;
			}
							
			if ($explnum->explnum_vignette) $obj="<img src='".$opac_url_base."vig_num.php?explnum_id=$explnum->explnum_id' alt='$alt' title='$alt' border='0'>";
				else // trouver l'icone correspondant au mime_type
					$obj="<img src='".get_url_icon('mimetype/'.icone_mimetype($explnum->explnum_mimetype, $explnum->explnum_extfichier), 1)."' alt='$alt' title='$alt' border='0'>";		
			$explnum_liste_obj = "";
			
			$obj.="$statut_libelle_div
				<a  href='#' onmouseout=\"z=document.getElementById('zoom_statut_docnum".$expl->explnum_id."'); z.style.display='none'; \" onmouseover=\"z=document.getElementById('zoom_statut_docnum".$expl->explnum_id."'); z.style.display=''; \">
					<div class='vignette_doc_num' ><img $class_img width='10' height='10' src='".get_url_icon('spacer.gif')."'></div>
				</a>
			";
			
			$words_to_find="";
			if (($explnum->explnum_mimetype=='application/pdf') ||($explnum->explnum_mimetype=='URL' && (strpos($explnum->explnum_nom,'.pdf')!==false))){
				if (is_array($search_terms)) {
					$words_to_find = "#search=\"".trim(str_replace('*','',implode(' ',$search_terms)))."\"";
				} 
			}
			
			//si l'affichage du lien vers les documents numériques est forcé et qu'on est pas connecté, on propose l'invite de connexion!
			if(!$docnum_visible && !$_SESSION['user_code'] && $opac_show_links_invisible_docnums){
				if ($opac_visionneuse_allow)
					$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				if ($allowed_mimetype && in_array($explnum->explnum_mimetype,$allowed_mimetype)){
					$link="
						<script type='text/javascript'>
							if(typeof(sendToVisionneuse) == 'undefined'){
								var sendToVisionneuse = function (explnum_id){
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
								}
							}
							function sendToVisionneuse_".$explnum->explnum_id."(){
								open_visionneuse(sendToVisionneuse,".$explnum->explnum_id.");
							}
						</script>
						<a href='#' onclick=\"auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=sendToVisionneuse_".$explnum->explnum_id."');\" title='$alt'>".$obj."</a><br />";
					$explnum_liste_obj .=$link;
				}else{
				$link="
						<a href='#' onclick=\"auth_popup('./ajax.php?module=ajax&categ=auth&new_tab=1&callback_url=".rawurlencode($opac_url_base."doc_num.php?explnum_id=".$explnum->explnum_id)."')\" title='$alt'>".$obj."</a><br />";
					$explnum_liste_obj .=$link;
				}
			}else{
				if ($opac_visionneuse_allow)
					$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				if ($allowed_mimetype && in_array($explnum->explnum_mimetype,$allowed_mimetype)){
					$link="
						<script type='text/javascript'>
							if(typeof(sendToVisionneuse) == 'undefined'){
								var sendToVisionneuse = function (explnum_id){
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
								}
							}
						</script>
						<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnum->explnum_id.");return false;\" title='$alt'>".$obj."</a><br />";
					$explnum_liste_obj .=$link;
				} else {
					$suite_url_explnum ="doc_num.php?explnum_id=$explnum->explnum_id";
					
					if(!$r_statut->explnum_download_opac){
						$explnum_liste_obj .= $obj."<br />" ;
					}else{
						$explnum_liste_obj .= "<a href='".$opac_url_base.$suite_url_explnum."' title='$alt' target='_blank'>".$obj."</a><br />" ;
					}
				}
			}

			if ($_mimetypes_byext_[$explnum->explnum_extfichier]["label"]) $explnummime_nom = $_mimetypes_byext_[$explnum->explnum_extfichier]["label"] ;
			elseif ($_mimetypes_bymimetype_[$explnum->explnum_mimetype]["label"]) $explnummime_nom = $_mimetypes_bymimetype_[$explnum->explnum_mimetype]["label"] ;
			else $explnummime_nom = $explnum->explnum_mimetype ;
						
			if ($tlink) {
				$explnum_liste_obj .= "<a href='$tlink'>";
				$explnum_liste_obj .= "<span class='title_docnum'>".htmlentities($explnum->explnum_nom,ENT_QUOTES, $charset)."</span></a><div class='explnum_type'>".htmlentities($explnummime_nom,ENT_QUOTES, $charset)."</div>";
			} else {
				$explnum_liste_obj .= "<span class='title_docnum'>".htmlentities($explnum->explnum_nom,ENT_QUOTES, $charset)."</span><div class='explnum_type'>".htmlentities($explnummime_nom,ENT_QUOTES, $charset)."</div>";
			}			
		} else return "";
	} else return "";
	return $explnum_liste_obj;
}


function &reduire_image_middle(&$data) {
	
	global $opac_photo_mean_size_x ;
	global $opac_photo_mean_size_y ;
	global $opac_photo_watermark;
	global $opac_photo_watermark_transparency;
	if ($opac_photo_watermark_transparency=="") $opac_photo_watermark_transparency=50;
	
	$src_img=imagecreatefromstring($data);
	if ($src_img) {
		$photo_mean_size_x=imagesx($src_img);
		$photo_mean_size_y=imagesy($src_img);
	} else {
		$photo_mean_size_x=200 ;
		$photo_mean_size_y=200 ;
	}
	if ($opac_photo_mean_size_x) $photo_mean_size_x=$opac_photo_mean_size_x;
	if ($opac_photo_mean_size_y) $photo_mean_size_y=$opac_photo_mean_size_y;
	
	if ($opac_photo_watermark) {
		$size = @getimagesize("images/".$opac_photo_watermark);
		/*   ".gif"=>"1",
		                   ".jpg"=>"2",
		                   ".jpeg"=>"2",
		                   ".png"=>"3",
		                   ".swf"=>"4",
		                   ".psd"=>"5",
		                   ".bmp"=>"6");
		*/
		switch ($size[2]) {
			case 1:
				$wat_img = imagecreatefromgif("images/".$opac_photo_watermark);
			 	break;
			case 2:
				$wat_img = imagecreatefromjpeg("images/".$opac_photo_watermark);
				break;
			case 3:
				$wat_img = imagecreatefrompng("images/".$opac_photo_watermark);
				break;
			case 6:
				$wat_img = imagecreatefromwbmp("images/".$opac_photo_watermark);
				break;
			default:
				$wat_img="";
				break;
		}
	}
	
	$erreur_vignette = 0 ;
	if ($src_img) {
		$rs=$photo_mean_size_x/$photo_mean_size_y;
		$taillex=imagesx($src_img);
		$tailley=imagesy($src_img);
		if (!$taillex || !$tailley) return "" ;
		if (($taillex>$photo_mean_size_x)||($tailley>$photo_mean_size_y)) {
			$r=$taillex/$tailley;
			if (($r<1)&&($rs<1)) {
				//Si x plus petit que y et taille finale portrait 
				//Si le format final est plus large en proportion
				if ($rs>$r) {
					$new_h=$photo_mean_size_y; 
					$new_w=$new_h*$r; 
				} else {
					$new_w=$photo_mean_size_x;
					$new_h=$new_w/$r;
				}
			} else if (($r<1)&&($rs>=1)){ 
				//Si x plus petit que y et taille finale paysage
				$new_h=$photo_mean_size_y;
				$new_w=$new_h*$r;  
			} else if (($r>1)&&($rs<1)) {
				//Si x plus grand que y et taille finale portrait
				$new_w=$photo_mean_size_x;
				$new_h=$new_w/$r;
			} else {
				//Si x plus grand que y et taille finale paysage
				if ($rs<$r) {
					$new_w=$photo_mean_size_x;
					$new_h=$new_w/$r;
				} else {
					$new_h=$photo_mean_size_y;
					$new_w=$new_h*$r;
				}
			}
		} else {
			$new_h = $tailley ;
			$new_w = $taillex ;
		}
			
		$dst_img=imagecreatetruecolor($photo_mean_size_x,$photo_mean_size_y);
		ImageSaveAlpha($dst_img, true);
		ImageAlphaBlending($dst_img, false);
		imagefilledrectangle($dst_img,0,0,$photo_mean_size_x,$photo_mean_size_y,imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
		imagecopyresized($dst_img,$src_img,round(($photo_mean_size_x-$new_w)/2),round(($photo_mean_size_y-$new_h)/2),0,0,$new_w,$new_h,ImageSX($src_img),ImageSY($src_img));
		if ($wat_img) {
			$wr_img=imagecreatetruecolor($photo_mean_size_x,$photo_mean_size_y);
			ImageSaveAlpha($wr_img, true);
			ImageAlphaBlending($wr_img, false);
			imagefilledrectangle($wr_img,0,0,$photo_mean_size_x,$photo_mean_size_y,imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
			imagecopyresized($wr_img,$wat_img,round(($photo_mean_size_x-$new_w)/2),round(($photo_mean_size_y-$new_h)/2),0,0,$new_w,$new_h,ImageSX($wat_img),ImageSY($wat_img));
			imagecopymerge($dst_img,$wr_img,0,0,0,0,$photo_mean_size_x,$photo_mean_size_y,$opac_photo_watermark_transparency);
		}
		imagepng($dst_img, "./temp/".session_id());
		$fp = fopen("./temp/".session_id() , "r" ) ;
		$contenu_vignette = fread ($fp, filesize("./temp/".session_id()));
		if (!$fp || $contenu_vignette=="") $erreur_vignette++ ;
		fclose ($fp) ;
		unlink("./temp/".session_id());
	} else $contenu_vignette = "" ;
	return $contenu_vignette ;
} // fin reduire_image