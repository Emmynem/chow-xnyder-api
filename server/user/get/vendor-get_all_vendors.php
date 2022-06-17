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

        $sql = "SELECT vendors.unique_id, vendors.business_name, vendors.stripped, vendors.details, vendors.fullname, vendors.email, vendors.phone_number, vendors.profile_image, vendors.cover_image, vendors.cover_image_file,
        vendors.opening_hours, vendors.closing_hours, vendors.city, vendors.state, vendors.country, vendors.address
        FROM vendors WHERE vendors.access=:status AND vendors.status=:status ORDER BY vendors.business_name ASC LIMIT ".$start_limit.",".$end_limit."";
        $query = $conn->prepare($sql);
        $query->bindParam(":status", $active);
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
