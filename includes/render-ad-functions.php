<?php

/**
 * Render Ad Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Render Ad Functions
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Render the adsense code
 * 
 * @param1 int the ad id 
 * @param2 string $string The adsense code
 * @param3 bool True when function is called from widget
 * 
 * @todo create support for widgets
 * @return string HTML js adsense code
 */
function quads_render_ad( $id, $string, $widget = false ) {
    global $quads_options;

    $string = apply_filters( 'quads_render_ad', $string );

    // Create the global id
    //$id = 'ad' . $ads_id;

    // Return empty string
    if( empty( $id ) ) {
        return '';
    }

    // Return the original ad code if its called from widget
    if( $widget === true && !empty( $string ) ) {
        return $string;
    }

    // Return the original ad code if it's no adsense code
    if( false === quads_is_adsense( $id, $string ) && !empty( $string ) ) {
        return $string;
    }

    // Return the adsense ad code
    if( true === quads_is_adsense( $id, $string ) ) {
        return quads_render_google_async( $id );
    }

    // Return empty string
    return '';

    // Return ad code as it is when wp quads pro is not installed
//    if( !quads_is_advanced() ) {
//        //return quads_render_normal_ad($id, $string);
//        return $string;
//    }
    // Render Google AdSense async ads
    //return quads_render_google_async($id);
}

/**
 * Render non adsense ads
 * 
 * @global array $quads_options
 * @param int $id
 * @return html
 */
//function quads_render_normal_ad( $id, $string ) {
//    global $quads_options;
//
//    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content Ad --> \n\n";
//
//    $html .= '<script type="text/javascript" data-cfasync="false">' . "\n";
//    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";
//
//
//    if( !isset( $quads_options[$id]['desktop'] ) ) {
//        $html .= 'if ( quads_screen_width >= 1140 ) {
///* desktop monitors */
//document.write(\'' . $string . '\');
//}';
//    }
//
//
//    if( !isset( $quads_options[$id]['tablet_landscape'] ) ) {
//        $html .= 'if ( quads_screen_width >= 1019  && quads_screen_width < 1140 ) {
///* landscape tablets */
//document.write(\'' . $string . '\');
//}';
//    }
//
//
//    if( !isset( $quads_options[$id]['tablet_portrait'] ) ) {
//        $html .= 'if ( quads_screen_width >= 768  && quads_screen_width < 1019 ) {
///* portrait tablets */
//document.write(\'' . $string . '\');
//}';
//    }
//
//
//    if( !isset( $quads_options[$id]['phone'] ) ) {
//        $html .= 'if ( quads_screen_width < 768 ) {
///* Phones */
//document.write(\'' . $string . '\');
//}';
//    }
//
//    $html .= '</script>' . "\n";
//
//    $html .= "\n <!-- end WP QUADS --> \n\n";
//
//    return apply_filters( 'quads_render_normal_ad', $html );
//}

/**
 * Render Google async ad
 * 
 * @global array $quads_options
 * @param int $id
 * @return html
 */
