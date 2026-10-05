<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: oai.class.php,v 1.18.2.1.2.2 2025/04/16 12:16:51 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($base_path."/admin/connecteurs/in/oai/oai_protocol.class.php");

class oai extends connector {

	//Variables internes pour la progression de la récupération des notices
	public $current_set;			//Set en cours de synchronisation
	public $total_sets;			//Nombre total de sets sélectionnés
	public $metadata_prefix;		//Préfixe du format de données courant
	public $n_recu;				//Nombre de notices reçues
	public $xslt_transform;		//Feuille xslt transmise
	public $sets_names;			//Nom des sets pour faire plus joli !!

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
    public function get_id()
    {
    	return "oai";
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::is_repository()
     */
	public function is_repository()
	{
	    return connector::REPOSITORY_YES;
	}

	//Formulaire des propriétés générales
	public function get_property_form() {
		$this->fetch_global_properties();
		//Affichage du formulaire en fonction de $this->parameters
		$parameters = unserialize($this->parameters);

		if(!isset($parameters['sets_completion'])) {
			$parameters['sets_completion'] = '1';
		}
		$r="
			<div class='row'>
				<div class='colonne3'><label for='sets_completion'>".$this->msg["oai_sets_to_sync_completion_format"]."</label></div>
				<div class='colonne-suite'><input type='checkbox' id='sets_completion' name='sets_completion' value='1' ".(!empty($parameters['sets_completion']) ? "checked='checked'" : "")." /></div>
			</div>
			";
		return $r;
	}

	public function source_get_property_form($source_id) {
		global $base_path, $charset, $clean_base_url, $sets, $formats, $xsl_transform, $url;
		global $del_deleted, $clean_html;

		$params = $this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars = unserialize($params["PARAMETERS"]);
			foreach ($vars as $key => $val) {
				global ${$key};
				${$key} = $val;
			}
		}
		$form = "
	    <div class='row'>
    		<div class='colonne3'>
    			<label for='url'>".$this->msg["oai_url"]."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='text' name='url' id='url' class='saisie-80em' value='".htmlentities($url ?? "", ENT_QUOTES, $charset)."'/>
    		</div>
	    </div>
		<div class='row'>
			<div class='colonne3'>
				<label for='clean_base_url'>".$this->msg["oai_clean_url"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' name='clean_base_url' id='clean_base_url' value='1' ".($clean_base_url ? "checked" : "")."/>
			</div>
		</div>
		<div class='row'>
		";
		if (empty($url)) {
			$form .= "<h3 style='text-align:center'>".$this->msg["rec_addr"]."</h3>";
		} else {
			//Intérogation du serveur
			$oai_p = new oai20($url, $charset, $params["TIMEOUT"]);
			if ($oai_p->error) {
				$form .= "<h3 style='text-align:center'>".sprintf($this->msg["error_contact_server"], $oai_p->error_message)."</h3>";
			} else {
				$form .= "<h3 style='text-align:center'>".$oai_p->repositoryName."</h3>";
				if (!empty($oai_p->description)) {
					$form .= "
					<div class='row'>
						<div class='colonne3'>
							<label>".$this->msg["oai_desc"]."</label>
						</div>
						<div class='colonne_suite'>
							".htmlentities($oai_p->description, ENT_QUOTES, $charset)."
						</div>
					</div>
					";
				}
				$form .= "
				<div class='row'>
					<div class='colonne3'>
						<label>".$this->msg["oai_older_metatdatas"]."</label>
					</div>
					<div class='colonne_suite'>
						".formatdate($oai_p->earliestDatestamp)."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label>".$this->msg["oai_email_admin"]."</label>
					</div>
					<div class='colonne_suite'>
						".$oai_p->adminEmail."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label>".$this->msg["oai_granularity"]."</label>
					</div>
					<div class='colonne_suite'>
						".($oai_p->granularity=="YYYY-MM-DD" ? $this->msg["oai_one_day"] : $this->msg["oai_minute"])."
					</div>
				</div>";

				if ($oai_p->has_feature("SETS")) {
					$form .= "
    				<div class='row'>
    					<div class='colonne3'>
    						<label for='sets'>".$this->msg["oai_sets_to_sync"]."</label>
    					</div>
    					<div class='colonne_suite'>";

					$elements = array();
					foreach ($oai_p->sets as $code => $set) {
						if (array_search($code, $sets) !== false) {
							$elements[] = array('id' => $code, 'name' => $set['name'].($set['description'] ? " (".$set['description'].")" : ""));
						}
					}

					$unserialized_parameters = unserialize($this->parameters);
					if(isset($unserialized_parameters['sets_completion'])) {
						$sets_completion = $unserialized_parameters['sets_completion'] ?? 0;
					} else {
						$sets_completion = 1;
					}
					if(!empty($sets_completion)) {
						$form .= "<script src='".$base_path."/javascript/ajax.js'></script>";
						templates::init_completion_attributes(array(
								array('name' => 'att_id_filter', 'value' => $source_id),
								array('name' => 'source_url', 'value' => urlencode($vars['url'])),
								array('name' => 'connector_path', 'value' => $this->get_id()),
								array('name' => 'connector_name', 'value' => $this->get_id())
						));
						templates::init_selection_attributes(array(
								array('name' => 'source_id', 'value' => $source_id),
								array('name' => 'source_url', 'value' => urlencode($vars['url'])),
								array('name' => 'connector_path', 'value' => $this->get_id()),
								array('name' => 'connector_name', 'value' => $this->get_id())
						));
						$form .= oai::get_syncronised_sets_template($elements, 'source_form', 'sets', 'set_id', 'connectors', true);
					} else {
						if (count($oai_p->sets)<80) $combien = count($oai_p->sets);
						else $combien=80;
						$form.="<select id='sets' name='sets[]' class='saisie-80em' multiple='yes' size='".$combien."'>";
						foreach ($oai_p->sets as $code => $set) {
							$set_name = $set['name'].($set['description'] ? " (".$set['description'].")" : "");
							$form.="<option value='".htmlentities($code,ENT_QUOTES,$charset)."' alt='".htmlentities($set_name,ENT_QUOTES,$charset)."' title='".htmlentities($set_name,ENT_QUOTES,$charset)."' ".(@array_search($code,$sets)!==false?"selected":"").">".htmlentities($set_name,ENT_QUOTES,$charset)."</option>\n";
						}
						$form.="	</select>";
					}
					$form .= "
						<input type='hidden' name='sets_completion' value='".$sets_completion."' />
					</div></div>";
				}
				$form .= "
				<div class='row'>
					<div class='colonne3'>
						<label for='formats'>".$this->msg["oai_preference_format"]."</label>
					</div>
					<div class='colonne_suite'>
						<select name='formats' id='formats'>";
				if (!is_array($formats)) {
					$formats = array($formats);
				}
				$nb_metadatas = count($oai_p->metadatas);
				for ($i = 0; $i < $nb_metadatas; $i++) {
					$form .= "<option value='".htmlentities($oai_p->metadatas[$i]["PREFIX"], ENT_QUOTES, $charset)."' alt='".htmlentities($oai_p->metadatas[$i]["PREFIX"], ENT_QUOTES, $charset)."' title='".htmlentities($oai_p->metadatas[$i]["PREFIX"], ENT_QUOTES, $charset)."' ".(@array_search($oai_p->metadatas[$i]["PREFIX"], $formats) !== false ? "selected" : "").">".htmlentities($oai_p->metadatas[$i]["PREFIX"], ENT_QUOTES, $charset)."</option>\n";
				}
				$form .= "</select>";
				if (!empty($xsl_transform)) {
					$form .= "<br /><i>".sprintf($this->msg["oai_xslt_file_linked"], $xsl_transform["name"])."</i> :".$this->msg["oai_del_xslt_file"]." <input type='checkbox' name='del_xsl_transform' value='1'/>";
				}
				$form .= "</div></div>";
				$form .= "
				<div class='row'>
				    <div class='colonne3'>
				        <label>".$this->msg['oai_xslt_file']."</label>
		            </div>
				    <div class='colonne_suite'>
				            <input type='file' name='xslt_file' />
		            </div>
	            </div>";
				if ($oai_p->deletedRecord == "persistent" || $oai_p->deletedRecord == "transient") {
					$form .= "
    				<div class='row'>
    					<div class='colonne3'>
    						<label>".sprintf($this->msg["oai_del_marked_elts"], ($oai_p->deletedRecord == "persistent" ? $this->msg["oai_del_marked_persistent"] : $this->msg["oai_del_marked_temp"]))."</label>
    					</div>
    					<div class='colonne_suite'>
    						<label for='del_yes'>".$this->msg["oai_yes"]."</label><input type='radio' name='del_deleted' id='del_yes' value='1' ".($del_deleted == 1 ? "checked" : "").">
    						<label for='del_no'>".$this->msg["oai_no"]."</label><input type='radio' name='del_deleted' id='del_no' value='0' ".($del_deleted == 0 ? "checked" : "").">
    					</div>
    				</div>";
				}
			}
		}
		$form.="
    	</div>
    	<div class='row'>
    		<div class='colonne3'>
    			<label for='clean_html'>".$this->msg["oai_clean_html"]."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='checkbox' name='clean_html' id='clean_html' value='1' ".($clean_html ? "checked" : "")."/>
    		</div>
    	</div>
    	<div class='row'></div>
    	<script>
		    document.getElementsByClassName('form-admin')[0].addEventListener('keypress', function(e) {
		    if (e.keyCode == 13) {
			    e.preventDefault();
            }});
	    </script>
";
		return $form;
    }

