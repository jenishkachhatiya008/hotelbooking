<?php
$static_key = "ThisIsASecretKey1234567890";
$static_iv = "ThisIsAnIV123456";
$encrypted_booking_id = $_GET['token'];
$decrypted_booking_id = openssl_decrypt($encrypted_booking_id, 'aes-256-cbc', $static_key, 0, $static_iv);
echo $decrypted_booking_id;
$hname = 'localhost';
$uname = 'root';
$dbpass = '';
$db = 'hbwebsite';
$connn = new mysqli($hname, $uname, $dbpass, $db);
$sql = "SELECT * FROM `booking_order` WHERE `booking_id` = $decrypted_booking_id";
echo $sql;
$booking = $connn->query($sql);
if($booking->num_rows > 0){
    $sql = "UPDATE `booking_order` SET `booking_status` = 'booked' WHERE `booking_id`=$decrypted_booking_id";
    $connn->query($sql);
    echo "<script>alert('Booking Verified Successful.')</script>";
    echo "<script>window.location.href = 'index.php'</script>";
}else{
    echo "<script>alert('Invalid Key');
            window.location.href = 'index.php';
            </script>";
}