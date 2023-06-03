<?php

namespace Seatplus\Connector\Contracts;

use Illuminate\Support\Collection;
use Seatplus\Connector\Models\Settings;
use Seatplus\Connector\Models\User;

interface Connector
{
    public static function getImg(): string;

    public static function getName(): string;

    public static function isConnectorConfigured(): bool;

    public static function getConnectorConfigUrl(): string;

    public static function getRegistrationUrl(): string;

    public static function users(): Collection;

    public static function findUser(int $user_id): ?User;

    public static function getSettings(): Settings;
}
