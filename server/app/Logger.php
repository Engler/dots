<?php
namespace App;

class Logger
{
	public static function message($message)
	{
		if (strpos($message, "\n") !== false) {
			$message .= "\n" . $message;
		}
		
		echo date('[Y-m-d H:i:s]') . ' ' . $message . "\n";
	}
}