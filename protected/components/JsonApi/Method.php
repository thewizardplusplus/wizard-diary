<?php

namespace JsonApi;

abstract class Method
{
	const GET='GET';
	const POST='POST';

	public static function getAll()
	{
		return (new \ReflectionClass(__CLASS__))->getConstants();
	}
}
