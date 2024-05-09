<?php
include_once('dbcon.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
    $fileName = $_FILES['csvFile']['tmp_name'];
    $startRow = 7; // Rows before this index will be skipped
    $batchSize = 1000; 
    // Open and read the CSV file
    if (($handle = fopen($fileName, "r")) !== FALSE) {
        $rowIndex = 0;
        $batchData = [];

        // Loop through each row in the CSV file
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $rowIndex++;

            // Skip rows until reaching the 7th row
            if ($rowIndex < $startRow) {
                continue;
            }

            // Exclude the first column (serial number/S.No.)
            $remainingValues = array_slice($data, 1);
            $batchData[] = $remainingValues;

            // Insert in batches of 1000
            if (count($batchData) == $batchSize) {
                insertBatch($conn, $batchData);
                $batchData = []; // Clear the batch array for the next set
            }
        }

        // Insert remaining rows if any exist after the last batch
        if (count($batchData) > 0) {
            insertBatch($conn, $batchData);
        }

        fclose($handle);
        echo "CSV data from the 7th row onwards has been successfully imported into the database.";
    } else {
        echo "Error opening the uploaded CSV file.";
    }
} else {
    echo "No file uploaded.";
}

function insertBatch($conn, $batchData) {
    if (empty($batchData)) {
        return;
    }

    // Assuming all rows have the same number of columns
    $columnsCount = count($batchData[0]);

    // Build column names and placeholders dynamically
    $columns = implode(", ", array_map(fn($i) => "column$i", range(1, $columnsCount)));
    $placeholders = implode(", ",$batchData[0]);
    print_r($placeholders);
    $sql = "INSERT INTO temp_csv_data (`Date`, `AcademicYear`, `Session`, `AllotedCategory`, `VoucherType`, `VoucherNo`, `RollNo`, `Admno/UniqueId`, `Status`, `FeeCategory`, `Faculty`, `Program`, `Department`, `Batch`, `ReceiptNo`, `FeeHead`, `DueAmount`, `PaidAmount`, `ConcessionAmount`, `ScholarshipAmount`, `ReverseConcessionAmount`, `WriteOffAmount`, `AdjustedAmount`, `RefundAmount`, `FundTranCferAmount`, `Remarks`) 
            VALUES ($placeholders)";

    // Prepare the statement only once per batch
    if (mysqli_query($conn, $sql)) {
        echo "done aaded";
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);;
    }
}