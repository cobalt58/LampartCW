<?php

use core\Session;
use http\components\FloatingInputFactory;

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/admin-nav.view.php'); ?>
    <style>
        *{
            --bs-accordion-btn-padding-y: 0.7rem;
            --bs-accordion-btn-padding-x: 0.7rem;
            --bs-accordion-body-padding-y:0.5rem;
            --bs-accordion-body-padding-x:0.5rem;
        }
    </style>
    <div class="content d-flex gap-2">
        <?php require base_path('views/partials/admin-offcanvas.view.php')?>
        <div class="content content-border p-3">
            <form method="post" action="/api/categories/getTree" id="form-search" class="d-flex gap-1 mb-2 align-items-center">
                <label for="dt-search-0">Пошук:</label>
                <input type="search" class="form-control form-control-sm" name="search" id="search" placeholder="Пошук" aria-controls="users-table">
                <button type="button" class="btn btn-outline-secondary btn-sm add-btn" value="null"><i class="fa-solid fa-plus"></i></button>
            </form>
            <div class="accordion" id="accordionPanelsStayOpenExample">

            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="category-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable"">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-title">Категорія</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="category-form">
                <div class="modal-body">
                    <input type="hidden" id="category-id" name="id" readonly>
                    <input type="hidden" id="category-mode" name="mode" value="add" readonly>
                    <div class="form-floating" >
                        <select class="form-select" name="parent-category" id="category-select" aria-label="category select">
                        </select>
                        <label for="category-select">Батьківська категорія</label>
                    </div>
                    <div class="mb-2"></div>
                    <?php FloatingInputFactory::text('title', 'Назва', 'title', true, Session::old('title')); ?>
                    <div class="mb-2"></div>
                    <div class="alert alert-warning d-none d-block m-0" id="alert-error" style="list-style-type: circle;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" id="modal-action-submit" class="btn btn-primary">Додати</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script src="/public/js/api/categories.js"></script>
<?php require base_path('views/partials/admin-footer.view.php'); ?>