<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ProductStatus.php,v 1.3 2023/05/03 12:41:20 rtigero Exp $

namespace Pmb\DSI\Models;

class ProductStatus extends Status
{
	protected $ormName = "Pmb\DSI\Orm\ProductStatusOrm";
	public $idProductStatus = 0;
}

