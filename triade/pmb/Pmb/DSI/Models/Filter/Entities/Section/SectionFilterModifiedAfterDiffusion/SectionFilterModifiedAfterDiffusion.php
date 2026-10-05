<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SectionFilterModifiedAfterDiffusion.php,v 1.1.4.3 2025/05/21 08:03:12 rtigero Exp $

namespace Pmb\DSI\Models\Filter\Entities\Section\SectionFilterModifiedAfterDiffusion;

use Pmb\DSI\Models\Filter\Entities\Section\SectionFilter;

class SectionFilterModifiedAfterDiffusion extends SectionFilter
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
                $section = new \cms_section($id);

                $date = \DateTime::createFromFormat("Y-m-d H:i:s", $section->last_update_date);

                if ($date->format("U") > $lastDiffusionDate->format("U")) {
                    $filteredData[$id] = $item;
                }
            }
        } else {
            foreach ($this->data as $id => $item) {
                $section = new \cms_section($id);
                $filteredData[$id] = $item;
            }
        }

        return $filteredData;
    }
}
