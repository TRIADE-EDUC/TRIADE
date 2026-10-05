<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_lecture.class.php,v 1.3.10.1 2025/04/25 09:37:43 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class liste_lecture {

	public $id_liste=0;
	public $num_empr=0;
	public $display='';
	public $notices=array();
	public $notices_create_date = array();
	public $action='';
	public $nom_liste='';
	public $description='';
	public $public=0;
	public $num_owner=0;
	public $readonly=0;
	public $confidential=0;
	public $tag = '';
	public $allow_add_records=1;
	public $allow_remove_records=1;
	public $subscribed = 0;
	public $empr = array();

	/**
	 * Constructeur
	 */
	public function __construct($id_liste=0, $act=''){
		$this->id_liste = intval($id_liste);
		$this->action = $act;
		$this->fetch_data();
		$this->proceed();
	}

	protected function fetch_data() {
		global $opac_shared_lists_readonly;

	    $this->nom_liste = '';
	    $this->description='';
	    $this->public=0;
	    $this->num_owner = 0;
	    $this->readonly=0;
	    $this->notices = array();
	    $this->notices_create_date = array();
	    $this->confidential=0;
	    $this->tag='';
	    $this->allow_add_records=($opac_shared_lists_readonly ? 1 : 0);
	    $this->allow_remove_records=($opac_shared_lists_readonly ? 1 : 0);
	    $this->subscribed = 0;
	    if ($this->id_liste) {
	        $req = "select * from opac_liste_lecture where id_liste='".$this->id_liste."' ";
	        $res = pmb_mysql_query($req);
	        if(pmb_mysql_num_rows($res)){
	            $liste = pmb_mysql_fetch_object($res);
	            $this->nom_liste = $liste->nom_liste;
	            $this->description=$liste->description;
	            $this->public=$liste->public;
	            $this->num_owner = $liste->num_empr;
	            $this->readonly=$liste->read_only;
	            $this->confidential=$liste->confidential;
	            $this->tag=$liste->tag;
	            $this->allow_add_records=$liste->allow_add_records;
	            $this->allow_remove_records=$liste->allow_remove_records;
	            $this->subscribed = $liste->subscribed;

	            $this->notices = array();
	            $this->notices_create_date = array();
	            $query = "select * from opac_liste_lecture_notices where opac_liste_lecture_num=" . $this->id_liste;
	            $result = pmb_mysql_query($query);
	            if (pmb_mysql_num_rows($result)) {
	                while ($row = pmb_mysql_fetch_object($result)) {
	                    $this->notices[] = $row->opac_liste_lecture_notice_num;
	                    $this->notices_create_date[$row->opac_liste_lecture_notice_num] = $row->opac_liste_lecture_create_date;
	                }
	            }
	        }
	    }
	}

	protected function proceed(){

		switch($this->action){
			case 'fetch_empr':
				$this->fetch_empr();
				break;
			default:
				$this->fetch_empr();
				break;
		}
	}

	protected function fetch_empr() {
		$query = "select id_empr, trim(concat(empr_prenom,' ',empr_nom)) as nom, empr_login, empr_mail, nom_liste, confidential
			from empr, abo_liste_lecture, opac_liste_lecture
			where abo_liste_lecture.num_empr=empr.id_empr
			and opac_liste_lecture.id_liste=abo_liste_lecture.num_liste
			and etat=2 and num_liste='".$this->id_liste."'
			order by nom";
		$result = pmb_mysql_query($query);
		$this->empr = array();
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->empr[$row->id_empr] = $row;
			}
		}
	}
}
?>