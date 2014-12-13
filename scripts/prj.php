<?php

define('PRJ_ROOT_DIR', 'D:\Progr\WebAMP\public_html\\');
define('PRJ_DOMAIN_PREFIX', '.sv');

define('EDITOR_PATH', 'C:\Program Files (x86)\phpDesigner 8\phpDesigner.exe');
define('NOTEPAD_PATH', 'C:\Program Files (x86)\Notepad++\notepad++.exe');
define('WIN_HOSTS_PATH', 'C:\Windows\system32\drivers\etc\hosts');
define('MYSQL_CONF_PATH', 'D:\Progr\Servers\WebAMP\MySQL Server 5.1\my.ini');
define('VHOSTS_CONF_PATH', 'D:\Progr\WebAMP\Apache2.2\conf\extra\httpd-vhosts.conf');

function callback($cmd)
{
	echo $cmd.PHP_EOL;
	die(1);
}

function call($cmd)
{
	echo $cmd.PHP_EOL;
	exec($cmd);
	die(0);
}

function parseArguments()
{
	global $argv;
	if (!isset($argv[1]) OR !trim($argv[1])) return false;
	return explode(' ',$argv[1]);
}

function createProjectPath($prjArgument, $domainPrefix=null) 
{
	$prj = $prjArgument;
	$prj = str_replace('/', '\\', $prj);
	$prj = explode('\\', $prj);
	$prjDomain = $prj[0].( $domainPrefix ? $domainPrefix : '' );
	$prjPath = PRJ_ROOT_DIR.$prjDomain.'\www\\';
	if (count($prj)>1) {
		$tmpPrj = $prj;
		array_shift($tmpPrj);
		$prjPath.=implode('\\',$tmpPrj).'\\';
	}
	return $prjPath;
}

function getProjectPath($prjArgument) {
	$ret = false;
	
	if ( ($prjPath=createProjectPath($prjArgument, PRJ_DOMAIN_PREFIX) AND is_dir($prjPath))    	// Keep in mind that we  need
		 OR																				  		// the same order to check paths
		 ($prjPath=createProjectPath($prjArgument) AND is_dir($prjPath))						// with domains prefix first
	) {
		$ret = $prjPath;
	} 
				
	return $ret;
}

if ($arg = parseArguments() AND is_array($arg))
{
	switch ($arg[0]) 
	{
		default:
			echo 'Undefined command: '.$arg[0];
		break;
		case 'open':
			if (!isset($arg[1])) {
				echo 'ERR: Project not specified. Run `prj ls` to view project list';
			} else {
				if ($prjPath = getProjectPath($arg[1])) {
					call('explorer.exe '.$prjPath);
				} else {
					echo 'ERR: Project with name "'.$arg[1].'" not found';
				}
			}
		break;
		case 'cd':
			if (!isset($arg[1])) {
				echo 'ERR: Project not specified. Run `prj ls` to view project list';
			} else {
				if ($prjPath = getProjectPath($arg[1])) {
					callback('cd /D "'.$prjPath.'"');
				} else {
					echo 'ERR: Project with name "'.$arg[1].'" not found';
				}
			}		
		break;
		case 'ls':
			$cmd = 'dir "'.PRJ_ROOT_DIR.'"';
			callback($cmd);
		break;				
		case 'help':
			echo 'Available commands:'.PHP_EOL.PHP_EOL;
			echo '-    prj ls [project name] = show projects list'.PHP_EOL.PHP_EOL;
			echo '-    prj cd [project name] = change dir in console to project dir'.PHP_EOL.PHP_EOL;
			echo '-    prj open [project name] = open project folder in explorer'.PHP_EOL.PHP_EOL;
			echo '-    prj edit [hosts|vhosts|mysql] = open specified config in editor'.PHP_EOL.PHP_EOL;
			echo '-    prj find [keyword] (!google[!php|!mysql]) = open browser with search page for specified keyword. You can also specify search site google, php or mysql.'.PHP_EOL.PHP_EOL;			
			echo '-    prj help = show commands list'.PHP_EOL.PHP_EOL;
			echo '-    prj ver = show version'.PHP_EOL.PHP_EOL;
		break;			
		case 'ver':
			echo 'Project manager, v.2.0'.PHP_EOL;
			echo 'Author: Vitaliy Orlov, orlov0562@gmail.com'.PHP_EOL;
		break;
		case 'editor':
			callback(EDITOR_PATH);
		break;
		case 'edit':
			if (!isset($arg[1])) {
				echo 'ERR: Config type not specified';
			} else {
				switch($arg[1]) {
					default:
						echo 'Undefined config';
					break;
					case 'hosts': 
						callback('"'.NOTEPAD_PATH.'" "'.WIN_HOSTS_PATH.'"');
					break;
					case 'mysql': 
						callback('"'.NOTEPAD_PATH.'" "'.MYSQL_CONF_PATH.'"');
					break;
					case 'vhosts': 
						callback('"'.NOTEPAD_PATH.'" "'.VHOSTS_CONF_PATH.'"');
					break;
				}
			}
		break;
		case 'find':
			if (!isset($arg[1])) {
				echo 'ERR: Search keyword not found';
			} else {
				$searchEngine = $arg[count($arg)-1];
				if (!in_array($searchEngine,array('php!','google!','mysql!','!php','!google','!mysql'))) {
					$searchEngine = 'google!';
				} else {
					$searchEngine = $arg[count($arg)-1];
					unset($arg[count($arg)-1]);
				}
				
				$query = implode(' ',array_slice($arg, 1));

				switch($searchEngine) {
					default:
					case '!google':
					case 'google!':
						$url = 'https://www.google.com/search?ie=utf-8&oe=utf-8&q='.urlencode($query);
					break;
					case '!php':
					case 'php!':
						$url = 'http://php.net/search.php?show=quickref&pattern='.urlencode($query);
					break;
					case '!mysql':
					case 'mysql!':
						$url = 'https://search.oracle.com/search/search?group=MySQL&q='.urlencode($query);
					break;
				}
				
				call('start '.escapeshellcmd($url));
			}			
		break;
		
		case 'md5':
			if (!isset($arg[1])) {
				echo 'ERR: keyword not found';
			} else {
				echo md5(trim($arg[1])).PHP_EOL;
			}			
		break;

		case 'ip':
			echo 'EXTERNAL IP: '.file_get_contents('http://phihag.de/ip/').PHP_EOL;
		break;
	}
} else {
	echo 'ERR: No arguments given';
}