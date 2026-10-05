<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Stats.php,v 1.1 2023/05/11 13:17:39 qvarin Exp $

namespace Pmb\DSI\Models;

use Pmb\DSI\Models\Channel\RootChannel;

class Stats
{
    const REPORT_NULL = 0;

    const REPORT_LINK = 1;

    public $stats = null;

    public $channelType = 0;

    public $settings = null;

    public $report = [
        "type" => Stats::REPORT_NULL,
        "data" => null
    ];

    public function __construct(int $channelType, object $settings = null)
    {
        $this->channelType = $channelType;
        $this->settings = !empty($settings) ? $settings : new \StdClass();
    }

    public function setStat($stat, $value)
    {
        $this->stats[$stat] = $value;
    }

    public function fetchStats()
    {
        if (isset($this->stats)) {
            return false;
        }

        $this->stats = [];
        $className = array_search($this->channelType, RootChannel::IDS_TYPE, true);
        foreach ($className::fetchStats($this) as $stat => $value) {
            $this->setStat($stat, $value);
        }
    }

    public function getStats()
    {
        return $this->stats;
    }

    public function setReport(int $type = Stats::REPORT_NULL, $data = null)
    {
        $this->report = [
            "type" => $type,
            "data" => $data
        ];
    }

    public function getReport()
    {
        return $this->report;
    }
}