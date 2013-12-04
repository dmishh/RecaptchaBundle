# reCAPTCHA support for Symfony2

## Features

* Easy-to-use
* Service-oriented architecture
* Integration into forms
* Integration into Security Component — for protecting login form with reCAPTCHA
* Independent reCAPTCHA API realization ([Recaptcher](https://github.com/dmishh/Recaptcher) by default)
* Both, PHP and Twig template engines support

## Installation using Composer

1. Add the following to your `composer.json` file:

    ```js
    // composer.json
    {
        // ...
        "require": {
            // ...
            "dmishh/recaptcha-bundle": "1.0.*"
        }
    }
    ```

1. Update dependencies, run from command line:

    ```bash
    php composer.phar update
    ```

1. Register the bundle in your ``AppKernel.php`` file:

    ```php
    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Dmishh\Bundle\RecaptchaBundle\RecaptchaBundle()
    );
    ```

## Configuration

Your reCAPTCHA's public and private keys that can be found at your [recaptcha admin page](https://www.google.com/recaptcha/admin/list).
Configure RecaptchaBundle in your `config.yml`:

``` yaml
# ...

recaptcha:
    public_key: 6LeJg90SAAAAAA9yk0zeNrF8QKaxqR_bV_9SNLz9
    private_key: 6LeJg90SAAAAAEuTLEbZuymhkigzzPm2_wsSdA8j
    use_https: false # optional
```

## Usage in forms

Add the following line to create the reCAPTCHA field:

``` php
<?php

// your form ...

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', 'recaptcha');
}
```

You can pass extra options to reCAPTCHA with the *widget_options* option:

``` php
<?php

// your form ...

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', 'recaptcha', array(
        'widget_options' => array(
            'theme' => 'clean'
        )
    ));
}
```

List of valid options:
* theme
* lang
* custom_translations
* custom_theme_widget
* tabindex

Visit [Customizing the Look and Feel of reCAPTCHA](https://developers.google.com/recaptcha/docs/customization) for the details of customization.

### Validation

`RecaptchaType` has built-in validator, you don't need to do anything more. Just use it!

But if you need to **disable** validation for some reasons, then remove existing validator:

``` php
<?php

// your form ...

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', 'recaptcha', array(
        // only for disabling validation
        'constraints'   => array()
    ));
}
```

## Usage in login form

You need to define `RecaptchaFormAuthenticationListener` as default listener for form authentication in `services.yml`:

``` yaml
# ...

security.authentication.listener.form:
    class: Dmishh\Bundle\RecaptchaBundle\Security\Firewall\RecaptchaFormAuthenticationListener
    parent: security.authentication.listener.abstract
    calls:
        - [setRecaptcha, [@recaptcha]]
    abstract: true
```

Second parameter in *setRecaptcha* method is used for disabling validator.
This might be useful when you need to disable check in dev environment. For example:

``` yaml
security.authentication.listener.form:
    class: Dmishh\Bundle\RecaptchaBundle\Security\Firewall\RecaptchaFormAuthenticationListener
    parent: security.authentication.listener.abstract
    calls:
        - [setRecaptcha, [@recaptcha, %kernel.debug%]]
    abstract: true
```

Now you need to add recaptcha field to your form. If you are using your own form type, than add recaptcha field to your form as described [above](#Usage in forms).

Otherwise, add those lines to your Controller which renders login form and to corresponding Twig template:

``` php
<?php

// your controller ...

public function yourAction(Request $request) {
    // ...
    $recaptcha = $this->createForm($this->get('form.type.recaptcha'));

    return array(
        // ...
        'recaptcha' => $recaptcha->createView()
    );
}
```

``` jinja
<!-- template -->
<label for="recaptcha_response_field">Captcha:</label>
{{ form_widget(recaptcha) }}
```

This documentation is based on [EWZRecaptchaBundle's docs](https://github.com/excelwebzone/EWZRecaptchaBundle).

### Roadmap

#### 1.0.* (dev)

* Docs about manual installation and usage reCAPTCHA with ajax

#### 1.0

* Initial version
