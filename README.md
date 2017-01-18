# Wp Content Translator

Minimalistic content translation in WordPress.

## Language selector

You can easily use the default language selector (a basic html ```<select>```element) or create a language selector from custom markup.

Use the ```wp_content_translator_language_selector()``` function to display a language selector. If you want to use the default selector just run the function without any paramters. If you want to create a custom selector there's an option to pass markup for the wrapper and the "language rows".

Available template tags to use in the markup:

**Wrapper:**

- *string* languages (displays the language rows)

**Language row:**

- *string* code (sv_SE)
- *string* name (Swedish)
- *string* nativeName (Svenska)
- *string* url (//domain.tld/?lang=sv_SE)
- *string* isCurrent ("is-current" if it's the current language, otherwise empty string)

```php
wp_content_translator_language_selector(
    $wrapper = '<ul>{{ languages }}</ul>',
    $element = '<li><a href="{{ url }}" class="{{ isCurrent }}">{{ name }}</a></li>'
)
```

## Hooks

### Admin menu

#### ```wp-content-translator/before_add_admin_menu_item```
**Action.** Runs before we add the language item to the admin menu.

#### ```wp-content-translator/after_add_admin_menu_item```
**Action.** Runs after we have added the language item to the admin menu.

### Admin Bar

#### ```wp-content-translator/meta/admin_bar_current_lang```
**Filter.** Current language in admin bar. 

**Params:**

- *string* $display: The string that is displayed. 
- *Language* $lang: Current language code (ex: sv_SE).

### Install language

#### ```wp-content-translator/before_install_language```
**Action:** Runs before language is installed.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language object

#### ```wp-content-translator/after_install_language```
**Action:** Runs before language is installed.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language object

#### ```wp-content-translator/should_download_wp_translation_when_installing```
**Filter:** Wheateer or not to download WP translation when installing a new language.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language object

### Uninstall language

#### ```wp-content-translator/before_uninstall_language```
**Action.** Runs before language removal.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language object

#### ```wp-content-translator/after_uninstall_language```
**Action.** Runs after language removal but before completion redirect.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language objec

#### ```wp-content-translator/should_drop_table_when_uninstalling_language```
**Filter.** Tells if tables of the deleted language should be dropped or not.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language objec

#### ```wp-content-translator/redirect_after_uninstall_language```
**Filter.** Url to redirect to after language is removed.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language objec

## Options

#### ```wp-content-translator/option/before_update_option```
**Action.** Runs right before an option is updated.

**Params:**

- *string* $option: Option name
- *mixed* $value: Option value

#### ```wp-content-translator/option/after_update_option```
**Action.** Runs right after an option is updated.

**Params:**

- *string* $option: Option name
- *mixed* $value: Option value

#### ```wp-content-translator/option/should_translate_default```
**Filter.** The default return value for the should translate function.

















stdClass Object
(
    [general] => stdClass Object
        (
            [translate_fallback] => 1
            [translate_delimeter] => _
        )

    [post] => stdClass Object
        (
            [translate_posts] => 1
            [untranslatable_post_types] => Array
                (
                    [0] => revision
                )

        )

    [meta] => stdClass Object
        (
            [translate_meta] => 1
            [translate_hidden_meta] => 
            [translate_numeric_meta] => 
            [untranslatable_meta] => Array
                (
                    [0] => _edit_lock
                    [1] => _edit_last
                    [2] => _wp_page_template
                    [3] => nickname
                    [4] => first_name
                    [5] => last_name
                    [6] => rich_editing
                    [7] => comment_shortcuts
                    [8] => admin_color
                    [9] => show_admin_bar_front
                    [10] => show_welcome_panel
                    [11] => session_tokens
                    [12] => closedpostboxes_page
                    [13] => metaboxhidden_page
                    [14] => closedpostboxes_post
                    [15] => metaboxhidden_post
                    [16] => modularity-modules
                    [17] => modularity-sidebar-options
                )

            [translatable_meta] => Array
                (
                    [0] => _aioseop_title
                    [1] => _aioseop_description
                )

        )

    [option] => stdClass Object
        (
            [translate_option] => 1
            [translate_numeric_option] => 
            [translate_hidden_option] => 
            [untranslatable_option] => Array
                (
                    [0] => wp-content-translator-active
                    [1] => wp-content-translator-installed
                    [2] => siteurl
                    [3] => home
                    [4] => users_can_register
                    [5] => permalink_structure
                    [6] => rewrite_rules
                    [7] => active_plugins
                    [8] => template
                    [9] => stylesheet
                    [10] => theme_switched
                    [11] => html_type
                    [12] => default_role
                    [13] => default_comments_page
                    [14] => comment_order
                    [15] => WPLANG
                    [16] => cron
                    [17] => nestedpages_posttypes
                    [18] => nestedpages_version
                    [19] => nestedpages_menusync
                    [20] => modularity-options
                    [21] => acf_version
                )

            [translatable_option] => Array
                (
                )

            [translate_site_option] => 1
            [translate_numeric_site_option] => 
            [translate_hidden_site_option] => 
            [translatable_site_option] => Array
                (
                )

            [untranslatable_site_option] => Array
                (
                )

        )

    [user] => stdClass Object
        (
            [translate_user] => 1
            [translate_hidden_meta] => 
            [translate_numeric_meta] => 
            [untranslatable_meta] => Array
                (
                    [0] => nickname
                    [1] => first_name
                    [2] => last_name
                    [3] => rich_editing
                    [4] => comment_shortcuts
                    [5] => admin_color
                    [6] => show_admin_bar_front
                    [7] => show_welcome_panel
                    [8] => session_tokens
                    [9] => closedpostboxes_page
                    [10] => metaboxhidden_page
                    [11] => closedpostboxes_post
                    [12] => metaboxhidden_post
                )

            [translatable_meta] => Array
                (
                )

        )

    [comment] => stdClass Object
        (
            [translate_comment] => 1
            [translate_comment_meta] => 1
        )

)
-

