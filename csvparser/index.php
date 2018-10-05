<?php
   $conn = mysqli_connect("RDS_ENDPOINT.rds.amazonaws.com", "USER", "PASSWORD", "DBNAME");
   
   if (isset($_POST["import"])) {
   
       $fileName = $_FILES["file"]["tmp_name"];
   
       if ($_FILES["file"]["size"] > 0) {
   
           $file = fopen($fileName, "r");
   
           while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
               $sqlInsert = "INSERT IGNORE into TABLENAME (sku,name,price) values ('$column[0]','$column[1]','$column[2]')";
               $result    = mysqli_query($conn, $sqlInsert);
   
               if (!empty($result)) {
                   $type    = "success";
                   $message = "CSV Data Imported into the Database";
               } else {
                   $type    = "error";
                   $message = "Problem in Importing CSV Data";
               }
           }
       }
   }
   ?>
<!DOCTYPE html>
<html>
   <head>
      <script src="https://sdk.amazonaws.com/js/aws-sdk-2.283.1.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script src="js/process-csv.js"></script>
      <style>
         body {
         font-family: Arial;
         width: 550px;
         }
         .outer-scontainer {
         background: #F0F0F0;
         border: #e0dfdf 1px solid;
         padding: 20px;
         border-radius: 2px;
         }
         .input-row {
         margin-top: 0px;
         margin-bottom: 20px;
         }
         .btn-submit {
         background: #333;
         border: #1d1d1d 1px solid;
         color: #f0f0f0;
         font-size: 0.9em;
         width: 100px;
         border-radius: 2px;
         cursor: pointer;
         }
         .outer-scontainer table {
         border-collapse: collapse;
         width: 100%;
         }
         .outer-scontainer th {
         border: 1px solid #dddddd;
         padding: 8px;
         text-align: left;
         }
         .outer-scontainer td {
         border: 1px solid #dddddd;
         padding: 8px;
         text-align: left;
         }
         #response {
         padding: 10px;
         margin-bottom: 10px;
         border-radius: 2px;
         display: none;
         }
         .success {
         background: #c7efd9;
         border: #bbe2cd 1px solid;
         }
         .error {
         background: #fbcfcf;
         border: #f3c6c7 1px solid;
         }
         div#response.display-block {
         display: block;
         }
      </style>
      <script type="text/javascript">
         $(document).ready(function() {
                     $("#frmCSVImport").on("submit", function() {
         
                             $("#response").attr("class", "");
                             $("#response").html("");
                             var fileType = ".csv";
                             var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + fileType + ")$");
                             if (!regex.test($("#file").val().toLowerCase())) {
                                 $("#response").addClass("error");
                                 $("#response").addClass("display-block");
                                 $("#response").html("Invalid File. Upload :  < b > " + fileType + " < /b> Files.");
                                     return false;
                                 }
                                 return true;
                             });
                     });
      </script>
      <script type="text/javascript">
         $(document).ready(function() {
         $(document).on('click', '#update', function() {
         var name = $(this).closest("tr").find('td:eq(1)').text().trim();
         var price = $(this).closest("tr").find('td:eq(2)').text().trim();
         var sku = $(this).closest("tr").find('td:eq(0)').text().trim();
         console.log(sku);
         console.log(name);
         console.log(price);
           $.ajax({
                 type: "POST",
                 url: "update.php",
                 data: { sku:sku, name:name, price:price },
                 success: function(response) {
         $("span").html(response);
         console.log("update php called.")
                 }
             });
          });
         });
               
      </script>
   </head>
   <body>
      <h2>PowerupCloud Demo: CSV to MySQL</h2>
      <div id="response" class="
         <?php
            if (!empty($type)) {
                echo $type . " display-block ";
            }
            ?>">
         <?php
            if (!empty($message)) {
                echo $message;
            }
            ?>
      </div>
      <div class="outer-scontainer">
      <div class="row">
      <form class="form-horizontal" action="" method="post" name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data">
         <div class="input-row">
            <label class="col-md-4 control-label">Choose CSV File
            </label>
            <input type="file" name="file" id="file" accept=".csv">
            <button type="submit" id="submit" name="import" class="btn-submit" onclick="uploadToS3()">Import</button>
            <br />
         </div>
      </form>
      <form action="" method="post" enctype="multipart/form-data">
         <div class="input-row">
            <button type="button" id="view" name="view" class="btn-submit" onclick="viewContents()">View Files</button>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" id="submit" name="viewdata" class="btn-submit">View Data</button>
            <br />
         </div>
         <span></span>
         <div class="row">
         <a style="display:block" href="#">
            <div class="col-lg-8" id="output">
         </a>
         </div>
      </form>
      <?php
         if (isset($_POST["viewdata"])) {
         $sqlSelect = "SELECT * FROM TABLENAME";
         $result = mysqli_query($conn, $sqlSelect);
         
         if (mysqli_num_rows($result) > 0) {
         ?>
      <table id='TABLENAME'>
         <thead>
            <tr>
               <th>sku</th>
               <th>name</th>
               <th>price</th>
            </tr>
         </thead>
         <?php
            while ($row = mysqli_fetch_array($result)) {
            $sku = $row['sku'];
            $name = $row['name'];
            $price = $row['price'];
            ?>
         <tbody>
            <tr>
               <td contenteditable="true">
                  <?php  echo $row['sku']; ?>
               </td>
               <td contenteditable="true">
                  <?php  echo $row['name']; ?>
               </td>
               <td contenteditable="true" id="id3">
                  <?php  echo $row['price']; ?>
               </td>
               <td>
                  <button type="button" id="update" name="update">Archive</button>
               </td>
               <form action="" method="post" enctype="multipart/form-data">
                  <input name="sku" type="hidden" value="
                     <?php echo $row['sku'];?>" />
                  <input name="name" type="hidden" value="
                     <?php echo $row['name'];?>" />
                  <input name="price" type="hidden" value="
                     <?php echo $row['price'];?>" />
                  <td>
                     <button class="deletebtn" id="submit" name="delete">Delete</button>
                  </td>
               </form>
            </tr>
            <?php
               }
               ?>
         </tbody>
      </table>
      <?php }
         }
         ?>
      <?php
         if (isset($_POST["delete"])) {
               $id=$_REQUEST['sku'];
               $sqlDelete="delete from TABLENAME where sku ='$id';";
               $queryresult=mysqli_query($conn, $sqlDelete);
         
         if (!empty($queryresult)) {
                               $type    = "success";
                               $message = "Database Updated.";
               		 echo $message;
               $sqlSelect = "SELECT * FROM TABLENAME";
               $result = mysqli_query($conn, $sqlSelect);
         
               if (mysqli_num_rows($result) > 0) {
               ?>
      <table id='TABLENAME'>
         <thead>
            <tr>
               <th>sku</th>
               <th>name</th>
               <th>price</th>
            </tr>
         </thead>
         <?php
            while ($row = mysqli_fetch_array($result)) {
            $sku = $row['sku'];
            $name = $row['name'];
            $price = $row['price'];
            ?>
         <tbody>
            <tr>
               <td contenteditable="true">
                  <?php  echo $row['sku']; ?>
               </td>
               <td contenteditable="true">
                  <?php  echo $row['name']; ?>
               </td>
               <td contenteditable="true">
                  <?php  echo $row['price']; ?>
               </td>
               <td>
                  <button type="button" id="update" name="update">Archive</button>
               </td>
               <form action="" method="post" enctype="multipart/form-data">
                  <input name="sku" type="hidden" value="
                     <?php echo $row['sku'];?>" />
                  <input name="name" type="hidden" value="
                     <?php echo $row['name'];?>" />
                  <input name="price" type="hidden" value="
                     <?php echo $row['price'];?>" />
                  <td>
                     <button class="deletebtn" id="submit" name="delete">Delete</button>
                  </td>
               </form>
            </tr>
            <?php
               }
               ?>
         </tbody>
      </table>
      <?php
         }
         else{}
         }}
         else {
                     $type    = "error";
                     $message = "Problem in Deleting Data Record";
                 }
          ?>
   </body>
</html>
