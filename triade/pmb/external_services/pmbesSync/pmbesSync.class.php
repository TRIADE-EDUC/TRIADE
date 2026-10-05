<?php
// +-------------------------------------------------+
// © 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesSync.class.php,v 1.19.4.1 2025/04/16 12:16:51 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/notice.class.php");

class pmbesSync extends external_services_api_class {
	public $id_source;
	public $id_tache;
	public $callback_listen_command;
	public $callback_deals_command;

	/* Liste des connecteurs sources étant un entrepôt */
	public function listEntrepotSources() {
	    if (!$this->has_user_rights(ADMINISTRATION_AUTH)) {
	        return array();
	    }
	    $list = array();
		$query = "select source_id, id_connector, comment, name from connectors_sources where repository=1";
		$result = pmb_mysql_query($query);

		while ($row = pmb_mysql_fetch_assoc($result)) {
		    $list[] = array(
				"source_id" => $row["source_id"],
				"id_connector" => encoding_normalize::utf8_normalize($row["id_connector"]),
				"comment" => encoding_normalize::utf8_normalize($row["comment"]),
				"name_connector_in" => encoding_normalize::utf8_normalize($row["name"]),
			);
		}
		return $list;
	}

	public function callback_progress($percent) {
		$requete="update source_sync set percent=".round($percent*100)." where source_id=".$this->id_source;
		pmb_mysql_query($requete);

		if ($this->id_tache != "") {
			$requete = "update taches set indicat_progress =".round($percent*100)." where id_tache=".$this->id_tache;
			pmb_mysql_query($requete);
		}

		// listen commands
		if (isset($this->callback_listen_command) && $this->callback_listen_command)
			call_user_func($this->callback_listen_command,$this->id_tache,$this->callback_deals_command);
	}

	protected function get_elements_already_sync($id_source) {
		$id_source = intval($id_source);
		$recid_ids = array();
		$query = "select distinct entrepot_source_".$id_source.".recid from entrepot_source_".$id_source;
		$result = pmb_mysql_query($query);
		if($result) {
			while($row = pmb_mysql_fetch_object($result)) {
				$recid_ids[] = $row->recid;
			}
		}
		return $recid_ids;
	}

	protected function get_sync_last_date($id_source) {
		$id_source = intval($id_source);
		$requete="select max(date_import) as sync_last_date from entrepot_source_".$id_source;
		$resultat=pmb_mysql_query($requete);
		if($resultat) {
			$sync_last_date = pmb_mysql_result($resultat, 0, 'sync_last_date');
			if(!empty($sync_last_date)) {
				return substr($sync_last_date, 0, 10);
			}
		}
		return '';
	}

