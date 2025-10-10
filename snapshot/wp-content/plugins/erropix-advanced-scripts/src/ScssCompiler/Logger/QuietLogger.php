<?php

/**
 * SCSSPHP
 *
 * @copyright 2012-2020 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://scssphp.github.io/scssphp
 */

namespace ERROPiX\AdvancedScripts\ScssCompiler\Logger;

/**
 * A logger that silently ignores all messages.
 *
 * @final
 */
class QuietLogger implements LoggerInterface
{
    public function warn($message, $deprecation = false)
    {
    }

    public function debug($message)
    {
    }
}
