<?php
session_start();
include_once("common/config.inc.php");
if (file_exists("common/lib_crypt.php")) {
	include_once("common/lib_crypt.php");
	
	function TextNoAccent($Text){
	 	return (strtr($Text, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"));
	}

	function encrypt($text)	{
		$text_num = str_split($text, CRYPT_CBIT_CHECK);
		$text_num = CRYPT_CBIT_CHECK - strlen($text_num[count($text_num)-1]);
		for ($i=0; $i<$text_num; $i++)
			$text .= chr($text_num);
		$encrypted = openssl_encrypt($text, 'DES-EDE3-CBC', CRYPT_CKEY, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, CRYPT_CIV);
		return base64_encode($encrypted);
	}
	
	$nom=TextNoAccent($_SESSION["nom"]);
	$prenom=TextNoAccent($_SESSION["prenom"]);
	$tab['nom']="$nom $prenom";
	if ((defined("SERVEURTYPE")) && (SERVEURTYPE == "LINUX")) {
		$serialize=encrypt(serialize($tab));
	}else{
		$serialize=serialize($tab);
	}
}

include_once("common/config2.inc.php");
if (LAN == "oui") {
	if ((defined("SERVEURTYPE")) && (SERVEURTYPE == "LINUX")) {
		header("Location:https://www.triade-educ.org/accueil/webradio.php?GRAPH=".GRAPH."&tab=$serialize&r=".CRYPT_CIV."&r2=".CRYPT_CKEY);
	}else{
		header("Location:https://www.triade-educ.org/accueil/webradio.php?GRAPH=".GRAPH);
	}
}else{
	print "<script>alert(\"Internet non accessible ! Valider l'accès via votre compte administrateur Triade\"); this.close(); </script>";
	
}
?>
