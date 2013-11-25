<?php

//Second version of the file loading class.
//Incredibly similar, except now it adds a year column when creating the database.

$csv = new CSVLoader();

$data = $csv->openFile('college/f0910_f1a.csv');
$data2 = $csv->openFile('college/f1011_f1a.csv');

$csv->writeToDatabase($data,2010);
$csv->writeToDatabase($data2,2011);


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
	
	
	public function writeToDatabase($a,$year){

		$host = "sql2.njit.edu";
		$dbname = "jao4";
		$table = "finances";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		//Let's write to the database
		foreach($a as $aa){
			$insert = null;
			foreach($aa as $label => $value){
				$insert[] = $value;
			}
			//Add the year
			$insert[] = $year;
			
			print_r($insert);
			
			$STH = $DBH->prepare("insert into $table values(?,?,?,?,?)");
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