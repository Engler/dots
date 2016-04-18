<?php
namespace App;

class Logger
{
	public static function message($context, $message = '')
	{
		if (is_string($context)) {
			$message = $context;
			$context = null;
		}

		if (strpos($message, "\n") !== false) {
			$message .= "\n" . $message;
		}
		
		echo date('[Y-m-d H:i:s]') . (is_object($context) ? (' [' . $context->getId() . ']') : '') . ' ' . $message . "\n";
	}
}