let table = null;

function createButtons(id, acl){
    let className = 'success';
    let btnClassName = 'unban';
    let icon = '<i class="fa-solid fa-user-check"></i>';
    let text = 'Розблокувати'
    if (acl>0){
        className = 'secondary'
        btnClassName = 'ban'
        icon = '<i class="fa-solid fa-user-xmark"></i>'
        text = 'Заблокувати'
    }

    /*return `
        <div class="btn-group">
                <button type="button" class="btn btn-outline-dark edit-btn btn-sm" value="${id}"><i class="fa-solid fa-gear"></i></button>
                <button type="button" class="btn btn-outline-dark droormwn-toggle droormwn-toggle-split" data-bs-toggle="droormwn" aria-expanded="false">
                <span class="visually-hidden">Toggle Droormwn</span>
                </button>
                <ul class="droormwn-menu" style="padding: 5px">
                    <li>
                        <div class="d-flex flex-column">
                            <button type="button" class="my-1 btn btn-outline-${className} ${btnClassName}-btn btn-sm" value="${id}">${icon} ${text}</button>
                             
                        </div>
                    </li>
                    <li>
                        <button type="button" class="my-1 btn btn-outline-danger w-100 remove-btn btn-sm" value="${id}"><i class="fa-solid fa-user-minus"></i> Видалити</button>
                    </li>
                </ul>
            </div>
    `*/

    return `<button type="button" class="btn btn-outline-dark edit-btn btn-sm" value="${id}"><i class="fa-solid fa-gear"></i></button><button type="button" class="btn btn-outline-${className} ${btnClassName}-btn mx-1 btn-sm" value="${id}">${icon}</button><button type="button" class="btn btn-outline-danger remove-btn btn-sm" value="${id}"><i class="fa-solid fa-user-minus"></i></button>`;

}

$('#media').change(function (event) {
    let files = $(this)[0].files;

    let reader = new FileReader();
    reader.onload = function(e) {
        $('#avatar')[0].src = e.target.result;
    };
    reader.readAsDataURL(files[0]);
})

function updateRow(id, data){
    table.row(`#row_${id}`).data([
        data.email,
        data.lastname ? data.lastname : data.surname,
        data.name,
        data.middlename ? data.middlename : data.patronymic,
        data.phone,
        data.role,
        createButtons(id,data.role === 'admin' || data.role === 'manager' ? 100 : data.role === 'ban' ? -1 : 10)
    ])
}

$(document).ready(function() {
    table = $("#users-table").DataTable({
        "paging": true,
        "lengthMenu": [2, 10, 25, 50],
        "pageLength": 10,
        "serverSide":true,
        "processing":true,
        "createdRow": function (row, data, rowIndex) {
            row.id = 'row_' + data[6];
            row.cells[6].innerHTML = createButtons(data[6], data[7]);
            for (const cell of row.cells) {
                if (cell === row.cells[4]) continue;
                cell.style.maxWidth = '100px';
                cell.style.overflow = 'hidden';
                cell.style.textOverflow = 'ellipsis';
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": -1, "className": "text-center" }
        ],
        "ajax":{
            "url": "/api/users/getUsersPagination",
            "type": "post",
        },
        "language": {
            "url": 'public/datatables/translation.json'
        },
        "layout":{
            "top": 'search',
            "topStart": 'pageLength',
            "topEnd": 'buttons'
        },
        "buttons": [
            {
                text: '<i class="fa-solid fa-plus"></i>',
                className: 'btn-sm',
                action: function () {
                    $('#user-mode').val('add')
                    $('#modal-title').text('Додавання користувача')
                    $('#modal-action-submit').text('Додати')
                    $('#password').attr('required', 'required')
                    $('#password-confirm').attr('required', 'required')
                    $('#avatar')[0].src = '/public/image/user.png';
                    $('#user-form')[0].reset()
                    $('#user-modal').modal('show')
                }
            },
        ]
    });
});


$(document).on('submit', '#user-form', function (e) {
    e.preventDefault();

    $('#alert-error').empty().addClass('d-none')

    let formData = new FormData(this)

    let mode = $('#user-mode').val()
    let url = '/api/users'
    switch (mode){
        case 'add': url = `${url}/addUser`; break;
        case 'update': url = `${url}/updateUser`; break;
        case 'delete': url = `${url}/deleteUser`; break;
        default: url = `${url}/addUser`; break;
    }

    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (response){
            if (response.code !== 200){
                $('#alert-error').removeClass('d-none');
                for (const error in response.errors) {
                    $('#alert-error').append(`<li>${response.errors[error]}</li>`);
                }
            }
            if (response.code === 200){
                $('#alert-error').addClass('d-none')
                $('#user-modal').modal('hide')
                $('#user-form')[0].reset()
                if (mode === 'add')
                    table.ajax.reload();
                else
                    updateRow(response.user.id, response.user)

                alertify.success('Успішно')
            }
        }
    })
})

