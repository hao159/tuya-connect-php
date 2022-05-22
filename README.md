# tuya-connect-php
Sample connect Tuya cloud

### Get started
[Tuya IoT Development Platform : Quick start](https://developer.tuya.com/en/docs/iot/quick-start1?id=K95ztz9u9t89n)


## usage

#### new obj
```php
require_once 'ConnectTuya.php';

$tuya_obj = new ConnectTuya();
```

#### get access token
```php
$response_token = $tuya_obj->get_token();
print_r($resonse_token);
```

#### Set command
```php
$commands_off = array(
    array(
        "code" => "switch_1",
        "value" => false
    )
);
# tắt ổ điện
$result_switch_off = $tuya_obj->send_commands($device_id, $commands_off, $access_token);
print_r($result_switch_off);
```


### More function at:

[Cloud Services API Reference](https://developer.tuya.com/en/docs/cloud/device-connection-service?id=Kb0b8geg6o761)
