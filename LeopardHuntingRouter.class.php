<?php
/**
 * Leopard Hunting Router
 *
 * @version 0.1.0-alpha
 * @copyright ©Leopard
 * @license http://creativecommons.org/licenses/by-nd/4.0/ CC BY-ND 4.0
 *
 * @author Julian Pfeil
 */
namespace Leopard;

/**
 * Routing-class
 */ 
class HuntingRouter
{
	/**
	 * Returns current URI
	 * 
	 * @api
	 *
	 * @return string $URI Current URI
	 */ 
	public static function getCurrentURI()
	{
		$BasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		$URI = substr($_SERVER['REQUEST_URI'], strlen($BasePath));
		if (strstr($URI, '?')) {
			$URI = substr($URI, 0, strrpos($URI, '?'));
		}
		$URI = '/'.trim($URI, '/');
		
		return $URI;
	}
	
	/**
	 * Description describing the function
	 * 
	 * @api
	 *
	 * @param string $URI URI as string
	 *
	 * @return array $URIArray URI as array
	 */ 
	public static function convertURIIntoArray($URI)
	{
		$URIArray = [];
		foreach(explode('/', substr($URI, 1)) as $ParamClause) {
			if (strpos($ParamClause, '=')) {
				$ParamClause = explode('=', $ParamClause);
				$URIArray[$ParamClause[0]] = $ParamClause[1];
			}
		}
		
		return $URIArray;
	}
	
	/**
	 * Uses URIArray as GET-Parameters
	 * 
	 * @api
	 *
	 * @param array $URIArray URI as array
	 *
	 * @return void
	 */
	public static function putParamsIntoGet(array $URIArray)
	{
		$_GET = $URIArray;
		$_REQUEST = array_merge ($_GET, $_POST, $_COOKIE);
		
		return;
	}
	
	/**
	 * Description describing the function
	 * 
	 * @api
	 *
	 * @param array $URIArray URI as array
	 * @param array $Parameters Array with data how to check parameters
	 *
	 * @return boolean true on success, false on failure
	 */ 
	public static function checkParameters($Parameters, $URIArray)
	{
		foreach ($Parameters as $Parameter => $Conditions) {
			if (isset($Conditions['pattern'])) {
				if(!preg_match($Conditions['pattern'], $URIArray[$Parameter])) {
					return false;
				}
			} 
			if ($Conditions['only'] == 'integer' || $Conditions['only'] == 'int') {
				if(!ctype_digit($URIArray[$Parameter])) {
					return false;
				} 
			} elseif ($Conditions['only'] == 'boolean' || $Conditions['only'] == 'bool') {
			if($URIArray[$Parameter] != '1' && $URIArray[$Parameter] != '0' && $URIArray[$Parameter] != 'true' && $URIArray[$Parameter] != 'false') {
					return false;
				}
			} elseif ($Conditions['only'] == 'alphanumeric') {
				if(!preg_match('/^[A-Z0-9]+$/i', $URIArray[$Parameter])) {
					return false;
				}
			}elseif ($Conditions['only'] == 'alpha') {
				if(!preg_match('/^[A-Z]+$/i', $URIArray[$Parameter])) {
					return false;
				}
			}
			return true;
		}
	}
	
	/**
	 * Starts routing automatically
	 * 
	 * @api
	 *
	 * @param array $Parameters Array with data how to check parameters if want to
	 *
	 * @uses self::checkParameters() Checks parameters
	 *
	 * @return boolean true on success, false on failure
	 */ 
	public static function startRouting(array $Parameters = [])
	{
		$URI = self::getCurrentUri();
		$URIArray = self::convertURIIntoArray($URI);
		if (count($Parameters) > 0) {
			if (!self::checkParameters($Parameters, $URIArray)) {
				return false;
			}
		}
		self::putParamsIntoGet($URIArray);
		
		return true;
	}
	
	/**
	 * Returns current path absolute or relative
	 * 
	 * @api
	 *
	 * @param boolean $Relativity If path absolute or relative
	 *
	 * @return string $Path Current absolute or relative path
	 */ 
	public static function getCurrentPath($Relativity = false)
	{
		if (!$Relativity) {
			$Path = str_replace('\\', '/', realpath(__DIR__));
		} else {
			$Path = substr(str_replace('\\', '/', realpath(__DIR__)), strlen(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])))).'/';
		}
		
		return $Path;
	}
	
	/**
	 * Returns current url
	 * 
	 * @api
	 *
	 * @return string $URL Current URL
	 */
	public static function getURL()
	{
		$URL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')?'https':'http').'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, -9);
		
		return $URL;
	}
}
