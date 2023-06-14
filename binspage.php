<?php

require_once 'vendor/autoload.php';

use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class BinsWebSocket implements MessageComponentInterface
{
    protected $clients;
    protected $binIds;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        $this->binIds = array();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->sendBinIds($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // You can perform additional actions or validations here if needed
        $this->binIds = $this->fetchBinIdsFromDatabase();
        $this->sendBinIds($from);
        $this->broadcastBinIds();
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->broadcastBinIds();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    public function fetchBinIdsFromDatabase()
    {
        include 'conn.php';

        // Select bin IDs from the database
        $query = 'SELECT * FROM bins';
        $result = mysqli_query($con, $query);

        $binIds = array();

        // Fetch the results and store the bin IDs in an array
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $binIds[] = $row;
            }
        }

        return $binIds;
    }

    public function sendBinIds(ConnectionInterface $conn)
    {
        $conn->send(json_encode($this->binIds));
    }

    public function broadcastBinIds()
    {
        foreach ($this->clients as $client) {
            $this->sendBinIds($client);
        }
    }
}

$server = new \Ratchet\App('localhost', 8080, '0.0.0.0');
$server->route('/ws', new BinsWebSocket(), ['*']);
$server->run();
