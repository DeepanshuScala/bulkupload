<?php
ini_set('display_errors', 0);
include_once('dbcon.php');
$getbranchesq = mysqli_query($conn,"SELECT * FROM branches");
$getbranches = $getbranchesq->fetch_all();
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
            if(!empty($row['FeeHead'])){
                $feetypehead = (strpos($row['FeeHead'],"Hostel") > -1)?2:1;
                foreach($getbranches as $gr){
                    $insertq = mysqli_query($conn,"INSERT INTO feetypes(`fee_category`,`f_name`,`collection_id`,`br_id`,`seq_id`,`fee_type_ledger`,`fee_headtype`) 
                                VALUES(1,'".$row['FeeHead']."',1,'".$gr[0]."',$i,'".$row['FeeHead']."',".$feetypehead.")");
                }
                $i++;
            }
        }
        echo json_encode(['status'=>200,'message'=>'Done']); 
        break;
    case 4:
        $transactionalarr = ['DUE','REVDUE','SCHOLARSHIP','CONCESSION','REVSCHOLARSHIP','REVCONCESSION'];
        $commonfeecollectionarr = ['RCPT','REVJV','PMT','Fundtransfer','REVRCPT','JV','REVPMT'];
        $perpage = 10000;
        $offset = $_POST['offset'];
        $s= mysqli_query($conn,"SELECT * FROM temp_csv_data LIMIT $offset,$perpage");
        while($row = $s->fetch_assoc()){
            $amount = 0;
            $crdr = '';
            $entrymode = 0;
        
            $transactionid = time();
            $feetypehead = (strpos($row['FeeHead'],"Hostel") > -1)?2:1;
            $branchinfo = mysqli_query($conn,"SELECT * FROM branches WHERE `branchname` = '".$row['Department']."'");
            $branchinfro = $branchinfo->fetch_all();
            $feetypeinfo = mysqli_query($conn,"SELECT * FROM feetypes WHERE `f_name` = '".$row['FeeHead']."' AND `br_id` = '".$branchinfro[0][0]."'");
            $feetypeinfro = $feetypeinfo->fetch_all();
            //check for transactional
            if(in_array($row['VoucherType'],$transactionalarr)){
                $concession = null;
                if($row['VoucherType'] == 'DUE'){
                    $crdr = 'D';
                    $amount = $row['DueAmount'];
                }
                elseif($row['VoucherType'] == 'REVDUE'){
                    $crdr = 'C';
                    $entrymode = 12;
                    $amount = $row['WriteOffAmount'];
                }
                elseif($row['VoucherType'] == 'SCHOLARSHIP'){
                    $crdr = 'C';
                    $concession = 1;
                    $entrymode = 15;
                    $amount = $row['ScholarshipAmount'];
                }
                elseif($row['VoucherType'] == 'CONCESSION'){
                    $crdr = 'C';
                    $concession = 1;
                    $entrymode = 15;
                    $amount = $row['ConcessionAmount'];
                }
                elseif($row['VoucherType'] == 'REVSCHOLARSHIP' || $row['VoucherType'] == 'REVCONCESSION'){
                    $crdr = 'D';
                    $entrymode = 16;
                    $amount = $row['ReverseConcessionAmount'];
                }
                
                $insertq = mysqli_query($conn,"INSERT INTO financial_trans(`moduleid`,`transid`,`admno`,`amount`,`crdr`,`transdate`,`acadyear`,`entrymode`,`voucherno`,`brid`,`typeofconcession`) 
                VALUES('".$feetypehead."','".$transactionid."','".$row['Admno/UniqueId']."','".$amount."','".$crdr."','".$row['Date']."','".$row['AcademicYear']."','".$entrymode."','".$row['VoucherNo']."','".$branchinfro[0][0]."','".$concession."')");

                $insertqtran = mysqli_query($conn,"INSERT INTO financial_transdetail(`financialtranid`,`moduleid`,`amount`,`headid`,`crdr`,`brid`,`head_name`) 
                    VALUES('".mysqli_insert_id($conn)."','".$feetypehead."','".$amount."','".$feetypeinfro[0][0]."','".$crdr."','".$branchinfro[0][0]."','".$row['FeeHead']."')");
            }
            
            //check for commonfee_collection
            if(in_array($row['VoucherType'],$commonfeecollectionarr)){
                $inactive = '';
                if($row['VoucherType'] == 'RCPT' || $row['VoucherType'] == 'REVRCPT'){
                    $crdr = 'D';
                    $inactive = ($row['VoucherType'] == 'RCPT')?0:1;
                    $amount = $row['PaidAmount'];
                }
                elseif($row['VoucherType'] == 'JV'){
                    $crdr = 'C';
                    $entrymode = 14;
                    $inactive = 0;
                    $amount = $row['AdjustedAmount'];
                }
                elseif($row['VoucherType'] == 'REVJV'){
                    $crdr = 'D';
                    $entrymode = 14;
                    $inactive = 1;
                    $amount = $row['AdjustedAmount'];
                }
                elseif($row['VoucherType'] == 'PMT'){
                    $crdr = 'D';
                    $entrymode = 1;
                    $inactive = 0;
                    $amount = $row['RefundAmount'];
                }
                elseif($row['VoucherType'] == 'REVPMT'){
                    $crdr = 'C';
                    $entrymode = 1;
                    $inactive = 1;
                    $amount = $row['RefundAmount'];
                }
                elseif($row['VoucherType'] == 'Fundtransfer'){
                    $crdr = '+ ve and -ve';
                    $entrymode = 1;
                    $amount = $row['FundTranCferAmount'];
                }

                $insertq = mysqli_query($conn,"INSERT INTO common_fee_collection(`moduleid`,`transid`,`admno`,`rollno`,`amount`,`brid`,`academicyear`,`financialyear`,`displayreceiptno`,`entrymode`,`paiddate`,`inactive`) 
                VALUES('".$feetypehead."','".$transactionid."','".$row['Admno/UniqueId']."','".$row['RollNo']."','".$amount."','".$branchinfro[0][0]."','".$row['AcademicYear']."','".$row['Session']."','".$row['ReceiptNo']."','".$entrymode."','".$row['Date']."','".$inactive."')");

                $insertqtran = mysqli_query($conn,"INSERT INTO common_fee_collection_headwise(`moduleid`,`receiptId`,`headid`,`headname`,`brid`,`amount`) 
                    VALUES('".$feetypehead."','".mysqli_insert_id($conn)."','".$feetypeinfro[0][0]."','".$row['FeeHead']."','".$branchinfro[0][0]."','".$amount."')");
            }
            $offset++;
        }
        $s1= mysqli_query($conn,"SELECT count(*) as total FROM temp_csv_data");
        $allcount = $s1->fetch_all();
        if($allcount[0][0] > $offset){
            echo json_encode(['status'=>200,'more'=>1,'offset'=>$offset]); 
        }
        else{
            echo json_encode(['status'=>200,'message'=>'Done uploading']); 
        }
        break;
}