let table = null;

function createButtons(id){
    return `<button type="button" class="btn btn-outline-dark edit-btn btn-sm mx-1" value="${id}"><i class="fa-solid fa-gear"></i></button>
            <button type="button" class="btn btn-outline-danger remove-btn btn-sm" value="${id}"><i class="fa-solid fa-trash"></i></button>`;

}

function updateRow(id, data){
    table.row(`#row_${id}`).data([
        data.name,
        createButtons(id)
    ])
}

$(document).ready(function() {
    table = $("#properties-table").DataTable({
        "paging": true,
        "lengthMenu": [10, 25, 50],
        "pageLength": 10,
        "serverSide":true,
        "processing":true,
        "createdRow": function (row, data, rowIndex) {
            row.id = 'row_' + data[1];
            row.cells[1].innerHTML = createButtons(data[1]);
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
            "url": "/api/properties/getPropertiesPagination",
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
                    $('#modal-title').text('Додавання характеристики')
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
    let url = '/api/properties'
    switch (mode){
        case 'add': url = `${url}/addProperty`; break;
        case 'update': url = `${url}/updateProperty`; break;
        case 'delete': url = `${url}/deleteProperty`; break;
        default: url = `${url}/addProperty`; break;
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
                    updateRow(response.property.id, response.property)

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
        url: "/api/properties/getProperty",
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (response){
            $('#property-form')[0].reset()
            $('#property-mode').val('update')
            $('#modal-title').text('Редагування характеристики')
            $('#modal-action-submit').text('Зберегти')
            $('#property-id').val(response.property.id)
            $('#name').val(response.property.name)
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
            'Ви впевнені, що бажаєте видалити властивість?',
            function(){
                $.ajax({
                    type: "POST",
                    url: "/api/properties/deleteProperty",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response){
                        if (response.code !== 200){
                            alertify.error('Помилка видалення')
                        }
                        if (response.code === 200){
                            alertify.success('Властивість видалено')
                            $(`#row_${response.property_id}`).remove()
                        }
                    }
                })
            },
            function(){
                alertify.notify('Скасовано', 'custom', 2);
            })
})
