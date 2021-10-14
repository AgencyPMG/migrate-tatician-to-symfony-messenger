<?php declare(strict_types=1);

namespace PMG\MigrateTactician;

use LogicException;
use ReflectionClass;
use Laminas\Code\Reflection\ClassReflection;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\FileGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Generator\AbstractMemberGenerator;

class HandlerRefactor
{
    /**
     * @return FileGenerator[]
     */
    public function refactor(Handler $handler) : array
    {
        $class = new ClassReflection($handler->getClassName());
        $handlerPrototype = ClassGenerator::fromReflection($class);

        $baseClass = $this->generateBaseClass($class, clone $handlerPrototype, $handler);
        $classes = [
            $baseClass,
        ];
        foreach ($handlerPrototype->getMethods() as $method) {
            if ($this->isHandleMethod($method->getName())) {
                $hc = $classes[] = $this->generateHandlerClass(clone $method, $baseClass, $handler);
                echo $hc->generate();
            }
        }

        $targetDirectory = $handler->getTargetDirectory() ?? (dirname($class->getFileName()).DIRECTORY_SEPARATOR.'Handler');

        return array_map(
            fn (ClassGenerator $cg) : FileGenerator => FileGenerator::fromArray([
                'classes' => [$cg],
                'filename' => $targetDirectory.DIRECTORY_SEPARATOR.$cg->getName().'.php',
                'declares' => $handler->withStrictTypes() ? ['strict_types' => 1] : [],
            ]),
            $classes
        );
    }

    /**
     * For the base class the general gist is:
     * 1. make all private properties, contants, and methods `protected`
     * 2. remove any public `handleXXX` methods
     * 3. make the class abstract
     */
    private function generateBaseClass(ReflectionClass $class, ClassGenerator $target, Handler $handler) : ClassGenerator
    {
        foreach ($target->getProperties() as $property) {
            if ($this->isPrivate($property)) {
                $property->setVisibility(AbstractMemberGenerator::VISIBILITY_PROTECTED);
            }
        }
        foreach ($target->getConstants() as $constant) {
            if ($this->isPrivate($constant)) {
                $constant->setVisibility(AbstractGenerator::VISIBILITY_PROTECTED);
            }
        }
        foreach ($target->getMethods() as $method) {
            if ($this->isHandleMethod($method->getName())) {
                $target->removeMethod($method->getName());
            } elseif ($this->isPrivate($method)) {
                $method->setVisibility(AbstractMemberGenerator::VISIBILITY_PROTECTED);
            }
        }

        $target->setNamespaceName($handler->getTargetNamespace() ?? ($target->getNamespaceName().'\\Handler'));
        $target->setAbstract(true);

        return $target;
    }

    private function generateHandlerClass(MethodGenerator $method, ClassGenerator $parent, Handler $handler) : ClassGenerator
    {
        $parameters = $method->getParameters();
        if (count($parameters) !== 1) {
            throw new LogicException(sprintf(
                'handler methods should only have a single argument, %s has %d arguments',
                $method->getName(),
                count($parameters)
            ));
        }

        $param = array_pop($parameters);
        if ($param->getType() === 'object') {
            throw new LogicException(sprintf(
                '%s is a generic command handler that accepts and object and cannot be refactored',
                $method->getName()
            ));
        }

        $typeParts = explode('\\', $param->getType());
        $commandName = array_pop($typeParts);

        $bcMethod = $this->generateBackCompatMethod($method);
        $generator = new ClassGenerator(
            $commandName.'Handler',
            $parent->getNamespaceName()
        );
        $generator
            ->setExtendedClass($parent->getNamespaceName().'\\'.$parent->getName())
            ->addMethodFromGenerator($method->setName('__invoke'))
            ->addMethodFromGenerator($bcMethod)
            ;

        return $generator;
    }

    private function generateBackCompatMethod(MethodGenerator $method) : MethodGenerator
    {
        $bc = new MethodGenerator($method->getName(), $method->getParameters());
        $bc->setVisibility(MethodGenerator::VISIBILITY_PUBLIC);
        $bc->setReturnType($method->getReturnType());

        $invokeParams = [];
        foreach ($method->getParameters() as $param) {
            $invokeParams[] = '$'.$param->getName();
        }
        $invokeCall = sprintf('$this->__invoke(%s);', implode(', ', $invokeParams));
        $bc->setBody('void' === (string) $method->getReturnType() ? $invokeCall : ('return '.$invokeCall));

        return $bc;
    }

    private function isPrivate(AbstractMemberGenerator $member) : bool
    {
        return $member->getVisibility() === AbstractMemberGenerator::VISIBILITY_PRIVATE;
    }

    private function isHandleMethod(string $methodName) : bool
    {
        return 'handle' === strtolower(substr($methodName, 0, 6));
    }
}
