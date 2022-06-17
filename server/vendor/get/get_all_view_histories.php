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

  if ($connected) {

    try {
      $conn->beginTransaction();

      $view_history_array = array();

      $sql = "SELECT view_history.unique_id, view_history.user_unique_id, view_history.product_unique_id, view_history.added_date, view_history.last_modified, view_history.status, users.fullname as user_fullname, users.email as user_email, users.phone_number as user_phone_number,
      products.name, products.stripped,  products.duration, products.weight, products.price, products.sales_price, products.views, products.favorites, products.good_rating, products.bad_rating, vendors.business_name as vendor_name, vendors.stripped as vendor_stripped, categories.name as category_name, categories.stripped as category_stripped
      FROM view_history INNER JOIN users ON view_history.user_unique_id = users.unique_id LEFT JOIN products ON view_history.product_unique_id = products.unique_id LEFT JOIN vendors ON products.vendor_unique_id = vendors.unique_id INNER JOIN categories ON products.category_unique_id = categories.unique_id ORDER BY view_history.added_date DESC";
      $query = $conn->prepare($sql);
      $query->execute();

      $result = $query->fetchAll();

      if ($query->rowCount() > 0) {
        foreach ($result as $key => $value) {

          $current_view_history = array();
          $current_view_history['unique_id'] = $value['unique_id'];
          $current_view_history['user_unique_id'] = $value['user_unique_id'];
          // $current_view_history['product_unique_id'] = $value['product_unique_id'];
          $current_view_history['added_date'] = $value['added_date'];
          $current_view_history['last_modified'] = $value['last_modified'];
          $current_view_history['user_fullname'] = $value['user_fullname'];
          $current_view_history['user_email'] = $value['user_email'];
          $current_view_history['user_phone_number'] = $value['user_phone_number'];
          $current_view_history['name'] = $value['name'];
          $current_view_history['stripped'] = $value['stripped'];
          $current_view_history['duration'] = $value['duration'];
          $current_view_history['weight'] = $value['weight'];
          $current_view_history['price'] = $value['price'];
          $current_view_history['sales_price'] = $value['sales_price'];
          $current_view_history['views'] = $value['views'];
          $current_view_history['favorites'] = $value['favorites'];
          $current_view_history['good_rating'] = $value['good_rating'];
          $current_view_history['bad_rating'] = $value['bad_rating'];
          $current_view_history['vendor_name'] = $value['vendor_name'];
          $current_view_history['vendor_stripped'] = $value['vendor_stripped'];
          $current_view_history['category_name'] = $value['category_name'];
          $current_view_history['category_stripped'] = $value['category_stripped'];

          $product_id = $value['product_unique_id'];

          $sql2 = "SELECT image FROM product_images WHERE product_unique_id=:product_unique_id LIMIT 1";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":product_unique_id", $product_id);
          $query2->execute();

          $images_result = $query2->fetchAll();

          if ($query2->rowCount() > 0) {
            $current_view_history_images = array();

            foreach ($images_result as $key => $image_value) {
              $current_view_history_images[] = $image_value['image'];
            }

            $current_view_history['product_images'] = $current_view_history_images;
          }
          else{
            $current_view_history['product_images'] = null;
          }

          $view_history_array[] = $current_view_history;
        }
        $returnvalue = new genericClass();
        $returnvalue->engineMessage = 1;
        $returnvalue->resultData = $view_history_array;
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
