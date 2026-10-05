<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titre_uniforme.class.php,v 1.71.4.1 2025/03/20 10:04:07 tsamson Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );

global $class_path;
require_once($class_path."/notice.class.php");
require_once($class_path."/authorities_collection.class.php");
require_once($class_path.'/authority.class.php');
require_once($class_path.'/marc_table.class.php');
require_once($class_path.'/vedette/vedette_composee.class.php');

/*
 * Classe recopiée de la gestion, allégée des méthodes inutiles en OPAC
 */
class titre_uniforme {
	
	// ---------------------------------------------------------------
	// propriétés de la classe
	// ---------------------------------------------------------------
	public $id; // MySQL id in table 'titres_uniformes'
	public $name; // titre_uniforme name
	public $tonalite; // tonalite de l'oeuvre musicale
	public $tonalite_marclist; // tonalite de l'oeuvre musicale (valeur issue de la liste music_key.xml)
	public $comment; // Commentaire, peut contenir du HTML
	public $import_denied = 0; // booléen pour interdire les modification depuis un import d'autorités
	public $form; // catégorie à laquelle appartient l'oeuvre (roman, pièce de théatre, poeme, ...)
	public $form_marclist; // catégorie à laquelle appartient l'oeuvre (roman, pièce de théatre, poeme, ...) (valeur issue de la liste music_form.xml)
	public $date; // date de création originelle de l'oeuvre (telle que saisie)
	public $date_date; // date formatée yyyy-mm-dd
	public $characteristic; // caractéristique permettant de distinguer une oeuvre d'une autre peuvre portant le même titre
	public $intended_termination; // complétude d'une oeuvre est finie ou se poursuit indéfiniment
	public $intended_audience; // categorie de personnes à laquelle l'oeuvre s'adresse
	public $context; // contexte historique, social, intellectuel, artistique ou autre au sein duquel l'oeuvre a été conçue
	public $coordinates; // coordonnees d'une oeuvre géographique (degrés, minutes et secondes de longitude et latitude ou angles de déclinaison et d'ascension des limiets de la zone représentée)
	public $equinox; // année de référence pour une carte ou un modèle céleste
	public $subject; // contenu de l'oeuvre et sujets qu'elle aborde
	public $place; // pays ou juridiction territoriale dont l'oeuvre est originaire
	public $history; // informations concernant l'histoire de l'oeuvre
	public $num_author; // identifiant de l'auteur principal de l'oeuvre
	private  $display; // usable form for displaying ( _name_ (_date_) / _author_name_ _author_rejete_ )
	private  $libelle;
	public $tu_isbd; // affichage isbd du titre uniforme AFNOR Z 44-061 (1986),
	private $responsabilites; // Auteurs répétables
	public $enrichment = null; // Enrichissements
	public static $marc_key;
	public static $marc_form;
	public $oeuvre_nature; // Nature de l'oeuvre
	public $oeuvre_nature_name; // Label de la Nature de l'oeuvre
	public $oeuvre_nature_nature; // Nature de la nature de l'oeuvre	
	public $oeuvre_type; // Type de l'oeuvre
	public $oeuvre_type_name; // Label du Type de l'oeuvre
	public $oeuvre_parent_expressions; // tableau des oeuvres dont le titre uniforme est l'expression
	public $other_links; // tableau des oeuvres liées
	public $oeuvre_events; // Evènements de l'oeuvre
	public $num_statut = 1;
	protected $p_perso;
	public $authors = null;
	public $recursif = 0;
	public $distrib = [];
	public $ref = [];
	public $subdiv = [];
	/**
	 * @var titre_uniforme
	 */
	private $oeuvre_parent_expressions_datas; // Tableau des données des oeuvres dont le titre uniforme est l'expression
	/**
	 * @var titre_uniforme
	 */
	private $oeuvre_events_datas; // Tableau des données des évenement de l'oeuvre
	/**
	 * @var titre_uniforme
	 */
	private $other_links_datas; // Tableau des données des oeuvres liées
	protected $sorted_responsabilities; //Tableau des responsabilité du titre uniforme (triées et dédoublonnées)
	protected $oeuvre_expressions; // tableau des expressions de l'oeuvre
	protected $oeuvre_expressions_datas; // Tableau des données des expressions de l'oeuvre
	
