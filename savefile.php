<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
    $uploadDir = 'temp/'; // Change this to the directory on your server
    $filename = time().basename($_FILES['csvFile']['name']);
    $uploadFile = $uploadDir . $filename;
    $rowIndex = 0;
    $startRow=7;
    if(move_uploaded_file($_FILES['csvFile']['tmp_name'], $uploadFile)) { 
        $file = fopen($uploadFile, 'r'); 
        while (fgets($file) !== false) $rowIndex++;
        echo json_encode(['status'=>200,'filename'=>$filename,'count'=>$rowIndex,'message'=>'Uploaded Successfully']);    
    }
    else{
        echo json_encode(['status'=>400,'message'=>'Issue while uplaoding']); 
    }
} else {
    echo json_encode(['status'=>400,'message'=>"No file uploaded"]); 
}
