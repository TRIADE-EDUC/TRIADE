<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Request.php,v 1.1 2022/08/02 09:49:55 qvarin Exp $

namespace Pmb\Common\Helper;

class Request
{
	/**
	 *
	 * @var integer
	 */
	protected const REDIRECT_CODE = 301;
	
	public static function redirect(string $redirect, int $code = self::REDIRECT_CODE) 
	{
		header("Location: {$redirect}", true, $code);
		session_write_close();
		exit();
	}
}