<?php
if(!defined('INITIALIZED'))
	exit;

$time_start = microtime(true);
session_start();

function autoLoadClass($className)
{
	if(!class_exists($className))
		if(file_exists('./classes/' . strtolower($className) . '.php'))
			include_once('./classes/' . strtolower($className) . '.php');
		else
			new Error_Critic('#E-7', 'Cannot load class <b>' . $className . '</b>, file <b>./classes/class.' . strtolower($className) . '.php</b> doesn\'t exist');
}
spl_autoload_register('autoLoadClass');

//load acc. maker config to $config['site']
$config = array();
include('./config/config.php');
//load server config $config['server']
if(Website::getWebsiteConfig()->getValue('useServerConfigCache'))
{
	// use cache to make website load faster
	if(Website::fileExists('./config/server.config.php'))
	{
		$tmp_php_config = new ConfigPHP('./config/server.config.php');
		$config['server'] = $tmp_php_config->getConfig();
	}
	else
	{
		// if file isn't cached we should load .lua file and make .php cache
		if(is_file(Website::getWebsiteConfig()->getValue('serverPath') . 'config.lua')):
			$tmp_lua_config = new ConfigLUA(Website::getWebsiteConfig()->getValue('serverPath') . 'config.lua');
		else:
			$tmp_lua_config = new ConfigLUA(Website::getWebsiteConfig()->getValue('serverPath') . 'config.lua.dist');
		endif;

		$config['server'] = $tmp_lua_config->getConfig();
		$tmp_php_config = new ConfigPHP();
		$tmp_php_config->setConfig($tmp_lua_config->getConfig());
		$tmp_php_config->saveToFile('./config/server.config.php');
	}
}
else
{
	$tmp_lua_config = new ConfigLUA(Website::getWebsiteConfig()->getValue('serverPath') . 'config.lua');
	$config['server'] = $tmp_lua_config->getConfig();
}
