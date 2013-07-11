<?php

/*
 * @name Gzip.interface.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Gzip.namespace.php
 */
namespace Gzip\interfaces;
interface Gzip {

    /*
     * @param [\Request\Request $request]
     */
    function __construct(\Request\Request $request);
    
    /*
     * @param [string $method, mixed $args]
     */
    function __call($method, $args);
    
    /*
     * @param [String $type, const $charset = DEFAULT_GZIP_CHARSET]
     */
    function _gzip(\String $type, $charset = DEFAULT_GZIP_CHARSET);
}