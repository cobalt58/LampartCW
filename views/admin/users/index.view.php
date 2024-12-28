<?php use core\Session;
use http\components\FloatingInputFactory;

require base_path('views/partials/header.view.php'); ?>
<?php require base_path('views/partials/admin-nav.view.php'); ?>
    <?php require base_path('views/partials/datatables.php'); ?>
    <div class="content d-flex gap-2">
        <?php require base_path('views/partials/admin-offcanvas.view.php')?>
        <div class="content content-border p-3">

            <div>
                <table id="users-table" class="table table-striped display nowrap" style="width: 100%">
                    <thead>
                    <tr>
                        <td>Email</td>
                        <td>Прізвище</td>
                        <td>Ім'я</td>
                        <td>По бітькові</td>
                        <td>Телефон</td>
                        <td>Роль</td>
                        <td class="text-center" style="width: 160px">Керування</td>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="user-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable"">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modal-title">Користувач</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="user-form">
                <div class="modal-body">
                    <div class="row">
                    <div class="col-4">
                        <div class="mb-3 d-flex justify-content-center">
                            <img id="avatar" src="../../../public/image/user.png" width="200" alt="profile image">
                        </div>
                        <input type="file" accept="image/*" class="form-control media" id="media" name="media">
                    </div>
                    <div class="col-8">
                    <input type="hidden" id="user-id" name="id" readonly>
                    <input type="hidden" id="user-mode" name="mode" value="add" readonly>
                    <?php FloatingInputFactory::email('email', 'Email', 'email', true, Session::old('email')); ?>
                    <div class="mb-2"></div>
                    <?php FloatingInputFactory::text('surname', 'Прізвище', 'surname', true, Session::old('surname')); ?>
                    <div class="mb-2"></div>
                    <?php FloatingInputFactory::text('name', 'Ім\'я', 'name', true, Session::old('name')); ?>
                    <div class="mb-2"></div>
                    <?php FloatingInputFactory::text('middlename', 'По батькові', 'middlename', true, Session::old('middlename')); ?>
                    <div class="mb-2"></div>
                    <?php FloatingInputFactory::text('phone', 'Телефон', 'phone', true, Session::old('phone')); ?>
                    <div class="mb-2"></div>
                    <div class="form-floating">
                        <select name="role" class="form-select" id="role" aria-label="Floating label select example">
                            <option value="user">Користувач</option>
                            <option value="manager">Менеджер</option>
                            <option value="admin">Адміністратор</option>
                        </select>
                        <label for="floatingSelect">Роль користувача</label>
                    </div>
                    <div class="mb-2"></div>
                    <?php FloatingInputFactory::password('password', 'Пароль', 'password', true, Session::old('password')); ?>
                    <div class="mb-2"></div>
                    <?php FloatingInputFactory::password('password-confirm', 'Повторіть ваш пароль', 'password-confirm', true, Session::old('password-confirm')); ?>
                    <div class="mb-2"></div>
                    <div class="alert alert-warning d-none d-block m-0" id="alert-error" style="list-style-type: circle;">
                    </div>
                    </div>
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

    <script src="/public/js/api/users.js"></script>
<?php require base_path('views/partials/admin-footer.view.php'); ?>