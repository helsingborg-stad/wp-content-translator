# Wp Content Translator

Minimalistic content translation in WordPress.

## Translations

The GUI is has base language english and is translated to: 

- Swedish

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








## Configurations
Each translation component has it's own configuration filter which can be used to change it's configuration.

```php
function my_custom_config($config) {
    $config['key'] = 'my value';
    return $config;
}
add_filter('wp-content-translator/configuration/{{component}}', 'my_custom_config');
```

#### wp-content-translator/configuration/general

Available configurations:
- *bool* translate_fallback - Fallback to default language or not
- *string* translate_delimeter - Delimeter to use

#### wp-content-translator/configuration/post

Available configurations:
- *bool* translate - Use the component or not

#### wp-content-translator/configuration/[comment/meta/user/option/siteoption]

Available configurations:
- *bool* translate - Use the component or not
- *bool* translate_hidden - Translate options prefixed with underscore or not
- *bool* translate_numeric - Translate numeric values or not
- *array* untranslatable - Array with option keys of untranslatable options
- *array* translatable - Array with option keys of translatable options





## Filters

#### wp-content-translator/should_download_wp_translation_when_installing
Decide if the plugin should download the WP language pack when installing languages.

- ```@param string $answer``` - The default answer
- ```@param string $code``` - The language code
- ```@param \ContentTranslator\Language $language``` - The language object

```php
function my_download_wp_translation(bool $answer, string $code, \ContentTranslator\Language $language) {
    if ($code === 'sv_SE') {
        return false;
    }

    return $answer;
}
add_filter('wp-content-translator/should_download_wp_translation_when_installing', 'my_download_wp_translation', 10, 3);
```

#### wp-content-translator/admin_bar/current_lang
Filters the name of the current language in the admin bar.

- ```@param string $language``` - The name of the language
- ```@param string $code``` - The language code

```php
function my_admin_bar_current_lang(string $language, string $code) {
    if ($code === 'sv_SE') {
        return 'Skånska';
    }

    return $language;
}
add_filter('wp-content-translator/admin_bar/current_lang', 'my_admin_bar_current_lang', 10, 2);
```

#### wp-content-translator/redirect_after_uninstall_language
Where to redirect to after a langauge have been uninstalled.

- ```@param string $url``` - The default redirect url
- ```@param string $code``` - The language code
- ```@param \ContentTranslator\Language $language``` - The language object

```php
function my_after_uninstall_redirect(string $url, string $code, \ContentTranslator\Language $language) {
    return 'http://www.helsingborg.se';
}
add_filter('wp-content-translator/redirect_after_uninstall_language', 'my_after_uninstall_redirect', 10, 2);
```








## Actions

#### wp-content-translator/before_install_language
Runs before a language is installed.

- ```@param string $code``` - The langauge code
- ```@param \ContentTranslator\Language $language``` - The langauge object

```php
function my_before_install_language($code, $language) {
    // Do my stuff
}
add_action('wp-content-translator/before_install_language', 'my_before_install_language', 10, 2);
```

#### wp-content-translator/after_install_language
Runs after a language have been installed.

- ```@param string $code``` - The langauge code
- ```@param \ContentTranslator\Language $language``` - The langauge object

```php
function my_after_install_language($code, $language) {
    // Do my stuff
}
add_action('wp-content-translator/after_install_language', 'my_after_install_language', 10, 2);
```

#### wp-content-translator/before_uninstall_language
Runs before a language is uninstalled.

- ```@param string $code``` - The langauge code
- ```@param \ContentTranslator\Language $language``` - The langauge object

```php
function my_before_uninstall_language($code, $language) {
    // Do my stuff
}
add_action('wp-content-translator/before_uninstall_language', 'my_before_uninstall_language', 10, 2);
```

#### wp-content-translator/after_uninstall_language
Runs after a language is uninstalled.

- ```@param string $code``` - The langauge code
- ```@param \ContentTranslator\Language $language``` - The langauge object

```php
function my_after_uninstall_language($code, $language) {
    // Do my stuff
}
add_action('wp-content-translator/after_uninstall_language', 'my_after_uninstall_language', 10, 2);
```

#### wp-content-translator/admin_bar/before_add_switcher
Runs before language switcher is added to the admin bar.

```php
function my_admin_bar_before() {
    // Do my stuff
}
add_action('wp-content-translator/admin_bar/before_add_switcher', 'my_admin_bar_before', 10);
```

#### wp-content-translator/admin_bar/after_add_switcher
Runs after language switcher have been added to the admin bar.

```php
function my_admin_bar_after() {
    // Do my stuff
}
add_action('wp-content-translator/admin_bar/after_add_switcher', 'my_admin_bar_before', 10);
```

#### wp-content-translator/options/before_add_options_page
Runs right before the "language" options page is added to the admin menu.

```php
function my_options_page_before() {
    // Do my stuff
}
add_action('wp-content-translator/options/before_add_options_page', 'my_options_page_before', 10);
```

#### wp-content-translator/options/aftere_add_options_page
Runs right after the "language" options page have been added to the admin menu.

```php
function my_options_page_after() {
    // Do my stuff
}
add_action('wp-content-translator/options/after_add_options_page', 'my_options_page_after', 10);
```


