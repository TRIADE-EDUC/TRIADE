<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.class.php,v 1.164.2.2.2.4 2025/04/30 08:32:19 rtigero Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

use Pmb\Digitalsignature\Models\DocnumCertifier;
use Pmb\Common\Orm\UploadFolderOrm;
use Pmb\Common\Library\CSRF\CollectionCSRF;

global $gestion_acces_active;
global $class_path, $include_path;
if(!isset($gestion_acces_active)) $gestion_acces_active = 0;
if ($gestion_acces_active == 1) {
	require_once ("$class_path/acces.class.php");
}
require_once ($class_path . "/zip.class.php");
require_once ($class_path . "/upload_folder.class.php");
require_once ($class_path . "/docs_location.class.php");
require_once ($include_path . "/explnum.inc.php");
require_once ($class_path . "/indexation_docnum.class.php");
require_once ($class_path . "/diarization_docnum.class.php");
require_once ($class_path . "/notice.class.php");
require_once ($class_path . "/index_concept.class.php");
require_once ($class_path . "/event/events/event_explnum.class.php");
require_once ($class_path . "/parametres_perso.class.php");
require_once($class_path.'/audit.class.php');
require_once($class_path.'/explnum_licence/explnum_licence.class.php');
require_once ($include_path . "/templates/explnum.tpl.php");
require_once($class_path.'/file_uploader.class.php');
require_once($class_path.'/thumbnail.class.php');

// classe de gestion des exemplaires numériques

