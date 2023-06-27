<?php
// +--------------------------------------------------------------------------+
// | PMB est sous licence GPL, la réutilisation du code est cadrée            |
// +--------------------------------------------------------------------------+
// $Id: download.php,v 1.7 2018-06-29 13:19:03 dgoron Exp $

//Impression

$base_path = ".";
$base_auth = "CATALOGAGE_AUTH|CIRCULATION_AUTH";
$base_title = "\$msg[download_title]";
$base_nobody=1;
$base_noheader=1;


require($base_path."/includes/init.inc.php");

require_once($class_path."/sort.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/acces.class.php");
require_once($class_path."/explnum.class.php");

if (file_exists($include_path.'/print/print_options_subst.xml')){
	$xml_print = new XMLlist($include_path.'/print/print_options_subst.xml');
} else {
	$xml_print = new XMLlist($include_path.'/print/print_options.xml');
}
$xml_print->analyser();
$download_options = $xml_print->table;

if ($action_download=="download_prepare") {
	header ("Content-Type: text/html; charset=$charset");
	print $std_header;
	print "<h3>".$msg["download_options"]."</h3>\n";
	print "<form name='download_options' action='download.php?action_download=download' method='post'>";
	print "<b>".$msg["download_size"]."</b>";
	print"
	<blockquote>
		<input type='radio' name='pager' id='selected_elements' value='2' ".($download_options['selected_elements'] ? ' checked ' : '')."/><label for='selected_elements'>&nbsp;".$msg["download_size_selected_elements"]."</label><br />
		<input type='radio' name='pager' id='current_page' value='1' ".($download_options['current_page'] ? ' checked ' : '')."/><label for='current_page'>&nbsp;".$msg["download_size_current_page"]."</label><br />
		<input type='radio' name='pager' id='all' value='0' ".($download_options['all'] ? ' checked ' : '')."/><label for='all'>&nbsp;".$msg["download_size_all"]."</label>
	</blockquote>";

	$sort_info = !empty($sort_id) ? '<input type="hidden" name="sort_id" value="'.$sort_id.'">' : '';
	
	print"
		<b>".$msg["download_output_title"]."</b>
		<blockquote>
			<input type='radio' name='output_docnum' id='output_docnum_singly' value='singly' ".($download_options['output_docnum_singly'] ? ' checked ' : '')."/><label for='output_docnum_singly'>&nbsp;".$msg["download_output_singly"]."</label><br />
			<input type='radio' name='output_docnum' id='output_docnum_zip' value='zip' ".($download_options['output_docnum_zip'] ? ' checked ' : '')."/><label for='output_docnum_zip'>&nbsp;".$msg["download_output_zip"]."</label><br />
		</blockquote>
		<input type='hidden' name='current_download' value='$current_download'/>
		<input type='hidden' name='notice_id' value='".(isset($notice_id) ? $notice_id : 0)."'/>".$sort_info."
		<span style='text-align:center'><input type='submit' value='".$msg["download_download"]."' class='bouton'/>&nbsp;<input type='button' value='".$msg["download_cancel"]."' class='bouton' onClick='self.close();'/></span>";
	print "</form></body></html>";
}

if ($action_download=="download") {
	if ($_SESSION["session_history"][$current_download]) {
		$_SESSION["DOWNLOAD"]=$_SESSION["session_history"][$current_download]["NOTI"];
		$_SESSION["DOWNLOAD"]["pager"]=$pager;
		$_SESSION["DOWNLOAD"]["output_docnum"]=$output_docnum;
		if ($sort_id) $_SESSION["DOWNLOAD"]["sort_id"]=$sort_id;
		else $_SESSION["DOWNLOAD"]["sort_id"]=$_SESSION['tri'];
		echo "<script>document.location='./download.php';</script>";
	} else {
		echo "<script>alert(\"".$msg["download_no_search"]."\"); self.close();</script>";
	}
}

