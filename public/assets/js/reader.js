!(function($) {
    "use strict";

    getNotifs();
    
    $(".alert-popup").hide();
    
    $("#addBook button").css("background", "#E91E1E");
    $(document).on('click', "#addBook", function(e) {
        e.preventDefault();
        $("#newBook").slideToggle(1000);
        $("#addBook i").toggleClass("icofont-plus icofont-minus");
        if($("#addBook span").html() == "Ajouter") {
        	$("#addBook span").html("Masquer");
        	$("#addBook button").css("background", "#E91E1E");
        }else{
        	$("#addBook span").html("Ajouter");
        	$("#addBook button").css("background", "#000");
        }
	});

    // Redirecter
    $(".nav-menu li").on('click', function(e) {
        e.preventDefault();

        var link = $(this).children('a').attr('href');
        location.replace("/" + link);
    });


})(jQuery);

function deleteBook(e, id) {
    e.preventDefault();

    if(confirm("Etes-vous sûr de vouloir supprimer cet élément ?")) {
        $.ajax({
            type: "post",
            url: "/reader/delete",
            data: "id=" + id,
            success: function(msg) {
                
                location.reload();
            }
        })
    }else {
        alert("NON");
    }
}

function removeFromPreference(e, id) {
    e.preventDefault();
    if(!confirm("Vous êtes sûr ?"))
        return false;
    $.ajax({
        type: "POST",
        url: "/reader/preference/remove",
        data: "id=" + id,
        success: function(msg) {
            if(msg == "__REMOVED__") {
                location.reload();
            }else {
                alert(msg);
            }
        }
    });
}

function exchange(e, against, userBook) {
    e.preventDefault();
    $("html, body").css({
        'cursor': 'progress'
    });
    $.ajax({
        type: "POST",
        url: "/reader/exchanges",
        data: "idLecteurBook=" + userBook + "&idAgainstBook=" + against,
        success: function(msg) {

            $("html, body").css({
                'cursor': 'pointer'
            });

            if(msg == "__DEJA_ACCEPTER__") {
                $(".alert-popup").css({
                    top: e.clientY,
                    left: e.clientX
                });

                $('.alert-popup p').html("Vous l'avez déjà accepté !");
                $('.alert-popup p').attr("class", "alert alert-warning");
                $('.alert-popup').fadeIn(1000).delay(1500).fadeOut(500);
            } else if(msg == "__ECHANGE_EN_COURS__") {
                $(".alert-popup").css({
                    top: e.clientY,
                    left: e.clientX
                });

                $('.alert-popup p').html("Echange en cours");
                $('.alert-popup p').attr("class", "alert alert-warning");
                $('.alert-popup').fadeIn(1000).delay(1500).fadeOut(500);
            }else if(msg == "__ECHANGE_VALIDE__") {
                $(".alert-popup").css({
                    top: e.clientY,
                    left: e.clientX
                });

                $('.alert-popup p').html("Echange Validé");
                $('.alert-popup p').attr("class", "alert alert-success");
                $('.alert-popup').fadeIn(1000).delay(1500).fadeOut(500);
                removeFromPreference(e, against);
            }
        }
    });
}

function getNotifs() {
    setTimeout(function() {
        $.ajax({
            type: "GET",
            url: "/reader/propositions",
            success: function(notifs) {
                //alert(notifs);
                $(".number-notif").html("" + notifs);
            }
        });

        getNotifs();        
    }, 500);

}