	protected $display_tonalite; //Affichage formaté de la tonalité selon la marc_list music_key.xml
	// ---------------------------------------------------------------
	// titre_uniforme($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id = 0, $recursif = 0) {
		$this->id = intval($id);
		if($this->id) {
			// on cherche à atteindre une notice existante
			$this->recursif=intval($recursif);
		} else {
			$this->recursif=0;
		}
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	// getData() : récupération infos titre_uniforme
	// ---------------------------------------------------------------
	public function getData() {
		$this->name = '';
		$this->tonalite = '';
		$this->tonalite_marclist = '';
		$this->comment = '';
		$this->distrib = array ();
		$this->ref = array ();
		$this->subdiv = array ();
		$this->import_denied = 0;
		$this->form = '';
		$this->form_marclist = '';
		$this->date = '';
		$this->date_date = '';
		$this->characteristic = '';
		$this->intended_termination = '';
		$this->intended_audience = '';
		$this->context = '';
		$this->coordinates = '';
		$this->equinox = '';
		$this->subject = '';
		$this->place = '';
		$this->history = '';
		$this->num_author = '';
		$this->oeuvre_nature = '';
		$this->oeuvre_nature_nature = '';
		$this->oeuvre_type = '';
		$this->num_statut = 1;
		if ($this->id) {
			$requete = "SELECT * FROM titres_uniformes WHERE tu_id='" . addslashes ( $this->id ) . "' LIMIT 1 ";
			$result = @pmb_mysql_query ( $requete );
			if (pmb_mysql_num_rows ( $result )) {
				$temp = pmb_mysql_fetch_object ( $result );
				$this->id = $temp->tu_id;
				$this->name = $temp->tu_name;
				$this->tonalite = $temp->tu_tonalite;
				$this->tonalite_marclist = $temp->tu_tonalite_marclist;
				$this->comment = $temp->tu_comment;
				$this->import_denied = $temp->tu_import_denied;
				$this->form = $temp->tu_forme;
				$this->form_marclist = $temp->tu_forme_marclist;
				$this->date = $temp->tu_date;
				$this->date_date = $temp->tu_date_date;
				$this->characteristic = $temp->tu_caracteristique;
				$this->intended_termination = $temp->tu_completude;
				$this->intended_audience = $temp->tu_public;
				$this->context = $temp->tu_contexte;
				$this->coordinates = $temp->tu_coordonnees;
				$this->equinox = $temp->tu_equinoxe;
				$this->subject = $temp->tu_sujet;
				$this->place = $temp->tu_lieu;
				$this->history = $temp->tu_histoire;
				$this->num_author = $temp->tu_num_author;
				$this->oeuvre_nature = $temp->tu_oeuvre_nature;
				$this->oeuvre_nature_nature = $temp->tu_oeuvre_nature_nature;
				$this->oeuvre_type = $temp->tu_oeuvre_type;
				$this->num_statut = $this->get_authority()->get_num_statut();
				
				$mc_oeuvre_type = marc_list_collection::get_instance('oeuvre_type');
				$this->oeuvre_type_name = array_key_exists($this->oeuvre_type, $mc_oeuvre_type->table) ? $mc_oeuvre_type->table[$this->oeuvre_type] : "";
				$mc_oeuvre_nature = marc_list_collection::get_instance('oeuvre_nature');
				$this->oeuvre_nature_name =  array_key_exists($this->oeuvre_nature, $mc_oeuvre_nature->table) ? $mc_oeuvre_nature->table[$this->oeuvre_nature] : "";
				
				$requete = "SELECT * FROM tu_distrib WHERE distrib_num_tu='$this->id' order by distrib_ordre";
				$result = pmb_mysql_query ( $requete );
				if (pmb_mysql_num_rows ( $result )) {
					while ( ($param = pmb_mysql_fetch_object ( $result )) ) {
						$this->distrib [] ["label"] = $param->distrib_name;
					}
				}
				$requete = "SELECT *  FROM tu_ref WHERE ref_num_tu='$this->id' order by ref_ordre";
				$result = pmb_mysql_query ( $requete );
				if (pmb_mysql_num_rows ( $result )) {
					while ( ($param = pmb_mysql_fetch_object ( $result )) ) {
						$this->ref [] ["label"] = $param->ref_name;
					}
				}
				$requete = "SELECT *  FROM tu_subdiv WHERE subdiv_num_tu='$this->id' order by subdiv_ordre";
				$result = pmb_mysql_query ( $requete );
				if (pmb_mysql_num_rows ( $result )) {
					while ( ($param = pmb_mysql_fetch_object ( $result )) ) {
						$this->subdiv [] ["label"] = $param->subdiv_name;
					}
				}
				// recuperation des oeuvres liees
				$this->get_other_links_datas();
			} else {
				// pas trouvé avec cette clé
				$this->id = 0;
			}
		}
	}
	
	public function get_authors() {
	    if($this->authors === null){

	        global $fonction_auteur;

	        $responsabilites = array() ;
	        $auteurs = array() ;

	        $this->authors["responsabilites"] = array() ;
	        $this->authors["auteurs"] = array() ;

	        $this->sorted_responsabilities = array(
	            'authors' => array(),
	            'performers' => array()
	        );

	        $query = "
                SELECT author_id, responsability_tu_fonction, responsability_tu_type, id_responsability_tu 
                FROM responsability_tu, authors 
                WHERE responsability_tu_num = $this->id 
                AND responsability_tu_author_num = author_id 
                ORDER BY responsability_tu_type, responsability_tu_ordre 
            ";

	        $result = pmb_mysql_query($query);
	        $tab_resp = pmb_mysql_fetch_all($result);

	        $resp_tu_filter = array_filter($tab_resp, function ($resp) {
	            return intval($resp[2]) == 0;
	        });

            $resp_tu_interpreter_filter = array_filter($tab_resp, function ($resp) {
                return intval($resp[2]) != 0;
	        });

            $tab_tu = vedette_composee::get_vedette_id_from_array(array_column($resp_tu_filter, 3), TYPE_TU_RESPONSABILITY);
            $tab_tu_interpreter = vedette_composee::get_vedette_id_from_array(array_column($resp_tu_interpreter_filter, 3), TYPE_TU_RESPONSABILITY_INTERPRETER);

            $rqt = pmb_mysql_query($query);
            while ($resp_tu = pmb_mysql_fetch_object($rqt)) {
	            $responsabilites[] = $resp_tu->responsability_tu_type;
	            if ($resp_tu->responsability_tu_type == 0) {
	                $qualif_id = $tab_tu[$resp_tu->id_responsability_tu] ?? 0;
	            } else {
	                $qualif_id = $tab_tu_interpreter[$resp_tu->id_responsability_tu] ?? 0;
	            }

	            $qualif = null;
	            if($qualif_id){
	                $qualif = new vedette_composee($qualif_id);
	            }

	            $fonction_label = '';
	            if (!empty($resp_tu->responsability_tu_fonction) && isset($fonction_auteur[$resp_tu->responsability_tu_fonction])) {
	                $fonction_label = $fonction_auteur[$resp_tu->responsability_tu_fonction];
	            }
	            $data = array(
	                'id' => $resp_tu->author_id,
	                'id_responsability_tu' => $resp_tu->id_responsability_tu,
	                'fonction' => $resp_tu->responsability_tu_fonction,
	                'fonction_label' => $fonction_label,
	                'qualif' => $qualif,
	                'qualif_label' => ($qualif ? $qualif->get_label() : ''),
	                'responsability' => $resp_tu->responsability_tu_type,
	                'objet' => authorities_collection::get_authority(AUT_TABLE_AUTHORS, $resp_tu->author_id)
	            ) ;
	            $auteurs[] = $data;
	            $data['attributes'][] = array(
	                'fonction' => $data['fonction'],
	                'fonction_label' => $data['fonction_label'],
	                'qualif' => $data['qualif'],
	                'qualif_label' => $data['qualif_label']
	            );
	            unset($data['fonction']);
	            unset($data['fonction_label']);
	            unset($data['qualif']);
	            unset($data['qualif_label']);
	            if (!$resp_tu->responsability_tu_type) {
	                if (!isset($this->sorted_responsabilities['authors'][$data['id']])) {
	                    $this->sorted_responsabilities['authors'][$data['id']] = $data;
	                } else {
	                    $this->sorted_responsabilities['authors'][$data['id']]['attributes'][] = $data['attributes'][0];
	                }
	            } else {
	                if (!isset($this->sorted_responsabilities['performers'][$data['id']])) {
	                    $this->sorted_responsabilities['performers'][$data['id']] = $data;
	                } else {
	                    $this->sorted_responsabilities['performers'][$data['id']]['attributes'][] = $data['attributes'][0];
	                }
	            }
	        }
	        $this->authors["responsabilites"] = $responsabilites ;
	        $this->authors["auteurs"] = $auteurs ;
	    }
	    return $this->authors;
	}
	
