<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
 *
 *   Module               : Exportation SIECLE-BEE XML & ZIP
 ***************************************************************************/

if (!defined('REPADMIN')) {
    include_once(__DIR__ . "/../common/config.inc.php");
}
if (file_exists(__DIR__ . "/../common/config2.inc.php")) {
    include_once(__DIR__ . "/../common/config2.inc.php");
}
if (!defined('SECURITE')) {
    define('SECURITE', 1);
}
include_once(__DIR__ . "/db_triade.php");
include_once(__DIR__ . "/timezone.php");

/**
 * Normalise un numéro de téléphone au format international E.164.
 *
 * @param string $phone
 * @param string $default_country_code Indicatif par défaut (ex: '33' pour la France)
 * @return string Numéro au format E.164 (ex: +33612345678) ou chaîne vide si invalide
 */
function siecle_bee_export_format_phone($phone, $default_country_code = '33') {
    $phone = trim((string)$phone);
    if ($phone === '') {
        return '';
    }

    if (strpos($phone, '+') === 0) {
        $digits = preg_replace('/[^0-9]/', '', substr($phone, 1));
        if (strlen($digits) >= 6 && strlen($digits) <= 15) {
            return '+' . $digits;
        }
        return '';
    }

    if (strpos($phone, '00') === 0) {
        $digits = preg_replace('/[^0-9]/', '', substr($phone, 2));
        if (strlen($digits) >= 6 && strlen($digits) <= 15) {
            return '+' . $digits;
        }
        return '';
    }

    $digits = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($digits) === 10 && strpos($digits, '0') === 0) {
        return '+' . $default_country_code . substr($digits, 1);
    }
    if (strlen($digits) === 9 && strpos($digits, '0') !== 0) {
        return '+' . $default_country_code . $digits;
    }

    if (strlen($digits) >= 6 && strlen($digits) <= 15) {
        return '+' . $default_country_code . $digits;
    }

    return '';
}

/**
 * Nettoie une chaîne de texte selon les règles de caractères autorisés SIECLE.
 *
 * @param string $str
 * @param int|null $max_len
 * @param bool $uppercase
 * @return string
 */
function siecle_bee_export_clean_name($str, $max_len = 100, $uppercase = true) {
    $str = trim((string)$str);
    if ($str === '') {
        return '';
    }

    $str = preg_replace('/\s+/', ' ', $str);
    $str = preg_replace('/\'\'/', '\'', $str);

    if ($uppercase) {
        if (function_exists('mb_strtoupper')) {
            $str = mb_strtoupper($str, 'UTF-8');
        } else {
            $str = strtoupper($str);
        }
    }

    if ($max_len !== null && $max_len > 0) {
        if (function_exists('mb_substr')) {
            $str = mb_substr($str, 0, $max_len, 'UTF-8');
        } else {
            $str = substr($str, 0, $max_len);
        }
    }

    return trim($str);
}

/**
 * Valide une adresse email selon les contraintes SIECLE (RFC 3696).
 *
 * @param string $email
 * @return string Email valide ou chaîne vide
 */
function siecle_bee_export_clean_email($email) {
    $email = trim((string)$email);
    if ($email === '' || strpos($email, '-') === 0 || strlen($email) > 254) {
        return '';
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }
    return '';
}
/**
 * Convertit un identifiant ou code de civilite TRIADE en code civilite SIECLE.
 *
 * @param int|string $civ
 * @return string '1' (Monsieur) | '2' (Madame)
 */
function siecle_bee_export_civilite_to_siecle($civ) {
    $c = strtoupper(trim((string)$civ));
    if ($c === '0' || $c === 'M.' || $c === 'M' || $c === 'MONSIEUR' || $c === 'MR') {
        return '1';
    }
    if ($c === '1' || $c === '2' || $c === 'MME' || $c === 'MADAME' || $c === 'MLLE' || $c === 'MADEMOISELLE') {
        return '2';
    }
    return '1';
}


/**
 * Convertit un libelle ou code regime TRIADE en code regime SIECLE.
 *
 * @param string $regime
 * @return string Code regime SIECLE ('0': Externe libre, '2': Demi-pensionnaire, '3': Interne, etc.)
 */
function siecle_bee_export_regime_to_siecle($regime) {
    $r = strtoupper(trim((string)$regime));
    if ($r === 'EXTERNE' || $r === 'EXTERNE LIBRE' || $r === '0' || $r === 'E') {
        return '0';
    }
    if ($r === 'EXTERNE SURVEILLE' || $r === '1') {
        return '1';
    }
    if ($r === 'DEMI PENSION' || $r === 'DEMI-PENSION' || $r === 'DEMI PENSIONNAIRE' || $r === '2' || $r === 'DP') {
        return '2';
    }
    if ($r === 'INTERNE' || $r === 'INTERNE DANS L\'ETABLISSEMENT' || $r === '3' || $r === 'INT') {
        return '3';
    }
    if ($r === 'INTERNE EXTERNE' || $r === '4') {
        return '4';
    }
    if ($r === 'INTERNE HEBERGE' || $r === '5') {
        return '5';
    }
    if ($r === 'DEMI-PENSIONNAIRE HORS L\'ETABLISSEMENT' || $r === '6') {
        return '6';
    }
    return '0';
}

/**
 * Normalise le code PCS profession pour SIECLE.
 *
 * @param string $code
 * @return string Code PCS 2 caracteres (redirection 11,12,13 vers 10, defaut '99')
 */
function siecle_bee_export_profession_to_siecle($code) {
    $c = trim((string)$code);
    if ($c === '11' || $c === '12' || $c === '13') {
        return '10';
    }
    $valid_codes = array(
        '10', '21', '22', '23', '31', '33', '34', '35', '37', '38',
        '42', '43', '44', '45', '46', '47', '48', '52', '53', '54',
        '55', '56', '62', '63', '64', '65', '67', '68', '69', '71',
        '72', '74', '75', '77', '78', '81', '83', '84', '85', '86', '99'
    );
    if (in_array($c, $valid_codes, true)) {
        return $c;
    }
    return '99';
}

