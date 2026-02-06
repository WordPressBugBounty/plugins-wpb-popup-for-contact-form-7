+(function($) {

    "use strict";

    // Country & Phone Field Contact Form 7 by Narinder Singh Bisht
    function wpbPcfCountryAndPhoneField(){
        if  (typeof $.fn.countrySelect === "function" ) {
            $(".wpcf7-countrytext").countrySelect();
        }
        
        if  (typeof $.fn.intlTelInput === "function" ) {
            $(".wpcf7-phonetext").intlTelInput({
               autoPlaceholder: "off",
               hiddenInput: "full_number",
               nationalMode: false,
            });
        }
    }

    /**
     * WPCF7 Cloudflare Turnstile Support
     */
    function pcfTurnstileSupport(popupEl) {
        // Wait a short moment for the DOM to render
        setTimeout(function () {
            // Check if Cloudflare Turnstile is loaded
            if (typeof turnstile !== "undefined") {
                // === Contact Form 7 Support ===
                $(popupEl)
                    .find(".wpcf7-turnstile, .cf-turnstile")
                    .each(function () {
                        var el = this;

                        // Clean up any existing widget first
                        var $el = $(el);
                        if ($el.find("iframe").length) {
                            // Remove existing iframe/widget
                            $el.empty();
                        }

                        // Clear any existing widget ID
                        if ($el.data("turnstile-widget-id")) {
                            try {
                                turnstile.remove(
                                    $el.data("turnstile-widget-id")
                                );
                            } catch (e) {
                                console.log("Turnstile widget removal skipped");
                            }
                            $el.removeData("turnstile-widget-id");
                        }

                        var siteKey =
                            $el.data("sitekey") || $el.attr("data-sitekey");

                        // Render the Turnstile widget
                        var widgetId = turnstile.render(el, {
                            sitekey: siteKey,
                            theme: "auto",
                            callback: function (token) {
                                console.log("Turnstile verified");
                            },
                        });

                        // Store widget ID for later cleanup
                        $el.data("turnstile-widget-id", widgetId);
                    });
            }
        }, 200); // slight delay to ensure form HTML is ready
    }

    /**
     * Reset Turnstile widgets
     */
    function resetTurnstile(container) {
        if (typeof turnstile === "undefined") return;

        $(container)
            .find(".wpcf7-turnstile, .cf-turnstile")
            .each(function () {
                var widgetId = $(this).data("turnstile-widget-id");
                if (widgetId !== undefined) {
                    try {
                        turnstile.reset(widgetId);
                    } catch (e) {
                        console.log("Turnstile reset failed:", e);
                    }
                }
            });
    }
    // Handle validation errors and spam detection
    $(document).on("wpcf7invalid wpcf7spam wpcf7mailfailed", function (event) {
        if ($(event.target).closest(".swal2-popup").length) {
            // Reset Turnstile on error
            setTimeout(function () {
                resetTurnstile(event.target);
            }, 100);
        }
    });

    /**
     * Clean existing Turnstile before moving form
     */
    function cleanTurnstileBeforeMove(container) {
        if (typeof turnstile === "undefined") return;

        $(container)
            .find(".wpcf7-turnstile, .cf-turnstile")
            .each(function () {
                var $el = $(this);

                // Remove iframe
                $el.find("iframe").remove();

                // Remove widget ID if exists
                var widgetId = $el.data("turnstile-widget-id");
                if (widgetId !== undefined) {
                    try {
                        turnstile.remove(widgetId);
                    } catch (e) {
                        console.log("Turnstile removal skipped");
                    }
                    $el.removeData("turnstile-widget-id");
                }

                // Clear the container
                $el.empty();
            });
    }

    // Trigger the popup on click
    $(document).on("click", ".wpb-pcf-form-fire", function(e) {
        
        e.preventDefault();

        var button          = $(this),
        id                  = button.attr('data-id'),
        post_id             = button.attr('data-post_id'),
        form_style          = button.attr('data-form_style') ? !0 : !1,
        allow_outside_click = button.attr('data-allow_outside_click') ? !0 : !1,
        width               = button.attr('data-width'),
        popup_type          = button.attr('data-popup_type');


        if (popup_type === "on_page_load") {
            // Get the actual form element (not clone)
            var formElement = $('.wpb-pcf-popup-content[data-id="' + id + '"]')
                .first()
                .find(".wpcf7");
            var originalParent = formElement.parent();

            // Clean existing Turnstile before moving
            cleanTurnstileBeforeMove(formElement);

            Swal.fire({
                html: '<div id="wpb-pcf-swal-form-wrapper"></div>',
                showConfirmButton: false,
                customClass: {
                    container: 'wpb-pcf-form-style-' + form_style,
                },
                padding: '30px',
                width: width,
                showCloseButton: true,
                allowOutsideClick: allow_outside_click,
                backdrop: true,
                didOpen: (popupEl) => {
                    // Move the actual initialized form into the popup
                    $("#wpb-pcf-swal-form-wrapper").append(formElement);
                    pcfTurnstileSupport(popupEl);
                },
                willClose: () => {
                    // Clean Turnstile before moving back
                    cleanTurnstileBeforeMove(formElement);

                    // Move the form back to its original location
                    originalParent.append(formElement);
                },
            });
        } else {
            wp.ajax.send( {
                data: {
                    action                  : 'wpb_pcf_fire_contact_form',
                    pcf_form_id             : id,
                    wpb_pcf_post_id         : post_id,
                    wpb_pcf_fire_popup_nonce: WPB_PCF_Vars.nonce
                },
                beforeSend : function ( xhr ) {
    				button.addClass('wpb-pcf-btn-loading');
    			},
                success: function( res ) {
                    button.removeClass('wpb-pcf-btn-loading');

                    Swal.fire({ 
                        html: res,
                        showConfirmButton: false,
                        customClass: {
                            container: 'wpb-pcf-form-style-' + form_style,
                        },
                        padding: '30px',
                        width: width,
                        showCloseButton: true,
                        allowOutsideClick: allow_outside_click,
                        backdrop: true,
                    });
                    
                    // For CF7 5.3.1 and before
                    if ( typeof wpcf7.initForm === 'function' ) {
                        wpcf7.initForm( $('.wpcf7-form') );
                    }

                    // For CF7 5.4 and after
                    if ( typeof wpcf7.init === 'function' ) {
                        document.querySelectorAll(".wpb-pcf-wpcf7-form > .wpcf7 > form").forEach(function (e) {
                            wpcf7.init(e);
                        });
                    }

                    // Add support for - Simple Cloudflare Turnstile – CAPTCHA Alternative
                    if( typeof turnstile  !== "undefined" ){

                        var cf_turnstile_id = $($.parseHTML(res)).find('.cf-turnstile').attr('id');
                        if (document.getElementById(cf_turnstile_id)) {
                            setTimeout(function() {
                                turnstile.render('#' + cf_turnstile_id);
                            }, 10);
                        }
                    }

                    // Add support for - Drag and Drop Multiple File Upload – Contact Form 7
                    if( typeof initDragDrop === "function" ){
                        window.initDragDrop();
                    }

                    // ReCaptcha v2 for Contact Form 7 - By IQComputing
                    if( typeof recaptchaCallback === "function" ){
                        recaptchaCallback();
                    }

                    // Add support for - Conditional Fields for Contact Form 7
                    if ( typeof wpcf7cf !== 'undefined' ) {
                        wpcf7cf.initForm( $('.wpcf7-form') );
                    }

                    // Add post ID to the popup form
                    $("[name='_wpcf7_container_post']").val( post_id );

                    // Country & Phone Field Contact Form 7 by Narinder Singh Bisht
                    wpbPcfCountryAndPhoneField();

                    // WP Armour – Honeypot Anti Spam By Dnesscarkey.
                    if (typeof wpa_add_honeypot_field == 'function') {
                        wpa_add_honeypot_field(); 
                    }

                    // WP Armour PRO – Honeypot Anti Spam By Dnesscarkey.
                    if (typeof wpae_add_honeypot_field == 'function') { // IF EXTENDED version exists.
                        wpae_add_honeypot_field(); 
                    }

                    // Adding any custom JS code on form init
                    if( typeof wpb_pcf_on_cf7_form_init === "function" ){
                        wpb_pcf_on_cf7_form_init();
                    }

                },
                error: function(error) {
                    alert( error );
                }
            });
        }
    });

})(jQuery);