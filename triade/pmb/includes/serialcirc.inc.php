<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.inc.php,v 1.2 2012/02/01 10:47:43 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


define("SERIALCIRC_EMPR_TYPE_empr",0);
define("SERIALCIRC_EMPR_TYPE_group",1);

define("SERIALCIRC_TYPE_rotative",0);
define("SERIALCIRC_TYPE_star",1);

define("SERIALCIRC_EXPL_STATE_CIRC_pending",0);
define("SERIALCIRC_EXPL_STATE_CIRC_inprogress",1);
define("SERIALCIRC_EXPL_STATE_CIRC_back",2);
define("SERIALCIRC_EXPL_STATE_CIRC_finished",3);

define("SERIALCIRC_EXPL_RET_asked",1);
define("SERIALCIRC_EXPL_RET_accepted",2);

define("SERIALCIRC_EXPL_TRANS_asked",1);
define("SERIALCIRC_EXPL_TRANS_accepted",2);

define("SERIALCIRC_EXPL_TRANS_DOC_ask",1);
define("SERIALCIRC_EXPL_TRANS_DOC_asked",2);
define("SERIALCIRC_EXPL_TRANS_DOC_accepted",3);