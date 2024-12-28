<?php

use http\components\FloatingInputFactory;
use models\Cart;
use models\CartItem;

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/nav.view.php'); ?>
<?php require base_path('views/partials/datatables.php'); ?>
<div class="content">
    <?php
    /* @var Cart $cart */

    ?>
    <div class="container">
        <table id="cart-table" class="table" style="width: 100%;">
            <thead>
            <tr>
                <td>Назва</td>
                <td>Кількість</td>
                <td>Ціна за шт.</td>
                <td>Ціна загальна</td>
                <td>
                    Керування
                </td>
            </tr>
            </thead>
            <tbody>
            <?php /** @var CartItem $cartItem */
            if ($cart) foreach ($cart->cartItems() as $cartItem): ?>
                <tr>
                    <td><?= $cartItem->product()->name() ?></td>
                    <td><?= $cartItem->quantity() ?></td>
                    <td><?= $cartItem->product()->priceWithDiscount() ?></td>
                    <td><?= $cartItem->totalPrice()?></td>
                    <td>
                        <button class="btn btn-sm btn-danger cart-item-remove" data-cart-item-id="<?= $cartItem->id(); ?>"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <div>
            Загальна кількість позицій: <?= ($cart ? $cart->countItems() : 0); ?>
            <br>
            Загальна вартість: <?= ($cart ? $cart->totalPrice() : 0); ?>
            <br>
            Загальна вартість із накопичувальною знижкою: <?= ($cart ? $cart->finalePrice() : 0); ?>
        </div>

        <form class="card card-body mt-3" id="offer-form">
            <input type="hidden" name="user_id" value="<?= ($cart ? $cart->userId() : \core\App::user()['id']); ?>">
            <input type="hidden" name="cart_id" value="<?= ($cart ? $cart->id() : -1); ?>">
            <?php FloatingInputFactory::text('address', 'Адреса доставки','address', true);?>
            <div class="d-flex flex-row justify-content-end mt-2 gap-2">
                <button <?= (!$cart ? 'disabled' : '')?> type="button" id="btn-clear-cart" class="btn btn-warning btn-sm">Очистити кошик</button>
                <button <?= (!$cart ? 'disabled' : '')?> class="btn btn-success btn-sm">Оформити замовлення</button>
            </div>
        </form>

    </div>
</div>
</div>
<script src="/public/js/api/cart.js"></script>
<?php require base_path('views/partials/footer.view.php'); ?>
