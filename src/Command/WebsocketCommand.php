<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Service\Websocket;
use Ratchet\Server\IoServer;

class WebsocketCommand extends Command
{
    // the name of the command (the part after "bin/console")
		protected static $defaultName = 'ws-server:start';
		private $ws;

		public function __construct(Websocket $ws) {
			$this->ws = $ws;
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
			$server = IoServer::factory(
					new Websocket(),
					8080
			);
			$server->run();
    }
}