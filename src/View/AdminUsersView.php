<?php

namespace App\View;

use App\Exceptions\ApplicationException;
use App\Components\Menu;
use App\Model\Users;
use App\Model\Roles;
use App\View\AdminUsersView;
use App\Components\Pagination;

/**
* Класс View — шаблонизатор приложения, реализует интерфейс Renderable. Используется для подключения view страницы.
*/
class AdminUsersView extends AdminView
{
    /**
    * Метод выводит необходимый шаблон.
    */
    public function render()
    {
     /** метод должен выводить необходимый шаблон. Внутри метода данные свойства $data распаковать в переменные через extract(), а затем подключить страницу шаблона, получив полный путь к ней с помощью другого метода этого класса getIncludeTemplate().
    */

        $userId = '';
        $role = '';
        $errors = false;

        if (isset($_POST['submit'])) { // Измененине роли пользователя
            $userId = $_POST['userId'];
            $role = $_POST['role'];

            // Валидация полей
            if (!$userId) {
                $errors[] = 'Авторизуйтесь пожалуйста.';
            } elseif (!(is_numeric($userId) && is_numeric($role))) { // Индексы д.б.целыми числами.
                $errors[] = 'Некорректные данные. Обратитесь к администртору!';
            } else {
                Users::changeRole($userId, $role);
            }
        }

        $users = Users::all(); // Пользователи
        $roles = Roles::all(); // Роли пользователей

        if (isset($_POST['exit'])) { // Выход пользователя из сессии.
            Users::exit();
        }

        extract($this->data); // ['title' => 'Index Page'] -> $title = 'Index Page' - создается переменная для исп-я в html
        $menu = Menu::getAdminMenu();

        $templateFile = $this->getIncludeTemplate($this->view); // Полное имя файла

        if (file_exists($templateFile)) {
            include $templateFile; // Вывод представления
        } else { // Если файл не найден
            throw new ApplicationException("$templateFile - шаблон не найден", 442); // Если запрашиваемого файла с шаблоном не найдено, то метод должен выбросить исключение ApplicationException, с таким текстом ошибки: "<имя файла шаблона> шаблон не найден". 
        }
    }
}
