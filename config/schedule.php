<?php
/** * @var \omnilight\scheduling\Schedule $schedule */

//每分钟执行一次，使用方式请参考 https://github.com/omnilight/yii2-scheduling
$schedule->command('task/run ' . base64_encode(\app\commands\tasks\DemoTask::class))
    ->everyMinute();