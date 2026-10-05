<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DSIParserManifest.php,v 1.8.4.1 2025/04/11 09:35:11 jparis Exp $

namespace Pmb\DSI\Models;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Library\Parser\ParserManifest;

class DSIParserManifest extends ParserManifest
{

	/**
	 *
	 * @var string
	 */
	public $namespace;

	/**
	 *
	 * @var array
	 */
	public $compatibility = [];

    public $levels = [];

	public $manually;

	public $id;

	public $previewable;
    
	public $customizable;

	public $limitable;

    public $allowedInModels;

    public $defaultModelImage;

    public $bypassSendQueue;

	/**
	 * 
	 * @param string $path
	 * @throws \InvalidArgumentException
	 */
	protected function formatData()
	{
		foreach ($this->simplexml->children() as $prop => $value) {
			if (in_array($prop, ['compatibility', 'author', 'levels'])) {
				continue;
			}

			$this->{Helper::camelize($prop)} = $value->__toString();
		}

        if (! empty($this->simplexml->compatibility)) {
            $this->compatibility = $this->parseElement($this->simplexml->compatibility, ["attachments"]);
        }
        if (! empty($this->simplexml->compatibility->attachments)) {
            $this->compatibility['attachments'] = $this->parseElement($this->simplexml->compatibility->attachments);
        }

        if (! empty($this->simplexml->levels)) {
            foreach ($this->simplexml->levels->children() as $type => $value) {
                $value = $value->element->__toString();

                $this->levels[$type] = [];

                if(!empty($value)) {
                    $this->levels[$type][] = $value;
                }
            }
        }
	}

	protected function parseElement(\SimpleXMLElement $element, array $ignoreTypes = [])
    {
        if (! empty($element)) {
            $children = [];
            foreach ($element->children() as $type => $value) {
                if (empty($type) || in_array($type, $ignoreTypes)) {
                    // compatibility est vide
                    continue;
                }

                $value = $value->__toString();
               
                if ("\\" == substr($value, -1)) {
                    // On a un Dossier
                    global $base_path;
                    $value = str_replace("\\", "/", $value);
                    //a verifier le ignoremanifest
                    //foreach (DSIParserDirectory::getInstance()->getManifests("{$base_path}/{$value}", [$this->path]) as $manifest) {
                    foreach (DSIParserDirectory::getInstance()->getManifests("{$base_path}/{$value}") as $manifest) {
                        $children[$type][] = $manifest->namespace;
                    }
                } elseif (class_exists($value)) {
                    // On a un Namespace
                    $children[$type][] = $value;
                }
            }
            return $children;
        }

        return null;
    }
}

