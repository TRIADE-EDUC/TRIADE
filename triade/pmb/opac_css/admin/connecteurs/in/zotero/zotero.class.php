<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: zotero.class.php,v 1.13.4.1 2025/04/16 12:16:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/xml_dom.class.php");

class zotero extends connector {

	//Variables internes pour la progression de la récupération des notices
	public $n_recu = 0;				//Nombre de notices reçues
	public $n_total = 0;			//Nombre total de notices

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
	public function get_id()
	{
		return 'zotero';
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

	public function source_get_property_form($source_id) {
		global $charset;

		$params=$this->get_source_params($source_id);
		if ($params['PARAMETERS']) {
			//Affichage du formulaire avec $params['PARAMETERS']
			$vars=unserialize($params['PARAMETERS']);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		$form="
		<div class='row'>&nbsp;</div>
		<h3>".$this->msg['zotero_auth']."</h3>
		<div class='row'>&nbsp;</div>
		<div class='row'>
		<div class='colonne3'>
		<label for='zotero_userid'>".$this->msg['zotero_userid']."</label>
		</div>
		<div class='colonne_suite'>
		<input type='text' name='zotero_userid' id='zotero_userid' class='saisie-60em' value='".htmlentities($zotero_userid,ENT_QUOTES,$charset)."'/>
		</div>
		</div>
		<div class='row'>
		<div class='colonne3'>
		<label for='zotero_client_key'>".$this->msg['zotero_client_key']."</label>
		</div>
		<div class='colonne_suite'>
		<input type='text' name='zotero_client_key' id='zotero_client_key' class='saisie-60em' value='".htmlentities($zotero_client_key,ENT_QUOTES,$charset)."'/>
		</div>
		</div>
		<div class='row'>&nbsp;</div>
		";
		if (!($zotero_userid && $zotero_client_key) ) {

			$form.="<div class='row'>
			<h3 style='text-align:center'>".$this->msg['zotero_record_to_see_more']."</h3>
			</div>";
			$form.="<div class='row'>&nbsp;</div>";

		} else {

			//Récupération des collections
			$zot = new zotero_protocol($vars,$charset);
			$collections = $zot->get_collections();
			if ($zot->error) {
				$form.="<div class='row'>
				<h3 style='text-align:center'>".$this->msg['zotero_error']."</h3>
				</div>";
				$form.="<div class='row'>&nbsp;</div>";
			} else {

				$form.="<div class='row'>
				<div class='colonne3'>
					<label>".$this->msg['zotero_collections_restrict']."</label>
				</div>
				<div class='colonne_suite'>";
				if (count($collections)) {
					$selected = array();
					if (is_array($vars['zotero_collections'])) $selected = $vars['zotero_collections'];
					$form.= $this->get_html_select($selected, $collections,array('id'=>'zotero_collections', 'name'=>'zotero_collections[]','class'=>'saisie-20em','size'=>'4','multiple'=>'multiple'));
				} else {
					$form.= $this->msg['zotero_no_collection'];
				}
				$form.= "	</div>
				</div>";
				$form.="<div class='row'>&nbsp;</div>";
			}
		}

		return $form;
	}

	public function get_html_select($selected=array(), $collections=array(), $sel_attr=array()) {
		global $charset;

		$sel='';
		$size=count($collections);
		if ($size) {
			$sel="<select ";
			if (count($sel_attr)) {
				foreach($sel_attr as $attr=>$val) {
					$sel.="$attr='".$val."' ";
				}
			}
			$sel.=">";
			foreach($collections as $id=>$val){
				$sel.="<option value='".$id."'";
				if(in_array($id,$selected)) $sel.=" selected='selected'";
				$sel.=" >";
				$sel.=htmlentities($val,ENT_QUOTES,$charset);
				$sel.="</option>";
			}
			$sel.='</select>';
		}
		return $sel;
	}

	public function make_serialized_source_properties($source_id) {
		global $zotero_userid,$zotero_client_key,$zotero_collections,$zotero_del_deleted;
		$t=array();
		$t['zotero_userid']=stripslashes($zotero_userid);
		$t['zotero_client_key']=stripslashes($zotero_client_key);

		if (is_array($zotero_collections) && count($zotero_collections)) {
			foreach($zotero_collections as $k=>$v) {
				$zotero_collections[$k]=stripslashes($v);
			}
		} else {
			$zotero_collections=array();
		}
		$t['zotero_collections']=$zotero_collections;
		$t['zotero_del_deleted']=$zotero_del_deleted;
		$this->sources[$source_id]['PARAMETERS']=serialize($t);
	}

	//Récupération  des propriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	public function fetch_default_global_values() {
		parent::fetch_default_global_values();
		$this->repository=1;
	}


	public function progress() {
		$callback_progress=$this->callback_progress;

		if ($this->n_total) {
			$percent =($this->n_recu / $this->n_total);
			$nlu = $this->n_recu;
			$ntotal = $this->n_total;
		} else {
			$percent=0;
			$nlu = $this->n_recu;
			$ntotal = "inconnu";
		}
		call_user_func($callback_progress,$percent,$nlu,$ntotal);
	}


	public function rec_record($record=array()) {
		global $charset, $base_path;

		$xml = new DOMDocument('1.0', 'utf-8');
		//$xml->formatOutput = true;
		$xml_rec = $xml->createElement('record');
		$xml->appendChild($xml_rec);
		$xml_rec->setAttribute('key', $record['zapi:key']);
		$xml_rec->setAttribute('version', $record['zapi:version']);

		if( is_array($record['content']) && count($record['content']) ) {
			foreach($record['content'] as $k=>$v) {
				$this->recurse_record($xml, $xml_rec, $k, $v);
	 		}
		}

		if( is_array($record['attachments']) && count($record['attachments']) ) {

			$xml_atts = $xml->createElement('attachments');
			$xml_rec->appendChild($xml_atts);
			foreach($record['attachments'] as $k=>$attachment) {
				$xml_att = $xml->createElement('attachment');
				$xml_atts->appendChild($xml_att);
				foreach($attachment['content'] as $k1=>$v1) {
					$xml_att->setAttribute('zapi:key', $attachment['zapi:key']);
					$xml_att->setAttribute('zapi:version', $record['zapi:version']);
					if($record['url']) {
						$new_elt =  $xml->createElement('url', $record['url'] );
						$xml_att->appendChild($new_elt);
					}
					$this->recurse_record($xml, $xml_att, $k1, $v1);
				}
 			}

		}

		$in = $xml->saveXML();
		$xsl_filename = $base_path.'/admin/connecteurs/in/zotero/xslt/zotero_atom_json.xsl';

		$proc = new XSLTProcessor();
		$xslDoc = new DOMDocument();
		$xslDoc->load($xsl_filename);
		$proc->registerPHPFunctions();
		$proc->importStylesheet($xslDoc);
		$out = $proc->transformToXml($xml);

		$ref = 0;
		if ($out) {

			//On a un enregistrement unimarc, on l'enregistre
			$rec_uni_dom=new xml_dom($out,"utf-8");

			if (!$rec_uni_dom->error) {

				//Initialisation
				$ref="";
				$ufield="";
				$usubfield="";
				$field_order=0;
				$subfield_order=0;
				$value="";
				$date_import=date('Y-m-d H:i:s');

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
					//Suppression anciennes notices
					$this->delete_from_entrepot($this->source_id, $ref);
					$this->delete_from_external_count($this->source_id, $ref);

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
						$this->insert_header_into_entrepot($this->source_id, $ref, $date_import, $hc, $code, $recid, $search_id);
					}

					for ($i=0; $i<count($fs); $i++) {
						$ufield=$fs[$i]["ATTRIBS"]["c"];
						$field_order=$i;
						$ss=$rec_uni_dom->get_nodes("s",$fs[$i]);
						if (is_array($ss)) {
							for ($j=0; $j<count($ss); $j++) {
								$usubfield=$ss[$j]["ATTRIBS"]["c"];
								$value=$rec_uni_dom->get_datas($ss[$j]);
								if($charset != "utf-8"){
									$value=encoding_normalize::utf8_decode($value);
								}
								$subfield_order=$j;
								$this->insert_content_into_entrepot($this->source_id, $ref, $date_import, $ufield, $usubfield, $field_order, $subfield_order, $value, $recid);
							}
						} else {
							$value=$rec_uni_dom->get_datas($fs[$i]);
							if($charset != "utf-8"){
								$value=encoding_normalize::utf8_decode($value);
							}
							$this->insert_content_into_entrepot($this->source_id, $ref, $date_import, $ufield, $usubfield, $field_order, $subfield_order, $value, $recid);
						}
					}
					$this->rec_isbd_record($this->source_id, $ref, $recid);
				}
			}
		}
		return $ref;
	}

	public function recurse_record($xml, $xml_elt, $key, $value) {

		if(is_array($value)) {
			if(count($value)) {
				foreach ($value as $k=>$v) {

					if (!is_numeric($key)) {
						$new_elt = $xml->createElement($key);
						$xml_elt->appendChild($new_elt);
	 					$this->recurse_record($xml, $new_elt, $k, $v);
					} else {
						$this->recurse_record($xml, $xml_elt, $k, $v);
					}

				}
			}

		} else {

			if(!is_numeric($key) && $value!='') {
				$new_elt = $xml->createElement($key, $value);
				$xml_elt->appendChild($new_elt);
			}
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::getSynchroForm()
	 */
	public function getSynchroForm($source_id, $sync_form = "sync_form")
	{
		global $form_from;
		global $form_until;
		global $form_radio;

		$source_id = intval($source_id);
		$sync_form = in_array($sync_form, [ "sync_form", "planificateur_form"]) ? $sync_form : "sync_form";

		$params=$this->get_source_params($source_id);
		$vars=unserialize($params['PARAMETERS']);

		$form = "<blockquote role='presentation'>";
		$form .= "</blockquote>";
		return $form;
	}

	public function maj_entrepot($source_id,$callback_progress='',$recover=false,$recover_env='') {
		global $charset;
		global $form_from, $form_until, $form_radio;

		$this->callback_progress = $callback_progress;
		$params = $this->unserialize_source_params($source_id);
		$p = $params['PARAMETERS'];
		$this->source_id = $source_id;
		$this->n_recu = 0;
		$this->n_total = 0;

		//Connexion
		$zot = new zotero_protocol($p,$charset);

		//Récupération des clés d'items
		$tab_items_keys = array();
		if (count($p['zotero_collections'])) {

			foreach($p['zotero_collections'] as $k=>$collection_key) {
				$tik = array();
				$tik = $zot->get_items_keys($collection_key);
				if (count($tik)) {
					$tab_items_keys = array_merge($tab_items_keys, $tik);
				}
			}

		} else {
			$tab_items_keys = $zot->get_items_keys();
		}

		//Nb items au total
		$this->n_total = count($tab_items_keys);

		//Récupération des items
		$tab_sync_items = array();
		foreach($tab_items_keys as $item_key) {
			$item = $zot->get_item($item_key);
			$si = $this->rec_record($item);
			if ($si) {
				$this->n_recu++;
				$this->progress();
				$tab_sync_items[]=$si;
			}
		}

		//Suppression des items non synchronisés
		$str_sync_items = '';
		if(count($tab_sync_items)) {
			$str_sync_items = implode('","',$tab_sync_items);
		}
		if($str_sync_items) {
			$q = "delete from entrepot_source_".$this->source_id." where ref not in (\"".$str_sync_items."\")";
			pmb_mysql_query($q);
		} else {
			$q = "delete from entrepot_source_".$this->source_id;
			pmb_mysql_query($q);
		}
		$q = "delete from external_count where source_id=".$this->source_id." and rid not in (select distinct recid from entrepot_source_".$this->source_id." )";
		pmb_mysql_query($q);

		return $this->n_recu;
	}

}


require_once("$class_path/curl.class.php");


class zotero_protocol {

	public $zotero_api_version = '2';
	public $zotero_api_url = "https://api.zotero.org";
	public $zotero_userid = '';
	public $zotero_client_key = '';

	public $channel = false;
	public $url = '';
	public $params = array();
	public $response = '';
	public $error = false;
	public $error_msg = '';

	public $result = false;
	public $charset = 'utf-8';
	public $zotero_parser = null;


	public function __construct($params=array(),$charset='utf-8') {

		$this->zotero_userid = $params['zotero_userid'];
		$this->zotero_client_key = $params['zotero_client_key'];
		$this->charset = $charset;
		$this->channel = new Curl();
		$this->channel->headers['Zotero-API-Version']=2;
		$this->zotero_parser = new zotero_parser();
	}


	public function send_request($other_params=array()) {

		$this->error = false;
		$this->params = array();
		$this->params['key'] = $this->zotero_client_key;
		$this->params['version'] = $this->zotero_api_version;

		if (is_array($other_params) && count($other_params)) {
			foreach($other_params as $k=>$v) {
				$this->params[$k]=$v;
			}
		}

		$rcurl = $this->channel->get($this->url,$this->params);
		if ($rcurl->headers['Status-Code']!='200') {
			$this->error = true;
			$this->error_msg = $rcurl->headers['Status'];
		} else {
			$this->response = $rcurl->body;
		}
	}


	public function get_collections() {

		$zp = $this->zotero_parser;
		$zp->reset('collections');
		$this->result = array();
		$this->url = $this->zotero_api_url."/users/".$this->zotero_userid."/collections?";

		$this->send_request();
		if (!$this->error) {
			try {
				$zpr = $zp->parse($this->response);
				if (count($zpr)) {
					foreach($zpr as $v) {
						$title = $v['title'];
						$id = $v['zapi:key'];
						if ($this->charset != 'utf-8') {
							$title = encoding_normalize::utf8_decode($title);
						}
						$this->result[$id] = $title;
					}
					natsort($this->result);
				}
			} catch (Exception $e) {
			}
		}
		return $this->result;
	}


	public function get_items_keys($collection_key='') {

		$this->result = array();
		$this->url = $this->zotero_api_url."/users/".$this->zotero_userid.(($collection_key)?"/collections/".$collection_key:'')."/items?format=keys&itemType=-attachment%20||%20note";
		$this->send_request();
		if (!$this->error) {
			$this->result = array_filter(explode(chr(0x0A),$this->response));
		}
		return $this->result;
	}


	public function get_childrens_keys($item_key='') {

		$this->result = array();
		if ($item_key) {
			$this->url = $this->zotero_api_url."/users/".$this->zotero_userid."/items/".$item_key."/children?format=keys";
			$this->send_request();
			if (!$this->error) {
				$this->result = array_filter(explode(chr(0x0A),$this->response));
			}
		}
		return $this->result;
	}


	public function get_children($item_key='') {

		$zp = $this->zotero_parser;
		$zp->reset('item');
		$this->result = array();
		if ($item_key) {
			$this->url = $this->zotero_api_url."/users/".$this->zotero_userid."/items/".$item_key."?content=json";
			$this->send_request();
			if (!$this->error) {
				try {
					$zpr = $zp->parse($this->response);
					if($zpr[0]['content']['itemType']=='attachment' && $zpr[0]['content']['linkMode']=='imported_file') {
						$zpr[0]['content']['url']=$this->zotero_api_url."/users/".$this->zotero_userid."/items/".$zpr[0]['zapi:key']."/file?key=".$this->zotero_client_key;
					}
					$this->result = $zpr[0];
				} catch(Exception $e) {}
			}
		}
		return $this->result;
	}


	public function get_item($item_key='') {

		$zp = $this->zotero_parser;
		$zp->reset('item');
		$this->result = array();
		if ($item_key) {
			$this->url = $this->zotero_api_url."/users/".$this->zotero_userid."/items/".$item_key."?content=json";
			$this->send_request();
			if (!$this->error) {
				try {
					$zpr = $zp->parse($this->response);
					if ($zpr[0]['zapi:numChildren']) {
						$childrens = $this->get_childrens_keys($zpr[0]['zapi:key']);
						foreach($childrens as $k=>$v) {
							$zpr[0]['attachments'][$k]=$this->get_children($v);
						}
					}
					$this->result = $zpr[0];

				} catch(Exception $e) {
				}
			}
		}
		return $this->result;
	}


	public function get_file($key) {
		$this->url = $this->zotero_api_url."/users/".$this->zotero_userid."/items/$key/file?";
		$this->send_request();
		if (!$this->error) {

		}
	}

}


class zotero_parser {

	public $parser;
	public $t=array();
	public $t_i=0;
	public $to_parse='item';
	public $prev_tag='';
	public $path_tag=array();
	public $text='';
	public $charset = 'utf-8';


	public function __construct ($to_parse='item', $charset='utf-8') {

		$this->charset = $charset;
		$this->to_parse = $to_parse;
	}


	public function reset($to_parse='item', $charset='utf-8') {

		$this->t = array();
		$this->t_i= 0;
		$this->to_parse = $to_parse;
		$this->prev_tag = '';
		$this->path_tag = array();
		$this->text = '';
		$this->charset = $charset;
	}


	public function parse ($xml) {

		$this->parser=xml_parser_create($this->charset);
		xml_set_object ($this->parser, $this);
		xml_parser_set_option ($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
		xml_parser_set_option ($this->parser, XML_OPTION_SKIP_WHITE, TRUE);
		xml_set_element_handler ($this->parser, 'tag_start', 'tag_end');
		xml_set_character_data_handler ($this->parser, 'texte');

		if ( !xml_parse ($this->parser, $xml, TRUE)) {
			die (sprintf ("erreur XML %s à la ligne: %d", xml_error_string (xml_get_error_code ($this->parser)), xml_get_current_line_number ($this->parser)));
		}
		xml_parser_free ($this->parser);
		unset($this->parser);
		return ($this->t);
	}


	public function tag_start ($parser, $tag, $att) {
		$this->prev_tag=end($this->path_tag);
		$this->path_tag[]=$tag;
		$this->text='';
	}


	public function tag_end ($parser, $tag) {
		if ( !count ($this->path_tag)) return;
		$this->text=trim($this->text);

		switch ($this->to_parse) {

			case 'collections' :
				switch ($tag) {
					case 'entry' :
						$this->t_i++;
						break;
					case 'title' :
						if ($this->prev_tag=='entry' && $this->text!=='') {
							$this->t[$this->t_i][$tag] = $this->text;
						}
						break;
					case 'zapi:key' :
						if ($this->prev_tag=='entry' && $this->text!=='') {
							$this->t[$this->t_i][$tag] = $this->text;
						}
						break;
					default :
						break;

				}
				break;

			case 'item' :
				switch ($tag) {
					case 'zapi:key' :
					case 'zapi:version' :
					case 'zapi:numChildren' :
						if ($this->text!=='') {
							$this->t[$this->t_i][$tag] = $this->text;
						}
						break;
					case 'content' :
						if ($this->text!=='') {
							$this->t[$this->t_i][$tag] = json_decode($this->text, true);
						}
						break;
					default :
						break;

				}
				break;
		}

		array_pop ($this->path_tag);
	}


	public function texte ($parser, $data) {

		if ( !count ($this->path_tag))
			return;
		$this->text.=$data;
	}

}

?>