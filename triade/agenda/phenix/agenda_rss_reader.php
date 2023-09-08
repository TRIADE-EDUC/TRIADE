<style>
h1 {
  font-size: 160%;
  border-bottom: 2px solid #AAAAAA;
  margin: 0 0 .2em 0;
}
h2 {
  color: #5A5A5A;
  font-size: 110%;
  font-weight: normal;
  margin: 0 0 .6em 0;
}
</style>
<script>
function showRSS(str,form) { 
  if (str==""){
//    document.getElementById("rssOutput").innerHTML= '<br><div align="center"><?php echo trad("RSS_CHOIX");?></div><br>';
  }
  else {
    document.forms[form].submit();
  }
}
function changeRSSnbobj(nb_obj,rss_id,form) {
document.forms[form].action.value = "Change nb obj";
document.forms[form].submit();
}
function suppRSS(rss_id,rss_titre,form) {
  var msgAlert;
  msgAlert = "<?php echo trad("RSS_SUP");?> '"+rss_titre+"' <?php echo trad("RSS_SUP_ACCUEIL");?> ?";
  if (confirm(msgAlert)) {
    document.forms[form].action.value = "Sup flux accueil";
    document.forms[form].submit();
  }
}

// Copie des fonctions d'affichage des videos /simplepie/simplepie.inc
function embed_odeo(link) {
	document.writeln('<embed src="http://odeo.com/flash/audio_player_fullsize.swf" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" quality="high" width="440" height="80" wmode="transparent" allowScriptAccess="any" flashvars="valid_sample_rate=true&external_url='+link+'"></embed>');
}

function embed_quicktime(type, bgcolor, width, height, link, placeholder, loop) {
	if (placeholder != '') {
		document.writeln('<embed type="'+type+'" style="cursor:hand; cursor:pointer;" href="'+link+'" src="'+placeholder+'" width="'+width+'" height="'+height+'" autoplay="false" target="myself" controller="false" loop="'+loop+'" scale="aspect" bgcolor="'+bgcolor+'" pluginspage="http://www.apple.com/quicktime/download/"></embed>');
	}
	else {
		document.writeln('<embed type="'+type+'" style="cursor:hand; cursor:pointer;" src="'+link+'" width="'+width+'" height="'+height+'" autoplay="false" target="myself" controller="true" loop="'+loop+'" scale="aspect" bgcolor="'+bgcolor+'" pluginspage="http://www.apple.com/quicktime/download/"></embed>');
	}
}

function embed_flash(bgcolor, width, height, link, loop, type) {
	document.writeln('<embed src="'+link+'" pluginspage="http://www.macromedia.com/go/getflashplayer" type="'+type+'" quality="high" width="'+width+'" height="'+height+'" bgcolor="'+bgcolor+'" loop="'+loop+'" allowfullscreen="true"></embed>');
}

function embed_flv(width, height, link, placeholder, loop, player) {
	document.writeln('<embed src="'+player+'" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" quality="high" width="'+width+'" height="'+height+'" wmode="transparent" flashvars="file='+link+'&autostart=false&repeat='+loop+'&showdigits=true&showfsbutton=false"></embed>');
}

function embed_wmedia(width, height, link) {
	document.writeln('<embed type="application/x-mplayer2" src="'+link+'" autosize="1" width="'+width+'" height="'+height+'" showcontrols="1" showstatusbar="0" showdisplay="0" autostart="0"></embed>');
}
</script>  
<?php
$DB_CX->DbQuery("SELECT count(*) FROM ${PREFIX_TABLE}rss_reader WHERE rr_util_id=".$idUser." Order By rr_rss_titre");
if ($DB_CX->DbResult(0,0)==0)
  $_GET['config']="ok";
