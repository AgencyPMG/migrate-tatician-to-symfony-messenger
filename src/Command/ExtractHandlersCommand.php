<?php declare(strict_types=1);

namespace PMG\MigrateTactician\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PMG\MigrateTactician\Handler;
use PMG\MigrateTactician\HandlerRefactor;

class ExtractHandlersCommand extends Command
{
    protected static $defaultName = 'extract-handlers';

    private HandlerRefactor $handlers;

    public function __construct(HandlerRefactor $handlers)
    {
        parent::__construct();
        $this->handlers = $handlers;
    }

    protected function configure() : void
    {
        $this->setDescription('pull handleCommandName methods out of a single class into multiple classes');
        $this->addArgument(
            'handlerClass',
            InputArgument::REQUIRED,
            'the handler class to refactor'
        );
        $this->addOption(
            'namespace',
            null,
            InputOption::VALUE_REQUIRED,
            'the target namespace, defaults to `{originalHandlerNamespace}\\Handler`'
        );
        $this->addOption(
            'directory',
            null,
            InputOption::VALUE_REQUIRED,
            sprintf('the target direcdtory, defaults to `{originalHandlerDirectory}%sHandler`', DIRECTORY_SEPARATOR)
        );
        $this->addOption(
            'no-strict',
            null,
            InputOption::VALUE_NONE,
            'set this to avoid declaring strict types on the file',
        );
    }

    protected function execute(InputInterface $in, OutputInterface $out) : int
    {
        $files = $this->handlers->refactor(new Handler(
            $in->getArgument('handlerClass'),
            $in->getOption('namespace'),
            $in->getOption('directory'),
            $in->getOption('no-strict') ? false : true
        ));

        foreach ($files as $file) {
            echo $file->generate();
        }

        return 0;        
    }
}
