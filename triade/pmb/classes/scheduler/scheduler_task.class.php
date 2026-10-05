<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_task.class.php,v 1.22 2024/04/11 14:33:24 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use Pmb\Common\Library\System\System;

global $include_path, $class_path;
require_once $include_path . "/parser.inc.php";
require_once $include_path . "/templates/taches.tpl.php";
require_once $include_path . "/connecteurs_out_common.inc.php";
require_once $class_path . "/scheduler/scheduler_planning.class.php";
require_once $class_path . "/scheduler/scheduler_task_docnum.class.php";
require_once $class_path . "/upload_folder.class.php";
require_once $class_path . "/xml_dom.class.php";

class scheduler_task {

    // Commandes
    const RESUME = 1;
    const SUSPEND = 2;
    const STOP = 3;
    const RETRY = 4;
    const ABORT = 5;
    const FAIL = 6;

    // Statuts
    const WAITING = 1;
    const RUNNING = 2;
    const ENDED = 3;
    const SUSPENDED = 4;
    const STOPPED = 5;
    const FAILED = 6;
    const ABORTED = 7;

    // Messages propres au type de tâche
    protected $msg;

    // classe contenant les méthodes de l'API
    public $proxy;

    //identifiant de la tâche
    public $id_tache = 0;

    public $num_planificateur = 0;

    public $start_at = '0000-00-00 00:00:00';

    public $end_at = '0000-00-00 00:00:00';

    public $commande = 0;

    public $next_state = 0;

    public $indicat_progress = 0;

    // rapport de la tâche
    public $report = array();

    public $id_process = 0;

    public $statut;

    public $msg_statut = array();

    public $num_type_tache = 0;

    public $libelle_tache = '';

    public $num_user = 0;

    public $connectors_out_source_id = 0;

    public $operating_system = '';

    public $params = array();

    public $rep_upload = 0;

    public $path_upload = '';

    public $calc_next_heure_deb = '';

    public $calc_next_date_deb = '';

    public $suspended_time = 0;

    protected static $instances = [];
    
    /**
     * Valeur de progression par étape
     * @var float
     */
    protected $p_value;
    
    protected $progression = 0;

    public function __construct($id_tache = 0)
    {
        $this->id_tache = intval($id_tache);
        $this->get_messages();
        $this->fetch_data();
    }

    protected function fetch_data()
    {
        if ($this->id_tache) {
            $query = "select * from taches join planificateur on id_planificateur=num_planificateur where id_tache=" . $this->id_tache;
            $result = pmb_mysql_query($query);
            if ($result && pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_object($result);
                $this->num_planificateur = $row->num_planificateur;
                $this->start_at = $row->start_at;
                $this->end_at = $row->end_at;
                $this->statut = $row->status;
                $msg_statut = encoding_normalize::json_decode($row->msg_statut, true);
                if (is_array($msg_statut)) {
                    $this->msg_statut = $msg_statut;
                }
                $this->commande = $row->commande;
                $this->next_state = $row->next_state;
                $this->indicat_progress = $row->indicat_progress;
                $this->report = unserialize(htmlspecialchars_decode($row->rapport ?? "", ENT_QUOTES));
                $this->id_process = $row->id_process;
                $this->num_type_tache = $row->num_type_tache;
                $this->libelle_tache = $row->libelle_tache;
                $this->num_user = $row->num_user;
                $this->params = unserialize($row->param);
                $this->rep_upload = $row->rep_upload;
                $this->path_upload = $row->path_upload;
                $this->calc_next_date_deb = $row->calc_next_date_deb;
                $this->calc_next_heure_deb = $row->calc_next_heure_deb;
            }
        }
    }

    public function get_id_type()
    {
        return $this->id_type;
    }


    /**
     * messages
     */
    public function get_messages()
    {
        global $base_path, $lang;

        $tache_path = $base_path . "/admin/planificateur/" . str_replace('scheduler_', '', static::class);
        if (file_exists($tache_path . "/messages/" . $lang . ".xml")) {
            $file_name = $tache_path . "/messages/" . $lang . ".xml";
        } else if (file_exists($tache_path . "/messages/fr_FR.xml")) {
            $file_name = $tache_path . "/messages/fr_FR.xml";
        } else {
            $file_name = '';
        }
        if ($file_name) {
            $xmllist = new XMLlist($file_name);
            $xmllist->analyser();
            $this->msg = $xmllist->table;
        }
    }

