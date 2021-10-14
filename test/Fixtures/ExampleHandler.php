<?php declare(strict_types=1);

namespace PMG\MigrateTactician\Test\Fixtures;

class ExampleHandler
{
    private ExampleCollaborator $collab;

    public function __construct(ExampleCollaborator $collab)
    {
        $this->collab = $collab;
    }

    public function handleCommandOne(CommandOne $command)
    {
        $this->collab->doThings();
    }

    public function handleCommandTwo(CommandTwo $command)
    {
        $this->privateMethod();
    }

    private function privateMethod()
    {
        // noop
    }
}
