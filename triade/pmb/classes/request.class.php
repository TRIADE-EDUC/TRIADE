<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: request.class.php,v 1.8 2021/12/28 08:46:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path.'/parser.inc.php');
require_once($include_path.'/templates/requests.tpl.php');

if(!defined('REQ_TYP_FRE')) define('REQ_TYP_FRE', 1);	//Type requete	1 = Libre


class request {
	
	public $idproc = 0;				//id de procedure
	public $name = '';					//nom de procédure
	public $requete = '';				//requete SQL
	public $comment = '';				//commentaires sur la procedure
	public $autorisations = array();	//autorisation d'utilisation de la procedure
	public $parameters = '';			//parametres d'execution de la procedure
	public $num_classement = 0;		//Classement de la procedure
	public $p_mode = 'REQ_MOD_FRE';		//mode de procedure
	public $p_form = '';				//formulaire XML de description de la procedure
	
	//Constructeur
	public function __construct($idproc=0) {
		$this->idproc = intval($idproc);
		if ($this->idproc) {
			$this->load();	
		}
	}

	// charge une procedure a partir de la base.
	public function load(){
		$q = "select name, requete, comment, autorisations, parameters, num_classement, p_mode, p_form from procs where idproc = '".$this->idproc."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);

		$this->name = $obj->name;
		$this->requete = $obj->requete;
		$this->comment = $obj->comment;
		$this->autorisations = explode(' ', $obj->autorisations);
		$this->parameters = $obj->parameters;
		$this->p_type = $obj->p_type;
		$this->num_classement = $obj->num_classement; 
		$this->p_mode = $obj->p_mode;
		$this->p_form = $obj->p_form;
	}
	
	// enregistre une procedure en base.
	public function save(){
		if ($this->idproc) {
			$q = "update procs set ";
			$q.= "requete = '".addslashes($this->requete)."', ";
			$q.= "comment = '".addslashes($this->comment)."', ";
			$q.= "autorisation = '".implode(' ', $this->autorisations)."', ";
			$q.= "parameters ='".addslashes($this->parameters)."', ";
			$q.= "num_classement = '".$this->num_classement."', ";
			$q.= "p_mode = '".$this->p_mode."', ";
			$q.= "p_form = '".addslashes($this->p_form)."' ";
			$q.= "where idproc = '".$this->idproc."' ";
			pmb_mysql_query($q);
		} else {
			$q = "insert into procs set ";
			$q.= "requete = '".addslashes($this->requete)."', ";
			$q.= "comment = '".addslashes($this->comment)."', ";
			$q.= "autorisation = '".implode(' ', $this->autorisations)."', ";
			$q.= "parameters ='".addslashes($this->parameters)."', ";
			$q.= "num_classement = '".$this->num_classement."', ";
			$q.= "p_mode = '".$this->p_mode."', ";
			$q.= "p_form = '".addslashes($this->p_form)."' ";
			pmb_mysql_query($q);
			$this->idproc = pmb_mysql_insert_id();			
		}
	}

	//supprime une procedure de la base
	public function delete($idproc = 0) {
		$idproc = intval($idproc);
		if(!$idproc) $idproc = $this->idproc; 	
		$q = "delete from procs where idproc = '".$idproc."' ";
		pmb_mysql_query($q);
	}

	//retourne un form pour les autorisations d'une requete ou les autorisations par defaut si requete non creee
	static public function getAutorisationsForm() {
		global $charset;
		global $req_auth;
		$aut = array('1');
		
		//recuperation des utilisateurs
		$q = "SELECT userid, username FROM users ";
		$r = pmb_mysql_query($q);
		$p_user = array();
		while (($row=pmb_mysql_fetch_row($r))) {
			$p_user[$row[0]]=$row[1];
		}
		
		$form = "";
		$id_check_list='';
		foreach($p_user as $userid=>$username) {

			$form.= $req_auth;
			$form = str_replace('!!user_name!!', htmlentities($username,ENT_QUOTES, $charset), $form);
			$form = str_replace('!!user_id!!', $userid, $form);
			if (in_array($userid, $aut)) { 
				$chk = 'checked=\'checked\'';
			} else {
				$chk = '';
			}
			$form = str_replace('!!checked!!', $chk, $form);
			
			$id_check="user_aut[".$userid."]";
			if($id_check_list)$id_check_list.='|';
			$id_check_list.=$id_check;			
		}
		$form.="<input type='hidden' id='auto_id_list' name='auto_id_list' value='$id_check_list' >";
		return $form;
	}	
}

?>