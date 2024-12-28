<?php use core\App;
use models\orm\Properties;
use models\Product;
use models\ProductProperty;

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/nav.view.php'); ?>
<div class="content container my-3">
    <?php
    /* @var int $uid */
    /* @var int $pid */
    /* @var Product $product */

    ?>
    <input type="hidden" readonly name="uid" id="uid" value="<?= $uid ?>">
    <input type="hidden" readonly name="pid" id="pid" value="<?= $pid ?>">
    <div id="carouselExample" class="card card-body carousel carousel-dark slide">
        <div class="carousel-inner">
            <?php if(empty($product->images())):?>
                <div class="carousel-item active" style="background-color: transparent!important;" >
                    <div class="d-flex w-100 justify-content-center" style="height: 400px;">
                        <img src="/public/favicon.png" style="height: 400px; width: auto;" alt="...">
                    </div>
                </div>
            <?php else: ?>
            <?php $count = 0; foreach ($product->images() as $file): ?>
                <div class="carousel-item <?= $count==0?"active":""; ?>" style="background-color: transparent!important;" >
                    <div class="d-flex w-100 justify-content-center" style="height: 400px;">
                        <img src="/<?= ($file); ?>" style="height: 400px; width: auto;" alt="...">
                    </div>
                </div>
                <?php $count++; endforeach; ?>
            <?php endif; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <div class="card mt-2">
        <div class="card-body">
            <div class="title"><?= $product->name() ?></div>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">Категорії:
                <?= implode(', ', array_map(function ($category) {
                    return $category->name();
                }, $product->categories())) ?>
            </li>
            <li class="list-group-item d-flex flex-row justify-content-between">
                <div>
                    Ціна:
                    <?php if ($product->price() != $product->priceWithDiscount()): ?>
                        <span style="text-decoration: line-through; color: red;">
                    <?= $product->price() ?>
                </span>
                        <span style="color: green; font-weight: bold;">
                    <?= $product->priceWithDiscount() ?>
                </span>
                    <?php else: ?>
                        <?= $product->price() ?>
                    <?php endif; ?>
                </div>
                <form class="d-flex flex-row gap-1 add-to-cart-form" >
                    <input type="hidden" value="<?= $product->id(); ?>" name="product_id" readonly>
                    <input name="quantity" value="1" min="1" max="<?= $product->quantity(); ?>" type="number" style="max-width: 50px" class="form-control form-select-sm">
                    <button class="btn btn-sm btn-success btn-add-to-cart">До кошика</button>
                </form>
            </li>
        </ul>
        <div class="card-body">
            <p class="card-text"><?= $product->description() ?></p>
            <ul class="list-group list-group-flush">
                <?php
                /** @var Properties $properties */
                $properties = App::resolve(Properties::class);
                /** @var ProductProperty $property */
                foreach ($product->properties() as $property):?>
                    <li class="list-group-item"><?= $properties->get($property->propertyId())->name(); ?>:<?= $property->propertyValue(); ?></li>
                <?php endforeach;?>
            </ul>
            <a href="/shop" class="link-info card-link">Повернутися до каталогу</a>
        </div>
    </div>
</div>
<script type="module" src="/public/js/api/shop.js"></script>
<?php require base_path('views/partials/footer.view.php'); ?>
