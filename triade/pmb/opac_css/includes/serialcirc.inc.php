<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.inc.php,v 1.1 2011/11/28 14:18:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

define("SERIALCIRC_TYPE_rotative",0);
define("SERIALCIRC_TYPE_etoile",1);

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

define("SERIALCIRC_COPY_STATUT_ask",0);
define("SERIALCIRC_COPY_STATUT_accepted",1);
define("SERIALCIRC_COPY_STATUT_refused",2);