<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_func.inc.php,v 1.145.2.4.2.5 2025/03/04 15:50:02 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $current_module;
global $deflt_docs_location, $book_lender_id, $book_statut_id, $PMBuserid, $deflt_import_lenders;

require_once($class_path."/serials.class.php");
require_once($class_path."/category_auto.class.php");
require_once($class_path."/origin.class.php");
require_once($class_path."/notice_doublon.class.php");
require_once($class_path."/notice_relations.class.php");
require_once($class_path.'/audit.class.php');

//On récupère le propriétaire par défaut pour préremplir le sélecteur
if(! $deflt_import_lenders) {
	$deflt_import_lenders = user::get_param($PMBuserid, 'deflt_import_lenders');
}
if(! $book_lender_id) {
	$book_lender_id = $deflt_import_lenders;
}

// templates
$tpl_beforeupload_expl = "
                <form class='form-$current_module' ENCTYPE=\"multipart/form-data\" METHOD=\"post\" ACTION=\"iimport_expl.php\">
                <h3>".$msg['import_expl_form_titre']."</h3>
                <div class='form-contenu'>
                    <div class='row'>
                        <div class='colonne2'>
                            <label class='etiquette' >$msg[564]</label><br />
                            <INPUT TYPE='radio' NAME='isbn_mandatory' id='io1' VALUE='1' CLASS='radio' /><label for='io1'> $msg[40] </label>
                            <INPUT TYPE='radio' NAME='isbn_mandatory' id='io0' VALUE='0' CLASS='radio' checked='checked' /><label for='io0'> $msg[39] </label>
                        </div>
                        <div class='colonne-suite'>
                            <label class='etiquette' >$msg[568]</label><br />
                            <INPUT TYPE='radio' NAME='isbn_dedoublonnage' id='di1' VALUE='1' CLASS='radio' checked='checked' /><label for='di1'> $msg[40] </label>
                            <INPUT TYPE='radio' NAME='isbn_dedoublonnage' id='di0' VALUE='0' CLASS='radio' /><label for='di0'> $msg[39] </label>
                            <input type='checkbox' name='isbn_only' id='ionly' value='1' checked='checked' /><label for='ionly'> ".$msg["ignore_issn"]." </label>
                       </div>
                    </div>
                    <div class='row'>&nbsp;</div>
                    <div class='row'>
                        <div class='colonne2'>
                    		<label class='etiquette' for='statutnot'>$msg[import_statutnot]</label>
                    		<div>
                    		".gen_liste_multiple ("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "statutnot", "", 1, "", "","","",0)."
                    		</div>
                    	</div>
                    	<div class='colonne-suite'>
                    		<label class='etiquette' >".$msg['import_genere_liens']."</label><br />
                    		<INPUT TYPE='radio' NAME='link_generate' id='link1' VALUE=' 1' CLASS='radio' onclick='param_links_display();' /><label for='link1'> $msg[40] </label>
                            <INPUT TYPE='radio' NAME='link_generate' id='link0' VALUE='0' CLASS='radio' checked='checked' onclick='param_links_display();' /><label for='link0'> $msg[39] </label>
                             <span id='list_param_links' style='display: none;'>
	                            	<div style='clear: both; margin-left: 50%;'>
	                            		<label class='etiquette' for='notice_replace_links'>".$msg['notice_replace_links_option_keep_title']."</label>
                            			<br /><input type='radio' name='notice_replace_links' value='0' ".($deflt_notice_replace_links==0?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_all']."
										<br /><input type='radio' name='notice_replace_links' value='1' ".($deflt_notice_replace_links==1?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replacing']."
										<br /><input type='radio' name='notice_replace_links' value='2' ".($deflt_notice_replace_links==2?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replaced']."
									</div>
                            </span>
                    	</div>
                    </div>
                    <div class='row'>&nbsp;</div>
                   	<div class='row'>
                   		<div class='colonne2'>
                   			<label class='etiquette' for='import_force_notice_is_new'>".$msg['import_force_notice_is_new']."</label>
                    		<div>
                    			<input type='radio' name='import_force_notice_is_new' id='import_force_notice_is_new' value='0' checked='checked'> ".$msg['39']." <input type='radio' name='import_force_notice_is_new' id='import_force_notice_is_new' value='1'> ".$msg['40']."
                    		</div>
                   		</div>
                   		<div class='colonne-suite'>
							<label class='etiquette' for='authorities_notices'>".htmlentities($msg['import_with_authorities_notices'],ENT_QUOTES,$charset)."</label><br />
							<input type='radio' name='authorities_notices' id='authorities_notices1' value='1' class='radio' /><label for='authorities_notices1'>".htmlentities($msg[40],ENT_QUOTES,$charset)."</label>
							<input type='radio' name='authorities_notices' id='authorities_notices0' value='0' class='radio' checked='checked'/><label for='authorities_notices0'>".htmlentities($msg[39],ENT_QUOTES,$charset)."</label>
                    	</div>
                   	</div>
                   	<div class='row'>&nbsp;</div>
                   		<div class='row'>
                   			<div class='colonne2'>
	                   			<label class='etiquette' for='import_notice_existing_replace'>".$msg['import_notice_existing_replace']."</label>
	                    		<div>
	                    			<input type='radio' name='import_notice_existing_replace' id='import_notice_existing_replace0' value='0' checked='checked' onclick='param_existing_replace_display();'> <label for='import_notice_existing_replace0'>".$msg['39']."</label>
	                    			<input type='radio' name='import_notice_existing_replace' id='import_notice_existing_replace1' value='1' onclick='param_existing_replace_display();'> <label for='import_notice_existing_replace1'>".$msg['40']."</label>
	                    		</div>
	                    		<div id='import_notice_existing_replace_message' class='warning' style='display: none;'>
	                    			".htmlentities($msg['import_notice_existing_replace_message'], ENT_QUOTES, $charset)."
	                    		</div>
	                   		</div>
                   			<div class='colonne-suite'>
                   				<label class='etiquette' for='authorities_default_origin'>".htmlentities($msg['import_authorities_origin_default_value'],ENT_QUOTES,$charset)."</label><br />
								".origin::gen_combo_box("authorities")."
                   			</div>
                   		</div>
                    <div class='row'><hr /></div>
					<div class='row'>
                        <label class='etiquette' for='preteur statut'>$msg[560]</label>
                    </div>
                    <div class='row'>".
                        lender::gen_combo_box($book_lender_id)."&nbsp;&nbsp;".
                        docs_statut::gen_combo_box($book_statut_id)."
                    </div>
                    <div class='row'>
                        <label class='etiquette' for='localisation'>$msg[import_localisation]</label>
                    </div>
                    <div class='row'>".
                        docs_location::gen_combo_box($deflt_docs_location)."
                    </div>
                    <div class='row'><hr /></div>
                    <div class='row'>
                        <label class='etiquette' for='cote_obligatoire'>$msg[566]</label>
                    </div>
                    <div class='row'>
                        <INPUT TYPE='radio' NAME='cote_mandatory' id='cm1' VALUE='1' CLASS='radio' /><label for='cm1'> $msg[40] </label>
                        <INPUT TYPE='radio' NAME='cote_mandatory' id='cm0' VALUE='0' CLASS='radio' checked='checked' /><label for='cm0'> $msg[39] </label>
                    </div>
                    <div class='row'><hr /></div>
                    <div class='row'>
                        <label class='etiquette'>$msg[17]</label>
                    </div>
                    <div class='row'>
                        <INPUT TYPE='radio' NAME='tdoc_codage' id='td1' VALUE='1' CLASS='radio' /><label for='td1'> ".$msg["import_expl_codage_proprio"]."</label>
                        <INPUT TYPE='radio' NAME='tdoc_codage' id='td0' VALUE='0' CLASS='radio' checked='checked' /><label for='td0'> ".$msg["import_expl_codage_generique"]."</label>
                    </div>
                    <div class='row'>
                        <label class='etiquette'>$msg[24]</label>
                    </div>
                    <div class='row'>
                        <INPUT TYPE='radio' NAME='statisdoc_codage' id='sd1' VALUE='1' CLASS='radio' /><label for='sd1'> ".$msg["import_expl_codage_proprio"]."</label>
                        <INPUT TYPE='radio' NAME='statisdoc_codage' id='sd0' VALUE='0' CLASS='radio' checked='checked' /><label for='sd0'> ".$msg["import_expl_codage_generique"]."</label>
                    </div>
                    <div class='row'>
                        <label class='etiquette'>$msg[19]</label>
                    </div>
                    <div class='row'>
                        <INPUT TYPE='radio' NAME='sdoc_codage' id='sdc1' VALUE='1' CLASS='radio' /><label for='sdc1'> ".$msg["import_expl_codage_proprio"]."</label>
                        <INPUT TYPE='radio' NAME='sdoc_codage' id='sdc0' VALUE='0' CLASS='radio' checked='checked' /><label for='sdc0'> ".$msg["import_expl_codage_generique"]."</label>
                    </div>
                    <div class='row'><hr /></div>

                    <div class='row'>
                        <label class='etiquette' for='txt_suite'>$msg[501]</label>
                        </div>
                    <div class='row'>
                        <INPUT NAME='userfile' class='saisie-80em' TYPE='file' size='60' />
                        <INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"import\" />
                        <INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import_expl\" />
                        <INPUT NAME=\"action\" TYPE=\"hidden\" value=\"afterupload\" />
                    </div>
                    </div>
                    <hr />
                    <div class='row'>
                        <input type='checkbox' name='import_without_index' id='import_without_index' value='1' /><label for='import_without_index' class='etiquette' >".htmlentities($msg['import_without_index'],ENT_QUOTES,$charset)."</label>
                    </div>
				".import_expl::get_advanced_form()."
                <INPUT TYPE='SUBMIT' class='bouton' NAME='upload' VALUE='".$msg[502]."' />
                </FORM>
                <script type='text/javascript'>
					function param_links_display(){
						if(document.getElementById('link1').checked){
							document.getElementById('list_param_links').style.display='';
						} else {
							document.getElementById('list_param_links').style.display='none';
						}
					}
                	function param_existing_replace_display(){
						if(document.getElementById('import_notice_existing_replace1').checked){
							document.getElementById('import_notice_existing_replace_message').style.display='';
						} else {
							document.getElementById('import_notice_existing_replace_message').style.display='none';
						}
					}
				</script>";

$tpl_beforeupload_notices = "
                    <form class='form-$current_module' ENCTYPE='multipart/form-data' METHOD='post' ACTION='iimport_expl.php' />
                    <h3>".$msg['import_noti_form_titre']."</h3>
                    <div class='form-contenu'>
                    <div class='row'>
                        <div class='colonne2'>
                            <label class='etiquette' for='isbn_obligatoire'>$msg[564]</label><br />
                            <INPUT TYPE='radio' NAME='isbn_mandatory' id='io1' VALUE='1' CLASS='radio' /><label for='io1'> $msg[40] </label>
                            <INPUT TYPE='radio' NAME='isbn_mandatory' id='io0' VALUE='0' CLASS='radio' checked='checked' /><label for='io0'> $msg[39] </label>
                            </div>
                        <div class='colonne-suite'>
                            <label class='etiquette' for='isbn_dedoublonnage'>$msg[568]</label><br />
                            <INPUT TYPE='radio' NAME='isbn_dedoublonnage' id='di1' VALUE='1' CLASS='radio' checked='checked' /><label for='di1'> $msg[40] </label>
                            <INPUT TYPE='radio' NAME='isbn_dedoublonnage' id='di0' VALUE='0' CLASS='radio' /><label for='di0'> $msg[39] </label>
                            <input type='checkbox' name='isbn_only' id='ionly' value='1' /><label for='ionly'> ".$msg["ignore_issn"]." </label>
                            </div>
                        </div>
                        <div class='row'>&nbsp;</div>
						<div class='row'>
	                        <div class='colonne2'>
	                    		<label class='etiquette' for='statutnot'>$msg[import_statutnot]</label>
	                    		<div>
	                    		".gen_liste_multiple ("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "statutnot", "", 1, "", "","","",0)."
	                    		</div>
	                    	</div>
	                    	<div class='colonne-suite'>
	                    		<label class='etiquette' for='generer_lien'>".$msg['import_genere_liens']."</label><br />
	                    		<INPUT TYPE='radio' NAME='link_generate' id='link1' VALUE='1' CLASS='radio' onclick='param_links_display();' /><label for='link1'> $msg[40] </label>
	                            <INPUT TYPE='radio' NAME='link_generate' id='link0' VALUE='0' CLASS='radio' checked='checked' onclick='param_links_display();' /><label for='link0'> $msg[39] </label>
	                            <span id='list_param_links' style='display: none;'>
	                            	<div style='clear: both; margin-left: 50%;'>
	                            		<label class='etiquette' for='notice_replace_links'>".$msg['notice_replace_links_option_keep_title']."</label>
	                            		<br /><input type='radio' name='notice_replace_links' value='0' ".($deflt_notice_replace_links==0?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_all']."
										<br /><input type='radio' name='notice_replace_links' value='1' ".($deflt_notice_replace_links==1?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replacing']."
										<br /><input type='radio' name='notice_replace_links' value='2' ".($deflt_notice_replace_links==2?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replaced']."
									</div>
	                            </span>
	                    	</div>
                   		</div>
                   		<div class='row'>&nbsp;</div>
                   		<div class='row'>
                   			<div class='colonne2'>
                   				<label class='etiquette' for='import_force_notice_is_new'>".$msg['import_force_notice_is_new']."</label>
	                    		<div>
	                    			<input type='radio' name='import_force_notice_is_new' id='import_force_notice_is_new' value='0' checked='checked'> ".$msg['39']." <input type='radio' name='import_force_notice_is_new' id='import_force_notice_is_new' value='1'> ".$msg['40']."
	                    		</div>
                   			</div>
                   			<div class='colonne-suite'>
								<label class='etiquette' for='authorities_notices'>".htmlentities($msg['import_with_authorities_notices'],ENT_QUOTES,$charset)."</label><br />
								<input type='radio' name='authorities_notices' id='authorities_notices1' value='1' class='radio' /><label for='authorities_notices1'>".htmlentities($msg[40],ENT_QUOTES,$charset)."</label>
								<input type='radio' name='authorities_notices' id='authorities_notices0' value='0' class='radio' checked='checked'/><label for='authorities_notices0'>".htmlentities($msg[39],ENT_QUOTES,$charset)."</label>
                   			</div>
                   		</div>
                   		<div class='row'>&nbsp;</div>
                   		<div class='row'>
                   			<div class='colonne2'>
	                   			<label class='etiquette' for='import_notice_existing_replace'>".$msg['import_notice_existing_replace']."</label>
	                    		<div>
	                    			<input type='radio' name='import_notice_existing_replace' id='import_notice_existing_replace0' value='0' checked='checked' onclick='param_existing_replace_display();'> <label for='import_notice_existing_replace0'>".$msg['39']."</label>
	                    			<input type='radio' name='import_notice_existing_replace' id='import_notice_existing_replace1' value='1' onclick='param_existing_replace_display();'> <label for='import_notice_existing_replace1'>".$msg['40']."</label>
	                    		</div>
	                    		<div id='import_notice_existing_replace_message' class='warning' style='display: none;'>
	                    			".htmlentities($msg['import_notice_existing_replace_message'], ENT_QUOTES, $charset)."
	                    		</div>
	                   		</div>
                   			<div class='colonne-suite'>
                   				<label class='etiquette' for='authorities_default_origin'>".htmlentities($msg['import_authorities_origin_default_value'],ENT_QUOTES,$charset)."</label><br />
								".origin::gen_combo_box("authorities","authorities_default_origin")."
                   			</div>
                   		</div>
	                    <div class='row'>&nbsp;</div>
                        <div class='row'>
                            <label class='etiquette' for='txt_suite'>$msg[501]</label>
                            </div>
                        <div class='row'>
                            <INPUT NAME='userfile' class='saisie-80em' TYPE='file' size='60' />
                            <INPUT NAME='categ' TYPE='hidden' value='import' />
                            <INPUT NAME='sub' TYPE='hidden' value='import' />
                            <INPUT NAME='action' TYPE='hidden' value='afterupload' />
                            </div>
                        </div>
                        <hr />
                        <div class='row'>
                            <input type='checkbox' name='import_without_index' id='import_without_index' value='1' /><label for='import_without_index' class='etiquette' >".htmlentities($msg['import_without_index'],ENT_QUOTES,$charset)."</label>
                        </div>
                    ".import_records::get_advanced_form()."
                    <INPUT TYPE='SUBMIT' class='bouton' NAME='upload' VALUE='".$msg[502]."' />
                    </FORM>
                    <script type='text/javascript'>
						function param_links_display(){
							if(document.getElementById('link1').checked){
								document.getElementById('list_param_links').style.display='';
							} else {
								document.getElementById('list_param_links').style.display='none';
							}
						}
                    	function param_existing_replace_display(){
							if(document.getElementById('import_notice_existing_replace1').checked){
								document.getElementById('import_notice_existing_replace_message').style.display='';
							} else {
								document.getElementById('import_notice_existing_replace_message').style.display='none';
							}
						}
					</script>";