if ($_POST['action']=="lire_flux") {
?>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD align="right" width="37" nowrap class="sousMenu"><IMG src="image/trans.gif" alt="" width="35" height="1" border="0"></TD>
    <TD height="28" width="100%" class="sousMenu">
	<form id="form" action="" method="POST"> 
	<?php
	echo trad("TITRE_RSS_READER3");
	$DB_CX->DbQuery("SELECT count(*) FROM ${PREFIX_TABLE}rss_reader WHERE rr_util_id=".$idUser." Order By rr_rss_titre");
	$nb_rss = $DB_CX->DbNextRow();
	if ($nb_rss[0] >0) {
	  echo '<select name="url" onchange="showRSS(this.value,\'form\')">';
	  echo '<option value=""></option>';
	  $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}rss_reader WHERE rr_util_id=".$idUser." Order By rr_rss_titre");
	  while ($enr = $DB_CX->DbNextRow()) {
	    $selected = "";
		if ($_POST["url"] == $enr['rr_rss_url']) $selected = "SELECTED";
  	    echo "<option value=\"".$enr['rr_rss_url']."\" ".$selected.">".htmlspecialchars(stripslashes($enr['rr_rss_titre']),ENT_QUOTES)."</option>";
	  }
	  echo "</select>";
	  echo "<INPUT type=\"hidden\" name=\"action\" value=\"lire_flux\">";
	  echo "</form>";
	}
	else echo trad("RSS_VIDE");
	?>
	</TD>
    <TD align="right" width="10%" nowrap class="sousMenu" style="text-align:right;"><a href="?<?php echo "sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&tcType="._TYPE_RSS_READER."&config=ok";?>"><img src="image/rss_add.gif" align="absmiddle" border="0">&nbsp;<font style="color:<?php echo $CalNavigationTexte;?>;"><?php echo trad("RSS_GERER");?></font></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="?<?php echo "sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&tcType="._TYPE_RSS_READER."&options=ok";?>"><img src="image/rss_options.gif" align="absmiddle" border="0">&nbsp;<font style="color:<?php echo $CalNavigationTexte;?>;"><?php echo trad("RSS_OPTIONS");?></font></a>&nbsp;&nbsp;</TD>	
  </TR>
  </TABLE>
  <BR>
  <TABLE cellspacing="0" cellpadding="5" bgcolor="<?php echo $ListeChoixFond; ?>" width="99%" border="0">
  <TR>
    <TD class="bordTLRB">
<div style="width:95%;">
<br>
<?php
// On récupère les infos de configuration globale du mod RSS # ici l'affichage de la vidéo ou non
$DB_CX->DbQuery("SELECT valeur FROM ${PREFIX_TABLE}configuration WHERE param='RSS_VIDEOS'");
$rss_videos = $DB_CX->DbResult(0,0);

// On récupère les infos de configuration de l'utilisateur
$DB_CX->DbQuery("SELECT util_rss_reader FROM ${PREFIX_TABLE}utilisateur WHERE util_id='".$idUser."'");
list($nb_items,$nb_col_acc,$nb_items_acc,$refresh) = explode(";",$DB_CX->DbResult(0,0));
if ($nb_items == "") $nb_items = 10;
if ($refresh == "") $refresh = 600;
$itemlimit = 0;
	
$url=$_POST["url"];
$host = explode("/",$url);
$host = $host[0]."//".$host[2];
include_once('simplepie/simplepie.inc.php'); // Include SimplePie
print "ok";

