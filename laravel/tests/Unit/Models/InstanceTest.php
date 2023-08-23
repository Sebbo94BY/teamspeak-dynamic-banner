<?php

namespace Tests\Unit\Models;

use App\Models\Instance;
use App\Models\InstanceProcess;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class InstanceTest extends TestCase
{
    use RefreshDatabase;

    protected Instance $instance;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->instance = Instance::factory()->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->instance);
    }

    /**
     * Test that the model properly hides specific attributes in serializations.
     */
    public function test_model_properly_hides_specific_attributes_in_serializations(): void
    {
        $this->assertArrayHasKey('virtualserver_name', $this->instance->toArray());
        $this->assertArrayHasKey('serverquery_username', $this->instance->toArray());

        $this->assertArrayNotHasKey('serverquery_password', $this->instance->toArray());
    }

    /**
     * Test that the model properly casts specific attributes.
     */
    public function test_model_properly_casts_specific_attributes(): void
    {
        $plain_password = 'veryS$ecretP4ssw0rD!';

        // Excplitly encrypting the password should fail as the `$casts` would otherwise encrypt it a
        // second time and then we would save the incorrect password. Thus, this test is expected to fail.
        $this->instance->serverquery_password = Crypt::encryptString($plain_password);
        $this->instance->save();
        $this->assertNotEquals($plain_password, $this->instance->serverquery_password);

        // Laravel automatically decrypts the encrypted value from the database when it fetches it due to
        // the `$casts` configuration, so there is no need to decrypt the value.
        $this->instance->serverquery_password = $plain_password;
        $this->instance->save();
        $this->expectException(DecryptException::class);
        Crypt::decryptString($this->instance->serverquery_password);

        // Laravel should automatically store the password encrypted in the database as we have defined it
        // as `$casts`, so we can store the plaintext value in the model.
        $this->instance->serverquery_password = $plain_password;
        $this->instance->save();
        $this->assertEquals($plain_password, $this->instance->serverquery_password);

        $this->assertIsBool($this->instance->is_ssh);
        $this->assertIsBool($this->instance->autostart_enabled);
    }

    /**
     * Test that the model can have zero active processes.
     */
    public function test_model_can_have_zero_active_processes(): void
    {
        $this->assertEmpty($this->instance->process);
    }

    /**
     * Test that the model has one active process.
     */
    public function test_model_has_one_active_process(): void
    {
        InstanceProcess::factory()
            ->for($this->instance)
            ->create();

        $this->assertInstanceOf(InstanceProcess::class, $this->instance->process);
    }

    /**
     * Test that the model can not have more than one active process.
     */
    public function test_model_can_not_have_more_than_one_active_process(): void
    {
        InstanceProcess::factory(3)
            ->for($this->instance)
            ->create();

        $this->assertInstanceOf(InstanceProcess::class, $this->instance->process);
    }
}
