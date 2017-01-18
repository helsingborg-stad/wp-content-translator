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

