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

