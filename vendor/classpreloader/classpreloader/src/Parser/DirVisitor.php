<?php

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClassPreloader\Parser;

use ClassPreloader\Exception\SkipFileException;
use PhpParser\Node;
use PhpParser\Node\Scalar\MagicConst\Dir as DirNode;
use PhpParser\Node\Scalar\String_ as StringNode;

/**
 * This is the directory node visitor class.
 *
 * This is used to replace all references to __DIR__ with the actual directory.
 */
class DirVisitor extends AbstractNodeVisitor
{
    /**
     * Should we skip the file if it contains a dir constant?
     *
     * @var bool
     */
    protected $skip = false;

    /**
     * Create a new directory visitor.
     *
     * @param bool $skip
     *
     * @return void
     */
    public function __construct($skip = false)
    {
        $this->skip = $skip;
    }

    /**
     * Enter and modify the node.
     *
     * @param \PhpParser\Node $node
     *
     * @return void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof DirNode) {
            if ($this->skip) {
                throw new SkipFileException('__DIR__ constant found, skipping...');
            }

            return new StringNode($this->getDir());
        }
    }
}
