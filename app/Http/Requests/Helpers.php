<?php

namespace App\Http\Requests;

class Helpers
{
  /**
   * Preprends 'sometimes|' to all rules
   */
  public static function sometimes(array $rules)
  {
    return array_map(function(string $rule) {
      return 'sometimes|' . $rule;
    }, $rules);
  }
}
