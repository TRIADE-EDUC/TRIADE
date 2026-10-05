<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TemplateInterface.php,v 1.2 2023/05/31 07:35:16 qvarin Exp $

namespace Pmb\DSI\Models\View\RssView\EntitiesTemplates;

interface TemplateInterface
{
    /**
     * Permet de retourner le titre de l'entite en fonction d'un template
     *
     * @param string|integer|null $tplTitle
     * @return string
     */
    public function getTitle($tplTitle);

    /**
     * Permet de retourner le permalien de l'entite
     *
     * @param string|integer|null $tplLink
     * @return string
     */
    public function getLink($tplLink);

    /**
     * Permet de retourner la date de publication de l'entite
     *
     * @return string
     */
    public function getPubDate();

    /**
     * Permet de retourner la description de l'entite en fonction d'un template
     *
     * @param string|integer|null $tplDescription
     * @return string
     */
    public function getDescription($tplDescription);

    /**
     * Retourne la liste des templates pour l'entité
     *
     * @return array
     */
    public static function getTemplates();
}