<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ParserManifest.php,v 1.3.8.1 2025/03/19 15:53:33 jparis Exp $

namespace Pmb\Common\Library\Parser;

use Pmb\Common\Helper\Helper;

class ParserManifest
{
    /**
     * 
     * @var \SimpleXMLElement
     */
    public $simplexml = null;
    
    /**
     * 
     * @var string
     */
    public $name = "";
	/**
	 *
	 * @var array
	 */
	public $author = [
		"name" => "",
		"organisation" => ""
	];	
	
	/**
	*
	* @var string
	*/
	public $type;

	/**
	 *
	 * @var string
	 */
	public $createdDate;

	/**
	 *
	 * @var string
	 */
	public $version = "1.0";

	/**
	 *
	 * @var string
	 */
	public $defaultLanguage;
	
	/**
	 * 
	 * @var string
	 */
	protected $path = "";

	/**
	 * 
	 * @param string $path
	 */
	public function __construct(string $path)
	{
	    $this->path = $path;
		$this->parse();
		$this->formatData();
	}
    
	/**
	 * 
	 * @throws \InvalidArgumentException
	 * @return \SimpleXMLElement
	 */
	protected function parse()
	{
		/**
		 * @var \SimpleXMLElement $simplexml
		 */
		$this->simplexml = simplexml_load_file($this->path);
		if ($this->simplexml === false) {
			throw new \InvalidArgumentException("Parse error ({$this->path})");
		}
		if (! empty($this->simplexml->author)) {
			$this->author = [
			    "name" => $this->simplexml->author->name->__toString() ?? "",
			    "organisation" => $this->simplexml->author->organisation->__toString() ?? ""
			];
		}
		return $this->simplexml;
	}
	
	/**
	 * formatage des donnees
	 */
	protected function formatData() 
	{
	    foreach ($this->simplexml->children() as $prop => $value) {
	        if (in_array($prop, ['author'])) {
	            continue;
	        }
	        if (!$value->count()) {
	            $this->{Helper::camelize($prop)} = $value->__toString();
	        } else {
	            $this->formatDataArray($value);
	        }
	    }
	}
	
	/**
	 * 
	 * @param \SimpleXMLElement $simplexml
	 */
	protected function formatDataArray(\SimpleXMLElement $simplexml) {
	    if (!isset($this->{Helper::camelize($simplexml->getName())})) {
	        $this->{Helper::camelize($simplexml->getName())} = [];
	    }
	    foreach ($simplexml->children() as $value) {
	        if (!$value->count()) {
	            $this->{Helper::camelize($simplexml->getName())}[] = $value->__toString();
	        }
	    }
	}
}

