<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFASmsOrm.php,v 1.2 2023/06/22 14:23:09 jparis Exp $
namespace Pmb\MFA\Orm;

use Pmb\Common\Orm\Orm;

class MFASmsOrm extends Orm
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "mfa_sms";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id";
	
	/**
	 *
	 * @var integer
	 */
	protected $id = 0;

	/**
	 *
	 * @var string
	 */
	protected $context = "";

	/**
	 *
	 * @var string
	 */
	protected $content = "";
	
}