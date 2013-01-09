<?php
class Config
{
	const $configs;
	const $configKey;
	
	function Rotate()
	{
		if ($configKey >= count($configs))
		{
			$configKey = 0;
		}
		else
		{
			$config = $configs[$configKey];
			
			return $config;
		}
	}
};
?>