<?php
include 'inc/header.php';
Session::CheckSession();

?>

<?php

if (isset($_GET['id'])) {
  $userid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['id']);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
  $getUinfo = $users->getProductInfoById($userid);

  // Check if a new image is uploaded
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image_tmp = $_FILES['image']['tmp_name'];

    // Generate a new image name based on the product name
    $imageName = strtolower(str_replace(' ', '-', $_POST['name'])) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    // Move the uploaded file to the "assets" folder
    move_uploaded_file($image_tmp, "assets/$imageName");
    $imageName = 'assets/' . $imageName;
    // If the old image exists, delete it
    if ($getUinfo->image) {
      unlink($getUinfo->image);
    }
  } else {
    // Use the old image name if no new image is uploaded
    $imageName = $getUinfo->image;
  }
  $updateUser = $users->updateUserByIdInfo($userid, $_POST['name'], $_POST['price'], $imageName);
}
if (isset($updateUser)) {
  echo $updateUser;
}

?>

<div class="card ">
  <div class="card-header">
    <h3>Product Info <span class="float-right"> <a href="admin.php" class="btn btn-primary">Back</a> </h3>
  </div>
  <div class="card-body">

    <?php
    $getUinfo = $users->getProductInfoById($userid);
    if ($getUinfo) {
    ?>


      <div style="width:600px; margin:0px auto">

        <form class="" action="" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" value="<?php echo $getUinfo->name; ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="email">Price</label>
            <input type="number" id="price" name="price" value="<?php echo $getUinfo->price; ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="mobile">Old Image</label>
            <div class="form-group">
              <?php if ($getUinfo->image) { ?>
                <img src="<?php echo $getUinfo->image; ?>" alt="Current Image" style="width: 300px; height: 300px;">
              <?php } ?></br>
              <label for="image">New Image</label>
              <input type="file" id="image" name="image" class="form-control">
            </div>
          </div>
          <?php ?>
          <input type="hidden" name="roleid" value="<?php echo $getUinfo->id; ?>">
          <div class="form-group">
            <button type="submit" name="update" class="btn btn-success">Update</button>
          </div>
        </form>
      </div>

    <?php } else {

      header('Location:index.php');
    } ?>



  </div>
</div>


<?php
include 'inc/footer.php';

?>