<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transfert.class.php,v 1.78.4.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );

global $class_path, $include_path;
require_once ("$include_path/templates/transferts.tpl.php");
require_once ("$include_path/resa.inc.php");
require_once ("$include_path/resa_func.inc.php");
require_once($class_path.'/event/events/event_transfert.class.php');
require_once($class_path.'/event/events_handler.class.php');
require_once ($class_path.'/transfert_demande.class.php');

//********************************************************************************************
// Classe de gestion des transferts d'exemplaire entre localisations
//********************************************************************************************

class transfert {
	protected $id;

	protected $num_notice;

	protected $num_bulletin;

	protected $date_creation;

	protected $formatted_date_creation;

	protected $type_transfert;

	protected $etat_transfert;

	protected $origine;

	protected $origine_comp;

	protected $source;

	protected $destinations;

	protected $date_retour;

	protected $formatted_date_retour;

	protected $motif;

	protected $transfert_ask_user_num;

	protected $transfert_send_user_num;

	protected $transfert_ask_date;

	protected $transfert_ask_formatted_date;

	protected $exemplaire;

	protected $transfert_demande;

	public $location_libelle_source;

	protected $location_origine;

	protected $location_libelle_origine;

	public $validation_send_event=0;

	public $num_exemplaire;
	public $new_location_libelle;
	public $check_pret_error_message;

