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








## Filters

#### wp-content-translator/admin_bar/current_lang
Filters the name of the current language in the admin bar.

- ```@param string $language``` - The key of the styleguide theme

```php
function my_admin_bar_current_lang($language, $code) {
    if ($code === 'sv_SE') {
        return 'Skånska';
    }

    return $language;
}
add_filter('wp-content-translator/admin_bar/current_lang', 'my_admin_bar_current_lang', 10, 2);
```








## Actions

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


