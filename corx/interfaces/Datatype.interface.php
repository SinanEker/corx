<?php

/*
 * @name Datatype.interface.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Datatype.namespace.php
 */
namespace Datatype\interfaces;
interface Datatype {
    function __construct($value);
    function getValue();
}