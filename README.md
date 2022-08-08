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
  "foo": "bar"
}
```

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
  "bar": "foo",
  "ajax": {
    "nonce": "wpnonce",
    "url": "https://example.com/wp-admin/admin-ajax.php?action=example-card-2-ajax"
  }
}
```
