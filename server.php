#!/usr/bin/php

<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use SeChat\Chat;

function displayUsage() {}

if($argc > 0) {
	foreach($argv as $arg){
		$args = explode('=',$arg);
		switch($args[0]){
			case '--help':
				return displayUsage();
			case '--logfile':
				$log = $args[1];
				break;
		}
	}
}

//fork the process to work in a daemonized environment
file_put_contents($log, "Status: starting up.\n", FILE_APPEND);
$pid = pcntl_fork();

if($pid == -1){
	file_put_contents($log, "Error: could not daemonize process.\n", FILE_APPEND);
	return 1;
}
else if($pid){
	return 0;
}
else{
	require __DIR__ . '/vendor/autoload.php';
	require __DIR__ . '/classes/ChatApplication.php';
	
	$server = IoServer::factory(
		new HttpServer(
			new WsServer(
				new Chat()
			)
		),
		8000
	);
	
	$server->run();
}
