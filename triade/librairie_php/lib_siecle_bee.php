<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
 *
 *   Module               : Importation SIECLE-BEE XML & ZIP
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
 * Convertit un libellé ou code de civilité SIECLE en identifiant de civilité TRIADE.
 *
 * @param string $lc_civ Libellé court (ex: M., MME, MLLE)
 * @param string $ll_civ Libellé long (ex: MONSIEUR, MADAME)
 * @return int Identifiant de civilité TRIADE (0: Monsieur, 1: Madame, 2: Mlle, 6: Autre/Défaut)
 */
function siecle_bee_civ_to_id($lc_civ, $ll_civ = '') {
    $civ = strtoupper(trim((string)$lc_civ));
    if ($civ === '') {
        $civ = strtoupper(trim((string)$ll_civ));
    }
    if ($civ === 'M.' || $civ === 'M' || $civ === 'MONSIEUR' || $civ === 'MR') {
        return 0;
    }
    if ($civ === 'MME' || $civ === 'MADAME') {
        return 1;
    }
    if ($civ === 'MLLE' || $civ === 'MADEMOISELLE') {
        return 2;
    }
    return 6;
}

/**
 * Convertit un code régime SIECLE en libellé TRIADE.
 *
 * @param string|int $code Code régime SIECLE
 * @return string 'Externe' | 'Demi Pension' | 'Interne'
 */
function siecle_bee_code_regime_to_label($code) {
    $c = strtoupper(trim((string)$code));
    if ($c === '0' || $c === '3' || $c === 'E' || $c === 'EXTERNE' || $c === 'EXTERN') {
        return 'Externe';
    }
    if ($c === '1' || $c === '5' || $c === 'D' || $c === 'DP' || $c === 'DP DAN' || $c === 'DEMI PENSION' || $c === 'DEMI-PENSION') {
        return 'Demi Pension';
    }
    if ($c === '2' || $c === '4' || $c === '6' || $c === 'I' || $c === 'INT' || $c === 'INTERNE') {
        return 'Interne';
    }
    return 'Externe';
}

/**
 * Détecte le type d'un document XML SIECLE-BEE.
 *
 * @param SimpleXMLElement $xml
 * @return string 'eleves' | 'responsables' | 'structures' | 'communs' | 'nomenclatures' | 'etablissements' | 'geographique' | 'inconnu'
 */
function siecle_bee_detect_xml_type($xml) {
    if (!($xml instanceof SimpleXMLElement)) {
        return 'inconnu';
    }
    $root = strtoupper($xml->getName());
    if ($root === 'BEE_ELEVES' || $root === 'BEE_EPHC_ELEVES' || isset($xml->DONNEES->ELEVES) || isset($xml->ELEVES)) {
        return 'eleves';
    }
    if ($root === 'BEE_RESPONSABLES' || $root === 'BEE_EPHC_RESPONSABLES' || isset($xml->DONNEES->PERSONNES) || isset($xml->PERSONNES) || isset($xml->DONNEES->RESPONSABLES)) {
        return 'responsables';
    }
    if ($root === 'BEE_STRUCTURES' || isset($xml->DONNEES->DIVISIONS) || isset($xml->DIVISIONS)) {
        return 'structures';
    }
    if ($root === 'BEE_COMMUN' || isset($xml->DONNEES->UAJ) || isset($xml->UAJ)) {
        return 'communs';
    }
    if ($root === 'BEE_NOMENCLATURE' || isset($xml->DONNEES->NOMENCLATURES)) {
        return 'nomenclatures';
    }
    if ($root === 'BEE_ETABLISSEMENT' || isset($xml->DONNEES->ETABLISSEMENTS)) {
        return 'etablissements';
    }
    if ($root === 'BEE_GEOGRAPHIQUE' || isset($xml->DONNEES->GEOGRAPHIQUE)) {
        return 'geographique';
    }
    return 'inconnu';
}

/**
 * Extrait de façon sécurisée une archive ZIP contenant des fichiers SIECLE-BEE.
 *
 * @param string $zip_path Chemin du fichier ZIP
 * @param string $target_dir Répertoire de destination
 * @return array Liste des chemins absolus des fichiers XML extraits
 */
function siecle_bee_extract_zip($zip_path, $target_dir) {
    $extracted_files = array();
    if (!class_exists('ZipArchive')) {
        return $extracted_files;
    }

    if (!is_dir($target_dir)) {
        @mkdir($target_dir, 0777, true);
    }

    $zip = new ZipArchive();
    if ($zip->open($zip_path) === true) {
        $real_target_dir = realpath($target_dir);
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            // Protection Zip Slip (path traversal) et fichiers indésirables
            if (strpos($filename, '..') !== false || strpos($filename, '__MACOSX') !== false) {
                continue;
            }

            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if ($ext !== 'xml') {
                continue;
            }

            $basename = basename($filename);
            $dest_file = $target_dir . DIRECTORY_SEPARATOR . $basename;

            $content = $zip->getFromIndex($i);
            if ($content !== false) {
                file_put_contents($dest_file, $content);
                $extracted_files[] = $dest_file;
            }
        }
        $zip->close();
    }

    return $extracted_files;
}

