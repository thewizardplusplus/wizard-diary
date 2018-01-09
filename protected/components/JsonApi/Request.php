<?php

namespace JsonApi;

class Request
{
	public static function __callStatic($name, $arguments)
	{
		return call_user_func_array(
			array(__CLASS__,'request'),
			array_merge(array(strtoupper($name)),$arguments)
		);
	}

	public static function request($method, $url, $data=array())
	{
		if(!in_array($method,Method::getAll()))
			throw new \Exception("The $method method isn't supported.");

		$cUrl=curl_init();
		if($cUrl===false)
			throw new \Exception('Unable to init cURL.');

		$response=null;
		try
		{
			switch($method)
			{
				case Method::GET:
					self::setCUrlOption($cUrl,CURLOPT_HTTPGET,true);

					$query=http_build_query($data);
					if(!empty($query))
						$url.='?'.$query;

					break;
				case Method::POST:
					self::setCUrlOption($cUrl,CURLOPT_POST,true);
					self::setCUrlOption($cUrl,CURLOPT_POSTFIELDS,$data);
					self::setCUrlOption($cUrl,CURLOPT_SAFE_UPLOAD,true);

					break;
			}
			self::setCUrlOption($cUrl,CURLOPT_URL,$url);
			self::setCUrlOption($cUrl,CURLOPT_RETURNTRANSFER,true);

			$rawResponse=curl_exec($cUrl);
			if($rawResponse===false)
				throw new \Exception(
					self::formatCUrlError(
						$cUrl,
						'Unable to send the HTTP request'
					)
				);

			$response=new Response($rawResponse);
		}
		finally
		{
			curl_close($cUrl);
		}

		return $response;
	}

	private static function setCUrlOption($cUrl, $option, $value)
	{
		$result=curl_setopt($cUrl,$option,$value);
		if($result===false)
			throw new \Exception(
				self::formatCUrlError($cUrl,'Unable to set the cURL option')
			);
	}

	private static function formatCUrlError($cUrl, $error)
	{
		$cUrlError=curl_error($cUrl);
		if(!empty($cUrlError))
			$error.=sprintf(' (%s)',$cUrlError);

		return $error.'.';
	}
}
