<?php

namespace Pmb\Common\Library\ICalendar;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class ICalendarException extends \Exception
{
}
