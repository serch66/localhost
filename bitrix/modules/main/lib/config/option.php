<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2015 Bitrix
 */
namespace Bitrix\Main\Config;

use Bitrix\Main;

class Option
{
	const CACHE_DIR = "b_option";

	protected static $options = array();

	/**
	 * Returns a value of an option.
	 *
	 * @param string $moduleId The module ID.
	 * @param string $name The option name.
	 * @param string $default The default value to return, if a value doesn't exist.
	 * @param bool|string $siteId The site ID, if the option differs for sites.
	 * @return string
	 * @throws Main\ArgumentNullException
	 * @throws Main\ArgumentOutOfRangeException
	 */
	public static function get($moduleId, $name, $default = "", $siteId = false)
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");
		if ($name == '')
			throw new Main\ArgumentNullException("name");

		if (!isset(self::$options[$moduleId]))
		{
			static::load($moduleId);
		}

		if ($siteId === false)
		{
			$siteId = static::getDefaultSite();
		}

		$siteKey = ($siteId == ""? "-" : $siteId);

		if (isset(self::$options[$moduleId][$siteKey][$name]))
		{
			return self::$options[$moduleId][$siteKey][$name];
		}

		if (isset(self::$options[$moduleId]["-"][$name]))
		{
			return self::$options[$moduleId]["-"][$name];
		}

		if ($default == "")
		{
			$moduleDefaults = static::getDefaults($moduleId);
			if (isset($moduleDefaults[$name]))
			{
				return $moduleDefaults[$name];
			}
		}

