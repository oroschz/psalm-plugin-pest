<?php

namespace Oroschz\PsalmPluginPest\Hooks;

use PhpParser\Comment\Doc;
use Psalm\Plugin\EventHandler\BeforeStatementAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\BeforeStatementAnalysisEvent;

class AssertCaseHandler implements BeforeStatementAnalysisInterface
{

    public static function beforeStatementAnalysis(BeforeStatementAnalysisEvent $event): ?bool
    {
        $stmt = $event->getStmt();

        if (!$stmt instanceof \PhpParser\Node\Stmt\Expression) {
            return null;
        }

        if ($stmt->expr instanceof \PhpParser\Node\Expr\FuncCall) {
            $case = $stmt->expr;
        } else if (
            $stmt->expr instanceof \PhpParser\Node\Expr\MethodCall
            && $stmt->expr->var instanceof \PhpParser\Node\Expr\FuncCall
        ) {
            $case = $stmt->expr->var;
        } else {
            return null;
        }

        $name = $case->name->getAttribute("resolvedName");
        if ($name !== "test" && $name !== "it") {
            return null;
        }

        if (count($case->args) < 2) {
            return null;
        }

        $callback = $case->args[1];

        if (!$callback instanceof \PhpParser\Node\Arg) {
            return null;
        }

        $value = $callback->value;

        if (!$value instanceof \PhpParser\Node\Expr\Closure) {
            return null;
        }

        $doc_block = new \PhpParser\Node\Stmt\Nop();
        $doc_block->setDocComment(new Doc("/** @psalm-scope-this PHPUnit\Framework\TestCase */"));
        $value->stmts = [$doc_block, ...$value->stmts];

        return  null;
    }
}
