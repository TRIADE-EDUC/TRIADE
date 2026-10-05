<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ToUpper.php,v 1.2 2023/07/03 15:10:00 dbellamy Exp $

namespace Pmb\Authentication\Helpers\Transfo;

use Pmb\Authentication\Interfaces\TransfoInterface;
use Pmb\Authentication\Common\AbstractLogger;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class ToUpper extends AbstractLogger implements TransfoInterface
{
    const ARGS = ['value', 'charset'];

    /**
     * Transformation en minuscules
     *
     * @param array $args
     *
     * @return string
     */
    public function transfo($args = [])
    {
        $ret = '';
        if( empty($args['value']) || !is_string($args['value']) ) {
            return $ret;
        }
        $value = $args['value'];

        $charset = null;
        if( !empty($args['charset']) && is_string($args['charset']) ) {
            $charset = $args['charset'];
        }
        $ret = mb_convert_case($value,  MB_CASE_UPPER, $charset);
        return $ret;
    }

    /**
     *
     * {@inheritDoc}
     * @see \Pmb\Authentication\Interfaces\TransfoInterface::getArgs()
     */
    public function getArgs()
    {
        return static::ARGS;
    }
}
