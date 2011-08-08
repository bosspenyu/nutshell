<?php
/**
* @package nutshell
*/
namespace nutshell\behaviour
{
	/**
	 * @package nutshell
	 */
	interface Native
	{
		public static function loadDependencies();
		
		public static function registerBehaviours();
	}
}
?>