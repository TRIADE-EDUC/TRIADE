<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailtplModel.php,v 1.5 2023/05/04 09:36:38 gneveu Exp $
namespace Pmb\Common\Models;

use Pmb\Common\Orm\MailtplOrm;

class MailtplModel extends Model
{

    protected $ormName = "\Pmb\Common\Orm\MailtplOrm";

    public $mailtplName;

    public $mailtplObjet;

    public $mailtplTpl;

    public $mailtplUsers;

    public $idMailtpl;

    public static function getMailtplList()
    {
        $mailtplOrmList = MailtplOrm::findAll();

        foreach ($mailtplOrmList as $key => $mailtpl) {
            $mt = new MailtplModel($mailtpl->id_mailtpl);
            $mailtplOrmList[$key] = $mt;
        }

        return self::toArray($mailtplOrmList);
    }

    public static function getMailtpl(int $id)
    {
        $mailtpl = new MailtplModel($id);
        return $mailtpl;
    }

    public static function getSelVars()
    {
        return [];
    }
}