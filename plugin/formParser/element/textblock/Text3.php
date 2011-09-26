<?php
/**
 * @package nutshell-plugin
 * @author Timothy Chandler
 */
namespace nutshell\plugin\formParser\element\textblock
{
	use nutshell\plugin\formParser\element\textblock\Textblock;
	
	/**
	 * @package nutshell-plugin
	 * @author Timothy Chandler
	 */
	class Text3 extends Textblock
	{
		public function init($elementDef)
		{
			$this->setClass('text3');
			parent::init($elementDef);
		}
	}
}
?>