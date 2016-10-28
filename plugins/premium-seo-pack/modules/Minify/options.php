<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author      Andrei Dinca, AA-Team
 * @version     1.0
 */
 
function __pspMinifyOptions_cache( $action='default', $istab = '', $is_subtab='' ) {
    global $psp;
    
    $req['action'] = $action;

    $notifyStatus = $psp->get_theoption('psp_Minify');

    if ( $req['action'] == 'getStatus' ) {
        if ( $notifyStatus === false || !isset($notifyStatus["cache"]) ) {
            return '';
        }
        return $notifyStatus["cache"]["msg_html"];
    }

    $html = array();
    
    $vals = array('cache_expiration' => '14400');
    foreach ( $vals as $key => $val ) {
        if ( isset($notifyStatus["$key"]) && !empty($notifyStatus["$key"]) ){
            $vals["$key"] = $notifyStatus["$key"];
        }
    }
    
    ob_start();
?>
<div class="psp-form-row psp-minify-cache <?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">

    <label><?php _e('Cache', 'psp'); ?></label>
    <div class="psp-form-item large">
    <span class="formNote">&nbsp;</span>

    <span><?php _e('Expiration:', 'psp'); ?></span>&nbsp;
    <input type="text" style="width: 160px;" name="cache_expiration" id="cache_expiration" value="<?php echo $vals["cache_expiration"]; ?>">&nbsp;
    <span><?php _e('(value in minutes; default = 10 days)', 'psp'); ?></span>&nbsp;
    
    <input type="button" class="psp-button blue" style="width: 160px;" id="psp-minify-cache-delete" value="<?php _e('Clear cache', 'psp'); ?>">
    <span style="margin:0px 0px 0px 10px" class="response"><?php echo __pspMinifyOptions_cache( 'getStatus' ); ?></span>

    </div>
</div>
<?php
    $htmlRow = ob_get_contents();
    ob_end_clean();
    $html[] = $htmlRow;
    
    // view page button
    ob_start();
?>
    <script>
    (function($) {
        var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';
        
        $(document).ready(function() {
            $.post(ajaxurl, {
                'action'        : 'pspMinifyAdminCache',
                'sub_action'    : 'getStatus'
            }, function(response) {

                var $box = $('.psp-minify-cache'), $res = $box.find('.response');
                $res.html( response.msg_html );
                if ( response.status == 'valid' )
                    return true;
                return false;
            }, 'json');
        });

        $("body").on("click", "#psp-minify-cache-delete", function(){

            $.post(ajaxurl, {
                'action'        : 'pspMinifyAdminCache',
                'sub_action'    : 'cache_delete'
            }, function(response) {

                var $box = $('.psp-minify-cache'), $res = $box.find('.response');
                $res.html( response.msg_html );
                if ( response.status == 'valid' )
                    return true;
                return false;
            }, 'json');
        });
    })(jQuery);
    </script>
<?php
    $__js = ob_get_contents();
    ob_end_clean();
    $html[] = $__js;

    return implode( "\n", $html );
}

