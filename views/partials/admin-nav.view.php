<?php
use core\App;
?>

<nav class="navbar navbar-light bg-light">
    <div class="container-fluid nav-logo">
        <a class="d-flex align-items-center gap-1" href="/">
            <img src="/public/image/logo.png" alt="" style="width: 24px; height: 24px;" class="mx-1 d-inline-block align-text-center">
            <h5 class="offcanvas-title flex-grow-1">Адміністратор</h5>
        </a>
        <script>
            function openOffCanvas(){
                console.log('click');
                console.log(document.getElementById('admin-offcanvas'));
                let offcanvas = document.getElementById('admin-offcanvas');
                if (offcanvas.style.transform.includes('-100%')){
                    offcanvas.style.transform = 'none';
                }else{
                    offcanvas.style.transform = 'translateX(-100%);'
                }
            }
        </script>
        <div class="admin-offcanvas-header align-items-center justify-content-end page flex-grow-1" style="padding-right: 10px;" id="current-page"><?= App::route()->title()?></div>
        <button class="navbar-toggler admin-offcanvas-btn" onclick="openOffCanvas()" type="button" data-bs-toggle="offcanvas" data-bs-target="#admin-offcanvas" aria-controls="admin-offcanvas">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

