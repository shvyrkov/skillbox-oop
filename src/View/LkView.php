<?php

namespace App\View;

use App\Exceptions\ApplicationException;
use App\Components\Menu;
use App\Model\Users;
use App\Model\Roles;
use \SplFileInfo;

/**
* Класс View — шаблонизатор приложения, реализует интерфейс Renderable. Используется для подключения view страницы.
*/
class LkView extends View
{
    /**
    * Метод выводит необходимый шаблон.
    */
    public function render()
    {
        $data = extract($this->data); // ['id' => 'id_article'] -> $id = 'id_article' - создается переменная для исп-я в html
        $menu = Menu::getUserMenu();
        $roles = Roles::all(); // Роли пользователей
        $templateFile = $this->getIncludeTemplate($this->view); // Полное имя файла

        $name = '';
        $email = '';
        $subscription = '';
        $aboutMe = '';
        $errors = false;

        if (isset($_POST['submit'])) { // Обработка формы ЛК
            $name = $_POST['name'];
            $email = $_POST['email'];
            $aboutMe = $_POST['aboutMe'];

            // Проверка ошибок ввода
            if (!Users::checkName($name)) {
                $errors['checkName'] = '- не должно быть короче 2-х символов';
            }

            if (!($name == $_SESSION['user']['name'])) { // Если имя было изменено
                if (Users::checkNameExists($name)) {
                    $errors['checkNameExists'] = '- такое имя уже используется';
                }
            }

            if (!Users::checkEmail($email)) { //  Проверка правильности ввода e-mail
                $errors['checkEmail'] = '- неправильный email';
            }

            if (!($email == $_SESSION['user']['email'])) { // Если email был изменен
                if (Users::checkEmailExists($email)) {
                    $errors['checkEmailExists'] = '- такой email уже используется';
                }
            }

            if ($_FILES['myfile']['name'] != '') { // Проверка на наличие файла для загрузки
                if (!empty($_FILES['myfile']['error'])) { // Проверяем наличие ошибок
                 $errors['file']['LoadingError'] = $_FILES['myfile']['error'];
                }
            // Проверить тип загружаемых файлов, это должны быть только картинки (jpeg, png, jpg).
                if (!in_array(mime_content_type($_FILES['myfile']['tmp_name']), ['image/jpeg', 'image/jpg', 'image/png'])) { 
                    $errors['file']['TypeError'] = 'Неправильный тип ' . mime_content_type($_FILES['myfile']['tmp_name']) . 'загружаемого файла ' . $_FILES['myfile']['name'];
                }
            // Проверить размер загружаемого файла (файл не должен превышать 2 Мб).
                if ($_FILES['myfile']['size'] > FILE_SIZE) {
                   $errors['file']['SizeError'] = 'Файл ' . $_FILES['myfile']['name'] . ' не может быть загружен на сервер, так как его размер составляет ' . Users::formatSize($_FILES['myfile']['size']) . ', что больше допустимых ' . Users::formatSize(FILE_SIZE);
                }

                if ($errors === false) { // Загружаем файл на сервер

                    if ((DEFAULT_AVATAR != $_SESSION['user']['avatar']) // Если это не заставка 
                        // && ($name != $_SESSION['user']['name']) // и было изменено имя пользователя,
                        && file_exists(AVATAR_STORAGE . $_SESSION['user']['avatar'])
                        ) {
                        unlink(AVATAR_STORAGE . $_SESSION['user']['avatar']); // то удаляем старый аватар на сервере
                    }

                    $myfile = new SplFileInfo($_FILES['myfile']['name']); // Загружаемое имя файла с расширением
                    $fileName = $name ? $name : $_SESSION['user']['name']; // Имя файла без расширения: новое, если было изменено, иначе - старое
                    $fileName = $fileName . '.' . $myfile->getExtension(); // Имя файла с расширением
                    // $fileName = $name . '.' . $myfile->getExtension(); // Имя файла с расширением
                    $fileMoved = move_uploaded_file($_FILES['myfile']['tmp_name'], AVATAR_STORAGE . $fileName); // Загрузка файла на сервер
echo "<br>";
var_dump(AVATAR_STORAGE . $fileName);
echo "<br>";
var_dump($fileMoved);
                    if (!$fileMoved) {
                        $errors['file']['LoadServerError'] = 'Файл ' . $fileName . ' не был загружен на сервер';
                    }
                }
            }

            if ($errors === false) { // Если ошибок нет, то обновляем данные пользователя.
                // if (($name != $_SESSION['user']['name']) && ($_FILES['myfile']['name'] != '')) { // Имя было изменено, но не было загрузки файла - надо менять имя существующего файла в БД и на сервере - @TODO
                //     $myfile = new SplFileInfo($_SESSION['user']['avatar']);
                //     $fileName = $name . '.' . $myfile->getExtension();
                // }

                if (Users::updateUser($_SESSION['user']['id'], $name, $email, $aboutMe, ($_FILES['myfile']['name'] != '') ? $fileName : $_SESSION['user']['avatar'])) { // Если обновление прошло нормально

                // Получаем обновленные данные пользователя
                    $user = Users::getUserById($_SESSION['user']['id']);

                    if ($user === false) {
                        // Если данные не получены - показываем ошибку
                        $errors[] = 'Ошибка получения данных.';
                    } else {
                        // Если данные правильные, запоминаем пользователя в сессии
                        Users::auth($user);

                        // Перегружаем кабинет с новыми данными
                        header('Location: /lk');

                        return new View('lk', ['title' => Menu::showTitle(Menu::getUserMenu())]); // Вывод представления
                    }
                 } else {
                    $errors[] = 'Ошибка обновления данных.';
                 }
            }
        }

        if (isset($_POST['subscription'])) { // Подписка/Отписка на рассылку

            $subscription = (int) $_POST['subscription'] ?? 0;

            if (!(in_array($subscription, [0, 1]))) {
                $errors[] = 'Некорректные данные. Обратитесь к администртору!';
            }

            if ($errors === false) { // Если ошибок нет, то обновляем данные пользователя.

                if (Users::changeSubscription($_SESSION['user']['id'], $subscription)) { // Если обновление прошло нормально

                // Получаем обновленные данные пользователя
                    $user = Users::getUserById($_SESSION['user']['id']);

                    if ($user === false) {
                        // Если данные не получены - показываем ошибку
                        $errors[] = 'Ошибка получения данных.';
                    } else {
                        // Если данные правильные, запоминаем пользователя в сессии
                        Users::auth($user);

                        // Перегружаем кабинет с новыми данными
                        header('Location: /lk');

                        return new View('lk', ['title' => Menu::showTitle(Menu::getUserMenu())]); // Вывод представления
                    }
                 } else {
                    $errors[] = 'Ошибка обновления данных.';
                 }
            }
        }

        if (file_exists($templateFile)) {
            include $templateFile; // Вывод представления
        } else { // Если файл не найден
            throw new ApplicationException("$templateFile - шаблон не найден", 443); // Если запрашиваемого файла с шаблоном не найдено, то метод должен выбросить исключение ApplicationException, с таким текстом ошибки: "<имя файла шаблона> шаблон не найден". 
        }
    }
}
