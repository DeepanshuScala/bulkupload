<?php
include_once('dbcon.php');
$s = "SELECT DISTINCT Department FROM temp_csv_data";
$r = mysqli_query($conn, $s);
//$row = $r->fetch_all();
while($row = $r->fetch_assoc()){
    echo "<pre>";
    print_r($row);
}
/*
$fileName = $_POST['filename'];
$startRow = $_POST['starting'];
$dueamount = (int)$_POST['dueamount'];
$paidamoount = (int)$_POST['paidamoount'];
$concessionamoount = (int)$_POST['concessionamoount'];
$scholarshipamount = (int)$_POST['scholarshipamount'];
$refundamount = (int)$_POST['refundamount'];
$batchSize = 0; 
$batchData=[];

$file = fopen('temp/'.$fileName, 'r');

for($i = 0; $i < $startRow && (fgets($file) !== false); $i++);
while (($line = fgetcsv($file,1000, ",")) !== FALSE) {
    
    $remainingValues = array_slice($line, 1);
    $batchData[$batchSize] = $remainingValues;
    // echo "<pre>";
    // print_r($batchData);
    $dueamount += (int)$batchData[$batchSize][16];
    $paidamoount += (int)$batchData[$batchSize][17];
    $concessionamoount += (int)$batchData[$batchSize][18];
    $scholarshipamount += (int)$batchData[$batchSize][19];
    $refundamount += (int)$batchData[$batchSize][23];

    insertBatch($conn, $batchData[$batchSize]);
    $batchSize++;
    $startRow++;
    if($batchSize == 10000){
        break;
    }
}
if($_POST['count']>$startRow){
    echo json_encode(['status'=>200,'more'=>1,'filename'=>$fileName,
        'starting'=>$startRow,'count'=>$_POST['count'],'dueamount'=>$dueamount,
        'paidamoount'=>$paidamoount,'concessionamoount'=>$concessionamoount,
        'scholarshipamount'=>$scholarshipamount,'refundamount'=>$refundamount]); 
}
else{
    echo json_encode(['status'=>200,'message'=>'Done uploading','starting'=>$startRow,'dueamount'=>$dueamount,
        'paidamoount'=>$paidamoount,'concessionamoount'=>$concessionamoount,
        'scholarshipamount'=>$scholarshipamount,'refundamount'=>$refundamount]); 
}
*/
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
    
//     // Open and read the CSV file
//     // if (($handle = fopen($fileName, "r")) !== FALSE) {
//     //     $rowIndex = 0;
//     //     $batchData = [];

//     //     // Loop through each row in the CSV file
//     //     while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
//     //         $rowIndex++;
//     //         // continue;
//     //         // print_r(count($data));
//     //         // die();
//     //         // Skip rows until reaching the 7th row
//     //         if ($rowIndex < $startRow) {
//     //             continue;
//     //         }

//     //         // Exclude the first column (serial number/S.No.)
//     //         // $remainingValues = array_slice($data, 1);
//     //         // $batchData[] = $remainingValues;
//     //         //insertBatch($conn, $batchData);
//     //         // Insert in batches of 1000
//     //         // if (count($batchData) == $batchSize) {
//     //         //     insertBatch($conn, $batchData);
//     //         //     $batchData = []; // Clear the batch array for the next set
//     //         // }
//     //     }
//     //     echo $rowIndex;
//     //     // Insert remaining rows if any exist after the last batch
//     //     // if (count($batchData) > 0) {
//     //     //     insertBatch($conn, $batchData);
//     //     // }
//     //     fclose($handle);
//     //     echo "CSV data from the 7th row onwards has been successfully imported into the database.";
//     // } else {
//     //     echo "Error opening the uploaded CSV file.";
//     // }
// } else {
//     echo "No file uploaded.";
// }

function insertBatch($conn, $batchData) {
    if (empty($batchData)) {
        return;
    }

    $placeholders = implode(", ",array_map(function($value) {return "'" . preg_replace('/[\'"]/','', $value) . "'";}, $batchData));
    //print_r($placeholders);
    $sql = "INSERT INTO temp_csv_data (`Date`, `AcademicYear`, `Session`, `AllotedCategory`, `VoucherType`, `VoucherNo`, `RollNo`, `Admno/UniqueId`, `Status`, `FeeCategory`, `Faculty`, `Program`, `Department`, `Batch`, `ReceiptNo`, `FeeHead`, `DueAmount`, `PaidAmount`, `ConcessionAmount`, `ScholarshipAmount`, `ReverseConcessionAmount`, `WriteOffAmount`, `AdjustedAmount`, `RefundAmount`, `FundTranCferAmount`, `Remarks`) 
            VALUES ($placeholders)";

    // Prepare the statement only once per batch
    if (!mysqli_query($conn, $sql)) {
        echo "Error preparing statement: " . mysqli_error($conn);
        die();
    }
}