    public function make_serialized_properties() {
    	global $sets_completion;
    	//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
    	$param = array();

    	$param['sets_completion']=$sets_completion ?? 0;
    	$this->parameters = serialize($param);
    }

    public function make_serialized_source_properties($source_id) {
    	global $url,$clean_base_url,$formats,$del_deleted,$del_xsl_transform,$clean_html, $sets, $sets_completion;
    	$t=array();
    	$t["url"]=stripslashes($url);
    	$t["clean_base_url"]=$clean_base_url;
    	if(!empty($sets_completion)) {
    		$t["sets"]=templates::get_values_completion_field_from_form('sets');
    	} else {
    		$t["sets"]=$sets;
    	}
    	$t["formats"]=$formats;
    	$t["del_deleted"]=$del_deleted;
    	$t["clean_html"]=$clean_html;

    	//Vérification du fichier
    	if ((!empty($_FILES["xslt_file"]))&&(!$_FILES["xslt_file"]["error"])) {
    		$xslt_file_content=array();
    		$xslt_file_content["name"]=$_FILES["xslt_file"]["name"];
    		$xslt_file_content["code"]=file_get_contents($_FILES["xslt_file"]["tmp_name"]);
    		$t["xsl_transform"]=$xslt_file_content;
    	} else if ($del_xsl_transform) {
			$t["xsl_transform"]="";
    	} else {
    		$oldparams=$this->get_source_params($source_id);
			if ($oldparams["PARAMETERS"]) {
				//Anciens paramètres
				$oldvars=unserialize($oldparams["PARAMETERS"]);
				$t["xsl_transform"] = $oldvars["xsl_transform"];
			} else {
				$t["xsl_transform"] = "";
			}
    	}
		$this->sources[$source_id]["PARAMETERS"]=serialize($t);
	}