/**
 * Convertit un libelle ou code sexe en code SIECLE ('1' masculin, '2' feminin).
 *
 * @param string $sexe
 * @return string '1' | '2'
 */
function siecle_bee_export_sexe_to_siecle($sexe) {
    $s = strtolower(trim((string)$sexe));
    if ($s === 'f' || $s === '2' || $s === 'feminin' || $s === 'fille') {
        return '2';
    }
    return '1';
}

/**
 * Recupere ou incremente le numero d'envoi pour l'etablissement.
 *
 * @param string $uai
 * @param bool $increment
 * @return int Numero d'envoi (entier positif)
 */
function siecle_bee_get_num_envoi($uai, $increment = false) {
    global $cnx;
    if (!$cnx && function_exists('cnx')) {
        $cnx = @cnx();
    }
    if (!$cnx) {
        return 1;
    }

    $key = "##siecle_num_envoi_{$uai}##";
    $data = aff_enr_parametrage($key);
    $current = 1;
    if (countTriade($data) > 0 && isset($data[0][1]) && is_numeric($data[0][1])) {
        $current = (int)$data[0][1];
    }

    if ($increment) {
        $next = $current + 1;
        enr_parametrage($key, (string)$next);
    }

    return $current;
}

/**
 * Recupere les donnees d'etablissement et pre-remplit les parametres par defaut.
 *
 * @return array
 */
function siecle_bee_get_etablissement_info() {
    global $cnx, $prefixe;
    if (!$cnx) {
        $cnx = cnx();
    }

    $info = array(
        'uai'            => '',
        'nom_ecole'      => '',
        'annee_scolaire' => '',
        'adresse'        => '',
        'postal'         => '',
        'ville'          => ''
    );

    $sql = "SELECT nom_ecole, adresse, postal, ville, annee_scolaire FROM {$prefixe}info_ecole WHERE id='1'";
    $res = execSql($sql);
    $data = chargeMat($res);
    if (countTriade($data) > 0) {
        $info['nom_ecole']      = trim((string)$data[0][0]);
        $info['adresse']        = trim((string)$data[0][1]);
        $info['postal']         = trim((string)$data[0][2]);
        $info['ville']          = trim((string)$data[0][3]);
        $info['annee_scolaire'] = trim((string)$data[0][4]);
    }

    $sql_uai = "SELECT numero_etablissement FROM {$prefixe}eleves WHERE numero_etablissement IS NOT NULL AND numero_etablissement != '' LIMIT 1";
    $res_uai = execSql($sql_uai);
    $data_uai = chargeMat($res_uai);
    if (countTriade($data_uai) > 0 && preg_match('/^[0-9]{7}[A-Z]$/i', trim((string)$data_uai[0][0]))) {
        $info['uai'] = strtoupper(trim((string)$data_uai[0][0]));
    }

    if (preg_match('/^(\d{4})/', $info['annee_scolaire'], $m)) {
        $info['annee_millesime'] = $m[1];
    } else {
        $info['annee_millesime'] = date('Y');
    }

    return $info;
}

/**
 * Recupere la liste de toutes les classes disponibles pour selection.
 *
 * @return array Liste des classes indexees par code_class
 */
function siecle_bee_get_classes_list() {
    global $cnx, $prefixe;
    if (!$cnx) {
        $cnx = cnx();
    }

    $classes = array();
    $sql = "SELECT code_class, libelle FROM {$prefixe}classes ORDER BY libelle ASC";
    $res = execSql($sql);
    $data = chargeMat($res);
    if (countTriade($data) > 0) {
        foreach ($data as $row) {
            $classes[$row[0]] = array(
                'code_class' => (int)$row[0],
                'libelle'    => trim((string)$row[1])
            );
        }
    }
    return $classes;
}
/**
 * Extrait et structure l'ensemble des donnees d'exportation SIECLE-BEE depuis la BDD TRIADE.
 *
 * @param string $uai Code UAI de l'etablissement (8 caracteres)
 * @param string $annee Annee scolaire (4 chiffres, ex: 2024)
 * @param array $options Options de filtre (classes, profil standard/ephc, etc.)
 * @return array Donnees structurees pretes pour la generation XML
 */