/**
 * Analyse le fichier Structures.xml et extrait les divisions et groupes.
 *
 * @param SimpleXMLElement $xml
 * @return array Structures extraites
 */
function siecle_bee_parse_structures($xml) {
    $result = array(
        'divisions' => array(),
        'groupes'   => array()
    );

    $divisions_node = isset($xml->DONNEES->DIVISIONS) ? $xml->DONNEES->DIVISIONS : (isset($xml->DIVISIONS) ? $xml->DIVISIONS : null);
    if ($divisions_node) {
        foreach ($divisions_node->DIVISION as $div) {
            $code = trim((string)$div['CODE_STRUCTURE']);
            if ($code === '') {
                $code = trim((string)$div->CODE_STRUCTURE);
            }
            $libelle_long = trim((string)$div->LIBELLE_LONG);
            $code_rne     = trim((string)$div->CODE_RNE);

            $mefs = array();
            if (isset($div->MEFS_APPARTENANCE)) {
                foreach ($div->MEFS_APPARTENANCE->MEF_APPARTENANCE as $mef) {
                    $mefs[] = trim((string)$mef->CODE_MEF);
                }
            }

            if ($code !== '') {
                $result['divisions'][$code] = array(
                    'code'         => $code,
                    'libelle_long' => $libelle_long !== '' ? $libelle_long : $code,
                    'code_rne'     => $code_rne,
                    'mefs'         => $mefs
                );
            }
        }
    }

    $groupes_node = isset($xml->DONNEES->GROUPES) ? $xml->DONNEES->GROUPES : (isset($xml->GROUPES) ? $xml->GROUPES : null);
    if ($groupes_node) {
        foreach ($groupes_node->GROUPE as $grp) {
            $code = trim((string)$grp['CODE_STRUCTURE']);
            if ($code === '') {
                $code = trim((string)$grp->CODE_STRUCTURE);
            }
            $libelle_long = trim((string)$grp->LIBELLE_LONG);
            if ($code !== '') {
                $result['groupes'][$code] = array(
                    'code'         => $code,
                    'libelle_long' => $libelle_long !== '' ? $libelle_long : $code
                );
            }
        }
    }

    return $result;
}

/**
 * Analyse le fichier Communs.xml.
 *
 * @param SimpleXMLElement $xml
 * @return array Données établissement
 */
function siecle_bee_parse_communs($xml) {
    $result = array(
        'uaj'              => '',
        'denomination'    => '',
        'adresse'          => '',
        'code_postal'      => '',
        'commune'          => '',
        'telephone'        => '',
        'mel'              => '',
        'date_debut_eleve' => '',
        'date_fin_eleve'   => '',
    );

    $uaj_node = isset($xml->DONNEES->UAJ) ? $xml->DONNEES->UAJ : (isset($xml->UAJ) ? $xml->UAJ : null);
    if ($uaj_node) {
        $result['uaj']           = trim((string)$uaj_node->ETAB_UAJ_ID);
        $result['denomination']  = trim((string)$uaj_node->DENOM_PRINC);
        $result['adresse']       = trim((string)$uaj_node->LIGNE1_ADRESSE);
        $result['code_postal']   = trim((string)$uaj_node->CODE_POSTAL);
        $result['commune']       = trim((string)$uaj_node->CODE_COMMUNE_INSEE);
        $result['telephone']     = trim((string)$uaj_node->TELEPHONE);
        $result['mel']           = trim((string)$uaj_node->MEL_1);
    }

    $annee_node = isset($xml->DONNEES->ANNEE_SCOLAIRE) ? $xml->DONNEES->ANNEE_SCOLAIRE : (isset($xml->ANNEE_SCOLAIRE) ? $xml->ANNEE_SCOLAIRE : null);
    if ($annee_node) {
        $result['date_debut_eleve'] = trim((string)$annee_node->DATE_DEBUT_ELEVE);
        $result['date_fin_eleve']   = trim((string)$annee_node->DATE_FIN_ELEVE);
    }

    return $result;
}

/**
 * Analyse le fichier ResponsablesAvecAdresses.xml et produit un mapping par ELEVE_ID.
 *
 * @param SimpleXMLElement $xml
 * @return array Mapping indexé par ELEVE_ID contenant tuteur1 et tuteur2
 */
