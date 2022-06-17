<?php

  $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
  $allowed_domains = array("https://auth.reestoc.com", "https://dashboard.reestoc.com");
  foreach ($allowed_domains as $value) {if ($http_origin === $value) {header('Access-Control-Allow-Origin: ' . $http_origin);}}
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, origin, Accept-Language, Range, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  ini_set('display_errors', 1);

  require '../../config/connect_to_me.php';
  include_once "../../config/functions.php";

  class genericClass {
    public $engineMessage = 0;
    public $engineError = 0;
    public $engineErrorMessage;
    public $resultData;
    public $filteredData;
  }

  $data = json_decode(file_get_contents("php://input"), true);

  $functions = new Functions();

  $cart_unique_id = isset($_GET['cart_unique_id']) ? $_GET['cart_unique_id'] : $data["cart_unique_id"];
  $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data["user_unique_id"];

  if ($connected) {

    try {
      $conn->beginTransaction();

      $active = $functions->active;
      $date_added = $functions->today;

      $cart_array = array();

      $sql = "SELECT carts.id, carts.unique_id, carts.user_unique_id, carts.vendor_unique_id, vendors.business_name as vendor_name, vendors.stripped as vendor_stripped, carts.product_unique_id, carts.quantity, carts.shipping_fee_unique_id, carts.pickup_location, carts.added_date, carts.last_modified, carts.status, users.fullname as user_fullname,
      users.email as user_email, users.phone_number as user_phone_number, products.name, products.stripped, products.duration, products.weight, products.stock, products.stock_remaining, products.price, products.sales_price, products.views, products.favorites, products.good_rating, products.bad_rating, shipping_fees.price as shipping_price FROM carts
      INNER JOIN users ON carts.user_unique_id = users.unique_id INNER JOIN vendors ON carts.vendor_unique_id = vendors.unique_id LEFT JOIN products ON carts.product_unique_id = products.unique_id LEFT JOIN shipping_fees ON carts.shipping_fee_unique_id = shipping_fees.unique_id WHERE carts.unique_id=:unique_id AND carts.user_unique_id=:user_unique_id ORDER BY carts.added_date DESC";
      $query = $conn->prepare($sql);
      $query->bindParam(":unique_id", $cart_unique_id);
      $query->bindParam(":user_unique_id", $user_unique_id);
      $query->execute();

      $result = $query->fetchAll();

      if ($query->rowCount() > 0) {
        foreach ($result as $key => $value) {

          $current_cart = array();
          $current_cart['id'] = $value['id'];
          $current_cart['unique_id'] = $value['unique_id'];
          $current_cart['user_unique_id'] = $value['user_unique_id'];
          $current_cart['product_unique_id'] = $value['product_unique_id'];
          $current_cart['quantity'] = $value['quantity'];
          $current_cart['user_fullname'] = $value['user_fullname'];
          $current_cart['user_email'] = $value['user_email'];
          $current_cart['user_phone_number'] = $value['user_phone_number'];
          $current_cart['added_date'] = $value['added_date'];
          $current_cart['last_modified'] = $value['last_modified'];
          $current_cart['status'] = $value['status'];
          $current_cart['vendor_name'] = $value['vendor_name'];
          $current_cart['vendor_stripped'] = $value['vendor_stripped'];
          $current_cart['name'] = $value['name'];
          $current_cart['stripped'] = $value['stripped'];
          $current_cart['duration'] = $value['duration'];
          $current_cart['weight'] = $value['weight'];
          $current_cart['stock'] = $value['stock'];
          $current_cart['stock_remaining'] = $value['stock_remaining'];
          $current_cart['price'] = $value['price'];
          $current_cart['sales_price'] = $value['sales_price'];
          $current_cart['views'] = $value['views'];
          $current_cart['favorites'] = $value['favorites'];
          $current_cart['good_rating'] = $value['good_rating'];
          $current_cart['bad_rating'] = $value['bad_rating'];
          $current_cart['shipping_price'] = $value['shipping_price'];

          $product_id = $value['product_unique_id'];
          $cart_unique_id = $value['unique_id'];
          $user_unique_id = $value['user_unique_id'];

          $current_shipping_price = (int)$value['shipping_price'];
          $current_cart_price = (int)$value['price'];
          $current_cart_sales_price = (int)$value['sales_price'];
          $current_cart_quantity = (int)$value['quantity'];

          $current_cart['full_shipping_price'] = $current_shipping_price * $current_cart_quantity;

          $sql2 = "SELECT image FROM product_images WHERE product_unique_id=:product_unique_id LIMIT 1";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":product_unique_id", $product_id);
          $query2->execute();

          $images_result = $query2->fetchAll();

          if ($query2->rowCount() > 0) {
            $current_cart_images = array();

            foreach ($images_result as $key => $image_value) {
              $current_cart_images[] = $image_value['image'];
            }

            $current_cart['cart_product_images'] = $current_cart_images;
          }
          else{
            $current_cart['cart_product_images'] = null;
          }

          $current_cart['total_cart_amount'] = $current_cart_sales_price == 0 ? ($current_cart_price * $current_cart_quantity) + $current_cart['full_shipping_price'] : ($current_cart_sales_price * $current_cart_quantity) + $current_cart['full_shipping_price'];

          $cart_array[] = $current_cart;
        }
        $returnvalue = new genericClass();
        $returnvalue->engineMessage = 1;
        $returnvalue->resultData = $cart_array;
      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "No data found";
      }

      $conn->commit();
    } catch (PDOException $e) {
      $conn->rollback();
      throw $e;
    }

  }
  else {
    $returnvalue = new genericClass();
    $returnvalue->engineError = 3;
    $returnvalue->engineErrorMessage = "No connection";
  }

  echo json_encode($returnvalue);

?>
