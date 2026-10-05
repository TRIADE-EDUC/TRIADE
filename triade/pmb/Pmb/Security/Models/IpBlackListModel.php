<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: IpBlackListModel.php,v 1.3.4.2 2025/05/20 14:00:07 qvarin Exp $

namespace Pmb\Security\Models;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Common\Models\Model;
use Pmb\Security\Library\ListInterface;
use Pmb\Security\Orm\IpBlackListOrm;

class IpBlackListModel extends Model implements ListInterface
{
    /**
     * Correspond au namespace de l'ORM
     *
     * @var string
     */
    protected $ormName = IpBlackListOrm::class;

    /**
     * ID
     *
     * @var int
     */
    public $idIpBlacklist = 0;

    /**
     * Address IP
     *
     * @var string
     */
    public $ipBlacklistIp = "";

    /**
     * Ajoute une IP dans la liste noire
     *
     * @param string $ip
     * @return void
     */
    public function add(string $ip): void
    {
        $orm = new IpBlackListOrm();

        $orm->ip_blacklist_ip = $ip;
        $orm->ip_blacklist_time = date('Y-m-d H:i:s');

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
        $orms = IpBlackListOrm::find('ip_blacklist_ip', $ip);
        if (!empty($orms)) {
            $orms[0]->delete();
        }
    }

    /**
     * Verifie si une IP est dans la liste noire
     *
     * @param string $ip
     * @return boolean
     */
    public function isInList(string $ip): bool
    {
        return !empty(IpBlackListOrm::find('ip_blacklist_ip', $ip));
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

        $orms = IpBlackListOrm::fetchList($start, $nb_per_page, 'ip_blacklist_time DESC, ip_blacklist_ip ASC');
        return array_map(function ($orm) {
            return [
                'id' => $orm->id_ip_blacklist,
                'ip' => $orm->ip_blacklist_ip,
                'date' => format_date($orm->ip_blacklist_time)
            ];
        }, $orms);
    }
}
