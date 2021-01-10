!(function($) {
    "use strict";
    
    $(".alert-zone").hide();
    $(".form-alert-zone").hide();
    $("#inscription-form").submit(function(e) {
        e.preventDefault();

        var erreur = false;

        if(!checkField("last-name", 2)) {
            erreur = true;
        }
        if(!checkField("first-name", 2)) {
            erreur = true;
        }
        if(!checkField("phone", 9)) {
            erreur = true;
        }

        if(!checkField("address", 3)) {
            erreur = true;
        }

        if(!checkField("username", 2)) {
            erreur = true;
        }
        if(!checkField("password", 8)) {
            erreur = true;
        }

        if(!checkField("conf-password", 8)) {
            erreur = true;
        }else if($("#conf-password").val() != $("#password").val()) {
            $("#conf-password").css("border", "1px solid #E91E1E");
            $(".conf-password-alert").html("Il ne correspond pas au mot de passe");
            $(".conf-password-alert").css("color", "#E91E1E")
            erreur = true;
        }else {
            $("#conf-password").css("border", "1px solid #00df00");
            $(".conf-password-alert").html("validé");
            $(".conf-password-alert").css("color", "#00df00")
        }

        if(erreur) {
            $('.alert-zone').slideDown(1000);
        }else {
            $.ajax({
                type: "POST",
                url: "/signin",
                data: $(this).serialize(),
                success: function(msg) {
                    if(msg == "__USERNAME_EXISTS__") {
                        $('#username').css('border', '1px solid #E91E1E');
                        $('.username-alert').html('Identifiant existant').css("color", "#E91E1E");
                        $(".form-alert-zone").html("Cet identifiant existe déjà");
                        $(".form-alert-zone").css({
                            background: "#E91E1E",
                            color: "#fff",
                        });
                        $(".form-alert-zone").slideDown(1000).delay(500).slideUp(1000);
                    }else if(msg == "__ECHEC__") {
                        $(".form-alert-zone").html("Erreur d'inscription :(");
                        $(".form-alert-zone").css({
                            background: "#E91E1E",
                            color: "#fff",
                        });
                        $(".form-alert-zone").slideDown(1000).delay(500).slideUp(1000);
                    }else if(msg == "__SUCCES__") {
                        $('#identifiant').val($("#username").val());
                        var scrollto = $("#connexion").offset().top - 100;

                        $("html, body").animate({
                            scrollTop: scrollto
                        }, 1500, 'easeInOutExpo');
                    }
                }
            });
        }
    });

    $("#connexion-form").submit(function(e) {
        e.preventDefault();

        if(!checkField("identifiant", 2) || !checkField("motDePasse", 8)) {
            return false;
        }
        $.ajax({
            type: "post",
            url: "/login",
            data: $(this).serialize(),
            success: function(msg) {
                //alert(msg);
                if(msg == "__SUCCES__") {
                    location.replace("/reader/library");
                }else if(msg == "__ERROR_PASSWORD__") {
                    $(".motDePasse-alert").html("erreur").css("color", "#E91E1E");
                    $(".motDePasse-alert").slideDown(1000);
                    $(".identifiant-alert").hide();
                }else if(msg == "__IDEN_PASSWORD__") {
                    $(".identifiant-alert").html("erreur").css("color", "#E91E1E");
                    $(".motDePasse-alert").hide();
                    $(".identifiant-alert").slideDown(1000);
                }
            }
        })
    });

})(jQuery);

function checkField(champs, maxChars) {
    var erreur = true;
    if($("#" + champs).val().length < maxChars) {
        $("#" + champs).css("border", "1px solid #E91E1E");
        $("." + champs + "-alert").html("Il faut au moins " + maxChars +" caractères");
        erreur = false;
    }else {
        $("#" + champs).css("border", "1px solid #00df00");
        $("." + champs + "-alert").html("validé");
        $("." + champs + "-alert").css("color", "#00df00")
    }

    return erreur;
}