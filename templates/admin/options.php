<div class="wrap" id="modularity-options">

    <h1><?php _e('Languages', 'wp-content-translator'); ?></h1>

    <form method="post">
        <?php wp_nonce_field('wp-content-translator-options'); ?>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

                <div id="post-body-content" style="display:none;">
                    <!-- #post-body-content -->
                </div>

                <div id="postbox-container-1" class="postbox-container">
                    <div class="postbox">
                        <h2 class="ui-sortable-handle"><?php _e('Save', 'municipio-intranet'); ?></h2>
                        <div class="inside">
                            <div id="major-publishing-actions" style="margin: -7px -12px -12px;">
                                <div id="publishing-action">
                                    <span class="spinner"></span>
                                    <input type="submit" value="<?php _e('Save', 'municipio-intranet'); ?>" class="button button-primary button-large" id="publish" name="publish">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-2" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle" style="cursor:default;"><?php _e('Languages', 'municipio-intranet'); ?></h2>
                        <div class="inside wp-content-translator-admin__manage-languages">
                            <table class="wp-content-translator-admin__table">
                                <thead>
                                    <tr>
                                        <th class="cb"><?php _e('Active', 'wp-content-translator'); ?></th>
                                        <th width="50%"><?php _e('Language', 'wp-content-translator'); ?></th>
                                        <th><?php _e('Identifier', 'wp-content-translator'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="cb"><input type="checkbox" checked disabled></td>
                                        <td><strong><?php _e('Default (WP Setting)', 'wp-content-translator'); ?>:</strong> <?php echo $defaultLang->name; ?></td>
                                        <td><?php echo $defaultLang->code; ?></td>
                                    </tr>

                                    <?php foreach ($installed as $lang) : ?>
                                    <tr>
                                        <td class="cb"><input type="checkbox" name="active-languages[]" value="<?php echo $lang->code; ?>" <?php echo \ContentTranslator\Language::isActive($lang->code) ? 'checked' : ''; ?>></td>
                                        <td><?php echo $lang->name; ?></td>
                                        <td><?php echo $lang->code; ?></td>
                                        <td class="actions"><a href="#" class="submitdelete deletion"><?php _e('Remove'); ?></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-2" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle" style="cursor:default;"><?php _e('Add languages', 'municipio-intranet'); ?></h2>
                        <div class="inside wp-content-translator-admin__manage-languages">
                            <table class="wp-content-translator-admin__table wp-content-translator-admin__table__add-lang">
                                <thead>
                                    <tr>
                                        <th class="cb"><?php _e('Active', 'wp-content-translator'); ?></th>
                                        <th width="50%"><?php _e('Language', 'wp-content-translator'); ?></th>
                                        <th><?php _e('Identifier', 'wp-content-translator'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-template>
                                        <td class="cb"><input type="checkbox" name="newlang[{num}][active]" value="on" checked></td>
                                        <td>
                                            <select name="newlang[{num}][lang]" class="widefat">
                                                <option value=""><?php _e('Select language', 'wp-content-translator'); ?>…</option>
                                                <?php foreach (\ContentTranslator\Language::uninstalled() as $language) : ?>
                                                <option value="<?php echo $language->code; ?>"><?php echo $language->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td data-placeholder="identifier"></td>
                                        <td class="actions"><a href="#" class="submitdelete deletion" data-action="wp-content-translator-remove-row"><?php _e('Remove'); ?></a></td>
                                    </tr>
                                </tbody>
                            </table>

                            <footer class="wp-content-translator-admin__footer-actions">
                                <button type="button" class="button button-primary" data-action="wp-content-translator-new-language"><?php _e('Add language', 'wp-content-translator'); ?></button>
                            </footer>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </form>
</div>
