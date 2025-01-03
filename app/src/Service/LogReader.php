<?php

declare(strict_types=1);

namespace App\Service;

class LogReader
{
    public function __construct(
        private string $logFilePath,
    ) {
    }

    /**
     * @return string[]
     */
    public function getLogs(int $page = 1, int $linesPerPage = 50): array
    {
        $lines = [];
        $startLine = ($page - 1) * $linesPerPage;

        if (!file_exists($this->logFilePath)) {
            throw new \Exception('Log file not found.');
        }

        $fileSize = filesize($this->logFilePath);
        $handle = fopen($this->logFilePath, 'r');
        if ($handle) {
            fseek($handle, $fileSize);
            $currentLine = 0;

            $buffer = '';
            while ($fileSize > 0) {
                $chunkSize = 1024;
                $fileSize -= $chunkSize;
                if ($fileSize < 0) {
                    $chunkSize += $fileSize;
                    $fileSize = 0;
                }

                fseek($handle, $fileSize);
                $buffer = fread($handle, $chunkSize).$buffer;

                $linesInBuffer = explode("\n", $buffer);
                $buffer = array_shift($linesInBuffer); // The last partial line

                foreach (array_reverse($linesInBuffer) as $line) {
                    if ('' !== trim($line)) {
                        ++$currentLine;
                        if ($currentLine > $startLine && count($lines) < $linesPerPage) {
                            $lines[] = trim($line);
                        }
                    }
                }

                if (count($lines) >= $linesPerPage) {
                    break;
                }
            }

            fclose($handle);
        } else {
            throw new \Exception('Unable to open log file.');
        }

        return array_reverse($lines);
    }

    public function getTotalPages(int $linesPerPage = 10): int
    {
        $totalLines = count(file($this->logFilePath));

        return (int) ceil($totalLines / $linesPerPage);
    }
}
