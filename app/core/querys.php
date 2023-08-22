<?php
$hostname 	="localhost";
$username 	="root"; //"lacanycu_admin";
$password 	=""; //"lacasitadelassalchipapas";
$dbname 	="lacanycu_pos";
$usertable 	="your_tablename";
$yourfield 	="your_field";

/*
$conn = mysqli_connect($hostname, $username, $password);

mysqli_select_db($dbname, $conn);

# Comprobar si existe registro
$query = "select current_date() fecha, current_time() hora, count(*) cantidad, sum(costo_banco) total_banco from tec_purchases group by current_date(), current_time()";

$result = mysqli_query($query);
*/

$mysqli = new mysqli($hostname, $username, "", $dbname);
if ($mysqli->connect_errno) {
    echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
echo $mysqli->host_info . "\n";

$cSql = "select current_date() fecha, current_time() hora, count(*) cantidad, sum(costo_banco) total_banco from tec_purchases group by current_date(), current_time()";

$result = $mysqli->query($cSql);

//if (!$query){
//    echo "Falló la creación de la tabla: (" . $mysqli->errno . ") " . $mysqli->error;
//}

foreach($result as $r){
	echo $r["fecha"] . " " . $r["hora"] . " __ " . $r["cantidad"] . " __ " . $r["total_banco"] . "<br>";
}
?>