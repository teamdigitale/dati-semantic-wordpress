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

namespace ERROPiX\AdvancedScripts\ScssCompiler\Compiler;

/**
 * Compiler environment
 *
 * @author Anthon Pang <anthon.pang@gmail.com>
 *
 * @internal
 */
class Environment
{
    /**
     * @var \ERROPiX\AdvancedScripts\ScssCompiler\Block|null
     */
    public $block;

    /**
     * @var \ERROPiX\AdvancedScripts\ScssCompiler\Compiler\Environment|null
     */
    public $parent;

    /**
     * @var Environment|null
     */
    public $declarationScopeParent;

    /**
     * @var Environment|null
     */
    public $parentStore;

    /**
     * @var array|null
     */
    public $selectors;

    /**
     * @var string|null
     */
    public $marker;

    /**
     * @var array
     */
    public $store;

    /**
     * @var array
     */
    public $storeUnreduced;

    /**
     * @var int
     */
    public $depth;
}
