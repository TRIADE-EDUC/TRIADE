<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: backup.inc.php,v 1.15 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;

print "<table border=\"0\">";
print "<tr><td class=\"formtitle\">";
print "$msg[529]";
print "</td></tr><td>";

// initialisation

if(!empty($file)) {
	@set_time_limit(0);
	$dump_buffer = '';
	// définition du retour chariot
	$crlf = "\n";
	$db = "bibli";
	$today = date("d/m/Y H:i:s");
	$separator = "# ------------------------------------$crlf";
	
	// construction du dump
	$tables = pmb_mysql_list_tables($db);
	$num_tables = @pmb_mysql_num_rows($tables);
	
	// en-tête
	$dump_buffer .= "$separator# pmb MySQL-Dump$crlf";
	$dump_buffer .= "# $today$crlf";
	$dump_buffer .= "# backup base \"$db\"$crlf";
	$dump_buffer .= $separator.$crlf;
	
	$i = 0;
	while($i < pmb_mysql_num_rows($tables)) {
		$table[$i] = pmb_mysql_tablename($tables, $i);
		$i++;
	}
	
	foreach ($table as $cle => $valeur) {
	
		$requete = "SHOW CREATE TABLE $valeur";
		$result = pmb_mysql_query($requete, $dbh);
		$create = pmb_mysql_fetch_row($result);
	
		// écriture de la méthode de création
		$dump_buffer .= "$crlf$separator# structure de la table $valeur$crlf$separator$crlf";
		$dump_buffer .= "DROP TABLE IF EXISTS ".$valeur.";$crlf";
		$dump_buffer .= $create[1].";$crlf";
		$dump_buffer .= "$crlf$separator# contenu de la table $valeur$crlf$separator$crlf";
	
		// écriture des données
	    $requete = "SELECT * FROM $valeur";
		$result = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = pmb_mysql_num_rows($result);
	
		$field_set = array();
		$field = array();
	
		for($i = 0; $i < $nbr_lignes; $i++) {
			$row = pmb_mysql_fetch_row($result);
			// on regarde si le champ est un entier
			for ($j=0; $j < pmb_mysql_num_fields($result); $j++) {
				$field_set[$j] = pmb_mysql_field_name($result, $j);
				$type = pmb_mysql_field_type($result, $j);
				if ($type=='tinyint'||$type=='smallint'||$type=='mediumint'||$type=='int'||$type=='bigint'||$type=='timestamp') {
					$field[$j] = $row[$j];
				} else {
					$field[$j] = "'".addslashes($row[$j])."'";
				}
			}
	    	$fields = implode(', ', $field_set);
	    	$content = implode(', ', $field);
			$dump_buffer .= "INSERT INTO ".$valeur." ($fields) VALUES ($content);$crlf";
	
		}
	
	}
	
	$file = "./tables/".$file.".sql";
	
	@set_time_limit(0); // timeout illimité
	
	// écriture du fichier
	$fp = @fopen($file, 'wb');
	if($fp) {
		$result = @fwrite($fp, $dump_buffer);
		if($result) {
			$size = number_format($result/1024,2);
			print "<strong><font color=#ff0000>$msg[528]</font></strong><br />$file&nbsp;: $size Ko écrits";
		} else {
			user_error_message(2);
		}
		fclose($fp);
	} else {
		user_error_message(2);
	}

} else {

	?>
	<script type="text/javascript">
	function test_form(form)
	{
		if(form.file.value.length == 0)
		{
			alert("<?php echo $msg[530]; ?>");
			return false;
		}
		return true;
	}
	
	</script>
	<?php
	$backup_form = "
		<form class='form-$current_module' name='backup_form' method='post' action='./admin.php?categ=misc&sub=backup'>
			<table border='0'>
			<tr>
				<td>
					<small>$msg[531]</small>
					<input type='text' size='24' name='file'><br />
				</td>
			</tr>
			<tr>
				<td class='align_right'>
					<input type='button' class='bouton' value='$msg[76]' onClick='document.location=\'./admin.php\''>
					<input type='submit' class='bouton' value='$msg[77]' onClick='return test_form(this.form)'>
				</td>
			</tr>
			</table>
		</form>
	";
	print $backup_form;
}
?>
