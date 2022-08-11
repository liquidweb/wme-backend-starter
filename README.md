# WME Backend Starter

## Configuration

If a Card or Wizard class returns a populated array in the `props()` method, the data will be available in the `window[{admin page slug}]["cards"]` or `window[{admin page slug}]["wizards"]` property.

### Example

```php
class Example_Card extends WME_Sparkplug_Card {

  protected $admin_page_slug = 'example';
  protected $card_slug = 'example-card-1';

  public function props(): array {
    return [
      'foo' => 'bar',
    ];
  }

}

new Example_Card;
```

```js
window['example']['cards'][0] === {
  "slug": "example-card-1",
  "foo": "bar"
}
```

### Note
The `cards` property is an array of objects, while the `wizards` property is an object of objects, with the keys being the slugs of the registered wizards: if a wizard's slug is `example-wizard-1` for the `example` admin page, the properties for that wizard will be available at `window['example']['wizards']['example_wizard_1']` (notice that dashes are converted to underscores).

## AJAX

If the `ajax_action` property is defined for a Card or Wizard, then the configuration payload will include an `ajax` property with `nonce` and `url` properties.

### Example

```php
class Example_Card extends WPE_Sparkplug_Card {

  protected $admin_page_slug = 'example';
  protected $card_slug = 'example-card-2';
  protected $ajax_action = 'example-card-2-ajax';

  public function props(): array {
    return [
      'bar' => 'foo',
    ];
  }

}

new Example_Card;
```

```js
window['example']['cards'][0] === {
  "slug": "example-card-2",
  "bar": "foo",
  "ajax": {
    "nonce": "wpnonce",
    "url": "https://example.com/wp-admin/admin-ajax.php?action=example-card-2-ajax",
    "action": "example-card-2-ajax"
  }
}
```
