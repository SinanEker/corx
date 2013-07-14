<?php

/*
 * @name Datatype.trait.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Datatype.namespace.php
 */
namespace Datatype\traits;
trait Datatype 
{
    private function __construct($value) 
    {
        $this->value = $value;
    }
    private function getValue()
    {
        return $this->value;
    }
}