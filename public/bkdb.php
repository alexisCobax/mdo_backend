<?php

/*
 * Backup-PHP-MySQL
 * https://github.com/CarlosPinedaT/Backup-PHP-MySQL
 * 
 */

$servidor="147.182.139.71";	$bd="jkkxjmpypf";	$usuario="jkkxjmpypf"; $password="CtZxUaBfS8";

backup_tables($servidor,$usuario,$password,$bd);

function backup_tables($host,$user,$pass,$name,$tables = '*')
{
	$return='';
	$link = mysqli_connect($host,$user,$pass);
	mysqli_select_db($link,$name);
	mysqli_query($link,"SET NAMES 'utf8'");
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysqli_query($link,'SHOW TABLES');
		while($row = mysqli_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	
	$return .= "--\n";
	$return .= "-- Sistema de AutoBackups Mysql v1\n";
	$return .= "--\n";
	$return .= '-- Creado: ' . date("Y/m/d h:i") . "\n";
	$return .= "--\n";
	$return .= "-- Base de datos : " . $name . "\n";
	$return .= "--\n";
	$return .= "-- ---------------------------------------------------\n";
	$return .= "-- ---------------------------------------------------\n\n\n\n";
	$return .= 'SET AUTOCOMMIT = 0 ;' ."\n" ;
	$return .= 'SET FOREIGN_KEY_CHECKS=0 ;' ."\n\n\n\n" ;


	foreach($tables as $table)
	{
		$result = mysqli_query($link,'SELECT * FROM '.$table);
		$num_fields = mysqli_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysqli_fetch_row(mysqli_query($link,'SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysqli_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j < $num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = str_replace("\n", '\n', $row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j < ($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	$handle = fopen(dirname(__DIR__).'/storage/app/public/backup/db-backup-'.date('Y-m-d H-i-s').'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}