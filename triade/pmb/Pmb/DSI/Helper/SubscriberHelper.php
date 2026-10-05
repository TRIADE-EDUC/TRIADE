<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubscriberHelper.php,v 1.9.2.1.2.1 2025/03/04 16:26:11 dbellamy Exp $

namespace Pmb\DSI\Helper;

use marc_list_collection;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Helper\DsiDocument;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;

class SubscriberHelper
{
    public const PREFIX_PATTERN = "subscriber_";
    public const OLD_PREFIX_PATTERN = "empr_";

    public const PREFIX_H2O = "subscriber.";

    public const PATTERN = [
        "!!subscriber_name!!",
        "!!subscriber_first_name!!",
        "!!subscriber_sexe!!",
        "!!subscriber_mail!!",
        "!!subscriber_phone!!",
        "!!subscriber_login!!",
        "!!subscriber_auth_code!!",
        "!!subscriber_date_auth_code!!",
        "!!subscriber_auto_connection_link!!",
        "!!subscriber_unsubscribe_link!!"
    ];

    public const OLD_PATTERN = [
        "!!empr_name!!",
        "!!empr_first_name!!",
        "!!empr_sexe!!",
        "!!empr_cb!!",
        "!!empr_login!!",
        "!!empr_mail!!",
        "!!empr_name_and_adress!!",
        "!!empr_all_information!!",
        "!!empr_connect!!",
        "!!empr_statut_id!!",
        "!!empr_statut_lib!!",
        "!!empr_categ_id!!",
        "!!empr_categ_lib!!",
        "!!empr_codestat_id!!",
        "!!empr_codestat_lib!!",
        "!!empr_langopac_code!!",
        "!!empr_langopac_lib!!",
        "!!loc_name!!",
        "!!loc_adr1!!",
        "!!loc_adr2!!",
        "!!loc_cp!!",
        "!!loc_town!!",
        "!!loc_phone!!",
        "!!loc_email!!",
        "!!loc_website!!"
    ];

    public const HTTP_QUERY_AUTO_CONNEXION = [
        "code=!!subscriber_auth_code!!",
        "emprlogin=!!subscriber_login!!",
        "date_conex=!!subscriber_date_auth_code!!"
    ];

