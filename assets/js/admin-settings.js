/**
 * Testimonial Slider Admin Settings Repeater & Media Logic
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const listContainer = $('#mts-slides-list');
        const countSpan = $('#mts-slides-count');

        // Update numbering and card indices
        function refreshCardIndices() {
            const cards = listContainer.find('.mts-slide-card');
            countSpan.text(cards.length);

            cards.each(function(idx) {
                const card = $(this);
                card.find('.mts-num-val').text(idx + 1);

                // Enable/disable up and down buttons
                card.find('.mts-move-up-btn').prop('disabled', idx === 0);
                card.find('.mts-move-down-btn').prop('disabled', idx === cards.length - 1);
            });
        }

        // Live update summary header on typing Name or Role
        $(document).on('input', '.mts-name-input', function() {
            const val = $(this).val().trim();
            const card = $(this).closest('.mts-slide-card');
            card.find('.mts-summary-name').text(val ? val : '(Untitled Testimonial)');
        });

        $(document).on('input', '.mts-role-input', function() {
            const val = $(this).val().trim();
            const card = $(this).closest('.mts-slide-card');
            let roleSpan = card.find('.mts-summary-role');
            if (val) {
                if (!roleSpan.length) {
                    card.find('.mts-card-summary').append('<span class="mts-summary-role"> — ' + $('<div>').text(val).html() + '</span>');
                } else {
                    roleSpan.text(' — ' + val);
                }
            } else {
                roleSpan.remove();
            }
        });

        // Toggle Expand / Collapse
        $(document).on('click', '.mts-toggle-card-btn, .mts-card-summary', function(e) {
            e.preventDefault();
            const card = $(this).closest('.mts-slide-card');
            card.toggleClass('is-collapsed');
        });

        // Move Up
        $(document).on('click', '.mts-move-up-btn', function(e) {
            e.preventDefault();
            const card = $(this).closest('.mts-slide-card');
            const prev = card.prev('.mts-slide-card');
            if (prev.length) {
                card.insertBefore(prev);
                refreshCardIndices();
            }
        });

        // Move Down
        $(document).on('click', '.mts-move-down-btn', function(e) {
            e.preventDefault();
            const card = $(this).closest('.mts-slide-card');
            const next = card.next('.mts-slide-card');
            if (next.length) {
                card.insertAfter(next);
                refreshCardIndices();
            }
        });

        // Delete Testimonial
        $(document).on('click', '.mts-remove-slide-btn', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this testimonial?')) {
                const card = $(this).closest('.mts-slide-card');
                card.slideUp(200, function() {
                    $(this).remove();
                    refreshCardIndices();
                    if (!listContainer.find('.mts-slide-card').length) {
                        listContainer.html('<div class="mts-empty-message"><p>No global testimonials added yet. Click "Add New Testimonial" below to get started.</p></div>');
                    }
                });
            }
        });

        // Add New Testimonial
        $('.mts-add-slide-btn').on('click', function(e) {
            e.preventDefault();
            const templateHtml = $('#mts-slide-template').html();
            const uniqueIndex = 'slide_' + Date.now();
            const newCardHtml = templateHtml.replace(/__INDEX__/g, uniqueIndex);

            listContainer.find('.mts-empty-message').remove();
            const newCard = $(newCardHtml);
            listContainer.append(newCard);
            refreshCardIndices();

            // Scroll to the new card
            $('html, body').animate({
                scrollTop: newCard.offset().top - 100
            }, 300);

            newCard.find('.mts-name-input').focus();
        });

        // WP Media Uploader
        let mediaFrame;
        $(document).on('click', '.mts-upload-btn', function(e) {
            e.preventDefault();
            const btn = $(this);
            const parentField = btn.closest('.mts-media-field');
            const inputField = parentField.find('.mts-media-input');
            const previewWrap = parentField.find('.mts-media-preview');
            const title = btn.data('title') || 'Select Image';

            mediaFrame = wp.media({
                title: title,
                button: { text: 'Use this image' },
                multiple: false
            });

            mediaFrame.on('select', function() {
                const attachment = mediaFrame.state().get('selection').first().toJSON();
                inputField.val(attachment.url).trigger('change');

                const thumb = '<div class="mts-thumb-wrap">' +
                    '<img src="' + attachment.url + '" class="mts-thumb-img" alt="Preview"/>' +
                    '<button type="button" class="button-link-delete mts-remove-media">Remove</button>' +
                    '</div>';
                previewWrap.html(thumb);
            });

            mediaFrame.open();
        });

        // Remove Media
        $(document).on('click', '.mts-remove-media', function(e) {
            e.preventDefault();
            const parentField = $(this).closest('.mts-media-field');
            parentField.find('.mts-media-input').val('').trigger('change');
            parentField.find('.mts-media-preview').empty();
        });

        // Copy Shortcode
        $('#mts-copy-sc-btn').on('click', function(e) {
            e.preventDefault();
            const scText = $('#mts-sc-text').text().trim();
            navigator.clipboard.writeText(scText).then(function() {
                const notice = $('#mts-copied-notice');
                notice.fadeIn(200);
                setTimeout(function() {
                    notice.fadeOut(300);
                }, 2500);
            });
        });

        refreshCardIndices();
    });
})(jQuery);
