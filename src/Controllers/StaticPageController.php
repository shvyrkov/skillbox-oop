<?php
namespace App\Controllers;

use App\View\View;
use App\Components\Menu;
use App\View\ArticleView;
use App\View\LoginView;
use App\View\RegistrationView;
use App\View\LkView;
use App\View\AdminView;
use App\View\PasswordView;

class StaticPageController
{
    public function about()
    {
        return new View('about', ['title' => Menu::showTitle(Menu::getUserMenu())]); // about.php - имя файла с Представлением (personal.messages.show -> __DIR__ . VIEW_DIR . 'personal/messages/show.php')
         // [...] - данные для Представления будут в виде $title = 'Главная'
    }

    public function contacts()
    {
        return new View('contacts', ['title' => Menu::showTitle(Menu::getUserMenu())]); // Вывод представления
    }

    /**
    * Вывод страницы авторизации пользователя
    *
    * @return View
    */
    public function login()
    {
        return new LoginView('login', ['title' => Menu::showTitle(Menu::getUserMenu())]); // Вывод представления
    }

    /**
    * Выход
    *
    * @return View
    */
    public function exit()
    {
        return new View('exit', ['title' => Menu::showTitle(Menu::getUserMenu())]); // Вывод представления
    }

    /**
    * Вывод страницы регистрации пользователя
    *
    * @return View
    */
    public function registration()
    {
        return new RegistrationView('registration', ['title' => Menu::showTitle(Menu::getUserMenu())]); // Вывод представления
    }

    /**
    * Вывод страницы с правилами сайта
    *
    * @return View
    */
    public function rules()
    {
        return new View('rules', ['title' => Menu::showTitle(Menu::getUserMenu())]); // Вывод представления
    }

    /**
    * Вывод страницы личного кабинета пользователя
    *
    * @return View
    */
    public function lk()
    {
        if (isset($_SESSION['user']['id'])) {

            return new LkView('lk', ['title' => Menu::showTitle(Menu::getUserMenu())]); // Вывод представления
        }
        else {
            header('Location: /login'); // @TODO: Выводить текст: вы не авторизованы...?
        }
    }

    /**
    * Вывод страницы личного кабинета пользователя
    *
    * @return View
    */
    public function password()
    {
        if (isset($_SESSION['user']['id'])) {

            return new PasswordView('password', ['title' => Menu::showTitle(Menu::getUserMenu())]); // Вывод представления
        } else {
            header('Location: /login'); // @TODO: Выводить текст: вы не авторизованы...?
        }
    }

    /**
    * Вывод страницы выбранной статьи
    *
    * @var string $id - данные строки запроса - id-статьи
    *
    * @return View
    */
    public function article($id)
    {
        return new ArticleView('article', ['id' => $id]);
    }

/**
* Метод принимает значения $params из строки запроса и выдает их обратно в виде строки опред-го вида...
*
*/
    public function test(...$params)
    {
        $string = "Test Page With : ";
        $i = 1;

        foreach ($params as $param) {
            $string .= ' param_' . $i++ . ' = ' . $param;
        }

        return $string;
    }

    public function index(...$params)
    {
        $params = [ // ???
            
               'title' => 'Главная', // Название пункта меню
               'path' => '/', // Ссылка на страницу, куда ведет этот пункт меню
               'class' => HomeController::class, // ?
               'method' => 'index', // ?
               'sort' => 0, // Индекс сортировки (?)
            
        ];

        return new View($params['path'], ['title' => 'Контакты', 
            'link_1' => '/', 'linkText_1' => 'На главную', 
            'link_2' => '/about', 'linkText_2' => 'О нас', 
            'link_3' => '/post', 'linkText_3' => 'Почта']); // Вывод представления
    }
}
