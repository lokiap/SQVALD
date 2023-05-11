<?php

namespace App\Twig;

use App\Entity\Document;
use App\Entity\Event;
use App\Entity\News;
use App\Entity\User;
use App\Entity\Video;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
			new TwigFunction('pluralize', [$this, 'doSomething']),
			new TwigFunction('shuffle', [$this, 'shuffle']),
			new TwigFunction('isDocument', [$this, 'isDocument']),
			new TwigFunction('isEvent', [$this, 'isEvent']),
			new TwigFunction('isNews', [$this, 'isNews']),
			new TwigFunction('isVideo', [$this, 'isVideo']),
			new TwigFunction('isUser', [$this, 'isUser']),
        ];
    }

    public function doSomething(int $count, string $singular, ?string $plural =null): string
    {
        $plural = $plural ?? $singular . 's';
        $str = $count === 1 ? $singular : $plural;
        return "$count $str";
    }

	public function shuffle(array $array): array
	{
		shuffle($array);
		return $array;
	}

	public function isDocument($var): bool
	{
		return $var instanceof Document;
	}

	public function isEvent($var): bool
	{
		return $var instanceof Event;
	}

	public function isNews($var): bool
	{
		return $var instanceof News;
	}

	public function isVideo($var): bool
	{
		return $var instanceof Video;
	}

	public function isUser($var): bool
	{
		return $var instanceof User;
	}
}
