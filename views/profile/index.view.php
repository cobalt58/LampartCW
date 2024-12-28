<?php use core\App;
use core\Session;
use http\components\FloatingInputFactory;
use models\Order;
use models\OrderItem;
use models\orm\Users;
use models\User;

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/nav.view.php'); ?>
<?php require base_path('views/partials/datatables.php'); ?>
<div class="content">
    <?php
    /* @var $user array */
    /* @var $orders array */

    ?>
    <div class="container">
        <div class="row gutters">
            <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12 my-3">
                <div class="card card-body">
                    <div class="account-settings">
                        <div class="user-profile text-center">
                            <div class="user-avatar mb-3 d-flex justify-content-center">
                                <img id="user-avatar" src="<?= (App::resolve(Users::class)->get(App::user()['id'])->avatar() ?? "/public/image/user.png") ?>" alt="profile image">
                            </div>
                            <h4 id="user-login" class="fs-3"><?= $user['login'] ?></h4>
                            <h5 id="user-email"><?= $user['email'] ?></h5>
                            <a class="btn btn-outline-danger my-1 w-100 d-flex justify-content-center align-items-center gap-1"
                               href="/profile/logout">
                                <h4>ВИХІД</h4>
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12 my-3">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-2 text-primary">Інформація про акаунт</h6>
                                    <button id="update-btn" value="<?= $user['id'] ?>"
                                            class="btn btn-sm btn-outline-dark"><i class="fa fa-solid fa-pencil"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 my-1">
                                <div class="row">
                                    <div class="col-3">
                                        <label for="email-preview">Email</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="email" class="form-control" id="email-preview"
                                               value="<?= $user['email'] ?>" placeholder="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 my-1">
                                <div class="row">
                                    <div class="col-3">
                                        <label for="login-preview">Телефон</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="login-preview" placeholder=""
                                               value="<?= $user['phone'] ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 my-1">
                                <div class="row">
                                    <div class="col-3">
                                        <label for="role-preview">Роль</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="role-preview" placeholder=""
                                               value="<?= $user['role'] ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($orders)):?>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-2 text-primary">Замовлення</h6>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 my-1">
                                <div class="accordion accordion-flush" id="accordionFlushExample">
                                    <?php /** @var Order $order */
                                    foreach ($orders as $order):?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $order->id(); ?>" aria-expanded="false" aria-controls="flush-collapseOne">
                                                Номер замовлення: <?= $order->id(); ?> | Дата замовлення: <?= $order->orderDate(); ?> | Статус: <?= $order->status(); ?>
                                            </button>
                                        </h2>
                                        <div id="flush-collapse<?= $order->id(); ?>" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <table id="orders-table" class="table" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <td>Назва</td>
                                                        <td>Кількість</td>
                                                        <td>Ціна за шт.</td>
                                                        <td>Ціна загальна</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php /** @var OrderItem $orderItem */
                                                    foreach ($order->orderItems() as $orderItem): ?>
                                                        <tr>
                                                            <td><?= $orderItem->product()->name() ?></td>
                                                            <td><?= $orderItem->quantity() ?></td>
                                                            <td><?= $orderItem->priceAtOrderTime() ?></td>
                                                            <td><?= $orderItem->quantity()*$orderItem->priceAtOrderTime()?></td>
                                                        </tr>
                                                    <?php endforeach;?>
                                                    </tbody>
                                                </table>
                                                <div>
                                                    Загальна кількість позицій: <?= ($order ? $order->countItems() : 0); ?>
                                                    <br>
                                                    Загальна вартість із накопичувальною знижкою: <?= ($order ? $order->totalOrderPrice() : 0); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="profile-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"
    ">
    <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="modal-title">Профіль</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="profile-form">
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">
                        <div class="user-avatar mb-3 d-flex justify-content-center">
                            <img id="modal-avatar" src="" width="200" alt="profile image">
                        </div>
                        <input type="file" accept="image/*" class="form-control media" id="media" name="media">
                    </div>
                    <div class="col-8">
                        <input type="hidden" id="user-id" name="id" readonly>
                        <input type="hidden" id="role" name="role" value="<?= App::user()['role']; ?>" readonly>
                        <?php FloatingInputFactory::email('email', 'Email', 'email', true, Session::old('email')); ?>
                        <div class="mb-2"></div>
                        <?php FloatingInputFactory::text('surname', 'Прізвище', 'surname', true, Session::old('surname')); ?>
                        <div class="mb-2"></div>
                        <?php FloatingInputFactory::text('name', 'Ім\'я', 'name', true, Session::old('name')); ?>
                        <div class="mb-2"></div>
                        <?php FloatingInputFactory::text('middlename', 'По батькові', 'middlename', true, Session::old('middlename')); ?>
                        <div class="mb-2"></div>
                        <?php FloatingInputFactory::text('phone', 'Телефон', 'phone', true, Session::old('phone')); ?>
                        <div class="mb-2"></div>
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true"
                                            aria-controls="collapseOne">
                                        Змінити пароль
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse p-2"
                                     data-bs-parent="#accordionExample">
                                    <?php FloatingInputFactory::password('password-old', 'Старий пароль', 'password-old', false, Session::old('password')); ?>
                                    <div class="mb-2"></div>
                                    <?php FloatingInputFactory::password('password', 'Новий пароль', 'password-new', false, Session::old('password')); ?>
                                    <div class="mb-2"></div>
                                    <?php FloatingInputFactory::password('password-confirm', 'Повторіть новий пароль', 'password-confirm', false, Session::old('password-confirm')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="alert mt-2 alert-warning d-none d-block m-0" id="alert-error"
                             style="list-style-type: circle;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="button" class="btn btn-danger">Видалити акаунт</button>
                <button type="submit" id="modal-action-submit" class="btn btn-primary">Зберегти</button>
            </div>
        </form>
    </div>
</div>
</div>
<script src="/public/js/api/profile.js"></script>
<?php require base_path('views/partials/footer.view.php'); ?>
