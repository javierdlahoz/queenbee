var $j = jQuery.noConflict();

$j(document).ready(function() {
    
    $j('.cupg_one_click_selectable').on('click', function() {
        $j(this).select();
    });
     
    /**
     * Popup design live preview
     */
    
    $j('#coupg_header').bind("keyup mouseup", function () {
        $j('.cupg_popup .cupg_header').html(cupg_nl2br($j(this).val()));
    });
    
    $j('#coupg_description').bind("keyup mouseup", function () {
        $j('.cupg_popup .cupg_description').html(cupg_nl2br($j(this).val()));
    });
    
    $j('#coupg_default_email_text').bind("keyup mouseup", function () {
        $j('.cupg_popup .cupg_email_input_wrapper .cupg_email_input').attr('placeholder', $j(this).val());
    });
    
    $j('#coupg_default_name_text').bind("keyup mouseup", function () {
        $j('.cupg_popup .cupg_email_input_wrapper .cupg_name_input').attr('placeholder', $j(this).val());
    });
    
    $j('#coupg_button_text').bind("keyup mouseup", function () {
        $j('.cupg_popup .cupg_submit_button').html($j(this).val());
    });
    
    $j('#coupg_privacy_statement').bind("keyup mouseup", function () {
        $j('.cupg_popup .cupg_privacy span').html($j(this).val());
    });
    
    $j('#coupg_pwdb').on("change", function() {
       if ($j(this).is(':checked')) {
           hide_if_visible($j('.cupg_poweredby'));
       }
       else {
           show_if_hidden($j('.cupg_poweredby'));
       }
    });
    
    $j('#coupg_theme').change(function () {
        var request = {
            action: "cupg_change_theme_preview", 
            cupg_id: $j(this).attr('data-id'),
            cupg_theme: $j(this).val()
        };
        $j.ajax({
            type: "post", 
            dataType: "json", 
            url: Cupg_Ajax.ajaxurl, 
            data: request, 
            success: function (e) {
                $j('#cupg_popup_preview').html(e.html);
            }
        });
        
    });
    
    function cupg_nl2br(str) {
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br />' + '$2');
    }
    
    
    /**
     * Content Upgrades Metaboxes
     */
    
    //Select shortcode
    toggle_location_inputs($j('#coupg_upg_location_page'));
    //Show custom url input
    $j('#coupg_upg_location_page').change(function () {
        toggle_location_inputs($j(this));
    });
    
    function toggle_location_inputs(element) {
        var value = $j(element).val();
        
        if (value == "-2")
            show_if_hidden($j('#cupg_content_custom_url_container'));
        else
            hide_if_visible($j('#cupg_content_custom_url_container'));
        
        if (value === 'already_sub' || value === 'thank_you' )
            show_if_hidden($j(element).next('.cupg_settings_block_note'));
        else
            hide_if_visible($j(element).next('.cupg_settings_block_note'));   
    }
    
    //Add A/B Header
    $j('#cupg_add_ab_header').on('click', function(e) {
        e.preventDefault();
        
        var hidden_ab_headlines = $j('.cupg_ab_headline.cupg_hidden').length;
        if (hidden_ab_headlines === 0) {
            return;
        }
        
        if (hidden_ab_headlines === 1) {
            $j(this).removeClass('button-primary').addClass('button-disabled');
        }

        $j('.cupg_ab_headline.cupg_hidden').first().removeClass('cupg_hidden');

    });
    
    //Delete A/B Header
    $j('.cupg_delete_ab_header').on('click', function() {
        
        $j(this).closest('.cupg_ab_headline').addClass('cupg_hidden');
        $j(this).closest('.cupg_ab_headline').find('textarea').val('');
        if ( $j('#cupg_add_ab_header').hasClass('button-disabled') ) {
            $j('#cupg_add_ab_header').removeClass('button-disabled').addClass('button-primary');
        }
        
    });
    
    //Change hidden field input for coupg_message_text   
    $j("#cupg_message_div").bind("DOMSubtreeModified",function() {
        var message = $j("#cupg_message_div").html(); 
        $j("#coupg_message_text").val(message);
    });
    
    //Change image
    
    var preview = $j('#cupg_popup_preview');
    //Custom
    $j('#cupg_custom_image').on('click', function(e) {
        e.preventDefault();
        var frame, image_url;
        var settings = {
            title: 'Select new image for pop-up',
            button: {
                text: 'Use This Image'
            },
            multiple: false
        };
        frame = get_media_frame(frame, settings);

        frame.on('select', function () {
            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();
            // Send the attachment URL to our custom image input field.
            image_url = attachment.url;
            frame.close();
            
            if (typeof (image_url) !== 'undefined') {
                preview.find('.cupg_figure').attr('style', '').css('background-image', 'url(' + image_url + ')');
                if (preview.find('.cupg_popup_wrapper').hasClass('no_image')) {
                    preview.find('.cupg_popup_wrapper').removeClass('no_image');
                }
                $j('#coupg_popup_image').val(image_url);
            }
        });
    });
    
    //Default
    $j('#cupg_default_image').on('click', function(e) {
        e.preventDefault();
        preview.find('.cupg_figure').attr('style', '');
        if (preview.find('.cupg_popup_wrapper').hasClass('no_image')) {
            preview.find('.cupg_popup_wrapper').removeClass('no_image');
        }
        $j('#coupg_popup_image').val('');
    });
    
    //None
    $j('#cupg_no_image').on('click', function(e) {
        e.preventDefault();
        preview.find('.cupg_figure').attr('style', '').css('background-image', 'none');
        if (!preview.find('.cupg_popup_wrapper').hasClass('no_image')) {
            preview.find('.cupg_popup_wrapper').addClass('no_image');
        }
        $j('#coupg_popup_image').val('none');
    });
    
    //Bonus file upload
    $j('#cupg_bonus_file').on('click', function(e) {
        e.preventDefault();
        var frame, file_url;
        var settings = {
            title: 'Upload bonus file',
            button: {
                text: 'Use This File as Bonus'
            },
            multiple: false
        };
        frame = get_media_frame(frame, settings);
        
        frame.on('select', function () {
            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();
            // Send the attachment URL to our custom image input field.
            file_url = attachment.url;
            frame.close();
            
            if (typeof (file_url) !== 'undefined') {
                $j('#coupg_bonus_file_url').text(file_url);
                $j('input[name="coupg_bonus_file_url"]').val(file_url);
                show_if_hidden($j('option.cupg_hidden'));
            }
            else {
                if ($j('#coupg_bonus_file_url').text() !== '') {
                    hide_if_visible($j('option.cupg_hidden'));
                }
            }
        });
    });
    
    $j('#coupg_add_to_depot').on('change', function() {
        if ( $j(this).is(':checked') ) {
            show_if_hidden($j('.cupg_bonus_depot_settings'));
        }
        else {
            hide_if_visible($j('.cupg_bonus_depot_settings'));
        }
    });
    
    function get_media_frame(frame, settings) {
        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }
        
        // Create a new media frame
        frame = window.wp.media(settings);
        frame.open();
        return frame;
    }
    
    /**
     * 
     */
    
    $j('#cupg_activate_plugin').on('click', function() {   
        var request = {
            action: "cupg_activate",
            cupg_code: $j('#cupg_key').val(),
            cupg_domain: $j('#cupg_domain').val(),
            cupg_email: $j('#cupg_email').val()
        };
        $j.ajax({
            type: 'post',
            dataType: 'json',
            url: Cupg_Ajax.ajaxurl,
            data: request,
            success: function (e) {
            if (e.status == 1) {
                alert('Plugin was successfully activated.');
                window.location = e.link;
            }
            else if (e.status == 0) {
                alert('Something went wrong.\nError: ' + e.error);
            }
        }});
    });
    
    
    /**
     * Settings page
     */
    
    //Refresh email service lists
    $j('#cupg_get_lists').on('click', function() {
        
        var request = {
            action: "cupg_get_lists",
            apikey: $j('#cupg_api_key').val(),
            appkey: $j('#cupg_app_key').val()
        };
        
        $j.ajax({
            type: "post",
            dataType: "json",
            url: Cupg_Ajax.ajaxurl,
            data: request,
            success: function (e) {
            if (e.status == 1) {
                if (e.listnum > 0) {
                    alert('Successfully grabbed ' + e.listnum + ' ' + (e.listnum == 1 ? 'list' : 'lists') + '\nList Names:\n' + e.listnames);
                }
                else {
                    alert('No email lists found');
                }
            }
            else if (e.status == 0) {
                alert('Something went wrong. Error messsage:\n' + e.error);
            }
        }})
        
    });
    
    //Change of email services and other options
    $j('#cupg_client').change(function() {
        $j('.cupg_settings_main').hide();
        $j('.cupg_keys').hide();
    });
    
    $j('#cupg_double_optin').change(function() {
        var double_optin_pages = $j('.cupg_double_optin_pages');
        var disable_double_optin_pages = $j('.cupg_disable_double_optin_pages');
        
        if ($j(this).is(':checked') ) {
            hide_if_visible(double_optin_pages);
            show_if_hidden(disable_double_optin_pages);
        }
        else {
            show_if_hidden(double_optin_pages);
            hide_if_visible(disable_double_optin_pages);
        }
    });
    
    $j('#cupg_send_email').change(function() {
        var email_settings = $j('.cupg_email_settings');
        if ($j(this).is(':checked') ) {
            show_if_hidden(email_settings);
        }
        else {
            hide_if_visible(email_settings);
        }
    });
    
    //Toggle display of custom Confirm, Already subscribed and Thank you pages url input
    $j('#cupg_confirm_sub').change(function () {
        toggle_visibility_by_select($j(this));
    });
    
    $j('#cupg_already_sub').change(function () {
        toggle_visibility_by_select($j(this));
    });
    
    $j('#cupg_thank_you').change(function () {
        toggle_visibility_by_select($j(this));
    });
    
    function show_if_hidden(elem) {
        if (elem.hasClass('cupg_hidden')) {
            elem.removeClass('cupg_hidden');
        }
    }
    
    function hide_if_visible(elem) {
        if (!elem.hasClass('cupg_hidden')) {
            elem.addClass('cupg_hidden');
        }
    }
    
    function toggle_visibility_by_select(select) {
        if (select.val() !== "-2") {
            hide_if_visible(select.siblings('.cupg_custom_page_container'));
        }
        else {
            show_if_hidden(select.siblings('.cupg_custom_page_container'));
        }
    }
    
    
    /**
     * Sitewide popup 
     */
    
    $j('#cupg_sitewide_popup_toggle').change(function() {
       if ($j(this).is(':checked')) {
           show_if_hidden($j('.cupg_sitewidepopup_settings'));
       }
       else {
           hide_if_visible($j('.cupg_sitewidepopup_settings'));
       }
    });
    
    $j('#cupg_sitewide_popup_display_type').change(function() {
       if ($j(this).val() === 'delay') {
           show_if_hidden($j('.cupg_popup_time_delay'));
       }
       else {
           hide_if_visible($j('.cupg_popup_time_delay'));
       }
    });
    
    
    /**
     * Fancy Boxes Live editor
     */

    var fb_settings = {
        id: 1,
        text: $j('#cupg_fb_text').val(),
        text2: $j('#cupg_fb_text2').val(),
        action1: $j('#cupg_action1').val(),
        action2: $j('#cupg_action2').val(),
        background: '',
        icon: true,
        align: $j('#cupg_align').val(),
        cu_id: 'none'
    };
    
    var default_fb_settings = {
        action1: $j('#cupg_action1').val(),
        action2: $j('#cupg_action2').val(),
        align: $j('#cupg_align').val()
    }
    var properties = ['align'];
    var text2_toggled = false;
    
    //Modal

    if (top.tinymce && top.tinymce.activeEditor) {
        
        var modalArgs = top.tinymce.activeEditor.windowManager.getParams();
        fb_settings.text = modalArgs.editor.selection.getContent();

        update_fb_shortcode();
        update_content_fields(fb_settings.text, "");
        
        $j('#fb-code .hndle.ui-sortable-handle span').text("INSERT");
            
        $j('#cupg_insert_code').on('click', function() {
            var shortcode = '';
            if ($j('#cupg_shortcode').val() === 'fancy_box')
                shortcode = $j('#cupg_code').val();
            else {
                var linked_cu = (fb_settings.cu_id === 'none')? '': ' id=' + fb_settings.cu_id ;
                shortcode = '[content_upgrade' + linked_cu + ']' + fb_settings.text + '[/content_upgrade]';
            }
            
            modalArgs.editor.selection.setContent(shortcode);
            modalArgs.editor.windowManager.close();
        });
        
        $j('#cupg_shortcode').on('change', function() {
            if ($j(this).val() === 'fancy_box') {
                $j('.cupg_shortcode_toggleable').show();
                $j('#fb-preview').show();
                hide_if_visible($j('.cupg_shortcode_reverse_toggleable'));
            } else {
                $j('.cupg_shortcode_toggleable').hide();  
                $j('#fb-preview').hide();
                show_if_hidden($j('.cupg_shortcode_reverse_toggleable'));
            }
        });
    }
    
    //Color picker
    if (typeof $j('.cupg_colorpicker').wpColorPicker === 'function') {
        
        $j('.cupg_colorpicker').wpColorPicker();
        
        // Color picker Mutation Observer
        var colorPickerObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'style') {
                    var color = $j('.wp-color-result').attr('style');
                    if (color)
                        fb_settings.background = color.split('color: ')[1].slice(0, -1);
                    else
                        fb_settings.background = "";
                    if(fb_settings.id != 10)
                        $j('.cupg_fancybox').css('background', fb_settings.background);
                    else
                        $j('.fancybox_action1').css('background', fb_settings.background);
                    update_fb_shortcode();
                }
            });    
        });
        colorPickerObserver.observe(document.querySelector('.wp-color-result'), { attributes: true, attributeFilter: ['style'] });
    }

    $j('#cupg_type').change(function () {
        fb_settings.id = $j(this).val();
        var request = {
            action: 'cupg_change_fb_preview',
            cupg_fb: fb_settings
        };
        $j.ajax({
            type: "post", 
            dataType: "json", 
            url: Cupg_Ajax.ajaxurl, 
            data: request, 
            success: function (e) {
                $j('.cupg_preview_wrapper').html(e.html.replace(/\\"/g, '"'));
                properties = e.properties;
                update_fb_settings();
                update_fb_shortcode();
            }
        });
    });

    $j('#cupg_icon').on('change', function() {
        var fancyboxPreview = $j('.cupg_fancybox');
        var icon = fancyboxPreview.hasClass('no_icon');
        if ($j(this).is(':checked') && icon) {
            fancyboxPreview.removeClass('no_icon');
            fb_settings.icon = true;
        }
        if (!$j(this).is(':checked') && !icon ) {
            fancyboxPreview.addClass('no_icon');
            fb_settings.icon = false;
        }
        update_fb_shortcode();
    });

    $j('#cupg_fb_text').on('input', function () {
        fb_settings.text = cupg_nl2br($j(this).val());
        $j('.fancybox_text').html(fb_settings.text);
        update_fb_shortcode();
    });
    
    $j('#cupg_fb_text2').on('input', function () {
        fb_settings.text2 = cupg_nl2br($j(this).val());
        $j('.fancybox_text2').html(fb_settings.text2);
        update_fb_shortcode();
    });

    $j('#cupg_action1').on('input', function () {
        fb_settings.action1 = $j(this).val();
        $j('.fancybox_action1').text(fb_settings.action1);
        update_fb_shortcode();
    });

    $j('#cupg_action2').on('input', function () {
        fb_settings.action2 = $j(this).val();
        $j('.fancybox_action2').text(fb_settings.action2);
        update_fb_shortcode();
    });

    $j('#cupg_align').on('change', function() {
        fb_settings.align = $j(this).val();
        $j('.fancybox_text').css('text-align', $j(this).val());
        update_fb_shortcode();
    });

    $j('#cupg_cu_selector').on('change', function() {
        fb_settings.cu_id = $j(this).val();
        update_fb_shortcode();
    });
    
    function split_text() {
        var text = fb_settings.text;
        text = text.split('.');
        fb_settings.text2 = text[0];
        fb_settings.text = "";
        if (text.length > 1) {
            fb_settings.text2 += '.';
            text = text.splice(1);
            fb_settings.text = text.join('.').trim();
        }
    }
    
    function update_content_fields(text, text2) {
        $j('.fancybox_text').text(text);
        $j('#cupg_fb_text').val(text);
        $j('.fancybox_text2').text(text2);
        $j('#cupg_fb_text2').val(text2);
    }

    function update_fb_shortcode() {
        var linked_cu = (fb_settings.cu_id === 'none')? '': ' linked_cu=' + fb_settings.cu_id ;
        var icon = (properties.indexOf('icon') > -1 && fb_settings.icon === false)? ' icon=false' : '';
        var background = (properties.indexOf('background') > -1 && fb_settings.background)? ' background="' + fb_settings.background + '"' : '';
        var action1 = (properties.indexOf('action1') > -1 && fb_settings.action1 && fb_settings.action1 !== default_fb_settings.action1)? ' action1="' + fb_settings.action1 + '"' : '';
        var action2 = (properties.indexOf('action2') > -1 && fb_settings.action2 && fb_settings.action2 !== default_fb_settings.action2)? ' action2="' + fb_settings.action2 + '"' : '';
        var text2 = (properties.indexOf('text2') > -1 && fb_settings.text2)? ' text2="' + fb_settings.text2 + '"' : '';
        var align = (properties.indexOf('align') > -1 && fb_settings.align && fb_settings.align !== default_fb_settings.align)? ' align="' + fb_settings.align + '"' : '';
        $j('#cupg_code').val('[fancy_box id=' + fb_settings.id + linked_cu + background + icon + action1 + action2 + text2 + align + ']' + fb_settings.text + '[/fancy_box]');
    }

    function update_fb_settings() {
        var propertiesList = ['align', 'background', 'icon', 'action1', 'action2', 'text2'];
        var saved = [$j('#cupg_align').val(), $j('#cupg_color').val(), $j('#cupg_icon').is(':checked'), 
            $j('#cupg_action1').val(), $j('#cupg_action2').val(), $j('#cupg_fb_text2').val()];

        for (var i = 0; i < propertiesList.length; i++) {
            if (properties.indexOf(propertiesList[i]) === -1) {
                hide_if_visible($j('.cupg_' + propertiesList[i]));
                if (text2_toggled && propertiesList[i] === 'text2') {
                    fb_settings.text = (fb_settings.text2 + " " + fb_settings.text).trim(); 
                    update_content_fields(fb_settings.text, "");
                    text2_toggled = false;
                }
                fb_settings[propertiesList[i]] = saved[i];
            } else {
                show_if_hidden($j('.cupg_' + propertiesList[i]));
                if (propertiesList[i] === 'text2') {
                    split_text();
                    update_content_fields(fb_settings.text, fb_settings.text2);
                    text2_toggled = true;
                }
            }
        }
    }
});
