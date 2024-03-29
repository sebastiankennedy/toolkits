<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\DesignPatterns\ChainOfResponsibility\ConceptualSchema;

abstract class AbstractHandler implements Handler
{
    private ?Handler $nextHandler;

    public function setNext(Handler $handle): Handler
    {
        $this->nextHandler = $handle;

        return $handle;
    }

    public function handle($next): void
    {
        if ($this->nextHandler) {
            $this->nextHandler->handle($next);
        }
    }
}
