<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docnum_merge.class.php,v 1.11.2.1 2024/06/28 07:32:17 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use Pmb\Common\Library\PDF\PDFMerger;

global $class_path;
require_once $class_path . "/explnum.class.php";
require_once $class_path . "/acces.class.php";

class docnum_merge
{
    public $ids;		// MySQL id in table 'notice_tpl'
    public $id_notices;
    public $docnum_ids;

    /**
     * Constructeur
     *
     * @param integer $id_notices
     * @param integer $docnum_ids
     */
    public function __construct($id_notices=0, $docnum_ids=0)
    {
        $this->id_notices = $id_notices;
        $this->docnum_ids = $docnum_ids;
        $this->getData();
    }

    /**
     * Récupération infos
     *
     * @return void
     */
    public function getData()
    {
        # Do nothing
    }

    /**
     * Mérges les documents numériques et affiche le re?sultat
     *
     * @return void
     */
    public function merge()
    {
        global $gestion_acces_active, $gestion_acces_empr_notice;
        global $docnum_part_summary;

        $filename_list = array();
        $summary = array();

        if (is_array($this->docnum_ids) && count($this->docnum_ids)) {
            foreach ($this->docnum_ids as $explnum_id) {
                $explnum = new explnum($explnum_id);
                $id_for_rigths = $this->get_id_for_rigths($explnum);

                //droits d'acces emprunteur/notice
                $rights = 0;
                $dom_2 = null;
                if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
                    $ac= new acces();
                    $dom_2= $ac->setDomain(2);
                    $rights= $dom_2->getRights($_SESSION['id_empr_session'], $id_for_rigths);
                }

                //Accessibilité des documents numériques aux abonnés en opac
                $req_restriction_abo = "SELECT  explnum_visible_opac, explnum_visible_opac_abon FROM notice_statut, explnum, notices WHERE explnum_notice=notice_id AND statut=id_notice_statut  AND explnum_id='$explnum_id' ";
                $result=pmb_mysql_query($req_restriction_abo);
                if (! pmb_mysql_num_rows($result)) {
                    $req_restriction_abo="SELECT explnum_visible_opac, explnum_visible_opac_abon
                        FROM notice_statut, explnum, bulletins, notices
                        WHERE explnum_bulletin = bulletin_id
                        AND num_notice = notice_id
                        AND statut = id_notice_statut
                        AND explnum_id='$explnum_id' ";
                    $result=pmb_mysql_query($req_restriction_abo);
                }
                $expl_num=pmb_mysql_fetch_array($result);

                if ($rights & 16 || (null === $dom_2 && $expl_num["explnum_visible_opac"] && (!$expl_num["explnum_visible_opac_abon"] || ($expl_num["explnum_visible_opac_abon"] && $_SESSION["user_code"])))) {
                    $filename = $this->create_temp_file($explnum);
                    if ($filename) {
                        $filename_list[] = $filename;
                        if ($docnum_part_summary) {
                            $summary[$filename] = $explnum->explnum_nom;
                        }
                    }
                }
            }
        }

        $this->ouput($filename_list, $summary);
    }

    /**
     * Mérges les documents numériques et affiche le re?sultat
     *
     * @param string[] $filename_list
     * @param array $summary
     * @return void
     */
    private function ouput(array $filename_list, array $summary)
    {
        global $docnum_part_odd_even;

        $pdfMerger = new PDFMerger(isset($docnum_part_odd_even), $summary);
        if (!empty($filename_list)) {
            $pdfMerger->merge($filename_list);
            foreach ($filename_list as $filename) {
                $this->delete($filename);
            }
        }

        header('Content-type: application/pdf');
        echo $pdfMerger->output(
            './temp/doc_num_output' . session_id() . '.pdf',
        	PDFMerger::OUTPUT_DEST_S
        );
    }

    /**
     * Retourne l'id de la notice pour les droits d'acces
     *
     * @param Explnum $explnum
     * @return int
     */
    private function get_id_for_rigths($explnum)
    {
        $id_for_rigths = intval($explnum->explnum_notice);
        if ($explnum->explnum_bulletin != 0) {
            // Si bulletin, les droits sont rattachés à la notice du bulletin, à défaut du pério...
            $req = "SELECT bulletin_notice, num_notice FROM bulletins WHERE bulletin_id = " . intval($explnum->explnum_bulletin);
            $res = pmb_mysql_query($req);
            if (pmb_mysql_num_rows($res)) {
                $row = pmb_mysql_fetch_assoc($res);
                $id_for_rigths = intval($row['num_notice']);
                if (!$id_for_rigths) {
                    $id_for_rigths = intval($row['bulletin_notice']);
                }

                pmb_mysql_free_result($res);
            }
        }

        return $id_for_rigths;
    }

    /**
     * Creation d'un fichier temporaire
     *
     * @param Explnum $explnum
     * @return string|false
     */
    private function create_temp_file($explnum)
    {
        $content = $explnum->get_file_content();
        if (!$content) {
            return false;
        }

        // On crée le fichier original dans le dossier temporaire
        $origineFile = './temp/doc_num_' . md5($explnum->explnum_id . session_id()) . '.pdf';
        $fp = fopen($origineFile, "wb");
        fwrite($fp, $content);
        fclose($fp);

        // On supprime les metadata et la compression du pdf
        // Obligatoire pour utiliser PDFMerger. Si une compression est active, FPDI ne fonctionne pas
        $tmpfile = './temp/doc_num_' . $explnum->explnum_id . session_id() . '.pdf';
        exec("pdfunite $origineFile $tmpfile");

        // On supprime le fichier temporaire
        $this->delete($origineFile);

        return $tmpfile;
    }

    /**
     * Suppression d'un fichier
     *
     * @param string $filename
     * @return void
     */
    private function delete(string $filename)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}
