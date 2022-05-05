<?php

namespace Oroschz\PsalmPluginPest\Hooks;

use Psalm\Plugin\EventHandler\AfterFunctionCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterFunctionCallAnalysisEvent;

class ExpectCaseHandler implements AfterFunctionCallAnalysisInterface
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
}
