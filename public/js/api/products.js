let table = null;


function categoryTemplate(id, title){
    return `
        <span class="badge text-bg-primary" id="category-badge-${id}">
            <input type="hidden" value="${id}" readonly name="categories[]">
            ${title}
            <button data-category-id="${id}" data-category-title="${title}" class="btn btn-warning btn-sm btn-remove-category"><i class="fa-solid fa-trash"></i></button>
        </span>
    `;
}

function propertyTemplate(id, title, value = ''){
    return `
        <div class="w-100" id="property-div-${id}">
            <hr>
            <div class="w-100 d-flex flex-row gap-1">
                <div class="d-flex align-items-center">
                    <label for="property-">${title}</label>
                </div>
                <input type="text" class="form-control flex-grow-1" name="properties[${id}]" id="property-value-${id}" required min="3" max="50" value="${value}">
                <button data-property-id="${id}" style="width: 40px;" data-property-title="${title}" class="btn btn-warning btn-sm btn-remove-property"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
    `;
}

function createButtons(id){
    return `<button type="button" class="btn btn-outline-dark edit-btn btn-sm mx-1" value="${id}"><i class="fa-solid fa-gear"></i></button>
            <button type="button" class="btn btn-outline-danger remove-btn btn-sm" value="${id}"><i class="fa-solid fa-trash"></i></button>`;

}

function updateRow(id, data){
    table.row(`#row_${id}`).data([
        data.name,
        data.categories[0]['name'],
        data.price,
        data.quantity,
        createButtons(id)
    ])
}
function renderCategoriesSelect(categories, level = 0) {
    let html = "";
    for (const category of categories) {
        const padding = level > 0 ? "-".repeat(level) : ""; // Adjust padding as needed (10px per level)
        html += `<option value="${category.category.id}">${padding+category.category.title}</option>`;
        if (category.children.length > 0) {
            html += renderCategoriesSelect(category.children, level + 1);
        }
    }
    return html;
}
function updateSelect(select_id = "#category-select", nullFirst = true){
    $.getJSON(
        '/api/categories/getTree',
        function (json){
            const $select = $(select_id);
            if (nullFirst){
                $select.html("<option value=\"null\" selected></option>"+renderCategoriesSelect(json.tree));
            }else{
                $select.html(renderCategoriesSelect(json.tree));
            }

        }
    )
}

function updateProperties(select_id = "#property-select", nullFirst = true){
    $.getJSON(
        '/api/properties/getAll',
        function (json){
            const $select = $(select_id);
            $select.empty()
            if (nullFirst){
                $select.html("<option value=\"null\" selected></option>"+renderCategoriesSelect(json.tree));
            }else{
                json.properties.forEach(property => {
                    $select.append(`<option value="${property.id}">${property.name}</option>`);
                });
            }

        }
    )
}


$(document).on('click', '.btn-add-selected-category', function (){
    let category = $("#category-select")[0];
    let catDiv = $("#product-categories")[0];

    catDiv.innerHTML = catDiv.innerHTML + categoryTemplate(category.value,$('#category-select option:selected').text());
    $('#category-select option[value="'+category.value+'"]').remove();
})
$(document).on('click', '.btn-remove-category', function (){
    let id = $(this).attr('data-category-id');
    let title = $(this).attr('data-category-title');
    $("#category-select").append(`<option value="${id}">${title}</option>`);
    $(this).closest('span').remove();
})

$(document).on('click', '.btn-add-selected-property', function (){
    let property = $("#property-select")[0];
    let propertyDiv = $("#product-properties")[0];

    propertyDiv.innerHTML = propertyDiv.innerHTML + propertyTemplate(property.value,$('#property-select option:selected').text());
    $('#property-select option[value="'+property.value+'"]').remove();
})
$(document).on('click', '.btn-remove-property', function (){
    let id = $(this).attr('data-property-id');
    let title = $(this).attr('data-property-title');
    $("#property-select").append(`<option value="${id}">${title}</option>`);
    $(`#property-div-${id}`).remove();
})



$('#media').change(function (event) {
    // Получить список выбранных файлов
    let files = $(this)[0].files;

    // Очистить область превью
    if ($('#product-mode').val() === 'add')
        $("#images-previews").empty();
    else
        $(".img-preview").remove()

    // Обработать каждый файл
    for (let i = 0; i < files.length; i++) {
        // Создать объект FileReader
        let reader = new FileReader();

        // Обработчик события загрузки
        reader.onload = function(e) {
            // Создать элемент изображения
            let img = new Image();
            img.src = e.target.result;
            img.classList.add('img-preview');
            img.style.margin = "10px";

            // Добавить изображение в область превью
            $("#images-previews").append(img);
        };

        // Загрузить файл
        reader.readAsDataURL(files[i]);
    }
})

