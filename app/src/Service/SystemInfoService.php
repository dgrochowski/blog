<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;

class SystemInfoService
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getSystemInfo(): array
    {
        return [
            'os' => php_uname(),
            'php_version' => phpversion(),
            'postgres_version' => $this->getPostgresVersion(),
            'free_disk_space' => $this->getFreeDiskSpace(),
            'memory_usage' => $this->getMemoryUsage(),
        ];
    }

    private function getPostgresVersion(): string
    {
        // Get PostgreSQL version by executing the query
        $sql = 'SHOW server_version;';
        $result = $this->connection->fetchOne($sql);

        return $result ?: 'N/A';
    }

    private function getFreeDiskSpace(): string
    {
        // Get free disk space in bytes
        $freeSpace = disk_free_space('/');

        // Convert bytes to GB (1 GB = 1024 * 1024 * 1024 bytes)
        $freeSpaceInGB = $freeSpace / 1024 / 1024 / 1024;

        // Format the result to show 2 decimal places
        return number_format($freeSpaceInGB, 2).' GB';
    }

    private function getMemoryUsage(): string
    {
        // Get current memory usage in bytes and convert to human-readable format
        $memoryUsage = memory_get_usage();

        return $this->formatBytes($memoryUsage);
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return sprintf('%s %s', number_format($bytes / pow(1024, $pow), $precision), $units[$pow]);
    }
}
