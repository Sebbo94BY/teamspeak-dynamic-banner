<?php

namespace Tests\Unit\Models;

use App\Models\TwitchApi;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class TwitchApiTest extends TestCase
{
    use RefreshDatabase;

    protected TwitchApi $twitch_api;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->twitch_api = TwitchApi::factory()->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->twitch_api);
    }

    /**
     * Test that the model properly hides specific attributes in serializations.
     */
    public function test_model_properly_hides_specific_attributes_in_serializations(): void
    {
        $this->assertArrayHasKey('client_id', $this->twitch_api->toArray());

        $this->assertArrayNotHasKey('client_secret', $this->twitch_api->toArray());
        $this->assertArrayNotHasKey('access_token', $this->twitch_api->toArray());
    }

    /**
     * Test that the model properly casts specific attributes.
     */
    public function test_model_properly_casts_specific_attributes(): void
    {
        $plain_secret = 'veryS$ecretStr1nG!';

        // Excplitly encrypting the secret should fail as the `$casts` would otherwise encrypt it a
        // second time and then we would save the incorrect secret. Thus, this test is expected to fail.
        $this->twitch_api->client_secret = Crypt::encryptString($plain_secret);
        $this->twitch_api->save();
        $this->assertNotEquals($plain_secret, $this->twitch_api->client_secret);

        // Laravel automatically decrypts the encrypted value from the database when it fetches it due to
        // the `$casts` configuration, so there is no need to decrypt the value.
        $this->twitch_api->client_secret = $plain_secret;
        $this->twitch_api->save();
        $this->expectException(DecryptException::class);
        Crypt::decryptString($this->twitch_api->client_secret);

        // Laravel should automatically store the secret encrypted in the database as we have defined it
        // as `$casts`, so we can store the plaintext value in the model.
        $this->twitch_api->client_secret = $plain_secret;
        $this->twitch_api->save();
        $this->assertEquals($plain_secret, $this->twitch_api->client_secret);

        $plain_token = 'veryS$ecretT0k3N!';

        // Excplitly encrypting the token should fail as the `$casts` would otherwise encrypt it a
        // second time and then we would save the incorrect token. Thus, this test is expected to fail.
        $this->twitch_api->access_token = Crypt::encryptString($plain_token);
        $this->twitch_api->save();
        $this->assertNotEquals($plain_token, $this->twitch_api->access_token);

        // Laravel automatically decrypts the encrypted value from the database when it fetches it due to
        // the `$casts` configuration, so there is no need to decrypt the value.
        $this->twitch_api->access_token = $plain_token;
        $this->twitch_api->save();
        $this->expectException(DecryptException::class);
        Crypt::decryptString($this->twitch_api->access_token);

        // Laravel should automatically store the token encrypted in the database as we have defined it
        // as `$casts`, so we can store the plaintext value in the model.
        $this->twitch_api->access_token = $plain_token;
        $this->twitch_api->save();
        $this->assertEquals($plain_token, $this->twitch_api->access_token);
    }
}
