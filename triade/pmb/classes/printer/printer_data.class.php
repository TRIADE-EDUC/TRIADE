<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: printer_data.class.php,v 1.5 2017-09-18 13:20:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once('./circ/print_pret/func.inc.php');

require_once("$base_path/classes/comptes.class.php");
require_once($class_path."/transaction/transaction.class.php");
require_once($include_path."/notice_authors.inc.php");

class printer_data {
	
	public $data;	// info biblo, empr, expl utile à l'impression
	
	public function __construct(){
		$this->fetch_data();
	}
	
	protected function fetch_data(){
		$this->get_data_mybiblio();
	}
	
	public function get_data_mybiblio(){
		global $biblio_name, $biblio_logo, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email, $biblio_website ;
		global $deflt_docs_location;
		$this->data["biblio"]["name"]=$biblio_name;
		$this->data["biblio"]["email"]=$biblio_email;
		$this->data["biblio"]["adr1"]=$biblio_adr1;
		$this->data["biblio"]["town"]=$biblio_town;
		$this->data["biblio"]["phone"]=$biblio_phone;
		$this->data["biblio"]["email"]=$biblio_email;
		$this->data["biblio"]["id_location"]=$deflt_docs_location;	
		return	$this->data["biblio"];
	}
	
	public function get_data_resa($id_resa_print){		
		global $dbh, $msg;
		$dates_resa_sql = " date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin " ;
		$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, resa_date_debut, resa_date_fin, resa_cb, resa_loc_retrait, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql ;
		$requete.= "FROM (((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ) LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id) ";
		$requete.= "WHERE id_resa='".$id_resa_print."' ";
		
		$res = pmb_mysql_query($requete, $dbh);
		$expl = pmb_mysql_fetch_object($res);
		
		$responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
		$header_aut = gen_authors_header($responsabilites);
		//$header_aut ? $auteur=" / ".$header_aut : $auteur="";
		
		$rqt_detail = "select resa_confirmee, resa_cb,location_libelle, expl_cote from resa
		left join exemplaires on expl_cb=resa_cb
		left join docs_location on idlocation=expl_location
		where id_resa =$id_resa_print  and resa_cb is not null and resa_cb!='' ";
		$res_detail = pmb_mysql_query($rqt_detail) ;
		$expl_detail = pmb_mysql_fetch_object($res_detail);
		
		$data_resa["id"]=$id_resa_print;
		$data_resa["titre"]=$expl->tit;
		$data_resa["auteur"]=$header_aut;
		$data_resa["cb"]=$expl->resa_cb;
		$data_resa["cote"]=$expl->expl_cote;
		$data_resa["date_debut"]=$expl->aff_resa_date_debut;
		$data_resa["date_fin"]=$expl->aff_resa_date_fin;
		
		$i=count($this->data["resa_list"]);
		$this->data["resa_list"][$i]=$data_resa;
		return $data_resa;
	}
		
