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

      $favorite_array = array();

      $sql = "SELECT favorites.product_unique_id, favorites.last_modified, users.fullname as user_fullname, users.email as user_email, products.category_unique_id,
      products.name, products.stripped, products.favorites, vendors.business_name as vendor_name, vendors.stripped as vendor_stripped FROM favorites
      INNER JOIN users ON favorites.user_unique_id = users.unique_id LEFT JOIN products ON favorites.product_unique_id = products.unique_id
      LEFT JOIN vendors ON products.vendor_unique_id = vendors.unique_id WHERE products.vendor_unique_id=:vendor_unique_id ORDER BY favorites.added_date DESC";
      $query = $conn->prepare($sql);
      $query->bindParam(":vendor_unique_id", $vendor_unique_id);
      $query->execute();

      $result = $query->fetchAll();

      if ($query->rowCount() > 0) {
        foreach ($result as $key => $value) {

          $current_product = array();
          // $current_product['product_unique_id'] = $value['product_unique_id'];
          $current_product['name'] = $value['name'];
          $current_product['stripped'] = $value['stripped'];
          $current_product['favorites'] = $value['favorites'];
          $current_product['last_modified'] = $value['last_modified'];
          $current_product['user_fullname'] = $value['user_fullname'];
          $current_product['user_email'] = $value['user_email'];
          $current_product['vendor_name'] = $value['vendor_name'];
          $current_product['vendor_stripped'] = $value['vendor_stripped'];

          $product_id = $value['product_unique_id'];

          $sql2 = "SELECT image FROM product_images WHERE product_unique_id=:product_unique_id LIMIT 1";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":product_unique_id", $product_id);
          $query2->execute();

          $images_result = $query2->fetchAll();

          if ($query2->rowCount() > 0) {
            $current_product_images = array();

            foreach ($images_result as $key => $image_value) {
              $current_product_images[] = $image_value['image'];
            }

            $current_product['product_images'] = $current_product_images;
          }
          else{
            $current_product['product_images'] = null;
          }

          $favorite_array[] = $current_product;
        }
        $returnvalue = new genericClass();
        $returnvalue->engineMessage = 1;
        $returnvalue->resultData = $favorite_array;
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
