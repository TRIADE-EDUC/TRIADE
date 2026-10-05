<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HTML_2_PDF.php,v 1.1.6.2 2024/07/03 13:41:16 qvarin Exp $

namespace Pmb\Common\Library\HTML2PDF;

use ReflectionClass;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;

class HTML_2_PDF extends Html2Pdf
{
    /**
     * Liste des tags autorise
     *
     * @var null|string[]
     */
    protected static $allowed_tags = null;

    /**
     * Recupere tous les tags autorises
     *
     * @return string[]
     */
    protected function fetchAllowedTags()
    {
        if (isset(static::$allowed_tags)) {
            return static::$allowed_tags;
        }

        // Recuperation des tags geres par les extensions
        static::$allowed_tags = array_map('strtolower', $this->getExtensionTags());

        // Ajout des tags utilises pour la CSS
        // Voir \Spipu\Html2Pdf\Html2Pdf::extractStyle
        static::$allowed_tags[] = 'link';
        static::$allowed_tags[] = 'style';

        // Recuperation des tags geres par HTML2PDF
        $reflection = new ReflectionClass($this);
        foreach ($reflection->getMethods() as $method) {
            $tagname = null;
            if (substr($method->name, 0, 10) == '_tag_open_') {
                $tagname = substr($method->name, 10);
            } elseif (substr($method->name, 0, 11) == '_tag_close_') {
                $tagname = substr($method->name, 11);
            } else {
                continue;
            }

            if (isset($tagname)) {
                static::$allowed_tags[] = strtolower($tagname);
            }
        }

        static::$allowed_tags = array_unique(static::$allowed_tags);
        return static::$allowed_tags;
    }

    /**
     * Retourne tous les tags de l'extension
     *
     * @return string[]
     */
    protected function getExtensionTags()
    {
        if (!$this->extensionsLoaded) {
            $this->loadExtensions();
        }

        return array_keys($this->tagObjects);
    }

    /**
     * Nettoie le HTML en supprimant les balises non autorisees
     *
     * @param string $html
     * @return string
     */
    protected function cleanHTML(string $html): string
    {
        $allowed_tags = $this->fetchAllowedTags();
        $allowed_tags = array_map(function ($tag) {
            return "<{$tag}>";
        }, $allowed_tags);

        return strip_tags(
            $html,
            implode('', $allowed_tags)
        );
    }

    /**
     * convert HTML to PDF
     *
     * @see \Spipu\Html2Pdf\Html2Pdf::writeHTML
     * @param string $html
     * @return HTML_2_PDF
     */
    public function writeHTML($html)
    {
        if (!is_string($html)) {
            throw new Html2PdfException('html must be a string');
        }

        $html = $this->cleanHTML($html);
        return parent::writeHTML($html);
    }

}