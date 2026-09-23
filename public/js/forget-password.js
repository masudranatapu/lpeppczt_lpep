$(document).ready(function () {
    $("#otpFrom").on("submit", function (e) {
        e.preventDefault();
        let form = $(this).serialize();
        $.post("/check-otp", form, function (response) {
            if (response) {
                $("#opt-section").css({ 'display': 'none' });
                $("#password-reset-section").css({ 'display': 'block' });
            } else {
                let error_message = "<span class='text-danger'> Otp Doesn't Match</span>";
                $("#otperror").html(error_message)
            }
        })
    })

    var is_submit = false;
    $("#reset-password-form").on("submit", function (e) {
        if (!is_submit) {
            e.preventDefault();
            let form = $(this).serialize();
            let password = $("input[name=password]").val();
            let confirm_password = $("input[name=confirm_password]").val();
            if (password == confirm_password) {
                is_submit = true;
                $(this).submit();

            } else {
                $("#passwordErrorMessage").text("Confirm Password Doesn't Match");
            }
        }
    })
})