$feed = new SimplePie(); // Create a new instance of SimplePie
$feed->set_feed_url($url);
$feed->set_cache_location ('simplepie/cache/'); // Set the cache location
$feed->set_cache_duration ($refresh); // Set the cache time
$feed->set_output_encoding('iso-8859-1');
$feed->set_favicon_handler('simplepie/tools/handler_image.php');
$success = $feed->init(); // Initialize SimplePie
$feed->handle_content_type(); 
if ($success) {
  if (!$favicon = $feed->get_favicon()) {
	$favicon = 'simplepie/tools/alternate.png';
	}
  echo '<table width="100%"><tr><td width="100%"><h1><a href="'.$url.'" target="_blank">'. $feed->get_title(). '</a></h1></td>';
  if ($feed->get_image_url()!="") {
    $width = $feed->get_image_width();
    if (($feed->get_image_width()>100) || ($feed->get_image_width()=="")) $width = 100;
    echo '<td rowspan="2" align="center">&nbsp;&nbsp;<a href="'.$feed->get_image_link().'" title="'.$feed->get_image_title().'" target="_blank">';
    echo '<img src="'.$feed->get_image_url().'" width="'.$width.'" border="0"></a></td>';
  }
  echo '<tr><td><h2>'.$feed->get_description().'</h2></td></tr></table>';
  echo '<table border="0" width="100%">';  
  // debut de la liste
  foreach($feed->get_items() as $item) {
    if ($itemlimit==$nb_items) { break; }
	$img=0;
	echo '<tr><td><BR>';
	echo '<img src="'.$favicon.'" align="absmiddle" width="16" />&nbsp;';
    echo '<B><a href="'.$item->get_permalink().'" title="'.$item->get_title().'" target="_blank">'.$item->get_title().'</a></B>';
	echo '&nbsp;('.$item->get_date('j F Y | g:i a').')<BR>';
	echo '</td></tr><tr><td style="border-bottom:2px dotted #999999;">&nbsp;<br>';
	$item_desc = "";
	$item_desc = $item->get_description();
	$item_desc_test = substr($item_desc,10,40);
	$item_desc = str_replace("href","target='_blank' href",$item_desc);
	if (strpos($item->get_content(),$item_desc_test)===false) {
      echo $item_desc;
	  if (substr_count($item_desc,"<img ")==0) $img=0;
	  elseif (substr_count($item_desc,'width="1"')==substr_count($item_desc,'width="')) $img=0;
	  else $img=1;
	  echo '<BR>';
	}
	if ($item->get_content()!="") {
	  echo '<BR>';
	  $item_content = "";
	  $item_content = $item->get_content();
	  $item_content = str_replace("href","target='_blank' href",$item_content);
	  echo $item_content;
	  if (substr_count($item_content,"<img ")==0) $img=0;
	  elseif (substr_count($item_content,'width="1"')==substr_count($item_content,'width="')) $img=0;
	  else $img=1;
	}
    if (($enclosure = $item->get_enclosure(0)) && ($rss_videos=="OUI"))
	{
	  $enclosure_obj = 0;
 	  echo '<BR><BR><BR><div align="center">';
	  if (($enclosure->get_link()!= "") && ((strpos($enclosure->get_link(),".flv")>0) || (strpos($enclosure->get_link(),".m4v")>0) || (strpos($enclosure->get_link(),".mp4")>0))) {
		echo '<object type="application/x-shockwave-flash" data="simplepie/tools/player_flv_maxi.swf" width="320" height="240">';
		echo '<param name="allowFullScreen" value="true" />';
		echo '<param name="FlashVars" value="flv='.$enclosure->get_link().'&showtime=2&showvolume=1&showfullscreen=1&width=320&amp;height=240" />';
		echo '<param name="wmode" value="transparent" />';
		echo '</object>'; 
		echo "<BR>";
		$enclosure_obj = 1;
	  }
	  elseif (($img==0) && ($enclosure->get_link()!= "") && (substr_count($enclosure->get_type(),"image") == 1 ) && (strpos($item->get_description(),$enclosure->get_link())===false)) {
		echo '<img src="'.$enclosure->get_link().'">';
		echo "<BR>";
		$enclosure_obj = 1;
	  }	  
	  else {
	    echo $enclosure->embed(array(
		  'audio' => './simplepie/tools/place_audio.png',
		  'video' => './simplepie/tools/place_video.png',
		  'mediaplayer' => './simplepie/tools/mediaplayer.swf',
		  'alt' => '<img src="./simplepie/tools/mini_podcast.png" class="download" border="0" title="'.trad("RSS_TELECHARGER").' ('.$enclosure->get_extension().'; '.$enclosure->get_size().' MB)" />',
		  'altclass' => 'download'
		  ));
		echo "<BR>";
		$enclosure_obj = 1;		
	 }
	  if ((($enclosure->get_type()) || ($enclosure->get_size())) && ($enclosure_obj == 1)) {
	    echo '(' . $enclosure->get_type();
	    if ($enclosure->get_size()) echo '; ' . $enclosure->get_size() . ' MB';								
	    echo ')<BR>';
	  }
	  if ((substr_count($enclosure->get_type(),"image") == 0) && (strpos($enclosure->get_link(),".jpg")===false)) 
	    echo '<div align="center"><B><a href="'.$enclosure->get_link().'" target="_blank">['.trad("RSS_TELECHARGER").']</a></B></div><BR>';
	  else echo "<BR>";
	  echo "</DIV>";
	}	
	else echo '<BR><BR></td></tr>';
    $itemlimit = $itemlimit + 1;	
  }
  echo "</TABLE>";
}
else {
  echo "<B>".trad("RSS_ERREUR")."</B> : <BR><a href=\"".$url."\" target=\"_blank\">".$url."</a><BR>";
  if ($feed->error())
	echo "<BR>".$feed->error()."<BR>";  
}
echo "<br></div></TD></TR></TABLE>";
}
elseif ($_GET['config']=="ok") {
  if (count($_POST)>0) {
    if ($_POST['action']=="ajout") {
	  $_POST['titre'] = addslashes($_POST['titre']);
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}rss_reader (rr_util_id,rr_rss_url,rr_rss_titre,rr_rss_accueil) VALUES ('".$idUser."','".$_POST['url']."','".$_POST['titre']."','0')");
	}
    if ($_POST['action']=="supp") {
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}rss_reader WHERE rr_rss_id='".$_POST['sup_id']."'");
	}
    if ($_POST['action']=="enreg") {
	  $nb_item = (count($_POST)-2)/5;
	  for ($i=0;$i<=$nb_item;$i++) {
	    if ($_POST['rss_accueil'.$i] == "on") $checked = 1;
	    else $checked = 0;
		if (($_POST['rss_ordre'.$i] == "") || ($_POST['rss_ordre'.$i] == " ")) $_POST['rss_ordre'.$i] = "x";
		$_POST['rss_titre'.$i] = addslashes($_POST['rss_titre'.$i]);
	    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}rss_reader SET rr_rss_accueil='".$checked."',rr_rss_titre='".$_POST['rss_titre'.$i]."', rr_rss_url='".$_POST['rss_url'.$i]."', rr_rss_ordre='".$_POST['rss_ordre'.$i]."' WHERE rr_rss_id='".$_POST['rss_id'.$i]."'");
	  }
	}
  }
