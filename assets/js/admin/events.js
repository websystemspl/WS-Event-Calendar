jQuery(document).ready(function () {
    if (jQuery('#wsec_start_event_date').length > 0) {
        if (jQuery('#wsec_start_event_date').val()) {
            jQuery('#wsec_end_event_date').attr("min", jQuery('#wsec_start_event_date').val());
        }
        jQuery('#wsec_start_event_date').on("change", function () {
            jQuery('#wsec_end_event_date').attr("min", jQuery(this).val());
            alert(jQuery('#wsec_end_event_date').val());
            if (jQuery('#wsec_end_event_date').val() < jQuery(this).val()) {
                jQuery('#wsec_end_event_date').val("");
            }
        })
    }
});