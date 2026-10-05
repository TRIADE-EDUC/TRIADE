<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_records_controller.class.php,v 1.19.4.2 2025/04/25 07:50:57 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Digitalsignature\Models\DocnumCertifier;

global $class_path;
require_once ($class_path."/entities/entities_controller.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/notice_doublon.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path.'/event/events/event_record.class.php');

class entities_records_controller extends entities_controller {

	protected $url_base = './catalog.php';

	protected $signature;

	protected $model_class_name = 'notice';

	public function get_display_object_instance($id=0, $niveau_biblio='') {
		return new mono_display($id);
	}

	/**
	 * 8 = droits de modification
	 */
	protected function get_acces_m() {
		global $PMBuserid;

		$acces_m = 1;
		if($this->id) $acces_m = $this->dom_1->getRights($PMBuserid,$this->id,8);
		if($acces_m == 0) {
			$this->error_message = 'mod_noti_error';
		}
		return $acces_m;
	}

	public function proceed() {
		global $PMBuserid;

		//verification des droits de modification notice
		if($this->has_rights()) {
			switch($this->action) {
				case 'form':
				    $entity_locking = new entity_locking($this->id, TYPE_NOTICE);
				    if($entity_locking->is_locked()){
				        print $entity_locking->get_locked_form();
				        break;
				    }
			        $this->proceed_form();
			        $entity_locking->lock_entity();
			        print $entity_locking->get_polling_script();
					break;
				case 'duplicate':
					$this->proceed_duplicate();
					break;
				case 'update':
				    $entity_locking = new entity_locking($this->id, TYPE_NOTICE);
                    $this->proceed_update();
					$entity_locking->unlock_entity();
					break;
				case 'delete':
				    $entity_locking = new entity_locking($this->id, TYPE_NOTICE);
				    if($entity_locking->is_locked()){
				        print $entity_locking->get_locked_form();
				        break;
				    }
				    if($this->is_deletable()) {
				    	// On déclenche un événement sur la supression
				    	$evt_handler = events_handler::get_instance();
				    	$event = new event_entity("entity", "has_deletion_rights");
				    	$event->set_entity_id($this->id);
				    	$event->set_entity_type(TYPE_NOTICE);
				    	$event->set_user_id($PMBuserid);
				    	$evt_handler->send($event);
				    	if($event->get_error_message()){
				    		information_message('', $event->get_error_message(), 1, $this->get_permalink());
				    	} else {
				    		$this->proceed_delete();
				    	}
				    }
					break;
				case 'replace':
				    $entity_locking = new entity_locking($this->id, TYPE_NOTICE);
				    if($entity_locking->is_locked()){
				        print $entity_locking->get_locked_form();
				        break;
				    }
				    $this->proceed_replace();
					break;
				case 'expl_form':
					$this->proceed_expl_form();
					break;
				case 'expl_duplicate':
					$this->proceed_expl_duplicate();
					break;
				case 'expl_update':
					$this->proceed_expl_update();
					break;
				case 'expl_delete':
					if($this->is_expl_deletable()) {
						// On déclenche un événement sur la supression
						$evt_handler = events_handler::get_instance();
						$event = new event_entity("entity", "has_deletion_rights");
						$event->set_entity_id($this->id);
						$event->set_entity_type(TYPE_EXPL);
						$event->set_user_id($PMBuserid);
						$evt_handler->send($event);
						if($event->get_error_message()){
							information_message('', $event->get_error_message(), 1, $this->get_permalink());
						} else {
							$this->proceed_expl_delete();
						}
					}
					break;
				case 'explnum_form':
					$this->proceed_explnum_form();
					break;
				case 'explnum_update':
					$this->proceed_explnum_update();
					break;
				case 'explnum_delete':
					if($this->is_explnum_deletable()) {
						// On déclenche un événement sur la supression
						$evt_handler = events_handler::get_instance();
						$event = new event_entity("entity", "has_deletion_rights");
						$event->set_entity_id($this->id);
						$event->set_entity_type(TYPE_EXPLNUM);
						$event->set_user_id($PMBuserid);
						$evt_handler->send($event);
						if($event->get_error_message()){
							information_message('', $event->get_error_message(), 1, $this->get_permalink());
						} else {
							$this->proceed_explnum_delete();
						}
					}
					break;
			}
		} else {
			$this->display_error_message();
		}
	}

	protected function is_deletable() {
		return true;
	}

	protected function is_expl_deletable() {
		return true;
	}

	protected function is_explnum_deletable() {
		global $msg;

		$expl = new explnum($this->id);

		$docNumCertifier = new DocnumCertifier($expl);
		$check = $docNumCertifier->checkSignExists();
		if ($check) {
			print return_error_message($msg["540"], $msg["digital_signature_already_signed_docnum_del"], 1, "./catalog.php?categ=isbd&id=".$expl->explnum_notice);
			return false;
		}
		return true;
	}

	public function proceed_form() {
		global $saisieISBN, $cataloging_scheme_id;
		$myNotice = new notice($this->id, $saisieISBN);
		if(method_exists('notice', 'set_controller')) {
			notice::set_controller($this);
		}
		$entity_form = $myNotice->show_form();
		$entity_form = str_replace('<form', '<form data-advanced-form="true"', $entity_form);

		if ($cataloging_scheme_id) {
			$entity_form.= $this->get_cataloging_scheme_link_script($this->get_model_class_name());
		}

		print $entity_form;
	}

	public function proceed_duplicate() {
		global $msg;

		print "<h1>".$msg['catal_duplicate_notice']."</h1>";
		$myNotice = new notice($this->id);
		if(method_exists('notice', 'set_controller')) {
			notice::set_controller($this);
		}
		$myNotice->id=0 ;
		$myNotice->code="";
		$myNotice->duplicate_from_id = $this->id;
		print $myNotice->show_form();
	}

	protected function duplication_control() {
		global $msg, $charset;
		global $current_module;
		global $pmb_notice_controle_doublons;
		global $ret_url, $forcage;
		global $nb_per_page_search;

		if ($forcage == 1) {
			$tab= unserialize(stripslashes($ret_url));
			foreach($tab->GET as $key => $val){
				add_sl($val);
				$GLOBALS[$key] = $val;
			}
			foreach($tab->POST as $key => $val){
				add_sl($val);
				$GLOBALS[$key] = $val;
			}
		} else if( $pmb_notice_controle_doublons != 0 ) {
			//Si controle de dedoublonnage active
			// En modification de notice, on ne dedoublonne pas
			if(!$this->id) {
				$requete="select signature, niveau_biblio ,notice_id from notices where signature='".$this->signature."' ";
				if($this->id)	$requete.= " and notice_id != '".$this->id."' ";
				$result=pmb_mysql_query($requete);
				if ($dbls=pmb_mysql_num_rows($result)) {
					//affichage de l'erreur, en passant tous les param postes (serialise) pour l'eventuel forcage
					$tab=new stdClass();
					$tab->POST = $_POST;
					$tab->GET = $_GET;
					$ret_url= htmlentities(serialize($tab), ENT_QUOTES,$charset);

					switch (static::class) {
						case 'entities_records_controller':
							$action_form = $this->url_base.'?categ=update&id='.$this->id;
							break;
						case 'entities_serials_controller':
							$action_form = $this->url_base.'&sub=update&id='.$this->id;
							break;
					}
					print "
						<br /><div class='erreur'>$msg[540]</div>
						<script type='text/javascript' src='./javascript/tablist.js'></script>
						<div class='row'>
							<div class='colonne10'>
								<img src='".get_url_icon('error.gif')."' class='align_left'>
							</div>
							<div class='colonne80'>
								<strong>".$msg["gen_signature_erreur_similaire"]."</strong>
							</div>
						</div>
						<div class='row'>
							<form class='form-$current_module' name='dummy'  method='post' action='".$action_form."'>
								<input type='hidden' name='forcage' value='1'>
								<input type='hidden' name='signature' value='".$this->signature."'>
								<input type='hidden' name='ret_url' value='".$ret_url."'>
								<input type='button' name='ok' class='bouton' value=' $msg[76] ' onClick='history.go(-1);'>
								<input type='submit' class='bouton' name='bt_forcage' value=' ".htmlentities($msg["gen_signature_forcage"], ENT_QUOTES, $charset)." '>
							</form>

						</div>
						";
					if($dbls<$nb_per_page_search){
						$maxAffiche=$dbls;
						echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$dbls,$dbls)."</strong></div>";
					}else{
						$maxAffiche=$nb_per_page_search;
						echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$maxAffiche,$dbls)."</strong></div>";
					}
					$enCours=1;
					while($enCours<=$maxAffiche){
						$r=pmb_mysql_fetch_object($result);
						$nt = $this->get_display_object_instance($r->notice_id);

					echo "
						<div class='row'>
						$nt->result
				 	    </div>
						<script>document.getElementById('el".$nt->unique_id."Child').setAttribute('startOpen','Yes');</script>
						<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>";
						$enCours++;
					}
					exit();
				}
			}
		}
	} //fin du controle de dedoublonage

	public function proceed_update() {
		$sign = new notice_doublon();
		$this->signature = $sign->gen_signature();
		$this->duplication_control();
		$myNotice = $this->get_object_instance();
		$myNotice->signature = $this->signature;
		$myNotice->set_properties_from_form();
		$myNotice->save();
		$this->id = $myNotice->id;

		$event = new event_record('record', 'after_update');
		$event->set_record_id($this->id);
		$event_handler = events_handler::get_instance();
		$event_handler->send($event);

		return $myNotice->id;
	}

	public function proceed_delete() {

	}

	public function proceed_replace() {
		global $msg;
		global $by;

		$myNotice = $this->get_object_instance();
		$by = intval($by);
		if(!$by) {
			$myNotice->replace_form();
		} else {
			// routine de remplacement
			$rep_result = $myNotice->replace($by);
			if(!$rep_result) {
				print "<div class='row'><div class='msg-perio'>".$msg["maj_encours"]."</div></div>";
				print $this->get_display_view($by);
			} else {
				error_message($msg[132], $rep_result, 1, $this->get_edit_link());
			}
		}
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."?categ=isbd&id=".$id;
	}

	protected function get_edit_link($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."?categ=modif&id=".$id;
	}

	public function get_display_view($id=0) {
		print "<script type='text/javascript'>
			document.location = '".$this->get_permalink($id)."';
			</script>";
	}

	public function get_document_title() {
		$class_instance = $this->get_model_class_name();
		if ($class_instance) {
			return $class_instance->tit1;
		}
		return '';
	}

	public function proceed_expl_form() {}

	public function proceed_expl_duplicate() {}

	public function proceed_expl_update() {}

	public function proceed_expl_delete() {}
}
