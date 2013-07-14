<?php

/*
 * @name Gzip.trait.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Gzip.namespace.php
 */
namespace Gzip\traits;
trait Gzip 
{
        private function __construct(\Request\Request $request)
        {
            $this->request = $request;
		}
		
		private static function gzipped()
        {
            if (isset(ob_list_handlers()["ob_gzhandler"]))
            {
                return ob_start("ob_gzhandler");
            } else {
                return ob_start();
            }
		}
		
		private static function expires()
        {
			return "Expires: ".gmdate("D, d M Y H:i:s", time()+3600000*182+32)." GMT";
		}
		
		public function __call($method, $args)
        {			
            if ($method === "gzip")
            {
                return call_user_func_array([$this, "_gzip"], $args);
            } else if ($method === "getYear")
            {
                return static::getYear();
            } else {
                return call_user_func_array([$this, $method], $args);
            }
        }
		
		private function _gzip(\String $type, $charset = DEFAULT_GZIP_CHARSET)
		{
            $type = $type->getValue();
			$type = strtolower($type);
			$charset = strtolower($charset);
			static::gzipped();
			$mime = $this->request->getMimeType($type);
			if ( $mime !== null)
            {
				header("Content-Type: ".$this->request->getMimeType($type)."; charset: {$charset}");
				header("Cache-Control: must-revalidate");
				header(static::expires());
			} else {
				header("Content-Type: ".$this->request->getMimeType(DEFAULT_MIME_TYPE)."; charset: ".DEFAULT_CHARSET);
				header("Cache-Control: ".DEFAULT_CACHE_CONTROL);
				header(static::expires());
			}
			return [
                "arg" => $this->request->getMimeType($type),
                "current_headers" => getallheaders()
            ];
		}
}