<?php
$sku=$_POST['sku'];
$name=$_POST['name'];
$price=$_POST['price'];
$conn=mysqli_connect("RDS_ENDPOINT.rds.amazonaws.com", "USER", "PASSWORD", "DBNAME");
$query = "UPDATE TABLENAME SET name='$name',price='$price' WHERE sku='$sku';";
$result    = mysqli_query($conn, $query);

               if (!empty($result)) {
                   $type    = "success";
                   $message = "CSV Data Updated into the Database";
echo $message;
               } else {
                   $type    = "error";
                   $message = "Problem in Updating CSV Data";
echo $message;
               }
?>
