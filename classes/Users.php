<?php

include 'lib/Database.php';
include_once 'lib/Session.php';


class Users
{


  // Db Property
  private $db;

  // Db __construct Method
  public function __construct()
  {
    $this->db = new Database();
  }

  // Date formate Method
  public function formatDate($date)
  {
    // date_default_timezone_set('Asia/Dhaka');
    $strtime = strtotime($date);
    return date('Y-m-d H:i:s', $strtime);
  }



  // Check Exist Email Address Method
  public function checkExistEmail($email)
  {
    $sql = "SELECT email from  tbl_users WHERE email = :email";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }



  // User Registration Method
  public function userRegistration($data)
  {
    $name = $data['name'];
    $username = $data['username'];
    $email = $data['email'];
    $mobile = $data['mobile'];
    $roleid = $data['roleid'];
    $password = $data['password'];

    $checkEmail = $this->checkExistEmail($email);

    if ($name == "" || $username == "" || $email == "" || $mobile == "" || $password == "") {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Please, User Registration field must not be Empty !</div>';
      return $msg;
    } elseif (strlen($username) < 3) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Username is too short, at least 3 Characters !</div>';
      return $msg;
    } elseif (filter_var($mobile, FILTER_SANITIZE_NUMBER_INT) == FALSE) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Enter only Number Characters for Mobile number field !</div>';
      return $msg;
    } elseif (strlen($password) < 5) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Password at least 6 Characters !</div>';
      return $msg;
    } elseif (!preg_match("#[0-9]+#", $password)) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Your Password Must Contain At Least 1 Number !</div>';
      return $msg;
    } elseif (!preg_match("#[a-z]+#", $password)) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Your Password Must Contain At Least 1 Number !</div>';
      return $msg;
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL === FALSE)) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Invalid email address !</div>';
      return $msg;
    } elseif ($checkEmail == TRUE) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Email already Exists, please try another Email... !</div>';
      return $msg;
    } else {

      $sql = "INSERT INTO tbl_users(name, username, email, password, mobile, roleid) VALUES(:name, :username, :email, :password, :mobile, :roleid)";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':name', $name);
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':email', $email);
      $stmt->bindValue(':password', SHA1($password));
      $stmt->bindValue(':mobile', $mobile);
      $stmt->bindValue(':roleid', $roleid);
      $result = $stmt->execute();
      if ($result) {
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success !</strong> Wow, you have Saveed Successfully !</div>';
        return $msg;
      } else {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Something went Wrong !</div>';
        return $msg;
      }
    }
  }

  public function addProductByAdmin($name, $price, $image)
  {

    if ($name == "" || $price == "" || $image == "") {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Input fields must not be Empty !</div>';
      return $msg;
    } elseif (strlen($name) < 3) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error !</strong> Name is too short, at least 3 Characters !</div>';
      return $msg;
    } else {

      $sql = "INSERT INTO product(name, price, image) VALUES(:name, :price, :image)";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':name', $name);
      $stmt->bindValue(':price', $price);
      $stmt->bindValue(':image', 'assets/' . $image);
      $result = $stmt->execute();
      if ($result) {
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success !</strong> Add Product Successfully !</div>';
        return $msg;
      } else {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Something went Wrong !</div>';
        return $msg;
      }
    }
  }



  // Select All User Method
  public function selectAllUserData()
  {
    $sql = "SELECT * FROM tbl_users ORDER BY id DESC";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }


  // User login Autho Method
  public function userLoginAutho($email, $password)
  {
    $password = SHA1($password);
    $sql = "SELECT * FROM tbl_users WHERE email = :email and password = :password LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $password);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }
  // Check Product Satatus
  public function CheckActiveUser($email)
  {
    $sql = "SELECT * FROM tbl_users WHERE email = :email and isActive = :isActive LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':isActive', 1);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  // User Login Authotication Method
  public function userLoginAuthotication($data)
  {
    $email = $data['email'];
    $password = $data['password'];

    if ($email == "" || $password == "") {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Email or Password not be Empty !</div>';
      return $msg;
    } else {
      if ($email === 'admin@gmail.com' && $password === 'admin') {
        Session::init();
        Session::set('login', TRUE);
        Session::set('id', 1);
        Session::set('roleid', 1);
        Session::set('name', 'Handsome Admin');
        Session::set('email', 'admin@gmail.com');
        Session::set('username', 'admin');
        Session::set('logMsg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Success !</strong> You are Logged In Successfully !</div>');
        echo "<script>location.href='admin.php';</script>";
      } else {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Email or Password did not Matched !</div>';
        return $msg;
      }
    }
  }



  // Get Single User Information By Id Method
  public function getProductInfoById($userid)
  {
    $sql = "SELECT * FROM product WHERE id = :id LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $userid);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    if ($result) {
      return $result;
    } else {
      return false;
    }
  }

  public function updateUserByIdInfo($id, $name, $price, $image)
  {
    if ($name == "" || $price == "") {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Input Fields must not be Empty !</div>';
      return $msg;
    } elseif (strlen($name) < 3) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Name is too short, at least 3 Characters !</div>';
      return $msg;
    } else {
      $sql = "UPDATE product SET
        name = :name,
        price = :price,
        image = :image
        WHERE id = :id";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':name', $name);
      $stmt->bindValue(':price', (int)$price);
      $stmt->bindValue(':image', $image);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $result = $stmt->execute();



      if ($result) {
        echo "<script>location.href='admin.php';</script>";
        Session::set('msg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Success !</strong> Edit Successfully !</div>');
      } else {
        echo "<script>location.href='admin.php';</script>";
        Session::set('msg', '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Data not inserted !</div>');
      }
    }
  }




  // Delete User by Id Method
  public function deleteUserById($remove)
  {
    $sql = "DELETE FROM product WHERE id = :id ";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $remove);
    $result = $stmt->execute();
    if ($result) {
      $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Success !</strong> Product Deleted Successfully !</div>';
      return $msg;
    } else {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Data not Deleted !</div>';
      return $msg;
    }
  }



  // User Deactivated By Admin
  public function userActiveByAdmin($active)
  {
    $sql = "UPDATE tbl_users SET
       isActive=:isActive
       WHERE id = :id";

    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':isActive', 0);
    $stmt->bindValue(':id', $active);
    $result =   $stmt->execute();
    if ($result) {
      echo "<script>location.href='admin.php';</script>";
      Session::set('msg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Success !</strong> Product activated Successfully !</div>');
    } else {
      echo "<script>location.href='admin.php';</script>";
      Session::set('msg', '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Data not activated !</div>');
    }
  }




  // Check Old password method
  public function CheckOldPassword($userid, $old_pass)
  {
    $old_pass = SHA1($old_pass);
    $sql = "SELECT password FROM tbl_users WHERE password = :password AND id =:id";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':password', $old_pass);
    $stmt->bindValue(':id', $userid);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }



  // Change User pass By Id
  public  function changePasswordBysingelUserId($userid, $data)
  {

    $old_pass = $data['old_password'];
    $new_pass = $data['new_password'];


    if ($old_pass == "" || $new_pass == "") {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Password field must not be Empty !</div>';
      return $msg;
    } elseif (strlen($new_pass) < 6) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> New password must be at least 6 character !</div>';
      return $msg;
    }

    $oldPass = $this->CheckOldPassword($userid, $old_pass);
    if ($oldPass == FALSE) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
     <strong>Error !</strong> Old password did not Matched !</div>';
      return $msg;
    } else {
      $new_pass = SHA1($new_pass);
      $sql = "UPDATE tbl_users SET

            password=:password
            WHERE id = :id";

      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':password', $new_pass);
      $stmt->bindValue(':id', $userid);
      $result =   $stmt->execute();

      if ($result) {
        echo "<script>location.href='admin.php';</script>";
        Session::set('msg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success !</strong> Great news, Password Changed successfully !</div>');
      } else {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Error !</strong> Password did not changed !</div>';
        return $msg;
      }
    }
  }
}
