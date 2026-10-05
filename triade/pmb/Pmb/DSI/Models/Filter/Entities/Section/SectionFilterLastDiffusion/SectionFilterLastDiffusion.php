<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SectionFilterLastDiffusion.php,v 1.1.4.3 2025/05/21 08:03:11 rtigero Exp $

namespace Pmb\DSI\Models\Filter\Entities\Section\SectionFilterLastDiffusion;

use Pmb\DSI\Models\Filter\Entities\Section\SectionFilter;

class SectionFilterLastDiffusion extends SectionFilter
{
    public static $fields = [
        "field_nb_days" => [
            "type" => "number",
            "required" => true
        ]
    ];
    public function __construct(array $data, int $entityId = 0)
    {
        parent::__construct($data, $entityId);
    }

    public function filter(): array
    {
        $filteredData = [];

        $lastDiffusionDate = $this->getLastDiffusionDate();

        if (!is_null($lastDiffusionDate)) {
            $nbDays = new \DateInterval("P" . $this->fieldsValues->field_nb_days . "D");
            $lastDiffusionDate->add($nbDays);

            foreach ($this->data as $id => $item) {
                $section = new \cms_section($id);

                $date = \DateTime::createFromFormat("Y-m-d H:i:s", $section->create_date);

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