function siecle_bee_get_export_data($uai, $annee, $options = array()) {
    global $cnx, $prefixe;
    if (!$cnx) {
        $cnx = cnx();
    }

    $profil = isset($options['profil']) && $options['profil'] === 'ephc' ? 'ephc' : 'standard';
    $classes_filtre = isset($options['classes']) && is_array($options['classes']) && !empty($options['classes']) ? $options['classes'] : array();

    $where_classe = "";
    if (!empty($classes_filtre)) {
        $safe_classes = array_map('intval', $classes_filtre);
        $where_classe = " AND e.classe IN (" . implode(',', $safe_classes) . ")";
    }

    $sql = "SELECT 
                e.elev_id, e.nom, e.prenom, e.classe, c.libelle as nom_classe,
                e.lv1, e.lv2, e.option, e.regime, e.date_naissance, e.lieu_naissance, e.nationalite,
                e.civ_1, e.nomtuteur, e.prenomtuteur, e.adr1, e.code_post_adr1, e.commune_adr1, e.tel_port_1,
                e.civ_2, e.nom_resp_2, e.prenom_resp_2, e.adr2, e.code_post_adr2, e.commune_adr2, e.tel_port_2,
                e.telephone, e.profession_pere, e.tel_prof_pere, e.profession_mere, e.tel_prof_mere,
                e.nom_etablissement, e.numero_etablissement, e.code_postal_etablissement, e.commune_etablissement,
                e.numero_eleve, e.email, e.email_eleve, e.email_resp_2, e.class_ant, e.annee_ant,
                e.tel_eleve, e.sexe, e.adr_eleve, e.ccp_eleve, e.commune_eleve, e.pays_eleve,
                e.boursier, e.compte_inactif, e.situation_familiale, e.ine,
                c.code_mef as classe_code_mef, c.niveau as classe_niveau
            FROM {$prefixe}eleves e
            LEFT JOIN {$prefixe}classes c ON c.code_class = e.classe
            WHERE 1=1 {$where_classe}
            ORDER BY c.libelle ASC, e.nom ASC, e.prenom ASC";

    $res = execSql($sql);
    $data_eleves = chargeMat($res);

    $personnes_map = array();
    $eleves_list   = array();
    $next_pers_id  = 1001;

    if (countTriade($data_eleves) > 0) {
        foreach ($data_eleves as $row) {
            $elev_id           = (int)$row[0];
            $nom_eleve         = siecle_bee_export_clean_name($row[1], 100, true);
            $prenom_eleve      = siecle_bee_export_clean_name($row[2], 100, true);
            $id_classe         = (int)$row[3];
            $nom_classe        = trim((string)$row[4]);
            $lv1               = trim((string)$row[5]);
            $lv2               = trim((string)$row[6]);
            $options_str       = trim((string)$row[7]);
            $regime_raw        = trim((string)$row[8]);
            $date_naiss_raw    = trim((string)$row[9]);
            $lieu_naiss_raw    = trim((string)$row[10]);
            $nationalite_raw   = trim((string)$row[11]);
            
            // Tuteur 1
            $civ_1             = $row[12];
            $nom_tut1          = siecle_bee_export_clean_name($row[13], 100, true);
            $prenom_tut1       = siecle_bee_export_clean_name($row[14], 100, true);
            $adr1              = trim((string)$row[15]);
            $cp1               = trim((string)$row[16]);
            $commune1          = trim((string)$row[17]);
            $tel_port1         = trim((string)$row[18]);

            // Tuteur 2
            $civ_2             = $row[19];
            $nom_tut2          = siecle_bee_export_clean_name($row[20], 100, true);
            $prenom_tut2       = siecle_bee_export_clean_name($row[21], 100, true);
            $adr2              = trim((string)$row[22]);
            $cp2               = trim((string)$row[23]);
            $commune2          = trim((string)$row[24]);
            $tel_port2         = trim((string)$row[25]);

            $tel_fixe_foyer    = trim((string)$row[26]);
            $prof_pere         = trim((string)$row[27]);
            $tel_prof_pere     = trim((string)$row[28]);
            $prof_mere         = trim((string)$row[29]);
            $tel_prof_mere     = trim((string)$row[30]);

            $etab_ant_nom      = trim((string)$row[31]);
            $etab_ant_rne      = trim((string)$row[32]);
            $etab_ant_cp       = trim((string)$row[33]);
            $etab_ant_commune  = trim((string)$row[34]);

            $num_eleve         = trim((string)$row[35]);
            $email_tut1        = siecle_bee_export_clean_email($row[36]);
            $email_eleve       = siecle_bee_export_clean_email($row[37]);
            $email_tut2        = siecle_bee_export_clean_email($row[38]);

            $class_ant         = trim((string)$row[39]);
            $annee_ant         = trim((string)$row[40]);
            $tel_eleve         = trim((string)$row[41]);
            $sexe_raw          = trim((string)$row[42]);

            $adr_el            = trim((string)$row[43]);
            $cp_el             = trim((string)$row[44]);
            $commune_el        = trim((string)$row[45]);
            $pays_el           = trim((string)$row[46]);

            $boursier          = (int)$row[47];
            $inactif           = (int)$row[48];
            $situation_fam     = trim((string)$row[49]);
            $ine_raw           = strtoupper(trim((string)$row[50]));

            if ($nom_eleve === '' || $prenom_eleve === '') {
                continue;
            }

            // Normalisation Date de naissance
            $date_naiss = '';
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date_naiss_raw)) {
                $date_naiss = $date_naiss_raw;
            } elseif (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date_naiss_raw, $m)) {
                $date_naiss = "{$m[3]}-{$m[2]}-{$m[1]}";
            }

            $code_sexe = siecle_bee_export_sexe_to_siecle($sexe_raw);
            $code_regime = siecle_bee_export_regime_to_siecle($regime_raw);

            $commune_insee_naiss = '';
            $ville_naiss = '';
            if (preg_match('/^[0-9]{5}$/', $lieu_naiss_raw)) {
                $commune_insee_naiss = $lieu_naiss_raw;
            } elseif ($lieu_naiss_raw !== '') {
                $ville_naiss = siecle_bee_export_clean_name($lieu_naiss_raw, 32, true);
            } else {
                $ville_naiss = 'PARIS';
            }

            $code_pays_nat = '100';
            if (preg_match('/^[0-9]{3}$/', $nationalite_raw)) {
                $code_pays_nat = $nationalite_raw;
            }

            $id_national = '';
            if (preg_match('/^[0-9A-Z]{11}$/', $ine_raw)) {
                $id_national = $ine_raw;
            }

            // Traitement des Responsables
            $responsables_eleve = array();

            // 1. Tuteur 1 (Representant Legal 1)
            if ($nom_tut1 !== '' && $prenom_tut1 !== '') {
                $pers_key = md5(strtolower($nom_tut1 . '_' . $prenom_tut1 . '_' . $cp1));
                if (!isset($personnes_map[$pers_key])) {
                    $id_pers = $next_pers_id++;
                    $personnes_map[$pers_key] = array(
                        'id_prv_per'       => $id_pers,
                        'code_civilite'    => siecle_bee_export_civilite_to_siecle($civ_1),
                        'nom_de_famille'   => $nom_tut1,
                        'nom_usage'        => '',
                        'prenom'           => $prenom_tut1,
                        'adresse'          => array(
                            'ligne1'       => substr($adr1, 0, 38),
                            'code_postal'  => preg_match('/^[0-9]{5}$/', $cp1) ? $cp1 : '75001',
                            'll_postal'    => siecle_bee_export_clean_name($commune1 ?: 'PARIS', 32, true),
                            'code_pays'    => '100'
                        ),
                        'code_profession'  => siecle_bee_export_profession_to_siecle($prof_pere ?: $prof_mere),
                        'tel_personnel'    => siecle_bee_export_format_phone($tel_fixe_foyer),
                        'tel_professionnel'=> siecle_bee_export_format_phone($tel_prof_pere ?: $tel_prof_mere),
                        'tel_portable'     => siecle_bee_export_format_phone($tel_port1),
                        'accepte_sms'      => $tel_port1 !== '' ? 'true' : 'false',
                        'email'            => $email_tut1,
                        'comm_adresse'     => 'true'
                    );
                }

                $responsables_eleve[] = array(
                    'type'                    => 'LEGAL',
                    'id_prv_per'              => $personnes_map[$pers_key]['id_prv_per'],
                    'code_parente'            => (siecle_bee_export_civilite_to_siecle($civ_1) === '1') ? '20' : '10',
                    'paie_frais_scolaires'    => 'true',
                    'heberge_eleve'           => 'true',
                    'percoit_les_aides'       => 'true',
                    'a_contacter_en_priorite' => 'true'
                );
            }

            // 2. Tuteur 2 (Representant Legal 2)
            if ($nom_tut2 !== '' && $prenom_tut2 !== '') {
                $pers_key2 = md5(strtolower($nom_tut2 . '_' . $prenom_tut2 . '_' . $cp2));
                if (!isset($personnes_map[$pers_key2])) {
                    $id_pers2 = $next_pers_id++;
                    $personnes_map[$pers_key2] = array(
                        'id_prv_per'       => $id_pers2,
                        'code_civilite'    => siecle_bee_export_civilite_to_siecle($civ_2),
                        'nom_de_famille'   => $nom_tut2,
                        'nom_usage'        => '',
                        'prenom'           => $prenom_tut2,
                        'adresse'          => array(
                            'ligne1'       => substr($adr2 ?: $adr1, 0, 38),
                            'code_postal'  => preg_match('/^[0-9]{5}$/', $cp2 ?: $cp1) ? ($cp2 ?: $cp1) : '75001',
                            'll_postal'    => siecle_bee_export_clean_name(($commune2 ?: $commune1) ?: 'PARIS', 32, true),
                            'code_pays'    => '100'
                        ),
                        'code_profession'  => siecle_bee_export_profession_to_siecle($prof_mere ?: $prof_pere),
                        'tel_personnel'    => siecle_bee_export_format_phone($tel_fixe_foyer),
                        'tel_professionnel'=> siecle_bee_export_format_phone($tel_prof_mere ?: $tel_prof_pere),
                        'tel_portable'     => siecle_bee_export_format_phone($tel_port2),
                        'accepte_sms'      => $tel_port2 !== '' ? 'true' : 'false',
                        'email'            => $email_tut2,
                        'comm_adresse'     => 'false'
                    );
                }

                $responsables_eleve[] = array(
                    'type'                    => 'LEGAL',
                    'id_prv_per'              => $personnes_map[$pers_key2]['id_prv_per'],
                    'code_parente'            => (siecle_bee_export_civilite_to_siecle($civ_2) === '1') ? '20' : '10',
                    'paie_frais_scolaires'    => empty($responsables_eleve) ? 'true' : 'false',
                    'heberge_eleve'           => ($adr2 === '' || $adr2 === $adr1) ? 'true' : 'false',
                    'percoit_les_aides'       => 'false',
                    'a_contacter_en_priorite' => empty($responsables_eleve) ? 'true' : 'false'
                );
            }

            if (empty($responsables_eleve)) {
                $pers_key_def = md5("defaut_{$elev_id}");
                $id_pers_def  = $next_pers_id++;
                $personnes_map[$pers_key_def] = array(
                    'id_prv_per'       => $id_pers_def,
                    'code_civilite'    => '1',
                    'nom_de_famille'   => $nom_eleve,
                    'nom_usage'        => '',
                    'prenom'           => 'RESPONSABLE',
                    'adresse'          => array(
                        'ligne1'       => 'ADRESSE NON RENSEIGNEE',
                        'code_postal'  => '75001',
                        'll_postal'    => 'PARIS',
                        'code_pays'    => '100'
                    ),
                    'code_profession'  => '99',
                    'tel_personnel'    => '',
                    'tel_professionnel'=> '',
                    'tel_portable'     => '',
                    'accepte_sms'      => 'false',
                    'email'            => '',
                    'comm_adresse'     => 'false'
                );

                $responsables_eleve[] = array(
                    'type'                    => 'LEGAL',
                    'id_prv_per'              => $id_pers_def,
                    'code_parente'            => '50',
                    'paie_frais_scolaires'    => 'true',
                    'heberge_eleve'           => 'true',
                    'percoit_les_aides'       => 'true',
                    'a_contacter_en_priorite' => 'true'
                );
            }

            // Options pedagogiques
            $options_list = array();
            if ($lv1 !== '') {
                $options_list[] = array('code_matiere' => substr(strtoupper(preg_replace('/[^0-9A-Z]/', '', $lv1)), 0, 6), 'modalite' => 'O');
            }
            if ($lv2 !== '') {
                $options_list[] = array('code_matiere' => substr(strtoupper(preg_replace('/[^0-9A-Z]/', '', $lv2)), 0, 6), 'modalite' => 'O');
            }
            if ($options_str !== '') {
                $opt_parts = explode(',', $options_str);
                foreach ($opt_parts as $op) {
                    $op_clean = substr(strtoupper(preg_replace('/[^0-9A-Z]/', '', trim($op))), 0, 6);
                    if ($op_clean !== '' && count($options_list) < 12) {
                        $options_list[] = array('code_matiere' => $op_clean, 'modalite' => 'F');
                    }
                }
            }

            $classe_code_mef = isset($row[51]) ? trim((string)$row[51]) : '';
            $classe_niveau   = isset($row[52]) ? trim((string)$row[52]) : '';

            $code_mef = '';
            if (preg_match('/^[0-9]{11}$/', $classe_code_mef)) {
                $code_mef = $classe_code_mef;
            } elseif ($profil === 'ephc') {
                $code_mef = '99999902990';
            } else {
                // Déduction selon le nom de classe ou le niveau
                $nom_cl_clean = strtoupper($nom_classe);
                $niv_clean = strtoupper($classe_niveau);
                if (preg_match('/6(?:EME|E)?\b/', $nom_cl_clean) || strpos($niv_clean, '6') !== false) {
                    $code_mef = '10010012110'; // 6EME GENERALE
                } elseif (preg_match('/5(?:EME|E)?\b/', $nom_cl_clean) || strpos($niv_clean, '5') !== false) {
                    $code_mef = '10110001110'; // 5EME GENERALE
                } elseif (preg_match('/4(?:EME|E)?\b/', $nom_cl_clean) || strpos($niv_clean, '4') !== false) {
                    $code_mef = '10210001110'; // 4EME GENERALE
                } elseif (preg_match('/3(?:EME|E)?\b/', $nom_cl_clean) || strpos($niv_clean, '3') !== false) {
                    $code_mef = '10310001110'; // 3EME GENERALE
                } elseif (preg_match('/2(?:NDE|ND|E)?\b/', $nom_cl_clean) || strpos($niv_clean, '2') !== false) {
                    $code_mef = '20010002210'; // 2NDE GENERALE ET TECHNO
                } elseif (preg_match('/1(?:ERE|ER|E)?\b/', $nom_cl_clean) || strpos($niv_clean, '1') !== false) {
                    $code_mef = '20110010110'; // 1ERE GENERALE
                } elseif (preg_match('/TERM|TLE\b/', $nom_cl_clean)) {
                    $code_mef = '20210010110'; // TERMINALE GENERALE
                } else {
                    $code_mef = '10210001110'; // Défaut 4ème
                }
            }

            $annee_int = (int)$annee;
            $date_deb_sco = "{$annee_int}-09-01";
            $date_fin_sco = ($annee_int + 1) . "-07-07";

            $is_sortant = ($inactif === 1);

            $eleves_list[] = array(
                'elev_id'             => $elev_id,
                'is_sortant'          => $is_sortant,
                'date_sortie'         => $is_sortant ? date('Y-m-d') : '',
                'code_motif_sortie'   => $is_sortant ? '10' : '',
                'id_national'         => $id_national,
                'nom_de_famille'      => $nom_eleve,
                'nom_usage'           => '',
                'prenom'              => $prenom_eleve,
                'prenom2'             => '',
                'prenom3'             => '',
                'code_pays_nat'       => $code_pays_nat,
                'date_naiss'          => $date_naiss ?: '2008-01-01',
                'code_sexe'           => $code_sexe,
                'code_pays'           => '100',
                'commune_insee_naiss' => $commune_insee_naiss,
                'ville_naiss'         => $ville_naiss,
                'date_entree'         => $date_deb_sco,
                'tel_portable'        => siecle_bee_export_format_phone($tel_eleve),
                'email'               => $email_eleve,
                'code_regime'         => $code_regime,
                'doublement'          => 'false',
                'boursier'            => $boursier === 1,
                'code_mef'            => $code_mef,
                'code_division'       => substr($nom_classe, 0, 8),
                'code_statut'         => 'ST',
                'date_deb_sco'        => $date_deb_sco,
                'date_fin_sco'        => $date_fin_sco,
                'options'             => $options_list,
                'responsables'        => $responsables_eleve,
                'etab_an_dernier'     => array(
                    'code_rne'        => preg_match('/^[0-9]{7}[A-Z]$/i', $etab_ant_rne) ? strtoupper($etab_ant_rne) : '',
                    'code_dept'       => preg_match('/^[0-9]{2,3}$/', $etab_ant_cp) ? substr($etab_ant_cp, 0, 2) : '75',
                    'code_provenance' => '1',
                    'code_mef'        => $profil === 'ephc' ? '99999902990' : '10110001110',
                    'type_mef'        => '0',
                    'code_division'   => substr($class_ant, 0, 8),
                    'code_statut'     => 'ST',
                    'date_deb_sco'    => ($annee_int - 1) . "-09-01",
                    'date_fin_sco'    => "{$annee_int}-07-02",
                    'code_regime'     => '2',
                    'options'         => array()
                )
            );
        }
    }

    return array(
        'parametres' => array(
            'uaj'            => strtoupper(trim((string)$uai)),
            'annee_scolaire' => (string)(int)$annee,
            'date_import'    => date('d/m/Y'),
            'num_envoi'      => siecle_bee_get_num_envoi($uai),
            'logiciel'       => 'TRIADE'
        ),
        'personnes'  => array_values($personnes_map),
        'eleves'     => $eleves_list,
        'profil'     => $profil
    );
}
/**
 * Genere le document XML d'exportation SIECLE-BEE encode en ISO-8859-15.
 *
 * @param array $export_data Donnees structurees retournees par siecle_bee_get_export_data()
 * @return string Flux XML encode en ISO-8859-15
 */
