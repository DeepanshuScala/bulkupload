<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funeral Scheduler</title>
    <!-- Link to Bootstrap CSS -->
    <link
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
        /* Styling for the loader */
        .loader-container {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            text-align: center;
            padding-top: 20%;
        }

        .loader {
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader-container" id="loader">
        <div class="loader"></div>
    </div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>CSV File Uploader</h4>
                    </div>
                    <div class="card-body">
                        <form action="savefile.php" id="csvform" method="post" enctype='multipart/form-data'>
                            <div class="form-group">
                                <label for="csvFile">Upload CSV File</label>
                                <input type="file" name="csvFile" class="form-control-file" id="csvFile" accept=".csv" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                        <div class="datashow" style="display:none;">
                            <p class="totalcount"></p>
                            <p class="dueamoount"></p>
                            <p class="paidamoount"></p>
                            <p class="concessionamoount"></p>
                            <p class="scholarshipamount"></p>
                            <p class="refundamount"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Link to Bootstrap JavaScript (Optional but useful for interactivity) -->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"
    ></script>
    <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
    ></script>
    <script>
        $(document).ready(function(){
            $("#csvform").on("submit",function(e){
                e.preventDefault();
                $(".loader-container").show();
                var form = $('#csvform')[0]; // You need to use standard javascript object here
                var formData = new FormData(form);
                console.log(formData,'form data');
                e.preventDefault();
                $.ajax({
                    url:$(this).attr('action'),
                    method:'POST',
                    data:formData,
                    contentType: false,
                    processData: false,
                }).done(function (response, status, xhr) {
                    $(".loader-container").hide();
                    let r = JSON.parse(response);
                    getcsvdetails(r.filename,6,r.count,0,0,0,0,0);
                    $("#csvform").hide();
                }).fail(function (xhr, ajaxOptions, responseJSON, thrownError) {
                    $(".loader-container").hide();
                    alert("Something went wrong.");
                    window.reload();
                })
            })
        });
        function getcsvdetails(filename,starting,count,dueamount,paidamoount,concessionamoount,scholarshipamount,refundamount){
            $(".loader-container").show();
            $.ajax({
                url:'upload.php',
                method:'POST',
                data:{filename:filename,starting:starting,count:count,dueamount:dueamount,paidamoount:paidamoount,concessionamoount:concessionamoount,scholarshipamount:scholarshipamount,refundamount:refundamount},
            }).done(function(response, status, xhr){
                let r = JSON.parse(response);
                $(".totalcount").html("Count of records- "+r.starting);
                $(".dueamoount").html("Sum of Due Amount- "+r.dueamount);
                $(".paidamoount").html("Sum of paid Amount- "+r.paidamoount);
                $(".concessionamoount").html("Sum of Concession- "+r.concessionamoount);
                $(".scholarshipamount").html("Sum of Scholarship- "+r.scholarshipamount);
                $(".refundamount").html("Sum of Refund- "+r.refundamount);
                if(r.more == 1){
                    getcsvdetails(filename,r.starting,count,r.dueamount,r.paidamoount,r.concessionamoount,r.scholarshipamount,r.refundamount);
                }
                else{
                    $(".loader-container").hide();
                }
                $(".datashow").show();
            }).fail(function (xhr, ajaxOptions, responseJSON, thrownError) {
                $(".loader-container").hide();
                alert("Something went wrong.");
                window.reload();
            })
        }
    </script>
</body>
</html>
