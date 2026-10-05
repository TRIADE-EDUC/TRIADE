<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: artecampus.class.php,v 1.1.2.3.2.2 2025/04/16 12:16:51 dbellamy Exp $

use Pmb\Common\Orm\DocsLocationOrm;
use Pmb\Common\Orm\EmprCategOrm;
use Pmb\Common\Orm\EmprOrm;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;
require_once $class_path . '/connecteurs.class.php';
require_once $class_path . '/curl.class.php';

if (version_compare(PHP_VERSION, '5', '>=') && extension_loaded('xsl')) {
    global $include_path;
    require_once $include_path.'/xslt-php4-to-php5.inc.php';
}

class artecampus extends connector
{
    /**
     * Url de connexion
     */
    public const LOGIN_URL = "https://campus.arte.tv/pmb/login";

    /**
     * Liste des roles autorises
     */
    public const VALID_ROLES = ['teacher', 'student'];

    /**
     * Nombre de notices recues
     *
     * @var int
     */
    public $n_recu;

    /**
     * Nombre total de notices
     *
     * @var int
     */
    public $n_total;

    /**
     * Renvoie le recid d'une notice et d'une source
     *
     * @param integer $notice_id
     * @param integer $source_id
     * @return integer|false
     */
    protected function fetch_recid(int $notice_id, int $source_id)
    {
        $query = 'SELECT external_count.rid FROM external_count
         JOIN notices_externes ON notices_externes.recid = external_count.recid
         WHERE
            external_count.source_id = ' . $source_id . ' AND
            notices_externes.num_notice = ' . $notice_id;

        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $recid = pmb_mysql_result($result, 0, 0);
            return intval($recid);
        }
        return false;
    }

    /**
     * Retourn le header de l'enrichissement
     *
     * @param integer $source_id
     * @return array
     */
    public function getEnrichmentHeader(int $source_id)
    {
        return [
            '<script data-source_id="' . $source_id . '">
                function artecampus_callback_auth_popup(id_empr) { window.location.reload(); }
            </script>',
        ];
    }

    /**
     * Renvoie l'enrichissement d'une notice
     *
     * @param integer $notice_id
     * @param integer $source_id
     * @param string $type
     * @param array $enrich_params
     * @return array{source_label: string, artecampus: array{content: string}}
     */
    public function getEnrichment($notice_id, $source_id, $type = "", $enrich_params = [])
    {
        if ($type !== $this->get_id()) {
            return [
                'source_label' => $this->msg['artecampus_title'],
                $this->get_id() => [
                    'content' => '',
                ],
            ];
        }

        global $charset, $msg;

        $logo = '<img src="./images/connecteurs/artecampus.svg" alt="'.htmlentities($this->msg['artecampus_title'], ENT_QUOTES, $charset).'"/>';
        if (empty($_SESSION['id_empr_session'])) {
            $button = '
            <input
                class="bouton" type="button"
                onclick="auth_popup(\'./ajax.php?module=ajax&categ=auth&callback_func=artecampus_callback_auth_popup\')"
                value="'.htmlentities($msg['artecampus_empr_login'], ENT_QUOTES, $charset).'"
            />';
        } else {

            if (!EmprOrm::exist($_SESSION['id_empr_session'])) {
                throw new \Exception('Empr does not exist');
            }

            $empr = new EmprOrm($_SESSION['id_empr_session']);
            $emails = explode(';', $empr->empr_mail);
            $email = $emails[0] ?? '';

            if (empty($email)) {
                throw new \Exception('Email not found');
            }

            $data = $this->generate_data($empr, $source_id);
            $action = static::LOGIN_URL . '?' . http_build_query(['hmac' => $this->generate_hmac($email, $source_id)]);

            $button = '
            <input class="bouton" type="submit" form="'. $this->get_id() .'_'. $source_id .'" value="'.htmlentities($msg['artecampus_see'], ENT_QUOTES, $charset).'">
            <form id="'. $this->get_id() .'_'. $source_id .'" action="'. $action .'" target="_blank" method="post">
                <input type="hidden" name="data" value="'. htmlentities(encoding_normalize::json_encode($data), ENT_QUOTES, $charset) .'">
            </form>';
        }

        return [
            'source_label' => '',
            $this->get_id() => [
                'logo' => $logo,
                'btn' => $button,
                'content' => $logo . $button,
            ],
        ];
    }

    /**
     * Renvoie l'UAI
     *
     * @param integer $location
     * @param integer $source_id
     * @return string|false
     */
    protected function fetch_uai(int $location, int $source_id)
    {
        $parameters = $this->fetch_parameters($source_id);
        $artecampus_locations = $parameters['artecampus_locations'] ?? [];

        foreach ($artecampus_locations as $artecampus_location) {
            if ($artecampus_location['id'] == $location) {
                return $artecampus_location['uai'];
            }
        }
        return false;
    }

    /**
     * Renvoie le role
     *
     * @param integer $categ
     * @param integer $source_id
     * @return string|false
     */
    protected function fetch_user_role(int $categ, int $source_id)
    {
        $parameters = $this->fetch_parameters($source_id);
        $artecampus_roles = $parameters['artecampus_roles'] ?? [];

        foreach ($artecampus_roles as $artecampus_role) {
            if ($artecampus_role['id_categ'] == $categ) {
                return $artecampus_role['role'];
            }
        }
        return false;
    }

    /**
     * Renvoie le hash HMAC
     *
     * @param string $email
     * @param integer $source_id
     * @return string
     */
    public function generate_hmac(string $email, int $source_id)
    {
        $parameters = $this->fetch_parameters($source_id);
        $artecampus_key = $parameters['artecampus_key'] ?? '';

        $hash = hash_hmac('sha256', $email . " " . date("Y-m-d"), $artecampus_key, true);
        return base64_encode($hash);
    }

    /**
     * Renvoie les payload
     *
     * @param EmprOrm $empr
     * @param integer $source_id
     * @return array{firstName: string, lastName: string, email: string, uai: string, userRole: string}
     */
    public function generate_data(EmprOrm $empr, int $source_id)
    {
        $emails = explode(';', $empr->empr_mail);
        $email = $emails[0] ?? '';

        if (empty($email)) {
            throw new \Exception('Email not found');
        }

        return [
            'firstName' => $empr->empr_prenom,
            'lastName' => $empr->empr_nom,
            'email' => $email,
            'uai' => $this->fetch_uai($empr->empr_location, $source_id),
            'userRole' => $this->fetch_user_role($empr->empr_categ, $source_id),
        ];
    }

     /**
      *
      * {@inheritDoc}
      * @see connector::get_id()
      */
     public function get_id()
    {
        return "artecampus";
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::is_repository()
     */
    public function is_repository()
    {
        return static::REPOSITORY_YES;
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::enrichment_is_allow()
     */
    public function enrichment_is_allow()
    {
        return connector::ENRICHMENT_YES;
    }

    /**
     * M.A.J. Entrepot lie a une source
     *
     * @param int $source_id ID de la source
     * @param string $callback_progress Fonction de progression
     * @param boolean $recover
     * @param string $recover_env
     * @return int Nombre de documents mis a jour
     */
    public function maj_entrepot($source_id, $callback_progress = "", $recover = false, $recover_env = "")
    {
        global $base_path;

        $this->callback_progress = $callback_progress;
        $this->fetch_global_properties();

        $parameters = $this->fetch_parameters($source_id);
        $artecampus_url = $parameters['artecampus_url'] ?? '';
        if (empty($artecampus_url)) {
            $this->error = true;
            $this->error_message = $this->msg["artecampus_error_curl"];
            return 0;
        }

        $file_tmp = $base_path . '/temp/artecampus.json';
        if (!file_exists($file_tmp)) {
            $curl = new Curl();
            $curl->timeout = 60;
            $curl->set_option('CURLOPT_SSL_VERIFYPEER', false);
            @mysql_set_wait_timeout();

            $response = $curl->get($artecampus_url);
            if ($response->headers['Status-Code'] != 200) {
                $this->error = true;
                $this->error_message = $this->msg["artecampus_error_curl"];
                return 0;
            }

            // On verifie que c'est bien du JSON
            $tmp_json_content = json_decode($response->body, true);
            if (empty($tmp_json_content)) {
                $this->error = true;
                $this->error_message = $this->msg["artecampus_error_curl"];
                return 0;
            }

            unset($tmp_json_content);
            $size = file_put_contents($file_tmp, $response->body);
            if ($size === false) {
                $this->error = true;
                $this->error_message = $this->msg["artecampus_error_curl"];
                return 0;
            }

            $json_string = $response->body;
        } else {
            $json_string = file_get_contents($file_tmp);
        }

        $json_content = json_decode($json_string, true);
        if (empty($json_content)) {
            if (file_exists($file_tmp)) {
                unlink($file_tmp);
            }
            return 0;
        }

        $this->source_id = $source_id;
        $this->n_recu = 0;
        $this->n_total = count($json_content);

        foreach ($json_content as $record) {
            $unimarc_record = $this->get_unimarc_record($record);
            $statut = $this->rec_record($unimarc_record, $source_id);

            $this->n_recu++;
            $this->progress();

            if ($statut === false) {
                break;
            }
        }

        if (file_exists($file_tmp)) {
            unlink($file_tmp);
        }

        return $this->n_recu;
    }

    /**
     * Progress
     *
     * @return void
     */
    protected function progress()
    {
        $callback_progress = $this->callback_progress;
        if ($this->n_total) {
            $percent = ($this->n_recu / $this->n_total);
        } else {
            $percent = 0;
        }
        call_user_func($callback_progress, $percent, $this->n_recu, $this->n_total ?? 'inconnu');
    }

    /**
     * Verifie si un record existe
     *
     * @param integer $source_id
     * @param string $id_unimarc
     * @return int
     */
    protected function record_exist(int $source_id, string $id_unimarc)
    {
        $source_id = intval($source_id);
        $query = "select 1 from entrepot_source_".$source_id." where ref='".addslashes($id_unimarc)."'";
        $result = pmb_mysql_query($query);
        return pmb_mysql_num_rows($result) > 0;
    }

    /**
     * Nettoyage du texte
     *
     * @param string $value
     * @return string
     */
    protected function clean(string $value)
    {
        global $charset;

        if ($charset != "utf-8") {
            $value = encoding_normalize::clean_cp1252($value, 'utf-8');
            $value = encoding_normalize::utf8_decode($value);
        }
        return $value;
    }

    protected function insert_fields($source_id, $unimarc_record, $date_import, $recid, $search_id = '')
    {
        $field_order = 0;
        $id_unimarc = $unimarc_record['001'][0];

        foreach ($unimarc_record as $field => $val) {
            for ($i = 0; $i < count($val); $i++) {
                if (is_array($val[$i])) {
                    foreach ($val[$i] as $sfield => $vals) {
                        for ($j = 0; $j < count($vals); $j++) {
                            $this->insert_content_into_entrepot(
                                $source_id,
                                $id_unimarc,
                                $date_import,
                                $field,
                                $sfield,
                                $field_order,
                                $j,
                                $this->clean($vals[$j] ?? ''),
                                $recid,
                                $search_id
                            );
                        }
                    }
                } else {
                    $this->insert_content_into_entrepot(
                        $source_id,
                        $id_unimarc,
                        $date_import,
                        $field,
                        '',
                        $field_order,
                        0,
                        $this->clean($val[$i] ?? ''),
                        $recid,
                        $search_id
                    );
                }
                $field_order++;
            }
        }
    }

    /**
     * Ajout de la notice dans l'entrepot
     *
     * @param array $unimarc_record
     * @param integer $source_id
     * @param string $search_id
     * @return bool
     */
    protected function rec_record(array $unimarc_record, int $source_id, $search_id = '')
    {
        $id_unimarc = $unimarc_record['001'][0];
        if (empty($id_unimarc)) {
            return false;
        }

        if ($this->record_exist($source_id, $id_unimarc)) {
            return true;
        }

        // Si pas de conservation des anciennes notices, on supprime
        if ($this->del_old) {
            $this->delete_from_entrepot($source_id, $id_unimarc);
            $this->delete_from_external_count($source_id, $id_unimarc);
        }

        // On recupere la date d'import
        $date_import = date("Y-m-d H:i:s", time());

        $recid = $this->insert_into_external_count($source_id, $id_unimarc);

        //Insertion de l'entete
        $n_header = [];
        $n_header["rs"] = "*";
        $n_header["ru"] = "*";
        $n_header["el"] = "1";
        $n_header["bl"] = "m";
        $n_header["hl"] = "0";
        $n_header["dt"] = "g";

        //Recuperation d'un ID
        foreach ($n_header as $hc => $code) {
            $this->insert_header_into_entrepot($source_id, $id_unimarc, $date_import, $hc, $code, $recid, $search_id);
        }

        $this->insert_fields($source_id, $unimarc_record, $date_import, $recid, $search_id);
        $this->rec_isbd_record($source_id, $id_unimarc, $recid);

        return true;
    }

    /**
     * Convert un record d'artecampus en record Unimarc
     *
     * @param array $record
     * @return array
     */
    protected function get_unimarc_record($record)
    {
        $unimarc = [];

        $unimarc['001'][0] = $this->get_id().':'.$record['codeEmission'];

        // Titre
        $unimarc['200'][0]['a'][0] = $record['editorial']['title'];

        // Sous-titre
        if (!empty($record['editorial']['subtitle'])) {
            $unimarc['200'][0]['e'][0] = $record['editorial']['subtitle'];
        }

        // Resume
        if (!empty($record['editorial']['shortDescription'])) {
            $unimarc['330'][0]['a'][0] = $record['editorial']['shortDescription'];
        }
        if (!empty($record['editorial']['description'])) {
            if (empty($unimarc['330'][0]['a'][0])) {
                $unimarc['330'][0]['a'][0] = $record['editorial']['description'];
            } else {
                $unimarc['330'][0]['a'][0] .= "\n".$record['editorial']['description'];
            }
        }

        // Mots-cles
        if (!empty($record['editorial']['subjects'])) {
            $regroup_keywords = "";
            foreach($record['editorial']['subjects'] as $cle => $keyword) {
                if (!empty(trim($keyword))) {
                    if (array_key_last($record['editorial']['keywords']) == $cle) {
                        $regroup_keywords .= trim($keyword);
                    } else {
                        $regroup_keywords .= trim($keyword) .";";
                    }
                }
            }
            if (!empty($regroup_keywords)) {
                $unimarc['610'][0]['a'][0] = trim($regroup_keywords);
            }
        }
        if (!empty($record['editorial']['keywords'])) {
            $regroup_keywords = "";
            foreach($record['editorial']['keywords'] as $cle => $keyword) {
                if (!empty(trim($keyword))) {
                    if (array_key_last($record['editorial']['keywords']) == $cle) {
                        $regroup_keywords .= trim($keyword);
                    } else {
                        $regroup_keywords .= trim($keyword) .";";
                    }
                }
            }
            if (!empty($regroup_keywords)) {
                if (empty($unimarc['610'][0]['a'][0])) {
                    $unimarc['610'][0]['a'][0] = trim($regroup_keywords);
                } else {
                    $unimarc['610'][0]['a'][0] .= trim($regroup_keywords);
                }
            }
        }

        // Date de publication
        if (!empty($record['technical']['productionYear'])) {
            $unimarc['210'][0]['d'][0] = $record['technical']['productionYear'];
        }

        // Duree en minutes
        if (!empty($record['technical']['duration'])) {
            $unimarc['215'][0]['a'][0] = $record['technical']['duration']."min";
        }

        // Langue
        if (!empty($record['technical']['versions'])) {
            foreach ($record['technical']['versions'] as $lang) {
                $unimarc['101'][] = [
                    'a' => [$this->get_correspondence_language($lang['language'])],
                ];
            }
        }

        if (!empty($record['staff']['director'])) {
            $unimarc['700'][0]['a'][0] = $record['staff']['director'];
            $unimarc['700'][0]['4'][0] = '300';
        }
        if (!empty($record['staff']['actors'])) {
            $unimarc['702'][0]['a'][0] = $record['staff']['actors'];
            $unimarc['702'][0]['4'][0] = '005';
        }
        if (!empty($record['staff']['producers'])) {
            $unimarc['701'][0]['a'][0] = $record['staff']['producers'];
        }

        if (!empty($record['media']['poster'])) {
            $unimarc['896'][0]['a'][0] = $record['media']['poster'];
        }
        if (!empty($record['url'])) {
            $unimarc['856'][0]['u'][0] = $record['url'];
        }

        return $unimarc;
    }

    /**
     * Renvoie le type d'enrichissement
     *
     * @param int $source_id
     * @return array{source_id: int, type: array{code: string, label: string}[]}
     */
    public function getTypeOfEnrichment($source_id)
    {
        return [
            'source_id' => $source_id,
            'type' => [
                ['code' => $this->get_id(), 'label' => $this->msg['artecampus_title']],
            ],
        ];
    }

    /**
     * Recupere les proprietes de la nouvelle source
     *
     * @param int $source_id
     * @return void
     */
    public function make_serialized_source_properties($source_id)
    {
        global $artecampus_url, $artecampus_key, $artecampus_location_ids;

        $locations = [];
        $locations_ids = explode(',', $artecampus_location_ids);
        $artecampus_location_ids = [];
        foreach ($locations_ids as $location_id) {
            $location_id = intval($location_id);
            if (empty($location_id) || !DocsLocationOrm::exist($location_id)) {
                continue;
            }

            $field_uai = 'artecampus_location_' . $location_id;
            global ${$field_uai};

            $uai = trim(${$field_uai});
            if (empty($uai)) {
                continue;
            }

            $artecampus_location_ids[] = $location_id;
            $locations[] = [
                'id' => $location_id,
                'uai' => $uai,
            ];
        }
        $artecampus_location_ids = implode(',', $artecampus_location_ids);

        $roles = [];
        foreach (EmprCategOrm::findAll() as $categ) {
            $field_role = 'artecampus_role_' . $categ->id_categ_empr;
            global ${$field_role};

            $role = strtolower(${$field_role});
            if (empty($role) || !in_array($role, static::VALID_ROLES)) {
                continue;
            }

            $roles[] = [
                'id_categ' => $categ->id_categ_empr,
                'role' => $role,
            ];
        }

        $this->sources[$source_id]['PARAMETERS'] = serialize([
            'artecampus_url' => trim($artecampus_url),
            'artecampus_key' => trim($artecampus_key),
            'artecampus_locations' => $locations,
            'artecampus_location_ids' => $artecampus_location_ids,
            'artecampus_roles' => $roles,
        ]);
    }

    /**
     * Recupere les proprietes de la source
     *
     * @param int $source_id
     * @return array
     */
    protected function fetch_parameters($source_id)
    {
        $params = $this->get_source_params($source_id);
        return unserialize($params['PARAMETERS']);
    }

    /**
     * Formulaire pour les proprietes de la nouvelle source
     *
     * @param int $source_id
     * @return string
     */
    public function source_get_property_form($source_id)
    {
        global $charset;

        $parameters = $this->fetch_parameters($source_id);

        $artecampus_url = $parameters['artecampus_url'] ?? '';
        $artecampus_key = $parameters['artecampus_key'] ?? '';
        $artecampus_locations = $parameters['artecampus_locations'] ?? [];
        $artecampus_location_ids = $parameters['artecampus_location_ids'] ?? '';
        $artecampus_roles = $parameters['artecampus_roles'] ?? [];

        $location_table_style = empty($artecampus_location_ids) ? 'style="display:none;"' : '';

        return '
        <div class="row">
            <div class="colonne3">
                <label for="artecampus_url">'.htmlentities($this->msg['artecampus_url'], ENT_QUOTES, $charset).'</label>
            </div>
            <div class="colonne_suite">
                <input type="text" required class="saisie-30em" name="artecampus_url" id="artecampus_url"  autocomplete="off" placeholder="https://campus.arte.tv/" value="'. htmlentities($artecampus_url, ENT_QUOTES, $charset) .'">
            </div>
        </div>
        <div class="row">
            <div class="colonne3">
                <label for="artecampus_key">'.htmlentities($this->msg['artecampus_key'], ENT_QUOTES, $charset).'</label>
            </div>
            <div class="colonne_suite">
                <input type="password" required class="saisie-30em" name="artecampus_key" id="artecampus_key" autocomplete="off" value="'. htmlentities($artecampus_key, ENT_QUOTES, $charset) .'"">
                <span class="fa fa-eye" onclick="toggle_password(this, \'artecampus_key\');"></span>
            </div>
        </div>
        <div class="row">
            <div class="colonne3">
                <label for="artecampus_location">'.htmlentities($this->msg['artecampus_location_label'], ENT_QUOTES, $charset).'</label>
            </div>
            <div class="colonne_suite">
                <select name="artecampus_location" id="artecampus_location" class="saisie-30em">
                    <option value="" disabled selected>'.htmlentities($this->msg['artecampus_location_default'], ENT_QUOTES, $charset).'</option>
                    '. $this->get_location_options($artecampus_location_ids) .'
                </select>
                <button id="artecampus_add_location" type="button" class="bouton" onclick="add_location()">'.htmlentities($this->msg['artecampus_add'], ENT_QUOTES, $charset).'</button>
            </div>
        </div>
        <div class="row">
            <div class="colonne3">&nbsp;</div>
            <div class="colonne_suite">
                <input type="hidden" name="artecampus_location_ids" id="artecampus_location_ids" value="'. htmlentities($artecampus_location_ids, ENT_QUOTES, $charset) .'">
                <table id="artecampus_location_table" '.$location_table_style.'>
                    <thead>
                        <tr>
                            <th>'.htmlentities($this->msg['artecampus_location'], ENT_QUOTES, $charset).'</th>
                            <th>'.htmlentities($this->msg['artecampus_uai'], ENT_QUOTES, $charset).'</th>
                            <th>'.htmlentities($this->msg['artecampus_actions'], ENT_QUOTES, $charset).'</th>
                        </tr>
                    </thead>
                    <tbody id="artecampus_location_table_body">'. $this->get_location_table($artecampus_locations)  .'</tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="colonne3">
                <label for="artecampus_role">'.htmlentities($this->msg['artecampus_role_label'], ENT_QUOTES, $charset).'</label>
            </div>
            <div class="colonne_suite">
                <table id="artecampus_role_table">
                    <thead>
                        <tr>
                            <th>'.htmlentities($this->msg['artecampus_categorie'], ENT_QUOTES, $charset).'</th>
                            <th>'.htmlentities($this->msg['artecampus_role'], ENT_QUOTES, $charset).'</th>
                        </tr>
                    </thead>
                    <tbody id="artecampus_role_table_body">
                        '. $this->get_role_table($artecampus_roles) .'
                    </tbody>
                </table>
            </div>
        </div>' . $this->source_get_javascript_form();
    }

    /**
     * Retourne le code javascript du formulaire
     *
     * @return string
     */
    protected function source_get_javascript_form()
    {
        global $charset;

        return '
        <template id="artecampus_location_template">
            <tr>
                <td class="location_label"></td>
                <td class="location_uai">
                    <input type="text" name="" placeholder="000000"  autocomplete="off" class="saisie-30em" value="" required>
                </td>
                <td class="location_action">
                    <button type="button" class="bouton">'.htmlentities($this->msg['artecampus_delete'], ENT_QUOTES, $charset).'</button>
                </td>
            </tr>
        </template>
        <script>
            function add_location() {

                let select = document.getElementById("artecampus_location");
                if (select.value === "") {
                    // Valeur par defaut, on ne fait rien
                    return false;
                }

                let table = document.getElementById("artecampus_location_table");
                table.removeAttribute("style");

                let location_ids = document.getElementById("artecampus_location_ids");
                if (!location_ids) {
                    throw new Error("input[id=artecampus_location_ids] not found");
                }

                let list = [];
                if (location_ids.value != "") {
                    list = location_ids.value.split(",");
                }

                list.push(select.value);
                location_ids.value = [...new Set(list)].join(",");

                const option = select.selectedOptions[0];
                option.setAttribute("style", "display:none");
                select.value = "";

                const template = document.getElementById("artecampus_location_template");
                const tr = template.content.cloneNode(true).querySelector("tr");
                tr.setAttribute("id", "artecampus_location_" + parseInt(option.value).toString());

                let td = tr.querySelector("td.location_label");
                td.innerText = option.label;

                let input = tr.querySelector("td.location_uai > input");
                input.name = "artecampus_location_" + parseInt(option.value).toString();

                let button = tr.querySelector("td.location_action > button");
                button.addEventListener("click", remove_location.bind(null, option.value));

                let tbody = document.getElementById("artecampus_location_table_body");
                tbody.appendChild(tr);
            }

            function remove_location(id) {
                const tr = document.getElementById("artecampus_location_" + id.toString());
                if (tr) {
                    tr.remove();

                    let location_ids = document.getElementById("artecampus_location_ids");
                    if (location_ids) {
                        let list = [];
                        if (location_ids.value != "") {
                            list = location_ids.value.split(",");
                        }

                        list = list.filter((value) => { return value != id; });
                        location_ids.value = [...new Set(list)].join(",");
                    }
                }

                const select = document.getElementById("artecampus_location");
                if (select) {
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value == id) {
                            select.options[i].removeAttribute("style");
                            break;
                        }
                    }
                }

                let table = document.getElementById("artecampus_location_table");
                if (table && table.tBodies[0].childNodes.length == 0) {
                    table.setAttribute("style", "display:none");
                }
            }
        </script>
        ';
    }

    /**
     * Formulaire pour les proprietes du connecteur
     *
     * @return string
     */
    public function get_property_form()
    {
        return '';
    }

    /**
     * Recupere les options des locations
     *
     * @param string $artecampus_location_ids
     * @return string
     */
    protected function get_location_options(string $artecampus_location_ids)
    {
        global $charset;

        $artecampus_location_ids = explode(',', $artecampus_location_ids);

        $html = '';
        foreach (DocsLocationOrm::findAll() as $location) {
            $style = in_array($location->idlocation, $artecampus_location_ids) ? 'style="display:none"' : '';

            $html .= '<option value="'.htmlentities($location->idlocation, ENT_QUOTES, $charset).'" '.$style.'>';
            $html .= htmlentities($location->location_libelle, ENT_QUOTES, $charset);
            $html .= '</option>';
        }

        return $html;
    }

    /**
     * Recupere le tableau des roles
     *
     * @return string
     */
    protected function get_role_table(array $artecampus_roles)
    {
        global $charset;

        $html = '';
        foreach (EmprCategOrm::findAll() as $categ) {
            $role = '';
            foreach ($artecampus_roles as $artecampus_role) {
                if ($artecampus_role['id_categ'] == $categ->id_categ_empr) {
                    $role = $artecampus_role['role'];
                    break;
                }
            }

            if (!in_array($role, static::VALID_ROLES, true)) {
                // role par defaut
                $role = '';
            }

            $teacher_selected = $role == 'teacher' ? 'selected' : '';
            $student_selected = $role == 'student' ? 'selected' : '';
            $default_selected = $role == '' ? 'selected' : '';

            $html .= '
            <tr>
                <td>'.htmlentities($categ->libelle, ENT_QUOTES, $charset).'</td>
                <td>
                    <select name="artecampus_role_'.$categ->id_categ_empr.'" id="artecampus_role_'.$categ->id_categ_empr.'" class="saisie-30em" required>
                        <option value="" '. $default_selected .'>'.htmlentities($this->msg['artecampus_role_default'], ENT_QUOTES, $charset).'</option>
                        <option value="teacher" '. $teacher_selected .'>'.htmlentities($this->msg['artecampus_role_teacher'], ENT_QUOTES, $charset).'</option>
                        <option value="student" '. $student_selected .'>'.htmlentities($this->msg['artecampus_role_student'], ENT_QUOTES, $charset).'</option>
                    </select>
                </td>
            </tr>';
        }

        return $html;
    }

    /**
     * Retourne le tableau des locations
     *
     * @param array $artecampus_locations
     * @return string
     */
    protected function get_location_table(array $artecampus_locations)
    {
        global $charset;

        $html = '';
        foreach ($artecampus_locations as $artecampus_location) {
            if (empty($artecampus_location['id']) || !DocsLocationOrm::exist($artecampus_location['id'])) {
                continue;
            }

            $location = new DocsLocationOrm($artecampus_location['id']);
            $html .= '
            <tr id="artecampus_location_'.$location->idlocation.'">
                <td class="location_label">'.htmlentities($location->location_libelle, ENT_QUOTES, $charset).'</td>
                <td class="location_uai">
                    <input type="text" name="artecampus_location_'.$location->idlocation.'" placeholder="000000" autocomplete="off" class="saisie-30em" value="'.htmlentities($artecampus_location['uai'], ENT_QUOTES, $charset).'" required>
                </td>
                <td class="location_action">
                    <button type="button" class="bouton" onclick="remove_location('.$location->idlocation.')">'.htmlentities($this->msg['artecampus_delete'], ENT_QUOTES, $charset).'</button>
                </td>
            </tr>';
        }

        return $html;
    }

    /**
     * Retourne la langue de PMB correspondante a la langue d'artecampus
     *
     * @param string $artecampus_language
     * @return string
     */
    protected function get_correspondence_language(string $artecampus_language)
    {
        $lang = "";
        switch (trim($artecampus_language)) {
            case "fr":
                $lang = "fre";
                break;
            case "de":
                $lang = "ger";
                break;
            case "en":
                $lang = "eng";
                break;
            case "es":
                $lang = "spa";
                break;
            case "it":
                $lang = "ita";
                break;
            default:
                $lang = "";
                break;
        }
        return $lang;
    }
}
