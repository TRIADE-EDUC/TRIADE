<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: surligner.js.php,v 1.4 2019/10/17 07:18:14 jlaurent Exp $

if(!isset($_SESSION))
{
    session_start();
} 

global $base_path;
file_put_contents($base_path.'/temp/surligner_codes.js', isset($_SESSION['surligner_codes'])?$_SESSION['surligner_codes']:'');
//echo (isset($_SESSION['surligner_codes'])?$_SESSION['surligner_codes']:''); 

?>