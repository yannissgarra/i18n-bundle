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
          path: /fr
          locale: fr
          position: 1
        - host: example.com
          locale: en
          position: 2
```

The bundle prepends sane `framework.default_locale`/`framework.translator`/`framework.set_content_language_from_locale` defaults so a fresh app works out of the box, but `enabled_locales` itself has no default — every consuming application must declare it explicitly.

## Usage

### Languages

`\Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface` (backed by `LanguageDependencyInjectionRepository`, built from `enabled_locales`) exposes `findAll()`, `findOneByLocale(string $locale)` (throws `LanguageNotFoundException`), `findOneDefault()` (the first enabled locale) and `localeExists(string $locale): bool`. Each `Language::getName()` uses `Symfony\Component\Intl\Locales`, so region-specific entries get a distinct, region-aware name — `fr_FR` and `fr_CA` both configured side by side come out as "Français (France)" and "Français (Canada)" rather than colliding on a single "Français".

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

The `language_name()` Twig function returns a language's autonym (its name in its own language) from any ISO code, regardless of `enabled_locales` — unlike `language()`, it never fails on a language that isn't configured. It accepts both a plain language code and a full locale (region part is ignored), and returns `null` if the code isn't a valid language:

```twig
{{ language_name('fr') }} {# Français #}
{{ language_name('fr_FR') }} {# Français #}
{{ language_name('fr-FR') }} {# Français #}
```

Pass a second argument to get the name translated into another language instead of its autonym:

```twig
{{ language_name('fr', 'en') }} {# French #}
```

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

If your application serves several sites/locales behind different hosts and/or path prefixes, declare them under `webmunkeez_i18n.sites` (each entry validates that its `locale`, if set, is part of `enabled_locales`). `position` is required, must be at least `1` and must be unique across sites — it's purely a display ordering: `findAll()`/`findAllLocalized()` and the `sites()` Twig function sort by it, from the smallest position to the largest, regardless of declaration order. It has no effect on routing: `findOneByUrl()` (used by `SiteRequestListener` to resolve the current request) always matches against sites in **declaration order**, so the catch-all-last rule below still applies independently of `position`.

```yaml
webmunkeez_i18n:
    sites:
        - host: example.com
          path: /fr
          locale: fr
          position: 1
        - host: example.com
          path: /api # no locale: an unlocalized API site
          position: 2
        - host: example.com
          locale: en
          position: 3
        - host: es.example.com
          locale: es
          position: 4
```

`path` is a plain literal path prefix (not a regex) — a request matches when its URI starts with `path` followed by `/` or the end of the string, so `/api` matches `/api` and `/api/anything` but not `/apiary`. Omitting `path` entirely matches any path on that host (an explicit `path: null` is rejected, same as an empty string — leave the key out instead), so declare the catch-all site for a given host last — regardless of its `position`, which only affects display order.

`host` can be omitted the same way to match any host — useful when every request lands on the same Symfony project regardless of subdomain (e.g. a wildcard DNS setup with `subdomain1.example.com`, `subdomain2.example.com`, etc. all pointing at the same app) and the routing should only depend on the path, not which subdomain was used:

```yaml
webmunkeez_i18n:
    sites:
        - path: /fr
          locale: fr
          position: 1
        - path: /es
          locale: es
          position: 2
        - locale: en # both host and path omitted: matches any host, any path
          position: 3
```

`SiteRequestListener` runs before `LocaleRequestListener` and resolves the current request into either a `\Webmunkeez\I18nBundle\Model\Site` (host + path, no locale) or a `\Webmunkeez\I18nBundle\Model\LocalizedSite` (also `LanguageAwareInterface`) via `\Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface::findOneByUrl()`, throwing `SiteNotFoundException` (converted to a 404) if nothing matches. The resolved site is stored as the `current-site` request attribute, and for a matched `LocalizedSite` the request locale and `current-language` are set immediately — before `LocaleRequestListener` even runs. The listener is a no-op entirely (no site resolution attempted) when no site is configured.

The `sites()` Twig function lists every configured site (`SiteRepositoryInterface::findAll()`) — useful for rendering a language/site switcher:

```twig
{% for site in sites() %}
    <a href="//{{ site.host }}{{ site.path }}">
        {{ site.locale is defined ? language(site.locale).name : site.host }}
    </a>
{% endfor %}
```

### Ago filter

The `ago` Twig filter formats a `\DateTimeInterface` (`\DateTime` or `\DateTimeImmutable`) as a human-readable relative time, translated through the `date_interval.*` message keys. It only accepts dates in the past — a future date throws an `\InvalidArgumentException`:

```twig
{{ post.createdAt|ago }} {# "3 days ago" #}
```

### Exceptions

- `LanguageNotFoundException`, `SiteNotFoundException`, `TranslationNotFoundException` all extend `\Webmunkeez\CQRSBundle\Exception\ModelNotFoundException` (from the required `webmunkeez/cqrs-bundle` dependency), so they're automatically converted to a 404 `NotFoundHttpException` by its `ModelNotFoundExceptionListener`.
