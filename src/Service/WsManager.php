<?php
namespace App\Service;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use Doctrine\ORM\EntityManagerInterface;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use App\Entity;

use \Exception;

class WsManager implements MessageComponentInterface
{
    protected $displays;
    private $entityManager;

    // URLs look like this: /digital-signage/websockets/{slug}/{id}
    private const ROUTE_SLUG = 3;
    private const ROUTE_ID = 4;
    private const ROUTE_LEN = 5;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->displays = [];
        $this->entityManager = $entityManager;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // http://socketo.me/api/class-Psr.Http.Message.RequestInterface.html
        $route = $conn->httpRequest->getUri()->getPath();
        $parts = explode('/', $route);
        if (count($parts) !== self::ROUTE_LEN) {
            throw new ResourceNotFoundException(date('c') . ": Malformed route {$route}");
        }
        $slug = $parts[self::ROUTE_SLUG];
        // Store the new connection to send messages to later
        if ($slug === 'display') {
            $display_id = $parts[self::ROUTE_ID];
            if (!isset($this->displays[$display_id])) {
                $this->displays[$display_id] = [];
            }
            $this->displays[$display_id][] = $conn;
        } elseif ($slug !== 'remote-controller') {
            throw new ResourceNotFoundException(date('c') . ": Route {$route} not found");
            $conn->close();
        }
        // $this->clients->attach($conn);
        // echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $button_id)
    {
        // Remote controllers will send what button was pressed to the appropriate display
        $buttonRepository = $this->entityManager->getRepository(Entity\Button::class);
        $button = $buttonRepository->find($button_id);
        if ($button === null) {
            throw new ResourceNotFoundException(date('c') . ": Button {$button_id} not found");
        }
        $display_id = $button->getOnDisplay()->getId();
        if (!isset($this->displays[$display_id])) {
            throw new Exception(date('c') . ": Display {$display_id} not connected\n");
        }
        $type = $button->getType();
        if ($type === Entity\Button::TRIGGER_FRAME) {
            $to_trigger = $button->getTriggerFrame()->getId();
            $response = json_encode([
                'type' => $type,
                'trigger' => $to_trigger
            ]);
        } elseif ($type === Entity\Button::PLAY) {
            $response = json_encode(['type' => $type]);
        } elseif ($type === Entity\Button::TRIGGER_URL) {
            $to_trigger = $button->getTriggerUrl();
            $response = json_encode([
                'type' => $type,
                'trigger' => $to_trigger
            ]);
        } else {
            throw new Exception(date('c') . "Unknown button type {$type}\n");
        }
        foreach ($this->displays[$display_id] as $display) {
            $display->send($response);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $route = $conn->httpRequest->getUri()->getPath();
        $parts = explode('/', $route);
        $slug = $parts[self::ROUTE_SLUG];
        if ($slug === 'display') {
            $id = $parts[self::ROUTE_ID];
            for ($i = 0; $i < count($this->displays[$id]); $i++) { 
                if ($conn->resourceId === $this->displays[$id][$i]->resourceId) {
                    unset($this->displays[$id][$i]);
                }
            }
        }
        $conn->close();
        // echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo __FILE__ . ":{$e->getLine()}\n";
        echo $e->getMessage();
        $conn->close();
    }
}
