import {isoToLocalDate, redirect} from './functions.js'
import {Pagination} from "./pagination.js";

let pagination;
let pid = null;
let category_id = null;
$(document).ready(function (event){
    initCategories(pid)

    pagination = $('.products').pagination({
        lengthMenu: [3,5,8,10],
        pageLength: 3,
        ajax:{
            url: '/api/products/getProductsNeoPagination',
            method: 'post'
        },
        search: {
            price_from: () => $('#price_from').val(),
            price_to: ()=> $('#price_to').val(),
            category: category_id
        },
        emptyText: "Покищо продуктів не має :(.",
        templateFunc: productTemplate
    })

    initMinMaxDate()
    initOnChange()

    $(document).on('submit', '.add-to-cart-form', function (e){
        e.preventDefault();

        let formData = new FormData(this)

        $.ajax({
            type: "POST",
            url: '/api/carts/appendCart',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: formData,
            success: function (response){
                if (response.code !== 200){
                    alertify.alert('Виникла помилка! Спробуйте пізніше та перевірте чи увійшли до акаунту?')
                }
                if (response.code === 200){
                    alertify.success('Додано до кошика')
                }
            }
        })
    })
})



function initOnChange(){
    $(document).on('change', '#price_from', function (){
        //pagination.search.date_from = $(this).val()
        pagination.updatePagination()
    })

    $(document).on('change', '#price_to', function (){
        //pagination.search.date_to = $(this).val()
        pagination.updatePagination()
    })

    $(document).on('click', '.filter-category', function (event){
        pid = $(this).data('id')
        initCategories(pid, ()=>{pagination.updatePagination()})
    })

    $(document).on('click', '.category-up', function (event){
        pid = $(this).data('id')
        category_id = $(this).val() === 'null' ? null : $(this).val()
        pagination.search.category = category_id
        initCategories(pid, ()=>{pagination.updatePagination()})
    })
}

function initCategories(pid, onsuccess = ()=>{}){
    let formData = new FormData();
    formData.append('pid', pid);
    $.ajax({
        type: "POST",
        url: '/shop/categories',
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (data){
            $('.categories').empty()
            if (data.category != null){
                $('.current-category').text(`(${data.category.title})`)
                category_id = data.category.id;
                pagination.search.category = category_id
                $('.category-up').data('id',data.category.parent)
                $('.category-up').show()
            }else{
                $('.category-up').hide()
                $('.current-category').empty()
            }
            for (const datum of data.children) {
                $('.categories').append($(`<div class="link link-dark filter-category mb-1" data-id="${datum.id}">${datum.title}</div>`))
            }
            onsuccess()
        }
    })
}

function initMinMaxDate(onsuccess = ()=>{}){
    $.ajax({
        type: "POST",
        url: '/shop/minMaxPrice',
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (data){
            console.log(data)
            $('#price_from')[0].min = data.min
            $('#price_to')[0].max = data.max
            $('#price_from').val(data.min)
            $('#price_to').val(data.max)
            onsuccess()
        }
    })
}

const productTemplate = (data) => {
    let product = data.product
    return `
        <div id="product-${product.id}" class="card mb-3">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="${product.images[0] ?? '/public/image/image_placeholder.png'}" style="width: 100%; height: 100%; max-height: 300px; object-fit:cover;" class="img-fluid rounded-start card-image" alt="...">
                </div>
                <div class="col-md-8">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">
                                ${product.name}
                            </h5>
                            <div class="product-category">Категорія: ${product.categories[0].name}</div>
                            <div class="product-category">Ціна: ${data.discounted_price}</div>
                            <p class="small-desc card-text mb-1">${product.description}</p>
                        </div>
                        <div class="card-control justify-content-between d-flex flex-row">
                            <a href="/shop/product/${product.id}" class="link-info">Детально</a>
                            <form class="d-flex flex-row gap-1 add-to-cart-form" >
                                <input type="hidden" value="${product.id}" name="product_id" readonly>
                                <input name="quantity" value="1" min="1" max="${product.quantity}" type="number" style="max-width: 50px" class="form-control form-select-sm">
                                <button class="btn btn-sm btn-success btn-add-to-cart">До кошика</button>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    `;
}