function siecle_bee_parse_responsables($xml) {
    $adresses = array();
    $adresses_node = isset($xml->DONNEES->ADRESSES) ? $xml->DONNEES->ADRESSES : (isset($xml->ADRESSES) ? $xml->ADRESSES : null);
    if ($adresses_node) {
        foreach ($adresses_node->ADRESSE as $adr) {
            $adr_id = trim((string)$adr['ADRESSE_ID']);
            if ($adr_id === '') {
                $adr_id = trim((string)$adr->ADRESSE_ID);
            }
            $l1 = trim((string)$adr->LIGNE1_ADRESSE);
            $l2 = trim((string)$adr->LIGNE2_ADRESSE);
            $l3 = trim((string)$adr->LIGNE3_ADRESSE);
            $l4 = trim((string)$adr->LIGNE4_ADRESSE);

            $adr_parts = array_filter(array($l1, $l2, $l3, $l4));
            $rue = implode(' ', $adr_parts);

            $commune = trim((string)$adr->LIBELLE_POSTAL);
            if ($commune === '') {
                $commune = trim((string)$adr->LL_COMMUNE_INSEE);
            }
            if ($commune === '') {
                $commune = trim((string)$adr->COMMUNE_ETRANGERE);
            }

            $adresses[$adr_id] = array(
                'rue'         => $rue,
                'code_postal' => trim((string)$adr->CODE_POSTAL),
                'commune'     => $commune,
                'pays'        => trim((string)$adr->LL_PAYS) ?: trim((string)$adr->CODE_PAYS)
            );
        }
    }

    $personnes = array();
    $personnes_node = isset($xml->DONNEES->PERSONNES) ? $xml->DONNEES->PERSONNES : (isset($xml->PERSONNES) ? $xml->PERSONNES : null);
    if ($personnes_node) {
        foreach ($personnes_node->PERSONNE as $p) {
            $pers_id = trim((string)$p['PERSONNE_ID']);
            if ($pers_id === '') {
                $pers_id = trim((string)$p->PERSONNE_ID);
            }

            $nom_famille = trim((string)$p->NOM_DE_FAMILLE);
            $nom_usage   = trim((string)$p->NOM_USAGE);
            $nom         = $nom_usage !== '' ? $nom_usage : $nom_famille;
            if ($nom === '') {
                $nom = trim((string)$p->NOM);
            }

            $prenom = trim((string)$p->PRENOM);
            $civ_id = siecle_bee_civ_to_id((string)$p->LC_CIVILITE, (string)$p->LL_CIVILITE);

            $tel_fixe = trim((string)$p->TEL_PERSONNEL);
            $tel_port = trim((string)$p->TEL_PORTABLE);
            $tel_pro  = trim((string)$p->TEL_PROFESSIONNEL);
            $mel      = trim((string)$p->MEL);

            $adr_id   = trim((string)$p->ADRESSE_ID);
            $adr_info = isset($adresses[$adr_id]) ? $adresses[$adr_id] : array('rue' => '', 'code_postal' => '', 'commune' => '', 'pays' => '');

            $personnes[$pers_id] = array(
                'personne_id' => $pers_id,
                'civilite'    => $civ_id,
                'nom'         => $nom,
                'nom_famille' => $nom_famille,
                'nom_usage'   => $nom_usage,
                'prenom'      => $prenom,
                'tel_fixe'    => $tel_fixe,
                'tel_port'    => $tel_port,
                'tel_pro'     => $tel_pro,
                'email'       => $mel,
                'adresse'     => $adr_info
            );
        }
    }

    $eleves_resp = array();
    $resp_node = isset($xml->DONNEES->RESPONSABLES) ? $xml->DONNEES->RESPONSABLES : (isset($xml->RESPONSABLES) ? $xml->RESPONSABLES : null);
    if ($resp_node) {
        foreach ($resp_node->RESPONSABLE_ELEVE as $re) {
            $eleve_id = trim((string)$re->ELEVE_ID);
            $pers_id  = trim((string)$re->PERSONNE_ID);
            $niveau   = (int)trim((string)$re->NIVEAU_RESPONSABILITE);
            $priorite = strtolower(trim((string)$re->A_CONTACTER_EN_PRIORITE)) === 'true';
            $heberge  = strtolower(trim((string)$re->HEBERGE_ELEVE)) === 'true';
            $code_parente = trim((string)$re->CODE_PARENTE);

            if (!isset($personnes[$pers_id])) {
                continue;
            }

            if (!isset($eleves_resp[$eleve_id])) {
                $eleves_resp[$eleve_id] = array();
            }

            $eleves_resp[$eleve_id][] = array(
                'personne'     => $personnes[$pers_id],
                'niveau'       => $niveau,
                'priorite'     => $priorite,
                'heberge'      => $heberge,
                'code_parente' => $code_parente
            );
        }
    }

    // Structuration finale par élève (Tuteur 1 et Tuteur 2)
    $result_map = array();
    foreach ($eleves_resp as $eleve_id => $liste) {
        // Trier par priorité : niveau 1 ou prioritaire d'abord, puis niveau 2
        usort($liste, function($a, $b) {
            if ($a['priorite'] && !$b['priorite']) return -1;
            if (!$a['priorite'] && $b['priorite']) return 1;
            return $a['niveau'] - $b['niveau'];
        });

        $tuteur1 = isset($liste[0]) ? $liste[0] : null;
        $tuteur2 = isset($liste[1]) ? $liste[1] : null;

        $result_map[$eleve_id] = array(
            'tuteur1' => $tuteur1,
            'tuteur2' => $tuteur2
        );
    }

    return $result_map;
}

/**
 * Analyse le fichier ElevesAvecAdresses.xml (ou sans adresses), intègre les données de structures
 * et de responsables, et génère la liste des élèves prête pour insertion dans TRIADE.
 *
 * @param SimpleXMLElement $xml
 * @param array $responsables_map Map des responsables indexée par ELEVE_ID
 * @param array $structures_map Map des structures
 * @return array Liste des données élèves enrichies
 */
