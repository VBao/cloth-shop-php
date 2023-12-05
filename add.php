<?php
include 'inc/header.php';
Session::CheckSession();
$sId =  Session::get('email');
if ($sId === 'admin@gmail.com') { ?>

  <?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    if (isset($_FILES['image'])) {
      $image_tmp = $_FILES['image']['tmp_name'];

      $imageName = strtolower(str_replace(' ', '-', $name)) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      move_uploaded_file($image_tmp, "assets/$imageName");

      $productAdd = $users->addProductByAdmin($name, $price, $imageName);
    } else {
      return
        '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Please add image !</div>';
    }
  }

  if (isset($productAdd)) {
    echo $productAdd;
  }
  ?>

  <div class="card ">
    <div class="card-header">
      <h3 class='text-center'>Add New Product</h3>
    </div>
    <div class="cad-body">



      <div style="width:600px; margin:0px auto">

        <form class="" action="" method="post" enctype="multipart/form-data">
          <div class="form-group pt-3">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control">
          </div>
          <div class="form-group">
            <label for="username">Price</label>
            <input type="number" name="price" class="form-control">
          </div>
          <div class="form-group">
            <!-- <label for="password">Password</label> -->
            <input type="file" name="image">
          </div>
          <div class="form-group">
            <button type="submit" name="add" class="btn btn-success">Save</button>
          </div>


        </form>
      </div>


    </div>
  </div>

<?php
} else {

  header('Location:admin.php');
}
?>

<?php
include 'inc/footer.php';

?>