$(document).ready(function() {
    updateSelect('#category-select', false)
    updateProperties('#property-select', false)
    table = $("#products-table").DataTable({
        "paging": true,
        "lengthMenu": [10, 25, 50],
        "pageLength": 10,
        "serverSide": true,
        "createdRow": function (row, data, rowIndex) {
            row.id = 'row_' + data[4];
            row.cells[4].innerHTML = createButtons(data[4]);
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
            "url": "/api/products/getProductsPagination",
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
                    updateSelect('#category-select', false)
                    updateProperties('#property-select', false)
                    $("#product-categories").empty();
                    $("#product-properties").empty();
                    $('#product-mode').val('add')
                    $('#modal-title').text('Додавання продукту')
                    $('#modal-action-submit').text('Додати')
                    $('#product-form')[0].reset()
                    $('#images-previews').empty()
                    $('#product-modal').modal('show')
                }
            },
            'pdf'
        ]
    });
});

$(document).on('submit', '#product-form', function (e) {
    e.preventDefault();

    $('#alert-error').empty().addClass('d-none')

    let formData = new FormData(this)

    let mode = $('#product-mode').val()
    let url = '/api/products'
    switch (mode){
        case 'add': url = `${url}/addProduct`; break;
        case 'update': url = `${url}/updateProduct`; break;
        case 'delete': url = `${url}/deleteProduct`; break;
        default: url = `${url}/addProduct`; break;
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
                $('#product-modal').modal('hide')
                $('#product-form')[0].reset()
                if (mode === 'add')
                    table.ajax.reload();
                else
                    updateRow(response.product.id, response.product)

                alertify.success('Успішно')
            }
        }
    })
})

function editImageTemplate(name){
    return `
     <div class="image position-relative w-100 mb-2" id="${name}">
        <img src="/${name}" alt="image" style="max-height: 200px; max-width: 200px;">
        <button type="button" class="btn btn-outline-danger position-absolute top-0 z-1 btn-image-delete" style="right: 0;"><i class="fa fa-solid fa-trash"></i></button>
    </div>
    `;
}



$(document).on('click', '.edit-btn', function (){
    let id = $(this).val()
    let formData = new FormData();
    formData.append('id', id)
    $.ajax({
        type: "POST",
        url: "/api/products/getProduct",
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (response){
            $('#product-form')[0].reset()
            $('#product-mode').val('update')
            $('#modal-title').text('Редагування продукту')
            $('#modal-action-submit').text('Зберегти')
            $('#product-id').val(response.product.id)
            $('#name').val(response.product.name)
            $('#price').val(response.product.price)
            $('#quantity').val(response.product.quantity)
            $('#description').val(response.product.description)
            $('#product-modal').modal('show')
            $('#images-previews').empty();
            for (let image of response.product.images){
                $('#images-previews').append(editImageTemplate(image))
            }

            $("#product-categories").empty();
            $("#product-properties").empty();

            let categories = response.product.categories;
            let properties = response.product.properties;
            categories.forEach(category => {
                let catDiv = $("#product-categories")[0];
                catDiv.innerHTML = catDiv.innerHTML + categoryTemplate(category.category_id,category.name);
                $('#category-select option[value="'+category.category_id+'"]').remove();
            })

            properties.forEach(property => {
                let propertyDiv = $("#product-properties")[0];
                propertyDiv.innerHTML = propertyDiv.innerHTML + propertyTemplate(property.property_id,$(`#property-select option[value="${property.property_id}"]`).text(), property.property_value);
                $('#property-select option[value="'+property.property_id+'"]').remove();
            })

        }
    })
})

$(document).on('click', '.remove-btn', function (){
    let id = $(this).val()
    let formData = new FormData()
    formData.append('id', id)

    alertify
        .confirm(
            'Ви впевнені, що бажаєте видалити товар?',
            function(){
                $.ajax({
                    type: "POST",
                    url: "/api/products/deleteProduct",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response){
                        if (response.code !== 200){
                            alertify.error('Помилка видалення')
                        }
                        if (response.code === 200){
                            alertify.success('Товар видалено')
                            $(`#row_${response.product_id}`).remove()
                        }
                    }
                })
            },
            function(){
                alertify.notify('Скасовано', 'custom', 2);
            })
})

$(document).on('click', '.btn-image-delete', function (event) {
    let image = $(this).parent()[0].id
    let formData = new FormData()
    formData.append('image', image)
    alertify
        .confirm(
            'Ви впевнені, що бажаєте видалити зображення?',
            function(){
                $.ajax({
                    type: "POST",
                    url: "/api/products/deleteProductImage",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response){
                        if (response.code !== 200){
                            alertify.error('Помилка видалення')
                        }
                        if (response.code === 200){
                            alertify.success('Зображення видалено')
                            document.getElementById(image).remove()
                        }
                    }
                })
            },
            function(){
                alertify.notify('Скасовано', 'custom', 2);
            })
})
