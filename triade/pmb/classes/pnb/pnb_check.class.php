<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_check.class.php,v 1.8 2023/08/28 14:01:12 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $base_path, $include_path, $class_path;
global $msg, $charset, $action, $offer_files;
global $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password, $pmb_pnb_param_ftp_server;

require_once "$include_path/templates/pnb/pnb_check.tpl.php";
require_once "$class_path/log.class.php";

class pnb_check
{

    const LOG_MAX_LEVELS = 10;

    const LOG_FILENAME = "pnb_check.txt";

    protected static $last_level = 0;

    protected static $template = [];

    protected static $error = false;

    protected static $offer_files = [];

    protected static $offers = [];

    protected static $offer_notices = [];

    protected static $report = [];

    protected static $report_nb_errors = 0;

    protected static $report_nb_warnings = 0;

    protected static $orderlines_to_delete = [];

    protected static $orderlines_to_add = [];

    protected static $orderlines_to_check = [];

    private function __construct()
    {}

    /**
     * Controleur
     */
    public static function proceed()
    {
        global $msg, $charset, $base_path, $action, $offer_files;

        settype($action, 'string');
        settype($offer_files, 'array');

        static::get_templates();

        switch ($action) {

            case '':
            default:
                $tpl = static::$template['default'];
                $tpl = str_replace('<!-- get_offer_files_description -->', static::$template['get_offer_files_description'], $tpl);
                $tpl = str_replace('!!action!!', 'get_offer_files', $tpl);

                if (is_readable($base_path . "/temp/" . static::LOG_FILENAME)) {
                    $tpl = str_replace('<!-- last_report -->', static::$template['last_report'], $tpl);
                }
                echo $tpl;
                break;

            case 'get_offer_files':

                // On limite a 10mn le tps de recuperation des fichiers
                set_time_limit(600);
                static::get_offer_files();

                $tpl = static::$template['default'];
                $tpl = str_replace('<!-- get_offer_files_description -->', static::$template['get_offer_files_description'], $tpl);

                // Il y a eu une erreur
                if (true === static::$error) {
                    $tpl = str_replace('<!-- get_offer_files_result -->', static::$template['error'], $tpl);
                    $tpl = str_replace('<!-- error -->', $msg['admin_pnb_check_get_offer_files_error_ftp'], $tpl);
                    $tpl = str_replace('!!action!!', '', $tpl);
                    echo $tpl;
                    break;
                }

                // Il n'y a pas de fichiers
                if (empty(static::$offer_files)) {

                    $tpl = str_replace('<!-- get_offer_files_result -->', static::$template['error'], $tpl);
                    $tpl = str_replace('<!-- error -->', $msg['admin_pnb_check_get_offer_files_error_no_file'], $tpl);
                    $tpl = str_replace('!!action!!', '', $tpl);
                    echo $tpl;
                    break;
                }

                $tpl = str_replace('<!-- get_offer_files_result -->', static::$template['offer_files_list'], $tpl);
                foreach (static::$offer_files as $onix_file) {
                    $tpl = str_replace('<!-- offer_files_list -->', static::$template['offer_files_item'] . '<!-- offer_files_list -->', $tpl);
                    $tpl = str_replace('<!-- offer_files_item -->', htmlentities($onix_file, ENT_QUOTES, $charset), $tpl);
                    $tpl = str_replace('!!offer_file!!', rawurlencode($onix_file), $tpl);
                }
                $tpl = str_replace('!!action!!', 'check_offer_files', $tpl);

                echo $tpl;
                break;

            case 'check_offer_files':

                // On limite a 10mn le tps de generation du rapport
                set_time_limit(600);
                if (isset($offer_files)) {
                    static::$offer_files = $offer_files;
                }

                static::switch_db_encoding_to_utf8();

                static::read_files();

                static::check_offers();

                static::generate_log();

                static::restore_db_encoding();

                $report = log::$log_msg;
                log::print_log();

                $report = encoding_normalize::charset_normalize($report, 'utf-8');
                $report = htmlentities($report, ENT_QUOTES, $charset);
                $report = nl2br($report);

                $tpl = static::$template['report'];
                $tpl = str_replace('<!-- report -->', $report, $tpl);
                echo $tpl;

                break;

            case 'view_last_report':

                if (is_readable($base_path . "/temp/" . static::LOG_FILENAME)) {

                    $report = file_get_contents($base_path . "/temp/" . static::LOG_FILENAME);
                    $report = encoding_normalize::charset_normalize($report, 'utf-8');
                    $report = htmlentities($report, ENT_QUOTES, $charset);
                    $report = nl2br($report);

                    $tpl = static::$template['report'];
                    $tpl = str_replace('<!-- report -->', $report, $tpl);
                    echo $tpl;
                }
                break;
        }
    }

    /**
     * Recuperation des templates
     *
     * @return void
     */
    protected static function get_templates()
    {
        global $pnb_check_form;
        static::$template = $pnb_check_form;
        return;
    }

