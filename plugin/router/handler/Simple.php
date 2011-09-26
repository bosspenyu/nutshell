<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\router\handler
{
	use nutshell\core\plugin\PluginExtension;
	use nutshell\plugin\router\Route;
	
	/**
	 * @author guillaume
	 * @package nutshell-plugin
	 */
	class Simple extends PluginExtension
	{
		private $route	=null;
		
		public function __construct()
		{
			if ($this->plugin->Url->nodeEmpty(0))
			{
				$this->route=new Route('index','index',array());
			}
			else
			{
				//Set the controler to node 0.
				$control=$this->plugin->Url->node(0);
				
				//Set some defaults.
				$action	='index';
				$args	=array();
				
				//Check for action.
				$node=$this->plugin->Url->node(1);
				if (!is_null($node))
				{
					//Set action to node 1.
					$action=$node;
					//Check for args.
					if (!is_null($this->plugin->Url->node(2)))
					{
						//Set args to nodes 2 onwards.
						$node=2;
						while (true)
						{
							//grab the next node
							$arg = $this->plugin->Url->node($node++);
							
							if(is_null($arg)) 
							{
								break;
							}
							//append to the args array 
							$args[] = $arg;
						}
					}
				}
				$this->route=new Route($control,$action,$args);
			}
		}
		
		public function getRoute()
		{
			return $this->route;
		}
	}
}
?>