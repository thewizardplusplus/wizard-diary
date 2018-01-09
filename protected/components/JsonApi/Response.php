<?php

namespace JsonApi;

class Response
{
	private $_response;

	public function __construct($rawResponse)
	{
		$this->_response=json_decode($rawResponse);
		if(!is_object($this->_response))
			throw new \Exception('The response isn\'t a correct JSON object.');
	}

	public function __get($name)
	{
		if(!property_exists($this->_response,$name))
			throw new \Exception('The response object is incorrect.');

		return $this->_response->{$name};
	}
}
