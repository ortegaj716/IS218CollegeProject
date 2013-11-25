<?php

//Final version of the file loading class.
//I was trying to upload the enrollment data in the database, but the total enrollment isn't
//conveniently put into one row; it's separated into multiple rows!
//I needed to create a work around to upload this with the appropriate total enrollment.

$csv = new CSVLoader();

$data = $csv->openFile('college/effy2010.csv');
$data2 = $csv->openFile('college/effy2011.csv');

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
		$table = "students2";
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
			
			//Does this row exist already? If it does, we just want to update the enrollment amount.
			$STH = $DBH->query("select * from $table where id = $insert[0] and year = $insert[2]");
			
			if($STH->rowCount() > 0){ //Looks like we're updating
				$newEnrollment = null;
					while($rows = $STH->fetch()){
						$newEnrollment = $rows['enrollment'];
						print "rows enrollment: " . $rows['enrollment'] . "<br>";
					}
				$newEnrollment += $insert[1];
				print "adding " . $insert[1] . " to make " . $newEnrollment;
				$STH = $DBH->prepare("update $table set enrollment = $newEnrollment where id = $insert[0] and year = $insert[2]");
				$STH->execute();
			}
			else{ //No row found, let's make a new one then
				$STH = $DBH->prepare("insert into $table values(?,?,?)");
				$STH->execute($insert);	
			}

		}
		
		
		$DBH = null;
		
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	
			
	}
}


?>