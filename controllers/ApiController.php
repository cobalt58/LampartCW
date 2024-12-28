<?php

namespace controllers;

use core\App;
use core\auth\Registrar;
use core\auth\UserUpdater;
use core\Controller;
use core\exceptions\ServerException;
use core\Request;
use DateTime;
use http\forms\RegForm;
use http\forms\UpdateUserForm;
use models\orm\Users;
use ReflectionClass;

class ApiController extends Controller
{
    public function index()
    {
        $this->error(500, 'How did you get here?');
        die();
    }

    public function users()
    {
        $this->redirectToController(ApiUsersController::class);
    }

    public function properties()
    {
        $this->redirectToController(ApiPropertiesController::class);
    }

    public function promotions()
    {
        $this->redirectToController(ApiPromotionController::class);
    }
    public function carts()
    {
        $this->redirectToController(ApiCartsController::class);
    }
    public function orders()
    {
        $this->redirectToController(ApiOrdersController::class);
    }
    public function discountSchemes()
    {
        $this->redirectToController(ApiDiscountSchemesController::class);
    }

    public function products()
    {
        $this->redirectToController(ApiProductsController::class);
    }

    public function categories()
    {
        $this->redirectToController(ApiCategoriesController::class);
    }

    public function places()
    {
        $this->redirectToController(ApiPlacesController::class);
    }

    public function offer()
    {
        $this->redirectToController(ApiOfferController::class);
    }

    public function reviews()
    {
        $this->redirectToController(ApiReviewsController::class);
    }

    protected function authenticateAdmin(): bool
    {
        if (!App::isAdmin())
            $this->error(500, 'Access denied');

        return true;
    }

    protected function response($code, $message, $attrs = [])
    {
        header('Content-Type: application/json');
        echo json_encode(array_merge([
            'code'=>$code,
            'message'=>$message
        ],$attrs));
    }

    protected function error($code, $message, $attrs = [])
    {
        $this->response($code, $message, $attrs);
        die();
    }

    protected function success($message, $attrs = [])
    {
        $this->response(200, $message, $attrs);
        die();
    }

    /*protected function dismount($object)
    {
        if (is_array($object)) {
            $array = array_map(function($value){
                return $this->dismount($value);
            }, $object);
        }elseif (!is_object($object)){
         return $object;
        }
        else {
            $reflectionClass = new ReflectionClass(get_class($object));
            $array = array();
            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($object);
                if (is_object($value) && !($value instanceof DateTime)) {
                    $array[$property->getName()] = $this->dismount($value);
                } else {
                    $array[$property->getName()] = $value;
                }
                $property->setAccessible(false);
            }
        }
        return $array;
    }*/
    protected function dismount($object)
    {
        if (is_array($object)) {
            // Перевірка для кожного елемента масиву
            return array_map(function($value) {
                return $this->dismount($value);  // Рекурсивно обробляємо елементи масиву
            }, $object);
        } elseif (!is_object($object)) {
            return $object;  // Якщо це не об'єкт, просто повертаємо значення
        } else {
            $reflectionClass = new ReflectionClass(get_class($object));
            $array = array();

            // Перебираємо всі властивості об'єкта
            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);  // Доступ до приватних властивостей
                $value = $property->getValue($object);

                // Якщо значення властивості є об'єктом і не є DateTime, рекурсивно обробляємо
                if (is_object($value) && !($value instanceof DateTime)) {
                    $array[$property->getName()] = $this->dismount($value);  // Рекурсія для об'єктів
                } elseif (is_array($value)) {
                    // Якщо значення є масивом, обробляємо кожен елемент масиву рекурсивно
                    $array[$property->getName()] = $this->dismount($value);
                } else {
                    $array[$property->getName()] = $value;  // Якщо це просте значення, просто додаємо
                }
                $property->setAccessible(false);  // Відновлюємо доступність властивості
            }
            return $array;
        }
    }

    protected function redirectToController($controller)
    {
        if (empty(App::route()->params())){
            $this->error(422, 'Invalid request');
            die();
        }

        $method = App::route()->params()[0] ?? 'index';

        if (!is_file(base_path("$controller.php"))){
            $this->error(500, 'Check your query! File NOT FOUND');
        }

        $c = new $controller();

        if (isset($method) && !method_exists($c, $method)){
            $this->error(500, 'Check your query! Method NOT FOUND');
        }

        call_user_func(array($c, $method));
    }
}