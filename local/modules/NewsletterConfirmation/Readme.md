# Newsletter Confirmation

Module for Thelia that verifies the email of newsletter subscribers, by sending them an email with a link they have to click in order to confirm their subscription.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is NewsletterConfirmation.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require your-vendor/newsletter-confirmation-module:~1.0
```

## Events

The module listens to and dispatches NEWSLETTER_CONFIRM_SUBSCRIPTION event.

## Hook

The module uses "newsletter.top" hook in order to display a message on newsletter.html after the email link click.

## Other ?

I'm a beginner developper and Thelia user. Use this module at your own risk.
