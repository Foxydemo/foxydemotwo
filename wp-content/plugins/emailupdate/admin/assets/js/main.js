jQuery(function() {
    //for changing colors
    jQuery("#simple_form").validate ({
        submitHandler:function(){
            var post_data = jQuery("#simple_form").serialize()+"&action=simple_form";
            jQuery.post(main_script.ajaxurl, post_data, function(response){
                console.log(response);
                if (response !== "0") {
                    alert(response);
                    if (response == "Email Address updated successfully") {
                        window.location.href = window.location.href;
                    }
                }
            });
        }
    });
});

jQuery(function() {
    jQuery('#emailinput').on("input", function() {

        var empty = false;
        
        var email1 = jQuery('#emailinput').val();
        var email2 = jQuery('#emailconfirm').val();

        if (email1 == "" || email2 == "") {
            empty = true;
        }

        if (email1 != email2) {
            empty = true;
        }

        if (empty) {
            jQuery('#register').attr('disabled', 'disabled');
        } else {
            jQuery('#register').removeAttr('disabled');
        }
    });

    jQuery('#emailconfirm').on("input", function() {

        var empty = false;
        
        var email1 = jQuery('#emailinput').val();
        var email2 = jQuery('#emailconfirm').val();

        if (email1 == "" || email2 == "") {
            empty = true;
        }

        if (email1 != email2) {
            empty = true;
        }

        if (empty) {
            jQuery('#register').attr('disabled', 'disabled');
        } else {
            jQuery('#register').removeAttr('disabled');
        }
    });
})