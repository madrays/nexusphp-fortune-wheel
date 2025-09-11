<?php

namespace NexusPlugin\FortuneWheel;

class FortuneWheel
{
    /**
     * 插件ID
     */
    const ID = 'fortune-wheel';

    /**
     * 创建插件实例
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * 获取插件ID
     */
    public function getId(): string
    {
        return self::ID;
    }
}