function siecle_bee_parse_eleves($xml, $responsables_map = array(), $structures_map = array()) {
    $eleves_data = array();

    // 1. Extraction des adresses élèves propres
    $adresses_eleves = array();
    $adresses_node = isset($xml->DONNEES->ADRESSES) ? $xml->DONNEES->ADRESSES : (isset($xml->ADRESSES) ? $xml->ADRESSES : null);
    if ($adresses_node) {
        foreach ($adresses_node->ADRESSE as $adr) {
            $adr_id = trim((string)$adr['ADRESSE_ID']);
            if ($adr_id === '') {
                $adr_id = trim((string)$adr->ADRESSE_ID);
            }
            $l1 = trim((string)$adr->LIGNE1_ADRESSE);
            $l2 = trim((string)$adr->LIGNE2_ADRESSE);
            $l3 = trim((string)$adr->LIGNE3_ADRESSE);
            $l4 = trim((string)$adr->LIGNE4_ADRESSE);

            $adr_parts = array_filter(array($l1, $l2, $l3, $l4));
            $rue = implode(' ', $adr_parts);

            $commune = trim((string)$adr->LIBELLE_POSTAL);
            if ($commune === '') {
                $commune = trim((string)$adr->LL_COMMUNE_INSEE);
            }

            $adresses_eleves[$adr_id] = array(
                'rue'         => $rue,
                'code_postal' => trim((string)$adr->CODE_POSTAL),
                'commune'     => $commune,
                'pays'        => trim((string)$adr->LL_PAYS) ?: trim((string)$adr->CODE_PAYS)
            );
        }
    }

    // 2. Extraction des rattachements aux divisions (classes) et groupes
    $classes_map = array();
    $structures_node = isset($xml->DONNEES->STRUCTURES) ? $xml->DONNEES->STRUCTURES : (isset($xml->STRUCTURES) ? $xml->STRUCTURES : null);
    if ($structures_node) {
        foreach ($structures_node->STRUCTURES_ELEVE as $se) {
            $eid = trim((string)$se['ELEVE_ID']);
            if ($eid === '') {
                $eid = trim((string)$se->ELEVE_ID);
            }
            foreach ($se->STRUCTURE as $s) {
                $type_struct = strtoupper(trim((string)$s->TYPE_STRUCTURE));
                $code_struct = trim((string)$s->CODE_STRUCTURE);
                if ($type_struct === 'D' && $code_struct !== '') {
                    $classes_map[$eid] = $code_struct;
                    break;
                }
            }
        }
    }

    // 2bis. Extraction des scolarités et codes MEF associés
    $scolarites_map = array();
    $scolarites_node = isset($xml->DONNEES->SCOLARITES) ? $xml->DONNEES->SCOLARITES : (isset($xml->SCOLARITES) ? $xml->SCOLARITES : null);
    if ($scolarites_node) {
        foreach ($scolarites_node->SCOLARITE_ELEVE as $sc) {
            $eid = trim((string)$sc['ELEVE_ID']);
            if ($eid === '') {
                $eid = trim((string)$sc->ELEVE_ID);
            }
            foreach ($sc->SCOLARITE as $s) {
                $code_mef = trim((string)$s->CODE_MEF);
                $code_struct = trim((string)$s->CODE_STRUCTURE);
                if ($code_mef !== '') {
                    $scolarites_map[$eid] = array(
                        'code_mef'       => $code_mef,
                        'code_structure' => $code_struct
                    );
                    break;
                }
            }
        }
    }

    // 3. Extraction des options pédagogiques
    $options_map = array();
    $options_node = isset($xml->DONNEES->OPTIONS) ? $xml->DONNEES->OPTIONS : (isset($xml->OPTIONS) ? $xml->OPTIONS : null);
    if ($options_node) {
        foreach ($options_node->OPTION as $opt) {
            $eid = trim((string)$opt['ELEVE_ID']);
            if ($eid === '') {
                $eid = trim((string)$opt->ELEVE_ID);
            }
            $opt_list = array();
            foreach ($opt->OPTIONS_ELEVE as $oe) {
                $num_opt = (int)trim((string)$oe->NUM_OPTION);
                $code_mat = trim((string)$oe->CODE_MATIERE);
                if ($code_mat !== '') {
                    $opt_list[$num_opt] = $code_mat;
                }
            }
            ksort($opt_list);
            $options_map[$eid] = $opt_list;
        }
    }

    // 4. Extraction des élèves
    $eleves_node = isset($xml->DONNEES->ELEVES) ? $xml->DONNEES->ELEVES : (isset($xml->ELEVES) ? $xml->ELEVES : null);
    if ($eleves_node) {
        foreach ($eleves_node->ELEVE as $e) {
            $eleve_id = trim((string)$e['ELEVE_ID']);
            if ($eleve_id === '') {
                $eleve_id = trim((string)$e->ELEVE_ID);
            }
            $elenoet  = trim((string)$e['ELENOET']);
            if ($elenoet === '') {
                $elenoet = trim((string)$e->ELENOET);
            }
            $ine      = trim((string)$e->ID_NATIONAL);
            $id_etab  = trim((string)$e->ID_ELEVE_ETAB);

            $nom_famille = trim((string)$e->NOM_DE_FAMILLE);
            $nom_usage   = trim((string)$e->NOM_USAGE);
            $nom_raw     = $nom_usage !== '' ? $nom_usage : $nom_famille;
            if ($nom_raw === '') {
                $nom_raw = trim((string)$e->NOM);
            }

            $prenom1 = trim((string)$e->PRENOM);
            $prenom2 = trim((string)$e->PRENOM2);
            $prenom3 = trim((string)$e->PRENOM3);

            if ($nom_raw === '' || $prenom1 === '') {
                continue;
            }

            $date_naiss_raw = trim((string)$e->DATE_NAISS);
            $date_sortie_raw = trim((string)$e->DATE_SORTIE);
            $date_entree_raw = trim((string)$e->DATE_ENTREE);

            $code_sexe = trim((string)$e->CODE_SEXE);
            $sexe = ($code_sexe === '2' || strtoupper($code_sexe) === 'F') ? 'f' : 'm';

            $code_regime = trim((string)$e->CODE_REGIME);
            $regime = siecle_bee_code_regime_to_label($code_regime);

            $lieu_naiss = trim((string)$e->CODE_COMMUNE_INSEE_NAISS);
            if ($lieu_naiss === '') {
                $lieu_naiss = trim((string)$e->VILLE_NAISS);
            }

            $mel_eleve = trim((string)$e->MEL);
            $tel_eleve = trim((string)$e->TELEPHONE);

            // Adresse élève
            $adr_id = trim((string)$e->ADRESSE_ID);
            $adr_eleve_info = isset($adresses_eleves[$adr_id]) ? $adresses_eleves[$adr_id] : array('rue' => '', 'code_postal' => '', 'commune' => '', 'pays' => '');

            // Classe et Scolarité (MEF)
            $code_classe = isset($classes_map[$eleve_id]) ? $classes_map[$eleve_id] : '';
            $code_mef = '';
            if (isset($scolarites_map[$eleve_id])) {
                $code_mef = $scolarites_map[$eleve_id]['code_mef'];
                if ($code_classe === '' && !empty($scolarites_map[$eleve_id]['code_structure'])) {
                    $code_classe = $scolarites_map[$eleve_id]['code_structure'];
                }
            }

            // Détection pour format direct SCOLARITE_ACTIVE (import unifié)
            if (isset($e->SCOLARITE_ACTIVE)) {
                $mef_dir = trim((string)$e->SCOLARITE_ACTIVE->CODE_MEF);
                $div_dir = trim((string)$e->SCOLARITE_ACTIVE->CODE_DIVISION);
                if ($mef_dir !== '') {
                    $code_mef = $mef_dir;
                }
                if ($div_dir !== '' && $code_classe === '') {
                    $code_classe = $div_dir;
                }
            }

            // Options
            $eleve_opts = isset($options_map[$eleve_id]) ? $options_map[$eleve_id] : array();
            $lv1 = isset($eleve_opts[1]) ? $eleve_opts[1] : '';
            $lv2 = isset($eleve_opts[2]) ? $eleve_opts[2] : '';
            $opt_autres = array_slice($eleve_opts, 2);
            $option_str = implode(',', $opt_autres);

            // Responsables
            $resp_info = isset($responsables_map[$eleve_id]) ? $responsables_map[$eleve_id] : null;
            if (!$resp_info && $elenoet !== '' && isset($responsables_map[$elenoet])) {
                $resp_info = $responsables_map[$elenoet];
            }

            $tuteur1 = isset($resp_info['tuteur1']['personne']) ? $resp_info['tuteur1']['personne'] : null;
            $tuteur2 = isset($resp_info['tuteur2']['personne']) ? $resp_info['tuteur2']['personne'] : null;

            // Données tuteur 1
            $civ_1       = $tuteur1 ? $tuteur1['civilite'] : '';
            $nomtuteur   = $tuteur1 ? $tuteur1['nom'] : '';
            $prenomtuteur= $tuteur1 ? $tuteur1['prenom'] : '';
            $adr1        = $tuteur1 ? $tuteur1['adresse']['rue'] : '';
            $cpadr1      = $tuteur1 ? $tuteur1['adresse']['code_postal'] : '';
            $commadr1    = $tuteur1 ? $tuteur1['adresse']['commune'] : '';
            $tel_fixe_tu = $tuteur1 ? ($tuteur1['tel_fixe'] ?: $tuteur1['tel_pro']) : '';
            $tel_port_1  = $tuteur1 ? $tuteur1['tel_port'] : '';
            $email_tu1   = $tuteur1 ? $tuteur1['email'] : '';

            // Données tuteur 2
            $civ_2       = $tuteur2 ? $tuteur2['civilite'] : '';
            $nom_resp2   = $tuteur2 ? $tuteur2['nom'] : '';
            $prenom_resp2= $tuteur2 ? $tuteur2['prenom'] : '';
            $adr2        = $tuteur2 ? $tuteur2['adresse']['rue'] : '';
            $cpadr2      = $tuteur2 ? $tuteur2['adresse']['code_postal'] : '';
            $commadr2    = $tuteur2 ? $tuteur2['adresse']['commune'] : '';
            $tel_port_2  = $tuteur2 ? ($tuteur2['tel_port'] ?: $tuteur2['tel_fixe']) : '';
            $email_tu2   = $tuteur2 ? $tuteur2['email'] : '';

            // Si l'adresse élève n'est pas spécifiée, utiliser l'adresse du tuteur 1
            if ($adr_eleve_info['rue'] === '' && $adr1 !== '') {
                $adr_eleve_info['rue']         = $adr1;
                $adr_eleve_info['code_postal'] = $cpadr1;
                $adr_eleve_info['commune']     = $commadr1;
            }

            $eleves_data[] = array(
                'eleve_id'         => $eleve_id,
                'elenoet'          => $elenoet,
                'ine'              => $ine,
                'id_eleve_etab'    => $id_etab,
                'nom'              => $nom_raw,
                'prenom'           => $prenom1,
                'prenom2'          => $prenom2,
                'prenom3'          => $prenom3,
                'date_naiss'       => $date_naiss_raw,
                'date_sortie'      => $date_sortie_raw,
                'date_entree'      => $date_entree_raw,
                'sexe'             => $sexe,
                'regime'           => $regime,
                'lieu_naiss'       => $lieu_naiss,
                'code_classe'      => $code_classe,
                'code_mef'         => $code_mef,
                'lv1'              => $lv1,
                'lv2'              => $lv2,
                'option'           => $option_str,
                'mail_eleve'       => $mel_eleve,
                'tel_eleve'        => $tel_eleve,
                'adr_eleve'        => $adr_eleve_info['rue'],
                'ccp_eleve'        => $adr_eleve_info['code_postal'],
                'commune_eleve'    => $adr_eleve_info['commune'],
                'pays_eleve'       => $adr_eleve_info['pays'],
                'civ_1'            => $civ_1,
                'nomtuteur'        => $nomtuteur,
                'prenomtuteur'     => $prenomtuteur,
                'adr1'             => $adr1,
                'cpadr1'           => $cpadr1,
                'commadr1'         => $commadr1,
                'telephone'        => $tel_fixe_tu,
                'tel_port_1'       => $tel_port_1,
                'email_tuteur1'    => $email_tu1,
                'civ_2'            => $civ_2,
                'nom_resp_2'       => $nom_resp2,
                'prenom_resp_2'    => $prenom_resp2,
                'adr2'             => $adr2,
                'cpadr2'           => $cpadr2,
                'commune_adr2'     => $commadr2,
                'tel_port_2'       => $tel_port_2,
                'email_resp_2'     => $email_tu2
            );
        }
    }

    return $eleves_data;
}

