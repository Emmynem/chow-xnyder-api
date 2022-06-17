<?php

  $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
  $allowed_domains = array("https://auth.reestoc.com", "https://dashboard.reestoc.com");
  foreach ($allowed_domains as $value) {if ($http_origin === $value) {header('Access-Control-Allow-Origin: ' . $http_origin);}}
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, origin, Accept-Language, Range, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");
  require '../../../config/connect_to_me.php';
  include_once "../../../config/functions.php";

  ini_set('max_execution_time', 3600);

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

      // $data['product_image_unique_id'] = isset($_GET['product_image_unique_id']);
      // $data['user_unique_id'] = isset($_GET['user_unique_id']);
      // $data['product_unique_id'] = isset($_GET['product_unique_id']);

      $product_image_unique_id = isset($_GET['product_image_unique_id']) ? $_GET['product_image_unique_id'] : $data['product_image_unique_id'];
      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $product_unique_id = isset($_GET['product_unique_id']) ? $_GET['product_unique_id'] : $data['product_unique_id'];
      $vendor_unique_id = isset($_POST['vendor_unique_id']) ? $_POST['vendor_unique_id'] : $data['vendor_unique_id'];

      $path_to_upload = "../../../../images/product_images/";
      // $path_to_delete = $_SERVER['DOCUMENT_ROOT']."/images/product_images"; // For online own
      $path_to_delete = $_SERVER['DOCUMENT_ROOT']."/cerotics_store/images/product_images"; // For offline own
      $path_to_save = "https://www.reestoc.com/images/product_images/";
      // $path_to_save = "https://www.reestock.com/images/product_images/";

      $sqlSearchUser = "SELECT unique_id FROM vendor_users WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":vendor_unique_id", $vendor_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $sql2 = "SELECT unique_id FROM products WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
        $query2 = $conn->prepare($sql2);
        $query2->bindParam(":unique_id", $product_unique_id);
        $query2->bindParam(":vendor_unique_id", $vendor_unique_id);
        $query2->bindParam(":status", $active);
        $query2->execute();

        if ($query2->rowCount() > 0) {

          $sql3 = "SELECT file FROM product_images WHERE unique_id=:unique_id AND product_unique_id=:product_unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
          $query3 = $conn->prepare($sql3);
          $query3->bindParam(":unique_id", $product_image_unique_id);
          $query3->bindParam(":product_unique_id", $product_unique_id);
          $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query3->bindParam(":status", $active);
          $query3->execute();

          if ($query3->rowCount() > 0) {

            $the_product_image_details = $query3->fetch();
            $old_product_image = $the_product_image_details[0];

            $sql = "DELETE FROM product_images WHERE unique_id=:unique_id AND product_unique_id=:product_unique_id AND vendor_unique_id=:vendor_unique_id";
            $query = $conn->prepare($sql);
            $query->bindParam(":unique_id", $product_image_unique_id);
            $query->bindParam(":product_unique_id", $product_unique_id);
            $query->bindParam(":vendor_unique_id", $vendor_unique_id);
            $query->execute();

            if ($query->rowCount() > 0) {
              unlink($path_to_delete."/".$old_product_image);
              $returnvalue = new genericClass();
              $returnvalue->engineMessage = 1;
              $returnvalue->resultData = 'Image Deleted Successfully';
            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Image Not Deleted";
            }


          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Old Item image not found";
          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Item not found";
        }

      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "Vendor user not found";
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