?>
<script>
function supprRSS(id,_titre) {
  var msgAlert;
  msgAlert = "<?php echo trad("RSS_SUP");?> '"+_titre+"' ?";
  if (confirm(msgAlert)) {
    document.forms['form0'].action.value = "supp";
	document.forms['form0'].sup_id.value = id;
    document.forms['form0'].submit();
  }
}
function enregRSS() {
  var msgAlert;
  msgAlert = "<?php echo trad("RSS_MODIF");?> ?";
  if (confirm(msgAlert)) {
    document.forms['form0'].action.value = "enreg";
    document.forms['form0'].submit();
  }
}
</script>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD align="right" width="37" nowrap class="sousMenu"><IMG src="image/trans.gif" alt="" width="35" height="1" border="0"></TD>
    <TD height="28" width="100%" class="sousMenu"><?php echo trad("TITRE_RSS_READER2");?>
	</TD>
    <TD align="right" width="10%" nowrap class="sousMenu" style="text-align:right;"><a href="?<?php echo "sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&tcType="._TYPE_RSS_READER."&config=ok";?>"><img src="image/rss_add.gif" align="absmiddle" border="0">&nbsp;<font style="color:<?php echo $CalNavigationLienHover;?>;"><?php echo trad("RSS_GERER");?></font></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="?<?php echo "sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&tcType="._TYPE_RSS_READER."&options=ok";?>"><img src="image/rss_options.gif" align="absmiddle" border="0">&nbsp;<font style="color:<?php echo $CalNavigationTexte;?>;"><?php echo trad("RSS_OPTIONS");?></font></a>&nbsp;&nbsp;</TD>	
  </TR>
  </TABLE>
  <BR><BR>
  <FORM action="" method="POST">
  <TABLE width="650" bgcolor="<?php echo $ListeChoixFond; ?>">
    <TR><TD colspan="2" width="100%" align="center"class="bordTLRB" height="30"><B><?php echo trad("RSS_AJOUT");?></B></TD></TR>
    <TR><TD class="bordTLRB" height="35"><B>&nbsp;<?php echo trad("RSS_CONF_TITRE");?> : </B><INPUT class="texte" TYPE="TEXT" value="" name="titre"></TD>
	<TD class="bordTLRB" height="35"><B>&nbsp;<?php echo trad("RSS_CONF_URL");?> : </B><INPUT class="texte" TYPE="TEXT" value="" name="url" size="50">&nbsp;&nbsp;<INPUT class="bouton" TYPE="SUBMIT" value="OK"></TD></TR>
  </TABLE>
  <INPUT type="hidden" name="action" value="ajout">
  </FORM>
  <BR>
  <?php
	$DB_CX->DbQuery("SELECT count(*) FROM ${PREFIX_TABLE}rss_reader WHERE rr_util_id=".$idUser." Order By rr_rss_titre");
	$nb_flux = $DB_CX->DbResult(0,0);
	if ($nb_flux>0) {
  ?>
  <FORM action="<?php echo $_SERVER["REQUEST_URI"]."&config=ok";?>" method="POST" name="form0">
  <TABLE width="650" bgcolor="<?php echo $ListeChoixFond; ?>" border="0">
    <TR>
	<TD width="50" height="30" align="center" class="bordTLRB"><B><?php echo trad("RSS_CONF_ACCUEIL");?></B></TD>
	<TD width="100" height="30" align="center" class="bordTLRB"><B><?php echo trad("RSS_CONF_TITRE");?></B></TD>
	<TD width="400" height="30" align="center" class="bordTLRB"><B><?php echo trad("RSS_CONF_URL");?></B></TD>
	<TD width="40" height="30" align="center" class="bordTLRB"><B><?php echo trad("RSS_CONF_ORDRE");?></B></TD>
	<TD width="60" height="30" align="center" class="bordTLRB">&nbsp;</TD>
	</TR>
  	<?php
	$DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}rss_reader WHERE rr_util_id=".$idUser." Order By rr_rss_titre");
	$i=0;
	while ($enr = $DB_CX->DbNextRow()) {
	  $titre_rss = htmlspecialchars(stripslashes($enr['rr_rss_titre']),ENT_QUOTES);
	  if ($enr['rr_rss_accueil'] == "1") $checked = "checked";
	  else $checked = "";
	  if (($enr['rr_rss_ordre'] == "x") || ($enr['rr_rss_ordre'] == " ")) $enr['rr_rss_ordre'] = "";
	  echo "<TR>";
	  echo "<TD width='50' align='center' class='bordTLRB'><input type='checkbox' name='rss_accueil".$i."' ".$checked."></TD>";
	  echo "<TD width='100' class='bordTLRB'><input type='text' name='rss_titre".$i."' value=\"".$titre_rss."\" size='15'></TD>";
	  echo "<TD width='400' class='bordTLRB'>&nbsp;<input type='text' name='rss_url".$i."' value='".$enr['rr_rss_url']."' size='60'></TD>";
	  echo "<TD width='40' class='bordTLRB'>&nbsp;<input type='text' name='rss_ordre".$i."' value='".$enr['rr_rss_ordre']."' size='1'></TD>";	  
	  echo "<TD width='60' class='bordTLRB' align='center'>&nbsp;<INPUT class='bouton' TYPE='button' value='".trad("RSS_S")."' onclick=\"supprRSS('".$enr['rr_rss_id']."','".$titre_rss."');\"></TD></TR>";
	  echo "<INPUT type='hidden' name='rss_id".$i."' value='".$enr['rr_rss_id']."'>";
	  $i++;
	}	
	echo "<TR>";
	echo "<TD align=\"center\" class=\"bordTLRB\" colspan=\"5\" height=\"30\"><input type=\"button\" class=\"bouton\" value=\"Enregistrer\" onclick=\"enregRSS('form0');\"></TD>";
	echo "</TR>";
	}
	?>
	<INPUT type='hidden' name='action' value=''>
	<INPUT type='hidden' name='sup_id' value=''>
  </TABLE>
  </FORM>
