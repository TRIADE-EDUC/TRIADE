<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesEmpr.class.php,v 1.23 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($class_path."/emprunteur.class.php");
require_once($class_path."/parametres_perso.class.php");

class pmbesEmpr extends external_services_api_class {
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}
	
	public function empr_list($filters=array()) {
		global $dbh;
		global $msg;
		global $charset;
		$sql_filters="";
		object_to_array($filters);
		if(is_array($filters)){
			$i=0;
			foreach($filters as $filter){
				if(!$filter['field']) continue;				
				
				if($i==0) $sql_filters=" where ";
				else {
					if($filter['separator'])
						$sql_filters.=" ".$filter['separator']." ";
					else
						$sql_filters.=" and ";
				}
				$sql_filters.= $filter['field']." ".$filter['operator']." '".$filter['value']."' ";
				$i++;
			}
		}
		$infos= array();
		$sql = "SELECT id_empr, empr_cb FROM empr $sql_filters";
		$res = pmb_mysql_query($sql);
		$i=0;
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]['empr_cb']=$res_info->empr_cb;	
			$infos[$i]['empr_id']=$res_info->id_empr;
			$i++;		
		}
		return $this->build_ok($infos);
	}
	
	public function fetch_empr($empr_cb='', $empr_id='') {
		global $dbh;
		global $msg;		
		global $charset;
		
		$result = array(
				'empr_id' => 0,
				'empr_cb' => "",
				'nom' => "",
				'prenom' => "",
				'sexe' => 0,
				'birth' => "",
				'adr1' => "",
				'adr2' => "",
				'cp' => "",
				'ville' => "",
				'pays' => "",
				'mail' => "",
				'tel1' => "",
				'sms' => 0,
				'tel2' => "",
				'prof' => "",
				'categ' => "",
				'cat_l' => "",
				'cstat' => "",
				'cstat_l' => "",
				'cdate' => "",
				'mdate' => "",
				'login' => "",
				'pwd' => "",
				'type_abt' => 0,
				'location' => 0,
				'location_l' => "",
				'date_blocage' => "",
				'statut' => 0,
				'statut_libelle' => "",
				'total_loans' => 0,
				'allow_loan' => 0,
				'allow_book' => 0,
				'allow_opac' => 0,
				'allow_dsi' => 0,
				'allow_dsi_priv' => 0,
				'allow_sugg' => 0,
				'allow_prol' => 0,
				'date_adhesion' => "",
				'date_expiration' => "",
				'last_loan_date' => "",
				'nb_pret' => 0,
				'msg' => "",
				'ldap' => 0,
				'pperso_list' => array(),
				'groupe_list' => array(),
				'prets' => array(),
				'reservations' => array(),
				'nb_retard' => 0,
				'nb_resa'=> 0,
				'nb_previsions'=> 0,
		);
		
		$empr_cb=$this->clean_field($empr_cb);
		$empr_id += 0;
		if (!$empr_id && $empr_cb=='') return $this->build_ok($result,"idempr et empr_cb vide.",false);
		
		if($empr_id)	$where=" id_empr = $empr_id ";
		else $where=" empr_cb = '".addslashes($empr_cb)."' ";
		
		$sql = "SELECT id_empr, empr_cb FROM empr WHERE $where";
		$res = pmb_mysql_query($sql);
		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_ok($result,$msg['54'].".",false);
			
		$empr_res = pmb_mysql_fetch_object($res);
		$empr_id=$empr_res->id_empr;
		
		$empr= new emprunteur($empr_id,'',false,1);
		if (!$empr->cb) return  $this->build_ok($result,$msg['54'].".",false);
		$sql = "select id_groupe, libelle_groupe from groupe, empr_groupe where empr_id='".$empr_id."' and id_groupe=groupe_id order by libelle_groupe";
		$res = pmb_mysql_query($sql);
		$i=0;
		$groupes_infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$groupes_infos[$i]['id']=$res_info->id_groupe;
			$groupes_infos[$i]['libelle']=$res_info->libelle_groupe;	
			$i++;		
		}
		
		$requete_nb_pret = "select count(1) as nb_pret from pret where pret_idempr=".$empr_id;
		$result_nb_pret = pmb_mysql_query($requete_nb_pret, $dbh);
		$r_nb_pret = pmb_mysql_fetch_object($result_nb_pret);
		$nb_pret = $r_nb_pret->nb_pret ;
		
		$resa_list=array();
		$sql="SELECT resa_idnotice, resa_idbulletin, resa_date, resa_date_debut, resa_date_fin, resa_cb, resa_confirmee, resa_idempr, ifnull(expl_cote,'') as expl_cote, empr_nom, empr_prenom, empr_cb, location_libelle, resa_loc_retrait, ";
		$sql.=" trim(concat(if(series_m.serie_name <>'', if(notices_m.tnvol <>'', concat(series_m.serie_name,', ',notices_m.tnvol,'. '), concat(series_m.serie_name,'. ')), if(notices_m.tnvol <>'', concat(notices_m.tnvol,'. '),'')), ";
		$sql.=" if(series_s.serie_name <>'', if(notices_s.tnvol <>'', concat(series_s.serie_name,', ',notices_s.tnvol,'. '), series_s.serie_name), if(notices_s.tnvol <>'', concat(notices_s.tnvol,'. '),'')), ";
		$sql.="	ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, id_resa, ";
		$sql.=" ifnull(notices_m.typdoc,notices_s.typdoc) as typdoc, ";
		$sql.=" IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, if(resa_date_fin='0000-00-00', '', date_format(resa_date_fin, '".$msg["format_date"]."')) as aff_resa_date_fin, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date " ;
		$sql.=" FROM ((((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ";
		$sql.=" LEFT JOIN series AS series_m ON notices_m.tparent_id = series_m.serie_id ) ";
		$sql.=" LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) ";
		$sql.=" LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id ";
		$sql.=" LEFT JOIN series AS series_s ON notices_s.tparent_id = series_s.serie_id ) ";
		$sql.=" LEFT JOIN exemplaires ON resa_cb = exemplaires.expl_cb), ";
		$sql.=" empr, docs_location ";
		$sql.=" WHERE resa_idempr = id_empr AND idlocation = empr_location  AND id_empr='$empr_id'";
		
		$res = pmb_mysql_query($sql);
		while( $res_info=pmb_mysql_fetch_object($res)){
			$resa["title"]=$res_info->tit;
			$resa["typdoc"]=$res_info->typdoc;
			$resa["date"]=$res_info->aff_resa_date;
			$resa["date_debut"]=$res_info->aff_resa_date_debut;
			$resa["date_fin"]=$res_info->aff_resa_date_fin;
			$resa["cb"]=$res_info->resa_cb;
			$resa["confirmee"]=$res_info->resa_confirmee;
			$resa["perimee"]=$res_info->perimee;
			$resa["id"]=$res_info->id_resa;
			$resa_list[]=$resa;
		}
		
		$p_perso = new parametres_perso("empr");
		$perso_ = $p_perso->show_fields($empr_id);	
		$pperso_list=array();
		if (count($perso_)){
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];				
				$pperso_list[$i]["id"]=$p["ID"];
				$pperso_list[$i]["name"]=$p["NAME"];
				$pperso_list[$i]["libelle"]=$p["TITRE"];
				$pperso_list[$i]["aff"]=$p["AFF"];
			}				
		}
		$result = array(
			'empr_id' => $empr_id,
			'empr_cb' => $empr->cb,
			'nom' => $empr->nom,
			'prenom' => $empr->prenom,
			'sexe' => $empr->sexe,
			'birth' => $empr->birth,
			'adr1' => $empr->adr1,
			'adr2' => $empr->adr2,
			'cp' => $empr->cp,
			'ville' => $empr->ville,
			'pays' => $empr->pays,
			'mail' => $empr->mail,
			'tel1' => $empr->tel1,
			'sms' => $empr->sms,
			'tel2' => $empr->tel2,
			'prof' => $empr->prof,
			'categ' => $empr->categ,
			'cat_l' => $empr->cat_l,
			'cstat' => $empr->cstat,
			'cstat_l' => $empr->cstat_l,
			'cdate' => $empr->cdate,
			'mdate' => $empr->mdate,
			'login' => $empr->login,
			'pwd' => $empr->pwd,
			'type_abt' => $empr->type_abt,
			'location' => $empr->empr_location,
			'location_l' => $empr->empr_location_l,
			'date_blocage' => $empr->date_blocage,
			'statut' => $empr->empr_statut,
			'statut_libelle' => $empr->empr_statut_libelle,
			'total_loans' => $empr->total_loans,
			'allow_loan' => $empr->allow_loan,
			'allow_book' => $empr->allow_book,
			'allow_opac' => $empr->allow_opac,
			'allow_dsi' => $empr->allow_dsi,
			'allow_dsi_priv' => $empr->allow_dsi_priv,
			'allow_sugg' => $empr->allow_sugg,
			'allow_prol' => $empr->allow_prol,
			'date_adhesion' => $empr->date_adhesion,
			'date_expiration' => $empr->date_expiration,
			'last_loan_date' => $empr->last_loan_date,
			'nb_pret' => $nb_pret,
			'msg' => $empr->empr_msg,
			'ldap' => $empr->empr_ldap,
			'pperso_list' => $pperso_list,
			'groupe_list' => $groupes_infos,
			'prets' => $empr->prets,
			'reservations' => $resa_list,
			'nb_retard' => $empr->retard,
			'nb_resa'=> $empr->nb_reservations,
			'nb_previsions'=> $empr->nb_previsions,
		);
		
		return $this->build_ok($result);
	}
	
	public function delete_empr($empr_cb='', $empr_id='') {
		global $dbh;
		global $msg;		
		global $charset;
		
		$empr_cb=$this->clean_field($empr_cb);
		$empr_id += 0;
		if (!$empr_id && $empr_cb=='') return $this->build_error( "idempr et empr_cb vide.");
		
		if($empr_id)	$where=" id_empr = $empr_id ";
		else $where=" empr_cb = '".addslashes($empr_cb)."' ";
		
		$sql = "SELECT id_empr, empr_cb FROM empr WHERE $where";
		$res = pmb_mysql_query($sql);
		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_error( $msg['54'].".");
			
		$empr_res = pmb_mysql_fetch_object($res);
		$empr_id=$empr_res->id_empr;
		
		$status= emprunteur::del_empr($empr_id);
		if($status==false) return $this->build_error( "Ce lecteur a des prets en cours.");
		return $this->build_ok();
	}
	
	public function create_empr($empr_cb='',$fields) {
		global $dbh,$lang;
		global $msg;		
		global $charset;
		global $pmb_num_carte_auto,$deflt2docs_location,$pmb_gestion_abonnement,$pmb_gestion_financiere;
		
		object_to_array($fields);
		
		$result = array(
				'empr_id' => 0,
				'empr_cb' => "0"
		);
		
		$empr_cb=$this->clean_field((string)$empr_cb);				
		if(!$empr_cb && $pmb_num_carte_auto) $empr_cb = emprunteur::gen_num_carte_auto();
		if (!$empr_cb)  return $this->build_ok($result,"Un code barre est obligatoire.",false);	
							
		$sql = "SELECT id_empr, empr_cb FROM empr WHERE  empr_cb = '".addslashes($empr_cb)."' ";
		$res = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($res)) return $this->build_ok($result,"Le code $empr_cb est deja utilise.",false);
			
		// clean des entrées
		$fields=$this->clean_fields($fields);
		/*
		$fields['nom']=$this->clean_field($fields['nom']);
		$fields['prenom']=$this->clean_field($fields['prenom']);
		$fields['adr1']=$this->clean_field($fields['adr1']);
		$fields['adr2']=$this->clean_field($fields['adr2']);
		$fields['cp']=$this->clean_field($fields['cp']);
		$fields['ville']=$this->clean_field($fields['ville']);
		$fields['pays']=$this->clean_field($fields['pays']);
		$fields['mail']=$this->clean_field($fields['mail']);
		$fields['tel1']=$this->clean_field($fields['tel1']);
		$fields['sms']=(int) $this->clean_field($fields['sms']);
		$fields['tel2']=$this->clean_field($fields['tel2']);
		$fields['prof']=$this->clean_field($fields['prof']);
		$fields['birth']=$this->clean_field($fields['birth']);
		$fields['sexe']=(int) $this->clean_field($fields['sexe']);
		$fields['login']=$this->clean_field($fields['login']);
		$fields['pwd']=$this->clean_field($fields['pwd']);
		$fields['msg']=$this->clean_field($fields['msg']);
		$fields['lang']=$this->clean_field($fields['lang']);
		$fields['location']=(int) $this->clean_field($fields['location']);
		$fields['date_adhesion']=$this->clean_field($fields['date_adhesion']);
		$fields['date_expiration']=$this->clean_field($fields['date_expiration']);
		$fields['categ']=(int) $this->clean_field($fields['categ']);
		$fields['statut']=(int) $this->clean_field($fields['statut']);
		$fields['lang']=$this->clean_field($fields['lang']);
		$fields['cstat']=(int) $this->clean_field($fields['cstat']);
		$fields['type_abt']=(int) $this->clean_field($fields['type_abt']);
		$fields['ldap']=(int) $this->clean_field($fields['ldap']);*/
		$fields['sexe']+=0;
		$fields['location']+=0;
		$fields['categ']+=0;
		$fields['statut']+=0;
		$fields['lang']+=0;
		$fields['cstat']+=0;
		$fields['type_abt']+=0;
		$fields['ldap']+=0;
		
		// vérification des champs obligatoie, et des Id...		
		if(!$fields['nom']) return $this->build_ok($result,"Le champ 'nom' n'est pas renseigne.",false);
		if(!$fields['categ']) return $this->build_ok($result,"Le champ 'categ' n'est pas renseigne.",false);
		if(!$fields['statut']) return $this->build_ok($result,"Le champ 'statut' n'est pas renseigne.",false);
		if(!$fields['cstat']) return $this->build_ok($result,"Le champ 'cstat' n'est pas renseigne.",false);
		
		$q="select idstatut from empr_statut where idstatut='".$fields['statut']."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_num_rows($r)) return $this->build_ok($result,"Le champ 'statut' = ".$fields['statut']." n'est pas un Id present dans la base de donnee.",false);
		
		$q="select idcode from empr_codestat where idcode='".$fields['cstat']."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_num_rows($r)) return $this->build_ok($result,"Le champ 'cstat' = ".$fields['cstat']." n'est pas un Id present dans la base de donnee.",false);
		
		$q="select id_categ_empr from empr_categ where id_categ_empr='".$fields['categ']."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_num_rows($r)) return $this->build_ok($result,"Le champ 'categ' = ".$fields['categ']." n'est pas un Id present dans la base de donnee.",false);
		
		if($fields['location']){			
			$q="select idlocation from docs_location where idlocation='".$fields['location']."' limit 1";
			$r = pmb_mysql_query($q, $dbh);
			if (!pmb_mysql_num_rows($r)) $fields['location']=0;
		}
		if (!$fields['location']) {
			$loca = pmb_mysql_query("select min(idlocation) as idlocation from docs_location", $dbh);
			$locaid = pmb_mysql_fetch_object($loca);
			$fields['location'] = $locaid->idlocation;
		}
		if($fields['mail'])if(!filter_var($fields['mail'], FILTER_VALIDATE_EMAIL)) return $this->build_ok($result,"Le champ 'mail' = ".$fields['mail']." n'est pas un mail valide.",false);
		
		if(!$fields['sexe']) $fields['sexe']=0;
		if(!$fields['lang']) $fields['lang']=$lang;		
		
		$requete = "INSERT INTO empr SET ";
		$requete .= "empr_cb='".addslashes($empr_cb)."', ";
		$requete .= "empr_nom='".addslashes($fields['nom'])."', ";
		$requete .= "empr_prenom='".addslashes($fields['prenom'])."', ";
		$requete .= "empr_adr1='".addslashes($fields['adr1'])."', ";
		$requete .= "empr_adr2='".addslashes($fields['adr2'])."', ";
		$requete .= "empr_cp='".addslashes($fields['cp'])."', ";
		$requete .= "empr_ville='".addslashes($fields['ville'])."', ";
		$requete .= "empr_pays='".addslashes($fields['pays'])."', ";
		$requete .= "empr_mail='".addslashes($fields['mail'])."', ";
		$requete .= "empr_tel1='".addslashes($fields['tel1'])."', ";
		$requete .= "empr_sms='".addslashes($fields['sms'])."', ";
		$requete .= "empr_tel2='".addslashes($fields['tel2'])."', ";
		$requete .= "empr_prof='".addslashes($fields['prof'])."', ";
		$requete .= "empr_year='".addslashes($fields['birth'])."', ";
		$requete .= "empr_categ='".$fields['categ']."', ";
		$requete .= "empr_statut='".$fields['statut']."', ";
		$requete .= "empr_lang='".addslashes($fields['lang'])."', ";
			
		if ($fields['date_adhesion']=="") $requete .= "empr_date_adhesion=CURRENT_DATE(), "; else $requete .= "empr_date_adhesion='".addslashes($fields['date_adhesion'])."', ";
		if (($fields['date_expiration']=="") or ($fields['date_expiration']==$fields['date_adhesion'])) {
			/* AJOUTER ICI LE CALCUL EN FONCTION DE LA CATEGORIE */
			$rqt_empr_categ = "select duree_adhesion from empr_categ where id_categ_empr = ".$fields['categ']." ";
			$res_empr_categ = pmb_mysql_query($rqt_empr_categ, $dbh);
			$empr_categ = pmb_mysql_fetch_object($res_empr_categ);
			
			if($fields['date_adhesion'])	$rqt_date = "select date_add('".addslashes($fields['date_adhesion'])."', INTERVAL ".$empr_categ->duree_adhesion." DAY) as date_expiration " ;
			else $rqt_date = "select date_add(CURRENT_DATE(), INTERVAL ".$empr_categ->duree_adhesion." DAY) as date_expiration " ;
			$resultatdate=pmb_mysql_query($rqt_date);
			$resdate=pmb_mysql_fetch_object($resultatdate);
			$requete .= "empr_date_expiration='".$resdate->date_expiration."', ";
		
		} else $requete .= "empr_date_expiration='".$fields['date_expiration']."', ";
		$requete .= "empr_codestat=".$fields['cstat'].", ";
		$requete .= "empr_creation=CURRENT_TIMESTAMP(), ";
		$requete .= "empr_modif=CURRENT_DATE(), ";
		$requete .= "empr_sexe='".$fields['sexe']."', ";
		$requete .= "empr_msg='".addslashes($fields['msg'])."', ";
		$requete .= "empr_login='".addslashes($fields['login'])."', ";
		$requete .= "empr_location='".$fields['location']."', ";
		
		// ldap - MaxMan
		if ($fields['ldap']){
			$requete .= "empr_ldap='1', ";
			$fields['pwd']="";
		}else{
			$requete .= "empr_ldap='0', ";
		}
		
		//Gestion financière
		if (($pmb_gestion_abonnement==2)&&($pmb_gestion_financiere)) {
			$requete.="type_abt='".$fields['type_abt']."', ";
		} else {
			$requete.="type_abt=0, ";
		}
		
		if ($fields['pwd']!="") $requete .= "empr_password='".addslashes($fields['pwd'])."' ";
		else $requete .= "empr_password='".addslashes($fields['birth'])."' ";
		
		$res = pmb_mysql_query($requete, $dbh);
		if(!$res)return $this->build_ok($result,"Impossible de creer le lecteur: $requete",false);				
		
		// on récupère l'id du de l'emprunteur
		$empr_id = pmb_mysql_insert_id($dbh);
		
		if(is_array($fields['pperso_list'])){
			if(count($fields['pperso_list'])){
				$p_perso = new parametres_perso("empr");
				foreach($fields['pperso_list'] as $pp){
					$name=$pp["name"];
					global ${$name};
					${$name}=$pp["value_list"];	
					
				}
				$p_perso->rec_fields_perso($empr_id);
			}
		}
		if(is_array($fields['groupe_list'])) emprunteur::rec_groupe_empr($empr_id, $fields['groupe_list']) ;
		emprunteur::ins_lect_categ_dsi($empr_id, $fields['categ'], 0) ;
		if (($pmb_gestion_financiere)&&($pmb_gestion_abonnement))	emprunteur::rec_abonnement($empr_id,$type_abt,$fields['categ']);	
		
		$result = array(
			'empr_id' => $empr_id,
			'empr_cb' => $empr_cb
		);
		
		return $this->build_ok($result);			
	}	
	
	public function update_empr($empr_cb='', $empr_id=0, $fields) {
		global $dbh,$lang;
		global $msg;
		global $charset;
		global $pmb_num_carte_auto,$deflt2docs_location,$pmb_gestion_abonnement,$pmb_gestion_financiere;
	
		object_to_array($fields);
		
		$empr_cb=$this->clean_field((string)$empr_cb);
		$empr_id += 0;
		if (!$empr_id && $empr_cb=='') return $this->build_error( "idempr et empr_cb vide.");
		
		if($empr_id)	$where=" id_empr = $empr_id ";
		else $where=" empr_cb = '".addslashes($empr_cb)."' ";
		
		$sql = "SELECT id_empr, empr_cb FROM empr WHERE $where";
		$res = pmb_mysql_query($sql);
		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_error( $msg['54'].": 'empr_cb' = $empr_cb ou id_empr = $empr_id .");
			
		$empr_res = pmb_mysql_fetch_object($res);
		$empr_id=$empr_res->id_empr;
			
		// clean des entrées
		$fields=$this->clean_fields($fields);/*
		$fields['nom']=$this->clean_field($fields['nom']);
		$fields['prenom']=$this->clean_field($fields['prenom']);
		$fields['adr1']=$this->clean_field($fields['adr1']);
		$fields['adr2']=$this->clean_field($fields['adr2']);
		$fields['cp']=$this->clean_field($fields['cp']);
		$fields['ville']=$this->clean_field($fields['ville']);
		$fields['pays']=$this->clean_field($fields['pays']);
		$fields['mail']=$this->clean_field($fields['mail']);
		$fields['tel1']=$this->clean_field($fields['tel1']);
		$fields['sms']=(int) $this->clean_field($fields['sms']);
		$fields['tel2']=$this->clean_field($fields['tel2']);
		$fields['prof']=$this->clean_field($fields['prof']);
		$fields['birth']=$this->clean_field($fields['birth']);
		$fields['sexe']=(int) $this->clean_field($fields['sexe']);
		$fields['login']=$this->clean_field($fields['login']);
		$fields['pwd']=$this->clean_field($fields['pwd']);
		$fields['msg']=$this->clean_field($fields['msg']);
		$fields['lang']=$this->clean_field($fields['lang']);
		$fields['location']=(int) $this->clean_field($fields['location']);
		$fields['date_adhesion']=$this->clean_field($fields['date_adhesion']);
		$fields['date_expiration']=$this->clean_field($fields['date_expiration']);
		$fields['categ']=(int) $this->clean_field($fields['categ']);
		$fields['statut']=(int) $this->clean_field($fields['statut']);
		$fields['lang']=$this->clean_field($fields['lang']);
		$fields['cstat']=(int) $this->clean_field($fields['cstat']);
		$fields['type_abt']=(int) $this->clean_field($fields['type_abt']);
		$fields['ldap']=(int) $this->clean_field($fields['ldap']);*/
		$fields['sexe']+=0;
		$fields['location']+=0;
		$fields['categ']+=0;
		$fields['statut']+=0;
		$fields['lang']+=0;
		$fields['cstat']+=0;
		$fields['type_abt']+=0;
		$fields['ldap']+=0;
		
	
		// vérification des champs obligatoires
		if(!$fields['nom']) return $this->build_error( "Le champ 'nom' n'est pas renseigne.");
		if(!$fields['categ']) return $this->build_error( "Le champ 'categ' n'est pas renseigne.");
		if(!$fields['statut']) return $this->build_error( "Le champ 'statut' n'est pas renseigne.");
		if(!$fields['cstat']) return $this->build_error( "Le champ 'cstat' n'est pas renseigne.");
	
		// vérification des relations
		$q="select idstatut from empr_statut where idstatut='".$fields['statut']."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_num_rows($r)) return $this->build_error( "Le champ 'statut' = ".$fields['statut']." n'est pas un Id present dans la base de donnee.");
	
		$q="select idcode from empr_codestat where idcode='".$fields['cstat']."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_num_rows($r)) return $this->build_error( "Le champ 'cstat' = ".$fields['cstat']." n'est pas un Id present dans la base de donnee.");
	
		$q="select id_categ_empr from empr_categ where id_categ_empr='".$fields['categ']."' limit 1";
		$r = pmb_mysql_query($q, $dbh);
		if (!pmb_mysql_num_rows($r)) return $this->build_error( "Le champ 'categ' = ".$fields['categ']." n'est pas un Id present dans la base de donnee.");
	
		if($fields['location']){
			$q="select idlocation from docs_location where idlocation='".$fields['location']."' limit 1";
			$r = pmb_mysql_query($q, $dbh);
			if (!pmb_mysql_num_rows($r)) $fields['location']=0;
		}
		if (!$fields['location']) {
			$loca = pmb_mysql_query("select min(idlocation) as idlocation from docs_location", $dbh);
			$locaid = pmb_mysql_fetch_object($loca);
			$fields['location'] = $locaid->idlocation;
		}
		if($fields['mail'])if(!filter_var($fields['mail'], FILTER_VALIDATE_EMAIL)) return $this->build_error( "Le champ 'mail' = ".$fields['mail']." n'est pas un mail valide.");
	
		if(!$fields['sexe']) $fields['sexe']=0;
		if(!$fields['lang']) $fields['lang']=$lang;
	
		$requete = "UPDATE empr SET ";
		$requete .= "empr_nom='".addslashes($fields['nom'])."', ";
		$requete .= "empr_prenom='".addslashes($fields['prenom'])."', ";
		$requete .= "empr_adr1='".addslashes($fields['adr1'])."', ";
		$requete .= "empr_adr2='".addslashes($fields['adr2'])."', ";
		$requete .= "empr_cp='".addslashes($fields['cp'])."', ";
		$requete .= "empr_ville='".addslashes($fields['ville'])."', ";
		$requete .= "empr_pays='".addslashes($fields['pays'])."', ";
		$requete .= "empr_mail='".addslashes($fields['mail'])."', ";
		$requete .= "empr_tel1='".addslashes($fields['tel1'])."', ";
		$requete .= "empr_sms='".addslashes($fields['sms'])."', ";
		$requete .= "empr_tel2='".addslashes($fields['tel2'])."', ";
		$requete .= "empr_prof='".addslashes($fields['prof'])."', ";
		$requete .= "empr_year='".addslashes($fields['birth'])."', ";
		$requete .= "empr_categ='".$fields['categ']."', ";
		$requete .= "empr_statut='".$fields['statut']."', ";
		$requete .= "empr_lang='".addslashes($fields['lang'])."', ";
			
		if ($fields['date_adhesion']=="") $requete .= "empr_date_adhesion=CURRENT_DATE(), "; else $requete .= "empr_date_adhesion='".addslashes($fields['date_adhesion'])."', ";
		if (($fields['date_expiration']=="") or ($fields['date_expiration']==$fields['date_adhesion'])) {
			/* AJOUTER ICI LE CALCUL EN FONCTION DE LA CATEGORIE */
			$rqt_empr_categ = "select duree_adhesion from empr_categ where id_categ_empr = ".$fields['categ']." ";
			$res_empr_categ = pmb_mysql_query($rqt_empr_categ, $dbh);
			$empr_categ = pmb_mysql_fetch_object($res_empr_categ);
				
			if($fields['date_adhesion'])	$rqt_date = "select date_add('".addslashes($fields['date_adhesion'])."', INTERVAL ".$empr_categ->duree_adhesion." DAY) as date_expiration " ;
			else $rqt_date = "select date_add(CURRENT_DATE(), INTERVAL ".$empr_categ->duree_adhesion." DAY) as date_expiration " ;
			$resultatdate=pmb_mysql_query($rqt_date);
			$resdate=pmb_mysql_fetch_object($resultatdate);
			$requete .= "empr_date_expiration='".$resdate->date_expiration."', ";
	
		} else $requete .= "empr_date_expiration='".$fields['date_expiration']."', ";
		$requete .= "empr_codestat=".$fields['cstat'].", ";
		$requete .= "empr_modif=CURRENT_DATE(), ";
		$requete .= "empr_sexe='".$fields['sexe']."', ";
		$requete .= "empr_msg='".addslashes($fields['msg'])."', ";
		$requete .= "empr_login='".addslashes($fields['login'])."', ";
		$requete .= "empr_location='".$fields['location']."', ";
	
		// ldap - MaxMan
		if ($fields['ldap']){
			$requete .= "empr_ldap='1', ";
			$fields['pwd']="";
		}else{
			$requete .= "empr_ldap='0', ";
		}
	
		//Gestion financière
		if (($pmb_gestion_abonnement==2)&&($pmb_gestion_financiere)) {
			$requete.="type_abt='".$fields['type_abt']."', ";
		} else {
			$requete.="type_abt=0, ";
		}
	
		if ($fields['pwd']!="") $requete .= "empr_password='".addslashes($fields['pwd'])."' ";
		else $requete .= "empr_password='".addslashes($fields['birth'])."' ";
	
		$requete .= " WHERE id_empr=".$empr_id." limit 1";
		
		$res = pmb_mysql_query($requete, $dbh);
		if(!$res)return $this->build_error( "Impossible de modifier le lecteur: $requete");
		
		if ($fields['pwd']!="") {
			emprunteur::update_digest($fields['login'],$fields['pwd']);
			emprunteur::hash_password($fields['login'],$fields['pwd']);
		} else {
			emprunteur::update_digest($fields['login'],$fields['birth']);
			emprunteur::hash_password($fields['login'],$fields['birth']);
		}
		
		if(is_array($fields['pperso_list'])){
			$p_perso = new parametres_perso("empr");
			foreach($fields['pperso_list'] as $pp){
				$name=$pp["name"];
				global ${$name};
				${$name}=$pp["value_list"];
	
			}
			$p_perso->rec_fields_perso($empr_id);
		}
	
		if(is_array($fields['groupe_list'])) emprunteur::rec_groupe_empr($empr_id, $fields['groupe_list']) ;
		emprunteur::ins_lect_categ_dsi($empr_id, $fields['categ'], 0) ;
		if (($pmb_gestion_financiere)&&($pmb_gestion_abonnement))	emprunteur::rec_abonnement($empr_id,$type_abt,$fields['categ']);
	
		return $this->build_ok();			
	}
	
	public function statut_list() {
		global $dbh;
		global $msg;
		global $charset;	
	
		$sql = "SELECT * FROM empr_statut ORDER BY statut_libelle ";	
		$res = pmb_mysql_query($sql);
		$i=0;
		$infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]["id"]=$res_info->idstatut;
			$infos[$i]["libelle"]=$res_info->statut_libelle;
			$infos[$i]["allow_loan"]=$res_info->allow_loan;
			$infos[$i]["allow_loan_hist"]=$res_info->allow_loan_hist;
			$infos[$i]["allow_book"]=$res_info->allow_book;
			$infos[$i]["allow_opac"]=$res_info->allow_opac;
			$infos[$i]["allow_dsi"]=$res_info->allow_dsi;
			$infos[$i]["allow_dsi_priv"]=$res_info->allow_dsi_priv;
			$infos[$i]["allow_sugg"]=$res_info->allow_sugg;
			$infos[$i]["allow_dema"]=$res_info->allow_dema;
			$infos[$i]["allow_prol"]=$res_info->allow_prol;
			$infos[$i]["allow_avis"]=$res_info->allow_avis;
			$infos[$i]["allow_tag"]=$res_info->allow_tag;
			$infos[$i]["allow_pwd"]=$res_info->allow_pwd;
			$infos[$i]["allow_liste_lecture"]=$res_info->allow_liste_lecture;
			$infos[$i]["allow_self_checkout"]=$res_info->allow_self_checkout;
			$infos[$i]["allow_self_checkin"]=$res_info->allow_self_checkin;
			$infos[$i]["allow_serialcirc"]=$res_info->allow_serialcirc;
			$infos[$i]["allow_scan_request"]=$res_info->allow_scan_request;
			$infos[$i]["allow_contribution"]=$res_info->allow_contribution;
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	
	public function categ_list() {
		global $dbh;
		global $msg;
		global $charset;
	
		$sql = "SELECT * FROM empr_categ ORDER BY libelle ";
		$res = pmb_mysql_query($sql);
		$i=0;
		$infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]["id"]=$res_info->id_categ_empr;
			$infos[$i]["libelle"]=$res_info->libelle;
			$infos[$i]["duree_adhesion"]=$res_info->duree_adhesion;
			$infos[$i]["tarif_abt"]=$res_info->tarif_abt;
			$infos[$i]["age_min"]=$res_info->age_min;
			$infos[$i]["age_max"]=$res_info->age_max;
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	public function codestat_list() {
		global $dbh;
		global $msg;
		global $charset;
	
		$sql = "SELECT * FROM empr_codestat ORDER BY libelle ";
		$res = pmb_mysql_query($sql);
		$i=0;
		$infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]["id"]=$res_info->idcode;
			$infos[$i]["libelle"]=$res_info->libelle;
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	public function groupe_list() {
		global $dbh;
		global $msg;
		global $charset;
	
		$sql = "SELECT * FROM groupe ORDER BY libelle_groupe ";
		$res = pmb_mysql_query($sql);
		$i=0;
		$infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]["id"]=$res_info->id_groupe;
			$infos[$i]["libelle"]=$res_info->libelle_groupe;
			$infos[$i]["resp_groupe"]=$res_info->resp_groupe;
			$infos[$i]["lettre_rappel"]=$res_info->lettre_rappel;
			$infos[$i]["mail_rappel"]=$res_info->id_groupe;
			$infos[$i]["lettre_rappel_show_nomgroup"]=$res_info->lettre_rappel_show_nomgroup;
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	public function abt_list() {
		global $dbh;
		global $msg;
		global $charset;
	
		$sql = "SELECT * FROM type_abts ORDER BY type_abt_libelle";
		$res = pmb_mysql_query($sql);
		$i=0;
		$infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]["id"]=$res_info->id_type_abt;
			$infos[$i]["libelle"]=$res_info->type_abt_libelle;
			$infos[$i]["prepay"]=$res_info->prepay;
			$infos[$i]["prepay_deflt_mnt"]=$res_info->prepay_deflt_mnt;
			$infos[$i]["tarif"]=$res_info->tarif;
			$infos[$i]["commentaire"]=$res_info->commentaire;
			$infos[$i]["caution"]=$res_info->caution;
			$infos[$i]["localisations"]=$res_info->localisations;
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	public function location_list() {
		global $dbh;
		global $msg;
		global $charset;
	
		$sql = "SELECT * FROM docs_location ORDER BY location_libelle";
		$res = pmb_mysql_query($sql);
		$i=0;
		$infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]["id"]=$res_info->idlocation;
			$infos[$i]["libelle"]=$res_info->location_libelle;		
			$infos[$i]["visible_opac"]=$res_info->location_visible_opac;	
			$infos[$i]["codage_import"]=$res_info->locdoc_codage_import;			
			$infos[$i]["name"]=$res_info->name;
			$infos[$i]["adr1"]=$res_info->adr1;
			$infos[$i]["adr2"]=$res_info->adr2;
			$infos[$i]["cp"]=$res_info->cp;
			$infos[$i]["town"]=$res_info->town;
			$infos[$i]["state"]=$res_info->state;
			$infos[$i]["country"]=$res_info->country;
			$infos[$i]["phone"]=$res_info->phone;
			$infos[$i]["email"]=$res_info->email;
			$infos[$i]["website"]=$res_info->website;
			$infos[$i]["logo"]=$res_info->logo;
			$infos[$i]["commentaire"]=$res_info->commentaire;
			$infos[$i]["surloc_num"]=$res_info->surloc_num;
			$infos[$i]["surloc_used"]=$res_info->surloc_used;
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	public function surlocation_list() {
		global $dbh;
		global $msg;
		global $charset;
	
		$sql = "SELECT * FROM sur_location ORDER BY surloc_libelle";
		$res = pmb_mysql_query($sql);
		$i=0;
		$infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]["id"]=$res_info->surloc_id;
			$infos[$i]["libelle"]=$res_info->surloc_libelle;
			$infos[$i]["visible_opac"]=$res_info->surloc_visible_opac;
			$infos[$i]["name"]=$res_info->surloc_name;
			$infos[$i]["adr1"]=$res_info->surloc_adr1;
			$infos[$i]["adr2"]=$res_info->surloc_adr2;
			$infos[$i]["cp"]=$res_info->surloc_cp;
			$infos[$i]["town"]=$res_info->surloc_town;
			$infos[$i]["state"]=$res_info->surloc_state;
			$infos[$i]["country"]=$res_info->surloc_country;
			$infos[$i]["phone"]=$res_info->surloc_phone;
			$infos[$i]["email"]=$res_info->surloc_email;
			$infos[$i]["website"]=$res_info->surloc_website;
			$infos[$i]["logo"]=$res_info->surloc_logo;
			$infos[$i]["commentaire"]=$res_info->surloc_commentaire;
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	public function caddie_list() {
		global $dbh;
		global $msg;
		global $charset;
	
		$sql = "SELECT * FROM empr_caddie ORDER BY name";
		$res = pmb_mysql_query($sql);
		$i=0;
		$infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]["id"]=$res_info->idemprcaddie;
			$infos[$i]["libelle"]=$res_info->name;
			$infos[$i]["comment"]=$res_info->comment;
			$infos[$i]["autorisations"]=$res_info->autorisations;
			$sql_count = "SELECT id_empr FROM empr_caddie_content,empr where empr_caddie_id =".$res_info->idemprcaddie." and id_empr= object_id ";
			$res_count = pmb_mysql_query($sql_count);
			$infos[$i]["nb_empr"]=pmb_mysql_num_rows($res_count);
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	public function caddie_empr_list($caddie_id) {
		global $dbh;
		global $msg;
		global $charset;
				
		$caddie_id+=0;
		$sql = "SELECT id_empr, empr_cb,flag FROM empr_caddie_content,empr where empr_caddie_id =$caddie_id and id_empr= object_id ORDER BY empr_nom";
		$res = pmb_mysql_query($sql);
		$i=0;
		$infos= array();
		while( $res_info=pmb_mysql_fetch_object($res)){
			$infos[$i]["empr_id"]=$res_info->id_empr;
			$infos[$i]["empr_cb"]=$res_info->empr_cb;
			if($res_info->flag)	$infos[$i]["flag"]=1;
			else $infos[$i]["flag"]=0;
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	public function procédure_exec($id) {
		global $dbh;
		global $msg;
		global $charset;
		global $PMBuserid;
		
		$id+=0;
		if ($PMBuserid!=1)
			$where=" and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
		
		$requete = "SELECT * FROM empr_caddie_procs WHERE idproc=$id $where ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = pmb_mysql_num_rows($res);
		$row = pmb_mysql_fetch_row($res);
		$idp = $row[0];
		$name = $row[2];
		$commentaire = $row[4];
		if (!$code)
			$code = $row[3];
		$commentaire = $row[4];
		
		return $this->build_ok($infos);
	}
	
	public function caddie_pointage_raz($caddie_id) {
		global $dbh;
		global $msg;
		global $charset;
		global $PMBuserid;
		$caddie_id+=0;
		$myCart = new empr_caddie($caddie_id);
		print $myCart->aff_cart_titre();
		$droit = empr_caddie::check_rights($caddie_id) ;
		if ($droit) $myCart->depointe_items();
	
		return $this->build_ok($infos);
	}			
	public function add_in_caddie($empr_cb='', $empr_id=0, $caddie_id) {
		global $dbh,$lang;
		global $msg;
		global $charset;
	
		$empr_cb=$this->clean_field((string)$empr_cb);
		$empr_id += 0;
		if (!$empr_id && $empr_cb=='') return $this->build_error( "idempr et empr_cb vide.");
	
		if($empr_id)	$where=" id_empr = $empr_id ";
		else $where=" empr_cb = '".addslashes($empr_cb)."' ";
	
		$sql = "SELECT id_empr, empr_cb FROM empr WHERE $where";
		$res = pmb_mysql_query($sql);
		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_error( $msg['54'].": 'empr_cb' = $empr_cb ou id_empr = $empr_id .");
				
		$empr_res = pmb_mysql_fetch_object($res);
		$empr_id=$empr_res->id_empr;
		
		$caddie_id+=0;
		$sql = "SELECT idemprcaddie FROM empr_caddie WHERE idemprcaddie = $caddie_id";
		$res = pmb_mysql_query($sql);
		if (!$res|| !pmb_mysql_num_rows($res) ) return $this->build_error( "Panier inconnu: 'caddie_id' = $caddie_id .");
		
		$sql = "INSERT INTO empr_caddie_content SET empr_caddie_id=$caddie_id, object_id=$empr_id";
		pmb_mysql_query($sql);
		return $this->build_ok();
	}	
	
	public function pointe_in_caddie($empr_cb='', $empr_id=0, $caddie_id) {
		global $dbh,$lang;
		global $msg;
		global $charset;
	
		$empr_cb=$this->clean_field((string)$empr_cb);
		$empr_id += 0;
		if (!$empr_id && $empr_cb=='') return $this->build_error( "idempr et empr_cb vide.");
	
		if($empr_id)	$where=" id_empr = $empr_id ";
		else $where=" empr_cb = '".addslashes($empr_cb)."' ";
	
		$sql = "SELECT id_empr, empr_cb FROM empr WHERE $where";
		$res = pmb_mysql_query($sql);
		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_error( $msg['54'].": 'empr_cb' = $empr_cb ou id_empr = $empr_id");
	
		$empr_res = pmb_mysql_fetch_object($res);
		$empr_id=$empr_res->id_empr;
	
		$caddie_id+=0;
		$sql = "SELECT idemprcaddie FROM empr_caddie WHERE idemprcaddie = $caddie_id";
		$res = pmb_mysql_query($sql);
		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_error( "Panier inconnu: 'caddie_id' = $caddie_id .");
	
		$sql = "update empr_caddie_content SET flag='1' where object_id=$empr_id and empr_caddie_id=$caddie_id limit 1";
		pmb_mysql_query($sql);
		return $this->build_ok();
	}	
	
	public function is_in_caddie($empr_cb='', $empr_id=0, $caddie_id) {
		global $dbh,$lang;
		global $msg;
		global $charset;
		
		$result = array(
				'status' => 0,
				'flag' => 0
		);
		$empr_cb=$this->clean_field((string)$empr_cb);
		$empr_id += 0;
		if (!$empr_id && $empr_cb=='') return $this->build_ok($result,"idempr et empr_cb vide.",false);
	
		if($empr_id)	$where=" id_empr = $empr_id ";
		else $where=" empr_cb = '".addslashes($empr_cb)."' ";
	
		$sql = "SELECT id_empr, empr_cb FROM empr WHERE $where";
		$res = pmb_mysql_query($sql);
		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_ok($result,$msg['54'].": 'empr_cb' = $empr_cb ou id_empr = $empr_id",false);

		$empr_res = pmb_mysql_fetch_object($res);
		$empr_id=$empr_res->id_empr;
	
		$caddie_id+=0;
		$sql = "SELECT idemprcaddie FROM empr_caddie WHERE idemprcaddie = $caddie_id";
		$res = pmb_mysql_query($sql);
		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_ok($result,"Panier inconnu: 'caddie_id' = $caddie_id .",false);
		
		$sql = "SELECT * FROM empr_caddie_content WHERE empr_caddie_id = $caddie_id";
		$res = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($res)) {
			$res_info=pmb_mysql_fetch_object($res);
			return $this->build_ok(array(
				'status' => true,
				'flag' => $res_info->flag
			));
		}			
		return $this->build_ok(array(
			'status' => false,
			'flag' => 0
		));				
	}
		
	public function lang_list() {	
		global $include_path;
		global $msg;
		global $charset;
		
		$la = new XMLlist("$include_path/messages/languages.xml", 0);
		$la->analyser();
		$languages = $la->table;
		$i=0;
		foreach($languages as $codelang => $libelle){
			$infos[$i]["codelang"]=$codelang;
			$infos[$i]["libelle"]=$libelle;
			$i++;
		}
		return $this->build_ok($infos);
	}
	
	public function pperso_list_type_values($pperso_name) {
		global $dbh;
		
		$result = array(
				'status' => 0,
				'flag' => 0
		);

		$sql = "SELECT idchamp FROM empr_custom WHERE name='".addslashes($pperso_name)."' AND type='list'";
		$res = pmb_mysql_query($sql);

		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_ok($result,"Champ personnalisé inconnu ou pas de type liste.",false);
		
		$pperso_res = pmb_mysql_fetch_object($res);
		$pperso_id=$pperso_res->idchamp;
		
		$sql = "SELECT empr_custom_list_value, empr_custom_list_lib FROM empr_custom_lists WHERE empr_custom_champ='".$pperso_id."' ORDER BY ordre";
		$res = pmb_mysql_query($sql);
		
		if (!$res || !pmb_mysql_num_rows($res)) return $this->build_ok($result,"Aucune valeur pour ce champ.",false);
		
		$i=0;
		while ($pperso_res = pmb_mysql_fetch_object($res)) {
			$infos[$i]["value"]=$pperso_res->empr_custom_list_value;
			$infos[$i]["libelle"]=$pperso_res->empr_custom_list_lib;
			$i++;
		}

		return $this->build_ok($infos);
	}
	
	public function clean_field($field,$addslashes=0){
		global $charset;
		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$field = utf8_encode($field);
		}
		else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$field = utf8_decode($field);
		}
		if($addslashes==1) $field=addslashes($field);
		
		return $field;
	}	
	
	public function clean_fields($field){
		array_walk_recursive($field, function(&$data,$key,$input_charset) {
			global $charset;
			if ($input_charset!='utf-8' && $charset == 'utf-8') {
				$data = utf8_encode($data);
			}
			else if ($input_charset=='utf-8' && $charset != 'utf-8') {
				$data = utf8_decode($data);
			}
			
		},$this->proxy_parent->input_charset);
		return $field;
	}
	
	public function build_ok($result=array(),$msg="",$statut=true){	
		array_walk_recursive($result, function(&$data) {
			$data = utf8_normalize($data);
		});
		return  array(
			'status' => $statut,
			'status_msg' => utf8_normalize($msg),
			'data'=>$result
		);
	}
	
	public function build_error($msg){
		return  array(
			'status' => false,
			'status_msg' => utf8_normalize($msg)
		);
	}
	
	
	public function bibloto_empr_list($filters=array()) {
	    global $dbh;
	    global $msg;
	    global $charset;
	    
	    $sql_filters="";
	    object_to_array($filters);
	    if(is_array($filters)){
	        $i=0;
	        foreach($filters as $filter){
	            if(!$filter['field']) continue;
	            
	            if($i==0) $sql_filters=" where ";
	            else {
	                if($filter['separator'])
	                    $sql_filters.=" ".$filter['separator']." ";
	                    else
	                        $sql_filters.=" and ";
	            }
	            $sql_filters.= $filter['field']." ".$filter['operator']." '".$filter['value']."' ";
	            $i++;
	        }
	    }
	    $infos= array();
	    $sql = "SELECT id_empr, empr_cb, empr_nom, empr_prenom FROM empr $sql_filters";
	    $res = pmb_mysql_query($sql);
	    $i=0;
	    while( $res_info=pmb_mysql_fetch_object($res)){
	        $infos[$i]['empr_cb']=$res_info->empr_cb;
	        $infos[$i]['empr_id']=$res_info->id_empr;
	        $infos[$i]['empr_nom']=$res_info->empr_nom;
	        $infos[$i]['empr_prenom']=$res_info->empr_prenom;
	        $i++;
	    }
	    return $this->build_ok($infos);
	}
}
