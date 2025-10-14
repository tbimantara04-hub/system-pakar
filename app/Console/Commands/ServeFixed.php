<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServeFixed extends Command
{
    protected $name = 'serve';
    protected $description = 'Custom serve command to replace default Laravel serve command';

    public function handle()
    {
        $port = (int) env('PORT', 8000);
        $host = '0.0.0.0';

        $this->info("✅ Starting fixed PHP server on http://{$host}:{$port}");

        $process = new Process(['php', '-S', "{$host}:{$port}", '-t', 'public']);
        $process->setTty(Process::isTtySupported());
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        return 0;
    }
}
