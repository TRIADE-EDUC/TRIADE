<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootSemantic.php,v 1.8 2023/05/03 11:46:30 qvarin Exp $
namespace Pmb\CMS\Semantics;

use Pmb\CMS\Models\PortalRootModel;

class RootSemantic extends PortalRootModel implements NodeSemantic
{

    /**
     *
     * @var string
     */
    protected $idTag = '';

    /**
     *
     * @var array
     */
    protected $classes = array();

    /**
     *
     * @var array
     */
    protected $attributes = array();

    protected $node = null;

    protected $containerNode = null;

    /**
     * Contient la liste des proprietes a ignorer pour le serialize
     *
     * @var array
     */
    public const IGNORE_PROPS_SERIALISE = [
        "node",
        "containerNode",
    ];

    public function getIdTag()
    {
        return $this->idTag;
    }

    public function setIdTag(string $idTag)
    {
        $this->idTag = $idTag;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function addClass(string $class)
    {
        $this->classes[] = $class;
    }

    public function removeAllClass()
    {
        $this->classes = [];
    }
    
    public function removeClass(string $class)
    {
        $index = array_search($class, $this->classes);
        if ($index !== false) {
            array_splice($this->classes, $index, 1);
        }
    }

    public function getContainerNode(): \DomNode
    {
        return $this->getNode();
    }

    public function getNode(): \DomNode
    {
        if (! isset($this->node)) {
            $dom = new \DOMDocument();
            $this->node = $dom->createElement("div");
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

    public static function getClassSemanticList()
    {
        global $include_path, $msg;

        $filename = $include_path . "/portal/semantics.xml";
        $filename_subst = $include_path . "/portal/semantics_subst.xml";

        if (is_readable($filename_subst)) {
            $filename = $filename_subst;
        }

        $xml = json_decode(json_encode(simplexml_load_file($filename, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT)), TRUE);

        $semantics = array();
        if (! empty($xml['semantic'][0])) {
            $semantics = $xml['semantic'];
        } else {
            $semantics[] = $xml['semantic'];
        }

        $index = count($semantics);
        for ($i = 0; $i < $index; $i ++) {
            $className = "Pmb\\CMS\\Semantics\\{$semantics[$i]['class_name']}";
            if (! class_exists($className)) {
                throw new \Exception("Semantic doesn't exist ({$className})");
            }

            if (substr($semantics[$i]['label'], 0, 4) == "msg:") {
                $code = substr($semantics[$i]['label'], 4, strlen($semantics[$i]['label']));
                if (isset($msg[$code])) {
                    $semantics[$i]['label'] = $msg[$code];
                }
            }

            $semantics[$i]['namespace'] = $className;
        }
        return $semantics;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function addAttribute(string $attribute, string $value = "")
    {
        $this->attributes[] = [
            "name" => $attribute,
            "value" => $value ?? ""
        ];
    }

    public function removeAllAttributes()
    {
        $this->attributes = [];
    }

    public function removeAttribute(string $attributeRemove)
    {
        $attributes = [];

        $index = count($this->attributes);
        for ($i = 0; $i < $index; $i ++) {
            if ($this->attributes[$i]['name'] != $attributeRemove) {
                $attributes[] = [
                    "name" => $this->attributes[$i]['name'],
                    "value" => $this->attributes[$i]['value'] ?? ""
                ];
            }
        }

        $this->attributes = $attributes;
        return $this->attributes;
    }

    /**
     * Retourne la liste des proprietes a ignorer pour le serialise
     *
     * @return array
     */
    protected static function getIgnorePropsSerialise()
    {
        return array_merge(
            parent::IGNORE_PROPS_SERIALISE,
            static::IGNORE_PROPS_SERIALISE
        );
    }
}