<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class DateTimeExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'getAgo']),
        ];
    }

    public function getAgo(\DateTime $date): string
    {
        $interval = (new \DateTime())->diff($date);

        if ($interval->y > 0) {
            return $this->translator->trans('date_interval.year_ago', ['%count%' => $interval->y]);
        }

        if ($interval->m > 0) {
            return $this->translator->trans('date_interval.month_ago', ['%count%' => $interval->m]);
        }

        if ($interval->d >= 7) {
            return $this->translator->trans('date_interval.week_ago', ['%count%' => floor($interval->d / 7)]);
        }

        if ($interval->d > 0) {
            return $this->translator->trans('date_interval.day_ago', ['%count%' => $interval->d]);
        }

        if ($interval->h > 0) {
            return $this->translator->trans('date_interval.hour_ago', ['%count%' => $interval->h]);
        }

        return $this->translator->trans('date_interval.minute_ago', ['%count%' => $interval->i]);
    }
}
