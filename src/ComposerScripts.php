<?php

namespace Faj1\Utils;

use Composer\Script\Event;

class ComposerScripts
{
    public static function postInstall(Event $event)
    {
        $io = $event->getIO();
        $io->write("自定义脚本：安装完成！");
    }

    public static function postUpdate(Event $event)
    {
        $io = $event->getIO();
        $io->write("自定义脚本：更新完成！");
    }
}