// PRELOAD
function loadfile_in_table () {
	global $msg ;
	global $sub, $book_lender_name ;
	global $noticenumber, $filename, $from_file, $pb_fini, $recharge ;
	global $pmb_import_limit_read_file ;

	if ($noticenumber=="") $noticenumber=0;

	if (!file_exists($filename)) {
		printf ($msg[506],$from_file); /* The file %s doesn't exist... */
		return;
	}

	if (filesize($filename)==0) {
		printf ($msg[507],$from_file); /* The file % is empty, it's going to be deleted */
		unlink ($filename);
		return;
	}

	$handle = fopen ($filename, "rb");
	if (!$handle) {
		printf ($msg[508],$from_file); /* Unable to open the file %s ... */
		return;
	}

	if ($sub=="import_expl") {
		printf ($msg[509], $from_file) ;
		printf ($msg[511], "\"".$book_lender_name."\"") ;
	}

	$file_size=filesize ($filename);

	$contents = fread ($handle, $file_size);
	fclose ($handle);

	/* First load of the shot, let's empty the import table */
	if ($recharge=="") {
		$sql = "DELETE FROM import_marc WHERE origine='".addslashes(SESSid)."' ";
		$sql_result = pmb_mysql_query($sql) or die ("Couldn't delete import table !");
		$sql = "DELETE FROM error_log WHERE error_origin LIKE '%_".addslashes(SESSid).".%' ";
		$sql_result = pmb_mysql_query($sql) or die ("Couldn't delete error_log table !");
	}

	/* The whole file is in $contents, let's read it */
	$str_lu="";
	$j=0;
	$i=0;
	$pb_fini="";
	$txt="";
	$ascii_cursor_left = chr(0x1d);
	$contents_len = strlen($contents);
	while ( ($i<=$contents_len) && ($pb_fini=="") ) {
		$car_lu=substr($contents,$i,1) ;
		$i++;
		if ($i<=$contents_len) {
			if ($car_lu != $ascii_cursor_left) {
				/* the read car isn't the end of the notice */
				$str_lu = $str_lu.$car_lu;
			} else {
				/* the read car is the end of a notice */
				$str_lu = $str_lu.$car_lu;
				$j++;
				$sql = "INSERT INTO import_marc (notice,origine) VALUES('".addslashes($str_lu)."','".addslashes(SESSid)."')";
				pmb_mysql_query($sql)
					or die ("Couldn't insert record!");
				if ($j>=$pmb_import_limit_read_file && $i<$contents_len) {
					/* let's rewrite the file with the remaing string  */
					$handle = fopen ($filename, "wb");
					fwrite ($handle, substr($contents,$i, $file_size-$i));
					fclose ($handle);
					printf (" ".$msg[510], ($file_size-$i)) ;
					$pb_fini="NOTEOF";
				} else if ($j>=$pmb_import_limit_read_file && $i>=$contents_len){
					$pb_fini = "EOF";
				}
				$str_lu="";
			}
		} else { /* the wole file has been read */
			$pb_fini="EOF";
		}
	} /* end while red file */

	if ($pb_fini=="NOTEOF") $recharge="YES"; else $recharge="NO" ;
	if ($pb_fini=="EOF") { /* The file has been read, we can delete it */
		unlink ($filename);
	}
} // fin fonction de load


function recup_noticeunimarc($notice) {

	global $id_unimarc;
	global $doc_type		;
	global $hierarchic_level;
	global $bibliographic_level	;
	global $bibliographic_level_origine;
	global $hierarchic_level_origine;
	global $isbn			;
	global $issn_011		;
	global $prix			;
	global $prix_cd			;
	global $cb				;
	global $lang_code		;
	global $org_lang_code	;
	global $tit_200a		;
	global $tit_200c		;
	global $tit_200d		;
	global $tit_200e		;
	global $tit_200v		;
	global $serie_200		;
	global $editeur_lieu  	;
	global $editeur_adr  	;
	global $editeur_nom		;
	global $editeur_date	;
	global $editeur_pays	;
	global $editeur_pmbfields	;
	global $no_edition		;
	global $npages			;
	global $ill				;
	global $size			;
	global $accomp			;
	global $collection_225	;
	global $n_contenu		;
	global $n_resume		;
	global $n_gen			;
	global $EAN				;
	global $collection_410	;
	global $collection_411	;
	global $serie			;
	global $index_sujets	;
	global $dewey			;
	global $dewey_l			;
	global $tu_500			;
	global $tu_500_r 		;
	global $tu_500_s		;
	global $tu_500_j		;
	global $aut_700			;
	global $aut_701			;
	global $aut_702			;
	global $aut_710			;
	global $aut_711			;
	global $aut_712			;
	global $origine_notice	;
	global $lien, $eformat	;
	global $info_995		;
	global $info_996		;
	global $info_852		;
	global $analytique		;

	global $info_600_a, $info_600_j, $info_600_x, $info_600_y, $info_600_z ;
	global $info_601_a, $info_601_j, $info_601_x, $info_601_y, $info_601_z ;
	global $info_602_a, $info_602_j, $info_602_x, $info_602_y, $info_602_z ;
	global $info_605_a, $info_605_j, $info_605_x, $info_605_y, $info_605_z ;
	global $info_606_a, $info_606_j, $info_606_x, $info_606_y, $info_606_z ;
	global $info_607_a, $info_607_j, $info_607_x, $info_607_y, $info_607_z ;

	global $info_686;
	global $indicateur;
	global $link_generate;
	$id_import=array();
	$id_unimarc='';
	$indicateur=array();
	$doc_type		= "";
	$hierarchic_level	= '0';
	$bibliographic_level	= 'm';
	$isbn			= array();
	$issn_011		= array();
	$prix			= array();
	$prix_cd		= array();
	$cb				= "";
	$lang_code		= array();
	$org_lang_code	= array();
	$tit_200a		= array();
	$tit_200c		= array();
	$tit_200d		= array();
	$tit_200e		= array();
	$tit_200v		= array();
	$serie_200		= array();
	$editeur_lieu   = array();
	$editeur_adr    = array();
	$editeur_nom	= array();
	$editeur_date	= array();
	$editeur_pays	= array();
	$editeur_pmbfields	= array();
	$npages			= array();
	$no_edition		= array();
	$ill			= array();
	$size			= array();
	$accomp			= array();
	$collection_225	= array();
	$collection_410	= array();
	$collection_411	= array();
	$n_contenu		= array();
	$n_resume		= array();
	$n_gen			= array();
	$EAN			= array();
	$serie			= array();
	$index_sujets	= array();
	$dewey			= array();
	$dewey_l		= array();
	$tu_500			= array();
	$tu_500_r 		= array();
	$tu_500_s 		= array();
	$tu_500_j 		= array();
	$aut_700		= array();
	$aut_701		= array();
	$aut_702		= array();
	$aut_710		= array();
	$aut_711		= array();
	$aut_712		= array();
	$origine_notice	= array() ;
	$lien			= array();
	$eformat		= array();
	$info_995		= array();
	$info_852		= array();
	$analytique 	= array() ;

	$info_600_a = array();
	$info_600_j = array();
	$info_600_x = array();
	$info_600_y = array();
	$info_600_z  = array();

	$info_601_a = array();
	$info_601_j = array();
	$info_601_x = array();
	$info_601_y = array();
	$info_601_z  = array();

	$info_602_a = array();
	$info_602_j = array();
	$info_602_x = array();
	$info_602_y = array();
	$info_602_z  = array();

	$info_605_a = array();
	$info_605_j = array();
	$info_605_x = array();
	$info_605_y = array();
	$info_605_z  = array();

	$info_606_a = array();
	$info_606_j = array();
	$info_606_x = array();
	$info_606_y = array();
	$info_606_z  = array();

	$info_607_a = array();
	$info_607_j = array();
	$info_607_x = array();
	$info_607_y = array();
	$info_607_z  = array();

	$info_686 = array();
	$info_996 = array();

	$record = new iso2709_record($notice, AUTO_UPDATE);
	if(!$record->valid("import_notice")){
		// On ne traite pas les notices invalides
		/*echo "<pre>";
		print_r($record->inner_data);
		echo "</pre>";
		die();*/
		$num_notice=$record->get_subfield("001");
		$titr=$record->get_subfield_array("200", 'a');
		$requete="insert into error_log(error_origin,error_text) values('import_func_".addslashes(SESSid).".inc.php','".addslashes("La notice (numéro : ".$num_notice[0].", titre : ".$titr[0].") n'a pas été reprise. <BR/><span style='color:#FF0000'>Erreur(s):".implode("<BR/>",$record->errors)."</span>")."')";
		pmb_mysql_query($requete);
		return false;
	}
	$doc_type=$record->inner_guide['dt'];

	$bibliographic_level_origine=$record->inner_guide['bl'];
	$hierarchic_level_origine=$record->inner_guide['hl'];

	if($bibliographic_level_origine){
		switch ($bibliographic_level_origine) {
			case 'a':
				$hierarchic_level = '2';
				$bibliographic_level = 'a';
				break;
			case 'b':
				$hierarchic_level = '2';
				$bibliographic_level = 'b';
				break;
			case 's':
				if($hierarchic_level_origine <= '1'){
					$hierarchic_level = '1';
					$bibliographic_level = 's';
				} else {
					$hierarchic_level = '2';
					$bibliographic_level = 'b';
				}
				break;
			case 'm':
			case 'c':
			default :
				// suite à pb d'export Orphée : si inconnu, non conforme, on force à 0 et m
				$hierarchic_level = '0';
				$bibliographic_level = 'm';
				break;
		}
	}else{
		//Sinon on reprend tous en temps que monographie
		$hierarchic_level = '0';
		$bibliographic_level = 'm';
	}


	for ($i=0;$i<count($record->inner_directory);$i++) {
		$cle=$record->inner_directory[$i]['label'];
		//$length=$record->inner_directory[$i]['length'];
		//$adress=$record->inner_directory[$i]['adress'];
		$flag_cle=0;
		if(!array_key_exists($cle,$indicateur)) {
			$flag_cle=1;
		}
		// memo indicateur de champ
		$indicateur[$cle][]=substr($record->inner_data[$i]['content'],0,2);
		if($flag_cle) {
			switch($cle) {
				case "001" :
					$id_import=$record->get_subfield($cle);
					$id_unimarc = $id_import[0];
					break;
				case "010": /* isbn */
					$isbn=$record->get_subfield($cle,'a');
					if(empty($prix)) {
						$prix=$record->get_subfield($cle,'d');
					}
					break;
				case "011": /* issn_011 */
					$issn_011=$record->get_subfield($cle,'a');
					break;
				case "071": /* barcode */
					$cb=$record->get_subfield($cle,"a");
					if(empty($prix)) {
						$prix=$record->get_subfield($cle,'d');
					}
					break;
				case "101": /* language */
					$lang_code=$record->get_subfield_array($cle,"a");
					$org_lang_code=$record->get_subfield_array($cle,"c");
					break;
				case "200": /* titles */
					$tit_200a=$record->get_subfield_array($cle, 'a');
					$tit_200c=$record->get_subfield_array($cle, 'c');
					$tit_200d=$record->get_subfield_array($cle, 'd');
					$tit_200e=$record->get_subfield_array($cle, 'e');
					$tit_200v=$record->get_subfield_array($cle, 'v');
					$serie_200=$record->get_subfield($cle,"h","i");
					break;
				case "205": /* no_edition */
					$no_edition=$record->get_subfield_array($cle,"a");
					break;
				case "210": /* publisher */ // b: adr
				case "214":
				case "219":
					$editeur_lieu=$record->get_subfield_array_array($cle, "a");
					$editeur_adr=$record->get_subfield_array_array($cle, "b");
					$editeur_nom=$record->get_subfield_array_array($cle, "c");
					$editeur_date=$record->get_subfield_array($cle, "d");
					$editeur_pays=$record->get_subfield_array_array($cle, "z");
					$editeur_pmbfields=$record->get_subfield_array_array($cle, "9");
					break;
				case "215": /* description */
					$npages=$record->get_subfield_array($cle,"a");
					$ill=$record->get_subfield_array($cle,"c");
					$size=$record->get_subfield_array($cle,"d");
					$accomp=$record->get_subfield_array($cle,"e");
					break;
				case "225": /* collection */
					$collection_225=$record->get_subfield($cle,"a","i","v","x");
					break;
				case "300": /* inside */
					$n_gen=$record->get_subfield_array($cle,"a");
					break;
				case "327": /* inside */
					$n_contenu=$record->get_subfield_array($cle,"a");
					break;
				case "330": /* abstract */
					$n_resume=$record->get_subfield_array($cle,"a");
					break;
				case "345": /* EAN */
					$EAN=$record->get_subfield($cle,"b");
					$prix_cd=$record->get_subfield($cle,"d");
					break;
				case "410": /* collection */
					$collection_410=$record->get_subfield($cle,"v","t","x","3");
					break;
				case "411": /* sub-collection */
					$collection_411=$record->get_subfield($cle,"v","t","x","3");
					break;
				case "461": /* series */
					$serie=$record->get_subfield($cle,"t","v");
					break;
				case "464": /* analytique */
					// $a pour le tout-venant le reste pour les périodiques bretons ! C'est un periodique donc un depouillement ou une notice objet
					$analytique=$record->get_subfield_array_array($cle);
					$info_464=$record->get_subfield($cle,"t","v","p","d","z","e");
					break;
				case "500": // titres uniformes
					$tu_500=$record->get_subfield($cle,"a","i","k","l","m","n","q","u","w","3");
					$tu_500_r=$record->get_subfield_array_array($cle,"r");
					$tu_500_s=$record->get_subfield_array_array($cle,"s");
					$tu_500_j=$record->get_subfield_array_array($cle,"j");
					break;
				case "600": // 600 PERSONAL NAME USED AS SUBJECT
					$info_600_a=$record->get_subfield_array_array($cle,"a");
					$info_600_j=$record->get_subfield_array_array($cle,"j");
					$info_600_x=$record->get_subfield_array_array($cle,"x");
					$info_600_y=$record->get_subfield_array_array($cle,"y");
					$info_600_z=$record->get_subfield_array_array($cle,"z");
					break;
				case "601": // 601 CORPORATE BODY NAME USED AS SUBJECT
					$info_601_a=$record->get_subfield_array_array($cle,"a");
					$info_601_j=$record->get_subfield_array_array($cle,"j");
					$info_601_x=$record->get_subfield_array_array($cle,"x");
					$info_601_y=$record->get_subfield_array_array($cle,"y");
					$info_601_z=$record->get_subfield_array_array($cle,"z");
					break;
				case "602": // 602 FAMILY NAME USED AS SUBJECT
					$info_602_a=$record->get_subfield_array_array($cle,"a");
					$info_602_j=$record->get_subfield_array_array($cle,"j");
					$info_602_x=$record->get_subfield_array_array($cle,"x");
					$info_602_y=$record->get_subfield_array_array($cle,"y");
					$info_602_z=$record->get_subfield_array_array($cle,"z");
					break;
				case "605": // 605 TITLE USED AS SUBJECT
					$info_605_a=$record->get_subfield_array_array($cle,"a");
					$info_605_j=$record->get_subfield_array_array($cle,"j");
					$info_605_x=$record->get_subfield_array_array($cle,"x");
					$info_605_y=$record->get_subfield_array_array($cle,"y");
					$info_605_z=$record->get_subfield_array_array($cle,"z");
					break;
				case "606": // RAMEAU / TOPICAL NAME USED AS SUBJECT
					$info_606_a=$record->get_subfield_array_array($cle,"a");
					$info_606_j=$record->get_subfield_array_array($cle,"j");
					$info_606_x=$record->get_subfield_array_array($cle,"x");
					$info_606_y=$record->get_subfield_array_array($cle,"y");
					$info_606_z=$record->get_subfield_array_array($cle,"z");
					break;
				case "607": // 607 GEOGRAPHICAL NAME USED AS SUBJECT
					$info_607_a=$record->get_subfield_array_array($cle,"a");
					$info_607_j=$record->get_subfield_array_array($cle,"j");
					$info_607_x=$record->get_subfield_array_array($cle,"x");
					$info_607_y=$record->get_subfield_array_array($cle,"y");
					$info_607_z=$record->get_subfield_array_array($cle,"z");
					break;
				case "610": /* mots clé */
					$index_sujets=$record->get_subfield_array($cle,"a");
					break;
				case "676": /* Dewey */
					$dewey=$record->get_subfield_array($cle,"a");
					$dewey_l=$record->get_subfield_array($cle,"l");
					$dewey_num=$record->get_subfield_array($cle,"l","3");
					break;
				case "686": /* pcdm3 */
					$info_686=$record->get_subfield($cle,"a","l","3");
					break;
				case "700":
					$aut_700=$record->get_subfield($cle,"a","b","c","d","4","f","N","3");
					break;
				case "701":
					$aut_701=$record->get_subfield($cle,"a","b","c","d","4","f","N","3");
					break;
				case "702":
					$aut_702=$record->get_subfield($cle,"a","b","c","d","4","f","N","3");
					break;
				case "710":
					$aut_710=$record->get_subfield($cle,"a","b","c","g","d","4","f","e","k","l","m","n","3");
					break;
				case "711":
					$aut_711=$record->get_subfield($cle,"a","b","c","g","d","4","f","e","k","l","m","n","3");
					break;
				case "712":
					$aut_712=$record->get_subfield($cle,"a","b","c","g","d","4","f","e","k","l","m","n","3");
					break;
				case "801": /* origine du catalogage */
					$origine_notice=$record->get_subfield($cle,"a","b");
					break;
				case "852": /* infos du SCD de Lyon 3 */
					$info_852=$record->get_subfield($cle, "b", "h", "p", "y");
					break;
				case "856": /* url */
					$lien=$record->get_subfield($cle,"u");
					$eformat=$record->get_subfield($cle,"q");
					break;
				case "995": /* infos de la BDP */
					$info_995=$record->get_subfield($cle, "a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z", "0","1","2","3","4","5","6","7","8","9","A","B","C","D","M","N","O","P","R","S","T","U","V","W","Z");
					break;
				case "996": /* infos supplémentaires... ? */
					$info_996=$record->get_subfield($cle, "a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z", "0","1","2","3","4","5","6","7","8","9","A","B","C","D","M","N","O","P","R","S","T","U","V","W","Z");
					break;
				default:
					break;

			} /* end of switch */
		}
	} /* end of for */

	//Récupération des catégories en lien avec le fichier xml
	category_auto::get_info_categ($record);


	return true ;

} // fin recup_noticeunimarc = fin récupération des variables lues dans la notice UNIMARC


