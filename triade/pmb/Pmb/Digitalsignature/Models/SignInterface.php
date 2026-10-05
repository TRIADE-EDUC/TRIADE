<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SignInterface.php,v 1.4 2022/05/10 12:01:33 jparis Exp $
namespace Pmb\Digitalsignature\Models;

interface SignInterface
{

    public function sign($signature): void;

    public function check(): array;

    public function getMetadata(): array;

    public function save(): void;
}