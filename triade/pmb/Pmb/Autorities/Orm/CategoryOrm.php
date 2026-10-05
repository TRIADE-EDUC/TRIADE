<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CategoryOrm.php,v 1.2 2021/03/26 08:51:56 qvarin Exp $

namespace Pmb\Autorities\Orm;

use Pmb\Common\Orm\Orm;

class CategoryOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "categories";
    
    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "num_noeud";
    
    /**
     * @var array
     */
    public static $primaryKeyAditional = [
        "langue"
    ];
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
    
    /**
     *
     * @var integer
     */
    protected $num_thesaurus = 0;
    
    /**
     *
     * @var integer
     */
    protected $num_noeud = 0;
    
    /**
     *
     * @var string
     */
    protected $langue = "";
    
    /**
     *
     * @var string
     */
    protected $libelle_categorie = "";
    
    /**
     *
     * @var string
     */
    protected $note_application = "";
    
    /**
     *
     * @var string
     */
    protected $comment_public = "";
    
    /**
     *
     * @var string
     */
    protected $comment_voir = "";
    
    /**
     *
     * @var string
     */
    protected $index_categorie = "";
    
    /**
     *
     * @var string
     */
    protected $path_word_categ = "";
    
    /**
     *
     * @var string
     */
    protected $index_path_word_categ = "";
}