<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sauv_lieu.class.php,v 1.15 2023/05/04 09:36:37 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Formulaire de gestion des lieux
global $include_path;
include ($include_path."/templates/lieux_form.tpl.php");
class sauv_lieu {

	//Données
	public $sauv_lieu_id; //Identifiant
	public $sauv_lieu_nom; //Nom du lieu
	public $sauv_lieu_url; //Chemin
	public $sauv_lieu_protocol; //Protocole
	public $sauv_lieu_host; //Serveur
	public $sauv_lieu_login; //Login
	public $sauv_lieu_password; //Mot de passe
	public $act; //Action

	public function __construct() {
		global $sauv_lieu_id; //Données reçues du formulaire
		global $sauv_lieu_nom;
		global $sauv_lieu_url;
		global $sauv_lieu_protocol;
		global $sauv_lieu_host;
		global $sauv_lieu_login;
		global $sauv_lieu_password;
		global $act;

		//Stockage des données reçues
		$this -> sauv_lieu_id = intval($sauv_lieu_id);
		$this -> sauv_lieu_nom = $sauv_lieu_nom;
		$this -> sauv_lieu_url = $sauv_lieu_url;
		$this -> sauv_lieu_protocol = $sauv_lieu_protocol;
		$this -> sauv_lieu_host = $sauv_lieu_host;
		$this -> sauv_lieu_login = $sauv_lieu_login;
		$this -> sauv_lieu_password = $sauv_lieu_password;
		$this -> act = $act;
	}
	
