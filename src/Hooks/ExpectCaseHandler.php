<?php

namespace Oroschz\PsalmPluginPest\Hooks;

use Psalm\Issue\InternalMethod;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Plugin\EventHandler\BeforeAddIssueInterface;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\BeforeAddIssueEvent;
use Psalm\Type\Union;
use Psalm\Type\Atomic;

use function str_starts_with;

class ExpectCaseHandler implements FunctionReturnTypeProviderInterface, BeforeAddIssueInterface
{
    /**
     * @return array<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return ["expect"];
    }

    /**
     * Determines the return type when calling the expect function
     * @return ?Union
     */
    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        if (!$event->getCallArgs()) {
            return new Union([
                new Atomic\TNamedObject(\Pest\Support\Extendable::class)
            ]);
        }

        return new Union([
            new Atomic\TNamedObject(\Pest\Expectation::class)
        ]);
    }

    /**
     * Suppresses Psalm's InternalMethod issue
     *
     * {@inheritDoc}
     */
    public static function beforeAddIssue(BeforeAddIssueEvent $event): ?bool
    {
        $issue = $event->getIssue();
        if (!$issue instanceof InternalMethod) {
            return null;
        }

        $isExpectation = str_starts_with($issue->method_id, "pest\\expectation::");
        if (!$isExpectation) {
            return null;
        }

        return false;
    }
}
