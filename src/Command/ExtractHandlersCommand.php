<?php declare(strict_types=1);

namespace PMG\MigrateTactician\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractHandlersCommand extends Command
{
    protected static $defaultName = 'extract-handlers';

    protected function configure() : void
    {
        $this->setDescription('pull handleCommandName methods out of a single class into multiple classes');
    }

    protected function execute(InputInterface $in, OutputInterface $out) : int
    {
        $out->writeln('hello, world');
        return 0;        
    }
}
