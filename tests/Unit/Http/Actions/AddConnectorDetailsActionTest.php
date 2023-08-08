<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\User;
use Seatplus\Connector\Contracts\Connector;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = Event::fakeFor(fn() => User::factory()->create());
});

describe('admin', function () {
   beforeEach(function () {

       $permission = faker()->word;

       assignPermissionToUser($this->user, ['superuser', $permission]);
       $this->actingAs($this->user);

       $this->action = (new \Seatplus\Connector\Http\Actions\AddConnectorDetailsAction())
           ->setAdminPermission($permission);

   });

   it('get Missing Configuration status if connector is not configured', function () {
       $connector_mock = mockConnector(function ($mock) {
           $mock->shouldReceive('isConnectorConfigured')
               ->once()
               ->andReturn(false);

           $mock->shouldReceive('getConnectorConfigUrl')
               ->once()
               ->andReturn('url');
       });

       $result = $this->action->execute($connector_mock);

       expect($result)->toHaveKey('status')
           ->and($result['status'])->toBe('Missing Configuration');
   });

   it('get Incomplete Setup status if connector is not setup', function () {
       $connector_mock = mockConnector(function ($mock) {
           $mock->shouldReceive('isConnectorConfigured')
               ->once()
               ->andReturn(true);

           $mock->shouldReceive('isConnectorSetup')
               ->once()
               ->andReturn(false);

           $mock->shouldReceive('getRegistrationUrl')
               ->once()
               ->andReturn('url');
       });


       $result = $this->action->execute($connector_mock);

       expect($result)->toHaveKey('status');
       expect($result['status'])->toBe('Incomplete Setup');
   });

   test('get Disabled status if connector is disabled by method', function () {
       $connector_mock = mockConnector(function ($mock) {
           $mock->shouldReceive('isConnectorConfigured')
               ->once()
               ->andReturn(true);

           $mock->shouldReceive('isConnectorSetup')
               ->once()
               ->andReturn(true);
       });

       $result = $this->action->setIsDisabled(true)->execute($connector_mock);

       expect($result)->toHaveKey('status');
       expect($result['status'])->toBe('Disabled');
   });
});

describe('regular user', function () {
    beforeEach(function () {
        $this->actingAs($this->user);

        $this->action = new \Seatplus\Connector\Http\Actions\AddConnectorDetailsAction();
    });

    it('get Disabled status if connector is disabled by method', function () {

        $connector_mock = mockConnector(fn() => null);

        $result = $this->action->setIsDisabled(true)->execute($connector_mock);

        expect($result)->toHaveKey('status');
        expect($result['status'])->toBe('Disabled');
    });

    it('get Disabled status if connector is not configured', function () {
        $connector_mock = mockConnector(function ($mock) {
            $mock->shouldReceive('isConnectorConfigured')
                ->once()
                ->andReturn(false);
        });

        $result = $this->action->execute($connector_mock);

        expect($result)->toHaveKey('status');
        expect($result['status'])->toBe('Disabled');
    });

    it('get Disabled status if connector is not setup', function () {
        $connector_mock = mockConnector(function ($mock) {
            $mock->shouldReceive('isConnectorConfigured')
                ->once()
                ->andReturn(true);

            $mock->shouldReceive('isConnectorSetup')
                ->once()
                ->andReturn(false);
        });

        $result = $this->action->execute($connector_mock);

        expect($result)->toHaveKey('status');
        expect($result['status'])->toBe('Disabled');
    });

    it('get registration status if user is', function (bool $registered) {

        $user = \Seatplus\Connector\Models\User::create([
            'user_id' => $this->user->getAuthIdentifier(),
            'connector_id' => faker()->numberBetween(1, 100),
            'connector_type' => faker()->name,
        ]);

        $connector_mock = mockConnector(function ($mock) use ($user, $registered) {
            $mock->shouldReceive('isConnectorConfigured')
                ->once()
                ->andReturn(true);

            $mock->shouldReceive('isConnectorSetup')
                ->once()
                ->andReturn(true);

            $mock->shouldReceive('findUser')
                ->once()
                ->andReturn($registered ? $user : null);

            // if user is not registered we need to mock the getRegistrationUrl method
            if(!$registered) {
                $mock->shouldReceive('getRegistrationUrl')
                    ->once()
                    ->andReturn('url');
            }
        });

        $result = $this->action->execute($connector_mock);

        expect($result)->toHaveKey('status');
        expect($result['status'])->toBe($registered ? 'Registered' : 'Not Registered');
    })->with([
        'registered' => true,
        'not registered' => false
    ]);
});

function mockConnector(Closure $closure) {
    return mock(Connector::class, function ($mock) use ($closure) {
        $mock->shouldReceive('getName')
            ->once()
            ->andReturn('name');

        $mock->shouldReceive('getImg')
            ->once()
            ->andReturn('img');

        $closure($mock);
    });
}
