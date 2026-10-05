<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CSRF.php,v 1.4.2.1 2024/10/15 09:04:37 gneveu Exp $
namespace Pmb\Common\Library\CSRF;

use Pmb\Common\Helper\Request;

class CSRF
{

	/**
	 *
	 * @var integer
	 */
	protected const SIZE = 10;

	/**
	 *
	 * @var string
	 */
	protected const SALT = "-- todo change this value --";

	/**
	 * Durée de vie du token CSRF en seconde
	 *
	 * @var integer
	 */
	protected const TOKEN_LIFE_TIME = 3600;

	/**
	 *
	 * @var string
	 */
	protected $token = "";

	/**
	 *
	 * @var int
	 */
	protected $time = 0;

	/**
	 *
	 * @param string $token
	 * @param int $time
	 */
	public function __construct(string $token = "", int $time = 0)
	{
		$this->token = $token;
		$this->time = $time;

		if (! empty($this->token) && $this->expireToken()) {
			$this->deleteToken();
		}
	}

	/**
	 *
	 * @return string
	 */
	public function generateToken(): string
	{
		$this->deleteToken();
		$this->token = sha1($this->generateValue());
		$this->time = time();
		return $this->getToken();
	}

	/**
	 *
	 * @param string $token
	 * @return bool
	 */
	public function checkToken(string $token): bool
	{
		if (isset($this->token) && ($token == $this->token) && ! $this->expireToken()) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return string
	 */
	protected function generateValue(): string
	{
		return bin2hex(openssl_random_pseudo_bytes(self::SIZE)) . self::SALT;
	}

	/**
	 *
	 * @return string
	 */
	public function getToken(): string
	{
		return $this->token;
	}

	/**
	 *
	 * @return int
	 */
	public function getTime(): int
	{
		return $this->time;
	}

	/**
	 *
	 * @return bool
	 */
	public function expireToken(): bool
	{
		if (isset($this->time) && (time() - $this->time) <= self::TOKEN_LIFE_TIME) {
			return false;
		}
		return true;
	}

	/**
	 *
	 * @return bool
	 */
	protected function deleteToken(): bool
	{
		$this->token = "";
		$this->time = 0;
		return true;
	}
}