/*
 *  Récupération des liens des notices
 */
function recup_noticeunimarc_link($notice){
	global $notices_liees, $titre_ppal_200, $titre_perio_530a, $champ_210 ;

	$record = new iso2709_record($notice,AUTO_UPDATE);
	$titre_ppal_200 = $record->get_subfield('200','a','h','i');
	$champ_210 = $record->get_subfield('210','d','h');
	$titre_perio_530a = $record->get_subfield('530','a');
	$notices_liees=$record->get_all_fields('4..');
}

/*
 * En cas de remplacement de notice
 */
function del_associations_existing_notice($id=0) {
	if($id) {
		//Suppression de la vignette de la notice si il y en a une d'uploadée
		thumbnail::delete($id);

		$p_perso=new parametres_perso("notices");
		$p_perso->delete_values($id);

		$requete = "DELETE FROM notices_categories WHERE notcateg_notice='$id'" ;
		pmb_mysql_query($requete);

		$requete = "DELETE FROM notices_langues WHERE num_notice='$id'" ;
		pmb_mysql_query($requete);

		notice_relations::delete($id);

		$requete = "DELETE FROM responsability WHERE responsability_notice='$id'" ;
		pmb_mysql_query($requete);

		// Supression des liens avec les titres uniformes
		$requete = "DELETE FROM notices_titres_uniformes WHERE ntu_num_notice='$id'" ;
		pmb_mysql_query($requete);

		// Nettoyage indexation concepts
		$index_concept = new index_concept($id, TYPE_NOTICE);
		$index_concept->delete();
	}
}

/*
 * Import d'une nouvelle notice
 */
