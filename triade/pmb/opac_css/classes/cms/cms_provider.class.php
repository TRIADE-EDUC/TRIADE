<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_provider.class.php,v 1.3.20.1 2025/01/08 14:32:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_provider
{
    public const OPTIMIZE_MEMORY = 0;

    public const OPTIMIZE_SPEED = 1;

    private static $optimizer = self::OPTIMIZE_MEMORY;

    private static $classes = [];

    /**
     * Retourne une instance de cms_article ou de cms_section
     *
     * @param string $type
     * @param int $id
     * @return cms_article|cms_section
     */
    public static function get_instance($type, $id)
    {
        $id = intval($id);

        if (!isset(self::$classes[$type])) {
            self::$classes[$type] = [];
        }

		if (self::$optimizer === self::OPTIMIZE_MEMORY) {
            $nbArt = count(self::$classes['article'] ?? []);
            $nbSec = count(self::$classes['section'] ?? []);

			if (($nbArt + $nbSec) >= 100) {
				self::$classes['article'] = [];
				self::$classes['section'] = [];
			}
        }

        if (!isset(self::$classes[$type][$id])) {
            switch($type) {
                case "article":
                    self::$classes[$type][$id] = new cms_article($id);
                    break;

                case "section":
                    self::$classes[$type][$id] = new cms_section($id);
                    break;
            }
        }

        return self::$classes[$type][$id];
    }
}
