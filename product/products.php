<?php
$db = new mysqli("localhost","root","","product_db");
if($db->connect_error) die($db->connect_error);
if(isset($_POST['add']) || isset($_POST['update'])){
$n = $db->real_escape_string($_POST['name']);
$p = $db->real_escape_string($_POST['price']);
$c = $db->real_escape_string($_POST['category_id']);
$st = $db->real_escape_string($_POST['quantity']);
$id = $_POST['id'] ?? '';
isset($_POST['add'])
? $db->query("INSERT INTO products(name,price,category_id,quantity) VALUES('$n','$p','$c','$st')")
: $db->query("UPDATE products SET name='$n',price='$p',category_id='$c',quantity='$st' WHERE id=$id");
header("Location: ?");
}
if (isset($_GET['delete'])) {
$id = (int) $_GET['delete'];
$db->query("DELETE FROM products WHERE id=$id");
header("Location: ?");
exit;
}
$edit = isset($_GET['edit']);
$row = $edit ? $db->query("SELECT * FROM products WHERE id=" . $_GET['edit'])->fetch_assoc() : [];
$cats = $db->query("SELECT * FROM categories");
$s = isset($_GET['s']) ? $db->real_escape_string($_GET['s']) : '';
$r = $db->query($s
? "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.name LIKE '%$s%'"
: "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id");
?>
<h3><?= $edit ? 'Edit' : 'Add' ?> Product</h3>
<form method="post">
<input type="hidden" name="id" value="<?= $row['id'] ?? '' ?>">
<input name="name" placeholder="Name" value="<?= $row['name'] ?? '' ?>" required>
<input name="price" placeholder="Price" type="number" step="0.01" value="<?= $row['price'] ?? '' ?>" required>
<input name="quantity" placeholder="quantity" type="number" value="<?= $row['quantity'] ?? 0 ?>" required>
<select name="category_id">
<?php while($cat = $cats->fetch_assoc()){ ?>
<option value="<?=$cat['id']?>" <?= ($row['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= $cat['name'] ?></option>
<?php } ?>
</select>
<button name="<?= $edit ? 'update' : 'add' ?>"><?= $edit ? 'Update' : 'Add' ?></button>
</form>
<h3>Search</h3>
<form method="get">
<input name="s" value="<?= $s ?>" placeholder="Search">
<button>Search</button>
<a href="?">Reset</a>
</form>
<h3>All Products</h3>
<table border=1>
<tr><th>Name</th><th>Price</th><th>quantity</th><th>Category</th><th>Added At</th><th>Action</th></tr>
<?php while($x = $r->fetch_assoc()){ ?>
<tr>
<td><?= $x['name'] ?></td>
<td><?= $x['price'] ?></td>
<td><?= $x['quantity'] ?></td>
<td><?= $x['cat_name'] ?></td>
<td><?= $x['created_at'] ?></td>
<td>
<a href="?edit=<?= $x['id'] ?>">Edit</a>
<a href="?delete=<?= $x['id'] ?>">Delete</a>
</td>
</tr>
<?php } ?>
</table>
<!--
CREATE DATABASE products_db;
USE products_db;
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    price DECIMAL(10,2),
    category_id INT,
    quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
INSERT INTO categories(name) VALUES ('Electronics'),('Food'),('Clothing');
-->