    /**
     * Lecture du dernier fichier d'offres "full" et des fichiers d'offre "diff" suivants sur le FTP Dilicom
     *
     * @return bool
     */
    protected static function get_offer_files()
    {
        global $base_path;
        global $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password, $pmb_pnb_param_ftp_server;

        // Connexion ftp pour récupérer le nom du fichier à récupérer
        if (empty($pmb_pnb_param_ftp_server)) {
            return false;
        }
        $conn_id = ftp_connect($pmb_pnb_param_ftp_server);
        if (false === $conn_id) {
            static::$error = true;
            return false;
        }

        // Identification avec un nom d'utilisateur et un mot de passe
        if (! ftp_login($conn_id, $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password)) {
            static::$error = true;
            ftp_close($conn_id);
            return false;
        }
        ftp_pasv($conn_id, true);
        ftp_chdir($conn_id, '/HUB/O/');

        $full_file_list = ftp_nlist($conn_id, 'full_pnb*');
        if (false === $full_file_list) {
            static::$error = true;
            ftp_close($conn_id);
            return false;
        }
        $last_full_file = '';
        $last_full_file_date = '';
        if (is_array($full_file_list) && count($full_file_list)) {
            rsort($full_file_list);
            $last_full_file = $full_file_list[0];
            $last_full_file_date = substr(explode('_', $last_full_file)[3], 0, 8);
        }

        if ('' === $last_full_file) {
            static::$error = true;
            return false;
        }
        $files_to_process[] = $last_full_file;

        $diff_file_list = ftp_nlist($conn_id, 'diffusion_pnb*');
        if (false === $diff_file_list) {
            $diff_file_list = [];
        }
        if (is_array($diff_file_list) && count($diff_file_list)) {
            sort($diff_file_list);
            foreach ($diff_file_list as $diff_file) {
                $diff_file_date = substr(explode('_', $diff_file)[3], 0, 8);
                if ($diff_file_date > $last_full_file_date) {
                    $files_to_process[] = $diff_file;
                }
            }
        }

        static::$offer_files = [];
        foreach ($files_to_process as $file_name) {

            $fg = ftp_get($conn_id, $base_path . '/temp/' . $file_name, $file_name, FTP_BINARY);
            if ($fg) {
                static::$offer_files[] = $file_name;
                // @unlink($base_path . '/temp/' . $file_name);
            }
        }
        ftp_close($conn_id);
        return true;
    }

    /**
     * Conversion de durees en jours
     *
     * @param string $value
     * @param string $unit
     *
     * @return string
     */
    public static function convert_duration_in_days($value, $unit)
    {
        switch ($unit) {
            case 'HOUR':
                return ceil($value / 24);
                break;
            case 'DAY':
                return $value;
                break;
            case 'MONTH':
                return $value * 30;
                break;
            case 'YEAR':
                return $value * 365;
                break;
            default:
                return $value;
                break;
        }
    }

    /**
     * Lecture des fichiers d'offres
     *
     * @return void
     *
     */
    protected static function read_files()
    {
        global $msg, $base_path;

        static::$report[] = [
            'step' => 'READING_FILES',
            'level' => 1,
            'desc' => [
                'msg' => $msg['admin_pnb_check_reading_files'],
                'cr' => 'a'
            ]
        ];

        if (! is_array(static::$offer_files) || empty(static::$offer_files)) {

            static::$report[] = [
                'step' => 'READING_FILES',
                'error' => [
                    'type' => 'NO_FILES',
                    'msg' => $msg['admin_pnb_check_reading_file_error'],
                    'cr' => 'a'
                ]
            ];
            static::$nb_errors ++;
            return;
        }

        // Lecture des fichiers et conversion en tableaux d'offres
        $i = 0;

        foreach (static::$offer_files as $file) {

            static::$report[] = [
                'step' => 'READING_FILE',
                'level' => 2,
                'desc' => [
                    'msg' => $msg['admin_pnb_check_reading_file'],
                    'values' => [
                        $file
                    ],
                    'cr' => 'a'
                ]
            ];

            if (! is_readable($base_path . "/temp/" . $file)) {

                static::$report[] = [
                    'step' => 'READING_FILE',
                    'error' => [
                        'type' => 'MISSING_FILE',
                        'msg' => $msg['admin_pnb_check_reading_file_error'],
                        'values' => [
                            $file
                        ],
                        'cr' => 'a'
                    ]
                ];
                static::$report_nb_errors ++;
            } else {

                $file_content = json_decode(json_encode(simplexml_load_file($base_path . "/temp/" . $file, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT)), TRUE);
                static::$offers[$i] = [
                    'file' => $file,
                    'content' => $file_content
                ];
                $i ++;
            }

            @unlink($base_path . "/temp/" . $file);
        }

        return;
    }

    /**
     * Verification des offres
     *
     * @return void
     *
     */
    protected static function check_offers()
    {
        global $msg;

        static::$report[] = [
            'step' => 'CHECKING_OFFER_FILES',
            'level' => 1,
            'desc' => [
                'msg' => $msg['admin_pnb_check_checking_offer_files'],
                'cr' => 'a'
            ]
        ];
        foreach (static::$offers as $k_offers => $offers) {

            $file = $offers['file'];
            $content = $offers['content'];

            static::$report[] = [
                'step' => 'CHECKING_OFFER_FILE',
                'level' => 2,
                'desc' => [
                    'msg' => $msg['admin_pnb_check_checking_offer_file'],
                    'values' => [
                        $file
                    ],
                    'cr' => 'a'
                ]
            ];

            // Si pas de contenu, on passe à la suite
            if (empty($content) || ! is_array($content['offer']) || empty($content['offer'])) {
                static::$report[] = [
                    'step' => 'CHECKING_OFFER_FILE',
                    'error' => [
                        'type' => 'NO_CONTENT',
                        'msg' => $msg['admin_pnb_check_no_content_error'],
                        'values' => $file,
                        'cr' => 'a'
                    ]
                ];
                static::$report_nb_errors ++;
                continue;
            }

            // Nb d'offres
            $nb_offers = count($content['offer']);
            static::$report[] = [
                'step' => 'COUNTING_OFFERS',
                'desc' => [
                    'msg' => $msg['admin_pnb_check_counting_offers'],
                    'values' => [
                        $nb_offers
                    ],
                    'cr' => 'a'
                ]
            ];

            // On passe les offres en revue.
            foreach ($content['offer'] as $k_offer => $offer) {
                static::check_offer($k_offers, $k_offer, $offer, $nb_offers);
            }
        }
    }

    /**
     * Verification d'une offre
     *
     * @param int $k_offers
     *            : indice du fichier d'offre
     * @param int $k_offer
     *            : indice de l'offre
     * @param array $offer
     *            : contenu de l'offre
     * @param int $nb_offers
     *            : nb d'offres dans le fichier
     *            
     * @return void
     *
     */
    protected static function check_offer($k_offers, $k_offer, $offer, $nb_offers)
    {
        global $msg;

        static::$report[] = [
            'step' => 'CHECKING_OFFER',
            'level' => 3,
            'desc' => [
                'msg' => $msg['admin_pnb_check_checking_offer'],
                'values' => [
                    $k_offer + 1,
                    $nb_offers
                ],
                'cr' => 'a'
            ]
        ];

        // Lignes de commande
        $orderlines = [];
        if (! empty($offer['orderLine'])) {
            if (! isset($offer['orderLine'][0])) {
                $orderlines[] = $offer['orderLine'];
            } else {
                $orderlines = $offer['orderLine'];
            }
        }
        // Si pas de lignes de commande, on passe a la suite
        if (! is_array($orderlines) || empty($orderlines)) {
            static::$report[] = [
                'step' => 'CHECKING_OFFER',
                'error' => [
                    'type' => 'OFFER_NO_ORDERLINES',
                    'msg' => $msg['admin_pnb_check_checking_offer_no_orderlines_error'],
                    'values' => [
                        $k_offer + 1
                    ],
                    'cr' => 'a'
                ]
            ];
            static::$report_nb_errors ++;
            return;
        }

        // Notice
        $notice = [];
        if (! empty($offer['notice'])) {
            $notice = json_decode(json_encode(simplexml_load_string($offer['notice'], "SimpleXMLElement", LIBXML_NOCDATA)), TRUE);
        }

        // Si pas de notice, on passe a la suite
        if (empty($notice)) {
            static::$report[] = [
                'step' => 'CHECKING_OFFER',
                'error' => [
                    'type' => 'OFFER_NO_NOTICE',
                    'msg' => $msg['admin_pnb_check_checking_offer_no_notice_error'],
                    'values' => [
                        $k_offer + 1
                    ],
                    'cr' => 'a'
                ]
            ];
            static::$report_nb_errors ++;
            return;
        }

        // On met a jour la notice transformee dans static::$offers
        static::$offers[$k_offers]['content']['offer'][$k_offer]['notice'] = $notice;

        // Extraction des donnees de notice
        static::parse_notice($k_offers, $k_offer, $notice);

        // Nombre de lignes de commande
        $nb_orderlines = count($orderlines);
        static::$report[] = [
            'step' => 'CHECKING_OFFER',
            'level' => 4,
            'desc' => [
                'msg' => $msg['admin_pnb_check_counting_orderlines'],
                'values' => [
                    $nb_orderlines
                ],
                'cr' => 'a'
            ]
        ];
        // Verification des lignes d'offres
        foreach ($orderlines as $k_orderline => $orderline) {
            static::check_orderline($k_offers, $k_offer, $k_orderline, $orderline, $nb_orderlines);
        }
    }

