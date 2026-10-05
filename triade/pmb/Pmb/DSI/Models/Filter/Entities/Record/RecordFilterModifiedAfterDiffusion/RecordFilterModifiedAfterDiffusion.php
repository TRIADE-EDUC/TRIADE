<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordFilterModifiedAfterDiffusion.php,v 1.4.4.2 2025/05/21 08:03:10 rtigero Exp $

namespace Pmb\DSI\Models\Filter\Entities\Record\RecordFilterModifiedAfterDiffusion;

use Pmb\DSI\Models\Filter\Entities\Record\RecordFilter;

class RecordFilterModifiedAfterDiffusion extends RecordFilter
{
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

        foreach ($this->data as $id => $item) {
            $notice = new \notice($id);

            $date = \DateTime::createFromFormat("d/m/Y H:i:s", $notice->update_date);

            if (false === $date) {
                continue;
            }

            if ($date->format("U") > $lastDiffusionDate->format("U")) {
                $filteredData[$id] = $item;
            }
        }

        return $filteredData;
    }
}
