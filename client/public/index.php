<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;


$client = new Client();
$apiUrl = $_ENV['API_BASE_URL'];

$response = $client->get("$apiUrl/products");
$products = json_decode($response->getBody(), true);
?>

<?php if (isset($_GET['error'])): ?>
    <div style="color: red;"><?= htmlspecialchars($_GET['error']) ?></div>
<?php elseif (isset($_GET['success'])): ?>
    <div style="color: green;"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Products Index</title></head>
<body>
<h1>Products</h1>
<table border="1">
    <tr><th>ID</th><th>Name</th><th>Price</th><th>Description</th><th>Status</th><th>Sold</th><th>Available</th></tr>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['id'] ?></td>
            <td><?= $product['name'] ?></td>
            <td><?= $product['price'] ?></td>
            <td><?= $product['description'] ?></td>
            <td><?= $product['status'] ?></td>
            <td><?= $product['stockSold'] ?></td>
            <td><?= $product['stockAvailable'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Update a product</h2>
<form method="POST" action="put.php">
    <label>ID: <input type="number" name="id" required></label><br>
    <label>Name: <input type="text" name="name" required></label><br>
    <label>Description: <input type="text" name="description"></label><br>
    <label>Price: <input type="number" name="price" step="0.01" required></label><br>
    <label>Status:
        <select name="status">
            <option value="available">available</option>
            <option value="outOfStock">out of stock</option>
        </select>
    </label><br>
    <label>Sold: <input type="number" name="sold" required></label><br>
    <label>Available: <input type="number" name="available" required></label><br>
    <button type="submit">Update</button>
</form>
</body>
</html>
