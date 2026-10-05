<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ItemOrm.php,v 1.10 2023/04/18 14:42:12 rtigero Exp $
namespace Pmb\DSI\Orm;

use Pmb\Common\Orm\Orm;

class ItemOrm extends Orm
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public static $tableName = "dsi_item";

	/**
	 * Primary Key
	 *
	 * @var string
	 */
	public static $idTableName = "id_item";

	/**
	 *
	 * @var integer
	 */
	protected $id_item = 0;
	
	/**
	 *
	 * @var string
	 */
	protected $name = "";
	
	/**
	 *
	 * @var boolean
	 */
	protected $model = false;

	/**
	 *
	 * @var string
	 */
	protected $settings = "";

	/**
	 *
	 * @var integer
	 */
	protected $type = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_model = 0;

	/**
	 *
	 * @var integer
	 */
	protected $num_parent = 0;

    public function checkBeforeDelete()
    {
        $fields["num_parent"] = [
            'value' =>  $this->id_item,
            'operator' => '='
        ];
        $result = $this::finds($fields);
        foreach($result as $child) {
            $child->delete();
        }

        return true;
    }

    /**
	 *
	 * @var \ReflectionClass
	 */
	protected static $reflectionClass = null;
	
	protected static $relations = array();
}