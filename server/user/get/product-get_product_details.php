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
  $vendor_stripped = isset($_GET['vendor_stripped']) ? $_GET['vendor_stripped'] : $data["vendor_stripped"];
  $category_stripped = isset($_GET['category_stripped']) ? $_GET['category_stripped'] : $data["category_stripped"];
  $stripped = isset($_GET['stripped']) ? $_GET['stripped'] : $data["stripped"];

  if ($connected) {

    try {
      $conn->beginTransaction();

      $active = $functions->active;
      $not_allowed_values = $functions->not_allowed_values;
      $anonymous = $functions->anonymous;
      $date_added = $functions->today;

      $sql7 = "SELECT unique_id, stripped FROM vendors WHERE stripped=:stripped AND status=:status";
      $query7 = $conn->prepare($sql7);
      $query7->bindParam(":stripped", $vendor_stripped);
      $query7->bindParam(":status", $active);
      $query7->execute();

      $result7 = $query7->fetch();

      if ($query7->rowCount() > 0) {

        $vendor_unique_id = $result7[0];

        $sql8 = "SELECT unique_id, stripped FROM categories WHERE stripped=:stripped AND status=:status";
        $query8 = $conn->prepare($sql8);
        $query8->bindParam(":stripped", $category_stripped);
        $query8->bindParam(":status", $active);
        $query8->execute();

        $result8 = $query8->fetch();

        if ($query8->rowCount() > 0) {

          $category_unique_id = $result8[0];

          $product_array = array();

          $sql9 = "SELECT products.unique_id, products.name, products.stripped, products.duration, products.weight, products.price, products.sales_price, products.views, products.favorites, products.good_rating, products.bad_rating,
          vendors.business_name as vendor_name, vendors.stripped as vendor_stripped, categories.name as category_name, categories.stripped as category_stripped, menus.name as menu_name, menus.stripped as menu_stripped,
          menus.start_time as menu_start_time, menus.end_time as menu_end_time FROM products INNER JOIN vendors ON products.vendor_unique_id = vendors.unique_id INNER JOIN categories ON products.category_unique_id = categories.unique_id
          LEFT JOIN menus ON products.menu_unique_id = menus.unique_id WHERE products.vendor_unique_id=:vendor_unique_id AND products.category_unique_id=:category_unique_id AND products.stripped=:stripped AND products.status=:status ORDER BY products.added_date ASC";
          $query9 = $conn->prepare($sql9);
          $query9->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query9->bindParam(":category_unique_id", $category_unique_id);
          $query9->bindParam(":stripped", $stripped);
          $query9->bindParam(":status", $active);
          $query9->execute();

          $product_result = $query9->fetchAll();

          if ($query9->rowCount() > 0) {

            foreach ($product_result as $key => $value) {

              $current_product['unique_id'] = $value['unique_id'];
              $current_product['name'] = $value['name'];
              $current_product['stripped'] = $value['stripped'];
              $current_product['duration'] = $value['duration'];
              $current_product['weight'] = $value['weight'];
              $current_product['price'] = $value['price'];
              $current_product['sales_price'] = $value['sales_price'];
              $current_product['views'] = $value['views'];
              $current_product['favorites'] = $value['favorites'];
              $current_product['good_rating'] = $value['good_rating'];
              $current_product['bad_rating'] = $value['bad_rating'];
              $current_product['vendor_name'] = $value['vendor_name'];
              $current_product['vendor_stripped'] = $value['vendor_stripped'];
              $current_product['category_name'] = $value['category_name'];
              $current_product['category_stripped'] = $value['category_stripped'];
              $current_product['menu_name'] = $value['menu_name'];
              $current_product['menu_stripped'] = $value['menu_stripped'];
              $current_product['menu_start_time'] = $value['menu_start_time'];
              $current_product['menu_end_time'] = $value['menu_end_time'];

              $product_id = $value['unique_id'];

              $sql10 = "SELECT image FROM product_images WHERE product_unique_id=:product_unique_id";
              $query10 = $conn->prepare($sql10);
              $query10->bindParam(":product_unique_id", $product_id);
              $query10->execute();

              $product_images_result = $query10->fetchAll();

              if ($query10->rowCount() > 0) {
                $current_product_images = array();

                foreach ($product_images_result as $key => $image_value) {
                  $current_product_images[] = $image_value['image'];
                }

                $current_product['product_images'] = $current_product_images;
              }
              else{
                $current_product['product_images'] = null;
              }

              $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
              $querySearchUser = $conn->prepare($sqlSearchUser);
              $querySearchUser->bindParam(":unique_id", $user_unique_id);
              $querySearchUser->bindParam(":status", $active);
              $querySearchUser->execute();

              if ($querySearchUser->rowCount() > 0) {

                $sql4 = "SELECT views FROM products WHERE unique_id=:unique_id AND status=:status";
                $query4 = $conn->prepare($sql4);
                $query4->bindParam(":unique_id", $product_id);
                $query4->bindParam(":status", $active);
                $query4->execute();

                if ($query4->rowCount() > 0) {

                  $the_product_views = $query4->fetch();
                  $recent_views = $the_product_views[0];

                  $new_views = $recent_views + 1;

                  $sql5 = "UPDATE products SET views=:views, last_modified=:last_modified WHERE unique_id=:unique_id";
                  $query5 = $conn->prepare($sql5);
                  $query5->bindParam(":unique_id", $product_id);
                  $query5->bindParam(":views", $new_views);
                  $query5->bindParam(":last_modified", $date_added);
                  $query5->execute();

                  if ($query5->rowCount() > 0) {

                    $sql2 = "SELECT added_date, unique_id FROM view_history WHERE product_unique_id=:product_unique_id AND user_unique_id=:user_unique_id AND status=:status ORDER BY added_date DESC LIMIT 1";
                    $query2 = $conn->prepare($sql2);
                    $query2->bindParam(":product_unique_id", $product_id);
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
                        $query->bindParam(":product_unique_id", $product_id);
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
                        $query3->bindParam(":product_unique_id", $product_id);
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
                      $query->bindParam(":product_unique_id", $product_id);
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
                $query4->bindParam(":unique_id", $product_id);
                $query4->bindParam(":status", $active);
                $query4->execute();

                if ($query4->rowCount() > 0) {

                  $the_product_views = $query4->fetch();
                  $recent_views = $the_product_views[0];

                  $new_views = $recent_views + 1;

                  $sql5 = "UPDATE products SET views=:views, last_modified=:last_modified WHERE unique_id=:unique_id";
                  $query5 = $conn->prepare($sql5);
                  $query5->bindParam(":unique_id", $product_id);
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
                    $query->bindParam(":product_unique_id", $product_id);
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

              $product_array[] = $current_product;
            }
            $returnvalue = new genericClass();
            $returnvalue->engineMessage = 1;
            $returnvalue->resultData = $product_array;
          }
          else{
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "No data found";
          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "No data found (category)";
        }

      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "No data found (vendor)";
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