    public function setEsProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function set_connectors_out_source_id($connectors_out_source_id)
    {
        $this->connectors_out_source_id = intval($connectors_out_source_id);
    }

    public function set_operating_system($operating_system)
    {
        $this->operating_system = $operating_system;
    }

    /**
     * Ecoute de la commande en temps réel
     * @param string $methode_callback
     */
    public function listen_commande($methode_callback)
    {
        $query_commande = "select status, commande, next_state from taches where id_tache=" . $this->id_tache;
        $result = pmb_mysql_query($query_commande);
        $this->commande = pmb_mysql_result($result, 0, "commande");
        if ($this->commande) {
            $this->next_state = pmb_mysql_result($result, 0, "next_state");
            $query = "update taches set status=" . $this->next_state . ", commande=0, next_state=0 where id_tache=" . $this->id_tache . "";
            $result = pmb_mysql_query($query);
            if ($result) {
                $this->statut = $this->next_state;
                call_user_func($methode_callback, $this->commande);
            }
        }
    }

    /**
     * Envoi d'une commande par la tache, changement du statut de la tâche
     *
     * @param number $state
     */
    public function send_command($state = 0)
    {
        $state = intval($state);
        if ($state) {
            $this->statut = $state;
            switch ($this->statut) {
                case scheduler_task::STOPPED: // 5
                    $query = "update taches set status=5,";
                    if ($this->start_at == '0000-00-00 00:00:00') {
                        $query .= "start_at=CURRENT_TIMESTAMP, ";
                    }
                    $query .= "end_at=CURRENT_TIMESTAMP, id_process=0, commande=0 where id_tache=" . $this->id_tache;
                    pmb_mysql_query($query);
                    break;
                default:
                    pmb_mysql_query("update taches set status=" . $this->statut . " where id_tache='" . $this->id_tache . "'");
                    break;
            }
        }
    }

