<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions.class.php,v 1.29 2018-07-04 13:10:28 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/notice_affichage.inc.php");

class suggestions{
	
	public $id_suggestion = 0;						//Identifiant de suggestion	
	public $titre  = '';							//Titre ouvrage
	public $editeur = '';							//Editeur ou diffuseur
	public $auteur = '';							//Auteur ouvrage
	public $code = '';								//ISBN, ISSN, ...				
	public $prix = '0.00';							//Prix indicatif
	public $nb = 1;								//Quantité à commander
	public $commentaires = '';						//Commentaires sur la suggestion
	public $date_creation = '0000-00-00';			
	public $date_decision = '0000-00-00';			//Date de la décision
	public $statut = '1';							//Statut de la suggestion 
	public $num_produit = 0;						//Identifiant du type de produit 
	public $num_entite = 0;						//Identifiant de l'entité sur laquelle est affectée la suggestion
	public $num_rubrique = 0;						//Identifiant de la rubrique budgetaire d'affectation
	public $num_fournisseur = 0;					//Identifiant du fournisseur associé
	public $num_notice = 0;						//Identifiant de notice si cataloguée			
	public $index_suggestion = '';					//Champ de recherche fulltext
	public $url_suggestion = '';					//URL
	public $num_categ = '1';						//Categorie associee a la suggestion
	public $sugg_location = 0;					//localisation
	public $date_publi='0000-00-00';			//date de publication
	public $sugg_src=0;						//source de la suggestion
	public $sugg_explnum=0;						//explnum attaché
	
	//Constructeur.	 
	public function __construct($id_suggestion=0) {
		$this->id_suggestion = $id_suggestion+0;
		if ($this->id_suggestion) {
			$this->load();	
		}
	}
	
	
	// charge une suggestion à partir de la base.
	public function load(){
	
		global $dbh;
		
		$q = "select * from suggestions left join explnum_doc_sugg on num_suggestion=id_suggestion where id_suggestion = '".$this->id_suggestion."' ";
		$r = pmb_mysql_query($q, $dbh) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->titre = $obj->titre;
		$this->editeur = $obj->editeur;
		$this->auteur = $obj->auteur;
		$this->code = $obj->code;
		$this->prix = $obj->prix;
		$this->nb = $obj->nb;
		$this->commentaires = $obj->commentaires;
		$this->date_creation = $obj->date_creation;
		$this->date_decision = $obj->date_decision;
		$this->statut = $obj->statut;
		$this->num_produit = $obj->num_produit;
		$this->num_entite = $obj->num_entite;
		$this->num_rubrique  = $obj->num_rubrique ;
		$this->num_fournisseur = $obj->num_fournisseur;
		$this->num_notice = $obj->num_notice;
		$this->index_suggestion = $obj->index_suggestion;
		$this->url_suggestion = $obj->url_suggestion;
		$this->num_categ = $obj->num_categ;
		$this->sugg_location = $obj->sugg_location;
		$this->date_publi = $obj->date_publication;
		$this->sugg_src = $obj->sugg_source;
		$this->sugg_explnum = $obj->num_explnum_doc;
	}

	
	// enregistre une suggestion en base.
	public function save($explnum_doc=""){
		
		global $dbh;
		
		if(($this->titre == '') || ((($this->editeur == '') && ($this->auteur == '')) && (!$this->code) && (!$this->sugg_explnum && !$explnum_doc))) 
			die("Erreur de création suggestions");
	
		if ($this->id_suggestion) {
			
			$q = "update suggestions set titre = '".addslashes($this->titre)."', editeur = '".addslashes($this->editeur)."', ";
			$q.= "auteur = '".addslashes($this->auteur)."', code = '".addslashes($this->code)."', prix = '".$this->prix."', nb = '".$this->nb."', commentaires = '".addslashes($this->commentaires)."', ";
			$q.= "date_creation = '".$this->date_creation."', date_decision = '".$this->date_decision."', statut = '".$this->statut."', ";
			$q.= "num_produit = '".$this->num_produit."', num_entite = '".$this->num_entite."', num_rubrique = '".$this->num_rubrique."', ";
			$q.= "num_fournisseur = '".$this->num_fournisseur."', num_notice = '".$this->num_notice."', "; 
			$q.= "index_suggestion = ' ".strip_empty_words($this->titre)." ".strip_empty_words($this->editeur)." ".strip_empty_words($this->auteur)." ".$this->code." ".strip_empty_words($this->commentaires)." ', ";
			$q.= "url_suggestion = '".addslashes($this->url_suggestion)."', "; 
			$q.= "num_categ = '".$this->num_categ."', ";
			$q.= "sugg_location = '".$this->sugg_location."', ";
			$q.= "date_publication = '".$this->date_publi."', ";
			$q.= "sugg_source = '".$this->sugg_src."' ";
			$q.= "where id_suggestion = '".$this->id_suggestion."' ";
			pmb_mysql_query($q, $dbh);
			
		} else {
			$q = "insert into suggestions set titre = '".addslashes($this->titre)."', editeur = '".addslashes($this->editeur)."', ";
			$q.= "auteur = '".addslashes($this->auteur)."', code = '".addslashes($this->code)."', prix = '".$this->prix."', nb = '".$this->nb."', commentaires = '".addslashes($this->commentaires)."', ";
			$q.= "date_creation = '".$this->date_creation."', date_decision = '".$this->date_decision."', statut = '".$this->statut."', ";
			$q.= "num_produit = '".$this->num_produit."', num_entite = '".$this->num_entite."', num_rubrique = '".$this->num_rubrique."', ";
			$q.= "num_fournisseur = '".$this->num_fournisseur."', num_notice = '".$this->num_notice."', "; 
			$q.= "index_suggestion = ' ".addslashes(strip_empty_words($this->titre)." ".strip_empty_words($this->editeur)." ".strip_empty_words($this->auteur)." ".$this->code." ".strip_empty_words($this->commentaires))." ', ";
			$q.= "url_suggestion = '".addslashes($this->url_suggestion)."', ";
			$q.= "num_categ = '".$this->num_categ."', ";
			$q.= "sugg_location = '".$this->sugg_location."', ";
			$q.= "date_publication = '".$this->date_publi."', ";
			$q.= "sugg_source = '".$this->sugg_src."' "; 			
			pmb_mysql_query($q, $dbh);
			$this->id_suggestion = pmb_mysql_insert_id($dbh);
		
		}
		
		if($explnum_doc){
			$explnum_doc->save();
			$req = "insert into explnum_doc_sugg set 
				num_explnum_doc='".$explnum_doc->explnum_doc_id."',
				num_suggestion='".$this->id_suggestion."'";
			pmb_mysql_query($req,$dbh);
		}
	}


