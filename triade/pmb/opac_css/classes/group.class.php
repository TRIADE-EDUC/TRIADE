<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: group.class.php,v 1.3 2023/08/31 08:31:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
    public $comment_gestion = '';
    public $comment_opac = '';
    public $lettre_resa = 0 ;
    public $mail_resa = 0 ;
    public $lettre_resa_show_nomgroup = 0 ;
    
    protected $nb_loans;
    protected $nb_loans_late;
    protected $nb_loans_including_late;
    protected $nb_resas;
    
	// constructeur
	public function __construct($id=0) {
		$this->id = intval($id);
		// si id; récupération des données du groupe
		if($this->id) {
			$this->members = array();
			$this->get_data();
		}
	}

	// récupération des données du groupe
	public function get_data() {
		$requete = "SELECT * FROM groupe";
		$requete .= " WHERE id_groupe='".$this->id."' ";
		$res = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_object($res);
			$this->libelle = $row->libelle_groupe;
			$this->lettre_rappel=$row->lettre_rappel;
			$this->mail_rappel=$row->mail_rappel;
			$this->lettre_rappel_show_nomgroup=$row->lettre_rappel_show_nomgroup;
			$this->comment_gestion = $row->comment_gestion;
			$this->comment_opac = $row->comment_opac;
			$this->lettre_resa=$row->lettre_resa;
			$this->mail_resa=$row->mail_resa;
			$this->lettre_resa_show_nomgroup=$row->lettre_resa_show_nomgroup;
			// récupération id et libelle du responsable
			if($row->resp_groupe) {
			  	$this->id_resp = $row->resp_groupe;
			  	$requete = "SELECT empr_nom, empr_prenom, empr_cb, empr_mail FROM empr";
			  	$requete .= " WHERE id_empr=".$this->id_resp." LIMIT 1";
			  	$res = pmb_mysql_query($requete);
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

	// récupération des membres du groupe (feed : array members)
	public function get_members() {
		if(!$this->id) return;
	
		$query = "SELECT EMPR.id_empr AS id, EMPR.empr_nom AS nom , EMPR.empr_prenom AS prenom, EMPR.empr_cb AS cb, EMPR.empr_categ AS id_categ, EMPR.type_abt AS id_abt";
		$query .= " FROM empr EMPR, empr_groupe MEMBERS";
		$query .= " WHERE MEMBERS.empr_id=EMPR.id_empr";
		$query .= " AND MEMBERS.groupe_id=".$this->id;
		$query .= " ORDER BY EMPR.empr_nom, EMPR.empr_prenom";
		$result = pmb_mysql_query($query);
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
		$this->nb_members = count($this->members);
		return;
	}

	// prolongation des prêts des membres, dont la date de retour est < à la date sélectionnée
	public function pret_prolonge_members() {
		global $group_prolonge_pret_date;

		if(!$this->id) {
		    return;
		}
		$expls = array();		
		foreach ($this->members as $empr) {
			$query = "SELECT pret_idexpl FROM pret WHERE pret_idempr=".$empr['id'];
			$result = pmb_mysql_query($query);
			while ($r = pmb_mysql_fetch_object($result)) {
		 	    if(!empty($group_prolonge_pret_date[$r->pret_idexpl])) {
		 	        $date_prolongation = $group_prolonge_pret_date[$r->pret_idexpl];
		 	        $instance_pret = new pret($empr['id'], $r->pret_idexpl);
		 	        //Assurons-nous que le prêt peut être prolongé et que nous ne sommes pas sur une actualisation F5
		 	        if($instance_pret->is_extendable() && $date_prolongation != $instance_pret->pret_retour) {
		 	            //La date passée via le formulaire correspond-elle à celle calculée ?
		 	            if($date_prolongation == $instance_pret->date_prolongation) {
    		 	            $expls[] = array(
            		 				'id' => $r->pret_idexpl,
            		 		);
            		 		$query = "UPDATE pret SET pret_retour='".$date_prolongation."', cpt_prolongation=cpt_prolongation+1 WHERE pret_retour<'".$date_prolongation."' and pret_idempr=".$empr['id'];
            		 		pmb_mysql_query($query);
		 	            }
		 	        }
		 	    }
		 	}
		}
		return $expls;
	}
	
	public function get_nb_loans() {
		if(!isset($this->nb_loans)) {
			$this->nb_loans = 0;
			$query = "SELECT count( pret_idempr ) as nb_pret FROM empr_groupe,pret where groupe_id=".$this->id." and empr_id = pret_idempr";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row=pmb_mysql_fetch_object($result);
				$this->nb_loans = $row->nb_pret;
			}
		}
		return $this->nb_loans;
	}
	
	public function get_nb_loans_late() {
		if(!isset($this->nb_loans_late)) {
			$this->nb_loans_late = 0;
			$query = "SELECT count( pret_idempr ) as nb_retards FROM empr_groupe,pret where groupe_id=".$this->id." and empr_id = pret_idempr and pret_retour<CURDATE()";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row=pmb_mysql_fetch_object($result);
				$this->nb_loans_late = $row->nb_retards;
			}
		}
		return $this->nb_loans_late;
	}
	
	public function get_nb_loans_including_late() {
		if(!isset($this->nb_loans_including_late)) {
			$this->nb_loans_including_late = $this->get_nb_loans();
			if ($this->nb_loans_including_late) {
				$nb_loans_late = $this->get_nb_loans_late();
				if ($nb_loans_late) {
					$this->nb_loans_including_late .= " (".$nb_loans_late.")";
				}
			}
		}
		return $this->nb_loans_including_late;
	}
	
	public function get_nb_resas() {
		if(!isset($this->nb_resas)) {
			$this->nb_resas = 0;
			$query = "SELECT count( resa_idempr ) as nb_resa FROM empr_groupe,resa where groupe_id=".$this->id." and empr_id = resa_idempr";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row=pmb_mysql_fetch_object($result);
				$this->nb_resas = $row->nb_resa;
			}
		}
		return $this->nb_resas;
	}
}
