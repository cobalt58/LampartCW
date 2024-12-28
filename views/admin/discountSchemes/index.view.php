<?php use core\Session;
use http\components\ButtonFactory;
use http\components\FloatingInputFactory;
use models\property;

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/admin-nav.view.php'); ?>
    <div class="content d-flex gap-2">
        <?php require base_path('views/partials/admin-offcanvas.view.php');?>
        <?php require base_path('views/partials/datatables.php'); ?>

        <div class="content content-border p-3">
            <div>
                <table id="schemes-table" class="table table-striped display nowrap" style="width: 100%">
                    <thead>
                    <tr>
                        <td>Мінімальна сума витрати</td>
                        <td>Відсоток знижки</td>
                        <td class="text-center" style="width: 120px">Керування</td>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="property-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable"">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-title">Роль</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="property-form">
                <div class="modal-body">
                    <input type="hidden" id="property-id" name="id" readonly>
                    <input type="hidden" id="property-mode" name="mode" value="add" readonly>
                    <?php FloatingInputFactory::number('min_spent_amount', 'Мінімальна сума витрати', 'min_spent_amount', true, 0,  9999999999999, Session::old('name')); ?>
                    <div class="mb-2"></div>
                    <?php FloatingInputFactory::number('discount_percentage', 'Відсоток знижки', 'discount_percentage', true, 0, 100, Session::old('name')); ?>
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
    <script src="/public/js/api/discountSchemes.js"></script>
<?php require base_path('views/partials/admin-footer.view.php'); ?>