function quads_render_google_async( $id ) {
    global $quads_options;

    // Default ad sizes - Option: Auto
    $default_ad_sizes[$id] = array(
        'desktop_width' => '300',
        'desktop_height' => '250',
        'tbl_landscape_width' => '300',
        'tbl_landscape_height' => '250',
        'tbl_portrait_width' => '300',
        'tbl_portrait_height' => '250',
        'phone_width' => '300',
        'phone_height' => '250'
    );

    // Overwrite default values if there are ones
    // Desktop big ad
    if( !empty( $quads_options[$id]['desktop_size'] ) && $quads_options[$id]['desktop_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['desktop_size'] );
        $default_ad_sizes[$id]['desktop_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['desktop_height'] = $ad_size_parts[1];
    }


    //tablet landscape
    if( !empty( $quads_options[$id]['tbl_lands_size'] ) && $quads_options[$id]['tbl_lands_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_lands_size'] );
        $default_ad_sizes[$id]['tbl_landscape_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['tbl_landscape_height'] = $ad_size_parts[1];
    }


    //tablet portrait
    if( !empty( $quads_options[$id]['tbl_portr_size'] ) && $quads_options[$id]['tbl_portr_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_portr_size'] );
        $default_ad_sizes[$id]['tbl_portrait_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['tbl_portrait_height'] = $ad_size_parts[1];
    }


    //phone
    if( !empty( $quads_options[$id]['phone_size'] ) && $quads_options[$id]['phone_size'] !== 'Auto' ) {
        $ad_size_parts = explode( ' x ', $quads_options[$id]['phone_size'] );
        $default_ad_sizes[$id]['phone_width'] = $ad_size_parts[0];
        $default_ad_sizes[$id]['phone_height'] = $ad_size_parts[1];
    }


    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";

    //google async script
    $html .= '<script async data-cfasync="false" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';

    $html .= '<script type="text/javascript" data-cfasync="false">' . "\n";
    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";

    $html .= quads_render_desktop_js( $id, $default_ad_sizes );
    $html .= quads_render_tablet_landscape_js( $id, $default_ad_sizes );
    $html .= quads_render_tablet_portrait_js( $id, $default_ad_sizes );
    $html .= quads_render_phone_js( $id, $default_ad_sizes );

    $html .= '</script>' . "\n";

    $html .= "\n <!-- end WP QUADS --> \n\n";

    return apply_filters( 'quads_render_adsense_async', $html );
}

/**
 * Render Google Ad Code Java Script for desktop devices
 * 
 * @global array $quads_options
 * @param string $id
 * @param array $default_ad_sizes
 * @return string
 */
function quads_render_desktop_js( $id, $default_ad_sizes ) {
    global $quads_options;
    
    $adtype = 'desktop';

    $backgroundcolor = '';

    $responsive_style = 'display:block;' . $backgroundcolor;

    if( quads_is_advanced() && isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ) {
        $width = $default_ad_sizes[$id][$adtype.'_width'];

        $height = $default_ad_sizes[$id][$adtype.'_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' && (isset( $quads_options[$id][$adtype.'_size'] ) && $quads_options[$id][$adtype.'_size'] === 'Auto') ? $responsive_style : $normal_style;
    } else {
        $width = empty( $quads_options[$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id][$adtype.'_width'] : $quads_options[$id]['g_data_ad_width'];

        $height = empty( $quads_options[$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id][$adtype.'_height'] : $quads_options[$id]['g_data_ad_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ? $responsive_style : $normal_style;
    }

    $ad_format = (isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive') && (isset( $quads_options[$id][$adtype.'_size'] ) && $quads_options[$id][$adtype.'_size'] === 'Auto') ? 'data-ad-format="auto"' : '';

    $html = '<ins class="adsbygoogle" style="' . $style . '"';
    $html .= ' data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '"';
    $html .= ' data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '" ' . $ad_format . '></ins>';

    if( !isset( $quads_options[$id][$adtype] ) and ! empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width >= 1140 ) {
/* desktop monitors */
document.write(\'' . $html . '\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
        return $js;
    }
}

/**
 * Render Google Ad Code Java Script for tablet landscape devices
 * 
 * @global array $quads_options
 * @param string $id
 * @param array $default_ad_sizes
 * @return string
 */
function quads_render_tablet_landscape_js( $id, $default_ad_sizes ) {
    global $quads_options;

//
//    $width = empty( $quads_options[$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id]['tbl_landscape_width'] : $quads_options[$id]['g_data_ad_width'];
//
//    $height = empty( $quads_options[$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id]['tbl_landscape_height'] : $quads_options[$id]['g_data_ad_height'];
//
//    $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;
//
//    $responsive_style = 'display:block;';
//
//    $style = isset( $quads_options[$id]['tbl_lands_size'] ) && $quads_options[$id]['tbl_lands_size'] === 'Auto' ? $responsive_style : $normal_style;
//
//    $ad_format = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ? 'data-ad-format="auto"' : '';
    
    $adtype = 'tbl_landscape';
    $adtype_short = 'tbl_lands';

    //$backgroundcolor = 'background-color:white;'; // Pro Version
    $backgroundcolor = '';

    $responsive_style = 'display:block;' . $backgroundcolor;

    if( quads_is_advanced() && isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ) {
        $width = $default_ad_sizes[$id][$adtype.'_width'];

        $height = $default_ad_sizes[$id][$adtype.'_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' && (isset( $quads_options[$id][$adtype_short.'_size'] ) && $quads_options[$id][$adtype_short.'_size'] === 'Auto') ? $responsive_style : $normal_style;
    } else {
        $width = empty( $quads_options[$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id][$adtype.'_width'] : $quads_options[$id]['g_data_ad_width'];

        $height = empty( $quads_options[$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id][$adtype.'_height'] : $quads_options[$id]['g_data_ad_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ? $responsive_style : $normal_style;
    }

    $ad_format = (isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive') && (isset( $quads_options[$id][$adtype_short.'_size'] ) && $quads_options[$id][$adtype_short.'_size'] === 'Auto') ? 'data-ad-format="auto"' : '';


    $html = '<ins class="adsbygoogle" style="' . $style . '"';
    $html .= ' data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '"';
    $html .= ' data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '" ' . $ad_format . '></ins>';

    if( !isset( $quads_options[$id]['tablet_landscape'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width >= 1019  && quads_screen_width < 1140 ) {
/* tablet landscape */
document.write(\'' . $html . '\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
        return $js;
    }
}

/**
 * Render Google Ad Code Java Script for tablet portrait devices
 * 
 * @global array $quads_options
 * @param string $id
 * @param array $default_ad_sizes
 * @return string
 */
function quads_render_tablet_portrait_js( $id, $default_ad_sizes ) {
    global $quads_options;

//    //$backgroundcolor = 'background-color:white;'; // Pro Version
//    $backgroundcolor = '';
//
//    $width = empty( $quads_options[$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id]['tbl_portrait_width'] : $quads_options[$id]['g_data_ad_width'];
//
//    $height = empty( $quads_options[$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id]['tbl_portrait_height'] : $quads_options[$id]['g_data_ad_height'];
//
//    $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;
//
//    $responsive_style = 'display:block;';
//
//    $style = isset( $quads_options[$id]['tbl_portr_size'] ) && $quads_options[$id]['tbl_portr_size'] === 'Auto' ? $responsive_style : $normal_style;
//
//    $ad_format = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ? 'data-ad-format="auto"' : '';
    
    $adtype = 'tbl_portrait';
    
    $adtype_short = 'tbl_portr';

    $backgroundcolor = '';

    $responsive_style = 'display:block;' . $backgroundcolor;

    if( quads_is_advanced() && isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ) {
        $width = $default_ad_sizes[$id][$adtype.'_width'];

        $height = $default_ad_sizes[$id][$adtype.'_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' && (isset( $quads_options[$id][$adtype_short.'_size'] ) && $quads_options[$id][$adtype_short.'_size'] === 'Auto') ? $responsive_style : $normal_style;
    } else {
        $width = empty( $quads_options[$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id][$adtype.'_width'] : $quads_options[$id]['g_data_ad_width'];

        $height = empty( $quads_options[$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id][$adtype.'_height'] : $quads_options[$id]['g_data_ad_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ? $responsive_style : $normal_style;
    }

    $ad_format = (isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive') && (isset( $quads_options[$id][$adtype_short.'_size'] ) && $quads_options[$id][$adtype_short.'_size'] === 'Auto') ? 'data-ad-format="auto"' : '';

    $html = '<ins class="adsbygoogle" style="' . $style . '"';
    $html .= ' data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '"';
    $html .= ' data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '" ' . $ad_format . '></ins>';

    if( !isset( $quads_options[$id]['tablet_portrait'] ) and !empty( $default_ad_sizes[$id]['tbl_portrait_width'] ) and !empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width >= 768  && quads_screen_width < 1019 ) {
/* tablet portrait */
document.write(\'' . $html . '\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
        return $js;
    }
}

/**
 * Render Google Ad Code Java Script for phone devices
 * 
 * @global array $quads_options
 * @param string $id
 * @param array $default_ad_sizes
 * @return string
 */
function quads_render_phone_js( $id, $default_ad_sizes ) {
    global $quads_options;

//    //$backgroundcolor = 'background-color:white;'; // Pro Version
//    $backgroundcolor = '';
//
//    $width = empty( $quads_options[$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id]['phone_width'] : $quads_options[$id]['g_data_ad_width'];
//
//    $height = empty( $quads_options[$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id]['phone_height'] : $quads_options[$id]['g_data_ad_height'];
//
//    $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;
//
//    $responsive_style = 'display:block;';
//
//    $style = isset( $quads_options[$id]['phone_size'] ) && $quads_options[$id]['phone_size'] === 'Auto' ? $responsive_style : $normal_style;
//
//    $ad_format = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ? 'data-ad-format="auto"' : '';
    
    $adtype = 'phone';

    $backgroundcolor = '';

    $responsive_style = 'display:block;' . $backgroundcolor;

    if( quads_is_advanced() && isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ) {
        $width = $default_ad_sizes[$id][$adtype.'_width'];

        $height = $default_ad_sizes[$id][$adtype.'_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' && (isset( $quads_options[$id][$adtype.'_size'] ) && $quads_options[$id][$adtype.'_size'] === 'Auto') ? $responsive_style : $normal_style;
    } else {
        $width = empty( $quads_options[$id]['g_data_ad_width'] ) ? $default_ad_sizes[$id][$adtype.'_width'] : $quads_options[$id]['g_data_ad_width'];

        $height = empty( $quads_options[$id]['g_data_ad_height'] ) ? $default_ad_sizes[$id][$adtype.'_height'] : $quads_options[$id]['g_data_ad_height'];

        $normal_style = 'display:inline-block;width:' . $width . 'px;height:' . $height . 'px;' . $backgroundcolor;

        $style = isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive' ? $responsive_style : $normal_style;
    }

    $ad_format = (isset( $quads_options[$id]['adsense_type'] ) && $quads_options[$id]['adsense_type'] === 'responsive') && (isset( $quads_options[$id][$adtype.'_size'] ) && $quads_options[$id][$adtype.'_size'] === 'Auto') ? 'data-ad-format="auto"' : '';

    $html = '<ins class="adsbygoogle" style="' . $style . '"';
    $html .= ' data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '"';
    $html .= ' data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '" ' . $ad_format . '></ins>';

    if( !isset( $quads_options[$id][$adtype] ) and ! empty( $default_ad_sizes[$id][$adtype.'_width'] ) and ! empty( $default_ad_sizes[$id][$adtype.'_height'] ) ) {
        $js = 'if ( quads_screen_width < 768 ) {
/* phone */
document.write(\'' . $html . '\');
(adsbygoogle = window.adsbygoogle || []).push({});
}';
        return $js;
    }
}

/**
 * Check if ad code is adsense or other ad code
 * 
 * @param string $string ad code
 * @return boolean
 */
function quads_is_adsense( $id, $string ) {
    global $quads_options;

    if( strpos( $string, 'googlesyndication.com' ) !== false ||
            (isset( $quads_options[$id]['ad_type'] ) && $quads_options[$id]['ad_type'] == 'adsense') ) {
        return true;
    }
    return false;
}

/**
 * Check if ad code is valid or empty
 * 
 * @param string $string ad code
 * @return boolean
 */
function quads_is_valid_content( $id, $string ) {
    global $quads_options;

    if( quads_is_adsense( $id, $string ) || !empty( $string ) ) {
        return true;
    }
    return false;
}

/**
 * Render Google normal ad
 * 
 * @global array $quads_options
 * @param int $id
 * @return html
 * 
 * @deprecated since version 1.2.7
 */
//function quads_render_google_normal( $id ) {
//    global $quads_options;
//    
//    // Default ad sizes
//    $default_ad_sizes[$id] = array(
//        'desktop_width' => '300',
//        'desktop_height' => '250',
//        'tbl_landscape_width' => '300',
//        'tbl_landscape_height' => '250',
//        'tbl_portrait_width' => '300',
//        'tbl_portrait_height' => '250',
//        'phone_width' => '300',
//        'phone_height' => '250'
//    );
//
//    // Overwrite default values if there are ones
//    // Desktop big ad
//    if( !empty( $quads_options[$id]['desktop_size'] ) && $quads_options[$id]['desktop_size'] !== 'Auto' ) {
//        $ad_size_parts = explode( ' x ', $quads_options[$id]['desktop_size'] );
//        $default_ad_sizes[$id]['desktop_width'] = $ad_size_parts[0];
//        $default_ad_sizes[$id]['desktop_height'] = $ad_size_parts[1];
//    }
//
//
//    //tablet landscape
//    if( !empty( $quads_options[$id]['tbl_lands_size'] ) && $quads_options[$id]['tbl_lands_size'] !== 'Auto' ) {
//        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_lands_size'] );
//        $default_ad_sizes[$id]['tbl_landscape_width'] = $ad_size_parts[0];
//        $default_ad_sizes[$id]['tbl_landscape_height'] = $ad_size_parts[1];
//    }
//
//
//    //tablet portrait
//    if( !empty( $quads_options[$id]['tbl_portr_size'] ) && $quads_options[$id]['tbl_portr_size'] !== 'Auto' ) {
//        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_portr_size'] );
//        $default_ad_sizes[$id]['tbl_portrait_width'] = $ad_size_parts[0];
//        $default_ad_sizes[$id]['tbl_portrait_height'] = $ad_size_parts[1];
//    }
//
//
//    //phone
//    if( !empty( $quads_options[$id]['phone_size'] ) && $quads_options[$id]['phone_size'] !== 'Auto' ) {
//        $ad_size_parts = explode( ' x ', $quads_options[$id]['phone_size'] );
//        $default_ad_sizes[$id]['phone_width'] = $ad_size_parts[0];
//        $default_ad_sizes[$id]['phone_height'] = $ad_size_parts[1];
//    }
//
//
//    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";
//
//    
//    //google async script
//    $html .= '<script async data-cfasync="false" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
//
//    $html .= '<script type="text/javascript" data-cfasync="false">' . "\n";
//    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";
//
//
//    if( !isset( $quads_options[$id]['desktop'] ) and ! empty( $default_ad_sizes[$id]['desktop_width'] ) and ! empty( $default_ad_sizes[$id]['desktop_height'] ) ) {
//$html .= 'if ( quads_screen_width >= 1140 ) {
///* desktop monitors */
//document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['desktop_width'] . 'px;height:' . $default_ad_sizes[$id]['desktop_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
//(adsbygoogle = window.adsbygoogle || []).push({});
//}';
//    }
//
//
//    if( !isset( $quads_options[$id]['tablet_landscape'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_height'] ) ) {
//$html .= 'if ( quads_screen_width >= 1019  && quads_screen_width < 1140 ) {
///* landscape tablets */
//document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['tbl_landscape_width'] . 'px;height:' . $default_ad_sizes[$id]['tbl_landscape_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
//(adsbygoogle = window.adsbygoogle || []).push({});
//}';
//    }
//
//
//    if( !isset( $quads_options[$id]['tablet_portrait'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_height'] ) ) {
//$html .= 'if ( quads_screen_width >= 768  && quads_screen_width < 1019 ) {
///* portrait tablets */
//document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['tbl_portrait_width'] . 'px;height:' . $default_ad_sizes[$id]['tbl_portrait_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
//(adsbygoogle = window.adsbygoogle || []).push({});
//}';
//    }
//
//    if( !isset( $quads_options[$id]['phone'] ) and ! empty( $default_ad_sizes[$id]['phone_width'] ) and ! empty( $default_ad_sizes[$id]['phone_height'] ) ) {
//$html .= 'if ( quads_screen_width < 768 ) {
///* Phones */
//document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['phone_width'] . 'px;height:' . $default_ad_sizes[$id]['phone_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
//(adsbygoogle = window.adsbygoogle || []).push({});
//}';
//    }
//
//    $html .= '</script>' . "\n";
//
//    $html .= "\n <!-- end WP QUADS --> \n\n";
//    
//    return apply_filters('quads_render_adsense_normal', $html);
//}


//function quads_render_google_async( $id ) {
//    global $quads_options;
//    
//    
//    // Create CSS
//    $bgcolor = 'background-color:#ffffff;';
//    
//    // Default ad sizes - Option: Auto
//    $default_ad_sizes[$id] = array(
//        'desktop_width' => '300',
//        'desktop_height' => '250',
//        'tbl_landscape_width' => '300',
//        'tbl_landscape_height' => '250',
//        'tbl_portrait_width' => '300',
//        'tbl_portrait_height' => '250',
//        'phone_width' => '300',
//        'phone_height' => '250'
//    );
//
//    // Overwrite default values if there are ones
//    // Desktop big ad
//    if( !empty( $quads_options[$id]['desktop_size'] ) && $quads_options[$id]['desktop_size'] !== 'Auto' ) {
//        $ad_size_parts = explode( ' x ', $quads_options[$id]['desktop_size'] );
//        $default_ad_sizes[$id]['desktop_width'] = $ad_size_parts[0];
//        $default_ad_sizes[$id]['desktop_height'] = $ad_size_parts[1];
//    }
//
//
//    //tablet landscape
//    if( !empty( $quads_options[$id]['tbl_lands_size'] ) && $quads_options[$id]['tbl_lands_size'] !== 'Auto' ) {
//        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_lands_size'] );
//        $default_ad_sizes[$id]['tbl_landscape_width'] = $ad_size_parts[0];
//        $default_ad_sizes[$id]['tbl_landscape_height'] = $ad_size_parts[1];
//    }
//
//
//    //tablet portrait
//    if( !empty( $quads_options[$id]['tbl_portr_size'] ) && $quads_options[$id]['tbl_portr_size'] !== 'Auto' ) {
//        $ad_size_parts = explode( ' x ', $quads_options[$id]['tbl_portr_size'] );
//        $default_ad_sizes[$id]['tbl_portrait_width'] = $ad_size_parts[0];
//        $default_ad_sizes[$id]['tbl_portrait_height'] = $ad_size_parts[1];
//    }
//
//
//    //phone
//    if( !empty( $quads_options[$id]['phone_size'] ) && $quads_options[$id]['phone_size'] !== 'Auto' ) {
//        $ad_size_parts = explode( ' x ', $quads_options[$id]['phone_size'] );
//        $default_ad_sizes[$id]['phone_width'] = $ad_size_parts[0];
//        $default_ad_sizes[$id]['phone_height'] = $ad_size_parts[1];
//    }
//
//
//    $html = "\n <!-- " . QUADS_NAME . " v." . QUADS_VERSION . " Content AdSense async --> \n\n";
//
//    //google async script
//    $html .= '<script async data-cfasync="false" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
//
//    $html .= '<script type="text/javascript" data-cfasync="false">' . "\n";
//    $html .= 'var quads_screen_width = document.body.clientWidth;' . "\n";
//
//
//    if( !isset( $quads_options[$id]['desktop'] ) and ! empty( $default_ad_sizes[$id]['desktop_width'] ) and ! empty( $default_ad_sizes[$id]['desktop_height'] ) ) {
//$style='display:inline-block;width:' . $default_ad_sizes[$id]['desktop_width'] . 'px;height:' . $default_ad_sizes[$id]['desktop_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '';
//$html .= 'if ( quads_screen_width >= 1140 ) {
///* desktop monitors */
//document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="'.$style.'"></ins>\');
//(adsbygoogle = window.adsbygoogle || []).push({});
//}';
//    }
//
//
//    if( !isset( $quads_options[$id]['tablet_landscape'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_landscape_height'] ) ) {
//$html .= 'if ( quads_screen_width >= 1019  && quads_screen_width < 1140 ) {
///* landscape tablets */
//document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['tbl_landscape_width'] . 'px;height:' . $default_ad_sizes[$id]['tbl_landscape_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
//(adsbygoogle = window.adsbygoogle || []).push({});
//}';
//    }
//
//
//    if( !isset( $quads_options[$id]['tablet_portrait'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_width'] ) and ! empty( $default_ad_sizes[$id]['tbl_portrait_height'] ) ) {
//$html .= 'if ( quads_screen_width >= 768  && quads_screen_width < 1019 ) {
///* portrait tablets */
//document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['tbl_portrait_width'] . 'px;height:' . $default_ad_sizes[$id]['tbl_portrait_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
//(adsbygoogle = window.adsbygoogle || []).push({});
//}';
//    }
//
//    if( !isset( $quads_options[$id]['phone'] ) and ! empty( $default_ad_sizes[$id]['phone_width'] ) and ! empty( $default_ad_sizes[$id]['phone_height'] ) ) {
//$html .= 'if ( quads_screen_width < 768 ) {
///* Phones */
//document.write(\'' . (!empty( $spot_title ) ? ('<span class="quads-ad-title">' . $spot_title . '</span>') : '') . '<ins class="adsbygoogle" style="display:inline-block;width:' . $default_ad_sizes[$id]['phone_width'] . 'px;height:' . $default_ad_sizes[$id]['phone_height'] . 'px;' . $bgcolor . '" data-ad-client="' . $quads_options[$id]['g_data_ad_client'] . '" data-ad-slot="' . $quads_options[$id]['g_data_ad_slot'] . '"></ins>\');
//(adsbygoogle = window.adsbygoogle || []).push({});
//}';
//    }
//
//    $html .= '</script>' . "\n";
//
//    $html .= "\n <!-- end WP QUADS --> \n\n";
//    
//    return apply_filters('quads_render_adsense_async', $html);
//}