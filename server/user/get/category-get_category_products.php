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

  $category_stripped = isset($_GET['category_stripped']) ? $_GET['category_stripped'] : $data["category_stripped"];
  $start_limit = isset($_GET['start_limit']) ? $_GET['start_limit'] : $data["start_limit"];
  $end_limit = isset($_GET['end_limit']) ? $_GET['end_limit'] : $data["end_limit"];

  if ($connected) {

    try {
      $conn->beginTransaction();

      $active = $functions->active;

      $not_allowed_values = $functions->not_allowed_values;

      $start_limit = in_array($start_limit,$not_allowed_values) ? 0 : $start_limit;

      if ($functions->validateInt($start_limit) == false && $start_limit != "0") {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "Specify start limit";
      }
      else if ($functions->validateInt($end_limit) == false) {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "Specify end limit";
      }
      else {

        $sql = "SELECT unique_id, name, stripped FROM categories WHERE stripped=:stripped AND status=:status";
        $query = $conn->prepare($sql);
        $query->bindParam(":stripped", $category_stripped);
        $query->bindParam(":status", $active);
        $query->execute();

        $result = $query->fetch();

        if ($query->rowCount() > 0) {

          $category_unique_id = $result[0];

          $product_array = array();

          $sql3 = "SELECT products.unique_id, products.name, products.stripped, products.duration, products.weight, products.price, products.sales_price, products.views, products.favorites, products.good_rating, products.bad_rating,
          vendors.business_name as vendor_name, vendors.stripped as vendor_stripped, categories.name as category_name, categories.stripped as category_stripped, menus.name as menu_name, menus.stripped as menu_stripped,
          menus.start_time as menu_start_time, menus.end_time as menu_end_time FROM products INNER JOIN vendors ON products.vendor_unique_id = vendors.unique_id INNER JOIN categories ON products.category_unique_id = categories.unique_id
          LEFT JOIN menus ON products.menu_unique_id = menus.unique_id WHERE products.category_unique_id=:category_unique_id AND products.status=:status ORDER BY products.added_date ASC, products.views DESC, products.favorites DESC, products.good_rating DESC LIMIT ".$start_limit.",".$end_limit."";
          $query3 = $conn->prepare($sql3);
          $query3->bindParam(":category_unique_id", $category_unique_id);
          $query3->bindParam(":status", $active);
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
              $current_product['views'] = $value['views'];
              $current_product['favorites'] = $value['favorites'];
              $current_product['good_rating'] = $value['good_rating'];
              $current_product['bad_rating'] = $value['bad_rating'];
              $current_product['vendor_name'] = $value['vendor_name'];
              $current_product['vendor_stripped'] = $value['vendor_stripped'];
              $current_product['category_name'] = $value['category_name'];
              $current_product['category_stripped'] = $value['category_stripped'];
              $current_product['menu_name'] = $value['menu_name'];
              $current_product['menu_stripped'] = $value['menu_stripped'];
              $current_product['menu_start_time'] = $value['menu_start_time'];
              $current_product['menu_end_time'] = $value['menu_end_time'];

              $product_id = $value['unique_id'];

              $sql4 = "SELECT image FROM product_images WHERE product_unique_id=:product_unique_id LIMIT 1";
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

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "No data found";
        }

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
