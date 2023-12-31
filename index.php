<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clothshop";
$is_connect = "FALSE";


session_start();
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}


$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT id, name, price, image FROM product ORDER BY id desc LIMIT 12";
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

if (isset($_GET['addToCart'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "clothshop";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    $itemId = $_GET['addToCart'];
    $key = array_search($itemId, array_column($_SESSION['cart'], 'id'));
    if ($key !== false) {
        $_SESSION['cart'][$key]['quantity'] += 1;
    } else {
        $sql = "SELECT id, name, price, image FROM product WHERE id = $itemId";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $_SESSION['cart'][] = array('id' => $itemId, 'quantity' => 1, 'name' => $row['name'], 'price' => $row['price']);
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}



// Function to remove an item from the cart based on its ID
function removeFromCart($itemId)
{
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $cartItem) {
            if ($cartItem['id'] == $itemId) {
                // Remove the item from the cart
                unset($_SESSION['cart'][$key]);
                // Reindex the array to avoid gaps in the array keys
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break; // Exit the loop once the item is found and removed
            }
        }
    }
}

// Check confirm to remove from cart
// Check if the "OK" button is clicked and an item ID is provided
if (isset($_POST['okButton']) && isset($_POST['itemId'])) {
    // Get the item ID from the POST data
    $itemId = $_POST['itemId'];

    // Call the function to remove the item from the cart
    removeFromCart($itemId);
    header("Refresh:0");
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get order information from the form
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $note = $_POST['note'];

    // Get cart items from the session (replace with your actual session variable)
    $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

    // Call the function to save order information to the database
    saveOrderToDatabase($name, $address, $phone, $note, $cartItems);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

function saveOrderToDatabase($name, $address, $phone, $note, $cartItems)
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "clothshop";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "INSERT INTO orders (name, address, phone, note) VALUES ('$name', '$address', '$phone', '$note')";
    if ($conn->query($sql) === TRUE) {
        // Get the order ID of the inserted row
        $orderID = $conn->insert_id;
        foreach ($cartItems as $cartItem) {
            $productId = $cartItem['id'];
            $quantity = $cartItem['quantity'];
            $sql = "INSERT INTO order_items (order_id, product_id, quantity) VALUES ('$orderID', '$productId', '$quantity')";
            $conn->query($sql);
        }

        $_SESSION['cart'] = array();
        echo "Order placed successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Function to get all orders with order items from the database
function getAllOrdersWithItems()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "clothshop";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT o.*, o.name as cname ,oi.product_id, oi.quantity,
     p.name, p.price,o.note
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN product p ON oi.product_id = p.id
            ORDER BY o.id";
    $result = $conn->query($sql);
    if ($result === false) {
        die("Error in the query: " . $conn->error);
    }
    $orders = array();
    while ($row = $result->fetch_assoc()) {
        $orderId = $row['id'];
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = array(
                'id' => $orderId,
                'name' => $row['cname'],
                'address' => $row['address'],
                'phone' => $row['phone'],
                'note' => $row['note'],
                'items' => array()
            );
        }
        $orders[$orderId]['items'][] = array(
            'product_id' => $row['product_id'],
            'product_name' => $row['name'],
            'price' => $row['price'],
            'quantity' => $row['quantity']
        );
    }
    return $orders;
}
$allOrders = getAllOrdersWithItems();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOTH SHOP</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <header>
        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>

        <a href="#" class="logo">CLOTH SHOP<span>.</span></a>
        <nav class="navbar">
            <a href="#home">Home</a>
            <a href="#about"> About</a>
            <a href="#products">Product</a>
            <a href="#review">Review</a>
            <a href="#contact">Contact</a>
        </nav>
        <div class="icons">
            <a href="#" class="fas fa-heart"></a>
            <a href="#" class="fas fa-shopping-cart"></a>
            <a href="admin.php" class="fas"><img src="https://cdn-icons-png.flaticon.com/512/3607/3607444.png" width="25px" alt=""></a>
        </div>
    </header>

    <section class="home" id="home">
        <div class="content">
            <div class="content">
                <div class="slider">
                    <div class="list">
                        <div class="item">
                            <img src="assets/1.webp" alt="">
                        </div>
                        <div class="item">
                            <img src="assets/2.webp" alt="">
                        </div>
                        <div class="item">
                            <img src="assets/3.webp" alt="">
                        </div>
                    </div>
                    <div class="buttons">
                        <button id="prev">
                            < <button id="next">>
                        </button>
                    </div>
                    <ul class="dots">
                        <li class="active"></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                </div>
                <script src="app.js"></script>
                <h3>CLOTH Shop</h3>
                <span>Hệ thống cửa hàng quần áo</span>
                <p>Cloth shop là cửa hàng quần áo uy tín
                    dịch vụ vô cùng hấp dẫn </p>
                <a href="#" class="btn">shop now</a>
            </div>
        </div>
    </section>

    <section class="products" id="products">
        <h1 class="heading"> Popular <span>products</span> </h1>
        <div class="box-container">
            <?php foreach ($products as $product) : ?>
                <div class="box">
                    <!-- <span class="discount">-10%</span> -->
                    <div class="image">
                        <img src="<?= $product['image'] ?>">
                        <div class="icons">
                            <a href="#" class="fas fa-heart"></a>
                            <?php echo "<a href='#' class='add-to-cart' data-item-id='{$product['id']}'>Add to Cart</a>"; ?>
                            <a href="#" class="fas fa-share"></a>
                        </div>
                    </div>
                    <div class="content">
                        <h3>
                            <?= $product['name'] ?>
                        </h3>
                        <div class="price">
                            <?= $product['price'] ?> VND
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </section>

    <?php if ($_SESSION["cart"] != []) : ?>
        <section class="order">
            <h1 class="heading"> Shopping<span> Cart</span> </h1>
            <!-- Shopping Cart Table -->
            <div>
                <table class="table-order">
                    <thead>
                        <tr>
                            <th scope="col-order">Product</th>
                            <th scope="col-order">Price</th>
                            <th scope="col-order">Quantity</th>
                            <th scope="col-order">Total</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 20px">
                        <?php foreach ($_SESSION['cart'] as $cartItem) :
                        ?>
                            <tr>
                                <td>
                                    <?php echo $cartItem['name']; ?>
                                </td>
                                <td>
                                    <?php echo $cartItem['price']; ?>
                                </td>
                                <td>
                                    <?php echo $cartItem['quantity']; ?>
                                </td>
                                <td>
                                    <?php echo $cartItem['quantity'] * $cartItem['price']; ?>
                                </td>
                                <?php echo "<td><button class='btn-del' type='button' onclick='removeFromCart({$cartItem['id']})' data-item-id='{$cartItem['id']}'>Remove</button></td>"; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Order Form -->
            <div class="order-form" style="font-size: 20px">
                <h2>Order Information</h2>
                <form action="#" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address:</label>
                        <textarea class="form-control" id="address" name="address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number:</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div>
                        <label for="phone" class="form-label">Note:</label>
                        <textarea name="note" class="box" placeholder="Note" id="" cols="30" rows="10"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Place Order</button>
                </form>
            </div>
        </section>

    <?php endif; ?>
    <section class="order-list" style="font-size: 20px">
        <table border="1">
            <tr>
                <th>Order ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Note</th>
            </tr>
            <?php foreach ($allOrders as $order) : ?>
                <tr class="collapsible" data-order-id="<?php echo $order['id']; ?>">
                    <td>
                        <?php echo $order['id']; ?>
                    </td>
                    <td>
                        <?php echo $order['name']; ?>
                    </td>
                    <td>
                        <?php echo $order['address']; ?>
                    </td>
                    <td>
                        <?php echo $order['phone']; ?>
                    </td>
                    <td>
                        <?php echo $order['note']; ?>
                    </td>
                </tr>
                <tr class="hidden-row" data-order-id="<?php echo $order['id']; ?>">
                    <td colspan="5">
                        <!-- Display order items in a nested table -->
                        <table border="1">
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                            </tr>
                            <?php foreach ($order['items'] as $item) : ?>
                                <tr>
                                    <td>
                                        <?php echo $item['product_id']; ?>
                                    </td>
                                    <td>
                                        <?php echo $item['product_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $item['price']; ?>
                                    </td>
                                    <td>
                                        <?php echo $item['quantity']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <section class="review" id="review">
        <h1 class="heading"> customer's <span>review</span></h1>
        <div class="box-container">

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Tạm chấp nhận được</p>
                <div class="user">
                    <img src="assets/mina.jpg" alt="">
                    <div class="user-info">
                        <h3>myoui mina</h3>
                        <span>happy customer</span>
                    </div>
                </div>
                <span class="fas fa-quote-right"></span>
            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Sản phẩm đẹp và giá hợp lý</p>
                <div class="user">
                    <img src="assets/nayeon.jpg" alt="">
                    <div class="user-info">
                        <h3>im nayeon</h3>
                        <span>happy customer</span>
                    </div>
                </div>
                <span class="fas fa-quote-right"></span>
            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Sản phẩm đẹp và giá hợp lý</p>
                <div class="user">
                    <img src="assets/sana.jpg" alt="">
                    <div class="user-info">
                        <h3>minatozaki sana</h3>
                        <span>happy customer</span>
                    </div>
                </div>
                <span class="fas fa-quote-right"></span>
            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Sản phẩm đẹp và giá hợp lý</p>
                <div class="user">
                    <img src="assets/momo.jpg" alt="">
                    <div class="user-info">
                        <h3>hirai momo</h3>
                        <span>happy customer</span>
                    </div>
                </div>
                <span class="fas fa-quote-right"></span>
            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Sản phẩm đẹp và giá hợp lý</p>
                <div class="user">
                    <img src="assets/tzuyu.jpg" alt="">
                    <div class="user-info">
                        <h3>chou tzuyu</h3>
                        <span>happy customer</span>
                    </div>
                </div>
                <span class="fas fa-quote-right"></span>
            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Sản phẩm đẹp và giá hợp lý</p>
                <div class="user">
                    <img src="assets/jihyo.jpg" alt="">
                    <div class="user-info">
                        <h3>park jihyo</h3>
                        <span>happy customer</span>
                    </div>
                </div>
                <span class="fas fa-quote-right"></span>
            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Mẩu mã đa dạng, đẹp</p>
                <div class="user">
                    <img src="assets/jeongyeon.jpg" alt="">
                    <div class="user-info">
                        <h3>yoo jeongyeon</h3>
                        <span>happy customer</span>
                    </div>
                </div>
                <span class="fas fa-quote-right"></span>
            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Sản phẩm tốt, vải đẹp</p>
                <div class="user">
                    <img src="assets/dahyun.jpg" alt="">
                    <div class="user-info">
                        <h3>kim dahyun</h3>
                        <span>happy customer</span>
                    </div>
                </div>
                <span class="fas fa-quote-right"></span>
            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Sản phẩm đẹp và giá hợp lý</p>
                <div class="user">
                    <img src="assets/chaeyoung.jpg" alt="">
                    <div class="user-info">
                        <h3>son chaeyoung</h3>
                        <span>happy customer</span>
                    </div>
                </div>
                <span class="fas fa-quote-right"></span>
            </div>

        </div>
    </section>

    <section class="contact" id="contact">

        <h1 class="heading"><span> contact </span> us </h1>

        <div class="row">
            <form action="">
                <input type="text" placeholder="Name" class="box">
                <input type="email" placeholder="Email" class="box">
                <input type="number" placeholder="Number" class="box">
                <textarea name="" class="box" placeholder="Message" id="" cols="30" rows="10"></textarea>
                <div class="msg-btn"><input type="submit" value="send message" class="btn"></div>
            </form>
        </div>
    </section>

    <section class="footer">

        <div class="box-container">

            <div class="box">
                <h3>quick links</h3>
                <a href="#">home</a>
                <a href="#">about</a>
                <a href="#">products</a>
                <a href="#">review</a>
                <a href="#">contact</a>
            </div>

            <div class="box">
                <h3>extra links</h3>
                <a href="#">my account</a>
                <a href="#">my order</a>
                <a href="#">my favorite</a>
            </div>

            <div class="box">
                <h3>location</h3>
                <a href="#">Vietnam</a>
                <a href="#">Korea</a>
                <a href="#">America</a>
                <a href="#">Japan</a>
                <a href="#">Canada</a>
            </div>

            <div class="box">
                <h3>contact info</h3>
                <a href="#">+84912-213-957</a>
                <a href="#">cloth@gmail.com</a>
                <a href="#">Ho Chi Minh city, Vietnam</a>
                <img src="assets/payment.png" alt="">
            </div>

        </div>
        <div class="credit">credit by <span> cloth shop </span> | all rights reserved </div>
    </section>

</body>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        $('.add-to-cart').on('click', function(e) {
            e.preventDefault();
            var itemId = $(this).data('item-id');
            console.log(itemId);
            $.ajax({
                type: 'GET',
                url: '?addToCart=' + itemId,
                success: function(response) {
                    alert('Item added to cart!');
                    window.location.reload();
                },
                error: function() {
                    alert('Error adding item to cart.');
                }
            });
        });
    });


    function removeFromCart(itemId) {
        var confirmRemove = confirm("Are you sure you want to remove this item from the cart?");
        if (confirmRemove) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "remove_from_cart.php", true); // Replace with the correct URL
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    window.location.reload();
                }
            };
            xhr.send("okButton=1&itemId=" + itemId);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        var collapsibleRows = document.querySelectorAll(".collapsible");

        collapsibleRows.forEach(function(row) {
            row.addEventListener("click", function() {
                var orderId = row.getAttribute("data-order-id");
                var hiddenRow = document.querySelector(".hidden-row[data-order-id='" + orderId + "']");

                if (hiddenRow) {
                    hiddenRow.classList.toggle("show");
                }
            });
        });
    });
</script>

</html>