$(document).on('click', '.edit-btn', function (){
    let id = $(this).val()
    let formData = new FormData();
    formData.append('id', id)
    $.ajax({
        type: "POST",
        url: "/api/users/getUser",
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (response){
            $('#user-form')[0].reset()
            $('#user-mode').val('update')
            $('#modal-title').text('Редагування користувача')
            $('#modal-action-submit').text('Зберегти')
            $('#password').removeAttr('required')
            $('#password-confirm').removeAttr('required')
            $('#user-id').val(response.user.id)
            $('#email').val(response.user.email)
            $('#role').val(response.user.role)
            $('#name').val(response.user.name)
            $('#surname').val(response.user.surname)
            $('#middlename').val(response.user.patronymic)
            $('#phone').val(response.user.phone)
            $('#avatar')[0].src =
                response.user.avatar != null
                    ? response.user.avatar
                    : "/public/image/user.png";
            $('#user-modal').modal('show')
        }
    })
})

$(document).on('click', '.remove-btn', function (){
    let id = $(this).val()
    let formData = new FormData()
    formData.append('id', id)

    alertify
        .confirm(
            'Ви впевнені, що бажаєте видалити користувача та всі його дані?',
            function(){
                $.ajax({
                    type: "POST",
                    url: "/api/users/deleteUser",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response){
                        if (response.code !== 200){
                            alertify.error('Помилка видалення')
                        }
                        if (response.code === 200){
                            alertify.success('Користувач видалений')
                            $(`#row_${response.user_id}`).remove()
                        }
                    }
                })
            },
            function(){
                alertify.notify('Скасовано', 'custom', 2);
            })
})

$(document).on('click', '.ban-btn', function (){
    let id = $(this).val()
    let formData = new FormData()
    formData.append('id', id)

    alertify
        .confirm(
            'Ви впевнені, що бажаєте заблокувати користувача?',
            function(){
                $.ajax({
                    type: "POST",
                    url: "/api/users/banUser",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response){
                        if (response.code !== 200){
                            alertify.error('Помилка при блокуванні')
                        }
                        if (response.code === 200){
                            alertify.success('Користувач заблокований')
                            updateRow(response.user.id, response.user)
                        }
                    }
                })
            },
            function(){
                alertify.notify('Скасовано', 'custom', 2);
            })
})

$(document).on('click', '.unban-btn', function (){
    let id = $(this).val()
    let formData = new FormData()
    formData.append('id', id)

    alertify
        .confirm(
            'Ви впевнені, що бажаєте розблокувати користувача?',
            function(){
                $.ajax({
                    type: "POST",
                    url: "/api/users/unbanUser",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response){
                        if (response.code !== 200){
                            alertify.error('Помилка при розблокуванні')
                        }
                        if (response.code === 200){
                            alertify.success('Користувач розблокований')
                            updateRow(response.user.id, response.user)
                        }
                    }
                })
            },
            function(){
                alertify.notify('Скасовано', 'custom', 2);
            })
})