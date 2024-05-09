<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCloudEMS</title>
    <!-- Link to Bootstrap CSS -->
    <link
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>CSV File Uploader</h4>
                    </div>
                    <div class="card-body">
                        <form action="upload.php" method="post" enctype='multipart/form-data'>
                            <div class="form-group">
                                <label for="csvFile">Upload CSV File</label>
                                <input type="file" name="csvFile" class="form-control-file" id="csvFile" accept=".csv">
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Link to Bootstrap JavaScript (Optional but useful for interactivity) -->
    <script
      src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"
    ></script>
    <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
    ></script>
</body>
</html>
