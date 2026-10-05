<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CMSFrameBuild.php,v 1.9 2023/11/09 11:45:20 dgoron Exp $

namespace Pmb\CMS\Library\Build;

class CMSFrameBuild extends FrameBuild
{
    private $cmsCadre = null;

    /**
     *
     * @return array
     */
    public function getHeaders()
    {
        if (! empty($this->getCMSCadre())) {
            $headerCache = $this->getHeaderCache();
            if (false === $headerCache) {
                $headerCache = $this->getCMSCadre()->get_headers();
                $this->addHeaderCache($headerCache);
            }
            return $headerCache;
        } else {
            return parent::getHeaders();
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \Pmb\CMS\Library\Build\FrameBuild::buildNode()
     */
    public function buildNode()
    {
        global $opac_parse_html, $charset;

        if (! empty($this->getCMSCadre())) {
            if (!$this->cmsCadre->check_conditions()) {
                return false;
            }

            $id = $this->getCMSCadre()->get_dom_id();
            $html = $this->getCache();
            if (false === $html) {
                $html = $this->getCMSCadre()->show_cadre();
                $this->addCache($html);
            }
        } else {
            $id = $this->layoutElement->getSemantic()->getIdTag();
            $html = $this->buildHTMLError("CMS Frame not found ({$id})");
        }

        if ($opac_parse_html) {
            $html = parseHTML($html);
        }

        if ($charset == "utf-8") {
            $html = "<?xml version='1.0' encoding='$charset'>" . $html;
        }

        $dom = new \domDocument();
        if (! @$dom->loadHTML($html)) {
            return false;
        }

        $elementNode =  $this->portalDocument->importNode($dom->getElementById($id), true);

        $semantic = $this->layoutElement->getSemantic();
        $newElementNode = $this->portalDocument->importNode($semantic->getNode(), true);
        $containerNode = $semantic->getContainerNode();

        if (! empty($containerNode) && $containerNode->getAttribute('id') != $newElementNode->getAttribute('id')) {
            // On importe le noeud container et on récupère tout les enfants du cadre CMS
            $containerNode = $this->portalDocument->importNode($containerNode, true);
            $this->portalDocument->switchParent($elementNode, $containerNode);
            $newElementNode->appendChild($containerNode);
            // Pour l'élément $semantic->getIdTag() on stock le noeud container
            $this->portalDocument->elementNodeContainer[$semantic->getIdTag()] = $containerNode;
        } else {
            $this->portalDocument->switchParent($elementNode, $newElementNode);
        }

        return $this->portalDocument->importNode($newElementNode, true);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Pmb\CMS\Library\Build\FrameBuild::checkConditions()
     */
    public function checkConditions(): bool
    {
        if (!$this->getCMSCadre()) {
            return false;
        }
        return $this->cmsCadre->check_conditions() == true;
    }

    /**
     *
     * @return \cms_module_root|null
     */
    private function getCMSCadre()
    {
        if (! isset($this->cmsCadre)) {
            $tagId = $this->layoutElement->getSemantic()->getIdTag();
            $idCadre = substr($tagId, strrpos($tagId, "_") + 1);
            $idCadre = intval($idCadre);

            $this->cmsCadre = \cms_modules_parser::get_module_class_by_id($idCadre);
        }
        return $this->cmsCadre;
    }

    /**
     *
     * @param string $error
     * @return string
     */
    private function buildHTMLError(string $error)
    {
        $html = '<!-- ' . $error . ' -->';
        $html .= '<div id="' . $this->layoutElement->getSemantic()->getIdTag() . '" class="error_on_template" title="' . htmlspecialchars($error, ENT_QUOTES) . '">';
        $html .= 'Error Build';
        $html .= '</div>';
        return $html;
    }

    private function addCache(string $render = "")
    {
        $hashCadre = $this->layoutElement::getHashCadre($this->layoutElement->getSemantic()->getIdTag());
        if (empty($hashCadre)) {
            return false;
        }
        $query = "INSERT INTO cms_cache_cadres (cache_cadre_hash, cache_cadre_type_content, cache_cadre_content) ";
        $query .= "VALUES ('".addslashes($hashCadre)."', 'html', '".addslashes($render)."')";
        $query .= "ON DUPLICATE KEY UPDATE cache_cadre_content='".addslashes($render)."'";

        if (pmb_mysql_query($query)) {
            return true;
        }
        return false;
    }

    private function getCache()
    {
        $hashCadre = $this->layoutElement::getHashCadre($this->layoutElement->getSemantic()->getIdTag());

        $query = "SELECT cache_cadre_content FROM cms_cache_cadres WHERE cache_cadre_type_content = 'html' ";
        $query .= "and cache_cadre_hash = '".addslashes($hashCadre)."'";
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            return pmb_mysql_result($result, 0);
        }
        return false;
    }
    
    private function addHeaderCache(array $render = [])
    {
        $hashCadre = $this->layoutElement::getHashCadre($this->layoutElement->getSemantic()->getIdTag());
        if (empty($hashCadre)) {
            return false;
        }
        $render = \encoding_normalize::json_encode($render);
        $query = "INSERT INTO cms_cache_cadres (cache_cadre_hash, cache_cadre_type_content, cache_cadre_header) ";
        $query .= "VALUES ('".addslashes($hashCadre)."', 'array', '".addslashes($render)."')";
        $query .= "ON DUPLICATE KEY UPDATE cache_cadre_header='".addslashes($render)."'";
        
        if (pmb_mysql_query($query)) {
            return true;
        }
        return false;
    }
    
    private function getHeaderCache()
    {
        $hashCadre = $this->layoutElement::getHashCadre($this->layoutElement->getSemantic()->getIdTag());
        
        $query = "SELECT cache_cadre_header FROM cms_cache_cadres WHERE cache_cadre_type_content = 'array' ";
        $query .= "and cache_cadre_hash = '".addslashes($hashCadre)."'";
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            return \encoding_normalize::json_decode(pmb_mysql_result($result, 0), true);
        }
        return false;
    }
}
