<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: comptes.class.php,v 1.7 2023/03/21 13:35:34 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

define('CMPTE_INIT',1);
define('CMPTE_CREATE',2);
define('CMPTE_REC_TRANSACTION',3);
define('CMPTE_VALIDATE_TRANSACTION',4);
define('CMPTE_UPDATE_SOLDE',5);

class comptes {

	public $id_compte; //Identifiant du compte en cours
	public $typ_compte; //Type de compte en cours de traitement
	public $compte; //Informations du compte

	public $error=false;
	public $error_message="";
	public $error_action=0;

    public function __construct($id_compte="") {
    	global $msg;

    	if ($id_compte) {
    		//Vérification que le compte existe
    		$requete="select id_compte,type_compte_id from comptes where id_compte='".$id_compte."'";
    		$resultat=pmb_mysql_query($requete);
    		if (@pmb_mysql_num_rows($resultat)) {
    			$this->id_compte=$id_compte;
    			$this->typ_compte=pmb_mysql_result($resultat,0,1);
    		} else {
    			$this->error=true;
    			$this->error_message=sprintf($msg["cmpt_bad_id"],$id_compte);
    			$this->error_action=CMPTE_INIT;
    		}
    	} else $this->id_compte="";
    }

    public function is_typ_compte($typ_compte) {
    	$requete="select * from type_comptes where id_type_compte='".$typ_compte."'";
    	$resultat=pmb_mysql_query($requete);
    	if (pmb_mysql_num_rows($resultat)) {
    		$this->typ_compte=pmb_mysql_fetch_object($resultat);
    		return true;
    	} else return false;
    }

   	public function is_valid() {
   		if ($this->id_compte) return true; else return false;
   	}

    public function must_be_unique() {
    	if ($this->typ_compte->multiple==0) return true; else return false;
    }

    public function create_compte($libelle,$typ_compte,$proprio_id,$droits) {
    	global $msg;

    	//Vérification validité du type de compte
    	if (!$this->is_typ_compte($typ_compte)) {
    		$this->error=true;
    		$this->error_message=sprintf($msg["cmpt_bad_typ_compte"],$typ_compte);
    		$this->error_action=CMPTE_CREATE;
    		return false;
    	}  else {
    		//Vérification propriétaire

    		//Vérification unicité si nécessaire
    		if ($this->must_be_unique()) {
    			//Y-a-t-il déjà un compte existant pour ce propriétaire ?
    			$requete="select count(1) from comptes where type_compte_id='".$typ_compte."' and proprio_id='".$proprio_id."'";
    			$resultat=pmb_mysql_query($requete);
    			if (pmb_mysql_result($resultat,0,0)) {
    				$this->error=true;
    				$this->error_message=sprintf($msg["cmpt_not_unique"],$typ_compte,$proprio_id);
    				$this->error_action=CMPTE_CREATE;
    				return false;
    			}
    		}
    		//Création
    		$requete="insert into comptes (libelle,type_compte_id,proprio_id,droits) values('".addslashes($libelle)."',$typ_compte,$proprio_id,'".addslashes($droits)."')";
    		$resultat=pmb_mysql_query($requete);
    		if (!$resultat) {
    			$this->error=true;
    			$this->error_message=$msg["cmpt_create_failed"];
    			$this->error_action=CMPTE_CREATE;
    			return false;
    		}

    		//Lecture des infos comptes
    		$this->id_compte=pmb_mysql_insert_id();
    		$requete="select * from comptes where id_compte=".$this->id_compte;
    		$resultat=pmb_mysql_query($requete);
    		$this->compte=pmb_mysql_fetch_object($resultat);
    	}
    	return true;
    }

    public function record_transaction($date_prevue,$montant,$sens,$comment="",$encaissement=0) {
    	global $msg;
    	global $PMBuserid, $PMBusername;
    	if ($this->is_valid()) {
    		//Vérification du sens
    		if (($sens!=-1)&&($sens!=1)) {
    			$this->error=false;
    			$this->error_message=$msg["cmpt_bad_sens"];
    			$this->error_action=CMPTE_REC_TRANSACTION;
    			return false;
    		}
    		//Récupération des infos annexes
    		$machine=$_SERVER["REMOTE_ADDR"];
    		if (!$date_prevue) $date_prevue=date("Y-m-d");
    		$requete="insert into transactions (compte_id,user_id,user_name,machine,date_enrgt,date_prevue,montant,sens,commentaire,encaissement) values(".$this->id_compte.",$PMBuserid,'".addslashes($PMBusername)."','$machine',now(),'".$date_prevue."','$montant',$sens,'".addslashes($comment)."',$encaissement)";
    		if (!pmb_mysql_query($requete)) {
    			$this->error=true;
    			$this->error_message=sprintf($msg["cmpt_query_transaction_failed"],pmb_mysql_error());
    			$this->error_action=CMPTE_REC_TRANSACTION;
    			return false;
    		}
    		return pmb_mysql_insert_id();
    	} else return false;
    }

