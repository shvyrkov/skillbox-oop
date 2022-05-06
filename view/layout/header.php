<?php
use App\Model\Methods;
use App\Model\Users;

include 'base/header.php';
?>
    <title><?=$title ?></title>
  </head>
  <body>
    <!-- Меню пользователя -->
    <!-- @TODO: подобрать цвета шрифтов и фона, чтобы всё было видно -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
      <div class="container-fluid">
        <a hidden class="navbar-brand" href="<?=$menu['main']['path'] ?>"><?=$menu['main']['title'] ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <?php
          foreach ($menu as $item) {
            if ($item['path'] == '/lk' && !isset($_SESSION['user']['id'])) {
              continue; //Если не было регистрации или входа - л.к. в меню не отображать.
            }
          ?>
          <li class="nav-item ">
              <a class="nav-link <?php
                if ($title === $item['title']) {
                  echo "active";
                }
              ?> " href="<?=$item['path'] ?>" ><?=$item['title'] ?></a>
            </li>
          <?php
          }
          ?>
          </ul>
          <?php
          if(isset($_SESSION['user']['id'])) { // Если была регистрация или вход в л.к.-->
          ?>
          <form class="d-flex" name="exit" action="/exit" method="post">
            <!-- <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"> -->
            <!-- <button class="btn btn-outline-success" type="submit">Search</button> -->
            <button type = "submit" class="btn btn-outline-success my-2 my-sm-0" name="exit">Выход</button>
          </form>
          <?php
          } else { // Если входа или регистрации не было
          ?>
            <a href="/login" >
              <button class="btn btn-outline-success my-2 my-sm-0">Вход</button>
            </a>
            <a href="/registration">
              <button class="btn btn-outline-success my-2 my-sm-0">Регистрация</button>
            </a>
          <?php
          }
          ?>
        </div>
      </div>
    </nav>

<!-- Заголовок - название сайта. -->
    <div class="container-fluid px-5-auto title"><!-- @TODO: сделать ссылку на Главную-->
        <div class="SiteName" >Библиотека</br>Фасилитатора</div>
        <div class="SitePodpis">Методы, инструменты, технологии...</div><br>
    </div>

<!-- Меню типов методов -->
    <div class="container-fluid pl-3 pt-2 pb-2 method_menu">
      <form action="" method="post">
        <div class="row">
        <?php
        foreach (Methods::all() as $method) {  // Метод модели all получит все записи из связанной с моделью таблицы БД
        ?>
          <div class="col-6 col-sm-3 col-md">
            <a id="M1" href="/<?=$method->uri ?>"> 
              <div  class="Mbt" style="background-image: url('<?='/' . IMG . '/' .$method->image; ?>'); background-position: left top; background-repeat: no-repeat; cursor:pointer"><?=$method->name ?>
              <input type="text" hidden class="" id="method" name="method" value="<?=$method->id ?>">
              </div>
            </a>
          </div>
        <?php
        }
        ?>
        </div><!-- row -->
      </form>
    </div><!-- container -->