	public function get_data_empr($id_empr){	
		global $dbh,$pmb_gestion_financiere, $pmb_gestion_abonnement,$pmb_gestion_tarif_prets,$pmb_gestion_amende; 	
		
		$empr=get_info_empr($id_empr);		
		$i=count($this->data["empr_list"]);
		$this->data["empr_list"][$i]["name"]=$empr->nom;
		$this->data["empr_list"][$i]["fistname"]=$empr->prenom;	
		$this->data["empr_list"][$i]["cb"]=$empr->cb;
		$this->data["empr_list"][$i]["aff_date_adhesion"]=$empr->aff_date_adhesion;
		$this->data["empr_list"][$i]["aff_date_expiration"]=$empr->aff_date_expiration;	
		$this->data["empr_list"][$i]["categorie"]=$empr->cat_l;
		
		if($pmb_gestion_financiere){
			$this->data["empr_list"][$i]["compte_abonnement"]["solde"]="";
			$this->data["empr_list"][$i]["compte_abonnement"]["novalid"]="";
			// abonnement 
			if($pmb_gestion_abonnement){
				$cpt_id=comptes::get_compte_id_from_empr($id_empr,1);
				$cpt=new comptes($cpt_id);
				$solde=$cpt->update_solde();
				$novalid=$cpt->summarize_transactions("","",0,0);
				$this->data["empr_list"][$i]["compte_abonnement"]["solde"]=$solde;
				$this->data["empr_list"][$i]["compte_abonnement"]["novalid"]=$novalid;
			}
			// prets
			$this->data["empr_list"][$i]["compte_pret"]["solde"]="";
			$this->data["empr_list"][$i]["compte_pret"]["novalid"]="";
			if($pmb_gestion_tarif_prets){				
				$cpt_id=comptes::get_compte_id_from_empr($id_empr,3);
				$cpt=new comptes($cpt_id);
				$solde=$cpt->update_solde();
				$novalid=$cpt->summarize_transactions("","",0,0);
				$this->data["empr_list"][$i]["compte_pret"]["solde"]=$solde;
				$this->data["empr_list"][$i]["compte_pret"]["novalid"]=$novalid;
			}
			// amendes
			$this->data["empr_list"][$i]["compte_amende"]["solde"]="";
			$this->data["empr_list"][$i]["compte_amende"]["novalid"]="";
			if($pmb_gestion_amende){
				$cpt_id=comptes::get_compte_id_from_empr($id_empr,2);
				$cpt=new comptes($cpt_id);
				$solde=$cpt->update_solde();
				$novalid=$cpt->summarize_transactions("","",0,0);
				$this->data["empr_list"][$i]["compte_amende"]["solde"]=$solde;
				$this->data["empr_list"][$i]["compte_amende"]["novalid"]=$novalid;
			}
			// Autre compte
			$this->data["empr_list"][$i]["compte_autre"]["solde"]="";
			$this->data["empr_list"][$i]["compte_autre"]["novalid"]="";
			$transactype=new transactype_list();  
			if ($transactype->get_count()) {
				$cpt_id=comptes::get_compte_id_from_empr($id_empr,4);					
				$cpte=new comptes($cpt_id);
				$solde=$cpt->update_solde();
				$novalid=$cpt->summarize_transactions("","",0,0);
				$this->data["empr_list"][$i]["compte_autre"]["solde"]=$solde;
				$this->data["empr_list"][$i]["compte_autre"]["novalid"]=$novalid;				
			}
		}
		return $this->data["empr_list"][$i];
	}
	
	public function get_data_expl($cb_doc){		
		$expl=get_info_expl($cb_doc);		
		$i=count($this->data["expl_list"]);
		$this->data["expl_list"][$i]["tit"]=$expl->tit;
		$this->data["expl_list"][$i]["header_aut"]=$expl->header_aut;
		$this->data["expl_list"][$i]["cb"]=$expl->expl_cb;
		$this->data["expl_list"][$i]["id_location"]=$expl->expl_location;
		$this->data["expl_list"][$i]["location"]=$expl->location_libelle;
		$this->data["expl_list"][$i]["section"]=$expl->section_libelle;
		$this->data["expl_list"][$i]["cote"]=$expl->expl_cote;
		$this->data["expl_list"][$i]["date_pret"]=$expl->aff_pret_date;
		$this->data["expl_list"][$i]["date_retour"]=$expl->aff_pret_retour;	
		return $this->data["expl_list"][$i];
	}	
	
	public function get_data_transactions($transacash_id){		
		global $dbh;
		
		$req="select * from transacash where transacash_id=$transacash_id";
		$result = pmb_mysql_query($query, $dbh);
		if (($r= pmb_mysql_fetch_object($result))) {
			$this->data["transacash"][0]["id_empr"]=$r->transacash_empr_num;
			$this->data["transacash"][0]["date"]=$r->transacash_date;
			$this->data["transacash"][0]["sold_before"]=$r->transacash_sold;
			$this->data["transacash"][0]["collected"]=$r->transacash_collected;
			
			$req="select * from transactions where transacash_num=$transacash_id and encaissement=0";
			$result = pmb_mysql_query($query, $dbh);
			$i=0;
			if (($r= pmb_mysql_fetch_object($result))) {				
				$this->data["transacash"][0]["transaction"][$i]["name"]=$r->commentaire;	
				$this->data["transacash"][0]["transaction"][$i]["montant"]=$r->montant;
				
				$i++;
			}
		}
		return $this->data["transacash"][0];
	}	

	
}