<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_empr.inc.php,v 1.48.4.3 2025/05/27 09:09:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg, $lang, $charset, $current_module;
global $action, $envoyer, $Deleter, $Precedent, $Actualiser, $Enregistrer;
global $pmb_lecteurs_localises, $empr_login;
global $empr_location, $deflt2docs_location;
global $plus1;

require_once $class_path."/emprunteur.class.php";
require_once $class_path."/import/import_empr.class.php";

//modif Massimo Mancini & Marco Vaninetti
// modif sauvegarde $OrdreImportEmpr en base
$base_path=".";


global $from_ldap;
if (!isset($from_ldap)) {
    $from_ldap = 0;
} else {
    $from_ldap = intval($from_ldap);
}

$querry = "select valeur_param from parametres where type_param = 'empr' and sstype_param='corresp_import' ";
$res = pmb_mysql_query($querry);
$obj = pmb_mysql_fetch_object($res) ;

if ($obj->valeur_param) {
	$OrdreImportEmpr=unserialize($obj->valeur_param);
} else {
	$querry = "DESCRIBE empr;";
	$res = pmb_mysql_query($querry);
	$nbr = pmb_mysql_num_rows($res);
	if($nbr) {
		$k = 0;
		$OrdreImportEmpr = [];
		for($j=0;$j<$nbr;$j++) {
			$row=pmb_mysql_fetch_row($res);
			$OrdreImportEmpr[$row[0]] = 0;
		}
	}
	// recup des champs perso
	$querry = "SELECT * from empr_custom;";
	$res = pmb_mysql_query($querry);
	if (pmb_mysql_num_rows($res)) {
		while (($row=pmb_mysql_fetch_array($res,PMB_MYSQL_ASSOC))){
			$OrdreImportEmpr[$row['name']] = 0;
		}
	}
}

print "
	<script type='text/javascript'>
		function aide_regex() {
			openPopUp('./help.php?whatis=import_empr', 'regex_howto');
		}
	</script>";


function show_import_choix_fichier($from_ldap) {

	global $msg;
	global $charset;
	global $current_module ;

	// premier formulaire pour avoir le nom du fichier a importer, le separateur de champ
	if (!$from_ldap) {
		$formtype= "<form class='form-$current_module' name='form1' ENCTYPE=\"multipart/form-data\" method='post' action=\"./admin.php?categ=empr&sub=implec&action=FichierOK\">
		<h3>".$msg["empr_import"]."</h3>
		<div class='form-contenu'>
		<div class='row'>
				<div class='colonne60'>
					<label class='etiquette' for='form_import_lec'>".$msg["import_lec_fichier"]."</label>
					<input name='import_lec' accept='text/csv, text/plain' type='file'  size='40' />
				</div>
				<div class='colonne_suite'>
					<label class='etiquette' for='form_import_lec'>". $msg["import_lec_separateur"]."</label>
					<input type='text' value=',' name='Sep_Champs' class='saisie-2em' />
				</div>
		</div>
		<br /><br />";
	} else {
		$formtype="<form class='form-$current_module' name='form1' method='post' action=\"./admin.php?categ=empr&sub=implec&action=FichierOK&from_ldap=1\">
					<input type='hidden'  value='|' name='Sep_Champs' />
		<h3>".$msg["import_ldap"]."</h3>
		<div class='form-contenu'>";
	}

	//La categorie, le code statistique et l'encodage
	print "
		$formtype
		<div class='row'>
			<div class='colonne2'>
				".import_empr::get_categories_selector('selectGroupe')."
		</div>
		<div class='colonne2'>
			".import_empr::get_codestat_selector('selectStat')."
		</div>
	</div>
	<br /><br />
	<div class='row'>
		<label class='etiquette' for='encodage_fic_source' id='text_desc_encodage_fic_source' name='text_desc_encodage_fic_source'>".htmlentities($msg["admin_import_encodage_fic_source"],ENT_QUOTES,$charset)."</label>
		".import_empr::get_encoding_selector()."
	</div>
	<br />
	</div>
	<div class='row'>
		<table class='table-but'><tr>
		<td class='td-lbut'>
			<input type='submit' class='bouton' name='envoyer' value='".$msg[502]."' />
		</td>
		<td class='td-rbut'>
			<input type='submit' class='bouton' name='Deleter' value='".$msg["import_lec_effacer"]."' />
		</td>
		</tr></table>

	</div>
	</div>
		<input type='hidden'  name='from_ldap' value='$from_ldap' />
	</form>
	";

}

