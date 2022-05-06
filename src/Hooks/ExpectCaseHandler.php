<?php

namespace Oroschz\PsalmPluginPest\Hooks;

use Psalm\Issue\InternalMethod;
use Psalm\Plugin\EventHandler\AfterFunctionCallAnalysisInterface;
use Psalm\Plugin\EventHandler\BeforeAddIssueInterface;
use Psalm\Plugin\EventHandler\Event\AfterFunctionCallAnalysisEvent;
use Psalm\Plugin\EventHandler\Event\BeforeAddIssueEvent;

use function str_starts_with;

class ExpectCaseHandler implements AfterFunctionCallAnalysisInterface, BeforeAddIssueInterface
{
    /**
     * Determines the return type when calling the expect function
     *
     * {@inheritDoc}
     */
    public static function afterFunctionCallAnalysis(AfterFunctionCallAnalysisEvent $event): void
    {
        $expression = $event->getExpr();
        if ($event->getFunctionId() !== "expect") {
            return;
        }

        $typeToDiscard = "Pest\Expectation";
        if ($expression->getArgs()) {
            $typeToDiscard = "Pest\Support\Extendable";
        }

        $event->getReturnTypeCandidate()->removeType($typeToDiscard);
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
