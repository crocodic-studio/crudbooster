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

namespace ClassPreloader;

/**
 * This is the class node class.
 *
 * This class contains a value, and the previous/next pointers.
 */
class ClassNode
{
    /**
     * The next node pointer.
     *
     * @var \ClassPreloader\ClassNode|null
     */
    public $next;

    /**
     * The previous node pointer.
     *
     * @var \ClassPreloader\ClassNode|null
     */
    public $prev;

    /**
     * The value of the class node.
     *
     * @var mixed
     */
    public $value;

    /**
     * Create a new class node.
     *
     * @param mixed                          $value
     * @param \ClassPreloader\ClassNode|null $prev
     *
     * @return void
     */
    public function __construct($value = null, $prev = null)
    {
        $this->value = $value;
        $this->prev = $prev;
    }
}
