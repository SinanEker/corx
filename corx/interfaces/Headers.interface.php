<?php

/*
 * @name Headers.interface.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Headers.namespace.php
 */
namespace Headers\interfaces;
interface Headers {
    function __construct(array $headers = []);
    function __toString();
    function all();
    function keys();
    function replace(array $headers = []);
    function add(array $headers);
    function get($key, $default = null, $first = true);
    function set($key, $values, $replace = true);
    function has($key);
    function contains($key, $value);
    function remove($key);
    function getDate($key, \DateTime $default = null);
    function addCacheControlDirective($key, $value = true);
    function hasCacheControlDirective($key);
    function getCacheControlDirective($key);
    function removeCacheControlDirective($key);
}