<?php
}
elseif ($_GET['options']=="ok") {
  // MAJ des options de l'utilisateur -> action du FORM
  if ($_POST['action']=="options_maj") {
	$rss_opt = $_POST['rss_nb_items'].";".$_POST['rss_nb_col_acc'].";".$_POST['rss_nb_items_acc'].";".$_POST['rss_refresh'];
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}utilisateur SET util_rss_reader='".$rss_opt."' WHERE util_id='".$_POST['id_user']."'");	  
  }
?>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD align="right" width="37" nowrap class="sousMenu"><IMG src="image/trans.gif" alt="" width="35" height="1" border="0"></TD>
    <TD height="28" width="100%" class="sousMenu"><?php echo trad("TITRE_RSS_READER2");?>
	</TD>
    <TD align="right" width="10%" nowrap class="sousMenu" style="text-align:right;"><a href="?<?php echo "sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&tcType="._TYPE_RSS_READER."&config=ok";?>"><img src="image/rss_add.gif" align="absmiddle" border="0">&nbsp;<font style="color:<?php echo $CalNavigationTexte;?>;"><?php echo trad("RSS_GERER");?></font></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="?<?php echo "sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&tcType="._TYPE_RSS_READER."&options=ok";?>"><img src="image/rss_options.gif" align="absmiddle" border="0">&nbsp;<font style="color:<?php echo $CalNavigationLienHover;?>;"><?php echo trad("RSS_OPTIONS");?></font></a>&nbsp;&nbsp;</TD>	
  </TR>
  </TABLE>
  <BR><BR>
  <FORM action="" method="POST">
  <TABLE width="500" bgcolor="<?php echo $ListeChoixFond; ?>" border="0">
    <TR>
	<TD width="250" align="center" height="30" class="bordTLRB"><B><?php echo trad("RSS_OPT_OPTIONS");?></B></TD>
	<TD width="250" align="center" height="30" class="bordTLRB"><B><?php echo trad("RSS_OPT_VAL");?></B></TD>
	</TR>
  	<?php
	$DB_CX->DbQuery("SELECT util_rss_reader FROM ${PREFIX_TABLE}utilisateur WHERE util_id='".$idUser."'");
	list($nb_items,$nb_col_acc,$nb_items_acc,$refresh) = explode(";",$DB_CX->DbResult(0,0));
	if ($nb_items == "") $nb_items = 10;
	if ($nb_col_acc == "") $nb_col_acc = 3;
	if ($nb_items_acc == "") $nb_items_acc = 6;
	if ($refresh == "") $refresh = 600;
	echo '<FORM action="" method="POST" name="form">';
	echo "<TR>";
	echo "<TD width='250' class='bordTLRB'>".trad("RSS_OPT_REFRESH")."</TD><TD width='250' class='bordTLRB'>&nbsp;<input type='text' name='rss_refresh' value='".$refresh."' size='20'>&nbsp;<i>(".trad("RSS_OPT_REFRESH_RES").")</i></TD></TR>";	
	echo "<TD width='250' class='bordTLRB'>".trad("RSS_OPT_NBITEMS")."</TD><TD width='250' class='bordTLRB'>&nbsp;<input type='text' name='rss_nb_items' value='".$nb_items."' size='20'></TD></TR>";
	echo "<TD width='250' class='bordTLRB'>".trad("RSS_OPT_NBCOLACC")."</TD><TD width='250' class='bordTLRB'>&nbsp;<input type='text' name='rss_nb_col_acc' value='".$nb_col_acc."' size='20'></TD></TR>";	
	echo "<TD width='250' class='bordTLRB'>".trad("RSS_OPT_NBITEMACC")."</TD><TD width='250' class='bordTLRB'>&nbsp;<input type='text' name='rss_nb_items_acc' value='".$nb_items_acc."' size='20'></TD></TR>";		
	echo "<TR><TD align='center' height='30' width='500' colspan='2' class='bordTLRB'><INPUT class='bouton' TYPE='submit' value='".trad("RSS_OPT_OK")."'></TD></TR>";
	echo "<INPUT type='hidden' name='action' value='options_maj'>";
	echo "<INPUT type='hidden' name='id_user' value='".$idUser."'>";
	echo "</FORM>";
	?>
  </TABLE>
