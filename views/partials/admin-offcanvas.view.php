<?php
use core\App;
?>
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" id="admin-offcanvas" aria-labelledby="admin-offcanvas">
    <div class="admin-offcanvas-header flex-column p-1 gap-1">
        <div class="d-flex align-items-center gap-1">
            <a class="navbar-brand flex-grow-1" href="/">
                <img src="<?= getStaticResource('image/logo.png') ?>" alt="" style="width: 24px; height: 24px; " class="d-inline-block align-text-center">
            </a>

            <h5 class="offcanvas-title">Адміністратор</h5>
            <button type="button" id="admin-offcanvas-close-btn" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    </div>
    <div class="offcanvas-body d-flex flex-column flex-grow-1 justify-content-between" id="admin-offcanvas-body" >
        <ul class="navbar-nav">
            <li class="nav-item p-1">
                <a class="nav-link p-1" aria-current="page" href="/">Головна</a>
            </li>
            <?php foreach (App::routes() as $route): ?>
                <?php if ($route->acl() >= 100 && $route->isNavVisible()):?>
                    <li class="nav-item <?= urlIs($route->url())? 'active' : '' ?> p-1">
                        <a class="nav-link p-1" aria-current="page" href="<?= $route->url() ?>"><?= $route->title() ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <div class="d-flex align-items-center m-1 bg-light p-1">
            <div class="d-flex flex-grow-1">
                <a href="/profile" class="d-flex align-items-center gap-1 mx-1">
                    <i class="fa-solid fa-user"></i>
                    <p style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;"><?= App::user()['email'] ?></p>
                </a>
            </div>
            <a class="btn btn-outline-danger" href="/profile/logout"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </div>
</div>
