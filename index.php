<?php
include 'models/Category.php';
include 'models/Product.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['categories'])) {
    $products1 = [
        new Product("Lenovo", 2500),
        new Product("Asus", 1500)
    ];
    $products2 = [
        new Product("Galaxy S24", 1550),
        new Product("Galaxy A55", 650)
    ];
    $products3 = [
        new Product("Xiaomi", 500),
        new Product("Huawei", 550)
    ];

    $_SESSION['categories'] = [
        new Category("Laptops", $products1),
        new Category("Phones", $products2),
        new Category("Tablets", $products3)
    ];
}

$categories = $_SESSION['categories'];

function searchCategoryByName($categories, $name) {
    foreach ($categories as $category) {
        if ($category->getCategoryName() === $name) {
            return $category;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];
    $categoryName = $_POST['product_category'];

    $category = searchCategoryByName($categories, $categoryName);

    if ($category && !empty($productName) && is_numeric($productPrice)) {
        $category->addProduct(new Product($productName, $productPrice));
    }

    $_SESSION['categories'] = $categories;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $categoryName = $_POST['category_name'];

    if (!empty($categoryName)) {
        $newCategory = new Category($categoryName, []);
        $categories[] = $newCategory;
    }

    $_SESSION['categories'] = $categories;
}

$currentCategory = null;
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $currentCategory = searchCategoryByName($categories, $_GET['category']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Categories and Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #888;
            color: #fff;
        }
        .content {
            width: 100%;
            max-width: 600px;
            background-color: #333;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            margin: 5px 0;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        form {
            margin-top: 20px;
            background-color: #444;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        form input, form select, form button {
            margin: 10px 0;
            padding: 15px;
            width: 100%;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
        }
        form input, form select {
            background-color: #555;
            color: #fff;
        }
        form button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="content">
    <h2>Categories:</h2>
    <ul>
        <?php foreach ($categories as $category): ?>
            <li>
                <a href="?category=<?php echo urlencode($category->getCategoryName()); ?>">
                    <?php echo htmlspecialchars($category->getCategoryName()); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($currentCategory): ?>
        <h2>Products in "<?php echo htmlspecialchars($currentCategory->getCategoryName()); ?>"</h2>
        <ul>
            <?php foreach ($currentCategory->getCategoryProducts() as $product): ?>
                <li><?php echo htmlspecialchars($product->getProduct()); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <h2>No category selected</h2>
    <?php endif; ?>

    <h2>Add Product</h2>
    <form method="POST">
        <input type="text" name="product_name" placeholder="Product Name" required>
        <input type="number" name="product_price" placeholder="Product Price" required>
        <select name="product_category" required>
            <option value="" disabled selected>Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category->getCategoryName()); ?>">
                    <?php echo htmlspecialchars($category->getCategoryName()); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="add_product">Add Product</button>
    </form>

    <h2>Add Category</h2>
    <form method="POST">
        <input type="text" name="category_name" placeholder="Category Name" required>
        <button type="submit" name="add_category">Add Category</button>
    </form>
</div>
</body>
</html>