function show_import($buffer,$from_ldap) {
	// formulaire ou l'on choisit dans quel champ on met les donnees qui souvent
	// proviennent d'une autre base de donnees, le code ne supporte que les fichiers
	// texte, il se peut que la page "plante" si vous envoyez des donnees qui pourraient
	// etre incompatible, il faut faire en sorte qu'ils soient du meme type sauf exceptions
	// genre un vachar qui va dans le year(int), s'il n'y a pas de lettre. Le code est
	// loin d'etre STABLE, il faut donc faire attention a ne pas envoyer des donnees erronees.
    global $msg, $charset;
	global $OrdreImportEmpr;
	global $import_lec;
	global $Sep_Champs;
	global $current_module ;
	print "<form class='form-$current_module' name='form2' method='post' action=\"./admin.php?categ=empr&sub=implec&action=enregistre\">";
	print "<h3>".$msg["select_col"]."&nbsp;&nbsp;&nbsp;<a href='#' onclick='aide_regex(); return false'>[".$msg[1900]."</a>]</h3><div class='form-contenu'><table width='98%' border='0' cellspacing='10'>";
	print "        <td class='jauge'><b>".$msg["champ_dans_base_donnee"]."</b></td>";
	print "        <td class='jauge' width='27%'><b>".$msg["champ_dans_texte"]."</b></td>";
	print "        <td class='jauge' width='60%'><b>".$msg["first_line_file"]."</b></td>";

	// pourrait utiliser la fonction desc_table, a faire plus tard! parce que c'est deja fonctionnel
	$querry = "DESCRIBE empr;";
	$res = pmb_mysql_query($querry);
	$nbr = pmb_mysql_num_rows($res);

	$ordre = array();
	if($nbr) {
		$k = 0;
		for($j=0;$j<$nbr;$j++) {

			$row=pmb_mysql_fetch_row($res);

			if (empty($_POST[$row[0]])) {
				$ordre[$k] = (isset($OrdreImportEmpr[$row[0]]) ? $OrdreImportEmpr[$row[0]] : '');
			} else {
				$ordre[$k] = $_POST[$row[0]];
			}
			$val_buff = (isset($buffer[$ordre[$k]]) ? $buffer[$ordre[$k]] : '');

			print "<tr>";

			if ($row[0] == "empr_adr1") {

				print "<td class='nobrd'>$row[0]</td>";
				print "<td class='nobrd'><input name='".$row[0]."' value='".$ordre[$k]."' type='text' size='1' />";
				$k++;
				if (empty($_POST['plus1'])) {
					$ordre[$k] = $OrdreImportEmpr[$row[0]];
				} else {
					$ordre[$k] = $_POST['plus1'];
				}

				print "<td class='nobrd'><input name='exem$k' value='$val_buff' type='text' disabled size='40' /></td>";

			} elseif ($row[0] == "id_empr" || $row[0] == "empr_categ" || $row[0] == "empr_codestat" || $row[0] == "empr_creation" || $row[0] == "empr_modif" || $row[0] == "empr_date_adhesion" || $row[0] == "empr_date_expiration" ||$row[0] == "empr_ldap") {

					print "<td class='nobrd'><span style='color:#FF0000'>$row[0]</span></td>";
					print "<td class='nobrd'><input name='".$row[0]."' value='".$ordre[$k]."' type='text' size='1' disabled /></td>";
					print "<td class='nobrd'><input name='exem$k' value='$val_buff' type='text' disabled size='40' /></td>";

			} elseif(in_array($row[0], array('empr_cb', 'empr_nom', 'empr_login'))) {

				print "<td class='nobrd'>$row[0] *</td>";
				print "<td class='nobrd'><input name='".$row[0]."' value='".$ordre[$k]."' type='text' size='1' /></td>";
				print "<td class='nobrd'><input name='exem$k' value='$val_buff' type='text' disabled size='40' /></td>";

			} else {

				print "<td class='nobrd'>$row[0]</td>";
				print "<td class='nobrd'><input name='".$row[0]."' value='".$ordre[$k]."' type='text' size='1' /></td>";
				print "<td class='nobrd'><input name='exem$k' value='$val_buff' type='text' disabled size='40' /></td>";
			}
			print "</tr>";
			$k++;
		}
	}

	// recup des champs perso
	$querry = "SELECT * from empr_custom ";
	$res = pmb_mysql_query($querry);
	if (pmb_mysql_num_rows($res)) {
		print "<tr><td colspan='3' class='nobrd'><hr /></td></tr>";
		print "<tr><td colspan='3' class='nobrd'><b>".htmlentities($msg['1131'], ENT_QUOTES, $charset)."</b></td></tr>";
		while (($row=pmb_mysql_fetch_array($res,PMB_MYSQL_ASSOC))) {
			print "<tr>";

			if (empty($_POST[$row['name']])) {
				$ordre[$k] = $OrdreImportEmpr[$row['name']];
			} else {
				$ordre[$k] = $_POST[$row['name']];
			}
			$val_buff = (isset($buffer[$ordre[$k]]) ? $buffer[$ordre[$k]] : '');
			print "<td class='nobrd'>".$row['name']."</td>";
			print "<td class='nobrd'><input name='".$row['name']."' value='".$ordre[$k]."' type='text' size='1' /></td>";
			print "<td class='nobrd'><input name='exem$k' value='$val_buff' type='text' disabled size='40' /></td>";

			print "</tr>";
			$k++;
		}
	}

	print "  </table>";
	print "
		<input name='import_lec' value='$import_lec' type='hidden' />
		<input name='Sep_Champs' value='$Sep_Champs' type='hidden' />
		<input name='from_ldap' value='$from_ldap' type='hidden' />
		<input name='selectGroupe' value='".$_POST['selectGroupe']."' type='hidden' />
		<input name='selectStat' value='".$_POST['selectStat']."' type='hidden' />
		</div>
		<div class='row'>
				<input name='Actualiser' value='".$msg["actualiser_page"]."' type='submit' class='bouton' />
				<input name='Enregistrer' value='".$msg["enregistrer_tout"]."' type='submit' class='bouton' />
		</div></form>";
}

