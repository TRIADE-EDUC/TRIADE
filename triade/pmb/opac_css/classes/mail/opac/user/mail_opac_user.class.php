<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_user.class.php,v 1.3 2023/11/09 14:12:48 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

abstract class mail_opac_user extends mail_opac
{

    protected function get_mail_to_name()
    {
        $query = "SELECT nom, prenom FROM users WHERE userid = " . $this->mail_to_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $user = pmb_mysql_fetch_object($result);
            return trim($user->prenom . " " . $user->nom);
        }
        return '';
    }

    protected function get_mail_to_mail()
    {
        $query = "SELECT user_email, user_email_recipient FROM users WHERE userid = " . $this->mail_to_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $user = pmb_mysql_fetch_object($result);
            if (! empty($user->user_email_recipient)) {
                return $user->user_email_recipient;
            }
            return $user->user_email;
        }
        return '';
    }
}