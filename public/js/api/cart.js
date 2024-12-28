$(document).ready(function (){
    $("#cart-table").DataTable({
        "paging": true,
        "lengthMenu": [10, 25, 50],
        "pageLength": 10,
        "responsive": true,
        "language": {
            "url": 'public/datatables/translation.json'
        },
        "columnDefs": [
            { "orderable": false, "targets": -1 },
            { "targets": [0,1,2], "className": "text-start" }
        ]
    });


    $(document).on('click', '.cart-item-remove',function (){
        let cartItemId = $(this).attr('data-cart-item-id');

        let formData = new FormData();
        formData.append('cart_item_id', cartItemId);

        $.ajax({
            type: "POST",
            url: '/api/carts/removeCartItem',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: formData,
            success: function (data){
                console.log(data)
                if (data.code !== 200){
                    alertify.alert('Виникла помилка!')
                }
                if (data.code === 200){
                    alertify.success('Видалено з кошика')
                    window.location.reload();
                }
            }
        })

    })

    $(document).on('click', '#btn-clear-cart', function (){
        $.ajax({
            type: "POST",
            url: '/api/carts/deleteCart',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data){
                console.log(data)
                if (data.code !== 200){
                    alertify.alert('Виникла помилка!')
                }
                if (data.code === 200){
                    alertify.alert('Кошик очищено')
                    window.location.reload();
                }
            }
        })
    })

    $(document).on('submit', '#offer-form', function (e){
        e.preventDefault()

        let formData = new FormData(this);

        $.ajax({
            type: "POST",
            url: '/api/carts/confirmOrder',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: formData,
            success: function (data){
                console.log(data)
                if (data.code !== 200){
                    alertify.alert('Виникла помилка!')
                }
                if (data.code === 200){
                    alertify.alert('Замовлення упшішно оформлено')
                    window.location.reload();
                }
            }
        })

    });

})