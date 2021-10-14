<?php declare(strict_types=1);

namespace PMG\MigrateTactician;

final class Handler
{
    private string $className;
    private ?string $targetNamespace;
    private ?string $targetDirectory;
    private bool $strictTypes;

    public function __construct(
        string $className,
        ?string $targetNamespace=null,
        ?string $targetDirectory=null,
        bool $strictTypes=true
    ) {
        $this->className = $className;
        $this->targetNamespace = $targetNamespace;
        $this->targetDirectory = $targetDirectory;
        $this->strictTypes = $strictTypes;
    }

    public function getClassName() : string
    {
        return $this->className;
    }

    public function getTargetNamespace() : ?string
    {
        return $this->targetNamespace;
    }

    public function getTargetDirectory() : ?string
    {
        return $this->targetDirectory;
    }

    public function withStrictTypes() : bool
    {
        return $this->strictTypes;
    }
}
