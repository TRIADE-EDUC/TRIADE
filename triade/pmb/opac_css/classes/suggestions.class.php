<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions.class.php,v 1.42 2023/12/20 11:20:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/suggestions_map.class.php');
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
		$this->id_suggestion = intval($id_suggestion);
		if ($this->id_suggestion) {
			$this->load();	
		}
	}
	
	// charge une suggestion à partir de la base.
	public function load(){
		$q = "select * from suggestions left join explnum_doc_sugg on num_suggestion=id_suggestion where id_suggestion = '".$this->id_suggestion."' ";
		$r = pmb_mysql_query($q) ;
		
		if (pmb_mysql_num_rows($r)) {
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
	}

	public function get_content_form() {
	    global $msg, $charset;
	    global $opac_rgaa_active;
	    global $empr_mail;
	    global $opac_suggestion_search_notice_doublon;
	    global $opac_sugg_categ, $opac_sugg_categ_default, $acquisition_sugg_categ, $opac_sugg_localises;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    if ($opac_rgaa_active) {
	        $interface_content_form->set_grid_model('flat_column_25_right');
	    } else {
	        $interface_content_form->set_grid_model('tr_column_2_right');
	    }

        if($opac_suggestion_search_notice_doublon){
            $title_attributes = array('onkeyup' => "if(typeof input_field_change == 'function') {input_field_change();}");
        } else {
            $title_attributes = array();
        }
	    $interface_content_form->add_element('tit', 'empr_sugg_tit')
	    ->add_input_node('text', $this->titre)
	    ->set_size(50)
	    ->set_attributes($title_attributes);
	    $interface_content_form->add_element('aut', 'empr_sugg_aut')
	    ->add_input_node('text', $this->auteur)
	    ->set_size(50);
	    $interface_content_form->add_element('edi', 'empr_sugg_edi')
	    ->add_input_node('text', $this->editeur)
	    ->set_size(50);
	    $interface_content_form->add_element('code', 'empr_sugg_code')
	    ->add_input_node('text', $this->code)
	    ->set_size(20)
	    ->set_class('saisie-20em');
	    $interface_content_form->add_element('prix', 'empr_sugg_prix')
	    ->add_input_node('text', $this->prix)
	    ->set_size(20)
	    ->set_class('saisie-20em');
	    $interface_content_form->add_element('url_sug', 'empr_sugg_url')
	    ->add_input_node('text', $this->url_suggestion)
	    ->set_size(50);
	    $interface_content_form->add_element('comment', 'empr_sugg_comment')
	    ->add_textarea_node($this->commentaires, 50, 4)
	    ->set_attributes(array('wrap' => 'virtual'));
	    
	    $html = "
        <input type='text' id=\"date_publi\" name=\"date_publi\" value=\"".($this->date_publi != '0000-00-00' ? $this->date_publi : '')."\" size=\"50\" placeholder=\"".htmlentities($msg['format_date_input_text_placeholder'], ENT_QUOTES, $charset)."\">
		<input type='button' class='bouton' id='date_publi_sug' name='date_publi_sug' value='...' onClick=\"window.open('./select.php?what=calendrier&caller=empr_sugg&param1=date_publi&param2=date_publi&auto_submit=NO&date_anterieure=YES', 'date_publi', 'toolbar=no, dependent=yes, width=250,height=250, resizable=yes')\"/>";
	    $interface_content_form->add_element('date_publi', 'empr_sugg_datepubli')
	    ->add_html_node($html);
	    
	    $interface_content_form->add_element('nb', 'empr_sugg_qte')
	    ->add_input_node('integer', $this->nb)
	    ->set_size(5);
	    
	    if(!$_SESSION["id_empr_session"]) {
	        $interface_content_form->add_element('mail', 'empr_sugg_mail')
	        ->add_input_node('text', $empr_mail)
	        ->set_size(50);
	    }
	    
	    if ($opac_sugg_categ == '1' ) {
	        if($this->id_suggestion){
	            $default_categ = $this->num_categ;
	        } else {
	            if (suggestions_categ::exists($opac_sugg_categ_default) ){
	                $default_categ = $opac_sugg_categ_default;
	            } else {
	                $default_categ = '1';
	            }
	        }
	        //Selecteur de categories
	        if ($acquisition_sugg_categ != '1') {
	            $sel_categ="";
	        } else {
	            $tab_categ = suggestions_categ::getCategList();
	            $sel_categ = "<select class='saisie-25em' id='num_categ' name='num_categ' >";
	            foreach($tab_categ as $id_categ=>$lib_categ){
	                $sel_categ.= "<option value='".$id_categ."' ";
	                if ($id_categ==$default_categ) $sel_categ.= "selected='selected' ";
	                $sel_categ.= "> ";
	                $sel_categ.= htmlentities($lib_categ, ENT_QUOTES, $charset)."</option>";
	            }
	            $sel_categ.= "</select>";
	        }
	        $interface_content_form->add_element('num_categ', 'acquisition_categ')
	        ->add_html_node($sel_categ);
	    }
	    
	    // Localisation de la suggestion
	    if($_SESSION["id_empr_session"]) {
	        $requete = "SELECT * FROM empr WHERE id_empr=".$_SESSION["id_empr_session"];
	        $res = pmb_mysql_query($requete);
	        if($res) {
	            $empr = pmb_mysql_fetch_object($res);
	            if (!$empr->empr_location) $empr->empr_location=0 ;
	            $list_locs='';
	            $locs=new docs_location();
	            $list_locs=$locs->gen_combo_box_sugg($empr->empr_location,1,"");
	            if ($opac_sugg_localises==1) {
	                $interface_content_form->add_element('sugg_location_id', 'acquisition_location')
	                ->add_html_node($list_locs);
	            } elseif ($opac_sugg_localises==2) {
	                $docs_location = new  docs_location($empr->empr_location);
	                $element = $interface_content_form->add_element('sugg_location_id', 'acquisition_location');
	                $element->add_html_node($docs_location->libelle);
	                $element->add_input_node('hidden', $empr->empr_location);
	            }
	        }
	    }
	    
	    //Affichage du selecteur de source
	    $interface_content_form->add_element('sug_src', 'empr_sugg_src')
	    ->add_query_node('select', "select id_source, libelle_source from suggestions_source order by libelle_source", $this->sugg_src)
	    ->set_empty_option(0, $msg['empr_sugg_no_src'])
	    ->set_first_option(0, $msg["empr_sugg_no_src"]);
	    
	    $element = $interface_content_form->add_element('piece_jointe_sug', 'empr_sugg_piece_jointe');
	    if($this->get_explnum('nom')){
	        $element->add_html_node("<label>".htmlentities($this->get_explnum('nom'), ENT_QUOTES, $charset)."</label>");
	    } else {
	        $element->add_input_node('file');
	    }
	    
	    if(!$_SESSION["id_empr_session"]) {
	        $sug_verifcode = "
                <img src='./includes/imageverifcode.inc.php'>
				<br /><br /><h4><span>".$msg['empr_sugg_verifcode']."</span></h4><input type='text' class='subsform' name='sug_verifcode' value='' />
                ";
	        
	        $interface_content_form->add_element('sug_verifcode')
	        ->add_html_node($sug_verifcode);
	    }
	    
	    return $interface_content_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $tit, $edi, $aut, $code, $prix, $nb;
		global $url_sug, $comment, $date_publi, $sug_src;
		
		$this->titre = stripslashes($tit);
		$this->editeur = stripslashes($edi);
		$this->auteur = stripslashes($aut);
		$this->code = stripslashes($code);
		$prix = str_replace(',','.',$prix);
		if (is_numeric($prix)) $this->prix = $prix;
		$this->nb = ((int)$nb?(int)$nb:"1");
		$sug_map = new suggestions_map();
		$this->statut = $sug_map->getFirstStateId();
		$this->url_suggestion = stripslashes($url_sug);
		$this->commentaires = stripslashes($comment);
		$this->date_creation = today();
		$this->date_publi = stripslashes($date_publi);
		$this->sugg_src = $sug_src;
	}
	
	// enregistre une suggestion en base.
	public function save($explnum_doc=""){
		if(($this->titre == '') || ((($this->editeur == '') && ($this->auteur == '')) && (!$this->code) && (!$this->sugg_explnum && !$explnum_doc))) {
		    throw new Exception('Erreur de création suggestions');
		}
	
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
			pmb_mysql_query($q);
			
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
			pmb_mysql_query($q);
			$this->id_suggestion = pmb_mysql_insert_id();
		}
		
		if(!empty($explnum_doc)){
			$explnum_doc->save();
			$req = "insert into explnum_doc_sugg set 
				num_explnum_doc='".$explnum_doc->explnum_doc_id."',
				num_suggestion='".$this->id_suggestion."'";
			pmb_mysql_query($req);
		}
	}

	//Vérifie si une suggestion existe déjà en base
	public static function exists($origine, $titre, $auteur, $editeur, $isbn) {
		//suggestions identiques autorisées si complètement anonyme : pas identifié ou pas d'email saisi
		if(!trim($origine)){
			return 0;
		}
		$q = "select count(1) from suggestions_origine, suggestions where origine = '".addslashes($origine)."' and titre = '".$titre."' and id_suggestion = num_suggestion and auteur='".$auteur."' and editeur = '".$editeur."' and code = '".$isbn."' ";
		$q.= "and statut in (1,2,8) ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//supprime une suggestion de la base
	public static function delete($id_suggestion= 0) {
		$id_suggestion = intval($id_suggestion);
		if($id_suggestion) {
    		$q = "delete from suggestions where id_suggestion = '".$id_suggestion."' ";
    		pmb_mysql_query($q);
    		
    		$q = "delete ed,eds from explnum_doc ed join explnum_doc_sugg eds on ed.id_explnum_doc=eds.num_explnum_doc where eds.num_suggestion=$id_suggestion";
    		pmb_mysql_query($q);
		}
	}

	//Compte le nb de suggestion par statut pour une bibliothèque
	public static function getNbSuggestions($id_bibli=0, $statut='-1', $num_categ='-1', $mask="", $aq=0) {
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
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0); 
			
	}
	
	
	//Retourne une requete pour liste des suggestions par statut pour une bibliothèque
	public static function listSuggestions($id_bibli=0, $statut='-1', $num_categ='-1', $mask="", $debut=0, $nb_per_page=0, $aq=0, $order='',$location=0) {

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
	public static function listSuggestionsByOrigine($origine, $type_origine='1') {
		$q = "select * from suggestions_origine, suggestions where origine = '".addslashes($origine)."' ";
		if ($type_origine != '-1') $q.= "and type_origine = '".$type_origine."' ";
		$q.= "and id_suggestion=num_suggestion order by date_suggestion ";		
		return $q;				
	}

	//Retourne un tableau des origines pour une suggestion
	public function getOrigines($id_suggestion=0) {
		$tab_orig=array();
		$id_suggestion = intval($id_suggestion);
		if (!$id_suggestion) $id_suggestion = $this->id_suggestion;
		$q = "select * from suggestions_origine where num_suggestion=$id_suggestion order by date_suggestion, type_origine ";
		$r = pmb_mysql_query($q);
			
		for($i=0;$i<pmb_mysql_num_rows($r);$i++) {
			$tab_orig[] = pmb_mysql_fetch_array($r,PMB_MYSQL_ASSOC); 
		}
		return $tab_orig;
	}
	
	//optimization de la table suggestions
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE suggestions');
		return $opt;
	}
	
	//Récupération du docnum associé
	public function get_explnum($champ=''){
		$req = "select * from explnum_doc join explnum_doc_sugg on num_explnum_doc=id_explnum_doc where num_suggestion='".$this->id_suggestion."'";
		$res= pmb_mysql_query($req);
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
		global $msg,$charset;
		global $opac_sugg_categ;
		global $base_path;
		
		require_once($base_path.'/classes/suggestion_source.class.php');
		require_once($base_path.'/classes/suggestions_categ.class.php');
		
		$table= "
		<table style='width:100%; padding:5px' role='presentation'>
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
		if(empty($_SESSION["id_empr_session"])) {
		    global $mail;
		    $table.= "
			<tr>
				<td >".htmlentities($msg["empr_sugg_mail"], ENT_QUOTES, $charset)."</td>
				<td>".htmlentities($mail, ENT_QUOTES, $charset)."</td>
			</tr>";
		}
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
		global $include_path;
		global $msg, $charset;
		
		require_once($include_path."/mail.inc.php");
		
		//Informations emprunteur
		$empr="";
		if($typeEmpr==1){
			//Abonné
			$query="SELECT empr_prenom, empr_nom, empr_cb, empr_mail, empr_tel1, empr_tel2, empr_cp, empr_ville, location_libelle FROM empr, docs_location WHERE id_empr='$userIdOrEmail' and empr_location=idlocation";
			$result = pmb_mysql_query($query);
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
		$query = "SELECT DISTINCT location_libelle, email, nom, prenom, userid, user_email, date_format(sysdate(), '".$msg["format_date_heure"]."') AS aff_quand 
				FROM docs_location, users WHERE idlocation=deflt_docs_location AND user_email like('%@%') and user_alert_suggmail=1";
		if($sugg_location_id){
			$query.=" AND idlocation=".$sugg_location_id;
		}
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			while($row=pmb_mysql_fetch_object($result)){
				$mail_opac_user_suggestion = new mail_opac_user_suggestion();
				$mail_opac_user_suggestion->set_mail_to_id($row->userid);
				$mail_opac_user_suggestion->set_recipient($row);
				
				$mail_content = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>" ;
				//infos visiteur
				$mail_content .= $empr;
				$mail_content .= $tableHtml;
				$mail_content .= "<hr /></body></html>";
				$mail_opac_user_suggestion->set_mail_content($mail_content);
				$mail_opac_user_suggestion->send_mail();
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
			$terms=array();
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
		$res = pmb_mysql_query($query);
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