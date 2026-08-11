<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ReconcileMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:reconcile
                            {--pretend : Show which migrations would be marked without writing anything}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record pending migrations as already run when their schema changes already exist in the database';

    /**
     * Blueprint methods that define a column (used to detect added columns).
     *
     * @var string
     */
    private const COLUMN_METHODS = 'bigIncrements|bigInteger|binary|boolean|char|date|dateTimeTz|dateTime|decimal|double|enum|float|foreignId|foreignUuid|geometryCollection|geometry|id|increments|integer|ipAddress|jsonb|json|lineString|longText|macAddress|mediumIncrements|mediumInteger|mediumText|multiLineString|multiPoint|multiPolygon|point|polygon|set|smallIncrements|smallInteger|softDeletesTz|softDeletes|string|text|timeTz|time|timestampTz|timestamp|tinyIncrements|tinyInteger|unsignedBigInteger|unsignedInteger|unsignedMediumInteger|unsignedSmallInteger|unsignedTinyInteger|uuid|ulid|year';

    public function handle(): int
    {
        $recorded = DB::table('migrations')->pluck('migration')->flip();

        $pending = [];
        foreach (File::files(database_path('migrations')) as $file) {
            $name = $file->getBasename('.php');

            if (! isset($recorded[$name])) {
                $pending[$name] = $file->getContents();
            }
        }

        if ($pending === []) {
            $this->info('All migrations are already recorded. Nothing to reconcile.');

            return self::SUCCESS;
        }

        $toRecord = [];
        $unresolved = [];

        foreach ($pending as $name => $content) {
            if ($this->alreadyApplied($content)) {
                $toRecord[$name] = true;
            } else {
                $unresolved[$name] = true;
            }
        }

        if ($toRecord === []) {
            $this->warn('No pending migration matches the current schema. Run `php artisan migrate` as normal.');

            return self::SUCCESS;
        }

        $batch = ((int) DB::table('migrations')->max('batch')) + 1;

        foreach (array_keys($toRecord) as $name) {
            if ($this->option('pretend')) {
                $this->line("  <comment>would record</comment> {$name}");
            } else {
                DB::table('migrations')->insert(['migration' => $name, 'batch' => $batch]);
                $this->line("  <info>recorded</info> {$name}");
            }
        }

        $this->info(sprintf('Recorded %d migration(s) as already applied.', count($toRecord)));

        if ($unresolved !== []) {
            $this->warn(sprintf('Left %d migration(s) pending (schema does not fully match):', count($unresolved)));
            foreach (array_keys($unresolved) as $name) {
                $this->line('  - ' . $name);
            }
            $this->line('Run `php artisan migrate` to apply these.');
        }

        return self::SUCCESS;
    }

    /**
     * Determine whether a migration's changes already exist in the database.
     *
     * Supports: create-table and add-column (Schema::table) migrations. Anything
     * it cannot confidently verify is treated as pending.
     */
    private function alreadyApplied(string $content): bool
    {
        $up = $this->upBody($content);

        if ($up === null) {
            return false;
        }

        $found = false;

        foreach ($this->allMatches($up, '/Schema::create\(\s*[\'"]([a-zA-Z0-9_]+)[\'"]/') as $table) {
            $found = true;

            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        foreach ($this->allMatches($up, '/Schema::table\(\s*[\'"]([a-zA-Z0-9_]+)[\'"]/') as $table) {
            $found = true;

            if (! Schema::hasTable($table)) {
                return false;
            }

            $columns = $this->addedColumns($up, $table);

            if ($columns === []) {
                return false;
            }

            $existing = array_column(Schema::getColumns($table), 'name');

            foreach ($columns as $column) {
                if (! in_array($column, $existing, true)) {
                    return false;
                }
            }
        }

        return $found;
    }

    /**
     * Extract the body of the up() method, matching braces so multi-statement
     * migrations (e.g. several Schema::create calls) are captured in full.
     */
    private function upBody(string $content): ?string
    {
        $pos = strpos($content, 'function up');

        if ($pos === false) {
            return null;
        }

        $open = strpos($content, '{', $pos);

        if ($open === false) {
            return null;
        }

        $depth = 0;
        $length = strlen($content);

        for ($i = $open; $i < $length; $i++) {
            if ($content[$i] === '{') {
                $depth++;
            } elseif ($content[$i] === '}') {
                $depth--;

                if ($depth === 0) {
                    return substr($content, $open + 1, $i - $open - 1);
                }
            }
        }

        return null;
    }

    /**
     * Column names added within a given Schema::table(...) block.
     */
    private function addedColumns(string $up, string $table): array
    {
        $start = strpos($up, "Schema::table('" . $table . "'");

        if ($start === false) {
            $start = strpos($up, 'Schema::table("' . $table . '"');
        }

        if ($start === false) {
            return [];
        }

        $end = strpos($up, ');', $start);

        if ($end === false) {
            return [];
        }

        $block = substr($up, $start, $end - $start);

        $names = $this->allMatches($block, '/->(?:' . self::COLUMN_METHODS . ')\(\s*[\'"]([a-zA-Z0-9_]+)[\'"]/');

        return array_values(array_unique($names));
    }

    /**
     * All capture-group matches for a regex, or an empty array.
     *
     * @return string[]
     */
    private function allMatches(string $subject, string $pattern): array
    {
        if (! preg_match_all($pattern, $subject, $matches)) {
            return [];
        }

        return $matches[1];
    }
}
