<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationParserManifest.php,v 1.4 2023/06/23 12:38:09 dbellamy Exp $

namespace Pmb\Authentication\Models;

use Pmb\Common\Library\Parser\ParserManifest;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AuthenticationParserManifest extends ParserManifest
{

    public $params = [];

    /**
     *
     * @param string $path
     * @throws \InvalidArgumentException
     */
    protected function formatData()
    {
        foreach ($this->simplexml->children() as $prop => $value) {
            if ("name" == $prop) {
                $this->name = $value->__toString();
                continue;
            }
            if (in_array($prop, [
                "search_process"
            ])) {
                continue;
            }

            if ($value->count()) {
                foreach ($value->children() as $childProp => $child) {
                    $this->params[$prop][$childProp] = $child->__toString();
                }
            } else {
                $this->params[$prop]["value"] = $value->__toString();
            }
        }

        if (! empty($this->simplexml->search_process)) {
            $this->params["search_process"] = $this->parseElementSearchProcess();
        }
    }

    /**
     *
     * @param \SimpleXMLElement $simplexml
     */
    protected function formatDataArray(\SimpleXMLElement $simplexml)
    {
        if (! isset($this->params[$simplexml->getName()])) {
            $this->params[$simplexml->getName()] = [];
        }
        foreach ($simplexml->children() as $value) {
            if (! $value->count()) {
                $this->params[$simplexml->getName()][] = $value->__toString();
            }
        }
    }

    protected function parseElementSearchProcess()
    {
        $search_process = array();
        foreach ($this->simplexml->search_process->children() as $prop => $value) {
            if (! is_array($search_process[$prop])) {
                $search_process[$prop] = array();
            }
            $search_process[$prop][] = $value->__toString();
        }
        return $search_process;
    }
}