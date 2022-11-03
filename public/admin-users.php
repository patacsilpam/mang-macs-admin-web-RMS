<?php
require 'public/admin-inventory.php';
if (!isset($_SESSION['loggedIn'])) {
    header('Location:login.php');
}
date_default_timezone_set('Asia/Manila');
$unameError = $emailError = $pwordError = "";
function insertUsers(){
    require 'public/connection.php';
    if(isset($_SERVER["REQUEST_METHOD"]) == "POST"){
        if (isset($_POST['btn-save-user'])) {
            $id = mysqli_real_escape_string($connect, $_POST['id']);
            $fname = mysqli_real_escape_string($connect, $_POST['fname']);
            $lname = mysqli_real_escape_string($connect, $_POST['lname']);
            $uname = mysqli_real_escape_string($connect, $_POST['uname']);
            $email = mysqli_real_escape_string($connect, $_POST['email']);
            $password = mysqli_real_escape_string($connect, $_POST['password']);
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $profile = "no image";
            $position = mysqli_real_escape_string($connect, $_POST['position']);
            $created_at = date('y-m-d h:i:s');
            $verification_code = 0;
            //check username and email
            $check_uname_email = $connect->prepare("SELECT * FROM tblusers WHERE uname=? OR email=?");
            $check_uname_email->bind_param('ss', $uname, $email);
            $check_uname_email->execute();
            $row = $check_uname_email->get_result();
            $fetch = $row->fetch_assoc();
            //check if username already exists
            if ($row->num_rows == 1) {
                if ($uname == $fetch['uname']) {
                    header('Location:users.php?username_already_exist');
                }
                if ($email == $fetch['email']) {
                    header('Location:users.php?email_already_exist');
                }
            }
            //if validation is correct insert data values to database
            else {
                $insertUser = $connect->prepare("INSERT INTO tblusers(id,fname,lname,uname,email,user_password,profile,position,created_at,verification_code)
                VALUES(?,?,?,?,?,?,?,?,?,?)");
                $insertUser->bind_param('issssssssi', $id, $fname, $lname, $uname, $email, $passwordHash, $profile, $position, $created_at, $verification_code);
                $insertUser->execute();
                if ($insertUser) {
                    header('Location:users.php?inserted');
                } else{
                    header('Location:users.php?error');
                }
            } 
        }
    }
}
function editUsers(){
    require 'public/connection.php';
    if(isset($_SERVER["REQUEST_METHOD"]) == "POST"){
        if (isset($_POST['btn-edit-user'])) {
            $id = mysqli_real_escape_string($connect, $_POST['id']);
            $fname = mysqli_real_escape_string($connect, $_POST['fname']);
            $lname = mysqli_real_escape_string($connect, $_POST['lname']);
            $uname = mysqli_real_escape_string($connect, $_POST['uname']);
            $email = mysqli_real_escape_string($connect, $_POST['email']);
            $position = mysqli_real_escape_string($connect, $_POST['position']);
            $password = mysqli_real_escape_string($connect,$_POST['password']);
            $passwordHash = password_hash($password,PASSWORD_DEFAULT);
            $created_at = date('y-m-d h:i:s');
            //check username and email
            $check_uname_email = $connect->prepare("SELECT * FROM tblusers WHERE uname=? OR email=?");
            $check_uname_email->bind_param('ss', $uname, $email);
            $check_uname_email->execute();
            $row = $check_uname_email->get_result();
            $fetch = $row->fetch_assoc();
            if ($row->num_rows == 1) {
                $updateUser = $connect->prepare("UPDATE tblusers SET fname=?,lname=?,uname=?,email=?,user_password=?,position=? WHERE id=?");
                $updateUser->bind_param('ssssssi', $fname, $lname, $uname, $email,$passwordHash,$position, $id);
                $updateUser->execute();
                if ($updateUser) {
                    header('Location:users.php?updated');
                } else{
                    header('Location:users.php?update_user_error');
                }
            }
        }
    }
}
function deleteUsers(){
    require 'public/connection.php';
    if(isset($_SERVER["REQUEST_METHOD"]) == "POST"){
        if (isset($_POST['btn-delete-users'])) {
            $code = mysqli_real_escape_string($connect, $_POST['id']);
            $deleteInventory = $connect->prepare("DELETE FROM tblusers WHERE id=?");
            $deleteInventory->bind_param('i', $code);
            $deleteInventory->execute();
            if ($deleteInventory) {
                //alter table id column
                $alterTable = "ALTER TABLE users AUTO_INCREMENT = 1";
                $alterTableId = $connect->query($alterTable);
                header('Location:users.php?deleted');
            }
            else{
                header('Location:users.php?delete_user_error');
            }
        }
    }
}

insertUsers();
editUsers();
deleteUsers();
?>