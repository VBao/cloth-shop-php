<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "phone_shop";
$is_connect = "FALSE";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT id, name, price, image FROM product LIMIT 12";
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

session_start();

if (isset($_GET['addToCart'])) {
    $itemId = $_GET['addToCart'];

    // Check if the item is already in the cart
    $key = array_search($itemId, array_column($_SESSION['cart'], 'id'));

    if ($key !== false) {
        // If the item is already in the cart, update the quantity
        $_SESSION['cart'][$key]['quantity'] += 1;
    } else {
        // If the item is not in the cart, add it with a quantity of 1
        $_SESSION['cart'][] = array('id' => $itemId, 'quantity' => 1);
    }

    // Return a JSON response to indicate success
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHONE SHOP</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <header>
        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>

        <a href="#" class="logo">PHONE SHOP<span>.</span></a>
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
            <a href="#" class="fas fa-user"></a>
        </div>
    </header>

    <section class="home" id="home">
        <div class="content">
            <div class="content">
                <div class="slider">
                    <div class="list">
                        <div class="item">
                            <img src="assets/1.png" alt="">
                        </div>
                        <div class="item">
                            <img src="assets/2.png" alt="">
                        </div>
                        <div class="item">
                            <img src="assets/3.png" alt="">
                        </div>
                        <div class="item">
                            <img src="assets/4.png" alt="">
                        </div>
                        <div class="item">
                            <img src="assets/5.png" alt="">
                        </div>
                    </div>
                    <div class="buttons">
                        <button id="prev">
                            << /button>
                                <button id="next">></button>
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
                <h3>Phone Shop</h3>
                <span>Hệ thống cửa hàng điện thoại</span>
                <p>Phone shop là cửa hàng điện thoại uy tín
                    dịch vụ vô cùng hấp dẫn </p>
                <a href="#" class="btn">shop now</a>
            </div>
        </div>
    </section>
    <h2>Shopping Cart</h2>



    <section class="contact" id="contact">

        <h1 class="heading"><span> contact </span> us </h1>

        <div class="row">

            <form action="">
                <input type="text" placeholder="Name" class="box">
                <input type="email" placeholder="Email" class="box">
                <input type="number" placeholder="Number" class="box">
                <textarea name="" class="box" placeholder="Message" id="" cols="30" rows="10"></textarea>
                <input type="submit" value="send message" class="btn">
            </form>

            <div class="image">
                <img src="" alt="">
            </div>

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
                <a href="#">+321-456-987</a>
                <a href="#">phoneshop@gmail.com</a>
                <a href="#">Ho Chi Minh city, Vietnam</a>
                <img src="assets/payment.png" alt="">
            </div>

        </div>
        <div class="credit">credit by <span> phone shop </span> | all rights reserved </div>
    </section>

</body>

</html>