function __pspMinifyOptions_remote( $action='default', $istab = '', $is_subtab='' ) {
    global $psp;
    
    $html = array();
    
    $notifyStatus = $psp->get_theoption('psp_Minify');

    $vals = array('enable_remote' => 'no', 'remote_username' => '', 'remote_password' => '');
    foreach ( $vals as $key => $val ) {
        if ( isset($notifyStatus["$key"]) && !empty($notifyStatus["$key"]) ){
            $vals["$key"] = $notifyStatus["$key"];
        }
    }
    
    ob_start();
?>
<div class="psp-form-row psp-minify-cache <?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">

    <label><?php _e('Download remote files ', 'psp'); ?></label>
    <p style="margin-top:-5px; color:#7b7b7b;">  This feature is if you have your server under password protection </p>
    <div class="psp-form-item large">
    <span class="formNote">&nbsp;</span>

    <span><?php _e('Enable:', 'psp'); ?></span>&nbsp;
    <select id="enable_remote" name="enable_remote" style="width:60px;">
        <?php
            foreach (array('yes' => __('YES', 'psp'), 'no' => __('NO', 'psp')) as $kk => $vv){
                $vv = (string) $vv;
                echo '<option value="' . ( $kk ) . '" ' . ( $vals["enable_remote"] == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
            } 
        ?>
    </select>&nbsp;&nbsp;

    <span><?php _e('htaccess username:', 'psp'); ?></span>&nbsp;    
    <input type="text" style="width: 160px;" name="remote_username" id="remote_username" value="<?php echo $vals["remote_username"]; ?>">&nbsp;&nbsp;
    <span><?php _e('htaccess password:', 'psp'); ?></span>&nbsp;    
    <input type="text" style="width: 160px;" name="remote_password" id="remote_password" value="<?php echo $vals["remote_password"]; ?>">&nbsp;

    </div>
</div>
<?php
    $htmlRow = ob_get_contents();
    ob_end_clean();
    $html[] = $htmlRow;
    
    return implode( "\n", $html );
}

function __pspMinifyOptions_excludingHtml( $action='default', $istab = '', $is_subtab='' ) {
    global $psp;
    
    $req['action'] = $action;
    
    if ( $req['action'] == 'getStatus' ) {
        $notifyStatus = $psp->get_theoption('psp_Minify');
        if ( $notifyStatus === false || !isset($notifyStatus["exclude"]) ) {
            return '';
        }
        return $notifyStatus["exclude"]["msg_html"];
    }

    $html = array();

    $notifyStatus = $psp->get_theoption('psp_Minify');

    $val = 'no';
    if( isset($notifyStatus['enable_excluding']) && isset($notifyStatus['enable_excluding']) ){
        $val = $notifyStatus['enable_excluding'];
    }
    $val = (string) $val;

    ob_start();
?>
<div class="psp-form-row psp-minify-exclude <?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">

    <label><?php _e('Excluding Assets List', 'psp'); ?></label>
    <div class="psp-form-item large">
    <span class="formNote">&nbsp;</span>
    
    <span><?php _e('Enable:', 'psp'); ?></span>&nbsp;
    <select id="enable_excluding" name="enable_excluding" style="width:60px;">
        <?php
            foreach (array('yes' => __('YES', 'psp'), 'no' => __('NO', 'psp')) as $kk => $vv){
                $vv = (string) $vv;
                echo '<option value="' . ( $kk ) . '" ' . ( $val == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
            } 
        ?>
    </select>&nbsp;&nbsp;

    <input type="button" class="psp-button blue" style="width: 160px;" id="psp-minify-assets-reset" value="<?php _e('Reset', 'psp'); ?>">
    <input type="button" class="psp-button blue" style="width: 160px;" id="psp-minify-assets-refresh" value="<?php _e('Refresh', 'psp'); ?>">
    <span style="margin:0px 0px 0px 10px" class="response"><?php echo __pspMinifyOptions_excludingHtml( 'getStatus' ); ?></span>

    </div>
</div>
<?php
    $htmlRow = ob_get_contents();
    ob_end_clean();
    $html[] = $htmlRow;
    
    // view page button
    ob_start();
?>
    <script>
    (function($) {
        var ajaxurl         = '<?php echo admin_url('admin-ajax.php');?>',
            ajaxurlExc      = '<?php echo admin_url("admin.php?page=psp#Minify#tab:__tab2");?>',
            buttons         = ['psp-minify-assets-reset', 'psp-minify-assets-refresh'];
        
        for (var i in buttons) {
            
            var btnval = buttons[i];
 
            $("body").on("click", "#"+btnval, function(){
 
                $.post(ajaxurl, {
                    'action'        : 'pspMinifyAdminExcluding',
                    'sub_action'    : $(this).prop('id').replace('psp-minify-assets-', '')
                }, function(response) {
    
                    var $box = $('.psp-minify-exclude'), $res = $box.find('.response');
                    $res.html( response.msg_html );

                    if ( response.status == 'valid' ) {
                        //window.location.replace( ajaxurlExc ); // similar behavior as an HTTP redirect
                        window.location.href = ajaxurlExc; // similar behavior as clicking on a link
                        window.location.reload(); // force window refresh
                    }
                }, 'json');
            });
        } // end for
    })(jQuery);
    </script>
<?php
    $__js = ob_get_contents();
    ob_end_clean();
    $html[] = $__js;

    return implode( "\n", $html );
}

function __pspMinifyOptions_excludingAssets($assetsType) {
    global $psp;

    $ret = array();
    $_assetsList = array();

    $assetsList = get_option('psp_Minify_assets', true);
    if ( empty($assetsList) ) {
        return $ret;
    }

    $assetsType = explode(':', $assetsType);    
    $type = $assetsType[0]; $pos = $assetsType[1];
    if ( isset($assetsList["$type"], $assetsList["$type"]["$pos"]) ) {
        $_assetsList = $assetsList["$type"]["$pos"];
    }
    
    foreach ( $_assetsList as $aKey => $aValue ) {
        $ret["$aKey"] = $aKey . ( !empty($aValue) ? ' : '.$aValue.'' : '' );
    }

    //foreach ( array('a' => 'A', 'b' => 'B', 'c' => 'C') as $aKey => $aValue ) {
    //    $ret["$aKey"] = $aValue;
    //}
    return $ret;
}


global $psp;
echo json_encode(
    array(
        $tryed_module['db_alias'] => array(
            /* define the form_messages box */
            'Minify' => array(
                'title'     => __('Minify', 'psp'),
                'icon'      => '{plugin_folder_uri}assets/menu_icon.png',
                'size'      => 'grid_4', // grid_1|grid_2|grid_3|grid_4
                'header'    => true, // true|false
                'toggler'   => false, // true|false
                'buttons'   => true, // true|false
                'style'     => 'panel', // panel|panel-widget
                
                // tabs
                'tabs'  => array(
                    '__tab1'    => array(__('General', 'psp'), 'help_general, enable_minify, enable_gzip, __remote, __cache'),
                    '__tab2'    => array(__('Excluding Assets', 'psp'), 'help_exclude, __exclude, exclude_header_styles, exclude_header_scripts, exclude_footer_scripts'),
                ),

                // create the box elements array
                'elements'  => array(

                    // General
                    'help_general' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">General Settings</h3>
                            <p>General settings: choose what to minify (css, javascript), cache!</p>
                        ', 'psp')
                    ),
                    
                    'enable_minify'    => array(
                        'type'      => 'multiselect_left2right',
                        'std'       => array('css', 'js'),
                        'size'      => 'large',
                        'rows_visible'  => 2,
                        'force_width'=> '300',
                        'title'     => __('Select what to minify', $psp->localizationName),
                        'desc'      => __('Choose what to minify from the list', $psp->localizationName),
                        'info'      => array(
                            'left' => __('Selected items to be minified from the list', $psp->localizationName),
                            'right' => __('The items that you chose to be minified from the list', $psp->localizationName),
                        ),
                        'options'   => array('css' => 'Minify CSS', 'js' => 'Minify JavaScript')
                    ),
                    
                    /*'enable_gzip'    => array(
                        'type'      => 'select',
                        'std'       => 'no',
                        'size'      => 'large',
                        'force_width'=> '70',
                        'title'     => 'Enable Gzip',
                        'desc'      => 'Enable Gzip',
                        'options'   => array(
                            'yes'   => __('YES', 'psp'),
                            'no'    => __('NO', 'psp')
                        )
                    ),*/
                    
                    '__remote' => array(
                        'type' => 'html',
                        'html' => __pspMinifyOptions_remote( 'default', '__tab1', '' )
                    ),
                    
                    '__cache' => array(
                        'type' => 'html',
                        'html' => __pspMinifyOptions_cache( 'default', '__tab1', '' )
                    ),
                    
                    // Exclude
                    'help_exclude' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Excluding Assets</h3>
                            <p>In Order to display all the CSS & JS files from your website, first you need to enable the Excluse Assets List, Next go the frontend of your website and navigate trough some pages, come back and press the Refresh button. If you click Reset button, the exclude assets lists are emptied and you can browse again through your website frontend to refill them.</p>
                        ', 'psp')
                    ),
                    
                    '__exclude' => array(
                        'type' => 'html',
                        'html' => __pspMinifyOptions_excludingHtml( 'default', '__tab2', '' )
                    ),
                    
                    'exclude_header_styles'    => array(
                        'type'      => 'multiselect_left2right',
                        'std'       => array(),
                        'size'      => 'large',
                        'rows_visible'  => 10,
                        'force_width'=> '300',
                        'title'     => __('Select Header Styles', $psp->localizationName),
                        'desc'      => __('Choose Header Styles which you want to exclude.', $psp->localizationName),
                        'info'      => array(
                            'left' => __('All Header Styles list', $psp->localizationName),
                            'right' => __('Your chosen Header Styles from list', $psp->localizationName),
                        ),
                        'options'   => __pspMinifyOptions_excludingAssets('css:header')
                    ),
                    
                    'exclude_header_scripts'    => array(
                        'type'      => 'multiselect_left2right',
                        'std'       => array(),
                        'size'      => 'large',
                        'rows_visible'  => 10,
                        'force_width'=> '300',
                        'title'     => __('Select Header Scripts', $psp->localizationName),
                        'desc'      => __('Choose Header Scripts which you want to exclude.', $psp->localizationName),
                        'info'      => array(
                            'left' => __('All Header Scripts list', $psp->localizationName),
                            'right' => __('Your chosen Header Scripts from list', $psp->localizationName),
                        ),
                        'options'   => __pspMinifyOptions_excludingAssets('js:header')
                    ),
                    
                    'exclude_footer_scripts'    => array(
                        'type'      => 'multiselect_left2right',
                        'std'       => array(),
                        'size'      => 'large',
                        'rows_visible'  => 10,
                        'force_width'=> '300',
                        'title'     => __('Select Footer Scripts', $psp->localizationName),
                        'desc'      => __('Choose Footer Scripts which you want to exclude.', $psp->localizationName),
                        'info'      => array(
                            'left' => __('All Footer Scripts list', $psp->localizationName),
                            'right' => __('Your chosen Footer Scripts from list', $psp->localizationName),
                        ),
                        'options'   => __pspMinifyOptions_excludingAssets('js:footer')
                    ),
                    
                )
            )
        )
    )
);