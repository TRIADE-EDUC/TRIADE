<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesTasks.class.php,v 1.26 2024/04/11 08:26:23 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path . "/external_services.class.php");

use Pmb\Common\Library\System\System;

class pmbesTasks extends external_services_api_class {

    /**
     * Repertorie les taches en timeout
     * et les passe en commande FAIL
     * @return string[]
     */
    public function timeoutTasks()
    {
        $requete = "select id_tache, param, start_at, num_type_tache FROM taches
				JOIN planificateur ON num_planificateur=id_planificateur
            WHERE id_process <> 0 and commande <> " . scheduler_task::FAIL;
        $resultat = pmb_mysql_query($requete);
        if (pmb_mysql_num_rows($resultat)) {
            while ($row = pmb_mysql_fetch_object($resultat)) {
                $params = unserialize($row->param);
                if (isset($params['timeout']) && $params['timeout']) {
                    $query = "select count(*) as nb from taches
							where DATE_ADD('" . $row->start_at . "', INTERVAL " . ($params['timeout']) . " MINUTE) <= CURRENT_TIMESTAMP
							and id_tache=" . $row->id_tache;
                    $result = pmb_mysql_query($query);
                    if ($result && pmb_mysql_result($result, 0, 'nb')) {
                        scheduler_log::add_content('scheduler_' . scheduler_tasks::get_catalog_element($row->num_type_tache, 'NAME') . '_task_' . $row->id_tache . '.log', 'Timeout of task exceeded');
                        // 6 = FAIL - Sera mis à l'échec à l'écoute de la tâche
                        $requete_check_timeout = "update taches set commande=" . scheduler_task::FAIL . "
							where id_tache=" . $row->id_tache;
                        pmb_mysql_query($requete_check_timeout);
                    }
                }
            }
        }
        return array("response" => "OK");
    }


