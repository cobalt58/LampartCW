<?php require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/nav.view.php'); ?>
<div class="flex-grow-1 d-flex gap-1 p-1">
    <?php require base_path('views/partials/filters-offcanvas.view.php'); ?>
    <div class="card card-body">
        <div class="create-pagination-search"></div>
        <div class="d-flex gap-1 justify-content-between">
            <button class="btn btn-outline-dark filters-offcanvas-btn" onclick="openOffCanvas()" type="button" data-bs-toggle="offcanvas" data-bs-target="#filters-offcanvas" aria-controls="filters-offcanvas">
                <i class="fa-solid fa-filter"></i>
            </button>

            <div class="create-items-per-page"></div>
        </div>
        <div class="products my-2"></div>
        <div class="create-pagination"></div>
    </div>
</div>
<script>
    function openOffCanvas(){
        console.log('click');
        console.log(document.getElementById('filters-offcanvas'));
        let offcanvas = document.getElementById('filters-offcanvas');
        if (offcanvas.style.transform.includes('-100%')){
            offcanvas.style.transform = 'none';
        }else{
            offcanvas.style.transform = 'translateX(-100%);'
        }
    }
</script>
<script type="module" src="/public/js/api/shop.js"></script>
<?php require base_path('views/partials/footer.view.php'); ?>
