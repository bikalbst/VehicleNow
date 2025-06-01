<?php
if(isset($_POST['addcar']) ){
    require_once('connection.php');
    // Removed debugging print_r for cleaner output
    // echo "<prev>";
    // print_r($_FILES['image']);
    // echo "</prev>";
    $img_name= $_FILES['image']['name'];
    $tmp_name= $_FILES['image']['tmp_name'];
    $error= $_FILES['image']['error'];

    if($error === 0){
        $img_ex = pathinfo($img_name,PATHINFO_EXTENSION);
        $img_ex_lc= strtolower($img_ex);

        $allowed_exs = array("jpg","jpeg","png","webp","svg");
        if(in_array($img_ex_lc,$allowed_exs)){
            $new_img_name = uniqid("IMG-",true).'.'.$img_ex_lc;
            $img_upload_path = 'images/'.$new_img_name;
            
            // Ensure the images directory exists and is writable
            if (!is_dir('images')) {
                mkdir('images', 0755, true);
            }

            if(move_uploaded_file($tmp_name, $img_upload_path)){
                $carname = mysqli_real_escape_string($con, $_POST['carname']);
                $ftype = mysqli_real_escape_string($con, $_POST['ftype']);
                $capacity = (int)$_POST['capacity']; // Cast to integer
                $price = (float)$_POST['price']; // Cast to float
                
                // Get the new fields
                $modelyear = (int)$_POST['modelyear']; // Cast to integer
                $transmission = mysqli_real_escape_string($con, $_POST['transmission']);
                $cartype = mysqli_real_escape_string($con, $_POST['cartype']);
                
                $available = "Y"; // Default availability

                // Updated query to include new fields
                $query = "INSERT INTO cars (CAR_NAME, FUEL_TYPE, CAPACITY, PRICE, CAR_IMG, AVAILABLE, MODEL_YEAR, TRANSMISSION, CAR_TYPE) 
                          VALUES ('$carname', '$ftype', $capacity, $price, '$new_img_name', '$available', $modelyear, '$transmission', '$cartype')";
                
                $res = mysqli_query($con, $query);
                if($res){
                    echo '<script>alert("New Car Added Successfully!!")</script>';
                    echo '<script> window.location.href = "adminvehicle.php";</script>';
                } else {
                    // Provide more detailed error for debugging (optional, remove in production)
                    echo '<script>alert("Error adding car: ' . mysqli_error($con) . '")</script>';
                    echo '<script> window.location.href = "addcar.php";</script>'; 
                }
            } else {
                echo '<script>alert("Failed to move uploaded file.")</script>';
                echo '<script> window.location.href = "addcar.php";</script>';
            }
        } else {
            echo '<script>alert("Cannot upload this type of image. Allowed types: jpg, jpeg, png, webp, svg.")</script>';
            echo '<script> window.location.href = "addcar.php";</script>';   
        }
    } else {
        $em = "Unknown error occurred during file upload."; // More specific error message
        // Redirect with error (consider logging detailed errors instead of exposing via GET)
        echo '<script>alert("'.$em.' Error code: '.$error.'")</script>';
        echo '<script> window.location.href = "addcar.php";</script>';
    }
} else {
    // If the form wasn't submitted correctly, redirect back or show an error.
    // For security, avoid echoing "false" or similar vague messages.
    header("Location: addcar.php");
    exit();
}
?>
