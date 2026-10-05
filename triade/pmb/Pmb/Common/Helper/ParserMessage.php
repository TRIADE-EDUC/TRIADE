<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ParserMessage.php,v 1.2 2023/05/03 12:41:20 rtigero Exp $
namespace Pmb\Common\Helper;

trait ParserMessage
{

    protected static $messages = [];

    public static function getMessages()
    {
        if (empty(static::$messages[static::class])) {
            static::parseMessages();
        }
        return static::$messages[static::class];
    }

    public static function setMessages($msg)
    {
        static::$messages[static::class] = $msg;
    }

    protected static function parseMessages()
    {
        global $lang;

        $parents = [];

        $classname = static::class;
        $parents[] = $classname;
        do {
            $parent = static::getParentClass($classname);
            if ($parent) {
                $parents[] = $parent;
            }
            $classname = $parent;
        } while ($parent);

        static::$messages[static::class] = [];
        foreach (array_reverse($parents) as $parent) {
            $path = static::getClassPath($parent) . "/messages/{$lang}.xml";
            if (! is_file($path)) {
                continue;
            }

            $xmlList = new \XMLlist($path);
            $xmlList->analyser();

            static::$messages[static::class] = array_merge(static::$messages[static::class], $xmlList->table);
        }

        unset($xmlList);
    }

    protected static function getParentClass($classname)
    {
        return get_parent_class($classname);
    }

    protected static function getClassPath($classname = null)
    {
        if (empty($classname)) {
            $classname = static::class;
        }

        global $base_path;
        $explode = explode("\\", $classname);
        array_pop($explode);
        return realpath("{$base_path}/" . implode("/", $explode));
    }
}

