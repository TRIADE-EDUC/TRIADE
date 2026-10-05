<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transferts.class.php,v 1.4 2021/02/25 13:27:32 dgoron Exp $

if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" )) die ( "no access" );

global $include_path;
require_once ("$include_path/templates/transferts.tpl.php");

class transferts {
		
	// constructeur
	public function __construct() {
	}
		
	public static function check_loc_retrait_resas($new_choix_lieu_opac) {
		//Si le sélecteur "Choix pour la réservation" a changé de valeur, et qu'on est passé en "Choix de la localisation",
		// il faut valoriser le champ resa_loc_retrait s'il est vide, pour les réservations déjà créées
		if ($new_choix_lieu_opac == 1) { //Choix pour la réservation
			$query = "SELECT valeur_param FROM parametres WHERE type_param='transferts' AND sstype_param='choix_lieu_opac'";
			$res = pmb_mysql_query($query);
			$old_choix_lieu_opac = pmb_mysql_result($res, 0);
			if ($old_choix_lieu_opac != $new_choix_lieu_opac) {
				//On va donc mettre à jour avec la loc du lecteur
				$query = "SELECT * FROM resa WHERE resa_loc_retrait = 0";
				$res = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($res)) {
					while ($row = pmb_mysql_fetch_object($res)) {
						$query_empr = "SELECT empr_location FROM empr WHERE id_empr=".$row->resa_idempr;
						$res_empr = pmb_mysql_query($query_empr);
						$loc_empr = pmb_mysql_result($res_empr, 0);
						$query_update = "UPDATE resa SET resa_loc_retrait=".$loc_empr." WHERE id_resa = ".$row->id_resa;
						pmb_mysql_query($query_update);
						$query_update = "UPDATE resa_archive, resa SET resarc_loc_retrait=".$loc_empr." WHERE id_resa = ".$row->id_resa." AND resa_arc = resarc_id";
						pmb_mysql_query($query_update);
					}
				}
			}
		}
	}
	
	//affiche le tableau des localisations pour modifier l'ordre
	public static function get_display_location_order() {
		//les templates
		global $transferts_admin_modif_ordre_loc;
		global $transferts_admin_modif_ordre_loc_ligne;
		global $transferts_admin_modif_ordre_loc_ligne_flBas;
		global $transferts_admin_modif_ordre_loc_ligne_flHaut;
		
		//on genere le tableau des sites
		$rqt = "SELECT idlocation,location_libelle,transfert_ordre FROM docs_location ORDER BY transfert_ordre, idLocation";
		$res = pmb_mysql_query($rqt);
		
		//le nb de lignes
		$nb=0;
		$nbTotal = pmb_mysql_num_rows($res);
		$tmpString = "";
		
		while ($value = pmb_mysql_fetch_array($res)) {
			
			//la classe de la ligne
			if ($nb % 2)
				$tmpLigne = str_replace('!!class_ligne!!', "even", $transferts_admin_modif_ordre_loc_ligne);
				else
					$tmpLigne = str_replace('!!class_ligne!!', "odd", $transferts_admin_modif_ordre_loc_ligne);
					
					//le libellé du site
					$tmpLigne = str_replace('!!lib_site!!', $value[1], $tmpLigne);
					
					if ($nb==0) {
						//on est sur la premiere ligne
						if ($nbTotal>1) {
							//on a plus d'une ligne
							$tmpLigne = str_replace("!!fl_bas!!",str_replace("!!idSite!!",$value[0],$transferts_admin_modif_ordre_loc_ligne_flBas),$tmpLigne);
						} else {
							$tmpLigne = str_replace("!!fl_bas!!","",$tmpLigne);
						}
						$tmpLigne = str_replace("!!fl_haut!!","",$tmpLigne);
						
					} else {
						if ($nb==($nbTotal-1)) {
							//on est sur la derniere ligne
							$tmpLigne = str_replace("!!fl_bas!!","",$tmpLigne);
							$tmpLigne = str_replace("!!fl_haut!!",str_replace("!!idSite!!",$value[0],$transferts_admin_modif_ordre_loc_ligne_flHaut),$tmpLigne);
						} else {
							//on est sur ligne du milieu
							$tmpLigne = str_replace("!!fl_bas!!",str_replace("!!idSite!!",$value[0],$transferts_admin_modif_ordre_loc_ligne_flBas),$tmpLigne);
							$tmpLigne = str_replace("!!fl_haut!!",str_replace("!!idSite!!",$value[0],$transferts_admin_modif_ordre_loc_ligne_flHaut),$tmpLigne);
						}
					}
					
					//on verifie que l'ordre est respecté
					if ($value[2]!=$nb) {
						//on met a jour le no d'ordre
						$rqt = "UPDATE docs_location SET transfert_ordre=".$nb." WHERE idlocation=".$value[0];
						pmb_mysql_query($rqt);
					}
					
					$nb++;
					$tmpString .= $tmpLigne;
					
		}
		
		//on insere la liste dans le template global
		$tmpString = str_replace("!!liste_sites!!",$tmpString,$transferts_admin_modif_ordre_loc);
		
		return $tmpString;
	}
	
	//change l'ordre de la localisation
	public static function save_location_order($sens, $id) {
		
		//on recuper l'ordre
		$rqt = "SELECT transfert_ordre FROM docs_location WHERE idlocation=".$id;
		$ordreBase = pmb_mysql_fetch_array( pmb_mysql_query( $rqt ) );
		
		//on recupere l'id de la 2eme localisation
		$rqt = "SELECT idLocation FROM docs_location WHERE transfert_ordre=".($ordreBase[0] + $sens);
		$idSecond = pmb_mysql_fetch_array( pmb_mysql_query( $rqt ) );
		
		//on met a jour l'ordre
		$rqt = "UPDATE docs_location SET transfert_ordre=".($ordreBase[0] + $sens)." WHERE idLocation=".$id;
		pmb_mysql_query( $rqt );
		
		//puis celui du 2eme
		$rqt = "UPDATE docs_location SET transfert_ordre=".$ordreBase[0]." WHERE idLocation=".$idSecond[0];
		pmb_mysql_query( $rqt );
	}
	
	//affiche l'écran de modification du statut par défaut d'un site
	public static function get_display_default_status($id) {
		global $transferts_admin_statuts_loc_modif;
		
		//la requete
		$rqt = "SELECT idlocation, location_libelle, transfert_statut_defaut FROM docs_location WHERE idlocation=".$id;
		$res = pmb_mysql_query($rqt);
		$value = pmb_mysql_fetch_array($res);
		
		//on remplace dans le template
		$tmpString = str_replace("!!nom_site!!",$value[1],$transferts_admin_statuts_loc_modif);
		$tmpString = str_replace("!!id_site!!",$value[0],$tmpString);
		$tmpString = str_replace("!!selStatut!!",$value[2],$tmpString);
		
		//la liste des statuts
		$rqt = "SELECT idstatut, statut_libelle FROM docs_statut order by statut_libelle";
		$res = pmb_mysql_query($rqt);
		$tmpOpt = "";
		while ($value = pmb_mysql_fetch_array($res)) {
			$tmpOpt .= "<option value='" . $value[0] . "'>" . $value[1] . "</option>";
		}
		$tmpString = str_replace("!!liste_statuts!!", $tmpOpt, $tmpString);
		
		return $tmpString;
	}
	
	//enregistre le nouveau statut par défaut d'un site
	public static function save_default_status($id, $statut) {
		//on met à jour l'enregistrement
		$rqt = "UPDATE docs_location SET transfert_statut_defaut=".$statut." WHERE idlocation=".$id;
		pmb_mysql_query( $rqt );
	}
	
	public static function get_display_purge($date_purge=null) {
		global $transferts_admin_purge_defaut;
		global $msg;
		
		if ($date_purge==null) {
			$tmpString = str_replace("!!message_purge!!", "", $transferts_admin_purge_defaut);
		} else {
			$tmpString = str_replace("!!date_purge!!",formatdate($date_purge),$msg["admin_transferts_message_purge"]);
			$tmpString = str_replace("!!message_purge!!", $tmpString, $transferts_admin_purge_defaut);
		}
		
		//on met la date du jour
		$date_purge_dt = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$date_purge_aff = date("Y-m-d", $date_purge_dt);
		$tmpString = str_replace("!!date_purge_mysql!!", $date_purge_aff, $tmpString);
		$date_purge_aff = date("d/m/Y", $date_purge_dt);
		$tmpString = str_replace("!!date_purge!!", $date_purge_aff, $tmpString);
		
		return $tmpString;
	}
	
	//purge l'historique des transferts
	public static function admin_purge_historique($datefin) {
		$rqt = "DELETE transferts.*, transferts_demande.*
				FROM transferts INNER JOIN transferts_demande
				WHERE transferts.etat_transfert=1 AND transferts.date_creation<'".$datefin."' AND num_transfert=id_transfert";
		pmb_mysql_query( $rqt );
	}
}

?>