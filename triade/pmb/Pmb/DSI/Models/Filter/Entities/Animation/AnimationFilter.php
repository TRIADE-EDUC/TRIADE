<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationFilter.php,v 1.1.4.2 2025/05/21 08:03:12 rtigero Exp $

namespace Pmb\DSI\Models\Filter\Entities\Animation;

use Pmb\DSI\Models\Filter\RootFilter;

class AnimationFilter extends RootFilter
{
    protected function __construct(array $data, int $entityId)
    {
        parent::__construct($data, $entityId);
    }
}
