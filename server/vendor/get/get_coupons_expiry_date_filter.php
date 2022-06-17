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
  $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : $data["start_date"];
  $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : $data["end_date"];

  if ($connected) {

    try {
      $conn->beginTransaction();

      $sql = "SELECT coupons.unique_id, coupons.user_unique_id, coupons.product_unique_id, coupons.category_unique_id, coupons.code, coupons.name, coupons.percentage, coupons.total_count, coupons.current_count, coupons.completion, coupons.expiry_date, coupons.added_date, coupons.last_modified, coupons.status,
      users.fullname as user_fullname, users.phone_number as user_phone_number, products.name as product_name, categories.name as category_name FROM coupons LEFT JOIN users ON coupons.user_unique_id = users.unique_id LEFT JOIN products ON coupons.product_unique_id = products.unique_id
      LEFT JOIN categories ON coupons.category_unique_id = categories.unique_id WHERE coupons.vendor_unique_id=:vendor_unique_id AND (coupons.expiry_date=:start_date OR coupons.expiry_date >:start_date) AND (coupons.expiry_date <:end_date OR coupons.expiry_date=:end_date) ORDER BY coupons.expiry_date DESC";
      $query = $conn->prepare($sql);
      $query->bindParam(":vendor_unique_id", $vendor_unique_id);
      $query->bindParam(":start_date", $start_date);
      $query->bindParam(":end_date", $end_date);
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