		return $default;
	}

	/**
	 * Returns the real value of an option as it's written in a DB.
	 *
	 * @param string $moduleId The module ID.
	 * @param string $name The option name.
	 * @param bool|string $siteId The site ID.
	 * @return null|string
	 * @throws Main\ArgumentNullException
	 */
	public static function getRealValue($moduleId, $name, $siteId = false)
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");
		if ($name == '')
			throw new Main\ArgumentNullException("name");

		if (!isset(self::$options[$moduleId]))
		{
			static::load($moduleId);
		}

		if ($siteId === false)
		{
			$siteId = static::getDefaultSite();
		}

		$siteKey = ($siteId == ""? "-" : $siteId);

		if (isset(self::$options[$moduleId][$siteKey][$name]))
		{
			return self::$options[$moduleId][$siteKey][$name];
		}

		return null;
	}

	/**
	 * Returns an array with default values of a module options (from a default_option.php file).
	 *
	 * @param string $moduleId The module ID.
	 * @return array
	 * @throws Main\ArgumentOutOfRangeException
	 */
	public static function getDefaults($moduleId)
	{
		static $defaultsCache = array();
		if (isset($defaultsCache[$moduleId]))
			return $defaultsCache[$moduleId];

		if (preg_match("#[^a-zA-Z0-9._]#", $moduleId))
			throw new Main\ArgumentOutOfRangeException("moduleId");

		$path = Main\Loader::getLocal("modules/".$moduleId."/default_option.php");
		if ($path === false)
			return $defaultsCache[$moduleId] = array();

		include($path);

		$varName = str_replace(".", "_", $moduleId)."_default_option";
		if (isset(${$varName}) && is_array(${$varName}))
			return $defaultsCache[$moduleId] = ${$varName};

		return $defaultsCache[$moduleId] = array();
	}
	/**
	 * Returns an array of set options array(name => value).
	 *
	 * @param string $moduleId The module ID.
	 * @param bool|string $siteId The site ID, if the option differs for sites.
	 * @return array
	 * @throws Main\ArgumentNullException
	 */
	public static function getForModule($moduleId, $siteId = false)
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");

		if (!isset(self::$options[$moduleId]))
		{
			static::load($moduleId);
		}

		if ($siteId === false)
		{
			$siteId = static::getDefaultSite();
		}

		$result = self::$options[$moduleId]["-"];

		if($siteId <> "" && !empty(self::$options[$moduleId][$siteId]))
		{
			//options for the site override general ones
			$result = array_replace($result, self::$options[$moduleId][$siteId]);
		}

		return $result;
	}

	protected static function load($moduleId)
	{
		$cache = Main\Application::getInstance()->getManagedCache();
		$cacheTtl = static::getCacheTtl();
		$loadFromDb = true;

		if ($cacheTtl !== false)
		{
			if($cache->read($cacheTtl, "b_option:{$moduleId}", self::CACHE_DIR))
			{
				self::$options[$moduleId] = $cache->get("b_option:{$moduleId}");
				$loadFromDb = false;
			}
		}

		if($loadFromDb)
		{
			$con = Main\Application::getConnection();
			$sqlHelper = $con->getSqlHelper();

			self::$options[$moduleId] = ["-" => []];

			$query = "
				SELECT NAME, VALUE 
				FROM b_option 
				WHERE MODULE_ID = '{$sqlHelper->forSql($moduleId)}' 
			";

			$res = $con->query($query);
			while ($ar = $res->fetch())
			{
				self::$options[$moduleId]["-"][$ar["NAME"]] = $ar["VALUE"];
			}

			try
			{
				//b_option_site possibly doesn't exist

				$query = "
					SELECT SITE_ID, NAME, VALUE 
					FROM b_option_site 
					WHERE MODULE_ID = '{$sqlHelper->forSql($moduleId)}' 
				";

				$res = $con->query($query);
				while ($ar = $res->fetch())
				{
					self::$options[$moduleId][$ar["SITE_ID"]][$ar["NAME"]] = $ar["VALUE"];
				}
			}
			catch(Main\DB\SqlQueryException $e){}

			if($cacheTtl !== false)
			{
				$cache->set("b_option:{$moduleId}", self::$options[$moduleId]);
			}
		}

		/*ZDUyZmZMmFiYTZhZDkzYTNkZGNlOTQ0NDNmZjdiNzBiNzc3MTY=*/$GLOBALS['____404050595']= array(base64_decode('ZXhwbG9kZQ'.'=='),base64_decode(''.'cGFjaw=='),base64_decode('b'.'W'.'Q1'),base64_decode('Y29'.'uc3Rhb'.'n'.'Q='),base64_decode(''.'aGFzaF9obWFj'),base64_decode('c3RyY21w'),base64_decode('aXNfb'.'2J'.'qZWN0'),base64_decode('Y2FsbF91'.'c2V'.'y'.'X'.'2Z1bmM'.'='),base64_decode('Y2FsbF91c2'.'VyX2Z1bmM'.'='),base64_decode('Y2FsbF91c2V'.'y'.'X2Z1bmM'.'='),base64_decode('Y2Fs'.'bF91'.'c2VyX2Z'.'1bmM='),base64_decode('Y'.'2Fsb'.'F91c2VyX'.'2Z1bmM='));if(!function_exists(__NAMESPACE__.'\\___1281318403')){function ___1281318403($_338439514){static $_1929645528= false; if($_1929645528 == false) $_1929645528=array('LQ==','bWFp'.'bg==','b'.'WFpbg='.'=','LQ'.'==','bWFpb'.'g'.'==','flBBU'.'k'.'FN'.'X01BWF9VU0VSUw==','L'.'Q==',''.'bWFpbg==','flBBUkFNX01'.'BW'.'F9VU'.'0'.'VS'.'Uw==','Lg'.'==',''.'S'.'Co'.'=','Yml0'.'cml4','TElDRU5'.'T'.'RV9LR'.'Vk=','c2h'.'h'.'Mj'.'U2','LQ==','b'.'W'.'Fpb'.'g==','f'.'lBBUkFNX'.'0'.'1'.'B'.'W'.'F9VU0VS'.'Uw==',''.'L'.'Q==','b'.'WFpbg==','UEFSQ'.'U1fT'.'UFYX1VTR'.'V'.'JT','V'.'VNFUg==','VVNFUg'.'==','VV'.'NFU'.'g==','SXNBdXRob3JpemV'.'k','VVNF'.'Ug==','SXN'.'BZG1pbg='.'=','QVBQTElDQVRJT04=',''.'Um'.'VzdGF'.'yd'.'E'.'J1ZmZlcg==','TG'.'9'.'j'.'YWx'.'S'.'ZWR'.'pc'.'mVj'.'dA='.'=','L'.'2xp'.'Y2V'.'uc2'.'VfcmVz'.'dHJpY3Rpb24ucGhw','LQ='.'=','bWFpb'.'g==','f'.'lBBUk'.'FNX01BWF9VU'.'0VSU'.'w==',''.'LQ==','bWFpb'.'g==','U'.'EFSQ'.'U1f'.'TUFYX1VTRV'.'JT','XEJpd'.'HJp'.'eFxNYWluXENvb'.'mZpZ1'.'xPcHRpb246OnNldA==','bWFp'.'bg==','UEFSQ'.'U'.'1f'.'TUFYX1V'.'T'.'RVJT');return base64_decode($_1929645528[$_338439514]);}};if(isset(self::$options[___1281318403(0)][___1281318403(1)]) && $moduleId === ___1281318403(2)){ if(isset(self::$options[___1281318403(3)][___1281318403(4)][___1281318403(5)])){ $_1169568527= self::$options[___1281318403(6)][___1281318403(7)][___1281318403(8)]; list($_590391024, $_306150935)= $GLOBALS['____404050595'][0](___1281318403(9), $_1169568527); $_1690917707= $GLOBALS['____404050595'][1](___1281318403(10), $_590391024); $_1255991397= ___1281318403(11).$GLOBALS['____404050595'][2]($GLOBALS['____404050595'][3](___1281318403(12))); $_360205523= $GLOBALS['____404050595'][4](___1281318403(13), $_306150935, $_1255991397, true); self::$options[___1281318403(14)][___1281318403(15)][___1281318403(16)]= $_306150935; self::$options[___1281318403(17)][___1281318403(18)][___1281318403(19)]= $_306150935; if($GLOBALS['____404050595'][5]($_360205523, $_1690917707) !== min(110,0,36.666666666667)){ if(isset($GLOBALS[___1281318403(20)]) && $GLOBALS['____404050595'][6]($GLOBALS[___1281318403(21)]) && $GLOBALS['____404050595'][7](array($GLOBALS[___1281318403(22)], ___1281318403(23))) &&!$GLOBALS['____404050595'][8](array($GLOBALS[___1281318403(24)], ___1281318403(25)))){ $GLOBALS['____404050595'][9](array($GLOBALS[___1281318403(26)], ___1281318403(27))); $GLOBALS['____404050595'][10](___1281318403(28), ___1281318403(29), true);} return;}} else{ self::$options[___1281318403(30)][___1281318403(31)][___1281318403(32)]= round(0+6+6); self::$options[___1281318403(33)][___1281318403(34)][___1281318403(35)]= round(0+6+6); $GLOBALS['____404050595'][11](___1281318403(36), ___1281318403(37), ___1281318403(38), round(0+6+6)); return;}}/**/
	}

	/**
	 * Sets an option value and saves it into a DB. After saving the OnAfterSetOption event is triggered.
	 *
	 * @param string $moduleId The module ID.
	 * @param string $name The option name.
	 * @param string $value The option value.
	 * @param string $siteId The site ID, if the option depends on a site.
	 * @throws Main\ArgumentOutOfRangeException
	 */
	public static function set($moduleId, $name, $value = "", $siteId = "")
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");
		if ($name == '')
			throw new Main\ArgumentNullException("name");

		if ($siteId === false)
		{
			$siteId = static::getDefaultSite();
		}

		$con = Main\Application::getConnection();
		$sqlHelper = $con->getSqlHelper();

		$updateFields = [
			"VALUE" => $value,
		];

		if($siteId == "")
		{
			$insertFields = [
				"MODULE_ID" => $moduleId,
				"NAME" => $name,
				"VALUE" => $value,
			];

			$keyFields = ["MODULE_ID", "NAME"];

			$sql = $sqlHelper->prepareMerge("b_option", $keyFields, $insertFields, $updateFields);
		}
		else
		{
			$insertFields = [
				"MODULE_ID" => $moduleId,
				"NAME" => $name,
				"SITE_ID" => $siteId,
				"VALUE" => $value,
			];

			$keyFields = ["MODULE_ID", "NAME", "SITE_ID"];

			$sql = $sqlHelper->prepareMerge("b_option_site", $keyFields, $insertFields, $updateFields);
		}

		$con->queryExecute(current($sql));

		static::clearCache($moduleId);

		static::loadTriggers($moduleId);

		$event = new Main\Event(
			"main",
			"OnAfterSetOption_".$name,
			array("value" => $value)
		);
		$event->send();

		$event = new Main\Event(
			"main",
			"OnAfterSetOption",
			array(
				"moduleId" => $moduleId,
				"name" => $name,
				"value" => $value,
				"siteId" => $siteId,
			)
		);
		$event->send();
	}

	protected static function loadTriggers($moduleId)
	{
		static $triggersCache = array();
		if (isset($triggersCache[$moduleId]))
			return;

		if (preg_match("#[^a-zA-Z0-9._]#", $moduleId))
			throw new Main\ArgumentOutOfRangeException("moduleId");

		$triggersCache[$moduleId] = true;

		$path = Main\Loader::getLocal("modules/".$moduleId."/option_triggers.php");
		if ($path === false)
			return;

		include($path);
	}

	protected static function getCacheTtl()
	{
		static $cacheTtl = null;

		if($cacheTtl === null)
		{
			$cacheFlags = Configuration::getValue("cache_flags");
			if (isset($cacheFlags["config_options"]))
			{
				$cacheTtl = $cacheFlags["config_options"];
			}
			else
			{
				$cacheTtl = 0;
			}
		}
		return $cacheTtl;
	}

	/**
	 * Deletes options from a DB.
	 *
	 * @param string $moduleId The module ID.
	 * @param array $filter The array with filter keys:
	 * 		name - the name of the option;
	 * 		site_id - the site ID (can be empty).
	 * @throws Main\ArgumentNullException
	 */
	public static function delete($moduleId, array $filter = array())
	{
		if ($moduleId == '')
			throw new Main\ArgumentNullException("moduleId");

		$con = Main\Application::getConnection();
		$sqlHelper = $con->getSqlHelper();

		$deleteForSites = true;
		$sqlWhere = $sqlWhereSite = "";

		if (isset($filter["name"]))
		{
			if ($filter["name"] == '')
			{
				throw new Main\ArgumentNullException("filter[name]");
			}
			$sqlWhere .= " AND NAME = '{$sqlHelper->forSql($filter["name"])}'";
		}
		if (isset($filter["site_id"]))
		{
			if($filter["site_id"] <> "")
			{
				$sqlWhereSite = " AND SITE_ID = '{$sqlHelper->forSql($filter["site_id"], 2)}'";
			}
			else
			{
				$deleteForSites = false;
			}
		}
		if($moduleId == 'main')
		{
			$sqlWhere .= "
				AND NAME NOT LIKE '~%' 
				AND NAME NOT IN ('crc_code', 'admin_passwordh', 'server_uniq_id','PARAM_MAX_SITES', 'PARAM_MAX_USERS') 
			";
		}
		else
		{
			$sqlWhere .= " AND NAME <> '~bsm_stop_date'";
		}

		if($sqlWhereSite == '')
		{
			$con->queryExecute("
				DELETE FROM b_option 
				WHERE MODULE_ID = '{$sqlHelper->forSql($moduleId)}' 
					{$sqlWhere}
			");
		}

		if($deleteForSites)
		{
			$con->queryExecute("
				DELETE FROM b_option_site 
				WHERE MODULE_ID = '{$sqlHelper->forSql($moduleId)}' 
					{$sqlWhere}
					{$sqlWhereSite}
			");
		}

		static::clearCache($moduleId);
	}

	protected static function clearCache($moduleId)
	{
		unset(self::$options[$moduleId]);

		if (static::getCacheTtl() !== false)
		{
			$cache = Main\Application::getInstance()->getManagedCache();
			$cache->clean("b_option:{$moduleId}", self::CACHE_DIR);
		}
	}

	protected static function getDefaultSite()
	{
		static $defaultSite;

		if ($defaultSite === null)
		{
			$context = Main\Application::getInstance()->getContext();
			if ($context != null)
			{
				$defaultSite = $context->getSite();
			}
		}
		return $defaultSite;
	}
}