function choix_supp_empr($from_ldap) {
    global $msg, $current_module;
    global $empr_date_expiration;

	$querry = "Select empr_date_expiration From empr order by empr_date_expiration;";
	$result = pmb_mysql_query($querry) or die($msg["select_echoue"]."!<p>".$querry);
	if (pmb_mysql_num_rows($result) >= 1) {
		// choisir la date a deleter d'apres les dates d'expirations
		print "<form class='form-$current_module' name='form3' method='post' action=\"./admin.php?categ=empr&sub=implec&action=ConfirmationDel\">";
		print "<h3>".$msg["date_enlever"]."</h3>";
		$compteur = 0;
		print "<table border='0'>";
		while(($row = pmb_mysql_fetch_row($result))) {
			$compteur++;
			$empr_date_expiration_tmp = $row[0];
			if( $empr_date_expiration != $empr_date_expiration_tmp) {
				$empr_date_expiration = $row[0];
				print "<tr>";
				print "<td>".$msg["ut_date_exp"].$row[0]."</td>";
				print "<td><input type='checkbox' name='datedel".$compteur."' value='$row[0]' /></td>";
				print "</tr>";
			}
		}
		print "</table>";
		print "
		<div cass='row'>
				<table class='table-but'><tr>
				<td class='td-rbut'>
					<input type='submit' class='bouton' name='Confirmation' value='".$msg["ut_deleter"]."' /></td></table>
				</td></tr></table>
		</div>
		<input name='from_ldap' value='$from_ldap' type='hidden' />
		</form>";
	} else
		print ($msg["no_empr_del"]);
}

