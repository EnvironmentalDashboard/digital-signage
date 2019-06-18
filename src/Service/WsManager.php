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
    protected $buttons;
    private $entityManager;

    // URLs look like this: /digital-signage/websockets/{slug}/{id}
    private const ROUTE_SLUG = 3;
    private const ROUTE_ID = 4;
    private const ROUTE_LEN = 5;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->displays = [];
        $this->buttons = [];
        $this->entityManager = $entityManager;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // http://socketo.me/api/class-Psr.Http.Message.RequestInterface.html
        $route = $conn->httpRequest->getUri()->getPath();
        $parts = explode('/', $route);
        if (count($parts) !== self::ROUTE_LEN) {
            throw new ResourceNotFoundException("Malformed route {$route}");
        }
        $slug = $parts[self::ROUTE_SLUG];
        // Store the new connection to send messages to later
        if ($slug === 'display') {
            $display_id = $parts[self::ROUTE_ID];
            if (isset($this->displays[$display_id])) {
                $this->displays[$display_id]->close();
            }
            $this->displays[$display_id] = $conn;
        } elseif ($slug !== 'remote-controller') {
            throw new ResourceNotFoundException("Route {$route} not found");
            $conn->close();
        }
        // $this->clients->attach($conn);
        // echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $button_id)
    {
        // Remote controllers will send what button was pressed to the appropriate display
        if (isset($this->buttons[$button_id])) {
            $button = $this->buttons[$button_id];
        } else {
            $buttonRepository = $this->entityManager->getRepository(Entity\Button::class);
            $button = $buttonRepository->find($button_id);
            if ($button === null) {
                throw new ResourceNotFoundException("Button {$button_id} not found");
            }
            $this->buttons[$button_id] = $button;
        }
        $display_id = $button->getOnDisplay()->getId();
        $to_trigger = $button->getTriggerFrame()->getId();
        if (!isset($this->displays[$display_id])) {
            throw new Exception("Display {$display_id} not connected; button {$button_id} failed to trigger frame {$to_trigger}");
        }
        $this->displays[$display_id]->send($to_trigger);
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $route = $conn->httpRequest->getUri()->getPath();
        $parts = explode('/', $route);
        $slug = $parts[self::ROUTE_SLUG];
        if ($slug === 'display') {
            $id = $parts[self::ROUTE_ID];
            unset($this->displays[$id]);
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
