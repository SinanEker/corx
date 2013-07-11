<?php

/*
 * @name Datatype.class.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Datatype.namespace.php
 *      Datatype.interface.php
 *      Datatype.trait.php
 */
namespace Datatype;
class Datatype implements \Datatype\interfaces\Datatype 
{
    use \Datatype\traits\Datatype 
    {
        __construct as public;
        getValue as public;
    }
}