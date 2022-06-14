<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmunkeez\I18nBundle\Exception\LanguageNotFoundException;
use Webmunkeez\I18nBundle\Model\LanguageAwareInterface;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LanguageAwareNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private LanguageRepositoryInterface $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param LanguageAwareInterface $object
     *
     * @throws LanguageNotFoundException
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        // avoid circular reference
        $context[self::class.'.already_called'] = true;

        $object->setLanguage($this->languageRepository->findOneByLocale($object->getLocale()));

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        // avoid circular reference
        if (
            true === array_key_exists(self::class.'.already_called', $context)
            && true === $context[self::class.'.already_called']
        ) {
            return false;
        }

        return $data instanceof LanguageAwareInterface;
    }
}
