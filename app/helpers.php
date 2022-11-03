<?php

if (! function_exists('now')) {
    /**
     * Get current time.
     *
     * @return DateTime
     * @throws Exception
     */
    function now(): DateTime
    {
        return (new DateTime(date('Y-m-d H:i:s')))
            ->setTimezone(new DateTimeZone('Europe/Moscow'));
    }
}
