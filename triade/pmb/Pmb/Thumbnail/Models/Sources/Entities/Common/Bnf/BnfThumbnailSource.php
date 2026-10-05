<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: BnfThumbnailSource.php,v 1.3 2023/08/18 10:40:52 qvarin Exp $
namespace Pmb\Thumbnail\Models\Sources\Entities\Common\Bnf;

use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;

class BnfThumbnailSource extends RootThumbnailSource
{
    /**
     * md5 de la no image de la BNF
     *
     * @var string
     */
    public const HASH_DEFAULT_IMG = "ae9431d0ef3b086057bd5fe4384fc7d2";
}

