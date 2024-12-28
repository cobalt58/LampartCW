$(document).ready(function (){
    $("#offers-table").DataTable({
        "paging": true,
        "lengthMenu": [10, 25, 50],
        "pageLength": 10,
        "responsive": true,
        "language": {
            "url": 'public/datatables/translation.json'
        },
        "columnDefs": [
            { "targets": [0,1,2], "className": "text-start" }
        ]
    });
})


$('#update-btn').click('click', function (event) {
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
            $('#profile-form')[0].reset()
            $('#user-id').val(response.user.id)
            $('#phone').val(response.user.phone)
            $('#email').val(response.user.email)
            $('#name').val(response.user.name)
            $('#surname').val(response.user.surname)
            $('#middlename').val(response.user.patronymic)


            $('#modal-avatar')[0].src =
                response.user.avatar != null
                    ? response.user.avatar
                    : "/public/image/user.png";

            $('#profile-modal').modal('show')
        }
    })

})

function updateData(user) {
    $('#login-preview').val(user.phone)
    $('#email-preview').val(user.email)
    $('#user-email').text(user.email)
    $('#user-avatar')[0].src =
        user.avatar != null
            ? user.avatar
            : "/public/image/user.png";
}

$('#media').change(function (event) {
    let files = $(this)[0].files;

    let reader = new FileReader();
    reader.onload = function(e) {
        $('#modal-avatar')[0].src = e.target.result;
    };
    reader.readAsDataURL(files[0]);
})

$(document).on('submit', '#profile-form', function (e) {
    e.preventDefault();

    $('#alert-error').empty().addClass('d-none')
    let formData = new FormData(this)


    $.ajax({
        type: "POST",
        url: '/api/users/updateUser',
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
                $('#profile-modal').modal('hide')
                updateData(response.user)
                alertify.success('Успішно')
            }
        }
    })
})