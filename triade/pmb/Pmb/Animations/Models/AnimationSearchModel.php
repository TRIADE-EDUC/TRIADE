<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationSearchModel.php,v 1.1.2.2 2025/06/04 12:39:32 qvarin Exp $

namespace Pmb\Animations\Models;

use Pmb\Common\Models\SearchModel;

class AnimationSearchModel extends SearchModel
{
    /**
     * Add Filter
     *
     * @param string $query
     * @param string $labelId
     * @return string
     */
    protected function addFilter(string $query, $labelId)
    {
        if (isset($this->filter)) {

            $clause = [];
            if (!empty($this->filter['status']) && false === array_search(0, $this->filter['status'])) {
                $joinAnimations = ' join anim_animations on anim_animations.id_animation=' . $labelId;
                $clause[] = ' num_status IN ('. implode(',', $this->filter['status']) .')';
            }

            if (!empty($this->filter['types']) && false === array_search(0, $this->filter['types'])) {
                $joinAnimations = ' join anim_animations on anim_animations.id_animation=' . $labelId;
                $clause[] = ' num_type IN ('. implode(',', $this->filter['types']) .')';
            }

            if (!empty($this->filter['locations']) && false === array_search(0, $this->filter['locations'])) {
                $joinLocations = ' join anim_animation_locations on anim_animation_locations.num_animation=' . $labelId;
                $clause[] = ' anim_animation_locations.num_location IN ('. implode(',', $this->filter['locations']) .')';
            }

            if (!empty($this->filter['communication_type']) && false === array_search(0, $this->filter['communication_type'])) {
                $joinCommunicationType = ' join anim_mailings on anim_mailings.num_animation=' . $labelId;
                $clause[] = ' anim_mailings.num_mailing_type IN ('. implode(',', $this->filter['communication_type']) .')';
            }

            if (!empty($clause)) {
                if (!empty($joinAnimations)) {
                    $query .= " " . $joinAnimations;
                }
                if (!empty($joinLocations)) {
                    $query .= " " . $joinLocations;
                }
                if (!empty($joinCommunicationType)) {
                    $query .= " " . $joinCommunicationType;
                }

                $query .= ' WHERE ' . implode(' AND ', $clause);
            }
        }
        return $query;
    }
}
