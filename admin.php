<?php
include 'inc/header.php';

Session::CheckSession();

$logMsg = Session::get('logMsg');
if (isset($logMsg)) {
  echo $logMsg;
}
$msg = Session::get('msg');
if (isset($msg)) {
  echo $msg;
}
Session::set("msg", NULL);
Session::set("logMsg", NULL);
?>
<?php

if (isset($_GET['remove'])) {
  $remove = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['remove']);
  $removeUser = $users->deleteUserById($remove);
}

if (isset($removeUser)) {
  echo $removeUser;
}

if (isset($deactiveId)) {
  echo $deactiveId;
}
if (isset($_GET['active'])) {
  $active = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['active']);
  $activeId = $users->userActiveByAdmin($active);
}

if (isset($activeId)) {
  echo $activeId;
}


?>
<div class="card ">
  <div class="card-header">
    <h3><i class="fas fa-users mr-2"></i>Product list <span class="float-right">Welcome! <strong>
          <span class="badge badge-lg badge-secondary text-white">
            <?php
            $username = Session::get('username');
            if (isset($username)) {
              echo $username;
            }
            ?></span>

        </strong></span></h3>
  </div>
  <div class="card-body pr-2 pl-2">

    <table id="example" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
          <th class="text-center">SL</th>
          <th class="text-center">Name</th>
          <th class="text-center">Price</th>
          <th class="text-center">Image</th>
          <th width='25%' class="text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "clothshop";
        $is_connect = "FALSE";
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
          die("Connection failed: " . mysqli_connect_error());
        }
        $sql = "SELECT id, name, price, image FROM product ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);

        $products = array();
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $product = array(
              'id' => $row['id'],
              'name' => $row['name'],
              'price' => $row['price'],
              'image' => $row['image']
            );
            array_push($products, $product);
          }
        }

        if ($products) {
          $i = 0;
          foreach ($products as  $value) {
            $i++;

        ?>

            <tr class="text-center" <?php if (Session::get("id") == $value['id']) {
                                      echo "style='background:#d9edf7' ";
                                    } ?>>

              <td><?php echo $i; ?></td>
              <td><?php echo $value['name']; ?></td>
              <td><?php echo $value['price']; ?> <br>
              <td><img src="<?php echo $value['image']; ?>" style="max-height: 150px;"></td>
              <td>
                <?php if (Session::get("roleid") == '1') { ?>
                  <!-- <a class="btn btn-success btn-sm
                            " href="profile.php?id=<?php echo $value['id']; ?>">View</a> -->
                  <a class="btn btn-info btn-sm " href="profile.php?id=<?php echo $value['id']; ?>">Edit</a>
                  <a onclick="return confirm('Are you sure To Delete ?')" class="btn btn-danger
                    <?php if (Session::get("id") == $value['id']) {
                      echo "disabled";
                    } ?>
                             btn-sm " href="?remove=<?php echo $value['id']; ?>">Remove</a>
                <?php  } elseif (Session::get("id") == $value['id']  && Session::get("roleid") == '2') { ?>
                  <a class="btn btn-success btn-sm " href="profile.php?id=<?php echo $value['id']; ?>">View</a>
                  <a class="btn btn-info btn-sm " href="profile.php?id=<?php echo $value['id']; ?>">Edit</a>
                <?php  } elseif (Session::get("roleid") == '2') { ?>
                  <a class="btn btn-success btn-sm
                          <?php if ($value->roleid == '1') {
                            echo "disabled";
                          } ?>
                          " href="profile.php?id=<?php echo $value['id']; ?>">View</a>
                  <a class="btn btn-info btn-sm
                          <?php if ($value->roleid == '1') {
                            echo "disabled";
                          } ?>
                          " href="profile.php?id=<?php echo $value['id']; ?>">Edit</a>
                <?php } elseif (Session::get("id") == $value['id']  && Session::get("roleid") == '3') { ?>
                  <a class="btn btn-success btn-sm " href="profile.php?id=<?php echo $value['id']; ?>">View</a>
                  <a class="btn btn-info btn-sm " href="profile.php?id=<?php echo $value['id']; ?>">Edit</a>
                <?php } else { ?>
                  <a class="btn btn-success btn-sm
                          <?php if ($value->roleid == '1') {
                            echo "disabled";
                          } ?>
                          " href="profile.php?id=<?php echo $value['id']; ?>">View</a>

                <?php } ?>

              </td>
            </tr>
          <?php }
        } else { ?>
        <?php } ?>

      </tbody>

    </table>









  </div>
</div>



<?php
include 'inc/footer.php';

?>