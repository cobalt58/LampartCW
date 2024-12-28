<?php
use core\App;
?>
<nav class="navbar navbar-expand-<?= App::user()['role']!='user' ? 'xl' : 'md'?> navbar-light bg-light">
    <div class="container-fluid nav-logo">
        <a class="d-flex align-items-center" href="/">
            <img src="/public/image/logo.png" alt="" width="24" height="24" style="width: 24px; height: 24px;" class="mx-1 d-inline-block align-text-center">
            <?php if (App::user()['auth'] && App::user()['role']!='user'):?>
                <h5 class="offcanvas-title flex-grow-1 mx-1">Адміністратор</h5>
            <?php else:?>
                <h5 class="offcanvas-title flex-grow-1 mx-1">Centry</h5>
            <?php endif;?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach (App::routes() as $route): ?>
                    <?php if (App::user()['acl']>=$route->acl() && $route->acl() < 100 && $route->isNavVisible()):?>
                        <li class="nav-item <?= urlIs($route->url())? 'active' : '' ?> p-1">
                            <a class="nav-link p-1" aria-current="page" href="<?= $route->url() ?>"><?= $route->title() ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <div class="nav-user">
                <?php if(App::user()['auth']): ?>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="/public/image/user.png" alt="Logo" width="30" height="35" class="d-inline-block align-text-top">
                                <?= App::user()['email'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="/profile">Профіль</a>
                                </li>
                                <?php if (App::isAdmin()):?>
                                    <li>
                                        <a class="dropdown-item" href="/admin">Панель управління</a>
                                    </li>
                                <?php endif;?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/profile/logout">Вихід</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <a href="/sign-in" class="btn btn-outline-dark">Увійти <i class="fa-solid fa-right-to-bracket"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>