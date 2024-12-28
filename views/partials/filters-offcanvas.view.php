<?php
use core\App;
?>
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" id="filters-offcanvas" aria-labelledby="filters-offcanvas">
    <div class="offcanvas-header p-2">
        <h5 class="offcanvas-title">Фільтри</h5>
        <button type="button" id="filters-offcanvas-close-btn" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <hr class="m-0">
    <div class="offcanvas-body d-flex flex-column flex-grow-1 justify-content-between" id="filters-offcanvas-body" >
        <div class="p-2 text-start">
            <div class="mb-2">
                <button style="cursor: pointer" class="category-up btn btn-outline-dark btn-sm " value="null"><i class="fa-solid fa-arrow-up"></i>
                </button> Категорії <span class="current-category"></span>
            </div>
            <input type="hidden" readonly name="pid" id="pid" value="">
            <div class="categories text-decoration-underline" style="cursor: pointer"></div>
            <hr>
            <div>
                <div class="mb-2">Ціна</div>
                <div class="d-flex flex-column gap-1">
                    Від
                    <input class="form-control" type="number" id="price_from" step="0,00000000001" name="price_from" required="" >
                    До
                    <input class="form-control" type="number" id="price_to" step="0,00000000001" name="price_to" required="">
                </div>
            </div>
        </div>

    </div>
</div>