    /**
     * Permet de remplacer les motifs dans les templates
     *
     * @param string $template
     * @param Subscriber $subscriber
     * @return string $template
     */
    public static function replacePattern(string $template, Subscriber $subscriber, Diffusion $diffusion)
    {
        global $opac_connexion_phrase, $opac_url_base;
        global $msg, $dsi_connexion_auto;

        $replace = [
            'name' => '',
            'first_name' => '',
            'civilite' => '',
            'mail' => '',
            'phone' => '',
            'login' => '',
            'auth_code' => '',
            'date_auth_code' => '',
            'auto_connection_link' => '',
            'unsubscribe_link' => ''
        ];

        if (!empty($subscriber->getIdEmpr())) {
            $empr = new \emprunteur($subscriber->getIdEmpr());
            switch ($empr->emprSexe ?? 0) {
                case "2":
                    $emprCivilite = $msg["civilite_madame"];
                    break;
                case "1":
                    $emprCivilite = $msg["civilite_monsieur"];
                    break;
                default:
                    $emprCivilite = $msg["civilite_unknown"];
                    break;
            }

            $date = time();
            $authCode = md5($opac_connexion_phrase . $empr->login . $date);

            $name = \emprunteur::get_name($subscriber->getIdEmpr(), 1);
            $parsedName = explode(' ', $name);

            $replace['name'] = $name;
            $replace['first_name'] = $parsedName[0] ?? "";
            $replace['civilite'] = $emprCivilite;
            $replace['mail'] = $empr->mail;
            $replace['phone'] = $empr->tel1;
            $replace['login'] = $empr->login;
            $replace['auth_code'] = $authCode;
            $replace['date_auth_code'] = $date;

            $replace['unsubscribe_link'] = "<a href='" . $opac_url_base . "index.php?lvl=dsi&action=unsubscribe&id_diffusion=" . $diffusion->id . "&code=" . md5($opac_connexion_phrase . $subscriber->getIdEmpr() . $date) . "&emprlogin=" . $subscriber->getIdEmpr() . "&date_conex=" . $date . "&empr_type=pmb' class='dsi_unsubscribe_link'>" . $msg["bannette_tpl_unsubscribe"] . "</a>";
            if ($dsi_connexion_auto) {
                $replace['auto_connection_link'] = "
                <a href='{$opac_url_base}empr.php?code={$authCode}&emprlogin={$subscriber->getIdEmpr()}&date_conex={$date}'>
                {$msg["selvars_empr_auth_opac"]}
                </a>
                ";
            }

            $template = static::replaceOldPatterns($empr, $template);
        } else {
            $date = time();
            $authCode = md5($opac_connexion_phrase . $subscriber->getIdSubscriber() . $date);

            $replace['name'] = $subscriber->getName();
            $replace['civilite'] = $msg["civilite_unknown"];
            $replace['unsubscribe_link'] = "<a href='" . $opac_url_base . "index.php?lvl=dsi&action=unsubscribe&id_diffusion=" . $diffusion->id . "&code=" . $authCode . "&emprlogin=" . $subscriber->getIdSubscriber() . "&date_conex=" . $date . "&empr_type=other' class='dsi_unsubscribe_link'>" . $msg["bannette_tpl_unsubscribe"] . "</a>";
            $replace = array_merge($replace, Helper::toArray($subscriber->settings));
        }

        return str_replace(static::PATTERN, $replace, $template);
    }

    public static function format(string $template, Subscriber $subscriber, bool $stripTags = false, Diffusion $diffusion = null)
    {
        $template = static::parseDom($template, $stripTags);
        $template = static::replacePattern($template, $subscriber, $diffusion);
        return $stripTags ? strip_tags($template) : $template;
    }

    public static function parseDom(string $template, bool $stripTags = false)
    {
        $dsiDocument = new DsiDocument();
        $dsiDocument->loadHTML($template);
        $dsiDocument->formatHTML();

        return $stripTags ? trim($dsiDocument->textContent) : $dsiDocument->saveHTML();
    }

    public static function getPatternList()
    {
        global $msg;

        $patternList = [];
        foreach (static::PATTERN as $pattern) {
            $label = trim($pattern, "!");
            $label = $msg["dsi_{$label}"] ?? $label;
            $patternList[$pattern] = $label;
        }
        return $patternList;
    }

    public static function getH2oPatternList()
    {
        global $msg;

        $patternList = [];
        foreach (static::getPatternList() as $pattern => $label) {
            $pattern = trim($pattern, "!");
            $pattern = str_replace(
                static::PREFIX_PATTERN,
                static::PREFIX_H2O,
                $pattern
            );
            $patternList[$pattern] = $label;
        }
        return $patternList;
    }

    public static function getTree()
    {
        global $msg;

        $children = [];
        foreach (static::getH2oPatternList() as $pattern => $label) {
            $children[] = [
                'var' => $pattern,
                'desc' => $label,
            ];
        }

        return [
            [
                'var' => "subscriber",
                'desc' => $msg['tree_subscriber_desc'] ?? "subscriber",
                'children' => $children
            ]
        ];
    }

    public static function h2oLookup($name, $h2oContext)
    {
        $prefixName = ":" . static::PREFIX_H2O;
        if (strpos($name, $prefixName) === 0) {
            $pattern = str_replace($prefixName, "", $name);
            $pattern = "!!" . static::PREFIX_PATTERN . $pattern . "!!";

            if (in_array($pattern, static::PATTERN)) {
                return $pattern;
            }
            return "";
        }
        return null;
    }

