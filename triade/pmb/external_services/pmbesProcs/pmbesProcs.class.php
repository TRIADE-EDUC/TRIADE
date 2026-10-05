<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesProcs.class.php,v 1.16 2023/09/22 07:34:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], '.class.php')) die('no access');

global $class_path;
require_once($class_path.'/external_services.class.php');
require_once($class_path.'/parameters.class.php');

if(!defined('INTERNAL')) {define ('INTERNAL',1);}
if(!defined('EXTERNAL')) {define ('EXTERNAL',2);}

class pmbesProcs extends external_services_api_class {

	public function listProcs() {
	    if (!$this->has_user_rights(ADMINISTRATION_AUTH)) {
	        return array();
	    }
		$list=array();
		$query = 'select idproc, name, requete, comment from procs';
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_assoc($result)) {
			$list[] = array (
				'idproc' => $row->idproc,
				'name' => encoding_normalize::utf8_normalize($row->name),
				'requete' => encoding_normalize::utf8_normalize($row->requete),
				'comment' => encoding_normalize::utf8_normalize($row->comment),
			);
		}
		return $list;
	}

	/*
	 *
	 */
	public function executeProc($procedure, $idProc, $tparams) {
		global $msg, $charset, $PMBuserid;
		global $pmb_procedure_server_credentials,$pmb_procedure_server_address;
		global $erreur_explain_rqt;
		
		if (!$this->has_user_rights(ADMINISTRATION_AUTH)) {
		    return array();
		}
		$idProc = intval($idProc);
		$name = '';
		$report = '';
		if ($tparams['envt']) {
			foreach ($tparams['envt'] as $aparam=>$vparam) {
				global ${$aparam};
				${$aparam} = $vparam;
			}
		}

		switch ($procedure) {
			case INTERNAL:
				$hp=new parameters($idProc,'procs');
				$hp->get_final_query();
				$code_sql=$hp->final_query;
				$autorisations = $hp->proc->autorisations;
				break;
			case EXTERNAL:
				$pmb_procedure_server_credentials_exploded = explode("\n", $pmb_procedure_server_credentials);
				if ($pmb_procedure_server_address && (count($pmb_procedure_server_credentials_exploded) == 2)) {
					$aremote_procedure_client = new remote_procedure_client($pmb_procedure_server_address, trim($pmb_procedure_server_credentials_exploded[0]), trim($pmb_procedure_server_credentials_exploded[1]));
					$procedure = $aremote_procedure_client->get_proc($idProc,"AP");
					$the_procedure = $procedure['procedure'];
					if ($procedure['error_message']) {
						$report = htmlentities($msg['remote_procedures_error_server'], ENT_QUOTES, $charset).':<br /><i>'.$procedure['error_message'].'</i>';
						$result = array(
							'name' => encoding_normalize::utf8_normalize($the_procedure->name),
							'report' => encoding_normalize::utf8_normalize($report)
						);
						return $result;
					} else if ($the_procedure->params && ($the_procedure->params != 'NULL')) {
						$sql = 'CREATE TEMPORARY TABLE remote_proc LIKE procs';
						pmb_mysql_query($sql) or die(pmb_mysql_error());

						$sql = "INSERT INTO remote_proc (idproc, name, requete, comment, autorisations, parameters, num_classement) VALUES (0, '".pmb_mysql_escape_string($the_procedure->name)."', '".pmb_mysql_escape_string($the_procedure->sql)."', '".pmb_mysql_escape_string($the_procedure->comment)."', '', '".pmb_mysql_escape_string($the_procedure->params)."', 0)";
						pmb_mysql_query($sql) or die(pmb_mysql_error());
						$idproc = pmb_mysql_insert_id();

						$hp=new parameters($idproc,'remote_proc');
						$hp->get_final_query();
						$the_procedure->sql = $hp->final_query;
						$name = $the_procedure->name;
						$code_sql = $the_procedure->sql;
					}
				}
				break;
		}

		$linetemp = explode(';', $code_sql);
		if($autorisations) {
			$temp_autorisation = explode(' ', $autorisations);
		}
		$allow=false;
		if ($temp_autorisation) {
			foreach ($temp_autorisation as $userid) {
				if ($userid == $PMBuserid) {
					$allow = true;
				}
			}
			if (!$allow) {
				$report = $msg[11];
//				throw new Exception($message, $code);
				$result = array(
					'name' => encoding_normalize::utf8_normalize($name),
					'report' => encoding_normalize::utf8_normalize($report)
				);
				return $result;
			}
		}

		$line=array();
		for ($i=0;$i<count($linetemp);$i++) {
			if (trim($linetemp[$i])) {
				$line[]=trim($linetemp[$i]);

			}
		}

		foreach ($line as $cle => $valeur) {

			if($valeur) {

				$report .= "<strong>".$msg['procs_ligne']." $cle </strong>:&nbsp;$valeur<br /><br />";
				$er=explain_requete($valeur);
				if ($er) {
					$res = pmb_mysql_query($valeur);
					$report .= pmb_mysql_error();
					$nbr_lignes = pmb_mysql_num_rows($res);
					$nbr_champs = pmb_mysql_num_fields($res);

					if($nbr_lignes) {
						$report .= "<table >";
						for($i=0; $i < $nbr_champs; $i++) {
							$fieldname = pmb_mysql_field_name($res, $i);
							$report .= "<th>".$fieldname."</th>";
						}

						for($i=0; $i < $nbr_lignes; $i++) {
							$row = pmb_mysql_fetch_row($res);
							$report .= "<tr>";
							foreach($row as $col) {
								if(trim($col)=='') $col = '&nbsp;';
								$report .= '<td >'.$col.'</td>';
							}
							$report .= "</tr>";
						}
						$report .= "</table><hr />";
						$report .= "<span style='color:#ff0000'>".$msg['admin_misc_lignes']." ".pmb_mysql_affected_rows()."</span>";

					} else {

						$report .= "<br /><span style='color:#ff0000'>".$msg['admin_misc_lignes']." ".pmb_mysql_affected_rows();
						$err = pmb_mysql_error();
						if ($err) $report .= "<br />$err";
						$report .= "</span><hr />";

					}

				} else {
					// erreur explain_requete
					$report .= $valeur."<br /><br />".$msg['proc_param_explain_failed']."<br /><br />".$erreur_explain_rqt;
				}
			}


		} // fin while

		//Export CSV sur le resultat de la derniere requete
		if ($er && $nbr_lignes && $tparams['tocsv']['checked']=='1' && $tparams['tocsv']['filepath']) {

			if(!$tparams['tocsv']['sep']) {
				$tparams['tocsv']['sep']=',';
			}
			$trow=array();
			if($tparams['tocsv']['enclosure']) {
				for($i=0; $i < $nbr_champs; $i++) {
					$trow[] = addcslashes(pmb_mysql_field_name($res, $i),$tparams['tocsv']['enclosure']);
				}
				$row = $tparams['tocsv']['enclosure'].implode($tparams['tocsv']['enclosure'].$tparams['tocsv']['sep'].$tparams['tocsv']['enclosure'],$trow).$tparams['tocsv']['enclosure']."\r\n";
			} else {
				$row = implode($tparams['tocsv']['sep'],$trow)."\r\n";
			}
			file_put_contents($tparams['tocsv']['filepath'], $row);

			pmb_mysql_data_seek($res, 0);
			for($i=0; $i < $nbr_lignes; $i++) {
				$trow = pmb_mysql_fetch_row($res);
				if($tparams['tocsv']['enclosure']) {
					foreach($trow as $k=>$v){
						$trow[$k]=addcslashes($v,$tparams['tocsv']['enclosure']);
					}
					$row = $tparams['tocsv']['enclosure'].implode($tparams['tocsv']['enclosure'].$tparams['tocsv']['sep'].$tparams['tocsv']['enclosure'],$trow).$tparams['tocsv']['enclosure']."\r\n";
				} else {
					$row = implode($tparams['tocsv']['sep'],$trow)."\r\n";
				}
				file_put_contents($tparams['tocsv']['filepath'], $row, FILE_APPEND);
			}
		}

		$result = array(
			'name' => encoding_normalize::utf8_normalize($name),
			'report' => encoding_normalize::utf8_normalize($report)
		);
		return $result;
	}
}
