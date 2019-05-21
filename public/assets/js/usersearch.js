$(document).ready(function () {

    var nameField = $('#user_search_SearchByFullName');
    var emailField = $('#user_search_SearchByEmail');

    var data = [];
    nameField.keyup(function () {
        var emailVal = emailField.val();
        data = {
            nameField: $(this).val(),
            emailField: emailVal,
            telephoneField: '',
        };
        callAjax(data)
    });
    emailField.keyup(function () {
        var nameVal = nameField.val();
        data = {
            nameField: nameVal,
            emailField: $(this).val(),
            telephoneField: '',
        };
        callAjax(data);
    });


    function callAjax(data)
    {
        $.ajax({
            url: '/admin/api/associates',
            type: "GET",
            data: data,
            contentType: 'text',
            success: function (data) {
                console.log(data);
            }
        })
    }

});