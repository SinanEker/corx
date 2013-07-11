<?php

/*
 * @name Headers.class.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Headers.namespace.php
 *      Headers.interface.php
 *      Headers.trait.php
 */
namespace Headers;
class Headers implements \Headers\interfaces\Headers 
{
    /*
     * @protected
     */
    protected $headers;

    /*
     * @protected
     */
    protected $cacheControl;
    
    use \Headers\traits\Headers 
    {
        /*
         * @param [array $headers = []]
         */
        __construct as public;
        
        /*
         * @param void
         */
        __toString as public;
        
        /*
         * @param void
         */
        all as public;
        
        /*
         * @param void
         */
        keys as public;
        
        /*
         * @param [array $headers = []]
         */
        replace as public;
        
        /*
         * @param [array $headers]
         */
        add as public;
        
        /*
         * @param [mixed $key, mixed $default = null, bool $first = true]
         */
        get as public;
        
        /*
         * @param [mixed $key, mixed $values, bool $replace = true]
         */
        set as public;
        
        /*
         * @param [mixed $key]
         */
        has as public;
        
        /*
         * @param [mixed $key, mixed $value]
         */
        contains as public;
        
        /*
         * @param [mixed $key]
         */
        remove as public;
        
        /*
         * @param [mixed $key, DateTime $default = null]
         */
        getDate as public;
        
        /*
         * @param [mixed $key, bool $value = true]
         */
        addCacheControlDirective as public;
        
        /*
         * @param [mixed $key]
         */
        hasCacheControlDirective as public;
        
        /*
         * @param [mixed $key]
         */
        getCacheControlDirective as public;
        
        /*
         * @param [mixed $key]
         */
        removeCacheControlDirective as public;
        
        /* @protected */
        
        /*
         * @param void
         */
        getCacheControlHeader as protected;
        
        /*
         * @param [string $header]
         */
        parseCacheControl as protected;
    }
}