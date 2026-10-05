<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RGAABuilder.php,v 1.15.2.2.2.4 2025/04/30 08:32:19 rtigero Exp $
namespace Pmb\Common\Library\RGAA;

use Pmb\Common\Helper\HTML;
use Pmb\CMS\Library\ParserXML\ {
    Container,
    Zone,
    TreeElement
};
use Pmb\Common\Helper\Portal;

class RGAABuilder
{

    private static $instance = null;

    /**
     * DomDocument cms_build_id_rgaa.xml
     *
     * @var \DomDocument
     */
    public $xmlDom;

    /**
     * Page HTML a transformer
     *
     * @var \DomDocument
     */
    public $htmlDom;

    /**
     * Headers
     *
     * @var array
     */
    public $headers = [];

    /**
     * Type de page
     *
     * @var string|integer
     */
    protected $typePage;

    /**
     * Sous-type de page
     *
     * @var string|integer
     */
    protected $subTypePage;

    /**
     * Permet de definir un title, pour la page courante
     *
     * @var string
     */
    public static $title = "";

    private function __construct(string $html, array $headers = [])
    {
        global $charset;

        $this->typePage = intval(Portal::getTypePage());
        $this->subTypePage = intval(Portal::getSubTypePage());

        $this->xmlDom = new \DomDocument();
        $this->xmlDom->load(__DIR__ . "/cms_build_id_rgaa.xml");

        $this->htmlDom = new \DomDocument();
        $this->htmlDom->encoding = $charset;
        if (!empty($html)) {
            @$this->htmlDom->loadHTML($html);
        }
        $this->headers = $headers;
    }

    /**
     * Transforme du HTML donne en HTML +RGAA
     *
     * @param string $html
     * @param array $headers
     * @return string
     */
    public static function transform(string $html, array $headers = [])
    {
        $html = HTML::cleanHTML($html);
        $html = static::stripMultiBr($html);
        $builder = static::getInstance($html, $headers);

        $container = new Container(__DIR__ . "/cms_build_id_rgaa.xml");

        $tree = $builder->fetchTree($container->zone);
        $builder->reorder($tree[0]['id'], $tree[0]['children']);

        $builder->updateSemantic();
        $builder->requiredAttributs();
        $builder->formatHeadingElements();
        $builder->replaceHeaders();
        $builder->domListNumberItemsChecker();
        $builder->formatIcons();
        $builder->loadJS();

        $html = $builder->htmlDom->saveHTML();
        return $html;
    }

