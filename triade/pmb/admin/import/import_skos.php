<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_skos.php,v 1.3 2019/07/12 10:25:27 btafforeau Exp $

// définition du minimum nécéssaire 
$base_path="../..";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "";    
require_once ("$base_path/includes/init.inc.php");  


// les requis pour import_skos.php ou ses sous modules
require_once ("$class_path/rdf/rdf.class.php");

$form_download_files="
	<form class='form-$current_module' METHOD='post' ACTION=\"import_skos.php\"  ENCTYPE='multipart/form-data'> 
	<h3>".$msg["ontology_skos_admin_import_info"]."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne_suite'>
				<!-- localisation -->
				<label class='etiquette' for='userfile'>".$msg["ontology_skos_admin_import_choose_files"]."</label>
				<input type='file' name='userfile[]' class='saisie-80em'  multiple=\"multiple\"/><br/>
			</div>
		</div>
		<div class='row'></div>
	</div>	
	<INPUT TYPE=\"submit\" class='bouton' name=\"upload\" value=\"".$msg[502]."\">
	<INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"import\">
	<INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import_skos\">
	<INPUT NAME=\"action\" TYPE=\"hidden\" value=\"beforeupload\">
	</form>
";
		
switch ($action) {
	case 'beforeupload':
		if(is_array($_FILES['userfile']['tmp_name']) && count($_FILES['userfile']['tmp_name']) && trim($_FILES['userfile']['tmp_name'][0])){
			$files=array();
			foreach ( $_FILES['userfile']['tmp_name'] as $key => $tmp_file ) {
       			$to_file = $base_path.'/temp/'.basename($tmp_file);
       			$from_file = $_FILES['userfile']['name'][$key];
       			if ($to_file=="") {
				    printf ($msg[503],$from_file); /* wrong permissions to copy the file %s ... Contact your admin... */
				    break;
				}
				if (!@move_uploaded_file($tmp_file,$to_file)) {
				    printf ($msg[504],$from_file); /* Fail to copy %s, Contact your admin... */
				    break;
				}
				$files[]=array("name"=>$from_file,"location"=>$to_file);
			}
			echo "<br/><br/><br/>".$msg["ontology_skos_admin_import_beforeupload_files"]."<br/>"; /* File transfered, Loading is about to go on */
			print "<form class='form-$current_module' METHOD=\"post\"  NAME=\"afterupload\" ACTION=\"import_skos.php\">";
			print "<INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"import\">";
			print "<INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import_skos\">";
			print "<INPUT NAME=\"action\" TYPE=\"hidden\" value=\"afterupload\">";
			print "<INPUT NAME=\"files_post\" TYPE=\"hidden\" value=\"".urlencode(serialize($files))."\">";
			print "<INPUT NAME=\"count_files\" TYPE=\"hidden\" value=\"".count($files)."\">";
			print "</form>";
			print "<SCRIPT>setTimeout(\"document.afterupload.submit()\",2000);</SCRIPT>";
			break;
		}
		//Si rien n'a été sélectionné on réafiche la page de chargement
		print $form_download_files;
	case 'afterupload':
		$files=unserialize(urldecode($files_post));
		if($logs_serialize){
			$logs_imports=unserialize(urldecode($logs_serialize));
		}else{
			$logs_imports=array();
		}
		$file=array_shift($files);
		
		$p = new sparql();
		$res=$p->load_file($file["location"]);
		if(!$res){
			$logs_imports[]=array("result" => "ko", "msg" => $msg["ontology_skos_admin_import_error"].$file["name"]);
		}else{
			$logs_imports[]=array("result" => "ok", "msg" => $res.$msg["ontology_skos_admin_import_ok"].$file["name"]);
		}
		unlink($file["location"]);
		
		if(count($files)){
			echo "<br/><br/><br/>";
			printf ($msg["ontology_skos_admin_import_afterupload_files"], count($files), $count_files);
			echo "<br/>";
			print "<form class='form-$current_module' METHOD=\"post\"  NAME=\"afterupload\" ACTION=\"import_skos.php\">";
			print "<INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"import\">";
			print "<INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import_skos\">";
			print "<INPUT NAME=\"action\" TYPE=\"hidden\" value=\"afterupload\">";
			print "<INPUT NAME=\"files_post\" TYPE=\"hidden\" value=\"".urlencode(serialize($files))."\">";
			print "<INPUT NAME=\"logs_serialize\" TYPE=\"hidden\" value=\"".urlencode(serialize($logs_imports))."\">";
			print "<INPUT NAME=\"count_files\" TYPE=\"hidden\" value=\"".$count_files."\">";
			print "</form>";
			print "<SCRIPT>setTimeout(\"document.afterupload.submit()\",2000);</SCRIPT>";
			break;
		}else{
			print "<form class='form-$current_module' METHOD=\"post\"  NAME=\"load\" ACTION=\"import_skos.php\">";
			print "<INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"import\">";
			print "<INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import_skos\">";
			print "<INPUT NAME=\"action\" TYPE=\"hidden\" value=\"load\">";
			print "<INPUT NAME=\"files_post\" TYPE=\"hidden\" value=\"".urlencode(serialize($files))."\">";
			print "<INPUT NAME=\"logs_serialize\" TYPE=\"hidden\" value=\"".urlencode(serialize($logs_imports))."\">";
			print "<INPUT NAME=\"count_files\" TYPE=\"hidden\" value=\"".$count_files."\">";
			print "</form>";
			print "<SCRIPT>document.load.submit();</SCRIPT>";
			break;
		}
		break;
	case "load":
		$logs_imports=unserialize(urldecode($logs_serialize));
		echo "<br/><br/><br/>";
		printf ($msg["ontology_skos_admin_import_load_files"],$count_files);
		echo "<br/>";
		foreach ( $logs_imports as $key => $value ) {
       		if($value["result"] == "ok"){
       			echo "<br/><div>".htmlentities($value["msg"],ENT_QUOTES,$charset)."</div>";
       			unset($logs_imports[$key]);
       		}
		}
		if(count($logs_imports)){
			echo "<br/><br/><br/>";
			foreach ( $logs_imports as $value ) {
       			echo "<div class='erreur'>".htmlentities($value["msg"],ENT_QUOTES,$charset)."</div><br/>";
			}
		}
		
		break;
	default:
		print $form_download_files;
		break;
}
?>