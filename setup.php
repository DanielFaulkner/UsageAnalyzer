<html><head><title>Setting up database for Logon Recorder</title></head><body>

<h3>Setting up...</h3>

<br /><i>
You will need to create a database and user manually. From the mysql terminal (mysql -u <b>user</b> -p) enter the following:<br>
CREATE DATABASE <b>database</b>;<br>
GRANT ALL ON <b>database</b>.* TO '<b>user</b>'@'localhost' IDENTIFIED BY '<b>password</b>';
</i><br />

<? // setup.php
include_once 'functions.php';

// Manually create a database with:
// CREATE DATABASE database_name;

// Manually create a user account with:
// GRANT ALL ON database_name.* TO 'username'@'localhost' IDENTIFIED BY 'password';

// The following tables will need optimising, they are probably quite wasteful currently

// Add domain and usertype
createTable('logins','id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
			status VARCHAR(16),
			user VARCHAR(256),
			location VARCHAR(63),
			time INT UNSIGNED');

createTable('audits','computer VARCHAR(63) PRIMARY KEY,
			updatetime INT UNSIGNED,
			make TINYTEXT,
			model TINYTEXT,
			snsys VARCHAR(100),
			snbios VARCHAR(100),
			snmb VARCHAR(100),
			os TINYTEXT,
			ram VARCHAR(100),
			hdd VARCHAR(100),
			hddfree INT UNSIGNED,
			cpumake TINYTEXT,
			cpuspeed VARCHAR(10),
			cpucores VARCHAR(10)');

createTable('benchmarks','computer VARCHAR(63) PRIMARY KEY,
			updatetime INT UNSIGNED,
			cpu FLOAT(2,1),
			ram FLOAT(2,1),
			gpu FLOAT(2,1),
			gpu3d FLOAT(2,1),
			hdd FLOAT(2,1),
			base FLOAT(2,1),
			state INT UNSIGNED');

createTable('serverResponse','computer VARCHAR(63) PRIMARY KEY,
			nextReply CHAR(3)');

createTable('areas','areaname VARCHAR(63) PRIMARY KEY,
			DefaultClientResponse INT UNSIGNED,
			DefaultBenchmarkWeightCPU INT UNSIGNED,
			DefaultBenchmarkWeightRAM INT UNSIGNED,
			DefaultBenchmarkWeightGPU INT UNSIGNED,
			DefaultBenchmarkWeightGPU3d INT UNSIGNED,
			DefaultBenchmarkWeightHDD INT UNSIGNED');

createTable('computers','computername VARCHAR(63) PRIMARY KEY,
			areaname VARCHAR(63)');

?>

<br>...done.
</body></html>