function siecle_bee_generate_xml($export_data) {
    $profil = isset($export_data['profil']) && $export_data['profil'] === 'ephc' ? 'ephc' : 'standard';
    $version_attr = ($profil === 'ephc') ? '1.1' : '3.0';

    $doc = new DOMDocument('1.0', 'ISO-8859-15');
    $doc->formatOutput = true;

    // Racine <IMPORT_ELEVES VERSION="...">
    $root = $doc->createElement('IMPORT_ELEVES');
    $root->setAttribute('VERSION', $version_attr);
    $doc->appendChild($root);

    // Bloc <PARAMETRES>
    $params_node = $doc->createElement('PARAMETRES');
    $p = $export_data['parametres'];

    $params_node->appendChild($doc->createElement('UAJ', htmlspecialchars($p['uaj'], ENT_XML1, 'ISO-8859-15')));
    $params_node->appendChild($doc->createElement('ANNEE_SCOLAIRE', htmlspecialchars($p['annee_scolaire'], ENT_XML1, 'ISO-8859-15')));
    $params_node->appendChild($doc->createElement('DATE_IMPORT', htmlspecialchars($p['date_import'], ENT_XML1, 'ISO-8859-15')));
    $params_node->appendChild($doc->createElement('NUM_ENVOI', htmlspecialchars((string)$p['num_envoi'], ENT_XML1, 'ISO-8859-15')));
    $params_node->appendChild($doc->createElement('LOGICIEL', htmlspecialchars($p['logiciel'], ENT_XML1, 'ISO-8859-15')));
    $root->appendChild($params_node);

    // Bloc <DONNEES>
    $donnees_node = $doc->createElement('DONNEES');
    $root->appendChild($donnees_node);

    // 1. Sous-bloc <PERSONNES>
    $personnes_node = $doc->createElement('PERSONNES');
    $donnees_node->appendChild($personnes_node);

    foreach ($export_data['personnes'] as $pers) {
        $pers_node = $doc->createElement('PERSONNE');
        $pers_node->appendChild($doc->createElement('ID_PRV_PER', (string)$pers['id_prv_per']));
        $pers_node->appendChild($doc->createElement('CODE_CIVILITE', (string)$pers['code_civilite']));
        $pers_node->appendChild($doc->createElement('NOM_DE_FAMILLE', htmlspecialchars($pers['nom_de_famille'], ENT_XML1, 'ISO-8859-15')));
        if (!empty($pers['nom_usage'])) {
            $pers_node->appendChild($doc->createElement('NOM_USAGE', htmlspecialchars($pers['nom_usage'], ENT_XML1, 'ISO-8859-15')));
        }
        $pers_node->appendChild($doc->createElement('PRENOM', htmlspecialchars($pers['prenom'], ENT_XML1, 'ISO-8859-15')));

        // Adresse personne
        if (!empty($pers['adresse'])) {
            $adr_node = $doc->createElement('ADRESSE');
            if (!empty($pers['adresse']['ligne1'])) {
                $adr_node->appendChild($doc->createElement('LIGNE1_ADRESSE', htmlspecialchars($pers['adresse']['ligne1'], ENT_XML1, 'ISO-8859-15')));
            }
            $adr_node->appendChild($doc->createElement('CODE_POSTAL', htmlspecialchars($pers['adresse']['code_postal'], ENT_XML1, 'ISO-8859-15')));
            $adr_node->appendChild($doc->createElement('LL_POSTAL', htmlspecialchars($pers['adresse']['ll_postal'], ENT_XML1, 'ISO-8859-15')));
            $adr_node->appendChild($doc->createElement('CODE_PAYS', htmlspecialchars($pers['adresse']['code_pays'], ENT_XML1, 'ISO-8859-15')));
            $pers_node->appendChild($adr_node);
        }

        if ($profil !== 'ephc' && isset($pers['comm_adresse'])) {
            $pers_node->appendChild($doc->createElement('COMMUNICATION_ADRESSE', $pers['comm_adresse']));
        }

        $pers_node->appendChild($doc->createElement('CODE_PROFESSION', (string)$pers['code_profession']));

        if (!empty($pers['tel_personnel'])) {
            $pers_node->appendChild($doc->createElement('TEL_PERSONNEL', htmlspecialchars($pers['tel_personnel'], ENT_XML1, 'ISO-8859-15')));
        }
        if (!empty($pers['tel_professionnel'])) {
            $pers_node->appendChild($doc->createElement('TEL_PROFESSIONNEL', htmlspecialchars($pers['tel_professionnel'], ENT_XML1, 'ISO-8859-15')));
        }
        if (!empty($pers['tel_portable'])) {
            $pers_node->appendChild($doc->createElement('TEL_PORTABLE', htmlspecialchars($pers['tel_portable'], ENT_XML1, 'ISO-8859-15')));
        }
        if ($profil !== 'ephc' && !empty($pers['accepte_sms'])) {
            $pers_node->appendChild($doc->createElement('ACCEPTE_SMS', $pers['accepte_sms']));
        }
        if (!empty($pers['email'])) {
            $pers_node->appendChild($doc->createElement('EMAIL', htmlspecialchars($pers['email'], ENT_XML1, 'ISO-8859-15')));
        }

        $personnes_node->appendChild($pers_node);
    }

    // 2. Sous-bloc <ELEVES>
    $eleves_node = $doc->createElement('ELEVES');
    $donnees_node->appendChild($eleves_node);

    foreach ($export_data['eleves'] as $el) {
        $el_node = $doc->createElement('ELEVE');

        if ($el['is_sortant']) {
            // --- ELEVE_SORTANT ---
            if ($profil !== 'ephc') {
                $el_node->appendChild($doc->createElement('CODE_MOTIF_SORTIE', (string)$el['code_motif_sortie']));
            }
            $el_node->appendChild($doc->createElement('DATE_SORTIE', (string)$el['date_sortie']));
            $el_node->appendChild($doc->createElement('ID_PRV_ELE', (string)$el['elev_id']));
            if (!empty($el['id_national'])) {
                $el_node->appendChild($doc->createElement('ID_NATIONAL', (string)$el['id_national']));
            }
            $el_node->appendChild($doc->createElement('NOM_DE_FAMILLE', htmlspecialchars($el['nom_de_famille'], ENT_XML1, 'ISO-8859-15')));
            $el_node->appendChild($doc->createElement('PRENOM', htmlspecialchars($el['prenom'], ENT_XML1, 'ISO-8859-15')));
            if ($profil !== 'ephc' && !empty($el['code_pays_nat'])) {
                $el_node->appendChild($doc->createElement('CODE_PAYS_NAT', (string)$el['code_pays_nat']));
            }
            $el_node->appendChild($doc->createElement('DATE_NAISS', (string)$el['date_naiss']));
            $el_node->appendChild($doc->createElement('CODE_SEXE', (string)$el['code_sexe']));
            $el_node->appendChild($doc->createElement('CODE_PAYS', (string)$el['code_pays']));

            if (!empty($el['commune_insee_naiss'])) {
                $el_node->appendChild($doc->createElement('CODE_COMMUNE_INSEE_NAISS', (string)$el['commune_insee_naiss']));
            } elseif (!empty($el['ville_naiss'])) {
                $el_node->appendChild($doc->createElement('VILLE_NAISS', htmlspecialchars($el['ville_naiss'], ENT_XML1, 'ISO-8859-15')));
            }

            if ($profil !== 'ephc') {
                $el_node->appendChild($doc->createElement('ADHESION_TRANSPORT', 'false'));
                $el_node->appendChild($doc->createElement('CODE_REGIME', (string)$el['code_regime']));
                $el_node->appendChild($doc->createElement('DOUBLEMENT', 'false'));
            }
        } else {
            // --- ELEVE_NON_SORTANT ---
            $el_node->appendChild($doc->createElement('ID_PRV_ELE', (string)$el['elev_id']));
            if (!empty($el['id_national'])) {
                $el_node->appendChild($doc->createElement('ID_NATIONAL', (string)$el['id_national']));
            }
            $el_node->appendChild($doc->createElement('NOM_DE_FAMILLE', htmlspecialchars($el['nom_de_famille'], ENT_XML1, 'ISO-8859-15')));
            $el_node->appendChild($doc->createElement('PRENOM', htmlspecialchars($el['prenom'], ENT_XML1, 'ISO-8859-15')));

            if ($profil !== 'ephc') {
                $el_node->appendChild($doc->createElement('CODE_PAYS_NAT', (string)$el['code_pays_nat']));
            }

            $el_node->appendChild($doc->createElement('DATE_NAISS', (string)$el['date_naiss']));
            $el_node->appendChild($doc->createElement('CODE_SEXE', (string)$el['code_sexe']));
            $el_node->appendChild($doc->createElement('CODE_PAYS', (string)$el['code_pays']));

            if (!empty($el['commune_insee_naiss'])) {
                $el_node->appendChild($doc->createElement('CODE_COMMUNE_INSEE_NAISS', (string)$el['commune_insee_naiss']));
            } else {
                $el_node->appendChild($doc->createElement('VILLE_NAISS', htmlspecialchars($el['ville_naiss'] ?: 'PARIS', ENT_XML1, 'ISO-8859-15')));
            }

            $el_node->appendChild($doc->createElement('DATE_ENTREE', (string)$el['date_entree']));

            if ($profil !== 'ephc') {
                if (!empty($el['tel_portable'])) {
                    $el_node->appendChild($doc->createElement('TEL_PORTABLE', htmlspecialchars($el['tel_portable'], ENT_XML1, 'ISO-8859-15')));
                    $el_node->appendChild($doc->createElement('ACCEPTE_SMS', 'false'));
                }
                if (!empty($el['email'])) {
                    $el_node->appendChild($doc->createElement('EMAIL', htmlspecialchars($el['email'], ENT_XML1, 'ISO-8859-15')));
                }
                $el_node->appendChild($doc->createElement('ADHESION_TRANSPORT', 'false'));
                $el_node->appendChild($doc->createElement('CODE_REGIME', (string)$el['code_regime']));
                $el_node->appendChild($doc->createElement('DOUBLEMENT', (string)$el['doublement']));
            }

            // Bloc <RESPONSABLES_ELEVE>
            $resp_eleve_node = $doc->createElement('RESPONSABLES_ELEVE');
            foreach ($el['responsables'] as $resp) {
                $r_node = $doc->createElement($resp['type']);
                $r_node->appendChild($doc->createElement('ID_PRV_PER', (string)$resp['id_prv_per']));
                $r_node->appendChild($doc->createElement('CODE_PARENTE', (string)$resp['code_parente']));
                
                if ($profil !== 'ephc') {
                    $r_node->appendChild($doc->createElement('PAIE_FRAIS_SCOLAIRES', (string)$resp['paie_frais_scolaires']));
                }
                
                $r_node->appendChild($doc->createElement('HEBERGE_ELEVE', (string)$resp['heberge_eleve']));
                
                if ($profil !== 'ephc') {
                    $r_node->appendChild($doc->createElement('PERCOIT_LES_AIDES', (string)$resp['percoit_les_aides']));
                    $r_node->appendChild($doc->createElement('A_CONTACTER_EN_PRIORITE', (string)$resp['a_contacter_en_priorite']));
                }
                $resp_eleve_node->appendChild($r_node);
            }
            $el_node->appendChild($resp_eleve_node);

            // Bloc <SCOLARITE_ACTIVE>
            $sco_node = $doc->createElement('SCOLARITE_ACTIVE');
            $sco_node->appendChild($doc->createElement('CODE_MEF', (string)$el['code_mef']));
            
            if ($profil !== 'ephc') {
                if (!empty($el['code_division'])) {
                    $sco_node->appendChild($doc->createElement('CODE_DIVISION', htmlspecialchars($el['code_division'], ENT_XML1, 'ISO-8859-15')));
                }
                $sco_node->appendChild($doc->createElement('CODE_STATUT', (string)$el['code_statut']));
            }

            $sco_node->appendChild($doc->createElement('DATE_DEB_SCO', (string)$el['date_deb_sco']));
            $sco_node->appendChild($doc->createElement('DATE_FIN_SCO', (string)$el['date_fin_sco']));

            if ($profil !== 'ephc') {
                $options_node = $doc->createElement('OPTIONS');
                foreach ($el['options'] as $opt) {
                    $opt_node = $doc->createElement('OPTION');
                    $opt_node->appendChild($doc->createElement('CODE_MATIERE', htmlspecialchars($opt['code_matiere'], ENT_XML1, 'ISO-8859-15')));
                    $opt_node->appendChild($doc->createElement('CODE_MODALITE_ELECT', (string)$opt['modalite']));
                    $options_node->appendChild($opt_node);
                }
                $sco_node->appendChild($options_node);
            }
            $el_node->appendChild($sco_node);

            // Bloc <ETABLISSEMENT_AN_DERNIER>
            if (!empty($el['etab_an_dernier'])) {
                $etab_node = $doc->createElement('ETABLISSEMENT_AN_DERNIER');
                if (!empty($el['etab_an_dernier']['code_rne'])) {
                    $etab_node->appendChild($doc->createElement('CODE_RNE', htmlspecialchars($el['etab_an_dernier']['code_rne'], ENT_XML1, 'ISO-8859-15')));
                } else {
                    $etab_node->appendChild($doc->createElement('CODE_DEPARTEMENT', htmlspecialchars($el['etab_an_dernier']['code_dept'], ENT_XML1, 'ISO-8859-15')));
                }

                if ($profil !== 'ephc') {
                    $etab_node->appendChild($doc->createElement('CODE_PROVENANCE', (string)$el['etab_an_dernier']['code_provenance']));
                    $etab_node->appendChild($doc->createElement('CODE_MEF', (string)$el['etab_an_dernier']['code_mef']));
                    $etab_node->appendChild($doc->createElement('TYPE_MEF', (string)$el['etab_an_dernier']['type_mef']));
                    if (!empty($el['etab_an_dernier']['code_division'])) {
                        $etab_node->appendChild($doc->createElement('CODE_DIVISION', htmlspecialchars($el['etab_an_dernier']['code_division'], ENT_XML1, 'ISO-8859-15')));
                    }
                    $etab_node->appendChild($doc->createElement('CODE_STATUT', (string)$el['etab_an_dernier']['code_statut']));
                }

                $etab_node->appendChild($doc->createElement('DATE_DEB_SCO', (string)$el['etab_an_dernier']['date_deb_sco']));
                $etab_node->appendChild($doc->createElement('DATE_FIN_SCO', (string)$el['etab_an_dernier']['date_fin_sco']));

                if ($profil !== 'ephc') {
                    $etab_node->appendChild($doc->createElement('CODE_REGIME', (string)$el['etab_an_dernier']['code_regime']));
                    $opts_an_node = $doc->createElement('OPTIONS_AN_DERNIER');
                    $etab_node->appendChild($opts_an_node);
                }

                $el_node->appendChild($etab_node);
            }
        }

        $eleves_node->appendChild($el_node);
    }

    $xml_output = $doc->saveXML();

    if (function_exists('mb_convert_encoding')) {
        $xml_output = mb_convert_encoding($xml_output, 'ISO-8859-15', 'UTF-8');
    }

    return $xml_output;
}
/**
 * Valide un document XML genere contre le schema XSD officiel SIECLE-BEE.
 *
 * @param string $xml_content Contenu XML
 * @param string $profil 'standard' | 'ephc'
 * @return array 'valid' => bool, 'errors' => array
 */
