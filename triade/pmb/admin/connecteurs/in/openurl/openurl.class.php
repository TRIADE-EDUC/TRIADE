<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl.class.php,v 1.11.8.1 2025/04/16 12:16:51 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/openurl/openurl_instance.class.php");
require_once($class_path."/openurl/openurl_parameters.class.php");
require_once($include_path."/parser.inc.php");

class openurl extends connector {

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
    public function get_id()
    {
    	return "openurl";
    }

    public function source_get_property_form($source_id) {
		global $charset,$base_path;

		$params=$this->get_source_params($source_id);
		if($params["PARAMETERS"]!=""){
		//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}else{
			$file_params = file_get_contents($base_path."/admin/connecteurs/in/openurl/conf.xml");
			$params = _parser_text_no_function_($file_params, "CONFIGURATION");
			foreach($params as $section => $parameters){
				switch($section){
					case "TRANSPORT" :
						$protocole = $parameters[0]['PROTOCOLE'][0]['value'];
						$method=$parameters[0]['METHOD'][0]['value'];
	    				$tparameters=$parameters[0]['PARAMETERS'][0]['value'];
						break;
					case "SERIALIZATION" :
						$serialization= $parameters[0]['value'];
						break;
					case "ENTITIES" :
						foreach($parameters[0]['ENTITY'] as $entity){
							switch($entity['TYPE']){
								case "referent":
									foreach($entity['IDENTIFIERS'][0]['IDENTIFIER'] as $identifier){
										switch($identifier['NAME']){
											case "issn" :
												$rft_issn=$identifier['ALLOW'] == "yes" ? 1:0;
												break;
											case "isbn" :
												$rft_isbn=$identifier['ALLOW'] == "yes" ? 1:0;
												break;
											case "pmid" :
												$rft_pmid=$identifier['ALLOW'] == "yes" ? 1:0;
												break;
											case "doi" :
												$rft_doi=$identifier['ALLOW'] == "yes" ? 1:0;
												break;
										}
									}
							    	$rft_byval=$entity['BYVALUE'][0]['ALLOW'] == "yes" ? 1:0;
							    	$rft_byref=$entity['BYREFERENCE'][0]['ALLOW'] == "yes" ? 1:0;
							    	$rft_private=$entity['PRIVATE'][0]['ALLOW'] == "yes" ? 1:0;
									break;
								case "referring_entity":
									$rfe_allow=$entity['ALLOW'] == "yes" ? 1:0;
	    							foreach($entity['IDENTIFIERS'][0]['IDENTIFIER'] as $identifier){
										switch($identifier['NAME']){
											case "issn" :
												$rfe_issn=$identifier['ALLOW'] == "yes" ? 1:0;
												break;
											case "isbn" :
												$rfe_isbn=$identifier['ALLOW'] == "yes" ? 1:0;
												break;
											case "pmid" :
												$rfe_pmid=$identifier['ALLOW'] == "yes" ? 1:0;
												break;
											case "doi" :
												$rfe_doi=$identifier['ALLOW'] == "yes" ? 1:0;
												break;
										}
									}
							    	$rfe_byval=$entity['BYVALUE'][0]['ALLOW'] == "yes" ? 1:0;
							    	$rfe_byref=$entity['BYREFERENCE'][0]['ALLOW'] == "yes" ? 1:0;
							    	$rfe_private=$entity['PRIVATE'][0]['ALLOW'] == "yes" ? 1:0;
									break;
								case "requester":
									$req_allow=$entity['ALLOW'] == "yes" ? 1:0;
									$req_parameter=$entity['PARAMETER'][0]['value'];
									break;
								case "service_type":
									$svc_allow=$entity['ALLOW'] == "yes" ? 1:0;
									$svc_services = array();
									foreach($entity['SERVICE'] as $service){
										$svc_services[] =array(
											'name' => $service['NAME'],
											'value'=> $service['DEFAULT_VALUE']
										);
									}
									break;
								case "resolver":
									$res_allow=$entity['ALLOW'] == "yes" ? 1:0;
	    							$res_parameter=$entity['PARAMETER'][0]['value'];
									break;
								case "referrer":
									$rfr_allow=$entity['ALLOW'] == "yes" ? 1:0;
	    							$rfr_parameter=$entity['PARAMETER'][0]['value'];
									break;
							}
						}
						break;
				}
			}
		}

		if (!isset($libelle))
			$libelle = "OpenURL";
		if (!isset($source_name))
			$source_name = "OpenURL";
		if (!isset($iwidth))
			$iwidth = "640";
		if (!isset($iheight))
			$iheight = "350";
		if (!isset($infobulle))
			$infobulle = "";
		if (!isset($byref_url))
			$byref_url = '';

		$result = "";
//		$result ="
//			<div class='row'>&nbsp;</div>
//			<div class='row'>
//				<div class='colonne3'>
//					<label for='conf_file'>".$this->msg["openurl_conf_file"]."</label>
//				</div>
//				<div class='colonne_suite'>
//					<input type='file' name='conf_file'/>
//				</div>
//			</div>";

		//VISUEL
		$form_visuel ="
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne3'><label for='libelle'>".$this->msg["openurl_libelle"]."</label></div>
				<div class='colonne-suite'><input type='text' name='libelle' value='".htmlentities($libelle,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='infobulle'>".$this->msg["openurl_infobulle"]."</label></div>
				<div class='colonne-suite'><input type='text' name='infobulle' value='".htmlentities($infobulle,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='iwidth'>".$this->msg["openurl_iwidth"]."</label></div>
				<div class='colonne-suite'><input type='text' name='iwidth' value='".htmlentities($iwidth,ENT_QUOTES,$charset)."'/></div>
				<div class='colonne3'><label for='iheight'>".$this->msg["openurl_iheight"]."</label></div>
				<div class='colonne-suite'><input type='text' name='iheight' value='".htmlentities($iheight,ENT_QUOTES,$charset)."'/></div>
				<div class='colonne3'><label for='source_name'>".$this->msg["openurl_source_name"]."</label></div>
				<div class='colonne-suite'><input type='text' name='source_name' value='".htmlentities($source_name,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>&nbsp;</div>";
		$result.= gen_plus("form_opac",$this->msg['openurl_form_param_visuel'],$form_visuel,1);


		//TRANSPORT
		$form_transport	="
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne3'><label for='protocole'>".$this->msg["openurl_protocole"]."</label></div>
				<div class='colonne-suite'>
					<select name ='protocole'>
						<option value='http' ".($protocole == "http" ? "selected='selected'": "").">".$this->msg["openurl_protocole_http"]."</option>
						<option value='https' ".($protocole == "https" ? "selected='selected'": "").">".$this->msg["openurl_protocole_https"]."</option>
					</select>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='method'>".$this->msg["openurl_method"]."</label></div>
				<div class='colonne-suite'>
					<select name='method'>
						<option value='byval' ".($method == "byval" ? "selected=\"selected\"": "").">".$this->msg["openurl_method_byval"]."</option>
						<option value='byref' ".($method == "byref" ? "selected=\"selected\"": "").">".$this->msg["openurl_method_byref"]."</option>
						<option value='inline' ".($method == "inline" ? "selected=\"selected\"": "").">".$this->msg["openurl_method_inline"]."</option>
					</select>
				</div>
				<div class='colonne3'><label for='tparameters'>".$this->msg["openurl_tparameters"]."</label></div>
				<div class='colonne-suite'><input type='text' name='tparameters' value='".htmlentities($tparameters,ENT_QUOTES,$charset)."'/></div>
				<div class='colonne3'><label for='byref_url'>".$this->msg["openurl_byref_url"]."</label></div>
				<div class='colonne-suite'><input type='text' name='byref_url' value='".htmlentities($byref_url,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>&nbsp;</div>";
		//TODO : voir pour genre plus générique tparameters... doit pouvoir renvoyer une chaine ou un tableau...
		$result.= gen_plus("form_transport",$this->msg['openurl_form_param_transport'],$form_transport,1);

		$form_serialize ="
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne3'><label for='serialization'>".$this->msg["openurl_serialize"]."</label></div>
				<div class='colonne-suite'>
					<span>".$this->msg['openurl_serialization_kev']."&nbsp;<input type='radio' name='serialization' value='kev' ".($serialization == "kev" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
					<span>".$this->msg['openurl_serialization_xml']."&nbsp;<input type='radio' name='serialization' value='xml' ".($serialization == "xml" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
				</div>
			</div>
			<div class='row'>&nbsp;</div>";
		$result.= gen_plus("form_serialize",$this->msg['openurl_form_param_serialization'],$form_serialize,1);

		$form_entities = "
			<div class='row'>&nbsp;</div>
			<div class='row'>";

		//REFERENT
		$referent="
					<table >
						<tr>
							<th style='text-align:center;'>".$this->msg['openurl_descriptors_type']."</th>
							<th style='text-align:center;' >".$this->msg['openurl_descriptors_stype']."</th>
							<th style='text-align:center;'>".$this->msg['openurl_descriptors_stype_allow']."</th>
						</tr>
						<tr class='even'>
							<td style='text-align:center;' rowspan='4'>".$this->msg['openurl_descriptors_identifier']."</td>
							<td style='text-align:center;' >
								".$this->msg['openurl_descriptors_identifier_isbn']."
							</td>
							<td style='text-align:center;' >
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rft_isbn' value='1' ".($rft_isbn == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rft_isbn' value='0' ".($rft_isbn == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='even'>
							<td style='text-align:center;' >
								".$this->msg['openurl_descriptors_identifier_issn']."
							</td>
							<td style='text-align:center;' >
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rft_issn' value='1' ".($rft_issn == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rft_issn' value='0' ".($rft_issn == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='even'>
							<td style='text-align:center;' >
								".$this->msg['openurl_descriptors_identifier_pmid']."
							</td>
							<td style='text-align:center;' >
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rft_pmid' value='1' ".($rft_pmid == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rft_pmid' value='0' ".($rft_pmid == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='even'>
							<td style='text-align:center;' >
								".$this->msg['openurl_descriptors_identifier_doi']."
							</td>
							<td style='text-align:center;' >
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rft_doi' value='1' ".($rft_doi == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rft_doi' value='0' ".($rft_doi == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='odd'>
							<td style='text-align:center;'>".$this->msg['openurl_descriptors_byval']."</td>
							<td></td>
							<td style='text-align:center;'>
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rft_byval' value='1' ".($rft_byval == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rft_byval' value='0' ".($rft_byval == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='even'>
							<td style='text-align:center;'>".$this->msg['openurl_descriptors_byref']."</td>
							<td></td>
							<td style='text-align:center;'>
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rft_byref' value='1' ".($rft_byref == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rft_byref' value='0' ".($rft_byref == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='odd'>
							<td style='text-align:center;'>".$this->msg['openurl_descriptors_private']."</td>
							<td></td>
							<td style='text-align:center;'>
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rft_private' value='1' ".($rft_private == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rft_private' value='0' ".($rft_private == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
					</table>";
		$form_entities.= gen_plus("referent",$this->msg['openurl_entities_referent'],$referent,1);

		//REFERRING ENTITY
		$referring_entity="
				<div class='row'>&nbsp;</div>
				<div class='colonne3'><label for='rfe_allow'>".$this->msg["openurl_entity_allow"]."</label></div>
				<div class='colonne-suite'>
					<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rfe_allow' value='1' ".($rfe_allow == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
					<span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rfe_allow' value='0' ".($rfe_allow == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
				</div>
				<div class='row'>&nbsp;</div>
					<table >
						<tr>
							<th style='text-align:center;'>".$this->msg['openurl_descriptors_type']."</th>
							<th style='text-align:center;' >".$this->msg['openurl_descriptors_stype']."</th>
							<th style='text-align:center;'>".$this->msg['openurl_descriptors_stype_allow']."</th>
						</tr>
						<tr class='even'>
							<td style='text-align:center;' rowspan='4'>".$this->msg['openurl_descriptors_identifier']."</td>
							<td style='text-align:center;' >
								".$this->msg['openurl_descriptors_identifier_isbn']."
							</td>
							<td style='text-align:center;' >
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rfe_isbn' value='1' ".($rfe_isbn == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rfe_isbn' value='0' ".($rfe_isbn == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='even'>
							<td style='text-align:center;'>
								".$this->msg['openurl_descriptors_identifier_issn']."
							</td>
							<td style='text-align:center;'>
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rfe_issn' value='1' ".($rfe_issn == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rfe_issn' value='0' ".($rfe_issn == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
							</td>
						</tr>
						<tr class='even'>
							<td style='text-align:center;' >
								".$this->msg['openurl_descriptors_identifier_pmid']."
							</td>
							<td style='text-align:center;' >
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rfe_pmid' value='1' ".($rfe_pmid == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rfe_pmid' value='0' ".($rfe_pmid == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='even'>
							<td style='text-align:center;' >
								".$this->msg['openurl_descriptors_identifier_doi']."
							</td>
							<td style='text-align:center;' >
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rfe_doi' value='1' ".($rfe_doi == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rfe_doi' value='0' ".($rfe_doi == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='odd'>
							<td style='text-align:center;'>".$this->msg['openurl_descriptors_byval']."</td>
							<td></td>
							<td style='text-align:center;'>
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rfe_byval' value='1' ".($rfe_byval == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rfe_byval' value='0' ".($rfe_byval == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='even'>
							<td style='text-align:center;'>".$this->msg['openurl_descriptors_byref']."</td>
							<td></td>
							<td style='text-align:center;'>
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rfe_byref' value='1' ".($rfe_byref == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rfe_byref' value='0' ".($rfe_byref == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
						<tr class='odd'>
							<td style='text-align:center;'>".$this->msg['openurl_descriptors_private']."</td>
							<td></td>
							<td style='text-align:center;'>
								<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rfe_private' value='1' ".($rfe_private == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rfe_private' value='0' ".($rfe_private == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span><br/>
							</td>
						</tr>
					</table>";
		$form_entities.= gen_plus("referring_entity",$this->msg['openurl_entities_referring_entity'],$referring_entity,1);

		//REQUESTER
		$requester = "
				<div class='row'>&nbsp;</div>
				<div class='colonne3'><label for='req_allow'>".$this->msg["openurl_entity_allow"]."</label></div>
				<div class='colonne-suite'>
					<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='req_allow' value='1' ".($req_allow == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
					<span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='req_allow' value='0' ".($req_allow == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
				</div>
				<div class='colonne3'><label for='req_parameter'>".$this->msg['openurl_requester_param']."</label></div>
				<div class='colonne-suite'><input type='text' name='req_parameter' value='".htmlentities($req_parameter,ENT_QUOTES,$charset)."'/></div>
				<div class='row'>&nbsp;</div>";
		$form_entities.= gen_plus("requester",$this->msg['openurl_entities_requester'],$requester,1);

		//SERVICE TYPE
		$service_type = "
				<div class='row'>&nbsp;</div>
				<div class='colonne3'><label for='svc_allow'>".$this->msg["openurl_entity_allow"]."</label></div>
				<div class='colonne-suite'>
					<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='svc_allow' value='1' ".($req_allow == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
					<span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='svc_allow' value='0' ".($req_allow == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
				</div>
				<table >
					<tr>
						<th style='text-align:center;'>".$this->msg['openurl_service']."</th>
						<th style='text-align:center;' >".$this->msg['openurl_service_use']."</th>
					</tr>";
		for($i=0 ; $i<count($svc_services) ; $i++){
			$service_type.="
					<tr class='".($i%2 ? "odd":"even")."'>
						<td style='text-align:center;'>".htmlentities($svc_services[$i]['name'],ENT_QUOTES,$charset)."<input type='hidden' name='svc_services[$i][name]' value='".htmlentities($svc_services[$i]['name'],ENT_QUOTES,$charset)."'</td>
						<td style='text-align:center;'>
							<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='svc_services[$i][value]' value='1' ".($svc_services[$i]['value'] == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
							<span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='svc_services[$i][value]' value='0' ".($svc_services[$i]['value'] == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
						</td>
					</tr>";
		}
		$service_type.="
				</table>
				<div class='row'>&nbsp;</div>";
		$form_entities.= gen_plus("service_type",$this->msg['openurl_entities_service_type'],$service_type,1);

		//RESOLVER
		$resolver = "
				<div class='row'>&nbsp;</div>
				<div class='colonne3'><label for='res_allow'>".$this->msg['openurl_entity_allow']."</label></div>
				<div class='colonne-suite'>
					<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='res_allow' value='1' ".($res_allow == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
					<span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='res_allow' value='0' ".($res_allow == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
				</div>
				<div class='colonne3'><label for='res_parameter'>".$this->msg['openurl_resolver_param']."</label></div>
				<div class='colonne-suite'><input type='text' name='res_parameter' value='".htmlentities($res_parameter,ENT_QUOTES,$charset)."'/></div>
				<div class='row'>&nbsp;</div>";
		$form_entities.= gen_plus("resolver",$this->msg['openurl_entities_resolver'],$resolver,1);

		//REFERRER
		$referrer = "
				<div class='row'>&nbsp;</div>
				<div class='colonne3'><label for='rfr_allow'>".$this->msg["openurl_entity_allow"]."</label></div>
				<div class='colonne-suite'>
					<span>".$this->msg['openurl_yes']."&nbsp;<input type='radio' name='rfr_allow' value='1' ".($rfr_allow == "1" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
					<span>".$this->msg['openurl_no']."&nbsp;<input type='radio' name='rfr_allow' value='0' ".($rfr_allow == "0" ? "checked='checked' ": "")."style='vertical-align:bottom;' /></span>
				</div>
				<div class='colonne3'><label for='rfr_parameter'>".$this->msg['openurl_referrer_param']."</label></div>
				<div class='colonne-suite'><input type='text' name='rfr_parameter' value='".htmlentities($rfr_parameter,ENT_QUOTES,$charset)."'/></div>
				<div class='row'>&nbsp;</div>
		";
		$form_entities.= gen_plus("referrer",$this->msg['openurl_entities_referrer'],$referrer,1);
		$result.= gen_plus("form_entities",$this->msg['openurl_form_param_entities'],$form_entities,1);


		$result.="
			<div class='row'>&nbsp;</div>
			<script type='text/javascript' src='javascript/tablist.js'></script>";
		return $result;
    }

    public function make_serialized_source_properties($source_id) {
    	global $iwidth,$iheight,$libelle,$source_name,$infobulle;
    	global $protocole,$method,$tparameters,$byref_url;
    	global $serialization;
    	global $rft_isbn,$rft_issn,$rft_doi,$rft_pmid,$rft_byval,$rft_byref,$rft_private;
    	global $rfe_allow,$rfe_isbn,$rfe_issn,$rfe_doi,$rfe_pmid,$rfe_byval,$rfe_byref,$rfe_private;
    	global $req_allow,$req_parameter;
    	global $svc_allow,$svc_services;
    	global $res_allow,$res_parameter;
    	global $rfr_allow,$rfr_parameter;

    	$t = array();
    	$t['libelle'] = $libelle ? stripslashes($libelle) : "OpenURL";
    	$t['source_name'] = $source_name ? stripslashes($source_name) : "OpenURL";
    	$t['iwidth'] = (int) $iwidth;
    	$t['iheight'] = (int) $iheight;
    	$t['infobulle'] = $infobulle ? stripslashes($infobulle) : "";
//      	if (($_FILES["conf_file"])&&(!$_FILES["conf_file"]["error"])) {
//			$file_params = file_get_contents($_FILES["conf_file"]["tmp_name"]);
//			$params = _parser_text_no_function_($file_params, "CONFIGURATION");
//			foreach($params as $section => $parameters){
//				switch($section){
//					case "TRANSPORT" :
//						$t['protocole'] = $parameters[0]['PROTOCOLE'][0]['value'];
//						$t['method']=$parameters[0]['METHOD'][0]['value'];
//	    				$t['tparameters']=$parameters[0]['PARAMETERS'][0]['value'];
//						break;
//					case "SERIALIZATION" :
//						$t['serialization']= $parameters[0]['value'];
//						break;
//					case "ENTITIES" :
//						foreach($parameters[0]['ENTITY'] as $entity){
//							switch($entity['TYPE']){
//								case "referent":
//									foreach($entity['IDENTIFIERS'][0]['IDENTIFIER'] as $identifier){
//										switch($identifier['NAME']){
//											case "issn" :
//												$t['rft_issn']=$identifier['ALLOW'] == "yes" ? 1:0;
//												break;
//											case "isbn" :
//												$t['rft_isbn']=$identifier['ALLOW'] == "yes" ? 1:0;
//												break;
//											case "pmid" :
//												$t['rft_pmid']=$identifier['ALLOW'] == "yes" ? 1:0;
//												break;
//											case "doi" :
//												$t['rft_doi']=$identifier['ALLOW'] == "yes" ? 1:0;
//												break;
//										}
//									}
//							    	$t['rft_byval']=$entity['BYVALUE'][0]['ALLOW'] == "yes" ? 1:0;
//							    	$t['rft_byref']=$entity['BYREFERENCE'][0]['ALLOW'] == "yes" ? 1:0;
//							    	$t['rft_private']=$entity['PRIVATE'][0]['ALLOW'] == "yes" ? 1:0;
//									break;
//								case "referring_entity":
//									$t['rfe_allow']=$entity['ALLOW'] == "yes" ? 1:0;
//	    							foreach($entity['IDENTIFIERS'][0]['IDENTIFIER'] as $identifier){
//										switch($identifier['NAME']){
//											case "issn" :
//												$t['rfe_issn']=$identifier['ALLOW'] == "yes" ? 1:0;
//												break;
//											case "isbn" :
//												$t['rfe_isbn']=$identifier['ALLOW'] == "yes" ? 1:0;
//												break;
//											case "pmid" :
//												$t['rfe_pmid']=$identifier['ALLOW'] == "yes" ? 1:0;
//												break;
//											case "doi" :
//												$t['rfe_doi']=$identifier['ALLOW'] == "yes" ? 1:0;
//												break;
//										}
//									}
//							    	$t['rfe_byval']=$entity['BYVALUE'][0]['ALLOW'] == "yes" ? 1:0;
//							    	$t['rfe_byref']=$entity['BYREFERENCE'][0]['ALLOW'] == "yes" ? 1:0;
//							    	$t['rfe_private']=$entity['PRIVATE'][0]['ALLOW'] == "yes" ? 1:0;
//									break;
//								case "requester":
//									$t['req_allow']=$entity['ALLOW'] == "yes" ? 1:0;
//									$t['req_parameter']=$entity['PARAMETER'][0]['value'];
//									break;
//								case "service_type":
//									$t['svc_allow']=$entity['ALLOW'] == "yes" ? 1:0;
//									$t['svc_services'] = array();
//									foreach($entity['SERVICE'] as $service){
//										$t['svc_services'][] =array(
//											'name' => $service['NAME'],
//											'value'=> $service['DEFAULT_VALUE']
//										);
//									}
//									break;
//								case "resolver":
//									$t['res_allow']=$entity['ALLOW'] == "yes" ? 1:0;
//	    							$t['res_parameter']=$entity['PARAMETER'][0]['value'];
//									break;
//								case "referrer":
//									$t['rfr_allow']=$entity['ALLOW'] == "yes" ? 1:0;
//	    							$t['rfr_parameter']=$entity['PARAMETER'][0]['value'];
//									break;
//							}
//						}
//						break;
//				}
//			}
//  	}else{
  			$t['protocole']=$protocole;
	    	$t['method']=$method;
	    	$t['tparameters']=$tparameters;
	    	$t['byref_url']=$byref_url;
	    	$t['serialization']=$serialization;
	    	$t['rft_isbn']=$rft_isbn;
	    	$t['rft_issn']=$rft_issn;
	    	$t['rft_pmid']=$rft_pmid;
	    	$t['rft_doi']=$rft_doi;
	    	$t['rft_byval']=$rft_byval;
	    	$t['rft_byref']=$rft_byref;
	    	$t['rft_private']=$rft_private;
	    	$t['rfe_allow']=$rfe_allow;
	    	$t['rfe_isbn']=$rfe_isbn;
	    	$t['rfe_issn']=$rfe_issn;
	    	$t['rfe_pmid']=$rfe_pmid;
	    	$t['rfe_doi']=$rfe_doi;
	    	$t['rfe_byval']=$rfe_byval;
	    	$t['rfe_byref']=$rfe_byref;
	    	$t['rfe_private']=$rfe_private;
	    	$t['req_allow']=$req_allow;
	    	$t['req_parameter']=$req_parameter;
	    	$t['svc_allow']=$svc_allow;
	    	$t['svc_services']=$svc_services;
	    	$t['res_allow']=$res_allow;
	    	$t['res_parameter']=$res_parameter;
	    	$t['rfr_allow']=$rfr_allow;
	    	$t['rfr_parameter']=$rfr_parameter;
 // 	}
    	$this->sources[$source_id]["PARAMETERS"]=serialize($t);
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::enrichment_is_allow()
	 */
	public function enrichment_is_allow()
	{
	    return connector::ENRICHMENT_YES;
	}

	public function getEnrichmentHeader(){
		$header= array();
		$header[]= "<!-- Script d'enrichissement pour OpenURL -->";
		return $header;
	}

	public function getTypeOfEnrichment($source_id){
		$type = array();
		$libelle = '';
		$infobulle = '';
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		$type['type'] = array(
			array(
				'code' => str_replace(array(" ","%","-","?","!",";",",",":"),"",strip_empty_chars(strtolower($libelle))),
				'label' => $libelle,
				'infobulle' => $infobulle
			)
		);
		$type['source_id'] = $source_id;
		return $type;
	}

	public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array(),$page=1){
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		$enrichment= array();
		//on renvoi ce qui est demandé... si on demande rien, on renvoi tout..
		switch ($type){
			case str_replace(array(" ","%","-","?","!",";",",",":"),"",strip_empty_chars(strtolower($libelle))) :
			default :
				$openurl_param = new openurl_parameters();
				$openurl_param->setParameters($vars);
				$openurl_instance = new openurl_instance($notice_id,0,$openurl_param->getParameters(),$source_id);
				global $debug;
				if($debug == 1 ){
					print $openurl_instance->getInFrame(1980,980);
				}else $enrichment[str_replace(array(" ","%","-","?","!",";",",",":"),"",strip_empty_chars(strtolower($libelle)))]['content'] = $openurl_instance->getInFrame($iwidth,$iheight);
				break;
		}
		$enrichment['source_label']=sprintf($this->msg['openurl_enrichment_source'],$source_name);
		return $enrichment;
	}

	public function getByRefContent($source_id,$notice_id,$uri,$entity){
		global $include_path;
		global $openurl_map;
		$openurl_map = array();
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		require_once ($include_path."/parser.inc.php") ;
    	_parser_($include_path."/openurl/openurl_mapping.xml", array("ITEM" => "_getMapItem_"), "MAP");

    	if($entity){
			//récupère les param d'exports
			$export_param = new export_param();
			$param = $export_param->get_parametres($export_param->context);
			//petit nettoyage pour un bon fonctionnement...
			foreach($param as $key => $value){
				$param["exp_".$key] = $value;
			}
			//maintenant que c'est en ordre, on peut y aller!
			$export = new export(array($notice_id),array(),array());
			$export->get_next_notice("",array(),array(),false,$param);
			$elem = new $openurl_map[$uri]['class']($export->xml_array,true);
			$elem->setEntityType($entity);
			print $elem->serialize();
		}else{
			//si on demande pas une entité, c'est un contextObject
			$openurl_param = new openurl_parameters();
			$openurl_param->setParameters($vars);
			$openurl_instance = new openurl_instance($notice_id,0,$openurl_param->getParameters(),$source_id);
			$openurl_instance->generateContextObject();
			print $openurl_instance->contextObject->serialize();
		}

	}
}
?>