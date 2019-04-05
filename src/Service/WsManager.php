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
        $slug = $parts[1];
        // Store the new connection to send messages to later
        if ($slug === 'display') {
            $display_id = $parts[2];
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
        // Remote controllers will send what button was pressed to the appriopriate display
        if (isset($buttons[$button_id])) {
            $button = $buttons[$button_id];
        } else {
            $buttonRepository = $this->entityManager->getRepository(Entity\Button::class);
            $button = $buttonRepository->find($button_id);
            if ($button === null) {
                throw new ResourceNotFoundException("Button {$button_id} not found");
            }
            $buttons[$button_id] = $button;
        }
        $display_id = $button->getOnDisplay()->getId();
        $to_trigger = $button->getTriggerFrame()->getId();
        $this->displays[$display_id]->send($to_trigger);
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $route = $conn->httpRequest->getUri()->getPath();
        $parts = explode('/', $route);
        $slug = $parts[1];
        if ($slug === 'display') {
            $id = $parts[2];
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
