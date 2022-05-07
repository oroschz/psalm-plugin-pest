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

        if (!$stmt->expr instanceof \PhpParser\Node\Expr\FuncCall) {
            return null;
        }

        $second = $stmt->expr->args[1];

        if (!$second instanceof \PhpParser\Node\Arg) {
            return null;
        }

        $value = $second->value;

        if (!$value instanceof \PhpParser\Node\Expr\Closure) {
            return null;
        }

        $doc_block = new \PhpParser\Node\Stmt\Nop();
        $doc_block->setDocComment(new Doc("/** @psalm-scope-this PHPUnit\Framework\TestCase */"));
        $value->stmts = [$doc_block, ...$value->stmts];

        return  null;
    }
}
