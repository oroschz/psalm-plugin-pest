<?php

namespace Oroschz\PsalmPluginPest;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;

class Plugin implements PluginEntryPointInterface
{
    /** @return void */
    public function __invoke(RegistrationInterface $psalm, ?SimpleXMLElement $config = null): void
    {
        foreach ($this->getStubFiles() as $file) {
            $psalm->addStubFile($file);
        }

        class_exists(Hooks\ExpectCaseHandler::class, true);
        $psalm->registerHooksFromClass(Hooks\ExpectCaseHandler::class);
    }

    /** @return list<string> */
    private function getStubFiles(): array
    {
        return glob(__DIR__ . '/stubs/*.phpstub') ?: [];
    }
}
