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
        } elseif (
            $stmt->expr instanceof \PhpParser\Node\Expr\MethodCall
            && $stmt->expr->var instanceof \PhpParser\Node\Expr\FuncCall
        ) {
            $case = $stmt->expr->var;
        } else {
            return null;
        }

        $name = $case->name->getAttribute("resolvedName");

        if (($name === "test" || $name === "it") && count($case->args) === 2) {
            $callback = $case->args[1];
        } elseif (($name === "beforeEach" || $name === "afterEach") && count($case->args) == 1) {
            $callback = $case->args[0];
        } else {
            return null;
        }

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
