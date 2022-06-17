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
      $tomorrow = $functions->tomorrow;
      $active = $functions->active;
      $not_allowed_values = $functions->not_allowed_values;
      $anonymous = $functions->anonymous;

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $product_unique_id = isset($_GET['product_unique_id']) ? $_GET['product_unique_id'] : $data['product_unique_id'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $sql4 = "SELECT views FROM products WHERE unique_id=:unique_id AND status=:status";
        $query4 = $conn->prepare($sql4);
        $query4->bindParam(":unique_id", $product_unique_id);
        $query4->bindParam(":status", $active);
        $query4->execute();

        if ($query4->rowCount() > 0) {

          $the_product_views = $query4->fetch();
          $recent_views = $the_product_views[0];

          $new_views = $recent_views + 1;

          $sql5 = "UPDATE products SET views=:views, last_modified=:last_modified WHERE unique_id=:unique_id";
          $query5 = $conn->prepare($sql5);
          $query5->bindParam(":unique_id", $product_unique_id);
          $query5->bindParam(":views", $new_views);
          $query5->bindParam(":last_modified", $date_added);
          $query5->execute();

          if ($query5->rowCount() > 0) {

            $sql2 = "SELECT added_date, unique_id FROM view_history WHERE product_unique_id=:product_unique_id AND user_unique_id=:user_unique_id AND status=:status ORDER BY added_date DESC LIMIT 1";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(":product_unique_id", $product_unique_id);
            $query2->bindParam(":user_unique_id", $user_unique_id);
            $query2->bindParam(":status", $active);
            $query2->execute();

            if ($query2->rowCount() > 0) {

              $view_history_details = $query2->fetch();
              $the_added_date = $view_history_details[0];
              $the_added_date_unique_id = $view_history_details[1];

              $converted_date = strtotime($the_added_date);
              $new_date = date('Y-m-d H:i:s', $converted_date);

              $compare_date = new DateTime($new_date);
              $todays_date = new DateTime($date_added);
              $difference = $compare_date->diff($todays_date);

              if ($difference->days >= 1) {
                $user_unique_id_alt = in_array($user_unique_id,$not_allowed_values) ? $anonymous : $user_unique_id;

                $unique_id = $functions->random_str(20);

                $sql = "INSERT INTO view_history (unique_id, user_unique_id, product_unique_id, added_date, last_modified, status)
                VALUES (:unique_id, :user_unique_id, :product_unique_id, :added_date, :last_modified, :status)";
                $query = $conn->prepare($sql);
                $query->bindParam(":unique_id", $unique_id);
                $query->bindParam(":user_unique_id", $user_unique_id_alt);
                $query->bindParam(":product_unique_id", $product_unique_id);
                $query->bindParam(":added_date", $date_added);
                $query->bindParam(":last_modified", $date_added);
                $query->bindParam(":status", $active);
                $query->execute();

                if ($query->rowCount() > 0) {
                  $returnvalue = new genericClass();
                  $returnvalue->engineMessage = 1;
                }
                else{
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Not inserted (new product view)";
                }
              }
              else {
                $sql3 = "UPDATE view_history SET added_date=:added_date, last_modified=:last_modified WHERE unique_id=:unique_id AND product_unique_id=:product_unique_id AND user_unique_id=:user_unique_id";
                $query3 = $conn->prepare($sql3);
                $query3->bindParam(":unique_id", $the_added_date_unique_id);
                $query3->bindParam(":added_date", $date_added);
                $query3->bindParam(":product_unique_id", $product_unique_id);
                $query3->bindParam(":user_unique_id", $user_unique_id);
                $query3->bindParam(":last_modified", $date_added);
                $query3->execute();

                if ($query3->rowCount() > 0) {
                  $returnvalue = new genericClass();
                  $returnvalue->engineMessage = 1;
                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Not updated (product view history)";
                }
              }

            }
            else {
              $user_unique_id_alt = in_array($user_unique_id,$not_allowed_values) ? $anonymous : $user_unique_id;

              $unique_id = $functions->random_str(20);

              $sql = "INSERT INTO view_history (unique_id, user_unique_id, product_unique_id, added_date, last_modified, status)
              VALUES (:unique_id, :user_unique_id, :product_unique_id, :added_date, :last_modified, :status)";
              $query = $conn->prepare($sql);
              $query->bindParam(":unique_id", $unique_id);
              $query->bindParam(":user_unique_id", $user_unique_id_alt);
              $query->bindParam(":product_unique_id", $product_unique_id);
              $query->bindParam(":added_date", $date_added);
              $query->bindParam(":last_modified", $date_added);
              $query->bindParam(":status", $active);
              $query->execute();

              if ($query->rowCount() > 0) {
                $returnvalue = new genericClass();
                $returnvalue->engineMessage = 1;
              }
              else{
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not inserted (new product view)";
              }

            }

          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Not updated (product view)";
          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Not found (product)";
        }

      }
      else {

        $sql4 = "SELECT views FROM products WHERE unique_id=:unique_id AND status=:status";
        $query4 = $conn->prepare($sql4);
        $query4->bindParam(":unique_id", $product_unique_id);
        $query4->bindParam(":status", $active);
        $query4->execute();

        if ($query4->rowCount() > 0) {

          $the_product_views = $query4->fetch();
          $recent_views = $the_product_views[0];

          $new_views = $recent_views + 1;

          $sql5 = "UPDATE products SET views=:views, last_modified=:last_modified WHERE unique_id=:unique_id";
          $query5 = $conn->prepare($sql5);
          $query5->bindParam(":unique_id", $product_unique_id);
          $query5->bindParam(":views", $new_views);
          $query5->bindParam(":last_modified", $date_added);
          $query5->execute();

          if ($query5->rowCount() > 0) {

            $user_unique_id_alt = in_array($user_unique_id,$not_allowed_values) ? $anonymous : $user_unique_id;

            $unique_id = $functions->random_str(20);

            $sql = "INSERT INTO view_history (unique_id, user_unique_id, product_unique_id, added_date, last_modified, status)
            VALUES (:unique_id, :user_unique_id, :product_unique_id, :added_date, :last_modified, :status)";
            $query = $conn->prepare($sql);
            $query->bindParam(":unique_id", $unique_id);
            $query->bindParam(":user_unique_id", $user_unique_id_alt);
            $query->bindParam(":product_unique_id", $product_unique_id);
            $query->bindParam(":added_date", $date_added);
            $query->bindParam(":last_modified", $date_added);
            $query->bindParam(":status", $active);
            $query->execute();

            if ($query->rowCount() > 0) {
              $returnvalue = new genericClass();
              $returnvalue->engineMessage = 1;
            }
            else{
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Not inserted (new product view)";
            }

          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Not updated (product view)";
          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Not found (product)";
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
