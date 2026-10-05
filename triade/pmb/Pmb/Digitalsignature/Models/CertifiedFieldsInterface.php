<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CertifiedFieldsInterface.php,v 1.2 2022/05/03 07:20:47 gneveu Exp $
namespace Pmb\Digitalsignature\Models;

interface CertifiedFieldsInterface
{

    public function getFields($signatureFields): string;
}