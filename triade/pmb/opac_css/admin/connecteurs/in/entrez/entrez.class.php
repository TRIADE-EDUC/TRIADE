<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entrez.class.php,v 1.22.2.1.2.2 2025/05/07 14:25:38 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
if (version_compare(PHP_VERSION,'5','>=') && extension_loaded('xsl')) {
	require_once($include_path.'/xslt-php4-to-php5.inc.php');
}

require_once($class_path."/connecteurs.class.php");
require_once("pubmed_analyse_query.class.php");
require_once($class_path."/curl.class.php");
/**There be komodo dragons**/

class entrez extends connector {

	public  $available_entrezdatabases = array("pubmed" => "PubMed");
	protected $base_url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/';
	public $n_recu;				//Nombre de notices reçues


    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
    	return "entrez";
    }

    public function source_get_property_form($source_id) {
    	global $charset, $xsl_transform;

    	$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		if (!isset($entrez_database))
			$entrez_database = "pubmed";

		if (!isset($entrez_maxresults))
			$entrez_maxresults = 100;
		$entrez_maxresults = intval($entrez_maxresults);

		if (!isset($entrez_operator))
			$entrez_operator = 2;
		$entrez_operator = intval($entrez_operator);

		$options = "";
		foreach ($this->get_available_entrezdatabases() as $code => $caption)
			$options .= '<option value="'.$code.'" '.($code == $entrez_database ? "selected" : "").'>'.htmlentities($caption, ENT_QUOTES, $charset).'</option>';

		$form="<div class='row'>
			<div class='colonne3'>
				<label for='url'>".$this->msg["entrez_database"]."</label>
			</div>
			<div class='colonne_suite'>
				<select name=\"entrez_database\">
					".$options."
				</select>
			</div>
		</div>";
		$form.="<div class='row'>
			<div class='colonne3'>
				<label for='operator'>".$this->msg["entrez_operator"]."</label>
			</div>
			<div class='colonne_suite'>
				<select name=\"entrez_operator\">
					<option value='2' ".($entrez_operator == 2 ? "selected" : "").">".$this->msg["entrez_operator_default"]."</option>
					<option value='0' ".($entrez_operator == 0 ? "selected" : "").">".$this->msg["entrez_operator_or"]."</option>
					<option value='1' ".($entrez_operator == 1 ? "selected" : "").">".$this->msg["entrez_operator_and"]."</option>
				</select>
			</div>
		</div>";
		$form.="<div class='row'>
			<div class='colonne3'>
				<label for='url'>".$this->msg["entrez_maxresults"]."</label>
			</div>
			<div class='colonne_suite'>
				<input name=\"entrez_maxresults\" type=\"text\" value=\"".$entrez_maxresults."\">
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='xslt_file'>".$this->msg["entrez_xslt_file"]."</label>
			</div>
			<div class='colonne_suite'>
				<input name='xslt_file' type='file'/>";
		if ($xsl_transform) $form.="<br /><i>".sprintf($this->msg["entrez_xslt_file_linked"],$xsl_transform["name"])."</i> : ".$this->msg["entrez_del_xslt_file"]." <input type='checkbox' name='del_xsl_transform' value='1'/>";
		 $form.="	</div>
		</div>
		<div class='row'></div>
";
		return $form;
    }

    public function make_serialized_source_properties($source_id) {
    	global $entrez_database, $entrez_maxresults, $entrez_operator;
    	global $del_xsl_transform;

    	$t=array();
    	$t["entrez_database"] = stripslashes($entrez_database);
    	$t["entrez_maxresults"] = (int) $entrez_maxresults;
    	$t["entrez_operator"] = (int) $entrez_operator;

    	//Vérification du fichier
    	if (($_FILES["xslt_file"])&&(!$_FILES["xslt_file"]["error"])) {
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
			}
	  		$t["xsl_transform"] = $oldvars["xsl_transform"];
    	}

		$this->sources[$source_id]["PARAMETERS"]=serialize($t);
	}

	//Fonction de recherche
	public function search($source_id,$query,$search_id) {
		global $base_path;
		global $pmb_default_operator;

		$params=$this->get_source_params($source_id);
		$this->fetch_global_properties();
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		if (!isset($entrez_database)) {
			$this->error_message = $this->msg["entrez_unconfigured"];
			$this->error = 1;
			return;
		}
		$entrez_operator = (int) $entrez_operator;
		$entrez_maxresults = (int) $entrez_maxresults;

		$unimarc_pubmed_mapping = array (
			'XXX' => '',
			'200$a' => '[Title]',
			'7XX' => '[Author]',
			'210$c' => '[Publisher]',
			'210$d' => '[Publication Date]',
			'461$t' => '[Journal]'
		);

		$pubmed_stopword = array(
			"a","about","again", "all", "almost", "also", "although", "always", "among", "an", "and", "another", "any", "are", "as", "at",
			"be", "because", "been", "before", "being", "between", "both","but", "by",
			"can", "could",
			"did", "do", "does", "done", "due", "during",
			"each", "either", "enough", "especially", "etc",
			"for", "found", "from", "further",
			"had", "has", "have", "having", "here", "how", "however",
			"i", "if", "in", "into", "is", "it", "its", "itself",
			"just",
			"kg", "km",
			"made", "mainly", "make", "may", "mg", "might", "ml", "mm", "most", "mostly", "must",
			"nearly", "neither", "no", "nor",
			"obtained", "of", "often", "on", "our", "overall",
			"perhaps", "pmid",
			"quite",
			"rather", "really", "regarding",
			"seem", "seen", "several", "should", "show", "showed", "shown", "shows", "significantly", "since", "so", "some", "such",
			"than", "that", "the", "their", "theirs", "them", "then", "there", "therefore", "these", "they", "this", "those", "through", "thus", "to",
			"upon", "use", "used", "using",
			"various", "very",
			"was", "we", "were", "what", "when", "which", "while", "with", "within", "without", "would"
		);

		$search_query = "";
		foreach($query as $aquery){
			$search_querys = array();
			if($entrez_operator != 2){
				$operator = $pmb_default_operator;
				$pmb_default_operator = $entrez_operator;
			}
			$field= (isset($unimarc_pubmed_mapping[$aquery->ufield]) ? $unimarc_pubmed_mapping[$aquery->ufield] : '');
			$a=new pubmed_analyse_query($aquery->values[0],0,0,1,0,$field,$pubmed_stopword);
			$sub_search_query =$a->show_analyse();
			if($entrez_operator != 2) $pmb_default_operator=$operator;
			if ($search_query) $search_query .= " " . strtoupper($aquery->inter) . " " . $sub_search_query;
			else $search_query = $sub_search_query;
		}

		require_once 'entrez_protocol.class.php';
		$entrez_client = new entrez_request($entrez_database, $search_query);
		$entrez_client->get_next_idlist($entrez_maxresults);
		$entrez_client->retrieve_currentidlist_notices();
		$responses = $entrez_client->get_current_responses();

		if($xsl_transform){
			if($xsl_transform['code'])
				$xsl_transform_content = $xsl_transform['code'];
			else $xsl_transform_content = "";
		}
		if($xsl_transform_content == "")
			$xsl_transform_content = file_get_contents($base_path."/admin/connecteurs/in/entrez/xslt/pubmed_to_unimarc.xsl");

		$notices = $this->apply_xsl_to_xml($responses, $xsl_transform_content);

		$this->rec_records($notices, $source_id, $search_id, $search_query);
	}

	public function rec_records($noticesxml, $source_id, $search_id, $search_term="") {
		global $charset;

		if (!isset($noticesxml) || !trim($noticesxml)) {
			return;
		}

		$rec_uni_dom = new xml_dom_entrez($noticesxml,$charset);
		$notices = $rec_uni_dom->get_nodes("unimarc/notice");

		foreach ($notices as $notice) {
			$this->rec_record($rec_uni_dom, $notice, $source_id, $search_id, $search_term);
		}
	}

	public function rec_record($rec_uni_dom, $noticenode, $source_id, $search_id, $search_term="") {
		global $base_path;

		if (!$rec_uni_dom->error) {
			//Initialisation
			$ref="";
			$ufield="";
			$usubfield="";
			$field_order=0;
			$subfield_order=0;
			$value="";
			$date_import=date("Y-m-d H:i:s",time());

			$fs=$rec_uni_dom->get_nodes("f", $noticenode);

			$fs[] = array("NAME" => "f", "ATTRIBS" => array("c" => "1000"), 'TYPE' => 1, "CHILDS" => array(array("DATA" => $search_term, "TYPE" => 2)));
			//Recherche du 001
			if ($fs)
				for ($i=0; $i<count($fs); $i++) {
					if ($fs[$i]["ATTRIBS"]["c"]=="001") {
						$ref=$rec_uni_dom->get_datas($fs[$i]);
						break;
					}
				}
			if (!$ref) $ref = md5($record);
			//Mise à jour
			if ($ref) {
				//Si conservation des anciennes notices, on regarde si elle existe
				if (!$this->del_old) {
					$ref_exists = $this->has_ref($source_id, $ref);
				}
				//Si pas de conservation des anciennes notices, on supprime
				if ($this->del_old) {
					$this->delete_from_entrepot($source_id, $ref);
					$this->delete_from_external_count($source_id, $ref);
				}
				$ref_exists = false;
				//Si pas de conservation ou refï¿½rence inexistante
				if (($this->del_old)||((!$this->del_old)&&(!$ref_exists))) {
					//Insertion de l'entï¿½te
					$n_header=array();
					$n_header["rs"]=$rec_uni_dom->get_value("rs", $noticenode);
					$n_header["ru"]=$rec_uni_dom->get_value("ru", $noticenode);
					$n_header["el"]=$rec_uni_dom->get_value("el", $noticenode);
					$n_header["bl"]=$rec_uni_dom->get_value("bl", $noticenode);
					$n_header["hl"]=$rec_uni_dom->get_value("hl", $noticenode);
					$n_header["dt"]=$rec_uni_dom->get_value("dt", $noticenode);

					//Récupération d'un ID
					$recid = $this->insert_into_external_count($source_id, $ref);

					$values = array();
					foreach($n_header as $hc=>$code) {
						$values[] = "('".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
						'".$hc."','',-1,0,'".addslashes($code)."','',$recid, '$search_id')";
					}
					$requete="insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values ";
					$requete.= implode(",", $values);
					pmb_mysql_query($requete);
					if ($fs) {
						$values = array();
						for ($i=0; $i<count($fs); $i++) {
							$ufield=$fs[$i]["ATTRIBS"]["c"];
							$field_order=$i;
							$ss=$rec_uni_dom->get_nodes("s",$fs[$i]);
							if (is_array($ss)) {
								for ($j=0; $j<count($ss); $j++) {
									$usubfield=$ss[$j]["ATTRIBS"]["c"];
									$value=$rec_uni_dom->get_datas($ss[$j]);
									$subfield_order=$j;
									$values[] = "(
									'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
									'".addslashes($ufield)."','".addslashes($usubfield)."',".$field_order.",".$subfield_order.",'".addslashes($value)."',
									' ".addslashes(strip_empty_words($value))." ',$recid, '$search_id')";
								}
							} else {
								$value=$rec_uni_dom->get_datas($fs[$i]);
								$values[] = "(
								'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
								'".addslashes($ufield)."','".addslashes($usubfield)."',".$field_order.",".$subfield_order.",'".addslashes($value)."',
								' ".addslashes(strip_empty_words($value))." ',$recid, '$search_id')";
							}
						}
						$requete="insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values ";
						$requete.= implode(",", $values);
						pmb_mysql_query($requete);
					}
					$this->rec_isbd_record($source_id, $ref, $recid);
					$this->insert_human_query_into_entrepot($source_id, $ref, $date_import, $search_term, $recid, $search_id);
				}
				$this->n_recu++;
			}
		}
	}

	protected function get_available_entrezdatabases () {
		$this->available_entrezdatabases = array();

		$curl =  new Curl();
		$url = $this->base_url . "einfo.fcgi";
		$result = $curl->get($url);

		$params = _parser_text_no_function_($result->body,"EINFORESULT");

		if (isset($params["DBLIST"][0]["DBNAME"])) {

			foreach ($params["DBLIST"][0]["DBNAME"] as $DB) {
				$this->available_entrezdatabases[$DB["value"]] = $DB["value"];
			}
		}

		return $this->available_entrezdatabases;
	}

}

?>