	//Vérifie si une suggestion existe déjà en base
	public static function exists($origine, $titre, $auteur, $editeur, $isbn) {

		global $dbh;
		
		//suggestions identiques autorisées si complètement anonyme : pas identifié ou pas d'email saisi
		if(!trim($origine)){
			return 0;
		}
		
		$q = "select count(1) from suggestions_origine, suggestions where origine = '".$origine."' and titre = '".$titre."' and id_suggestion = num_suggestion and auteur='".$auteur."' and editeur = '".$editeur."' and code = '".$isbn."' ";
		$q.= "and statut in (1,2,8) ";
		$r = pmb_mysql_query($q, $dbh);
		return pmb_mysql_result($r, 0, 0);

	}


	//supprime une suggestion de la base
	public function delete($id_suggestion= 0) {
		
		global $dbh;

		if(!$id_suggestion) $id_suggestion = $this->id_suggestion; 	

		$q = "delete from suggestions where id_suggestion = '".$id_suggestion."' ";
		$r = pmb_mysql_query($q, $dbh);
				
	}


	//Compte le nb de suggestion par statut pour une bibliothèque
	public static function getNbSuggestions($id_bibli=0, $statut='-1', $num_categ='-1', $mask, $aq=0) {
		
		global $dbh;
		if (!$statut) $statut='-1';
		if ($statut == '-1') { 
			$filtre1 = '1';
		} elseif ($statut == $mask) {
			$filtre1 = "(statut & '".$mask."') = '".$mask."' ";
		} else {
			$filtre1 = "(statut & '".$mask."') = 0 and (statut & '".$statut."') = '".$statut."' ";
		}
		
		if ($num_categ == '-1') {
			$filtre2 = '1';
		} else {
			$filtre2 = "num_categ = '".$num_categ."' ";
		}
			
		if (!$id_bibli) $filtre3 = '1';
			else $filtre3.= "num_entite = '".$id_bibli."' ";
		
		if (!$aq) {
			$q = "select count(1) from suggestions where 1 ";
			$q.= "and ".$filtre1." and ".$filtre2." and ".$filtre3." "; 
		} else {
			$q = $aq->get_query_count("suggestions","concat(titre,' ',editeur,' ',auteur,' ',commentaires)","index_suggestion", "id_suggestion", $filtre1." and ".$filtre2." and ".$filtre3 );
		}
		$r = pmb_mysql_query($q, $dbh); 
		return pmb_mysql_result($r, 0, 0); 
			
	}
	
	
	//Retourne une requete pour liste des suggestions par statut pour une bibliothèque
	public static function listSuggestions($id_bibli=0, $statut='-1', $num_categ='-1', $mask, $debut=0, $nb_per_page=0, $aq=0, $order='',$location=0) {

		if ($statut == '-1') { 
			$filtre1 = '1';
		} elseif ($statut == $mask) {
			$filtre1 = "(statut & '".$mask."') = '".$mask."' ";
		} else {
			$filtre1 = "(statut & '".$mask."') = 0 and (statut & ".$statut.") = '".$statut."' ";
		}
			
		if ($num_categ == '-1') {
			$filtre2 = '1';
		} else {
			$filtre2 = "num_categ = '".$num_categ."' ";
		}

		if (!$id_bibli) $filtre3 = '1';
			else $filtre3.= "num_entite = '".$id_bibli."' ";

		if ($location == 0) {
			$filtre4 = '1';
		} else {
			$filtre4 = "sugg_location = '".$location."' ";
		}		
		if(!$aq) {
			
			$q = "select * from suggestions where 1 ";
			$q.= "and ".$filtre1." and ".$filtre2." and ".$filtre3." and ".$filtre4 ." ";
			if(!$order) $q.="order by statut, date_creation desc ";
				else $q.= "order by".$order." ";
			
		} else {
			
			$members=$aq->get_query_members("suggestions","concat(titre,' ',editeur,' ',auteur,' ',commentaires)","index_suggestion","id_suggestion", $filtre1." and ".$filtre2." and ".$filtre3." and ".$filtre4);
			if (!$order) {
				$q = "select *, ".$members["select"]." as pert from suggestions where ".$members["where"]." and ".$members["restrict"]." order by pert desc ";	
			} else {
				$q = "select *, ".$members["select"]." as pert from suggestions where ".$members["where"]." and ".$members["restrict"]." order by ".$order.", pert desc ";
			}
		}  
		
		if (!$debut && $nb_per_page) $q.= "limit ".$nb_per_page;
		if ($debut && $nb_per_page) $q.= "limit ".$debut.",".$nb_per_page;

		return $q;				
	}

	
	//Retourne  une requete pour liste des suggestions par origine 
	//type_origine: 0=utilisateur, 1=lecteur, 2=visiteur
	public static function listSuggestionsByOrigine($id_origine, $type_origine='1') { 
		
		$q = "select * from suggestions_origine, suggestions where origine = '".$id_origine."' ";
		if ($type_origine != '-1') $q.= "and type_origine = '".$type_origine."' ";
		$q.= "and id_suggestion=num_suggestion order by date_suggestion ";		
		return $q;				
	}

	
	//Retourne un tableau des origines pour une suggestion
	public function getOrigines($id_suggestion=0) {
		
		global $dbh;
		$tab_orig=array();
		if (!$id_suggestion) $id_suggestion = $this->id_suggestion;
		$q = "select * from suggestions_origine where num_suggestion=$id_suggestion order by date_suggestion, type_origine ";
		$r = pmb_mysql_query($q, $dbh);
			
		for($i=0;$i<pmb_mysql_num_rows($r);$i++) {
			$tab_orig[] = pmb_mysql_fetch_array($r,PMB_MYSQL_ASSOC); 
		}
		return $tab_orig;
	}
	
	
	//optimization de la table suggestions
	public function optimize() {
		
		global $dbh;
		
		$opt = pmb_mysql_query('OPTIMIZE TABLE suggestions', $dbh);
		return $opt;
				
	}
	