function desc_table($table) {

	$querry = "Select * from $table";
	$res = pmb_mysql_query($querry);
	$nbr = pmb_mysql_num_fields($res);

	if($nbr) {
		$desc_table = [];
		for($j=0;$j<$nbr;$j++) {
				$desc_table[$j][0] = pmb_mysql_field_name($res,$j);
				$desc_table[$j][1] = pmb_mysql_field_type($res,$j);
				$desc_table[$j][2] = pmb_mysql_field_len($res,$j);
				$desc_table[$j][3] = estNumerique($desc_table[$j][1], $desc_table[$j][2]);
				// j'ai seulement besoin de l'auto_increment, $desc_table[$j][4] est vrai si trouve !
				$desc_table[$j][4] = array_search ( "auto_increment", explode( " ", pmb_mysql_field_flags($res,$j) ) );
		}
		//print_r($desc_table);
		return $desc_table;
	}
}

function estNumerique($field_type, $field_len) {
	// ceci est le meme code que dans la class mysql_backup
	$is_numeric=true;

	switch(strtolower($field_type)) {
		case "blob":
		case "string":
		case "date":
		case "datetime":
		case "time":
			$is_numeric=false;
			break;
	}
	return $is_numeric;
}

// string de la date d'aujourd'hui ou avec quelques jours de plus
function aujourdhui($nbjour=0) {
	$date1 = date("Y-m-d", time() + 3600*24*$nbjour);
	return strval($date1);
}

// sert a savoir si un etudiant existe deja
function return_cb($cb) {
    if (empty($cb)) {
        return '';
    }
	$query = "select empr_cb from empr where empr_cb = '".addslashes($cb)."' ";
	$result = pmb_mysql_query($query);
	if (pmb_mysql_num_rows($result)) {
	    $row = pmb_mysql_fetch_row($result);
	    return $row[0];
	}
	return '';
}

// save fields/text association - MaxMan
function save_fields_association() {
	$OrdreImportEmpr = array();
	reset($_POST);
	foreach ($_POST as $kk => $vv) {
		if (!preg_match("/$kk/i","import_lec Sep_Champs selectGroupe Actualiser")) {
			$OrdreImportEmpr[$kk]=$vv;
		}
	}
	$querry = "update parametres set valeur_param='".serialize($OrdreImportEmpr)."' where type_param = 'empr' and sstype_param='corresp_import' ";
	pmb_mysql_query($querry);
}

