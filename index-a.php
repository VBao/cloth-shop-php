<?php
// Start the PHP session
session_start();

// Sample array of items
$items = array(
    array('id' => 1, 'name' => 'Item 1', 'price' => 19.99),
    array('id' => 2, 'name' => 'Item 2', 'price' => 29.99),
    array('id' => 3, 'name' => 'Item 3', 'price' => 39.99),
);

// Check if the cart array is not set in the session, initialize it
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Display items
foreach ($items as $item) {
    echo "<div>";
    echo "<h3>{$item['name']}</h3>";
    echo "<p>Price: {$item['price']} USD</p>";
    echo "<a href='?addToCart={$item['id']}'>Add to Cart</a>";
    echo "</div>";
}

// Process adding items to the cart when the anchor tag is clicked
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

    // Redirect to the same page to avoid re-submission on page refresh
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!-- Display the current items in the cart -->
<h2>Shopping Cart</h2>
<ul>
    <?php foreach ($_SESSION['cart'] as $cartItem): ?>
        <li>Item <?php echo $cartItem['id']; ?> - Quantity: <?php echo $cartItem['quantity']; ?></li>
    <?php endforeach; ?>
</ul>
