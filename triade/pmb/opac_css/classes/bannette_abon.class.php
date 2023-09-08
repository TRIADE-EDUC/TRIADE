<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_abon.class.php,v 1.10 2018-12-26 11:22:32 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/bannette.class.php");
require_once($class_path."/classements.class.php");

class bannette_abon{
	protected $num_bannette;
	
	protected $num_empr;
	
	protected $groups;
	
	public function __construct($num_bannette=0,$num_empr=0) {
		$this->num_bannette = $num_bannette+0;
		$this->num_empr = $num_empr+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->groups=array();
		$query = "select id_groupe, libelle_groupe from groupe join empr_groupe on groupe_id=id_groupe where empr_id='".$this->num_empr."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($grp_temp=pmb_mysql_fetch_object($result)) {
				$this->groups[$grp_temp->id_groupe]=$grp_temp->libelle_groupe;
			}
		}
	}
	
	public function save_bannette_abon($bannette_abon) {
		$tableau_bannettes = $this->tableau_gerer_bannette("PUB");
		foreach ($tableau_bannettes as $bannette) {
			pmb_mysql_query("delete from bannette_abon where num_empr = '" . $this->num_empr . "' and num_bannette = '".$bannette->id_bannette."' ");
			if (isset($bannette_abon[$bannette->id_bannette]) && $bannette_abon[$bannette->id_bannette]) {
				pmb_mysql_query("replace into bannette_abon (num_empr, num_bannette) values ('" . $this->num_empr . "', '".$bannette->id_bannette."')");
			}
		}
	}
	
	public function delete_bannette_abon($bannette_abon) {
		$tableau_bannettes = $this->tableau_gerer_bannette("PRI");
		foreach ($tableau_bannettes as $bannette) {
			if (isset($bannette_abon[$bannette->id_bannette]) && $bannette_abon[$bannette->id_bannette]) {
				pmb_mysql_query("delete from bannette_abon where num_empr = '" . $this->num_empr . "' and num_bannette = '".$bannette->id_bannette."' ");
				pmb_mysql_query("delete from bannette_contenu where num_bannette='".$bannette->id_bannette."' ");
				$req_eq = pmb_mysql_query("select num_equation from bannette_equation where num_bannette = '".$bannette->id_bannette."' ");
				$eq = pmb_mysql_fetch_object($req_eq);
				pmb_mysql_query("delete from equations where id_equation = '" . $eq->num_equation . "' ");
				pmb_mysql_query("delete from bannette_equation where num_bannette = '".$bannette->id_bannette."' ");
				pmb_mysql_query("delete from bannettes where id_bannette = '".$bannette->id_bannette."' ");
			}
		}
	}
	
	// retourne un tableau des bannettes possibles de l'abonné : les privées / les publiques : celles de sa catégorie et/ou celles auxquelles il est abonné
	public function tableau_gerer_bannette($priv_pub='PUB') {
		global $msg;
		global $empr_categ;
	
		$tableau_bannette = array();
		//Récupération des infos des bannettes
		if ($priv_pub == 'PUB') {
			$access_liste_id = array();
			
			$query = "SELECT empr_categ_num_bannette FROM bannette_empr_categs WHERE empr_categ_num_categ=".$empr_categ;
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_object($result)) {
				$access_liste_id[] = $row->empr_categ_num_bannette;
			}
			$query = "select groupe_id from empr_groupe where empr_id=".$this->num_empr." AND groupe_id != 0";//En création de lecteur une entrée avec groupe_id = 0 est créée ...
			$result = pmb_mysql_query($query);
			$groups = array();
			while ($row=pmb_mysql_fetch_object($result)) {
				$groups[] = $row->groupe_id;
			}
			if (count($groups)) {
				$query = "SELECT empr_groupe_num_bannette FROM bannette_empr_groupes WHERE empr_groupe_num_groupe IN (".implode(",",$groups).")";
				$result = pmb_mysql_query($query);
				while ($row = pmb_mysql_fetch_object($result)) {
					$access_liste_id[] = $row->empr_groupe_num_bannette;
				}
			}
			
			if (count($access_liste_id)) {
				$access_liste_id = array_unique($access_liste_id);
					
			} else {
				$access_liste_id[] = 0;
			}
			
			$restrict = "((id_bannette IN (".implode(',',$access_liste_id).")) or (bannette_opac_accueil = 1))";
			
			$requete = "select distinct id_bannette, num_classement, nom_bannette from bannettes join bannette_abon on num_bannette=id_bannette where num_empr='".$this->num_empr."' and proprio_bannette=0 ";
			$requete .= " union select distinct id_bannette, num_classement, nom_bannette from bannettes where ".$restrict." and proprio_bannette=0 ";
			$requete .= " order by num_classement, nom_bannette ";
		} else {
			$requete = "select distinct id_bannette, num_classement, nom_bannette from bannettes where proprio_bannette='".$this->num_empr."' ";
			$requete .= " order by nom_bannette ";
		}
		$resultat = pmb_mysql_query($requete);
		while ($r = pmb_mysql_fetch_object($resultat)) {
			$tableau_bannette[] = new bannette($r->id_bannette);
		}
		// tri
		if ($priv_pub=="PUB") {
			$tri_bannettes=array();
			$rqt = "select id_classement from classements order by classement_order";
			$res = pmb_mysql_query($rqt);
			if ($res) {
				while ($r=pmb_mysql_fetch_object($res)) {
					if(is_array($tableau_bannette)) {
						foreach ($tableau_bannette as $bannette){
							if($bannette->num_classement == $r->id_classement){
								$tri_bannettes[]=$bannette;
							}
						}
					}
				}
			}
			return $tri_bannettes;
		}
		return $tableau_bannette;
	}
	
	// permet d'afficher un formulaire de gestion des abonnements aux bannettes du lecteur
	// paramètres :
	//	$bannettes : les numéros des bannettes séparés par les ',' toutes si vides
	//	$aff_notices_nb : nombres de notices affichées : toutes = 0
	//	$mode_aff_notice : mode d'affichage des notices, REDUIT (titre+auteur principal) ou ISBD ou PMB ou les deux : dans ce cas : (titre + auteur) en entête du truc
	//	$depliable : affichage des notices une par ligne avec le bouton de dépliable
	//	$link_to_bannette : lien pour afficher le contenu de la bannette
	//	$htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="" : les id, class et zindex du <DIV > englobant le résultat de la fonction
	//	$liens_opac : tableau contenant les url destinatrices des liens si voulu
	public function gerer_abon_bannette($priv_pub="PUB", $link_to_bannette="", $htmldiv_id="bannette-container", $htmldiv_class="bannette-container", $htmldiv_zindex="") {
		global $charset;
		global $msg;
		global $opac_allow_resiliation ;
		global $base_path;
		global $opac_private_bannette_date_used_to_calc;
		
		// récupération des bannettes
		$tableau_bannettes = $this->tableau_gerer_bannette($priv_pub);
	
		if (!count($tableau_bannettes)) return "";
	
		//Pour statistiques abo/desabonnement
		$tableau_bannettes_abo_checked = array();
		foreach ($tableau_bannettes as $bannette) {
			if ($bannette->is_subscribed($this->num_empr)) {
				$tableau_bannettes_abo_checked[] = $bannette->id_bannette;
			}
		}
	
		// préparation du tableau
		$retour_aff = "<div id='$htmldiv_id' class='$htmldiv_class'";
		if ($htmldiv_zindex) $retour_aff .= " zindex='$htmldiv_zindex' ";
		$retour_aff .= " >";
		$retour_aff .= "<form name='bannette_abonn' method='post' >";
		$retour_aff .= "<input type='hidden' name='liste_abo_bann_".strtolower($priv_pub)."' value='".implode(',',$tableau_bannettes_abo_checked)."' />";
		$retour_aff .= "<input type='hidden' name='lvl' value='bannette_gerer' />";
		$retour_aff .= "<input type='hidden' name='enregistrer' value='$priv_pub' />";
		$retour_aff .= "<table cellpadding='3px' cellspacing='5px'>
							<tr>
								<th class='bannette_abon align_right' style='vertical-align:middle'>".$msg['dsi_bannette_gerer_abonn']."</th>
								<th class='bannette_nom_liste align_left' style='vertical-align:middle'>".$msg['dsi_bannette_gerer_nom_liste']."</th>
								<th class='bannette_date center' style='vertical-align:middle'>".$msg['dsi_bannette_gerer_date']."</th>
								<th class='bannette_nb_notices center' style='vertical-align:middle'>".$msg['dsi_bannette_gerer_nb_notices']."</th>
								<th class='bannette_periodicite center' style='vertical-align:middle'>".$msg['dsi_bannette_gerer_periodicite']."</th>
								".($opac_private_bannette_date_used_to_calc == 2 ?"<th class='bannette_date_used_to_calc center' style='vertical-align:middle'>".$msg['dsi_bannette_gerer_date_used_to_calc']."</th>" : "")."
								".($priv_pub == 'PRI' ? "<th class='bannette_edit center' style='vertical-align:bottom'></th>" : "")."
							</tr>";
		$last_classement='';
		foreach ($tableau_bannettes as $bannette) {
			$id_bannette = $bannette->id_bannette;
			$classement = new classement($bannette->num_classement);
			if(isset($classement->nom_classement_opac) && $classement->nom_classement_opac && ($classement->nom_classement_opac != $last_classement)){
				$retour_aff.="<tr><td class='banette_classement' colspan='4'>".$classement->nom_classement_opac."<td>";
				$last_classement = $classement->nom_classement_opac;
			}
			$retour_aff .= "\n<tr><td column_name='".htmlentities($msg['dsi_bannette_gerer_abonn'], ENT_QUOTES, $charset)."' class='bannette_abon align_right' style='vertical-align:top'>";
			if (!$opac_allow_resiliation && count($bannette->categorie_lecteurs)) {
				$retour_aff .= "\n<input type='checkbox' name='dummy[]' value='' ".($priv_pub == "PUB" && in_array($id_bannette, $tableau_bannettes_abo_checked) ? "checked='checked'" : "")." disabled />";
				$retour_aff .= "<input type='hidden' name='bannette_abon[$id_bannette]' value='1' style='display:none'/>";
			} else $retour_aff .= "\n<input type='checkbox' name='bannette_abon[$id_bannette]' value='1' ".($priv_pub == "PUB" && in_array($id_bannette, $tableau_bannettes_abo_checked) ? "checked='checked'" : "")." />";
			$retour_aff .= "\n</td><td column_name='".htmlentities($msg['dsi_bannette_gerer_nom_liste'],ENT_QUOTES, $charset)."' class='bannette_nom_liste align_left' style='vertical-align:top'>";
			if ($link_to_bannette) {
				// Construction de l'affichage de l'info bulle de la requette
				$recherche = get_bannette_human_query($id_bannette);
				if ($recherche) {
					$zoom_comment = "<div id='zoom_comment".$id_bannette."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>";
					$zoom_comment .= $recherche;
					$zoom_comment .= "</div>";
					$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$id_bannette."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$id_bannette."'); z.style.display='none'; \"" ;
				}
				$retour_aff .= "<a href=\"".str_replace("!!id_bannette!!", $id_bannette, $link_to_bannette)."\" $java_comment >";
			}
			$retour_aff.= htmlentities($bannette->comment_public, ENT_QUOTES, $charset);
			if ($link_to_bannette) {
				$retour_aff .= "</a>";
				$retour_aff .= $zoom_comment;
			}
			$retour_aff .= "\n</td><td column_name='".htmlentities($msg['dsi_bannette_gerer_date'], ENT_QUOTES, $charset)."' class='bannette_date center' style='vertical-align:top'>";
			$retour_aff .= htmlentities($bannette->aff_date_last_envoi, ENT_QUOTES, $charset);
			$retour_aff .= "\n</td><td column_name='".htmlentities($msg['dsi_bannette_gerer_nb_notices'], ENT_QUOTES, $charset)."' class='bannette_nb_notices center' style='vertical-align:top'>";
			$retour_aff .= htmlentities($bannette->nb_notices, ENT_QUOTES, $charset);
			$retour_aff .= "\n</td><td column_name='".htmlentities($msg['dsi_bannette_gerer_periodicite'], ENT_QUOTES, $charset)."' class='bannette_periodicite center' style='vertical-align:top'>";
			$retour_aff .= htmlentities($bannette->periodicite, ENT_QUOTES, $charset);
			if ($opac_private_bannette_date_used_to_calc == 2) {
                $retour_aff .= "\n</td><td column_name='".htmlentities($msg['dsi_bannette_gerer_date_used_to_calc'], ENT_QUOTES, $charset)."' class='bannette_date_used_to_calc center' style='vertical-align:top'>";
			    $retour_aff .= htmlentities($msg['dsi_ban_update_type_'.strtolower($bannette->update_type)], ENT_QUOTES, $charset);
			}
			if($priv_pub == 'PRI') {
				$retour_aff .= "\n</td><td column_name='' class='bannette_edit center' style='vertical-align:top'>";
				$retour_aff .= "<a href='".$base_path."/empr.php?tab=dsi&lvl=bannette_edit&id_bannette=".$bannette->id_bannette."' style='cursor : pointer'>";
				$retour_aff .= "<img src='".get_url_icon('tag.png')."' alt='".htmlentities($msg['edit'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['edit'],ENT_QUOTES,$charset)."' />";
				$retour_aff .= "</a>";
			}
			$retour_aff .= "</td></tr>";
		}
		$retour_aff .= "</table>
					<INPUT type='submit' class='bouton' value=\"";
		if ($priv_pub == "PUB") {
			$retour_aff .= $msg['dsi_bannette_gerer_sauver'];
		} else {
			$retour_aff .= $msg['dsi_bannette_gerer_supprimer'];
		}
		$retour_aff .= "\" />
					</form></div>";
		return $retour_aff;
	
	}
}// end class
