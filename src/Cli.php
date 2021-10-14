<?php declare(strict_types=1);

namespace App;

use Symfony\Component\Console\Application;

class Cli extends Application
{
    const NAME = 'Tactician -> Messenger';

    public static function factory() : self
    {
        $cli = new self(self::NAME);

        return $cli;
    }
}
