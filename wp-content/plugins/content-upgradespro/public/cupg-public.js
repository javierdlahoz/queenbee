var $j = jQuery.noConflict();

$j(document).ready(function() {
    
    function send_ajax(request) {
        
        $j.ajax({
            type: "post",
            dataType: "json",
            url: Cupg_Ajax.ajaxurl,
            data: request
        });
    }
    
    function get_cookie(cookie_name) {
        
        var name = cookie_name + "=";
        var cookie_array = document.cookie.split(';');
        for(var i = 0; i < cookie_array.length; i++) {
            var c = cookie_array[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) == 0) {
                var cookie_value = c.substring(name.length, c.length);
                if (cookie_value.length > 0) {
                    cookie_value = decodeURIComponent(cookie_value);
                    return JSON.parse(cookie_value);
                }
            }
        }
        return "";
    }
    
    function set_cookie(cookie_name, cookie_value, cookie_expiry_days) {
        
        var d = new Date();
        d.setTime(d.getTime() + (cookie_expiry_days * 24 * 60 * 60 * 1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cookie_name + "=" + cookie_value + "; " + expires;
    } 
    
      
    /* Visit */
    
    //Update statistic on user visit
    var cupg_ids = [];
    $j('.cupg_link_container').each(function () {
        var id = $j(this).attr('data-id');
        if (cupg_ids.indexOf(id) === -1) {
            cupg_ids.push(id);
        }
    });
    
    if (cupg_ids.length > 0) {
        var request = {
            action: 'cupg_visits',
            upgrade_ids: cupg_ids
        }
        send_ajax(request);
    }
    
    /* End Visit */


    /* Popup */
   
    function cupg_show_popup(cupg_upgrade_box, popup_type) {
        
        cupg_upgrade_box.show();
        
        var cupg_session = get_cookie(Cupg_Session.cookies.session);
        if (typeof cupg_session.name !== 'undefined') {
            cupg_upgrade_box.find('.cupg_name_input').val(unescape(cupg_session.name));
        }
        if (typeof cupg_session.email !== 'undefined') {
            cupg_upgrade_box.find('.cupg_email_input').val(cupg_session.email);
        }
        
        var request = {
            action: "cupg_popups",
            popup_type: popup_type,
            upgrade_id: cupg_upgrade_box.attr('data-id'),
            header_id: cupg_upgrade_box.find('.cupg_header').attr('data-id')
        };
        send_ajax(request);
        toggleScrolling('off');
    }
    
    function manage_sitewide_popup_cookies(block_popup) {
        
        var popup_shown = get_cookie(Cupg_Session.cookies.counter);
        var counter_validity = 365;
        var blocker_validity = + Cupg_Session.popup.interval;
        
        if (Cupg_Session.popup.max_times_shown != 0 &&
                (popup_shown >= Cupg_Session.popup.max_times_shown || block_popup)) {
            counter_validity = -1;
            blocker_validity = 365;
        }
        
        set_cookie(Cupg_Session.cookies.counter,  + popup_shown + 1, counter_validity);
        set_cookie(Cupg_Session.cookies.blocker, true, blocker_validity);
    }
    
    //Open standard popup [content_upgrade]
    $j('.cupg_link_container > a').on('click', function(e) {
        e.preventDefault();
        
        var cu_id = $j(this).parent().attr('data-id');
        var cupg_upgrade_box = $j('.cupg_regular_popup.cupg_upgrade_box_' + cu_id);
        cupg_show_popup($j(cupg_upgrade_box), 'popups');
    });
    
    //Open standard popup [fancy_box]
    $j('.cupg_link_container.cupg_fancybox').on('click', function(e) {
        e.preventDefault();
        
        var cu_id = $j(this).attr('data-id');
        var cupg_upgrade_box = $j('.cupg_regular_popup.cupg_upgrade_box_' + cu_id);
        cupg_show_popup($j(cupg_upgrade_box), 'popups');
    });

    //Open sitewide popup
    $j('.cupg_sitewide_popup').each(function() {
        var popup = $j(this);
        //Open after delay
        if (Cupg_Session.popup.display_type !== 'exit') {
            setTimeout(function() {
                cupg_show_popup(popup, 'popups_popup');
                manage_sitewide_popup_cookies(false);
            }, Cupg_Session.popup.delay * 1000, popup);
        }
        //Open at exit intent
        else {
            $j(document).on("mousemove", popup, track_mouse);
        }
    });
    
    function track_mouse(e) {
        if (e.clientY % 15 === 0) {
            track_mouse.prev = e.clientY;
        }

        if (e.clientY <= 45 && track_mouse.prev && track_mouse.prev > e.clientY) {
            cupg_show_popup(e.data, 'popups_popup');
            manage_sitewide_popup_cookies(false);
            $j(document).off("mousemove", track_mouse);
        }
    }
    
    /* End Popup */
    
    
    /* Subscription */
    
    $j('.cupg_submit_button').on('click', function (e) {
        e.preventDefault();
        
        var name = $j(this).closest('.cupg_popup_wrapper').find('.cupg_name_input').val();
        if (typeof name !== 'undefined' && name.length === 0) {
            alert('Please enter your name');
            return;
        }

        var email = $j(this).closest('.cupg_popup_wrapper').find('.cupg_email_input').val();        
        if (!isEmail(email)) {
            alert('Invalid E-mail');
            return;
        }
        
        //Distinguish subscription with standard and sitewide popup
        if ($j(this).hasClass('cupg_sitewide_popup_submit')) {
            subscribe($j(this), email, name, 'subscriptions_popup');
        }
        else {
            subscribe($j(this), email, name, 'subscriptions');
        }
    });
    
    //Press Enter to subscribe
    $j(document).keypress(function(e) {
        if (e.which == 13 && $j('.cupg_popup').is(':visible')) {
            $j('.cupg_popup:visible').find('.cupg_submit_button').click();
        }
    });
    
    function isEmail(email) {
        var regex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        return regex.test(email);
    }
    
    function subscribe(button, email, name, popup_type) {
        
        button.addClass('cupg_action');
        button.attr('disabled', 'disabled');
        var cupg_upgrade_box = button.closest('.cupg_popup');
        
        var request = {
            action: "cupg_subscriptions",
            popup_type: popup_type,
            upgrade_id: cupg_upgrade_box.attr('data-id'),
            header_id: cupg_upgrade_box.find('.cupg_header').attr('data-id'),
            email: email
        };
        
        if (typeof name !== 'undefined') {
            request.subscriber_name = name;
        }

        $j.ajax({
            type: "post",
            dataType: "json",
            url: Cupg_Ajax.ajaxurl,
            data: request,
            success: function(e) {
                
                if (popup_type === 'subscriptions_popup') {
                    manage_sitewide_popup_cookies(true);
                }
                
                if (e.status == 'success') {
                    if (e.link !== 'none') {
                        window.location = e.link;
                    }
                } else
                    alert('Something went wrong.\nError code: ' + e.error);
                
                closePopup(button);
            },
            error: function(e) {
                closePopup(button);
            }
        });
    }
    
    /* End Subscription */
    
    
    //Close popup
    $j('.cupg_close_button').on('click', function() {
        closePopup($j(this).closest('.cupg_popup'));
    });
    
    function closePopup(popup) {
        var button = popup.find('.cupg_submit_button');
        if (button.hasClass('cupg_action')) {
            button.removeAttr('disabled');
            button.removeClass('cupg_action');
        }
        popup.fadeOut();
        toggleScrolling('on');
    }
    
    function toggleScrolling(state) {
        var noScroll = $j('html').hasClass('cupg_no_scroll');
        
        if (state === 'off' && !noScroll) {
            $j('html').addClass('cupg_no_scroll');
        }
        
        if (state === 'on' && noScroll) {
            $j('html').removeClass('cupg_no_scroll');
        }
    }
    
});