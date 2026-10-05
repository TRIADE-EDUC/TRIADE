<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OpacListeLectureOrm.php,v 1.1.2.2 2024/06/20 10:02:43 jparis Exp $

namespace Pmb\Common\Orm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class OpacListeLectureOrm extends Orm
{
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "opac_liste_lecture";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_liste";

    protected $id_liste = 0;

    protected $nom_liste = "";

    protected $description = "";

    protected $public = 0;

    protected $num_empr = 0;

    protected $read_only = 0;

    protected $confidential = 0;

    protected $tag = "";

    protected $allow_add_records = 0;

    protected $allow_remove_records = 0;

    /**
     * Permet de verifier si l'utilisateur a acces a la liste
     *
     * @param integer $id_liste
     * @param integer $id_empr
     * @return boolean
     */
    public static function has_access(int $id_liste, int $id_empr): bool
    {
        $query = 'SELECT 1 FROM opac_liste_lecture LEFT JOIN abo_liste_lecture ON abo_liste_lecture.num_liste = opac_liste_lecture.id_liste';
        $query .= ' WHERE id_liste = '. $id_liste .' AND opac_liste_lecture.num_empr = ' . $id_empr;
        $query .= ' OR id_liste = '. $id_liste .' AND abo_liste_lecture.num_empr = ' . $id_empr;

        $result = pmb_mysql_query($query);
        return pmb_mysql_num_rows($result) === 1;
    }
}