function import_new_notice($notice_existing_id=0) {
	global $notice_id ;
	global $bulletin_ex;//Identifiant du bulletin
	$bulletin_ex=0;
	global $id_unimarc;
	global $notices_crees;
	global $bulletins_crees;
	global $notices_a_creer;
	global $bulletins_a_creer;
	global $base_path;
	global $pmb_keyword_sep;
	global $link_generate;
	global $doc_type 		;
	global $hierarchic_level	;
	global $bibliographic_level	;
	global $isbn_OK			;
	global $prix			;
	global $prix_cd			;
	global $cb				;
	global $tit_200a		;
	global $tit_200c		;
	global $tit_200d		;
	global $tit_200e		;
	global $tit_200v		;
	global $serie_200		;
	global $editor			;
	global $editeur_lieu  	;
	global $editeur_adr  	;
	global $editeur_nom		;
	global $editeur_date	;
	global $editeur_pays	;
	global $editeur_pmbfields	;
	global $no_edition		;
	global $npages			;
	global $ill				;
	global $size			;
	global $accomp			;
	global $collection_225	;
	global $n_contenu		;
	global $n_resume		;
	global $n_gen			;
	global $EAN				;
	global $collection_410	;
	global $collection_411	;
	global $tu_500		;
	global $tu_500_r		;
	global $tu_500_s		;
	global $tu_500_j		;
	global $serie			;
	global $index_sujets	;
	global $dewey			;
	global $dewey_l			;
	global $aut_700			;
	global $aut_701			;
	global $aut_702			;
	global $aut_710			;
	global $aut_711			;
	global $aut_712			;
	global $origine_notice	;
	global $lien			;
	global $eformat			;
	global $analytique		;
	global $statutnot 		;
	global $notice_is_new	;
	global $indicateur;
	global $add_explnum;
	global $info_686;

	global $authorities_notices;
	global $authorities_default_origin;

	global $import_force_notice_is_new;
	global $import_notice_existing_replace;

	global $notice_replace_links;

	//Suppression des associations pour le remplacement
	$notice_existing_id = intval($notice_existing_id);
	if($import_notice_existing_replace && $notice_existing_id) {
		del_associations_existing_notice($notice_existing_id);
	}

	$origin_authority= $authorities_default_origin;
	$add_explnum=FALSE;
	/* traitement des éditeurs */
	$coll_id=0;
	$subcoll_id=0;
	$serie_id=0;
	$tnvol_ins="";
	$ed1_id=0;
	$ed2_id=0;
	$year="";
	$date_parution="0000-00-00";

	//On récupère le tableau des notices créées à partir du fichier temporaire (rechargement d'iframe)
	if($link_generate && !isset($notices_crees)){
		$tabimport_id= file_get_contents("$base_path/temp/liste_id".SESSid.".txt");
		if($tabimport_id){
			$tabimport_id = unserialize($tabimport_id);
			$notices_crees = $tabimport_id['notices_existantes'];
            $notices_a_creer = $tabimport_id['notices_a_creer'];
            $bulletins_crees = $tabimport_id['bulletins_crees'];
            $bulletins_a_creer = $tabimport_id['bulletins_a_creer'];
		}
	}

	if(!is_array($notices_crees)) $notices_crees = array();

	//Pour le cas ou on est sur une notice (bidon) d'article juste pour les exemplaires de bulletin
	if($bibliographic_level == "a" && isset($tit_200d[0]) && $tit_200d[0] == "Article_expl_bulletin"){
		//On importe rien
		return;
	}elseif(isset($tit_200d[0]) && $tit_200d[0] == "Article_expl_bulletin"){
		//Pour le cas ou on import sans les liens un fichier exporter avec les exemplaires et les liens
		$tit_200d[0]="";
	}

	$coll_name="";
	$subcoll_name="";
	$coll_issn="";
	$coll_aut_number="";
	$subcoll_issn="";
	$subcoll_aut_number="";
	$nocoll_ins="";
	$collec = array();
	$subcollec = array();

	if($bibliographic_level != "a" && $bibliographic_level != "b"){
		//Pour les articles et les bulletins on ne garde pas les informations suivantes
		$year = (isset($editeur_date[0]) ? clean_string($editeur_date[0]) : '');
		$date_parution=notice::get_date_parution($year);

		//traitement des éditeurs
		$editor=array();
		foreach ( $editeur_nom as $key_nom1 => $nom1 ) {
       		foreach ( $nom1 as $key_nom2 => $nom2 ) {
       			$mon_ed=array();
       			$mon_ed["c"]=$nom2;

       			if(isset($editeur_adr[$key_nom1][$key_nom2])){
       				$mon_ed["b"]=$editeur_adr[$key_nom1][$key_nom2];
       			}

       			if(isset($editeur_lieu[$key_nom1][$key_nom2])){
       				$mon_ed["a"]=$editeur_lieu[$key_nom1][$key_nom2];
       			}

       			if(isset($editeur_pays[$key_nom1][$key_nom2])){
       				$mon_ed["z"]=$editeur_pays[$key_nom1][$key_nom2];
       			}
       			if($editeur_pmbfields[$key_nom1][$key_nom2]){//Si j'ai ces informations c'est un export PMB donc je n'ai pas deux editeurs dans le même 210
       				foreach ( $editeur_pmbfields[$key_nom1] as $val9 ) {
       					if(preg_match("/^(.+?):(.+)$/",$val9,$matches)){
       						$mon_ed[trim($matches[1])]=$matches[2];
       					}
					}
       			}
       			$editor[]=$mon_ed;
			}
		}
		$ed=array();
		$ed['name'] = (isset($editor[0]['c']) ? clean_string($editor[0]['c']) : '');
		$ed['adr'] = (isset($editor[0]['b']) ? clean_string($editor[0]['b']) : '');
		if(isset($editor[0]['adr1']) && $editor[0]['adr1'])$ed['adr']=clean_string($editor[0]['adr1']);//Si cela vient de PMB dans $b j'ai un concat
		$ed['ville'] = (isset($editor[0]['a']) ? clean_string($editor[0]['a']) : '');
		$ed['adr2'] = (isset($editor[0]['adr2']) ? clean_string($editor[0]['adr2']) : '');
		$ed['cp'] = (isset($editor[0]['cp']) ? clean_string($editor[0]['cp']) : '');
		$ed['pays'] = (isset($editor[0]['z']) ? clean_string($editor[0]['z']) : '');
		$ed['web'] = (isset($editor[0]['web']) ? clean_string($editor[0]['web']) : '');
		$ed['ed_comment'] = (isset($editor[0]['comment']) ? clean_string($editor[0]['comment']) : '');
		$ed1_id = editeur::import($ed);
		$ed=array();
		$ed['name'] = (isset($editor[1]['c']) ? clean_string($editor[1]['c']) : '');
		$ed['adr'] = (isset($editor[1]['b']) ? clean_string($editor[1]['b']) : '');
		if(isset($editor[1]['adr1']) && $editor[1]['adr1'])$ed['adr']=clean_string($editor[1]['adr1']);//Si cela vient de PMB dans $b j'ai un concat
		$ed['ville'] = (isset($editor[1]['a']) ? clean_string($editor[1]['a']) : '');
		$ed['adr2'] = (isset($editor[1]['adr2']) ? clean_string($editor[1]['adr2']) : '');
		$ed['cp'] = (isset($editor[1]['cp']) ? clean_string($editor[1]['cp']) : '');
		$ed['pays'] = (isset($editor[1]['z']) ? clean_string($editor[1]['z']) : '');
		$ed['web'] = (isset($editor[1]['web']) ? clean_string($editor[1]['web']) : '');
		$ed['ed_comment'] = (isset($editor[1]['comment']) ? clean_string($editor[1]['comment']) : '');
		$ed2_id = editeur::import($ed);
		if($bibliographic_level != "s"){
			//Pour les periodiques on ne garde pas les informations suivantes
			/* traitement des collections */
			/* traitement de 225$a, si rien alors 410$t pour la collection */
			if (isset($collection_225[0]['a']) && $collection_225[0]['a']!="") {
				$coll_name=$collection_225[0]['a'];
				$coll_issn=$collection_225[0]['x'];
			} elseif (isset($collection_410[0]['t']) && $collection_410[0]['t']!="") {
				$coll_name=$collection_410[0]['t'];
				$coll_issn=$collection_410[0]['x'];
				$coll_aut_number = $collection_410[0]['3'];
			}
			/* traitement de 225$i, si rien alors 411$t pour la sous-collection */
			if (isset($collection_225[0]['i']) && $collection_225[0]['i']!="") {
				$subcoll_name=$collection_225[0]['i'];
				$subcoll_issn=$collection_225[1]['x'];
			} elseif (isset($collection_411[0]['t']) && $collection_411[0]['t']!="") {
					$subcoll_name=$collection_411[0]['t'];
					$subcoll_issn=$collection_411[0]['x'];
					$coll_aut_number = $collection_411[0]['3'];
			}
			/* gaffe au nocoll, en principe en 225$v selon FL  sinon en 410$v ou 411$v*/
			if (isset($collection_225[0]['v']) && $collection_225[0]['v']!="")
				$nocoll_ins=$collection_225[0]['v'];
			elseif(isset($collection_410[0]['v']) && $collection_410[0]['v']!="")
				$nocoll_ins=$collection_410[0]['v'];
			elseif(isset($collection_411[0]['v']) && $collection_411[0]['v']!="")
				$nocoll_ins=$collection_411[0]['v'];
			else
				$nocoll_ins="";

			$collec['name'] = ($coll_name ? clean_string($coll_name) : '');
			$collec['parent'] = $ed1_id;
			$collec['issn'] = ($coll_issn ? clean_string($coll_issn) : '');
			$collec['authority_number'] = $coll_aut_number;
			if($collec['authority_number']){
				$coll_id = keep_authority_infos($collec['authority_number'],"collection",$origin_authority,0,$collec);
			}else{
				$coll_id = collection::import($collec);
			}

			/* sous collection */
			$subcollec['name'] = ($subcoll_name ? clean_string($subcoll_name) : '');
			$subcollec['coll_parent'] = $coll_id;
			$subcollec['issn'] = ($subcoll_issn ? clean_string($subcoll_issn) : '');
			$subcollec['authority_number'] = $subcoll_aut_number;
			if($subcollec['authority_number']){
				$subcoll_id = keep_authority_infos($subcollec['authority_number'],"subcollection",$origin_authority,0,$subcollec);
			}else{
				$subcoll_id = collection::import($subcollec);
			}
			$subcoll_id = subcollection::import($subcollec);

			/* traitement des séries */
			if(isset($serie[0]['t'])) {
				$serie_id = serie::import(clean_string($serie[0]['t']));
			} else {
				$serie_id = 0;
			}
			$tnvol_ins = (isset($serie[0]['v']) ? $serie[0]['v'] : '');
			if(!$serie_id){
				if(isset($serie_200[0]['i'])) {
					$serie_id = serie::import(clean_string($serie_200[0]['i']));
				} else {
					$serie_id = 0;
				}
				$serie[0]['t'] = (isset($serie_200[0]['i']) ? $serie_200[0]['i'] : '');
				//$tnvol_ins=$serie_200[0]['h'];
			}
			if(!$tnvol_ins){
				$tnvol_ins = (isset($serie_200[0]['h']) ? $serie_200[0]['h'] : '');
			}
		}
	}

	/* traitement de Dewey */
	if(isset($dewey[0])) {
		$indexint_id = indexint::import(clean_string($dewey[0]),$dewey_l[0]);
	} else {
		$indexint_id = 0;
	}
	if(!$indexint_id and count($info_686)){
		if(isset($info_686[0]["a"])) {
			$indexint_id = indexint::import(clean_string($info_686[0]["a"]),$info_686[0]["l"],99);
		} else {
			$indexint_id = 0;
		}
	}

	/* Traitement des notes */
	$n_resume_total  = "";
	$n_gen_total     = "";
	$n_contenu_total = "";

	if (!$n_resume) $n_resume=array();
	$n_resume_total= implode("\n",$n_resume);

	if (!$n_gen) $n_gen=array();
	$n_gen_total= implode("\n",$n_gen);

	if (!$n_contenu) $n_contenu=array();
	$n_contenu_total= implode("\n",$n_contenu);

	// ajout : les 464$a sont ajouté aux notes de contenu à déporter éventuellement dans func_bdp41 si besoin
	for ($i = 0; $i< count($analytique); $i++) {
		$ana=array();
		for ($j = 0; $j< count($analytique[$i]); $j++) {
			$ana[$analytique[$i][$j]["label"]][]=$analytique[$i][$j]["content"];
		}
		if(isset($ana["a"]) && is_array($ana["a"])) {
			$ana["e"] ??= [];
			$ana["f"] ??= [];
			$ana["g"] ??= [];
		    if(count($ana["a"]) == 1){//Selon la norme le $a n'est pas répétable dans le même champ 4XX
		        $n_contenu_total.=$ana["a"][0].(is_countable($ana["e"]) && count($ana["e"])?" ; ".implode(" ; ",$ana["e"]):"").(is_countable($ana["f"]) && count($ana["f"])?" / ".implode(" / ",$ana["f"]):"").(is_countable($ana["g"]) && count($ana["g"])?" / ".implode(" / ",$ana["g"]):"")."\n";
		    }else{//Au cas où
		        for ($j=0; $j<count($ana["a"]); $j++) {
		            $n_contenu_total.=$ana["a"][$j].($ana["e"][$j]?" ; ".$ana["e"][$j]:"").($ana["f"][$j]?" / ".$ana["f"][$j]:"").($ana["g"][$j]?" / ".$ana["g"][$j]:"")."\n";
		        }
		    }
		}
	}



	// Préparation des titres
	$tit = array();
	$tit[0]['a'] = implode (" ; ",$tit_200a);
	if($bibliographic_level != "a" && $bibliographic_level != "b"  && $bibliographic_level != "s"){
		//Pour les articles et les bulletins on ne garde pas les informations suivantes
		$tit[0]['c'] = implode (" ; ",$tit_200c);
	}else{
		$tit[0]['c']="";
	}

	$tit[0]['d'] = implode (" ; ",$tit_200d);
	$tit[0]['e'] = implode (" ; ",$tit_200e);

	if (is_array($index_sujets))
		$index_l = implode (' '.$pmb_keyword_sep.' ',$index_sujets);
	else
		$index_l = $index_sujets;

	// if (trim($n_resume_total)=="") $n_resume_total = $n_gen_total." ".$n_contenu_total ;

	/* Origine de la notice */
	$origine_not = array();
	$origine_not['nom'] = (isset($origine_notice[0]['b']) ? clean_string($origine_notice[0]['b']) : '');
	$origine_not['pays'] = (isset($origine_notice[0]['a']) ? clean_string($origine_notice[0]['a']) : '');
	$orinot_id = origine_notice::import($origine_not);
	if ($orinot_id==0) $orinot_id=1 ;

	if($bibliographic_level != "a" && $bibliographic_level != "s"){
		//Pour les articles et les periodiques on ne garde pas les informations suivantes
		// prix
		$price = (isset($prix[0]) ? $prix[0] : '');
		//Pour les CDs
		if(!$price){
			$price = (isset($prix_cd[0]) ? $prix_cd[0] : '');
		}

		$illustration=implode (" : ",$ill);
		$taille=implode (" ; ",$size);
		$mat_accomp=implode (" + ",$accomp);
		if($bibliographic_level != "b"){
			$mention_edit=implode (", ",$no_edition);
		}else{
			$mention_edit="";
		}

	}else{
	    $price ="";
		$illustration="";
		$taille="";
		$mat_accomp="";
		$mention_edit="";
	}

	if($bibliographic_level != "s"){
		//Pour les periodiques on ne garde pas les informations suivantes
		$nbpages=implode (" - ",$npages);
	}else{
		$nbpages="";
	}

	//Valeurs à globaliser pour le calcul des droits d'accès
	global $typdoc;
	global $form_notice_statut;
	$typdoc = $doc_type;
	$form_notice_statut = $statutnot;

	$notice_is_new = intval($notice_is_new);
	$notice_date_is_new = '';

	if ($notice_is_new || (isset($import_force_notice_is_new)) && ($import_force_notice_is_new==1)) {
		$notice_is_new = 1;
		$notice_date_is_new = date('Y-m-d H:i:s');
	}

	/* and at least, the insertion in notices table */
	if(isset($import_notice_existing_replace) && ($import_notice_existing_replace==1) && $notice_existing_id) {
		$sql_ins = "update notices set ";
	} else {
		$sql_ins = "insert into notices set ";
	}
	$sql_ins .= "
		typdoc = '".$doc_type."',
		code = '".addslashes($isbn_OK)."',
		statut = '".$statutnot."',
		tit1 = '".(isset($tit[0]['a']) ? addslashes(clean_string($tit[0]['a'])) : '')."',
		tit2 = '".(isset($tit[0]['c']) ? addslashes(clean_string($tit[0]['c'])) : '')."',
		tit3 = '".(isset($tit[0]['d']) ? addslashes(clean_string($tit[0]['d'])) : '')."',
		tit4 = '".(isset($tit[0]['e']) ? addslashes(clean_string($tit[0]['e'])) : '')."',
		tparent_id = '".$serie_id."',
		tnvol = '".addslashes($tnvol_ins)."',
		ed1_id = ".$ed1_id." ,
		ed2_id = ".$ed2_id." ,
		year = '".addslashes($year)."',
		npages = '".addslashes($nbpages)."',
		ill = '".addslashes($illustration)."',
		size = '".addslashes($taille)."',
		accomp = '".addslashes($mat_accomp)."',
		coll_id = ".$coll_id." ,
		subcoll_id = ".$subcoll_id." ,
		nocoll = '".addslashes($nocoll_ins)."',
		mention_edition = '".addslashes($mention_edit)."',
		n_gen = '".addslashes($n_gen_total)."',
		n_contenu = '".addslashes($n_contenu_total )."',
		n_resume = '".addslashes($n_resume_total  )."',
		index_l = '".addslashes($index_l)."',
		indexint = '".$indexint_id."',
		niveau_biblio = '".$bibliographic_level."',
		niveau_hierar = '".$hierarchic_level."',
		lien = '".(isset($lien[0]) ? addslashes($lien[0]) : '')."',
		eformat = '".(isset($eformat[0]) ? addslashes($eformat[0]) : '')."',
		origine_catalogage = '".$orinot_id."',
		prix = '".addslashes($price)."',
		date_parution = '".addslashes($date_parution)."',
		notice_is_new = '".$notice_is_new."',
		notice_date_is_new = '".$notice_date_is_new."' ";
	if(isset($import_notice_existing_replace) && ($import_notice_existing_replace==1) && $notice_existing_id) {
		$sql_ins .= " where notice_id = ".$notice_existing_id;
	} else {
		$sql_ins .= ", create_date = sysdate() ";
	}
	pmb_mysql_query($sql_ins) or die ("Couldn't insert into notices ! = ".$sql_ins);
	if($notice_existing_id) {
		$notice_id = $notice_existing_id;
		audit::insert_modif(AUDIT_NOTICE,$notice_id);
	} else {
		$notice_id = pmb_mysql_insert_id();
		audit::insert_creation(AUDIT_NOTICE,$notice_id);
	}
	notice::majNotices($notice_id);

	//calcul des droits d'accès s'ils sont activés
	notice::calc_access_rights($notice_id);

	// on devait attendre que la notice soit intégrée pour faire l'association avec la notice..;
	if(!empty($collec['authority_number'])){
		keep_authority_infos($collec['authority_number'],"collection",$origin_authority,$notice_id,$collec);
	}
	if(!empty($subcollec['authority_number'])){
		keep_authority_infos($subcollec['authority_number'],"subcollection",$origin_authority,$notice_id,$subcollec);
	}


	/* INSERT de la notice OK, on va traiter les auteurs
	70# : personnal : type auteur 70                71# : collectivités : type auteur 71
	1 seul en 700                                   idem pour les déclinaisons
	n en 701 n en 702
	les 7#0 tombent en auteur principal : responsability_type = 0
	les 7#1 tombent en autre auteur : responsability_type = 1
	les 7#2 tombent en auteur secondaire : responsability_type = 2
	*/
	$aut_array = array();
	/* on compte tout de suite le nbre d'enreg dans les répétables */
	$nb_repet_701 = count($aut_701);
	$nb_repet_711 = count($aut_711);
	$nb_repet_702 = count($aut_702);
	$nb_repet_712 = count($aut_712);
	//indicateur["710"];
	/* renseignement de aut0 */
	if (isset($aut_700[0]['a']) && $aut_700[0]['a']!="") { /* auteur principal en 700 ? */
		$aut_array[] = array(
			"entree" => $aut_700[0]['a'],
			"rejete" => $aut_700[0]['b'],
			"author_comment" => $aut_700[0]['c']." ".$aut_700[0]['d'],
			"date" => $aut_700[0]['f'],
			"type_auteur" => "70",
			"fonction" => $aut_700[0][4],
			"id" => 0,
			"responsabilite" => 0,
			"ordre" => 0,
			'authority_number' =>  $aut_700[0][3]
		) ;
	} elseif (isset($aut_710[0]['a']) && $aut_710[0]['a']!="") { /* auteur principal en 710 ? */
		if(substr($indicateur["710"][0],0,1)=="1")	$type_auteur="72";
			else $type_auteur="71";

		$lieu=$aut_710[0]['e'];
		if(!$lieu)$lieu=$aut_710[0]['k'];
		$aut_array[] = array(
			"entree" => $aut_710[0]['a'],
			"rejete" => $aut_710[0]['g'],
			"subdivision" => $aut_710[0]['b'],
			"author_comment" => $aut_710[0]['c'],
			"numero" => $aut_710[0]['d'],
			"lieu" => $lieu,
			"ville" => $aut_710[0]['l'],
			"pays" => $aut_710[0]['m'],
			"web" => $aut_710[0]['n'],
			"date" => $aut_710[0]['f'],
			"type_auteur" => $type_auteur,
			"fonction" => $aut_710[0][4],
			"id" => 0,
			"responsabilite" => 0,
			"ordre" => 0,
			'authority_number' =>  $aut_710[0][3]
		 ) ;
	}

	/* renseignement de aut1 */
	for ($i=0 ; $i < $nb_repet_701 ; $i++) {
		$aut_array[] = array(
			"entree" => $aut_701[$i]['a'],
			"rejete" => $aut_701[$i]['b'],
			"author_comment" => $aut_701[$i]['c']." ".$aut_701[$i]['d'],
			"date" => $aut_701[$i]['f'],
			"type_auteur" => "70",
			"fonction" => $aut_701[$i][4],
			"id" => 0,
			"responsabilite" => 1,
			"ordre" => ($i+1),
			'authority_number' =>  $aut_701[$i][3]
		) ;
	}

	for ($i=0 ; $i < $nb_repet_711 ; $i++) {
		if(substr($indicateur["711"][$i],0,1)=="1")	$type_auteur="72";
		else $type_auteur="71";

		$lieu=$aut_711[$i]['e'];
		if(!$lieu)$lieu=$aut_711[$i]['k'];
		$aut_array[] = array(
			"entree" => $aut_711[$i]['a'],
			"rejete" => $aut_711[$i]['g'],
			"subdivision" => $aut_711[$i]['b'],
			"author_comment" => $aut_711[$i]['c'],
			"numero" => $aut_711[$i]['d'],
			"lieu" => $lieu,
			"ville" => $aut_711[$i]['l'],
			"pays" => $aut_711[$i]['m'],
			"web" => $aut_711[$i]['n'],
			"date" => $aut_711[$i]['f'],
			"type_auteur" => $type_auteur,
			"fonction" => $aut_711[$i][4],
			"id" => 0,
			"responsabilite" => 1,
			"ordre" => ($i+1),
			'authority_number' =>  $aut_711[$i][3]
		) ;
	}

	/* renseignement de aut2 */
	for ($i=0 ; $i < $nb_repet_702 ; $i++) {
		$aut_array[] = array(
			"entree" => $aut_702[$i]['a'],
			"rejete" => $aut_702[$i]['b'],
			"author_comment" => $aut_702[$i]['c']." ".$aut_702[$i]['d'],
			"date" => $aut_702[$i]['f'],
			"type_auteur" => "70",
			"fonction" => $aut_702[$i][4],
			"id" => 0,
			"responsabilite" => 2,
			"ordre" => ($i+1),
			'authority_number' =>  $aut_702[$i][3]
		) ;
	}

	for ($i=0 ; $i < $nb_repet_712 ; $i++) {
		if(substr($indicateur["712"][$i],0,1)=="1")	$type_auteur="72";
		else $type_auteur="71";

		$lieu=$aut_712[$i]['e'];
		if(!$lieu)$lieu=$aut_712[$i]['k'];
		$aut_array[] = array(
			"entree" => $aut_712[$i]['a'],
			"rejete" => $aut_712[$i]['g'],
			"subdivision" => $aut_712[$i]['b'],
			"author_comment" => $aut_712[$i]['c'],
			"numero" => $aut_712[$i]['d'],
			"lieu" => $lieu,
			"ville" => $aut_712[$i]['l'],
			"pays" => $aut_712[$i]['m'],
			"web" => $aut_712[$i]['n'],
			"date" => $aut_712[$i]['f'],
			"type_auteur" => $type_auteur,
			"fonction" => $aut_712[$i][4],
			"id" => 0,
			"responsabilite" => 2,
			"ordre" => ($i+1),
			'authority_number' =>  $aut_712[$i][3]
		) ;
	}

	// récup des infos auteurs et mise en tableau :
	// appel de la fonction membre d'importation et insertion en table
	$rqt_ins = "insert into responsability (responsability_author, responsability_notice, responsability_fonction, responsability_type, responsability_ordre) VALUES ";
	$values = "";
	if (!empty($aut_array)) {
	    $nb_aut = count($aut_array);
	    for ($i = 0; $i < $nb_aut; $i++) {
			$aut = array();
			$aut['name'] = (isset($aut_array[$i]['entree']) ? clean_string($aut_array[$i]['entree']) : '');
			$aut['rejete'] = (isset($aut_array[$i]['rejete']) ? clean_string($aut_array[$i]['rejete']) : '');
			$aut['type'] = $aut_array[$i]['type_auteur'];
			$aut['date'] = (isset($aut_array[$i]['date']) ? clean_string($aut_array[$i]['date']) : '');
			$aut['subdivision'] = (isset($aut_array[$i]['subdivision']) ? clean_string($aut_array[$i]['subdivision']) : '');
			$aut['numero'] = (isset($aut_array[$i]['numero']) ? clean_string($aut_array[$i]['numero']) : '');
			$aut['lieu'] = (isset($aut_array[$i]['lieu']) ? clean_string($aut_array[$i]['lieu']) : '');
			$aut['ville'] = (isset($aut_array[$i]['ville']) ? clean_string($aut_array[$i]['ville']) : '');
			$aut['pays'] = (isset($aut_array[$i]['pays']) ? clean_string($aut_array[$i]['pays']) : '');
			$aut['web'] = (isset($aut_array[$i]['web']) ? clean_string($aut_array[$i]['web']) : '');
			$aut['author_comment'] = (isset($aut_array[$i]['author_comment']) ? clean_string($aut_array[$i]['author_comment']) : '');
			$aut['authority_number'] = (isset($aut_array[$i]['authority_number']) ? clean_string($aut_array[$i]['authority_number']) : '');
			//si on à demander la prise en compte des numéro d'autorités
			if($authorities_notices && $aut['authority_number']!=""){
				$aut_array[$i]["id"] = keep_authority_infos($aut['authority_number'],"author",$origin_authority,$notice_id,$aut);
			}
			if(!$aut_array[$i]["id"]){
				$aut_array[$i]["id"] = auteur::import($aut);
			}
			$aut_array[$i]['fonction'] = trim($aut_array[$i]['fonction']);
			if ($aut_array[$i]["id"]) {
			    $req_select = "SELECT * FROM responsability
                WHERE responsability_author = '".$aut_array[$i]["id"]."'
                AND responsability_notice = '$notice_id'
                AND responsability_fonction = '".addslashes($aut_array[$i]['fonction'])."'
                AND responsability_type = '".$aut_array[$i]['responsabilite']."'
                AND responsability_ordre = '".$aut_array[$i]['ordre']."'";
			    $res_select = pmb_mysql_query($req_select);
			    if (pmb_mysql_num_rows($res_select)) {
			        continue;
			    }
				if($values!= "") $values.=",";
				$values.=" ('".$aut_array[$i]["id"]."','".$notice_id."','".addslashes($aut_array[$i]['fonction'])."','".$aut_array[$i]['responsabilite']."','".$aut_array[$i]['ordre']."') " ;
//				$rqt = $rqt_ins . " ('".$aut_array[$i]["id"]."','".$notice_id."','".addslashes($aut_array[$i]['fonction'])."','".$aut_array[$i]['responsabilite']."','".$aut_array[$i]['ordre']."') " ;
//				@pmb_mysql_query($rqt);
			}
		}
		@pmb_mysql_query($rqt_ins.$values);
	}
	// Titres uniformes
	global $pmb_use_uniform_title;
	if ($pmb_use_uniform_title) {
	    $value_tu = array();
		$nb_tu = count($tu_500);
		for ($i=0 ; $i<$nb_tu ; $i++ ) {
		    $value_tu[$i] = array();
		    $value_tu[$i]['name'] = $tu_500[$i]['a'];
			$value_tu[$i]['tonalite'] = $tu_500[$i]['u'];
			$value_tu[$i]['comment'] = $tu_500[$i]['n'];
			$value_tu[$i]['authority_number'] = $tu_500[$i]['3'];

			for($j=0;$j<count($tu_500_r[$i]);$j++) {
				$value_tu[$i]['distrib'][$j]= $tu_500_r[$i][$j];
			}
			for($j=0;$j<count($tu_500_s[$i]);$j++) {
				$value_tu[$i]['ref'][$j]= $tu_500_s[$i][$j];
			}
			for($j=0;$j<count($tu_500_j[$i]);$j++) {
				$value_tu[$i]['subdiv'][$j]= $tu_500_j[$i][$j];
			}
			if($authorities_notices && $aut['authority_number']!=""){
				$tu_id = keep_authority_infos($value_tu[$i]['authority_number'],"uniform_title",$origin_authority,$notice_id,$value_tu[$i]);
			}else{
				$tu_id = titre_uniforme::import($value_tu[$i]);
			}
			if($tu_id) {
				$requete = "INSERT INTO notices_titres_uniformes SET
				ntu_num_notice='$notice_id',
				ntu_num_tu='$tu_id',
				ntu_titre='".addslashes($tu_500[$i]['i'])."',
				ntu_date='".addslashes($tu_500[$i]['k'])."',
				ntu_sous_vedette='".addslashes($tu_500[$i]['l'])."',
				ntu_langue='".addslashes($tu_500[$i]['m'])."',
				ntu_version='".addslashes($tu_500[$i]['q'])."',
				ntu_mention='".addslashes($tu_500[$i]['w'])."',
				ntu_ordre=$i
				";
				pmb_mysql_query($requete);
			}
		}
	}

	global $lang_code		;
	global $org_lang_code		;
	if((is_array($lang_code) && count($lang_code)) || (is_array($org_lang_code) && count($org_lang_code))) {
		$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
		$values = "";
		$lang_code = array_unique($lang_code);
		for ($i=0; $i<count($lang_code); $i++) {
			$lang_code[$i]=trim($lang_code[$i]);
			if($values!="") $values.=",";
			$values.="('$notice_id',0, '".addslashes($lang_code[$i])."','$i') ";
			//$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue) VALUES ('$notice_id',0, '".addslashes($lang_code[$i])."') " ;
			//@pmb_mysql_query($rqt_ins);
		}
		$org_lang_code = array_unique($org_lang_code);
		for ($i=0; $i<count($org_lang_code); $i++) {
			$org_lang_code[$i]=trim($org_lang_code[$i]);
			if($values!="") $values.=",";
			$values.="('$notice_id',1, '".addslashes($org_lang_code[$i])."','".$i."') ";
			//$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue) VALUES ('$notice_id',1, '".addslashes($org_lang_code[$i])."') " ;
// 			@pmb_mysql_query($rqt_ins);
		}
		@pmb_mysql_query($rqt_ins.$values);
	}
	//Import des catégories
	category_auto::save_info_categ();
	//Calcule de la signature
	$sign= new notice_doublon();
	$val= $sign->gen_signature($notice_id);
	pmb_mysql_query("update notices set signature='$val' where notice_id=".$notice_id);

	//Si on a un id de notice et qu'il n'est pas dans le tableau des notices créées, on l'ajoute
	if($link_generate && trim($id_unimarc) !== "" && !$notices_crees[$id_unimarc]){
		$notices_crees[$id_unimarc]=$notice_id;

	}elseif($link_generate && trim($id_unimarc) !== "" && $notices_crees[$id_unimarc]){
		//Si la notice a déjà été créée (Export des liens dans les notices liées) on remplace celle précédemment par celle en cours de traitement
		 $niveau_biblio=$bibliographic_level.$hierarchic_level;
		 switch ($niveau_biblio) {
		 	case 'm0':
		 		//On a une notice de monographie
		 		$notice_a_supp=$notices_crees[$id_unimarc];
		 		$ma_notice= new notice($notice_a_supp);
		 		$ma_notice->replace($notice_id);
		 		break;
		 	case 's1':
		 		//On a une notice de periodique
		 		$notice_a_supp=$notices_crees[$id_unimarc];
		 		$ma_notice= new serial($notice_a_supp);
		 		$ma_notice->replace($notice_id);
		 		break;
		 	case 'b2':
		 		//On a une notice de bulletin
		 		$notice_a_supp=$notices_crees[$id_unimarc];
		 		//Dans les bulletins
		 		$requete="update bulletins set num_notice='".$notice_id."' where num_notice='".$notice_a_supp."' ";
		 		pmb_mysql_query($requete);
		 		//Dans les relations entre notice
		 		notice_relations::replace_links($notice_a_supp, $notice_id, $notice_replace_links);
		 		notice::del_notice($notice_a_supp);
		 		break;
		 	case 'a2':
		 		//On a une notice d'article
		 		$notice_a_supp=$notices_crees[$id_unimarc];
		 		//Dans les bulletins
		 		$requete="update analysis set analysis_notice='".$notice_id."' where analysis_notice='".$notice_a_supp."' ";
		 		pmb_mysql_query($requete);
		 		//Dans les relations entre notice
		 		notice_relations::replace_links($notice_a_supp, $notice_id, $notice_replace_links);
		 		notice::del_notice($notice_a_supp);
		 		break;
		}
		$notices_crees[$id_unimarc]=$notice_id;
	}

} // fin import_new_notice


