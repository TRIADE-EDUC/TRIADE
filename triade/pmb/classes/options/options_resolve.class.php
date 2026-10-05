<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_resolve.class.php,v 1.2.8.1 2025/04/16 08:15:53 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_resolve extends options {
	    
	protected $dtype;
	
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters['RESOLVE']=array();
		$this->parameters['SIZE'][0]['value'] = '';
		$this->parameters['REPEATABLE'][0]['value'] = '';
	}
	
	protected function get_hidden_fields_form() {
		global $charset, $_custom_prefixe_;
		
		return "<input type='hidden' name='first' value='1'>
			<input type='hidden' name='idchamp' value='".$this->idchamp."'>
			<input type='hidden' name='_custom_prefixe_' value='".$_custom_prefixe_."'>
			<input type='hidden' name='dtype' value='".$this->dtype."'>
			<input type='hidden' name='name' value='".htmlentities($this->name,ENT_QUOTES,$charset)."'>";
	}
	
	public function get_content_form() {
		global $msg, $charset, $_custom_prefixe_;
		
		$content_form = $this->get_line_content_form($msg["procs_options_text_taille"], 'SIZE', 'number');
		$content_form .= $this->get_line_content_form($msg["persofield_textrepeat"], 'REPEATABLE', 'checkbox');
		$content_form .= "
		<tr>
			<td colspan='2'>";
			if($this->idchamp){
				$content_form .= "
				<table>
					<tr>
						<td>".$msg['procs_options_resolve_options']."</td>
					</tr>
					<tr>
						<td>
							<table border='1' id='resolve_table' style='text-align:center'>
								<tr>
									<th></th>
									<th>".$msg['procs_options_resolve_options_id']."</th>
									<th>".$msg['procs_options_resolve_options_label']."</th>
									<th>".$msg['procs_options_resolve_options_link']."</th>
								</tr>";
							for($i=0; $i<count($this->parameters['RESOLVE']);$i++){
								$requete="select count(".$_custom_prefixe_."_custom_".$this->dtype.") from ".$_custom_prefixe_."_custom_values where ".$_custom_prefixe_."_custom_champ=".$this->idchamp." and SUBSTRING_INDEX(".$_custom_prefixe_."_custom_".$this->dtype.",'|',-1) like '".$this->parameters['RESOLVE'][$i]['ID']."'";
								$res = pmb_mysql_query($requete);
								if(pmb_mysql_num_rows($res)) $nb = pmb_mysql_result($res,0,0);
								else $nb = 0;
								$content_form .= "
								<tr>
									<td><input type='checkbox' name='checked[]' value='".htmlentities($this->parameters['RESOLVE'][$i]['ID'],ENT_QUOTES,$charset)."' ".($nb > 0 ? "disabled='true' ": "")."/></td>
									<td><input type='text' name='RESOLVE[id][]' size='2' value='".htmlentities($this->parameters['RESOLVE'][$i]['ID'],ENT_QUOTES,$charset)."' readonly='true'/></td>
									<td><input type='text' name='RESOLVE[label][]' size='10' value='".htmlentities($this->parameters['RESOLVE'][$i]['LABEL'],ENT_QUOTES,$charset)."'/></td>
									<td><input type='text' name='RESOLVE[value][]' size='30' value='".htmlentities($this->parameters['RESOLVE'][$i]['value'],ENT_QUOTES,$charset)."'/></td>
								</tr>";
							}
							$content_form .= "</table>
						</td>
					<tr>
				</table>";
			}else{
				$content_form .= "<b>".$msg["parperso_options_list_before_rec"]."</b>"; 
			}
		$content_form .= "</td>
		</tr>";
		return $content_form;
	}
    
	protected function get_buttons_form() {
		global $msg;
		return "<input class='bouton' type='submit' value='".$msg['ajouter']."' onclick='add_entry();return false;' />&nbsp;
		<input class='bouton' type='submit' value='".$msg['procs_options_suppr_options_coche']."' onClick='this.form.first.value=2' />&nbsp;
		<input class='bouton' type='submit' value='".$msg[77]."' />";
	}
	
	public function get_form() {
		$form = parent::get_form();
		$form .= '
		<script type="text/javascript">
			var tab = new Array();';
			
		for($i=0; $i<count($this->parameters['RESOLVE']);$i++){
			$form .= "
			tab[$i] = ".$this->parameters['RESOLVE'][$i]['ID'].";";
		}
		$form .= '
			function getMaxId() {
				var max = 0;
				for(var i=0 ; i<tab.length; i++){
					if(tab[i]>max) max = tab[i];
				}
				return max;
			}
			function add_entry(){
				var new_id = getMaxId()+1;
				tab.push(new_id);
				var table = document.getElementById("resolve_table");
				var row = table.insertRow(table.rows.length);
				var cell = row.insertCell(row.cells.length);
				var check = document.createElement("input");
				check.setAttribute("type","checkbox");
				check.setAttribute("name","checked[]");
				check.setAttribute("value",new_id);		
				cell.appendChild(check);	
				var cell1 = row.insertCell(row.cells.length);
				var id = document.createElement("input");
				id.setAttribute("type","text");
				id.setAttribute("name","RESOLVE[id][]");
				id.setAttribute("readonly","true");
				id.setAttribute("size","2");
				id.setAttribute("value",new_id);
				cell1.appendChild(id);
				var cell2 = row.insertCell(row.cells.length);
				var label = document.createElement("input");
				label.setAttribute("type","text");
				label.setAttribute("name","RESOLVE[label][]");
				label.setAttribute("size","10");
				cell2.appendChild(label);
				var cell3 = row.insertCell(row.cells.length);	
				var link = document.createElement("input");
				link.setAttribute("type","text");
				link.setAttribute("name","RESOLVE[value][]");
				link.setAttribute("size","30");
				cell3.appendChild(link);
			}
		</script>';
		return $form;
	}
	
	public function set_parameters_from_form() {
		global $SIZE, $RESOLVE, $REPEATABLE;
		global $first;
		
		parent::set_parameters_from_form();
		$this->parameters['SIZE'][0]['value'] = intval($SIZE);
		$this->parameters['REPEATABLE'][0]['value'] = $REPEATABLE ? 1 : 0;
		if ($first == 2){
			$this->parameters['RESOLVE']= array();
			for($i=0; $i<count($RESOLVE['id']);$i++){
				global $checked;
				if(count($checked)==0 || (count($checked)>0 && !in_array($RESOLVE['id'][$i],$checked))){
					if($RESOLVE['id'][$i] && $RESOLVE['label'][$i] && $RESOLVE['value'][$i]){
						$array= array(
								'ID' => stripslashes($RESOLVE['id'][$i]),
								'LABEL' => stripslashes($RESOLVE['label'][$i]),
								'value' => stripslashes($RESOLVE['value'][$i])
						);
						$this->parameters['RESOLVE'][]=$array;
					}
				}
			}
		} else {
			if(!isset($RESOLVE['id']) || ! is_array($RESOLVE['id'])){
				$RESOLVE['id'] = array();
			}
			if(count($RESOLVE['id'])==0){
				$this->parameters['RESOLVE'][0]['ID'] = "1";
				$this->parameters['RESOLVE'][0]['LABEL'] = "Pubmed";
				$this->parameters['RESOLVE'][0]['value'] = "http://www.ncbi.nlm.nih.gov/pubmed/!!id!!";
				$this->parameters['RESOLVE'][1]['ID'] = "2";
				$this->parameters['RESOLVE'][1]['LABEL'] = "DOI";
				$this->parameters['RESOLVE'][1]['value'] = "http://dx.doi.org/!!id!!";
			}
			for($i=0; $i<count($RESOLVE['id']);$i++){
				if($RESOLVE['id'][$i] && $RESOLVE['label'][$i] && $RESOLVE['value'][$i]){
					$this->parameters['RESOLVE'][$i]['ID'] = $RESOLVE['id'][$i];
					$this->parameters['RESOLVE'][$i]['LABEL'] = $RESOLVE['label'][$i];
					$this->parameters['RESOLVE'][$i]['value'] = "<![CDATA[".$RESOLVE['value'][$i]."]]>";
				}
			}
		}
	}
	
	public function set_dtype($dtype) {
		$this->dtype = $dtype;
	}
	
	public static function proceed() {
		global $options, $first, $msg, $charset;
		global $dtype, $idchamp, $_custom_prefixe_;
		
		if(!$dtype && $idchamp){
			$requete="SELECT datatype FROM ".$_custom_prefixe_."_custom WHERE idchamp = $idchamp";
			$resultat = pmb_mysql_query($requete);
			$dtype = pmb_mysql_result($resultat,0,0);
		}
		
		$classname = static::class;
		$instance = new $classname();
		$instance->set_idchamp($idchamp);
		$instance->set_dtype($dtype);
		$options = stripslashes($options);
		
		if ($first == 2) {
			print "<h3>" . $msg['procs_options_param'].$instance->get_name()."</h3><hr />";
			$instance->set_parameters_from_form();
			// Formulaire
			print $instance->get_form();
		} elseif ($first == 1) { // Si enregistrer
			$instance->set_parameters_from_form();
			$options = array_to_xml($instance->get_parameters(), "OPTIONS");
			
			print "<script>
        	   opener.document.formulaire.".$instance->get_name()."_options.value='" . str_replace ( "\n", "\\n", addslashes($options)). "';
        		opener.document.formulaire.".$instance->get_name()."_for.value='".$instance->get_type()."';
        	   self.close();
        	</script>";
		} else {
			print "<h3>" . $msg['procs_options_param'].$instance->get_name()."</h3><hr />";
			if (empty($options)) {
				$options = "<OPTIONS></OPTIONS>";
			}
			$param = _parser_text_no_function_( "<?xml version='1.0' encoding='".$charset."'?>\n" . $options, "OPTIONS");
			$instance->set_parameters($param);
			if (! isset ( $param ["FOR"] ) || $param ["FOR"] != $instance->get_type()) {
				$instance->init_default_parameters();
			}
			// Formulaire
			print $instance->get_form();
		}
	}
}
?>