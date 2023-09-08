<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: surligner.js.php,v 1.2 2018-08-01 10:34:05 dgoron Exp $

session_start();

echo (isset($_SESSION['surligner_codes'])?$_SESSION['surligner_codes']:''); 

?>