<?php

/*
 * @name Request.trait.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Request.namespace.php
 */
namespace Request\traits;
trait Request 
{
    private function __construct(\Headers\Headers $headers)
    {
        $this->headers = $headers;
    }
    private function isAjax()
    {
        return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
    }
    private function isNoCache()
    {
        return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma');
    }
    private function getPreferredLanguage(array $locales = null)
    {
        $preferredLanguages = $this->getLanguages();
        if(empty($locales))
        {
            if (isset($preferredLanguages[0])) 
            {
                return $preferredLanguages[0];
            } else {
                return null;
            }
        }
        if(!$preferredLanguages){
            return $locales[0];
        }
        $preferredLanguages = array_values( array_intersect($preferredLanguages, $locales) );
        
        if (isset($preferredLanguages[0]))
        {
            return $preferredLanguages[0];
        } else {
            return $locales[0];
        }
    }
    private static function noRobots()
    {
        header('X-Robots-Tag: noindex');
    }
    private static function initializeFormats() /* @protected */
    {
        static::$formats = [
            'html' => [
                    'text/html', 
                    'application/xhtml+xml'
            ],
            'txt'  => [
                    'text/plain'
            ],
            'js'   => [
                    'application/javascript', 
                    'application/x-javascript', 
                    'text/javascript'
            ],
            'css'  => [
                    'text/css'
            ],
            'json' => [
                    'application/json',
                    'application/x-json'
            ],
            'xml'  => [
                    'text/xml', 
                    'application/xml', 
                    'application/x-xml'
            ],
            'rdf'  => [
                    'application/rdf+xml'
            ],
            'atom' => [
                    'application/atom+xml'
            ],
            'rss'  => [
                    'application/rss+xml'
            ],
        ];
    }
    private function getMimeType($format, $index = 0, $full = false){
        if(static::$formats === null){
            static::initializeFormats();
        }
        $format = strtolower($format);
        if (isset(static::$formats[$format]))
        {
            $format = static::$formats[$format];
            if ($full === true)
            {
                return $format;
            } else {
                if (isset($format[$index]))
                {
                    return $format[$index];
                } else {
                    return $format[0];
                }
            }
        } else {
            return null;
        }
    }
    private function preferredLanguageIs($name, array $locales = null){
        if ($name === self::getPreferredLanguage($locales))
        {
            return true;
        } else {
            return false;
        }
    }
    private function getLanguages()
    {
        if(null !== $this->languages)
        {
            return $this->languages;
        }
        $languages = $this->splitHttpAcceptHeader($this->headers->get('Accept-Language'));
        $this->languages = [];
        foreach ($languages as $lang => $q)
        {
            if(strstr($lang,'-'))
            {
                $codes = explode('-',$lang);
                if($codes[0] === 'i')
                {
                    if(count($codes) > 1)
                    {
                        $lang = $codes[1];
                    }
                } else {
                    $i = 0;
                    while($i < count($codes)) 
                    {
                        if($i == 0)
                        {
                            $lang = strtolower($codes[0]);
                        } else {
                            $lang .= '_'.strtoupper($codes[$i]);
                        }
                        ++$i;
                    }
                }
            }
            $this->languages[] = $lang;
        }
        return $this->languages;
    }
    private function splitHttpAcceptHeader($h)
    {
        if (!$h || is_array($h) || is_object($h))
        {
            return [];
        }
        $values = [];
        foreach (array_filter(explode(',', $h)) as $value) 
        {
            if (preg_match('/;\s*(q=.*$)/', $value, $match))
            {
                $q = (float) substr(trim($match[1]), 2);
                $value = trim(substr($value, 0, -strlen($match[0])));
            } else {
                $q = 1;
            }	
            if (0<$q)
            {
                $values[trim($value)] = $q;
            }
        }
        arsort($values);
        reset($values);
        return $values;
    }	
}