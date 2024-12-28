<?php
use core\Session;
use http\components\FloatingInputFactory;

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/nav.view.php'); ?>
    <div class="content d-flex align-items-center justify-content-center">
        <div class="card" style="border-radius: 15px; max-width: 800px; width: 800px">
            <div class="card-body p-4">
                <h2 class="text-uppercase text-center mb-5">Реєстрація</h2>

                <form action="/sign-up/registration" method="POST">
                    <div class="row">
                        <div class="col-md-6 my-2">
                            <?php FloatingInputFactory::email('email', 'Email', 'email', true, Session::old('email')); ?>
                        </div>
                        <div class="col-md-6 my-2">
                            <?php FloatingInputFactory::text('surname', 'Прізвище', 'surname', true, Session::old('surname')); ?>
                        </div>
                        <div class="col-md-6 my-2">
                            <?php FloatingInputFactory::text('name', 'Ім\'я', 'name', true, Session::old('name')); ?>
                        </div>
                        <div class="col-md-6 my-2">
                            <?php FloatingInputFactory::text('middlename', 'По батькові', 'middlename', true, Session::old('middlename')); ?>
                        </div>
                        <div class="col-md-6 my-2">
                            <?php FloatingInputFactory::text('phone', 'Телефон', 'phone', true, Session::old('phone')); ?>
                        </div>
                        <div class="col-md-6 my-2">
                            <?php FloatingInputFactory::password('password', 'Пароль', 'password', true, Session::old('password')); ?>
                        </div>
                        <div class="col-md-6 my-2">
                            <?php FloatingInputFactory::password('password-confirm', 'Повторіть ваш пароль', 'password-confirm', true, Session::old('password-confirm')); ?>
                        </div>
                    </div>

                    <div style="list-style-type: circle;">
                        <?php  foreach (Session::get('errors') ?? [] as $error): ?>
                            <li class="text-danger">
                                <?= $error ?>
                            </li>
                        <?php endforeach;?>
                    </div>


                    <button type="submit" class="btn btn-outline-success w-100 mt-1">Зареєструватися</button>

                    <p class="text-center text-muted mt-5 mb-0">Вже маєте акаунт?
                        <a href="/sign-in" class="fw-bold text-body"><u>Увійти</u></a>
                    </p>

                </form>
            </div>
        </div>
    </div>
<?php require base_path('views/partials/footer.view.php'); ?>