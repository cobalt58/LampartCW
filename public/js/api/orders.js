let table = null;

function createButtons(id, status) {
    let disabled = status === "Опрацьоване" ? 'disabled' : '';
    return `<button type="button"  ${disabled} class="btn btn-outline-dark edit-btn btn-sm mx-1" value="${id}"><i class="fa-solid fa-check"></i></button>
            <button type="button" class="btn btn-outline-dark details-btn btn-sm mx-1" value="${id}"><i class="fa-solid fa-gear"></i></button>
            <button type="button" class="btn btn-outline-danger remove-btn btn-sm" value="${id}"><i class="fa-solid fa-trash"></i></button>`;

}

function updateRow(id, data) {
    table.row(`#row_${id}`).data([
        data.user.surname + " " + data.user.name + " " + data.user.patronymic,
        data.order_date,
        data.delivery_address,
        data.total_order_price,
        data.status,
        createButtons(id, data.status)
    ])
}

$(document).ready(function () {
    table = $("#orders-table").DataTable({
        "paging": true,
        "lengthMenu": [10, 25, 50],
        "pageLength": 10,
        "serverSide": true,
        "processing": true,
        "createdRow": function (row, data, rowIndex) {
            row.id = 'row_' + data[5];
            row.cells[5].innerHTML = createButtons(data[5], data[4]);
            for (const cell of row.cells) {
                cell.style.maxWidth = '150px';
                cell.style.overflow = 'hidden';
                cell.style.textOverflow = 'ellipsis';
            }
        },
        "columnDefs": [
            {"orderable": false, "targets": -1, "className": "text-center"}
        ],
        "ajax": {
            "url": "/api/orders/getOrdersPagination",
            "type": "post",
        },
        "language": {
            "url": 'public/datatables/translation.json'
        },
        "layout": {
            "top": 'search',
            "topStart": 'pageLength',
            "topEnd": 'buttons'
        }
    });
});

$(document).on('click', '.edit-btn', function () {
    let id = $(this).val()
    let formData = new FormData();
    formData.append('id', id)
    $.ajax({
        type: "POST",
        url: "/api/orders/process",
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (response) {
            if (response.code !== 200) {
                alertify.alert("Помилка обробки!")
            }
            if (response.code === 200) {
                updateRow(response.order.id, response.order);
                alertify.success('Успішно')
            }
        }
    })
})

$(document).on('click', '.details-btn', function () {
    let id = $(this).val()
    let formData = new FormData();
    formData.append('id', id)
    $.ajax({
        type: "POST",
        url: "/api/orders/getOrder",
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (response) {
            if (response.code !== 200) {
                alertify.alert("Помилка обробки!")
            }
            if (response.code === 200) {
                $('#details-table tbody').empty();
                $('#order-modal').modal('show');
                response.order.order_items.forEach(item => {
                    const productName = item.product.name;
                    const quantity = item.quantity;
                    const pricePerItem = item.price_at_order_time;
                    const totalPrice = (parseFloat(pricePerItem) * parseInt(quantity)).toFixed(2);

                    // Створюємо рядок
                    const row = `
                        <tr>
                            <td>${productName}</td>
                            <td>${quantity}</td>
                            <td>${pricePerItem}</td>
                            <td>${totalPrice}</td>
                        </tr>
                    `;

                    // Додаємо рядок до таблиці
                    $('#details-table tbody').append(row);
                });
                console.log(response);
            }
        }
    })
})

$(document).on('click', '.remove-btn', function () {
    let id = $(this).val()
    let formData = new FormData()
    formData.append('id', id)

    alertify
        .confirm(
            'Ви впевнені, що бажаєте видалити замовення?',
            function () {
                $.ajax({
                    type: "POST",
                    url: "/api/orders/deleteOrder",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.code !== 200) {
                            alertify.error('Помилка видалення')
                        }
                        if (response.code === 200) {
                            alertify.success('Замовлення видаленно')
                            $(`#row_${response.order_id}`).remove()
                        }
                    }
                })
            },
            function () {
                alertify.notify('Скасовано', 'custom', 2);
            })
})
