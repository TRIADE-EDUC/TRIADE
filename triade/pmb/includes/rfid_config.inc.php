<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rfid_config.inc.php,v 1.10 2019/05/28 13:35:21 ngantier Exp $


function get_rfid_port() {
	global $pmb_rfid_ip_port, $_SERVER;
	
	// Donne le port rfid associé à l'ip du client
	$rfid_port = '';
	if( $pmb_rfid_ip_port) {
	    $rfid_port_list = array();
		$rfid_cmds=explode(";",$pmb_rfid_ip_port);		
		foreach($rfid_cmds as $rfid_cmd) {			
			$rfid_cmd_1=explode(",",$rfid_cmd);
			if($rfid_cmd_1[0]) {
			    $rfid_port_list[trim($rfid_cmd_1[0])]=trim($rfid_cmd_1[1]);
			}
		}		
		if(!empty($rfid_port_list[$_SERVER['REMOTE_ADDR']])) {
			$rfid_port=$rfid_port_list[$_SERVER['REMOTE_ADDR']];
		}
	}
	return $rfid_port;	
}

function get_rfid_js_header() {
	global $pmb_rfid_driver,$pmb_rfid_serveur_url,$pmb_rfid_library_code,$pmb_rfid_afi_security_codes;
	global $rfid_js_header;
	global $base_path;
	
	$codes_afi=explode(",",$pmb_rfid_afi_security_codes);
	
	$rfid_js_header="
	<script type='text/javascript'>
		url_serveur_rfid=\"".$pmb_rfid_serveur_url."\";
		SerialPort=\"".get_rfid_port()."\";
		LibraryCode='$pmb_rfid_library_code';
		rfid_afi_security_active='".(isset($codes_afi[0]) ? $codes_afi[0] : '')."';
		rfid_afi_security_off='".(isset($codes_afi[1]) ? $codes_afi[1] : '')."';
	</script>
	<script type='text/javascript' src='$base_path/javascript/pmbtoolkit.js'></script>
 	<script src='$base_path/javascript/soap.js'></script>
	<script src='$base_path/javascript/rfid/rfid_pret.js'></script>";
	$driver_path= $base_path."/javascript/rfid/".$pmb_rfid_driver;

	if (is_dir($driver_path)) {
	    if (($dh = opendir($driver_path))) {

	        while (($file = readdir($dh)) !== false) {	
	        	       
	            if(filetype($driver_path."/".$file) =='file') {
	            	if( substr($file, -3) == ".js" )
	            		$rfid_js_header.="<script src='".$driver_path."/".$file."'></script>\n";
	            }	
	        }
	        closedir($dh);
	    }
	}   	
}