function siecle_bee_validate_export_xml($xml_content, $profil = 'standard') {
    $xsd_file = ($profil === 'ephc')
        ? __DIR__ . '/schema_Import_ephc_1.1.xsd'
        : __DIR__ . '/schema_Import_4.0.xsd';

    if (!file_exists($xsd_file)) {
        return array(
            'valid'  => false,
            'errors' => array("Schema XSD introuvable : " . basename($xsd_file))
        );
    }

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    
    $loaded = @$doc->loadXML($xml_content);
    if (!$loaded) {
        $errors = array();
        foreach (libxml_get_errors() as $err) {
            $errors[] = "Ligne {$err->line}: " . trim($err->message);
        }
        libxml_clear_errors();
        return array('valid' => false, 'errors' => $errors);
    }

    $is_valid = @$doc->schemaValidate($xsd_file);
    $errors = array();
    if (!$is_valid) {
        foreach (libxml_get_errors() as $err) {
            $errors[] = "Ligne {$err->line}: " . trim($err->message);
        }
        libxml_clear_errors();
    }

    return array(
        'valid'  => $is_valid,
        'errors' => $errors
    );
}

/**
 * Cree le package ZIP d'exportation SIECLE-BEE norme.
 *
 * @param string $xml_content
 * @param string $uai
 * @param string $annee
 * @param string $profil
 * @return array 'success' => bool, 'zip_path' => string, 'zip_name' => string, 'xml_name' => string, 'errors' => array
 */
