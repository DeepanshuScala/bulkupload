<?php
include_once('dbcon.php');
$fileName = $_POST['filename'];
$startRow = $_POST['starting']; // Rows before this index will be skipped
$batchSize = 0; 
$batchData=[];

$file = fopen('temp/'.$fileName, 'r');

for($i = 0; $i < $startRow && (fgets($file) !== false); $i++);
while (($line = fgetcsv($file,0, ",")) !== FALSE) {
    $batchSize++;
    $startRow++;
    $remainingValues = array_slice($line, 1);
    $batchData[] = $remainingValues;
    
    insertBatch($conn, $batchData);
    if($batchSize == 20000){
        break;
    }
}
if($_POST['count']>$startRow){
    echo json_encode(['status'=>200,'more'=>1,'filename'=>$fileName,'starting'=>$startRow,'count'=>$_POST['count']]); 
}
else{
    echo json_encode(['status'=>200,'message'=>'Done uploading']); 
}

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

    $placeholders = implode(", ",array_map(function($value) {return '"' . $value . '"';}, $batchData[0]));
    //print_r($placeholders);
    $sql = "INSERT INTO temp_csv_data (`Date`, `AcademicYear`, `Session`, `AllotedCategory`, `VoucherType`, `VoucherNo`, `RollNo`, `Admno/UniqueId`, `Status`, `FeeCategory`, `Faculty`, `Program`, `Department`, `Batch`, `ReceiptNo`, `FeeHead`, `DueAmount`, `PaidAmount`, `ConcessionAmount`, `ScholarshipAmount`, `ReverseConcessionAmount`, `WriteOffAmount`, `AdjustedAmount`, `RefundAmount`, `FundTranCferAmount`, `Remarks`) 
            VALUES ($placeholders)";

    // Prepare the statement only once per batch
    if (!mysqli_query($conn, $sql)) {
        echo "Error preparing statement: " . mysqli_error($conn);
        die();
    }
}