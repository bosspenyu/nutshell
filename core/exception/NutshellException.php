<?php
/**
 * @package nutshell
 */
namespace nutshell\core\exception
{
	use nutshell\Nutshell;
	use nutshell\core\Component;
	use \Exception;

	/**
	 * @package nutshell
	 */
	class NutshellException extends Exception
	{
		public static function register() 
		{
			Component::load(array());
		}
		
		/**
		* Prevents recursion.
		* @var bool
		*/
		private static $blockRecursion = false;
		
		/**
		 * Error handler used before the class is created.
		 * @var bool
		 */
		private static $oldErrorHandler = false;
		
		/**
		 * Indicates that errors should be shown. (depends on ns_env environment variable).
		 * @var bool
		 */
		private static $echoErrors = false;
		
		/**
		 * All errors that shouldn't be shown in the user interface.
		 * @var Array
		 */
		private static $dontShowErrors = array
		(
			E_STRICT => 1
		);

		/**
		 * Echoes an error if $echoErrors and not $dontShowErrors[$errno]
		 * @param int $errno
		 * @param string $message
		 */
		private static function echoError($errno, $message)
		{
			if (self::$echoErrors) 
			{
				if (!isset(self::$dontShowErrors[$errno]))
				{
					echo $message;
				}
			}
		}
		
		/**
		 * Logs a message if Nutshell has a loader.
		 * @param string $message
		 */
		public static function logMessage($message)
		{
			if (strlen($message)>0)
			{
				try
				{	
					$nutInst = Nutshell::getInstance();
					if ($nutInst->hasPluginLoader())
					{
						$log = $nutInst->plugin->Logger();
						$log->fatal($message);
					} 
					else 
					{
						user_error("Failed to load logger: $message", E_USER_ERROR);
					}
				}
				catch (Exception $e) 
				{
					//falling back to the system logger
					error_log($message);
				}
			}
		}
		
		/**
		 * Logs this exception
		 */
		public function log()
		{
			$message = self::getDescription($this);
			self::logMessage($message);
		}
		
		/**
		 * This method treats (and logs) errors.
		 * @param int $errno
		 * @param string $errstr
		 * @param string $errfile
		 * @param int    $errline
		 * @param array $errcontext
		 */
		public static function treatError($errno, $errstr = null, $errfile = null, $errline = null, array $errcontext = null)
		{
			if (!self::$blockRecursion)
			{
				self::$blockRecursion = true;
			
				$message =
					"ERROR $errno. ".
					( (strlen($errstr)>0)  ? "Message: $errstr. " : "").
					( (strlen($errfile)>0) ? "File: $errfile. " : "").
					( ($errline>0) ? "Line: $errline. " : "") ;
				
				try // to log
				{
					self::echoError($errno, $message);
					self::logMessage($message);		
				} catch (Exception $e) 
				{
					//falling back to the system logger
					error_log($message);
				}
				self::$blockRecursion = false;
			}
			return false;
		}
		
		/**
		 * Generates a nice desription of the message in either HTML or JSON.
		 * Good for returning to the client (in dev mode) or logging.
		 * @param Exception $exception the exception 
		 * @param String $format html or json
		 */
		public static function getDescription($exception, $format=null)
		{
			if($format=='json')
			{
				$message = array('error' => true);
				$message["class"] = get_class($exception);
				if($exception->code>0)				$message["code"] = $exception->code;
				if(strlen($exception->message)>0)	$message["message"] = $exception->message;
				if(strlen($exception->file)>0)		$message["file"] = $exception->file;
				if($exception->line>0)				$message["line"] = $exception->line;
				header('content-type:application/json');
				$message = json_encode($message);
			}
			else
			{
				$message = "\nERROR";
				$message .= "\nClass:".get_class($exception);
				if($exception->code>0)				$message .= "\nCode: ".$exception->code;
				if(strlen($exception->message)>0)	$message .= "\nMessage: ".$exception->message;
				if(strlen($exception->file)>0)		$message .= "\nFile: ".$exception->file;
				if($exception->line>0)				$message .= "\nLine: ".$exception->line;
				$message .= "\n";
			}
			return $message;
		}
		
		/**
		 * This method is called when an exception happens.
		 * @param Exception $exception
		 */
		public static function treatException($exception, $format=null)
		{
			if (!self::$blockRecursion)
			{
				self::$blockRecursion = true;
				
				$message = self::getDescription($exception, $format);
				
				self::logMessage($message);
				
				if (self::$echoErrors) 
				{
					header('HTTP/1.1 500 Application Error');
					echo $message;
				}
					
				self::$blockRecursion = false;
			}
		}
		
		/**
		 * This function sets exception/error handlers. Before this call, no error is treated by this class.
		 * Errors are shown in the user interface only if NS_ENV (environment variable) is set to "dev". So, errors won't be shown in production but will be logged.
		 */
		public static function setHandlers()
		{
			set_exception_handler('nutshell\core\exception\NutshellException::treatException');
			self::$oldErrorHandler = set_error_handler('nutshell\core\exception\NutshellException::treatError');
			self::$echoErrors = (NS_ENV=='dev');
		}
	}
}
