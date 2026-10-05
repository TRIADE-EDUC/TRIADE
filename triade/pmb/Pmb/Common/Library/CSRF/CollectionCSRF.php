<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CollectionCSRF.php,v 1.3.2.1 2024/10/15 09:04:37 gneveu Exp $
namespace Pmb\Common\Library\CSRF;

use Pmb\Common\Helper\Request;

class CollectionCSRF
{

	public function __construct()
	{
		if (isset($_SESSION['csrf_token']) && is_array($_SESSION['csrf_token'])) {
			$this->checkTokens();
		} else {
			$_SESSION['csrf_token'] = array();
		}
	}

	protected function checkTokens(): void
	{
		foreach ($_SESSION['csrf_token'] as $index => $csrf_token) {
			$csrf = $this->buildInstance($csrf_token['token'], $csrf_token['time']);
			if ($csrf->expireToken()) {
				static::removeIndex($index);
			}
		}
	}

	/**
	 *
	 * @param CSRF $csrf
	 */
	protected function append(CSRF $csrf): void
	{
		array_push($_SESSION['csrf_token'], [
			"token" => $csrf->getToken(),
			"time" => $csrf->getTime()
		]);
	}

	/**
	 *
	 * @return string
	 */
	public function generateToken(): string
	{
		$csrf = $this->buildInstance();
		$token = $csrf->generateToken();
		$this->append($csrf);
		return $token;
	}

	/**
	 *
	 * @param string $token
	 * @param string $redirect
	 * @param string $defaultRedirect
	 * @return boolean
	 */
	public function valideToken(string $token = "", string $redirect = "", string $defaultRedirect = ""): bool
	{
	    if (empty($defaultRedirect)) {
	        if (defined("GESTION") && GESTION) {
	            global $pmb_url_base;
	            $defaultRedirect = $pmb_url_base;
	        } else {
	            global $opac_url_base;
	            $defaultRedirect = $opac_url_base;
	        }
	    }

		if (empty($redirect)) {
		    $redirect = $defaultRedirect;
		}

		if (!$this->valideTokenWithoutRedirect($token)) {
			Request::redirect($redirect);
			return false;
		}
		return true;
	}

	/**
	 *
	 * @param string $token
	 * @return boolean
	 */
	public function valideTokenWithoutRedirect(string $token = ""): bool
	{
		foreach ($_SESSION['csrf_token'] as $index => $csrf_token) {

			if ($csrf_token['token'] != $token) {
				continue;
			}

			$csrf = $this->buildInstance($csrf_token['token'], $csrf_token['time']);
			if ($csrf->checkToken($token)) {
				static::removeIndex($index);
				return true;
			}
		}
		return false;
	}

	/**
	 *
	 * @param string $token
	 * @param int $time
	 * @return CSRF
	 */
	protected function buildInstance(string $token = "", int $time = 0): CSRF
	{
	    return new CSRF($token, $time);
	}

	/**
	 *
	 * @param string $index
	 * @return boolean
	 */
	protected static function removeIndex(string $index): bool
	{
		array_splice($_SESSION['csrf_token'], $index, 1);
		return true;
	}

		/**
	 * Retourne un tableau de 5 tokens CSRF
	 *
	 * @return array
	 */
	public function getArrayTokens(): array
	{
		$tokens = [];
		for ($i = 0; $i < 5; $i++) {
			$tokens[] = $this->generateToken();
		}
		return $tokens;
	}
}

