<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ListInterface.php,v 1.2.4.2 2025/05/20 14:00:08 qvarin Exp $

namespace Pmb\Security\Library;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

interface ListInterface
{
    public function add(string $ip): void;

    public function remove(string $ip): void;

    public function isInList(string $ip): bool;

    public static function fetchList(int $page, int $nb_per_page = 20): array;
}
