let table = null;

function createButtons(id){
    return `<button type="button" class="btn btn-outline-dark edit-btn btn-sm mx-1" value="${id}"><i class="fa-solid fa-gear"></i></button>
            <button type="button" class="btn btn-outline-danger remove-btn btn-sm" value="${id}"><i class="fa-solid fa-trash"></i></button>`;

}

function updateRow(id, data){
    table.row(`#row_${id}`).data([
        data.min_spent_amount,
        data.discount_percentage,
        createButtons(id)
    ])
}

$(document).ready(function() {
    table = $("#schemes-table").DataTable({
        "paging": true,
        "lengthMenu": [10, 25, 50],
        "pageLength": 10,
        "serverSide":true,
        "processing":true,
        "createdRow": function (row, data, rowIndex) {
            row.id = 'row_' + data[2];
            row.cells[2].innerHTML = createButtons(data[2]);
            for (const cell of row.cells) {
                cell.style.maxWidth = '150px';
                cell.style.overflow = 'hidden';
                cell.style.textOverflow = 'ellipsis';
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": -1, "className": "text-center" }
        ],
        "ajax":{
            "url": "/api/discountSchemes/getDiscountSchemesPagination",
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
                className: "btn-primary btn-sm",
                action: function () {
                    $('#property-mode').val('add')
                    $('#modal-title').text('Додавання схеми')
                    $('#modal-action-submit').text('Додати')
                    $('#property-form')[0].reset()
                    $('#property-modal').modal('show')
                }
            },
        ]
    });
});

$(document).on('submit', '#property-form', function (e) {
    e.preventDefault();

    $('#alert-error').empty().addClass('d-none')

    let formData = new FormData(this)

    let mode = $('#property-mode').val()
    let url = '/api/discountSchemes'
    switch (mode){
        case 'add': url = `${url}/addDiscountScheme`; break;
        case 'update': url = `${url}/updateDiscountScheme`; break;
        case 'delete': url = `${url}/deleteDiscountScheme`; break;
        default: url = `${url}/addDiscountScheme`; break;
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
                $('#property-modal').modal('hide')
                $('#property-form')[0].reset()
                if (mode === 'add')
                    table.ajax.reload();
                else
                    updateRow(response.scheme.id, response.scheme)

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
        url: "/api/discountSchemes/getDiscountScheme",
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (response){
            $('#property-form')[0].reset()
            $('#property-mode').val('update')
            $('#modal-title').text('Редагування характеристики')
            $('#modal-action-submit').text('Зберегти')
            $('#property-id').val(response.scheme.id)
            $('#min_spent_amount').val(response.scheme.min_spent_amount)
            $('#discount_percentage').val(response.scheme.discount_percentage)
            $('#property-modal').modal('show')
        }
    })
})

$(document).on('click', '.remove-btn', function (){
    let id = $(this).val()
    let formData = new FormData()
    formData.append('id', id)

    alertify
        .confirm(
            'Ви впевнені, що бажаєте видалити схему накопичувальної знижки?',
            function(){
                $.ajax({
                    type: "POST",
                    url: "/api/discountSchemes/deleteDiscountScheme",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response){
                        if (response.code !== 200){
                            alertify.error('Помилка видалення')
                        }
                        if (response.code === 200){
                            alertify.success('Схему накопичувальної знижки видалено')
                            $(`#row_${response.discount_scheme_id}`).remove()
                        }
                    }
                })
            },
            function(){
                alertify.notify('Скасовано', 'custom', 2);
            })
})
