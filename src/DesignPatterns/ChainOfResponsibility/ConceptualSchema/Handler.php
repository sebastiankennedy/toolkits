<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\DesignPatterns\ChainOfResponsibility\ConceptualSchema;

interface Handler
{
    public function setNext(Handler $handle): Handler;

    /**
     * @param mixed $next
     */
    public function handle($next): void;
}
