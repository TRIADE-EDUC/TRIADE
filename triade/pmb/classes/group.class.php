<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: group.class.php,v 1.22 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/expl.class.php');
require_once($class_path.'/comptes.class.php');

// définition de la classe de gestion des groupes emprunteurs

class group {
	public $id=0;
	public $libelle = '';
	public $id_resp = 0;
	public $libelle_resp = '';
	public $cb_resp = '';
	public $mail_resp = '';
	public $members;
	public $nb_members = 0;
	public $lettre_rappel = 0 ;
	public $mail_rappel = 0 ;
	public $lettre_rappel_show_nomgroup = 0 ;

	// constructeur
	public function __construct($id=0) {
		$this->id = $id+0;
		// si id; récupération des données du groupe
		if($this->id) {
			$this->members = array();
			$this->get_data();
		}
	}

	// récupération des données du groupe
	public function get_data() {
		global $dbh;
		$requete = "SELECT * FROM groupe";
		$requete .= " WHERE id_groupe='".$this->id."' ";
		$res = pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_object($res);
			$this->libelle = $row->libelle_groupe;
			$this->lettre_rappel=$row->lettre_rappel;
			$this->mail_rappel=$row->mail_rappel;
			$this->lettre_rappel_show_nomgroup=$row->lettre_rappel_show_nomgroup;
			// récupération id et libelle du responsable
			if($row->resp_groupe) {
			  	$this->id_resp = $row->resp_groupe;
			  	$requete = "SELECT empr_nom, empr_prenom, empr_cb, empr_mail FROM empr";
			  	$requete .= " WHERE id_empr=".$this->id_resp." LIMIT 1";
			  	$res = pmb_mysql_query($requete, $dbh);
			  	if(pmb_mysql_num_rows($res)) {
			  		$row = pmb_mysql_fetch_object($res);
			  		$this->libelle_resp = $row->empr_nom;
			  		if($row->empr_prenom) $this->libelle_resp .= ', '.$row->empr_prenom;
			  		$this->libelle_resp .= ' ('.$row->empr_cb.')';
			  		$this->cb_resp = $row->empr_cb;
			  		$this->mail_resp = $row->empr_mail;
		  		}
		  	}
			$this->get_members();
		}
		return;
	}

	// génération du form de group
	public function form() {
		global $group_form;
		global $msg;
	 	global $charset;
		if($this->id) $titre = $msg[912]; // modification
			else $titre = $msg[910]; // création
		$group_form = str_replace('!!titre!!', $titre, $group_form);
		if ($this->lettre_rappel) $group_form = str_replace('!!lettre_rappel!!', "checked", $group_form);
		else $group_form = str_replace('!!lettre_rappel!!', "", $group_form);
		if ($this->mail_rappel) $group_form = str_replace('!!mail_rappel!!', "checked", $group_form);
		else $group_form = str_replace('!!mail_rappel!!', "", $group_form);
		if ($this->lettre_rappel_show_nomgroup) $group_form = str_replace('!!lettre_rappel_show_nomgroup!!', "checked", $group_form);
		else $group_form = str_replace('!!lettre_rappel_show_nomgroup!!', "", $group_form);
	 	$group_form = str_replace('!!group_name!!', htmlentities($this->libelle,ENT_QUOTES, $charset), $group_form);
		$group_form = str_replace('!!nom_resp!!', $this->libelle_resp, $group_form);
		$group_form = str_replace('!!groupID!!', $this->id, $group_form);
		$group_form = str_replace('!!respID!!', $this->id_resp, $group_form);
		if($this->id) {
		 	$link_annul = './circ.php?categ=groups&action=showgroup&groupID='.$this->id;
		 	$link_suppr = "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\" />";
		} else {
	 		$link_annul = './circ.php?categ=groups';
	 		$link_suppr = "";
	 	}
		$group_form = str_replace('!!link_annul!!', $link_annul, $group_form);
		$group_form = str_replace('<!-- bouton_suppression -->', $link_suppr, $group_form);
		return $group_form;
	}
      
	// affectation de nouvelles valeurs
	public function set($group_name, $respID=0, $lettre_rappel=0, $mail_rappel=0, $lettre_rappel_show_nomgroup=0) {
		if ($group_name) $this->libelle = $group_name;
		$this->id_resp = $respID;
		$this->lettre_rappel=$lettre_rappel;
		$this->mail_rappel=$mail_rappel;
		$this->lettre_rappel_show_nomgroup=$lettre_rappel_show_nomgroup;
		return;
	}

	// récupération des membres du groupe (feed : array members)
	public function get_members() {
		if(!$this->id) return;
		global $dbh;
	
		$requete = "select EMPR.id_empr AS id, EMPR.empr_nom AS nom , EMPR.empr_prenom AS prenom, EMPR.empr_cb AS cb, EMPR.empr_categ AS id_categ, EMPR.type_abt AS id_abt";
		$requete .= " FROM empr EMPR, empr_groupe MEMBERS";
		$requete .= " WHERE MEMBERS.empr_id=EMPR.id_empr";
		$requete .= " AND MEMBERS.groupe_id=".$this->id;
		$requete .= " ORDER BY EMPR.empr_nom, EMPR.empr_prenom";
		$result = pmb_mysql_query($requete, $dbh);
		$this->nb_members = pmb_mysql_num_rows($result);
		if($this->nb_members) {
		 	while($mb = pmb_mysql_fetch_object($result)) {
		 		$this->members[] = array( 'nom' => $mb->nom,
							'prenom' => $mb->prenom,
							'cb' => $mb->cb,
							'id' => $mb->id,
		 					'id_categ' => $mb->id_categ,
		 					'id_abt' => $mb->id_abt);
			}
		}
		$this->nb_members = sizeof($this->members);
		return;
	}

	// ajout d'un membre
	public function add_member($member) {
		global $dbh;
		if(!$member) return 0;
		
		// checke si ce membre n'est pas déjà dans le groupe
		$requete = "SELECT count(1) FROM empr_groupe";
		$requete .= " WHERE empr_id=$member AND groupe_id=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_result($res, 0, 0)) return $member;
		
		// OK. insertion 'pour de vrai'
		$requete = "INSERT INTO empr_groupe";
		$requete .= " SET empr_id='$member', groupe_id='".$this->id."'";
		$res = pmb_mysql_query($requete, $dbh);
		if($res) return $member;
			else return 0;
	}
      
	// suppression du groupe
	public function delete() {
		global $dbh;
		$requete = "DELETE FROM groupe WHERE id_groupe=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		$nb = pmb_mysql_affected_rows($dbh);
		$requete = "DELETE FROM empr_groupe WHERE groupe_id=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		return $nb;
	}

	// suppression d'un membre
	public function del_member($member) {
		global $dbh;
		if(!$member) return 0;
		$requete = "DELETE FROM empr_groupe";
		$requete .= " WHERE empr_id=$member AND groupe_id=".$this->id;
		$res = pmb_mysql_query($requete, $dbh);
		return $res;
	}

	// mise à jour dans la table
	public function update() {
		global $dbh;
		global $msg;
		
		if($this->id) {
			// mise à jour
			$requete = "UPDATE groupe";
			$requete .= " SET libelle_groupe='".$this->libelle."'";
			$requete .= ", resp_groupe='".$this->id_resp."'";
			$requete .= ", lettre_rappel='".$this->lettre_rappel."'";
			$requete .= ", mail_rappel='".$this->mail_rappel."'";
			$requete .= ", lettre_rappel_show_nomgroup='".$this->lettre_rappel_show_nomgroup."'";
			$requete .= " WHERE id_groupe=".$this->id." LIMIT 1";
			$res = pmb_mysql_query($requete, $dbh);
		} else {
			// on voit si ça n'existe pas
			if($this->exists($this->libelle)) return $this->id;
			
			// création
			$requete = "INSERT INTO groupe SET id_groupe=''";
			$requete .= ", libelle_groupe='".$this->libelle."'";
			$requete .= ", resp_groupe='".$this->id_resp."'";
			$requete .= ", lettre_rappel='".$this->lettre_rappel."'";
			$requete .= ", mail_rappel='".$this->mail_rappel."'";
			$requete .= ", lettre_rappel_show_nomgroup='".$this->lettre_rappel_show_nomgroup."'";
			$result = pmb_mysql_query($requete, $dbh);
			$this->id = pmb_mysql_insert_id();
		}
		return $this->id;
	}

	public function exists($name) {
		global $dbh;
		if(!$name) return;
		$requete = "SELECT count(1) FROM groupe";
		$requete .= " WHERE libelle_groupe='$name'";
		$result = pmb_mysql_query($requete, $dbh);
		return pmb_mysql_result($result, 0, 0);
	}
	
	// prolongation d'adhésion des membres en fin d'abonnement ou en abonnement dépassé
	public function update_members() {
		global $dbh;
		global $msg;
	
		if($this->id) {
			if($this->nb_members) {
			    foreach ($this->members as $cle => $membre) {
					$date_prolong = "form_expiration_".$membre['id'];
					global ${$date_prolong};
					if (${$date_prolong} != "") {
						//Ne pas débiter l'abonnement deux fois..
						$requete = "SELECT empr_date_expiration FROM empr WHERE id_empr=".$membre['id'];
						$resultat = pmb_mysql_query($requete,$dbh);
						if ($resultat) {
							if (str_replace("-","",pmb_mysql_result($resultat,0,0)) != str_replace("-","",${$date_prolong})) {
								// mise à jour
								$requete = "UPDATE empr";
								$requete .= " SET empr_date_expiration='".${$date_prolong}."'";
								$requete .= " WHERE id_empr=".$membre['id']." LIMIT 1";
								@pmb_mysql_query($requete, $dbh);
								if(!pmb_mysql_errno($dbh)) {
									global $debit;
									if ($debit) {
										if ($debit==2) $rec_caution=true; else $rec_caution=false;
										emprunteur::rec_abonnement($membre['id'],$membre['id_abt'],$membre['id_categ'],$rec_caution);
									}
								} else {
									error_message($msg[540], "erreur modification emprunteur", 1, './circ.php?categ=groups&action=showgroup&groupID=".$this->id."');
								}
							}
						}
					}
				}
			}
		}
	}

	// prolongation des prêts des membres, dont la date de retour est < à la date sélectionnée
	public function pret_prolonge_members() {
		global $dbh, $msg;
		global $group_prolonge_pret_date;

		if(!$this->id) return;
		$expls = array();		
		foreach ($this->members as $empr) {
			$req = "select pret_idexpl from pret where pret_idempr=".$empr['id'];
			$res = pmb_mysql_query($req, $dbh);
		 	while ($r = pmb_mysql_fetch_object($res)) {
		 		$expls[] = array(
		 				'id' => $r->pret_idexpl,
		 		);
		 	}
			$req = "update pret set pret_retour='".$group_prolonge_pret_date."', cpt_prolongation=cpt_prolongation+1 where pret_retour<'".$group_prolonge_pret_date."' and pret_idempr=".$empr['id'];
			$res = pmb_mysql_query($req, $dbh);
		}
		return $expls;
	}
	
	public static function gen_combo_box_grp ( $selected=false, $multiple=0, $afficher_aucun=1, $afficher_premier=1, $on_change="" ) {
		global $msg,$deflt2docs_location;
		
		if (!$selected) {
			$selected=array(0=>$deflt2docs_location);
		}
		
		$requete="select idlocation, location_libelle from docs_location order by location_libelle ";
		$champ_code="idlocation";
		$champ_info="location_libelle";
		$nom="group_location_id";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_location'];
		$option_premier_code="-1";
		if ($afficher_premier) $option_premier_info=$msg['all_location'];
		$option_aucun_code="-2";
		if ($afficher_aucun) $option_aucun_info=$msg['no_location'];
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select ";
		if($multiple){
			$gen_liste_str .="multiple='multiple' ";
		}
		$gen_liste_str .="name='".$nom."[]' onChange='".$on_change."' >\n";
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value='".$liste_vide_code."'>".$liste_vide_info."</option>\n" ;
		} else {
			if ($option_premier_info!="") {
				$gen_liste_str.="<option value='".$option_premier_code."' ";
				if (in_array($option_premier_code,$selected)) $gen_liste_str.="selected" ;
				$gen_liste_str.=">- ".$option_premier_info." -</option>\n";
			}
			if ($option_aucun_info!="") {
				$gen_liste_str.="<option value='".$option_aucun_code."' ";
				if (in_array($option_aucun_code,$selected)) $gen_liste_str.="selected" ;
				$gen_liste_str.=">- ".$option_aucun_info." -</option>\n";
			}
			$i=0;
			while ($i<$nb_liste) {
				$gen_liste_str.="<option value='".pmb_mysql_result($resultat_liste,$i,$champ_code)."' " ;
				if (in_array(pmb_mysql_result($resultat_liste,$i,$champ_code),$selected)) {
					$gen_liste_str.="selected" ;
				}
				$gen_liste_str.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n" ;
				$i++;
			}
		}
		$gen_liste_str.="</select>\n" ;
		return $gen_liste_str ;
	}

	public function get_transactions($typ_compte) {
		global $charset, $msg;
		global $show_transactions, $date_debut;

		$display = '';
		$nb_transactions = 0;
		$transactions_display = '';
		$solde_total = 0;
		$non_valide_total = 0;
		
		foreach ($this->members as $empr) {
			$id_compte = comptes::get_compte_id_from_empr($empr['id'], $typ_compte);

			$cpte = new comptes($id_compte);
			$solde_total+= $cpte->get_solde();
			$non_valide_total+= $cpte->summarize_transactions("", "", 0, 0);
			
			switch ($show_transactions) {
				case "2":
					$t = $cpte->get_transactions("", "", 0, 0);
					break;
				case "3":
					$date_debut_ = extraitdate($date_debut);
					$t = $cpte->get_transactions($date_debut_, "", 0, -1, 0, "asc");
					break;
				case "1":
				default:
					$t = $cpte->get_transactions("", "", 0, -1, 10);
					break;
			}
			if (count($t)) {				
				for ($i = 0; $i < count($t); $i++) {
					$nb_transactions++;
					$transactions_display.= "
						<tr>
							<td>".formatdate($t[$i]->date_enrgt)."</td>
							<td>".($t[$i]->encaissement ? "*" : " ")."<a href=\"./circ.php?categ=pret&form_cb=".rawurlencode($empr['cb'])."&groupID=".$this->id."\">".$empr['nom']." ".$empr['prenom']."</a></td>
							<td>".$t[$i]->commentaire."</td>
							<td  style='text-align:right'>".($t[$i]->sens==-1 ? "<span class='erreur'>" : "").comptes::format($t[$i]->montant).($t[$i]->sens==-1? "</span>":"")."</td>
							<td style='text-align:right'>".($t[$i]->sens==1 ? $msg["finance_form_empr_libelle_credit"] : $msg["finance_form_empr_libelle_debit"])."</td>
							<td style='text-align:center'>".($t[$i]->realisee ? "X" : "")."</td>
							<td>".formatdate($t[$i]->date_effective)."</td>					
						</tr>";
				}
			}			
		}
		if ($nb_transactions) {
			$display = "
				<table style='width:100%'>
					<tr>
						<th>".$msg["finance_list_tr_date_enrgt"]."</th>
						<th>&nbsp;</th>
						<th>".$msg["finance_list_tr_comment"]."</th>
						<th style='text-align:right'>".$msg["finance_montant"]."</th>
						<th style='text-align:right'>".$msg["finance_list_tr_deb_cred"]."</th>
						<th style='text-align:center'>".$msg["finance_list_tr_validee"]."</th>
						<th>".$msg["finance_date_valid"]."</th>
					</tr>".
					$transactions_display."
				</table>";
		}

		return array(
				'typ_compte' => $typ_compte,
				'typ_compte_lib' => comptes::get_typ_compte_lib($typ_compte),
				'solde_total' => $solde_total,
				'non_valide_total' => $non_valide_total,
				'solde_total_display' => comptes::format($solde_total),
				'non_valide_total_display' => comptes::format($non_valide_total),
				'transactions_display' => $display,				
		);
	}
	
	public function get_transactions_form() {
		global $charset, $msg;
		global $show_transactions, $date_debut, $typ_compte;	
		
		if (!$show_transactions) {
			$show_transactions = 2; // Non validée par défaut
		}
		$transactions = $this->get_transactions($typ_compte);
		
		$form = "		
		<div class='row'>
			<div class='colonne2'><h2><a href='./circ.php?categ=groups&action=showgroup&groupID=".$this->id."'>!!group_name!!</a> : !!type_compte!!</h2></div><div class='colonne2' style='text-align:right'><h2>".$msg["finance_solde"]." !!solde!!<br />".$msg["finance_not_validated"]." : !!non_valide!!</h2></div>
		</div>
		<form name='compte_form' method='post' action='./circ.php?categ=groups&action=showcompte&typ_compte=!!typ_compte!!&groupID=".$this->id."'>
				<input type='hidden' name='act' value=''/>
				<div class='row' id='selector_transaction_list'>
				<div class='colonne3'><input type='radio' name='show_transactions' value='1' id='show_transactions_1' !!checked1!! onClick=\"this.form.submit();\"/><label for='show_transactions_1'>".$msg["finance_form_empr_ten_last"]."</label></div>
				<div class='colonne3'><input type='radio' name='show_transactions' value='2' id='show_transactions_2' !!checked2!! onClick=\"this.form.submit();\"/><label for='show_transactions_2'>".$msg["finance_form_empr_not_validated"]."</label></div>
				<div class='colonne3'><input type='radio' name='show_transactions' value='3' id='show_transactions_3' !!checked3!! onClick=\"this.form.submit()\"/><label for='show_transactions_3'>".$msg["finance_form_empr_tr_from"]." </label><input type='text' size='10' name='date_debut' value='!!date_debut!!'></div>
			</div>
			<div class='row'>&nbsp;</div>
			".$transactions['transactions_display']."
			<div class='row'>&nbsp;</div>
			<div class='row' id='buttons_transaction_list'>	
				<table>
					<tr>
						<td style='text-align:left'>
							<input type='button' class='bouton' value='".$msg["finance_but_valenc"]."' onClick=\"this.form.act.value='valenc'; this.form.submit()\"><br />
						</td>
					</tr>
				</table>
			</div>
		</form>";
		
		for ($i = 1; $i <= 3; $i++) {
			if ($i == $show_transactions) $form = str_replace("!!checked$i!!", "checked", $form);
			else $form = str_replace("!!checked$i!!", "", $form);
		}
		$form = str_replace("!!group_name!!", htmlentities($this->libelle, ENT_QUOTES, $charset), $form);
		$form = str_replace("!!typ_compte!!", $typ_compte, $form);
		$form = str_replace("!!type_compte!!", htmlentities($transactions['typ_compte_lib'], ENT_QUOTES, $charset), $form);
		$form = str_replace("!!solde!!", $transactions['solde_total_display'], $form);
		$form = str_replace("!!non_valide!!", $transactions['non_valide_total_display'], $form);
		$form = str_replace("!!date_debut!!", htmlentities(stripslashes($date_debut), ENT_QUOTES, $charset), $form);
		
		return $form;
	}

	public function get_solde_form() {
		global $msg;
		global $charset;
		global $pmb_gestion_financiere, $pmb_gestion_abonnement, $pmb_gestion_tarif_prets, $pmb_gestion_amende;		
		
		if (!$pmb_gestion_financiere) return '';

		$solde_abonnement = 0;
		$novalid_abonnement = 0;
		$solde_prets = 0;
		$novalid_prets = 0;
		$solde_amende = 0;
		$novalid_amende = 0;
		$total_amende = 0;
		$nb_amendes = 0;
		$solde_transac = 0;
		$novalid_transac = 0;
		$form = '';
		foreach ($this->members as $empr) {
			if ($pmb_gestion_abonnement) {
				$cpt_id = comptes::get_compte_id_from_empr($empr['id'], 1);
				if ($cpt_id) {
					$cpt = new comptes($cpt_id);
					$solde_abonnement+= $cpt->update_solde();
					$novalid_abonnement+= $cpt->summarize_transactions("", "", 0, 0);
				}
			}
			if ($pmb_gestion_tarif_prets) {
				$cpt_id = comptes::get_compte_id_from_empr($empr['id'], 3);
				if ($cpt_id) {
					$cpt = new comptes($cpt_id);
					$solde_prets+= $cpt->update_solde();
					$novalid_prets+= $cpt->summarize_transactions("", "", 0, 0);
				}
			}
			if ($pmb_gestion_amende) {
				$cpt_id = comptes::get_compte_id_from_empr($empr['id'], 2);
				if ($cpt_id) {
					$cpt = new comptes($cpt_id);
					$solde_amende+= $cpt->update_solde();
					$novalid_amende+= $cpt->summarize_transactions("", "", 0, 0);
				
					//Calcul des amendes
					$amende = new amende($empr['id'],true);
					$total_amende+= $amende->get_total_amendes();
					$nb_amendes+= $amende->nb_amendes;
				}
			}
			// Autre compte, que s'il y a des types de transaction
			$transactype = new transactype_list();
			if ($transactype->get_count()) {
				$cpt_id = comptes::get_compte_id_from_empr($empr['id'], 4);
				if ($cpt_id) {
					$cpt = new comptes($cpt_id);
					$solde_transac+= $cpt->update_solde();
					$novalid_transac+= $cpt->summarize_transactions("", "", 0, 0);
				}
			}
		}	
		// construnction du formulaire
		if ($solde_abonnement || $novalid_abonnement) {
			$form.= "<div class='colonne4'><div><strong><a href='./circ.php?categ=groups&action=showcompte&groupID=".$this->id."&typ_compte=1'>".$msg["finance_solde_abt"]."</a></strong> ".comptes::format($solde_abonnement)."</div>";
			if ($novalid_abonnement)
				$form.= "<div>".$msg["finance_not_validated"]." : ".comptes::format($novalid_abonnement)."</div>";			
			$form.= "</div>";
		}
		if ($solde_prets || $novalid_prets) {
			$form.= "<div class='colonne4'><div><strong><a href='./circ.php?categ=groups&action=showcompte&groupID=".$this->id."&typ_compte=3'>".$msg["finance_solde_pret"]."</a></strong> ".comptes::format($solde_prets)."</div>";
			if ($novalid_prets)
				$form.= "<div>".$msg["finance_not_validated"]." : ".comptes::format($novalid_prets)."</div>";
			$form.= "</div>";
		}
		if ($solde_amende || $novalid_amende) {
			$form.= "<div class='colonne4'><div><strong><a href='./circ.php?categ=groups&action=showcompte&groupID=".$this->id."&typ_compte=2'>".$msg["finance_solde_amende"]."</a></strong> ".comptes::format($solde_amende)."</div>";
			if ($novalid_amende)
				$form.= "<div>".$msg["finance_not_validated"]." : ".comptes::format($novalid_amende)."</div>";
			if ($total_amende)
				$form.= "<div> ".$msg["finance_pret_amende_en_cours"]." : ".comptes::format($total_amende)."</div>";
			$form.= "</div>";
		}
		if ($solde_transac || $novalid_transac) {
			$form.= "
				<div class='colonne4'>
					<div>
						<strong><a href='./circ.php?categ=groups&action=showcompte&groupID=".$this->id."&typ_compte=4'>".$msg["transactype_empr_compte"]."</a></strong> ".comptes::format($solde_transac)."</div>";
			if ($novalid_transac)
				$form.= "<div>".$msg["finance_not_validated"]." : ".comptes::format($novalid_transac)."</div>";
			$form.= "</div>";
		}
		return $form;
	}
	
	public function get_encaissement_rapide_form() {
		global $charset, $msg;
		global $show_transactions, $typ_compte, $date_debut, $pmb_gestion_devise;		
		
		$somme = 0;
		$solde = 0;
		$val_transactions = "";
		foreach ($this->members as $empr) {
			$id_compte = comptes::get_compte_id_from_empr($empr['id'], $typ_compte);
			$cpte = new comptes($id_compte);
			//Validation de ce qui n'est pas valide
			$t = $cpte->get_transactions("", "", 0, 0);
			for ($i = 0; $i < count($t); $i++) {
				if ($cpte->validate_transaction($t[$i]->id_transaction)) {
					$somme+= $t[$i]->montant*$t[$i]->sens;
					$val_transactions.= " #".$t[$i]->id_transaction."#";
				}
			}
			if (count($t)) {
				$transacash_num = $cpte->cashdesk_memo_transactions($t);
			}
			
			$solde_avant = $cpte->get_solde();
			if ($solde_avant != 0) $val_transactions.= $msg["finance_enc_tr_lib_etat_compte"]." : ".$solde_avant;
			$cpte->update_solde();

			$solde+= $cpte->get_solde();	
		}
		if ($val_transactions != "") $val_transactions = $msg["finance_enc_tr_lib_valider"]." : ".$val_transactions."\n";
		
		$form = '';
		if ($solde < 0) {
			$form = "		
				<h2><a href='./circ.php?categ=groups&action=showgroup&groupID=".$this->id."'>".htmlentities($this->libelle)."</a> : ".comptes::get_typ_compte_lib($typ_compte)."</h2>
				<table>
					<tr>
						<td style='text-align:right'>".$msg["finance_enc_montant_valide"]." : </td>
						<td style='text-align:right'>".comptes::format($somme*(-1))."</td>
					</tr>";
			if ($solde <= 0) {
				$form.= "
					<tr class='erreur'>
						<td style='text-align:right'>".$msg["finance_enc_montant_a_enc"]." : </td>";
			}elseif ($solde > 0) {
				$form.= "			
					<tr>
						<td>".$msg["finance_enc_compte_cred"]." : </td>";
			}
			$form.= "
						<td style='text-align:right'>".comptes::format($solde*(-1))."</td>
					</tr>
				</table>					
				<script type='text/javascript'>
					function check_somme(f) {
						var message = '';
						if (isNaN(f.somme.value)) {
							message = '".addslashes($msg["finance_enc_nan"])."';
						} else {
							if (f.somme.value <= 0)
								message = '".addslashes($msg["finance_enc_mnt_neg"])."';
						}
						if (message) {
							alert(message);
							return false;
						}
						return true;
					}
				</script>
				<form name='form_encaissement' action='./circ.php?categ=groups&action=showcompte&groupID=".$this->id."&typ_compte=".$typ_compte."&show_transactions=$show_transactions&date_debut=".rawurlencode(stripslashes($date_debut))."' method='post'>
					<input type='hidden' name='act' value='enc'/>
					<input type='hidden' name='transacash_num' value='$transacash_num'/>
					<input type='hidden' name='val_transactions' value=\"".htmlentities($val_transactions,ENT_QUOTES,$charset)."\"/>".
					htmlentities($msg['finance_mnt_percu'], ENT_QUOTES, $charset)."&nbsp;<input type='text' value='".$solde*(-1)."' name='somme' class='saisie-5em' style='text-align:right'>&nbsp;".$pmb_gestion_devise."
					<input type='submit' value='".$msg["finance_but_enc"]."' class='bouton' onClick=\"return check_somme(this.form)\"/>&nbsp;
					<input type='button' value='".$msg["76"]."' class='bouton' onClick=\"this.form.act.value=''; this.form.submit();\"/>
				</form>";
		} else {
			$form.= "<script type='text/javascript'>parent.document.location=\"./circ.php?categ=groups&action=showcompte&groupID=".$this->id."&typ_compte=".$typ_compte."&show_transactions=$show_transactions&date_debut=".rawurlencode(stripslashes($date_debut))."\";</script>";			
		}
		return $form;
	}

	public function do_encaissement_rapide() {
		global $charset, $msg;
		global $show_transactions, $typ_compte, $date_debut;		
		global $somme, $transacash_num, $val_transactions;
		
		$somme_restante = $somme*1;
		if ($somme_restante > 0) {
			foreach ($this->members as $empr) {
				if ($somme_restante <= 0) {
					// toute la somme donnée est encaisée
					break;
				}
				$id_compte = comptes::get_compte_id_from_empr($empr['id'], $typ_compte);
				$cpte = new comptes($id_compte);
				$solde_empr = $cpte->get_solde();
				if ($solde_empr < 0) {
					if (round($somme_restante - abs($solde_empr), 2) >= 0) {
						// la totalité de ce que doit l'emprunteur est encaissée
						$encaisser = abs($solde_empr);						
						$somme_restante = round($somme_restante - abs($solde_empr), 2);
					} else {
						// Une partie (la somme restante) de ce que doit l'emprunteur est encaissée
						$encaisser = $somme_restante;
						$somme_restante = 0;
					}
					// Generation de la transaction
					if (($id_transaction = $cpte->record_transaction("", $encaisser, 1, $val_transactions, 1))) {
						$cpte->validate_transaction($id_transaction);
						$cpte->update_solde();
						if (!$transacash_num) {
							$req = "select MAX(transacash_num) from transactions where compte_id=".$cpte->id_compte;
							$resultat = pmb_mysql_query($req);
							if ($transacash_num = pmb_mysql_result($resultat, 0, 0)) {
								$req = "update transactions set transacash_num = $transacash_num where compte_id=".$cpte->id_compte." and transacash_num=0";
								pmb_mysql_query($req);
							}
						}
						$cpte->cashdesk_memo_encaissement($id_transaction, $transacash_num, $encaisser);
					}
				}
			}
			if ($somme_restante > 0) {
				// Trop percu, il faut rendre la monnaie
				return "<script type='text/javascript'>alert('".$msg['group_encaissement_trop_percu'].$somme_restante.$pmb_gestion_devise."'); parent.document.location=\"./circ.php?categ=groups&action=showgroup&groupID=".$this->id."\";</script>";
			}		
		}
		return "<script type='text/javascript'>parent.document.location=\"./circ.php?categ=groups&action=showgroup&groupID=".$this->id."\";</script>";
	}
	
	public function transactions_proceed() {
		global $act;
		
		switch ($act) {
			case 'valenc':
				print $this->get_encaissement_rapide_form();
				break;
			case "enc":
				print $this->do_encaissement_rapide();
				break;
			default: 
				print $this->get_transactions_form();
				break;
 		}
	}
}