/*
 * Importer les liens entre les notices
 */

function import_notice_link(){
	global $notices_liees, $notices_a_creer, $id_unimarc, $notices_crees, $bulletins_crees;
	global $notice_id, $titre_ppal_200, $titre_perio_530a, $champ_210, $bulletins_a_creer;
	global $hierarchic_level, $bibliographic_level, $tit_200d,$bulletin_ex;

	if($bibliographic_level == "a" && $hierarchic_level == "2" && $tit_200d[0] == "Article_expl_bulletin"){
		//Quand on est sur le cas de la reconstruction d'un exemplaire de bulletin
		$tab_bull= array();
		$tab_bull = get_infos_notices_liees($notices_liees,'463','bull_expl');

		$id_perio=creer_notice_periodique($tab_bull[0]["0"][0],$tab_bull[0]["t"][1],$tab_bull[0]["x"][0]);
		$bulletin=array();
		$bulletin=array("titre"=>$tab_bull[0]["t"][0],"date"=>$tab_bull[0]["d"][0],"mention"=>$tab_bull[0]["e"][0],"num"=>$tab_bull[0]["v"][0]);
		$bulletin_ex=creer_bulletin($id_perio,$bulletin);
		return;
	}


	//Traitements des liens entre notices
	$requete = "SELECT * FROM notices WHERE notice_id='".$notice_id."'";
	$res = pmb_mysql_query($requete);
	while(($notice_creee=pmb_mysql_fetch_object($res))){
		$niveau_biblio = $notice_creee->niveau_biblio.$notice_creee->niveau_hierar;
		$tab_field = $tab_art = $tab_bull = $tab_perio = array();

		switch($niveau_biblio){
			case 's1' :
				//Lien(s) vers Périodique
				$tab_art = get_infos_notices_liees($notices_liees,'464','art');
				$tab_bull = get_infos_notices_liees($notices_liees,'462','bull');
				creer_bulletinage_et_articles($tab_bull,$tab_art);
				break;
			case 'b2' :
				//Lien(s) vers Notice de bulletin
				creer_liens_pour_bull_notice($titre_ppal_200, $titre_perio_530a, $champ_210, get_infos_notices_liees($notices_liees,'461','parent','b'));
				break;
			case 'a2' :
				//Lien(s) vers Article
				$tab_perio = get_infos_notices_liees($notices_liees,'461','perio');
				$tab_bull = get_infos_notices_liees($notices_liees,'463','bull');
				$tab_field = array("id_base"=>$id_unimarc, "titre"=> $titre_ppal_200);
				/*print "xxxxxxxxxxxxxxxxxxxxxxxx".$notice_id;
				print "<pre>";
				print_r($tab_bull);
				print_r($tab_perio);
				print_r($tab_field);
				print "</pre><br />";*/
				creer_liens_pour_articles($tab_bull,$tab_perio, $tab_field);
				break;
			default:
				break;
		}
	}

	//Traitement des relations mères/filles
	$parents = get_infos_notices_liees($notices_liees,'','parent');
	if(count($parents)){
		foreach($parents as $key=>$parent){
			//Si pas de titre, on évite de créer une notice sans titre
			if(!isset($parent["t"])){
				unset($parents[$key]);
			}
		}
		//Reste-t-il des relations à créer après nettoyage ci-dessus ?
		if(count($parents)){
			creer_relation_notice($parents);
		}
	}

	$enfants = get_infos_notices_liees($notices_liees,'','child');
	if(count($enfants)){
		foreach($enfants as $key=>$enfant){
			//Si pas de titre, on évite de créer une notice sans titre
			if(!isset($enfant["t"])){
				unset($enfants[$key]);
			}
		}
		//Reste-t-il des relations à créer après nettoyage ci-dessus ?
		if(count($enfants)){
			creer_relation_notice($enfants);
		}
	}

	//Traitement des relations horizontales
	$pairs = get_infos_notices_liees($notices_liees,'','pair');
	if(count($pairs)){
		foreach($pairs as $key=>$pair){
			//Si pas de titre, on évite de créer une notice sans titre
			if(!isset($pair["t"])){
				unset($pairs[$key]);
			}
		}
		//Reste-t-il des relations à créer après nettoyage ci-dessus ?
		if(count($pairs)){
			creer_relation_notice($pairs);
		}
	}

	//On traite les notices qui ont été mises en attente de création
	if (!empty($notices_a_creer[$id_unimarc])) {
	    $nb_notices_a_creer = count($notices_a_creer[$id_unimarc]);
	    for ($i = 0; $i < $nb_notices_a_creer; $i++) {
			if($notices_a_creer[$id_unimarc][$i]['lnk'] == 'parent'){
				//on a une relation vers un parent
				if (!notice_relations::relation_exists($notices_a_creer[$id_unimarc][$i]['id_asso'], $notice_id, $notices_a_creer[$id_unimarc][$i]['type_lnk'])) {
					notice_relations::insert_from_import($notices_a_creer[$id_unimarc][$i]['id_asso'], $notice_id, $notices_a_creer[$id_unimarc][$i]['type_lnk'], $notices_a_creer[$id_unimarc][$i]['rank']);
				}
			} elseif($notices_a_creer[$id_unimarc][$i]['lnk'] == 'child'){
				//on a une relation vers un enfant
				if (!notice_relations::relation_exists($notice_id, $notices_a_creer[$id_unimarc][$i]['id_asso'], $notices_a_creer[$id_unimarc][$i]['type_lnk'])) {
					notice_relations::insert_from_import($notice_id, $notices_a_creer[$id_unimarc][$i]['id_asso'], $notices_a_creer[$id_unimarc][$i]['type_lnk'], $notices_a_creer[$id_unimarc][$i]['rank']);
				}
			} elseif($notices_a_creer[$id_unimarc][$i]['lnk'] == 'pair'){
				//on a une relation vers une notice horizontale
				if (!notice_relations::relation_exists($notice_id, $notices_a_creer[$id_unimarc][$i]['id_asso'], $notices_a_creer[$id_unimarc][$i]['type_lnk'])) {
					notice_relations::insert_from_import($notice_id, $notices_a_creer[$id_unimarc][$i]['id_asso'], $notices_a_creer[$id_unimarc][$i]['type_lnk'], $notices_a_creer[$id_unimarc][$i]['rank']);
				}
			} elseif($notices_a_creer[$id_unimarc][$i]['lnk'] == 'art'){
				//on a un lien d'un article vers un periodique
				if(!$notices_crees[$id_unimarc] ){//On ne peut pas passer par la
					$req_insert_art = "insert into notices (tit1, niveau_biblio, niveau_hierar, npages)
												values( '".addslashes($notices_a_creer[$id_unimarc][$i]['titre_art'])."', 'a', '2','".addslashes($notices_a_creer[$id_unimarc][$i]['page'])."' )";
					pmb_mysql_query($req_insert_art);
					$id_art = pmb_mysql_insert_id();
					audit::insert_creation(AUDIT_NOTICE,$id_art);
					//calcul des droits d'accès s'ils sont activés
					notice::calc_access_rights($id_art);
					$notices_crees[$id_unimarc] = $id_art;
				}
				$id_perio=$notices_a_creer[$id_unimarc][$i]['id_asso'];
				$bulletin=array();
				$bulletin=array("titre"=>$notices_a_creer[$id_unimarc][$i]['titre'],"date"=>$notices_a_creer[$id_unimarc][$i]['date'],"mention"=>$notices_a_creer[$id_unimarc][$i]['mention'],"num"=>$notices_a_creer[$id_unimarc][$i]['num']);
				creer_notice_article("","","",$id_art?$id_art:$notice_id,$bulletin,"","",$id_perio);
			} elseif($notices_a_creer[$id_unimarc][$i]['lnk'] == 'perio'){
				//On a un lien d'un periodique vers un article
				if(!$notices_crees[$id_unimarc]){//On ne peut pas passer par la
					$req_insert_perio = "insert into notices (tit1, code, niveau_biblio, niveau_hierar)
												values( '".addslashes($notices_a_creer[$id_unimarc][$i]['titre_perio'])."', '".addslashes($notices_a_creer[$id_unimarc][$i]['code']).", 's', '1' )";
					pmb_mysql_query($req_insert_perio);
					$id_perio = pmb_mysql_insert_id();
					audit::insert_creation(AUDIT_NOTICE,$id_perio);
					//calcul des droits d'accès s'ils sont activés
					notice::calc_access_rights($id_perio);
					$notices_crees[$id_unimarc] = $id_perio;
				}else{
					$id_perio=$notices_crees[$id_unimarc];
				}
				$bulletin=array();
				$bulletin=array("titre"=>$notices_a_creer[$id_unimarc][$i]['titre_bull'],"date"=>$notices_a_creer[$id_unimarc][$i]['date'],"mention"=>$notices_a_creer[$id_unimarc][$i]['mention'],"num"=>$notices_a_creer[$id_unimarc][$i]['num']);
				creer_notice_article("","","",$notices_a_creer[$id_unimarc][$i]['id_asso'],$bulletin,"","",$id_perio);
			}
		}
		unset($notices_a_creer[$id_unimarc]);
	}
	//On rattache le perio a ses notices de bulletin
	if (!empty($bulletins_a_creer[$id_unimarc])) {
		$id_perio = $notice_id;
		$nb_bulletins_a_creer = count($bulletins_a_creer[$id_unimarc]);
		for ($i = 0; $i < $nb_bulletins_a_creer; $i++) {
			//Si on décomante la suite et qu on commente la fin ya un bulletin de créé qui ne doit pas exister (test avec export tt lien)
			$bulletin = array();
			$bulletin = array("titre" => $bulletins_a_creer[$id_unimarc][$i]['titre'], "date" => $bulletins_a_creer[$id_unimarc][$i]['date_date'], "mention" => $bulletins_a_creer[$id_unimarc][$i]['mention'], "num" => $bulletins_a_creer[$id_unimarc][$i]['bull_num']);
			$id_bulletin = creer_bulletin($id_perio, $bulletin, "", "");
			creer_lien_notice_bulletin("", $id_perio, $id_bulletin, $bulletins_a_creer[$id_unimarc][$i]['bull_notice'], "", $bulletin);
		}
		unset($bulletins_a_creer[$id_unimarc]);
	}
}
/*
 * Récupération de sous tableaux correspondant aux critères
 */
function get_infos_notices_liees($notices_liees=array(), $cle_uni='', $lien='', $type_lien=''){
	$result_tab = array();
	$link_tab = array();
	$type_link_tab = array();
	if($cle_uni){
		$result_tab = $notices_liees[$cle_uni];
		if(!empty($lien) && $result_tab){
			foreach($result_tab as $field){
				//on récupère toutes les options du $9 dans un tableau
			   $options=array();
			   if (!empty($field['9'])) {
    			   for($i=0;$i<count($field['9']);$i++){
    					$chaine_parse = explode(':',$field['9'][$i]);
    					if($chaine_parse[0] == 'lnk'){
    						$options["lien"] = $chaine_parse[1];
    					}
    				}
			   }
			   if(isset($options["lien"]) && $options["lien"] == $lien) {
			       $link_tab[] = $field;
			   }
			}
			$result_tab = $link_tab;
			if(!empty($type_lien)){
				foreach($result_tab as $field){
					//on récupère toutes les options du $9 dans un tableau
				   $options=array();
				   for($i=0;$i<count($field['9']);$i++){
						$chaine_parse = explode(':',$field['9'][$i]);
						if($chaine_parse[0] == 'type_lnk'){
							$options["type_lnk"] = $chaine_parse[1];
						}
					}
				    if($options["type_lnk"] == $type_lien) $type_link_tab[] = $field;
				} $result_tab = $type_link_tab;
			}
		}
	} elseif(!empty($lien)){
		foreach($notices_liees as $fields){
			foreach($fields as $field){
				//on récupère toutes les options du $9 dans un tableau
			   $options=array();
                if(is_array($field['9'])) {
    			   for($i=0;$i<count($field['9']);$i++){
    					$chaine_parse = explode(':',$field['9'][$i]);
    					if($chaine_parse[0] == 'lnk'){
    						$options["lien"] = $chaine_parse[1];
    					}
                    }
                }
                if($options["lien"] == $lien) $link_tab[] = $field;
			}
	   	$result_tab = $link_tab;
		}
		if($type_lien && $result_tab){
			foreach($result_tab as $field){
				//on récupère toutes les options du $9 dans un tableau
			   $options=array();
			   for($i=0;$i<count($field['9']);$i++){
					$chaine_parse = explode(':',$field['9'][$i]);
					if($chaine_parse[0] == 'type_lnk'){
						$options["type_lnk"] = $chaine_parse[1];
					}
				}
			    if($options["type_lnk"] == $type_lien) $type_link_tab[] = $field;
			}
			$result_tab = $type_link_tab;
		}
	} elseif(!empty($type_lien)){
		foreach($notices_liees as $fields){
			foreach($fields as $field){
				//on récupère toutes les options du $9 dans un tableau
			   $options=array();
			   for($i=0;$i<count($field['9']);$i++){
					$chaine_parse = explode(':',$field['9'][$i]);
					if($chaine_parse[0] == 'type_lnk'){
						$options["type_lnk"] = $chaine_parse[1];
					}
				}
			    if($options["type_lnk"] == $type_lien) $type_link_tab[] = $field;
			}
	   	$result_tab = $type_link_tab;
		}
	}
	return $result_tab;
}
/*
 *  Fonction qui génère les liens pour les notices de bulletin
 */
function creer_liens_pour_bull_notice($titre200=array(), $titre530=array(), $champ210=array(), $tab_perio=array()){
	global $notice_id, $notices_crees, $id_unimarc, $bulletins_a_creer, $bulletins_crees;
	if(!$tab_perio){
		if(!$notices_crees[$id_unimarc]){//On passe ici si on importe les liens pour une notice de bulletin qui n'a ni numéro ni lien vers un periodique (très improbable)
			//On passe la notice en monographie
			$requete="update notices set niveau_biblio='m', niveau_hierar='0' where notice_id='".$notice_id."'";
			pmb_mysql_query($requete);

		}else{
			if(!trim($titre530[0]))$titre530[0]="Sans titre";
			$id_perio=creer_notice_periodique(0,trim($titre530[0]),"");
			$bulletin=array();
			$bulletin=array("titre"=>trim($titre200[0]['i']),"date"=>trim($champ210[0]['h']),"mention"=>trim($champ210[0]['d']),"num"=>trim($titre200[0]['h']));
			$id_bull=creer_bulletin($id_perio,$bulletin);
			creer_lien_notice_bulletin($id_unimarc,$id_perio,$id_bull,$notice_id,"",$bulletin);
		}
	} else {
		if(!$notices_crees[get_valeur_champ9($tab_perio[0]['9'],'id')] && !$tab_perio[0]['0']['0']){
			//On créé le periodique car il n'est pas créé et n'est pas à créé
			if(!trim($titre530[0]))$titre530[0]="Sans titre";
			if($tmp=get_valeur_champ9($tab_perio[0]['9'],'id')){
				$id_perio=creer_notice_periodique($tmp,trim($titre530[0]),$tab_perio[0]['x']['0']);
			}else{
				$id_perio=creer_notice_periodique(0,trim($titre530[0]),$tab_perio[0]['x']['0']);
			}
			$bulletin=array();
			$bulletin=array("titre"=>trim($titre200[0]['i']),"date"=>trim($champ210[0]['h']),"mention"=>trim($champ210[0]['d']),"num"=>trim($titre200[0]['h']));
			$id_bull=creer_bulletin($id_perio,$bulletin);
			creer_lien_notice_bulletin($id_unimarc,$id_perio,$id_bull,$notice_id,"",$bulletin);
		}elseif($notices_crees[get_valeur_champ9($tab_perio[0]['9'],'id')]){
			//La notice de perio est déja créé
			$id_perio=$notices_crees[get_valeur_champ9($tab_perio[0]['9'],'id')];
			$bulletin=array();
			$bulletin=array("titre"=>trim($titre200[0]['i']),"date"=>trim($champ210[0]['h']),"mention"=>trim($champ210[0]['d']),"num"=>trim($titre200[0]['h']));
			$id_bull=creer_bulletin($id_perio,$bulletin);
			creer_lien_notice_bulletin($id_unimarc,$id_perio,$id_bull,$notice_id,"",$bulletin);
		}elseif($tab_perio[0]['0']['0']){
			//Le lien sera a refaire plus tard
			$bulletins_a_creer[$tab_perio[0]['0'][0]][] = array ("bull_notice"=>$notices_crees[$id_unimarc], "bull_num"=>$titre200[0]['h'], "date_date"=>$champ210[0]['h'],"mention"=>$champ210[0]['d'],"titre"=>$titre200[0]['i']);
		}else{
			//Si j'ai un bulletin avec un 461 qui ne rentre pas dans les autres cas je passe le bulletin en monographie (cas très peu probable)
			$requete="select bulletin_id from bulletins where num_notice='".addslashes($notice_id)."' ";
			$res = pmb_mysql_query($requete);
			if (!pmb_mysql_num_rows($res))  {
				//Si il n'est pas déja relié je le passe en monographie sinon je n'y touche pas
				$requete="update notices set niveau_biblio='m', niveau_hierar='0' where notice_id='".$notice_id."' ";
				pmb_mysql_query($requete);
			}
		}
	}
}
/*
 *  Génère la création des liens pour les articles
 */
function creer_liens_pour_articles($tab_bull=array(),$tab_perio=array(), $tab_field=array()){
	global $notice_id, $notices_crees, $bulletins_crees, $notices_a_creer,$serie,$champ_210;
		if(!$tab_bull && !$tab_perio){
			//On regarde si on a les informations de bulletinage dans le 461
			if(trim($serie[0]["t"]) and trim($serie[0]["v"])){
				//J'ai les informations pour recréer le bulletinage
				$bulletin=array();
				$bulletin=array("titre"=>"Bulletin N°".trim($serie[0]["v"]),"date"=>"","mention"=>trim($champ_210[0]['d']),"num"=>trim($serie[0]["v"]));
				creer_notice_article("","","",$notice_id,$bulletin,trim($serie[0]["t"]),"",0);
			}else{
				//Si elle a été créée mais qu'elle n'est pas a créer
				if( !$tab_field['id_base'] || ($notices_crees[$tab_field['id_base']] && !$notices_a_creer[$tab_field['id_base']])){
					//On regarde si elle n'est pas reliée a un bulletin
					$requete="select analysis_bulletin from analysis where analysis_notice='".addslashes($notices_crees[$tab_field['id_base']])."' ";
					$res=pmb_mysql_query($requete);
					if(!pmb_mysql_num_rows($res)){
						//Si elle n'est pas reliée on la passe en monographie, sinon c'est bien un article
						$requete="update notices set niveau_biblio='m', niveau_hierar='0' where notice_id='".addslashes($notice_id)."' ";
						pmb_mysql_query($requete);
					}
				}elseif(!$notices_crees[$tab_field['id_base']] && !$notices_a_creer[$tab_field['id_base']]){
					//on ne doit pas passer par là
					$req_insert_art = "insert into notices (tit1, niveau_biblio, niveau_hierar)
												values( '".addslashes($tab_field['titre'][0]['a'])."', 'm', '0' )";
					pmb_mysql_query($req_insert_art);
					$notices_crees[$tab_field['id_base']] = pmb_mysql_insert_id();
					audit::insert_creation(AUDIT_NOTICE,$notices_crees[$tab_field['id_base']]);
					//calcul des droits d'accès s'ils sont activés
					notice::calc_access_rights($notices_crees[$tab_field['id_base']]);
				}
			}
		} elseif($tab_bull && !$tab_perio){
			//On créé un periodique sans titre (On regarde avant si on en a pas déja créé une)
			$bulletin=array();
			$bulletin=array("titre"=>$tab_bull[0]['v'][0],"date"=>$tab_bull[0]['d'][0],"mention"=>$tab_bull[0]['e'][0],"num"=>$tab_bull[0]['v'][0]);
			creer_notice_article("","","",$notice_id,$bulletin,"Sans titre","",0);
		} elseif(!$tab_bull && $tab_perio){
			//On créé un bulletin générique pour rattacher les articles au pério
			if(!$notices_crees[get_valeur_champ9($tab_perio[0]['9'],'id')] && !$tab_perio[0]['0'][0]){// Si le periodique n'est pas déja créé et si il ne sera pas a créer
				$id_perio=creer_notice_periodique(get_valeur_champ9($tab_perio[0]['9'],'id'),$tab_perio[0]['t'][0],$tab_perio[0]['x'][0]);
				$bulletin=array();
				$bulletin=array("titre"=>"bull_générique","date"=>"0000-00-00","mention"=>"00/00/0000","num"=>"0");
				creer_notice_article("","","",$notice_id,$bulletin,"","",$id_perio);
			}elseif($notices_crees[get_valeur_champ9($tab_perio[0]['9'],'id')]){//Si il est déja créé
				$id_perio=$notices_crees[get_valeur_champ9($tab_perio[0]['9'],'id')];
				$bulletin=array();
				$bulletin=array("titre"=>"bull_générique","date"=>"0000-00-00","mention"=>"00/00/0000","num"=>"0");
				creer_notice_article("","","",$notice_id,$bulletin,"","",$id_perio);
			}else{
				// Les liens seront à creer plus tard pour cet article
				$id=get_valeur_champ9($tab_perio[0]['9'],'id');
				$type_lien =get_valeur_champ9($tab_perio[0]['9'],'type_lnk');
				$lien=get_valeur_champ9($tab_perio[0]['9'],'lnk');
				$rank = intval(get_valeur_champ9($tab_perio[0]['9'],'rank'));
				$notices_a_creer[$id][] = array( "type_lnk"=> $type_lien, "lnk"=> $lien, "rank"=>$rank, "titre_perio"=>$tab_perio[0]['t'][0],
										"code"=>$tab_perio[0]['x'][0], "id_asso"=>$notice_id, "num"=>"0",
										"mention"=>"00/00/0000","date"=>"0000-00-00","titre_bull"=>"bull_générique");
			}
		} else {
			for($i=0;$i<count($tab_perio);$i++){
				if(!$notices_crees[get_valeur_champ9($tab_perio[$i]['9'],'id')] && !$tab_perio[$i]['0'][0]){
					//On a les deux liens, on regarde si le perio existe déjà dans la base
					$id_perio=creer_notice_periodique(get_valeur_champ9($tab_perio[$i]['9'],'id'),$tab_perio[$i]['t'][0],$tab_perio[$i]['x'][0]);
					$bulletin=array();
					$bulletin=array("titre"=>$tab_bull[$i]['t'][0],"date"=>$tab_bull[$i]['d'][0],"mention"=>$tab_bull[$i]['e'][0],"num"=>$tab_bull[$i]['v'][0]);
					creer_notice_article("","","",$notice_id,$bulletin,"","",$id_perio);
				} else{
					if($notices_crees[get_valeur_champ9($tab_perio[$i]['9'],'id')]){
						//Si il est créé on récupère son identifiant
						$id_perio=	$notices_crees[get_valeur_champ9($tab_perio[$i]['9'],'id')];
						//On regarde si le bulletin est déja créé
						$bulletin=array();
						$bulletin=array("titre"=>$tab_bull[$i]['t'][0],"date"=>$tab_bull[$i]['d'][0],"mention"=>$tab_bull[$i]['e'][0],"num"=>$tab_bull[$i]['v'][0]);
						creer_notice_article("","","",$notice_id,$bulletin,"","",$id_perio);
					} else {
						$id=get_valeur_champ9($tab_perio[$i]['9'],'id');
						$type_lien =get_valeur_champ9($tab_perio[$i]['9'],'type_lnk');
						$lien=get_valeur_champ9($tab_perio[$i]['9'],'lnk');
						$rank = intval(get_valeur_champ9($tab_perio[$i]['9'],'rank'));
						$notices_a_creer[$id][] = array( "type_lnk"=> $type_lien, "lnk"=> $lien, "rank"=>$rank, "titre_perio"=>$tab_perio[$i]['t'][0],
												"code"=>$tab_perio[$i]['x'][0], "id_asso"=>$notice_id, "num"=>$tab_bull[$i]['v'][0],
												"mention"=>$tab_bull[$i]['e'][0],"date"=>$tab_bull[$i]['d'][0],"titre_bull"=>$tab_bull[$i]['t'][0]);
					}
				}
			}
		}
}

/*
 * Fonction qui génère la création du bulletinage et les articles pour les périos
 */
function creer_bulletinage_et_articles($bull=array(), $art=array()){
	global $notice_id, $notices_a_creer, $bulletins_crees, $notices_crees,$tit_200a,$isbn_OK,$id_unimarc,$msg;
	global $force_creation_notice_perio;
	//On regarde si la notice n'existe pas déjà dans la base
	$requete="select notice_id from notices where tit1 LIKE '".addslashes(clean_string(implode (" ; ",$tit_200a)))."' and niveau_biblio='s' and niveau_hierar='1' and notice_id !='".addslashes($notice_id)."' ";
	if($isbn_OK) $requete.= "and code = '".addslashes($isbn_OK)."'";
	$res=pmb_mysql_query($requete);
	if((!isset($force_creation_notice_perio) || !$force_creation_notice_perio) && pmb_mysql_num_rows($res)){
		$id_perio_garde=0;
		while (($r=pmb_mysql_fetch_object($res)) && !$id_perio_garde) {
			if(!array_search($r->notice_id,$notices_crees)){
				//Si le periodique ne fait pas parti des notices créées (il était déja dans la base)
				$id_perio_garde=$r->notice_id;
			}
		}
		if($id_perio_garde){
			pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[542]." $id_unimarc "." $isbn_OK ".addslashes(clean_string(implode (" ; ",$tit_200a)))."') ") ;
			//Si j'ai déja une notice dans la base avec ce titre et ce code je supprime celle que je suis en train d'importer
			$perio_traite = new serial($notice_id);
			$perio_traite->replace($id_perio_garde);
			$perio_traite->serial_delete();
			//Je travail avec le periodique qui était dans la base
			$notice_id=$id_perio_garde;
			$notices_crees[$id_unimarc]=$id_perio_garde;
		}
	}
	if($bull){
		for($i=0;$i<count($bull);$i++){
			$bulletin=array();
			$bulletin=array("titre"=>$bull[$i]['t'][0],"date"=>$bull[$i]['d'][0],"mention"=>$bull[$i]['e'][0],"num"=>$bull[$i]['v'][0]);
			creer_bulletin($notice_id,$bulletin,"","");
		}
	}
	if($art){
		for($i=0;$i<count($art);$i++){
			if(!$notices_crees[get_valeur_champ9($art[$i]['9'],'id')] && !$art[$i]['0'][0]){
				$bulletin=array();
				$bulletin=array("titre"=>$art[$i]['t'][1],"date"=>$art[$i]['d'][0],"mention"=>$art[$i]['e'][0],"num"=>$art[$i]['v'][0]);
				creer_notice_article(get_valeur_champ9($art[$i]['9'],'id'),$art[$i]['t'][0],get_valeur_champ9($art[$i]['9'],'page'),0,$bulletin,"","",$notice_id);
			}elseif($art[$i]['0'][0]){
				$id=get_valeur_champ9($art[$i]['9'],'id');
				$type_lien =get_valeur_champ9($art[$i]['9'],'type_lnk');
				$lien=get_valeur_champ9($art[$i]['9'],'lnk');
				$rank = intval(get_valeur_champ9($art[$i]['9'],'rank'));
				$page=get_valeur_champ9($art[$i]['9'],'page');
				//On enregistre les informations pour créer l'article plus tard
				$notices_a_creer[$id][] = array( "type_lnk"=> $type_lien, "lnk"=> $lien, "rank"=>$rank, "titre_art"=>$art[$i]['t'][0], "titre"=>$art[$i]['t'][1], "num"=>$art[$i]['v'][0], "mention"=>$art[$i]['e'][0], "date"=>$art[$i]['d'][0], "id_asso"=>$notice_id, "page"=>$page);
			}
		}
	}
}