	// constructeur
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}

	protected function fetch_data() {
		$this->num_notice = 0;
		$this->num_bulletin = 0;
		$this->date_creation = '';
		$this->formatted_date_creation = '';
		$this->type_transfert = 0;
		$this->etat_transfert = '';
		$this->origine = 0;
		$this->origine_comp = '';
		$this->source = 0;
		$this->destinations = '';
		$this->date_retour = '';
		$this->formatted_date_retour = '';
		$this->motif = '';
		$this->transfert_ask_user_num = 0;
		$this->transfert_send_user_num = 0;
		$this->transfert_ask_date = '';
		$this->transfert_ask_formatted_date = '';
		if($this->id) {
			$query = "select * from transferts
				INNER JOIN transferts_demande ON id_transfert=num_transfert
				INNER JOIN exemplaires ON num_expl=expl_id
				where id_transfert = ".$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->num_notice = $row->num_notice;
			$this->num_bulletin = $row->num_bulletin;
			$this->date_creation = $row->date_creation;
			$this->formatted_date_creation = formatdate($row->date_creation);
			$this->type_transfert = $row->type_transfert;
			$this->etat_transfert = $row->etat_transfert;
			$this->origine = $row->origine;
			$this->origine_comp = $row->origine_comp;
			$this->source = $row->source;
			$this->destinations = $row->destinations;
			$this->date_retour = $row->date_retour;
			$this->formatted_date_retour = formatdate($row->date_retour);
			$this->motif = $row->motif;
			$this->transfert_ask_user_num = $row->transfert_ask_user_num;
			$this->transfert_send_user_num = $row->transfert_send_user_num;
			$this->transfert_ask_date = $row->transfert_ask_date;
			$this->transfert_ask_formatted_date = formatdate($row->transfert_ask_date);
			$this->num_exemplaire = $row->expl_id;
			if($row->id_transfert_demande) {
				$this->transfert_demande = new transfert_demande($row->id_transfert_demande);
				$this->exemplaire = $this->transfert_demande->get_exemplaire();
			}
		}
	}
	//********************************************************************************************

	public function _creer_transfert( $id_expl, $src, $dest, $t_trans, $date_ret='', $origine=0, $ori_comp ='', $motif='', $sens=0, $etat=0, $ask_date='') {

		//on recupere le no de notice
		$rqt = "SELECT expl_notice, expl_bulletin, expl_statut, expl_section
				FROM exemplaires
				WHERE expl_id=".$id_expl;
		$res = pmb_mysql_query( $rqt );
		$expl = pmb_mysql_fetch_object($res);
		//$id_notice = pmb_mysql_result( $res, 0 );

		// verif si déjà existrant
		$rqt = "Select * from transferts ,transferts_demande where
			num_transfert=id_transfert and num_expl=$id_expl  and
			num_notice=".$expl->expl_notice." and
			num_bulletin=".$expl->expl_bulletin." and
			type_transfert=$t_trans and
			etat_transfert=0 and
			origine=$origine and
			origine_comp ='".addslashes($ori_comp)."' and
			source=$src and
			destinations =$dest ";

		$res=pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res)) {
			$obj_data = pmb_mysql_fetch_object($res);
			$num=$obj_data->id_transfert;
		} else {
			if(!$ask_date)$ask_date=" NOW() ";
			else $ask_date=" '".$ask_date."' ";
			//on cree l'enregistrement dans la table transferts
			$rqt = "INSERT INTO transferts (
						num_notice, num_bulletin, date_creation,
						type_transfert, etat_transfert,
						origine, origine_comp,
						source, destinations,
						date_retour, motif, transfert_ask_date ) VALUES (".
						$expl->expl_notice . ", " . $expl->expl_bulletin . ", NOW(),".
						$t_trans . ", 0, ".
						$origine . ", '" . addslashes($ori_comp) . "',".
						$src . ", '" . $dest . "',
						'" . $date_ret . "', '" .addslashes($motif) . "', ".$ask_date.")";
			pmb_mysql_query( $rqt );

			//on recupere l'id du transfert crée
			$num = pmb_mysql_insert_id();
		}
		$rqt = "Select * from transferts_demande where
					num_transfert=$num and
					sens_transfert=$sens and
					num_location_source=$src and
					num_location_dest=$dest and
					num_expl=$id_expl
					";
		$res=pmb_mysql_query( $rqt );

		if (!pmb_mysql_num_rows($res)) {

			//la table transferts_demande
			$rqt = "INSERT INTO transferts_demande (
						num_transfert, date_creation,
						sens_transfert, num_location_source,
						num_location_dest, num_expl,
						statut_origine, section_origine,
						etat_demande ) VALUES (".
						$num . ", NOW(), ".
						$sens . ", " . $src . ", ".
						$dest . ", " . $id_expl . ", ".
						$expl->expl_statut . ", " . $expl->expl_section . ", ".
						$etat .")";
			pmb_mysql_query( $rqt );
		}
		return $num;
	}

	//change le statut d'un exemplaire
	protected function _change_statut_exemplaire( $id_expl, $id_statut ) {
		$id_expl = intval($id_expl);
		$id_statut = intval($id_statut);
		if ($id_statut && $id_expl) {
			$rqt = 	"UPDATE exemplaires SET transfert_statut_origine = expl_statut WHERE expl_id=".$id_expl;
			pmb_mysql_query( $rqt );

			//changement du statut
			$rqt = 	"UPDATE exemplaires SET expl_statut=".$id_statut." WHERE expl_id=".$id_expl;
			pmb_mysql_query( $rqt );
		}
	}

	//change la localisation d'un exemplaire
	protected static function _change_localisation_exemplaire( $id_expl, $id_localisation, $sauve_loc = false ) {
		//sauvegarde de la localisation
		if ($sauve_loc) {
			$query = "UPDATE exemplaires SET transfert_location_origine = expl_location WHERE expl_id=".$id_expl;
			pmb_mysql_query($query);
		}
		//changement de la localisation
		$query = "UPDATE exemplaires SET expl_location=".$id_localisation." WHERE expl_id=".$id_expl;
		pmb_mysql_query($query);
	}

	//retourne le no de transfert à partir de son no d'exemplaire
	public function _explcb_2_transid($cbEx, $etat, $sens) {
		global $deflt_docs_location;
		global $transferts_statut_transferts;

		//on recupere l'id de l'exemplaire*
		if($sens) {
			$loc_req= " AND num_location_dest=".$deflt_docs_location ;
		} else {
			$loc_req= " AND num_location_source=".$deflt_docs_location ;
		}
		$rqt = "SELECT id_transfert
					FROM transferts
						INNER JOIN transferts_demande ON id_transfert=num_transfert
						INNER JOIN exemplaires ON num_expl=expl_id
					WHERE etat_transfert=0 AND expl_cb='".$cbEx."' AND etat_demande=".$etat. " $loc_req " ;
		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res)) {
			return pmb_mysql_result( $res, 0 );
		}elseif($etat == 1) {
			// pour l'envoi seulement, chercher si cet exemplaire peut convenir à une demande de transfert de la meme notice
			// Cela évite d'envoyer l'exemplaire rangé, alors que celui-ci est dans la main...

			$rqt = "SELECT expl_notice, expl_bulletin, expl_id, expl_statut, transfert_flag
					FROM exemplaires
					INNER JOIN docs_statut ON expl_statut=idstatut WHERE expl_cb='".$cbEx."'";
			$res = pmb_mysql_query( $rqt );
			$expl = pmb_mysql_fetch_object($res);

			// on verifie que le transfert est autorisé
			if ($expl->transfert_flag==0)	return 0;

			// on verifie que pas en pret
			$rqt = "SELECT COUNT(1) FROM pret WHERE pret_idexpl=".$expl->expl_id;
			$res = pmb_mysql_query( $rqt );
			if (pmb_mysql_result( $res, 0 ) )return 0;

			// on verifie que pas reservé, validé
			$rqt = "SELECT COUNT(1) FROM resa WHERE resa_cb='".$cbEx."' and resa_confirmee=1";
			$res = pmb_mysql_query( $rqt );
			if (pmb_mysql_result( $res, 0 ) )return 0;

			$rqt = "SELECT id_transfert, id_transfert_demande, num_expl as origine_num_expl
				FROM transferts	INNER JOIN transferts_demande ON id_transfert=num_transfert
				WHERE etat_transfert=0 and
				num_notice=".$expl->expl_notice." and
				num_bulletin=".$expl->expl_bulletin." and
				etat_demande=".$etat. " $loc_req " ;
			$res = pmb_mysql_query( $rqt );
			if (pmb_mysql_num_rows($res)){
				$row = pmb_mysql_fetch_object($res);

				// restauration du statut de l'exemplaire demandé à l'origine, puisqu'il reste en rayon
				$rqt = "UPDATE exemplaires SET expl_statut=transfert_statut_origine WHERE expl_id=".$row->origine_num_expl;
				pmb_mysql_query( $rqt );

				// remplacement de l'exemplaire demandé à l'origine, par celui qui va partir
				$rqt = "UPDATE transferts_demande SET num_expl='".$expl->expl_id."' WHERE id_transfert_demande=".$row->id_transfert_demande;
				pmb_mysql_query( $rqt );
				// Sauvegarde du statut de celui qui va partir
				$rqt = "UPDATE exemplaires SET transfert_statut_origine = expl_statut WHERE expl_id=".$expl->expl_id;
				pmb_mysql_query( $rqt );
				// Changement du statut de celui qui va partir
				$rqt = "UPDATE exemplaires SET expl_statut=".$transferts_statut_transferts." WHERE expl_id=".$expl->expl_id;
				pmb_mysql_query( $rqt );
				return  $row->id_transfert;
			}
		}
		return 0;
	}

	//retourne le no de transfert à partir de son no d'exemplaire
	protected function _transid_2_explcb($transId, $etat) {
		//on recupere l'id de l'exemplaire
		$rqt = "SELECT expl_cb
					FROM transferts
						INNER JOIN transferts_demande ON id_transfert=num_transfert
						INNER JOIN exemplaires ON num_expl=expl_id
					WHERE id_transfert=".$transId." AND etat_demande=".$etat;
		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res))
			return pmb_mysql_result( $res, 0 );
		else
			return 0;
	}

	//retourne le no de transfert à partir de son no d'exemplaire
	protected function _transid_2_explid($transId, $etat) {
		//on recupere l'id de l'exemplaire
		$rqt = "SELECT num_expl
					FROM transferts
						INNER JOIN transferts_demande ON id_transfert=num_transfert
					WHERE id_transfert=".$transId." AND etat_demande=".$etat;
		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res))
			return pmb_mysql_result( $res, 0 );
		else
			return 0;
	}

	//restaure le statut de l'exemplaire
	protected function _restaure_statut($idTrans) {
		//Récupération des informations d'origine
		$rqt = "SELECT statut_origine, num_expl FROM transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
					WHERE id_transfert=".$idTrans." AND sens_transfert=0";
		$res = pmb_mysql_query($rqt);
		if($res && pmb_mysql_num_rows($res)) {
			$obj_data = pmb_mysql_fetch_object($res);
			//on met à jour
			$rqt = "UPDATE exemplaires SET expl_statut=".$obj_data->statut_origine." WHERE expl_id=".$obj_data->num_expl;
			pmb_mysql_query( $rqt );
		}
	}

	//reception - la section est-elle visible sur ce site ?
	protected function _available_section($idTrans) {
	    global $deflt_docs_location;

	    $available_section=false;
	    //Récupération de la section exemplaire courante
	    $rqt = "SELECT expl_section FROM transferts, transferts_demande, exemplaires
					WHERE id_transfert=num_transfert AND num_expl=expl_id AND id_transfert=".$idTrans." AND sens_transfert=1";
	    $res = pmb_mysql_query($rqt);
	    if(pmb_mysql_num_rows($res)){
	        $obj_data = pmb_mysql_fetch_object($res);
	        $section_id = $obj_data->expl_section;
	        if($section_id){
	            //est-elle dispo ici ?
	            $rqt = 	"SELECT num_section FROM docsloc_section WHERE num_location=".$deflt_docs_location." AND num_section=".$section_id;
	            $res = pmb_mysql_query($rqt);
	            if(pmb_mysql_num_rows($res)){
	                $available_section=true;
	            }
	        }
	    }
	    return $available_section;
	}

	//restaure la section de l'exemplaire
	protected function _restaure_section($idTrans) {
		global $deflt_docs_location;

		//Récupération des informations d'origine
		$rqt = "SELECT num_expl, transfert_section_origine FROM transferts, transferts_demande, exemplaires
					WHERE id_transfert=num_transfert AND num_expl=expl_id AND id_transfert=".$idTrans." AND sens_transfert=1";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$obj_data = pmb_mysql_fetch_object($res);
			$expl_id = $obj_data->num_expl;
			$section_origine_id = $obj_data->transfert_section_origine;

			$ok_section=false;
			if($section_origine_id){
				//est-elle toujours dispo dans la loc ?
				$rqt = 	"SELECT num_section FROM docsloc_section WHERE num_location=".$deflt_docs_location." AND num_section=".$section_origine_id;
				$res = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($res)){
					$ok_section=true;
				}
			}
			if(!$ok_section){
				//on va chercher la première dispo
				$rqt = 	"SELECT idsection FROM docs_section INNER JOIN docsloc_section ON idsection=num_section WHERE num_location=".$deflt_docs_location." LIMIT 1";
				$res = pmb_mysql_query($rqt);
				$section_origine_id = pmb_mysql_result($res,0);
			}
			//on met à jour
			if($section_origine_id){
				$rqt = "UPDATE exemplaires SET expl_section=".$section_origine_id.", transfert_section_origine=".$section_origine_id." WHERE expl_id=".$expl_id;
				pmb_mysql_query( $rqt );
			}
		}
	}

	//restaure la localisation de l'exemplaire
	protected function _restaure_localisation($idTrans) {

		//Récupération des informations d'origine
		$rqt = "SELECT source, num_expl FROM transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
				WHERE id_transfert=".$idTrans." AND sens_transfert=0";
		$res = pmb_mysql_query($rqt);
		$obj_data = pmb_mysql_fetch_object($res);

		//on met à jour
		$rqt = "UPDATE exemplaires SET expl_location=".$obj_data->source." WHERE expl_id=".$obj_data->num_expl;
		pmb_mysql_query( $rqt );
/*
		$rqt = 	"UPDATE exemplaires " .
				"SET expl_location = transfert_location_origine " .
				"WHERE expl_id = " . $idExpl;

		pmb_mysql_query( $rqt );
*/
	}

	//********************************************************************************************
	// pour le retour de pret d'exemplaires
	//********************************************************************************************

	//sur un retour d'exemplaire on change la localisation
	public function retour_exemplaire_change_localisation($expl_id) {
		global $transferts_retour_change_localisation;
		global $deflt_docs_location;

		$rqt = "SELECT expl_location FROM exemplaires WHERE expl_id=".$expl_id;
		$res = pmb_mysql_query( $rqt );
		$locOri = pmb_mysql_result( $res, 0 );

		static::_change_localisation_exemplaire($expl_id, $deflt_docs_location, ($transferts_retour_change_localisation == "1"));

		$rqt = "SELECT idsection FROM exemplaires INNER JOIN docs_section ON expl_section=idsection INNER JOIN docsloc_section ON idsection=num_section
				WHERE expl_id=".$expl_id." AND num_location=".$deflt_docs_location;
		$res = pmb_mysql_query($rqt);
		if (pmb_mysql_num_rows($res)==0) {
			//la section n'existe pas pour cette localisation !
			//on cherche la premiere section dispo
			$rqt = 	"SELECT idsection FROM docs_section INNER JOIN docsloc_section ON idsection=num_section WHERE num_location=".$deflt_docs_location." LIMIT 1";
			$res = pmb_mysql_query($rqt);
			$id_section = pmb_mysql_result($res,0);
		} else
			$id_section = pmb_mysql_result($res,0);

		//changement de la localisation
		$rqt = 	"UPDATE exemplaires SET expl_section=".$id_section." WHERE expl_id=" . $expl_id;
		pmb_mysql_query($rqt);
		return $locOri;
	}

	public static function is_retour_exemplaire_loc_origine($expl_id) {
		global $deflt_docs_location;

		$rqt = "SELECT expl_location,expl_cb, transfert_location_origine,transfert_statut_origine, expl_section FROM exemplaires WHERE expl_id=".$expl_id;
		$res = pmb_mysql_query( $rqt );
		$expl = pmb_mysql_fetch_object( $res );

		$rqt = "SELECT id_transfert
					FROM transferts
						INNER JOIN transferts_demande ON id_transfert=num_transfert
						INNER JOIN exemplaires ON num_expl=expl_id
					WHERE etat_transfert=0 AND expl_cb='".$expl->expl_cb."' AND etat_demande=3  AND num_location_source=".$deflt_docs_location ;

		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res))
			return  pmb_mysql_result( $res, 0 );
		else
			return 0;


	}
	//sur un retour d'exemplaire sur sa localisation d'origine alors qu'il était localisé ailleur (par un transfert)
	// il faut donc cloturer le retour programmé et rétablir la localisation, section cet exemplaire
	public function retour_exemplaire_loc_origine($expl_id) {
		global $deflt_docs_location;

		$rqt = "SELECT expl_location,expl_cb, transfert_location_origine,transfert_statut_origine, expl_section FROM exemplaires WHERE expl_id=".$expl_id;
		$res = pmb_mysql_query( $rqt );
		$expl = pmb_mysql_fetch_object( $res );

		$rqt = "SELECT id_transfert
					FROM transferts
						INNER JOIN transferts_demande ON id_transfert=num_transfert
						INNER JOIN exemplaires ON num_expl=expl_id
					WHERE etat_transfert=0 AND expl_cb='".$expl->expl_cb."' AND etat_demande=3  AND num_location_source=".$deflt_docs_location ;

		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res))
			$idTrans= pmb_mysql_result( $res, 0 );
		else
			return 0;

		$this->enregistre_retour ( $idTrans );
		$rqt = "SELECT  location_libelle
			FROM transferts_demande,docs_location
			WHERE num_location_source=idlocation and num_transfert=".$idTrans." AND etat_demande=5";
		$res = pmb_mysql_query( $rqt );
		$value = pmb_mysql_fetch_array( $res );
		$this->new_location_libelle=$value[0];

		$num = $this->enregistre_reception_cb($expl->expl_cb, 0,0);
		//purge les restes de transfert intermédiaire...
		$rqt = "update transferts,transferts_demande, exemplaires set etat_transfert=1
				WHERE id_transfert=num_transfert and num_expl=expl_id  and etat_transfert=0 AND expl_cb='".$expl->expl_cb."' " ;
		 pmb_mysql_query( $rqt );
		return $num;
	}

	//sur un retour d'exemplaire on genere un transfert de retour
	public function retour_exemplaire_genere_transfert_retour($expl_id) {
		global $transferts_retour_etat_transfert;
		global $transferts_retour_motif_transfert;
		global $deflt_docs_location;

		//on recupere la localisation de l'exemplaire
		//elle va servir pour la destination du transfert
		$rqt = "SELECT expl_location FROM exemplaires WHERE expl_id=".$expl_id;
		$res = pmb_mysql_query( $rqt );
		$dest_id = pmb_mysql_result( $res, 0 );

		//création du transfert
		$num = $this->_creer_transfert( $expl_id, $deflt_docs_location, $dest_id, 0, '', 3, '', $transferts_retour_motif_transfert, 1, 0);

		$this->enregistre_validation($num);

		$rqt = "update exemplaires set expl_location= $deflt_docs_location  WHERE expl_id=".$expl_id;
		pmb_mysql_query( $rqt );

		if ($transferts_retour_etat_transfert == "1")
			$this->enregistre_envoi($num);
		return $num;
	}

	public function retour_exemplaire_genere_transfert_retour_origine($expl_id) {
		global $transferts_retour_etat_transfert;
		global $transferts_retour_motif_transfert;
		global $deflt_docs_location;

		$dest_id = $this->get_origine($expl_id);

		/*
		 * #103430 - 26/02/21 - Pourquoi tenir compte du paramètre nb_jours_alerte ici ?
		 * N'empêchons pas le transfert vers le site d'origine
		if($dest_id != $deflt_docs_location) {
			// l'exemplaire n'est pas a sa bonne localisation d'origine
			// Si c'est un transfert suite à une résa, le document est immédiatement transférable,
			// sinon il est en dépôt jusqu'à la date d'alerte

			$rqt = "SELECT id_transfert, sens_transfert, num_location_source, num_location_dest,expl_location
					FROM transferts, transferts_demande, exemplaires
					WHERE id_transfert=num_transfert and num_expl=expl_id  and num_expl='".$expl_id."' AND etat_demande=3 and etat_transfert=0
					AND resa_trans=0
					AND IF(origine=3, DATE_ADD(date_retour,INTERVAL - ".$transferts_nb_jours_alerte." DAY) > CURDATE(), 1)";
			$res = pmb_mysql_query( $rqt );
			if (pmb_mysql_num_rows($res)){
				// L'exemplaire reste un certain temps ici, rien a faire de plus
				return 0;
			}
		}
		*/
		$rqt_loc = "SELECT location_libelle FROM docs_location	WHERE idlocation=".$dest_id ;
		$res_loc = pmb_mysql_query( $rqt_loc );
		$obj_loc = pmb_mysql_fetch_object($res_loc);
		$this->location_libelle_origine = $obj_loc->location_libelle;

		// on cloture le transfert en cours
		$rqt = "update transferts,transferts_demande set etat_transfert=1
		WHERE id_transfert=num_transfert and etat_transfert=0 and num_expl=".$expl_id;
		pmb_mysql_query( $rqt );

		if($dest_id == $deflt_docs_location) {
			// l'exemplaire est sur son site d'origine, rien a faire de plus
			return 0;
		}
		// création du transfert pour le retourner sur son site d'origine
		$num = $this->_creer_transfert( $expl_id, $deflt_docs_location, $dest_id, 0, '', 3, '', $transferts_retour_motif_transfert, 1, 0);

		$this->enregistre_validation($num);

		if ($transferts_retour_etat_transfert == "1") {
			$this->enregistre_envoi($num);
		}
		return $num;
	}

	// memorise la loc d'origine de l'exemplaire
	public function memo_origine($expl_id) {
		$origine_id = 0;
		//on recupere la localisation de l'exemplaire
		$rqt = "SELECT trans_source_numloc FROM transferts_source WHERE trans_source_numexpl=$expl_id ";
		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res)) {
			$origine_id = pmb_mysql_result( $res, 0 );
			if($origine_id) return $origine_id;
		}
		//on recupere la localisation de l'exemplaire du premier transfert ouvert de cet exemplaire
		$rqt = "SELECT source FROM transferts,transferts_demande WHERE	id_transfert= num_transfert and etat_transfert=0 and num_expl=$expl_id order by id_transfert limit 1";
		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res)) {
			$origine_id = pmb_mysql_result( $res, 0 );
			if($origine_id){
				$rqt = "insert transferts_source SET trans_source_numloc=".$origine_id." , trans_source_numexpl=".$expl_id;
				pmb_mysql_query( $rqt );
				return $origine_id;
			}
		}
		if(!$origine_id){
			$rqt = "SELECT expl_location FROM exemplaires WHERE expl_id=".$expl_id;
			$res = pmb_mysql_query( $rqt );
			$origine_id = pmb_mysql_result($res,0);
		}
		// delete au cas ou trans_source_numloc=0
		$rqt = "DELETE FROM transferts_source WHERE trans_source_numexpl=".$expl_id ;
		pmb_mysql_query( $rqt );

		$rqt = "insert transferts_source SET trans_source_numloc=".$origine_id." , trans_source_numexpl=".$expl_id;
		pmb_mysql_query( $rqt );
		return $origine_id;
	}

	// reset la loc d'origine de l'exemplaire par $deflt_docs_location
	public function reset_origine($expl_id) {
		global $deflt_docs_location;

		$rqt = "DELETE FROM transferts_source WHERE trans_source_numexpl=".$expl_id ;
		pmb_mysql_query( $rqt );

		$rqt = "insert transferts_source SET trans_source_numloc=".$deflt_docs_location." , trans_source_numexpl=".$expl_id;
		pmb_mysql_query( $rqt );
	}

	// retourne la loc d'origine de l'exemplaire
	public function get_origine($expl_id) {

		// on teste s'il y a un transfert actif sur l'exemplaire
		$rqt = "SELECT * FROM transferts, transferts_demande WHERE id_transfert = num_transfert AND etat_transfert=0 AND num_expl=".$expl_id;
		$res = pmb_mysql_query($rqt);
		if (!pmb_mysql_num_rows($res)) {
			//On nettoie le champ exemplaire transfert_location_origine car certains cas de figure anciens ont pu générer un décalage de localisation d'origine
			pmb_mysql_query("UPDATE exemplaires SET transfert_location_origine=expl_location WHERE expl_id=".$expl_id);
			//On retourne la localisation de l'exemplaire.
			$rqt = "SELECT expl_location FROM exemplaires WHERE expl_id=".$expl_id;
			$res = pmb_mysql_query($rqt);
			return pmb_mysql_result($res, 0);
		}

		// on recupere la localisation d'origine de l'exemplaire, priorité table 'transferts_source'
		$rqt = "SELECT trans_source_numloc FROM transferts_source JOIN docs_location ON docs_location.idlocation = transferts_source.trans_source_numloc WHERE trans_source_numexpl=".$expl_id;
		$res = pmb_mysql_query($rqt);
		if (pmb_mysql_num_rows($res)) {
			$origine_id = pmb_mysql_result($res, 0);
			if($origine_id){
				return $origine_id;
			}
		}
		// on recupere la localisation d'origine de l'exemplaire
		$rqt = "SELECT transfert_location_origine FROM exemplaires JOIN docs_location ON docs_location.idlocation = exemplaires.transfert_location_origine WHERE expl_id=".$expl_id;
		$res = pmb_mysql_query($rqt);
		$origine_id =  pmb_mysql_result($res, 0);
		if($origine_id){
			return $origine_id;
		}
		// si pas de transfert_location_origine, on retourne expl_location
		$rqt = "SELECT expl_location FROM exemplaires WHERE expl_id=".$expl_id;
		$res = pmb_mysql_query($rqt);
		return pmb_mysql_result($res, 0);
	}

	//restaure la localisation apres une sauvegarde
	public function retour_exemplaire_restaure_localisation($expl_id, $loc_id) {
		$rqt = "UPDATE exemplaires SET expl_location=".$loc_id." WHERE expl_id=".$expl_id;
		pmb_mysql_query( $rqt );
	}

	//on supprime le transfert généré
	public function retour_exemplaire_supprime_transfert($expl_id, $idTrans) {
		$this->_restaure_statut($idTrans);
		$rqt = "DELETE FROM transferts WHERE id_transfert=" . $idTrans;
		pmb_mysql_query( $rqt );
		$rqt = "DELETE FROM transferts_demande WHERE num_transfert=" . $idTrans;
		pmb_mysql_query( $rqt );
	}

	//********************************************************************************************
	// pour la circulation
	//********************************************************************************************

	//enregistre la validation d'un exemplaire à partir de son cb
	public function enregistre_validation_cb($cbEx) {
		$idTrans = $this->_explcb_2_transid ( $cbEx, 0,0 );
		if ($idTrans != 0) {
			$this->enregistre_validation ( $idTrans );
			$rqt = "SELECT  location_libelle
				FROM transferts_demande,docs_location
				WHERE num_location_dest=idlocation and num_transfert=".$idTrans." AND etat_demande=1";
			$res = pmb_mysql_query( $rqt );
			$value = pmb_mysql_fetch_array( $res );
			$this->new_location_libelle=$value[0];
			return $cbEx;
		} else
			return false;
	}

	//enregistre la validation d'une liste de transferts
	public function enregistre_validation($listeTransferts) {
		global $transferts_statut_validation;

		$tabTrans = explode ( ",", $listeTransferts );
		foreach ( $tabTrans as $transId ) {
			//pour chacun des transferts sélectionnés

			//on met a jour l'etat de la demande => on passe en validé
			$rqt = "UPDATE transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
					SET etat_demande=1, date_visualisee = NOW()
					WHERE id_transfert=".$transId." AND etat_demande=0 ";
			pmb_mysql_query( $rqt );

			//on recupere l'id de l'exemplaire
			$idExpl = $this->_transid_2_explid ( $transId, 1 );

			//on change le statut de l'exemplaire
			if ($transferts_statut_validation) {
				$this->_change_statut_exemplaire( $idExpl, $transferts_statut_validation);
			}

			if($this->validation_send_event) {
				// Publication d'un évenement (transfert validé)
				$evt_handler = events_handler::get_instance();
				$event = new event_transfert("transfert", "validated");
				$event->set_id_transfert($transId);
				$evt_handler->send($event);
			}
		} // foreach

	}

	//enregistre le refus sur une liste de transfert
	public function enregistre_refus($listeTransferts, $motif) {
		$tabTrans = explode ( ",", $listeTransferts );
		foreach ( $tabTrans as $transId ) {
			//pour chacun des transferts sélectionnés

			//on met a jour l'etat de la demande => on passe en validé
			$rqt = "UPDATE transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
					SET etat_demande = 4, date_visualisee = NOW(), motif_refus = '".$motif."'
					WHERE id_transfert=".$transId." AND etat_demande<2 ";
			pmb_mysql_query( $rqt );

			//on restaure le statut au cas ou il aurais été modifié...
			$this->_restaure_statut($transId);
		}
	}

	//valide l'envoi d'un exemplaire
	public function enregistre_envoi_cb($cbEx) {
		$idTrans = $this->_explcb_2_transid ( $cbEx, 1 ,0);
		if ($idTrans != 0) {
			$this->enregistre_envoi ( $idTrans );
			$rqt = "SELECT  location_libelle
				FROM transferts_demande,docs_location
				WHERE num_location_dest=idlocation and num_transfert=".$idTrans." AND etat_demande=2";
			$res = pmb_mysql_query( $rqt );
			$value = pmb_mysql_fetch_array( $res );
			$this->new_location_libelle=$value[0];
			return $cbEx;
		} else
			return false;
	}

	//valide l'envoi d'une liste de transferts
	public function enregistre_envoi($listeTransferts) {
		global $transferts_statut_transferts;
		global $transferts_validation_actif;
		global $transferts_statut_validation, $PMBuserid;

		$tabTrans = explode ( ",", $listeTransferts );

		foreach ( $tabTrans as $transId ) {
			//pour chacun des transferts sélectionnés
			$idExpl = $this->_transid_2_explid ( $transId, 1 ,0);

			if ( ($transferts_validation_actif == "1") && ($transferts_statut_validation != "0") )
				//si la validation est active et le changement de statut activé
				//on restaure le statut sauvegardé
				$this->_restaure_statut($transId);

			//on change le statut et on le sauvegarde
			$this->_change_statut_exemplaire($idExpl, $transferts_statut_transferts);

			//on met a jour l'etat de la demande => on passe en envoyé
			$rqt = "UPDATE transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
					SET etat_demande=2, date_envoyee=NOW(), transfert_send_user_num= '".$PMBuserid."'
					WHERE id_transfert=".$transId." AND etat_demande = 1";
			pmb_mysql_query( $rqt );
		}
	}

	//effectue la reception d'un exemplaire
	public function enregistre_reception_cb($cbEx, $idStatut, $idSection,&$info=array()) {
		$idTrans = $this->_explcb_2_transid ( $cbEx, 2 ,1);
		if ($idTrans != 0) {
			$this->enregistre_reception ( $idTrans, $idStatut, $idSection,$info );
			return $idTrans;
		} else
			return false;
	}

	/*Autorise ou pas le prêt, et si transfert, on valide la reception
	 retourne:
		 1: Prêt interdit
		 2: prêt forcable
		 0: prêt ok
	*/
	public function check_pret($cbEx,$force=0) {
		global $transferts_pret_statut_transfert,$msg;
		global $deflt_docs_location;

		$this->check_pret_error_message='';
		//on recupere l'id de l'exemplaire
		$rqt = "SELECT id_transfert, sens_transfert, num_location_source, num_location_dest
				FROM transferts, transferts_demande, exemplaires
				WHERE id_transfert=num_transfert and num_expl=expl_id  and expl_cb='".$cbEx."' AND etat_demande=2" ;
		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_num_rows($res)){
			$obj_data = pmb_mysql_fetch_object($res);
			$rqt_loc = "SELECT  location_libelle FROM transferts_demande,docs_location	WHERE num_location_source=idlocation and num_transfert=".$obj_data->id_transfert;
			$res_loc = pmb_mysql_query( $rqt_loc );
			$value = pmb_mysql_fetch_array( $res_loc );
			$location_source_libelle=$value[0];
			$rqt_loc = "SELECT  location_libelle FROM transferts_demande,docs_location	WHERE num_location_dest=idlocation and num_transfert=".$obj_data->id_transfert;
			$res_loc = pmb_mysql_query( $rqt_loc );
			$value = pmb_mysql_fetch_array( $res_loc );
			$location_dest_libelle=$value[0];

			if(!$obj_data->sens_transfert && ($deflt_docs_location == $obj_data->num_location_source)) {
				// c'est un envoi, coté du propriétaire: l'exemplaire aurai dû partir...
				if(!$transferts_pret_statut_transfert) {
					// prêt interdit
					$this->check_pret_error_message=str_replace("!!dest_location!!",$location_dest_libelle, $msg["transferts_check_pret_erreur_envoi"]);
					return 1;
				}
				else {
					// forçable en prêt, on le laisse en transfert ?
					$this->check_pret_error_message=str_replace("!!dest_location!!",$location_dest_libelle, $msg["transferts_check_pret_erreur_envoi"]);
					return 2;
				}
			}
			if(!$obj_data->sens_transfert && ($deflt_docs_location == $obj_data->num_location_dest)) {
				// c'est un envoi, coté destinataire: l'exemplaire aurai dû être réceptionné avant un prêt...
				if($force) {
					$res_rcp = $this->enregistre_reception_cb($cbEx, 0, 0);
					$this->_restaure_statut($obj_data->id_transfert);
					if ($res_rcp==false) return 1;
				} else {
					$this->check_pret_error_message=str_replace("!!source_location!!",$location_source_libelle, $msg["transferts_check_pret_erreur_reception"]);
					return 2;
				}
			}
			if($obj_data->sens_transfert && ($deflt_docs_location == $obj_data->num_location_source)) {
				// c'est un retour, coté destinataire: l'exemplaire aurai du être retourné et non prêté...
				if(!$transferts_pret_statut_transfert) {
					// prêt interdit
					$this->check_pret_error_message=str_replace("!!dest_location!!",$location_source_libelle, $msg["transferts_check_pret_erreur_envoi"]);
					return 1;
				}
				else {
					// forçable en prêt, on le laisse en transfert ?
					$this->check_pret_error_message=str_replace("!!dest_location!!",$location_source_libelle, $msg["transferts_check_pret_erreur_envoi"]);
					return 2;
				}
			}
			if($obj_data->sens_transfert && ($deflt_docs_location == $obj_data->num_location_dest)) {
				// c'est un retour, coté du propriétaire: l'exemplaire aurai dû être réceptionné avant un prêt...
				if($force) {
					$res_rcp = $this->enregistre_reception_cb($cbEx, 0, 0);
					$this->_restaure_statut($obj_data->id_transfert);
					if ($res_rcp==false) return 1;
				} else {
					$this->check_pret_error_message=str_replace("!!source_location!!",$location_dest_libelle, $msg["transferts_check_pret_erreur_reception"]);
					return 2;
				}
			}
		}
		return 0;
	}

	public function resa_is_first_availability($resa_id_notice=0, $resa_id_bulletin=0, $resa_rank=0) {
		//on regarde si les rangs précédents sont validés
		$is_first_availability = true;
		$ranks = recupere_rangs($resa_id_notice, $resa_id_bulletin/*, $this->filters['removal_location']*/);
		if(!empty($ranks)) {
			$ranks = array_slice($ranks, 0, $resa_rank, true);
			foreach ($ranks as $id_resa=>$rank) {
				if($rank < $resa_rank) {
					if($is_first_availability && empty(reservation::get_cb_from_id($id_resa))) {
						$is_first_availability = false;
					}
				}
			}
		}
		return $is_first_availability;
	}

	//effectue la reception d'une liste de transferts
	public function enregistre_reception($listeTransferts, $idStatut, $listeSections,&$info=array()) {
		global $deflt_docs_location;

		$tabTrans = explode ( ",", $listeTransferts );
		$tabSections =  explode ( ",", $listeSections );

		$idSection = current($tabSections);

		$nb=0;
		foreach ( $tabTrans as $transId ) {
			//on recupere l'id de l'exemplaire
			$noEx = $this->_transid_2_explid ( $transId, 2 );
			$info[$nb]=array();
			//le sens du transfert
			$rqt = "SELECT sens_transfert, type_transfert, origine, origine_comp, motif
					FROM transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
					WHERE id_transfert=".$transId." AND etat_demande = 2";
			$res = pmb_mysql_query( $rqt );
			$value = pmb_mysql_fetch_array( $res );
			$sensTrans = $value[0];
			$typeTrans = $value[1];
			$origine = $value[2];
			$origineComp = $value[3];
			$info[$nb]["motif"] = $value[4];

			if ($sensTrans == 1) {
				//c'est un retour !
				//on cloture le transfert
				$rqt = 	"UPDATE transferts SET etat_transfert=1 WHERE id_transfert=".$transId;
				pmb_mysql_query( $rqt );

				if(!exemplaire::purge_ghost($noEx)){
					if ($typeTrans == 1) {
						//si c'est un aller/retour
						//on restaure la localisation sauvegardé de l'exemplaire
						$this->_restaure_localisation($transId);
					}
					if($idSection){
						$rqt = "UPDATE exemplaires INNER JOIN transferts_demande ON num_expl=expl_id INNER JOIN transferts ON id_transfert=num_transfert
									SET expl_section=".$idSection.", transfert_section_origine=".$idSection."
									WHERE id_transfert=".$transId." AND etat_demande = 2";
						pmb_mysql_query( $rqt );
					} else {
					    //Section après réception : section identique
					    //on conserve la section - est-elle visible ici ?
					    if(!$this->_available_section($transId)) {
    						//sinon on restaure le section sauvegardé de l'exemplaire
    						$this->_restaure_section($transId);
                        }
					}
					if($idStatut) {
						//on met à jour le statut et la localisation de l'exemplaire
						$rqt = "UPDATE exemplaires INNER JOIN transferts_demande ON num_expl=expl_id INNER JOIN transferts ON id_transfert=num_transfert
								SET expl_statut=".$idStatut.", transfert_statut_origine=".$idStatut.", expl_location = num_location_dest
								WHERE id_transfert=".$transId." AND etat_demande = 2";
						pmb_mysql_query( $rqt );
					} else {
						//on restaure le statut sauvegardé de l'exemplaire
						$this->_restaure_statut($transId);
					}

					//Même en retour de transfert, on teste s'il y a une résa à satisfaire
					//on recupere le cb
					$rqt = "SELECT expl_cb FROM exemplaires WHERE expl_id=".$noEx;
					$res = pmb_mysql_query($rqt);
					$value = pmb_mysql_fetch_array($res);
					$explcb = $value[0];
					//résa validable ?
					$id_resa_validee = affecte_cb($explcb,0);
					if ($id_resa_validee) {
						//on genere la lettre de confirmation
						alert_empr_resa($id_resa_validee);
					}
				}

			} else {
				//c'est un transfert

				// aller simple ?
				if ($typeTrans == 0) {
					//on cloture le transfert => pas de gestion du retour
					$rqt = 	"UPDATE transferts SET etat_transfert=1 WHERE id_transfert=".$transId;
					pmb_mysql_query( $rqt );

				} else {
					//c'est l'aller donc
					if ($origine==4 ) {
						//c'est un transfert suite a une resa donc
						//on recupere le cb pour
						$explcb = $this->_transid_2_explcb($transId,2);
					}
					$id_section = $idSection;
					if ($idSection==0) {
						//chercher la meme section dans le nouveau site
						$rqt = "SELECT idsection
								FROM exemplaires INNER JOIN docs_section ON expl_section=idsection INNER JOIN docsloc_section ON idsection=num_section
								WHERE expl_id=".$noEx." AND num_location=".$deflt_docs_location;
						$res = pmb_mysql_query($rqt);
						if (pmb_mysql_num_rows($res)==0) {
							//la section n'existe pas pour cette localisation !
							//on cherche la premiere section dispo
							$rqt = "SELECT idsection
									FROM docs_section INNER JOIN docsloc_section ON idsection=num_section
									WHERE num_location=".$deflt_docs_location." LIMIT 1";
							$res = pmb_mysql_query($rqt);
							$id_section = pmb_mysql_result($res,0);
						} else
							$id_section = pmb_mysql_result($res,0);
					}

					$rqt = "UPDATE exemplaires INNER JOIN transferts_demande ON num_expl=expl_id INNER JOIN transferts ON id_transfert=num_transfert
							SET expl_section=".$id_section."
							WHERE id_transfert=".$transId." AND etat_demande = 2";
					pmb_mysql_query( $rqt );

				} //fin du else de if ($typeTrans == 0)

				//on met à jour le statut et la localisation de l'exemplaire
				if($idStatut) {
					$rqt = "UPDATE exemplaires INNER JOIN transferts_demande ON num_expl=expl_id INNER JOIN transferts ON id_transfert=num_transfert
							SET expl_statut=".$idStatut.", expl_location = num_location_dest
							WHERE id_transfert=".$transId." AND etat_demande = 2";
					pmb_mysql_query( $rqt );
				}else {
					//on restaure le statut sauvegardé de l'exemplaire
					$this->_restaure_statut($transId);
				}
				// Traitement de la résa
				if ($origine==4 && $typeTrans) {
					//c'est un transfert suite a une resa donc

					//on verifie qu'elle soit en rang 1
					$rank = 1;
					$is_first_availability = false;
					if($origineComp) {
						$rqt = "SELECT resa_idnotice, resa_idbulletin, resa_idempr
								FROM resa WHERE id_resa='".$origineComp."'";
						$res = pmb_mysql_query( $rqt );
						if(pmb_mysql_num_rows($res)) {
							$row = pmb_mysql_fetch_object($res);
							$rank = recupere_rang($row->resa_idempr, $row->resa_idnotice, $row->resa_idbulletin);
							$rank = intval($rank);
							$is_first_availability = $this->resa_is_first_availability($row->resa_idnotice, $row->resa_idbulletin, $rank);
						}
					}
					//on valide la resa si elle est prioritaire
					if($rank == 1 || $is_first_availability) {
					//valider la resa
						$id_resa_validee = affecte_cb($explcb,$origineComp);
						//on genere la lettre de confirmation
						alert_empr_resa($id_resa_validee);
					}
				}

			} //fin du else de if ($sensTrans == 0)

			//on met a jour l'etat de la demande => on passe en receptionné et terminer
			$rqt = "UPDATE transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
					SET etat_demande=3, date_reception=NOW()
					WHERE id_transfert=".$transId." AND etat_demande = 2";
			pmb_mysql_query( $rqt );

			//on passe à la section suivante
			$idSection = next($tabSections);

			$nb++;
		} //fin du while
	}

	//lance le retour d'un exemplaire
	public function enregistre_retour_cb($cbEx) {
		$idTrans = $this->_explcb_2_transid ( $cbEx, 3, 1 );
		if ($idTrans != 0) {
			$this->enregistre_retour ( $idTrans );
			$rqt = "SELECT  location_libelle
				FROM transferts_demande,docs_location
				WHERE num_location_source=idlocation and num_transfert=".$idTrans." AND etat_demande=5";
			$res = pmb_mysql_query( $rqt );
			$value = pmb_mysql_fetch_array( $res );
			$this->new_location_libelle=$value[0];
			return $cbEx;
		} else
			return false;

	}

	//effectue le retour d'une liste de transferts
	public function enregistre_retour($listeTransferts) {
		global $transferts_statut_transferts;
		$tabTrans = explode ( ",", $listeTransferts );
		foreach ( $tabTrans as $transId ) {
			//on met a jour l'etat de la demande => on passe en receptionné et terminer
			$rqt = "UPDATE transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
					SET etat_demande=5
					WHERE id_transfert=".$transId." AND etat_demande=3";
			pmb_mysql_query( $rqt );

			//on recupere les infos de la demande de l'aller
			$rqt = "SELECT num_location_source, num_location_dest, num_expl, statut_origine, section_origine
					FROM transferts_demande
					WHERE num_transfert=".$transId." AND etat_demande=5";
			$res = pmb_mysql_query( $rqt );
			$value = pmb_mysql_fetch_array( $res );

			//on insert l'information d'envoi du retour
			$rqt = "INSERT INTO transferts_demande (num_transfert, date_creation, sens_transfert, num_location_source,
						num_location_dest, num_expl, etat_demande, date_visualisee, date_envoyee, statut_origine, section_origine) VALUES (".
						$transId.", NOW(), 1, $value[1], $value[0], $value[2], 2, NOW(), NOW(), $value[3], $value[4])";
			pmb_mysql_query( $rqt );

			//on met à jour le statut de l'exemplaire avec l'etat défini pour la validation
			$rqt = "UPDATE exemplaires SET expl_statut=".$transferts_statut_transferts."
					WHERE expl_id=".$value[2];
			pmb_mysql_query( $rqt );
		}
	}

	//change la date de retour d'un transfert
	static function change_date_retour($idTransfert,$date_retour) {
		$rqt = "UPDATE transferts SET date_retour='".$date_retour."' WHERE id_transfert=".$idTransfert;
		pmb_mysql_query( $rqt );
	}

	//cloture un ou plusieurs transferts
	public function cloture_transferts($listeTransferts) {
		$tabTrans = explode ( ",", $listeTransferts );

		foreach ( $tabTrans as $transId ) {
			//on cloture le transfert
			$rqt = 	"UPDATE transferts SET etat_transfert=1 WHERE id_transfert=".$transId;
			pmb_mysql_query( $rqt );
		}
	}
