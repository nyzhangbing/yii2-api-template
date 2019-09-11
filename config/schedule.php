<?php
/** * @var \omnilight\scheduling\Schedule $schedule */

//每分钟执行一次
$schedule->command('task/run ' . base64_encode(\app\commands\tasks\DemoTask::class))
    ->everyMinute();