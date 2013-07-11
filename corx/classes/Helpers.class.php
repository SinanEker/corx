<?php

/*
 * @name Helpers.class.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Helpers.namespace.php
 *      Helpers.interface.php
 *      Helpers.trait.php
 */
namespace Helpers;
abstract class Helpers implements \Helpers\interfaces\Helpers 
{
    use \Helpers\traits\Helpers 
    {
        noArgsException as public;
        varDump as public;
        contains as public;
        getMainNamespaceName as public;
        getAllArrayValuesExcept as public;
        appendArray as public;
        exceptionToJson as public;
    }
}