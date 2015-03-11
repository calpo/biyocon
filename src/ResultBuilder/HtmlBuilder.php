<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\ResultBuilder;


use Biyocon\Comparator\Result;

class HtmlBuilder
{
    /**
     * @var array  list of \Biyocon\Comparator\Result
     */
    private $list = [];

    private $directory;
    private $summaryFile;

    /**
     * @param string $directory  path to directory where html should be created
     */
    public function __construct($directory)
    {
        if (empty($directory)) {
            throw new \RuntimeException('result directory required');
        }
        if (!is_dir($directory)) {
            throw new \RuntimeException("[$directory] not found or not a directory");
        }

        $this->directory = $directory;
        $this->summaryFile = $this->directory . '/index.html';
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
     */
    public function build()
    {
        $this->clearDirectory();

        $summary = $this->buildSummary();

        if (file_put_contents($this->summaryFile, $summary) === false) {
            throw new \RuntimeException("failed to create file [{$this->summaryFile}]");
        };
    }

    private function buildSummary()
    {
        $html = '';

        /** @var \Biyocon\Comparator\Result $result */
        foreach ($this->list as $result) {
            if (!$result->getDiff()->hasDifference()) {
                continue;
            }

            $html .= <<<EOT_PARTIAL
<p>
実装中
</p>
EOT_PARTIAL;
        }

        $html = <<<EOT
<html>
    <head>
    </head>
    <body>
    </body>
</html>
EOT;

        return $html;
    }

    private function clearDirectory()
    {
        foreach (glob($this->directory . '/*.html') as $file) {
            if (!unlink($file)) {
                throw new \RuntimeException("failed to delete file [{$file}]");
            }
        }
    }
}