    /**
     * Extraction des donnees de la notice d'offre
     *
     * @param int $k_offers
     *            : indice du fichier d'offre
     * @param int $k_offer
     *            : indice de l'offre
     * @param array $notice
     *            : contenu de la notice
     *            
     *            return void
     */
    protected static function parse_notice($k_offers, $k_offer, $notice)
    {
        global $msg;

        // Notice d'offre
        static::$report[] = [
            'step' => 'CHECKING_NOTICE',
            'level' => 4,
            'desc' => [
                'msg' => $msg['admin_pnb_check_checking_notice'],
                'cr' => 'a'
            ]
        ];

        // Titre
        $title = $notice['Product']['DescriptiveDetail']['TitleDetail']['TitleElement']['TitleText'];
        static::$report[] = [
            'step' => 'CHECKING_NOTICE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_notice_title'],
                'values' => [
                    $title
                ]
            ]
        ];

        // Sous-titre
        $subtitle = '';
        if (! empty($notice['Product']['DescriptiveDetail']['TitleDetail']['TitleElement']['Subtitle'])) {
            $subtitle = $notice['Product']['DescriptiveDetail']['TitleDetail']['TitleElement']['Subtitle'];
            static::$report[] = [
                'step' => 'CHECKING_NOTICE',
                'desc' => [
                    'msg' => $msg['admin_pnb_check_notice_subtitle'],
                    'values' => [
                        $subtitle
                    ]
                ]
            ];
        }

        // EAN + ISBN
        $product_identifier_ean = '';
        $product_identifier_isbn = '';

        // formatage donnees
        if (! empty($notice['Product']['ProductIdentifier']['ProductIDType'])) {
            $tmp = $notice['Product']['ProductIdentifier'];
            unset($notice['Product']['ProductIdentifier']);
            $notice['Product']['ProductIdentifier'][0] = $tmp;
            unset($tmp);
        }

        if (! empty($notice['Product']['ProductIdentifier']) && is_array($notice['Product']['ProductIdentifier'])) {
            // Recherche EAN13 (GTIN13) normalement obligatoire => ProductIDType=03
            // ou a defaut ISBN13 => ProductIDType=15
            foreach ($notice['Product']['ProductIdentifier'] as $pi) {

                if (isset($pi['ProductIDType']) && isset($pi['IDValue'])) {
                    if ($pi['ProductIDType'] == '03') {
                        $product_identifier_ean = $pi['IDValue'];
                    }
                    if ($pi['ProductIDType'] == '15') {
                        $product_identifier_isbn = $pi['IDValue'];
                    }
                }
            }
        }

        $product_identifier = '';
        if ($product_identifier_ean) {
            $product_identifier = $product_identifier_ean;
        } elseif ($product_identifier_isbn) {
            $product_identifier = $product_identifier_isbn;
        }
        static::$report[] = [
            'step' => 'CHECKING_NOTICE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_notice_ean'],
                'values' => [
                    $product_identifier_ean
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_NOTICE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_notice_isbn'],
                'values' => [
                    $product_identifier_isbn
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_NOTICE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_notice_identifier'],
                'values' => [
                    $product_identifier
                ]
            ]
        ];

        if (empty($product_identifier)) {
            static::$report[] = [
                'step' => 'CHECKING_NOTICE',
                'error' => [
                    'type' => 'ERROR_CHECKING_NOTICE_NO_IDENTIFIER',
                    'msg' => $msg['admin_pnb_check_notice_identifier_error'],
                    'cr' => 'ba'
                ]
            ];
            static::$report_nb_errors ++;
            return;
        }

        // Recherche d'une notice ayant pour identifiant $product_identifier
        static::$report[] = [
            'step' => 'CHECKING_NOTICE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_search_db_notice'],
                'values' => [
                    $product_identifier
                ],
                'cr' => 'ba'
            ]
        ];

        $q = "select notice_id, tit1, tit4, serie_name, tnvol from notices left join series on tparent_id=serie_id where is_numeric=1 and replace(code, '-', '') ='" . addslashes($product_identifier) . "' ";
        $r = pmb_mysql_query($q);
        $nb = pmb_mysql_num_rows($r);

        $notice_id = 0;

        switch (true) {

            case (0 === $nb):

                static::$report[] = [
                    'step' => 'CHECKING_NOTICE',
                    'warning' => [
                        'type' => 'WARNING_NO_NOTICE_WITH_THIS_IDENTIFIER',
                        'msg' => $msg['admin_pnb_check_no_notice_with_this_identifier_error'],
                        'cr' => 'a',
                        'values' => [
                            $product_identifier
                        ]
                    ]
                ];
                static::$report_nb_warnings ++;
                break;

            case (1 == $nb):

                $row = pmb_mysql_fetch_assoc($r);
                $notice_id = $row['notice_id'];

                static::$report[] = [
                    'step' => 'CHECKING_NOTICE',
                    'desc' => [
                        'msg' => $msg['admin_pnb_check_db_notice_id'],
                        'values' => [
                            $notice_id
                        ]
                    ]
                ];
                $tit1 = $row['tit1'];
                $tit4 = $row['tit4'];
                $serie_name = $row['serie_name'];
                $tnvol = $row['tnvol'];

                if ($tit4 === '') {
                    $cr = 'a';
                }
                static::$report[] = [
                    'step' => 'CHECKING_NOTICE',
                    'desc' => [
                        'msg' => $msg['admin_pnb_check_db_notice_title'],
                        'cr' => $cr,
                        'values' => [
                            $tit1
                        ]
                    ]
                ];
                if ($tit4 !== '') {
                    static::$report[] = [
                        'step' => 'CHECKING_NOTICE',
                        'desc' => [
                            'msg' => $msg['admin_pnb_check_db_notice_subtitle'],
                            'cr' => 'a',
                            'values' => [
                                $tit4
                            ]
                        ]
                    ];
                }
                $notice_full_title = ($serie_name) ? $serie_name . ' ' : '';
                $notice_full_title .= ($tnvol) ? $tnvol . ' ' : '';
                $notice_full_title .= ($tit1) ? $tit1 : '';
                $notice_full_title .= ($tit4) ? ' ' . $tit4 : '';
                $notice_full_title = static::strip_empty_words($notice_full_title);
                $offer_full_title = $title;
                $offer_full_title .= ($subtitle) ? ' ' . $subtitle : '';
                $offer_full_title = static::strip_empty_words($offer_full_title);

                $notice_title = static::strip_empty_words($tit1);
                $offer_subtitle = static::strip_empty_words($subtitle);
                $offer_title = static::strip_empty_words($title);

                if (($notice_full_title != $offer_full_title) && ($notice_title != $offer_title) && ($notice_title != $offer_subtitle) && (false === strpos($offer_full_title, $notice_title))) {

                    static::$report[] = [
                        'step' => 'CHECKING_NOTICE',
                        'warning' => [
                            'type' => 'WARNING_TITLES_MISMATCH',
                            'msg' => $msg['admin_pnb_check_titles_mismatch_error'],
                            'cr' => 'a',
                            'values' => [
                                $product_identifier
                            ]
                        ]
                    ];
                    static::$report_nb_warnings ++;
                } elseif (($notice_full_title != $offer_full_title) && ($notice_title != $offer_title)) {
                    static::$report[] = [
                        'step' => 'CHECKING_NOTICE',
                        'warning' => [
                            'type' => 'WARNING_TITLES_MISMATCH',
                            'msg' => $msg['admin_pnb_check_titles_mismatch_warning'],
                            'cr' => 'a',
                            'values' => [
                                $product_identifier
                            ]
                        ]
                    ];
                    static::$report_nb_warnings ++;
                }

                break;

            case (1 < $nb):

                $notices = [];
                while ($row = pmb_mysql_fetch_assoc($r)) {
                    $notices[] = [
                        $msg['admin_pnb_check_log_notice_id'],
                        $row['notice_id']
                    ];
                    $notices[] = [
                        $msg['admin_pnb_check_log_notice_title'],
                        $row['tit1']
                    ];
                }
                static::$report[] = [
                    'step' => 'CHECKING_NOTICE',
                    'warning' => [
                        'type' => 'ERROR_NO_NOTICE_WITH_THIS_IDENTIFIER',
                        'msg' => $msg['admin_pnb_check_too_many_notices_with_this_identifier_error'],
                        'cr' => 'a',
                        'values' => [
                            $product_identifier
                        ],
                        'details' => $notices
                    ]
                ];
                static::$report_nb_warnings ++;
                break;

            default:

                static::$report[] = [
                    'step' => 'CHECKING_NOTICE',
                    'warning' => [
                        'type' => 'ERROR_SEARCH_IDENTIFIER_QUERY_HAS_FAILED',
                        'msg' => $msg['admin_pnb_check_search_identifier_query_has_failed'],
                        'cr' => 'a',
                        'values' => [
                            $product_identifier
                        ]
                    ]
                ];
                static::$report_nb_warnings ++;
                break;
        }

        static::$offer_notices[$k_offers][$k_offer] = [
            'title' => $title,
            'subtitle' => $subtitle,
            'ean' => $product_identifier_ean,
            'isbn' => $product_identifier_isbn,
            'product_identifier' => $product_identifier,
            'notice_id' => $notice_id
        ];
        return;
    }

    /**
     * Verification d'une ligne de commande
     *
     * @param int $k_offers
     *            : indice du fichier d'offre
     * @param int $k_offer
     *            : indice de l'offre
     * @param int $k_orderline
     *            : indice de la ligne de commande
     * @param array $orderline
     *            : contenu de la ligne de commande
     * @param int $nb_orderlines
     *            : nb de lignes de commande dans l'offre
     *            
     * @return void
     *
     */
    protected static function check_orderline($k_offers, $k_offer, $k_orderline, $orderline, $nb_orderlines)
    {
        global $msg;
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'level' => 4,
            'desc' => [
                'msg' => $msg['admin_pnb_check_checking_orderline'],
                'values' => [
                    $k_orderline + 1,
                    $nb_orderlines
                ],
                'cr' => 'a'
            ]
        ];

        $offer_validity_in_days = static::convert_duration_in_days($orderline['usage']['collRights']['offerValidity']['value'], $orderline['usage']['collRights']['offerValidity']['unit']);
        $max_loan_duration = static::convert_duration_in_days($orderline['usage']['loanTerms']['loanMaxDuration']['value'], $orderline['usage']['loanTerms']['loanMaxDuration']['unit']);
        $date_time_now = new DateTime();
        $date_now = $date_time_now->format('Ymd');
        $date_time_beg = new DateTime($orderline['orderDate']);
        $date_beg_aff = $date_time_beg->format('d/m/Y');
        $date_time_end = $date_time_beg->add(new DateInterval('P' . $offer_validity_in_days . 'D'));
        $date_end = $date_time_end->format('Ymd');
        $date_end_aff = $date_time_end->format('d/m/Y');
        $diff = ($date_end - $date_now);

        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_orderId'],
                'values' => [
                    $orderline['orderId']
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_orderIdColl'],
                'values' => [
                    $orderline['orderIdColl']
                ]
            ]
        ];

        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_orderLineId'],
                'values' => [
                    $orderline['orderLineId']
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_orderDate'],
                'values' => [
                    $date_beg_aff
                ]
            ]
        ];

        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_ean13'],
                'values' => [
                    $orderline['ean13']
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_quantity'],
                'values' => [
                    $orderline['quantity']
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_loanMaxDuration'],
                'values' => [
                    $max_loan_duration
                ]
            ]
        ];
        $nb_expl = (int) $orderline['usage']['loanTerms']['loanNbSimultaneousUsers'];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_loanNbSimultaneousUsers'],
                'values' => [
                    $nb_expl
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_consultNbSimultaneousUsersInSitu'],
                'values' => [
                    $orderline['usage']['loanTerms']['consultNbSimultaneousUsersInSitu']
                ]
            ]
        ];

        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_consultNbSimultaneousUsersExSitu'],
                'values' => [
                    $orderline['usage']['loanTerms']['consultNbSimultaneousUsersExSitu']
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_usage_printing'],
                'values' => [
                    $orderline['usage']['userRights']['printing']
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_usage_copyAndPaste'],
                'values' => [
                    $orderline['usage']['userRights']['copyAndPaste']
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_usage_nbAllowedDevices'],
                'values' => [
                    $orderline['usage']['userRights']['nbAllowedDevices']
                ]
            ]
        ];
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_offerValidity'],
                'values' => [
                    $offer_validity_in_days
                ]
            ]
        ];

        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_orderline_offerValidity_date'],
                'values' => [
                    $date_end_aff
                ],
                'cr' => 'a'
            ]
        ];

        if ($diff <= 0) {
            static::$report[] = [
                'step' => 'CHECKING_ORDERLINE',
                'desc' => [
                    'msg' => $msg['admin_pnb_check_ordeline_expired'],
                    'cr' => 'a'
                ]
            ];
        }

        // Verification de la presence de la ligne de commande en base
        static::$report[] = [
            'step' => 'CHECKING_ORDERLINE',
            'desc' => [
                'msg' => $msg['admin_pnb_check_search_db_orderline'],
                'cr' => 'a'
            ]
        ];

        $q1 = "select id_pnb_order from pnb_orders ";
        $q1 .= "where pnb_order_id_order='" . addslashes($orderline['orderId']) . "' and pnb_order_line_id='" . addslashes($orderline['orderLineId']) . "'";
        $r1 = pmb_mysql_query($q1);
        $n1 = pmb_mysql_num_rows($r1);

        $pnb_order_ids_tab_list = [];
        if ($n1 > 0) {
            while ($row1 = pmb_mysql_fetch_assoc($r1)) {
                $pnb_order_ids_tab_list[] = $row1['id_pnb_order'];
            }
        }
        $pnb_order_ids_str_list = implode(',', $pnb_order_ids_tab_list);

        // Verification de la presence d'une notice attachee a la ligne de commande en base
        if ($n1 > 0) {
            $q2 = "select distinct(pnb_order_num_notice) from pnb_orders ";
            $q2 .= "join notices on pnb_order_num_notice=notice_id ";
            $q2 .= "where pnb_order_id_order='" . addslashes($orderline['orderId']) . "' and pnb_order_line_id='" . addslashes($orderline['orderLineId']) . "' ";
            $q2 .= "and pnb_order_num_notice!=0";
            $r2 = pmb_mysql_query($q2);
            $n2 = pmb_mysql_num_rows($r2);

            $notice_ids_tab_list = [];
            if ($n2 > 0) {
                while ($row2 = pmb_mysql_fetch_assoc($r2)) {
                    $notice_ids_tab_list[] = $row2['pnb_order_num_notice'];
                }
            }
            $notice_ids_str_list = implode(',', $notice_ids_tab_list);
        }

        // Verification de la presence d'exemplaires attaches a la ligne de commande en base
        if ($n1 > 0) {
            $q3 = "select distinct(pnb_order_expl_num) from pnb_orders ";
            $q3 .= "join pnb_orders_expl on id_pnb_order=pnb_order_num ";
            $q3 .= "join exemplaires on pnb_order_expl_num=expl_id ";
            $q3 .= "where pnb_order_id_order='" . addslashes($orderline['orderId']) . "' and pnb_order_line_id='" . addslashes($orderline['orderLineId']) . "' ";
            $r3 = pmb_mysql_query($q3);
            $n3 = pmb_mysql_num_rows($r3);

            $expl_ids_tab_list = [];
            if ($n3 > 0) {
                while ($row3 = pmb_mysql_fetch_assoc($r3)) {
                    $expl_ids_tab_list[] = $row3['pnb_order_expl_num'];
                }
            }
            $expl_ids_str_list = implode(',', $expl_ids_tab_list);
        }

        switch (true) {

            // Si la validite est depassee, la ligne de commande ne doit plus exister en base
            case (($diff <= 0) && ($n1 > 0)):

                static::$orderlines_to_delete[] = [
                    'orderId' => $orderline['orderId'],
                    'orderLineId' => $orderline['orderLineId']
                ];
                static::$report[] = [
                    'step' => 'CHECKING_ORDERLINE',
                    'error' => [
                        'type' => 'ORDERLINE_IS_INVALID',
                        'msg' => $msg['admin_pnb_check_orderline_is_invalid_error'],
                        'values' => [
                            $orderline['orderLineId'],
                            $orderline['orderId'],
                            $n1
                        ],
                        'cr' => 'a'
                    ]
                ];
                static::$report_nb_errors ++;
                $cr = '';
                if (($n2 === 0) && ($n3 === 0)) {
                    $cr = 'a';
                }
                static::$report[] = [
                    'step' => 'CHECKING_ORDERLINE',
                    'error' => [
                        'type' => 'ORDERLINE_IS_INVALID',
                        'msg' => $msg['admin_pnb_check_orderline_pnb_order_ids_list'],
                        'values' => [
                            $pnb_order_ids_str_list
                        ],
                        'cr' => $cr
                    ]
                ];
                // Les notices concernees
                if ($n2 > 0) {
                    $cr = '';
                    if ($n3 === 0) {
                        $cr = 'a';
                    }
                    static::$report[] = [
                        'step' => 'CHECKING_ORDERLINE',
                        'error' => [
                            'type' => 'ORDERLINE_IS_INVALID',
                            'msg' => $msg['admin_pnb_check_orderline_notice_ids_list'],
                            'values' => [
                                $notice_ids_str_list
                            ],
                            'cr' => $cr
                        ]
                    ];
                }
                // Les exemplaires concernes
                if ($n3 > 0) {
                    static::$report[] = [
                        'step' => 'CHECKING_ORDERLINE',
                        'error' => [
                            'type' => 'ORDERLINE_IS_INVALID',
                            'msg' => $msg['admin_pnb_check_orderline_expl_ids_list'],
                            'values' => [
                                $expl_ids_str_list
                            ],
                            'cr' => 'a'
                        ]
                    ];
                }
                break;

            // Si la validite n'est pas depassee, la commande doit exister en base
            case (($diff > 0) && ($n1 < 1)):

                static::$orderlines_to_add[] = [
                    'orderId' => $orderline['orderId'],
                    'orderLineId' => $orderline['orderLineId']
                ];
                static::$report[] = [
                    'step' => 'CHECKING_ORDERLINE',
                    'error' => [
                        'type' => 'ORDERLINE_IS_MISSING',
                        'msg' => $msg['admin_pnb_check_orderline_is_missing_error'],
                        'values' => [
                            $orderline['orderLineId'],
                            $orderline['orderId']
                        ],
                        'cr' => 'a'
                    ]
                ];
                static::$report_nb_errors ++;
                break;

            // Si la validite n'est pas depassee, la commande doit etre unique en base
            case (($diff > 0) && ($n1 > 1)):

                static::$orderlines_to_check[] = [
                    'orderId' => $orderline['orderId'],
                    'orderLineId' => $orderline['orderLineId']
                ];
                static::$report[] = [
                    'step' => 'CHECKING_ORDERLINE',
                    'error' => [
                        'type' => 'ORDERLINE_IS_DUPLICATED',
                        'msg' => $msg['admin_pnb_check_orderline_is_duplicated_error'],
                        'values' => [
                            $orderline['orderLineId'],
                            $orderline['orderId'],
                            $n1
                        ],
                        'cr' => 'a'
                    ]
                ];
                static::$report_nb_errors ++;
                $cr = '';
                if (($n2 === 0) && ($n3 === 0)) {
                    $cr = 'a';
                }
                static::$report[] = [
                    'step' => 'CHECKING_ORDERLINE',
                    'error' => [
                        'type' => 'ORDERLINE_IS_DUPLICATED',
                        'msg' => $msg['admin_pnb_check_orderline_pnb_order_ids_list'],
                        'values' => [
                            $pnb_order_ids_str_list
                        ],
                        'cr' => $cr
                    ]
                ];

                // Les notices concernees
                if ($n2 > 0) {
                    $cr = '';
                    if ($n3 === 0) {
                        $cr = 'a';
                    }
                    static::$report[] = [
                        'step' => 'CHECKING_ORDERLINE',
                        'error' => [
                            'type' => 'ORDERLINE_IS_INVALID',
                            'msg' => $msg['admin_pnb_check_orderline_notice_ids_list'],
                            'values' => [
                                $notice_ids_str_list
                            ],
                            'cr' => $cr
                        ]
                    ];
                }

                // Les exemplaires concernes
                if ($n3 > 0) {
                    static::$report[] = [
                        'step' => 'CHECKING_ORDERLINE',
                        'error' => [
                            'type' => 'ORDERLINE_IS_INVALID',
                            'msg' => $msg['admin_pnb_check_orderline_expl_ids_list'],
                            'values' => [
                                $expl_ids_str_list
                            ],
                            'cr' => 'a'
                        ]
                    ];
                }
                break;

            // Sinon, c'est OK pour la commande
            case (($diff > 0) && ($n1 == 1)):
                static::$report[] = [
                    'step' => 'CHECKING_ORDERLINE',
                    'desc' => [
                        'msg' => $msg['admin_pnb_check_orderline_pnb_order_ids_list'],
                        'values' => [
                            $pnb_order_ids_str_list
                        ]
                    ]
                ];
                // Les notices concernees
                if ($n2 == 1) {
                    static::$report[] = [
                        'step' => 'CHECKING_ORDERLINE',
                        'desc' => [
                            'type' => 'ORDERLINE_IS_INVALID',
                            'msg' => $msg['admin_pnb_check_orderline_notice_ids_list'],
                            'values' => [
                                $notice_ids_str_list
                            ]
                        ]
                    ];
                } else {
                    static::$report[] = [
                        'step' => 'CHECKING_ORDERLINE',
                        'error' => [
                            'type' => 'ORDERLINE_NO_NOTICE_ERROR',
                            'msg' => $msg['admin_pnb_check_orderline_notice_ids_list'],
                            'values' => [
                                $notice_ids_str_list
                            ],
                            'cr' => 'ba'
                        ]
                    ];
                    static::$report_nb_errors ++;
                }

                // La notice associee correspond-t-elle a celle trouvee depuis l'offre ?
                if ($n2 == 1 && ($notice_ids_str_list != static::$offer_notices[$k_offers][$k_offer]['notice_id'])) {
                    static::$report[] = [
                        'step' => 'CHECKING_ORDERLINE',
                        'error' => [
                            'type' => 'ORDERLINE_NO_NOTICE_ERROR',
                            'msg' => $msg['admin_pnb_check_orderline_notice_dont_match_offer_notice'],
                            'values' => [
                                $notice_ids_str_list,
                                static::$offer_notices[$k_offers][$k_offer]['notice_id']
                            ],
                            'cr' => 'ba'
                        ]
                    ];
                    static::$report_nb_errors ++;
                }

                // Les exemplaires concernes
                if ($n3 == $nb_expl) {
                    static::$report[] = [
                        'step' => 'CHECKING_ORDERLINE',
                        'desc' => [
                            'msg' => $msg['admin_pnb_check_orderline_expl_ids_list'],
                            'values' => [
                                $expl_ids_str_list
                            ],
                            'cr' => 'a'
                        ]
                    ];
                } else {
                    static::$report[] = [
                        'step' => 'CHECKING_ORDERLINE',
                        'error' => [
                            'type' => 'ORDERLINE_NB_EXPL_ERROR',
                            'msg' => $msg['admin_pnb_check_orderline_expl_ids_list'],
                            'values' => [
                                $expl_ids_str_list
                            ],
                            'cr' => 'ba'
                        ]
                    ];
                    static::$report_nb_errors ++;
                }
                break;
        }
    }

    /**
     * Construction du rapport
     */
    protected static function generate_log()
    {
        static::initialize_log();

        static::generate_log_title();
        foreach (static::$report as $part) {
            static::generate_log_part($part);
        }
        static::generate_log_end();
    }

    protected static function generate_log_title()
    {
        global $msg;
        static::generate_log_level_separator(0);
        static::log($msg['admin_pnb_check_log_title'], [
            date($msg['1005'])
        ]);
        static::generate_log_level_separator(0);
    }

    protected static function generate_log_end()
    {
        global $msg;

        static::generate_log_level_separator(0);
        static::generate_log_level_separator(0);

        static::log($msg['admin_pnb_check_log_nb_errors'], [
            static::$report_nb_errors
        ]);
        static::log($msg['admin_pnb_check_log_nb_warnings'], [
            static::$report_nb_warnings
        ]);

        if (! empty(static::$orderlines_to_delete) || ! empty(static::$orderlines_to_add) || ! empty(static::$orderlines_to_check)) {
            static::generate_log_level_separator(0);
            if (! empty(static::$orderlines_to_delete)) {
                static::log($msg['admin_pnb_check_log_orderlines_to_delete']);
                foreach (static::$orderlines_to_delete as $ol) {
                    static::log($msg['admin_pnb_check_log_orderline_detail'], $ol);
                }
            }
            if (! empty(static::$orderlines_to_add)) {
                static::log($msg['admin_pnb_check_log_orderlines_to_add']);
                foreach (static::$orderlines_to_add as $ol) {
                    static::log($msg['admin_pnb_check_log_orderline_detail'], $ol);
                }
            }
            if (! empty(static::$orderlines_to_check)) {
                static::log($msg['admin_pnb_check_log_orderlines_to_add']);
                foreach (static::$orderlines_to_check as $ol) {
                    static::log($msg['admin_pnb_check_log_orderline_detail'], $ol);
                }
            }
        }
        static::generate_log_level_separator(0);
        static::log($msg['admin_pnb_check_log_end'], [
            date($msg['1005'])
        ]);
        static::generate_log_level_separator(0);
    }

    protected static function generate_log_part($part, $level = 0)
    {
        if (! is_array($part)) {
            return;
        }
        // file_put_contents(log::$log_file, print_r($part,true), FILE_APPEND);
        foreach ($part as $k_item => $item) {
            switch ($k_item) {

                case 'level':
                    static::generate_log_level_separator((int) $item);
                    break;

                case 'desc':
                case 'error':
                case 'warning':
                    if (empty($item['msg'])) {
                        $item['msg'] = '';
                    }
                    if (empty($item['values'])) {
                        $item['values'] = [];
                    }
                    if (empty($item['cr'])) {
                        $item['cr'] = '';
                    }
                    if (empty($item['details'])) {
                        $item['details'] = [];
                    }
                    static::log($item['msg'], $item['values'], $item['cr'], $item['details']);
                    break;
            }
        }
    }

    protected static function generate_log_level_separator(int $level)
    {
        global $msg;

        $nb = static::LOG_MAX_LEVELS - $level;
        if ($nb <= 0) {
            $nb = 1;
        }
        $sep = $msg['admin_pnb_check_log_level_separator'];
        $level_sep = str_repeat($sep, $nb);

        static::log($level_sep);
        return;
    }

    /**
     * Initialisation du fichier de log
     *
     * @return void
     */
    public static function initialize_log()
    {
        global $base_path;

        log::$log_file = $base_path . "/temp/" . static::LOG_FILENAME;
        log::$log_format = "txt";
        log::$log_now = false;
        log::reset();
        return;
    }

    /**
     * Ecriture dans le fichier de log
     *
     * @param string $message
     *            : message
     * @param array $replacements
     *            : elements en utf8 a inserer dans le message
     * @param string $cr
     *            : saut de ligne b=before / a=after / ba = before+after
     * @param array $details
     *            : tableau de tableaux [0=>titre 1=>valeur]
     *            
     * @return void
     */
    public static function log(string $message = '', array $replacements = [], string $cr = '', $details = [])
    {
        global $msg;
        $utf8_message = encoding_normalize::utf8_normalize($message);
        if (! empty($replacements)) {
            array_unshift($replacements, $utf8_message);
            $utf8_message = call_user_func_array('sprintf', $replacements);
        }
        switch ($cr) {
            case 'b':
                $utf8_message = PHP_EOL . $utf8_message;
                break;
            case 'a':
                $utf8_message .= PHP_EOL;
                break;
            case 'ab':
            case 'ba':
                $utf8_message = PHP_EOL . $utf8_message . PHP_EOL;
                break;
        }
        if (! empty($details) && is_array($details)) {
            $utf8_message .= PHP_EOL . encoding_normalize::utf8_normalize($msg['admin_pnb_check_log_details']) . PHP_EOL;
            foreach ($details as $v) {
                $utf8_message .= sprintf(encoding_normalize::utf8_normalize($v[0]), $v[1]) . PHP_EOL;
            }
        }
        log::print_message($utf8_message);
        return;
    }

    /**
     * Passage de l'encodage de la connexion à la BDD en utf-8
     *
     * @return void
     */
    protected static function switch_db_encoding_to_utf8()
    {
        global $charset;
        if ($charset != 'utf-8') {
            pmb_mysql_query('set names utf8');
        }
        return;
    }

    /**
     * Restauration de l'encodage de la connexion à la BDD
     *
     * @return void
     */
    protected static function restore_db_encoding()
    {
        global $charset;
        if ($charset != 'utf-8') {
            pmb_mysql_query('set names latin1');
        }
    }

    /**
     * Fonction de nettoyage de chaine pour comparaison
     *
     * @param string $string
     * @return string
     */
    protected static function strip_empty_words($string = '')
    {
        global $charset;
        $back_charset = $charset;
        if ($charset != 'utf-8') {
            $charset = 'utf-8';
        }
        $string = strip_empty_words($string);
        $charset = $back_charset;
        return $string;
    }
}