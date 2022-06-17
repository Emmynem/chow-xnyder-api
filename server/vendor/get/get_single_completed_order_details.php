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
  $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
  $order_unique_id = isset($_GET['order_unique_id']) ? $_GET['order_unique_id'] : $data['order_unique_id'];
  $tracker_unique_id = isset($_GET['tracker_unique_id']) ? $_GET['tracker_unique_id'] : $data['tracker_unique_id'];

  if ($connected) {

    try {
      $conn->beginTransaction();

      $sql = "SELECT vendors.business_name as vendor_name, vendors.stripped as vendor_stripped, orders_completed.order_unique_id, orders_completed.tracker_unique_id, orders_completed.quantity, orders_completed.payment_method, orders_completed.product_name,
      orders_completed.coupon_name, orders_completed.coupon_code, orders_completed.coupon_percentage, orders_completed.coupon_price, orders_completed.user_address_fullname, orders_completed.city, orders_completed.state, orders_completed.country,
      orders_completed.shipping_fee_price, orders_completed.total_price, orders_completed.added_date FROM orders_completed LEFT JOIN vendors ON orders_completed.vendor_unique_id = vendors.unique_id WHERE orders_completed.vendor_unique_id=:vendor_unique_id
      AND orders_completed.user_unique_id=:user_unique_id AND orders_completed.order_unique_id=:order_unique_id AND orders_completed.tracker_unique_id=:tracker_unique_id ORDER BY orders_completed.added_date DESC";
      $query = $conn->prepare($sql);
      $query->bindParam(":vendor_unique_id", $vendor_unique_id);
      $query->bindParam(":user_unique_id", $user_unique_id);
      $query->bindParam(":order_unique_id", $order_unique_id);
      $query->bindParam(":tracker_unique_id", $tracker_unique_id);
      $query->execute();

      $result = $query->fetchAll();

      if ($query->rowCount() > 0) {
        $returnvalue = new genericClass();
        $returnvalue->engineMessage = 1;
        $returnvalue->resultData = $result;
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
