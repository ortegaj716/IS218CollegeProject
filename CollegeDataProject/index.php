<?php

$program = new program();

class program{
	
	function __construct(){
		
		$page = 'homepage';
		$arg = NULL;
		
		if(isset($_REQUEST['page'])){
			$page = $_REQUEST['page'];
		}
		
		if(isset($_REQUEST['arg'])){
			$arg = $_REQUEST['arg'];
		}
		
		$page = new $page($arg);
		
	}
	
	
}	


abstract class page{
	
	public $content;
	
	function __construct($arg = NULL){
		
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			
			$this->get();
		}
		else{
			
			$this->post();
		}
	}
	
	function menu(){
				
	}
		
	function get(){
	}
	
	function post(){
	}
	
	function __destruct(){
		//Echo out some content
		echo $this->content;
	}
	
	
	
}
	


class homepage extends page{
	
	function get(){
		$this->content = '
		<h1>IT218 College Data Project</h1>
		<h2>Joshua Ortega</h2>
		<br>
		<h3>Directory</h3>
		<a href = "index.php?page=pEnrollment">The highest enrollments.</a><br>
		<a href = "index.php?page=pLiabilities">The highest total liabilities</a><br>
		<a href = "index.php?page=pAssets">The highest net assets</a><br>
		<a href = "index.php?page=pRevenue">The highest total revenue</a><br>
		<a href = "index.php?page=pRPS">The highest total revenue per student</a><br>
		<a href = "index.php?page=pAPS">The highest net assets per student</a><br>
		<a href = "index.php?page=pLPS">The highest total liabilities per student</a><br>
		<a href = "index.php?page=p5">Top Colleges</a><br>
		<a href = "index.php?page=pState">Colleges in my state</a><br>
		<a href = "index.php?page=pPiL">The largest increase in liabilities</a><br>
		<a href = "index.php?page=pPiE">The largest increase in enrollment</a><br>
		
		';
	}
	
}

/*READ ME
 * HEY, WHY ARE YOU INNER JOINING EXTRA TABLES?
 * Some records are missing from certain csv files. For example, the college that had the
 * highest enrollment had no financial records. When I had to do the page for the
 * top colleges, the colleges wouldn't show up unless they had data across all 3 tables
 * (since I was querying all their data at once). Therefore, every page here only shows 
 * colleges IF THEY EXIST IN ALL 3 DATABASE TABLES. The only way to ensure that was
 * to join all tables for all queries, even if the join didn't provide necessary data.
 */

class pEnrollment extends page{
	
