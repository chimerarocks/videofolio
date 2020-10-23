<?php


namespace Tests\Traits;


use Illuminate\Foundation\Testing\TestResponse;

trait TestSave
{
    protected function assertStore(array $sendData, array $testDatabase, array $testJson = null): TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()}:\n {$response->content()}");
        }
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json('id')]);
        $testResponse = $testJson ?? $testDatabase;
        $response->assertJsonFragment($testResponse + ['id' => $response->json('id')]);
    }
}