	// ---------------------------------------------------------------
	// print_resume($level) : affichage d'informations sur le titre uniforme
	// ---------------------------------------------------------------
	public function print_resume($level = 2) {
		global $msg, $charset;
		if (! $this->id)
			return;
			
			// adaptation par rapport au niveau de détail souhaité
		switch ($level) {
			// case x :
			case 2 :
			default :
				global $titre_uniforme_level2_display;
				$titre_uniforme_display = $titre_uniforme_level2_display;
				break;
		}
		$print = $titre_uniforme_display;
		
		$print_distrib = $print_ref = $print_subdiv = '';
		foreach ( $this->distrib as $field ) {
			if ($print_distrib)
				$print_distrib .= "; ";
			$print_distrib .= $field ["label"];
		}
		foreach ( $this->ref as $field ) {
			if ($print_ref)
				$print_ref .= "; ";
			$print_ref .= $field ["label"];
		}
		foreach ( $this->subdiv as $field ) {
			if ($print_subdiv)
				$print_subdiv .= "; ";
			$print_subdiv .= $field ["label"];
		}
		
		// remplacement des champs
		$print = str_replace ( "!!id!!", $this->id, $print );
		$print = str_replace ( "!!name!!", $this->name, $print );
		
		$auteurs = "";
		$this->get_responsabilites();
		if (isset ( $this->responsabilites ["auteurs"] ) && count ( $this->responsabilites ["auteurs"] )) {
			foreach ( $this->responsabilites ["auteurs"] as $id => $responsable ) {
				if (is_object ( $responsable ["objet"] )) {
					
					if ($id > 0)
						$auteurs .= " / "; // entre auteurs
					$auteurs .= "<a href='index.php?lvl=author_see&id=" . $responsable ["objet"]->id . "'>" . htmlentities ( $responsable ["objet"]->display, ENT_QUOTES, $charset ) . "</a>";
				}
			}
		}
		
		$print = str_replace ( "!!auteur!!", ($auteurs ? "<p>" . $msg ["aut_oeuvre_form_auteur"] . " : " . $auteurs . "</p>" : ""), $print );
		$print = str_replace ( "!!forme!!", ($this->form ? "<p>" . $msg ["aut_oeuvre_form_forme"] . " : " . htmlentities ( $this->form, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!forme_list!!", ($this->get_form_label () ? "<p>" . $msg ["aut_oeuvre_form_forme_list"] . " : " . htmlentities ( $this->get_form_label (), ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!date!!", ($this->date ? "<p>" . $msg ["aut_oeuvre_form_date"] . " : " . htmlentities ( $this->date, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!sujet!!", ($this->subject ? "<p>" . $msg ["aut_oeuvre_form_sujet"] . " : " . htmlentities ( $this->subject, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!lieu!!", ($this->place ? "<p>" . $msg ["aut_oeuvre_form_lieu"] . " : " . htmlentities ( $this->place, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$completude = '';
		if ($this->intended_termination == 1) {
			$completude = $msg['aut_oeuvre_form_completude_finished'];
		} elseif ($this->intended_termination == 2) {
			$completude = $msg['aut_oeuvre_form_completude_infinite'];
		}
		$print = str_replace ( "!!completude!!", ($completude ? "<p>" . $msg ["aut_oeuvre_form_completude"] . " : " . htmlentities ( $completude, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!public!!", ($this->intended_audience ? "<p>" . $msg ["aut_oeuvre_form_public"] . " : " . htmlentities ( $this->intended_audience, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!histoire!!", ($this->history ? "<p>" . $msg ["aut_oeuvre_form_histoire"] . " : " . htmlentities ( $this->history, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!contexte!!", ($this->context ? "<p>" . $msg ["aut_oeuvre_form_contexte"] . " : " . htmlentities ( $this->context, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!distribution!!", ($print_distrib ? "<p>" . $msg ["aut_oeuvre_form_distribution"] . " : " . htmlentities ( $print_distrib, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!reference!!", ($print_ref ? "<p>" . $msg ["aut_oeuvre_form_reference"] . " : " . htmlentities ( $print_ref, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!tonalite!!", ($this->tonalite ? "<p>" . $msg ["aut_oeuvre_form_tonalite"] . " : " . htmlentities ( $this->tonalite, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!tonalite_list!!", ($this->get_key_label () ? "<p>" . $msg ["aut_oeuvre_form_tonalite_list"] . " : " . htmlentities ( $this->get_key_label (), ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!subdivision!!", ($print_subdiv ? "<p>" . $msg ["aut_oeuvre_form_subdivision"] . " : " . htmlentities ( $print_subdiv, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!coordonnees!!", ($this->coordinates ? "<p>" . $msg ["aut_oeuvre_form_coordonnees"] . " : " . htmlentities ( $this->coordinates, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!equinoxe!!", ($this->equinox ? "<p>" . $msg ["aut_oeuvre_form_equinoxe"] . " : " . htmlentities ( $this->equinox, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!caracteristique!!", ($this->characteristic ? "<p>" . $msg ["aut_oeuvre_form_caracteristique"] . " : " . htmlentities ( $this->characteristic, ENT_QUOTES, $charset ) . "</p>" : ""), $print );
		$print = str_replace ( "!!aut_comment!!", $this->comment, $print );
		
		return $print;
	}
	public static function gen_input_selection($label, $form_name, $item, $values, $what_sel, $class = 'saisie-80em') {
		global $msg;
		$select_prop = "scrollbars=yes, toolbar=no, dependent=yes, resizable=yes";
		$link = "'./select.php?what=$what_sel&caller=$form_name&p1=f_" . $item . "_code!!num!!&p2=f_" . $item . "!!num!!&deb_rech='+" . pmb_escape () . "(this.form.f_" . $item . "!!num!!.value), '$what_sel', 400, 400, -2, -2, '$select_prop'";
		$size_item = strlen ( $item ) + 2;
		$script_js = "
		<script>
		function fonction_selecteur_" . $item . "() {
			var nom='f_" . $item . "';
	        name=this.getAttribute('id').substring(4);  
			name_id = name.substr(0,nom.length)+'_code'+name.substr(nom.length);
			openPopUp('./select.php?what=$what_sel&caller=$form_name&p1='+name_id+'&p2='+name, '$what_sel', 400, 400, -2, -2, '$select_prop');
	        
	    }
	    function fonction_raz_" . $item . "() {
	        name=this.getAttribute('id').substring(4);
			name_id = name.substr(0,$size_item)+'_code'+name.substr($size_item);
	        document.getElementById(name).value='';
			document.getElementById(name_id).value='';
	    }
	    function add_" . $item . "() {
	        template = document.getElementById('add" . $item . "');
	        " . $item . "=document.createElement('div');
	        " . $item . ".className='row';
	
	        suffixe = eval('document." . $form_name . ".max_" . $item . ".value')
	        nom_id = 'f_" . $item . "'+suffixe
	        f_" . $item . " = document.createElement('input');
	        f_" . $item . ".setAttribute('name',nom_id);
	        f_" . $item . ".setAttribute('id',nom_id);
	        f_" . $item . ".setAttribute('type','text');
	        f_" . $item . ".className='$class';
	        f_" . $item . ".setAttribute('value','');
			f_" . $item . ".setAttribute('completion','" . $item . "');
	        
			id = 'f_" . $item . "_code'+suffixe
			f_" . $item . "_code = document.createElement('input');
			f_" . $item . "_code.setAttribute('name',id);
	        f_" . $item . "_code.setAttribute('id',id);
	        f_" . $item . "_code.setAttribute('type','hidden');
			f_" . $item . "_code.setAttribute('value','');
	 
	        del_f_" . $item . " = document.createElement('input');
	        del_f_" . $item . ".setAttribute('id','del_f_" . $item . "'+suffixe);
	        del_f_" . $item . ".onclick=fonction_raz_" . $item . ";
	        del_f_" . $item . ".setAttribute('type','button');
	        del_f_" . $item . ".className='bouton';
	        del_f_" . $item . ".setAttribute('readonly','');
	        del_f_" . $item . ".setAttribute('value','" . $msg ["raz"] . "');
	
	        sel_f_" . $item . " = document.createElement('input');
	        sel_f_" . $item . ".setAttribute('id','sel_f_" . $item . "'+suffixe);
	        sel_f_" . $item . ".setAttribute('type','button');
	        sel_f_" . $item . ".className='bouton';
	        sel_f_" . $item . ".setAttribute('readonly','');
	        sel_f_" . $item . ".setAttribute('value','" . $msg ["parcourir"] . "');
	        sel_f_" . $item . ".onclick=fonction_selecteur_" . $item . ";
	
	        " . $item . ".appendChild(f_" . $item . ");
			" . $item . ".appendChild(f_" . $item . "_code);
	        space=document.createTextNode(' ');
	        " . $item . ".appendChild(space);
	        " . $item . ".appendChild(del_f_" . $item . ");
	        " . $item . ".appendChild(space.cloneNode(false));
	        if('$what_sel')" . $item . ".appendChild(sel_f_" . $item . ");
	        
	        template.appendChild(" . $item . ");
	
	        document." . $form_name . ".max_" . $item . ".value=suffixe*1+1*1 ;
	        ajax_pack_element(f_" . $item . ");
	    }
		</script>";
		
		// template de zone de texte pour chaque valeur
		$aff = "
		<div class='row'>
		<input type='text' class='$class' id='f_" . $item . "!!num!!' name='f_" . $item . "!!num!!' value=\"!!label_element!!\" autfield='f_" . $item . "_code!!num!!' completion=\"" . $item . "\" />
		<input type='hidden' id='f_" . $item . "_code!!num!!' name='f_" . $item . "_code!!num!!' value='!!id_element!!'>
		<input type='button' class='bouton' value='" . $msg ["raz"] . "' onclick=\"this.form.f_" . $item . "!!num!!.value='';this.form.f_" . $item . "_code!!num!!.value=''; \" />
		!!bouton_parcourir!!
		!!bouton_ajouter!!
		</div>\n";
		if ($what_sel)
			$bouton_parcourir = "<input type='button' class='bouton' value='" . $msg ["parcourir"] . "' onclick=\"openPopUp(" . $link . ")\" />";
		else
			$bouton_parcourir = "";
		$aff = str_replace ( '!!bouton_parcourir!!', $bouton_parcourir, $aff );
		
		$template = $script_js . "<div id=add" . $item . "' class='row'>";
		$template .= "<div class='row'><label for='f_" . $item . "' class='etiquette'>" . $label . "</label></div>";
		$num = 0;
		if (! $values [0])
			$values [0] = array (
					"id" => "",
					"label" => "" 
			);
		foreach ( $values as $value ) {
			
			$label_element = $value ["label"];
			$id_element = $value ["id"];
			
			$temp = str_replace ( '!!id_element!!', $id_element, $aff );
			$temp = str_replace ( '!!label_element!!', $label_element, $temp );
			$temp = str_replace ( '!!num!!', $num, $temp );
			
			if (! $num)
				$temp = str_replace ( '!!bouton_ajouter!!', " <input class='bouton' value='" . $msg ["req_bt_add_line"] . "' onclick='add_" . $item . "();' type='button'>", $temp );
			else
				$temp = str_replace ( '!!bouton_ajouter!!', "", $temp );
			$template .= $temp;
			$num ++;
		}
		$template .= "<input type='hidden' name='max_" . $item . "' value='$num'>";
		
		$template .= "</div><div id='add" . $item . "'/>
		</div>";
		return $template;
	}
	
	// ---------------------------------------------------------------
	// search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	public function search_form() {
		global $user_query;
		global $msg;
		$user_query = str_replace ( '!!user_query_title!!', $msg [357] . " : " . $msg ["aut_menu_titre_uniforme"], $user_query );
		$user_query = str_replace ( '!!action!!', './autorites.php?categ=titres_uniformes&sub=reach&id=', $user_query );
		$user_query = str_replace ( '!!add_auth_msg!!', $msg ["aut_titre_uniforme_ajouter"], $user_query );
		$user_query = str_replace ( '!!add_auth_act!!', './autorites.php?categ=titres_uniformes&sub=titre_uniforme_form', $user_query );
		$user_query = str_replace ( '<!-- lien_derniers -->', "<a href='./autorites.php?categ=titres_uniformes&sub=titre_uniforme_last'>" . $msg ["aut_titre_uniforme_derniers_crees"] . "</a>", $user_query );
		print pmb_bidi ( $user_query );
	}
	
	// ---------------------------------------------------------------
	// do_isbd() : génération de l'isbd du titre uniforme (AFNOR Z 44-061 de 1986)
	// ---------------------------------------------------------------
	public function do_isbd_old() {
		$this->tu_isbd = "";
		if (! $this->id)
			return;
		$this->get_responsabilites();
		$as = array_keys ( $this->responsabilites ["responsabilites"], "0" );
		for($i = 0; $i < count ( $as ); $i ++) {
			$indice = $as [$i];
			$auteur_0 = $this->responsabilites ["auteurs"] [$indice];
			$auteur = authorities_collection::get_authority('author', $auteur_0["id"]);
			if($i>0)$this->tu_isbd.= " / ";
			$this->tu_isbd .= $auteur->display . ". ";
		}
		if ($i)
			$this->tu_isbd .= ". ";
			/*
		 * if($this->num_author){
		 * $tu_auteur = new auteur ($this->num_author);
		 * $this->tu_isbd. = $tu_auteur->display.". ";
		 * }
		 */
		if ($this->name) {
			$this->tu_isbd .= $this->name;
		}
		
		return $this->tu_isbd;
	}
	public function get_enrichment() {
		if ($this->enrichment === null) {
			return ""; // tu_enrichment n'existe pas ...
			$enrichment = "";
			$requete = "select tu_enrichment from titres_uniformes where tu_id=" . $this->id;
			print $requete;
			$resultat = pmb_mysql_query ( $requete );
			$enrichment = pmb_mysql_result ( $resultat, 0, 0 );
			if ($enrichment) {
				$enrichment = unserialize ( $enrichment );
			}
			$this->enrichment = $enrichment;
		}
		return $this->enrichment;
	}
	public static function get_marc_key() {
		if (empty(titre_uniforme::$marc_key)) {
			titre_uniforme::$marc_key = new marc_list ( "music_key" );
		}
		return titre_uniforme::$marc_key;
	}
	public static function get_marc_form() {
	    if (empty(titre_uniforme::$marc_form)) {
			titre_uniforme::$marc_form = new marc_list ( "music_form" );
		}
		return titre_uniforme::$marc_form;
	}
	public function get_key_label() {
		if ($this->tonalite_marclist) {
			return titre_uniforme::get_marc_key ()->table [$this->tonalite_marclist];
		}
	}
	public function get_form_label() {
		if ($this->form_marclist) {
			return titre_uniforme::get_marc_form ()->table [$this->form_marclist];
		}
	}
	
	/**
	 * Renvoie les données des oeuvres dont le titre uniforme est l'expression
	 * @return titre_uniforme Tableau de titre uniformes
	 */
	public function get_oeuvre_parent_expressions_datas() {
		if (!is_array($this->oeuvre_parent_expressions_datas) || !count($this->oeuvre_parent_expressions_datas)) {
			$this->oeuvre_parent_expressions_datas = array();
			$this->get_oeuvre_parent_expressions();
			if (is_array($this->oeuvre_parent_expressions) && count($this->oeuvre_parent_expressions)) {
				foreach ($this->oeuvre_parent_expressions as $oeuvre_parent_expression) {
					//$this->oeuvre_parent_expressions_datas[] = new authority(0, $oeuvre_parent_expression['to_id'], AUT_TABLE_TITRES_UNIFORMES);
					$this->oeuvre_parent_expressions_datas[] = authorities_collection::get_authority('authority', 0, ['num_object' => $oeuvre_parent_expression['to_id'], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
				}
			}
		}
		return $this->oeuvre_parent_expressions_datas;
	}


	/**
	 * Renvoie les données des expressions de l'oeuvre
	 * @return titre_uniforme Tableau de titre uniformes
	 */
	public function get_oeuvre_expressions_datas() {
	    if (!isset($this->oeuvre_expressions_datas)) {
			$this->oeuvre_expressions_datas = array();
			if (is_array($this->get_oeuvre_expressions())) {
				foreach ($this->get_oeuvre_expressions() as $oeuvre_expression) {
				    if (!isset($this->oeuvre_expressions_datas[$oeuvre_expression['type']]['label'])) $this->oeuvre_expressions_datas[$oeuvre_expression['type']]['label'] = $oeuvre_expression['type_name'];
					//$this->oeuvre_expressions_datas[] = new authority(0, $oeuvre_expression['to_id'], AUT_TABLE_TITRES_UNIFORMES);
				    $this->oeuvre_expressions_datas[$oeuvre_expression['type']]['elements'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $oeuvre_expression['to_id'], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
				}
				ksort($this->oeuvre_expressions_datas);
			}
		}
		return $this->oeuvre_expressions_datas;
	}
	
	public function get_oeuvre_expressions_from_datas(){
		if (!isset($this->oeuvre_expressions_from_datas)) {
			$this->oeuvre_expressions_from_datas = array();
			if (isset($this->oeuvre_expressions_from) && is_array($this->oeuvre_expressions_from)) {
				foreach ($this->oeuvre_expressions_from as $oeuvre_expression) {
					//$this->oeuvre_expressions_from_datas[] = new authority(0, $oeuvre_expression['to_id'], AUT_TABLE_TITRES_UNIFORMES);
					$this->oeuvre_expressions_from_datas[] = authorities_collection::get_authority('authority', 0, ['num_object' => $oeuvre_expression['to_id'], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
				}
			}
		}
		return $this->oeuvre_expressions_from_datas;
	}
	
	/**
	 * Renvoie les données des évenements de l'oeuvre
	 * @return titre_uniforme Tableau de titre uniformes
	 */
	public function get_oeuvre_events_datas() {
		//on initialise oeuvre_events
		$this->get_oeuvre_events();
		if (!is_array($this->oeuvre_events_datas) || !count($this->oeuvre_events_datas)) {
			$this->oeuvre_events_datas = array();
			if (isset($this->oeuvre_events) && is_array($this->oeuvre_events)) {
				foreach ($this->oeuvre_events as $oeuvre_event) {
				    $this->oeuvre_events_datas[] = authorities_collection::get_authority('authperso', $oeuvre_event['id']);
				}
			}
		}
		return $this->oeuvre_events_datas;
	}	
	
	/**
	 * Renvoie les oeuvres liées
	 * @return titre_uniforme Tableau de titres uniformes
	 */
	public function get_other_links_datas() {
		if (!is_array($this->other_links_datas) || !count($this->other_links_datas)) {
			if($this->get_other_links()){
				foreach ($this->get_other_links() as $other_link) {
					if (!isset($this->other_links_datas[$other_link['type']]['label'])) $this->other_links_datas[$other_link['type']]['label'] = $other_link['type_name'];
					//$this->other_links_datas[$other_link['type']]['elements'][] = new authority(0, $other_link['to_id'], AUT_TABLE_TITRES_UNIFORMES);
					$this->other_links_datas[$other_link['type']]['elements'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $other_link['to_id'], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
				}
				ksort($this->other_links_datas);
			}
		}
		return $this->other_links_datas;
	}
	
	public function get_oeuvre_links() {
	    if($this->oeuvre_expressions === null){
	        $oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
	        $this->oeuvre_expressions = array();
	        $this->other_links = array();
	        $this->oeuvre_parent_expressions = array();
	        
    		$query = 'select oeuvre_link_to, tu_name, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link, tu_oeuvre_type
    				from tu_oeuvres_links join titres_uniformes on tu_id = oeuvre_link_to where oeuvre_link_from = "'.$this->id.'"
    				order by oeuvre_link_type, index_tu, oeuvre_link_order';
    		$result = pmb_mysql_query($query);
    		if ($result && pmb_mysql_num_rows($result)) {
    			while ($link = pmb_mysql_fetch_object($result)) {
    				$type_name = "";
    				foreach ($oeuvre_link->table as $link_type) {
    					if (isset($link_type[$link->oeuvre_link_type])) {
    						$type_name = $link_type[$link->oeuvre_link_type];
    						break;
    					}
    				}
    				if ($link->oeuvre_link_other_link) {
    					// Autres liens
    					$this->other_links[] = array(
    							'to_id' => $link->oeuvre_link_to,
    							'to_name' => $link->tu_name,
    							'oeuvre_type' => $link->tu_oeuvre_type,
    							'type' => $link->oeuvre_link_type,
    							'type_name' => $type_name
    					);
    				} else if ($link->oeuvre_link_expression) {
    					// Expressions de
    					$this->oeuvre_parent_expressions[] = array(
    							'to_id' => $link->oeuvre_link_to,
    							'to_name' => $link->tu_name,
    							'oeuvre_type' => $link->tu_oeuvre_type,
    							'type' => $link->oeuvre_link_type,
    							'type_name' => $type_name
    					);
    				} else {
    					// a pour expressions
    					$this->oeuvre_expressions[] = array(
    							'to_id' => $link->oeuvre_link_to,
    							'to_name' => $link->tu_name,
    							'oeuvre_type' => $link->tu_oeuvre_type,
    							'type' => $link->oeuvre_link_type,
    							'type_name' => $type_name
    					);
    				}
    			}
    		}
	    }
	}
	
	public function get_oeuvre_events() {
		if (!isset($this->oeuvre_events)) {
			$query = 'select oeuvre_event_authperso_authority_num
					from tu_oeuvres_events where oeuvre_event_tu_num = "'.$this->id.'"
					order by oeuvre_event_order';
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($auth = pmb_mysql_fetch_object($result)) {
					$this->oeuvre_events[]=array(
							'id' => $auth->oeuvre_event_authperso_authority_num,
							'isbd'=> authperso::get_isbd($auth->oeuvre_event_authperso_authority_num)
					);
				}
			}
		}		
		return $this->oeuvre_events;	
	}
	
	// ---------------------------------------------------------------
	// do_isbd() : génération de l'isbd complete de l'oeuvre
	// ---------------------------------------------------------------
	public function do_isbd() {
	    //initialisation des propriétés
	    //$other_links = $this->get_oeuvre_others_links_datas();
	    //$oeuvre_expressions = $this->get_oeuvre_expressions_datas();
	    $this->tu_isbd=$this->get_isbd_simple();
	    
	    return $this->tu_isbd;
	}
	
	// ---------------------------------------------------------------
	// get_isbd_simple() : génération de l'isbd minimaliste du titre uniforme (AFNOR Z 44-061 de 1986)
	// ---------------------------------------------------------------
	public function get_isbd_simple($with_responsabilites = true) {
	    global $fonction_auteur;
	    $isbd_simple = "";
	    if ($this->name) {
	        $isbd_simple .= $this->name;
	    }
	    if ($this->oeuvre_nature || $this->oeuvre_type) {
	        $isbd_simple.= ' [';
	        $isbd_simple.= ($this->oeuvre_nature ? $this->oeuvre_nature_name : '');
	        $isbd_simple.= ' ';
	        $isbd_simple.= ($this->oeuvre_type ? $this->oeuvre_type_name : '');
	        $isbd_simple.= ']';
	    }
	    
	    if ($with_responsabilites) {
	        $this->get_responsabilites();
    	    $as = array_keys($this->responsabilites["responsabilites"], "0");
    	    for($i = 0; $i < count($as); $i++) {
    	        $indice = $as[$i];
    	        $auteur_0 = $this->responsabilites["auteurs"][$indice];
    	        $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, ['num_object' => $auteur_0["id"],'type_object'=> AUT_TABLE_AUTHORS]);//new authority(0, $auteur_0["id"], AUT_TABLE_AUTHORS);
    	        $auteur = $authority->get_object_instance();
    	        $isbd_simple .= " / ";
    	        $isbd_simple .= $auteur->display;
    	        if($this->responsabilites['auteurs'][$i]['fonction']){
    	            $isbd_simple.= ', '.$fonction_auteur[$this->responsabilites['auteurs'][$i]['fonction']];
    	        }
    	        if(is_object($this->responsabilites['auteurs'][$i]['qualif'])){
    	            $isbd_simple.= ' ('.$this->responsabilites['auteurs'][$i]['qualif']->get_label().')';
    	        }
    	    }
	    }
	    if ($this->date) {
	        $isbd_simple .= ' ('.$this->date.')';
	    }
	    return $isbd_simple;
	}
	
	public function get_isbd() {
	    if (empty($this->tu_isbd)) {
	        $this->do_isbd();
	    }
	    return $this->tu_isbd;
	}
	
	public function get_sorted_responsabilities() {
		return $this->sorted_responsabilities;
	}

	public function get_db_id() {
		return $this->id;
	}
	
	public function get_permalink() {
		global $liens_opac;
		return str_replace('!!id!!', $this->id, $liens_opac['lien_rech_titre_uniforme']);
	}
	
	public function get_comment() {
		return $this->comment;
	}
	
	public function get_header() {
		return $this->get_isbd();
	}
	
	public function format_link_datas($linked_oeuvre=array()) {
	    $titre_uniforme = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $linked_oeuvre['to_id']);
		return array_merge(
				array(
						'link_type' => $linked_oeuvre['type'],
						'link_type_name' => $linked_oeuvre['type_name']
				),
				$titre_uniforme->format_datas(true)
		);
	}
	
	public function format_datas($antiloop = false){
		
		$expressions_datas = array();
		$expressions_from_datas = array();
		$others_links_datas = array();
		if(!$antiloop) {
			if (is_array($this->get_oeuvre_expressions())) {
				foreach ($this->get_oeuvre_expressions() as $expression) {
					$expressions_datas[] = $this->format_link_datas($expression);
				}
			}
			if (is_array($this->get_oeuvre_parent_expressions())) {
				foreach ($this->get_oeuvre_parent_expressions() as $parent_expression) {
					$expressions_datas[] = $this->format_link_datas($parent_expression);
				}
			}
			if (is_array($this->get_other_links())) {
				foreach ($this->get_other_links() as $other_link) {
					$others_links_datas[] = $this->format_link_datas($other_link);
				}
			}
		}
		$authors = array();
		if (!empty($this->sorted_responsabilities['authors'])) {
			foreach ($this->sorted_responsabilities['authors'] as $id=>$author) {
				$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $id);
							$a = $auteur->format_datas(true);
							$a['functions']=$author['attributes'];
				$authors[] = $a;
			}
		}
		$performers = array();
		if (!empty($this->sorted_responsabilities['performers'])) {
			foreach ($this->sorted_responsabilities['performers'] as $id=>$performer) {
				$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $id);
				$a = $auteur->format_datas(true);
				$a['functions']=$author['attributes'];
				$performers[] = $a;
			}
		}
		$formatted_data = array(
				'type' => $this->oeuvre_type_name,
				'nature' => $this->oeuvre_nature_name,
				'name' => $this->name,
				'expressions' => $expressions_datas,
				'expressions_from' => $expressions_from_datas,
				'other_links' => $others_links_datas,
				'events' => $this->get_oeuvre_events(),
				'authors' => $authors,
				'performers' => $performers,
				'form' => $this->form,
				'date' => $this->date,
				'date_date' => $this->date_date,
				'place' => $this->place,
				'subject' => $this->subject,
				'intended_termination' => $this->intended_termination,
				'intended_audience' => $this->intended_audience,
				'history' => $this->history,
				'context' => $this->context,
				'distribution' => $this->distrib,
				'ref_numerique' => $this->ref,
				'tonalite' => $this->tonalite,
				'tonalite_marclist' => $this->tonalite_marclist,
				'coordinates' => $this->coordinates,
				'equinox' => $this->equinox,
				'subdivision_shape' => $this->subdiv,
				'characteristic' => $this->characteristic,
				'comment' => $this->comment,
		);
		$formatted_data = array_merge($this->get_authority()->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public function get_other_links() {	
		if (!isset($this->other_links) || !$this->other_links) {
			$this->get_oeuvre_links();
		}
		return $this->other_links;
	}
	
	public function get_oeuvre_parent_expressions() {	
		if (!isset($this->oeuvre_parent_expressions) || !$this->oeuvre_parent_expressions) {
			$this->get_oeuvre_links();
		}
		return $this->oeuvre_parent_expressions;
	}
	
	public function get_oeuvre_expressions() {	
		if (!isset($this->oeuvre_expressions) || !$this->oeuvre_expressions) {
			$this->get_oeuvre_links();
		}
		return $this->oeuvre_expressions;
	}
	
	public function get_type() {
		return $this->oeuvre_type_name;
	}
	
	public function get_nature() {
		return $this->oeuvre_nature_name;
	}
	
	public function get_events() {
		return $this->get_oeuvre_events();
	}
	
	public function get_distribution() {
		return $this->distrib;
	}
	
	public function get_ref_numerique() {
		return $this->ref;
	}
	
	public function get_subdivision_shape() {
		return $this->subdiv;
	}
	
	public function get_p_perso() {
		if(!isset($this->p_perso)) {
			$this->p_perso = $this->get_authority()->get_p_perso();
		}
		return $this->p_perso;
	}
	
	public function get_authority() {
		return authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
	}
	
	public function get_isbd_without_responsabilites() {
	    return $this->get_isbd_simple(false);
	}
	
	public function __get($attribute) {
	    if (method_exists($this, "get_".$attribute)) {
	        return call_user_func_array(array($this, "get_".$attribute), array());
	    } else if (method_exists($this, $attribute)) {
	        return call_user_func_array(array($this, $attribute), array());
	    } else if (is_object($this) && isset($this->{$attribute})) {
	        return $this->{$attribute};
	    }
	    return null;
	}
	
	public function get_responsabilites() {
	    if (!isset($this->responsabilites)) {
	        $this->responsabilites ["responsabilites"] = array ();
	        $this->responsabilites = $this->get_authors();
	    }
	    return $this->responsabilites;
	}
	
	public function get_display() {
	    if (!isset($this->display)) {
	        $this->display = $this->name;
	        if ($this->date) {
	            $this->display .= " (" . $this->date . ")";
	        }
            $this->get_responsabilites();
            if (count ( $this->responsabilites ["auteurs"] )) {
                $this->display .= ", ";
                $libelle = array ();
                foreach ( $this->responsabilites ["auteurs"] as $id => $responsable ) {
                    if (is_object ( $responsable ["objet"] )) {
                        if ($id > 0)
                            $this->display .= " / "; // entre auteurs
                            $libelle [] = $responsable ["objet"]->display;
                            $this->display .= $responsable ["objet"]->rejete . " " . $responsable ["objet"]->name;
                    }
                }
                
                $this->libelle = implode ( "; ", $libelle );
            }
	    }
	    
	    return $this->display;
	}
	
	public function get_libelle() {
	    if (!isset($this->libelle)) {
	        $this->get_display();
	    }
	    
	    return $this->libelle;
	}
	
	public function get_display_tonalite() {
		if(isset($this->display_tonalite)) {
			return $this->display_tonalite;
		}
		
		$marc_key = self::get_marc_key();
		if(!empty($this->tonalite_marclist) && isset($marc_key->table[$this->tonalite_marclist])){
			return $marc_key->table[$this->tonalite_marclist];
		}
		return "";
	}
	
	public function get_authors_without_vedettes () 
	{
	    if($this->authors === null){
	        global $fonction_auteur;
	        $responsabilites = array() ;
	        $auteurs = array() ;
	        $this->authors["responsabilites"] = array() ;
	        $this->authors["auteurs"] = array() ;
	        $this->sorted_responsabilities = array(
	            'authors' => array(),
	            'performers' => array()
	        );

	        $rqt = "select author_id, responsability_tu_fonction, responsability_tu_type, id_responsability_tu ";
	        $rqt.= "from responsability_tu, authors where responsability_tu_num='".$this->id."' and responsability_tu_author_num=author_id order by responsability_tu_type, responsability_tu_ordre " ;

	        $res_sql = pmb_mysql_query($rqt);

	        while ($resp_tu=pmb_mysql_fetch_object($res_sql)) {
	            $responsabilites[] = $resp_tu->responsability_tu_type;
	            $fonction_label = '';
	            if (!empty($resp_tu->responsability_tu_fonction) && isset($fonction_auteur[$resp_tu->responsability_tu_fonction])) {
	                $fonction_label = $fonction_auteur[$resp_tu->responsability_tu_fonction];
	            }
	            $data = array(
	                'id' => $resp_tu->author_id,
	                'id_responsability_tu' => $resp_tu->id_responsability_tu,
	                'fonction' => $resp_tu->responsability_tu_fonction,
	                'fonction_label' => $fonction_label,
	                'responsability' => $resp_tu->responsability_tu_type,
	                'objet' => authorities_collection::get_authority(AUT_TABLE_AUTHORS, $resp_tu->author_id)
	            ) ;
	            $auteurs[] = $data;
	            $data['attributes'][] = array(
	                'fonction' => $data['fonction'],
	                'fonction_label' => $data['fonction_label'],
	            );
	            unset($data['fonction']);
	            unset($data['fonction_label']);
	            if (!$resp_tu->responsability_tu_type) {
	                if (!isset($this->sorted_responsabilities['authors'][$data['id']])) {
	                    $this->sorted_responsabilities['authors'][$data['id']] = $data;
	                } else {
	                    $this->sorted_responsabilities['authors'][$data['id']]['attributes'][] = $data['attributes'][0];
	                }
	            } else {
	                if (!isset($this->sorted_responsabilities['performers'][$data['id']])) {
	                    $this->sorted_responsabilities['performers'][$data['id']] = $data;
	                } else {
	                    $this->sorted_responsabilities['performers'][$data['id']]['attributes'][] = $data['attributes'][0];
	                }
	            }
	        }
	        $this->authors["responsabilites"] = $responsabilites ;
	        $this->authors["auteurs"] = $auteurs ;
	    }
	    return $this->authors;
	}
} // class titre uniforme