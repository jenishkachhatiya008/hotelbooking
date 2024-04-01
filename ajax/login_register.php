<?php
session_start();
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phonenum'];
    $pin = $_POST['pincode'];
    $dob = $_POST['dob'];
    $img = uploadUserImage($_FILES['profile']);
    $enc_pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);
    $password = $_POST['pass'];
    $cpass = $_POST['cpass'];
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);
    $hname = 'localhost';
    $uname = 'root';
    $dbpass = '';
    $db = 'hbwebsite';
    $connn = new mysqli($hname, $uname, $dbpass, $db);
    $sql = "SELECT * FROM `user_cred` WHERE `email` = '$email'";
    $vemail = $connn->query($sql);
    if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        echo 'inv_pass';
    }elseif ($vemail->num_rows > 0){
        echo 'email_already';
    } elseif ($password != $cpass){
        echo 'pass_mismatch';
    }else {
        $userdata = array(
            'email' => $email,
            'name' => $name,
            'address' => $address,
            'phone' => $phone,
            'pin' => $pin,
            'dob' => $dob,
            'img' => $img,
            'password' => $password,
            'cpass' => $cpass
        );
        $_SESSION['user_data'] = $userdata;
        var_dump($userdata);
        echo 'success';
    }
}

if (isset($_POST['login'])) {
    $data = filteration($_POST);

    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1",
        [$data['email_mob'], $data['email_mob']], "ss");

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email_mob';
    } else {
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['is_verified'] == 0) {
            echo 'not_verified';
        } else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } else {
            if (!password_verify($data['pass'], $u_fetch['password'])) {
                echo 'invalid_pass';
            } else {
                session_start();
                $_SESSION['login'] = true;
                $_SESSION['uId'] = $u_fetch['id'];
                $_SESSION['uName'] = $u_fetch['name'];
                $_SESSION['uPic'] = $u_fetch['profile'];
                $_SESSION['uPhone'] = $u_fetch['phonenum'];
                echo 1;
            }
        }
    }
}

if (isset($_POST['forgot_pass'])) {
    $data = filteration($_POST);

    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$data['email']], "s");

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email';
    } else {
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['is_verified'] == 0) {
            echo 'not_verified';
        } else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } else {
            // send reset link to email
            $token = bin2hex(random_bytes(16));

            if (!send_mail($data['email'], $token, 'account_recovery')) {
                echo 'mail_failed';
            } else {
                $date = date("Y-m-d");

                $query = mysqli_query($con, "UPDATE `user_cred` SET `token`='$token', `t_expire`='$date' 
            WHERE `id`='$u_fetch[id]'");

                if ($query) {
                    echo 1;
                } else {
                    echo 'upd_failed';
                }
            }
        }
    }

}

if (isset($_POST['recover_user'])) {
    $data = filteration($_POST);

    $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);

    $query = "UPDATE `user_cred` SET `password`=?, `token`=?, `t_expire`=? 
      WHERE `email`=? AND `token`=?";

    $values = [$enc_pass, null, null, $data['email'], $data['token']];

    if (update($query, $values, 'sssss')) {
        echo 1;
    } else {
        echo 'failed';
    }
}

?>