if (($action_download=="")&&($_SESSION["DOWNLOAD"])) {
	$environement=$_SESSION["DOWNLOAD"];
	$limit='';
	if ($environement["TEXT_QUERY"]) {
		if (count($environement["TEXT_LIST_QUERY"])) {
			foreach($environement["TEXT_LIST_QUERY"] as $query) {
				@pmb_mysql_query($query);
			}
		}
		$requete=preg_replace('/limit\s+[0-9]\s*,*\s*[0-9]*\s*$/','',$environement["TEXT_QUERY"],1);
	} else {
		switch ($environement["SEARCH_TYPE"]) {
			case "extended":
				$sh=new search();
				$table=$sh->make_search();
				$requete="select notice_id from $table";
				break;
			case "cart":
				$requete="select object_id as notice_id from caddie_content join notices where caddie_id=".$idcaddie." and object_id=notice_id order by index_sew";
				break;
		}
	}
	if ($environement["pager"]) {
		$start= $nb_per_page_search*($environement["PAGE"]-1);
		$nbLimit = $nb_per_page_search;
		$limit="limit ".$start.",$nb_per_page_search";
	}else{
		$start = 0;
		$nbLimit = -1;
	}
	
	if ($environement["sort_id"]) {
		$sort = new sort('notices','base');
		$requete = $sort->appliquer_tri($environement["sort_id"] , $requete, "notice_id", $start, $nbLimit);
	}else{
		$requete.=" $limit";
	}
	$resultat=@pmb_mysql_query($requete);
	while (($r=pmb_mysql_fetch_object($resultat))) {
		$query = "SELECT explnum_id from explnum where explnum_notice=".$r->notice_id;
		$query .= " union ";
		$query .= " select explnum_id from explnum ,bulletins where explnum_bulletin=bulletin_id and num_notice=".$r->notice_id;
		$result = pmb_mysql_query($query,$dbh);
		if ($result) {
			while($row = pmb_mysql_fetch_object($result)){
				$explnum_id=$row->explnum_id;
					
				$res = pmb_mysql_query("SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_data, length(explnum_data) as taille,explnum_path, concat(repertoire_path,explnum_path,explnum_nomfichier) as path, repertoire_id FROM explnum left join upload_repertoire on repertoire_id=explnum_repertoire WHERE explnum_id = '$explnum_id' ", $dbh);
				$ligne = pmb_mysql_fetch_object($res);
					
				$id_for_rigths = $ligne->explnum_notice;
				if($ligne->explnum_bulletin != 0){
					//si bulletin, les droits sont rattachés à la notice du bulletin, à défaut du pério...
					$req = "select bulletin_notice,num_notice from bulletins where bulletin_id =".$ligne->explnum_bulletin;
					$res = pmb_mysql_query($req,$dbh);
					if(pmb_mysql_num_rows($res)){
						$r = pmb_mysql_fetch_object($res);
						$id_for_rigths = $r->num_notice;
						if(!$id_for_rigths){
							$id_for_rigths = $r->bulletin_notice;
						}
					}
				}
		
				//droits d'acces utilisateur/notice
				if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
					require_once("$class_path/acces.class.php");
					$ac= new acces();
					$dom_1= $ac->setDomain(1);
					$rights = $dom_1->getRights($PMBuserid,$id_for_rigths);
				}
		
				if( $rights & 4 || (is_null($dom_1))){
					if (($ligne->explnum_data)||($ligne->explnum_path)) {
						$explnum_list[] = $ligne->explnum_id;
					}
				}
			}
		}
	}
	
	if (count($explnum_list)) {
		switch($environement['output_docnum']) {
			case 'singly':
				foreach ($explnum_list as $explnum_id) {
					print "<script type='text/javascript'>
						window.open('".$pmb_url_base."doc_num_data.php?explnum_id=".$explnum_id."&force_download=1','_blank','');
						</script>";
				}
				break;
			case 'zip':
				$zip = new ZipArchive();
				$filename=microtime();
				$filename=str_replace(".","",$filename);
				$filename=str_replace(" ","",$filename);
				$filename="temp/pmb_".$filename.".zip";
				$res = $zip->open($filename, ZipArchive::CREATE);
				if ($res) {
					foreach ($explnum_list as $explnum_id) {
						$explnum = new explnum($explnum_id);
						$zip->addFromString(reg_diacrit($explnum->get_file_name()),$explnum->get_file_content());
					}
					$zip->close();

					header("Content-disposition: attachment; filename=\"".basename($filename)."\"");
					header("Content-Type: application/force-download");
					header("Content-Transfer-Encoding: application/zip");
					header("Content-Length: ".filesize($filename));
					header("Pragma: no-cache");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
					header("Expires: 0");
					
					$fp = fopen($filename, 'rb');
					fpassthru($fp);
					fclose($fp) ;
					
					@unlink($filename);
				}
				break;
		}
	}
}
?>