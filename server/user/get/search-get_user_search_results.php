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
  $search_word = isset($_GET['search_word']) ? $_GET['search_word'] : $data["search_word"];
  $start_limit = isset($_GET['start_limit']) ? $_GET['start_limit'] : $data["start_limit"];
  $end_limit = isset($_GET['end_limit']) ? $_GET['end_limit'] : $data["end_limit"];

  if ($connected) {

    try {
      $conn->beginTransaction();

      $active = $functions->active;
      $not_allowed_values = $functions->not_allowed_values;
      $anonymous = $functions->anonymous;
      $date_added = $functions->today;

      if (!in_array($search_word,$not_allowed_values)) {

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

          $search_history_array = array();

          $keyword = "%".$search_word."%";

          $sql3 = "SELECT products.unique_id, products.name, products.stripped, products.duration, products.weight, products.price, products.sales_price, products.views, products.favorites, products.good_rating, products.bad_rating,
          vendors.business_name as vendor_name, vendors.stripped as vendor_stripped, categories.name as category_name, categories.stripped as category_stripped, menus.name as menu_name, menus.stripped as menu_stripped,
          menus.start_time as menu_start_time, menus.end_time as menu_end_time FROM products INNER JOIN vendors ON products.vendor_unique_id = vendors.unique_id INNER JOIN categories ON products.category_unique_id = categories.unique_id
          LEFT JOIN menus ON products.menu_unique_id = menus.unique_id WHERE products.name LIKE :search_word OR products.name LIKE :search_word OR products.duration LIKE :search_word OR products.weight LIKE :search_word OR categories.name LIKE :search_word
          ORDER BY products.added_date ASC, products.views DESC, products.favorites DESC, products.good_rating DESC LIMIT ".$start_limit.",".$end_limit."";
          $query3 = $conn->prepare($sql3);
          $query3->bindParam(":search_word", $keyword);
          $query3->execute();

          $product_result = $query3->fetchAll();

          if ($query3->rowCount() > 0) {
            foreach ($product_result as $key => $product_value) {

              $current_search_history = array();
              $current_search_history['product_unique_id'] = $product_value['unique_id'];
              $current_search_history['search'] = $search_word;
              $current_search_history['name'] = $product_value['name'];
              $current_search_history['stripped'] = $product_value['stripped'];
              $current_search_history['duration'] = $product_value['duration'];
              $current_search_history['weight'] = $product_value['weight'];
              $current_search_history['price'] = $product_value['price'];
              $current_search_history['sales_price'] = $product_value['sales_price'];
              $current_search_history['views'] = $product_value['views'];
              $current_search_history['favorites'] = $product_value['favorites'];
              $current_search_history['good_rating'] = $product_value['good_rating'];
              $current_search_history['bad_rating'] = $product_value['bad_rating'];
              $current_search_history['vendor_name'] = $product_value['vendor_name'];
              $current_search_history['vendor_stripped'] = $product_value['vendor_stripped'];
              $current_search_history['category_name'] = $product_value['category_name'];
              $current_search_history['category_stripped'] = $product_value['category_stripped'];
              $current_search_history['menu_name'] = $product_value['menu_name'];
              $current_search_history['menu_stripped'] = $product_value['menu_stripped'];
              $current_search_history['menu_start_time'] = $product_value['menu_start_time'];
              $current_search_history['menu_end_time'] = $product_value['menu_end_time'];

              $product_id = $product_value['unique_id'];

              $sql2 = "SELECT image FROM product_images WHERE product_unique_id=:product_unique_id LIMIT 1";
              $query2 = $conn->prepare($sql2);
              $query2->bindParam(":product_unique_id", $product_id);
              $query2->execute();

              $images_result = $query2->fetchAll();

              if ($query2->rowCount() > 0) {
                $current_search_history_images = array();

                foreach ($images_result as $key => $image_value) {
                  $current_search_history_images[] = $image_value['image'];
                }

                $current_search_history['product_images'] = $current_search_history_images;
              }
              else{
                $current_search_history['product_images'] = null;
              }

              $search_history_array[] = $current_search_history;

            }

            $user_unique_id_alt = in_array($user_unique_id,$not_allowed_values) ? $anonymous : $user_unique_id;
            $unique_id = $functions->random_str(20);
            $type = "Available";

            $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
            $querySearchUser = $conn->prepare($sqlSearchUser);
            $querySearchUser->bindParam(":unique_id", $user_unique_id);
            $querySearchUser->bindParam(":status", $active);
            $querySearchUser->execute();

            if ($querySearchUser->rowCount() > 0) {

              $sql = "INSERT INTO search_history (unique_id, user_unique_id, search, type, added_date, last_modified, status)
              VALUES (:unique_id, :user_unique_id, :search, :type, :added_date, :last_modified, :status)";
              $query = $conn->prepare($sql);
              $query->bindParam(":unique_id", $unique_id);
              $query->bindParam(":user_unique_id", $user_unique_id_alt);
              $query->bindParam(":search", $search_word);
              $query->bindParam(":type", $type);
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
                $returnvalue->engineErrorMessage = "Not inserted (new search history)";
              }

            }
            else {

              $sql = "INSERT INTO search_history (unique_id, user_unique_id, search, type, added_date, last_modified, status)
              VALUES (:unique_id, :user_unique_id, :search, :type, :added_date, :last_modified, :status)";
              $query = $conn->prepare($sql);
              $query->bindParam(":unique_id", $unique_id);
              $query->bindParam(":user_unique_id", $user_unique_id_alt);
              $query->bindParam(":search", $search_word);
              $query->bindParam(":type", $type);
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
                $returnvalue->engineErrorMessage = "Not inserted (new search history)";
              }
            }

            $returnvalue = new genericClass();
            $returnvalue->engineMessage = 1;
            $returnvalue->resultData = $search_history_array;
          }
          else {

            $user_unique_id_alt = in_array($user_unique_id,$not_allowed_values) ? $anonymous : $user_unique_id;
            $unique_id = $functions->random_str(20);
            $type = "Unavailable";

            $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
            $querySearchUser = $conn->prepare($sqlSearchUser);
            $querySearchUser->bindParam(":unique_id", $user_unique_id);
            $querySearchUser->bindParam(":status", $active);
            $querySearchUser->execute();

            if ($querySearchUser->rowCount() > 0) {

              $sql = "INSERT INTO search_history (unique_id, user_unique_id, search, type, added_date, last_modified, status)
              VALUES (:unique_id, :user_unique_id, :search, :type, :added_date, :last_modified, :status)";
              $query = $conn->prepare($sql);
              $query->bindParam(":unique_id", $unique_id);
              $query->bindParam(":user_unique_id", $user_unique_id_alt);
              $query->bindParam(":search", $search_word);
              $query->bindParam(":type", $type);
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
                $returnvalue->engineErrorMessage = "Not inserted (new search history)";
              }

            }
            else {

              $sql = "INSERT INTO search_history (unique_id, user_unique_id, search, type, added_date, last_modified, status)
              VALUES (:unique_id, :user_unique_id, :search, :type, :added_date, :last_modified, :status)";
              $query = $conn->prepare($sql);
              $query->bindParam(":unique_id", $unique_id);
              $query->bindParam(":user_unique_id", $user_unique_id_alt);
              $query->bindParam(":search", $search_word);
              $query->bindParam(":type", $type);
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
                $returnvalue->engineErrorMessage = "Not inserted (new search history)";
              }
            }

            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "No data found";
          }

        }

      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "Empty search";
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