    /**
     * MAJ d'une commande par la tâche
     * @param number $cmd
     */
    public function update_command($cmd = 0)
    {
        global $msg;

        $scheduler_tasks = new scheduler_tasks();
        foreach ($scheduler_tasks->types as $type) {
            if ($this->num_type_tache == $type->get_id()) {
                if ($this->end_at == '0000-00-00 00:00:00') {
                    // une commande a deja ete envoyee auparavant...
                    //	 				if ($scheduler_task->commande) {
                    //	 					$cmd = $scheduler_task->commande;
                    //	 				}
                    // check command - la commande envoyee est verifiee par rapport au status
                    $states = $type->get_states();
                    foreach ($states as $state) {
                        if ($state["id"] == $this->statut) {
                            foreach ($state["nextState"] as $nextState) {
                                $commands = $type->get_commands();
                                foreach ($commands as $command) {
                                    if ($nextState["command"] == $command["name"]) {
                                        if ($command["id"] == $cmd) {
                                            $this->commande = $cmd;
                                            $this->next_state = constant('scheduler_task::' . $nextState["value"]);
                                            pmb_mysql_query("update taches set commande=" . $this->commande . ", next_state='" . $this->next_state . "' where id_tache=" . $this->id_tache);
                                            scheduler_log::add_content('scheduler_' . scheduler_tasks::get_catalog_element($this->num_type_tache, 'NAME') . '_task_' . $this->id_tache . '.log', '[' .
                                                date('Y-m-d H:i:s') . '] ' . $msg['planificateur_command_' . $this->commande]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //une commande de reprise sur une tache de reindexation est-elle demandee ?
                if ($type->get_name() == 'clean' && $this->is_more_recently()) {
                    if ($cmd == scheduler_task::RESUME) {
                        $this->commande = $cmd;
                        $this->next_state = scheduler_task::RUNNING;
                        pmb_mysql_query("update taches set commande=" . $this->commande . ", next_state='" . $this->next_state . "' where id_tache=" . $this->id_tache);

                        scheduler_log::add_content('scheduler_' . scheduler_tasks::get_catalog_element($this->num_type_tache, 'NAME') . '_task_' . $this->id_tache . '.log', '[' . date('Y-m-d H:i:s') .
                            '] ' . $msg['planificateur_command_' . $this->commande]);
                    }
                }
            }
        }
    }

    public function send_mail()
    {
        global $PMBuseremail;
        global $include_path, $class_path;

        if ($this->params["alert_mail_on_failure"] != "") {
            $params_alert_mail = explode(",", $this->params["alert_mail_on_failure"]);
            if ($params_alert_mail[0]) {
                $mails = explode(";", $params_alert_mail[1]);
                if (preg_match("#.*@.*#", $PMBuseremail)) {
                    if (count($mails)) {
                        $mail_scheduler_task = new mail_scheduler_task();
                        $mail_scheduler_task->set_scheduler_task($this);
                        foreach ($mails as $mail) {
                            if (preg_match("#.*@.*#", $mail)) {
                                $mail_scheduler_task->set_mail_to_mail($mail);
                                $mail_scheduler_task->send_mail();
                            }
                        }
                    }
                }
            }
        }
    }

    protected function add_section_report($content = '', $css_class = 'scheduler_report_section')
    {
        $this->report[] = "<tr><th class='" . $css_class . "'>" . $content . "</th></tr>";
    }

    protected function add_content_report($content = '', $css_class = 'scheduler_report_content')
    {
        $this->report[] = "<tr><td class='" . $css_class . "'>" . $content . "</td></tr>";
    }

    protected function add_function_rights_report($method = '', $group = '')
    {
        global $msg;
        global $PMBusername;

        $this->report[] = "<tr><td>" . sprintf($msg["planificateur_function_rights"], $method, $group, $PMBusername) . "</td></tr>";
    }

    protected function add_rights_bad_user_report()
    {
        global $msg;
        global $PMBusername;

        $this->report[] = "<tr><th>" . sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername) . "</th></tr>";
    }

    /*
     * Exécution de la tâche - Méthode appelée par la classe spécifique
     * Modification des données de la base
     */
    public function execute()
    {
        //initialisation de la tâche planifiée sur la base
        if (empty(round($this->indicat_progress))) {
            $this->initialize();
        }

        if ($this->statut == scheduler_task::STOPPED && $this->commande == scheduler_task::RESUME) {
            $this->end_at = '0000-00-00 00:00:00';
            $this->statut = $this->next_state;
            $this->commande = 0;
            $this->next_state = 0;
            $this->indicat_progress = 0;
            $this->save();
        }
        //appel de la méthode spécifique
        $this->id_process = $this->execution();

        //si l'on obtient un identifiant de processus, cela veut dire qu'un nouveau processus de cette tâche a été détâchée
        if (empty($this->id_process)) {
            //finalisation de la tâche planifiée sur la base
            $this->finalize();

            $result_success = pmb_mysql_query("select num_planificateur from taches where id_tache=" . $this->id_tache);
            //mise à jour de la prochaine exec
            if (pmb_mysql_num_rows($result_success) == 1) {
                //planification d'une nouvelle tâche
                $scheduler_planning = new scheduler_planning(pmb_mysql_result($result_success, 0, "num_planificateur"));
                $scheduler_planning->calcul_execution();
                $scheduler_planning->insertOfTask();
            }
        } else {
            //on met à jour l'identifiant du processus
            $this->update_process();
        }
    }

    public function save()
    {
        global $charset;

        $query = "UPDATE taches SET
			start_at = '" . $this->start_at . "',
			end_at = '" . $this->end_at . "',
			status = '" . $this->statut . "',
			msg_statut = '" . addslashes(encoding_normalize::json_encode($this->msg_statut)) . "',
			commande = " . $this->commande . ",
			next_state = " . $this->next_state . ",
			indicat_progress = '" . addslashes($this->indicat_progress) . "',
			rapport = '" . htmlspecialchars(serialize($this->report), ENT_QUOTES, $charset) . "'
			WHERE id_tache = " . $this->id_tache;
        pmb_mysql_query($query);
    }

    public function cancellation()
    {
        $this->start_at = date('Y-m-d H:i:s');
        $this->end_at = date('Y-m-d H:i:s');
        $this->statut = $this->next_state;
        $this->commande = 0;
        $this->next_state = 0;
        $this->save();
    }

    public function recovery()
    {
        //On a demandé une reprise, on supprime celle en attente
        $delete_waiting = "DELETE FROM taches WHERE num_planificateur = " . $this->num_planificateur . " AND status = 1";
        pmb_mysql_query($delete_waiting);

        //TODO : vérifier qu'une tache n'est pas en cours
    }

    public function run()
    {
        global $base_path;
        global $pmb_path_php, $pmb_psexec_cmd;

        $path_file = $base_path . '/admin/planificateur/run_task.php ' . $this->id_tache . ' ' . $this->num_type_tache . ' ' . $this->num_planificateur . ' ' . $this->num_user . ' ' .
            $this->connectors_out_source_id . ' ' . LOCATION;
        $this->id_process = System::runProcess($path_file);

        $this->update_process();

        $path_file = $base_path . '/admin/planificateur/watch_task.php ' . $this->id_tache . ' ' . $this->num_user . ' ' . $this->connectors_out_source_id . ' ' . LOCATION;
        System::runProcess($path_file);
        $this->init_watching();

        return $this->id_process;
    }

    public function get_task_params()
    {
        $params = "";
        if ($this->id_tache) {
            $result = pmb_mysql_query("select param from planificateur, taches where id_planificateur=num_planificateur and id_tache=" . $this->id_tache);
            if ($result) $params = unserialize(pmb_mysql_result($result, 0, "param"));
        }
        return $params;
    }

    public function initialize()
    {
        $this->start_at = date('Y-m-d H:i:s');
        $this->statut = scheduler_task::RUNNING;
        $this->save();
    }

    public function finalize()
    {
        global $charset;

        if (round($this->indicat_progress) == 100) {
            $this->statut = scheduler_task::ENDED;
        } else {
            if (in_array($this->statut, array(scheduler_task::SUSPENDED, scheduler_task::STOPPED))) {
                //on finalise la tâche avec le statut Arrêté
                $this->statut = scheduler_task::STOPPED;
            } else {
                $this->statut = scheduler_task::FAILED;
                if ($this->params['alert_mail_on_failure']) {
                    $this->send_mail();
                }
            }
        }
        //fin de l'exécution, mise à jour sur la base
        $this->end_at = date('Y-m-d H:i:s');
        $this->commande = 0;
        $query = "UPDATE taches SET
			end_at = '" . $this->end_at . "',
			status = " . $this->statut . ",
			msg_statut = '" . addslashes(encoding_normalize::json_encode($this->msg_statut)) . "',
			commande=" . $this->commande . ",
			rapport = '" . htmlspecialchars(serialize($this->report), ENT_QUOTES, $charset) . "',
			id_process=0
			WHERE id_tache='" . $this->id_tache . "'";
        pmb_mysql_query($query);
    }

    public function update_progression($percent)
    {
        global $charset;

        $this->indicat_progress = round($percent, 2);
        $query = "update taches set indicat_progress ='" . $this->indicat_progress . "', msg_statut = '" . addslashes(encoding_normalize::json_encode($this->msg_statut)) . "', rapport='" .
            htmlspecialchars(serialize($this->report), ENT_QUOTES, $charset) . "' where id_tache=" . $this->id_tache;
        pmb_mysql_query($query);
    }


    /**
     * Mise a jour de la tache avec id du processus
     */
    protected function update_process()
    {
        $query = "update taches set id_process ='" . $this->id_process . "' where id_tache=" . $this->id_tache;
        pmb_mysql_query($query);
    }


    /**
     * Initialisation surveillance
     * Enregistre l'hote sur lequel s'execute la tache
     * et un timestamp de derniere verification
     */
    protected function init_watching()
    {
        $host_name = gethostname();
        if (false === $host_name) {
            $host_name = 'localhost';
        }
        $alive_at = date('Y-m-d H:i:s');
        $query = "update taches set host_name ='" . addslashes($host_name) . "', alive_at= '" . $alive_at . "' where id_tache=" . $this->id_tache;
        pmb_mysql_query($query);
    }

    public function isUploadValide()
    {
        $up = new upload_folder($this->rep_upload);
        $nom_chemin = $up->formate_nom_to_path($up->repertoire_nom . $this->path_upload);
        if (is_dir($nom_chemin) && is_writable($nom_chemin)) {
            return true;
        }
        return false;
    }

    // que passer à cette fonction datas ou object ?? (objet pdf , contenu xls)
    public function generate_docnum($content, $mimetype = "application/pdf", $ext_fichier = "pdf")
    {
        global $msg;

        $scheduler_task_docnum = new scheduler_task_docnum();
        $scheduler_task_docnum->num_tache = $this->id_tache;

        $up = new upload_folder($this->rep_upload);
        $nom_chemin = $up->formate_nom_to_path($up->repertoire_nom . $this->path_upload);
        //appel de fonction pour le calcul de nom de fichier
        $date_now = date('Ymd');
        $scheduler_task_docnum->nomfichier = clean_string_to_base($this->libelle_tache) . "_" . $date_now;
        $scheduler_task_docnum->contenu = $content;
        $scheduler_task_docnum->extfichier = $ext_fichier;
        $scheduler_task_docnum->file = "";
        $scheduler_task_docnum->mimetype = $mimetype;
        $scheduler_task_docnum->repertoire = $this->rep_upload;
        $scheduler_task_docnum->path = $this->path_upload;
        $path_absolu = $nom_chemin . $scheduler_task_docnum->nomfichier . "." . $scheduler_task_docnum->extfichier;
        if (file_exists($path_absolu)) {
            $i = 2;
            while (file_exists($nom_chemin . $scheduler_task_docnum->nomfichier . "_" . $i . "." . $scheduler_task_docnum->extfichier)) {
                $i++;
            }
            $path_absolu = $nom_chemin . $scheduler_task_docnum->nomfichier . "_" . $i . "." . $scheduler_task_docnum->extfichier;
            $scheduler_task_docnum->nomfichier = $scheduler_task_docnum->nomfichier . "_" . $i;
        }
        $path_absolu = $up->encoder_chaine($path_absolu);

        //verifier permissions d'ecriture...
        if (is_writable($nom_chemin)) {
            switch ($mimetype) {
                case "application/pdf":
                    $content->Output($path_absolu, "F");
                    break;
                case "application/ms-excel":
                    file_put_contents($path_absolu, $content);
                    break;
            }
            //			if ($mimetype == "application/pdf") {
            //				$content->Output($path_absolu,"F");
            //			} else if ($mimetype == "application/ms-excel") {
            //				file_put_contents($path_absolu, $content);
            //			}
            $scheduler_task_docnum->save();
            $this->report[] = "<tr><td>" . $msg["planificateur_write_success"] . " : <a target='_blank' href='./tache_docnum.php?tache_docnum_id=" . $scheduler_task_docnum->id . "'>" .
                $scheduler_task_docnum->nomfichier . "." . $scheduler_task_docnum->extfichier . "</a></td></tr>";
            return true;
        } else {
            $this->report[] = "<tr><td>" . sprintf($msg["planificateur_write_error"], $path_absolu) . "</td></tr>";
            return false;
        }
    }

    public function unserialize_task_params()
    {
        return $this->get_task_params();
    }

    public function suspend()
    {
        //Suspension à 10 minutes max
        while ($this->statut == scheduler_task::SUSPENDED && $this->suspended_time <= 600) {
            sleep(20);
            $this->suspended_time += 20;
            $this->listen_commande(array(&$this, "traite_commande"));
        }
        if ($this->suspended_time > 600) {
            $this->statut = scheduler_task::STOPPED;
        }
    }

    public function traite_commande($cmd, $message = '')
    {
        switch ($cmd) {
            case scheduler_task::RESUME:
                $this->send_command(scheduler_task::WAITING);
                break;
            case scheduler_task::SUSPEND:
                $this->suspend();
                break;
            case scheduler_task::STOP:
                $this->finalize();
                die();
                break;
            case scheduler_task::ABORT:
                $this->abort();
                $this->finalize();
                die();
                break;
            case scheduler_task::FAIL:
                $this->finalize();
                die();
                break;
        }
    }

    protected function is_more_recently()
    {
        $query = "SELECT id_tache FROM taches WHERE num_planificateur = " . $this->num_planificateur . " AND status=" . scheduler_task::STOPPED . " ORDER BY id_tache DESC LIMIT 1";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            if ($this->id_tache == pmb_mysql_result($result, 0) && !scheduler_planning::is_already_in_progress($this->num_planificateur)) {
                return true;
            }
        }
        return false;
    }

    public function get_availability_commands()
    {
        global $msg;

        $availability_commands = array();
        $scheduler_tasks = new scheduler_tasks();
        foreach ($scheduler_tasks->types as $type) {
            if ($type->get_id() == $this->num_type_tache) {
                $states = $type->get_states();
                foreach ($states as $aelement) {
                    if ($this->statut == $aelement["id"]) {
                        foreach ($aelement["nextState"] as $state) {
                            if ($state["command"] != "") {
                                //récupère le label de la commande
                                $commands = $type->get_commands();
                                foreach ($commands as $command) {
                                    if (($state["command"] == $command["name"]) && ($state["dontsend"] != "yes")) {
                                        if ($command["id"] == $this->commande) {
                                            $command["asked"] = true;
                                        } else {
                                            $command["asked"] = false;
                                        }
                                        $availability_commands[$command["name"]] = $command;
                                    }
                                }
                            }
                        }
                    }
                }
                if (!in_array('RESUME', $availability_commands)) {
                    if ($type->get_name() == 'clean' && $this->is_more_recently()) {
                        $availability_commands['RESUME'] = array(
                            'id' => '1', 
                            'name' => 'RESUME', 
                            'label' => $msg['task_resume'], 
                            'asked' => ($this->commande == 1 ? true : false)
                        );
                    }
                }
            }
        }
        return $availability_commands;
    }

    public static function delete($id)
    {
        $id = intval($id);
        $query = "delete from taches where id_tache = " . $id . " and status <> '" . scheduler_task::RUNNING . "'";
        pmb_mysql_query($query);
        return true;
    }

    public function is_param_active($name)
    {
        if ($this->params[$name]) {
            return true;
        } else {
            return false;
        }
    }


    //retourne le nombre de taches associees a un type de tache
    public function get_nb_docnum()
    {
        $query = "select * from taches t, taches_docnum tdn where t.id_tache=tdn.num_tache and id_tache=" . $this->id_tache;
        $result = pmb_mysql_query($query);
        return pmb_mysql_num_rows($result);
    }

    public function get_id_tache()
    {
        return $this->id_tache;
    }

    public function get_num_planificateur()
    {
        return $this->num_planificateur;
    }

    public function get_num_type_tache()
    {
        return $this->num_type_tache;
    }

    public function get_indicat_progress()
    {
        return $this->indicat_progress;
    }

    public function get_libelle_tache()
    {
        return $this->libelle_tache;
    }

    public function get_status_label()
    {
        global $msg;

        return $msg["planificateur_state_" . $this->statut];
    }

    public function get_param($name)
    {
        return $this->params[$name];
    }

    public static function get_num_type_from_id($id)
    {
        $id = intval($id);
        $query = "SELECT num_type_tache FROM taches
			JOIN planificateur ON id_planificateur = num_planificateur WHERE id_tache=" . $id;
        $result = pmb_mysql_query($query);
        return pmb_mysql_result($result, 0, 'num_type_tache');
    }

    public static function get_instance($id)
    {
        $id = intval($id);
        if (!isset(static::$instances[$id])) {
            static::$instances[$id] = new scheduler_task($id);
        }
        return static::$instances[$id];
    }
}