	//Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	public function fetch_default_global_values() {
		parent::fetch_default_global_values();
		$this->repository=1;
	}

	public function progress($query,$token) {
		$callback_progress=$this->callback_progress;
		if ($token["completeListSize"]) {
			$percent=($this->current_set/$this->total_sets)+(($token["cursor"]/$token["completeListSize"])/$this->total_sets);
			$nlu=$this->n_recu;
			$ntotal="inconnu";
			//$nlu=$token["cursor"];
			//$ntotal=$token["completeListSize"];
		} else {
			$percent=($this->current_set/$this->total_sets);
			$nlu=$this->n_recu;
			$ntotal="inconnu";
		}
		$callback_progress($percent,$nlu,$ntotal);
	}

	public function rec_record($record) {
		global $charset,$base_path;

		$rec=new oai_record($record,$charset,$base_path."/admin/connecteurs/in/oai/xslt",$this->metadata_prefix,$this->xslt_transform,$this->sets_names);
		$rec_uni=$rec->unimarc;
		if (!$rec->error) {
			//On a un enregistrement unimarc, on l'enregistre
			$rec_uni_dom=new xml_dom($rec_uni,$charset);
			if (!$rec_uni_dom->error) {
				//Initialisation
				$ref="";
				$ufield="";
				$usubfield="";
				$field_order=0;
				$subfield_order=0;
				$value="";
				$date_import=$rec->header["DATESTAMP"];

				$fs=$rec_uni_dom->get_nodes("unimarc/notice/f");
				//Recherche du 001
				for ($i=0; $i<count($fs); $i++) {
					if ($fs[$i]["ATTRIBS"]["c"]=="001") {
						$ref=$rec_uni_dom->get_datas($fs[$i]);
						break;
					}
				}
				//Mise à jour
				if ($ref) {
					//Si conservation des anciennes notices, on regarde si elle existe
					if (!$this->del_old) {
						$ref_exists = $this->has_ref($this->source_id, $ref);
					}
					//Si pas de conservation des anciennes notices, on supprime
					if ($this->del_old) {
						$this->delete_from_entrepot($this->source_id, $ref);
						$this->delete_from_external_count($this->source_id, $ref);
					}
					//Si pas de conservation ou reférence inexistante
					if (($this->del_old)||((!$this->del_old)&&(!$ref_exists))) {
						//Insertion de l'entête
						$n_header=array();
						$n_header["rs"]=$rec_uni_dom->get_value("unimarc/notice/rs");
						$n_header["ru"]=$rec_uni_dom->get_value("unimarc/notice/ru");
						$n_header["el"]=$rec_uni_dom->get_value("unimarc/notice/el");
						$n_header["bl"]=$rec_uni_dom->get_value("unimarc/notice/bl");
						$n_header["hl"]=$rec_uni_dom->get_value("unimarc/notice/hl");
						$n_header["dt"]=$rec_uni_dom->get_value("unimarc/notice/dt");

						//Récupération d'un ID
						$recid = $this->insert_into_external_count($this->source_id, $ref);

						foreach($n_header as $hc=>$code) {
							$this->insert_header_into_entrepot($this->source_id, $ref, $date_import, $hc, $code, $recid);
						}

						for ($i=0; $i<count($fs); $i++) {
							$ufield=(isset($fs[$i]["ATTRIBS"]["c"]) ? $fs[$i]["ATTRIBS"]["c"] : '');
							$field_order=$i;
							$ss=$rec_uni_dom->get_nodes("s",$fs[$i]);
							if (!empty($ss) && is_array($ss)) {
								for ($j=0; $j<count($ss); $j++) {
									$usubfield=$ss[$j]["ATTRIBS"]["c"];
									$value=$rec_uni_dom->get_datas($ss[$j]);
									if ($clean_html) {
										$value = strip_tags(html_entity_decode($value,ENT_QUOTES,$charset));
									}
									$subfield_order=$j;
									$this->insert_content_into_entrepot($this->source_id, $ref, $date_import, $ufield, $usubfield, $field_order, $subfield_order, $value, $recid);
								}
							} else {
								$value=$rec_uni_dom->get_datas($fs[$i]);
								if ($clean_html) {
									$value = strip_tags(html_entity_decode($value,ENT_QUOTES,$charset));
								}
								$this->insert_content_into_entrepot($this->source_id, $ref, $date_import, $ufield, $usubfield, $field_order, $subfield_order, $value, $recid);
							}
						}
						$this->insert_origine_into_entrepot($this->source_id, $ref, $date_import, $recid);
						$this->rec_isbd_record($this->source_id, $ref, $recid);
					}
					$this->n_recu++;
				}
			}
		}
	}

	public function maj_entrepot($source_id,$callback_progress="",$recover=false,$recover_env="") {
		global $charset;
		$this->callback_progress=$callback_progress;
		$params=$this->unserialize_source_params($source_id);
		$p=$params["PARAMETERS"];
		$this->metadata_prefix=$p["formats"];
		$this->source_id=$source_id;
		$this->n_recu=0;
		$this->xslt_transform = $p["xsl_transform"]["code"] ?? '';

		//Connexion
		$oai20=new oai20($p["url"],$charset,$p["TIMEOUT"],$p["clean_base_url"]);
		if (!$oai20->error) {
			if ($recover) {
				$envt=unserialize($recover_env);
				$sets=$envt["sets"];
				$date_start=$envt["date_start"];
				$this->del_old=false;
			} else {
				//Recherche de la dernière date...
				$requete="select unix_timestamp(max(date_import)) from entrepot_source_".$this->source_id." where connector_id='".addslashes($this->get_id()).";";
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)) {
					$last_date=pmb_mysql_result($resultat,0,0);
					if ($last_date) {
						//En fonction de la granularité, on ajoute une seconde ou un jour !
						if ($oai20->granularity=="YYYY-MM-DD") $last_date+=3600*24; else $last_date+=1;
					} else {
						$earliest_date=new iso8601($oai20->granularity);
						$last_date=$earliest_date->iso8601_to_unixtime($oai20->earliestDatestamp);
					}
				} else {
					$earliest_date=new iso8601($oai20->granularity);
					$last_date=$earliest_date->iso8601_to_unixtime($oai20->earliestDatestamp);
				}
				//Affectation de la date de départ
				$date_start=$last_date;
				//Recherche des sets sélectionnés
				$sets_names = array();
				foreach ($oai20->sets as $code=>$set) {
					$sets_names[$code] = $set['name'];
				}
				$this->sets_names=$sets_names;
				for ($i=0; $i<count($p["sets"]);$i++) {
					if ($oai20->sets[$p["sets"][$i]]) {
						$sets[]=$p["sets"][$i];
					}
				}
				$this->del_old=true;
			}

			//Mise à jour de source_sync pour reprise en cas d'erreur
			$envt["sets"]=$sets;
			$envt["date_start"]=$date_start;
			$requete="update source_sync set env='".addslashes(serialize($envt))."' where source_id=".$source_id;
			pmb_mysql_query($requete);

			//Lancement de la requête
			$this->current_set = 0;
			if (is_countable($sets)) {
				$this->total_sets = count($sets);
			}
			if (is_countable($sets) && count($sets)) {
				for ($i=0; $i<count($sets); $i++) {
					$this->current_set=$i;
					$oai20->list_records($date_start, "",$sets[$i],$p["formats"],array(&$this,"rec_record"),array(&$this,"progress"));
					if (($oai20->error)&&($oai20->error_oai_code!="noRecordsMatch")) {
						$this->error=true;
						$this->error_message.=$oai20->error_message."<br />";
					}
				}
			} else {
				$this->current_set=0;
				$this->total_sets=1;
				$oai20->list_records($date_start,"","",$p["formats"],array(&$this,"rec_record"),array(&$this,"progress"));
				if (($oai20->error)&&($oai20->error_oai_code!="noRecordsMatch")) {
					$this->error=true;
					$this->error_message.=$oai20->error_message."<br />";
				}
			}
		} else {
			$this->error=true;
			$this->error_message=$oai20->error_message;
		}
		return $this->n_recu;
	}

	public static function get_syncronised_sets_template($elements, $caller, $element_name, $element_id, $completion) {
	    $display = '';
	    $display .= templates::get_event_add_completion_field($element_name, $element_id, $completion);
	    $display .= templates::get_button_selector($caller.'_'.$element_name.'_selector', $completion, $caller, '&param1='.$element_id.'&param2='.$element_name);
	    $display .= templates::get_button_clear_values($element_name, $element_id);
	    $display .= templates::get_button_add_completion_field($element_name, $element_id, $completion);

	    if (!empty($elements)) {
	        foreach ($elements as $i => $element) {
	            $display .= "<div id='".$caller."_".$element_name."_".$i."'>";
	            $display .= templates::get_input_completion($element_name, $element_id, $i, $element['id'], $element['name'], $completion);
	            if (!isset($elements[$i+1])) {
	                $display .= "<input id='button_add_$element_name' type='button' class='bouton' value='+' onclick='templates.add_completion_field(\"$element_name\", \"$element_id\", \"connectors\");'>";
	            }
	            $display .= "</div>";
	        }
	        $display .= templates::get_input_hidden('max_'.$element_name, count($elements));
	    } else {
	        $display .= "<div id='".$caller."_".$element_name."_0'>";
	        $display .= templates::get_input_completion($element_name, $element_id, 0, '', '', $completion);
 	        $display .= "<input id='button_add_$element_name' type='button' class='bouton' value='+' onclick='templates.add_completion_field(\"$element_name\", \"$element_id\", \"connectors\");'>";
	        $display .= "</div>";
	        $display .= templates::get_input_hidden('max_'.$element_name, 1);
	    }
	    $display.= "<div id='add".$element_name."' data-completion-attributes='".encoding_normalize::json_encode(templates::get_completion_attributes())."'></div>";

	    // Ré-initialisation des propriétés statiques
	    templates::reset_completion_attributes();
	    templates::reset_selection_attributes();

	    return $display;
	}
}
?>