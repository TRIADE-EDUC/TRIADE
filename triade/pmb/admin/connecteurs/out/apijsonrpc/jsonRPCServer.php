<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: jsonRPCServer.php,v 1.15 2023/07/18 13:55:18 dbellamy Exp $

/*
					COPYRIGHT

Copyright 2007 Sergio Vaccaro <sergio@inservibile.org>

This file is part of JSON-RPC PHP.

JSON-RPC PHP is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

JSON-RPC PHP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with JSON-RPC PHP; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * This class build a json-RPC Server 1.0
 * http://json-rpc.org/wiki/specification
 *
 * @author sergio <jsonrpcphp@inservibile.org>
 * @author Erwan Martin <emartin@sigb.net>
 */


class jsonRPCServer {
	private static function return_function_list($object, $allowed_methods) {
		//Un peu de réflexivité et le tour est joué
		$rc = new ReflectionClass($object);
		$methods = $rc->getMethods(ReflectionMethod::IS_PUBLIC);
		$private_methods = array("copy_error", "set_error", "clear_error", "es_proxy");
		
		$result = array();
		$result["serviceType"] = "JSON-RPC";
		$result["serviceURL"] = "http://".$_SERVER["SERVER_NAME"]."/".$_SERVER["REQUEST_URI"];
		$result["methods"] = array();
		foreach ($methods as $amethod) {
			if (in_array($amethod->name, $private_methods))
				continue;
			if(!in_array($amethod->name, $allowed_methods))
				continue;
			$amethod_result = array();
			$amethod_result["name"] = $amethod->name;
			$parameters = $amethod->getParameters();
			$amethod_result["parameters"] = array();
			foreach ($parameters as $aparam) {
				$amethod_result["parameters"][] = array(
					"name" => $aparam->name
				);
			}
			$result["methods"][] = $amethod_result;
		}
		header("Content-Type: text/json-comment-filtered");
		echo json_encode($result);
		return true;
	}
	
	/**
	 * This function handle a request binding it to a given object
	 *
	 * @param object $object
	 * @return boolean
	 */
	public static function handle($object, $allowed_methods, $json_input) {

		// checks if a JSON-RCP request has been received
		if (
			!$json_input ||
			$_SERVER['REQUEST_METHOD'] != 'POST' || 
			empty($_SERVER['CONTENT_TYPE']) ||
			strpos($_SERVER['CONTENT_TYPE'], 'application/json') === FALSE
			) {
			// This is not a JSON-RPC request, we will then return the function list
				if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
					header('status: 200');
					return true;
				}
				
			return self::return_function_list($object, $allowed_methods);
		}
				
		// reads the input data
		$request = $json_input;

		// try to assign optional parameters to avoid parameter mismatch
		$tokens = explode('_', $request['method'], 2);
		$group = $tokens[0];
		$method = $tokens[1];
		$expected_params = [];
		$enabled_params = [];
		if( !empty($object->es->catalog->groups[$group]->methods[$method]->inputs) ) {
			$expected_params = $object->es->catalog->groups[$group]->methods[$method]->inputs;
		}
		if( !empty($expected_params) ) {
			foreach($expected_params as $k => $expected_param) {
				//si les parametres sont nommes
				if( array_key_exists($expected_param->name, $request['params']) ) {
					$enabled_params[$expected_param->name] = $request['params'][$expected_param->name];
				//sinon s'ils sont dans l'ordre
				} elseif (isset($request['params'][$k])) {
					$enabled_params[$expected_param->name] = $request['params'][$k];
				//sinon on prend la valeur par defaut
				} else {
					$enabled_params[$expected_param->name] =(($expected_param->default_value)?$expected_param->default_value:'');
				}
			}
			$request['params'] = $enabled_params;
		} 
		$request['params'] = is_null($request['params']) ? [] : $request['params'];
		
		unset($tokens);
		unset($group);
		unset($method);
		unset($expected_params);
		unset($enabled_params);
		
		// executes the task on local object
		try {
			
			$object->set_error_callback(function($e) {
				throw new Exception($e->getMessage());
			});
						
			$result = @call_user_func_array(array($object,$request['method']),$request['params']);
						
			if ($result !== FALSE) {
				
				$response = array (
									'id' => $request['id'],
									'result' => $result,
									'error' => NULL
									);
			} else {
				$response = array (
									'id' => $request['id'],
									'result' => NULL,
									'error' => 'unknown method or incorrect parameters'
									);
			}
		} catch (Exception $e) {
			$response = array (
								'id' => $request['id'],
								'result' => NULL,
								'error' => $e->getMessage()
								);
		}
		
		// output the response
		if (!empty($request['id'])) {
			header("Content-Type:application/json;charset=utf-8");
			echo json_encode($response);
		}
		
		// finish
		return true;
	}
}
