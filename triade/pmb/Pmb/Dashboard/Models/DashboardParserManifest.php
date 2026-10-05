<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DashboardParserManifest.php,v 1.5 2024/03/18 12:48:38 jparis Exp $

namespace Pmb\Dashboard\Models;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Library\Parser\ParserManifest;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class DashboardParserManifest extends ParserManifest
{
    /**
     * @var string
     */
    public $namespace = "";

    public $settings = [];

    public $source = "";

    public $displayFormats = [];

    protected function formatData()
    {
        foreach ($this->simplexml->children() as $prop => $value) {

            if ($prop == 'author') {
                continue;
            }
            if (!$value->count()) {
                $this->{Helper::camelize($prop)} = $value->__toString();
            } else {
                $this->{Helper::camelize($prop)} = $this->formatDataArray($value);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $simplexml
     * @return array
     */
    protected function formatDataArray(\SimpleXMLElement $simplexml)
    {
        $result = [];

        foreach ($simplexml->children() as $child) {
            $key = Helper::camelize($child->getName());
            if (!$child->count()) {
                if(isset($result[$key])) {
                    if(is_string($result[$key])) {
                        $result[$key] = [$result[$key]];
                    }
                    $result[$key][] = $child->__toString();
                } else {
                    $result[$key] = $child->__toString();
                }
            } else {
                $result[$key] = $this->formatDataArray($child);
            }
        }

        return $result;
    }
}