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

  $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data["vendor_unique_id"];

  if ($connected) {

    try {
      $conn->beginTransaction();

      $product_array = array();

      $sql3 = "SELECT products.unique_id, products.name, products.stripped, products.duration, products.weight, products.price, products.sales_price, vendors.business_name as vendor_name, vendors.stripped as vendor_stripped
      FROM products INNER JOIN vendors ON products.vendor_unique_id = vendors.unique_id WHERE products.vendor_unique_id=:vendor_unique_id ORDER BY products.name ASC";
      $query3 = $conn->prepare($sql3);
      $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
      $query3->execute();

      $product_result = $query3->fetchAll();

      if ($query3->rowCount() > 0) {

        foreach ($product_result as $key => $value) {

          $current_product['unique_id'] = $value['unique_id'];
          $current_product['name'] = $value['name'];
          $current_product['stripped'] = $value['stripped'];
          $current_product['duration'] = $value['duration'];
          $current_product['weight'] = $value['weight'];
          $current_product['price'] = $value['price'];
          $current_product['sales_price'] = $value['sales_price'];
          $current_product['vendor_name'] = $value['vendor_name'];
          $current_product['vendor_stripped'] = $value['vendor_stripped'];

          $product_id = $value['unique_id'];

          $sql4 = "SELECT image FROM product_images WHERE product_unique_id=:product_unique_id";
          $query4 = $conn->prepare($sql4);
          $query4->bindParam(":product_unique_id", $product_id);
          $query4->execute();

          $product_images_result = $query4->fetchAll();

          if ($query4->rowCount() > 0) {
            $current_product_images = array();

            foreach ($product_images_result as $key => $image_value) {
              $current_product_images[] = $image_value['image'];
            }

            $current_product['product_images'] = $current_product_images;
          }
          else{
            $current_product['product_images'] = null;
          }

          $product_array[] = $current_product;
        }
        $returnvalue = new genericClass();
        $returnvalue->engineMessage = 1;
        $returnvalue->resultData = $product_array;
      }
      else{
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