/*
	function ajoute_demande($transId, $source, $motif, $dateRetour) {
		global $deflt_docs_location;
		global $transferts_validation_actif;

		//on met a jour l'etat de la demande => on passe en refus traité et la date de retour souhaitée
		$rqt = "UPDATE transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
				SET etat_demande=6, date_retour='".$dateRetour."', motif='$motif'
				WHERE id_transfert=".$transId." AND etat_demande=4";
		pmb_mysql_query( $rqt );

		//recuperation des informations pour déterminer le nouveau no d'exemplaire
		$rqt = "SELECT num_notice, num_bulletin
				FROM transferts
				WHERE id_transfert=".$transId;
		$res = pmb_mysql_query( $rqt );
		$value = pmb_mysql_fetch_array( $res );

		//on a besoin du no d'exemplaire pour la source donnée
		$rqt = "SELECT expl_id
				FROM exemplaires
				WHERE expl_notice=".$value[0]." AND expl_bulletin=".$value[1]." AND expl_location=".$source;
		$id_expl = pmb_mysql_result(pmb_mysql_query($rqt),0);

		//la table transferts_demande
		$rqt = "INSERT INTO transferts_demande (num_transfert, date_creation, sens_transfert, num_location_source, num_location_dest, num_expl, etat_demande)
				VALUES (".$transId.", NOW(), 0, ".$source.", ".$deflt_docs_location.", ".$id_expl.", 0)";
		pmb_mysql_query( $rqt );
		// $num pas initialisé ?????????????
		if ($transferts_validation_actif == "0")
			//pas d'étape de validation => etape d'envoi direct
			$this->enregistre_validation($num);

	}
	*/
	public function ajoute_demande($transId, $id_expl, $motif, $dateRetour) {
		global $deflt_docs_location;

		$rqt = "SELECT expl_location from exemplaires WHERE expl_id=".$id_expl;
		$res=pmb_mysql_query( $rqt );
		$value = pmb_mysql_fetch_object( $res );
		$source=$value->expl_location;

		//on met a jour l'etat de la demande => on passe en refus traité et la date de retour souhaitée
		$rqt = "UPDATE transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
				SET etat_demande=6, source= ".$source.", date_retour='".$dateRetour."', motif='$motif'
				WHERE id_transfert=".$transId." AND etat_demande=4";
		pmb_mysql_query( $rqt );

		//la table transferts_demande
		$rqt = "INSERT INTO transferts_demande (num_transfert, date_creation, sens_transfert, num_location_source, num_location_dest, num_expl, etat_demande)
				VALUES (".$transId.", NOW(), 0, ".$source.", ".$deflt_docs_location.", ".$id_expl.", 0)";
		pmb_mysql_query( $rqt );



	}
	//********************************************************************************************
	// pour les réservations
	//********************************************************************************************

	public function transfert_pour_resa($cb_expl,$dest,$resa_id) {
		global $transferts_resa_etat_transfert;
		global $transferts_resa_motif_transfert;
		global $transferts_nb_jours_pret_defaut;
		global $transferts_pret_demande_statut,$transferts_statut_validation,$PMBuserid;

		//récuperation des infos de l'exemplaire
		$rqt = "SELECT expl_id, expl_location FROM exemplaires WHERE expl_cb='".$cb_expl."'";
		$res = pmb_mysql_query($rqt);
		$obj_expl = pmb_mysql_fetch_object($res);

		//generation de la date de retour par défaut
		$date_retour = mktime(0, 0, 0, date("m"), date("d")+$transferts_nb_jours_pret_defaut, date("Y"));
		$date_retour_mysql = date("Y-m-d", $date_retour);

		//génération du transfert
		$num = $this->_creer_transfert( $obj_expl->expl_id, $obj_expl->expl_location, $dest, 1, $date_retour_mysql, 4, $resa_id, $transferts_resa_motif_transfert);

		// lier la résa au transfert
		$rqt = "UPDATE transferts_demande SET resa_trans=$resa_id WHERE num_transfert=$num and num_expl='".$obj_expl->expl_id."' ";
		pmb_mysql_query( $rqt );

		$rqt = "UPDATE transferts SET transfert_ask_user_num='".$PMBuserid."' WHERE id_transfert=$num ";
		pmb_mysql_query( $rqt );

		//mémo de resa archive
		$rqt = "SELECT resa_arc FROM resa WHERE id_resa='".$resa_id."'";
		$res = pmb_mysql_query($rqt);
		$obj_resa = pmb_mysql_fetch_object($res);

		$rqt = "UPDATE transferts_demande SET resa_arc_trans=".$obj_resa->resa_arc." WHERE num_transfert=$num and num_expl='".$obj_expl->expl_id."' ";
		pmb_mysql_query( $rqt );

		if ($transferts_resa_etat_transfert == "1")
			//on valide
			$this->enregistre_validation($num);
		elseif($transferts_pret_demande_statut){
			if ($transferts_statut_validation) {
				$this->_change_statut_exemplaire( $obj_expl->expl_id, $transferts_statut_validation);
			}
		}

		return $num;
	}

	//********************************************************************************************
	// pour l'affichage des exemplaires
	//********************************************************************************************

	// dit si un exemplaire est transférable.
	public static function est_transferable($expl) {
		global $deflt_docs_location;

		$rqt = "SELECT expl_location, transfert_location_origine, transfert_flag
				FROM exemplaires INNER JOIN docs_statut ON expl_statut=idstatut
				WHERE expl_id=".$expl;

		$res = pmb_mysql_query($rqt) or die (pmb_mysql_error()."<br /><br />".$rqt);
		$value = pmb_mysql_fetch_array($res);
		$loc_expl = $value[0];
// 		$loc_expl_ori = $value[1];
		$trans_aut = $value[2];

		//on verifie que le pret est autorisé
		if ($trans_aut==0)	return false;

		// si l'exemplaire est ici: pas transférable
		if ($deflt_docs_location == $loc_expl)	return false;
/*
		//on verifie que l'exemplaire n'est pas déja sur le site de l'utilisateur
		if ($deflt_docs_location != $loc_expl) {

			//si les transferts d'exemplaires deja transféré ne sont pas autorisés
			if ($transferts_transfert_transfere_actif == "0") {
				//si ce n'est pas la localisation d'origine
				if ($loc_expl != $loc_expl_ori) {
					//si la localisation d'origine n'a pas la valeure par défaut(0)
					if ($loc_expl_ori != 0)
						return false;
				}
			}
		} else
			return false;
*/
		$rqt = "SELECT COUNT(1) FROM pret WHERE pret_idexpl=".$expl;
		$res = pmb_mysql_query( $rqt );
		if (pmb_mysql_result( $res, 0 ) )return false;

		//on verifie qu'un transfert n'est pas déja demande
		$rqt = "SELECT COUNT(1)
				FROM transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
				WHERE etat_transfert=0 AND num_expl=".$expl." AND etat_demande<4";
		$res = pmb_mysql_query( $rqt );
		$nbTrans = pmb_mysql_result( $res, 0 );

		if ($nbTrans != 0)
			return false;

		return true;
	}

	// dit si un exemplaire est doit faire l'objet d'un retour
	public function est_retournable($expl) {
		global $deflt_docs_location;

		$dest_id = $this->get_origine($expl);

		$rqt_loc = "SELECT  location_libelle FROM docs_location	WHERE idlocation=".$dest_id ;
		$res_loc = pmb_mysql_query( $rqt_loc );
		$obj_loc = pmb_mysql_fetch_object($res_loc);
		$this->location_libelle_source=$obj_loc->location_libelle;
		$this->location_origine=$dest_id;
		if($deflt_docs_location != $dest_id)return(true);

	}

	//genere une demande de transfert
	public function creer_transfert_catalogue($expl_id, $dest_id, $date_ret, $motif,$ask_date='') {
		global $transferts_validation_actif;

		if(!$expl_id) return 0;
		//on recupere les informations manquantes sur l'exemplaire
		$rqt = "SELECT expl_location FROM exemplaires WHERE expl_id=".$expl_id;
		$res = pmb_mysql_query( $rqt );
		$src_id = pmb_mysql_result($res,0);


		//on creer le transfert
		$num = $this->_creer_transfert( $expl_id, $src_id, $dest_id, 1, $date_ret, 3, '', $motif, 0, 0 ,$ask_date);

		if ($transferts_validation_actif == "0")
			//pas d'étape de validation => etape d'envoi
			$this->enregistre_validation($num);
		return $num;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_num_notice() {
		return $this->num_notice;
	}

	public function get_num_bulletin() {
		return $this->num_bulletin;
	}

	public function get_type_transfert() {
		return $this->type_transfert;
	}

	public function get_etat_transfert() {
		return $this->etat_transfert;
	}

	public function get_formatted_date_creation() {
		return $this->formatted_date_creation;
	}

	public function get_date_retour() {
		return $this->date_retour;
	}

	public function get_formatted_date_retour() {
		return $this->formatted_date_retour;
	}

	public function get_motif() {
		return $this->motif;
	}

	public function get_transfert_ask_user_num() {
		return $this->transfert_ask_user_num;
	}

	public function get_transfert_send_user_num() {
		return $this->transfert_send_user_num;
	}

	public function get_transfert_ask_date() {
		return $this->transfert_ask_date;
	}

	public function get_transfert_ask_formatted_date() {
		return $this->transfert_ask_formatted_date;
	}

	public function get_exemplaire() {
		return $this->exemplaire;
	}

	public function get_transfert_demande() {
		return $this->transfert_demande;
	}

	public function get_location_origine() {
		return $this->location_origine;
	}

	public function get_location_libelle_origine() {
		return $this->location_libelle_origine;
	}
}

?>