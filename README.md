# WebmunkeezI18nBundle

This bundle unleashes __internationalization__ on Symfony applications.

## Installation

Use Composer to install this bundle:

```console
$ composer require webmunkeez/i18n-bundle
```

Add the bundle in your application kernel:

```php
// config/bundles.php

return [
    // ...
    Webmunkeez\I18nBundle\WebmunkeezI18nBundle::class => ['all' => true],
    // ...
];
```

## Configuration

```yaml
# config/packages/webmunkeez_i18n.yaml
webmunkeez_i18n:
    enabled_locales: [en, fr, es] # required, at least one, each must be a valid ICU locale

    sites: # optional, see Multi-site below
        - host: example.com
          path: ^\/fr
          locale: fr
        - host: example.com
          path: ^\/
          locale: en
```

The bundle prepends sane `framework.default_locale`/`framework.translator`/`framework.set_content_language_from_locale` defaults so a fresh app works out of the box, but `enabled_locales` itself has no default — every consuming application must declare it explicitly.

## Usage

### Languages

`\Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface` (backed by `LanguageDependencyInjectionRepository`, built from `enabled_locales`) exposes `findAll()`, `findOneByLocale(string $locale)` (throws `LanguageNotFoundException`), `findOneDefault()` (the first enabled locale) and `localeExists(string $locale): bool`.

`LocaleRequestListener` runs on every request: if a `_locale` query parameter is present and enabled, it becomes the request locale and a `\Webmunkeez\I18nBundle\Model\Language` is stored as the `current-language` request attribute; otherwise it falls back to whatever `current-language` is already set (e.g. by the multi-site listener below) or to `findOneDefault()`.

The `#[Webmunkeez\I18nBundle\Validator\Constraint\Locale]` constraint validates that a string property is one of the enabled locales:

```php
final class PostTranslation
{
    #[Webmunkeez\I18nBundle\Validator\Constraint\Locale]
    private string $locale;
}
```

### Language-aware objects

Any translation-like class can implement `\Webmunkeez\I18nBundle\Model\LanguageAwareInterface` (`getLocale()` + `getLanguage()`/`setLanguage()`) to get its `Language` resolved and cached lazily:

```php
final class PostTranslation implements Webmunkeez\I18nBundle\Model\LanguageAwareInterface
{
    private string $locale;

    private ?Webmunkeez\I18nBundle\Model\Language $language = null;

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getLanguage(): ?Webmunkeez\I18nBundle\Model\Language
    {
        return $this->language;
    }

    public function setLanguage(?Webmunkeez\I18nBundle\Model\Language $language): static
    {
        $this->language = $language;

        return $this;
    }
}
```

The `language()` Twig function resolves it (and caches the result back onto the object, so it's only looked up once):

```twig
{{ language(translation).name }}
```

It also accepts a raw locale string directly — useful when you don't have a `LanguageAwareInterface` object at hand (this call is never cached, since there's nowhere to store the result):

```twig
{{ language(app.request.locale).name }}
```

`\Webmunkeez\I18nBundle\Serializer\Normalizer\LanguageAwareNormalizer` does the same resolution automatically when serializing any `LanguageAwareInterface` object.

### Translations

A translatable entity implements `\Webmunkeez\I18nBundle\Model\TranslationAwareInterface` (`getTranslations()`/`getTranslation(string $locale)`, throwing `TranslationNotFoundException`) over a collection of `\Webmunkeez\I18nBundle\Model\TranslationInterface` (itself just `LocaleAwareInterface`, typically also implementing `LanguageAwareInterface`):

```php
final class Post implements Webmunkeez\I18nBundle\Model\TranslationAwareInterface
{
    /** @var PostTranslation[] */
    private array $translations = [];

    public function getTranslations(): iterable
    {
        return $this->translations;
    }

    public function addTranslation(Webmunkeez\I18nBundle\Model\TranslationInterface $translation): self
    {
        $this->translations[] = $translation;

        return $this;
    }

    public function getTranslation(string $locale): Webmunkeez\I18nBundle\Model\TranslationInterface
    {
        foreach ($this->translations as $translation) {
            if ($locale === $translation->getLocale()) {
                return $translation;
            }
        }

        throw new Webmunkeez\I18nBundle\Exception\TranslationNotFoundException();
    }
}
```

### TranslatorAware

`\Webmunkeez\I18nBundle\Translation\TranslatorAwareInterface`/`TranslatorAwareTrait` give any service `$this->trans()` and `$this->getTranslatorCatalogue()`, wired automatically by an idempotent compiler pass (it only injects the translator if you haven't already configured your own `setTranslator()` call on that service, so your own explicit wiring is never silently overridden):

```php
final class PostNotifier implements Webmunkeez\I18nBundle\Translation\TranslatorAwareInterface
{
    use Webmunkeez\I18nBundle\Translation\TranslatorAwareTrait;

    public function notify(): string
    {
        return $this->trans('post.created');
    }
}
```

### Multi-site

If your application serves several sites/locales behind different hosts and/or path prefixes, declare them under `webmunkeez_i18n.sites` (each entry validates that its `locale`, if set, is part of `enabled_locales`):

```yaml
webmunkeez_i18n:
    sites:
        - host: example.com
          path: ^\/fr
          locale: fr
        - host: example.com
          path: ^\/api # no locale: an unlocalized API site
        - host: example.com
          path: ^\/
          locale: en
        - host: es.example.com
          path: ^\/
          locale: es
```

`SiteRequestListener` runs before `LocaleRequestListener` and resolves the current request into either a `\Webmunkeez\I18nBundle\Model\Site` (host + path, no locale) or a `\Webmunkeez\I18nBundle\Model\LocalizedSite` (also `LanguageAwareInterface`) via `\Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface::findOneByUrl()`, throwing `SiteNotFoundException` (converted to a 404) if nothing matches. The resolved site is stored as the `current-site` request attribute, and for a matched `LocalizedSite` the request locale and `current-language` are set immediately — before `LocaleRequestListener` even runs. The listener is a no-op entirely (no site resolution attempted) when no site is configured.

### Ago filter

The `ago` Twig filter formats a `\DateTime` as a human-readable relative time, translated through the `date_interval.*` message keys:

```twig
{{ post.createdAt|ago }} {# "3 days ago" #}
```

### Exceptions

- `LanguageNotFoundException`, `SiteNotFoundException`, `TranslationNotFoundException` all extend `\Webmunkeez\CQRSBundle\Exception\ModelNotFoundException` (from the required `webmunkeez/cqrs-bundle` dependency), so they're automatically converted to a 404 `NotFoundHttpException` by its `ModelNotFoundExceptionListener`.
