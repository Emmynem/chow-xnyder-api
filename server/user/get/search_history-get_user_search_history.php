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

  if ($connected) {

    try {
      $conn->beginTransaction();

      $search_history_array = array();

      $sql = "SELECT search, type, last_modified FROM search_history WHERE user_unique_id=:user_unique_id ORDER BY CASE WHEN user_unique_id != 'Anonymous' THEN 0 ELSE 1 END, added_date DESC";
      $query = $conn->prepare($sql);
      $query->bindParam(":user_unique_id", $user_unique_id);
      $query->execute();

      $result = $query->fetchAll();

      if ($query->rowCount() > 0) {
        foreach ($result as $key => $value) {

          $current_search_history = array();

          $current_search_history['search'] = $value['search'];
          $current_search_history['search_type'] = $value['type'];
          $current_search_history['last_modified'] = $value['last_modified'];

          $search_history_array[] = $current_search_history;

        }
        $returnvalue = new genericClass();
        $returnvalue->engineMessage = 1;
        $returnvalue->resultData = $search_history_array;
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
