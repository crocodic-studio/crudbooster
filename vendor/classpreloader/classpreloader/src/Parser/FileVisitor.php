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
use PhpParser\Node\Scalar\MagicConst\File as FileNode;
use PhpParser\Node\Scalar\String_ as StringNode;

/**
 * This is the file node visitor class.
 *
 * This is used to replace all references to __FILE__ with the actual file.
 */
class FileVisitor extends AbstractNodeVisitor
{
    /**
     * Should we skip the file if it contains a file constant?
     *
     * @var bool
     */
    protected $skip = false;

    /**
     * Create a new file visitor.
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
        if ($node instanceof FileNode) {
            if ($this->skip) {
                throw new SkipFileException('__FILE__ constant found, skipping...');
            }

            return new StringNode($this->getFilename());
        }
    }
}
