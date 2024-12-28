<?php use core\Session;
use http\components\FloatingInputFactory;

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/nav.view.php'); ?>
    <div class="content d-flex align-items-center justify-content-center">
        <div class="card" style="border-radius: 15px; max-width: 500px; width: 400px">
            <div class="card-body p-4">
                <h2 class="text-uppercase text-center mb-5">Вхід</h2>

                <form action="/sign-in/auth" method="POST">
                    <?php FloatingInputFactory::email('email', 'Email', 'email', true,  Session::old('email')); ?>
                    <div class="mb-2"></div>
                    <?php FloatingInputFactory::password('password', 'Пароль', 'password', true, Session::old('password')); ?>
                    <div class="mb-2"></div>
                    <div style="list-style-type: circle;">
                        <?php  foreach (Session::get('errors') ?? [] as $error): ?>
                            <li class="text-danger">
                                <?= $error ?>
                            </li>
                        <?php endforeach;?>
                    </div>

                    <button type="submit" class="btn btn-outline-success w-100 mt-1">Увійти</button>

                    <p class="text-center text-muted mt-5 mb-0">Ще не маєте акаунта?
                        <a href="/sign-up" class="fw-bold text-body"><u>Зареєструватися</u></a>
                    </p>

                </form>

            </div>
        </div>
    </div>
<?php require base_path('views/partials/footer.view.php'); ?>