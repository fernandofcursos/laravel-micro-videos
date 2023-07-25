<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse as TestingTestResponse;

trait TestValidations
{


  protected function assertInvalidationStoreAction(
    array $data,
    string $rule,
    $ruleParams = []
  )
  {
    $response = $this->json('POST', $this->routeStore(), $data);
    $fields = array_keys($data);
    $this->assertInvalidationFields($response,$fields,$rule,$ruleParams);
  }

  protected function assertInvalidationUpdateAction(
    array $data,
    string $rule,
    $ruleParams = []
  )
  {
    $response = $this->json('PUT', $this->routeUpdate(), $data);
    $fields = array_keys($data);
    $this->assertInvalidationFields($response,$fields,$rule,$ruleParams);
  }

  protected function assertInvalidationFields
  (
    TestingTestResponse $response,
    array $fields,
    string $rule,
    array $ruleParams = []
  ) {
      $response
      ->assertStatus(422)
      ->assertJsonValidationErrors($fields);

      foreach ($fields as $field){
        $fieldName = str_replace('_',' ', $field);
        $response->assertJsonFragment([
          Lang::trans("validation.{$rule}", ['attribute'=>$fieldName ]+$ruleParams)
        ]);
      }
 
  }
  
}
