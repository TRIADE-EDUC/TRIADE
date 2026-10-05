<?php
/***********************************************************************************************
 Ce fichier fait partie du projet voxilabPHP, implémentation PHP de
l'API Voxilab (https://github.com/voxilab)

Cette  implémentation développée par © 2013- PMB Services.

Ce programme est régi par la licence CeCILL soumise au droit français et
respectant les principes de diffusion des logiciels libres. Vous pouvez
utiliser, modifier et/ou redistribuer ce programme sous les conditions
de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA
sur le site "http://www.cecill.info".

En contrepartie de l'accessibilité au code source et des droits de copie,
de modification et de redistribution accordés par cette licence, il n'est
offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
seule une responsabilité restreinte pèse sur l'auteur du programme,  le
titulaire des droits patrimoniaux et les concédants successifs.

A cet égard  l'attention de l'utilisateur est attirée sur les risques
associés au chargement,  à l'utilisation,  à la modification et/ou au
développement et à la reproduction du logiciel par l'utilisateur étant
donné sa spécificité de logiciel libre, qui peut le rendre complexe à
manipuler et qui le réserve donc à des développeurs et des professionnels
avertis possédant  des  connaissances  informatiques approfondies.  Les
utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
logiciel à leurs besoins dans des conditions permettant d'assurer la
sécurité de leurs systèmes et ou de leurs données et, plus généralement,
à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.

Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
pris connaissance de la licence CeCILL, et que vous en avez accepté les
termes.
***********************************************************************************************/

/*
 * Interface to implement a new protocol for diarization
*
* $Id: voxilabProtocol.class.php,v 1.1 2014/01/10 15:46:42 apetithomme Exp $
*/

if (stristr ($_SERVER['REQUEST_URI'], ".class.php"))
	die ("no access");

interface voxilabProtocol
{
	/**
	 * @param array options Array of protocol options
	 */
	public function __construct($options);
	
	/**
	 * Post a file
	 * 
	 * @param string path Path of the file to post
	 * 
	 * @return string Json string
	 */
	public function postFile($path);
	
	/**
	 * Execute a command
	 * 
	 * @param int id File identifier
	 * 
	 * @param string command Command to execute
	 * 
	 * @return string Json string
	 */
	public function command($id, $command);
	
	/**
	 * Initialize the protocol
	 */
	public function initProtocol();
}
?>