	//Récupération du docnum associé
	public function get_explnum($champ=''){
		global $dbh;
		
		$req = "select * from explnum_doc join explnum_doc_sugg on num_explnum_doc=id_explnum_doc where num_suggestion='".$this->id_suggestion."'";
		$res= pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($res)){
			$tab = pmb_mysql_fetch_array($res);
			switch($champ){				
				case 'id':
					return $tab['id_explnum_doc'];
					break;
				case 'nom':
					return $tab['explnum_doc_nomfichier'];
					break;
				case 'ext';
					return $tab['explnum_doc_extfichier'];
					break;
				case 'mime';
					return $tab['explnum_doc_mimetype'];
					break;	
			}
		}
		return 0;
	}
	
	public function get_table(){
		global $dbh;
		global $msg,$charset;
		global $opac_sugg_categ;
		global $base_path;
		
		require_once($base_path.'/classes/suggestion_source.class.php');
		require_once($base_path.'/classes/suggestions_categ.class.php');
		
		$table= "
		<table style='width:100%' cellpadding='5'>
			<tr>
				<td >".htmlentities($msg["empr_sugg_tit"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($this->titre, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td >".htmlentities($msg["empr_sugg_aut"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($this->auteur, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td >".htmlentities($msg["empr_sugg_edi"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($this->editeur, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td >".htmlentities($msg["empr_sugg_code"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($this->code, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td >".htmlentities($msg["empr_sugg_prix"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($this->prix, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td >".htmlentities($msg['empr_sugg_url'], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($this->url_suggestion, ENT_QUOTES, $charset)."</td>
			</tr>
			<tr>
				<td>".htmlentities($msg['empr_sugg_comment'], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($this->commentaires, ENT_QUOTES, $charset)."</td>
			</tr>";
		if ($opac_sugg_categ=='1') {
			$categ = new suggestions_categ($this->num_categ);
			$table.= "
			<tr>
				<td >".htmlentities($msg['acquisition_categ'], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($categ->libelle_categ, ENT_QUOTES, $charset)."</td>
			</tr>";
		}
		$table.= "
		<tr>
			<td >".htmlentities($msg["empr_sugg_datepubli"], ENT_QUOTES, $charset)."</td>
			<td>".htmlentities($this->date_publi, ENT_QUOTES, $charset)."</td>
		</tr>
		<tr>
			<td >".htmlentities($msg["empr_sugg_qte"], ENT_QUOTES, $charset)."</td>
			<td>".htmlentities($this->nb, ENT_QUOTES, $charset)."</td>
		</tr>";
		$source = new suggestion_source($this->sugg_src);
		$table.= "
		<tr>
			<td >".htmlentities($msg["empr_sugg_src"], ENT_QUOTES, $charset)."</td>
			<td>".htmlentities($source->libelle_source, ENT_QUOTES, $charset)."</td>
		</tr>";
		if($tmp=$this->get_explnum('nom')){
			$table.= "
			<tr>
				<td >".htmlentities($msg["empr_sugg_piece_jointe"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($tmp, ENT_QUOTES, $charset)."</td>
			</tr>";
		}
		$table.= "</table>";
		
		return $table;
	}
	
	public static function alert_mail_sugg_users_pmb($typeEmpr = 2, $userIdOrEmail = "", $tableHtml = "", $sugg_location_id = 0) {
		global $dbh;
		global $include_path;
		global $msg, $charset;
		
		require_once($include_path."/mail.inc.php");
		
		//Informations emprunteur
		$empr="";
		if($typeEmpr==1){
			//Abonné
			$query="SELECT empr_prenom, empr_nom, empr_cb, empr_mail, empr_tel1, empr_tel2, empr_cp, empr_ville, location_libelle FROM empr, docs_location WHERE id_empr='$userIdOrEmail' and empr_location=idlocation";
			$result = @pmb_mysql_query($query, $dbh);
			if($result && pmb_mysql_num_rows($result)){
				$row=pmb_mysql_fetch_object($result);
				$empr .= "<strong>".$row->empr_prenom." ".$row->empr_nom."</strong>
					<br /><i>".$row->empr_mail." / ".$row->empr_tel1." / ".$row->empr_tel2."</i>";
				if ($row->empr_cp || $row->empr_ville) $empr .= "<br /><u>".$row->empr_cp." ".$row->empr_ville."</u>";
				$empr .= "<hr />".$msg['situation'].": ".$row->location_libelle."<hr />";
			}
		}else{
			//Visiteur non authentifié
			$empr .= "<strong>".$msg["mail_sugg_non_empr"]."</strong>
					<br /><i>".$userIdOrEmail."</i><hr />";
		}
		//Biblios destinataires selon paramétrage et localisation de la suggestion
		$query = "SELECT DISTINCT location_libelle, email, nom, prenom, user_email, date_format(sysdate(), '".$msg["format_date_heure"]."') AS aff_quand 
				FROM docs_location, users WHERE idlocation=deflt_docs_location AND user_email like('%@%') and user_alert_suggmail=1";
		if($sugg_location_id){
			$query.=" AND idlocation=".$sugg_location_id;
		}
		$result = @pmb_mysql_query($query, $dbh);
		if($result && pmb_mysql_num_rows($result)){
			while($row=pmb_mysql_fetch_object($result)){
				$PMBuseremail="";
				$PMBusernom = $row->location_libelle;
				$PMBuserprenom = '';
				$PMBuseremail = $row->user_email;
				if (trim($PMBuseremail)) {
					$headers  = "MIME-Version: 1.0\n";
					$headers .= "Content-type: text/html; charset=".$charset."\n";
					$output_final = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>" ;
					//infos visiteur
					$output_final .= $empr;				
					$output_final .= $tableHtml;
					$output_final .= "<hr /></body></html> ";
					$res_envoi=mailpmb($row->nom." ".$row->prenom, $row->user_email,$msg["mail_sugg_obj"]." ".$row->aff_quand,$output_final,$PMBusernom, $PMBuseremail, $headers, "", "", 0);
				}
			}
		}					
	}
	
	public static function get_doublons(){
		global $msg, $tit, $code, $opac_suggestion_search_notice_doublon;
	
		$tit = trim($tit);
		$code = trim($code);
		if (!$tit && !$code) return '';
	
		$query_fields = array();
		if (strlen($code) > 2) {
			$terms[0] = $code;
			if (isEAN($code)) {
				//C'est un isbn ?
				if (isISBN($code)) {
					$rawisbn = preg_replace('/-|\.| /', '', $code);
					//On envoi tout ce qu'on sait faire en matiere d'ISBN, en raw et en formatte, en 10 et en 13
					$terms[1] = formatISBN($rawisbn, 10);
					$terms[2] = formatISBN($rawisbn, 13);
					$terms[3] = preg_replace('/-|\.| /', '', $terms[1]);
					$terms[4] = preg_replace('/-|\.| /', '', $terms[2]);
				}
			}
			else if (isISBN($code)) {
				$rawisbn = preg_replace('/-|\.| /', '', $code);
				//On envoi tout ce qu'on sait faire en matiere d'ISBN, en raw et en formatte, en 10 et en 13
				$terms[1] = formatISBN($rawisbn, 10);
				$terms[2] = formatISBN($rawisbn, 13);
				$terms[3] = preg_replace('/-|\.| /', '', $terms[1]);
				$terms[4] = preg_replace('/-|\.| /', '', $terms[2]);
			}
			foreach ($terms as $term){
				$query_fields[] = ' code like "' . addslashes($term) . '" ';
			}
		}
		if (strlen($tit) > 2) {
			$query_fields[] = ' tit1 like "' . $tit . '%" ';
				
			if($opac_suggestion_search_notice_doublon == 2) {
				$sugg= new suggest($tit);
				if(count($sugg->arrayResults)) {
					foreach ($sugg->arrayResults as $result) {
						$query_fields[] = ' tit1 like "' . addslashes($result['field_clean_content']) . '" ';
					}
				}
			}
		}
		if(!count($query_fields)) return '';
		$query = 'SELECT notice_id FROM notices where ' . implode('or', $query_fields) . ' order by tit1 limit 10';
		$display = '';
		$res = pmb_mysql_query($query, $dbh);
		if (pmb_mysql_num_rows($res)) {
			while ($row = pmb_mysql_fetch_object($res)) {
				$display.= aff_notice($row->notice_id);
			}
		}
		if ($display) {
			return '<h3>' . $msg['empr_sugg_notice_doublon'] . '</h3>' . $display;
		}
		return '';
	}
	
}
?>