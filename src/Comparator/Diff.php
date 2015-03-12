<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Comparator;


class Diff
{
    /**
     * @var \Diff
     */
    private $statusDiff;

    /**
     * @var \Diff
     */
    private $headerDiff;

    /**
     * @var \Diff
     */
    private $bodyDiff;

    /**
     * @var \Diff_Renderer_Abstract
     */
    private $renderer;

    /**
     * @param \Diff $statusDiff
     * @param \Diff $headerDiff
     * @param \Diff $bodyDiff
     */
    public function __construct(\Diff $statusDiff, \Diff $headerDiff, \Diff $bodyDiff)
    {
        $this->statusDiff = $statusDiff;
        $this->headerDiff = $headerDiff;
        $this->bodyDiff = $bodyDiff;
        $this->renderer = new \Diff_Renderer_Html_SideBySide();
    }

    /**
     * returns true when any difference exists in status, headers or body.
     *
     * @return bool
     */
    public function hasDifference()
    {
        return $this->hasDifferentStatus()
            || $this->hasDifferentHeader()
            || $this->hasDifferentBody();
    }

    /**
     * @return bool
     */
    public function hasDifferentStatus()
    {
        return !empty($this->statusDiff->getGroupedOpcodes());
    }

    /**
     * @return bool
     */
    public function hasDifferentHeader()
    {
        return !empty($this->headerDiff->getGroupedOpcodes());
    }

    /**
     * @return bool
     */
    public function hasDifferentBody()
    {
        return !empty($this->bodyDiff->getGroupedOpcodes());
    }

    /**
     * @return array  number of lines (added and removed)
     *                  [
     *                      '+' => (added count),
     *                      '-' => (removed count),
     *                  ]
     */
    public function getHeaderSummary()
    {
        return $this->buildSummary($this->headerDiff);
    }

    /**
     * @return array  number of lines (added and removed)
     *                  [
     *                      '+' => (added count),
     *                      '-' => (removed count),
     *                  ]
     */
    public function getBodySummary()
    {
        return $this->buildSummary($this->bodyDiff);
    }

    /**
     * Returns partial html
     *
     * @return string
     */
    public function render()
    {
        $html = '';

        if ($this->hasDifferentHeader()) {
            $html .= $this->headerDiff->render($this->renderer) . PHP_EOL;
        }
        if ($this->hasDifferentBody()) {
            $html .= $this->bodyDiff->render($this->renderer) . PHP_EOL;
        }

        return $html;
    }

    /**
     * @param \Diff_Renderer_Abstract $renderer
     * @return Diff
     */
    public function setRenderer(\Diff_Renderer_Abstract $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    private function buildSummary(\Diff $diff)
    {
        $summary = [
            '+' => 0,
            '-' => 0,
        ];

        $renderer = new \Diff_Renderer_Text_Unified();
        $list = explode("\n", $diff->render($renderer));
        if (empty($list)) {
            return $summary;
        }

        foreach ($list as $line) {
            if (preg_match('/^([-+])/', $line, $matches)) {
                $summary[$matches[1]]++;
            }
        }

        return $summary;
    }
}
