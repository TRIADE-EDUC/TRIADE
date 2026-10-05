<?php
namespace Pmb\Common\Library\CSRF;

use Pmb\Common\Helper\HTML;

class ParserCSRF
{

	/**
	 *
	 * @var string
	 */
	protected const METHOD_POST = "POST";

	/**
	 *
	 * @var string
	 */
	protected const FORM_ATTRIBUTE = "data-csrf";

	/**
	 *
	 * @var bool
	 */
	protected const INGORE_FORM_ATTRIBUTE = true;

	/**
	 *
	 * @var string
	 */
	protected const INPUT_NAME_CSRF = "csrf_token";

	/**
	 *
	 * @var boolean
	 */
	protected const VALIDE_FORM = true;

	/**
	 *
	 * @var boolean
	 */
	protected const INVALIDE_FORM = false;

	/**
	 *
	 * @var \DOMDocument
	 */
	protected $document;

	/**
	 *
	 * @var string
	 */
	protected $encoding = "";

	/**
	 *
	 * @var CollectionCSRF
	 */
	protected $collectionCSRF;

	/**
	 *
	 * @param string $version
	 * @param string $encoding
	 */
	public function __construct(string $version = "1.0", string $encoding = "")
	{
		$this->setEncoding($encoding);
		$this->collectionCSRF = new CollectionCSRF();
		$this->document = new \DOMDocument($version, $this->getEncoding());
	}

	/**
	 *
	 * @return string
	 */
	public function getEncoding(): string
	{
		if (empty($this->encoding)) {
			global $charset;
			$this->encoding = $charset;
		}
		return $this->encoding;
	}

	/**
	 *
	 * @param string $encoding
	 */
	public function setEncoding(string $encoding = "")
	{
		if (empty($encoding)) {
			global $charset;
			$encoding = $charset;
		}
		$this->encoding = $encoding;
	}

	/**
	 *
	 * @param string $html
	 * @throws \Exception
	 * @return string
	 */
	public function parseHTML(string $html): string
	{
		if (empty($html)) {
			return $html;
		}

		$html = $this->formatHTML($html);
		if (! @$this->document->loadHTML($html)) {
			throw new \Exception("HTML could not be loaded");
		}

		$domNodeList = $this->document->getElementsByTagName("form");
		for ($i = 0; $i < $domNodeList->length; $i ++) {
			$domNode = $domNodeList->item($i);
			if ($this->valideForm($domNode)) {
				$this->appendNodeCSRF($domNode);
				if ($domNode->hasAttribute(self::FORM_ATTRIBUTE)) {
    				$domNode->removeAttribute(self::FORM_ATTRIBUTE);
				}
			}
		}
		return $this->document->saveHTML();
	}

	/**
	 *
	 * @param string $html
	 * @throws \Exception
	 * @return string
	 */
	protected function formatHTML(string $html): string
	{
		$html = HTML::cleanHTML($html, $this->getEncoding());

		if (! @$this->document->loadHTML($html)) {
			throw new \Exception("HTML could not be loaded");
		}

		// On vérifie que $html contient bien la META (http-equiv) avec le charset en content-type
		$metaFound = false;
		$domNodeList = $this->document->getElementsByTagName("meta");
		for ($i = 0; $i < $domNodeList->length; $i ++) {
			$domNode = $domNodeList->item($i);
			if ($domNode->hasAttribute('http-equiv') && strtolower($domNode->getAttribute('http-equiv')) == "content-type" && $domNode->hasAttribute('content') && strpos($domNode->getAttribute('content'), "charset=") !== FALSE) {
				$metaFound = true;
				break;
			}
		}
		if($metaFound === false) {
		    $domNodeHead = $this->document->getElementsByTagName("head");
		    if(!empty($domNodeHead->length)) {
    		    $metaNode = $this->document->createElement("meta");
    		    $metaNode->setAttribute('http-equiv', 'Content-Type');
    		    $metaNode->setAttribute('content', 'charset='.$this->getEncoding());
    		    $domNodeHead[0]->appendChild($metaNode);
    		    $html = $this->document->saveHTML();
		    }
		}
		return $html;
	}

	/**
	 *
	 * @param \DOMNode $domNode
	 * @return bool
	 */
	protected function valideForm(\DOMNode $domNode): bool
	{
	    if (! $domNode->nodeName == "form" || (!self::INGORE_FORM_ATTRIBUTE && ! $domNode->hasAttribute(self::FORM_ATTRIBUTE))) {
			return self::INVALIDE_FORM;
		}
		if ($domNode->hasAttribute('method') && strtolower($domNode->getAttribute('method')) == strtolower(self::METHOD_POST)) {
			return self::VALIDE_FORM;
		}
		return self::INVALIDE_FORM;
	}

	/**
	 *
	 * @param \DOMNode $domNode
	 */
	protected function appendNodeCSRF(\DOMNode $domNode): void
	{
		$domNode->appendChild($this->buildNodeCSRF());
	}

	/**
	 *
	 * @return \DOMNode
	 */
	protected function buildNodeCSRF(): \DOMNode
	{
		$nodeInput = $this->document->createElement("input");
		$nodeInput->setAttribute("type", "hidden");
		$nodeInput->setAttribute("name", self::INPUT_NAME_CSRF);
		$nodeInput->setAttribute("value", $this->collectionCSRF->generateToken());
		return $nodeInput;
	}

	/**
	 *
	 * @return string
	 */
	public function generateHiddenField(): string
	{
		return $this->document->saveHTML($this->buildNodeCSRF());
	}
}