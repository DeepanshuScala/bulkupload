<?php
include_once('dbcon.php');
$getbranchesq = mysqli_query($conn,"SELECT * FROM branches");
$getbranches = $getbranchesq->fetch_all();
$_POST['case'] = 3;
switch($_POST['case']){
    case 1:
        $s = "SELECT DISTINCT Department FROM temp_csv_data";
        $r = mysqli_query($conn, $s);
        while($row = $r->fetch_assoc()){
            if(!empty($row['Department'])){
                $inser = "INSERT INTO branches(`branchname`) VALUES('".$row['Department']."')";
                $inserqu = mysqli_query($conn,$inser);
            }
        }
        echo json_encode(['status'=>200,'message'=>'Done']); 
        break;
    case 2:
        $s = "SELECT DISTINCT FEECATEGORY FROM temp_csv_data";
        $r = mysqli_query($conn,$s);
        while($row = $r->fetch_assoc()){
            if(!empty($row['FEECATEGORY'])){
                foreach($getbranches as $gr){
                    $insertq = mysqli_query($conn,"INSERT INTO feecategory(`fee_category`,`br_id`) VALUES('".$row['FEECATEGORY']."','".$gr[0]."')");
                }
            }
        }

        //add feecollectiontype also
        $staticv = ['academic','academicmisc','hostel','hostelmisc','transport','transportmisc'];
        foreach($staticv as $stcv){
            foreach($getbranches as $key => $gr){
                $insertq = mysqli_query($conn,"INSERT INTO feecollectiontype(`collection_head`,`collection_desc`,`br_id`) VALUES('".$stcv."','".$stcv."','".$gr[0]."')");
            }
        }
        echo json_encode(['status'=>200,'message'=>'Done']); 
        break;
    case 3:
        $s = "SELECT DISTINCT FeeHead FROM temp_csv_data";
        $r = mysqli_query($conn, $s);
        $i = 1;
        while($row = $r->fetch_assoc()){
            foreach($getbranches as $gr){
                $insertq = mysqli_query($conn,"INSERT INTO feetypes(`fee_category`,`f_name`,`collection_id`,`br_id`,`seq_id`,`fee_type_ledger`,`fee_headtype`) 
                            VALUES(1,'".$row['FeeHead']."',1,'".$gr[0]."',$i,'".$row['FeeHead']."',1)");
            }
            $i++;
        }
        echo json_encode(['status'=>200,'message'=>'Done']); 
        break;
    case 4:
        $transactionalarr = ['DUE','REVDUE','SCHOLARSHIP','CONCESSION','REVSCHOLARSHIP','REVCONCESSION'];
        $commonfeecollectionarr = ['RCPT','REVJV','PMT','Fundtransfer','REVRCPT','JV','REVPMT'];
        $perpage = 10000;
        $offset = $_POST['offset'];
        $s= mysqli_query($conn,"SELECT * FROM temp_csv_data LIMIT '.$offset.','.$perpage.'");
        while($row = $r->fetch_assoc()){
            
            //check for transactional
            if(in_array($row['VoucherType'],$transactionalarr)){
                $insertq = mysqli_query($conn,"INSERT INTO financial_trans(`moduleid`,`transid`,`admno`,`amount`,`crdr`,`transdate`,`acadyear`,`entrymode`,`voucherno`,`brid`,`typeofconcession`) 
                VALUES(1,'".$row['FeeHead']."',1,'".$gr[0]."',$i,'".$row['FeeHead']."',1)");

                $insertqtran = mysqli_query($conn,"INSERT INTO financial_transdetail(`financialtranid`,`moduleid`,`amount`,`headid`,`crdr`,`brid`,`head_name`) 
                    VALUES(1,'".$row['FeeHead']."',1,'".$gr[0]."',$i,'".$row['FeeHead']."',1)");
            }
            
            //check for commonfee_collection
            if(in_array($row['VoucherType'],$commonfeecollectionarr)){
                $insertq = mysqli_query($conn,"INSERT INTO common_fee_collection(`moduleid`,`transid`,`admno`,`rollno`,`amount`,`brid`,`academicyear`,`financialyear`,`displayreceiptno`,`entrymode`,`paiddate`,`inactive`) 
                VALUES(1,'".$row['FeeHead']."',1,'".$gr[0]."',$i,'".$row['FeeHead']."',1)");

                $insertqtran = mysqli_query($conn,"INSERT INTO common_fee_collection_headwise(`moduleid`,`receiptId`,`headid`,`headname`,`brid`,`amount`) 
                    VALUES(1,'".$row['FeeHead']."',1,'".$gr[0]."',$i,'".$row['FeeHead']."',1)");
            }
        }
}