<?php

namespace App\Http\Covid2019;

use Illuminate\Support\MessageBag;
use Illuminate\Routing\UrlGenerator;

/**
 * Description of Utils
 *
 * @author IEUser
 */
class Utils {

    /**
     * oopsCreateMsgBagAndAddError creates messageBag and add an error
     * @param \Exception $result
     * @param string $msg optional
     * @return MessageBag
     */
    public static function oopsCreateMsgBagAndAddError(\Exception $result, $msg = '') {
        $bag = new MessageBag();
        $bag->add('justkey', $msg . $result->getMessage());
        return $bag;
    }


    /*
     * generateSelectOptions from assoc array
  0 => {#1263 ▼
    +"id": 5
    +"name": "test"
    +"description": ""
  }
  1 => {#1261 ▼
    +"id": 9
    +"name": "hjhk"
    +"description": ""
  }
]
     */
    public static function generateSelectOptions($arrSomeInput, $value = "id", $label = "name") {
        $result = "<option> -- select an option -- </option>";
        if (!is_array($arrSomeInput)) {
            $result = "<option> -- create a campaign first. Go to Call Records -> Campaigns and create one -- </option>";
            return $result;
        }
        foreach ($arrSomeInput as $record) {
            $result .= "<option value='" . $record->$value . "'>" . $record->$label . "</option>";
        }
//        $result .= "<option value='-1'>Abandoned Records</option>";
        return $result;
    }
}