if (! defined('EXPLNUM_CLASS')) {
	define('EXPLNUM_CLASS', 1);
	class explnum {
		public $explnum_id = 0;
		public $explnum_notice = 0;
		public $explnum_bulletin = 0;
		public $explnum_nom = '';
		public $explnum_mimetype = '';
		public $explnum_url = '';
		public $explnum_data = '';
		public $explnum_vignette = '';
		public $explnum_statut = '0';
		public $explnum_index = '';
		public $explnum_repertoire = 0;
		public $explnum_path = '';
		public $explnum_nomfichier = '';
		public $explnum_rep_nom = '';
		public $explnum_rep_path = '';
		public $explnum_index_wew = '';
		public $explnum_index_sew = '';
		public $explnum_extfichier = '';
		public $explnum_location = '';
		public $infos_docnum = array();
		public $params = array();
		public $unzipped_files = array();
		public $explnum_docnum_statut = '1';
		public $lenders = array();
		protected $explnum_signature;
		protected $explnum_create_date;
		protected $explnum_update_date;
		protected $explnum_file_size;
		protected $has_doublons;

        /**
         * Instance de parametres_perso associée
		 * @var parametres_perso
		 */
 		protected $p_perso;

		// constructeur
		public function __construct($id = 0, $id_notice = 0, $id_bulletin = 0) {
			$this->explnum_id = intval($id);
			$this->explnum_notice = intval($id_notice);
			$this->explnum_bulletin = intval($id_bulletin);
			$this->fetch_data();
		}

		protected function init_repertoire() {
			$req = "select repertoire_id, repertoire_nom, repertoire_path from  upload_repertoire, users where repertoire_id=deflt_upload_repertoire and username='" . SESSlogin . "'";
			$res = pmb_mysql_query($req);
			if (pmb_mysql_num_rows($res)) {
				$item = pmb_mysql_fetch_object($res);
				$this->explnum_rep_nom = $item->repertoire_nom;
				$this->explnum_rep_path = $item->repertoire_path;
				$this->explnum_repertoire = $item->repertoire_id;
			} else {
				$this->explnum_rep_nom = '';
				$this->explnum_rep_path = '';
				$this->explnum_repertoire = 0;
			}
		}

		protected function fetch_data() {
			global $pmb_indexation_docnum_default, $deflt_explnum_statut, $deflt_explnum_lenders;

			$this->explnum_nom = '';
			$this->explnum_mimetype = '';
			$this->explnum_url = '';
			$this->explnum_data = '';
			$this->explnum_vignette = '';
			$this->explnum_statut = '0';
			$this->init_repertoire();
			$this->explnum_index = ($pmb_indexation_docnum_default ? 'checked' : '');
			$this->explnum_path = '';
			$this->explnum_nomfichier = '';
			$this->explnum_extfichier = '';
			$this->explnum_location = '';
			$this->explnum_docnum_statut = ($deflt_explnum_statut ? $deflt_explnum_statut : '1');
			$this->lenders = array(0 => $deflt_explnum_lenders);
			$this->explnum_create_date = '0000-00-00 00:00:00';
			$this->explnum_update_date = '0000-00-00 00:00:00';
			$this->explnum_file_size = 0;
			$this->unzipped_files = array();

			if ($this->explnum_id) {
				$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_extfichier, explnum_url, explnum_data, explnum_vignette,
				explnum_statut, explnum_index_sew, explnum_index_wew, explnum_repertoire, explnum_nomfichier, explnum_path, repertoire_nom, repertoire_path, group_concat(num_location SEPARATOR ',') as loc, explnum_docnum_statut,
				explnum_signature, explnum_create_date, explnum_update_date, explnum_file_size
				FROM explnum left join upload_repertoire on explnum_repertoire=repertoire_id left join explnum_location on num_explnum=explnum_id where explnum_id='".$this->explnum_id."' group by explnum_id";
				$result = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result)) {
					$item = pmb_mysql_fetch_object($result);
					$this->explnum_notice = $item->explnum_notice;
					$this->explnum_bulletin = $item->explnum_bulletin;
					$this->explnum_nom = $item->explnum_nom;
					$this->explnum_mimetype = $item->explnum_mimetype;
					$this->explnum_url = $item->explnum_url;
					$this->explnum_data = $item->explnum_data;
					$this->explnum_vignette = $item->explnum_vignette;
					$this->explnum_statut = $item->explnum_statut;
					$this->explnum_index_wew = $item->explnum_index_wew;
					$this->explnum_index_sew = $item->explnum_index_sew;
					$this->explnum_index = (($item->explnum_index_wew || $item->explnum_index_sew || $pmb_indexation_docnum_default) ? 'checked' : '');
					$this->explnum_repertoire = $item->explnum_repertoire;
					$this->explnum_path = $item->explnum_path;
					$this->explnum_rep_nom = $item->repertoire_nom;
					$this->explnum_rep_path = $item->repertoire_path;
					$this->explnum_nomfichier = $item->explnum_nomfichier;
					$this->explnum_extfichier = $item->explnum_extfichier;
					$this->explnum_location = $item->loc ? explode(",", $item->loc) : '';
					$this->explnum_docnum_statut = $item->explnum_docnum_statut;
					$this->explnum_signature = $item->explnum_signature;
					$this->explnum_create_date = $item->explnum_create_date;
					$this->explnum_update_date = $item->explnum_update_date;
					$this->explnum_file_size = $item->explnum_file_size;

					$this->lenders = array();
					$query = 'select explnum_lender_num_lender from explnum_lenders where explnum_lender_num_explnum=' . $this->explnum_id;
					$result_lender = pmb_mysql_query($query);
					if (pmb_mysql_num_rows($result_lender)) {
						while ( $lender = pmb_mysql_fetch_object($result_lender) ) {
							$this->lenders[] = $lender->explnum_lender_num_lender;
						}
					}
				}
			}
		}

		/*
		 * Construction du formulaire
		 */
		public function fill_form(&$form, $action, $suppr = '') {
			global $charset;
			global $msg, $lang;
			global $pmb_scan_pmbws_client_url, $pmb_scan_pmbws_url;
			global $pmb_indexation_docnum, $pmb_explnum_statut;
			global $b_mimetype;
			global $pmb_docnum_in_directory_allow, $pmb_docnum_in_database_allow;
			global $explnum_id;
			global $pmb_diarization_docnum;
			global $base_path;
			global $thesaurus_concepts_active;
			global $pmb_type_audit;

			$form = str_replace('!!action!!', $action, $form);
			$form = str_replace('!!explnum_id!!', $this->explnum_id, $form);
			$form = str_replace('!!bulletin!!', $this->explnum_bulletin, $form);
			$form = str_replace('!!notice!!', $this->explnum_notice, $form);
			$form = str_replace('!!nom!!', htmlentities($this->explnum_nom, ENT_QUOTES, $charset), $form);
			$form = str_replace('!!url!!', htmlentities($this->explnum_url, ENT_QUOTES, $charset), $form);
			if($this->explnum_id && $this->explnum_nomfichier && $this->explnum_mimetype != 'URL') {
				$form = str_replace('!!disabled_url!!', "disabled='disabled' placeholder='".htmlentities($msg['explnum_url_associated_already'], ENT_QUOTES, $charset)."'", $form);
			} else {
				$form = str_replace('!!disabled_url!!', "", $form);
			}

			// Gestion de l'interface d'indexation
			if ($pmb_indexation_docnum) {
				$checkbox = "<div id='el0Child_6' class='row' movable='yes' title=\"" . htmlentities($msg['docnum_a_indexer'], ENT_QUOTES, $charset) . "\">
				 		<input type='checkbox' id='ck_index' value='1' name='ck_index' $this->explnum_index /><label for='ck_index'>" . $msg['docnum_a_indexer'] . "</label>
				 	</div>
				 ";
				if ($this->explnum_index_sew != '' && $this->explnum_index_wew != '') {
					$fct = "
				 	<script> function suppr_index(form){
				 		 if(!form.ck_index.checked) {
				 		 	conf = confirm(\"" . $msg['docnum_suppr_index'] . "\");
				 			return conf;
				 		 }
				 		 return true;
				 	}</script>
				 	";
					$form = str_replace("!!submit_action!!", 'return suppr_index(this)', $form);
				} else {
					$fct = "";
					$form = str_replace("!!submit_action!!", "return testing_file(" . $this->explnum_id . ");", $form);
				}
				$form = str_replace('!!ck_indexation!!', $checkbox . $fct, $form);
			} else {
				$form = str_replace("!!ck_indexation!!", "", $form);
				$form = str_replace("!!submit_action!!", "return testing_file(" . $this->explnum_id . ");", $form);
			}

			// Gestion de l'interface de segmentation
			if ($pmb_diarization_docnum) {
				$checkbox = "<div id='el0Child_7' class='row' movable='yes' title=\"" . htmlentities($msg['diarize_explnum'], ENT_QUOTES, $charset) . "\">
				 		<input type='checkbox' id='ck_diarization' value='1' name='ck_diarization' /><label for='ck_diarization'>" . $msg['diarize_explnum'] . "</label>
				 	</div>
				 ";
				$form = str_replace('!!ck_diarization!!', $checkbox, $form);
			} else {
				$form = str_replace("!!ck_diarization!!", "", $form);
			}

			// Gestion du scanner
			if (($pmb_scan_pmbws_client_url) && ($pmb_scan_pmbws_url)) {
				$scan_addon = "
				<script>function afterscan(format) {
					if (document.explnum.f_fichier) {
						sitxt=document.createElement('span');
						sitxt.setAttribute('id','scanned_image_txt');
						sitxt.className='erreur';
						document.explnum.f_fichier.parentNode.replaceChild(sitxt,document.explnum.f_fichier);
					}
					document.getElementById('scanned_image_txt').innerHTML='" . $msg["scan_image_recorded"] . "';
					document.getElementById('scanned_image_ext').value=format;
				}</script>
				<input type='button' value='" . $msg["scan_button"] . "' onClick='openPopUp(\"" . $pmb_scan_pmbws_client_url . "?scanfield=scanned_image&urlbase=" . rawurlencode($pmb_scan_pmbws_url) . "&scanform=explnum&callbackimage=afterscan&lang=$lang&charset=$charset\",\"scanWindow\",900,700,0,0,\"scrollbars=yes, resizable=yes\")' class='bouton'/>
				<input type='hidden' name='scanned_image_ext' id='scanned_image_ext' value=''/>
				<input type='hidden' name='scanned_image' value=''/>
				<input type='hidden' id='scanned_texte' name='scanned_texte' value=''/>";
				$form = str_replace('<!-- !!scan_button!! -->', $scan_addon, $form);
			}

			// Ajout du bouton d'association s'il y a des segments en base
			$associer = "";
			$fct = "";
			if ($this->explnum_id) {
				$nb = 0;
				$query = "select count(*) as nb from explnum_segments where explnum_segment_explnum_num = " . $this->explnum_id;
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					$nb = pmb_mysql_fetch_object($result)->nb;
				}
				if ($nb > 0) {
					$associer = "<input type='button' class='bouton' value=\"" . $msg['associate_speakers'] . "\" name='associate_speakers' id='associate_speakers' onClick=\"document.location = '" . $base_path . "/catalog.php?categ=explnum_associate&explnum_id=" . $this->explnum_id . "';\" />";

					if ($pmb_diarization_docnum) {
						// On ajoute une confirmation pour une deuxième segmentation => perte des associations
						$fct = "<script type='text/javascript'>
							function conf_diarize_again() {
								if (document.getElementById('ck_diarization').checked) {
									conf = confirm('" . addslashes($msg['explnum_associate_conf_diarize_again']) . "');
									if (!conf) {
										document.getElementById('ck_diarization').checked = false;
									}
								}
							}

							document.getElementById('ck_diarization').addEventListener('change', conf_diarize_again, false);
						</script>";
					}
				}
			}
			$form = str_replace("!!associate_speakers!!", $associer, $form);
			$form = str_replace("!!fct_conf_diarize_again!!", $fct, $form);

			// Champs persos
            $this->get_p_perso();
            $perso = '';
            if (!$this->p_perso->no_special_fields) {
                $perso_ = $this->p_perso->show_editable_fields($this->explnum_id);
				for($i = 0; $i < count($perso_["FIELDS"]); $i++) {
					if (($i == count($perso_["FIELDS"]) - 1) && ($i % 2 == 0))
						$element_class = 'row';
					else
						$element_class = 'colonne2';
					$p = $perso_["FIELDS"][$i];
					$perso .= "<div id='el0Child_8_" . $p["ID"] . "' class='" . $element_class . "' movable='yes' title=\"" . htmlentities($p["TITRE"], ENT_QUOTES, $charset) . "\">
								<label for='" . $p["NAME"] . "' class='etiquette'>" . $p["TITRE"] . " </label>" . $p["COMMENT_DISPLAY"] . "
								<div class='row'>" . $p["AFF"] . "</div>
							</div>\n";
				}
				$perso = $perso_["CHECK_SCRIPTS"] . "\n" . $perso;
			} else {
				$perso = "\n<script>function check_form() { return true; }</script>\n";
			}
			$form = str_replace("!!champs_perso!!", $perso, $form);

			$form = str_replace("!!rights_form!!", $this->get_rights_form(), $form);

			// Ajout du bouton supprimer si modification
			if ($this->explnum_id && $suppr) {
				$supprimer = "
					<script type=\"text/javascript\">
					    function confirm_delete() {
		        			result = confirm(\"{$msg["confirmdelete_explnum"]} ?\");
		        			if(result)
		            			document.location = \"$suppr\";
		    			}
					</script>
					<input type='button' class='bouton' value=\"{$msg['63']}\" name='del_ex' id='del_ex' onClick=\"confirm_delete();\" />
					";
			} else {
				$supprimer = "";
			}
			$form = str_replace('!!supprimer!!', $supprimer, $form);

			// Gestion du statut de notice
			if ($pmb_explnum_statut == '1') {
				$explnum_statut_form = "&nbsp;<input type='checkbox' id='f_statut_chk' name='f_statut_chk' value='1' ";
				if ($this->explnum_statut == '1')
					$explnum_statut_form .= "checked='checked' ";
				$explnum_statut_form .= "/>&nbsp;<label class='etiquette' for='f_statut_chk'>" . htmlentities($msg['explnum_statut_msg'], ENT_QUOTES, $charset) . "</label>";
				$form = str_replace('<!-- explnum_statut -->', $explnum_statut_form, $form);
			}

			// Conserver la vignette
			if ($this->explnum_vignette)
				$form = str_replace('!!vignette_existante!!', "&nbsp;<input type='checkbox' checked='checked' name='conservervignette' id='conservervignette' value='1'>&nbsp;<label for='conservervignette'>" . $msg['explnum_conservervignette'] . "</label>", $form);
			else
				$form = str_replace('!!vignette_existante!!', '', $form);
			global $_mimetypes_bymimetype_;
			create_tableau_mimetype();
			$selector_mimetype = "<label class='etiquette'>" . htmlentities($msg['explnum_mime_label'], ENT_QUOTES, $charset) . "</label>&nbsp;<select id='mime_vign' name='mime_vign' >
			<option value=''>" . htmlentities($msg['explnum_no_mimetype'], ENT_QUOTES, $charset) . "</option>
			";
			foreach ( $_mimetypes_bymimetype_ as $key => $val ) {
				$selected="";
				// if($this->explnum_mimetype == $key)
				// $selected = "selected";
				$selector_mimetype .= "<option value='" . $key . "' $selected >" . htmlentities($key, ENT_QUOTES, $charset) . "</option>";
			}
			$selector_mimetype .= "</select>";
			$form = str_replace('!!mimetype_list!!', $selector_mimetype, $form);

			// Intégration de la gestion de l'interface de l'upload
			if ($pmb_docnum_in_directory_allow) {
				$div_up = "<div class='row'>";
				if ($pmb_docnum_in_database_allow)
					$div_up .= "<input type='radio' name='up_place' id='base' value='0' !!check_base!!/> <label for='base'>".$msg['upload_repertoire_sql']."</label>";

				$div_up .= "	<input type='radio' name='up_place' id='upload' value='1' !!check_up!! />
								<label for='upload'>".$msg['upload_repertoire_server']."
									<input type='text' name='path' id='path' class='saisie-50emr' value='!!path!!' />
                                    <input type='button' class='bouton' id='upload_path' value='...' onclick='upload_openFrame(event)'/>
								</label>
								<input type='hidden' name='id_rep' id='id_rep' value='!!id_rep!!' />
                                <input type='hidden' name='folder_path' id='folder_path' value='!!folder_path!!' />
							</div>";
				$form = str_replace('!!div_upload!!', $div_up, $form);
				$up = new upload_folder($this->explnum_repertoire);
				$nom_chemin = $this->explnum_rep_nom ?? '';
				if ($nom_chemin) {
					if ($up->isHashing()) {
						$nom_chemin .= "/";
					} else {
						$nom_chemin .= ($this->explnum_path === '' ? "/" : $this->explnum_path);
					}
				}
				$form = str_replace('!!path!!', htmlentities($nom_chemin, ENT_QUOTES, $charset), $form);
				$form = str_replace('!!id_rep!!', htmlentities($this->explnum_repertoire, ENT_QUOTES, $charset), $form);

				//Construction du chemin de stockage du fichier
				$folder_path = $this->explnum_repertoire."_";
				if( ($up->repertoire_navigation == 1) && !empty($this->explnum_path) ) {
				    $folder_path.= $this->explnum_path;
				}
				$form = str_replace('!!folder_path!!', rawurlencode($folder_path), $form);

				if ($this->explnum_rep_nom || $this->isEnUpload()) {
					$form = str_replace('!!check_base!!', '', $form);
					$form = str_replace('!!check_up!!', "checked='checked'", $form);
				} else {
					$form = str_replace('!!check_base!!', "checked='checked'", $form);
					$form = str_replace('!!check_up!!', '', $form);
				}
			} else {
				$form = str_replace('!!div_upload!!', '', $form);
			}

			// Ajout du selecteur de localisation
			$liste_id = array();
			if ($explnum_id) {
				if (! $this->explnum_location) {
					$requete = "select idlocation from docs_location";
					$res = pmb_mysql_query($requete);
					$i = 0;
					while ( $row = pmb_mysql_fetch_array($res) ) {
						$liste_id[$i] = $row["idlocation"];
						$i++;
					}
				} else {
					$liste_id = $this->explnum_location;
				}
			} else {
				global $deflt_explnum_location;
				$liste_id[0] = $deflt_explnum_location;
			}

			$docloc = new docs_location();
			$selector_location = $docloc->gen_multiple_combo($liste_id);

			$form = str_replace('!!location_explnum!!', "<div class='row'><label class='etiquette'>" . htmlentities($msg['empr_location'], ENT_QUOTES, $charset) . "</label></div>" . $selector_location, $form);

			$form = str_replace('!!lenders!!', lender::gen_multiple_combo_box($this->lenders), $form);

			// statut
			$select_statut = gen_liste_multiple("select id_explnum_statut, gestion_libelle from explnum_statut order by 2", "id_explnum_statut", "gestion_libelle", "id_explnum_statut", "f_explnum_statut", "", $this->explnum_docnum_statut, "", "", "", "", 0);
			$form = str_replace('!!statut_list!!', $select_statut, $form);

			$explnum_licence_selector = explnum_licence::get_licence_selector(explnum_licence::get_explnum_licence_profiles($this->explnum_id));
			if ($explnum_licence_selector) {
				$explnum_licence_selector = "
						<div id='el0Child_5' class='row' movable='yes' title=\"".htmlentities($msg['admin_menu_noti_licence'], ENT_QUOTES, $charset)."\">
							<div class='row'>
							    <label class='etiquette'>".$msg['admin_menu_noti_licence']."</label>
					    	</div>
						    <div class='row'>
						    ".$explnum_licence_selector."
						    </div>
						    <div class='row'>&nbsp;</div>
						</div>";
			}

			$form = str_replace('!!explnum_licence_selectors!!', $explnum_licence_selector, $form);

			// Indexation concept
			if ($thesaurus_concepts_active == 1) {
				$index_concept = new index_concept($this->explnum_id, TYPE_EXPLNUM);
				$form = str_replace('!!index_concept_form!!', $index_concept->get_form('explnum'), $form);
			} else {
				$form = str_replace('!!index_concept_form!!', "", $form);
			}

			if($pmb_type_audit && $this->explnum_id) {
				$form = str_replace('!!link_audit!!', audit::get_dialog_button($this->explnum_id, AUDIT_EXPLNUM), $form);
			} else {
				$form = str_replace('!!link_audit!!', '', $form);
			}

		    $form = str_replace('!!sign_docnum!!', $this->get_form_cert(), $form);

		    $collectionCSRF = new CollectionCSRF();
		    $tokens = json_encode($collectionCSRF->getArrayTokens());

		    $form = str_replace('!!tokens_csrf!!', $tokens, $form);
		}

		/*
		 * Appel au constructeur du formulaire puis retourne le formulaire créé
		 */
		public function explnum_form($action, $annuler = '', $suppr = '') {
			global $explnum_form;

			// $action .= '&id='.$this->explnum_id;

			$this->fill_form($explnum_form, $action, $suppr);

			// action du bouton annuler
			if (! $annuler) {
				// default : retour à la liste des exemplaires
				$annuler = './catalog.php?categ=isbd&id=' . $this->explnum_notice;
			}

			$explnum_form = str_replace('!!annuler_action!!', $annuler, $explnum_form);

			// affichage
			return $explnum_form;
		}

		/*
		 * Mise à jour des documents numériques
		 */
		public function mise_a_jour($f_notice, $f_bulletin, $f_nom, $f_url, $retour, $conservervignette, $f_statut_chk, $f_explnum_statut, $book_lender_id = array(), $forcage = 0, $f_url_vignette='') {
			global $multi_ck, $base_path, $iframe;

			$this->recuperer_explnum($f_notice, $f_bulletin, $f_nom, $f_url, $retour, $conservervignette, $f_statut_chk, $f_explnum_statut, $forcage, $f_url_vignette);
			if ($multi_ck) {
				// Gestion multifichier

				$this->unzip($base_path . "/temp/" . $this->infos_docnum["userfile_moved"]);
				if (! is_array($this->unzipped_files) || ! count($this->unzipped_files)) { // Si la décompression n'a pas fonctionnée on reprend le fonctionnement normal
					$this->infos_docnum["nom"] = "-x-x-x-x-";
					$this->analyser_docnum();
					$this->update(!$iframe);
				} else {
					$this->analyse_multifile();
				}
				if (file_exists($base_path . "/temp/" . $this->infos_docnum["userfile_moved"]))
					unlink($base_path . "/temp/" . $this->infos_docnum["userfile_moved"]);
			} else {
				// Gestion normale du fichier
				$this->analyser_docnum();
				$this->update(!$iframe);
			}

			if ($f_notice) {
				// Mise a jour de la table notices_mots_global_index
				notice::majNoticesMotsGlobalIndex($f_notice, "explnum");
			    notice::update_index($f_notice, "explnum");
			} elseif ($f_bulletin) {
				// Mise a jour de la table notices_mots_global_index pour toutes les notices en relation avec l'exemplaire
				$req_maj = "SELECT bulletin_notice,num_notice FROM bulletins WHERE bulletin_id='" . $f_bulletin . "'";
				$res_maj = pmb_mysql_query($req_maj);
				if ($res_maj && pmb_mysql_num_rows($res_maj)) {
					if ($tmp = pmb_mysql_result($res_maj, 0, 0)) { // Périodique
						notice::majNoticesMotsGlobalIndex($tmp, "explnum");
					}
					if ($tmp = pmb_mysql_result($res_maj, 0, 1)) { // Notice de bulletin
						notice::majNoticesMotsGlobalIndex($tmp, "explnum");
					}
				}
			}
			if($iframe){
			    return true;
			}
		}

		/*
		 * Effacement de l'exemplaire numérique
		 */
		public function delete() {
			/**
			 * Publication d'un évenement après la mise à jour
			 */

		    global $msg;
		    // Suppression des fichiers de signature
		    $docNumCertifier = new DocnumCertifier($this);
		    $check = $docNumCertifier->checkSignExists();
		    if ($check) {
		        return print return_error_message($msg["540"], $msg["digital_signature_already_signed_docnum_del"], 1, "./catalog.php?categ=isbd&id=".$this->explnum_notice);
		    }

			$evt_handler = events_handler::get_instance();
			$event = new event_explnum("explnum", "before_delete");
			$event->set_explnum($this);
			$evt_handler->send($event);

			if ($this->isEnUpload()) {
				$up = new upload_folder($this->explnum_repertoire);
				$chemin = str_replace("//", "/", $this->explnum_rep_path . $this->explnum_path . $this->explnum_nomfichier);
				$chemin = $up->encoder_chaine($chemin);
				if (file_exists($chemin))
					unlink($chemin);
			}
			$requete = "DELETE FROM explnum WHERE explnum_id=" . $this->explnum_id;
			pmb_mysql_query($requete);

			audit::delete_audit(AUDIT_EXPLNUM, $this->explnum_id);

			// on oublie pas la localisation associé
			$requete = "delete from explnum_location where num_explnum = " . $this->explnum_id;
			pmb_mysql_query($requete);

			// Suppression des segments et locuteurs
			$requete = "delete from explnum_speakers where explnum_speaker_explnum_num = " . $this->explnum_id;
			pmb_mysql_query($requete);

			$requete = "delete from explnum_segments where explnum_segment_explnum_num = " . $this->explnum_id;
			pmb_mysql_query($requete);

			// Nettoyage demande de numérisation
			$requete = "delete from scan_request_explnum where scan_request_explnum_num_explnum = " . $this->explnum_id;
			pmb_mysql_query($requete);

			$requete = "delete from caddie_content using caddie, caddie_content where caddie_id=idcaddie and type='EXPLNUM' and object_id='".$this->explnum_id."' ";
			pmb_mysql_query($requete);

			// Nettoyage indexation concepts
			$index_concept = new index_concept($this->explnum_id, TYPE_EXPLNUM);
			$index_concept->delete();

			// Nettoyage des régimes de licence
			explnum_licence::delete_explnum_licence_profiles($this->explnum_id);

			// Supression des champs perso
            $this->get_p_perso();
            $this->p_perso->delete_values($this->explnum_id);

			// On recalcule l'index global pour la notice
            if ($this->explnum_notice) {
                // Suppression de la vignette de la notice liee
                thumbnail::clearCache($this->explnum_notice, TYPE_NOTICE);
				// Mise a jour de la table notices_mots_global_index
			    notice::majNoticesMotsGlobalIndex($this->explnum_notice, "explnum");
			    notice::update_index($this->explnum_notice, "explnum");
			} elseif ($this->explnum_bulletin) {
				// Mise a jour de la table notices_mots_global_index pour toutes les notices en relation avec l'exemplaire
				$req_maj = "SELECT bulletin_notice,num_notice FROM bulletins WHERE bulletin_id='" . $this->explnum_bulletin . "'";
				$res_maj = pmb_mysql_query($req_maj);
				if ($res_maj && pmb_mysql_num_rows($res_maj)) {
					if ($tmp = pmb_mysql_result($res_maj, 0, 0)) { // Périodique
						notice::majNoticesMotsGlobalIndex($tmp, "explnum");
					}
					if ($tmp = pmb_mysql_result($res_maj, 0, 1)) { // Notice de bulletin
					    // Suppression de la vignette de la notice liee
					    thumbnail::clearCache($tmp, TYPE_NOTICE);
						notice::majNoticesMotsGlobalIndex($tmp, "explnum");
					}
				}
			}
		}

		protected function save_lenders() {
			global $book_lender_id;

			$query = "delete from explnum_lenders where explnum_lender_num_explnum='" . $this->explnum_id . "'";
			pmb_mysql_query($query);
			if (isset($book_lender_id) && is_array($book_lender_id) && count($book_lender_id)) {
			    if ((count($book_lender_id) == 1) && ($book_lender_id[0] == - 1)) {
			    } else {
			        foreach ($book_lender_id as $lender_id) {
			            $query = 'insert into explnum_lenders set explnum_lender_num_explnum=' . $this->explnum_id . ', explnum_lender_num_lender=' . $lender_id;
			            pmb_mysql_query($query);
			        }
			    }
			}
		}

		/**
		 * On enregistre la ou les localisations
		 */
		protected function save_locations() {
			global $loc_selector;

			$query = "delete from explnum_location where num_explnum='" . $this->explnum_id . "'";
			pmb_mysql_query($query);
			if(isset($loc_selector)) {
				if ((count($loc_selector) == 1) && ($loc_selector[0] == - 1)) {
				} else {
					for($i = 0; $i < count($loc_selector); $i++) {
						$req = "replace into explnum_location set num_explnum='" . $this->explnum_id . "', num_location='" . $loc_selector[$i] . "'";
						pmb_mysql_query($req);
					}
				}
			}
		}

		public function save() {
			global $id_rep, $up_place;
			global $gestion_acces_active, $gestion_acces_empr_docnum;
			global $res_prf, $chk_rights, $prf_rad, $r_rad;
			global $pmb_diarization_docnum;
			global $thesaurus_concepts_active;
			global $pmb_digital_signature_activate, $sign_data_cert, $ck_sign, $is_sign;

			if (empty($this->params["erreur"])) {
				$update = false;
				if ($this->explnum_id) {
					$query = "UPDATE explnum SET ";
					$limiter = " WHERE explnum_id='".$this->explnum_id."' ";
					$update = true;
				} else {
					$query = "INSERT INTO explnum SET ";
					$limiter = "";
				}
				$query .= " explnum_notice='".$this->explnum_notice."'";
				$query .= ", explnum_bulletin='".$this->explnum_bulletin."'";
				$query .= ", explnum_nom='".addslashes($this->explnum_nom)."'";
				$query .= ", explnum_url='".addslashes($this->explnum_url)."'";
				$query .= ", explnum_mimetype='".addslashes($this->explnum_mimetype)."'";
				$query .= ", explnum_data='".addslashes($this->explnum_data)."'";
				$query .= ", explnum_nomfichier='".addslashes($this->explnum_nomfichier)."'";
				$query .= ", explnum_extfichier='".addslashes($this->explnum_extfichier)."'";
				$query .= ", explnum_vignette='".addslashes($this->explnum_vignette)."'";
				$query .= ", explnum_statut='".$this->explnum_statut."'";
				$query .= ", explnum_repertoire='".$this->explnum_repertoire."'";
				$query .= ", explnum_path='".$this->explnum_path."'";
				$query .= ", explnum_docnum_statut='".$this->explnum_docnum_statut."'";
				$query .= ", explnum_signature = '".$this->gen_signature()."'".
				(!$this->explnum_id ? ", explnum_create_date=sysdate() " : "")."
				, explnum_update_date=sysdate() ";
				$query.= ", explnum_file_size = '".$this->explnum_file_size."'";
				$query .= $limiter;

				pmb_mysql_query($query);
				if (empty($this->infos_docnum['from_contrib']) || $this->infos_docnum['from_contrib'] !== true) {
    				if(!$this->explnum_id) {
    					$this->explnum_id = pmb_mysql_insert_id();
    					audit::insert_creation (AUDIT_EXPLNUM, $this->explnum_id);
    				} else {
    					audit::insert_modif (AUDIT_EXPLNUM, $this->explnum_id);
    				}

    				$this->save_lenders();
    				$this->save_locations();

    				$this->get_p_perso();
    				$this->p_perso->rec_fields_perso($this->explnum_id);
				}

				// traitement des droits acces user_docnum
				if ($gestion_acces_active == 1 && $gestion_acces_empr_docnum == 1) {
					$ac = new acces();
					$dom_3 = $ac->setDomain(3);
					if ($update) {
						$dom_3->storeUserRights(1, $this->explnum_id, $res_prf, $chk_rights, $prf_rad, $r_rad);
					} else {
						$dom_3->storeUserRights(0, $this->explnum_id, $res_prf, $chk_rights, $prf_rad, $r_rad);
					}
				}

				// Segmentation du document
				if ($pmb_diarization_docnum) {
					$this->diarization_docnum();
				}

				// Indexation concepts
				if ($thesaurus_concepts_active == 1) {
					$index_concept = new index_concept($this->explnum_id, TYPE_EXPLNUM);
					$index_concept->save();
				}

				// On vient des contributions, on a deja sauvegarder le regime de licence
				if (isset($this->infos_docnum["from_contrib"]) && $this->infos_docnum["from_contrib"]) {
    				return true;
                }

				explnum_licence::save_explnum_licence_profiles($this->explnum_id);

				if (!empty($this->explnum_notice)) {
    				// Suppression de la vignette de la notice liee
				    thumbnail::clearCache($this->explnum_notice, TYPE_NOTICE);
    				// indexation de la notice liee
				    notice::update_index($this->explnum_notice, "explnum");
				}

				if ($pmb_digital_signature_activate && $ck_sign && !isset($is_sign) && $is_sign != $this->explnum_id) {
				    $is_sign = $this->explnum_id;
				    $docSign = new DocnumCertifier($this);
				    $docSign->sign($sign_data_cert);
				}
				return true;
			} else {
				return false;
			}
		}

		protected function update_explnum_vignette($explnum_vignette) {
			$query = "update explnum set explnum_vignette='" . addslashes($explnum_vignette) . "' where explnum_id='" . $this->explnum_id . "'";
			pmb_mysql_query($query);
		}

		public function has_doublons($tmp_filename='') {
			global $base_path;

			if(!isset($this->has_doublons)) {
				$this->has_doublons = array();

				//Si controle de dedoublonnage active
				if (file_exists($base_path.'/temp/explnum_doublon_'.$this->explnum_notice)) {
					// On supprime les doublons stockés inutilement
					unlink($base_path.'/temp/explnum_doublon_'.$this->explnum_notice);
				}

				// En modification de document numérique, on ne dedoublonne pas
				if(!$this->explnum_id) {
					$signature = $this->gen_signature($tmp_filename);
					if ($signature) {
						$query = "select explnum_id, explnum_notice, explnum_bulletin, explnum_nom from explnum where explnum_signature = '".$signature."'";
						$result = pmb_mysql_query($query);
						$nb_doublons = pmb_mysql_num_rows($result);
						if ($nb_doublons) {
							while ($row = pmb_mysql_fetch_object($result)) {
								$this->has_doublons[] = $row;
							}
						}
					}
				}
			}
			return $this->has_doublons;
		}

		/*
		 * Mise à jour de l'exemplaire numérique
		 */
		public function update($with_print = true) {
			global $msg;
			global $current_module, $pmb_explnum_statut;
			global $id_rep, $up_place;
			global $mime_vign;

           	 /**
             * Publication d'un évenement avant la mise à jour
             */
            $evt_handler = events_handler::get_instance();
            $event = new event_explnum("explnum", "before_update");
            $event->set_explnum($this);
            $evt_handler->send($event);

			$update = false;
			if ($this->explnum_id) {
				$update = true;
			}
			if ($with_print) {
				print "<div class=\"row\"><h1>" . $msg['explnum_doc_associe'] . "</h1>";
			}
			if (empty($this->params["erreur"])) {
				$this->explnum_notice = intval($this->infos_docnum["notice"]);
				$this->explnum_bulletin = intval($this->infos_docnum["bull"]);
				$this->explnum_nom = stripslashes($this->infos_docnum["nom"]);
				$this->explnum_url = stripslashes($this->infos_docnum["url"] ?? '');
				if ($this->params["maj_mimetype"]) {
					$this->explnum_mimetype = stripslashes($this->infos_docnum["mime"]);
				}
				if ($this->params["maj_data"]) {
					if (!isset($this->params["is_upload"]) || !$this->params["is_upload"]) {
						$this->explnum_data = (isset($this->infos_docnum["contenu"]) ? $this->infos_docnum["contenu"] : '');
					} else {
						$this->explnum_data = '';
					}
					$this->explnum_nomfichier = $this->infos_docnum["userfile_name"];
					$this->explnum_extfichier = $this->infos_docnum["userfile_ext"];
				}
				if ($this->params["maj_vignette"] && (!isset($this->params["conservervignette"]) || !$this->params["conservervignette"])) {
					$this->explnum_vignette = $this->infos_docnum["contenu_vignette"];
				}
				if ($pmb_explnum_statut == '1') {
					$this->explnum_statut = $this->params["statut"];
				}
				$this->explnum_repertoire = (($up_place) ? $id_rep : 0);
				$this->explnum_path = $this->infos_docnum["path"];
				$this->explnum_docnum_statut = ((isset($this->params["explnum_statut"]) && $this->params["explnum_statut"]) ? $this->params["explnum_statut"] : 1);
				if ($this->params["maj_data"]) {
					$this->explnum_file_size = $this->get_file_size(true);
				}
			}
			$saved = $this->save();
			if ($saved) {
				// Indexation du document
				global $pmb_indexation_docnum;
				if ($pmb_indexation_docnum) {
					$vign_index = $this->indexer_docnum();
					if (! $mime_vign && (!isset($this->params["conservervignette"]) || !$this->params["conservervignette"]) && (!isset($this->infos_docnum["vignette_name"]) || !$this->infos_docnum["vignette_name"])) {
						if ($vign_index) {
							$this->update_explnum_vignette($vign_index);
						} else {
							if($this->params["maj_vignette"] && $this->infos_docnum["contenu_vignette"]) {
								$contenu_vignette = $this->infos_docnum["contenu_vignette"];
							} else {
								$contenu_vignette = construire_vignette("", "", $this->infos_docnum["url"]);
							}
							if ($contenu_vignette) {
								$this->update_explnum_vignette($contenu_vignette);
							}
						}
					}
				} elseif (! $mime_vign && (!isset($this->params["conservervignette"]) || !$this->params["conservervignette"]) && (!isset($this->infos_docnum["vignette_name"]) || !$this->infos_docnum["vignette_name"]) && $this->infos_docnum["url"]) { // Si pas d'indexation et que je ne force pas la vignette en fonction du mimetype et si j'ai une url
					if($this->params["maj_vignette"] && $this->infos_docnum["contenu_vignette"]) {
						$contenu_vignette = $this->infos_docnum["contenu_vignette"];
					} else {
						$contenu_vignette = construire_vignette("", "", $this->infos_docnum["url"]);
					}
					if ($contenu_vignette) {
						$this->update_explnum_vignette($contenu_vignette);
					}
				}

				/**
				 * Publication d'un évenement après la mise à jour
				 */
				$evt_handler = events_handler::get_instance();
				$event = new event_explnum("explnum", "after_update");
				$event->set_explnum($this);
				$evt_handler->send($event);
				if($event->get_error_message()){
				    print $event->get_error_message();
				    return ;
				}
				// on reaffiche l'ISBD
				if ($with_print) {
					print "<div class='row'><div class='msg-perio'>" . $msg['maj_encours'] . "</div></div>";
				}
				$id_form = md5(microtime());
				if (pmb_mysql_error()) {
					if ($with_print) {
						echo "MySQL error : " . pmb_mysql_error();
						print "
							<form class='form-" . $current_module . "' name=\"dummy\" method=\"post\" action=\"" . $this->params["retour"] . "\" >
								<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\">
								</form>";
						print "</div>";
					}
					exit();
				}
				if ($with_print) {
					print "
					<form class='form-" . $current_module . "' name=\"dummy\" method=\"post\" action=\"" . $this->params["retour"] . "\" style=\"display:none\">
						<input type=\"hidden\" name=\"id_form\" value=\"" . $id_form . "\">
						</form>";
					print "<script type=\"text/javascript\">document.dummy.submit();</script>";
				}
			} else {
				eval("\$bid=\"" . $msg['explnum_erreurupload'] . "\";");
				if ($with_print) {
					print "<div class='row'><div class='msg-perio'>" . $bid . "</div></div>";
					print "
					<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"" . $this->params["retour"] . "\" >
						<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\">
					</form>";
				}
			}
			if ($with_print) {
				print "</div>";
			}
		}

		/*
		 * Indexation du document
		 */
		public function indexer_docnum() {
			global $scanned_texte, $ck_index;

			if (! $this->explnum_id && $ck_index) {
				$id_explnum = $this->explnum_id;
				$indexation = new indexation_docnum($id_explnum, $scanned_texte);
				$indexation->indexer();
			} elseif ($this->explnum_id && $ck_index) {
				$indexation = new indexation_docnum($this->explnum_id, $scanned_texte);
				$indexation->indexer();
			} elseif ($this->explnum_id && ! $ck_index && ($this->explnum_index_sew != '' || $this->explnum_index_wew != '')) {
				$indexation = new indexation_docnum($this->explnum_id);
				$indexation->desindexer();
			}
			return isset($indexation) ? $indexation->vignette : '';
		}

		/**
		 * Segmentation du document
		 */
		protected function diarization_docnum() {
			global $ck_diarization;

			if (in_array($this->infos_docnum['mime'], array(
					"audio/mpeg",
					"audio/ogg",
					"video/mp4",
					"video/webm"
			))) {
				if ($ck_diarization) {
					$diarization = new diarization_docnum($this);
					$diarization->diarize();
				}
			}
		}

		public static function clean_explnum_file_name($filename){
			return file_uploader::clean_file_name($filename);
		}

		/*
		 * Analyse du document
		 */
		public function analyser_docnum() {
			global $path, $id_rep, $up_place, $base_path;
			$is_upload = false;

			if (! $this->infos_docnum["nom"]) {
			    if ($this->infos_docnum["userfile_name"])
			        $this->infos_docnum["nom"] = $this->infos_docnum["userfile_name"];
				elseif ($this->infos_docnum["url"])
					$this->infos_docnum["nom"] = $this->infos_docnum["url"];
				else
					$this->infos_docnum["nom"] = "-x-x-x-x-";
			}

			$path = stripslashes($path);
			$upfolder = new upload_folder($id_rep);
			$chemin = '';
			$contenu = '';
			if ($this->infos_docnum["fic"]) {
				$chemin_hasher = "";
				$this->infos_docnum["userfile_name"] = static::clean_explnum_file_name($this->infos_docnum["userfile_name"]);
				if (($up_place && $path != '')) {
					if ($upfolder->isHashing()) {
						$rep = $upfolder->hachage($this->infos_docnum["userfile_name"]);
						@mkdir($rep);
						$chemin_hasher = $upfolder->formate_path_to_nom($rep);
						$file_name = $rep . $this->infos_docnum["userfile_name"];
					} else {
						$file_name = $upfolder->formate_nom_to_path($path) . $this->infos_docnum["userfile_name"];
					}

					$chemin = $upfolder->formate_path_to_save($chemin_hasher ? $chemin_hasher : $path);
					$file_name = $upfolder->encoder_chaine($file_name);

					$nom_tmp = $this->infos_docnum["userfile_name"];
					$continue = true;
					$compte = 1;
					do {
						$query = "select explnum_notice,explnum_id,explnum_bulletin from explnum where explnum_nomfichier = '" . addslashes($nom_tmp) . "' AND explnum_repertoire='" . $id_rep . "' AND explnum_path='" . addslashes($chemin) . "'";
						$result = pmb_mysql_query($query);
						if (pmb_mysql_num_rows($result) && ((pmb_mysql_result($result, 0, 0) != $this->infos_docnum["notice"]) || (pmb_mysql_result($result, 0, 2) != $this->infos_docnum["bull"]))) { // Si j'ai déjà un document numérique avec ce fichier pour une autre notice je dois le renommer pour ne pas perdre l'ancien
							if (preg_match("/^(.+)(\..+)$/i", $this->infos_docnum["userfile_name"], $matches)) {
								$nom_tmp = $matches[1] . "_" . $compte . $matches[2];
							} else {
								$nom_tmp = $this->infos_docnum["userfile_name"] . "_" . $compte;
							}
							$compte++;
						} else {
							if (pmb_mysql_num_rows($result) && (! $this->explnum_id || ($this->explnum_id != pmb_mysql_result($result, 0, 1)))) { // J'ai déjà ce fichier pour cette notice et je ne suis pas en modification
							                                                                                                                      // Je dois enlever l'ancien document numérique pour ne pas l'avoir en double
								$old_docnum = new explnum(pmb_mysql_result($result, 0, 1));
								$old_docnum->delete();
							} elseif (pmb_mysql_num_rows($result)) {
							}
							$continue = false;
						}
					} while ( $continue );
					if ($compte != 1) {
						$this->infos_docnum["userfile_name"] = $nom_tmp;
						if ($upfolder->isHashing()) {
							$file_name = $rep . $this->infos_docnum["userfile_name"];
						} else {
							$file_name = $upfolder->formate_nom_to_path($path) . $this->infos_docnum["userfile_name"];
						}
						$file_name = $upfolder->encoder_chaine($file_name);
					}
					if (copy($base_path . '/temp/' . $this->infos_docnum["userfile_moved"], $file_name)) {
					    unlink($base_path . '/temp/' . $this->infos_docnum["userfile_moved"]);
					}
					$is_upload = true;
				} else
					$file_name = $base_path . '/temp/' . $this->infos_docnum["userfile_moved"];
				$fp = fopen($file_name, "r");
				if ($fp !== false) {
					$size = filesize($file_name);
					if($size > 0) {
						$contenu = fread($fp, $size);
						if (! $fp || $contenu == "") {
							if (!isset($this->params["erreur"])) {
								$this->params["erreur"] = 0;
							}
							$this->params["erreur"]++;
						}
					}
    				fclose($fp);
				}
			}

			// Dans le cas d'une modification, on regarde si il y a eu un déplacement du stockage
			if ($this->explnum_id) {
				if ($this->isEnBase() && ($up_place && $path != '')) {
					$new_path = $this->remove_from_base($path, $upfolder, $id_rep);
					$contenu = "";
					if (! $upfolder->isHashing()) {
						$chemin = $upfolder->formate_path_to_save($path);
					} else
						$chemin = $upfolder->formate_path_to_save($upfolder->formate_path_to_nom($new_path));
					$this->params["maj_data"] = true;
				} elseif ($this->isEnUpload() && (! $up_place)) {
					$contenu = $this->remove_from_upload();
					$id_rep = 0;
					$path = "";
					$this->params["maj_data"] = true;
				} elseif ($this->isEnUpload() && ($up_place && $path)) {
					$contenu = "";
					$chemin = $this->change_rep_upload($upfolder, $upfolder->formate_nom_to_path($path));
					if (! $upfolder->isHashing()) {
						$chemin = $upfolder->formate_path_to_save($upfolder->formate_path_to_nom($path));
					} else
						$chemin = $upfolder->formate_path_to_save($upfolder->formate_path_to_nom($chemin));
					$this->params["maj_data"] = true;
				}
			}

			$this->params["is_upload"] = $is_upload;
			$this->infos_docnum["contenu"] = $contenu;
			$this->infos_docnum["path"] = $chemin;
			if ($this->infos_docnum["userfile_name"] && $this->infos_docnum["userfile_moved"] && file_exists($base_path . '/temp/' . $this->infos_docnum["userfile_moved"]))
				unlink($base_path . '/temp/' . $this->infos_docnum["userfile_moved"]);
			if ($this->infos_docnum["vignette_name"])
				unlink($base_path . '/temp/' . $this->infos_docnum["vignette_moved"]);
			if ($this->explnum_id && $this->infos_docnum["userfile_name"] && ($this->infos_docnum["userfile_name"] != $this->explnum_nomfichier)) {
				$up = new upload_folder($this->explnum_repertoire);
				$old_file = str_replace('//', '/', $this->explnum_rep_path . $this->explnum_path . $this->explnum_nomfichier);
				if (file_exists($old_file))
					unlink($up->encoder_chaine($old_file));
			}
		}

		/*
		 * Récupère les informations de l'exemplaire à ajouter à la la notice
		 */
		public function recuperer_explnum($f_notice, $f_bulletin, $f_nom, $f_url, $retour, $conservervignette = 0, $f_statut_chk = 0, $f_explnum_statut = 1, $forcage = 0, $f_url_vignette='') {
			global $scanned_image, $scanned_image_ext, $base_path;
			global $f_new_name, $mime_vign;

			$maj_mimetype = 0;
			$maj_data = 0;
			$maj_vignette = 0;
			$fic = 0;

			$this->infos_docnum = array();
			$this->params = array();

			create_tableau_mimetype();

			$erreur = 0;
			$userfile_name = $_FILES['f_fichier']['name'];
			$userfile_temp = $_FILES['f_fichier']['tmp_name'];
			$userfile_moved = basename($userfile_temp);

			$vignette_name = $_FILES['f_vignette']['name'];
			$vignette_temp = $_FILES['f_vignette']['tmp_name'];
			$vignette_moved = basename($vignette_temp);

			$userfile_name = preg_replace("/ |'|\\|\"|\//m", "_", $userfile_name);
			$vignette_name = preg_replace("/ |'|\\|\"|\//m", "_", $vignette_name);
			$contenu_vignette = "";
			$userfile_ext = '';
			if ($userfile_name) {
				$userfile_ext = extension_fichier($userfile_name);
			}
			if ($this->explnum_id) {
				// modification
				// si $userfile_name est vide on ne fera pas la maj du data
				if (($scanned_image) || ($userfile_name)) {
					// Avant tout, y-a-t-il une image extérieure ?
					if ($scanned_image) {
						// Si oui !
						$tmpid = str_replace(" ", "_", microtime());
						$fp = @fopen($base_path . "/temp/scanned_$tmpid." . $scanned_image_ext, "w+");
						if ($fp) {
							fwrite($fp, base64_decode($scanned_image));
							$nf = 1;
							$part_name = "scanned_image_" . $nf;
							global ${$part_name};
							while ( ${$part_name} ) {
								fwrite($fp, base64_decode(${$part_name}));
								$nf++;
								$part_name = "scanned_image_" . $nf;
								global ${$part_name};
							}
							fclose($fp);
							$fic = 1;
							$maj_data = 1;
							$userfile_name = "scanned_$tmpid." . $scanned_image_ext;
							$userfile_ext = $scanned_image_ext;
							$userfile_moved = $userfile_name;
							$f_url = "";
						} else
							$erreur++;
					} else if ($userfile_name) {
						$move_return = true;
						if (!$forcage) {
							$move_return = move_uploaded_file($userfile_temp, $base_path . '/temp/' . $userfile_moved);
						}
						if ($move_return) {
							$fic = 1;
							$f_url = "";
							$maj_data = 1;
							move_uploaded_file($vignette_temp, $base_path . '/temp/' . $vignette_moved);
						} else {
							$erreur++;
						}
					}
					$mimetype = trouve_mimetype($userfile_moved, $userfile_ext);
					if (! $mimetype)
						$mimetype = "application/data";
					$maj_mimetype = 1;
					if (! $conservervignette && ! $mime_vign) {
						if (! $f_url_vignette) {
							$contenu_vignette = construire_vignette($vignette_moved, $userfile_moved);
						} else {
							$contenu_vignette = construire_vignette('', '', $f_url_vignette);
						}
					}
					$maj_vignette = 1;
				} else {
					if ($vignette_name) {
						move_uploaded_file($vignette_temp, $base_path . '/temp/' . $vignette_moved);
						if (! $conservervignette && ! $mime_vign)
							$contenu_vignette = construire_vignette($vignette_moved, $userfile_moved);
						$maj_vignette = 1;
					} elseif($f_url_vignette) {
						if (! $conservervignette && ! $mime_vign)
							$contenu_vignette = construire_vignette('', '', $f_url_vignette);
						$maj_vignette = 1;
					}
					if ($f_url) {
						$mimetype = "URL";
						$maj_mimetype = 1;
						move_uploaded_file($vignette_temp, $base_path . '/temp/' . $vignette_moved);
						if (!$maj_vignette && ! $conservervignette && ! $mime_vign)
							$contenu_vignette = construire_vignette($vignette_moved, $userfile_moved);
						$maj_vignette = 1;
						$contenu = "";
						$maj_data = 1;
					}
					//Option "Conserver la vignette existante ?" décochée
					if(!$conservervignette && !$maj_vignette) {
						$contenu_vignette = "";
						$maj_vignette = 1;
					}
				}
			} else {
				// creation
				// Y-a-t-il une image exterieure ?
				if ($scanned_image) {
					// Si oui !
					$tmpid = str_replace(" ", "_", microtime());
					$fp = @fopen($base_path . "/temp/scanned_$tmpid." . $scanned_image_ext, "w+");
					if ($fp) {
						fwrite($fp, base64_decode($scanned_image));
						$nf = 1;
						$part_name = "scanned_image_" . $nf;
						global ${$part_name};
						while ( ${$part_name} ) {
							fwrite($fp, base64_decode(${$part_name}));
							$nf++;
							$part_name = "scanned_image_" . $nf;
							global ${$part_name};
						}
						fclose($fp);
						$fic = 1;
						$maj_data = 1;
						$userfile_name = "scanned_$tmpid." . $scanned_image_ext;
						$userfile_ext = $scanned_image_ext;
						$userfile_moved = $userfile_name;
						$f_url = "";
					} else
						$erreur++;
				} else {
					$move_return = true;
					if (!$forcage) {
						$move_return = move_uploaded_file($userfile_temp, $base_path . '/temp/' . $userfile_moved);
					}
					if ($move_return) {
						$fic = 1;
						$f_url = "";
						$maj_data = 1;
					} elseif (! $f_url) {
						$erreur++;
					}
				}

				if (! $f_url && ! $fic)
					$erreur++;
				if ($f_url) {
					$mimetype = "URL";
				} else {
					$mimetype = trouve_mimetype($userfile_moved, $userfile_ext);
					if (! $mimetype)
						$mimetype = "application/data";
				}
				$maj_mimetype = 1;
				if (!empty($vignette_moved)) {
    				move_uploaded_file($vignette_temp, $base_path . '/temp/' . $vignette_moved);
				}
				if (! $mime_vign) {
					if (! $f_url_vignette) {
						$contenu_vignette = construire_vignette($vignette_moved, $userfile_moved);
					} else {
						$contenu_vignette = construire_vignette('', '', $f_url_vignette);
					}
				}
				$maj_vignette = 1;
			}

			if ($mime_vign && ! $conservervignette) {
				global $prefix_url_image;
				if ($prefix_url_image)
					$tmpprefix_url_image = $prefix_url_image;
				else
					$tmpprefix_url_image = "./";
				$vignette = $tmpprefix_url_image . "images/mimetype/" . icone_mimetype($mime_vign, "");
				$fp = fopen($vignette, "r");
				if ($fp)
					$contenu_vignette = fread($fp, filesize($vignette));
				if ($contenu_vignette)
					$maj_vignette = 1;
			}
			// Initialisation des tableaux d'infos
			$this->infos_docnum["mime"] = (($this->explnum_id && ! $maj_mimetype) ? $this->explnum_mimetype : $mimetype);
			$this->infos_docnum["nom"] = $f_nom;
			$this->infos_docnum["notice"] = intval($f_notice);
			$this->infos_docnum["bull"] = intval($f_bulletin);
			$this->infos_docnum["url"] = $f_url;
			$this->infos_docnum["fic"] = $fic;
			$this->infos_docnum["contenu_vignette"] = $contenu_vignette;
			$this->infos_docnum["userfile_name"] = (($this->explnum_id && ! $userfile_name) ? $this->explnum_nomfichier : ($f_new_name ? $f_new_name : $userfile_name));
			$this->infos_docnum["userfile_ext"] = (($this->explnum_id && ! $userfile_ext) ? $this->explnum_extfichier : $userfile_ext);
			$this->infos_docnum["userfile_moved"] = $userfile_moved;
			$this->infos_docnum["vignette_name"] = $vignette_name;
			$this->infos_docnum["vignette_moved"] = $vignette_moved;

			$this->params["error"] = $erreur;
			$this->params["maj_mimetype"] = $maj_mimetype;
			$this->params["maj_data"] = $maj_data;
			$this->params["maj_vignette"] = $maj_vignette;
			$this->params["retour"] = $retour;
			$this->params["conservervignette"] = $conservervignette;
			$this->params["statut"] = $f_statut_chk;
			$this->params["explnum_statut"] = $f_explnum_statut;
		}

		/*
		 * Teste si l'exemplaire est stocké en base
		 */
		public function isEnBase() {
			if ($this->explnum_data && ! $this->explnum_repertoire && ! $this->explnum_path)
				return true;
			return false;
		}

		/*
		 * Teste si l'exemplaire est stocké sur le disque
		 */
		public function isEnUpload() {
			if ($this->explnum_repertoire && $this->explnum_path)
				return true;
			return false;
		}

		/*
		 * Teste si l'exemplaire est stocké sous forme d'URL
		 */
		public function isURL() {
			if ($this->explnum_url)
				return true;
			return false;
		}

		/*
		 * Retire l'exemplaire de la base pour le mettre en upload
		 */
		public function remove_from_base($chemin, $upfolder, $id_rep) {
			$content = $this->explnum_data;

			$chemin_hasher = "";
			if ($upfolder->isHashing()) {
				$rep = $upfolder->hachage($this->explnum_nomfichier);
				@mkdir($rep);
				$chemin_hasher = $upfolder->formate_path_to_nom($rep);
			}
			$chemin_query = $upfolder->formate_path_to_save($chemin_hasher ? $chemin_hasher : $chemin);

			$nom_tmp = $this->explnum_nomfichier;
			$continue = true;
			$compte = 1;
			do {
				$query = "select explnum_notice,explnum_id,explnum_bulletin from explnum where explnum_nomfichier = '" . addslashes($nom_tmp) . "' AND explnum_repertoire='" . $id_rep . "' AND explnum_path='" . addslashes($chemin_query) . "' AND explnum_id<>" . $this->explnum_id;
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) { // Si j'ai déjà un document numérique avec ce fichier pour une autre notice je dois le renommer pour ne pas perdre l'ancien
					if (preg_match("/^(.+)(\..+)$/i", $this->explnum_nomfichier, $matches)) {
						$nom_tmp = $matches[1] . "_" . $compte . $matches[2];
					} else {
						$nom_tmp = $this->explnum_nomfichier . "_" . $compte;
					}
					$compte++;
				} else {
					$continue = false;
				}
			} while ( $continue );

			// on renomme aussi le champ dans la table
			$this->infos_docnum["userfile_name"] = $nom_tmp;

			$new_path = "";
			if ($upfolder->isHashing()) {
				$hashname = $upfolder->hachage($nom_tmp);
				$file_path = $upfolder->encoder_chaine($hashname . $nom_tmp);
				if (! is_dir($hashname))
					mkdir($hashname);
				$new_path = $upfolder->encoder_chaine($hashname);
			} else {
				$file_path = $upfolder->encoder_chaine($upfolder->formate_nom_to_path($chemin) . $nom_tmp);
			}
			file_put_contents($file_path, $content);

			return $new_path;
		}

		/*
		 * Supprime le fichier uploadé pour le mettre en base
		 */
		public function remove_from_upload() {
			$up = new upload_folder($this->explnum_repertoire);
			$contenu = '';
			$path = $up->repertoire_path . $this->explnum_path . $this->explnum_nomfichier;
			$path = str_replace('//', '/', $path);
			$path = $up->encoder_chaine($path);

			if (is_file($path)) {
				$contenu = file_get_contents($path);
				unlink($path);
			}
			return $contenu;
		}

		/*
		 * Permet le changement de répertoire d'upload
		 */
		public function change_rep_upload($rep, $new_path) {
			$nom_fich = ($this->explnum_nomfichier != "" ? $this->explnum_nomfichier : $this->explnum_nom);
			$old_path = $this->explnum_rep_path . $this->explnum_path;
			$old_path = str_replace('//', '/', $old_path);

			if ($rep->isHashing()) {
				$new_rep = $rep->hachage($nom_fich);
				if (! is_dir($new_rep))
					mkdir($new_rep);
			} else {
				$new_rep = $new_path;
			}

			$up = new upload_folder($this->explnum_repertoire);
			$old_path = $up->formate_nom_to_path($old_path);
			$ancien_fichier = $up->encoder_chaine($old_path . $nom_fich);
			$nouveau_fichier = $rep->encoder_chaine($new_rep . $nom_fich);

			if (! file_exists($nouveau_fichier) && ($nouveau_fichier != $ancien_fichier)) {
			    if (copy($ancien_fichier, $nouveau_fichier)) {
			        unlink($ancien_fichier);
			    }
				$nom_rep = $new_path;
			}

			return (isset($nom_rep) && $nom_rep ? $nom_rep : $new_rep);
		}

		/*
		 * Copie dans un répertoire
		 */
		public function copy_to($new_dir_id = 0, $rename = false) {
			$ret = false;
			$old_dir_id = $this->explnum_repertoire;

			if ($old_dir_id && $new_dir_id) {

				$old_dir = new upload_folder($old_dir_id);
				$new_dir = new upload_folder($new_dir_id);

				$old_file_name = ($this->explnum_nomfichier != '' ? $this->explnum_nomfichier : $this->explnum_nom);
				if ($rename) {
					$new_file_name = $this->rename();
				} else {
					$new_file_name = $old_file_name;
				}

				$old_path = $old_dir->repertoire_path . $this->explnum_path;
				$old_path = str_replace('//', '/', $old_path);

				if ($new_dir->isHashing()) {
					$new_sub_dir = $new_dir->hachage($new_file_name);
					if (! is_dir($new_sub_dir))
						mkdir($new_sub_dir);
					$new_path = $new_dir->repertoire_path . $new_sub_dir;
				} else {
					$new_path = $new_dir->repertoire_path;
				}
				$new_path = str_replace('//', '/', $new_path);

				$old_file = $old_dir->encoder_chaine($old_path . $old_file_name);
				$new_file = $new_dir->encoder_chaine($new_path . $new_file_name);
				if (file_exists($new_file)) {
					$new_file_name = $this->rename();
					$new_file = $new_dir->encoder_chaine($new_path . $new_file_name);
				}
				if (! file_exists($new_file)) {
					if (copy($old_file, $new_file)) {
						$ret = true;
					}
				}
			} else if ($new_dir_id) {

				$new_dir = new upload_folder($new_dir_id);

				$old_file_name = ($this->explnum_nomfichier != '' ? $this->explnum_nomfichier : $this->explnum_nom);
				if ($rename) {
					$new_file_name = $this->rename();
				} else {
					$new_file_name = $old_file_name;
				}

				if ($new_dir->isHashing()) {
					$new_sub_dir = $new_dir->hachage($new_file_name);
					if (! is_dir($new_sub_dir))
						mkdir($new_sub_dir);
					$new_path = $new_dir->repertoire_path . $new_sub_dir;
				} else {
					$new_path = $new_dir->repertoire_path;
				}
				$new_path = str_replace('//', '/', $new_path);
				$new_file = $new_dir->encoder_chaine($new_path . $new_file_name);
				if (file_exists($new_file)) {
					$new_file_name = $this->rename();
					$new_file = $new_dir->encoder_chaine($new_path . $new_file_name);
				}
				if (! file_exists($new_file)) {
					if (file_put_contents($new_file, $this->explnum_data)) {
						$ret = true;
					}
				}
			}
			return ($ret) ? $new_file : $ret;
		}

		// fournit un nom de fichier unique
		public function rename() {
			$new_file_name = 'file_' . md5(microtime()) . (($this->explnum_extfichier) ? '.' . $this->explnum_extfichier : '');
			return $new_file_name;
		}

		public static function static_rename($ext = '') {
			$new_file_name = 'file_' . md5(microtime()) . (($ext) ? '.' . $ext : '');
			return $new_file_name;
		}

		/*
		 * Fonction qui dézippe dans le bon répertoire
		 */
		public function unzip($filename) {
			global $up_place, $path, $id_rep;

			if ($up_place && $path != '') {
				$this->unzipped_files = file_uploader::unzip($filename, $up_place, $path, $id_rep);
			}
		}

		/*
		 * Gestion de l'ajout multifichier
		 */
		public function analyse_multifile() {
			global $id_rep;

			create_tableau_mimetype();
			$repup = new upload_folder($id_rep);
			if (is_array($this->unzipped_files) && !empty($this->unzipped_files)) {
			    $nb_unzipped_files = count($this->unzipped_files);
			    for ($i = 0; $i < $nb_unzipped_files; $i++) {
					$this->infos_docnum['userfile_name'] = $this->unzipped_files[$i]['nom'];
					if ($repup->isHashing()) {
						$hashname = $repup->hachage($this->infos_docnum['userfile_name']);
						$chemin = $repup->formate_path_to_save($repup->formate_path_to_nom($hashname));
					} else
						$chemin = $repup->formate_path_to_save($this->unzipped_files[$i]["chemin"]);

					if ($this->unzipped_files[$i]['base']) {
						$this->infos_docnum['contenu'] = file_get_contents($this->unzipped_files[$i]['chemin']);
						$this->infos_docnum['path'] = '';
					} else {
						$this->infos_docnum['contenu'] = '';
						$this->infos_docnum['path'] = $chemin;
					}
					$ext = '';
					if ($this->infos_docnum['userfile_name']) {
						$ext = extension_fichier($this->infos_docnum['userfile_name']);
						$this->infos_docnum['userfile_ext'] = $ext;
					}

					if ($this->unzipped_files[$i]['base']) {
						$this->infos_docnum['contenu_vignette'] = construire_vignette("", $this->infos_docnum['userfile_name']);
					} else {
						if ($repup->isHashing())
							$this->infos_docnum['contenu_vignette'] = construire_vignette("", $repup->encoder_chaine($hashname . $this->infos_docnum['userfile_name']));
						else
							$this->infos_docnum['contenu_vignette'] = construire_vignette("", $repup->encoder_chaine($repup->formate_nom_to_path($this->unzipped_files[$i]['chemin']) . $this->infos_docnum['userfile_name']));
					}
					$mimetype = trouve_mimetype($this->unzipped_files[$i]['chemin'], $this->infos_docnum['userfile_ext']);
					if (! $mimetype)
						$mimetype = "application/data";
					$this->infos_docnum['mime'] = $mimetype;

					if ($this->unzipped_files[$i]['base']) {
						unlink($this->unzipped_files[$i]['chemin']);
					}
					if ($mimetype == 'URL') {
						$this->infos_docnum['url'] = $this->unzipped_files[$i]['nom'];
						$this->infos_docnum['nom'] = '';
					} else {
						$this->infos_docnum['nom'] = $this->unzipped_files[$i]['nom'];
						$this->infos_docnum['url'] = '';
					}
					$this->update();
					$this->explnum_id = 0;
				}
			}
		}

		public function get_file_from_temp($filename, $name, $upload_place) {
			global $ck_index;
			global $id_rep, $up_place;
			global $pmb_indexation_docnum_default;

			$up_place = $upload_place;

			create_tableau_mimetype();
			if (!isset($ck_index)) {
			    $ck_index = $pmb_indexation_docnum_default;
			}
			// Initialisation des tableaux d'infos
			$this->infos_docnum = $this->params = array();
			$this->infos_docnum["mime"] = trouve_mimetype($filename, extension_fichier($name));
			$this->infos_docnum["nom"] = substr($name, 0, strrpos($name, "."));
	   		if (! $this->infos_docnum["nom"]) {
	   		    $this->infos_docnum["nom"] = $name;
	   		}
			$this->infos_docnum["notice"] = $this->explnum_notice;
			$this->infos_docnum["bull"] = $this->explnum_bulletin;
			$this->infos_docnum["url"] = "";
			$this->infos_docnum["fic"] = false;
            if ($this->infos_docnum["mime"] != 'text/plain') {
				$this->infos_docnum["contenu_vignette"] = construire_vignette('', substr($filename, strrpos($filename, "/")));
            }
			$this->infos_docnum["userfile_name"] = static::clean_explnum_file_name($name);
			$this->infos_docnum["userfile_ext"] = extension_fichier($name);

			if ($up_place && $id_rep != 0) {
				$upfolder = new upload_folder($id_rep);
				$chemin_hasher = "/";
				if ($upfolder->isHashing()) {
					$rep = $upfolder->hachage($this->infos_docnum["userfile_name"]);
					@mkdir($rep);
					$chemin_hasher = $upfolder->formate_path_to_nom($rep);
					$file_name = $rep . $this->infos_docnum["userfile_name"];
					$chemin = $upfolder->formate_path_to_save($chemin_hasher);
				} else {
					$file_name = $upfolder->get_path($this->infos_docnum["userfile_name"]) . $this->infos_docnum["userfile_name"];
					$chemin = $upfolder->formate_path_to_save("/");
				}
				$this->infos_docnum["path"] = $chemin;
				$file_name = $upfolder->encoder_chaine($file_name);
				if (! $this->explnum_nomfichier) { // Si je suis en création de fichier numérique
					$nom_tmp = $this->infos_docnum["userfile_name"];
					$continue = true;
					$compte = 1;
					do {
						$query = "select explnum_notice,explnum_id from explnum where explnum_nomfichier = '" . addslashes($nom_tmp) . "' AND explnum_repertoire='" . $id_rep . "' AND explnum_path='" . addslashes($this->infos_docnum["path"]) . "'";
						$result = pmb_mysql_query($query);
						if (pmb_mysql_num_rows($result) && (pmb_mysql_result($result, 0, 0) != $this->infos_docnum["notice"])) { // Si j'ai déjà un document numérique avec ce fichier pour une autre notice je dois le renommer pour ne pas perdre l'ancien
							if (preg_match("/^(.+)(\..+)$/i", $this->infos_docnum["userfile_name"], $matches)) {
								$nom_tmp = $matches[1] . "_" . $compte . $matches[2];
							} else {
								$nom_tmp = $this->infos_docnum["userfile_name"] . "_" . $compte;
							}
							$compte++;
						} else {
							if (pmb_mysql_num_rows($result)) { // J'ai déjà ce fichier pour cette notice
							                                   // Je dois enlever l'ancien document numérique pour ne pas l'avoir en double
								$old_docnum = new explnum(pmb_mysql_result($result, 0, 1));
								$old_docnum->delete();
							} else {
							}
							$continue = false;
						}
					} while ( $continue );
					if ($compte != 1) {
						$this->infos_docnum["userfile_name"] = $nom_tmp;
						if ($upfolder->isHashing()) {
							$file_name = $rep . $this->infos_docnum["userfile_name"];
						} else {
							$file_name = $upfolder->get_path($this->infos_docnum["userfile_name"]) . $this->infos_docnum["userfile_name"];
						}
						$file_name = $upfolder->encoder_chaine($file_name);
					} else {
					}
				} else {
				}
				if (copy($filename, $file_name)) {
				    unlink($filename);
				}
				#Pour avoir une vignette si le précédent calcul n'a pas fonctionné car le fichier n'est pas dans le répertoire temp de PMB
				if (($this->infos_docnum["mime"] != 'text/plain') && (!$this->infos_docnum["contenu_vignette"])) {
				    $this->infos_docnum["contenu_vignette"] = construire_vignette('', $file_name);
				}
			} else {
				// enregistrement en base
				$this->infos_docnum["contenu"] = file_get_contents($filename);
			}

			$this->params["maj_mimetype"] = true;
			$this->params["maj_data"] = true;
			$this->params["maj_vignette"] = true;
		}

		public function get_file_content() {
			$data = "";
			/**
			 * Publication d'un évenement avant la récupération
			 */
			$evt_handler = events_handler::get_instance();
			$event = new event_explnum("explnum", "before_get_file_content");
			$event->set_explnum($this);
			$evt_handler->send($event);

			if (! $this->explnum_id) {
				exit();
			}

			if ($this->explnum_data && ($this->explnum_data != 'NULL')) {
				$data = $this->explnum_data;
			} else if ($this->explnum_path) {
				$up = new upload_folder($this->explnum_repertoire);
				$path = str_replace("//", "/", $this->explnum_rep_path . $this->explnum_path . $this->explnum_nomfichier);
				$path = $up->encoder_chaine($path);
				if (file_exists($path)) {
					$fo = fopen($path, 'rb');
					if ($fo) {
						while ( ! feof($fo) ) {
							$data .= fread($fo, 4096);
						}
						fclose($fo);
					}
				}
			}

			return $data;
		}

		public function get_is_file() {
			$path = '';
			if (! $this->explnum_id) {
				return '';
			}
			if ($this->explnum_data && ($this->explnum_data != 'NULL')) {
				return '';
			} else if ($this->explnum_path) {
				$up = new upload_folder($this->explnum_repertoire);
				$path = str_replace("//", "/", $this->explnum_rep_path . $this->explnum_path . $this->explnum_nomfichier);
				$path = $up->encoder_chaine($path);
				if (file_exists($path)) {
					return $path;
				}
			}
			return '';
		}

		public function get_file_name() {
		    if ($this->explnum_nomfichier && pmb_substr($this->explnum_nomfichier, 0, 5) != 'file_') {
		        return static::clean_explnum_file_name($this->explnum_nomfichier);
		    }
		    if ($this->explnum_extfichier) {
		        $nomfichier = static::clean_explnum_file_name($this->explnum_nom);
		        if ($nomfichier) {
		            if (! pmb_preg_match("/\." . $this->explnum_extfichier . "$/", $nomfichier)) {
		                $nomfichier .= "." . $this->explnum_extfichier;
		            }
		            return $nomfichier;
		        } elseif ($this->explnum_nomfichier) {
		            return static::clean_explnum_file_name($this->explnum_nomfichier);
		        } else {
		            return "pmb" . $this->explnum_id . "." . $this->explnum_extfichier;
		        }
		    }
		    if ($this->explnum_nomfichier) {
		        return static::clean_explnum_file_name($this->explnum_nomfichier);
		    }
		}

		public function get_file_size($force=false) {
			$size = $this->explnum_file_size;
			if ($force || !$this->explnum_file_size) {
				if ($this->explnum_data) {
					$size = strlen($this->explnum_data);
				} elseif ($this->explnum_path) {
					$up = new upload_folder($this->explnum_repertoire);
                    $path = str_replace("//", "/", $up->repertoire_path . $this->explnum_path . $this->explnum_nomfichier);
					$path = $up->encoder_chaine($path);
					$size = filesize($path);
				}
				$this->explnum_file_size = $size;
			}
			return $size;
		}

		public function get_create_date() {
			return $this->explnum_create_date;
		}

		public function get_update_date() {
			return $this->explnum_update_date;
		}

		public function get_rights_form() {
			global $msg, $charset;
			global $gestion_acces_active, $gestion_acces_empr_docnum;
			global $gestion_acces_empr_docnum_def;

			if ($gestion_acces_active != 1)
				return '';
			$ac = new acces();

			$form = '';
			$c_form = "
		<div id='el8Child_0' class='row' movable='yes' title=\"<!-- domain_name -->\">
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette'><!-- domain_name --></label>
			</div>
			<div class='row'>
				<div class='colonne3'>" . htmlentities($msg['dom_cur_prf'], ENT_QUOTES, $charset) . "</div>
				<div class='colonne_suite'><!-- prf_rad --></div>
			</div>
			<div class='row'>
				<div class='colonne3'>" . htmlentities($msg['dom_cur_rights'], ENT_QUOTES, $charset) . "</div>
				<div class='colonne_suite'><!-- r_rad --></div>
				<div class='row'><!-- rights_tab --></div>
			</div>
		</div>";

			if ($gestion_acces_empr_docnum == 1) {

				$r_form = $c_form;
				$dom_3 = $ac->setDomain(3);
				$r_form = str_replace('<!-- domain_name -->', htmlentities($dom_3->getComment('long_name'), ENT_QUOTES, $charset), $r_form);
				if ($this->explnum_id) {

					// profil ressource
					$def_prf = $dom_3->getComment('res_prf_def_lib');
					$res_prf = $dom_3->getResourceProfile($this->explnum_id);
					$q = $dom_3->loadUsedResourceProfiles();

					// Recuperation droits generiques utilisateur
					$user_rights = $dom_3->getDomainRights(0, $res_prf);

					if ($user_rights & 2) {
						$p_sel = gen_liste($q, 'prf_id', 'prf_name', 'res_prf[3]', '', $res_prf, '0', $def_prf, '0', $def_prf);
						$p_rad = "<input type='radio' id='prf_rad_3_R' name='prf_rad[3]' value='R' ";
						if ($gestion_acces_empr_docnum_def != '1')
							$p_rad .= "checked='checked' ";
						$p_rad .= "><label for='prf_rad_3_R' >" . htmlentities($msg['dom_rad_calc'], ENT_QUOTES, $charset) . "</label></input><input type='radio' id='prf_rad_3_C' name='prf_rad[3]' value='C' ";
						if ($gestion_acces_empr_docnum_def == '1')
							$p_rad .= "checked='checked' ";
						$p_rad .= "><label for='prf_rad_3_C' >" . htmlentities($msg['dom_rad_def'], ENT_QUOTES, $charset) . " $p_sel</label></input>";
						$r_form = str_replace('<!-- prf_rad -->', $p_rad, $r_form);
					} else {
						$r_form = str_replace('<!-- prf_rad -->', htmlentities($dom_3->getResourceProfileName($res_prf), ENT_QUOTES, $charset), $r_form);
					}

					// droits/profils utilisateurs
					if ($user_rights & 1) {
						$r_rad = "<input type='radio' id='r_rad_3_R' name='r_rad[3]' value='R' ";
						if ($gestion_acces_empr_docnum_def != '1')
							$r_rad .= "checked='checked' ";
						$r_rad .= "><label for='r_rad_3_R' >" . htmlentities($msg['dom_rad_calc'], ENT_QUOTES, $charset) . "</label></input><input type='radio' id='r_rad_3_C' name='r_rad[3]' value='C' ";
						if ($gestion_acces_empr_docnum_def == '1')
							$r_rad .= "checked='checked' ";
						$r_rad .= "><label for='r_rad_3_C' >" . htmlentities($msg['dom_rad_def'], ENT_QUOTES, $charset) . "</label></input>";
						$r_form = str_replace('<!-- r_rad -->', $r_rad, $r_form);
					}

					// recuperation profils utilisateurs
					$t_u = array();
					$t_u[0] = $dom_3->getComment('user_prf_def_lib'); // niveau par defaut
					$qu = $dom_3->loadUsedUserProfiles();
					$ru = pmb_mysql_query($qu);
					if (pmb_mysql_num_rows($ru)) {
						while ( ($row = pmb_mysql_fetch_object($ru)) ) {
							$t_u[$row->prf_id] = $row->prf_name;
						}
					}

					// recuperation des controles dependants de l'utilisateur
					$t_ctl = $dom_3->getControls(0);

					// recuperation des droits
					$t_rights = $dom_3->getResourceRights($this->explnum_id);

					if (count($t_u)) {

						$h_tab = "<div class='dom_div'><table class='dom_tab'><tr>";
						foreach ( $t_u as $k => $v ) {
							$h_tab .= "<th class='dom_col'>" . htmlentities($v, ENT_QUOTES, $charset) . "</th>";
						}
						$h_tab .= "</tr><!-- rights_tab --></table></div>";

						$c_tab = '<tr>';
						foreach ( $t_u as $k => $v ) {

							$c_tab .= "<td><table style='border:1px solid;'><!-- rows --></table></td>";
							$t_rows = "";

							foreach ( $t_ctl as $k2 => $v2 ) {

								$t_rows .= "
								<tr>
									<td style='width:25px;' ><input type='checkbox' id='chk_rights_3_".$k."_".$k2."' name='chk_rights[3][" . $k . "][" . $k2 . "]' value='1' ";
								if ($t_rights[$k][$res_prf] & (pow(2, $k2 - 1))) {
									$t_rows .= "checked='checked' ";
								}
								if (($user_rights & 1) == 0) {
									$t_rows .= "disabled='disabled' /></td>
    									<td>" . htmlentities($v2, ENT_QUOTES, $charset) . "</td>
    								</tr>";
								} else {
    								$t_rows .= "/></td>
    									<td><label for='chk_rights_3_".$k."_".$k2."' >" . htmlentities($v2, ENT_QUOTES, $charset) . "</label></td>
    								</tr>";
								}

							}
							$c_tab = str_replace('<!-- rows -->', $t_rows, $c_tab);
						}
						$c_tab .= "</tr>";
					}
					$h_tab = str_replace('<!-- rights_tab -->', $c_tab, $h_tab);
					;
					$r_form = str_replace('<!-- rights_tab -->', $h_tab, $r_form);
				} else {
					$r_form = str_replace('<!-- prf_rad -->', htmlentities($msg['dom_prf_unknown'], ENT_QUOTES, $charset), $r_form);
					$r_form = str_replace('<!-- r_rad -->', htmlentities($msg['dom_rights_unknown'], ENT_QUOTES, $charset), $r_form);
				}
				$form .= $r_form;
			}
			return $form;
		}

		public function gen_signature($full_path = '', $explnum_data = '', $explnum_path = '', $explnum_repertoire = 0, $explnum_nomfichier = '') {
			$this->explnum_signature = '';
			if (!$explnum_data) $explnum_data = $this->explnum_data;
			if (!$explnum_path) $explnum_path = $this->explnum_path;
			if (!$explnum_repertoire) $explnum_repertoire = $this->explnum_repertoire;
			if (!$explnum_nomfichier) $explnum_nomfichier = $this->explnum_nomfichier;

			if ($explnum_data && ($explnum_data != 'NULL')) {
				$this->explnum_signature = md5($explnum_data);
			} else if ($full_path) {
				$fo = @fopen($full_path, 'r');
				if ($fo) {
					$this->explnum_signature = md5_file($full_path);
					fclose($fo);
				}
			} else if ($explnum_path) {
				$up = new upload_folder($explnum_repertoire);
				$path = str_replace("//", "/", $up->repertoire_path . $explnum_path . $explnum_nomfichier);
				$path = $up->encoder_chaine($path);
				if (file_exists($path)) {
					$this->explnum_signature = md5_file($path);
				}
			}
			return $this->explnum_signature;
		}

		public function get_statut_libelle() {
			$statut_libelle = '';

			$res = pmb_mysql_query("SELECT gestion_libelle FROM explnum_statut WHERE id_explnum_statut=".$this->explnum_docnum_statut);
			if ($res && pmb_mysql_num_rows($res)) {
				$statut_libelle = pmb_mysql_result($res, 0, 0);
			}
			return $statut_libelle;
		}

		public static function get_default_upload_directory(){
		    $query = "select repertoire_id from upload_repertoire
                     join users on users.deflt_upload_repertoire=upload_repertoire.repertoire_id
                     and users.username='" . SESSlogin . "'";
		    $result = pmb_mysql_query($query);
		    if (pmb_mysql_num_rows($result)) {
		        return pmb_mysql_result($result, 0, 0);
		    }
		    return false;
		}

		public static function get_drop_zone($entity_id, $entity_type, $bul_id = ''){
		    global $explnum_drop_zone;
		    $explnum_drop_zone_filled = str_replace('!!entity_id!!', $entity_id, $explnum_drop_zone);
		    $explnum_drop_zone_filled = str_replace('!!entity_type!!', $entity_type, $explnum_drop_zone_filled);
		    $explnum_drop_zone_filled = str_replace('!!bul_id!!', $bul_id, $explnum_drop_zone_filled);

		    return $explnum_drop_zone_filled;
		}

		public static function create_doc_from_file(){
			global $fnc;
		    global $record_id;
		    global $bulletin_id;
		    global $id_rep;
		    global $pmb_indexation_docnum_default;
		    global $pmb_explnum_controle_doublons;

		    $protocol = $_SERVER["SERVER_PROTOCOL"];

		    $file = file_uploader::get_file();

		    $file->name = preg_replace("/ |'|\\|\"|\//m", "_", $file->name);

		    if(is_object($file)) {
                $fh = fopen("php://input", "r");
                $th = fopen("./temp/".$file->filename, "w");
                $part = 5 * 1024 * 1024;

                while (!feof($fh)) {
                	fwrite($th, fread($fh, $part));
                }

                fclose($fh);
                fclose($th);
                $limit = file_uploader::get_limit();
                if (filesize("./temp/".$file->filename) > $limit) {
		        	header($protocol.' 403 Forbidden');
		        	return false;
                }
                if (filesize("./temp/".$file->filename) !== false) {
		            header($protocol.' 201 Created');
	                if($bulletin_id){
	                	$bulletin_id = intval($bulletin_id);
	                    $explnum = new explnum(0,0,$bulletin_id);
	                }else if($record_id){
	                	$record_id = intval($record_id);
	                    $explnum = new explnum(0,$record_id,0);
	                }else{
	                    return false;
	                }
	                $id_rep = explnum::get_default_upload_directory();
		            $explnum->get_file_from_temp("./temp/".$file->filename, $file->name, true);
		            if($pmb_indexation_docnum_default){
		                global $ck_index;
                        $ck_index = 1;
		            }
		            $doublons = array();
		            if(!$explnum->explnum_id && $pmb_explnum_controle_doublons) {
		            	$doublons = $explnum->has_doublons("./temp/".$explnum->infos_docnum["userfile_name"]);
		            }
		            if(empty($doublons)) {
		            	$explnum->update(false);
		            } else {
		            	//il existe au moins un doublon
		            }
		            return $explnum;
		        }else {
		            header($protocol.' 505 Internal Server Error');
		            return false;
		        }
		    }
		}

		public static function get_pattern_link() {
			global $base_path;
			return $base_path.'/catalog.php?categ=edit_explnum&id=!!notice_id!!&explnum_id=!!explnum_id!!';
		}

		public function get_display_link(){
		    /**
		     * Si un bulletin possède un document numérique
		     * il a forcement une notice associée
		     */

		    if($this->explnum_bulletin){
		        return "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
		    }
		    if($this->explnum_notice){
		        $notice = new notice($this->explnum_notice);
		        if ($notice->biblio_level =='a' && $notice->hierar_level == 2) {
		            return "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
		        } elseif ($notice->biblio_level=='m' && $notice->hierar_level== 0) {
		            return static::get_pattern_link();
		        } elseif ($notice->biblio_level=='b' && $notice->hierar_level==2) { // on est face à une notice de bulletin
		            $query = 'select bulletin_id from bulletins where num_notice = '.$notice->id;
		            $result = pmb_mysql_query($query);
		            if($result && pmb_mysql_num_rows($result)){
		                return "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=".pmb_mysql_result($result, 0, 0)."&explnum_id=!!explnum_id!!";
		            }
		        }
		    }
		    return '';
		}

        /**
         *
         * @param parametres_perso $p_perso
         */
        public function set_p_perso(parametres_perso $p_perso) {
        	$this->p_perso = $p_perso;
        }

        public function get_p_perso() {
        	if (isset($this->p_perso)) {
        		return $this->p_perso;
        	}
        	$this->p_perso = new parametres_perso("explnum");
        	return $this->p_perso;
        }
        public function get_file_from_contrib($filename, $name) {
            global $base_path;
            global $ck_index;
            global $id_rep, $up_place;
            global $pmb_indexation_docnum_default;

            // On a rajouté le champ dans la map de base, donc on peut s'en servir !
            $up_place = $this->explnum_repertoire;
            $id_rep = $this->explnum_repertoire;

            create_tableau_mimetype();
            if (!isset($ck_index)) {
                $ck_index = $pmb_indexation_docnum_default;
            }
            // Initialisation des tableaux d'infos
            $this->infos_docnum = $this->params = array();
            $this->infos_docnum["mime"] = trouve_mimetype($filename, extension_fichier($name));
            $this->infos_docnum["nom"] = $filename;
            $this->infos_docnum["notice"] = $this->explnum_notice;
            $this->infos_docnum["bull"] = $this->explnum_bulletin;
            $this->infos_docnum["url"] = "";
            $this->infos_docnum["fic"] = false;
            $this->params['explnum_statut'] = $this->explnum_docnum_statut;
            if ($this->infos_docnum["mime"] != 'text/plain') {
                $this->infos_docnum["contenu_vignette"] = construire_vignette('', substr($filename, strrpos($filename, "/")));
            }
            $this->infos_docnum["userfile_name"] = static::clean_explnum_file_name($name);
            $this->infos_docnum["userfile_ext"] = extension_fichier($name);
            $this->infos_docnum["from_contrib"] = true;

            if ($up_place && $id_rep != 0) {
                $upfolder = new upload_folder($id_rep);
                $chemin_hasher = "/";
                if ($upfolder->isHashing()) {
                    $rep = $upfolder->hachage($this->infos_docnum["userfile_name"]);
                    @mkdir($rep);
                    $chemin_hasher = $upfolder->formate_path_to_nom($rep);
                    $file_name = $rep . $this->infos_docnum["userfile_name"];
                    $chemin = $upfolder->formate_path_to_save($chemin_hasher);
                } else {
                    $file_name = $upfolder->get_path($this->infos_docnum["userfile_name"]) . $this->infos_docnum["userfile_name"];
                    $chemin = $upfolder->formate_path_to_save("/");
                }
                $this->infos_docnum["path"] = $chemin;
                $file_name = $upfolder->encoder_chaine($file_name);
                if (! $this->explnum_nomfichier) { // Si je suis en création de fichier numérique
                    $nom_tmp = $this->infos_docnum["userfile_name"];
                    $continue = true;
                    $compte = 1;
                    do {
                        $query = "select explnum_notice,explnum_id from explnum where explnum_nomfichier = '" . addslashes($nom_tmp) . "' AND explnum_repertoire='" . $id_rep . "' AND explnum_path='" . addslashes($this->infos_docnum["path"]) . "'";
                        $result = pmb_mysql_query($query);
                        if (pmb_mysql_num_rows($result) && (pmb_mysql_result($result, 0, 0) != $this->infos_docnum["notice"])) { // Si j'ai déjà un document numérique avec ce fichier pour une autre notice je dois le renommer pour ne pas perdre l'ancien
                            if (preg_match("/^(.+)(\..+)$/i", $this->infos_docnum["userfile_name"], $matches)) {
                                $nom_tmp = $matches[1] . "_" . $compte . $matches[2];
                            } else {
                                $nom_tmp = $this->infos_docnum["userfile_name"] . "_" . $compte;
                            }
                            $compte++;
                        } else {
                            if (pmb_mysql_num_rows($result)) { // J'ai déjà ce fichier pour cette notice
                                // Je dois enlever l'ancien document numérique pour ne pas l'avoir en double
                                $old_docnum = new explnum(pmb_mysql_result($result, 0, 1));
                                $old_docnum->delete();
                            }
                            $continue = false;
                        }
                    } while ( $continue );
                    if ($compte != 1) {
                        $this->infos_docnum["userfile_name"] = $nom_tmp;
                        if ($upfolder->isHashing()) {
                            $file_name = $rep . $this->infos_docnum["userfile_name"];
                        } else {
                            $file_name = $upfolder->get_path($this->infos_docnum["userfile_name"]) . $this->infos_docnum["userfile_name"];
                        }
                        $file_name = $upfolder->encoder_chaine($file_name);
                    }
                }
                if (copy($filename, $file_name)) {
                    unlink($filename);
                }
            } else {
                // enregistrement en base
                $this->infos_docnum["contenu"] = "";
                if (is_file($filename) && is_readable($filename)) {
                    $this->infos_docnum["contenu"] = file_get_contents($filename);
                }
            }

            $this->params["maj_mimetype"] = true;
            $this->params["maj_data"] = true;
            $this->params["maj_vignette"] = true;
        }

        public static function get_thumbnail_url($explnum_vignette, $explnum_id) {
    	    global $pmb_docnum_img_folder_id;
	        global $prefix_url_image ;

    	    if ($pmb_docnum_img_folder_id) {
    	        static::upload_thumbnail($explnum_vignette, $explnum_id);
    	    }
	        if ($prefix_url_image) {
	            $tmpprefix_url_image = $prefix_url_image;
	        } else {
	            $tmpprefix_url_image = "./" ;
	        }
            return $tmpprefix_url_image."vig_num.php?explnum_id=".$explnum_id;
    	}

    	public static function upload_thumbnail($explnum_vignette, $explnum_id) {
    	    if ($explnum_vignette) {
    	        $query = "select repertoire_path from upload_repertoire where repertoire_id ='".thumbnail::get_parameter_img_folder_id("docnum")."'";
    	        $result = pmb_mysql_query($query);
    	        if(pmb_mysql_num_rows($result)){
    	            $row=pmb_mysql_fetch_object($result);
    	            $filename_output=$row->repertoire_path.thumbnail::get_img_prefix("docnum").$explnum_id;
    	            if (file_put_contents($filename_output, $explnum_vignette)) {
        	            $query = "update explnum set explnum_vignette='' where explnum_id='" . $explnum_id . "'";
        	            pmb_mysql_query($query);
    	            }
    	        }
    	    }
    	}

    	public function get_form_cert() {
    	    global $pmb_digital_signature_activate;
    	    global $msg, $charset;

    	    if (!$pmb_digital_signature_activate) {
    	       return "";
    	    }

    	    //si il existe deja une signature, on ne peut en recreer une
    	    if ($this->explnum_id) {
    	        $docSign = new DocnumCertifier($this);
    	        if ($docSign->checkSignExists()) {
    	            return "";
    	        }
    	    }

	        $select_cert = gen_liste_multiple("select id, name, num_cert from digital_signature","id", "name","", "sign_data_cert","","","", "", "", "", 0);
	        $form_cert = "
                <div id='el0Child_6' class='row' movable='yes' title=\"".htmlentities($msg['digital_signature_docnum_title'], ENT_QUOTES, $charset)."\">
						<div class='row'>
                	       <label class='etiquette'>".htmlentities($msg['digital_signature_base_title'], ENT_QUOTES, $charset)."</label>
                		</div>
                		<div class='row'>
                            <input type='checkbox' id='ck_sign' value='1' name='ck_sign' /><label for='ck_sign'>" . $msg['explnum_digital_signature_ck_sign'] . "</label>
                            <br />
                			$select_cert
                		</div>
					    <div class='row'>&nbsp;</div>
					</div>
            ";

		    return $form_cert;
    	}

    	public function add_url($f_nom, $f_url, $overwrite = true, $f_statut = 0)
    	{
    	    if (!$overwrite) {
    	        $sql_find = "SELECT count(*) FROM explnum WHERE explnum_notice = " . $this->explnum_notice . " AND explnum_nom = '" . addslashes($f_nom) . "'";
    	        $res = pmb_mysql_query($sql_find);
    	        $count = pmb_mysql_result($res, 0, 0);
    	        if ($count) {
    	            return;
    	        }
    	    }
    	    $sql_delete = "DELETE FROM explnum WHERE explnum_notice = " . $this->explnum_notice . " AND explnum_nom = '" . addslashes($f_nom) . "'";
    	    pmb_mysql_query($sql_delete);

    	    $original_filename = basename($f_url);
    	    $extension = strrchr($original_filename, '.');
    	    $insert_sql = "INSERT INTO explnum (explnum_notice, explnum_bulletin, explnum_nom, explnum_nomfichier, explnum_url, explnum_mimetype, explnum_extfichier, explnum_docnum_statut, explnum_vignette) VALUES (";
    	    $insert_sql .= $this->explnum_notice . ",";
    	    $insert_sql .= $this->explnum_bulletin . ",";
    	    $insert_sql .= "'" . addslashes($f_nom) . "',";
    	    $insert_sql .= "'" . addslashes($original_filename) . "',";
    	    $insert_sql .= "'" . addslashes($f_url) . "',";
    	    $insert_sql .= "'" . "URL" . "',";
    	    $insert_sql .= "'" . addslashes($extension) . "',";
    	    $insert_sql .= "'" . (($f_statut) ? $f_statut : 1) . "',";
    	    $insert_sql .= "'" . addslashes(construire_vignette('', '', $f_url)) . "'";
    	    $insert_sql .= ")";

    	    if (pmb_mysql_query($insert_sql)) {
    	        $docnum_id = pmb_mysql_insert_id();
    	        if ($docnum_id) {
    	            $index = new indexation_docnum($docnum_id);
    	            $index->indexer();
    	        }
    	    }
    	}

    	public function add_from_url($f_nom, $f_url, $overwrite = true, $source_id = 0, $filename = '', $f_path = '', $f_statut = 0)
    	{
    	    global $base_path;

    	    if (! $overwrite) {
    	        $sql_find = "SELECT count(*) FROM explnum WHERE explnum_notice = " . $this->explnum_notice . " AND explnum_nom = '" . addslashes($f_nom) . "'";
    	        $res = pmb_mysql_query($sql_find);
    	        $count = pmb_mysql_result($res, 0, 0);
    	        if ($count) {
    	            return;
    	        }
    	    }
    	    $sql_delete = "DELETE FROM explnum WHERE explnum_notice = " . $this->explnum_notice . " AND explnum_nom = '" . addslashes($f_nom) . "' ";
    	    pmb_mysql_query($sql_delete);

    	    $original_filename = basename($f_url);
    	    if (strripos($original_filename, '.') !== false) {
    	        $extension = substr($original_filename, strripos($original_filename, '.') * 1 + 1);
    	    } elseif (strripos($filename, '.') !== false) {
    	        $extension = substr($filename, strripos($filename, '.') * 1 + 1);
    	    } else {
    	        $extension = substr($f_nom, strripos($f_nom, '.') * 1 + 1);
    	    }
    	    $tmp_filename = explnum::static_rename($extension);
    	    if ($filename) {
    	        $new_filename = $filename;
    	    } else {
    	        $new_filename = $tmp_filename;
    	    }
    	    // copie en repertoire temporaire
    	    $r = false;
    	    if (file_exists($f_url) && filesize($f_url)) { // document en repertoire
    	        $r = copy($f_url, $base_path . '/temp/' . $tmp_filename);
    	    } else { // url
    	        $aCurl = new Curl();
    	        $aCurl->set_option('CURLOPT_SSL_VERIFYPEER', false);
    	        $content = $aCurl->get($f_url);
    	        $content = $content->body;
    	        $r = file_put_contents($base_path . "/temp/" . $tmp_filename, $content);
    	    }

    	    if ($r) {

    	        // construction vignette
    	        $vignette = construire_vignette('', $tmp_filename);
    	        create_tableau_mimetype();
    	        $mimetype = trouve_mimetype("$base_path/temp/" . $tmp_filename, $extension);

    	        // si la source du connecteur est precisee, on regarde si on a pas un repertoire associe
    	        $rep_upload = 0;
    	        if ($source_id) {
    	            $check_rep = "select rep_upload from connectors_sources where source_id = " . $source_id;
    	            $res = pmb_mysql_query($check_rep);
    	            if (pmb_mysql_num_rows($res)) {
    	                $rep_upload = pmb_mysql_result($res, 0, 0);
    	            }
    	        }
    	        if ($rep_upload != 0) {
    	            $upload_folder = new upload_folder($rep_upload);
    	            $rep_path = $upload_folder->get_path($new_filename);
    	            if ($f_path && file_exists($rep_path . $f_path)) {
    	                $rep_path = $rep_path . $f_path . '/';
    	            }

    	            if (file_exists($upload_folder->encoder_chaine($rep_path . $new_filename))) {
    	                $suffix = 1;
    	                $ext = extension_fichier($new_filename);
    	                $file = str_replace("." . $ext, "", basename($new_filename));
    	                while (file_exists($upload_folder->encoder_chaine($rep_path . $file . "_" . $suffix . "." . $ext))) {
    	                    $suffix ++;
    	                }
    	                $new_filename = $file . "_" . $suffix . "." . $ext;
    	            }
    	            if (copy("$base_path/temp/" . $tmp_filename, $upload_folder->encoder_chaine($rep_path . $new_filename))) {
    	                unlink("$base_path/temp/" . $tmp_filename);
    	            }
    	            $path = $upload_folder->formate_path_to_save($upload_folder->formate_path_to_nom($rep_path));
    	            $insert_sql = "INSERT INTO explnum (explnum_notice, explnum_bulletin, explnum_nom, explnum_nomfichier, explnum_mimetype, explnum_extfichier, explnum_vignette, explnum_repertoire, explnum_path, explnum_docnum_statut) VALUES (";
    	            $insert_sql .= $this->explnum_notice . ",";
    	            $insert_sql .= $this->explnum_bulletin . ",";
    	            $insert_sql .= "'" . addslashes($f_nom) . "',";
    	            $insert_sql .= "'" . addslashes($new_filename) . "',";
    	            $insert_sql .= "'" . addslashes($mimetype) . "',";
    	            $insert_sql .= "'" . addslashes($extension) . "',";
    	            $insert_sql .= "'" . addslashes($vignette) . "',";
    	            $insert_sql .= "'" . addslashes($rep_upload) . "',";
    	            $insert_sql .= "'" . addslashes($path) . "',";
    	            $insert_sql .= "'" . (($f_statut) ? $f_statut : 1) . "'";
    	            $insert_sql .= ")";
    	        } else {
    	            $insert_sql = "INSERT INTO explnum (explnum_notice, explnum_bulletin, explnum_nom, explnum_nomfichier, explnum_mimetype, explnum_extfichier, explnum_data, explnum_vignette, explnum_docnum_statut) VALUES (";
    	            $insert_sql .= $this->explnum_notice . ",";
    	            $insert_sql .= $this->explnum_bulletin . ",";
    	            $insert_sql .= "'" . addslashes($f_nom) . "',";
    	            $insert_sql .= "'" . addslashes($new_filename) . "',";
    	            $insert_sql .= "'" . addslashes($mimetype) . "',";
    	            $insert_sql .= "'" . addslashes($extension) . "',";
    	            $insert_sql .= "'" . addslashes($content) . "',";
    	            $insert_sql .= "'" . addslashes($vignette) . "',";
    	            $insert_sql .= "'" . (($f_statut) ? $f_statut : 1) . "'";
    	            $insert_sql .= ")";
    	        }
    	        if (pmb_mysql_query($insert_sql)) {
    	            $docnum_id = pmb_mysql_insert_id();
    	            if ($docnum_id) {
    	                $index = new indexation_docnum($docnum_id);
    	                $index->indexer();
    	            }
    	        }
    	    }
    	}

    	public static function get_explnum_name($explnum_id) {
    	    $requete = "SELECT explnum_nom
				        FROM explnum where explnum_id='$explnum_id'";
    	    $result = pmb_mysql_query($requete);

    	    if(pmb_mysql_num_rows($result)) {
    	        $item = pmb_mysql_fetch_object($result);
    	        return $item->explnum_nom;
    	    }
    	}


    	/**
    	 * Droit d'acces pour la vignette, renvoie vrai en gestion.
    	 * Pour la compatibilite avec les classes de sources de vignettes qui sont utilisees en gestion et opac
    	 * @param int $explnum_id
    	 * @param int $explnum_notice
    	 * @return boolean
    	 */
    	public static function has_acces_vignette($explnum_id, $explnum_notice) {
    	    return true;
    	}

    	// fonction qui permet de savoir si les exemplaires numeriques pour une notice ou un bulletin donne sont affichable a l'OPAC
    	public static function allow_opac($no_notice, $no_bulletin)
    	{
    	    global $gestion_acces_active, $gestion_acces_empr_notice, $opac_show_links_invisible_docnums;

    	    $no_notice = intval($no_notice);
    	    $no_bulletin = intval($no_bulletin);
    	    if (! $no_notice && ! $no_bulletin) {
    	        return false;
    	    }
	        $docnum_visible = true;
	        $id_for_right = $no_notice;
	        if ($no_bulletin) {
	            $query = "select num_notice,bulletin_notice from bulletins where bulletin_id = " . $no_bulletin;
	            $result = pmb_mysql_query($query);
	            if (pmb_mysql_num_rows($result)) {
	                $infos = pmb_mysql_fetch_object($result);
	                if ($infos->num_notice) {
	                    $id_for_right = $infos->num_notice;
	                } else {
	                    $id_for_right = $infos->bulletin_notice;
	                }
	            }
	        }
	        if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
	            $ac = new acces();
	            $dom_2 = $ac->setDomain(2);
	            $docnum_visible = $dom_2->getRights(0, $id_for_right, 16);
	        } else {
	            $requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='" . $id_for_right . "' and id_notice_statut=statut ";
	            $myQuery = pmb_mysql_query($requete);
	            if (pmb_mysql_num_rows($myQuery)) {
	                $statut_temp = pmb_mysql_fetch_object($myQuery);
	                if (! $statut_temp->explnum_visible_opac)
	                    $docnum_visible = false;
	                    if (($statut_temp->explnum_visible_opac_abon) && (! $opac_show_links_invisible_docnums))
	                        $docnum_visible = false;
	            } else
	                $docnum_visible = false;
	        }
	        return $docnum_visible;
    	}
	}

}

