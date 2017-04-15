# Google Maps Form Type
Input address with google maps popup

### Code Sample
```php
$this->form[] = ['label'=>'Address','name'=>'address','type'=>'googlemaps'];
```
### Input Address with Latitude & Longitude
#### Sample Table
| Field Name | Data Type |
| ---------- | --------- |
| id | int(pk) |
| created_at | timestamp |
| name | varchar(255) |
| address | varchar(255) |
| lat | varchar(255) |
| lng | varchar(255) |

#### Code Sample
```php
$this->form[] = ['label'=>'Address','name'=>'address','type'=>'googlemaps','latitude'=>'lat','longitude'=>'lng'];
```

### Attribute Available
| Attribute Name | Example | Description |
| -------------- | ------- | ----------- |
| latitude | `latitude` | latitude field |
| longitude | `longitude` | longitude field |


## What's Next
- [Form Input Type: hidden](./form-hidden.md)

## Table Of Contents
- [Back To Index](./index.md)