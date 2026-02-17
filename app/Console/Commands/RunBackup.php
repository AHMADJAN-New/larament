<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

final class RunBackup extends Command
{
    protected $signature = 'app:backup {--retention=14 : Number of days to keep backups}';

    protected $description = 'Backup database and storage files';

    public function handle(): int
    {
        $backupDirectory = storage_path('app/backups');
        File::ensureDirectoryExists($backupDirectory);

        $timestamp = now()->format('Ymd_His');

        $databaseBackupPath = $backupDirectory.'/database_'.$timestamp.'.sql';
        $storageBackupPath = $backupDirectory.'/storage_'.$timestamp.'.tar.gz';

        $this->backupDatabase($databaseBackupPath);
        $this->backupStorage($storageBackupPath);
        $this->cleanupOldBackups($backupDirectory, (int) $this->option('retention'));

        $this->info('Backup completed: '.$databaseBackupPath);
        $this->info('Backup completed: '.$storageBackupPath);

        return self::SUCCESS;
    }

    private function backupDatabase(string $databaseBackupPath): void
    {
        $driver = (string) config('database.default');
        $connection = config('database.connections.'.$driver);

        if (! is_array($connection)) {
            $this->warn('Database connection configuration is missing.');

            return;
        }

        match ($driver) {
            'mysql' => $this->dumpMySql($connection, $databaseBackupPath),
            'pgsql' => $this->dumpPostgres($connection, $databaseBackupPath),
            'sqlite' => $this->dumpSqlite($connection, $databaseBackupPath),
            default => $this->warn('Unsupported database driver for automated backup: '.$driver),
        };
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function dumpMySql(array $connection, string $databaseBackupPath): void
    {
        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (string) ($connection['port'] ?? '3306');
        $database = (string) ($connection['database'] ?? '');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($databaseBackupPath),
        );

        $this->runShellCommand($command);
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function dumpPostgres(array $connection, string $databaseBackupPath): void
    {
        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (string) ($connection['port'] ?? '5432');
        $database = (string) ($connection['database'] ?? '');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');

        $command = sprintf(
            'PGPASSWORD=%s pg_dump --host=%s --port=%s --username=%s --dbname=%s --no-owner --no-acl > %s',
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($databaseBackupPath),
        );

        $this->runShellCommand($command);
    }

    /**
     * @param  array<string, mixed>  $connection
     */
    private function dumpSqlite(array $connection, string $databaseBackupPath): void
    {
        $path = (string) ($connection['database'] ?? '');

        if (! File::exists($path)) {
            $this->warn('SQLite database file not found: '.$path);

            return;
        }

        File::copy($path, $databaseBackupPath);
    }

    private function backupStorage(string $storageBackupPath): void
    {
        $publicPath = storage_path('app/public');
        $privatePath = storage_path('app/private');

        File::ensureDirectoryExists($publicPath);
        File::ensureDirectoryExists($privatePath);

        $command = sprintf(
            'tar -czf %s -C %s app/public app/private',
            escapeshellarg($storageBackupPath),
            escapeshellarg(storage_path()),
        );

        $this->runShellCommand($command);
    }

    private function cleanupOldBackups(string $backupDirectory, int $retentionInDays): void
    {
        $files = File::files($backupDirectory);

        foreach ($files as $file) {
            if ($file->getMTime() < now()->subDays($retentionInDays)->timestamp) {
                File::delete($file->getPathname());
            }
        }
    }

    private function runShellCommand(string $command): void
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->warn('Backup command failed: '.$process->getErrorOutput());
        }
    }
}
