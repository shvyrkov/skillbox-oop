<?php
include 'layout/header.php';
?>

<div class="container">
    <h1><?=$title ?></h1>
</div>

<div class="container-fluid my-4 mx-auto">
    <?php
    use App\Model\Articles;

    // include 'layout/pagination.php';

    ?>
    <div class="row">
        <?php
        foreach ($articles as $article) {
        ?>
        <div class="col-12 col-sm-6 col-md-3">
            <a href="<?=ARTICLE . DIRECTORY_SEPARATOR . $article->id ?>">
                <div class="card border-0">
                    <img src="<?php echo DIRECTORY_SEPARATOR . IMG . DIRECTORY_SEPARATOR . $article->thumbnail; ?>" class="card-img-top" alt="/<?php echo DIRECTORY_SEPARATOR . IMG . DIRECTORY_SEPARATOR . $article->thumbnail ?>">
                    <div class="card-body ">
                        <div class="Markers">
                            <?php
                            foreach (Articles::getMethods($article->id) as $method) { ?>
                                <img  src="<?=DIRECTORY_SEPARATOR . IMG . DIRECTORY_SEPARATOR . $method->image; ?>">
                            <?php
                            }
                            ?>
                            <!-- <img id="MP1" src="img/mbt4.png" width="26" height="26"> -->
                        </div>
                        <h5 class="card-title MName"><?=$article->title ?></h5>
                        <h6 class="card-subtitle mb-2 VName"></h6>
                    </div>
                </div>
            </a>
            <div class="GBlock">
                <div class="People"><?=$article->people ?></div>
                <div class="Hours">><?=$article->duration ?></div>
            </div>
            <div class="IBlock"><?=$article->description ?></div>
        </div>
        <?php
        }
        ?>
    </div><!-- row -->
</div><!--Container-fluid-->
<?php
include 'layout/footer.php';
