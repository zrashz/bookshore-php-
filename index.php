<?php
// Start the session to store cart data
session_start();

// Establish the connection to the database
$servername = "localhost"; // Database server
$username = "root"; // Database username
$password = ""; // Database password (default is empty for XAMPP)
$dbname = "bookstore"; // Your database name

// Create connection
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add to cart functionality
if (isset($_POST["add"])) {
    $book_id = $_GET["id"];
    $book_title = $_POST["hidden_title"];
    $book_price = $_POST["hidden_price"];
    $book_quantity = $_POST["quantity"];

    $item_array = array(
        'book_id' => $book_id,
        'book_title' => $book_title,
        'book_price' => $book_price,
        'book_quantity' => $book_quantity
    );

    // If cart session is empty, create a new cart
    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = array();
    }

    // Add the item to the cart
    $_SESSION["cart"][] = $item_array;
}

// Remove from cart functionality
if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    $book_id_to_delete = $_GET["id"];

    // Loop through the cart and remove the item by its book ID
    foreach ($_SESSION["cart"] as $key => $value) {
        if ($value["book_id"] == $book_id_to_delete) {
            unset($_SESSION["cart"][$key]);
            break;
        }
    }

    // Re-index the array after deletion to avoid gaps in array indexes
    $_SESSION["cart"] = array_values($_SESSION["cart"]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Background image style */
        body {
            background-image: url('bg.jpeg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            
        }

        .container {
            background-color: rgba(235, 242, 239, 0.8); /* Slightly transparent background for content */
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .book {
            border: 1px solid #eaeaec;
            margin: 10px;
            padding: 10px;
            text-align: center;
            background-color:rgb(218, 245, 10);
        }

        table, th, tr {
            text-align: center;
        }

        .title {
            text-align: center;
            color: #333;
            background-color:rgb(233, 156, 156);
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container" style="width: 70%">
        <h2 class="title">Online Bookstore</h2>
        <?php
            $query = "SELECT * FROM books ORDER BY id ASC";
            $result = mysqli_query($connection, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
        ?>
        <div class="col-md-3" style="float: left;">
            <form method="post" action="index.php?action=add&id=<?php echo $row["id"]; ?>">
                <div class="book">
                    <img src="<?php echo $row["image"]; ?>" width="200px" height="200px">
                    <h5 class="text-info"><?php echo $row["title"]; ?></h5>
                    <h5 class="text-danger">$<?php echo $row["price"]; ?></h5>
                    <input type="number" name="quantity" class="form-control" value="1" min="1">
                    <input type="hidden" name="hidden_title" value="<?php echo $row["title"]; ?>">
                    <input type="hidden" name="hidden_price" value="<?php echo $row["price"]; ?>">
                    <input type="submit" name="add" class="btn btn-success" value="Add to Cart">
                </div>
            </form>
        </div>
        <?php
                }
            }
        ?>
        <div style="clear: both"></div>
        <h3 class="title">Cart Details</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>Book Title</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>
                <?php
                    if (!empty($_SESSION["cart"])) {
                        $total = 0;
                        foreach ($_SESSION["cart"] as $key => $value) {
                ?>
                <tr>
                    <td><?php echo $value["book_title"]; ?></td>
                    <td><?php echo $value["book_quantity"]; ?></td>
                    <td>$<?php echo $value["book_price"]; ?></td>
                    <td>$<?php echo number_format($value["book_quantity"] * $value["book_price"], 2); ?></td>
                    <td><a href="index.php?action=delete&id=<?php echo $value["book_id"]; ?>" class="text-danger">Remove</a></td>
                </tr>
                <?php
                        $total += ($value["book_quantity"] * $value["book_price"]);
                        }
                ?>
                <tr>
                    <td colspan="3" align="right">Total</td>
                    <td>$<?php echo number_format($total, 2); ?></td>
                    <td></td>
                </tr>
                <?php
                    }
                ?>
            </table>
        </div>
    </div>
</body>
</html>
