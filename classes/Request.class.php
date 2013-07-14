<?php

/*
 * @name Request.class.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Request.namespace.php
 *      Request.interface.php
 *      Request.trait.php
 */
namespace Request;
class Request implements \Request\interfaces\Request 
{
    /*
     * @protected
     */
    protected $headers;
    
    /*
     * @protected
     */
    protected $languages;
    
    /*
     * @protected static
     * @default null
     */
    protected static $formats = null;
    
    use \Request\traits\Request 
    {
        /*
         * @param [\Headers\Headers $headers]
         */
        __construct as public;
        
        /*
         * @param void
         */
        isAjax as public;
        
        /*
         * @param void
         */
        isNoCache as public;
        
        /*
         * @param [array $locales = null]
         */
        getPreferredLanguage as public;
        
        /*
         * @param [string / mixed $format]
         */
        getMimeType as public;
        
        /*
         * @param [mixed $name, array $locales = null]
         */
        preferredLanguageIs as public;
        
        /*
         * @param [mixed $h]
         */
        splitHttpAcceptHeader as public;
        
        /*
         * @param void
         */
        noRobots as public;
        
        /*
         * @param void
         */
        initializeFormats as protected;
    }
}