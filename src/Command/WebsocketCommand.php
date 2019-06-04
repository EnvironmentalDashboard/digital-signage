<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Ratchet\App;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use App\Service\WsManager;

class WebsocketCommand extends Command
{
    // the name of the command (the part after "bin/console")
        protected static $defaultName = 'ws-server:start';
        private $manager;

        public function __construct(WsManager $manager) {
            $this->manager = $manager;
            parent::__construct();
        }

    protected function configure()
    {
            $this
                // the short description shown while running "php bin/console list"
                ->setDescription('Starts websocket server.')

                // the full command description shown when running the command with
                // the "--help" option
                ->setHelp('This command allows you to start the websocket server...')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
            $output->writeln('Starting...');
            // $app = IoServer::factory(new HttpServer(new WsServer(new WsManager())), 8080);
            $app = new App("localhost", 8080, '0.0.0.0');
            $app->route('/remote-controller/{id}', $this->manager, ['*']);
            $app->route('/display/{id}', $this->manager, ['*']);
            $app->run();

    }
}