switch($action) {

	case 'FichierOK':
	    
		if($envoyer) {
			if (!$from_ldap) {
				if (!($_FILES['import_lec']['tmp_name'])) {
					print $msg["click_prec_fic"];
				} elseif (!(move_uploaded_file($_FILES['import_lec']['tmp_name'], "./temp/".basename($_FILES['import_lec']['tmp_name'])))) {
					print $msg["fic_no_tel"]."<br />";
				}
				$ficher = @fopen( "./temp/".basename($_FILES['import_lec']['tmp_name']), "r" );
				$import_lec = basename($_FILES['import_lec']['tmp_name']);
			} else {
				$ficher = @fopen( "./temp/ldap_users.txt", "r" );
				$import_lec = "./temp/ldap_users.txt";
				$Sep_Champs='|';
			}
			if($ficher)	{
				$buffer = fgets($ficher, 1000);
				$buffer = import_empr::get_encoded_buffer($buffer);
				$buffer = explode ($Sep_Champs, $buffer);
				$cpt = count($buffer) - 1;
				for($j=$cpt; $j>=0; $j--) {
					$buffer[$j+1] = trim($buffer[$j]);
				}
				$buffer[0] = "";
				fclose($ficher);
				show_import($buffer, $from_ldap);
			}

		} elseif($Deleter || $Precedent) {
				choix_supp_empr($from_ldap);
        } else {
				show_import_choix_fichier($from_ldap);
		}
		break;

	case 'enregistre':

		if ($Actualiser) {

			save_fields_association();
			if ($from_ldap){
				$ficher = @fopen( "./temp/ldap_users.txt", "r" );
				$Sep_Champs='|';
			} else {
				$ficher = @fopen( "./temp/".$import_lec, "r" );
			}
			if($ficher)	{
				$buffer = fgets($ficher, 1000);
				$buffer = import_empr::get_encoded_buffer($buffer);
				$buffer = explode ($Sep_Champs, $buffer);
				$cpt = count($buffer) - 1;
				for($j=$cpt; $j>=0; $j--) {
					$buffer[$j+1] = trim($buffer[$j]);
				}
				$buffer[0] = "";
				fclose($ficher);
				show_import($buffer,$from_ldap);
			}
		}

		if ($Enregistrer) {

		    save_fields_association();
			// download le fichier au complet

			if ($from_ldap) {
				$file_lec = "./temp/ldap_users.txt";
				$Sep_Champs='|';
			} else {
				$file_lec = "./temp/$import_lec";
			}
			if (file_exists($file_lec)) {
                $ficher = fopen( $file_lec, "r" );
			} else {
			    $ficher = false;
			}
			if($file_lec != './temp/' && $ficher)	{

				//champs perso
				$perso=array();
				$querry = "SELECT * from empr_custom;";
				$res = pmb_mysql_query($querry);
				$k=0;
				if (pmb_mysql_num_rows($res)) {
					while ($row=pmb_mysql_fetch_array($res,PMB_MYSQL_ASSOC)) {
						$perso[$k++]=$row;
					}
				}

				$result = pmb_mysql_query("Select duree_adhesion From empr_categ where id_categ_empr='{$_POST['selectGroupe']}';") or die($msg["err_sql"]);
				$row = pmb_mysql_fetch_row($result);
				$dur=htmlentities($row[0],ENT_QUOTES, $charset);
				// mise en tampon du ficher
				$buffer = fread ( $ficher, filesize ($file_lec));
				$buffer = import_empr::get_encoded_buffer($buffer);

				if (preg_match('/\r\n/',$buffer)) {
					//txt msdos
					$bufferLine = explode("\r\n", $buffer);
				} else {
					//txt linux
					$bufferLine = explode("\n", $buffer);
				}

				// on enleve les [enter] de trop en fin de fichier
				//TODO enlever les enter(/r/n) en milieu du fichier
				// s'il y en a, le prog va inserer des donnees vides et les afficher
				// comme erreur
				// si le fichier est vide ca va creer une boucle sans fin!!!
				while(end($bufferLine) == "") {
					array_pop($bufferLine);
				}
				// check la table empr
				$desc_empr = desc_table("empr");

				$nbChamp_empr = count($desc_empr);
				//
				//  traitement du buffer pour chaque ligne
				//
				foreach($bufferLine as $tmp){
					$bufferChamp = explode ($Sep_Champs, $tmp);
					$cpt = count($bufferChamp) - 1;
					for($j=$cpt; $j>=0; $j--) {
						// enleve les " et les espaces en debut et fin du string (pour chaque champ!)
						$bufferChamp[$j+1] = trim($bufferChamp[$j], "\"");
						$bufferChamp[$j+1] = pmb_mysql_escape_string( $bufferChamp[$j+1] );
					}
					$bufferChamp[0] = "";

					if ( return_cb($bufferChamp[${$desc_empr[1][0]}]) )	{

						// ca veut dire que c'est un update d'une personne deja dans la BD
						for($i = 1; $i < $nbChamp_empr; $i++) {
							if(!$desc_empr[$i][4] && !empty(${$desc_empr[$i][0]}) && !empty($bufferChamp[${$desc_empr[$i][0]}])){ // s'il n'est pas auto incrementable
								// cas particulier : on reçoit le libelle de la localisation
								if($desc_empr[$i][0] == 'empr_location' && !intval($bufferChamp[${$desc_empr[$i][0]}])) {
									$query2="select idlocation from docs_location where location_libelle='".addslashes($bufferChamp[${$desc_empr[$i][0]}])."' limit 1";
									$res = pmb_mysql_query($query2);
									if (pmb_mysql_num_rows($res)) {
										$query2 = 'update empr set '.$desc_empr[$i][0].' = "'.substr ( pmb_mysql_result($res,0,0), 0, $desc_empr[$i][2]).'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
										pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<br />";
									}
								} else {
									$query2 = 'update empr set '.$desc_empr[$i][0].' = "'.substr ( $bufferChamp[${$desc_empr[$i][0]}], 0, $desc_empr[$i][2]).'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
									pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<br />";
								}
							}
						}
						// update pour inserer la date de creation , modif, date_adhesion...
						$query2 = 'update empr set empr_modif  = "'.aujourdhui().'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						$query2 = 'update empr set empr_categ = "'.$_POST['selectGroupe'].'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						$query2 = 'update empr set empr_codestat =  "'.$_POST['selectStat'].'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						$query2 = 'update empr set empr_date_expiration = "'.aujourdhui($dur).'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						if ($from_ldap){
							$query2 = "update empr set empr_ldap = '1' where empr_cb = '".$bufferChamp[${$desc_empr[1][0]}]."';";
							pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						}

					} else {

						// sinon le cb n'existe pas, donc c'est une nouvelle personne
						$fields = "";
						$values = "";
						for($i = 1; $i < $nbChamp_empr; $i++) {
							if(empty($desc_empr[$i][4]) && !empty($bufferChamp[${$desc_empr[$i][0]}])) { // s'il n'est pas auto incrementable et que le $bufferChamp n'est pas vide
								// remplit le $fields dans : insert into empr($fields)...
								if($fields) {
									$fields .= ', '.$desc_empr[$i][0];
								} else {
									$fields .= $desc_empr[$i][0];
								}
								// on concatène si values n'est pas vide
								if($values) {
									$values .= ', ';
								}
								// cas particulier : on reçoit le libelle de la localisation
								if($desc_empr[$i][0] == 'empr_location' && !intval($bufferChamp[${$desc_empr[$i][0]}])) {
									$query2="select idlocation from docs_location where location_libelle='".addslashes($bufferChamp[${$desc_empr[$i][0]}])."' limit 1";
									$res = pmb_mysql_query($query2);
									if (pmb_mysql_num_rows($res)) {
										$values .= '"'.substr ( pmb_mysql_result($res,0,0), 0, $desc_empr[$i][2]).'"';
									} else {
										$values .= '"'.substr ( $deflt2docs_location, 0, $desc_empr[$i][2]).'"';
									}
								} else {
									$values .= '"'.substr ( $bufferChamp[${$desc_empr[$i][0]}], 0, $desc_empr[$i][2]).'"';
								}
								if($i == 4) { // l'exception pour l'adresse (2 champs dans le 2e formulaire)
									if($values) {
										$values = substr($values, 0, strlen($values) - 1);
										$values .= ', '.substr ( $bufferChamp[$plus1], 0, $desc_empr[$i][2]).'"';
									} else
										$values .= '"'.substr ( $bufferChamp[$plus1], 0, $desc_empr[$i][2]).'"';
								}
							}
						}
						$query = "insert into empr ($fields) values ($values);";

						pmb_mysql_query($query) or print $msg["ins_echoue"]."<p>".$query."<hr />";
						// update pour inserer la date de creation , modif, date_adhesion...
						$query2 = 'update empr set empr_creation = "'.aujourdhui().'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						$query2 = 'update empr set empr_modif  = "'.aujourdhui().'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						$query2 = 'update empr set empr_date_adhesion = "'.aujourdhui().'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						$query2 = 'update empr set empr_date_expiration = "'.aujourdhui($dur).'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						$query2 = 'update empr set empr_categ = "'.$_POST["selectGroupe"].'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						$query2 = 'update empr set empr_codestat = "'.$_POST["selectStat"].'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						if ($from_ldap){
							$query2 = "update empr set empr_ldap = '1' where empr_cb = '".$bufferChamp[${$desc_empr[1][0]}]."';";
							pmb_mysql_query($query2) or print $msg["upd_echoue"]."<p>".$query2."<hr />";
						}
					}

					// Gestion de la localisation des lecteurs; prend la loc par défaut du user si non défini
					if ($pmb_lecteurs_localises) {
						if (!$empr_location || !$bufferChamp[$empr_location]) {
							$req_location = 'update empr set empr_location = "'.$deflt2docs_location.'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
							pmb_mysql_query($req_location) or print $msg["upd_echoue"]."<p>".$req_location."<hr />";
						}
					}
					if(!$empr_login) {
						$query = 'select empr_nom, empr_prenom from empr where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'"';
						$result = pmb_mysql_query($query);
						$row = pmb_mysql_fetch_object($result);
						$calculated_login = emprunteur::do_login($row->empr_nom, $row->empr_prenom);
						$req_login = 'update empr set empr_login = "'.$calculated_login.'" where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'";';
						pmb_mysql_query($req_login) or print $msg["upd_echoue"]."<p>".$req_login."<hr />";
					}

                    //Recuperation donnees lecteur pour maj mot de passe et champs perso
					$query3 = 'select id_empr, empr_login, empr_password, empr_password_is_encrypted from empr where empr_cb = "'.$bufferChamp[${$desc_empr[1][0]}].'" ';

					$res = pmb_mysql_query($query3) or print $msg["upd_echoue"]."<p>".$query3."<hr />";
					if(pmb_mysql_num_rows($res)) {

					   $row = pmb_mysql_fetch_assoc($res);

					   if( $row['empr_password_is_encrypted'] == 0) {
    					   //Chiffrement du mot de passe
                            //On verifie que le mot de passe lecteur correspond aux regles de saisie definies
        					//Si non, encodage dans l'ancien format
        					$old_hash = false;
    					    $check_password_rules = emprunteur::check_password_rules((int) $row['id_empr'], $row['empr_password'], [], $lang);
    				        if( !$check_password_rules['result'] ) {
    				            $old_hash = true;
    				        }
    					    emprunteur::update_digest($row['empr_login'], $row['empr_password']);
    					    emprunteur::hash_password($row['empr_login'], $row['empr_password'], $old_hash);
					   }
					    //on passe aux champs perso
					    $empr_id=$row['id_empr'];
					    reset($perso);
					    foreach ($perso as $cp) {
					        $querry ="SELECT * FROM empr_custom_values WHERE empr_custom_champ = ".$cp['idchamp']." AND empr_custom_origine=".$empr_id.";";
					        $res = pmb_mysql_query($querry);
					        if (pmb_mysql_num_rows($res)) { // le champ existe: update
					            $field="empr_custom_".$cp['datatype'];
					            if (isset($bufferChamp[$_POST[$cp['name']]])) {
					                $value="'".$bufferChamp[$_POST[$cp['name']]]."'" ;
					            } else {
					                $value="''" ;
					            }
					            $querry = "UPDATE empr_custom_values SET $field = $value WHERE empr_custom_champ = ".$cp['idchamp']." AND empr_custom_origine=".$empr_id.";";
					            $res = pmb_mysql_query($querry) or print $msg["upd_echoue"]."<p>".$querry."<hr />";

					        } else { //le champ n'existe pas: insert
					            $fields="empr_custom_champ,empr_custom_origine,empr_custom_".$cp['datatype'];
					            $values=$cp['idchamp'].",".$empr_id.",'".$bufferChamp[$_POST[$cp['name']]]."'" ;
					            $querry = "INSERT INTO empr_custom_values ($fields) VALUES ($values);";
					            $res = pmb_mysql_query($querry) or print $msg["ins_echoue"]."<p>".$querry."<hr />";
					        }
					    }
					} else {
						$msg["upd_echoue"]."<p>".$query3."<hr />";
					}
				}
				fclose($ficher);
				print $msg["personnes_upd"].count($bufferLine)."<p>";
				print "<a href='./admin.php?categ=empr&sub=implec' title='Retour'><img name='gg.gif' src='".get_url_icon('gg.gif')."' width='38' height='26'></a>";
			} else {
				die($msg["choix_fi"]);
			}
		}
		break;

	case 'ConfirmationDel':
		print "<form class='form-$current_module' name='form4' method='post' action=\"./admin.php?categ=empr&sub=implec&action=delall\">";
		foreach ($_POST as $cle => $val) {
				if (substr($cle, 0, 7) == "datedel") {
					print sprintf("<input type='hidden' name='%s' value='%s' />",
						htmlentities($cle, ENT_QUOTES, $charset),
						htmlentities($val, ENT_QUOTES, $charset));

					// la confirmation delete nous montre les 5 premieres personnes pour chaque date choisie
					print htmlentities($val, ENT_QUOTES, $charset);
					$desc_empr = desc_table("empr");
					print "<table border='2'>";
					print " <tr>";
					for($i=0;$i<4;$i++) {
						print " <th><strong>".$desc_empr[$i][0]."</strong></th>";
					}
					print " </tr>";

					$querry = "Select id_empr,empr_cb,empr_nom,empr_prenom From empr where empr_date_expiration = '$val';";
					$res = pmb_mysql_query($querry) or die($msg["select_echoue"]."<p>".$querry);
					$nbr1 = pmb_mysql_num_fields($res);
					$nbr2 = pmb_mysql_num_rows($res);
					if($nbr2 > 5) $nbr2 = 5;        // max de 5 reponses par date
					for($j = 0;$j < $nbr2; $j++) {
						$row = pmb_mysql_fetch_row($res);
						print " <tr>";
						for ($i = 0; $i < $nbr1; $i++) {
							print " <td>$row[$i]</td>";
						}
						print " </tr>";
					}
					print "</table>";

				}
		}
		// Le Precedent=1 c'est pour eviter des erreurs en apuyant sur precedent, cause: <form class='form-$current_module' name='form1' ENCTYPE=\"multipart/form-data\"
		print "
		<div class='row'>
				<table class='table-but'><tr>
				<td class='td-lbut'>
					<a href='./admin.php?categ=empr&sub=implec&action=FichierOK&Precedent=1' title='".$msg[654]."'>
					<img name='gg.gif' src='".get_url_icon('gg.gif')."' width='38' height='26'/>
					</a>
				</td>
				<td class='td-rbut'>
					<input type='submit' class='bouton' name='Effacer' value='".$msg["del_tout"]."' />
				</td>
				</tr></table>
		</div>
		</form>";
		break;

	case 'delall':
		foreach ($_POST as $cle => $val) {
			$cmpt = 0;
			//print $cle.", ". $val;
			if (substr($cle, 0, 7) == "datedel") {
				$querry = "select id_empr,empr_cb,empr_nom,empr_prenom From empr where empr_date_expiration = '".addslashes($val)."';";
				$res = pmb_mysql_query($querry) or die($msg["sqlselect_errdel1"]."<br />".$msg["sqlselect_errdel2"].$querry);
				while($row = pmb_mysql_fetch_row($res)) {
					$querry1 = "select * From pret where pret_idempr = ".$row[0].";";
					$result3 = pmb_mysql_query($querry1);
					$row2 = pmb_mysql_fetch_row($result3);
					if ($row2[0] == "") {
						emprunteur::del_empr($row[0]);
					} else {
						$cmpt++;
						if($cmpt == 1) {
							$desc_empr = desc_table("empr");
							print htmlentities($val, ENT_QUOTES, $charset)."<br />";
							print '<span style="color:#FF0000"><strong>'.$msg["personnes_nodel"]."<p>";
							print '</strong></span>';
							print "<table border='2'>";
							print " <tr>";
							foreach($desc_empr as $empr) {
								print " <td>".$empr[0]."</td>";
							}
							print " </tr>";
						}
						$nbr1 = pmb_mysql_num_fields($res);
						print " <tr>";
						for ($i = 0; $i < $nbr1; $i++)
							print " <td>$row[$i]</td>";
						print " </tr>";
					}

				}
				// a cause des prets qui peuvent etre en cours (et qui ne sont pas deletes)
				// pmb_mysql_num_rows($res) - $cmpt($cmpt: sont ceux qui n'ont pas ete delete)
				print "</table>";
				print $msg["personnes_delete"].(pmb_mysql_num_rows($res) - $cmpt)."<p>";
			}
		}
		print "<a href='./admin.php?categ=empr&sub=implec' title='".$msg[654]."'><img name='gg.gif' src='".get_url_icon('gg.gif')."' width='38' height='26'></a>";

		break;

	default:
		show_import_choix_fichier($from_ldap);
		break;
}

