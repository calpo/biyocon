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
     * Wrap partial html with css definition
     *
     * @param string $partialHtml  Partial html that render() has returned
     * @return string  Whole html
     */
    public static function wrapHtml($partialHtml)
    {
        $html = <<<EOT
﻿<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Differences</title>
        <style type="text/css">
            body {
                background: #fff;
                font-family: Arial;
                font-size: 12px;
            }
            .Differences {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                empty-cells: show;
            }

            .Differences thead th {
                text-align: left;
                border-bottom: 1px solid #000;
                background: #aaa;
                color: #000;
                padding: 4px;
            }
            .Differences tbody th {
                text-align: right;
                background: #ccc;
                width: 4em;
                padding: 1px 2px;
                border-right: 1px solid #000;
                vertical-align: top;
                font-size: 13px;
            }

            .Differences td {
                padding: 1px 2px;
                font-family: Consolas, monospace;
                font-size: 13px;
            }

            .DifferencesSideBySide .ChangeInsert td.Left {
                background: #dfd;
            }

            .DifferencesSideBySide .ChangeInsert td.Right {
                background: #cfc;
            }

            .DifferencesSideBySide .ChangeDelete td.Left {
                background: #f88;
            }

            .DifferencesSideBySide .ChangeDelete td.Right {
                background: #faa;
            }

            .DifferencesSideBySide .ChangeReplace .Left {
                background: #fe9;
            }

            .DifferencesSideBySide .ChangeReplace .Right {
                background: #fd8;
            }

            .Differences ins, .Differences del {
                text-decoration: none;
            }

            .DifferencesSideBySide .ChangeReplace ins, .DifferencesSideBySide .ChangeReplace del {
                background: #fc0;
            }

            .Differences .Skipped {
                background: #f7f7f7;
            }

            .DifferencesInline .ChangeReplace .Left,
            .DifferencesInline .ChangeDelete .Left {
                background: #fdd;
            }

            .DifferencesInline .ChangeReplace .Right,
            .DifferencesInline .ChangeInsert .Right {
                background: #dfd;
            }

            .DifferencesInline .ChangeReplace ins {
                background: #9e9;
            }

            .DifferencesInline .ChangeReplace del {
                background: #e99;
            }

            pre {
                width: 100%;
                overflow: auto;
            }
        </style>
    </head>
    <body>
    {$partialHtml}
    </body>
</html>
EOT;

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
