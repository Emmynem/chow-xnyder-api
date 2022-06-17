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
      $yes = $functions->yes;
      $no = $functions->no;

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $product_unique_id = isset($_GET['product_unique_id']) ? $_GET['product_unique_id'] : $data['product_unique_id'];
      $rating = isset($_GET['rating']) ? $_GET['rating'] : $data['rating'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $validation = $functions->review_ratings_validation($rating);

        if ($validation["error"] == true) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = $validation["message"];
        }
        else {

          $sql2 = "SELECT unique_id, rating FROM ratings WHERE user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id AND status=:status";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":user_unique_id", $user_unique_id);
          $query2->bindParam(":product_unique_id", $product_unique_id);
          $query2->bindParam(":status", $active);
          $query2->execute();

          if ($query2->rowCount() > 0) {

            $the_rating = $query2->fetch();

            $the_rating_unique_id = $the_rating[0];
            $the_rating = $the_rating[1];

            if ($the_rating == $rating) {
              $returnvalue = new genericClass();
              $returnvalue->engineMessage = 1;
            }
            else {

              $sql = "UPDATE ratings SET rating=:rating, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id";
              $query = $conn->prepare($sql);
              $query->bindParam(":unique_id", $the_rating_unique_id);
              $query->bindParam(":user_unique_id", $user_unique_id);
              $query->bindParam(":product_unique_id", $product_unique_id);
              $query->bindParam(":rating", $rating);
              $query->bindParam(":last_modified", $date_added);
              $query->execute();

              if ($query->rowCount() > 0) {

                $sql4 = "SELECT good_rating, bad_rating FROM products WHERE unique_id=:unique_id AND status=:status";
                $query4 = $conn->prepare($sql4);
                $query4->bindParam(":unique_id", $product_unique_id);
                $query4->bindParam(":status", $active);
                $query4->execute();

                $the_review_ratings = $query4->fetch();

                if ($query4->rowCount() > 0) {

                  $the_total_good_ratings = (int)$the_review_ratings[0];
                  $the_total_bad_ratings = (int)$the_review_ratings[1];

                  $new_total_good_ratings = strtolower($rating) == $yes ? $the_total_good_ratings + 1 : $the_total_good_ratings - 1;
                  $new_total_bad_ratings = strtolower($rating) == $no ? $the_total_bad_ratings + 1 : $the_total_bad_ratings - 1;

                  $sql5 = "UPDATE products SET good_rating=:good_rating, bad_rating=:bad_rating, last_modified=:last_modified WHERE unique_id=:unique_id";
                  $query5 = $conn->prepare($sql5);
                  $query5->bindParam(":good_rating", $new_total_good_ratings);
                  $query5->bindParam(":bad_rating", $new_total_bad_ratings);
                  $query5->bindParam(":unique_id", $product_unique_id);
                  $query5->bindParam(":last_modified", $date_added);
                  $query5->execute();

                  if ($query5->rowCount() > 0) {
                    $returnvalue = new genericClass();
                    $returnvalue->engineMessage = 1;
                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = "Not edited (product ratings)";
                  }

                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Not found (product)";
                }

              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not added (rating)";
              }

            }

          }
          else {

            $unique_id = $functions->random_str(20);

            $sql = "INSERT INTO ratings (unique_id, user_unique_id, product_unique_id, rating, added_date, last_modified, status)
            VALUES (:unique_id, :user_unique_id, :product_unique_id, :rating, :added_date, :last_modified, :status)";
            $query = $conn->prepare($sql);
            $query->bindParam(":unique_id", $unique_id);
            $query->bindParam(":user_unique_id", $user_unique_id);
            $query->bindParam(":product_unique_id", $product_unique_id);
            $query->bindParam(":rating", $rating);
            $query->bindParam(":added_date", $date_added);
            $query->bindParam(":last_modified", $date_added);
            $query->bindParam(":status", $active);
            $query->execute();

            if ($query->rowCount() > 0) {

              $sql4 = "SELECT good_rating, bad_rating FROM products WHERE unique_id=:unique_id AND status=:status";
              $query4 = $conn->prepare($sql4);
              $query4->bindParam(":unique_id", $product_unique_id);
              $query4->bindParam(":status", $active);
              $query4->execute();

              $the_review_ratings = $query4->fetch();

              if ($query4->rowCount() > 0) {

                $the_total_good_ratings = (int)$the_review_ratings[0];
                $the_total_bad_ratings = (int)$the_review_ratings[1];

                $new_total_good_ratings = strtolower($rating) == $yes ? $the_total_good_ratings + 1 : $the_total_good_ratings;
                $new_total_bad_ratings = strtolower($rating) == $no ? $the_total_bad_ratings + 1 : $the_total_bad_ratings;

                $sql5 = "UPDATE products SET good_rating=:good_rating, bad_rating=:bad_rating, last_modified=:last_modified WHERE unique_id=:unique_id";
                $query5 = $conn->prepare($sql5);
                $query5->bindParam(":good_rating", $new_total_good_ratings);
                $query5->bindParam(":bad_rating", $new_total_bad_ratings);
                $query5->bindParam(":unique_id", $product_unique_id);
                $query5->bindParam(":last_modified", $date_added);
                $query5->execute();

                if ($query5->rowCount() > 0) {
                  $returnvalue = new genericClass();
                  $returnvalue->engineMessage = 1;
                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Not edited (product ratings)";
                }

              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not found (product)";
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Not added (rating)";
            }

          }

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
