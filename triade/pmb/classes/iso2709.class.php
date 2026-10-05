<?php
// ---------------------------------------------------
//  iso2709_record : classe PHP pour la manipulation
//  d'enregistrements au format ISO2709
//	(c) François Lemarchand 2002
//	public release 0.0.6
//  Cette bibliothèque est distribuée sous la Licence 2 GNU GPL
//
//  Cette bibliothèque est distribuée car potentiellement utile mais
//  SANS AUCUNE GARANTIE, ni explicite, ni implicite, y compris les
//  garanties de commercialisation ou d'adaptation dans un but
//  spécifique. Reportez vous à la Licence Publique Générale GNU pour
//  plus de détails.
//
//  Tous les fichiers sont sous ce copyright sans exception.
//  Voir le fichier GPL.txt
//
// ---------------------------------------------------

// +-------------------------------------------------+
// ATTENTION, cette classe a été sérieusement débogguée par rapport à l'original. Les corrections ont été réalisées par PMB Services.
// © PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: iso2709.class.php,v 1.59.2.3.2.1 2025/03/04 10:45:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// on s'assure que la classe n'est pas définie afin
// d'éviter les inclusions multiples

if ( ! defined( 'ISO2709' ) ) {
    define( 'ISO2709', 1 );

    define('AUTO_UPDATE', 1);
    define('USER_UPDATE', 0);

    define("IS3",chr(0x1d));			//Caractère de fin d'enregistrement
    define("IS2",chr(0x1e));			//Caractère de fin de champ
    define("IS1",chr(0x1f));			//Caractère de début de sous champ
    define("NSBB",chr(0x88));			//Caractère de début "non sorting bloc"
    define("NSBE",chr(0x89));			//Caractère de fin "non sorting bloc"

    class iso2709_record {

        // enregistrement UNIMARC complet
        public $full_record;

        // parties de l'enregistrement UNIMARC
        public $guide = '';
        public $directory = '';
        public $data = '';

        // propriétés 'publiques'
        public $errors;
        public $warnings;
        // mode de mise à jour;
        public $auto_update;

        // variables 'internes' de la classe
        public $inner_guide;
        public $inner_directory;
        public $inner_data;

        // caractères spéciaux
        public $record_end;
        public $rgx_record_end;
        public $field_end;
        public $rgx_field_end;
        public $subfield_begin;
        public $rgx_subfield_begin;
        public $NSB_begin;
        public $rgx_NSB_begin;
        public $NSB_end;
        public $rgx_NSB_end;

        //Utilisé pour reconnaitre l'USMARC de UNIMARC (= unimarc ou usmarc)
        public $type_marc;
        //definit si notice encodee en utf-8
        public $is_utf8 = false;
        //Liste des formats marc utilisant la position 9 pour l'encodage
        private $use_pos9_utf = array("usmarc", "marc21");

        /**
         * constructeur : récupération de l'enregistrement
         *
         * @param string $string
         * @param string $update
         * @param string $type_marc
         */
        public function __construct($string = '', $update = AUTO_UPDATE, $type_marc = "unimarc")
        {
            // initialisation des caractères spéciaux
            $this->record_end = chr(0x1d); // fin de notice (IS3 de l'ISO 6630)
            $this->rgx_record_end = "\x1D";
            $this->field_end = chr(0x1e); // fin de champ (IS2 de l'ISO 6630)
            $this->rgx_field_end = "\x1E";
            $this->subfield_begin = chr(0x1f); // début de sous-champ (IS1 de l'ISO 6630)
            $this->rgx_subfield_begin = "\x1F";
            $this->NSB_begin = chr(0x88); // début de NSB
            $this->rgx_NSB_begin = "\x88";
            $this->NSB_end = chr(0x89); // fin de NSB (NSE)
            $this->rgx_NSB_end = "\x89";

            // initialisation du mode d'update
            $this->auto_update = $update;

            // initialisation du type marc de la notice (unimarc ou usmarc)
            $this->type_marc = $type_marc;
            # true : l'update est géré par la classe
            # false : c'est au script appelant de gérer l'update;

            // initialisation du tableau des erreurs
            $this->errors = array();

            // initialisation du tableau des warnings
            $this->warnings = array();

            // initialisation de la classe
            // récupération de l'enregistrement intégral
            $this->full_record = $string;

            // mise à jour des variables internes
            // guide de l'enregistrement
            $this->guide = substr($this->full_record, 0, 24);

            // guide interne : valeurs par défaut si création
            $rl = intval(substr($this->guide, 0, 5)); # record length : pos.1-4
            $rs = substr($this->guide, 5, 1); # record status : pos.5
            $dt = substr($this->guide, 6, 1); # document type : pos.6
            $bl = substr($this->guide, 7, 1); # bibliographic level : pos.7
            $hl = intval(substr($this->guide, 8, 1)); # hierarchical level : pos.8
            $pos9 = substr($this->guide, 9, 1); # pos.9 undefined, contains a blank (except for usmarc UTF8, contains 'a')
            $il = intval(substr($this->guide, 10, 1)); # indicator length : pos.10 (2)
            $sl = intval(substr($this->guide, 11, 1)); # subfield identifier length : pos.11 (2)
            $ba = intval(substr($this->guide, 12, 5)); # base adress : pos.12-16
            $el = substr($this->guide, 17, 1); # encoding level : pos.17
            $ru = substr($this->guide, 18, 1); # record update : pos.18
            $pos19 = substr($this->guide, 19, 1); # pos.19 : undefined, contains a blank
            $dm1 = intval(substr($this->guide, 20, 1)); # Length of 'Length of field' (pos.20, 4 in UNIMARC)
            $dm2 = intval(substr($this->guide, 21, 1)); # Length of 'Starting character position' (pos.21, 5 in UNIMARC)
            $dm3 = intval(substr($this->guide, 22, 1)); # Length of implementationdefined portion (pos.22, 0 in UNIMARC)
            $pos23 = substr($this->guide, 23, 1); # POS.23 : undefined, contains a blank

            // martizva - some server z3950 send UPCASE $bl !!!
            $bl = strtolower($bl);

            $this->inner_guide = array(
                'rl' => $rl ? $rl : 0,
                'rs' => $rs ? $rs : 'n',
                'dt' => $dt ? $dt : 'a',
                'bl' => $bl ? $bl : 'm',
                'hl' => $hl ? $hl : 0,
                'pos9' => $pos9 ? $pos9 : ' ',
                'il' => $il ? $il : 2,
                'sl' => $sl ? $sl : 2,
                'ba' => $ba ? $ba : 24,
                'el' => $el ? $el : '1',
                'ru' => $ru ? $ru : 'i',
                'pos19' => $pos19 ? $pos19 : ' ',
                'dm1' => $dm1 ? $dm1 : 4,
                'dm2' => $dm2 ? $dm2 : 5,
                'dm3' => $dm3 ? $dm3 : 0,
                'pos23' => $pos23 ? $pos23 : ' '
            );

            // récupération du répertoire
            $m = 3 + $this->inner_guide["dm1"] + $this->inner_guide["dm2"];

            $this->directory = substr($this->full_record, 24, $this->inner_guide["ba"] - 25);

            $tmp_dir = explode('|', chunk_split($this->directory, $m, '|'));
            for ($i = 0; $i < count($tmp_dir); $i ++) {
                if ($tmp_dir[$i]) {
                    $this->inner_directory[$i] = array(
                        'label' => substr($tmp_dir[$i], 0, 3),
                        'length' => intval(substr($tmp_dir[$i], 3, $this->inner_guide["dm1"])),
                        'adress' => intval(substr($tmp_dir[$i], 3 + $this->inner_guide["dm1"], $this->inner_guide["dm2"]))
                    );
                }
            }

            if ((! in_array($this->type_marc, $this->use_pos9_utf)) && ! (preg_match("/^[x-z]$/", $this->inner_guide['dt']))) {
                $this->inner_guide['pos9'] = ' ';
            }

            // récupération des champs
            $m = substr($this->full_record, $this->inner_guide["ba"], strlen($this->full_record) - $this->inner_guide["ba"]);
            if ($m) {
                foreach ($this->inner_directory as $cle => $valeur) {
                    $this->inner_data[$cle] = array(
                        'label' => $this->inner_directory[$cle]["label"],
                        'content' => substr($this->full_record, $this->inner_guide["ba"] + $valeur["adress"], $valeur["length"])
                    );
                    if ($this->inner_data[$cle]['label'] == '100')
                        $f100 = $this->inner_data[$cle]['content'];
                }

                // Prise en compte de l'encodage des notices en UTF-8
                $encodage_fic_source = (isset($_SESSION["encodage_fic_source"]) ? $_SESSION["encodage_fic_source"] : '');
                if ($encodage_fic_source) { // On choisi de forcer l'encodage des notices lues
                    if ($encodage_fic_source == "utf8") {
                        $this->is_utf8 = true;
                    }
                } else {
                    if (in_array($this->type_marc, $this->use_pos9_utf)) {
                        if ($this->inner_guide['pos9'] == 'a') {
                            $this->is_utf8 = true; // USMARC
                        }
                    } else {
                        if (substr($f100, 30, 2) == '50') {
                            $this->is_utf8 = true; // UNIMARC
                        }
                    }
                }
            } else {
                $this->inner_data = array();
                $this->inner_directory = array();
            }
        }

        /**
         * récupération d'un ou plusieurs sous-champ(s)
         *
         * @return array
         */
        public function get_subfield() {
            $result = array();

            // vérification des paramètres
            if (! func_num_args()) {
                return $result;
            }

            for ($i = 0; $i < count($this->inner_data); $i ++) {
                if (preg_match('/' . func_get_arg(0) . '/', $this->inner_data[$i]["label"])) {

                    switch (func_num_args()) {

                        case 1:
                            // pas d'indication de sous-champ : on retourne le contenu entier
                            $result[] = $this->ISO_decode(preg_replace("/$this->rgx_field_end/", '', $this->inner_data[$i]["content"]));
                            break;

                        case 2:
                            // un seul sous-champ demandé
                            // récupération de la valeur du champ
                            $field = $this->inner_data[$i]["content"];
                            // le masque de recherche : subfield_begin cars. subfield_begin ou field_end
                            $mask = $this->rgx_subfield_begin . func_get_arg(1);
                            // MODIF ER
                            // $mask .= '(.*)['.$this->rgx_subfield_begin.'|'.$this->rgx_field_end.']';
                            $mask .= '(.*)[' . $this->rgx_subfield_begin . '' . $this->rgx_field_end . ']';
                            while (preg_match("/$mask/sU", $field)) {
                                preg_match("/$mask/sU", $field, $regs);
                                $result[] = $this->ISO_decode($regs[1]);
                                $field = preg_replace("/$mask/sU", '', $field);
                            }
                            break;

                        default:
                            // un ou plusieurs sous-champs
                            // récupération de la valeur du champ
                            $field = $this->inner_data[$i]["content"];
                            for ($j = 1; $j < func_num_args(); $j ++) {
                                $subfield = func_get_arg($j);
                                $mask = $this->rgx_subfield_begin . $subfield;
                                $mask .= '(.*)[' . $this->rgx_subfield_begin . '' . $this->rgx_field_end . ']';
                                preg_match("/$mask/sU", $field, $regs);
                                if (isset($regs[1])) {
                                    $tmp[$subfield] = $this->ISO_decode($regs[1]);
                                } else {
                                    $tmp[$subfield] = '';
                                }
                            }
                            $result[] = $tmp;
                            break;
                    }
                }
            }
            return $result;
        }

        /**
         * Retourne le tableau des sous champs du champ $field
         * Si $subfield est vide (le code d'un sous champ), la fonction retourne un tableau de tableaux :
         * array(array("label"=>code du sous champ,"content"=>valeur du sous champ))
         * Sinon, si le sous champ est précisé, la fonction retourne un tableau simple correspondant
         * à toutes les valeurs trouvées pour le sous champ $subfield
         *
         */
        public function get_subfield_array($field, $subfield = "")
        {
            $result = array();
            $res_inter = array();
            for ($i = 0; $i < count($this->inner_data); $i ++) {
                if ($this->inner_data[$i]["label"] == $field) {
                    $content = substr($this->inner_data[$i]["content"], 0, strlen($this->inner_data[$i]["content"]) - 1);
                    $sub_fields = explode(chr(31), $content);
                    for ($j = 1; $j < count($sub_fields); $j ++) {
                        $res = array();
                        $res["label"] = substr($sub_fields[$j], 0, 1);
                        $res["content"] = $this->ISO_decode(substr($sub_fields[$j], 1));
                        $res_inter[] = $res;
                    }
                }
            }
            if ($subfield != "") {
                foreach ($res_inter as $inter) {
                    if ($inter["label"] == $subfield) {
                        $result[] = $inter["content"];
                    }
                }
            } else {
                $result = $res_inter;
            }
            return $result;
        }

        /**
         * Retourne le tableau des sous champs du champ $field
         * Si $subfield est vide (le code d'un sous champ), la fonction retourne un tableau de tableaux :
         * array(array("label"=>code du sous champ,"content"=>valeur du sous champ))
         * Sinon, si le sous champ est précisé, la fonction retourne un tableau simple correspondant
         * à toutes les valeurs trouvées pour le sous champ $subfield
         *
         */
        public function get_subfield_array_array($field, $subfield = "")
        {
            $result_field = array();
            for ($i = 0; $i < count($this->inner_data); $i ++) {
                if ($this->inner_data[$i]["label"] == $field) {
                    $result = array();
                    $res_inter = array();
                    $content = substr($this->inner_data[$i]["content"], 0, strlen($this->inner_data[$i]["content"]) - 1);
                    $sub_fields = explode(chr(31), $content);
                    for ($j = 1; $j < count($sub_fields); $j ++) {
                        $res = array();
                        $res["label"] = substr($sub_fields[$j], 0, 1);
                        $res["content"] = $this->ISO_decode(substr($sub_fields[$j], 1));
                        $res_inter[] = $res;
                    }
                    if ($subfield != "") {
                        foreach ($res_inter as $inter) {
                            if ($inter["label"] == $subfield) {
                                $result[] = $inter["content"];
                            }
                        }
                    } else {
                        $result = $res_inter;
                    }
                    $result_field[] = $result;
                }
            }
            return $result_field;
        }

        public function get_all_fields($field)
        {
            $result_fields = array();
            for ($i = 0; $i < count($this->inner_data); $i ++) {
                if (preg_match('/' . $field . '/', $this->inner_data[$i]["label"])) {
                    $content = substr($this->inner_data[$i]["content"], 0, strlen($this->inner_data[$i]["content"]) - 1);
                    $sub_fields = explode(chr(0x1F), $content);
                    $res = array();
                    for ($j = 1; $j < count($sub_fields); $j ++) {
                        $res[substr($sub_fields[$j], 0, 1)][] = $this->ISO_decode(substr($sub_fields[$j], 1));
                    }
                    $result_fields[$this->inner_data[$i]["label"]][] = $res;
                }
            }
            return $result_fields;
        }

        /**
         * ajout d'un champ
         *
         * @param string $label
         * @param string $ind
         * @return boolean
         */
        public function add_field($label = '000', $ind = '')
        {
            global $charset;

            // vérification des paramètres : au moins 2
            if (func_num_args() < 3) {
                $txt_error = '[add_field] impossible d\'ajouter un champ vide';
                $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
                return false;
            }

            if ($label < 1) {
                $txt_error = '[add_field] le label \'' . $label . '\' n\'est pas valide';
                $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
                return false;
            }

            // test des indicateurs
            if (strlen($ind) != 0 && strlen($ind) != $this->inner_guide['il']) {
                $txt_error = '[add_field] l\'indicateur \'' . $ind . '\' n\'est pas valide';
                $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
                return false;
            }

            // mise en form du label
            if (strlen($label) < 3 && $label < 100)
                $label = sprintf('%03d', $label);

            // notre champ doit commencer par un label
            if (! preg_match('/^[0-9]{3}$/', $label)) {
                $txt_error = '[add_field] le label \'' . $label . '\' n\'est pas valide';
                $this->last_error = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
                return false;
            }

            $nb_args = func_num_args();

            // suivant le cas, ajout des infos
            switch ($nb_args) {
                case 3: // il n'y a qu'un seul param en plus du label et des indicateurs
                    if (! is_array(func_get_arg(2))) {
                        $content = func_get_arg(2);
                    } else {
                        // le param est un tableau
                        $field = func_get_arg(2);
                        $content = '';
                        for ($i = 0; $i < count($field); $i ++) {
                            if (preg_match('/^[a-zA-Z0-9]$/', $field[$i][0]) && $field[$i][1]) {
                                $content .= $this->subfield_begin . $field[$i][0] . $field[$i][1];
                            }
                        }
                    }
                    break;
                default:
                    // plus d'un champ
                    // on s'assure que le nombre de param est pair
                    if (floor($nb_args / 2) < $nb_args / 2)
                        $nb_args = $nb_args - 1;
                    // récupérer les paires champ/valeur
                    $i = 2;
                    while ($i < $nb_args - 1) {
                        $field = func_get_arg($i);
                        $fieldbis = func_get_arg($i + 1);
                        if (preg_match('/^[a-zA-Z0-9]$/', $field)) {
                            $content .= $this->subfield_begin . $field . $fieldbis;
                        } else {
                            $txt_error = '[add_field] étiquette de sous-champ non valide';
                            $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
                        }
                        $i = $i + 2;
                    }
                    break;
            }

            if (! empty($content)) {
                $content = $this->ISO_encode($content) . $this->field_end;

                // ajout des éventuels indicateurs
                if (strlen($ind) == $this->inner_guide["il"])
                    $content = $ind . $content;

                // mise à jour des inner_data
                $index = count($this->inner_data);
                $this->inner_data[$index]["label"] = $label;
                $this->inner_data[$index]["content"] = $content;
            }

            if ($this->auto_update)
                $this->update();
            return true;
        }

        /**
         * suppression d'un champ
         *
         * @return boolean
         */
        public function delete_field($label, $index = - 1)
        {
            global $charset;

            if (! func_num_args()) {
                $txt_error = '[delete_field] pas de label pour le champ';
                $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
                return false;
            }

            if (! $label) {
                $txt_error = '[delete_field] le label \'' . $label . '\' n\'est pas valide';
                $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
                return false;
            }

            // mise en form du label
            if (strlen($label) < 3 && $label < 100) {
                $label = sprintf('%03d', $label);
            }

            // vérification du format du label
            if (! preg_match('/^[0-9\.]{3}$/', $label)) {
                $txt_error = '[delete_field] le label \'' . $label . '\' n\'est pas valide';
                $this->last_error = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
                return false;
            }

            for ($i = 0; $i < count($this->inner_data); $i ++) {
                if (preg_match('/' . $label . '/', $this->inner_data[$i]["label"])) {
                    $this->inner_data[$i]["label"] = '';
                    $this->inner_data[$i]["content"] = '';
                }
            }

            if ($this->auto_update) {
                $this->update();
            }
            return true;
        }

        /**
         * update de l'enregistrement
         */
        public function update()
        {
            global $charset;

            // supprime les lignes vides d'inner_data et gestion de l'encodage
            $ch_100_trouve = false;
            for ($i = 0; $i < count($this->inner_data); $i ++) {
                if (empty($this->inner_data[$i]["label"]) || empty($this->inner_data[$i]["content"])) {
                    array_splice($this->inner_data, $i, 1);
                    $i --;
                }

                // Gestion de l'encodage de la notice
                if ($this->inner_data[$i]["label"] == "100") {
                    if (preg_match("#^(.*?)(" . $this->subfield_begin . ".*?)" . $this->field_end . "#", $this->inner_data[$i]["content"], $matches)) {
                        if (strlen($matches[1]) != 2) {
                            $this->inner_data[$i]["content"] = "  " . $matches[2];
                        }
                    }
                    if (strlen($this->inner_data[$i]["content"]) > 35) {
                        $this->inner_data[$i]["content"] = substr($this->inner_data[$i]["content"], 0, 35);
                    } elseif (strlen($this->inner_data[$i]["content"]) < 31) {
                        $this->inner_data[$i]["content"] = substr($this->inner_data[$i]["content"], 0, - 1); // j'enlève le caractère de fin de champ
                    }
                    if ($this->is_utf8) {
                        $this->inner_data[$i]["content"][30] = "5";
                        $this->inner_data[$i]["content"][31] = "0";
                        $this->inner_data[$i]["content"][32] = " ";
                        $this->inner_data[$i]["content"][33] = " ";
                        $this->inner_data[$i]["content"][34] = $this->field_end;
                    } else {
                        $this->inner_data[$i]["content"][30] = "0";
                        $this->inner_data[$i]["content"][31] = "1";
                        $this->inner_data[$i]["content"][32] = "0";
                        $this->inner_data[$i]["content"][33] = "3";
                        $this->inner_data[$i]["content"][34] = $this->field_end;
                    }
                    $ch_100_trouve = true;
                }
            }

            // Gestion de l'encodage de la notice
            if (! $ch_100_trouve) {
                $tmp = array();
                $tmp["label"] = "100";
                if ($this->is_utf8) {
                    $tmp["content"] = "  " . $this->subfield_begin . "a                          50" . $this->field_end;
                } else {
                    $tmp["content"] = "  " . $this->subfield_begin . "a                          0103" . $this->field_end;
                }
                $this->inner_data[] = $tmp;
            }

            // reconstitution inner_directory
            $this->inner_directory = array();
            for ($i = 0; $i < count($this->inner_data); $i ++) {

                if (strlen($this->inner_data[$i]["content"]) > 9999) {
                    // Si le champs est trop long on le découpe et on créer un warning
                    $tempo = $this->inner_data[$i]["content"];
                    $this->inner_data[$i]["content"] = substr($tempo, 0, 9998) . substr($tempo, - 1);
                    $num_notice = $this->get_subfield("001");
                    $txt = $num_notice[0] ? $num_notice[0] . " " : "";
                    $txt_warning = '[warning : longueur] notice ' . $txt . 'exportée mais champ \'' . $this->inner_data[$i]["label"] . '\' tronqué';
                    $this->warnings[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_warning) : $txt_warning;
                }
                $this->inner_directory[$i] = array(
                    'label' => $this->inner_data[$i]["label"],
                    'length' => strlen($this->inner_data[$i]["content"]),
                    'adress' => 0
                );
            }

            // mise à jour des offset et du répertoire 'réel'
            for ($i = 1; $i < count($this->inner_data); $i ++) {
                $this->inner_directory[$i]["adress"] = $this->inner_directory[$i - 1]["length"] + $this->inner_directory[$i - 1]["adress"];
            }

                // mise à jour du répertoire
            $this->directory = '';
            for ($i = 0; $i < count($this->inner_directory); $i ++) {
                $this->directory .= sprintf('%03d', $this->inner_directory[$i]["label"]);
                $this->directory .= sprintf('%0' . $this->inner_guide["dm1"] . 'd', $this->inner_directory[$i]["length"]);
                $this->directory .= sprintf('%0' . $this->inner_guide["dm2"] . 'd', $this->inner_directory[$i]["adress"]);
            }

                // mise à jour du contenu
            $this->data = $this->field_end;
            for ($i = 0; $i < count($this->inner_data); $i ++) {
                $this->data .= $this->inner_data[$i]["content"];
            }
            $this->data .= $this->record_end;

                // mise à jour du guide
                # # adresse de base.
            $this->inner_guide["ba"] = 24 + strlen($this->directory) + 1;
            # # longueur de l'enregistrement iso2709
            $this->inner_guide["rl"] = 24 + strlen($this->directory) + strlen($this->data);

            $this->guide = sprintf('%05d', $this->inner_guide["rl"]);
            $this->guide .= $this->inner_guide["rs"];
            $this->guide .= $this->inner_guide["dt"];
            $this->guide .= $this->inner_guide["bl"];
            $this->guide .= $this->inner_guide["hl"];
            $this->guide .= $this->inner_guide["pos9"];
            $this->guide .= $this->inner_guide["il"];
            $this->guide .= $this->inner_guide["sl"];
            $this->guide .= sprintf('%05d', $this->inner_guide["ba"]);
            $this->guide .= $this->inner_guide["el"];
            $this->guide .= $this->inner_guide["ru"];
            $this->guide .= $this->inner_guide["pos19"];
            $this->guide .= $this->inner_guide["dm1"];
            $this->guide .= $this->inner_guide["dm2"];
            $this->guide .= $this->inner_guide["dm3"];
            $this->guide .= $this->inner_guide["pos23"];

            // constitution du nouvel enregistrement
            $this->full_record = $this->guide . $this->directory . $this->data;
        }

        /**
         * affichage d'un rapport des erreurs
         */
        public function show_errors()
        {
            if (count($this->errors)) {
                print '<table style="border:1px" role="presentation">';
                print '<tr><th colspan=\'2\'>iso2709_record : erreurs</th></tr>';
                for ($i = 0; $i < count($this->errors); $i ++) {
                    print '<tr><td>';
                    print $i + 1;
                    print '</td><td>' . $this->errors[$i] . '</td></tr>';
                }
                print '</table>';
            } else {
                print 'aucune erreur<br />';
            }
        }

        /**
         * fonction de validation d'un enregistrement
         *
         * @param string $mode
         * @return boolean
         */
        public function valid($mode = "convert")
        {
            global $charset;

            // $this->errors = array(); // init du tableau des erreurs
            $num_notice = $this->get_subfield("001");
            $txt = (isset($num_notice[0]) && $num_notice[0] ? $num_notice[0] . " " : "");
            // test de la longueur de l'enregistrement
            if (strlen($this->full_record) != $this->inner_guide['rl'] || substr($this->full_record, - 1, 1) != $this->record_end) {
                $txt_error = '[error : format] notice ' . $txt . 'perdue : La longueur de l\'enregistrement ne correspond pas au guide';
                $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
            }

            // test des fin de champs
            // on retourne false si un champ ne finit pas par l'IS3
            foreach ($this->inner_data as $cle => $valeur) {
                if (! preg_match("/" . $this->rgx_field_end . "$/", $valeur["content"])) {
                    $txt_error = '[error : format] notice ' . $txt . 'perdue : Le champ ' . $cle . ' ne finit pas par le caractère de fin de champ';
                    $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
                }
            }

                // les tableaux internes sont vides
            if (! count($this->inner_data)) {
                $txt_error = '[error : internal] notice ' . $txt . 'perdue : Cet enregistrement est vide';
                $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
            }

            // les inner_data et le inner_directory ne sont pas synchronisés
            if (count($this->inner_data) != count($this->inner_directory)) {
                $txt_error = '[error : internal] notice ' . $txt . 'perdue : Les tableaux internes ne sont pas synchronisés';
                $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
            }

            if (($mode == "import_notice") && (trim($this->inner_guide['pos9'])) && (preg_match("/^[a-l]$/", $this->inner_guide['pos9']))) {
                $txt_error = '[error : format] notice ' . $txt . 'perdue : Il ne s\'agit pas d\'une notice bibliographique (voir norme UNIMARC B) (' . $this->inner_guide['pos9'] . ')';
                $this->errors[] = ($charset == 'utf-8') ? encoding_normalize::utf8_encode($txt_error) : $txt_error;
            }

            if (count($this->errors)) {
                return false;
            }
            return true;
        }

        // ---------------------------------------------------
        //		fonctions de mise à jour du guide
        // ---------------------------------------------------
        public function set_rs($status)
        {
            if ($status) {
                $this->inner_guide["rs"] = $status[0];
                if ($this->auto_update)
                    $this->update();
            }
        }

        public function set_dt($dtype)
        {
            if ($dtype) {
                $this->inner_guide["dt"] = $dtype[0];
                if ($this->auto_update)
                    $this->update();
            }
        }

        public function set_bl($bltype)
        {
            if ($bltype) {
                $this->inner_guide["bl"] = $bltype[0];
                if ($this->auto_update)
                    $this->update();
            }
        }

        public function set_hl($hltype)
        {
            if ($hltype) {
                $this->inner_guide["hl"] = $hltype[0];
                if ($this->auto_update)
                    $this->update();
            }
        }

        public function set_el($eltype)
        {
            if ($eltype) {
                $this->inner_guide["el"] = $eltype[0];
                if ($this->auto_update)
                    $this->update();
            }
        }

        public function set_ru($rutype)
        {
            if ($rutype) {
                $this->inner_guide["ru"] = $rutype[0];
                if ($this->auto_update)
                    $this->update();
            }
        }

        /**
         * Tables de conversion ISO 646 & 5426 / ISO 8859-15
         */
        public static function iso_tables()
        {
            global $ISO5426, $ISO5426_dia, $ISO8859_15, $ISO8859_15_dia;
            // Tableaux de correspondance de ISO646/5426 vers ISO8859-15
            $ISO5426_dia = array(
                chr(0xc1) . chr(0x41) => chr(0xc0),
                chr(0xc1) . chr(0x45) => chr(0xc8),
                chr(0xc1) . chr(0x49) => chr(0xcc),
                chr(0xc1) . chr(0x4f) => chr(0xd2),
                chr(0xc1) . chr(0x55) => chr(0xd9),
                chr(0xc1) . chr(0x61) => chr(0xe0),
                chr(0xc1) . chr(0x65) => chr(0xe8),
                chr(0xc1) . chr(0x69) => chr(0xec),
                chr(0xc1) . chr(0x6f) => chr(0xf2),
                chr(0xc1) . chr(0x75) => chr(0xf9),
                chr(0xc2) . chr(0x41) => chr(0xc1),
                chr(0xc2) . chr(0x45) => chr(0xc9),
                chr(0xc2) . chr(0x49) => chr(0xcd),
                chr(0xc2) . chr(0x4f) => chr(0xd3),
                chr(0xc2) . chr(0x55) => chr(0xda),
                chr(0xc2) . chr(0x59) => chr(0xdd),
                chr(0xc2) . chr(0x61) => chr(0xe1),
                chr(0xc2) . chr(0x65) => chr(0xe9),
                chr(0xc2) . chr(0x69) => chr(0xed),
                chr(0xc2) . chr(0x6f) => chr(0xf3),
                chr(0xc2) . chr(0x75) => chr(0xfa),
                chr(0xc2) . chr(0x79) => chr(0xfd),
                chr(0xc3) . chr(0x41) => chr(0xc2),
                chr(0xc3) . chr(0x45) => chr(0xca),
                chr(0xc3) . chr(0x49) => chr(0xce),
                chr(0xc3) . chr(0x4f) => chr(0xd4),
                chr(0xc3) . chr(0x55) => chr(0xdb),
                chr(0xc3) . chr(0x61) => chr(0xe2),
                chr(0xc3) . chr(0x65) => chr(0xea),
                chr(0xc3) . chr(0x69) => chr(0xee),
                chr(0xc3) . chr(0x6f) => chr(0xf4),
                chr(0xc3) . chr(0x75) => chr(0xfb),
                chr(0xc4) . chr(0x41) => chr(0xc3),
                chr(0xc4) . chr(0x4e) => chr(0xd1),
                chr(0xc4) . chr(0x4f) => chr(0xd5),
                chr(0xc4) . chr(0x61) => chr(0xe3),
                chr(0xc4) . chr(0x6e) => chr(0xf1),
                chr(0xc4) . chr(0x6f) => chr(0xf5),
                chr(0xc8) . chr(0x41) => chr(0xc4),
                chr(0xc8) . chr(0x45) => chr(0xcb),
                chr(0xc8) . chr(0x49) => chr(0xcf),
                chr(0xc8) . chr(0x4f) => chr(0xd6),
                chr(0xc8) . chr(0x55) => chr(0xdc),
                chr(0xc8) . chr(0x59) => chr(0xbe),
                chr(0xc8) . chr(0x61) => chr(0xe4),
                chr(0xc8) . chr(0x65) => chr(0xeb),
                chr(0xc8) . chr(0x69) => chr(0xef),
                chr(0xc8) . chr(0x6f) => chr(0xf6),
                chr(0xc8) . chr(0x75) => chr(0xfc),
                chr(0xc8) . chr(0x79) => chr(0xff),
                chr(0xc9) . chr(0x41) => chr(0xc4),
                chr(0xc9) . chr(0x45) => chr(0xcb),
                chr(0xc9) . chr(0x49) => chr(0xcf),
                chr(0xc9) . chr(0x4f) => chr(0xd6),
                chr(0xc9) . chr(0x55) => chr(0xdc),
                chr(0xc8) . chr(0x59) => chr(0xbe),
                chr(0xc9) . chr(0x61) => chr(0xe4),
                chr(0xc9) . chr(0x65) => chr(0xeb),
                chr(0xc9) . chr(0x69) => chr(0xef),
                chr(0xc9) . chr(0x6f) => chr(0xf6),
                chr(0xc9) . chr(0x75) => chr(0xfc),
                chr(0xc9) . chr(0x79) => chr(0xff),
                chr(0xca) . chr(0x41) => chr(0xc5),
                chr(0xca) . chr(0x61) => chr(0xe5),
                chr(0xd0) . chr(0x43) => chr(0xc7),
                chr(0xd0) . chr(0x63) => chr(0xe7),
                chr(0xcf) . chr(0x53) => chr(0xa6),
                chr(0xcf) . chr(0x73) => chr(0xa8),
                chr(0xcf) . chr(0x5a) => chr(0xb4),
                chr(0xc5) . chr(0x20) => chr(0xaf),
                chr(0xca) . chr(0x20) => chr(0xb0),
                chr(0xc7) . chr(0x20) => chr(0xba)
            );

            $ISO5426 = array(
                chr(0xa0) => chr(0xa0),
                chr(0xa1) => chr(0xa1),
                chr(0xa2) => chr(0x22),
                chr(0xa3) => chr(0xa3),
                chr(0xa4) => chr(0x45) . chr(0x75) . chr(0x72),
                chr(0xa5) => chr(0xa5),
                chr(0xa6) => chr(0x3f),
                chr(0xa7) => chr(0xa7),
                chr(0xa8) => chr(0x27),
                chr(0xa9) => chr(0x60),
                chr(0xaa) => chr(0x22),
                chr(0xab) => chr(0xab),
                chr(0xac) => chr(0x62),
                chr(0xad) => chr(0xa9),
                chr(0xae) => chr(0x28) . chr(0x50) . chr(0x29),
                chr(0xaf) => chr(0xae),
                chr(0xb0) => chr(0xb0),
                chr(0xb1) => chr(0x3f),
                chr(0xb2) => chr(0x2c),
                chr(0xb3) => chr(0x3f),
                chr(0xb4) => chr(0x3f),
                chr(0xb5) => chr(0x3f),
                chr(0xb6) => chr(0x3f),
                chr(0xb7) => chr(0xb7),
                chr(0xb8) => chr(0x27) . chr(0x27),
                chr(0xb9) => chr(0x27),
                chr(0xba) => chr(0x22),
                chr(0xbb) => chr(0xbb),
                chr(0xbc) => chr(0x23),
                chr(0xbd) => chr(0x27),
                chr(0xbe) => chr(0x22),
                chr(0xbf) => chr(0xbf),
                chr(0xe0) => chr(0x3f),
                chr(0xe1) => chr(0xc6),
                chr(0xe2) => chr(0xd0),
                chr(0xe3) => chr(0x3f),
                chr(0xe4) => chr(0x3f),
                chr(0xe5) => chr(0x3f),
                chr(0xe6) => chr(0x49) . chr(0x4a),
                chr(0xe7) => chr(0x3f),
                chr(0xe8) => chr(0x4c),
                chr(0xe9) => chr(0xd8),
                chr(0xea) => chr(0x4f) . chr(0x65),
                chr(0xeb) => chr(0x3f),
                chr(0xec) => chr(0xde),
                chr(0xed) => chr(0x3f),
                chr(0xee) => chr(0x3f),
                chr(0xef) => chr(0x3f),
                chr(0xf0) => chr(0x3f),
                chr(0xf1) => chr(0xe6),
                chr(0xf2) => chr(0x64),
                chr(0xf3) => chr(0xf0),
                chr(0xf4) => chr(0x3f),
                chr(0xf5) => chr(0x69),
                chr(0xf6) => chr(0x69) . chr(0x6a),
                chr(0xf7) => chr(0x3f),
                chr(0xf8) => chr(0x6c),
                chr(0xf9) => chr(0xf8),
                chr(0xfa) => chr(0x6f) . chr(0x65),
                chr(0xfb) => chr(0xdf),
                chr(0xfc) => chr(0xfe),
                chr(0xfd) => chr(0x3f),
                chr(0xfe) => chr(0x3f),
                chr(0xff) => chr(0x3f)
            );

            // Tableaux de correspondance de ISO8859-15 vers ISO646/5426
            // Pour les diacritiques, il y a correspondance biunivoque, on fait donc une inversion du tableau
            $ISO8859_15_dia = array_flip($ISO5426_dia);

            // Pour les caractères spéciaux, la transformation n'est pas biunivoque
            $ISO8859_15 = array(
                chr(0xa0) => chr(0xa0),
                chr(0xa1) => chr(0xa1),
                chr(0xa2) => chr(0x3f),
                chr(0xa3) => chr(0xa3),
                chr(0xa4) => chr(0x80),
                chr(0xa5) => chr(0xa5),
                chr(0xa6) => chr(0xcf) . chr(0x53),
                chr(0xa7) => chr(0xa7),
                chr(0xa8) => chr(0xcf) . chr(0x73),
                chr(0xa9) => chr(0xad),
                chr(0xaa) => chr(0x41),
                chr(0xab) => chr(0xab),
                chr(0xac) => chr(0x3f),
                chr(0xad) => chr(0xa0),
                chr(0xae) => chr(0xaf),
                chr(0xb1) => chr(0xd8) . chr(0x2b),
                chr(0xb2) => chr(0x32),
                chr(0xb3) => chr(0x33),
                chr(0xb4) => chr(0xcf) . chr(0x5a),
                chr(0xb5) => chr(0x75),
                chr(0xb6) => chr(0x20),
                chr(0xb7) => chr(0xb7),
                chr(0xb8) => chr(0xcf) . chr(0x7a),
                chr(0xb9) => chr(0x31),
                chr(0xbb) => chr(0xbb),
                chr(0xbc) => chr(0x4f) . chr(0x45),
                chr(0xbd) => chr(0x6f) . chr(0x65),
                chr(0xbf) => chr(0xbf),
                chr(0xc6) => chr(0xe1),
                chr(0xd0) => chr(0xe2),
                chr(0xd7) => chr(0x2a),
                chr(0xd8) => chr(0xe9),
                chr(0xde) => chr(0xec),
                chr(0xdf) => chr(0xfb),
                chr(0xe6) => chr(0xf1),
                chr(0xf0) => chr(0xf3),
                chr(0xf7) => chr(0x2f),
                chr(0xf8) => chr(0xf9),
                chr(0xfe) => chr(0xfc)
            );
        }

        /**
         * Conversion d'une chaine ISO 8859-15 en ISO 646/5426
         */
        public function ISO_646_5426_encode($string)
        {
            global $ISO5426, $ISO5426_dia, $ISO8859_15, $ISO8859_15_dia;
            if (! isset($ISO5426)) {
                static::iso_tables();
            }

            $string_r = "";
            for ($i = 0; $i < strlen($string); $i ++) {
                if ($string[$i] < chr(0xa0)) {
                    $string_r .= $string[$i];
                } else if ($ISO8859_15_dia[$string[$i]]) {
                    $string_r .= $ISO8859_15_dia[$string[$i]];
                } else if ($ISO8859_15[$string[$i]]) {
                    $string_r .= $ISO8859_15[$string[$i]];
                } else {
                    $string_r .= "?";
                }
            }
            return $string_r;
        }

        /**
         * Conversion d'une chaine ISO 646 / 5426 en ISO 8859-15
         *
         * @param string $string
         * @return string
         */
        public static function ISO_646_5426_decode($string)
        {
            global $ISO5426, $ISO5426_dia, $ISO8859_15, $ISO8859_15_dia;
            if (! isset($ISO5426)) {
                static::iso_tables();
            }
            // Remplacement des symboles et caractères spéciaux
            $string_r = "";
            for ($i = 0; $i < strlen($string); $i ++) {
                // Si c'est un caractère avant 0xA0 alors rien a changer
                if ($string[$i] < chr(0xA0)) {
                    $string_r .= $string[$i];
                } else if (($string[$i] >= chr(0xC0)) && ($string[$i] <= chr(0xDF))) {
                    // Si c'est un diacritique on regarde le caractère suivant et on cherche dans la table de correspondance
                    $car = $string[$i] . $string[$i + 1];
                    // Si le caractère est connu
                    if ($ISO5426_dia[$car]) {
                        $string_r .= $ISO5426_dia[$car];
                    } else {
                        // Sinon on ne tient juste pas compte du diacritique
                        $string_r .= $string[$i + 1];
                    }
                    // On avance d'un caractère
                    $i ++;
                } else {
                    // Sinon c'est un catactère spécial ou un symbole
                    $car = $string[$i];
                    $string_r .= $ISO5426[$car];
                }
            }
            $string_r = str_replace(NSBB, "", $string_r);
            if (preg_match("/[a-zA-Z0-9]" . NSBE . "[a-zA-Z0-9]/", $string_r)) {
                $string_r = str_replace(NSBE, " ", $string_r);
            } else {
                $string_r = str_replace(NSBE, "", $string_r);
            }
            // $string_r=str_replace(NSBE,"",$string_r);
            return $string_r;
        }

        public function ISO_decode($chaine)
        {
            global $ISO_decode_do_not_decode;
            global $charset;
            $encodage_fic_source = (isset($_SESSION["encodage_fic_source"]) ? $_SESSION["encodage_fic_source"] : '');
            if ($ISO_decode_do_not_decode) {
                return $chaine;
            }

            // On a forcé l'encodage au moment de l'import ou de la conversion
            if ($encodage_fic_source == "iso8859") {
                if ($charset !== 'utf-8') { // Le charset de PMB est en iso-8859
                    return $chaine;
                } else {
                    $chaine = encoding_normalize::utf8_encode($chaine);
                    return $chaine;
                }
            } elseif ($encodage_fic_source == "iso5426") {
                $chaine = static::ISO_646_5426_decode($chaine);
                if ($charset !== 'utf-8') { // Le charset de PMB est en iso-8859
                    return $chaine;
                } else {
                    $chaine = encoding_normalize::utf8_encode($chaine);
                    return $chaine;
                }
            } elseif ($encodage_fic_source == "utf8") {
                if ($charset !== 'utf-8') { // Le charset de PMB est en iso-8859
                    return encoding_normalize::utf8_decode($chaine);
                } else {
                    return $chaine;
                }
            }

            // Fonctionnement normal

            // On verifie que la chaine est bien en utf8 comme indique dans la notice (pour les fournisseurs qui font n'importe quoi !!!)
            $check_utf8_encoding = false;
            if ($this->is_utf8) {
                $check_utf8_encoding = mb_check_encoding($chaine, 'UTF-8');
            }
                // Cas notices USMARC et UNIMARC encodees en UTF8
            if (is_object($this) && $check_utf8_encoding) {

                if (class_exists("Normalizer") && ($tmp_chaine = Normalizer::normalize($chaine))) { // php-intl
                    $chaine = $tmp_chaine; // Dans le cas du SUDOC les notices sont en utf-8 sur 3 octets pour les accents, les conversions ne fonctionnent donc pas
                    $chaine = str_replace(array(
                        chr(0xC2) . chr(0x98),
                        chr(0xC2) . chr(0x9C)
                    ), "", $chaine); // Caractères "Début du non-classement" et "Fin du non-classement" supprimés
                }
                if ($charset !== 'utf-8') {
                    $chaine = encoding_normalize::utf8_decode($chaine);
                }
                return $chaine;
            }

            $chaine = static::ISO_646_5426_decode($chaine);
            if ($charset == 'utf-8') {
                $chaine = encoding_normalize::utf8_encode($chaine);
            }
            return $chaine;
        }

        public function ISO_encode($chaine)
        {
            if (! $chaine)
                return $chaine;

            if (is_object($this) && ($this->is_utf8 === true)) {
                return $chaine;
            }

            if (is_object($this)) {
                $chaine = $this->ISO_646_5426_encode($chaine);
            } else {
                $chaine = iso2709_record::ISO_646_5426_encode($chaine);
            }
            return $chaine;
        }
    }

}