	/* Lancement de la synchronisation */
	public function doSync($id_connector,
	    $id_source,
	    $auto_import = false,
	    $id_tache = '',
	    $callback_listen_command = null,
	    $callback_deals_command = null,
	    $auto_delete = false,
	    $not_in_notices_externes = false,
	    $date_start = '',
	    $date_end = ''
	    ) {
		global $base_path, $msg;
		global $pmb_notice_controle_doublons;

		if (!$this->has_user_rights(ADMINISTRATION_AUTH)) {
		    return array();
		}
		$id_source = intval($id_source);
		$id_tache = intval($id_tache);
		if ((!$id_connector) || (!$id_source)) {
			return array();
		}
		$this->clear_error();

		$this->callback_listen_command = $callback_listen_command;
		$this->callback_deals_command = $callback_deals_command;
		$result_dosync=array();
		$this->id_source = $id_source;
		$this->id_tache = $id_tache;

		$contrs = new connecteurs();
		require_once ($base_path . "/admin/connecteurs/in/" . $contrs->catalog[$id_connector]["PATH"] . "/" . $contrs->catalog[$id_connector]["NAME"] . ".class.php");

		$classname = $contrs->catalog[$id_connector]["NAME"];
		$conn = new $classname("{$base_path}/admin/connecteurs/in/{$contrs->catalog[$id_connector]["PATH"]}");

		//Vérification qu'il n'y a pas de synchronisation en cours...
		$is_already_sync = false;
		
		$recover_env = "";
		$recover = false;

		$requete="select * from source_sync where source_id=$id_source";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {


			$rs_s=pmb_mysql_fetch_object($resultat);
			if (!$rs_s->cancel) {
				$result_dosync[] =  $msg["connecteurs_sync_currentexists"];
				$is_already_sync=true;
			} else {
				$recover=true;
				$recover_env=$rs_s->env;
				$env = array();
			}
		} else {
			global $form_from;
			global $form_until;
			global $form_radio;
			$form_radio = 'all_notices';
			$form_from = '';
			$form_until = '';
			$sync_last_date = $this->get_sync_last_date($id_source);
			if(($date_start && $date_start == $sync_last_date) && !$date_end) {
				$form_radio = 'last_sync';
			} elseif($date_start || $date_end) {
				$form_radio = 'date_sync';
				$form_from = $date_start;
				$form_until = $date_end;
			}

            // Recuperation des parametres d'environnement de la tache
			$task = new scheduler_task($id_tache);
			$taskParams = $task->params;
			$env = $conn->getScheduledTaskEnvironment($id_source, $taskParams);
			foreach($env as $k => $v) {
			    global $$k;
			    $$k = $v;
			}
		}

		//si l'import automatique est activé
		//on récupère la liste des éléments déjà synchronisés pour l'import après synchro
		if($auto_import) {
			$recid_ids = $this->get_elements_already_sync($id_source);
		}

		if (!$is_already_sync) {

			if (!$recover) {
				$requete="insert into source_sync (source_id,nrecu,ntotal,date_sync) values($id_source,0,0,now())";
				$r=pmb_mysql_query($requete);
			} else {
				$requete="update source_sync set cancel=0 where source_id=$id_source";
				$r=pmb_mysql_query($requete);
			}
			if ($r) {


				$n_maj=$conn->maj_entrepot($id_source,array(&$this,"callback_progress"),$recover,$recover_env);

				$result_dosync[] = sprintf($msg["connecteurs_count_notices"],$n_maj);
				if (!$conn->error) {
					$this->callback_progress(1);
					$percent = 1;
					$requete="update source_sync set percent=".round($percent*100)." where source_id=$id_source";
					$r=pmb_mysql_query($requete);

					$requete="delete from source_sync where source_id=".$id_source;
					pmb_mysql_query($requete);
					$requete="update connectors_sources set last_sync_date=now() where source_id=".$id_source;
					pmb_mysql_query($requete);
				} else {
					if ($conn->break_maj($id_source)) {
						$requete="delete from source_sync where source_id=".$id_source;
					} else {
						$requete="update source_sync set cancel=2 where source_id=".$id_source;
					}
					pmb_mysql_query($requete);
					$result_dosync[] = $conn->error_message;
					//Recopie du message d'erreur sur la propriété de la classe
					$this->set_error($conn->error_message);
				}

			} else {
			    $this->set_error(pmb_mysql_error());
			    $result_dosync[] = $this->error_message;
			}
		} else {
		    $result_dosync[] = $msg["connecteurs_sync_currentexists"];
		}

		//si l'import automatique est activé
		if($auto_import/* && !$auto_import*/){
			//on va chercher les notices non intégrées
			$query = "select distinct entrepot_source_".$id_source.".recid from entrepot_source_".$id_source." where concat(connector_id,' ".$id_source." ',ref) not in(select recid from notices_externes)";
			if(count($recid_ids)) {
				$query .= " AND entrepot_source_".$id_source.".recid not in (".implode(',', $recid_ids).")";
			}
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($row = pmb_mysql_fetch_object($result)){
					$infos=entrepot_to_unimarc($row->recid);

					if($infos['notice']){
						$z=new z3950_notice("unimarc",$infos['notice'],$infos['source_id']);
						if($pmb_notice_controle_doublons != 0){
							$sign = new notice_doublon(true,$infos['source_id']);
							$signature = $sign->gen_signature($row->recid);
						}else {
							$signature = "";
						}
						$z->signature = $signature;
						if($infos['notice']) $z->notice = $infos['notice'];
						if($infos['source_id']) $z->source_id = $infos['source_id'];
						$z->var_to_post();
						$ret=$z->insert_in_database();
						$id_notice = $ret[1];
						$rqt = "select recid from external_count where rid = '$row->recid'";
						$res = pmb_mysql_query($rqt);
						if(!isset($recid)) $recid = '';
						if(pmb_mysql_num_rows($res)) $recid = pmb_mysql_result($res,0,0);
						if(!$not_in_notices_externes){
							$req= "insert into notices_externes set num_notice = '".$id_notice."', recid = '".$recid."'";
							pmb_mysql_query($req);
						}
					}
				}
			}
			if (!$conn->error && $auto_delete) {
				// On gère la suppression des notices qui ne sont plus présentes dans l'entrepôt
				$query = "select distinct num_notice
						from notices_externes
						left join external_count on notices_externes.recid = external_count.recid
						where rid is null and notices_externes.recid like '% ".$id_source." %'";
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						// suppression de la notice
						notice::del_notice($row->num_notice);
					}
				}
			}
		}
		return $result_dosync;
	}

	/* Opération de vidage */
	public function emptySource($id_connector, $id_source) {
		global $base_path;

		$id_source = intval($id_source);
		if ((!$id_connector) || (!$id_source)) {
			return false;
		}

		$contrs = new connecteurs();
		require_once ($base_path . "/admin/connecteurs/in/" . $contrs->catalog[$id_connector]["PATH"] . "/" . $contrs->catalog[$id_connector]["NAME"] . ".class.php");

		$classname = $contrs->catalog[$id_connector]["NAME"];
		$conn = new $classname("{$base_path}/admin/connecteurs/in/{$contrs->catalog[$id_connector]["PATH"]}");

		if (($id_source)&&($conn)) {
			$conn->del_notices($id_source);
			return true;
		}
		return false;
	}

}