<?php

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/admin-nav.view.php'); ?>
    <div class="content d-flex gap-2">
        <?php require base_path('views/partials/admin-offcanvas.view.php')?>
        <?php require base_path('views/partials/datatables.php'); ?>
        <div class="content content-border p-3">
            <table id="products-table" class="table table-responsive table-hover nowrap" style="width: 100%; max-width: calc(100% - 0px)">
                <thead>
                <tr>
                    <td>Назва</td>
                    <td>Категорія</td>
                    <td>Ціна</td>
                    <td>Кількість</td>
                    <td class="text-center" style="width: 120px">Керування</td>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="product-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl product-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modal-title">Додавання товару</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="product-form" method="post" enctype="multipart/form-data">
                <div class="modal-body" style="max-height: calc(100vh - 220px)">
                    <input type="hidden" id="product-id" name="id" readonly>
                    <input type="hidden" id="product-mode" name="mode" value="add" readonly>
                    <div class="row gutters-sm">
                        <div class="col-md-4 mb-1">
                            <div class="card ">
                                <div class="card-body d-flex flex-column gap-1">
                                    <div id="images-previews">

                                    </div>
                                    <input type="file" multiple class="form-control mt-2" name="media[]" id="media" accept=".png, .jpg, .jpeg">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 mb-1">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="alert alert-warning d-none mb-3 d-block m-0" id="alert-error" style="list-style-type: circle;">
                                    </div>

                                        <div class="col-sm-3">
                                            <h6 class="mb-0">Назва:</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">
                                            <input type="text" name="name" id="name" class="form-control w-100" value="">
                                        </div>

                                        <hr>

                                        <div class="col-sm-3">
                                            <h6 class="mb-0">Ціна:</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">
                                            <input type="number" name="price" id="price" class="form-control" step="0.5" value="">
                                        </div>
                                        <hr>

                                        <div class="col-sm-3">
                                            <h6 class="mb-0">Кількість:</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">
                                            <input type="number" name="quantity" id="quantity" class="form-control" step="1" value="">
                                        </div>
                                    <hr>

                                        <div class="col-sm-3">
                                            <h6 class="mb-0">Категорії:</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">
                                            <div class="d-flex flex-row gap-1 mb-2">
                                                <select class="form-select" id="category-select">
                                                </select>
                                                <button type="button" class="btn btn-success btn-add-selected-category" id="btnAddCategory"><i class="fa-solid fa-plus"></i></button>
                                            </div>
                                            <div class="d-flex flex-row gap-1 flex-wrap" id="product-categories">
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="col-sm-3">
                                            <h6 class="mb-0">Характеристики:</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">
                                            <div class="d-flex flex-row gap-1 mb-2">
                                                <select class="form-select" id="property-select">
                                                </select>
                                                <button type="button" class="btn btn-success btn-add-selected-property" id="btnAddProperty"><i class="fa-solid fa-plus"></i></button>
                                            </div>
                                            <div class="p-1 d-flex flex-column gap-1" id="product-properties">

                                            </div>
                                        </div>

                                    <hr>
                                        <div class="col-sm-3">
                                            <h6 class="mb-0">Опис:</h6>
                                        </div>
                                        <div class="col-sm-9 text-secondary">
                                            <textarea name="description" id="description" cols="30" rows="10" itemid="description" class="form-control" style="resize: none;"></textarea>
                                        </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-primary" id="modal-action-submit">Додати</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script type="module" src="/public/js/api/products.js"></script>
<?php require base_path('views/partials/admin-footer.view.php'); ?>