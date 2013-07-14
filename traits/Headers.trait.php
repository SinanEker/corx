<?php

/*
 * @name Headers.trait.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Headers.namespace.php
 */
namespace Headers\traits;
trait Headers 
{
    private function __construct(array $headers = [])
    {
        if (count($headers) === 0)
        {
            $headers = getallheaders();
        }
        $this->cacheControl = [];
        $this->headers = [];
        foreach ($headers as $key => $values)
        {
            $this->set($key, $values);
        }
    }
    public function __toString()
    {
        if (!$this->headers)
        {
	            return '';
        }
        $beautifier = function ($name) 
        {
            return preg_replace_callback('/\-(.)/', function ($match) 
            { 
                return '-'.strtoupper($match[1]); 
            }, ucfirst($name));
        };
        $max = max(array_map('strlen', array_keys($this->headers))) + 1;
        $content = '';
        ksort($this->headers);
        foreach ($this->headers as $name => $values) 
        {
            foreach ($values as $value) 
            {
                $content .= sprintf("%-{$max}s %s\r\n", $beautifier($name).':', $value);
            }
        }
        return $content;
    }
    private function all()
    {
        return $this->headers;
    }
    private function keys()
    {
        return array_keys($this->headers);
    }
    private function replace(array $headers = [])
    {
        $this->headers = [];
        $this->add([]);
    }
    private function add(array $headers)
    {
        foreach ($headers as $key => $values)
        {
            $this->set($key, $values);
        }
    }
    private function get($key, $default = null, $first = true)
    {
        $key = strtr(strtolower($key), '_', '-');
        if (!isset($this->headers[$key]))
        {
            if (null === $default)
            {
                return $first ? null : [];
            }
            return $first ? $default : [$default];
        }
        if ($first)
        {
            return count($this->headers[$key]) ? $this->headers[$key][0] : $default;
        }
        return $this->headers[$key];
    }
    private function set($key, $values, $replace = true)
    {
        $key = strtr(strtolower($key), '_', '-');
        $values = (array) $values;
        if (true === $replace || !isset($this->headers[$key]))
        {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }
        if ('cache-control' === $key) 
        {
            $this->cacheControl = $this->parseCacheControl($values[0]);
        }
    }
    private function has($key)
    {
        return isset($this->headers[strtr(strtolower($key), '_', '-')]);
    }
    private function contains($key, $value)
    {
        return in_array($value, $this->get($key, null, false));
    }
    private function remove($key)
    {
        $key = strtr(strtolower($key), '_', '-');
        unset($this->headers[$key]);
        if ('cache-control' === $key)
        {
            $this->cacheControl = array();
        }
    }
    private function getDate($key, \DateTime $default = null)
    {
        if (null === $value = $this->get($key))
        {
            return $default;
        }
        if (false === $date = \DateTime::createFromFormat(DATE_RFC2822, $value))
        {
            throw new \RuntimeException(sprintf('The %s HTTP header is not parseable (%s).', $key, $value));
        }
        return $date;
    }
    private function addCacheControlDirective($key, $value = true)
    {
        $this->cacheControl[$key] = $value;
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }
    private function hasCacheControlDirective($key)
    {
        return $this->cacheControl[$key];
    }
    private function getCacheControlDirective($key)
    {
        return(isset($this->cacheControl[$key])?$this->cacheControl[$key]:null);
    }
    private function removeCacheControlDirective($key)
    {
        unset($this->cacheControl[$key]);
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }
    private function getCacheControlHeader() // as protected
    {  
        $parts = [];
        ksort($this->cacheControl);
        foreach ($this->cacheControl as $key => $value)
        {
            if (true === $value)
            {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', $value))
                {
                    $value = '"'.$value.'"';
                }
                $parts[] = "$key=$value";
            }
        }
        return implode(', ', $parts);
    }
    private function parseCacheControl($header) // as protected
    {
        $cacheControl = [];
        preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?#', $header, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) 
        {
            $cacheControl[strtolower($match[1])] = isset($match[2]) && $match[2] ? $match[2] : (isset($match[3]) ? $match[3] : true);
        }
        return $cacheControl;
    }
}