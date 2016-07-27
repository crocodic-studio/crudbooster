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

use PhpParser\NodeVisitorAbstract;

/**
 * This is the abstract node visitor class.
 *
 * This is used to track the filename.
 */
abstract class AbstractNodeVisitor extends NodeVisitorAbstract
{
    /**
     * The current file being parsed.
     *
     * @var string
     */
    protected $filename = '';

    /**
     * Set the full path to the current file being parsed.
     *
     * @param string $filename
     *
     * @return \ClassPreloader\Parser\AbstractNodeVisitor
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the full path to the current file being parsed.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the directory of the current file being parsed.
     *
     * @return string
     */
    public function getDir()
    {
        return dirname($this->getFilename());
    }
}
