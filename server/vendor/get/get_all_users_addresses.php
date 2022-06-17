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

      $sql = "SELECT users_addresses.unique_id, users_addresses.user_unique_id, users_addresses.firstname, users_addresses.lastname, users_addresses.address,
      users_addresses.additional_information, users_addresses.city, users_addresses.state, users_addresses.country, users_addresses.default_status, users_addresses.added_date,
      users_addresses.last_modified, users_addresses.status, users.fullname as user_fullname FROM users_addresses INNER JOIN users ON users_addresses.user_unique_id = users.unique_id
      ORDER BY FIELD (users_addresses.default_status, 'Yes') DESC, users_addresses.added_date DESC";
      $query = $conn->prepare($sql);
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
