<?php

/*
 * @name Gzip.class.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Gzip.namespace.php
 *      Gzip.interface.php
 *      Gzip.trait.php
 */
namespace Gzip;
class Gzip implements \Gzip\interfaces\Gzip 
{
    /*
     * @protected
     */
    protected $request = null;
    
    use \Gzip\traits\Gzip 
    {
        /*
         * @param [\Request\Request $request]
         * @return void
         */
        __construct as public;
        
		/*
         * @param void
		 * @static
		 * @return bool ob_start retuning value
		 */
        gzipped as public;
        
		/*
		 * @param void
		 * @static
		 * @return string expires value
		 */
        expires as public;
        
		/*
		 * @param [string $method, mixed $args]
         * @return mixed called function retuning value
		 */
        __call as public;
        
		/*
         * @name gzip
         * @param [\String $type, const $charset = DEFAULT_GZIP_CHARSET]
         * @return array A array that contains the mime type and the current headers
		 */
        _gzip as public;
    }
}