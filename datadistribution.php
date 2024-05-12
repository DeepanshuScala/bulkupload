<?php
include_once('dbcon.php');

switch($_POST['case']){
    case 1:
        $s = "SELECT DISTINCT Department FROM temp_csv_data";
        $r = mysqli_query($conn, $s);
        $row = $r->fetch_all();
        print_r($row);
        break;
}