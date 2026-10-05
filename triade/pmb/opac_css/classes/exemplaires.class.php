<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: exemplaires.class.php,v 1.10.4.2 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class exemplaires {

	protected $notice_id;

	protected $bulletin_id;

	protected $niveau_biblio;

	protected $data;

	public function __construct($notice_id=0, $bulletin_id=0, $niveau_biblio='m') {
	    $this->notice_id = intval($notice_id);
	    $this->bulletin_id = intval($bulletin_id);
		$this->niveau_biblio = $niveau_biblio;
	}

	protected static function get_query($id=0, $bull_id=0) {
		global $opac_sur_location_activate;
		global $opac_view_filter_class;
		global $opac_expl_order;

		if($opac_sur_location_activate){
			$opac_sur_location_select=", sur_location.*";
			$opac_sur_location_from=", sur_location";
			$opac_sur_location_where=" AND docs_location.surloc_num=sur_location.surloc_id";
		} else {
			$opac_sur_location_select="";
			$opac_sur_location_from="";
			$opac_sur_location_where="";
		}
		if($opac_view_filter_class){
			if(is_countable($opac_view_filter_class->params["nav_sections"]) && sizeof($opac_view_filter_class->params["nav_sections"])){
				$opac_view_filter_where=" AND idlocation in (". implode(",",$opac_view_filter_class->params["nav_sections"]).")";
			}else{
				return "";
			}
		} else {
			$opac_view_filter_where = '';
		}

		$query = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_type.*, docs_codestat.*, lenders.* $opac_sur_location_select";
		$query .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl, docs_location, docs_section, docs_statut, docs_type, docs_codestat, lenders $opac_sur_location_from";
		$query .= " WHERE expl_notice='$id' and expl_bulletin='$bull_id'";
		$query .= " AND location_visible_opac=1 AND section_visible_opac=1 AND statut_visible_opac=1";
		$query .= $opac_sur_location_where;
		$query .= $opac_view_filter_where;
		$query .= " AND exemplaires.expl_location=docs_location.idlocation";
		$query .= " AND exemplaires.expl_section=docs_section.idsection ";
		$query .= " AND exemplaires.expl_statut=docs_statut.idstatut ";
		$query .= " AND exemplaires.expl_typdoc=docs_type. idtyp_doc ";
		$query .= " AND exemplaires.expl_codestat=docs_codestat.idcode ";
		$query .= " AND exemplaires.expl_owner=lenders.idlender ";
		if ($opac_expl_order) $query .= " ORDER BY $opac_expl_order ";
		return $query;
	}

	/**
	 * Retourne les données d'exemplaires
	 * @return array
	 */
	public function get_data() {
		if (!isset($this->data)) {
			global $opac_sur_location_activate;
			global $opac_view_filter_class;
			global $opac_expl_data;
			global $memo_p_perso_expl;
			global $opac_show_exemplaires_analysis;

			$this->data = array();
			$this->data['expls'] = array();
			$this->data['colonnesarray'] = array();
// 			if((is_null($this->dom_2) && $opac_show_exemplaires && $this->is_visu_expl() && (!$this->is_visu_expl_abon() || ($this->is_visu_expl_abon() && $_SESSION["user_code"]))) || ($this->get_rights() & 8)) {
				if($opac_view_filter_class){
					if(empty($opac_view_filter_class->params["nav_sections"])){
						return;
					}
				}
				$result = false;
				// les exemplaires des monographies
				if ($this->niveau_biblio=="m") {
					$requete = static::get_query($this->notice_id, $this->bulletin_id);
					$result = pmb_mysql_query($requete);
				} // fin si "m"

				// les exemplaires des bulletins
				if ($this->niveau_biblio=="b") {
					$requete = static::get_query(0, $this->bulletin_id);
					$result = pmb_mysql_query($requete);
				} // fin si "b"

				// les exemplaires des bulletins des articles affichés
				// ERICROBERT : A faire ici !
				if ($this->niveau_biblio=="a" && $opac_show_exemplaires_analysis) {
					$requete = static::get_query(0, $this->bulletin_id);
					$result = pmb_mysql_query($requete);
				} // fin si "a"

				$surloc_field="";
				if ($opac_sur_location_activate==1) $surloc_field="surloc_libelle,";
				if (!$opac_expl_data) $opac_expl_data="tdoc_libelle,".$surloc_field."location_libelle,section_libelle,expl_cote";

				//Présence de champs personnalisés
				if (strstr($opac_expl_data, "#")) {
				    if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
				}
				$colonnesarray=explode(",",$opac_expl_data);
				$this->data['colonnesarray'] = $colonnesarray;

				if ($result && pmb_mysql_num_rows($result)) {

					while ($expl = pmb_mysql_fetch_object($result)) {

						$requete_resa = "SELECT count(1) from resa where resa_cb='".addslashes($expl->expl_cb)."' ";
						$flag_resa = pmb_mysql_result(pmb_mysql_query($requete_resa),0,0);
						$requete_resa = "SELECT count(1) from resa_ranger where resa_cb='".addslashes($expl->expl_cb)."' ";
						$flag_resa = $flag_resa + pmb_mysql_result(pmb_mysql_query($requete_resa),0,0);
						$requete_owner = "SELECT distinct lender_libelle FROM exemplaires JOIN lenders ON lenders.idlender = exemplaires.expl_owner WHERE expl_id = '".$expl->expl_id."'";
						$lender = pmb_mysql_result(pmb_mysql_query($requete_owner),0,0);
						$requete_codestat = "SELECT codestat_libelle FROM exemplaires JOIN docs_codestat ON docs_codestat.idcode = exemplaires.expl_codestat WHERE expl_id = '".$expl->expl_id."'";
						$codestat = pmb_mysql_result(pmb_mysql_query($requete_codestat),0,0);

						$expl_datas = array(
								'num_infopage' => $expl->num_infopage,
								'surloc_id' => (isset($expl->surloc_id) ? $expl->surloc_id : 0),
								'expl_location' => $expl->expl_location,
								'expl_cb' => $expl->expl_cb,
								'statut_libelle_opac' => translation::get_translated_text($expl->expl_statut, "docs_statut", "statut_libelle_opac", $expl->statut_libelle_opac),
								'section_libelle_opac' => translation::get_translated_text($expl->idsection, "docs_section", "section_libelle_opac", $expl->section_libelle_opac),
								'pret_flag' => $expl->pret_flag,
								'pret_retour' => $expl->pret_retour,
								'pret_idempr' => $expl->pret_idempr,
								'expl_statut' => $expl->expl_statut,
								'expl_id' => $expl->expl_id,
								'flag_resa' => $flag_resa,
								'id_notice' => 0,
								'id_bulletin' => 0,
								'expl_comment' => $expl->expl_comment,
								'expl_owner' => $lender,
								'expl_codestat' => $codestat
						);

						foreach ($colonnesarray as $colonne) {
						    if (substr($colonne,0,1)=="#") {
						        //champs personnalisés
						        $cp_id=substr($colonne,1);
						        $memo_p_perso_expl->get_values($expl->expl_id);
						        if (!$memo_p_perso_expl->no_special_fields) {
						            $temp=$memo_p_perso_expl->get_formatted_output((isset($memo_p_perso_expl->values[$cp_id]) ? $memo_p_perso_expl->values[$cp_id] : array()), $cp_id);
						            if (!$temp) $temp="";
						            $expl_datas[$colonne] = $temp;
						        }
						    } else {
						        $expl_datas[$colonne] = $expl->{$colonne};
						    }
						}
						if($expl->pret_retour) { // exemplaire sorti
							$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr FROM empr WHERE id_empr='".$expl->pret_idempr."' ";
							$res_empr = pmb_mysql_query($rqt_empr);
							$res_empr_obj = pmb_mysql_fetch_object($res_empr);

							$expl_datas['empr_nom'] = $res_empr_obj->empr_nom;
							$expl_datas['empr_prenom'] = $res_empr_obj->empr_prenom;
						}

						$this->data['expls'][] = $expl_datas;
					}
				}
// 			}
		}
		return $this->data;
	}
}