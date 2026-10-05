<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationPdfModel.php,v 1.4.2.1.2.1 2025/04/04 07:57:32 jparis Exp $

namespace Pmb\Animations\Models;

use Pmb\Animations\Orm\RegistrationOrm;
use Pmb\Common\Helper\HTML;

class AnimationPdfModel
{
    public static function renderRegistrationList(int $id)
    {
        global $include_path;

        $template = "";
        $template_path = $include_path . '/templates/animations/printRegistrationList.tpl.html';

        if (file_exists($include_path . '/templates/animations/printRegistrationList_subst.tpl.html')) {
            $template_path = $include_path . '/templates/animations/printRegistrationList_subst.tpl.html';
        }

        if (file_exists($template_path)) {

            $animation = new AnimationModel($id);
            $animation->getFetchAnimation();

            $allQuotas = AnimationModel::getAllQuotas($animation->idAnimation);

            $registrationListOrm = RegistrationOrm::find("num_animation", $animation->idAnimation);
            $registrationList = [];
            foreach ($registrationListOrm as $registrationOrm) {
                $registrationModel = new RegistrationModel($registrationOrm->id_registration);
                $registrationModel->fetchRegistrationListPerson();
                $registrationList[] = $registrationModel;
            }

            $h2o = \H2o_collection::get_instance($template_path);
            $template = $h2o->render([
                'animation' => $animation,
                'registrationList' => $registrationList,
                'registrationStatus' => RegistrationStatusModel::getRegistrationStatuses(),
                'allQuotas' => $allQuotas,
                'summaryPrice' => $animation->getSummaryPerson(),
            ]);
        }

        return [
            "template" => static::formatHTML($template),
            "title" => $animation->name . " " . $animation->event->startDate,
            "fileName" => strtolower(preg_replace("/\W/", "_", $animation->name . "_" . $animation->event->startDate)) . ".pdf",
        ];
    }

    /**
     * Formate le HTML et verifier si on peut mettre la balise <barcode>
     *
     * @param string $html
     * @return string
     */
    private static function formatHTML(string $html)
    {
        global $charset;

        if ($charset == "utf-8") {
            $html = "<?xml version='1.0' encoding='$charset'>" . $html;
        }

        $dom = new \DOMDocument('1.0', $charset);
        if (@$dom->loadHTML(HTML::cleanHTML($html, $charset))) {
            $removeNodeList = [];
            foreach ($dom->getElementsByTagName('barcode') as $domElement) {
                $domAttr = $domElement->getAttributeNode('value');
                if (static::isValidCodebar($domAttr->textContent)) {
                    continue;
                }

                $p = $dom->createElement('p', $domAttr->textContent);
                $domElement->parentNode->insertBefore($p, $domElement);
                $removeNodeList[] = $domElement;
            }

            foreach ($removeNodeList as $domElement) {
                $domElement->parentNode->removeChild($domElement);
            }

            $html = $dom->saveHTML();
        }
        return $html;
    }

    /**
     * Verifie si une chaine contient uniquement des caracteres ASCII (0-127).
     *
     * @param string $code Chaine a verifier.
     * @return bool True si compatible avec l'ASCII standard, False sinon.
     */
    private static function isValidCodebar(string $code)
    {
        $codeLen = strlen($code);
        for ($i = 0; $i < $codeLen; $i++) {
            if (ord($code[$i]) > 127) {
                // Caractère hors de la plage ASCII
                return false;
            }
        }
        return true;
    }

}
