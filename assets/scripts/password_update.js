$(document).ready(function () {
    $('#password_update_newpassword').on('input', function () {
        checkpass();
    });
    $('#password_update_passwordConfirm').on('input', function () {
        checkcpass();
    });
});

function checkpass() {
    var pattern2 = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
    var pass = $('#password_update_newpassword').val();
    var validpass = pattern2.test(pass);

    if (pass == "") {
        $('#newPassword_err').html('Le mot de passe ne peut pas être vide');
        return false;
    } else if (!validpass) {
        $('#newPassword_err').html("Minimum 8 et jusqu'à 15 caractères, au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial:");
        return false;

    } else {
        $('#newPassword_err').html("");
        return true;
    }
}

function checkcpass() {
    var pass = $('#password_update_newpassword').val();
    var cpass = $('#password_update_passwordConfirm').val();
    if (cpass == "") {
        $('#passwordConfirm_err').html('Confirmation du mot de passe ne peut pas être vide');
        return false;
    } else if (pass !== cpass) {
        $('#passwordConfirm_err').html("Vous n'avez pas correctement confirmé votre mot de passe");
        return false;
    } else {
        $('#passwordConfirm_err').html('');
        return true;
    }
}