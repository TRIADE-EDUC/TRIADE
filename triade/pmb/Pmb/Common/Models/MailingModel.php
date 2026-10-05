<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailingModel.php,v 1.9 2023/02/20 14:07:00 qvarin Exp $
namespace Pmb\Common\Models;

class MailingModel extends Model
{

    /**
     * Permet de remplacer les motifs dans les templates
     *
     * @param object $empr
     * @param string $template
     *
     * @return string $template
     */
    public static function getReplacePattern(object $empr, string $template)
    {
        global $msg;
        global $opac_connexion_phrase, $opac_url_base;

        switch ($empr->emprSexe ?? "") {
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

        $empr_auth_opac = "<a href='".$opac_url_base."empr.php?code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac"]."</a>";
        $empr_auth_opac_subscribe_link = "<a href='".$opac_url_base."empr.php?lvl=renewal&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac_subscribe_link"]."</a>";
        $empr_auth_opac_change_password_link = "<a href='".$opac_url_base."empr.php?lvl=change_password&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac_change_password_link"]."</a>";


        $locName = '';
        $locAdr1 = '';
        $locAdr2 = '';
        $locCp = '';
        $locTown = '';
        $locPhone = '';
        $locEmail = '';
        $locWebsite = '';

        if (!empty($empr->emprLocation)) {
            $empr->emprLocation = intval($empr->emprLocation);
            $empr_dest_loc = pmb_mysql_query("SELECT * FROM docs_location WHERE idlocation=" . $empr->emprLocation);
            if (pmb_mysql_num_rows($empr_dest_loc)) {
                $empr_loc = pmb_mysql_fetch_object($empr_dest_loc);
                $locName = $empr_loc->name;
                $locAdr1 = $empr_loc->adr1;
                $locAdr2 = $empr_loc->adr2;
                $locCp = $empr_loc->cp;
                $locPhone = $empr_loc->town;
                $locEmail = $empr_loc->phone;
                $locEmail = $empr_loc->email;
                $locWebsite = $empr_loc->website;
            }
        }

        $empr->emprNom = ! empty($empr->emprNom) ? $empr->emprNom : "";
        $empr->emprPrenom = ! empty($empr->emprPrenom) ? $empr->emprPrenom : "";
        $empr->emprCb = ! empty($empr->emprCb) ? $empr->emprCb : "";
        $empr->emprLogin = ! empty($empr->emprLogin) ? $empr->emprLogin : "";
        $empr->emprMail = ! empty($empr->emprMail) ? $empr->emprMail : "";
        $empr->affEmprDateAdhesion = ! empty($empr->affEmprDateAdhesion) ? $empr->affEmprDateAdhesion : "";
        $empr->affEmprDateExpiration = ! empty($empr->affEmprDateExpiration) ? $empr->affEmprDateExpiration : "";
        $empr->nbDaysBeforeExpiration = ! empty($empr->nbDaysBeforeExpiration) ? $empr->nbDaysBeforeExpiration : "";
        $empr->affEmprDayDate = ! empty($empr->affEmprDayDate) ? $empr->affEmprDayDate : "";
        $empr->emprLogin = ! empty($empr->emprLogin) ? $empr->emprLogin : "";
        $empr->affLastLoanDate = ! empty($empr->affLastLoanDate) ? $empr->affLastLoanDate : "";
        $empr->id_empr= ! empty($empr->id_empr) ? $empr->id_empr : 0;

        $search = array(
            "!!empr_name!!",
            "!!empr_first_name!!",
            "!!empr_cb!!",
            "!!empr_login!!",
            "!!empr_mail!!",
            "!!empr_dated!!",
            "!!empr_datef!!",
            "!!empr_nb_days_before_expiration!!",
            "!!empr_day_date!!",
            "!!login!!",
            "!!empr_last_loan_date!!",
            "!!empr_sexe!!",
            "!!empr_auth_opac!!",
            "!!empr_auth_opac_subscribe_link!!",
            "!!empr_auth_opac_change_password_link!!",
            "!!empr_loc_name!!",
            "!!empr_loc_adr1!!",
            "!!empr_loc_adr2!!",
            "!!empr_loc_cp!!",
            "!!empr_loc_town!!",
            "!!empr_loc_phone!!",
            "!!empr_loc_email!!",
            "!!empr_loc_website!!",
            "!!code!!",
            "!!date_conex!!"
        );
        $replace = array(
            $empr->emprNom,
            $empr->emprPrenom,
            $empr->emprCb,
            $empr->emprLogin,
            $empr->emprMail,
            $empr->affEmprDateAdhesion,
            $empr->affEmprDateExpiration,
            $empr->nbDaysBeforeExpiration,
            $empr->affEmprDayDate,
            $empr->emprLogin,
            $empr->affLastLoanDate,
            $emprCivilite,
            $empr_auth_opac,
            $empr_auth_opac_subscribe_link,
            $empr_auth_opac_change_password_link,
            $locName,
            $locAdr1,
            $locAdr2,
            $locCp,
            $locTown,
            $locPhone,
            $locEmail,
            $locWebsite,
            md5($opac_connexion_phrase . $empr->emprLogin . $date),
            $date
        );

        $emprunteurDatas = new \emprunteur_datas($empr->id_empr);
        if (strpos($template, "!!empr_loans!!")) {
            $search[] = "!!empr_loans!!";
            $replace[] = ! empty($emprunteurDatas->m_liste_prets()) ? $emprunteurDatas->m_liste_prets() : "";
        }
        if (strpos($template, "!!empr_loans_late!!")) {
            $search[] = "!!empr_loans_late!!";
            $replace[] = ! empty($emprunteurDatas->m_liste_prets(true)) ? $emprunteurDatas->m_liste_prets(true) : "";
        }
        if (strpos($template, "!!empr_resas!!")) {
            $search[] = "!!empr_resas!!";
            $replace[] = ! empty($emprunteurDatas->m_liste_resas()) ? $emprunteurDatas->m_liste_resas() : "";
        }
        if (strpos($template, "!!empr_resa_confirme!!")) {
            $search[] = "!!empr_resa_confirme!!";
            $replace[] = ! empty($emprunteurDatas->m_liste_resas_confirme()) ? $emprunteurDatas->m_liste_resas_confirme() : "";
        }
        if (strpos($template, "!!empr_resa_not_confirme!!")) {
            $search[] = "!!empr_resa_not_confirme!!";
            $replace[] = ! empty($emprunteurDatas->m_liste_resas_not_confirme()) ? $emprunteurDatas->m_liste_resas_not_confirme() : "";
        }
        if (strpos($template, "!!empr_name_and_adress!!")) {
            $search[] = "!!empr_name_and_adress!!";
            $replace[] = ! empty($emprunteurDatas->m_lecteur_adresse()) ? $emprunteurDatas->m_lecteur_adresse() : "";
        }
        if (strpos($template, "!!empr_all_information!!")) {
            $search[] = "!!empr_all_information!!";
            $replace[] = ! empty($emprunteurDatas->m_lecteur_info()) ? $emprunteurDatas->m_lecteur_info() : "";
        }

        $template = str_replace($search, $replace, $template);

        return $template;
    }
}