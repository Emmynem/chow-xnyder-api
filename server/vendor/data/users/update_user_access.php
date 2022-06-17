<?php

  $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
  $allowed_domains = array("https://auth.reestoc.com", "https://dashboard.reestoc.com");
  foreach ($allowed_domains as $value) {if ($http_origin === $value) {header('Access-Control-Allow-Origin: ' . $http_origin);}}
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, origin, Accept-Language, Range, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");
  require '../../../config/connect_to_me.php';
  include_once "../../../config/functions.php";

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

      $date_added = $functions->today;
      $active = $functions->active;
      $authorized_users = $functions->authorized_users;

      $admin_user_unique_id = isset($_GET['admin_user_unique_id']) ? $_GET['admin_user_unique_id'] : $data['admin_user_unique_id'];
      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $access = isset($_GET['access']) ? $_GET['access'] : $data['access'];

      // $sqlSearchUser = "SELECT unique_id FROM management WHERE unique_id=:unique_id AND status=:status";
      // $querySearchUser = $conn->prepare($sqlSearchUser);
      // $querySearchUser->bindParam(":unique_id", $edit_user_unique_id);
      // $querySearchUser->bindParam(":status", $active);
      // $querySearchUser->execute();
      //
      // if ($querySearchUser->rowCount() > 0) {

        if (in_array($admin_user_unique_id,$authorized_users)) {

          $validation = $functions->update_user_access_validation($access);

          if ($validation["error"] == true) {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = $validation["message"];
          }
          else {

            $sql2 = "SELECT fullname FROM users WHERE unique_id=:unique_id AND status=:status";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(":unique_id", $user_unique_id);
            $query2->bindParam(":status", $active);
            $query2->execute();

            if ($query2->rowCount() > 0) {

              $sql = "UPDATE users SET access=:access, last_modified=:last_modified WHERE unique_id=:unique_id";
              $query = $conn->prepare($sql);
              $query->bindParam(":access", $access);
              $query->bindParam(":unique_id", $user_unique_id);
              $query->bindParam(":last_modified", $date_added);
              $query->execute();

              if ($query->rowCount() > 0) {
                $returnvalue = new genericClass();
                $returnvalue->engineMessage = 1;
              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not edited (user access)";
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "User not found";
            }

          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Unauthorized access key";
        }

      // }
      // else {
      //   $returnvalue = new genericClass();
      //   $returnvalue->engineError = 2;
      //   $returnvalue->engineErrorMessage = "Management user not found";
      // }

      $conn->commit();
    } catch (PDOException $e) {
      $conn->rollback();
      throw $e;
    }

  }
  else {
    $returnvalue = new genericClass();
    $returnvalue->engineError = 2;
    $returnvalue->engineErrorMessage = "No connection";
  }

  echo json_encode($returnvalue);

?>