/*
 * Récupère la valeur du champ $9 en fonction du critère
 */
function get_valeur_champ9($champ9=array(),$crit=''){
	$options=array();
	$options[$crit]='';
   for($i=0;$i<count($champ9);$i++){
		$chaine_parse = explode(':',$champ9[$i]);
		if($chaine_parse[0] == $crit) {
			$options[$crit] = $chaine_parse[1];
		}
	}
	return $options[$crit];
}

/*
 * Créer les relations entre notice
 */
function creer_relation_notice($notice_liee=array()){
	global $notice_id,$notices_crees;
	foreach ( $notice_liee as $key => $value ) {
		$id=get_valeur_champ9($value['9'],'id');
		$type_lien =get_valeur_champ9($value['9'],'type_lnk');
		$lien=get_valeur_champ9($value['9'],'lnk');
		$rank = intval(get_valeur_champ9($value['9'],'rank'));
		$id_mere=0;
		$id_fille=0;
		$id_notice_liee=0;
		$ancien_id=get_valeur_champ9($value['9'],'id');
		if($notices_crees[$ancien_id]){
			//Si la notice lié est créé
			$id_notice_liee=$notices_crees[$ancien_id];
		}elseif($value['0'][0]){
			//Le lien sera a creer plus tard
			$notices_a_creer[$ancien_id][] = array( "type_lnk"=> $type_lien, "lnk"=> $lien, "rank"=>$rank, "id_asso"=>$notice_id);
		}else{
			//Il faut la créer
			$niveau_bilio=get_valeur_champ9($value['9'],'bl');
			switch ( $niveau_bilio ) {
				case 'm0':
					$id_notice_liee=creer_notice_monographie($ancien_id,$value['t'][0],$value['y'][0]);
					break;
				case 's1':
					$id_notice_liee=creer_notice_periodique($ancien_id,$value['t'][0],$value['x'][0]);
					break;
				case 'a2':
					$bulletin=array("titre"=>$value['t'][1],"date"=>$value['d'][0],"mention"=>$value['e'][0],"num"=>$value['v'][0]);
					$id_notice_liee=creer_notice_article($ancien_id,$value['t'][0],"","",$bulletin,$value['t'][2],$value['x'][0],"");
					break;
				case 'b2':
					$bulletin=array("titre"=>$value['t'][1],"date"=>$value['d'][0],"mention"=>$value['e'][0],"num"=>$value['v'][0]);
					$id_notice_liee=creer_notice_bulletin($ancien_id,$value['t'][0],$bulletin,$value['t'][2],$value['x'][0]);
					break;
			}
		}

		if($lien == 'child'){
			$direction = 'down';
		}elseif($lien == 'parent'){
			$direction = 'up';
		}elseif($lien == 'pair'){
			$direction = 'both';
		}

		if($notice_id && $id_notice_liee && ($notice_id != $id_notice_liee)){
			if (!notice_relations::relation_exists($notice_id, $id_notice_liee, $type_lien, $direction)) {
				notice_relations::insert_from_import($notice_id, $id_notice_liee, $type_lien, $rank, $direction);
			}
		}
	}
}
/*
 * Créer les notices de monographie
 */
