<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HtmlSemantic.php,v 1.4 2022/05/18 08:33:02 rtigero Exp $
namespace Pmb\CMS\Semantics;

class HtmlSemantic extends RootSemantic
{

    /**
     *
     * @var string
     */
    protected $tag = '';

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag(string $tag)
    {
        return $this->tag = $tag;
    }

    public function getNode(): \DomNode
    {
        if (! isset($this->node)) {
            $dom = new \DOMDocument();
            $this->node = $dom->createElement($this->getTag());
            $this->node->setAttribute("id", $this->getIdTag());
            $this->node->setIdAttribute("id", true);
            if (! empty($this->getClasses())) {
                $this->node->setAttribute("class", implode(" ", $this->getClasses()));
            }
            if (! empty($this->getAttributes())) {
                $index = count($this->attributes);
                for ($i = 0; $i < $index; $i ++) {
                    $this->node->setAttribute($this->attributes[$i]['name'], $this->attributes[$i]['value']);
                }
            }
        }
        return $this->node;
    }
    
    public static function getSemanticList() 
    {
        global $include_path;
        $filename = $include_path."/portal/semanticHtml.xml";
        $filename_subst = $include_path."/portal/semanticHtml_subst.xml";
        
        if( is_readable($filename_subst )) {
            $filename = $filename_subst;
        }
        $semantic = json_decode(json_encode(simplexml_load_file($filename, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT)), TRUE);
        return $semantic['tag'] ?? [];
    }
}