    public static function get_empr_status()
    {
        $ac = new \acces();
        $dom = $ac->setDomain(2);
        $t_u = array();
        $t_u[] = [
            "value" => 0,
            "label" => $dom->getComment('user_prf_def_lib')
        ];
        $qu = $dom->loadUsedUserProfiles();
        $ru = pmb_mysql_query($qu);
        if (pmb_mysql_num_rows($ru)) {
            while (($row = pmb_mysql_fetch_object($ru))) {
                $t_u[] = [
                    "value" => $row->prf_id,
                    "label" => $row->prf_name
                ];
            }
        }
        return $t_u;
    }

    public static function get_empr_categ()
    {
        $result = array();

        $requete = "SELECT id_categ_empr, libelle FROM empr_categ ORDER BY libelle ";
        $res = pmb_mysql_query($requete);

        if (pmb_mysql_num_rows($res)) {
            while ($row = pmb_mysql_fetch_assoc($res)) {
                $result[] = [
                    "value" => $row["id_categ_empr"],
                    "label" => $row["libelle"]
                ];
            }
        }

        return $result;
    }

    public static function get_empr_groups()
    {
        $result = array();

        $requete = "SELECT id_groupe, libelle_groupe FROM groupe ORDER BY libelle_groupe";
        $res = pmb_mysql_query($requete);

        if (pmb_mysql_num_rows($res)) {
            while ($row = pmb_mysql_fetch_assoc($res)) {
                $result[] = [
                    "value" => $row["id_groupe"],
                    "label" => $row["libelle_groupe"]
                ];
            }
        }

        return $result;
    }

    public static function m_lecteur_adresse($empr)
    {
        global $msg;

        $res_final = array();

        if ($empr->prenom) $empr->nom = $empr->prenom . " " . $empr->nom;
        $res_final[] = $empr->nom;

        if ($empr->adr2 != "") $empr->adr1 = $empr->adr1 . "\n";
        if (($empr->cp != "") || ($empr->ville != "")) $empr->adr2 = $empr->adr2 . "\n";
        $adr = $empr->adr1 . $empr->adr2 . $empr->cp . " " . $empr->ville;
        if ($empr->pays != "") $adr = $adr . "\n" . $empr->pays;
        $res_final[] = $adr;
        if (!isset($tel)) $tel = "";
        if ($empr->tel1 != "") {
            $tel = $tel . $msg['fpdf_tel'] . " " . $empr->tel1 . " ";
        }
        if ($empr->tel2 != "") {
            $tel = $tel . $msg['fpdf_tel2'] . " " . $empr->tel2;
        }
        if ($empr->mail != "") {
            if ($tel) $tel = $tel . "\n";
            $mail = $msg['fpdf_email'] . " " . $empr->mail;
        }

        $res_final[] = "\n" . $tel . $mail;

        return implode("\n", $res_final);
    }