function creer_notice_monographie($ancien_id=0,$titre="",$code=""){
	global $notices_crees,$statutnot;
	global $isbn_dedoublonnage,$isbn_only;
	if((($isbn_dedoublonnage)&&(!$isbn_only))||(($isbn_dedoublonnage)&&($isbn_only)&&(isISBN($code)))){// Si dédoublonnage sur ISBN activé
		$requete="SELECT notice_id FROM notices WHERE niveau_biblio='m' and niveau_hierar='0' AND code='".addslashes($code)."' ";
		$res=pmb_mysql_query($requete);
		if($res && (pmb_mysql_num_rows($res) == 1)){//J'ai déjà une notice dans la base avec ce code barre
			$id=pmb_mysql_result($res,0,0);
			if($ancien_id){
				$notices_crees[$ancien_id]=$id;
			}
			return $id;
		}
	}

	$requete="insert into notices (tit1, code, niveau_biblio, niveau_hierar,statut) values ('".addslashes($titre)."','".addslashes($code)."', 'm', '0','".$statutnot."')";
	pmb_mysql_query($requete);
	$id=pmb_mysql_insert_id();
	audit::insert_creation(AUDIT_NOTICE,$id);
	// Mise à jour de tous les index de la notice
	notice::majNoticesTotal($id);
	if($ancien_id){
		$notices_crees[$ancien_id]=$id;
	}
	return $id;
}
/*
 * Créer les notices de periodique
 */
