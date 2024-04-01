<?php

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
//  require('inc/paytm/config_paytm.php');
//  require('inc/paytm/encdec_paytm.php');
use PHPMailer\PHPMailer\PHPMailer;

require("PHPMailer/Exception.php");
require("PHPMailer/PHPMailer.php");
require("PHPMailer/SMTP.php");
date_default_timezone_set("Asia/Kolkata");

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

if (isset($_POST['pay_now'])) {
    header("Pragma: no-cache");
    header("Cache-Control: no-cache");
    header("Expires: 0");

    $checkSum = "";

    $ORDER_ID = 'ORD_' . $_SESSION['uId'] . random_int(11111, 9999999);
    $CUST_ID = $_SESSION['uId'];
//    $INDUSTRY_TYPE_ID = INDUSTRY_TYPE_ID;
//    $CHANNEL_ID = CHANNEL_ID;
    $TXN_AMOUNT = $_SESSION['room']['payment'];
    function sendMail($email, $token)
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
            $mail->setFrom('hotelsbooking8386@gmail.com', 'Bookoing Verification ');
            $mail->addAddress($email);     //Add a recipient

            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Booking Verification';
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
        <p>Thank you for HB. Use the following Link to
            complete your Booking procedures.</p>
        <a href="http://localhost/hbwebsite/link_verification.php?token='.$token.'" style="background: #00466a;margin: 50px;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">Booking Verification</a>
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

    // Create an array having all required parameters for creating checksum.

//    $paramList = array();
//    $paramList["MID"] = PAYTM_MERCHANT_MID;
//    $paramList["ORDER_ID"] = $ORDER_ID;
//    $paramList["CUST_ID"] = $CUST_ID;
//    $paramList["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
//    $paramList["CHANNEL_ID"] = $CHANNEL_ID;
//    $paramList["TXN_AMOUNT"] = $TXN_AMOUNT;
//    $paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
//    $paramList["CALLBACK_URL"] = CALLBACK_URL;


    //Here checksum string will return by getChecksumFromArray() function.
//    $checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);

    // Insert payment data into database

    $frm_data = filteration($_POST);

    $query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`,`order_id`) VALUES (?,?,?,?,?)";

    insert($query1, [$CUST_ID, $_SESSION['room']['id'], $frm_data['checkin'],
        $frm_data['checkout'], $ORDER_ID], 'issss');

    $booking_id = mysqli_insert_id($con);

    $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`,
      `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";

    insert($query2, [$booking_id, $_SESSION['room']['name'], $_SESSION['room']['price'],
        $TXN_AMOUNT, $frm_data['name'], $frm_data['phonenum'], $frm_data['address']], 'issssss');
    $hname = 'localhost';
    $uname = 'root';
    $dbpass = '';
    $db = 'hbwebsite';
    $connn = new mysqli($hname, $uname, $dbpass, $db);
    $sql = "SELECT * FROM `hbwebsite`.`user_cred` WHERE `id` = $CUST_ID";
    $useremail = $connn->query($sql);
    $useremail = $useremail->fetch_assoc()['email'];
    $key = "ThisIsASecretKey1234567890"; // Should be 32 bytes for AES-256
    $iv = "ThisIsAnIV123456";
    $encrypted_booking_id = openssl_encrypt($booking_id, 'aes-256-cbc', $key, 0, $iv);
    sendMail($useremail,$encrypted_booking_id);
    echo "<script>window.location.href = 'index.php'</script>";
}

?>

<!--<html>-->
<!--  <head>-->
<!--    <title>Processing</title>-->
<!--  </head>-->
<!--  <body>-->
<!---->
<!--		<h1>Please do not refresh this page...</h1>-->
<!---->
<!--		<form method="post" action="--><?php //echo PAYTM_TXN_URL ?><!--" name="f1">-->
<!--			--><?php
//			foreach($paramList as $name => $value) {
//				echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
//			}
//			?>
<!--			<input type="hidden" name="CHECKSUMHASH" value="--><?php //echo $checkSum ?><!--">-->
<!--		</form>-->
<!---->
<!--		<script type="text/javascript">-->
<!--			document.f1.submit();-->
<!--		</script>-->
<!---->
<!---->
<!--        <button id="rzp-button1">Pay</button>-->
<!--        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>-->
<!--        <script>-->
<!--            document.getElementById('rzp-button1').onclick = function(e){-->
<!--                var options = {-->
<!--                    "key": "rzp_test_yRiou18lW0WTzS", // Enter the Key ID generated from the Dashboard-->
<!--                    "amount": "--><?php //echo $_SESSION['room']['payment'] * 100; ?>//", // Amount is in currency subunits. Convert INR to paise
//                    "currency": "INR",
//                    "name": "Your Company Name",
//                    "description": "Room Booking Payment",
//                    "image": "https://example.com/your_logo",
//                    "handler": function (response){
//                        // Handle the payment success
//                        alert("Payment Successful: " + response.razorpay_payment_id);
//                        // You may want to redirect or perform other actions here
//                    },
//                    "prefill": {
//                        "name": "<?php //echo $_SESSION['user']['name']; ?>////",
//                        "email": "<?php //echo $_SESSION['user']['email']; ?>////",
//                        "contact": "<?php //echo $_SESSION['user']['phone']; ?>////"
//                    },
//                    "notes": {
//                        "address": "<?php //echo $_SESSION['user']['address']; ?>////"
//                    },
//                    "theme": {
//                        "color": "#3399cc"
//                    }
//                };
//                var rzp1 = new Razorpay(options);
//                rzp1.open();
//                e.preventDefault();
//            };
//        </script>
//
//  </body>
//</html>