    public function is_transaction_validate($id_transaction) {
    	$requete="select count(*) from transactions where id_transaction=$id_transaction and realisee=1";
    	$resultat=pmb_mysql_query($requete);
    	if (@pmb_mysql_result($resultat,0,0)) return true; else return false;
    }

    public function transaction_exists($id_transaction) {
    	$requete="select count(*) from transactions where id_transaction=$id_transaction and compte_id=".$this->id_compte;
    	$resultat=pmb_mysql_query($requete);
    	if (@pmb_mysql_result($resultat,0,0)) return true; else return false;
    }

    public function validate_transaction($id_transaction) {
    	global $msg;
    	if ($this->is_valid()) {
	    	if ($this->transaction_exists($id_transaction)) {
	    		if (!$this->is_transaction_validate($id_transaction)) {
		 	   		$requete="update transactions set realisee=1, date_effective=now() where id_transaction=$id_transaction";
 	  		 		pmb_mysql_query($requete);
	    			return true;
	    		} else {
	    			$this->error=false;
   		 			$this->error_message=sprintf($msg["cmpt_transaction_already_validate"],$id_transaction);
   		 			$this->error_action=CMPTE_VALIDATE_TRANSACTION;
   		 			return false;
	    		}
  	 	 	} else {
   		 		$this->error=false;
   		 		$this->error_message=sprintf($msg["cmpt_transaction_does_not_exists"],$id_transaction);
   		 		$this->error_action=CMPTE_VALIDATE_TRANSACTION;
	    		return false;
   		 	}
    	} else return false;
    }

    public function delete_transaction($id_transaction) {
    	global $msg;
    	if ($this->is_valid()) {
	    	if ($this->transaction_exists($id_transaction)) {
	    		if (!$this->is_transaction_validate($id_transaction)) {
		 	   		$requete="delete from transactions where id_transaction=$id_transaction";
 	  		 		pmb_mysql_query($requete);
	    			return true;
	    		} else {
	    			$this->error=false;
   		 			$this->error_message=sprintf($msg["cmpt_transaction_already_validate"],$id_transaction);
   		 			$this->error_action=CMPTE_VALIDATE_TRANSACTION;
   		 			return false;
	    		}
  	 	 	} else {
   		 		$this->error=false;
   		 		$this->error_message=sprintf($msg["cmpt_transaction_does_not_exists"],$id_transaction);
   		 		$this->error_action=CMPTE_VALIDATE_TRANSACTION;
	    		return false;
   		 	}
    	} else return false;
    }

	public function summarize_transactions($date_debut,$date_fin,$sens=0,$realisee=1) {
    	global $msg;
    	if ($this->is_valid()) {
    		if ($date_debut) $date_debut_terme=" and date_effective>='$date_debut'";
    		else $date_debut_terme="";
    		if ($date_fin) $date_fin_terme=" and date_effective<='$date_fin'";
    		else $date_fin_terme="";
    		if (($sens==-1)||($sens==1)) $sens_terme=" and sens=$sens";
    		else $sens_terme="";
    		if ($realisee!=-1) $realisee_terme=" and realisee=$realisee";
    		else $realisee_terme="";
    		$requete="select sum(montant*sens) from transactions where compte_id=".$this->id_compte.$date_debut_terme.$date_fin_terme.$sens_terme.$realisee_terme;
    		$resultat=pmb_mysql_query($requete);
    		$montant=@pmb_mysql_result($resultat,0,0);
    		return $montant;
    	} else return false;
    }

	public function get_transactions($date_debut,$date_fin,$sens=0,$realisee=-1, $limit=0, $order="desc") {
    	if ($this->is_valid()) {
    		if ($date_debut) $date_debut_terme=" and date_enrgt>='$date_debut'";
    		else $date_debut_terme="";
    		if ($date_fin) $date_fin_terme=" and date_enrgt<='$date_fin'";
    		else $date_fin_terme="";
    		if (($sens==-1)||($sens==1)) $sens_terme=" and sens=$sens";
    		else $sens_terme="";
    		if ($realisee!=-1) $realisee_terme=" and realisee=$realisee";
    		else $realisee_terme="";
    		$requete="select * from transactions where compte_id=".$this->id_compte.$date_debut_terme.$date_fin_terme.$sens_terme.$realisee_terme." order by date_enrgt $order";
    		if ($limit) $requete.=" limit $limit";
    		$resultat=pmb_mysql_query($requete);
    		while ($r=pmb_mysql_fetch_object($resultat)) {
    			$t[]=$r;
    		}
    		return $t;
    	} else return false;
    }

