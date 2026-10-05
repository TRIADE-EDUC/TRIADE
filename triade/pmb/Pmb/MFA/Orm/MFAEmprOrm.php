<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFAEmprOrm.php,v 1.1 2023/06/21 07:47:57 jparis Exp $
namespace Pmb\MFA\Orm;

use Pmb\Common\Orm\Orm;

class MFAEmprOrm extends Orm
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "mfa_empr";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "num_empr";

	/**
	 *
	 * @var integer
	 */
	protected $num_empr = 0;
	
	/**
	 *
	 * @var string
	 */
	protected $favorite = "mail";
	
	/**
	 *
	 * @var string
	 */
	protected $secret_code = "";
	
}