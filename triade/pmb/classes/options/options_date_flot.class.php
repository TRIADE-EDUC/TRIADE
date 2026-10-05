<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_date_flot.class.php,v 1.2 2022/05/05 06:44:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

class options_date_flot extends options {
    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters["DEFAULT_TODAY"][0]["value"] = 'yes';
		$this->parameters['REPEATABLE'][0]['value'] = '';
		$this->parameters['DURATION'][0]['value'] = 1;
		$this->parameters['DURATION_D_M_Y'][0]['value'] = 0;
	}
	
	public function get_content_form() {
		global $msg, $charset;
		$content_form = $this->get_line_content_form($msg["parperso_default_today"], 'DEFAULT_TODAY', 'checkbox', 'yes');
		
        //TODO
// 		$content_form .= $this->get_line_content_form($msg['persofield_textrepeat'], 'REPEATABLE', 'checkbox');
		$content_form .= "
		<tr>
            <td>".$msg['parperso_option_duration']."</td>
			<td>
				<input type='text' class='saisie-10em' name='DURATION' value='".htmlentities($this->parameters['DURATION'][0]['value'], ENT_QUOTES, $charset)."' />
				<select name='DURATION_D_M_Y'>
					<option value='0' ".(!$this->parameters['DURATION_D_M_Y'][0]['value'] ? ' selected ' : '').">".$msg['parperso_option_duration_d']."</option>							
					<option value='1' ".($this->parameters['DURATION_D_M_Y'][0]['value']==1 ? ' selected ' : '').">".$msg['parperso_option_duration_m']."</option>
					<option value='2' ".($this->parameters['DURATION_D_M_Y'][0]['value']==2 ? ' selected ' : '').">".$msg['parperso_option_duration_y']."</option>
				</select>					
			</td>
		</tr>";
        return $content_form;
    }
    
	public function set_parameters_from_form() {
    	global $DEFAULT_TODAY, $REPEATABLE, $DURATION, $DURATION_D_M_Y;

		parent::set_parameters_from_form();
		if ($DEFAULT_TODAY) {
			$this->parameters["DEFAULT_TODAY"][0]["value"]="yes";
        } else {
        	$this->parameters["DEFAULT_TODAY"][0]["value"]="";
        }
        $this->parameters['REPEATABLE'][0]['value'] = ($REPEATABLE ? 1 : 0);
        $this->parameters['DURATION'][0]['value'] = intval($DURATION);
        $this->parameters['DURATION_D_M_Y'][0]['value'] = intval($DURATION_D_M_Y);
    }
}
?>