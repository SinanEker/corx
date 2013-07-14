<?php

/*
 * @name Request.interface.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Request.namespace.php
 */
namespace Request\interfaces;
interface Request {
    function __construct(\Headers\Headers $headers);
    function isAjax();
    function isNoCache();
    function getPreferredLanguage(array $locales = null);
    function getMimeType($format, $index = 0, $full = false);
    function preferredLanguageIs($name, array $locales = null);
    function getLanguages();
    function splitHttpAcceptHeader($h);
}