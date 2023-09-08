<?php
// change false to true if you want to play distant playlists
define('USE_FRAMAPROXY', false);

//---------------------------------------------------------------------
if (USE_FRAMAPROXY!==true) die("Proxy use is forbidden !");

function rand_str($size) {
   $feed = "0123456789abcdefghijklmnopqrstuvwxyz";
   for ($i=0; $i < $size; $i++) {
      $rand_str .= substr($feed, rand(0, strlen($feed)-1), 1);
   }
   return $rand_str;
}

if (!$_GET["xml"]) { die("no file"); }

$dataURL = $_GET["xml"];
/*
$handle = fopen ($dataURL, "r");
$contents = fread ($handle, 4096*4096);
fclose ($handle);
*/
$contents = file_get_contents($dataURL);

// Test1 : on vérifie qu'il s'agit bien d'un fichier xml
// Test1a : on vérifie la présence du "<?xml"
	if (!preg_match('`<\?xml\s`is', $contents)) {
		die($dataURL." doesn't seem to be a valid xml file...");
	}
// Test1b : on vérifie que le "<?xml" est bien sur la première ligne du fichier, et qu'il n'y apparait qu'une fois
	if (preg_match('`\S.*<\?xml`is', $contents)) {
		die($dataURL." seems to contain multiple '<?xml' tags...");
	}
	
// Test2 : on vérifie (rapidement :( ) l'absence de code malicieux
// Test2a : on protége le "<?xml"
$replace = "[hash:".rand_str(20)."]";
$contents = str_replace("<?xml", $replace, $contents);
// Test2b : on s'arrête si on croise une balise "<?" ou "<(.*)?script"
	if (preg_match('`((\<\?)|\<\s{0,}script)`is', $contents)) {
		die($dataURL." seems to contain non-secure tags...");
	}
// Test2c : on remet notre chaine protégée
$contents = str_replace($replace, "<?xml", $contents);

// si on est encore là, c'est que le fichier doit pouvoir être lu.
echo $contents;

?>