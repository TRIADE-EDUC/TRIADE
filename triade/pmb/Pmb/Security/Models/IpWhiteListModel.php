<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: IpWhiteListModel.php,v 1.3.4.2 2025/05/20 14:00:06 qvarin Exp $

namespace Pmb\Security\Models;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Common\Models\Model;
use Pmb\Security\Library\ListInterface;
use Pmb\Security\Orm\IpWhiteListOrm;

class IpWhiteListModel extends Model implements ListInterface
{
    /**
     * Correspond au namespace de l'ORM
     *
     * @var string
     */
    protected $ormName = IpWhiteListOrm::class;

    /**
     * ID
     *
     * @var int
     */
    public $idIpWhitelist = 0;

    /**
     * Address IP
     *
     * @var string
     */
    public $ipWhitelistIp = "";

    /**
     * Ajoute une IP dans la liste blanche
     *
     * @param string $ip
     * @return void
     */
    public function add(string $ip): void
    {
        $orm = new IpWhiteListOrm();

        $orm->ip_whitelist_ip = $ip;
        $orm->ip_whitelist_time = date('Y-m-d H:i:s');

        $orm->save();
    }

    /**
     * Supprime une IP
     *
     * @param string $ip
     * @return void
     */
    public function remove(string $ip): void
    {
        $orms = IpWhiteListOrm::find('ip_whitelist_ip', $ip);
        if (!empty($orms)) {
            $orms[0]->delete();
        }
    }

    /**
     * Verifie si une IP est dans la liste blanche
     *
     * @param string $ip
     * @return boolean
     */
    public function isInList(string $ip): bool
    {
        return !empty(IpWhiteListOrm::find('ip_whitelist_ip', $ip));
    }

    /**
     * Renvoie une liste
     *
     * @param integer $page
     * @param integer $nb_per_page (optionnel, par de?faut 20)
     * @return array
     */
    public static function fetchList(int $page, int $nb_per_page = 20): array
    {
        $start = ($page - 1) * $nb_per_page;

        $orms = IpWhiteListOrm::fetchList($start, $nb_per_page, "ip_whitelist_time DESC, ip_whitelist_ip ASC");
        return array_map(function ($orm) {
            return [
                'id' => $orm->id_ip_whitelist,
                'ip' => $orm->ip_whitelist_ip,
                'date' => format_date($orm->ip_whitelist_time)
            ];
        }, $orms);
    }

    /**
     * Renvoie le nombre d'IP dans la liste
     *
     * @return int
     */
    public static function count()
    {
        return IpWhiteListOrm::count();
    }
}
