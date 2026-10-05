<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationFilterStartDateLastDiffusion.php,v 1.1.4.2 2025/05/21 08:03:10 rtigero Exp $

namespace Pmb\DSI\Models\Filter\Entities\Animation\AnimationFilterStartDateLastDiffusion;

use Pmb\Animations\Models\AnimationModel;
use Pmb\DSI\Models\Filter\Entities\Animation\AnimationFilter;

class AnimationFilterStartDateLastDiffusion extends AnimationFilter
{
    public function __construct(array $data, int $entityId = 0)
    {
        parent::__construct($data, $entityId);
    }

    public function filter(): array
    {
        $filteredData = [];

        $lastDiffusionDate = $this->getLastDiffusionDate();

        if (!is_null($lastDiffusionDate)) {
            foreach ($this->data as $id => $item) {
                $animation = AnimationModel::getAnimation($id);
                $event = $animation->fetchEvent();

                $date = \DateTime::createFromFormat("Y-m-d H:i:s", $event->rawStartDate);

                if ($date->format("U") > $lastDiffusionDate->format("U")) {
                    $filteredData[$id] = $item;
                }
            }
        } else {
            foreach ($this->data as $id => $item) {
                $filteredData[$id] = $item;
            }
        }
        return $filteredData;
    }
}