<?php
}
else {
// Page d'accueil RSS Reader
// MAJ du nombre d'objets par flux (si nécessaire)
if ($_POST['action']=="Change nb obj") {
$DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}rss_reader SET rr_rss_nb_obj=".$_POST['nb_obj_new']." WHERE rr_rss_id=".$_POST['rss_id']);
}
if ($_POST['action']=="Sup flux accueil") {
$DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}rss_reader SET rr_rss_accueil='0' WHERE rr_rss_id=".$_POST['rss_id']);
}
?>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD align="right" width="37" nowrap class="sousMenu"><IMG src="image/trans.gif" alt="" width="35" height="1" border="0"></TD>
    <TD height="28" width="100%" class="sousMenu">
	<form id="form" action="" method="POST"> 
	<?php
	echo trad("TITRE_RSS_READER3");
	$DB_CX->DbQuery("SELECT count(*) FROM ${PREFIX_TABLE}rss_reader WHERE rr_util_id=".$idUser." Order By rr_rss_titre");
	$nb_rss = $DB_CX->DbNextRow();
	if ($nb_rss[0] >0) {
	  echo '<select name="url" onchange="showRSS(this.value,\'form\')">';
	  echo '<option value=""></option>';
	  $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}rss_reader WHERE rr_util_id=".$idUser." Order By rr_rss_titre");
	  while ($enr = $DB_CX->DbNextRow()) {
  	    echo "<option value=\"".$enr['rr_rss_url']."\">".htmlspecialchars(stripslashes($enr['rr_rss_titre']),ENT_QUOTES)."</option>";
	  }
	  echo "</select>";
	  echo "<INPUT type=\"hidden\" name=\"action\" value=\"lire_flux\">";
	  echo "</form>";
	}
	else echo trad("RSS_VIDE");
	?>
	</TD>
    <TD align="right" width="10%" nowrap class="sousMenu" style="text-align:right;"><a href="?<?php echo "sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&tcType="._TYPE_RSS_READER."&config=ok";?>"><img src="image/rss_add.gif" align="absmiddle" border="0">&nbsp;<font style="color:<?php echo $CalNavigationTexte;?>;"><?php echo trad("RSS_GERER");?></font></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="?<?php echo "sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&tcType="._TYPE_RSS_READER."&options=ok";?>"><img src="image/rss_options.gif" align="absmiddle" border="0">&nbsp;<font style="color:<?php echo $CalNavigationTexte;?>;"><?php echo trad("RSS_OPTIONS");?></font></a>&nbsp;&nbsp;</TD>
  </TR>
  </TABLE>
  <BR> 
