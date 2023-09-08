<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_update.inc.php,v 1.56 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


require_once($class_path."/authperso_notice.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/vedette/vedette_link.class.php");
require_once($class_path."/notice_relations.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/serials.class.php");
require_once($class_path."/indexation_stack.class.php");

if($gestion_acces_active==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
}

require_once($class_path."/index_concept.class.php");

//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	$dom_1= $ac->setDomain(1);
	$acces_m = $dom_1->getRights($PMBuserid,$serial_id,8);
}

if ($acces_m==0) {
	
	if (!$bul_id) {
		error_message('', htmlentities($dom_1->getComment('mod_seri_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
	}
		
} else {

	
	// script d'update d'un bulletinage
	
	// nettoyage des valeurs du form
	// les valeurs passees sont mises en tableau pour etre passees
	// a la methode de mise a jour
	$table = array();
	$table['bul_no']      = clean_string($bul_no);
	$table['bul_date']    = clean_string($bul_date);
	$table['date_date']    = extraitdate($date_date_lib) ;
	
	$table['bul_cb']    = clean_string($bul_cb);
	$table['bul_titre'] = $bul_titre ;
	
	//création de notice de bulletin si case à cocher
	if (isset($create_notice_bul) && $create_notice_bul) {
		$table['create_notice_bul']=true;
	}
	
	// mise a jour de l'entete de page
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['catalog_serie_modif_bull'], $serial_header);
	
	// nettoyage des valeurs du form
	$f_tit3 = clean_string($f_tit3);
	$f_tit4 = clean_string($f_tit4);
	//$f_n_gen = clean_string($f_n_gen);
	//$f_n_resume = clean_string($f_n_resume);
	//$f_indexation = clean_string($f_indexation);
	$f_lien = clean_string($f_lien);
	$f_eformat = clean_string($f_eformat);
	
	// les valeurs passees sont mises en tableau pour etre passees
	// a la methode de mise à jour
	//$table = array();
	$table['typdoc']        = $typdoc;
	$table['statut']        = $form_notice_statut;
	$table['commentaire_gestion'] =  $f_commentaire_gestion ;
	$table['thumbnail_url'] =  $f_thumbnail_url ;
	$table['code']          = (isset($f_cb) ? $f_cb : '');
	$table['tit1']          = $table["bul_no"].($table["bul_date"]?" - ".$table["bul_date"]:"").($table["bul_titre"]?" - ".$table["bul_titre"]:"");
	$table['tit3']          = $f_tit3;
	$table['tit4']          = $f_tit4;
	$table['num_notice_usage'] = $form_num_notice_usage;
	
	// auteur principal
	$f_aut[] = array (
			'id' => $f_aut0_id,
			'fonction' => $f_f0_code,
			'type' => '0',
			'ordre' => 0 );
	// autres auteurs
	for ($i=0; $i<$max_aut1; $i++) {
		$var_autid = "f_aut1_id$i" ;
		$var_autfonc = "f_f1_code$i" ;
		$f_aut[] = array (
				'id' => ${$var_autid},
				'fonction' => ${$var_autfonc},
				'type' => '1',
				'ordre' => $i );
	}
	// auteurs secondaires
	for ($i=0; $i<$max_aut2 ; $i++) {
	
		$var_autid = "f_aut2_id$i" ;
		$var_autfonc = "f_f2_code$i" ;
		$f_aut[] = array (
				'id' => ${$var_autid},
				'fonction' => ${$var_autfonc},
				'type' => '2',
				'ordre' => $i );
	}
	
	$table['aut'] = $f_aut;
	
	$table['ed1_id']        = (isset($f_ed1_id) ? $f_ed1_id : 0);
	$table['ed2_id']        = (isset($f_ed2_id) ? $f_ed2_id : 0);
	$table['n_gen']         = $f_n_gen;
	$table['n_contenu']		= $f_n_contenu;
	$table['n_resume']      = $f_n_resume;
	
// categories		
	if($tab_categ_order){
		$categ_order=explode(",",$tab_categ_order);
		$order=0;
		foreach($categ_order as $old_order){
			$var_categid = "f_categ_id$old_order" ;
			if($var_categid){
				$f_categ[] = array (
						'id' => ${$var_categid},
						'ordre' => $order );
				$order++;
			}	
		}
	}else{
		for ($i=0; $i< $max_categ ; $i++) {
			$var_categid = "f_categ_id$i" ;
			$f_categ[] = array (
					'id' => ${$var_categid},
					'ordre' => $i );
		}
	}
	
	$table['categ']=$f_categ;

	$table['concept'] = index_concept::is_concept_in_form();
	
	$table['indexint']      = $f_indexint_id;
	$table['index_l']       = clean_tags($f_indexation);
	$table['lien']          = $f_lien;
	$table['eformat']       = $f_eformat;
	$table['niveau_biblio'] = $b_level;
	$table['niveau_hierar'] = $h_level;
	$table['ill']			= $f_ill;
	$table['size']			= $f_size;
	$table['prix']			= $f_prix;
	$table['accomp']		= $f_accomp;
	$table['npages']		= $f_npages;
	$table['indexation_lang'] = $indexation_lang;
	
	if($table['date_date'] == '0000-00-00' || !isset($date_date_lib)) $table['year'] = "";
	else $table['year'] = substr($table['date_date'],0,4);
	
	$table['date_parution'] = $table['date_date'];
		
	$p_perso=new parametres_perso("notices");
	$nberrors=$p_perso->check_submited_fields();
	
	$table['force_empty'] = $p_perso->presence_exclusion_fields();
	
	if($_FILES['f_img_load']['name'] && $pmb_notice_img_folder_id){
		$table['force_empty'] = "f_img_load";
	}
	
	//Pour la synchro rdf
	if($pmb_synchro_rdf){
		require_once($class_path."/synchro_rdf.class.php");
		$synchro_rdf=new synchro_rdf();
		if($bul_id){
			$synchro_rdf->delRdf(0,$bul_id);
		}
	}
	if (!$nberrors) {
		$myBulletinage = new bulletinage($bul_id, $serial_id);
		
		$req_notice_date_is_new="";
		if($myBulletinage->bull_num_notice) {
			$req_new="select notice_is_new, notice_date_is_new from notices where notice_id=".$myBulletinage->bull_num_notice;
			$res_new=pmb_mysql_query($req_new, $dbh);
			if (pmb_mysql_num_rows($res_new)) {
				if($r=pmb_mysql_fetch_object($res_new)){
					if($r->notice_is_new==$f_notice_is_new){ // pas de changement du flag
						$req_notice_date_is_new= "";
					}elseif($f_notice_is_new){ // Changement du flag et affecté comme new
						$req_notice_date_is_new= ", notice_date_is_new =now(), notice_is_new=1 ";
					}else{// raz date
						$req_notice_date_is_new= ", notice_date_is_new ='', notice_is_new=0 ";
					}
				}
			}
		}else{
			if($f_notice_is_new){ // flag affecté comme new en création
				$req_notice_date_is_new = ", notice_date_is_new =now(), notice_is_new=" . (int) $f_notice_is_new;
			}
		}
		
		$result = $myBulletinage->update($table,false,$req_notice_date_is_new);
	} else {
		error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
		exit();
	}
	
	// autorité personnalisées
	if($myBulletinage->bull_num_notice){
		$authperso = new authperso_notice($myBulletinage->bull_num_notice);
		$authperso->save_form();
	}	
	
	// vignette de la notice uploadé dans un répertoire
	$uploaded_thumbnail_url = thumbnail::create($myBulletinage->bull_num_notice);
	if($uploaded_thumbnail_url) {
		$query = "update notices set thumbnail_url='".$uploaded_thumbnail_url."' where notice_id ='".$myBulletinage->bull_num_notice."'";
		pmb_mysql_query($query);
	}
	
	$update_result=$myBulletinage->bull_num_notice;
	
	if ($update_result) {
		//Traitement des liens
		$notice_relations = notice_relations_collection::get_object_instance($update_result);
		$notice_relations->set_properties_from_form();
		$notice_relations->save();
		
		// Clean des vedettes
		$id_vedettes_links_deleted=bulletinage::delete_vedette_links($update_result);
		
		// traitement des auteurs
		$rqt_del = "DELETE FROM responsability WHERE responsability_notice='$update_result' ";
		$res_del = pmb_mysql_query($rqt_del, $dbh);
		$rqt_ins = "INSERT INTO responsability (responsability_author, responsability_notice, responsability_fonction, responsability_type, responsability_ordre) VALUES ";
		$i=0;		
		$var_name='notice_role_composed';
		$role_composed=${$var_name};
		$var_name='notice_role_autre_composed';
		$role_composed_autre=${$var_name};
		$var_name='notice_role_secondaire_composed';
		$role_composed_secondaire=${$var_name};	
		$id_vedettes_used=array();	
		while ($i<=count ($f_aut)-1) {
			$id_aut=$f_aut[$i]['id'];
			if ($id_aut) {
				$fonc_aut=$f_aut[$i]['fonction'];
				$type_aut=$f_aut[$i]['type'];
				$ordre_aut = $f_aut[$i]['ordre'];
				$rqt = $rqt_ins . " ('$id_aut','$update_result','$fonc_aut','$type_aut', $ordre_aut) " ; 
				$res_ins = @pmb_mysql_query($rqt, $dbh);
				$id_responsability=pmb_mysql_insert_id();
				if($pmb_authors_qualification){
					$id_vedette=0;
					switch($type_aut){
						case 0:
							$id_vedette=bulletinage::update_vedette(stripslashes_array($role_composed[$ordre_aut]),$id_responsability,TYPE_NOTICE_RESPONSABILITY_PRINCIPAL);
							break;
						case 1:
							$id_vedette=bulletinage::update_vedette(stripslashes_array($role_composed_autre[$ordre_aut]),$id_responsability,TYPE_NOTICE_RESPONSABILITY_AUTRE);
							break;
						case 2:
							$id_vedette=bulletinage::update_vedette(stripslashes_array($role_composed_secondaire[$ordre_aut]),$id_responsability,TYPE_NOTICE_RESPONSABILITY_SECONDAIRE);
							break;
					}
					if($id_vedette)$id_vedettes_used[]=$id_vedette; 
				}
			}
			$i++;
		}
		foreach ($id_vedettes_links_deleted as $id_vedette){
			if(!in_array($id_vedette,$id_vedettes_used)){
				$vedette_composee = new vedette_composee($id_vedette);
				$vedette_composee->delete();
			}
		}
	
		// traitement des categories
		$rqt_del = "DELETE FROM notices_categories WHERE notcateg_notice='$update_result' ";
		$res_del = pmb_mysql_query($rqt_del, $dbh);
		$rqt_ins = "INSERT INTO notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ";
		foreach ($f_categ as $key => $val) {
			$id_categ=$val['id'];
			if ($id_categ) {
				$ordre_categ = $val['ordre'];
				$rqt = $rqt_ins . " ('$update_result','$id_categ', $ordre_categ ) " ; 
				$res_ins = @pmb_mysql_query($rqt, $dbh);
			}
		}
		
		// Indexation concepts
		global $thesaurus_concepts_active;
		
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($update_result, TYPE_NOTICE);
			$index_concept->save();
		}
	
		// traitement des langues
		// langues
		$f_lang_form = array();
		$f_langorg_form = array() ;
		for ($i=0; $i< $max_lang ; $i++) {
			$var_langcode = "f_lang_code$i" ;
			if (${$var_langcode}) $f_lang_form[] =  array ('code' => ${$var_langcode},'ordre' => $i);
		}
	
		// langues originales
		for ($i=0; $i< $max_langorg ; $i++) {
			$var_langorgcode = "f_langorg_code$i" ;
			if (${$var_langorgcode}) $f_langorg_form[] =  array ('code' => ${$var_langorgcode},'ordre' => $i);
		}
	
		$rqt_del = "delete from notices_langues where num_notice='$update_result' ";
		$res_del = pmb_mysql_query($rqt_del, $dbh);
		$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
		foreach ($f_lang_form as $key => $val) {
			$tmpcode_langue=$val['code'];
			if ($tmpcode_langue) {
				$tmpordre_langue = $val['ordre'];
				$rqt = $rqt_ins . " ('$update_result',0, '$tmpcode_langue', $tmpordre_langue) " ; 
				$res_ins = pmb_mysql_query($rqt, $dbh);
			}
		}
		
		// traitement des langues originales
		$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
		foreach ($f_langorg_form as $key => $val) {
			$tmpcode_langue=$val['code'];
			if ($tmpcode_langue) {
				$tmpordre_langue = $val['ordre'];
				$rqt = $rqt_ins . " ('$update_result',1, '$tmpcode_langue', $tmpordre_langue) " ; 
				$res_ins = @pmb_mysql_query($rqt, $dbh);
			}
		}
		
		//Traitement des champs perso
		$p_perso->rec_fields_perso($update_result);
		indexation_stack::push($update_result, TYPE_NOTICE);
		
		if ($gestion_acces_active==1 && $myBulletinage->bull_num_notice) {
			
			//mise a jour des droits d'acces user_notice (idem notice mere perio)
			if ($gestion_acces_user_notice==1) {
				$q = "replace into acces_res_1 select $myBulletinage->bull_num_notice, res_prf_num, usr_prf_num, res_rights, res_mask from acces_res_1 where res_num=".$myBulletinage->bulletin_notice;
				pmb_mysql_query($q, $dbh);
			} 
	
			//mise a jour des droits d'acces empr_notice 
			if ($gestion_acces_empr_notice==1) {
				$dom_2 = $ac->setDomain(2);
				if ($bul_id) {	
					$dom_2->storeUserRights(1, $myBulletinage->bull_num_notice, $res_prf, $chk_rights, $prf_rad, $r_rad);
				} else {
					$dom_2->storeUserRights(0, $myBulletinage->bull_num_notice, $res_prf, $chk_rights, $prf_rad, $r_rad);
				}
			} 
		}
		
	}
	//Pour la synchro rdf
	if($pmb_synchro_rdf){
		$synchro_rdf->addRdf(0,$myBulletinage->bulletin_id);
	}
	if($result) {
		print "<div class='row'><div class='msg-perio'>".$msg["maj_encours"]."</div></div>";
		$retour = "./catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=$result";
		print "
			<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
				<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
			</form>
			<script type=\"text/javascript\">document.dummy.submit();</script>
			";
	} else {
		error_message($msg['catalog_serie_modif_bull'] , $msg['catalog_serie_modif_bull_imp'], 1, "./catalog.php?categ=serials&sub=view&serial_id=$serial_id");
	}

}