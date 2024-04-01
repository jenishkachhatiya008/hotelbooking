<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require("PHPMailer/Exception.php");
require("PHPMailer/PHPMailer.php");
require("PHPMailer/SMTP.php");

date_default_timezone_set("Asia/Kolkata");

function sendMail($email, $otp)
{

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'hotelsbooking8386@gmail.com';                     //SMTP username   quickcarhire.india@gmail.com
        $mail->Password = 'fhzkavlgdsugybcd';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = phpmailer::ENCRYPTION_STARTTLS`
//Recipients
        $mail->setFrom('hotelsbooking8386@gmail.com', 'OTP Verification ');
        $mail->addAddress($email);     //Add a recipient

        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'OTP verification Hotel Booking';
        $mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div
        style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
    <div style="margin:50px auto;width:70%;padding:20px 0">
        <div style="border-bottom:1px solid #eee">
            <a href
               style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">
                Hotel Booking</a>
        </div>
        <p style="font-size:1.1em">Hi,</p>
        <p>Thank you for HB. Use the following OTP to
            complete your Sign Up procedures. OTP is valid for 5 minutes</p>
        <h2
                style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">' . $otp . '</h2>
        <p style="font-size:0.9em;">Regards,<br />Hotel Booking System</p>
        <hr style="border:none;border-top:1px solid #eee" />
        <div
                style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
            <p>HB</p>
            <img src="cid:image_cid" alt="car" height="50" width="50">
        </div>
    </div>
</div>
</body>
</html>';
        $mail->send();
    } catch (Exception $e) {
        echo "<script> alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}')</script>";
    }
}
$user_data = $_SESSION['user_data'];
$email = $user_data['email'];
$name = $user_data['name'];
$address = $user_data['address'];
$phone = $user_data['phone'];
$pin = $user_data['pin'];
$dob = $user_data['dob'];
$img = $user_data['img'];
$password = $user_data['password'];
$cpass = $user_data['cpass'];
$enc_pass = password_hash($password, PASSWORD_BCRYPT);
$hname = 'localhost';
$uname = 'root';
$dbpass = '';
$db = 'hbwebsite';
$connn = new mysqli($hname, $uname, $dbpass, $db);
$otp = rand(1000, 9999);
$_SESSION['Emailid'] = $email;
$sql = "INSERT INTO `otp`(`email`, `otp`) VALUES ('$email','$otp')";
$connn->query($sql);
echo $email;
sendMail($email,$otp);
$currentDateTime = date("Y-m-d H:i:s");
$sql = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `is_verified`, `status`, `datentime`)
    VALUES ('$name','$email','$address','$phone','$pin','$dob','$img','$enc_pass','0','1','$currentDateTime')";
$connn->query($sql);
//echo '<script>window.location.href = "otpVerification.php"</script>';