<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titre_uniforme.class.php,v 1.224.4.2 2025/03/04 16:31:17 dgoron Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
    die ( "no access" );

use Pmb\Ark\Entities\ArkEntityPmb;
    
global $class_path, $include_path;
require_once($class_path."/notice.class.php");
require_once("$class_path/aut_link.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once("$class_path/audit.class.php");
require_once("$class_path/author.class.php");
require_once($class_path."/synchro_rdf.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once("$class_path/marc_table.class.php");
require_once($class_path.'/authorities_statuts.class.php');
require_once($class_path."/indexation_authority.class.php");
require_once($class_path."/authority.class.php");
require_once($class_path."/vedette/vedette_link.class.php");
require_once($include_path."/h2o/pmb_h2o.inc.php");
require_once($class_path.'/form_mapper/form_mapper.class.php');
require_once("$include_path/templates/titres_uniformes.tpl.php");
require_once($class_path.'/event/events/event_titre_uniforme.class.php');
require_once($class_path.'/tu_notice.class.php');
require_once($class_path.'/authority.class.php');
require_once ($class_path.'/indexations_collection.class.php');
require_once ($class_path.'/indexation_stack.class.php');
require_once($class_path.'/parametres_perso.class.php');

global $fonction_auteur;
if (!isset($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

class titre_uniforme {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------	
	public $id; // MySQL id in table 'titres_uniformes'
	public $name; // titre_uniforme name
	public $tonalite; // tonalite de l'oeuvre musicale
	public $tonalite_marclist; // tonalite de l'oeuvre musicale (valeur issue de la liste music_key.xml)
	public $tonalite_marclist_name; // tonalite de l'oeuvre musicale (valeur issue de la liste music_key.xml)
	public $comment; // Commentaire, peut contenir du HTML
	public $import_denied = 0; // booléen pour interdire les modification depuis un import d'autorités
	public $tu_form; // catégorie à laquelle appartient l'oeuvre (roman, pièce de théatre, poeme, ...)
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
	private $display; // usable form for displaying ( _name_ (_date_) / _author_name_ _author_rejete_ )
	private $libelle;
	public $tu_isbd; // affichage isbd du titre uniforme AFNOR Z 44-061 (1986)
	private $responsabilites = null; // Auteurs répétables
	public $oeuvre_nature; // Nature de l'oeuvre
	public $oeuvre_nature_name; // Label de la Nature de l'oeuvre
	public $oeuvre_nature_nature; // Nature de la nature de l'oeuvre	
	public $oeuvre_type; // Type de l'oeuvre
	public $oeuvre_type_name; // Label du Type de l'oeuvre
	public $oeuvre_expressions = null; // tableau Expression de l'oeuvre
	public $other_links; // tableau Expression de l'oeuvre
	public $oeuvre_expressions_from; // tableau A pour Expression
	public $oeuvre_events; // Evènements de l'oeuvre
	public $num_statut = 1;
	public $recursif = 0;
	public $distrib = array();
	public $ref = array();
	public $subdiv = array();
	protected $oeuvre_expressions_list_ui;
	protected $oeuvre_expressions_from_list_ui;
	protected $sorted_responsabilities; // Tableau trié des responsabilités
	protected $error_message = '';
	private $authors = null;
	protected $isbd_calculated = "";
	protected $tu_notices;
	public $cp_error_message;
	protected static $deleted_index = false;
	protected static $controller;
	protected $oeuvre_expressions_grouped_list_ui = [];
	
	// ---------------------------------------------------------------
	//		titre_uniforme($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0,$recursif=0) {
		$this->id = intval($id);
		if($this->id) {
			// on cherche à atteindre une notice existante
			$this->recursif=intval($recursif);
		}
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos titre_uniforme
	// ---------------------------------------------------------------
	public function getData() {
		global $mapper;
		
		$music_key = new marc_list('music_key');
		
		$this->name = '';			
		$this->tonalite = '';
		$this->tonalite_marclist = '';
		$this->comment ='';
		$this->distrib=array();
		$this->ref=array();
		$this->subdiv=array();
		$this->import_denied=0;
		$this->tu_form = '';
		$this->form_marclist = '';
		$this->date ='';
		$this->date_date ='';
		$this->characteristic = '';
		$this->intended_termination = '';
		$this->intended_audience = '';
		$this->context = '';
		$this->coordinates = '';
		$this->equinox = '';
		$this->subject = '';
		$this->place = '';
		$this->history = '';
		$this->oeuvre_nature = '';
		$this->oeuvre_nature_nature = '';
		$this->oeuvre_type = '';
		$this->num_statut = 1;
		if($this->id) {
			$requete = "SELECT * FROM titres_uniformes WHERE tu_id=$this->id LIMIT 1 ";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				
				$this->id	= $temp->tu_id;
				$this->name	= $temp->tu_name;
				$this->tonalite	= $temp->tu_tonalite;
				$this->tonalite_marclist = $temp->tu_tonalite_marclist;
				$this->tonalite_marclist_name = ($music_key->table[$this->tonalite_marclist] ?? "") ;
				$this->comment	= $temp->tu_comment	;
				$this->import_denied = $temp->tu_import_denied;
				$this->tu_form = $temp->tu_forme;
				$this->form_marclist = $temp->tu_forme_marclist;
				$this->date  = $temp->tu_date;
				$this->date_date  = $temp->tu_date_date;
				$this->characteristic = $temp->tu_caracteristique;
				$this->intended_termination = $temp->tu_completude;
				$this->intended_audience = $temp->tu_public;
				$this->context = $temp->tu_contexte;
				$this->coordinates = $temp->tu_coordonnees;
				$this->equinox = $temp->tu_equinoxe;
				$this->subject = $temp->tu_sujet;
				$this->place = $temp->tu_lieu;
				$this->history = $temp->tu_histoire;
				$this->oeuvre_nature = $temp->tu_oeuvre_nature;
				$this->oeuvre_nature_nature = $temp->tu_oeuvre_nature_nature;
				$this->oeuvre_type = $temp->tu_oeuvre_type;
				$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, ['num_object'=>$this->id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
				$this->num_statut = $authority->get_num_statut();

				$mc_oeuvre_type = marc_list_collection::get_instance('oeuvre_type');
				$this->oeuvre_type_name= array_key_exists($this->oeuvre_type, $mc_oeuvre_type->table) ? $mc_oeuvre_type->table[$this->oeuvre_type] : "";
				$mc_oeuvre_nature = marc_list_collection::get_instance('oeuvre_nature');
				$this->oeuvre_nature_name=  array_key_exists($this->oeuvre_nature, $mc_oeuvre_nature->table) ? $mc_oeuvre_nature->table[$this->oeuvre_nature] : "";
				$this->oeuvre_nature_nature=$temp->tu_oeuvre_nature_nature;
				
				$requete = "SELECT * FROM tu_distrib WHERE distrib_num_tu='$this->id' order by distrib_ordre";
				$result = pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($result)) {
					while(($param=pmb_mysql_fetch_object($result))) {
						$this->distrib[]["label"]=$param->distrib_name;
					}
					pmb_mysql_free_result($result);
				}					
				$requete = "SELECT *  FROM tu_ref WHERE ref_num_tu='$this->id' order by ref_ordre";
				$result = pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($result)) {
					while(($param=pmb_mysql_fetch_object($result))) {
						$this->ref[]["label"]=$param->ref_name;
					}
					pmb_mysql_free_result($result);
				}			
				$requete = "SELECT *  FROM tu_subdiv WHERE subdiv_num_tu='$this->id' order by subdiv_ordre";
				$result = pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($result)) {
					while(($param=pmb_mysql_fetch_object($result))) {
						$this->subdiv[]["label"]=$param->subdiv_name;
					}
					pmb_mysql_free_result($result);
				}	
			} else {
				// pas trouvé avec cette clé
				$this->id = 0;			
			}
		}elseif(isset($mapper)){
			$this->oeuvre_nature = $mapper['oeuvre_nature'];
			$this->oeuvre_type = $mapper['oeuvre_type'];
		}
	}
	
	public function build_header_to_export() {
	    global $msg;
	    
	    $data = array(
	    	    $msg['aut_oeuvre_form_oeuvre_type'], 
	    	    $msg['aut_oeuvre_form_oeuvre_nature'], 
	    	    $msg['aut_titre_uniforme_form_nom'], 
	    	    $msg['collstate_statut_libelle'], 
	    	    $msg['aut_titre_uniforme_form_tonalite_list'], 
	    	    $msg['707'], 
	    	    $msg['aut_oeuvre_form_forme_list'], 
	    	    $msg['aut_oeuvre_form_forme_list'], 
	    	    $msg['catal_titre_uniforme_date'], 
	    	    $msg['catal_titre_uniforme_date'], 
	    	    $msg['aut_oeuvre_form_caracteristique'], 
	    	    $msg['aut_oeuvre_form_completude'], 
	    	    $msg['aut_oeuvre_form_public'], 
	    	    $msg['aut_oeuvre_form_contexte'], 
	    	    $msg['aut_oeuvre_form_coordonnees'], 
	    	    $msg['aut_oeuvre_form_equinoxe'], 
	    	    $msg['aut_oeuvre_form_sujet'], 
	    	    $msg['aut_oeuvre_form_lieu'], 
	    	    $msg['aut_oeuvre_form_histoire'], 
	    );
	    return $data;
	}
	
	public function build_data_to_export() {	    
	   
	    $data = array( 
	    	    $this->oeuvre_type_name,
	    	    $this->oeuvre_nature_name,
	    	    $this->name,	  
	    	    $this->libelle,
	    	    $this->tonalite,
	    	    $this->comment,
	    	    $this->tu_form,
	    	    $this->form_marclist,
	    	    $this->date,
	    	    $this->date_date,
	    	    $this->characteristic,
	    	    $this->intended_termination,
	    	    $this->intended_audience,
	    	    $this->context,
	    	    $this->coordinates,
	    	    $this->equinox,
	    	    $this->subject,
	    	    $this->place,
	    	    $this->history,
	    );
	    return $data;
	}
	
	public function get_authors() {
		
		if($this->authors === null) {
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
			if (pmb_mysql_num_rows($res_sql)) {
				while ($resp_tu=pmb_mysql_fetch_object($res_sql)) {
					$responsabilites[] = $resp_tu->responsability_tu_type;
					$qualif_id = vedette_composee::get_vedette_id_from_object($resp_tu->id_responsability_tu, (!$resp_tu->responsability_tu_type ? TYPE_TU_RESPONSABILITY : TYPE_TU_RESPONSABILITY_INTERPRETER));
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
					
					unset($data['fonction'], $data['fonction_label'], $data['qualif'], $data['qualif_label']);
					
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
				pmb_mysql_free_result($res_sql);
			}
			$this->authors["responsabilites"] = $responsabilites ;
			$this->authors["auteurs"] = $auteurs ;
		}
		return $this->authors;
	}
	
	public static function gen_input_selection($label,$form_name,$item,$values,$what_sel,$class='saisie-80em' ) {  
	
		global $msg;
		$select_prop = "scrollbars=yes, toolbar=no, dependent=yes, resizable=yes";
		$link="'./select.php?what=$what_sel&caller=$form_name&p1=f_".$item."_code!!num!!&p2=f_".$item."!!num!!&deb_rech='+".pmb_escape()."(this.form.f_".$item."!!num!!.value), '$what_sel', 400, 400, -2, -2, '$select_prop'";
		$size_item=strlen($item)+2;
		$script_js="
		<script>
		function fonction_selecteur_".$item."() {
			var nom='f_".$item."';
	        name=this.getAttribute('id').substring(4);  
			name_id = name.substr(0,nom.length)+'_code'+name.substr(nom.length);
			openPopUp('./select.php?what=$what_sel&caller=$form_name&p1='+name_id+'&p2='+name, '$what_sel', 400, 400, -2, -2, '$select_prop');
	        
	    }
	    function fonction_raz_".$item."() {
	        name=this.getAttribute('id').substring(4);
			name_id = name.substr(0,$size_item)+'_code'+name.substr($size_item);
	        document.getElementById(name).value='';
			document.getElementById(name_id).value='';
	    }
	    function add_".$item."() {
	        template = document.getElementById('add".$item."');
	        ".$item."=document.createElement('div');
	        ".$item.".className='row';
	
	        suffixe = eval('document.".$form_name.".max_".$item.".value')
	        nom_id = 'f_".$item."'+suffixe
	        f_".$item." = document.createElement('input');
	        f_".$item.".setAttribute('name',nom_id);
	        f_".$item.".setAttribute('id',nom_id);
	        f_".$item.".setAttribute('type','text');
	        f_".$item.".className='$class';
	        f_".$item.".setAttribute('value','');
			f_".$item.".setAttribute('completion','".$item."');
	        
			id = 'f_".$item."_code'+suffixe
			f_".$item."_code = document.createElement('input');
			f_".$item."_code.setAttribute('name',id);
	        f_".$item."_code.setAttribute('id',id);
	        f_".$item."_code.setAttribute('type','hidden');
			f_".$item."_code.setAttribute('value','');
	 
	        del_f_".$item." = document.createElement('input');
	        del_f_".$item.".setAttribute('id','del_f_".$item."'+suffixe);
	        del_f_".$item.".onclick=fonction_raz_".$item.";
	        del_f_".$item.".setAttribute('type','button');
	        del_f_".$item.".className='bouton';
	        del_f_".$item.".setAttribute('readonly','');
	        del_f_".$item.".setAttribute('value','".$msg["raz"]."');
	
	        sel_f_".$item." = document.createElement('input');
	        sel_f_".$item.".setAttribute('id','sel_f_".$item."'+suffixe);
	        sel_f_".$item.".setAttribute('type','button');
	        sel_f_".$item.".className='bouton';
	        sel_f_".$item.".setAttribute('readonly','');
	        sel_f_".$item.".setAttribute('value','".$msg["parcourir"]."');
	        sel_f_".$item.".onclick=fonction_selecteur_".$item.";
	
	        ".$item.".appendChild(f_".$item.");
			".$item.".appendChild(f_".$item."_code);
	        space=document.createTextNode(' ');
	        ".$item.".appendChild(space);
	        ".$item.".appendChild(del_f_".$item.");
	        ".$item.".appendChild(space.cloneNode(false));
	        ".$item.".appendChild(document.getElementById('button_add_field_".$item."'));	        

	        if('$what_sel')".$item.".appendChild(sel_f_".$item.");
	        
	        template.appendChild(".$item.");
	
	        document.".$form_name.".max_".$item.".value=suffixe*1+1*1 ;
	        ajax_pack_element(f_".$item.");
	    }
		</script>";
		
		//template de zone de texte pour chaque valeur				
		$aff="
		<div class='row'>
		<input type='text' class='$class' id='f_".$item."!!num!!' name='f_".$item."!!num!!' data-form-name='f_".$item."' value=\"!!label_element!!\" autfield='f_".$item."_code!!num!!' completion=\"".$item."\" />
		<input type='hidden' id='f_".$item."_code!!num!!' name='f_".$item."_code!!num!!' data-form-name='f_".$item."_code' value='!!id_element!!'>
		<input type='button' class='bouton' value='".$msg["raz"]."' onclick=\"this.form.f_".$item."!!num!!.value='';this.form.f_".$item."_code!!num!!.value=''; \" />
		!!bouton_parcourir!!
		!!bouton_ajouter!!
		</div>";
		
		if ($what_sel)
			$bouton_parcourir = "<input type='button' class='bouton' value='" . $msg ["parcourir"] . "' onclick=\"openPopUp(" . $link . ")\" />";
		else
			$bouton_parcourir = "";
		$aff= str_replace('!!bouton_parcourir!!', $bouton_parcourir, $aff);	

		$template=$script_js."<div id='add".$item."' class='row'>";
		$template.="<div class='row'><label for='f_".$item."' class='etiquette'>".$label."</label>
						<input class='bouton' value='" . $msg ["req_bt_add_line"] . "' onclick='add_" . $item . "();' type='button'>
					</div>";
		$num=0;
		if (!isset($values [0]))
			$values [0] = array (
					"id" => 0,
					"label" => "" 
			);
			
		foreach($values as $value) {
			
			$label_element=$value["label"];
			$id_element= !empty($value["id"]) ? $value['id'] : '';
			$button_add = '';
			
			if(end($values) == $value){
				$button_add = "<input id='button_add_field_".$item."' class='bouton' value='".$msg["req_bt_add_line"]."' onclick='add_".$item."();' type='button'>";
			}
			
			$temp= str_replace('!!bouton_ajouter!!', $button_add, $aff);
			$temp= str_replace('!!id_element!!', $id_element, $temp);	
			$temp= str_replace('!!label_element!!', $label_element, $temp);	
			$temp= str_replace('!!num!!', $num, $temp);	
		
			$template.=$temp;			
			$num++;
		}	
		$template.="<input type='hidden' id='max_".$item."' name='max_".$item."' value='$num'>";			
		
		$template.="</div><div id='add".$item."'/>
		</div>";
		return $template;		
	}
	
	protected function get_content_form() {
	    global $msg, $charset;
	    global $charset, $thesaurus_concepts_active;
	    global $titre_uniforme_content_form;
	    global $value_deflt_fonction;
	    global $tu_authors_tpl, $tu_authors_all_tpl, $pmb_authors_qualification;
	    
	    $fonction = new marc_list('function');
	    $music_key = new marc_list('music_key');
	    $music_form = new marc_list('music_form');
	    
	    $content_form = $titre_uniforme_content_form;
	    
	    // Auteurs
	    $this->get_responsabilites();
	    $as = array_keys ($this->responsabilites["responsabilites"], "0" ) ;
	    $max_aut0 = (count($as)) ;
	    $tu_auteurs="";
	    if ($max_aut0 == 0) {
	        $max_aut0 = 1;
	    }
        for ($i = 0 ; $i < $max_aut0 ; $i++) {
            if (isset($as[$i]) && $as[$i]!== FALSE && $as[$i]!== NULL) {
                $indice = $as[$i] ;
                $auteur_0 = $this->responsabilites["auteurs"][$indice] ;
            } else {
                $auteur_0 = array(
                    'id' => 0,
                    'fonction' => ($value_deflt_fonction ? $value_deflt_fonction : ''),
                    'responsability' => '',
                    'id_responsability_tu' => 0
                );
            }
            $authority_isbd = "";
            if($auteur_0["id"] != 0){
                $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $auteur_0["id"], 'type_object' => AUT_TABLE_AUTHORS]);
                $authority_isbd = trim($authority_instance->get_isbd());
            }
            
            $ptab_aut_tu=$tu_authors_tpl;
            $ptab_aut_tu = str_replace('!!n!!', 0, $ptab_aut_tu) ;
            $ptab_aut_tu = str_replace('!!iaut!!', $i, $ptab_aut_tu) ;
            $ptab_aut_tu = str_replace('!!title!!',$msg["tu_authors_list"], $ptab_aut_tu);
            $ptab_aut_tu = str_replace('!!vedettetype!!', 'role', $ptab_aut_tu);
            
            if($i){
                $ptab_aut_tu = str_replace('!!title_display!!', 'display:none', $ptab_aut_tu);
                $ptab_aut_tu = str_replace('!!bouton_add_display!!', 'display:none', $ptab_aut_tu);
            } else {
                $ptab_aut_tu = str_replace('!!title_display!!', '', $ptab_aut_tu);
                $ptab_aut_tu = str_replace('!!bouton_add_display!!', '', $ptab_aut_tu);
            }
            
            $ptab_aut_tu = str_replace('!!aut0_id!!', $auteur_0["id"], $ptab_aut_tu);
            $button_add = '';
            if ($i == ($max_aut0 - 1)) {
                $button_add = "<input id='button_add_titre_uniforme_aut_composed_0' type='button' class='bouton' value='+' onClick=\"add_aut(0);\"/>";
            }
            $ptab_aut_tu = str_replace('!!button_add_aut!!', $button_add, $ptab_aut_tu);
            $ptab_aut_tu = str_replace('!!aut0!!',				htmlentities($authority_isbd,ENT_QUOTES, $charset), $ptab_aut_tu);
            $ptab_aut_tu = str_replace('!!f0_code!!',			$auteur_0["fonction"], $ptab_aut_tu);
            $ptab_aut_tu = str_replace('!!f0!!',				($auteur_0["fonction"] ? $fonction->table[$auteur_0["fonction"]] : ''), $ptab_aut_tu);
            if($pmb_authors_qualification){
                $vedette_ui = new vedette_ui(new vedette_composee(vedette_composee::get_vedette_id_from_object($auteur_0["id_responsability_tu"],TYPE_TU_RESPONSABILITY), 'tu_authors'));
                $ptab_aut_tu = str_replace('!!vedette_author!!', $vedette_ui->get_form('role', $i, 'saisie_titre_uniforme'), $ptab_aut_tu);
            }else{
                $ptab_aut_tu = str_replace('!!vedette_author!!', "", $ptab_aut_tu);
            }
            $tu_auteurs .= $ptab_aut_tu ;
        }
        // Script à l'intérieur de $tu_authors_all_tpl
        $tu_authors_all_tpl = str_replace('!!max_aut0!!', $max_aut0, $tu_authors_all_tpl);
        $tu_authors_all_tpl = str_replace('!!authors_list0!!', $tu_auteurs, $tu_authors_all_tpl);
        
        // Interpretes
        $as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
        $max_aut1 = (count($as));
        $tu_auteurs="";
        if ($max_aut1 == 0) {
            $max_aut1 = 1;
        }
        for ($i = 0 ; $i < $max_aut1 ; $i++) {
            if (isset($as[$i]) && $as[$i]!== FALSE && $as[$i]!== NULL) {
                $indice = $as[$i] ;
                $auteur_1 = $this->responsabilites["auteurs"][$indice] ;
            } else {
                $auteur_1 = array(
                    'id' => 0,
                    'fonction' => ($value_deflt_fonction ? $value_deflt_fonction : ''),
                    'responsability' => '',
                    'id_responsability_tu' => 0
                );
            }
            $authority_isbd = "";
            if($auteur_1["id"] != 0){
                $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $auteur_1["id"], 'type_object' => AUT_TABLE_AUTHORS]);
                $authority_isbd = trim($authority_instance->get_isbd());
            }
            $ptab_aut_tu=$tu_authors_tpl;
            $ptab_aut_tu = str_replace('!!n!!', 1, $ptab_aut_tu) ;
            $ptab_aut_tu = str_replace('!!iaut!!', $i, $ptab_aut_tu) ;
            $ptab_aut_tu = str_replace('!!title!!',$msg["tu_interpreter_list"], $ptab_aut_tu);
            $ptab_aut_tu = str_replace('!!vedettetype!!', 'role_autre', $ptab_aut_tu);
            
            if($i){
                $ptab_aut_tu = str_replace('!!title_display!!', 'display:none', $ptab_aut_tu);
                $ptab_aut_tu = str_replace('!!bouton_add_display!!', 'display:none', $ptab_aut_tu);
            }else{
                $ptab_aut_tu = str_replace('!!title_display!!', '', $ptab_aut_tu);
                $ptab_aut_tu = str_replace('!!bouton_add_display!!', '', $ptab_aut_tu);
            }
            $ptab_aut_tu = str_replace('!!aut1_id!!',			$auteur_1["id"], $ptab_aut_tu);
            $button_add = '';
            if ($i == ($max_aut1 - 1)) {
                $button_add = "<input id='button_add_titre_uniforme_aut_composed_1' type='button' class='bouton' value='+' onClick=\"add_aut(1);\"/>";
            }
            $ptab_aut_tu = str_replace('!!button_add_aut!!', $button_add, $ptab_aut_tu);
            $ptab_aut_tu = str_replace('!!aut1!!',				htmlentities($authority_isbd,ENT_QUOTES, $charset), $ptab_aut_tu);
            $ptab_aut_tu = str_replace('!!f1_code!!',			$auteur_1["fonction"], $ptab_aut_tu);
            $ptab_aut_tu = str_replace('!!f1!!',				($auteur_1["fonction"] ? $fonction->table[$auteur_1["fonction"]] : ''), $ptab_aut_tu);
            if($pmb_authors_qualification){
                $vedette_ui = new vedette_ui(new vedette_composee(vedette_composee::get_vedette_id_from_object($auteur_1["id_responsability_tu"],TYPE_TU_RESPONSABILITY_INTERPRETER), 'tu_authors'));
                $ptab_aut_tu = str_replace('!!vedette_author!!', $vedette_ui->get_form('role_autre', $i, 'saisie_titre_uniforme'), $ptab_aut_tu);
            }else{
                $ptab_aut_tu = str_replace('!!vedette_author!!', "", $ptab_aut_tu);
            }
            $tu_auteurs .= $ptab_aut_tu ;
        }
        $tu_authors_all_tpl = str_replace('!!max_aut1!!', $max_aut1, $tu_authors_all_tpl);
        $tu_authors_all_tpl = str_replace('!!authors_list1!!', $tu_auteurs, $tu_authors_all_tpl);
        
        $aut_link= new aut_link(AUT_TABLE_TITRES_UNIFORMES,$this->id);
        $content_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_titre_uniforme') , $content_form);
        
        $aut_pperso= new aut_pperso("tu",$this->id);
        $content_form = str_replace('!!aut_pperso!!',	$aut_pperso->get_form(), $content_form);
        
        $content_form = str_replace('!!id!!',				$this->id,		$content_form);
	            
	            
        $select_oeuvre_type = new marc_select ( 'oeuvre_type', 'oeuvre_type', $this->oeuvre_type, "",'','',array(array('name'=>'data-form-name','value'=>'oeuvre_type')) );
        $content_form = str_replace ( '!!oeuvre_type!!', $select_oeuvre_type->display, $content_form );
        
        $select_oeuvre_nature = new marc_select ( 'oeuvre_nature', 'oeuvre_nature', $this->oeuvre_nature, "",'','',array(array('name'=>'data-form-name','value'=>'oeuvre_nature')) );
        $content_form = str_replace ( '!!oeuvre_nature!!', $select_oeuvre_nature->display, $content_form );
        
        $content_form = str_replace ( '!!oeuvre_expression!!', $this->gen_oeuvre_expression_form (), $content_form );
        $content_form = str_replace ( '!!other_link!!', $this->gen_other_link_form (), $content_form );
        $content_form = str_replace ( '!!oeuvre_expression_from!!', $this->gen_oeuvre_expression_from_form (), $content_form );
        $content_form = str_replace ( '!!oeuvre_event!!', $this->gen_oeuvre_event_form (), $content_form );
        $content_form = str_replace('!!nom!!',				htmlentities($this->name,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!authors!!',			$tu_authors_all_tpl, $content_form);
        $content_form = str_replace('!!tu_form!!',			htmlentities($this->tu_form,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!music_form_id!!',		$this->form_marclist,	$content_form);
        $content_form = str_replace('!!music_form!!',		($this->form_marclist ? $music_form->table[$this->form_marclist] : ''),	$content_form);
        $content_form = str_replace('!!date!!',				htmlentities($this->date,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!subject!!',			htmlentities($this->subject,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!place!!',				htmlentities($this->place,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!history!!',			htmlentities($this->history,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!intended_audience!!',	htmlentities($this->intended_audience,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!context!!',			htmlentities($this->context,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!tonalite!!',			htmlentities($this->tonalite,ENT_QUOTES, $charset),	$content_form);
        $content_form = str_replace('!!music_key_id!!',		$this->tonalite_marclist,	$content_form);
        $content_form = str_replace('!!music_key!!',			($this->tonalite_marclist ? $music_key->table[$this->tonalite_marclist] : ''),	$content_form);
        $content_form = str_replace('!!coordinates!!',		htmlentities($this->coordinates,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!equinox!!',			htmlentities($this->equinox,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!characteristic!!',	htmlentities($this->characteristic,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!comment!!',			htmlentities($this->comment,ENT_QUOTES, $charset),	$content_form);
        /**
         * Gestion du selecteur de statut d'autorité
         */
        $content_form = str_replace('!!auth_statut_selector!!', authorities_statuts::get_form_for(AUT_TABLE_TITRES_UNIFORMES, $this->num_statut), $content_form);
        
        // complétude
        $intended_termination_id = $this->intended_termination;
        $select_0 = "";
        $select_1 = "";
        $select_2 = "";
        if($intended_termination_id == 1){
            $select_1 = "selected";
        } elseif($intended_termination_id == 2){
            $select_2 = "selected";
        } else {
            $select_0 = "selected";
        }
        $content_form = str_replace('!!intended_termination_0!!',	htmlentities($select_0,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!intended_termination_1!!',	htmlentities($select_1,ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!intended_termination_2!!',	htmlentities($select_2,ENT_QUOTES, $charset), $content_form);
        // distribution
        $distribution_form=static::gen_input_selection($msg["aut_titre_uniforme_form_distribution"],"saisie_titre_uniforme","distrib",$this->distrib,"","saisie-80em");
        $content_form = str_replace("<!--	Distribution instrumentale et vocale (pour la musique)	-->",$distribution_form, $content_form);
        // reference
        $ref_num_form=static::gen_input_selection($msg["aut_titre_uniforme_form_ref_numerique"],"saisie_titre_uniforme","ref",$this->ref,"","saisie-80em");
        $content_form = str_replace("<!--	Référence numérique (pour la musique)	-->",$ref_num_form, $content_form);
        // subdivision
        $sub_form=static::gen_input_selection($msg["aut_titre_uniforme_form_subdivision_forme"],"saisie_titre_uniforme","subdiv",$this->subdiv,"","saisie-80em");
        $content_form = str_replace('<!-- Subdivision de forme -->',	$sub_form, $content_form);
	            
        if($this->import_denied == 1 || !$this->id){
            $import_denied_checked = "checked='checked'";
        }else{
            $import_denied_checked = "";
        }
        $content_form = str_replace('!!tu_import_denied!!', $import_denied_checked, $content_form);
        if($thesaurus_concepts_active == 1){
            $index_concept = new index_concept($this->id, TYPE_TITRE_UNIFORME);
            $content_form = str_replace('!!concept_form!!',	$index_concept->get_form('saisie_titre_uniforme'),$content_form);
        }else{
            $content_form = str_replace('!!concept_form!!',	"",	$content_form);
        }
        $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $this->id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
        $content_form = str_replace('!!thumbnail_url_form!!', thumbnail::get_form('authority', $authority->get_thumbnail_url()), $content_form);
        
        /**************** Notices liées ********************/
        $content_form = str_replace('!!tu_notices!!', tu_notice::gen_input_selection($msg["notice_relations"], 'saisie_titre_uniforme', 'tu_notices', $this->get_tu_notices(), 'notice','saisie-80emr'), $content_form);
        return $content_form;
	}
	
	/**
	 * Retourne le formulaire
	 * @param boolean $duplicate
	 * @return string
	 */
	//ATTENTION : ne pas utiliser le nom de méthode get_form car cela entre en conflit avec le django
	public function get_interface_form($duplicate = false) {
	    global $msg;
	    global $user_input, $nbr_lignes, $page;
	    
	    $interface_form = new interface_entity_titre_uniforme_form('saisie_titre_uniforme');
	    if(isset(static::$controller) && is_object(static::$controller)) {
	        $interface_form->set_controller(static::$controller);
	    }
	    $interface_form->set_enctype('multipart/form-data');
	    if($this->id && !$duplicate) {
	        $interface_form->set_label($msg["aut_titre_uniforme_modifier"]);
	    } else {
	        $interface_form->set_label($msg["aut_titre_uniforme_ajouter"]);
	    }
	    if ($this->name) {
	        $interface_form->set_document_title($this->name.' - '.$interface_form->get_label());
	    } else {
	        $interface_form->set_document_title($interface_form->get_label());
	    }
	    $interface_form->set_object_id(($duplicate ? 0 : $this->id))
	    ->set_num_statut($this->num_statut)
	    ->set_content_form($this->get_content_form())
	    ->set_table_name('titres_uniformes')
	    ->set_field_focus('tu_name')
	    ->set_url_base(static::format_url())
	    ->set_duplicable(true);
	    
	    $interface_form->set_page($page)
	    ->set_nbr_lignes($nbr_lignes)
	    ->set_user_input($user_input);
	    //Gestion de la duplication
	    if($duplicate) {
	        $interface_form->set_is_duplication("1");
	    }
	    return $interface_form->get_display();
	}
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form($duplicate=false) {
		print $this->get_interface_form($duplicate);
	}
	
	public function gen_oeuvre_expression_form() {
		global $oeuvre_expression_tpl, $oeuvre_expression_tpl_first, $oeuvre_expression_tpl_other, $charset;
		
		//initialisation des propriétés
		$oeuvre_expressions = $this->get_oeuvre_expressions_datas();
		$tab_link_order = "";
		
		// oeuvre expression repetables
		$oeuvre_expression_repetables = '';
		if (empty($oeuvre_expressions)) {
			$max_oeuvre_expression = 1;
			$oeuvre_expression_new_order = 0;
		} else {
			$max_oeuvre_expression = count($oeuvre_expressions);
			$oeuvre_expression_new_order = $max_oeuvre_expression-1;
		}
		for($i = 0; $i < $max_oeuvre_expression; $i ++) {
			$button_add = '';
			if ($i) {
				$ptab_expression = str_replace ( '!!ioeuvre_expression!!', $i, $oeuvre_expression_tpl_other );
			} else {
				$ptab_expression = str_replace ( '!!ioeuvre_expression!!', $i, $oeuvre_expression_tpl_first );

			}
			if ($i == ($max_oeuvre_expression - 1)) {
				$button_add = "<input id='button_add_f_oeuvre_expression' type='button' class='bouton' value='+' onClick=\"add_oeuvre_expression();\"/>";
			}
			$ptab_expression = str_replace( '!!button_add_oeuvre_expression!!', $button_add, $ptab_expression);
			
			if (empty($oeuvre_expressions)) {
				$ptab_expression = str_replace ( '!!expression_type!!', $this->get_selector('expression_of', 'f_oeuvre_expression_type0', ''), $ptab_expression );
				$ptab_expression = str_replace ( '!!oeuvre_expression_code!!', '', $ptab_expression );
				$ptab_expression = str_replace ( '!!oeuvre_expression!!', '', $ptab_expression );
			} else {
			    $tu_object = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $oeuvre_expressions[$i]["to_id"], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
				$ptab_expression = str_replace ( '!!expression_type!!', $this->get_selector('expression_of', 'f_oeuvre_expression_type'.$i, $oeuvre_expressions[$i]['type']), $ptab_expression );
				$ptab_expression = str_replace ( '!!oeuvre_expression_code!!', $oeuvre_expressions [$i] ["to_id"], $ptab_expression );
				$isbd = $tu_object->get_isbd();
				if (empty($tu_object->get_isbd_template())) {
				    //si on a pas de template django d'isbd, on fait le htmlentities
				    $isbd = htmlentities($isbd, ENT_QUOTES, $charset);
				}
				$ptab_expression = str_replace ( '!!oeuvre_expression!!', $isbd, $ptab_expression );
			}
			if($tab_link_order!="") $tab_link_order.=",";
			$tab_link_order.= $i;
			$oeuvre_expression_repetables .= $ptab_expression;
		}
		$tpl = "
		$oeuvre_expression_tpl
		<input type='hidden' id='max_oeuvre_expression' name='max_oeuvre_expression' value=\"!!max_oeuvre_expression!!\" />
		!!oeuvre_expression_repetables!!
		<div id='addoeuvre_expression'/>
		</div>";
		
		$tpl = str_replace ( '!!myid!!', $this->id, $tpl );
		$tpl = str_replace ( '!!max_oeuvre_expression!!', $max_oeuvre_expression, $tpl );
		$tpl = str_replace ( '!!oeuvre_expression_new_order!!', $oeuvre_expression_new_order, $tpl );
		$tpl = str_replace ( '!!oeuvre_expression_repetables!!', $oeuvre_expression_repetables, $tpl );
		$tpl = str_replace ( '!!tab_oeuvre_expression_order!!', $tab_link_order, $tpl);
		return $tpl;
	}

	public function gen_oeuvre_expression_from_form() {
		global $oeuvre_expression_from_tpl, $oeuvre_expression_from_tpl_first, $oeuvre_expression_from_tpl_other, $charset;

		//initialisation des propriétés
		$oeuvre_expressions_from = $this->get_oeuvre_expressions_from_datas();
		$tab_link_order = "";
		
		// oeuvre expression repetables
		$oeuvre_expression_from_repetables = '';
		if (empty($oeuvre_expressions_from)) {
			$max_oeuvre_expression_from = 1;
			$oeuvre_expression_from_new_order = 0;
		} else {
			$max_oeuvre_expression_from = count( $oeuvre_expressions_from );
			$oeuvre_expression_from_new_order = $max_oeuvre_expression_from-1;
		}
		
		for($i = 0; $i < $max_oeuvre_expression_from; $i ++) {
		    $button_add = '';
		    if ($i) {
		        $ptab_expression = str_replace ( '!!ioeuvre_expression_from!!', $i, $oeuvre_expression_from_tpl_other );
		    } else {
		        $ptab_expression = str_replace ( '!!ioeuvre_expression_from!!', $i, $oeuvre_expression_from_tpl_first );
		    }
		    if ($i == ($max_oeuvre_expression_from - 1)) {
		        $button_add = "<input id='button_add_f_oeuvre_expression_from' type='button' class='bouton' value='+' onClick=\"add_oeuvre_expression_from();\"/>";
		    }
		    $ptab_expression = str_replace( '!!button_add_oeuvre_expression_from!!', $button_add, $ptab_expression);

			if (empty($oeuvre_expressions_from)) {
				$ptab_expression = str_replace ( '!!expression_type!!', $this->get_selector('have_expression', 'f_oeuvre_expression_from_type0', ''), $ptab_expression );
				$ptab_expression = str_replace ( '!!oeuvre_expression_from_code!!', '', $ptab_expression );
				$ptab_expression = str_replace ( '!!oeuvre_expression_from!!', '', $ptab_expression );
			} else {
			    $tu_object = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $oeuvre_expressions_from[$i]["to_id"], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
				$ptab_expression = str_replace ( '!!expression_type!!', $this->get_selector('have_expression', 'f_oeuvre_expression_from_type'.$i, $oeuvre_expressions_from[$i]['type']), $ptab_expression );
				$ptab_expression = str_replace ( '!!oeuvre_expression_from_code!!', $oeuvre_expressions_from [$i] ["to_id"], $ptab_expression );
				$isbd = $tu_object->get_isbd();
				if (empty($tu_object->get_isbd_template())) {
				    //si on a pas de template django d'isbd, on fait le htmlentities
				    $isbd = htmlentities($isbd, ENT_QUOTES, $charset);
				}
				$ptab_expression = str_replace ( '!!oeuvre_expression_from!!', $isbd, $ptab_expression );
			}
			if($tab_link_order!="") $tab_link_order.=",";
			$tab_link_order.= $i;
			$oeuvre_expression_from_repetables .= $ptab_expression;
		}
		$tpl = "
		$oeuvre_expression_from_tpl
		<input type='hidden' id='max_oeuvre_expression_from' name='max_oeuvre_expression_from' value=\"!!max_oeuvre_expression_from!!\" />
		!!oeuvre_expression_from_repetables!!
		<div id='addoeuvre_expression_from'/>
		</div>";
	
		$tpl = str_replace ( '!!myid!!', $this->id, $tpl );
		$tpl = str_replace ( '!!max_oeuvre_expression_from!!', $max_oeuvre_expression_from, $tpl );
		$tpl = str_replace ( '!!oeuvre_expression_from_new_order!!', $oeuvre_expression_from_new_order, $tpl );
		$tpl = str_replace ( '!!oeuvre_expression_from_repetables!!', $oeuvre_expression_from_repetables, $tpl );
		$tpl = str_replace ( '!!tab_oeuvre_expression_from_order!!', $tab_link_order, $tpl);
		return $tpl;
	}
	
	public function gen_oeuvre_event_form() {
	    global $oeuvre_event_tpl, $oeuvre_event_tpl_first, $oeuvre_event_tpl_other, $charset, $default_event_type;
		
		//on initialise oeuvre_events 
		$this->get_oeuvre_events();
		
		// oeuvre event repetables
		$oeuvre_event_repetables = '';
		if (empty($this->oeuvre_events)) {
			$max_oeuvre_event = 1;
		} else {
			$max_oeuvre_event = count($this->oeuvre_events);
		}
		for ($i = 0; $i < $max_oeuvre_event; $i ++) {
			if ($i) {
				$ptab_event = str_replace ( '!!ioeuvre_event!!', $i, $oeuvre_event_tpl_other );
			} else {
				$ptab_event = str_replace ( '!!ioeuvre_event!!', $i, $oeuvre_event_tpl_first );
			}
			$button_add = '';
			if ($i == ($max_oeuvre_event - 1)) {
				$button_add = "<input id='button_add_f_oeuvre_event' type='button' class='bouton' value='+' onClick=\"add_oeuvre_event($i);\"/>";
			}
			$ptab_event = str_replace('!!button_add_oeuvre_event!!', $button_add, $ptab_event);
			
			if (empty($this->oeuvre_events)) {
				$ptab_event = str_replace('!!oeuvre_event_code!!', '', $ptab_event);
				$ptab_event = str_replace('!!oeuvre_event!!', '', $ptab_event);
    			$ptab_event = str_replace('!!oeuvre_event_type!!', $this->get_selector_event($i), $ptab_event);
    			$ptab_event = str_replace('!!oeuvre_event_type_value!!', $default_event_type, $ptab_event);
    			$ptab_event = str_replace('!!type!!', $default_event_type, $ptab_event);
			} else {
				$ptab_event = str_replace('!!oeuvre_event_code!!', $this->oeuvre_events[$i]["id"], $ptab_event);
				$ptab_event = str_replace('!!oeuvre_event!!', htmlentities($this->oeuvre_events[$i]["isbd"], ENT_QUOTES, $charset), $ptab_event);
				$ptab_event = str_replace('!!oeuvre_event_type!!', $this->get_selector_event($i, $this->oeuvre_events[$i]["type_oeuvre_event"]), $ptab_event);
				$ptab_event = str_replace('!!oeuvre_event_type_value!!', $this->oeuvre_events[$i]["type_oeuvre_event"], $ptab_event);
				$ptab_event = str_replace('!!type!!', $this->oeuvre_events[$i]["type_oeuvre_event"], $ptab_event);
			}
			$oeuvre_event_repetables .= $ptab_event;
		}
		$oeuvre_event_tpl = str_replace('!!event_type_default!!', $default_event_type, $oeuvre_event_tpl);
		$tpl = "$oeuvre_event_tpl
		<input type='hidden' id='max_oeuvre_event' name='max_oeuvre_event' value=\"!!max_oeuvre_event!!\" />
		!!oeuvre_event_repetables!!
		<div id='addoeuvre_event'/>
		</div>";
	
		$tpl = str_replace('!!max_oeuvre_event!!', $max_oeuvre_event, $tpl);
		$tpl = str_replace('!!oeuvre_event_repetables!!', $oeuvre_event_repetables, $tpl);
		return $tpl;
	}
	
	public function get_selector_event($rep_number, $type_oeuvre_event = '') {
	    global $msg, $default_event_type;

	    $selector = '';
	    $options = '';
	    $default_event_type = '';
	    
	    $query = "SELECT id_authperso, authperso_name FROM authperso WHERE authperso_oeuvre_event=1";
	    $res = pmb_mysql_query($query);
	    if (!empty($res)) {
	        if (pmb_mysql_num_rows($res) == 1) {
	            $row = pmb_mysql_fetch_assoc($res);
	            $selector = "<input type='hidden' id='f_oeuvre_type_event$rep_number' name='f_oeuvre_type_event$rep_number' data-form-name='f_oeuvre_type_event' value='".$row['id_authperso']."'/>";
	            $default_event_type = $row['id_authperso'];
	        } else {
        	    $selector = "<select id='f_oeuvre_type_event$rep_number' name='f_oeuvre_type_event$rep_number' data-form-name='f_oeuvre_type_event' onchange='onchange_oeuvre_type_event($rep_number)'>";
    	        while ($row = pmb_mysql_fetch_assoc($res)) {
    	            if ($row['id_authperso'] == $type_oeuvre_event) {
    	                $options .= "<option value='".$row['id_authperso']."' selected>".$row['authperso_name']."</option>";
    	                $default_event_type = $row['id_authperso'];
    	            } else {
    	                $options .= "<option value='".$row['id_authperso']."'>".$row['authperso_name']."</option>";
    	                if (empty($default_event_type)) $default_event_type = $row['id_authperso'];
    	            }
    	        }
        	    if (empty($options)) {
                    $selector .= "<option value=''>".$msg['authority_marc_list_empty_filter']."</option>";
        	    } else {
        	        $selector .= $options;
        	    }
        	    $selector.= '</select>';
	        }
	    }
	    return $selector;
	}
	
	public function update_oeuvre_expression($value) {
		$this->delete_oeuvre_expression ();
		if(!is_array($value)) return;
		$rqt_ins = "insert into tu_oeuvres_links (oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link, oeuvre_link_order) VALUES ";
		$ordre = 0;
		foreach ( $value as $val ) {
			if ($val['code']) {
				$rqt = $rqt_ins . " ('".$this->id."', '".$val['code']."','".$val['type']."', 1, 0, ".$ordre.") ";
				pmb_mysql_query ( $rqt );
				$ordre ++;
				$this->save_expression_inversed_link($this->id,$val['code'], $val['type'],0);
			}
		}
	}

	public function delete_oeuvre_expression() {
		if(!$this->id) return;
		$rqt_del = "delete from tu_oeuvres_links where oeuvre_link_from='".$this->id."' and oeuvre_link_other_link = 0 ";
		pmb_mysql_query ( $rqt_del );		
		
		$rqt_del = "delete from tu_oeuvres_links where oeuvre_link_to='".$this->id."' and oeuvre_link_other_link = 0 ";
		pmb_mysql_query ( $rqt_del );
	}
	
	public function update_oeuvre_expression_from($value) {
		if(!is_array($value)) return;
		$rqt_ins = "insert into tu_oeuvres_links ( oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link, oeuvre_link_order) VALUES ";
		$ordre = 0;
		foreach ( $value as $val ) {
			if ($val['code']) {
				$rqt = $rqt_ins . " ('".$this->id."', '".$val['code']."','".$val['type']."', 0, 0, ".$ordre.") ";
				pmb_mysql_query ( $rqt );
				$ordre ++;
				$this->save_expression_inversed_link($this->id,$val['code'], $val['type'],1);
			}
		}
		
	}
		
	public function get_oeuvre_links() {
		if($this->oeuvre_expressions === null){
			$oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
			$this->oeuvre_expressions = array();
			$this->other_links = array();

			$this->oeuvre_expressions_from = array();
		
			$query = 'select oeuvre_link_to, tu_name, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link 
				from tu_oeuvres_links join titres_uniformes on tu_id = oeuvre_link_to where oeuvre_link_from = "'.$this->id.'" 
				order by oeuvre_link_order, oeuvre_link_type, index_tu';
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($link = pmb_mysql_fetch_object($result)) {				
					if ($link->oeuvre_link_other_link) {
						$type_label = '';
						foreach ($oeuvre_link->table as $link_type) {
							if (isset($link_type[$link->oeuvre_link_type])) {
								$type_label = $link_type[$link->oeuvre_link_type];
								break;
							}
						}
						$this->other_links[] = array(
							'to_id' => $link->oeuvre_link_to,
							'to_name' => $link->tu_name,
							'type' => $link->oeuvre_link_type,
							'type_label' => $type_label,
						);
					}else{
						$type_label = '';
						foreach ($oeuvre_link->table as $link_type) {
							if (isset($link_type[$link->oeuvre_link_type])) {
								$type_label = $link_type[$link->oeuvre_link_type];
								break;
							}
						} 
						if ($link->oeuvre_link_expression) {
							// expression de
							$this->oeuvre_expressions[] = array(
								'to_id' => $link->oeuvre_link_to,
								'to_name' => $link->tu_name,
								'type' => $link->oeuvre_link_type,
								'type_label' => $type_label,
							);
						}else {
							// A pour expression
							$this->oeuvre_expressions_from[] = array(
								'to_id' => $link->oeuvre_link_to,
								'to_name' => $link->tu_name,
								'type' => $link->oeuvre_link_type,
								'type_label' => $type_label,
							);
						}					
					}
				}
			}
		}
	}
	
	public function get_oeuvre_events() {	
		if(!isset($this->oeuvre_events)){
			$this->oeuvre_events = array();
			$query = "SELECT oeuvre_event_authperso_authority_num, authperso_authority_authperso_num
				FROM tu_oeuvres_events, authperso_authorities
				WHERE oeuvre_event_authperso_authority_num = id_authperso_authority
				AND oeuvre_event_tu_num = $this->id
				ORDER BY oeuvre_event_order";
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($auth = pmb_mysql_fetch_object($result)) {				    
				    $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $auth->oeuvre_event_authperso_authority_num, 'type_object' => AUT_TABLE_AUTHPERSO]);
					$authperso = $authority->get_object_instance();
					$this->oeuvre_events[] = array(
					        'id' => $auth->oeuvre_event_authperso_authority_num,
					        'type_oeuvre_event' => $auth->authperso_authority_authperso_num,
							'isbd' => $authperso->get_isbd()
					);
				}
			}
		}
		return $this->oeuvre_events;
	}
		
	public function get_link_type($link_sel = '', $iother_link = 0) {
		$select_oeuvre_type = new marc_select ( 'oeuvre_link', 'f_oeuvre_other_link' . $iother_link, $link_sel, "" );
		return $select_oeuvre_type->display;
	}
	
	public function gen_other_link_form() {
		global $other_link_tpl, $other_link_tpl_first, $other_link_tpl_other, $charset;
		
		$other_links = $this->get_oeuvre_others_links_datas();
		$tab_link_order = "";
		
		// oeuvre expression repetables
		$other_link_repetables = '';
		if (empty($other_links)) {
			$max_other_link = 1;
			$other_link_new_order = 0;
		} else {
			$max_other_link = count($other_links);
			$other_link_new_order = $max_other_link-1;
		}
		
		for($i = 0; $i < $max_other_link; $i ++) {
			if ($i) {
				$ptab_expression = str_replace ( '!!iother_link!!', $i, $other_link_tpl_other );
			} else {
				$ptab_expression = str_replace ( '!!iother_link!!', $i, $other_link_tpl_first );
			}
			$button_add = '';
			if ($i == ($max_other_link - 1)) {
				$button_add = "<input id='button_add_f_other_link' type='button' class='bouton' value='+' onClick=\"add_other_link();\"/>";
			}
			$ptab_expression = str_replace( '!!button_add_other_link!!', $button_add, $ptab_expression);
			if (empty($other_links)) {
				$ptab_expression = str_replace ( '!!other_link_code!!', '', $ptab_expression );
				$ptab_expression = str_replace ( '!!other_link!!', '', $ptab_expression );
				$ptab_expression = str_replace ( '!!link_type!!', $this->get_selector('other_link', 'f_oeuvre_other_link0', ''), $ptab_expression);
			} else {
			    $tu_object = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $other_links[$i]["to_id"], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
				$ptab_expression = str_replace ( '!!other_link_code!!', $other_links [$i] ["to_id"], $ptab_expression );
				$isbd = $tu_object->get_isbd();
				if (empty($tu_object->get_isbd_template())) {
				    //si on a pas de template django d'isbd, on fait le htmlentities
				    $isbd = htmlentities($isbd, ENT_QUOTES, $charset);
				}
				$ptab_expression = str_replace ( '!!other_link!!', $isbd, $ptab_expression );
				$ptab_expression = str_replace ( '!!link_type!!', $this->get_selector('other_link', 'f_oeuvre_other_link'.$i, $other_links[$i]["type"]), $ptab_expression );
			}
			if($tab_link_order!="") $tab_link_order.=",";
			$tab_link_order.= $i;
			$other_link_repetables .= $ptab_expression;
		}
		$tpl = "
		$other_link_tpl
		<input type='hidden' id='max_other_link' name='max_other_link' value=\"!!max_other_link!!\" />
		!!other_link_repetables!!
		<div id='addother_link'/>
		</div>";
		
		$tpl = str_replace ( '!!max_other_link!!', $max_other_link, $tpl );
		$tpl = str_replace ( '!!other_link_repetables!!', $other_link_repetables, $tpl );
		$tpl = str_replace ( '!!other_link_new_order!!', $other_link_new_order, $tpl );
		$tpl = str_replace ( '!!myid!!', $this->id, $tpl );
		$tpl = str_replace ( '!!tab_other_link_order!!', $tab_link_order, $tpl);
		return $tpl;
	}
	
	public function update_other_link($value) {
		$this->delete_other_link ();
		if(!is_array($value)) return;
		$rqt_ins = "insert into tu_oeuvres_links(oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link, oeuvre_link_order) VALUES ";
		$ordre = 0;
		foreach ( $value as $val ) {
			if ($val['code']) {
				$this->save_inversed_link($this->id,$val['code'], $val['type']);
				$rqt = $rqt_ins."('".$this->id."', '".$val['code']."', '".$val['type']."', 0, 1, ".$ordre.") ";
				pmb_mysql_query($rqt);
				$ordre++;
			}
		}
	}
			
	public function update_oeuvre_event($value) {
		$this->delete_oeuvre_event();
		if(!is_array($value)) return;
		$rqt_ins = "insert into tu_oeuvres_events(oeuvre_event_tu_num, oeuvre_event_authperso_authority_num, oeuvre_event_order) VALUES ";
		$ordre = 0;
		foreach ($value as $val) {
			if ($val ['code']) {
				$rqt = $rqt_ins."('".$this->id."', '".$val['code']."',  ".$ordre.") ";
				pmb_mysql_query ( $rqt );
				indexation_stack::push($val ['code'], TYPE_AUTHPERSO, "event");
				$ordre ++;
			}
		}
	}
	
	public function delete_oeuvre_event(){
		if(!$this->id) return;
		$rqt_del = "delete from tu_oeuvres_events where oeuvre_event_tu_num=".$this->id ;
		pmb_mysql_query ( $rqt_del );
	}

	private function save_expression_inversed_link($from, $to, $type,$sens=0) {
		global $include_path;
		$to = intval($to);
		$from = intval($from);
		$oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
		if(!isset($oeuvre_link->inverse_of[$type])){
			return;
		}
		$select = 'select oeuvre_link_type from tu_oeuvres_links where oeuvre_link_from = "'.$to.'" and oeuvre_link_to= "'.$from.'" and oeuvre_link_type = "'.$oeuvre_link->inverse_of[$type].'" ';
		$result = pmb_mysql_query($select);
		if(pmb_mysql_num_rows($result)>0){
			return;
		}
		$max_query = 'select max(oeuvre_link_order) from tu_oeuvres_links where oeuvre_link_from = "'.$to.'"';
		$result = pmb_mysql_query($max_query);
		$max_order = pmb_mysql_result($result, 0, 0);
		$query = 'insert into tu_oeuvres_links (oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link, oeuvre_link_order) VALUES ("'.$to.'","'.$from.'","'.$oeuvre_link->inverse_of[$type].'", '.$sens.', 0, "'.($max_order+1).'")';

		pmb_mysql_query ( $query );
	}
	
	private function save_inversed_link($from, $to, $type) {
		global $include_path;
		$to = intval($to);
		$from = intval($from);
		$oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
		if(!isset($oeuvre_link->inverse_of[$type])){	
			return;	
		}
		$select = 'select oeuvre_link_type from tu_oeuvres_links where oeuvre_link_from = "'.$to.'" and oeuvre_link_to= "'.$from.'" and oeuvre_link_type = "'.$oeuvre_link->inverse_of[$type].'" ';
		$result = pmb_mysql_query($select);
		if(pmb_mysql_num_rows($result)>0){
			return;
		}
		$max_query = 'select max(oeuvre_link_order) from tu_oeuvres_links where oeuvre_link_from = "'.$to.'"';
		$result = pmb_mysql_query($max_query);
		$max_order = pmb_mysql_result($result, 0, 0);
		$query = 'insert into tu_oeuvres_links (oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link, oeuvre_link_order) VALUES ("'.$to.'","'.$from.'","'.$oeuvre_link->inverse_of[$type].'", 0, 1, "'.($max_order+1).'")';
		pmb_mysql_query ( $query );
		$indexation_authority = indexations_collection::get_indexation(AUT_TABLE_TITRES_UNIFORMES);
		$indexation_authority->maj($to,'oeuvre_link');
	}
	
	public function delete_other_link() {
		if(!$this->id) return;
		$to_delete = array();
		$select = 'select oeuvre_link_to, oeuvre_link_type from tu_oeuvres_links where oeuvre_link_from="'.$this->id.'"';
		$result = pmb_mysql_query($select);
		
		if(pmb_mysql_num_rows($result)){
			$oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
			$to_delete =array();
			while($row = pmb_mysql_fetch_object($result)){
				if(isset($oeuvre_link->inverse_of[$row->oeuvre_link_type]) && isset($oeuvre_link->inverse_of[$oeuvre_link->inverse_of[$row->oeuvre_link_type]]) && $oeuvre_link->inverse_of[$oeuvre_link->inverse_of[$row->oeuvre_link_type]] === $row->oeuvre_link_type){
					$to_delete[] = array(
						'type' => $oeuvre_link->inverse_of[$row->oeuvre_link_type],
						'from' => $row->oeuvre_link_to
					);
				}
			}
			if(count($to_delete)>0){
				$delete = "";
				foreach($to_delete as $del){
					if ($delete){
						$delete.= " OR ";
					}
					$delete.= '(oeuvre_link_from = "'.$del['from'].'" and oeuvre_link_to = "'.$this->id.'" and oeuvre_link_type = "'.$del['type'].'" and oeuvre_link_other_link = 1 and oeuvre_link_expression = 0)';
				}
				$result = pmb_mysql_query('delete from tu_oeuvres_links where '.$delete);
			}
		}
		
		$rqt_del = "delete from tu_oeuvres_links where oeuvre_link_from='" . $this->id . "' and oeuvre_link_other_link = 1 and oeuvre_link_expression = 0";
		pmb_mysql_query ( $rqt_del );
	}
	
	// ---------------------------------------------------------------
	//		replace_form : affichage du formulaire de remplacement
	// ---------------------------------------------------------------
	public function replace_form() {
		global $titre_uniforme_replace;
		global $msg;
		global $include_path;
	
		if(!$this->id || !$this->name) {
			require_once("$include_path/user_error.inc.php");
			error_message($msg[161], $msg[162], 1, static::format_url('&sub=&id='));
			return false;
		}
		$titre_uniforme_replace=str_replace('!!old_titre_uniforme_libelle!!', $this->display, $titre_uniforme_replace);
		$titre_uniforme_replace=str_replace('!!id!!', $this->id, $titre_uniforme_replace);
		$titre_uniforme_replace=str_replace('!!controller_url_base!!', static::format_url(), $titre_uniforme_replace);
		$titre_uniforme_replace=str_replace('!!cancel_action!!', static::format_back_url(), $titre_uniforme_replace);
		print $titre_uniforme_replace;
		return true;
	}
		
	
	public function check_uses(){
		global $msg;

		$message = '';
	
		/** TODO: Publish the check uses event, it will be trigerred in rf concept plugins) **/		 
		
		
		$evt_handler = events_handler::get_instance();
		$event = new event_titre_uniforme("titre_uniforme", "tu_check_uses");
		$event->set_titre_uniforme_id($this->id);
		$event->set_titre_uniforme_isbd($this->get_isbd());
		$evt_handler->send($event);
		if(!$event->get_error_message()){
		    //On vérifie que l'oeuvre n'est pas lié
		    $requete = "SELECT oeuvre_link_to FROM tu_oeuvres_links WHERE oeuvre_link_from='$this->id' ";
		    
		    $res = pmb_mysql_query($requete);
		    $nbr_lignes = pmb_mysql_result($res, 0, 0);
		    if($nbr_lignes) {
		        $message .=  '<strong>'.$this->display."</strong><br />{$msg['titre_uniforme_delete_linked']}<br /><br />";
		    }
		    
			if(($usage=aut_pperso::delete_pperso(AUT_TABLE_TITRES_UNIFORMES, $this->id,0) )){
				// Cette autorité est utilisée dans des champs perso, impossible de supprimer
				$message .= '<strong>'.$this->display.'</strong><br />'.$msg['autority_delete_error'].'<br /><br />'.$usage['display'];
			}
			// effacement dans les notices
			// récupération du nombre de notices affectées
			$requete = "SELECT count(1) FROM notices_titres_uniformes WHERE ntu_num_tu='$this->id' ";
			
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			if($nbr_lignes) {
				// Ce titre uniforme est utilisé dans des notices, impossible de le supprimer
				$message .=  '<strong>'.$this->display."</strong><br />{$msg['titre_uniforme_delete']}";
			}
			
			// On regarde si l'autorité est utilisée dans des vedettes composées
			$attached_vedettes = vedette_composee::get_vedettes_built_with_element($this->id, TYPE_TITRE_UNIFORME);
			if(count($attached_vedettes)){
				if(isset($event->get_elements()['concept'])){
					if(count(array_diff($event->get_elements()['concept'], $attached_vedettes))){
						$message .= '<strong>'.$this->display."</strong><br />".$msg["vedette_dont_del_autority"].'<br/>'.vedette_composee::get_vedettes_display($attached_vedettes);
					}
				}else{
					$message .= '<strong>'.$this->display."</strong><br />".$msg["vedette_dont_del_autority"].'<br/>'.vedette_composee::get_vedettes_display($attached_vedettes);
				}
			}
			return $message;
		}
		return $event->get_error_message();
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression 
	// ---------------------------------------------------------------
	public function delete() {
		global $msg;
		global $forcing;
				
		if(!$this->id)	// impossible d'accéder à cette notice titre uniforme
			return $msg[403];
	       
		$is_used = '';
		if (!$forcing){
    		$is_used = $this->check_uses();
		}
		if(!$is_used){
			$evt_handler = events_handler::get_instance();
			$event = new event_titre_uniforme("titre_uniforme", "delete");
			$event->set_titre_uniforme_id($this->id);
			$event->set_titre_uniforme_isbd($this->get_isbd());
			$evt_handler->send($event);
			
			// Clean des vedettes
			$id_vedettes_links_deleted=titre_uniforme::delete_vedette_links($this->id);
			foreach ($id_vedettes_links_deleted as $id_vedette){
				$vedette_composee = new vedette_composee($id_vedette);
				$vedette_composee->delete();
			}
			
			// effacement dans la table des titres_uniformes
			$requete = "DELETE FROM titres_uniformes WHERE tu_id='$this->id' ";
			pmb_mysql_query($requete);
			// delete les champs répétables
			$requete = "DELETE FROM tu_distrib WHERE distrib_num_tu='$this->id' ";
			pmb_mysql_query($requete);
			$requete = "DELETE FROM tu_ref WHERE ref_num_tu='$this->id' ";
			pmb_mysql_query($requete);
			$requete = "DELETE FROM tu_subdiv WHERE subdiv_num_tu='$this->id' ";
			pmb_mysql_query($requete);
			
			//suppression dans la table de stockage des numéros d'autorités...
			titre_uniforme::delete_autority_sources($this->id);
			
			$this->delete_other_link();
			$this->delete_oeuvre_event();
			$this->delete_oeuvre_expression();
			$this->delete_tu_notices();
			
			// suppression des auteurs
			$rqt_del = "delete from responsability_tu where responsability_tu_num='".$this->id."' ";
			pmb_mysql_query($rqt_del);
			
			// liens entre autorités
			$aut_link= new aut_link(AUT_TABLE_TITRES_UNIFORMES,$this->id);
			$aut_link->delete();
			
			$aut_pperso= new aut_pperso("tu",$this->id);
			$aut_pperso->delete();
			
			// nettoyage indexation concepts
			$index_concept = new index_concept($this->id, TYPE_TITRE_UNIFORME);
			$index_concept->delete();
			
			// nettoyage indexation
			indexation_authority::delete_all_index($this->id, "authorities", "id_authority", AUT_TABLE_TITRES_UNIFORMES);
			
		// effacement de l'identifiant unique d'autorité
			$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $this->id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
			$authority->delete();
			
			audit::delete_audit(AUDIT_TITRE_UNIFORME,$this->id);
		}else{
			return $is_used;
		}
		return false;
	}

	// Clean des vedettes
	public static function delete_vedette_links($id) {	
		$id_vedettes=array();
		$rqt_responsability = 'select id_responsability_tu, responsability_tu_type from responsability_tu where responsability_tu_num="'.$id.'" ';
		$res_responsability=pmb_mysql_query($rqt_responsability);
		if (pmb_mysql_num_rows($res_responsability)) {
			while($r=pmb_mysql_fetch_object($res_responsability)){
				$object_id=$r->id_responsability_tu;
				$type_aut=$r->responsability_tu_type;				
				$id_vedette=0;	
				switch($type_aut){
					case 0:
						$id_vedette=vedette_link::delete_vedette_link_from_object(new vedette_composee(0,'titre_uniforme'), $object_id, TYPE_TU_RESPONSABILITY);
						break;
					case 1:
						$id_vedette=vedette_link::delete_vedette_link_from_object(new vedette_composee(0,'titre_uniforme'), $object_id, TYPE_TU_RESPONSABILITY_INTERPRETER);
						break;
				}
				if($id_vedette)$id_vedettes[]=$id_vedette;
			}
		}
		return $id_vedettes;
	}
	
	// ---------------------------------------------------------------
	//		delete_autority_sources($idcol=0) : Suppression des informations d'import d'autorité
	// ---------------------------------------------------------------
	public static function delete_autority_sources($idtu=0){
		$tabl_id=array();
		if(!$idtu){
			$requete="SELECT DISTINCT num_authority FROM authorities_sources LEFT JOIN titres_uniformes ON num_authority=tu_id  WHERE authority_type = 'uniform_title' AND tu_id IS NULL";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				while ($ligne = pmb_mysql_fetch_object($res)) {
					$tabl_id[]=$ligne->num_authority;
				}
			}
		}else{
			$tabl_id[]=$idtu;
		}
		foreach ( $tabl_id as $value ) {
	       //suppression dans la table de stockage des numéros d'autorités...
			$query = "select id_authority_source from authorities_sources where num_authority = ".$value." and authority_type = 'uniform_title'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($ligne = pmb_mysql_fetch_object($result)) {
					$query = "delete from notices_authorities_sources where num_authority_source = ".$ligne->id_authority_source;
					pmb_mysql_query($query);
				}
			}
			$query = "delete from authorities_sources where num_authority = ".$value." and authority_type = 'uniform_title'";
			pmb_mysql_query($query);
		}
	}
	
	// ---------------------------------------------------------------
	//		replace($by) : remplacement 
	// ---------------------------------------------------------------
	public function replace($by,$link_save) {
	
		global $msg;
		global $pmb_synchro_rdf;
		global $pmb_ark_activate;
		
		if (($this->id == $by) || (!$this->id))  {
			return $msg[223];
		}
		
		//publication d'un event permettant de signifier que l'on va remplacer une oeuvre par unz autre ;
		$evt_handler = events_handler::get_instance();
		$event = new event_titre_uniforme("titre_uniforme", "replace");
		$event->set_titre_uniforme_id($this->id);
		$event->set_replacement_id($by);
		
		$evt_handler->send($event);
		
		$aut_link= new aut_link(AUT_TABLE_TITRES_UNIFORMES,$this->id);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_TITRES_UNIFORMES,$by);		
		}
		$aut_link->delete();

		vedette_composee::replace(TYPE_TITRE_UNIFORME, $this->id, $by);
		
 		// remplacement liens de type expression et autres liens
		$requete = 'UPDATE tu_oeuvres_links set oeuvre_link_from="'.$by.'" WHERE oeuvre_link_from="'.$this->id.'"';
		@pmb_mysql_query($requete);
		
		$requete = 'UPDATE tu_oeuvres_links set oeuvre_link_to="'.$by.'" WHERE oeuvre_link_to="'.$this->id.'"';
		@pmb_mysql_query($requete);
		
		// En cas de doublons restant sur l'oeuvre on supprime le lien
		$requete = 'DELETE FROM tu_oeuvres_links where oeuvre_link_from="'.$this->id.'" OR oeuvre_link_to="'.$this->id.'"';
		pmb_mysql_query($requete);
		
		// remplacement dans les responsabilités
		$requete = "UPDATE notices_titres_uniformes SET ntu_num_tu='$by' WHERE ntu_num_tu='$this->id' ";
		@pmb_mysql_query($requete);
		
		$requete = "UPDATE responsability_tu set responsability_tu_num ='$by' where responsability_tu_num='".$this->id."' ";
		@pmb_mysql_query($requete);		

		// delete les champs répétables
		$requete = "DELETE FROM tu_distrib WHERE distrib_num_tu='$this->id' ";
		pmb_mysql_query($requete);
		$requete = "DELETE FROM tu_ref WHERE ref_num_tu='$this->id' ";
		pmb_mysql_query($requete);
		$requete = "DELETE FROM tu_subdiv WHERE subdiv_num_tu='$this->id' ";
		pmb_mysql_query($requete);
		
		//nettoyage d'autorities_sources
		$query = "select * from authorities_sources where num_authority = ".$this->id." and authority_type = 'uniform_title'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($row->authority_favorite == 1){
					//on suprime les références si l'autorité a été importée...
					$query = "delete from notices_authorities_sources where num_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
					$query = "delete from authorities_sources where id_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
				}else{
					//on fait suivre le reste
					$query = "update authorities_sources set num_authority = ".$by." where id_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
				}
			}
		}
		
		// nettoyage indexation concepts
		$index_concept = new index_concept($this->id, TYPE_TITRE_UNIFORME);
		$index_concept->delete();
		
		//Remplacement dans les champs persos sélecteur d'autorité
		aut_pperso::replace_pperso(AUT_TABLE_TITRES_UNIFORMES, $this->id, $by);
		
		audit::delete_audit(AUDIT_TITRE_UNIFORME,$this->id);
				
		// nettoyage indexation
		indexation_authority::delete_all_index($this->id, "authorities", "id_authority", AUT_TABLE_TITRES_UNIFORMES);
		
		if ($pmb_ark_activate) {
		    $idReplaced = authority::get_authority_id_from_entity($this->id, AUT_TABLE_TITRES_UNIFORMES);
		    $idReplacing = authority::get_authority_id_from_entity($by, AUT_TABLE_TITRES_UNIFORMES);
		    if ($idReplaced && $idReplacing) {
		        $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplaced);
		        $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplacing);
		        $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
		    }
		}
		// effacement de l'identifiant unique d'autorité
		$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $this->id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
		$authority->delete();
		
		// effacement dans la table des titres_uniformes
		$requete = "DELETE FROM titres_uniformes WHERE tu_id='$this->id' ";
		pmb_mysql_query($requete);
		
		titre_uniforme::update_index($by);
		
		//mise à jour de l'oeuvre rdf
		if($pmb_synchro_rdf){
			$synchro_rdf = new synchro_rdf();
			$synchro_rdf->replaceAuthority($this->id,$by,'oeuvre');
		}
		
		return FALSE;
	}
	
	/**
	 * Initialisation du tableau de valeurs pour update et import
	 */
	protected static function get_default_data() {
		return array(
				'name' => '',
				'tu_form' => '',
				'form_selector' => '',
				'date_date' => '',
				'date' => '',
				'subject' => '',
				'place' => '',
				'characteristic' => '',
				'intended_termination' => '',
				'intended_audience' => '',
				'equinox' => '',
				'coordinates' => '',
				'tonalite' => '',
				'tonalite_selector' => '',
				'comment' => '',
				'import_denied' => 0,
				'oeuvre_nature' => '',
				'oeuvre_nature_nature' => '',
				'oeuvre_type' => '',
				'oeuvre_expression' => '',
				'other_link' => '',
				'oeuvre_expression_from' => '',
				'oeuvre_event' => '',
				'tu_notices' => '',
				'statut' => 1,
				'thumbnail_url' => '',
				'distrib' => array(),
				'ref' => array(),
				'subdiv' => array(),
				'authors' => array()
		);
	}
	
	// ---------------------------------------------------------------
	//		update($value) : mise à jour 
	// ---------------------------------------------------------------
	public function update($value, $forcing = false ) {
		global $msg;
		global $include_path;
		global $pmb_synchro_rdf;
		global $thesaurus_concepts_active,$max_aut0,$max_aut1;
		global $pmb_authors_qualification;
		global $mapping_source_type;
		global $mapping_source_id;
		global $opac_enrichment_bnf_sparql;
		global $is_duplication;
		
		$value = array_merge(static::get_default_data(), $value);
		
		if (! $value ['name'])
			return false;
		
		$f_aut = array();
		// auteurs
		for ($i=0; $i<$max_aut0; $i++) {
			eval("global \$f_aut0_id$i; \$var_autid=\$f_aut0_id$i;");
			eval("global \$f_f0_code$i; \$var_autfonc=\$f_f0_code$i;");
			if($var_autid){
				$f_aut[] = array (
						'id' => $var_autid,
						'fonction' => $var_autfonc,
						'type' => '0',
						'ordre' => $i
				);
			}
		}
		// interpretes
		for ($i=0; $i<$max_aut1; $i++) {
			eval("global \$f_aut1_id$i; \$var_autid=\$f_aut1_id$i;");
			eval("global \$f_f1_code$i; \$var_autfonc=\$f_f1_code$i;");
			if($var_autid){
				$f_aut[] = array (
						'id' => $var_autid,
						'fonction' => $var_autfonc,
						'type' => '1',
						'ordre' => $i
				);
			}
		}
		
		// nettoyage des chaînes en entrée		
		$value['name'] = clean_string($value['name']);
		$value['tu_form'] = clean_string($value['tu_form']);
		$value['form'] = clean_string($value['tu_form']);
		$value['form_selector'] = clean_string($value['form_selector']);
		$value['date_date'] = detectFormatDate($value['date']);
		$value['date'] = clean_string($value['date']);
		$value['place'] = clean_string($value['place']);
		$value['intended_termination'] = clean_string($value['intended_termination']);
		$value['intended_audience'] = clean_string($value['intended_audience']);
		$value['equinox'] = clean_string($value['equinox']);
		$value['coordinates'] = clean_string($value['coordinates']);
		$value['tonalite'] = clean_string($value['tonalite']);
		$value['tonalite_selector'] = clean_string($value['tonalite_selector']);
		$value['oeuvre_nature'] = clean_string ( $value ['oeuvre_nature'] );
		$mc_oeuvre_nature = marc_list_collection::get_instance('oeuvre_nature');
		$value['oeuvre_nature_nature'] = clean_string($mc_oeuvre_nature->attributes[$value ['oeuvre_nature']]['NATURE']);
		$value['oeuvre_type'] = clean_string ( $value ['oeuvre_type'] );
		$value['authors'] = $f_aut;
		
		if (!$forcing) {
		    $titre=titre_uniforme::import_tu_exist($value,1,$this->id);
    		if($titre){
    			//require_once("$include_path/user_error.inc.php");
    			//warning($msg["aut_titre_uniforme_creation"], $msg["aut_titre_uniforme_doublon_erreur"]);
    		    print $this->warning_already_exist($msg["aut_titre_uniforme_creation"], $msg["aut_titre_uniforme_doublon_erreur"], $value);
    			
    			return FALSE;
    		}
		}
				
		$flag_index=0;
		$requete  = "SET ";
		$requete .= "tu_name='".$value["name"]."', ";
		$requete .= "tu_forme='".$value["tu_form"]."', ";
		$requete .= "tu_forme_marclist='".$value["form_selector"]."', ";
		$requete .= "tu_date='".$value["date"]."', ";
		$requete .= "tu_date_date='".$value["date_date"]."', ";
		$requete .= "tu_sujet='".$value["subject"]."', ";
		$requete .= "tu_lieu='".$value["place"]."', ";
		$requete .= "tu_histoire='".$value["history"]."', ";
		$requete .= "tu_caracteristique='".$value["characteristic"]."', ";
		$requete .= "tu_completude='".$value["intended_termination"]."', ";
		$requete .= "tu_public='".$value["intended_audience"]."', ";
		$requete .= "tu_contexte='".$value["context"]."', ";
		$requete .= "tu_equinoxe='".$value["equinox"]."', ";
		$requete .= "tu_coordonnees='".$value["coordinates"]."', ";
		$requete .= "tu_tonalite='".$value["tonalite"]."', ";	
		$requete .= "tu_tonalite_marclist='".$value["tonalite_selector"]."', ";
		$requete .= "tu_comment='".$value["comment"]."', ";
		$requete .= "tu_import_denied='" . $value ["import_denied"] . "', ";
		$requete .= "tu_oeuvre_nature='" . $value ["oeuvre_nature"] . "', ";
		$requete .= "tu_oeuvre_nature_nature='" . $value ["oeuvre_nature_nature"] . "', ";
		$requete .= "tu_oeuvre_type='" . $value ["oeuvre_type"] . "' ";

		if($this->id) {
			// update
			$requete = 'UPDATE titres_uniformes '.$requete;
			$requete .= ' WHERE tu_id='.$this->id.' ;';
			
			if(pmb_mysql_query($requete)) {
				$flag_index=1;
			} else {
				require_once("$include_path/user_error.inc.php"); 
				warning($msg["aut_titre_uniforme_creation"], $msg["aut_titre_uniforme_modif_erreur"]);
				return FALSE;
			}	
			
			audit::insert_modif (AUDIT_TITRE_UNIFORME, $this->id) ;
		} else {
			// creation
			$requete = 'INSERT INTO titres_uniformes '.$requete.' ';
			$result = pmb_mysql_query($requete);
			if($result) {
				$this->id=pmb_mysql_insert_id();				
			} else {
				require_once("$include_path/user_error.inc.php"); 
				warning($msg["aut_titre_uniforme_creation"], $msg["aut_titre_uniforme_creation_erreur"]);
				return FALSE;
			}
			audit::insert_creation(AUDIT_TITRE_UNIFORME, $this->id) ;
		}
		
		$this->update_oeuvre_expression ( $value ['oeuvre_expression'] );
		
		$this->update_other_link ( $value ['other_link'] );
		
		$this->update_oeuvre_expression_from ( $value ['oeuvre_expression_from'] );
		
		$this->update_oeuvre_event ( $value ['oeuvre_event'] );
		
		$this->update_tu_notices ($value['tu_notices']);
		
		
			// Clean des vedettes
			$id_vedettes_links_deleted=titre_uniforme::delete_vedette_links($this->id);
			
			// traitement des auteurs
			// la variable $f_aut a été renseignée au début de la fonction
			// pour gérer les auteurs dans la recherche des doublons
			$rqt_del = "delete from responsability_tu where responsability_tu_num='".$this->id."' ";
			$res_del = pmb_mysql_query($rqt_del);
			$rqt_ins = "INSERT INTO responsability_tu (responsability_tu_author_num, responsability_tu_num, responsability_tu_fonction, responsability_tu_type, responsability_tu_ordre) VALUES ";		
			$i=0;	
			$var_name='saisie_titre_uniforme_role_composed';
			global ${$var_name};
			$role_composed=${$var_name};
			$var_name='saisie_titre_uniforme_role_autre_composed';
			global ${$var_name};
			$role_composed_autre=${$var_name};
			$id_vedettes_used=array();
			while ($i<=count ($f_aut)-1) {
				$id_aut=$f_aut[$i]['id'];
				if ($id_aut) {
					$fonc_aut = $f_aut[$i]['fonction'];
					$type_aut = $f_aut[$i]['type'];
					$ordre_aut = $f_aut[$i]['ordre'];
					$rqt = $rqt_ins . " ('".$id_aut."','".$this->id."','".$fonc_aut."','".$type_aut."', '".$ordre_aut."') ";				
					$res_ins = @pmb_mysql_query($rqt);
					$id_responsability_tu=pmb_mysql_insert_id();
					if($pmb_authors_qualification){
						switch($type_aut){
							case 0:
								//On duplique la vedette en mode duplication
								if(! empty($is_duplication) && $is_duplication == 1) {
									$role_composed[$ordre_aut]["id"] = 0;
								}
								$id_vedette=$this->update_vedette(stripslashes_array($role_composed[$ordre_aut]),$id_responsability_tu,TYPE_TU_RESPONSABILITY);
							break;
							case 1:
								if(! empty($is_duplication) && $is_duplication == 1) {
									$role_composed_autre[$ordre_aut]["id"] = 0;
								} 
								$id_vedette=$this->update_vedette(stripslashes_array($role_composed_autre[$ordre_aut]),$id_responsability_tu,TYPE_TU_RESPONSABILITY_INTERPRETER);
							break;			
						}
						if($id_vedette)$id_vedettes_used[]=$id_vedette; 
					}
				}
				$i++;
			}
			foreach ($id_vedettes_links_deleted as $id_vedette){
				if(!in_array($id_vedette,$id_vedettes_used)){
					$vedette_composee = new vedette_composee($id_vedette);
					$vedette_composee->delete();
				}
			}	
			$aut_link= new aut_link(AUT_TABLE_TITRES_UNIFORMES,$this->id);
			$aut_link->save_form();
				
			$aut_pperso= new aut_pperso("tu",$this->id);
			if($aut_pperso->save_form()){
				$this->cp_error_message = $aut_pperso->error_message; 
				return false;
			}
		// }
		
		//update authority informations
		$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $this->id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
		$authority->set_num_statut($value['statut']);
		$authority->set_thumbnail_url($value['thumbnail_url']);
		$authority->update();
		
		// Indexation concepts
		if($thesaurus_concepts_active == 1 ){
			$index_concept = new index_concept($this->id, TYPE_TITRE_UNIFORME);
			$index_concept->save();
		}
		
		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($this->id, TYPE_TITRE_UNIFORME);
		
		// Gestion des champ répétables
		$requete = "DELETE FROM tu_distrib WHERE distrib_num_tu='$this->id' ";
		pmb_mysql_query($requete);
		$requete = "DELETE FROM tu_ref WHERE ref_num_tu='$this->id' ";
		pmb_mysql_query($requete);
		$requete = "DELETE FROM tu_subdiv WHERE subdiv_num_tu='$this->id' ";
		pmb_mysql_query($requete);
		
		// Distribution instrumentale et vocale (pour la musique)
		if(is_array($value['distrib'])) {
			for($i=0;$i<count($value['distrib']);$i++) {
				$requete = "INSERT INTO tu_distrib SET
				distrib_num_tu='$this->id',
				distrib_name='".$value['distrib'][$i]."',
				distrib_ordre='$i' ";
				pmb_mysql_query($requete);
			}
		}
		// Référence numérique (pour la musique)
		if(is_array($value['ref'])) {
			for($i=0;$i<count($value['ref']);$i++) {
				$requete = "INSERT INTO tu_ref SET
				ref_num_tu='$this->id',
				ref_name='".$value['ref'][$i]."',
				ref_ordre='$i' ";
				pmb_mysql_query($requete);
			}
		}
		// Subdivison de forme
		if(is_array($value['subdiv'])) {
			for($i=0;$i<count($value['subdiv']);$i++) {
				$requete = "INSERT INTO tu_subdiv SET
				subdiv_num_tu='$this->id',
				subdiv_name='".$value['subdiv'][$i]."',
				subdiv_ordre='$i' ";
				pmb_mysql_query($requete);
			}
		}
		
		// mise à jour du champ index du titre uniforme
		if ($this->id)
			titre_uniforme::update_index_tu ( $this->id );
		
		// réindexation de la notice
		titre_uniforme::update_index($this->id);

		//Enrichissement
		if ($this->id && $opac_enrichment_bnf_sparql) {
			titre_uniforme::tu_enrichment($this->id);
		}
		
		//mise à jour de l'oeuvre rdf
		if($flag_index && $pmb_synchro_rdf){
			$synchro_rdf = new synchro_rdf();
			$synchro_rdf->updateAuthority($this->id,'oeuvre');
		}		
		
		//Evenement publié à chaque mise à jour d'un titre uniforme
		$evt_handler = events_handler::get_instance();
		$event = new event_titre_uniforme("titre_uniforme", "update");
		$event->set_titre_uniforme_id($this->id);
		$event->set_titre_uniforme_isbd($this->get_isbd());
		if(isset($mapping_source_type)){
			$event->set_source_type($mapping_source_type);
		}
		if(isset($mapping_source_id)){
			$event->set_source_id($mapping_source_id);
		}
		$evt_handler->send($event);
		
		return TRUE;
	}
	
	public function update_vedette($data,$id,$type){
		if ($data["elements"]) {
			$vedette_composee = new vedette_composee($data["id"],'tu_authors');
			if ($data["value"]) {
				$vedette_composee->set_label($data["value"]);
			}	
			// On commence par réinitialiser le tableau des éléments de la vedette composée
			$vedette_composee->reset_elements();	
			// On remplit le tableau des éléments de la vedette composée
			$vedette_composee_id=0;
			$tosave=false;
			foreach ($data["elements"] as $subdivision => $elements) {
				if ($elements["elements_order"] !== "") {
					$elements_order = explode(",", $elements["elements_order"]);
					foreach ($elements_order as $position => $num_element) {
						if ($elements[$num_element]["id"] && $elements[$num_element]["label"]) {
							$tosave=true;
							$velement = $elements[$num_element]["type"];
							if(strpos($velement,"vedette_ontologies") === 0){
								$velement = "vedette_ontologies";
							}
							$available_field_class_name = $vedette_composee->get_at_available_field_num($elements[$num_element]['available_field_num']);
							if(empty($available_field_class_name['params'])) {
								$available_field_class_name['params'] = array();
							}
							$vedette_element = new $velement($elements[$num_element]['available_field_num'],$elements[$num_element]["id"], $elements[$num_element]["label"], $available_field_class_name['params']);
							$vedette_composee->add_element($vedette_element, $subdivision, $position);
						}
					}
				}
			}
			if($tosave)$vedette_composee_id = $vedette_composee->save();
		}
		if ($vedette_composee_id) {
			vedette_link::save_vedette_link($vedette_composee, $id, $type);
		}		
		return $vedette_composee_id;		
	}	
	// ---------------------------------------------------------------
	//		import() : import d'un titre_uniforme
	// ---------------------------------------------------------------
	// fonction d'import de notice titre_uniforme 
	public static function import($value,$from_form=0) {
		$value = array_merge(static::get_default_data(), $value);
		
		// Si vide on sort
		if (trim ( $value ['name'] ) == '')
			return FALSE;
		if(!$from_form) {
			$value['name'] = addslashes($value['name']);
			$value['form'] = addslashes($value['form']);
			if($value['date_date']) {
				$value['date_date'] = detectFormatDate($value['date_date']);
			}else {
				$value['date_date'] = detectFormatDate($value['date']);
			}
			$value['date'] = addslashes($value['date']);
			$value['subject'] = addslashes($value['subject']);
			$value['place'] = addslashes($value['place']);
			$value['history'] = addslashes($value['history']);
			$value['characteristic'] = addslashes($value['characteristic']);
			$value['intended_termination'] = addslashes($value['intended_termination']);
			$value['intended_audience'] = addslashes($value['intended_audience']);
			$value['context'] = addslashes($value['context']);
			$value['equinox'] = addslashes($value['equinox']);
			$value['coordinates'] = addslashes($value['coordinates']);
			$value['tonalite'] = addslashes($value['tonalite']);
			$value['comment'] = addslashes($value['comment']);
			$value['databnf_uri'] = addslashes($value['databnf_uri']);
			$value['oeuvre_nature'] = addslashes($value['oeuvre_nature']);
			$value['oeuvre_nature_nature'] = addslashes($value['oeuvre_nature_nature']);
			$value['oeuvre_type'] = addslashes($value['oeuvre_type']);
			if(is_array($value['distrib'])) {
				for($i=0;$i<count($value['distrib']);$i++) {	
					$value['distrib'][$i]= addslashes($value['distrib'][$i]);		
				}
			}
			if(is_array($value['ref'])) {
				for($i=0;$i<count($value['ref']);$i++) {	
					$value['ref'][$i]= addslashes($value['ref'][$i]);		
				}
			}
			if(is_array($value['subdiv'])) {
				for($i=0;$i<count($value['subdiv']);$i++) {	
					$value['subdiv'][$i]= addslashes($value['subdiv'][$i]);		
				}
			}
			if(is_array($value['authors'])) {
				for($i=0;$i<count($value['authors']);$i++) {
					// les champs auteurs sont addslashes dans import auteur
					$value['authors'][$i]['type'] = addslashes($value['authors'][$i]['type']);	// 70, 71, 72
					$value['authors'][$i]['interpreter'] = addslashes($value['authors'][$i]['interpreter']);	// 0, 1
					$value['authors'][$i]['fonction'] = addslashes($value['authors'][$i]['fonction']);
				}
			}
		}	
		
		$marc_key = marc_list_collection::get_instance("music_key");
		$marc_form = marc_list_collection::get_instance("music_form");
		
		$flag_form = false;
		$flag_key = false;
		foreach ($marc_form->table as $value_form=>$libelle_form){
			if($value_form == $value['form']){
				$flag_form = true;
			}
		}
		foreach ($marc_key->table as $value_key=>$libelle_key){
			if($value_key == $value['tonalite']){
				$flag_key = true;
			}
		}
						
		if(count($value['authors'])){
			for($i=0;$i<count($value['authors']);$i++) {
				if($value['authors'][$i]['id']){
					$tu_auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $value['authors'][$i]['id']);
					if(!$tu_auteur->id){
						// id non valide
						$value['authors'][$i]['id']=0;
					}
				}
				if(!$value['authors'][$i]['id']){
					// création ou déjà existant. auteur::import addslashes les champs
					$value['authors'][$i]['id']=auteur::import($value['authors'][$i]); 
				}
			}
		}	
				
		// $value déjà addslashes plus haut -> 1 
		$titre=titre_uniforme::import_tu_exist($value,1);
		if($titre){
			return $titre;
		}
			
		$requete  = "INSERT INTO titres_uniformes SET ";
		$requete .= "tu_name='".$value["name"]."', ";
		$requete .= ((!$flag_form)?"tu_forme='":"tu_forme_marclist='").$value['form']."', ";
		$requete .= "tu_date='".$value["date"]."', ";
		$requete .= "tu_date_date='".$value["date_date"]."', ";
		$requete .= "tu_sujet='".$value["subject"]."', ";
		$requete .= "tu_lieu='".$value["place"]."', ";
		$requete .= "tu_histoire='".$value["history"]."', ";
		$requete .= "tu_caracteristique='".$value["characteristic"]."', ";
		$requete .= "tu_completude='".$value["intended_termination"]."', ";
		$requete .= "tu_public='".$value["intended_audience"]."', ";
		$requete .= "tu_contexte='".$value["context"]."', ";
		$requete .= "tu_equinoxe='".$value["equinox"]."', ";
		$requete .= "tu_coordonnees='".$value["coordinates"]."', ";
		$requete .= ((!$flag_key)?"tu_tonalite='":"tu_tonalite_marclist='").$value['tonalite']."', ";		
		$requete .= "tu_comment='".$value["comment"]."', ";		
		$requete .= "tu_oeuvre_nature='".$value["oeuvre_nature"]."',
					tu_oeuvre_type='".$value["oeuvre_type"]."',
					tu_oeuvre_nature_nature='".$value["oeuvre_nature_nature"]."' ";
		
		//AR - 17/02/16 - Pour le moment, ce n'existe toujours pas dans PMB
		//$requete .= "tu_databnf_uri='".$value["databnf_uri"]."' ";
		
		// insertion du titre uniforme	et mise à jour de l'index tu
		if(pmb_mysql_query($requete)) {
			$tu_id=pmb_mysql_insert_id();			
		} else {
			return FALSE;
		}		
		
		if(count($value['authors'])){			
			$ordre=0;
			$rqt_ins = "INSERT INTO responsability_tu (responsability_tu_author_num, responsability_tu_num, responsability_tu_fonction, responsability_tu_type, responsability_tu_ordre) VALUES ";
			foreach($value['authors'] as $author){				
				if($author['id']){			
					$rqt = $rqt_ins . " ('".$author['id']."','".$tu_id."','".$author['fonction']."','".$author['interpreter']."', $ordre) " ;
					@pmb_mysql_query($rqt);
					$ordre++;						
				}		
			}				
		}
		
		// Distribution instrumentale et vocale (pour la musique)
		for($i=0;$i<count($value['distrib']);$i++) {
			$requete = "INSERT INTO tu_distrib SET
			distrib_num_tu='$tu_id',
			distrib_name='".$value['distrib'][$i]."',
			distrib_ordre='$i' ";
			pmb_mysql_query($requete);
		}
		// Référence numérique (pour la musique)
		for($i=0;$i<count($value['ref']);$i++) {
			$requete = "INSERT INTO tu_ref SET
			ref_num_tu='$tu_id',
			ref_name='".$value['ref'][$i]."',
			ref_ordre='$i' ";
			pmb_mysql_query($requete);
		}
		// Subdivision de forme
		for($i=0;$i<count($value['subdiv']);$i++) {
			$requete = "INSERT INTO tu_subdiv SET
			subdiv_num_tu='$tu_id',
			subdiv_name='".$value['subdiv'][$i]."',
			subdiv_ordre='$i' ";
			pmb_mysql_query($requete);
		}
		
		audit::insert_creation(AUDIT_TITRE_UNIFORME, $tu_id) ;		
		
		//update authority informations
		$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $tu_id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
		$authority->set_num_statut($value["statut"]);
		$authority->set_thumbnail_url($value['thumbnail_url']);
		$authority->update();
		
		// mise à jour du champ index du titre uniforme
		if($tu_id) {
			titre_uniforme::update_index_tu($tu_id);
			titre_uniforme::tu_enrichment($tu_id);		
		}
		
		return 	$tu_id;		
	}
	
	// ---------------------------------------------------------------
	//		import_tu_exist() : Recherche si le titre uniforme existe déjà
	// ---------------------------------------------------------------
	public static function import_tu_exist($value,$from_form=0,$tu_id=0) {
		// Si vide on sort
		if (trim ( $value ['name'] ) == '')
			return FALSE;
		
		$marc_key = new marc_list("music_key");
		$marc_form = new marc_list("music_form");

		if(!is_array($value['distrib'])) {
		    $value['distrib'] = [];
		}
		if(!is_array($value['ref'])) {
		    $value['ref'] = [];
		}
		if(!is_array($value['authors'])) {
		    $value['authors'] = [];
		}
		if(!is_array($value['oeuvre_event'])) {
		    $value['oeuvre_event'] = [];
		}
		if(!is_array($value['subdiv'])) {
		    $value['subdiv'] = [];
		}
		if(!$from_form) {
			$value['name'] = addslashes($value['name']);
			$value['tonalite'] = addslashes($value['tonalite']);
			$value['form'] = addslashes($value['form']);
			$value['date'] = addslashes($value['date']);
			$value['subject'] = addslashes($value['subject']);
			$value['place'] = addslashes($value['place']);
			$value['history'] = addslashes($value['history']);
			$value['characteristic'] = addslashes($value['characteristic']);
			$value['intended_termination'] = addslashes($value['intended_termination']);
			$value['intended_audience'] = addslashes($value['intended_audience']);
			$value['context'] = addslashes($value['context']);
			$value['equinox'] = addslashes($value['equinox']);
			$value['coordinates'] = addslashes($value['coordinates']);
			$value ['oeuvre_nature'] = addslashes ( $value ['oeuvre_nature'] );
			$value ['oeuvre_type'] = addslashes ( $value ['oeuvre_type'] );
			
			for($i=0;$i<count($value['distrib']);$i++) {	
				$value['distrib'][$i]= addslashes($value['distrib'][$i]);		
			}
			for($i=0;$i<count($value['ref']);$i++) {	
				$value['ref'][$i]= addslashes($value['ref'][$i]);		
			}
			
			for($i=0;$i<count($value['authors']);$i++) {
				$value['authors'][$i]['type']= addslashes($value['authors'][$i]['type']);
				$value['authors'][$i]['fonction']= addslashes($value['authors'][$i]['fonction']);
			}
		}	
		$flag_form = false;
		$flag_key = false;
		//Si une valeur est présente pour la forme, on vérifie si la valeur est existante dans la marclist, si oui, on set le champs marclist en base (flag)
		if(!empty($value['form'])){
			foreach ($marc_form->table as $key=>$form_value){
				if($key == $value['form']){
					$flag_form = true;
				}
			}
		}
		//Si une valeur est présente pour la tonalité, on vérifie si la valeur est existante dans la marclist, si oui, on set le champs marclist en base (flag)
		if($value['tonalite']){
			foreach ($marc_key->table as $key=>$tonalite_value){
				if($key == $value['tonalite']){
					$flag_key = true;
				}
			}
		}
		$dummy = "SELECT * FROM titres_uniformes WHERE tu_name='".$value['name']."' ";
		$dummy.= ((!$flag_key)?"AND tu_tonalite='":"AND tu_tonalite_marclist='").$value['tonalite']."' ";
		$dummy.= ((!$flag_form)?"AND tu_forme='":"AND tu_forme_marclist='").$value['form']."' AND tu_date='".$value['date']."' AND tu_sujet='".$value['subject']."' AND tu_lieu='".$value['place']."' ";
		$dummy.= "AND tu_histoire='".$value['history']."' AND tu_caracteristique='".$value['characteristic']."' AND tu_completude='".$value['intended_termination']."' ";
		$dummy.= "AND tu_public='".$value['intended_audience']."' AND tu_contexte='".$value['context']."' AND tu_coordonnees='".$value['coordinates']."' ";
		$dummy .= "AND tu_equinoxe='" . $value ['equinox'] . "' AND tu_oeuvre_nature='" . $value ['oeuvre_nature'] . "' AND tu_oeuvre_type='" . $value ['oeuvre_type'] . "' ";
		if ($tu_id) {
			$dummy = $dummy . "and tu_id!='" . $tu_id . "'"; // Pour la création ou la mise à jour par l'interface
		}
		
		$check = pmb_mysql_query($dummy);
		
		if (pmb_mysql_error()=="" && pmb_mysql_num_rows($check)) {
			while($row = pmb_mysql_fetch_object($check)){
				$tu_id=$row->tu_id;
				$different=false;
				
				//Test si les titres de même nom ont aussi la (ou les) même distribution
				if(count($value['distrib']) == 0){ //Si le titre que je veux ajouter n'a pas de distribution je regarde si celui qui existe en a une
					$requete = "select distrib_num_tu from tu_distrib where  
					distrib_num_tu='$tu_id' ";
					$test = pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($test)) {
						$different = true; //S'il a une distribution, le titre que je veux ajouter est différent
					}
					
				}else{
					//On teste s'il y a autant de distribution
					$requete = "select distrib_num_tu from tu_distrib where distrib_num_tu='$tu_id' ";
					$nb=pmb_mysql_num_rows(pmb_mysql_query($requete));
					if($nb != count($value['distrib'])){ //Si il y en a pas autant c'est un titre différent
						$different = true;
					}else{ //Sinon on regarde si ce sont les mêmes
						$nb_occurence=array_count_values($value['distrib']);//avoir le nombre d'occurence de chaque terme
						for($i=0;$i<count($value['distrib']);$i++) {
							$requete = "select count(distrib_num_tu) from tu_distrib where  
							distrib_num_tu='$tu_id' and 
							distrib_name='".$value['distrib'][$i]."' group by distrib_num_tu "; 
							$test = pmb_mysql_query($requete);
							$nb=@pmb_mysql_result($test,0,0);
							if (!$nb) {
								$different = true; //Si une des distributions n'existe pas c'est un titre uniforme différent
							}elseif($nb != $nb_occurence[$value['distrib'][$i]]){
								$different = true; //Si le nombre de cette distribution est différent c'est un titre uniforme différent
							}
						}	
					}
				}
				//Test si les titres de même nom ont aussi la (ou les) même réference
				if(count($value['ref']) == 0){ //Si le titre que je veux ajouter n'a pas de référence, je regarde si celui qui existe en a une
					$requete = "select ref_num_tu from tu_ref where  
					ref_num_tu='$tu_id' ";
					$test = pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($test)) {
						$different = true; //S'il a une réference, le titre que je veux ajouter est différent
					}
					
				}else{
					//On teste s'il y a autant de réference
					$requete = "select ref_num_tu from tu_ref where ref_num_tu='$tu_id' ";
					$nb=pmb_mysql_num_rows(pmb_mysql_query($requete));
					if($nb != count($value['ref'])){ //Si il y en a pas autant c'est un titre différent
						$different = true;
					}else{ //Sinon on regarde si ce sont les mêmes
						$nb_occurence=array_count_values($value['ref']);//avoir le nombre d'occurence de chaque terme
						for($i=0;$i<count($value['ref']);$i++) {
							$requete = "select count(ref_num_tu) from tu_ref where  
							ref_num_tu='$tu_id' and 
							ref_name='".$value['ref'][$i]."' group by ref_num_tu "; 
							$test = pmb_mysql_query($requete);
							$nb=@pmb_mysql_result($test,0,0);
							if (!$nb) {
								$different = true; //Si une des réference n'existe pas c'est un titre uniforme différent
							}elseif($nb != $nb_occurence[$value['ref'][$i]]){
								$different = true; //Si le nombre de cette réference est différent c'est un titre uniforme différent
							}
						}	
					}
				}				
				// Auteurs				
				$rqt = "select author_id, responsability_tu_fonction, responsability_tu_type ";
				$rqt.= "from responsability_tu, authors where responsability_tu_num='$tu_id' and responsability_tu_author_num=author_id order by responsability_tu_type, responsability_tu_ordre ";			
				$res_sql = pmb_mysql_query($rqt);				
				
				if (pmb_mysql_num_rows($res_sql) != count($value['authors'])) {
					$different = true;
				} elseif (pmb_mysql_num_rows($res_sql)) {
					$found=0;
					while ($resp_tu=pmb_mysql_fetch_object($res_sql)) {
						foreach($value['authors'] as $author){
							if($author['id'] == $resp_tu->author_id && $author['fonction'] == $resp_tu->responsability_tu_fonction && $author['type'] == $resp_tu->responsability_tu_type){
								$found++;
							}
						}
					}
					if ($found != count($value['authors'])) {
						$different = true;
					}
				}
				
				/**
				 * Ajout du test de dédoublonnages sur les evenements
				 */
				if(count($value['oeuvre_event'])){
					foreach($value['oeuvre_event'] as $value_subarray){
						foreach($value_subarray as $event_id){
							$query = 'select * from tu_oeuvres_events where oeuvre_event_authperso_authority_num = "'.$event_id.'" and oeuvre_event_tu_num = "'.$tu_id.'"';
							$result = pmb_mysql_query($query);
							if(!pmb_mysql_num_rows($result)){
								$different = true;
								break;
							}
						}
					}
				}
				
				if($different == false){ //Si le titre n'est pas différent on retourne l'id du titre identique
					return $tu_id;
				}
			}
		}		
		// Subdivision de forme 
		for($i=0;$i<count($value['subdiv']);$i++) {
			
		}	
		return 0;
	}
	
	// ---------------------------------------------------------------
	//		search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	public static function search_form() {
		global $user_query_tpl, $user_input;
		global $msg, $charset;
		global $oeuvre_nature_selector,$oeuvre_type_selector;
		global $authority_statut;
		
		$oeuvres_nature = new marc_select('oeuvre_nature', 'oeuvre_nature_selector', $oeuvre_nature_selector, '', '0', $msg['authorities_select_all']);
		$oeuvres_nature->first_item_at_last();
		$oeuvres_type  = new marc_select('oeuvre_type', 'oeuvre_type_selector', $oeuvre_type_selector, '', '0', $msg['authorities_select_all']);
		
		$user_query_tpl = str_replace ('!!user_query_title!!', $msg[357]." : ".$msg['aut_menu_titre_uniforme'] , $user_query_tpl);
		$user_query_tpl = str_replace ('!!action!!', static::format_url('&sub=reach&id='), $user_query_tpl);
		$user_query_tpl = str_replace ('!!add_auth_msg!!', $msg["aut_titre_uniforme_ajouter"] , $user_query_tpl);
		$user_query_tpl = str_replace ('!!add_auth_act!!', static::format_url('&sub=titre_uniforme_form'), $user_query_tpl);
		$user_query_tpl = str_replace ('<!-- lien_derniers -->', "<a href='".static::format_url('&sub=titre_uniforme_last')."'>".$msg['aut_titre_uniforme_derniers_crees']."</a>", $user_query_tpl);
		$user_query_tpl = str_replace('<!-- sel_authority_statuts -->', authorities_statuts::get_form_for(AUT_TABLE_TITRES_UNIFORMES, $authority_statut, true), $user_query_tpl);
		$user_query_tpl = str_replace('!!user_input!!',htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query_tpl);
		$user_query_tpl = str_replace ( '<!-- sel_oeuvres_nature -->',$oeuvres_nature->get_radio_selector(), $user_query_tpl);
		$user_query_tpl = str_replace ( '<!-- sel_oeuvres_type -->',$oeuvres_type->display, $user_query_tpl);
		print pmb_bidi($user_query_tpl) ;
	}
	
	//---------------------------------------------------------------
	// update_index($id) : maj des n-uplets la table notice_global_index en rapport avec ce titre uniforme
	//---------------------------------------------------------------
	public static function update_index($id, $datatype = 'all') {
		indexation_stack::push($id, TYPE_TITRE_UNIFORME, $datatype);
		
		// On cherche tous les n-uplet de la table notice correspondant à ce titre_uniforme.
		$query = "select distinct ntu_num_notice as notice_id from notices_titres_uniformes where ntu_num_tu = ".$id;
		authority::update_records_index($query, 'uniformtitle');
	}
	
	//---------------------------------------------------------------
	// get_informations_from_unimarc : ressort les infos d'un titre uniforme depuis une notice unimarc
	//---------------------------------------------------------------
	
	public static function get_informations_from_unimarc($fields,$zone){
		$data = array();
		if($zone == "2"){
			$data['name'] = $fields[$zone.'30'][0]['a'][0];
			$data['tonalite']= $fields[$zone.'30'][0]['u'][0];
			$data['date']= $fields[$zone.'30'][0]['k'][0];
			$data['distrib'] = array();
			for($i=0 ; $i<count($fields[$zone.'30'][0]['r']) ; $i++){
				$data['distrib'][] = $fields[$zone.'30'][0]['r'][$i];
			}
			$data['ref'] = array();
			for($i=0 ; $i<count($fields[$zone.'30'][0]['s']) ; $i++){
				$data['ref'][] = $fields[$zone.'30'][0]['s'][$i];
			}
			$data['subdiv'] = array();
			for($i=0 ; $i<count($fields[$zone.'30'][0]['j']) ; $i++){
				$data['subdiv'][] = $fields[$zone.'30'][0]['j'][$i];
			}
			$data['comment'] = "";
			for($i=0 ; $i<count($fields['300']) ; $i++){
				for($j=0; $j<count($fields['300'][$i]['a']) ; $j++){
					if ($data ['comment'] != "")
						$data ['comment'] .= "\n";
					$data['comment'] .= $fields['300'][$i]['a'][$j];
				}
			}
		}else{
			$data['name'] = $fields['a'][0];
			$data['tonalite']= $fields['u'][0];
			$data['date']= $fields['k'][0];
			$data['distrib'] = array();
			for($i=0 ; $i<count($fields['r']) ; $i++){
				$data['distrib'][] = $fields['r'][$i];
			}
			$data['ref'] = array();
			for($i=0 ; $i<count($fields['s']) ; $i++){
				$data['ref'][] = $fields['s'][$i];
			}	
			$data['subdiv'] = array();
			for($i=0 ; $i<count($fields['j']) ; $i++){
				$data['subdiv'][] = $fields['j'][$i];
			}	
		}
		$data['type_authority'] = "uniform_title";
		return $data;
	}
	
	// ---------------------------------------------------------------
	//		majIndexTu() : mise à jour du champ tu_index d'un titre uniforme
	// ---------------------------------------------------------------
	public static function update_index_tu($tu_id){
		global $include_path;
		
		if($tu_id){
			$requete = "UPDATE titres_uniformes SET index_tu=";
						
			$oeuvre = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $tu_id);
			
			$index = $oeuvre->name." ".$oeuvre->tonalite." ".$oeuvre->subject." ".$oeuvre->place." ".$oeuvre->history." ";
			$index.= $oeuvre->date." ".$oeuvre->context." ".$oeuvre->equinox." ".$oeuvre->coordinates." ";
			
			if (!empty($oeuvre->responsabilites)) {
				$as = array_keys ($oeuvre->responsabilites["responsabilites"], "0" ) ;
				for ($i = 0 ; $i < count($as) ; $i++) {
					$indice = $as[$i] ;
					$auteur_0 = $oeuvre->responsabilites["auteurs"][$indice] ;
					$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $auteur_0["id"]);
					$index .= $auteur->name . " " . $auteur->rejete . " ";
				}
			}
				
			
			$req = "SELECT distrib_name FROM tu_distrib WHERE distrib_num_tu='$tu_id' ";
			$res = pmb_mysql_query($req);
			if($distrib=pmb_mysql_fetch_object($res)){
				$index.= $distrib->distrib_name." ";
			}
			$req = "SELECT ref_name FROM tu_ref WHERE ref_num_tu='$tu_id' ";
			$res = pmb_mysql_query($req);
			if($ref=pmb_mysql_fetch_object($res)){
				$index.= $ref->ref_name." ";
			}
			
			$requete .= "' ".addslashes(strip_empty_chars($index))." ' WHERE tu_id=".$tu_id;
			pmb_mysql_query($requete);
		}
		return ;		
	}
	
	// ---------------------------------------------------------------
	// do_old_isbd() : génération de l'isbd du titre uniforme (AFNOR Z 44-061 de 1986)
	// ---------------------------------------------------------------
	public function do_old_isbd() {
		$this->tu_isbd="";
		if (! $this->id)
			return;
		
		if(!empty($this->responsabilites)) {
			$as = array_keys ($this->responsabilites["responsabilites"], "0" ) ;
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_0 = $this->responsabilites["auteurs"][$indice] ;
				$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $auteur_0["id"]);
				if ($i > 0)
					$this->tu_isbd .= " / ";
				$this->tu_isbd.= $auteur->display.". ";			
			}
		}
		if ($i)
			$this->tu_isbd .= ". ";
		
		if($this->name){
			$this->tu_isbd.= $this->name;
		}
				
		return $this->tu_isbd;
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
    		$responsabilites = $this->get_responsabilites();
    		if (!empty($responsabilites)) {
        		$as = array_keys($this->responsabilites["responsabilites"], "0");
        		for($i = 0; $i < count($as); $i++) {
        			$indice = $as[$i];
        			$auteur_0 = $this->responsabilites["auteurs"][$indice];
        			$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, ['num_object' => $auteur_0["id"],'type_object'=> AUT_TABLE_AUTHORS]);//new authority(0, $auteur_0["id"], AUT_TABLE_AUTHORS);
        			$isbd_simple .= " / ";
        			$isbd_simple .= $authority->get_isbd();
        			if($this->responsabilites['auteurs'][$i]['fonction'] && array_key_exists($this->responsabilites['auteurs'][$i]['fonction'], $fonction_auteur)){
        				$isbd_simple.= ', '.$fonction_auteur[$this->responsabilites['auteurs'][$i]['fonction']];
        			}
        			if(is_object($this->responsabilites['auteurs'][$i]['qualif'])){
        				$isbd_simple.= ' ('.$this->responsabilites['auteurs'][$i]['qualif']->get_label().')';
        			}
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
	
	public static function get_data_bnf_uri($isbn) {
		$isbn13=formatISBN($isbn,13);
		$isbn10=formatISBN($isbn,10);
		
		//Récupération de l'URI data.bnf.fr à partir d'un isbn
		// definition des endpoints databnf et dbpedia
		$configbnf = array(
				'remote_store_endpoint' => 'http://data.bnf.fr/sparql'
		);
		$storebnf = ARC2::getRemoteStore($configbnf);
	
		$sparql = "
		PREFIX bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/>
		prefix rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>
		
		SELECT ?oeuvre WHERE {
			?manifestation bnf-onto:isbn \"".$isbn13."\".
		  	?manifestation rdarelationships:workManifested ?oeuvre.
		}";
		$ret=false;
		$rows = $storebnf->query($sparql, 'rows');
		// On vérifie qu'il n'y a pas d'erreur sinon on stoppe le programme et on renvoi une chaine vide
		$err = $storebnf->getErrors();
		if (!$err) {
			//print $rows[0]['oeuvre']; On évite d'afficher des messages incompréhensibles pour l'utilisateur ;-)
			if (!$rows[0]['oeuvre']) {
				$sparql = "
					PREFIX bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/>
					prefix rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>
							
					SELECT ?oeuvre WHERE {
						?manifestation bnf-onto:isbn \"".$isbn10."\".
					  	?manifestation rdarelationships:workManifested ?oeuvre.
					}";
				$rows = $storebnf->query($sparql, 'rows');
				$err = $storebnf->getErrors();
				if (!$err) {
					if ($rows[0]['oeuvre']) {
						$ret=$rows[0]['oeuvre'];
					}
				}
			} else
				$ret = $rows [0] ['oeuvre'];
		}
		return $ret;
	}
	
	public static function get_manifestation_list($uri) {
		$isbns=array();
		$configbnf = array(
				'remote_store_endpoint' => 'http://data.bnf.fr/sparql'
		);
		$storebnf = ARC2::getRemoteStore($configbnf);
		
		$sparql = "
		PREFIX bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/>
		prefix rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>
		
		SELECT ?isbn WHERE {
			?manifestation bnf-onto:isbn ?isbn.
		  	?manifestation rdarelationships:workManifested <$uri>.
		}";
			
		$ret=false;
		$rows = $storebnf->query($sparql, 'rows');
		// On vérifie qu'il n'y a pas d'erreur sinon on stoppe le programme et on renvoi une chaine vide
		$err = $storebnf->getErrors();
		if (!$err) {
			for($i=0;$i<count($rows); $i++) {
				$isbns[]=formatISBN($rows[$i]['isbn'],13);
				$isbns[]=formatISBN($rows[$i]['isbn'],10);
			}
		}
		return $isbns;
	}
	
	public static function delete_enrichment($id) {
		// to Do
	}
	
	public static function tu_enrichment($id) {
		$requete="select tu_databnf_uri from titres_uniformes where tu_id=$id";
		$resultat=pmb_mysql_query($requete);
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			$uri=pmb_mysql_result($resultat,0,0);
		} else
			$uri = "";
		
		if ($uri) {
			$configbnf = array(
					'remote_store_endpoint' => 'http://data.bnf.fr/sparql'
			);
			$storebnf = ARC2::getRemoteStore($configbnf);
			
			$sparql= "
			PREFIX dc: <http://purl.org/dc/terms/>
			PREFIX rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>
													
			SELECT min(?gallica) as ?gallica2 ?title ?date ?editeur WHERE {
			  ?manifestation rdarelationships:workManifested <".$uri.">.
			  ?manifestation rdarelationships:electronicReproduction ?gallica.
			  ?manifestation dc:title ?title.
			  OPTIONAL { ?manifestation dc:date ?date.}
			  OPTIONAL { ?manifestation dc:publisher ?editeur.}
			} group by ?title ?date ?editeur";
			$ret=false;
			$rows = $storebnf->query($sparql, 'rows');
			// On vérifie qu'il n'y a pas d'erreur sinon on stoppe le programme et on renvoi une chaine vide
			$err = $storebnf->getErrors();
			$tr=array();
			if (!$err) {
				foreach($rows as $row) {
					$t=array();
					$t["uri_gallica"]=$row["gallica2"];
					$t["titre"]=$row["title"];
					$t["date"]=$row["date"];
					$t["editeur"]=$row["editeur"];
					$t["uri_gallica"]=$row["gallica2"];
					$tr[]=$t;
				} 
			}
			$tr=encoding_normalize::charset_normalize($tr,"utf-8");
			//Stockage du tableau
			$requete="update titres_uniformes set tu_enrichment='".addslashes(serialize($tr))."', tu_enrichment_last_update=now() where tu_id=$id";
			// tu_enrichment n'existe pas ...
			// pmb_mysql_query($requete);
		}
	}
	
	public function get_selector($type, $name, $selected) {
		global $charset,$msg;
		
		$optgroup_list=array();
		$selector = '<select id="'.$name.'" name="'.$name.'" data-form-name='.substr($name,0,-1).'>';
		$oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
		foreach($oeuvre_link->table as $group=>$types) {
			$options = '';
			foreach($types as $code => $libelle){
				if ($oeuvre_link->attributes[$code]['GROUP'] == $type) {
					if(!($code == $selected))
						$options .= "<option value='".$code."'>".$libelle."</option>";
					else{
						$options .= "<option value='".$code."' selected='selected'>".$libelle."</option>";
					}
				}
			}			
			if($options) $optgroup_list[$group]=$options;
		}
		if(count($optgroup_list)>1){
			foreach ($optgroup_list as $group=>$options) {
				$selector .= '<optgroup label="'.htmlentities($group,ENT_QUOTES,$charset).'">'.$options.'</optgroup>';
			}
		}elseif(count($optgroup_list)){
			foreach ($optgroup_list as $group=>$options) {
				$selector.= $optgroup_list[$group];	
			}						
		}else{
			$selector.= "<option value=''>".$msg['authority_marc_list_empty_filter']."</option>";
		}
		$selector.= '</select>';
		
		return $selector;
	}
	
	public function get_header() {
		return $this->get_isbd();
	}
	
	public function get_oeuvre_expressions_datas(){
		if (!isset($this->oeuvre_expressions) || !$this->oeuvre_expressions) {
			$this->get_oeuvre_links();
		}
		return $this->oeuvre_expressions;
	}

	public function get_oeuvre_expressions_from_datas(){
		if (!isset($this->oeuvre_expressions_from) || !$this->oeuvre_expressions_from) {
			$this->get_oeuvre_links();
		}
		return $this->oeuvre_expressions_from;
	}
		
	public function get_oeuvre_others_links_datas(){
		if (!isset($this->other_links) || !$this->other_links) {
			$this->get_oeuvre_links();
		}
		return $this->other_links;
	}
	
	public function get_oeuvre_expressions_list_ui() {
		//if (!$this->oeuvre_expressions_list_ui) {
			$contents = array();
			$nb_results = 0;
			if(count($this->get_oeuvre_expressions_datas())){
				foreach ($this->get_oeuvre_expressions_datas() as $expression) {
				    $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $expression['to_id'], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
					$contents[] = $authority->get_id();
					$nb_results++;
				}
				$this->oeuvre_expressions_list_ui = new elements_authorities_list_ui($contents, $nb_results, false);
			}
		//}
		return $this->oeuvre_expressions_list_ui;
	}

	public function get_oeuvre_expressions_from_list_ui() {
		if (!$this->oeuvre_expressions_from_list_ui) {
			$contents = array();
			$nb_results = 0;
			foreach ($this->get_oeuvre_expressions_from_datas() as $expression) {
			    $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $expression['to_id'], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
				$contents[] = $authority->get_id();
				$nb_results++;
			}
			$this->oeuvre_expressions_from_list_ui = new elements_authorities_list_ui($contents, $nb_results, false);
		}
		return $this->oeuvre_expressions_from_list_ui;
	}
	
	public function get_sorted_responsabilities() {
	    $this->get_responsabilites();
		return $this->sorted_responsabilities;
	}

	public function get_cp_error_message(){
		return $this->cp_error_message;
	}
	
	public function get_tu_notices() {
		global $msg;
				
		if(isset($this->tu_notices)){
			return $this->tu_notices;
		}
		
		$this->tu_notices = array();
		$query = "	SELECT notice_id, serie_name, tnvol, tit1, code, ntu_titre, ntu_date, ntu_sous_vedette, ntu_langue, ntu_version, ntu_mention 
					FROM notices_titres_uniformes 
					JOIN notices ON notice_id = ntu_num_notice 
					LEFT JOIN series on serie_id = tparent_id 
					WHERE ntu_num_tu = '".$this->id."'
					ORDER BY ntu_ordre";			
		$result = pmb_mysql_query($query);
		$result_tu_notices = array();
		if ($result && pmb_mysql_num_rows($result)) {			
			while ($row = pmb_mysql_fetch_object($result)) {
				$result_tu_notices[] = $row;
			}
		}		
		
		if(count($result_tu_notices)) {
			$i=0;
			do {
				//$this->tu_notices[$i]["id"]= ($result_tu_notices[$i]->notice_id ? $result_tu_notices[$i]->notice_id : '') ;
				$this->tu_notices[$i]["id"]= $result_tu_notices[$i]->notice_id;
				//$this->tu_notices[$i]["label"]= ($result_tu_notices[$i]->serie_name? $result_tu_notices[$i]->serie_name." ":"").($result_tu_notices[$i]->tnvol ? $result_tu_notices[$i]->tnvol." ":"").($result_tu_notices[$i]->tit1 ? $result_tu_notices[$i]->tit1 : "").($result_tu_notices[$i]->code ?" (".$result_tu_notices[$i]->code.")":"");
				$this->tu_notices[$i]["label"]= notice::get_notice_title($result_tu_notices[$i]->notice_id);
				$j=0;
				$this->tu_notices[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_titre_section"];
				$this->tu_notices[$i]["objets"][$j]["name"]="ntu_titre";
				$this->tu_notices[$i]["objets"][$j]["class"]="saisie-80em";
				$this->tu_notices[$i]["objets"][$j]["value"]=$result_tu_notices[$i]->ntu_titre;
				$j++;
				$this->tu_notices[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_date"];
				$this->tu_notices[$i]["objets"][$j]["name"]="ntu_date";
				$this->tu_notices[$i]["objets"][$j]["class"]="saisie-80em";
				$this->tu_notices[$i]["objets"][$j]["value"]=$result_tu_notices[$i]->ntu_date;
				$j++;
				$this->tu_notices[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_sous_vedette"];
				$this->tu_notices[$i]["objets"][$j]["name"]="ntu_sous_vedette";
				$this->tu_notices[$i]["objets"][$j]["class"]="saisie-80em";
				$this->tu_notices[$i]["objets"][$j]["value"]=$result_tu_notices[$i]->ntu_sous_vedette;
				$j++;
				$this->tu_notices[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_langue"];
				$this->tu_notices[$i]["objets"][$j]["name"]="ntu_langue";
				$this->tu_notices[$i]["objets"][$j]["class"]="saisie-80em";
				$this->tu_notices[$i]["objets"][$j]["value"]=$result_tu_notices[$i]->ntu_langue;
				$j++;
				$this->tu_notices[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_version"];
				$this->tu_notices[$i]["objets"][$j]["name"]="ntu_version";
				$this->tu_notices[$i]["objets"][$j]["class"]="saisie-80em";
				$this->tu_notices[$i]["objets"][$j]["value"]=$result_tu_notices[$i]->ntu_version;
				$j++;
				$this->tu_notices[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_mention"];
				$this->tu_notices[$i]["objets"][$j]["name"]="ntu_mention";
				$this->tu_notices[$i]["objets"][$j]["class"]="saisie-80em";
				$this->tu_notices[$i]["objets"][$j]["value"]=$result_tu_notices[$i]->ntu_mention;
			} while	(++$i<count($result_tu_notices));
		}
		return $this->tu_notices;	
	}	
	
	public function update_tu_notices($value) {
		$this->delete_tu_notices();
		if(!is_array($value)) {
			return;
		}

		for($i = 0 ; $i < count($value); $i++) {
			if ($value[$i]) {
				$rqt = "INSERT INTO notices_titres_uniformes (ntu_num_tu, ntu_num_notice, ntu_titre, ntu_date, ntu_sous_vedette, ntu_langue, ntu_version, ntu_mention, ntu_ordre) 
						VALUES ('".$this->id."', '".$value[$i]['ntu_num_notice']."', '".$value[$i]['ntu_titre']."', '".$value[$i]['ntu_date']."', '".$value[$i]['ntu_sous_vedette']."', '".$value[$i]['ntu_langue']."', '".$value[$i]['ntu_version']."', '".$value[$i]['ntu_mention']."','".$i."')";
				pmb_mysql_query($rqt);
			}
		}
	}
	
	public function delete_tu_notices($notice_id = 0){
		if(!$this->id) {
			return;
		}
		
		$rqt_del = "DELETE FROM notices_titres_uniformes WHERE ntu_num_tu = ".$this->id ;
		
		if($notice_id) {
			$rqt_del .= " AND ntu_num_notice = '".$notice_id."'";
		}
		pmb_mysql_query ($rqt_del);
	}
		
	public function get_gestion_link(){
		return './autorites.php?categ=see&sub=titre_uniforme&id='.$this->id;
	}
	
	public static function get_format_data_structure($antiloop = false) {
		global $msg;
		
		$main_fields = array();
		$main_fields[] = array(
				'var' => "type",
				'desc' => $msg['aut_oeuvre_form_oeuvre_type']
		);
		$main_fields[] = array(
				'var' => "nature",
				'desc' => $msg['aut_oeuvre_form_oeuvre_nature']
		);
		$main_fields[] = array(
				'var' => "name",
				'desc' => $msg['aut_titre_uniforme_form_nom']
		);
		if (!$antiloop) {
			$linked_oeuvre_fields = array_merge(
					array(
						array(
							'var' => "link_type",
							'desc' => "Code du type de lien"
						),
						array(
								'var' => "link_type_name",
								'desc' => "Nom du type de lien"
						)
					),
					titre_uniforme::get_format_data_structure(true)
			);
			$main_fields[] = array(
					'var' => "expressions",
					'desc' => $msg['aut_oeuvre_form_oeuvre_expression'],
					'children' => authority::prefix_var_tree($linked_oeuvre_fields,"expressions[i]")
			);
			$main_fields[] = array(
					'var' => "expressions_from",
					'desc' => $msg['aut_oeuvre_form_oeuvre_expression_from'],
					'children' => authority::prefix_var_tree($linked_oeuvre_fields,"expressions_from[i]")
			);
			$main_fields[] = array(
					'var' => "other_links",
					'desc' => $msg['aut_oeuvre_form_other_link'],
					'children' => authority::prefix_var_tree($linked_oeuvre_fields,"other_links[i]")
			);
		}
		$main_fields[] = array(
				'var' => "events",
				'desc' => $msg['aut_oeuvre_form_oeuvre_event'],
				'children' => authority::prefix_var_tree(array(
						array(
								'var' => "id",
								'desc' => ""
						),
						array(
								'var' => "isbd",
								'desc' => ""
						)
				),"events[i]")
		
		);
		$main_fields[] = array(
				'var' => "authors",
				'desc' => $msg['tu_authors_list'],
				'children' => authority::prefix_var_tree(auteur::get_format_data_structure(),"authors[i]")
		);
		$main_fields[] = array(
				'var' => "performers",
				'desc' => $msg['tu_interpreter_list'],
				'children' => authority::prefix_var_tree(auteur::get_format_data_structure(),"performers[i]")
		);
		
		$main_fields[] = array(
				'var' => "form",
				'desc' => $msg['aut_oeuvre_form_forme']
		);
		$main_fields[] = array(
				'var' => "date",
				'desc' => $msg['aut_oeuvre_form_date']
		);
		$main_fields[] = array(
				'var' => "place",
				'desc' => $msg['aut_oeuvre_form_lieu']
		);
		$main_fields[] = array(
				'var' => "subject",
				'desc' => $msg['aut_oeuvre_form_sujet']
		);
		$main_fields[] = array(
				'var' => "intended_termination",
				'desc' => $msg['aut_oeuvre_form_completude']
		);
		$main_fields[] = array(
				'var' => "intended_audience",
				'desc' => $msg['aut_oeuvre_form_public']
		);
		$main_fields[] = array(
				'var' => "history",
				'desc' => $msg['aut_oeuvre_form_histoire']
		);
		$main_fields[] = array(
				'var' => "context",
				'desc' => $msg['aut_oeuvre_form_contexte']
		);
		$main_fields[] = array(
				'var' => "distribution[i]",
				'desc' => $msg['aut_titre_uniforme_form_distribution'],
				'children' => authority::prefix_var_tree(array(array('var' => "label", 'desc' => $msg['aut_titre_uniforme_form_distribution'])),"distribution[i]")
		);
		$main_fields[] = array(
				'var' => "ref_numerique[i]",
				'desc' => $msg['aut_titre_uniforme_form_ref_numerique'],
				'children' => authority::prefix_var_tree(array(array('var' => "label", 'desc' => $msg['aut_titre_uniforme_form_ref_numerique'])),"ref_numerique[i]")
		);
		$main_fields[] = array(
				'var' => "tonalite",
				'desc' => $msg['aut_titre_uniforme_form_tonalite']
		);
		$main_fields[] = array(
				'var' => "tonalite_marclist",
				'desc' => $msg['aut_titre_uniforme_form_tonalite_list']
		);
		$main_fields[] = array(
				'var' => "coordinates",
				'desc' => $msg['aut_oeuvre_form_coordonnees']
		);
		$main_fields[] = array(
				'var' => "equinox",
				'desc' => $msg['aut_oeuvre_form_equinoxe']
		);
		$main_fields[] = array(
				'var' => "subdivision_shape[i]",
				'desc' => $msg['aut_titre_uniforme_form_subdivision_forme'],
				'children' => authority::prefix_var_tree(array(array('var' => "label", 'desc' => $msg['aut_titre_uniforme_form_subdivision_forme'])),"subdivision_shape[i]")
		);
		$main_fields[] = array(
				'var' => "characteristic",
				'desc' => $msg['aut_oeuvre_form_caracteristique']
		);
		$main_fields[] = array(
				'var' => "comment",
				'desc' => $msg['aut_titre_uniforme_commentaire']
		);
		$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => 0, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}
	
	public function format_datas($antiloop = false){
		
		$expressions_datas = array();
		$expressions_from_datas = array();
		$others_links_datas = array();
		if(!$antiloop) {
			foreach ($this->get_oeuvre_expressions_datas() as $expression) {
				$titre_uniforme = new titre_uniforme($expression['to_id']);
				$expressions_datas[] = $titre_uniforme->format_datas(true); 
			}
			foreach ($this->get_oeuvre_expressions_from_datas() as $expression_from) {
				$titre_uniforme = new titre_uniforme($expression_from['to_id']);
				$expressions_from_datas[] = $titre_uniforme->format_datas(true);
			}
			foreach ($this->get_oeuvre_others_links_datas() as $other_link) {
				$titre_uniforme = new titre_uniforme($other_link['to_id']);
				$others_links_datas[] = $titre_uniforme->format_datas(true);
			}
		}
		$authors = array();
		foreach ($this->sorted_responsabilities['authors'] as $id=>$author) {
		    $auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS,$id);
			$authors[] = $auteur->format_datas(true);
		}
		$performers = array();
		foreach ($this->sorted_responsabilities['performers'] as $id=>$performer) {
			$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $id);
			$performers[] = $auteur->format_datas(true);
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
				'form' => $this->tu_form,
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
		$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $this->id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public static function set_deleted_index($deleted_index) {
		static::$deleted_index = $deleted_index;
	}
	
	public static function set_controller($controller) {
		static::$controller = $controller;
	}
	
	protected static function format_url($url='') {
		global $base_path;
		
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_url_base().$url;
		} else {
			return $base_path.'/autorites.php?categ=titres_uniformes'.$url;
		}
	}
	
	protected static function format_back_url() {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_back_url();
		} else {
			return "history.go(-1)";
		}
	}
	
	public static function format_delete_url($url='', $forcing = false) {
		global $base_path;
			
		if ($forcing && $url) {
			return static::format_url("&sub=delete".$url);
		}
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_delete_url();
		} else {
			return static::format_url("&sub=delete".$url);
		}
	}
	
	public function get_concepts(){
		$index_concept = new index_concept($this->id, TYPE_TITRE_UNIFORME);
		return $index_concept->get_concepts();
	}
	
	public function get_linked_records_id(){
		$linked_records_id = array();
		$query = "select distinct ntu_num_notice from notices_titres_uniformes where ntu_num_tu = ".$this->id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$linked_records_id[] = $row['ntu_num_notice'];
			}
		}
		return $linked_records_id;
	}
	
	public function get_linked_works_id($type) {
		$this->get_oeuvre_links();
		$works_id = array();
		if (is_array($this->{$type})) {
			foreach ($this->{$type} as $work) {
				$works_id[] = array(
						'id' => $work['to_id'],
						'link_type' => array(
								'id' =>$work['type'],
								'label' =>$work['type_label'],
						),
				);
			}
		}
		return $works_id;
	}
	
	public function get_linked_events_id() {
		$events_id = array();
		$events = $this->get_oeuvre_events();
		if (is_array($events)) {
			foreach ($events as $event) {
				$events_id[] = $event['id'];
			}
		}
		return $events_id;
	}
	
	public function get_linked_responsabilities_id ($type) {
		$responsibilities_id =  array();
		$this->get_authors();
		if (isset($this->sorted_responsabilities[$type]) && is_array($this->sorted_responsabilities[$type])) {
			foreach ($this->sorted_responsabilities[$type] as $id => $responsiblity) {
				$responsibilities_id[] = $id;
			}
		}
		return $responsibilities_id;
	}
	
	protected function warning_already_exist($error_title, $error_message, $values=array())  {
	    global $msg;
		global $max_aut0, $max_aut1;
	    
	    $authority = new authority(0, $this->id, AUT_TABLE_TITRES_UNIFORMES);
	    $display = $authority->get_display_authority_already_exist($error_title, $error_message, $values);
	    $display = str_replace("!!action!!", static::format_url('&sub=update&id='.$this->id.'&forcing=1'), $display);
	    $display = str_replace("!!forcing_button!!", $authority->get_display_forcing_button($msg[287]), $display);
	    $hidden_specific_values = $authority->put_global_in_hidden_field("saisie_titre_uniforme_role_composed");
	    $hidden_specific_values .= $authority->put_global_in_hidden_field("saisie_titre_uniforme_role_autre_composed");
	    $hidden_specific_values .= $authority->put_global_in_hidden_field("tab_concept_order");
	    $hidden_specific_values .= $authority->put_global_in_hidden_field("concept");
	    
	    $hidden_specific_values .= $authority->put_global_in_hidden_field("max_aut0");
	    $hidden_specific_values .= $authority->put_global_in_hidden_field("max_aut1");
	    $hidden_specific_values .= $authority->put_global_in_hidden_field("is_duplication");
	    
	    for ($i=0; $i<$max_aut0; $i++) {
	    	$hidden_specific_values .= $authority->put_global_in_hidden_field("f_aut0_id".$i);
	    	$hidden_specific_values .= $authority->put_global_in_hidden_field("f_f0_code".$i);
	    }
	    
	    for ($i=0; $i<$max_aut1; $i++) {
	    	$hidden_specific_values .= $authority->put_global_in_hidden_field("f_aut1_id".$i);
	    	$hidden_specific_values .= $authority->put_global_in_hidden_field("f_f1_code".$i);
	    }
	    $display = str_replace('!!hidden_specific_values!!', $hidden_specific_values, $display);
	    return $display;
	}
	
	public function __get($attribute)
	{
	    if (method_exists($this, "get_".$attribute)) {
	        return call_user_func_array(array($this, "get_".$attribute), array());
	    } else if (method_exists($this, $attribute)) {
	        return call_user_func_array(array($this, $attribute), array());
	    } else if (is_object($this) && isset($this->{$attribute})) {
	        return $this->{$attribute};
	    }
	    return null;
	}
	
	public function get_responsabilites() 
	{
	    if (!isset($this->responsabilites)) {	        
    	    $this->responsabilites = array();
    	    $this->responsabilites["responsabilites"] = array();
    	    $this->responsabilites = $this->get_authors();
	    }
	    return $this->responsabilites;
	}
	
	public function get_display() 
	{
	    if (!isset($this->display)) {	        
    	    $this->display = '';
    	    $this->display = $this->name;
    	    if ($this->date) {
    	        $this->display .=" (".$this->date.")";
    	    }
    	    
    	    /*
    	    if ($this->num_author) {
    	        $tu_auteur = new auteur($this->num_author);
    	        $this->display .=" / ".$tu_auteur->rejete." ".$tu_auteur->name;
    	    }
    	    */
    	    
    	    $responsabilites = $this->get_responsabilites();
    	    if (!empty($responsabilites)) {
        	    $keys = array_keys($responsabilites["responsabilites"], "0");
        	    $index = count($keys);
        	    if ($index) {
        	        for ($i = 0 ; $i < $index; $i++) {
        	            $indice = $keys[$i];
        	            $auteur = $this->responsabilites["auteurs"][$indice] ;
        	            $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $auteur["id"]);
        	            if ($i > 0) {
        	                $this->display .= " / "; // entre auteurs
        	            }
        	            $this->display .= $authority->rejete." ".$authority->name;
        	        }
        	    }
    	    }
	    }
	    
	    return $this->display;
	}
	
	public function get_libelle() 
	{
	    if (!isset($this->libelle)) {
	        $this->libelle = "";
	        
	        $separator = "; ";
	        $libelle = "";
	        $libelle = $this->name;
	        
	        if ($this->tonalite) {	            
	            $libelle .= $separator.$this->tonalite;
	        }
	        
	        $index = count($this->distrib);
	        for ($i = 0; $i < $index; $i++) {
	            $libelle .= $separator.$this->distrib[$i]['label'];
	        }
	        
	        $index = count($this->ref);
	        for ($i = 0; $i < $index; $i++) {
	            $libelle .= $separator.$this->ref[$i]['label'];
	        }
	        
	        $index = count($this->subdiv);
	        for ($i = 0; $i < $index; $i++) {
	            $libelle .= $separator.$this->subdiv[$i]['label'];
	        }
	        
	        /*
            if ($this->num_author) {
                $tu_auteur = new auteur($this->num_author);
                $libelle .= $separator.$tu_auteur->display;
            }
            */
	        
	        $responsabilites = $this->get_responsabilites();
	        if (!empty($responsabilites)) {
    	        $keys = array_keys($responsabilites["responsabilites"], "0");
    	        $index = count($keys);
    	        if ($index) {
    	            for ($i = 0 ; $i < $index; $i++) {
    	                $indice = $keys[$i];
    	                $auteur = $this->responsabilites["auteurs"][$indice] ;
    	                $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $auteur["id"]);
    	                $libelle .= $separator.$authority->display;
    	            }
    	        }
	        }
	        
	        $this->libelle = $libelle;
	    }
	    
	    return $this->libelle;
	}
	
	public function get_isbd_without_responsabilites() {
	    return $this->get_isbd_simple(false);
	}
	
	public function get_oeuvre_expressions_grouped_list_ui() {
	    $contents = array();
	    if(count($this->get_oeuvre_expressions_datas())){
	        foreach ($this->get_oeuvre_expressions_datas() as $expression) {
	            if (!isset($contents[$expression["type"]])) {
	                $contents[$expression["type"]] = [];
	                $contents[$expression["type"]] = [
	                    "nb_result" => 0,
	                    "label" => ucfirst($expression["type_label"]),
	                    "content" => [],
	                    "display" => ""
	                ];
	            }
	            $contents[$expression["type"]]["nb_result"]++;
	            
	            $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $expression['to_id'], 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
	            $contents[$expression["type"]]["content"][] = $authority->get_id();
	        }
	        
	        foreach ($contents as $key => $value) {
	            $contents[$key]["display"] = new elements_authorities_list_ui($contents[$key]["content"], $contents[$key]["nb_result"], false);
	        }
	        $this->oeuvre_expressions_grouped_list_ui = $contents;
	    }
	    return $this->oeuvre_expressions_grouped_list_ui;
	}
} // class titre uniforme


