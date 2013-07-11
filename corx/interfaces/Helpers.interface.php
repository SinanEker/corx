<?php

/*
 * @name Helpers.interface.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Helpers.namespace.php
 */
namespace Helpers\interfaces;
interface Helpers {
    static function noArgsException($count = 0);
    static function varDump();
    static function conatins($haystack, $needle);
    static function getMainNamespaceName($namespace = __NAMESPACE__);
    static function getAllArrayValuesExcept($key, array $array);
    static function appendArray();
    static function shiftKeyUp(array $array);
    static function exceptionToJson(\Exception $e);
}