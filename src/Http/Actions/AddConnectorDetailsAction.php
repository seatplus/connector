<?php

namespace Seatplus\Connector\Http\Actions;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Pipeline;
use Seatplus\Connector\Contracts\Connector;

class AddConnectorDetailsAction
{
    private bool $is_admin;

    public function __construct(
        private string $admin_permission = 'superuser',
        private bool $is_disabled = false
    ) {
        $this->is_admin = auth()->user()->can($this->admin_permission);
    }

    /**
     * @throws \Throwable
     */
    public function execute(Connector $connector): array
    {

        $pipes = $this->is_admin ? $this->getAdminArray() : [];

        $status = Pipeline::send($connector)
            ->through([
                ...$pipes,
                fn (Connector $connector, Closure $next) => $this->checkIfConnectorIsDisabled($connector, $next),
                fn (Connector $connector, Closure $next) => $this->checkIfConnectorIsOpenForUsers($connector, $next),
                fn (Connector $connector, Closure $next) => $this->checkIfUserIsRegistered($connector, $next),
            ])->thenReturn();

        // return merged array with status and connector details
        return Arr::collapse([
            $status,
            [
                'name' => $connector::getName(),
                'image' => $connector::getImg(),
            ],
        ]);

    }

    private function getAdminArray(): array
    {
        return [
            fn (Connector $connector, Closure $next) => $this->checkIfConnectorIsConfigured($connector, $next),
            fn (Connector $connector, Closure $next) => $this->checkIfConnectorIsSetup($connector, $next),
        ];
    }

    private function checkIfConnectorIsConfigured(Connector $connector, Closure $next): array
    {
        if ($connector::isConnectorConfigured()) {
            return $next($connector);
        }

        return $this->getStatusArray('Missing Configuration', $connector);
    }

    private function checkIfConnectorIsSetup(Connector $connector, Closure $next): array
    {
        if ($connector::isConnectorSetup()) {
            return $next($connector);
        }

        return $this->getStatusArray('Incomplete Setup', $connector);
    }

    private function checkIfConnectorIsDisabled(Connector $connector, Closure $next): array
    {
        if ($this->is_disabled) {
            return [
                'status' => 'Disabled',
                'can_enable' => $this->is_admin,
            ];
        }

        return $next($connector);
    }

    public function setAdminPermission(string $admin_permission): AddConnectorDetailsAction
    {
        $this->admin_permission = $admin_permission;

        return $this;
    }

    public function setIsDisabled(bool $is_disabled): AddConnectorDetailsAction
    {
        $this->is_disabled = $is_disabled;

        return $this;
    }

    private function checkIfConnectorIsOpenForUsers(Connector $connector, Closure $next): array
    {
        // if connector is configured and setup and not disabled return next
        if ($connector::isConnectorConfigured() && $connector::isConnectorSetup()) {
            return $next($connector);
        }

        return $this->getStatusArray('Disabled', $connector);
    }

    private function checkIfUserIsRegistered(Connector $connector, Closure $next): array
    {
        $user = $connector::findUser(auth()->user()->getAuthIdentifier());

        return $this->getStatusArray($user ? 'Registered' : 'Not Registered', $connector);
    }

    private function getStatusArray(string $status, Connector $connector): array
    {
        return match ($status) {
            'Missing Configuration' => [
                'status' => 'Missing Configuration',
                'url' => $connector::getConnectorConfigUrl(),
            ],
            'Incomplete Setup' => [
                'status' => 'Incomplete Setup',
                'url' => $connector::getRegistrationUrl(),
            ],
            'Not Registered' => [
                'status' => 'Not Registered',
                'url' => $connector::getRegistrationUrl(),
            ],
            'Registered' => [
                'status' => 'Registered',
                'can_enable' => $this->is_admin, // This is required for the frontend to show the disable button
            ],
            default => [
                'status' => 'Disabled',
                'can_enable' => $this->is_admin, // This is required for the frontend to show the enable button
            ],
        };
    }
}