<?php
// On récupère les infos de configuration de l'utilisateur
$DB_CX->DbQuery("SELECT util_rss_reader FROM ${PREFIX_TABLE}utilisateur WHERE util_id='".$idUser."'");
list($nb_items,$nb_col_acc,$nb_items_acc,$refresh) = explode(";",$DB_CX->DbResult(0,0));
if ($nb_col_acc == "") $nb_col_acc = 3;
if ($nb_items_acc == "") $nb_items_acc = 6;
if ($refresh == "") $refresh = 600;


include_once('simplepie/simplepie.inc.php'); // Include SimplePie
function affiche_xml($url,$id_form,$rss_id,$nb_obj,$ordre,$rss_titre) {
global $nb_col_acc,$nb_items_acc,$refresh;
$itemlimit = 0;

$feed = new SimplePie(); // Create a new instance of SimplePie
$feed->set_feed_url($url);
$feed->set_cache_location ('simplepie/cache/'); // Set the cache location
$feed->set_cache_duration ($refresh); // Set the cache time
$feed->set_output_encoding('iso-8859-1');
$feed->set_favicon_handler('simplepie/tools/handler_image.php');
$success = $feed->init(); // Initialize SimplePie
$feed->handle_content_type(); 
if ($success) {
  if (!$favicon = $feed->get_favicon()) {
	$favicon = 'simplepie/tools/alternate.png';
	}
  echo '<form id="form'.$id_form.'" action="" method="POST">';
  echo '<table height="100%" width="100%" border="0">';
  echo '<tr>';
  echo '<td height="20">&nbsp;<a href="#" onclick="showRSS(this.value,\'form'.$id_form.'\')" >';
  echo '<img src="'.$favicon.'" align="absmiddle" width="16" border="0"/>&nbsp;<B>'.$feed->get_title().'</B></a></td>';
  echo '<td valign="top" align="right" width="80">';
  echo '<SELECT name="nb_obj_new" onchange="changeRSSnbobj(this.value,'.$rss_id.',\'form'.$id_form.'\');">';
  if ($nb_obj==0) $nb_obj=$nb_items_acc;
  for ($i=($nb_obj-5);$i<=($nb_obj+5);$i++) {
    if ($i>0) {
      if ($i != $nb_obj ) echo '<OPTION>'.$i.'</OPTION>';
	  else  echo '<OPTION selected>'.$i.'</OPTION>';
	}
  }
  echo '</SELECT>&nbsp;';
  echo '<input size="1" type="button" class="bouton" onclick="suppRSS('.$rss_id.',\''.$rss_titre.'\',\'form'.$id_form.'\');" value="X">';
  echo '</td>';
  echo '</tr>';
  // debut de la liste
  echo '<tr><td colspan="2">';
  echo "<font style=\"font-size:smaller;\">(".$feed->get_description().")</font>";
  echo '<ul>';
  foreach($feed->get_items() as $item) {
	echo '<li><a href="'.$item->get_permalink().'" title="'.$item->get_title().'" target="_blank">'.$item->get_title().'</a></li>';
	$itemlimit++;
    if ($itemlimit==$nb_obj) break;
	else echo "<br>";
  }
  echo '</ul>';
  echo '</td></tr></table>';
  echo "<INPUT type=\"hidden\" name=\"rss_id\" value=\"".$rss_id."\">";  
  echo "<INPUT type=\"hidden\" name=\"action\" value=\"lire_flux\">";
  echo "<INPUT type=\"hidden\" name=\"url\" value=\"".$url."\">";    
  echo '</form>'; 
}
else {
  echo '<table height="100%" width="100%" border="0">';
  echo '<tr><td>';
  echo "<B>".trad("RSS_ERREUR")."</B> : <BR><a href=\"".$url."\" target=\"_blank\">".$url."</a><BR>";
  if ($feed->error())
  echo "<BR>".$feed->error();
  echo "</td><td valign=\"top\" align=\"right\"";
  echo '<form id="form'.$id_form.'" action="" method="POST">';
  echo '<input size="1" type="button" class="bouton" onclick="suppRSS('.$rss_id.',\''.$rss_titre.'\',\'form'.$id_form.'\');" value="X">';
  echo "<INPUT type=\"hidden\" name=\"rss_id\" value=\"".$rss_id."\">";  
  echo '</form>'; 
  echo '</td></tr></table>';
}
$feed->__destruct(); // Do what PHP should be doing on it's own.
unset($feed); 
}


