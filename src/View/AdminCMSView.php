<?php

namespace App\View;

use App\Exceptions\ApplicationException;
use App\Components\Menu;
use App\Components\Helper;
use App\Components\SimpleImage;
use App\Model\Users;
use App\Model\Articles;
use App\Model\ArticleMethods;
use App\View\AdminCMSView;
use \SplFileInfo;
use \Imagick;

/**
* Класс View — шаблонизатор приложения, реализует интерфейс Renderable. Используется для подключения view страницы.
*/
class AdminCMSView extends AdminView
{
    /**
    * Метод выводит необходимый шаблон.
    */
    public function render()
    {
     /** метод должен выводить необходимый шаблон. Внутри метода данные свойства $data распаковать в переменные через extract(), а затем подключить страницу шаблона, получив полный путь к ней с помощью другого метода этого класса getIncludeTemplate().
    */
        $articleTitle = '';
        $subtitle = '';
        $people = '';
        $duration = '';
        $description = '';
        $author = '';
        $link = '';
        $method = '';
        $content = '';
        $image = '';
        $thumbnail = '';
        // $userId = $_SESSION['user']['id']; // @TODO: добавлять id создавшего статью? Новое поле в Articles?

        $errors = false;
        $success = false;

        if (isset($_POST['submit'])) { // Обработка формы
            $articleTitle = $_POST['articleTitle'] ?? '';
            $subtitle = $_POST['subtitle'] ?? '';
            $people = $_POST['people'] ?? '';
            $duration = $_POST['duration'] ?? '';
            $description = $_POST['description'] ?? '';
            $author = $_POST['author'] ?? '';
            $link = $_POST['link'] ?? '';
            $methods = $_POST['methods'] ?? [];
            $content = $_POST['content'] ?? '';

            // Валидация данных ввода
            if (!Helper::checkLength($articleTitle, MIN_TITLE_LENGTH, MAX_TITLE_LENGTH)) {
                $errors['articleTitle'] = 'Название статьи не должно быть меньше ' . MIN_TITLE_LENGTH . ' и не больше ' . MAX_TITLE_LENGTH . ' символов';
            }

            if ($subtitle && !Helper::checkLength($subtitle, 0, MAX_SUBTITLE_LENGTH)) {
                $errors['subtitle'] = 'Название подзаголовка не должно быть больше ' . MAX_SUBTITLE_LENGTH . ' символов';
            }

            if ($people && !Helper::checkLength($people, MIN_PEOPLE_LENGTH, MAX_PEOPLE_LENGTH)) {
                $errors['people'] = 'Количество символов в поле не должно быть больше ' . MAX_PEOPLE_LENGTH;
            }

            if ($duration && !Helper::checkLength($duration, MIN_PEOPLE_LENGTH, MAX_DURATION_LENGTH)) {
                $errors['duration'] = 'Количество символов в поле не должно быть больше ' . MAX_DURATION_LENGTH;
            }

            if ($description && !Helper::checkLength($description, MIN_TITLE_LENGTH, MAX_DESCRIPTION_LENGTH)) {
                $errors['description'] = 'Должно быть не меньше ' . MIN_TITLE_LENGTH . ' и не больше ' . MAX_DESCRIPTION_LENGTH . ' символов';
            }

            if ($author && !Helper::checkLength($author, MIN_TITLE_LENGTH, MAX_AUTHOR_LENGTH)) {
                $errors['author'] = 'Должно быть не меньше ' . MIN_TITLE_LENGTH . ' и не больше ' . MAX_AUTHOR_LENGTH . ' символов';
            }

            if ($link && !Helper::checkLength($link, 0, MAX_LINK_LENGTH)) {
                $errors['link'] = 'Количество символов в поле не должно быть больше ' . MAX_LINK_LENGTH;
            }

            if ($content && !Helper::checkLength($content, 0, MAX_CONTENT_LENGTH)) {
                $errors['content'] = 'Количество символов в поле не должно быть больше ' . MAX_CONTENT_LENGTH;
            }

            // if (!is_numeric($method)) {
            //     $errors['method'] = 'Ошибка ввода. Обратитесь к Администратору.';
            // }
// echo "<br>";
// var_dump($methods);

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

                    $myfile = new SplFileInfo($_FILES['myfile']['name']); // Загружаемое имя файла с расширением
                    $image = $myfile->getFilename();
                    $fileMoved = move_uploaded_file($_FILES['myfile']['tmp_name'], IMG_STORAGE . $image); // Загрузка файла на сервер
                    // $fileMoved = move_uploaded_file($_FILES['myfile']['tmp_name'], IMG_STORAGE . $image); // Загрузка файла на сервер

                    $thumbnail = 'small-' . $image;

                    $thumbnailObj = new SimpleImage();
                    $thumbnailObj->load(IMG_STORAGE . $image);
                    $thumbnailObj->resize(490, 280); // Изменение размера изображения для Главной.
                    $thumbnailObj->save(IMG_STORAGE . $thumbnail);

                    $imageObj = new SimpleImage();
                    $imageObj->load(IMG_STORAGE . $image);
                    $imageObj->resize(1100, 620); // Изменение размера изображения для страницы статьи.
                    $imageObj->save(IMG_STORAGE . $image);

                    if (!$fileMoved) {
                        $errors['file']['LoadServerError'] = 'Файл ' . $image . ' не был загружен на сервер';
                    }
                }
            }

            if ($errors === false) { // Если ошибок нет, то добавляем данные.
                $article = new Articles();

                $article->title = $articleTitle;
                $article->people = $people;
                $article->duration = $duration;
                $article->description = $description;
                $article->author = $author;
                $article->subtitle = $subtitle;
                $article->link = $link;
                $article->content = $content;
                $article->image = $image ? $image : DEFAULT_ARTICLE_IMAGE;
                $article->thumbnail = $thumbnail ? $thumbnail : DEFAULT_ARTICLE_IMAGE;

                $article->save();

                if ($article->id) { // Добавление новых связей статья-метод
                    foreach ($methods as $method) {
                        ArticleMethods::upsert(
                            ['id_article' => $article->id,
                            'id_method' => $method],
                            [],
                            []);
                    }

                    $success = 'Статья успешно добавлена/изменена!';
                }
            }
        } // Обработка формы

        extract($this->data); // ['title' => 'Index Page'] -> $title = 'Index Page' - создается переменная для исп-я в html
        $menu = Menu::getAdminMenu();

        $templateFile = $this->getIncludeTemplate($this->view); // Полное имя файла

        if (isset($_POST['exit'])) {
            Users::exit();
        }

        if (file_exists($templateFile)) {
            include $templateFile; // Вывод представления
        } else { // Если файл не найден
            throw new ApplicationException("$templateFile - шаблон не найден", 442); // Если запрашиваемого файла с шаблоном не найдено, то метод должен выбросить исключение ApplicationException, с таким текстом ошибки: "<имя файла шаблона> шаблон не найден". 
        }
    }
}
