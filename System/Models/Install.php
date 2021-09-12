<?php

class Model_Install {
	
	public function createTables() {
		$mysql = MySQL::getInstance();

		$sqlFile = fopen(ROOT_PATH.'/Models/install.sql', 'r');

		$queries = fread($sqlFile, filesize(ROOT_PATH.'/Models/install.sql'));

		$queries = explode(';', $queries);

		foreach($queries as &$query) {
			$query = str_replace('prefix_', $mysql->getTablePrefix(), $query);
			
			if(!empty($query))	{
				$mysql->query($query);	
			}			
		}


	}

};
