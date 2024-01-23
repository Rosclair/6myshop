$(document).ready(function () {
    $('#registration_fullname').on('input', function () {
      checkuser();
    });
    $('#registration_email').on('input', function () {
        checkemail();
    });
    $('#registration_password').on('input', function () {
        checkpass();
    });
    $('#registration_passwordConfirm').on('input', function () {
        checkcpass();
    });
});

function checkuser() {
    var pattern = /^[A-Za-z0-9 ]+$/;
    var user = $('#registration_fullname').val();
    var validuser = pattern.test(user);
    if ($('#registration_fullname').val().length < 5) {
        $('#fullname_err').html('Top Court! Ne doit pas être inférieur à 5 cararacters');
        return false;
    } else if (!validuser) {
        $('#fullname_err').html('Le nom doit être a-z, A-Z uniquement');
        return false;
    } else {
        $('#fullname_err').html('');
        return true;
    }
}
  
function checkemail() {
    var pattern1 = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    var email = $('#registration_email').val();
    var validemail = pattern1.test(email);
    if (email == "") {
        $('#email_err').html('Champs requis');
        return false;
    } else if (!validemail) {
        $('#email_err').html('Email invalid');
        return false;
    } else {
        $('#email_err').html('');
        return true;
    }
}

function checkpass() {
    var pattern2 = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
    var pass = $('#registration_password').val();
    var validpass = pattern2.test(pass);

    if (pass == "") {
        $('#password_err').html('Le mot de passe ne peut pas être vide');
        return false;
    } else if (!validpass) {
        $('#password_err').html("Minimum 8 et jusqu'à 15 caractères, au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial:");
        return false;

    } else {
        $('#password_err').html("");
        return true;
    }
}

function checkcpass() {
    var pass = $('#registration_password').val();
    var cpass = $('#registration_passwordConfirm').val();
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