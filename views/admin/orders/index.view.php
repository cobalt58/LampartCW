<?php use core\Session;
use http\components\FloatingInputFactory;

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/admin-nav.view.php'); ?>
    <div class="content d-flex gap-2">
        <?php require base_path('views/partials/admin-offcanvas.view.php') ?>
        <?php require base_path('views/partials/datatables.php'); ?>

        <div class="content content-border p-3">
            <div>
                <table id="orders-table" class="table table-striped display nowrap" style="width: 100%">
                    <thead>
                    <tr>
                        <td>Замовник</td>
                        <td>Дата</td>
                        <td>Адреса</td>
                        <td>Загальна вартість</td>
                        <td>Статус</td>
                        <td class="text-center" style="width: 120px">Керування</td>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="order-modal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-title"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable"
        ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-title">Позиції в замовленні</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="order-form">
                <div class="modal-body">
                    <input type="hidden" id="order-id" name="id" readonly>
                    <input type="hidden" id="order-mode" name="mode" value="add" readonly>
                    <table id="details-table" class="table" style="width: 100%;">
                        <thead>
                        <tr>
                            <td>Назва</td>
                            <td>Кількість</td>
                            <td>Ціна за шт.</td>
                            <td>Ціна загальна</td>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script src="/public/js/api/orders.js"></script>
<?php require base_path('views/partials/admin-footer.view.php'); ?>