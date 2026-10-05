<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_query_list.class.php,v 1.2 2024/01/17 07:55:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_query_list extends options {
    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters['MULTIPLE'][0]['value'] = '';
		$this->parameters['UNSELECT_ITEM'][0]['VALUE'] = '';
		$this->parameters['UNSELECT_ITEM'][0]['value'] = '';
		$this->parameters['QUERY'][0]['value'] = '';
		if($this->display_type != 'custom_action') {
			$this->parameters['AUTORITE'][0]['value'] = '';
			$this->parameters['CHECKBOX'][0]['value'] = '';
			$this->parameters['CHECKBOX_NB_ON_LINE'][0]['value'] = '';
			$this->parameters["INSERTAUTHORIZED"][0]['value'] = '';
			$this->parameters["OPTIMIZE_QUERY"][0]['value'] = '';
			$this->parameters["FIELD0"][0]["value"] = '';
			$this->parameters["FIELD1"][0]["value"] = '';
		}
	}
	
	protected function get_hidden_fields_form() {
		global $charset;
		
		$hidden_fields_form = "
			<input type='hidden' name='first' value='0' />
			<input type='hidden' name='name' value='".$this->name."' />
			<input type='hidden' name='type' value='".$this->type."' />
		";
		if($this->display_type != 'custom_action') {
			$hidden_fields_form .= "
			<input type='hidden' name='FIELD0' value='".htmlentities($this->parameters["FIELD0"][0]['value'],ENT_QUOTES,$charset)."' />
			<input type='hidden' name='FIELD1' value='".htmlentities($this->parameters["FIELD1"][0]['value'],ENT_QUOTES,$charset)."' />";
		}
		return $hidden_fields_form;
	}
	
	public function get_content_form() {
    	global $msg, $charset;
    	
    	$content_form = $this->get_line_content_form($msg["procs_options_liste_multi"], 'MULTIPLE', 'checkbox', 'yes');
    	if($this->display_type != 'custom_action') {
    		$content_form .= $this->get_line_content_form($msg["pprocs_options_liste_authorities"], 'AUTORITE', 'checkbox', 'yes');
    		$content_form .= $this->get_line_content_form($msg["pprocs_options_liste_authorities_new_value"], 'INSERTAUTHORIZED', 'checkbox', 'yes');
    		$content_form .= "
			<tr>
				<td>".$msg['pprocs_options_liste_checkbox']."</td>
				<td>
					<input type='checkbox' value='yes' name='CHECKBOX' ".($this->parameters['CHECKBOX'][0]['value']=="yes" ? "checked='checked'" : "")." />
					&nbsp;".$msg['pprocs_options_liste_checkbox_nb_on_line']."<input class='saisie-2em' type='text' name='CHECKBOX_NB_ON_LINE' value='".htmlentities($this->parameters['CHECKBOX_NB_ON_LINE'][0]['value'],ENT_QUOTES,$charset)."' />
				</td>
			</tr>";
    	}
    	$content_form .= "
		<tr>
			<td>".$msg['procs_options_choix_vide']."</td>
			<td>".$msg['procs_options_value']." : <input class='saisie-10em' type='text' name='UNSELECT_ITEM_VALUE' value='".htmlentities($this->parameters['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."' />&nbsp;".$msg['procs_options_label']." : <input class='saisie-20em' type='text' name='UNSELECT_ITEM_LIB' value='".htmlentities($this->parameters['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."' /></td>
		</tr>";
    	if($this->display_type != 'custom_action') {
    		$content_form .= "
			<tr>
				<td colspan='2'>
					<table role='presentation'>
						".$this->get_line_content_form($msg['procs_options_requete'], 'QUERY', 'textarea')."
					</table>
				</td>
			<tr>";
    		$content_form .= $this->get_line_content_form($msg["pprocs_options_liste_optimize_req"], 'OPTIMIZE_QUERY', 'checkbox', 'yes');
    	} else {
    		$content_form .= $this->get_line_content_form($msg['procs_options_requete'], 'QUERY', 'textarea');
    	}
		return $content_form;
    }
    
    protected function get_buttons_form() {
    	global $msg;
    	
    	$buttons = "<input class='bouton' type='submit' value='".$msg['procs_options_tester_requete']."' onClick='this.form.first.value=2'>&nbsp;";
		$buttons .= "<input class='bouton' type='submit' value='".$msg[77]."' onClick='this.form.first.value=1'>";
    	return $buttons;
    }
    
    public function get_form() {
    	global $first, $msg, $charset;
    	
    	$form = parent::get_form();
    	if ($first==2) {
    		$resultat=pmb_mysql_query($this->parameters['QUERY'][0]['value']);
    		if (!$resultat) {
    			$form .= "<span class='center'>".$msg['procs_options_echec_requete']."<br />".pmb_mysql_error()."</span>";
    		} else {
    			$form .= "<span class='center'><b>".$msg['procs_options_reponse_requete']."</b></span>";
    			$form .= "<table width=100% border=1>\n";
    			while ($r=pmb_mysql_fetch_row($resultat)) {
    				$form .= "<tr><td>".htmlentities($r[0],ENT_QUOTES,$charset)."</td><td>".htmlentities($r[1],ENT_QUOTES,$charset)."</td></tr>\n";
    			}
    			$form .= "</table>";
    		}
    	}
    	return $form;
    }
    
    public function set_parameters_from_form() {
    	global $QUERY, $MULTIPLE, $AUTORITE, $CHECKBOX;
    	global $INSERTAUTHORIZED, $OPTIMIZE_QUERY;
    	global $UNSELECT_ITEM_VALUE, $UNSELECT_ITEM_LIB, $CHECKBOX_NB_ON_LINE;
    	global $FIELD0, $FIELD1;
    	global $first;
    	
    	parent::set_parameters_from_form();
    	if ($first==2) {
    		$this->parameters['QUERY'][0]['value']=stripslashes($QUERY);
    	} else {
    		$this->parameters['QUERY'][0]['value']="<![CDATA[".str_replace("\r\n","\n",stripslashes($QUERY))."]]>";
    	}
    	
		if ($MULTIPLE=="yes")
			$this->parameters['MULTIPLE'][0]['value']="yes";
		else
			$this->parameters['MULTIPLE'][0]['value']="no";
    			
		$this->parameters['UNSELECT_ITEM'][0]['VALUE']=stripslashes($UNSELECT_ITEM_VALUE);
		if ($first==2) {
			$this->parameters['UNSELECT_ITEM'][0]['value']=stripslashes($UNSELECT_ITEM_LIB);
		} else {
			$this->parameters['UNSELECT_ITEM'][0]['value']="<![CDATA[".stripslashes($UNSELECT_ITEM_LIB)."]]>";
		}
			
		if($this->display_type != 'custom_action') {
			if ($AUTORITE=="yes")
				$this->parameters['AUTORITE'][0]['value']="yes";
			else
				$this->parameters['AUTORITE'][0]['value']="no";
			if ($CHECKBOX=="yes")
				$this->parameters['CHECKBOX'][0]['value']="yes";
			else
				$this->parameters['CHECKBOX'][0]['value']="no";
			if ($INSERTAUTHORIZED=="yes")
				$this->parameters["INSERTAUTHORIZED"][0]['value']="yes";
			else
				$this->parameters["INSERTAUTHORIZED"][0]['value']="no";
					
			if ($OPTIMIZE_QUERY=="yes")
				$this->parameters["OPTIMIZE_QUERY"][0]['value']="yes";
			else
				$this->parameters["OPTIMIZE_QUERY"][0]['value']="no";
			
			$this->parameters['CHECKBOX_NB_ON_LINE'][0]['value']=stripslashes($CHECKBOX_NB_ON_LINE);
			
			$this->parameters["FIELD0"][0]['value']=stripslashes($FIELD0);
			$this->parameters["FIELD1"][0]['value']=stripslashes($FIELD1);
			
			if ($first==2) {
				$resultat=pmb_mysql_query(stripslashes($QUERY));
				if ($resultat) {
					$this->parameters["FIELD0"][0]['value']=pmb_mysql_field_name($resultat,0);
					$this->parameters["FIELD1"][0]['value']=pmb_mysql_field_name($resultat,1);
				}
			}
		}
    }
}
?>