function creer_notice_periodique($ancien_id=0,$titre="",$code=""){
	global $notices_crees,$force_creation_notice_perio,$statutnot;
	//On regarde si il existe
	if($ancien_id){
		if($notices_crees[$ancien_id]){
			$id_perio=$notices_crees[$ancien_id];
		}else{
			$requete="select notice_id from notices where tit1 LIKE '".addslashes(clean_string($titre))."' and niveau_biblio='s' and niveau_hierar='1'";
			if($code) $requete.=" and code='".addslashes($code)."'";
			$res_perio = pmb_mysql_query($requete);
			if ((!isset($force_creation_notice_perio) || !$force_creation_notice_perio) && pmb_mysql_num_rows($res_perio))  {
				$id_perio = pmb_mysql_result($res_perio,0,0);
			}else{
				$requete="insert into notices (tit1,code, niveau_biblio, niveau_hierar,statut) values('".addslashes(clean_string($titre))."','".addslashes($code)."', 's', '1','".$statutnot."')";
				pmb_mysql_query($requete);
				$id_perio = pmb_mysql_insert_id();
				audit::insert_creation(AUDIT_NOTICE,$id_perio);
				// Mise à jour de tous les index de la notice
				notice::majNoticesTotal($id_perio);
			}
			$notices_crees[$ancien_id]=$id_perio;
		}
	}else{
		$requete="select notice_id from notices where tit1 LIKE '".addslashes(clean_string($titre))."' and niveau_biblio='s' and niveau_hierar='1'";
		if($code) $requete.=" and code='".addslashes($code)."'";
		$res_perio = pmb_mysql_query($requete);
		if ((!isset($force_creation_notice_perio) || !$force_creation_notice_perio) && pmb_mysql_num_rows($res_perio))  {
			$id_perio = pmb_mysql_result($res_perio,0,0);
		}else{
			$requete="insert into notices (tit1,code, niveau_biblio, niveau_hierar,statut) values('".addslashes(clean_string($titre))."','".addslashes($code)."', 's', '1','".$statutnot."')";
			pmb_mysql_query($requete);
			$id_perio = pmb_mysql_insert_id();
			audit::insert_creation(AUDIT_NOTICE,$id_perio);
			// Mise à jour de tous les index de la notice
			notice::majNoticesTotal($id_perio);
		}
	}
	return $id_perio;
}

/*
 * Créer les bulletins
 * Bulletin est un tableau avec les clés : titre, date, mention, num
 */
function creer_bulletin($id_perio=0,$bulletin=array(),$titre_perio="",$code_perio=""){
	global $bulletins_crees;

	if(!$id_perio){
		//Si je n'ai pas d'identifiant de periodique je vais en chercher un ou le créer
		$id_perio=creer_notice_periodique(0,$titre_perio,$code_perio);
	}

	if(!$bulletins_crees[$id_perio][$bulletin["num"]][$bulletin["date"].$bulletin["mention"]]){
		//Si il n'est pas déja créé, on regarde si le bulletin est présent dans la base avant de le créer
		$requete="select bulletin_id from bulletins where bulletin_notice='".addslashes($id_perio)."' and bulletin_numero='".addslashes($bulletin["num"])."' and mention_date='".addslashes($bulletin["mention"])."'";
		if($bulletin["date"])$requete.=" and date_date='".addslashes($bulletin["date"])."' ";
		$res=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)){
			$id_bull = pmb_mysql_result($res,0,0);
		}else{
			$requete_bulletin = "insert into bulletins (bulletin_numero, bulletin_notice, mention_date, date_date, bulletin_titre) values ('".addslashes($bulletin["num"])."', '".addslashes($id_perio)."', '".addslashes($bulletin["mention"])."', '".addslashes($bulletin["date"])."', '".addslashes($bulletin["titre"])."')";
			pmb_mysql_query($requete_bulletin);
			$id_bull = pmb_mysql_insert_id();
			audit::insert_creation(AUDIT_BULLETIN,$id_bull);
		}
		$bulletins_crees[$id_perio][$bulletin["num"]][$bulletin["date"].$bulletin["mention"]] = $id_bull;
	} else {
		$id_bull = $bulletins_crees[$id_perio][$bulletin["num"]][$bulletin["date"].$bulletin["mention"]];
	}
	return $id_bull;
}

/*
 * Créer les notices d'article
 * Bulletin est un tableau avec les clés : titre, date, mention, num
 */
function creer_notice_article($ancien_id=0,$titre_article="",$npage_article="",$id_article=0,$bulletin=array(),$titre_perio="",$code_perio="",$id_perio=0){
	global $notices_crees,$msg,$statutnot;
	if($ancien_id){
		if($notices_crees[$ancien_id]){
			$id_article=$notices_crees[$ancien_id];
		}else{
			//On va chercher le bulletin
			if($id_perio){
				$id_bulletin=creer_bulletin($id_perio,$bulletin,$titre_perio,$code_perio);
			}else{
				$id_bulletin=creer_bulletin(0,$bulletin,$titre_perio,$code_perio);
			}
			//On créer l'article
			if(!$id_article){
				$requete="insert into notices (tit1, npages, niveau_biblio, niveau_hierar,statut) values ('".addslashes(clean_string($titre_article))."','".addslashes($npage_article)."', 'a', '2','".$statutnot."')";
				pmb_mysql_query($requete);
				$id_article=pmb_mysql_insert_id();
				audit::insert_creation(AUDIT_NOTICE,$id_article);
				//calcul des droits d'accès s'ils sont activés
				notice::calc_access_rights($id_article);
				// Mise à jour de tous les index de la notice
				notice::majNoticesTotal($id_article);
			}
			//Je regarde si je n'ai pas un autre article avec ce titre
			$requete="SELECT old.notice_id,old.tit1 FROM notices new, notices old JOIN analysis ON analysis_notice=old.notice_id WHERE new.notice_id='".addslashes($id_article)."' AND new.notice_id!=old.notice_id AND analysis_bulletin='".addslashes($id_bulletin)."' AND new.tit1=old.tit1 ";
			$res_doubl=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res_doubl)){
				notice::del_notice($id_article);
				pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[542]." $id_unimarc "." $isbn_OK ".addslashes(pmb_mysql_result($res_doubl,0,1))."') ") ;
				$id_article=pmb_mysql_result($res_doubl,0,0);//A voir pr modif
			}else{
				$requete="SELECT notice_id FROM notices WHERE notice_id='".addslashes($id_article)."'";
				$res_art=pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($res_art)){
					//On créer le lien entre le bulletin et l'article
					$requete="insert into analysis (analysis_bulletin, analysis_notice) values ( '".addslashes($id_bulletin)."', '".addslashes($id_article)."' )";
					pmb_mysql_query($requete);
				}
			}
			$notices_crees[$ancien_id]=$id_article;
		}
	}else{
		//On va chercher le bulletin
		if($id_perio){
			$id_bulletin=creer_bulletin($id_perio,$bulletin,$titre_perio,$code_perio);
		}else{
			$id_bulletin=creer_bulletin(0,$bulletin,$titre_perio,$code_perio);
		}
		//On créer l'article
		if(!$id_article){
			$requete="insert into notices (tit1, npages, niveau_biblio, niveau_hierar,statut) values ('".addslashes(clean_string($titre_article))."','".addslashes($npage_article)."', 'a', '2','".$statutnot."')";
			pmb_mysql_query($requete);
			$id_article=pmb_mysql_insert_id();
			audit::insert_creation(AUDIT_NOTICE,$id_article);
			//calcul des droits d'accès s'ils sont activés
			notice::calc_access_rights($id_article);
			// Mise à jour de tous les index de la notice
			notice::majNoticesTotal($id_article);
		}
		//Je regarde si je n'ai pas un autre article avec ce titre
		$requete="SELECT old.notice_id,old.tit1 FROM notices new, notices old JOIN analysis ON analysis_notice=old.notice_id WHERE new.notice_id='".addslashes($id_article)."' AND new.notice_id!=old.notice_id  AND analysis_bulletin='".addslashes($id_bulletin)."' AND new.tit1=old.tit1 ";
		$res_doubl=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res_doubl)){
			notice::del_notice($id_article);
			pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[542]." $id_unimarc "." $isbn_OK ".addslashes(pmb_mysql_result($res_doubl,0,1))."') ") ;
			$id_article=pmb_mysql_result($res_doubl,0,0);//A voir pr modif
		}else{
			$requete="SELECT notice_id FROM notices WHERE notice_id='".addslashes($id_article)."'";
			$res_art=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res_art)){
				//On créer le lien entre le bulletin et l'article
				$requete="insert into analysis (analysis_bulletin, analysis_notice) values ( '".addslashes($id_bulletin)."', '".addslashes($id_article)."' )";
				pmb_mysql_query($requete);
			}
		}
	}
	if($id_article && ($bulletin["date"])){
		$requete="UPDATE notices SET year='".addslashes(substr($bulletin["date"],0,4))."', date_parution='".addslashes($bulletin["date"])."' WHERE notice_id='".$id_article."'";
		pmb_mysql_query($requete);
	}
	return $id_article;
}

/*
 * Créer les notices de bulletin
 * Bulletin est un tableau avec les clés : titre, date, mention, num
 */
function creer_notice_bulletin($ancien_id=0,$titre_notice_bulletin="",$bulletin=array(),$titre_perio="",$code_perio=""){
	global $notices_crees;
	if($ancien_id){
		if($notices_crees[$ancien_id]){
			$id_notice_bulletin=$notices_crees[$ancien_id];
		}else{
			//On va chercher le bulletin
			$id_perio=creer_notice_periodique(0,$titre_perio,$code_perio);
			$id_bulletin=creer_bulletin($id_perio,$bulletin,$titre_perio,$code_perio);
			$id_notice_bulletin= creer_lien_notice_bulletin($ancien_id,$id_perio,$id_bulletin,0,$titre_notice_bulletin,$bulletin);
		}
	}else{
		//On va chercher le bulletin
		$id_perio=creer_notice_periodique(0,$titre_perio,$code_perio);
		$id_bulletin=creer_bulletin($id_perio,$bulletin,$titre_perio,$code_perio);
		$id_notice_bulletin= creer_lien_notice_bulletin($ancien_id,$id_perio,$id_bulletin,0,$titre_notice_bulletin,$bulletin);
	}
	return $id_notice_bulletin;
}

/*
 * Faire les liens d'une notice de bulletin
 */

function creer_lien_notice_bulletin($ancien_id=0,$id_perio=0,$id_bulletin=0,$id_not_bull=0,$titre_not_bull="",$bulletin=array()){
	global $msg,$isbn_OK,$tit_200a,$notice_id,$notices_crees,$statutnot;

	if (empty($titre_not_bull)) {
		$titre_not_bull = $tit_200a[0];
	}
	$data = array(
			'titles' => $tit_200a,
			'code' => $isbn_OK,
			'tit1' => $titre_not_bull,
			'statut' => $statutnot,
	);
	$id_notice_bulletin = import_records::insert_relation_bulletin_num_notice($id_bulletin, $id_not_bull, $data);
	$notice_id = $id_notice_bulletin;

	$notices_crees[$ancien_id]=$id_notice_bulletin;
	//Lien entre la notice de bulletin et la notice de periodique
	notice_relations::insert($id_notice_bulletin, $id_perio, 'b', 0, 'up', false);
	if($id_notice_bulletin && ($bulletin["date"])){
		$requete="UPDATE notices SET year='".addslashes(substr($bulletin["date"],0,4))."', date_parution='".addslashes($bulletin["date"])."' WHERE notice_id='".$id_notice_bulletin."'";
		pmb_mysql_query($requete);
	}
	return $id_notice_bulletin;
}

function keep_authority_infos($authority_number,$type,$origin_authority,$notice_id,$authority_infos=array()){
	//on a un numéro d'autorité, on regarde si on l'a déjà rencontré
	$query = "select id_authority_source,num_authority from authorities_sources where authority_number = '".$authority_number."' and num_origin_authority='".$origin_authority."' and authority_type = '".$type."'";
	$result = pmb_mysql_query($query);
	$num_authority = 0;
	$num_authority_source = 0;
	if(pmb_mysql_num_rows($result)){
		$row = pmb_mysql_fetch_object($result);
		$num_authority = $row->num_authority;
		$num_authority_source= $row->id_authority_source;
		// on cherche la préférence... dès fois que...
		$query = "select id_authority_source, num_authority from authorities_sources where authority_number = '".$authority_number."' and authority_type = '".$type."' and authority_favorite = 1";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$num_authority = $row->num_authority;
			$num_authority_source= $row->id_authority_source;
		}
	}else{
		// c'est un petit nouveau
		switch($type){
			case "author" :
				$num_authority = auteur::import($authority_infos);
				break;
			case "uniform_title" :
				$num_authority = titre_uniforme::import($authority_infos);
				break;
			/*
			// DB : Non utilise, commente car la methode correspondante ne comporte pas les memes parametres
			case "category" :
				$num_authority = category::import($authority_infos);
				break;
			*/
			case "collection" :
				$num_authority = collection::import($authority_infos);
				break;
			case "subcollection" :
				$num_authority = subcollection::import($authority_infos);
				break;
			case "serie" :

				break;
		}
		$query = "insert into authorities_sources set
			num_authority = '$num_authority',
			authority_number = '".$authority_number."',
			authority_type = '$type',
			num_origin_authority = ".$origin_authority.",
			import_date = now()";
		pmb_mysql_query($query);
		$num_authority_source = pmb_mysql_insert_id();
	}
	//certaines autorités sont créés avant la notice...
	if($notice_id!=0){
		$query = "insert into notices_authorities_sources set
		num_authority_source = ".$num_authority_source.",
		num_notice = ".$notice_id;
		pmb_mysql_query($query);
	}
	return $num_authority;
}