	public function verifName() {
		global $msg;
		// we must avoid duplication also when changing a pre-existents destination
		//$requete="select sauv_lieu_id from sauv_lieux where sauv_lieu_nom='".$this->sauv_lieu_nom."'";
		$requete="select sauv_lieu_id from sauv_lieux where (sauv_lieu_nom='".$this->sauv_lieu_nom."' and sauv_lieu_id !='".$this -> sauv_lieu_id."')";
		$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error());
		if (pmb_mysql_num_rows($resultat)!=0) {
			echo "<script>alert(\"".$msg["sauv_lieux_valid_form_error_duplicate_name"]."\"); history.go(-1);</script>";
			exit();
		}
	}
	
	//Traitement de l'action reçue du formulaire (à appeller juste après l'instanciation de la classe)
	//Renvoie le formulaire à afficher
	public function proceed() {

		global $first;

		switch ($this -> act) {
			//Enregistrer
			case "update" :
				//Si sauv_lieu_id vide alors création
				if ($this -> sauv_lieu_id == "") {
					$this->verifName();
					$requete = "insert into sauv_lieux (sauv_lieu_nom,sauv_lieu_url) values('','')";
					pmb_mysql_query($requete) or die(pmb_mysql_error());
					$this -> sauv_lieu_id = pmb_mysql_insert_id();
					$first="";
				}
				//Update avec les données rfeçues
				$this->verifName();
				$requete = "update sauv_lieux set sauv_lieu_nom='".$this -> sauv_lieu_nom."', sauv_lieu_url='".$this -> sauv_lieu_url."', sauv_lieu_protocol='".$this -> sauv_lieu_protocol."',sauv_lieu_host='".$this -> sauv_lieu_host."',sauv_lieu_login='".$this -> sauv_lieu_login."', sauv_lieu_password='".$this -> sauv_lieu_password."' where sauv_lieu_id=".$this -> sauv_lieu_id;
				pmb_mysql_query($requete) or die(pmb_mysql_error());
				$first="";
				break;
				//Supprimer
			case "delete" :
				$requete = "delete from sauv_lieux where sauv_lieu_id=".$this -> sauv_lieu_id;
				pmb_mysql_query($requete) or die(pmb_mysql_error());
				$this -> sauv_lieu_id = "";
				$first = 0;
				break;
				//Annuler
			case "cancel" :
				echo "<script>history.go(-2);</script>";
				exit();
				break;
				//Visualiser
			default :
				//Ne rien faire, le numéro de la fiche est déjà dans $this->sauv_lieu_id
		}
		return $this -> showForm();
	}

	//Préaparation du formulaire pour affiochage
	public function showForm() {
		global $form;
		global $first;
		global $msg;
		
		//Si première connexion
		if (!$first) {
			$form = "<h3>".$msg["sauv_lieux_sel_or_add"]."</h3>";
		} else {
			//Si identifiant non vide
			if ($this -> sauv_lieu_id) {
				//Récupération des données de la fiche
				$requete = "select sauv_lieu_nom,sauv_lieu_url,sauv_lieu_protocol, sauv_lieu_host, sauv_lieu_login, sauv_lieu_password from sauv_lieux where sauv_lieu_id=".$this -> sauv_lieu_id;
				$resultat = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat) != 0)
					list ($this -> sauv_lieu_nom, $this -> sauv_lieu_url, $this -> sauv_lieu_protocol, $this->sauv_lieu_host, $this -> sauv_lieu_login, $this -> sauv_lieu_password) = pmb_mysql_fetch_row($resultat);
				$form = str_replace("!!quel_lieu!!", $this -> sauv_lieu_nom, $form);
				$form = str_replace("!!delete!!", "<input type=\"submit\" value=\"".$msg["sauv_supprimer"]."\" onClick=\"if (confirm('".$msg["sauv_lieux_confirm_delete"]."')) { this.form.act.value='delete'; return true; } else { return false; }\" class=\"bouton\">", $form);
			} else {
				//Sinon : Nouvelle fiche
				$form = str_replace("!!quel_lieu!!", $msg["sauv_lieu_new"], $form);
				$form = str_replace("!!delete!!", "", $form);
			}
			$form = str_replace("!!sauv_lieu_id!!", $this -> sauv_lieu_id, $form);
			$form = str_replace("!!sauv_lieu_nom!!", $this -> sauv_lieu_nom ?? "", $form);
			$form = str_replace("!!sauv_lieu_url!!", $this -> sauv_lieu_url ?? "", $form);
			$form = str_replace("!!protocol_list!!", $this -> showSelectProtocol(), $form);

			$login = "<tr><th class='nobrd' colspan=2>".$msg["sauv_lieu_form_param_cnx"]."</th></tr>\n";
			$login.= "<tr><td class='nobrd'>".$msg["sauv_lieux_host"]."</td><td class='nobrd'><input type=\"text\" name=\"sauv_lieu_host\" value=\"".$this -> sauv_lieu_host."\" class=\"saisie-simple\"></td></tr>\n";
			$login.= "<tr><td class='nobrd'>".$msg["sauv_lieux_user"]."</td><td class='nobrd'><input type=\"text\" name=\"sauv_lieu_login\" value=\"".$this -> sauv_lieu_login."\" class=\"saisie-simple\"></td></tr>\n";
			$login.= "<tr><td class='nobrd'>".$msg["sauv_lieux_password"]."</td ><td class='nobrd'><input type=\"password\" name=\"sauv_lieu_password\" value=\"".$this -> sauv_lieu_password."\" class=\"saisie-simple\"></td></tr>\n";
			$login.= "<tr><td class='nobrd center' colspan=2><a href=\"\" onClick=\"callFtpTest(); return false\">".$msg["sauv_lieux_test_cnx"]."</a></td></tr>";

			$form = str_replace("!!login!!", $login, $form);
		}
		return $form;
	}

	//Affichage de la liste des lieux existants dans la base
	//linkToForm : true = rend la liste interactive avec le formulaire
	public function showTree($linkToForm = true) {
		global $msg;
		
		$tree = "<form><table>\n";
		$tree.= "<th class='brd'>".$msg["sauv_lieux_tree_title"]."</th>";

		//Récupération de la liste
		$requete = "select sauv_lieu_id, sauv_lieu_nom, sauv_lieu_protocol from sauv_lieux order by sauv_lieu_nom";
		$resultat = pmb_mysql_query($requete) or die(pmb_mysql_error());
		while ($res = pmb_mysql_fetch_object($resultat)) {
			$tree.= "<tr><td class='brd'>";
			switch ($res -> sauv_lieu_protocol) {
				case "ftp" :
					$tree.= "<img src=\"images/ftp.png\" border=0 class='center'>&nbsp;";
					break;
				case "file" :
					$tree.= "<img src=\"images/file.png\" border=0 class='center'>&nbsp;";
					break;
			}
			if ($linkToForm == true) {
				$tree.= "<a href=\"admin.php?categ=sauvegarde&sub=lieux&act=show&sauv_lieu_id=".$res -> sauv_lieu_id."&first=1\">";
			}
			$tree.= $res -> sauv_lieu_nom;
			if ($linkToForm == true) {
				$tree.= "</a>";
			}
			$tree.= "</td></tr>\n";
		}
		$tree.= "</table>";
		//Nouveau lieu
		if ($linkToForm) {
			//$tree.= "<a href=\"admin.php?categ=sauvegarde&sub=lieux&act=show&sauv_lieu_id=&first=1\">".$msg["sauv_lieux_tree_add"]."</a>";
			$tree.="
				<div class='center'>
				<input type=\"button\" value=\"".$msg["sauv_lieux_tree_add"]."\" 
					class=\"bouton\" 
					onClick=\"document.location='./admin.php?categ=sauvegarde&sub=lieux&act=show&sauv_lieu_id=&first=1';\" />
				</div></form>";
			
		}
		return $tree;
	}

	//Liste des protocols avec sélection par défaut
	public function showSelectProtocol() {
		global $msg;
		$values = array("file", "ftp");
		$toshow = array($msg["sauv_lieux_pro_list_file"],$msg["sauv_lieux_pro_list_ftp"]);
		$select = "<select name='sauv_lieu_protocol' class='saisie-simple'>\n";
		for ($i = 0; $i < count($values); $i ++) {
			$select.= "<option value='".$values[$i]."' ";
			if ($values[$i] == $this -> sauv_lieu_protocol)
				$select.= "selected";
			$select.= ">".$toshow[$i]."</option>\n";
		}
		$select.= "</select>";
		return $select;
	}
}
?>