    /**
     * Vérifie que les processus des taches en cours sont actifs
     * Prend en compte le nom d'hote sur lequel la tache a ete lancee
     *
     */
    public function checkTasks()
    {
        $current_host_name = System::getHostName();
        $query = "SELECT id_tache, start_at, id_process, host_name, (UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(alive_at)) as time_since_alive , num_type_tache
                FROM taches
                JOIN planificateur ON num_planificateur = id_planificateur
                WHERE id_process != 0 ";
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {

                $check_process = false;
                //Tache lancee sur le meme host > verification processus
                if ($current_host_name == $row['host_name']) {
                    //file_put_contents("/tmp/watch.log", "checkTask > my host".PHP_EOL, FILE_APPEND);
                    $check_process = System::checkProcess($row['id_process']);
                    //Tache lancee sur un host different > on verifie que la derniere fois quelle etait vivante date de moins de 10mn
                } else {
                    //file_put_contents("/tmp/watch.log", "checkTask > not my host , time_since_alive = ".$row['time_since_alive'].PHP_EOL, FILE_APPEND);

                    $check_process = ($row['time_since_alive'] > 600) ? false : true;
                }
                if (!$check_process) {
                    //file_put_contents("/tmp/watch.log", "checkTask > Abort".PHP_EOL, FILE_APPEND);
                    $scheduler_task = new scheduler_task($row["id_tache"]);
                    $scheduler_task->send_command(scheduler_task::ABORT);
                    //la tâche s'est arrêtée involontairement / pour du debug si besoin
                    //scheduler_log::add_content('scheduler_'.scheduler_tasks::get_catalog_element($row->num_type_tache, 'NAME').'_task_'.$row->id_tache.'.log', 'The task stopped unintentionally');

                    //En fonction du paramétrage de la tâche...
                    //Replanifier / Envoi de mail
                    if ($scheduler_task->is_param_active('alert_mail_on_failure')) {
                        $scheduler_task->send_mail();
                    }
                    if ($scheduler_task->is_param_active('restart_on_failure')) {
                        $this->createNewTask($scheduler_task->get_id_tache(), $scheduler_task->get_num_type_tache(), $scheduler_task->get_num_planificateur());
                    }
                }
            }
        }
    }


    /**
     * Annule les taches a annuler
     * Relance les taches a reprendre
     * Lance les taches a executer
     *
     * @param int $connectors_out_source_id : id source connecteur sortant
     *
     */
    public function runTasks($connectors_out_source_id)
    {
        $connectors_out_source_id = intval($connectors_out_source_id);
        $os = System::getOS();

        //A-t-on demandé l'annulation d'une tâche ?
        $query = "SELECT id_tache FROM taches
			JOIN planificateur ON id_planificateur = num_planificateur
            WHERE id_process = 0 AND commande = " . scheduler_task::ABORT;
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $scheduler_task = new scheduler_task($row["id_tache"]);
                $scheduler_task->cancellation();
            }
        }

        //A-t-on essayé une reprise de tâche ?
        $query = "SELECT id_tache FROM taches
			JOIN planificateur ON id_planificateur = num_planificateur
            WHERE id_process = 0 AND commande = " . scheduler_task::RESUME;
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $scheduler_task = new scheduler_task($row["id_tache"]);
                $scheduler_task->recovery();
                $scheduler_task->set_connectors_out_source_id($connectors_out_source_id);
                $scheduler_task->set_operating_system($os);
                $scheduler_task->run();
            }
        }

        //Y-a t-il une ou plusieurs tâches à exécuter...
        $query = "SELECT id_tache FROM taches
			JOIN planificateur ON num_planificateur = id_planificateur
			WHERE start_at='0000-00-00 00:00:00'
			AND status=1
			AND calc_next_date_deb <> '0000-00-00'
			AND (calc_next_date_deb < '" . date('Y-m-d') . "' OR calc_next_date_deb = '" . date('Y-m-d') . "' AND calc_next_heure_deb <= '" . date('H:i') . "')";
        $result = pmb_mysql_query($query);
        while ($row = pmb_mysql_fetch_assoc($result)) {
            $scheduler_task = new scheduler_task($row["id_tache"]);
            $scheduler_task->set_connectors_out_source_id($connectors_out_source_id);
            $scheduler_task->set_operating_system($os);
            $scheduler_task->run();
        }
    }


    /**
     * Retourne la liste des tâches réalisées et planifiées
     * @return []
     */
    public function listTasksPlanned()
    {
        $list = array();
        $query = "SELECT id_tache, libelle_tache, desc_tache, start_at, end_at, indicat_progress, status
			FROM taches JOIN planificateur ON num_planificateur = id_planificateur";
        $result = pmb_mysql_query($query);
        if ($result) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $list[] = array(
                    "id_tache" => $row["id_tache"], 
                    "libelle_tache" => encoding_normalize::utf8_normalize($row["libelle_tache"]),
                    "desc_tache" => encoding_normalize::utf8_normalize($row["desc_tache"]), 
                    "start_at" => $row["start_at"], 
                    "end_at" => $row["end_at"], 
                    "indicat_progress" => $row["indicat_progress"],
                    "status" => $row["status"],
                );
            }
        }
        return $list;
    }


    /**
     * Retourne les types de tâches
     *
     * @return []
     */
    public function listTypesTasks()
    {
        $types_taches = array();

        if (file_exists("../admin/planificateur/catalog_subst.xml")) {
            $filename = "../admin/planificateur/catalog_subst.xml";
        } else {
            $filename = "../admin/planificateur/catalog.xml";
        }
        $xml = file_get_contents($filename);
        $param = _parser_text_no_function_($xml, "CATALOG", $filename);

        foreach ($param["ACTION"] as $anitem) {
            $t = array();
            $t["ID"] = $anitem["ID"];
            $t["NAME"] = $anitem["NAME"];
            $t["COMMENT"] = $anitem["COMMENT"];
            $types_taches[$t["ID"]] = $t;
        }
        return $types_taches;
    }


    /**
     * Retourne les informations concernant une tâche planifiée
     *
     * @param int $planificateur_id
     * @param string $active
     *
     * @throws Exception
     *
     * @return []
     */
    public function getInfoTaskPlanned($planificateur_id, $active = "")
    {
        $result = array();
        $planificateur_id = intval($planificateur_id);

        if (!$planificateur_id) {
            throw new Exception("Missing parameter: planificateur_id");
        }

        if ($active != "") {
            $critere = " and statut=" . $active;
        } else {
            $critere = "";
        }

        $sql = "SELECT * FROM planificateur WHERE id_planificateur = " . $planificateur_id;
        $sql = $sql . $critere;
        $res = pmb_mysql_query($sql);
        if (!$res) {
            throw new Exception("Not found: planificateur_id = " . $planificateur_id);
        }

        while ($row = pmb_mysql_fetch_assoc($res)) {
            $result[] = array(
                "id_planificateur" => $row["id_planificateur"], 
                "num_type_tache" => $row["num_type_tache"], 
                "libelle_tache" => encoding_normalize::utf8_normalize($row["libelle_tache"]),
                "desc_tache" => encoding_normalize::utf8_normalize($row["desc_tache"]), 
                "num_user" => $row["num_user"], 
                "statut" => $row["statut"],
                "calc_next_date_deb" => encoding_normalize::utf8_normalize($row["calc_next_date_deb"]), 
                "calc_next_heure_deb" => encoding_normalize::utf8_normalize($row["calc_next_heure_deb"]),
            );
        }
        return $result;
    }


    /**
     * Cree une nouvelle tache
     *
     * @param int $id_tache
     * @param int $id_type_tache
     * @param int $id_planificateur
     *
     * @throws Exception
     */
    public function createNewTask($id_tache, $id_type_tache, $id_planificateur)
    {
        global $base_path;

        $id_tache = intval($id_tache);
        $id_planificateur = intval($id_planificateur);
        if (!$id_tache) {
            throw new Exception("Missing parameter: id_tache");
        }

        if (file_exists($base_path . "/admin/planificateur/catalog_subst.xml")) {
            $filename = $base_path . "/admin/planificateur/catalog_subst.xml";
        } else {
            $filename = $base_path . "/admin/planificateur/catalog.xml";
        }
        $xml = file_get_contents($filename);
        $param = _parser_text_no_function_($xml, "CATALOG", $filename);

        $scheduler_planning = new scheduler_planning($id_planificateur);
        $scheduler_planning->calcul_execution();
        $scheduler_planning->insertOfTask();
    }
}