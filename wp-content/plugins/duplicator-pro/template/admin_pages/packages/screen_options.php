<?php

/**
 * @package Duplicator
 */

use Duplicator\Views\UserUIOptions;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$uiOpts = UserUIOptions::getInstance();

$perPage     = $uiOpts->get(UserUIOptions::VAL_PACKAGES_PER_PAGE);
$dateFormat  = $uiOpts->get(UserUIOptions::VAL_CREATED_DATE_FORMAT);
$showNote    = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_NOTE);
$showSize    = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_SIZE);
$showCreated = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_CREATED);
$showAge     = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_AGE);
?>
<fieldset class="metabox-prefs">
        <legend>Columns</legend>
        <label>
            <input 
                class="dup-hide-column-tog" 
                name="dup-note-hide" 
                type="checkbox" 
                id="dup-note-hide" 
                value="1" 
                <?php checked($showNote); ?>
                data-target-colum="dup-note-column"
            >
            <?php esc_html_e('Note', 'duplicator-pro'); ?>
        </label>
        <label>
            <input 
                class="dup-hide-column-tog" 
                name="dup-size-hide" 
                type="checkbox" 
                id="dup-size-hide" 
                value="1" <?php checked($showSize); ?>
                data-target-colum="dup-size-column"
            >
            <?php esc_html_e('Size', 'duplicator-pro'); ?>
        </label>
        <label>
            <input 
                class="dup-hide-column-tog" 
                name="dup-created-hide" 
                type="checkbox" 
                id="dup-created-hide" 
                value="1" 
                <?php checked($showCreated); ?>
                data-target-colum="dup-created-column"
            >
            <?php esc_html_e('Created', 'duplicator-pro'); ?>
        </label>
        <label>
            <input 
                class="dup-hide-column-tog" 
                name="dup-age-hide" 
                type="checkbox" 
                id="dup-age-hide" 
                value="1" 
                <?php checked($showAge); ?>
                data-target-colum="dup-age-column"
            >
            <?php esc_html_e('Age', 'duplicator-pro'); ?>
        </label>
</fieldset>
<fieldset class="screen-options" >
    <legend>Pagination</legend>
    <label for="duplicator_pro_opts_per_page">Packages Per Page</label>
    <input 
        type="number" 
        step="1" 
        min="1" 
        max="999" 
        class="screen-per-page" 
        name="duplicator_pro_opts_per_page" 
        id="duplicator_pro_opts_per_page" 
        maxlength="3" 
        value="<?php echo esc_html($perPage); ?>"
    >
</fieldset>
<fieldset class="screen-options">
    <legend>Created Format</legend>
    <div class="metabox-prefs">
        <input type="hidden" name="wp_screen_options[option]" value="package_screen_options">
        <input type="hidden" name="wp_screen_options[value]" value="val">
        <div class="created-format-wrapper">
            <select name="duplicator_pro_created_format">
            <!-- YEAR -->
            <optgroup label="By Year">
                <option value="1" <?php selected($dateFormat, 1); ?> >Y-m-d H:i &nbsp;  [2000-01-05 12:00]</option>
                <option value="2" <?php selected($dateFormat, 2); ?> >Y-m-d H:i:s       [2000-01-05 12:00:01]</option>
                <option value="3" <?php selected($dateFormat, 3); ?> >y-m-d H:i &nbsp;  [00-01-05   12:00]</option>
                <option value="4" <?php selected($dateFormat, 4); ?> >y-m-d H:i:s       [00-01-05   12:00:01]</option>
            </optgroup>
            <!-- MONTH -->
            <optgroup label="By Month">
                <option value="5" <?php selected($dateFormat, 5); ?> >m-d-Y H:i  &nbsp; [01-05-2000 12:00]</option>
                <option value="6" <?php selected($dateFormat, 6); ?> >m-d-Y H:i:s       [01-05-2000 12:00:01]</option>
                <option value="7" <?php selected($dateFormat, 7); ?> >m-d-y H:i  &nbsp; [01-05-00   12:00]</option>
                <option value="8" <?php selected($dateFormat, 8); ?> >m-d-y H:i:s       [01-05-00   12:00:01]</option>
            </optgroup>
            <!-- DAY -->
            <optgroup label="By Day">
                <option value="9" <?php selected($dateFormat, 9); ?> > d-m-Y H:i &nbsp; [05-01-2000 12:00]</option>
                <option value="10" <?php selected($dateFormat, 10); ?> >d-m-Y H:i:s      [05-01-2000 12:00:01]</option>
                <option value="11" <?php selected($dateFormat, 11); ?> >d-m-y H:i &nbsp; [05-01-00   12:00]</option>
                <option value="12" <?php selected($dateFormat, 12); ?> >d-m-y H:i:s      [05-01-00   12:00:01]</option>
            </optgroup>
        </select>
        </div>
    </div>
</fieldset>
<p class="submit">
    <input type="submit" name="screen-options-apply" id="screen-options-apply" class="button button-primary" value="Apply">
</p>
<script>
    jQuery(document).ready(function($) {
        $('.dup-hide-column-tog').on('change', function() {
            let node = $(this);
            let columns = $('.' + node.data('target-colum'));
            if (node.is(':checked')) {
                columns.show();
            } else {
                columns.hide();
            }
        });
    });
</script>