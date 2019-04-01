<?php
declare(strict_types=1);

/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

namespace rmswing;


class EventLabeler
{
    /** @var array */
    private $labels;
    private $sources;

    public function __construct(array $settings)
    {
        $this->labels = $settings['category_labels'];
        $this->sources = $settings['sources'];
    }

    private function getCateoriesForSource(string $source): ?array {
        $idx = array_search($source, array_column($this->sources, 'id'));
        return $this->sources[$idx]['categories'] ?? null;
    }

    public function getLabelsForSource(string $source): array
    {
        if (!$categories = $this->getCateoriesForSource($source)) {
            return [];
        }

        $out = [];
        foreach ($categories as $cat) {
            if (array_key_exists($cat, $this->labels)) {
                $out[] = $this->labels[$cat];
            }
        }
        return $out;
    }
}