    public static function m_lecteur_info($empr)
    {
        global $msg;

        $res_final = array();
        $requete = "SELECT group_concat(libelle_groupe SEPARATOR ', ') as_all_groupes, 1 as rien from groupe join empr_groupe on groupe_id=id_groupe WHERE lettre_rappel_show_nomgroup=1 and empr_id='" . $empr->id . "' group by rien ";
        $lib_all_groupes = pmb_sql_value($requete);
        if ($lib_all_groupes) $lib_all_groupes = "\n" . $lib_all_groupes;

        if ($empr->prenom) $empr->nom = $empr->prenom . " " . $empr->nom;
        $res_final[] = $empr->nom;

        if ($empr->adr2 != "") $empr->adr1 = $empr->adr1 . "\n";
        if (($empr->cp != "") || ($empr->ville != "")) $empr->adr2 = $empr->adr2 . "\n";
        $adr = $empr->adr1 . $empr->adr2 . $empr->cp . " " . $empr->ville;
        if ($empr->pays != "") $adr = $adr . "\n" . $empr->pays;
        $res_final[] = $adr;
        if (!isset($tel)) $tel = "";
        if ($empr->tel1 != "") {
            $tel = $tel . $msg['fpdf_tel'] . " " . $empr->tel1 . " ";
        }
        if ($empr->tel2 != "") {
            $tel = $tel . $msg['fpdf_tel2'] . " " . $empr->tel2;
        }
        if ($empr->mail != "") {
            if ($tel) $tel = $tel . "\n";
            $mail = $msg['fpdf_email'] . " " . $empr->mail;
        }

        $res_final[] = "\n" . $tel . $mail . $lib_all_groupes;
        $res_final[] = "";
        $res_final[] = $msg['fpdf_carte'] . " " . $empr->cb;
        $res_final[] = $msg['fpdf_adherent'] . " " . $empr->aff_date_adhesion . " " . $msg['fpdf_adherent_au'] . " " . $empr->aff_date_expiration;

        return implode("\n", $res_final);
    }
    /**
     * Traitement des patterns de l'ancienne DSI
     */

    protected static function replaceOldPatterns($empr, $template)
    {
        $replace = array(
            'empr_name' => '',
            'empr_first_name' => '',
            'empr_sexe' => '',
            'empr_cb' => '',
            'empr_login' => '',
            'empr_mail' => '',
            'empr_name_and_adress' => '',
            'empr_all_information' => '',
            'empr_connect' => '',
            'empr_statut_id' => '',
            'empr_statut_lib' => '',
            'empr_categ_id' => '',
            'empr_categ_lib' => '',
            'empr_codestat_id' => '',
            'empr_codestat_lib' => '',
            'empr_langopac_code' => '',
            'empr_langopac_lib' => '',
            'loc_name' => '',
            'loc_adr1' => '',
            'loc_adr2' => '',
            'loc_cp' => '',
            'loc_town' => '',
            'loc_phone' => '',
            'loc_email' => '',
            'loc_website' => ''
        );


        $replace['empr_name'] = $empr->nom;
        $replace['empr_first_name'] = $empr->prenom;
        $replace['empr_sexe'] = $empr->sexe;
        $replace['empr_cb'] = $empr->cb;
        $replace['empr_login'] = $empr->login;
        $replace['empr_mail'] = $empr->mail;
        $replace['empr_name_and_adress'] = nl2br(static::m_lecteur_adresse($empr));
        $replace['empr_all_information'] = nl2br(static::m_lecteur_info($empr));
        $replace['empr_statut_id'] = $empr->empr_statut;
        $replace['empr_statut_lib'] = $empr->empr_statut_libelle;
        $replace['empr_categ_id'] = $empr->categ;
        $replace['empr_categ_lib'] = $empr->cat_l;
        $replace['empr_codestat_id'] = $empr->cstat;
        $replace['empr_codestat_lib'] = $empr->cstat_l;
        $replace['empr_langopac_code'] = $empr->empr_lang;
        $langues = marc_list_collection::get_instance('languages');
        $replace['empr_langopac_lib'] = $langues->table[$empr->empr_lang];

        if ($empr->empr_location) {
            $empr_dest_loc = pmb_mysql_query("SELECT * FROM docs_location WHERE idlocation=" . $empr->empr_location);
            $empr_loc = pmb_mysql_fetch_object($empr_dest_loc);
            $replace['loc_name'] = $empr_loc->name;
            $replace['loc_adr1'] = $empr_loc->adr1;
            $replace['loc_adr2'] = $empr_loc->adr2;
            $replace['loc_cp'] = $empr_loc->cp;
            $replace['loc_town'] = $empr_loc->town;
            $replace['loc_phone'] = $empr_loc->phone;
            $replace['loc_email'] = $empr_loc->email;
            $replace['loc_website'] = $empr_loc->website;
        }

        return str_replace(static::OLD_PATTERN, $replace, $template);
    }
}
