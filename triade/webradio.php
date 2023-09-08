<?php
session_start();
if (file_exists("common/lib_crypt.php")) {
	include_once("common/lib_crypt.php");
	
	function TextNoAccent($Text){
	 	return (strtr($Text, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"));
	}

	function encrypt($text)	{
		
	     	$text_num = str_split($text, CRYPT_CBIT_CHECK);
     		$text_num = CRYPT_CBIT_CHECK - strlen($text_num[count($text_num)-1]);
 
    		for ($i=0;$i<$text_num; $i++)
        	 	$text = $text . chr($text_num);
 
	    	$cipher = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'cbc', '');
     		mcrypt_generic_init($cipher, CRYPT_CKEY, CRYPT_CIV);
     
    		$decrypted = mcrypt_generic($cipher, $text);
	     	mcrypt_generic_deinit($cipher);
 
    		return base64_encode($decrypted);
	}
	
	$nom=TextNoAccent($_SESSION["nom"]);
	$prenom=TextNoAccent($_SESSION["prenom"]);
	$tab['nom']="$nom $prenom";
	if (SERVEURTYPE == "LINUX") {
		$serialize=encrypt(serialize($tab));
	}else{
		$serialize=serialize($tab);
	}
}


include_once("common/config2.inc.php");
if (LAN == "oui") {
	if (SERVEURTYPEdd == "LINUX") {
		header("Location:https://www.triade-educ.org/accueil/webradio.php?GRAPH=".GRAPH."&tab=$serialize&r=".CRYPT_CIV."&r2=".CRYPT_CKEY);
	}else{
		header("Location:https://www.triade-educ.org/accueil/webradio.php?GRAPH=".GRAPH);
	}
}else{
	print "<script>alert(\"Internet non accessible ! Valider l'accès via votre compte administrateur Triade\"); this.close(); </script>";
	
}
?>
