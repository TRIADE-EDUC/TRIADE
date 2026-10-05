<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CRUD.php,v 1.2 2022/10/13 12:41:32 qvarin Exp $

namespace Pmb\DSI\Models;

interface CRUD
{
	public function create();
	
	public function read();
	
	public function update();

	public function delete();
}

