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

  $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data["user_unique_id"];
  $order_unique_id = isset($_GET['order_unique_id']) ? $_GET['order_unique_id'] : $data["order_unique_id"];

  if ($connected) {

    try {
      $conn->beginTransaction();

      $active = $functions->active;

      $disputes_array = array();

      $sql = "SELECT disputes.unique_id, disputes.order_unique_id, disputes.message, disputes.last_modified,
      products.unique_id as product_unique_id, products.name, products.stripped, vendors.business_name as vendor_name, vendors.stripped as vendor_stripped FROM disputes
      INNER JOIN users ON disputes.user_unique_id = users.unique_id INNER JOIN orders ON disputes.order_unique_id = orders.unique_id LEFT JOIN vendors ON orders.vendor_unique_id = vendors.unique_id
      LEFT JOIN products ON orders.product_unique_id = products.unique_id WHERE disputes.user_unique_id=:user_unique_id AND disputes.order_unique_id=:order_unique_id AND disputes.status=:status ORDER BY disputes.added_date DESC";
      $query = $conn->prepare($sql);
      $query->bindParam(":user_unique_id", $user_unique_id);
      $query->bindParam(":order_unique_id", $order_unique_id);
      $query->bindParam(":status", $active);
      $query->execute();

      $result = $query->fetchAll();

      if ($query->rowCount() > 0) {
        foreach ($result as $key => $value) {

          $current_disputes = array();
          // $current_disputes['unique_id'] = $value['unique_id'];
          // $current_disputes['order_unique_id'] = $value['order_unique_id'];
          $current_disputes['message'] = $value['message'];
          $current_disputes['last_modified'] = $value['last_modified'];
          $current_disputes['name'] = $value['name'];
          $current_disputes['stripped'] = $value['stripped'];
          $current_disputes['vendor_name'] = $value['vendor_name'];
          $current_disputes['vendor_stripped'] = $value['vendor_stripped'];

          $order_unique_id = $value['order_unique_id'];

          $product_id = $value['product_unique_id'];

          $sql2 = "SELECT image FROM product_images WHERE product_unique_id=:product_unique_id LIMIT 1";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":product_unique_id", $product_id);
          $query2->execute();

          $images_result = $query2->fetchAll();

          if ($query2->rowCount() > 0) {
            $current_disputes_images = array();

            foreach ($images_result as $key => $image_value) {
              $current_disputes_images[] = $image_value['image'];
            }

            $current_disputes['product_images'] = $current_disputes_images;
          }
          else{
            $current_disputes['product_images'] = null;
          }

          $disputes_array[] = $current_disputes;
        }
        $returnvalue = new genericClass();
        $returnvalue->engineMessage = 1;
        $returnvalue->resultData = $disputes_array;
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
