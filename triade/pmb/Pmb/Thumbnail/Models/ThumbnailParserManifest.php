<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ThumbnailParserManifest.php,v 1.6 2023/12/19 10:27:36 qvarin Exp $
namespace Pmb\Thumbnail\Models;

use Pmb\Common\Library\Parser\ParserManifest;

class ThumbnailParserManifest extends ParserManifest
{
	/**
	 *
	 * @var string
	 */
	public $namespace = "";

	/**
	 *
	 * @var string
	 */
	public $entityType = "";

	/**
	 *
	 * @var string
	 */
	public $entity = "";
}