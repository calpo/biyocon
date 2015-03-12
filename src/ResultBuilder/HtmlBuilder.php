<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\ResultBuilder;


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

        $this->createAssetFiles();
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
     * Returns result html file
     *
     * @return string
     */
    public function getHtmlFile()
    {
        return $this->indexHtmlFile;
    }
    /**
     * Create CSS, JS, etc in result directory
     */
    protected function createAssetFiles()
    {
        $this->copyFile('php-diff-style.css');
        $this->copyFile('result.css');
        $this->copyFile('jquery.js');
        $this->copyFile('application.js');
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
        $message = 'Same';
        if ($result->getDiff()->hasDifference()) {
            $class = 'has-diff';
            $message = 'Diff';
        }

        $urlA = Util::h($result->getRequestA()->getUrl());
        $urlB = Util::h($result->getRequestB()->getUrl());

        $partialStatus = $this->buildPartialHtmlStatus($result);
        $partialHeader = $this->buildPartialHtmlHeader($result);
        $partialBody = $this->buildPartialHtmlBody($result);
        $detail = $result->getDiff()->render();

        return <<<EOT
<div class="diff-item">
    <table class="summary $class">
        <tr>
            <th class="message">$message</td>
            <td class="diff-count">
            $partialStatus
            $partialHeader
            $partialBody
            </td>
            <td class="url">$urlA</td>
            <td class="url">$urlB</td>
        </tr>
    </table>
    <div class="diff-detail">$detail</div>
</div>

EOT;
    }

    protected function buildPartialHtmlStatus(Result $result)
    {
        if (!$result->getDiff()->hasDifferentStatus()) {
            return '';
        }

        $statusA = Util::h($result->getResponseA()->getStatus());
        $statusB = Util::h($result->getResponseB()->getStatus());

        return <<<EOT
Status
<span class="added">$statusA</span>
: <span class="removed">$statusB</span>
<br>

EOT;
    }

    protected function buildPartialHtmlHeader(Result $result)
    {
        if (!$result->getDiff()->hasDifferentHeader()) {
            return '';
        }

        $summary = $result->getDiff()->getHeaderSummary();

        return $this->buildPartialSummaryColumn($summary, 'Header');
    }

    protected function buildPartialHtmlBody(Result $result)
    {
        if (!$result->getDiff()->hasDifferentBody()) {
            return '';
        }

        $summary = $result->getDiff()->getBodySummary();

        return $this->buildPartialSummaryColumn($summary, 'Body');
    }

    protected function buildPartialSummaryColumn($summary, $title)
    {
        return <<<EOT
$title
<span class="added">+{$summary['+']}</span>
: <span class="removed">-{$summary['-']}</span>
<br>

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
