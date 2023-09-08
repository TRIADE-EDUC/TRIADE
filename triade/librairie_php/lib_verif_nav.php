<?php
function verif_navigateur() {
	if (preg_match('/Firefox/i', $_SERVER["HTTP_USER_AGENT"])) { return "Firefox"; }
	if (preg_match('/Chrome/i', $_SERVER["HTTP_USER_AGENT"])) { return "Chrome"; }
	if (preg_match('/msie 10/i', $_SERVER["HTTP_USER_AGENT"])) { return "IE10"; }
	if (preg_match('/msie/i', $_SERVER["HTTP_USER_AGENT"])) { return "IE"; }
	if (preg_match('/opera/i', $_SERVER["HTTP_USER_AGENT"])) { return "Opera"; }
	if (preg_match('/Mozilla\/5.0/i', $_SERVER["HTTP_USER_AGENT"]) && !preg_match('/Konqueror/i', $_SERVER["HTTP_USER_AGENT"])) { return "Netscape 6.x"; }
	return "??";
}

function verif_os() {
	if (preg_match('/windows/i', $_SERVER["HTTP_USER_AGENT"])) { return "Windows"; }
	if (preg_match('/linux/i', $_SERVER["HTTP_USER_AGENT"])) { return "Linux"; }
	return "";
}
?>
