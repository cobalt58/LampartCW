let table = null;

function createButtons(id){
    return `<button type="button" class="btn btn-outline-dark edit-btn btn-sm mx-1" value="${id}"><i class="fa-solid fa-gear"></i></button>
            <button type="button" class="btn btn-outline-danger remove-btn btn-sm" value="${id}"><i class="fa-solid fa-trash"></i></button>`;

}

function updateProducts(select_id = "#product_id", nullFirst = false){
    $.getJSON(
        '/api/products/getAll',
        function (json){
            const $select = $(select_id);
            $select.empty()
            if (nullFirst){
                $select.html("<option value=\"null\" selected></option>");
            }
            json.products.forEach(product => {
                $select.append(`<option value="${product.id}">${product.name}</option>`);
            });
        }
    )
}

function updateRow(id, data){
    table.row(`#row_${id}`).data([
        data.product.name,
        data.discount_percentage,
        data.start_date.date.split(' ')[0],
        data.end_date.date.split(' ')[0],
        data.product.price,
        data.product.priceWithDiscount,
        createButtons(id)
    ])
}

$(document).ready(function() {
    updateProducts("#product_id")
    table = $("#promotions-table").DataTable({
        "paging": true,
        "lengthMenu": [10, 25, 50],
        "pageLength": 10,
        "serverSide":true,
        "processing":true,
        "createdRow": function (row, data, rowIndex) {
            row.id = 'row_' + data[6];
            row.cells[6].innerHTML = createButtons(data[6]);
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
            "url": "/api/promotions/getPromotionsPagination",
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
                    $('#promotion-mode').val('add')
                    $('#modal-title').text('Додавання знижки')
                    $('#modal-action-submit').text('Додати')
                    $('#promotion-form')[0].reset()
                    $('#promotion-modal').modal('show')
                }
            },
        ]
    });
});

$(document).on('submit', '#promotion-form', function (e) {
    e.preventDefault();

    $('#alert-error').empty().addClass('d-none')

    let formData = new FormData(this)

    let mode = $('#promotion-mode').val()
    let url = '/api/promotions'
    switch (mode){
        case 'add': url = `${url}/addPromotion`; break;
        case 'update': url = `${url}/updatePromotion`; break;
        case 'delete': url = `${url}/deletePromotion`; break;
        default: url = `${url}/addPromotion`; break;
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
                $('#promotion-modal').modal('hide')
                $('#promotion-form')[0].reset()
                if (mode === 'add')
                    table.ajax.reload();
                else
                    updateRow(response.promotion.id, response.promotion)

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
        url: "/api/promotions/getPromotion",
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (response){
            $('#promotion-form')[0].reset()
            $('#promotion-mode').val('update')
            $('#modal-title').text('Редагування характеристики')
            $('#modal-action-submit').text('Зберегти')
            $('#promotion-id').val(response.promotion.id)
            $('#product_id').val(response.promotion.product_id)
            $('#discount_percentage').val(response.promotion.discount_percentage)
            $('#start_date').val(response.promotion.start_date.date.split(' ')[0])
            $('#end_date').val(response.promotion.end_date.date.split(' ')[0])
            $('#promotion-modal').modal('show')
        }
    })
})

$(document).on('click', '.remove-btn', function (){
    let id = $(this).val()
    let formData = new FormData()
    formData.append('id', id)

    alertify
        .confirm(
            'Ви впевнені, що бажаєте видалити знижку?',
            function(){
                $.ajax({
                    type: "POST",
                    url: "/api/promotions/deletePromotion",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response){
                        if (response.code !== 200){
                            alertify.error('Помилка видалення')
                        }
                        if (response.code === 200){
                            alertify.success('Знижку видалено')
                            $(`#row_${response.promotion_id}`).remove()
                        }
                    }
                })
            },
            function(){
                alertify.notify('Скасовано', 'custom', 2);
            })
})
