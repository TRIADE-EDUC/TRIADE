<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordFilterLastDiffusion.php,v 1.5.4.2 2025/05/21 08:03:10 rtigero Exp $

namespace Pmb\DSI\Models\Filter\Entities\Record\RecordFilterLastDiffusion;

use Pmb\DSI\Models\Filter\Entities\Record\RecordFilter;

class RecordFilterLastDiffusion extends RecordFilter
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

        //Si on n'a pas de dernière diffusion on renvoie tout
        if (is_null($lastDiffusionDate)) {
            return $this->data;
        }
        $nbDays = new \DateInterval("P" . abs($this->fieldsValues->field_nb_days) . "D");

        // Rend l'intervalle négatif
        if ($this->fieldsValues->field_nb_days < 0) {
            $nbDays->invert = 1;
        }

        $lastDiffusionDate->add($nbDays);

        foreach ($this->data as $id => $item) {
            $notice = new \notice($id);
            $date = \DateTime::createFromFormat("d/m/Y H:i:s", $notice->create_date);

            if (!$date) {
                // Ignore les entrées avec des dates invalides
                continue;
            }

            if ($date->format("U") > $lastDiffusionDate->format("U")) {
                $filteredData[$id] = $item;
            }
        }

        return $filteredData;
    }
}