    public function update_solde() {
    	global $msg ;
    	if ($this->is_valid()) {
    		$solde=$this->summarize_transactions("","",0,1);
    		if ($solde=="") $solde=0;
    		if ($solde!==false) {
    			$requete="update comptes set solde=".$solde." where id_compte=".$this->id_compte;
    			$update=pmb_mysql_query($requete);
    			if (!$update) {
    				$this->error=false;
   		 			$this->error_message=sprintf($msg["cmpt_update_solde_query_failed"],pmb_mysql_error());
   		 			$this->error_action=CMPTE_UPDATE_SOLDE;
    			} else return $solde;
    		} else {
    			$this->error=false;
   		 		$this->error_message=$msg["cmpt_update_solde_summarize_failed"];
   		 		$this->error_action=CMPTE_UPDATE_SOLDE;
    		}
    	} else return false;
    }

	public static function get_compte_id_from_empr($empr_id,$typ_compte) {
    	$requete="select id_compte from comptes where proprio_id='$empr_id' and type_compte_id='".$typ_compte."'";
    	$resultat=pmb_mysql_query($requete);
    	if (@pmb_mysql_num_rows($resultat)==0) {
    		//Compte inexistant : création
    		$requete="insert into comptes (libelle,type_compte_id,solde,prepay_mnt,proprio_id) values('Created on ".date("Y-m-d")."',$typ_compte,0,0,$empr_id)";
     		$r=pmb_mysql_query($requete);
    		if ($r) return pmb_mysql_insert_id(); else return false;
    	}
    	if (@pmb_mysql_num_rows($resultat)>1) return false;
    	return pmb_mysql_result($resultat,0,0);
    }

    public function get_empr_from_compte_id() {
    	$requete="select proprio_id from comptes where id_compte=".$this->id_compte;
    	$resultat=pmb_mysql_query($requete);
    	if (@pmb_mysql_num_rows($resultat)) return pmb_mysql_result($resultat,0,0); else return false;
    }

    public static function format($f) {
    	global $pmb_gestion_devise, $pmb_fine_precision;
    	if (!isset($pmb_fine_precision)) $pmb_fine_precision=2;
    	$neg="<span class='erreur'>%s %s</span>";
		$pos="%s %s";
    	return sprintf($f<0?$neg:$pos,sprintf('%01.'.$pmb_fine_precision.'f',$f),$pmb_gestion_devise);
    }

    public static function format_simple($f) {
    	global $pmb_gestion_devise,$pmb_fine_precision;
    	if (!isset($pmb_fine_precision)) $pmb_fine_precision=2;
    	$pos="%s %s";
    	return sprintf($pos,sprintf('%01.'.$pmb_fine_precision.'f',$f),$pmb_gestion_devise);
    }

    public function get_typ_compte_lib($id_typ_compte) {
    	global $msg;
    	$r="";
    	switch ($id_typ_compte) {
    		case 1:
    			$r=$msg["finance_cmpte_abt"];
    			break;
    		case 2:
    			$r=$msg["finance_cmpte_amendes"];
    			break;
    		case 3:
    			$r=$msg["finance_cmpte_prets"];
    			break;
    		case 22:
    			$r=$msg["finance_cmpte_animation"];
    			break;
    		default:
    			$requete="select libelle from type_comptes where id_type_compte=".$id_typ_compte;
    			$resultat=pmb_mysql_query($requete);
    			if (@pmb_mysql_num_rows($resultat)) $r=pmb_mysql_result($resultat,0,0);
    	}
    	return $r;
    }

    public function get_solde() {
    	if ($this->is_valid()) {
    		$requete="select solde from comptes where id_compte=".$this->id_compte;
    		$resultat=pmb_mysql_query($requete);
    		if (@pmb_mysql_num_rows($resultat)) return pmb_mysql_result($resultat,0,0); else return false;
    	} else return false;
    }

    public function frais_relance($niveau) {
    	global $finance_relance_1, $finance_relance_2, $finance_relance_3;

    	$frais=0;

    	if ($niveau>0) $frais+=$finance_relance_1;
    	if ($niveau>1) $frais+=$finance_relance_2;
    	if ($niveau>2) $frais+=$finance_relance_3;

    	return $frais;
    }
}
?>