function siecle_bee_create_zip_export($xml_content, $uai, $annee, $profil = 'standard') {
    $uai = strtoupper(trim((string)$uai));
    $annee = (string)(int)$annee;
    $timestamp = date('ymdHis');

    $base_name = "{$uai}PRIVE{$annee}{$timestamp}";
    $xml_name  = "{$base_name}.xml";
    $zip_name  = "{$base_name}.zip";

    $temp_dir = __DIR__ . "/../data/fichier_gep";
    if (!is_dir($temp_dir)) {
        @mkdir($temp_dir, 0777, true);
    }

    $xml_path = $temp_dir . "/" . $xml_name;
    $zip_path = $temp_dir . "/" . $zip_name;

    if (file_put_contents($xml_path, $xml_content) === false) {
        return array(
            'success' => false,
            'errors'  => array("Impossible d'ecrire le fichier XML temporaire.")
        );
    }

    if (!class_exists('ZipArchive')) {
        return array(
            'success'  => false,
            'xml_path' => $xml_path,
            'xml_name' => $xml_name,
            'errors'   => array("L'extension PHP ZipArchive n'est pas activee.")
        );
    }

    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return array(
            'success' => false,
            'errors'  => array("Impossible de creer l'archive ZIP.")
        );
    }

    $zip->addFile($xml_path, $xml_name);
    $zip->close();

    siecle_bee_get_num_envoi($uai, true);

    return array(
        'success'  => true,
        'zip_path' => $zip_path,
        'zip_name' => $zip_name,
        'xml_path' => $xml_path,
        'xml_name' => $xml_name,
        'errors'   => array()
    );
}