echo '<TABLE cellspacing="0" cellpadding="5" width="99%" border="0"><TR><TD></TD>';
$i=0;
$width = 100/$nb_col_acc;
$DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}rss_reader WHERE rr_util_id=".$idUser." and rr_rss_accueil=1 Order By rr_rss_ordre,rr_rss_titre ASC");
while ($enr = $DB_CX->DbNextRow()) {
  $reste = $i%$nb_col_acc;
  if ($reste == 0) echo '</TR><TR>';
  echo '<TD align="left" width="'.$width.'%" valign="top">';  
  echo '<TABLE cellspacing="0" cellpadding="2" bgcolor="'.$ListeChoixFond.'" width="100%" height="100%" border="0">';
  echo '<tr><td class="bordTLRB">';
  affiche_xml($enr['rr_rss_url'],$i,$enr['rr_rss_id'],$enr['rr_rss_nb_obj'],$enr['rr_rss_ordre'],$enr['rr_rss_titre']);
  echo '</td></tr>';
  echo '</table>';
  echo '</TD>'; 
  $i++;
 }
echo '</TR>';
echo '</TABLE>';
// Il n'y a pas de flux configuré en page d'accueil RSS
if ($i==0) {
  echo '<TABLE cellspacing="0" cellpadding="5" bgcolor="'.$ListeChoixFond.'" width="50%" height="50" border="0"><tr>';
  echo '<td align="center" height="30" class="bordTLRB">';
  echo trad("RSS_ACCUEIL_VIDE");
  echo "</TD></TR></TABLE>";
}
}
?>
