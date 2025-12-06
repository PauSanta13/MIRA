<?php
/* Replaces the default "---" when you choose include_blank */
function ov3rfly_replace_include_blank($name, $text, &$html) {
    $matches = false;
    preg_match('/<select name="' . $name . '"[^>]*>(.*)<\/select>/iU', $html, $matches);
    if ($matches) {
        $select = str_replace('<option value="">---</option>', '<option value="">' . $text . '</option>', $matches[0]);
        $html = preg_replace('/<select name="' . $name . '"[^>]*>(.*)<\/select>/iU', $select, $html);
    }
}
/* Hooking custom replacements on cf7: */
function pegasus_form_elements($html) {
    // Replace --- for -Select Enquiry type:
    ov3rfly_replace_include_blank('enquiry-type', '-Select Enquiry Type*', $html);
    // Replace the submit input tag for a submit button:
    $html = preg_replace('/<input ([^>]*)value="([^"]*)" ([^>]*)id="btnsubmit"[ \/]*>/', '<button $1 $3>$2</button>', $html);
    // Replace the double quote for underlines:
    $html = preg_replace('/«([\wáéíóú\s]+)»/', '<u>$1</u>', $html);
    // Make subscription checkbox checked by default:
    $html = str_replace('value="I agree to', 'checked value="I agree to', $html);
    return $html;
}
add_filter('wpcf7_form_elements', 'pegasus_form_elements');


/* Cleaning form url from ajax parameters: */
function pegasus_form_action_url($url) {
    $url = preg_replace('/[?|&]callback=jQuery([\d_=&;]+)/i', '',$url);
    return $url;
}
add_filter('wpcf7_form_action_url', 'pegasus_form_action_url');

/* Custom ajax loader */
function pegasus_wpcf7_ajax_loader () {
    return  get_bloginfo('url') . '/wp-admin/images/spinner-2x.gif';
}
add_filter('wpcf7_ajax_loader', 'pegasus_wpcf7_ajax_loader');

/* Dont load wpcf7 stylesheet nor javascript */
//add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );

