<?php
namespace Phidias\Documentation;

use Phidias\Json\Document;
use Phidias\Json\Schema;
use Phidias\Api\Server\Module;

class Controller
{
    /* documentation index file (relative to module's root) */
    private static $documentationIndex = "/documentation/index.json";

    public function main()
    {
        /* Find all loaded modules */
        $documentedModules = [];

        foreach ( Module::getLoadedModules() as $module ) {
            if (is_file($module."/".self::$documentationIndex)) {
                $documentedModules[] = Document::parse($module."/".self::$documentationIndex);
            }
        }

        return $documentedModules;
    }
}