<?php
namespace nutshell\core\request\handler
{
	use nutshell\core\request\Handler;
	
	class Http extends Handler
	{
		protected function setupNodes()
		{
			$baseURL = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
			if(defined('NS_APP_WEB_HOME'))
			{
				$baseURL = preg_replace('/^' . preg_quote(NS_APP_WEB_HOME, '/') . '/', '', $baseURL);
			}
			$nodes=explode('/', $baseURL);
			if (reset($nodes)=='')	array_shift($nodes);
			if (end($nodes)=='')	array_pop($nodes);
			if (!isset($nodes[0]))
			{
				$nodes[0]='';
			}
			if (substr(current($nodes),0,1)=='?')
			{
				array_pop($nodes);
			}
			$this->nodes=$nodes;
		}
		
		protected function setupData()
		{
			$this->data =&$_REQUEST;
			$this->raw	= file_get_contents('php://input');
		}
	}
}
?>
