<?php
include 'layout/header.php';
?>

<div class="container">
    <!-- <h1>Вход</h1> -->

    <!-- <section> -->
        <!-- <div class="container"> -->
            <br>
            <div class="row">

                <div class="col-sm-8 col-sm-offset-4 padding-right">

                    <?php if (isset($errors) && is_array($errors)): ?>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li class="font-error"> <?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <div class="signup-form"><!--sign up form-->
                        <h2>Смена пароля </h2>
                        <?php
                        if (isset($success)) {
                          echo "<h4 class='font-success'>$success</h4>";
                        }
                        ?>
                        <form action="" method="post">
                            <div class="mb-3">
                              <label for="old_password" class="form-label">Старый пароль</label>
                              <input type="password" class="form-control
                              <?php
                              if($errors) {
                                echo "border-error";
                              }
                              ?>
                              " id="old_password" name="old_password" value="<?=$old_password ?>" >
                            </div>

                            <div class="mb-3">
                              <label for="new_password" class="form-label">Новый пароль</label>
                              <input type="password" class="form-control
                              <?php
                              if($errors) {
                                echo "border-error";
                              }
                              ?>
                              " id="new_password" name="new_password" value="<?=$new_password ?>" >
                            </div>

                            <div class="mb-3">
                              <label for="confirm_password" class="form-label">Ещё раз новый пароль</label>
                              <input type="password" class="form-control
                              <?php
                              if($errors) {
                                echo "border-error";
                              }
                              ?>
                              " id="confirm_password" name="confirm_password" value="<?=$confirm_password ?>" >
                            </div>

                            <input name="email" hidden type="text" value="<?=$_SESSION['user']['email'] ?>">

                            <div class="mb-3">
                              <button class="btn btn-outline-primary" type="submit" name="submit" id="button-addon1">Сменить пароль</button>
                            </div>
                        </form>
                    </div><!--/sign up form-->
                    <br/>
                    <br/>
                </div>
            </div>
        <!-- </div> -->
    <!-- </section> -->
</div>

<?php
include 'layout/footer.php';
