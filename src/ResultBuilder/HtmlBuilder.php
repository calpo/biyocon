<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\ResultBuilder;


use Biyocon\Comparator\Diff;
use Biyocon\Comparator\Result;
use Biyocon\Exception\ResultBuildingException;
use Biyocon\Util\Util;

class HtmlBuilder
{
    /**
     * @var array  list of \Biyocon\Comparator\Result
     */
    private $list = [];

    private $directory;
    private $indexHtmlFile;
    private $initialized = false;

    /**
     * @param string $directory  path to directory where html should be created
     */
    public function __construct($baseDirectory, $subDirectory = null)
    {
        if (empty($baseDirectory)) {
            throw new ResultBuildingException('result directory required');
        }
        if (!is_dir($baseDirectory)) {
            throw new ResultBuildingException("[$baseDirectory] not found or not a directory");
        }

        $this->directory = $baseDirectory . '/' . ($subDirectory ?: date('Ymd_His'));
        $this->indexHtmlFile = $this->directory . '/index.html';
    }

    /**
     * Initialize result context
     *
     * this method should be called before crawling.
     * to avoid useless crawling when building result fail.
     */
    public function initialize()
    {
        $directory = $this->getDirectory();
        if (is_file($directory)) {
            throw new ResultBuildingException("file exists (not a directory) [$directory]");
        }
        if (!mkdir($directory)) {
            throw new ResultBuildingException("failed to create result directory [$directory]");
        }

        $this->initialized = true;
    }

    /**
     * @param \Biyocon\Comparator\Result $result
     * @return \Biyocon\ResultBuilder\HtmlBuilder
     */
    public function add(Result $result)
    {
        $this->list[] = $result;
        return $this;
    }

    /**
     * Create result html files
     *
     * Existing file in directory will be removed.
     *
     * @throws \Biyocon\Exception\ResultBuildingException
     * @throws \LogicException
     */
    public function build()
    {
        if (!$this->initialized) {
            throw new \LogicException('call initialize() before build');
        }

        $html = $this->buildWholeHtml();

        if (file_put_contents($this->indexHtmlFile, $html) === false) {
            throw new ResultBuildingException("failed to create file [{$this->indexHtmlFile}]");
        };

        $this->copyFile('php-diff-style.css');
        $this->copyFile('result.css');
        $this->copyFile('jquery.js');
        $this->copyFile('application.js');
    }

    /**
     * Returns result html directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Build whole summary html
     *
     * @return string  html
     */
    protected function buildWholeHtml()
    {
        $partialHtml = '';

        /** @var \Biyocon\Comparator\Result $result */
        foreach ($this->list as $result) {
            $partialHtml .= $this->buildPartialHtml($result);
        }

        return <<<EOT
﻿<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Differences</title>
        <link href="./php-diff-style.css" rel="stylesheet" type="text/css">
        <link href="./result.css" rel="stylesheet" type="text/css">
        <script src="./jquery.js"></script>
        <script src="./application.js"></script>
    </head>
    <body>
    {$partialHtml}
    </body>
</html>
EOT;
    }

    /**
     * @param \Biyocon\Comparator\Result $result
     * @return string
     */
    protected function buildPartialHtml(Result $result)
    {
        $class = 'no-diff';
        if ($result->getDiff()->hasDifference()) {
            $class = 'has-diff';
        }

        $urlA = Util::h($result->getRequestA()->getUrl());
        $urlB = Util::h($result->getRequestB()->getUrl());

        $partialStatus = $this->buildPartialStatusTable($result);
        $partialHeader = $this->buildPartialHeaderTable($result);
        $partialBody = $this->buildPartialBodyTable($result);
        $detail = $result->getDiff()->render();

        return <<<EOT
<div class="diff-item">
    <table class="summary $class">
        <tr class="url">
            <th>URL</td>
            <td>$urlA</td>
            <td>$urlB</td>
        </tr>
        $partialStatus
        $partialHeader
        $partialBody
    </table>
    <div class="diff-detail">
    $detail
    </div>
</div>

EOT;
    }

    private function buildPartialStatusTable(Result $result)
    {
        if (!$result->getDiff()->hasDifferentStatus()) {
            return '';
        }

        $statusA = Util::h($result->getResponseA()->getStatus());
        $statusB = Util::h($result->getResponseB()->getStatus());

        return <<<EOT
<tr class="diff-status">
    <th>Http status</th>
    <td>$statusA</td>
    <td>$statusB</td>
</tr>

EOT;
    }

    private function buildPartialHeaderTable(Result $result)
    {
        if (!$result->getDiff()->hasDifferentHeader()) {
            return '';
        }

        $summary = $result->getDiff()->getHeaderSummary();

        return $this->buildPartialSummaryColumn($summary, 'diff-header', 'header');
    }

    private function buildPartialBodyTable(Result $result)
    {
        if (!$result->getDiff()->hasDifferentBody()) {
            return '';
        }

        $summary = $result->getDiff()->getBodySummary();

        return $this->buildPartialSummaryColumn($summary, 'diff-body', 'body');
    }

    private function buildPartialSummaryColumn($summary, $class, $title)
    {
        return <<<EOT
<tr class="$class">
    <th>$title diff</th>
    <td colspan="2">
        <span class="added">+{$summary['+']}</span>
        : <span class="removed">-{$summary['-']}</span>
    </td>
</tr>

EOT;
    }

    private function copyFile($filename)
    {
        $assetsDir = __DIR__ . '/assets';
        $resultDir = $this->getDirectory();
        $source = "$assetsDir/$filename";
        $dest = "$resultDir/$filename";

        if (!copy($source, $dest)) {
            throw new ResultBuildingException("failed to copy file [$source] to [$dest]");
        }
    }
}
