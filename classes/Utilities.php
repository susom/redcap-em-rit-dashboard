<?php

namespace Stanford\ProjectPortal;

class Utilities
{

    const PROD = 1;
    const HAS_FEES = 2;
    const LINKED = 4;
    const HAS_RMA = 8;
    const APPROVED_RMA = 16;

    /**
     * @param bool $isLinked
     * @param bool $status
     * @param int $fees
     * @return string
     */
    public static function determineProjectAlert($state): string
    {
        $alert = 'warning';
        if ($state & self::LINKED && $state & self::HAS_RMA && $state & self::APPROVED_RMA) {
            return 'success';
        }
        if ($state & self::PROD && $state & self::HAS_FEES) {
            return 'danger';
        }
        return $alert;
    }

    public static function determineProjectIcon($state): string
    {
        $icon = 'fas fa-exclamation-circle';
        if ($state & self::LINKED && $state & self::HAS_RMA && $state & self::APPROVED_RMA) {
            return 'fas fa-check';
        }
        if ($state & self::PROD && $state & self::HAS_FEES) {
            return 'fas fa-times';
        }
        return $icon;
    }

    /**
     * bitwise operation to determine redcap project state
     * @param bool $status either prod or not (dev, completed, archived)
     * @param int $fees
     * @param bool $isLinked
     * @param bool $hasRMA
     * @param bool $approvedRMA
     * @return int
     */
    public static function determineProjectState($status, $fees, $isLinked, $hasRMA = false, $approvedRMA = false)
    {
        $state = 0;
        if ($status) {
            $state ^= self::PROD;
        }
        if ($fees > 0) {
            $state ^= self::HAS_FEES;
        }
        if ($isLinked) {
            $state ^= self::LINKED;
        }
        if ($hasRMA) {
            $state ^= self::HAS_RMA;
        }
        if ($approvedRMA) {
            $state ^= self::APPROVED_RMA;
        }
        return $state;
    }

    /**
     * @param string $notification
     * @param array $variables
     * @return string
     */
    public static function replaceNotificationsVariables(string $notification, array $variables)
    {
        foreach ($variables as $key => $value) {
            $notification = str_replace("[" . $key . "]", $value, $notification);
        }

        return $notification;
    }
}