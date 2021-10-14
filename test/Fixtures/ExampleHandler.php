<?php declare(strict_types=1);

namespace PMG\MigrateTactician\Test\Fixtures;

class ExampleHandler
{
    private ExampleCollaborator $collab;

    public function __construct(ExampleCollaborator $collab)
    {
        $this->collab = $collab;
    }

    public function handleCommandOne(CommandOne $command) : CommandOne
    {
        $this->collab->doThings();
        return $command;
    }

    /**
     * docblock here!
     */
    public function handleCommandTwo(CommandTwo $command) : CommandTwo
    {
        $this->privateMethod();

        return $command;
    }

    public function handleCommandThree(CommandThree $command) : void
    {
        // do stuff
    }

    private function privateMethod()
    {
        // noop
    }
}
