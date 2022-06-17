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
      $not_active = $functions->not_active;

      $favorite_unique_id = isset($_GET['favorite_unique_id']) ? $_GET['favorite_unique_id'] : $data['favorite_unique_id'];
      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $product_unique_id = isset($_GET['product_unique_id']) ? $_GET['product_unique_id'] : $data['product_unique_id'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $sql2 = "SELECT unique_id FROM favorites WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id AND status=:status";
        $query2 = $conn->prepare($sql2);
        $query2->bindParam(":unique_id", $favorite_unique_id);
        $query2->bindParam(":user_unique_id", $user_unique_id);
        $query2->bindParam(":product_unique_id", $product_unique_id);
        $query2->bindParam(":status", $active);
        $query2->execute();

        if ($query2->rowCount() > 0) {

          $sql = "DELETE FROM favorites WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id";
          $query = $conn->prepare($sql);
          $query->bindParam(":unique_id", $favorite_unique_id);
          $query->bindParam(":user_unique_id", $user_unique_id);
          $query->bindParam(":product_unique_id", $product_unique_id);
          $query->execute();

          if ($query->rowCount() > 0) {

            $sql3 = "SELECT favorites FROM products WHERE unique_id=:unique_id";
            $query3 = $conn->prepare($sql3);
            $query3->bindParam(":unique_id", $product_unique_id);
            $query3->execute();

            if ($query3->rowCount() > 0) {

              $favorites_details = $query3->fetch();
              $recent_favorites = (int)$favorites_details[0];
              $new_favorites = $recent_favorites - 1;

              $sql4 = "UPDATE products SET favorites=:favorites, last_modified=:last_modified WHERE unique_id=:unique_id";
              $query4 = $conn->prepare($sql4);
              $query4->bindParam(":unique_id", $product_unique_id);
              $query4->bindParam(":favorites", $new_favorites);
              $query4->bindParam(":last_modified", $date_added);
              $query4->execute();

              if ($query4->rowCount() > 0) {
                $returnvalue = new genericClass();
                $returnvalue->engineMessage = 1;
              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not updated (product favorites)";
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Product not found";
            }

          }
          else{
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Not removed (favorite)";
          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Favorite not found";
        }

      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "User not found";
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