/**
 * Exécute l'importation complète d'un ensemble de fichiers SIECLE-BEE XML ou archive ZIP.
 *
 * @param array $file_paths Liste des chemins absolus de fichiers XML ou ZIP
 * @param array $options Options d'importation (update, updatevide, updatepasswd, vide_eleve)
 * @return array Bilan et statistiques de l'import
 */
function siecle_bee_import_files($file_paths, $options = array()) {
    $stats = array(
        'total_fichiers'       => 0,
        'fichiers_traites'     => array(),
        'classes_creees'       => 0,
        'responsables_charges' => 0,
        'eleves_total'         => 0,
        'eleves_crees'         => 0,
        'eleves_mis_a_jour'    => 0,
        'eleves_deja_presents' => 0,
        'eleves_supprimes'     => 0,
        'eleves_erreurs'       => 0,
        'erreurs'              => array()
    );

    $temp_extracted_dir = "data/fichier_gep/siecle_bee_" . time() . "_" . mt_rand(1000, 9999);
    $xml_files = array();

    // 1. Récupération et extraction éventuelle des archives ZIP
    foreach ($file_paths as $fpath) {
        if (!file_exists($fpath)) {
            continue;
        }
        $ext = strtolower(pathinfo($fpath, PATHINFO_EXTENSION));
        if ($ext === 'zip') {
            $extracted = siecle_bee_extract_zip($fpath, $temp_extracted_dir);
            foreach ($extracted as $ef) {
                $xml_files[] = $ef;
            }
        } elseif ($ext === 'xml') {
            $xml_files[] = $fpath;
        }
    }

    $stats['total_fichiers'] = count($xml_files);
    if (empty($xml_files)) {
        $stats['erreurs'][] = "Aucun fichier XML valide trouvé dans l'import.";
        return $stats;
    }

    // 2. Chargement et classification des fichiers XML
    libxml_use_internal_errors(true);
    $parsed_xmls = array(
        'structures'   => array(),
        'communs'      => array(),
        'responsables' => array(),
        'eleves'       => array()
    );

    foreach ($xml_files as $xf) {
        $xml_obj = @simplexml_load_file($xf);
        if ($xml_obj === false) {
            $errs = libxml_get_errors();
            $msg = basename($xf) . " : XML invalide.";
            if (!empty($errs)) {
                $msg .= " (" . trim($errs[0]->message) . ")";
            }
            $stats['erreurs'][] = $msg;
            libxml_clear_errors();
            continue;
        }

        $type = siecle_bee_detect_xml_type($xml_obj);
        $stats['fichiers_traites'][] = array(
            'fichier' => basename($xf),
            'type'    => $type
        );

        if (isset($parsed_xmls[$type])) {
            $parsed_xmls[$type][] = $xml_obj;
        }
    }

    // 3. Initialisation de la connexion base de données
    global $cnx;
    $cnx = cnx();

    // Purge préalable des élèves si demandée
    if (!empty($options['vide_eleve']) && $options['vide_eleve'] === 'oui') {
        purge_element_eleve();
    }

    // 4. Étape 1 : Traitement des Structures
    $structures_map = array('divisions' => array(), 'groupes' => array());
    foreach ($parsed_xmls['structures'] as $s_xml) {
        $res_s = siecle_bee_parse_structures($s_xml);
        foreach ($res_s['divisions'] as $code => $div) {
            $structures_map['divisions'][$code] = $div;
            $id_classe = chercheIdClasse($code);
            $code_mef = !empty($div['mefs']) ? $div['mefs'][0] : '';
            if (!$id_classe) {
                $cr_cl = create_classe_with_mef($code, $div['libelle_long'], $code_mef);
                if ($cr_cl > 0) {
                    $stats['classes_creees']++;
                }
            } else if ($code_mef !== '') {
                $curr_mef = chercherCodeMefClasse($id_classe);
                if (empty($curr_mef)) {
                    updateCodeMefClasse($id_classe, $code_mef);
                }
            }
        }
        foreach ($res_s['groupes'] as $code => $grp) {
            $structures_map['groupes'][$code] = $grp;
        }
    }

    // 5. Étape 2 : Traitement des Communs
    $communs_info = array();
    foreach ($parsed_xmls['communs'] as $c_xml) {
        $communs_info = siecle_bee_parse_communs($c_xml);
    }

    // 6. Étape 3 : Traitement des Responsables
    $responsables_map = array();
    foreach ($parsed_xmls['responsables'] as $r_xml) {
        $res_r = siecle_bee_parse_responsables($r_xml);
        foreach ($res_r as $eid => $r_data) {
            $responsables_map[$eid] = $r_data;
        }
    }
    $stats['responsables_charges'] = count($responsables_map);

    // 7. Étape 4 : Traitement des Élèves
    $cnx = cnx();
    $f_pass_path = "./data/fic_pass.txt";

    foreach ($parsed_xmls['eleves'] as $e_xml) {
        $eleves_list = siecle_bee_parse_eleves($e_xml, $responsables_map, $structures_map);
        foreach ($eleves_list as $el) {
            $stats['eleves_total']++;

            $nom    = strtolower(addslashes($el['nom']));
            $prenom = strtolower(addslashes($el['prenom']));

            // Date de naissance
            $date_naiss_raw = $el['date_naiss'];
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_naiss_raw)) {
                $date_naiss_db = $date_naiss_raw;
                $ascii = 0;
            } else {
                $date_naiss_db = ($date_naiss_raw !== '') ? $date_naiss_raw : dateDMY();
                $ascii = 1;
            }

            // Gestion de la date de sortie (départ de l'élève)
            if ($el['date_sortie'] !== '') {
                $ds_fmt = $ascii ? dateFormBase($el['date_sortie']) : $el['date_sortie'];
                $ds_fmt = preg_replace('/-/', '', (string)$ds_fmt);
                $aujour = dateYMD();
                if ($ds_fmt !== '' && $ds_fmt < $aujour) {
                    delete_compte_eleve($nom, $prenom, $date_naiss_db);
                    $stats['eleves_supprimes']++;
                    continue;
                }
            }

            // Résolution de la classe
            $id_classe = '';
            if ($el['code_classe'] !== '') {
                $id_classe = chercheIdClasse($el['code_classe']);
                $cl_mef = !empty($structures_map['divisions'][$el['code_classe']]['mefs'])
                    ? $structures_map['divisions'][$el['code_classe']]['mefs'][0]
                    : (!empty($el['code_mef']) ? $el['code_mef'] : '');
                if (!$id_classe) {
                    $lib_cl = isset($structures_map['divisions'][$el['code_classe']]['libelle_long'])
                        ? $structures_map['divisions'][$el['code_classe']]['libelle_long']
                        : $el['code_classe'];
                    $cr_cl = create_classe_with_mef($el['code_classe'], $lib_cl, $cl_mef);
                    if ($cr_cl > 0) {
                        $stats['classes_creees']++;
                    }
                    $id_classe = chercheIdClasse($el['code_classe']);
                } else if ($cl_mef !== '') {
                    $curr_mef = chercherCodeMefClasse($id_classe);
                    if (empty($curr_mef)) {
                        updateCodeMefClasse($id_classe, $cl_mef);
                    }
                }
            }

            $passwd       = passwd_random2();
            $passwd_eleve = passwd_random();

            $params = array(
                'ne'            => $nom,
                'pe'            => $prenom,
                'ce'            => $id_classe,
                'lv1'           => addslashes($el['lv1']),
                'lv2'           => addslashes($el['lv2']),
                'option'        => addslashes($el['option']),
                'regime'        => $el['regime'],
                'naiss'         => $date_naiss_db,
                'lieunais'      => strtolower(addslashes($el['lieu_naiss'])),
                'nat'           => '',
                'mdp'           => $passwd,
                'mdp2'          => '',
                'mdpeleve'      => $passwd_eleve,
                'nt'            => addslashes($el['nomtuteur']),
                'pt'            => addslashes($el['prenomtuteur']),
                'nadr1'         => '',
                'adr1'          => addslashes($el['adr1']),
                'cpadr1'        => addslashes($el['cpadr1']),
                'commadr1'      => addslashes($el['commadr1']),
                'nadr2'         => '',
                'adr2'          => addslashes($el['adr2']),
                'cpadr2'        => addslashes($el['cpadr2']),
                'commadr2'      => addslashes($el['commune_adr2']),
                'tel'           => addslashes($el['telephone']),
                'profp'         => '',
                'telprofp'      => '',
                'profm'         => '',
                'telprofm'      => '',
                'nomet'         => '',
                'numet'         => '',
                'cpet'          => '',
                'commet'        => '',
                'numero_eleve'  => addslashes($el['ine'] ?: $el['elenoet']),
                'email'         => addslashes($el['email_tuteur1']),
                'classe_ant'    => '',
                'annee_ant'     => '',
                'civ_1'         => (string)$el['civ_1'],
                'civ_2'         => (string)$el['civ_2'],
                'nom_resp2'     => addslashes($el['nom_resp_2']),
                'prenom_resp2'  => addslashes($el['prenom_resp_2']),
                'tel_port_1'    => addslashes($el['tel_port_1']),
                'tel_port_2'    => addslashes($el['tel_port_2']),
                'sexe'          => $el['sexe'],
                'numero_gep'    => addslashes($el['id_eleve_etab'] ?: $el['elenoet']),
                'boursier'      => 'non',
                'boursier_montant' => '',
                'indemnite_stage'  => '',
                'mail_eleve'    => addslashes($el['mail_eleve']),
                'tel_eleve'     => addslashes($el['tel_eleve']),
                'email_resp_2'  => addslashes($el['email_resp_2']),
                'annee_scolaire'=> '',
                'adr_eleve'     => addslashes($el['adr_eleve']),
                'ccp_eleve'     => addslashes($el['ccp_eleve']),
                'commune_eleve' => addslashes($el['commune_eleve']),
                'pays_eleve'    => addslashes($el['pays_eleve'])
            );

            $is_update     = !empty($options['update']) && $options['update'] == 1;
            $update_vide   = !empty($options['updatevide']) ? (int)$options['updatevide'] : 0;
            $update_passwd = !empty($options['updatepasswd']) ? (int)$options['updatepasswd'] : 0;

            if ($is_update) {
                $cr = create_update_eleve_scolnet($params['ne'], $params['pe'], $params['naiss'], $params, $ascii, $update_vide, $update_passwd);
            } elseif ($id_classe) {
                $cr = @create_eleve($params, $ascii);
            } else {
                $cr = @create_eleve_sans_classe($params, $ascii);
            }

            if ($cr == 1) {
                $f_pass = @fopen($f_pass_path, "a+");
                if ($f_pass) {
                    fwrite($f_pass, $params['ne'] . ";" . $params['pe'] . ";" . $passwd . ";" . $passwd_eleve . "<br />\n");
                    fclose($f_pass);
                }
                $stats['eleves_crees']++;
                if (function_exists('history_cmd') && isset($_SESSION["nom"])) {
                    history_cmd($_SESSION["nom"], "CREATION", "eleve BEE XML (" . $nom . " " . $prenom . ")");
                }
            } elseif ($cr == -2) {
                $stats['eleves_mis_a_jour']++;
            } elseif ($cr == -3) {
                $stats['eleves_deja_presents']++;
            } else {
                $stats['eleves_erreurs']++;
            }
        }
    }

    // 8. Traçabilité & Journalisation
    if (function_exists('acceslog')) {
        $log_details = sprintf(
            "Fichiers: %d | Eleves total: %d | Crees: %d | MAJ: %d | Deja presents: %d | Classes: %d | Responsables: %d",
            $stats['total_fichiers'],
            $stats['eleves_total'],
            $stats['eleves_crees'],
            $stats['eleves_mis_a_jour'],
            $stats['eleves_deja_presents'],
            $stats['classes_creees'],
            $stats['responsables_charges']
        );
        acceslog("IMPORT_SIECLE_BEE_XML: " . $log_details);
    }

    $today = dateDMY();
    $flog_path = "./" . (defined('REPADMIN') ? REPADMIN : 'admin') . "/data/fic_opinion.txt";
    $flog = @fopen($flog_path, "a+");
    if ($flog) {
        $nom_s = isset($_SESSION['nom']) ? $_SESSION['nom'] : 'SYSTEM';
        $prenom_s = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : '';
        $membre_s = isset($_SESSION['membre']) ? $_SESSION['membre'] : 'admin';
        $ecole_s  = defined('REPECOLE') ? REPECOLE : '';
        fwrite($flog, "<BR>Message du : <FONT color=red>$today</font> De :<FONT color=red> $nom_s $prenom_s</FONT> <BR>Membre : <font color=red> $membre_s</font><BR><B>Message :</B> <font color=red> IMPORTATION BEE XML / ZIP </font> - &Eacute;tablissement : <font color=red>$ecole_s</font>");
        fclose($flog);
    }

    // 9. Nettoyage des fichiers temporaires extraits
    if (is_dir($temp_extracted_dir)) {
        $extracted_files = glob($temp_extracted_dir . "/*");
        if ($extracted_files) {
            foreach ($extracted_files as $ef) {
                @unlink($ef);
            }
        }
        @rmdir($temp_extracted_dir);
    }

    return $stats;
}
