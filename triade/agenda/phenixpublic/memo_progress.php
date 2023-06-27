<?php
  if (isset($_GET['sid']) || isset($HTTP_GET_VARS['sid'])) {
    include("inc/param.inc.php");
    include("inc/fonctions.inc.php");
  } else {
    include("inc/interdit.html");
    exit;
  }

  $idUser = Session_ok($sid);

  if ($idUser == -1) {
    include("inc/interdit.html");
    exit;
  }

if ( isset($_GET['memid']) ) {
	$DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}memo SET mem_pcent=".$_GET['memval']." WHERE mem_id=".$_GET['memid']." Limit 1");
}

$memid = $_GET['memid'];
$barre = $_GET['memval']*1;
$barre2 = 100-$barre;
$valpercent = 20;
if ( $_GET['memval'] <= $valpercent ) {
	$mem_pcent_reduire = 0;
	$mem_pcent_augmenter = $_GET['memval']+$valpercent;
} 
elseif (( $_GET['memval'] > $valpercent ) & ( $_GET['memval'] < (100-$valpercent ) )) {
	$mem_pcent_reduire = $_GET['memval']-$valpercent;
	$mem_pcent_augmenter = $_GET['memval']+$valpercent;
} 
elseif ( $_GET['memval'] >= ( 100-$valpercent )) {
	$mem_pcent_reduire = $_GET['memval']-$valpercent;
	$mem_pcent_augmenter = 100;
}

// Affichage via AJAX dans le div
echo ("	<input type=button onclick=\"MemoProgress('$memid','$mem_pcent_reduire');\" value='-' style='height: 15px; width: 15px; FONT-SIZE: 15px; FONT-WEIGHT: bold; BORDER: 0px; BACKGROUND-COLOR:transparent;'>	
	<img src='image/barre-vert.gif' width='$barre' height='7px'><img src='image/barre-rouge.gif' width='$barre2' height='7px'>
	<input type=button onclick=\"MemoProgress('$memid','$mem_pcent_augmenter');\" value='+' style='height: 15px; width: 15px; FONT-SIZE: 15px; FONT-WEIGHT: bold; BORDER: 0px; BACKGROUND-COLOR:transparent;'>");
?>
