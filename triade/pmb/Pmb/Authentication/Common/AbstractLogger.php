<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AbstractLogger.php,v 1.2 2023/06/23 12:38:09 dbellamy Exp $

namespace Pmb\Authentication\Common;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

abstract class AbstractLogger
{

    protected static $error = false;

    protected static $logger = null;

    public function getError()
    {
        return static::$error;
    }

    /**
     * Constructeur
     *
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return void
     */
    public function __construct(LoggerInterface $logger = null)
    {
        if (is_null($logger)) {
            $logger = new NullLogger();
        }
        static::$logger = $logger;
    }

    public function setLogger(LoggerInterface $logger = null)
    {
        if (is_null($logger)) {
            $logger = new NullLogger();
        }
        static::$logger = $logger;
    }

    public function getLogger()
    {
        return static::$logger;
    }
}
