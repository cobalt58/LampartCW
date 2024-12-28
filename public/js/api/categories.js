
function template(data){
    return `
        <div class="accordion-item" id="category-`+data.id+`">
            <h2 class="accordion-header d-flex ">
                <button class="accordion-button collapsed" id="category-title-`+data.id+`" type="button" data-bs-toggle="collapse" data-bs-target="#`+data.id+`" aria-expanded="true" aria-controls="#`+data.id+`">
                    ${data.title}
                </button>
                <div class="d-flex align-items-center justify-content-center p-1 gap-1">
                    `+ (data.id === -1 ? `
                        <button class="btn btn-outline-dark edit-btn btn-sm" value="`+data.id+`"><i class="fa-solid fa-gear"></i></button>
                    ` : `
                        <button class="btn btn-outline-secondary add-btn btn-sm" value="`+data.id+`"><i class="fa-solid fa-plus"></i></button>
                        <button class="btn btn-outline-dark edit-btn btn-sm" value="`+data.id+`"><i class="fa-solid fa-gear"></i></button>
                        <button class="btn btn-outline-danger remove-btn btn-sm" value="`+data.id+`"><i class="fa-solid fa-trash"></i></button>
                    `) +`
                </div>
            </h2>
            <div id="`+data.id+`" class="accordion-collapse collapse">
                <div class="accordion-body" id="body-`+data.id+`">
                </div>
            </div>
        </div>
    `
}
function renderCategories(categories, parentElement) {
    for (let item of categories) {
        let category = item.category
        let children = item.children
        parentElement.append(template(category))
        if (children.length > 0) {
            renderCategories(children, $(`#body-${category.id}`));
        }
    }
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

function update(){
    let formData = new FormData();
    formData.append('search', $("#search").val())
    $.ajax({
        type: "POST",
        url: "/api/categories/getTree",
        dataType: 'json',
        processData: false,
        contentType: false,
        data:formData,
        success: function (response){
            let div = $("#accordionPanelsStayOpenExample");
            div.empty()
            renderCategories(response.tree, div);
            const $select = $("#category-select");
            $select.html("<option value=\"null\" selected></option>"+renderCategoriesSelect(response.tree));

        }
    })
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


$(document).ready(function(){
    update()
})

$(document).on('click', '.add-btn', function (event){
    let id = $(this).val()
    $('#category-form')[0].reset();
    $('#category-mode').val('add')
    $('#category-select').val(id)
    $('#category-modal').modal('show');
})

$(document).on('click', '.edit-btn', function (event){
    let id = $(this).val()
    let formData = new FormData();
    formData.append('id', id)
    $.ajax({
        type: "POST",
        url: "/api/categories/getCategory",
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (response){
            $('#category-form')[0].reset();
            $('#category-mode').val('update')
            $('#category-id').val(id)
            $('#category-select').val(response.category.parent)
            $('#title').val(response.category.title)
            $('#modal-title').text('Редагування категорії')
            $('#modal-action-submit').text('Зберегти')
            $('#category-modal').modal('show');
        }
    })
})

function appendCategory(category) {
    let newCategory = $(template(category))
    let div = category.parent == null
        ? $(`#accordionPanelsStayOpenExample`)
        : $(`#body-${category.parent}`)

    div.append(newCategory)
}

function updateCategory(category) {
    $(`#category-title-${category.id}`).html(category.title)
}

$(document).on('submit', '#category-form', function (e) {
    e.preventDefault();

    $('#alert-error').empty().addClass('d-none')

    let formData = new FormData(this)

    let mode = $('#category-mode').val()
    let url = '/api/categories'
    switch (mode){
        case 'add': url = `${url}/addCategory`; break;
        case 'update': url = `${url}/updateCategory`; break;
        case 'delete': url = `${url}/deleteCategory`; break;
        default: url = `${url}/addCategory`; break;
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
                $('#category-modal').modal('hide')
                $('#category-form')[0].reset()
                updateSelect()
                if (mode === 'add')
                    appendCategory(response.category)
                else
                    updateCategory(response.category)

                alertify.success('Успішно')
            }
        }
    })
})

$(document).on('click', '.remove-btn', function (){
    let id = $(this).val()
    let formData = new FormData()
    formData.append('id', id)

    alertify
        .confirm(
            'Ви впевнені, що бажаєте видалити категорію?',
            function(){
                $.ajax({
                    type: "POST",
                    url: "/api/categories/deleteCategory",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response){
                        if (response.code !== 200){
                            alertify.error('Помилка видалення')
                        }
                        if (response.code === 200){
                            alertify.success('Категорія видалена')
                            update()
                        }
                    }
                })
            },
            function(){
                alertify.notify('Скасовано', 'custom', 2);
            })
})

let timer;
$("#search").keyup(function() {
    clearTimeout(timer);
    timer = setTimeout(function() {

        update()
    }, 500);
});

$("#search").on("input", function() {
    const value = $(this).val();
    if (value === "") {
        update();
    }
});