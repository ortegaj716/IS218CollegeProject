<?php

//File loading class with write to database feature

$csv = new CSVLoader();

$data = $csv->openFile('college/hd2011.csv');

$csv->writeToDatabase($data);


class CSVLoader{
	
	public function openFile($f){
		$firstLine = true;
		$fields;
		$data;
		//Need a handler. Open the $f file and use r for read mode.
		if($handle = fopen($f,"r")){
			//Opening the file was a success. Let's try to get the csv data.
			while($line = fgetcsv($handle)){
				//Was it the first time?
				if($firstLine == true){
					$firstLine = false;
					$fields = $line;
				}
				else{
				//Adding the [] adds a new numerical index. Very important and useful!
				$data[] = array_combine($fields,$line);	
				}
			}

		fclose($handle);
		
		return $data;
		
		}
		else{
			//Could not open file!
			echo "Failed to open the file " . $f;
		}
	}
	
	
	public function writeToDatabase($a){

		$host = "sql2.njit.edu";
		$dbname = "jao4";
		$table = "colleges";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		//Let's write to the database
		foreach($a as $aa){
			$insert = null;
			foreach($aa as $label => $value){
				$insert[] = $value;
			}
			
			print_r($insert);
			
			$STH = $DBH->prepare("insert into $table values(?,?,?)");
			$STH->execute($insert);	
		}
		
		
		$DBH = null;
		
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	
			
	}
}


?>