	function get(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$STH = $DBH->query("select colleges.name, students2.enrollment from colleges inner join students2 on colleges.id = students2.id
		inner join finances on colleges.id = finances.id 
		where (students2.year = 2011 and finances.year = 2011)
		 order by enrollment desc limit 50");
		
		$this->content .= "<h1>Highest Enrollment</h1><h3>Top 50 in 2011</h3><br>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
			<tr>
				<th>Name</th>
				<th>Enrollment</th>
			</tr>
		";
		
		while($rows = $STH->fetch()){
			$this->content .= "<tr>";
			$this->content .= "<td>" . $rows['name'] . "</td>";
			$this->content .= "<td>" . $rows['enrollment'] . "</td>";
			$this->content .= "</tr>";
		}
		
		$this->content .= "</table>";
		
		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}


class pLiabilities extends page{
	
	function get(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$STH = $DBH->query("select colleges.name, finances.liability, finances.year from colleges inner join finances on colleges.id = finances.id
		 inner join students2 on colleges.id = students2.id where (finances.year = 2011 and students2.year = 2011)
		  order by liability desc limit 50");

		
		$this->content .= "<h1>Highest Liabilities</h1><h3>Top 50 in 2011</h3><br>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
			<tr>
				<th>Name</th>
				<th>Liability</th>
			</tr>
		";
		
		while($rows = $STH->fetch()){
			$this->content .= "<tr>";
			$this->content .= "<td>" . $rows['name'] . "</td>";
			$this->content .= "<td>" . $rows['liability'] . "</td>";
			$this->content .= "</tr>";
		}
		
		$this->content .= "</table>";
		
		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}

class pAssets extends page{
	
	function get(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$STH = $DBH->query("select colleges.name, finances.assets, finances.year from colleges
		 inner join finances on colleges.id = finances.id inner join students2 on colleges.id = students2.id
		where (finances.year = 2011 and students2.year = 2011) 
		order by finances.assets desc limit 50");
		
		$this->content .= "<h1>Highest Net Assets</h1><h3>Top 50 in 2011</h3><br>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
			<tr>
				<th>Name</th>
				<th>Assets</th>
			</tr>
		";
		
		while($rows = $STH->fetch()){
			$this->content .= "<tr>";
			$this->content .= "<td>" . $rows['name'] . "</td>";
			$this->content .= "<td>" . $rows['assets'] . "</td>";
			$this->content .= "</tr>";
		}
		
		$this->content .= "</table>";
		
		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}

class pRevenue extends page{
	
	function get(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$STH = $DBH->query("select colleges.name, finances.revenue from colleges 
		inner join finances on colleges.id = finances.id inner join students2 on colleges.id = students2.id
		where (finances.year = 2011 and students2.year = 2011) 
		order by finances.revenue desc limit 50");
		
		$this->content .= "<h1>Highest Revenue</h1><h3>Top 50 in 2011</h3><br>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
			<tr>
				<th>Name</th>
				<th>Revenue</th>
			</tr>
		";
		
		while($rows = $STH->fetch()){
			$this->content .= "<tr>";
			$this->content .= "<td>" . $rows['name'] . "</td>";
			$this->content .= "<td>" . $rows['revenue'] . "</td>";
			$this->content .= "</tr>";
		}
		
		$this->content .= "</table>";
		
		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}

class pRPS extends page{
	
	function get(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$STH = $DBH->query("select colleges.name, finances.revenue, students2.enrollment, (finances.revenue / students2.enrollment) as rps
		from colleges inner join finances on colleges.id = finances.id inner join students2 on colleges.id = students2.id
		where (finances.year = 2011 and students2.year = 2011)
		order by rps desc limit 50");
		
		$this->content .= "<h1>Highest Revenue per Student</h1><h3>Top 50 in 2011</h3><br>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
			<tr>
				<th>Name</th>
				<th>Revenue</th>
				<th>Enrollment</th>
				<th>Revenue per Student</th>
			</tr>
		";
		
		while($rows = $STH->fetch()){
			$this->content .= "<tr>";
			$this->content .= "<td>" . $rows['name'] . "</td>";
			$this->content .= "<td>" . $rows['revenue'] . "</td>";
			$this->content .= "<td>" . $rows['enrollment'] . "</td>";
			$this->content .= "<td>" . $rows['rps'] . "</td>";
			$this->content .= "</tr>";
		}
		
		$this->content .= "</table>";
		
		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}

class pAPS extends page{
	
	function get(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$STH = $DBH->query("select colleges.name, finances.assets, students2.enrollment, (finances.assets / students2.enrollment) as aps
		from colleges inner join finances on colleges.id = finances.id inner join students2 on colleges.id = students2.id
		where (finances.year = 2011  and students2.year = 2011)
		order by aps desc limit 50");
		
		$this->content .= "<h1>Highest Assets per Student</h1><h3>Top 50 in 2011</h3><br>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
			<tr>
				<th>Name</th>
				<th>Assets</th>
				<th>Enrollment</th>
				<th>Assets per Student</th>
			</tr>
		";
		
		while($rows = $STH->fetch()){
			$this->content .= "<tr>";
			$this->content .= "<td>" . $rows['name'] . "</td>";
			$this->content .= "<td>" . $rows['assets'] . "</td>";
			$this->content .= "<td>" . $rows['enrollment'] . "</td>";
			$this->content .= "<td>" . $rows['aps'] . "</td>";
			$this->content .= "</tr>";
		}
		
		$this->content .= "</table>";
		
		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}

class pLPS extends page{
	
	function get(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		$table = "colleges";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$STH = $DBH->query("select colleges.name, finances.liability, students2.enrollment, (finances.liability / students2.enrollment) as lps
		from colleges inner join finances on colleges.id = finances.id inner join students2 on colleges.id = students2.id
		where (finances.year = 2011 and students2.year = 2011) order by lps desc limit 50");
		
		$this->content .= "<h1>Highest Liability per Student</h1><h3>Top 50 in 2011</h3><br>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
			<tr>
				<th>Name</th>
				<th>Liability</th>
				<th>Enrollment</th>
				<th>Liability per Student</th>
			</tr>
		";
		
		while($rows = $STH->fetch()){
			$this->content .= "<tr>";
			$this->content .= "<td>" . $rows['name'] . "</td>";
			$this->content .= "<td>" . $rows['liability'] . "</td>";
			$this->content .= "<td>" . $rows['enrollment'] . "</td>";
			$this->content .= "<td>" . $rows['lps'] . "</td>";
			$this->content .= "</tr>";
		}
		
		$this->content .= "</table>";
		
		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}

class p5 extends page{
	

	function get(){
	
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		/* GAME PLAN 
		 * 
		 * 1.Check if a variable is set (variable is for what to search up)
		 * 2.If doesn't exist, set variable to enrollment
		 * 3.If exists, grab variable.
		 * 4.Variable is used for ORDER BY $VAR DESC
		 * 5.Make sure you can click columns to reload page and and set variable
		 * 6.Should be ok, right?
		 */
		 
		 $sort = "enrollment";
		 
		 if(isset($_REQUEST['sort'])){
			$sort = $_REQUEST['sort'];
		}
		 
		$STH = $DBH->query("select colleges.name, students2.enrollment, finances.liability,
			finances.assets, finances.revenue, (finances.revenue / students2.enrollment) as rps,
			(finances.assets / students2.enrollment) as aps, (finances.liability / students2.enrollment) as lps 
			
			from colleges inner join students2 on colleges.id = students2.id 
			inner join finances on colleges.id = finances.id 
			
			where (students2.year = 2011 and finances.year = 2011)
			order by $sort desc limit 5");

		//Puts the data in a table.
		$this->content .= "<h1>Top Colleges</h1><h3>Ranked by previous criteria</h3><br>";
		
		$this->content .= "<table border = 2>";
		$this->content .= '
			<tr>
				<th>Name</th>
				<th><a href = "index.php?page=p5&sort=enrollment">Enrollment</a></th>
				<th><a href = "index.php?page=p5&sort=liability">Liability</a></th>
				<th><a href = "index.php?page=p5&sort=assets">Assets</a></th>
				<th><a href = "index.php?page=p5&sort=revenue">Revenue</a></th>
				<th><a href = "index.php?page=p5&sort=rps">Revenue per Student</a></th>
				<th><a href = "index.php?page=p5&sort=aps">Assets per Student</a></th>
				<th><a href = "index.php?page=p5&sort=lps">Liability per Student</a></th>
			</tr>
		';

		while($rows = $STH->fetch()){
			$this->content .= "<tr>";
				$this->content .= "<td>" . $rows['name'] . "</td>";
				$this->content .= "<td>" . $rows['enrollment'] . "</td>";
				$this->content .= "<td>" . $rows['liability'] . "</td>";
				$this->content .= "<td>" . $rows['assets'] . "</td>";
				$this->content .= "<td>" . $rows['revenue'] . "</td>";
				$this->content .= "<td>" . $rows['rps'] . "</td>";
				$this->content .= "<td>" . $rows['aps'] . "</td>";
				$this->content .= "<td>" . $rows['lps'] . "</td>";
			$this->content .= "</tr>";
		}
		
		$this->content .= "</table>";

		$DBH = null;
		
		}catch(PDOException $e){
			echo $e->getMessage();
		}
		
		$this->content .= "<br><br><a href = 'index.php'>Back to Directory</a>";
		
	}

}

class pState extends page{
	
	function get(){
		$this->content = '
		<h1>Colleges In Your State</h1>
		
		<form action = "http://web.njit.edu/~jao4/college/index.php?page=pState" method = "POST">
			Please enter your state abbreviation: <br>
			<input type = "text" name = "state"> <br>
			<input type = "submit">
		</form>
		';
	}
	
	function post(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		$state = $_POST['state'];
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$STH = $DBH->query("select colleges.name, colleges.state from colleges where colleges.state = '$state'");
		
		$this->content .= "<h1>Colleges In Your State</h1><br>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
			<tr>
				<th>Name</th>
				<th>State</th>
			</tr>
		";
		
		while($rows = $STH->fetch()){
			$this->content .= "<tr>";
				$this->content .= "<td>" . $rows['name'] . "</td>";
				$this->content .= "<td>" . $rows['state'] . "</td>";
			$this->content .= "</tr>";
		}
		
		$this->content .= "</table>";
		
		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}

class pPiL extends page{
	
	function get(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$L2010 = $DBH->query("select colleges.id, colleges.name, finances.liability from colleges 
		inner join finances on colleges.id = finances.id inner join students2 on colleges.id = students2.id 
		where (finances.year = 2010 and students2.year = 2010)
		order by colleges.id asc");
		
		$L2011 = $DBH->query("select colleges.id, colleges.name, finances.liability from colleges 
		inner join finances on colleges.id = finances.id inner join students2 on colleges.id = students2.id 
		where (finances.year = 2011 and students2.year = 2011)
		order by colleges.id asc");
		
		$a10 = array(array());
		$a11 = array(array());
		
		$i = 0;
		while($rows = $L2010->fetch()){
			$a10[$i][0] = $rows['id'];
			$a10[$i][1] = $rows['name'];
			$a10[$i][2] = $rows['liability'];
			$i+=1;
		}
		
		$i = 0;
		while($rows = $L2011->fetch()){
			$a11[$i][0] = $rows['id'];
			$a11[$i][1] = $rows['name'];
			$a11[$i][2] = $rows['liability'];
			$i+=1;
		}

		$final = array(array());
		
		//Data doesn't perfectly match from 2010 to 2011.
		//We need to make sure we're matching the right ID's.
		//Very brute force, but results are more important than methods
		for($i = 0; $i < count($a11); $i++){
			for($j = 0; $j < count($a10); $j++){
				if($a11[$i][0] == $a10[$j][0]){ //Make sure ID's match
					$change = ($a11[$i][2] - $a10[$j][2]) / $a10[$j][2];
					$final[] = array('id' => $a11[$i][0],'name' => $a11[$i][1],'2010' => $a10[$j][2],'2011' => $a11[$i][2],'change' => $change);
					break;
				}
			}
		}
		
		//Sort the data with our custom sorting function shown below.
		usort($final, 'cmp');		
		
		$this->content .= "<h1>Largest Increase in Liabilities</h1>";
		$this->content .= "<h3>Top 50</h3>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
		<tr>
			<th>College</th><th>2010 Liability</th><th>2011 Liability</th><th>Percent Change</th>
		</tr>
		";

		$i = 0; //Only top 50
		foreach($final as $a){
			$this->content .= "<tr>";
				$this->content .= "<td>" . $a['name'] . "</td>";
				$this->content .= "<td>" . $a['2010'] . "</td>";
				$this->content .= "<td>" . $a['2011'] . "</td>";
				$this->content .= "<td>" . $a['change'] * 100 . "</td>";
			$this->content .= "</tr>";
			
			$i++;
			if($i > 49)
			break;
		}

		$this->content .= "</table>";

		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}

class pPiE extends page{
	
	function get(){
		
		$host = "sql2.njit.edu";
		$dbname = "jao4";
		try{
		$DBH = new PDO("mysql:host=$host;dbname=$dbname","jao4","TopSecret");
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$L2010 = $DBH->query("select colleges.id, colleges.name, students2.enrollment from colleges 
		inner join students2 on colleges.id = students2.id inner join finances on colleges.id = finances.id
		where (students2.year = 2010 and finances.year = 2010) order by colleges.id asc");
		
		$L2011 = $DBH->query("select colleges.id, colleges.name, students2.enrollment from colleges 
		inner join students2 on colleges.id = students2.id inner join finances on colleges.id = finances.id
		where (students2.year = 2011 and finances.year = 2011) order by colleges.id asc");
		
		$a10 = array(array());
		$a11 = array(array());
		
		$i = 0;
		while($rows = $L2010->fetch()){
			$a10[$i][0] = $rows['id'];
			$a10[$i][1] = $rows['name'];
			$a10[$i][2] = $rows['enrollment'];
			$i+=1;
		}
		
		$i = 0;
		while($rows = $L2011->fetch()){
			$a11[$i][0] = $rows['id'];
			$a11[$i][1] = $rows['name'];
			$a11[$i][2] = $rows['enrollment'];
			$i+=1;
		}

		$final = array(array());
		
		//Data doesn't perfectly match from 2010 to 2011.
		//We need to make sure we're matching the right ID's.
		//Very brute force, but results are more important than methods
		for($i = 0; $i < count($a11); $i++){
			for($j = 0; $j < count($a10); $j++){
				if($a11[$i][0] == $a10[$j][0]){ //Make sure ID's match
					$change = ($a11[$i][2] - $a10[$j][2]) / $a10[$j][2];
					$final[] = array('id' => $a11[$i][0],'name' => $a11[$i][1],'2010' => $a10[$j][2],'2011' => $a11[$i][2],'change' => $change);
					break;
				}
			}
		}
		
		//Sort the data with our custom sorting function.
		usort($final,'cmp');
		
		$this->content .= "<h1>Largest Increase in Enrollment</h1>";
		$this->content .= "<h3>Top 50</h3>";
		
		$this->content .= "<table border = 2>";
		$this->content .= "
		<tr>
			<th>College</th><th>2010 Enrollment</th><th>2011 Enrollment</th><th>Percent Change</th>
		</tr>
		";

		$i = 0; //Only going to do the top 50. 
		foreach($final as $a){
			$this->content .= "<tr>";
				$this->content .= "<td>" . $a['name'] . "</td>";
				$this->content .= "<td>" . $a['2010'] . "</td>";
				$this->content .= "<td>" . $a['2011'] . "</td>";
				$this->content .= "<td>" . $a['change'] * 100 . "</td>";
			$this->content .= "</tr>";
			
			$i++;
			if($i > 49)
			break;
		}

		$this->content .= "</table>";

		$DBH = null;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
	}
}

//Custom sorting function.

function cmp(array $a, array $b){
	if($a['change'] < $b['change']){
		return 1;
	} else if($a['change'] > $b['change']){
		return -1;
	} else {
		return 0;
	}
}

//No longer need the extra custom sorting functions because mysql is sorting the database table for me.

?>