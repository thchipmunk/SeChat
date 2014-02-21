<?php

namespace SeChat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use stdClass;

class Chat implements MessageComponentInterface {
	protected $clients = array();
	protected $connections;
	
	public function __construct() {
		$this->connections = new \SplObjectStorage;
	}
	
	public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->connections->attach($conn);

        #echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {		
		$data = json_decode($msg);
		
		#echo printf("Received message %s\n", $msg);
		
		switch($data->status) {
			case 'system':
			case 'connection':
				$this->switchSystemMessage($from, $data);
				break;
			case 'client':
				$this->switchUserMessage($from, $data);
				break;
		}
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->connections->detach($conn);

        #echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        #echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
	
	private function switchUserMessage(ConnectionInterface $from, $msg) {
		/*
		{
			"status":"client",
			"from_user":"7116ca86b82abf8293a46a797a2e4a6c",
			"to_user":"289a546193d5917e4c56901567418dba",
			"message":"dwwbb9Uxj/Ty8vm5F8KYYIKw3b5FfdEwJRvyujUkYZwHxR+A9JV9JdDDMzVtgb6Me4ZzTSLqogP1mVAa7YX+o/HRNihnV5XEkmlDNd5LAZogEsLD495htnJ9wlVe0gCamFMokUBszr1172762brCCZ9mF0Annxq+Ux8o8E9bDTU=?6IjMGx4cKVK0z8F6DJx4KQylywmjBgaOoP9K9ESi0KE="
		}
		*/
		
		if (!isset($this->clients[$msg->to_user])) { return; }
		
		$conn = $this->clients[$msg->to_user]["connection"];
		
		foreach ($this->connections as $connection) {
            if ($conn === $connection) {
				#echo printf("Sending message from %s to %s.\n", $msg->from_user, $msg->to_user);
                // The sender is not the receiver, send to each client connected
                $connection->send(json_encode($msg));
            }
        }
	}
	
	private function switchSystemMessage(ConnectionInterface $from, $msg) {
		/*
		{
			"status":"connection",
			"user":"289a546193d5917e4c56901567418dba",
			"connect_user":"7116ca86b82abf8293a46a797a2e4a6c",
			"public_key":"zWM4n1hGwV39XIh+PPHMvBq7Y/l0OdFCOBNQZMa7KTeRIVcoeackSFK7YhQVv6heh2cQ4p5mF6TmYD2FFx4IQit5B+DwiIpHURtnhm5bxXkIbkRc2My7fj/3a2ZX2vnEZPoqjGpBsISEFmCTg3KRrjviFNJzq23d1FTJWpOAUOc="
		}
		*/
		
		$this->clients[$msg->user] = array(
			"public_key" => $msg->public_key,
			"connection" => $from
		);
		
		//Send system information from UserA to UserB
		$return = new stdClass();
		
		$return->status = "system";
		$return->user = $msg->user;
		$return->connect_user = $msg->connect_user;
		$return->public_key = $msg->public_key;
		
		if (isset($this->clients[$msg->connect_user])) {
			$conn = $this->clients[$msg->connect_user]["connection"];
		} else {
			return;
		}
		
		foreach ($this->connections as $connection) {
            if ($conn === $connection) {
				#echo printf("Sending message from %s to %s.\n", "system", $msg->user);
                $connection->send(json_encode($return));
            }
        }
		
		//Send system information from UserB to UserA
		$return = new stdClass();
		
		$return->status = "system";
		$return->user = $msg->connect_user;
		$return->connect_user = $msg->user;
		$return->public_key = $this->clients[$msg->connect_user]["public_key"];
		
		if (isset($this->clients[$msg->user])) {
			$conn = $this->clients[$msg->user]["connection"];
		} else {
			return;
		}
		
		foreach ($this->connections as $connection) {
            if ($conn === $connection) {
				#echo printf("Sending message from %s to %s.\n", "system", $msg->user);
                $connection->send(json_encode($return));
            }
        }
	}
}