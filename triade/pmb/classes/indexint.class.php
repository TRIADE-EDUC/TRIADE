<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.class.php,v 1.114.4.2 2025/04/24 14:45:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Entities\ArkEntityPmb;
// définition de la classe de gestion des 'indexations internes'
if ( ! defined( 'INDEXINT_CLASS' ) ) {
  define( 'INDEXINT_CLASS', 1 );

  global $class_path;

require_once($class_path."/notice.class.php");
require_once("$class_path/aut_link.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path.'/authorities_statuts.class.php');
require_once($class_path."/indexation_authority.class.php");
require_once($class_path."/authority.class.php");
require_once ($class_path.'/indexations_collection.class.php');
require_once($class_path."/pclassement.class.php");
require_once ($class_path.'/indexation_stack.class.php');
require_once ($class_path.'/interface/entity/interface_entity_indexint_form.class.php');

class indexint {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $indexint_id=0; 	// MySQL indexint_id in table 'indexint'
	public	$name=''; 		// nom de l'indexation
	public	$comment='';	// commentaire
	public	$display='';	// name + comment
	public $isbd_entry_lien_gestion ; // lien sur le nom vers la gestion
	public $id_pclass='1';
	public $name_pclass='';
	public $num_statut = 1;
	public $cp_error_message = '';
	protected static $controller;

	// ---------------------------------------------------------------
	//		indexint($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0,$id_pclass=1) {
		$this->indexint_id = intval($id);
		$this->init_id_pclass($id_pclass);
		$this->getData();
	}

	protected function init_id_pclass($id_pclass=1) {
		$this->id_pclass=$id_pclass;
		if(!pclassement::is_visible($id_pclass)) {
			$this->id_pclass = pclassement::get_default_id($id_pclass);
		}
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos
	// ---------------------------------------------------------------
	public function getData() {
		if($this->indexint_id) {
			$requete = "SELECT indexint_id,indexint_name,indexint_comment, num_pclass, id_pclass,name_pclass FROM indexint,pclassement
			WHERE indexint_id='".$this->indexint_id."' and id_pclass = num_pclass " ;
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);

				$this->indexint_id	= $temp->indexint_id;
				$this->name			= $temp->indexint_name;
				$this->comment		= $temp->indexint_comment;
				$this->id_pclass	= $temp->id_pclass;
				$this->name_pclass	= $temp->name_pclass;
				$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, [ 'num_object' => $this->indexint_id, 'type_object' => AUT_TABLE_INDEXINT]);
				$this->num_statut = $authority->get_num_statut();
				if ($this->comment) $this->display = $this->name." ($this->comment)" ;
					else $this->display = $this->name ;
				// Ajoute un lien sur la fiche autorité si l'utilisateur à accès aux autorités
				if (defined('SESSrights') && (SESSrights & AUTORITES_AUTH)){
				    //$this->isbd_entry_lien_gestion = "<a href='./autorites.php?categ=indexint&sub=indexint_form&id=".$this->indexint_id."&id_pclass=".$this->id_pclass."' class='lien_gestion'>".$this->display."</a>";
				    $this->isbd_entry_lien_gestion = "<a href='./autorites.php?categ=see&sub=indexint&id=".$this->indexint_id."&id_pclass=".$this->id_pclass."' class='lien_gestion'>".$this->display."</a>";
				}else{
				    $this->isbd_entry_lien_gestion = $this->display;
				}
			}
		}
	}

	public function build_header_to_export() {
	    global $msg;

	    $data = array(
	        $msg[67],
	        $msg['menu_pclassement'],
	        $msg[707],
	        $msg[4019],
	    );
	    return $data;
	}

	public function build_data_to_export() {
	    $data = array(
	        $this->name,
	        $this->name_pclass,
	        $this->comment,
	        $this->num_statut,
	    );
	    return $data;
	}

	protected function get_content_form() {
		global $charset, $thesaurus_concepts_active;
		global $indexint_content_form;

		$content_form = $indexint_content_form;

		//Plan de classement
		$element = interface_entity_element::get_instance('el0Child_2', 'indexint_pclassement', 'menu_pclassement');
		$element->add_html_node(pclassement::get_selector('indexint_pclassement', $this->id_pclass));
		$content_form = str_replace('!!element_indexint_pclassement!!', $element->get_display(), $content_form);

		//Nom
		$element = interface_entity_element::get_instance('el0Child_0', 'indexint_nom', 'indexint_nom');
		$element->add_input_node('text', $this->name, ['data-pmb-deb-rech' => '1'])
		->set_class('saisie-50em');
		$content_form = str_replace('!!element_indexint_nom!!', $element->get_display(), $content_form);

		//Commentaire
		$element = interface_entity_element::get_instance('el0Child_1', 'indexint_comment', 'indexint_comment');
		$element->add_textarea_node($this->comment, 62, 6)
		->set_class('saisie-80em')
		->set_attributes(array('wrap' => 'virtual'));
		$content_form = str_replace('!!element_indexint_comment!!', $element->get_display(), $content_form);

		$aut_link= new aut_link(AUT_TABLE_INDEXINT,$this->indexint_id);
		$content_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_indexint') , $content_form);

		$aut_pperso= new aut_pperso("indexint",$this->indexint_id);
		$content_form = str_replace('!!aut_pperso!!',	$aut_pperso->get_form(), $content_form);

		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->indexint_id, TYPE_INDEXINT);
			$content_form = str_replace('!!concept_form!!',	$index_concept->get_form('saisie_indexint'), $content_form);
		}else{
			$content_form = str_replace('!!concept_form!!',	"", $content_form);
		}
		$authority = new authority(0, $this->indexint_id, AUT_TABLE_INDEXINT);
		$content_form = str_replace('!!thumbnail_url_form!!', thumbnail::get_form('authority', $authority->get_thumbnail_url()), $content_form);
		return $content_form;
	}

	public function get_form($duplicate = false) {
		global $msg;
		global $user_input, $nbr_lignes, $page, $exact;

		$interface_form = new interface_entity_indexint_form('saisie_indexint');
		if(isset(static::$controller) && is_object(static::$controller)) {
			$interface_form->set_controller(static::$controller);
		}
		$interface_form->set_enctype('multipart/form-data');
		if($this->indexint_id && !$duplicate) {
			$interface_form->set_label($msg['indexint_update']);
			$interface_form->set_document_title($this->name.($this->comment ? ' : '.$this->comment : '').' - '.$msg['indexint_update']);
		} else {
			$interface_form->set_label($msg['indexint_create']);
			$interface_form->set_document_title($msg['indexint_create']);
		}
		$interface_form->set_object_id($this->indexint_id)
		->set_num_statut($this->num_statut)
		->set_id_pclass($this->id_pclass)
		->set_content_form($this->get_content_form())
		->set_table_name('indexint')
		->set_field_focus('indexint_nom')
		->set_url_base(static::format_url());

		$interface_form->set_page($page)
		->set_nbr_lignes($nbr_lignes)
		->set_user_input($user_input)
		->set_exact($exact);
		return $interface_form->get_display();
	}

	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form($duplicate = false) {
		print $this->get_form($duplicate);
	}

	// ---------------------------------------------------------------
	//		replace_form : affichage du formulaire de remplacement
	// ---------------------------------------------------------------
	public function replace_form() {
		global $indexint_replace_content_form;
		global $msg;
		global $include_path;
		global $charset ;

		if(!$this->indexint_id || !$this->name) {
			require_once("$include_path/user_error.inc.php");
			error_message($msg['indexint_replace'], $msg['indexint_unable'], 1, static::format_url('&sub=&id='));
			return false;
		}

		$notin="$this->indexint_id";
		$liste_remplacantes="";
		$lenremplacee = strlen($this->name)-1 ;
		while ($lenremplacee>0) {
			$recherchee = substr($this->name,0,$lenremplacee) ;

			$requete = "SELECT indexint_id,indexint_name,indexint_comment FROM indexint WHERE num_pclass='".$this->id_pclass."' and indexint_name='".addslashes($recherchee)."' and indexint_id not in (".$notin.") order by indexint_name " ;
			$result = pmb_mysql_query($requete) or die ($requete."<br />".pmb_mysql_error());
			$trouvees = 0 ;
			while ($lue=pmb_mysql_fetch_object($result)) {
				$notin.=",".$lue->indexint_id;
				$liste_remplacantes.="<tr><td><a href='".static::format_url("&sub=replace&id=".$this->indexint_id."&n_indexint_id=".$lue->indexint_id)."'>".htmlentities($lue->indexint_name,ENT_QUOTES, $charset)."</a></td><td>".htmlentities($lue->indexint_comment,ENT_QUOTES, $charset)."</tr>";
				$trouvees=1 ;
			}
			if ($trouvees) $liste_remplacantes.="<tr><td>&nbsp;</td><td>&nbsp;</td></tr>" ;
			$lenremplacee = $lenremplacee-1 ;
		}
		if ($liste_remplacantes) $liste_remplacantes="<table>".$liste_remplacantes."</table>";

		$content_form = $indexint_replace_content_form;
		$content_form = str_replace('!!id!!', $this->indexint_id, $content_form);
		$content_form=str_replace('!!id_pclass!!', $this->id_pclass, $content_form);

		$interface_form = new interface_autorites_replace_form('indexint_replace');
		$interface_form->set_object_id($this->indexint_id)
		->set_id_pclass($this->id_pclass)
		->set_label($msg["159"]." ".$this->name)
		->set_content_form($content_form)
		->set_table_name('indexint')
		->set_field_focus('indexint_libelle')
		->set_url_base(static::format_url());
		print $interface_form->get_display();
		print "<div class='row'>
			".$liste_remplacantes."
		</div>";
	}


	// ---------------------------------------------------------------
	//		delete() : suppression
	// ---------------------------------------------------------------
	public function delete() {
		global $msg;

		if(!$this->indexint_id)
			// impossible d'accéder à cette indexation
			return $msg['indexint_unable'];

		if(($usage=aut_pperso::delete_pperso(AUT_TABLE_INDEXINT, $this->indexint_id,0) )){
			// Cette autorité est utilisée dans des champs perso, impossible de supprimer
			return '<strong>'.$this->display.'</strong><br />'.$msg['autority_delete_error'].'<br /><br />'.$usage['display'];
		}
		// récupération du nombre de notices affectées
		$requete = "SELECT COUNT(1) FROM notices WHERE ";
		$requete .= "indexint=".$this->indexint_id;
		$res = pmb_mysql_query($requete);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);

		if(!$nbr_lignes) {

			// On regarde si l'autorité est utilisée dans des vedettes composées
			$attached_vedettes = vedette_composee::get_vedettes_built_with_element($this->indexint_id, TYPE_INDEXINT);
			if (count($attached_vedettes)) {
				// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
				return '<strong>'.$this->name."</strong><br />".$msg["vedette_dont_del_autority"].'<br/>'.vedette_composee::get_vedettes_display($attached_vedettes);
			}

			// indexation non-utilisé dans les notices : Suppression OK
			// effacement dans la table des indexations internes
			$requete = "DELETE FROM indexint WHERE indexint_id=".$this->indexint_id;
			pmb_mysql_query($requete);
			// liens entre autorités
			$aut_link= new aut_link(AUT_TABLE_INDEXINT,$this->indexint_id);
			$aut_link->delete();

			$aut_pperso= new aut_pperso("indexint",$this->indexint_id);
			$aut_pperso->delete();

			// nettoyage indexation concepts
			$index_concept = new index_concept($this->indexint_id, TYPE_INDEXINT);
			$index_concept->delete();

			// nettoyage indexation
			indexation_authority::delete_all_index($this->indexint_id, "authorities", "id_authority", AUT_TABLE_INDEXINT);

			// effacement de l'identifiant unique d'autorité
			$authority = new authority(0, $this->indexint_id, AUT_TABLE_INDEXINT);
			$authority->delete();

			audit::delete_audit(AUDIT_INDEXINT,$this->indexint_id);
			return false;
		} else {
			// Cette indexation est utilisée dans des notices, impossible de la supprimer
			return '<strong>'.$this->name."</strong><br />{$msg['indexint_used']}";
		}
	}

	// ---------------------------------------------------------------
	//		replace($by) : remplacement
	// ---------------------------------------------------------------
	public function replace($by,$link_save) {
		global $msg;
		global $pmb_ark_activate;

		if(!$by) {
			// pas de valeur de remplacement !!!
			return "serious error occured, please contact admin...";
		}
		if (($this->indexint_id == $by) || (!$this->indexint_id))  {
			// impossible de remplacer une autorité par elle-même
			return $msg['indexint_self'];
		}

		$aut_link= new aut_link(AUT_TABLE_INDEXINT,$this->indexint_id);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_INDEXINT,$by);
		}
		$aut_link->delete();

		vedette_composee::replace(TYPE_INDEXINT, $this->indexint_id, $by);

		// a) remplacement dans les notices
		$requete = "UPDATE notices SET indexint=$by WHERE indexint='".$this->indexint_id."' ";
		pmb_mysql_query($requete);

		// b) suppression de l'indexation à remplacer
		$requete = "DELETE FROM indexint WHERE indexint_id=".$this->indexint_id;
		pmb_mysql_query($requete);

		//Remplacement dans les champs persos sélecteur d'autorité
		aut_pperso::replace_pperso(AUT_TABLE_INDEXINT, $this->indexint_id, $by);

		audit::delete_audit(AUDIT_INDEXINT,$this->indexint_id);

		// nettoyage indexation
		indexation_authority::delete_all_index($this->indexint_id, "authorities", "id_authority", AUT_TABLE_INDEXINT);
		if ($pmb_ark_activate) {
		    $idReplaced = authority::get_authority_id_from_entity($this->indexint_id, AUT_TABLE_INDEXINT);
		    $idReplacing = authority::get_authority_id_from_entity($by, AUT_TABLE_INDEXINT);
		    if ($idReplaced && $idReplacing) {
		        $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplaced);
		        $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplacing);
		        $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
		    }
		}
		// effacement de l'identifiant unique d'autorité
		$authority = new authority(0, $this->indexint_id, AUT_TABLE_INDEXINT);
		$authority->delete();

		indexint::update_index($by);

		return FALSE;
	}

	// ---------------------------------------------------------------
	//		update($value) : mise à jour de l'indexation
	// ---------------------------------------------------------------
	public function update($nom, $comment,$id_pclass=0, $statut=1, $thumbnail_url='') {
		global $msg;
		global $include_path;
		global $thesaurus_classement_mode_pmb,$thesaurus_classement_defaut;
		global $thesaurus_concepts_active;

		if(!$nom)
			return false;

		// nettoyage de la chaîne en entrée
		$nom = clean_string($nom);
		if ($thesaurus_classement_mode_pmb == 0 || $id_pclass==0) {
			$id_pclass=$thesaurus_classement_defaut;
		}

		$requete = "SET indexint_name='$nom', ";
		$requete .= "indexint_comment='$comment', ";
		$requete .= "num_pclass='$id_pclass', ";
		$requete .= "index_indexint=' ".strip_empty_words($nom." ".$comment)." '";

		if($this->indexint_id) {
			// update
			$requete = 'UPDATE indexint '.$requete;
			$requete .= ' WHERE indexint_id='.$this->indexint_id.' LIMIT 1;';
			if(pmb_mysql_query($requete)) {

				indexint::update_index($this->indexint_id);
				audit::insert_modif(AUDIT_INDEXINT,$this->indexint_id);

				$aut_link= new aut_link(AUT_TABLE_INDEXINT,$this->indexint_id);
				$aut_link->save_form();
				$aut_pperso= new aut_pperso("indexint",$this->indexint_id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;
					return false;
				}

			}else {
				require_once("$include_path/user_error.inc.php");
				warning($msg['indexint_update'], $msg['indexint_unable']);
				return FALSE;
			}
		} else {
			// création : s'assurer que le nom n'existe pas déjà
			$dummy = "SELECT * FROM indexint WHERE indexint_name = '".$nom."' and num_pclass='".$id_pclass."' LIMIT 1 ";
			$check = pmb_mysql_query($dummy);
			if(pmb_mysql_num_rows($check)) {
				require_once("$include_path/user_error.inc.php");
				print $this->warning_already_exist($msg['indexint_create'], $msg['indexint_exists']);
				return FALSE;
			}
			$requete = 'INSERT INTO indexint '.$requete.';';
			if(pmb_mysql_query($requete)) {
				$this->indexint_id=pmb_mysql_insert_id();

				audit::insert_creation(AUDIT_INDEXINT,$this->indexint_id);

				$aut_link= new aut_link(AUT_TABLE_INDEXINT,$this->indexint_id);
				$aut_link->save_form();
				$aut_pperso= new aut_pperso("indexint",$this->indexint_id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;
					return false;
				}
			}
			else {
				require_once("$include_path/user_error.inc.php");
				warning($msg['indexint_create'], $msg['indexint_unable_create']);
				return FALSE;
			}
		}
		//update authority informations
		$authority = new authority(0, $this->indexint_id, AUT_TABLE_INDEXINT);
		$authority->set_num_statut($statut);
		$authority->set_thumbnail_url($thumbnail_url);
		$authority->update();

		// Indexation concepts
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->indexint_id, TYPE_INDEXINT);
			$index_concept->save();
		}

		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($this->indexint_id, TYPE_INDEXINT);

		indexint::update_index($this->indexint_id);

		return TRUE;
	}

	// ---------------------------------------------------------------
	//		import() : import d'une indexation
	// ---------------------------------------------------------------
	// fonction d'import de notice : indexation interne : INUTILISEE à la date du 12/02/04
	public static function import($name,$comment="",$id_pclassement="", $statut=1, $thumbnail_url='') {
		global $pmb_limitation_dewey ;
		global $thesaurus_classement_defaut;

		// check sur la variable passée en paramètre
		if (!$name) return 0;

		if ($pmb_limitation_dewey<0) return 0;

		if ($pmb_limitation_dewey) $name=substr($name,0,$pmb_limitation_dewey) ;

		// tentative de récupérer l'id associée dans la base (implique que l'autorité existe)
		// préparation de la requête
		$key = addslashes($name);
		$comment = addslashes($comment);
		if (!$id_pclassement) {
			 $num_pclass=$thesaurus_classement_defaut;
		} else {
			$num_pclass=$id_pclassement;
		}

		//On regarde si le plan de classement existe
		$query = "SELECT name_pclass FROM pclassement WHERE id_pclass='".addslashes($num_pclass)."' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT pclassement ".$query);
		if(!pmb_mysql_num_rows($result)){//Le plan de classement demandé n'existe pas
			return 0;// -> pas d'import
		}

		$query = "SELECT indexint_id FROM indexint WHERE indexint_name='".rtrim(substr($key,0,255))."' and num_pclass='$num_pclass' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT indexint ".$query);

		// récupération du résultat de la recherche
		if(pmb_mysql_num_rows($result)) {
			$tindexint = pmb_mysql_fetch_object($result);
			// du résultat et récupération éventuelle de l'id
			if ($tindexint->indexint_id) {
				return $tindexint->indexint_id;
			}
		}

		// id non-récupérée >> création
		if (!$id_pclassement) {
			 $num_pclass=$thesaurus_classement_defaut;
		} else {
			$num_pclass=$id_pclassement;
		}
		$query = "INSERT INTO indexint SET indexint_name='$key', indexint_comment='$comment', index_indexint=' ".strip_empty_words($key." ".$comment)." ', num_pclass=$num_pclass ";

		$result = pmb_mysql_query($query);
		if(!$result) die("can't INSERT into indexint ".$query);
		$id=pmb_mysql_insert_id();
		audit::insert_creation(AUDIT_INDEXINT,$id);

		//update authority informations
		$authority = new authority(0, $id, AUT_TABLE_INDEXINT);
		$authority->set_num_statut($statut);
		$authority->set_thumbnail_url($thumbnail_url);
		$authority->update();

		indexint::update_index($id);
		return $id;
	}

	// ---------------------------------------------------------------
	//		search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	public static function search_form($id_pclass=0) {
		global $user_query, $user_input;
		global $msg;
		global $thesaurus_classement_mode_pmb;
		global $charset ;
		global $authority_statut ;
		global $exact;

		// Gestion Indexation décimale multiple
		if ($thesaurus_classement_mode_pmb != 0) { //la liste des pclassement n'est pas affichée en mode monopclassement
			$sel_pclassement = '';
			$requete = "SELECT id_pclass, name_pclass,	typedoc FROM pclassement order by id_pclass";
			$result = pmb_mysql_query($requete) or die ($requete."<br />".pmb_mysql_error());

			$sel_pclassement = "<select class='saisie-30em' id='id_pclass' name='id_pclass'>";
			$sel_pclassement.= "<option value='0' ";

			if ($id_pclass==0) $sel_pclassement.= " selected";
			$sel_pclassement.= ">".htmlentities($msg["pclassement_select_index_standart"],ENT_QUOTES, $charset)."</option>";
			while ($lue=pmb_mysql_fetch_object($result)) {
				$sel_pclassement.= "<option value='".$lue->id_pclass."' "; ;
				if ($lue->id_pclass == $id_pclass) $sel_pclassement.= " selected";
				$sel_pclassement.= ">".htmlentities($lue->name_pclass,ENT_QUOTES, $charset)."</option>";
			}
			$sel_pclassement.= "</select>&nbsp;";
			$pclass_url="&id_pclass=".$id_pclass;
			$user_query = str_replace ('<!-- sel_pclassement -->', $sel_pclassement , $user_query);
			$user_query = str_replace ('<!-- lien_classement -->', "<a href='".static::format_url('&sub=pclass')."'>".$msg['pclassement_link_edition']."</a> ", $user_query);
		} else {
			$pclass_url="";
		}
		$user_query = str_replace ('!!user_query_title!!', $msg[357]." : ".$msg['indexint_menu_title'] , $user_query);
		$user_query = str_replace ('!!action!!', static::format_url('&sub=reach&id='), $user_query);
		$user_query = str_replace ('!!checked_index!!', ($exact ? "checked='checked'" : '') , $user_query);
		$user_query = str_replace ('!!checked_comment!!', (!$exact ? "checked='checked'" : '') , $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg["indexint_create_button"] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', static::format_url('&sub=indexint_form'.$pclass_url), $user_query);
		$user_query = str_replace ('<!-- lien_derniers -->', "<a href='".static::format_url('&sub=indexint_last'.$pclass_url)."'>$msg[indexint_last]</a>", $user_query);

		$user_query = str_replace('<!-- sel_authority_statuts -->', authorities_statuts::get_form_for(AUT_TABLE_INDEXINT, $authority_statut, true), $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);

		print pmb_bidi($user_query) ;
	}

	public function has_notices() {
		$query = "select count(1) from notices where indexint=".$this->indexint_id;
		$result = pmb_mysql_query($query);
		return (pmb_mysql_result($result, 0, 0));
	}

	//---------------------------------------------------------------
	// update_index($id) : maj des index
	//---------------------------------------------------------------
	public static function update_index($id, $datatype = 'all') {
		indexation_stack::push($id, TYPE_INDEXINT, $datatype);

		// On cherche tous les n-uplet de la table notice correspondant à cette index. décimale.
		$query = "select distinct notice_id from notices where indexint='".$id."'";
		authority::update_records_index($query, 'indexint');
	}

	public function get_header() {
		return $this->display;
	}

	public function get_cp_error_message(){
		return $this->cp_error_message;
	}


	public function get_gestion_link(){
		return './autorites.php?categ=see&sub=indexint&id='.$this->indexint_id;
	}

	public function get_isbd() {
		global $thesaurus_classement_mode_pmb;

		if ($this->comment) $isbd = $this->name." - ".str_replace("\r"," ",str_replace("\n"," ",$this->comment));
		else $isbd = $this->name ;
		if ($thesaurus_classement_mode_pmb != 0) {
			$isbd = "[".$this->name_pclass."] ".$isbd;
		}
		return $isbd;
	}

	public static function get_format_data_structure($antiloop = false) {
		global $msg;

		$main_fields = array();
		$main_fields[] = array(
				'var' => "name",
				'desc' => $msg['indexint_nom']
		);
		$main_fields[] = array(
				'var' => "comment",
				'desc' => $msg['indexint_comment']
		);
		$authority = new authority(0, 0, AUT_TABLE_INDEXINT);
		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}

	public function format_datas($antiloop = false){
		$formatted_data = array(
				'name' => $this->name,
				'comment' => $this->comment
		);
		$authority = new authority(0, $this->indexint_id, AUT_TABLE_INDEXINT);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}

	public static function set_controller($controller) {
		static::$controller = $controller;
	}

	protected static function format_url($url='') {
		global $base_path;

		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_url_base().$url;
		} else {
			return $base_path.'/autorites.php?categ=indexint'.$url;
		}
	}

	protected static function format_back_url() {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_back_url();
		} else {
			return "history.go(-1)";
		}
	}

	protected static function format_delete_url($url='') {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_delete_url();
		} else {
			return static::format_url("&sub=delete".$url);
		}
	}

	protected function warning_already_exist($error_title, $error_message, $values=array())  {
		$authority = new authority(0, $this->indexint_id, AUT_TABLE_INDEXINT);
		$display = $authority->get_display_authority_already_exist($error_title, $error_message, $values);
		$display = str_replace("!!action!!", static::format_url(), $display);
		$display = str_replace("!!forcing_button!!", '', $display);

		$hidden_specific_values = $authority->put_global_in_hidden_field("indexint_nom");
		$hidden_specific_values .= $authority->put_global_in_hidden_field("indexint_comment");
		$hidden_specific_values .= $authority->put_global_in_hidden_field("indexint_pclassement");
		$hidden_specific_values .= $authority->put_global_in_hidden_field("authority_statut");
		$hidden_specific_values .= $authority->put_global_in_hidden_field("authority_thumbnail_url");
		$display = str_replace('!!hidden_specific_values!!', $hidden_specific_values, $display);
		return $display;
	}
} # fin de définition de la classe indexint

} # fin de délaration

