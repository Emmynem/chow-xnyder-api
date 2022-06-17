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

      $sql = "SELECT shipping_fees.unique_id, shipping_fees.user_unique_id, shipping_fees.product_unique_id, shipping_fees.city, shipping_fees.state, shipping_fees.country, shipping_fees.price,
      shipping_fees.added_date, shipping_fees.last_modified, shipping_fees.status, products.name, products.duration, products.weight,  vendor_users.fullname as added_user_fullname FROM shipping_fees
      LEFT JOIN products ON shipping_fees.product_unique_id = products.unique_id INNER JOIN vendor_users ON shipping_fees.user_unique_id = vendor_users.unique_id WHERE shipping_fees.vendor_unique_id=:vendor_unique_id ORDER BY shipping_fees.added_date DESC";
      $query = $conn->prepare($sql);
      $query->bindParam(":vendor_unique_id", $vendor_unique_id);
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