    /**
     * Insere les js dans le dom
     */
    public function loadJS()
    {
        $directory = __DIR__ . '/js';
        if (is_dir($directory)) {
            $files = scandir($directory);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'js') {
                    $content = file_get_contents($directory . '/' . $file);
                    $script = $this->htmlDom->createElement('script');
                    $scriptContent = $this->htmlDom->createTextNode($content);
                    $script->appendChild($scriptContent);
                    if (is_object($this->htmlDom->getElementsByTagName('body')->item(0))) {
                        $this->htmlDom->getElementsByTagName('body')->item(0)->appendChild($script);
                    }
                }
            }
        }
    }

    /**
     * Verifie et ajoute les attributs obligatoire
     */
    protected function requiredAttributs()
    {
        $imgObjects = $this->htmlDom->getElementsByTagName('img');
        foreach ($imgObjects as $imgObject) {
            if (! $imgObject->hasAttribute('alt')) {
                $imgObject->setAttribute('alt', '');
            }
        }
    }

    /**
     *
     * @param string $parentId
     * @param array $children
     */
    protected function reorder(string $parentId, array $children)
    {
        $parentNode = $this->htmlDom->getElementById($parentId);
        foreach ($children as $element) {
            $node = $this->htmlDom->getElementById($element['id']);
            if (! $node) {
                continue;
            }

            if (! empty($element['children'])) {
                $this->reorder($element['id'], $element['children']);
            }
            if(isset($parentNode)) {
                $node = $parentNode->appendChild($node);
            }
        }
    }

    /**
     *
     * @param TreeElement $element
     * @return array
     */
    protected function fetchTree(TreeElement $element)
    {
        $tree = [];

        do {
            $node = [
                'id' => $element->id,
            ];

            if ($element instanceof Zone) {
                $firstChild = $element->getFirstChild();
                if (! empty($firstChild)) {
                    // Si il a un suivant, on le parcourt recursivement pour recuperer ses enfants
                    $node['children'] = $this->fetchTree($firstChild);
                }
            }
            $tree[] = $node;
        } while ($element = $element->getNext());

        return $tree;
    }

    /**
     * Permet de mettre a jour les tags semantiques
     */
    protected function updateSemantic()
    {
        $cmsObjects = $this->xmlDom->getElementsByTagName('cms_object');
        foreach ($cmsObjects as $cmsObject) {
            $node = $this->htmlDom->getElementById($cmsObject->getAttribute('id'));
            if (! $node) {
                continue;
            }

            $semanticTag = $cmsObject->getAttribute('semantic-tag');
            if (! empty($semanticTag) && $node->nodeType != $semanticTag) {
                $newNode = $this->htmlDom->createElement($semanticTag);
                $semanticRole = $cmsObject->getAttribute('semantic-role');
                if (! empty($semanticRole)) {
                    $newNode->setAttribute('role', $semanticRole);
                }
                $newNode = $this->htmlDom->importNode($newNode);

                foreach ($node->attributes as $attribute) {
                    $newNode->setAttribute($attribute->nodeName, $attribute->nodeValue);
                    if ($attribute->nodeName == "id") {
                        $newNode->setIdAttribute($attribute->nodeName, true);
                    }
                }

                while ($childNode = $node->childNodes->item(0)) {
                    $newNode->appendChild($childNode);
                }

                $node->parentNode->replaceChild($newNode, $node);
            }
        }
    }

    /**
     * Permet de formater les éléments de titre de section (<h1> - <h6>)
     * Et de definir egalement la balise <title>
     */
    protected function formatHeadingElements()
    {
        global $charset;

        if (in_array($this->subTypePage, Portal::SEARCH_SUB_PAGES, true)) {
            return $this->formatSearchHeadingElements();
        }
        if (in_array($this->subTypePage, Portal::UNIVERS_SEGMENT_SUB_PAGES, true)) {
            return $this->formatUniverSegmentHeadingElements();
        }

        $label = "";
        if (!empty(static::$title)) {
            $label = $this->getFormatedTextContent(static::$title);
        }

        $nodes = $this->htmlDom->getElementsByTagName("h1");
        if ($nodes->length && empty($label)) {
            $label = $this->getFormatedTextContent($nodes->item(0)->textContent);
        }

        if (empty($label)) {
            $label = Portal::getLabel($this->subTypePage);
        }
        if (empty($label)) {
            $label = Portal::getLabel($this->typePage);
        }

        if (! empty($label)) {
            $this->headers[] = "<title>"
                . htmlentities($this->formatHeadTitle($label), ENT_QUOTES, $charset)
                . "</title>";
        }
    }

    protected function formatHeadTitle($title)
    {
        global $msg, $charset;
        global $opac_biblio_name;

        if (! empty($msg['rgaa_title_format'])) {
            return str_replace([
                '!!page_title!!',
                '!!biblio_name!!'
            ], [
                html_entity_decode(strip_tags($title), ENT_QUOTES, $charset),
                $opac_biblio_name
            ], $msg['rgaa_title_format']);
        } else {
            return html_entity_decode(strip_tags($title), ENT_QUOTES, $charset) . ' ' . $opac_biblio_name;
        }
    }

    /**
     * Permet de formater les éléments de titre de section pour une page de recherche universe/segment
     * Et de definir egalement la balise <title>
     */
    protected function formatUniverSegmentHeadingElements()
    {
        $universeTitleNode = $this->htmlDom->getElementById("universe_title");
        if (! $universeTitleNode) {
            return false;
        }

        $segmentTitleNode = $this->htmlDom->getElementById("segment_title");
        if (! $segmentTitleNode) {
            // On est sur une page d'univer on met son nom dans le title
            $universeTitleNode = $this->changeTagName($universeTitleNode ?? null, "h1");
            $title = $this->getFormatedTextContent($universeTitleNode->textContent);
            if (! empty($title)) {
                $this->headers[] = "<title>"
                    . \common::get_formatted_page_title(Portal::getLabel($this->typePage) . " {$title}")
                    . "</title>";
            }

            return false;
        }

        $segmentTitleNode = $this->changeTagName($segmentTitleNode ?? null, "h1");

        $segmentSearchNode = $this->htmlDom->getElementById("new_search_segment_title");
        $segmentSearchNode = $this->changeTagName($segmentSearchNode ?? null, "h3");

        $segmentResultsNode = $this->htmlDom->getElementById("segment_search_results");
        $segmentResultsNode = $this->changeTagName($segmentResultsNode ?? null, "p");

        $humanQuery = $this->getFormatedTextContent($segmentTitleNode->textContent);
        if (!empty($segmentSearchNode->textContent)) {
            $humanQuery .= " " . strtolower($this->getFormatedTextContent($segmentSearchNode->textContent));
        }
        if (!empty($segmentResultsNode->textContent)) {
            $humanQuery .= " " . $this->getFormatedTextContent($segmentResultsNode->textContent);
        }

        global $page, $msg;
        $page = intval($page ?? 0);
        if (! empty($page) && ! empty($humanQuery)) {
            // On ajout le numero de page dans le title
            $humanQuery .= " " . sprintf(trim(strip_tags($msg['rgaa_search_page_title'])), $page);
        }

        if (! empty($humanQuery)) {
            $this->headers[] = "<title>"
                . \common::get_formatted_page_title(Portal::getLabel($this->typePage) . " {$humanQuery}")
                . "</title>";
        }
    }

    /**
     * Permet de formater les éléments de titre de section pour une page de recherche
     * Et de definir egalement la balise <title>
     */
    protected function formatSearchHeadingElements()
    {
        $node = $this->htmlDom->getElementById("resultatrech");
        if (! $node) {
            return false;
        }

        $children = [];
        for ($i = 0; $i < $node->childNodes->length; $i ++) {
            $child = $node->childNodes->item($i);
            if ($child->nodeType == \XML_ELEMENT_NODE && in_array($child->tagName, [
                "h1",
                "h3"
            ])) {
                $children[] = $child;
            }
        }

        $this->changeTagName($children[0] ?? null, "h1");

        $humanQueryNode = $this->htmlDom->getElementById('searchResult-search');
        $humanQueryNodeAffiliate = $this->htmlDom->getElementById('searchResult-search-affiliate');

        if (isset($humanQueryNode)) {
            $humanQueryNode = $this->changeTagName($humanQueryNode, "p");
        } elseif (isset($humanQueryNodeAffiliate)) {
            // Recherche affiliee.
            $humanQueryNode = $humanQueryNodeAffiliate;
            $humanQueryNode = $this->changeTagName($humanQueryNode, "p");
        } else {
            // Cas specifique ou la human query est dans la div#resultatrech
            $humanQueryNode = $this->changeTagName($children[1] ?? null, "p");
        }

        if (! $humanQueryNode) {
            return false;
        }

        global $page, $msg;
        $humanQuery = $this->getFormatedTextContent($humanQueryNode->textContent);

        $page = intval($page ?? 0);
        if (! empty($page) && ! empty($humanQuery)) {
            // On ajout le numero de page dans le title
            $humanQuery .= " " . sprintf(trim(strip_tags($msg['rgaa_search_page_title'])), $page);
        }

        if (! empty($humanQuery)) {
            $this->headers[] = "<title>" . \common::get_formatted_page_title($humanQuery) . "</title>";
        }
    }

    /**
     * Permet de changer le tagName d'un element DOM
     *
     * @param \DOMElement $node
     * @param string $name
     * @return \DOMElement|boolean
     */
    protected function changeTagName($node, $name)
    {
        if (empty($node) || empty($name) || empty($node->parentNode)) {
            return false;
        }

        // Correspond deja au tag demande
        if ($node->tagName == $name) {
            return $node;
        }

        $newnode = $this->htmlDom->createElement($name);

        $childnodes = array();
        if (isset($node->childNodes)) {
            foreach ($node->childNodes as $child) {
                $childnodes[] = $child;
            }

            foreach ($childnodes as $child) {
                $child2 = $this->htmlDom->importNode($child, true);
                $newnode->appendChild($child2);
            }
        }

        if (isset($node->attributes)) {
            foreach ($node->attributes as $attrName => $attrNode) {
                $attrName = $attrNode->nodeName;
                $attrValue = $attrNode->nodeValue;

                $newnode->setAttribute($attrName, $attrValue);
            }
        }

        $node->parentNode->replaceChild($newnode, $node);
        return $newnode;
    }

    /**
     * Retourne le textContent formater
     *
     * @param \DOMElement $node
     * @return string
     */
    protected function getFormatedTextContent($textContent)
    {
        $textContent = trim(strip_tags($textContent ?? ""));
        return preg_replace("/\s+/", " ", $textContent);
    }

    /**
     * Remplacement des headers
     *
     * @return boolean
     */
    public function replaceHeaders()
    {
        global $charset;

        if (! is_countable($this->headers) || ! count($this->headers)) {
            return false;
        }

        $tmpDom = new \DomDocument();
        $headNode = $this->htmlDom->getElementsByTagName("head")->item(0);

        foreach ($this->headers as $header) {

            if ($charset == "utf-8") {
                @$tmpDom->loadHTML("<?xml version='1.0' encoding='$charset'>" . $header);
            } else {
                @$tmpDom->loadHTML($header);
            }

            $tmpHeadNode = $tmpDom->getElementsByTagName("head")->item(0);
            for ($i = 0; $i < $tmpHeadNode->childNodes->length; $i ++) {

                $newItem = $tmpHeadNode->childNodes->item($i);
                $elments = $this->htmlDom->getElementsByTagName($newItem->nodeName);

                if ($elments->length > 0) {
                    for ($j = 0; $j < $elments->length; $j ++) {

                        $child = $elments->item($j);
                        $replace = true;

                        if ($newItem->hasAttributes()) {
                            for ($k = 0; $k < $newItem->attributes->length; $k ++) {
                                $attr = $newItem->attributes->item($k);
                                if ($attr->name == "content" || $attr->name == "value") {
                                    continue;
                                }

                                if (
                                    ! $child->hasAttribute($attr->name) ||
                                    $child->getAttribute($attr->name) != $attr->value
                                ) {
                                    $replace = false;
                                }
                            }
                        }

                        if ($replace) {
                            $child->parentNode->removeChild($child);
                            break;
                        }
                    }
                }
                if (is_object($headNode)) {
                    $headNode->appendChild($this->htmlDom->importNode($newItem, true));
                }
            }
        }
    }

    /**
     *
     * @return RGAABuilder
     */
    private static function getInstance(string $html, array $headers = [])
    {
        if (null === static::$instance) {
            static::$instance = new static($html, $headers);
        }
        return static::$instance;
    }

    /**
     * Suppression des balises <br> multiples
     *
     * @param string $html
     * @return string
     */
    protected static function stripMultiBr($html = '')
    {
        $html = preg_replace("/(<br\s*\/*>\s*\t*\R*(&nbsp;)*)+/", '<br>', $html);
        return $html;
    }

    /**
     * Ajout d'un role presentation pour les lists (ul/ol/dl) composés d'un seul élément
     */
    protected function domListNumberItemsChecker()
    {
        // Définition des différents types de liste HTML
        $listTypeArray = [
            'ol' => ['li'],
            'ul' => ['li'],
            'dl' => ['dt', 'dd'],
        ];

        foreach($listTypeArray as $listType => $listTag) {
            // Récupération des listes présentes dans le DOM
            $nodeListType = $this->htmlDom->getElementsByTagName($listType);
            
            // Parcours des listes et ajout du role presentation si la liste est composé de moins de 2 éléments
            foreach($nodeListType as $node){
                foreach ($listTag as $tag) {
                    $list = $node->getElementsByTagName($tag);
                    if($list->length <= 1){
                        $node->setAttribute('role', 'presentation');
                    }
                }
            }
        }
    }

    /**
     * Ajoute les aria-hidden aux icons fontawesome qui en non pas
     */
    protected function formatIcons()
    {
        $iconObjects = $this->htmlDom->getElementsByTagName('i');

        foreach ($iconObjects as $iconObject) {
            $class = $iconObject->getAttribute('class');

            if(preg_match("/^fa fa-/", $class)) {
                $iconObject->setAttribute('aria-hidden', 'true');
            }
        }
    }
}
