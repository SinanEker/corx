<?php

/*
 * @name View.class.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      View.namespace.php
 *      View.interface.php
 *      View.trait.php
 */
namespace View;
class View implements \View\interfaces\View 
{
    use \View\traits\View 
    {
        __construct as public;
    }
}