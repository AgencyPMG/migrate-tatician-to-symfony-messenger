<?php declare(strict_types=1);

namespace PMG\MigrateTactician;

use Symfony\Component\Console\Application;
use App\Command\ExtractHandlersCommand;

class Cli extends Application
{
    const NAME = 'Tactician -> Messenger';

    public static function factory() : self
    {
        $cli = new self(self::NAME);

        return $cli;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands() : array
    {
        return array_merge(parent::getDefaultCommands(), [
            new ExtractHandlersCommand(),
        ]);
    }
}
