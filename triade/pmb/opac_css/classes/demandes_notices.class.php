<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_notices.class.php,v 1.9 2021/12/27 12:52:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class demandes_notices {
	
	public static function majNoticesTotal($notice){	
		$info=self::indexation_prepare($notice);
		self::majNotices($notice);
		self::majNoticesGlobalIndex($notice);
		self::majNoticesMotsGlobalIndex($notice);
		self::indexation_restaure($info);
	}
	
	public static function indexation_prepare($notice){
		global $lang,$include_path;
		global $pmb_indexation_lang;
		global $empty_word;
		global $indexation_lang;
			
		$info=array();
		$info['flag_lang_change']=0;
		if(!$notice) return;
		$query = pmb_mysql_query("SELECT indexation_lang FROM notices WHERE notice_id='".$notice."'");
		if(pmb_mysql_num_rows($query)) {
			$row = pmb_mysql_fetch_object($query);
			$indexation_lang=$row->indexation_lang;
			pmb_mysql_free_result($query);
	
			if($indexation_lang && $indexation_lang!= $lang){
				$info['save_pmb_indexation_lang']=$pmb_indexation_lang;
				$info['save_lang']=$lang;
				$info['flag_lang_change']=1;
					
				$pmb_indexation_lang=$indexation_lang;
				$lang=$indexation_lang;
				$empty_word=array();
				include("$include_path/marc_tables/".$lang."/empty_words");
			}else{
				//$indexation_lang=$lang;
			}
		}
	}
	
	public static function majNotices($notice){
		global $pmb_keyword_sep;
		if($notice){
			$query = pmb_mysql_query("SELECT notice_id,tparent_id,tit1,tit2,tit3,tit4,index_l, n_gen, n_contenu, n_resume, tnvol, indexation_lang FROM notices WHERE notice_id='".$notice."'");
			if(pmb_mysql_num_rows($query)) {
				$row = pmb_mysql_fetch_object($query);
				$ind_serie = '';
				$ind_wew = $ind_serie." ".$row->tnvol." ".$row->tit1." ".$row->tit2." ".$row->tit3." ".$row->tit4 ;
				$ind_sew = strip_empty_words($ind_wew) ;
				$row->index_l ? $ind_matieres = ' '.strip_empty_words(str_replace($pmb_keyword_sep," ",$row->index_l)).' ' : $ind_matieres = '';
				$row->n_gen ? $ind_n_gen = ' '.strip_empty_words($row->n_gen).' ' : $ind_n_gen = '';
				$row->n_contenu ? $ind_n_contenu = ' '.strip_empty_words($row->n_contenu).' ' : $ind_n_contenu = '';
				$row->n_resume ? $ind_n_resume = ' '.strip_empty_words($row->n_resume).' ' : $ind_n_resume = '';
					
					
				$req_update = "UPDATE notices";
				$req_update .= " SET index_wew='".addslashes($ind_wew)."'";
				$req_update .= ", index_sew=' ".addslashes($ind_sew)." '";
				$req_update .= ", index_serie='".addslashes($ind_serie)."'";
				$req_update .= ", index_n_gen='".addslashes($ind_n_gen)."'";
				$req_update .= ", index_n_contenu='".addslashes($ind_n_contenu)."'";
				$req_update .= ", index_n_resume='".addslashes($ind_n_resume)."'";
				$req_update .= ", index_matieres='".addslashes($ind_matieres)."'";
				$req_update .= " WHERE notice_id=$row->notice_id ";
				pmb_mysql_query($req_update);
				pmb_mysql_free_result($query);
			}
		}
	}
	
	public static function majNoticesGlobalIndex($notice, $NoIndex = 1) {
		pmb_mysql_query("delete from notices_global_index where num_notice = ".$notice." AND no_index = ".$NoIndex);
		$titres = pmb_mysql_query("select index_serie, tnvol, index_wew, index_sew, index_l, index_matieres, n_gen, n_contenu, n_resume, index_n_gen, index_n_contenu, index_n_resume, eformat, niveau_biblio from notices where notice_id = ".$notice);
		$mesNotices = pmb_mysql_fetch_assoc($titres);
		$tit = $mesNotices['index_wew'];
		$indTit = $mesNotices['index_sew'];
		$indMat = $mesNotices['index_matieres'];
		$indL = $mesNotices['index_l'];
		$indResume = $mesNotices['index_n_resume'];
		$indGen = $mesNotices['index_n_gen'];
		$indContenu = $mesNotices['index_n_contenu'];
		$resume = $mesNotices['n_resume'];
		$gen = $mesNotices['n_gen'];
		$contenu = $mesNotices['n_contenu'];
		$indSerie = $mesNotices['index_serie'];
		$tvol = $mesNotices['tnvol'];
		$eformatlien = $mesNotices['eformat'];
		$infos_global=' '.$tvol.' '.$tit.' '.$resume.' '.$gen.' '.$contenu.' '.$indL.' ';
		$infos_global_index=' '.$indSerie.' '.$indTit.' '.$indResume.' '.$indGen.' '.$indContenu.' '.$indMat.' ';
	
		pmb_mysql_free_result($titres);

		pmb_mysql_query("insert into notices_global_index SET num_notice=".$notice.",no_index =".$NoIndex.", infos_global='".addslashes($infos_global)."', index_infos_global='".addslashes($infos_global_index)."'" );
	}
	
	public static function majNoticesMotsGlobalIndex($notice, $datatype='all') {
		global $include_path;
		global $lang;
		global $indexation_lang;
			
		//Uniquement les champs nécessaires
		$tableau = array();
		$tableau["REFERENCE"][0]["value"]="notices";
		$tableau["REFERENCEKEY"][0]["value"]="notice_id";
		$tableau["FIELD"][0] = array(
			"NAME" => "237",
			"ID" => "001",
			"POND" => "130",
			"TABLE" => array(
				"0"=>array(
					"TABLEFIELD"=>array(
						"0"=>array(
							"value"=>"tit1"
						)
					)
				)
			)
		);
		$tableau["FIELD"][1] = array(
				"NAME" => "266",
				"ID" => "013",
				"POND" => "120",
				"TABLE" => array(
						"0"=>array(
								"TABLEFIELD"=>array(
										"0"=>array(
												"value"=>"n_contenu"
										)
								)
						)
				)
		);
			
		//analyse des donnees des tables
		$temp_not=array();
		$temp_not['t'][0][0]=$tableau['REFERENCE'][0]['value'] ;
		$temp_ext=array();
		$temp_marc=array();
		$champ_trouve=false;
		$tab_code_champ = array();
		$tab_languages=array();
		$tab_keep_empty = array();
		$tab_pp=array();
		$tab_authperso=array();
		$authperso_code_champ_start=0;
		$isbd_ask_list=array();
		for ($i=0;$i<count($tableau['FIELD']);$i++) { //pour chacun des champs decrits
	
			//recuperation de la liste des informations a mettre a jour
			if ( $datatype=='all' || ($datatype==$tableau['FIELD'][$i]['DATATYPE']) ) {
				//conservation des mots vides
				if($tableau['FIELD'][$i]['KEEPEMPTYWORD'] == "yes"){
					$tab_keep_empty[]=$tableau['FIELD'][$i]['ID'];
				}
				//champ perso
				if($tableau['FIELD'][$i]['DATATYPE'] == "custom_field"){
					$tab_pp[$tableau['FIELD'][$i]['ID']]=$tableau['FIELD'][$i]['TABLE'][0]['value'];
					//autorité perso
				}elseif($tableau['FIELD'][$i]['DATATYPE'] == "authperso"){
					$tab_authperso[$tableau['FIELD'][$i]['ID']]=$tableau['FIELD'][$i]['TABLE'][0]['value'];
					$authperso_code_champ_start=$tableau['FIELD'][$i]['ID'];
					$authpersos = new authperso_notice($notice);
				}else if ($tableau['FIELD'][$i]['EXTERNAL']=="yes") {
					//champ externe à la table notice
					//Stockage de la structure pour un accès plus facile
					$temp_ext[$tableau['FIELD'][$i]['ID']]=$tableau['FIELD'][$i];
				} else {
					//champ de la table notice
					$temp_not['f'][0][$tableau['FIELD'][$i]['ID']]= $tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value'];
					$tab_code_champ[0][$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']] = array(
							'champ' => $tableau['FIELD'][$i]['ID'],
							'ss_champ' => 0,
							'pond' => $tableau['FIELD'][$i]['POND'],
							'no_words' => ($tableau['FIELD'][$i]['DATATYPE'] == "marclist" ? true : false),
							'internal' => 1,
							'use_global_separator' => $tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['USE_GLOBAL_SEPARATOR']
					);
					if($tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE']){
						$tab_code_champ[0][$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']]['marctype']=$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
						$temp_not['f'][0][$tableau['FIELD'][$i]['ID']."_marc"]=$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']." as "."subst_for_marc_".$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
					}
				}
				if($tableau['FIELD'][$i]['ISBD']){ // isbd autorités
					$isbd_ask_list[$tableau['FIELD'][$i]['ID']]= array(
							'champ' => $tableau['FIELD'][$i]['ID'],
							'ss_champ' => $tableau['FIELD'][$i]['ISBD'][0]['ID'],
							'pond' => $tableau['FIELD'][$i]['ISBD'][0]['POND'],
							'class_name' => $tableau['FIELD'][$i]['ISBD'][0]['CLASS_NAME']
					);
				}
				$champ_trouve=true;
			}
		}
		if ($champ_trouve) {
	
			$tab_req=array();
	
			//Recherche des champs directs
			if($datatype=='all') {
				$tab_req[0]["rqt"]= "select ".implode(',',$temp_not['f'][0])." from ".$temp_not['t'][0][0];
				$tab_req[0]["rqt"].=" where ".$tableau['REFERENCEKEY'][0]['value']."='".$notice."'";
				$tab_req[0]["table"]=$temp_not['t'][0][0];
			}
			foreach($temp_ext as $k=>$v) {
				$isbd_tab_req=array();
				$no_word_field=false;
				//Construction de la requete
				//Champs pour le select
				$select=array();
				//on harmonise les fichiers XML décrivant des requetes...
				for ($i = 0; $i<count($v["TABLE"]); $i++) {
					$table = $v['TABLE'][$i];
					$select=array();
					if(count($table['TABLEFIELD'])){
						$use_word=true;
					}else{
						$use_word=false;
					}
					if($table['IDKEY'][0]){
						$select[]=$table['NAME'].".".$table['IDKEY'][0]['value']." as subst_for_autorite_".$table['IDKEY'][0]['value'];
					}
					for ($j=0;$j<count($table['TABLEFIELD']);$j++) {
						$select[]=($table['ALIAS'] ? $table['ALIAS']."." : "").$table['TABLEFIELD'][$j]["value"];
						if($table['LANGUAGE']){
							$select[]=$table['LANGUAGE'][0]['value'];
							$tab_languages[$k]=$table['LANGUAGE'][0]['value'];
						}
						$field_name = $table['TABLEFIELD'][$j]["value"];
						if(strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")!== false){//Pour le cas où l'on a besoin de nommer un champ et d'utiliser un alias
							$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")+4);
						}elseif(strpos($table['TABLEFIELD'][$j]["value"],".")!== false){
							$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos($table['TABLEFIELD'][$j]["value"],".")+1);
						}
						$field_name=trim($field_name);
						$tab_code_champ[$v['ID']][$field_name] = array(
								'champ' => $v['ID'],
								'ss_champ' => $table['TABLEFIELD'][$j]["ID"],
								'pond' => $table['TABLEFIELD'][$j]['POND'],
								'no_words' => ($v['DATATYPE'] == "marclist" ? true : false),
								'autorite' =>  $table['IDKEY'][0]['value']
						);
						if($table['TABLEFIELD'][$j]['MARCTYPE']){
							$tab_code_champ[$v['ID']][$table['TABLEFIELD'][$j]["value"]]['marctype']=$table['TABLEFIELD'][$j]['MARCTYPE'];
							$select[]=$table['NAME'].".".$table['TABLEFIELD'][$j]["value"]." as subst_for_marc_".$table['TABLEFIELD'][$j]['MARCTYPE'];
						}
					}
					$query="select ".implode(",",$select)." from notices";
					$jointure="";
					for( $j=0 ; $j<count($table['LINK']) ; $j++){
							
						$link = $table['LINK'][$j];
	
						if($link["TABLE"][0]['ALIAS']){
							$alias = $link["TABLE"][0]['ALIAS'];
						}else{
							$alias = $link["TABLE"][0]['value'];
						}
						switch ($link["TYPE"]) {
							case "n0" :
								if ($link["TABLEKEY"][0]['value']) {
									$jointure .= " LEFT JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
									if($link["EXTERNALTABLE"][0]['value']){
										$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
									}else{
										$jointure .= " ON " . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
									}
									$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
								} else {
									$jointure .= " LEFT JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
									$jointure .= " ON " . $tableau['REFERENCE'][0]['value'] . "." . $tableau['REFERENCEKEY'][0]['value'];
									$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
								}
								break;
							case "n1" :
								if ($link["TABLEKEY"][0]['value']) {
									$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
									if($link["EXTERNALTABLE"][0]['value']){
										$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
									}else{
										$jointure .= " ON " . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
									}
									$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
								} else {
									$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
									$jointure .= " ON " . $tableau['REFERENCE'][0]['value'] . "." . $tableau['REFERENCEKEY'][0]['value'];
									$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
								}
								break;
							case "1n" :
								$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
								$jointure .= " ON (" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'];
								$jointure .= "=" . $tableau['REFERENCE'][0]['value'] . "." . $link["REFERENCEFIELD"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value']. ") ";
									
									
								break;
							case "nn" :
								$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
								$jointure .= " ON (" . $tableau['REFERENCE'][0]['value'] . "." .  $tableau['REFERENCEKEY'][0]['value'];
								$jointure .= "=" . $alias . "." . $link["REFERENCEFIELD"][0]['value'] . ") ";
								if ($link["TABLEKEY"][0]['value']) {
									$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
									$jointure .= " ON (" . $alias . "." . $link["TABLEKEY"][0]['value'];
									$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'] ." ".$link["LINKRESTRICT"][0]['value']. ") ";
								} else {
									$jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS']? " as ".$table['ALIAS'] :"");
									$jointure .= " ON (" . $alias . "." . $link["EXTERNALFIELD"][0]['value'];
									$jointure .= "=" . ($table['ALIAS']? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value'].") ";
								}
								break;
						}
					}
					$where=" where ".$temp_not['t'][0][0].".".$tableau['REFERENCEKEY'][0]['value']."=".$notice;
					if($table['FILTER']){
						foreach ( $table['FILTER'] as $filter ) {
							if($tmp=trim($filter["value"])){
								$where.=" AND (".$tmp.")";
							}
						}
					}
					if($table['LANGUAGE']){
						$tab_req_lang[$k]= "select ".$table['LANGUAGE'][0]['value']." from ";
					}
					$query.=$jointure.$where;
					if($table['LANGUAGE']){
						$tab_req_lang[$k].=$jointure.$where;
					}
					if($use_word){
						$tab_req[$k]["new_rqt"]['rqt'][]=$query;
					}
					if($isbd_ask_list[$k]){ // isbd  => memo de la requete pour retrouver les id des autorités
						$id_aut=$table['NAME'].".".$table["TABLEKEY"][0]['value'];
						$req="select $id_aut as id_aut_for_isbd from notices".$jointure.$where;
						$isbd_tab_req[]=$req;
					}
	
				}
				if($use_word){
					$tab_req[$k]["rqt"] = implode(" union ",$tab_req[$k]["new_rqt"]['rqt']);
				}
				if($isbd_ask_list[$k]){ // isbd  => memo de la requete pour retrouver les id des autorités
					$req=implode(" union ",$isbd_tab_req);
					$isbd_ask_list[$k]['req']=  $req;
				}
			}
	
			//qu'est-ce qu'on efface?
			if($datatype=='all') {
				$req_del="delete from notices_mots_global_index where id_notice='".$notice."' ";
				pmb_mysql_query($req_del);
				//la table pour les recherche exacte
				$req_del="delete from notices_fields_global_index where id_notice='".$notice."' ";
				pmb_mysql_query($req_del);
			}else{
				foreach ( $tab_code_champ as $subfields ) {
					foreach($subfields as $subfield){
						$req_del="delete from notices_mots_global_index where id_notice='".$notice."' and code_champ='".$subfield['champ']."'";
						pmb_mysql_query($req_del);
						//la table pour les recherche exacte
						$req_del="delete from notices_fields_global_index where id_notice='".$notice."' and code_champ='".$subfield['champ']."'";
						pmb_mysql_query($req_del);
						break;
					}
				}
					
				//Les champs perso
				if(count($tab_pp)){
					foreach ( $tab_pp as $id ) {
						$req_del="delete from notices_mots_global_index where id_notice='".$notice."' and code_champ=100 and code_ss_champ='".$id."' ";
						pmb_mysql_query($req_del);
						//la table pour les recherche exacte
						$req_del="delete from notices_fields_global_index where id_notice='".$notice."' and code_champ=100 and code_ss_champ='".$id."' ";
						pmb_mysql_query($req_del);
					}
				}
				//Les autorités perso
				if(count($tab_authperso)){
					$authperso_fields=$authpersos->get_index_fields_to_delete();
					foreach ( $authperso_fields as $code_champ ) {
						$code_champ+=$authperso_code_champ_start;
						$req_del="delete from notices_mots_global_index where id_notice='".$notice."' and code_champ=$code_champ ";
						pmb_mysql_query($req_del);
						//la table pour les recherche exacte
						$req_del="delete from notices_fields_global_index where id_notice='".$notice."' and code_champ=$code_champ ";
						pmb_mysql_query($req_del);
					}
				}
			}
	
			//qu'est-ce qu'on met a jour ?
			$tab_insert=array();
			$tab_field_insert=array();
			foreach($tab_req as $k=>$v) {
				$r=pmb_mysql_query($v["rqt"]);
	
				$tab_mots=array();
				$tab_fields=array();
				if (pmb_mysql_num_rows($r)) {
					while(($tab_row=pmb_mysql_fetch_array($r,PMB_MYSQL_ASSOC))) {
						$langage="";
						if(isset($tab_row[$tab_languages[$k]])){
							$langage = $tab_row[$tab_languages[$k]];
							unset($tab_row[$tab_languages[$k]]);
						}
						foreach($tab_row as $nom_champ => $liste_mots) {
							if(substr($nom_champ,0,10)=='subst_for_'){
								continue;
							}
							if($tab_code_champ[$k][$nom_champ]['internal']){
								$langage=$indexation_lang;
							}
							if($tab_code_champ[$k][$nom_champ]['marctype']){
								//on veut toutes les langues, pas seulement celle de l'interface...
								$saved_lang = $lang;
								$code = $liste_mots;
								$dir = opendir($include_path."/marc_tables");
								while($dir_lang = readdir($dir)){
									if($dir_lang!= "." && $dir_lang!=".." && $dir_lang!="CVS" && $dir_lang!=".svn" && is_dir($include_path."/marc_tables/".$dir_lang)){
										$lang = $dir_lang;
										$marclist = new marc_list($tab_code_champ[$k][$nom_champ]['marctype']);
										$liste_mots = $marclist->table[$code];
										$tab_fields[$nom_champ][] = array(
												'value' => trim($liste_mots),
												'lang' => $lang,
												'autorite' => $tab_row["subst_for_marc_".$tab_code_champ[$k][$nom_champ]['marctype']]
										);
									}
								}
								$lang = $saved_lang;
								$liste_mots = "";
							}
							if($liste_mots!='') {
								$tab_tmp=array();
								$liste_mots = strip_tags($liste_mots);
								if(!in_array($k,$tab_keep_empty)){
									$tab_tmp=explode(' ',strip_empty_words($liste_mots));
								}else{
									$tab_tmp=explode(' ',strip_empty_chars(clean_string($liste_mots)));
								}
								//	if($lang!="") $tab_tmp[]=$lang;
								//la table pour les recherche exacte
								if(!$tab_fields[$nom_champ]) $tab_fields[$nom_champ]=array();
								if(!$tab_code_champ[$k][$nom_champ]['use_global_separator']){
									$tab_fields[$nom_champ][] = array(
											'value' =>trim($liste_mots),
											'lang' => $langage,
											'autorite' => $tab_row["subst_for_autorite_".$tab_code_champ[$k][$nom_champ]['autorite']]
									);
								} else {
									$var_global_sep = $tab_code_champ[$k][$nom_champ]['use_global_separator'];
									global ${$var_global_sep};
									$tab_liste_mots = explode(${$var_global_sep},$liste_mots);
									if(count($tab_liste_mots)){
										foreach ($tab_liste_mots as $mot) {
											$tab_fields[$nom_champ][] = array(
													'value' =>trim($mot),
													'lang' => $langage,
													'autorite' => $tab_row["subst_for_autorite_".$tab_code_champ[$k][$nom_champ]['autorite']]
											);
										}
									}
								}
								if(!$tab_code_champ[$k][$nom_champ]['no_words']){
									foreach($tab_tmp as $mot) {
										if(trim($mot)){
											$langageKey = $langage;
											if (!trim($langageKey)) {
												$langageKey = "empty";
											}
											$tab_mots[$nom_champ][$langageKey][]=$mot;
										}
									}
								}
							}
						}
					}
				}
					
				foreach ($tab_mots as $nom_champ=>$tab) {
					$memo_ss_champ="";
					$order_fields=1;
					$pos=1;
					foreach ( $tab as $langage => $mots ) {
						if ($langage == "empty") {
							$langage = "";
						}
						foreach ($mots as $mot) {
							//on cherche le mot dans la table de mot...
							$num_word = 0;
							$query = "select id_word from words where word = '".$mot."' and lang = '".$langage."'";
							$result = pmb_mysql_query($query);
							if(pmb_mysql_num_rows($result)){
								$num_word = pmb_mysql_result($result,0,0);
							}else{
								$dmeta = new DoubleMetaPhone($mot);
								$stemming = new stemming($mot);
								$element_to_update = "";
								if($dmeta->primary || $dmeta->secondary){
									$element_to_update.="
										double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
								}
								if($element_to_update) $element_to_update.=",";
								$element_to_update.="stem = '".$stemming->stem."'";
									
								$query = "insert into words set word = '".$mot."', lang = '".$langage."'".($element_to_update ? ", ".$element_to_update : "");
								pmb_mysql_query($query);
								$num_word = pmb_mysql_insert_id();
							}
								
							if($num_word != 0){
								$tab_insert[]="(".$notice.",".$tab_code_champ[$k][$nom_champ]['champ'].",".$tab_code_champ[$k][$nom_champ]['ss_champ'].",".$num_word.",".$tab_code_champ[$k][$nom_champ]['pond'].",$order_fields,$pos)";
								$pos++;
								if($tab_code_champ[$k][$nom_champ]['ss_champ']!= $memo_ss_champ) $order_fields++;
								$memo_ss_champ=$tab_code_champ[$k][$nom_champ]['ss_champ'];
							}
						}
					}
	
				}
				//la table pour les recherche exacte
				foreach ($tab_fields as $nom_champ=>$tab) {
					foreach($tab as $order => $values){
						//$tab_field_insert[]="(".$notice.",".$tab_code_champ[$v["table"]][$nom_champ][0].",".$tab_code_champ[$v["table"]][$nom_champ][1].",".$order.",'".addslashes($values['value'])."','".addslashes($values['lang'])."',".$tab_code_champ[$v["table"]][$nom_champ][2].")";
						$tab_field_insert[]="(".$notice.",".$tab_code_champ[$k][$nom_champ]['champ'].",".$tab_code_champ[$k][$nom_champ]['ss_champ'].",".($order+1).",'".addslashes($values['value'])."','".addslashes($values['lang'])."',".$tab_code_champ[$k][$nom_champ]['pond'].",'".addslashes($values['autorite'])."')";
					}
				}
			}
			//Les champs perso
			if(count($tab_pp)){
				foreach ( $tab_pp as $code_champ => $table ) {
					$p_perso=new parametres_perso($table);
					//on doit retrouver l'id des eléments...
					switch($table){
						case "expl" :
							$rqt = "select expl_id from notices join exemplaires on expl_notice = notice_id and expl_notice!=0 where notice_id = $notice union select expl_id from notices join bulletins on num_notice = notice_id join exemplaires on expl_bulletin = bulletin_id and expl_bulletin != 0 where notice_id = $notice";
							$res = pmb_mysql_query($rqt);
							if(pmb_mysql_num_rows($res)) {
								$ids = array();
								while($row= pmb_mysql_fetch_object($res)){
									$ids[] =$row->expl_id;
								}
							}
							break;
						case "collstate" :
							break;
						default :
							$ids = array($notice);
					}
					if(count($ids)){
						for($i=0 ; $i<count($ids) ; $i++) {
							$data=$p_perso->get_fields_recherche_mot_array($ids[$i]);
		      
							$j=0;
							$order_fields=1;
							foreach ( $data as $code_ss_champ => $value ) {
								$tab_mots=array();
								foreach($value as $val) {
									$tab_tmp=explode(' ',strip_empty_words($val));
									//la table pour les recherche exacte
									$tab_field_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$j.",'".addslashes(trim($val))."','',".$p_perso->get_pond($code_ss_champ).",0)";
									$j++;
									foreach($tab_tmp as $mot) {
										if(trim($mot)){
											$tab_mots[$mot]= "";
										}
									}
								}
								$pos=1;
								foreach ( $tab_mots as $mot => $langage ) {
									$num_word = 0;
									//on cherche le mot dans la table de mot...
									$query = "select id_word from words where word = '".$mot."' and lang = '".$langage."'";
									$result = pmb_mysql_query($query);
									if(pmb_mysql_num_rows($result)){
										$num_word = pmb_mysql_result($result,0,0);
									}else{
										$dmeta = new DoubleMetaPhone($mot);
										$stemming = new stemming($mot);
										$element_to_update = "";
										if($dmeta->primary || $dmeta->secondary){
											$element_to_update.="
												double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
										}
										if($element_to_update) $element_to_update.=",";
										$element_to_update.="stem = '".$stemming->stem."'";
											
										$query = "insert into words set word = '".$mot."', lang = '".$langage."'".($element_to_update ? ", ".$element_to_update : "");
										pmb_mysql_query($query);
										$num_word = pmb_mysql_insert_id();
									}
									if($num_word != 0){
										$tab_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$num_word.",".$p_perso->get_pond($code_ss_champ).",$order_fields,$pos)";
										$pos++;
									}
								}
								$order_fields++;
							}
						}
					}
				}
			}
			//Les autorités perso
			if(count($tab_authperso)){
				$order_fields=1;
				$index_fields=$authpersos->get_index_fields($notice);
				foreach ( $index_fields as $code_champ => $auth ) {
					$code_champ+=$authperso_code_champ_start;
					$tab_mots=array();
					foreach ($auth['ss_champ'] as $ss_field){
						foreach ($ss_field as $code_ss_champ =>$val){
							$tab_field_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$j.",'".addslashes(trim($val))."','',".$auth['pond'].",0)";
	
							$tab_tmp=explode(' ',strip_empty_words($val));
							foreach($tab_tmp as $mot) {
								if(trim($mot)){
									$tab_mots[$mot]= "";
								}
							}
							$pos=1;
							foreach ( $tab_mots as $mot => $langage ) {
								$num_word = 0;
								//on cherche le mot dans la table de mot...
								$query = "select id_word from words where word = '".$mot."' and lang = '".$langage."'";
								$result = pmb_mysql_query($query);
								if(pmb_mysql_num_rows($result)){
									$num_word = pmb_mysql_result($result,0,0);
								}else{
									$dmeta = new DoubleMetaPhone($mot);
									$stemming = new stemming($mot);
									$element_to_update = "";
									if($dmeta->primary || $dmeta->secondary){
										$element_to_update.="
											double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
									}
									if($element_to_update) $element_to_update.=",";
									$element_to_update.="stem = '".$stemming->stem."'";
	
									$query = "insert into words set word = '".$mot."', lang = '".$langage."'".($element_to_update ? ", ".$element_to_update : "");
									pmb_mysql_query($query);
									$num_word = pmb_mysql_insert_id();
								}
								if($num_word != 0){
									$tab_insert[]="(".$notice.",".$code_champ.",".$code_ss_champ.",".$num_word.",".$auth['pond'].",$order_fields,$pos)";
									$pos++;
								}
							}
							$order_fields++;
						}
					}
				}
			}
	
			if(count($isbd_ask_list)){
				// Les isbd d'autorités
				foreach($isbd_ask_list as $infos){
					$isbd_s=array(); // cumul des isbd
		
					$res = pmb_mysql_query($infos["req"]) or die($infos["req"]);
					if(pmb_mysql_num_rows($res)) {
		
						switch ($infos["class_name"]){
							case 'author':
								while($row= pmb_mysql_fetch_object($res)){
									$aut=new auteur($row->id_aut_for_isbd);
									$isbd_s[]=$aut->get_isbd();
								}
								break;
							case 'editeur':
								while($row= pmb_mysql_fetch_object($res)){
									$aut=new publisher($row->id_aut_for_isbd);
									$isbd_s[]=$aut->get_isbd();
								}
								break;
							case 'indexint':
								while($row= pmb_mysql_fetch_object($res)){
									$aut=new indexint($row->id_aut_for_isbd);
									$isbd_s[]=$aut->get_isbd();
								}
								break;
							case 'collection':
								while($row= pmb_mysql_fetch_object($res)){
									$aut=new collection($row->id_aut_for_isbd);
									$isbd_s[]=$aut->get_isbd();
								}
								break;
							case 'subcollection':
								while($row= pmb_mysql_fetch_object($res)){
									$aut=new subcollection($row->id_aut_for_isbd);
									$isbd_s[]=$aut->get_isbd();
								}
								break;
							case 'serie':
								while($row= pmb_mysql_fetch_object($res)){
									$aut=new serie($row->id_aut_for_isbd);
									$isbd_s[]=$aut->get_isbd();
								}
								break;
							case 'categories':
								while($row= pmb_mysql_fetch_object($res)){
									$aut=new categories($row->id_aut_for_isbd,$lang);
									$isbd_s[]=$aut->libelle_categorie;
								}
								break;
							case 'titre_uniforme':
								while($row= pmb_mysql_fetch_object($res)){
									$aut=new titre_uniforme($row->id_aut_for_isbd);
										$isbd_s[]=$aut->get_isbd();
									}
								break;
						}
					}
					$order_fields=1;
					for($i=0 ; $i<count($isbd_s) ; $i++) {
						$tab_mots=array();
						$tab_field_insert[]="(".$notice.",".$infos["champ"].",".$infos["ss_champ"].",".$order_fields.",'".addslashes(trim($isbd_s[$i]))."','',".$infos["pond"].",0)";
							
						$tab_tmp=explode(' ',strip_empty_words($isbd_s[$i]));
						foreach($tab_tmp as $mot) {
							if(trim($mot)){
								$tab_mots[$mot]= "";
							}
						}
						$pos=1;
						foreach ( $tab_mots as $mot => $langage ) {
							$num_word=0;
							//on cherche le mot dans la table de mot...
							$query = "select id_word from words where word = '".$mot."' and lang = '".$langage."'";
							$result = pmb_mysql_query($query);
							if(pmb_mysql_num_rows($result)){
								$num_word = pmb_mysql_result($result,0,0);
							}else{
								$dmeta = new DoubleMetaPhone($mot);
								$stemming = new stemming($mot);
								$element_to_update = "";
								if($dmeta->primary || $dmeta->secondary){
										$element_to_update.="
										double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
								}
								if($element_to_update) $element_to_update.=",";
								$element_to_update.="stem = '".$stemming->stem."'";
									
								$query = "insert into words set word = '".$mot."', lang = '".$langage."'".($element_to_update ? ", ".$element_to_update : "");
								pmb_mysql_query($query);
								$num_word = pmb_mysql_insert_id();
							}
							if($num_word != 0){
								$tab_insert[]="(".$notice.",".$infos["champ"].",".$infos["ss_champ"].",".$num_word.",".$infos["pond"].",$order_fields,$pos)";
								$pos++;
							}
						}
						$order_fields++;
					}
				}
			}
	
			if(count($tab_insert)){
				$req_insert="insert ignore into notices_mots_global_index(id_notice,code_champ,code_ss_champ,num_word,pond,position, field_position) values ".implode(',',$tab_insert);
				pmb_mysql_query($req_insert);
			}
			if(count($tab_field_insert)){
				//la table pour les recherche exacte
				$req_insert="insert ignore into notices_fields_global_index(id_notice,code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert);
				pmb_mysql_query($req_insert);
			}
	
		}
	}
		
	public static function indexation_restaure($info){
		global $lang;
		global $pmb_indexation_lang;
		global $empty_word;
		global $include_path;
			
		if($info['flag_lang_change']){
			// restauration de l'environemment
			$pmb_indexation_lang=$info['save_pmb_indexation_lang'];
			$lang=$info['save_lang'];
			$empty_word=array();
			include("$include_path/marc_tables/$lang/empty_words");
		}
		$pmb_indexation_lang="";
		$flag_lang_change=0;
	}	
	
}