<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFAServicesOrm.php,v 1.2 2023/07/06 14:57:03 jparis Exp $
namespace Pmb\MFA\Orm;

use Pmb\Common\Orm\Orm;

class MFAServicesOrm extends Orm
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "mfa_services";

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
	 * @var boolean
	 */
	protected $application = false;

	/**
	 *
	 * @var boolean
	 */
	protected $mail = false;

	/**
	 *
	 * @var boolean
	 */
	protected $sms = false;

	/**
	 *
	 * @var boolean
	 */
	protected $required = false;
	
	/**
	 *
	 * @var string
	 */
	protected $suggest_message = "";
	
// 	/**
// 	 *
// 	 * @var \ReflectionClass
// 	 */
// 	protected static $reflectionClass = null;
// 	protected static $relations = array();
}