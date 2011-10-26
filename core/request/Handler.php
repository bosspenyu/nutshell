<?php
namespace nutshell\core\request
{
	use nutshell\core\exception\NutshellException;
	
	abstract class Handler
	{
		public $data	=null;
		public $nodes	=array();
		
		abstract function setupNodes();
		abstract function setupData();
		
		
		public function __construct()
		{
			$this->setupNodes();
			$this->setupData();
		}
		
		public function &get($key)
		{
			return $this->data[$key];
		}
		
		public function set()
		{
			if ($numArgs=func_num_args())
			{
				$args=func_get_args();
				if (!is_array($args[0]))
				{
					$args[0]=array($args[0]=>$args[1]);
				}
				foreach ($args[0] as $key=>$val)
				{
					$this->data[$key]=$val;
				}
			}
			else
			{
				throw new NutshellException('Invalid request set. No args given.');
			}
			return $this;
		}
	}
}
?>